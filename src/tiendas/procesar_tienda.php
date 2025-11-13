<?php
require_once "../login/check_adminEmple.php"; 
include("../db/conexion.php"); 
$conn = conectar();

$mensaje = '';
$es_error = false;

// 🔹 Obtener lista de empleados para el combo de gerente
$empleados = [];
$result = $conn->query("SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM empleados WHERE activo = 1 ORDER BY nombres ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $empleados[] = $row;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nombre = trim($_POST['nombre']);
  $direccion = trim($_POST['direccion']);
  $gerente_id = trim($_POST['gerente_id']);
  $telefono = trim($_POST['telefono']);
  $numero_mesas = trim($_POST['numero_mesas']);
  $horarios = trim($_POST['horarios']);
  $caracteristicas = trim($_POST['caracteristicas']);
  $calificacion = trim($_POST['calificacion']);
  $capacidad = trim($_POST['capacidad']);

  $errores = [];
  if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
  if ($numero_mesas !== '' && !is_numeric($numero_mesas)) $errores[] = 'Número de mesas debe ser numérico.';
  if ($calificacion !== '' && (!is_numeric($calificacion) || $calificacion < 0 || $calificacion > 5)) $errores[] = 'La calificación debe ser un número entre 0 y 5.';
  if ($capacidad !== '' && !is_numeric($capacidad)) $errores[] = 'Capacidad debe ser numérico.';

  if (empty($errores)) {
    // Si gerente_id está vacío, se pasa NULL
    $gerente_id = ($gerente_id === '') ? NULL : intval($gerente_id);

    // Cálculo automático de capacidad si no se proporciona
    if (empty($capacidad) || $capacidad == 0) {
      $personas_por_mesa = 4;
      $capacidad = intval($numero_mesas) * $personas_por_mesa;
    } else {
      $capacidad = intval($capacidad);
    }

    $stmt = $conn->prepare("
      INSERT INTO sucursales 
      (nombre, direccion, gerente_id, telefono, numero_mesas, horarios, caracteristicas, calificacion, capacidad, creado_en)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param(
      "ssisissdi",
      $nombre,
      $direccion,
      $gerente_id,
      $telefono,
      $numero_mesas,
      $horarios,
      $caracteristicas,
      $calificacion,
      $capacidad
    );

    if ($stmt->execute()) {
      header('Location: index.php?success=Sucursal creada exitosamente');
      exit;
    } else {
      $errores[] = "Error al crear: " . $conn->error;
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
  <title>Crear Sucursal</title>
  <link rel="stylesheet" href="crear.css">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
include("../compartido/componentes/cabecera/index.php");
cabecera("Crear Sucursal");
?>

<main class="contenido">
  <form method="POST" class="formulario">

    <div class="campo">
      <label>Nombre *</label>
      <input type="text" name="nombre" required>
    </div>

    <div class="campo">
      <label>Dirección</label>
      <input type="text" name="direccion">
    </div>

    <div class="campo">
      <label>Gerente</label>
      <select name="gerente_id">
        <option value="">-- Seleccione un Gerente --</option>
        <?php foreach ($empleados as $emp): ?>
          <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nombre_completo']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="campo">
      <label>Teléfono</label>
      <input type="text" name="telefono">
    </div>

    <div class="campo">
      <label>Número de Mesas</label>
      <input type="number" name="numero_mesas" min="0" value="0">
    </div>

    <div class="campo">
      <label>Horarios</label>
      <input type="text" name="horarios" placeholder="Ejemplo: Lunes a Domingo de 8am a 10pm">
    </div>

    <div class="campo">
      <label>Características</label>
      <textarea name="caracteristicas" rows="3" placeholder="Ejemplo: Restaurante familiar, con terraza y parqueo."></textarea>
    </div>

    <div class="campo">
      <label>Calificación (0 - 5)</label>
      <input type="number" step="0.1" min="0" max="5" name="calificacion" value="0">
    </div>

    <div class="campo">
      <label>Capacidad</label>
      <input type="number" name="capacidad" min="0" value="0" readonly>
      <small>(Se calculará automáticamente basado en mesas × 4 personas/mesa)</small>
    </div>

    <div class="botones">
      <button type="submit" class="btn">Guardar</button>
      <a href="index.php" class="btn">Regresar</a>
    </div>

  </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const inputMesas = document.querySelector('input[name="numero_mesas"]');
  const inputCapacidad = document.querySelector('input[name="capacidad"]');

  function calcularCapacidad() {
    const mesas = parseInt(inputMesas.value) || 0;
    const personasPorMesa = 4; // Ajustable si necesitas cambiarlo
    const capacidad = mesas * personasPorMesa;
    inputCapacidad.value = capacidad;
  }

  inputMesas.addEventListener('input', calcularCapacidad);
  
  // Calcular inicial si hay valor por defecto
  calcularCapacidad();
});
</script>

<?php if ($mensaje): ?>
<script>
Swal.fire('<?php echo $es_error ? "Error" : "Éxito"; ?>', '<?php echo $mensaje; ?>', '<?php echo $es_error ? "error" : "success"; ?>');
</script>
<?php endif; ?>
</body>
</html>