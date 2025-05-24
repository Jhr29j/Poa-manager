<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador') {
    die("<p>No tienes permisos para acceder a esta página.</p>");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'] ?? null;
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'] ?? null;
    $rol = $_POST['rol'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE usuarios SET 
            primer_nombre = ?, 
            segundo_nombre = ?, 
            primer_apellido = ?, 
            segundo_apellido = ?, 
            rol = ? 
            WHERE id = ?");

        $stmt->execute([
            $primer_nombre,
            $segundo_nombre,
            $primer_apellido,
            $segundo_apellido,
            $rol,
            $id
        ]);

        // Si se edita a sí mismo, actualizar sesión
        if ($_SESSION['usuario']['id'] == $id) {
            $_SESSION['usuario']['primer_nombre'] = $primer_nombre;
            $_SESSION['usuario']['segundo_nombre'] = $segundo_nombre;
            $_SESSION['usuario']['primer_apellido'] = $primer_apellido;
            $_SESSION['usuario']['segundo_apellido'] = $segundo_apellido;
            $_SESSION['usuario']['rol'] = $rol;
        }

        $pdo->commit();
        $_SESSION['notificacion'] = ['texto' => 'Usuario actualizado correctamente!', 'tipo' => 'success'];
        header("Location: ../usuarios.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al actualizar el usuario: " . $e->getMessage());
    }
}

// Obtener el usuario a editar
if (!isset($_GET['id'])) {
    die("<p>Usuario no encontrado.</p>");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, rol FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario_a_editar = $stmt->fetch();

if (!$usuario_a_editar) {
    die("<p>Usuario no encontrado.</p>");
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
                <h1>Editar Usuario</h1>
            </header>

            <div class="content">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $usuario_a_editar['id'] ?>">

                    <div class="form-group">
                        <label>Primer Nombre</label>
                        <input type="text" name="primer_nombre" value="<?= htmlspecialchars($usuario_a_editar['primer_nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Segundo Nombre</label>
                        <input type="text" name="segundo_nombre" value="<?= htmlspecialchars($usuario_a_editar['segundo_nombre'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Primer Apellido</label>
                        <input type="text" name="primer_apellido" value="<?= htmlspecialchars($usuario_a_editar['primer_apellido']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" value="<?= htmlspecialchars($usuario_a_editar['segundo_apellido'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" required>
                            <option value="administrador" <?= ($usuario_a_editar['rol'] === 'administrador') ? 'selected' : '' ?>>Administrador</option>
                            <option value="editor" <?= ($usuario_a_editar['rol'] === 'editor') ? 'selected' : '' ?>>Editor</option>
                            <option value="lector" <?= ($usuario_a_editar['rol'] === 'lector') ? 'selected' : '' ?>>Lector</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Actualizar Usuario</button>
                        <a href="../usuarios.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
