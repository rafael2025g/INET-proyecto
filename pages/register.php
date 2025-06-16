<?php include('../model/r.php');?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="../assets/css/register/register.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="register-container">
      <form class="register-form" method="POST" >
        <h2>Crear Cuenta</h2>

        <div class="input-field">
          <label for="usuario">Nombre de usuario</label>
          <div class="input-box">
            <i class="fa fa-user"></i>
            <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" required />
          </div>
        </div>
        
        <div class="input-field">
          <label for="email">Email</label>
          <div class="input-box">
            <i class="fa fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="Email" required />
          </div>
        </div>

        <div class="input-field">
          <label for="telefono">Teléfono</label>
          <div class="input-box">
            <i class="fa fa-phone"></i>
            <input type="tel" id="telefono" name="telefono" placeholder="Telefono" required  autocomplete="new-password"/>
          </div>
        </div>

        <div class="input-field">
          <label for="contraseña">Contraseña</label>
          <div class="input-box">
            <i class="fa fa-lock"></i>
            <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña" required   autocomplete="new-password"/>
          </div>
        </div>

        <div class="input-field">
          <label for="confirm">Confirmar contraseña</label>
          <div class="input-box">
            <i class="fa fa-lock"></i>
            <input
              type="password"
              id="confirm"
              name="confirm"
              placeholder="Confirma tu contraseña"
              required
            />
          </div>
        </div>

        <div class="checkbox-field">
          <label class="checkbox-container">
            <input type="checkbox" id="terms" required />
            <span class="custom-checkbox"></span>
            <span class="checkbox-text">
              Acepto los <a href="#">términos y condiciones</a>
            </span>
          </label>
        </div>

        <button type="submit" name="registrar" class="register-btn">Registrarse</button>

        <p class="login-link">
          ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
        </p>
      </form>
    </div>
  </body>
</html>
