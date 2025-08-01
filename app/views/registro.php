<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../../public/css/registro.css" />
    <script>
        function validarFormulario() {
            const password = document.getElementById('password').value;
            const confirmar = document.getElementById('confirmar_password').value;
            if (password !== confirmar) {
                alert('Las contraseñas no coinciden.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <h2>Registro de Usuario</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="../controllers/UsuarioController.php?action=registro" method="POST" onsubmit="return validarFormulario();">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required pattern=".{3,}" title="Mínimo 3 caracteres" />

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required pattern=".{6,}" title="Mínimo 6 caracteres" />

        <label for="confirmar_password">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_password" name="confirmar_password" required />

        <button type="submit">Registrarse</button>
    </form>
    <button type="button" onclick="window.location.href='/Bazar_online/index.php';" class="btn-regresar">Regresar</button>
</body>
</html>
