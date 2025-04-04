<?php
require '../config.php';
require '../auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trabajador_id = $_POST['trabajador_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    
    $stmt = $pdo->prepare("INSERT INTO usuarios (trabajador_id, username, password_hash, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$trabajador_id, $username, $password, $rol]);
    
    header('Location: admin.php');
    exit;
}

$trabajadores = $pdo->query("
    SELECT t.id, t.nombre, t.apellido 
    FROM trabajadores t 
    LEFT JOIN usuarios u ON t.id = u.trabajador_id 
    WHERE u.id IS NULL
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; }
        .btn { padding: 8px 15px; background: #2ecc71; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Usuario</h1>
        
        <form method="post">
            <div class="form-group">
                <label>Trabajador:</label>
                <select name="trabajador_id" required>
                    <?php foreach ($trabajadores as $t): ?>
                        <option value="<?= $t['id'] ?>">
                            <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nombre de usuario:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="empleado">Empleado</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Crear Usuario</button>
        </form>
    </div>
</body>
</html>
