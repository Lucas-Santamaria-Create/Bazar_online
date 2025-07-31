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
        <?php if (isset($_SESSION['success'])): ?>
            <div id="success-message" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 4px;">
                <?php
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('success-message');
                    if (msg) {
                        msg.style.display = 'none';
                    }
                }, 5000);
            </script>
        <?php endif; ?>
        <h2>Panel de Vendedor</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>. Aquí puedes gestionar tus productos.</p>
        <a href="../controllers/ProductoController.php?action=crear" class="btn-primary">Crear Nuevo Producto</a>
        <a href="../controllers/PanelVendedorController.php?action=reservas" class="btn-primary" style="margin-left: 10px;">Ver Reservas Recibidas</a>
        <?php if (!empty($productos)): ?>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
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
                            <td data-label="Nombre"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td data-label="Precio">$<?php echo number_format($producto['precio'], 2); ?></td>
                            <td data-label="Disponibles"><?php echo intval($producto['disponibles']); ?></td>
                            <td data-label="Categoría"><?php echo htmlspecialchars($producto['categoria']); ?></td>
                            <td data-label="Imagen">
                                <img src="../../public/uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen" width="80" />
                            </td>
                            <td data-label="Acciones">
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