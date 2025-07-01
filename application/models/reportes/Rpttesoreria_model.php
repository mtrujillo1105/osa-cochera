<?php
/* *********************************************************************************
Dev: Martín Trujillo
/* ******************************************************************************** */
class Rpttesoreria_Model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->compania = $this->session->userdata('compania');
    }
    
    public function getMovimientos($filter = NULL) {

         $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
         $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir, cm.CAJAMOV_FechaRegistro DESC " : "";

         $where = '';
         if (isset($filter->caja) && $filter->caja != '')
             $where .= " AND c.CAJA_Codigo = $filter->caja";

         if (isset($filter->nombre) && $filter->nombre != '')
             $where .= " AND CONCAT_WS(' ', c.CAJA_Nombre, c.CAJA_CodigoUsuario) LIKE '%$filter->nombre%'";

         if (isset($filter->fpago) && $filter->fpago != '')
             $where .= " AND fp.FORPAP_Codigo = $filter->fpago";

         if (isset($filter->fechai) && $filter->fechai != ''){
             $fechaf = (isset($filter->fechaf) && $filter->fechaf != '') ? $filter->fechaf : date("Y-m-d");
             $where .= " AND cm.CAJAMOV_FechaRegistro BETWEEN '$filter->fechai 00:00:00' AND '$fechaf 23:59:59'";
         }

         $rec = "
                SELECT  c.CAJA_CodigoUsuario, 
                         c.CAJA_Nombre, 
                         cm.*,
                         cp.CPC_Serie,cp.CPC_Numero,
                         CASE cm.CAJAMOV_MovDinero
                             WHEN 1 THEN 'INGRESO'
                             WHEN 2 THEN 'EGRESO'
                             ELSE ''
                         END as movimiento, 
                         fp.FORPAC_Descripcion
                FROM cji_cajamovimiento cm
                INNER JOIN cji_caja c ON c.CAJA_Codigo = cm.CAJA_Codigo
                LEFT JOIN cji_formapago fp ON fp.FORPAP_Codigo = cm.FORPAP_Codigo
                left join cji_comprobante cp on cp.CPP_Codigo = cm.CPP_Codigo
                WHERE c.CAJA_FlagEstado LIKE '1' 
                AND cm.CAJAMOV_FlagEstado LIKE '1' 
                AND c.COMPP_Codigo = $this->compania 
                $where $order $limit
            ";
         
         $recF = "
                SELECT count(*) as registros
                FROM cji_cajamovimiento cm
                INNER JOIN cji_caja c ON c.CAJA_Codigo = cm.CAJA_Codigo
                LEFT JOIN cji_formapago fp ON fp.FORPAP_Codigo = cm.FORPAP_Codigo
                WHERE c.CAJA_FlagEstado LIKE '1' 
                AND cm.CAJAMOV_FlagEstado LIKE '1' 
                AND c.COMPP_Codigo = $this->compania 
                $where 
            ";

         $recT = "
                SELECT count(*) as registros
                FROM cji_cajamovimiento cm
                INNER JOIN cji_caja c ON c.CAJA_Codigo = cm.CAJA_Codigo
                LEFT JOIN cji_formapago fp ON fp.FORPAP_Codigo = cm.FORPAP_Codigo
                WHERE c.CAJA_FlagEstado LIKE '1' 
                AND cm.CAJAMOV_FlagEstado LIKE '1' 
                AND c.COMPP_Codigo = $this->compania 
            ";           
         
         $records = $this->db->query($rec);
         $recordsFilter = $this->db->query($recF)->row()->registros;
         $recordsTotal = $this->db->query($recT)->row()->registros;         

         if ($records->num_rows() > 0){
            $info = array(
                        "records"       => $records->result(),
                        "recordsFilter" => $recordsFilter,
                        "recordsTotal"  => $recordsTotal
                );
         }
         else{
            $info = array(
                        "records"       => NULL,
                        "recordsFilter" => 0,
                        "recordsTotal"  => $recordsTotal
                );
         }
 
         return $info;         

     }

    
}

?>