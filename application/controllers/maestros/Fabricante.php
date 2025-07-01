<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Fabricante extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/fabricante_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/fabricante.js");
	}

	public function listar(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/fabricante_index', $data);
	}

	public function datatable_fabricante(){

		$posDT = -1;
		$columnas = array(
			++$posDT => "FABRIC_CodigoUsuario",
			++$posDT => "FABRIC_Descripcion"
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

		$fabricanteInfo = $this->fabricante_model->getFabricantes($filter, false);
		$records = array();

		if ( $fabricanteInfo["records"] != NULL) {
			foreach ($fabricanteInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->FABRIP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->FABRIP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->FABRIC_CodigoUsuario,
					++$posDT => $valor->FABRIC_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $fabricanteInfo["recordsTotal"] != NULL ) ? $fabricanteInfo["recordsTotal"] : 0;
		$recordsFilter = $fabricanteInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getFabricante(){

		$fabricante = $this->input->post("fabricante");

		$fabricanteInfo = $this->fabricante_model->getFabricante($fabricante);
		$lista = array();

		if ( $fabricanteInfo != NULL ){
			foreach ($fabricanteInfo as $indice => $val) {
				$lista = array(
					"fabricante" => $val->FABRIP_Codigo,
					"codigo" => $val->FABRIC_CodigoUsuario,
					"descripcion" => $val->FABRIC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$fabricante = $this->input->post("fabricante");
		$codigo_fabricante = $this->input->post("codigo_fabricante");
		$descripcion_fabricante = $this->input->post("descripcion_fabricante");

		$filter = new stdClass();
		$filter->FABRIC_Descripcion = strtoupper($descripcion_fabricante);
		$filter->FABRIC_FlagEstado = "1";

		if ($fabricante != ""){
			$filter->FABRIC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->fabricante_model->actualizar_fabricante($fabricante, $filter);
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
			if ($this->existsCode($codigo_fabricante, 'controller') == false){
				$filter->FABRIC_CodigoUsuario = $codigo_fabricante;
				$filter->FABRIC_FechaRegistro = date("Y-m-d H:i:s");
				$result = $this->fabricante_model->insertar_fabricante($filter);
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
				$json_message = "El código ($codigo_fabricante) fue registrado anteriormente.";
			}
		}

		$json = array("result" => $json_result, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	public function deshabilitar_fabricante(){

		$fabricante = $this->input->post("fabricante");

		$filter = new stdClass();
		$filter->FABRIC_FlagEstado  = "0";

		if ($fabricante != ""){
			$filter->FABRIC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->fabricante_model->deshabilitar_fabricante($fabricante, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function existsCode($code = NULL, $return = 'json'){

		if ($code == NULL)
			$codigo = trim($this->input->post("codigo_fabricante"));
		else
			$codigo = $code;

		$result = $this->fabricante_model->existsCode($codigo);

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