<?php
include("../db/conexion.php");
$conn = conectar();
$mensaje = '';
$es_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura de datos del formulario
    $nombre = trim($_POST['nombre']);
    $producto = trim($_POST['dpi']); // <- aquí el campo de tu formulario es "dpi" pero guarda el producto
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $origen = trim($_POST['origen']);

    // Validación básica
    if (empty($nombre)) {
        $mensaje = 'El nombre es requerido.';
        $es_error = true;
    } else {
        // Preparamos el INSERT con todas las columnas correctas
        $stmt = $conn->prepare("
            INSERT INTO proveedores 
            (nombre, telefono, correo, producto_suministra, direccion, creado_en, origen)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)
        ");

        // Vinculamos los parámetros
        $stmt->bind_param("ssssss", $nombre, $telefono, $correo, $producto, $direccion, $origen);

        // Ejecutamos
        if ($stmt->execute()) {
            header('Location: index.php?success=Proveedor creado exitosamente');
            exit;
        } else {
            $mensaje = 'Error al crear: ' . $stmt->error;
            $es_error = true;
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
  <title>Crear Proveedor - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Crear Nuevo Proveedor", "proveedores");
?>
  <main class="contenido">
    <form method="POST" class="formulario" id="formulario">
      <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>">
      </div>
      <div class="campo">
        <label for="dpi">Producto suministrado</label>
        <input type="text" id="dpi" name="dpi" value="<?php echo isset($producto) ? htmlspecialchars($producto) : ''; ?>">
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
        <label for="origen">Origen</label>
        <input type="text" id="origen" name="origen" value="<?php echo isset($origen) ? htmlspecialchars($origen) : ''; ?>">
      </div>
      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo isset($direccion) ? htmlspecialchars($direccion) : ''; ?></textarea>
      </div>
      
      <button type="button" id="btn-guardar" class="btn-guardar">Guardar</button>
      <button type="button" id="btn-regresar" class="btn-regresar">Regresar</button>
    </form>
  </main>

  <?php if ($mensaje): ?>
  <script>
    Swal.fire({
      title: '<?php echo $es_error ? "Error" : "Éxito"; ?>',
      text: '<?php echo htmlspecialchars($mensaje); ?>',
      icon: '<?php echo $es_error ? "error" : "success"; ?>',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <script>
    document.getElementById('btn-guardar').addEventListener('click', function() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "Se creará el Proveedor con la información proporcionada",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('formulario').submit();
        }
      });
    });

    document.getElementById('btn-regresar').addEventListener('click', function() {
      Swal.fire({
        title: '¿Regresar a la lista?',
        text: "Perderás los cambios no guardados",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, regresar',
        cancelButtonText: 'Quedarse aquí'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'index.php';
        }
      });
    });
  </script>
</body>
</html>
