<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Proyecto extends CI_Controller{

	private $usuario;
	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/proyecto_model');
		$this->load->model('maestros/ubigeo_model');
		$this->usuario = $this->session->userdata('user');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/proyecto.js");
	}

	public function index(){
		$this->listar();
	}

	public function listar(){
		$data["departamentos"] = $this->ubigeo_model->listar_departamentos();
		$data["provincias"] = $this->ubigeo_model->getProvincias("15");
		$data["distritos"] = $this->ubigeo_model->getDistritos("15","01");
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/proyecto_index', $data);
	}

	public function datatable_proyecto(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "EMPRC_Ruc",
			++$posDT => "EMPRC_RazonSocial",
			++$posDT => "PROYC_Nombre"
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

		$filter->cliente = $this->input->post('search_cliente');
		$filter->proyecto_titulo = $this->input->post('search_proyecto');

		$proyectoInfo = $this->proyecto_model->getProyectos($filter);
		$records = array();

		if ($proyectoInfo["records"] != NULL) {
			foreach ($proyectoInfo["records"] as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar($valor->PROYP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_info = "<button type='button' onclick='viewInfo($valor->PROYP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/icono-documentos.png' class='image-size-1b'>
				</button>";

				$btn_directions = "<button type='button' onclick='viewdirections($valor->PROYP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/icon-map.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->PROYP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->EMPRC_Ruc,
					++$posDT => $valor->EMPRC_RazonSocial,
					++$posDT => $valor->PROYC_Nombre,
					++$posDT => $btn_editar,
					++$posDT => $btn_directions,
					++$posDT => $btn_info,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $proyectoInfo["recordsTotal"] != NULL ) ? $proyectoInfo["recordsTotal"] : 0;
		$recordsFilter = $proyectoInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getProyecto(){

		$proyecto = $this->input->post("proyecto");

		$proyectoInfo = $this->proyecto_model->getProyecto($proyecto);
		$lista = array();
		$comprobantes = array();

		if ( $proyectoInfo != NULL ){
			foreach ($proyectoInfo as $indice => $val) {
				$lista = array(
					"proyecto" => $val->PROYP_Codigo,
					"cliente" => $val->CLIP_Codigo,
					"ruc" => $val->EMPRC_Ruc,
					"razon_social" => $val->EMPRC_RazonSocial,
					"nombre_proyecto" => $val->PROYC_Nombre,
					"fecha_inicio" => $val->PROYC_FechaInicio,
					"fecha_inicio_corta" => formatDate($val->PROYC_FechaInicio),
					"fecha_final" => $val->PROYC_FechaFin,
					"fecha_final_corta" => formatDate($val->PROYC_FechaFin),
					"descripcion_proyecto" => $val->PROYC_Descripcion
				);
			}

			$comprobantesInfo = $this->proyecto_model->getComprobantes($proyecto);
			if ( $comprobantesInfo != NULL ){
				foreach ($comprobantesInfo as $i => $val){
					$btn_pdfA4 = "<button type='button' class='btn btn-default' href='".$this->url."index.php/ventas/comprobante/comprobante_ver_pdf/".$val->CPP_Codigo."/a4' data-fancybox data-type='iframe'>
					<img src='".$this->url."public/images/icons/pdf.png' class='image-size-1b'>
					</button>";
					$comprobantes[] = array(
						"empresa_emisora" => $val->EMPRC_RazonSocial,
						"codigo" => $val->CPP_Codigo,
						"serie" => $val->CPC_Serie,
						"numero" => $val->CPC_Numero,
						"moneda" => $val->MONED_Simbolo,
						"importe" => $val->CPC_Total,
						"fecha" => formatDate($val->CPC_Fecha),
						"documento" => $val->documento,
						"estado" => $val->estado,
						"pdf" => $btn_pdfA4
					);
				}
			}

			$json = array("match" => true, "info" => $lista, "comprobantes" => $comprobantes);
		}
		else
			$json = array("match" => false, "info" => "", "comprobantes" => NULL);

		echo json_encode($json);
	}

	public function guardar_registro(){
		
		$proyecto = $this->input->post("proyecto");
		$cliente = $this->input->post("cliente");
		$nombre_proyecto = $this->input->post("nombre_proyecto");
		$fecha_inicio = $this->input->post("fecha_inicio");
		$fecha_final = $this->input->post("fecha_final");
		$descripcion_proyecto = $this->input->post("descripcion_proyecto");

		$filter = new stdClass();
		$filter->PROYC_Nombre = strtoupper($nombre_proyecto);
		$filter->PROYC_Descripcion = strtoupper($descripcion_proyecto);
		$filter->PROYC_FechaInicio = ($fecha_inicio == "") ? date("Y-m-d") : $fecha_inicio;
		$filter->PROYC_FechaFin = ($fecha_final == "") ? date("Y-m-d") : $fecha_final;
		$filter->PROYC_CodigoUsuario = $this->usuario;
		$filter->CLIP_Codigo = $cliente;
		$filter->PROYC_FlagEstado = "1";

		if ($proyecto != ""){
			$filter->PROYP_Codigo = $proyecto;
			$filter->PROYC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->proyecto_model->actualizar_proyecto($proyecto, $filter);
		}
		else{
			$filter->PROYC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->proyecto_model->insertar_proyecto($filter);
		}


		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_proyecto(){

		$proyecto = $this->input->post("proyecto");

		$filter = new stdClass();
		$filter->PROYC_FlagEstado  = "0";

		if ($proyecto != ""){
			$filter->PROYC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->proyecto_model->deshabilitar_proyecto($proyecto, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	/** Directions **/

	public function datatable_directions(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "DIRECC_Descrip",
			++$posDT => "DIRECC_Referen",
			++$posDT => "dpto",
			++$posDT => "prov",
			++$posDT => "dist"
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

		$filter->proyecto = $this->input->post('proyecto');
		$directionsInfo = $this->proyecto_model->getDirections($filter);

		$lista = array();
		if ( $directionsInfo != NULL) {
			foreach ($directionsInfo as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar_directions($valor->DIRECC_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_deshabilitar = "<button type='button' onclick='disable_directions($valor->DIRECC_Codigo, $valor->PROYP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";
				$posDT = -1;
				$lista[] = array(
					++$posDT => $valor->DIRECC_Descrip,
					++$posDT => $valor->DIRECC_Referen,
					++$posDT => $valor->dpto,
					++$posDT => $valor->prov,
					++$posDT => $valor->dist,
					++$posDT => $btn_editar,
					++$posDT => $btn_deshabilitar
				);
			}
		}

		unset($filter->start);
		unset($filter->length);

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => count($this->proyecto_model->getDirections()),
			"recordsFiltered" => intval( count($this->proyecto_model->getDirections($filter)) ),
			"data"            => $lista
		);

		echo json_encode($json);
	}

	public function getDirection(){

		$direction = $this->input->post("direction");

		$directionInfo = $this->proyecto_model->getDirection($direction);
		$lista = array();

		if ( $directionInfo != NULL ){
			foreach ($directionInfo as $indice => $val) {
				$lista = array(
					"direction_id" => $val->DIRECC_Codigo,
					"proyecto" => $val->PROYP_Codigo,
					"direccion" => $val->DIRECC_Descrip,
					"referencia" => $val->DIRECC_Referen,
					
					"departamento" => substr($val->UBIGP_Domicilio, 0, 2),
					"provincia" => substr($val->UBIGP_Domicilio, 2, 2),
					"distrito" => substr($val->UBIGP_Domicilio, 4, 2)					
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function save_direction(){
		
		$id = $this->input->post("direction_id");
		$proyecto = $this->input->post("proyecto_id");
		$direccion = $this->input->post("direccion_proyecto");
		$referencia = $this->input->post("referencia_proyecto");

		$departamento = $this->input->post("departamento");
		$provincia = $this->input->post("provincia");
		$distrito = $this->input->post("distrito");
		$ubigeo = $departamento.$provincia.$distrito;

		$filter = new stdClass();
		$filter->DIRECC_Descrip = $direccion;
		$filter->DIRECC_Referen = $referencia;
		$filter->PROYP_Codigo = $proyecto;
		$filter->UBIGP_Domicilio = $ubigeo;
		$filter->DIRECC_CodigoUsuario = $this->usuario;
		$filter->DIRECC_FlagEstado = "1";

		if ($id != ""){
			$filter->DIRECC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->proyecto_model->updateDirection($id, $filter);
		}
		else{
			$filter->DIRECC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->proyecto_model->saveDirection($filter);
		}


		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function disable_direction(){

		$direccion = $this->input->post("direccion");

		$filter = new stdClass();
		$filter->DIRECC_FlagEstado = "0";
		$filter->DIRECC_FechaModificacion = date("Y-m-d H:i:s");

		if ($direccion != "")
			$direccion = $this->proyecto_model->updateDirection($direccion, $filter);

		if ($direccion)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

}       
?>