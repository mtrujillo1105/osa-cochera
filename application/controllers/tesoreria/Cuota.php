<?php

class Cuota extends CI_Controller {

	private $compania;
	private $usuario;
	private $rol;
	private $url;
    private $base_url;

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');

        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        
        $this->load->library('lib_props');
        $this->load->library('movimientos');

        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/almacen_model');
        
        $this->load->model('empresa/proveedor_model');

        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/tipocambio_model');

        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('ventas/comprobantedetalle_model');

        $this->load->model('configuracion_model');
        
        $this->load->model('tesoreria/banco_model');
        $this->load->model('tesoreria/caja_model');
        $this->load->model('tesoreria/cuota_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/cuentaspago_model');

        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
        $this->rol = $this->session->userdata('rol');
        $this->url = $_SERVER['REQUEST_URI'];
        $this->base_url = base_url();
    }

    public function index() {
        $this->load->view('seguridad/inicio');
        
    }

    public function cuotas($tipo_oper = "V"){
		
		$filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;
        $data['registros'] = count($this->cuota_model->getComprobantes($filterAll));
        
        $data['action'] = "index.php/tesoreria/cuota/cuotas/$tipo_oper";

        $data['titulo_tabla'] = "RELACIÓN DE CUOTAS";
        $data['titulo_busqueda'] = "BUSCAR COMPROBANTES";
        $data['tipo_oper'] = $tipo_oper;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper));

        $this->layout->view('tesoreria/cuota_index', $data);
    }

    public function datatable_comprobantes($tipo_oper){

        $columnas = array(
                            0 => "CPC_Fecha",
                            1 => "CPC_Serie",
                            2 => "CPC_Numero",
                            3 => "CLIC_CodigoUsuario",
                            4 => "",
                            5 => "",
                            6 => ""
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

        $fecha_ini = $this->input->post('fechai');
        $filter->fecha_ini = ($fecha_ini != "") ? $fecha_ini : ""; # date("Y-m") . '-1';

        $fecha_fin = $this->input->post('fechaf');
        $filter->fecha_fin = ($fecha_fin != "") ? $fecha_fin : ""; # date("Y-m-d");

        $filter->tipo_oper = $tipo_oper;
        $filter->seriei = $this->input->post('seriei');
        $filter->numero = $this->input->post('numero');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');
        $filter->nombre_proveedor = $this->input->post('nombre_proveedor');

        $listado_comprobantes = $this->cuota_model->getComprobantes($filter);
        $lista = array();
        
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                $modal = "<button type='button' onclick='ver_cuotas_comprobante($valor->CPP_Codigo)' data-toggle='modal' data-target='#ver_cuotas' class='btn btn-info'>cuotas</button>";
                $general_pdf = "<button href='" . $this->base_url . "index.php/tesoreria/cuota/cuotas_cliente/$valor->CLIP_Codigo/$valor->PROVP_Codigo' data-fancybox data-type='iframe' class='btn btn-info'>Documentos</button>";

                $lista[] = array(
                                    0 => mysql_to_human($valor->CPC_Fecha),
                                    1 => $valor->CPC_Serie,
                                    2 => $this->lib_props->getOrderNumeroSerie($valor->CPC_Numero),
                                    3 => $valor->CLIC_CodigoUsuario,
                                    4 => $valor->numdoc,
                                    5 => $valor->nombre,
                                    6 => $valor->CPC_total,
                                    7 => $modal,
                                    8 => $general_pdf
                                );
            }
        }

        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->cuota_model->getComprobantes($filterAll)),
                            "recordsFiltered" => intval( count($this->cuota_model->getComprobantes($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function obtener_cuotas_comprobante($codigo = NULL){
        $codigo = $this->input->post("comprobante");
        $datos = $this->cuota_model->lista_cuotas_comprobantes($codigo);
                
        $lista = array();
        if ($datos != NULL) {
            foreach ($datos as $key => $value) {
                $lista[] = array(
                    "codigo_cuota" => $value->CUOT_Codigo,
                    "ncuota"  => $this->validar_formato_serie_cuota($value->CUOT_Numero),
                    "fechaiv"      => $value->CUOT_FechaInicio,
                    "fechafv"      => $value->CUOT_Fecha,
                    "fechai"       => mysql_to_human($value->CUOT_FechaInicio),
                    "fechaf"       => mysql_to_human($value->CUOT_Fecha),
                    "cuota"        => $value->CUOT_Monto,
                    "estado_pago"  => $value->CUOT_FlagPagado,
                    "caja"         => $value->CAJA_Codigo,
                    "cajas"        => $this->caja_model->getCajas(),
                    "observacion"  => ( $value->CUOT_Observacion == NULL ) ? "" : $value->CUOT_Observacion
                );
            }
            
            $comprobante = array(
                                    "comprobante_id" => $datos[0]->CPP_Codigo,
                                    "documento" => $datos[0]->CPC_TipoDocumento,
                                    "operacion" => $datos[0]->CPC_TipoOperacion,
                                    "serie" 	=> $datos[0]->CPC_Serie,
                                    "numero" 	=> $this->lib_props->getOrderNumeroSerie($datos[0]->CPC_Numero),
                                    "total" 	=> number_format($datos[0]->CPC_total,2,'.','')
                                );

            $json = array("match" => true, "info" => $lista, "comprobante" => $comprobante);
        }
        else
            $json = array("match" => false, "info" => "", "comprobante" => "");

        echo json_encode($json);
    }


    public function guardar_couta(){

        $idcuota = $this->input->post("idcuota");
        $comprobante = $this->input->post("comprobante");
        $observacion = $this->input->post("observacion");
        $estado_pago = $this->input->post("estado_pago");
        $caja = $this->input->post("caja");
        $fechai = $this->input->post("fechai");
        $fechaf = $this->input->post("fechaf");
        $cuota = $this->input->post("cuota");

        $filter = new stdClass();
        $filter->CUOT_FechaInicio = $fechai;
        $filter->CUOT_Fecha       = $fechaf;
        $filter->CUOT_FlagPagado  = ($estado_pago == "0") ? 0 : 1;
        $filter->CUOT_Observacion = $observacion;
        $filter->CUOT_Monto       = $cuota;
        $filter->CAJA_Codigo      = $caja;

        if ($comprobante != NULL && $comprobante != "")
            $update = $this->cuota_model->insertarNvaCuota($comprobante, $filter);
        
        if ($idcuota != NULL && $idcuota != "")
            $update = $this->cuota_model->modificarCuota($idcuota, $filter);

        if ($estado_pago == 1){
	        $info = new stdClass();
	        $info->comprobante = $comprobante;
	        $info->cuota = $idcuota;
	        $info->importe = $cuota;
            $info->caja = $caja;
	        $info->observacion = $observacion;
        	$this->guardar_pago($info);
	    }

        if ($update)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        
        echo json_encode($json);
    }

    public function borrar_cuota(){
        $idcuota = $this->input->post("idcuota");
        
        $filter = new stdClass();
        $filter->CUOT_FlagEstado = 0;
        
        $rspta = $this->cuota_model->borrarCuota($idcuota,$filter);

        if ($rspta) {
            $lista = array("message" => 1 );
            echo json_encode($lista);
        }else{
                echo json_encode(array(
                    "message" => 0
                ));
        }
    }

    public function obtener_tipo_de_cambio($fecha_comprobante) {
        return $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);
    }
    
    public function validar_formato_serie_cuota($numero){
        $nn = strlen($numero);
        switch ($nn){
            case 1:
                $temp_numero = "000".$numero;
                break;
            case 2:
                $temp_numero = "00".$numero;
                break;
            case 3:
                $temp_numero = "0".$numero;
                break;
            default:
                $temp_numero = $numero;
                break;
        }
        return "CUOT - ".$temp_numero;
    }

    public function guardar_pago($info){
		$fecha = date("Y-m-d");
		$fechaRegistro = date("Y-m-d h:i:s");

		$comprobanteInfo = $this->cuota_model->getComprobanteInfo($info);
        $comprobante = $comprobanteInfo[0];
		$tipo_cuenta = ($comprobante->CPC_TipoOperacion == "V") ? 1 : 2; # 1 COBRAR 2 PAGAR

		$tipo = ( $tipo_cuenta == 1 ) ? 20 : 21;
		$correlativo = $this->configuracion_model->obtener_numero_documento($this->compania, $tipo);
    	
    	$filter = new stdClass();
		$filter->PAGC_TipoCuenta = $tipo_cuenta;
		$filter->PAGP_Serie = $correlativo[0]->CONFIC_Serie;
		$filter->PAGP_Numero = $this->lib_props->getOrderNumeroSerie($correlativo[0]->CONFIC_Numero + 1);
		$filter->PAGC_FechaOper = $fecha;
		$filter->CLIP_Codigo = $comprobante->CLIP_Codigo;
		$filter->PROVP_Codigo = $comprobante->PROVP_Codigo;
		$filter->PAGC_TDC = $this->tipocambio_model->getCambio($comprobante->MONED_Codigo, $fecha);
		$filter->PAGC_FormaPago = 1; # POR AHORA EFECTIVO
		$filter->PAGC_Monto = $info->importe;
		$filter->MONED_Codigo = $comprobante->MONED_Codigo;
		$filter->PAGC_Obs = $info->observacion;
		$filter->PAGC_Saldo = "";
		$filter->COMPP_Codigo = $this->compania;

		$idPago = $this->pago_model->registrar_pago_cuota($filter);

		$cuentaInfo = $this->cuentas_model->getCuentaComprobante($comprobante->CPP_Codigo);

		if ($cuentaInfo != NULL){
			$filter = new stdClass();
			$filter->CUE_Codigo 	= $cuentaInfo[0]->CUE_Codigo;
			$filter->PAGP_Codigo 	= $idPago;
			$filter->CPAGC_TDC 		= "";
			$filter->CPAGC_Monto 	= $info->importe;
			$filter->MONED_Codigo 	= $comprobante->MONED_Codigo;
			
			$cod_cuentaspago = $this->cuentaspago_model->insertar($filter);
			
			$estado = ($cuentaInfo[0]->CUE_Monto > $info->importe) ? "A" : "C";
			$this->cuentas_model->modificar_estado($cuentaInfo[0]->CUE_Codigo, $estado);
		}

        # SI UNA CAJA ESTA ASOCIADA AL DOCUMENTO, REGISTRA EL MOVIMIENTO EN LA CAJA
        if ($info->caja != "" && $info->caja != NULL){

            $filter = new stdClass();
            $filter->CAJAMOV_Codigo = ""; # "" PARA INGRESAR UN REGISTRO NUEVO
            $filter->CAJA_Codigo = $info->caja;
            $filter->PAGP_Codigo = $idPago;
            $filter->RESPMOV_Codigo = NULL;
            $filter->CUENT_Codigo = $cuentaInfo[0]->CUE_Codigo;
            $filter->MONED_Codigo = $comprobante->MONED_Codigo;
            $filter->CAJAMOV_Monto = $info->importe;
            $filter->CAJAMOV_MovDinero = ($comprobante->CPC_TipoOperacion == 'V') ? 1 : 2; # (V:1) = INGRESO | (C:2) = EGRESO
            $filter->FORPAP_Codigo = 1; #$comprobante->FORPAP_Codigo; # LAS CUOTAS SE PAGAN EN EFECTIVO
            $filter->CAJAMOV_FechaRecep = $fecha;
            $filter->CAJAMOV_Justificacion = "PAGO DE CUOTA EN DOCUMENTO: ".$comprobante->CPC_Serie . " - " . $this->lib_props->getOrderNumeroSerie($comprobante->CPC_Numero);
            $filter->CAJAMOV_Observacion = $info->observacion;
            $filter->CAJAMOV_FlagEstado = "1";
            $filter->CAJAMOV_CodigoUsuario = $this->usuario;
            $filter->CPP_Codigo            = NULL;
            $this->movimientos->guardar_movimiento($filter);
        }
    }
    
    public function comprobante_cuotas($comprobante, $flagImage = 1){
    	$this->lib_props->comprobante_cuotas($comprobante, $flagImage);
    }

    public function cuota_pdf($cuota, $flagImage = 1){
        $this->lib_props->cuota_pdf($cuota, $flagImage);
    }

    public function cuotas_cliente($cliente = NULL, $proveedor = NULL, $flagImage = 1){
        $this->lib_props->cuotas_cliente($cliente, $proveedor, $flagImage);
    }
}

?>