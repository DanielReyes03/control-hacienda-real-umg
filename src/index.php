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
      position: relative;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Fondo con blur */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      filter: blur(4px);
      z-index: -1;
    }

    /* Navbar */
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(255, 255, 255, 0.9);
      padding: 10px 30px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    nav .logo {
      display: flex;
      align-items: center;
      font-weight: bold;
      font-size: 1.2rem;
      color: #222;
    }

    nav .logo img {
      height: 40px;
      margin-right: 10px;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin-right: 50px;
      padding: 10px;
    }

    nav ul li {
      display: inline;
    }

    nav ul li a {
      text-decoration: none;
      color: #222;
      font-weight: bold;
      transition: color 0.3s;
    }

    nav ul li a:hover {
      color: #0077b6;
    }

    /* Header */
    header {
      padding: 100px 20px 40px; /* más padding por navbar fijo */
    }

    header h1 {
      font-size: 2.5rem;
      margin: 0;
      font-weight: bold;
    }

    header p {
      margin: 10px 0 0;
      font-size: 1rem;
      letter-spacing: 2px;
    }

    /* Grid */
    .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      padding: 20px;
      max-width: 1100px;
      height: 600px;
      margin: auto;
    }

    .grid-item {
      position: relative;
      overflow: hidden;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      max-height: 250px;
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
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(255, 255, 255, 0.85);
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: bold;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav>
    <div class="logo">
      <img src="../login/assets/img/Logo-Hacienda-Real.png" alt="Logo">
      HACIENDA REAL
    </div>
    <ul>
      <li><a href="index.php">Menú</a></li>
      <li><a href="../login/login.php">Inicio de sesión</a></li>
    </ul>
  </nav>

  <header>
    <h1>SISTEMA WEB HACIENDA REAL</h1>
    <p>Bienvenido</p>
  </header>

  <section class="grid-container">
    <div class="grid-item" onclick="redirigir('../inventario/index.php')" style="cursor:pointer;">
      <img src="../login/assets/img/pexels-tiger-lily-4483610.jpg" alt="Cafés">
      <div class="overlay">INVENTARIO</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/pexels-cottonbro-4068314.jpg" alt="Viajes">
      <div class="overlay">COMPRAS</div>
    </div>
    <div class="grid-item" onclick="redirigir('../Proveedores/index.php')" style="cursor:pointer;">>
      <img src="../login/assets/img/pexels-artempodrez-5025489.jpg" alt="Lista Verde">
      <div class="overlay">PROVEEDORES</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/pexels-olly-3760072.jpg" alt="Deco">
      <div class="overlay">VENTAS</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/pexels-biekir-2148554792-33715049.jpg" alt="Diario">
      <div class="overlay">PLANILLA</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/pexels-kampus-8931691.jpg" alt="Diario">
      <div class="overlay">SERVICIO DOMICILIO</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/fondositio.jpg" alt="Diario">
      <div class="overlay">SUCURSALES</div>
    </div>
    <div class="grid-item">
      <img src="../login/assets/img/pexels-renee-razumov-2155050841-33814686.jpg" alt="Diario">
      <div class="overlay">CONTROL DE VEHICULOS</div>
    </div>
  </section>
</body>
</html>
<script>
  function redirigir(ruta) {
    // Redirige directamente a la ruta que pases
    window.location.href = ruta;
  }
</script>