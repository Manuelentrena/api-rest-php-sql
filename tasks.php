<?php
  /* DEPENDENCIES */
  require_once 'class/response.class.php';
  require_once 'class/task.class.php';

  $_response = new response;
  $_task = new task;
  $resquest = $_SERVER['REQUEST_METHOD'];
  $postBody = file_get_contents("php://input");
  $headers = getallheaders();

  header('content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token");
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  switch ($resquest) {
    case "GET": /* SHOW TASKS AND TASK */
      /* SHOW LISTTASK */
      if(isset($_GET["page"])){ 
        $page = $_GET["page"];
        $res = $_task->listTask($headers,$page);
      /* SHOW ONE TASK */
      }elseif(isset($_GET["id"])){ 
        $id = $_GET["id"];
        $res = $_task->oneTask($headers,$id);
      /* SEARCH TASK BY NAME */
      }elseif(isset($_GET["name"])){ 
        $name = $_GET["name"];
        $res = $_task->taskByName($headers,$name);
      /* SEARCH TASK BY USER */
      }elseif(isset($_GET["user"])){ 
        $user = $_GET["user"];
        $res = $_task->taskByUser($headers,$user);
      /* SEARCH TASK BY STATE */
      }elseif(isset($_GET["state"])){ 
        $state = $_GET["state"];
        $res = $_task->taskByState($headers,$state);
      /* SHOW BY DEFAULT */
      }else{
        $res = $_task->listTask($headers);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      http_response_code(200);
      break;

    case "POST": /* SAVE TASK */
      $res = $_task->postTask($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "PUT": /* MODIFY TASK */
      $res = $_task->putTask($postBody,$headers);
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
      break;

    case "DELETE": /* DELETE TASK */
      $res = $_task->deleteTask($postBody,$headers);
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