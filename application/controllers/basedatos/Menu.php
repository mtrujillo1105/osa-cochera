<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Menu extends CI_Controller {

	private $compania;
	private $usuario;
	private $url;
	private $view_js = NULL;

	public function __construct() {
		parent::__construct();


		$this->load->model('seguridad/menu_model');
		$this->load->model('seguridad/permiso_model');

		$this->compania = $this->session->userdata("compania");
		$this->usuario = $this->session->userdata("user");
		$this->url = base_url();
		$this->view_js = array(0 => "basedatos/basedatos.js");
	}

	public function index() {
		$data['titulo'] = "MENUS";
		$data['base_url'] = $this->url;
		$data['modulos'] = $this->menu_model->getModulos();
		$data['scripts'] = $this->view_js;
		$this->layout->view('basedatos/menu_index', $data);
	}

	public function datatable_menu(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "modulo",
			++$posDT => "MENU_Titulo",
			++$posDT => "MENU_Icon",
			++$posDT => "MENU_Url",
			++$posDT => "MENU_AccesoRapido",
			++$posDT => "MENU_OrderBy",
			++$posDT => "estado"
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

		$filter->menu = $this->input->post('menu');
		$filter->modulo = $this->input->post('modulo');

		$menuInfo = $this->menu_model->getMenus($filter);
		$records = array();

		if ($menuInfo["records"] != NULL) {
			foreach ($menuInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->MENU_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				if ($valor->MENU_FlagEstado == 1){
					$btn_estado = "<button type='button' onclick='deshabilitar($valor->MENU_Codigo)' class='btn btn-default'>
					<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
					</button>";
				}
				else{
					$btn_estado = "<button type='button' onclick='habilitar($valor->MENU_Codigo)' class='btn btn-default'>
					<img src='".$this->url."/public/images/icons/documento-add.png' class='image-size-1b'>
					</button>";
				}

				$colorAcceso = ($valor->MENU_AccesoRapido == '0') ? "color-red" : 'color-green';
				$colorEstado = ($valor->MENU_FlagEstado == '0') ? "color-red" : 'color-green';

				$btn_Order = "<form id='frmOrden$valor->MENU_Codigo' action='#' method='post'>
												<div class='input-group'>
													<input type='hidden' name='menu' value='$valor->MENU_Codigo'/>
													<input type='number' name='orden' id='iorden-$valor->MENU_Codigo' min='1' step='1' class='form-control form-control-sm' value='$valor->MENU_OrderBy' readonly aria-describedby='editOrder$valor->MENU_Codigo'/>
													<div class='input-group-append'>
														<span class='input-group-text bg-info' id='editOrderEnable-$valor->MENU_Codigo' onclick='changeOrderEdit($valor->MENU_Codigo)'>
															<i class='fas fa-edit'></i>
														</span>
														<span class='input-group-text bg-success oculto' id='editOrderUpdate-$valor->MENU_Codigo' onclick='changeOrderUpdate($valor->MENU_Codigo)'>
															<i class='fas fa-check'></i>
														</span>
														<span class='input-group-text bg-secondary oculto' id='editOrderDisable-$valor->MENU_Codigo' onclick='changeOrderDisable($valor->MENU_Codigo, $valor->MENU_OrderBy)'>
															<i class='fas fa-redo'></i>
														</span>
													</div>
												</div>
											</form>";

				$posDT = -1;
				$records[] = array(
					++$posDT => ($valor->modulo == "") ? "Modulo" : $valor->modulo,
					++$posDT => $valor->MENU_Titulo,
					++$posDT => "<i class='$valor->MENU_Icon'></i>",
					++$posDT => $valor->MENU_Url,
					++$posDT => "<span class='bold $colorAcceso'>$valor->acceso</span>",
					++$posDT => $btn_Order,
					++$posDT => "<span class='bold $colorEstado'>$valor->estado</span>",
					++$posDT => $btn_modal,
					++$posDT => $btn_estado
				);
			}
		}

		$recordsTotal = ( $menuInfo["recordsTotal"] != NULL ) ? $menuInfo["recordsTotal"] : 0;
		$recordsFilter = $menuInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}

	public function getMenu(){

		$codigo = $this->input->post("menu");

		$menuInfo = $this->menu_model->getMenu($codigo);
		$lista = array();

		if ( $menuInfo != NULL ){
			foreach ($menuInfo as $indice => $val) {
				$lista = array(
					"menu" => $val->MENU_Codigo,
					"padre" => $val->MENU_Codigo_Padre,
					"titulo" => $val->MENU_Titulo,
					"url" => $val->MENU_Url,
					"access" => $val->MENU_AccesoRapido,
					"order" => $val->MENU_OrderBy,
					"icon" => $val->MENU_Icon
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$menu = $this->input->post("menu");

		$padre = $this->input->post("modulo_padre");
		$titulo = $this->input->post("modulo_titulo");
		$url = $this->input->post("modulo_url");
		$access = $this->input->post("modulo_access");
		$order = $this->input->post("modulo_order");
		$icono = $this->input->post("modulo_icono");

		$filter = new stdClass();
		$filter->MENU_Codigo_Padre = $padre;
		$filter->MENU_Titulo = $titulo;
		$filter->MENU_Url = $url;
		$filter->MENU_AccesoRapido = $access;
		$filter->MENU_OrderBy = $order;
		$filter->MENU_Icon = $icono;
		$filter->MENU_FlagEstado = "1";

		if ($menu != ""){
			$filter->MENU_Codigo = $menu;
			$filter->MENU_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->menu_model->actualizar($menu, $filter);
		}
		else{
			$filter->MENU_FechaRegistro = date("Y-m-d H:i:s");
			$menu = $this->menu_model->insertar($filter);

      ## REGISTRO EL PERMISO PARA EL USUARIO CCAPA
			$filterUsuarioPermiso = new stdClass();
	    # 7000 ES EL ROL ASIGNADO AL USUARIO CCAPA
			$filterUsuarioPermiso->ROL_Codigo = 7000;
			$filterUsuarioPermiso->MENU_Codigo = $menu; 
			$filterUsuarioPermiso->COMPP_Codigo = $this->compania;
			$filterUsuarioPermiso->PERM_FlagEstado = 1;

			$result = $this->permiso_model->registrar_permiso($filterUsuarioPermiso);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function update_order(){
		$menu = trim($this->input->post("menu"));
		$order = trim($this->input->post("orden"));

		$filter = new stdClass();
		$filter->MENU_OrderBy = $order;

		if ($menu != "" && $order != ""){
			$filter->MENU_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->menu_model->actualizar($menu, $filter);
		}
		else{
			$result = false;
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
		die();
	}

	public function habilitar_menu(){

		$menu = $this->input->post("menu");

		$filter = new stdClass();
		$filter->MENU_FlagEstado  = "1";

		if ($menu != ""){
			$result = $this->menu_model->actualizar($menu, $filter);

			$filterUsuarioPermiso = new stdClass();
      # 7000 ES EL ROL ASIGNADO AL USUARIO CCAPA
			$filterUsuarioPermiso->ROL_Codigo = 7000;
			$filterUsuarioPermiso->MENU_Codigo = $menu; 
			$filterUsuarioPermiso->COMPP_Codigo = $this->compania;
			$filterUsuarioPermiso->PERM_FlagEstado = 1;

			$result = $this->permiso_model->registrar_permiso($filterUsuarioPermiso);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_menu(){

		$menu = $this->input->post("menu");

		$filter = new stdClass();
		$filter->MENU_FlagEstado  = "0";

		if ($menu != ""){
			$result = $this->menu_model->actualizar($menu, $filter);
			$this->permiso_model->delete_menu_permiso($menu);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
