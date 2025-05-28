<?php
require 'configuraconDB.php';

$conn = new mysqli($host, $usuario, $password, $base_datos);


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Eliminar invitado
    $stmt = $conn->prepare("DELETE FROM invitados WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Si la eliminación es exitosa, redirige a la página principal
        header("Location: index.php");
        exit();
    } else {
        // Si ocurre un error, muestra un mensaje
        echo "Error al eliminar el invitado. Intenta nuevamente.";
        exit();
    }
} else {
    echo "ID no proporcionado.";
    exit();
}
?>
