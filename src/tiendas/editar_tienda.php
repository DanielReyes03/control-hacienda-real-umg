<?php
require_once "../login/check_adminGer.php";
include("../db/conexion.php");
$conn = conectar();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?error=ID inválido'); exit; }

$stmt = $conn->prepare("SELECT * FROM sucursales WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$datos) { header('Location: index.php?error=Sucursal no encontrada'); exit; }

// 🔹 Obtener lista de empleados para el combo de gerente
$empleados = [];
$result = $conn->query("SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM empleados WHERE activo = 1 ORDER BY nombres ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $empleados[] = $row;
  }
}

$mensaje = '';
$es_error = false;

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
  if ($calificacion !== '' && (!is_numeric($calificacion) || $calificacion < 0 || $calificacion > 5)) $errores[] = 'La calificación debe estar entre 0 y 5.';
  if ($capacidad !== '' && !is_numeric($capacidad)) $errores[] = 'Capacidad debe ser numérico.';

  if (empty($errores)) {
    // Si gerente_id está vacío, se pasa NULL
    $gerente_id = ($gerente_id === '') ? NULL : $gerente_id;

    $stmt = $conn->prepare("UPDATE sucursales 
      SET nombre=?, direccion=?, gerente_id=?, telefono=?, numero_mesas=?, horarios=?, caracteristicas=?, calificacion=?, capacidad=? 
      WHERE id=?");
    $stmt->bind_param("ssisissdii", $nombre, $direccion, $gerente_id, $telefono, $numero_mesas, $horarios, $caracteristicas, $calificacion, $capacidad, $id);

    if ($stmt->execute()) {
      header('Location: index.php?success=Sucursal actualizada exitosamente');
      exit;
    } else {
      $errores[] = 'Error al actualizar: ' . $conn->error;
    }
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
  <title>Editar Sucursal</title>
  <link rel="stylesheet" href="crear.css">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
include("../compartido/componentes/cabecera/index.php");
cabecera("Editar Sucursal");
?>

<main class="contenido">
  <form method="POST" class="formulario">

    <div class="campo">
      <label>Nombre *</label>
      <input type="text" name="nombre" required value="<?= htmlspecialchars($datos['nombre'] ?? ''); ?>">
    </div>

    <div class="campo">
      <label>Dirección</label>
      <input type="text" name="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? ''); ?>">
    </div>

    <div class="campo">
      <label>Gerente</label>
      <select name="gerente_id">
        <option value="">-- Seleccione un Gerente --</option>
        <?php foreach ($empleados as $emp): 
          $selected = ($emp['id'] == ($datos['gerente_id'] ?? null)) ? 'selected' : '';
        ?>
          <option value="<?= $emp['id'] ?>" <?= $selected ?>><?= htmlspecialchars($emp['nombre_completo']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="campo">
      <label>Teléfono</label>
      <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? ''); ?>">
    </div>

    <div class="campo">
      <label>Número de Mesas</label>
      <input type="number" name="numero_mesas" min="0" value="<?= htmlspecialchars($datos['numero_mesas'] ?? '0'); ?>">
    </div>

    <div class="campo">
      <label>Horarios</label>
      <input type="text" name="horarios" placeholder="Ejemplo: Lunes a Domingo de 8am a 10pm" value="<?= htmlspecialchars($datos['horarios'] ?? ''); ?>">
    </div>

    <div class="campo">
      <label>Características</label>
      <textarea name="caracteristicas" rows="3" placeholder="Ejemplo: Restaurante familiar, con terraza y parqueo."><?= htmlspecialchars($datos['caracteristicas'] ?? ''); ?></textarea>
    </div>

    <div class="campo">
      <label>Calificación (0 - 5)</label>
      <input type="number" step="0.1" min="0" max="5" name="calificacion" value="<?= htmlspecialchars($datos['calificacion'] ?? '0'); ?>">
    </div>

    <div class="campo">
      <label>Capacidad</label>
      <input type="number" name="capacidad" min="0" value="<?= htmlspecialchars($datos['capacidad'] ?? '0'); ?>">
      <small>(Se calculará automáticamente basado en mesas × 4 personas/mesa)</small>
    </div>

    <div class="botones">
      <button type="submit" class="btn">Actualizar</button>
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