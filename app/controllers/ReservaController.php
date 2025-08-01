<?php
// Iniciar sesión para mantener estado del usuario
session_start();
// Incluir modelos necesarios para reservas y productos
require_once '../models/Reserva.php';
require_once '../models/Producto.php';

// Crear instancias de los modelos
$reservaModel = new Reserva();
$productoModel = new Producto();

// Obtener la acción a realizar desde la URL
$action = $_GET['action'] ?? '';

// Controlador principal que maneja las diferentes acciones según el parámetro 'action'
switch ($action) {

    case 'reservar':
        // Obtener el ID del producto a reservar y validar
        $idProducto = $_GET['id'] ?? null;
        $idProducto = intval($idProducto);

        if (!$idProducto) {
            die('Producto no especificado');
        }

        // Obtener información del producto
        $producto = $productoModel->obtenerPorId($idProducto);
        if (!$producto) {
            die('Producto no encontrado');
        }

        // Verificar disponibilidad de stock
        if ((int)$producto['disponibles'] <= 0) {
            echo "<script>alert('Producto sin stock disponible'); window.history.back();</script>";
            exit;
        }

        // Obtener ID del usuario desde sesión
        $idUsuario = $_SESSION['usuario']['id_usuario'];
        // Crear reserva con estado 'pendiente' y pago adelantado 0
        $exito = $reservaModel->crear($idProducto, $idUsuario, 0, 'pendiente');

        if ($exito) {
            // Actualizar disponibilidad del producto restando 1
            $productoModel->actualizarDisponibilidad($idProducto, $producto['disponibles'] - 1);
            // Redirigir a la vista de mis reservas
            header('Location: ../views/mis_reservas.php');
            exit;
        } else {
            die('Error al realizar la reserva.');
        }

    case 'guardar':
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }

        // Manejar envío del formulario para guardar reserva
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y validar datos del formulario
            $id_producto = $_POST['id_producto'] ?? null;
            $id_producto = intval($id_producto);
            $pago_adelantado = floatval($_POST['pago_adelantado'] ?? 0);
            $id_usuario = $_SESSION['usuario']['id_usuario'];

            if (!$id_producto) {
                die('Producto no especificado.');
            }

            // Verificar disponibilidad del producto
            $producto = $productoModel->obtenerPorId($id_producto);
            if (!$producto || $producto['disponibles'] <= 0) {
                die('Producto no disponible.');
            }

            // Crear reserva con estado 'pendiente' y pago adelantado
            $exito = $reservaModel->crear($id_producto, $id_usuario, $pago_adelantado, 'pendiente');

            if ($exito) {
                // Actualizar disponibilidad del producto restando 1
                $productoModel->actualizarDisponibilidad($id_producto, $producto['disponibles'] - 1);
                // Redirigir a la vista de mis reservas con mensaje de éxito
                header('Location: ../views/mis_reservas.php?mensaje=Reserva realizada con éxito');
                exit();
            } else {
                die('Error al realizar la reserva.');
            }
        }
        break;

    case 'cancelar':
        // Obtener ID de la reserva a cancelar desde POST o GET
        $id_reserva = $_POST['id_reserva'] ?? $_GET['id'] ?? null;
        $id_reserva = intval($id_reserva);
        if ($id_reserva === 0) {
            die('ID de reserva no especificado.');
        }

        // Cancelar la reserva
        $reservaModel->cancelarReserva($id_reserva);

        // Redirección dinámica según el rol del usuario
        if ($_SESSION['usuario']['rol'] === 'vendedor') {
            header("Location: PanelVendedorController.php?action=reservas");
            exit();
        } else {
            header("Location: PanelVendedorController.php?action=reservas");
            exit();
        }
        exit;

    case 'eliminar':
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }

        // Manejar eliminación de reserva vía POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_reserva = $_POST['id_reserva'] ?? null;
            $id_reserva = intval($id_reserva);
            if ($id_reserva) {
                $exito = $reservaModel->eliminar($id_reserva);
                if ($exito) {
                    // Redirigir según rol del usuario
                    if ($_SESSION['usuario']['rol'] === 'vendedor') {
                        header("Location: PanelVendedorController.php?action=reservas");
                    } else {
                        header("Location: PanelVendedorController.php?action=reservas");
                    }
                    exit();
                } else {
                    die('Error al eliminar la reserva.');
                }
            } else {
                die('Reserva no especificada.');
            }
        }
        break;

    case 'actualizar_estado':
        // Manejar actualización del estado de la reserva vía POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_reserva = $_POST['id_reserva'] ?? null;
            $nuevo_estado = $_POST['estado'] ?? null;

            $id_reserva = intval($id_reserva);
            $nuevo_estado = htmlspecialchars(trim($nuevo_estado ?? ''));

            if ($id_reserva && $nuevo_estado) {
                // Actualizar estado de la reserva
                $Reserva->actualizarEstado($id_reserva, $nuevo_estado);

                // Redirección dinámica según rol del usuario
                if ($_SESSION['usuario']['rol'] === 'vendedor') {
                    header('Location: ../views/reservas_vendedor.php?mensaje=Reserva actualizada');
                } else {
                    header('Location: ../views/mis_reservas.php?mensaje=Reserva actualizada');
                }
                exit();
            } else {
                die('Datos incompletos para actualizar la reserva.');
            }
        }
        break;

    case 'mis_reservas':
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }
        // Obtener ID del usuario y sus reservas
        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $reservas = $reservaModel->obtenerPorUsuario($id_usuario);
        // Incluir vista para mostrar las reservas del usuario
        include '../views/mis_reservas.php';
        break;

    default:
        // Redirigir a la página de registro si la acción no es válida
        header('Location: ../views/Registro.php');
        exit();
}
