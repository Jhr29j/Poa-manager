<?php
session_start();
require 'db.php';

// Configuración de rutas base
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/poa-manager/';
$views_path = $base_url . 'views/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Variables recibidas
    $primer_nombre = trim($_POST['primer_nombre']);
    $segundo_nombre = isset($_POST['segundo_nombre']) ? trim($_POST['segundo_nombre']) : null;
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = isset($_POST['segundo_apellido']) ? trim($_POST['segundo_apellido']) : null;
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $genero = isset($_POST['genero']) ? $_POST['genero'] : null;
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;

    // Validaciones
    if (empty($primer_nombre) || empty($primer_apellido) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Los campos obligatorios deben ser completados";
        header("Location: " . $views_path . "registro.php");
        exit;
    }
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden";
        header("Location: " . $views_path . "registro.php");
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El email no es válido";
        header("Location: " . $views_path . "registro.php");
        exit;
    }
    if ($fecha_nacimiento && strtotime($fecha_nacimiento) > strtotime('today')) {
        $_SESSION['error'] = "La fecha de nacimiento no puede ser futura";
        header("Location: " . $views_path . "registro.php");
        exit;
    }

    // Verificar email duplicado
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Este email ya está registrado";
        header("Location: " . $views_path . "registro.php");
        exit;
    }

    // Insertar usuario con rol por defecto
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios 
            (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
            email, contraseña, rol, fecha_nacimiento, genero, telefono) 
            VALUES (?, ?, ?, ?, ?, ?, 'lector', ?, ?, ?)");

        if ($stmt->execute([
            $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, 
            $email, $hashed_password, $fecha_nacimiento, $genero, $telefono
        ])) {
            // Obtener el id del usuario creado
            $user_id = $pdo->lastInsertId();

            // Consultar el usuario recién creado para asegurar los datos
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            // Crear sesión completa
            $_SESSION['usuario'] = [
                'id' => $user['id'],
                'primer_nombre' => $user['primer_nombre'],
                'segundo_nombre' => $user['segundo_nombre'],
                'primer_apellido' => $user['primer_apellido'],
                'segundo_apellido' => $user['segundo_apellido'],
                'email' => $user['email'],
                'rol' => $user['rol'], // Asegurando que sea 'lector'
                'fecha_nacimiento' => $user['fecha_nacimiento'],
                'genero' => $user['genero'],
                'telefono' => $user['telefono']
            ];

            $_SESSION['success'] = "Registro exitoso. Bienvenido, " . $user['primer_nombre'];
            header("Location: " . $base_url . "inicio.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar el usuario: " . $e->getMessage();
        header("Location: " . $views_path . "registro.php");
        exit;
    }
} else {
    header("Location: " . $views_path . "registro.php");
    exit;
}
?>