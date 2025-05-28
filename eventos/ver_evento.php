<?php
include "configuraconDB.php";
// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT * FROM eventos WHERE id=" . $id;
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .event-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        h2 {
            font-weight: 600;
            color: #133980;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
        }

        iframe {
            width: 100%;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .no-event {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="event-container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <?php 
                    if ($resultado->num_rows > 0) {
                        while($evento = $resultado->fetch_assoc()) {
                            $fecha_formateada = date("d/m/Y", strtotime($evento['fecha']));
                            echo "<h2>" . htmlspecialchars($evento['nombre']) . "</h2>";
                            echo "<p><strong>Descripción:</strong> " . htmlspecialchars($evento['descripcion']) . "</p>";
                            echo "<p><strong>Dirección:</strong> " . htmlspecialchars($evento['direccion']) . "</p>";
                            echo "<p><strong>Fecha:</strong> " . $fecha_formateada . "</p>";
                            echo '<iframe src="https://embed.waze.com/iframe?zoom=12&' . htmlspecialchars($evento['ubicacion']) . '&ct=livemap" height="400" allowfullscreen></iframe>';
                        }
                    } else {
                        echo "<div class='no-event'>No hay eventos registrados</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
