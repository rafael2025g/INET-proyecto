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
  <section class="cart-container" style="text-align: center;">
    <h2>¡Gracias por tu compra!</h2>
    <p>Tu pedido fue procesado exitosamente.</p>
    <a href="paquetes.php" class="checkout-btn">Volver a paquetes</a>
  </section>
</body>
</html>
