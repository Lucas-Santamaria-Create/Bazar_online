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
    <title>Reservas Recibidas - Panel de Vendedor</title>
    <link rel="stylesheet" href="../../public/css/panel_vendedor.css" />
</head>
<body>
    <div class="seller-panel-container">
        <h2>Reservas Recibidas</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>. Aquí puedes ver las reservas realizadas a tus productos.</p>
        <?php if (!empty($reservas)): ?>
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Nombre del Comprador</th>
                        <th>Fecha de Reserva</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['nombre_comprador']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['estado']); ?></td>
                            <td>
                                <?php if ($reserva['estado'] === 'pendiente'): ?>
                                    <form method="POST" action="../controllers/ReservaController.php?action=actualizar_estado" style="display:inline;">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>" />
                                        <input type="hidden" name="estado" value="confirmada" />
                                        <button type="submit" class="btn-confirm">Confirmar</button>
                                    </form>
                                    <form method="POST" action="../controllers/ReservaController.php?action=actualizar_estado" style="display:inline;">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>" />
                                        <input type="hidden" name="estado" value="rechazada" />
                                        <button type="submit" class="btn-reject">Rechazar</button>
                                    </form>
                                <?php else: ?>
                                    <span>No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes reservas recibidas.</p>
        <?php endif; ?>
        <a href="../controllers/PanelVendedorController.php?action=productos" class="btn-secondary">Volver al Panel de Vendedor</a>
    </div>
</body>
</html>
