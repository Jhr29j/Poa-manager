<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Actualizar último acceso cada 5 minutos
if (isset($_SESSION['usuario']['id'])) {
    require_once 'db.php';

    if (!isset($_SESSION['ultimo_acceso_actualizado']) || 
        (time() - $_SESSION['ultimo_acceso_actualizado']) > 300) {
        
        $fecha_actual = date('Y-m-d H:i:s');
        $_SESSION['usuario']['ultimo_acceso'] = $fecha_actual;

        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = ? WHERE id = ?");
            $stmt->execute([$fecha_actual, $_SESSION['usuario']['id']]);
            $_SESSION['ultimo_acceso_actualizado'] = time();
        } catch (PDOException $e) {
            error_log("Error al actualizar último acceso: " . $e->getMessage());
        }
    }
}
?>