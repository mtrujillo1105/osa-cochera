<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Unidadmedida extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/unidadmedida_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/unidadmedida.js");
	}

	public function listar(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/unidadmedida_index', $data);
	}

	public function datatable_um(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "UNDMED_Simbolo",
			++$posDT => "UNDMED_Descripcion"
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

		$filter->descripcion = $this->input->post('descripcion');

		$umInfo = $this->unidadmedida_model->getUmedidas($filter, false);
		$records = array();

		if ( $umInfo["records"] != NULL ) {
			foreach ($umInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->UNDMED_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->UNDMED_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->UNDMED_Simbolo,
					++$posDT => $valor->UNDMED_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $umInfo["recordsTotal"] != NULL ) ? $umInfo["recordsTotal"] : 0;
		$recordsFilter = $umInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getUnidad(){

		$um = $this->input->post("um");

		$umInfo = $this->unidadmedida_model->getUmedida($um);
		$lista = array();

		if ( $umInfo != NULL ){
			foreach ($umInfo as $indice => $val) {
				$lista = array(
					"um" => $val->UNDMED_Codigo,
					"simbolo" => $val->UNDMED_Simbolo,
					"descripcion" => $val->UNDMED_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$um = $this->input->post("um");
		$descripcion_um = $this->input->post("descripcion_um");
		$simbolo_um = $this->input->post("simbolo_um");

		$filter = new stdClass();
		$filter->UNDMED_Descripcion = strtoupper($descripcion_um);
		$filter->UNDMED_Simbolo = $simbolo_um;
		$filter->UNDMED_FlagEstado = "1";

		if ($um != ""){
			$filter->UNDMED_Codigo = $um;
			$filter->UNDMED_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->unidadmedida_model->actualizar_unidad($um, $filter);
		}
		else{
			$filter->UNDMED_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->unidadmedida_model->insertar_unidad($filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_um(){

		$um = $this->input->post("um");

		$filter = new stdClass();
		$filter->UNDMED_FlagEstado  = "0";

		if ($um != ""){
			$filter->UNDMED_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->unidadmedida_model->deshabilitar_unidad($um, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
?>