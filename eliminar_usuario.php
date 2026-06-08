<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = intval($_POST['id_usuario']);
    $motivo = $_POST['motivo'];

    if (!empty($id_usuario) && !empty($motivo)) {
        // En lugar de hacer DELETE duro, hacemos un Soft Delete (Baja lógica) para guardar el motivo
        $stmt = $conn->prepare("UPDATE usuarios SET eliminado = 1, motivo_eliminacion = ? WHERE id = ?");
        $stmt->bind_param("si", $motivo, $id_usuario);
        
        if ($stmt->execute()) {
            header("Location: panel_admin.php?status=success");
            exit;
        }
    }
}
header("Location: panel_admin.php");
exit;
?>