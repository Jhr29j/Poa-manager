<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    header("Location: ../planes.php?error=no_autorizado");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $objetivo = $_POST['objetivo_general'];
        $año = $_POST['año'];
        $presupuesto = $_POST['presupuesto'];
        $responsable_id = $_POST['responsable_id'];
        $estado = 'borrador';

        $stmt = $pdo->prepare("INSERT INTO planes 
            (titulo, descripcion, objetivo_general, año, presupuesto, responsable_id, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$titulo, $descripcion, $objetivo, $año, $presupuesto, $responsable_id, $estado])) {
            $_SESSION['notificacion'] = [
                'texto' => '¡Plan creado exitosamente!',
                'tipo' => 'success-created'
            ];
            header("Location: ../planes.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['notificacion'] = [
            'texto' => 'Error al crear el plan: ' . $e->getMessage(),
            'tipo' => 'error'
        ];
        header("Location: ../planes.php");
        exit;
    }
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
    <title>Crear Nuevo Plan</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/crear_plan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>
        <main class="main-content">
            <header class="header">
                <h1>Crear Nuevo Plan Operativo</h1>
            </header>
        
            <div class="content">
                <form method="POST">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Objetivo General</label>
                        <textarea name="objetivo_general" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="año" min="<?= date('Y') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Presupuesto</label>
                        <input type="number" name="presupuesto" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Responsable</label>
                        <select name="responsable_id" required>
                            <?php foreach ($responsables as $r): ?>
                                <option value="<?= $r['id'] ?>">
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Guardar Plan</button>
                        <a href="../planes.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>