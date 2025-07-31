<?php
include 'navbar.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['usuario'];
require_once '../models/Reserva.php'; // Asegúrate de que la ruta es correcta

$reservaModel = new Reserva();
$reservas = $reservaModel->obtenerReservasPorUsuario($user['id_usuario']);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="../../public/css/mis_reservas.css" />
</head>

<body>
    <div class="reservations-container">
        <h2>Mis Reservas</h2>
        <?php if (!empty($reservas)): ?>
            <div class="table-wrapper">
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Correo</th>
                        <th>Fecha de Reserva</th>
                        <th>Estado</th>
                        <th>Pago Adelantado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['nombre_producto']) ?></td>
                            <td><?= htmlspecialchars($reserva['email_vendedor']) ?></td> <!-- Mostrar email -->
                            <td><?= htmlspecialchars($reserva['fecha_reserva']) ?></td>
                            <td><?= htmlspecialchars($reserva['estado']) ?></td>
                            <td><?= htmlspecialchars($reserva['pago_adelantado']) ?> €</td>
                            <td>
                                <?php if ($reserva['estado'] === 'pendiente'): ?>
                                    <form method="POST" action="../controllers/ReservaController.php?action=actualizar_estado" style="display:inline;">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>" />
                                        <input type="hidden" name="estado" value="confirmada" />
                                        <button type="submit" class="btn-confirm">Confirmar</button>
                                    </form>
                                    <form method="POST" action="../controllers/ReservaController.php?action=cancelar" style="display:inline;">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>" />
                                        <button type="submit" class="btn-cancel" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                    </form>
                                <?php else: ?>
                                    <span>No disponible</span>
                                    <form method="POST" action="../controllers/ReservaController.php?action=eliminar" style="display:inline;">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>" />
                                        <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta reserva? Esta acción no se puede deshacer.')">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <p>No tienes reservas.</p>
        <?php endif; ?>
        <a href="../controllers/ProductoController.php?action=catalogo">← Volver al catálogo</a>
    </div>
</body>

</html>