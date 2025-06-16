<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection
include('db_connect.php');

// Function to get cart items
function getCartItems($conn, $userId) {
    $sql = "SELECT c.id AS cart_item_id, p.id AS product_id, p.nombre, p.precio, c.cantidad, p.imagen_url 
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.usuario_id = ?";
    $stmt = $conn->prepare($sql);
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
$userId = $_SESSION['usuario']['id'];

// Get cart items for the user
$cartItems = getCartItems($conn, $userId);

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['precio'] * $item['cantidad'];
}

// Handle update quantity request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $cartItemId = $_POST['cart_item_id'];
    $newQuantity = $_POST['quantity'];

    // Validate quantity
    if ($newQuantity > 0) {
        // Update quantity in the database
        $updateSql = "UPDATE carrito SET cantidad = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newQuantity, $cartItemId);
        if ($updateStmt->execute()) {
            // Refresh cart items and total price
            $cartItems = getCartItems($conn, $userId);
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['precio'] * $item['cantidad'];
            }
        } else {
            echo "Error updating quantity: " . $updateStmt->error;
        }
    }
}

// Handle remove item request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $cartItemId = $_POST['cart_item_id'];

    // Remove item from the database
    $removeSql = "DELETE FROM carrito WHERE id = ?";
    $removeStmt = $conn->prepare($removeSql);
    $removeStmt->bind_param("i", $cartItemId);
    if ($removeStmt->execute()) {
        // Refresh cart items and total price
        $cartItems = getCartItems($conn, $userId);
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['precio'] * $item['cantidad'];
        }
    } else {
        echo "Error removing item: " . $removeStmt->error;
    }
}

// Handle checkout request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    // Redirect to checkout page
    header("Location: checkout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="navbar bg-base-100">
  <div class="flex-1">
    <a class="btn btn-ghost text-xl" href="index.php">Tienda</a>
  </div>
  <div class="flex-none gap-2">
    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <div class="indicator">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
          <span class="badge badge-sm indicator-item"><?php echo count($cartItems); ?></span>
        </div>
      </div>
      <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
        <div class="card-body">
          <span class="font-bold text-lg"><?php echo count($cartItems); ?> Items</span>
          <span class="text-info">Subtotal: $<?php echo number_format($totalPrice, 2); ?></span>
          <div class="card-actions">
            <a href="cart.php" class="btn btn-primary btn-block">View cart</a>
          </div>
        </div>
      </div>
    </div>
    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
        <div class="w-10 rounded-full">
          <img alt="Tailwind CSS Navbar component" src="https://daisyui.com/images/stock/photo-1534528741702-a0cfae58b737.jpg" />
        </div>
      </div>
      <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
        <li>
          <a class="justify-between">
            Profile
            <span class="badge">New</span>
          </a>
        </li>
        <li><a>Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
    <?php if($_SESSION['rol'] === 'cliente'): ?>
      <button class="btn-outline">
        <a href="historial.php">Ver Mi Historial</a>
      </button>
    <?php elseif($_SESSION['rol'] === 'admin'): ?>
      <button class="btn-outline">
        <a href="historial.php">Ver Historial Completo</a>
      </button>
    <?php endif; ?>
  </div>
</div>

<div class="container mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-4">Carrito de Compras</h1>

    <?php if (empty($cartItems)): ?>
        <p>El carrito está vacío.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="<?php echo $item['imagen_url']; ?>" alt="<?php echo $item['nombre']; ?>" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?php echo $item['nombre']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                $<?php echo number_format($item['precio'], 2); ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <div class="join">
                                        <button class="btn join-item" type="button" onclick="decreaseQuantity(this, <?php echo $item['cart_item_id']; ?>)">-</button>
                                        <input type="number" class="input input-bordered join-item w-16" name="quantity" value="<?php echo $item['cantidad']; ?>" min="1" onchange="updateQuantity(this, <?php echo $item['cart_item_id']; ?>)">
                                        <button class="btn join-item" type="button" onclick="increaseQuantity(this, <?php echo $item['cart_item_id']; ?>)">+</button>
                                    </div>
                                    <input type="hidden" name="update_quantity">
                                </form>
                            </td>
                            <td>
                                $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <button class="btn btn-error btn-sm" name="remove_item">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center">
            <span class="text-lg font-bold">Total: $<?php echo number_format($totalPrice, 2); ?></span>
            <form method="post">
                <button class="btn btn-primary" name="checkout">Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    function decreaseQuantity(button, cartItemId) {
        let input = button.parentNode.querySelector('input[name="quantity"]');
        let quantity = parseInt(input.value);
        if (quantity > 1) {
            input.value = quantity - 1;
            updateQuantity(input, cartItemId);
        }
    }

    function increaseQuantity(button, cartItemId) {
        let input = button.parentNode.querySelector('input[name="quantity"]');
        let quantity = parseInt(input.value);
        input.value = quantity + 1;
        updateQuantity(input, cartItemId);
    }

    function updateQuantity(input, cartItemId) {
        let form = input.closest('form');
        form.submit();
    }
</script>

</body>
</html>
