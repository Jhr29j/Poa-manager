<?php
$host = "tramway.proxy.rlwy.net"; // MYSQHOST
$port = "17185";                      // MYSQLPORT
$db = "railway";                   // MYSQLDATABASE
$user = "root";                    // MYSQLUSER
$pass = "rFLLNjMncPcBDuxjvqtMwQGwmFKauECp"; // MYSQLPASSWORD

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
