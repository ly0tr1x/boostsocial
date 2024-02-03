<?php
$servername = "localhost";
$username = "u877644141_admin";
$password = "[Atbx!n3";
$dbname = "u877644141_ventas";

// Crear conexión con la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
