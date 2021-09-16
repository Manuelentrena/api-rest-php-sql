<?php
  /* DEPENDENCIAS */
  require_once 'class/auth.class.php';
  require_once 'class/response.class.php';

  $_auth = new auth;
  $_response = new response;

  header('content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    /* receive data(body) */
    $postBody = file_get_contents("php://input");

    /* middleware data to handler auth */
    $data = $_auth->login($postBody);

    /* set Response data */
    header('content-Type: application/json');
    if(isset($data["result"]["error_id"])){
      $responseCode = $data["result"]["error_id"];
      http_response_code($responseCode);
    }else{
      http_response_code(200);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
  }else{
    header('content-Type: application/json');
    $data = $_response->error_405();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
  }

?>