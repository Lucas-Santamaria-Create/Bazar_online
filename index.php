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
                <a href="app/views/login.php" class="btn-primary">Registrarse</a> <?php endif; ?>
        </section>

        <!-- Barra de búsqueda -->
        <section class="search-bar">
            <form id="filtroForm" method="get" action="app/controllers/ProductoController.php">
                <input type="hidden" name="action" value="catalogo" />

                <input type="text" name="buscar" placeholder="Buscar por nombre..."
                    value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                    onkeydown="if(event.key === 'Enter'){ this.form.submit(); }" />

                <button type="submit" name="buscarBtn">Buscar</button>
            </form>


        </section>

        <?php if (isLoggedIn()): ?>
            <div class="publicar-container">
                <a href="app\views\perfil.php" class="btn-primary">Publicar Producto</a>
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