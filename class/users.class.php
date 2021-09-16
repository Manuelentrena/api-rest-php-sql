<?php
  /* Dependencies */
  require_once 'connection/connection.php';
  require_once 'response.class.php';

  class user extends connection{

    /* VARIABLES */
    private $table = "users";
    private $id = null;
    private $name = null;
    private $lastname = null;
    private $email = null;
    private $password = null;
    private $direction = "";
    private $available = true;
    private $token = "";

    /* METHODS GETS */
    public function listUsers($headers, $page = 1){
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
      $query = "SELECT id, name, email, direction, available FROM " .$this->table . " limit $initial,$end";
      $data = parent::getData($query);
      return $data;
    }

    public function oneUser($headers,$id){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };
      
      $query = "SELECT id, name, email, direction, available FROM " .$this->table . " WHERE id = $id";
      $data = parent::getData($query);
      return $data;
    }

    public function usersByName($headers,$name){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT id, name, lastname, email, direction, available FROM " .$this->table . " WHERE name LIKE '%$name%'";
      $data = parent::getData($query);
      return $data;
    }

    public function usersByLastName($headers,$lastName){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT id, name, lastname, email, direction, available FROM " .$this->table . " WHERE lastname LIKE '%$lastName%'";
      $data = parent::getData($query);
      return $data;
    }

    public function usersByEmail($headers,$email){
      $tokenValidate = $this->verifyToken($headers);

      if($tokenValidate){
        return $tokenValidate;
      };

      $query = "SELECT id, name, lastname, email, direction, available FROM " .$this->table . " WHERE email LIKE '%$email%'";
      $data = parent::getData($query);
      return $data;
    }
    /* METHODS POST */
    public function postUser($json){
      $_response = new response;
      $data = json_decode($json, true);

      /* fields required (REGISTER USER)*/
      if(!isset($data["name"]) || !isset($data["email"]) || !isset($data["password"]) ){
        return $_response->error_400();
      }

      $this->name = $data["name"];
      $this->email = $data["email"];
      $this->password = md5($data["password"]);
      if(isset($data["lastname"])) { $this->lastname = $data["lastname"]; }
      if(isset($data["direction"])) { $this->direction = $data["direction"]; }
      if(isset($data["available"])) { $this->available = $data["available"]; }

      /* Validate email not repeat */
      $emailRepeat = $this->searchUserWithEmail($this->email);

      if($emailRepeat){
        return $_response->error_200("Email ya registrado");
      };

      /* return $data; */
      $result = $this->insertUser();

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

    private function searchUserWithEmail($email){
      $query = "SELECT * FROM ". $this->table . " WHERE email ='$email'";

      $res = parent::nonQuery($query);
      if($res >= 1){
        return $res;
      }else{
        return 0;
      }
    }

    private function insertUser(){
      $query = "INSERT INTO " . $this->table . " (name,lastname,email,password,direction,available) VALUES ('" . $this->name . "','" . ($this->lastname ?? 'null') . "','" . $this->email . "','" . $this->password . "','" . $this->direction . "','" . $this->available . "')";

      $res = parent::nonQueryId($query);
      if($res){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS PUT */

    public function putUser($json,$headers){
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
      if(isset($data["name"])) { $this->name = $data["name"]; }
      if(isset($data["lastname"])) { $this->lastname = $data["lastname"]; }
      if(isset($data["direction"])) { $this->direction = $data["direction"]; }
      if(isset($data["available"])) { $this->available = $data["available"]; }

      /* return $data; */
      $result = $this->modifyUser();

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

    private function modifyUser(){
      $query = "UPDATE " . $this->table . " SET name = '" . $this->name . "' , lastname = '" . ($this->lastname ?? null) . "' , direction = '" . $this->direction . "' , available = '" . $this->available . "' WHERE id = '" . $this->id ."'";

      $res = parent::nonQuery($query);
      if($res >= 1){
        return $res;
      }else{
        return 0;
      }
    }

    /* METHODS DELETE */

    public function deleteUser($json,$headers){
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
      $result = $this->eliminateUser();

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

    private function eliminateUser(){
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
      /* $data = json_decode($json, true); */


      /* Verify TokenBody */
      /* if(!isset($data["token"])){
        return $_response->error_401();
      } */
      if(!$headers["token"]){
        return $_response->error_401();
      }

      $this->token = $headers["token"];
      $infoToken = $this->getToken();

      /* Verify TokenDB */
      if(!$infoToken){
        return $_response->error_401("Token caducado o inválido");
      }
      return 0;
    }

  }

?>