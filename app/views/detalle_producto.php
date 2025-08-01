<?php

require_once '../models/Calificacion.php';

$calificacionModel = new Calificacion();
$id_producto = $producto['id_producto'] ?? null;
$calificaciones = $id_producto ? $calificacionModel->obtenerPorProducto($id_producto) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Producto</title>
    <link rel="stylesheet" href="../../public/css/detalle_producto.css" />
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

            <form action="../controllers/FavoritoController.php?action=agregar" method="POST" style="display:inline;">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>" />
                <button type="submit" class="btn-favorito">❤️</button>
            </form>

            <!-- Sección agregar calificación -->
            <h3>Agregar Calificación</h3>
            <form action="../controllers/CalificacionController.php?action=crear" method="POST">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>" />
                <label for="estrellas">Estrellas (1 a 5):</label>
                <select name="estrellas" id="estrellas" required>
                    <option value="" selected disabled>Seleccione</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select><br>

                <label for="comentario">Comentario (opcional):</label><br>
                <textarea name="comentario" id="comentario" rows="3" cols="40"></textarea><br>

                <button type="submit">Enviar Calificación</button>
            </form>
 <section class="comentarios">
    <h3>Comentarios y Calificaciones</h3>
    <?php if (!empty($calificaciones)): ?>
        <?php foreach ($calificaciones as $cal): ?>
            <div class="comentario">
                <strong><?= htmlspecialchars($cal['nombre_usuario'] ?? 'Usuario Anónimo') ?></strong>
                <span><?= str_repeat('⭐', (int)$cal['estrellas']) ?></span>
                <?php if (!empty($cal['comentario'])): ?>
                    <p><?= nl2br(htmlspecialchars($cal['comentario'])) ?></p>
                <?php endif; ?>
                <small>Fecha: <?= htmlspecialchars($cal['fecha']) ?></small>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay comentarios para este producto aún.</p>
    <?php endif; ?>
</section>

        <?php else: ?>
            <p><em>Inicia sesión para poder reservar este producto.</em></p>
        <?php endif; ?>

        <a href="../controllers/ProductoController.php?action=catalogo">← Volver al catálogo</a>
    </main>
</body>
</html>

