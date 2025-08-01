<?php

session_start();

if (isset($_SESSION['usuario'])) {
    date_default_timezone_set('America/Panama');

    $user_id = $_SESSION['usuario']['id_usuario'];
    date_default_timezone_set('America/Panama');
    $last_login_data = json_encode([
        'user_id' => $user_id,
        'last_login' => date('Y-m-d h:i A')
    ]);
    setcookie('last_login', $last_login_data, time() + (30 * 24 * 60 * 60), "/");
}

session_unset();
session_destroy();
header('Location: index.php');
exit();
