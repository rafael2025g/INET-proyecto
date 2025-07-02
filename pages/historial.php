<?php
session_start();
include('../controller/conexionbd.php');

// Verificar si el usuario está logueado
if(!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Función para verificar si es admin
function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

$esAdmin = esAdmin();
$ID_usuario = $_SESSION['ID_usuario'];

// Función para obtener historial según el rol del usuario
function obtenerHistorial($esAdmin, $ID_usuario, $conexion) {
    if($esAdmin) {
        // Admin ve todo el historial
        $consulta = "SELECT h.*, u.usuario, u.email, p.fecha_pago 
                    FROM historial h 
                    INNER JOIN usuario u ON h.ID_usuario = u.ID_usuario 
                    INNER JOIN pagos p ON h.ID_pago = p.ID_pago 
                    ORDER BY h.fecha_compra DESC";
        $resultado = mysqli_query($conexion, $consulta);
    } else {
        // Cliente solo ve su historial
        $consulta = "SELECT h.*, p.fecha_pago 
                    FROM historial h 
                    INNER JOIN pagos p ON h.ID_pago = p.ID_pago 
                    WHERE h.ID_usuario = ? 
                    ORDER BY h.fecha_compra DESC";
        $stmt = mysqli_prepare($conexion, $consulta);
        mysqli_stmt_bind_param($stmt, "i", $ID_usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
    }
    
    return $resultado;
}

// Función para obtener detalles de productos de una compra
function obtenerDetallesCompra($ID_pago, $conexion) {
    $consulta = "SELECT dp.*, v.titulo, v.lugar, v.dias 
                FROM detalle_pago dp 
                INNER JOIN ventas v ON dp.ID_producto = v.ID_Producto 
                WHERE dp.ID_pago = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "i", $ID_pago);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    return $resultado;
}

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
    
    // Migrar datos existentes
    $migrate_data = "INSERT INTO historial (ID_usuario, ID_pago, fecha_compra, total_compra, estado, detalles)
                    SELECT 
                        p.ID_usuario,
                        p.ID_pago,
                        p.fecha_pago,
                        p.total_pago,
                        'completado',
                        CONCAT('Compra migrada - Total: $', p.total_pago)
                    FROM pagos p
                    WHERE p.total_pago > 0";
    
    mysqli_query($Ruta, $migrate_data);
}

$historial = obtenerHistorial($esAdmin, $ID_usuario, $Ruta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $esAdmin ? 'Historial de Todas las Compras' : 'Mi Historial de Compras'; ?> - TravelWorld</title>
    <link rel="stylesheet" href="../assets/css/historial/historial.css">
    <link rel="stylesheet" href="../assets/css/historial/historial-button.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?php echo $esAdmin ? 'Historial de Todas las Compras' : 'Mi Historial de Compras'; ?></h2>
            <div>
                <a href="../index.php" class="nav-link">← Inicio</a>
                <?php if($esAdmin): ?>
                    <a href="admin-panel.php" class="nav-link">Panel Admin</a>
                    <a href="paquetes.php" class="nav-link">Gestionar Paquetes</a>
                <?php else: ?>
                    <a href="paquetes.php" class="nav-link">Ver Paquetes</a>
                    <a href="cart.php" class="nav-link">Mi Carrito</a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Debug: Mostrar información del usuario
        if(isset($_GET['debug'])) {
            echo '<div class="debug-info">';
            echo '<h4>Información de Debug:</h4>';
            echo '<p>Usuario: ' . $_SESSION['usuario'] . '</p>';
            echo '<p>Rol: ' . $_SESSION['rol'] . '</p>';
            echo '<p>ID Usuario: ' . $_SESSION['ID_usuario'] . '</p>';
            echo '<p>Es Admin: ' . ($esAdmin ? 'Sí' : 'No') . '</p>';
            
            // Verificar si hay datos en historial
            $count_query = "SELECT COUNT(*) as total FROM historial";
            $count_result = mysqli_query($Ruta, $count_query);
            $count_row = mysqli_fetch_assoc($count_result);
            echo '<p>Total registros en historial: ' . $count_row['total'] . '</p>';
            
            // Verificar si hay datos para este usuario
            if(!$esAdmin) {
                $user_count_query = "SELECT COUNT(*) as total FROM historial WHERE ID_usuario = " . $ID_usuario;
                $user_count_result = mysqli_query($Ruta, $user_count_query);
                $user_count_row = mysqli_fetch_assoc($user_count_result);
                echo '<p>Registros para este usuario: ' . $user_count_row['total'] . '</p>';
            }
            echo '</div>';
        }

        // Calcular estadísticas
        $total_compras = 0;
        $total_gastado = 0;
        $compras_mes = 0;
        
        if($historial && mysqli_num_rows($historial) > 0) {
            // Guardar los datos para mostrar después
            $historial_data = [];
            while($row = mysqli_fetch_assoc($historial)) {
                $historial_data[] = $row;
                $total_compras++;
                $total_gastado += $row['total_compra'];
                
                // Compras del mes actual
                $fecha_compra = new DateTime($row['fecha_compra']);
                $fecha_actual = new DateTime();
                if($fecha_compra->format('Y-m') == $fecha_actual->format('Y-m')) {
                    $compras_mes++;
                }
            }
        }
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_compras; ?></div>
                <div class="stat-label"><?php echo $esAdmin ? 'Total Compras' : 'Mis Compras'; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($total_gastado, 0, ',', '.'); ?></div>
                <div class="stat-label"><?php echo $esAdmin ? 'Total Facturado' : 'Total Gastado'; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $compras_mes; ?></div>
                <div class="stat-label">Compras Este Mes</div>
            </div>
            <?php if($esAdmin): ?>
            <div class="stat-card">
                <div class="stat-number">$<?php echo $total_compras > 0 ? number_format($total_gastado / $total_compras, 0, ',', '.') : '0'; ?></div>
                <div class="stat-label">Promedio por Compra</div>
            </div>
            <?php endif; ?>
        </div>

        <?php if(isset($historial_data) && count($historial_data) > 0): ?>
            <table class="historial-table">
                <thead>
                    <tr>
                        <th>ID Compra</th>
                        <?php if($esAdmin): ?>
                            <th>Cliente</th>
                            <th>Email</th>
                        <?php endif; ?>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($historial_data as $row): ?>
                        <tr>
                            <td>#<?php echo $row['ID_pago']; ?></td>
                            <?php if($esAdmin): ?>
                                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <?php endif; ?>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_compra'])); ?></td>
                            <td class="total">$<?php echo number_format($row['total_compra'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="estado estado-<?php echo $row['estado']; ?>">
                                    <?php echo ucfirst($row['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="detalles-btn" onclick="toggleDetalles(<?php echo $row['ID_pago']; ?>)">
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                        <tr id="detalles-<?php echo $row['ID_pago']; ?>" style="display: none;">
                            <td colspan="<?php echo $esAdmin ? '7' : '5'; ?>">
                                <div class="detalles-compra">
                                    <h4>Productos Comprados:</h4>
                                    <?php
                                    $detalles = obtenerDetallesCompra($row['ID_pago'], $Ruta);
                                    if($detalles && mysqli_num_rows($detalles) > 0):
                                        while($detalle = mysqli_fetch_assoc($detalles)):
                                    ?>
                                        <div class="producto-item">
                                            <div>
                                                <strong><?php echo htmlspecialchars($detalle['titulo']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($detalle['lugar']); ?> - <?php echo $detalle['dias']; ?> días</small>
                                            </div>
                                            <div>
                                                <span>Cantidad: <?php echo $detalle['cantidad']; ?></span><br>
                                                <span class="total">$<?php echo number_format($detalle['subtotal'], 0, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <p>No se encontraron detalles para esta compra.</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-historial">
                <i>📋</i>
                <h3><?php echo $esAdmin ? 'No hay compras registradas' : 'No tienes compras realizadas'; ?></h3>
                <p><?php echo $esAdmin ? 'Cuando los clientes realicen compras, aparecerán aquí.' : 'Cuando realices tu primera compra, aparecerá aquí.'; ?></p>
                <?php if(!$esAdmin): ?>
                    <a href="paquetes.php" class="nav-link">Ver Paquetes Disponibles</a>
                <?php endif; ?>
                
                <!-- Botón de debug para verificar datos -->
                <div style="margin-top: 2rem;">
                    <a href="?debug=1" class="nav-link">🔍 Mostrar Info de Debug</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetalles(idPago) {
            const detallesRow = document.getElementById('detalles-' + idPago);
            const btn = event.target;
            
            if (detallesRow.style.display === 'none' || detallesRow.style.display === '') {
                detallesRow.style.display = 'table-row';
                btn.textContent = 'Ocultar Detalles';
            } else {
                detallesRow.style.display = 'none';
                btn.textContent = 'Ver Detalles';
            }
        }

        // Auto-refresh cada 30 segundos para admins
        <?php if($esAdmin): ?>
        setInterval(function() {
            // Solo refrescar si no hay detalles abiertos
            const detallesAbiertos = document.querySelectorAll('[id^="detalles-"]');
            let hayAbiertos = false;
            detallesAbiertos.forEach(function(detalle) {
                if(detalle.style.display === 'table-row') {
                    hayAbiertos = true;
                }
            });
            
            if(!hayAbiertos) {
                location.reload();
            }
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>
