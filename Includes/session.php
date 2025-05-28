<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Opcional: Actualizar último acceso periódicamente durante la sesión
if (isset($_SESSION['usuario']['id'])) {
    require 'db.php';
    $fecha_actual = date('Y-m-d H:i:s');
    
    // Actualizar en sesión
    $_SESSION['usuario']['ultimo_acceso'] = $fecha_actual;
    
    // Actualizar en base de datos (cada cierto tiempo, no en cada carga)
    if (!isset($_SESSION['last_db_update']) || time() - $_SESSION['last_db_update'] > 300) { // 5 minutos
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = ? WHERE id = ?");
            $stmt->execute([$fecha_actual, $_SESSION['usuario']['id']]);
            $_SESSION['last_db_update'] = time();
        } catch (PDOException $e) {
            error_log("Error al actualizar último acceso: " . $e->getMessage());
        }
    }
}
?>