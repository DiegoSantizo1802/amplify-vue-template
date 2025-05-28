<?php

include "configuraconDB.php";


// Crear conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

// Seleccionar base de datos
$conn->select_db($dbname);

// Crear tabla de invitados si no existe
$sql = "CREATE TABLE IF NOT EXISTS invitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    celular VARCHAR(100) NOT NULL,
    UNIQUE KEY (correo)
)";
$conn->query($sql);

// Verificar si la columna 'celular' existe, si no agregarla
$sql = "SHOW COLUMNS FROM invitados LIKE 'celular'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // La columna no existe, entonces la agregamos
    $sql = "ALTER TABLE invitados ADD COLUMN celular VARCHAR(100) NOT NULL";
    $conn->query($sql);
}

?>
