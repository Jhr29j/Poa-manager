<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    $_SESSION['notificacion'] = ['texto' => 'No tienes permisos para esta acción', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

$plan_id = $_GET['plan_id'] ?? null;

if (!$plan_id) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no especificado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener información del plan
$stmt = $pdo->prepare("SELECT id, titulo, presupuesto FROM planes WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no encontrado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener responsables (usuarios con roles permitidos)
$responsables = $pdo->query("
    SELECT id, CONCAT(primer_nombre, ' ', primer_apellido) AS nombre 
    FROM usuarios 
    WHERE rol IN ('administrador', 'editor')
")->fetchAll();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $responsable_id = $_POST['responsable_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $presupuesto = (float)$_POST['presupuesto'];

    // Validar fechas
    if ($fecha_fin < $fecha_inicio) {
        $_SESSION['notificacion'] = ['texto' => 'La fecha de fin debe ser posterior a la de inicio', 'tipo' => 'error'];
    } else {
        // Insertar en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO actividades (
                plan_id, nombre, descripcion, responsable_id, 
                fecha_inicio, fecha_fin, presupuesto_asignado
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $plan_id, $nombre, $descripcion, $responsable_id,
            $fecha_inicio, $fecha_fin, $presupuesto
        ]);

        $_SESSION['notificacion'] = ['texto' => 'Actividad creada exitosamente', 'tipo' => 'success'];
        header("Location: actividades.php?plan_id=$plan_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Actividad</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/agregar_actividad.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Nueva Actividad para: <?= htmlspecialchars($plan['titulo']) ?></h1>
                <a href="actividades.php?plan_id=<?= $plan_id ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </header>

            <div class="content">
                <form method="POST" class="form-actividad">
                    <div class="form-group">
                        <label for="nombre">Nombre de la actividad*</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de inicio*</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_fin">Fecha de fin*</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="responsable_id">Responsable*</label>
                            <select id="responsable_id" name="responsable_id" required>
                                <?php foreach ($responsables as $responsable): ?>
                                    <option value="<?= $responsable['id'] ?>">
                                        <?= htmlspecialchars($responsable['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="presupuesto">Presupuesto asignado (USD)*</label>
                            <input type="number" id="presupuesto" name="presupuesto" step="0.01" min="0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Actividad
                    </button>
                </form>
            </div>
        </main>
    </div>

    <!-- Script para validar fechas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');

            fechaInicio.addEventListener('change', function() {
                fechaFin.min = this.value;
            });
        });
    </script>
</body>
</html>