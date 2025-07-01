<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiatransdetalle_Model extends CI_Model{
	protected $_name = "cji_guiatransdetalle";

	##  -> Begin
	public function  __construct(){
		parent::__construct();
	}
	##  -> End

	public function listar($guiatrans_id)
	{
		$where = array("GTRANP_Codigo" => $guiatrans_id);
		$query = $this->db->where($where)->order_by('GTRANDETP_Codigo')->get('cji_guiatransdetalle');
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return NULL;
	}

	public function update($filter){
		$datos = array(
			'PROD_Codigo' => $filter->PROD_Codigo,
			'UNDMED_Codigo' => $filter->UNDMED_Codigo,
			'GTRANDETC_Cantidad' => $filter->GTRANDETC_Cantidad,
			'GTRANDETC_Costo' => $filter->GTRANDETC_Costo,
			'GTRANDETC_GenInd' => $filter->GTRANDETC_GenInd,
			'GTRANDETC_Descripcion' => $filter->GTRANDETC_Descripcion,
			'GTRANDETC_FlagEstado' => $filter->GTRANDETC_FlagEstado
		);
		$valor = $this->db->insert('cji_guiatransdetalle', $datos);
		return $valor;
	}

	public function eliminar($guiatrans){
		$this->db->where('GTRANP_Codigo', $guiatrans);
		$valor = $this->db->delete('cji_guiatransdetalle');
		return $valor;
	}

	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_guiatransdetalle", (array)$filter);
	}

}

?>