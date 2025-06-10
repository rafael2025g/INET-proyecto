<?php
session_start();
include('../controller/conexionbd.php');

// Claves y constantes
define("KEY", "develoteca");
define("COD", "AES-128-ECB");
define("KEY_TOKEN", "APR.wqc-354*");

// --- Función para guardar imagen como BLOB ---
if(isset($_POST["ingresar"])){
    
    $dias=$_POST["dias"];
    $stock=$_POST["cantidad"];
    $precio=$_POST["precio"];
    $descripcion=$_POST["descripcion"];
    $lugar=$_POST["lugar"];
    $titulo=$_POST["titulo"];
    $imagen=addslashes(file_get_contents($_FILES['img']['tmp_name']));
    include('../controller/conexionbd.php');

    
    //consulta
    $consulta_Sql="INSERT INTO indumentaria(imagen, título, descripcion, precio, stock, lugar, dias)VALUES('$imagen', '$titulo', '$descripcion', '$precio', '$stock', '$lugar', '$dias')";
    $validacion=mysqli_query($Ruta, $consulta_Sql);
    if($validacion){
        echo 'registo';
        
    }
    else{
        echo 'No Registrado';
    }
}

// --- Lógica del carrito ---
$mensaje = "";

if (isset($_POST['btnAccion'])) {
    switch ($_POST['btnAccion']) {
        case 'agregar':
            $id = openssl_decrypt($_POST['id'], COD, KEY);
            $titulo = openssl_decrypt($_POST['titulo'], COD, KEY);
            $precio = openssl_decrypt($_POST['precio'], COD, KEY);
            $cantidad = openssl_decrypt($_POST['cantidad'], COD, KEY);

            if (is_numeric($id) && is_string($modelo) && is_numeric($precio) && is_numeric($cantidad)) {
                $producto = [
                    'id' => $id,
                    'titulo' => $titulo,
                    'precio' => $precio,
                    'cantidad' => $cantidad
                ];

                $_SESSION['carrito'][] = $producto;
                $mensaje = print_r($_SESSION, true);
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
    }
}

// --- Login de usuario ---
if (!empty($_POST["login"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["contraseña"])) {
        $usuario = $_POST["usuario"];
        $contraseña = $_POST["contraseña"];

        $consulta_sql = $Ruta->prepare("SELECT * FROM usuario WHERE usuario = ? OR gmail = ?");
        $consulta_sql->bind_param("ss", $usuario, $usuario);
        $consulta_sql->execute();
        $resultado = $consulta_sql->get_result();

        if ($fila = $resultado->fetch_assoc()) {
           
            if (password_verify($contraseña, $fila['contraseña'])) {
               $_SESSION['rol'] = $fila['rol'];
               $_SESSION['nombre'] = $fila['nombre'];
               $_SESSION['usuario'] = $fila['usuario'];

                header("Location: pagina/inicio.php");
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "Usuario no encontrado.";
        }
        $stmt->close();
    } else {
        echo "Completa ambos campos.";
    }
}

// --- Registro de nuevo usuario ---
if (!empty($_POST["usuario"])) {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $usuario = $_POST["usuario"];
    $gmail = $_POST["gmail"];
    $contraseña = $_POST["contraseña"];
    $telefono = $_POST["telefono"];
    $rol = $_POST["rol"];

    $contraseña_encry = password_hash($contraseña, PASSWORD_BCRYPT);

    $stmt = $Ruta->prepare("INSERT INTO usuario(nombre, apellido, usuario, gmail, contraseña,telefono,rol) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nombre, $apellido, $usuario, $gmail, $contraseña_encry,$telefono,$rol);

    if ($stmt->execute()) {
        header("Location: ../pagina/iniciar.php");
        exit();
    } else {
        echo "Error al registrar usuario.";
    }
    $stmt->close();
}
?>
