<?php
$link = mysqli_connect("MySQL-8.0", "root", "", "mysite") 
    or die("Ошибка подключения к базе данных: " . mysqli_connect_error());

mysqli_set_charset($link, "utf8mb4");
?>