<?php
include "configuraconDB.php";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_GET['evento_id'])) {
    $evento_id = intval($_GET['evento_id']);
    $sql = "SELECT nombre, apellido, correo FROM invitados WHERE evento_id = $evento_id";
    $resultado = $conn->query($sql);

    $invitados = [];
    while ($fila = $resultado->fetch_assoc()) {
        $invitados[] = $fila;
    }

    echo json_encode($invitados);
}
?>
