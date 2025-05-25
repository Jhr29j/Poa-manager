<?php 
include("Includes/session.php");
include("Includes/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Planes Operativos</title>
    <link rel="stylesheet" href="assets/css/Inicio.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/planes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("Includes/sidebar.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Planes Operativos Anuales</h1>
            </header>

            <div class="content">

                <!-- MÉTRICAS -->
                <?php
                    $usuarios_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuarios"))['total'];
                    $planes_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM planes_operativos"))['total'];
                    $actividades_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM actividades"))['total'];
                    $presupuesto_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(presupuesto) AS total FROM planes_operativos"))['total'];
                    $usuarios_recientes = mysqli_query($conn, "SELECT nombre, correo, fecha_registro FROM usuarios ORDER BY fecha_registro DESC LIMIT 5");
                    $top_planes = mysqli_query($conn, "SELECT nombre_plan, presupuesto FROM planes_operativos ORDER BY presupuesto DESC LIMIT 3");
                ?>

                <div class="dashboard-grid">
                    <div class="card"><h2><?= $usuarios_total ?></h2><p>Usuarios Totales</p></div>
                    <div class="card"><h2><?= $planes_total ?></h2><p>Planes Operativos</p></div>
                    <div class="card"><h2><?= $actividades_total ?></h2><p>Actividades Totales</p></div>
                    <div class="card"><h2>$<?= number_format($presupuesto_total, 2) ?></h2><p>Presupuesto Total</p></div>
                </div>

                <!-- USUARIOS RECIENTES -->
                <h3 class="section-title">Usuarios Recientes</h3>
                <table class="dashboard-table">
                    <thead>
                        <tr><th>Nombre</th><th>Email</th><th>Fecha y Hora</th></tr>
                    </thead>
                    <tbody>
                        <?php while($u = mysqli_fetch_assoc($usuarios_recientes)): ?>
                        <tr>
                            <td><?= $u['nombre'] ?></td>
                            <td><?= $u['correo'] ?></td>
                            <td><?= $u['fecha_registro'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- PLANES DESTACADOS -->
                <h3 class="section-title">Top 3 Planes con Mayor Presupuesto</h3>
                <ul class="top-planes">
                    <?php while($p = mysqli_fetch_assoc($top_planes)): ?>
                    <li><strong><?= $p['nombre_plan'] ?></strong> - $<?= number_format($p['presupuesto'], 2) ?></li>
                    <?php endwhile; ?>
                </ul>

            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
