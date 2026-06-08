<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$id_estilo = intval($_GET['id']);

// Verificar si ya existe en favoritos
$check = $conn->prepare("SELECT id FROM favoritos WHERE id_usuario = ? AND id_estilo = ?");
$check->bind_param("ii", $id_usuario, $id_estilo);
$check->execute();
$res = $check->get_result();

if ($res->num_rows == 0) {
    // Si no existe, lo agregamos
    $stmt = $conn->prepare("INSERT INTO favoritos (id_usuario, id_estilo) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_usuario, $id_estilo);
    $stmt->execute();
} else {
    // Si ya existe, lo borramos 
    $stmt = $conn->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_estilo = ?");
    $stmt->bind_param("ii", $id_usuario, $id_estilo);
    $stmt->execute();
}

// Redirección 
if (isset($_GET['from']) && $_GET['from'] === 'fav') {
    // Si venimos de la lista de favoritos o queremos volver ahí:
    header("Location: mis_favoritos.php");
} else {
    // Por defecto vuelve a ver el estilo
    header("Location: ver_estilo.php?id=" . $id_estilo);
}
exit;