<?php
require_once "../login/check_admin.php";
// Configuración de la base de datos
include("../db/conexion.php");
$conn = conectar();
$id = intval($_GET['id'] ?? 0);
$cliente = null;
$mensaje = '';
$es_error = false;

if ($id <= 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

// Obtener datos actuales del cliente
$stmt = $conn->prepare("SELECT * FROM proveedores WHERE id = ?");
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
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $producto = trim($_POST['producto']);
    $origen = trim($_POST['origen']);

    if (empty($nombre)) {
        $mensaje = 'El nombre es requerido.';
        $es_error = true;
    } else {
        // Actualizar el cliente incluyendo producto_suministra y origen
        $stmt = $conn->prepare("
            UPDATE proveedores 
            SET nombre = ?, telefono = ?, correo = ?, direccion = ?, producto_suministra = ?, origen = ?
            WHERE id = ?
        ");
        // 7 strings y 1 entero (id)
        $stmt->bind_param("ssssssi", $nombre, $telefono, $correo, $direccion, $producto, $origen, $id);

        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente actualizado exitosamente');
            exit;
        } else {
            $mensaje = 'Error al actualizar: ' . $stmt->error;
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
  <title>Editar Proveedores - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Editar Proveedor", "proveedores");
  ?>

  <main class="contenido">
    <form method="POST" class="formulario" id="formulario">
      <div class="campo">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="producto">Producto suministrado</label>
        <input type="text" id="producto" name="producto" value="<?php echo htmlspecialchars($cliente['producto_suministra'] ?? ''); ?>">
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
        <label for="origen">Origen</label>
        <input type="text" id="origen" name="origen" value="<?php echo htmlspecialchars($cliente['origen'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
      </div>

      <button type="button" id="btn-guardar" class="btn-guardar">Actualizar</button>
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
        text: "Se actualizará el cliente con la información proporcionada",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, actualizar',
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
