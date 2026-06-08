<?php
$contraseña = "1234"; // la contraseña que quieres usar
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo $hash;
?>
