<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador') {
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
    $rol = $_POST['rol'];

    // Verificar si el correo ya existe
    $verificarStmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
    $verificarStmt->execute([$email]);
    $existe = $verificarStmt->fetchColumn();

    if ($existe) {
        $mensajeError = "El correo ya está registrado.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, email, contraseña, rol)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $primer_nombre,
                $segundo_nombre,
                $primer_apellido,
                $segundo_apellido,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $rol
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
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="../assets/css/editar_usuario.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Nuevo Usuario</h1>
            </header>

            <div class="content">
                <?php if (!empty($mensajeError)): ?>
                    <div class="error-message"><?= htmlspecialchars($mensajeError) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Primer Nombre</label>
                        <input type="text" name="primer_nombre" required>
                    </div>

                    <div class="form-group">
                        <label>Segundo Nombre</label>
                        <input type="text" name="segundo_nombre">
                    </div>

                    <div class="form-group">
                        <label>Primer Apellido</label>
                        <input type="text" name="primer_apellido" required>
                    </div>

                    <div class="form-group">
                        <label>Segundo Apellido</label>
                        <input type="text" name="segundo_apellido">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="contraseña" required>
                    </div>

                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" required>
                            <option value="administrador">Seleccionar...</option>
                            <option value="administrador">Administrador</option>
                            <option value="editor">Editor</option>
                            <option value="lector">Lector</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    <a href="../usuarios.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
