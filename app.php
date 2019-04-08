<?php
require __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = [ __DIR__ ];

$app->get('/', function () use ($app) {
    /** @var PDO $db */
    $db = $app['database'];
    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    $results = $db->query('SELECT * from requests limit 20');

    return $twig->render('/history.php', [
        'results' => $results,
    ]);
});

$app->post('/', function (Request $request) use ($app) {
    /** @var PDO $db */
    $db = $app['database'];

    $coords       = $request->request->get('coords');
    $address      = $request->request->get('address');
    $floor        = $request->request->get('floor');
    $floor_total  = $request->request->get('floor_total');
    $renovation   = $request->request->get('renovation');
    $time_to_tube = $request->request->get('time_to_tube');
    $square       = $request->request->get('square');
    $user_agent   = $request->headers->get('User-Agent');
    $ip         === $request->getClientIp();

    if ($address) {
        $stmt = $db->prepare('INSERT INTO requests (coords, address, floor,
            floor_total, renovation, time_to_tube, square) VALUES (:coords,
            :address, :floor, :floor_total, :renovation, :time_to_tube, :square)');
        $stmt->execute([
            ':coords' => $coords,
            ':address' => $address,
            ':floor' => $floor,
            ':floor_total' => $floor_total,
            ':renovation' => $renovation,
            ':time_to_tube' => $time_to_tube,
            ':square' => $square
        ]);
    }

    return $app->redirect('/calc.php');
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
