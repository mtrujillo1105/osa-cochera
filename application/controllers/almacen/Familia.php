<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Familia extends CI_Controller
{

	private $compania;
	private $url;
	private $view_js = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('almacen/familia_model');
		$this->compania = $this->session->userdata("compania");
		$this->url = base_url();
		$this->view_js = array(0 => "almacen/familia.js");
	}

	public function index()
	{
		$data['scripts'] = $this->view_js;
		$this->layout->view('almacen/familia_index', $data);
	}

	public function datatable_familia()
	{
		$posDT = -1;
		$columnas = array(
			++$posDT => "FAMI_FlagBienServicio",
			++$posDT => "FAMI_CodigoUsuario",
			++$posDT => "FAMI_Descripcion"
		);

		$filter = new stdClass();
		$filter->start = $this->input->post("start");
		$filter->length = $this->input->post("length");
		$filter->search = $this->input->post("search")["value"];

		$ordenar = $this->input->post("order")[0]["column"];
		if ($ordenar != "") {
			$filter->order = $columnas[$ordenar];
			$filter->dir = $this->input->post("order")[0]["dir"];
		}

		$item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;

		$filter->flagBS = $this->input->post('tipo');
		$filter->codigo = $this->input->post('codigo');
		$filter->descripcion = $this->input->post('descripcion');

		$familiaInfo = $this->familia_model->getFamilias($filter, false);
		$records = array();

		if ($familiaInfo["records"] != NULL) {
			foreach ($familiaInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->FAMI_Codigo)' class='btn btn-default'>
				<img src='" . $this->url . "/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_disabled = "<button type='button' onclick='deshabilitar($valor->FAMI_Codigo)' class='btn btn-default'>
					<img src='" . $this->url . "/public/images/icons/documento-delete.png' class='image-size-1b'>
					</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->flagBS,
					++$posDT => $valor->FAMI_CodigoUsuario,
					++$posDT => $valor->FAMI_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_disabled
				);
			}
		}

		$recordsTotal = ($familiaInfo["recordsTotal"] != NULL) ? $familiaInfo["recordsTotal"] : 0;
		$recordsFilter = $familiaInfo["recordsFilter"];

		$json = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getFamilia()
	{
		$codigo = $this->input->post("familia");
		$familiaInfo = $this->familia_model->getFamilia($codigo);
		$lista = array();

		if ($familiaInfo != NULL) {
			$lista = array(
				"id" => $familiaInfo->FAMI_Codigo,
				"codigo" => $familiaInfo->FAMI_CodigoUsuario,
				"descripcion" => $familiaInfo->FAMI_Descripcion,
				"flagBS" => $familiaInfo->FAMI_FlagBienServicio,
				"estado" => $familiaInfo->FAMI_FlagEstado
			);
			$json = array("match" => true, "info" => $lista);
		} else
			$json = array("match" => false, "info" => "");

		die(json_encode($json));
	}

	public function guardar_registro()
	{
		$execute = true;
		$familia = $this->input->post("familia");
		$tipo = $this->input->post('tipoFamilia');
		$codigo = $this->input->post('codigoFamilia');
		$descripcion = trim($this->input->post('descripcionFamilia'));

		if ($tipo == '') {
			$json_result = 'error';
			$json_message = 'Debe seleccionar el tipo de familia.';
			$execute = false;
		}

		if ($descripcion == '') {
			$json_result = 'error';
			$json_message = 'Debe agregar una descripción valida.';
			$execute = false;
		}

		if ($execute == true) {
			$filter = new stdClass();
			$filter->FAMI_FlagBienServicio = $tipo;
			$filter->FAMI_CodigoUsuario = $codigo;
			$filter->FAMI_Descripcion = $descripcion;
			$filter->FAMI_FlagEstado = '1';

			if ($familia != "") {
				$filter->FAMI_FechaModificacion = date("Y-m-d H:i:s");
				$result = $this->familia_model->actualizar($familia, $filter);
				if ($result) {
					$json_result = 'success';
					$json_message = 'Actualizacion completa.';
				} else {
					$json_result = 'error';
					$json_message = 'Error al actualizar los datos.';
				}
			} else {
				$filter->FAMI_FechaRegistro = date("Y-m-d H:i:s");
				$id = $this->familia_model->insertar($filter);
				if ($id) {
					$json_result = 'success';
					$json_message = 'Registro completado.';
				} else {
					$json_result = 'error';
					$json_message = 'Error al guardar los datos.';
				}
			}
		}

		$json = array("result" => $json_result, "message" => $json_message);
		die(json_encode($json));
	}

	public function deshabilitar_familia()
	{
		$familia = $this->input->post("familia");
		if ($familia != "") {
			$cantidad = $this->familia_model->cantidad_productos($familia);
			if ($cantidad > 0) {
				$json_result = 'warning';
				$json_title = 'Familia no eliminada';
				$json_message = 'No se puede eliminar esta familia, mientras tenga productos relacionados';
			} else {
				$filter = new stdClass();
				$filter->FAMI_FlagEstado  = "0";
				$filter->FAMI_FechaModificacion = date('Y-m-d H:i:s');
				$result = $this->familia_model->actualizar($familia, $filter);
				if ($result) {
					$json_result = 'success';
					$json_title = 'Registro eliminado';
					$json_message = '';
				} else {
					$json_result = 'error';
					$json_title = 'Familia no eliminada';
					$json_message = 'No fue posible eliminar la familia. Intentelo nuevamente.';
				}
			}
		} else {
			$json_result = 'warning';
			$json_title = 'Familia no definida';
			$json_message = 'Sin cambios. Intentelo nuevamente.';
		}
		$json = array("result" => $json_result, "title" => $json_title, "message" => $json_message);
		die(json_encode($json));
	}
}
