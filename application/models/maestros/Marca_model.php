<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Marca_Model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function getMarcas($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND MARCC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_marca m WHERE MARCC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_marca WHERE MARCC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_marca WHERE MARCC_FlagEstado LIKE '1'";

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

	public function getMarca($codigo) {

		$sql = "SELECT m.* FROM cji_marca m WHERE m.MARCP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function getLastId(){
		$sql = "SELECT MAX(MARCP_Codigo) ID FROM cji_marca";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->ID;
		else
			return 0;
	}

	public function insertar_marca($filter){
		$this->db->insert("cji_marca", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_marca($marca, $filter){
		$this->db->where('MARCP_Codigo',$marca);
		return $this->db->update('cji_marca', $filter);
	}

	public function deshabilitar_marca($marca, $filter){
		$this->db->where('MARCP_Codigo',$marca);
		$query = $this->db->update('cji_marca', $filter);
		return $query;
	}
}
?>