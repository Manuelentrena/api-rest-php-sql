<?php
  /* DEPENDENCIES */
  require_once 'class/response.class.php';
  require_once 'class/states.class.php';

  $_response = new response;
  $_state = new state;
  $resquest = $_SERVER['REQUEST_METHOD'];
  $postBody = file_get_contents("php://input");
  $headers = getallheaders();

  header('content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  switch ($resquest) {
    case "GET":
      /* LIST STATES BY PAGE */
      if(isset($_GET["page"])){ 
        $page = $_GET["page"];
        $res = $_state->listStates($headers,$page);
      /* SEARCH STATE BY ID */
      }elseif(isset($_GET["id"])){ 
        $id = $_GET["id"];
        $res = $_state->oneState($headers,$id);
      }else{
        $res = $_state->listStates($headers);
      }
      http_response_code(200);
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "POST": 
      $res = $_state->postState($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "PUT": 
      $res = $_state->putState($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "DELETE": 
      $res = $_state->deleteState($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    default: /* RESQUEST NOT PERMIT */
      header('content-Type: application/json');
      $data = $_response->error_405();
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

?>