<?php
  require_once "class/connection/connection.php";
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');


  $connection = new connection;

?>