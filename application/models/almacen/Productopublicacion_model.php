<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Productopublicacion_Model extends CI_Model{
	protected $_name = "cji_productopublicacion";
	##  -> Begin
	private $compania;
  ##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}
  ##  -> End

	public function listar($producto){
		$where = array("PROD_Codigo"=>$producto, 'COMPP_Codigo'=>$this->compania, 'PRODPUBC_FlagEstado'=>'1');
		$query = $this->db->where($where)->get('cji_productopublicacion');
		if($query->num_rows() > 0){
			return $query->result();
		}
		else
			return array();
	}

	public function despublicar_producto($cod){
		$where = array("PROD_Codigo"=>$cod);
		$this->db->delete('cji_productopublicacion',$where);
	}

	public function insertar(stdClass $filter = null){
		$this->db->insert("cji_productopublicacion",(array)$filter);
	}
}
?>