<?php

include "configuraconDB.php";
$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("<div style='color:red; font-weight:bold;'>❌ Error de conexión: " . $conn->connect_error . "</div>");
}
$message="Evento confirmado para enivo de notificaciones.";
$id=$_GET['id'];

if ($id>0){
    $message="Evento confirmado para enivo de notificaciones.";
} else {
    $message="Evento no encontrado.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title>Confirmacion</title>
    <style>
        h2{
            text-align: center;
        }
        img {
            margin-left: 38%;
        }
        body {
            background-image:url(https://st2.depositphotos.com/5956290/9396/v/450/depositphotos_93964826-stock-illustration-white-background-of-colorful-confetti.jpg);
            background-size: cover;
            background-repeat: no-repeat;
        }
        button {
            margin-left: 45%;
        }
    </style>
</head>
<body>
<div class="container-fluid">
        <div class="row">
            <div calss="col-12 align-items-center text-center">
                <h2><?php echo($message);?></h2>
    
    </div>
    </div>
</div>
</body>
</html>