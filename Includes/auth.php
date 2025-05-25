<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';
require 'db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header("Location: ../views/login.php?error=Faltan credenciales");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['contraseña'])) {
    header("Location: ../views/login.php?error=Credenciales incorrectas");
    exit;
}

// Verificar si está activo
if (isset($user['activo']) && !$user['activo']) {
    header("Location: ../views/login.php?error=Cuenta desactivada");
    exit;
}

// Actualizar último acceso
try {
    $update_stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
    $update_stmt->execute([$user['id']]);
} catch (PDOException $e) {
    error_log("Error actualizando último acceso: " . $e->getMessage());
}

// Obtener usuario actualizado
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user['id']]);
$user = $stmt->fetch();

// Guardar en sesión
$_SESSION['usuario'] = [
    'id' => $user['id'],
    'primer_nombre' => $user['primer_nombre'],
    'segundo_nombre' => $user['segundo_nombre'],
    'primer_apellido' => $user['primer_apellido'],
    'email' => $user['email'],
    'rol' => $user['rol'],
    'telefono' => $user['telefono'] ?? null,
    'genero' => $user['genero'] ?? null,
    'ultimo_acceso' => $user['ultimo_acceso'] ?? null
];

// Redirigir según rol
switch ($user['rol']) {
    case 'administrador':
    case 'editor':
    case 'lector':
        header("Location: ../inicio.php");
        break;
    default:
        header("Location: ../views/login.php?error=Rol no válido");
}
exit;
