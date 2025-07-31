<?php include 'navbar.php'; 
if (!isset($productos)) {
    $productos = []; // Evita el error si se abre mal
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Catálogo - Bazar Online</title>
    <link rel="stylesheet" href="/bazar_online/public/css/catalogo.css" />
</head>
<body>
<main>
    <h1>Catálogo de Productos</h1>
    
    <form id="filtroForm" method="get" action="../controllers/ProductoController.php">
    <input type="hidden" name="action" value="catalogo" />
    
    <input type="text" name="buscar" placeholder="Buscar por nombre..." 
       value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" 
       onkeydown="if(event.key === 'Enter'){ this.form.submit(); }" />

        <button type="submit" name="buscarBtn">Buscar</button>

    <select name="categoria" id="categoriaSelect">
        <option value="">Todas las categorías</option>
        <option value="Accesorio" <?= (($_GET['categoria'] ?? '') === 'Accesorio') ? 'selected' : '' ?>>Accesorio</option>
        <option value="Libros y Papelería" <?= (($_GET['categoria'] ?? '') === 'Libros y Papelería') ? 'selected' : '' ?>>Libros y Papelería</option>
        <option value="Mascotas" <?= (($_GET['categoria'] ?? '') === 'Mascotas') ? 'selected' : '' ?>>Mascotas</option>
        <option value="Juguetes" <?= (($_GET['categoria'] ?? '') === 'Juguetes') ? 'selected' : '' ?>>Juguetes</option>
        <option value="Ropa y Moda" <?= (($_GET['categoria'] ?? '') === 'Ropa y Moda') ? 'selected' : '' ?>>Ropa y Moda</option>
        <option value="Salud y Belleza" <?= (($_GET['categoria'] ?? '') === 'Salud y Belleza') ? 'selected' : '' ?>>Salud y Belleza</option>
        <option value="Otros" <?= (($_GET['categoria'] ?? '') === 'Otros') ? 'selected' : '' ?>>Otros</option>
    </select>

    </form>


    <?php if (empty($productos)): ?>
        <p>No hay productos publicados aún.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="product-card">
                    <img src="../../public/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" />
                    <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
                    <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
                    <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>
                    <p><strong>Disponibles:</strong> <?= (int)$producto['disponibles'] ?></p>
                    <a href="../controllers/ProductoController.php?action=detalle&id=<?= (int)$producto['id_producto'] ?>">Ver más</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

    <script>
    document.getElementById('categoriaSelect').addEventListener('change', function () {
        document.getElementById('filtroForm').submit();
    });
    </script>

</body>
</html>
