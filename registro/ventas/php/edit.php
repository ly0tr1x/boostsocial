<?php
try {

    $db = new PDO("mysql:host=localhost;dbname=u877644141_ventas;charset=utf8mb4", "u877644141_admin", "[Atbx!n3");

    // Establecer el modo de errores de PDO para que lance excepciones en caso de error
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera los datos que el usuario ingresó
    $id = $_POST['id']; // Se asume que tienes un campo ID para identificar la fila que se actualizará
    
    $pais = $_POST['pais'];
    $numero_celular = $_POST['numero_celular'];
    $monto = $_POST['monto'];
    $estado = $_POST['estado'];

    // Preparar la consulta con parámetros para actualizar los datos
    $query = "UPDATE datos SET pais = :pais, numero_celular = :numero_celular, 
              monto = :monto, estado = :estado WHERE id = :id";

    // Prepara la consulta para evitar inyecciones SQL
    $statement = $db->prepare($query);

    // Asigna los valores a los parámetros
    $statement->bindParam(':pais', $pais);
    $statement->bindParam(':numero_celular', $numero_celular);
    $statement->bindParam(':monto', $monto);
    $statement->bindParam(':estado', $estado);
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
