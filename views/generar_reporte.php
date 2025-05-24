<?php
require '../Includes/session.php';
require '../Includes/db.php';
require '../vendor/autoload.php';

$plan_id = $_GET['plan_id'] ?? null;
$tipo = $_GET['tipo'] ?? 'pdf';

if (!$plan_id) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no especificado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener información del plan
$stmt = $pdo->prepare("SELECT p.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
                        FROM planes p 
                        JOIN usuarios u ON u.id = p.responsable_id 
                        WHERE p.id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no encontrado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener actividades del plan
$stmt = $pdo->prepare("SELECT a.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
                        FROM actividades a 
                        JOIN usuarios u ON u.id = a.responsable_id 
                        WHERE a.plan_id = ? 
                        ORDER BY a.fecha_inicio");
$stmt->execute([$plan_id]);
$actividades = $stmt->fetchAll();

// Generar reporte según el tipo solicitado
switch ($tipo) {
    case 'excel':
        generarExcel($plan, $actividades);
        break;
    case 'word':
        generarWord($plan, $actividades);
        break;
    default:
        generarPDF($plan, $actividades);
}

function generarPDF($plan, $actividades) {
    $html = obtenerHTMLReporte($plan, $actividades);
    
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="reporte_plan_' . $plan['id'] . '.pdf"');
    echo $dompdf->output();
    exit;
}

function generarExcel($plan, $actividades) {
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar contenido del Excel
    $sheet->setCellValue('A1', 'Reporte del Plan: ' . $plan['titulo']);
    $sheet->setCellValue('A2', 'Descripción: ' . $plan['descripcion']);
    $sheet->setCellValue('A3', 'Responsable: ' . $plan['responsable']);
    $sheet->setCellValue('A4', 'Presupuesto: ' . number_format($plan['presupuesto'], 2));
    
    // Encabezados de tabla
    $sheet->setCellValue('A6', 'Actividad');
    $sheet->setCellValue('B6', 'Responsable');
    $sheet->setCellValue('C6', 'Fecha Inicio');
    $sheet->setCellValue('D6', 'Fecha Fin');
    $sheet->setCellValue('E6', 'Presupuesto');
    $sheet->setCellValue('F6', 'Estado');
    
    // Datos de actividades
    $row = 7;
    foreach ($actividades as $actividad) {
        $sheet->setCellValue('A' . $row, $actividad['nombre']);
        $sheet->setCellValue('B' . $row, $actividad['responsable']);
        $sheet->setCellValue('C' . $row, $actividad['fecha_inicio']);
        $sheet->setCellValue('D' . $row, $actividad['fecha_fin']);
        $sheet->setCellValue('E' . $row, $actividad['presupuesto_asignado']);
        $sheet->setCellValue('F' . $row, ucfirst(str_replace('_', ' ', $actividad['estado'])));
        $row++;
    }
    
    // Autoajustar columnas
    foreach (range('A', 'F') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Generar y descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="reporte_plan_' . $plan['id'] . '.xlsx"');
    
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function generarWord($plan, $actividades) {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    
    // Título
    $section->addText('Reporte del Plan: ' . $plan['titulo'], ['bold' => true, 'size' => 16]);
    $section->addTextBreak(1);
    
    // Información básica
    $section->addText('Descripción: ' . $plan['descripcion']);
    $section->addText('Responsable: ' . $plan['responsable']);
    $section->addText('Presupuesto: ' . number_format($plan['presupuesto'], 2));
    $section->addTextBreak(2);
    
    // Tabla de actividades
    $table = $section->addTable();
    $table->addRow();
    $table->addCell()->addText('Actividad', ['bold' => true]);
    $table->addCell()->addText('Responsable', ['bold' => true]);
    $table->addCell()->addText('Fechas', ['bold' => true]);
    $table->addCell()->addText('Presupuesto', ['bold' => true]);
    $table->addCell()->addText('Estado', ['bold' => true]);
    
    foreach ($actividades as $actividad) {
        $table->addRow();
        $table->addCell()->addText($actividad['nombre']);
        $table->addCell()->addText($actividad['responsable']);
        $table->addCell()->addText($actividad['fecha_inicio'] . ' a ' . $actividad['fecha_fin']);
        $table->addCell()->addText(number_format($actividad['presupuesto_asignado'], 2));
        $table->addCell()->addText(ucfirst(str_replace('_', ' ', $actividad['estado'])));
    }
    
    // Generar y descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="reporte_plan_' . $plan['id'] . '.docx"');
    
    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save('php://output');
    exit;
}

function obtenerHTMLReporte($plan, $actividades) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte del Plan <?= $plan['titulo'] ?></title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #2c3e50; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
        </style>
    </head>
    <body>
        <h1>Reporte del Plan: <?= $plan['titulo'] ?></h1>
        <p><strong>Descripción:</strong> <?= $plan['descripcion'] ?></p>
        <p><strong>Responsable:</strong> <?= $plan['responsable'] ?></p>
        <p><strong>Presupuesto:</strong> <?= number_format($plan['presupuesto'], 2) ?></p>
        
        <h2>Actividades</h2>
        <table>
            <tr>
                <th>Actividad</th>
                <th>Responsable</th>
                <th>Fechas</th>
                <th class="text-right">Presupuesto</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($actividades as $actividad): ?>
            <tr>
                <td><?= $actividad['nombre'] ?></td>
                <td><?= $actividad['responsable'] ?></td>
                <td><?= $actividad['fecha_inicio'] ?> a <?= $actividad['fecha_fin'] ?></td>
                <td class="text-right"><?= number_format($actividad['presupuesto_asignado'], 2) ?></td>
                <td><?= ucfirst(str_replace('_', ' ', $actividad['estado'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </body>
    </html>
    <?php
    return ob_get_clean();
}