<?php
// procesar_empleado.php - Versión CORREGIDA para campos requeridos NOT NULL
// Incluye 'nombres', 'apellidos', 'dpi' en INSERT (mapea desde input)
// Asume split simple de 'nombre': primera palabra a 'nombres', resto a 'apellidos'
// Mapea 'cedula' a 'dpi' (campo original)
// Agrega 'activo' = 1 y 'puesto_id' = 1 (default; ajusta si tienes tabla puestos)
// AGREGADO: Campos teléfono y correo (de la tabla ver_empleados.php)
// AGREGADO: Campo "Estado" (activo) editable en el formulario (default Activo)
// AGREGADO: Validación para Cédula/DPI: exactamente 13 dígitos (cliente y servidor)

$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje_success = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $salario_base = floatval($_POST['salario_base'] ?? 0);
    $fecha_contratacion = trim($_POST['fecha_contratacion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $notas = trim($_POST['notas'] ?? '');
    $activo = intval($_POST['activo'] ?? 1); // Nuevo: Estado desde formulario (1=Activo, 0=Inactivo)

    if (empty($nombre) || empty($cedula) || empty($puesto) || $salario_base <= 0 || empty($fecha_contratacion)) {
        $mensaje_error = 'Por favor, completa todos los campos requeridos correctamente.';
    } elseif (strlen($cedula) !== 13 || !ctype_digit($cedula)) {
        $mensaje_error = 'La cédula/DPI debe tener exactamente 13 dígitos numéricos.';
    } else {
        // Split nombre: primera palabra a 'nombres', resto a 'apellidos'
        $partes_nombre = explode(' ', $nombre, 2);
        $nombres = trim($partes_nombre[0] ?? '');
        $apellidos = trim($partes_nombre[1] ?? '');

        // Verificar duplicado por cedula (o dpi)
        $check_sql = "SELECT id FROM empleados WHERE cedula = ? OR dpi = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("ss", $cedula, $cedula);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $mensaje_error = 'Ya existe un empleado con esa cédula/DPI.';
        } else {
            // INSERT completo con TODOS los campos requeridos, incluyendo telefono, correo y activo editable
            $sql = "INSERT INTO empleados (nombres, apellidos, dpi, puesto, salario, fecha_inicio, telefono, correo, notas, cedula, nombre, puesto_id, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
            $stmt = $conn->prepare($sql);
            // bind: s s s s d s s s s s s i (11 strings/double + activo i)
            $stmt->bind_param("ssssdssssssi", $nombres, $apellidos, $cedula, $puesto, $salario_base, $fecha_contratacion, $telefono, $correo, $notas, $cedula, $nombre, $activo);

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
          <label for="nombre">Nombre Completo * (ej: Juan Carlos Pérez López)</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
          <small>Se dividirá en nombres y apellidos automáticamente.</small>
        </div>

        <div class="campo">
          <label for="cedula">Cédula/DPI * (exactamente 13 dígitos)</label>
          <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" required pattern="\d{13}" title="Debe tener exactamente 13 dígitos numéricos">
        </div>

        <div class="campo">
          <label for="puesto">Puesto *</label>
          <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($_POST['puesto'] ?? ''); ?>" required>
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
          <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" placeholder="Ej: +502 5550-1234">
        </div>

        <div class="campo">
          <label for="correo">Correo Electrónico</label>
          <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" placeholder="Ej: empleado@haciendareal.com">
        </div>

        <div class="campo">
          <label for="activo">Estado *</label>
          <select id="activo" name="activo" required>
            <option value="1" <?php echo (($_POST['activo'] ?? 1) == 1) ? 'selected' : ''; ?>>Activo</option>
            <option value="0" <?php echo (($_POST['activo'] ?? 1) == 0) ? 'selected' : ''; ?>>Inactivo</option>
          </select>
          <small>El estado determina si el empleado aparece en las planillas activas.</small>
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
    // Validación cliente-side para cédula/DPI (13 dígitos exactos)
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

    // Validación en tiempo real (opcional, para feedback inmediato)
    document.getElementById('cedula').addEventListener('input', function() {
      const cedula = this.value.trim();
      if (cedula.length > 0 && (cedula.length !== 13 || !/^\d{13}$/.test(cedula))) {
        this.style.borderColor = '#dc3545';
      } else {
        this.style.borderColor = '#28a745';
      }
    });
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