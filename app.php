<?php
require __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = [ __DIR__ ];

$app->get('/history', function () use ($app) {
    /** @var PDO $db */
    $db = $app['database'];
    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    $results = $db->query('SELECT * from requests order by id desc limit 20');

    return $twig->render('/history.html', [
        'results' => $results,
    ]);
});

$app->get('/calc', function () use ($app) {
    $twig = $app['twig'];

    return $twig->render('/calc.html', [
        'results' => NULL,
    ]);
});

$app->post('/calc', function (Request $request) use ($app) {
    Request::setTrustedProxies(array($request->server->get('REMOTE_ADDR')));

    /** @var PDO $db */
    $db = $app['database'];
    $twig = $app['twig'];

    $coords_str       = $request->request->get('coords');
    $address      = $request->request->get('address');
    $floor        = $request->request->get('floor');
    $floor_total  = $request->request->get('floor_total');
    $renovation   = $request->request->get('renovation');
    $time_to_tube = $request->request->get('time_to_tube');
    $square       = $request->request->get('square');
    $user_agent   = $request->headers->get('User-Agent');
    $ip           = $request->getClientIp();
    $phptime      = time();
    $time         = date("Y-m-d H:i:s", $phptime);

    if ($address) {
        $stmt = $db->prepare('INSERT INTO requests (time, IP, User_Agent, coords,
                address, floor, floor_total, renovation, time_to_tube, square)
            VALUES (:time, :ip, :user_agent, :coords, :address, :floor,
                :floor_total, :renovation, :time_to_tube, :square)');
        $logm = $stmt->execute([
            ':time' => $time,
            ':ip' => $ip,
            ':user_agent' => $user_agent,
            ':coords' => $coords_str,
            ':address' => $address,
            ':floor' => $floor,
            ':floor_total' => $floor_total,
            ':renovation' => $renovation,
            ':time_to_tube' => $time_to_tube,
            ':square' => $square,
        ]);
        syslog(LOG_INFO , $logm);
    }
    // коэффициенты для рассчета величины арендной платы
    $base_cost_unit = 350;
    
    // Moscow center coords 55°45'07.3"N 37°37'27.0"E
    $lat_c = 55.752028;
    $long_c = 37.624167;
    $coords = explode(", ", $coords_str);
    $distance_to_center = getDistance($lat_c, $long_c, $coords[0], $coords[1]);
    $to_bulv = 1.5;
    $to_sad = 2.5;
    $to_ttk = 5;
    $to_mcad = 17;

    if($distance_to_center <= $to_bulv){
        $district_factor = 1.5;
    }else if($distance_to_center <= $to_ttk){
        $district_factor = $distance_to_center <=  $to_sad? 1.2 : 1;
    } else {
        $district_factor = $distance_to_center <= $to_mcad ? 0.9 : 0.7;
    }

     = 1;
    $floor_factor = $floor == $floor_total || $floor == 1 ? 0.8 : 1;
    $ren_factor_arr = [
        'без ремонта' => 0.6,
        'косметический' => 1,
        'евро'=> 1.1,
        'дизайнерский'=> 1.3,
    ];
    if($time_to_tube <= 10){
        $tube_factor = $time_to_tube <= 5 ? 1.5 : 1.2;
    } else {
        $tube_factor = $time_to_tube <= 15 ? 1 : 0.8;
    }

    $cost = $base_cost_unit * $square * ($district_factor * $tube_factor +
         $floor_factor * $ren_factor_arr[$renovation]);

    return $twig->render('/calc.html', [
        'results' => $cost,
    ]);
});

// function to return the PDO instance
$app['database'] = function () use ($app) {
    // Connect to CloudSQL from App Engine.
    $dsn = getenv('MYSQL_DSN');
    $user = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    if (!isset($dsn, $user) || false === $password) {
        throw new Exception('Set MYSQL_DSN, MYSQL_USER, and MYSQL_PASSWORD environment variables');
    }

    $db = new PDO($dsn, $user, $password);

    return $db;
};

function getDistance($lat1, $long1, $lat2, $long2) {
    if($lat1 == $lat2 && $long1 == $long2){
        return 0;
    }
    $to_rad = M_PI / 180;
    $dist = acos(sin($lat1 * $rad)
        * sin($lat2 * $rad) +  cos($lat1 * $rad)
        * cos($lat2 * $rad) * cos(($long1 - $long2) * $rad));
    $dist_km = $dist / $rad * 60 *  1.853;
    return $dist_km;
}

return $app;
?>
