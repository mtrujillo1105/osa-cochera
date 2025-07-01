<?php
class Cuadrocom_model extends CI_Model{
    var $somevar;
	function __construct()
        {
            parent::__construct();
            $this->load->database();
            $this->load->helper('date');
            $this->load->model('mantenimiento_model');
			$this->load->model('configuracion_model');
			$this->load->model('maestros/companiaconfiguracion_model');
			$this->load->model('maestros/companiaconfidocumento_model');
            $this->somevar ['compania'] = $this->session->userdata('compania');
            $this->somevar ['usuario']    = $this->session->userdata('user');
            $this->somevar['hoy']              = mdate("%Y-%m-%d %h:%i:%s",time());
	}
  
  
    public function buscar_cuadros($filter, $number_items='',$offset='')
    {   $compania = $this->somevar['compania'];
        $data_confi           = $this->companiaconfiguracion_model->obtener($compania);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        
        $where='';
		
		/*
        if(isset($filter->fechai) && $filter->fechai!='' && isset($filter->fechaf) && $filter->fechaf!='')
            $where=' and p.PRESUC_Fecha BETWEEN "'.human_to_mysql($filter->fechai).'" AND "'.human_to_mysql($filter->fechaf).'"';
		*/
		
        if(isset($filter->observacion) && $filter->observacion!='')
            $where.=' and COMP_Observacion LIKE \'%'.$filter->observacion.'%\'';
			
        $limit="";
        if((string)$offset!='' && $number_items!='')
            $limit = 'LIMIT '.$offset.','.$number_items;
        
        $sql = "SELECT * FROM cji_comparativo 
				LEFT JOIN cji_pedido USING (PEDIP_Codigo)
                WHERE cji_comparativo.COMPP_Codigo =".$compania." ".$where." 
                ORDER BY COMP_FechaRegistro DESC ".$limit."
                ";
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
  
  
  public function seleccionar_ganadores($pedido)
  {
      $arreglo = array(''=>':: Seleccione ::');
      $lista = $this->seleccionar_cotizaciones_ganadoras($pedido);
      if(count($lista)>0){
          foreach($lista as $indice=>$valor)
          {   $indice1   = $valor->PRESUP_Codigo;
              $valor1    = $valor->PRESUC_Numero." / ".$valor->EMPRC_RazonSocial;                
              $arreglo[$indice1] = $valor1;
          }
      }
      return $arreglo;
  }
  
  public function seleccionar_todas($pedido)
  {
    
      $arreglo = array(''=>':: Seleccione ::');
	$compania = $this->somevar['compania'];
	$this->db->select('*');
	$this->db->from('cji_presupuesto');
	$this->db->join('cji_proveedor','cji_presupuesto.PROVP_Codigo = cji_proveedor.PROVP_Codigo');
	$this->db->join('cji_empresa','cji_proveedor.EMPRP_Codigo = cji_empresa.EMPRP_Codigo','left');
	$this->db->where('cji_presupuesto.PEDIP_Codigo',$pedido);
	$this->db->where('cji_presupuesto.CPC_TipoOperacion','S');
	$query = $this->db->get();
	$data = array();
	if($query->num_rows() > 0){
		foreach($query->result() as $fila){
			$data[] = $fila;
		}
	}
	  $lista = $data;
      if(count($lista)>0){
          foreach($lista as $indice=>$valor)
          {   $indice1   = $valor->PRESUP_Codigo;
              $valor1    = $valor->PRESUC_Numero." / ".$valor->EMPRC_RazonSocial;                
              $arreglo[$indice1] = $valor1;
          }
      }
      return $arreglo;
  }
  
  public function seleccionar_cotizaciones_ganadoras($pedido){
        $compania = $this->somevar['compania'];
		$this->db->select('*');
        $this->db->from('cji_comparativodetalle');
        $this->db->join('cji_comparativo','cji_comparativodetalle.COMP_Codigo = cji_comparativo.COMP_Codigo');
        $this->db->join('cji_presupuesto','cji_comparativodetalle.PRESUP_Codigo = cji_presupuesto.PRESUP_Codigo');
        $this->db->join('cji_proveedor','cji_presupuesto.PROVP_Codigo = cji_proveedor.PROVP_Codigo');
        $this->db->join('cji_empresa','cji_proveedor.EMPRP_Codigo = cji_empresa.EMPRP_Codigo','left');
        $this->db->where('cji_comparativo.PEDIP_Codigo',$pedido);
        $this->db->where('cji_comparativodetalle.CUACOMC_Ganador',1);
        $query = $this->db->get();
        if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
        }
  }
  
  public function seleccionar()
  {
      $arreglo = array(''=>':: Seleccione ::');
      $lista = $this->listar_pedidos();
      if(count($lista)>0){
          foreach($lista as $indice=>$valor)
          {   $indice1   = $valor->PEDIP_Codigo;
              $valor1    = $valor->PEDIC_Numero." ".$valor->PEDIC_Observacion." [".$valor->PEDIC_Tipo.']';                
              $arreglo[$indice1] = $valor1;
          }
      }
      return $arreglo;
  }
  
	function listar_cuadros($number_items='',$offset='')
    {
        $compania = $this->somevar['compania'];
		$this->db->select('*');
        $this->db->from('cji_comparativo',$number_items,$offset);
        $this->db->join('cji_pedido','cji_comparativo.PEDIP_Codigo = cji_pedido.PEDIP_Codigo','left');
        $this->db->where('cji_comparativo.COMPP_Codigo',$compania);
        $this->db->order_by('COMP_FechaRegistro','DESC');
        $query = $this->db->get();
        if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
        }
	}
	
	
    function obtener_pedido($pedido){
        $query = $this->db->where('PEDIP_Codigo',$pedido)->get('cji_pedido');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
	
	
    function obtener_detalle_pedido($pedido){
         $where = array("PEDIP_Codigo"  => $pedido,"PEDIDETC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_pedido');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
    }
    function insertar_comparativo($descripcion,$pedido){
      $compania = $this->somevar['compania'];
      $usuario =  $this->somevar['usuario'];
      $fecha = date('Y-m-d h:i:s');
      $data = array(
        'COMP_Observacion' => $descripcion,
        'COMC_FlagEstado' => 'C',
        'COMP_FechaRegistro' => $fecha,
        'COMPP_Codigo' => $compania,
        'PEDIP_Codigo' => $pedido
      );
      $this->db->insert("cji_comparativo",$data);
      return $this->db->insert_id();
    }
    
    function insertar_comparativo_detalle($comparativo,$cotizacion,$observacion,$ganador='0')
    {
      if(is_null($ganador))
        $ganador = 0;
      $data = array(
        'COMP_Codigo' => $comparativo,
        'PRESUP_Codigo' => $cotizacion,
        'CUACOMC_Observacion' => $observacion,
        'CUACOMC_Ganador' => $ganador
      );
      $this->db->insert("cji_comparativodetalle",$data);
    }
    function insertar_detalle_pedido(){

    }
    function modificar_pedido($pedido,$centro_costo,$numero_documento,$observacion,$tipo_pedido){
      $compania = $this->somevar['compania'];
      $usuario =  $this->somevar['usuario'];
      $fecha = date('Y-m-d h:i:s');
      $data = array(
        'PEDIC_Numero' => $numero_documento,
        'CENCOST_Codigo' => $centro_costo,
        'USUA_Codigo' => $usuario,
        'USUA_Responsable' => $usuario,
        'PEDIC_Observacion' => $observacion,
        'PEDIC_FechaModificacion' => $fecha,
        'COMPP_Codigo' => $compania,
        'PEDIC_Tipo' => $tipo_pedido
      );
      $this->db->where("PEDIP_Codigo",$pedido);
      $this->db->update("cji_pedido",$data);
    }
	

    function eliminar_pedido($pedido){
		$data      = array("PEDIC_FlagEstado"=>'0');
		$where = array("PEDIP_Codigo"=>$pedido);
		$this->db->where($where);
		$this->db->update('cji_pedido',$data);
		$data      = array("PEDIDETC_FlagEstado"=>'0');
		$where = array("PEDIP_Codigo"=>$pedido);
		$this->db->where($where);
		$this->db->update('cji_pedidodetalle',$data);
    }
    function eliminar_producto_pedido($detalle_pedido){
		$data      = array("PEDIDETC_FlagEstado"=>'0');
		$where = array("PEDIDETP_Codigo"=>$detalle_pedido);
		$this->db->where($where);
		$this->db->update('cji_pedidodetalle',$data);
    }
}
?>