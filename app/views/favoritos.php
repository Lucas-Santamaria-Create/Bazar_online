<?php
include 'navbar.php';

include_once '../controllers/FavoritoController.php';
function mostrarMensaje($tipo) {
    if (isset($_SESSION[$tipo])) {
        echo '<div class="mensaje">' . htmlspecialchars($_SESSION[$tipo]) . '</div>';
        unset($_SESSION[$tipo]);
    }
    
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mis Favoritos</title>
    <link rel="stylesheet" href="../../public/css/style.css" />
    <link rel="stylesheet" href="../../public/css/favorito.css" />
</head>

<body>
    <div class="favoritos-container">
        <h2>Mis Favoritos</h2>
        
        <?php mostrarMensaje('success'); ?>
        <?php mostrarMensaje('error'); ?>

        <?php if (!empty($favoritos)): ?>
            <table class="favoritos-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($favoritos as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['producto']['nombre']) ?></td>
                            <td>
                                <img src="../../public/uploads/<?= htmlspecialchars($item['producto']['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($item['producto']['nombre']) ?>" class="product-img" width="80" height="80">
                            </td>
                            <td>$<?= number_format($item['producto']['precio'], 2) ?></td>
                            <td>
                                <form method="POST" action="../controllers/FavoritoController.php?action=eliminar" style="display:inline;">
                                    <input type="hidden" name="id_favorito" value="<?= htmlspecialchars($item['favorito']['id_favorito']) ?>" />
                                    <button type="submit" class="btn-remove" onclick="return confirm('¿Eliminar este favorito?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes favoritos.</p>
        <?php endif; ?>

        <a href="../controllers/ProductoController.php?action=catalogo" class="link-volver">← Volver al catálogo</a>
    </div>
</body>

</html>

        <a href="../controllers/ProductoController.php?action=catalogo" class="link-volver">← Volver al catálogo</a>
    </div>
</body>

</html>
