<?php
require 'db.php';
require '../vendor/autoload.php'; // Para librerías como PhpSpreadsheet y Dompdf

class ReportGenerator {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Generar reporte PDF de un plan
    public function generarPDF($plan_id) {
        $plan = $this->getPlanData($plan_id);
        $actividades = $this->getActividades($plan_id);
        
        $html = $this->generateHTML($plan, $actividades);
        
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    // Generar reporte Excel
    public function generarExcel($plan_id) {
        $plan = $this->getPlanData($plan_id);
        $actividades = $this->getActividades($plan_id);
        
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar contenido del Excel
        $sheet->setCellValue('A1', 'Plan Operativo: ' . $plan['titulo']);
        // ... (resto de la configuración)
        
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    private function getPlanData($plan_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM planes WHERE id = ?");
        $stmt->execute([$plan_id]);
        return $stmt->fetch();
    }

    private function getActividades($plan_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM actividades WHERE plan_id = ?");
        $stmt->execute([$plan_id]);
        return $stmt->fetchAll();
    }

    private function generateHTML($plan, $actividades) {
        // Generar HTML para el PDF
        ob_start();
        include 'templates/reporte_plan.php';
        return ob_get_clean();
    }
}