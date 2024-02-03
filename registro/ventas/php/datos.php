<?php

include 'conexion.php'; // Incluir el archivo de conexión

// Configurar la zona horaria a Venezuela
date_default_timezone_set('America/Caracas');

$fechaActual = date('Y-m-d'); // Obtener la fecha actual en formato YYYY-MM-DD

// Consulta SQL para obtener los datos de ventas para la fecha actual
$sql = "SELECT monto FROM datos WHERE DATE(fecha) = '$fechaActual'";

$sql2 = "SELECT monto FROM datos";


$result = mysqli_query($conn, $sql);

$result2 = mysqli_query($conn, $sql2);


$sqlEcuador = "SELECT ecuador_pendiente FROM sumas WHERE id = 1";
$resultEcuador = mysqli_query($conn, $sqlEcuador);

if ($resultEcuador) {
    $rowEcuador = mysqli_fetch_assoc($resultEcuador);
    $ecuadorPendiente = $rowEcuador['ecuador_pendiente'];
    
    echo "$" . $ecuadorPendiente;
} else {
    echo "Error al obtener el valor de Ecuador Pendiente: " . mysqli_error($conn);
}

$sqlPeru = "SELECT peru_pendiente FROM sumas WHERE id = 1";
$resultPeru = mysqli_query($conn, $sqlPeru);

if ($resultPeru) {
    $rowPeru = mysqli_fetch_assoc($resultPeru);
    $PeruPendiente = $rowPeru['peru_pendiente'];
    
    echo "$" . $PeruPendiente;
} else {
    echo "Error al obtener el valor de Ecuador Pendiente: " . mysqli_error($conn);
}


$sumaVentas = 0; // Inicializar la variable para almacenar la suma de ventas

$sumaVentas2 = 0; // Inicializar la variable para almacenar la suma de ventas



if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $monto = floatval(str_replace(['$', ','], '', $row['monto'])); // Convertir el monto a número decimal
    $sumaVentas += round($monto, 2); // Sumar el monto a la suma total de ventas, redondeado a 2 decimales
  }
}

if ($result2->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result2)) {
    $monto2 = floatval(str_replace(['$', ','], '', $row['monto']));
    $sumaVentas2 += round($monto2, 2);
  }
}

mysqli_close($conn); // Cerrar la conexión con la base de datos

// Mostrar la suma de ventas para la fecha actual
echo "$" . number_format(round($sumaVentas, 0), 0);

// Mostrar la suma total de ventas
echo "$" . number_format(round($sumaVentas2, 0), 0);



?>