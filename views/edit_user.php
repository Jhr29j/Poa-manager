<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador') {
    die("<p>No tienes permisos para acceder a esta página.</p>");
}

// Obtener el usuario a editar
if (!isset($_GET['id'])) {
    die("<p>Usuario no encontrado.</p>");
}

$id = $_GET['id'];
$usuarioActualId = $_SESSION['usuario']['id'] ?? null;

// Verificar si está intentando editarse a sí mismo
if ($id == $usuarioActualId) {
    $_SESSION['notificacion'] = ['texto' => 'No puedes editarte a ti mismo.', 'tipo' => 'error'];
    header("Location: ../usuarios.php");
    exit;
}

// Obtener información del usuario a editar
$stmt = $pdo->prepare("SELECT id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, rol, es_super_admin, fecha_nacimiento, genero, telefono 
                FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario_a_editar = $stmt->fetch();

if (!$usuario_a_editar) {
    die("<p>Usuario no encontrado.</p>");
}

// Verificar si es un super admin protegido
if ($usuario_a_editar['es_super_admin']) {
    $_SESSION['notificacion'] = ['texto' => 'No puedes editar a este usuario.', 'tipo' => 'error'];
    header("Location: ../usuarios.php");
    exit;
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'] ?? null;
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'] ?? null;
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $genero = $_POST['genero'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $rol = $_POST['rol'];
    $es_super_admin = isset($_POST['es_super_admin']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE usuarios SET 
            primer_nombre = ?, 
            segundo_nombre = ?, 
            primer_apellido = ?, 
            segundo_apellido = ?,
            fecha_nacimiento = ?,
            genero = ?,
            telefono = ?,
            rol = ?,
            es_super_admin = ?
            WHERE id = ?");

        $stmt->execute([
            $primer_nombre,
            $segundo_nombre,
            $primer_apellido,
            $segundo_apellido,
            $fecha_nacimiento,
            $genero,
            $telefono,
            $rol,
            $es_super_admin,
            $id
        ]);

        $pdo->commit();
        $_SESSION['notificacion'] = ['texto' => 'Usuario actualizado correctamente!', 'tipo' => 'success'];
        header("Location: ../usuarios.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al actualizar el usuario: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../assets/css/editar_usuario.css">
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
                <div class="header-content">
                    <h1>Editar Usuario</h1>
                </div>
            </header>

            <div class="content">
                <form method="POST" class="edit-user-form">
                    <input type="hidden" name="id" value="<?= $usuario_a_editar['id'] ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Primer Nombre *</label>
                            <input type="text" name="primer_nombre" value="<?= htmlspecialchars($usuario_a_editar['primer_nombre']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" value="<?= htmlspecialchars($usuario_a_editar['segundo_nombre'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> Primer Apellido *</label>
                            <input type="text" name="primer_apellido" value="<?= htmlspecialchars($usuario_a_editar['primer_apellido']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-tag"></i> Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" value="<?= htmlspecialchars($usuario_a_editar['segundo_apellido'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($usuario_a_editar['fecha_nacimiento'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario_a_editar['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="genero"><i class="fas fa-venus-mars"></i> Género</label>
                        <select name="genero" id="genero">
                            <option value="">Seleccionar...</option>
                            <option value="masculino" <?= ($usuario_a_editar['genero'] === 'masculino') ? 'selected' : '' ?>>Masculino</option>
                            <option value="femenino" <?= ($usuario_a_editar['genero'] === 'femenino') ? 'selected' : '' ?>>Femenino</option>
                            <option value="otro" <?= ($usuario_a_editar['genero'] === 'otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-shield"></i> Rol *</label>
                        <select name="rol" required>
                            <option value="administrador" <?= ($usuario_a_editar['rol'] === 'administrador') ? 'selected' : '' ?>>Administrador</option>
                            <option value="editor" <?= ($usuario_a_editar['rol'] === 'editor') ? 'selected' : '' ?>>Editor</option>
                            <option value="lector" <?= ($usuario_a_editar['rol'] === 'lector') ? 'selected' : '' ?>>Lector</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-group">
                        <label for="es_super_admin" class="checkbox-label checkbox-right">
                            <i class="fas fa-crown"></i> Super Admin
                            <input type="checkbox" id="es_super_admin" name="es_super_admin" value="1" <?= $usuario_a_editar['es_super_admin'] ? 'checked' : '' ?>>
                            <span class="custom-checkbox"></span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> <span>Actualizar Usuario</span>
                        </button>
                        <a href="../usuarios.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i> <span>Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Script para controlar el sidebar en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileHeaderToggle = document.querySelector('.mobile-header-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            
            // Toggle sidebar desde el botón del sidebar
            if(mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            // Toggle sidebar desde el botón del header
            if(mobileHeaderToggle) {
                mobileHeaderToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
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
    <script src="../assets/js/responsive.js"></script>
</body>
</html>