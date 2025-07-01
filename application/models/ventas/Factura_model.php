<?php
class Factura_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('configuracion_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function listar_facturas($number_items='',$offset='')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania);
        $query = $this->db->order_by('FACTC_Numero','desc')->where($where)->get('cji_factura',$number_items,$offset);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function obtener_factura($factura)
    {
        $query = $this->db->where('FACTP_Codigo',$factura)->get('cji_factura');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila)
            {
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function insertar_factura($presupuesto,$forma_pago,$serie,$numero,$cliente,$moneda,$subtotal,$descuentotal,$igvtotal,$total,$igv100,$descuento100,$observacion,$fecha)
    {
        $compania = $this->somevar['compania'];
        $user =  $this->somevar ['user'] ;
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($compania,'8');
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;
        $data = array(
                "PRESUP_Codigo"       => $presupuesto,
                "FACTC_Serie"         =>$serie,
                "FACTC_Numero"        => $numero,
                "CLIP_Codigo"         => $cliente,
                "USUA_Codigo"         => $user,
                "MONED_Codigo"        => $moneda,
                "FACTC_subtotal"      => $subtotal,
                "FACTC_descuento"     => $descuentotal,
                "FACTC_igv"           => $igvtotal,
                "FACTC_total"         => $total,
                "FACTC_Observacion"   => $observacion,
                "FORPAP_Codigo"       =>$forma_pago,
                "FACTC_igv100"        =>$igv100,
                "FACTC_descuento100"  =>$descuento100,
                "FACTC_Fecha"         =>$fecha,
                "COMPP_Codigo"        => $compania
                );
        $this->db->insert("cji_factura",$data);
        $factura = $this->db->insert_id();
        $this->configuracion_model->modificar_configuracion($compania,8,$numero);
        return $factura;
    }
    
    public function modificar_factura($factura,$presupuesto,$forma_pago,$serie,$numero,$cliente,$moneda,$subtotal,$descuentotal,$igvtotal,$total,$igv100,$descuento100,$observacion,$fecha)
    {
        $user     =  $this->somevar ['user'] ;
        $data     = array(
                        "PRESUP_Codigo"        =>$presupuesto,
                        "FACTC_Serie"          =>$serie,
                        "FACTC_Numero"         =>$numero,
                        "CLIP_Codigo"          =>$cliente,
                        "USUA_Codigo"          =>$user,
                        "MONED_Codigo"         =>$moneda,
                        "FACTC_subtotal"       =>$subtotal,
                        "FACTC_descuento"      =>$descuentotal,
                        "FACTC_igv"            =>$igvtotal,
                        "FACTC_total"          =>$total,
                        "FACTC_Observacion"    =>$observacion,
                        "FORPAP_Codigo"        =>$forma_pago,
                        "FACTC_igv100"         =>$igv100,
                        "FACTC_descuento100"  =>$descuento100,
                        "FACTC_Fecha"         =>$fecha
               );
        $where = array("FACTP_Codigo"=>$factura);
        $this->db->where($where);
        $this->db->update('cji_factura',$data);
    }
   
    public function eliminar_factura($factura)
    {
        $data      = array("FACTC_FlagEstado"=>'0');
        $where = array("FACTP_Codigo"=>$factura);
        $this->db->where($where);
        $this->db->update('cji_factura',$data);
        
        $data      = array("FACTDEC_FlagEstado"=>'0');
        $where = array("FACTP_Codigo"=>$factura);
        $this->db->where($where);
        $this->db->update('cji_facturadetalle',$data);
    }

}
?>