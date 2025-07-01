<?php
class Facturadetalle_model extends CI_Model{
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
    public function listar($factura)
    {
        $where = array("FACTP_Codigo"=>$factura,"FACTDEC_FlagEstado"=>"1");
        $query = $this->db->order_by('FACTDEP_Codigo')->where($where)->get('cji_facturadetalle');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function insertar($factura,$producto,$unidad,$pu,$cantidad,$subtotal,$descuento,$igv,$total,$descuento100,$igv100,$observacion)
    {
        $data     = array(
                  "FACTP_Codigo"           =>$factura, 
                  "PROD_Codigo"            =>$producto, 
                  "UNDMED_Codigo"          =>$unidad, 
                  "FACTDEC_Pu"             =>$pu,
                  "FACTDEC_Cantidad"       =>$cantidad,
                  "FACTDEC_Subtotal"       =>$subtotal,
                  "FACTDEC_Descuento"      =>$descuento,
                  "FACTDEC_Igv"            =>$igv,
                  "FACTDEC_Total"          =>$total,
                  "FACTDEC_Descuento100"   =>$descuento100,
                  "FACTDEC_Igv100"         =>$igv100,
                  "FACTDEC_Observacion"    =>$observacion
            );
        $this->db->insert('cji_facturadetalle',$data);
    }
     public function modificar($factura_detalle,$producto,$unidad,$pu,$cantidad,$subtotal,$descuento,$igv,$total,$descuento100,$igv100,$observacion)
    {
        $data     = array(
                  "PROD_Codigo"            =>$producto, 
                  "UNDMED_Codigo"          =>$unidad, 
                  "FACTDEC_Pu"             =>$pu,
                  "FACTDEC_Cantidad"       =>$cantidad,
                  "FACTDEC_Subtotal"       =>$subtotal,
                  "FACTDEC_Descuento"      =>$descuento,
                  "FACTDEC_Igv"            =>$igv,
                  "FACTDEC_Total"          =>$total,
                  "FACTDEC_Descuento100"   =>$descuento100,
                  "FACTDEC_Igv100"         =>$igv100,
                  "FACTDEC_Observacion"    =>$observacion
        );
        $where = array("FACTDEP_Codigo"=>$factura_detalle);
        $this->db->where($where);
        $this->db->update('cji_facturadetalle',$data);
    }
    public function eliminar($factura_detalle)
    {
        $data      = array("FACTDEC_FlagEstado"=>'0');
        $where = array("FACTDEP_Codigo"=>$factura_detalle);
        $this->db->where($where);
        $this->db->update('cji_facturadetalle',$data);
    }
    
}
?>