<?php
include 'navbar.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['usuario'];

$isEdit = isset($producto);
$formAction = $isEdit ? '../controllers/ProductoController.php?action=actualizar' : '../controllers/ProductoController.php?action=crear';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $isEdit ? 'Editar Producto' : 'Crear Producto'; ?> - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/producto_form.css" />
</head>
<body>
    <div class="product-form-container">
        <h2><?php echo $isEdit ? 'Editar Producto' : 'Crear Producto'; ?></h2>
        <form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id_producto" value="<?php echo intval($producto['id_producto']); ?>" />
            <?php endif; ?>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required value="<?php echo $isEdit ? htmlspecialchars($producto['nombre']) : ''; ?>" />

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo $isEdit ? htmlspecialchars($producto['descripcion']) : ''; ?></textarea>

            <label for="precio">Precio (€):</label>
            <input type="number" id="precio" name="precio" step="0.01" min="0" required value="<?php echo $isEdit ? htmlspecialchars($producto['precio']) : ''; ?>" />

            <label for="disponibles">Disponibles:</label>
            <input type="number" id="disponibles" name="disponibles" min="0" value="<?php echo $isEdit ? intval($producto['disponibles']) : '0'; ?>" />

            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria">
                <?php
                $categorias = ['Accesorio', 'Libros y Papelería', 'Mascotas', 'Juguetes', 'Ropa y Moda', 'Salud y Belleza', 'Otros'];
                $selectedCategoria = $isEdit ? $producto['categoria'] : 'Otros';
                foreach ($categorias as $categoria) {
                    $selected = ($categoria === $selectedCategoria) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($categoria) . "\" $selected>" . htmlspecialchars($categoria) . "</option>";
                }
                ?>
            </select>

            <label for="imagen">Imagen:</label>
            <?php if ($isEdit && !empty($producto['imagen'])): ?>
                <img src="../../public/uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen actual" width="120" />
            <?php endif; ?>
            <input type="file" id="imagen" name="imagen" accept="image/*" />

            <button type="submit"><?php echo $isEdit ? 'Actualizar' : 'Crear'; ?></button>
            <a href="../controllers/PanelVendedorController.php?action=productos" class="btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
