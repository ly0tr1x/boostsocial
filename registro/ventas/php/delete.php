<?php
// Archivo eliminar_filas.php

// Conexión a la base de datos (debes completar con tus propios datos de conexión)
$conexion = new mysqli("localhost", "u877644141_admin", "[Atbx!n3", "u877644141_ventas");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

if (isset($_POST['service']) && !empty($_POST['service'])) {
    // Escapar los valores para prevenir inyección SQL
    $ids_a_eliminar = array_map('intval', $_POST['service']);

    // Construir la consulta para eliminar las filas
    $ids = implode(",", $ids_a_eliminar);
    $consulta = "DELETE FROM datos WHERE id IN ($ids)";

    // Ejecutar la consulta
    if ($conexion->query($consulta) === TRUE) {
        echo "Eliminación exitosa"; // Envía una respuesta específica para indicar la eliminación exitosa
    } else {
        echo "Error al eliminar filas: " . $conexion->error;
    }
} else {
    echo "No se han proporcionado datos para eliminar.";
}

// Cerrar la conexión
$conexion->close();
?>
