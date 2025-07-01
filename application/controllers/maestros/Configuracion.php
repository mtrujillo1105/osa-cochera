<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Configuracion extends CI_Controller {

	private $compania;
	private $view_js = NULL;

	public function __construct() {
		parent::__construct();
		$this->load->model('empresa/empresa_model');
		$this->load->model('seguridad/permiso_model');
		$this->load->model('maestros/compania_model');
		$this->load->model('maestros/configuracion_model');
		$this->load->model('maestros/companiaconfiguracion_model');
                $this->load->model('tesoreria/caja_model');
		$this->compania = $this->session->userdata('compania');
		$this->view_js = array(0 => "maestros/configuracion.js");
	}

	public function index() {
		$this->layout->view('seguridad/inicio');
	}

	##  -> Begin
	public function cambiar_sesion(){
		$company = $this->input->post('sessionCompany');

		if ($company != ""){
			$compania = $this->compania_model->obtener_compania($company);
			$empresa = $this->empresa_model->obtener_datosEmpresa($compania[0]->EMPRP_Codigo);
                        $cajas = $this->caja_model->getCajasLogin($company);

			if ($empresa != NULL){
				$datos_compania = $this->compania_model->obtener($compania[0]->COMPP_Codigo);
				$rol = $this->permiso_model->obtener_rol_compania($compania[0]->COMPP_Codigo, $this->session->userdata('user'));

				$datosSesion = array(
					"compania" => $compania[0]->COMPP_Codigo,
					"empresa" => $compania[0]->EMPRP_Codigo,
					"nombre_empresa" => $empresa[0]->EMPRC_RazonSocial,
					"establec" => $datos_compania[0]->EESTABP_Codigo,
					"rol" => $rol[0]->ROL_Codigo,
					"desc_rol" => $rol[0]->ROL_Descripcion,
                                        "caja_activa" => ($cajas==NULL)?0:$cajas[0]->CAJA_Codigo,
                                        "cajero_id"   => ($cajas==NULL)?0:$cajas[0]->CAJA_Usuario
				);
				$this->session->set_userdata($datosSesion);
				$json = array("result" => "success", "message" => 'Cambio completo, actualice la página.');
			}
			else
				$json = array("result" => "success", "message" => 'Datos de la empresa no disponible.');
		}
		else
			$json = array("result" => "error", "message" => 'Compañia invalida.');

		echo json_encode($json);
		die();
	}

	public function configuracion() {
		$seriesInfo = $this->configuracion_model->getSeriesDocumentos();
		$cfg = $this->companiaconfiguracion_model->getConfiguracion($this->compania);
		$data['series'] = $seriesInfo;
		$data['cfg'] = $cfg;
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/configuracion_nuevo', $data);
	}

	public function guardar_articulos_cfg(){
		$igv = $this->input->post("igv");
		$precio_igv = $this->input->post("precio_igv");
		$determina_precio = $this->input->post("determina_precio");
		$codigo_productos = $this->input->post("codigo_productos");
		$bscodigo = $this->input->post("codigo_unico");

		$validacion = true;
		$filter = new stdClass();
		$filter->COMPCONFIC_Igv = $igv;
		$filter->COMPCONFIC_PrecioContieneIgv = $precio_igv;
		$filter->COMPCONFIC_DeterminaPrecio = $determina_precio;
		$filter->COMPCONFIC_CodigoProductos = $codigo_productos;
		$filter->COMPCONFIC_BSCodigo = $bscodigo;

		if (!is_numeric($igv) || $igv < 0){
			$json_status = 'error';
			$json_message = 'Valor de IGV invalido.';
			$validacion = false;
		}

		if ($validacion == true){
			if ($this->compania != ""){
				$filter->COMPCONFIC_FechaModificacion = date("Y-m-d H:i:s");
				$result = $this->companiaconfiguracion_model->actualizar_configuracion($this->compania, $filter);

				if ($result){
					$json_status = 'success';
					$json_message = 'Registro actualizado.';
				}
				else{
					$json_status = 'error';
					$json_message = 'Error al actualizar el registro. Intentelo nuevamente';
				}
			}
			else{
				$json_status = 'error';
				$json_message = 'Compañia (tienda) invalida.';
			}
		}

		$json = array("result" => $json_status, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	public function guardar_series_cfg(){

		$configuracion = $this->input->post("configuracion");
		$serie = $this->input->post("serie");
		$numero = $this->input->post("numero");

		$cant_cfg = count($configuracion);
		$cant_serie = count($serie);
		$cant_numero = count($numero);
		$validacion = true;
		$this->db->trans_begin();

		if ( $cant_cfg == $cant_serie && $cant_cfg == $cant_numero ){
			$filter = new stdClass();

			for ( $i=0; $i<$cant_cfg; $i++ ){
				$filter->CONFIC_Serie = $serie[$i];
				$filter->CONFIC_Numero = $numero[$i];

				if ($configuracion[$i] == "" || $serie[$i] == "" || $numero[$i] == ""){
					$json_status = 'danger';
					$json_message = 'Valor invalido en uno de los campos. Los cambios no fueron aplicados.';
					$validacion = false;
					break;
				}

				if ($validacion == true)
					$this->configuracion_model->actualizar_series($configuracion[$i], $filter);
			}
		}
		else{
			$json_status = 'error';
			$json_message = '';
		}

		if ($validacion){
			$json_status = 'success';
			$json_message = 'Registro actualizado.';
		}

		if($this->db->trans_status() == false || $validacion == false)
      $this->db->trans_rollback();
    else
      $this->db->trans_commit();

		$json = array("result" => $json_status, "message" => $json_message);
		echo json_encode($json);
		die();
	}

	/** AGREGAR EMPRESAS Y COMPAÑIAS **/

	public function agregar_empresa(){
		$filter = new stdClass();
		$filter->CIIUP_Codigo = "0";
		$filter->TIPCOD_Codigo = "1";
		$filter->SECCOMP_Codigo = "1";
		$filter->EMPRC_Ruc = $this->input->post("ruc");
		$filter->EMPRC_RazonSocial = $this->input->post("razon_social");
		$filter->EMPRC_Telefono = $this->input->post("telefono");
		$filter->EMPRC_Movil = $this->input->post("movil");
		$filter->EMPRC_Fax = $this->input->post("fax");
		$filter->EMPRC_Web = $this->input->post("web");
		$filter->EMPRC_Email = $this->input->post("email");
		$filter->EMPRC_CtaCteSoles = "";
		$filter->EMPRC_CtaCteDolares = "";
		$filter->EMPRC_FechaRegistro = date("Y-m-d h:i:s");
		$filter->EMPRC_FechaModificacion = NULL;
		$filter->EMPRC_FlagEstado = "1";
		$filter->EMPRC_Direccion = $this->input->post("direccion");

		$empresa = $this->compania_model->agregar_empresa($filter);

		if ($empresa != NULL && $empresa != "")
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function agregar_companias(){

		$empresa = $this->input->post("empresa");
		$ubigeo = $this->input->post("ubigeo");
		$descripcion = $this->input->post("descripcion");
		$direccion = $this->input->post("direccion");
		/** ESTABLECIMIENTO **/
		$filterEstablecimiento = new stdClass();
		$filterEstablecimiento->EESTABP_Codigo = NULL;
		$filterEstablecimiento->TESTP_Codigo = "1";
		$filterEstablecimiento->EMPRP_Codigo = $empresa;
		$filterEstablecimiento->UBIGP_Codigo = $ubigeo;
		$filterEstablecimiento->EESTABC_Descripcion = $descripcion;
		$filterEstablecimiento->EESTAC_Direccion = $direccion;
		$filterEstablecimiento->EESTABC_FechaRegistro = date('Y-m-d h:i:s');
		$filterEstablecimiento->EESTABC_FechaModificacion = NULL;
		$filterEstablecimiento->EESTABC_FlagTipo = "1";
		$filterEstablecimiento->EESTABC_FlagEstado = "1";

		$establecimiento = $this->compania_model->agregar_establecimiento($filterEstablecimiento);

		/** COMPAÑIA **/

		$filterCompania = new stdClass();
		$filterCompania->COMPP_Codigo = NULL;
		$filterCompania->EMPRP_Codigo = $empresa;
		$filterCompania->EESTABP_Codigo = $establecimiento;
		$filterCompania->COMPC_Logo = "";
		$filterCompania->COMPC_TipoValorizacion = "0";
		$filterCompania->COMPC_FlagEstado = "1";

		$compania = $this->compania_model->agregar_compania($filterCompania);

		/**  USUARIOS **/
		/** USUARIO CCAPA **/
		$filterUsuarioCompania = new stdClass();
		$filterUsuarioCompania->USUCOMP_Codigo = NULL;
		$filterUsuarioCompania->USUA_Codigo = 2;
		$filterUsuarioCompania->COMPP_Codigo = $compania;
		$filterUsuarioCompania->ROL_Codigo = "7000";
		$filterUsuarioCompania->CARGP_Codigo = "0";
		$filterUsuarioCompania->USUCOMC_Default = "1";

		$this->compania_model->agregar_usuario_compania($filterUsuarioCompania);

		/** USUARIO ADMINISTRADOR **/
		$filterUsuarioCompania->USUA_Codigo = 1;
		$filterUsuarioCompania->ROL_Codigo = "1";
		$filterUsuarioCompania->CARGP_Codigo = "1";
		$filterUsuarioCompania->USUCOMC_Default = "1";

		$this->compania_model->agregar_usuario_compania($filterUsuarioCompania);

		$json = array("result" => "success");
		echo json_encode($json);
	}

}
