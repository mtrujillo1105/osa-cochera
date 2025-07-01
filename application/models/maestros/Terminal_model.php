<?php
class Terminal_model extends CI_Model
{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user'] = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    
    function listar_terminales(){
    	$query = $this->db->order_by('TERMINAL_Nombre')->where('TERMINAL_FlagEstado','1')->get('cji_terminal');
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    
	public function listar_proyectos($conf="",$j="",$codproy){
		if($codproy == null){
			$where = array("PROYC_FlagEstado"=>1);
			$query = $this->db->order_by('PROYC_Nombre')
					->where($where)
					->select('PROYP_Codigo,PROYC_Nombre,PROYC_Descripcion,DIREP_Codigo')
					->from('cji_proyecto')
					->get();
			if($query->num_rows() > 0){
				foreach($query->result() as $fila){
					$data[] = $fila;
				}
				return $data;
			}
		}else{
		$where = array("PROYC_FlagEstado"=>1);
        $query = $this->db->order_by('PROYC_Nombre')
        				  ->where_in("PROYP_Codigo",$codproy)
        				  ->where($where)
                          ->select('PROYP_Codigo,PROYC_Nombre,PROYC_Descripcion,DIREP_Codigo')
                          ->from('cji_proyecto')
                          ->get();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
         }
        
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
    
    public function obtener_terminal($terminal){
    	$query = $this->db->where('TERMINAL_Codigo',$terminal)->get('cji_terminal');
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    
    public function obtener_direccion_proyecto($direccion){
    	$query = $this->db->where('DIRECC_Codigo',$direccion)->get('cji_direccion');
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    

    
    public function insertar_terminal($filter){
    	$data = array(
    			"TERMINAL_Nombre"       => $filter->TERMINAL_Nombre,
    			"TERMINAL_Modelo"       => $filter->TERMINAL_Modelo,
    			"TERMINAL_Serie" 		=> $filter->TERMINAL_Serie,
    			"TERMINAL_NroLed" 		=> $filter->TERMINAL_NroLed,
    			"PROYP_Codigo"      	=> $filter->PROYP_Codigo,
    			"DIRECC_Codigo"         => $filter->DIRECC_Codigo,
    			"TERMINAL_FlagEstado"   => '1'         
    			);
    	$this->db->insert("cji_terminal",$data);
    	return $this->db->insert_id();
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

    
   public function eliminar_proyecto($proyecto)
    {
        $data  = array("PROYC_FlagEstado"=>'0');
        $where = array("PROYP_Codigo"=>$proyecto);
        $this->db->where($where);
        $this->db->update('cji_proyecto',$data);
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
    
    public function listar_detalle($rol,$direcciones,$proyecto,$total="",$inicio="")
    {
    	if($rol == 5){
    		$where = array("PROYP_Codigo"=>$proyecto , "DIRECC_FlagEstado" => '1' );
    		$query = $this->db->order_by('DIRECC_Codigo')
    							->where_in("DIRECC_Codigo",$direcciones)
    							->where($where)
    							->get('cji_direccion',$total='',$inicio='');
    		if($query->num_rows() > 0){
    			foreach($query->result() as $fila){
    				$data[] = $fila;
    			}
    			return $data;
    		}
    	}else {
    		$where = array("PROYP_Codigo"=>$proyecto , "DIRECC_FlagEstado" => '1' );
    		$query = $this->db->order_by('PROYP_Codigo')->where($where)->get('cji_direccion',$total='',$inicio='');
    		if($query->num_rows() > 0){
    			foreach($query->result() as $fila){
    				$data[] = $fila;
    			}
    			return $data;
    		}
    	}
    	
    }
    
    public function listar_detalle_terminal($terminales,$direccionCodigo,$total="",$inicio="")
    {
    	if($terminales != null){
    		$where = array("DIRECC_Codigo"=>$direccionCodigo , "TERMINAL_FlagEstado" => '1' );
    		$query = $this->db->order_by('TERMINAL_Codigo')
				              ->where_in("TERMINAL_Codigo",$terminales)
    						  ->where($where)
    						  ->get('cji_terminal',$total='',$inicio='');
    		if($query->num_rows() > 0){
    			foreach($query->result() as $fila){
    				$data[] = $fila;
    			}
    			return $data;
    		}
    		
    	}else{
    		$where = array("DIRECC_Codigo"=>$direccionCodigo , "TERMINAL_FlagEstado" => '1' );
    		$query = $this->db->order_by('DIRECC_Codigo')->where($where)->get('cji_terminal',$total='',$inicio='');
    		if($query->num_rows() > 0){
    			foreach($query->result() as $fila){
    				$data[] = $fila;
    			}
    			return $data;
    		}
    	}
    	
    }
    
    public function eliminar_terminal($valor)
    {
    	$data  = array("TERMINAL_FlagEstado"=>'0');
    	$where = array("TERMINAL_Codigo"=>$valor);
    	$this->db->where($where);
    	$this->db->update('cji_terminal',$data);
    }
    
    public function modificar_terminal($valor ,$filter)
    	{
    	  $where = array("TERMINAL_Codigo"=>$valor);
    	  $this->db->where($where);
    	  $this->db->update('cji_terminal',(array)$filter);
    	}
  	 }


  	 
?>