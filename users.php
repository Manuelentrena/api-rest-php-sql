<?php
  /* DEPENDENCIES */
  require_once 'class/response.class.php';
  require_once 'class/users.class.php';

  $_response = new response;
  $_user = new user;
  $resquest = $_SERVER['REQUEST_METHOD'];
  $postBody = file_get_contents("php://input");
  header('content-Type: application/json');

  switch ($resquest) {
    case "GET":
      /* LIST USER BY PAGE */
      if(isset($_GET["page"])){ 
        $page = $_GET["page"];
        $res = $_user->listUsers($postBody,$page);
      /* SEARCH USER BY ID */
      }elseif(isset($_GET["id"])){ 
        $id = $_GET["id"];
        $res = $_user->oneUser($postBody,$id);
      /* SEARCH USER BY NAME */
      }elseif(isset($_GET["name"])){ 
        $name = $_GET["name"];
        $res = $_user->usersByName($postBody,$name);
      /* SEARCH USER BY LASTNAME */
      }elseif(isset($_GET["lastname"])){ 
        $lastName = $_GET["lastname"];
        $res = $_user->usersByLastName($postBody,$lastName);
      /* SEARCH USER BY EMAIL */
      }elseif(isset($_GET["email"])){ 
        $email = $_GET["email"];
        $res = $_user->usersByEmail($postBody,$email);
      /* LIST USER BY PAGE 1 */
      }else{
        $res = $_user->listUsers($postBody);

      }
      http_response_code(200);
      print_r("AL FINAL");
      print_r($res);
      echo json_encode($res);
      break;

    case "POST": 
      $res = $_user->postUser($postBody);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res);
      break;

    case "PUT": 
      $res = $_user->putUser($postBody);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res);
      break;

    case "DELETE": 
      $res = $_user->deleteUser($postBody);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res);
      break;

    default: /* RESQUEST NOT PERMIT */
      header('content-Type: application/json');
      $data = $_response->error_405();
      echo json_encode($data);
}
?>