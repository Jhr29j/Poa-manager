<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    $_SESSION['notificacion'] = [
        'texto' => 'No tienes permisos para eliminar planes',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['notificacion'] = [
        'texto' => 'ID de plan no especificado',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

$id = $_GET['id'];

// Verificar que el plan existe
$stmt = $pdo->prepare("SELECT * FROM planes WHERE id = ?");
$stmt->execute([$id]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['notificacion'] = [
        'texto' => 'Plan no encontrado',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

// Eliminar el plan
try {
    $stmt = $pdo->prepare("DELETE FROM planes WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['notificacion'] = [
            'texto' => 'Plan eliminado correctamente',
            'tipo' => 'error' // Usamos 'error' como tipo pero es rojo
        ];
    }
} catch (PDOException $e) {
    $_SESSION['notificacion'] = [
        'texto' => 'Error al eliminar el plan: ' . $e->getMessage(),
        'tipo' => 'error'
    ];
}

header("Location: ../planes.php");
exit;