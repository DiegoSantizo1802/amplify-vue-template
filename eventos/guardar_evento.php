<?php
// Configuración de conexión a la base de datos
include "configuraconDB.php";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Capturar datos del formulario
$nombre_evento = $_POST['nombre_evento'];
// Usar strip_tags para limpiar contenido HTML
$descripcion_evento = strip_tags($_POST['descripcion_evento'], '<p><strong><em><u><h1><h2><h3><ul><ol><li><a>');
$fecha_evento = $_POST['fecha_evento'];
$ubicacion = $_POST['ubicacion'];
$direccion = $_POST['direccion'];
$cantidad_invitados = $_POST['cantidad_invitados'];
$tipo_evento = $_POST['tipo_evento'];

// Preparar consulta SQL con sentencia preparada
$sql = "INSERT INTO eventos (
    nombre, 
    descripcion, 
    fecha, 
    ubicacion, 
    direccion, 
    cantidad_invitados, 
    tipo_evento
) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

// Vincular parámetros
$stmt->bind_param(
    "sssssss", 
    $nombre_evento, 
    $descripcion_evento, 
    $fecha_evento, 
    $ubicacion, 
    $direccion, 
    $cantidad_invitados, 
    $tipo_evento
);

// Ejecutar consulta
if ($stmt->execute()) {
   echo (' 
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Evento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
<h1>Evento registrado exitosamente.</h1>
<a class="btn btn-primary" href="lista_eventos.php">Volver a la lista de eventos</a>
        <div class="alert alert-success" role="alert">
            El evento <strong>' . htmlspecialchars($nombre_evento) . '</strong> ha sido registrado correctamente.
        </div>
        <div class="alert alert-info" role="alert">
            <strong>Descripción:</strong> ' . htmlspecialchars($descripcion_evento) . '
        </div>
    </div>
</body>
</html>
');
} else {
    echo "Error al registrar el evento: " . $stmt->error;
}

// Cerrar conexión
$stmt->close();






if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo_csv"])) {
    // Verificar que sea un archivo CSV
    $archivo = $_FILES["archivo_csv"];
    $nombre_archivo = $archivo["name"];
    $tipo_archivo = $archivo["type"];
    $tamano_archivo = $archivo["size"];
    $tmp_archivo = $archivo["tmp_name"];
    $error_archivo = $archivo["error"];

    // Comprobar errores
    if ($error_archivo === 0) {
        // Verificar extensión
        $extension_archivo = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        if ($extension_archivo != "csv") {
            mostrarMensaje("error", "El archivo debe ser CSV.");
        } else {
            // Verificar tamaño (máximo 5MB)
            if ($tamano_archivo > 5000000) {
                mostrarMensaje("error", "El archivo es demasiado grande (máximo 5MB).");
            } else{
                    // Abrir el archivo CSV
                    if (($gestor = fopen($tmp_archivo, "r")) !== FALSE) {
                        // Leer la primera línea como encabezados
                        $encabezados = fgetcsv($gestor, 1000, ",");
                        
                        // Configurar contador de filas procesadas
                        $filas_procesadas = 0;
                        $filas_error = 0;
                        
                        // Procesar cada fila
                        while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                            // Verificar que hay datos suficientes
                            if (count($datos) >= 7) {
                                // Extraer datos y limpiarlos
                                $nombre_evento = limpiarDato($datos[0]);
                                $descripcion_evento = $datos[1]; // Permitir HTML formateado
                                $fecha_evento = limpiarDato($datos[2]);
                                $ubicacion = limpiarDato($datos[3]);
                                $direccion = limpiarDato($datos[4]);
                                $cantidad_invitados = (int)limpiarDato($datos[5]);
                                $tipo_evento = limpiarDato($datos[6]);
                                
                                // Preparar consulta
                                $sql = "INSERT INTO invitados (
                                    idEvento, 
                                    nombre, 
                                    email, 
                                    telefono, 
                                    estado
                                ) VALUES (?, ?, ?, ?, ?)";
                                
                                $stmt = $conexion->prepare($sql);
                                $stmt->bind_param(
                                    "sssss", 
                                    $nombre_evento, 
                                    $descripcion_evento, 
                                    $fecha_evento, 
                                    $ubicacion, 
                                    $direccion, 
                                    $cantidad_invitados, 
                                    $tipo_evento
                                );
                                
                                // Ejecutar la inserción
                                if ($stmt->execute()) {
                                    $filas_procesadas++;
                                } else {
                                    $filas_error++;
                                }
                                
                                $stmt->close();
                            } else {
                                $filas_error++;
                            }
                        }
                        
                        fclose($gestor);
                        
                        // Mostrar resultados
                        mostrarMensaje(
                            "success", 
                            "Importación completada. Filas procesadas: $filas_procesadas. Filas con error: $filas_error"
                        );
                    } else {
                        mostrarMensaje("error", "No se pudo abrir el archivo CSV.");
                    }
                    
                    // Cerrar conexión
                    $conexion->close();
                }
            }
        }
        } else {
        mostrarMensaje("error", "Error al subir el archivo: " . $error_archivo);
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();



    $conexion->close();


?>
