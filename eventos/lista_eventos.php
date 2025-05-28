<?php
include "configuraconDB.php";
// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta para obtener todos los eventos
$sql = "SELECT id, nombre, fecha, tipo_evento FROM eventos ORDER BY fecha DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .table-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        h2 {
            font-weight: 600;
            color: #333;
            text-align: center;
        }

        .table {
            margin-top: 20px;
        }

        .table thead th {
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        .action-icons a {
            margin: 0 5px;
            font-size: 1.2rem;
            transition: transform 0.2s ease;
        }

        .action-icons a:hover {
            transform: scale(1.2);
        }

        .edit-icon {
            color: #28a745;
        }

        .delete-icon {
            color: #dc3545;
        }

        .btn-primary {
            background-color: #133980;
            border-color: #133980;
        }

        .btn-primary:hover {
            background-color: #0f2e66;
            border-color: #0f2e66;
        }

        .text-center a.btn {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <h2 class="mb-4">Listado de Eventos</h2>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre del Evento</th>
                        <th>Fecha</th>
                        <th>Tipo de Evento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($resultado->num_rows > 0) {
                        while($evento = $resultado->fetch_assoc()) {
                            $fecha_formateada = date("d/m/Y", strtotime($evento['fecha']));
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($evento['nombre']) . "</td>";
                            echo "<td>" . $fecha_formateada . "</td>";
                            echo "<td>" . htmlspecialchars($evento['tipoevento']) . "</td>";
                            echo "<td class='action-icons'>";
                            echo "<a href='editar_evento.php?id=" . $evento['id'] . "' class='edit-icon'><i class='bi bi-pencil-square'></i></a>";
                            echo "<a href='ver_evento.php?id=" . $evento['id'] . "' class='edit-icon'><i class='bi bi-check-square'></i></a>";
                            echo "<a href='./index.php?id=" . $evento['id'] . "' class='invite-icon' title='Ver invitados'><i class='bi bi-people'></i></a>";
                            echo "<a href='eliminar_evento.php?id=" . $evento['id'] . "' class='delete-icon' onclick='return confirm(\"¿Estás seguro de eliminar este evento?\")'><i class='bi bi-trash'></i></a>";
                            echo "<a href='ses.php?id=" . $evento['id'] . "' class='delete-icon' ><i class='bi bi-mailbox'></i></a>";
                            echo "<a href='confirmacion.php?id=" . $evento['id'] . "' class='delete-icon' ><i class='bi bi-patch-check-fill'></i></a>";
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No hay eventos registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <a href="index.html" class="btn btn-primary">Registrar Nuevo Evento</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
