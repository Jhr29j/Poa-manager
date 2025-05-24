<?php
// php/db.php

$host = "mysql.railway.internal";
$port = "3306";
$db = "poa_db";
$user = "root";
$pass = "TDyZFJNonVTPAFPJuYncFJUHqbwqfVgR"; 


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}