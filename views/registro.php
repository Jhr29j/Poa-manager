<?php
session_start();
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registro de Usuario</title>
        <link rel="stylesheet" href="../assets/css/registro.css">
    </head>
    <body>
        <div class="registration-box">
            <h1 class="registration-title">Crear Cuenta</h1>


            <?php if ($error): ?>
                <div class="notification error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="notification success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="../Includes/registrar_usuario.php" method="POST" onsubmit="return validarFormulario()">
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container"></span>
                        Primer Nombre
                    </label>
                    <input type="text" class="form-input" placeholder="Ingrese su primer nombre" name="primer_nombre">
                </div>
    
                <!-- Segundo Nombre -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container checked"></span>
                        Segundo Nombre
                    </label>
                    <input type="text" class="form-input" placeholder="Ingrese su segundo nombre" name="segundo_nombre">
                </div>
    
                <!-- Apellidos -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <span class="checkbox-container"></span>
                            Primer Apellido
                        </label>
                        <input type="text" class="form-input" placeholder="Ingrese su primer apellido" name="primer_apellido">
                    </div>
    
                    <div class="form-group">
                        <label class="form-label">
                            <span class="checkbox-container checked"></span>
                            Segundo Apellido
                        </label>
                        <input type="text" class="form-input" placeholder="Ingrese su segundo apellido" name="segundo_apellido">
                        </div>
                </div>
    
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container"></span>
                        Email
                    </label>
                    <input type="email" class="form-input" placeholder="Ingrese su email" name="email">
                </div>
    
                <!-- Contraseñas -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <span class="checkbox-container checked"></span>
                            Contraseña
                        </label>
                        <input type="password" class="form-input" placeholder="Cree una contraseña" name="password">
                    </div>
    
                    <div class="form-group">
                        <label class="form-label">
                            <span class="checkbox-container"></span>
                            Confirmar Contraseña
                        </label>
                        <input type="password" class="form-input" placeholder="Repita su contraseña" name="confirm_password">
                    </div>
                </div>
    
                <!-- Fecha de Nacimiento -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container"></span>
                        Fecha de Nacimiento
                    </label>
                    <input type="date" class="form-input" name="fecha_nacimiento">
                </div>
    
                <!-- Género -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container"></span>
                        Género
                    </label>
                    <div class="custom-select">
                        <select class="form-input" name="genero">
                            <option>Seleccionar...</option>
                            <option>Masculino</option>
                            <option>Femenino</option>
                            <option>Otro</option>
                        </select>
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="form-label">
                        <span class="checkbox-container checked"></span>
                        Teléfono
                    </label>
                    <input type="tel" class="form-input" placeholder="Ingrese su teléfono" name="telefono">
                </div>
    
                <button type="submit" class="register-btn">Registrarse</button>
    
                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
                </div>
            </form>
        </div>

        <script>
        function validarFormulario() {
            const pass = document.querySelector('input[name="password"]').value;
            const confirm = document.querySelector('input[name="confirm_password"]').value;
            if (pass !== confirm) {
                alert('Las contraseñas no coinciden.');
                return false;
            }
            return true;
}
</script>

    </body>
</html>