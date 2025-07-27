<?php
session_start();

// Funciones de sesión
function isLoggedIn()
{
    return isset($_SESSION['usuario']);
}

function getUserName()
{
    return $_SESSION['usuario']['nombre'] ?? '';
}
?>

<link rel="stylesheet" href="/Bazar_online/public/css/navbar.css" />
<header class="header">
    <button class="nav-toggle" id="nav-toggle" aria-label="Abrir menú de navegación">
        &#9776;
    </button>
    <nav class="nav" id="nav-menu">
        <div class="logo">Bazar Online</div>
        <div class="nav-links">
            <a href="\Bazar_online\index.php">Inicio</a>
            <a href="\Bazar_online\app\views\catalogo.php">Catálogo</a>
            <?php if (!isLoggedIn()): ?>
                <a href="\Bazar_online\app\views\login.php">Iniciar Sesión</a>
                <a href="\Bazar_online\app\views\registro.php">Registrarse</a>
            <?php else: ?>
                <div class="user-info">
                    <a href="\Bazar_online\app\views\perfil.php">Perfil</a>
                    <a href="\Bazar_online\logout.php">Cerrar Sesión</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script>
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('nav-menu_visible');
    });
</script>
