<?php

session_start();
include 'conexionbd.php';

if (!isset($_SESSION['ID_usuario'])) {
    die("No está logueado.");
}

$ID_usuario = $_SESSION['ID_usuario'];
$productos = $_SESSION['carrito'] ?? [];

if (empty($productos)) {
    die("Carrito vacío.");
}

$monto_total = 0;

// Calcular monto total y validar datos
foreach ($productos as $item) {
    $ID_producto = $item['id'];
    $cantidad = $item['cantidad'];

    $stmt = $Ruta->prepare("SELECT precio, stock FROM ventas WHERE ID_Producto = ?");
    $stmt->bind_param("i", $ID_producto);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$res) {
        die("Producto no encontrado: $ID_producto");
    }
    if ($res['stock'] < $cantidad) {
        die("Stock insuficiente para el producto ID $ID_producto");
    }

    $monto_total += $res['precio'] * $cantidad;
}

// Insertar pago
$stmtPago = $Ruta->prepare("INSERT INTO pagos (ID_usuario, total_pago) VALUES (?, ?)");
$stmtPago->bind_param("id", $ID_usuario, $monto_total);
$stmtPago->execute();
$ID_pago = $stmtPago->insert_id;
$stmtPago->close();

// Insertar detalle y actualizar stock
foreach ($productos as $item) {
    $ID_producto = $item['id'];
    $cantidad = $item['cantidad'];

    // Precio calculado antes, se puede repetir si quieres
    $stmtPrecio = $Ruta->prepare("SELECT precio FROM ventas WHERE ID_Producto = ?");
    $stmtPrecio->bind_param("i", $ID_producto);
    $stmtPrecio->execute();
    $precio = $stmtPrecio->get_result()->fetch_assoc()['precio'];
    $stmtPrecio->close();

    $subtotal = $precio * $cantidad;

    $stmtDetalle = $Ruta->prepare("INSERT INTO detalle_pago (ID_pago, ID_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
    $stmtDetalle->bind_param("iiii", $ID_pago, $ID_producto, $cantidad, $subtotal);
    $stmtDetalle->execute();
    $stmtDetalle->close();

    $stmtStock = $Ruta->prepare("UPDATE ventas SET stock = stock - ? WHERE ID_Producto = ?");
    $stmtStock->bind_param("ii", $cantidad, $ID_producto);
    $stmtStock->execute();
    $stmtStock->close();
}

// Vaciar carrito en sesión
unset($_SESSION['carrito']);

// Redirigir a confirmación
header("Location: ../pages/confirmacion.php");
exit();

?>
