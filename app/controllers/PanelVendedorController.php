<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/Producto.php';
require_once '../models/Reserva.php';

$action = $_GET['action'] ?? 'productos';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

switch ($action) {
    case 'productos':
        $productoModel = new Producto();
        $productos = $productoModel->obtenerPorUsuario($id_usuario);
        include '../views/panel_vendedor.php';
        break;

    case 'reservas':
        $reservaModel = new Reserva();
        $reservas = $reservaModel->obtenerPorVendedor($id_usuario);
        include '../views/reservas_vendedor.php';
        break;

    default:
        header('Location: ../views/perfil.php');
        exit();
}
?>
