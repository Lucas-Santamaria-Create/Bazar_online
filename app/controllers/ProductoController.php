<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/Producto.php';

$action = $_GET['action'] ?? 'listar';
$productoModel = new Producto();

if (!in_array($action, ['catalogo', 'detalle']) && !isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}


$id_usuario = null;
if (isset($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
}

function redirectWithMessage($location, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

switch ($action) {
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio'] ?? 0);
            $disponibles = intval($_POST['disponibles'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'Otros');

            // Subir imagen a uploads
            $imagen = 'default.jpg';

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $originalName = basename($_FILES['imagen']['name']);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                // Asegúrate de que la extensión sea válida (seguridad básica)
                $extPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($extension), $extPermitidas)) {
                    redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Formato de imagen no permitido.');
                }

                // Nombre único para evitar sobrescribir imágenes
                $nuevoNombre = uniqid('img_', true) . '.' . $extension;
                $targetFile = $uploadDir . $nuevoNombre;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $nuevoNombre;
                } else {
                    redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Error al subir la imagen.');
                }
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Error al subir la imagen.');
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

            $imagen = $producto['imagen']; // conservar imagen actual si no se sube nueva
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $uniqueName = uniqid('img_', true) . '.' . $extension;
                $targetFile = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $uniqueName;
                } else {
                    redirectWithMessage('../controllers/ProductoController.php?action=editar&id=' . $id_producto, 'error', 'Error al subir la imagen.');
                }
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                redirectWithMessage('../controllers/ProductoController.php?action=editar&id=' . $id_producto, 'error', 'Error al subir la imagen.');
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
        $imagen = $producto['imagen'];
        if ($imagen !== 'default.jpg') {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            $imagePath = $uploadDir . $imagen;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $productoModel->eliminar($id_producto);
        redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto eliminado correctamente.');
        break;

    case 'catalogo':
        $buscar = $_GET['buscar'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $productos = $productoModel->obtenerTodos($buscar, $categoria);
        include '../views/catalogo.php';
        break;

    case 'detalle':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("Producto no válido.");
        }

        $id_producto = (int)$_GET['id'];
        $producto = $productoModel->obtenerDetalleConVendedor($id_producto);

        if (!$producto) {
            die("Producto no encontrado.");
        }

        include '../views/detalle_producto.php';
        break;



    default:
        header('Location: ../views/perfil.php');
        exit();
}
