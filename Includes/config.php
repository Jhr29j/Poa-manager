<?php
// config.php - Versión optimizada

// Configuración BASE_URL
define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');
date_default_timezone_set('America/Santo_Domingo');

// Configuración de sesión segura ANTES de iniciar sesión
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>