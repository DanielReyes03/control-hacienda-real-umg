<?php
require_once "../login/check_adminGer.php";
include("../db/conexion.php");
$conn = conectar();

$id = intval($_GET['id'] ?? 0);
$cliente = null;
$mensaje = '';
$es_error = false;

if ($id <= 0) {
    header('Location: index.php?error=ID de cliente inválido');
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

    // Validaciones
    $errores = [];
    if (empty($nombre)) {
        $errores[] = 'El nombre es requerido.';
    }
    if (!empty($dpi) && (!is_numeric($dpi) || strlen($dpi) !== 13)) {
        $errores[] = 'El DPI debe tener exactamente 13 dígitos numéricos.';
    }
    if (!empty($telefono) && (!is_numeric($telefono) || strlen($telefono) !== 8)) {
        $errores[] = 'El teléfono debe tener exactamente 8 dígitos numéricos.';
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, dpi = ?, telefono = ?, correo = ?, direccion = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $dpi, $telefono, $correo, $direccion, $id);
        
        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente actualizado exitosamente');
            exit;
        } else {
            $errores[] = 'Error al actualizar: ' . $conn->error;
        }
        $stmt->close();
    }

    if (!empty($errores)) {
        $mensaje = implode('<br>', $errores);
        $es_error = true;
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
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .error { color: #d33; font-size: 0.9em; margin-top: 5px; display: none; }
    input:invalid { border-color: #d33; }
  </style>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Editar Clientes");
  ?>

  <main class="contenido">
    <form method="POST" class="formulario" id="formulario">
      <div class="campo">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>">
        <div class="error" id="err-nombre">El nombre es requerido.</div>
      </div>
      <div class="campo">
        <label for="dpi">DPI (exactamente 13 dígitos)</label>
        <input type="text" id="dpi" name="dpi" maxlength="13" pattern="[0-9]{13}" title="Solo números, exactamente 13 dígitos" value="<?php echo htmlspecialchars($cliente['dpi'] ?? ''); ?>">
        <div class="error" id="err-dpi">El DPI debe tener exactamente 13 dígitos numéricos.</div>
      </div>
      <div class="campo">
        <label for="telefono">Teléfono (exactamente 8 dígitos)</label>
        <input type="text" id="telefono" name="telefono" maxlength="8" pattern="[0-9]{8}" title="Solo números, exactamente 8 dígitos" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>">
        <div class="error" id="err-telefono">El teléfono debe tener exactamente 8 dígitos numéricos.</div>
      </div>
      <div class="campo">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
      </div>
      <button type="button" id="btn-guardar" class="btn-guardar">Actualizar</button>
      <button type="button" id="btn-regresar" class="btn-regresar">Regresar o cancelar</button>
    </form>
  </main>

  <?php if ($mensaje): ?>
  <script>
    Swal.fire({
      title: '<?php echo $es_error ? "Error" : "Éxito"; ?>',
      html: '<?php echo $mensaje; ?>',
      icon: '<?php echo $es_error ? "error" : "success"; ?>',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <script>
    // Funciones para solo números
    function soloNumeros(input) {
      input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    }
    soloNumeros(document.getElementById('dpi'));
    soloNumeros(document.getElementById('telefono'));

    // Validaciones y SweetAlert
    document.getElementById('btn-guardar').addEventListener('click', function() {
      const form = document.getElementById('formulario');
      const nombre = form.nombre.value.trim();
      const dpi = form.dpi.value.trim();
      const telefono = form.telefono.value.trim();

      let errores = [];

      if (!nombre) errores.push("El nombre es requerido.");
      if (dpi && (!/^[0-9]{13}$/.test(dpi))) errores.push("El DPI debe tener 13 dígitos.");
      if (telefono && (!/^[0-9]{8}$/.test(telefono))) errores.push("El teléfono debe tener 8 dígitos.");

      if (errores.length > 0) {
        Swal.fire("Error", errores.join("<br>"), "error");
        return;
      }

      Swal.fire({
        title: "¿Actualizar cliente?",
        text: "Se guardarán los cambios realizados.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, actualizar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });

    document.getElementById('btn-regresar').addEventListener('click', function() {
      Swal.fire({
        title: "¿Regresar a la lista?",
        text: "Perderás los cambios no guardados.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, regresar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "index.php";
        }
      });
    });
  </script>
</body>
</html>
