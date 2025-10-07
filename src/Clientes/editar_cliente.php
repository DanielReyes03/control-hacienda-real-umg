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

$id = intval($_GET['id'] ?? 0);
$cliente = null;
$mensaje = '';

if ($id <= 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

if (!$cliente) {
    header('Location: index.php?error=Cliente no encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $dpi = trim($_POST['dpi']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    if (empty($nombre)) {
        $mensaje = 'El nombre es requerido.';
    } else {
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, dpi = ?, telefono = ?, correo = ?, direccion = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $dpi, $telefono, $correo, $direccion, $id);
        
        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente actualizado exitosamente');
            exit;
        } else {
            $mensaje = 'Error al actualizar: ' . $conn->error;
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
  <title>Editar Cliente - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="encabezado">
    <a href="index.php" class="btn-volver">← Volver</a>
    <h1>Editar Cliente</h1>
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
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="dpi">DPI</label>
        <input type="text" id="dpi" name="dpi" value="<?php echo htmlspecialchars($cliente['dpi'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
      </div>
      <button type="submit" class="btn-guardar">Actualizar</button>
    </form>
  </main>
</body>
</html>