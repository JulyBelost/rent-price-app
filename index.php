<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/app.php';

$app['debug'] = true;
$app->run();
?>
