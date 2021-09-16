<?php
  require_once "class/connection/connection.php";
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  $connection = new connection;

  /* $query = "SELECT * FROM users"; */
  /* $query = "INSERT INTO users (name, lastname, email, password, direction) value ('Prueba','Apellido','prueba2@gmail.com','123','calle')"; */

  /* print_r($connection->obtenerDatos($query)); */
?>

hola index