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

// Insertar en historial
$detalles_compra = "Compra realizada - Productos: ";
$productos_nombres = [];

foreach ($productos as $item) {
    $ID_producto = $item['id'];
    $stmtNombre = $Ruta->prepare("SELECT titulo FROM ventas WHERE ID_Producto = ?");
    $stmtNombre->bind_param("i", $ID_producto);
    $stmtNombre->execute();
    $resultado_nombre = $stmtNombre->get_result();
    if($resultado_nombre && $fila_nombre = $resultado_nombre->fetch_assoc()) {
        $nombre = $fila_nombre['titulo'];
        $productos_nombres[] = $nombre . " (x" . $item['cantidad'] . ")";
    }
    $stmtNombre->close();
}

$detalles_compra .= implode(", ", $productos_nombres);

// Verificar si la tabla historial existe, si no, crearla
$check_table = "SHOW TABLES LIKE 'historial'";
$table_exists = mysqli_query($Ruta, $check_table);

if(mysqli_num_rows($table_exists) == 0) {
    // Crear la tabla historial
    $create_table = "CREATE TABLE `historial` (
        `ID_historial` int(11) NOT NULL AUTO_INCREMENT,
        `ID_usuario` int(11) NOT NULL,
        `ID_pago` int(11) NOT NULL,
        `fecha_compra` datetime NOT NULL DEFAULT current_timestamp(),
        `total_compra` decimal(10,2) NOT NULL,
        `estado` varchar(20) NOT NULL DEFAULT 'completado',
        `detalles` text,
        PRIMARY KEY (`ID_historial`),
        KEY `ID_usuario` (`ID_usuario`),
        KEY `ID_pago` (`ID_pago`),
        CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`ID_usuario`) REFERENCES `usuario` (`ID_usuario`),
        CONSTRAINT `historial_ibfk_2` FOREIGN KEY (`ID_pago`) REFERENCES `pagos` (`ID_pago`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    mysqli_query($Ruta, $create_table);
}

$stmtHistorial = $Ruta->prepare("INSERT INTO historial (ID_usuario, ID_pago, total_compra, detalles) VALUES (?, ?, ?, ?)");
$stmtHistorial->bind_param("iids", $ID_usuario, $ID_pago, $monto_total, $detalles_compra);
$stmtHistorial->execute();
$stmtHistorial->close();

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
