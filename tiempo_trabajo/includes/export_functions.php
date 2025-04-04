<?php
function getTimeRecords($pdo, $filters) {
    $query = "SELECT [...]"; // Tu consulta SQL actual
    $stmt = $pdo->prepare($query);
    $stmt->execute($filters);
    return $stmt->fetchAll();
}

function exportToExcel($pdo, $filters) {
    $registros = getTimeRecords($pdo, $filters);
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=informe_tiempo_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Cabeceras
    fputcsv($output, [
        'Trabajador',
        'Proyecto', 
        'Parte',
        'Fecha Inicio',
        'Fecha Fin',
        'Tiempo (HH:MM:SS)',
        'Horas Decimales'
    ]);
    
    // Datos
    foreach ($registros as $reg) {
        fputcsv($output, [
            $reg['trabajador'],
            $reg['proyecto'],
            $reg['parte'],
            $reg['inicio'],
            $reg['fin'] ?? 'En progreso',
            gmdate("H:i:s", $reg['segundos']),
            round($reg['segundos']/3600, 2)
        ]);
    }
    
    fclose($output);
    exit;
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>
