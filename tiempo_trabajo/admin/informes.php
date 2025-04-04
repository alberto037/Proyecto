<?php
require '../config.php';
require '../auth.php';
requireRole('admin');

// Filtros por defecto (últimos 30 días)
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$proyecto_id = $_GET['proyecto_id'] ?? '';
$trabajador_id = $_GET['trabajador_id'] ?? '';

// Obtener datos para filtros
$proyectos = $pdo->query("SELECT id, nombre FROM proyectos")->fetchAll();
$trabajadores = $pdo->query("SELECT t.id, CONCAT(t.nombre, ' ', t.apellido) AS nombre FROM trabajadores t JOIN usuarios u ON t.id = u.trabajador_id")->fetchAll();

// Consulta base para informes
$query = "
    SELECT 
        t.nombre AS trabajador,
        p.nombre AS proyecto,
        pp.nombre AS parte,
        ti.inicio,
        ti.fin,
        SEC_TO_TIME(ti.tiempo_total) AS tiempo,
        ti.tiempo_total AS segundos
    FROM tiempo ti
    JOIN trabajadores t ON ti.trabajador_id = t.id
    JOIN proyectos p ON ti.proyecto_id = p.id
    JOIN partes_proyecto pp ON ti.parte_id = pp.id
    WHERE DATE(ti.inicio) BETWEEN :fecha_inicio AND :fecha_fin
";

// Aplicar filtros adicionales
$params = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
];

if (!empty($proyecto_id)) {
    $query .= " AND ti.proyecto_id = :proyecto_id";
    $params['proyecto_id'] = $proyecto_id;
}

if (!empty($trabajador_id)) {
    $query .= " AND ti.trabajador_id = :trabajador_id";
    $params['trabajador_id'] = $trabajador_id;
}

$query .= " ORDER BY ti.inicio DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$registros = $stmt->fetchAll();

// Totales calculados
$total_horas = array_sum(array_column($registros, 'segundos')) / 3600;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes de Tiempo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .filtros { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .total { font-weight: bold; background: #f2f2f2; }
        .btn { padding: 8px 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-export { background: #2ecc71; }
.navbar a { color: black; text-decoration: none; margin-left: 15px; }
    </style>
</head>
<body>
    <h1>Informes de Tiempo</h1>
    </div>    
    <div class="navbar">
        <div>
            <a href="../index.php">Inicio</a>
            <a href="../logout.php">Cerrar sesión</a>
        </div>
    </div>
    <!-- Filtros -->
    <div class="filtros">
        <form method="get">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                <div>
                    <label>Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control">
                </div>
                <div>
                    <label>Fecha Fin:</label>
                    <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" class="form-control">
                </div>
                <div>
                    <label>Proyecto:</label>
                    <select name="proyecto_id">
                        <option value="">Todos</option>
                        <?php foreach ($proyectos as $proy): ?>
                            <option value="<?= $proy['id'] ?>" <?= $proyecto_id == $proy['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($proy['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Trabajador:</label>
                    <select name="trabajador_id">
                        <option value="">Todos</option>
                        <?php foreach ($trabajadores as $tra): ?>
                            <option value="<?= $tra['id'] ?>" <?= $trabajador_id == $tra['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tra['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn" style="margin-top: 15px;">Aplicar Filtros</button>
            <a href="informes.php?exportar=excel&<?= http_build_query($_GET) ?>" class="btn btn-export" style="margin-top: 15px;">Exportar a Excel</a>
        </form>
    </div>

    <!-- Resultados -->
    <table>
        <thead>
            <tr>
                <th>Trabajador</th>
                <th>Proyecto</th>
                <th>Parte</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Tiempo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $reg): ?>
            <tr>
                <td><?= htmlspecialchars($reg['trabajador']) ?></td>
                <td><?= htmlspecialchars($reg['proyecto']) ?></td>
                <td><?= htmlspecialchars($reg['parte']) ?></td>
                <td><?= $reg['inicio'] ?></td>
                <td><?= $reg['fin'] ?? 'En progreso' ?></td>
                <td><?= $reg['tiempo'] ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="5">Total Horas</td>
                <td><?= round($total_horas, 2) ?> horas</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
