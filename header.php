<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Biblioteca de Danza</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="css/estilos_formularios.css">

    <style>
        html, body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: #6f2da8;
            width: 100%;
        }

        .wrapper-sistema {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 30px 0; 
        }

        .wrapper-sistema > .container {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom mb-3">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-bold" href="#">
        <i class="fa-solid fa-graduation-cap me-2"></i> Plataforma de Danza
    </a>

    <div class="d-flex">
    <?php 
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    if ($pagina_actual == 'panel_profesor.php' || $pagina_actual == 'panel_alumno.php'): 
    ?>
        <a class="btn btn-danger btn-sm logout-btn" href="logout.php" style="border-radius: 8px; font-weight: 600; width: auto; padding: 5px 15px;">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
        </a>
    <?php endif; ?>
    </div>
  </div>
</nav>

<div class="wrapper-sistema">
    <div class="container">