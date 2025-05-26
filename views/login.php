<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="registration-box">
        <h1 class="registration-title">Iniciar Sesión</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="../Includes/auth.php" method="POST">
            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-input" placeholder="Introduce tu correo" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" placeholder="Introduce tu contraseña" required>
            </div>

            <button type="submit" class="login-btn">Iniciar Sesión</button>

            <div class="login-link">
                ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </form>
    </div>
</body>
</html>
