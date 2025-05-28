<?php
include "configuraconDB.php";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta para obtener todos los eventos
$sql_eventos = "SELECT * FROM eventos";
$result_eventos = $conn->query($sql_eventos);

// Array para guardar los eventos
$eventos = [];
if ($result_eventos->num_rows > 0) {
    while($row = $result_eventos->fetch_assoc()) {
        $eventos[] = $row;
    }
}

// Consulta para obtener los invitados por evento
$sql_invitados = "SELECT evento_id, COUNT(*) as cantidad_invitados FROM invitados GROUP BY evento_id";
$result_invitados = $conn->query($sql_invitados);

// Array para guardar la cantidad de invitados por evento
$invitados_por_evento = [];
if ($result_invitados->num_rows > 0) {
    while($row = $result_invitados->fetch_assoc()) {
        $invitados_por_evento[$row['evento_id']] = $row['cantidad_invitados'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            text-align: center;
            color: #133980;
            font-weight: bold;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        label {
            font-size: 1.1rem;
            color: #333;
            margin-right: 10px;
        }

        input[type="date"], input[type="text"] {
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 200px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #133980;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #ffdb2d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #133980;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        canvas {
            display: block;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .invitados-container {
            margin-top: 10px;
            padding: 10px;
            background: #fff;
            border-left: 4px solid #133980;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard de Eventos</h1>

        <form method="GET" action="">
            <label for="fecha">Filtrar por Fecha:</label>
            <input type="date" name="fecha" id="fecha">
            <label for="tipo_evento">Filtrar por Tipo de Evento:</label>
            <input type="text" name="tipo_evento" id="tipo_evento">
            <input type="submit" value="Filtrar">
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre del Evento</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Dirección</th>
                    <th>Tipo de Evento</th>
                    <th>Porcentaje de Ocupación</th>
                    <th>Invitados</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['fecha']) || isset($_GET['tipo_evento'])) {
                    $fecha = $_GET['fecha'];
                    $tipo_evento = $_GET['tipo_evento'];
                    $sql_eventos = "SELECT * FROM eventos WHERE 1";

                    if ($fecha) {
                        $sql_eventos .= " AND fecha = '$fecha'";
                    }

                    if ($tipo_evento) {
                        $sql_eventos .= " AND tipo_evento LIKE '%$tipo_evento%'";
                    }

                    $result_eventos = $conn->query($sql_eventos);
                    $eventos = [];
                    if ($result_eventos->num_rows > 0) {
                        while($row = $result_eventos->fetch_assoc()) {
                            $eventos[] = $row;
                        }
                    }
                }

                foreach ($eventos as $evento) {
                    $evento_id = $evento['id'];
                    $cantidad_invitados = isset($invitados_por_evento[$evento_id]) ? $invitados_por_evento[$evento_id] : 0;
                    $ocupacion = ($cantidad_invitados / $evento['cantidad_invitados']) * 100;
                ?>
                    <tr>
                        <td><?php echo $evento['nombre']; ?></td>
                        <td><?php echo $evento['descripcion']; ?></td>
                        <td><?php echo $evento['fecha']; ?></td>
                        <td><?php echo $evento['direccion']; ?></td>
                        <td><?php echo $evento['tipo_evento']; ?></td>
                        <td><?php echo round($ocupacion, 2) . '%'; ?></td>
                        <td>
                            <button onclick="verInvitados(<?php echo $evento['id']; ?>)">Ver invitados</button>
                            <div id="invitados_<?php echo $evento['id']; ?>" class="invitados-container"></div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <canvas id="ocupacionChart" width="400" height="400"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            var ctx = document.getElementById('ocupacionChart').getContext('2d');
            var ocupacionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_column($eventos, 'nombre')); ?>,
                    datasets: [{
                        label: 'Ocupación de Eventos',
                        data: <?php echo json_encode(array_map(function($evento) use ($invitados_por_evento) {
                            $evento_id = $evento['id'];
                            $cantidad_invitados = isset($invitados_por_evento[$evento_id]) ? $invitados_por_evento[$evento_id] : 0;
                            return ($cantidad_invitados / $evento['cantidad_invitados']) * 100;
                        }, $eventos)); ?>,
                        backgroundColor: ['#ffdb2d', '#133980', '#cccccc'],
                    }]
                },
                options: {
                    responsive: true
                }
            });

            function verInvitados(eventoId) {
                const contenedor = document.getElementById("invitados_" + eventoId);
                if (contenedor.style.display === "block") {
                    contenedor.style.display = "none";
                    contenedor.innerHTML = "";
                    return;
                }

                fetch("get_invitados.php?evento_id=" + eventoId)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            contenedor.innerHTML = "<em>No hay invitados registrados.</em>";
                        } else {
                            let html = "<ul>";
                            data.forEach(inv => {
                                html += `<li>${inv.nombre} ${inv.apellido} (${inv.correo})</li>`;
                            });
                            html += "</ul>";
                            contenedor.innerHTML = html;
                        }
                        contenedor.style.display = "block";
                    })
                    .catch(err => {
                        contenedor.innerHTML = "<span>Error al cargar los invitados.</span>";
                        contenedor.style.display = "block";
                        console.error(err);
                    });
            }
        </script>
    </div>
</body>
</html>

<?php
$conn->close();
?>