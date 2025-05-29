<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    $_SESSION['notificacion'] = [
        'texto' => 'No tienes permisos para editar planes',
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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $objetivo = $_POST['objetivo_general'];
        $año = $_POST['año'];
        $presupuesto = $_POST['presupuesto'];
        $responsable_id = $_POST['responsable_id'];
        $estado = $_POST['estado'];

        $stmt = $pdo->prepare("UPDATE planes SET 
            titulo = ?, descripcion = ?, objetivo_general = ?, año = ?, presupuesto = ?, responsable_id = ?, estado = ?
            WHERE id = ?");
        
        if ($stmt->execute([$titulo, $descripcion, $objetivo, $año, $presupuesto, $responsable_id, $estado, $id])) {
            $_SESSION['notificacion'] = [
                'texto' => '¡Plan actualizado correctamente!',
                'tipo' => 'success'
            ];
            header("Location: ../planes.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['notificacion'] = [
            'texto' => 'Error al actualizar el plan: ' . $e->getMessage(),
            'tipo' => 'error'
        ];
        header("Location: ../planes.php");
        exit;
    }
}

// Obtener datos del plan
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

// Obtener responsables
$responsables = $pdo->query("
    SELECT id, CONCAT(primer_nombre, ' ', primer_apellido) AS nombre 
    FROM usuarios 
    WHERE rol IN ('administrador', 'editor')
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plan</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/editar_plan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>
        <main class="main-content">
            <header class="header">
                <button class="mobile-header-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-content">
                    <h1>Editar Plan Operativo</h1>
                </div>
            </header>
        
            <div class="content">
                <form method="POST" class="edit-plan-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="titulo" value="<?= htmlspecialchars($plan['titulo']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Año</label>
                            <input type="number" name="año" value="<?= $plan['año'] ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" required><?= htmlspecialchars($plan['descripcion']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Objetivo General</label>
                        <textarea name="objetivo_general" required><?= htmlspecialchars($plan['objetivo_general']) ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Presupuesto</label>
                            <input type="number" name="presupuesto" step="0.01" value="<?= $plan['presupuesto'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado">
                                <option value="borrador" <?= $plan['estado'] === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                <option value="en_revision" <?= $plan['estado'] === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                                <option value="aprobado" <?= $plan['estado'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Responsable</label>
                        <select name="responsable_id" required>
                            <?php foreach ($responsables as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= $r['id'] == $plan['responsable_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> <span>Guardar Cambios</span>
                        </button>
                        <a href="../planes.php" class="btn btn-cancel">
                            <i class="fas fa-times"></i> <span>Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Script para controlar el sidebar en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileHeaderToggle = document.querySelector('.mobile-header-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            
            // Toggle sidebar desde el botón del sidebar
            if(mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            // Toggle sidebar desde el botón del header
            if(mobileHeaderToggle) {
                mobileHeaderToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            // Cerrar sidebar al hacer clic en el overlay
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });
    </script>
    <script src="../assets/js/responsive.js"></script>
</body>
</html>