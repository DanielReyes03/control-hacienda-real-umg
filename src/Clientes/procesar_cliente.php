<?php
require_once "../login/check_adminEmple.php"; 
include("../db/conexion.php"); 
$conn = conectar();

$mensaje = '';
$es_error = false;
$nombre = $dpi = $telefono = $correo = $direccion = ''; // Inicializar para repoblado

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
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, dpi, telefono, correo, direccion, creado_en) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $nombre, $dpi, $telefono, $correo, $direccion);

        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente creado exitosamente');
            exit;
        } else {
            $errores[] = 'Error al crear: ' . $conn->error;
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
  <title>Crear Cliente - Hacienda Real</title>
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
cabecera("Crear Clientes");
?>

<main class="contenido">
  <form method="POST" class="formulario" id="formulario">
    <div class="campo">
      <label for="nombre">Nombre *</label>
      <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($nombre); ?>">
      <div class="error" id="err-nombre">El nombre es requerido.</div>
    </div>

    <div class="campo">
      <label for="dpi">DPI (exactamente 13 dígitos)</label>
      <input type="text" id="dpi" name="dpi" maxlength="13" pattern="[0-9]{13}" title="Solo números, exactamente 13 dígitos" value="<?php echo htmlspecialchars($dpi); ?>">
      <div class="error" id="err-dpi">El DPI debe tener exactamente 13 dígitos numéricos.</div>
    </div>

    <div class="campo">
      <label for="telefono">Teléfono (exactamente 8 dígitos)</label>
      <input type="text" id="telefono" name="telefono" maxlength="8" pattern="[0-9]{8}" title="Solo números, exactamente 8 dígitos" value="<?php echo htmlspecialchars($telefono); ?>">
      <div class="error" id="err-telefono">El teléfono debe tener exactamente 8 dígitos numéricos.</div>
    </div>

    <div class="campo">
      <label for="correo">Correo</label>
      <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
    </div>

    <div class="campo">
      <label for="direccion">Dirección</label>
      <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($direccion); ?></textarea>
    </div>

    <button type="button" id="btn-guardar" class="btn-guardar">Guardar</button>
    <button type="button" id="btn-regresar" class="btn-regresar">Regresar</button>
  </form>
</main>

<?php if ($mensaje): ?>
<script>
Swal.fire({
  title: '<?php echo $es_error ? "Error" : "Éxito"; ?>',
  html: '<?php echo htmlspecialchars($mensaje); ?>',
  icon: '<?php echo $es_error ? "error" : "success"; ?>',
  confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

<script>
  // Solo números
  function soloNumeros(input) {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
  }
  soloNumeros(document.getElementById('dpi'));
  soloNumeros(document.getElementById('telefono'));

  // Validación del formulario
  document.getElementById('btn-guardar').addEventListener('click', function() {
    const form = document.getElementById('formulario');
    const nombre = document.getElementById('nombre').value.trim();
    const dpi = document.getElementById('dpi').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    let valid = true;

    document.querySelectorAll('.error').forEach(e => e.style.display = 'none');

    if (!nombre) {
      document.getElementById('err-nombre').style.display = 'block';
      valid = false;
    }
    if (dpi && (dpi.length !== 13 || !/^\d{13}$/.test(dpi))) {
      document.getElementById('err-dpi').style.display = 'block';
      valid = false;
    }
    if (telefono && (telefono.length !== 8 || !/^\d{8}$/.test(telefono))) {
      document.getElementById('err-telefono').style.display = 'block';
      valid = false;
    }

    if (!valid) {
      Swal.fire('Error', 'Por favor corrige los campos indicados.', 'error');
      return;
    }

    Swal.fire({
      title: '¿Estás seguro?',
      text: "Se creará el cliente con la información proporcionada.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, guardar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

  document.getElementById('btn-regresar').addEventListener('click', function() {
    Swal.fire({
      title: '¿Regresar a la lista?',
      text: "Perderás los cambios no guardados.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, regresar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'index.php';
      }
    });
  });
</script>
</body>
</html>
