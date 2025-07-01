<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Linea extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/linea_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/linea.js");
	}

	public function index(){
		$this->listar();
	}

	public function listar(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/linea_index', $data);
	}

	public function datatable_linea(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "LINC_CodigoUsuario",
			++$posDT => "LINC_Descripcion"
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

		$lineaInfo = $this->linea_model->getLineas($filter);
		$records = array();

		if ( $lineaInfo["records"] > 0) {
			foreach ($lineaInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->LINP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->LINP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->LINC_CodigoUsuario,
					++$posDT => $valor->LINC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $lineaInfo["recordsTotal"] != NULL ) ? $lineaInfo["recordsTotal"] : 0;
		$recordsFilter = $lineaInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getLinea(){

		$linea = $this->input->post("linea");

		$lineaInfo = $this->linea_model->getLinea($linea);
		$lista = array();

		if ( $lineaInfo != NULL ){
			foreach ($lineaInfo as $indice => $val) {
				$lista = array(
					"linea" => $val->LINP_Codigo,
					"codigo" => $val->LINC_CodigoUsuario,
					"descripcion" => $val->LINC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$linea = $this->input->post("linea");
		$codigo_linea = $this->input->post("codigo_linea");
		$descripcion_linea = $this->input->post("descripcion_linea");

		$filter = new stdClass();
		$filter->LINC_Descripcion = strtoupper($descripcion_linea);
		$filter->LINC_FlagEstado = "1";

		if ($linea != ""){
			$filter->LINC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->linea_model->actualizar_linea($linea, $filter);
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
				if ($this->existsCode($codigo_linea, 'controller') == false){
					$filter->LINC_CodigoUsuario = $codigo_linea;
					$filter->LINC_FechaRegistro = date("Y-m-d H:i:s");
					$result = $this->linea_model->insertar_linea($filter);
					if ($result){
						$json_result = "success";
						$json_message = "Registro exitoso.";
					}
					else{
						$json_result = "error";
						$json_message = "Error al guardar el registro, intentelo nuevamente.";
					}
				}
				else{
					$json_result = "error";
					$json_message = "El código ($codigo_linea) fue registrado anteriormente.";
				}
		}

		$json = array("result" => $json_result, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	public function deshabilitar_linea(){

		$linea = $this->input->post("linea");

		$filter = new stdClass();
		$filter->LINC_FlagEstado  = "0";

		if ($linea != ""){
			$filter->LINC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->linea_model->deshabilitar_linea($linea, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function existsCode($code = NULL, $return = 'json'){

		if ($code == NULL)
			$codigo = trim($this->input->post("linea_fabricante"));
		else
			$codigo = $code;

		$result = $this->linea_model->existsCode($codigo);

		if ($result != NULL)
			$json = array("match" => true, "code" => $result);
		else
			$json = array("match" => false, "code" => NULL);

		switch ($return) {
			case 'json':
					echo json_encode($json);
					die();
				break;
			case 'controller':
					return $json["match"];
				break;
			
			default:
					return $json["match"];
				break;
		}
	}
}
?>