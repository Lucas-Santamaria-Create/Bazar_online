<?php
session_start();

require_once 'app/controllers/ProductoController.php';

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

// Función para sanitizar entrada GET (solo números para id)
function getIntParam($paramName) {
    if (isset($_GET[$paramName]) && ctype_digit($_GET[$paramName])) {
        return (int)$_GET[$paramName];
    }
    return null;
}

$productoController = new ProductoController();

// Obtener ID producto desde GET y validar
$id = getIntParam('id');

if ($id !== null) {
    // Obtener producto por ID con validación
    $producto = $productoController->obtenerProductoPorId($id);

    if (!$producto) {
        $error = "Producto no encontrado.";
    }
} else {
    // Si no hay ID, mostrar todo el catálogo sin filtros (podrías agregar filtros aquí)
    $productos = (new Producto())->obtenerTodos();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Catálogo - Bazar Online</title>
    <link rel="stylesheet" href="public/css/catalogo.css" />
    <!-- Aquí podrías agregar CSS responsivo o framework si tienes -->
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="catalog-container">
        <?php if (isset($error)): ?>
            <!-- Mostrar error si producto no encontrado -->
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <a href="catalogo.php" class="btn-primary">Volver al catálogo</a>

        <?php elseif (isset($producto)): ?>
            <!-- Vista detalle de producto -->
            <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
            <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="max-width:400px;" />
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
            <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
            <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>

            <?php if (isLoggedIn()): ?>
                <!-- Aquí podrías agregar botón para reservar o comprar -->
                <form method="POST" action="reservar.php">
                    <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>" />
                    <button type="submit" class="btn-primary">Reservar</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Inicia sesión</a> para reservar este producto.</p>
            <?php endif; ?>

            <a href="catalogo.php" class="btn-secondary">Volver al catálogo</a>

        <?php else: ?>
            <!-- Vista listado catálogo -->
            <h1>Catálogo de Productos</h1>
            <div class="product-grid">
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card">
                        <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" />
                        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
                        <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
                        <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>
                        <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
                        <a href="catalogo.php?id=<?= (int)$producto['id_producto'] ?>" class="btn-secondary">Ver más</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
