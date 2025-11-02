<?php
include("../db/conexion.php");
$conn = conectar();
$mensaje = '';
$es_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura de datos del formulario
    $sucursal_id = trim($_POST['sucursal_id']);
    $placa = trim($_POST['placa']);
    $modelo = trim($_POST['modelo']);
    $capacidad = trim($_POST['capacidad']);
    $notas = trim($_POST['notas']);

    // Validación básica
    if (empty($sucursal_id) || empty($placa) || empty($modelo)) {
        $mensaje = 'Sucursal, placa y modelo son requeridos.';
        $es_error = true;
    } else {
        // Preparamos el INSERT con todas las columnas correctas
        $stmt = $conn->prepare("
            INSERT INTO vehiculos
            (sucursal_id, placa, modelo, capacidad, activo, notas)
            VALUES (?, ?, ?, ?, 1, ?)
        ");

        // Vinculamos los parámetros
        $stmt->bind_param("issss", $sucursal_id, $placa, $modelo, $capacidad, $notas);

        // Ejecutamos con manejo de excepciones
        try {
            if ($stmt->execute()) {
                header('Location: index.php?success=Vehículo creado exitosamente');
                exit;
            } else {
                $mensaje = 'Error al crear: ' . $stmt->error;
                $es_error = true;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Código de error para entrada duplicada
                $mensaje = 'La placa ya existe. Por favor, ingrese una placa única.';
            } else {
                $mensaje = 'Error al crear: ' . $e->getMessage();
            }
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
  <title>Crear Vehículo - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Crear Nuevo Vehículo", "Vehiculos");
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
              echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nombre']) . "</option>";
            }
          }
          $conn->close();
          ?>
        </select>
      </div>
      <div class="campo">
        <label for="placa">Placa *</label>
        <input type="text" id="placa" name="placa" required value="<?php echo isset($placa) ? htmlspecialchars($placa) : ''; ?>">
      </div>
      <div class="campo">
        <label for="modelo">Modelo *</label>
        <input type="text" id="modelo" name="modelo" required value="<?php echo isset($modelo) ? htmlspecialchars($modelo) : ''; ?>">
      </div>
      <div class="campo">
        <label for="capacidad">Capacidad</label>
        <input type="text" id="capacidad" name="capacidad" value="<?php echo isset($capacidad) ? htmlspecialchars($capacidad) : ''; ?>">
      </div>
      <div class="campo">
        <label for="notas">Notas</label>
        <textarea id="notas" name="notas"><?php echo isset($notas) ? htmlspecialchars($notas) : ''; ?></textarea>
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
        text: "Se creará el Vehículo con la información proporcionada",
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
