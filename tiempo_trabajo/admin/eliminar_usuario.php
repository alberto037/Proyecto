<?php
require '../config.php';
require '../auth.php';
requireRole('admin');

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$usuario_id = $_GET['id'];

// Evitar que el admin principal se elimine a sí mismo
if ($usuario_id == 1 && $_SESSION['user']['id'] == 1) {
    $_SESSION['error'] = "No puedes eliminar al administrador principal";
    header('Location: admin.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);

header('Location: admin.php');
exit;
?>
