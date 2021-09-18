<?php
  require_once "class/connection/connection.php";
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  $_response = new response;
  $connection = new connection;

  header('content-Type: application/json');
  $data = $_response->error_405();
  echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>