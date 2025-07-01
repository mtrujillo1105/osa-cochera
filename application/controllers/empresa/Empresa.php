<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Empresa extends CI_Controller{

	##  -> Begin - El array somevar es reemplazado por atributos
	private $empresa;
	private $compania;
	private $url;
	private $view_js = NULL;
  ##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->model('empresa/empresa_model'); 
		$this->load->model('maestros/tipoestablecimiento_model');
		$this->load->model('maestros/emprestablecimiento_model');
		$this->load->model('maestros/ubigeo_model');
		$this->load->model('maestros/tipocodigo_model');
		$this->load->model('maestros/comercial_model');
		$this->load->model('empresa/cuentaempresa_model');

		$this->empresa = $this->session->userdata("empresa");
		$this->compania = $this->session->userdata("compania");
		$this->url = base_url();
		$this->view_js = array(0 => "empresa/empresa.js");
	}

	##  -> Begin
	public function datatable_sucursales(){

		$posDT = -1;
		$columnas = array(
			++$posDT => "EESTABC_Descripcion",
			++$posDT => "TESTC_Descripcion",
			++$posDT => "EESTAC_Direccion",
			++$posDT => "ubigeo_descripcion"
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

		$filter->empresa = $this->input->post('empresa');

		$establecimientoInfo = $this->emprestablecimiento_model->getEstablecimientos($filter);

		$records = array();
		if ( $establecimientoInfo["records"] != NULL) {
			foreach ($establecimientoInfo["records"] as $indice => $valor) {
				$btn_editar = ($valor->EESTABC_FlagTipo == 1) ? "" : "<button type='button' onclick='editar_sucursal($valor->EESTABP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_deshabilitar = ($valor->EESTABC_FlagTipo == 1) ? "" : "<button type='button' onclick='deshabilitar_sucursal($valor->EESTABP_Codigo)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->EESTABC_Descripcion,
					++$posDT => $valor->TESTC_Descripcion,
					++$posDT => $valor->EESTAC_Direccion,
					++$posDT => $valor->ubigeo_descripcion,
					++$posDT => $btn_editar,
					++$posDT => $btn_deshabilitar
				);
			}
		}

		$recordsTotal = ( $establecimientoInfo["recordsTotal"] != NULL ) ? $establecimientoInfo["recordsTotal"] : 0;
		$recordsFilter = $establecimientoInfo["recordsFilter"];

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
	public function getEstablecimiento(){

		$sucursal = $this->input->post("sucursal");

		$sucursalInfo = $this->emprestablecimiento_model->getEstablecimiento($sucursal);
		$lista = array();

		if ( $sucursalInfo != NULL ){
			foreach ($sucursalInfo as $i => $val) {
				$lista = array(
					"sucursal" => $val->EESTABP_Codigo,
					"nombre" => $val->EESTABC_Descripcion,
					"tipo" => $val->TESTP_Codigo,
					"direccion" => $val->EESTAC_Direccion,
					"departamento" => substr($val->UBIGP_Codigo, 0, 2),
					"provincia" => substr($val->UBIGP_Codigo, 2, 2),
					"distrito" => substr($val->UBIGP_Codigo, 4, 2),
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
	public function guardar_sucursal(){

		$sucursal = $this->input->post("sucursal");
		$sucursal_empresa = $this->input->post("sucursal_empresa");
		$nombre = $this->input->post("establecimiento_nombre");
		$tipo = $this->input->post("establecimiento_tipo");
		$direccion = $this->input->post("establecimiento_direccion");
		$departamento = strtoupper( $this->input->post("establecimiento_departamento") );
		$provincia = strtoupper( $this->input->post("establecimiento_provincia") );
		$distrito = strtoupper( $this->input->post("establecimiento_distrito") );

		$sucursalInfo = new stdClass();
		$sucursalInfo->TESTP_Codigo = $tipo;
		$sucursalInfo->UBIGP_Codigo = $departamento.$provincia.$distrito;
		$sucursalInfo->EESTABC_Descripcion = strtoupper($nombre);
		$sucursalInfo->EESTAC_Direccion = strtoupper($direccion);
		$sucursalInfo->EESTABC_FlagTipo = "0";
		$sucursalInfo->EESTABC_FlagEstado = "1";

		if ($sucursal != ""){
			$sucursalInfo->EESTABC_FechaModificacion = date("Y-m-d H:i:s");
			$sucursal = $this->emprestablecimiento_model->actualizar_establecimiento($sucursal, $sucursalInfo);    
		}
		else{
			if ($sucursal_empresa != ""){
				$sucursalInfo->EMPRP_Codigo = $sucursal_empresa;

				$sucursalInfo->EESTABC_FechaRegistro = date("Y-m-d H:i:s");
				$sucursal = $this->emprestablecimiento_model->insertar_establecimiento($sucursalInfo);
			}
			else
				$sucursal = false;

		}

		if ($sucursal)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_sucursal(){

		$sucursal = $this->input->post("sucursal");

		$sucursalInfo = new stdClass();
		$sucursalInfo->EESTABC_FlagEstado = "0";
		$sucursalInfo->EESTABC_FechaModificacion = date("Y-m-d H:i:s");

		if ($sucursal != "")
			$sucursal = $this->emprestablecimiento_model->actualizar_establecimiento($sucursal, $sucursalInfo);

		if ($sucursal)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function datatable_contactos(){

		$posDT = -1;
		$columnas = array(
			++$posDT => "ECONC_Descripcion",
                        ++$posDT => "ECONC_Area",
                    	++$posDT => "ECONC_Cargo",
                        ++$posDT => "ECONC_Telefono",
                        ++$posDT => "ECONC_Email",
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

		$filter->empresa = $this->input->post('empresa');
		$filter->persona = $this->input->post('persona');

		$contactoInfo = $this->empresa_model->getContactos($filter);

		$records = array();
		if ( $contactoInfo["records"] != NULL) {
			foreach ($contactoInfo["records"] as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar_contacto($valor->ECONP_Contacto)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
				</button>";

				$btn_deshabilitar = "<button type='button' onclick='deshabilitar_contacto($valor->ECONP_Contacto)' class='btn btn-default'>
				<img src='".$this->url."public/images/icons/documento-delete.png' class='image-size-1b'>
				</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->ECONC_Descripcion,                                    
                                        ++$posDT => $valor->ECONC_Area,
					++$posDT => $valor->ECONC_Cargo,                                    
                                        ++$posDT => $valor->ECONC_Telefono, 
                                    	++$posDT => $valor->ECONC_Email,
					++$posDT => $btn_editar,
					++$posDT => $btn_deshabilitar
				);
			}
		}

		$recordsTotal = ( $contactoInfo["recordsTotal"] != NULL ) ? $contactoInfo["recordsTotal"] : 0;
		$recordsFilter = $contactoInfo["recordsFilter"];

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
	public function getContacto(){
            $contacto = $this->input->post("contacto");
            $contactoInfo["records"] = $this->empresa_model->getContacto($contacto);
            if ($contactoInfo["records"] != NULL){
                $contactos = $contactoInfo["records"];
                foreach ($contactos as $key => $val)    
                    $info = array(
                        "contacto" => $val->ECONP_Contacto,
                        "empresa" => $val->EMPRP_Codigo,
                        "persona" => $val->PERSP_Contacto,
                        "nombre" => $val->ECONC_Descripcion,
                        "area" => $val->ECONC_Area,
                        "cargo" => $val->ECONC_Cargo,
                        "telefono" => $val->ECONC_Telefono,
                        "movil" => $val->ECONC_Movil,
                        "fax" => $val->ECONC_Fax,
                        "correo" => $val->ECONC_Email,
                        "placa"  => $val->ECONC_Placa,
                        "tarifa" => $val->TARIFP_Codigo,
                        "fechai" => $val->ECONC_FechaIngreso,
                        "monto"  => $val->ECONC_Monto,
                        "numerodoc" => $val->ECONC_NumeroDocIdentidad
                    );
                $json = array("match" => true, "info" => $info);
            }
            else
                $json = array("match" => true, "info" => NULL);
            echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function guardar_contacto(){

            $contacto = $this->input->post("contacto");
            $empresa = $this->input->post("contacto_empresa");
            $persona = $this->input->post("contacto_persona");
            $nombre = strtoupper( $this->input->post("contacto_nombre") );
            $area = strtoupper( $this->input->post("contacto_area") );
            $cargo = strtoupper( $this->input->post("contacto_cargo") );
            $telefono = $this->input->post("contacto_telefono");
            $movil = $this->input->post("contacto_movil");
            $fax = $this->input->post("contacto_fax");
            $correo = $this->input->post("contacto_correo");
            $placa  = $this->input->post("contacto_placa");
            $tarifa = $this->input->post("contacto_tarifa");
            $fechai = $this->input->post("contacto_fechai");
            $monto  = $this->input->post("contacto_monto");
            $numerodoc  = $this->input->post("contacto_numerodoc");
            
            $filter = new stdClass();
            $filter->EMPRP_Codigo = ($empresa == "") ? 0 : $empresa;
            $filter->PERSP_Contacto = ($persona == "") ? 0 : $persona;
            $filter->ECONC_Descripcion = $nombre;
            $filter->ECONC_Area = $area;
            $filter->ECONC_Cargo = $cargo;
            $filter->ECONC_Telefono = $telefono;
            $filter->ECONC_Movil = $movil;
            $filter->ECONC_Fax = $fax;
            $filter->ECONC_Email = $correo;
            # 0 : PERSONA | 1 : EMPRESA
            $filter->ECONC_TipoContacto = "0";
            $filter->ECONC_FlagEstado   = "1";
            $filter->ECONC_Placa        = strtoupper($placa);
            $filter->TARIFP_Codigo      = $tarifa;
            $filter->ECONC_FechaIngreso = $fechai;
            $filter->ECONC_Monto        = $monto;
            $filter->ECONC_NumeroDocIdentidad = $numerodoc;
            if ($contacto != ""){
                $filter->ECONC_FechaModificacion = date("Y-m-d H:i:s");
                $cta = $this->empresa_model->actualizar_contacto($contacto, $filter);    
            }
            else{
                if ($empresa != "" || $persona != ""){
                    $filter->ECONC_FechaRegistro = date("Y-m-d H:i:s");
                    $contacto = $this->empresa_model->insertar_contacto($filter);
                }
                else
                    $contacto = false;
            }
            if ($contacto)
                $json = array("result" => "success");
            else
                $json = array("result" => "error");
            echo json_encode($json);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_contacto(){

		$contacto = $this->input->post("contacto");

		$filter = new stdClass();
		$filter->ECONC_FlagEstado = "0";
		$filter->ECONC_FechaModificacion = date("Y-m-d H:i:s");

		if ($contacto != "")
			$contacto = $this->empresa_model->actualizar_contacto($contacto, $filter);

		if ($contacto)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
	##  -> End

	##  -> Begin
  public function index(){
		$this->empresas();    
	}
	##  -> End

	##  -> Begin
	public function empresas($j=0){
		$this->load->model('tesoreria/banco_model');
		$this->load->model('maestros/moneda_model');

		$data["titulo_busqueda"] = "BUSCAR EMPRESAS";
		$data["titulo_tabla"] = "RELACION DE EMPRESAS";
		$data["documentosJuridico"] = $this->tipocodigo_model->listar_tipo_codigo();
		$data["bancos"] = $this->banco_model->listar_banco();
		$data["monedas"] = $this->moneda_model->listar();
		$data["lista_empresas"] = $this->empresa_model->listar_empresas();
		$data["sector_comercial"] = $this->comercial_model->getComercials();
		$data["tipo_establecimiento"] = $this->tipoestablecimiento_model->getTipoEstablecimientos();
		$data["departamentos"] = $this->ubigeo_model->listar_departamentos();
		$data["provincias"] = $this->ubigeo_model->getProvincias("15");
		$data["distritos"] = $this->ubigeo_model->getDistritos("15","01");
		$data['scripts'] = $this->view_js;
		$this->layout->view("empresa/empresa_index",$data);
	}
	##  -> End

	##  -> Begin
	public function dt_empresas(){
		$posDT = -1;
		$columnas = array(
			++$posDT => "EMPRC_Ruc",
			++$posDT => "EMPRC_RazonSocial"
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

		$filter->ruc = $this->input->post("documento");
		$filter->razon_social = $this->input->post("nombre");
		$empresaInfo = $this->empresa_model->getEmpresas($filter);

		$records = array();
		if ( $empresaInfo["records"] != NULL) {
			foreach ($empresaInfo["records"] as $indice => $valor) {
				$btn_editar = "<button type='button' onclick='editar_empresa($valor->EMPRP_Codigo)' class='btn btn-default'>
												<img src='".$this->url."public/images/icons/modificar.png' class='image-size-1b'>
											</button>";

				$btn_bancos = "<button type='button' onclick='modal_CtasBancarias(\"$valor->EMPRP_Codigo\", \"$valor->EMPRC_Ruc - $valor->EMPRC_RazonSocial\")' class='btn btn-default' title='Bancos'>
												<img src='".$this->url."public/images/icons/banco.png' class='image-size-1b'>
											</button>";

				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->EMPRC_Ruc,
					++$posDT => $valor->EMPRC_RazonSocial,
					++$posDT => $valor->EMPRC_Direccion,
					++$posDT => "$valor->EMPRC_Telefono / $valor->EMPRC_Movil / $valor->EMPRC_Fax",
					++$posDT => $btn_editar,
					++$posDT => $btn_bancos
				);
			}
		}

		$recordsTotal = ( $empresaInfo["recordsTotal"] != NULL ) ? $empresaInfo["recordsTotal"] : 0;
		$recordsFilter = $empresaInfo["recordsFilter"];

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
	public function getEmpresa(){

		$empresa = $this->input->post("empresa");

		$empresaInfo = $this->empresa_model->getEmpresa($empresa);
		$lista = array();

		if ( $empresaInfo != NULL ){
			foreach ($empresaInfo as $i => $val) {
				$lista = array(
					"empresa" => $val->EMPRP_Codigo,
					"tipo_documento" => $val->TIPCOD_Codigo,
					"numero_documento" => $val->EMPRC_Ruc,
					"razon_social" => $val->EMPRC_RazonSocial,
					"direccion" => $val->EMPRC_Direccion,

					"departamento" => substr($val->ubigeo, 0, 2),
					"provincia" => substr($val->ubigeo, 2, 2),
					"distrito" => substr($val->ubigeo, 4, 2),

					"sector_comercial" => $val->SECCOMP_Codigo,

					"telefono" => $val->EMPRC_Telefono,
					"movil" => $val->EMPRC_Movil,
					"fax" => $val->EMPRC_Fax,
					"correo" => $val->EMPRC_Email,
					"web" => $val->EMPRC_Web
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

		$empresa = $this->input->post("empresa");

		$tipo_documento = $this->input->post("tipo_documento");
		$numero_documento = $this->input->post("numero_documento");
		$razon_social = strtoupper( $this->input->post("razon_social"));

		$direccion = strtoupper( $this->input->post("direccion") );
		$departamento = $this->input->post("departamento");
		$provincia = $this->input->post("provincia");
		$distrito = $this->input->post("distrito");
		$ubigeo = $departamento.$provincia.$distrito;

		$sector_comercial = $this->input->post("sector_comercial");

		$telefono = $this->input->post("telefono");
		$movil = $this->input->post("movil");
		$fax = $this->input->post("fax");
		$correo = $this->input->post("correo");
		$web = $this->input->post("web");

    ## EMPRESA
		$empresaInfo = new stdClass();
		$empresaInfo->CIIUP_Codigo      = 0;
		$empresaInfo->TIPCOD_Codigo     = $tipo_documento;
		$empresaInfo->SECCOMP_Codigo    = $sector_comercial;
		$empresaInfo->EMPRC_Ruc         = $numero_documento;
		$empresaInfo->EMPRC_RazonSocial = $razon_social;
		$empresaInfo->EMPRC_Telefono    = $telefono;
		$empresaInfo->EMPRC_Movil       = $movil;
		$empresaInfo->EMPRC_Fax         = $fax;
		$empresaInfo->EMPRC_Web         = $web;
		$empresaInfo->EMPRC_Email       = $correo;
		$empresaInfo->EMPRC_CtaCteSoles = "";
		$empresaInfo->EMPRC_CtaCteDolares = "";
		$empresaInfo->EMPRC_FlagEstado  = "1";
		$empresaInfo->EMPRC_Direccion   = $direccion;

    ## SUCURSAL
		$sucursalInfo = new stdClass();
		$sucursalInfo->TESTP_Codigo = 1;
		$sucursalInfo->UBIGP_Codigo = $ubigeo;
		$sucursalInfo->EESTABC_Descripcion = "PRINCIPAL";
		$sucursalInfo->EESTAC_Direccion = $direccion;
		$sucursalInfo->EESTABC_FlagTipo = "1";
		$sucursalInfo->EESTABC_FlagEstado = "1";

		$this->db->trans_start();

		if ($empresa != ""){
			$this->empresa_model->actualizar_empresa($empresa, $empresaInfo);
			$sucursalInfo->EMPRP_Codigo = $empresa;
			$establecimiento = $this->emprestablecimiento_model->actualizar_establecimiento_principal($sucursalInfo);
			
			if($this->db->trans_status() == false){
	      $this->db->trans_rollback();
	      $json_result = "error";
				$json_message = "No fue posible actualizar los datos.";
			}
	    else{
	      $this->db->trans_commit();
				$json_result = "success";
				$json_message = "Actualización satisfactoria.";
	    }
		}
		else{
			$json_result = "info";
			$json_message = "Solo se permite actualizar empresas registradas.";
		}

		$json = array("result" => $json_result, "message" => $json_message);
		echo json_encode($json);
	}
	##  -> End

	public function insertar_marca(){
		$empresa      = $this->input->post('empresa');
		$codigo  = $this->input->post('codigo');
		if($codigo!='' && $empresa !=''){
			$this->empresa_model->insertar_marcaEmpresa($empresa,$codigo);          
		}
		$tablaHTML = $this->TABLA_marcas('p',$empresa);
		echo $tablaHTML;
	}

	public function TABLA_tipos($tipo,$proveedor,$pinta=0){
		$datos_marcasProveedor = $this->empresa_model->obtener_tiposEmpresa($proveedor);

		$tabla='<table id="tablaTipo" width="98%" class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="1">
		<tr align="center" bgcolor="#BBBB20" height="10px;">
		<td>Nro</td>
		<td>Nombre del tipo de proveedor</td>
		<td>Borrar</td>
		<td>Editar</td>
		</tr>';

		$item = 1;
		if(count($datos_marcasProveedor)>0){
			foreach($datos_marcasProveedor as $valor){
				$tabla.='<tr bgcolor="#ffffff">';
				$nombre_marca  = $valor->FAMI_Descripcion;
				$codigo   = $valor->FAMI_Codigo;
				$registro = $valor->EMPTIPOP_Codigo;
				$codigo  = "<input type='hidden' name='tipoCodigo[".$item."]' id='tipoCodigo[".$item."]' class='cajaMedia' value='".$codigo."'>";
				$editar  = "&nbsp;";
				$eliminar  = "<a href='#' onclick='eliminar_tipo(".$registro.");'><img src='".base_url()."images/delete.gif' border='0'></a>";
				$tabla.='<td>'.$item.'</td>';
				$tabla.='<td>'.$nombre_marca.'</td>';
				$tabla.='<td>'.$eliminar.'</td>';
				$tabla.='<td>'.$editar.'</td>';
				$tabla.='</tr>';
				$item++;
			}
		}               
		$tabla.='</table>';
		if(count($datos_marcasProveedor)==0)
			$tabla.='<div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;">NO EXISTEN REGISTROS</div>';

		if($pinta=='1')
			echo $tabla;
		else
			return $tabla;
	}


	public function TABLA_marcas($tipo,$empresa,$pinta=0){
		$datos_marcasProveedor = $this->empresa_model->obtener_marcasEmpresa($empresa);

		$tabla='<table id="tablaMarca" width="98%" class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="1">
		<tr align="center" bgcolor="#BBBB20" height="10px;">
		<td>Nro</td>
		<td>Nombre de la marca</td>
		<td>Borrar</td>
		<td>Editar</td>
		</tr>';

		$item = 1;
		if(count($datos_marcasProveedor)>0){
			foreach($datos_marcasProveedor as $valor){
				$tabla.='<tr bgcolor="#ffffff">';
				$nombre_marca  = $valor->MARCC_Descripcion;
				$codigo   = $valor->MARCP_Codigo;
				$registro = $valor->EMPMARP_Codigo;
				$codigo  = "<input type='hidden' name='marcaCodigo[".$item."]' id='marcaCodigo[".$item."]' class='cajaMedia' value='".$codigo."'>";
				$editar  = "&nbsp;";
				$eliminar  = "<a href='#' onclick='eliminar_marca(".$registro.");'><img src='".base_url()."images/delete.gif' border='0'></a>";
				$tabla.='<td>'.$item.'</td>';
				$tabla.='<td>'.$nombre_marca.'</td>';
				$tabla.='<td>'.$eliminar.'</td>';
				$tabla.='<td>'.$editar.'</td>';
				$tabla.='</tr>';
				$item++;
			}
		}               
		$tabla.='</table>';
		if(count($datos_marcasProveedor)==0)
			$tabla.='<div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;">NO EXISTEN REGISTROS</div>';

		if($pinta=='1')
			echo $tabla;
		else
			return $tabla;
	}

	public function JSON_busca_empresa_xruc($tipo, $numero){
		$datos_empresa  = $this->empresa_model->busca_xnumeroDoc($tipo, $numero);
		$resultado          = '[]';
		if(count($datos_empresa)>0){
			$datos_empresaSucursal = $this->empresa_model->obtener_establecimientoEmpresa($datos_empresa[0]->EMPRP_Codigo,'1');
			$dpto_domicilio     = "15";
			$prov_domicilio     = "01";
			$dist_domicilio     = "00";             
			if(count($datos_empresaSucursal)>0){
				$ubigeo_domicilio         = $datos_empresaSucursal[0]->UBIGP_Codigo;
				$dpto_domicilio           = substr($ubigeo_domicilio,0,2);
				$prov_domicilio           = substr($ubigeo_domicilio,2,2);
				$dist_domicilio           = substr($ubigeo_domicilio,4,2);  
			}

			$resultado   = '[{"codigo":"'.$datos_empresa[0]->EMPRP_Codigo.
			'","cod_cliente":"'.$datos_empresa[0]->CLIP_Codigo.
			'","razon_social":"'.$datos_empresa[0]->EMPRC_RazonSocial.
			'","departamento":"'.$dpto_domicilio.
			'","provincia":"'.$prov_domicilio.
			'","distrito":"'.$dist_domicilio.
			'","direccion":"'.$datos_empresaSucursal[0]->EESTAC_Direccion.
			'","telefono":"'.$datos_empresa[0]->EMPRC_Telefono.
			'","movil":"'.$datos_empresa[0]->EMPRC_Movil.
			'","fax":"'.$datos_empresa[0]->EMPRC_Fax.
			'","correo":"'.$datos_empresa[0]->EMPRC_Email.
			'","paginaweb":"'.$datos_empresa[0]->EMPRC_Web.
			'","sector_comercial":"'.$datos_empresa[0]->SECCOMP_Codigo.
			'","ctactesoles":"'.$datos_empresa[0]->EMPRC_CtaCteSoles.
			'","ctactedolares":"'.$datos_empresa[0]->EMPRC_CtaCteDolares.'"}]';
		}
		echo $resultado;
	}
	public function JSON_busca_empresa_proveedor_xruc($tipo, $numero){
		$datos_empresa  = $this->empresa_model->proveedor_busca_xnumeroDoc($tipo, $numero);
		$resultado          = '[]';
		if(count($datos_empresa)>0){
			$datos_empresaSucursal = $this->empresa_model->obtener_establecimientoEmpresa($datos_empresa[0]->EMPRP_Codigo,'1');
			$dpto_domicilio     = "15";
			$prov_domicilio     = "01";
			$dist_domicilio     = "00";             
			if(count($datos_empresaSucursal)>0){
				$ubigeo_domicilio         = $datos_empresaSucursal[0]->UBIGP_Codigo;
				$dpto_domicilio           = substr($ubigeo_domicilio,0,2);
				$prov_domicilio           = substr($ubigeo_domicilio,2,2);
				$dist_domicilio           = substr($ubigeo_domicilio,4,2);  
			}

			$resultado   = '[{"codigo":"'.$datos_empresa[0]->EMPRP_Codigo.
			'","cod_proveedor":"'.$datos_empresa[0]->PROVP_Codigo.
			'","razon_social":"'.$datos_empresa[0]->EMPRC_RazonSocial.
			'","departamento":"'.$dpto_domicilio.
			'","provincia":"'.$prov_domicilio.
			'","distrito":"'.$dist_domicilio.
			'","direccion":"'.$datos_empresaSucursal[0]->EESTAC_Direccion.
			'","telefono":"'.$datos_empresa[0]->EMPRC_Telefono.
			'","movil":"'.$datos_empresa[0]->EMPRC_Movil.
			'","fax":"'.$datos_empresa[0]->EMPRC_Fax.
			'","correo":"'.$datos_empresa[0]->EMPRC_Email.
			'","paginaweb":"'.$datos_empresa[0]->EMPRC_Web.
			'","sector_comercial":"'.$datos_empresa[0]->SECCOMP_Codigo.
			'","ctactesoles":"'.$datos_empresa[0]->EMPRC_CtaCteSoles.
			'","ctactedolares":"'.$datos_empresa[0]->EMPRC_CtaCteDolares.'"}]';
		}
		echo $resultado;
	}

	public function getBuscaCuenta($codigo){
		$data_model = $this->cuentaempresa_model->getBuscarNumCuenta($codigo);
		if (count($data_model )>0) {
			echo json_encode($data_model);
		}
	}

	public function searchEmpresa(){
		$search = $this->input->post("search");
		$info = $this->empresa_model->searchEmpresas($search);
		if ($info != NULL){
			foreach ($info as $key => $value) {
				$result[] = array("value" => $value->EMPRC_RazonSocial, "label" => "$value->EMPRC_Ruc - $value->EMPRC_RazonSocial", "codigo" => $value->EMPRP_Codigo, "direccion" => $value->EMPRC_Direccion);
			}
			echo json_encode($result);
		}
	}
}       
?>