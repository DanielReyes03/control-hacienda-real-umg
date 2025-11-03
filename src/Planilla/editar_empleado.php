<?php
require_once "../login/check_adminGer.php";
// editar_empleado.php - Formulario para editar un empleado existente
// Carga datos por ID, permite edición y actualiza en DB

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
$empleado = null;

// Obtener ID del empleado
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: ver_empleados.php?error=" . urlencode("ID de empleado inválido."));
    exit();
}

// Cargar datos del empleado si no es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $sql = "SELECT * FROM empleados WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        header("Location: ver_empleados.php?error=" . urlencode("Empleado no encontrado."));
        exit();
    }
    
    $empleado = $result->fetch_assoc();
    $stmt->close();
} else {
    // Procesar actualización
    $nombre = trim($_POST['nombre'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $salario_base = floatval($_POST['salario_base'] ?? 0);
    $fecha_contratacion = trim($_POST['fecha_contratacion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $notas = trim($_POST['notas'] ?? '');
    $activo = intval($_POST['activo'] ?? 1);

    if (empty($nombre) || empty($cedula) || empty($puesto) || $salario_base <= 0 || empty($fecha_contratacion)) {
        $mensaje_error = 'Por favor, completa todos los campos requeridos correctamente.';
    } else {
        // Split nombre: primera palabra a 'nombres', resto a 'apellidos'
        $partes_nombre = explode(' ', $nombre, 2);
        $nombres = trim($partes_nombre[0] ?? '');
        $apellidos = trim($partes_nombre[1] ?? '');

        // Verificar duplicado (excluyendo el actual ID)
        $check_sql = "SELECT id FROM empleados WHERE (cedula = ? OR dpi = ?) AND id != ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("ssi", $cedula, $cedula, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $mensaje_error = 'Ya existe otro empleado con esa cédula/DPI.';
        } else {
            // UPDATE con campos editables
            $sql = "UPDATE empleados SET nombres = ?, apellidos = ?, dpi = ?, puesto = ?, salario = ?, fecha_inicio = ?, telefono = ?, correo = ?, notas = ?, cedula = ?, nombre = ?, activo = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssdssssssii", $nombres, $apellidos, $cedula, $puesto, $salario_base, $fecha_contratacion, $telefono, $correo, $notas, $cedula, $nombre, $activo, $id);

            if ($stmt->execute()) {
                $mensaje_success = 'Empleado actualizado exitosamente.';
                header("Location: ver_empleados.php?success=" . urlencode($mensaje_success));
                exit();
            } else {
                $mensaje_error = 'Error al actualizar el empleado: ' . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}

// Si no hay datos cargados, redirigir
if (!$empleado) {
    header("Location: ver_empleados.php?error=" . urlencode("Error al cargar datos del empleado."));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Empleado - Hacienda Real</title>
  <link rel="stylesheet" href="emple.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php 
    include("../compartido/componentes/cabecera/index.php"); 
    cabecera("Editar Empleado"); 
  ?>

  <main class="contenido">
    <div class="formulario-contenedor">
      <h2>Editar Empleado (ID: <?php echo htmlspecialchars($id); ?>)</h2>
      
      <?php if ($mensaje_error): ?>
        <div class="alerta error"><?php echo htmlspecialchars($mensaje_error); ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="campo">
          <label for="nombre">Nombre Completo * (ej: Juan Carlos Pérez López)</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre'] ?? ''); ?>" required>
          <small>Se dividirá en nombres y apellidos automáticamente.</small>
        </div>

        <div class="campo">
          <label for="cedula">Cédula/DPI *</label>
          <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($empleado['dpi'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="puesto">Puesto *</label>
          <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($empleado['puesto'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="salario_base">Salario Base *</label>
          <input type="number" id="salario_base" name="salario_base" step="0.01" min="0" value="<?php echo htmlspecialchars($empleado['salario'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="fecha_contratacion">Fecha de Contratación *</label>
          <input type="date" id="fecha_contratacion" name="fecha_contratacion" value="<?php echo htmlspecialchars($empleado['fecha_inicio'] ?? ''); ?>" required>
        </div>

        <div class="campo">
          <label for="telefono">Teléfono</label>
          <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?>" placeholder="Ej: +502 5550-1234">
        </div>

        <div class="campo">
          <label for="correo">Correo Electrónico</label>
          <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($empleado['correo'] ?? ''); ?>" placeholder="Ej: empleado@haciendareal.com">
        </div>

        <div class="campo">
          <label for="activo">Estado *</label>
          <select id="activo" name="activo" required>
            <option value="1" <?php echo ($empleado['activo'] ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
            <option value="0" <?php echo ($empleado['activo'] ?? 1) == 0 ? 'selected' : ''; ?>>Inactivo</option>
          </select>
        </div>

        <div class="campo">
          <label for="notas">Notas</label>
          <textarea id="notas" name="notas" rows="3"><?php echo htmlspecialchars($empleado['notas'] ?? ''); ?></textarea>
        </div>

        <div class="acciones-form">
          <button type="submit" class="btn-guardar">Actualizar Empleado</button>
          <a href="ver_empleados.php" class="btn-cancelar">Cancelar</a>
        </div>
      </form>
    </div>
  </main>

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