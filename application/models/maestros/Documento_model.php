<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Documento_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function getDocumentos($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->descripcion) && $filter->descripcion != '')
			$where .= " AND d.DOCUC_Descripcion = '$filter->descripcion'";

		$rec = "SELECT d.*,
									CASE DOCUC_FlagEstado
										WHEN '1' THEN 'ACTIVO'
										ELSE 'INACTIVO'
									END as estado
								FROM cji_documento d
								WHERE d.DOCUP_Codigo <> 0 $where $order $limit";

		$recF = "SELECT COUNT(*) as registros FROM cji_documento d WHERE d.DOCUP_Codigo <> 0 $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_documento WHERE DOCUP_Codigo <> 0";

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

	public function getDocumento($codigo) {
		$sql = "SELECT * FROM cji_documento WHERE DOCUP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row();
		else
			return NULL;
	}

    public function obtenerAbreviatura($Abreviatura)
    {
    	$query = $this->db->where('DOCUC_Inicial',$Abreviatura)->get('cji_documento');
    	if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
    	}
    }
    
    public function obtenerDocuInicial($DocuInicial)
    {
    	$query = $this->db->where('DOCUC_Inicial',$DocuInicial)->get('cji_documento');
    	if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
    	}
    }    

	public function insertar_documento($filter){
		$this->db->insert("cji_documento", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_documento($documento, $filter){
		$this->db->where('DOCUP_Codigo',$documento);
		return $this->db->update('cji_documento', $filter);
	}
}
?>