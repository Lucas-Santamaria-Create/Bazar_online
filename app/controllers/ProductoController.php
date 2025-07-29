<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/Producto.php';

$action = $_GET['action'] ?? '';
$productoModel = new Producto();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

function redirectWithMessage($location, $type, $message) {
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

switch ($action) {
    case 'listar':
        // List products for the logged-in user
        $productos = $productoModel->obtenerPorUsuario($id_usuario);
        include '../views/panel_vendedor.php';
        break;

    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio'] ?? 0);
            $disponibles = intval($_POST['disponibles'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'Otros');

            // Handle image upload
            $imagen = 'default.jpg';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $fileName = basename($_FILES['imagen']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $fileName;
                }
            }

            $productoModel->crear($id_usuario, $nombre, $descripcion, $precio, $imagen, $disponibles, $categoria);
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto creado correctamente.');
        } else {
            include '../views/producto_form.php';
        }
        break;

    case 'editar':
        $id_producto = intval($_GET['id'] ?? 0);
        $producto = $productoModel->obtenerPorId($id_producto);
        if (!$producto || $producto['id_usuario'] != $id_usuario) {
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
        }
        include '../views/producto_form.php';
        break;

    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_producto = intval($_POST['id_producto'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio'] ?? 0);
            $disponibles = intval($_POST['disponibles'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'Otros');

            $producto = $productoModel->obtenerPorId($id_producto);
            if (!$producto || $producto['id_usuario'] != $id_usuario) {
                redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
            }

            $imagen = $producto['imagen'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $fileName = basename($_FILES['imagen']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $fileName;
                }
            }

            $productoModel->actualizar($id_producto, $nombre, $descripcion, $precio, $imagen, $disponibles, $categoria);
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto actualizado correctamente.');
        }
        break;

    case 'eliminar':
        $id_producto = intval($_GET['id'] ?? 0);
        $producto = $productoModel->obtenerPorId($id_producto);
        if (!$producto || $producto['id_usuario'] != $id_usuario) {
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
        }
        $productoModel->eliminar($id_producto);
        redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto eliminado correctamente.');
        break;

    default:
        header('Location: ../views/perfil.php');
        exit();
}
?>
