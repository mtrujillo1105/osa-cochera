<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Familia_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getFamilias($filter = NULL, $onlyRecords = true)
	{

		$limit = (isset($filter->start) && isset($filter->length)) ? " LIMIT $filter->start, $filter->length " : "";
		$order = (isset($filter->order) && isset($filter->dir)) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->flagBS) && $filter->flagBS != '')
			$where .= " AND f.FAMI_FlagBienServicio = '$filter->flagBS'";

		if (isset($filter->codigo) && $filter->codigo != '')
			$where .= " AND f.FAMI_CodigoUsuario LIKE '%$filter->codigo%'";

		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND f.FAMI_Descripcion LIKE '%$filter->descripcion%'";

		$rec = "SELECT f.*, CASE f.FAMI_FlagBienServicio WHEN 'B' THEN 'PRODUCTO' WHEN 'S' THEN 'SERVICIO' ELSE '' END as flagBS
							FROM cji_familia f WHERE f.FAMI_FlagEstado LIKE '1' $where $order $limit ";
		$recF = "SELECT COUNT(*) as registros FROM cji_familia f WHERE f.FAMI_FlagEstado LIKE '1' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_familia WHERE FAMI_FlagEstado LIKE '1'";

		$records = $this->db->query($rec);

		if ($onlyRecords == false) {
			$recordsFilter = $this->db->query($recF)->row()->registros;
			$recordsTotal = $this->db->query($recT)->row()->registros;
		}

		if ($records->num_rows() > 0) {
			if ($onlyRecords == false) {
				$info = array(
					"records" => $records->result(),
					"recordsFilter" => $recordsFilter,
					"recordsTotal" => $recordsTotal
				);
			} else {
				$info = $records->result();
			}
		} else {
			if ($onlyRecords == false) {
				$info = array(
					"records" => NULL,
					"recordsFilter" => 0,
					"recordsTotal" => $recordsTotal
				);
			} else {
				$info = $records->result();
			}
		}

		return $info;
	}

	public function getFamilia($codigo)
	{
		$sql = "SELECT f.*, CASE f.FAMI_FlagBienServicio WHEN 'B' THEN 'PRODUCTO' WHEN 'S' THEN 'SERVICIO' ELSE '' END as flagBS
						FROM cji_familia f WHERE f.FAMI_Codigo = '$codigo'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->row();
		else
			return NULL;
	}

	public function insertar($filter)
	{
		$this->db->insert("cji_familia", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar($familia, $filter)
	{
		$this->db->where('FAMI_Codigo', $familia);
		return $this->db->update('cji_familia', $filter);
	}

	public function cantidad_productos($familia)
	{
		$sql = "SELECT count(*) as cantidad FROM cji_producto WHERE FAMI_Codigo = '$familia' AND PROD_FlagEstado LIKE '1'";
		$query = $this->db->query($sql);
		return $query->row()->cantidad;
	}
        
	function obtener_familia($familia){
            $this->db->select("*");
            $this->db->where('FAMI_Codigo',$familia);
            $query = $this->db->get('cji_familia');
            $data  = array();
            if($query->num_rows()>0){
                foreach($query->result() as $fila){
                    $data[] = $fila;
                }	
            }
            return $data;
	}        
}
