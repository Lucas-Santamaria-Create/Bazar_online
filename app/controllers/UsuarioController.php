<?php
session_start(); // Inicia la sesión para manejar variables globales de usuario
require_once '../models/Usuario.php'; // Incluye el modelo Usuario para interactuar con la base de datos

// Obtiene el parámetro 'action' de la URL (login, registro, logout, etc.)
$action = $_GET['action'] ?? '';

// Función para redirigir con un mensaje en la sesión (error o éxito)
function redirectWithMessage($location, $type, $message)
{
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

/* ============================
   LOGIN
   ============================ */
if ($action === 'login') {
    // Solo procesa si se envió el formulario por método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitiza y recorta el correo y la contraseña ingresada
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        // Validación: campos vacíos
        if (empty($email) || empty($password)) {
            redirectWithMessage('../views/login.php', 'error', 'Por favor, complete todos los campos.');
        }

        // Validación: formato de correo
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage('../views/login.php', 'error', 'Correo electrónico no válido.');
        }

        // Busca el usuario en la base de datos
        $usuarioModel = new Usuario();
        $user = $usuarioModel->obtenerPorEmail($email);

        // Verifica la contraseña usando el hash almacenado en la BD
        if ($user && password_verify($password, $user['password'])) {
            // Guarda los datos del usuario en la sesión
            $_SESSION['usuario'] = [
                'id_usuario' => $user['id_usuario'],
                'nombre' => htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8'),
                'email' => $user['email'],
                'rol' => $user['rol']
            ];
            // Set cookie for last login date, expires in 30 days
            date_default_timezone_set('America/Panama');
            setcookie('last_login', date('Y-m-d h:i A'), time() + (30 * 24 * 60 * 60), "/");

            header('Location: ../../index.php'); // Redirige al inicio
            exit();
        } else {
            // Credenciales incorrectas
            redirectWithMessage('../views/login.php', 'error', 'Correo electrónico o contraseña incorrectos.');
        }
    }

    /* ============================
   REGISTRO
   ============================ */
} elseif ($action === 'registro') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitiza y valida los datos del formulario
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $confirmar_password = trim($_POST['confirmar_password'] ?? '');

        // Validaciones de campos
        if (empty($nombre) || empty($email) || empty($password) || empty($confirmar_password)) {
            redirectWithMessage('../views/registro.php', 'error', 'Por favor, complete todos los campos.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage('../views/registro.php', 'error', 'Correo electrónico no válido.');
        }

        if ($password !== $confirmar_password) {
            redirectWithMessage('../views/registro.php', 'error', 'Las contraseñas no coinciden.');
        }

        $usuarioModel = new Usuario();

        // Verifica si el email ya está registrado
        if ($usuarioModel->existeEmail($email)) {
            redirectWithMessage('../views/registro.php', 'error', 'El correo electrónico ya está registrado.');
        }

        // Hashea la contraseña antes de guardarla en la BD
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Intenta crear el usuario
        $registrado = $usuarioModel->crear($nombre, $email, $hashedPassword);

        if ($registrado) {
            redirectWithMessage('../views/login.php', 'success', 'Registro exitoso. Por favor, inicie sesión.');
        } else {
            redirectWithMessage('../views/registro.php', 'error', 'Error al registrar el usuario. Intente nuevamente.');
        }
    } else {
        // Si se accede sin POST, redirige al registro
        header('Location: ../views/registro.php');
        exit();
    }

    /* ============================
   LOGOUT
   ============================ */
} elseif ($action === 'logout') {
    // Cierra la sesión
    session_unset();
    session_destroy();
    header('Location: ../views/login.php');
    exit();

    /* ============================
   CONVERTIR A VENDEDOR
   ============================ */
} elseif ($action === 'convertirVendedor') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_usuario = $_POST['id_usuario'] ?? null;
        $response = ['success' => false, 'message' => ''];

        // Validación: ID de usuario requerido
        if ($id_usuario === null) {
            $response['message'] = 'ID de usuario no proporcionado.';
            echo json_encode($response);
            exit();
        }

        $usuarioModel = new Usuario();
        $user = $usuarioModel->obtenerPorId($id_usuario);

        if (!$user) {
            $response['message'] = 'Usuario no encontrado.';
            echo json_encode($response);
            exit();
        }

        // Cambia el rol del usuario a "vendedor"
        $updated = $usuarioModel->actualizarRol($id_usuario, 'vendedor');

        if ($updated) {
            // Si es el usuario actual, también actualiza la sesión
            if (isset($_SESSION['usuario']) && $_SESSION['usuario']['id_usuario'] == $id_usuario) {
                $_SESSION['usuario']['rol'] = 'vendedor';
            }
            $response['success'] = true;
            $response['message'] = 'Rol actualizado a vendedor correctamente.';
        } else {
            $response['message'] = 'Error al actualizar el rol.';
        }

        echo json_encode($response);
        exit();
    }

    /* ============================
   DEFAULT (redirige al login)
   ============================ */
} else {
    header('Location: ../views/login.php');
    exit();
}
