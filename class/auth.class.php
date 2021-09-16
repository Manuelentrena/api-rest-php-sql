<?php
  /* DEPENDENCIAS */
  require_once 'connection/connection.php';
  require_once 'response.class.php';

  class auth extends connection{

    public function login($json){
      $_response = new response;
      $body = json_decode($json,true);

      /* BODY INCORRECT */
      if(!isset($body['username']) || !isset($body['password'])){
        return $_response->error_400();
      }

      $email = $body['username']; // trae el email del user
      $password = parent::encript($body['password']);
      $dataUser = $this->getDataUser($email);

      /* QUERY EMPTY */
      if(!$dataUser){
        return $_response->error_200("El usuario $email no existe");
      }

      /* PASSWORD INCORRECT */
      if($password != $dataUser[0]['password']){
        return $_response->error_200("El password es invalido");
      }

      /* USER NO ACTIVE */
      if($dataUser[0]['available'] != true){
        return $_response->error_200("Cuenta Desactivada");
      }

      /* CREATE TOKEN */
      $newToken= $this->getToken($dataUser[0]['id']);

      /* VERIFY TOKEN */
      if(!$newToken){
        return $_response->error_500();
      }

      $res = $_response->res;
      $res["result"] = array(
        "token" => $newToken,
        "id" => $dataUser[0]['id']
      );

      return $res;
    }

    private function getDataUser($email){
      $query = "SELECT id,name,lastname,email,password,direction,available FROM users WHERE email='$email'";
      $data = parent::getData($query);
      if(isset($data[0]["id"])){
        return $data;
      }else{
        return 0;
      }
    }

    private function getToken($userId){
      $val = true;
      $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
      $date = date("Y-m-d H:i");
      $state = true;
      $query = "INSERT INTO userstoken (userid,token,state,date) VALUES('$userId','$token','$state','$date')";
      $verify = parent::nonQuery($query);

      if($verify){
        return $token;
      }else{
        return 0;
      }
    }

  }

?>