<?php
// Iniciar sesión al principio
session_start();

// Rutas correctas para los includes
require_once __DIR__.'includes/config.php';
require_once __DIR__.'includes/db.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Método no permitido";
    header("Location: ".BASE_URL."views/registro.php");
    exit;
}

// Obtener y limpiar datos del formulario
$primer_nombre = trim($_POST['primer_nombre'] ?? '');
$segundo_nombre = isset($_POST['segundo_nombre']) ? trim($_POST['segundo_nombre']) : null;
$primer_apellido = trim($_POST['primer_apellido'] ?? '');
$segundo_apellido = isset($_POST['segundo_apellido']) ? trim($_POST['segundo_apellido']) : null;
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
$genero = $_POST['genero'] ?? null;
$telefono = isset($_POST['telefono']) ? preg_replace('/[^0-9]/', '', $_POST['telefono']) : null;

// Validaciones
$errores = [];

// Validar campos obligatorios
if (empty($primer_nombre)) $errores[] = "El primer nombre es obligatorio";
if (empty($primer_apellido)) $errores[] = "El primer apellido es obligatorio";
if (empty($email)) {
    $errores[] = "El email es obligatorio";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del email no es válido";
}

// Validar contraseña
if (empty($password)) {
    $errores[] = "La contraseña es obligatoria";
} elseif (strlen($password) < 8) {
    $errores[] = "La contraseña debe tener al menos 8 caracteres";
} elseif ($password !== $confirm_password) {
    $errores[] = "Las contraseñas no coinciden";
}

// Validar fecha de nacimiento
if ($fecha_nacimiento && strtotime($fecha_nacimiento) > time()) {
    $errores[] = "La fecha de nacimiento no puede ser futura";
}

// Si hay errores, redirigir al formulario
if (!empty($errores)) {
    $_SESSION['error'] = implode("<br>", $errores);
    $_SESSION['form_data'] = $_POST;
    header("Location: ".BASE_URL."views/registro.php");
    exit;
}

// Verificar email duplicado
try {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Este email ya está registrado";
        $_SESSION['form_data'] = $_POST;
        header("Location: ".BASE_URL."views/registro.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al verificar email: ".$e->getMessage());
    $_SESSION['error'] = "Error en el sistema. Por favor intente más tarde.";
    header("Location: ".BASE_URL."views/registro.php");
    exit;
}

// Registrar el nuevo usuario
try {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $rol = 'lector'; // Rol por defecto
    $activo = 1; // Usuario activo por defecto
    
    $stmt = $pdo->prepare("INSERT INTO usuarios 
        (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
        email, contraseña, rol, fecha_nacimiento, genero, telefono, activo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido,
        $email, $hashed_password, $rol, $fecha_nacimiento, $genero, $telefono, $activo
    ]);
    
    // Obtener el ID del nuevo usuario
    $user_id = $pdo->lastInsertId();
    
    // Obtener los datos completos del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Establecer sesión de usuario
    $_SESSION['usuario'] = [
        'id' => $user['id'],
        'primer_nombre' => $user['primer_nombre'],
        'segundo_nombre' => $user['segundo_nombre'],
        'primer_apellido' => $user['primer_apellido'],
        'segundo_apellido' => $user['segundo_apellido'],
        'email' => $user['email'],
        'rol' => $user['rol'],
        'fecha_nacimiento' => $user['fecha_nacimiento'],
        'genero' => $user['genero'],
        'telefono' => $user['telefono'],
        'activo' => $user['activo']
    ];
    
    // Redirigir al dashboard con mensaje de éxito
    $_SESSION['success'] = "Registro exitoso. ¡Bienvenido/a!";
    header("Location: ".BASE_URL."inicio.php");
    exit;
    
} catch (PDOException $e) {
    error_log("Error al registrar usuario: ".$e->getMessage());
    $_SESSION['error'] = "Error al registrar el usuario. Por favor intente nuevamente.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ".BASE_URL."views/registro.php");
    exit;
}
?>