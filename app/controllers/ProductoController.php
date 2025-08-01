<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Incluir el modelo Producto para interactuar con la base de datos
require_once '../models/Producto.php';

// Obtener la acción a realizar desde la URL, por defecto 'listar'
$action = $_GET['action'] ?? 'listar';
// Crear instancia del modelo Producto
$productoModel = new Producto();

// Verificar si la acción requiere usuario autenticado, si no está autenticado redirigir al login
if (!in_array($action, ['catalogo', 'detalle']) && !isset($_SESSION['usuario'])) {
    header('Location: ../views/login.php');
    exit();
}

// Obtener el id del usuario autenticado si existe
$id_usuario = null;
if (isset($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
}

// Función auxiliar para redirigir con un mensaje de sesión
function redirectWithMessage($location, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

// Controlador principal que maneja las diferentes acciones según el parámetro 'action'
switch ($action) {
    case 'crear':
        // Manejar creación de producto
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y obtener datos del formulario
            $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
            $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
            $precio = floatval($_POST['precio'] ?? 0);
            $disponibles = intval($_POST['disponibles'] ?? 0);
            $categoria = htmlspecialchars(trim($_POST['categoria'] ?? 'Otros'));

            // Imagen por defecto si no se sube ninguna
            $imagen = 'default.jpg';

            // Manejar subida de imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $originalName = basename($_FILES['imagen']['name']);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                // Validar extensión permitida para seguridad básica
                $extPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($extension), $extPermitidas)) {
                    redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Formato de imagen no permitido.');
                }

                // Generar nombre único para evitar sobrescribir imágenes
                $nuevoNombre = uniqid('img_', true) . '.' . $extension;
                $targetFile = $uploadDir . $nuevoNombre;

                // Mover archivo subido a la carpeta destino
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $nuevoNombre;
                } else {
                    redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Error al subir la imagen.');
                }
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Error en la subida de imagen
                redirectWithMessage('../controllers/ProductoController.php?action=crear', 'error', 'Error al subir la imagen.');
            }

            // Crear producto en la base de datos
            $productoModel->crear($id_usuario, $nombre, $descripcion, $precio, $imagen, $disponibles, $categoria);
            // Redirigir a la lista de productos con mensaje de éxito
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto creado correctamente.');
        } else {
            // Mostrar formulario de creación de producto
            include '../views/producto_form.php';
        }
        break;

    case 'editar':
        // Obtener id del producto a editar
        $id_producto = intval($_GET['id'] ?? 0);
        // Obtener datos del producto
        $producto = $productoModel->obtenerPorId($id_producto);
        // Verificar que el producto exista y pertenezca al usuario autenticado
        if (!$producto || $producto['id_usuario'] != $id_usuario) {
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
        }
        // Mostrar formulario de edición con datos cargados
        include '../views/producto_form.php';
        break;

    case 'actualizar':
        // Manejar actualización de producto
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y sanitizar datos del formulario
            $id_producto = intval($_POST['id_producto'] ?? 0);
            $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
            $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
            $precio = floatval($_POST['precio'] ?? 0);
            $disponibles = intval($_POST['disponibles'] ?? 0);
            $categoria = htmlspecialchars(trim($_POST['categoria'] ?? 'Otros'));

            // Obtener producto actual para verificar permisos
            $producto = $productoModel->obtenerPorId($id_producto);
            if (!$producto || $producto['id_usuario'] != $id_usuario) {
                redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
            }

            // Mantener imagen actual si no se sube una nueva
            $imagen = $producto['imagen'];
            // Manejar subida de nueva imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $tmpName = $_FILES['imagen']['tmp_name'];
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $uniqueName = uniqid('img_', true) . '.' . $extension;
                $targetFile = $uploadDir . $uniqueName;

                // Mover archivo subido a la carpeta destino
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagen = $uniqueName;
                } else {
                    redirectWithMessage('../controllers/ProductoController.php?action=editar&id=' . $id_producto, 'error', 'Error al subir la imagen.');
                }
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Error en la subida de imagen
                redirectWithMessage('../controllers/ProductoController.php?action=editar&id=' . $id_producto, 'error', 'Error al subir la imagen.');
            }

            // Actualizar producto en la base de datos
            $productoModel->actualizar($id_producto, $nombre, $descripcion, $precio, $imagen, $disponibles, $categoria);
            // Redirigir a la lista de productos con mensaje de éxito
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'success', 'Producto actualizado correctamente.');
        }
        break;

    case 'eliminar':
        // Manejar eliminación de producto
        $id_producto = intval($_GET['id'] ?? 0);
        $producto = $productoModel->obtenerPorId($id_producto);
        // Verificar que el producto exista y pertenezca al usuario autenticado
        if (!$producto || $producto['id_usuario'] != $id_usuario) {
            redirectWithMessage('../controllers/ProductoController.php?action=listar', 'error', 'Producto no encontrado o acceso denegado.');
        }
        // Eliminar imagen del producto si no es la imagen por defecto
        $imagen = $producto['imagen'];
        if ($imagen !== 'default.jpg') {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            $imagePath = $uploadDir . $imagen;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Eliminar producto de la base de datos
        $productoModel->eliminar($id_producto);
        // Redirigir a la lista de productos del vendedor con mensaje de éxito
        redirectWithMessage('../controllers/PanelVendedorController.php?action=productos', 'success', 'Producto eliminado correctamente.');
        break;

    case 'catalogo':
        // Mostrar catálogo de productos con filtros opcionales
        $buscar = $_GET['buscar'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $productos = $productoModel->obtenerTodos($buscar, $categoria);
        include '../views/catalogo.php';
        break;

    case 'detalle':
        // Validar que se haya recibido un id válido
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("Producto no válido.");
        }

        // Obtener detalle del producto junto con información del vendedor
        $id_producto = (int)$_GET['id'];
        $producto = $productoModel->obtenerDetalleConVendedor($id_producto);

        // Verificar que el producto exista
        if (!$producto) {
            die("Producto no encontrado.");
        }

        // Mostrar vista de detalle del producto
        include '../views/detalle_producto.php';
        break;

    default:
        // Redirigir a perfil si la acción no es válida
        header('Location: ../views/perfil.php');
        exit();
}
