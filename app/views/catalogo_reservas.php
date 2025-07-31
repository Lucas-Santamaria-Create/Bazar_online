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
    <title>Catálogo de Productos - Reservas</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/catalogo_reservas.css" />
</head>

<body>
    <div class="catalog-container">
        <h2>Catálogo de Productos Disponibles para Reserva</h2>
        <?php if (!empty($productos)): ?>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Disponibles</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="max-width: 100px; max-height: 100px;" />
                            </td>

                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($producto['precio']); ?> €</td>
                            <td><?php echo htmlspecialchars($producto['disponibles']); ?></td>
                            <td>
                                <?php if ($producto['disponibles'] > 0): ?>
                                    <form method="POST" action="../controllers/ReservaController.php?action=crear">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>" />
                                        <input type="number" name="pago_adelantado" placeholder="Pago adelantado (€)" min="0" step="0.01" required />
                                        <button type="submit" class="btn-reservar">Reservar</button>
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
            <p>No hay productos disponibles para reserva.</p>
        <?php endif; ?>
    </div>
</body>

</html>