<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'super_admin') {
    die("<p>No tienes permisos para acceder a esta página.</p>");
}

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'] ?? null;
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'] ?? null;
    $email = $_POST['email'];
    $password = $_POST['contraseña'];
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $genero = isset($_POST['genero']) ? $_POST['genero'] : null;
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $rol = $_POST['rol'];
    $es_super_admin = isset($_POST['es_super_admin']) ? 1 : 0;

    // Verificar si el correo ya existe
    $verificarStmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
    $verificarStmt->execute([$email]);
    $existe = $verificarStmt->fetchColumn();

    if ($existe) {
        $mensajeError = "El correo ya está registrado.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, email, contraseña, fecha_nacimiento, genero, telefono, rol, es_super_admin)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $primer_nombre,
                $segundo_nombre,
                $primer_apellido,
                $segundo_apellido,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $fecha_nacimiento,
                $genero,
                $telefono,
                $rol,
                $es_super_admin
            ]);

            $_SESSION['notificacion'] = ['texto' => 'Usuario creado correctamente!', 'tipo' => 'success'];
            header("Location: ../usuarios.php");
            exit;

        } catch (PDOException $e) {
            $mensajeError = "Error al crear el usuario: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="../assets/css/agregar_usuario.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <button class="mobile-header-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Nuevo Usuario</h1>
            </header>

            <div class="content">
                <?php if (!empty($mensajeError)): ?>
                    <div class="error-message"><?= htmlspecialchars($mensajeError) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="required-field"><i class="fas fa-user"></i> Primer Nombre *</label>
                            <input type="text" name="primer_nombre" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Segundo Nombre</label>
                            <input type="text" name="segundo_nombre">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="required-field"><i class="fas fa-user-tag"></i> Primer Apellido *</label>
                            <input type="text" name="primer_apellido" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> Segundo Apellido</label>
                            <input type="text" name="segundo_apellido">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-field"><i class="fas fa-mail-bulk"></i> Email *</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label class="required-field"><i class="fas fa-lock"></i> Contraseña *</label>
                        <input type="password" name="contraseña" required minlength="6">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento *</label>
                            <input type="date" name="fecha_nacimiento">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Género</label>
                            <select name="genero">
                                <option value="">Seleccionar...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="tel" name="telefono" placeholder="Ej: 8091234567">
                        </div>

                        <div class="form-group">
                            <label class="required-field"> <i class="fas fa-user-shield"></i> Rol *</label>
                            <select name="rol" required>
                                <option value="">Seleccionar...</option>
                                <option value="administrador">Administrador</option>
                                <option value="editor">Editor</option>
                                <option value="lector">Lector</option>
                            </select>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <label for="es_super_admin" class="checkbox-label checkbox-right"><i class="fas fa-crown"></i> Super Admin
                            <input type="checkbox" id="es_super_admin" name="es_super_admin" value="1">
                            <span class="custom-checkbox"></span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> <span class="action-text">Crear Usuario</span>
                        </button>
                        <a href="../usuarios.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i> <span class="action-text">Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Script para manejar el menú hamburguesa -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mobileHeaderToggle = document.querySelector('.mobile-header-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            
            // Toggle sidebar desde el botón del header
            if(mobileHeaderToggle) {
                mobileHeaderToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    if(sidebarOverlay) {
                        sidebarOverlay.classList.toggle('active');
                    }
                });
            }
            
            // Cerrar sidebar al hacer clic en el overlay
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>