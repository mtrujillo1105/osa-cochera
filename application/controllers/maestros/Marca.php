<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Marca extends CI_Controller{

  ##  -> Begin
	private $url;
	private $view_js = NULL;
  ##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/marca_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/marca.js");
	}

	public function index(){
		$data['titulo'] = "MARCAS";
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/marca_index', $data);
	}

	/** En las db anteriores el metodo en el menu es listar **/
	public function listar(){
		$this->index();
	}

	public function datatable_marca(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "MARCC_CodigoUsuario",
			++$posDT => "MARCC_Descripcion"
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

		$marcaInfo = $this->marca_model->getMarcas($filter, false);
		$records = array();

		if ($marcaInfo["records"] != 0) {
			foreach ($marcaInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->MARCP_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1l'>
											</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->MARCP_Codigo)' class='btn btn-default'>
													<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1l'>
												</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->MARCC_CodigoUsuario,
					++$posDT => $valor->MARCC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $marcaInfo["recordsTotal"] != NULL ) ? $marcaInfo["recordsTotal"] : 0;
		$recordsFilter = $marcaInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getMarca(){

		$marca = $this->input->post("marca");

		$marcaInfo = $this->marca_model->getMarca($marca);
		$lista = array();

		if ( $marcaInfo != NULL ){
			foreach ($marcaInfo as $indice => $val) {
				$lista = array(
					"marca" => $val->MARCP_Codigo,
					"codigo" => $val->MARCC_CodigoUsuario,
					"descripcion" => $val->MARCC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$marca = $this->input->post("marca");
		#$codigo_marca = $this->input->post("codigo_marca");
		$descripcion_marca = $this->input->post("descripcion_marca");

		$filter = new stdClass();
		$filter->MARCC_Descripcion = strtoupper($descripcion_marca);
		$filter->MARCC_FlagEstado = "1";

		if ($marca != ""){
			$filter->MARCC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->marca_model->actualizar_marca($marca, $filter);
		}
		else{
			$filter->MARCC_CodigoUsuario = $this->getNewCodigo();
			$filter->MARCC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->marca_model->insertar_marca($filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_marca(){

		$marca = $this->input->post("marca");

		$filter = new stdClass();
		$filter->MARCC_FlagEstado  = "0";

		if ($marca != ""){
			$filter->MARCC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->marca_model->deshabilitar_marca($marca, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	private function getNewCodigo(){
		$this->load->library('lib_props');
		$lastID = $this->marca_model->getLastId();
		return 'MR-'.$this->lib_props->getNumberFormat($lastID, 3);
	}
}
?>