<?php
require '../config.php';
require '../auth.php';
requireRole('admin');

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$usuario_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $rol = $_POST['rol'];
    
    // Actualizar solo si la contraseña no está vacía
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, password_hash = ?, rol = ? WHERE id = ?");
        $stmt->execute([$username, $password, $rol, $usuario_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, rol = ? WHERE id = ?");
        $stmt->execute([$username, $rol, $usuario_id]);
    }
    
    header('Location: admin.php');
    exit;
}

// Obtener datos del usuario
$stmt = $pdo->prepare("
    SELECT u.*, t.nombre, t.apellido 
    FROM usuarios u 
    JOIN trabajadores t ON u.trabajador_id = t.id 
    WHERE u.id = ?
");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; }
        .btn { padding: 8px 15px; background: #3498db; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Usuario: <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h1>
        
        <form method="post">
            <div class="form-group">
                <label>Nombre de usuario:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nueva contraseña (dejar vacío para no cambiar):</label>
                <input type="password" name="password">
            </div>
            
            <div class="form-group">
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                    <option value="supervisor" <?= $usuario['rol'] === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
