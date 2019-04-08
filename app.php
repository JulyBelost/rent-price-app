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

    $results = $db->query('SELECT * from requests limit 20');

    return $twig->render('/history.php', [
        'results' => $results,
    ]);
});

$app->get('/calc', function () use ($app) {
    $twig = $app['twig'];

    return $twig->render('/calc.php');
});

$app->post('/calc', function (Request $request) use ($app) {
    /** @var PDO $db */
    $db = $app['database'];
    $twig = $app['twig'];

    $coords       = $request->request->get('coords');
    $address      = $request->request->get('address');
    $floor        = $request->request->get('floor');
    $floor_total  = $request->request->get('floor_total');
    $renovation   = $request->request->get('renovation');
    $time_to_tube = $request->request->get('time_to_tube');
    $square       = $request->request->get('square');
    $user_agent   = $request->headers->get('User-Agent');
    $ip           = $request->getClientIp();
    $time         = time();

    if ($address) {
        $stmt = $db->prepare('INSERT INTO requests (time, IP, User_Agent, coords,
                address, floor, floor_total, renovation, time_to_tube, square)
            VALUES (:time, :ip, :user_agent, :coords, :address, :floor,
                :floor_total, :renovation, :time_to_tube, :square)');
        $stmt->execute([
            ':time' => $time,
            ':ip' => $ip,
            ':user_agent' => $user_agent,
            ':coords' => $coords,
            ':address' => $address,
            ':floor' => $floor,
            ':floor_total' => $floor_total,
            ':renovation' => $renovation,
            ':time_to_tube' => $time_to_tube,
            ':square' => $square,
        ]);
    }

    $cost = $floor * 1000;

    return $twig->render('/calc.php', [
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

return $app;
?>
