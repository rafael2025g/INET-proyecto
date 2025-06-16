<?php
session_start();
include __DIR__ . '/../controller/conexionbd.php';

// Claves y constantes
define("KEY", "develoteca");
define("COD", "AES-128-ECB");
define("KEY_TOKEN", "APR.wqc-354*");

// --- Función para guardar paquete turístico ---
if(isset($_POST["ingresar"])){
    
    $dias=$_POST["dias"];
    $stock=$_POST["cantidad"];
    $precio=$_POST["precio"];
    $descripcion=$_POST["descripcion"];
    $lugar=$_POST["lugar"];
    $titulo=$_POST["titulo"];
    $descuento=$_POST["descuento"];
    $imagen=addslashes(file_get_contents($_FILES['img']['tmp_name']));
    
    //consulta
    $consulta_Sql="INSERT INTO ventas(imagen, titulo, descripcion, precio, stock, lugar, dias, descuento) VALUES('$imagen', '$titulo', '$descripcion', '$precio', '$stock', '$lugar', '$dias', '$descuento')";
    $validacion=mysqli_query($Ruta, $consulta_Sql);
    if($validacion){
        echo '<script>alert("Paquete registrado exitosamente"); window.location.href="../pages/paquetes.php";</script>';
        
    }
    else{
        echo '<script>alert("Error al registrar paquete");</script>';
    }
}

// --- Función para actualizar paquete ---
if(isset($_POST["actualizar"])){
    
    $id = $_POST["id"];
    $dias=$_POST["dias"];
    $stock=$_POST["cantidad"];
    $precio=$_POST["precio"];
    $descripcion=$_POST["descripcion"];
    $lugar=$_POST["lugar"];
    $titulo=$_POST["titulo"];
    $descuento=$_POST["descuento"];
    
    if($_FILES['img']['size'] > 0) {
        $imagen=addslashes(file_get_contents($_FILES['img']['tmp_name']));
        $consulta_Sql="UPDATE ventas SET imagen='$imagen', titulo='$titulo', descripcion='$descripcion', precio='$precio', stock='$stock', lugar='$lugar', dias='$dias', descuento='$descuento' WHERE ID_Producto='$id'";
    } else {
        $consulta_Sql="UPDATE ventas SET titulo='$titulo', descripcion='$descripcion', precio='$precio', stock='$stock', lugar='$lugar', dias='$dias', descuento='$descuento' WHERE ID_Producto='$id'";
    }
    
    $validacion=mysqli_query($Ruta, $consulta_Sql);
    if($validacion){
        echo '<script>alert("Paquete actualizado exitosamente"); window.location.href="../pages/paquetes.php";</script>';
    }
    else{
        echo '<script>alert("Error al actualizar paquete");</script>';
    }
}

// --- Función para eliminar paquete ---
if(isset($_POST["eliminar"])){
    $id = $_POST["id"];
    $consulta_Sql="DELETE FROM ventas WHERE ID_Producto='$id'";
    $validacion=mysqli_query($Ruta, $consulta_Sql);
    if($validacion){
        echo '<script>alert("Paquete eliminado exitosamente"); window.location.href="../pages/paquetes.php";</script>';
    }
    else{
        echo '<script>alert("Error al eliminar paquete");</script>';
    }
}

// --- Lógica del carrito (solo para clientes) ---

if (isset($_POST['btnAccion'])) {
    // Verificar que el usuario esté logueado y sea cliente
    if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'cliente') {
        header("Location: login.php");
        exit();
    }
    
    switch ($_POST['btnAccion']) {
        case 'agregar':
            $id = openssl_decrypt($_POST['id'], COD, KEY);
            $titulo = openssl_decrypt($_POST['titulo'], COD, KEY);
            $precio = openssl_decrypt($_POST['precio'], COD, KEY);
            $cantidad = openssl_decrypt($_POST['cantidad'], COD, KEY);

            if (is_numeric($id) && is_string($titulo) && is_numeric($precio) && is_numeric($cantidad)) {
                $producto = [
                    'id' => $id,
                    'titulo' => $titulo,
                    'precio' => $precio,
                    'cantidad' => $cantidad
                ];

                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                $_SESSION['carrito'][] = $producto;
                $mensaje = "Producto agregado al carrito";
            } else {
                $mensaje = "Datos del producto no válidos.";
            }
            break;

        case 'eliminar':
            $id = openssl_decrypt($_POST['id'], COD, KEY);
            if (is_numeric($id)) {
                foreach ($_SESSION['carrito'] as $index => $producto) {
                    if ($producto['id'] == $id) {
                        unset($_SESSION['carrito'][$index]);
                        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                        break;
                    }
                }
            }
            break;

        case 'vaciar':
            $_SESSION['carrito'] = [];
            break;
    }
}

// --- Login de usuario ---
if (isset($_POST['login'])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["contraseña"])) {
        $usuario = $_POST["usuario"];
        $contraseña = $_POST["contraseña"];

        $consulta_sql = $Ruta->prepare("SELECT * FROM usuario WHERE usuario = ? OR email = ?");
        $consulta_sql->bind_param("ss", $usuario, $usuario);
        $consulta_sql->execute();
        $resultado = $consulta_sql->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            if (password_verify($contraseña, $fila['contraseña'])) {
                $_SESSION['rol'] = $fila['rol'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['ID_usuario'] = $fila['ID_usuario'];

                header("Location: ../index.php");
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "Usuario no encontrado.";
        }
        $consulta_sql->close();
    } else {
        echo "Completa ambos campos.";
    }
}

// --- Función para obtener paquetes ---
function obtenerPaquetes() {
    global $Ruta;
    $consulta = "SELECT * FROM ventas ORDER BY titulo ASC";
    $resultado = mysqli_query($Ruta, $consulta);
    return $resultado;
}

// --- Función para obtener paquete por ID ---
function obtenerPaquetePorId($id) {
    global $Ruta;
    $consulta = $Ruta->prepare("SELECT * FROM ventas WHERE ID_Producto = ?");
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $resultado = $consulta->get_result();
    return $resultado->fetch_assoc();
}

// --- Función para verificar si el usuario puede comprar ---
function puedeComprar() {
    return isset($_SESSION['usuario']) && $_SESSION['rol'] === 'cliente';
}

// --- Función para verificar si es admin ---
function esAdmin() {
    return isset($_SESSION['usuario']) && $_SESSION['rol'] === 'admin';
}

// --- Función para obtener historial de compras ---
function obtenerHistorialUsuario($ID_usuario) {
    global $Ruta;
    $consulta = $Ruta->prepare("SELECT h.*, p.fecha_pago 
                               FROM historial h 
                               INNER JOIN pagos p ON h.ID_pago = p.ID_pago 
                               WHERE h.ID_usuario = ? 
                               ORDER BY h.fecha_compra DESC");
    $consulta->bind_param("i", $ID_usuario);
    $consulta->execute();
    return $consulta->get_result();
}

// --- Función para obtener todo el historial (solo admin) ---
function obtenerTodoHistorial() {
    global $Ruta;
    $consulta = "SELECT h.*, u.usuario, u.email, p.fecha_pago 
                FROM historial h 
                INNER JOIN usuario u ON h.ID_usuario = u.ID_usuario 
                INNER JOIN pagos p ON h.ID_pago = p.ID_pago 
                ORDER BY h.fecha_compra DESC";
    return mysqli_query($Ruta, $consulta);
}

// --- Función para insertar en historial ---
function insertarHistorial($ID_usuario, $ID_pago, $total_compra, $detalles) {
    global $Ruta;
    $consulta = $Ruta->prepare("INSERT INTO historial (ID_usuario, ID_pago, total_compra, detalles) VALUES (?, ?, ?, ?)");
    $consulta->bind_param("iids", $ID_usuario, $ID_pago, $total_compra, $detalles);
    return $consulta->execute();
}
?>
