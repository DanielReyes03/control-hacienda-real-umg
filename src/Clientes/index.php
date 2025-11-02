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

// Manejo de mensajes de redirección
$mensaje_success = $_GET['success'] ?? '';
$mensaje_error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Clientes - Hacienda Real</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../compartido/componentes/cabecera/cabecera.css">
  <!-- Importar SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php
    include("../compartido/componentes/cabecera/index.php");
    cabecera("Clientes");
  ?>

  <main class="contenido">
    <div class="acciones">
      <a href="#" class="btn-crear" onclick="alertaCrear(event)">Crear Nuevo</a>
    </div>

    <div class="tabla-contenedor">
      <table>
        <tr>
          <th>ID</th>
          <th>DPI</th>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Dirección</th>
          <th>Creado el</th>
          <th>Acciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM clientes ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
            $id = $fila['id']; // No htmlspecialchars para JS numérico
            echo "<tr>
                    <td>" . htmlspecialchars($id) . "</td>
                    <td>" . htmlspecialchars($fila['dpi'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['nombre']) . "</td>
                    <td>" . htmlspecialchars($fila['telefono'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['correo'] ?? '') . "</td>
                    <td>" . htmlspecialchars($fila['direccion'] ?? '') . "</td>
                    <td>" . ($fila['creado_en'] ? date('d/m/Y', strtotime($fila['creado_en'])) : '') . "</td>
                    <td class='acciones'>
                      <a href='#' class='boton-editar' onclick='alertaEditar(event, $id)'>Editar</a>
                      <a href='#' class='boton-eliminar' onclick='alertaEliminar(event, $id)'>Eliminar</a>
                    </td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"8\">No hay clientes registrados</td></tr>";
        }
        $conn->close();
        ?>
      </table>
    </div>
  </main>

  <?php if ($mensaje_success): ?>
  <script>
    Swal.fire({
      title: 'Éxito',
      text: '<?php echo htmlspecialchars($mensaje_success); ?>',
      icon: 'success',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <?php if ($mensaje_error): ?>
  <script>
    Swal.fire({
      title: 'Error',
      text: '<?php echo htmlspecialchars($mensaje_error); ?>',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>

  <script>
    // Alerta al crear nuevo cliente
    function alertaCrear(event) {
      event.preventDefault();
      Swal.fire({
        title: "¿Deseas crear un nuevo cliente?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "procesar_cliente.php";
        }
      });
    }

    // Alerta al editar
    function alertaEditar(event, id) {
      event.preventDefault();
      console.log("ID para editar:", id); // Debug: verifica en consola F12
      Swal.fire({
        title: "Editar cliente",
        text: "¿Deseas modificar la información de este cliente?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "editar_cliente.php?id=" + id;
        }
      });
    }

    // Alerta al eliminar
    function alertaEliminar(event, id) {
      event.preventDefault();
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el cliente permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: "Eliminado",
            text: "El cliente ha sido eliminado correctamente.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = "eliminar_cliente.php?id=" + id;
          }, 1500);
        }
      });
    }
  </script>
</body>
</html>