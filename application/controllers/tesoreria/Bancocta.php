<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Bancocta extends CI_Controller{

	##  -> Begin - El array somevar es reemplazado por atributos
	private $empresa;
	private $compania;
	private $url;
	##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->load->model('tesoreria/bancocta_model');

		$this->empresa = $this->session->userdata("empresa");
		$this->compania = $this->session->userdata("compania");
		$this->url = base_url();
	}
	##  -> End

	##  -> Begin
	public function datatable_ctaEmpresa(){

		$columnas = array(
			0 => "BANC_Nombre",
			1 => "CUENT_Titular",
			2 => "CUENT_TipoCuenta",
			3 => "MONED_Descripcion"
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

		$filter->empresa = $this->input->post('empresa');
		$filter->persona = $this->input->post('persona');
		$filter->banco = $this->input->post('banco');
		$filter->moneda = $this->input->post('moneda');
		$filter->tipo_cuenta = $this->input->post('tipo_cuenta');

		$ctaBancariaInfo = $this->bancocta_model->getCtasEmpresa($filter);

		$records = array();
		if ( $ctaBancariaInfo["records"] != NULL) {
			foreach ($ctaBancariaInfo["records"] as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar_CtaBancaria($valor->CUENT_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_deshabilitar = "<button type='button' onclick='deshabilitar_CtaBancaria($valor->CUENT_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$records[] = array(
					0 => $valor->BANC_Nombre,
					1 => $valor->CUENT_Titular,
					2 => $valor->tipo_cuenta,
					3 => $valor->MONED_Descripcion,
					4 => $valor->CUENT_NumeroEmpresa,
					5 => $valor->CUENT_Interbancaria,
					6 => $btn_editar,
					7 => $btn_deshabilitar
				);
			}
		}

		$recordsTotal = ( $ctaBancariaInfo["recordsTotal"] != NULL ) ? $ctaBancariaInfo["recordsTotal"] : 0;
		$recordsFilter = $ctaBancariaInfo["recordsFilter"];

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
	public function getCtaBancaria(){
		$cta = $this->input->post("cta_bancaria");
		$ctaBancariaInfo = $this->bancocta_model->getCtaEmpresa($cta);

		if ($ctaBancariaInfo != NULL){
			foreach ($ctaBancariaInfo as $key => $val)
				$info = array(
					"cta_bancaria" => $val->CUENT_Codigo,
					"empresa" => $val->EMPRE_Codigo,
					"persona" => $val->PERSP_Codigo,
					"banco" => $val->BANP_Codigo,
					"titular" => $val->CUENT_Titular,
					"cta_numero" => $val->CUENT_NumeroEmpresa,
					"cta_interbancaria" => $val->CUENT_Interbancaria,
					"tipo_cuenta" => $val->CUENT_TipoCuenta,
					"moneda" => $val->MONED_Codigo
				);


			$json = array("match" => true, "info" => $info);
		}
		else
			$json = array("match" => true, "info" => NULL);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function guardar_ctabancaria(){

		$cta = $this->input->post("cta_bancaria");
		$empresa = $this->input->post("cta_bancaria_empresa");
		$persona = $this->input->post("cta_bancaria_persona");
		$banco = $this->input->post("banco");
		$titular = $this->input->post("cta_bancaria_titular");
		$tipo_cta = $this->input->post("cta_bancaria_tipo");
		$moneda = $this->input->post("cta_bancaria_moneda");
		$cta_numero = $this->input->post("cta_bancaria_numero");
		$cta_interbancaria = $this->input->post("cta_bancaria_interbancaria");

		$filter = new stdClass();
		$filter->EMPRE_Codigo = $empresa;
		$filter->PERSP_Codigo = $persona;
		$filter->BANP_Codigo = $banco;
		$filter->CUENT_Titular = $titular;
		$filter->CUENT_NumeroEmpresa = $cta_numero;
		$filter->CUENT_Interbancaria = $cta_interbancaria;
		$filter->CUENT_TipoCuenta = $tipo_cta;
		$filter->MONED_Codigo = $moneda;
		$filter->CUENT_TipoPersona = ($empresa != "0" && $empresa != "") ? 1 : 0;
		$filter->CUENT_Oficina = "";
		$filter->CUENT_Sectoriza = "";
		$filter->CUENT_UsuarioRegistro = $this->session->userdata("user");
		$filter->CUENT_FlagEstado = "1";

		if ($cta != ""){
			$filter->CUENT_FechaModificacion = date("Y-m-d H:i:s");
			$cta = $this->bancocta_model->actualizar_CtaBancaria($cta, $filter);    
		}
		else{
			if ($empresa != "" || $persona != ""){
				$filter->CUENT_FechaRegistro = date("Y-m-d H:i:s");
				$cta = $this->bancocta_model->insertar_CtaBancaria($filter);
			}
			else
				$cta = false;

		}

		if ($cta)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_ctabancaria(){

		$cta = $this->input->post("cta_bancaria");

		$filter = new stdClass();
		$filter->CUENT_FlagEstado = "0";
		$filter->CUENT_FechaModificacion = date("Y-m-d H:i:s");

		if ($cta != "")
			$cta = $this->bancocta_model->actualizar_CtaBancaria($cta, $filter);

		if ($cta)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

  ## FUNCTIONS OLDS

	public function JSON_listar($banco){
		$lista_cta=$this->bancocta_model->listar($banco);
		echo json_encode($lista_cta);
	}

}
?>