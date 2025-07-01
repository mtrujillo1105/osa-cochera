<?php
class Cotizacion_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('maestros/configuracion_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['usuario']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function seleccionar()
    {
        $arreglo = array('0'=>':: Seleccione ::');
        if(count($this->listar_cotizaciones2())>0){
            foreach($this->listar_cotizaciones() as $indice=>$valor){
                $indice1   = $valor->COTIP_Codigo;
                $valor1    = $valor->COTIC_Numero;
                if($valor1==0) $valor1='****';
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
    public function seleccionar2()
    {
        $arreglo = array('0'=>':: Seleccione ::');
        if(count($this->listar_cotizaciones2())>0){
            foreach($this->listar_cotizaciones2() as $indice=>$valor){
                $indice1   = $valor->COTIP_Codigo;
                $valor1    = $valor->COTIC_Numero;
                if($valor1==0) $valor1='****';
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
    public function listar_cotizaciones($number_items='',$offset='')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania,"COTIC_FlagEstado"=>"1");
        $query = $this->db->order_by('COTIC_Numero','desc')->where_not_in('COTIP_Codigo','0')->where($where)->get('cji_cotizacion',$number_items,$offset);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
	public function listar_cotizaciones2($number_items='',$offset=''){
            $compania = $this->somevar['compania'];
            $where = array("COMPP_Codigo"=>$compania,"COTIC_FlagEstado"=>"1","COTIC_FlagCompra"=>"0");
            $query = $this->db->order_by('COTIC_Numero','desc')->where_not_in('COTIP_Codigo','0')->where($where)->get('cji_cotizacion',$number_items,$offset);
            if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                    $data[] = $fila;
                }
                return $data;
            }
	}
    public function obtener_cotizacion($cotizacion){
        $query = $this->db->where('COTIP_Codigo',$cotizacion)->get('cji_cotizacion');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
    public function obtener_detcotizacion_producto($producto,$cotizacion){
        $where = array("PROD_Codigo"=>$producto,"COTIP_Codigo"=>$cotizacion,"COTDEC_FlagEstado"=>1);
        $query = $this->db->where($where)->get('cji_cotizaciondetalle');
        return $query->row();
    }
    public function obtener_detalle_cotizacion($cotizacion){
         $where = array("COTIP_Codigo"  => $cotizacion,"COTDEC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_cotizaciondetalle');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
    public function obtener_detalle_cotizacion2($cotizacion){
         $where = array("COTIP_Codigo"  => $cotizacion,"COTDEC_FlagEstado" =>"1","COTDEC_FlagOcompra"=>"0");
        $query = $this->db->where($where)->get('cji_cotizaciondetalle');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
    public function insertar_cotizacion($pedido,$numero,$proveedor,$forma_pago,$condicion_entrega,$lugar_entrega,$centrocosto,$observacion){
        $compania = $this->somevar['compania'];
        $usuario     =  $this->somevar ['usuario'] ;
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($compania,'2');
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;
		$data = array(
					"PEDIP_Codigo"        => $pedido,
                    "COTIC_Numero"     => $numero,
                    "PROVP_Codigo"     => $proveedor,
                    "FORPAP_Codigo"  => $forma_pago,
                    "CONENP_Codigo" => $condicion_entrega,
                    "ALMAP_Codigo"    => $lugar_entrega,
                    "COTIC_Observacion"     => $observacion,
                    "USUA_Codigo"           => $usuario,
                    "CENCOSP_Codigo"  => $centrocosto,
                    "COMPP_Codigo"      => $compania
					);
		$this->db->insert("cji_cotizacion",$data);
        $cotizacion = $this->db->insert_id();
        $this->configuracion_model->modificar_configuracion($compania,2,$numero);
        return $cotizacion;
    }
    public function insertar_detalle_cotizacion($cotizacion,$pedido,$producto,$cantidad,$unidad,$observ)
    {
        $data = array(
        "COTIP_Codigo"       => $cotizacion,
        "PEDIP_Codigo"       => $pedido,
        "PROD_Codigo"        => $producto,
        "COTDEC_Cantidad"    => $cantidad,
        "COTDEC_Observacion" => $observ,
        "UNDMED_Codigo"      =>$unidad
        );
        $this->db->insert("cji_cotizaciondetalle",$data);
    }
    public function modificar_cotizacion($cotizacion,$proveedor,$forma_pago,$lugar_entrega,$condicion_entrega,$observacion){
         $data     = array(
                                    "PROVP_Codigo"          =>$proveedor,
                                    "FORPAP_Codigo"        =>$forma_pago,
                                    "CONENP_Codigo"       =>$condicion_entrega,
                                    "ALMAP_Codigo"          =>$lugar_entrega,
                                    "COTIC_Observacion" =>$observacion
                           );
         $where = array("COTIP_Codigo"=>$cotizacion);
		$this->db->where($where);
		$this->db->update('cji_cotizacion',$data);
    }
    public function modificar_producto_cotizacion($cotizacion_detalle,$cantidad,$observacion)
    {
        $data     = array(
                       "COTDEC_Cantidad"         =>$cantidad,
                       "COTDEC_Observacion"  =>$observacion
                    );
        $where = array("COTDEP_Codigo"=>$cotizacion_detalle);
        $this->db->where($where);
        $this->db->update('cji_cotizaciondetalle',$data);
    }
    public function modificar_detcotizacion_flagCompra($detcotizacion){
        $data=array("COTDEC_FlagOcompra"=>1);
        $where = array("COTDEP_Codigo"=>$detcotizacion);
        $this->db->where($where);
        $this->db->update('cji_cotizaciondetalle',$data);
    }
    public function modificar_cotizacion_flagCompra($cotizacion)
    {
        $where  = array("COTDEC_FlagEstado"=>"1","COTIP_Codigo"=>$cotizacion);
        $query  = $this->db->where($where)->get("cji_cotizaciondetalle");
        $where2 = array("COTDEC_FlagEstado"=>"1","COTIP_Codigo"=>$cotizacion,"COTDEC_FlagOcompra"=>1);
        $query2 = $this->db->where($where2)->get("cji_cotizaciondetalle");
        if($query->num_rows==$query2->num_rows){
            $this->db->where(array("COTIP_Codigo"=>$cotizacion))->update("cji_cotizacion",array("COTIC_FlagCompra"=>1));
        }
    }
    public function eliminar_cotizacion($cotizacion){
        $data      = array("COTIC_FlagEstado"=>'0');
        $where = array("COTIP_Codigo"=>$cotizacion);
        $this->db->where($where);
        $this->db->update('cji_cotizacion',$data);
        $data      = array("COTDEC_FlagEstado"=>'0');
        $where = array("COTIP_Codigo"=>$cotizacion);
        $this->db->where($where);
        $this->db->update('cji_cotizaciondetalle',$data);
    }
    public function eliminar_producto_cotizacion($detalle_cotizacion){
        $data      = array("COTDEC_FlagEstado"=>'0');
        $where = array("COTDEP_Codigo"=>$detalle_cotizacion);
        $this->db->where($where);
        $this->db->update('cji_cotizaciondetalle',$data);
    }
}
?>