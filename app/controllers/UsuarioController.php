<?php
// Iniciar sesión para manejar variables globales de usuario
session_start();
// Incluir el modelo Usuario para interactuar con la base de datos
require_once '../models/Usuario.php';

// Obtener el parámetro 'action' de la URL (login, registro, logout, etc.)
$action = $_GET['action'] ?? '';

// Función auxiliar para redirigir con un mensaje en la sesión (error o éxito)
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
    // Procesar solo si el formulario fue enviado por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitizar y obtener email y contraseña
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        // Validar campos vacíos
        if (empty($email) || empty($password)) {
            redirectWithMessage('../views/login.php', 'error', 'Por favor, complete todos los campos.');
        }

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage('../views/login.php', 'error', 'Correo electrónico no válido.');
        }

        // Buscar usuario en la base de datos
        $usuarioModel = new Usuario();
        $user = $usuarioModel->obtenerPorEmail($email);

        // Verificar contraseña con hash almacenado
        if ($user && password_verify($password, $user['password'])) {
            // Guardar datos del usuario en sesión
            $_SESSION['usuario'] = [
                'id_usuario' => $user['id_usuario'],
                'nombre' => htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8'),
                'email' => $user['email'],
                'rol' => $user['rol']
            ];

            // Redirigir a la página principal
            header('Location: ../../index.php');
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
        // Sanitizar y validar datos del formulario
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $confirmar_password = trim($_POST['confirmar_password'] ?? '');

        // Validar campos vacíos
        if (empty($nombre) || empty($email) || empty($password) || empty($confirmar_password)) {
            redirectWithMessage('../views/registro.php', 'error', 'Por favor, complete todos los campos.');
        }

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage('../views/registro.php', 'error', 'Correo electrónico no válido.');
        }

        // Validar que las contraseñas coincidan
        if ($password !== $confirmar_password) {
            redirectWithMessage('../views/registro.php', 'error', 'Las contraseñas no coinciden.');
        }

        $usuarioModel = new Usuario();

        // Verificar si el email ya está registrado
        if ($usuarioModel->existeEmail($email)) {
            redirectWithMessage('../views/registro.php', 'error', 'El correo electrónico ya está registrado.');
        }

        // Hashear la contraseña antes de guardar
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Intentar crear el usuario
        $registrado = $usuarioModel->crear($nombre, $email, $hashedPassword);

        if ($registrado) {
            redirectWithMessage('../views/login.php', 'success', 'Registro exitoso. Por favor, inicie sesión.');
        } else {
            redirectWithMessage('../views/registro.php', 'error', 'Error al registrar el usuario. Intente nuevamente.');
        }
    } else {
        // Redirigir a registro si no es POST
        header('Location: ../views/registro.php');
        exit();
    }

/* ============================
   LOGOUT
   ============================ */
} elseif ($action === 'logout') {
    // Cerrar sesión
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

        // Validar que se proporcione ID de usuario
        if ($id_usuario === null) {
            $response['message'] = 'ID de usuario no proporcionado.';
            echo json_encode($response);
            exit();
        }

        $usuarioModel = new Usuario();
        $user = $usuarioModel->obtenerPorId($id_usuario);

        // Verificar que el usuario exista
        if (!$user) {
            $response['message'] = 'Usuario no encontrado.';
            echo json_encode($response);
            exit();
        }

        // Cambiar rol a vendedor
        $updated = $usuarioModel->actualizarRol($id_usuario, 'vendedor');

        if ($updated) {
            // Actualizar sesión si es el usuario actual
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
   EDICIÓN DE PERFIL
   ============================ */
} elseif ($action === 'editar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Validar campos obligatorios
        if (empty($nombre) || empty($email)) {
            redirectWithMessage('../views/perfil.php', 'error', 'Nombre y correo son obligatorios.');
        }

        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithMessage('../views/perfil.php', 'error', 'Correo electrónico no válido.');
        }

        // Validar que las contraseñas coincidan
        if ($password !== $confirm_password) {
            redirectWithMessage('../views/perfil.php', 'error', 'Las contraseñas no coinciden.');
        }

        $usuarioModel = new Usuario();

        // Verificar si el email ya está registrado por otro usuario
        $usuarioExistente = $usuarioModel->obtenerPorEmail($email);
        if ($usuarioExistente && $usuarioExistente['id_usuario'] != $id_usuario) {
            redirectWithMessage('../views/perfil.php', 'error', 'El correo electrónico ya está registrado por otro usuario.');
        }

        // Actualizar datos con o sin contraseña
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $actualizado = $usuarioModel->actualizarConPassword($id_usuario, $nombre, $email, $hashedPassword);
        } else {
            $actualizado = $usuarioModel->actualizarSinPassword($id_usuario, $nombre, $email);
        }

        if ($actualizado) {
            // Actualizar sesión
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['email'] = $email;
            redirectWithMessage('../views/perfil.php', 'success', 'Datos actualizados correctamente.');
        } else {
            redirectWithMessage('../views/perfil.php', 'error', 'Error al actualizar los datos.');
        }
    } else {
        header('Location: ../views/perfil.php');
        exit();
    }
} elseif ($action === 'eliminar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $usuarioModel = new Usuario();

        $eliminado = $usuarioModel->eliminar($id_usuario);
        if ($eliminado) {
            session_unset();
            session_destroy();
            header('Location: ../views/login.php');
            exit();
        } else {
            redirectWithMessage('../views/perfil.php', 'error', 'Error al eliminar la cuenta.');
        }
    } else {
        header('Location: ../views/perfil.php');
        exit();
    }
} else {
    header('Location: ../views/login.php');
    exit();
}
