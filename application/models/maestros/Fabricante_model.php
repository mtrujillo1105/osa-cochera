<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Fabricante_Model extends CI_Model{

	public function  __construct(){
		parent::__construct();
	}

	public function getFabricantes($filter = NULL, $onlyRecords = true) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND FABRIC_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT * FROM cji_fabricante WHERE FABRIC_FlagEstado LIKE '1' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_fabricante WHERE FABRIC_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_fabricante WHERE FABRIC_FlagEstado LIKE '1'";

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

	public function getFabricante($codigo) {

		$sql = "SELECT f.* FROM cji_fabricante f WHERE f.FABRIP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function insertar_fabricante($filter){
		$this->db->insert("cji_fabricante", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_fabricante($fabricante, $filter){
		$this->db->where('FABRIP_Codigo',$fabricante);
		return $this->db->update('cji_fabricante', $filter);
	}

	public function deshabilitar_fabricante($fabricante, $filter){
		$this->db->where('FABRIP_Codigo',$fabricante);
		$query = $this->db->update('cji_fabricante', $filter);
		return $query;
	}

	public function existsCode($codigo) {
		$sql = "SELECT FABRIC_CodigoUsuario FROM cji_fabricante WHERE FABRIC_CodigoUsuario LIKE '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->ALMAC_CodigoUsuario;
		else
			return NULL;
	}
        
     public function obtener($id)
     {
        $where = array("FABRIP_Codigo"=>$id);
        $query = $this->db->order_by('FABRIC_Descripcion')->where($where)->get('cji_fabricante',1);
        if($query->num_rows()>0){
          return $query->result();
        }
        else return array();
     }        
}
?>