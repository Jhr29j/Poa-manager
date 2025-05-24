<?php
require '../Includes/session.php';
require '../Includes/db.php';

$plan_id = $_GET['plan_id'] ?? null;

if (!$plan_id) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no especificado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener información del plan
$stmt = $pdo->prepare("SELECT * FROM planes WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no encontrado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener actividades con presupuesto
$stmt = $pdo->prepare("SELECT id, nombre, presupuesto_asignado, gasto_real FROM actividades WHERE plan_id = ?");
$stmt->execute([$plan_id]);
$actividades = $stmt->fetchAll();

// Cálculos presupuestales
$total_asignado = array_sum(array_column($actividades, 'presupuesto_asignado'));
$total_gastado = array_sum(array_column($actividades, 'gasto_real'));
$disponible = $plan['presupuesto'] - $total_asignado;
$porcentaje_ejecucion = ($total_asignado > 0) ? ($total_gastado / $total_asignado) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control Presupuestal</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/presupuesto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Control Presupuestal: <?= htmlspecialchars($plan['titulo']) ?></h1>
            </header>

            <div class="content">
                <div class="resumen-presupuesto">
                    <div class="presupuesto-card">
                        <h3>Presupuesto Total</h3>
                        <p><?= number_format($plan['presupuesto'], 2) ?></p>
                    </div>
                    <div class="presupuesto-card">
                        <h3>Asignado a Actividades</h3>
                        <p><?= number_format($total_asignado, 2) ?></p>
                    </div>
                    <div class="presupuesto-card">
                        <h3>Disponible</h3>
                        <p class="<?= $disponible < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($disponible, 2) ?>
                        </p>
                    </div>
                    <div class="presupuesto-card">
                        <h3>Ejecución Financiera</h3>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje_ejecucion ?>%">
                                <?= round($porcentaje_ejecucion, 2) ?>%
                            </div>
                        </div>
                    </div>
                </div>

                <h2>Detalle por Actividad</h2>
                <table class="table-presupuesto">
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Presupuesto</th>
                            <th>Gasto Real</th>
                            <th>Diferencia</th>
                            <th>% Ejecución</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividades as $actividad): 
                            $diferencia = $actividad['presupuesto_asignado'] - $actividad['gasto_real'];
                            $porcentaje = ($actividad['presupuesto_asignado'] > 0) ? 
                                ($actividad['gasto_real'] / $actividad['presupuesto_asignado']) * 100 : 0;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($actividad['nombre']) ?></td>
                                <td><?= number_format($actividad['presupuesto_asignado'], 2) ?></td>
                                <td><?= number_format($actividad['gasto_real'], 2) ?></td>
                                <td class="<?= $diferencia < 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($diferencia, 2) ?>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%">
                                            <?= round($porcentaje, 2) ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>