<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */

class Cliente extends CI_Controller{

    ##  -> Begin
    private $empresa;
    private $compania;
    private $url;
    private $view_js = NULL;
    ##  -> End

    ##  -> Begin
    public function __construct(){
        parent::__construct();
        $this->load->model('empresa/proveedor_model');
        $this->load->model('maestros/cargo_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/tarifa_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/area_model');
        $this->load->model('maestros/tipoestablecimiento_model');
        $this->load->model('maestros/emprestablecimiento_model');
        $this->load->model('maestros/nacionalidad_model');
        $this->load->model('maestros/tipodocumento_model');
        $this->load->model('maestros/tipocodigo_model');
        $this->load->model('maestros/estadocivil_model');
        $this->load->model('maestros/ubigeo_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/comercial_model');
        $this->load->model('tesoreria/banco_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('maestros/tipocliente_model');
        $this->load->library('html');
        $this->load->library('table');
        $this->load->library('lib_props');
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->url = base_url();
        $this->view_js = array(0 => "empresa/cliente.js");
    }
    ##  -> End;

    ##  -> Begin
    public function index() {
            $this->clientes();
    }
    ##  -> End

    ##  -> Begin
    public function clientes( $tipo = "C" ){
        
        //Definimos si es cliente o abonado
        if($tipo == "C"){
            $data["tipo_clienteabonado"] = 9;
        }
        else if($tipo == "A"){
            $data["tipo_clienteabonado"] = 8;
        }
        $data["categorias_cliente"] = $this->tipocliente_model->getCategorias();
        
    ## SELECTS
        $data["documentosNatural"]  = $this->tipodocumento_model->listar_tipo_documento();
        $data["documentosJuridico"] = $this->tipocodigo_model->listar_tipo_codigo();
        $data['edo_civil']    = $this->estadocivil_model->listar_estadoCivil();
        $data['nacionalidad'] = $this->nacionalidad_model->listar_nacionalidad();
        $data["cargos"] = $this->cargo_model->getCargos();
        $data["bancos"] = $this->banco_model->listar_banco();
        $data["sector_comercial"]     = $this->comercial_model->getComercials();
        $data["tipo_establecimiento"] = $this->tipoestablecimiento_model->getTipoEstablecimientos();
        $data["forma_pago"] = $this->formapago_model->getFpagos();
        $data["monedas"]    = $this->moneda_model->listar();
        $data["vendedor"]  = $this->directivo_model->listarVendedores();
        $filter = new stdClass();
        $filter->tipo_tarifa = $tipo == "C" ? 3 : 2;
        $data["tarifas"]   = $this->tarifa_model->getTarifasTotal($filter);
        $data["departamentos"] = $this->ubigeo_model->listar_departamentos();
        $data["provincias"]    = $this->ubigeo_model->getProvincias("15");
        $data["distritos"]     = $this->ubigeo_model->getDistritos("15","01");
        $data['scripts']       = $this->view_js;
        $data['titulo_tabla']    = $tipo == "C"?"RELACIÓN DE CLIENTES":"RELACIÓN DE ABONADOS";
        $data['titulo_busqueda'] = $tipo == "C"?"BUSCAR CLIENTES":"BUSCAR ABONADOS";
        $this->layout->view('empresa/cliente_index',$data);
    }
    ##  -> End

    ##  -> Begin
    public function datatable_cliente(){
        
            $tipo_clienteabonado = $this->input->post('tipo_clienteabonado');
        
            $posDT = -1;
            
            if($tipo_clienteabonado == 8){
                $columnas = array(
                        ++$posDT => "CLIC_CodigoUsuario",
                        ++$posDT => "documento",
                        ++$posDT => "numero",
                        ++$posDT => "razon_social",
                        ++$posDT => "CLIC_FlagSituracion"
                ); 
            }
            else{
                $columnas = array(
                        ++$posDT => "CLIC_CodigoUsuario",
                        ++$posDT => "documento",
                        ++$posDT => "numero",
                        ++$posDT => "razon_social",
            );                
            }

            //Listar clientes
            $filter = new stdClass();
            $filter->start = $this->input->post("start");
            $filter->length = $this->input->post("length");
            $filter->search = $this->input->post("search")["value"];
            $ordenar = $this->input->post("order")[0]["column"];
            if ($ordenar != ""){
                    $filter->order = $columnas[$ordenar];
                    $filter->dir = $this->input->post("order")[0]["dir"];
            }
            $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
            $filter->codigo    = $this->input->post('codigo');
            $filter->documento = $this->input->post('documento');
            $filter->nombre    = $this->input->post('nombre');
            $filter->placa     = strtoupper($this->input->post('placa'));
            $filter->tipo_clienteabonado = $tipo_clienteabonado;
            $clienteInfo = $this->cliente_model->getClientes($filter);
            
            $records = array();
            if ($clienteInfo["records"] != NULL) {
                foreach ($clienteInfo["records"] as $indice => $valor) {
                    
                    $btn_editar = "<button type='button' onclick='editar_cliente($valor->CLIP_Codigo)' class='btn btn-default'>
                    <img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
                    </button>";

                    $btn_sucursales = ($valor->CLIC_TipoPersona == 0) ? "" : "<button type='button' onclick='sucursales($valor->EMPRP_Codigo,\"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Sucursales'>
                    <img src='".$this->url."public/images/icons/sucursal.png' class='image-size-1b'>
                    </button>";

                    $btn_contactos = ($valor->CLIC_TipoPersona == 0) ? "" : "<button type='button' onclick='modal_contactos(\"$valor->EMPRP_Codigo\", \"$valor->PERSP_Codigo\",\"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Abonados'>
                    <img src='".$this->url."public/images/icons/contactos.png' class='image-size-1b'>
                    </button>";

                    $btn_vehiculos = "<button type='button' onclick='modal_vehiculos(\"$valor->CLIP_Codigo\", \"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Vehiculos'><img src='".$this->url."public/images/icons/carro.png' class='image-size-1b'>
                    </button>";

                    $btn_bancos = "<button type='button' onclick='modal_CtasBancarias(\"$valor->EMPRP_Codigo\", \"$valor->PERSP_Codigo\", \"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Bancos'>
                    <img src='".$this->url."public/images/icons/banco.png' class='image-size-1b'>
                    </button>";

                    $btn_deshabilitar = "<button type='button' onclick='deshabilitar_cliente($valor->CLIP_Codigo)' class='btn btn-default'>
                    <img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
                    </button>";

                    $btn_documentos = "<button type='button' onclick='docs_emitidos($valor->CLIP_Codigo, \"$valor->numero\", \"$valor->razon_social\",\"$valor->CLIC_FlagSituracion\",\"$valor->CLIC_TipoPersona\")' class='btn btn-default'>
                    <img src='".$this->url."public/images/icons/icono-documentos.png' class='image-size-1b'>
                </button>";

                    $situacion = $valor->CLIC_FlagSituracion;
                    $msgSituacion = "";

                    switch($situacion){
                        case 0:
                            $msgSituacion = "<span class='bold color-red'>PENDIENTE</span>";
                            break;
                        case 1:
                            $msgSituacion = "<span class='bold color-green'>FACTURADO</span>";
                            break;
                        case 2:
                            $msgSituacion = "<span class='color-black'>SIN CALIF.</span>";
                            break;
                    }

                    $posDT = -1;
                    
                    if($tipo_clienteabonado == 8){//Es abonado
                        
                        $records[] = array(
                                ++$posDT => $valor->CLIC_CodigoUsuario,
                                ++$posDT => $valor->documento,
                                ++$posDT => $valor->numero,
                                ++$posDT => $valor->razon_social,
                                ++$posDT => $valor->Placas,
                                ++$posDT => $msgSituacion,
                                ++$posDT => $btn_editar,
                                ++$posDT => $btn_documentos,
                                //++$posDT => $btn_bancos,
                                ++$posDT => $btn_vehiculos,
                                ++$posDT => $btn_deshabilitar
                        );
                        
                    }
                    else{
                        
                        $records[] = array(
                                ++$posDT => $valor->CLIC_CodigoUsuario,
                                ++$posDT => $valor->documento,
                                ++$posDT => $valor->numero,
                                ++$posDT => $valor->razon_social,
                                ++$posDT => $valor->Placas,
                                ++$posDT => $btn_editar,
                                ++$posDT => $btn_documentos,
                                //++$posDT => $btn_bancos,
                                ++$posDT => $btn_vehiculos,
                                ++$posDT => $btn_deshabilitar
                        );                        
                        
                    }
                }
            }

            $recordsTotal = ( $clienteInfo["recordsTotal"] != NULL ) ? $clienteInfo["recordsTotal"] : 0;
            $recordsFilter = $clienteInfo["recordsFilter"];

            $json = array(
                    "draw"            => intval( $this->input->post('draw') ),
                    "recordsTotal"    => $recordsTotal,
                    "recordsFiltered" => $recordsFilter,
                    "data"            => $records
            );

            echo json_encode($json);
            die();
    }
    ##  -> End

    public function datatable_vehiculos(){
        
        $posDT = -1;
        $columnas = array(
                ++$posDT => "CLIEVEHIP_Nombres",
                ++$posDT => "CLIEVEHIP_Placa",
                ++$posDT => "TARIFC_Descripcion",
                ++$posDT => "CLIEVEHIP_Telefono"
        );

        $filter = new stdClass();
        $filter->start = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];

        $ordenar = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
                $filter->order = $columnas[$ordenar];
                $filter->dir = $this->input->post("order")[0]["dir"];
        }

        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;

        $filter->cliente = $this->input->post('cliente');

        $vehiculosInfo = $this->cliente_model->getVehiculos($filter);

        $records = array();
        if ( $vehiculosInfo["records"] != NULL) {
                foreach ($vehiculosInfo["records"] as $indice => $valor) {
                        $btn_editar = "<button type='button' onclick='editar_vehiculo($valor->CLIEVEHIP_Codigo)' class='btn btn-default'>
                        <img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
                        </button>";

                        $btn_deshabilitar = "<button type='button' onclick='deshabilitar_vehiculo($valor->CLIEVEHIP_Codigo)' class='btn btn-default'>
                        <img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
                        </button>";

                        $posDT = -1;
                        $records[] = array(
                                ++$posDT => $valor->CLIEVEHIP_Nombres,                                    
                                ++$posDT => $valor->CLIEVEHIP_Placa,
                                ++$posDT => $valor->TARIFC_Descripcion,                                    
                                ++$posDT => $valor->CLIEVEHIP_Telefono, 
                                ++$posDT => $btn_editar,
                                ++$posDT => $btn_deshabilitar
                        );
                }
        }

        $recordsTotal = ( $vehiculosInfo["recordsTotal"] != NULL ) ? $vehiculosInfo["recordsTotal"] : 0;
        $recordsFilter = $vehiculosInfo["recordsFilter"];

        $json = array(
                "draw"            => intval( $this->input->post('draw') ),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFilter,
                "data"            => $records
        );

        echo json_encode($json);
        die(); 
    }
    
    ##  -> Begin
    public function getDocumentos(){
    # 0 : NATURAL | 1 : JURIDICO
            $tipo = $this->input->post("tipo");
            $documentos = NULL;

            if ($tipo == "0")
                    $info = $this->tipodocumento_model->listar_tipo_documento();
            else
                    $info = $this->tipocodigo_model->listar_tipo_codigo();

            if ($info != NULL){
                    if ($tipo == "0"){
                            foreach ($info as $key => $val)
                                    $documentos[] = array("codigo" => $val->TIPDOCP_Codigo, "inicial" => $val->TIPOCC_Inciales);
                    }
                    else{
                            foreach ($info as $key => $val)
                                    $documentos[] = array("codigo" => $val->TIPCOD_Codigo, "inicial" => $val->TIPCOD_Inciales);
                    }

                    $json = array("match" => true, "documentos" => $documentos);
            }
            else
                    $json = array("match" => true, "documentos" => $documentos);

            echo json_encode($json);
    }
    ##  -> End

    
    ##  -> Begin
    public function getCliente(){
            $cliente = $this->input->post("cliente");
            $clienteInfo = $this->cliente_model->getCliente($cliente);

            if ($clienteInfo != NULL){
                    foreach ($clienteInfo as $key => $val)
                            $info = array(
                                    "cliente" => $val->CLIP_Codigo,
                                    "tipo_cliente" => $val->CLIC_TipoPersona,
                                    "tipo_documento" => $val->tipo_documento,
                                    "numero_documento" => $val->numero,

                                    "razon_social" => $val->razon_social,
                                    "nombres" => $val->PERSC_Nombre,
                                    "apellido_paterno" => $val->PERSC_ApellidoPaterno,
                                    "apellido_materno" => $val->PERSC_ApellidoMaterno,

                                    "genero" => $val->PERSC_Sexo,
                                    "edo_civil" => $val->ESTCP_EstadoCivil,
                                    "nacionalidad" => $val->NACP_Nacionalidad,

                                    "direccion" => $val->direccion,

                                    "departamento" => substr($val->ubigeo, 0, 2),
                                    "provincia" => substr($val->ubigeo, 2, 2),
                                    "distrito" => substr($val->ubigeo, 4, 2),

                                    "idcliente" => $val->CLIC_CodigoUsuario,
                                    "vendedor" => $val->CLIC_Vendedor,
                                    "sector_comercial" => $val->SECCOMP_Codigo,
                                    "forma_pago" => $val->FORPAP_Codigo,
                                    "categoria" => $val->TIPCLIP_Codigo,
                                    "fecha_ingreso" => $val->CLIC_FechaIngreso,
                                    "monto_facturado" => $val->CLIC_Monto,

                                    "telefono" => $val->telefono,
                                    "movil" => $val->movil,
                                    "fax" => $val->fax,
                                    "correo" => $val->correo,
                                    "web" => $val->web
                            );

                    $json = array("match" => true, "info" => $info);
            }
            else
                    $json = array("match" => true, "info" => NULL);

            echo json_encode($json);
    }
    ##  -> End

    public function getVehiculo(){
        $vehiculo = $this->input->post("vehiculo");
          $vehiculoInfo["records"] = $this->cliente_model->getVehiculo($vehiculo);
          if ($vehiculoInfo["records"] != NULL){
              $vehiculos = $vehiculoInfo["records"];
              foreach ($vehiculos as $key => $val)    
                  $info = array(
                      "vehiculo" => $val->CLIEVEHIP_Codigo,
                      "cliente" => $val->CLIP_Codigo,
                      "nombre" => $val->CLIEVEHIP_Nombres,
                      "telefono" => $val->CLIEVEHIP_Telefono,
                      "movil" => $val->CLIEVEHIP_Movil,
                      "placa"  => $val->CLIEVEHIP_Placa,
                      "tarifa" => $val->TARIFP_Codigo,
                      "numerodoc" => $val->CLIEVEHIP_NumeroDocIdentidad
                  );
              $json = array("match" => true, "info" => $info);
          }
          else
              $json = array("match" => true, "info" => NULL);
          echo json_encode($json);
    }
    
    public function getVehiculoXPlacaTotal(){
        $placa   = trim($this->input->post("placa"));
        $cliente = trim($this->input->post("cliente"));

        $filter = new stdClass();
        $filter->placa = $placa;
        $datos_abonado = $this->cliente_model->getClienteVehiculosTotal($filter);
        $records = $datos_abonado["records"];
        
        if($records != NULL){
            
            //La placa existe
            if(count($records) == 1){
                
                $clientereg = $records[0]->CLIP_Codigo;

                if((int)$clientereg != (int)$cliente){
                    $json_result =true;
                    $json_message = 'La placa '.strtoupper($placa).' ya existe.';   
                }
                else{
                    $json_result  = false;
                    $json_message = NULL; 
                }

            }
            else{
                $json_result = true;
                $json_message = 'La placa '.strtoupper($placa).' ya existe.'; 
            }            
            
        }
        else{
            $json_result  = false;
            $json_message = NULL; 
        }
        
        $json = array("result" => $json_result, "message" => $json_message);
        echo json_encode($json);        
        die;        
    }
    
    public function getVehiculoXPlaca(){
        $placa  = trim($this->input->post("placa"));
        $filter = new stdClass();
        $filter->placa = $placa;
        $filter->tipo_clienteabonado = 8;//Tipo Abonados
        $datos_abonado = $this->cliente_model->getClienteVehiculos($filter);
        $records = $datos_abonado["records"];
        
        if($records != NULL){
            foreach ($records as $key => $val){
                
                $msgsituracion = $val->CLIC_FlagSituracion == 1?"FACTURADO":"PENDIENTE";
                
                $info = array(
                    "clientevehiculo" => $val->CLIEVEHIP_Codigo,
                    "cliente"         => $val->CLIP_Codigo,
                    "nombres"          => $val->CLIEVEHIP_Nombres,
                    "telefono"        => $val->CLIEVEHIP_Telefono,
                    "movil"           => $val->CLIEVEHIP_Movil,
                    "dnivehiculo"     => $val->CLIEVEHIP_NumeroDocIdentidad,
                    "placa"           => $val->CLIEVEHIP_Placa,
                    "tarifa"          => $val->TARIFP_Codigo,
                    "hinicio"         => str_replace(':','',$val->TARIFC_Hinicio),
                    "hfin"            => str_replace(':','',$val->TARIFC_Hfin),
                    "monto"           => $val->CLIC_Monto,
                    "fpago"           => $val->CLIC_FechaIngreso,
                    "situacion"       => $msgsituracion    
                );
            }
            $json = array("match" => true, "info" => $info); 
        }
        else{
            $json = array("match" => false, "info" => NULL);
        }
        echo json_encode($json);
        die;
    }    
    
    ##  -> Begin
    public function guardar_registro(){

            $cliente = $this->input->post("cliente");
            $empresa = 0;
            $persona = 0;
            $categoria_ini = 0;

            //Si editamos recuperamos la empresa, persona y categoria inicial
            if ( $cliente != "" ){
                $clienteInfo = $this->cliente_model->getCliente($cliente);
                $empresa = $clienteInfo[0]->EMPRP_Codigo;
                $persona = $clienteInfo[0]->PERSP_Codigo;
                
                //Tipo de cliente inicial: 8 Abonado, 9 cliente
                $categoria_ini = $clienteInfo[0]->TIPCLIP_Codigo;
            }

            $tipo_cliente = $this->input->post("tipo_cliente");
            $tipo_documento = $this->input->post("tipo_documento");
            $numero_documento = $this->input->post("numero_documento");

            $razon_social = strtoupper( $this->input->post("razon_social"));
            $nombres = strtoupper( $this->input->post("nombres") );
            $apellido_paterno = strtoupper( $this->input->post("apellido_paterno") );
            $apellido_materno = strtoupper( $this->input->post("apellido_materno") );

            $genero = $this->input->post("genero");
            $edo_civil = $this->input->post("edo_civil");
            $nacionalidad = $this->input->post("nacionalidad");
            $fecha_nacimiento = $this->input->post("fecha_nacimiento");

            $direccion = strtoupper( $this->input->post("direccion") );
            $departamento = $this->input->post("departamento");
            $provincia = $this->input->post("provincia");
            $distrito = $this->input->post("distrito");
            $ubigeo = $departamento.$provincia.$distrito;

            $idcliente = $this->input->post("idcliente");
            $vendedor = $this->input->post("vendedor");
            $sector_comercial = $this->input->post("sector_comercial");
            $forma_pago = $this->input->post("forma_pago");
            $categoria = $this->input->post("categoria");
            $telefono = $this->input->post("telefono");
            $movil = $this->input->post("movil");
            $fax = $this->input->post("fax");
            $correo = $this->input->post("correo");
            $web = $this->input->post("web");
            
            $fecha_ingreso = $this->input->post("fecha_ingreso_cliente");
            $monto_facturado = $this->input->post("monto_facturado");

## EMPRESA
            $empresaInfo = new stdClass();
            $empresaInfo->CIIUP_Codigo      = 0;
            $empresaInfo->TIPCOD_Codigo     = $tipo_documento;
            $empresaInfo->SECCOMP_Codigo    = $sector_comercial;
            $empresaInfo->EMPRC_Ruc         = $numero_documento;
            $empresaInfo->EMPRC_RazonSocial = $razon_social;
            $empresaInfo->EMPRC_Telefono    = $telefono;
            $empresaInfo->EMPRC_Movil       = $movil;
            $empresaInfo->EMPRC_Fax         = $fax;
            $empresaInfo->EMPRC_Web         = $web;
            $empresaInfo->EMPRC_Email       = $correo;
            $empresaInfo->EMPRC_CtaCteSoles = "";
            $empresaInfo->EMPRC_CtaCteDolares = "";
            $empresaInfo->EMPRC_FlagEstado  = "1";
            $empresaInfo->EMPRC_Direccion   = $direccion;

## PERSONA
            $personaInfo = new stdClass();
            $personaInfo->UBIGP_LugarNacimiento = "000000";
            $personaInfo->UBIGP_Domicilio       = $ubigeo;
            $personaInfo->ESTCP_EstadoCivil     = $edo_civil;
            $personaInfo->NACP_Nacionalidad     = $nacionalidad;
            $personaInfo->PERSC_Nombre          = $nombres;
            $personaInfo->PERSC_ApellidoPaterno = $apellido_paterno;
            $personaInfo->PERSC_ApellidoMaterno = $apellido_materno;
            $personaInfo->PERSC_TipoDocIdentidad = $tipo_documento;
            $personaInfo->PERSC_Ruc             = "";
            $personaInfo->PERSC_NumeroDocIdentidad = $numero_documento;
            $personaInfo->PERSC_FechaNac        = $fecha_nacimiento;
            $personaInfo->PERSC_FechaNacz        = $fecha_nacimiento;
            $personaInfo->PERSC_Direccion       = $direccion;
            $personaInfo->PERSC_Telefono        = $telefono;
            $personaInfo->PERSC_Movil           = $movil;
            $personaInfo->PERSC_Fax             = $fax;
            $personaInfo->PERSC_Email           = $correo;
            $personaInfo->PERSC_Domicilio       = $direccion;
            $personaInfo->PERSC_Web             = $web;
            $personaInfo->PERSC_Sexo            = $genero;
            $personaInfo->PERSC_FlagEstado      = "1";
#$persona->BANP_Codigo           = NULL;

## CLIENTE

            $clienteInfo = new stdClass();
            $clienteInfo->CLIC_TipoPersona   = $tipo_cliente;
            $clienteInfo->TIPCLIP_Codigo     = $categoria;
            $clienteInfo->CLIC_Vendedor      = $vendedor;
            $clienteInfo->FORPAP_Codigo      = $forma_pago;
    # SOLO EN LAZPER, este campo guarda el estado de la empresa en digemid
            $clienteInfo->CLIC_Digemin       = "";
            $clienteInfo->CLIC_flagCalifica  = 1; 
            $clienteInfo->CLIC_FechaIngreso  = $fecha_ingreso;
            $clienteInfo->CLIC_Monto         = $monto_facturado;

## PROVEEDOR

            $proveedorInfo = new stdClass();
            $proveedorInfo->PROVC_TipoPersona = $tipo_cliente;
            $proveedorInfo->PROVC_FlagEstado = "1";

## SUCURSAL

            $sucursalInfo = new stdClass();
            $sucursalInfo->TESTP_Codigo = 1;
            $sucursalInfo->UBIGP_Codigo = $ubigeo;
            $sucursalInfo->EESTABC_Descripcion = "PRINCIPAL";
            $sucursalInfo->EESTAC_Direccion = $direccion;
            $sucursalInfo->EESTABC_FlagTipo = "1";
            $sucursalInfo->EESTABC_FlagEstado = "1";

            $this->db->trans_start();

            //Actualizamos el cliente
            if ($cliente != ""){
                
                //Persona natual o persona juridica
                if ($tipo_cliente == "0"){
                    if ($persona != 0)  $this->persona_model->actualizar_persona($persona, $personaInfo);
                }
                else{
                    if ($empresa != 0){
                            $this->empresa_model->actualizar_empresa($empresa, $empresaInfo);
                            $sucursalInfo->EMPRP_Codigo = $empresa;
                            $establecimiento = $this->emprestablecimiento_model->actualizar_establecimiento_principal($sucursalInfo);
                    }
                }
                
                //Si un cliente se convierte en abonado pasa como pendiente y mes de facturacion NULL
                if($categoria_ini == 9 && $categoria == 8){
                    $clienteInfo->CLIC_FlagSituracion = 0;
                    $clienteInfo->CLIC_MesFacturacion = NULL;        
                }
                
                //Si un abonado se convierte en cliente pasa como pendiente y mes de facturacion NULL
                //Monto de pago 0, fecha de ingreso NULL y forma de pago a NULL 19/04/2021
                if($categoria_ini == 8 && $categoria == 9){
                    $clienteInfo->CLIC_FlagSituracion = 0;
                    $clienteInfo->CLIC_MesFacturacion = NULL;      
                    $clienteInfo->CLIC_Monto          = 0;      
                    $clienteInfo->CLIC_FechaIngreso   = NULL;      
                    $clienteInfo->FORPAP_Codigo       = NULL;      
                }

                $clienteInfo->EMPRP_Codigo  = $empresa;
                $clienteInfo->PERSP_Codigo  = $persona;
                
                $cliente = $this->cliente_model->actualizar_cliente($cliente, $clienteInfo);

                if ($cliente){
                        $json_result = "success";
                        $json_message = "Actualización satisfactoria.";
                }
                else{
                        $json_result = "error";
                        $json_message = "El número de documento $numero_documento, ya se encuentra registrado.";
                }
            }
            //Insertamos cliente
            else{

                    if ( $this->empresa_model->documento_exists($numero_documento) == true ){
                            $json_result = "error";
                            $json_message = "El número de documento $numero_documento, ya se encuentra registrado.";
                    }
                    else{
                            if ($tipo_cliente == "0")
                                    $persona = $this->persona_model->insertar_persona($personaInfo);
                            else{
                                    $empresa = $this->empresa_model->insertar_empresa($empresaInfo);

                                    $sucursalInfo->EMPRP_Codigo = $empresa;
                                    $establecimiento = $this->emprestablecimiento_model->insertar_establecimiento($sucursalInfo);
                            }

                            $clienteInfo->CLIC_CodigoUsuario = $this->generateCodeCliente();
                            $clienteInfo->EMPRP_Codigo  = $empresa;
                            $clienteInfo->PERSP_Codigo  = $persona;
                            //Si es un abonado nuevo, se crea como no facturadeo  
                            if($categoria == 8)      $clienteInfo->CLIC_FlagSituracion = 0; 
                            $proveedorInfo->PERSP_Codigo = $persona;
                            $proveedorInfo->EMPRP_Codigo = $empresa;

                            $cliente = $this->cliente_model->insertar_cliente($clienteInfo);
                            $proveedor = $this->proveedor_model->insertar_proveedor($proveedorInfo);

                            if ($cliente != 0 && $cliente != NULL){
                                    $json_result = "success";
                                    $json_message = "Registro satisfactorio.";
                            }
                            else{
                                    $json_result = "error";
                                    $json_message = "No fue posible registrar al cliente. Intentelo nuevamente";
                            }
                    }
            }

            if($this->db->trans_status() == false)
                $this->db->trans_rollback();
            else
                $this->db->trans_commit();

            $json = array("result" => $json_result, "message" => $json_message);
            echo json_encode($json);
    }
    ##  -> End

    public function guardar_vehiculo(){
        
        $vehiculo = $this->input->post("vehiculo");
        $cliente  = $this->input->post("vehiculo_cliente");
        $nombre = strtoupper( $this->input->post("vehiculo_nombre") );
        $telefono = $this->input->post("vehiculo_telefono");
        $movil = $this->input->post("vehiculo_movil");
        $placa  = $this->input->post("vehiculo_placa");
        $tarifa = $this->input->post("vehiculo_tarifa");
        $numerodoc  = $this->input->post("vehiculo_numerodoc");

        $filter = new stdClass();
        $filter->CLIP_Codigo          = $cliente;
        $filter->CLIEVEHIP_Nombres    = $nombre;
        $filter->CLIEVEHIP_Telefono   = $telefono;
        $filter->CLIEVEHIP_Movil      = $movil;
        $filter->CLIEVEHIP_FlagEstado = "1";
        $filter->CLIEVEHIP_Placa      = strtoupper($placa);
        $filter->TARIFP_Codigo        = $tarifa;
        $filter->CLIEVEHIP_NumeroDocIdentidad = $numerodoc;
        
        //Listamos los vehiculos que tienen la misma PLACA
        $filterVehiculo = new stdClass();
        $filterVehiculo->placa = strtoupper($placa);
        $rsVehiculos = $this->cliente_model->getClienteVehiculosTotal($filterVehiculo);        
        
        if ($vehiculo != ""){
            $filter->CLIEVEHIP_FechaModificacion = date("Y-m-d H:i:s");

            if($rsVehiculos['records'] == NULL){
                
                //Actualizamos los datos de un vehiculo
                $result = $this->cliente_model->actualizar_vehiculo($vehiculo, $filter);   
                if ($result) {
                    $json_result = 'success';
                    $json_message = 'Actualizacion completa.';
                } else {
                    $json_result = 'error';
                    $json_message = 'Error al actualizar los datos.';
                }
                
            }
            else{
                
                //La placa existe
                if(count($rsVehiculos['records']) == 1){
                    $clientereg = $rsVehiculos['records'][0]->CLIP_Codigo;
                    
                    //Es el mismo cliente
                    if($clientereg == $cliente){
                       
                        //Actualizamos los datos de un vehiculo
                        $result = $this->cliente_model->actualizar_vehiculo($vehiculo, $filter);   
                        if ($result) {
                            $json_result = 'success';
                            $json_message = 'Actualizacion completa.';
                        } else {
                            $json_result = 'error';
                            $json_message = 'Error al actualizar los datos.';
                        }                        
                        
                    }
                    else{
                        $json_result = 'error';
                        $json_message = 'Error, la placa '.strtoupper($placa).' ya existe.';   
                    }

                }
                else{
                    $json_result = 'error';
                    $json_message = 'Error, la placa '.strtoupper($placa).' ya existe.'; 
                }
            }
            
        }
        else{
            $filter->CLIEVEHIP_FechaRegistro = date("Y-m-d H:i:s");
            
            //Insertamos vehiculo
            if(count($rsVehiculos['records']) == 0){
                $id = $this->cliente_model->insertar_vehiculo($filter);    
                if ($id) {
                    $json_result = 'success';
                    $json_message = 'Registro completado.';
                } else {
                    $json_result = 'error';
                    $json_message = 'Error al guardar los datos.';
                }
            }
            else{
                $json_result = 'error';
                $json_message = 'Error, la placa '.strtoupper($placa).' ya existe.';
            }
        }
        
        $json = array("result" => $json_result, "message" => $json_message);
        echo json_encode($json);        
        die;
    }
    
	##  -> Begin
	public function deshabilitar_cliente(){
		$cliente = $this->input->post("cliente");

		if ($cliente != ""){
			$docsExists = $this->cliente_model->docs_generated_exists($cliente);

			if ($docsExists == false){
				$filter = new stdClass();
				$filter->CLIC_FlagEstado = "0";
				$filter->CLIC_FechaModificacion = date("Y-m-d H:i:s");
				$oper = $this->cliente_model->actualizar_cliente($cliente, $filter);

				if ($oper){
					$result = "success";
					$message = "Operacion exitosa";
				}
				else{
					$result = "error";
					$message = "¡Ups! Cliente no eliminado, intentalo nuevamente.";
				}
			}
			else{
				$result = "info";
				$message = "No se pueden eliminar clientes con documentos asociados.";
			}

			$json = array("result" => $result, "message" => $message);
		}
		else
			$json = array("result" => "error", "message" => "Cliente no seleccionado.");

		echo json_encode($json);
	}
	##  -> End

        public function deshabilitar_vehiculo(){

            $vehiculo = $this->input->post("vehiculo");

            $filter = new stdClass();
            $filter->CLIEVEHIP_FlagEstado = "0";
            $filter->CLIEVEHIP_FechaModificacion = date("Y-m-d H:i:s");

            if ($vehiculo != "")
                    $vehiculo = $this->cliente_model->actualizar_vehiculo($vehiculo, $filter);

            if ($vehiculo)
                    $json = array("result" => "success");
            else
                    $json = array("result" => "error");

            echo json_encode($json);
        }
        
	##  -> Begin
	public function docs_emitidos(){

            $posDT = -1;
            $columnas = array(
                    ++$posDT => "EMPRC_RazonSocial",
                    ++$posDT => "documento",
                    ++$posDT => "fechaRegistro",
                    ++$posDT => "fecha",
                    ++$posDT => "serie",
                    ++$posDT => "numero",
                    ++$posDT => "total",
                    ++$posDT => "situacion"
            );

            $filter = new stdClass();
            $filter->start = $this->input->post("start");
            $filter->length = $this->input->post("length");

            $ordenar = $this->input->post("order")[0]["column"];
            if ($ordenar != ""){
                    $filter->order = $columnas[$ordenar];
                    $filter->dir = $this->input->post("order")[0]["dir"];
            }

            $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;

            $cliente = $this->input->post('cliente');

            $clienteInfo = $this->cliente_model->docs_emitidos($cliente, $filter);
            $lista = array();

            if ($clienteInfo != NULL) {
                foreach ($clienteInfo as $i => $val) {
                    $posDT = -1;
                    $lista[] = array(
                            ++$posDT => $val->EMPRC_RazonSocial,
                            ++$posDT => $val->documento,
                            ++$posDT => $val->fechaRegistro,
                            ++$posDT => $val->fecha,
                            ++$posDT => $val->serie,
                            ++$posDT => $this->lib_props->getNumberFormat($val->numero,4),
                            ++$posDT => $val->total,
                            ++$posDT => $val->estado
                    );
                }
            }

            unset($filter->start);
            unset($filter->length);
            unset($filter->order);
            unset($filter->dir);

            $filter->count = true;
            $recordsTotal = $this->cliente_model->docs_emitidos($cliente, $filter);
            #$recordsFiltered = $this->proveedor_model->docs_emitidos($proveedor);
            $recordsFiltered = $recordsTotal;

            $json = array(
                    "draw"            => intval( $this->input->post('draw') ),
                    "recordsTotal"    => $recordsTotal->registros,
                    "recordsFiltered" => $recordsFiltered->registros,
                    "data"            => $lista
            );

            echo json_encode($json);
  }
    ##  -> End

	##  -> Begin
	public function search_documento($valida = true){
		$numero = trim($this->input->post('numero'));
                $exists = false;
                
                if($valida == "true")
                    $exists = $this->empresa_model->documento_exists($numero);

		if ($exists == false){
			$getCode = $this->generateCodeCliente();

			if (is_numeric($numero)){
				$url = "https://www.facturacionelectronicaccapa.com/api/api/searchDocument/".$numero;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$result = curl_exec($ch);
				curl_close($ch);

				$object = json_decode($result);

				if ($object->match == true){
					$tipo_cliente = "";
					$data = $object->result;

					if ( $data->documento == 'DNI' ){
						$info = array(
                                                        "documento" => "DNI",
                                                        "dni" => (isset($data->dni)) ? $data->dni : $numero,
                                                        "nombre" => (isset($data->nombres)) ? $data->nombres : "",
                                                        "paterno" => (isset($data->apellido_paterno)) ? $data->apellido_paterno : "",
                                                        "materno" => (isset($data->apellido_materno)) ? $data->apellido_materno : ""
                                                );
						$tipo_cliente = 0;
					}
					else if ( $data->documento == 'RUC' ){
                                            $rsocial = str_replace('"','',$data->razon_social);
                                            $info = array(
                                                    "documento" => $data->documento,
                                                    "ruc" => $data->ruc,
                                                    "razon_social" => trim($rsocial),
                                                    "direccion" => $data->direccion,
                                                    "ubigeo" => $data->ubigeo,
                                                    "estado" => $data->estado,
                                                    "condicion" => $data->condicion,
                                                    "tipovia" => $data->tipovia,
                                                    "nombrevia" => $data->nombrevia,
                                                    "codigozona" => $data->codigozona,
                                                    "tipozona" => $data->tipozona,
                                                    "numero" => $data->numero,
                                                    "interior" => $data->interior,
                                                    "lote" => $data->lote,
                                                    "departamento" => $data->departamento,
                                                    "manzana" => $data->manzana,
                                                    "kilometro" => $data->kilometro
                                            );
                                            $tipo_cliente = 1;
					}

					$json = array(
						"exists" => $exists,
						"match" => true,
						"tipo_cliente" => $tipo_cliente,
						"message" => "El documento fue encontrado",
						"info" => $info,
						"id_cliente" => $getCode
					);
				}
				else{
					$json = array("exists" => $exists, "match" => false, "message" => "Documento no encontrado.");
				}
			}
			else{
					$json = array("exists" => $exists, "match" => false, "message" => "Formato de RUC/DNI invalido.");
			}
		}
		else{
			$json = array("exists" => $exists, "match" => true, "message" => "El documento $numero, fue registrado anteriormente.");
		}
		echo json_encode($json);
		die();
	}
	##  -> End

	public function search_documento_insert(){
		$numero = trim($this->input->post('numero'));

                $exists = $this->empresa_model->documento_exists($numero);

		if ($exists == false){
			$getCode = $this->generateCodeCliente();

			if (is_numeric($numero)){
				$url = "https://www.facturacionelectronicaccapa.com/api/api/searchDocument/".$numero;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$result = curl_exec($ch);
				curl_close($ch);

				$object = json_decode($result);

				if ($object->match == true){
					$tipo_cliente = "";
					$data      = $object->result;
                                        $idpersona = 0;
                                        $idempresa = 0;

					if ( $data->documento == 'DNI' ){
                                            $tipo_cliente = 0;

                                            $filter = new stdClass();
                                            $filter->NACP_Nacionalidad        = 193;
                                            $filter->UBIGP_LugarNacimiento    = "000000";
                                            $filter->UBIGP_Domicilio          = "000000";
                                            $filter->PERSC_TipoDocIdentidad   = 1;
                                            $filter->PERSC_Nombre             = (isset($data->nombres)) ? $data->nombres : "";
                                            $filter->PERSC_ApellidoPaterno    = (isset($data->apellido_paterno)) ? $data->apellido_paterno : "";
                                            $filter->PERSC_ApellidoMaterno    = (isset($data->apellido_materno)) ? $data->apellido_materno : "";
                                            $filter->PERSC_NumeroDocIdentidad = (isset($data->dni)) ? $data->dni : $numero;
                                            $filter->PERSC_FechaNac           = "0000-00-00";
                                            $idpersona = $this->persona_model->insertar_persona($filter);
					}
					else if ( $data->documento == 'RUC' ){
                                            $rsocial = str_replace('"','',$data->razon_social);
                                            $tipo_cliente = 1;
                                            
                                            $filter = new stdClass();
                                            $filter->EMPRC_Ruc         = $data->ruc;
                                            $filter->EMPRC_RazonSocial = trim($rsocial);
                                            $filter->EMPRC_Direccion   = $data->direccion;
                                            $filter->TIPCOD_Codigo     = 6;
                                            $idempresa = $this->empresa_model->insertar_empresa($filter);
                                            
					}
                                        
                                        //Registramos al cliente
                                        $filter = new stdClass();
                                        $filter->EMPRP_Codigo       = $idempresa;
                                        $filter->PERSP_Codigo       = $idpersona;
                                        $filter->TIPCLIP_Codigo     = 9;//Es Cliente
                                        $filter->CLIC_TipoPersona   = $tipo_cliente;
                                        $filter->CLIC_CodigoUsuario = $getCode;
                                        $id_cliente = $this->cliente_model->insertar_cliente($filter);                                        
                                        
					$json = array(
						"exists" => $exists,
						"match" => true,
						"tipo_cliente" => $tipo_cliente,
						"message" => "Se registro al cliente",
						"info" => (array)$filter,
						"id_cliente" => $id_cliente
					);
				}
				else{
					$json = array("exists" => $exists, "match" => false, "message" => "Documento no encontrado.");
				}
			}
			else{
                            $json = array("exists" => $exists, "match" => false, "message" => "Formato de RUC/DNI invalido.");
			}
		}
		else{
			$json = array("exists" => $exists, "match" => true, "message" => "El documento $numero, fue registrado anteriormente.");
		}
		echo json_encode($json);
		die();
	}        
        
	##  -> Begin
	public function generateCodeCliente(){
		$rjson = $this->input->post("json");
		$code = $this->cliente_model->getCodeCliente();
		$nvoCode = "CL0".$code;

		if ($rjson != ""){
			$json = array("code" => $nvoCode);
			echo json_encode($json);
		}
		else
			return $nvoCode;
	}
	##  -> End


	public function comparar($x, $y)
	{
		if ($x->nombre == $y->nombre)
			return 0;
		else if ($x->nombre < $y->nombre)
			return -1;
		else
			return 1;
	}

	public function obtener_datosPersona($datos_persona)
	{
		$objeto = new stdClass();
		$objeto->id = $datos_persona[0]->PERSP_Codigo;
		$objeto->persona = $datos_persona[0]->PERSP_Codigo;
		$objeto->empresa = 0;
		$objeto->nombre = $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno . " " . $datos_persona[0]->PERSC_Nombre;
		$objeto->ruc = $datos_persona[0]->PERSC_Ruc;
		$objeto->telefono = $datos_persona[0]->PERSC_Telefono;
		$objeto->fax = $datos_persona[0]->PERSC_Fax;
		$objeto->movil = $datos_persona[0]->PERSC_Movil;
		$objeto->web = $datos_persona[0]->PERSC_Web;
		$objeto->direccion = $datos_persona[0]->PERSC_Direccion;
		$objeto->email = $datos_persona[0]->PERSC_Email;
		$objeto->dni = $datos_persona[0]->PERSC_NumeroDocIdentidad;
		$objeto->tipo = "0";
		return $objeto;
	}

  //Obtiene campos especificos de una empresa y lo renombra
	public function obtener_datosEmpresa($datos_empresa)
	{
		$empresa = $datos_empresa[0]->EMPRP_Codigo;
		$datos_empresaSucursal = $this->empresa_model->obtener_establecimientoEmpresa($empresa, '1');
		if (count($datos_empresaSucursal) > 0) {
			$direccion = $datos_empresaSucursal[0]->EESTAC_Direccion;
		} else {
			$direccion = "";
		}
		$objeto = new stdClass();
		$objeto->id = $datos_empresa[0]->EMPRP_Codigo;
		$objeto->persona = 0;
		$objeto->empresa = $datos_empresa[0]->EMPRP_Codigo;
		$objeto->nombre = $datos_empresa[0]->EMPRC_RazonSocial;
		$objeto->ruc = $datos_empresa[0]->EMPRC_Ruc;
		$objeto->telefono = $datos_empresa[0]->EMPRC_Telefono;
		$objeto->fax = $datos_empresa[0]->EMPRC_Fax;
		$objeto->movil = $datos_empresa[0]->EMPRC_Movil;
		$objeto->web = $datos_empresa[0]->EMPRC_Web;
		$objeto->direccion = $direccion;
		$objeto->email = $datos_empresa[0]->EMPRC_Email;
		$objeto->tipo = "1";
		$objeto->dni = "";
		return $objeto;
	}

	public function obtener_datosEmpresa_array($datos_empresa)
	{
		$resultado = array();
		foreach ($datos_empresa as $indice => $valor) {
			$objeto = new stdClass();
			$empresa = $datos_empresa[$indice]->EMPRP_Codigo;
			$datos_empresaSucursal = $this->empresa_model->obtener_establecimientoEmpresa($empresa, '1');
			if (count($datos_empresaSucursal) > 0) {
				$direccion = $datos_empresaSucursal[0]->EESTAC_Direccion;
			} else {
				$direccion = "";
			}
			$objeto->id = $datos_empresa[$indice]->EMPRP_Codigo;
			$objeto->persona = 0;
			$objeto->empresa = $datos_empresa[$indice]->EMPRP_Codigo;
			$objeto->nombre = $datos_empresa[$indice]->EMPRC_RazonSocial;
			$objeto->ruc = $datos_empresa[$indice]->EMPRC_Ruc;
			$objeto->telefono = $datos_empresa[$indice]->EMPRC_Telefono;
			$objeto->fax = $datos_empresa[$indice]->EMPRC_Fax;
			$objeto->movil = $datos_empresa[$indice]->EMPRC_Movil;
			$objeto->web = $datos_empresa[$indice]->EMPRC_Web;
			$objeto->direccion = $direccion;
			$objeto->email = $datos_empresa[$indice]->EMPRC_Email;
			$objeto->tipo = "1";
			$objeto->dni = "";
			$resultado[$indice] = $objeto;
		}
		return $resultado;


	}

	public function listar_sucursalesEmpresa($empresa)
	{
		$listado_sucursalesEmpresa = $this->empresa_model->listar_sucursalesEmpresa($empresa, '0');
		$resultado = array();
		if (count($listado_sucursalesEmpresa) > 0) {
			foreach ($listado_sucursalesEmpresa as $indice => $valor) {
				$tipo = $valor->TESTP_Codigo;
				$ubigeo = $valor->UBIGP_Codigo;
				$nombre_tipo = "";
				if ($tipo != '') {
					$datos_tipoEstab = $this->tipoestablecimiento_model->obtener_tipoEstablecimiento($tipo);
					if (count($datos_tipoEstab) > 0)
						$nombre_tipo = $datos_tipoEstab[0]->TESTC_Descripcion;
				}
				$nombre_ubigeo = "";
				if ($ubigeo != '000000' && $ubigeo != '') {
					$datos_ubigeo = $this->ubigeo_model->obtener_ubigeo($ubigeo);
					if (count($datos_ubigeo) > 0)
						$nombre_ubigeo = $datos_ubigeo[0]->UBIGC_Descripcion;
				}
				$objeto = new stdClass();
				$objeto->tipo = $valor->TESTP_Codigo;
				$objeto->nombre_tipo = $nombre_tipo;
				$objeto->empresa = $valor->EMPRP_Codigo;
				$objeto->ubigeo = $valor->UBIGP_Codigo;
				$objeto->des_ubigeo = $nombre_ubigeo;
				$objeto->descripcion = $valor->EESTABC_Descripcion == '' ? '&nbsp;' : $valor->EESTABC_Descripcion;
				$objeto->direccion = $valor->EESTAC_Direccion == '' ? "&nbsp;" : $valor->EESTAC_Direccion;
				$objeto->estado = $valor->EESTABC_FlagEstado;
				$objeto->sucursal = $valor->EESTABP_Codigo;
				$resultado[] = $objeto;
			}
		}
		return $resultado;
	}

	public function listar_contactosEmpresa($empresa)
	{
		$listado_contactosEmpresa = $this->empresa_model->listar_contactosEmpresa($empresa);
		$resultado = array();
		if (count($listado_contactosEmpresa) > 0) {
			foreach ($listado_contactosEmpresa as $indice => $valor) {
				$persona = $valor->ECONC_Persona;
				$datos_persona = $this->persona_model->obtener_datosPersona($persona);
				$nombres_persona = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno . " ";
				$datos_directivo = $this->directivo_model->buscar_directivo($empresa, $persona);
				$directivo = $datos_directivo[0]->DIREP_Codigo;
				$cargo = $datos_directivo[0]->CARGP_Codigo;
				$datos_areaEmpresa = $this->empresa_model->obtener_areaEmpresa($empresa, $directivo);
				$datos_cargo = $this->cargo_model->getCargo($cargo);
				$nombre_cargo = $datos_cargo[0]->CARGC_Descripcion;
				$area = $datos_areaEmpresa[0]->AREAP_Codigo;
				$datos_area = $this->area_model->obtener_area($area);
				$nombre_area = $datos_area[0]->AREAC_Descripcion;
				$objeto = new stdClass();
				$objeto->area = $area;
				$objeto->nombre_area = $nombre_area;
				$objeto->empresa = $valor->EMPRP_Codigo;
				$objeto->personacontacto = $valor->PERSP_Contacto;
				$objeto->descripcion = $valor->ECONC_Descripcion;
				$objeto->telefono = $valor->ECONC_Telefono == '' ? '&nbsp;' : $valor->ECONC_Telefono;
				$objeto->movil = $valor->ECONC_Movil;
				$objeto->fax = $valor->ECONC_Fax;
				$objeto->email = $valor->ECONC_Email == '' ? '&nbsp;' : $valor->ECONC_Email;
				$objeto->persona = $valor->ECONC_Persona;
				$objeto->nombre_persona = $nombres_persona;
				$objeto->tipo_contacto = $valor->ECONC_TipoContacto;
				$objeto->nombre_cargo = $nombre_cargo;
				$resultado[] = $objeto;
			}
		}
		return $resultado;
	}

	function JSON_buscar_cliente($numdoc)
	{
		$datos_empresa = $this->empresa_model->obtener_datosEmpresa2($numdoc);
		$datos_persona = $this->persona_model->obtener_datosPersona2($numdoc);
		$resultado = '[{"CLIP_Codigo":"0","EMPRC_Ruc":"","EMPRC_RazonSocial":""}]';
		if (count($datos_empresa) > 0) {
			$empresa = $datos_empresa[0]->EMPRP_Codigo;
			$razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
			$datosCliente = $this->cliente_model->obtener_datosCliente2($empresa);
			if (count($datosCliente) > 0) {
				$cliente = $datosCliente[0]->CLIP_Codigo;
				$resultado = '[{"CLIP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $razon_social . '"}]';
			}
		} elseif (count($datos_persona) > 0) {
			$persona = $datos_persona[0]->PERSP_Codigo;
			$nombres = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
			$datosCliente = $this->cliente_model->obtener_datosCliente3($persona);
			if (count($datosCliente) > 0) {
				$cliente = $datosCliente[0]->CLIP_Codigo;
				$resultado = '[{"CLIP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $nombres . '"}]';
			}
		}
		echo $resultado;
	}

	public function JSON_listar_sucursalesEmpresa($cliente = '')
	{

		$listado_sucursalesEmpresa = array();
		if ($cliente != '') {
			$datos_cliente = $this->cliente_model->obtener($cliente);
			$empresa = $datos_cliente->empresa;
			if ($empresa != '0' && $empresa != '') {
				$listado_sucursalesEmpresa = $this->empresa_model->listar_sucursalesEmpresa($empresa);
				foreach ($listado_sucursalesEmpresa as $key => $reg) {
					$reg->distrito = "";
					$reg->provincia = "";
					$reg->departamento = "";
					if ($reg->UBIGP_Codigo != '' && $reg->UBIGP_Codigo != '000000') {
						$datos_ubigeo_dist = $this->ubigeo_model->obtener_ubigeo_dist($reg->UBIGP_Codigo);
						$datos_ubigeo_prov = $this->ubigeo_model->obtener_ubigeo_prov($reg->UBIGP_Codigo);
						$datos_ubigeo_dep = $this->ubigeo_model->obtener_ubigeo_dpto($reg->UBIGP_Codigo);
						if (count($datos_ubigeo_dist) > 0)
							$reg->distrito = $datos_ubigeo_dist[0]->UBIGC_Descripcion;
						if (count($datos_ubigeo_prov) > 0)
							$reg->provincia = $datos_ubigeo_prov[0]->UBIGC_Descripcion;
						if (count($datos_ubigeo_dep) > 0)
							$reg->departamento = $datos_ubigeo_dep[0]->UBIGC_Descripcion;
					}
					$listado_sucursalesEmpresa[$key] = $reg;
				}
			} else {
				$filter = new stdClass();
				$filter->EESTAC_Direccion = $datos_cliente->direccion;
				$filter->UBIGP_Codigo = $datos_cliente->ubigeo;
				$filter->departamento = $datos_cliente->departamento;
				$filter->provincia = $datos_cliente->provincia;
				$filter->distrito = $datos_cliente->distrito;
				$listado_sucursalesEmpresa = array($filter);
			}
		}

		$result[] = array('Tipo' => '1', 'Titulo' => 'LOS ESTABLECIMIENTOS DE MI CLIENTE');
		foreach ($listado_sucursalesEmpresa as $reg)
			$result[] = array('Tipo' => '2', 'EESTAC_Direccion' => $reg->EESTAC_Direccion, 'UBIGP_Codigo' => $reg->UBIGP_Codigo, 'departamento' => $reg->departamento, 'provincia' => $reg->provincia, 'distrito' => $reg->distrito);

		echo json_encode($result);
	}

	public function autocomplete(){
		$keyword = $this->input->post('term');
		$compania = $this->compania;
		$datosCliente = $this->cliente_model->autocompleteCliente($keyword);
		$result = array();
		$contactos = array();

		if($datosCliente != NULL){
			$vendedor = "";
			foreach ($datosCliente  as $key => $value) {

				$tipoPersona = $value->CLIC_TipoPersona;
				$filterContactos = new stdClass();

				if ( $tipoPersona== '0') {
					$nombre = $value->PERSC_Nombre . ' ' .$value->PERSC_ApellidoPaterno;
					$ruc = $value->PERSC_Ruc;
					$ruc = ($ruc == NULL || $ruc == 0) ? $value->PERSC_NumeroDocIdentidad : $ruc;
					$codigoEmpresa = $value->PERSP_Codigo;
					$filterContactos->persona = $value->PERSP_Codigo;
					$direccion = "-";
				} else {
					$nombre =$value->EMPRC_RazonSocial;
					$ruc = $value->EMPRC_Ruc;
					$codigoEmpresa = $value->EMPRP_Codigo;
					$filterContactos->empresa = $value->EMPRP_Codigo;
					$direccion = $value->EMPRC_Direccion;
				}

				$contactos = $this->empresa_model->getContactos($filterContactos);
				$vendedor = $value->CLIC_Vendedor;
				$digemin = $value->CLIC_Digemin;
				$result[] = array("value" => $nombre, "label" => "$value->CLIC_CodigoUsuario | $ruc - $nombre", "nombre" => $nombre, "codigo" => $value->CLIP_Codigo, "ruc" => $ruc, "TIPCLIP_Codigo" => $value->TIPCLIP_Codigo, "tipoPersona" => $tipoPersona, "codigoEmpresa" => $codigoEmpresa, "vendedor" => $vendedor, "contactos" =>  $contactos, "digemin" =>  $digemin,"direccion" => $direccion);
			}
		}
		echo json_encode($result);
	}

	public function autocomplete_ruc(){
		$keyword = $this->input->post('term');
		$compania = $this->compania;
		$consulta = $this->cliente_model->buscarClienteRuc($keyword, $compania);
		$result = array();
		$contactos = array();
		if ($consulta != NULL) {
			$vendedor = "";
			foreach ($consulta AS $cliente => $value) {
				$tipoPersona = $value->CLIC_TipoPersona;
				$filterContactos = new stdClass();

				if ($tipoPersona== '0') {
					$nombre = $value->PERSC_Nombre;
					$ruc = $value->PERSC_NumeroDocIdentidad;
					$codigoEmpresa = $value->PERSP_Codigo;
					$filterContactos->persona = $value->PERSP_Codigo;
				} else {
					$nombre = $value->EMPRC_RazonSocial;
					$ruc = $value->EMPRC_Ruc;
					$codigoEmpresa = $value->EMPRP_Codigo;
					$filterContactos->empresa = $value->EMPRP_Codigo;
					$direccion = $value->EMPRC_Direccion;
				}

				$contactos = $this->empresa_model->getContactos($filterContactos);
				$vendedor = $value->CLIC_Vendedor;
				$digemin = $value->CLIC_Digemin;
				$result[] = array("value" => $ruc, "label" => "$value->CLIC_CodigoUsuario | $ruc - $nombre", "nombre" => $nombre, "codigo" => $value->CLIP_Codigo, "ruc" => $ruc, "TIPCLIP_Codigo" => $value->TIPCLIP_Codigo, "tipoPersona" => $tipoPersona, "codigoEmpresa" => $codigoEmpresa, "vendedor" => $vendedor, "contactos" =>  $contactos, "digemin" =>  $digemin);
			}
		}
		echo json_encode($result);
	}

	public function categoria_cliente(){
		$vendedor = $this->input->post('vendedor');
		$detalles = $this->directivo_model->listarVendedores($vendedor);
		echo json_encode($detalles);
	}

	function response(Array $response = null){
		if(!is_array($response)) $response = array();

		$status = isset($response["status"]) ? $response["status"] : 500;
		$message = "No se pudo realizar la consulta a la sunat.";

		if(!isset($response["status"])) $response["status"] = $status;
		if(!isset($response["message"])) $response["message"] = $message;

		header($_SERVER["SERVER_PROTOCOL"]." $status " . ($status == 500 ? 'Error' : 'Success'));
		header("Content-type: application/json");

		exit(json_encode($response));
	}

	public function cliente_sunat(){

		$ruc = $this->input->post('ruc');
		$ruc_valida = $this->empresa_model->buscar_ruc($ruc);
		$getCode = $this->generateCodeCliente();

		if ( strlen($ruc) == 11 ){
			
			require_once("registro/src-sunat/autoload.php");

			if( count($ruc_valida) > 0 ){
				self::response(array("message" => "El numero de ruc esta registrado."));
			}

			$company = new \Sunat\Sunat( true, true );
			$ruc = $ruc;
			$dni = $ruc;

			$search1 = $company->search( $ruc );
			$search2 = $company->search( $dni );

			if( $search1->success == true )
				$datos = $search1;

			if( $search2->success == true )
				$datos = $search2;

			if($datos == NULL)
				self::response(array("message" => "El cliente no esta registrado en Sunat."));

			$datos->result->ubigeo = $this->obtUbigueo($datos->result->direccion);

			self::response(array(
				"status" => 200,
				"message" => "El cliente fue encontrado",
				"tipoCliente" => "RUC",
				"cliente" => $datos,
				"idNvoCliente" => $getCode
			));
		}
		else{
			require_once('registro/src-dni/simple_html_dom.php');

      //OBTENEMOS EL VALOR
      //$consulta = file_get_html('http://aplicaciones007.jne.gob.pe/srop_publico/Consulta/Afiliado/GetNombresCiudadano?DNI='.$dni)->plaintext;
			$url = 'https://eldni.com/buscar-por-dni?dni='.$ruc;
      //$consulta = file_get_html('https://eldni.com/buscar-por-dni?dni='.$ruc);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$consulta = curl_exec($ch);
			curl_close($ch);

			$datosnombres = array();

			preg_match_all('/<td class="text-left">(.*)<\/td>/Ui', $consulta, $datosnombres);
      //LA LOGICA DE LA PAGINAS ES APELLIDO PATERNO | APELLIDO MATERNO | NOMBRES
      //$partes = explode("|", $consulta);


			$datos = array(
				"dni" => $ruc,
				"nombre" => $datosnombres[1][0],
				"paterno" => $datosnombres[1][1],
				"materno" => $datosnombres[1][2],
			);

			if($datosnombres == NULL)
				self::response(array("message" => "No se encontro informacion del cliente."));

			self::response(array(
				"status" => 200,
				"message" => "El cliente fue encontrado",
				"tipoCliente" => "DNI",
				"cliente" => $datos,
				"idNvoCliente" => $getCode
			));
		}

	}

	##  -> Begin
	public function obtUbigueo($dir){
		if ($dir == "-" || $dir == "- ")
			return "000000";

		$ubigeo = $this->ubigeo_model->buscar_ubigeo($dir);
		$ubigeo[0]->UBIGC_CodProv = substr($ubigeo[0]->UBIGC_CodProv,2,2);
		$ubigeo[0]->UBIGC_CodDist = substr($ubigeo[0]->UBIGC_CodDist,4,2);

		return $ubigeo[0];
	}
	##  -> End

	public function cliente_sunat_ubg($ruc){
		include_once('registro/Sunat.php');

		$sunat = new Sunat();
		$cliente_sunat = $sunat->consulta_ruc($ruc);

		return $cliente_sunat;
	}
}
?>