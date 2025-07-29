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
    <title>Panel de Vendedor - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/panel_vendedor.css" />
</head>
<body>
    <div class="seller-panel-container">
        <h2>Panel de Vendedor</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>. Aquí puedes gestionar tus productos.</p>
        <a href="../controllers/ProductoController.php?action=crear" class="btn-primary">Crear Nuevo Producto</a>
        <?php if (!empty($productos)): ?>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Disponibles</th>
                        <th>Categoría</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                            <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                            <td><?php echo intval($producto['disponibles']); ?></td>
                            <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                            <td>
                                <img src="../../public/uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen" width="80" />
                            </td>
                            <td>
                                <a href="../controllers/ProductoController.php?action=editar&id=<?php echo $producto['id_producto']; ?>" class="btn-edit">Editar</a>
                                <a href="../controllers/ProductoController.php?action=eliminar&id=<?php echo $producto['id_producto']; ?>" class="btn-delete" onclick="return confirm('¿Está seguro de eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes productos publicados.</p>
        <?php endif; ?>
        <a href="../views/perfil.php" class="btn-secondary">Volver al Perfil</a>
    </div>
</body>
</html>
