<?php
session_start();
require_once '../models/Favorito.php';

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
$favoritoModel = new Favorito();

$accion = $_GET['action'] ?? '';

if ($accion === 'agregar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_producto = $_POST['id_producto'] ?? null;
        if (!$id_producto) {
            redirectWithMessage('../views/favoritos.php', 'error', 'ID de producto no especificado para favorito.');
        }
        if ($favoritoModel->exists($id_usuario, $id_producto)) {
            redirectWithMessage('../views/favoritos.php', 'error', 'El producto ya está en favoritos.');
        }
        $exito = $favoritoModel->agregar($id_usuario, $id_producto);
        if ($exito) {
            redirectWithMessage('../views/favoritos.php', 'success', 'Favorito agregado correctamente');
        } else {
            redirectWithMessage('../views/favoritos.php', 'error', 'Error al agregar favorito.');
        }
    } else {
        redirectWithMessage('../views/favoritos.php', 'error', 'Método no permitido.');
    }
} elseif ($accion === 'eliminar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_favorito = $_POST['id_favorito'] ?? null;
        if (!$id_favorito) {
            redirectWithMessage('../views/favoritos.php', 'error', 'ID de favorito no especificado para eliminar.');
        }
        $exito = $favoritoModel->eliminar($id_favorito, $id_usuario);
        if ($exito) {
            redirectWithMessage('../views/favoritos.php', 'success', 'Favorito eliminado correctamente');
        } else {
            redirectWithMessage('../views/favoritos.php', 'error', 'Error al eliminar favorito.');
        }
    } else {
        redirectWithMessage('../views/favoritos.php', 'error', 'Método no permitido.');
    }
} elseif ($accion === 'listar' || $accion === '') {
    // Listar favoritos del usuario
    $favoritos = $favoritoModel->obtenerPorUsuario($id_usuario);

    require_once '../models/Producto.php';
    $productoModel = new Producto();

    // Combinar favoritos con detalles del producto
    $favoritosConProductos = [];
    foreach ($favoritos as $fav) {
        $producto = $productoModel->obtenerPorId($fav['id_producto']);
        if ($producto) {
            $favoritosConProductos[] = [
                'favorito' => $fav,
                'producto' => $producto
            ];
        }
    }

    // Hacer disponibles los favoritos con productos a la vista
    $favoritos = $favoritosConProductos;
    require_once '../views/favoritos.php';
} else {
    // Acción no válida
    redirectWithMessage('../views/favoritos.php', 'error', 'Acción inválida');
}
