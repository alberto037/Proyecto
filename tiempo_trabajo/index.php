<?php
require 'config.php';
require 'auth.php';
requireAuth();

// Obtener datos del usuario desde la sesión
$trabajador_id = $_SESSION['user']['trabajador_id'];
$nombre_usuario = $_SESSION['user']['nombre'] . ' ' . $_SESSION['user']['apellido'];
$rol_usuario = $_SESSION['user']['rol'];

// Inicializar variables
$proyectos = $pdo->query("SELECT id, nombre FROM proyectos")->fetchAll();
$partes = [];
$error = '';
$success = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $error = 'Token CSRF inválido';
    } else {
        if (isset($_POST['iniciar'])) {
            $proyecto_id = $_POST['proyecto'];
            $parte_id = $_POST['parte'];
            
            // Verificar acceso a la parte
            $stmt = $pdo->prepare("SELECT 1 FROM acceso_partes WHERE trabajador_id = ? AND parte_id = ?");
            $stmt->execute([$trabajador_id, $parte_id]);
            
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO tiempo (trabajador_id, proyecto_id, parte_id, inicio) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$trabajador_id, $proyecto_id, $parte_id]);
                $_SESSION['tiempo_id'] = $pdo->lastInsertId();
                $success = "Contador iniciado correctamente.";
            } else {
                $error = "No tienes acceso a esta parte del proyecto.";
            }
        } 
        elseif (isset($_POST['pausar'])) {
            if (isset($_SESSION['tiempo_id'])) {
                $stmt = $pdo->prepare("UPDATE tiempo SET pausado = TRUE, ultima_pausa = NOW() WHERE id = ?");
                $stmt->execute([$_SESSION['tiempo_id']]);
                $success = "Tiempo pausado correctamente.";
            }
        }
        elseif (isset($_POST['reanudar'])) {
            if (isset($_SESSION['tiempo_id'])) {
                $stmt = $pdo->prepare("UPDATE tiempo SET pausado = FALSE, tiempo_pausado = tiempo_pausado + TIMESTAMPDIFF(SECOND, ultima_pausa, NOW()), ultima_pausa = NULL WHERE id = ?");
                $stmt->execute([$_SESSION['tiempo_id']]);
                $success = "Tiempo reanudado correctamente.";
            }
        }
        elseif (isset($_POST['finalizar'])) {
            if (isset($_SESSION['tiempo_id'])) {
                $stmt = $pdo->prepare("UPDATE tiempo SET fin = NOW(), tiempo_total = TIMESTAMPDIFF(SECOND, inicio, NOW()) - tiempo_pausado WHERE id = ?");
                $stmt->execute([$_SESSION['tiempo_id']]);
                
                // Obtener tiempo registrado
                $stmt = $pdo->prepare("SELECT tiempo_total FROM tiempo WHERE id = ?");
                $stmt->execute([$_SESSION['tiempo_id']]);
                $tiempo_total = $stmt->fetchColumn();
                
                $success = "Tiempo registrado: " . gmdate("H:i:s", $tiempo_total);
                unset($_SESSION['tiempo_id']);
            }
        }
    }
}

// Obtener partes si se seleccionó proyecto
if (isset($_POST['proyecto'])) {
    $proyecto_id = $_POST['proyecto'];
    
    $stmt = $pdo->prepare("
        SELECT pp.id, pp.nombre 
        FROM partes_proyecto pp
        JOIN acceso_partes ap ON pp.id = ap.parte_id
        WHERE pp.proyecto_id = ? AND ap.trabajador_id = ?
    ");
    $stmt->execute([$proyecto_id, $trabajador_id]);
    $partes = $stmt->fetchAll();
}

// Verificar estado actual del tiempo
$tiempo_activo = null;
if (isset($_SESSION['tiempo_id'])) {
    $stmt = $pdo->prepare("SELECT pausado FROM tiempo WHERE id = ?");
    $stmt->execute([$_SESSION['tiempo_id']]);
    $tiempo_activo = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Tiempo</title>
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
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        select, button { width: 100%; padding: 10px; margin-top: 5px; }
        .btn-iniciar { background: #2ecc71; }
        .btn-pausar { background: #f39c12; }
        .btn-reanudar { background: #2ecc71; }
        .btn-finalizar { background: #e74c3c; }
        .error { color: #e74c3c; }
        .success { color: #2ecc71; }
        #tiempo-transcurrido { font-weight: bold; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="navbar">
        <span><?= htmlspecialchars($nombre_usuario) ?> (<?= $rol_usuario ?>)</span>
        <div>
            <?php if (hasRole('admin')): ?>
                <a href="admin/admin.php">Administración</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>

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
                <select name="proyecto" id="proyecto" required>
                    <option value="">Seleccione un proyecto</option>
                    <?php foreach ($proyectos as $proyecto): ?>
                        <option value="<?= $proyecto['id'] ?>" <?= isset($_POST['proyecto']) && $_POST['proyecto'] == $proyecto['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($proyecto['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($partes)): ?>
                <div class="form-group">
                    <label>Parte del Proyecto:</label>
                    <select name="parte" required>
                        <option value="">Seleccione una parte</option>
                        <?php foreach ($partes as $parte): ?>
                            <option value="<?= $parte['id'] ?>"><?= htmlspecialchars($parte['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <?php if (!isset($_SESSION['tiempo_id'])): ?>
                    <button type="submit" name="iniciar" class="btn-iniciar">Iniciar</button>
                <?php else: ?>
                    <?php if ($tiempo_activo && $tiempo_activo['pausado']): ?>
                        <button type="submit" name="reanudar" class="btn-reanudar">Reanudar</button>
                    <?php else: ?>
                        <button type="submit" name="pausar" class="btn-pausar">Pausar</button>
                    <?php endif; ?>
                    <button type="submit" name="finalizar" class="btn-finalizar">Finalizar</button>
                <?php endif; ?>
            </div>
        </form>

        <?php if (isset($_SESSION['tiempo_id'])): ?>
            <div id="tiempo-transcurrido">Tiempo trabajado: <span id="tiempo-actual">00:00:00</span></div>
        <?php endif; ?>
    </div>

    <script>
        // Actualizar partes al cambiar proyecto
        document.getElementById('proyecto').addEventListener('change', function() {
            this.form.submit();
        });

        // Actualizar tiempo en vivo
        <?php if (isset($_SESSION['tiempo_id'])): ?>
            function actualizarTiempo() {
                fetch('tiempo_actual.php?tiempo_id=<?= $_SESSION['tiempo_id'] ?>')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('tiempo-actual').textContent = data.tiempo;
                        if (data.esta_pausado) {
                            document.querySelector('button[name="reanudar"]').style.display = 'block';
                            document.querySelector('button[name="pausar"]').style.display = 'none';
                        }
                    });
            }
            setInterval(actualizarTiempo, 1000);
            actualizarTiempo();
        <?php endif; ?>
    </script>
</body>
</html>
