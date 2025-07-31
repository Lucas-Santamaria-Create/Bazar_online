<?php
// creo la cokies para el ultimo inicio de sesion y expira en 30 dias
date_default_timezone_set('America/Panama');
setcookie('last_login', date('Y-m-d h:i A'), time() + (30 * 24 * 60 * 60), "/");

session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit();
