<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'reservar':
        if (!isset($_SESSION['usuario'])) {
            // No está logueado, redirigir al login con mensaje
            $_SESSION['error'] = 'Debes iniciar sesión para reservar.';
            header('Location: ../views/login.php');
            exit();
        }
        
        // Aquí el código para reservar
        $id_producto = intval($_GET['id'] ?? 0);
        // Validar id_producto, hacer reserva...
        
        break;

    // otras acciones ...
}

?>
