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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Bazar Online</title>
    <link rel="stylesheet" href="../../public/css/login.css" />
    <style>
      .quote-container {
        background-color: #f0f8ff;
        border-left: 5px solid #007bff;
        padding: 15px;
        margin-bottom: 20px;
        font-style: italic;
        font-size: 1.1em;
        color: #333;
      }
      blockquote {
        margin: 0;
      }
      footer {
        text-align: right;
        font-weight: bold;
        margin-top: 10px;
      }
      #btn-refresh {
        margin-bottom: 20px;
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
      }
    </style>
</head>
<body>
    
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <button id="btn-refresh">Obtener nueva cita traducida</button>

        <div id="quote-area" class="quote-container" style="display:none;">
            <blockquote id="quote-text"></blockquote>
            <footer id="quote-author"></footer>
            <hr />
            <blockquote id="quote-text-es" style="color: #004080;"></blockquote>
            <footer id="quote-author-es" style="text-align:left; font-weight: normal;"></footer>
        </div>

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
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const quoteArea = document.getElementById("quote-area");
    const quoteText = document.getElementById("quote-text");
    const quoteAuthor = document.getElementById("quote-author");
    const quoteTextEs = document.getElementById("quote-text-es");
    const quoteAuthorEs = document.getElementById("quote-author-es");
    const btnRefresh = document.getElementById("btn-refresh");

    async function cargarCita() {
        try {
            const resQuote = await fetch("https://api.quotable.io/random");
            const dataQuote = await resQuote.json();
            const originalText = dataQuote.content;
            const author = dataQuote.author;

            quoteText.textContent = `"${originalText}"`;
            quoteAuthor.textContent = `- ${author}`;

            const resTrans = await fetch("https://de.libretranslate.com/translate", {
                method: "POST",
                body: JSON.stringify({
                    q: originalText,
                    source: "en",
                    target: "es",
                    format: "text",
                    api_key: "" // Puedes dejarlo vacío
                }),
                headers: { "Content-Type": "application/json" }
            });

            const translated = await resTrans.json();

            if (translated && translated.translatedText) {
                quoteTextEs.textContent = `"${translated.translatedText}"`;
                quoteAuthorEs.textContent = `Traducido por LibreTranslate`;
            } else {
                quoteTextEs.textContent = "No se pudo traducir la cita.";
                quoteAuthorEs.textContent = "";
            }

            quoteArea.style.display = "block";
        } catch (e) {
            console.error("Error al traducir o mostrar la cita:", e);
            quoteArea.style.display = "block";
            quoteText.textContent = "No se pudo obtener la cita.";
            quoteTextEs.textContent = "Error al traducir.";
        }
    }

    btnRefresh.addEventListener("click", cargarCita);
    cargarCita(); // Llamada inicial
});
</script>

</body>
</html>
