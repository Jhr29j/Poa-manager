<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    $_SESSION['notificacion'] = [
        'texto' => 'No tienes permisos para eliminar actividades',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['notificacion'] = [
        'texto' => 'ID de actividad no especificado',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

$id = $_GET['id'];

// Obtener plan_id antes de eliminar para redireccionar
$stmt = $pdo->prepare("SELECT plan_id FROM actividades WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch();

if (!$actividad) {
    $_SESSION['notificacion'] = [
        'texto' => 'Actividad no encontrada',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

// Eliminar la actividad
try {
    $stmt = $pdo->prepare("DELETE FROM actividades WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['notificacion'] = [
            'texto' => 'Actividad eliminada correctamente',
            'tipo' => 'success'
        ];
    }
} catch (PDOException $e) {
    $_SESSION['notificacion'] = [
        'texto' => 'Error al eliminar la actividad: ' . $e->getMessage(),
        'tipo' => 'error'
    ];
}

header("Location: actividades.php?plan_id=" . $actividad['plan_id']);
exit;
?>