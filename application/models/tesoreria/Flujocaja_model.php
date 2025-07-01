<?php
class Flujocaja_Model extends CI_Model
{
    protected $_name = "cji_flujocaja";
    public function  __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function listar($cuenta, $number_items='',$offset='')
    {   $where = array("CUE_Codigo"=>$cuenta, "FLUCAJ_FlagEstado"=>'1');
        
        $query = $this->db->order_by('FLUCAJ_FechaOperacion')
                ->join('cji_formapago', 'cji_formapago.FORPAP_Codigo = cji_flujocaja.FORPAP_Codigo', 'left')
                ->where($where)
                ->select('cji_flujocaja.*, cji_formapago.FORPAC_Descripcion FORPAC_Descripcion')
                ->from('cji_flujocaja', $number_items, $offset)
                ->get();
        if($query->num_rows() > 0){
          return $query->result();
        }
    }
    
    public function insertar(stdClass $filter = null)
    {        
        $this->db->insert("cji_flujocaja",(array)$filter);
        $id = $this->db->insert_id();
        return $id;
    }
    
    public function modificar($id,$filter)
    {
        $this->db->where("FLUCAJ_Codigo",$id);
        $this->db->update("cji_flujocaja",(array)$filter);
    }
    
    public function buscar_comprobantes($tipo_oper='V', $filter=NULL, $number_items='',$offset='')
    {   $compania = $this->somevar['compania'];
    
        $where='';
        if(isset($filter->fechai) && $filter->fechai!='' && isset($filter->fechaf) && $filter->fechaf!='')
            $where=' and cp.CPC_Fecha BETWEEN "'.human_to_mysql($filter->fechai).'" AND "'.human_to_mysql($filter->fechaf).'"';
        if(isset($filter->serie) && $filter->serie!='' && isset($filter->numero) && $filter->numero!='')
            $where.=' and cp.CPC_Serie="'.$filter->serie.'" and cp.CPC_Numero='.$filter->numero;

        if($tipo_oper!='C')
            if(isset($filter->cliente) && $filter->cliente!='')
                $where.=' and cp.CLIP_Codigo='.$filter->cliente;
        else
            if(isset($filter->proveedor) && $filter->proveedor!='')
                $where.=' and cp.PROVP_Codigo='.$filter->proveedor;
            
        if(isset($filter->producto) && $filter->producto!='')
            $where.=' and cpd.PROD_Codigo='.$filter->producto;
        $limit="";
        if((string)$offset!='' && $number_items!='')
            $limit = 'LIMIT '.$offset.','.$number_items;
        $sql = "SELECT cp.CPC_Fecha,
                       cp.CPP_Codigo,
                       cp.CPC_Serie,
                       cp.CPC_Numero,
                       cp.CPC_GuiaRemCodigo,
                       cp.CPC_DocuRefeCodigo,
                       (CASE ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")."  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CPC_total,
                       cp.CPC_FlagEstado,
                       cp.CPC_TipoDocumento
                FROM cji_comprobante cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_comprobantedetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
                ".($tipo_oper!='C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "INNER JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo")."
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")." ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")."='1'
                WHERE cp.CPC_TipoOperacion='".$tipo_oper."' 
                      AND cp.COMPP_Codigo =".$compania." ".$where."
                GROUP BY cp.CPP_Codigo
                ORDER BY cp.CPC_FechaRegistro DESC ".$limit;
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
    
    
   
}
?>