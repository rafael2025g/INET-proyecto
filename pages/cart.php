<?php
include('../model/modelos.php');

// Verificar si el usuario está logueado y es cliente
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/cart/cart.css" />
    <title>Carrito - TravelWorld</title>
  </head>
  <body>
    <header>
      <nav class="navbar">
        <div class="logo">TravelWorld</div>

        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menú">
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>

        <div class="nav-menu" id="navMenu">
          <div class="nav-overlay" id="navOverlay"></div>

          <div class="nav-content">
            <div class="mobile-menu-header">
              <div class="logo">TravelWorld</div>
              <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Cerrar menú">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <ul class="nav-links">
              <li><a href="../index.php" class="nav-link">inicio</a></li>
              <li><a href="../index.php#promoSection" class="nav-link">ofertas</a></li>
              <li><a href="paquetes.php" class="nav-link">paquetes</a></li>
              <li><a href="../index.php#servicios" class="nav-link">servicios</a></li>
              <li><a href="../index.php#contacto" class="nav-link">contacto</a></li>
            </ul>

            <div class="nav-buttons">
              <span>Hola, <?php echo $_SESSION['usuario']; ?></span>
              <button class="btn-filled">
                <a href="cart.php">Carrito (<?php echo count($carrito); ?>)</a>
              </button>
              <button class="btn-outline">
                <a href="logout.php">Cerrar Sesión</a>
              </button>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <section class="cart-container">
      <div class="cart-list">
        <h2>Tu carrito</h2>

        <?php if(empty($carrito)): ?>
          <div style="text-align: center; padding: 2rem;">
            <h3>Tu carrito está vacío</h3>
            <p>¡Agrega algunos paquetes increíbles!</p>
            <a href="paquetes.php" style="display: inline-block; margin-top: 1rem; padding: 0.8rem 1.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 6px;">Ver Paquetes</a>
          </div>
        <?php else: ?>
          <?php foreach($carrito as $index => $item): ?>
            <?php $subtotal = $item['precio'] * $item['cantidad']; $total += $subtotal; ?>
            <div class="cart-item">
              <img src="../assets/images/patagonia.jpg" alt="<?php echo htmlspecialchars($item['titulo']); ?>" />
              <div class="item-details">
                <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                <p>Paquete turístico</p>
                <div class="item-quantity">
                  <button onclick="updateQuantity(<?php echo $index; ?>, -1)">-</button>
                  <span><?php echo $item['cantidad']; ?></span>
                  <button onclick="updateQuantity(<?php echo $index; ?>, 1)">+</button>
                </div>
              </div>
              <div class="item-price">
                <p>$<?php echo number_format($subtotal); ?></p>
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="id" value="<?php echo openssl_encrypt($item['id'], COD, KEY); ?>">
                  <input type="hidden" name="btnAccion" value="eliminar">
                  <button type="submit" class="delete-btn">🗑️</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if(!empty($carrito)): ?>
      <!-- Resumen -->
      <div class="cart-summary">
        <h3>Resumen de compra</h3>
        <p><strong>Subtotal:</strong> $<?php echo number_format($total); ?></p>
        <p><strong>Impuestos (19%):</strong> $<?php echo number_format($total * 0.19); ?></p>
        <p><strong>Total:</strong> <span class="total-price">$<?php echo number_format($total * 1.19); ?></span></p>
        <form method="POST" action="../controller/procesar_pago.php">
          <button type="submit" class="checkout-btn">Procesar al pago</button>
        </form>
        <form method="POST" style="display: inline;">
          <input type="hidden" name="btnAccion" value="vaciar">
          <button type="submit" class="empty-btn">Vaciar carrito</button>
        </form>
      </div>
      <?php endif; ?>
    </section>

    <footer class="footer">
      <div class="footer-container">
        <div class="footer-brand">
          <h3>TravelWorld</h3>
          <p>Empresa líder en venta de paquetes turísticos nacionales e internacionales.</p>
        </div>
        <div class="footer-links">
          <h4>Enlaces</h4>
          <a href="#">Inicio</a>
          <a href="#">Ofertas</a>
          <a href="#">Paquetes</a>
          <a href="#">Servicios</a>
          <a href="#">Contacto</a>
        </div>
        <div class="footer-contact">
          <h4>Contacto</h4>
          <ul class="contact-content">
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Av. Principal 123, Ciudad
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>
              </svg>
              +54 9 11 1234 5678
            </li>
            <li class="contact-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/>
                <rect x="2" y="4" width="20" height="16" rx="2"/>
              </svg>
              info@travelworld.com
            </li>
          </ul>
        </div>
      </div>
      <div class="footer-detail">
        <p>&copy; 2025 TravelWorld. Todos los derechos reservados.</p>
      </div>
    </footer>

    <script>
      function updateQuantity(index, change) {
        // Aquí podrías implementar AJAX para actualizar la cantidad
        console.log('Actualizar cantidad del item', index, 'en', change);
      }
    </script>
    <script src="../assets/js/mobile-menu.js"></script>
  </body>
</html>
