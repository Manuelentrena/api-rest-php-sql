<?php 

class response{

  public  $res = [
    'status' => "ok",
    "result" => array()
  ];


  public function error_405(){
    $this->res['status'] = "error";
    $this->res['result'] = array(
      "error_id" => "405",
      "error_msg" => "Metodo no permitido"
    );
    return $this->res;
  }

  public function error_200($valor = "Datos incorrectos"){
    $this->res['status'] = "error";
    $this->res['result'] = array(
      "error_id" => "200",
      "error_msg" => $valor
    );
    return $this->res;
  }

  public function error_400(){
    $this->res['status'] = "error";
    $this->res['result'] = array(
      "error_id" => "400",
      "error_msg" => "Datos enviados incompletos o con formato incorrecto"
    );
    return $this->res;
  }

  public function error_500($valor = "Error interno del servidor"){
    $this->res['status'] = "error";
    $this->res['result'] = array(
      "error_id" => "500",
      "error_msg" => $valor
    );
    return $this->res;
  }

  public function error_401($valor = "No autorizado"){
    $this->res['status'] = "error";
    $this->res['result'] = array(
      "error_id" => "401",
      "error_msg" => $valor
    );
    return $this->res;
  }
  
}

?>