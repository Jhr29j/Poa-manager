<?php
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Actualizar último acceso periódicamente
if (isset($_SESSION['usuario']['id'])) {
    require 'db.php';
    
    // Solo actualizar si han pasado más de 5 minutos
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