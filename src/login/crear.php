<?php
session_start();
require_once "../conexion/conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar si el usuario es administrador
if ($_SESSION['usuario_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores pueden crear usuarios.'); window.location='../dashboard.php';</script>";
    exit;
}

$conn = Database::getInstance();
$mensaje = "";

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol_id = intval($_POST['rol']);

    // Validar que el usuario no exista
    $check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje = "<p style='color:red;'>El usuario ya existe.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (rol_id, usuario, contrasena_hash, nombre_completo, correo, creado_en)
                                VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issss", $rol_id, $usuario, $password, $nombre, $correo);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green;'>Usuario creado correctamente ✅</p>";
        } else {
            $mensaje = "<p style='color:red;'>Error al crear el usuario: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check->close();
}

// Obtener lista de roles
$roles = $conn->query("SELECT id, nombre FROM roles");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef3f9;
            padding: 40px;
        }
        .container {
            width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #bbb;
            border-radius: 8px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #0078D7;
            border: none;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }
        .back {
            text-align: center;
            margin-top: 10px;
        }
        .back a {
            color: #0078D7;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Crear nuevo usuario</h2>
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Nombre completo:</label>
        <input type="text" name="nombre" required>

        <label>Usuario:</label>
        <input type="text" name="usuario" required>

        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <label>Rol:</label>
        <select name="rol" required>
            <option value="">Seleccione un rol</option>
            <?php while ($r = $roles->fetch_assoc()): ?>
                <option value="<?= $r['id'] ?>"><?= ucfirst($r['nombre']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Crear usuario</button>
    </form>

    <div class="back">
        <a href="../dashboard.php">← Volver al panel</a>
    </div>
</div>
</body>
</html>
