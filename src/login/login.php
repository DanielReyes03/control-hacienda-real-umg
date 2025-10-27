<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>La Hacienda Real - Acceso</title>
  <link rel="stylesheet" href="./CSS/login.css">
</head>
<body>
  <div class="container">
    <!-- Encabezado -->
    <div class="header">
      <img src="./assets/img/Logo-Hacienda-Real.png" alt="Logo La Hacienda Real">
      <h1>La Hacienda Real</h1>
      <p class="slogan">El sabor auténtico de la carne</p>
    </div>

    <!-- Login -->
    <div class="form-container" id="login-form">
      <h2>Iniciar Sesión</h2>
      <form method="POST" action="auth.php">
        <input type="email" placeholder="Correo electrónico" required>
        <input type="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
      </form>
      <p>¿No tienes cuenta? <a href="#" id="show-register">Regístrate aquí</a></p>
    </div>

    <!-- Registro -->
    <div class="form-container hidden" id="register-form">
      <h2>Registro</h2>
      <form>
        <input type="text" placeholder="Nombre completo" required>
        <input type="email" placeholder="Correo electrónico" required>
        <input type="tel" placeholder="Número de teléfono" required>
        <input type="password" placeholder="Contraseña" required>
        <input type="password" placeholder="Confirmar contraseña" required>
        <select required>
          <option value="" disabled selected>Selecciona tu preferencia</option>
          <option value="res">Carne de res</option>
          <option value="cerdo">Carne de cerdo</option>
          <option value="pollo">Pollo</option>
          <option value="mixto">Mixto</option>
        </select>
        <button type="submit">Registrarme</button>
      </form>
      <p>¿Ya tienes cuenta? <a href="#" id="show-login">Inicia sesión</a></p>
    </div>
  </div>

  <script src="./JS/login.js"></script>
</body>
</html>
