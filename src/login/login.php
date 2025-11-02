<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
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
    <div class="header">
      <img src="./assets/img/Logo-Hacienda-Real.png" alt="Logo La Hacienda Real">
      <h1>La Hacienda Real</h1>
      <p class="slogan">El sabor auténtico de la carne</p>
    </div>

    <!-- Login -->
    <div class="form-container" id="login-form">
      <h2>Iniciar Sesión</h2>

      <?php if($error): ?>
        <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <?php if($success): ?>
        <p style="color:green; text-align:center;"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>

      <form method="POST" action="auth.php">
          <input type="text" name="usuario" placeholder="Usuario" required>
          <input type="password" name="password" placeholder="Contraseña" required>
          <button type="submit">Entrar</button>
      </form>
      <p>¿No tienes cuenta? <a href="#" id="show-register">Regístrate aquí</a></p>
    </div>

    <!-- Registro -->
    <div class="form-container hidden" id="register-form">
      <h2>Registro</h2>
      <form action="register_process.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="text" name="usuario_reg" placeholder="Usuario" required>
        <input type="email" name="correo_reg" placeholder="Correo electrónico" required>
        <input type="tel" name="telefono_reg" placeholder="Número de teléfono">
        <input type="password" name="password_reg" placeholder="Contraseña" required>
        <input type="password" name="password_confirm" placeholder="Confirmar contraseña" required>
        <button type="submit">Registrarme</button>
      </form>
      <p>¿Ya tienes cuenta? <a href="#" id="show-login">Inicia sesión</a></p>
    </div>
  </div>

  <script src="./JS/login.js"></script>
</body>
</html>
