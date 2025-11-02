<?php
// Configuración de la base de datos
$host = 'db';
$user = 'user';
$password = 'userpassword';
$database = 'mydb';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = intval($_GET['id'] ?? 0);
$cliente = null;
$mensaje = '';
$es_error = false;

if ($id <= 0) {
    header('Location: index.php?error=ID de cliente inválido');
    exit;
}

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

if (!$cliente) {
    header('Location: index.php?error=Cliente no encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $dpi = trim($_POST['dpi']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    // Validaciones mejoradas
    $errores = [];
    if (empty($nombre)) {
        $errores[] = 'El nombre es requerido.';
    }
    if (!empty($dpi) && (!is_numeric($dpi) || strlen($dpi) !== 13)) {
        $errores[] = 'El DPI debe tener exactamente 13 dígitos numéricos.';
    }
    if (!empty($telefono) && (!is_numeric($telefono) || strlen($telefono) !== 8)) {
        $errores[] = 'El teléfono debe tener exactamente 8 dígitos numéricos.';
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, dpi = ?, telefono = ?, correo = ?, direccion = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $dpi, $telefono, $correo, $direccion, $id);
        
        if ($stmt->execute()) {
            header('Location: index.php?success=Cliente actualizado exitosamente');
            exit;
        } else {
            $errores[] = 'Error al actualizar: ' . $conn->error;
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
  <title>Editar Cliente - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <link rel="stylesheet" href="./crear.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    /* Estilos inline para validaciones (agrega a crear.css si prefieres) */
    .error { color: #d33; font-size: 0.9em; margin-top: 5px; display: none; }
    input:invalid { border-color: #d33; }
  </style>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Editar Clientes");
    ?>

  <main class="contenido">

    <form method="POST" class="formulario" id="formulario">
      <div class="campo">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>">
        <div class="error" id="err-nombre">El nombre es requerido.</div>
      </div>
      <div class="campo">
        <label for="dpi">DPI (exactamente 13 dígitos)</label>
        <input type="text" id="dpi" name="dpi" maxlength="13" pattern="[0-9]{13}" title="Solo números, exactamente 13 dígitos" value="<?php echo htmlspecialchars($cliente['dpi'] ?? ''); ?>">
        <div class="error" id="err-dpi">El DPI debe tener exactamente 13 dígitos numéricos.</div>
      </div>
      <div class="campo">
        <label for="telefono">Teléfono (exactamente 8 dígitos)</label>
        <input type="text" id="telefono" name="telefono" maxlength="8" pattern="[0-9]{8}" title="Solo números, exactamente 8 dígitos" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>">
        <div class="error" id="err-telefono">El teléfono debe tener exactamente 8 dígitos numéricos.</div>
      </div>
      <div class="campo">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
      </div>
      <button type="button" id="btn-guardar" class="btn-guardar">Actualizar</button>
      <button type="button" id="btn-regresar" class="btn-regresar">Regresar o cancelar</button>
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
    // Solo números en tiempo real
    function soloNumeros(input) {
      input.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
      input.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) e.preventDefault();
      });
    }

    soloNumeros(document.getElementById('dpi'));
    soloNumeros(document.getElementById('telefono'));

    // Validación al submit
    document.getElementById('formulario').addEventListener('submit', function(e) {
      let valid = true;
      const dpi = document.getElementById('dpi').value.trim();
      const telefono = document.getElementById('telefono').value.trim();
      const nombre = document.getElementById('nombre').value.trim();

      if (!nombre) {
        document.getElementById('err-nombre').style.display = 'block';
        valid = false;
      }
      if (dpi && (dpi.length !== 13 || !/^\d{13}$/.test(dpi))) {
        document.getElementById('err-dpi').style.display = 'block';
        valid = false;
      }
      if (telefono && (telefono.length !== 8 || !/^\d{8}$/.test(telefono))) {
        document.getElementById('err-telefono').style.display = 'block';
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        Swal.fire('Error', 'Por favor corrige los campos indicados.', 'error');
      }
    });

    document.getElementById('btn-guardar').addEventListener('click', function() {
      // Validar antes de Swal
      document.getElementById('formulario').dispatchEvent(new Event('submit'));
      if (document.getElementById('formulario').checkValidity() && !document.querySelector('.error[style*="block"]')) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "Se actualizará el cliente con la información proporcionada",
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById('formulario').submit();
          }
        });
      }
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