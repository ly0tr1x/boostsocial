
<?php
// Archivo deleteDatosPeru.php

// Conexión a la base de datos (reemplaza 'localhost', 'usuario', 'contraseña' y 'nombre_base_de_datos' con tus propios datos)
$conexion = new mysqli("localhost", "u877644141_admin", "[Atbx!n3", "u877644141_ventas");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Actualizar el campo en la base de datos con el nuevo valor
$query2 = "UPDATE sumas SET ecuador_pendiente = 0 WHERE id = 1";

// Ejecutar la consulta directamente sin preparación
$result2 = $conexion->query($query2);

// Verificar si la consulta se ejecutó correctamente
if ($result2 === true) {
    echo "Eliminación exitosa"; // Envía un mensaje de éxito al cliente
} else {
    echo "Error al eliminar datos: " . $conexion->error; // Envía un mensaje de error al cliente
}

// Cerrar la conexión
$conexion->close();
?>
