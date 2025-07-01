<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Tipocliente_model extends CI_Model{

	private $compania;

	public function __construct(){
		parent::__construct();
	}

	public function getCategorias($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND TIPCLIC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_tipocliente WHERE TIPCLIC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_tipocliente WHERE TIPCLIC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_tipocliente WHERE TIPCLIC_FlagEstado LIKE '1'";

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
	
	public function getCategoria($codigo) {
		$sql = "SELECT tp.* FROM cji_tipocliente tp WHERE tp.TIPCLIP_Codigo = $codigo";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	
	public function insertar_categoria($filter){
		$this->db->insert("cji_tipocliente", (array) $filter);
		return $this->db->insert_id();
	}
	
	public function actualizar_categoria($categoria, $filter){
		$this->db->where('TIPCLIP_Codigo',$categoria);
		return $this->db->update('cji_tipocliente', $filter);
	}

	public function deshabilitar_categoria($categoria, $filter){
		$this->db->where('TIPCLIP_Codigo',$categoria);
		$query = $this->db->update('cji_tipocliente', $filter);

		if ($query){
			$sql = "DELETE FROM cji_productoprecio WHERE TIPCLIP_Codigo = '$categoria'";
			$this->db->query($sql);
		}
		return $query;
	}
}
?>