<?php 
/* *********************************************************************************
Dev: 
/* ******************************************************************************** */
class Tarifa_model extends CI_Model{
    private $empresa;
    private $compania;
  
    public function __construct(){
        parent::__construct();
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');    
    }
  
    public function getTarifas($filter = NULL, $onlyRecords = true) {
        $limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
        $where = " AND COMPP_Codigo = $this->compania";
        
        if (isset($filter->tipo_tarifa) && $filter->tipo_tarifa != ''){
            $tipo_tarifas = is_array($filter->tipo_tarifa) ? $filter->tipo_tarifa : array($filter->tipo_tarifa);
            $where .= " AND TARIFC_Tipo in (".implode(",",$tipo_tarifas).")";
        }
        
        if (isset($filter->descripcion) && $filter->descripcion != '')
            $where .= " AND TARIFC_Descripcion LIKE '%$filter->descripcion%'";
        
        $rec = "SELECT * FROM cji_tarifa WHERE TARIFC_Estado LIKE '1' $where $order $limit";
        
        $recF = "SELECT COUNT(*) as registros FROM cji_tarifa WHERE TARIFC_Estado LIKE '1' $where";
        $recT = "SELECT COUNT(*) as registros FROM cji_tarifa WHERE TARIFC_Estado LIKE '1'";
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

    public function getTarifasTotal($filter){
        $where = "";
        
        if (isset($filter->tipo_tarifa) && $filter->tipo_tarifa != ''){
            $tipo_tarifas = is_array($filter->tipo_tarifa) ? $filter->tipo_tarifa : array($filter->tipo_tarifa);
            $where .= " AND c.TARIFC_Tipo in (".implode(",",$tipo_tarifas).")";
        }
        
        $sql = "
                SELECT 
                c.*,ee.EESTABC_Descripcion
                FROM cji_tarifa c 
                inner join cji_compania cia on (cia.COMPP_Codigo = c.COMPP_Codigo)
                inner join cji_emprestablecimiento ee on (ee.EESTABP_Codigo = cia.EESTABP_Codigo)
                WHERE c.TARIFC_Estado LIKE '1' $where
                order by ee.EESTABC_Descripcion
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
                $info = $query->result();
        else
                $info = NULL;
        return $info;
    }
    
    public function getTarifa($codigo) {
        $sql = "SELECT * FROM cji_tarifa c WHERE c.TARIFP_Codigo = $codigo";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
                return $query->result();
        else
                return NULL;
    }

    public function registrar_tarifa($filter){
        $this->db->insert("cji_tarifa", (array) $filter);
        return $this->db->insert_id();
    }

    public function actualizar_tarifa($tarifa, $filter){
        $this->db->where('TARIFP_Codigo',$tarifa);
        return $this->db->update('cji_tarifa', $filter);
    }
  
}
?>