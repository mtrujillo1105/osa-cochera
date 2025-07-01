<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Categoriapublicacion_Model extends CI_Model{

	##  -> Begin
	public function  __construct(){
		parent::__construct();
	}
	##  -> End

	public function seleccionar()
	{
		$arreglo = array(''=>':: Seleccione ::');
		$lista = $this->listar();
		if(count($lista)>0){
			foreach($lista as $indice=>$valor)
			{
				$indice1   = $valor->CATPUBP_Codigo;
				$valor1    = $valor->CATPUBC_Descripcion;
				$arreglo[$indice1] = $valor1;
			}
		}
		return $arreglo;
	}
	public function listar()
	{
		$where = array('CATPUBC_FlagEstado'=>'1');
		$query = $this->db->where($where)->order_by('CATPUBC_Orden')->get('cji_categoriapublicacion');
		if($query->num_rows() > 0){
			return $query->result();
		}
		else
			return array();
	}

}
?>