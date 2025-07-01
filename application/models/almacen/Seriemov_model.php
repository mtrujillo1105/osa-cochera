<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Seriemov_Model extends CI_Model{
	protected $_name = "cji_seriemov";
  ##  -> Begin
	private $compania;
	##  -> End
	public function  __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}
	public function listar($serie){
		$where=array('SERIP_Codigo'=>$serie);
		$query = $this->db->where($where)
		->get('cji_seriemov');
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}


	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_seriemov",(array)$filter);
		return $this->db->insert_id();
	}

	public function eliminar($id)
	{
		$this->db->delete('cji_seriemov',array("SERMOVP_Codigo"=>$id));
	}
	
		//bUSCAR serie x guiainp-------------------------
	public function buscar_x_guiainp($guiainp,$producto){ 

		$sql="SELECT  SERIC_Numero,cji_serie.SERIP_Codigo
		FROM cji_seriemov
		LEFT JOIN  cji_serie ON  cji_serie.SERIP_Codigo = cji_seriemov.SERIP_Codigo
		WHERE PROD_Codigo = ".$producto."  AND  GUIAINP_Codigo='".$guiainp."' AND 1";

		$query = $this->db->query($sql);        

		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
		
	}
	//bUSCAR serie x guiaSAP-------------------------
	public function buscar_x_guiasap($guiasap,$producto){
		$sql="SELECT  SERIC_Numero,cji_serie.SERIP_Codigo
		FROM cji_seriemov
		LEFT JOIN  cji_serie ON  cji_serie.SERIP_Codigo = cji_seriemov.SERIP_Codigo
		WHERE PROD_Codigo= ".$producto."  AND  GUIASAP_Codigo='".$guiasap."' AND 1";

		$query = $this->db->query($sql);        

		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}
	//---------------
	public function obtener($serie){
		$where=array('SERIP_Codigo'=>$serie,'GUIASAP_Codigo'=>Null);
		$query = $this->db->where($where)
		->get('cji_seriemov');
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

}
?>