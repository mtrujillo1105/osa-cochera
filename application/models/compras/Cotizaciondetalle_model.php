<?php
class Cotizaciondetalle extends CI_Model{
    var $somevar;
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    function tns_actualiza_cotizacion($cotizacion)
    {
        $where = array("COTDEC_FlagEstado"=>"1","COTIP_Codigo"=>$cotizacion);
        $query = $this->db->where($where)->get("cji_cotizaciondetalle");
        if($query->num_rows() > 0){
            $cantidad = $query->num_rows;
            $jj=0;
            foreach($query->result() as $fila){
                if($fila->COTDEC_FlagOcompra==1)    $jj++;
            }
            if($cantidad==$jj){
               $data   = array("COTIC_FlagCompra"=>1);
               $query2 = $this->db->where(array("COTIP_Codigo"=>$cotizacion))->update("cji_cotizacion",$data);
            }
        }
    }
}
?>