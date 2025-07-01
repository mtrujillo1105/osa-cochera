<?php
class Notacreditodetalle_model extends CI_Model{
    var $somevar;
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    
    public function listar($comprobante){
        /*$where = array("CRED_Codigo"=>$comprobante,"CREDET_FlagEstado"=>"1");
        $query = $this->db->order_by('CREDET_Codigo')->where($where)->get('cji_notadetalle');*/

        // Cambios para mostrar el TDC al editar una nota de credito - 

        $sql = "SELECT n.CRED_TDC, n.MONED_Codigo, n.CRED_ComproInicio, nd.*,
                    pr.PROD_CodigoInterno, pr.PROD_CodigoUsuario, pr.PROD_CodigoOriginal, pr.PROD_FlagBienServicio, pr.PROD_Nombre, um.UNDMED_Simbolo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion,
                    l.LOTC_Numero, l.LOTC_FechaVencimiento
                    
                    FROM `cji_notadetalle` nd
                    INNER JOIN cji_nota n ON n.CRED_Codigo = nd.CRED_Codigo

                        INNER JOIN cji_producto pr ON nd.PROD_Codigo = pr.PROD_Codigo
                        LEFT JOIN cji_marca m ON m.MARCP_Codigo = pr.MARCP_Codigo
                        LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = nd.UNDMED_Codigo
                        LEFT JOIN cji_lote l ON l.LOTP_Codigo = nd.LOTP_Codigo

                        WHERE nd.`CRED_Codigo` = $comprobante AND nd.`CREDET_FlagEstado` = 1
                        ORDER BY nd.CREDET_Codigo
                ";

        $query = $this->db->query($sql);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function insertar($filter=null){
        
        $insert = $this->db->insert('cji_notadetalle',(array)$filter);
        return $insert;
    }
    
    public function modificar($comprobante_detalle,$filter=null){
        $where = array("CREDET_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_notadetalle',(array)$filter);
    }
    
    public function eliminar($comprobante_detalle){
        $data      = array("CREDET_FlagEstado"=>'0');
        $where = array("CREDET_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_notadetalle',$data);
    }
    
    public function reporte_ganancia($producto, $f_ini, $f_fin, $companias=''){       
    $where = array('c.CRED_Fecha >='=>$f_ini,'c.CRED_Fecha <='=>$f_fin, 'c.CRED_TipoOperacion'=>'V', 'd.CREDET_FlagEstado'=>'1');
            if($producto!='')
                $where['d.PROD_Codigo']=$producto;
            
            $companias=is_array($companias) ? $companias :  array($this->somevar['compania']);
            
            $query = $this->db->where($where)
                              ->where_in('c.COMPP_Codigo', $companias)
                              ->join('cji_credito c', 'c.CRED_Codigo = d.CRED_Codigo', 'left')
                              ->join('cji_producto p', 'p.PROD_Codigo = d.PROD_Codigo', 'left')
                              ->join('cji_moneda m', 'm.MONED_Codigo = c.MONED_Codigo', 'left')
                              ->join('cji_compania co', 'co.COMPP_Codigo = c.COMPP_Codigo', 'left')
                              ->join('cji_emprestablecimiento ee', 'ee.EESTABP_Codigo = co.EESTABP_Codigo', 'left')
                              ->select('d.*, m.MONED_Simbolo, c.CRED_Fecha, c.COMPP_Codigo, ee.EESTABC_Descripcion, p.PROD_Nombre')->from('cji_creditodetalle d')->get();
            if($query->num_rows() > 0)
                return $query->result();
            else
                return array();
        
    }
    
}
?>