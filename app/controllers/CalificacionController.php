<?php
session_start();
require_once '../models/Calificacion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

function redirectWithMessage($location, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$calificacionModel = new Calificacion();

$accion = $_GET['action'] ?? '';

if ($accion === 'crear') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = filter_var($_POST['id_producto'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $estrellas = filter_var($_POST['estrellas'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $comentario = htmlspecialchars(trim($_POST['comentario'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (!$id_producto || !$estrellas) {
            redirectWithMessage('../views/catalogo.php', 'error', 'Faltan datos para calificar');
        }

        $exito = $calificacionModel->crear($id_producto, $id_usuario, $estrellas, $comentario);
        if ($exito) {
            redirectWithMessage('../controllers/ProductoController.php?action=detalle&id=' . $id_producto, 'success', 'Calificación agregada correctamente');
        } else {
            redirectWithMessage('../views/catalogo.php', 'error', 'No se pudo agregar la calificación');
        }
    } else {
        redirectWithMessage('../views/catalogo.php', 'error', 'Método no permitido');
    }

} elseif ($accion === 'eliminar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_calificacion = filter_var($_POST['id_calificacion'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $id_producto = filter_var($_POST['id_producto'] ?? null, FILTER_SANITIZE_NUMBER_INT);

        if (!$id_calificacion || !$id_producto) {
            redirectWithMessage('../views/catalogo.php', 'error', 'Datos incompletos para eliminar');
        }

        $exito = $calificacionModel->eliminar($id_calificacion);
        if ($exito) {
            redirectWithMessage('../controllers/ProductoController.php?action=detalle&id=' . $id_producto, 'success', 'Calificación eliminada correctamente');
        } else {
            redirectWithMessage('../views/catalogo.php', 'error', 'No se pudo eliminar la calificación');
        }
    } else {
        redirectWithMessage('../views/catalogo.php', 'error', 'Método no permitido');
    }

} else {
    redirectWithMessage('../views/catalogo.php', 'error', 'Acción inválida');
}
