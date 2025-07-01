<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */

class Proveedor extends CI_Controller{

	##  -> Begin
	private $empresa;
	private $compania;
	private $url;
	private $view_js = NULL;
	##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();

		$this->load->library('html');
		$this->load->library('table');

		$this->load->library('pagination');

		$this->load->model('maestros/compania_model');
		$this->load->model('empresa/empresa_model');
		$this->load->model('maestros/persona_model');
		$this->load->model('empresa/directivo_model');
		$this->load->model('maestros/cargo_model');
		$this->load->model('maestros/area_model');
		$this->load->model('maestros/tipoestablecimiento_model');
		$this->load->model('maestros/nacionalidad_model');
		$this->load->model('maestros/tipodocumento_model');
		$this->load->model('maestros/tipocodigo_model');
		$this->load->model('maestros/estadocivil_model');
		$this->load->model('maestros/ubigeo_model');
		$this->load->model('maestros/comercial_model');
		$this->load->model('empresa/proveedor_model');
		$this->load->model('empresa/cliente_model');

		$this->load->model('maestros/tipocliente_model');
		$this->load->model('maestros/emprestablecimiento_model');
		$this->load->model('maestros/formapago_model');
		$this->load->model('maestros/moneda_model');
		$this->load->model('tesoreria/banco_model');

		$this->load->library('lib_props');

		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
		$this->url = base_url();
		$this->view_js = array(0 => "empresa/proveedor.js");
	}

	##  -> Begin
	public function index() {
		$this->proveedores();
	}
	##  -> End

	##  -> Begin
	public function proveedores( $j = "" ){
    ## SELECTS
		$data["documentosNatural"] = $this->tipodocumento_model->listar_tipo_documento();
		$data["documentosJuridico"] = $this->tipocodigo_model->listar_tipo_codigo();

		$data['edo_civil'] = $this->estadocivil_model->listar_estadoCivil();
		$data['nacionalidad'] = $this->nacionalidad_model->listar_nacionalidad();

		$data["cargos"] = $this->cargo_model->getCargos();
		$data["bancos"] = $this->banco_model->listar_banco();

		$data["sector_comercial"] = $this->comercial_model->getComercials();
		$data["tipo_establecimiento"] = $this->tipoestablecimiento_model->getTipoEstablecimientos();

		$data["forma_pago"] = $this->formapago_model->getFpagos();
		$data["monedas"] = $this->moneda_model->listar();
		$data["categorias_cliente"] = $this->tipocliente_model->getCategorias();
		$data["vendedor"] = $this->directivo_model->listarVendedores();

		$data["departamentos"] = $this->ubigeo_model->listar_departamentos();
		$data["provincias"] = $this->ubigeo_model->getProvincias("15");
		$data["distritos"] = $this->ubigeo_model->getDistritos("15","01");

		$data['scripts'] = $this->view_js;

		$data['titulo_tabla']    = "RELACIÓN DE PROVEEDORES";
		$data['titulo_busqueda'] = "BUSCAR PROVEEDOR";
		$this->layout->view('empresa/proveedor_index',$data);
	}
	##  -> End

	##  -> Begin
	public function datatable_proveedor(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "documento",
			++$posDT => "numero",
			++$posDT => "razon_social"
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

		$filter->documento = $this->input->post('documento');
		$filter->nombre = $this->input->post('nombre');

		$proveedorInfo = $this->proveedor_model->getProveedores($filter);
		$records = array();

		if ($proveedorInfo["records"] != NULL) {
			foreach ($proveedorInfo["records"] as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar_proveedor($valor->PROVP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_sucursales = ($valor->PROVC_TipoPersona == 0) ? "" : "<button type='button' onclick='sucursales($valor->EMPRP_Codigo, \"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Sucursales'>
				<img src='".$this->url."public/images/icons/sucursal.png' class='image-size-1b'>
				</button>";

				$btn_contactos = ($valor->PROVC_TipoPersona == 0) ? "" : "<button type='button' onclick='modal_contactos(\"$valor->EMPRP_Codigo\", \"$valor->PERSP_Codigo\", \"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Contactos'>
				<img src='".$this->url."public/images/icons/contactos.png' class='image-size-1b'>
				</button>";

				$btn_bancos = "<button type='button' onclick='modal_CtasBancarias(\"$valor->EMPRP_Codigo\", \"$valor->PERSP_Codigo\", \"$valor->numero - $valor->razon_social\")' class='btn btn-default' title='Bancos'>
				<img src='".$this->url."public/images/icons/banco.png' class='image-size-1b'>
				</button>";

				$btn_deshabilitar = "<button type='button' onclick='deshabilitar_proveedor($valor->PROVP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$btn_documentos = "<button type='button' onclick='docs_emitidos($valor->PROVP_Codigo, \"$valor->numero\", \"$valor->razon_social\")' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/icono-documentos.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->documento,
					++$posDT => $valor->numero,
					++$posDT => $valor->razon_social,
					++$posDT => $btn_editar,
					++$posDT => $btn_documentos,
					++$posDT => $btn_bancos,
					++$posDT => $btn_contactos,
					++$posDT => $btn_sucursales,
					++$posDT => $btn_deshabilitar
				);
			}
		}

		$recordsTotal = ( $proveedorInfo["recordsTotal"] != NULL ) ? $proveedorInfo["recordsTotal"] : 0;
		$recordsFilter = $proveedorInfo["recordsFilter"];

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

	##  -> Begin
	public function getDocumentos(){
    # 0 : NATURAL | 1 : JURIDICO
		$tipo = $this->input->post("tipo");

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
	public function getProveedor(){
		$proveedor = $this->input->post("proveedor");
		$proveedorInfo = $this->proveedor_model->getProveedor($proveedor);

		if ($proveedorInfo != NULL){
			foreach ($proveedorInfo as $key => $val)
				$info = array(
					"proveedor" => $val->PROVP_Codigo,
					"tipo_proveedor" => $val->PROVC_TipoPersona,
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

					"sector_comercial" => $val->SECCOMP_Codigo,

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

	##  -> Begin
	public function guardar_registro(){

		$proveedor = $this->input->post("proveedor");
		$empresa = 0;
		$persona = 0;

		if ( $proveedor != "" ){
			$proveedorInfo = $this->proveedor_model->getProveedor($proveedor);
			$empresa = $proveedorInfo[0]->EMPRP_Codigo;
			$persona = $proveedorInfo[0]->PERSP_Codigo;
		}

		$tipo_proveedor = $this->input->post("tipo_proveedor");
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

		$vendedor = 0;
		$sector_comercial = $this->input->post("sector_comercial");
		$forma_pago = $this->input->post("forma_pago");
		$categoria = $this->input->post("categoria");
		$telefono = $this->input->post("telefono");
		$movil = $this->input->post("movil");
		$fax = $this->input->post("fax");
		$correo = $this->input->post("correo");
		$web = $this->input->post("web");

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
		$clienteInfo->CLIC_TipoPersona   = $tipo_proveedor;
		$clienteInfo->TIPCLIP_Codigo     = $categoria;
		$clienteInfo->CLIC_Vendedor      = $vendedor;
		$clienteInfo->FORPAP_Codigo      = $forma_pago;
		$clienteInfo->CLIC_Digemin       = "";
		$clienteInfo->CLIC_flagCalifica  = 1;
		$clienteInfo->CLIC_FlagEstado    = "1";

    ## PROVEEDOR
		$proveedorInfo = new stdClass();
		$proveedorInfo->PROVC_TipoPersona = $tipo_proveedor;
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

		if ($proveedor != ""){
			if ($tipo_proveedor == "0"){
				if ($persona != 0)
					$this->persona_model->actualizar_persona($persona, $personaInfo);
			}
			else{
				if ($empresa != 0){
					$this->empresa_model->actualizar_empresa($empresa, $empresaInfo);
					$sucursalInfo->EMPRP_Codigo = $empresa;
					$establecimiento = $this->emprestablecimiento_model->actualizar_establecimiento_principal($sucursalInfo);
				}
			}

			$proveedorInfo->EMPRP_Codigo  = $empresa;
			$proveedorInfo->PERSP_Codigo  = $persona;

			$proveedor = $this->proveedor_model->actualizar_proveedor($proveedor, $proveedorInfo);

			if ($proveedor){
				$json_result = "success";
				$json_message = "Actualización satisfactoria.";
			}
			else{
				$json_result = "error";
				$json_message = "El número de documento $numero_documento, ya se encuentra registrado.";
			}
		}
		else{

			if ( $this->empresa_model->documento_exists($numero_documento) == true ){
				$json_result = "error";
				$json_message = "El número de documento $numero_documento, ya se encuentra registrado.";
			}
			else{
				if ($tipo_proveedor == "0")
					$persona = $this->persona_model->insertar_persona($personaInfo);
				else{
					$empresa = $this->empresa_model->insertar_empresa($empresaInfo);

					$sucursalInfo->EMPRP_Codigo = $empresa;
					$establecimiento = $this->emprestablecimiento_model->insertar_establecimiento($sucursalInfo);
				}

				$clienteInfo->CLIC_CodigoUsuario = $this->generateCodeCliente();
				$clienteInfo->EMPRP_Codigo  = $empresa;
				$clienteInfo->PERSP_Codigo  = $persona;

				$proveedorInfo->PERSP_Codigo = $persona;
				$proveedorInfo->EMPRP_Codigo = $empresa;

				$proveedor = $this->cliente_model->insertar_cliente($clienteInfo);
				$proveedor = $this->proveedor_model->insertar_proveedor($proveedorInfo);

				if ($proveedor != 0 && $proveedor != NULL){
					$json_result = "success";
					$json_message = "Registro satisfactorio.";
				}
				else{
					$json_result = "error";
					$json_message = "No fue posible registrar al proveedor. Intentelo nuevamente";
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

	##  -> Begin
	public function deshabilitar_proveedor(){
		$proveedor = $this->input->post("proveedor");

		if ($proveedor != ""){
			$docsExists = $this->proveedor_model->docs_generated_exists($proveedor);

			if ($docsExists == false){
				$filter = new stdClass();
				$filter->PROVC_FlagEstado = "0";
				$filter->PROVC_FechaModificacion = date("Y-m-d H:i:s");
				$oper = $this->proveedor_model->actualizar_proveedor($proveedor, $filter);

				if ($oper){
					$result = "success";
					$message = "Operacion exitosa";
				}
				else{
					$result = "error";
					$message = "¡Ups! Proveedor no eliminado, intentalo nuevamente.";
				}
			}
			else{
				$result = "info";
				$message = "No se pueden eliminar proveedores con documentos asociados.";
			}

			$json = array("result" => $result, "message" => $message);
		}
		else
			$json = array("result" => "error", "message" => "Proveedor no seleccionado.");

		echo json_encode($json);
	}
	##  -> End

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
			++$posDT => "total"
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

		$proveedor = $this->input->post('proveedor');

		$proveedorInfo = $this->proveedor_model->docs_emitidos($proveedor, $filter);
		$lista = array();

		if ($proveedorInfo != NULL) {
			foreach ($proveedorInfo as $i => $val) {
				$posDT = -1;
				$lista[] = array(
					++$posDT => $val->EMPRC_RazonSocial,
					++$posDT => $val->documento,
					++$posDT => $val->fechaRegistro,
					++$posDT => $val->fecha,
					++$posDT => $this->lib_props->getNumberFormat($val->serie,6),
					++$posDT => $this->lib_props->getNumberFormat($val->numero,6),
					++$posDT => $val->total
				);
			}
		}

		unset($filter->start);
		unset($filter->length);
		unset($filter->order);
		unset($filter->dir);

		$filter->count = true;
		$recordsTotal = $this->proveedor_model->docs_emitidos($proveedor, $filter);
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
	public function search_documento(){

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
					$tipo_proveedor = "";
					$data = $object->result;

					if ( $data->documento == 'DNI' ){
						$info = array(
							"documento" => "DNI",
							"dni" => (isset($data->dni)) ? $data->dni : $numero,
							"nombre" => (isset($data->nombres)) ? $data->nombres : "",
							"paterno" => (isset($data->apellido_paterno)) ? $data->apellido_paterno : "",
							"materno" => (isset($data->apellido_materno)) ? $data->apellido_materno : ""
						);
						$tipo_proveedor = 0;
					}
					else if ( $data->documento == 'RUC' ){
						$info = array(
							"documento" => $data->documento,
							"ruc" => $data->ruc,
							"razon_social" => $data->razon_social,
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
						$tipo_proveedor = 1;
					}

					$json = array(
						"exists" => $exists,
						"match" => true,
						"tipo_proveedor" => $tipo_proveedor,
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

  ## FUNCTIONS OLDS

	public function eliminar_proveedor()
	{
		$proveedor = $this->input->post('proveedor');
		$eliminar = $this->proveedor_model->eliminar_proveedor($proveedor);
		echo $eliminar;
	}


	public function eliminar_marca()
	{
		$marca = $this->input->post('marca');
		$empresa = $this->input->post('empresa');
		$this->empresa_model->eliminar_marcaEmpresa($marca, $empresa);
		echo $this->TABLA_marcas('p', $empresa);
	}

	public function eliminar_tipo()
	{
		$tipo = $this->input->post('tipo');
		$proveedor = $this->input->post('proveedor');
		$this->empresa_model->eliminar_tipoProveedor($tipo);
		echo $this->TABLA_tipos('p', $proveedor);
	}

	public function TABLA_tipos($tipo, $proveedor, $pinta = 0)
	{
		$datos_marcasProveedor = $this->empresa_model->obtener_tiposEmpresa($proveedor);

		$tabla = '<table id="tablaTipo" width="98%" class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="1">
		<tr align="center" bgcolor="#BBBB20" height="10px;">
		<td>Nro</td>
		<td>Nombre del tipo de proveedor</td>
		<td>Borrar</td>
		<td>Editar</td>
		</tr>';

		$item = 1;
		if (count($datos_marcasProveedor) > 0) {
			foreach ($datos_marcasProveedor as $valor) {
				$tabla .= '<tr bgcolor="#ffffff">';
				$nombre_marca = $valor->FAMI_Descripcion;
				$codigo = $valor->FAMI_Codigo;
				$registro = $valor->EMPTIPOP_Codigo;
				$codigo = "<input type='hidden' name='tipoCodigo[" . $item . "]' id='tipoCodigo[" . $item . "]' class='cajaMedia' value='" . $codigo . "'>";
				$editar = "&nbsp;";
				$eliminar = "<a href='#' onclick='eliminar_tipo(" . $registro . ");'><img src='" . base_url() . "images/delete.gif' border='0'></a>";
				$tabla .= '<td>' . $item . '</td>';
				$tabla .= '<td>' . $nombre_marca . '</td>';
				$tabla .= '<td>' . $eliminar . '</td>';
				$tabla .= '<td>' . $editar . '</td>';
				$tabla .= '</tr>';
				$item++;
			}
		}
		$tabla .= '</table>';
		if (count($datos_marcasProveedor) == 0)
			$tabla .= '<div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;">NO EXISTEN REGISTROS</div>';

		if ($pinta == '1')
			echo $tabla;
		else
			return $tabla;
	}

	public function TABLA_marcas($tipo, $empresa, $pinta = 0)
	{
		$datos_marcasProveedor = $this->empresa_model->obtener_marcasEmpresa($empresa);

		$tabla = '<table id="tablaMarca" width="98%" class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="1">
		<tr align="center" bgcolor="#BBBB20" height="10px;">
		<td>Nro</td>
		<td>Nombre de la marca</td>
		<td>Borrar</td>
		<td>Editar</td>
		</tr>';

		$item = 1;
		if (count($datos_marcasProveedor) > 0) {
			foreach ($datos_marcasProveedor as $valor) {
				$tabla .= '<tr bgcolor="#ffffff">';
				$nombre_marca = $valor->MARCC_Descripcion;
				$codigo = $valor->MARCP_Codigo;
				$registro = $valor->EMPMARP_Codigo;
				$codigo = "<input type='hidden' name='marcaCodigo[" . $item . "]' id='marcaCodigo[" . $item . "]' class='cajaMedia' value='" . $codigo . "'>";
				$editar = "&nbsp;";
				$eliminar = "<a href='#' onclick='eliminar_marca(" . $registro . ");'><img src='" . base_url() . "images/delete.gif' border='0'></a>";
				$tabla .= '<td>' . $item . '</td>';
				$tabla .= '<td>' . $nombre_marca . '</td>';
				$tabla .= '<td>' . $eliminar . '</td>';
				$tabla .= '<td>' . $editar . '</td>';
				$tabla .= '</tr>';
				$item++;
			}
		}
		$tabla .= '</table>';
		if (count($datos_marcasProveedor) == 0)
			$tabla .= '<div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;">NO EXISTEN REGISTROS</div>';

		if ($pinta == '1')
			echo $tabla;
		else
			return $tabla;
	}

	public function buscar_proveedores($j = '0')
	{
		$numdoc = $this->input->post('txtNumDoc');
		$nombre = $this->input->post('txtNombre');
		$telefono = $this->input->post('txtTelefono');
		$tipo = $this->input->post('cboTipoProveedor');
		$codtipoproveedor = $this->input->post('tipoCodigo');
		$nombre_tipoproveedor = $this->input->post('tipoNombre');
		$codmarca = $this->input->post('marcaCodigo');
		$nombre_marcaproveedor = $this->input->post('marcaNombre');
		$filter = new stdClass();
		$filter->numdoc = $numdoc;
		$filter->nombre = $nombre;
		$filter->telefono = $telefono;
		$filter->codtipoproveedor = $codtipoproveedor;
		$filter->codmarca = $codmarca;

		$data['numdoc'] = $numdoc;
		$data['nombre'] = $nombre;
		$data['telefono'] = $telefono;
		$data['tipo'] = $tipo;
		$data['codtipoproveedor'] = $codtipoproveedor;
		$data['nombre_tipoproveedor'] = $nombre_tipoproveedor;
		$data['codmarca'] = $codmarca;
		$data['nombre_marcaproveedor'] = $nombre_marcaproveedor;
		$data['titulo_tabla'] = "RESULTADO DE BÚSQUEDA DE PROVEEDORES";

		$data['registros'] = count($this->proveedor_model->buscar_proveedor($filter));
		$conf['base_url'] = site_url('empresa/proveedor/buscar_proveedores/');
		$data['action'] = base_url() . "index.php/empresa/proveedor/buscar_proveedores";
		$conf['total_rows'] = $data['registros'];
		$conf['per_page'] = 50;
		$conf['num_links'] = 3;
		$conf['next_link'] = "&gt;";
		$conf['prev_link'] = "&lt;";
		$conf['first_link'] = "&lt;&lt;";
		$conf['last_link'] = "&gt;&gt;";
		$conf['uri_segment'] = 4;
		$this->pagination->initialize($conf);
		$data['paginacion'] = $this->pagination->create_links();
		$listado_proveedores = $this->proveedor_model->buscar_proveedor($filter, $conf['per_page'], $j);
		$item = $j + 1;
		$lista = array();
		if (count($listado_proveedores) > 0) {
			foreach ($listado_proveedores as $indice => $valor) {
				$codigo = $valor->PROVP_Codigo;
				$ruc = $valor->ruc;
				$dni = $valor->dni;
				$razon_social = $valor->nombre;
				$direccion=$valor->direccion;
				$tipo_proveedor = $valor->PROVC_TipoPersona == 1 ? "P.JURIDICA" : "P.NATURAL";

				$editar = "<a href='#' onclick='editar_proveedor(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
				$ver = "<a href='#' onclick='ver_proveedor(" . $codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
				$eliminar = "<a href='#' onclick='eliminar_proveedor(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
				$lista[] = array($item, $ruc, $dni, $razon_social, 
					$tipo_proveedor,$direccion,
					$editar, $ver, $eliminar);
				$item++;
			}
		}
		$data['lista'] = $lista;
		$this->layout->view("empresa/proveedor_index", $data);

	}

	public function ventana_selecciona_proveedor($buscar)
	{
		if (is_numeric($buscar))
			$this->session->set_userdata(array('ruc' => $buscar, 'nombre' => ''));
		else
			$this->session->set_userdata(array('ruc' => '', 'nombre' => $buscar));

		$this->ventana_busqueda_proveedor();
	}

	public function obtener_nombre_proveedor($numdoc)
	{
		$datos_empresa = $this->empresa_model->obtener_datosEmpresa2($numdoc);
		$datos_persona = $this->persona_model->obtener_datosPersona2($numdoc);
		$resultado = '[{"PROVP_Codigo":"0","EMPRC_Ruc":"","EMPRC_RazonSocial":""}]';
		if (count($datos_empresa) > 0) {
			$empresa = $datos_empresa[0]->EMPRP_Codigo;
			$razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
			$datosProveedor = $this->proveedor_model->obtener_datosProveedor2($empresa);
			if (count($datosProveedor) > 0) {
				$proveedor = $datosProveedor[0]->PROVP_Codigo;
				$resultado = '[{"PROVP_Codigo":"' . $proveedor . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $razon_social . '"}]';
			}
		} elseif (count($datos_persona) > 0) {
			$persona = $datos_persona[0]->PERSP_Codigo;
			$nombres = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
			$datosProveedor = $this->proveedor_model->obtener_datosProveedor3($persona);
			if (count($datosProveedor) > 0) {
				$proveedor = $datosProveedor[0]->PROVP_Codigo;
				$resultado = '[{"PROVP_Codigo":"' . $proveedor . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $nombres . '"}]';
			}
		}
		echo $resultado;
	}

	public function obtener_proveedor($proveedor)
	{
		$datos_proveedor = $this->proveedor_model->obtener($proveedor);
		echo json_encode($datos_proveedor);
	}

	public function ver_proveedor($proveedor)
	{
		$datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
		$persona = $datos_proveedor[0]->PERSP_Codigo;
		$empresa = $datos_proveedor[0]->EMPRP_Codigo;
		$tipo_proveedor = $datos_proveedor[0]->PROVC_TipoPersona;
		if ($tipo_proveedor == 0) {
			$datos = $this->persona_model->obtener_datosPersona($persona);
			$tipo_doc = $datos[0]->PERSC_TipoDocIdentidad;
			$estado_civil = $datos[0]->ESTCP_EstadoCivil;
			$nacionalidad = $datos[0]->NACP_Nacionalidad;
			$nacimiento = $datos[0]->UBIGP_LugarNacimiento;
			$sexo = $datos[0]->PERSC_Sexo;
			$ubigeo_domicilio = $datos[0]->UBIGP_Domicilio;
			$datos_nacionalidad = $this->nacionalidad_model->obtener_nacionalidad($nacionalidad);
			$datos_nacimiento = $this->ubigeo_model->obtener_ubigeo($nacimiento);
			$datos_ubigeoDom_dpto = $this->ubigeo_model->obtener_ubigeo_dpto($ubigeo_domicilio);
			$datos_ubigeoDom_prov = $this->ubigeo_model->obtener_ubigeo_prov($ubigeo_domicilio);
			$datos_ubigeoDom_dist = $this->ubigeo_model->obtener_ubigeo($ubigeo_domicilio);
			$datos_doc = $this->tipodocumento_model->obtener_tipoDocumento($tipo_doc);
			$datos_estado_civil = $this->estadocivil_model->obtener_estadoCivil($estado_civil);
			$data['nacionalidad'] = $datos_nacionalidad[0]->NACC_Descripcion;
			$data['nacimiento'] = $datos_nacimiento[0]->UBIGC_Descripcion;
			$data['tipo_doc'] = $datos_doc[0]->TIPOCC_Inciales;
			$data['estado_civil'] = $datos_estado_civil[0]->ESTCC_Descripcion;
			$data['sexo'] = $sexo == 0 ? 'MASCULINO' : 'FEMENINO';
			$data['telefono'] = $datos[0]->PERSC_Telefono;
			$data['movil'] = $datos[0]->PERSC_Movil;
			$data['fax'] = $datos[0]->PERSC_Fax;
			$data['email'] = $datos[0]->PERSC_Email;
			$data['web'] = $datos[0]->PERSC_Web;
			$data['direccion'] = $datos[0]->PERSC_Direccion;
			$data['dpto'] = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
			$data['prov'] = $datos_ubigeoDom_prov[0]->UBIGC_Descripcion;
			$data['dist'] = $datos_ubigeoDom_dist[0]->UBIGC_Descripcion;
		} elseif ($tipo_proveedor == 1) {
			$datos = $this->empresa_model->obtener_datosEmpresa($empresa);
			$datos_sucurPrincipal = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
			$ubigeo_domicilio = $datos_sucurPrincipal[0]->UBIGP_Codigo;
			$datos_ubigeoDom_dpto = $this->ubigeo_model->obtener_ubigeo_dpto($ubigeo_domicilio);
			$data['dpto'] = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
			$data['prov'] = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
			$data['dist'] = $datos_ubigeoDom_dpto[0]->UBIGC_Descripcion;
			$data['direccion'] = $datos_sucurPrincipal[0]->EESTAC_Direccion;
			$data['telefono'] = $datos[0]->EMPRC_Telefono;
			$data['movil'] = $datos[0]->EMPRC_Movil;
			$data['fax'] = $datos[0]->EMPRC_Fax;
			$data['email'] = $datos[0]->EMPRC_Email;
			$data['web'] = $datos[0]->EMPRC_Web;
		}
		$data['datos'] = $datos;
		$data['titulo'] = "VER PROVEEDOR";
		$data['tipo'] = $tipo_proveedor;
		$this->load->view('empresa/proveedor_ver', $data);
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
				$datos_tipoEstab = $this->tipoestablecimiento_model->obtener_tipoEstablecimiento($tipo);
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

	public function listar_marcasEmpresa($empresa)
	{
		$listado_marcasEmpresa = $this->empresa_model->listar_marcasEmpresa($empresa);
		$resultado = array();
		if (count($listado_marcasEmpresa) > 0) {
			foreach ($listado_marcasEmpresa as $indice => $valor) {
				$objeto = new stdClass();
				$objeto->numero = $valor->EMPRP_Codigo;
				$objeto->registro = $valor->EMPMARP_Codigo;
				$objeto->nombre_marca = $valor->MARCC_Descripcion;
				$resultado[] = $objeto;
			}
		}
		return $resultado;
	}

	public function listar_tiposEmpresa($proveedor)
	{
		$empresa = $proveedor;
		$listado_tiposEmpresa = $this->empresa_model->listar_tiposEmpresa($proveedor);
		$resultado = array();
		if (count($listado_tiposEmpresa) > 0) {
			foreach ($listado_tiposEmpresa as $indice => $valor) {
				$objeto = new stdClass();
				$objeto->numero = $valor->FAMI_Codigo;
				$objeto->registro = $valor->EMPTIPOP_Codigo;
				$objeto->nombre_tipo = $valor->FAMI_Descripcion;
				$resultado[] = $objeto;
			}
		}
		return $resultado;
	}

	public function seleccionar_estadoCivil($indSel)
	{
		$array_dist = $this->estadocivil_model->listar_estadoCivil();
		$arreglo = array();
		foreach ($array_dist as $indice => $valor) {
			$indice1 = $valor->ESTCP_Codigo;
			$valor1 = $valor->ESTCC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo, $indSel, array('0', '::Seleccione::'));
		return $resultado;
	}

	public function seleccionar_nacionalidad($indSel = '')
	{
		$array_dist = $this->nacionalidad_model->listar_nacionalidad();
		$arreglo = array();
		foreach ($array_dist as $indice => $valor) {
			$indice1 = $valor->NACP_Codigo;
			$valor1 = $valor->NACC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo, $indSel, array('', '::Seleccione::'));
		return $resultado;
	}

	public function insertar_areaEmpresa($nombre_area){
		$this->empresa_model->insertar_areaEmpresa($area, $empresa, $descripcion);
	}

	public function seleccionar_departamento($indDefault = '')
	{
		$array_dpto = $this->ubigeo_model->listar_departamentos();
		$arreglo = array();
		if (count($array_dpto) > 0) {
			foreach ($array_dpto as $indice => $valor) {
				$indice1 = $valor->UBIGC_CodDpto;
				$valor1 = $valor->UBIGC_DescripcionDpto;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
		return $resultado;
	}

	public function seleccionar_provincia($departamento, $indDefault = '')
	{
		$array_prov = $this->ubigeo_model->listar_provincias($departamento);
		$arreglo = array();
		if (count($array_prov) > 0) {
			foreach ($array_prov as $indice => $valor) {
				$indice1 = substr($valor->UBIGC_CodProv,2,2);
				$valor1 = $valor->UBIGC_DescripcionProv;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
		return $resultado;
	}

	public function seleccionar_distritos($departamento, $provincia, $indDefault = '')
	{
		$array_dist = $this->ubigeo_model->listar_distritos($departamento, $provincia);
		$arreglo = array();
		if (count($array_dist) > 0) {
			foreach ($array_dist as $indice => $valor) {
				$indice1 = substr($valor->UBIGC_CodDist,4,2);
				$valor1 = $valor->UBIGC_Descripcion;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
		return $resultado;
	}

	public function seleccionar_tipocodigo($indDefault = '')
	{
		$array_dist = $this->tipocodigo_model->listar_tipo_codigo();
		$arreglo = array();
		if (count($array_dist) > 0) {
			foreach ($array_dist as $indice => $valor) {
				$indice1 = $valor->TIPCOD_Codigo;
				$valor1 = $valor->TIPCOD_Inciales;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('0', '::Seleccione::'));
		return $resultado;
	}

	public function seleccionar_tipodocumento($indDefault = '')
	{
		$array_dist = $this->tipodocumento_model->listar_tipo_documento();
		$arreglo = array();
		if (count($array_dist) > 0) {
			foreach ($array_dist as $indice => $valor) {
				$indice1 = $valor->TIPDOCP_Codigo;
				$valor1 = $valor->TIPOCC_Inciales;
				$arreglo[$indice1] = $valor1;
			}
		}
		$resultado = $this->html->optionHTML($arreglo, $indDefault, array('0', '::Seleccione::'));
		return $resultado;
	}

	public function JSON_listar_sucursalesEmpresa($proveedor = '')
	{
		$listado_sucursalesEmpresa = array();
		if ($proveedor != '') {
			$datos_proveedor = $this->proveedor_model->obtener($proveedor);
			$empresa = $datos_proveedor->empresa;
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
				$filter->EESTAC_Direccion = $datos_proveedor->direccion;
				$filter->UBIGP_Codigo = $datos_proveedor->ubigeo;
				$filter->departamento = $datos_proveedor->departamento;
				$filter->provincia = $datos_proveedor->provincia;
				$filter->distrito = $datos_proveedor->distrito;
				$listado_sucursalesEmpresa = array($filter);
			}
		}

		$result[] = array('Tipo' => '1', 'Titulo' => 'LOS ESTABLECIMIENTOS DE MI PROVEEDOR');
		foreach ($listado_sucursalesEmpresa as $reg)
			$result[] = array('Tipo' => '2', 'EESTAC_Direccion' => $reg->EESTAC_Direccion, 'UBIGP_Codigo' => $reg->UBIGP_Codigo, 'departamento' => $reg->departamento, 'provincia' => $reg->provincia, 'distrito' => $reg->distrito);

		echo json_encode($result);
	}

	public function autocomplete(){
		$keyword = $this->input->post('term');
		$compania = $this->compania;
		$consulta = $this->proveedor_model->buscarProveedor($keyword, $compania);
		$result = array();
		$contactos = array();

		if($consulta != NULL){
			foreach ($consulta as $key => $value) {
				$tipoPersona = $value->CLIC_TipoPersona;
				if ( $tipoPersona== '0') {
					$nombre = $value->PERSC_Nombre . ' ' .$value->PERSC_ApellidoPaterno;
					$ruc = $value->PERSC_Ruc;
					$ruc = ($ruc == NULL || $ruc == 0) ? $value->PERSC_NumeroDocIdentidad : $ruc;
					$codigoEmpresa = $value->PERSP_Codigo;
					$direccion="";
				} else {
					$nombre =$value->EMPRC_RazonSocial;
					$ruc = $value->EMPRC_Ruc;
					$codigoEmpresa = $value->EMPRP_Codigo;
					$contactos = $this->empresa_model->listar_contactosEmpresa($codigoEmpresa);
					$direccion = $value->EMPRC_Direccion;
				}
				$result[] = array("value" => $nombre, "label" => "$ruc - $nombre", "nombre" => $nombre, "codigo" => $value->PROVP_Codigo, "ruc" => $ruc, "tipoPersona" => $tipoPersona, "codigoEmpresa" => $codigoEmpresa, "contactos" =>  $contactos, "direccion" => $direccion);
			}
		}
		echo json_encode($result);
	}

	public function autocomplete_ruc(){
		$keyword = $this->input->post('term');
		$compania = $this->compania;
		$consulta = $this->proveedor_model->buscarProveedorRuc($keyword, $compania);
		$result = array();
		$contactos = array();

		if ($consulta != NULL) {
			foreach ($consulta AS $cliente => $value) {
				$tipoPersona = $value->PROVC_TipoPersona;
				if ($tipoPersona== '0') {
					$nombre = $value->PERSC_Nombre;
					$ruc = $value->PERSC_NumeroDocIdentidad;
					$codigoEmpresa = $value->PERSP_Codigo;
				}
				else {
					$nombre = $value->EMPRC_RazonSocial;
					$ruc = $value->EMPRC_Ruc;
					$codigoEmpresa = $value->EMPRP_Codigo;
					$contactos = $this->empresa_model->listar_contactosEmpresa($codigoEmpresa);
				}
				$result[] = array("value" => $ruc, "label" => "$ruc - $nombre", "nombre" => $nombre, "codigo" => $value->PROVP_Codigo, "ruc" => $ruc, "tipoPersona" => $tipoPersona, "codigoEmpresa" => $codigoEmpresa, "contactos" =>  $contactos);
			}
		}
		echo json_encode($result);
	}

	function JSON_buscar_proveedor($numdoc){
		$datos_empresa = $this->empresa_model->obtener_datosEmpresa2($numdoc);
		$datos_persona = $this->persona_model->obtener_datosPersona2($numdoc);
		$resultado = '[{"PROVP_Codigo":"0","EMPRC_Ruc":"","EMPRC_RazonSocial":""}]';
		if (count($datos_empresa) > 0) {
			$empresa = $datos_empresa[0]->EMPRP_Codigo;
			$razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
			$datosCliente = $this->proveedor_model->obtener_datosCliente2($empresa);
			if (count($datosCliente) > 0) {
				$cliente = $datosCliente[0]->PROVP_Codigo;
				$resultado = '[{"PROVP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $razon_social . '"}]';
			}
		} elseif (count($datos_persona) > 0) {
			$persona = $datos_persona[0]->PERSP_Codigo;
			$nombres = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
			$datosCliente = $this->proveedor_model->obtener_datosCliente3($persona);
			if (count($datosCliente) > 0) {
				$cliente = $datosCliente[0]->PROVP_Codigo;
				$resultado = '[{"PROVP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $nombres . '"}]';
			}
		}
		echo $resultado;
	}

	public function registro_proveedor_pdf($telefono, $docum, $nombre)
	{

		$this->load->library('cezpdf');
		$this->load->helper('pdf_helper');
        //prep_pdf();
		$this->cezpdf = new Cezpdf('a4');
		$datacreator = array(
			'Title' => 'Estadillo de ',
			'Name' => 'Estadillo de ',
			'Author' => 'Vicente Producciones',
			'Subject' => 'PDF con Tablas',
			'Creator' => 'info@vicenteproducciones.com',
			'Producer' => 'http://www.vicenteproducciones.com'
		);

		$this->cezpdf->addInfo($datacreator);
		$this->cezpdf->selectFont(APPPATH . 'libraries/fonts/Helvetica.afm');
		$delta = 20;

		$this->cezpdf->ezText('<b>RELACION DE PROVEEDORES</b>', 14, array("leading" => 0, 'left' => 185));
		$this->cezpdf->ezText('', '', array("leading" => 10));

		$db_data = array();

		$listado_proveedor = $this->proveedor_model->listar_proveedor_pdf($telefono, $docum, $nombre);

		if (count($listado_proveedor) > 0) {
			foreach ($listado_proveedor as $indice => $valor) {
				$ruc = $valor->ruc;
				$dni = $valor->dni;
				$razon_social = $valor->nombre;
				$direccion=$valor->direccion;
				$tipo_proveedor = $valor->PROVC_TipoPersona == 1 ? "P.JURIDICA" : "P.NATURAL";

				$db_data[] = array(
					'cols1' => $indice + 1,
					'cols2' => $ruc,
					'cols3' => $dni,
					'cols4' => $razon_social,
					'cols5' => $tipo_proveedor,
					'cols6' => $direccion
				);
			}
		}

		$col_names = array(
			'cols1' => '<b>ITEM</b>',
			'cols2' => '<b>RUC</b>',
			'cols3' => '<b>DNI</b>',
			'cols4' => '<b>NOMBRE O RAZÓN SOCIAL</b>',
			'cols5' => '<b>T. P.</b>',
			'cols6' => '<b>DIRECCION</b>'
		);

		$this->cezpdf->ezTable($db_data, $col_names, '', array(
			'width' => 525,
			'showLines' => 1,
			'shaded' => 1,
			'showHeadings' => 1,
			'xPos' => 'center',
			'fontSize' => 8,
			'cols' => array(
				'cols1' => array('width' => 30, 'justification' => 'center'),
				'cols2' => array('width' => 70, 'justification' => 'center'),
				'cols3' => array('width' => 50, 'justification' => 'center'),
				'cols4' => array('width' => 150, 'justification' => 'center'),
				'cols5' => array('width' => 70, 'justification' => 'center'),
				'cols6' => array('width' => 100, 'justification' => 'center')
			)
		));


		$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');

		ob_end_clean();

		$this->cezpdf->ezStream($cabecera);
	}
}

?>