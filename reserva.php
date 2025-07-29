<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once 'app/models/Reserva.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $id_usuario = $_SESSION['usuario']['id_usuario'];

    if ($id_producto && is_numeric($id_producto)) {
        $reservaModel = new Reserva();

        if ($reservaModel->yaReservado($id_usuario, $id_producto)) {
            $mensaje = "Ya has reservado este producto antes.";
        } else {
            $exito = $reservaModel->crearReserva($id_usuario, $id_producto, 0.00, 'pendiente');
            $mensaje = $exito ? "Reserva realizada con éxito." : "❌ Error al realizar la reserva.";
        }
    } else {
        $mensaje = "Producto inválido.";
    }
} else {
    header('Location: catalogo.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Resultado de Reserva</title>
    <link rel="stylesheet" href="public/css/index.css" />
</head>
<body>
    <?php include_once 'app/views/navbar.php'; ?>
    <main class="detalle-producto">
        <h2>Resultado de la reserva</h2>
        <p><?= htmlspecialchars($mensaje) ?></p>
        <a href="catalogo.php" class="btn-primary">Volver al catálogo</a>
    </main>
</body>
</html>
