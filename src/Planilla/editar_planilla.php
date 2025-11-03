<?php
// Configuración de la base de datos
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Revisar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener ID de la planilla a editar
$id = intval($_GET['id'] ?? 0);
$mensaje_success = '';
$mensaje_error = '';

// Obtener datos actuales de la planilla
$datos_planilla = null;
if ($id > 0) {
    $sql_select = "SELECT * FROM planilla WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result();
    $datos_planilla = $resultado->fetch_assoc();
    $stmt_select->close();

    if (!$datos_planilla) {
        $mensaje_error = 'Planilla no encontrada. Verifica el ID.';
    }
}

// Manejo de formulario (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
    $empleado_id = intval($_POST['empleado_id'] ?? 0);
    $puesto = trim($_POST['puesto'] ?? '');
    $periodo_inicio = $_POST['periodo_inicio'] ?? '';
    $periodo_fin = $_POST['periodo_fin'] ?? '';
    $sueldo_bruto = floatval($_POST['sueldo_bruto'] ?? 0);
    $deducciones = floatval($_POST['deducciones'] ?? 0);
    $sueldo_neto = $sueldo_bruto - $deducciones;
    $fecha_pago = $_POST['fecha_pago'] ?? null;
    $notas = trim($_POST['notas'] ?? '');

    // Validaciones básicas
    if ($empleado_id <= 0 || empty($puesto) || empty($periodo_inicio) || empty($periodo_fin) || $sueldo_bruto <= 0) {
        $mensaje_error = 'Por favor, completa todos los campos obligatorios correctamente.';
    } else {
        // Validación FK: Verificar si empleado_id existe
        $check_sql = "SELECT id FROM empleados WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $empleado_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            $mensaje_error = "Error: El ID de empleado '$empleado_id' no existe en la tabla de empleados.";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            try {
                $sql = "UPDATE planilla SET empleado_id = ?, puesto = ?, periodo_inicio = ?, periodo_fin = ?, sueldo_bruto = ?, deducciones = ?, sueldo_neto = ?, fecha_pago = ?, notas = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error en prepare: " . $conn->error);
                }
                $stmt->bind_param("isssddissi", $empleado_id, $puesto, $periodo_inicio, $periodo_fin, $sueldo_bruto, $deducciones, $sueldo_neto, $fecha_pago, $notas, $id);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $mensaje_success = 'Planilla actualizada exitosamente.';
                    } else {
                        $mensaje_error = 'No se realizaron cambios o planilla no encontrada.';
                    }
                } else {
                    throw new Exception("Error en execute: " . $stmt->error);
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                    $mensaje_error = "Error de clave foránea: El ID de empleado no es válido.";
                } else {
                    $mensaje_error = 'Error SQL: ' . $e->getMessage();
                }
            } catch (Exception $e) {
                $mensaje_error = 'Error general: ' . $e->getMessage();
            }
        }
    }
}

// Prefill con datos actuales si no hay POST
if ($datos_planilla) {
    $empleado_id = $datos_planilla['empleado_id'];
    $puesto = $datos_planilla['puesto'];
    $periodo_inicio = $datos_planilla['periodo_inicio'];
    $periodo_fin = $datos_planilla['periodo_fin'];
    $sueldo_bruto = $datos_planilla['sueldo_bruto'];
    $deducciones = $datos_planilla['deducciones'];
    $sueldo_neto = $datos_planilla['sueldo_neto'];
    $fecha_pago = $datos_planilla['fecha_pago'];
    $notas = $datos_planilla['notas'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Planilla - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Editar Planilla");
  ?>

  <main class="contenido">
    <div style="text-align: center; margin-bottom: 30px;">
      <a href="index.php" class="btn-regresar">Regresar a Planillas</a>
    </div>

    <?php if ($mensaje_success): ?>
      <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        <?php echo htmlspecialchars($mensaje_success); ?>
      </div>
    <?php endif; ?>

    <?php if ($mensaje_error): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        <?php echo htmlspecialchars($mensaje_error); ?>
      </div>
    <?php endif; ?>

    <?php if (!$datos_planilla): ?>
      <div style="text-align: center; color: #721c24; padding: 20px;">
        <h3>Planilla no encontrada</h3>
        <a href="index.php" class="btn-regresar">Volver a la lista</a>
      </div>
    <?php else: ?>
    <form method="POST" action="" style="max-width: 600px; margin: 0 auto; background-color: var(--blanco); padding: 30px; border-radius: 15px; box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
      <h2 style="text-align: center; color: var(--rojo); margin-bottom: 25px;">Editar Planilla ID: <?php echo htmlspecialchars($id); ?></h2>

      <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em;">
        <strong>Tip:</strong> Usa un ID de empleado existente (verifícalo en el módulo de Empleados).
      </div>

      <div style="margin-bottom: 20px;">
        <label for="empleado_id" style="display: block; margin-bottom: 5px; font-weight: 600;">ID Empleado:</label>
        <input type="number" id="empleado_id" name="empleado_id" value="<?php echo htmlspecialchars($empleado_id); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="puesto" style="display: block; margin-bottom: 5px; font-weight: 600;">Puesto:</label>
        <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($puesto); ?>" placeholder="Ej: Mesero, Chef, Gerente" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="periodo_inicio" style="display: block; margin-bottom: 5px; font-weight: 600;">Período Inicio:</label>
        <input type="date" id="periodo_inicio" name="periodo_inicio" value="<?php echo htmlspecialchars($periodo_inicio); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="periodo_fin" style="display: block; margin-bottom: 5px; font-weight: 600;">Período Fin:</label>
        <input type="date" id="periodo_fin" name="periodo_fin" value="<?php echo htmlspecialchars($periodo_fin); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="sueldo_bruto" style="display: block; margin-bottom: 5px; font-weight: 600;">Sueldo Bruto:</label>
        <input type="number" id="sueldo_bruto" name="sueldo_bruto" step="0.01" value="<?php echo htmlspecialchars($sueldo_bruto); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="deducciones" style="display: block; margin-bottom: 5px; font-weight: 600;">Deducciones:</label>
        <input type="number" id="deducciones" name="deducciones" step="0.01" value="<?php echo htmlspecialchars($deducciones); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="sueldo_neto" style="display: block; margin-bottom: 5px; font-weight: 600;">Sueldo Neto (Calculado):</label>
        <input type="number" id="sueldo_neto" name="sueldo_neto" step="0.01" readonly value="<?php echo number_format($sueldo_neto, 2); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background-color: #f8f9fa; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="fecha_pago" style="display: block; margin-bottom: 5px; font-weight: 600;">Fecha de Pago:</label>
        <input type="date" id="fecha_pago" name="fecha_pago" value="<?php echo htmlspecialchars($fecha_pago); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 25px;">
        <label for="notas" style="display: block; margin-bottom: 5px; font-weight: 600;">Notas:</label>
        <textarea id="notas" name="notas" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($notas); ?></textarea>
      </div>

      <div style="text-align: center;">
        <button type="submit" style="background-color: var(--amarillo); color: var(--blanco); padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.3s ease;">Actualizar Planilla</button>
      </div>
    </form>
    <?php endif; ?>
  </main>

  <script>
    // Actualizar sueldo neto en tiempo real
    document.getElementById('sueldo_bruto').addEventListener('input', calcularNeto);
    document.getElementById('deducciones').addEventListener('input', calcularNeto);

    function calcularNeto() {
      const bruto = parseFloat(document.getElementById('sueldo_bruto').value) || 0;
      const deducciones = parseFloat(document.getElementById('deducciones').value) || 0;
      document.getElementById('sueldo_neto').value = (bruto - deducciones).toFixed(2);
    }

    <?php if ($mensaje_success): ?>
    Swal.fire({
      title: 'Éxito',
      text: '<?php echo htmlspecialchars($mensaje_success); ?>',
      icon: 'success',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'index.php?success=Planilla actualizada';
    });
    <?php endif; ?>

    <?php if ($mensaje_error): ?>
    Swal.fire({
      title: 'Error',
      text: '<?php echo htmlspecialchars($mensaje_error); ?>',
      icon: 'error',
      confirmButtonText: 'OK'
    });
    <?php endif; ?>
  </script>
</body>
</html>
<?php $conn->close(); ?>