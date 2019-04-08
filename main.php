
<html>
<head>
<meta charset="utf-8">
</head>
<body>

<form action="" method="post">
<label for="1">Координаты:</label>
<input id="1" type="text" name="coords" placeholder="координаты дома"><br>
<label for="2">Адрес:</label>
<input id="2" type="text" name="address" required><br>
<label for="3">Этаж:</label>
<input id="3" type="number" name="floor" step="1" min = "1" value="1"><br>
<label for="4">Количество этажей:</label>
<input id="4" type="number" name="floor_total" step="1" value="9"><br>
<label for="5">Ремонт:</label>
<input id="5" list="renovation_type" name="renovation"><br>
<datalist id="renovation_type">
    <option value="без ремонта">
    <option value="косметический">
    <option value="евро">
    <option value="дизайнервский">
</datalist>
<label for="6">Время до метро:</label>
<input id="6" type="number" name="time_to_tube" min ="0"> минут<br>
<label for="7">Площадь:</label>
<input id="7" type="number" name="square" value="37" required> кв. м. <br>

<input type="submit" name="submit" value="Calculate rent cost">
</form>

<form action="" method="get">
<input type="submit" name="submit" value="Get history">
</form>

</html>

<?php
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

// create the Silex application
$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = [ __DIR__ ];

$app->get('/', function () use ($app) {
    /** @var PDO $db */
    $db = $app['database'];
    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    // Show existing guestbook entries.
    $results = $db->query('SELECT * from requests limit 20');

    return $twig->render('cloudsql.html.twig', [
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
    echo '<h2>Form POST Method</h2>';
    echo 'Your rent cost is ' . $floor_total/$floor;
    return $app->redirect('/');
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
}; ?>
