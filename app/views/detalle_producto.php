<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

function puedeReservar() {
    if (!isLoggedIn()) return false;
    $rol = $_SESSION['usuario']['rol'] ?? '';
    return in_array($rol, ['comprador', 'vendedor']);
}
?>





<?php
// Ya definido arriba: isLoggedIn() y puedeReservar()
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($producto['nombre']) ?></title>
    <link rel="stylesheet" href="public/css/index.css" />
</head>
<body>
    <?php include_once 'app/views/navbar.php'; ?>

    <main class="detalle-producto">
        <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
        <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="max-width:400px;" />
        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
        <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
        <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>

        <?php if (puedeReservar()): ?>
            <form method="POST" action="reservar.php">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>" />
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                <button type="submit" class="btn-primary">Reservar</button>
            </form>
        <?php elseif (isLoggedIn()): ?>
            <p>Tu rol no permite reservar productos.</p>
        <?php else: ?>
            <p><a href="login.php">Inicia sesión</a> para reservar este producto.</p>
        <?php endif; ?>

        <a href="catalogo.php" class="btn-secondary">Volver al catálogo</a>
    </main>
</body>
</html>

