<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Usuario extends CI_Controller{

	##  -> Begin
	private $empresa;
	private $compania;
	private $rol;
	private $url;
	private $view_js = NULL;
	##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->library('tokens');
		$this->load->model('almacen/guiatrans_model');
		$this->load->model('almacen/guiain_model');
		$this->load->model('almacen/guiarem_model');
		$this->load->model('maestros/persona_model');
		$this->load->model('ventas/comprobante_model');
		$this->load->model('ventas/notacredito_model');
		$this->load->model('seguridad/usuario_model');
		$this->load->model('seguridad/usuario_compania_model');
		$this->load->model('maestros/emprestablecimiento_model');
                $this->load->model('tesoreria/movimiento_model');
                $this->load->model('tesoreria/cajacierre_model');
		$this->load->model('maestros/compania_model');
		$this->load->model('empresa/directivo_model');
		$this->load->model('seguridad/rol_model');

		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
                
                $this->user     = $this->session->userdata('user');
		$this->rol = $this->session->userdata('rol');
		$this->url = base_url();
		$this->view_js = array(0 => "seguridad/usuario.js");
	}

	public function index(){
		$this->usuarios();
	}

	##  -> Begin
	public function usuarios(){
		$data['directivos'] = $this->directivo_model->listar_combodirectivo($this->empresa);
		$data['roles'] = $this->rol_model->listar_roles();
		$data['establecimientos'] = $this->usuario_model->getEstablecimientos();
		$data['scripts'] = $this->view_js;
		$this->layout->view('seguridad/usuario_index', $data);
	}
	##  -> End

	##  -> Begin
	public function datatable_usuarios(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "nombres",
			++$posDT => "USUA_usuario",
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

		$filter->searchNombre = $this->input->post("txtNombres");
		$filter->searchUsuario = $this->input->post("txtUsuario");
		$filter->searchRol = $this->input->post("txtRol");

		$usuariosInfo = $this->usuario_model->getUsuariosDatatable($filter);
		$records = array();

		if ( $usuariosInfo["records"] != NULL ){
			foreach ($usuariosInfo["records"] as $indice => $valor){

				$btn_editar = "<button type='button' onclick='editar_usuario($valor->PERSP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_view = "<button type='button' onclick='ver_usuario($valor->USUA_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/icono-documentos.png' class='image-size-1b'>
				</button>";

				$btn_eliminar = "<button type='button' onclick='deshabilitar($valor->USUA_Codigo)' class='btn btn-default'>
				<img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->nombres,
					++$posDT => $valor->USUA_usuario,
					++$posDT => $valor->ROL_Descripcion,
					++$posDT => $btn_editar,
					++$posDT => $btn_view,
					++$posDT => $btn_eliminar
				);
			}
		}

		$recordsTotal = ( $usuariosInfo["recordsTotal"] != NULL ) ? $usuariosInfo["recordsTotal"] : 0;
		$recordsFilter = $usuariosInfo["recordsFilter"];

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

		$usuario = $this->input->post("usuario");
		$persona = $this->input->post("persona");

		$nombre_usuario = $this->input->post("txtUsuario");
		$clave = $this->input->post("txtClave");
		$clave2 = $this->input->post("txtClave2");
		$msj = "";

		if ($clave == $clave2){       
			$establecimientos = $this->input->post("establecimientos");
			$rol = $this->input->post("rol");
			$acceso = $this->input->post("acceso");

                #### INFORMACIÓN DEL USUARIO
			$filterUsuario = new stdClass();
			$filterUsuario->PERSP_Codigo = $persona;
			$filterUsuario->ROL_Codigo = "0";
			$filterUsuario->USUA_usuario = $nombre_usuario;

			if ($clave != "")
				$filterUsuario->USUA_Password = md5($clave);

			$filterUsuario->USUA_FechaRegistro = date("Y-m-d H:i:s");
			$filterUsuario->USUA_FlagEstado = 1;

                # ACTUALIZO LOS DATOS DEL USUARIO ELSE REGISTRO UN NUEVO USUARIO
			if ($usuario != ""){                        
				$filterUsuario->USUA_Codigo = $usuario;
				$updated = $this->usuario_model->actualizar_usuario($usuario, $filterUsuario);

			}
			else{      
				$usuario = $this->usuario_model->registrar_usuario($filterUsuario);
                  }
                        
			if ($usuario != NULL){
                    ### ASIGNAR ESTABLECIMIENTOS
				$size = count($establecimientos);
                    # PRIMERO ELIMINO TODOS LOS ACCESOS DEL USUARIO
				$this->usuario_compania_model->clean_acceso_usuario($usuario);
                    # AHORA REGISTRO LOS NUEVOS
				if ($establecimientos != "" && $size > 0){
					$default = true;
					for ( $i=0; $i < $size; $i++ ){
						if ($acceso[$i] == 1){
							$filterUsuarioCompania = new stdClass();
							$filterUsuarioCompania->USUA_Codigo = $usuario;
							$filterUsuarioCompania->COMPP_Codigo = $establecimientos[$i];
							$filterUsuarioCompania->ROL_Codigo = $rol[$i];
							$filterUsuarioCompania->CARGP_Codigo = "1";
							$filterUsuarioCompania->USUCOMC_Default = ($default == true) ? 1 : 0;

							$this->usuario_compania_model->registrar_acceso_usuario($filterUsuarioCompania);
							$default = false;
						}
					}
				}
				$result = "success";
			}
			else
				$result = "error";
		}
		else{
			$result = "error";
			$msj = "LAS CONTRASEÑAS NO COINCIDEN";
		}

		$json = array("result" => $result, "mensaje" => $msj);
		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_usuario(){
		$usuario = $this->input->post('usuario');
		$result = $this->usuario_model->deshabilitar_usuario($usuario);

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getUsuario(){
		$usuario = $this->input->post('usuario');

		$data = $this->usuario_model->getUsuario($usuario);

		if ($data != NULL){

			$acceso = $this->usuario_model->getAccesoUsuario($data[0]->USUA_Codigo);

			$access = array();
			if ($acceso != NULL){
				foreach ($acceso as $i => $val){
					$access[$i]["empresa"] = $val->EMPRC_RazonSocial;
					$access[$i]["establecimiento"] = $val->EESTABC_Descripcion;
					$access[$i]["rol"] = $val->ROL_Descripcion;
				}
			}

			$info = array(
				"usuario" => $data[0]->USUA_Codigo,
				"nombre_usuario" => $data[0]->USUA_usuario,

				"nombres" => $data[0]->PERSC_Nombre,
				"apellido_paterno" => $data[0]->PERSC_ApellidoPaterno,
				"apellido_materno" => $data[0]->PERSC_ApellidoMaterno,

				"acceso" => $access
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
	public function getPersonaUsuario(){
		$persona = $this->input->post('persona');

		$data = $this->usuario_model->getPersonaUsuario($persona);

		if ( $data[0]->USUA_usuario != NULL && $data[0]->USUA_usuario != ""){
			$nombre_usuario = $data[0]->USUA_usuario;
			$acceso = $this->usuario_model->getAccesoUsuario($data[0]->USUA_Codigo);
		}
		else{
			if ($data[0]->PERSC_Email != NULL && $data[0]->PERSC_Email != "")
				$nombre_usuario = $data[0]->PERSC_Email;
			else{
				$nvoNombre = explode(" ", $data[0]->PERSC_Nombre);
				$nombre_usuario = $nvoNombre[0];

				unset($nvoNombre);

				$nvoNombre = explode(" ", $data[0]->PERSC_ApellidoPaterno);
				$nombre_usuario .= "_".$nvoNombre[0] . "@osa-erp.com";
			}

			$acceso = NULL;
		}

		$access = array();

		if ($acceso != NULL){
			foreach ($acceso as $i => $val){
				$access[$i]["usuario"] = $val->USUA_Codigo;
				$access[$i]["establecimiento"] = $val->COMPP_Codigo;
				$access[$i]["rol"] = $val->ROL_Codigo;
			}
		}

		$info = array(
			"persona" => $data[0]->PERSP_Codigo,
			"nombres" => $data[0]->PERSC_Nombre,
			"apellido_paterno" => $data[0]->PERSC_ApellidoPaterno,
			"apellido_materno" => $data[0]->PERSC_ApellidoMaterno,
			"usuario" => $data[0]->USUA_Codigo,
			"nombre_usuario" => $nombre_usuario,
			"acceso" => $access
		);

		if ($info != NULL)
			$json = array("match" => true, "info" => $info);
		else
			$json = array("match" => false, "info" => NULL);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function credencialVendedor(){
		$vendedor = $this->input->post("vendedor");
		$txtusuario = $this->input->post("usuario");
		$password = $this->input->post("password");

		$passwd = md5($password);

		$datos_usuario = $this->usuario_model->obtener_datosUsuarioLoginVenta($txtusuario, $passwd, $vendedor);
		if (count($datos_usuario) > 0) {
			$datos_usu_com = $this->usuario_compania_model->listar($datos_usuario[0]->USUA_Codigo);
			if (count($datos_usu_com) > 0) {
				$datos_compania = $this->compania_model->obtener($datos_usu_com[0]->COMPP_Codigo);
				$datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);
				$datos_establec = $this->emprestablecimiento_model->obtener($datos_compania[0]->EESTABP_Codigo);
				$usuario = $datos_usuario[0]->USUA_Codigo;
				$userCod = $usuario;
				$obtener_rol = $this->usuario_model->obtener_rolesUsuario($usuario);
				if (count($obtener_rol) > 0)
					$json = array("match" => true, "mensaje" => "");
				else
					$json = array("match" => false, "mensaje" => "Su usuario no tiene acceso a la informacion de esta empresa.");
			}
			else {
				$json = array("match" => false, "mensaje" => "Su usuario no tiene acceso a la informacion de ninguna empresa.");
			}
		}
		else
			$json = array("match" => false, "mensaje" => "Usuario y/o contrasena no validos.");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function getUserPers(){
		$vendedor = $this->input->post("vendedor");
		$usuarioInfo = $this->usuario_model->getUserPers($vendedor);

		if ( $usuarioInfo != NULL) {
			$json = array(
				"match" => true,
				"usuario" => $usuarioInfo[0]->USUA_Codigo,
				"nombre" => $usuarioInfo[0]->USUA_usuario
			);
		}
		else
			$json = array("match" => false);

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function buscar_nombre_usuario(){
		$username = strtolower( $this->input->post("username") );
		$user_info = $this->usuario_model->buscar_nombre_usuario($username);

		if ( $user_info != NULL )
			$json = array( "match" => true );
		else
			$json = array( "match" => false );

		echo json_encode($json);
	}
	##  -> End

  ## FUNCTIONS OLDS

	# Edit:  -> Begin
	public function confirmacion_usuario_anulafb($tDocu, $comprobante){
		$txtUsuario = $this->input->post('txtUsuario');
		$txtClave = $this->input->post('txtClave');
		$txtClave = md5($txtClave);
		$datos_usuario = $this->usuario_model->obtener_datosUsuarioLogin($txtUsuario, $txtClave);
		if (count($datos_usuario) > 0) {
			$datos_usu_com = $this->usuario_compania_model->listar($datos_usuario[0]->USUA_Codigo);

			if (count($datos_usu_com) > 0) {
				$datos_compania = $this->compania_model->obtener($datos_usu_com[0]->COMPP_Codigo);
				$datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);
				$datos_establec = $this->emprestablecimiento_model->obtener($datos_compania[0]->EESTABP_Codigo);
				$usuario = $datos_usuario[0]->USUA_Codigo;
				$userCod = $usuario;
				$obtener_rol = $this->usuario_model->obtener_rolesUsuario($usuario);

				if (count($obtener_rol) > 0) {
					$persona = $datos_usuario[0]->PERSP_Codigo;
					$rol = $obtener_rol[0]->ROL_Codigo;
                                        $msgError = "";

					if ($rol == 1 || $rol == 7005 || $_SESSION['user_name'] == 'ccapasistemas'){
						if ($tDocu == "guiarem"){
							$motivoAnulacion = $this->input->post('motivo');
							if ($motivoAnulacion != NULL && $motivoAnulacion != ''){
								$this->guiarem_model->motivoAnulacion($comprobante, $motivoAnulacion);
								$this->anular_guia($comprobante);
							}
							else{
								$msgError = "<br><div align='center' class='error'>Debe incluir un motivo de anulación.</div>";
								echo $msgError;
							}
						}
						else if ($tDocu == "guiatrans"){
							$this->guiatrans_model->eliminar($comprobante);
						}
						else if ($tDocu == "C" || $tDocu == "D") {
							/** Nota de credito o Debito **/
							$motivoAnulacion = $this->input->post('motivo');
							if ($motivoAnulacion != NULL && $motivoAnulacion != ''){
								$this->notacredito_model->motivoAnulacion($comprobante, $motivoAnulacion);
								$this->notaCredito_eliminar($comprobante);
							}
							else{
								$msgError = "<br><div align='center' class='error'>Debe incluir un motivo de anulación.</div>";
								echo $msgError;
							}
						}
						else{
							$motivoAnulacion = $this->input->post('motivo');
                                               
							if ($motivoAnulacion != NULL && $motivoAnulacion != ''){
								$this->comprobante_model->motivoAnulacion($comprobante, $motivoAnulacion);
								$this->anular_comprobante($comprobante);
							}
							else{
								$msgError = "<br><div align='center' class='error'>Debe incluir un motivo de anulación.</div>";
								echo $msgError;
							}
						}

						$msgError = ($msgError != '') ? '' : "<br><div align='center' class='success'>Anulación exitosa.</div> <span id='refresh'></span>";
						echo $msgError;
					}
					else{
						$msgError = "<br><div align='center' class='error'>Su usuario no posee privilegios de administrador.</div>";
						echo $msgError;
					}
					$this->ventana_confirmacion_usuario2($tDocu, $comprobante);
				}
				else {
					$msgError = "<br><div align='center' class='error'>Su usuario no tiene acceso a la informacion de ninguna empresa.</div>";
					echo $msgError;
					$this->ventana_confirmacion_usuario2($tDocu, $comprobante);
				}
			}
			else {
				$msgError = "<br><div align='center' class='error'>Su usuario no tiene acceso a la informacion de ninguna empresa.</div>";
				echo $msgError;
				$this->ventana_confirmacion_usuario2($tDocu, $comprobante);
			}
		}
		else {
			$msgError = "<br><div align='center' class='error'>Usuario y/o contrasena no valido para esta empresa.</div>";
			echo $msgError;
			$this->ventana_confirmacion_usuario2($tDocu, $comprobante);
		}
	}
	# Edit:  -> End

	##  -> Begin
	public function anular_comprobante($comprobante){
		$datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante);
                $tipo_operacion = $datos_comprobante[0]->CPC_TipoOperacion;
                $total = $datos_comprobante[0]->CPC_total;
                
		if ($tipo_operacion == 'V'){
                    
                    //Eliminar datos de la nube
                    $this->EliminarComprobanteNubefactUsu($comprobante);
                    
                    //Obtengo el cierre del comprobante
                    $filter = new stdClass();
                    $filter->comprobante = $comprobante;
                    $datos_movimiento = $this->movimiento_model->getMovimientos($filter);
                    
                    //Disminuyo el cierre en el monto del comprobante
                    if(count($datos_movimiento) > 0){
                        $cajacierre = $datos_movimiento[0]->CAJCIERRE_Codigo;
                        $rs = $this->cajacierre_model->disminuye_monto_cierre($cajacierre,$total);
                    }
                }
		$this->comprobante_model->eliminar_comprobante($comprobante, $this->user);
	}
	##  -> End

	##  -> Begin
	public function EliminarComprobanteNubefactUsu($codigo, $nota = false){

		if ($nota == false){
			$datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
			$serie = $datos_comprobante[0]->CPC_Serie;
			$numero = $datos_comprobante[0]->CPC_Numero;
			$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
			$motivoAnulacion = explode(' * ',$datos_comprobante[0]->CPC_Observacion);

			if ($datos_comprobante[0]->CPC_TipoDocumento == 'N')
				return NULL;
		}
		else{
			$datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
			$serie = $datos_comprobante[0]->CRED_Serie;
			$numero = $datos_comprobante[0]->CRED_Numero;
			$tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
			$motivoAnulacion = explode(' * ',$datos_comprobante[0]->CRED_Observacion);
		}

		$size = count($motivoAnulacion);
		$motivo = $motivoAnulacion[$size-1];

		switch ($tipo_docu){
			case 'F':
			$tipo_de_comprobante = '1'; /** Facturas => 1 **/
			break;
			case 'B':
			$tipo_de_comprobante = '2'; /** Boletas => 2 **/
			break;
			case 'C':
			$tipo_de_comprobante = '3'; /** Notas de credito => 3 **/
			$tipo_docu = $datos_comprobante[0]->CRED_TipoDocumento_inicio;
			break;
			case 'D':
			$tipo_de_comprobante = '4'; /** Notas de debito => 4 **/
			$tipo_docu = $datos_comprobante[0]->CRED_TipoDocumento_inicio;
			break;

			default:
			$tipo_de_comprobante = '1';
			break;
		}

		$compania = $this->compania;

		$deftoken = $this->tokens->deftoken("$compania");
		$ruta = $deftoken['ruta'];
		$token = $deftoken['token'];

		$serieFac = $serie;

		$data2 = array(
			"operacion"             => "generar_anulacion",
			"tipo_de_comprobante"   => "${tipo_de_comprobante}",
			"serie"                 => "${serieFac}",
			"numero"                => "${numero}",
			"motivo"                => "${motivo}",
			"codigo_unico" => ""
		);

		$data_json = json_encode($data2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ruta);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Token token="'.$token.'"',
				'Content-Type: application/json',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta  = curl_exec($ch);
		$respuesta2 = json_decode($respuesta);

		$data['respuesta'] = "";

		$eliminado = false;
		$filter2= new stdClass();

		if ( !isset($respuesta2->errors) ) {
			$filter2->CPP_codigo = $codigo; 
			$filter2->respuestas_compañia = $this->compania;
			$filter2->respuestas_serie = $serieFac;    
			$filter2->respuestas_tipoDocumento = $tipo_docu;
			$filter2->respuestas_numero = $respuesta2->numero;
			$filter2->respuestas_enlace = $respuesta2->enlace;
			$filter2->respuestas_enlace = $respuesta2->sunat_ticket_numero;
			$filter2->respuestas_aceptadaporsunat = $respuesta2->aceptada_por_sunat;
			$filter2->respuestas_sunatdescription = $respuesta2->sunat_description;
			$filter2->respuestas_sunatnote = $respuesta2->sunat_note;
			$filter2->respuestas_sunatresponsecode = $respuesta2->sunat_responsecode;
			$filter2->respuestas_sunatsoaperror = $respuesta2->sunat_soap_error;
			$filter2->respuestas_enlacepdf = $respuesta2->enlace_del_pdf;
			$filter2->respuestas_enlacexml = $respuesta2->enlace_del_xml;
			$filter2->respuestas_enlacecdr = $respuesta2->enlace_del_cdr;

			$eliminado = true;
		}
		else {
			$filter2->respuestas_compañia = $this->compania;
			$filter2->CPP_codigo = $codigo;
			$filter2->respuestas_serie = $serieFac;    
			$filter2->respuestas_numero = $numero;
			$filter2->respuestas_tipoDocumento = $tipo_de_comprobante;

			$filter2->respuestas_deta = $respuesta2->errors;
			$eliminado = false;
		}

		$this->comprobante_model->insertar_respuestaSunat($filter2);
		/** return $eliminado; **/
	}
	##  -> End

	##  -> Begin
	public function notaCredito_eliminar( $codigo ){
		$datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
		$codNota = $datos_comprobante[0]->CRED_Codigo;
		$comprobante_inicio = $datos_comprobante[0]->CRED_ComproInicio;
		$tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
		$estado = $datos_comprobante[0]->CRED_FlagEstado;
		$tipoNota = $datos_comprobante[0]->DOCUP_Codigo; /** Aqui guardo el tipo / motivo de la nota de credito o debito **/

		$tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
		$docInicio = $datos_comprobante[0]->CRED_TipoDocumento_inicio;

		switch ($tipo_docu) { /** Tipo de comprobante a enviar al facturador **/
			case 'C':
			$tipo_de_comprobante = 3; /** Notas de credito => 3 **/
			break;
			case 'D':
			$tipo_de_comprobante = 4; /** Notas de debito => 4 **/
			break;

			default:
			$tipo_de_comprobante = 3;
			break;
		}

		if ($tipo_de_comprobante == 3 && $tipoNota != 4 && $tipoNota != 5 && $tipoNota != 8 && $tipoNota != 9) /** Si es nota de credito => 3, y los tipos son distintos a descuentos -> mueve el stock **/
			$this->comprobante_model->actualizarStock($comprobante_inicio, $codNota, $tipo_de_comprobante, true);

		if ($tipo_oper == 'V' && $docInicio != "N")
			$this->EliminarComprobanteNubefactUsu($codNota, true); /** true es nota de credito o debito **/

		$this->notacredito_model->eliminar_comprobante($codNota);
	}
	##  -> End

	##  -> Begin
	public function anular_guia($guia){
		$datos_guiarem = $this->guiarem_model->obtener($guia);

    #if ($datos_guiarem[0]->GUIAREMC_TipoOperacion == 'V')
    #    $this->anular_guia_sunat($guia);

		$this->guiarem_model->eliminar($guia, $this->user);
	}
	##  -> End

	##  -> Begin
	public function anular_guia_sunat($codigo){

		$datos_guiarem = $this->guiarem_model->obtener($codigo);

		$serie = $datos_guiarem[0]->GUIAREMC_Serie;
		$numero = $datos_guiarem[0]->GUIAREMC_Numero;
		$motivoAnulacion = explode(' * ',$datos_guiarem[0]->GUIAREMC_Observacion);
		$size = count($motivoAnulacion);
		$motivo = $motivoAnulacion[$size-1];

		$compania = $this->compania;

		$deftoken= $this->tokens->deftoken("$compania");
		$ruta = $deftoken['ruta'];
		$token = $deftoken['token'];

		$tipo_de_comprobante = "9";
		$serieFac = $serie;

		$data2 = array(
			"operacion"             => "generar_anulacion",
			"tipo_de_comprobante"   => "${tipo_de_comprobante}",
			"serie"                 => "${serieFac}",
			"numero"                => "${numero}",
			"motivo"                => "${motivo}",
			"codigo_unico" => ""
		);

		$data_json = json_encode($data2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ruta);
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Token token="'.$token.'"',
				'Content-Type: application/json',
			)
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respuesta  = curl_exec($ch);
		$respuesta2 = json_decode($respuesta);

		$eliminado = false;
		$filter2 = new stdClass();

		if ( !isset($respuesta2->errors) ) {
			$filter2->CPP_codigo = $codigo; 
			$filter2->respuestas_compañia = $this->compania;
			$filter2->respuestas_serie = $serieFac;    
			$filter2->respuestas_tipoDocumento = $tipo_docu;
			$filter2->respuestas_numero = $respuesta2->numero;
			$filter2->respuestas_enlace = $respuesta2->enlace;
			$filter2->respuestas_enlace = $respuesta2->sunat_ticket_numero;
			$filter2->respuestas_aceptadaporsunat = $respuesta2->aceptada_por_sunat;
			$filter2->respuestas_sunatdescription = $respuesta2->sunat_description;
			$filter2->respuestas_sunatnote = $respuesta2->sunat_note;
			$filter2->respuestas_sunatresponsecode = $respuesta2->sunat_responsecode;
			$filter2->respuestas_sunatsoaperror = $respuesta2->sunat_soap_error;
			$filter2->respuestas_enlacepdf = $respuesta2->enlace_del_pdf;
			$filter2->respuestas_enlacexml = $respuesta2->enlace_del_xml;
			$filter2->respuestas_enlacecdr = $respuesta2->enlace_del_cdr;

			$eliminado = true;
		}
		else {
			$filter2->respuestas_compañia = $this->compania;
			$filter2->CPP_codigo = $codigo;
			$filter2->respuestas_serie = $serieFac;    
			$filter2->respuestas_numero = $numero;
			$filter2->respuestas_tipoDocumento = $tipo_de_comprobante;

			$filter2->respuestas_deta = $respuesta2->errors;
			$eliminado = false;
		}

		$this->comprobante_model->insertar_respuestaSunat($filter2);
	}
	##  -> End

	public function ventana_confirmacion_usuario2($serie, $comprobante, $tipo_oper = NULL, $tipo_docu = NULL){
            $rolusuario = $this->session->userdata('rol');
            $lblUsuario = form_label('USUARIO *', 'usuario');
            $lblClave = form_label('CLAVE *', 'clave');
            $txtUsuario = form_input(array('name' => 'txtUsuario', 'id' => 'txtUsuario', 'value' => '', 'maxlength' => '30', 'class' => 'cajaGeneral'));
            $txtClave = form_password(array('name' => 'txtClave', 'id' => 'txtClave', 'value' => '', 'maxlength' => '30', 'class' => 'cajaGeneral', 'onClick' => 'this.value=\'\''));
            $oculto = form_hidden(array('accion' => "", 'codigo' => "", 'modo' => "insertar", 'base_url' => base_url()));
            $tipo_docu = ($tipo_docu == NULL) ? $serie : $tipo_docu;

            $t_oper = array(
                'type'  => 'hidden',
                'name'  => 'txtoper',
                'id'    => 'txtoper',
                'value' => $tipo_oper,
                'class' => 'cajaGeneral'
            );
            $t_doc = array(
                'type'  => 'hidden',
                'name'  => 'txtdocu',
                'id'    => 'txtdocu',
                'value' => $tipo_docu,
                'class' => 'cajaGeneral'
            );

            $txtoper = form_input($t_oper);
            $txtdocu = form_input($t_doc);
            $data['titulo'] = "";
            $data['formulario'] = "frmUsuario";
            $data['nota'] = "";
            $data['img'] = "<img src='" . base_url() . "public/images/icons/anular.jpg' width='100%' height='auto' border='0' title='Ver'>";
            $data['btnAceptar'] = "verificarUsuario";
            $data['campos'] = array($lblUsuario, $lblClave);
            $data['valores'] = array($txtUsuario, $txtClave);
            $data['tiposOTD'] = array($txtoper, $txtdocu);
            $data['lista'] = array();
            $data['action'] = base_url() . "/index.php/seguridad/usuario/confirmacion_usuario_anulafb/" . $serie . "/" . $comprobante;
            $data['oculto'] = $oculto;
            $data['serie'] = $serie;
            $data['comprobante'] = $comprobante;
            $data['rolinicio'] = $rolusuario;

            if ($serie == "" and $comprobante == "") {
                    $data['onload'] = "redireccionar2()";
            } else {
                    $data['onload'] = "javascript:txtUsuario.focus();";
            }
            $this->load->view('seguridad/ventana_confirmacion_usuario', $data);
	}

  //ventana confimacion de usuario
	public function ventana_confirmacion_usuario($datax = '')
	{

		$lblUsuario = form_label('USUARIO *', 'usuario');
		$lblClave = form_label('CLAVE *', 'clave');
		$txtUsuario = form_input(array('name' => 'txtUsuario', 'id' => 'txtUsuario', 'value' => '', 'maxlength' => '30', 'class' => 'cajaGeneral'));
		$txtClave = form_password(array('name' => 'txtClave', 'id' => 'txtClave', 'value' => '', 'maxlength' => '30', 'class' => 'cajaGeneral', 'onClick' => 'this.value=\'\''));
		$oculto = form_hidden(array('accion' => "", 'codigo' => "", 'modo' => "insertar", 'base_url' => base_url()));
		$data['titulo'] = "";
		$data['formulario'] = "frmUsuario";
		$data['img'] = "<img src='" . base_url() . "images/emision.jpg' width='100%' height='auto' border='0' title='Ver'>";
		$data['nota'] = "*Nota: Es necesario la confirmacion de esta operacion";
		$data['btnAceptar'] = "verificarUsuario";
		$data['campos'] = array($lblUsuario, $lblClave);
		$data['valores'] = array($txtUsuario, $txtClave);
		$data['lista'] = array();
		$data['action'] = base_url() . "/index.php/seguridad/usuario/verificar_confirmacion_usuario";
		$data['oculto'] = $oculto;

		if ($datax == '') {
			$data['onload'] = "javascript:txtUsuario.focus();";
		} else {
			$data['onload'] = "confirmar_usuario('valido');";
		}


		$this->load->view('seguridad/ventana_confirmacion_usuario', $data);
	}
}
?>