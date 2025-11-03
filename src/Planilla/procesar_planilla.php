<?php
require_once "../login/check_adminGer.php";
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

// Manejo de AJAX: Fetch empleado si se solicita
if (isset($_GET['fetch_employee']) && !empty($_GET['fetch_employee'])) {
    $empleado_id = intval($_GET['fetch_employee']);
    $sql = "SELECT nombre, puesto, salario, fecha_inicio FROM empleados WHERE id = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
        // Calcular periodo sugerido basado en fecha_inicio (ej: mes actual)
        $periodo_inicio = date('Y-m-01'); // Primer día del mes actual
        $periodo_fin = date('Y-m-t');     // Último día del mes actual
        $fecha_pago = date('Y-m-d', strtotime('+1 day')); // Sugerir pago mañana
        
        echo json_encode([
            'success' => true,
            'data' => [
                'nombre' => $empleado['nombre'],
                'puesto' => $empleado['puesto'],
                'sueldo_bruto' => $empleado['salario'],
                'periodo_inicio' => $periodo_inicio,
                'periodo_fin' => $periodo_fin,
                'fecha_pago' => $fecha_pago
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Empleado no encontrado o inactivo.']);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Manejo de formulario
$mensaje_success = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado_id = intval($_POST['empleado_id'] ?? 0);
    $puesto = trim($_POST['puesto'] ?? '');
    $periodo_inicio = $_POST['periodo_inicio'] ?? '';
    $periodo_fin = $_POST['periodo_fin'] ?? '';
    $sueldo_bruto = floatval($_POST['sueldo_bruto'] ?? 0);
    $deducciones = floatval($_POST['deducciones'] ?? 0);
    $sueldo_neto = $sueldo_bruto - $deducciones; // Calcular sueldo neto
    $fecha_pago = $_POST['fecha_pago'] ?? null;
    $notas = trim($_POST['notas'] ?? '');

    // Validaciones básicas
    if ($empleado_id <= 0 || empty($puesto) || empty($periodo_inicio) || empty($periodo_fin) || $sueldo_bruto <= 0) {
        $mensaje_error = 'Por favor, completa todos los campos obligatorios correctamente.';
    } else {
        // Validación FK: Verificar si empleado_id existe en la tabla empleados
        $check_sql = "SELECT id FROM empleados WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $empleado_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            $mensaje_error = "Error: El ID de empleado '$empleado_id' no existe en la tabla de empleados. Verifica el ID o crea el empleado primero en el módulo de Empleados.";
            $check_stmt->close();
        } else {
            $check_stmt->close();
            try {
                $sql = "INSERT INTO planilla (empleado_id, puesto, periodo_inicio, periodo_fin, sueldo_bruto, deducciones, sueldo_neto, fecha_pago, notas) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error en prepare: " . $conn->error);
                }
                $stmt->bind_param("isssddiss", $empleado_id, $puesto, $periodo_inicio, $periodo_fin, $sueldo_bruto, $deducciones, $sueldo_neto, $fecha_pago, $notas);

                if ($stmt->execute()) {
                    $mensaje_success = 'Planilla creada exitosamente.';
                    header("Location: index.php?success=" . urlencode($mensaje_success));
                    exit();
                } else {
                    throw new Exception("Error en execute: " . $stmt->error);
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                    $mensaje_error = "Error de clave foránea: El ID de empleado no es válido. Verifica que exista en 'empleados'.";
                } else {
                    $mensaje_error = 'Error SQL: ' . $e->getMessage();
                }
            } catch (Exception $e) {
                $mensaje_error = 'Error general: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Planilla - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Crear Planilla");
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

    <form method="POST" action="" style="max-width: 600px; margin: 0 auto; background-color: var(--blanco); padding: 30px; border-radius: 15px; box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
      <h2 style="text-align: center; color: var(--rojo); margin-bottom: 25px;">Nueva Planilla</h2>

      <!-- Tip para el usuario sobre empleados -->
      <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em;">
        <strong>Tip:</strong> Ingresa el ID de empleado. Los campos se autocompletarán automáticamente (puesto, sueldo, período, etc.).
      </div>

      <div style="margin-bottom: 20px;">
        <label for="empleado_id" style="display: block; margin-bottom: 5px; font-weight: 600;">ID Empleado:</label>
        <input type="number" id="empleado_id" name="empleado_id" value="<?php echo htmlspecialchars($_POST['empleado_id'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;" placeholder="Ej: 1">
        <div id="info_empleado" style="margin-top: 5px; font-size: 0.9em; color: #666; font-style: italic;"></div>
      </div>

      <div style="margin-bottom: 20px;">
        <label for="puesto" style="display: block; margin-bottom: 5px; font-weight: 600;">Puesto:</label>
        <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($_POST['puesto'] ?? ''); ?>" placeholder="Ej: Mesero, Chef, Gerente" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="periodo_inicio" style="display: block; margin-bottom: 5px; font-weight: 600;">Período Inicio:</label>
        <input type="date" id="periodo_inicio" name="periodo_inicio" value="<?php echo htmlspecialchars($_POST['periodo_inicio'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="periodo_fin" style="display: block; margin-bottom: 5px; font-weight: 600;">Período Fin:</label>
        <input type="date" id="periodo_fin" name="periodo_fin" value="<?php echo htmlspecialchars($_POST['periodo_fin'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="sueldo_bruto" style="display: block; margin-bottom: 5px; font-weight: 600;">Sueldo Bruto:</label>
        <input type="number" id="sueldo_bruto" name="sueldo_bruto" step="0.01" value="<?php echo htmlspecialchars($_POST['sueldo_bruto'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="deducciones" style="display: block; margin-bottom: 5px; font-weight: 600;">Deducciones:</label>
        <input type="number" id="deducciones" name="deducciones" step="0.01" value="<?php echo htmlspecialchars($_POST['deducciones'] ?? '0'); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="sueldo_neto" style="display: block; margin-bottom: 5px; font-weight: 600;">Sueldo Neto (Calculado):</label>
        <input type="number" id="sueldo_neto" name="sueldo_neto" step="0.01" readonly value="<?php echo number_format($sueldo_bruto - $deducciones ?? 0, 2); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background-color: #f8f9fa; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label for="fecha_pago" style="display: block; margin-bottom: 5px; font-weight: 600;">Fecha de Pago:</label>
        <input type="date" id="fecha_pago" name="fecha_pago" value="<?php echo htmlspecialchars($_POST['fecha_pago'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 25px;">
        <label for="notas" style="display: block; margin-bottom: 5px; font-weight: 600;">Notas:</label>
        <textarea id="notas" name="notas" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($_POST['notas'] ?? ''); ?></textarea>
      </div>

      <div style="text-align: center;">
        <button type="submit" style="background-color: var(--amarillo); color: var(--blanco); padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.3s ease;">Crear Planilla</button>
      </div>
    </form>
  </main>

  <script>
    // Auto-fill al cambiar ID de empleado
    document.getElementById('empleado_id').addEventListener('blur', fetchEmpleado);
    document.getElementById('empleado_id').addEventListener('change', fetchEmpleado); // También en change por si usan enter

    function fetchEmpleado() {
      const idInput = document.getElementById('empleado_id');
      const id = parseInt(idInput.value);
      const infoDiv = document.getElementById('info_empleado');

      if (id <= 0) {
        infoDiv.innerHTML = '';
        return;
      }

      // Mostrar loading
      infoDiv.innerHTML = 'Cargando datos del empleado...';

      // AJAX request
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `?fetch_employee=${id}`, true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
              // Llenar campos
              document.getElementById('puesto').value = response.data.puesto;
              document.getElementById('sueldo_bruto').value = response.data.sueldo_bruto;
              document.getElementById('periodo_inicio').value = response.data.periodo_inicio;
              document.getElementById('periodo_fin').value = response.data.periodo_fin;
              document.getElementById('fecha_pago').value = response.data.fecha_pago;

              // Actualizar neto
              calcularNeto();

              // Mostrar info
              infoDiv.innerHTML = `<strong>Empleado:</strong> ${response.data.nombre} | <strong>Puesto:</strong> ${response.data.puesto}`;
              infoDiv.style.color = '#28a745'; // Verde para éxito
            } else {
              infoDiv.innerHTML = response.message;
              infoDiv.style.color = '#dc3545'; // Rojo para error
              // Limpiar campos si error
              document.getElementById('puesto').value = '';
              document.getElementById('sueldo_bruto').value = '';
              calcularNeto();
            }
          } else {
            infoDiv.innerHTML = 'Error al cargar datos. Verifica la conexión.';
            infoDiv.style.color = '#dc3545';
          }
        }
      };
      xhr.send();
    }

    // Actualizar sueldo neto en tiempo real
    document.getElementById('sueldo_bruto').addEventListener('input', calcularNeto);
    document.getElementById('deducciones').addEventListener('input', calcularNeto);

    function calcularNeto() {
      const bruto = parseFloat(document.getElementById('sueldo_bruto').value) || 0;
      const deducciones = parseFloat(document.getElementById('deducciones').value) || 0;
      document.getElementById('sueldo_neto').value = (bruto - deducciones).toFixed(2);
    }

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