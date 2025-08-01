<?php
session_start(); // <-- Esto debe ir primero, siempre antes de usar $_SESSION

include 'navbar.php'; 

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['usuario'];

$mensajeExito = '';
if (isset($_SESSION['success'])) {
    $mensajeExito = $_SESSION['success'];
    unset($_SESSION['success']); // Limpiar para que no se repita
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil de Usuario - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/perfil.css" />
    
</head>
<body>
   
    <div class="profile-container">
        <h2>Perfil de Usuario</h2>
        <div class="user-info">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Rol:</strong> <?php echo htmlspecialchars($user['rol']); ?></p>
            <?php
            if (isset($_COOKIE['last_login'])):
                $lastLoginData = json_decode($_COOKIE['last_login'], true);
                if ($lastLoginData && isset($lastLoginData['user_id'], $lastLoginData['last_login']) && $lastLoginData['user_id'] == $user['id_usuario']):
            ?>
                <p><strong>Último inicio de sesión:</strong> <?php echo htmlspecialchars($lastLoginData['last_login']); ?></p>
            <?php
                endif;
            endif;
            ?>
        </div>

        <!-- Formulario para editar datos -->
        <h3>Editar Datos</h3>
        <form method="POST" action="../controllers/UsuarioController.php?action=editar" class="edit-form">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required />

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />

            <label for="password">Nueva Contraseña (dejar vacío para no cambiar):</label>
            <input type="password" id="password" name="password" />

            <label for="confirm_password">Confirmar Nueva Contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" />

            <button type="submit" class="btn-primary">Guardar Cambios</button>
        </form>


    <div class="actions">
        <?php if ($user['rol'] === 'vendedor'): ?>
            <a href="../controllers/PanelVendedorController.php" class="btn-primary">Publicar Producto</a>
        <?php else: ?>
            <button id="montarPuestoBtn" class="btn-primary">Montar Puesto</button>
        <?php endif; ?>
    </div>
    </div>

    <!-- Modal -->
    <div id="sellerPolicyModal" class="modal">
        <div class="modal-content">
            <h3>Políticas de Vendedor</h3>
            <p>Por favor, lea y acepte las políticas para convertirse en vendedor en Bazar Online.</p>
            <ul>
                <li>Debe cumplir con las normativas legales vigentes.</li>
                <li>Responsabilidad en la calidad y entrega de productos.</li>
                <li>Respetar las políticas de la plataforma y los usuarios.</li>
                <li>Otros términos y condiciones aplicables.</li>
            </ul>
            <div class="modal-buttons">
                <button class="btn-cancel" id="cancelBtn">Cancelar</button>
                <button class="btn-accept" id="acceptBtn">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('sellerPolicyModal');
        const btn = document.getElementById('montarPuestoBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const acceptBtn = document.getElementById('acceptBtn');

        btn.onclick = function() {
            modal.style.display = 'block';
        }

        cancelBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        acceptBtn.onclick = function() {
            // Send POST request to update role
            fetch('../controllers/UsuarioController.php?action=convertirVendedor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id_usuario=<?php echo urlencode($user['id_usuario']); ?>'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Error al actualizar el rol.');
            });
            modal.style.display = 'none';
        }
        
    </script>
    <?php if (!empty($mensajeExito)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $mensajeExito; ?>',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php endif; ?>

</body>
</html>
