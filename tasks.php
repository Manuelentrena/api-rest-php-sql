<?php
  /* DEPENDENCIES */
  require_once 'class/response.class.php';
  require_once 'class/task.class.php';

  $_response = new response;
  $_task = new task;
  $resquest = $_SERVER['REQUEST_METHOD'];

  switch ($resquest) {
    case "GET": /* SHOW TASKS AND TASK */
      header('content-Type: application/json');
      $postBody = file_get_contents("php://input");
      /* SHOW LISTTASK */
      if(isset($_GET["page"])){ 
        $page = $_GET["page"];
        $res = $_task->listTask($postBody,$page);
      /* SHOW ONE TASK */
      }elseif(isset($_GET["id"])){ 
        $id = $_GET["id"];
        $res = $_task->oneTask($postBody,$id);
      /* SEARCH TASK BY NAME */
      }elseif(isset($_GET["name"])){ 
        $name = $_GET["name"];
        $res = $_task->taskByName($postBody,$name);
      /* SEARCH TASK BY USER */
      }elseif(isset($_GET["user"])){ 
        $user = $_GET["user"];
        $res = $_task->taskByUser($postBody,$user);
      /* SEARCH TASK BY STATE */
      }elseif(isset($_GET["state"])){ 
        $state = $_GET["state"];
        $res = $_task->taskByState($postBody,$state);
      /* SHOW BY DEFAULT */
      }else{
        $res = $_task->listTask($postBody);
        print_r($res);
      }
      echo utf8_encode(json_encode($res));
      
      http_response_code(200);
      break;

    case "POST": /* SAVE TASK */
      $postBody = file_get_contents("php://input");
      $res = $_task->postTask($postBody);

      /* set Response data */
      header('content-Type: application/json');
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res);
      break;

    case "PUT": /* MODIFY TASK */
      $postBody = file_get_contents("php://input");
      $res = $_task->putTask($postBody);
      /* set Response data */
      header('content-Type: application/json');
      if(isset($res["result"]["error_id"])){
        $responseCode = $res["result"]["error_id"];
        http_response_code($responseCode);
      }else{
        http_response_code(200);
      }
      echo json_encode($res);
      break;

    case "DELETE": /* DELETE TASK */
      $postBody = file_get_contents("php://input");
      $res = $_task->deleteTask($postBody);
      /* set Response data */
      header('content-Type: application/json');
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