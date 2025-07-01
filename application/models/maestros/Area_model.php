<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Area_Model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function getAreas($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND AREAC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_area WHERE AREAC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_area WHERE AREAC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_area WHERE AREAC_FlagEstado LIKE '1'";

		$records = $this->db->query($rec);
		$recordsFilter = $this->db->query($recF)->row()->registros;
		$recordsTotal = $this->db->query($recT)->row()->registros;

		if ($records->num_rows() > 0){
			$info = array(
										"records" => $records->result(),
										"recordsFilter" => $recordsFilter,
										"recordsTotal" => $recordsTotal
									);
		}
		else{
			$info = array(
										"records" => NULL,
										"recordsFilter" => 0,
										"recordsTotal" => $recordsTotal
									);
		}
		return $info;
	}

	public function getArea($codigo) {
		$sql = "SELECT l.* FROM cji_area l WHERE l.AREAP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function insertar_area($filter){
		$this->db->insert("cji_area", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_area($area, $filter){
		$this->db->where('AREAP_Codigo',$area);
		return $this->db->update('cji_area', $filter);
	}

	public function deshabilitar_area($area, $filter){
		$this->db->where('AREAP_Codigo',$area);
		$query = $this->db->update('cji_area', $filter);
		return $query;
	}
}
?>