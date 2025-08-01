<?php
include 'navbar.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['usuario'];

require_once '../models/Favorito.php';
require_once '../models/Producto.php';

$favoritoModel = new Favorito();
$productoModel = new Producto();

// Obtener favoritos del usuario
$favoritos = $favoritoModel->obtenerPorUsuario($user['id_usuario']);
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
        
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje"><?= htmlspecialchars($_GET['mensaje']) ?></div>
        <?php endif; ?>

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
                    <?php foreach ($favoritos as $fav):
                        $producto = $productoModel->obtenerPorId($fav['id_producto']);
                        if (!$producto) continue;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>
                                <img src="../../public/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($producto['nombre']) ?>" class="product-img" width="80" height="80">
                            </td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td>
                                <form method="POST" action="../controllers/FavoritoController.php?action=eliminar" style="display:inline;">
                                    <input type="hidden" name="id_favorito" value="<?= htmlspecialchars($fav['id_favorito']) ?>" />
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
