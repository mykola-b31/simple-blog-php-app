<?php
$host = "localhost";
$db = "my_site_db";
$user = "admin";
$password = "admin";

// Підключення до сервера
$link = mysqli_connect($host, $user, $password, $db);

if (!$link) {
    die("Помилка з'єднання: " . mysqli_connect_error());
}

// Вибір бд
$db = "my_site_db";
$select = mysqli_select_db($link, $db);
if (!$select) {
    die("Помилка вибору бази: " . mysqli_error($link));
}

// Налаштування кодування
mysqli_set_charset($link, "utf8");