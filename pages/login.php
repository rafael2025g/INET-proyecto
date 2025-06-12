<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }
    .login-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .login-container input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .login-container button {
      width: 100%;
      padding: 10px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .login-container button:hover {
      background: #218838;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <form class="login-box">
      <h2>Iniciar sesión</h2>

      <div class="input-group">
        <label for="email">Email *</label>
        <input type="email" id="email" placeholder="tu@email.com" required>
      </div>

      <div class="input-group">
        <label for="password">Contraseña *</label>
        <div class="password-wrapper">
          <input type="password" id="password" required>
          <span class="toggle-password"><i class="fas fa-eye"></i></span>
        </div>
      </div>

      <div class="options">
        <label><input type="checkbox"> Recordarme</label>
        <a href="#">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="login-btn">
        <i class="fas fa-arrow-right"></i> Entrar
      </button>

      <p class="register-text">
        ¿No tienes una cuenta? <a href="#">Regístrate</a>
      </p>
    </form>
  </div>

</body>
</html>


    
</body>
</html>