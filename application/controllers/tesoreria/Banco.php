<?php

class banco extends CI_Controller{

    private $empresa;
    private $compania;
    private $usuario;

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('date');

        $this->load->model('empresa/proveedor_model'); 
        $this->load->model('empresa/empresa_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/proyecto_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('tesoreria/banco_model');
        $this->load->model('tesoreria/caja_model');
        $this->load->model('tesoreria/movimiento_model');
        $this->load->model('tesoreria/tipocaja_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('seguridad/usuario_model');
        
        $this->load->library('html');
        $this->load->library('pagination');
        $this->load->library('layout','layout');
        
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
    }
    
    public function index(){
       $this->layout->view('seguridad/inicio');	
    }

    #########################
    ##### FUNCTIONS NEWS
    #########################

        public function getBancosAsoc(){
            $cliente = $this->input->post("cliente");
            $proveedor = $this->input->post("proveedor");

            if ($cliente != "")
                $bancosInfo = $this->banco_model->getBancosCliente($cliente);

            if ($proveedor != "")
                $bancosInfo = $this->banco_model->getBancosProveedor($proveedor);

            $data = array();
            $match = false;
            if ($bancosInfo != NULL){
                foreach ($bancosInfo as $i => $val){
                    $data[] = array(
                                        "banco" => $val->BANP_Codigo,
                                        "nombre" => $val->BANC_Nombre,
                                        "siglas" => $val->BANC_Siglas,

                                        "cuenta" => $val->CUENT_Codigo,
                                        "titular" => $val->CUENT_Titular,
                                        "moneda" => $val->MONED_Simbolo,
                                        "numero" => $val->CUENT_NumeroEmpresa
                                    );
                }
                $match = true;
            }

            $json = array( "match" => $match, "bancos" => $data );
            echo json_encode($json);
        }

    #########################
    ##### FUNCTIONS OLDS
    #########################
    
    public function movimientos($page = 1){

    	$filter = new stdClass();
    	$filter->PERSC_Nombre   = $this->input->post('nombre');
    	$data['nombres']    	= $filter->PERSC_Nombre;
    	$data['titulo']         = "Movimiento bancos";
    	$data['cboBancos'] = $this->OPTION_generador($this->banco_model->listar_by_id_empresa($this->session->userdata("empresa")), 'BANP_Codigo','BANC_Nombre','');
    	
    	/*************/
    	$conf['per_page']  		= $per_page = 10;
    	$conf['num_links']  	= 3;
    	$conf['next_link'] 		= "&gt;";
    	$conf['prev_link'] 		= "&lt;";
    	$conf['first_link'] 	= "&lt;&lt;";
    	$conf['last_link']  	= "&gt;&gt;";
    	$conf['uri_segment'] 	= 4;
    	$this->pagination->initialize($conf);
    	
    	$data['paginacion'] 	= $this->pagination->create_links();

        $filter = array();

    	$movimientos = $this->banco_model->listar_movimientos($filter, $page, $per_page);

        $clients_list = array();
        $proveedores_list = array();

        $resumen = array(
            "ingresos" => array(
                "soles" => 0,
                "dolares" => 0,
                "saldo" => 0
            ),
            "salidas" => array(
                "soles" => 0,
                "dolares" => 0,
                "saldo" => 0
            )
        );

        foreach ($movimientos as $key => $movimiento) {
            $movimiento->razonSocial = "";

            if($movimiento->CLIP_Codigo && $movimiento->CLIP_Codigo != 0) {

                if(!isset($clients_list[$movimiento->CLIP_Codigo])) $clients_list[$movimiento->CLIP_Codigo] = $this->cliente_model->obtener($movimiento->CLIP_Codigo);
                $cliente = $clients_list[$movimiento->CLIP_Codigo];
                $movimiento->razonSocial = $cliente->nombre;
            }

            if($movimiento->PROVP_Codigo && $movimiento->PROVP_Codigo != 0) {

                if(!isset($proveedores_list[$movimiento->PROVP_Codigo])) $proveedores_list[$movimiento->PROVP_Codigo] = $this->proveedor_model->get_by_id($movimiento->PROVP_Codigo);
                $proveedor = $proveedores_list[$movimiento->PROVP_Codigo];
                $movimiento->razonSocial = $proveedor->nombre;
            }

            //calculando resumen
            if($movimiento->CAJAMOV_MovDinero == 1) {
                if($movimiento->MONED_Codigo == 1) $resumen["salidas"]["soles"] += $movimiento->PAGC_Monto;
                if($movimiento->MONED_Codigo == 2)  $resumen["salidas"]["dolares"] += $movimiento->PAGC_Monto;
            }else {
                if($movimiento->MONED_Codigo == 1) $resumen["ingresos"]["soles"] += $movimiento->PAGC_Monto;
                if($movimiento->MONED_Codigo == 2)  $resumen["ingresos"]["dolares"] += $movimiento->PAGC_Monto;
            }
        }

        $data["resumen"] = $resumen;
    	
    	$data['lista'] = $movimientos;
    	
    	$this->layout->view("tesoreria/banco_movimiento",$data);
    }
    
    public function nuevo_movimiento($filter = '0',$movimientoDinero,$codigoCaja){
     	if($movimientoDinero == 1){//INGRESO
     		$data['titulo']         = "REGISTRAR INGRESO DE DINERO";
     	}
     	
     	if($movimientoDinero == 2){ //SALIDA
     		$data['titulo']         = "REGISTRAR SALIDA DE DINERO";
     	}
     	
     	$data['movimientoDinero'] = $movimientoDinero;
     	$data['modo']           = "insertar";
     	$objeto                 = new stdClass();
     	$objeto->id             = "";
     	$data['datos']          = $objeto;
     	$data['display']        = "";
     	$data['nombreCaja']     = "";
     	$data['numeroCaja']     = "";
     	$data['tipo_caja']      = "0";
     	$data['checkedBan']     = "";
     	$data['url_action']     = base_url() . "index.php/tesoreria/caja/insertar_cuenta";
     	$data['cboCuentaContable'] = $this->OPTION_generador($this->movimiento_model->lista_cuentacontable(), 'CUNTCONTBL_Codigo','CUNTCONTBL_Descripcion','');
     	$data['cbomonedamovmiento'] = $this->OPTION_generador($this->moneda_model->listartipomoneda(), 'moned_codigo','moned_descripcion','');
     	 
     	if($filter != 0){
     		$data['cboResponsable'] = $this->OPTION_generador($this->movimiento_model->listCaja($filter), 'CAJA_Codigo','CAJA_Nombre','');
     	}else{
     		$data['cboResponsable'] = $this->OPTION_generador($this->movimiento_model->listCajatotal(), 'CAJA_Codigo','CAJA_Nombre','');
     	}
     	
     	$data['codigoCajaSeleccion'] = $codigoCaja;   	
     	$data['txtobservacion'] = "";
		$data['txtjustificacion'] = "";
     	$data['monto'] = "";
		$data['fechaingreso'] = "";
     	$data['nombrecliente'] = "";
     	
		$data['hidden'] = "hidden";
		$data['estado'] = "";
		$data['cmbformapago'] = "";
     	$this->load->view("tesoreria/movimiento_nuevo",$data);
     	
    }
    
    public function buscar_movimiento($j='0'){
    	$filter = new stdClass();
    	$filter->PERSC_Nombre = $this->input->post('nombres');
    	$data['nombres']      = $filter->PERSC_Nombre;
    	$data['titulo_tabla'] = "RESULTADO DE BUSQUEDA DE PROYECTOS";
    	$data['registros']    = count($this->movimiento_model->buscar_movimiento($filter));
    	$data['action'] 	  = base_url()."index.php/tesoreria/movimiento/buscar_movimiento";
    	$conf['base_url'] 	  = site_url('tesoreria/movimiento/buscar_movimiento/');
    	$conf['total_rows']   = $data['registros'];
    	$conf['per_page']     = 20;
    	$conf['num_links']    = 3;
    	$conf['next_link']    = "&gt;";
    	$conf['prev_link']    = "&lt;";
    	$conf['first_link']   = "&lt;&lt;";
    	$conf['last_link']    = "&gt;&gt;";
    	$conf['uri_segment']  = 4;
    	$this->pagination->initialize($conf);
    	$data['paginacion']   = $this->pagination->create_links();
    	$listado_movimiento   = $this->movimiento_model->buscar_movimiento($filter, $conf['per_page'],$j);
    	$item            	  = $j+1;
    	$lista           	  = array();
    	if(count($listado_movimiento)>0){
    		foreach($listado_movimiento as $indice=>$valor){
    			$caja       	= $valor->CAJA_Codigo;
    			$nombres        = $valor->CAJA_Nombre;
    			$editar         = "<a href='#' onclick='nuevo_movimiento(".$caja.")'><img src='".base_url()."images/icono_nuevo.png' width='16' height='16' border='0' title='Modificar'></a>";
    			$ver            = "<a href='#' onclick='ver_movimiento(".$caja.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
    			$eliminar       = "<a href='#' onclick='eliminar_movimiento(".$caja.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
    			$lista[]        = array($item,$nombres,$editar,$ver,$eliminar);
    			$item++;
    		}
    	}
    	$data['lista'] = $lista;
    	$this->layout->view("tesoreria/movimiento_index",$data);
    
    }
    
    
    public function ver_caja($caja)
    {
    	 
    	$datos   					 = $this->caja_model->obtener_datosCaja($caja);
    	$data['nombres']             = $datos[0]->CAJA_Nombre;
    	$tipoCaja        		 	 = $datos[0]->tipCa_codigo;
    	if($tipoCaja != null){
    		$datosTipoCaja			 = $this->caja_model->obtener_datosTipoCaja($tipoCaja);
    		$data['tipoCaja']		 = $datosTipoCaja[0]->tipCa_Descripcion;
    	}
    	$data['
    			
    	']       = $datos[0]->CAJA_Observaciones;
    	$data['datos']  			 = $datos;
    	$data['titulo'] 			 = "VER CAJA";
    	$this->load->view('tesoreria/caja_ver',$data);
    }
	
    public function eliminar_caja($caja){
    	$caja = $this->input->post('caja');
    	$this->caja_model->eliminar_caja($caja);
    }
    
    
   
    
    public function insertar_cuenta(){
    	/** INSERTAR DATOS DE CAJA **/
    	$nombreCaja 	= $this ->input -> post('nombreCaja');
    	$cboTipCaja		= $this ->input -> post('cboTipCaja');
    	$tipo_caja  	= $this ->input -> post('tipo_caja');
    	$cboResponsable = $this ->input -> post('cboResponsable');
    	$observaciones  = $this ->input -> post('observaciones');
    	$caja 			= $this->caja_model->insertar_datosCaja($nombreCaja,$cboTipCaja,$tipo_caja,$cboResponsable,$observaciones);
    	
    	/** INSERTAR DATOS DE  CAJA CUENTA**/
    	$cuentaCodigo  		 = $this ->input -> post('cuentaCodigo');
    	$cboCuentas    		 = $this ->input -> post('cboCuentas');
    	$cboTipoCaja 	   	 = $this ->input -> post('cboTipoCaja');
    	$limiteRetiro		 = $this ->input -> post('limiteRetiro');
    	$cuentaaccion  	 	 = $this ->input -> post('cuentaaccion');
    	if(is_array($cuentaCodigo)){
    		foreach ($cuentaCodigo as $indice => $valor){
    			if($valor != $cuentaCodigo){
    				$filter = new stdClass();
    				$filter ->CUENT_Codigo 	  = $cboCuentas[$indice];
    				$filter ->TIPOING_Codigo  = $cboTipoCaja[$indice];
    				$filter ->CAJCUENT_LIMITE = $limiteRetiro[$indice];
    				$filter ->CAJA_Codigo     = $caja;
    				if ($cuentaaccion[$indice] != 'e') {
    					$this->caja_model->insertar_cuenta($filter);
    				}
    			}
    		}
    	}
    	/** INSERTAR DATOS DE  CAJA CHEQUERA**/
    	$chequeraCodigo   = $this ->input -> post('chequeraCodigo');
    	$descripcion      = $this ->input -> post('descripcion');
    	$cboSerie 	      = $this ->input -> post('cboSerie');
    	$chequeaccion     = $this ->input -> post('chequeaccion');
    	if(is_array($chequeraCodigo)){
    		foreach ($chequeraCodigo as $indice => $valor){
    			if($valor != $chequeraCodigo){
    				$filter = new stdClass();
    				$filter ->CAJCHEK_Descripcion  = $descripcion[$indice];
    				$filter ->CAJA_Codigo 	  	   = $caja;
    				$filter ->CHEK_Codigo 	  	   = $cboSerie[$indice];
    				if ($chequeaccion[$indice] != 'e') {
    					$this->caja_model->insertar_chekera($filter);
    				}
    			}
    		}
       }
       $this->cajas();    
    }
    
    public function editar_caja($caja){
    	
    	$data['modo']	 = "modificar";
    	$data['id']	  	 = $this->input->post('id');
    	$data['titulo']  = "MODIFICAR CAJA";
    	$data['display'] = "";
    	$datos_caja      = $this->caja_model->obtener_datosCaja($caja);
    	$codigoCaja		 = $datos_caja[0]->CAJA_Codigo;
    	$cboTipCaja		 = $datos_caja[0]->tipCa_codigo;
    	$nombreCaja   	 = $datos_caja[0]->CAJA_Nombre;
    	$cboResponsable  = $datos_caja[0]->USUA_Codigo;
    	$observaciones   = $datos_caja[0]->CAJA_Observaciones;    	
    	$objeto                 = new stdClass();
    	$objeto->id             = $datos_caja[0]->CAJA_Codigo;
    	$data['datos']    		= $objeto;
    	$data['nombreCaja']   	= $nombreCaja;
    	$data['cboResponsable'] = $this->OPTION_generador($this->usuario_model->listar_usuarios(), 'USUA_Codigo','PERSC_Nombre',$datos_caja[0]->USUA_Codigo);
    	$data['cboTipCaja'] 	= $this->OPTION_generador($this->caja_model->listar_tipoCaja(), 'tipCa_codigo', 'tipCa_Descripcion', $datos_caja[0]->tipCa_codigo);
    	$data['observaciones']  = $observaciones;    	
    	$compania 				= $this->session->userdata('compania');
    	$Datoscuenta  = $this->caja_model->obtener_datosCuenta_banco($compania);
    	if(is_array($Datoscuenta)){
    		foreach ($Datoscuenta as $indice => $valor){
    			if($valor != $Datoscuenta){
    				$bancoCodigo  = $Datoscuenta[0]->BANP_Codigo;
    				if($bancoCodigo != null){
    					$objetos[]= $bancoCodigo;
    				}
    			}
    		}
    	}
    	$data['cboBancos'] 		= $this->OPTION_generador($this->banco_model->listar_banco($objetos), 'BANP_Codigo', 'BANC_Nombre', '');
    	$data['limiteRetiro'] 	= "";
    	$data['descripcion'] 	= "";
    	
    	/** OBTENER DATOS DE CAJA CUENTA **/
    	$detalle_cuenta 	  		= $this->listar_detalle_cuenta($caja);
    	$data['detalle_cuenta']     = $detalle_cuenta;
    	
    	/** OBTENER DATOS DE CHEQUERA CUENTA **/
    	$detalle_chequera 	  		= $this->listar_detalle_chequera($caja);
    	$data['detalle_chequera']     = $detalle_chequera;
    	
    	
    	$this->load->view("tesoreria/caja_nuevo",$data);

		
    }
    
    public function modificar_caja(){
    	/** MODIFICAR DATOS DE CAJA **/
    	$codigo             = $this ->input -> post('caja');
    	$nombreCaja 		= $this ->input -> post('nombreCaja');
    	$cboTipCaja			= $this ->input -> post('cboTipCaja');
    	$tipo_caja  		= $this ->input -> post('tipo_caja');
    	$cboResponsable 	= $this ->input -> post('cboResponsable');
    	$observaciones  	= $this ->input -> post('observaciones');
    	$caja 				= $this->caja_model->modificar_datosCaja($codigo,$nombreCaja,$cboTipCaja,$tipo_caja,$cboResponsable,$observaciones);
    	
    	/** MODIFICAR DATOS DE CUENTA **/
    	$cuentaCodigo  		 = $this ->input -> post('cuentaCodigo');
    	$cboCuentas    		 = $this ->input -> post('cboCuentas');
    	$cboTipoCaja 	   	 = $this ->input -> post('tipCaja');
    	$limiteRetiro		 = $this ->input -> post('limiteRetiro');
    	$cuentaaccion  	 	 = $this ->input -> post('cuentaaccion');
    	if(is_array($cuentaCodigo)){
    		foreach ($cuentaCodigo as $indice => $valor){
    			if($valor != $cuentaCodigo){
    				$detalle_accion = $cuentaaccion[$indice];
    				$filter = new stdClass();
    				$filter ->CUENT_Codigo 	  = $cboCuentas[$indice];
    				$filter ->TIPOING_Codigo  = $cboTipoCaja[$indice];
    				$filter ->CAJCUENT_LIMITE = $limiteRetiro[$indice];
    				$filter ->CAJA_Codigo     = $codigo;
    				    				
    				if ($detalle_accion == 'n') {
    					$this->caja_model->insertar_cuenta($filter);
    				} elseif ($detalle_accion == 'm') {
    					$this->caja_model->modificar_cuenta($valor, $filter);
    				} elseif ($detalle_accion == 'e') {
    					$this->caja_model->eliminar_cuenta($valor);
    				}
    			}
    		}
    	}
    	
    	/** MODIFICAR DATOS DE  CHEQUERA**/
    	$chequeraCodigo   = $this ->input -> post('chequeraCodigo');
    	$descripcion      = $this ->input -> post('descripcion');
    	$cboSerie 	      = $this ->input -> post('cboSerie');
    	$chequeaccion     = $this ->input -> post('chequeaccion');
    	if(is_array($chequeraCodigo)){
    		foreach ($chequeraCodigo as $indice => $valor){
    			if($valor != $chequeraCodigo){
    				$detalle_accion = $chequeaccion[$indice];
    				$filter = new stdClass();
    				$filter ->CAJCHEK_Descripcion  = $descripcion[$indice];
    				$filter ->CAJA_Codigo 	  	   = $codigo;
    				$filter ->CHEK_Codigo 	  	   = $cboSerie[$indice];
    				
    				if ($detalle_accion == 'n') {
    					$this->caja_model->insertar_chekera($filter);
    				} elseif ($detalle_accion == 'm') {
    					$this->caja_model->modificar_chekera($valor, $filter);
    				} elseif ($detalle_accion == 'e') {
    					$this->caja_model->eliminar_chekera($valor);
    				}
    				
    			}
    		}
    	}
    	
    	
    	
    }
    
    public function listar_detalle_cuenta($caja){
    	$detalle = $this->caja_model->obtener_cuenta_caja($caja);
    	$lista_detalles = array();
    	if (count($detalle) > 0) {
    		foreach ($detalle as $indice => $valor) {
    			$cajaCuentaCodigo 	  = $valor->CAJCUENT_Codigo;
    			$cboCuentas			  = $valor->CUENT_Codigo;
    			$limiteRetiro		  = $valor->CAJCUENT_LIMITE;
    			$tipoCaja			  = $valor->TIPOING_Codigo;
    			if($tipoCaja == 1){
    				$tipoNombre = "INGRESO";
    			}elseif ($tipoCaja == 2){
    				$tipoNombre = "SALIDA";
    			}
    			
    			if($cboCuentas != null){
    				$Datoscuenta  = $this->caja_model->obtener_datosCuenta($cboCuentas);
    				$bancoCodigo  = $Datoscuenta[0]->BANP_Codigo;
    				$numroCuenta  = $Datoscuenta[0]->CUENT_NumeroEmpresa;
    				$tipCuenta	  = $Datoscuenta[0]->CUENT_TipoCuenta;
    				if($bancoCodigo != null){
    					$datosBanco   = $this->banco_model->obtener($bancoCodigo);
    					$bancoNombre  = $datosBanco[0]->BANC_Nombre;
    				}
    				if($tipCuenta == 1){
    					$tipCuentaNombre = "AHORROS";
    				}elseif ($tipCuenta == 2){
    					$tipCuentaNombre = "CORRIENTE";
    				}
    				$moneda		  = $Datoscuenta[0]->MONED_Codigo;
    				if($moneda != null){
    					$datosMoneda  = $this->moneda_model->obtener($moneda);
    					$monedaNombre = $datosMoneda[0]->MONED_Descripcion;
    				}
    			}
    			
    
    			$objeto = new stdClass();
    			$objeto->CAJCUENT_Codigo	 = $cajaCuentaCodigo;
    			$objeto->BANP_Codigo 		 = $bancoCodigo;
    			$objeto->CUENT_Codigo 		 = $cboCuentas;
    			$objeto->CUENT_NumeroEmpresa = $numroCuenta;
    			$objeto->CUENT_TipoCuenta 	 = $tipCuenta;
    			$objeto->CUENT_TipoCuenta	 = $tipCuentaNombre;
    			$objeto->MONED_Codigo 		 = $moneda;
    			$objeto->MONED_Descripcion   = $monedaNombre;
    			$objeto->CAJCUENT_LIMITE 	 = $limiteRetiro;
    			$objeto->TIPOING_Codigo 	 = $tipoCaja;
    			$objeto->TIPOING_Codigo	 	 = $tipoNombre;
    			$objeto->BANC_Nombre		 = $bancoNombre;
    			$lista_detalles[] = $objeto;
    		}
    	}
    	return $lista_detalles;
    }
    
    public function listar_detalle_chequera($caja){
    	$detalle = $this->caja_model->obtener_cuenta_chequera($caja);
    	$lista_detalles = array();
    	if (count($detalle) > 0) {
    		foreach ($detalle as $indice => $valor) {
    			$chequeraCuentaCodigo 	  = $valor->CAJCHEK_Codigo;
    			$descripcion		  	  = $valor->CAJCHEK_Descripcion;
    			$cboSerie		  	  	  = $valor->CHEK_Codigo;
    			if($cboSerie != null){
    				$Datoschequera  = $this->caja_model->obtener_chequeraCodigo($cboSerie);
    				$serieChequera  = $Datoschequera[0]->SERIP_Codigo;
    				$numroSerie     = $Datoschequera[0]->CHEK_Numero;
    				$serie 			= $serieChequera."-".$numroSerie;
    			}
    			if($caja != null){
    				$cuentaCaja  = $this->caja_model->obtener_cuenta_caja($caja);
    				$cuenta		  = $cuentaCaja[0]->CUENT_Codigo;
    				if($cuenta != null){
    					$Datoscuenta  = $this->caja_model->obtener_datosCuenta($cuenta);
    					$bancoCodigo  = $Datoscuenta[0]->BANP_Codigo;
    					$numroCuenta  = $Datoscuenta[0]->CUENT_NumeroEmpresa;
    				}
    				
    			}
    			
    			if($bancoCodigo != null){
    				$datosBanco   = $this->banco_model->obtener($bancoCodigo);
    				$bancoNombre  = $datosBanco[0]->BANC_Nombre;
    			}
    
    			$objeto = new stdClass();
    			$objeto->CAJCHEK_Codigo	 	 = $chequeraCuentaCodigo;
    			$objeto->CUENT_Codigo 		 = $cuenta;
    			$objeto->CAJCHEK_Descripcion = $descripcion;
    			$objeto->BANP_Codigo 		 = $bancoCodigo;
    			$objeto->BANC_Nombre		 = $bancoNombre;
    			$objeto->CUENT_NumeroEmpresa = $numroCuenta;
    			$objeto->SERIP_Codigo        = $serieChequera;
    			$objeto->CHEK_Numero		 = $numroSerie;
    			$objeto->CHEK_Codigo		 = $cboSerie;
				$objeto->SERIP_Codigo		 = $serie;
    			$lista_detalles[] = $objeto;
    		}
    	}
    	return $lista_detalles;
    }
    
    public function cargar_cuenta($banco){
    	$cboCuentas = $this->seleccionar_cuenta_banco($banco);
    	$fila  ="<select id='cboCuentas' name='cboCuentas' class='comboMedio'>";
    	$fila .="<option value=''> ::Seleccione:: </option>";
    	$fila .= "<?php echo $cboCuentas; ?>";
    	$fila .= "</select>";
    	echo $fila;
    }
    
    public function cargar_cuentaCheque($banco){
    	$cboCuentaCheque = $this->seleccionar_cuenta_banco($banco);
    	$fila  = "<select id='cboCuentaCheque' name='cboCuentaCheque' class='comboMedio' onchange='cargar_serieCuenta(this);'>";
    	$fila .="<option value=''> ::Seleccione:: </option>";
    	$fila .= "<?php echo $cboCuentaCheque; ?>";
    	$fila .= "</select>";
    	echo $fila;
    }
    
    public function cargar_datosCuenta($cuenta){
    	$Datoscuenta  = $this->caja_model->obtener_datosCuenta($cuenta);
    	$tipCuenta	  = $Datoscuenta[0]->CUENT_TipoCuenta;
    	$moneda		  = $Datoscuenta[0]->MONED_Codigo;
    	if($tipCuenta == 1){
    		$nomTipCuenta = "AHORRO";
    	}elseif ($tipCuenta == 2){
    		$nomTipCuenta = "CORRIENTE";
    	}
    	$DatosMoneda  = $this->moneda_model->obtener($moneda);
    	$monedaNom	  = $DatosMoneda[0]->MONED_Descripcion;
    	$fila	      = "<input name='tipCuenta' type='text' class='cajaGeneral' disabled	id='tipCuenta' maxlength='150' value='$nomTipCuenta'>";
    	$fila	      .= "&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Moneda&nbsp; &nbsp;&nbsp;";
    	$fila	      .= "<input name='monedaCuenta' type='text' class='cajaGeneral' disabled id='monedaCuenta' maxlength='150' value='$monedaNom'>";
    	echo $fila;
    }
    
 	public function cargar_serie($serie){
    	$cboSerie = $this->caja_model->obtener_numeroSerie($serie);
    	$numero   = $numeroSerie[0]->CHEK_Numero;
    	$fila     = "<input name='serieNumero' type='text' class='cajaGeneral'  disabled id='serieNumero' maxlength='150' value='$numero'>";
    	echo $fila;
    }
    
    public function cargar_serieCuenta($cuenta){
    	$cboSerie  = $this->seleccionar_chequera($cuenta);
    	$fila = "<select id='cboSerie' name='cboSerie' class='comboMedio'>";
    	$fila .="<option value=''> ::Seleccione:: </option>";
    	$fila .= "<?php echo $cboSerie; ?>";
    	$fila .= "</select>";
    	echo $fila;
    }
    
    public function cargar_banco($banco){
    	$cboBancoCuenta = $this->seleccionar_banco_cuenta($banco);
    	$fila 			= "<select id='cboBancoCuenta' name='cboBancoCuenta' class='comboMedio'>";
    	$fila 		   .="<option value=''> ::Seleccione:: </option>";
    	$fila 		   .= "<?php echo $cboBancoCuenta; ?>";
    	$fila 		   .= "</select>";
    	echo $fila;
    }
    
    public function cargar_chequera($cuenta){
    	$cboSerie  = $this->seleccionar_chequera($cuenta);
    	$fila  = "<select id='cboSerie' name='cboSerie' class='comboMedio'>";
    	$fila .="<option value=''> ::Seleccione:: </option>";
    	$fila .= "<?php echo $cboSerie; ?>";
    	$fila .= "</select>";
    	echo $fila;
    }
    
    public function seleccionar_chequera($cuenta){
    	$chequera = $this->caja_model->listar_chequera($cuenta);
    	$arreglo = array();
    	if(count($chequera)>0){
    		foreach($chequera as $indice=>$valor){
    			$indice1   = $valor->CHEK_Codigo;
    			$valor1    = $valor->SERIP_Codigo;
    			$valor2    = $valor->CHEK_Numero;
    			$arreglo[$indice1] = $valor1."-".$valor2;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }
	
    public function cargar_tabla_cuenta($cuenta){
    	if($cuenta != null){
    		$Datoscuenta  = $this->caja_model->obtener_datosCuenta($cuenta);
    		$monedaCodigo = $Datoscuenta[0]->MONED_Codigo;
    		$bancoCodigo  = $Datoscuenta[0]->BANP_Codigo;
    		$compania = $this->session->userdata('compania');
    		$cboCuentas   = $this->seleccionar_cuenta($compania);
    		$cbo_banco 	  = $this->seleccionar_banco($bancoCodigo);
    		$cbo_moneda   = $this->seleccionar_moneda($monedaCodigo);
    		$fila	      = "<tr>";
    		$fila	      .= "<td>Bancos</td>";
    		$fila	      .= "<td>";
    		$fila     	  .= "<select id='cboBancos' name='cboBancos' class='comboMedio'>";
    		$fila 		  .="<option value=''> ::Seleccione:: </option>";
    		$fila    	  .= $cbo_banco;
    		$fila    	  .= "</select>&nbsp;&nbsp;";
    		$fila	      .= "</td>";
    		$fila	      .= "<td>Moneda</td>";
    		$fila	      .= "<td>";
    		$fila    	  .= "<select id='cboMoneda' name='cboMoneda' class='comboMedio'>";
    		$fila    	  .= $cbo_moneda;
    		$fila    	  .= "</select>&nbsp;&nbsp;";
    		$fila	      .= "</td>";
    		$fila	      .= "</tr>";
    		echo $fila;
    	}
    }
    
    public function seleccionar_cuenta($compania){
    	$cuenta = $this->caja_model->listar_cuenta($compania);
    	$arreglo = array();
    	if(count($cuenta)>0){
    		foreach($cuenta as $indice=>$valor){
    			$indice1   = $valor->CUENT_Codigo;
    			$valor1    = $valor->CUENT_NumeroEmpresa;
    			$arreglo[$indice1] = $valor1;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }
    
    public function seleccionar_cuenta_banco($banco){
    	$cuenta = $this->caja_model->listar_cuenta_banco($banco);
    	$arreglo = array();
    	if(count($cuenta)>0){
    		foreach($cuenta as $indice=>$valor){
    			$indice1   = $valor->CUENT_Codigo;
    			$valor1    = $valor->CUENT_NumeroEmpresa;
    			$arreglo[$indice1] = $valor1;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }
    
    public function seleccionar_banco($cuenta){
    	$bancos = $this->caja_model->listar_banco_cuenta($cuenta);
    	$arreglo = array();
    	if(count($bancos)>0){
    		foreach($bancos as $indice=>$valor){
    			$indice1   = $valor->BANP_Codigo;
    			$valor1    = $valor->BANC_Nombre;
    			$arreglo[$indice1] = $valor1;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }
    
    public function seleccionar_banco_cuenta($banco){
    	$bancos = $this->caja_model->listar_banco_cuenta($banco);
    	$arreglo = array();
    	if(count($bancos)>0){
    		foreach($bancos as $indice=>$valor){
    			$indice1   = $valor->BANP_Codigo;
    			$valor1    = $valor->BANC_Nombre;
    			$arreglo[$indice1] = $valor1;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }
    
    public function seleccionar_moneda($cuenta){
    	$moneda = $this->caja_model->listar_moneda_cuenta($cuenta);
    	$arreglo = array();
    	if(count($moneda)>0){
    		foreach($moneda as $indice=>$valor){
    			$indice1   = $valor->MONED_Codigo;
    			$valor1    = $valor->MONED_Descripcion;
    			$arreglo[$indice1] = $valor1;
    		}
    	}
    	$resultado = $this->html->optionHTML($arreglo,array('00','::Seleccione::'));
    	return $resultado;
    }

    public function encuentrax_cliente() {
    	   
    	$compania = $this->compania; //session
    	$keyword = $this->input->post('term'); //captura de ajax mando un valor
    	$codigoInventario = $this->input->post('codigoInventario'); // igual capturo un valor de ajax
    	$filter=new stdClass();
    	$filter->nombre=$keyword;
        //	$filter->flagBS='B';
    	$result = array();
		$value = "";
		$consultaNombre = 0;
    	
		if($keyword!=null && count(trim($keyword))>0){
			
    		if($codigoInventario == 10){ //TIPO CAJA
    			$consultaNombre =$datosTipoCaja = $this->caja_model->autocompleteCaja($keyword);
    			if($consultaNombre != null && count($consultaNombre)>0){
    				foreach ($datosTipoCaja as $indice => $valor) {
    					$nombre = $valor-> CAJA_Nombre;
    					$codigito = $valor-> CAJA_Codigo;
    					$result[] = array( "value" => $nombre ,"codigo" => $codigito);
    					 
    				}
    			}
    	}//TERMINA LA SENTENCIA DE TIPO CAJA
    	
    	if($codigoInventario == 20){ //TIPO DIRECTIVO
    		$consultaNombre =$datosDirectivo = $this->directivo_model->autocompleteDirectivo($keyword);
    		if($consultaNombre != null && count($consultaNombre)>0){
    			foreach ($datosDirectivo as $indice => $valor) {
    				$nombre = $valor-> PERSC_Nombre;
    				$codigito = $valor-> PERSP_Codigo;
    				$result[] = array( "value" => $nombre ,"codigo" => $codigito);
    					
    			}
    		}
    	}//TERMINA LA SENTENCIA DE DIRECTIVO
    		
    	if($codigoInventario == 30){ //PROVEEDOR
    		$TipoPersonaGlobarl = $this->proveedor_model->autocompleteTipoProveedor($keyword);
    		$separado_por_comas = serialize($TipoPersonaGlobarl);
    		if($separado_por_comas != "N;"){
    			$consultaNombre = $this->proveedor_model->autocompleteProveedor($keyword); // JURIDICO
    			if ($consultaNombre != NULL && count($consultaNombre)>0) {
    				foreach ($consultaNombre AS $proveedor => $value) {
    						$nombre = $value->EMPRC_RazonSocial;
    						$codigito = $value->PROVP_Codigo;
    						$result[] = array("value" => $nombre, "codigo" => $codigito);//, "ruc" => $ruc
    				}
    			}
    		}else{
    				$consultaNombre = $this->proveedor_model->autocompleteProveedorNatural($keyword); // NATURAL
    				
    			if ($consultaNombre != NULL && count($consultaNombre)>0) {
    				foreach ($consultaNombre AS $proveedor => $value) {
    						$nombre = $value->PERSC_Nombre . ' ' . $value->PERSC_ApellidoPaterno. ' ' . $value->PERSC_ApellidoMaterno;
    						$codigito = $value->PROVP_Codigo;
    					
    					$result[] = array("value" => $nombre, "codigo" => $codigito);//, "ruc" => $ruc
    				}
    			}
    		}
    			
    			
    	}//TERMINA LA SENTENCIA DE TIPO PROVEEDOR 
    		
    	if($codigoInventario == 40){//CLIENTE
    			$TipoPersonaGlobar=$this->cliente_model->TipoPersonaCliente($keyword);
    			$separado_por_comas = serialize($TipoPersonaGlobar);
    			if(trim($separado_por_comas) == "N;"){
    					$consultaNombreNatura = $this->cliente_model->autocompleteClienteNatural($keyword);
    					foreach ($consultaNombreNatura  as $key => $valor) {
    						$nombre = $valor->PERSC_Nombre . ' ' .$valor->PERSC_ApellidoPaterno . ' ' .$valor->PERSC_ApellidoMaterno;
    						$codigito=$valor->CLIP_Codigo;
    						$result[] = array("value" => $nombre, "codigo" => $codigito);//, "ruc" => $ruc
	
    				}
    			}else{
    				
       				$consultaNombre=$this->cliente_model->autocompleteCliente($keyword);
    				foreach ($consultaNombre  as $key => $valor) {
    				
    					$nombre =$valor->EMPRC_RazonSocial;
    					$codigito=$valor->CLIP_Codigo;
    						
    					$result[] = array("value" => $nombre, "codigo" => $codigito);//, "ruc" => $ruc
    				}
    			}

    	}//TERMINA LA SENTENCIA DE CLIENTE	    		
    
    	}
    
    	echo json_encode($result);
    }
    
    public function obtenerBancos(){
    	$codigo = $this->input->post('codigo'); // igual capturo un valor de ajax
    	$codigoSeleccion = $this->input->post('codigoInventario');
    	$result = array();
    	 $x=0;
    	 $SeleccionBanco=0;
    	 $codigopersona=0;
    	if($codigo!=null && count(trim($codigo))>0){
    		if($codigoSeleccion == 10){//CAJA
    			$SeleccionBanco = $this->movimiento_model->comboBancoCaja($codigo);
    		}
    		if($codigoSeleccion == 20){//DIRECTIVO
    			$SeleccionBanco = $this->movimiento_model->comboBancoDirectivo($codigo);
    		}
    		
    		if($codigoSeleccion == 30){//PROVEEDOR
    			$tipoProveedor = $this->movimiento_model->comboBancoTipoProveedor($codigo);
    			
    			foreach ($tipoProveedor as $indice => $valor){
    				$tipoProveedor = $valor-> PROVC_TipoPersona;
    			}//1=EMPRESA , 0=PERSONA
    			if($tipoProveedor  != 1){
    				$SeleccionBanco = $this->movimiento_model->comboBancoProveedorNatural($codigo);
    			}
    			
    			if($tipoProveedor  == 1){
    				$SeleccionBanco = $this->movimiento_model->comboBancoProveedor($codigo);
    				
    			}
    		
    		}
    		
			if($codigoSeleccion == 40){//CLIENTE
				$tipoCLiente = $this->movimiento_model->comboBancoTipoCliente($codigo);
				foreach ($tipoCLiente as $indice => $valor){
					$tipoCLiente = $valor-> CLIC_TipoPersona;
				}//1=EMPRESA , 0=PERSONA
				if($tipoCLiente  != 1){ 
					$SeleccionBanco = $this->movimiento_model->comboBancoClienteNatural($codigo);
				}
				
				if($tipoCLiente  == 1){
					$SeleccionBanco = $this->movimiento_model->comboBanco($codigo);
				}
			}
    		 
			
			if($SeleccionBanco != null && count($SeleccionBanco)>0){
				foreach ($SeleccionBanco as $indice => $valor) {
					$nombre = $valor-> BANC_Nombre;
					$codigito = $valor-> BANP_Codigo;
					
					if($codigoSeleccion == 10){//CAJA
						$codigopersona = $valor->CAJA_Codigo;
					}
					if($codigoSeleccion == 20){//DIRECTIVO
						$codigopersona = $valor->PERSP_Codigo;
					}
					if($codigoSeleccion == 30){//PROVEEDOR
					
						if($tipoProveedor  != 1){
							$codigopersona = $valor->PERSP_Codigo;
						}
							
						if($tipoProveedor  == 1){
							$codigopersona = $valor->EMPRP_Codigo;
						}
					}
					if($codigoSeleccion == 40){//CLIENTE
						
						if($tipoCLiente  != 1){
								$codigopersona = $valor->PERSP_Codigo;
						}
							
						if($tipoCLiente  == 1){
								$codigopersona = $valor->EMPRP_Codigo;
						}
					}
					
					$result[] = array( "nombre" => $nombre ,"codigo" => $codigito ,"codigopersona" => $codigopersona);
				}
			}
    	}
    
    	echo json_encode($result);
    }
  
    public function obtenerCuentas(){
    	$codigo = $this->input->post('codigo');
    	$codigopersona = $this->input->post('codipersona');
    	$codigoSeleccion = $this->input->post('codigoInventario');
    	
//     		echo "<script>alert('CodigoBanco : ".$codigo." + CodigoPersona: ".$codigopersona."  ')</script>";
    	
    	$result = array();
    	$SeleccionCuenta ;
    	if($codigo!=null && count(trim($codigo))>0){
    		
    		if($codigoSeleccion == 10){//CAJA
    			$SeleccionCuenta = $this->movimiento_model->comboCuentaCaja($codigo,$codigopersona);
    		}
    		
    		if($codigoSeleccion == 20){//DIRECTIVO
    			$SeleccionCuenta = $this->movimiento_model->comboCuentaDirectivo($codigo,$codigopersona);
    		}
    		
    		if( $codigoSeleccion == 30){//PROVEEDOR
    			$sleccionCuentaProveedor = $this->movimiento_model->SeleccionarCuentaProveedor($codigo,$codigopersona); // ESTO BUSCA al proveedor empresa
    			$separado_por_comas = serialize($sleccionCuentaProveedor);
//     			    			echo "<script>alert('aber   ".$separado_por_comas."')</script>";
    			if($separado_por_comas != "N;"){
    				/**Aca esta la empresa**/
    				$SeleccionCuenta = $this->movimiento_model->comboCuentaProve($codigo,$codigopersona);
    			}else{
    				/**Aca esta la Persona**/
    				$SeleccionCuenta = $this->movimiento_model->comboCuentaProveedorNatural($codigo,$codigopersona);
    			}
    			
    		}
    		
    		if( $codigoSeleccion == 40){//CLIENTE
    			$sleccionCuentaCliente = $this->movimiento_model->SeleccionarCuentaCliente($codigo,$codigopersona);
    			$separado_por_comas = serialize($sleccionCuentaCliente);
//      			echo "<script>alert('aber   ".$separado_por_comas."')</script>";
    			if($separado_por_comas != "N;"){
    				$SeleccionCuenta = $this->movimiento_model->comboCuentaClienteNatural($codigo,$codigopersona);
    				
    			}else{
    				$SeleccionCuenta = $this->movimiento_model->comboCuenta($codigo,$codigopersona);
  				
    			}
    		}
	
    		if(count($SeleccionCuenta) != null){
    			foreach ($SeleccionCuenta as $indice => $valor){
    				$nombre = $valor->CUENT_NumeroEmpresa;
    				$codigo = $valor->CUENT_Codigo;
    				$result[] = array("nombre" => $nombre , "codigo" => $codigo);
    				
    			}
    		}
    		
    	}
    	
    	
    	echo json_encode($result);
    	 
    }
   
    public function buscarCuentas(){
    	$codigo = $this->input->post('codigo');
    	$result = array();
    	 $verificartipoCuenta=0;
    	if($codigo!=null && count(trim($codigo))>0){
    		$CodigoDeBancos = $this->movimiento_model->obtenerCuenta($codigo);
    		if($CodigoDeBancos !=null && count($CodigoDeBancos)){
    			foreach ($CodigoDeBancos as $indice => $valor){
    				
    				$banco = $valor->BANC_Nombre;
    				$cuenta = $valor->CUENT_NumeroEmpresa;
//     				$titulo = $valor->CUENT_Titular;
    				$moneda = $valor->MONED_Descripcion;
    				$tipocuenta = $valor->CUENT_TipoCuenta;
    				if($tipocuenta ==1){
    					$verificartipoCuenta="Ahorros";
    				}else{
    					$verificartipoCuenta="Corriente";
    				}
    				$result[] = array("banco" => $banco , "cuenta" => $cuenta  , "moneda" =>$moneda, "tipocuenta" =>$verificartipoCuenta );// ,"titulo" => $titulo
    				
    			}
    		}
    	}
    	echo json_encode($result);
    
    }
    
    public function buscar_cajamovimiento($codigo,$seleccionando){
    
    	$filter = new stdClass();
    	$filtero = new stdClass();
    	$usuario =$this->usuario;
    	$fechaRegistro = mdate("%Y-%m-%d ", time());
    	
    	$cuantaContableGirador = $this->input->post('cuentacontable_1');
//     	echo "<script>alert('cuantaContableGirador :   ".$cuantaContableGirador."')</script>";
    	
    	     	if(count($cuantaContableGirador)>0){
	    			foreach ($cuantaContableGirador as $indice => $valor) {
	    				
	    				if($seleccionando == 10){
	    					$filter->CAJA_Codigo=$codigo;
	    				}
	    				if($seleccionando == 20){
	    					$filter->DIREP_Codigo=$codigo;
	    				}
	    				if($seleccionando == 30){
	    					$filter->PROVP_Codigo=$codigo;
	    				}
	    				if($seleccionando == 40){
	    					$filter->CLIP_Codigo=$codigo;
	    				}
	    				
	    				$MovimientoDineros = $this->input->post('movimientoDinero');
	    				if($MovimientoDineros == 1)
	    					$TipoBeneficiario = "G";
	    				else	$TipoBeneficiario = "B";
	    				
	    				
	    				$GiradorBeneficiario = $TipoBeneficiario; // GIRADOR = G ; BENEFICIARIO = B
	    				 
	    				$codigobuscarcaja = $this->movimiento_model->buscar_cajamovimiento($codigo,$GiradorBeneficiario);
	    				
	    				
			    		if($codigobuscarcaja !=0){
			    					echo "<script>alert('no guardar por que ya existe')</script>";
			    		}else{
			    			/***** CJI_RESPONSABLEMOVIMIENTO ******/
			    			
			    			$fechaIngreso = $this->input->post('txtfechaingreso_1');
			    			$fechaIngresoGene = "";
			    			
			    			if(trim($fechaIngreso) == ""){
			    				$fechaIngresoGene = $this->input->post('txtfechaingreso_2');
			    				 
			    			}else{
			    				$fechaIngresoGene = $this->input->post('txtfechaingreso_1');
			    			}
			    			
			    			$filter->RESPNMOV_TipBenefi = $GiradorBeneficiario[$indice];
			    			$filter->RESPNMOV_FechaIngreso = $fechaIngresoGene[$indice];
			    			$filter->RESPNMOV_CodigoUsuario = $usuario;
			    			$filter->RESPNMOV_FlagEstado = "1"; 
			    			$codigomovimiento = $this->movimiento_model->insertar_responsablevimiento($filter);
			    			
			    			
			    			/***** CJI_CAJAMOVIMIENTO ****/
			    			$filtero->RESPMOV_Codigo = $codigomovimiento;
			    			
			    			$CodigoTipoResponsable = $this->input->post('seleccionando_1');
			    			
			    			$codigoCajaDiaria = $this->input->post('cajadiaria_2');
			    			
			    			$cuentaContableCodigo_G = $this->input->post('cuentacontable_1');
			    			$cuentaContableCodigo_B = $this->input->post('cuentacontable_2');
			    			
			    			$cuentaCodigo_G = $this->input->post('idcmbcuentas_1');
			    			if($cuentaCodigo_G == "")
			    				$cuentaCodigo_G = 0;
			    			else $cuentaCodigo_G = $cuentaCodigo_G[$indice];
			    				
			    			$cuentaCodigo_B = $this->input->post('idcuentacaja_2');
			    			if($cuentaCodigo_B == "")
			    				$cuentaCodigo_B = 0;
			    			else $cuentaCodigo_B = $cuentaCodigo_B[$indice];
			    			
			    			$TipoMoneda_G = $this->input->post('tipomoneda_1');
			    			$TipoMoneda_B = $this->input->post('tipomoneda_2');
			    			
			    			$CajaMonto_G = $this->input->post('monto_1');
			    			$CajaMonto_B = $this->input->post('monto_2');
			    			
			    			
			    			
			    			$movimientodinero = $this->input->post('movimientoDinero');
			    			if($movimientodinero == 1){
			    				$filtero->CAJAMOV_MovDinero = 1;
			    			}else{
			    				$filtero->CAJAMOV_MovDinero = 2;
			    			}
			    			
			    			//$formapago_B = 0;
			    			$formapago_G = $this->input->post('formapago_1');
			    			if($formapago_G == "")
			    				$formapago_G = 0;
			    				else $formapago_G = $formapago_G[$indice];
			    				
			    			$formapago_B = $this->input->post('formapago_2');
			    				if($formapago_B == "")
			    					$formapago_B = 0;
			    					else $formapago_B = $formapago_B[$indice];
			    					
			    					echo "<script>alert('formapago_B     :      ".$formapago_B."')</script>";
			    					
			    			$fechaingreso = $this->input->post('txtfechaingreso_1');
			    			$justificacion = $this->input->post('txtjustificacion');
			    			$observacion = $this->input->post('txtobservacion');
			    						    			
			    			$filtero->CAJAMOV_TipoRespo = $CodigoTipoResponsable[$indice];
			    			$filtero->CAJA_Codigo = $codigoCajaDiaria[$indice];
			    			$filtero->CUNTCONTBL_Codigo_G = $cuentaContableCodigo_G[$indice];
			    			$filtero->CUNTCONTBL_Codigo_B = $cuentaContableCodigo_B[$indice];
			    			 
			    			$filtero->CUENT_Codigo_G = $cuentaCodigo_G;
			    			$filtero->CUENT_Codigo_B = $cuentaCodigo_B;
			    			 
			    			$filtero->MONED_Codigo_G = $TipoMoneda_G[$indice];
			    			$filtero->MONED_Codigo_B = $TipoMoneda_B[$indice];
			    			 
			    			$filtero->CAJAMOV_Monto_B = $CajaMonto_B[$indice];
			    			$filtero->CAJAMOV_Monto_G = $CajaMonto_G[$indice];
			    			
			    			$filtero->CAJAMOV_FormaPago_G = $formapago_G;
			    			$filtero->CAJAMOV_FormaPago_B = $formapago_B;
			    			
			    			$filtero->CAJAMOV_FechaSistema = $fechaIngresoGene[$indice];
			    			$filtero->CAJAMOV_Justificacion = $justificacion;
			    			$filtero->CAJAMOV_Observacion = $observacion;
			    			$filtero->CAJAMOV_CodigoUsuario = $usuario;
			    			
			    			$this->movimiento_model->insertar_cajamovimiento($filtero);
			    					
			    		}
	    			}
    	      }
    	}
    


public function buscarcaja_movimiento($codigo='',$estado='',$j=''){ //$codigo='',

// 	echo "<script>alert('Estadoss   : ".$estado."')</script>";
	
	/*************/
	$conf['per_page']  		= 10;
	$conf['num_links']  	= 3;
	$conf['next_link'] 		= "&gt;";
	$conf['prev_link'] 		= "&lt;";
	$conf['first_link'] 	= "&lt;&lt;";
	$conf['last_link']  	= "&gt;&gt;";
	$conf['uri_segment'] 	= 4;
	$this->pagination->initialize($conf);
	
	if($codigo != 00 && $estado != "sele" && $estado != "2"){
		$listado_cajas 	= $this->movimiento_model->listaDeCajaCod_Estado($codigo,$estado,$conf['per_page'],$j);
	}else{
		if($codigo != 00  && $estado == "2"){
			$listado_cajas 	= $this->movimiento_model->listaDeCajaCod_Estado3($codigo,$conf['per_page'],$j);
		}else{
			if($codigo != "00"){
				$listado_cajas 	= $this->movimiento_model->listarnombrecaja_codigo($codigo,$conf['per_page'],$j);
			}else{
				if($estado != "sele"){
					$listado_cajas 	= $this->movimiento_model->listaEstado_cajamovi($estado,$conf['per_page'],$j);
						
					if($estado == 2){
						$listado_cajas 	= $this->movimiento_model->listaEstadoTotal_cajamovi($conf['per_page'],$j);
					}
				}else{
					$listado_cajas 	= $this->movimiento_model->lista_cajamovimiento($conf['per_page'],$j);
			
				}
			}
		}
		
		
	}
	
	
	
	
	$listacuentacontable = $this->movimiento_model->lista_cuentacontable();
	$listaDeMonedas = $this->moneda_model->listartipomoneda();
	
	$data['nombres']    	= "";
	$data['titulo']         = "Nuevo Movimiento";
	$data['cboNombreCaja'] = $this->OPTION_generador($this->movimiento_model->combo_cajanuevo(), 'CAJA_Codigo','CAJA_Nombre','');
	
	$item   = $j+1;
	$lista  = array();
	if(count($listado_cajas)>0){
		foreach($listado_cajas as $indice=>$valor){
			$codigocajaMovimiento = $valor->CAJAMOV_Codigo;
	
			$nombre 		= $valor->caja_nombre;
			$monedacodigo         = $valor->MONED_Codigo_G;
			$monedacodigo_B         = $valor->MONED_Codigo_B;
			$moneda = 0;
	
			foreach ($listaDeMonedas as $monedita=>$valormoneda){
	
				$codigomoneda = $valormoneda->moned_codigo;
				if($codigomoneda == $monedacodigo)
					$moneda = $valormoneda->moned_descripcion;
					 
					if($codigomoneda == $monedacodigo_B)
						$moneda = $valormoneda->moned_descripcion;
			}
	
	
			$monto_G = $valor->CAJAMOV_Monto_G;
			$monto_B = $valor->CAJAMOV_Monto_B;
	
			$monto = $monto_G + " " + $monto_B;
	
			$fechasistema = $valor->cajamov_fechaSistema;
			$cuentaContable =	$valor->CUNTCONTBL_Codigo_G;
			$cuentaContable_B = $valor->CUNTCONTBL_Codigo_B;
	
			
			$cuentaContablenombre = 0;
			foreach ($listacuentacontable as $indice1=>$valor1){
				$codigocuentaconta = $valor1->CUNTCONTBL_Codigo;
				if($cuentaContable == $codigocuentaconta)
					$cuentaContablenombre = $valor1->CUNTCONTBL_Descripcion;
					 
					if($cuentaContable_B == $codigocuentaconta)
						$cuentaContablenombre = $valor1->CUNTCONTBL_Descripcion;
			}
	
			$MoviDinero = $valor->CAJAMOV_MovDinero;
			if($MoviDinero == 1){
				$MoviDineroNombre = "<i style='background:green;color:white;'> INGRESO </i>";
			}else{
				$MoviDineroNombre = "<i style='background:red;color:white;'> SALIDA </i>";
			}
			
			$falgEstado = $valor->CAJAMOV_FlagEstado;
// 			echo "<script>alert('falgEstado :  ".$falgEstado."')</script>";
			if($falgEstado != 0){
				$eliminar       = "<a href='javascript:;' onclick='eliminar_cajamovimineto(".$codigocajaMovimiento.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
			}else{
				$eliminar = "ELIMINADO";
			}
			
			$lista[]        = array($item,$nombre,$moneda,$monto,$fechasistema,$cuentaContablenombre,$MoviDineroNombre,$eliminar);
			$item++;
		}
	}
	
	$data['lista'] 		= $lista;
	$this->layout->view("tesoreria/movimiento_index",$data);
	
}


public function obtenerBancosCajaDiaria(){
	$codigo = $this->input->post('codigo');
	//	echo "<script>alert('codig prub epasdasd : ".$codigo."')</script>";
	$result = array();
	$verificartipoCuenta=0;

	if($codigo!=null && count(trim($codigo))>0){
		$listado_bancos = $this->movimiento_model->buscar_bancos_caja($codigo);
		if($listado_bancos !=null && count($listado_bancos)){
			foreach($listado_bancos as $indice=>$valor){
				$banconombre		= $valor->BANC_Nombre;
				$bancocodigo =	$valor->BANP_Codigo;
				$cajacodigo =	$valor->CAJA_Codigo;
				$result[] = array("banconombre" => $banconombre , "bancocodigo" => $bancocodigo , "cajacodigo" => $cajacodigo  );

			}
		}
	}
	echo json_encode($result);
}

	public function obtenerCuentasCajaDiaria(){
		$codigocaja = $this->input->post('codigocaja');
		$codigobanco = $this->input->post('codigobanco');
		
	//	echo "<script>alert('codig prub epasdasd : ".$codigo."')</script>";
		$result = array();
		$verificartipoCuenta=0;
		
		if($codigocaja!=null && count(trim($codigocaja))>0){
			$listado_cajas = $this->movimiento_model->buscar_caja_codigo($codigocaja,$codigobanco);
			if($listado_cajas !=null && count($listado_cajas)){
				foreach($listado_cajas as $indice=>$valor){
					$numerocaja 		= $valor->CUENT_NumeroEmpresa;
					$cuentaempresas =	$valor->CUENT_Codigo;
							$result[] = array("numerocaja" => $numerocaja , "codigo" => $cuentaempresas  );
		
				}
			}
		}
		echo json_encode($result);
	}
	
	public function obtenerDatosCajaDiaria(){
		$nombrecuentacaja = $this->input->post('nombrecuentacaja');
	
		//	echo "<script>alert('codig prub epasdasd : ".$codigo."')</script>";
		$result = array();
	
		if($nombrecuentacaja!=null && count(trim($nombrecuentacaja))>0){
			$listado_cajas = $this->movimiento_model->obtener_cajadiaria($nombrecuentacaja);
			if($listado_cajas !=null && count($listado_cajas)){
				foreach($listado_cajas as $indice=>$valor){
					$banconombre 		= $valor->BANC_Nombre;
					$numeroempresa =	$valor->CUENT_NumeroEmpresa;
					$tipocuenta 		= $valor->CUENT_TipoCuenta;
					$monedadescripcion =	$valor->MONED_Descripcion;
					$result[] = array("banconombre" => $banconombre , "numeroempresa" => $numeroempresa, "tipocuenta" => $tipocuenta , "monedadescripcion" => $monedadescripcion  );
	
				}
			}
		}
		echo json_encode($result);
	}
	
    public function eliminar_cajamovimineto($codigo){
    	$codigo = $this->input->post('cajacodigomov');
    	$this->movimiento_model->eliminar_cajamovimiento($codigo);
    }

    public function editar_cajamovimineto($caja,$MovDinero){

    	if($MovDinero == 1){//INGRESO DE DINERO
    		$data['movimientoDinero'] = 1;
    		$data['titulo']  = "MODIFICAR CAJA INGRESO";
    		$datos_caja      = $this->movimiento_model->obtener_datosCajaMovimiento($caja);
    		$datos_cajaDiariaB      = $this->movimiento_model->obtener_datosCajaDiaria($caja);
    		
    		$CodigoEmpresa = $datos_caja[0]->EMPRE_Codigo;
    		$CodigoPersona = $datos_caja[0]->PERSP_Codigo;
				if($CodigoPersona != 0)    	
					$BUscarBanco = $datos_caja[0]->PERSP_Codigo;
	
	
				if($CodigoEmpresa != 0)
					$BUscarBanco = $datos_caja[0]->EMPRE_Codigo;

			$cajaCodigo = $datos_cajaDiariaB[0]->CAJA_Codigo;
			$bancoCodigo = $datos_cajaDiariaB[0]->BANP_Codigo;
				
			$bancoCodigoG = $datos_caja[0]->BANP_Codigo;
				
			
    		$data['cmbformapago'] = $datos_caja[0]->CAJAMOV_FormaPago_G;
    		$data['cbomonedamovmiento'] = $this->OPTION_generador($this->moneda_model->listartipomoneda(), 'moned_codigo','moned_descripcion',$datos_caja[0]->MONED_Codigo_G);
    		$data['monto'] = $datos_caja[0]->CAJAMOV_Monto_G;
    		$data['cboCuentaContable'] = $this->OPTION_generador($this->movimiento_model->lista_cuentacontable(), 'CUNTCONTBL_Codigo','CUNTCONTBL_Descripcion',$datos_caja[0]->CUNTCONTBL_Codigo_G);
    		$data['fechaingreso'] = $datos_caja[0]->cajamov_fechaSistema;
//     		     		echo "<script>alert('".$datos_caja[0]->CUNTCONTBL_Codigo_G."')</script>";
    		
			$data['estado'] = $datos_caja[0]->CAJAMOV_TipoRespo;    		
    		$data['nombrecliente'] = $datos_caja[0]->EMPRC_RazonSocial;
    		$data['bancosmovmientos'] = $this->OPTION_generador($this->movimiento_model->listaBancoTR($BUscarBanco), 'BANP_Codigo','BANC_Nombre',$datos_caja[0]->BANP_Codigo);
    		$data['cuentamoviminetos'] = $this->OPTION_generador($this->movimiento_model->listaBancoCajaTR($BUscarBanco,$bancoCodigoG), 'CUENT_Codigo','CUENT_NumeroEmpresa',$datos_caja[0]->CUENT_Codigo);
    		$data['mendamovi'] = $datos_caja[0]->MONED_Descripcion;
    		$data['tipocuentamovi'] = $datos_caja[0]->CUENT_TipoCuenta;
    		
//      		echo "<script>alert('".$datos_caja[0]->EMPRE_Codigo."')</script>";
     		
    		$data['cboResponsable'] = $this->OPTION_generador($this->movimiento_model->listCajatotal(), 'CAJA_Codigo','CAJA_Nombre',$datos_cajaDiariaB[0]->CAJA_Codigo);
    		$data['cboBancoCaja'] = $this->OPTION_generador($this->movimiento_model->listaBanco($cajaCodigo), 'BANP_Codigo','BANC_Nombre',$datos_cajaDiariaB[0]->BANP_Codigo);
    		$data['cboCuentaCaja'] = $this->OPTION_generador($this->movimiento_model->listaBancoCaja($cajaCodigo,$bancoCodigo), 'CUENT_Codigo','CUENT_NumeroEmpresa',$datos_cajaDiariaB[0]->CUENT_Codigo);
    		
    		$data['txtobservacion']  = $datos_caja[0]->CAJAMOV_Observacion;
    		$data['txtjustificacion'] = $datos_caja[0]->CAJAMOV_Justificacion;
    		
    		
    		$data['hidden'] = "";
    	}
    	//$data['modo']	 = "modificar";
    //	$data['id']	  	 = $this->input->post('id');
//     	$data['display'] = "";
     
//     	$codigoCaja		 = $datos_caja[0]->CAJA_Codigo;
//     	$cboTipCaja		 = $datos_caja[0]->tipCa_codigo;
//     	$nombreCaja   	 = $datos_caja[0]->CAJA_Nombre;
//     	$cboResponsable  = $datos_caja[0]->CODIGO_Directorio;
    	//echo "<script>alert('EditarCara : ".$cboResponsable."')</script>";
    	
//     	$objeto                 = new stdClass();
//     	$objeto->id             = $datos_caja[0]->CAJA_Codigo;
//     	$data['datos']    		= $objeto;
//     	$data['nombreCaja']   	= $nombreCaja;
//     	//     	
//     	$data['cboResponsable'] = $this->OPTION_generador($this->directivo_model->combo_directivos(), 'DIREP_Codigo','PERSC_Nombre',$datos_caja[0]->CODIGO_Directorio);
//     	$data['cboTipCaja'] 	= $this->OPTION_generador($this->caja_model->listar_tipoCaja(), 'tipCa_codigo', 'tipCa_Descripcion', $datos_caja[0]->tipCa_codigo);
    
//     	$compania 				= $this->session->userdata('compania');

//     	$data['cboBancos'] 		= $this->OPTION_generador($this->banco_model->listar_banco($objetos), 'BANP_Codigo', 'BANC_Nombre', '');
//     	$data['limiteRetiro'] 	= "";
//     	$data['descripcion'] 	= "";
    	 
//     	/** OBTENER DATOS DE CAJA CUENTA **/
//     	$detalle_cuenta 	  		= $this->listar_detalle_cuenta($caja);
//     	$data['detalle_cuenta']     = $detalle_cuenta;
//     	//$data['detalle_cuenta'] = $this->caja_model->obtener_cuenta_caja2($caja);
//     	/** OBTENER DATOS DE CHEQUERA CUENTA **/
//     	$detalle_chequera 	  		= $this->listar_detalle_chequera($caja);
//     	$data['detalle_chequera']     = $detalle_chequera;
    	 
    	 
    	$this->load->view("tesoreria/movimiento_nuevo",$data);
    	
    }

    public function listar_cuentas($id_banco){
        echo json_encode($this->caja_model->listar_cuenta_banco($id_banco));
    }

    public function buscar_movimiento_banco($id_banco, $id_cuenta='',$page = 1) {

    /*************/
    $conf['per_page']       = $per_page = 10;
    $conf['num_links']      = 3;
    $conf['next_link']      = "&gt;";
    $conf['prev_link']      = "&lt;";
    $conf['first_link']     = "&lt;&lt;";
    $conf['last_link']      = "&gt;&gt;";
    $conf['uri_segment']    = 4;
    $this->pagination->initialize($conf);

    $filter = array();
    $filter["id_banco"] = $id_banco;
    if($id_cuenta != "") $filter["id_cuenta"] = $id_cuenta;

    $movimientos = $this->banco_model->listar_movimientos($filter, $page, $per_page);
    
    $data['nombres']        = "";
    $data['titulo']         = "Movimiento bancos";
    $data['cboBancos'] = $this->OPTION_generador($this->banco_model->listar_by_id_empresa($this->session->userdata("empresa")), 'BANP_Codigo','BANC_Nombre','');

    $resumen = array(
            "ingresos" => array(
                "soles" => 0,
                "dolares" => 0,
                "saldo" => 0
            ),
            "salidas" => array(
                "soles" => 0,
                "dolares" => 0,
                "saldo" => 0
            )
        );

        foreach ($movimientos as $key => $movimiento) {
            $movimiento->razonSocial = "";

            if($movimiento->CLIP_Codigo && $movimiento->CLIP_Codigo != 0) {

                if(!isset($clients_list[$movimiento->CLIP_Codigo])) $clients_list[$movimiento->CLIP_Codigo] = $this->cliente_model->obtener($movimiento->CLIP_Codigo);
                $cliente = $clients_list[$movimiento->CLIP_Codigo];
                $movimiento->razonSocial = $cliente->nombre;
            }

            if($movimiento->PROVP_Codigo && $movimiento->PROVP_Codigo != 0) {

                if(!isset($proveedores_list[$movimiento->PROVP_Codigo])) $proveedores_list[$movimiento->PROVP_Codigo] = $this->proveedor_model->get_by_id($movimiento->PROVP_Codigo);
                $proveedor = $proveedores_list[$movimiento->PROVP_Codigo];
                $movimiento->razonSocial = $proveedor->nombre;
            }

            //calculando resumen
            if($movimiento->CAJAMOV_MovDinero == 1) {
                if($movimiento->MONED_Codigo == 1) $resumen["salidas"]["soles"] += $movimiento->PAGC_Monto;
                if($movimiento->MONED_Codigo == 2)  $resumen["salidas"]["dolares"] += $movimiento->PAGC_Monto;
            }else {
                if($movimiento->MONED_Codigo == 1) $resumen["ingresos"]["soles"] += $movimiento->PAGC_Monto;
                if($movimiento->MONED_Codigo == 2)  $resumen["ingresos"]["dolares"] += $movimiento->PAGC_Monto;
            }
        }

        $data["resumen"] = $resumen;
    
    $data['lista']      = $movimientos;
    $this->layout->view("tesoreria/banco_movimiento",$data);
    
}

    
}       
?>