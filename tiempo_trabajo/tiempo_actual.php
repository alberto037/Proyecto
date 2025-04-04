<?php
require 'config.php';
header('Content-Type: application/json');

if (isset($_GET['tiempo_id'])) {
    $stmt = $pdo->prepare("
        SELECT 
            TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, inicio, NOW()) - tiempo_pausado), '%H:%i:%s') as tiempo,
            pausado as esta_pausado
        FROM tiempo WHERE id = ?
    ");
    $stmt->execute([$_GET['tiempo_id']]);
    echo json_encode($stmt->fetch());
}
?>
