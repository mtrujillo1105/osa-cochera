<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Directivo extends CI_Controller {

	##  -> Begin
	private $empresa;
	private $compania;
	private $usuario;
	private $url;
	private $view_js = NULL;
	##  -> End

	##  -> Begin
	Public function __construct() {
		parent::__construct();
		$this->load->model('maestros/compania_model');
		$this->load->model('empresa/empresa_model');
		$this->load->model('maestros/persona_model');
		$this->load->model('empresa/directivo_model');
		$this->load->model('maestros/cargo_model');
		$this->load->model('maestros/nacionalidad_model');
		$this->load->model('maestros/tipodocumento_model');
		$this->load->model('maestros/tipocodigo_model');
		$this->load->model('maestros/estadocivil_model');

		$this->load->model('tesoreria/banco_model');

		$this->load->library('lib_props');

		$this->empresa = $this->session->userdata("empresa");
		$this->compania = $this->session->userdata("compania");
		$this->usuario = $this->session->userdata("usuario");
		$this->url = base_url();
		$this->view_js = array(0 => "empresa/directivo.js");
	}
	##  -> End

	##  -> Begin
	public function index() {
		$this->directivos();
	}
	##  -> End

	##  -> Begin
	public function directivos( $j = "" ){
    ## SELECTS
		$data["documentos"] = $this->tipodocumento_model->listar_tipo_documento();
		$data['edo_civil'] = $this->estadocivil_model->listar_estadoCivil();
		$data['nacionalidad'] = $this->nacionalidad_model->listar_nacionalidad();
		$data["cargos"] = $this->cargo_model->getCargos();
		$data["bancos"] = $this->banco_model->listar_banco();

		$data['scripts'] = $this->view_js;

		$data['titulo'] = "BUSCAR EMPLEADO";
		$data['titulo_tabla'] = "RELACIÓN DE EMPLEADOS";
		$this->layout->view('empresa/directivo_index',$data);
	}
	##  -> End
	
	##  -> Begin
	public function datatable_empleado(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "DIREC_CodigoEmpleado",
			++$posDT => "PERSC_NumeroDocIdentidad",
			++$posDT => "PERSC_Nombre",
			++$posDT => "PERSC_ApellidoPaterno",
			++$posDT => "CARGC_Descripcion",
			++$posDT => ""
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

		$filter->codigo = $this->input->post('codigo');
		$filter->documento = $this->input->post('documento');
		$filter->nombre = $this->input->post('nombre');

		$empleadoInfo = $this->directivo_model->getDirectivos($filter);
		$records = array();

		if ($empleadoInfo["records"] != NULL) {
			foreach ($empleadoInfo["records"] as $indice => $valor) {
				$btn_modal = "<button type='button' onclick='editar($valor->DIREP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
				</button>";
				$btn_borrar = "<button type='button' onclick='deshabilitar($valor->DIREP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";
				$btn_ficha = "<button href='".$this->url."index.php/empresa/directivo/ficha_empleado/$valor->DIREP_Codigo' data-fancybox data-type='iframe' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/pdf.png' class='image-size-1b'>
				</button>";
				$btn_clientes = "<button href='".$this->url."index.php/empresa/directivo/relacion_clientes/$valor->DIREP_Codigo' data-fancybox data-type='iframe' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/icon-clientes.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => "$valor->DIREC_CodigoEmpleado",
					++$posDT => "$valor->PERSC_NumeroDocIdentidad",
					++$posDT => "$valor->PERSC_Nombre",
					++$posDT => "$valor->PERSC_ApellidoPaterno $valor->PERSC_ApellidoMaterno",
					++$posDT => "$valor->CARGC_Nombre",
					++$posDT => "$btn_ficha",
					++$posDT => "$btn_modal",
					++$posDT => "$btn_borrar",
					++$posDT => "$btn_clientes"
				);
			}
		}

		$recordsTotal = ( $empleadoInfo["recordsTotal"] != NULL ) ? $empleadoInfo["recordsTotal"] : 0;
		$recordsFilter = $empleadoInfo["recordsFilter"];

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
	public function getEmpleado(){

		$codigo = $this->input->post("empleado");

		$empleadoInfo = $this->directivo_model->getDirectivo($codigo);
		$lista = array();

		if ( $empleadoInfo != NULL ){
			foreach ($empleadoInfo as $indice => $val) {
				$lista = array(
					"tipo_documento" => $val->PERSC_TipoDocIdentidad,
					"numero_documento" => $val->PERSC_NumeroDocIdentidad,
					"numero_ruc" => $val->PERSC_Ruc,
					"nombres" => $val->PERSC_Nombre,
					"apellido_paterno" => $val->PERSC_ApellidoPaterno,
					"apellido_materno" => $val->PERSC_ApellidoMaterno,
					"fecha_nacimiento" => $val->PERSC_FechaNacz,
					"genero" => $val->PERSC_Sexo,
					"edo_civil" => $val->ESTCP_EstadoCivil,
					"nacionalidad" => $val->NACP_Nacionalidad,

					"telefono" => $val->PERSC_Telefono,
					"movil" => $val->PERSC_Movil,
					"fax" => $val->PERSC_Fax,
					"correo" => $val->PERSC_Email,
					"web" => $val->PERSC_Web,
					"direccion" => $val->PERSC_Direccion,
					"direccion" => $val->PERSC_Domicilio,

					"banco" => $val->BANP_Codigo,
					"cta_soles" => $val->PERSC_CtaCteSoles,
					"cta_dolares" => $val->PERSC_CtaCteDolares,

					"empleado" => $val->DIREP_Codigo,
					"cargo" => $val->CARGP_Codigo,
					"numero_contrato" => $val->DIREC_NroContrato,
					"fecha_inicio" => $val->DIREC_FechaInicio,
					"fecha_final" => $val->DIREC_FechaFin,
					"codigo_empleado" => $val->DIREC_CodigoEmpleado
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}
	##  -> End
	
	##  -> Begin	
	public function guardar_registro(){

		$empleado = $this->input->post("empleado");
		$tipo_documento = $this->input->post("tipo_documento");
		$numero_documento = $this->input->post("numero_documento");
		$numero_ruc = $this->input->post("numero_ruc");
		$nombres = strtoupper( $this->input->post("nombres") );
		$apellido_paterno = strtoupper( $this->input->post("apellido_paterno") );
		$apellido_materno = strtoupper( $this->input->post("apellido_materno") );
		$fecha_nacimiento = $this->input->post("fecha_nacimiento");
		$genero = $this->input->post("genero");
		$edo_civil = $this->input->post("edo_civil");
		$nacionalidad = $this->input->post("nacionalidad");
		$direccion = strtoupper( $this->input->post("direccion") );

		$telefono = $this->input->post("telefono");
		$movil = $this->input->post("movil");
		$fax = $this->input->post("fax");
		$correo = $this->input->post("correo");
		$web = $this->input->post("web");

		$banco = $this->input->post("banco");
		$cta_soles = $this->input->post("cta_soles");
		$cta_dolares = $this->input->post("cta_dolares");

		$cargo = $this->input->post("cargo");
		$numero_contrato = $this->input->post("numero_contrato");
		$fecha_inicio = $this->input->post("fecha_inicio");
		$fecha_final = $this->input->post("fecha_final");

    ### PERSONA
		$personaInfo = new stdClass();
		$personaInfo->PERSC_TipoDocIdentidad = $tipo_documento;
		$personaInfo->PERSC_NumeroDocIdentidad = $numero_documento;
		$personaInfo->PERSC_Ruc = $numero_ruc;
		$personaInfo->PERSC_Nombre = strtoupper($nombres);
		$personaInfo->PERSC_ApellidoPaterno = strtoupper($apellido_paterno);
		$personaInfo->PERSC_ApellidoMaterno = strtoupper($apellido_materno);
		$personaInfo->PERSC_FechaNacz = $fecha_nacimiento;
		$personaInfo->PERSC_Sexo = $genero;
		$personaInfo->ESTCP_EstadoCivil = $edo_civil;
		$personaInfo->NACP_Nacionalidad = $nacionalidad;

		$personaInfo->PERSC_Telefono = $telefono;
		$personaInfo->PERSC_Movil = $movil;
		$personaInfo->PERSC_Fax = $fax;
		$personaInfo->PERSC_Email = $correo;
		$personaInfo->PERSC_Web = $web;

		$personaInfo->BANP_Codigo = $banco;
		$personaInfo->PERSC_CtaCteSoles = $cta_soles;
		$personaInfo->PERSC_CtaCteDolares = $cta_dolares;

		$personaInfo->UBIGP_LugarNacimiento = "000000";
		$personaInfo->UBIGP_Domicilio = "000000";
		$personaInfo->PERSC_Direccion = strtoupper($direccion);
		$personaInfo->PERSC_Domicilio = strtoupper($direccion);

    ### DIRECTIVO
		$directivoInfo = new stdClass();
		$directivoInfo->EMPRP_Codigo = $this->empresa;
		$directivoInfo->CARGP_Codigo = $cargo;
		$directivoInfo->TIPCLIP_Codigo = "";
		$directivoInfo->DIREC_Imagen = "";
		$directivoInfo->DIREC_FechaInicio = $fecha_inicio;
		$directivoInfo->DIREC_FechaFin = $fecha_final;
		$directivoInfo->DIREC_NroContrato = $numero_contrato;
		$directivoInfo->DIREC_CodigoEmpleado = $this->generateCodeDirectivo();

		if ($empleado != ""){
			$directivo = $this->directivo_model->actualizar_directivo($empleado, $directivoInfo);
			$persona = $this->directivo_model->actualizar_persona($empleado, $personaInfo);

			if ($directivo != NULL && $persona != NULL)
				$result = true;
			else
				$result = false;
		}
		else{
			$persona = $this->directivo_model->insertar_persona($personaInfo);

			if ($persona != NULL){
				$directivoInfo->PERSP_Codigo = $persona;
				$directivo = $this->directivo_model->insertar_directivo($directivoInfo);

				$result = true;
			}
			else
				$result = false;
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End
	
	##  -> Begin	
	public function deshabilitar_empleado(){

		$empleado = $this->input->post("empleado");

		$directivoInfo = new stdClass();
		$directivoInfo->DIREC_FlagEstado  = "0";

		$personaInfo = new stdClass();
		$personaInfo->PERSC_FlagEstado  = "0";

		if ($empleado != ""){
			$result = $this->directivo_model->actualizar_directivo($empleado, $directivoInfo);
			if ($result){
				$result = $this->directivo_model->actualizar_persona($empleado, $personaInfo);
				$result = $this->directivo_model->deshabilitar_usuario($empleado);
			}
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
  public function generateCodeDirectivo(){
  	$code = $this->directivo_model->getCodeDirectivo();
  	$inicial = substr($_SESSION["nombre_empresa"],0,3);
  	$code = ($code == NULL || $code == '') ? "$inicial-0" : $code;

  	$codeString = substr($code, 0, 4);
  	$codeInt = substr($code, 4);

  	$codeInt++;
  	$nvoCode = $codeString . $this->lib_props->getNumberFormat($codeInt,3);

  	return $nvoCode;
  }
  ##  -> End

  #############################################
  ###### DOCS PDF
  #############################################

	public function ficha_empleado($codigo, $enviarcorreo = false){

            $medidas = "a4"; // a4 - carta

            $this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
            $this->pdf->SetMargins(10, 55, 10); // Cada 10 es 1cm - Como es hoja estoy tratando las medidad en cm -> 
            $this->pdf->SetTitle("FICHA DEL EMPLEADO");
            $this->pdf->SetFont('freesans', '', 8);
            $this->pdf->setPrintHeader(true);

            ### INFORMACION DEL PACIENTE
            $empleadoInfo = $this->directivo_model->getDirectivo($codigo);

            $documento_descripcion  = $empleadoInfo[0]->tipo_documento;
            $documento_numero       = $empleadoInfo[0]->PERSC_NumeroDocIdentidad;
            $documento_ruc          = $empleadoInfo[0]->PERSC_Ruc;
            $nombres                = $empleadoInfo[0]->PERSC_Nombre;
            $apellido_paterno       = $empleadoInfo[0]->PERSC_ApellidoPaterno;
            $apellido_materno       = $empleadoInfo[0]->PERSC_ApellidoMaterno;
            $genero                 = $empleadoInfo[0]->genero;
            $fecha_nacimiento       = $empleadoInfo[0]->PERSC_FechaNacz;
            $edo_civil              = $empleadoInfo[0]->ESTCC_Descripcion;

            $telefono               = $empleadoInfo[0]->PERSC_Telefono;
            $movil                  = $empleadoInfo[0]->PERSC_Movil;
            $fax                    = $empleadoInfo[0]->PERSC_Fax;
            $correo                 = $empleadoInfo[0]->PERSC_Email;
            $direccion              = $empleadoInfo[0]->PERSC_Direccion;

            $nacionalidad           = $empleadoInfo[0]->NACC_Descripcion;
            $web                    = $empleadoInfo[0]->PERSC_Web;

            $banco                  = $empleadoInfo[0]->BANC_Nombre;
            $cta_soles              = $empleadoInfo[0]->PERSC_CtaCteSoles;
            $cta_dolares            = $empleadoInfo[0]->PERSC_CtaCteDolares;

            $cargo                  = $empleadoInfo[0]->CARGC_Nombre;
            $contrato_numero        = $empleadoInfo[0]->DIREC_NroContrato;
            $contrato_inicio        = $empleadoInfo[0]->DIREC_FechaInicio;
            $contrato_fin           = $empleadoInfo[0]->contrato_fin;

                ##### FECHA DE NACIMIENTO
                    $fechaN = new DateTime($fecha_nacimiento); # CREA UN OBJETO CON LA FECHA DE NACIMIENTO
                    $fechaH = new DateTime(date("Y-m-d")); # CREA UN OBJETO CON LA FECHA DE HOY
                    $fechaF = $fechaN->diff($fechaH); # CREA UN OBJETO CON LA DIFERENCIA ENTRE AMBAS FECHAS

                ##### FECHA DE CONTRATO
                    if ($contrato_fin != 'INDEFINIDO'){
                        $contrato_fechaI = new DateTime($contrato_inicio); # CREA UN OBJETO CON LA FECHA DE CONTRATO
                        $contrato_fechaF = new DateTime($contrato_fin); # CREA UN OBJETO CON LA FECHA DE EXPIRACIÓN
                        $contrato_vence = $contrato_fechaI->diff($contrato_fechaF); # CREA UN OBJETO CON LA DIFERENCIA ENTRE AMBAS FECHAS
                        #$contrato_vence->m .= " MES(ES)";

                        $contrato_inicio = mysql_to_human($contrato_inicio);
                        $contrato_fin = mysql_to_human($contrato_fin);

                        $contrato_vence->duracion = ($contrato_vence->y * 12) + $contrato_vence->m;
                        $contrato_vence->duracion .= " MES(ES)";
                      }
                      else{
                      	$contrato_vence = new stdClass();
                      	$contrato_vence->duracion = "";
                      }

                      $companiaInfo = $this->compania_model->obtener($this->compania);
                      $establecimientoInfo = $this->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
                      $empresaInfo =  $this->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

                      $tipoDocumento = "FICHA DEL EMPLEADO";
                      $tipoDocumentoF = "FICHA DEL EMPLEADO";

                      $this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, "", NULL);

                      $this->pdf->AddPage();
                      $this->pdf->SetAutoPageBreak(true, 1);

            ##### INFORMACIÓN DEL EMPLEADO
                      $empleadoHTML = '<table cellpadding="1.5mm" border="0">
                      <tr bgcolor="#E1E1E1">
                      <th style="width: 18.8cm; font-weight:bold">INFORMACIÓN DEL EMPLEADO</th>
                      </tr>
                      <tr>
                      <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">'.$documento_descripcion.'</td>
                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm;">'.$documento_numero.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">R.U.C.</td>
                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm;">'.$documento_ruc.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:4.0cm; font-weight:bold;">NOMBRE Y APELLIDOS:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:7.8cm;">'.$nombres.' '.$apellido_paterno.' '.$apellido_materno.'</td>
                      </tr>
                      <tr>
                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">GENERO:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:2.5cm;">'.$genero.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:4.0cm; font-weight:bold;">FECHA DE NACIMIENTO:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm;">'.mysql_to_human($fecha_nacimiento).'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">EDAD:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:1.5cm;">'.$fechaF->y.' años</td>

                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">EDO. CIVIL:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:3.3cm;">'.$edo_civil.'</td>
                      </tr>
                      <tr>
                      <td style="border-bottom: #EEE 1mm solid; width:3.0cm; font-weight:bold;">NACIONALIDAD:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:5.0cm;">'.$nacionalidad.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">TELEFONO:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:3.0cm;">'.$telefono.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">MOVIL:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:4.3cm;">'.$movil.'</td>
                      </tr>
                      <tr>
                      <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">CORREO:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:6.0cm;">'.$correo.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:1.2cm; font-weight:bold;">FAX:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:2.5cm;">'.$fax.'</td>

                      <td style="border-bottom: #EEE 1mm solid; width:1.1cm; font-weight:bold;">WEB:</td>
                      <td style="border-bottom: #EEE 1mm solid; width:6.0cm;">'.$web.'</td>

                      </tr>
                      <tr>
                      <td style="width:2.0cm; font-weight:bold;">DIRECCIÓN:</td>
                      <td style="width:16.8cm;">'.$direccion.'</td>
                      </tr>
                      </table>';

                $posI = $this->pdf->getY(); # OBTENGO LA POSICION INICIAL
                $this->pdf->writeHTML($empleadoHTML,true,false,true,'');
                $posF = $this->pdf->getY(); # OBTENGO LA POSICION LUEGO DE IMPRIMIR

                $this->pdf->RoundedRect(8, $posI-2, 192, $posF-$posI, 1.50, '1111', ''); # RESTO LA POSICION FINAL MENOS LA INICIAL PARA EL ALTO DEL CUADRO
                $this->pdf->setY($posF + 3);

            ##### CUENTA BANCARIA
                $bancoHTML = '<table cellpadding="1.5mm" border="0">
                <tr bgcolor="#E1E1E1">
                <th style="width: 18.8cm; font-weight:bold">CUENTA BANCARIA</th>
                </tr>
                <tr>
                <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">BANCO:</td>
                <td style="border-bottom: #EEE 1mm solid; width:4.5cm;">'.$banco.'</td>

                <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">CTA SOLES:</td>
                <td style="border-bottom: #EEE 1mm solid; width:4.0cm;">'.$cta_soles.'</td>

                <td style="border-bottom: #EEE 1mm solid; width:2.5cm; font-weight:bold;">CTA DOLARES:</td>
                <td style="border-bottom: #EEE 1mm solid; width:4.3cm;">'.$cta_dolares.'</td>
                </tr>
                </table>';

                $posI = $this->pdf->getY(); # OBTENGO LA POSICION INICIAL
                $this->pdf->writeHTML($bancoHTML,true,false,true,'');
                $posF = $this->pdf->getY(); # OBTENGO LA POSICION LUEGO DE IMPRIMIR

                $this->pdf->RoundedRect(8, $posI-2, 192, $posF-$posI, 1.50, '1111', ''); # RESTO LA POSICION FINAL MENOS LA INICIAL PARA EL ALTO DEL CUADRO
                $this->pdf->setY($posF + 3);

            ##### INFORMACIÓN DEL CONTRATO
                $contratoHTML = '<table cellpadding="1.5mm" border="0">
                <tr bgcolor="#E1E1E1">
                <th style="width: 18.8cm; font-weight:bold">CONTRATO</th>
                </tr>
                <tr>
                <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">CARGO:</td>
                <td style="border-bottom: #EEE 1mm solid; width:7.5cm;">'.$cargo.'</td>

                <td style="border-bottom: #EEE 1mm solid; width:4.0cm; font-weight:bold;">NÚMERO DE CONTRATO:</td>
                <td style="border-bottom: #EEE 1mm solid; width:5.8cm;">'.$contrato_numero.'</td>
                </tr>
                <tr>
                <td style="border-bottom: #EEE 1mm solid; width:1.5cm; font-weight:bold;">INICIO:</td>
                <td style="border-bottom: #EEE 1mm solid; width:2.0cm;">'.$contrato_inicio.'</td>

                <td style="border-bottom: #EEE 1mm solid; width:2.5cm; font-weight:bold;">VENCIMIENTO:</td>
                <td style="border-bottom: #EEE 1mm solid; width:3.0cm;">'.$contrato_fin.'</td>

                <td style="border-bottom: #EEE 1mm solid; width:2.0cm; font-weight:bold;">DURACIÓN:</td>
                <td style="border-bottom: #EEE 1mm solid; width:7.8cm;">'.$contrato_vence->duracion.'</td>
                </tr>
                </table>';

                $posI = $this->pdf->getY(); # OBTENGO LA POSICION INICIAL
                $this->pdf->writeHTML($contratoHTML,true,false,true,'');
                $posF = $this->pdf->getY(); # OBTENGO LA POSICION LUEGO DE IMPRIMIR

                $this->pdf->RoundedRect(8, $posI-2, 192, $posF-$posI, 1.50, '1111', ''); # RESTO LA POSICION FINAL MENOS LA INICIAL PARA EL ALTO DEL CUADRO



                if ($enviarcorreo == false){

                	$this->pdf->Output("ficha.pdf", 'I');

                }
                else{
                                                                
                	return $this->pdf->Output("ficha.pdf", 'S');
                }
              }

              public function relacion_clientes($directivo, $flagPdf = 1, $enviarcorreo = false){

            $medidas = "a4";
            $this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
            $this->pdf->SetMargins(10, 55, 10);
            $this->pdf->SetTitle("RELACIÓN DE CLIENTES");
            $this->pdf->SetFont('freesans', '', 8);
            $this->pdf->setPrintHeader(true);

            $companiaInfo = $this->compania_model->obtener($this->compania);
            $establecimientoInfo = $this->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
            $empresaInfo =  $this->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );
            
            $tipoDocumento = "RELACIÓN DE<br>CLIENTES";
            $tipoDocumentoF = "RELACIÓN DE<br>CLIENTES";
            
            $this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, "", NULL);

            $this->pdf->AddPage();
            $this->pdf->SetAutoPageBreak(true, 1);
            
            /* Listado de detalles */
            $listado = $this->directivo_model->relacion_clientes($directivo);
            $deta = "";
            $j = 1;
            foreach ($listado as $indice => $valor) {
            	$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

            	$deta = $deta. '
            	<tr bgcolor="'.$bgcolor.'">
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CLIC_CodigoUsuario.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->nombre_cliente.$valor->razon_social.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->total_documentos.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->total_ventas.'</td>
            	</tr>';
            	$j++;
            }

            $detalleHTML = '<table style="font-size:7.5pt;" cellpadding="0.1cm" border="0">
            <tr>
            <td style="width:2.5cm; font-style:normal; font-weight:bold;">VENDEDOR:</td>
            <td style="width:15cm; text-indent:0.1cm; text-align:justification">'.$listado[0]->PERSC_Nombre.' - '.$listado[0]->PERSC_ApellidoPaterno.' - '.$listado[0]->PERSC_ApellidoMaterno.'</td>
            </tr>
            </table>';

            $this->pdf->writeHTML($detalleHTML,true,false,true,'');

            $productoHTML = '
            <table cellpadding="0.05cm">
            <tr style="font-size:8pt;">
            <th colspan="8" style="font-style:normal; font-weight:bold; text-align:left; border-bottom: 1px #000 solid;">LISTA DE CLIENTES</th>
            </tr>
            <tr bgcolor="#F1F1F1" style="font-size:7.5pt;">
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:03.0cm;">ID CLIENTE</th>

            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:10.0cm;">RUC/DNI - RAZÓN SOCIAL</th>

            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:right; width:3.0cm;">CANTIDAD DOC.</th>

            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:right; width:3.0cm;">TOTAL VENTAS</th>
            </tr>
            '.$deta.'
            </table>';
            $this->pdf->writeHTML($productoHTML,true,false,true,'');

            $nameFile = "Clientes por vendedor.pdf";

            if ($enviarcorreo == false)
            	$this->pdf->Output($nameFile, 'I');
            else
            	return $this->pdf->Output($nameFile, 'S');
          }


    ###########################
    ###### FUNCTIONS OLDS
    ###########################

          public function directivos_old($j = 0) {
          	$data['codigo'] = "";
          	$data['numdoc'] = "";
          	$data['personacod'] = "";
          	$data['nombre'] = "";
          	$data['cargo'] = "";
          	$data['fecini'] = "";
          	$data['fecfin'] = "";
          	$data['contrato'] = "";
          	$data['titulo_tabla'] = "RELACIÓN DE EMPLEADOS";
          	$data['registros'] = count($this->directivo_model->lista_vendedores2());
          	$data['action'] = base_url() . "index.php/empresa/directivo/buscar_directivos";
          	$conf['base_url'] = site_url('empresa/directivo/directivos/');
          	$conf['total_rows'] = $data['registros'];
          	$conf['per_page'] = 50;
          	$conf['num_links'] = 3;
          	$conf['next_link'] = "&gt;";
          	$conf['prev_link'] = "&lt;";
          	$conf['first_link'] = "&lt;&lt;";
          	$conf['last_link'] = "&gt;&gt;";
          	$conf['uri_segment'] = 4;
          	$data['cbo_empresa'] = $this->seleccionar_empresa("1");
          	$this->pagination->initialize($conf);
          	$data['paginacion'] = $this->pagination->create_links();
          	$listado_directivos = $this->directivo_model->lista_vendedores2();
          	$item = $j + 1;
          	$lista = array();
          	if (count($listado_directivos) > 0) {
          		foreach ($listado_directivos as $indice => $valor) {
          			$codigo = $valor->DIREP_Codigo;
          			$numdoc = $valor->dni;
          			$nombres = $valor->nombre . " " . $valor->paterno . " " . $valor->materno;
                    $empresa = $valor->empresa;//empresa
                    $cargo = $valor->cargo;
                    $inicio = mysql_to_human($valor->Inicio);
                    $fin = mysql_to_human($valor->Fin);
                    $contrato = $valor->Nro_Contrato;
                    $editar = "<a href='javascript:;' onclick='editar_directivo(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $ver = "<a href='javascript:;' onclick='ver_directivo(" . $codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver'></a>";
                    $eliminar = ""; #"<a href='javascript:;' onclick='eliminar_directivo(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                    $relacion_clientes = "<a href='javascript:;' onclick='clientes_directivo($codigo)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Eliminar'></a>";
                    $lista[] = array($item, $numdoc, $nombres, $empresa, $cargo, $contrato, $inicio, $fin, $editar, $ver, $eliminar, $relacion_clientes, $valor->DIREC_CodigoEmpleado);
                    $item++;
                  }
                }
                $data['lista'] = $lista;
                $this->layout->view("empresa/directivo_index", $data);
              }

              public function buscar_directivos($j = '0') {
              	$numdoc = $this->input->post('txtNumDoc');
              	$nombre = $this->input->post('txtNombre');
              	$codigoEmpleado = $this->input->post('txtCodigoEmpleado');

              	$empresa = $this->input->post('cboCompania');
              	$filter = new stdClass();
              	$filter->numdoc = $numdoc;
              	$filter->nombre = $nombre;
              	$filter->empresa = $empresa;
              	$filter->codigoEmpleado = $codigoEmpleado;

              	$data['numdoc'] = $numdoc;
              	$data['nombre'] = $nombre;
              	$data['cbo_empresa'] = $this->seleccionar_empresa($empresa);
              	$data['titulo_tabla'] = "RESULTADO DE BÚSQUEDA DE EMPLEADOS";

              	$data['registros'] = count($this->directivo_model->buscar_directivo2($filter));
              	$data['action'] = base_url() . "index.php/empresa/directivo/buscar_directivos";
              	$conf['base_url'] = site_url('empresa/directivo/buscar_directivos/');
              	$conf['total_rows'] = $data['registros'];
              	$conf['per_page'] = 50;
              	$conf['num_links'] = 3;
              	$conf['next_link'] = "&gt;";
              	$conf['prev_link'] = "&lt;";
              	$conf['first_link'] = "&lt;&lt;";
              	$conf['last_link'] = "&gt;&gt;";
              	$conf['uri_segment'] = 4;
              	$this->pagination->initialize($conf);
              	$data['paginacion'] = $this->pagination->create_links();
              	$listado_directivos = $this->directivo_model->buscar_directivo2($filter, $conf['per_page'], $j);
              	$item = $j + 1;
              	$lista = array();
              	if (count($listado_directivos) > 0) {
              		foreach ($listado_directivos as $indice => $valor) {
              			$codigo = $valor->DIREP_Codigo;
              			$numdoc = $valor->dni;
              			$nombres = $valor->nombre . " " . $valor->paterno . " " . $valor->materno;
              			$empresa = $valor->empresa;
              			$cargo = $valor->cargo;
              			$inicio = $valor->Inicio;
              			$fin = $valor->Fin;
              			$contrato = $valor->Nro_Contrato;
              			$editar = "<a href='#' onclick='editar_directivo(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
              			$ver = "<a href='#' onclick='ver_directivo(" . $codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $eliminar = ""; #"<a href='#' onclick='eliminar_directivo(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $relacion_clientes = "<a href='javascript:;' onclick='clientes_directivo($codigo)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Eliminar'></a>";
                    $lista[] = array($item, $numdoc, $nombres, $empresa, $cargo, $contrato, $inicio, $fin, $editar, $ver, $eliminar, $relacion_clientes, $valor->DIREC_CodigoEmpleado);
                    $item++;
                  }
                }
                $data['lista'] = $lista;
                $this->layout->view("empresa/directivo_index", $data);
              }


          public function eliminar_directivo() {
          	$directivo = $this->input->post('directivo');
          	$this->directivo_model->eliminar_directivo($directivo);
          }

          public function seleccionar_empresa($indDefault = '') {
          	$array_dist = $this->empresa_model->listar_empresas();

          	$arreglo = array();
          	if (count($array_dist) > 0) {
          		foreach ($array_dist as $indice => $valor) {
          			$indice1 = $valor->EMPRP_Codigo;
          			$valor1 = $valor->EMPRC_RazonSocial;
          			$arreglo[$indice1] = $valor1;
          		}
          	}
          	$resultado = $this->html->optionHTML($arreglo, $indDefault, array('0', '::Seleccione::'));
          	return $resultado;
          }

          
          public function seleccionar_estadoCivil($indSel) {
          	$array_dist = $this->estadocivil_model->listar_estadoCivil();
          	$arreglo = array();
          	foreach ($array_dist as $indice => $valor) {
          		$indice1 = $valor->ESTCP_Codigo;
          		$valor1 = $valor->ESTCC_Descripcion;
          		$arreglo[$indice1] = $valor1;
          	}
          	$resultado = $this->html->optionHTML($arreglo, $indSel, array('0', '::Seleccione::'));
          	return $resultado;
          }

          public function seleccionar_nacionalidad($indSel = '') {
          	$array_dist = $this->nacionalidad_model->listar_nacionalidad();
          	$arreglo = array();
          	foreach ($array_dist as $indice => $valor) {
          		$indice1 = $valor->NACP_Codigo;
          		$valor1 = $valor->NACC_Descripcion;
          		$arreglo[$indice1] = $valor1;
          	}
          	$resultado = $this->html->optionHTML($arreglo, $indSel, array('', '::Seleccione::'));
          	return $resultado;
          }

          public function seleccionar_tipodocumento($indDefault = '') {
          	$array_dist = $this->tipodocumento_model->listar_tipo_documento();
          	$arreglo = array();
          	if (count($array_dist) > 0) {
          		foreach ($array_dist as $indice => $valor) {
          			$indice1 = $valor->TIPDOCP_Codigo;
          			$valor1 = $valor->TIPOCC_Inciales;
          			$arreglo[$indice1] = $valor1;
          		}
          	}
          	$resultado = $this->html->optionHTML($arreglo, $indDefault, array('0', '::Seleccione::'));
          	return $resultado;
          }

          public function seleccionar_tipocodigo($indDefault = '') {
          	$array_dist = $this->tipocodigo_model->listar_tipo_codigo();
          	$arreglo = array();
          	if (count($array_dist) > 0) {
          		foreach ($array_dist as $indice => $valor) {
          			$indice1 = $valor->TIPCOD_Codigo;
          			$valor1 = $valor->TIPCOD_Inciales;
          			$arreglo[$indice1] = $valor1;
          		}
          	}
          	$resultado = $this->html->optionHTML($arreglo, $indDefault, array('0', '::Seleccione::'));
          	return $resultado;
          }

          public function JSON_buscar_directivo($numdoc) {
          	$datos_empresa = $this->empresa_model->obtener_datosEmpresa2($numdoc);
          	$datos_persona = $this->persona_model->obtener_datosPersona2($numdoc);
          	$resultado = '[{"CLIP_Codigo":"0","EMPRC_Ruc":"","EMPRC_RazonSocial":""}]';
          	if (count($datos_empresa) > 0) {
          		$empresa = $datos_empresa[0]->EMPRP_Codigo;
          		$razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
          		$datosCliente = $this->cliente_model->obtener_datosCliente2($empresa);
          		if (count($datosCliente) > 0) {
          			$cliente = $datosCliente[0]->CLIP_Codigo;
          			$resultado = '[{"CLIP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $razon_social . '"}]';
          		}
          	} elseif (count($datos_persona) > 0) {
          		$persona = $datos_persona[0]->PERSP_Codigo;
          		$nombres = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
          		$datosCliente = $this->cliente_model->obtener_datosCliente3($persona);
          		if (count($datosCliente) > 0) {
          			$cliente = $datosCliente[0]->CLIP_Codigo;
          			$resultado = '[{"CLIP_Codigo":"' . $cliente . '","EMPRC_Ruc":"' . $numdoc . '","EMPRC_RazonSocial":"' . $nombres . '"}]';
          		}
          	}
          	echo $resultado;
          }

          public function registro_directivo_pdf($documento='', $nombre='', $empresa='')
          {

          	$this->load->library('cezpdf');
          	$this->load->helper('pdf_helper');
            //prep_pdf();
          	$this->cezpdf = new Cezpdf('a4');
          	$datacreator = array(
          		'Title' => 'Estadillo de ',
          		'Name' => 'Estadillo de ',
          		'Author' => 'Vicente Producciones',
          		'Subject' => 'PDF con Tablas',
          		'Creator' => 'info@vicenteproducciones.com',
          		'Producer' => 'http://www.vicenteproducciones.com'
          	);

          	$this->cezpdf->addInfo($datacreator);
          	$this->cezpdf->selectFont(APPPATH . 'libraries/fonts/Helvetica.afm');
          	$delta = 20;



          	$this->cezpdf->ezText('', '', array("leading" => 35));
          	$this->cezpdf->ezText('<b>RELACION DE EMPLEADOS</b>', 14, array("leading" => 0, 'left' => 185));
          	$this->cezpdf->ezText('', '', array("leading" => 10));


          	/* Datos del cliente */

          	$db_data = array();


          	$listado_productos = $this->directivo_model->listar_directivo_pdf($documento,$nombre,$empresa);

          	if (count($listado_productos) > 0) {
          		foreach ($listado_productos as $indice => $valor) {
          			$dni = $valor->dni;
          			$nombre = $valor->nombre." ".$valor->paterno." ".$valor->materno;
          			$empresa = $valor->empresa;
          			$cargo = $valor->cargo;
          			$inicio = $valor->Inicio;
          			$fin = $valor->Fin;
          			$contrato = $valor->Nro_Contrato;


          			$db_data[] = array(
          				'cols1' => $indice + 1,
          				'cols2' => $dni,
          				'cols3' => $nombre,
          				'cols4' => $empresa,
          				'cols5' => $cargo,
          				'cols6' => $contrato,
          				'cols7' => $fin,
          				'cols8' => $inicio,
          			);
          		}
          	}




          	$col_names = array(
          		'cols1' => '<b>ITEM</b>',
          		'cols2' => '<b>DNI</b>',
          		'cols3' => '<b>NOMBRE</b>',
          		'cols4' => '<b>EMPRESA</b>',
          		'cols5' => '<b>CARGO</b>',
          		'cols6' => '<b>CONTRATO</b>',
          		'cols7' => '<b>INICIO</b>',
          		'cols8' => '<b>FIN</b>'
          	);

          	$this->cezpdf->ezTable($db_data, $col_names, '', array(
          		'width' => 525,
          		'showLines' => 1,
          		'shaded' => 1,
          		'showHeadings' => 1,
          		'xPos' => 'center',
          		'fontSize' => 8,
          		'cols' => array(
          			'cols1' => array('width' => 30, 'justification' => 'center'),
          			'cols2' => array('width' => 50, 'justification' => 'center'),
          			'cols3' => array('width' => 120, 'justification' => 'center'),
          			'cols4' => array('width' => 100, 'justification' => 'center'),
          			'cols5' => array('width' => 70, 'justification' => 'center'),
          			'cols6' => array('width' => 60, 'justification' => 'center'),
          			'cols7' => array('width' => 60, 'justification' => 'center'), 
          			'cols8' => array('width' => 60, 'justification' => 'center')
          		)
          	));


          	$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');

          	ob_end_clean();

          	$this->cezpdf->ezStream($cabecera);
          }
        }
        ?>