<?php
session_start();
require_once '../models/Calificacion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$calificacionModel = new Calificacion();

$accion = $_GET['action'] ?? '';

if ($accion === 'crear') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = $_POST['id_producto'] ?? null;
        $estrellas = $_POST['estrellas'] ?? null;
        $comentario = $_POST['comentario'] ?? null;

        if (!$id_producto || !$estrellas) {
            header("Location: ../views/catalogo.php?error=Faltan+datos+para+calificar");
            exit();
        }

        $exito = $calificacionModel->crear($id_producto, $id_usuario, $estrellas, $comentario);
        if ($exito) {
            // ✅ Redirige al catálogo con mensaje (para evitar warning por falta de $producto)
            header("Location: ../views/catalogo.php?mensaje=Calificación+agregada+correctamente");
            exit();
        } else {
            header("Location: ../views/catalogo.php?error=No+se+pudo+agregar+la+calificación");
            exit();
        }
    } else {
        header("Location: ../views/catalogo.php?error=Método+no+permitido");
        exit();
    }

} elseif ($accion === 'eliminar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_calificacion = $_POST['id_calificacion'] ?? null;
        $id_producto = $_POST['id_producto'] ?? null;

        if (!$id_calificacion || !$id_producto) {
            header("Location: ../views/catalogo.php?error=Datos+incompletos+para+eliminar");
            exit();
        }

        $exito = $calificacionModel->eliminar($id_calificacion);
        if ($exito) {
            header("Location: ../views/catalogo.php?mensaje=Calificación+eliminada+correctamente");
            exit();
        } else {
            header("Location: ../views/catalogo.php?error=No+se+pudo+eliminar+la+calificación");
            exit();
        }
    } else {
        header("Location: ../views/catalogo.php?error=Método+no+permitido");
        exit();
    }

} else {
    // Acción no válida
    header('Location: ../views/catalogo.php?error=Acción+inválida');
    exit();
}
