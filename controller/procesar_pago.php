<?php
include('conexionbd.php');
session_start();
$ID_usuario = $_SESSION['ID_usuario']; // Asegurate que el ID esté en sesión

// 1. Obtener todos los productos del carrito del usuario
$query = $Ruta->prepare("SELECT ID_Producto, cantidad FROM carrito WHERE ID_usuario = ?");
$query->bind_param("i", $ID_usuario);
$query->execute();
$result = $query->get_result();

$productos = [];
$monto_total = 0;

while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
    
    // Obtener precio para calcular el total
    $precioStmt = $Ruta->prepare("SELECT precio FROM ventas WHERE ID_Producto = ?");
    $precioStmt->bind_param("i", $row['ID_producto']);
    $precioStmt->execute();
    $precioRes = $precioStmt->get_result()->fetch_assoc();
    
    $monto_total += $precioRes['precio'] * $row['cantidad'];
    $precioStmt->close();
}

// 2. Insertar el pago
$metodo = "tarjeta"; // Esto puede venir de un formulario
$stmtPago = $Ruta->prepare("INSERT INTO pagos (ID_usuario, ID_carrito, monto, metodo, estado) VALUES (?, 0, ?, ?, 'completado')");
$stmtPago->bind_param("ids", $ID_usuario, $monto_total, $metodo);
$stmtPago->execute();
$ID_pago = $stmtPago->insert_id;
echo "<pre>";
print_r($carrito);
echo "</pre>";
// 3. Insertar en detalle_pago y descontar stock
foreach($carrito as $item) {
    $id_producto = $item['id'];
    $cantidad = $item['cantidad'];
    $subtotal = $item['precio'] * $cantidad;

    // DEBUG
    echo "Insertando: Producto $id_producto, Cantidad $cantidad, Total $subtotal<br>";

    // Insertar en carrito
    $sql = "INSERT INTO carrito (ID_producto, cantidad, total, ID_usuario) VALUES (?, ?, ?, ?)";
    $stmt = $Ruta->prepare($sql);
    $stmt->execute([$id_producto, $cantidad, $subtotal, $ID_usuario]);

    // Actualizar stock
    $sqlStock = "UPDATE ventas SET stock = stock - ? WHERE ID_Producto = ?";
    $stmtStock = $Ruta->prepare($sqlStock);
    $stmtStock->execute([$cantidad, $id_producto]);

    // DEBUG stock
    echo "Actualizando stock de producto $id_producto, restando $cantidad<br>";
}


// 4. Vaciar carrito
$stmtClear = $Ruta->prepare("DELETE FROM carrito WHERE ID_usuario = ?");
$stmtClear->bind_param("i", $ID_usuario);
$stmtClear->execute();

// 5. Redirigir
header("Location: ../pages/confirmacion.php");
exit();
?>
