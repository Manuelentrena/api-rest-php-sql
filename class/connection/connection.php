<?php

/* {
  "conexion": {
    "server": "localhost",
    "user": "root",
    "password": "",
    "database": "tasklist",
    "port": "3306"
  }
} */

class connection {

  private $server;
  private $user;
  private $password;
  private $database;
  private $port;
  private $connection;

  function __construct(){
    $listadatos = $this->datosConexion();
    foreach ($listadatos as $key => $value) {
      $this->server = $value['server'];
      $this->user = $value['user'];
      $this->password = $value['password'];
      $this->database = $value['database'];
      $this->port = $value['port'];
    }
    $this->connection = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
    if($this->connection->connect_errno){
      echo "ERROR TO CONECT IN BD";
      die();
    }

  }

  private function datosConexion(){
    $direction = dirname(__FILE__);
    $jsondata = file_get_contents($direction . "/" . "config.json");
    return json_decode($jsondata, true);
  }

  private function convertirUTF8($array){
    array_walk_recursive($array,function(&$item,$key){
      if(!mb_detect_encoding($item,'utf-8',true)){
        $item = utf8_encode($item);
      }
    });
    return $array;
  }

  public function getData($sqlstr){
    print_r($sqlstr);
    print_r(" ");
    $results = $this->connection->query($sqlstr);
    $resultArray = array();
    foreach ($results as $key) {
      $resultArray[] = $key;
    }
    print_r($resultArray);
    return $resultArray;
  }

  public function nonQuery($sqlstr){
    $results = $this->connection->query($sqlstr);
    return $this->connection->affected_rows;
  }

  public function nonQueryId($sqlstr){
    $results = $this->connection->query($sqlstr);
    $filas = $this->connection->affected_rows;
    if($filas >= 1){
      return $this->connection->insert_id;
    }else{
      return 0;
    }
  }

  protected function encript($string){
    return md5($string);
  }

}

?>