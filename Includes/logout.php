<?php
require_once 'config.php';

session_start();
session_unset();
session_destroy();

header("Location: " . BASE_URL . "views/login.php");
exit;