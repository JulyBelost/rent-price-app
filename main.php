
<html>
<head> 
<meta charset="utf-8">
</head>
<body>

<form action="main.php" method="post">
Координаты: <input type="text" name="coords"><br>
Адрес: <input type="text" name="address"><br>
Этаж: <input type="text" name="floor"><br>
Количество этажей: <input type="floor_total" name="email"><br>
Ремонт: <input type="text" name="renovation_degree"><br>
Время до метро: <input type="text" name="time_to_tube"><br>
Площадь: <input type="text" name="square"><br>

<input type="submit">
</form>

according to the flat address <?php echo $_POST["address"]; ?><br>
your rent cost: <?php echo "10000000" . $_POST["floor"] . "$"; ?>


</body>
</html>
