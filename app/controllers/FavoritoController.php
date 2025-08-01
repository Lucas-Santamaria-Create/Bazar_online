<?php
session_start();
require_once '../models/Favorito.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$favoritoModel = new Favorito();

$accion = $_GET['action'] ?? '';

if ($accion === 'agregar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = $_POST['id_producto'] ?? null;
        if (!$id_producto) {
            die("ID de producto no especificado para favorito.");
        }
        $exito = $favoritoModel->agregar($id_usuario, $id_producto);
        if ($exito) {
            header('Location: ../views/favoritos.php?mensaje=Favorito agregado correctamente');
            exit();
        } else {
            die('Error al agregar favorito.');
        }
    } else {
        die("Método no permitido.");
    }
} elseif ($accion === 'eliminar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_favorito = $_POST['id_favorito'] ?? null;
        if (!$id_favorito) {
            die("ID de favorito no especificado para eliminar.");
        }
        $exito = $favoritoModel->eliminar($id_favorito, $id_usuario);
        if ($exito) {
            header('Location: ../views/favoritos.php?mensaje=Favorito eliminado correctamente');
            exit();
        } else {
            die('Error al eliminar favorito.');
        }
    } else {
        die("Método no permitido.");
    }
} else {
    // Puedes implementar aquí listar, o redirigir por defecto
    header('Location: ../views/favoritos.php');
    exit();
}
