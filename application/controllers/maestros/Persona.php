<?php
class Persona extends CI_Controller{
	public function __construct(){

		parent::Controller();
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/ubigeo_model');
        $this->load->model('maestros/tipodocumento_model');
        $this->load->model('maestros/estadocivil_model');
        $this->load->model('maestros/nacionalidad_model');
        $this->load->model('empresa/directivo_model');
        $this->load->helper('json');
        $this->load->library('html');
        $this->load->library('table');
        $this->load->library('layout','layout');
        $this->load->library('pagination');

	}
	public function index(){
		$this->layout->view('seguridad/inicio');	
	}
        public function personas($j=0){
            $data['numdoc']       = "";
            $data['nombre']    = "";
            $data['telefono']  = "";
            $data['titulo_tabla']    = "RELACIÓN DE PERSONAS";
            $data['registros']  = count($this->persona_model->listar_personas());
            $data['action']          = base_url()."index.php/maestros/persona/buscar_personas";
            $conf['base_url']   = site_url('maestros/persona/personas/');
            $conf['total_rows'] = $data['registros'];
            $conf['per_page']   = 50;
            $conf['num_links']  = 3;
            $conf['next_link'] = "&gt;";
            $conf['prev_link'] = "&lt;";
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link']  = "&gt;&gt;";
            $conf['uri_segment'] = 4;
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $listado_personas = $this->persona_model->listar_personas($conf['per_page'],$j);
            $item            = $j+1;
            $lista           = array();
		if(count($listado_personas)>0){
				foreach($listado_personas as $indice=>$valor){
					$codigo   = $valor->PERSP_Codigo;
					$nombres   = $valor->PERSC_Nombre.'   '.$valor->PERSC_ApellidoPaterno.'  '.$valor->PERSC_ApellidoMaterno;
                                        $apellido = $valor->PERSC_ApellidoPaterno;
                                        $dni      = $valor->PERSC_NumeroDocIdentidad;
                    $direccion=$valor->PERSC_Direccion;
					$telefono       = $valor->PERSC_Telefono;
					//$movil          = $valor->PERSC_Movil;
					$editar         = "<a href='javascript:;' onclick='editar_persona(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
					$ver            = "<a href='javascript:;' onclick='ver_persona(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
					$eliminar       = "<a href='javascript:;' onclick='eliminar_persona(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
					$lista[]        = array($item,$dni,$nombres,$direccion,
                        $telefono,
                        //$movil,
                        $editar,$ver,$eliminar);
					$item++;
				}
			}
          $data['lista'] = $lista;
          $this->layout->view("maestros/persona_index",$data);
	}

        
   public function nuevo_persona(){
   $data["indetif"]="";           
		$data['cbo_dpto']         = $this->seleccionar_departamento('15');
		$data['cbo_prov']         = $this->seleccionar_provincia('15','01');
		$data['cbo_dist']         = $this->seleccionar_distritos('15','01');
		$data['cbo_estadoCivil']  = $this->seleccionar_estadoCivil('');
		$data['cbo_nacionalidad'] = $this->seleccionar_nacionalidad('193');
		$data['cbo_nacimiento']   = $this->seleccionar_distritos('15','01','01');
                $data['nombres']	      = "";
		$data['paterno']	      = "";
		$data['materno']	      = "";
		$data['numero_documento']     = "";
		$data['ruc']		      = "";
		$data['sexo']		      = "";
		$data['tipo_documento']	      = $this->seleccionar_tipodocumento('1');
		$data['tipo_persona']	      = "1";
		$data['id']		      = "";
		$data['modo']		      = "insertar";
               
		$objeto = new stdClass();
		$objeto->id      = "";
                $objeto->tipo    = "";
                $objeto->ruc     = "";
		$objeto->nombre  = "";
		$objeto->telefono = "";
		$objeto->movil    = "";
		$objeto->fax      = "";
		$objeto->web      = "";
		$objeto->email    = "";
		$objeto->direccion="";
                $objeto->ctactesoles    = "";
                $objeto->ctactedolares  = "";
		$data['datos'] = $objeto;
		$data['titulo'] = "REGISTRAR PERSONA";
		$data['listado_personas']  = array();
		
		$data['cboNacimiento'] = "000000";
		$data['cboNacimientovalue'] = "";
		$this->load->view("maestros/persona_nuevo",$data);
	}
   public function insertar_persona(){           
        $nombres         = $this->input->post('nombres');
        $paterno         = $this->input->post('paterno');
	$materno         = $this->input->post('materno');
        $nacionalidad     = $this->input->post('cboNacionalidad');
        $numero_documento  = $this->input->post('numero_documento');
        $ubigeo_nacimiento = $this->input->post('cboNacimiento')==''?'000000':$this->input->post('cboNacimiento');
        $tipo_documento    = $this->input->post('tipo_documento');
        $sexo       = $this->input->post('cboSexo');
        $estado_civil     = $this->input->post('cboEstadoCivil');
        $ruc_persona      = $this->input->post('ruc_persona');
        $departamento    = $this->input->post('cboDepartamento');
        $provincia       = $this->input->post('cboProvincia');
	$distrito        = $this->input->post('cboDistrito');
        $direccion       = $this->input->post('direccion');
        $telefono        = $this->input->post('telefono');
        $movil           = $this->input->post('movil');
        $fax             = $this->input->post('fax');
        $email           = $this->input->post('email');
        $web             = $this->input->post('web');
        $ctactesoles     = $this->input->post('ctactesoles');
        $ctactedolares   = $this->input->post('ctactedolares');
        $ubigeo_domicilio = $departamento.$provincia.$distrito;
        if($arrayDpto!='' && $arrayProv!='' && $arrayDist!=''){
                $ubigeo_sucursal  = $this->html->array_ubigeo($arrayDpto,$arrayProv,$arrayDist);
        }
     $this->persona_model->insertar_datosPersona($ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$domicilio,$sexo,$fax,$web,$ctactesoles,$ctactedolares);
    }
 
   public function obtener_persona($persona)
   {
		$datos_persona = $this->persona_model->obtener_datosPersona($persona);
		echo json_encode($datos_persona);
   }		
	
   public function buscar_personas($j='0'){
        $filter = new stdClass();
        $filter->PERSC_NumeroDocIdentidad = $this->input->post('txtNumDoc');;
        $filter->nombre = $this->input->post('txtNombre');
        $filter->PERSC_Telefono = $this->input->post('txtTelefono');

        $data['numdoc']    = $filter->PERSC_NumeroDocIdentidad;
        $data['nombre']    = $filter->nombre;
        $data['telefono']  = $filter->PERSC_Telefono;
        $data['titulo_tabla']    = "RESULTADO DE BÚSQUEDA DE PERSONAS";

        $data['registros']  = count($this->persona_model->buscar_personas($filter));
        $data['action'] = base_url()."index.php/maestros/persona/buscar_personas";
        $conf['base_url'] = site_url('maestros/persona/buscar_personas/');
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
        $listado_personas = $this->persona_model->buscar_personas($filter, $conf['per_page'],$j);
        $item            = $j+1;
        $lista           = array();
                    if(count($listado_personas)>0){
                            foreach($listado_personas as $indice=>$valor){
                                    $persona   = $valor->PERSP_Codigo;
                                    $ruc            = $valor->PERSC_NumeroDocIdentidad;
                                    $nombres   = $valor->PERSC_Nombre;
                                    $direccion =$valor->PERSC_Direccion;
                                    $telefono       = $valor->PERSC_Telefono;
                                    //$movil          = $valor->PERSC_Movil;
                                    $editar         = "<a href='#' onclick='editar_persona(".$persona.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                                    $ver            = "<a href='#' onclick='ver_persona(".$persona.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                                    $eliminar       = "<a href='#' onclick='eliminar_persona(".$persona.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                                    $lista[]        = array($item,$ruc,$nombres,$direccion,$telefono,
                                        //$movil,
                                        $editar,$ver,$eliminar);
                                    $item++;
                            }
                    }
        $data['lista'] = $lista;
        $this->layout->view("maestros/persona_index",$data);

    }
   public function persona_ventana_mostrar($persona, $directivo=''){
            if($directivo!='' && $directivo!='0'){
                $lista_directivo=$this->directivo_model->obtener_directivo($directivo);
                $persona=$lista_directivo[0]->PERSP_Codigo;
            }
            $datos                = $this->persona_model->obtener_datosPersona($persona);
            $tipo_doc             = $datos[0]->PERSC_TipoDocIdentidad;
            $estado_civil         = $datos[0]->ESTCP_EstadoCivil;
            $nacionalidad         = $datos[0]->NACP_Nacionalidad;
            $nacimiento           = $datos[0]->UBIGP_LugarNacimiento;
            $sexo                 = $datos[0]->PERSC_Sexo;
            $ubigeo_domicilio     = $datos[0]->UBIGP_Domicilio;
            $datos_nacionalidad   = $this->nacionalidad_model->obtener_nacionalidad($nacionalidad);
            $datos_nacimiento     = $this->ubigeo_model->obtener_ubigeo($nacimiento);
            
            $datos_doc            = $this->tipodocumento_model->obtener_tipoDocumento($tipo_doc);
            $datos_estado_civil   = $this->estadocivil_model->obtener_estadoCivil($estado_civil);
            $data['nacionalidad'] ="";
            if(count($datos_nacionalidad)>0){
            	$data['nacionalidad'] = $datos_nacionalidad[0]->NACC_Descripcion;
            }
            $data['nacimiento']="";
            if(count($datos_nacimiento)>0){
            	$data['nacimiento']   = $datos_nacimiento[0]->UBIGC_Descripcion;
            }
            $data['tipo_doc']     = $datos_doc[0]->TIPOCC_Inciales;
            $data['estado_civil'] = $datos_estado_civil[0]->ESTCC_Descripcion;
            $data['sexo']         = $sexo==0?'MASCULINO':'FEMENINO';
            $data['telefono']     = $datos[0]->PERSC_Telefono;
            $data['movil']        = $datos[0]->PERSC_Movil;
            $data['fax']          = $datos[0]->PERSC_Fax;
            $data['email']        = $datos[0]->PERSC_Email;
            $data['web']          = $datos[0]->PERSC_Web;
            $data['direccion']    = $datos[0]->PERSC_Direccion;
            $data['dpto']         = '';
            $data['prov']         = '';
            $data['dist']         = '';
            if($ubigeo_domicilio!='' && $ubigeo_domicilio!='000000'){
                $datos_ubigeoDom_dpto = $this->ubigeo_model->obtener_ubigeo_dpto($ubigeo_domicilio);
                $datos_ubigeoDom_prov = $this->ubigeo_model->obtener_ubigeo_prov($ubigeo_domicilio);
                $datos_ubigeoDom_dist = $this->ubigeo_model->obtener_ubigeo_dist($ubigeo_domicilio);
                $data['dpto']         = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
                $data['prov']         = $datos_ubigeoDom_prov[0]->UBIGC_Descripcion;
                $data['dist']         = $datos_ubigeoDom_dist[0]->UBIGC_Descripcion;
            }
            
        $data['datos']  = $datos;
        $data['titulo'] = "VER PERSONA";
        
       $this->load->view('ventas/persona_ventana_mostrar', $data);
   }
   public function editar_persona($persona){  
   $data["indetif"]="P";//Para indentificar si es persona o empresa                    
                        //$persona         = $persona;
                        $data['modo']	 = "modificar";
                       // $data['id']	  = $persona;
                        $data['id']	  = $this->input->post('id');
			$datos_persona             = $this->persona_model->obtener_datosPersona($persona);
                        $ubigeo_domicilio          = $datos_persona[0]->UBIGP_Domicilio;
			$ubigeo_nacimiento         = $datos_persona[0]->UBIGP_LugarNacimiento;
			$nacionalidad			   = $datos_persona[0]->NACP_Nacionalidad;
			$estado_civil			   = $datos_persona[0]->ESTCP_EstadoCivil;
			$dpto_domicilio            = substr($ubigeo_domicilio,0,2);
			$prov_domicilio            = substr($ubigeo_domicilio,2,2);
			$dist_domicilio            = substr($ubigeo_domicilio,4,2);
			$dpto_nacimiento           = substr($ubigeo_nacimiento,0,2);
			$prov_nacimiento           = substr($ubigeo_nacimiento,2,2);
			$dist_nacimiento           = substr($ubigeo_nacimiento,4,2);
                        $data['nombres']           = $datos_persona[0]->PERSC_Nombre;
			$data['paterno']           = $datos_persona[0]->PERSC_ApellidoPaterno;
			$data['materno']           = $datos_persona[0]->PERSC_ApellidoMaterno;
			$data['tipo_documento']    = $this->seleccionar_tipodocumento($datos_persona[0]->PERSC_TipoDocIdentidad);
			$data['numero_documento']  = $datos_persona[0]->PERSC_NumeroDocIdentidad;
                        $data['tipo_persona']	      = "1";
			$data['ruc']               = $datos_persona[0]->PERSC_Ruc;
			$data['sexo']		   = $datos_persona[0]->PERSC_Sexo;
			$data['cbo_estadoCivil']   = $this->seleccionar_estadoCivil($estado_civil);
			$data['cbo_nacionalidad']  = $this->seleccionar_nacionalidad($nacionalidad);
			$data['cboNacimiento']     = $ubigeo_nacimiento;
			$nombre_persona            = $datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno." ".$datos_persona[0]->PERSC_Nombre;
			$datos_nacimiento          = $this->ubigeo_model->obtener_ubigeo($ubigeo_nacimiento);
			$data['cboNacimientovalue'] = $ubigeo_nacimiento=='000000'?'':$datos_nacimiento[0]->UBIGC_Descripcion;
			$data['cbo_dpto']         = $this->seleccionar_departamento($dpto_domicilio);
			$data['cbo_prov']         = $this->seleccionar_provincia($dpto_domicilio,$prov_domicilio);
			$data['cbo_dist']         = $this->seleccionar_distritos($dpto_domicilio,$prov_domicilio,$dist_domicilio);
			$data['direccion']	  = $datos_persona[0]->PERSC_Direccion;
                        /*Mejorar esto*/
                        $objeto            = new stdClass();
                        $objeto->id        = $datos_persona[0]->PERSP_Codigo;
                        $objeto->persona   = $datos_persona[0]->PERSP_Codigo;
                        $objeto->empresa   = 0;
                        $objeto->nombre    = $datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno." ".$datos_persona[0]->PERSC_Nombre;
                        $objeto->ruc       = $datos_persona[0]->PERSC_Ruc;
                        $objeto->telefono  = $datos_persona[0]->PERSC_Telefono;
                        $objeto->fax       = $datos_persona[0]->PERSC_Fax;
                        $objeto->movil     = $datos_persona[0]->PERSC_Movil;
                        $objeto->web       = $datos_persona[0]->PERSC_Web;
                        $objeto->direccion = $datos_persona[0]->PERSC_Direccion;
                        $objeto->email     = $datos_persona[0]->PERSC_Email;
                        $objeto->dni       = $datos_persona[0]->PERSC_NumeroDocIdentidad;
                        $objeto->ctactesoles   = $datos_persona[0]->PERSC_CtaCteSoles;
                        $objeto->ctactedolares = $datos_persona[0]->PERSC_CtaCteDolares;
                        $objeto->tipo      = "0";
                        $data['datos']    = $objeto;
                        $data['titulo']  = "EDITAR PERSONA ::: ".$nombre_persona;
	   $this->load->view("maestros/persona_nuevo",$data);
	}

   public function modificar_persona(){
		$persona           = $this->input->post('persona');
		$datos             =  $this->persona_model->obtener_datosPersona($persona);
		//$empresa         = $datos[0]->EMPRP_Codigo;
		$persona           = $datos[0]->PERSP_Codigo;
		//$tipo_persona      = $datos[0]->PROVC_TipoPersona;
		$tipocodigo        = $this->input->post('cboTipoCodigo');
                $ruc               = $this->input->post('ruc');
		$razon_social      = $this->input->post('razon_social');
		$telefono          = $this->input->post('telefono');
		$movil             = $this->input->post('movil');
		$fax               = $this->input->post('fax');
		$email             = $this->input->post('email');
		$web               = $this->input->post('web');
		$ubigeo_nacimiento = $this->input->post('cboNacimiento');
		$ubigeo_domicilio  = $this->input->post('cboDepartamento').$this->input->post('cboProvincia').$this->input->post('cboDistrito');;
		$domicilio         = $this->input->post('direccion');
		$estado_civil      = $this->input->post('cboEstadoCivil');
		$nacionalidad      = $this->input->post('cboNacionalidad');
		$nombres           = $this->input->post('nombres');
		$paterno           = $this->input->post('paterno');
		$materno           = $this->input->post('materno');
		$ruc_persona       = $this->input->post('ruc_persona');
		$tipo_documento    = $this->input->post('tipo_documento');
		$numero_documento  = $this->input->post('numero_documento');
		$direccion         = $this->input->post('direccion');
		$sexo              = $this->input->post('cboSexo');
                $ctactesoles       = $this->input->post('ctactesoles');
                $ctactedolares     = $this->input->post('ctactedolares');
		$this->persona_model->modificar_datosPersona($persona,$ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc_persona,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$domicilio,$sexo,$fax,$web,$ctactesoles,$ctactedolares);
  }
   public function busqueda_personas()
	{
		$ruc      = $this->input->post('ruc');
		$nombre  = $this->input->post('nombre');
		$telefono = $this->input->post('telefono');
                $filter=new stdClass();
                $filter->nombre=$nombre;
		$data['n']                  = $this->input->post('n');
		$data['resultado_personas'] = $this->persona_model->buscar_personas($filter);
		$this->load->view('compras/busqueda_personas',$data);
	}
   public function JSON_busca_persona_xdoc($tipo, $numero){
            $datos_persona = $this->persona_model->busca_xnumeroDoc($tipo, $numero);  //Esta funcion me devuelde el registro de la empresa
            
            $resultado = '[]';
            if(count($datos_persona)>0){
                $dpto_domicilio = "15";
                $prov_domicilio = "01";
                $dist_domicilio = "00";				
                $ubigeo_domicilio = $datos_persona[0]->UBIGP_Domicilio;
                if($ubigeo_domicilio!='000000' && $ubigeo_domicilio!=''){
                    $dpto_domicilio = substr($ubigeo_domicilio,0,2);
                    $prov_domicilio = substr($ubigeo_domicilio,2,2);
                    $dist_domicilio = substr($ubigeo_domicilio,4,2);	
                }
                $ubig_naci = $datos_persona[0]->UBIGP_LugarNacimiento;
                $ubignom = '';
                if($ubig_naci!='000000' && $ubig_naci!=''){
                    $temp =  $this->ubigeo_model->obtener_ubigeo($ubig_naci);
                    if(count($temp)>0)
                        $ubignom=$temp[0]->UBIGC_Descripcion;
                }
                
                $resultado   = '[{"codigo":"'.$datos_persona[0]->PERSP_Codigo.
                                '","nombre":"'.$datos_persona[0]->PERSC_Nombre.
                                '","apepat":"'.$datos_persona[0]->PERSC_ApellidoPaterno.
                                '","apemat":"'.$datos_persona[0]->PERSC_ApellidoMaterno.
                                '","ubignom":"'.$ubignom.
                                '","ubigcod":"'.$datos_persona[0]->UBIGP_LugarNacimiento.
                                '","sexo":"'.$datos_persona[0]->PERSC_Sexo.
                                '","estadocivil":"'.$datos_persona[0]->ESTCP_EstadoCivil.
                                '","nacionalidad":"'.$datos_persona[0]->NACP_Nacionalidad.
                                '","ruc":"'.$datos_persona[0]->PERSC_Ruc.
                                '","departamento":"'.$dpto_domicilio.
                                '","provincia":"'.$prov_domicilio.
                                '","distrito":"'.$dist_domicilio.
                                '","direccion":"'.$datos_persona[0]->PERSC_Direccion.
                                '","telefono":"'.$datos_persona[0]->PERSC_Telefono.
                                '","movil":"'.$datos_persona[0]->PERSC_Movil.
                                '","fax":"'.$datos_persona[0]->PERSC_Fax.
                                '","correo":"'.$datos_persona[0]->PERSC_Email.
                                '","paginaweb":"'.$datos_persona[0]->PERSC_Web.
                                '","ctactesoles":"'.$datos_persona[0]->PERSC_CtaCteSoles.
                                '","ctactedolares":"'.$datos_persona[0]->PERSC_CtaCteDolares.'"}]';
            }
            echo $resultado;
        }

   public function eliminar_persona(){
            $persona = $this->input->post('persona');

            $this->persona_model->eliminar_persona($persona);
    }
   public function seleccionar_estadoCivil($indSel){
            $array_dist = $this->estadocivil_model->listar_estadoCivil();
            $arreglo = array();
            foreach($array_dist as $indice=>$valor){
                    $indice1   = $valor->ESTCP_Codigo;
                    $valor1    = $valor->ESTCC_Descripcion;
                    $arreglo[$indice1] = $valor1;
            }
            $resultado = $this->html->optionHTML($arreglo,$indSel,array('0','::Seleccione::'));
            return $resultado;
    }
   public function seleccionar_nacionalidad($indSel=''){
            $array_dist = $this->nacionalidad_model->listar_nacionalidad();
            $arreglo = array();
            foreach($array_dist as $indice=>$valor){
                    $indice1   = $valor->NACP_Codigo;
                    $valor1    = $valor->NACC_Descripcion;
                    $arreglo[$indice1] = $valor1;
            }
            $resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
            return $resultado;
    }
    /*public function insertar_areaEmpresa($nombre_area){
            $this->empresa_model->insertar_areaEmpresa($area,$empresa,$descripcion);

    }*/
   public function seleccionar_departamento($indDefault=''){
            $array_dpto = $this->ubigeo_model->listar_departamentos();
            $arreglo = array();
            if(count($array_dpto)>0){
                    foreach($array_dpto as $indice=>$valor){
                            $indice1   = $valor->UBIGC_CodDpto;
                            $valor1    = $valor->UBIGC_DescripcionDpto;
                            $arreglo[$indice1] = $valor1;
                    }
            }
            $resultado = $this->html->optionHTML($arreglo,$indDefault,array('00','::Seleccione::'));
            return $resultado;
    }
   public function seleccionar_provincia($departamento,$indDefault=''){
            $array_prov = $this->ubigeo_model->listar_provincias($departamento);
            $arreglo = array();
            if(count($array_prov)>0){
                    foreach($array_prov as $indice=>$valor){
                            $indice1   = substr($valor->UBIGC_CodProv,2,2);
                            $valor1    = $valor->UBIGC_DescripcionProv;
                            $arreglo[$indice1] = $valor1;
                    }
            }
            $resultado = $this->html->optionHTML($arreglo,$indDefault,array('00','::Seleccione::'));
            return $resultado;
    }
   public function seleccionar_distritos($departamento,$provincia,$indDefault=''){
            $array_dist = $this->ubigeo_model->listar_distritos($departamento,$provincia);
            $arreglo = array();
            if(count($array_dist)>0){
                    foreach($array_dist as $indice=>$valor){
                            $indice1   = substr($valor->UBIGC_CodDist,4,2);
                            $valor1    = $valor->UBIGC_Descripcion;
                            $arreglo[$indice1] = $valor1;
                    }
            }
            $resultado = $this->html->optionHTML($arreglo,$indDefault,array('00','::Seleccione::'));
            return $resultado;
    }
   public function seleccionar_tipodocumento($indDefault=''){
            $array_dist = $this->tipodocumento_model->listar_tipo_documento();
            $arreglo = array();
            if(count($array_dist)>0){
                    foreach($array_dist as $indice=>$valor){
                            $indice1   = $valor->TIPDOCP_Codigo;
                            $valor1    = $valor->TIPOCC_Inciales;
                            $arreglo[$indice1] = $valor1;
                    }
            }
            $resultado = $this->html->optionHTML($arreglo,$indDefault,array('0','::Seleccione::'));
            return $resultado;
    }

   public function ver_persona($persona)
    {

            $datos                = $this->persona_model->obtener_datosPersona($persona);
            $tipo_doc             = $datos[0]->PERSC_TipoDocIdentidad;
            $estado_civil         = $datos[0]->ESTCP_EstadoCivil;
            $nacionalidad         = $datos[0]->NACP_Nacionalidad;
            $nacimiento           = $datos[0]->UBIGP_LugarNacimiento;
            $sexo                 = $datos[0]->PERSC_Sexo;
            $ubigeo_domicilio     = $datos[0]->UBIGP_Domicilio;
            $datos_nacionalidad   = $this->nacionalidad_model->obtener_nacionalidad($nacionalidad);
            $datos_nacimiento     = $this->ubigeo_model->obtener_ubigeo($nacimiento);
            $datos_ubigeoDom_dpto = $this->ubigeo_model->obtener_ubigeo_dpto($ubigeo_domicilio);
            $datos_ubigeoDom_prov = $this->ubigeo_model->obtener_ubigeo_prov($ubigeo_domicilio);
            $datos_ubigeoDom_dist = $this->ubigeo_model->obtener_ubigeo($ubigeo_domicilio);
            $datos_doc            = $this->tipodocumento_model->obtener_tipoDocumento($tipo_doc);
            $datos_estado_civil   = $this->estadocivil_model->obtener_estadoCivil($estado_civil);
            $data['nacionalidad'] = $datos_nacionalidad[0]->NACC_Descripcion;
            $data['nacimiento']   = $datos_nacimiento[0]->UBIGC_Descripcion;
            $data['tipo_doc']     = $datos_doc[0]->TIPOCC_Inciales;
            $data['estado_civil'] = $datos_estado_civil[0]->ESTCC_Descripcion;
            $data['sexo']         = $sexo==0?'MASCULINO':'FEMENINO';
            $data['telefono']     = $datos[0]->PERSC_Telefono;
            $data['movil']        = $datos[0]->PERSC_Movil;
            $data['fax']          = $datos[0]->PERSC_Fax;
            $data['email']        = $datos[0]->PERSC_Email;
            $data['web']          = $datos[0]->PERSC_Web;
            $data['direccion']    = $datos[0]->PERSC_Direccion;
            $data['dpto']         = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
            $data['prov']         = $datos_ubigeoDom_prov[0]->UBIGC_Descripcion;
            $data['dist']         = $datos_ubigeoDom_dist[0]->UBIGC_Descripcion;


        $data['datos']  = $datos;
        $data['titulo'] = "VER PERSONA";

        $this->load->view('maestros/persona_ver',$data);
    }
   public function JSON_datos_persona($persona){
        $datos_persona = $this->persona_model->obtener_datosPersona($persona);
        $result=array();
        if(count($datos_persona)>0)
            $result=$datos_persona[0];
        echo json_encode($result);
   }

}
?>