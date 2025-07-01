<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Rol extends CI_Controller{

	##  -> Begin
	private $empresa;
	private $compania;
	private $url;
	private $view_js = NULL;
	##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->load->model('seguridad/rol_model');
		$this->load->model('seguridad/permiso_model');

		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
		$this->url = base_url();
		$this->view_js = array(0 => "seguridad/rol.js");
	}
	##  -> End

	##  -> Begin
	public function index(){
		$this->listar();
	}
	##  -> End

	##  -> Begin
	public function listar($j='0'){
		$modulos = $this->permiso_model->getModulos();
		$info = array();
		if ($modulos != NULL){
			foreach ($modulos as $i => $val) {
				$permisos = $this->permiso_model->getPermisos($val->MENU_Codigo);
				foreach ($permisos as $j => $value){
					if ($j == 0){
						$info[$i]["permiso"][] = $val->MENU_Codigo;
						$info[$i]["descripcion"][] = $val->MENU_Titulo;
						$info[$i]["modulo"][] = true;
					}

					$info[$i]["permiso"][] = $value->MENU_Codigo;
					$info[$i]["descripcion"][] = $value->MENU_Titulo;
					$info[$i]["modulo"][] = false;
				}
			}
		}

		$data["modulos"] = $info;
		$data['scripts'] = $this->view_js;
		$this->layout->view('seguridad/rol_index',$data);
	}
	##  -> End

	##  -> Begin
	public function datatable_rol(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "ROL_Descripcion"
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

		$filter->nombre = $this->input->post('nombre');

		$rolesInfo = $this->rol_model->getRoles($filter);
		$records = array();

		if ( $rolesInfo["records"] != NULL ) {
			foreach ($rolesInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->ROL_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
											</button>";
				$btn_borrar = "<button type='button' onclick='deshabilitar($valor->ROL_Codigo)' class='btn btn-default'>
												<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
											</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->ROL_Descripcion,
					++$posDT => $btn_modal,
					++$posDT => $btn_borrar
				);
			}
		}

		$recordsTotal = ( $rolesInfo["recordsTotal"] != NULL ) ? $rolesInfo["recordsTotal"] : 0;
		$recordsFilter = $rolesInfo["recordsFilter"];

		$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);

		echo json_encode($json);
		die();
	}
	##  -> End

	##  -> Begin
	public function guardar_registro(){

		$rol = $this->input->post("rol");
		$rol_nombre = $this->input->post("rol_nombre");

		$permiso = $this->input->post("permiso");

		$msj = "";

            #### INFORMACIÓN DEL ROL
		$filterRol = new stdClass();
		$filterRol->ROL_Descripcion = $rol_nombre;
		$filterRol->COMPP_Codigo = $this->compania;
		$filterRol->ROL_FlagEstado = "1";

            # ACTUALIZO LOS DATOS DEL ROL ELSE REGISTRO UN NUEVO ROL
		if ($rol != ""){
			$filterRol->ROL_FechaModificacion = date("Y-m-d H:i:s");
			$updated = $this->rol_model->actualizar_rol($rol, $filterRol);
		}
		else{
			$filterRol->ROL_FechaRegistro = date("Y-m-d H:i:s");
			$rol = $this->rol_model->registrar_rol($filterRol);
		}

		if ($rol != NULL){
                ### ASIGNAR PERMISOS
			$size = count($permiso);
                # PRIMERO ELIMINO TODOS LOS PERMISOS DEL ROL
			$this->permiso_model->clean_permisos($rol);
                # AHORA REGISTRO LOS NUEVOS
			if ($permiso != "" && $size > 0){
				for ( $i=0; $i < $size; $i++ ){
					$filterUsuarioPermiso = new stdClass();
					$filterUsuarioPermiso->ROL_Codigo = $rol;
					$filterUsuarioPermiso->MENU_Codigo = $permiso[$i];
					$filterUsuarioPermiso->COMPP_Codigo = $this->compania;
					$filterUsuarioPermiso->PERM_FlagEstado = 1;

					$this->permiso_model->registrar_permiso($filterUsuarioPermiso);
				}
			}
			$result = "success";
		}
		else
			$result = "error";

		$json = array("result" => $result, "mensaje" => $msj);
		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getPermisos(){
		$rol = $this->input->post('rol');

		$data = $this->permiso_model->getPermisosRol($rol);

		if ($data != NULL){
			$permisos = array();
			foreach ($data as $key => $value) {
				$permisos[] = $value->MENU_Codigo;
			}

			$info = array(
				"rol" => $data[0]->ROL_Codigo,
				"descripcion" => $data[0]->ROL_Descripcion,
				"permisos" => $permisos
			);
		}
		else
			$info = NULL;


		if ($info != NULL)
			$json = array("match" => true, "info" => $info);
		else
			$json = array("match" => false, "info" => NULL);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_rol(){
		$rol = $this->input->post("rol");

		$filter = new stdClass();
		$filter->ROL_FlagEstado  = "0";

		if ($rol != ""){
			$result = $this->rol_model->actualizar_rol($rol, $filter);
			$this->permiso_model->clean_permisos($rol);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

  ## FUNCTIONS OLDS
	public function JSON_listar_rol(){
		echo json_encode($this->rol_model->listar_roles());
	}
}
?>