<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago exitoso</title>
  <link rel="stylesheet" href="../assets/css/cart/cart.css">
</head>
<body>

  <div class="loader-container">
    <div class="spinner"></div>
    <div class="message" id="thankyou-message">
      <h2>¡Gracias por tu compra!</h2>
      <p>Tu pedido fue procesado exitosamente.</p>
    </div>
  </div>

  <script>
    // Mostrar mensaje de agradecimiento tras 2 segundos
    setTimeout(() => {
      document.querySelector('.spinner').style.display = 'none';
      document.getElementById('thankyou-message').style.display = 'flex';
    }, 2000);

    // Redirigir a paquetes.php tras 5 segundos en total
    setTimeout(() => {
      window.location.href = "paquetes.php";
    }, 5000);
  </script>

</body>
</html>
