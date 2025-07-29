<?php
 if (isLoggedIn()): ?>
    <form method="POST" action="reservar.php">
        <input type="hidden" name="id_producto" value="<?= htmlspecialchars($producto['id_producto']) ?>" />
        <button type="submit" class="btn-primary">Reservar</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Inicia sesión</a> para poder reservar este producto.</p>
<?php endif; 
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

        <!-- Aquí puedes agregar sección para calificaciones, botón reservar, favoritos, etc. -->

        <a href="catalogo.php">Volver al catálogo</a>
    </main>
</body>
</html>
