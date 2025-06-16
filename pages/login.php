
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="../assets/css/login/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <div class="login-container">
    <form class="login-form" method="POST" action="../model/modelos.php">
      <h2>Iniciar sesión</h2>

      <div class="input-field">
        <label for="usuario">Email o Usuario</label>
        <div class="input-box">
          <i class="fa fa-envelope"></i>
          <input type="text" id="usuario" name="usuario" placeholder="Email o Usuario" required />
        </div>
      </div>

      <div class="input-field">
        <label for="contraseña">Contraseña</label>
        <div class="input-box">
          <i class="fa fa-lock"></i>
          <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña" required />
        </div>
      </div>

      <div class="options-row">
        <label class="remember-me">
          <input type="checkbox" />
          <span class="checkmark"></span>
          Recordarme
        </label>
        <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" name="login" class="login-btn">Iniciar sesión</button>

      <p class="register-link">¿No tienes una cuenta? <a href="register.php">Regístrate</a></p>
    </form>
  </div>
</body>
</html>
