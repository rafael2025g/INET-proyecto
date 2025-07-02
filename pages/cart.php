<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection
include('../controller/conexionbd.php');

// Function to get cart items
function getCartItems($Ruta, $userId) {
    $sql = "SELECT c.ID_producto, p.titulo AS nombre, p.precio, c.cantidad, p.imagen AS imagen_url 
            FROM carrito c
            JOIN ventas p ON c.ID_producto = p.ID_Producto
            WHERE c.ID_usuario = ?";

    $stmt = $Ruta->prepare($sql);
    if (!$stmt) {
        die("Error en prepare(): " . $Ruta->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = array();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    return $cartItems;
}

// Get user ID from session
$userId = $_SESSION['ID_usuario'];

// Handle update quantity request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['quantity'];

    if (!is_numeric($newQuantity) || $newQuantity < 1) {
        $newQuantity = 1;
    }
    if (!is_numeric($productId)) {
        die("ID de producto inválido.");
    }

    $updateSql = "UPDATE carrito SET cantidad = ? WHERE ID_usuario = ? AND ID_producto = ?";
    $updateStmt = $Ruta->prepare($updateSql);
    if (!$updateStmt) {
        die("Error en prepare UPDATE: " . $Ruta->error);
    }
    $updateStmt->bind_param("iii", $newQuantity, $userId, $productId);

    if (!$updateStmt->execute()) {
        echo "Error actualizando cantidad: " . $updateStmt->error;
    }
}

// Handle remove item request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $productId = $_POST['product_id'];

    if (!is_numeric($productId)) {
        die("ID de producto inválido.");
    }

    $removeSql = "DELETE FROM carrito WHERE ID_usuario = ? AND ID_producto = ?";
    $removeStmt = $Ruta->prepare($removeSql);
    if (!$removeStmt) {
        die("Error en prepare DELETE: " . $Ruta->error);
    }
    $removeStmt->bind_param("ii", $userId, $productId);

    if (!$removeStmt->execute()) {
        echo "Error eliminando producto: " . $removeStmt->error;
    }
}

// Handle checkout request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    header("Location: checkout.php");
    exit();
}

// Get cart items for the user (after possible updates/removals)
$cartItems = getCartItems($Ruta, $userId);

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../assets/css/cart/cart.css">
</head>
<body>

<!-- SECCIÓN 1: NAVEGACIÓN PRINCIPAL-->
    <header>
      <nav class="navbar">
        <!-- LOGO DE LA EMPRESA -->
        <div class="logo">TravelWorld</div>

        <!-- BOTÓN HAMBURGUESA PARA MÓVIL -->
        <button
          class="mobile-menu-toggle"
          id="mobileMenuToggle"
          aria-label="Abrir menú"
        >
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>

        <!-- MENÚ DE NAVEGACIÓN CON OVERLAY MÓVIL -->
        <div class="nav-menu" id="navMenu">
          <div class="nav-overlay" id="navOverlay"></div>
          
          <div class="nav-content">
            <div class="mobile-menu-header">
              <div class="logo">TravelWorld</div>
              <button
                class="mobile-menu-close"
                id="mobileMenuClose"
                aria-label="Cerrar menú"
              >
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <ul class="nav-links">
              <li><a href="../index.php" class="nav-link">inicio</a></li>
              <li><a href="../index.php#promoSection" class="nav-link">ofertas</a></li>
              <li><a href="./paquetes.php" class="nav-link active">paquetes</a></li>
              <li><a href="../index.php#servicios" class="nav-link">servicios</a></li>
              <li><a href="../index.php#contacto" class="nav-link">contacto</a></li>
            </ul>

            <div class="nav-buttons">
              <?php if(isset($_SESSION['usuario'])): ?>
                
                <span class='nav-user'><img src="../assets/images/user.png" alt=""></span>
                
                <?php if($_SESSION['rol'] === 'cliente'): ?>
                  <!-- Opciones para clientes -->
                  <button class="btn-cart">
                    <a href="cart.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg><?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>+</a>
                  </button>
                <?php elseif($_SESSION['rol'] === 'admin'): ?>
                  <!-- Opciones para administradores -->
                   
                <?php endif; ?>
                
                <button class="btn-login">
                  <a href="./logout.php">Cerrar Sesión</a>
                </button>
              <?php else: ?>
                <button class="btn-login">
                  <a href="./login.php">Iniciar Sesion</a>
                </button>
                <button class="btn-register">
                  <a href="./register.php">Registrarse</a>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </nav>
    </header>

<!-- Carrito -->
<div class="container">
    <h2 class="title">Carrito de Compras</h2>

    <?php if (empty($cartItems)): ?>
        <p>El carrito está vacío.</p>
    <?php else: ?>
    <div class="cart-wrapper">
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="product-image" />
                    <div class="item-info">
                        <div class="item-header">
                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <form method="post" class="item-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['ID_producto']; ?>">
                                <button class="btn-remove" name="remove_item"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg></button>
                            </form>
                        </div>
                        <div class="item-detail">
                            <form method="post" class="quantity-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['ID_producto']; ?>">
                                <div class="quantity-controls">
                                    <button type="button" onclick="decreaseQuantity(this, <?php echo $item['ID_producto']; ?>)">-</button>
                                    <input type="number" name="quantity" value="<?php echo $item['cantidad']; ?>" min="1" onchange="updateQuantity(this, <?php echo $item['ID_producto']; ?>)">
                                    <button type="button" onclick="increaseQuantity(this, <?php echo $item['ID_producto']; ?>)">+</button>
                                </div>
                                <input type="hidden" name="update_quantity">
                            </form>
                            <div class="item-price">
                                <p>$<?php echo number_format($item['precio'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="cart-title"><h2>Resumen de compra</h2></div>
            <div class="cart-summary-calculate">
                <p>Subtotal: <span>$<?php echo number_format($totalPrice, 2); ?></span></p>
                <p>IVA (19%): <span>$<?php echo number_format($totalPrice * 0.19, 2); ?></span></p>
            </div>
            <div class="cart-sumarry-total">
                <p>Total: <span>$<?php echo number_format($totalPrice * 1.19, 2); ?></span></p>
            </div>
            <form method="post">
                <button class="btn-checkout" name="checkout"><a href="../controller/procesar_pago.php">Procesar Pago</a></button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-container">
      <!-- INFORMACIÓN DE LA MARCA -->
      <div class="footer-brand">
        <h3>TravelWorld</h3>
        <p>
          Empresa líder en venta de paquetes turísticos nacionales e
          internacionales.
        </p>
      </div>
      <!-- ENLACES DE NAVEGACIÓN -->
      <div class="footer-links">
        <h4>Enlaces</h4>
        <a href="#">Inicio</a>
        <a href="#">Ofertas</a>
        <a href="#">Paquetes</a>
        <a href="#">Servicios</a>
        <a href="#">Contacto</a>
      </div>
      <!-- INFORMACIÓN DE CONTACTO -->
      <div class="footer-contact">
        <h4>Contacto</h4>
        <ul class="contact-content">
          <li class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            Av. Principal 123, Ciudad
          </li>
          <li class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>
            </svg>
            +54 9 11 1234 5678
          </li>
          <li class="contact-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail">
              <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/>
              <rect x="2" y="4" width="20" height="16" rx="2"/>
            </svg>
            info@travelworld.com
          </li>
        </ul>
      </div>
    </div>
    <!-- COPYRIGHT Y DERECHOS -->
    <div class="footer-detail">
      <p>&copy; 2025 TravelWorld. Todos los derechos reservados.</p>
    </div>
</footer>

<script>
    function decreaseQuantity(button, productId) {
        let input = button.parentNode.querySelector('input[name="quantity"]');
        let quantity = parseInt(input.value);
        if (quantity > 1) {
            input.value = quantity - 1;
            updateQuantity(input, productId);
        }
    }

    function increaseQuantity(button, productId) {
        let input = button.parentNode.querySelector('input[name="quantity"]');
        let quantity = parseInt(input.value);
        input.value = quantity + 1;
        updateQuantity(input, productId);
    }

    function updateQuantity(input, productId) {
        let form = input.closest('form');
        form.submit();
    }
</script>
<script src="../assets/js/mobile-menu.js"></script>
</body>
</html>
