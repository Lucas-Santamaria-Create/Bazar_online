<?php
include 'navbar.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulario de Reserva - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/producto_form.css" />
</head>
<body>
    <div class="product-form-container">
        <h2>Reservar Producto: <?= htmlspecialchars($producto['nombre']) ?></h2>
        <img src="../../public/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" width="150" />
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
        <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
        <form action="../controllers/ReservaController.php?action=guardar" method="POST">
            <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>" />
            <label for="pago_adelantado">Pago Adelantado ($):</label>
            <input type="number" id="pago_adelantado" name="pago_adelantado" step="0.01" min="0" required />
            <button type="submit">Confirmar Reserva</button>
            <a href="../controllers/ProductoController.php?action=detalle&id=<?= (int)$producto['id_producto'] ?>" class="btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
