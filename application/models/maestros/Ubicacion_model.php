<?php 
/* *********************************************************************************
Dev: 
/* ******************************************************************************** */
class Ubicacion_model extends CI_Model{
    private $empresa;
    private $compania;
  
    public function __construct(){
        parent::__construct();
	$this->empresa = $this->session->userdata('empresa');
	$this->compania = $this->session->userdata('compania');    
    }

    public function getUbicaciones($filter = NULL, $onlyRecords = true) {
	$limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
	$order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
	$where = '';
	if (isset($filter->descripcion) && $filter->descripcion != '')
            $where .= " AND UBICC_Descripcion LIKE '%$filter->descripcion%'";
            $rec = "
                    SELECT * 
                    FROM cji_ubicacion 
                    WHERE UBICC_FlagEstado LIKE '1' 
                    AND COMPP_Codigo = $this->compania
                    $where $order $limit
                    ";
            $recF = "SELECT COUNT(*) as registros FROM cji_ubicacion WHERE UBICC_FlagEstado AND COMPP_Codigo = $this->compania LIKE '1' $where";
            $recT = "SELECT COUNT(*) as registros FROM cji_ubicacion WHERE UBICC_FlagEstado AND COMPP_Codigo = $this->compania LIKE '1'";
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

	public function getUbicacion($codigo) {
		$sql = "SELECT * FROM cji_ubicacion c WHERE c.UBICP_Codigo = $codigo";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function registrar_ubicacion($filter){
		$this->db->insert("cji_ubicacion", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_ubicacion($ubicacion, $filter){
		$this->db->where('UBICP_Codigo',$ubicacion);
		return $this->db->update('cji_ubicacion', $filter);
	}

	public function incrementar_ubicacion($ubicacion, $filter){
		$this->db->set('UBICC_EspaciosUsados', 'UBICC_EspaciosUsados+1', FALSE);
		$this->db->where('UBICP_Codigo',$ubicacion);
		return $this->db->update('cji_ubicacion', $filter);
	}

	public function disminuir_ubicacion($ubicacion, $filter){
		$this->db->set('UBICC_EspaciosUsados', 'UBICC_EspaciosUsados-1', FALSE);
		$this->db->where('UBICP_Codigo',$ubicacion);
		return $this->db->update('cji_ubicacion', $filter);
	}	
}
?>