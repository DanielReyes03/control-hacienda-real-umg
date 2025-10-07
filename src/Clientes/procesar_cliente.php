<?php
// Configuración de la base de datos
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $dpi = trim($_POST['dpi']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    // Validación básica (agrega más si quieres)
    if (empty($nombre)) {
        $mensaje = 'El nombre es requerido.';
    } else {
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, dpi, telefono, correo, direccion, creado_en) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $nombre, $dpi, $telefono, $correo, $direccion);
        
        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente creado exitosamente');
            exit;
        } else {
            $mensaje = 'Error al crear: ' . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Cliente - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="encabezado">
    <a href="index.php" class="btn-volver">← Volver</a>
    <h1>Crear Cliente</h1>
  </header>

  <main class="contenido">
    <?php if ($mensaje): ?>
      <div class="mensaje <?php echo strpos($mensaje, 'Error') === 0 ? 'error' : 'success'; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="formulario">
      <div class="campo">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>">
      </div>
      <div class="campo">
        <label for="dpi">DPI</label>
        <input type="text" id="dpi" name="dpi" value="<?php echo isset($dpi) ? htmlspecialchars($dpi) : ''; ?>">
      </div>
      <div class="campo">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>">
      </div>
      <div class="campo">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?php echo isset($correo) ? htmlspecialchars($correo) : ''; ?>">
      </div>
      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo isset($direccion) ? htmlspecialchars($direccion) : ''; ?></textarea>
      </div>
      <button type="submit" class="btn-guardar">Guardar</button>
    </form>
  </main>
</body>
</html>