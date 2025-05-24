<?php
require 'db.php';
session_start();

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

// Verificar si el usuario está activo
if (isset($user['activo']) && !$user['activo']) {
    header("Location: ../views/login.php?error=Cuenta desactivada");
    exit;
}

// Guardar datos en sesión (actualizado con nuevos campos)
$_SESSION['usuario'] = [
    'id' => $user['id'],
    'primer_nombre' => $user['primer_nombre'],
    'segundo_nombre' => $user['segundo_nombre'],
    'primer_apellido' => $user['primer_apellido'],
    'email' => $user['email'],
    'rol' => $user['rol'],
    'telefono' => $user['telefono'] ?? null,
    'genero' => $user['genero'] ?? null
];

// Redirigir según rol
switch ($user['rol']) {
    case 'administrador':
        header("Location: ../inicio.php");
        break;
    case 'editor':
        header("Location: ../inicio.php");
        break;
    case 'lector':
        header("Location: ../inicio.php");
        break;
    default:
        header("Location: ../views/login.php?error=Rol no válido");
}
exit;
?>