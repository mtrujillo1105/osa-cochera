<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Documento extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/configuracion_model');
		$this->load->model('maestros/documento_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/documento.js");
	}

	public function index(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/documento_index', $data);
	}

	public function listar(){
		$this->index();
	}

	public function datatable_documento(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "DOCUC_Descripcion",
			++$posDT => "DOCUC_Inicial",
			++$posDT => "estado",
			++$posDT => "DOCUC_Abreviacion"
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

		$documentoInfo = $this->documento_model->getDocumentos($filter);
		$records = array();

		if ($documentoInfo["records"] != 0) {
			foreach ($documentoInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->DOCUP_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1l'>
											</button>";

				$btn_enabled = "<button type='button' onclick='habilitar($valor->DOCUP_Codigo)' class='btn btn-default'>
													<img src='".$this->url."/public/images/icons/documento-add.png' class='image-size-1l'>
												</button>";

				$btn_disabled = "<button type='button' onclick='deshabilitar($valor->DOCUP_Codigo)' class='btn btn-default'>
													<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1l'>
												</button>";

				$color = ($valor->DOCUC_FlagEstado == "1") ? "color-green" : "color-red";
				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->DOCUC_Descripcion,
					++$posDT => $valor->DOCUC_Inicial,
					++$posDT => "<span class='$color'>$valor->estado</span>",
					++$posDT => $valor->DOCUC_Abreviacion,
					++$posDT => $btn_modal,
					++$posDT => ($valor->DOCUC_FlagEstado == "1") ? $btn_disabled : $btn_enabled
				);
			}
		}

		$recordsTotal = ( $documentoInfo["recordsTotal"] != NULL ) ? $documentoInfo["recordsTotal"] : 0;
		$recordsFilter = $documentoInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getDocumento(){

		$documento = $this->input->post("documento");
		$docInfo = $this->documento_model->getDocumento($documento);
		$lista = array();

		if ( $docInfo != NULL ){
				$lista = array(
					"documento" => $docInfo->DOCUP_Codigo,
					"descripcion" => $docInfo->DOCUC_Descripcion,
					"inicial" => $docInfo->DOCUC_Inicial,
					"estado" => $docInfo->DOCUC_FlagEstado,
					"abreviacion" => $docInfo->DOCUC_Abreviacion
				);

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){
		$documento = $this->input->post("documento");
		$descripcion = strtoupper(trim($this->input->post("descripcion_documento")));
		$inicial = strtoupper(trim($this->input->post("inicial_documento")));
		$abreviacion = strtoupper(trim($this->input->post("abreviacion_documento")));
		$estado = $this->input->post("estado_documento");

		$filter = new stdClass();
		$filter->DOCUC_Descripcion = $descripcion;
		$filter->DOCUC_Inicial = $inicial;
		$filter->DOCUC_Abreviacion = $abreviacion;
		$filter->DOCUC_FlagEstado = $estado;

		if ($documento != ""){
			$result = $this->documento_model->actualizar_documento($documento, $filter);
			
			$filterSeries = new stdClass();
			$filterSeries->CONFIC_FlagEstado = $estado;
			$this->configuracion_model->actualizar_estado_series($documento, $filterSeries);
		}
		else{
			$result = $this->documento_model->insertar_documento($filter);

			if ($result){
				$filterSeries = new stdClass();
				$filterSeries->DOCUP_Codigo = $result;
				$filterSeries->CONFIC_Serie = $inicial;
				$filterSeries->CONFIC_Numero = 0;
				$filterSeries->CONFIC_FechaRegistro = date("Y-m-d h:i:s");
				$filterSeries->CONFIC_FlagEstado = $estado;
				$this->configuracion_model->insertar_serie($filterSeries);
			}
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
		die();
	}

	public function deshabilitar_documento(){

		$documento = $this->input->post("documento");

		$filter = new stdClass();
		$filter->DOCUC_FlagEstado = "0";

		if ($documento != ""){
			$result = $this->documento_model->actualizar_documento($documento, $filter);

			$filterSeries = new stdClass();
			$filterSeries->CONFIC_FlagEstado = "0";
			$this->configuracion_model->actualizar_estado_series($documento, $filterSeries);
		}
		else
			$result = false;

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
		die();
	}

	public function habilitar_documento(){

		$documento = $this->input->post("documento");

		$filter = new stdClass();
		$filter->DOCUC_FlagEstado = "1";

		if ($documento != ""){
			$result = $this->documento_model->actualizar_documento($documento, $filter);

			$filterSeries = new stdClass();
			$filterSeries->CONFIC_FlagEstado = "1";
			$this->configuracion_model->actualizar_estado_series($documento, $filterSeries);
		}
		else
			$result = false;

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
		die();
	}
}
?>