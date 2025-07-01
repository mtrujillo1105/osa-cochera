<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function seleccionar_moneda($array_rol,$indSel=''){
    $this->load->model('compras_model');
    $array_rol = $this->compras_model->listar();
    $arreglo = array();
    foreach($array_rol as $indice=>$valor){
        $indice1   = $valor->MONED_Codigo;
        $valor1    = $valor->MONED_Descripcion;
        $arreglo[$indice1] = $valor1;
    }
    $resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
    return $resultado;
}
?>
