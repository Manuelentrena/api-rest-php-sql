<?php
  /* DEPENDENCIES */
  require_once 'class/response.class.php';
  require_once 'class/users.class.php';

  header('content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  $_response = new response;
  $_user = new user;
  $resquest = $_SERVER['REQUEST_METHOD'];
  $postBody = file_get_contents("php://input");
  $headers = getallheaders();

  switch ($resquest) {
    case "GET":
      /* LIST USER BY PAGE */
      if(isset($_GET["page"])){ 
        $page = $_GET["page"];
        $res = $_user->listUsers($headers,$page);
      /* SEARCH USER BY ID */
      }elseif(isset($_GET["id"])){ 
        $id = $_GET["id"];
        $res = $_user->oneUser($headers,$id);
      /* SEARCH USER BY NAME */
      }elseif(isset($_GET["name"])){ 
        $name = $_GET["name"];
        $res = $_user->usersByName($headers,$name);
      /* SEARCH USER BY LASTNAME */
      }elseif(isset($_GET["lastname"])){ 
        $lastName = $_GET["lastname"];
        $res = $_user->usersByLastName($headers,$lastName);
      /* SEARCH USER BY EMAIL */
      }elseif(isset($_GET["email"])){ 
        $email = $_GET["email"];
        $res = $_user->usersByEmail($headers,$email);
      /* LIST USER BY PAGE 1 */
      }else{
        $res = $_user->listUsers($headers);

      }
      http_response_code(200);
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "POST": 
      $res = $_user->postUser($postBody);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "PUT": 
      $res = $_user->putUser($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "DELETE": 
      $res = $_user->deleteUser($postBody,$headers);
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
      $data = $_response->error_405("Error de Ruta con el servidor");
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>