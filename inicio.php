<?php 
include("Includes/session.php");
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
                <!-- Filtros -->
                <div class="filters">
                    <div class="filter-group">
                        <label for="year-filter">Año:</label>
                        <select id="year-filter">
                            <option value="all" selected>Todos</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Estado:</label>
                        <select id="status-filter">
                            <option value="all">Todos</option>
                            <option value="draft">Borrador</option>
                            <option value="in_review">En revisión</option>
                            <option value="approved">Aprobado</option>
                        </select>
                    </div>
                </div>

                <div class="plans-container" id="plans-container">
                </div>
            </div>
        </main>

    <script src="assets/js/app.js"></script>
</body>
</html>