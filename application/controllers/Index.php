<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Index extends CI_Controller{

    ##  -> Begin
    private $compania;
    private $base_url;
    ##  -> End

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('almacen/producto_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/emprestablecimiento_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('seguridad/permiso_model');
        $this->load->model('seguridad/menu_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('seguridad/usuario_compania_model');
        $this->load->model('seguridad/rol_model');
        $this->load->model('tesoreria/caja_model');
        $this->compania = $this->session->userdata('compania');
        $this->base_url = base_url();
    }

    public function index( $msg = NULL ){
        $data["msg"] = $msg;
        $data["base_url"] = $this->base_url;
        $this->load->view("index", $data);
    }

    public function obtener_datosUsuarioLogin(){
        $txtUsuario = $this->input->post('usuario');
        $txtClave   = $this->input->post('clave');
        $ticket     = $this->input->post('parqueo'); 

        if ($txtUsuario != "" && $txtClave != "") {
            $txtClave = md5($txtClave);
            $datos_usuario = $this->usuario_model->obtener_datosUsuarioLogin($txtUsuario, $txtClave);
            if($datos_usuario){
                $usuario = $datos_usuario[0]->USUA_Codigo;
                $empresa = $this->usuario_model->obtener_empresa_usuario($usuario);
                $datos_usu_com = $this->usuario_compania_model->listar($usuario, $empresa[0]->EMPRP_Codigo);
                $obtener_rol   = $this->usuario_model->obtener_rolesUsuario($usuario,$empresa[0]->EMPRP_Codigo);
                $rol  = $obtener_rol[0]->ROL_Codigo;
                if($rol == 7000 || $rol == 7005  || $rol == 1){
                    $json = array('result' => 'success', 'message' => 'Usuario autorizado', 'info' => $datos_usuario);
                }
                else{
                    $json = array('result' => 'error', 'message' => 'Usuario no autorizado');
                }
            }
            else{
                $json = array('result' => 'error', 'message' => 'Las credenciales son incorrectas');
            }
        }
        else{
            $json = array('result' => 'error', 'message' => 'No se enviaron datos');
        }
        echo json_encode($json);
        die();
            
    }

    public function ingresar_sistema(){
        $txtUsuario = $this->input->post('txtUsuario');
        $txtClave = $this->input->post('txtClave');
        if ($txtUsuario == "" || $txtClave == "") {
                $this->index("<br><div align='center' class='error'>Login invalido.</div>");
        }
        else {
            $txtClave = md5($txtClave);
            $datos_usuario = $this->usuario_model->obtener_datosUsuarioLogin($txtUsuario, $txtClave);
            if ($datos_usuario != NULL) {
                $empresa = $this->usuario_model->obtener_empresa_usuario($datos_usuario[0]->USUA_Codigo);
                $datos_usu_com = $this->usuario_compania_model->listar($datos_usuario[0]->USUA_Codigo, $empresa[0]->EMPRP_Codigo);

                if (count($datos_usu_com) > 0) {
                    $datos_compania = $this->compania_model->obtener($datos_usu_com[0]->COMPP_Codigo);
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);
                    $datos_establec = $this->emprestablecimiento_model->obtener($datos_compania[0]->EESTABP_Codigo);
                    $usuario = $datos_usuario[0]->USUA_Codigo;
//obtengo rol
                    $obtener_rol = $this->usuario_model->obtener_rolesUsuario($usuario, $empresa/* , $establecimiento */);

                    if (count($obtener_rol) > 0) {
                        $persona = $datos_usuario[0]->PERSP_Codigo;
                        $rol = $obtener_rol[0]->ROL_Codigo;
                        $desc_rol = $obtener_rol[0]->ROL_Descripcion;
                        $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                        $datos_rol = $this->rol_model->obtener_rol($rol);
                        $nombre_rol = $datos_rol[0]->ROL_Descripcion;
                        $nombre_persona = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno;
                        $datos_permisos = $this->permiso_model->obtener_permisosMenu($rol);
                        //Get Caja activa
                        $cajas = $this->caja_model->getCajasLogin($datos_usu_com[0]->COMPP_Codigo);
                        $data = array(
                                'user' => $usuario,
                                'persona' => $persona,
                                'nombre_persona' => $nombre_persona,
                                'rol' => $rol,
                                'desc_rol' => $desc_rol,
                                'nombre_rol' => $nombre_rol,
                                'compania' => $datos_usu_com[0]->COMPP_Codigo,
                                'empresa'  => $datos_empresa[0]->EMPRP_Codigo,
                                'nombre_empresa' => $datos_empresa[0]->EMPRC_RazonSocial,
                                'establec' => $datos_establec[0]->EESTABP_Codigo,
                                'nombre_establec' => $datos_establec[0]->EESTABC_Descripcion,
                                'constante' => 0,
                                'menu' => 0,
                                'user_name'   => strtolower($txtUsuario),
                                'idcompania'  => $datos_compania[0]->COMPP_Codigo,
                                'codUsuario'  => $datos_usuario[0]->PERSP_Codigo,
                                'caja_activa' => ($cajas==NULL)?0:$cajas[0]->CAJA_Codigo,
                                'cajero_id'   => ($cajas==NULL)?0:$cajas[0]->CAJA_Usuario
                        );
                        $this->session->set_userdata($data);
                        //header("Location:" . base_url() . "index.php/index/inicio");
                        header("Location:" . base_url() . "index.php/seguridad/inicio");
                    }
                    else {
                        $msgError = "<br><div align='center' class='error'>Su usuario no tiene acceso a la informacion de ninguna empresa.</div>";
                        $this->index($msgError);
                    }
                }
                else {
                    $msgError = "<br><div align='center' class='error'>Su usuario no tiene acceso a la informacion de ninguna empresa.</div>";
                    $this->index($msgError);
                }
            }
            else {
                $msgError = "<br><div align='center' class='error'>Usuario y/o contraseña no valido.</div>";
                $this->index($msgError);
            }
        }
    }

    public function inicio($j = 0, $k = 0){
        $fecha = date("Y-m-d");
        $data = array();
        $tcInfo = $this->tipocambio_model->getTCday($fecha);
        $faltan = 0;
        if ($tcInfo != NULL){
            foreach ($tcInfo as $key => $value){
                if ( $value->TIPCAMC_FactorConversion == NULL || $value->TIPCAMC_FactorConversion == 0 )
                        $faltan = 1;
            }
            $data["tcf"] = $faltan;
        }
        else{
            $data["tcf"] = 1;
            echo "aqui";
        }
        $data["compania"] = $this->compania;
        $data["nombre_empleado"] = $_SESSION['nombre_persona'];
        //$this->layout->view("ventas/parqueo_index", $data);
        //header("Location:" . base_url() . "index.php/ventas/parqueo");
        $this->layout->view("seguridad/inicio", $data);
    }

    public function salir_sistema(){
        session_destroy();
        unset($_SESSION);
        header("Location:".$this->base_url);
    }

    public function seleccionar_compania(){
        $array_empresas = $this->compania_model->listar_empresas();
        $arreglo = array();
        foreach ($array_empresas as $indice => $valor) {
            $empresa = $valor->EMPRP_Codigo;
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
            $arreglo[] = array('tipo' => '1', 'nombre' => $razon_social, 'empresa' => $empresa);
        }
        return $arreglo;
    }

	/**gcbq:ponemos en session el menu seleccionado**/
	public function sessionMenuSeleccion(){
		$idMenuSeleccionado = $this->input->post('idMenuSeleccionadoReal');
		$idMenuSub = $this->input->post('idMenusubReal');

		if($idMenuSeleccionado!=null && $idMenuSeleccionado!=0)
			$_SESSION['idMenuSeleccionado']=$idMenuSeleccionado;
		else
			unset($_SESSION['idMenuSeleccionado']);

		if($idMenuSub!=null && $idMenuSub!=0)
			$_SESSION['idMenuSub']=$idMenuSub;
		else
			unset($_SESSION['idMenuSub']);
	}

}
?>