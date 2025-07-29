<?php
session_start(); // Para manejar sesión y $_SESSION['usuario']

require_once 'app/models/Producto.php';
require_once 'app/models/Reserva.php';

// Función auxiliar para verificar sesión iniciada
function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

// Procesar reserva si se envía un POST con id_producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    if (!isLoggedIn()) {
        // Si no está logueado, redirigir a login
        header('Location: login.php');
        exit;
    }

    $id_producto = $_POST['id_producto'];
    $id_usuario = $_SESSION['usuario']['id_usuario'];

    if ($id_producto && is_numeric($id_producto)) {
        $reservaModel = new Reserva();

        if ($reservaModel->yaReservado($id_usuario, $id_producto)) {
            $mensaje = "Ya has reservado este producto antes.";
        } else {
            $exito = $reservaModel->crearReserva($id_usuario, $id_producto, 0.00, 'pendiente');
            $mensaje = $exito ? "Reserva realizada con éxito." : "❌ Error al realizar la reserva.";
        }
    } else {
        $mensaje = "Producto inválido.";
    }

    // Mostrar mensaje resultado reserva y detener ejecución para no mostrar catálogo
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title>Resultado de Reserva</title>
        <link rel="stylesheet" href="public/css/index.css" />
    </head>
    <body>
        <?php include_once 'app/views/navbar.php'; ?>
        <main class="detalle-producto">
            <h2>Resultado de la reserva</h2>
            <p><?= htmlspecialchars($mensaje) ?></p>
            <a href="index.php" class="btn-primary">Volver al catálogo</a>
        </main>
    </body>
    </html>
    <?php
    exit; // No continuar con la carga normal del catálogo
}

// Ruteo: si se pide ver el detalle de un producto
if (isset($_GET['r']) && $_GET['r'] === 'detalle') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $productoModel = new Producto();
        $producto = $productoModel->obtenerPorId($id);

        if ($producto):
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= htmlspecialchars($producto['nombre']) ?> - Detalles</title>
        <link rel="stylesheet" href="public/css/detalle.css" />
    </head>
    <body>
        <?php include_once 'app/views/navbar.php'; ?>
        <main class="detalle-container">
            <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
            <img src="public/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" width="300" />
            <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
            <p><strong>Categoría:</strong> <?= htmlspecialchars($producto['categoria']) ?></p>
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
            <p><strong>Vendedor:</strong> <?= htmlspecialchars($producto['vendedor']) ?></p>

            <?php if (isLoggedIn()): ?>
                <form method="POST" action="index.php">
                    <input type="hidden" name="id_producto" value="<?= htmlspecialchars($producto['id_producto']) ?>" />
                    <button type="submit" class="btn-primary">Reservar</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Inicia sesión</a> para poder reservar este producto.</p>
            <?php endif; ?>

            <a href="index.php" class="btn-secondary">Volver al Catálogo</a>
        </main>
    </body>
    </html>
    <?php
        else:
            echo "<p>Producto no encontrado.</p>";
        endif;
    } else {
        echo "<p>Falta el ID del producto.</p>";
    }

    exit; // No continúa con el catálogo
}

// Si NO es detalle ni reserva, mostrar catálogo normal
$buscar = $_GET['buscar'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$productoModel = new Producto();
$productos = $productoModel->obtenerTodos($buscar, $categoria);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bazar Online - Inicio</title>
    <link rel="stylesheet" href="public/css/index.css" />
</head>
<body>
    <?php include_once 'app/views/navbar.php'; ?>

    <main>
        <section class="welcome">
            <h1>Bienvenido a Bazar Online</h1>
            <p>Encuentra los mejores productos al mejor precio.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="registro.php" class="btn-primary">Registrarse</a>
            <?php endif; ?>
        </section>

        <section class="search-bar">
            <form method="GET" action="index.php">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Buscar por nombre..."
                    value="<?= htmlspecialchars($buscar) ?>"
                />
                <select name="categoria">
                    <option value="">Todas las categorías</option>
                    <option value="Accesorio" <?= ($categoria === 'Accesorio') ? 'selected' : '' ?>>Accesorio</option>
                    <option value="Libros y Papelería" <?= ($categoria === 'Libros y Papelería') ? 'selected' : '' ?>>Libros y Papelería</option>
                    <option value="Mascotas" <?= ($categoria === 'Mascotas') ? 'selected' : '' ?>>Mascotas</option>
                    <option value="Juguetes" <?= ($categoria === 'Juguetes') ? 'selected' : '' ?>>Juguetes</option>
                    <option value="Ropa y Moda" <?= ($categoria === 'Ropa y Moda') ? 'selected' : '' ?>>Ropa y Moda</option>
                    <option value="Salud y Belleza" <?= ($categoria === 'Salud y Belleza') ? 'selected' : '' ?>>Salud y Belleza</option>
                    <option value="Otros" <?= ($categoria === 'Otros') ? 'selected' : '' ?>>Otros</option>
                </select>
                <button type="submit">Buscar</button>
            </form>
        </section>

        <section class="product-grid">
            <?php if (empty($productos)): ?>
                <p>No hay productos publicados aún.</p>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card">
                        <img
                            src="public/img/<?= htmlspecialchars($producto['imagen']) ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                        />
                        <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <p>$<?= number_format($producto['precio'], 2) ?></p>
                        <small>Vendedor: <?= htmlspecialchars($producto['vendedor']) ?></small><br />
                        <a href="index.php?r=detalle&id=<?= $producto['id_producto'] ?>" class="btn-secondary">Ver más</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if (isLoggedIn()): ?>
            <div class="publicar-container">
                <a href="publicar_producto.php" class="btn-primary">Publicar Producto</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="footer-links">
            <a href="contacto.php">Contacto</a> |
            <a href="redes.php">Redes Sociales</a> |
            <a href="terminos.php">Términos de Uso</a>
        </div>
        <div>&copy; <?= date('Y') ?> Bazar Online</div>
    </footer>

    <script>
    document.querySelector('select[name="categoria"]').addEventListener('change', function() {
        this.form.submit();
    });
    </script>
</body>
</html>
