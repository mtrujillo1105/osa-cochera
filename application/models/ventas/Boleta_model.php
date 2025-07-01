<?php
class Boleta_model extends CI_Model{
    var $somevar;
	function __construct(){
		parent::__construct();
		$this->load->database();
        $this->load->helper('date');
        $this->load->model('mantenimiento_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
         $this->somevar ['usuario']    = $this->session->userdata('usuario');
        $this->somevar['hoy']              = mdate("%Y-%m-%d %h:%i:%s",time());
	}
    function listar_boletas($number_items='',$offset=''){
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania,"BOLEC_FlagEstado"=>"1");
        $query = $this->db->order_by('BOLEC_Numero','desc')->where($where)->get('cji_boleta',$number_items,$offset);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    function obtener_boleta($boleta){
        $query = $this->db->where('BOLEP_Codigo',$boleta)->get('cji_boleta');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
    function insertar_boleta($presupuesto,$ccosto,$numero,$cliente,$moneda,$subtotal,$descuento,$igv,$total,$observacion){
        $compania = $this->somevar['compania'];
        $usuario     =  $this->somevar ['usuario'] ;
        $datos_configuracion = $this->mantenimiento_model->obtener_numero_documento($compania,'9');
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;
		$data = array(
					"PRESUP_Codigo"                => $presupuesto,
                    "BOLEC_Numero"               => $numero,
                    "CLIP_Codigo"                      => $cliente,
                    "USUA_Codigo"                   => $usuario,
                    "CENCOSP_Codigo"          => $ccosto,
                    "MONED_Codigo"             => $moneda,
                    "BOLEC_Subtotal"             =>  $subtotal,
                    "BOLEC_Descuento"        => $descuento,
                    "BOLEC_Igv"                        => $igv,
                    "BOLEC_Total"                    => $total,
                     "BOLEC_Observacion"   => $observacion,
                    "COMPP_Codigo"              => $compania
					);
		$this->db->insert("cji_boleta",$data);
        $boleta = $this->db->insert_id();
        $this->mantenimiento_model->modificar_configuracion($compania,9,$numero);
        return $boleta;
    }
    function modificar_boleta($boleta,$presupuesto,$ccosto,$numero,$cliente,$moneda,$subtotal,$descuento,$igv,$total,$observacion){
        $usuario     =  $this->somevar ['usuario'] ;
         $data     = array(
                                    "PRESUP_Codigo"            =>$presupuesto,
                                    "BOLEC_Numero"            =>$numero,
                                    "CLIP_Codigo"                   =>$cliente,
                                    "USUA_Codigo"                =>$usuario,
                                    "CENCOSP_Codigo"        =>$ccosto,
                                    "MONED_Codigo"           =>$moneda,
                                    "BOLEC_Subtotal"           =>$subtotal,
                                    "BOLEC_Descuento"      =>$descuento,
                                   "BOLEC_Igv"                       =>$igv,
                                   "BOLEC_Total"                   =>$total,
                                   "BOLEC_Observacion"   =>$observacion
                           );
         $where = array("BOLEP_Codigo"=>$boleta);
		$this->db->where($where);
		$this->db->update('cji_boleta',$data);
    }
    function eliminar_boleta($boleta){
		$data      = array("BOLEC_FlagEstado"=>'0');
		$where = array("BOLEP_Codigo"=>$boleta);
		$this->db->where($where);
		$this->db->update('cji_boleta',$data);
		$data      = array("BOLEDEC_FlagEstado"=>'0');
		$where = array("BOLEP_Codigo"=>$boleta);
		$this->db->where($where);
		$this->db->update('cji_boletadetalle',$data);
    }
}
?>