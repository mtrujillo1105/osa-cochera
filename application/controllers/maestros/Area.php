<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Area extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/area_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/area.js");
	}

	public function index(){
		$this->areas();
	}

	public function areas(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/area_index', $data);
	}

	public function datatable_area(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "AREAC_Descripcion"
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

		$areaInfo = $this->area_model->getAreas($filter);
		$records = array();

		if ( $areaInfo["records"] > 0) {
			foreach ($areaInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->AREAP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->AREAP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->AREAC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $areaInfo["recordsTotal"] != NULL ) ? $areaInfo["recordsTotal"] : 0;
		$recordsFilter = $areaInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getArea(){

		$area = $this->input->post("area");

		$areaInfo = $this->area_model->getArea($area);
		$lista = array();

		if ( $areaInfo != NULL ){
			foreach ($areaInfo as $indice => $val) {
				$lista = array(
					"area" => $val->AREAP_Codigo,
					"descripcion" => $val->AREAC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$area = $this->input->post("area");
		$descripcion_area = $this->input->post("descripcion_area");

		$filter = new stdClass();
		$filter->AREAC_Descripcion = strtoupper($descripcion_area);
		$filter->AREAC_FlagEstado = "1";

		if ($area != ""){
			$filter->AREAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->area_model->actualizar_area($area, $filter);
			if ($result){
				$json_result = "success";
				$json_message = "Actualización exitosa.";
			}
			else{
				$json_result = "error";
				$json_message = "Error al actualizar la información, intentelo nuevamente.";
			}
		}
		else{
				$filter->AREAC_FechaRegistro = date("Y-m-d H:i:s");
				$result = $this->area_model->insertar_area($filter);
				if ($result){
					$json_result = "success";
					$json_message = "Registro exitoso.";
				}
				else{
					$json_result = "error";
					$json_message = "Error al guardar el registro, intentelo nuevamente.";
				}
		}

		$json = array("result" => $json_result, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	public function deshabilitar_area(){

		$area = $this->input->post("area");

		$filter = new stdClass();
		$filter->AREAC_FlagEstado  = "0";

		if ($area != ""){
			$filter->AREAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->area_model->deshabilitar_area($area, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
?>