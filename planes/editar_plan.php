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
                <h1>Editar Plan Operativo</h1>
            </header>
        
            <div class="content">
                <form method="POST">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($plan['titulo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" required><?= htmlspecialchars($plan['descripcion']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Objetivo General</label>
                        <textarea name="objetivo_general" required><?= htmlspecialchars($plan['objetivo_general']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="año" value="<?= $plan['año'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Presupuesto</label>
                        <input type="number" name="presupuesto" step="0.01" value="<?= $plan['presupuesto'] ?>" required>
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

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="borrador" <?= $plan['estado'] === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                            <option value="en_revision" <?= $plan['estado'] === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                            <option value="aprobado" <?= $plan['estado'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Guardar Cambios</button>
                        <a href="../planes.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>