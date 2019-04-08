<html>
<head>
<meta charset="utf-8">
</head>
<body>

<form action="/" method="post">
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
    <option value="дизайнерский">
</datalist>
<label for="6">Время до метро:</label>
<input id="6" type="number" name="time_to_tube" min ="0"> минут<br>
<label for="7">Площадь:</label>
<input id="7" type="number" name="square" value="37" required> кв. м. <br>

<input type="submit" name="submit" value="Calculate rent cost">
</form>

<form action="/" method="get">
<input type="submit" name="submit" value="Get history">
</form>

</html>

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/app.php';

$app['debug'] = true;
$app->run();
?>
