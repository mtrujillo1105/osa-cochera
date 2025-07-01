<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */

class Cuentas extends CI_Controller{

	##  -> Begin - El array somevar es reemplazado por atributos
	private $empresa;
	private $compania;
	private $usuario;
	private $url;
	##  -> End

	##  -> Begin
	public function __construct(){
            parent::__construct();

            $this->load->helper('pago');
            $this->load->helper('date');
            $this->load->helper('html');
            $this->load->helper('url');

            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->library('form_validation');
            $this->load->library('lib_props');
            $this->load->library('movimientos');

            $this->load->model('maestros/configuracion_model');
            $this->load->model('maestros/moneda_model');
            $this->load->model('maestros/tipocambio_model');
            $this->load->model('maestros/formapago_model');

            $this->load->model('empresa/proveedor_model');
            $this->load->model('empresa/proveedor_model');

            $this->load->model('empresa/cliente_model');
            $this->load->model('ventas/notacredito_model');

            $this->load->model('tesoreria/banco_model');
            $this->load->model('tesoreria/caja_model');
            $this->load->model('tesoreria/cuentas_model');
            $this->load->model('tesoreria/cuentaspago_model');
            $this->load->model('tesoreria/flujocaja_model');
            $this->load->model('tesoreria/pago_model');
            $this->load->model('tesoreria/movimiento_model');

            $this->empresa = $this->session->userdata('empresa');
            $this->compania = $this->session->userdata('compania');
            $this->usuario = $this->session->userdata('user');
            $this->url = base_url();
	}
	##  -> End

	##  -> begin
	public function listar($tipo_cuenta = '1', $j = '0', $limpia = ''){
            $conf['base_url'] = $this->url ."/$tipo_cuenta/";
            $data['titulo'] = "CUENTAS POR " . ($tipo_cuenta == '1' ? 'COBRAR' : 'PAGAR');
            $data['titulo_busqueda'] = "BUSCAR CUENTAS POR " . ($tipo_cuenta == '1' ? 'COBRAR' : 'PAGAR');
            $data['tipo_cuenta']     = $tipo_cuenta;
            $data["fechai"]          = "";
            $data["fechaf"]          = "";
            $data["serie"]           = "";
            $data["numero"]          = "";
            $data["cboestadopago"]          = "";
            $data['totalCuentas']    = $this->cuentas_model->montosCuentas($tipo_cuenta);
            $data['monedas'] = $this->moneda_model->listar();
            $data['bancos'] = $this->banco_model->listar_banco();
            $data['cajas'] = $this->caja_model->getCajas();
            $data['mis_bancos'] = $this->banco_model->getBancosEmpresa($this->empresa);
            $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_cuenta' => $tipo_cuenta));
            $this->layout->view('tesoreria/cuentas_index', $data);
	}
	##  -> End

	##  -> Begin
	public function datatable_cuentas($tipo_cuenta = '1'){

		$columnas = array(
			0 => "CUE_FechaOper",
			1 => "DOCUP_Codigo",
			2 => "CPC_Serie",
			3 => "CPC_Numero",
			4 => "CLIC_CodigoUsuario",
			5 => "nombre",
			6 => "",
			7 => "",
			8 => "",
			9 => "",
			10 => "",
			11 => ""
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
		$filter->fechai = ($fecha_ini != "") ? $fecha_ini : "";

		$fecha_fin = $this->input->post('fechaf');
		$filter->fechaf = ($fecha_fin != "") ? $fecha_fin : "";

		$filter->tipo_cuenta = $tipo_cuenta;

		$filter->serie = $this->input->post('serie');
		$filter->numero = $this->input->post('numero');

		$filter->cliente = $this->input->post('cliente');
		$filter->ruc_cliente = $this->input->post('ruc_cliente');
		$filter->nombre_cliente = $this->input->post('nombre_cliente');

		$filter->cond_pago = $this->input->post('estado_pago');
		$filter->comprobante = $this->input->post('comprobante');

		$filter->proveedor = $this->input->post('proveedor');
		$filter->ruc_proveedor = $this->input->post('ruc_proveedor');
		$filter->nombre_proveedor = $this->input->post('nombre_proveedor');
		$filter->producto = '';
		$filter->codproducto = '';
		$filter->nombre_producto = '';

		$filter->MONED_Codigo = $this->input->post('monedalista');

		$listado_cuentas = $this->cuentas_model->getCuentas($filter);

		$lista = array();
		if ($listado_cuentas != 0) {
			foreach ($listado_cuentas as $indice => $valor) {
				$codigo = $valor->CUE_CodDocumento;
				$tipo_documento = $valor->tipo_documento;
				$fecha = mysql_to_human($valor->CUE_FechaOper);
				$serie = $valor->CPC_Serie;
				$numero = $valor->CPC_Numero;
				$ruc = $valor->rucDni;
				$nombre = $valor->nombre;

				$total_formato = $valor->MONED_Simbolo . ' ' . number_format($valor->CUE_Monto, 2);
				$listado_pagos = $this->cuentaspago_model->listar($valor->CUE_Codigo);
				$avance = $this->pago_model->total_pagos($listado_pagos);

				$saldo = $valor->CUE_Monto - $avance;
				$estado_formato = obtener_estado_de_cuenta($saldo, $avance, $valor->CPC_Fecha, $valor->CPC_FechaVencimiento, false);
				$saldo = $valor->MONED_Simbolo . ' ' . number_format($saldo, 2);

				$btn_pagos = "<button type='button' onclick='modal_pagos($valor->CUE_Codigo)' class='btn btn-default'>
				<img src='".$this->url."images/icono-documentos.png' class='image-size-1b'>
				</button>";

				$btn_ticket = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/TICKET' data-fancybox data-type='iframe'>
				<img src='".$this->url."images/icono_imprimir.png' class='image-size-1b'>
				</button>";

				$btn_a4 = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/a4' data-fancybox data-type='iframe'>
				<img src='".$this->url."images/pdf.png' class='image-size-1b'>
				</button>";

				$btn_pagosPDF = "<button type='button' class='btn btn-default' href='".$this->url."index.php/tesoreria/cuentas/cuenta_pdf/$valor->CUE_Codigo' data-fancybox data-type='iframe'>
				<img src='".$this->url."images/pdf.png' class='image-size-1b'>
				</button>";

				if ( $valor->CLIP_Codigo != '' ){
					$btn_all = "<button type='button' title='CUENTAS' class='btn btn-default' data-fancybox data-type='iframe' href='".$this->url."index.php/tesoreria/cuentas/generarPdfCuentas/$valor->CLIP_Codigo/1/P'>
					<img src='".$this->url."images/icono-doc.png' class='image-size-1b'>
					</button>";

				}
				else{
					$btn_all = "<button type='button' title='CUENTAS' class='btn btn-default' data-fancybox data-type='iframe' href='".$this->url."index.php/tesoreria/cuentas/generarPdfCuentas/$valor->PROVP_Codigo/2/P'>
					<img src='".$this->url."images/icono-doc.png' class='image-size-1b'>
					</button>";
				}

				$lista[] = array(
					0 => $fecha,
					1 => $tipo_documento,
					2 => $serie,
					3 => $numero,
					4 => $nombre,
					5 => $total_formato,
					6 => $saldo,
					7 => $estado_formato,
					8 => $btn_pagos,
					9 => $btn_pagosPDF,
					10 => $btn_all
				);
				$item++;
			}
		}

		unset($filter->start);
		unset($filter->length);

		$filterAll = new stdClass();
		$filterAll->tipo_cuenta = $tipo_cuenta;

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => count($this->cuentas_model->getCuentas($filterAll)),
			"recordsFiltered" => intval( count($this->cuentas_model->getCuentas($filter)) ),
			"data"            => $lista
		);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getCuentaInfo(){
		$cuenta = $this->input->post("cuenta");
		$cuentaResult = $this->cuentas_model->getCuenta($cuenta);

		if ($cuentaResult != NULL){
			$cuentaInfo = array();
			$pagosInfo = array();
			foreach ($cuentaResult as $i => $val) {

				$listado_pagos = $this->cuentaspago_model->listar($val->CUE_Codigo);
				$avance =  ( $listado_pagos == NULL ) ? 0 : $this->pago_model->total_pagos($listado_pagos);
				$estado = obtener_estado_de_cuenta($val->CUE_Monto - $avance, $avance, $val->CPC_Fecha, $val->CPC_FechaVencimiento, false);
				$saldo = $val->CUE_Monto - $avance;
				$btn_prorroga = ($saldo > 0) ? true : false;

				$cuentaInfo = array(
					"cuenta" => $val->CUE_Codigo,
					"total" => number_format($val->CUE_Monto,2),
					"saldo" => number_format($saldo,2),
					"estado" => $estado,

					"moneda" => $val->MONED_Simbolo,

					"comprobante" => $val->CPP_Codigo,
					"documento" => $val->documento,
					"serie" => $val->CPC_Serie,
					"numero" => $this->lib_props->getOrderNumeroSerie($val->CPC_Numero),
					"fechaEmision" => mysql_to_human($val->CPC_Fecha),
					"fechaVencimiento" => mysql_to_human($val->CPC_FechaVencimiento),

					"ruc" => $val->rucDni,
					"razon_social" => $val->razon_social,

					"btn_prorroga" => $btn_prorroga
				);

				$listado_pago = $this->cuentaspago_model->listar($val->CUE_Codigo);

				if ($listado_pago != NULL){
					foreach ($listado_pago as $indice => $valor){
						$formaPago = $this->pago_model->obtener_forma_pago($valor->PAGC_FormaPago);
						$formaPago = ($formaPago != NULL) ? $formaPago : "";

						$noperacion = "";

						if ($valor->PAGC_DepoNro != ""){
							$formaPago = "DEPOSITO";
							$deposito = $valor->PAGC_DepoNro;
							$noperacion = $deposito;
						}

						if ($valor->PAGC_Trans != ""){
							$formaPago = "TRANSFERENCIA";
							$transferencia = $valor->PAGC_Trans;
							$noperacion = $transferencia;
						}

						if ($valor->CHEC_Nro != ""){
							$formaPago = "CHEQUE";
							$ncheque = $valor->CHEC_Nro;
							$noperacion = $ncheque;
						}


						$obs = ($valor->PAGC_Obs != NULL) ? $valor->PAGC_Obs : "";

						$pagosInfo[] = array(
							"fecha" => mysql_to_human($valor->PAGC_FechaOper),
							"serieNumero" => $valor->PAGP_Serie.' - '. $this->lib_props->getOrderNumeroSerie($valor->PAGP_Numero),
							"moneda" => $valor->MONED_Simbolo,
							"monto" => number_format($valor->CPAGC_Monto,2),
							"formaPago" => $formaPago,
							"noperacion" => $noperacion,
							"observacion" => $obs
						);
					}
				}
			}

			$json = array("match" => true, "cuenta" => $cuentaInfo, "pagos" => $pagosInfo);
		}
		else
			$json = array("match" => false);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getCuentasPendientes($tipo_cuenta = '1'){

		$columnas = array(
			0 => "CPC_Fecha",
			1 => "CPC_FechaVencimiento",
			2 => "tipo_documento",
			3 => "CPC_Serie",
			4 => "CPC_Numero",
			5 => "",
			6 => "",
			7 => "",
			8 => "",
			9 => "",
			10 => "",
			11 => ""
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
		$filter->fechai = ($fecha_ini != "") ? $fecha_ini : "";

		$fecha_fin = $this->input->post('fechaf');
		$filter->fechaf = ($fecha_fin != "") ? $fecha_fin : "";

		$filter->tipo_cuenta = $tipo_cuenta;

		$filter->serie = $this->input->post('serie');
		$filter->numero = $this->input->post('numero');

		$filter->cond_pago = "P";

		$filter->cliente = $this->input->post('cliente');
		$filter->proveedor = $this->input->post('proveedor');

		$filter->MONED_Codigo = $this->input->post('monedalista');

		$lista = array();
		if ($filter->cliente != "" || $filter->proveedor != ""){
			$listado_cuentas = $this->cuentas_model->getCuentas($filter);

			if ($listado_cuentas != NULL) {
				foreach ($listado_cuentas as $indice => $valor) {
					$listado_pagos = $this->cuentaspago_model->listar($valor->CUE_Codigo);
					$avance = $this->pago_model->total_pagos($listado_pagos);
					
					$saldo = $valor->CUE_Monto - $avance;

					# ES UNA CUENTA PENDIENTE
					if ($saldo > 0){

						$estado_formato = obtener_estado_de_cuenta($saldo, $avance, $valor->CPC_Fecha, $valor->CPC_FechaVencimiento, false);

						# COMPROBANTE
						$codigo = $valor->CUE_CodDocumento;
						$tipo_documento = $valor->tipo_documento;
						$serie = $valor->CPC_Serie;
						$numero = $this->lib_props->getOrderNumeroSerie($valor->CPC_Numero);

						$btn_ticket = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/TICKET' data-fancybox data-type='iframe'>
						<img src='".$this->url."images/icono_imprimir.png' class='image-size-1'>
						</button>";

						$btn_a4 = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/a4' data-fancybox data-type='iframe'>
						<img src='".$this->url."images/pdf.png' class='image-size-1'>
						</button>";

						$btn_pagar = "<button type='button' onclick='registrar_pago($codigo, $saldo, \"$serie - $numero\")' class='btn btn-default' title='Registrar pago'>
						<img src='".$this->url."images/dolar.png' class='image-size-1'>
						</button>";

						$lista[] = array(
							0 => mysql_to_human($valor->CPC_Fecha),
							1 => mysql_to_human($valor->CPC_FechaVencimiento),
							2 => $tipo_documento,
							3 => $serie,
							4 => $numero,
							5 => $valor->MONED_Simbolo . ' ' . number_format($valor->CUE_Monto, 2),
							6 => $valor->MONED_Simbolo . ' ' . number_format($avance, 2),
							7 => $valor->MONED_Simbolo . ' ' . number_format($saldo, 2),
							8 => $estado_formato,
							9 => $btn_a4,
							10 => $btn_pagar
						);
						$item++;
					}
				}
			}
		}

		unset($filter->start);
		unset($filter->length);

		$filterAll = new stdClass();
		$filterAll->tipo_cuenta = $tipo_cuenta;

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => count($this->cuentas_model->getCuentas($filterAll)),
			"recordsFiltered" => intval( count($this->cuentas_model->getCuentas($filter)) ),
			"data"            => $lista
		);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getNotas($tipo_cuenta = '1'){
		$posDT = -1;
		$columnas = array(
			++$posDT => "tipo_documento",
			++$posDT => "CRED_Fecha",
			++$posDT => "CRED_Serie",
			++$posDT => "CRED_Numero",
			++$posDT => "CRED_total",
			++$posDT => "",
			++$posDT => ""
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
		$filter->fechai = ($fecha_ini != "") ? $fecha_ini : "";

		$fecha_fin = $this->input->post('fechaf');
		$filter->fechaf = ($fecha_fin != "") ? $fecha_fin : "";

		$filter->tipo_cuenta = $tipo_cuenta;

		$filter->serie = $this->input->post('serie');
		$filter->numero = $this->input->post('numero');

		$filter->cliente = $this->input->post('cliente');
		$filter->proveedor = $this->input->post('proveedor');

		$filter->MONED_Codigo = $this->input->post('monedalista');

		$filter->tipo_nota = 'C';
		$notasInfo = $this->cuentas_model->getNotas($filter);

		$lista = array();

		if ($filter->cliente != "" || $filter->proveedor != ""){
			if ($notasInfo != 0) {
				foreach ($notasInfo as $indice => $valor) {
					$codigo = $valor->CRED_Codigo;
					$serie = $valor->CRED_Serie;
					$numero = $this->lib_props->getOrderNumeroSerie($valor->CRED_Numero);

					$btn_a4 = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/notacredito/ver_pdf/$codigo/a4' data-fancybox data-type='iframe'>
					<img src='".$this->url."images/pdf.png' class='image-size-1'>
					</button>";

					$inputNota = "<input type='radio' name='cod_nota' value='$codigo' class='form-control h-2' title='Seleccionar nota' onclick='asignar_importe_nota(\"$valor->CRED_total\")'>";

					$rel_serie = $valor->CPC_Serie;
					$rel_numero = $this->lib_props->getOrderNumeroSerie($valor->CPC_Numero);

					$btn_pagar = "<button type='button' onclick='registrar_pago($valor->CPP_Codigo, $valor->CRED_total, \"$rel_serie - $rel_numero\", \"$codigo\", \"$valor->CRED_total\", \"$valor->MONED_Codigo\")' class='btn btn-default' title='Registrar pago'>
						<img src='".$this->url."images/dolar.png' class='image-size-1'>
						</button>";

					$posDT = -1;
					$lista[] = array(
						++$posDT => $valor->tipo_documento,
						++$posDT => mysql_to_human($valor->CRED_Fecha),
						++$posDT => $serie,
						++$posDT => $numero,
						++$posDT => $valor->MONED_Simbolo . ' ' . number_format($valor->CRED_total, 2),
						++$posDT => $valor->documento_relacionado,
						++$posDT => $valor->CPC_Serie,
						++$posDT => $this->lib_props->getOrderNumeroSerie($valor->CPC_Numero),
						++$posDT => $btn_a4,
						++$posDT => $btn_pagar
					);
					$item++;
				}
			}
		}

		unset($filter->start);
		unset($filter->length);

		$filterAll = new stdClass();
		$filterAll->tipo_cuenta = $tipo_cuenta;

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => count($this->cuentas_model->getNotas($filterAll)),
			"recordsFiltered" => intval( count($this->cuentas_model->getNotas($filter)) ),
			"data"            => $lista
		);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function prorroga(){
		$comprobante = $this->input->post("comprobante");
		$dias = $this->input->post("dias");

		if ($comprobante != "" && $dias != ""){
			$dias = intval($dias);
			$dias = ($dias < 0) ? $dias * -1 : $dias;
    	# ACTUALIZA EN LA DB LAS FECHAS DE EMISION Y VENCIMIENTO DEL COMPROBANTE
			$result = $this->cuentas_model->add_prorroga($comprobante, $dias);
			$json = array("result" => "success");
		}
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function guardar_pago($tipo_cuenta = 1){
		$cliente = ($tipo_cuenta == 1) ? $this->input->post("nvopago_cliente") : "";
		$proveedor = ($tipo_cuenta == 2) ? $this->input->post("nvopago_cliente") : "";

		$comprobante = $this->input->post("nvopago_comprobante");
		$fecha = $this->input->post("nvopago_fecha");
		$formaPago = $this->input->post("nvopago_formaPago");
		$moneda = $this->input->post("nvopago_moneda");
		$monto = $this->input->post("nvopago_monto");
		$caja = $this->input->post("nvopago_caja");

		$ctacliente = $this->input->post("nvopago_ctacliente");
		$micta = $this->input->post("nvopago_miscta");

		$nrocheque = $this->input->post("nvopago_nrocheque");
		$emisioncheque = $this->input->post("nvopago_emisioncheque");
		$vencimientocheque = $this->input->post("nvopago_vencimientocheque");
		$nrodeposito = $this->input->post("nvopago_nrodeposito");
		$nrotrans = $this->input->post("nvopago_nrotrans");

		$observacion = $this->input->post("nvopago_observacion");
		$codigo_nota = $this->input->post("cod_nota");

		if ($monto == "" || $monto == 0){
			$monedaPago = $this->moneda_model->getById($moneda);
			$json = array("result" => "error", "message" => "Monto invalido.", "detalle" => "El monto ingresado ($monedaPago $monto) no es un monto valido.");
			echo json_encode($json);
			die();
		}
		# INFORMACION DE LA CUENTA
		$cuentaInfo = $this->cuentas_model->getCuentaComprobante($comprobante);
		$cuentaPago = false;

		if ($cuentaInfo != NULL){
			$tdc = $this->tipocambio_model->getCambio($moneda, $fecha, $cuentaInfo[0]->MONED_Codigo);

			if ($tdc == NULL || $tdc == ""){
				$json = array("result" => "error", "message" => "Tipo de cambio no registrado para la fecha: $fecha", "detalle" => "");
				echo json_encode($json);
				die();
			}

			$listado_pagos = $this->cuentaspago_model->listar($cuentaInfo[0]->CUE_Codigo);
			$avance = $this->pago_model->total_pagos($listado_pagos);
			
			$saldo = $cuentaInfo[0]->CUE_Monto - $avance;
			$importe = round(($monto * $tdc), 3);

			$monedaPago = $this->moneda_model->getById($moneda);
			$monedaCuenta = $this->moneda_model->getById($cuentaInfo[0]->MONED_Codigo);

			if ($saldo < $importe){
				$excedente = round( abs($saldo - $importe), 2);
				$json = array("result" => "error",
											"message" => "El importe indicado supera el saldo de la cuenta.",
											"detalle" => "Pago: ".$monedaPago->MONED_Simbolo." $monto (Pago: ".$monedaCuenta->MONED_Simbolo." $importe) <br> Excedente: ".$monedaCuenta->MONED_Simbolo." $excedente"
										);
				echo json_encode($json);
				die();
			}
		}
		else{
			$json = array("result" => "error", "message" => "Cuenta no disponible", "detalle" => "La cuenta no esta disponible, intente realizar el pago nuevamente.");
			echo json_encode($json);
			die();
		}

		$tipo = ( $tipo_cuenta == 1 ) ? 20 : 21;
		$correlativo = $this->configuracion_model->obtener_numero_documento($this->compania, $tipo);

  	# REGISTRAR EL PAGO
		$filter = new stdClass();
		$filter->PAGC_TipoCuenta = $tipo_cuenta;
		$filter->PAGP_Serie = $correlativo[0]->CONFIC_Serie;
		$filter->PAGP_Numero = $this->lib_props->getOrderNumeroSerie($correlativo[0]->CONFIC_Numero + 1);
		$filter->PAGC_FechaOper = $fecha;
		$filter->CLIP_Codigo = $cliente;
		$filter->PROVP_Codigo = $proveedor;
		$filter->PAGC_TDC = $tdc;
		$filter->PAGC_Monto = $monto;
		$filter->MONED_Codigo = $moneda;
		$filter->PAGC_FormaPago = $formaPago;

		# CUENTA BANCARIA DEL CLIENTE | PROVEEDOR
		$filter->CUENT_CodigoCP = $ctacliente;
		# MI CUENTA BANCARIA
		$filter->CUENT_CodigoEmpresa = $micta;

		$filter->PAGC_DepoNro = $nrodeposito;

		## AGREGA PRIMERO REGISTRO A LA TABLA CHEQUE (Esto lo hace el modelo -> pago_model->insertar())
		$filter->CHEC_Nro = $nrocheque;
		$filter->CHEC_FechaEmision = $emisioncheque;
		$filter->CHEC_FechaVencimiento = $vencimientocheque;

		$filter->PAGC_NotaCredito = ($codigo_nota != "") ? $codigo_nota : NULL;

		$filter->PAGC_Trans = $nrotrans;
		$filter->PAGC_Saldo = "";

		$filter->PAGC_Obs = $observacion;
		$filter->COMPP_Codigo = $this->compania;
		$filter->PAGC_FechaRegistro = date("Y-m-d H:i:s");
		$filter->PAGC_FlagEstado = "1";

		$this->db->trans_begin();
		$idPago = $this->pago_model->insertar($filter);

		# ASOCIAR EL PAGO A LA CUENTA
		if ($cuentaInfo != NULL){
			$filter = new stdClass();
			$filter->CUE_Codigo 	= $cuentaInfo[0]->CUE_Codigo;
			$filter->PAGP_Codigo 	= $idPago;
			$filter->CPAGC_TDC 		= $tdc;
			$filter->CPAGC_Monto 	= $monto;
			$filter->MONED_Codigo 	= $moneda;

			$cuentaPago = $this->cuentaspago_model->insertar($filter);

			$importe = $monto * $tdc;
			$estado = ($cuentaInfo[0]->CUE_Monto > $importe) ? "A" : "C";
			$this->cuentas_model->modificar_estado($cuentaInfo[0]->CUE_Codigo, $estado);

    	# SI UNA CAJA ESTA ASOCIADA AL DOCUMENTO, REGISTRA EL MOVIMIENTO EN LA CAJA
			if ($caja != "" && $caja != NULL){
				$filter = new stdClass();
        # "" PARA INGRESAR UN REGISTRO NUEVO
				$filter->CAJAMOV_Codigo = "";
				$filter->CAJA_Codigo = $caja;
				$filter->PAGP_Codigo = $idPago;
				$filter->RESPMOV_Codigo = NULL;
				$filter->CUENT_Codigo = $cuentaInfo[0]->CUE_Codigo;
				$filter->MONED_Codigo = $moneda;
				$filter->CAJAMOV_Monto = $monto;
        # (V:1) = INGRESO | (C:2) = EGRESO
				$filter->CAJAMOV_MovDinero = ($tipo_cuenta == 1) ? 1 : 2;
				$filter->FORPAP_Codigo = 1;
				$filter->CAJAMOV_FechaRecep = $fecha;

				$justification = "REGISTRO DE PAGO DEL DOCUMENTO: ".$cuentaInfo[0]->CPC_Serie."-".$this->lib_props->getOrderNumeroSerie($cuentaInfo[0]->CPC_Numero). ". PAGO: ".$correlativo[0]->CONFIC_Serie."-".$this->lib_props->getOrderNumeroSerie($correlativo[0]->CONFIC_Numero + 1);

				$filter->CAJAMOV_Justificacion = $justification;
				$filter->CAJAMOV_Observacion   = $observacion;
				$filter->CAJAMOV_FlagEstado    = "1";
				$filter->CAJAMOV_CodigoUsuario = $this->usuario;
                                $filter->CPP_Codigo 	       = NULL;
				$this->movimientos->guardar_movimiento($filter);
			}
		}

    $this->db->trans_rollback();
    /*
		if($this->db->trans_status() == false){
      $this->db->trans_rollback();
			$json = array("result" => "error", "message" => "Error al grabar pago, los cambios no fueron guardados.", "detalle" => "");
			echo json_encode($json);
			die();
		}
    else
      $this->db->trans_commit();
    */

		$json = ($cuentaPago) ? array("result" => "success") : array("result" => "error");
		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function cuenta_pdf($codigo){			
		$this->lib_props->cuenta_pdf($codigo);		
	}
	##  -> End

	##  -> Begin
	public function generarPdfCuentas($codigo, $tipo_cuenta, $estadoPago = 'T'){

		$filter = new stdClass();
		$filter->cliente = $codigo;
		$filter->proveedor = $codigo;
		$filter->tipo_cuenta = $tipo_cuenta;
		$filter->cond_pago = $estadoPago;

		$this->lib_props->cuentas_pdf($filter);		
	}
	##  -> End

  ###############################
	####### FUNCTIONS OLDS
	###############################

	public function nuevo($tipo_cuenta = '1'){
		
		$codigo = "";
		$this->session->unset_userdata('estado_pago2');
		$data['form_open'] = form_open(base_url() . 'index.php/tesoreria/cuentas/grabar', array("name" => "frmCuenta", "id" => "frmCuenta"));
		$data['form_close'] = form_close();

		$filter = new stdClass();
		$filter->TIPCAMC_Fecha = date('Y-m-d', time());
		$filter->TIPCAMC_MonedaDestino = '2';
		$temp = $this->tipocambio_model->buscar($filter);
		$data['tdc'] = count($temp) > 0 ? $temp[0]->TIPCAMC_FactorConversion : '';

		$data['detalle_cuentas'] = array();

		$data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');

		$data['titulo'] = "REGISTRAR PAGOS";
		$data['tipo_cuenta'] = $tipo_cuenta;
		$data['alerta'] = $this->seleccionar_alerta();
		$compania = $this->compania;
		if($tipo_cuenta == '1')
			$tipo = 20;
		else
			$tipo = 21;

		$data['oculto'] = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_cuenta' => $tipo_cuenta));

		$cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);

            //$ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'F');
            $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_f'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);

		$data['cmbCaja'] = $this->OPTION_generador($this->movimiento_model->listCajatotal(), 'CAJA_Codigo','CAJA_Nombre','');
		$this->layout->view('tesoreria/cuentas_pago', $data);
	}

	public function getOrderNumeroSerie($numero){
	 
		$cantidad=strlen($numero);
		
		if($cantidad==1){
		    $dato ="00000$numero";
		}

		if($cantidad==2){
		     $dato ="0000$numero";
		}
		
		if($cantidad==3){
		    $dato ="000$numero";
		}
		
		if($cantidad==4){
		    $dato= "00$numero";
		}
		
		if($cantidad==5){
		    $dato ="0$numero";
		}
		
		if($cantidad==6){
		    $dato ="$numero";
		}
		
		return $dato;
	}

	public function grabar(){
		 
		$filterres = new stdClass();
		$filtero = new stdClass();
		$usuario =$this->usuario;
		$fechaRegistro = mdate("%Y-%m-%d ", time());
		 
		 
		$datos = array();
		if ($this->input->post('tipo_cuenta') == '1' && $this->input->post('cliente') == '')
			exit('{"result":"error", "campo":"ruc_cliente"}');
			
		if ($this->input->post('tipo_cuenta') == '2' && $this->input->post('proveedor') == '')
			exit('{"result":"error", "campo":"ruc_proveedor"}');

		if ($this->input->post('monto') == '' || $this->input->post('monto') == '0')
			exit('{"result":"error", "campo":"monto"}');

		if ($this->input->post('forma_pago') == '2' && $this->input->post('banco') == '')
			exit('{"result":"error", "campo":"banco"}');

		#if ($this->input->post('forma_pago') == '2' && $this->input->post('ctacte') == '')
			//exit('{"result":"error", "campo":"ctacte"}');

		if ($this->input->post('forma_pago') == '3' && $this->input->post('nroCheque') == '')
			exit('{"result":"error", "campo":"nroCheque"}');

		if ($this->input->post('forma_pago') == '3' && $this->input->post('fechaEmi') == '')
			exit('{"result":"error", "campo":"fechaEmi"}');

		if ($this->input->post('forma_pago') == '3' && $this->input->post('fechaVenc') == '')
			exit('{"result":"error", "campo":"fechaVenc"}');

		if ($this->input->post('forma_pago') == '4' && $this->input->post('factura') == '')
			exit('{"result":"error", "campo":"factura"}');

		// NOTA DE CREDITO
		if ($this->input->post('forma_pago') == '5' && $this->input->post('codigoNota') == '0')
			exit('{"result":"error", "campo":"notaCredito"}');

		if ($this->input->post('forma_pago') == '6' && $this->input->post('obsDesc') == '')
			exit('{"result":"error", "campo":"obsDesc"}');

		#if ($this->input->post('forma_pago') == '7' && $this->input->post('trans') == '')
			//exit('{"result":"error", "campo":"trans"}');

		$filter = new stdClass();
		$filter->PAGC_TipoCuenta = $this->input->post('tipo_cuenta');
		$filter->PAGC_FechaOper = human_to_mysql($this->input->post('fecha'));
		
		if ($this->input->post('tipo_cuenta') == '1')
			$filter->CLIP_Codigo = $this->input->post('cliente');
		else
			$filter->PROVP_Codigo = $this->input->post('proveedor');
		
		$filter->PAGC_TDC = $this->input->post('tdc');
		$filter->PAGC_Monto = $this->input->post('monto');
		$filter->MONED_Codigo = $this->input->post('moneda');
		$filter->PAGC_FormaPago = $this->input->post('forma_pago');
		
		if ($this->input->post('observacion') != '')
			$filter->PAGC_Obs = $this->input->post('observacion');

		if ($filter->PAGC_FormaPago == '2' && $this->input->post('banco') != '')
			$filter->PAGC_DepoNro = $this->input->post('forma_pago');
		
		if ($filter->PAGC_FormaPago == '2' && $this->input->post('ctacte') != '' || $filter->PAGC_FormaPago == '2' && $this->input->post('miCtacte') != ''){
			$filter->PAGC_DepoCta = $this->input->post('ctacte');

			$tCuenta = ( $filter->PAGC_TipoCuenta == 1) ? 'CLIENTE' : 'PROVEEDOR';
			$filter->PAGC_Obs .= "<br>CUENTAS BANCARIAS<br>";

			if ($this->input->post('ctacte') != ''){
				$bancoCP = $this->banco_model->obtener($this->input->post('banco'));
				$cuentaCP = $this->banco_model->obtenerCuenta($this->input->post('ctacte'));
				$filter->PAGC_Obs .= "<br><b>".$bancoCP[0]->BANC_Nombre.".</b><br><b>TITULAR:</b>".$cuentaCP[0]->CUENT_Titular.". <br><b>NRO. DE CUENTA:</b> ".$cuentaCP[0]->CUENT_NumeroEmpresa."<br>";
			}

			if ($this->input->post('miCtacte') != ''){
				$bancoE = $this->banco_model->obtener($this->input->post('miBanco'));
				$cuentaE = $this->banco_model->obtenerCuenta($this->input->post('miCtacte'));
				$filter->PAGC_Obs .= "<br>$_SESSION[nombre_empresa] <br><b>".$bancoE[0]->BANC_Nombre.".</b><br><b>TITULAR:</b> ".$cuentaE[0]->CUENT_Titular.".<br><b>NRO. DE CUENTA</b> ".$cuentaE[0]->CUENT_NumeroEmpresa;
			}
		}
		
		if ($filter->PAGC_FormaPago == '3' && $this->input->post('nroCheque') != '')
			$filter->nroCheque = $this->input->post('nroCheque');
		
		if ($filter->PAGC_FormaPago == '3' && $this->input->post('fechaEmi') != '')
			$filter->fechaEmi = human_to_mysql($this->input->post('fechaEmi'));
		
		if ($filter->PAGC_FormaPago == '3' && $this->input->post('fechaVenc') != '')
			$filter->fechaVenc = human_to_mysql($this->input->post('fechaVenc'));
		
		if ($filter->PAGC_FormaPago == '4' && $this->input->post('factura') != '')
			$filter->PAGC_Factura = $this->input->post('factura');
		
		if ($filter->PAGC_FormaPago == '5' && $this->input->post('codigoNota') != '')
			$filter->PAGC_NotaCredito = $this->input->post('codigoNota');
		
		if ($filter->PAGC_FormaPago == '6' && $this->input->post('obsDesc') != '')
			$filter->PAGC_DescObs = $this->input->post('obsDesc');

		if ($filter->PAGC_FormaPago == '7' && $this->input->post('trans') != '')
			$filter->PAGC_Trans = $this->input->post('trans');
		
		$filter->PAGC_Saldo = $this->input->post('saldo');
		$filter->COMPP_Codigo = $this->compania;
		$comprobanteAfectado = $this->input->post('nota_codDocumento1');
		$filter->PAGP_Serie= $this->input->post('compra_serie');
		$filter->PAGP_Numero= $this->input->post('compra_numero');

		$cod_pago = $this->pago_model->insertar($filter, $this->input->post('tipo_cuenta'), $this->input->post('forma_pago'), $this->compania);

		/*********************/
		$MovimientoDineros = $this->input->post('tipo_cuenta');

		if($MovimientoDineros == 1){
			$GiradorBeneficiario = "G";
			
			$codigo = $this->input->post('cliente');
			$filterres->CLIP_Codigo=$codigo; //CLIENTE
		}else{
				$GiradorBeneficiario = "B";
				$codigo = $this->input->post('proveedor');
				$filterres->PROVP_Codigo=$codigo; // PORVEEDOR
		}
				 
		//     			echo "<script>alert('Proveedor  y Cliente : ".$codigo."')</script>";
			 
		$codigobuscarcaja = $this->movimiento_model->buscar_cajamovimiento($codigo,$GiradorBeneficiario);
				 
			
		/***** CJI_RESPONSABLEMOVIMIENTO ******/
		$filterres->RESPNMOV_TipBenefi = $GiradorBeneficiario;
		$fechaIngreso =  human_to_mysql($this->input->post('fecha'));
		$filterres->RESPNMOV_FechaIngreso = $fechaIngreso;
		$filterres->RESPNMOV_CodigoUsuario = $usuario;
		$filterres->RESPNMOV_FlagEstado = "1";
		$codigomovimiento = $this->movimiento_model->insertar_responsablevimiento($filterres);


		/***** CJI_CAJAMOVIMIENTO ****/
		$filtero->RESPMOV_Codigo = $codigomovimiento;
		$filtero->PAGP_Codigo = $cod_pago;
		
		if($MovimientoDineros == 1){ // BENEFICIARIO
			$filtero->CAJAMOV_MovDinero = 2;
			$filtero->CAJAMOV_TipoRespo = 40;
			$filtero->CAJA_Codigo = $this->input->post("idcajadiaria");
			$filtero->CUNTCONTBL_Codigo_B = 0;
			$filtero->CUENT_Codigo_B = $this->input->post("idcuentacaja");
			$filtero->CAJAMOV_Monto_B = 0;
			$filtero->CAJAMOV_FormaPago_B = $this->input->post("forma_pago");
			$filtero->CAJAMOV_FechaSistema = $fechaIngreso;
			$filtero->CAJAMOV_CodigoUsuario = $usuario;
		}else{ //2 = GIRADOR
			$filtero->CAJAMOV_MovDinero = 1;
			$filtero->CAJAMOV_TipoRespo = 30;
			$filtero->CAJA_Codigo = $this->input->post("idcajadiaria");
			$filtero->CUNTCONTBL_Codigo_G = 0;
			$filtero->CUENT_Codigo_G = $this->input->post("idcuentacaja");
			$filtero->CAJAMOV_Monto_G = 0;
			$filtero->CAJAMOV_FormaPago_G = $this->input->post("forma_pago");
			$filtero->CAJAMOV_FechaSistema = $fechaIngreso;
			$filtero->CAJAMOV_CodigoUsuario = $usuario;
		}
					 
		$this->movimiento_model->insertar_cajamovimiento($filtero);
			
		/*************************/

		$listado_cuentas = $this->cuentas_model->buscar($this->input->post('tipo_cuenta'), ($this->input->post('tipo_cuenta') == '1' ? $this->input->post('cliente') : $this->input->post('proveedor')));

		if ($this->input->post('posiciones_pagos')) {
			$posiciones = $this->input->post('posiciones_pagos');
			foreach ($posiciones as $valor) {
				foreach ($listado_cuentas as $cambiar) {
					if ($cambiar->CUE_CodDocumento == $valor) {
						$nuevaposicion[] = $cambiar;
					}
				}
			}
			$listado_cuentas = $nuevaposicion;
		}

		$resultado = array();
		$monto = $this->input->post('monto');

		$codigoCuenta = NULL;
		$codigoDocumento = NULL;
		$codigoEscojido = FALSE;

		if (is_array($listado_cuentas)) {
			foreach ($listado_cuentas as $cuenta) {
				if ($monto == 0)
					break;
		
				$listado_pagos = $this->cuentaspago_model->listar($cuenta->CUE_Codigo, '');
				$avance = $this->pago_model->sumar_pagos($listado_pagos, $this->input->post('moneda'));
				$total = cambiar_moneda($cuenta->CUE_Monto, $this->input->post('tdc'), $cuenta->MONED_Codigo, $this->input->post('moneda'));
				$saldo = $total - $avance;
				$lista_moneda = $this->moneda_model->obtener($this->input->post('moneda'));

				if ($monto > $saldo) {
					$pago = $saldo;
					$monto -= $saldo;
					$avance = $total;
				} else {
					$pago = $monto;
					$avance += $monto;
					$monto = 0;
				}

				$filter = new stdClass();
				$filter->CUE_Codigo = $cuenta->CUE_Codigo;
		
				if($codigoEscojido == FALSE) {
					$codigoCuenta = $cuenta->CUE_Codigo;
					$codigoDocumento = $cuenta->CUE_CodDocumento;
					$codigoEscojido = TRUE;
				}
		
				$filter->PAGP_Codigo = $cod_pago;
				$filter->CPAGC_TDC = $this->input->post('tdc');
				$filter->CPAGC_Monto = $pago;
				$filter->MONED_Codigo = $this->input->post('moneda');
				$cod_cuentaspago = $this->cuentaspago_model->insertar($filter);
				$this->cuentas_model->modificar_estado($cuenta->CUE_Codigo, ($avance == $total ? 'C' : 'A'));
			}
		}

		$insertNotaCredito = FALSE;
		
		if ($this->input->post('forma_pago') == '5' && $this->input->post('codigoNota') != '0') {
			$datosComprobante = $this->notacredito_model->buscarComprobante_nota($codigoDocumento);
		
			if($datosComprobante != NULL){
				$insertNotaCredito = $this->notacredito_model->modificar_notaCredito($datosComprobante, $this->input->post('codigoNota'));
			}
		}

		$datos = array(
				'cod_cajamov' => $pago_cajamov,
				'comprobanteAfectado' => $comprobanteAfectado,
				'cod_pago' => $cod_pago,
				'cod_cuentaspago' => $cod_cuentaspago,
				'nota' => $insertNotaCredito,
				'result' => "ok"
		);

		echo json_encode($datos);
	}

	public function ventana_muestra_notaCredito_cliente($cliente){

		if($cliente == "" || $cliente <= 0 || $cliente == NULL){
			echo "Error en levantar la nota de credito. " . "<a href='".base_url().'index.php/tesoreria/cuentas/nuevo/1'."' >Click Aqui</a>";
		}else {
			$data['cliente'] = $cliente;
			$datosCliente = $this->cliente_model->obtener($cliente);
			$data['datosCliente'] = $datosCliente;
			// Notas de credito => return NULL O array
			$notaCredito = $this->cuentas_model->buscar_notas_credito_cliente($cliente);
			$data['notas'] = $notaCredito;
			$this->load->view('tesoreria/ventana_muestra_notacredito', $data);
		}
	}

	public function ventana_muestra_notaCredito_proveedor($proveedor){

		if($proveedor == "" || $proveedor <= 0 || $proveedor == NULL){
			echo "Error en levantar la nota de credito. <a href='".base_url()."index.php/tesoreria/cuentas/nuevo/1'>Click Aqui</a>";
		}else {
			$data['cliente'] = $proveedor;
			$datosCliente = $this->proveedor_model->obtener_proveedor_info($proveedor);
			$data['datosCliente'] = $datosCliente;
			// Notas de credito => return NULL O array
			$notaCredito = $this->cuentas_model->buscar_notas_credito_proveedor($proveedor);
			$data['notas'] = $notaCredito;
			$this->load->view('tesoreria/ventana_muestra_notacredito', $data);
		}
	}

	public function JSON_cuentas_pendientes()
	{
		$tipo_cuenta = $this->input->post('tipo_cuenta');
		$codigo = $this->input->post('codigo');
		$monto = $this->input->post('monto');
		$moneda = $this->input->post('moneda');
		$tdc = $this->input->post('tdc');
		$aplica_pago = $this->input->post('aplica_pago');
		$posiciones = 0;
		$order = $this->input->post('order');
		$estado = array('V', 'A');
		$listado_cuentas = $this->cuentas_model->buscar($tipo_cuenta, $codigo, $estado, '', '', $order);
		//ordenar array segun otro array gcbq
		if ($order == '') {
			$posiciones = $this->input->post('posiciones');
			if ($posiciones) {
				foreach ($posiciones as $valor) {
					foreach ($listado_cuentas as $cambiar) {
						if ($cambiar->CUE_CodDocumento == $valor) {
							$nuevaposicion[] = $cambiar;
						}
					}
				}
				$listado_cuentas = $nuevaposicion;
			}
		}
		//

		$resultado = array();
		if (is_array($listado_cuentas)) {
			foreach ($listado_cuentas as $indice => $cuenta) {
				$temp = $this->obtener_nombre_numdoc(($tipo_cuenta == '1' ? 'CLIENTE' : 'PROVEEDOR'), $codigo);
				$ruc = $temp['numdoc'];
				$nombre = $temp['nombre'];
				$listado_pagos = $this->cuentaspago_model->listar($cuenta->CUE_Codigo);
				$avance = $this->pago_model->sumar_pagos($listado_pagos, $moneda);
				$total = cambiar_moneda($cuenta->CUE_Monto, $tdc, $cuenta->MONED_Codigo, $moneda);
				$saldo = $total - $avance;
				$lista_moneda = $this->moneda_model->obtener($moneda);
				$cod_documento = $cuenta->CUE_CodDocumento;
				$serie = $cuenta->CPC_Serie;
				$numero = $cuenta->CPC_Numero;
				$tipo_doc = $cuenta->CPC_TipoDocumento;
				$desc_cod = "";
				if ($tipo_doc == 'F')
					$desc_cod = 'FACTURA';
					else if ($tipo_doc == 'B')
						$desc_cod = 'BOLETA';
						if ($aplica_pago == '1') {
							if ($monto > $saldo) {
								$monto -= $saldo;
								$avance = $total;
							} else {
								$avance += $monto;
								$monto = 0;
							}
							$saldo = $total - $avance;
						}
						$resultado[] = array('fecha' => mysql_to_human($cuenta->CUE_FechaOper),
								"ruc" => $ruc, "nombre" => $nombre, "moneda" => $lista_moneda[0]->MONED_Simbolo . $order,
								"total" => number_format($total, 4), "avance" => number_format($avance, 4),
								"saldo" => number_format($saldo, 4), "saldo_total" => number_format($monto, 4),
								"serie" => $serie, "numero" => $numero, "tipo_doc" => $tipo_doc, 'desc_doc' => $desc_cod,
								"cod_documento" => $cod_documento, "total_int" => (double)$total, "avance_int" => (double)$avance,
								"saldo_int" => (double)$saldo, "saldo_total_int" => (double)$monto);
			}
		}
		$opcional = count($resultado);
		if($opcional > 0) {
			echo json_encode($resultado);
		}else{
			$error = array(
					'errores' => 'warning'
			);

			echo json_encode($error);
		}
	}

	public function JSON_notas_credito_pendientes($tipo_cuenta, $codigo)
	{
		$listado_cuentas = $this->cuentas_model->buscar_notas_credito($tipo_cuenta, $codigo);
		if ($listado_cuentas != NULL) {
			echo json_encode($listado_cuentas);
		}else{
			$errores = array(
					'warning' => 'Sin notas',
			);
			echo json_encode($errores);
		}
	}

	function obtener_nombre_numdoc($tipo, $codigo){
		$nombre = '';
		$numdoc = '';

		if ($tipo == 'CLIENTE') {
			$datos_cliente = $this->cliente_model->obtener($codigo);

			if ($datos_cliente) {
				$nombre = $datos_cliente->nombre;
				$numdoc = $datos_cliente->ruc;
				$empresa = $datos_cliente->empresa;
			}
		}
		else
			if ($tipo == 'PROVEEDOR') {
				$datos_proveedor = $this->proveedor_model->obtener($codigo);

				if ($datos_proveedor) {

					$nombre = $datos_proveedor->nombre;
					$numdoc = $datos_proveedor->ruc;
					$empresa = $datos_cliente->empresa;
				}
			}
		return array('numdoc' => $numdoc, 'nombre' => $nombre, 'idEmpresa' => $empresa);
	}

	public function seleccionar_alerta($indDefault = '')
	{
		$array_dist = $this->cuentas_model->listar_alertas();
		$arreglo = array();
		if (count($array_dist) > 0) {
			foreach ($array_dist as $indice => $valor) {
				$indice1 = $valor->BANP_Codigo;
				$valor1 = $valor->BANC_Nombre;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('', '.::SELECCIONE::.'));
		return $resultado;
	}

	public function generarPdfCuentas_old($tipo_cuenta = NULL, $codigo = NULL, $monto = NULL, $moneda = NULL, $tdc = NULL, $aplica_pago = NULL, $order = NULL, $nombre_cliente = NULL){

		$posiciones = 0;
		$estado = array('V', 'A');
		$listado_cuentas = $this->cuentas_model->buscar($tipo_cuenta, $codigo, $estado, '', '', $order);

		//ordenar array segun otro array gcbq
		if ($order == '') {
			if ($this->input->post('posiciones')) {
				$posiciones = $this->input->post('posiciones');
				foreach ($posiciones as $valor) {
					foreach ($listado_cuentas as $cambiar) {
						if ($cambiar->CUE_CodDocumento == $valor) {
							$nuevaposicion[] = $cambiar;
						}
					}
				}
				$listado_cuentas = $nuevaposicion;
			}
		}
		//

		$total1 = 0;
		$avance1 = 0;
		$saldo1 = 0;

		$this->load->library('cezpdf');
		$this->load->helper('pdf_helper');
		$this->cezpdf = new Cezpdf('a4', 'portrait');
		$resultado = array();
		$db_data = array();
		if (is_array($listado_cuentas)) {
			foreach ($listado_cuentas as $indice => $cuenta) {
				$temp = $this->obtener_nombre_numdoc(($tipo_cuenta == '1' ? 'CLIENTE' : 'PROVEEDOR'), $codigo);
				$ruc = $temp['numdoc'];
				$nombre = $temp['nombre'];
				$listado_pagos = $this->cuentaspago_model->listar($cuenta->CUE_Codigo);
				$avance = $this->pago_model->sumar_pagos($listado_pagos, $moneda);
				$total = cambiar_moneda($cuenta->CUE_Monto, $tdc, $cuenta->MONED_Codigo, $moneda);
				$saldo = $total - $avance;
				$lista_moneda = $this->moneda_model->obtener($moneda);
				$cod_documento = $cuenta->CUE_CodDocumento;
				$serie = $cuenta->CPC_Serie;
				$numero = $cuenta->CPC_Numero;
				$tipo_doc = $cuenta->CPC_TipoDocumento;
				$desc_cod = "";
				if ($tipo_doc == 'F')
					$desc_cod = 'FACTURA';
					else if ($tipo_doc == 'B')
						$desc_cod = 'BOLETA';
						if ($aplica_pago == '1') {
							if ($monto > $saldo) {
								$monto -= $saldo;
								$avance = $total;
							} else {
								$avance += $monto;
								$monto = 0;
							}
							$saldo = $total - $avance;
						}


						$total1 = $total1 + $total;
						$avance1 = $avance1 + $avance;
						$saldo1 = $saldo1 + $saldo;


						$db_data[] = array(
								'cols1' => $indice + 1,
								'cols2' => mysql_to_human($cuenta->CUE_FechaOper),
								'cols3' => $desc_cod,
								'cols4' => $serie . "-" . $numero,
								'cols5' => $lista_moneda[0]->MONED_Simbolo,
								'cols6' => $total,
								'cols7' => $avance,
								'cols8' => $saldo

						);


			}
			$db_data[] = array(
					'cols1' => '',
					'cols2' => '',
					'cols3' => 'Total',
					'cols4' => '',
					'cols5' => '',
					'cols6' => $total1,
					'cols7' => $avance1,
					'cols8' => $saldo1

			);

		}

		/* Cabecera */
		$this->cezpdf->ezText("Translogint EIRL", 10, array("leading" => 10, "left" => 40));
		$this->cezpdf->ezText('', '', array('leading' => 10));

		/* Datos del cliente */
		$this->cezpdf->ezText("Cliente: " . $ruc . " " . $nombre_cliente, 10, array("leading" => 10, "left" => 40));

		$this->cezpdf->ezText('', '', array('leading' => 10));
		/* Listado de detalles */

		$col_names = array(
				'cols1' => 'Item',
				'cols2' => 'Fecha',
				'cols3' => 'Comprobante',
				'cols4' => 'Serie / Numero.',
				'cols5' => 'Moneda.',
				'cols6' => 'Avance.',
				'cols7' => 'Saldo',
				'cols8' => 'Estado'

		);

		$this->cezpdf->ezTable($db_data, $col_names, '', array(
				'width' => 750,
				'showLines' => 1,
				'shaded' => 0,
				'showHeadings' => 1,
				'xPos' => 'center',
				'fontSize' => 8,
				'cols' => array(
						'cols1' => array('width' => 30, 'justification' => 'center'),
						'cols2' => array('width' => 70, 'justification' => 'left'),
						'cols3' => array('width' => 70, 'justification' => 'left'),
						'cols4' => array('width' => 40, 'justification' => 'left'),
						'cols5' => array('width' => 40, 'justification' => 'left'),
						'cols6' => array('width' => 50, 'justification' => 'right'),
						'cols7' => array('width' => 60, 'justification' => 'right'),
						'cols8' => array('width' => 60, 'justification' => 'right')
				)
		));
		/* Totales */

		$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
		$this->cezpdf->ezStream($cabecera);
	}

	public function convertFloat($num){
		$int = explode(',',$num);
		$decimal = explode('.',$num);

		for ($i = 0; $i < count($int); $i++)
			$numInt .= $int[$i];

		return floatval($numInt.'.'.$decimal[1]);
	}

	public function obtenerBancosProveedor(){
		$codigoproveedor = $this->input->post('codigoproveedor');
		$result = array();
		if($codigoproveedor!=null && count(trim($codigoproveedor))>0){
			$tipoProveedor = $this->movimiento_model->comboBancoTipoProveedor($codigoproveedor);
			foreach ($tipoProveedor as $indice => $valor){
				$tipoProveedor = $valor-> PROVC_TipoPersona;
			}//1=EMPRESA , 0=PERSONA
			if($tipoProveedor  != 1){
				$SeleccionBanco = $this->movimiento_model->comboBancoProveedorNatural($codigoproveedor);
			}

			if($tipoProveedor  == 1){
				$SeleccionBanco = $this->movimiento_model->comboBancoProveedor($codigoproveedor);
			}
			if($SeleccionBanco !=null && count($SeleccionBanco)){
				foreach($SeleccionBanco as $indice=>$valor){
					$banconombre		= $valor->BANC_Nombre;
					$bancocodigo =	$valor->BANP_Codigo;
					$cajacodigo =	$valor->CAJA_Codigo;
					$result[] = array("banconombre" => $banconombre , "bancocodigo" => $bancocodigo , "cajacodigo" => $cajacodigo  );

				}
			}
		}
		echo json_encode($result);
	}

	public function obtenerMisBancos(){
		$result = array();
		$SeleccionBanco = $this->movimiento_model->comboBancoEmpresa($_SESSION['empresa']);
			if($SeleccionBanco !=null && count($SeleccionBanco)){
				foreach($SeleccionBanco as $indice=>$valor){
					$banconombre		= $valor->BANC_Nombre;
					$bancocodigo =	$valor->BANP_Codigo;
					$cajacodigo =	$valor->CAJA_Codigo;
					$result[] = array("banconombre" => $banconombre , "bancocodigo" => $bancocodigo , "cajacodigo" => $cajacodigo  );

				}
			}
		echo json_encode($result);
	}

	public function obtenerBancosCliente(){
		$codigoCliente = $this->input->post('codigoCliente');
		//     		echo "<script>alert('codiugo de provedodr  : ".$codigoproveedor."')</script>";

		$result = array();

		if($codigoCliente!=null && count(trim($codigoCliente))>0){

			$tipoCLiente = $this->movimiento_model->comboBancoTipoCliente($codigoCliente);
			foreach ($tipoCLiente as $indice => $valor){
				$tipoCLiente = $valor-> CLIC_TipoPersona;
			}//1=EMPRESA , 0=PERSONA
			if($tipoCLiente  != 1){
				$SeleccionBanco = $this->movimiento_model->comboBancoClienteNatural($codigoCliente);
			}

			if($tipoCLiente  == 1){
				$SeleccionBanco = $this->movimiento_model->comboBanco($codigoCliente);
			}

			if($SeleccionBanco !=null && count($SeleccionBanco)){
				foreach($SeleccionBanco as $indice=>$valor){
					$banconombre		= $valor->BANC_Nombre;
					$bancocodigo =	$valor->BANP_Codigo;
					$cajacodigo =	$valor->CAJA_Codigo;
					$result[] = array("banconombre" => $banconombre , "bancocodigo" => $bancocodigo , "cajacodigo" => $cajacodigo  );

				}
			}
		}
		echo json_encode($result);
	}


	public function obtenerCuentasProveedor(){
		$tipo_cuenta = $this->input->post('tipo_cuenta');
		$codigopersona = $this->input->post('codigopersona');
		$codigobanco = $this->input->post('codigobanco');

		$result = array();

		if($codigobanco != null && count(trim($codigobanco))>0){
			//    		echo "<script>alert('tipo_cuenta   :  ".$tipo_cuenta."')</script>";
			if($tipo_cuenta == 1){
				$sleccionCuentaCliente = $this->movimiento_model->SeleccionarCuentaCliente($codigobanco,$codigopersona);
				$separado_por_comas = serialize($sleccionCuentaCliente);
				//      			echo "<script>alert('aber   ".$separado_por_comas."')</script>";
				if($separado_por_comas != "N;"){
					$SeleccionCuenta = $this->movimiento_model->comboCuentaClienteNatural($codigobanco,$codigopersona);

				}else{
					$SeleccionCuenta = $this->movimiento_model->comboCuenta($codigobanco,$codigopersona);

				}
			}else{
				$sleccionCuentaProveedor = $this->movimiento_model->SeleccionarCuentaProveedor($codigobanco,$codigopersona); // ESTO BUSCA al proveedor empresa
				$separado_por_comas = serialize($sleccionCuentaProveedor);
				//     			    			echo "<script>alert('aber   ".$separado_por_comas."')</script>";
				if($separado_por_comas != "N;"){
					/**Aca esta la empresa**/
					$SeleccionCuenta = $this->movimiento_model->comboCuentaProve($codigobanco,$codigopersona);
				}else{
					/**Aca esta la Persona**/
					$SeleccionCuenta = $this->movimiento_model->comboCuentaProveedorNatural($codigobanco,$codigopersona);
				}
			}
			 
			 

				
			if(count($SeleccionCuenta) != null){
				foreach ($SeleccionCuenta as $indice => $valor){
					$nombre = $valor->CUENT_NumeroEmpresa;
					$codigo = $valor->CUENT_Codigo;
					$result[] = array("nombrecuenta" => $nombre , "codigocuenta" => $codigo);
					 
				}
			}
		}
		echo json_encode($result);
	}

	public function obtenerMisCuentas(){
		$codigobanco = $this->input->post('codigobanco');
		$result = array();

		if($codigobanco != null && count(trim($codigobanco))>0){
			$SeleccionCuenta = $this->movimiento_model->comboCuentasEmpresa($codigobanco, $_SESSION['empresa']);
			if(count($SeleccionCuenta) != null){
				foreach ($SeleccionCuenta as $indice => $valor){
					$nombre = $valor->CUENT_NumeroEmpresa;
					$codigo = $valor->CUENT_Codigo;
					$result[] = array("nombrecuenta" => $nombre , "codigocuenta" => $codigo);
					 
				}
			}
		}
		echo json_encode($result);
	}
	 
	public function buscarDatosDeCuenta(){
		$nombreCuentas = $this->input->post('nombrecuentas');
		$result = array();

		$listado_cajas = $this->movimiento_model->obtener_cajadiaria($nombreCuentas);
		if($listado_cajas !=null && count($listado_cajas)){
			foreach($listado_cajas as $indice=>$valor){
				$codigomoneda = $valor->MONED_Codigo;
				if($codigomoneda == 1){
					$generalmoneda= "<img src='".base_url()."images/soles.png' width='35' height='25' border='0' style='margin-bottom:-4%' title='Soles'>";
				}else{
					$generalmoneda= "<img src='".base_url()."images/dolares.png' width='35' height='25' border='0' style='margin-bottom:-4%' title='dolar'>";
				}
				$result[] = array("monedadescripcion" => $generalmoneda);

			}
		}
		echo json_encode($result);
	}
	 
	public function buscarBancosDeCaja(){
		$idnombrecaja = $this->input->post('idcajadiaria');
		$result = array();
		if($idnombrecaja!=null && count(trim($idnombrecaja))>0){
			$listado_bancos = $this->movimiento_model->buscar_bancos_caja($idnombrecaja);
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

	public function obtenerCuentasCaja(){
		$codigocaja = $this->input->post('idcajadiaria');
		$codigobanco = $this->input->post('idbancoscaja');
		 
		$result = array();
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

	public function obtenerDatosCuentaCaja(){
		$nombrecuentacaja = $this->input->post('nombrecuentas');
		$result = array();
		if($nombrecuentacaja!=null && count(trim($nombrecuentacaja))>0){
			$listado_cajas = $this->movimiento_model->obtener_cajadiaria($nombrecuentacaja);
			if($listado_cajas !=null && count($listado_cajas)){
				foreach($listado_cajas as $indice=>$valor){
					$codigomoneda = $valor->moned_codigo;
					if($codigomoneda == 1){
						//    					$generalmoneda = "$";
						$generalmoneda= "<img src='".base_url()."images/dollar.png' width='25' height='25' border='0' style='margin-bottom:-4%' title='dolar'>";

					}else{
						//    					$general = "S/.";
						$generalmoneda= "<img src='".base_url()."images/soles.png' width='25' height='25' border='0' style='margin-bottom:-4%' title='Soles'>";
					}
					$monedadescripcion =	$valor->MONED_Descripcion;
					$result[] = array("monedadescripcion" => $generalmoneda);

				}
			}
		}
		echo json_encode($result);
	}
}

?>