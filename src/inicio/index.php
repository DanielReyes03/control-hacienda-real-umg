<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Helper para escapar texto
if (!function_exists('e')) {
    function e($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

// Datos del usuario desde sesión
$nombre = $_SESSION['nombre_completo'] ?? $_SESSION['nombre'] ?? 'Invitado';
$rol_id = $_SESSION['rol_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hacienda Real</title>
  <style>
    body {
      margin: 0;
      font-family: 'Helvetica Neue', Arial, sans-serif;
      color: #111;
      text-align: center;
      min-height: 100vh;
      background-color: #f6f8fa;
    }

    nav {
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(90deg, #ffffffcc, #f3f3f3cc);
      padding: 10px 40px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    .nav-content {
      width: 100%;
      max-width: 1100px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      display: flex;
      align-items: center;
      font-weight: bold;
      font-size: 1.2rem;
      color: #222;
    }

    .logo img {
      height: 45px;
      margin-right: 10px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .user-details {
      text-align: right;
    }

    .user-details h1 {
      font-size: 1rem;
      margin: 0;
      color: #444;
    }

    .user-details p {
      margin: 0;
      font-size: 0.9rem;
      color: #777;
    }

    .user-info a {
      text-decoration: none;
      background-color: #e63946;
      color: #fff;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .user-info a:hover {
      background-color: #d62828;
    }

    header {
      padding: 120px 20px 40px;
    }

    header h1 {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 10px;
    }

    header p {
      font-size: 1rem;
      letter-spacing: 2px;
      color: #555;
    }

    .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      padding: 20px;
      max-width: 1100px;
      margin: auto;
    }

    .grid-item {
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      max-height: 250px;
      cursor: pointer;
      transition: transform 0.3s ease;
      background: #fff;
    }

    .grid-item:hover {
      transform: translateY(-5px);
    }

    .grid-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }

    .grid-item:hover img {
      transform: scale(1.05);
    }

    .overlay {
      position: absolute;
      bottom: 15px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(255, 255, 255, 0.9);
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      font-size: 1rem;
      color: #111;
    }

    .clientes-tiendas-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 15px;
      margin: 40px 0;
      max-width: 1100px;
      margin-left: auto;
      margin-right: auto;
    }

    .grid-item.clientes,
    .grid-item.tiendas {
      width: 300px;
      height: 200px;
    }
  </style>
</head>
<body>

  <nav>
    <div class="nav-content">
      <div class="logo">
        <img src="../login/assets/img/Logo-Hacienda-Real.png" alt="Logo">
        HACIENDA REAL
      </div>

      <div class="user-info">
        <div class="user-details">
          <h1>Bienvenido, <?= e($nombre) ?></h1>
          <p>Rol ID: <?= e($rol_id) ?></p>
        </div>
        <a href="../index.php">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <header>
    <h1>SISTEMA WEB HACIENDA REAL</h1>
    <p>Bienvenido al panel principal</p>
  </header>

  <section class="grid-container">

    <?php if ($rol_id != 5): ?>
      <div class="grid-item" onclick="redirigir('../inventario/index.php')">
        <img src="../login/assets/img/pexels-tiger-lily-4483610.jpg" alt="Inventario">
        <div class="overlay">INVENTARIO</div>
      </div>

      <div class="grid-item" onclick="redirigir('../login/admin_usuarios.php')">
        <img src="../login/assets/img/pexels-shkrabaanthony-5475750.jpg" alt="Usuarios">
        <div class="overlay">USUARIOS</div>
      </div>

      <div class="grid-item" onclick="redirigir('../Proveedores/index.php')">
        <img src="../login/assets/img/pexels-artempodrez-5025489.jpg" alt="Proveedores">
        <div class="overlay">PROVEEDORES</div>
      </div>

      <div class="grid-item" onclick="redirigir('../reportes/index.php')">
        <img src="../login/assets/img/pexels-olly-3760072.jpg" alt="Reportes">
        <div class="overlay">REPORTES</div>
      </div>

      <div class="grid-item" onclick="redirigir('../Planilla/index.php')">
        <img src="../login/assets/img/pexels-biekir-2148554792-33715049.jpg" alt="Planilla">
        <div class="overlay">PLANILLA</div>
      </div>
    <?php endif; ?>

    <!-- Común para todos -->
    <div class="grid-item" onclick="redirigir('../compras/index.php')">
      <img src="../login/assets/img/pexels-kampus-8931691.jpg" alt="Servicio Domicilio">
      <div class="overlay">SERVICIO DOMICILIO</div>
    </div>

    <div class="grid-item" onclick="redirigir('../sucursales/index.php')">
      <img src="../login/assets/img/fondositio.jpg" alt="Sucursales">
      <div class="overlay">SUCURSALES</div>
    </div>

    <?php if ($rol_id != 5): ?>
      <div class="grid-item" onclick="redirigir('../Vehiculos/index.php')">
        <img src="../login/assets/img/pexels-renee-razumov-2155050841-33814686.jpg" alt="Vehículos">
        <div class="overlay">CONTROL DE VEHÍCULOS</div>
      </div>
    <?php endif; ?>
    
  </section>

  <!-- CLIENTES Y TIENDAS SOLO PARA OTROS ROLES -->
  <?php if ($rol_id != 5): ?>
  <section>
    <div class="clientes-tiendas-container">
      <div class="grid-item clientes" onclick="redirigir('../clientes/index.php')">
        <img src="../login/assets/img/clientes.jpg" alt="Clientes">
        <div class="overlay">CLIENTES</div>
      </div>
      <div class="grid-item tiendas" onclick="redirigir('../tiendas/index.php')">
        <img src="../login/assets/img/tiendas.jpeg" alt="Tienda">
        <div class="overlay">TIENDA</div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <script>
    function redirigir(ruta) {
      window.location.href = ruta;
    }
  </script>

</body>
</html>
