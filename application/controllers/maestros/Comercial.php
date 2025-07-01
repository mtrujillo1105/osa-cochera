<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Comercial extends CI_Controller{

  ##  -> Begin
	private $url;
	private $view_js = NULL;
  ##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/comercial_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/comercial.js");
	}

	public function index(){
		$data['titulo'] = "COMERCIALES";
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/comercial_index', $data);
	}

	/** En las db anteriores el metodo en el menu es listar **/
	public function listar(){
		$this->index();
	}

	public function datatable_comercial(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "SECCOMC_Descripcion"
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

		$comercialInfo = $this->comercial_model->getComercials($filter, false);
		$records = array();

		if ($comercialInfo["records"] != 0) {
			foreach ($comercialInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->SECCOMP_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1l'>
											</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->SECCOMP_Codigo)' class='btn btn-default'>
													<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1l'>
												</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->SECCOMC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $comercialInfo["recordsTotal"] != NULL ) ? $comercialInfo["recordsTotal"] : 0;
		$recordsFilter = $comercialInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getComercial(){

		$comercial = $this->input->post("comercial");

		$comercialInfo = $this->comercial_model->getComercial($comercial);
		$lista = array();

		if ( $comercialInfo != NULL ){
			foreach ($comercialInfo as $indice => $val) {
				$lista = array(
					"comercial" => $val->SECCOMP_Codigo,
					"descripcion" => $val->SECCOMC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$comercial = $this->input->post("comercial");
		$descripcion_comercial = $this->input->post("descripcion_comercial");

		$filter = new stdClass();
		$filter->SECCOMC_Descripcion = strtoupper($descripcion_comercial);
		$filter->SECCOMC_FlagEstado = "1";

		if ($comercial != ""){
			$filter->SECCOMC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->comercial_model->actualizar_comercial($comercial, $filter);
		}
		else{
			$filter->SECCOMC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->comercial_model->insertar_comercial($filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_comercial(){

		$comercial = $this->input->post("comercial");

		$filter = new stdClass();
		$filter->SECCOMC_FlagEstado  = "0";

		if ($comercial != ""){
			$filter->SECCOMC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->comercial_model->deshabilitar_comercial($comercial, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
?>