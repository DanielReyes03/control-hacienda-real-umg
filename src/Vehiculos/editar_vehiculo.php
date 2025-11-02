<?php
require_once "../login/check_adminGer.php";
// Configuración de la base de datos
include("../db/conexion.php");
$conn = conectar();
$id = intval($_GET['id'] ?? 0);
$vehiculo = null;
$mensaje = '';
$es_error = false;

if ($id <= 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

// Obtener datos actuales del vehículo
$stmt = $conn->prepare("SELECT * FROM vehiculos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vehiculo = $result->fetch_assoc();
$stmt->close();

if (!$vehiculo) {
    header('Location: index.php?error=Vehículo no encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sucursal_id = trim($_POST['sucursal_id']);
    $placa = trim($_POST['placa']);
    $modelo = trim($_POST['modelo']);
    $capacidad = trim($_POST['capacidad']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $notas = trim($_POST['notas']);

    if (empty($sucursal_id) || empty($placa) || empty($modelo)) {
        $mensaje = 'Sucursal, placa y modelo son requeridos.';
        $es_error = true;
    } else {
        // Actualizar el vehículo
        $stmt = $conn->prepare("
            UPDATE vehiculos
            SET sucursal_id = ?, placa = ?, modelo = ?, capacidad = ?, activo = ?, notas = ?
            WHERE id = ?
        ");
        $stmt->bind_param("isssisi", $sucursal_id, $placa, $modelo, $capacidad, $activo, $notas, $id);

        if ($stmt->execute()) {
            header('Location: index.php?success=Vehículo actualizado exitosamente');
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
  <title>Editar Vehículo - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Editar Vehículo", "Vehiculos");
  ?>

  <main class="contenido">
    <form method="POST" class="formulario" id="formulario">
      <div class="campo">
        <label for="sucursal_id">Sucursal *</label>
        <select id="sucursal_id" name="sucursal_id" required>
          <option value="">Selecciona una sucursal</option>
          <?php
          $conn = conectar();
          $sql = "SELECT id, nombre FROM sucursales ORDER BY nombre";
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = ($row['id'] == $vehiculo['sucursal_id']) ? 'selected' : '';
              echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['nombre']) . "</option>";
            }
          }
          $conn->close();
          ?>
        </select>
      </div>

      <div class="campo">
        <label for="placa">Placa *</label>
        <input type="text" id="placa" name="placa" required value="<?php echo htmlspecialchars($vehiculo['placa'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="modelo">Modelo *</label>
        <input type="text" id="modelo" name="modelo" required value="<?php echo htmlspecialchars($vehiculo['modelo'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="capacidad">Capacidad</label>
        <input type="text" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($vehiculo['capacidad'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="activo">Activo</label>
        <input type="checkbox" id="activo" name="activo" <?php echo ($vehiculo['activo'] ? 'checked' : ''); ?>>
      </div>

      <div class="campo">
        <label for="notas">Notas</label>
        <textarea id="notas" name="notas"><?php echo htmlspecialchars($vehiculo['notas'] ?? ''); ?></textarea>
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
        text: "Se actualizará el vehículo con la información proporcionada",
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
