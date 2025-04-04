<?php
require '../config.php';
require '../auth.php';
requireRole('admin');

// Obtener lista de usuarios
$usuarios = $pdo->query("
    SELECT u.*, t.nombre, t.apellido
    FROM usuarios u
    JOIN trabajadores t ON u.trabajador_id = t.id
")->fetchAll();

// Obtener lista de trabajadores sin usuario
$trabajadores_sin_usuario = $pdo->query("
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
    <title>Panel de Administración</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f2f2f2; }
        .btn { padding: 5px 10px; color: white; text-decoration: none; border-radius: 3px; }
        .btn-edit { background: #3498db; }
        .btn-delete { background: #e74c3c; }
        .btn-add { background: #2ecc71; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Panel de Administración</span>
        <div>
            <a href="../index.php">Inicio</a>
	    <a href="informes.php">Informe</a>
            <a href="../logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <h1>Gestión de Usuarios</h1>
        
        <a href="crear_usuario.php" class="btn btn-add">+ Crear Usuario</a>
        <a href="proyectos/crear.php" class="btn btn-add">+ Crear Proyecto</a>
        <a href="proyectos/editar.php" class="btn btn-add">+ Editar Proyecto</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Trabajador</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id'] ?></td>
		    <td><?= htmlspecialchars($usuario['nombre']) . ' ' . htmlspecialchars($usuario['apellido']) ?></td>
                    <td><?= htmlspecialchars($usuario['username']) ?></td>
                    <td><?= $usuario['rol'] ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-delete"
                           onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
