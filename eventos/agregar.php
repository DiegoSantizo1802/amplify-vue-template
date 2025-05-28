<?php
require 'configuraconDB.php';

$conn = new mysqli($host, $usuario, $password, $base_datos);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación de datos obligatorios
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $evento_id = trim($_POST['evento_id']);

    
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($evento_id)) {
        $error = "Todos los campos son obligatorios. ($evento_id)";
    } else {
        // Evitar duplicados
        $stmt = $conn->prepare("SELECT * FROM invitados WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "El correo ya está registrado.";
        } else {
            // Insertar invitado
            $stmt = $conn->prepare("INSERT INTO invitados (nombre,apellido,correo,evento_id) VALUES (?,?,?,?)");
            $stmt->bind_param("sssi", $nombre, $apellido,$correo,$evento_id);
            $stmt->execute();
            
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Invitado</title>
    <style>
        :root {
            --azul-oscuro: #133980;
            --verde: #28a745;
            --gris-fondo: #f7f7f7;
            --blanco: #ffffff;
            --error: #d32f2f;
        }

        * {
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
            background-color: var(--gris-fondo);
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            color: var(--azul-oscuro);
            font-size: 28px;
            margin-bottom: 20px;
        }

        form {
            background-color: var(--blanco);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
            animation: floatIn 0.6s ease;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: 600;
            color: #333;
        }

        input {
            padding: 12px;
            margin-top: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        input:focus {
            border-color: var(--azul-oscuro);
            outline: none;
            box-shadow: 0 0 5px var(--azul-oscuro);
        }

        .btn-submit {
            margin-top: 24px;
            padding: 12px 24px;
            background-color: var(--azul-oscuro);
            color: var(--blanco);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-submit:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }

        .error {
            margin-top: 20px;
            color: var(--error);
            background-color: #ffebee;
            border: 1px solid var(--error);
            padding: 12px;
            border-radius: 6px;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes floatIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-3px); }
            100% { transform: translateX(0); }
        }
    </style>
</head>
<body>
    <h1>Agregar Invitado</h1>
    <form method="POST" action="./agregar.php">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="apellido">Apellido</label>
        <input type="text" name="apellido" id="apellido" required>
        
        <label for="correo">Correo</label>
        <input type="email" name="correo" id="correo" required>
    
        <label for="evento_id">Id Evento</label>
        <input type="number" name="evento_id" id="evento_id" value="<?php echo $_GET['id']; ?>" required>
        
        <button type="submit" class="btn-submit">Agregar Invitado</button>
        <a type="submit" href="./index.php" class="btn-submit">Regresar lista</a>

        
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
