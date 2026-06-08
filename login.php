<?php
session_start();
include 'conexion.php';

if(isset($_POST['email']) && isset($_POST['contraseña']) && isset($_POST['rol'])){
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $rolSeleccionado = $_POST['rol'];

    // Buscar usuario por email y rol
    $sql = "SELECT * FROM usuarios WHERE email='$email' AND rol='$rolSeleccionado' LIMIT 1";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contraseña, $usuario['contraseña'])) {
            
            
            // Validación, verificar si fue eliminado
            if (isset($usuario['eliminado']) && $usuario['eliminado'] == 1) {
                $motivo = !empty($usuario['motivo_eliminacion']) ? $usuario['motivo_eliminacion'] : 'Baja administrativa.';
                header("Location: index.php?error=cuenta_eliminada&motivo=" . urlencode($motivo));
                exit;
            }

            // Guardar datos en sesión si la cuenta está activa
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['nombre'] = $usuario['nombre']; 

            // Redirigir según rol 
            if ($usuario['rol'] == 'admin') {
                header("Location: panel_admin.php");
            } elseif ($usuario['rol'] == 'profesor') {
                header("Location: panel_profesor.php");
            } else {
                header("Location: panel_alumno.php");
            }
            exit;
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado o rol incorrecto";
    }
} else {
    echo "Por favor completa todos los campos del formulario";
}
?>


