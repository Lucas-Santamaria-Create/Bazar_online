
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazar Online</title>
    <link rel="stylesheet" href="public/css/index.css">
</head>

<body>
    <!-- Header -->
    <?php include_once 'app/views/navbar.php'; ?>

    <script>
        const navToggle = document.getElementById('nav-toggle');
        const navMenu = document.getElementById('nav-menu');

        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('nav-menu_visible');
        });
    </script>

    <!-- Main -->
    <main>
        <!-- Bienvenida -->
        <section class="welcome">
            <h1>Bienvenido a Bazar Online</h1>
            <p>Encuentra los mejores productos al mejor precio.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="registro.php" class="btn-primary">Registrarse</a>
            <?php endif; ?>
        </section>

        <!-- Barra de búsqueda -->
        <section class="search-bar">
            <form method="GET" action="catalogo.php">
                <input type="text" name="buscar" placeholder="Buscar por nombre..." required />
                <button type="submit">Buscar</button>
            </form>
        </section>

        <!-- Catálogo de productos ejemplo -->
        <!-- este codgo debe debe ser eliminado -->
        <section class="product-grid">
            <?php
            $productos = [
                ['imagen' => 'public/img/producto1.jpg', 'nombre' => 'Producto 1', 'precio' => '10.00'],
                ['imagen' => 'public/img/producto2.jpg', 'nombre' => 'Producto 2', 'precio' => '20.00'],
                ['imagen' => 'public/img/producto3.jpg', 'nombre' => 'Producto 3', 'precio' => '30.00'],
                ['imagen' => 'public/img/producto4.jpg', 'nombre' => 'Producto 4', 'precio' => '40.00'],
                ['imagen' => 'public/img/producto5.jpg', 'nombre' => 'Producto 5', 'precio' => '50.00'],
                ['imagen' => 'public/img/producto6.jpg', 'nombre' => 'Producto 6', 'precio' => '60.00'],
            ];

            foreach ($productos as $producto): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>" />
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p>$<?php echo htmlspecialchars($producto['precio']); ?></p>
                    <a href="detalle_producto.php?nombre=<?php echo urlencode($producto['nombre']); ?>" class="btn-secondary">Ver más</a>
                </div>
            <?php endforeach; ?>
        </section>

        <?php if (isLoggedIn()): ?>
            <div class="publicar-container">
                <a href="publicar_producto.php" class="btn-primary">Publicar Producto</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-links">
            <a href="contacto.php">Contacto</a> |
            <a href="redes.php">Redes Sociales</a> |
            <a href="terminos.php">Términos de Uso</a>
        </div>
        <div>&copy; <?php echo date('Y'); ?> Bazar Online</div>
    </footer>
</body>

</html>