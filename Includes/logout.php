<?php
require_once 'config.php';

session_unset();
session_destroy();

header("Location: ".BASE_URL."views/login.php");
exit;
?>