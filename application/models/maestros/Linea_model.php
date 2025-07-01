<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Linea_Model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function getLineas($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND LINC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_linea WHERE LINC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_linea WHERE LINC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_linea WHERE LINC_FlagEstado LIKE '1'";

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

	public function getLinea($codigo) {
		$sql = "SELECT l.* FROM cji_linea l WHERE l.LINP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function insertar_linea($filter){
		$this->db->insert("cji_linea", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_linea($linea, $filter){
		$this->db->where('LINP_Codigo',$linea);
		return $this->db->update('cji_linea', $filter);
	}

	public function deshabilitar_linea($linea, $filter){
		$this->db->where('LINP_Codigo',$linea);
		$query = $this->db->update('cji_linea', $filter);
		return $query;
	}

	public function existsCode($codigo) {
		$sql = "SELECT LINC_CodigoUsuario FROM cji_linea WHERE LINC_CodigoUsuario LIKE '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->ALMAC_CodigoUsuario;
		else
			return NULL;
	}
}
?>