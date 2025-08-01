<?php
session_start();
require_once '../models/Reserva.php';
require_once '../models/Producto.php';

$reservaModel = new Reserva();
$productoModel = new Producto();

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'reservar':
        $idProducto = $_GET['id'] ?? null;

        if (!$idProducto) {
            die('Producto no especificado');
        }

        $producto = $productoModel->obtenerPorId($idProducto);
        if (!$producto) {
            die('Producto no encontrado');
        }

        if ((int)$producto['disponibles'] <= 0) {
            echo "<script>alert('Producto sin stock disponible'); window.history.back();</script>";
            exit;
        }

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $exito = $reservaModel->crear($idProducto, $idUsuario, 0, 'pendiente');

        if ($exito) {
            $productoModel->actualizarDisponibilidad($idProducto, $producto['disponibles'] - 1);
            header('Location: ../views/mis_reservas.php');
            exit;
        } else {
            die('Error al realizar la reserva.');
        }

    case 'guardar':
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_producto = $_POST['id_producto'] ?? null;
            $pago_adelantado = floatval($_POST['pago_adelantado'] ?? 0);
            $id_usuario = $_SESSION['usuario']['id_usuario'];

            if (!$id_producto) {
                die('Producto no especificado.');
            }

            $producto = $productoModel->obtenerPorId($id_producto);
            if (!$producto || $producto['disponibles'] <= 0) {
                die('Producto no disponible.');
            }

            $exito = $reservaModel->crear($id_producto, $id_usuario, $pago_adelantado, 'pendiente');

            if ($exito) {
                $productoModel->actualizarDisponibilidad($id_producto, $producto['disponibles'] - 1);
                header('Location: ../views/mis_reservas.php?mensaje=Reserva realizada con éxito');
                exit();
            } else {
                die('Error al realizar la reserva.');
            }
        }
        break;

    case 'cancelar':
        $id_reserva = $_POST['id_reserva'] ?? $_GET['id'] ?? null;
        if ($id_reserva === null) {
            die('ID de reserva no especificado.');
        }

        $reservaModel->cancelarReserva($id_reserva);

        // Redirección dinámica según el rol
        if ($_SESSION['usuario']['rol'] === 'vendedor') {
            header("Location: PanelVendedorController.php?action=reservas");
            exit();
        } else {
            header("Location: PanelVendedorController.php?action=reservas");
            exit();
        }
        exit;

    case 'eliminar':
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_reserva = $_POST['id_reserva'] ?? null;
            if ($id_reserva) {
                $exito = $reservaModel->eliminar($id_reserva);
                if ($exito) {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_reserva = $_POST['id_reserva'] ?? null;
            $nuevo_estado = $_POST['estado'] ?? null;

            if ($id_reserva && $nuevo_estado) {
                $Reserva->actualizarEstado($id_reserva, $nuevo_estado);

                // Redirección dinámica según rol
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
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../views/login.php');
            exit();
        }
        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $reservas = $reservaModel->obtenerPorUsuario($id_usuario);
        include '../views/mis_reservas.php';
        break;

    default:
        header('Location: ../views/perfil.php');
        exit();
}
