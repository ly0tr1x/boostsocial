<?php
// Establecer conexión a la base de datos (reemplaza 'host', 'usuario', 'contraseña' y 'nombre_base_de_datos' con tus propios datos)
include 'conexion.php'; // Incluir el archivo de conexión



// Obtener las filas que cumplen la condición (fecha mayor a 3 días)
$query = "UPDATE datos SET estado = 'Completado' WHERE fecha < DATE_SUB(NOW(), INTERVAL 3 DAY)";
$result = $conn->query($query);

if (!$result) {
    echo "Error al ejecutar la consulta: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
