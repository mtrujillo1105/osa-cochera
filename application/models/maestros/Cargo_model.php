<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Cargo_model extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}

	public function getCargos($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->nombre) && $filter->nombre != '')
			$where .= " AND c.CARGC_Nombre LIKE '%$filter->nombre%'";

		$rec = "SELECT * FROM cji_cargo WHERE CARGC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_cargo WHERE CARGC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_cargo WHERE CARGC_FlagEstado LIKE '1'";

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

	public function getCargo($codigo) {

		$sql = "SELECT * FROM cji_cargo c WHERE c.CARGP_Codigo = $codigo";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function insertar($filter){
		$this->db->insert("cji_cargo", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar($alergia, $filter){
		$this->db->where('CARGP_Codigo',$alergia);
		return $this->db->update('cji_cargo', $filter);
	}

}
?>