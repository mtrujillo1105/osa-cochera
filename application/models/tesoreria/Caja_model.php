<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Caja_model extends CI_Model{

    private $empresa;
    private $compania;
    private $usuario;
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('date');
        
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
    }

    #########################
    ###### FUNCTIONS NEWS
    #########################

    public function getCajas($filter = NULL) {

        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $where = '';
        
        if (isset($filter->cajero) && $filter->cajero != '')
            $where .= " AND c.CAJA_Usuario = $filter->cajero";        
        
        if (isset($filter->codigo) && $filter->codigo != '')
            $where .= " AND c.CAJA_Codigo = $filter->codigo";

        if (isset($filter->descripcion) && $filter->descripcion != '')
            $where .= " AND c.CAJA_Nombre LIKE '%$filter->descripcion%'";

        if (isset($filter->tipo) && $filter->tipo != '')
            $where .= " AND c.tipCa_codigo = $filter->tipo";
        
        if (isset($filter->situacion) && $filter->situacion != '')
            $where .= " AND c.CAJA_FlagSituacion = $filter->situacion";        

        $sql = "SELECT c.*, 
                       tp.tipCa_Descripcion,
                       concat(p.PERSC_Nombre,' ',p.PERSC_ApellidoPaterno,' ',p.PERSC_ApellidoMaterno) nombres
                FROM cji_caja c
                LEFT JOIN cji_tipocaja tp ON tp.tipCa_codigo = c.tipCa_codigo
                LEFT JOIN cji_usuario u on u.USUA_Codigo = c.CAJA_Usuario
                LEFT JOIN cji_persona p on p.PERSP_Codigo = u.PERSP_Codigo
                WHERE c.CAJA_FlagEstado LIKE '1' AND c.COMPP_Codigo = $this->compania $where $order $limit";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
    
    public function getCajasLogin($compania){
        
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $where = '';
        
        if (isset($filter->descripcion) && $filter->descripcion != '')
            $where .= " AND c.CAJA_Nombre LIKE '%$filter->descripcion%'";        
        
        $sql = "SELECT c.*, tp.tipCa_Descripcion
                    FROM cji_caja c
                    LEFT JOIN cji_tipocaja tp ON tp.tipCa_codigo = c.tipCa_codigo
                    WHERE c.CAJA_FlagEstado LIKE '1' AND c.CAJA_FlagSituacion = 1
                    AND c.COMPP_Codigo = $compania $where $order $limit";
        


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getCaja($codigo) {

        $sql = "SELECT c.*,
                       concat(p.PERSC_Nombre,' ',p.PERSC_ApellidoPaterno,' ',p.PERSC_ApellidoMaterno) nombre_responsable,
                       u.USUA_usuario
                FROM cji_caja c 
                LEFT JOIN cji_usuario u on u.USUA_Codigo = c.CAJA_Usuario
                LEFT JOIN cji_persona p on p.PERSP_Codigo = u.PERSP_Codigo
                WHERE c.CAJA_Codigo = $codigo";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return array();
    }

    public function insertar_caja($filter){
        $this->db->insert("cji_caja", (array) $filter);
        return $this->db->insert_id();
    }

    public function actualizar_caja($caja, $filter){
        $this->db->where('CAJA_Codigo',$caja);
        return $this->db->update('cji_caja', $filter);
    }

    public function deshabilitar_caja($caja, $filter){
        $this->db->where('CAJA_Codigo',$caja);
        $query = $this->db->update('cji_caja', $filter);
        return $query;
    }

    #########################
    ###### FUNCTIONS OLDS
    #########################

	public function listar_cajas(){
        $where = array("CAJA_FlagEstado"=>1);
        $query = $this->db->order_by('CAJA_Nombre')
                          ->where($where)
                          ->select('CAJA_Codigo,CAJA_Nombre,CAJA_tipo,tipCa_codigo')
                          ->from('cji_caja')
                          ->get();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
 	}
 	
 	public function listar_tipoCaja()
 	{
 		$where = array("tipCa_FlagEstado" => '1' );
 		$query = $this->db->order_by('tipCa_codigo')->where($where)->get('cji_tipocaja');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 
 	public function listar_cuenta($compania)
 	{
 		$where = array("EMPRE_Codigo"=>$compania , "CUENT_FlagEstado" => '1' );
 		$query = $this->db->order_by('CUENT_Codigo')->where($where)->get('cji_cuentasempresas');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	
 	public function listar_cuenta_banco($banco){
 		$compania =$this->somevar ['compania'];
 		$where = array("BANP_Codigo"=>$banco , "EMPRE_Codigo"=>$compania, "CUENT_FlagEstado" => '1' );
 		$query = $this->db->order_by('CUENT_Codigo')->where($where)->get('cji_cuentasempresas');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	} 	

 	
 	public function listar_banco_cuenta($banco)
 	{   
 		$where = array("BANC_FlagEstado"=>"1", "BANP_Codigo" => $banco);
 		$query = $this->db->order_by('BANC_Nombre')->where($where)->get('cji_banco');
 		if($query->num_rows() > 0)
 		  {
 			return $query->result();
 		  }
 	}
 	
 	public function obtener_numeroSerie($serie){
 		$query = $this->db->where('CHEK_Codigo',$serie)->get('cji_chekera');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function obtener_chequera($cuenta){
 		$query = $this->db->where('CUENT_Codigo',$cuenta)->get('cji_chekera');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function obtener_chequeraCodigo($chequera){
 		$query = $this->db->where('CHEK_Codigo',$chequera)->get('cji_chekera');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function obtener_cuenta_caja($caja){
  /*  $this->db->select('*');
$this->db->from('cji_caja_cuenta cc');
$this->db->join('cji_caja c','c.CAJA_Codigo=cc.CAJA_Codigo');
$this->db->where('CAJCUENT_FlagEstado', '1'); 
$this->db->where('cc.CAJA_Codigo',$caja);*/
 $query = $this->db->where('CAJA_Codigo',$caja)->get('cji_caja_cuenta');
 //$query = $this->db->get();
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
    public function obtener_cuenta_caja2($caja){
        $this->db->select('*');
        $this->db->from('cji_caja_cuenta cc');
        $this->db->join('cji_caja c','c.CAJA_Codigo=cc.CAJA_Codigo');
        $this->db->join('cji_cuentasempresas e','e.CUENT_Codigo=cc.CUENT_Codigo');
        $this->db->where('CAJCUENT_FlagEstado', '1'); 
        $this->db->where('cc.CAJA_Codigo',$caja);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }	
 	public function obtener_cuenta_chequera($caja){
 		$query = $this->db->where('CAJA_Codigo',$caja)->get('cji_caja_chekera');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function listar_chequera($cuenta)
 	{
 		$where = array("CHEK_FlagEstado"=>"1");
 		$query = $this->db
 					  ->order_by('SERIP_Codigo')
 					  ->where_in("CUENT_Codigo",$cuenta)
 					  ->where($where)
 					  ->get('cji_chekera');
 		if($query->num_rows() > 0)
 		{
 			return $query->result();
 		}
 	}
 	
 	public function listar_moneda_cuenta($cuenta)
 	{
 		$where = array("MONED_FlagEstado"=>"1", "MONED_Codigo" => $cuenta);
 		$query = $this->db->order_by('MONED_Descripcion')->where($where)->get('cji_moneda');
 		if($query->num_rows() > 0)
 		{
 			return $query->result();
 		}
 	}
 	
 	public function obtener_datosCuenta($cuenta){
 		$query = $this->db->where('CUENT_Codigo',$cuenta)->get('cji_cuentasempresas');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	
 	public function obtener_datosCuenta_banco($compania){
 		$query = $this->db->where('EMPRE_Codigo',$compania)->get('cji_cuentasempresas');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function obtener_datosCaja($caja){
 		$query = $this->db->where('CAJA_Codigo',$caja)->get('cji_caja');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	public function obtener_datosTipoCaja($caja){
 		$query = $this->db->where('tipCa_codigo',$caja)->get('cji_tipocaja');
 		if($query->num_rows() > 0){
 			foreach($query->result() as $fila){
 				$data[] = $fila;
 			}
 			return $data;
 		}
 	}
 	
 	
 	

	public function obtener_datosProyecto($proyecto){
        $query = $this->db->where('PROYP_Codigo',$proyecto)->get('cji_proyecto');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function obtener_direccion($proyecto){
    	$query = $this->db->where('PROYP_Codigo',$proyecto)->get('cji_direccion');
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    
    
    public function insertar_datosCaja($nombreCaja,$cboTipCaja,$tipo_caja,$cboResponsable,$observaciones)
    {
        $usuario =$this->somevar ['user'];        
        $data = array(
                    "CAJA_Nombre"        => strtoupper($nombreCaja),
        			"CAJA_Observaciones" => strtoupper($observaciones),
                    "tipCa_codigo"  	 => $cboTipCaja,
                    "CAJA_tipo"      	 => $tipo_caja,
        			"CODIGO_Directorio"  => $cboResponsable,
        			"USUA_Codigo"     	 => $usuario,
        			"CAJA_FlagEstado"    => '1',
                    "CAJA_CodigoUsuario" =>  $usuario
                   );
       $this->db->insert("cji_caja",$data);
       return $this->db->insert_id();
    }
    
    public function modificar_datosCaja($codigo,$nombreCaja,$cboTipCaja,$tipo_caja,$cboResponsable,$observaciones)
    {
    	$usuario =$this->somevar ['user'];
    	$data = array(
    			"CAJA_Codigo"        => $codigo,
    			"CAJA_Nombre"        => strtoupper($nombreCaja),
    			"CAJA_Observaciones" => strtoupper($observaciones),
    			"tipCa_codigo"  	 => $cboTipCaja,
    			"CAJA_tipo"      	 => $tipo_caja,
    			"CODIGO_Directorio"  => $cboResponsable,
    			"USUA_Codigo"     	 => $usuario
    			 
    	);
    	$this->db->where("CAJA_Codigo",$codigo);
    	$this->db->update("cji_caja",$data);
    }
    
    public function insertar_cuenta($filter){
    	$data = array(
    			"CAJA_Codigo"         => $filter -> CAJA_Codigo,
    			"CUENT_Codigo"        => $filter -> CUENT_Codigo,
    			"TIPOING_Codigo" 	  => $filter -> TIPOING_Codigo,
    			"CAJCUENT_LIMITE"     => $filter -> CAJCUENT_LIMITE,
    			"CAJCUENT_FlagEstado" => '1'         
    			);
    	$this->db->insert("cji_caja_cuenta",$data);
    	return $this->db->insert_id();
    }
    
    public function modificar_cuenta($valor ,$filter)
    {
    	$where = array("CAJCUENT_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_caja_cuenta',(array)$filter);
    }
    
    public function eliminar_cuenta($valor)
    {
    	$data  = array("CAJCUENT_FlagEstado"=>'0');
    	$where = array("CAJCUENT_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_caja_cuenta',$data);
    }
    
    public function insertar_chekera($filter){
    	$data = array(
    			"CAJCHEK_Descripcion"  => $filter -> CAJCHEK_Descripcion,
    			"CAJA_Codigo"          => $filter -> CAJA_Codigo,
    			"CHEK_Codigo"          => $filter -> CHEK_Codigo,
    			"CAJCHEK_FlagEstado"   => '1'
    	);
    	$this->db->insert("cji_caja_chekera",$data);
    	return $this->db->insert_id();
    }
    
    public function modificar_chekera($valor ,$filter)
    {
    	$where = array("CAJCHEK_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_caja_chekera',(array)$filter);
    }
    
    public function eliminar_chekera($valor)
    {
    	$data  = array("CAJCHEK_FlagEstado"=>'0');
    	$where = array("CAJCHEK_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_caja_chekera',$data);
    }
    
    
 	public function modificar_datosProyecto($proyecto,$nombreProyecto,$descpProyecto,$cbo_clientes,$fechai,$fechaf)
             {
      $data = array(
                    "PROYC_Nombre"       =>$nombreProyecto,
                    "PROYC_Descripcion"  =>$descpProyecto,
                    "EMPRP_Codigo"       =>$cbo_clientes,
                    "PROYC_FechaInicio"  =>$fechai,
                    "PROYC_FechaFin"     =>$fechaf
                    );
     $this->db->where("PROYP_Codigo",$proyecto);
     $this->db->update("cji_proyecto",$data);
    }

    
   public function eliminar_caja($caja)
    {
        $data  = array("CAJA_FlagEstado"=>'0');
        $where = array("CAJA_Codigo"=>$caja);
        $this->db->where($where);
        $this->db->update('cji_caja',$data);
    }
     public function buscar_proyectos($filter,$number_items='',$offset='')
    {       
       if(isset($filter->PROYC_Nombre) && $filter->PROYC_Nombre!=""){
       $this->db->like('PROYC_Nombre',$filter->PROYC_Nombre);          
       }
        $query = $this->db->order_by('PROYC_Nombre')
                          ->where('PROYC_FlagEstado','1')
                          ->get('cji_proyecto',$number_items='',$offset='');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                    $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function listar_detalle($proyecto)
    {
    	$where = array("PROYP_Codigo"=>$proyecto , "DIRECC_FlagEstado" => '1' );
    	$query = $this->db->order_by('PROYP_Codigo')->where($where)->get('cji_direccion');
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    
    public function eliminar_direccion($valor)
    {
    	$data  = array("DIRECC_FlagEstado"=>'0');
    	$where = array("DIRECC_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_direccion',$data);
    }
    
    public function modificar_direccion($valor ,$filter)
    	{
    	  $where = array("DIRECC_Codigo"=>$valor);
    	  $this->db->where($where);
    	  $this->db->update('cji_direccion',(array)$filter);
    	}
		public function getCajaDetalleCuentaEmpresa($codigo){
		$this->db->select('cc.CUENT_Codigo,ce.CUENT_NumeroEmpresa');
		$this->db->from('cji_caja_cuenta cc');
		$this->db->join('cji_cuentasempresas ce','ce.CUENT_Codigo=cc.CUENT_Codigo');
		$this->db->where('CAJCUENT_FlagEstado', '1');
		$this->db->where('ce.CUENT_Codigo',$codigo);
		         $query = $this->db->get();
		      if ($query->num_rows() > 0) {
		            foreach ($query->result() as $fila) {
		                $data[] = $fila;
		            }
		            return $data;
		        }
		}

public function autocompleteCaja($keyword){
	try {
		$sql = "SELECT * FROM cji_caja where CAJA_Nombre LIKE '%" . $keyword . "%' and CAJA_FlagEstado = 1 ";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}

	} catch (Exception $e) {
		 
	}
}


  	}


  	 
?>