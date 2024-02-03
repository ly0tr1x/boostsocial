<?php
try {

    
  $db = new PDO("mysql:host=localhost;dbname=u877644141_ventas;charset=utf8mb4", "u877644141_admin", "[Atbx!n3");

  // Establece el modo de errores de PDO para que lance excepciones en caso de error
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Recupera los datos que el usuario ingresó
$timestamp = time(); // Obtiene la marca de tiempo actual

$fecha = date('Y-m-d', $timestamp); // Convierte la marca de tiempo a una fecha
  $fecha_formateada = date('Y-m-d', strtotime($fecha)); // Esto formateará la fecha al formato YYYY-MM-DD
  $tienda_online = $_POST['tienda_online'];
  $pais = $_POST['pais'];
  $numero_celular = $_POST['numero_celular'];
  $red_social = $_POST['red_social'];
  $servicios = $_POST['servicios'];
  $cantidad = $_POST['cantidad'];
  $enlace = $_POST['enlace'];
  $metodo_pago = $_POST['metodo_pago'];
  $monto = $_POST['monto'];
  $estado = $_POST['estado'];
  $notas = $_POST['notas'];

// Formatear la variable $pais a minúsculas
$pais2 = strtolower($pais);


// Consultar el valor actual del campo
$query = "SELECT {$pais}_pendiente FROM sumas WHERE id = 1";
$statement = $db->prepare($query);
$statement->execute();
$valorActual = $statement->fetchColumn();

// Realizar la suma del valor actual con el nuevo valor
$nuevoValor = $valorActual + $monto;


  // Preparar la consulta con parámetros
  $query = "INSERT INTO datos (fecha, tienda_online, pais, numero_celular, red_social, servicios, cantidad, enlace, metodo_pago, monto, estado, notas)
       VALUES (:fecha, :tienda_online, :pais, :numero_celular, :red_social, :servicios, :cantidad, :enlace, :metodo_pago, :monto, :estado, :notas)";
       
if ($metodo_pago != 'Paypal') {
  // Actualizar el campo en la base de datos con el nuevo valor
  $query2 = "UPDATE sumas SET {$pais}_pendiente = :nuevoValor WHERE id = 1";
  $statement2 = $db->prepare($query2);
  $statement2->bindParam(':nuevoValor', $nuevoValor);
  $result2 = $statement2->execute();
}
  
  // Prepara la consulta para evitar inyecciones SQL
  $statement = $db->prepare($query);




  // Asigna los valores a los parámetros
  $statement->bindParam(':fecha', $fecha_formateada);
  $statement->bindParam(':tienda_online', $tienda_online);
  $statement->bindParam(':pais', $pais);
  $statement->bindParam(':numero_celular', $numero_celular);
  $statement->bindParam(':red_social', $red_social);
  $statement->bindParam(':servicios', $servicios);
  $statement->bindParam(':cantidad', $cantidad);
  $statement->bindParam(':enlace', $enlace);
  $statement->bindParam(':metodo_pago', $metodo_pago);
  $statement->bindParam(':monto', $monto);
  $statement->bindParam(':estado', $estado);
  $statement->bindParam(':notas', $notas);

  // Ejecutar la consulta preparada
  $result = $statement->execute();

  // Verificar el resultado
  if ($result) {
    exit();
  } else {
    echo "Error al ingresar los datos.";
  }
} catch (PDOException $e) {
  // Captura cualquier excepción de PDO (error de base de datos) y muestra el mensaje de error
  echo "Error de base de datos: " . $e->getMessage();
}























?>