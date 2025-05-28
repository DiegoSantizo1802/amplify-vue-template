<?php
require 'configuraconDB.php';

$conn = new mysqli($host, $usuario, $password, $base_datos);

// Inicializa el filtro si se proporciona
$evento_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($evento_id) {
    $stmt = $conn->prepare("SELECT * FROM invitados WHERE evento_id = ?");
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM invitados");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Invitados</title>
    <style>
        :root {
            --azul-oscuro: #133980;
            --verde: #28a745;
            --gris-fondo: #f7f7f7;
            --blanco: #ffffff;
        }

        * {
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: var(--gris-fondo);
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            color: var(--azul-oscuro);
            margin-bottom: 20px;
            font-size: 32px;
        }

        .btn-agregar {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--azul-oscuro);
            color: var(--blanco);
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: bounce 0.6s ease;
        }

        .btn-agregar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }

        form {
            margin-bottom: 20px;
        }

        input[type="number"] {
            padding: 8px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            padding: 8px 16px;
            background-color: var(--verde);
            color: var(--blanco);
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--blanco);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        th {
            background-color: var(--azul-oscuro);
            color: var(--blanco);
            padding: 16px;
            text-align: left;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #e8f0ff;
            transform: scale(1.005);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        a {
            color: var(--azul-oscuro);
            font-weight: bold;
            text-decoration: none;
            margin-right: 12px;
            position: relative;
        }

        a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0;
            height: 2px;
            background-color: var(--azul-oscuro);
            transition: width 0.3s ease;
        }

        a:hover::after {
            width: 100%;
        }

        a:hover {
            filter: brightness(1.2);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <h1>Lista de Invitados</h1>
    <a href="./agregar.php?id=<?php echo($evento_id) ?>" class="btn-agregar">+ Agregar Invitado</a>
    <a href="./subir_csv.html?id=<?php echo($evento_id) ?>" class="btn-agregar">+ Cargar Lista</a>
    <a href="./lista_eventos.php" class="btn-agregar">Ver eventos</a>

   

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>No. Evento</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['apellido']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= htmlspecialchars($row['evento_id']) ?></td>
            <td>
                <a href="editar.php?id=<?= $row['id'] ?>">✏️ Editar</a>
                <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar a este invitado?')">🗑️ Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>