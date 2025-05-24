<?php
// config.php - Versión corregida

// Configuración BASE_URL
define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');

// Configuración de sesión segura CORREGIDA
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Iniciar sesión
session_start();
?>