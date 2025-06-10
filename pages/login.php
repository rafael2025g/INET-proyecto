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
    <h2>Iniciar sesión</h2>
    <form action="" method="POST">
      <input type="text" name="nombre de usuario" placeholder="nombre de usuario" required>
      <input type="varchar" name="email" placeholder="email" required>
      <input type="password" name="clave" placeholder="Contraseña" required>
      <button type="submit" name="login">Entrar</button>
    </form>
  </div>

</body>
</html>


    
</body>
</html>