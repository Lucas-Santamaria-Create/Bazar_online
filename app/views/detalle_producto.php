<!-- detalle_producto.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Producto</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <h1><?= htmlspecialchars($producto['nombre']) ?></h1>

        <img src="../../public/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen del producto" width="300">

        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
        <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
        <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>

        <?php if (isset($_SESSION['usuario'])): ?>
    <?php if ((int)$producto['disponibles'] > 0): ?>
        <a href="../controllers/ReservaController.php?action=reservar&id=<?= (int)$producto['id_producto'] ?>" class="btn-reservar">Reservar</a>
    <?php else: ?>
        <p><strong>Producto sin stock disponible.</strong></p>
    <?php endif; ?>
<?php else: ?>
    <p><em>Inicia sesión para poder reservar este producto.</em></p>
<?php endif; ?>
        <a href="../controllers/ProductoController.php?action=catalogo">← Volver al catálogo</a>
    </main>
</body>
</html>
