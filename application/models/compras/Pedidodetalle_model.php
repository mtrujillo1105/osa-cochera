<?php
class Pedidodetalle_model extends CI_Model{
    var $somevar;
    protected $_name = 'cji_pedidodetalle';
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }

    public function listar($cod_pedido){
        $sql = "SELECT pd.* (SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as stock
                    FROM cji_pedidodetalle pd
                    WHERE pd.PEDIDETP_Codigo = $cod_pedido AND pd.PEDIDETC_FlagEstado = 1";

        $where = array("PEDIP_Codigo"=>$cod_pedido,"PEDIDETC_FlagEstado"=>"1");
        $query = $this->db->order_by('PEDIDETP_Codigo')->where($where)->get($this->_name);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function listar1($pedido)
    {
        $where = array("PEDIP_Codigo"=>$pedido,"PEDIDETC_FlagEstado"=>"1");
        $query = $this->db->order_by('PEDIDETP_Codigo')->where($where)->get('cji_pedidodetalle');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function insertar($filter=null)
    {
        $filter->PEDIDETC_FechaRegistro= date('Y-m-d h:i:s');
        $this->db->insert($this->_name,(array)$filter);
        return $this->db->insert_id();
    }
    public function insertar_varios($filter=null){
        $this->db->insert($this->_name,(array)$filter);
        return $this->db->insert_id();
    }
     public function modificar($pedido_detalle,$filter=null)
    {
        $filter->PEDIDETC_FechaModificacion= date('Y-m-d h:i:s');
        $where = array("PEDIDETP_Codigo"=>$pedido_detalle);
        $this->db->where($where);
        $this->db->update($this->_name,(array)$filter);
    }
    public function eliminar($pedido_detalle)
    {
        $data      = array("PEDIDETC_FlagEstado"=>'0');
        $where = array("PEDIDETP_Codigo"=>$pedido_detalle);
        $this->db->where($where);
        $this->db->update($this->_name,$data);
    }
    
    public function eliminar_x_pedido($cod_pedido){
        $where = array("PEDIP_Codigo"=>$cod_pedido);
        $this->db->where($where);
        $this->db->delete($this->_name,$data);
    }
    
}
?>