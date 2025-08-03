<?php
session_start();
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
// Incluir la clase QuotableService
require_once __DIR__ . '/../../public/services/QuotableService.php';
// Crear instancia del servicio y obtener cita traducida
$quotableService = new QuotableService();
$quoteData = $quotableService->getRandomQuoteInSpanish();

$quote = $quoteData['quote'] ?? '';
$author = $quoteData['author'] ?? '';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/login.css" />
</head>
<body>
    
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

         <?php if ($quote): ?>
            <div class="quote-container">
                <blockquote>"<?php echo htmlspecialchars($quote); ?>"</blockquote>
                <footer>- <?php echo htmlspecialchars($author); ?></footer>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="../controllers/UsuarioController.php?action=login">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required />
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required />
            <button type="submit">Entrar</button>
        </form>

        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        <button type="button" onclick="window.location.href='/Bazar_online/index.php';" class="btn-regresar">Regresar</button>
    </div>

</body>
</html>
