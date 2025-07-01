<?php
class Importaciondetalle_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
   
    public function listar($comprobante)
    {
        $where = array("IMPOR_Codigo"=>$comprobante,"IMPORDEC_FlagEstado"=>"1");
        $query = $this->db->from('cji_importaciondetalle')
                            ->order_by('IMPORDEP_Codigo')
                            ->where($where)
                            ->join("cji_ocompradetalle", "cji_ocompradetalle.OCOMDEP_Codigo = cji_importaciondetalle.OCOMDEP_Codigo", "LEFT")
                            ->join("cji_ordencompra", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo_venta", "LEFT")
                            ->join("cji_proyecto", "cji_proyecto.PROYP_Codigo = cji_ordencompra.PROYP_Codigo", "LEFT")
                            ->select("cji_importaciondetalle.*, cji_proyecto.*, cji_ordencompra.*")->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
/*SELECT * 
FROM cji_importacion i
INNER JOIN cji_importaciondetalle id ON id.IMPOR_Codigo=i.IMPOR_Codigo
WHERE i.OCOMP_Codigo = 66 AND IMPORDEC_FlagEstado= 1 ORDER BY IMPORDEP_Codigo*/
   
    public function listar_impordetalle($ocompra)
    {
        $where = array("i.OCOMP_Codigo"=>$ocompra,"id.IMPORDEC_FlagEstado"=>"1");
        $query = $this->db->select('*')
                          ->join('cji_importaciondetalle id','id.IMPOR_Codigo=i.IMPOR_Codigo')
                          ->order_by('id.IMPORDEP_Codigo')
                          ->where($where)
                          ->get('cji_importacion i');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar($filter=null)
    {
        $this->db->insert('cji_importaciondetalle',(array)$filter);
        
    }
     public function modificar($comprobante_detalle,$filter=null)
    {
        $where = array("IMPORDEP_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_importaciondetalle',(array)$filter);
    }
    public function eliminar($comprobante_detalle)
    {
        $data      = array("IMPORDEC_FlagEstado"=>'0');
        $where = array("IMPORDEP_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_importaciondetalle',$data);
    }
    
    public function reporte_ganancia($producto, $f_ini, $f_fin, $companias='')
    {       $where = array('c.CPC_Fecha >='=>$f_ini,'c.CPC_Fecha <='=>$f_fin, 'c.CPC_TipoOperacion'=>'V', 'd.CPDEC_FlagEstado'=>'1');
            if($producto!='')
                $where['d.PROD_Codigo']=$producto;
            
            $companias=is_array($companias) ? $companias :  array($this->somevar['compania']);
            
            $query = $this->db->where($where)
                              ->where_in('c.COMPP_Codigo', $companias)
                              ->join('cji_comprobante c', 'c.CPP_Codigo = d.CPP_Codigo', 'left')
                              ->join('cji_producto p', 'p.PROD_Codigo = d.PROD_Codigo', 'left')
                              ->join('cji_moneda m', 'm.MONED_Codigo = c.MONED_Codigo', 'left')
                              ->join('cji_compania co', 'co.COMPP_Codigo = c.COMPP_Codigo', 'left')
                              ->join('cji_emprestablecimiento ee', 'ee.EESTABP_Codigo = co.EESTABP_Codigo', 'left')
                              ->select('d.*, m.MONED_Simbolo, c.CPC_Fecha, c.COMPP_Codigo, ee.EESTABC_Descripcion, p.PROD_Nombre')->from('cji_comprobantedetalle d')->get();
            if($query->num_rows() > 0)
                return $query->result();
            else
                return array();
        
    }

    public function listar_compras_by_id_orden_producto($id_orden, $id_producto)
    {
        $query = $this->db->from("cji_importaciondetalle");

        $query->join("cji_importacion", "cji_importacion.IMPOR_Codigo = cji_importaciondetalle.IMPOR_Codigo");
        $query->join("cji_ocompradetalle", "cji_ocompradetalle.OCOMDEP_Codigo = cji_importaciondetalle.OCOMDEP_Codigo");
        $query->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_importacion.MONED_Codigo");

        $query->where("cji_ocompradetalle.OCOMP_Codigo_venta", $id_orden);
        $query->where("cji_importaciondetalle.PROD_Codigo", $id_producto);

        return $query->select(array("cji_importaciondetalle.*", "cji_importacion.*", "cji_moneda.*"))->get()->result();
    }
    
}
?>