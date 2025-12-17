<?php
$host = "localhost";
$db   = "helpdesk_pro";
$user = "root";
$pass = ""; // XAMPP normalmente não tem password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados");
}
