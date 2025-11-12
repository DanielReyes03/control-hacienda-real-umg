<?php
require_once "../login/check_adminGer.php";
include("../db/conexion.php");

// Crear conexión
$conn = conectar();
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

$mensaje_success = '';
$mensaje_error = '';

// 🔹 Cargar lista de puestos
$puestos = [];
$query_puestos = "SELECT id, nombre FROM puestos ORDER BY nombre ASC";
$result_puestos = $conn->query($query_puestos);
if ($result_puestos && $result_puestos->num_rows > 0) {
    while ($row = $result_puestos->fetch_assoc()) {
        $puestos[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $puesto_id = intval($_POST['puesto_id'] ?? 0);
    $salario_base = floatval($_POST['salario_base'] ?? 0);
    $fecha_contratacion = trim($_POST['fecha_contratacion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $notas = trim($_POST['notas'] ?? '');
    $activo = intval($_POST['activo'] ?? 1);

    if (empty($nombre) || empty($cedula) || $puesto_id <= 0 || $salario_base <= 0 || empty($fecha_contratacion)) {
        $mensaje_error = 'Por favor, completa todos los campos requeridos correctamente.';
    } elseif (strlen($cedula) !== 13 || !ctype_digit($cedula)) {
        $mensaje_error = 'La cédula/DPI debe tener exactamente 13 dígitos numéricos.';
    } else {
        // Validar que el puesto exista
        $puesto_nombre = '';
        $stmt_puesto = $conn->prepare("SELECT nombre FROM puestos WHERE id = ?");
        $stmt_puesto->bind_param("i", $puesto_id);
        $stmt_puesto->execute();
        $stmt_puesto->bind_result($puesto_nombre);
        $stmt_puesto->fetch();
        $stmt_puesto->close();

        if (empty($puesto_nombre)) {
            $mensaje_error = 'El puesto seleccionado no existe.';
        } else {
            // Dividir nombre en nombres y apellidos
            $partes_nombre = explode(' ', $nombre, 2);
            $nombres = trim($partes_nombre[0] ?? '');
            $apellidos = trim($partes_nombre[1] ?? '');

            // Verificar duplicado por cédula
            $check_sql = "SELECT id FROM empleados WHERE cedula = ?";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("s", $cedula);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $mensaje_error = 'Ya existe un empleado con esa cédula/DPI.';
            } else {
                // Insertar empleado
                $sql = "INSERT INTO empleados 
                        (nombres, apellidos, dpi, puesto, salario, fecha_inicio, telefono, correo, notas, cedula, puesto_id, activo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssssdssssiii",
                    $nombres, $apellidos, $cedula, $puesto_nombre, $salario_base,
                    $fecha_contratacion, $telefono, $correo, $notas, $cedula,
                    $puesto_id, $activo
                );

                if ($stmt->execute()) {
                    $mensaje_success = 'Empleado creado exitosamente.';
                    header("Location: ver_empleados.php?success=" . urlencode($mensaje_success));
                    exit();
                } else {
                    $mensaje_error = 'Error al crear el empleado: ' . $stmt->error;
                }
                $stmt->close();
            }
            $stmt_check->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Nuevo Empleado - Hacienda Real</title>
  <link rel="stylesheet" href="emple.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php 
    include("../compartido/componentes/cabecera/index.php"); 
    cabecera("Crear Empleado"); 
  ?>

  <main class="contenido">
    <div class="formulario-contenedor">
      <h2>Crear Nuevo Empleado</h2>
      
      <?php if ($mensaje_error): ?>
        <div class="alerta error"><?php echo htmlspecialchars($mensaje_error); ?></div>
      <?php endif; ?>

      <form method="POST" action="" onsubmit="return validarCedula()">
        <div class="campo">
          <label for="nombre">Nombre Completo *</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="cedula">Cédula/DPI *</label>
          <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" required pattern="\d{13}" title="Debe tener exactamente 13 dígitos numéricos">
        </div>

        <div class="campo">
          <label for="puesto_id">Puesto *</label>
          <select id="puesto_id" name="puesto_id" required>
            <option value="">-- Selecciona un puesto --</option>
            <?php foreach ($puestos as $p): ?>
              <option value="<?php echo $p['id']; ?>" 
                <?php echo (isset($_POST['puesto_id']) && $_POST['puesto_id'] == $p['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($p['nombre']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="campo">
          <label for="salario_base">Salario Base *</label>
          <input type="number" id="salario_base" name="salario_base" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['salario_base'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="fecha_contratacion">Fecha de Contratación *</label>
          <input type="date" id="fecha_contratacion" name="fecha_contratacion" value="<?php echo htmlspecialchars($_POST['fecha_contratacion'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="telefono">Teléfono</label>
          <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
        </div>

        <div class="campo">
          <label for="correo">Correo Electrónico</label>
          <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>">
        </div>

        <div class="campo">
          <label for="activo">Estado *</label>
          <select id="activo" name="activo" required>
            <option value="1" <?php echo (($_POST['activo'] ?? 1) == 1) ? 'selected' : ''; ?>>Activo</option>
            <option value="0" <?php echo (($_POST['activo'] ?? 1) == 0) ? 'selected' : ''; ?>>Inactivo</option>
          </select>
        </div>

        <div class="campo">
          <label for="notas">Notas</label>
          <textarea id="notas" name="notas" rows="3"><?php echo htmlspecialchars($_POST['notas'] ?? ''); ?></textarea>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn-guardar">Guardar Empleado</button>
          <a href="ver_empleados.php" class="btn-cancelar">Cancelar</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    function validarCedula() {
      const cedula = document.getElementById('cedula').value.trim();
      if (cedula.length !== 13 || !/^\d{13}$/.test(cedula)) {
        Swal.fire({
          title: 'Error',
          text: 'La cédula/DPI debe tener exactamente 13 dígitos numéricos.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        return false;
      }
      return true;
    }
  </script>

  <?php if ($mensaje_success): ?>
  <script>
    Swal.fire({
      title: 'Éxito',
      text: '<?php echo htmlspecialchars($mensaje_success); ?>',
      icon: 'success',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'ver_empleados.php';
    });
  </script>
  <?php endif; ?>

  <?php $conn->close(); ?>
</body>
</html>