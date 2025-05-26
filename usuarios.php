<?php 
require 'Includes/session.php';
require 'Includes/db.php';

$mensajes = [
    'actualizado' => ['texto' => '¡El usuario fue actualizado correctamente!', 'tipo' => 'success'],
    'eliminado' => ['texto' => 'El usuario fue eliminado correctamente.', 'tipo' => 'error'],
    'creado' => ['texto' => '¡Usuario creado exitosamente!', 'tipo' => 'success-created'],
    'error' => ['texto' => 'Ocurrió un error al procesar la solicitud.', 'tipo' => 'error'],
    'no_autorizado' => ['texto' => 'No tienes permisos para realizar esta acción.', 'tipo' => 'error']
];

$notificacion = null;
if (isset($_GET['estado']) && isset($mensajes[$_GET['estado']])) {
    $notificacion = $mensajes[$_GET['estado']];
} elseif (isset($_SESSION['notificacion'])) {
    $notificacion = $_SESSION['notificacion'];
    unset($_SESSION['notificacion']);
}

// Obtener ID del usuario actual
$usuarioActualId = $_SESSION['usuario']['id'] ?? null;
$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - POA Manager</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("Includes/sidebar.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Gestión de Usuarios</h1>
                <?php if ($esAdmin): ?>
                    <a href="views/add_user.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                <?php endif; ?>
            </header>

            <div class="content-container">
                <div class="usuarios-container">
                    <?php
                        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id != ? ORDER BY primer_nombre");
                        $stmt->execute([$usuarioActualId]);
                        while ($u = $stmt->fetch()):
                    ?>
                        <div class="usuario-card">
                            <div class="usuario-info">
                                <h3><?= htmlspecialchars($u['primer_nombre'] . ' ' . ($u['segundo_nombre'] ?? '') . ' ' . $u['primer_apellido'] . ' ' . ($u['segundo_apellido'] ?? '')) ?></h3>
                                <p><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
                                <p><strong>Rol:</strong> <?= ucfirst($u['rol']) ?>
                                    <?php if ($u['es_super_admin']): ?>
                                        <span class="super-admin-badge">Super Admin</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if ($esAdmin): ?>
                            <div class="usuario-actions">
                                <?php if (!$u['es_super_admin']): ?>
                                    <a href="views/edit_user.php?id=<?= $u['id'] ?>" class="btn-edit">
                                        <i class="fas fa-pen"></i> Editar
                                    </a>
                                    <a href="views/delete_user.php?id=<?= $u['id'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                <?php else: ?>
                                    <span class="protected-badge">Protegido</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>
    </div>

    <div class="notification-container" id="notification-container">
        <?php if ($notificacion): ?>
        <div class="notification <?= $notificacion['tipo'] ?>" id="php-notification">
            <span><?= $notificacion['texto'] ?></span>
            <button class="close-btn" onclick="this.parentElement.classList.add('hide')">
                &times;
            </button>
        </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>