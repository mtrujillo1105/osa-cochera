<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Cargo extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/cargo_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/cargo.js");
	}

	public function index(){
		$this->cargos();
	}

	public function cargos( $j = "" ){
		echo "<BR>";
		print_r($_SESSION['empresa']);		
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/cargo_index',$data);
	}

	public function datatable_cargo(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "",
			++$posDT => "CARGC_Nombre",
			++$posDT => "CARGC_Descripcion",
			++$posDT => "CARGC_FlagEstado"
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

		$filter->nombre = $this->input->post('nombre');

		$cargoInfo = $this->cargo_model->getCargos($filter, false);
		$records = array();

		if ( $cargoInfo["records"] != NULL ) {
			foreach ($cargoInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->CARGP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";
				$btn_borrar = "<button type='button' onclick='deshabilitar($valor->CARGP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $indice + 1,
					++$posDT => $valor->CARGC_Nombre,
					++$posDT => $valor->CARGC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_borrar
				);
			}
		}

		$recordsTotal = ( $cargoInfo["recordsTotal"] != NULL ) ? $cargoInfo["recordsTotal"] : 0;
		$recordsFilter = $cargoInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getCargo(){

		$codigo = $this->input->post("cargo");

		$cargoInfo = $this->cargo_model->getCargo($codigo);
		$lista = array();

		if ( $cargoInfo != NULL ){
			foreach ($cargoInfo as $indice => $val) {
				$lista = array(
					"cargo" => $val->CARGP_Codigo,
					"nombre" => $val->CARGC_Nombre,
					"descripcion" => $val->CARGC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$cargo = $this->input->post("cargo");
		$nombre = strtoupper( $this->input->post("cargo_nombre") );
		$descripcion = strtoupper( $this->input->post("cargo_descripcion") );

		$filter = new stdClass();
		$filter->CARGC_Nombre      = strtoupper($nombre);
		$filter->CARGC_Descripcion = strtoupper($descripcion);
		$filter->CARGC_FlagEstado  = "1";

		if ($cargo != ""){
			$filter->CARGP_Codigo = $cargo;
			$result = $this->cargo_model->actualizar($cargo, $filter);
		}
		else
			$result = $this->cargo_model->insertar($filter);

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_cargo(){

		$cargo = $this->input->post("cargo");

		$filter = new stdClass();
		$filter->CARGC_FlagEstado  = "0";

		if ($cargo != "")
			$result = $this->cargo_model->actualizar($cargo, $filter);

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}	

}
?>