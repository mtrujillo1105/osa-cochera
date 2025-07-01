<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Tipocliente extends CI_Controller{

	private $empresa;
	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/tipocliente_model');
		$this->compania = $this->session->userdata('compania');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/tipocliente.js");
	}

	public function index() {
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/tipocliente_index', $data);
	}

	public function datatable_categoria(){

		$posDT = -1;
		$columnas = array(
			++$posDT => "TIPCLIC_Descripcion"
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

		$categoriaInfo = $this->tipocliente_model->getCategorias($filter, false);
		$records = array();

		if ( $categoriaInfo["records"] != NULL ) {
			foreach ($categoriaInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->TIPCLIP_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
											</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->TIPCLIP_Codigo)' class='btn btn-default'>
													<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
												</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->TIPCLIC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $categoriaInfo["recordsTotal"] != NULL ) ? $categoriaInfo["recordsTotal"] : 0;
		$recordsFilter = $categoriaInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getCategoria(){

		$categoria = $this->input->post("categoria");

		$categoriaInfo = $this->tipocliente_model->getCategoria($categoria);
		$lista = array();

		if ( $categoriaInfo != NULL ){
			foreach ($categoriaInfo as $indice => $val) {
				$lista = array(
					"categoria" => $val->TIPCLIP_Codigo,
					"descripcion" => $val->TIPCLIC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$categoria = $this->input->post("categoria");
		$descripcion = $this->input->post("descripcion_categoria");

		$filter = new stdClass();
		$filter->TIPCLIC_Descripcion = strtoupper($descripcion);
		$filter->TIPCLIC_FlagEstado = "1";

		if ($categoria != ""){
			$filter->TIPCLIP_Codigo = $categoria;
			$filter->TIPCLIC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->tipocliente_model->actualizar_categoria($categoria, $filter);
		}
		else{
			$filter->COMPP_Codigo = $this->compania;
			$filter->TIPCLIC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->tipocliente_model->insertar_categoria($filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_categoria(){

		$categoria = $this->input->post("categoria");

		$filter = new stdClass();
		$filter->TIPCLIC_FlagEstado  = "0";

		if ($categoria != ""){
			$filter->TIPCLIC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->tipocliente_model->deshabilitar_categoria($categoria, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
?>