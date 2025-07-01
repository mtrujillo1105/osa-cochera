<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Unidadmedida_model extends CI_Model{

	##  -> Begin
	public function __construct(){
		parent::__construct();
	}
	##  -> End

	##  -> Begin
	public function getUmedidas($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND UNDMED_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_unidadmedida WHERE UNDMED_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_unidadmedida WHERE UNDMED_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_unidadmedida WHERE UNDMED_FlagEstado LIKE '1'";

		$records = $this->db->query($rec);

		if ($onlyRecords == false){
			$recordsFilter = $this->db->query($recF)->row()->registros;
			$recordsTotal = $this->db->query($recT)->row()->registros;
		}

		if ($records->num_rows() > 0){
			if ($onlyRecords == false){
				$info = array(
											"records" => $records->result(),
											"recordsFilter" => $recordsFilter,
											"recordsTotal" => $recordsTotal
										);
			}
			else{
				$info = $records->result();
			}
		}
		else{
			if ($onlyRecords == false){
				$info = array(
											"records" => NULL,
											"recordsFilter" => 0,
											"recordsTotal" => $recordsTotal
										);
			}
			else{
				$info = $records->result();
			}
		}
		return $info;
	}
	##  -> End

	##  -> Begin
	public function getUmedida($codigo) {

		$sql = "SELECT um.* FROM cji_unidadmedida um WHERE um.UNDMED_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function insertar_unidad($filter){
		$this->db->insert("cji_unidadmedida", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_unidad($um, $filter){
		$this->db->where('UNDMED_Codigo',$um);
		return $this->db->update('cji_unidadmedida', $filter);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_unidad($um, $filter){
		$this->db->where('UNDMED_Codigo',$um);
		$query = $this->db->update('cji_unidadmedida', $filter);
		return $query;
	}
	##  -> End

  ## FUNCTIONS OLDS

	public function obtener($unidad){
		$query = $this->db->where("UNDMED_Codigo",$unidad)->get("cji_unidadmedida");
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function getById($id){
		$data = $this->db->where("UNDMED_Codigo",$id)->get("cji_unidadmedida")->result();
		return $data[0];
	}
	
}
?>