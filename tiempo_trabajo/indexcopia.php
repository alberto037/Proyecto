<?php
require 'config.php';
require 'auth.php';
requireAuth();

// Obtener datos del usuario desde la sesión
$trabajador_id = $_SESSION['user']['trabajador_id'];
$nombre_usuario = $_SESSION['user']['nombre'] . ' ' . $_SESSION['user']['apellido'];
$rol_usuario = $_SESSION['user']['rol'];

// Resto de la lógica (proyectos, partes, tiempo, etc.)
$proyectos = $pdo->query("SELECT id, nombre FROM proyectos")->fetchAll();
$error = '';
$success = '';

// Procesar formulario (iniciar/pausar/reanudar/finalizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'])) {
    // ... (tu lógica existente para manejar el tiempo)
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Tiempo</title>
    <style>
        /* Estilos generales */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        
        /* Barra de navegación */
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .navbar a:hover { text-decoration: underline; }
        
        /* Contenedor principal */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Formulario y botones */
        .form-group { margin-bottom: 15px; }
        select, button { width: 100%; padding: 10px; }
        button { cursor: pointer; border: none; color: white; }
        .btn-iniciar { background: #2ecc71; }
        .btn-pausar { background: #f39c12; }
        .btn-finalizar { background: #e74c3c; }
        
        /* Mensajes */
        .error { color: #e74c3c; }
        .success { color: #2ecc71; }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <span><?= htmlspecialchars($nombre_usuario) ?> <small>(<?= $rol_usuario ?>)</small></span>
        <div>
            <?php if (hasRole('admin')): ?>
                <a href="admin/admin.php">Administración</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="container">
        <h1>Registro de Tiempo</h1>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="form-group">
                <label>Proyecto:</label>
                <select name="proyecto" required>
                    <option value="">Seleccione un proyecto</option>
                    <?php foreach ($proyectos as $proyecto): ?>
                        <option value="<?= $proyecto['id'] ?>"><?= htmlspecialchars($proyecto['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Botones de control de tiempo -->
            <div class="form-group">
                <button type="submit" name="iniciar" class="btn-iniciar">Iniciar</button>
                <button type="submit" name="pausar" class="btn-pausar">Pausar</button>
                <button type="submit" name="finalizar" class="btn-finalizar">Finalizar</button>
            </div>
        </form>
    </div>
</body>
</html>
