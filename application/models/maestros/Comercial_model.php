<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Comercial_Model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function getComercials($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND SECCOMC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_sectorcomercial WHERE SECCOMC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_sectorcomercial WHERE SECCOMC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_sectorcomercial WHERE SECCOMC_FlagEstado LIKE '1'";

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

	public function getComercial($codigo) {

		$sql = "SELECT * FROM cji_sectorcomercial WHERE SECCOMP_Codigo = $codigo";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function insertar_comercial($filter){
		$this->db->insert("cji_sectorcomercial", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_comercial($comercial, $filter){
		$this->db->where('SECCOMP_Codigo',$comercial);
		return $this->db->update('cji_sectorcomercial', $filter);
	}

	public function deshabilitar_comercial($comercial, $filter){
		$this->db->where('SECCOMP_Codigo',$comercial);
		$query = $this->db->update('cji_sectorcomercial', $filter);
		return $query;
	}
}
?>