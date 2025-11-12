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
  $num_resenas = trim($_POST['num_resenas']);
  $capacidad = trim($_POST['capacidad']);

  $errores = [];
  if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
  if ($numero_mesas !== '' && !is_numeric($numero_mesas)) $errores[] = 'Número de mesas debe ser numérico.';
  if ($calificacion !== '' && (!is_numeric($calificacion) || $calificacion < 0 || $calificacion > 5)) $errores[] = 'La calificación debe estar entre 0 y 5.';
  if ($num_resenas !== '' && !is_numeric($num_resenas)) $errores[] = 'Número de reseñas debe ser numérico.';
  if ($capacidad !== '' && !is_numeric($capacidad)) $errores[] = 'Capacidad debe ser numérico.';

  if (empty($errores)) {
    $stmt = $conn->prepare("UPDATE sucursales 
      SET nombre=?, direccion=?, gerente_id=?, telefono=?, numero_mesas=?, horarios=?, caracteristicas=?, calificacion=?, num_resenas=?, capacidad=? 
      WHERE id=?");
    $stmt->bind_param("ssisisdsiii", $nombre, $direccion, $gerente_id, $telefono, $numero_mesas, $horarios, $caracteristicas, $calificacion, $num_resenas, $capacidad, $id);

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
  <link rel="stylesheet" href="styles.css">
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
    <label>Nombre *</label>
    <input type="text" name="nombre" required value="<?= htmlspecialchars($datos['nombre']); ?>">

    <label>Dirección</label>
    <input type="text" name="direccion" value="<?= htmlspecialchars($datos['direccion']); ?>">

    <label>ID del Gerente</label>
    <input type="number" name="gerente_id" value="<?= htmlspecialchars($datos['gerente_id']); ?>">

    <label>Teléfono</label>
    <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono']); ?>">

    <label>Número de Mesas</label>
    <input type="number" name="numero_mesas" value="<?= htmlspecialchars($datos['numero_mesas']); ?>">

    <label>Horarios</label>
    <input type="text" name="horarios" value="<?= htmlspecialchars($datos['horarios']); ?>">

    <label>Características</label>
    <textarea name="caracteristicas"><?= htmlspecialchars($datos['caracteristicas']); ?></textarea>

    <label>Calificación (0-5)</label>
    <input type="number" step="0.1" name="calificacion" value="<?= htmlspecialchars($datos['calificacion']); ?>">

    <label>Número de Reseñas</label>
    <input type="number" name="num_resenas" value="<?= htmlspecialchars($datos['num_resenas']); ?>">

    <label>Capacidad</label>
    <input type="number" name="capacidad" value="<?= htmlspecialchars($datos['capacidad']); ?>">

    <button type="submit" class="btn-guardar">Actualizar</button>
    <a href="index.php" class="btn-regresar">Regresar</a>
  </form>
</main>

<?php if ($mensaje): ?>
<script>
Swal.fire('<?php echo $es_error ? "Error" : "Éxito"; ?>', '<?php echo $mensaje; ?>', '<?php echo $es_error ? "error" : "success"; ?>');
</script>
<?php endif; ?>
</body>
</html>
