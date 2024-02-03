<?php
try {

    $db = new PDO("mysql:host=localhost;dbname=u877644141_ventas;charset=utf8mb4", "u877644141_admin", "[Atbx!n3");

    // Establecer el modo de errores de PDO para que lance excepciones en caso de error
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera los datos que el usuario ingresó
    $id = $_POST['id']; // Se asume que tienes un campo ID para identificar la fila que se actualizará
    $fecha = $_POST['fecha'];
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

    // Preparar la consulta con parámetros para actualizar los datos
    $query = "UPDATE datos SET fecha = :fecha, tienda_online = :tienda_online, pais = :pais, numero_celular = :numero_celular, 
              red_social = :red_social, servicios = :servicios, cantidad = :cantidad, enlace = :enlace, metodo_pago = :metodo_pago, 
              monto = :monto, estado = :estado, notas = :notas WHERE id = :id";

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
    $statement->bindParam(':id', $id);

    // Ejecutar la consulta preparada
    $result = $statement->execute();

    // Verificar el resultado
    if ($result) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error al actualizar los datos.";
    }
} catch (PDOException $e) {
    // Captura cualquier excepción de PDO (error de base de datos) y muestra el mensaje de error
    echo "Error de base de datos: " . $e->getMessage();
}
?>
