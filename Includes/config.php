<?php
// Configurar el timezone
date_default_timezone_set('America/Bogota');

// Definir constantes de rutas
define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');
define('ROOT_PATH', realpath(dirname(__FILE__).'/../');

// Configuración de sesión segura
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
?>