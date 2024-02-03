<?php
include 'conexion.php'; // Incluir el archivo de conexión

$records_per_page = 20; // Cantidad de registros por página
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Página actual, obtenida del parámetro de la URL
$start_from = ($page - 1) * $records_per_page; // Cálculo del inicio del conjunto de resultados

// Consulta SQL para obtener los datos
$sql = "SELECT id, fecha, pais, numero_celular, monto FROM datos ORDER BY id DESC LIMIT 100 ";

$result = mysqli_query($conn, $sql);

$data = array(); // Inicializar un array para almacenar los datos

if ($result->num_rows > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row; // Almacena cada fila de datos en el array $data
    }
}

mysqli_close($conn); // Cerrar la conexión con la base de datos

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($data);
?>