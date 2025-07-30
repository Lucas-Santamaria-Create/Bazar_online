<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/Reserva.php';

$action = $_GET['action'] ?? 'listar';
$reservaModel = new Reserva();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

function redirectWithMessage($location, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

switch ($action) {
    case 'listar':
        // Listar reservas para el vendedor
        $reservas = $reservaModel->obtenerPorVendedor($id_usuario);
        include '../views/reservas_vendedor.php';
        break;

    case 'actualizar_estado':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_reserva = intval($_POST['id_reserva'] ?? 0);
            $nuevo_estado = trim($_POST['estado'] ?? '');

            if (in_array($nuevo_estado, ['pendiente', 'confirmada', 'rechazada', 'entregada'])) {
                $reservaModel->actualizar($id_reserva, $nuevo_estado);
                redirectWithMessage('../controllers/ReservaController.php?action=listar', 'success', 'Estado de la reserva actualizado.');
            } else {
                redirectWithMessage('../controllers/ReservaController.php?action=listar', 'error', 'Estado inválido.');
            }
        } else {
            header('Location: ../controllers/ReservaController.php?action=listar');
            exit();
        }
        break;

    default:
        header('Location: ../views/perfil.php');
        exit();
}
?>
