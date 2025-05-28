<<<<<<< HEAD
<?php

$host = getenv('MYSQLHOST') ?: "tramway.proxy.rlwy.net";
$port = getenv('MYSQLPORT') ?: "17185";
$db   = getenv('MYSQLDATABASE') ?: "railway";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "rFLLNjMncPcBDuxjvqtMwQGwmFKauECp";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET time_zone = 'America/Santo_Domingo'");

} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error en el sistema. Por favor, inténtelo más tarde.");
}
=======
<?php

$host = getenv('MYSQLHOST') ?: "tramway.proxy.rlwy.net";
$port = getenv('MYSQLPORT') ?: "17185";
$db   = getenv('MYSQLDATABASE') ?: "railway";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "rFLLNjMncPcBDuxjvqtMwQGwmFKauECp";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET time_zone = 'America/Santo_Domingo'");

} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error en el sistema. Por favor, inténtelo más tarde.");
}
>>>>>>> 1bc4df184ba8a2a882bc880481269965a3efa759
?>