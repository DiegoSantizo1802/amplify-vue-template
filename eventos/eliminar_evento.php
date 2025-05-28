<?php
// Configuración de conexión a la base de datos
include "configuraconDB.php";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si se recibió un ID de evento
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_evento = $_GET['id'];

    // Preparar consulta de eliminación
    $sql = "DELETE FROM eventos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_evento);

    // Ejecutar eliminación
    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: lista_eventos.php?mensaje=eliminado");
        exit();
    } else {
        // Redirigir con mensaje de error
        header("Location: lista_eventos.php?error=no_eliminado");
        exit();
    }
} else {
    // ID inválido
    header("Location: lista_eventos.php?error=id_invalido");
    exit();
}

// Cerrar conexión
$conexion->close();
?>