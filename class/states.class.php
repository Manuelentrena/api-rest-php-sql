<?php
  /* Dependencies */
  require_once 'connection/connection.php';
  require_once 'response.class.php';

  class state extends connection{

    /* VARIABLES */
    private $table = "states";
    private $id = null;
    private $name = null;
    private $description = null;
    private $token = "";


    /* METHODS GETS */
    public function listStates($json, $page = 1){
      $tokenValidate = $this->verifyToken($json);

      if($tokenValidate){
        return $tokenValidate;
      };

      $initial = 0;
      $end = 100;
      if($page > 1){
        $initial = ($end * ($page - 1)) + 1;
        $end = $end * $page;
      }
      $query = "SELECT * FROM " .$this->table . " limit $initial,$end";
      $data = parent::getData($query);
      
      return $data;
    }

    public function oneState($json,$id){
      $tokenValidate = $this->verifyToken($json);

      if($tokenValidate){
        return $tokenValidate;
      };
      
      $query = "SELECT * FROM " .$this->table . " WHERE id = $id";
      $data = parent::getData($query);
      return $data;
    }

    /* METHODS POST */
    public function postState($json){
      $tokenValidate = $this->verifyToken($json);
      
      if($tokenValidate){
        return $tokenValidate;
      };

      $_response = new response;
      $data = json_decode($json, true);

      /* fields required */
      if(!isset($data["name"]) ){
        return $_response->error_400();
      }

      $this->name = $data["name"];
      if(isset($data["description"])) { $this->description = $data["description"]; }

      /* return $data; */
      $result = $this->insertState();

      /* IF NOT QUERY SUCCCES */
      if(!$result){
        return $_response->error_500();
      }

      $res = $_response->res;
      $res["result"] = array(
        "id" => $result
      );
      return $res;
    }

    private function insertState(){
      $query = "INSERT INTO " . $this->table . " (name,description) VALUES ('" . $this->name . "','" . $this->description . "')";

      $res = parent::nonQueryId($query);
      if($res){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS PUT */

    public function putState($json){
      $tokenValidate = $this->verifyToken($json);
      
      if($tokenValidate){
        return $tokenValidate;
      };

      $_response = new response;
      $data = json_decode($json, true);

      /* id required */
      if(!isset($data["id"])){
        return $_response->error_400();
      }

      $this->id = $data["id"];
      if(isset($data["name"])) { $this->name = $data["name"]; }
      if(isset($data["description"])) { $this->description = $data["description"]; }

      /* return $data; */
      $result = $this->modifyState();

      /* IF NOT QUERY SUCCCES */
      if(!$result){
        return $_response->error_500();
      }

      $res = $_response->res;
      $res["result"] = array(
        "id" => $this->id
      );
      return $res;
    }

    private function modifyState(){
      $query = "UPDATE " . $this->table . " SET name = '" . $this->name . "' , description = '" . $this->description . "' WHERE id = '" . $this->id ."'";

      $res = parent::nonQuery($query);
      if($res >= 1){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS DELETE */

    public function deleteState($json){
      $tokenValidate = $this->verifyToken($json);
      
      if($tokenValidate){
        return $tokenValidate;
      };

      $_response = new response;
      $data = json_decode($json, true);

      /* id required */
      if(!isset($data["id"])){
        return $_response->error_400();
      }

      $this->id = $data["id"];

      /* return $data; */
      $result = $this->eliminateState();

      /* IF NOT QUERY SUCCCES */
      if(!$result){
        return $_response->error_500();
      }

      $res = $_response->res;
      $res["result"] = array(
        "id" => $this->id
      );
      return $res;
    }

    private function eliminateState(){
      $query = "DELETE FROM " . $this->table . " WHERE id = " . $this->id;

      $res = parent::nonQuery($query);
      if($res >= 1){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS SECURITY */
    private function getToken(){
      $query = "SELECT id,userid,token,state,date FROM userstoken WHERE token = '" . $this->token . "' AND state = true";

      $res = parent::getData($query);
      if($res){
        return $res;
      }else{
        return 0;
      }
    }

    private function verifyToken($json){
      $_response = new response;
      $data = json_decode($json, true);

      /* Verify TokenBody */
      if(!isset($data["token"])){
        return $_response->error_401();
      }

      $this->token = $data["token"];
      $infoToken = $this->getToken();

      /* Verify TokenDB */
      if(!$infoToken){
        return $_response->error_401("Token caducado o inválido");
      }
      return 0;
    }
  }

?>