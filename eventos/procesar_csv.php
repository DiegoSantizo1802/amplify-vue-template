<?php
session_start();
include "configuraconDB.php";

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("<div style='color:red; font-weight:bold;'>❌ Error de conexión: " . $conn->connect_error . "</div>");
}

function startHTML() {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Procesar CSV</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                padding: 40px;
                background-color: #f7f7f7;
                color: #333;
            }
            h3 {
                color: #133980;
            }
            .success { color: #28a745; font-weight: bold; }
            .error { color: #c0392b; font-weight: bold; }
            ul { margin-top: 10px; }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 20px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: center;
            }
            th {
                background-color: #133980;
                color: #fff;
            }
            form {
                margin-top: 20px;
            }
            button {
                background-color: #133980;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .button {
                background-color: #133980;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            button:hover {
                background-color: #0f2f6b;
                transform: scale(1.05);
            }
        </style>
    </head>
    <body>";
}

function endHTML() {
    echo "</body></html>";
}
$evento_id = $_POST['evento_id'] ?? null;
startHTML();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $errors = [];
    $invitados = [];

    $fileTmp = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($fileTmp, 'r')) !== false) {
        $header = fgetcsv($handle, 0, ';');
        $header[0] = preg_replace('/\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map('trim', $header);

        if ($header !== ['nombre', 'apellido', 'correo', 'evento_id']) {
            die("<p class='error'>❌ Encabezado incorrecto. Debe ser: nombre,apellido,correo,evento_id</p>");
        }

        $line = 2;
        $contador = 1;
        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            list($nombre, $apellido, $correo, $evento_id) = $data;

            if (
                empty($nombre) || empty($apellido) ||
                !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
                !is_numeric($evento_id)
            ) {
                $errors[] = "Línea $line: datos inválidos.";
            } else {
                $eventoCheck = $conn->prepare("SELECT id FROM eventos WHERE id = ?");
                $eventoCheck->bind_param("i", $evento_id);
                $eventoCheck->execute();
                $eventoCheck->store_result();

                if ($eventoCheck->num_rows === 0) {
                    $errors[] = "Línea $line: El evento con ID $evento_id no existe.";
                } else {
                    $invitados[] = [
                        'id' => $contador++,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'correo' => $correo,
                        'evento_id' => (int)$evento_id
                    ];
                }

                $eventoCheck->close();
            }
            $line++;
        }

        fclose($handle);
    }

    if ($errors) {
        echo "<h3 class='error'>❌ Errores encontrados:</h3><ul>";
        foreach ($errors as $e) echo "<li>$e</li>";
        echo "</ul>";
        echo "<a href = './subir_csv.html' class = 'button'>Subir de nuevo csv</a>";
    } else {
        $_SESSION['invitados_validados'] = $invitados;

        echo "<h3 class='success'>✅ Datos validados:</h3>";
        echo "<table><tr><th>#</th><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Evento ID</th></tr>";
        foreach ($invitados as $i) {
            echo "<tr><td>{$i['id']}</td><td>{$i['nombre']}</td><td>{$i['apellido']}</td><td>{$i['correo']}</td><td>{$i['evento_id']}</td></tr>";
        }
        echo "</table>";

        echo "<form method='post'>
                <input type='hidden' name='guardar_db' value='1'>
                <button type='submit'>Guardar en base de datos</button>
              </form>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_db']) && isset($_SESSION['invitados_validados'])) {
    $stmtInsert = $conn->prepare("INSERT INTO invitados (nombre, apellido, correo, evento_id) VALUES (?, ?, ?, ?)");
    $stmtUpdate = $conn->prepare("UPDATE invitados SET nombre = ?, apellido = ?, evento_id = ? WHERE correo = ?");

    foreach ($_SESSION['invitados_validados'] as $inv) {
        $emailCheck = $conn->prepare("SELECT id FROM invitados WHERE correo = ?");
        $emailCheck->bind_param("s", $inv['correo']);
        $emailCheck->execute();
        $emailCheck->store_result();
        if ($emailCheck->num_rows > 0) {
            $stmtUpdate->bind_param("ssis", $inv['nombre'], $inv['apellido'], $inv['evento_id'], $inv['correo']);
            $stmtUpdate->execute();
        } else {
            $stmtInsert->bind_param("sssi", $inv['nombre'], $inv['apellido'], $inv['correo'], $inv['evento_id']);
            $stmtInsert->execute();
        }

        $emailCheck->close();
    }

    $stmtInsert->close();
    $stmtUpdate->close();
    unset($_SESSION['invitados_validados']);

    echo "<p class='success'>✅ Los invitados han sido procesados correctamente (insertados o actualizados).</p>";
    echo "<form action='subir_csv.html' method='get'>
            <button type='submit'>Regresar a la carga de CSV</button>
          </form>";
}

endHTML();
?>