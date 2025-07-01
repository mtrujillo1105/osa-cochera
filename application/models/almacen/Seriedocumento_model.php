<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Seriedocumento_model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function buscar(stdClass $filter = null){

		if($filter!=null){

			$where=array();
			if(isset($filter->PROD_Codigo)  && $filter->PROD_Codigo!=null  && trim($filter->PROD_Codigo)!= "" && $filter->PROD_Codigo!=0)
				$where["cji_serie.PROD_Codigo"] =$filter->PROD_Codigo;

			if(isset($filter->DOCUP_Codigo) && $filter->DOCUP_Codigo!=null  && trim($filter->DOCUP_Codigo)!= "" && $filter->DOCUP_Codigo!=0)
				$where["cji_seriedocumento.DOCUP_Codigo"] =$filter->DOCUP_Codigo;

			if(isset($filter->SERDOC_NumeroRef) && $filter->SERDOC_NumeroRef!=null   && trim($filter->SERDOC_NumeroRef)!= "" && $filter->SERDOC_NumeroRef!=0)
				$where["cji_seriedocumento.SERDOC_NumeroRef"] =$filter->SERDOC_NumeroRef;

			if(isset($filter->ALMAP_Codigo) && $filter->ALMAP_Codigo!=null && trim($filter->ALMAP_Codigo)!="")
				$this->db->where('cji_serie.ALMAP_Codigo', $filter->ALMAP_Codigo);

			if(isset($filter->SERIC_FlagEstado) && $filter->SERIC_FlagEstado!=null && trim($filter->SERIC_FlagEstado)!="")
				$this->db->where('cji_serie.SERIC_FlagEstado', $filter->SERIC_FlagEstado);

			$query = $this->db->join('cji_serie', 'cji_serie.SERIP_Codigo=cji_seriedocumento.SERIP_Codigo', 'RIGHT')->where($where)->get('cji_seriedocumento');
			if($query->num_rows() > 0)
				return $query->result();
			else
				return array();

		}else{
			return array();
		} 
	}


	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_seriedocumento",(array)$filter);
		return $this->db->insert_id();
	}

	public function eliminar($id)
	{
		$this->db->delete('cji_seriedocumento',array("SERDOC_Codigo"=>$id));
	}
	

	public function modificar($id, $filter) {
		$this->db->where("SERDOC_Codigo", $id);
		$this->db->update("cji_seriedocumento", (array) $filter);
	}

	public function obtener($serie){
		$where=array('SERDOC_Codigo'=>$serie);
		$query = $this->db->where($where)->get('cji_seriedocumento');
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}


	public function eliminarDocumento($idDocumento,$tipoDocumento)
	{
		$this->db->delete('cji_seriedocumento',array("DOCUP_Codigo"=>$tipoDocumento,"SERDOC_NumeroRef"=>$idDocumento,"SERDOC_FlagEstado"=>"0"));
	}

	/**gcbq eliminar segunn estado 0 y tipo documeto y codigo producto**/
	public function eliminarEstadoDocumentoProducto($tipoDocumento,$codigoDocumento,$producto){
		$sql="DELETE SD , S FROM cji_seriedocumento SD
		INNER JOIN cji_serie S ON S.SERIP_Codigo = SD.SERIP_Codigo
		WHERE SD.DOCUP_Codigo=$tipoDocumento
		AND SD.SERDOC_NumeroRef=$codigoDocumento
		AND S.PROD_Codigo=$producto";
		$this->db->query($sql);

	}
	/**gcbq eliminar segunn estado 0 y tipo documeto y codigo **/
	public function eliminarEstadoDocumentoSerie($tipoDocumento,$codigoDocumento){
		$sql="DELETE SD , S FROM cji_seriedocumento SD
		INNER JOIN cji_serie S ON S.SERIP_Codigo = SD.SERIP_Codigo
		WHERE SD.DOCUP_Codigo=$tipoDocumento
		AND SD.SERDOC_NumeroRef=$codigoDocumento
		AND SD.SERDOC_FlagEstado=0 ";
		$this->db->query($sql);
	}

	/**eliminamos las series asociadas a un documento ***/
	public function  eliminarDocumetoCodigoAsociado($tipoDocumento,$codigoDocumento){
		$sql="DELETE SD FROM cji_seriedocumento SD
		WHERE SD.DOCUP_Codigo=$tipoDocumento
		AND SD.SERDOC_NumeroRef=$codigoDocumento";
		$this->db->query($sql);
	}


}
?>