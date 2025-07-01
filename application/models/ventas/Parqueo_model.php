<?php 
/* *********************************************************************************
/* ******************************************************************************** */
class Parqueo_model extends CI_Model{
    private $empresa;
    private $compania;
    public function __construct(){
        parent::__construct();
	$this->empresa = $this->session->userdata('empresa');
	$this->compania = $this->session->userdata('compania');    
    }

    public function getParqueos($filter = NULL, $onlyRecords = true) {
        $limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
	if(isset($filter->ordenar)){
            $order = "ORDER BY ";
            foreach($filter->ordenar as $value){
		$order.=$value[0]." ".$value[1].",";
            }
            $order = substr($order,0,strlen($order)-1);
        }
        else{
            $order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";	
        }
        $where = '';
        
        if (isset($filter->situacion) && $filter->situacion != '')  $where .= " AND p.PARQC_FlagSituacion = '$filter->situacion'";	
        if (isset($filter->estacionamiento) && $filter->estacionamiento != '')  $where .= " AND p.PARQC_FlagEstacionamiento = '$filter->estacionamiento'";
        if (isset($filter->placa) && $filter->placa != '')          $where .= " AND p.PARQC_Placa like '%".$filter->placa."'";	
        if (isset($filter->tarifa) && $filter->tarifa != '')        $where .= " AND p.TARIFP_Codigo = '$filter->tarifa'";	
        if (isset($filter->tarifa_not) && $filter->tarifa_not != ''){
            $tarifa_not = is_array($filter->tarifa_not)?$filter->tarifa_not:array($filter->tarifa_not);
            $where .= " AND t.TARIFP_Codigo NOT IN (".implode(",",$tarifa_not).")";
        }
        if (isset($filter->servicio) && $filter->servicio != '')    $where .= " AND p.PROD_Codigo = '$filter->servicio'";	
        if (isset($filter->serie) && $filter->serie != '')          $where .= " AND c.CPC_Serie = '$filter->serie'";	
        if (isset($filter->numero) && $filter->numero != '')        $where .= " AND c.CPC_Numero = '$filter->numero'";	
        if (isset($filter->ticket) && $filter->ticket != '')        $where .= " AND p.PARQC_Numero = '$filter->ticket'";	//Agrego ultimo
        
        if (isset($filter->tipo_tarifa_not) && $filter->tipo_tarifa_not != '') $where .= " AND t.TARIFC_Tipo != '$filter->tipo_tarifa_not'";	
        if (isset($filter->tipo_tarifa) && $filter->tipo_tarifa != ""){
            $tipo_tarifas = is_array($filter->tipo_tarifa)?$filter->tipo_tarifa:array($filter->tipo_tarifa);
            $where .= " AND t.TARIFC_Tipo IN (".implode(",",$tipo_tarifas).")";
        }
        
	$rec = "SELECT  p.*,
                        DATE_FORMAT(p.PARQC_FechaIngreso,'%d/%m/%Y') PARQC_FechaIn,
                        p.PARQC_HoraIngreso PARQC_HoraIn,
                        t.*,
                        c.CPC_Numero,
                        c.CPC_Serie,
                        c.CPC_TipoDocumento,
                        concat(c.CPC_Serie,'-',c.CPC_Numero) as CPC_SerieNumero,
                        c.CPC_FlagEstado,
                        cj.CAJA_Nombre,cj.CAJA_Usuario
                    FROM cji_parqueo p 
                    INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo
                    inner join cji_producto prod on prod.PROD_Codigo = p.PROD_Codigo

                    left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo
                    left join cji_caja cj on cj.CAJA_Codigo = c.CAJA_Codigo
                    WHERE p.PARQC_FlagEstado LIKE '1' 

                    AND p.COMPP_Codigo = ".$this->compania."
                    $where $order $limit";
        
        //and (cd.CPDEC_FlagEstado = '1' or  cd.CPDEC_FlagEstado is null)
        
        $recF = "SELECT COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where";
        $recT = "SELECT COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where";
        $recAc = "SELECT  p.PARQC_FlagSituacion,COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where group by p.PARQC_FlagSituacion";        
        $records = $this->db->query($rec);
        if ($onlyRecords == false){
            $recordsFilter = $this->db->query($recF)->row()->registros;
            $recordsTotal = $this->db->query($recT)->row()->registros;
            $recordsAcum  = $this->db->query($recAc)->result();
        }
        if ($records->num_rows() > 0){
            if ($onlyRecords == false){
                $info = array(
                        "records" => $records->result(),
                        "recordsFilter" => $recordsFilter,
                        "recordsTotal"  => $recordsTotal,
                        "recordsAcum"   => $recordsAcum
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
                            "recordsTotal"  => $recordsTotal,
                            "recordsAcum"   => $recordsAcum
                             );
            }
            else{
                $info = NULL;
            }
        }
        return $info;
    }
    
    //Busqueda de registros de la tabla cji_parque por múltiples campos
   public function getParqueoXcamposGeneral($filter = NULL) {
        $limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
	if(isset($filter->ordenar)){
            $order = "ORDER BY ";
            foreach($filter->ordenar as $value){
		$order.=$value[0]." ".$value[1].",";
            }
            $order = substr($order,0,strlen($order)-1);
        }
        else{
            $order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";	
        }
        $where = '';
        
        if (isset($filter->situacion) && $filter->situacion != '')  $where .= " AND p.PARQC_FlagSituacion = '$filter->situacion'";	
        if (isset($filter->estacionamiento) && $filter->estacionamiento != '')  $where .= " AND p.PARQC_FlagEstacionamiento = '$filter->estacionamiento'";
        if (isset($filter->placa) && $filter->placa != '')          $where .= " AND p.PARQC_Placa = '$filter->placa'";	
        if (isset($filter->tarifa) && $filter->tarifa != '')        $where .= " AND p.TARIFP_Codigo = '$filter->tarifa'";	
        if (isset($filter->servicio) && $filter->servicio != '')    $where .= " AND p.PROD_Codigo = '$filter->servicio'";	
        if (isset($filter->serie) && $filter->serie != '')          $where .= " AND c.CPC_Serie = '$filter->serie'";	
        if (isset($filter->numero) && $filter->numero != '')        $where .= " AND c.CPC_Numero = '$filter->numero'";	
        if (isset($filter->tipo_tarifa_not) && $filter->tipo_tarifa_not != '') $where .= " AND t.TARIFC_Tipo != '$filter->tipo_tarifa_not'";	
        if (isset($filter->tipo_tarifa) && $filter->tipo_tarifa != ""){
            $tipo_tarifas = is_array($filter->tipo_tarifa)?$filter->tipo_tarifa:array($filter->tipo_tarifa);
            $where .= " AND t.TARIFC_Tipo IN (".implode(",",$tipo_tarifas).")";
        }
        
	$rec = "SELECT  p.*,
                        DATE_FORMAT(p.PARQC_FechaIngreso,'%d/%m/%Y') PARQC_FechaIn,
                        p.PARQC_HoraIngreso PARQC_HoraIn,
                        t.*,
                        c.CPC_Numero,
                        c.CPC_Serie,
                        c.CPC_TipoDocumento,
                        concat(c.CPC_Serie,'-',c.CPC_Numero) as CPC_SerieNumero,
                        c.CPC_FlagEstado,
                        cj.CAJA_Nombre,cj.CAJA_Usuario
                    FROM cji_parqueo p 
                    INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo
                    inner join cji_producto prod on prod.PROD_Codigo = p.PROD_Codigo
                    left join cji_comprobantedetalle cd on cd.PROD_Codigo = prod.PROD_Codigo
                    left join cji_comprobante c on c.CPP_Codigo = cd.CPP_Codigo
                    left join cji_caja cj on cj.CAJA_Codigo = c.CAJA_Codigo
                    WHERE p.PARQC_FlagEstado LIKE '1' 
                    $where $order $limit";
       
        $records = $this->db->query($rec);

        if ($records->num_rows() > 0)
            $info = $records->result();
        
        else
            $info = NULL;
        
        return $info;
    }    
    
    public function getParqueoXPlaca($filter = NULL) {
        $limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
	if(isset($filter->ordenar)){
            $order = "ORDER BY ";
            foreach($filter->ordenar as $value){
		$order.=$value[0]." ".$value[1].",";
            }
            $order = substr($order,0,strlen($order)-1);
        }
        else{
            $order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";	
        }
        $where = '';
        
        if (isset($filter->situacion) && $filter->situacion != '')  $where .= " AND p.PARQC_FlagSituacion = '$filter->situacion'";	
        if (isset($filter->estacionamiento) && $filter->estacionamiento != '')  $where .= " AND p.PARQC_FlagEstacionamiento = '$filter->estacionamiento'";
        if (isset($filter->placa) && $filter->placa != '')          $where .= " AND p.PARQC_Placa = '$filter->placa'";	
        if (isset($filter->tarifa) && $filter->tarifa != '')        $where .= " AND p.TARIFP_Codigo = '$filter->tarifa'";	
        if (isset($filter->servicio) && $filter->servicio != '')    $where .= " AND p.PROD_Codigo = '$filter->servicio'";	
        if (isset($filter->serie) && $filter->serie != '')          $where .= " AND c.CPC_Serie = '$filter->serie'";	
        if (isset($filter->numero) && $filter->numero != '')        $where .= " AND c.CPC_Numero = '$filter->numero'";	
        if (isset($filter->tipo_tarifa_not) && $filter->tipo_tarifa_not != '') $where .= " AND t.TARIFC_Tipo != '$filter->tipo_tarifa_not'";	
        if (isset($filter->tipo_tarifa) && $filter->tipo_tarifa != ""){
            $tipo_tarifas = is_array($filter->tipo_tarifa)?$filter->tipo_tarifa:array($filter->tipo_tarifa);
            $where .= " AND t.TARIFC_Tipo IN (".implode(",",$tipo_tarifas).")";
        }
        
	$rec = "SELECT  p.*,
                        DATE_FORMAT(p.PARQC_FechaIngreso,'%d/%m/%Y') PARQC_FechaIn,
                        p.PARQC_HoraIngreso PARQC_HoraIn,
                        t.*,
                        c.CPC_Numero,
                        c.CPC_Serie,
                        c.CPC_TipoDocumento,
                        concat(c.CPC_Serie,'-',c.CPC_Numero) as CPC_SerieNumero,
                        c.CPC_FlagEstado,
                        cj.CAJA_Nombre,cj.CAJA_Usuario
                    FROM cji_parqueo p 
                    INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo
                    inner join cji_producto prod on prod.PROD_Codigo = p.PROD_Codigo
                    left join cji_comprobantedetalle cd on cd.PROD_Codigo = prod.PROD_Codigo
                    left join cji_comprobante c on c.CPP_Codigo = cd.CPP_Codigo
                    left join cji_caja cj on cj.CAJA_Codigo = c.CAJA_Codigo
                    WHERE p.PARQC_FlagEstado LIKE '1' 
                    $where $order $limit";
       
        $records = $this->db->query($rec);

        if ($records->num_rows() > 0)
            $info = $records->result();
        
        else
            $info = NULL;
        
        return $info;
    }    
  
    public function getParqueo($codigo) {
        $where = " AND c.PARQP_Codigo = ".$codigo;						
        $sql = "
                SELECT  c.*,
                        t.TARIFC_Descripcion,
                        t.TARIFC_Precio,
                        t.TARIFC_Tipo,
                        concat(com.CPC_Serie,' - ',com.CPC_Numero) as CPC_SerieNumero,
                        com.CPC_Serie,
                        com.CPC_Numero
                FROM cji_parqueo c 
                INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = c.TARIFP_Codigo
                left join cji_comprobante com on (com.CPP_Codigo = c.CPP_Codigo)
                WHERE c.PARQC_FlagEstado = 1
                AND c.COMPP_Codigo = $this->compania
                $where
                ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
    
    public function getCantidadXCondicion($filter = NULL) {
        $where = '';
        
        if (isset($filter->situacion) && $filter->situacion != '')  $where .= " AND c.PARQC_FlagSituacion = '$filter->situacion'";	
        if (isset($filter->fecha) && $filter->fecha != '')          $where .= " AND c.PARQC_FechaIngreso = '$filter->fecha'";
        
        $sql = "
                SELECT count(*) cantidad
                FROM cji_parqueo c 
                WHERE c.PARQC_FlagEstado = 1 
                AND c.COMPP_Codigo = '".$this->compania."'
                $where
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return NULL;
    }      
    
    public function getMaxParqueo() {
        $sql = "
                SELECT max(c.PARQC_Numero) maximo
                FROM cji_parqueo c 
                WHERE c.PARQC_FlagEstado = 1 
                AND c.COMPP_Codigo = '".$this->compania."'
                ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }    

    public function registrar_parqueo($filter){
        $this->db->insert("cji_parqueo", (array) $filter);
        return $this->db->insert_id();
    }

    public function actualizar_parqueo($parqueo, $filter){
        $this->db->where('PARQP_Codigo',$parqueo);
        return $this->db->update('cji_parqueo', $filter);
    }
}
?>