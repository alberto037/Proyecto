<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pruebas CRUD</title>
    <script>
        const BASE_URL = window.location.pathname.replace('/pruebas.php', '');

        async function crearProyecto() {
            try {
                const response = await fetch(`${BASE_URL}/crear.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nombre_proyecto: "Proyecto prueba",
                        fecha_inicio: "2024-01-01",
                        fecha_fin: "2024-12-31",
                        descripcion: "Descripción del proyecto"
                    })
                });
                const result = await response.json();
                console.log("Respuesta de crear.php:", result);
                alert(result.message);
            } catch (error) {
                console.error("Error:", error);
                alert("Error al crear proyecto. Ver consola.");
            }
        }

        async function agregarParte() {
            const id_proyecto = prompt("Ingrese ID de proyecto existente:");
            if (!id_proyecto) return;

            try {
                const response = await fetch(`${BASE_URL}/acciones/agregar_parte.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_proyecto: id_proyecto,
                        nombre_parte: "Parte de prueba",
                        descripcion: "Descripción de la parte"
                    })
                });
                const result = await response.json();
                console.log("Respuesta de agregar_parte.php:", result);
                alert(result.message);
            } catch (error) {
                console.error("Error:", error);
                alert("Error al agregar parte. Ver consola.");
            }
        }

        async function eliminarProyecto() {
            const id_proyecto = prompt("Ingrese ID de proyecto a eliminar:");
            if (!id_proyecto) return;

            try {
                const response = await fetch(`${BASE_URL}/acciones/eliminar_proyecto.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_proyecto: id_proyecto })
                });
                const result = await response.json();
                console.log("Respuesta de eliminar_proyecto.php:", result);
                alert(result.message);
            } catch (error) {
                console.error("Error:", error);
                alert("Error al eliminar proyecto. Ver consola.");
            }
        }
    </script>
</head>
<body>
    <h1>Pruebas CRUD</h1>
    <button onclick="crearProyecto()">Probar crear.php</button>
    <button onclick="agregarParte()">Probar agregar_parte.php</button>
    <button onclick="eliminarProyecto()">Probar eliminar_proyecto.php</button>
    <p>Revisa la consola (F12 > Console) para ver respuestas detalladas.</p>
</body>
</html>
