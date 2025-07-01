<?php
class terminal extends CI_Controller{
    public function __construct(){
            parent::Controller();
    $this->load->helper('form');
    $this->load->helper('date');
    $this->load->model('maestros/terminal_model');
    $this->load->model('empresa/directivo_model');
    $this->load->model('maestros/compania_model');
    $this->load->model('maestros/persona_model');
    $this->load->model('empresa/cliente_model');
    $this->load->model('maestros/proyecto_model');
    $this->load->model('seguridad/usuario_model');
    $this->load->library('html');
    $this->load->library('pagination');
    $this->load->library('layout','layout');
    $this->somevar ['compania'] = $this->session->userdata('compania');
    $this->somevar['user'] = $this->session->userdata('user');
    $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    
    public function index()
    {
       $this->layout->view('seguridad/inicio');	
    }
    
    
    public function terminales($j=0){
        $data['nombres']       = "";
        $data['descripcion']   = "";
        $data['encargado']     = "";
        $data['titulo_tabla']  = "LISTADO DE PROYECTOS";
        $data['action'] 	   = base_url()."index.php/maestros/terminal/buscar_proyectos";
        $conf['base_url'] 	   = site_url('maestros/terminal/buscar_proyectos/');
        
        $conf['per_page'] 	   = 50;
        $conf['num_links']     = 3;
        $conf['next_link'] 	   = "&gt;";
        $conf['prev_link']     = "&lt;";
        $conf['first_link']    = "&lt;&lt;";
        $conf['last_link']     = "&gt;&gt;";
        $conf['uri_segment']   = 4;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $rol = $this->session->userdata('rol');
        $user = $this->session->userdata('user');
        if($rol == 5){
        	$usu_term = $this->usuario_model->obtener_usuario_terminal($user);
        	$objetos = array();
        	if(count($usu_term)>0){
        		foreach ($usu_term as $indice => $valor){
        			$codTerm = $valor->TERMINAL_Codigo;
        			if($codTerm != null){
        				$terminal = $this->terminal_model->obtener_terminal($codTerm);
        				if(count($terminal)>0){
        					foreach ($terminal as $indice => $valores){
        						$codDirec = $valores->DIRECC_Codigo;
        						if($codDirec != null){
        							$direccion = $this->terminal_model->obtener_direccion_proyecto($codDirec);
        							foreach ($direccion as $indice => $var){
        								$codproy = $var->PROYP_Codigo;
        								if($codproy != null){
        									$objetos[]=$codproy;
        								}
        							}
        						}
        					}
        				}
        			}
        		}
        		
        		$listado_proyectos = $this->terminal_model->listar_proyectos($conf['per_page'],$j,$objetos);
        		$data['registros'] 	   = count($listado_proyectos);
        		$conf['total_rows']    = $data['registros'];        		
        		
        	}else{
        		$objetos = null;
        		$listado_proyectos = $this->terminal_model->listar_proyectos($conf['per_page'],$j,$objetos);
        		$data['registros'] 	   =  count($listado_proyectos);
        		$conf['total_rows']    = $data['registros'];
        	}
        	
        }else{
        	$objetos = null;
        	$listado_proyectos = $this->terminal_model->listar_proyectos($conf['per_page'],$j,$objetos);
        	$data['registros'] 	   =  count($listado_proyectos);
        	$conf['total_rows']    = $data['registros'];
        }
        $item        = $j+1;
        $lista       = array();
        if(count($listado_proyectos)>0){
             foreach($listado_proyectos as $indice=>$valor){
             	$codigo        = $valor->PROYP_Codigo;
                $nombre        = $valor->PROYC_Nombre;
                $descripcion   = $valor->PROYC_Descripcion;
                $directivo     = $valor->DIREP_Codigo;
              if($directivo!=0){
                 $temp          = $this->directivo_model-> obtener_directivo($directivo);
                 $persona       = $temp[0]->PERSP_Codigo;
                 $temp2         = $this->persona_model->obtener_datosPersona($persona);
                 $encargado     = $temp2[0]->PERSC_Nombre;
              }else{
                 $encargado="";
              }               
                 $ver            = "<a href='javascript:;' onclick='ver_direccion(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $lista[]        = array($item,$nombre,$descripcion,$encargado,$ver);
                 $item++;
               }
           }
        $data['lista'] = $lista;
        $this->layout->view("maestros/terminal_index.php",$data);
    }


	public function ver_direccion($proyecto, $j=0)
	  {		
		  /* INICIO DE METODOS */
	  	$data['titulo_tabla']  = "LISTADO DE DIRECCIONES DEL PROYECTO";
	  	$conf['per_page'] 	   =  1;
	  	$conf['num_links']     =  3;
	  	$conf['next_link'] 	   = "&gt;";
	  	$conf['prev_link']     = "&lt;";
	  	$conf['first_link']    = "&lt;&lt;";
	  	$conf['last_link']     = "&gt;&gt;";
	  	$conf['uri_segment']   = 4;
	  	$this->pagination->initialize($conf);
	  	$data['paginacion']   = $this->pagination->create_links();
	  	
	  	
	  	$rol = $this->session->userdata('rol');
	  	$user = $this->session->userdata('user');
	  	
	  	if($rol == 5){
	  		$usu_term = $this->usuario_model->obtener_usuario_terminal($user);
	  		if(count($usu_term)>0){
	  			foreach ($usu_term as $indice => $valor){
	  				$codTerm = $valor->TERMINAL_Codigo;
	  				if($codTerm != null){
	  				  $terminal = $this->terminal_model->obtener_terminal($codTerm);
	  				  if(count($terminal)>0){
	  					foreach ($terminal as $indice => $valores){
	  						$direccion = $valores->DIRECC_Codigo;
	  						if($direccion != null){
	  							$direcciones[]=$direccion;		
	  						}
	  						
	  					}
	  				}
	  			}
	  		  }
	  		  
	  		  $listado_proyectos = $this->terminal_model->listar_detalle($rol,$direcciones,$proyecto,$conf['per_page'],$j);
	  		  $data['registros'] 	   =  count($listado_proyectos);
	  		  $conf['total_rows']    =  $data['registros'];
	  		}else{
	  			$direcciones = null;
	  			$listado_proyectos = $this->terminal_model->listar_detalle($rol,$direcciones,$proyecto,$conf['per_page'],$j);
	  			$data['registros'] 	   =  count($listado_proyectos);
	  			$conf['total_rows']    =  $data['registros'];
	  		}
	  	}else{
	  		$direcciones = null;
	  		$listado_proyectos = $this->terminal_model->listar_detalle($rol,$direcciones,$proyecto,$conf['per_page'],$j);
	  		$data['registros'] 	   =  count($listado_proyectos);
	  		$conf['total_rows']    =  $data['registros'];
	  	}
	  	$item        = $j+1;
	  	$lista           = array();
	  	if(count($listado_proyectos)>0){
	  		foreach($listado_proyectos as $indice=>$valor){
	  			$direccionCodigo 	  = $valor->DIRECC_Codigo;
	  			$descripcionDireccion = $valor->DIRECC_Descrip;
	  			$referenciaDireccion  = $valor->DIRECC_Referen;
	  			$nuevo         	  = "<a href='javascript:;' onclick='nuevo_terminal(".$direccionCodigo.")'><img src='".base_url()."images/icono_nuevo.png' width='16' height='16' border='0' title='Agregar Terminal'></a>";
	  			$lista[]        	  = array($indice,$descripcionDireccion,$referenciaDireccion,$nuevo);
	  		}
	  	}
	  	$data['lista'] = $lista;
	  	$this->load->view("maestros/terminal_ver.php",$data);
	  	
	  	  /* FIN DE METODOS */

	  }
	  
	public function nuevo_terminal($direccion){

	  	$data['modo']	  = "modificar";
	  	$objeto = new stdClass();
	  	$objeto->id       = "";
	  	$datos_direccion  = $this->terminal_model->obtener_direccion_proyecto($direccion);
	  	$direccionCodigo 	  = $datos_direccion[0]->DIRECC_Codigo;
	  	$descripcionDireccion = $datos_direccion[0]->DIRECC_Descrip;
		$proyecto			  = $datos_direccion[0]->PROYP_Codigo;
	  		  	
	  	$datos_proyecto   = $this->terminal_model->obtener_datosProyecto($proyecto);
	  	$nombreProyecto   = $datos_proyecto[0]->PROYC_Nombre;
	  
	  	$data['url_action'] 		   = base_url() . "index.php/maestros/terminal/insertar_terminal";
	  	$data['descripcionDireccion']  = $descripcionDireccion;
	  	$data['direccionCodigo']  	   = $direccionCodigo;
	  	$data['nombreProyecto']   	   = $nombreProyecto;
	  	$data['proyecto']   	   	   = $proyecto;
	  	$oculto               	  	   = form_hidden(array('accion'=>"index.php/maestros/terminal/insertar_terminal",'codigo'=>$direccion,'modo'=>"modificar",'base_url'=>base_url()));
	  	$data['oculto']           	   = $oculto;	  	
	  	$data['nombreTerminal'] 	   = "";
	  	$data['modeloTerminal'] 	   = "";
	  	$data['numeroSerie'] 		   = "";
	  	$data['numeroLed'] 			   = "";
	  	
	  	$rol = $this->session->userdata('rol');
	  	$user = $this->session->userdata('user');
	  	if($rol == 5){
	  		$usu_term = $this->usuario_model->obtener_usuario_terminal($user);
	  		if(count($usu_term)>0){
	  			foreach ($usu_term as $indice => $valor){
	  				$terminalCodigo = $valor->TERMINAL_Codigo;
	  				if($terminalCodigo != null){
	  					$terminales []= $terminalCodigo;
	  				}
	  			}
	  			$detalle_terminal = $this->listar_detalle_terminal($terminales,$direccion);
	  		}else{
	  			$terminales = null;
	  			$detalle_terminal = $this->listar_detalle_terminal($terminales,$direccion);
	  		}
	  	}else{
	  		$terminales = null;
	  		$detalle_terminal = $this->listar_detalle_terminal($terminales,$direccion);
	  	}	  	  	
	  	$data['detalle_terminal']      = $detalle_terminal;
	  	$data['datos'] 				   = $objeto;
	  	$data['titulo']  			   = "REGISTRAR TERMINAL ";
	  	$this->load->view("maestros/terminal_nuevo",$data);
	  }
  	
  	public function insertar_terminal(){
		
  		$proyecto			= $this->input->post('proyecto');
  		$direccionCodigo	= $this->input->post('direccionCodigo');

  		$terminalCodigo     = $this->input->post('terminalCodigo');
  		$terminalNombre     = $this->input->post('terminalNombre');
  		$terminalModelo     = $this->input->post('terminalModelo');
  		$terminalSerie      = $this->input->post('terminalSerie');
  		$terminalLed        = $this->input->post('terminalLed');
        $teraccion 		    = $this->input->post('teraccion');
		if (is_array($terminalCodigo) > 0) {
			foreach ($terminalCodigo as $indice => $valor) {
				$detalle_accion = $teraccion[$indice];
				$filter = new stdClass();
				$filter->TERMINAL_Nombre   = $terminalNombre[$indice];
				$filter->TERMINAL_Modelo   = $terminalModelo[$indice];
				$filter->TERMINAL_Serie    = $terminalSerie[$indice];
				$filter->TERMINAL_NroLed   = $terminalLed[$indice];
				$filter->PROYP_Codigo 	   = $proyecto[$indice];
				$filter->DIRECC_Codigo 	   = $direccionCodigo[$indice];
				
				if ($detalle_accion == 'n') {
					$this->terminal_model->insertar_terminal($filter);
				} elseif ($detalle_accion == 'm') {
					$this->terminal_model->modificar_terminal($valor, $filter);
				} elseif ($detalle_accion == 'e') {
					$this->terminal_model->eliminar_terminal($valor);
				}
			}
		}
		
		$this->ver_direccion($proyecto[0]);
	}


   	public function buscar_proyectos($j='0'){
        $filter = new stdClass();
        $filter->PROYC_Nombre = $this->input->post('nombres');
        $data['nombres']      = $filter->PROYC_Nombre;
        $data['titulo_tabla']    = "RESULTADO DE BÚSQUEDA DE PROYECTOS";
        $data['registros']  = count($this->proyecto_model->buscar_proyectos($filter));
        $data['action'] = base_url()."index.php/maestros/terminal/buscar_proyectos";
        $conf['base_url'] = site_url('maestros/terminal/buscar_proyectos/');
        $conf['total_rows'] = $data['registros'];
        $conf['per_page']   = 10;
        $conf['num_links']  = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link']  = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $listado_proyectos = $this->terminal_model->buscar_proyectos($filter, $conf['per_page'],$j);
        $item            = $j+1;
        $lista           = array();
                    if(count($listado_proyectos)>0){
                            foreach($listado_proyectos as $indice=>$valor){
                                    $proyecto       = $valor->PROYP_Codigo;
                                    $nombres        = $valor->PROYC_Nombre;
                                    $descripcion    = $valor->PROYC_Descripcion;
                                    $encargado      = $valor->DIREP_Codigo;                                  
                                    $ver            = "<a href='#' onclick='ver_proyecto(".$proyecto.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                                    $lista[]        = array($item,$nombres,$descripcion,$encargado,$ver);
                                    $item++;
                            }
                    }
        $data['lista'] = $lista;
        $this->layout->view("maestros/terminal_index",$data);

    }

    
    public function eliminar_proyecto(){
		$proyecto = $this->input->post('proyecto');
		$this->terminal_model->eliminar_proyecto($proyecto);
	}


	public function JSON_listar_proyecto($cliente){

    $lista_todos=array();
     $datoCliente = $this->cliente_model->obtener($cliente);


    $listado_proyectos = $this->proyecto_model->listar_proyetos_cliente($datoCliente ->empresa);
      foreach ($listado_proyectos as $key => $datos_proyecto) {

            $objeto = new stdClass();
            $objeto->PROYP_Codigo = $datos_proyecto->PROYP_Codigo;;
            $objeto->nombre = $datos_proyecto->PROYC_Nombre;;
            $objeto->descripcion = $datos_proyecto->PROYC_Descripcion;;
            $lista_detalles[] = $objeto;
            
        }
         $resultado[] = array('Tipo' => '1', 'Titulo' => 'Los establecimientos de mi cliente');
            $resultado = json_encode($lista_detalles);

        echo  $resultado;
	}
	

	public function listar_detalle_terminal($terminales, $direccionCodigo, $total="",$inicio=""){
	$detalle = $this->terminal_model->listar_detalle_terminal($terminales, $direccionCodigo,$total,$inicio);
	
	$lista_detalles = array();
	if (count($detalle) > 0) {
		foreach ($detalle as $indice => $valor) {
			$proyecto			= $valor->PROYP_Codigo;
			$direccionCodigo	= $valor->DIRECC_Codigo;	
			$terminal           = $valor->TERMINAL_Codigo;
			$nombreTerminal     = $valor->TERMINAL_Nombre;
			$modeloTerminal     = $valor->TERMINAL_Modelo;
			$numeroSerie        = $valor->TERMINAL_Serie;
			$numeroLed          = $valor->TERMINAL_NroLed;
			$objeto = new stdClass();
			$objeto->PROYP_Codigo= $proyecto;
			$objeto->DIRECC_Codigo = $direccionCodigo;
			$objeto->TERMINAL_Codigo = $terminal;
			$objeto ->TERMINAL_Nombre = $nombreTerminal;		
			$objeto->TERMINAL_Modelo = $modeloTerminal;
			$objeto->TERMINAL_Serie = $numeroSerie;
			$objeto->TERMINAL_NroLed = $numeroLed;
			$lista_detalles[] = $objeto;
		}
	}
	return $lista_detalles;
}

	public function JSON_listar_terminal(){
		echo json_encode($this->terminal_model->listar_terminales());
	}

}       
?>