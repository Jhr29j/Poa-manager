<?php
// config.php
define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');
define('VIEWS_PATH', __DIR__.'/../views/');
define('ASSETS_PATH', BASE_URL.'assets/');

// Configuración de sesión segura
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();
?>