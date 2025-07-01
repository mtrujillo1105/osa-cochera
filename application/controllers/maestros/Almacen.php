<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Almacen extends CI_Controller{

	private $compania;
	private $url;
	private $establecimiento;
	private $nombre_establecimiento;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/almacen_model');
		$this->load->model('almacen/tipoalmacen_model');

		$this->compania = $this->session->userdata('compania');
		$this->establecimiento = $this->session->userdata('establec');
		$this->nombre_establecimiento = $this->session->userdata('nombre_establec');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/almacen.js");
	}

	public function index(){
		$this->listar();
	}

	public function listar(){
		$data['base_url'] = $this->url;
		$data['tipo_almacen'] = $this->tipoalmacen_model->listar();
		$data['establecimiento'] = $this->establecimiento;
		$data['nombre_establecimiento'] = $this->nombre_establecimiento;

		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/almacen_index', $data);
	}

	public function datatable_almacen(){

		$posDT = -1;
		$columnas = array(
			++$posDT => "ALMAC_CodigoUsuario",
			++$posDT => "EESTABC_Descripcion",
			++$posDT => "ALMAC_Descripcion",
			++$posDT => "TIPALM_Descripcion",
			++$posDT => "ALMAC_Compartido",
			++$posDT => "ALMAC_Direccion"
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
		$filter->tipo = $this->input->post('tipo');

		$almacenInfo = $this->almacen_model->getAlmacens($filter, false);
		$records = array();

		if ( $almacenInfo["records"] != 0) {
			foreach ($almacenInfo["records"]  as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->ALMAP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->ALMAP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->ALMAC_CodigoUsuario,
					++$posDT => $valor->EESTABC_Descripcion,
					++$posDT => $valor->ALMAC_Descripcion,
					++$posDT => $valor->TIPALM_Descripcion,
					++$posDT => $valor->compartido,
					++$posDT => $valor->ALMAC_Direccion,
					++$posDT => $btn_modal,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $almacenInfo["recordsTotal"] != NULL ) ? $almacenInfo["recordsTotal"] : 0;
		$recordsFilter = $almacenInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getAlmacen(){

		$almacen = $this->input->post("almacen");

		$almacenInfo = $this->almacen_model->getAlmacen($almacen);
		$lista = array();

		if ( $almacenInfo != NULL ){
			foreach ($almacenInfo as $indice => $val) {
				$lista = array(
					"almacen" => $val->ALMAP_Codigo,
					"codigo" => $val->ALMAC_CodigoUsuario,
					"descripcion" => $val->ALMAC_Descripcion,
					"tipo" => $val->TIPALM_Codigo,
					"compartido" => $val->ALMAC_Compartido,
					"direccion" => $val->ALMAC_Direccion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){
		$almacen = $this->input->post("almacen");
		$establecimiento = $this->input->post("establecimiento");
		$codigo_almacen = $this->input->post("codigo_almacen");
		$descripcion_almacen = $this->input->post("descripcion_almacen");
		$tipo_almacen = $this->input->post("tipo_almacen");
		$compartir = $this->input->post("compartir_almacen");
		$direccion_almacen = $this->input->post("direccion_almacen");

		$filter = new stdClass();
		$filter->TIPALM_Codigo = $tipo_almacen;
		$filter->CENCOSP_Codigo = "1";
		$filter->ALMAC_Descripcion = strtoupper($descripcion_almacen);
		$filter->ALMAC_Compartido = $compartir;
		$filter->ALMAC_Direccion = strtoupper($direccion_almacen);
		$filter->ALMAC_FlagEstado = "1";

		if ($almacen != ""){
			$filter->ALMAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->almacen_model->actualizar_almacen($almacen, $filter);
			
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
			if ($this->existsCode($codigo_almacen, 'controller') == false){
				$filter->EESTABP_Codigo = $this->establecimiento;
				$filter->ALMAC_CodigoUsuario = trim($codigo_almacen);
				$filter->COMPP_Codigo = $this->compania;
				$filter->ALMAC_FechaRegistro = date("Y-m-d H:i:s");
				$result = $this->almacen_model->insertar_almacen($filter);
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
				$json_message = "El código ($codigo_almacen) fue registrado anteriormente.";
			}
		}

		$json = array("result" => $json_result, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	public function deshabilitar_almacen(){

		$almacen = $this->input->post("almacen");

		$filter = new stdClass();
		$filter->ALMAC_FlagEstado  = "0";

		if ($almacen != ""){
			$filter->ALMAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->almacen_model->deshabilitar_almacen($almacen, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function existsCode($code = NULL, $return = 'json'){

		if ($code == NULL)
			$codigo = trim($this->input->post("codigo_almacen"));
		else
			$codigo = $code;

		$result = $this->almacen_model->existsCode($codigo);

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