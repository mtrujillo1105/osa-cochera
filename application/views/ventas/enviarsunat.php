<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
 
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");

//$products = json_decode($data_json, true);
print_r($prueba);
//var_dump($prueba);
// foreach ($products as $product) {
 // echo $prueba."--".$respuesta;
   
// }

?>
