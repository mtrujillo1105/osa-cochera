<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');
/* *********************************************************************************
/* ******************************************************************************** */
class Aux_db{

	protected $ci;
	
	public function __construct(){
		$this->ci =& get_instance();
	}

	public function getInCompanys($company = NULL, $enterprise = NULL){
		$this->ci->load->model("maestros/compania_model");
		$id = ($company != NULL) ? $company : $enterprise;
		$info = $this->ci->compania_model->getCompanys($id);
		$r = "";
		if ($info != NULL){
			foreach($info as $i => $v){
				$r .= ($i == 0) ? $v->COMPP_Codigo : "," . $v->COMPP_Codigo;
			}
		}
		return $r;
	}

}
