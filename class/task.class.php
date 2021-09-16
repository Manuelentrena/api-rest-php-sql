<?php
  require_once 'connection/connection.php';
  require_once 'response.class.php';

  class task extends connection {

    private $table = "tasks";
    private $id = null;
    private $name = null;
    private $idstate = null;
    private $iduser = null;
    private $date = "0000-00-00";
    private $description = null;
    private $token = "";

    /* METHODS GET */

    public function listTask($headers, $page = 1){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $initial = 0;
      $end = 100;
      if($page > 1){
        $initial = ($end * ($page - 1)) + 1;
        $end = $end * $page;
      }

      $query = "SELECT t.id, t.name as name, t.description, concat_ws(' ',u.name,u.lastname) as user, u.id as userid, s.id as stateid, s.name as state, t.date FROM users u, tasks t, states s WHERE u.id = t.iduser AND s.id = t.idstate limit $initial,$end";
      $data = parent::getData($query);
      return $data;
    }

    public function oneTask($headers, $id){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT t.id, t.name as name, t.description, concat(u.name,' ',u.lastname) as user, u.id as userid, s.id as stateid, s.name as state, t.date FROM users u, tasks t, states s WHERE u.id = t.iduser AND s.id = t.idstate AND t.id = $id";
      $data = parent::getData($query);
      return $data;
    }

    public function taskByName($headers,$name){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT t.id, t.name as name, t.description, concat(u.name,' ',u.lastname) as user, u.id as userid, s.id as stateid, s.name as state, t.date FROM users u, tasks t, states s WHERE u.id = t.iduser AND s.id = t.idstate AND t.name LIKE '%$name%'";
      $data = parent::getData($query);
      return $data;
    }

    public function taskByUser($headers,$user){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT t.id, t.name as name, t.description, concat(u.name,' ',u.lastname) as user, u.id as userid, s.id as stateid, s.name as state, t.date FROM users u, tasks t, states s WHERE u.id = t.iduser AND s.id = t.idstate AND concat(u.name,' ',u.lastname) LIKE '%$user%'";
      $data = parent::getData($query);
      return $data;
    }

    public function taskByState($headers,$state){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT t.id, t.name as name, t.description, concat(u.name,' ',u.lastname) as user, u.id as userid, s.id as stateid, s.name as state, t.date FROM users u, tasks t, states s WHERE u.id = t.iduser AND s.id = t.idstate AND s.name LIKE '%$state%'";
      $data = parent::getData($query);
      return $data;
    }

    /* METHODS POST */

    public function postTask($json,$headers){
      $tokenValidate = $this->verifyToken($headers);
      
      if($tokenValidate){
        return $tokenValidate;
      };

      $_response = new response;
      $data = json_decode($json, true);
      
      /* fields required */
      if(!isset($data["name"]) || !isset($data["idstate"]) || !isset($data["iduser"])){
        return $_response->error_400();
      }

      $this->name = $data["name"];
      $this->idstate = $data["idstate"];
      $this->iduser = $data["iduser"];
      $this->date = date("Y-m-d H:i");
      if(isset($data["description"])) { $this->description = $data["description"]; }

      /* return $data; */
      $result = $this->insertTask();

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

    private function insertTask(){
      $query = "INSERT INTO " . $this->table . " (name,idstate,iduser,date,description) VALUES ('" . $this->name . "'," . ($this->idstate ?? 'null') . "," . ($this->iduser ?? 'null') . ",'" . $this->date . "','" . $this->description . "')";
      $res = parent::nonQueryId($query);
      if($res){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS PUT */

    public function putTask($json,$headers){
      $tokenValidate = $this->verifyToken($headers);
      
      if($tokenValidate){
        return $tokenValidate;
      };

      $_response = new response;
      $data = json_decode($json, true);

      /* fields required */
      if(!isset($data["id"]) || !isset($data["name"]) || !isset($data["idstate"]) || !isset($data["iduser"])){
        return $_response->error_400();
      }

      $this->id = $data["id"];
      $this->name = $data["name"];
      $this->idstate = $data["idstate"];
      $this->iduser = $data["iduser"];
      $this->date = date("Y-m-d H:i");
      if(isset($data["description"])) { $this->description = $data["description"]; }

      /* return $data; */
      $result = $this->modifyTask();

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

    private function modifyTask(){
      $query = "UPDATE " . $this->table . " SET name = '" . $this->name . "' , idstate = " . $this->idstate . " ,iduser = " . $this->iduser . " ,date = '" . $this->date . "' ,description = '" . $this->description . "' WHERE id = '" . $this->id ."'";

      $res = parent::nonQuery($query);
      if($res >= 1){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS DELETE */

    public function deleteTask($headers){
      $tokenValidate = $this->verifyToken($headers);
      
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
      $result = $this->eliminateTask();

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

    private function eliminateTask(){
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

    private function verifyToken($headers){
      
      $_response = new response;
      print_r($headers);
      print_r("hola");
      if(!isset($headers["Token"])){
        return $_response->error_401();
      }

      $this->token = $headers["Token"];
      $infoToken = $this->getToken();

      /* Verify TokenDB */
      if(!$infoToken){
        return $_response->error_401("Token caducado o inválido");
      }
      return 0;
    }

  }
?>