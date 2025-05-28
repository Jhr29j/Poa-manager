<<<<<<< HEAD
<?php

define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');
date_default_timezone_set('America/Santo_Domingo');

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
=======
<?php

define('BASE_URL', 'https://'.$_SERVER['HTTP_HOST'].'/');
date_default_timezone_set('America/Santo_Domingo');

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
>>>>>>> 1bc4df184ba8a2a882bc880481269965a3efa759
?>