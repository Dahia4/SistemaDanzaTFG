<?php
session_start();
include 'conexion.php';

// Seguridad: Solo profesores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'profesor') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Borrar registro de la tabla asistencia
    $stmt = $conn->prepare("DELETE FROM asistencia WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirigir con un mensaje de éxito
        header("Location: lista_asistencias.php?msg=eliminado");
    } else {
        echo "Error al intentar eliminar el registro.";
    }
    $stmt->close();
} else {
    header("Location: lista_asistencias.php");
}
exit;