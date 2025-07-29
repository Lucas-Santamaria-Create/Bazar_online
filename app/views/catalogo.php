<?php
require_once 'app/controllers/ProductoController.php';

$productoController = new ProductoController();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $producto = $productoController->obtenerProductoPorId($id);

    if (!$producto) {
        $error = "Producto no encontrado";
    }
} else {
    $productos = (new Producto())->obtenerTodos();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Catálogo - Bazar Online</title>
    <link rel="stylesheet" href="public/css/catalogo.css" />
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="catalog-container">
        <?php if (isset($error)): ?>
            <p><?= htmlspecialchars($error) ?></p>
            <a href="catalogo.php">Volver al catálogo</a>
        <?php elseif (isset($producto)): ?>
            <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
            <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="max-width:400px;">
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
            <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
            <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>
            <a href="catalogo.php">Volver al catálogo</a>
        <?php else: ?>
            <h1>Catálogo de Productos</h1>
            <div class="product-grid">
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card">
                        <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
                        <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
                        <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>
                        <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
                        <a href="catalogo.php?id=<?= $producto['id_producto'] ?>" class="btn-secondary">Ver más</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
