<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */

##  -> Begin - Inclusion de Librerias para pdf deshabilitadas
	#include("system/application/libraries/pchart/pData.php");
	#include("system/application/libraries/pchart/pChart.php");
	#include("system/application/libraries/cezpdf.php");
	#include("system/application/libraries/class.backgroundpdf.php");
##  -> End

class Ocompra extends CI_Controller{

	##  -> Begin - El array somevar es reemplazado por atributos
	private $empresa;
	private $compania;
	private $usuario;
	private $rol;
	private $base_url;
	##  -> End

  public function __construct(){
		##  -> Begin
		/* Parent cambio de controller() a __construct() - Se eliminaron loads no utilizados y se movieron otras que son llamados en pocos metodos*/
    parent::__construct();
    $this->load->library('lib_props');
    $this->load->library('html');

    $this->load->helper('form');
    $this->load->helper('date');
    $this->load->helper('util');
    $this->load->helper('my_guiarem_helper');

    $this->load->model('maestros/almacen_model');
    $this->load->model('almacen/guiarem_model');
    $this->load->model('almacen/producto_model');
    $this->load->model('maestros/unidadmedida_model');

    $this->load->model('compras/ocompra_model');
    $this->load->model('compras/ocompradetalle_model');
    $this->load->model('empresa/proveedor_model');
   
    $this->load->model('empresa/empresa_model');
    $this->load->model('maestros/moneda_model');
    $this->load->model('maestros/proyecto_model');
    $this->load->model('maestros/formapago_model');
    $this->load->model('maestros/persona_model');

    $this->load->model('maestros/companiaconfiguracion_model');
    $this->load->model('empresa/directivo_model');
    $this->load->model('maestros/configuracion_model');
    $this->load->model('configuracion_model');

    $this->load->model('empresa/cliente_model');
    $this->load->model('ventas/presupuesto_model');

    $this->empresa = $this->session->userdata('empresa');
    $this->compania = $this->session->userdata('compania');
    $this->usuario = $this->session->userdata('user');
    $this->rol = $this->session->userdata('rol');
    $this->base_url = base_url();
		##  -> End
  }

  public function index(){
    $this->layout->view('seguridad/inicio');
  }

  ##  -> Begin
  public function ocompras($tipo_oper = 'C', $param = NULL){
  	## En los updates, si se olvida actualizar el acceso al controlador en la tabla cji_menu, tomamos en valor de $param y lo asignamos a $tipo de operacion
  	## $parametros anteriores ($j = '0', $tipo_oper = 'C', $eval = '0')
	  	if ($tipo_oper == '0')
	  		$tipo_oper = $param;
	  ## End

    $data['compania'] = $this->compania;
    $data['tipo_oper'] = $tipo_oper;
    
    $data['id_documento'] = ($tipo_oper == 'V') ? 18 : 3;
		$data['vendedores'] = $this->directivo_model->listarVendedores();

    $tOperacion = ($tipo_oper == 'V') ? 'VENTA' : 'COMPRA';
    $data['tOperacion'] = strtolower($tOperacion);
    $data['titulo_busqueda'] = "BUSCAR COTIZACIONES DE $tOperacion";
    $data['titulo_tabla'] = "COTIZACIONES DE $tOperacion";
    $this->layout->view('compras/ocompra_index', $data);
  }
  ##  -> End

  ##  -> Begin
  public function datatable_ocompra($tipo_oper = 'V'){

  	$posDT = -1;
    $columnas = array(
                      ++$posDT => "OCOMC_FechaRegistro",
                      ++$posDT => "OCOMC_Numero",
                      ++$posDT => "CLIC_CodigoUsuario",
                      ++$posDT => "rucDni",
                      ++$posDT => "nombre",
                      ++$posDT => "",
                      ++$posDT => "OCOMC_Total",
                      ++$posDT => "",
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

      $fecha_ini = $this->input->post('fechai');
      $filter->fechai = ($fecha_ini != "") ? $fecha_ini : "";

      $fecha_fin = $this->input->post('fechaf');
      $filter->fechaf = ($fecha_fin != "") ? $fecha_fin : "";

      $filter->tipo_oper = $tipo_oper;
      $filter->nombre_cliente = $this->input->post('nombre_cliente');
      $filter->ruc_cliente = $this->input->post('ruc_cliente');

      $filter->proveedor = $this->input->post('nombre_proveedor');
      $filter->ruc_proveedor = $this->input->post('ruc_proveedor');

      $filter->producto = $this->input->post('producto');
      $filter->aprobado = $this->input->post('aprobado');
      $filter->ingreso = $this->input->post('ingreso');
      $filter->vendedor = ( $this->input->post('cboVendedor') != NULL && $this->input->post('cboVendedor') != "null" ) ? $this->input->post('cboVendedor') : "";
      $filter->empleado = $this->input->post('codigoEmpleado');

      $listado_ocompras = $this->ocompra_model->getOcompra($filter);
      $lista = array();
      if ($listado_ocompras != NULL) {
        foreach ($listado_ocompras as $indice => $valor) {
          $codigo = $valor->OCOMP_Codigo;
          $arrfecha = explode(" ", $valor->OCOMC_FechaRegistro);
          $fecha = $this->lib_props->formatHours($arrfecha[1]) . " del " . mysql_to_human($arrfecha[0]);

          if ($tipo_oper == 'V')
              $cotizacion = $valor->PRESUP_Codigo;
          else
              $cotizacion = $valor->COTIP_Codigo;

          # Guia de remision asociada
            $guiarem_codigo = $valor->GUIAREMP_Codigo;
            $guiarem_relacionada = $valor->GUIAREMC_SerieNumero;
            $guiarem_estado = $valor->GUIAREMC_FlagEstado;
          # End Guia de remision asociada
          
          # Comprobante relacionado
            $comprobante = $valor->CPP_Codigo;
            $comprobante_serieNumero = $valor->CPC_SerieNumero;

            if ($comprobante_serieNumero != NULL){
              $formatNumber = explode("-",$comprobante_serieNumero);
              $comprobante_serieNumero = $formatNumber[0]."-".$this->lib_props->getOrderNumeroSerie($formatNumber[1]);
            }
          # End comprobante relacionado

          $tipoComprobante = $valor->CPC_TipoDocumento;
          $pedido = $valor->PEDIP_Codigo;
          $numero = $valor->OCOMC_Serie . "-" . $this->lib_props->getOrderNumeroSerie($valor->OCOMC_Numero);
          $cliente = $valor->CLIP_Codigo;
          $proveedor = $valor->PROVP_Codigo;
          $ccosto = $valor->CENCOSP_Codigo;
          $total = $valor->OCOMC_total;
          $flagIngreso = $valor->OCOMC_FlagIngreso;
          $flagAprobado = $valor->OCOMC_FlagAprobado;
          $moneda = $valor->MONED_Codigo;
          $estado = $valor->OCOMC_FlagEstado;

          $idCliente = $valor->CLIC_CodigoUsuario;
          $ruc = $valor->rucDni;
          $nombre = $valor->nombre;

          $monto_total = $valor->MONED_Simbolo." ".number_format($total, 2);

          if ($valor->vendedor != '')
            $nombre = "<div class='tip'> $nombre <span class='msg'>VENDEDOR: $valor->vendedor </span> </div>";
          
          $btn_editar = "<button type='button' class='btn2 btn-default' onclick='editar_ocompra($codigo)'>
		          						<img src='".$this->base_url."images/modificar.png' class='image-size-1l' title='Modificar'>
		          					</button>";
          
          $btn_pdf = "<button type='button' class='btn2 btn-default' href='".$this->base_url."index.php/compras/ocompra/ocompra_ver_pdf_conmenbrete/$codigo/1' data-fancybox data-type='iframe'>
	    									<img src='".$this->base_url."images/pdf.png' class='image-size-1l' title='Ver PDF'>
	  									</button>";

        	$btn_options = "<button type='button' id='btnPadreCanje$codigo' class='btn2 btn-default btn-padre' onclick='btnHijos(\"btnPadreCanje$codigo\", $codigo, \"oc\",$item);'>
        									<img src='".$this->base_url."images/icono-documentos.png' class='image-size-1l' title='Más opciones'>
        									<ul class='btn-hijo'><ul>
        								</button>";

          if ( $guiarem_codigo != NULL ){
          	$guiarem_relacionada = "<a href='".base_url()."index.php/almacen/guiarem/guiarem_ver_pdf/$guiarem_codigo/a4/1' data-fancybox data-type='iframe'> <span style='font-weight: bold; font-size: 7pt; color:green'>$guiarem_relacionada</span> </a>";

          	if ($guiarem_estado == "2")
          		$guiarem_relacionada .= "<a href='".base_url()."index.php/almacen/guiarem/editar/$guiarem_codigo/$tipo_oper'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
          }
          else
          	$guiarem_relacionada = "";
          
          $comprobante_serieNumero = ($comprobante != NULL) ? "<a href='".base_url()."index.php/ventas/comprobante/comprobante_ver_pdf/$comprobante/a4' data-fancybox data-type='iframe'> <span style='font-weight: bold; font-size: 7pt; color:green'>$comprobante_serieNumero</span> </a>" : "";
            
          $posDT = -1;
          $lista[] = array(
                            ++$posDT => $fecha,
                            ++$posDT => $numero,
                            ++$posDT => $idCliente,
                            ++$posDT => $ruc,
                            ++$posDT => $nombre,
                            ++$posDT => $monto_total,
                            ++$posDT => "<div align='left' class='gResult_$item'>$guiarem_relacionada</div> <div align='left'><span class='icon-loading loading_g_$item'></span></div>",
                            ++$posDT => "<div align='left' class='cResult_$item'>$comprobante_serieNumero</div> <div align='left'><span class='icon-loading loading_c_$item'></span></div>",
                            ++$posDT => $btn_editar,
                            ++$posDT => $btn_pdf,
                            ++$posDT => $btn_options
                          );
            $item++;
        }
      }

      unset($filter->start);
      unset($filter->length);

      $filterAll = new stdClass();
      $filterAll->tipo_oper = $tipo_oper;

      $filterAll->count = true;
      $filter->count = true;

      $recordsTotal = $this->ocompra_model->getOcompra($filterAll);
      $recordsFiltered = $this->ocompra_model->getOcompra($filter);

      $json = array(
                          "draw"            => intval( $this->input->post('draw') ),
                          "recordsTotal"    => $recordsTotal->registros,
                          "recordsFiltered" => $recordsFiltered->registros,
                          "data"            => $lista
                  );

      echo json_encode($json);
  }
  ##  -> End

  ##  -> Begin
  public function insertar_guiarem(){
    $this->load->model("maestros/compania_model");
    $this->load->model('maestros/emprestablecimiento_model');

    $datos_ocompra = NULL;
    $error = false;
    $codigoOC = $this->input->post("idOC");

    if ($codigoOC == ""){
    	$message = "Documento de origen no definido.";
    	$error = true;
    }
    else{
	    $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigoOC);
	    $compania = $datos_ocompra[0]->COMPP_Codigo;

	    if ($datos_ocompra[0]->GUIAREMP_Codigo != NULL){
    		$guia_ant = $datos_ocompra[0]->GUIAREMP_Codigo;
				$sernum_ant = $datos_ocompra[0]->GUIAREMC_SerieNumero;
	    	$message = "Una guia fue asociada anteriormente: ".$datos_ocompra[0]->GUIAREMC_SerieNumero;
    		$error = true;
	    }
	  }

    if ($datos_ocompra == NULL){
    	$message = "Documento origen no encontrado.";
    	$error = true;
    }

    if ($error == false){
	    ## DATOS DE LA EMPRESA EMISORA
	      $companiaInfo = $this->compania_model->obtener($compania);
	      $establecimientoInfo = $this->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
	      $empresaInfo =  $this->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

	      $ubigeo_origen = $establecimientoInfo[0]->UBIGP_Codigo;
	      $direccion_origen = $establecimientoInfo[0]->EESTAC_Direccion;
	      
	      $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, 10);
	      $serie = ($configuracion_datos[0]->CONFIC_Serie == NULL || $configuracion_datos[0]->CONFIC_Serie == "") ? 1 : $configuracion_datos[0]->CONFIC_Serie;
	      $numero = $this->lib_props->getNumberFormat($configuracion_datos[0]->CONFIC_Numero + 1, 6);
	    
	    ## OC DETALLE GENERAL
	      $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
	      $serieOC = $datos_ocompra[0]->OCOMC_Serie;
	      $numeroOC = $datos_ocompra[0]->OCOMC_Numero;
	      $cliente = $datos_ocompra[0]->CLIP_Codigo;
	      $proveedor = $datos_ocompra[0]->PROVP_Codigo;
	      $contacto = $datos_ocompra[0]->personal;
	      $moneda = $datos_ocompra[0]->MONED_Codigo;
	      $fecha = $datos_ocompra[0]->OCOMC_Fecha;
	      $fecha = ( $datos_ocompra[0]->OCOMC_FechaEntrega != NULL && $datos_ocompra[0]->OCOMC_FechaEntrega != "" ) ? $datos_ocompra[0]->OCOMC_FechaEntrega : $fecha;
	      $almacen = $datos_ocompra[0]->ALMAP_Codigo;
	      $direccion = $datos_ocompra[0]->OCOMC_EnvioDireccion;
	      $observacion = $datos_ocompra[0]->OCOMC_Observacion;
	      # Numero de orden de compra cliente
	      $OCcliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada;
	      $obra = $datos_ocompra[0]->PROYP_Codigo;
	      # $tipo_movimiento por defecto es venta (1)
	      $tipo_movimiento = 1;
	      $otro_motivo = NULL;
	      
	    ## TOTALES
	      $igv = $datos_ocompra[0]->OCOMC_igv;
	      $igv100 = $datos_ocompra[0]->OCOMC_igv100;
	      $descuento = $datos_ocompra[0]->OCOMC_descuento;
	      $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
	      $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
	      $total = $datos_ocompra[0]->OCOMC_total;

	    ## A insertar en la guia R.
	      $filter = new stdClass();
	      $filter->GUIAREMC_TipoOperacion = $tipo_oper;
	      $filter->GUIAREMC_Serie = $serie;
	      $filter->GUIAREMC_Numero = $numero;
	      $filter->MONED_Codigo = $moneda;

	      $filter->PROYP_Codigo = $obra;
	      $filter->OCOMP_Codigo = $codigoOC;
	      $filter->PRESUP_Codigo = NULL;

	      $filter->TIPOMOVP_Codigo = $tipo_movimiento;
	      $filter->GUIAREMC_OtroMotivo = $otro_motivo;
	      $filter->ALMAP_Codigo = $almacen;
	      
	      $filter->GUIAREMC_CodigoUsuario = NULL;
	      $filter->USUA_Codigo = $this->usuario;

	      $filter->COMPP_Codigo = $compania;
	      # OC cliente guardado en $filter->DOCUP_Codigo
	      $filter->DOCUP_Codigo = $OCcliente;

	      $filter->GUIAREMC_PersReceNombre = "-";
	      $filter->GUIAREMC_PersReceDNI = "-";
	      $filter->GUIAREMC_NumeroRef = "";
	      $filter->GUIAREMC_OCompra = "$serieOC-$numeroOC";
	      $filter->GUIAREMC_FechaTraslado = $fecha;
	      
	      $filter->GUIAREMC_Fecha = date("Y-m-d");
	      $filter->EMPRP_Codigo = NULL;

	    ## CLIENTE Y DIRECCIONES
	      if ($tipo_oper == 'V'){
	          $filter->CLIP_Codigo = $cliente;
	          $datos_cliente = $this->cliente_model->obtener($cliente);

	          $nombre_cliente = $datos_cliente->nombre;
	          $ruc_cliente = $datos_cliente->ruc;
	          $dni_cliente = $datos_cliente->dni;
	          $ruc_cliente = ( $ruc_cliente == "" ) ? $dni_cliente : $ruc_cliente ;
	          $email   = $datos_cliente->correo;
	          $direccion_destino = (trim($direccion) == "") ? $datos_cliente->direccion : trim($direccion);
	          $ubigeo_destino = $datos_cliente->ubigeo;
	      }
	      else{
	          $filter->PROVP_Codigo = $proveedor;
	          $datos_proveedor = $this->proveedor_model->obtener($proveedor);

	          $nombre_proveedor = $datos_proveedor->nombre;
	          $ruc_proveedor = $datos_proveedor->ruc;
	          $direccion_destino = (trim($direccion) == "") ? $datos_proveedor->direccion : trim($direccion);
	          $ubigeo_destino = $datos_proveedor->ubigeo_codigo;
	      }
	        
		    $filter->GUIAREMC_UbigeoPartida = $ubigeo_origen;
		    $filter->GUIAREMC_PuntoPartida = strtoupper($direccion_origen);
		    #$ubigeo_destino;
		    $filter->GUIAREMC_UbigeoLlegada = 0;
		    $filter->GUIAREMC_PuntoLlegada = strtoupper($direccion_destino);

	    ## TRANSPORTE
	      $filter->EMPRP_Codigo = 1;
	      $filter->GUIAREMC_Marca = "-";
	      $filter->GUIAREMC_Placa = "-";
	      $filter->GUIAREMC_RegistroMTC = "";
	      $filter->GUIAREMC_Certificado = "";
	      $filter->GUIAREMC_Licencia = "";
	      $filter->GUIAREMC_PersReceDNI = "00000000";
	      $filter->GUIAREMC_NombreConductor = "-";
	      
	      $filter->GUIAREMC_Observacion = strtoupper($observacion);
	      $filter->GUIAREMC_descuento100 = $descuento100;
	      $filter->GUIAREMC_igv100 = $igv100;
	      $filter->GUIAREMC_subtotal = $subtotal;
	      $filter->GUIAREMC_descuento = $descuento;
	      $filter->GUIAREMC_igv = $igv;
	      $filter->GUIAREMC_total = $total;
	      $filter->GUIAREMC_FlagEstado = "2";
	      $guiarem_id = $this->guiarem_model->insertar($filter);
	      
	    ## ACTUALIZA EL CORRELATIVO DE GUIAS
	    if ($tipo_oper == 'V')
	        $this->configuracion_model->modificar_configuracion($compania, 10, $numero);

	    ## ARTICULOS
	    $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigoOC);
	    $detalle_ocompra = array();
	    if ($detalle != NULL) {
	      foreach ($detalle as $indice => $valor) {
	        $filterGuia = new stdClass();
	        $filterGuia->GUIAREMP_Codigo = $guiarem_id;
	        $filterGuia->PRODCTOP_Codigo = $valor->PROD_Codigo;
	        $filterGuia->UNDMED_Codigo = $valor->UNDMED_Codigo;
	        $filterGuia->LOTP_Codigo = $valor->LOTP_Codigo;
	        $filterGuia->AFECT_Codigo = $valor->AFECT_Codigo;
	        $filterGuia->GUIAREMDETC_Cantidad = $valor->OCOMDEC_Cantidad;
	        $filterGuia->GUIAREMDETC_Pu = $valor->OCOMDEC_Pu;
	        $filterGuia->GUIAREMDETC_Subtotal = $valor->OCOMDEC_Subtotal;
	        $filterGuia->GUIAREMDETC_Descuento = $valor->OCOMDEC_Descuento;
	        $filterGuia->GUIAREMDETC_Igv = $valor->OCOMDEC_Igv;
	        $filterGuia->GUIAREMDETC_Total = $valor->OCOMDEC_Total;
	        $filterGuia->GUIAREMDETC_Pu_ConIgv = $valor->OCOMDEC_Pu_ConIgv;
	        $filterGuia->GUIAREMDETC_Descuento100 = $valor->OCOMDEC_Descuento100;
	        $filterGuia->GUIAREMDETC_Igv100 = $valor->OCOMDEC_Igv100;
	        $filterGuia->GUIAREMDETC_Costo = $valor->OCOMDEC_Costo;
	        
	        $filterGuia->GUIAREMDETC_Venta = NULL;
	        $filterGuia->GUIAREMDETC_ITEM = $indice + 1;
	        $filterGuia->GUIAREMDETC_Peso = 0;

	        $filterGuia->GUIAREMDETC_GenInd = $valor->OCOMDEC_GenInd;
	        $filterGuia->GUIAREMDETC_Descripcion = $valor->OCOMDEC_Descripcion;
	        $filterGuia->GUIAREMDETC_Observacion = $valor->OCOMDEC_Observacion;
	        $filterGuia->ALMAP_Codigo = $almacen;
	        $this->guiaremdetalle_model->insertar($filterGuia);
	      }
	    }

  		$json = array(
                        "result" => "success",
                        "message" => "Operacion exitosa.",
                        "guia" => $guiarem_id,
                        "sernum" => $serie."-".$this->lib_props->getOrderNumeroSerie($numero)
                      );
  	}
  	else{
			$json = array(
                        "result" => "error",
                        "message" => $message,
                        "guia" => (isset($guia_ant)) ? $guia_ant : "",
                        "sernum" => (isset($sernum_ant)) ? $sernum_ant : ""
                      );
  	}

    echo json_encode($json);
  }
  ##  -> End

  ##  -> Begin
  public function insertar_comprobante(){
    $this->load->model("maestros/compania_model");
    $this->load->model('maestros/documento_model');
    $this->load->model('ventas/comprobante_model');
    $this->load->model('ventas/comprobantedetalle_model');

    $datos_ocompra = NULL;
    $error = false;
    $codigoOC = $this->input->post("idOC");
    $tipoDocumento = trim($this->input->post("doc"));

    if ($codigoOC == ""){
    	$message = "Documento de origen no definido.";
    	$error = true;
    }
    else{
	    $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigoOC);
	    $compania = $datos_ocompra[0]->COMPP_Codigo;

	    if ($datos_ocompra[0]->CPP_Codigo != NULL){
	    	$comprobante_ant = $datos_ocompra[0]->CPP_Codigo;
				$sernum_ant = $datos_ocompra[0]->CPC_SerieNumero;
	    	$message = "Un documento fue asociado anteriormente: ".$datos_ocompra[0]->CPC_SerieNumero;
    		$error = true;
	    }
    }

    if ($tipoDocumento == ""){
    	$message = "Tipo de documento destino no definido.";
    	$error = true;
    }

    if ($datos_ocompra == NULL){
    	$message = "Documento origen no encontrado.";
    	$error = true;
    }

    if ($error == false){
      ## OC DETALLE GENERAL
	      $compania = $datos_ocompra[0]->COMPP_Codigo;
	      $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
	      #$tipo_docu = $datos_ocompra[0]->CPC_TipoDocumento;
	      $tipo_docu = $tipoDocumento;
	      $serieOC = $datos_ocompra[0]->OCOMC_Serie;
	      $numeroOC = $datos_ocompra[0]->OCOMC_Numero;
	      $cliente = $datos_ocompra[0]->CLIP_Codigo;
	      $proveedor = $datos_ocompra[0]->PROVP_Codigo;
	      $contacto = $datos_ocompra[0]->personal;
	      $fecha = date("Y-m-d");
	      $hora = date("H:i:s");
	      $almacen = $datos_ocompra[0]->ALMAP_Codigo;
	      $OCcliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada; # NUMERO DE ORDEN DE COMPRA CLIENTE
	      
	      $direccion = "";
	      if ($tipo_oper == 'V'){
	        if ($datos_ocompra[0]->OCOMC_FactDireccion != "")
	          $direccion = $datos_ocompra[0]->OCOMC_FactDireccion;
	        else{
	          $datos_cliente = $this->cliente_model->obtener($cliente);
	          $direccion = $datos_cliente->direccion;
	        }
	      }

      ## COMPROBANTE DETALLE GENERAL
        $filter = new stdClass();
        $filter->CPC_TipoOperacion = $tipo_oper;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->ALMAP_Codigo = $almacen;
        $filter->CPC_NumeroAutomatico = 1;
        $filter->CPC_Fecha = $fecha;
        $filter->CPC_Hora = $hora;
        $filter->CPC_Observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $filter->CPC_FlagEstado = "2";
        
        $documento = $this->documento_model->obtenerAbreviatura($tipo_docu);
        $tipo = $documento[0]->DOCUP_Codigo;

        # Correlativo del documento
        $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        $filter->CPC_Serie = $configuracion_datos[0]->CONFIC_Serie;
        $filter->CPC_Numero = $configuracion_datos[0]->CONFIC_Numero + 1;

        $cSerie = $configuracion_datos[0]->CONFIC_Serie;
        $cNumero = $configuracion_datos[0]->CONFIC_Numero + 1;

        $filter->CLIP_Codigo = $cliente;
        $filter->CPC_Direccion = $direccion;
        $filter->PROVP_Codigo = $proveedor;

      ## PAGO Y TOTAL
        $filter->FORPAP_Codigo = $datos_ocompra[0]->FORPAP_Codigo;
        $f_pago = $datos_ocompra[0]->FORPAP_Codigo;
        $filter->MONED_Codigo = $datos_ocompra[0]->MONED_Codigo;
        
        $filter->CPC_igv = $datos_ocompra[0]->OCOMC_igv;
        $filter->CPC_igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $filter->CPC_descuento = $datos_ocompra[0]->OCOMC_descuento;
        $filter->CPC_descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $filter->CPC_subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $filter->CPC_total = $datos_ocompra[0]->OCOMC_total;
    
        $filter->CPC_TDC = $datos_ocompra[0]->OCOMP_TDC;
        $filter->CPC_TDC_opcional = $datos_ocompra[0]->OCOMP_TDC_opcional;
        
      ## OTROS DETALLES
        $filter->CPC_Vendedor = $datos_ocompra[0]->OCOMC_MiPersonal;
        $filter->OCOMP_Codigo = $codigoOC;
        $filter->CPP_Compracliente = $OCcliente;
        $filter->PROYP_Codigo = $datos_ocompra[0]->PROYP_Codigo;
    
        $filter->CPC_FlagUsaAdelanto = 0;        
        $filter->PRESUP_Codigo = NULL;
        $filter->CPC_GuiaRemCodigo = "";
        $filter->GUIAREMP_Codigo = "";
        $filter->CPC_DocuRefeCodigo = "";
        $filter->CPC_ModoImpresion = '1';
        $filter->IMPOR_Nombre = 0;

        #$mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        $comprobante = $this->comprobante_model->insertar_comprobante($filter);

      ## ASOCIAMOS LA GUIA DE REMISION
        if ($datos_ocompra[0]->GUIAREMP_Codigo != NULL){
          $filterCG = new stdClass();
          $filterCG->CPP_Codigo = $comprobante;
          $filterCG->GUIAREMP_Codigo = $datos_ocompra[0]->GUIAREMP_Codigo;
          $filterCG->COMPGUI_FlagEstado = 1;
          $filterCG->COMPGU_FechaRegistro = date("Y-m-d H:i:s");
          $this->comprobante_model->insertar_comprobante_guiarem($filterCG);
        }

      ## ARTICULOS
        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigoOC);
        $detalle_ocompra = array();
        if (count($detalle) > 0) {
          foreach ($detalle as $indice => $valor) {        
            $filterDet = new stdClass();
            $filterDet->CPP_Codigo = $comprobante;
            $filterDet->PROD_Codigo = $valor->PROD_Codigo;
            $filterDet->CPDEC_GenInd = $valor->OCOMDEC_GenInd;
            $filterDet->UNDMED_Codigo = $valor->UNDMED_Codigo;
            $filterDet->LOTP_Codigo = $valor->LOTP_Codigo;
            $filterDet->AFECT_Codigo = $valor->AFECT_Codigo;
            $filterDet->CPDEC_Cantidad = $valor->OCOMDEC_Cantidad;
            $filterDet->CPDEC_Costo = $valor->OCOMDEC_Costo;
            $filterDet->CPDEC_Pu = $valor->OCOMDEC_Pu;
            $filterDet->CPDEC_Subtotal = $valor->OCOMDEC_Subtotal;
            $filterDet->CPDEC_Descuento = $valor->OCOMDEC_Descuento;
            $filterDet->CPDEC_Descuento100 = $valor->OCOMDEC_Descuento100;
            $filterDet->CPDEC_Igv = $valor->OCOMDEC_Igv;
            $filterDet->CPDEC_Igv100 = $valor->OCOMDEC_Igv100;
            $filterDet->CPDEC_Pu_ConIgv = $valor->OCOMDEC_Pu_ConIgv;
            $filterDet->CPDEC_Total = $valor->OCOMDEC_Total;
            $filterDet->CPDEC_Descripcion = $valor->OCOMDEC_Descripcion;
            $filterDet->CPDEC_Observacion = $valor->OCOMDEC_Observacion;
            $filterDet->ALMAP_Codigo = $almacen;

            $this->comprobantedetalle_model->insertar($filterDet);
          }
        }

      $json = array(
                        "result" => "success",
                        "message" => "Operacion exitosa.",
                        "comprobante" => $comprobante,
                        "sernum" => $cSerie."-".$this->lib_props->getOrderNumeroSerie($cNumero)
                      );
  	}
  	else{
      $json = array(
                        "result" => "error",
                        "message" => $message,
                        "comprobante" => (isset($comprobante_ant)) ? $comprobante_ant : "",
                        "sernum" => (isset($sernum_ant)) ? $sernum_ant : "" 
                      );
  	}

    echo json_encode($json);
  }
  ##  -> End

	public function nueva_ocompra_dev($tipo_oper = 'C'){
    /*:::: SE CREA LA SESSION:::*/
    $hoy = date('Y-m-d H:i:s');
    $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
    $tempSession = str_replace('.','',$cadena);
    $data['tempSession']  = $tempSession;
    /* :::::::::::::::::::::::::::*/

    if ($tipo_oper == 'C')
      $data['tipo_docu'] = "OC";
    else
      $data['tipo_docu'] = "OV";

    $compania = $this->compania;
    $data['compania'] = $this->compania;
    $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
    $data_compania = $this->compania_model->obtener_compania($this->compania);

    $my_empresa = $data_compania[0]->EMPRP_Codigo;

    $usuario = $this->usuario;
    $datos_usuario = $this->usuario_model->obtener($usuario);
    $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
    $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => '', 'persona' => '', 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
    $data['url_action'] = base_url() . "index.php/compras/ocompra/insertar_ocompra";
    $data['titulo'] = "REGISTRAR ORDENES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
    $data['tipo_oper'] = $tipo_oper;
    $data['formulario'] = "frmOrdenCompra";
    $data['descuento'] = "0";
    $data['igv'] = "18";
    $data['percepcion'] = "0";
    
    $lista_almacen = $this->almacen_model->seleccionar();
    $almacen_dafault = '';
    if (count($lista_almacen) == 2) {
      foreach ($lista_almacen as $indice => $value)
        $almacen_dafault = $indice;
    }
  
    $data['detalle_ocompra'] = array();
    $data['numero'] = "";
    $data['codigo_usuario'] = "";
    $data['serie'] = "";
    $data['cliente'] = "";
    $data['ruc_cliente'] = "";
    $data['nombre_cliente'] = "";
    $data['proveedor'] = "";
    $data['nombre_proveedor'] = "";
    $data['ruc_proveedor'] = "";
    $data['pendiente'] = "";
    $data['ctactesoles'] = "";
    $data['ctactedolares'] = "";
    $data['preciototal'] = "";
    $data['descuentotal'] = "";
    $data['igvtotal'] = "";
    $data['percepciontotal'] = "";
    $data['importetotal'] = "";
    $data['observacion'] = "";
    $data['envio_direccion'] = "";
    $data['fact_direccion'] = "";
    $data['ordencompra']="";
    $data['focus'] = "";
    $data['pedido'] = "0";
    $data['hoy'] = mdate("%d/%m/%Y ", time());
    $data['fechaentrega'] = "";
    $data['contacto'] = "";
    $data['tiempo_entrega'] = "";
    $data['codigo'] = "";
    $data['estado'] = "1";
    $data['ordencompraventa']="";
    $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
    $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
    $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
    $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
    $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
    $data['verpersona'] = anchor_popup('maestros/persona/persona_ventana_mostrar', $contenido, $atributos, 'linkVerPersona');

    $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

    if (count($cambio_dia) > 0) {
        $data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
    } else {
        $data['tdcDolar'] = '';
    }

    $data["tdcEuro"] = '';

    $data['terminado'] = 0;
    $data['evaluado'] = 0;
    $data['terminado_importacion'] = 0;

    $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo_oper == 'C' ? '3' : '18');
    $cofiguracion_datos[0]->CONFIC_Serie;
    $cofiguracion_datos[0]->CONFIC_Numero;

    $data['serie'] = $cofiguracion_datos[0]->CONFIC_Serie;
    $data['serie_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Serie;
    $data['numero_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;

    $this->layout->view('compras/ocompra_nueva', $data);
  }

















    public function seguimiento_ocompras($j = '0', $tipo_oper = 'C', $eval = '0'){
        $data['compania'] = $this->compania;
        $this->load->library('layout', 'layout');
        $data['registros'] = $this->ocompra_model->total_ocompra($tipo_oper);
        $conf['base_url'] = site_url('compras/ocompra/ocompras/0/' . $tipo_oper . '/0/');
        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $data['titulo_tabla'] = "SEGUIMIENTO ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['titulo_busqueda'] = "BUSCAR SEGUIMIENTO DE ORDEN DE" . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['tipo_oper'] = $tipo_oper;
        $data['id_documento'] = ($tipo_oper == C) ? 3 : 18;
        $data['cboVendedor'] = $this->lib_props->listarVendedores();
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper));
        $this->layout->view('compras/seguimiento_ocompras', $data);
    }

    public function datatable_seguimientoOcompra($tipo_oper='C'){
        $columnas = array(
            0 => "OCOMC_Fecha",
            1 => "OCOMC_Numero",
            2 => "PRESUP_Codigo",
            3 => "rucDni",
            4 => "nombre",
            5 => "",
            6 => "OCOMC_Total",
            7 => "",
            8 => ""
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

        $fecha_ini = $this->input->post('fechai');
        $filter->fechai = ($fecha_ini != "") ? $fecha_ini : ""; # date("Y-m") . '-1';

        $fecha_fin = $this->input->post('fechaf');
        $filter->fechaf = ($fecha_fin != "") ? $fecha_fin : ""; # date("Y-m-d");

        $filter->tipo_oper = $tipo_oper;
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');

        $filter->proveedor = $this->input->post('nombre_proveedor');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');

        $filter->producto = $this->input->post('producto');
        $filter->aprobado = $this->input->post('aprobado');
        $filter->ingreso = $this->input->post('ingreso');
        $filter->vendedor = ( $this->input->post('cboVendedor') != NULL && $this->input->post('cboVendedor') != "null" ) ? $this->input->post('cboVendedor') : "";
        $filter->empleado = $this->input->post('codigoEmpleado');
        
        $listado_ocompras = $this->ocompra_model->getOcompra($filter);

        $lista = array();
        if (count($listado_ocompras) > 0) {
            foreach ($listado_ocompras as $indice => $valor) {
                $arrfecha = explode(" ", $valor->OCOMC_FechaRegistro);
                $fecha = mysql_to_human($arrfecha[0]);
                $codigo = $valor->OCOMP_Codigo;

                if ($tipo_oper == 'V')
                    $cotizacion = $valor->PRESUP_Codigo;
                else
                    $cotizacion = $valor->COTIP_Codigo;

                $pedido = $valor->PEDIP_Codigo;
                $numero = $valor->OCOMC_Numero;
                $cliente = $valor->CLIP_Codigo;
                $proveedor = $valor->PROVP_Codigo;
                $ccosto = $valor->CENCOSP_Codigo;
                $total = $valor->OCOMC_total;
                $flagIngreso = $valor->OCOMC_FlagIngreso;
                $flagAprobado = $valor->OCOMC_FlagAprobado;
                $moneda = $valor->MONED_Codigo;
                $datos_moneda = $this->moneda_model->obtener($moneda);

                if ($cliente != '' && $cliente != '0') {
                    $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
                    $empresa = $datos_cliente[0]->EMPRP_Codigo;
                    $persona = $datos_cliente[0]->PERSP_Codigo;
                    $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                }
                else
                    if ($proveedor != '' && $proveedor != '0') {
                        $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                        $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                        $persona = $datos_proveedor[0]->PERSP_Codigo;
                        $tipo = $datos_proveedor[0]->PROVC_TipoPersona;
                    }

                $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
                $monto_total = $simbolo_moneda . " " . number_format($total, 2);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_proveedor = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
                }

                $msgaprob = '';

                switch ($flagAprobado) {
                    case '0':
                        $msgaprob = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>No evaluado</div>";
                        break;
                    case '1':
                        $msgaprob = "<div style='background-color: rgba(3,170,3,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Aprobado</div>";
                        break;
                    case '2':
                        $msgaprob = "<div style='background-color: rgba(194,0,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Desaprobado</div>";
                        break;
                    
                    default:
                        $msgaprob = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>No evaluado</div>";
                        break;
                }

                $estado = $valor->OCOMC_FlagEstado;
                switch ($estado) {
                    case '1':
                        $img_estado = "<div style='background-color: rgba(3,170,3,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Aceptado</div>";
                        break;
                    case '2':
                        $img_estado = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Pendiente</div>";
                        break;
                    default:
                        $img_estado = "<div style='background-color: rgba(194,0,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Anulado</div>";
                        break;
                }

                $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
                $por_entregado = 0;
                $por_no_entregado = 0;
                $cantidad_total = 0;
                $cantidad_entregada = 0;
                if (count($detalle) > 0) {
                    foreach ($detalle as $valor2) {
                        $cantidad_total += $valor2->OCOMDEC_Cantidad;
                        $cantidad_entregada += calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor2->PROD_Codigo);
                    }
                }
                $por_entregado = $cantidad_total == 0 ? 0 : ($cantidad_entregada * 100) / $cantidad_total;
                $por_no_entregado = 100 - $por_entregado;
                $por_entregado = round($por_entregado, 2);
                $por_no_entregado = round($por_no_entregado, 2);
                $url_img = "";
                $title = "Entreagado : al " . $por_entregado . "%, No Entregado al " . $por_no_entregado . "%";
                // Estado
                $msguiain = '';
                $edit = true;
                if ($cantidad_entregada == 0) {
                    $url_img = "images/ninguno.png";
                    $msguiain = "<span class='tooltip' style='color: #8c1a16; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Pendiente' >Pend.</span>";
                }
                if ($cantidad_entregada > 0) {
                    $url_img = "images/proceso.png";
                    $msguiain = "<span class='tooltip' style='color: #8c8b02; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Cargando' >Carg.</span>";
                }
                if ($cantidad_entregada == $cantidad_total) {
                    $url_img = "images/entregado.png";
                    $msguiain = "<span class='tooltip' style='color: #33d811; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Terminado' >Term.</span>";
                    $edit = false;
                }

                $estado = $img_estado;
                                
                $ver_detalles = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/ver_detalle.png' width='16' height='16' border='0' title='Ver Detalle'></a>";
                
                $ver = "<a href='javascript:;' onclick='ocompra_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2 = "<a href='javascript:;' onclick='ocompra_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar = "<a href='javascript:;' onclick='eliminar_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[] = array(
                    $fecha, 
                    $numero, 
                    $cotizacion, 
                    $nombre_proveedor, 
                    $msguiain, 
                    $monto_total, 
                    $msgaprob, 
                    $estado, 
                    $ver_detalles, 
                    $ver, 
                    $ver2
                );
            }
        }

        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->ocompra_model->getOcompra($filterAll)),
                            "recordsFiltered" => intval( count($this->ocompra_model->getOcompra($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    ###########OBSOLETO###############
    public function seguimiento_ocompras_old($j = '0', $tipo_oper = 'C', $eval = '0'){
        $data['compania'] = $this->compania;
        $this->load->helper('my_guiarem');
        $this->load->library('layout', 'layout');
        $evalua = false;

        $data['registros'] = $this->ocompra_model->total_ocompra($tipo_oper);
        $conf['base_url'] = site_url('compras/ocompra/ocompras/0/' . $tipo_oper . '/0/');
        $conf['total_rows'] = $data['registros'];
        $conf['per_page'] = 50;
        $conf['num_links'] = 3;
        $conf['uri_segment'] = 7;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $offset = (int)$this->uri->segment(7);
        $listado_ocompras = $this->ocompra_model->seguimiento_listar($tipo_oper, $conf['per_page'], $offset);
        $item = $j + 1;
        $lista = array();
        if (count($listado_ocompras) > 0) {
            foreach ($listado_ocompras as $indice => $valor) {
                $arrfecha = explode(" ", $valor->OCOMC_FechaRegistro);
                $fecha = mysql_to_human($arrfecha[0]);
                $codigo = $valor->OCOMP_Codigo;

                if ($tipo_oper == 'V')
                    $cotizacion = $valor->PRESUP_Codigo;
                else
                    $cotizacion = $valor->COTIP_Codigo;

                $pedido = $valor->PEDIP_Codigo;
                $numero = $valor->OCOMC_Numero;
                $cliente = $valor->CLIP_Codigo;
                $proveedor = $valor->PROVP_Codigo;
                $ccosto = $valor->CENCOSP_Codigo;
                $total = $valor->OCOMC_total;
                $flagIngreso = $valor->OCOMC_FlagIngreso;
                $flagAprobado = $valor->OCOMC_FlagAprobado;
                $moneda = $valor->MONED_Codigo;
                $datos_moneda = $this->moneda_model->obtener($moneda);

                if ($cliente != '' && $cliente != '0') {
                    $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
                    $empresa = $datos_cliente[0]->EMPRP_Codigo;
                    $persona = $datos_cliente[0]->PERSP_Codigo;
                    $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                }
                else
                    if ($proveedor != '' && $proveedor != '0') {
                        $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                        $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                        $persona = $datos_proveedor[0]->PERSP_Codigo;
                        $tipo = $datos_proveedor[0]->PROVC_TipoPersona;
                    }

                $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
                $monto_total = $simbolo_moneda . " " . number_format($total, 2);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_proveedor = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
                }

                $msgaprob = '';

                switch ($flagAprobado) {
                    case '0':
                        $msgaprob = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>No evaluado</div>";
                        break;
                    case '1':
                        $msgaprob = "<div style='background-color: rgba(3,170,3,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Aprobado</div>";
                        break;
                    case '2':
                        $msgaprob = "<div style='background-color: rgba(194,0,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Desaprobado</div>";
                        break;
                    
                    default:
                        $msgaprob = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>No evaluado</div>";
                        break;
                }

                $estado = $valor->OCOMC_FlagEstado;
                switch ($estado) {
                    case '1':
                        $img_estado = "<div style='background-color: rgba(3,170,3,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Aceptado</div>";
                        break;
                    case '2':
                        $img_estado = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Pendiente</div>";
                        break;
                    default:
                        $img_estado = "<div style='background-color: rgba(194,0,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Anulado</div>";
                        break;
                }

                $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
                $por_entregado = 0;
                $por_no_entregado = 0;
                $cantidad_total = 0;
                $cantidad_entregada = 0;
                if (count($detalle) > 0) {
                    foreach ($detalle as $valor2) {
                        $cantidad_total += $valor2->OCOMDEC_Cantidad;
                        $cantidad_entregada += calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor2->PROD_Codigo);
                    }
                }
                $por_entregado = $cantidad_total == 0 ? 0 : ($cantidad_entregada * 100) / $cantidad_total;
                $por_no_entregado = 100 - $por_entregado;
                $por_entregado = round($por_entregado, 2);
                $por_no_entregado = round($por_no_entregado, 2);
                $url_img = "";
                $title = "Entreagado : al " . $por_entregado . "%, No Entregado al " . $por_no_entregado . "%";
                // Estado
                $msguiain = '';
                $edit = true;
                if ($cantidad_entregada == 0) {
                    $url_img = "images/ninguno.png";
                    $msguiain = "<span class='tooltip' style='color: #8c1a16; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Pendiente' >Pend.</span>";
                }
                if ($cantidad_entregada > 0) {
                    $url_img = "images/proceso.png";
                    $msguiain = "<span class='tooltip' style='color: #8c8b02; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Cargando' >Carg.</span>";
                }
                if ($cantidad_entregada == $cantidad_total) {
                    $url_img = "images/entregado.png";
                    $msguiain = "<span class='tooltip' style='color: #33d811; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Terminado' >Term.</span>";
                    $edit = false;
                }

                $estado = $img_estado;
                                
                $ver_detalles = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/ver_detalle.png' width='16' height='16' border='0' title='Ver Detalle'></a>";
                
                $ver = "<a href='javascript:;' onclick='ocompra_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2 = "<a href='javascript:;' onclick='ocompra_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar = "<a href='javascript:;' onclick='eliminar_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[] = array(NULL, $item++, $fecha, $numero, $cotizacion, $pedido, $nombre_proveedor, $msguiain, $monto_total, $msgaprob, $estado, NULL, $ver_detalles, $ver, $ver2);
            }
        }
        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
        $data['evalua'] = $evalua;
        $data['titulo_tabla'] = "SEGUIMIENTO DE COTIZACIONES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['titulo_busqueda'] = "SEGUIMIENTO DE COTIZACIONES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['lista'] = $lista;
        $data['tipo_oper'] = $tipo_oper;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('compras/seguimiento_ocompras', $data);
    }

    public function buscar($tipo_oper, $eval = '0'){
        $this->load->helper('my_guiarem');
        $data['compania'] = $this->compania;
        $evalua = true;
        if ($eval == '1' && count($this->permiso_model->busca_permiso($this->rol, 38)) > 0) {
            $evalua = false;
        }
        $filter = new stdClass();
        $fecha_ini = $this->input->post('fechai');
        if ($fecha_ini != "") {
            $filter->fechai = $fecha_ini;
        } else {
            $filter->fechai = date("Y-m-d");
        }
        $fecha_fin = $this->input->post('fechaf');
        if ($fecha_fin != "") {
            $filter->fechaf = $fecha_fin;
        } else {
            $filter->fechaf = date("Y-m-d");
        }
        $filter->tipo_oper = $tipo_oper;
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');

        $filter->proveedor = $this->input->post('nombre_proveedor');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');

        $filter->producto = $this->input->post('producto');
        $filter->aprobado = $this->input->post('aprobado');
        $filter->ingreso = $this->input->post('ingreso');
        $filter->vendedor = $this->input->post('cboVendedor');
        $filter->empleado = $this->input->post('codigoEmpleado');

        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => $filter->fechai));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => $filter->fechaf));
        $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        if ($tipo_oper == 'V') {
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos);
        } else {
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
        }
        if ($tipo_oper == 'V') {
            $listado_ocompras = $this->ocompra_model->obtenerOrdenCompra($filter);
        } else {
            $listado_ocompras = $this->ocompra_model->obtenerOrdenCompra($filter);
        }



        /*********************************************************************************************/
            $data['registros'] = count($listado_ocompras);
            $conf['base_url'] = site_url('compras/ocompra/ocompras/0/' . $tipo_oper . '/0/');
            $conf['total_rows'] = $data['registros'];
            $conf['per_page'] = 50;
            $conf['num_links'] = 3;
            $conf['uri_segment'] = 7;
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link'] = "&gt;&gt;";
            $offset = (int)$this->uri->segment(7);
            $item = $j + 1;
            $lista = array();
            
            if (count($listado_ocompras) > 0) {
                foreach ($listado_ocompras as $indice => $valor) {
                    $arrfecha = explode(" ", $valor->OCOMC_FechaRegistro);
                    $fecha = $this->lib_props->formatHours($arrfecha[1]) . " del " . mysql_to_human($arrfecha[0]);
                    $codigo = $valor->OCOMP_Codigo;

                    if ($tipo_oper == 'V')
                        $cotizacion = $valor->PRESUP_Codigo;
                    else
                        $cotizacion = $valor->COTIP_Codigo;

                    $guiarem_codigo = $valor->GUIAREMP_Codigo;
                    $guiarem_relacionada = $valor->GUIAREMC_SerieNumero;
                    
                    $comprobante = $valor->CPP_Codigo;
                    $comprobante_serieNumero = $valor->CPC_SerieNumero;

                    if ($comprobante_serieNumero != NULL){
                        $formatNumber = explode("-",$comprobante_serieNumero);
                        $comprobante_serieNumero = $formatNumber[0]."-".$this->lib_props->getOrderNumeroSerie($formatNumber[1]);
                    }

                    $tipoComprobante = $valor->CPC_TipoDocumento;
                    $pedido = $valor->PEDIP_Codigo;
                    $numero = $this->lib_props->getOrderNumeroSerie($valor->OCOMC_Numero);
                    $cliente = $valor->CLIP_Codigo;
                    $proveedor = $valor->PROVP_Codigo;
                    $ccosto = $valor->CENCOSP_Codigo;
                    $total = $valor->OCOMC_total;
                    $flagIngreso = $valor->OCOMC_FlagIngreso;
                    $flagAprobado = $valor->OCOMC_FlagAprobado;
                    $moneda = $valor->MONED_Codigo;
                    $datos_moneda = $this->moneda_model->obtener($moneda);
                    $estado = $valor->OCOMC_FlagEstado;

                    $idCliente = "";

                    if ($cliente != '' && $cliente != '0') {
                        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
                        $idCliente = $datos_cliente[0]->CLIC_CodigoUsuario;
                        $empresa = $datos_cliente[0]->EMPRP_Codigo;
                        $persona = $datos_cliente[0]->PERSP_Codigo;
                        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                    } elseif ($proveedor != '' && $proveedor != '0') {
                        $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                        $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                        $persona = $datos_proveedor[0]->PERSP_Codigo;
                        $tipo = $datos_proveedor[0]->PROVC_TipoPersona;
                    }

                    $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
                    $monto_total = $simbolo_moneda . " " . number_format($total, 2);

                    if ($tipo == 0) {
                        $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                        $nombre_proveedor = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    } elseif ($tipo == 1) {
                        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                        $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
                    }

                    if ($cliente != '' && $cliente != '0') {
                        $nombre_proveedor = "<div class='tip'> $nombre_proveedor <span class='msg'>VENDEDOR: $valor->razon_social_cliente $valor->vendedor </span> </div>";
                    }

                    $msgaprob = '';
                    if ($flagAprobado == "0") {
                        $msgaprob = "Pend.";
                    } elseif ($flagAprobado == "1") {
                        $msgaprob = "Aprob.";
                    } elseif ($flagAprobado == "2") {
                        $msgaprob = "Desaprob.";
                    }
                    if ($evalua == true)
                        $check = "<input type='checkbox' name='checkO[" . $item . "]' id='checkO[" . $item . "]' value='" . $codigo . "'>";
                    else
                        $check = "";

                    $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
                    $por_entregado = 0;
                    $por_no_entregado = 0;
                    $cantidad_total = 0;
                    $cantidad_entregada = 0;
                    if (count($detalle) > 0) {
                        foreach ($detalle as $valor2) {
                            $cantidad_total += $valor2->OCOMDEC_Cantidad;
                            $cantidad_entregada += calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor2->PROD_Codigo);
                        }
                    }
                    $por_entregado = $cantidad_total == 0 ? 0 : ($cantidad_entregada * 100) / $cantidad_total;
                    $por_no_entregado = 100 - $por_entregado;
                    $por_entregado = round($por_entregado, 2);
                    $por_no_entregado = round($por_no_entregado, 2);
                    $url_img = "";
                    $title = "Entreagado : al " . $por_entregado . "%, No Entregado al " . $por_no_entregado . "%";
                    // Estado
                    $msguiain = '';
                    $edit = true;
                    if ($cantidad_entregada == 0) {
                        $url_img = "images/ninguno.png";
                        $msguiain = "<span class='tooltip' style='color: #8c1a16; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Pendiente' >Pend.</span>";
                    }
                    if ($cantidad_entregada > 0) {
                        $url_img = "images/proceso.png";
                        $msguiain = "<span class='tooltip' style='color: #8c8b02; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Cargando' >Carg.</span>";
                    }
                    if ($cantidad_entregada >= $cantidad_total) {
                        $url_img = "images/entregado.png";
                        $msguiain = "<span class='tooltip' style='color: green; padding: 2px 15px 2px 15px; font-weight: bolder; font-size: 11px' title='Terminado' >Term.</span>";
                        $edit = false;

                        if ($estado == "2"){
                            $filterU = new stdClass();
                            $filterU->OCOMC_FlagEstado = 1;
                            $this->ocompra_model->modificar_ocompra($codigo, $filterU);
                        }
                    }

                    switch ($estado) {
                        case '1':
                            $img_estado = "<div style='background-color: rgba(3,170,3,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Aceptado</div>";
                            break;
                        case '2':
                            $img_estado = "<div style='background-color: rgba(200,200,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Pendiente</div>";
                            break;
                        
                        default:
                            $img_estado = "<div style='background-color: rgba(194,0,0,1); padding: 0.5em 1em; text-align: center; color: rgba(255,255,255,1)'>Anulado</div>";
                            break;
                    }

                    $img = "<a href='javascript:;' title='" . $title . "' class='tooltip'><img src='" . base_url() . "" . $url_img . "' /></a>";
                    $estado = $img;

                    if ($eval == '0') {
                        $estado = $img_estado;
                    }

                    $enviarcorreo =  "<a onclick='enviar_cotizacion(".$codigo.");' href='#' class='enviarcorreo'><img src='" . base_url() . "images/send.png' width='16' height='16' border='0' title='Enviar Cotizacion via correo'></a>";

                    $contents = "<img height='16' width='16' src='" . base_url() . "images/icono-factura.gif' title='Factura' border='0'>";
                    $attribs = array('width' => 400, 'height' => 150, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
                    $ver3 = anchor_popup('compras/ocompra/ventana_ocompra_factura/' . $codigo, $contents, $attribs);
                    if ($evalua) {
                        $editar = "<a href='javascript:;' onclick='editar_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";

                        if ($edit == false)
                           $editar = '';
                    } else {
                        $editar = "<a href='javascript:;' onclick='ver_detalle_ocompra($codigo)'><img src='" . base_url() . "images/ver_detalle.png' width='16' height='16' border='0' title='Ver Detalle'></a>";
                    }
                    $ver = "<a href='javascript:;' onclick='ocompra_ver_pdf($codigo)'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                    $ver2 = "<a href='javascript:;' onclick='ocompra_ver_pdf_conmenbrete($codigo)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                    $excel = "<a href='javascript:;' onclick='ocompra_download_excel($codigo)'><img src='" . base_url() . "images/excel.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                    $eliminar = "<a href='javascript:;' onclick='eliminar_ocompra(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                    if ( $valor->OCOMC_FlagEstado == "1"){
                            switch ($tipoComprobante) {
                                case 'F':
                                    $tComprobante = "Factura";
                                    $nComprobante = 8;
                                    break;
                                case 'B':
                                    $tComprobante = "Boleta";
                                    $nComprobante = 9;
                                    break;
                                case 'N':
                                    $tComprobante = "Comprobante";
                                    $nComprobante = 14;
                                    break;
                                default:
                                    $tComprobante = "Comprobante";
                                    $nComprobante = 14;
                                    break;
                            }

                            if ($tipoComprobante == 'N'){
                                $canjeGuia = "";
                                $canjeComprobante = ($comprobante != NULL) ? "<a href='javascript:;' onclick=\"comprobante_ver_pdf_conmenbrete($comprobante, $nComprobante, 'a4')\" target='_parent'> <span style='font-weight: bold; font-size: 7pt; color:green'>► $comprobante_serieNumero</span> </a>" : "<a href='javascript:;' onclick='canjeToComprobante($codigo, $item)' title='Convertir en Comprobante' style='font-weight: bold; font-size: 7pt;'>► $tComprobante</a>";
                            }
                            else{
                                $canjeGuia = ( $guiarem_codigo != NULL ) ? "<a href='javascript:;' onclick=comprobante_ver_pdf_conmenbrete($guiarem_codigo,10,'a4',1) target='_parent'> <span style='font-weight: bold; font-size: 7pt; color:green'>► $guiarem_relacionada</span> </a>" : "<a href='javascript:;' onclick='canjeToGuia($codigo, $item)' title='Convertir en Guia de Remisión' style='font-weight: bold; font-size: 7pt;'>► Guia</a>";
                                
                                $canjeComprobante = ($comprobante != NULL) ? "<a href='javascript:;' onclick=\"comprobante_ver_pdf_conmenbrete($comprobante, $nComprobante, 'a4')\" target='_parent'> <span style='font-weight: bold; font-size: 7pt; color:green'>► $comprobante_serieNumero</span> </a>" : "<a href='javascript:;' onclick='canjeToComprobante($codigo, $item)' title='Convertir en Factura o Boleta' style='font-weight: bold; font-size: 7pt;'>► $tComprobante</a>";
                            }
                    }
                    else{
                        $canjeGuia = ( $guiarem_codigo != NULL ) ? "<a href='javascript:;' onclick=comprobante_ver_pdf_conmenbrete($guiarem_codigo,10,'a4',1) target='_parent'> <span style='font-weight: bold; font-size: 7pt; color:green'>$guiarem_relacionada</span> </a>" : "";
                        
                        $canjeComprobante = ($comprobante != NULL) ? "<a href='javascript:;' onclick=comprobante_ver_pdf_conmenbrete($comprobante, $nComprobante, 'a4') target='_parent'> <span style='font-weight: bold; font-size: 7pt; color:green'>► $comprobante_serieNumero</span> </a>" : "";
                    }
                    
                    $lista[] = array($check, $item++, $fecha, $numero, $cotizacion, $pedido, $nombre_proveedor, $msguiain, $monto_total, $msgaprob, $estado, $ver3, $editar, $ver, $ver2, $enviarcorreo, $canjeGuia, $canjeComprobante, $idCliente, $excel);
                }
            }

            $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
            $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
            $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
            $data['evalua'] = $evalua;
            $data['titulo_tabla'] = "RELACIÓN de COTIZACIONES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
            $data['titulo_busqueda'] = "BUSCAR COTIZACIONES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
            $data['lista'] = $lista;
            $data['producto'] = $filter->producto;
            $data['tipo_oper'] = $tipo_oper;
            $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper));
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            //$this->layout->view('compras/ocompra_index', $data);
            $this->load->view("compras/buscar_ocompra_index", $data);
    }

    public function nueva_ocompra($tipo_oper = 'C'){
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        if ($tipo_oper == 'C') {
            $data['tipo_docu'] = "OC";
        }else{
            $data['tipo_docu'] = "OV";
        }
        /* :::::::::::::::::::::::::::*/
        $compania = $this->compania;
        $data['compania'] = $this->compania;
        $this->load->library('layout', 'layout');
        unset($_SESSION['serie']);

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $compania = $this->compania;
        $data['compania'] = $compania;

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $modo = "";
        $data['modo'] = $modo;
        $accion = "";
        $modo = "insertar";
        $codigo = "";
        $usuario = $this->usuario;
        $datos_usuario = $this->usuario_model->obtener($usuario);
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $data['nombre_usuario'] = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => '', 'persona' => '', 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['url_action'] = base_url() . "index.php/compras/ocompra/insertar_ocompra";
        $data['titulo'] = "REGISTRAR ORDENES DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['tipo_oper'] = $tipo_oper;
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['descuento'] = "0";
        $data['igv'] = "18";
        $data['percepcion'] = "0";
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        
        $lista_almacen = $this->almacen_model->seleccionar();
        $almacen_dafault = '';
        if (count($lista_almacen) == 2) {
            foreach ($lista_almacen as $indice => $value)
                $almacen_dafault = $indice;
        }

        $data['cboObra'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '');
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, $almacen_dafault, " class='comboGrande' id='almacen'");
        $data['cboFormapago'] = form_dropdown("formapago", $this->formapago_model->seleccionar(), "1", " class='comboMedio' id='formapago'");
        $data['cboContacto'] = $this->OPTION_generador(array(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'));
        $data['cboVendedor'] = $this->lib_props->listarVendedores();
       
        $data['detalle_ocompra'] = array();
        $data['numero'] = "";
        $data['codigo_usuario'] = "";
        $data['serie'] = "";
        $data['cliente'] = "";
        $data['ruc_cliente'] = "";
        $data['nombre_cliente'] = "";
        $data['proveedor'] = "";
        $data['nombre_proveedor'] = "";
        $data['ruc_proveedor'] = "";
        $data['pendiente'] = "";
        $data['ctactesoles'] = "";
        $data['ctactedolares'] = "";
        $data['preciototal'] = "";
        $data['descuentotal'] = "";
        $data['igvtotal'] = "";
        $data['percepciontotal'] = "";
        $data['importetotal'] = "";
        $data['observacion'] = "";
        $data['envio_direccion'] = "";
        $data['fact_direccion'] = "";
        $data['ordencompra']="";
        $data['focus'] = "";
        $data['pedido'] = "0";
        $data['hoy'] = mdate("%d/%m/%Y ", time());
        $data['fechaentrega'] = "";
        $data['contacto'] = "";
        $data['tiempo_entrega'] = "";
        $data['codigo'] = "";
        $data['estado'] = "1";
        $data['ordencompraventa']="";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['verpersona'] = anchor_popup('maestros/persona/persona_ventana_mostrar', $contenido, $atributos, 'linkVerPersona');

        $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

        if (count($cambio_dia) > 0) {
            $data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
        } else {
            $data['tdcDolar'] = '';
        }

        $data["tdcEuro"] = '';

        $data['terminado'] = 0;
        $data['evaluado'] = 0;
        $data['terminado_importacion'] = 0;

        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo_oper == 'C' ? '3' : '18');
        $cofiguracion_datos[0]->CONFIC_Serie;
        $cofiguracion_datos[0]->CONFIC_Numero;

        $data['serie'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['serie_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;

        $this->layout->view('compras/ocompra_nueva', $data);
    }

    public function listar_contactos_empresa($codigo = NULL){
        $codigo = $this->input->post('codigoEmpresa');
        //var_dump($codigo);
        $compania = $this->compania;
        $datosCliente=$this->ocompra_model->listar_clientes_cotizacion($codigo);
        $result = array();
        
        if($datosCliente!=null && count($datosCliente)>0){
            foreach ($datosCliente  as $key => $valor) {
                $nombre = $valor->PERSC_Nombre . ' ' .$valor->PERSC_ApellidoPaterno;
                $DIREP = $valor->DIREP_Codigo;
                
                $result[] = array("nombreContacto" => $nombre, "DIREP_Codigo" => $DIREP);
            }
        }
        
        echo json_encode($result);
    }

    public function insertar_ocompra(){
        
        if ($this->input->post('tipo_oper') == 'C' && ($this->input->post('almacen') == '' || $this->input->post('almacen') == ''))
            exit('{"result":"error", "campo":"almacen"}');

        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');

        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == '')
            exit('{"result":"error", "campo":"ruc_proveedor"}');

        if ($this->input->post('moneda') == '' || $this->input->post('moneda') == '0')
            exit('{"result":"error", "campo":"moneda"}');

        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');

        $tipo_oper = $this->input->post('tipo_oper');

        $filter = new stdClass();
        $filter->OCOMC_TipoOperacion = $tipo_oper;
        $filter->OCOMC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->OCOMC_Serie = $this->input->post('serie');
        $filter->OCOMC_Entrega = $this->input->post('tiempo_entrega');

        if ($tipo_oper == 'V') {
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $filter->PRESUP_Codigo = NULL;
            if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
                $filter->PRESUP_Codigo = $this->input->post('presupuesto');

        } else {
            $filter->PROVP_Codigo = $this->input->post('proveedor');
            $proveedor = $this->input->post('proveedor');
            $filter->COTIP_Codigo = NULL;
            if ($this->input->post('cotizacion') != '' && $this->input->post('cotizacion') != '0')
                $filter->COTIP_Codigo = $this->input->post('cotizacion');
            
            $cotizacion = $this->input->post('cotizacion');

        }
        $filter->OCOMP_CodigoVenta = $this->input->post('ordencompra');
        $filter->PROYP_Codigo = $this->input->post('obra');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->OCOMC_descuento100 = $this->input->post('descuento');
        if ($this->input->post('ordencompraempresa') != '' && $this->input->post('ordencompraempresa') != '0') // GUARDA LA ORDEN DE COMPRA DE LA EMPRESA EN EL CAMPO OCOMC_PersonaAutorizada
            $filter->OCOMC_PersonaAutorizada = $this->input->post('ordencompraempresa');

        if ($this->input->post('cboVendedor') != '' && $this->input->post('cboVendedor') != '0') // VENDEDOR
            $filter->OCOMC_MiPersonal = $this->input->post('cboVendedor');

        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0') // CONTACTO DE LA EMPRESA A QUIEN SE COTIZA
            $filter->OCOMC_Personal = $this->input->post('contacto');

        $filter->OCOMC_igv100 = $this->input->post('igv');
        //$filter->OCOMC_percepcion100 = $this->input->post('percepcion');
        $filter->OCOMC_subtotal = $this->input->post('preciototal');
        $filter->OCOMC_descuento = $this->input->post('descuentotal');
        $filter->OCOMC_igv = $this->input->post('igvtotal');
        $filter->OCOMC_total = $this->input->post('importetotal');
        $filter->OCOMC_percepcion = $this->input->post('percepciontotal');
        $filter->CENCOSP_Codigo = $this->input->post('centro_costo');
        $filter->OCOMC_Observacion = strtoupper($this->input->post('observacion'));
        if ($this->input->post('almacen') != '' && $this->input->post('almacen') != '0')
            $filter->ALMAP_Codigo = $this->input->post('almacen');

        if ($this->input->post('formapago') != '' && $this->input->post('formapago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('formapago');

        $filter->OCOMC_EnvioDireccion = $this->input->post('envio_direccion');
        $filter->OCOMC_FactDireccion = $this->input->post('fact_direccion');
        $filter->PROYP_Codigo = $this->input->post("proyecto");

        $filter->OCOMP_TDC = $this->input->post('moneda') == 4 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');
        $filter->OCOMP_TDC_opcional = $this->input->post('moneda') == 4 ? $this->input->post('tdcDolar') : 0;

        $filter->OCOMC_Fecha = human_to_mysql($this->input->post('fecha'));
        if ($this->input->post('fechaentrega') != '')
            $filter->OCOMC_FechaEntrega = human_to_mysql($this->input->post('fechaentrega'));

        $filter->OCOMC_CtaCteSoles = $this->input->post('ctactesoles');
        $filter->OCOMC_CtaCteDolares = $this->input->post('ctactedolares');
        $filter->OCOMC_FlagEstado = $this->input->post('estado');
        $filter->CPC_TipoDocumento = $this->input->post('tipoComprobante');

        #datos correlativo
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->compania, $id_documento = ($tipo_oper == 'C' ? '3' : '18'));
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;

        $filter->OCOMC_Numero = $numero;

        if($id_documento == 18) $filter->OCOMC_FlagBS = '';

        $ocompra = $this->ocompra_model->insertar_ocompra($filter);
        $this->configuracion_model->modificar_configuracion($this->compania, $id_documento, $numero);
        
        $prodcodigo = $this->input->post('prodcodigo');
        $flagBS = $this->input->post('flagBS');
        $produnidad = $this->input->post('produnidad');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodpu = $this->input->post('prodpu');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $proddescuento2 = $this->input->post('proddescuento2');
        $prodigv = $this->input->post('prodigv');
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $detaccion = $this->input->post('detaccion');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodcosto = $this->input->post('prodcosto');
        $proddescri = $this->input->post('proddescri');
        $pendiente = $this->input->post('pendiente');
        $cantidareal = $this->input->post('cantidareal');
        $oventas = $this->input->post('oventacod');
        $ordencompra = $this->input->post('ordencompra');
        $tafectacion = $this->input->post('tafectacion');
        #$lote = $this->input->post('idLote');

        $observacionesdetalle = $this->input->post('prodobservacion');
        $fleteDetalle = $this->input->post("flete");

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $accion = $detaccion[$indice];

                if($accion != "e" && $accion != "EE"){
                    $producto = $prodcodigo[$indice];
                    $filter = new stdClass();
                    $filter->OCOMP_Codigo = $ocompra;
                    $filter->PROD_Codigo = $prodcodigo[$indice];
                    $filter->OCOMDEC_Cantidad = $prodcantidad[$indice];
                    $filter->OCOMDEC_Pendiente = $pendiente[$indice];
                    $filter->UNDMED_Codigo = $produnidad[$indice] == 0 ? NULL : $produnidad[$indice];
                    #$filter->LOTP_Codigo = $lote[$indice];
                    $filter->AFECT_Codigo = ($tafectacion[$indice] != '' && $tafectacion[$indice] != 0) ? $tafectacion[$indice] : 1;

                    $filter->OCOMDEC_Pendiente_pago = $filter->OCOMDEC_Cantidad;
                    
                    if(isset($cantidareal[$indice])) $filter->OCOMDEC_CantidadReal = $cantidareal[$indice];

                    $filter->OCOMDEC_Descuento100 = $proddescuento100[$indice];
                    $filter->OCOMDEC_Igv100 = $prodigv100[$indice];
                    $filter->OCOMDEC_Pu = $prodpu[$indice];
                    $filter->OCOMDEC_Subtotal = $prodprecio[$indice];
                    $filter->OCOMDEC_Descuento = $proddescuento[$indice];
                    $filter->OCOMDEC_Descuento2 = 0;
                    $filter->OCOMDEC_Igv = $prodigv[$indice];
                    $filter->OCOMDEC_Total = $prodimporte[$indice];
                    $filter->OCOMDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                    $filter->OCOMDEC_Costo = $prodcosto[$indice];
                    $filter->OCOMDEC_Descripcion = strtoupper($proddescri[$indice]);
                    $filter->OCOMDEC_GenInd = $flagGenInd[$indice];

                    $filter->OCOMDEC_Observacion = $observacionesdetalle[$indice];
                    $filter->OCOMDEC_flete = isset($fleteDetalle[$indice]) ? $fleteDetalle[$indice] : 0;

                    //incluimos el id de OV para poder identificar a que cliente y proyecto le corresponde
                    $filter->OCOMP_Codigo_venta = $oventas[$indice];
                    $this->ocompradetalle_model->insertar($filter);
                     
                     $codoventa= $oventas[$indice];
                     if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != ''  ){
                            //$this->ocompra_model->modificar_pendienteoventa($codoventa,$filter->PROD_Codigo,$filter->OCOMDEC_Cantidad);
                            $this->ocompra_model->modificar_pendiente_cantidad_id_detalle_venta($codoventa, -$filter->OCOMDEC_Cantidad);
                        }


                }
            }
        }
            
            foreach ($oventas as  $codoventa) {
                
                if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != '' ){
                                $detalle_ocompra=$this->ocompra_model->esta_importado($codoventa);
                               // $cantidadp=$detalle_ocompra->OCOMDEC_Pendiente;
                                    if ($detalle_ocompra) {
                                         $this->ocompra_model->modificar_flagTerminado_oventa($codoventa, "1");
                                    }
                                
                            }
            }
            
        if ( $this->input->post('estado') == "1" ){
            $this->lib_props->sendMail(60, $ocompra); # MENU 60 = "Seguimiento de Cotizaciones",
        }

        exit('{"result":"ok", "codigo":"' . $ocompra . '"}');
    }

    public function editar_ocompra($codigo, $tipo_oper = 'C')
    {   
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        if ($tipo_oper == 'C') {
            $data['tipo_docu'] = "OC";
        }else{
            $data['tipo_docu'] = "OV";
        }
        $data['codigo'] = $codigo;
        /* :::::::::::::::::::::::::::*/
        $data['compania'] = $this->compania;
        $this->load->library('layout', 'layout');
        unset($_SESSION['serie']);
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $data['modo'] = $modo;
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $presupuesto = $datos_ocompra[0]->PRESUP_Codigo;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $codigo_usuario = $datos_ocompra[0]->OCOMC_CodigoUsuario;
        $serie = $datos_ocompra[0]->OCOMC_Serie;

        /**ponemos en en estado seleccionado presupuesto**/
        if($presupuesto!=null && trim($presupuesto)!="" &&  $presupuesto!=0){
            $estadoSeleccion=1;
            $codigoPresupuesto=$presupuesto;
            /**1:sdeleccionado,0:deseleccionado**/
            $this->presupuesto_model->modificarTipoSeleccion($codigoPresupuesto,$estadoSeleccion);
        }
        /**fin de poner**/
        $data['ordencompra'] =$codigo;
        $tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $cboVendedor = $datos_ocompra[0]->OCOMC_MiPersonal;
        $contacto = $datos_ocompra[0]->OCOMC_Personal;
        $envio_direccion = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_ocompra[0]->OCOMC_FactDireccion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $fecha = substr($datos_ocompra[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_ocompra[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $usuario = $datos_ocompra[0]->USUA_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $estado = $datos_ocompra[0]->OCOMC_FlagEstado;
        $evaluado = $datos_ocompra[0]->OCOMC_FlagAprobado;
        $tipoComprobante = $datos_ocompra[0]->CPC_TipoDocumento;

        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $percepciontotal = $datos_ocompra[0]->OCOMC_percepcion;
        $total = $datos_ocompra[0]->OCOMC_total;
        $ordencompraventa = $datos_ocompra[0]->OCOMP_CodigoVenta;
        $data['ordencompraventa'] = $ordencompraventa; 
        $data['ordencompraempresa'] = $datos_ocompra[0]->OCOMC_PersonaAutorizada;
        //$codigocliente = $datos_ocompra[0]->CLIP_Codigo;
        $codigoproyecto = $datos_ocompra[0]->PROYP_Codigo;
        
        $data['cboObra'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', $codigoproyecto == 0 ? '3' : $codigoproyecto);
        $data["almacen"] = $almacen;

        /*if($codigoproyecto != 0){
            $listaproyecto = $this->proyecto_model->seleccionar($codigoproyecto);
            $data['cboObra'] = form_dropdown("obra",$listaproyecto,$codigoproyecto, " class='comboGrande'  id='obra' ");
        }else{
            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
        }*/
            


        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $empresa = '';
        $numero_suger_oc = '';
        $serie_suger_oc = '';
        $persona = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $tipo = $datos_cliente->tipo;
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = ($datos_cliente->ruc != '' && $datos_cliente->ruc != 0) ? $datos_cliente->ruc : $datos_cliente->dni;
                $empresa = $datos_cliente->empresa;
                $persona = $datos_cliente->persona;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $tipo = $datos_proveedor->tipo;
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
                $empresa = $datos_proveedor->empresa;
                $persona = $datos_proveedor->persona;
            }
        }

        $data['tipo_oper'] = $tipo_oper;
        //$data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, 'F'), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), $presupuesto, array('0', '::Seleccione::'), ' - ');
        //$data['cboCotizacion'] = form_dropdown("cotizacion", $this->cotizacion_model->seleccionar(), $cotizacion, " class='comboMedio' id='cotizacion' onchange='obtener_detalle_cotizacion();' onfocus='javascript:this.blur();return false;'");
        $data['cboMoneda'] = $this->seleccionar_moneda($moneda);

        if ($cotizacion == 0) {
            $data['cboAlmacen'] = form_dropdown("almacen", $this->almacen_model->seleccionar(), $almacen, " class='comboMedio' id='almacen'");
            $data['cboFormapago'] = form_dropdown("formapago", $this->formapago_model->seleccionar(), $formapago, " class='comboMedio' id='formapago'");
        } else {
            $data['cboAlmacen'] = form_dropdown("almacen", $this->almacen_model->seleccionar(), $almacen, " class='comboMedio' id='almacen' onfocus='javascript:this.blur();return false;'");
            $data['cboFormapago'] = form_dropdown("formapago", $this->formapago_model->seleccionar(), $formapago, " class='comboMedio' id='formapago' onfocus='javascript:this.blur();return false;'");
        }

        $data['contacto'] = $contacto;
        $data['cboVendedor'] = $this->lib_props->listarVendedores($cboVendedor);
        
        $contactosEmpresa = $this->empresa_model->listar_contactosEmpresa($empresa);

        $data["cboContacto"] = "<option value=''>Seleccionar</option>";
        foreach ($contactosEmpresa as $key => $value) {
          
            $selected = ($contacto == $value->ECONP_Contacto) ? 'SELECTED' : '';
            $data["cboContacto"] .= "<option value='".$value->ECONP_Contacto."' $selected>".$value->ECONC_Descripcion."</option>";
        }

         $datos_usuario= $this->usuario_model->obtener($usuario);
         $numun ="";
         if(count($datos_usuario)>0){
           $numun = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;

         }
        $data['nombre_usuario'] =$numun;
        $data['numero'] = $numero;
        $data['codigo_usuario'] = $codigo_usuario;
        $data['serie'] = $serie;
        $data['igv'] = $igv100;
        $data['igv_db'] = 18;
        $data['descuento'] = $descuento100;
        $data['percepcion'] = $percepcion100;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['serie_suger_oc'] = $serie_suger_oc;
        $data['numero_suger_oc'] = $numero_suger_oc;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['pedido'] = $pedido;
        $data['cotizacion'] = $cotizacion;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => $empresa, 'persona' => $persona, 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "EDITAR ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/compras/ocompra/modificar_ocompra";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = mysql_to_human($fecha);
        $data['fechaentrega'] = ($fechaentrega != '' ? mysql_to_human($fechaentrega) : '');
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuentototal;
        $data['igvtotal'] = $igvtotal;
        $data['percepciontotal'] = $percepciontotal;
        $data['importetotal'] = $total;
        $data['ctactesoles'] = $ctactesoles;
        $data['ctactedolares'] = $ctactedolares;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;
        $data['evaluado'] = $evaluado;
        $data['tipoComprobante'] = $tipoComprobante;
        $data['tiempo_entrega'] = $tiempo_entrega;
        $data['envio_direccion'] = $envio_direccion;
        $data['fact_direccion'] = $fact_direccion;

        $tdc = $datos_ocompra[0]->OCOMP_TDC;

        $data['tdcDolar'] = $moneda == 4 ? $datos_ocompra[0]->OCOMP_TDC_opcional : $tdc;
        $data['tdcEuro'] = $moneda == 4 ? $tdc : '';

        $data['terminado'] = $datos_ocompra[0]->OCOMC_FlagTerminado;
        $data['terminado_importacion'] = $datos_ocompra[0]->OCOMC_FlagTerminadoProceso;

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);

        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {

                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $pendiente = $valor->OCOMDEC_Pendiente;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $total = $valor->OCOMDEC_Total;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;

                $descuento = $valor->OCOMDEC_Descuento;
                $descuento2 = $valor->OCOMDEC_Descuento2;
                $observ = $valor->OCOMDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->OCOMDEC_GenInd;
                $costo = $valor->OCOMDEC_Costo;
                $nombre_producto = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pendiente=$pendiente;
                $objeto->OCOMDEC_Igv = $igv;
                $objeto->OCOMDEC_Pu = $pu;
                $objeto->OCOMDEC_Total = $total;
                $objeto->OCOMDEC_Pu_ConIgv = $pu_conigv;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->OCOMDEC_GenInd = $GenInd;
                $objeto->OCOMDEC_Costo = $costo;
                $objeto->OCOMDEC_Subtotal = $subtotal;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento2 = $descuento2;
                $objeto->OCOMDEC_Pendiente_pago = $valor->OCOMDEC_Pendiente_pago;

                $objeto->RazonSocial = '';

                $referencia_venta = $this->ocompra_model->obtener_referencia_compra_by_id_detalle_venta($valor->OCOMP_Codigo_venta);

                if(isset($referencia_venta->PROVP_Codigo)) {

                }

                if(isset($referencia_venta->CLIP_Codigo)) {
                    $cliente_datos = $this->cliente_model->obtener($referencia_venta->CLIP_Codigo);

                    $objeto->RazonSocial = $cliente_datos->nombre;
                }

                $objeto->RazonSocial = (!isset($referencia_venta->PROVP_Codigo) && !isset($referencia_venta->CLIP_Codigo)) ? "Almacen" : $objeto->RazonSocial;
                $objeto->PROYP_Codigo = isset($referencia_venta->PROYP_Codigo) ? $referencia_venta->PROYP_Codigo : 0;
                $objeto->PROYC_Nombre = isset($referencia_venta->PROYC_Nombre) ? $referencia_venta->PROYC_Nombre : 0;

                $objeto->OCOMP_Codigo_venta = $valor->OCOMP_Codigo_venta;
                $objeto->OCOMP_Codigo = $valor->OCOMP_Codigo;
                
                $objeto->OCOMP_Codigo_referencia = isset($referencia_venta->OCOMP_Codigo) ? $referencia_venta->OCOMP_Codigo : 0;

                $objeto->OCOMDEC_Observacion = $observ;


                $detalle_ocompra[] = $objeto;
            }
        }
        $data['detalle_ocompra'] = $detalle_ocompra;
        $this->layout->view('compras/ocompra_nueva', $data);
    }

    public function ver_detalle_ocompra($codigo, $tipo_oper = 'C'){
    	$this->load->model('almacen/serie_model');
    	$this->load->model('compras/pedido_model');
        $this->load->helper('my_guiarem');
        $this->load->library('layout', 'layout');
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $presupuesto = $datos_ocompra[0]->PRESUP_Codigo;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $codigo_usuario = $datos_ocompra[0]->OCOMC_CodigoUsuario;
        $serie = $datos_ocompra[0]->OCOMC_Serie;

        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        #$lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_MiPersonal);
        $mi_contacto = $datos_ocompra[0]->mipersonal;
        #$lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $contacto = $datos_ocompra[0]->personal;
        $envio_direccion = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_ocompra[0]->OCOMC_FactDireccion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $fecha = substr($datos_ocompra[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_ocompra[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $usuario = $datos_ocompra[0]->USUA_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $estado = $datos_ocompra[0]->OCOMC_FlagEstado;
        $estadoEvaluacion = $datos_ocompra[0]->OCOMC_FlagAprobado;

        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $percepciontotal = $datos_ocompra[0]->OCOMC_percepcion;
        $total = $datos_ocompra[0]->OCOMC_total;

        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $empresa = '';
        $persona = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $tipo = $datos_cliente->tipo;
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $empresa = $datos_cliente->empresa;
                $persona = $datos_cliente->persona;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $tipo = $datos_proveedor->tipo;
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
                $empresa = $datos_proveedor->empresa;
                $persona = $datos_proveedor->persona;
            }
        }

        $data['tipo_oper'] = $tipo_oper;
        // $data['cboPresupuesto'] = $this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, 'F');
        //$data['cboCotizacion'] = $this->cotizacion_model->obtener_cotizacion($cotizacion);
        $data['cboMoneda'] = $this->moneda_model->obtener($moneda);

        if ($cotizacion == 0) {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        } else {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        }

        $data['mi_contacto'] = $mi_contacto;
        $data['contacto'] = $contacto;
        $data['cboPedidos'] = form_dropdown("pedidos", $this->pedido_model->seleccionar_finalizados(), "", " onchange='load_cotizaciones();' class='comboGrande' style='width:200px;' id='pedidos'");
        $datos_usuario = $this->usuario_model->obtener($usuario);
        $data['nombre_usuario'] = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;
        $data['numero'] = $numero;
        $data['id'] = $codigo;
        $data['codigo_usuario'] = $codigo_usuario;
        $data['igv'] = $igv100;
        $data['descuento'] = $descuento100;
        $data['percepcion'] = $percepcion100;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['pedido'] = $pedido;
        $data['cotizacion'] = $cotizacion;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => $empresa, 'persona' => $persona, 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "ESTADO DE COTIZACIÓN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/compras/ocompra/actualizarEvaluacion";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = mysql_to_human($fecha);
        $data['fechaentrega'] = ($fechaentrega != '' ? mysql_to_human($fechaentrega) : '');
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuentototal;
        $data['igvtotal'] = $igvtotal;
        $data['percepciontotal'] = $percepciontotal;
        $data['importetotal'] = $total;
        $data['ctactesoles'] = $ctactesoles;
        $data['ctactedolares'] = $ctactedolares;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;
        $data['estadoEvaluacion'] = $estadoEvaluacion;

        $data['envio_direccion'] = $envio_direccion;
        $data['fact_direccion'] = $fact_direccion;

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $total = $valor->OCOMDEC_Total;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento2 = $valor->OCOMDEC_Descuento2;
                $observ = $valor->OCOMDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
                $cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;                
                $cantidad_presente = $this->serie_model->cantidad_series_presente_x_ocompra($codigo, $producto);

                $stockAlmacenInfo = $this->producto_model->getStockDisponible($producto);

                $stockAlmacen = ($stockAlmacenInfo == NULL) ? 0 : $stockAlmacenInfo[0]->ALMPROD_StockDisponible;
                $stockAlmacen = ($stockAlmacen < 0 ) ? 0 : $stockAlmacen;


                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->COTDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pu = $pu;
                $objeto->OCOMDEC_Subtotal = $subtotal;
                $objeto->OCOMDEC_Total = $total;
                $objeto->OCOMDEC_Pu_ConIgv = $pu_conigv;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento2 = $descuento2;
                $objeto->OCOMDEC_Igv = $igv;

                $objeto->cantidad_entregada = $cantidad_entregada;
                $objeto->cantidad_pendiente = $cantidad_pendiente;
                $objeto->cantidad_vendida = $cantidad_entregada - $cantidad_presente;
                $objeto->codigo = $codigo;
                $objeto->tipo_oper = $tipo_oper;
                $objeto->stockAlmacen = $stockAlmacen;

                $inventariado = ($stockAlmacenInfo == NULL ) ? false : true;

                if ($inventariado == false)
                    $objeto->generarPedido = true;
                else
                    if ( $inventariado == true && $objeto->COTDEC_Cantidad - $objeto->cantidad_vendida <= $stockAlmacen ) // HAY STOCK PARA HACER LA VENTA
                        $objeto->generarPedido = false;
                else
                    if ( $inventariado == true && $objeto->COTDEC_Cantidad - $objeto->cantidad_vendida > $stockAlmacen ) // NO HAY STOCK PARA HACER LA VENTA
                        $objeto->generarPedido = true;
                    
                $detalle_ocompra[] = $objeto;
            }
        }
        $data['detalle_ocompra'] = $detalle_ocompra;
        $this->layout->view('compras/ocompra_detalle', $data);
    }

    public function actualizarEvaluacion(){
        if ( $this->input->post('process') == 0 ){ // Si no ha sido evaluado antes
            $estado = $this->input->post('estadoEvaluacion');
            $codigo = $this->input->post('id');
            $generarPedido = $this->input->post('gPedido');

            if ($estado != '' && $codigo != ''){
                if ($estado != "0"){
                    $filter = new stdClass();
                    $filter->OCOMC_FlagAprobado = $estado;
                    $this->ocompra_model->modificar_ocompra($codigo, $filter);

                    if ($generarPedido == "1" && $estado == "1"){
                        $result = $this->insertar_pedido($codigo);
                    }
                }
            }
        }

        $json = array("redirect" => base_url()."index.php/compras/ocompra/seguimiento_ocompras/0/V/1", "msg" => $result);
        echo json_encode($json);
        #redirect(base_url()."index.php/compras/ocompra/seguimiento_ocompras/0/V/1");
    }

    public function insertar_pedido($codigoOC){
    	$this->load->model('compras/pedido_model');
    	$this->load->model('compras/pedidodetalle_model');
        $compania = $this->compania;

        $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, 1);
        $serie = ($configuracion_datos[0]->CONFIC_Serie == NULL || $configuracion_datos[0]->CONFIC_Serie == "") ? 1 : $configuracion_datos[0]->CONFIC_Serie;
        $numero = $this->lib_props->getOrderNumeroSerie($configuracion_datos[0]->CONFIC_Numero + 1);

        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigoOC);       
        $fechasistema = date("Y-m-d h:i:s");
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $contacto = $datos_ocompra[0]->personal;
        $ocompra = $datos_ocompra[0]->OCOMP_Codigo;
        $igvpp = $datos_ocompra[0]->OCOMC_igv100;
        $descuentotal = $datos_ocompra[0]->OCOMC_descuento;
        $vventa = $datos_ocompra[0]->OCOMC_subtotal;
        $importebruto = $datos_ocompra[0]->OCOMC_total;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;

        $preciototal = $datos_ocompra[0]->OCOMC_total;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $obra = "";

        $estado = 3;
            
        $cod_pedido = $this->pedido_model->insertar_pedido($serie,$numero,$fechasistema,$moneda,$obra,$cliente,$contacto, $ocompra, $igvpp,$importebruto,$descuentotal,$vventa,$igvtotal,$preciototal,$descuento100,$estado,$observacion);

        $filter = new stdClass();
        $filter->PEDIP_Codigo = $cod_pedido;
        $this->ocompra_model->modificar_ocompra($codigoOC, $filter);
    
        $this->configuracion_model->update_numero_pedido($numero, $compania);

        $fecha = date('Y-m-d h:i:s');
        $gocompra = false;

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigoOC);
        $detalle_ocompra = array();

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $total = $valor->OCOMDEC_Total;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento100 = $valor->OCOMDEC_Descuento100;

                $stockAlmacen = $this->producto_model->getStockDisponible($producto);

                if ($stockAlmacen != NULL){ # SI ESTA EN ALMACEN, ESTA INVENTARIADO.
                    if ( $stockAlmacen[0]->ALMPROD_StockDisponible > 0 ){ # CANTIDAD DISPONIBLE EN POSITIVO, ES IGUAL A STOCK - CANTIDAD PENDIENTE EN COTIZACIONES/GUIAS/COMPROBANTES
                        if ( $cantidad <= $stockAlmacen[0]->ALMPROD_StockDisponible ) # SI LA CANTIDAD COTIZADA ES MENOR DE LO DISPONIBLE DEJALA EN 0 PARA NO INCLUIRLA EN EL PEDIDO.
                            $cantidad = 0;
                        else
                            if ( $cantidad > $stockAlmacen[0]->ALMPROD_StockDisponible ){ # SI CANTIDAD > QUE EL DISPONIBLE VERIFICO CUANTO SE DEBE PEDIR
                                if ( $stockAlmacen[0]->ALMPROD_StockDisponible <= 0 ) # SI STOCK DISPONIBLE <= 0 PIDO LA CANTIDAD COMPLETA DEL PRODUCTO
                                    $cantidad = $cantidad;
                                else # SI STOCK DISPONIBLE > 0, PIDO LA CANTIDAD ORIGINAL DEL PRODUCTO MENOS LO QUE ESTA DISPONIBLE.
                                    $cantidad -= $stockAlmacen[0]->ALMPROD_StockDisponible;
                            }
                    }
                }

                if ($stockAlmacen == NULL || $cantidad > 0){ # SI NO ESTA INVENTARIADO O CANTIDAD > 0 (STOCK DISPONIBLE NO CUBRE CANTIDAD DE COTIZADA), INCLUYO EL PRODUCTO EN EL PEDIDO
                    $filterDP = new stdClass();
                    $filterDP->PEDIP_Codigo = $cod_pedido;
                    $filterDP->PROD_Codigo = $producto;
                    $filterDP->UNDMED_Codigo = $unidad;
                    $filterDP->PEDIDETC_Cantidad = $cantidad;
                    $filterDP->PEDIDETC_PCIGV = $pu;
                    $filterDP->PEDIDETC_PSIGV = $pu;
                    $filterDP->PEDIDETC_Precio  = $pu;
                    $filterDP->PEDIDETC_Descuento100 = $descuento100;
                    $filterDP->PEDIDETC_Descuento = $descuento;
                    $filterDP->PEDIDETC_IGV = $igv;
                    $filterDP->PEDIDETC_Importe = $total;
                    $filterDP->PEDIDETC_FechaRegistro =   $fecha;
                    $filterDP->PEDIDETC_FlagEstado = "1";
                    $this->pedidodetalle_model->insertar_varios($filterDP);

                    if ( $valor->FABRIP_Codigo != 1 )
                        $gocompra = true;
                }
            }
            
            $this->lib_props->sendMail(61, $cod_pedido, NULL, "Un pedido ha sido generado desde la aprobacion de una cotización. <b>Serie: $serie-$numero</b>"); # MENU 61 = Órdenes de pedidos

            if ( $gocompra == true){

                $tipo_oper = "C";
                        
                $filterOC = new stdClass();
                $filterOC->OCOMC_TipoOperacion = $tipo_oper;
                $filterOC->OCOMC_CodigoUsuario = $_SESSION['user'];
                $filterOC->OCOMC_Serie = "OC";
                $filterOC->OCOMC_Entrega = "";
                $filterOC->PROVP_Codigo = $_SESSION['empresa'];
                $filterOC->COTIP_Codigo = NULL;
                $filterOC->OCOMP_CodigoVenta = "";
                $filterOC->PROYP_Codigo = "";
                $filterOC->MONED_Codigo = "1";
                $filterOC->OCOMC_descuento100 = "0";
                $filterOC->OCOMC_PersonaAutorizada = ""; # GUARDA LA ORDEN DE COMPRA DE LA EMPRESA EN EL CAMPO OCOMC_PersonaAutorizada
                $filterOC->OCOMC_MiPersonal = ""; # VENDEDOR
                $filterOC->OCOMC_Personal = ""; # CONTACTO DE LA EMPRESA A QUIEN SE COTIZA
                $filterOC->OCOMC_igv100 = "18";
                $filterOC->OCOMC_igv = "";
                $filterOC->OCOMC_subtotal = "0";
                $filterOC->OCOMC_descuento = "0";
                $filterOC->OCOMC_total = "0";
                $filterOC->OCOMC_percepcion = "0";
                $filterOC->CENCOSP_Codigo = "";
                $filterOC->OCOMC_Observacion = "";
                $filterOC->ALMAP_Codigo = $datos_ocompra[0]->ALMAP_Codigo;
                $filterOC->FORPAP_Codigo = "1";
                $filterOC->OCOMC_EnvioDireccion = $datosEmpresa[0]->EMPRC_Direccion;
                $filterOC->OCOMC_FactDireccion = $datosEmpresa[0]->EMPRC_Direccion;
                $filterOC->OCOMP_TDC = "2";
                $filterOC->OCOMP_TDC_opcional = "";
                $filterOC->OCOMC_Fecha = date("Y-m-d");
                $filterOC->OCOMC_FechaEntrega = "";
                $filterOC->OCOMC_CtaCteSoles = "";
                $filterOC->OCOMC_CtaCteDolares = "";
                $filterOC->OCOMC_FlagEstado = 2;
                $filterOC->CPC_TipoDocumento = "F";

                #datos correlativo
                $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->compania, 3);
                $numeroOC = $datos_configuracion[0]->CONFIC_Numero + 1;
                $filterOC->OCOMC_Numero = $numeroOC;

                $ocompra = $this->ocompra_model->insertar_ocompra($filterOC);
                $this->configuracion_model->modificar_configuracion($this->compania, 3, $numeroOC);

                foreach ($detalle as $indice => $valor) {
                    $producto = $valor->PROD_Codigo;
                    $cantidad = $valor->OCOMDEC_Cantidad;

                    if ( $valor->FABRIP_Codigo != 1 ){
                        $stockAlmacen = $this->producto_model->getStockDisponible($producto);

                        if ($stockAlmacen != NULL){ # SI ESTA EN ALMACEN, ESTA INVENTARIADO.
                            if ( $stockAlmacen[0]->ALMPROD_StockDisponible > 0 ){ # CANTIDAD DISPONIBLE EN POSITIVO, ES IGUAL A STOCK - CANTIDAD PENDIENTE EN COTIZACIONES/GUIAS/COMPROBANTES
                                if ( $cantidad <= $stockAlmacen[0]->ALMPROD_StockDisponible ) # SI LA CANTIDAD COTIZADA ES MENOR DE LO DISPONIBLE DEJALA EN 0 PARA NO INCLUIRLA EN EL PEDIDO.
                                    $cantidad = 0;
                                else
                                    if ( $cantidad > $stockAlmacen[0]->ALMPROD_StockDisponible ){ # SI CANTIDAD > QUE EL DISPONIBLE VERIFICO CUANTO SE DEBE PEDIR
                                        if ( $stockAlmacen[0]->ALMPROD_StockDisponible <= 0 ) # SI STOCK DISPONIBLE <= 0 PIDO LA CANTIDAD COMPLETA DEL PRODUCTO
                                            $cantidad = $cantidad;
                                        else # SI STOCK DISPONIBLE > 0, PIDO LA CANTIDAD ORIGINAL DEL PRODUCTO MENOS LO QUE ESTA DISPONIBLE.
                                            $cantidad -= $stockAlmacen[0]->ALMPROD_StockDisponible;
                                    }
                            }
                        }

                        if ($stockAlmacen == NULL || $cantidad > 0){ # SI NO ESTA INVENTARIADO O CANTIDAD > 0 (STOCK DISPONIBLE NO CUBRE CANTIDAD DE COTIZADA), INCLUYO EL PRODUCTO EN EL PEDIDO
                            $filter = new stdClass();
                            $filter->OCOMP_Codigo = $ocompra;
                            $filter->PROD_Codigo = $valor->PROD_Codigo;
                            $filter->OCOMDEC_Cantidad = $valor->OCOMDEC_Cantidad;
                            $filter->OCOMDEC_Pendiente = $valor->OCOMDEC_Pendiente;
                            $filter->UNDMED_Codigo = $valor->UNDMED_Codigo;
                            #$filter->LOTP_Codigo = ""; NO APLICA EN COMPRA
                            $filter->AFECT_Codigo = $valor->AFECT_Codigo;

                            $filter->OCOMDEC_Pendiente_pago = $valor->OCOMDEC_Pendiente_pago;
                            
                            $filter->OCOMDEC_Descuento100   = 0; #$valor->OCOMDEC_Descuento100;
                            $filter->OCOMDEC_Igv100         = $valor->OCOMDEC_Igv100;
                            $filter->OCOMDEC_Pu             = 0; #$valor->OCOMDEC_Pu;
                            $filter->OCOMDEC_Subtotal       = 0; #$valor->OCOMDEC_Subtotal;
                            $filter->OCOMDEC_Descuento      = 0; #$valor->OCOMDEC_Descuento;
                            $filter->OCOMDEC_Descuento2     = 0; #$valor->OCOMDEC_Descuento2;
                            $filter->OCOMDEC_Igv            = 0; #$valor->OCOMDEC_Igv;
                            $filter->OCOMDEC_Total          = 0; #$valor->OCOMDEC_Total;
                            $filter->OCOMDEC_Pu_ConIgv      = 0; #$valor->OCOMDEC_Pu_ConIgv;
                            $filter->OCOMDEC_Costo          = $valor->OCOMDEC_Costo;
                            $filter->OCOMDEC_Descripcion    = $valor->OCOMDEC_Descripcion;
                            $filter->OCOMDEC_GenInd         = $valor->OCOMDEC_GenInd;
                            $filter->OCOMDEC_Observacion    = $valor->OCOMDEC_Observacion;
                            $filter->OCOMDEC_flete          = $valor->OCOMDEC_flete;
                            $filter->OCOMP_Codigo_venta = $valor->OCOMP_Codigo_venta;

                            $this->ocompradetalle_model->insertar($filter);
                        }
                    }
                }

                $this->lib_props->sendMail(20, $ocompra, NULL, "Una órden de compra ha sido generada desde la aprobacion de una cotización. <b>Serie: OC-$numeroOC</b>"); # MENU 20 = Órdenes de compra
            }
        }

        $result = "Se ha generado el pedido: $serie-$numero";
        $result = ( isset($numeroOC) && $numeroOC != "" ) ? $result . "\nSe ha generado la orden de compra: OC-$numeroOC." : $result;
        return $result;
    }

    public function ver_ocompra($codigo, $tipo_oper = 'C'){
    	$this->load->model('almacen/serie_model');
    	$this->load->model('compras/pedido_model');
        $this->load->helper('my_guiarem');
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $presupuesto = $datos_ocompra[0]->PRESUP_Codigo;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $codigo_usuario = $datos_ocompra[0]->OCOMC_CodigoUsuario;
        $serie = $datos_ocompra[0]->OCOMC_Serie;

        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_MiPersonal);
        $mi_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $envio_direccion = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_ocompra[0]->OCOMC_FactDireccion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $fecha = substr($datos_ocompra[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_ocompra[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $usuario = $datos_ocompra[0]->USUA_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $estado = $datos_ocompra[0]->OCOMC_FlagEstado;

        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $percepciontotal = $datos_ocompra[0]->OCOMC_percepcion;
        $total = $datos_ocompra[0]->OCOMC_total;

        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $empresa = '';
        $persona = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $tipo = $datos_cliente->tipo;
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $empresa = $datos_cliente->empresa;
                $persona = $datos_cliente->persona;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $tipo = $datos_proveedor->tipo;
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
                $empresa = $datos_proveedor->empresa;
                $persona = $datos_proveedor->persona;
            }
        }

        $data['tipo_oper'] = $tipo_oper;
        //$data['cboPresupuesto'] = $this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, 'F');
        //$data['cboCotizacion'] = $this->cotizacion_model->obtener_cotizacion($cotizacion);
        $data['cboMoneda'] = $this->moneda_model->obtener($moneda);

        if ($cotizacion == 0) {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        } else {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        }

        $data['mi_contacto'] = $mi_contacto;
        $data['contacto'] = $contacto;
        $data['cboPedidos'] = form_dropdown("pedidos", $this->pedido_model->seleccionar_finalizados(), "", " onchange='load_cotizaciones();' class='comboGrande' style='width:200px;' id='pedidos'");
        $datos_usuario = $this->usuario_model->obtener($usuario);
        $data['nombre_usuario'] = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;
        $data['numero'] = $numero;
        $data['codigo_usuario'] = $codigo_usuario;
        $data['igv'] = $igv100;
        $data['descuento'] = $descuento100;
        $data['percepcion'] = $percepcion100;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['pedido'] = $pedido;
        $data['cotizacion'] = $cotizacion;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => $empresa, 'persona' => $persona, 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "ESTADO DE ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/compras/ocompra/modificar_ocompra";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = mysql_to_human($fecha);
        $data['fechaentrega'] = ($fechaentrega != '' ? mysql_to_human($fechaentrega) : '');
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuentototal;
        $data['igvtotal'] = $igvtotal;
        $data['percepciontotal'] = $percepciontotal;
        $data['importetotal'] = $total;
        $data['ctactesoles'] = $ctactesoles;
        $data['ctactedolares'] = $ctactedolares;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;

        $data['envio_direccion'] = $envio_direccion;
        $data['fact_direccion'] = $fact_direccion;

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $total = $valor->OCOMDEC_Total;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento2 = $valor->OCOMDEC_Descuento2;
                $observ = $valor->OCOMDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
                $cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;

                $cantidad_presente = $this->serie_model->cantidad_series_presente_x_ocompra($codigo, $producto);

                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->COTDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pu = $pu;
                $objeto->OCOMDEC_Subtotal = $subtotal;
                $objeto->OCOMDEC_Total = $total;
                $objeto->OCOMDEC_Pu_ConIgv = $pu_conigv;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento2 = $descuento2;
                $objeto->OCOMDEC_Igv = $igv;


                $objeto->cantidad_entregada = $cantidad_entregada;
                $objeto->cantidad_pendiente = $cantidad_pendiente;
                $objeto->cantidad_vendida = $cantidad_entregada - $cantidad_presente;
                $objeto->codigo = $codigo;
                $objeto->tipo_oper = $tipo_oper;

                $detalle_ocompra[] = $objeto;
            }
        }
        $data['detalle_ocompra'] = $detalle_ocompra;
        $this->load->view('compras/ocompra_ver', $data);
    }

    public function modificar_ocompra(){
        $tipo_oper = $this->input->post('tipo_oper');
        $codigo = $this->input->post('codigo');

        if ($this->input->post('tipo_oper') == 'C' && ($this->input->post('almacen') == '' || $this->input->post('almacen') == '')){
            exit('{"result":"error", "campo":"almacen"}');
        }
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == ''){
            exit('{"result":"error", "campo":"ruc_cliente"}');
        }
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == ''){
            exit('{"result":"error", "campo":"ruc_proveedor"}');
        }
        if ($this->input->post('moneda') == '' || $this->input->post('moneda') == '0'){
            exit('{"result":"error", "campo":"moneda"}');
        }
        if ($this->input->post('estado') == '0' && trim($this->input->post('observacion')) == '') {
            exit('{"result":"error", "campo":"observacion"}');
        }

        $lista_guiarem = $this->guiarem_model->listar_ocompra($codigo, $tipo_oper);
        if ($this->input->post('estado') == '0' && count($lista_guiarem) > 0){
            exit('{"result":"error", "msj":"Esta orden de ' . ($tipo_oper == 'V' ? 'venta' : 'compra') . ' no se puede anular debido a que esta enlazada a una guia de remision"}');
        }
       
        $filter = new stdClass();
        if ($tipo_oper == 'V') {
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $filter->PRESUP_Codigo = NULL;
            if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
                $filter->PRESUP_Codigo = $this->input->post('presupuesto');
        } else {
            $filter->PROVP_Codigo = $this->input->post('proveedor');
            $filter->COTIP_Codigo = NULL;
            if ($this->input->post('cotizacion') != '' && $this->input->post('cotizacion') != '0')
                $filter->COTIP_Codigo = $this->input->post('cotizacion');
        }
        $filter->OCOMC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->OCOMC_Serie = $this->input->post('serie');

        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->OCOMC_descuento100 = $this->input->post('descuento');

        if ($this->input->post('cboVendedor') != '' && $this->input->post('cboVendedor') != '0') // VENDEDOR
            $filter->OCOMC_MiPersonal = $this->input->post('cboVendedor');
        
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0')
            $filter->OCOMC_Personal = $this->input->post('contacto');

        if ($this->input->post('ordencompraempresa') != '' && $this->input->post('ordencompraempresa') != '0') // EL CODIGO DE ORDEN DE COMPRA SE GUARDAN EN PERSONA AUTORIZADA
            $filter->OCOMC_PersonaAutorizada = $this->input->post('ordencompraempresa');
            #$filter->OCOMC_Personal = $this->input->post('contacto');

        $filter->PROYP_Codigo = $this->input->post('obra');
        $filter->OCOMC_igv100 = $this->input->post('igv');
        $filter->OCOMC_percepcion100 = $this->input->post('percepcion');
        $filter->OCOMC_subtotal = $this->input->post('preciototal');
        $filter->OCOMC_descuento = $this->input->post('descuentotal');
        $filter->OCOMC_Entrega = $this->input->post('tiempo_entrega');
        $filter->OCOMC_igv = $this->input->post('igvtotal');
        $filter->OCOMC_total = $this->input->post('importetotal');
        $filter->PROYP_Codigo = $this->input->post("proyecto");
        $filter->OCOMC_percepcion = $this->input->post('percepciontotal');
        $filter->CENCOSP_Codigo = $this->input->post('centro_costo');
        $filter->OCOMC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->ALMAP_Codigo = NULL;
        if ($this->input->post('almacen') != '' && $this->input->post('almacen') != '0')
            $filter->ALMAP_Codigo = $this->input->post('almacen');
        
        $filter->FORPAP_Codigo = NULL;
        if ($this->input->post('formapago') != '' && $this->input->post('formapago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('formapago');
        
        $filter->OCOMC_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->OCOMC_FechaEntrega = NULL;
        if ($this->input->post('fechaentrega') != '')
            $filter->OCOMC_FechaEntrega = human_to_mysql($this->input->post('fechaentrega'));
        
        $filter->OCOMC_EnvioDireccion = $this->input->post('envio_direccion');
        $filter->OCOMC_FactDireccion = $this->input->post('fact_direccion');
        $filter->OCOMC_FlagEstado = $this->input->post('estado');
        $filter->OCOMC_CtaCteSoles = $this->input->post('ctactesoles');
        $filter->OCOMC_CtaCteDolares = $this->input->post('ctactedolares');
        $filter->CPC_TipoDocumento = $this->input->post('tipoComprobante');

        $filter->OCOMP_TDC = $this->input->post('moneda') == 4 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');
        $filter->OCOMP_TDC_opcional = $this->input->post('moneda') == 4 ? $this->input->post('tdcDolar') : 0;
        
        $this->ocompra_model->modificar_ocompra($codigo, $filter);

        $prodcodigo = $this->input->post('prodcodigo');
        $flagBS = $this->input->post('flagBS');
        $prodpu = $this->input->post('prodpu');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $proddescuento2 = $this->input->post('proddescuento2');
        $prodigv = $this->input->post('prodigv');
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $proddescri = $this->input->post('proddescri');
        $pendiente = $this->input->post('pendiente');
        $oventas = $this->input->post("oventacod");
        $cantreal = $this->input->post('cantreal');
        $tafectacion = $this->input->post('tafectacion');
        #$lote = $this->input->post('idLote');

        $observacionesdetalle = $this->input->post("prodobservacion");
        $fleteDetalle = $this->input->post("flete");
        //$ordencompra= $this->input->post("ordencompraventa");

        if (is_array($detacodi)) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];
                $filter = new stdClass();
                $filter->OCOMP_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                $filter->OCOMDEC_Cantidad = $prodcantidad[$indice];                      

                if ($flagBS == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                #$filter->LOTP_Codigo = $lote[$indice];
                
                $filter->OCOMDEC_Descuento100 = $proddescuento100[$indice];
                $filter->OCOMDEC_Igv100 = $prodigv100[$indice];
                $filter->AFECT_Codigo = ($tafectacion[$indice] != '' && $tafectacion[$indice] != 0) ? $tafectacion[$indice] : 1;
                $filter->OCOMDEC_Pu = $prodpu[$indice];
                $filter->OCOMDEC_Subtotal = $prodprecio[$indice];
                $filter->OCOMDEC_Descuento = $proddescuento[$indice];
                $filter->OCOMDEC_Descuento2 = 0;
                $filter->OCOMDEC_Igv = $prodigv[$indice];
                $filter->OCOMDEC_Total = $prodimporte[$indice];
                $filter->OCOMDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->OCOMDEC_Descripcion = strtoupper($proddescri[$indice]);
                $filter->OCOMDEC_Observacion = $observacionesdetalle[$indice];
                $filter->OCOMDEC_flete = isset($fleteDetalle[$indice]) ? $fleteDetalle[$indice] : 0;

                $filter->OCOMP_Codigo_venta = $oventas[$indice];
                $pendientef = $pendiente[$indice]-$prodcantidad[$indice];
                $codovente = $oventas[$indice];
             
                if ($detalle_accion == 'n') {
                    $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                    $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];

                    $this->ocompradetalle_model->insertar($filter);

                    if($tipo_oper == 'C'  && !is_null($codovente) && $codovente != ''  ){
                        $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, $filter->OCOMDEC_Cantidad);
                    }
                }
                else
                    if ($detalle_accion == 'm') {
                        //canr real(v)==cantidad(c)  y cantiudad(c)==pendien(c)
                        if($tipo_oper == 'V'){
                            if($cantreal[$indice] == $prodcantidad[$indice] && $prodcantidad[$indice] == $pendiente[$indice]){
                                $filter->OCOMDEC_Pendiente = $pendiente[$indice];
                                $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];
                            }
                            else{
                                $cantmed = $prodcantidad[$indice]-$cantreal[$indice];
                                $cantnew =$cantmed+$pendiente[$indice];
                                $filter->OCOMDEC_Pendiente = $cantnew;
                                if(isset($filter->OCOMDEC_Pendiente_pago)) {
                                    $filter->OCOMDEC_Pendiente_pago += $cantmed;
                                }
                                else {
                                    $this->ocompra_model->modificar_pendiente_pago_by_id_detalle_venta($valor, - $cantmed);
                                }
                            }
                            #modificamos la cantidad real de todos aquellos oc que contengan el item
                        }
                        else{
                            $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                            $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];
                        }
                        $this->ocompradetalle_model->modificar($valor, $filter);

                        if($tipo_oper == 'C' && !is_null($codovente) && $codovente != ''){
                            $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, - $pendientef);
                        }
                }
                else
                    if ($detalle_accion == 'e') {
                        $this->ocompradetalle_model->eliminar($valor);
                        if($tipo_oper == 'C'  && !is_null($codovente) && $codovente != ''  ){
                            $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, -($filter->OCOMDEC_Cantidad));
                        }
                    }
            }
        }
        foreach ($oventas as  $codoventa) {
            if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != '' ){
                $detalle_ocompra=$this->ocompra_model->esta_importado($codoventa);
                if ($detalle_ocompra) {
                   $this->ocompra_model->modificar_flagTerminado_oventa($codoventa, "1");
                }

            }
        }

        if ( $this->input->post('estado') == "1" ){
            $this->lib_props->sendMail(60, $codigo); # "Seguimiento de Cotizaciones",
        }
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }

    public function eliminar_ocompra()
    {
        $codigo = $this->input->post('codigo');
        $this->ocompra_model->eliminar($codigo);
    }

    public function evaluar_ocompra()
    {
        $flag = $this->input->post('flag');

        $checkO = $this->input->post('checkO');
        if (is_array($checkO))
            $this->ocompra_model->evaluar_ocompra($flag, $checkO);

        if (count($this->permiso_model->busca_permiso($this->rol, 38)) > 0)
            $this->ocompras(0, 1);
        else
            $this->index();
    }

    public function ocompra_ver_pdf($codigo)
    {
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                $this->ocompra_ver_pdf_formato1($codigo);
                break;
            case 2:  //Formato para jimmyplat
                $this->ocompra_ver_pdf_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->ocompra_ver_pdf_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->ocompra_ver_pdf_formato4($codigo);
                break;
            default:
                $this->ocompra_ver_pdf_formato1($codigo);
                break;
        }
    }

    public function ocompra_ver_pdf_conmenbrete($codigo, $flagPdf = 1)
    {
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                #$this->ocompra_ver_pdf_conmenbrete_formato1($codigo, $flagPdf);
                $this->ocompra_ver_pdf_a4($codigo, $flagPdf);
                break;
            case 2:  //Formato para jimmyplat
                $this->ocompra_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->ocompra_ver_pdf_conmenbrete_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->ocompra_ver_pdf_conmenbrete_formato4($codigo);
                break;
            default:
                $this->ocompra_ver_pdf_conmenbrete_formato1($codigo, $flagPdf);
                break;
        }
    }

    public function ocompra_ver_pdf_formato1($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;

        $datos_moneda = $this->moneda_model->obtener($moneda);

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_MiPersonal);
        $mi_nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $mi_nombre_area = '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';

        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax),
            array('c1' => utf8_decode_seguro('Atención:'), 'c2' => utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'c3' => '', 'c4' => '')
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));

        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_cantidad,
                    'col3' => $prod_unidad,
                    'col4' => $prod_codigo,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Cant',
            'col3' => 'Und',
            'col4' => utf8_decode_seguro('Código'),
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 35, 'justification' => 'center'),
                'col3' => array('width' => 40, 'justification' => 'center'),
                'col4' => array('width' => 67, 'justification' => 'left'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');
        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento    ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.           ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción') . '   ' . $percepcion100 . ' %', 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 370, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 75, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 155 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $mi_nombre_contacto . ($mi_nombre_area != '' ? ' - AREA: ' . $mi_nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Cta. Cte. Soles") . '           : ' . $ctactesoles);
        $this->cezpdf->addText($positionx2, $positiony2 - 112, 9, utf8_decode_seguro("Cta. Cte. Dólares") . '        : ' . $ctactedolares);
        $this->cezpdf->addText($positionx2, $positiony2 - 127, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 146, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_formato2($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';


        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');
        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_formato3($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';


        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');

        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_formato4($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';

        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');
        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_conmenbrete_formato1($codigo, $flagPdf)
    {
        $this->load->model('maestros/almacen_model');

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;

        $tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $tdc = $datos_ocompra[0]->OCOMP_TDC;

        $datos_moneda = $this->moneda_model->obtener($moneda);

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_MiPersonal);
        $mi_nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $mi_nombre_area = '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';
        $datos_compania = $this->compania_model->obtener_compania($this->compania);
        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);

        $compa = $this->compania;

        /* Cabecera */
        $delta = 20;

        if ($tipo_oper == 'C') {
            $this->cezpdf = new Cezpdf('a4');
            if ($flagPdf == 1) {
                $img = 'images/img_db/menbrete'.$compa.'.jpg';
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img)); 
                //$this->cezpdf->ezImage("images/menbrete4.jpg", 0, 536, 'none', 'left');
            } else {
                $img = 'images/img_db/menbrete'.$compa.'.jpg';
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img)); 
                //$this->cezpdf->ezImage("images/img_db/logo_instrume_unido1.jpg", 1, 1, 'none', 'left');
            }
        }
        else
            if ($tipo_oper == 'V') {
                $img = 'images/img_db/menbrete'.$compa.'.jpg';    
                $this->cezpdf = new Cezpdf('a4'); /// asi taba   , 'landscape'
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img)); 
        }

        $this->cezpdf->ezText(($tipo_oper == 'V' ? 'COTIZACION - ' : ($datos_ocompra[0]->OCOMC_FlagBS == "S" ? 'S-' : 'OC - ')) . str_pad($numero, 4, '0', STR_PAD_LEFT) . '-'. strftime("%y", strtotime($fecha)), 10, array("leading" => 110, 'left' => 402));
        $this->cezpdf->ezText('<b>Fecha:              ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 50 - $delta, 'left' => 5));
        $this->cezpdf->ezText('', '', array("leading" => 40));
        $this->cezpdf->ezText('1. DATOS' . ($tipo_oper == 'C' ? ' DEL PROVEEDOR : ' : ' DEL CLIENTE :' ), 8, array("left" => 2));
        $this->cezpdf->ezText(utf8_decode_seguro('2. CONDICIONES GENERALES'), 8, array("leading" => 0,"left" => 355));

        $data_cabecera = array(
            array('c1' => utf8_decode_seguro($tipo_oper == 'C' ? 'PROVEEDOR' : 'CLIENTE').'               :  '.($nombres)),
            array('c1' => 'RUC'.'                       :  '.($ruc)),
            array('c1' => utf8_decode_seguro('DIRECCION:').'          :  '. utf8_decode_seguro($direccion))
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '193',
            'fontSize' => 7,
            'cols' => array(
                'c1' => array('width' => 315, 'justification' => 'left'),
               
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 0));
        $data_cabecera = array(
           array('c1' => utf8_decode_seguro('CONTACTO').'           :  '.$mi_nombre_contacto),
            array('c1' => 'TELEFONO'.'            :           '.$telefono.'        E-mail: '),
            array('c1' => utf8_decode_seguro('MOVIL:').'                   :  ')
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '193',
            'fontSize' => 7,
            'cols' => array(
                'c1' => array('width' => 315, 'justification' => 'left'),
            )
        ));

         $this->cezpdf->ezText('', '', array("leading" => -73));
        $data_cabecera = array(
           array('c1' => utf8_decode_seguro('FORMA DE PAGO :')),
            array('c1' =>  utf8_decode_seguro($nombre_formapago)),
            array('c1' =>  '        '),
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '468',
            'fontSize' => 7,
            'cols' => array(
                'c1' => array('width' => 150, 'justification' => 'left'),
            )
        ));

        $this->cezpdf->ezText('', '', array("leading" => 0));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('MONEDA : ').'     '.$moneda_nombre),
            array('c1' =>  '        '),
            array('c1' =>  'TC. ' . $tdc),
            
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '468',
            'fontSize' => 7,
            'cols' => array(
                'c1' => array('width' => 150, 'justification' => 'left'),
            )
        ));

        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */

        $positionx2 = 35;
        $positiony2 = 270;

         //juntando los productos
        $lista_productos_distinct = array();
        if(count($datos_detalle_ocompra) > 0){
            foreach ($datos_detalle_ocompra as $product) {
                if(!isset($lista_productos_distinct[$product->PROD_Codigo])){
                    $lista_productos_distinct[$product->PROD_Codigo] = $product;
                }else {
                    $lista_productos_distinct[$product->PROD_Codigo]->OCOMDEC_Cantidad += $product->OCOMDEC_Cantidad;
                    $lista_productos_distinct[$product->PROD_Codigo]->OCOMDEC_Subtotal = ($lista_productos_distinct[$product->PROD_Codigo]->OCOMDEC_Cantidad * $product->OCOMDEC_Pu);
                }
            }
        }

        $db_data = array();
        $contador = 0;
        if (count($lista_productos_distinct) > 0) {
            foreach ($lista_productos_distinct as $indice => $valor) {
                $positiony2 -= 45;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu_ConIgv;
                $importe = $valor->OCOMDEC_Total;
                $descuento =$valor->OCOMDEC_Descuento100;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                #$prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre).(!is_null($valor->OCOMDEC_Observacion) && trim($valor->OCOMDEC_Observacion) != "" ? "\n-------------------------------------\n".$valor->OCOMDEC_Observacion."\n-------------------------------------" : '');
                $prod_nombre = $datos_producto[0]->PROD_Nombre; // 
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                #$prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario; // 
                $prod_unidad = is_null($datos_unidad) ? '' : $datos_unidad[0]->UNDMED_Descripcion;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $contador + 1,
                    'col2' => $prod_codigo,
                    'col3' => utf8_decode_seguro($prod_nombre),
                    'col4' => $prod_cantidad,
                    'col5' => $prod_unidad,
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($descuento, 2),
                    'col8' => number_format($importe, 2)
                );

                $contador++;
            }
        }
        $col_names = array(
            'col1' => 'ITEM',
            'col2' => utf8_decode_seguro('CODIGO'),
            'col3' => utf8_decode_seguro('DESCRIPCION'),
            'col4' => 'CANT',
            'col5' => 'UND',
            'col6' => 'P.U',
            'col7' => 'DSCTO.',
            'col8' => 'TOTAL',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => '295',
            'fontSize' => 6,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 60, 'justification' => 'center'),
                'col3' => array('width' => 220),
                'col4' => array('width' => 35, 'justification' => 'right'),
                'col5' => array('width' => 45, 'justification' => 'right'),
                'col6' => array('width' => 40, 'justification' => 'right'),
                'col7' => array('width' => 45, 'justification' => 'right'),
                'col8' => array('width' => 45, 'justification' => 'right')
            )
        ));
         $this->cezpdf->ezText('', '', array("leading" => 10));
        
        $data_subtotal = array(
            array('cols0' => '                      ', 'cols1' => 'SUBTOTAL', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '295',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 360, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 75, 'justification' => 'right')
            )
        ));
         $data_subtotal = array(
             array('cols0' => '', 'cols1' => 'I.G.V.           ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '295',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 360, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 75, 'justification' => 'right')
            )
        ));
        $data_subtotal = array(
             array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'TOTAL', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '295',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 360, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 75, 'justification' => 'right')
            )
        ));


        $this->cezpdf->ezText('', '', array("leading" => 15));
         $data_subtotal = array(
             array('cols0' => 'PLAZO DE ENTREGA :',  'cols1' => utf8_decode_seguro($tiempo_entrega)),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '178',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 140, 'justification' => 'left'),
                'cols1' => array('width' => 140, 'justification' => 'left'),
               )
        ));
         $this->cezpdf->ezText('', '', array("leading" => 0));
         $data_subtotal = array(
             array('cols0' => 'LUGAR DE ENTREGA :',  'cols1' => utf8_decode_seguro($lugar_entrega)),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '178',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 140, 'justification' => 'left'),
                'cols1' => array('width' => 140, 'justification' => 'left'),
               )
        ));

        $this->cezpdf->ezText('', '', array("leading" => 30));
        $data_subtotal = array(
             array('cols0' => '____________________________________'),
             array('cols0' => '                        '.' Aprobado por   '),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '475',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 200, 'justification' => 'left'),
               
               )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);

    }

    public function ocompra_ver_pdf_a4($codigo, $flagPdf = 1, $enviarcorreo = false){
        $this->lib_props->ocompra_pdf($codigo, $flagPdf, $enviarcorreo);
        return NULL;
    }

    public function ocompra_ver_pdf_rango($inicio = 1, $fin = 20, $tipo_oper = "V", $flagPdf = 0, $enviarcorreo = false){
        $this->lib_props->ocompra_rango(NULL, $inicio, $fin, $tipo_oper, $flagPdf, $enviarcorreo);
        return NULL;
    }

    public function imprimirOcompraFiltro($tipo_oper = "V", $fechai = "", $fechaf = "", $nombre_cliente = "", $ruc_cliente = "", $producto = "", $vendedor = ""){
        $filter = new stdClass();

        $filter->tipo_oper = $tipo_oper;
        $filter->fechai = ( $fechai == "0") ? date("Y-m-d") : $fechai;
        $filter->fechaf = ( $fechaf == "0") ? date("Y-m-d") : $fechaf;
        $filter->nombre_cliente = ( $nombre_cliente == "0" ) ? "" : $nombre_cliente;
        $filter->ruc_cliente = ( $ruc_cliente == "0" ) ? "" : $ruc_cliente;
        $filter->producto = ( $producto == "0" ) ? "" : $producto;
        $filter->vendedor = ( $vendedor == "0" ) ? "" : $vendedor;
        
        $this->lib_props->ocompra_rango($filter, "", "", $tipo_oper, 0, false);
    }

    public function ocompra_ver_pdf_conmenbrete_formato2($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/jimmyplast_fondo.jpg'));

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';


        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');
        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_conmenbrete_formato3($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/instrume_fondo_ocompra.jpg'));

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';


        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');

        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ocompra_ver_pdf_conmenbrete_formato4($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        if ($this->compania == 1)
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_fondo.jpg'));
        else
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_jmb_fondo.jpg'));

        /* Datos principales */
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;

        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');

        $nombre_almacen = '';
        if ($almacen != '') {
            $datos_almacen = $this->almacen_model->obtener($almacen);
            $nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
            $datos_formapago = $this->formapago_model->obtener($formapago);
            $nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        if ($tipo_oper == 'C') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            $nombres = $datos_proveedor->nombre;
            $ruc = $datos_proveedor->ruc;
            $telefono = $datos_proveedor->telefono;
            $direccion = $datos_proveedor->direccion;
            $fax = $datos_proveedor->fax;
        } else {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            $nombres = $datos_cliente->nombre;
            $ruc = $datos_cliente->ruc;
            $telefono = $datos_cliente->telefono;
            $direccion = $datos_cliente->direccion;
            $fax = $datos_cliente->fax;
        }

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';


        /* Cabecera */
        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));

        $this->cezpdf->ezText('Orden de ' . ($tipo_oper == 'C' ? 'Compra' : 'Venta') . ' Nro. ' . $numero, 17, array("leading" => 40, 'left' => 155));
        $this->cezpdf->ezText('<b>Fecha:  ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 40 - $delta, 'left' => 442));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('Señor(es):'), 'c2' => utf8_decode_seguro($nombres), 'c3' => utf8_decode_seguro('Teléfono:'), 'c4' => $telefono),
            array('c1' => 'RUC:', 'c2' => $ruc, 'c3' => utf8_decode_seguro('Móvil:'), 'c4' => ''),
            array('c1' => utf8_decode_seguro('Dirección:'), 'c2' => utf8_decode_seguro($direccion), 'c3' => 'Fax:', 'c4' => $fax)
        );
        $this->cezpdf->ezTable($data_cabecera, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'c1' => array('width' => 60, 'justification' => 'left'),
                'c2' => array('width' => 335, 'justification' => 'left'),
                'c3' => array('width' => 60, 'justification' => 'left'),
                'c4' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '', array("leading" => 10));
        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoUsuario;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => $prod_cantidad,
                    'col4' => $prod_unidad,
                    'col5' => utf8_decode_seguro($prod_nombre),
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($importe, 2)
                );
            }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => utf8_decode_seguro('Código'),
            'col3' => 'Cant',
            'col4' => 'Und',
            'col5' => utf8_decode_seguro('Descripción'),
            'col6' => 'P.U',
            'col7' => 'Total',
        );
        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 67, 'justification' => 'left'),
                'col3' => array('width' => 35, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 68, 'justification' => 'right'),
                'col7' => array('width' => 70, 'justification' => 'right')
            )
        ));
        $this->cezpdf->ezText('', '');
        /*         * Sub Totales* */
        $data_subtotal = array(
            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
            array('cols0' => '', 'cols1' => 'Descuento  ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
            array('cols0' => '', 'cols1' => 'I.G.V.        ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción'), 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 380, 'justification' => 'left'),
                'cols1' => array('width' => 80, 'justification' => 'left'),
                'cols3' => array('width' => 65, 'justification' => 'right')
            )
        ));
        /* Observaciones */
        $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 135 + $delta;
        $this->cezpdf->addText($positionx2, $positiony2, 9, "<b>TERMINOS DE " . ($tipo_oper == 'C' ? 'COMPRA' : 'VENTA') . "</b>");
        $this->cezpdf->addText($positionx2, $positiony2 - 14, 9, utf8_decode_seguro("Almacén                     ") . ': ' . utf8_decode_seguro($nombre_almacen));
        $this->cezpdf->addText($positionx2, $positiony2 - 28, 9, "Cond. de pago           " . ': ' . utf8_decode_seguro($nombre_formapago));
        $this->cezpdf->addText($positionx2, $positiony2 - 42, 9, "Lugar de entrega        " . ': ' . utf8_decode_seguro($lugar_entrega));
        $this->cezpdf->addText($positionx2, $positiony2 - 56, 9, "Facturar en                 " . ': ' . utf8_decode_seguro($lugar_factura));
        $this->cezpdf->addText($positionx2, $positiony2 - 70, 9, utf8_decode_seguro("Fecha límite entrega  ") . ': ' . $fecha_entrega);
        $this->cezpdf->addText($positionx2, $positiony2 - 84, 9, utf8_decode_seguro("Contacto                     ") . ': ' . $nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : ''));
        $this->cezpdf->addText($positionx2, $positiony2 - 98, 9, utf8_decode_seguro("Observación               ") . ': ' . $observacion);
        $this->cezpdf->addText($positionx2, $positiony2 - 126, 9, utf8_decode_seguro("<b>IMPORTANTE: Esta Orden de Compra no es válida sin El Sello y Firma del Jefe de Compras</b>"));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ventana_muestra_ocompra($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = '', $ventana = ''){
        // $formato: SELECT_ITEM, SELECT_HEADER, $docu_orig: DOCUMENTO QUE SOLICITA LA REFERENCIA, FACTURA, GUIA DE REMISION, ETC
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($tipo_oper == 'V') {
            $cliente = $codigo;
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
            $filter = new stdClass();
            $filter->cliente = $cliente;
        } else {
            $proveedor = $codigo;
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
            $filter = new stdClass();
            $filter->proveedor = $proveedor;
        }


        $lista_comprobante = $this->ocompra_model->buscar_ocompra_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        /*foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                if ($ventana != 'OC') {
                    //$select = "<a href='" . base_url() . "index.php/compras/ocompra/comprobante_nueva_ocompra/" . $value->OCOMP_Codigo . "/" . $tipo_oper . "' id='linkVerOrdenCompra' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";
                    $select = "<a href='javascript:;' onclick='seleccionar_ocompra(" . $value->OCOMP_Codigo . ",".$value->OCOMC_Serie." ," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";
                } else {
                    $select = "<a href='javascript:;' onclick='seleccionar_ocompra(" . $value->OCOMP_Codigo . ",0," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";

                }
            }
            $lista[] = array(mysql_to_human($value->OCOMC_Fecha), $value->OCOMC_Serie, $value->OCOMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total), $ver, $select);
        }*/

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['docu_orig'] = $docu_orig;
        $data['formato'] = $formato;
        if ($ventana != 'OC') {
            $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('ventas/ventana_muestra_comprobante', $data);
        } else {
            $data['form_open'] = form_open(base_url() . "index.php/compras/ventana_muestra_ocompra", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('compras/ventana_muestra_ocompra', $data);
        }
    }

    public function datatable_ventana_ocompra(){

        $data['compania'] = $this->compania;

        $columnas = array(
                            0 => "OCOMC_Fecha",
                            1 => "OCOMC_Serie",
                            2 => "OCOMC_Numero"
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

        $filter->fecha = $this->input->post("fecha");
        $filter->serie = $this->input->post("serie");
        $filter->numero = $this->input->post("numero");

        $tipo_oper = $this->input->post("tipo_oper");
        $tipo_docu = $this->input->post("tipo_docu");
        $codigo = $this->input->post("cliente"); # cliente o proveedor

        $filter->tipo_oper = $tipo_oper;
        $filter->tipo_docu = $tipo_docu;
        
        $cliente = '';
        $proveedor = '';

        if ($tipo_oper == 'V')
            $cliente = $codigo;
        else
            $proveedor = $codigo;
        
        $filter->cliente = $cliente;
        $filter->proveedor = $proveedor;

        $lista_comprobante = $this->ocompra_model->getOcompraAsoc($filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            $select = "<a href='javascript:;' onclick=\"seleccionar_ocompra($value->OCOMP_Codigo)\"><img src='".base_url()."images/ir.png' width='16' height='16' border='0' title='Seleccionar Documento'></a>";
            
            $lista[] = array(
                                0 => $value->OCOMC_Fecha,
                                1 => $value->OCOMC_Serie,
                                2 => $value->OCOMC_Numero,
                                3 => $value->numdoc,
                                4 => $value->nombre,
                                5 => $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total),
                                6 => $ver,
                                7 => $select
                            );
        }

        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;
        $filterAll->tipo_docu = $tipo_docu;
        $filterAll->cliente = $cliente;
        $filterAll->proveedor = $proveedor;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->ocompra_model->getOcompraAsoc($filterAll)),
                            "recordsFiltered" => intval( count($this->ocompra_model->getOcompraAsoc($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);

    }

    //public function ventana_muestra_oventa($tipo_oper = 'V', $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = '', $ventana = '')
    public function ventana_muestra_oventa($tipo_oper = 'V',$formato = 'SELECT_HEADER', $docu_orig = "", $comprobante = '', $ventana = 'OC')
    {


        $lista_comprobante = $this->ocompra_model->buscar_oventa_asoc('V', $comprobante);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                if ($ventana != 'OC') {
                    $select = "<a href='javascript:;' onclick='seleccionar_oventa(" . $value->OCOMP_Codigo . ",".$value->OCOMC_Serie." ," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";
                } else {
                    $select = "<a href='javascript:;' onclick='seleccionar_oventa(" . $value->OCOMP_Codigo . ",".$value->OCOMC_Serie."," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";

                }
            }
            $lista[] = array(mysql_to_human($value->OCOMC_Fecha), $value->OCOMC_Serie, $value->OCOMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total), $ver,$select);
        }

        $data['lista'] = $lista;
        $data["cliente"] = "";
        $data['nombre_cliente'] = "";
        $data['ruc_cliente'] = "";
        $data['proveedor'] = "";
        $data['nombre_proveedor'] = "";
        $data['ruc_proveedor'] = "";
        $data['almacen'] = "";
        $data['comprobante'] = "OV";
        $data['tipo_oper'] = $tipo_oper;
        $data['docu_orig'] = "";
        $data['formato'] = $formato;
        if ($ventana != 'OC') {
            $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('ventas/ventana_muestra_comprobante', $data);
        }else if($ventana != "OC"){
                    $select = "<a href='javascript:;' onclick='seleccionar_oventa(" . $value->OCOMP_Codigo . ",". $value->OCOMC_Serie. " ," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";
                } else {
            $data['form_open'] = form_open(base_url() . "index.php/compras/ventana_muestra_ocompra", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('compras/ventana_muestra_ocompra', $data);
        }
    }

    public function ventana_muestra_ocompraCom($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $tipo_doc = '', $almacen = "", $comprobante = '')
    {
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($tipo_oper == 'V') {
            $cliente = $codigo;
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
            $filter = new stdClass();
            $filter->cliente = $cliente;
        } else {
            $proveedor = $codigo;
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
            $filter = new stdClass();
            $filter->proveedor = $proveedor;
        }


        $lista_comprobante = $this->ocompra_model->buscar_ocompra_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_ocompra($value->OCOMP_Codigo)'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                //$select = "<a href='" . base_url() . "index.php/compras/ocompra/comprobante_nueva_ocompra/" . $value->OCOMP_Codigo . "/" . $tipo_oper . "' id='linkVerOrdenCompra' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";
            }

            $tipoOrden = $value->OCOMC_TipoOperacion;
            $valorOrden = "";
            if ($tipoOrden == 'C') {
                $valorOrden = 1;
            } else {
                $valorOrden = 2;
            }

            $select = "<a href='#' onClick='seleccionarOdenCompra($value->OCOMP_Codigo)' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";

            $lista[] = array(mysql_to_human($value->OCOMC_Fecha), /*$value->OCOMC_Serie*/$value->OCOMC_FlagBS == "S" ? "OS" : "OC", $value->OCOMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total), $ver, $select, $value->OCOMP_Codigo);
        }

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_doc'] = $tipo_doc;
        $data['formato'] = $formato;
        $data['form_open'] = form_open(base_url() . "index.php/almacen/producto/ventana_muestra_guiarem", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "tipo_doc" => $tipo_doc, "formato" => $formato));

        $this->load->view('almacen/ventana_muestra_guiarem', $data);
    }

    public function relacionar_oc(){
        $codigo = $this->input->post("ocompra");
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        if ($datos_ocompra != NULL){
            foreach ($datos_ocompra as $key => $value) {
                $operacion = ($value->OCOMC_TipoOperacion == "C") ? 1 : 2;
                $info = array(
                                    "ocompra" => $value->OCOMP_Codigo,
                                    "serie" => $value->OCOMC_Serie,
                                    "numero" => $value->OCOMC_Numero,
                                    "operacion" => $operacion,
                                    "vendedor" => $value->OCOMC_MiPersonal,
                                    "forma_pago" => $value->FORPAP_Codigo,
                                    "moneda" => $value->MONED_Codigo,
                                    "descuento" => $value->OCOMC_descuento100,
                                    "direccion_env" => $value->OCOMC_EnvioDireccion,
                                    "direccion" => $value->OCOMC_FactDireccion,
                                    "proyecto" => $value->PROYP_Codigo,
                                    "OCcliente" => $value->OCOMC_PersonaAutorizada,
                                );
            }
            $json = array( "result" => "success", "info" => $info);
        }
        else
            $json = array( "result" => "warning", "info" => NULL);
            
        echo json_encode($json);
    }

    public function ventana_muestra_ocompra_importacion($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $tipo_doc = '', $almacen = "", $comprobante = '')
    {
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($tipo_oper == 'V') {
            $cliente = $codigo;
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
            $filter = new stdClass();
            $filter->cliente = $cliente;
        } else {
            $proveedor = $codigo;
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
            $filter = new stdClass();
            $filter->proveedor = $proveedor;
        }


        $lista_comprobante = $this->ocompra_model->buscar_ocompra_importacion($tipo_oper, $comprobante, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_ocompra(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                //$select = "<a href='" . base_url() . "index.php/compras/ocompra/comprobante_nueva_ocompra/" . $value->OCOMP_Codigo . "/" . $tipo_oper . "' id='linkVerOrdenCompra' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";
            }

            $tipoOrden = $value->OCOMC_TipoOperacion;
            $valorOrden = "";
            if ($tipoOrden == 'C') {
                $valorOrden = 1;
            } else {
                $valorOrden = 2;
            }

            $select = "<a href='#' onClick='seleccionarOdenCompra(" . $value->OCOMP_Codigo . ", Number(" . $value->OCOMC_Serie . "), " . $value->OCOMC_Numero . ", " . $valorOrden . ")' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";

            $lista[] = array(mysql_to_human($value->OCOMC_Fecha), $value->OCOMC_Serie, $value->OCOMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total), $ver, $select, $value->OCOMP_Codigo);
        }

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_doc'] = $tipo_doc;
        $data['formato'] = $formato;
        $data['form_open'] = form_open(base_url() . "index.php/almacen/producto/ventana_muestra_guiarem", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "tipo_doc" => $tipo_doc, "formato" => $formato));

        $this->load->view('almacen/ventana_muestra_guiarem', $data);
    }

    public function obtener_detalles_ocompras()
    {
        $detalle = array();
        $codigo_moneda = 0;
        foreach ($this->ocompra_model->listarByIds($this->input->post("compras")) as $articulo) {
            $codigo_moneda = $articulo->MONED_Codigo;
            
            if(isset($detalle[$articulo->PROD_Codigo])) {
                $detalle[$articulo->PROD_Codigo]['cantidad'] = $detalle[$articulo->PROD_Codigo]['cantidad'] + $articulo->OCOMDEC_Cantidad;
                $detalle[$articulo->PROD_Codigo]['precio_uni_c_igv'] = $articulo->OCOMDEC_Pu_ConIgv;
                $detalle[$articulo->PROD_Codigo]['precio_uni_s_igv'] = $articulo->OCOMDEC_Pu;
                $detalle[$articulo->PROD_Codigo]['precio_total'] = $detalle[$articulo->PROD_Codigo]['precio_uni_s_igv'] * $detalle[$articulo->PROD_Codigo]['cantidad'];
            }else {
                $detalle[$articulo->PROD_Codigo] = array(
                        'codigo' => $articulo->PROD_Codigo,
                        'codigo_interno' => $articulo->PROD_CodigoUsuario,
                        'descripcion' => $articulo->OCOMDEC_Descripcion,
                        'cantidad' => $articulo->OCOMDEC_Cantidad,
                        'uni_med' => $articulo->UNDMED_Simbolo,
                        'uni_med_cod' => $articulo->UNDMED_Codigo,
                        'precio_uni_c_igv' => $articulo->OCOMDEC_Pu_ConIgv,
                        'precio_uni_s_igv' => $articulo->OCOMDEC_Pu,
                        'precio_total' => $articulo->OCOMDEC_Cantidad * $articulo->OCOMDEC_Pu,
                        'real' => $articulo
                    );
            }
        }

        echo json_encode(array(
                'detalle' => array_values($detalle),
                'ordenes' => $this->input->post("compras"),
                'moneda' => $codigo_moneda
            ));
    }

    public function obtener_detalle_cotizacion($cotizacion){
    	$this->load->model('compras/cotizacion_model');
        $datos_detalle_cotizacion = $this->cotizacion_model->obtener_detalle_cotizacion2($cotizacion);
        $listado = array();
        if (count($datos_detalle_cotizacion) > 0) {
            foreach ($datos_detalle_cotizacion as $indice => $valor) {
                $detcotizacion = $valor->COTDEP_Codigo;
                $pedido = $valor->PEDIP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->COTDEC_Cantidad;
                $datos_cotizacion = $this->cotizacion_model->obtener_cotizacion($cotizacion);
                $proveedor = $datos_cotizacion[0]->PROVP_Codigo;
                $almacen = $datos_cotizacion[0]->ALMAP_Codigo;
                $formapago = $datos_cotizacion[0]->FORPAP_Codigo;
                $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                $persona = $datos_proveedor[0]->PERSP_Codigo;
                $tipo = $datos_proveedor[0]->PROVC_TipoPersona;
                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $razon_social = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    $ruc = $datos_persona[0]->PERSC_Ruc;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $razon_social = $datos_empresa[0]->EMPRC_RazonSocial;
                    $ruc = $datos_empresa[0]->EMPRC_Ruc;
                }
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->COTDEP_Codigo = $detcotizacion;
                $objeto->PEDIP_Codigo = $pedido;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->COTDEC_Cantidad = $cantidad;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $listado[] = $objeto;
            }
        }
        $resultado = json_encode($listado);
        echo $resultado;
    }

    public function obtener_detalle_ocompra($ocompra)
    {
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($ocompra);
        $listado = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $ocompra = $valor->OCOMP_Codigo;
                $cotizacion = $valor->COTIP_Codigo;
                $pedido = $valor->PEDIP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $costo = $valor->OCOMDEC_Total;
                $datos_ocompra = $this->ocompra_model->obtener_ocompra($ocompra);
                $proveedor = $datos_ocompra[0]->PROVP_Codigo;
                $almacen = $datos_ocompra[0]->ALMAP_Codigo;
                $formapago = $datos_ocompra[0]->FORPAP_Codigo;

                $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                $razon_social = $datos_proveedor->nombre;
                $ruc = $datos_proveedor->ruc;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->OCOMP_Codigo = $ocompra;
                $objeto->COTIP_Codigo = $cotizacion;
                $objeto->PEDIP_Codigo = $pedido;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_GenericoIndividual = $flagGenInd;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Total = $costo;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $listado[] = $objeto;
            }
        }
        $resultado = json_encode($listado);
        echo $resultado;
    }

    public function obtener_detalle_ocompra2($ocompra)
    {
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra2($ocompra);
        $listado = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $ocompra = $valor->OCOMP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $costo = $valor->OCOMDEC_Pu;
                $costoPUconIgv = $valor->OCOMDEC_Pu_ConIgv;
                $costoTotal = $valor->OCOMDEC_Total;
                $subTotal = $valor->OCOMDEC_Subtotal;
                $igvocom = $valor->OCOMDEC_Igv;
                $igvocom100 = $valor->OCOMDEC_Igv100;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento100 = $valor->OCOMDEC_Descuento100;
                $flagGenInd = $valor->OCOMDEC_GenInd;
                
                $datos_ocompra = $this->ocompra_model->obtener_ocompra($ocompra);
                if ($datos_ocompra[0]->PROVP_Codigo == '') {
                    $proveedor = $datos_ocompra[0]->CLIP_Codigo;
                    $datos_proveedor = $this->cliente_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                } else {
                    $proveedor = $datos_ocompra[0]->PROVP_Codigo;
                    $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                }
                $almacen = $datos_ocompra[0]->ALMAP_Codigo;
                $formapago = $datos_ocompra[0]->FORPAP_Codigo;
                $moned_codigo = $datos_ocompra[0]->MONED_Codigo;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = is_null($unidad_medida) ? NULL : $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                
                $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->OCOMP_Codigo = $ocompra;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->MONED_Codigo = $moned_codigo;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pu = $costo;
                $objeto->OCOMDEC_Igv = $igvocom;
                $objeto->OCOMDEC_Igv100 = $igvocom100;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento100 = $descuento100;
                $objeto->OCOMDEC_Pu_ConIgv = $costoPUconIgv;
                $objeto->OCOMDEC_Subtotal = $subTotal;
                $objeto->OCOMDEC_Total = $costoTotal;
                $objeto->OCOMDEC_GenInd = $flagGenInd;
                
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PROD_GenericoIndividual = $flagGenInd;
                $listado[] = $objeto;
            }
        }
        $resultado = json_encode($listado);
        echo $resultado;
    }
     /*
        public function obtener_detalle_ocompra2($ocompra, $tipo = NULL)
        {
            $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra2($ocompra);
            //var_dump($datos_detalle_ocompra);
            $lista_products = array();


            $ocompras_list = array();
            $clients_list = array();
            $provedores_list = array();
            $medidas_list = array();

            $prices_list = array();

            $listado = array();

            if (count($datos_detalle_ocompra) > 0) {
                foreach ($datos_detalle_ocompra as $indice => $valor) {
                    $detocompra = $valor->OCOMDEP_Codigo;
                    $ocompra = $valor->OCOMP_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad_medida = $valor->UNDMED_Codigo;
                    $cantidad = $valor->OCOMDEC_Cantidad;
                    $pendiente = $valor->OCOMDEC_Pendiente;
                    $costo = $valor->OCOMDEC_Pu;
                    $costoPUconIgv = $valor->OCOMDEC_Pu_ConIgv;
                    $costoTotal = $valor->OCOMDEC_Total;
                    $subTotal = $valor->OCOMDEC_Subtotal;
                    $igvocom = $valor->OCOMDEC_Igv;
                    $igvocom100 = $valor->OCOMDEC_Igv100;
                    $descuento = $valor->OCOMDEC_Descuento;
                    $descuento100 = $valor->OCOMDEC_Descuento100;
                    $flagGenInd = $valor->OCOMDEC_GenInd;
                    $pendientepago = $valor->OCOMDEC_Pendiente_pago;
                    #verificamos si es una importacion

                    if(!isset($ocompras_list[$ocompra])) $ocompras_list[$ocompra] = $this->ocompra_model->obtener_ocompra($ocompra);
                    
                    $datos_ocompra = $ocompras_list[$ocompra];

                    if ($datos_ocompra[0]->PROVP_Codigo == '') {
                        $proveedor = $datos_ocompra[0]->CLIP_Codigo;

                        if(!isset($clients_list[$proveedor])) $clients_list[$proveedor] = $this->cliente_model->obtener($proveedor);

                        $datos_proveedor = $clients_list[$proveedor];
                        $razon_social = $datos_proveedor->nombre;
                        $ruc = $datos_proveedor->ruc;
                    } else {
                        $proveedor = $datos_ocompra[0]->PROVP_Codigo;

                        if(!isset($provedores_list[$proveedor])) $provedores_list[$proveedor] = $this->proveedor_model->obtener($proveedor);

                        $datos_proveedor = $provedores_list[$proveedor];
                        $razon_social = $datos_proveedor->nombre;
                        $ruc = $datos_proveedor->ruc;
                    }
                    $almacen = $datos_ocompra[0]->ALMAP_Codigo;
                    $formapago = $datos_ocompra[0]->FORPAP_Codigo;
                    $moned_codigo = $datos_ocompra[0]->MONED_Codigo;

                    $datos_producto = $this->producto_model->obtener_producto($producto);

                    if(!is_null($unidad_medida) && !isset($medidas_list[$unidad_medida])) $medidas_list[$unidad_medida] = $this->unidadmedida_model->obtener($unidad_medida);

                    $datos_umedida = is_null($unidad_medida) ? NULL : $medidas_list[$unidad_medida];
                    $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                    $nombre_producto = $datos_producto[0]->PROD_Nombre;
                    //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                    
                    $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;
                    $objeto = new stdClass();
                    $objeto->OCOMDEP_Codigo = $detocompra;
                    $objeto->OCOMP_Codigo = $ocompra;
                    $objeto->PROD_Codigo = $producto;
                    $objeto->UNDMED_Codigo = $unidad_medida;
                    $objeto->MONED_Codigo = $moned_codigo;
                    $objeto->OCOMDEC_Cantidad = $cantidad;
                    $objeto->OCOMDEC_Pendiente = $pendiente;
                    $objeto->OCOMDEC_Pendiente_pago = $pendientepago;
                    $objeto->OCOMDEC_Pu = $costo;
                    $objeto->OCOMDEC_Igv = $igvocom;
                    $objeto->OCOMDEC_Igv100 = $igvocom100;
                    $objeto->OCOMDEC_Descuento = $descuento;
                    $objeto->OCOMDEC_Descuento100 = $descuento100;
                    $objeto->OCOMDEC_Pu_ConIgv = $costoPUconIgv;
                    $objeto->OCOMDEC_Subtotal = $subTotal;
                    $objeto->OCOMDEC_Total = $costoTotal;
                    $objeto->OCOMDEC_GenInd = $flagGenInd;
                    $objeto->OCOMDEC_Observacion = $valor->OCOMDEC_Observacion;
                    
                    $objeto->PROD_CodigoUsuario = $codigo_interno;
                    $objeto->PROD_Nombre = $nombre_producto;
                    $objeto->UNDMED_Simbolo = $nombre_unidad;
                    $objeto->Ruc = $ruc;
                    $objeto->RazonSocial = $razon_social;
                    $objeto->PROVP_Codigo = $proveedor;
                    $objeto->ALMAP_Codigo = $almacen;
                    $objeto->FORPAP_Codigo = $formapago;
                    $objeto->PROD_GenericoIndividual = $flagGenInd;
                    $objeto->PROYP_Codigo = $valor->PROYP_Codigo;
                    $objeto->PROYC_Nombre = $valor->PROYC_Nombre;

                    $objeto->es_importado = $igvocom100 == 0 ? 1 : 0;

                    #obtenemos el ultimo precio de la oc del producto
                    if(true) {
                        if(!isset($prices_list[$valor->PROD_Codigo])) $prices_list[$valor->PROD_Codigo] = $this->ocompra_model->get_last_price_by_id_product($valor->PROD_Codigo);

                        $last_price = $prices_list[$valor->PROD_Codigo];

                        if(count($last_price) > 0) {
                            $objeto->OCOMDEC_Pu = $last_price = $last_price[0]->OCOMDEC_Pu;
                        }else {
                           // $objeto->OCOMDEC_Pu = 0.0;
                        }
                        
                        #$objeto->OCOMDEC_Igv100 = $igvocom100;
                        $objeto->OCOMDEC_Igv = $objeto->OCOMDEC_Pu * ($igvocom100 / 100) * $valor->OCOMDEC_Pendiente;
                        #$objeto->OCOMDEC_Descuento = $descuento;
                        #$objeto->OCOMDEC_Descuento100 = $descuento100;
                        $objeto->OCOMDEC_Pu_ConIgv = $objeto->OCOMDEC_Pu + ($objeto->OCOMDEC_Pu * ($igvocom100 / 100));
                        $objeto->OCOMDEC_Subtotal = $objeto->OCOMDEC_Pu * $valor->OCOMDEC_Pendiente;
                        $objeto->OCOMDEC_Total = $objeto->OCOMDEC_Pu_ConIgv * $valor->OCOMDEC_Pendiente;

                    }

                    if(!is_null($tipo) && $datos_ocompra[0]->OCOMC_TipoOperacion == 'V' && $datos_producto[0]->PROD_FlagBienServicio != $tipo) 
                        continue;

                    $listado[] = $objeto;
                }
            }

            echo json_encode($listado);
        }

    */

    public function obtener_detalle_ocompra_for_comprobante($ocompra, $tipo = NULL)
    {
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra2($ocompra);

        $lista_products = array();
        /*if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $product) {
                $lista_products[] = $product;
                if(!isset($lista_products[$product->PROD_Codigo])) {
                    $lista_products[$product->PROD_Codigo] = $product;
                }else {
                    $lista_products[$product->PROD_Codigo]->OCOMDEC_Cantidad += $product->OCOMDEC_Cantidad;
                    $lista_products[$product->PROD_Codigo]->OCOMDEC_Subtotal = $lista_products[$product->PROD_Codigo]->OCOMDEC_Cantidad * $product->OCOMDEC_Pu;
                }
            }
        }*/

        $ocompras_list = array();
        $clients_list = array();
        $provedores_list = array();
        $medidas_list = array();

        $listado = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $ocompra = $valor->OCOMP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $pendiente = $valor->OCOMDEC_Pendiente;
                $costo = $valor->OCOMDEC_Pu;
                $costoPUconIgv = $valor->OCOMDEC_Pu_ConIgv;
                $costoTotal = $valor->OCOMDEC_Total;
                $subTotal = $valor->OCOMDEC_Subtotal;
                $igvocom = $valor->OCOMDEC_Igv;
                $igvocom100 = $valor->OCOMDEC_Igv100;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento100 = $valor->OCOMDEC_Descuento100;
                $flagGenInd = $valor->OCOMDEC_GenInd;
                $pendientepago = $valor->OCOMDEC_Pendiente_pago;
                #verificamos si es una importacion

                #referencia de la orden de venta
                $referencia_oventa = $this->ocompra_model->obtener_referencia_compra_by_id_detalle_venta($valor->OCOMP_Codigo_venta);

                if(!isset($ocompras_list[$ocompra])) $ocompras_list[$ocompra] = $this->ocompra_model->obtener_ocompra($ocompra);
                
                $datos_ocompra = $ocompras_list[$ocompra];

                if ($datos_ocompra[0]->PROVP_Codigo == '') {
                    $proveedor = $datos_ocompra[0]->CLIP_Codigo;

                    if(!isset($clients_list[$proveedor])) $clients_list[$proveedor] = $this->cliente_model->obtener($proveedor);

                    $datos_proveedor = $clients_list[$proveedor];
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                } else {
                    $proveedor = $datos_ocompra[0]->PROVP_Codigo;

                    if(!isset($provedores_list[$proveedor])) $provedores_list[$proveedor] = $this->proveedor_model->obtener($proveedor);

                    $datos_proveedor = $provedores_list[$proveedor];
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                }
                $almacen = $datos_ocompra[0]->ALMAP_Codigo;
                $formapago = $datos_ocompra[0]->FORPAP_Codigo;
                $moned_codigo = $datos_ocompra[0]->MONED_Codigo;

                $datos_producto = $this->producto_model->obtener_producto($producto);

                if(!is_null($unidad_medida) && !isset($medidas_list[$unidad_medida])) $medidas_list[$unidad_medida] = $this->unidadmedida_model->obtener($unidad_medida);

                $datos_umedida = is_null($unidad_medida) ? NULL : $medidas_list[$unidad_medida];
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                
                $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->OCOMP_Codigo = $ocompra;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->MONED_Codigo = $moned_codigo;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pendiente = $pendiente;
                $objeto->OCOMDEC_Pendiente_pago = $pendientepago;
                $objeto->OCOMDEC_Pu = $costo;
                $objeto->OCOMDEC_Igv = $igvocom;
                $objeto->OCOMDEC_Igv100 = $igvocom100;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento100 = $descuento100;
                $objeto->OCOMDEC_Pu_ConIgv = $costoPUconIgv;
                $objeto->OCOMDEC_Subtotal = $subTotal;
                $objeto->OCOMDEC_Total = $costoTotal;
                $objeto->OCOMDEC_GenInd = $flagGenInd;
                $objeto->OCOMDEC_Observacion = $valor->OCOMDEC_Observacion;
                
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PROD_GenericoIndividual = $flagGenInd;

                $objeto->PROYP_Codigo = isset($referencia_oventa->PROYP_Codigo) ? $referencia_oventa->PROYP_Codigo : 0;
                $objeto->PROYC_Nombre = isset($referencia_oventa->PROYC_Nombre) ? $referencia_oventa->PROYC_Nombre : 0;
                $objeto->OCOMP_Codigo_venta = $valor->OCOMP_Codigo_venta;
                $objeto->RazonSocialRef = "Almacen";
                $objeto->OCOMP_Codigo_venta_ref = 0;

                if(isset($referencia_oventa->CLIP_Codigo) && !is_null($referencia_oventa->CLIP_Codigo)) {
                    $ref_cliente = $this->cliente_model->obtener($referencia_oventa->CLIP_Codigo);
                    $objeto->RazonSocialRef = $ref_cliente->nombre;
                }

                if($referencia_oventa) {
                    $objeto->OCOMP_Codigo_venta_ref = $referencia_oventa->OCOMP_Codigo;
                }

                $objeto->es_importado = $igvocom100 == 0 ? 1 : 0;

                if(!is_null($tipo) && $datos_ocompra[0]->OCOMC_TipoOperacion == 'V' && $datos_producto[0]->PROD_FlagBienServicio != $tipo) 
                    continue;

                $listado[] = $objeto;
            }
        }

        echo json_encode($listado);
    }

    public function obtener_detalle_ocompra_importar($ocompra, $tipo = NULL)
    {
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra($ocompra);
        $lista_products = array();
        /*if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $product) {
                $lista_products[] = $product;
                /*if(!isset($lista_products[$product->PROD_Codigo])) {
                    $lista_products[$product->PROD_Codigo] = $product;
                }else {
                    $lista_products[$product->PROD_Codigo]->OCOMDEC_Pendiente += $sum_Cantidad = $product->OCOMDEC_Pendiente;
                    $lista_products[$product->PROD_Codigo]->OCOMDEC_Subtotal = $sum_Cantidad * $product->OCOMDEC_Pu;
                }*/
            /*}
        }*/

        $listado = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $ocompra = $valor->OCOMP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $pendiente = $valor->OCOMDEC_Pendiente;
                $costo = $valor->OCOMDEC_Pu;
                $costoPUconIgv = $valor->OCOMDEC_Pu_ConIgv;
                $costoTotal = $valor->OCOMDEC_Total;
                $subTotal = $valor->OCOMDEC_Subtotal;
                $igvocom = $valor->OCOMDEC_Igv;
                $igvocom100 = $valor->OCOMDEC_Igv100;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento100 = $valor->OCOMDEC_Descuento100;
                $flagGenInd = $valor->OCOMDEC_GenInd;

                #referencia ov
                $referencia_venta = $this->ocompra_model->obtener_referencia_compra_by_id_detalle_venta($valor->OCOMP_Codigo_venta);

                #verificamos si es una importacion
                
                $datos_ocompra = $this->ocompra_model->obtener_ocompra($ocompra);
                $ruc = "";
                $proveedor = "";

                if (isset($datos_ocompra[0]->CLIP_Codigo)) {
                    $proveedor = $datos_ocompra[0]->CLIP_Codigo;
                    $datos_proveedor = $this->cliente_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = isset($datos_proveedor->ruc) ? $datos_proveedor->ruc : "";
                } 

                if(isset($datos_ocompra[0]->PROVP_Codigo)) {
                    $proveedor = $datos_ocompra[0]->PROVP_Codigo;
                    $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = isset($datos_proveedor->ruc) ? $datos_proveedor->ruc : '';
                }

                $almacen = isset($datos_ocompra[0]->ALMAP_Codigo) ? $datos_ocompra[0]->ALMAP_Codigo : 0;
                $formapago = isset($datos_ocompra[0]->FORPAP_Codigo) ? $datos_ocompra[0]->FORPAP_Codigo : 0;
                $moned_codigo = isset($datos_ocompra[0]->MONED_Codigo) ? $datos_ocompra[0]->MONED_Codigo : 0;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = is_null($unidad_medida) ? NULL : $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                
                $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->OCOMP_Codigo = $ocompra;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->MONED_Codigo = $moned_codigo;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Pendiente = $pendiente;
                $objeto->OCOMDEC_Pu = $costo;
                $objeto->OCOMDEC_Igv = $igvocom;
                $objeto->OCOMDEC_Igv100 = $igvocom100;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento100 = $descuento100;
                $objeto->OCOMDEC_Pu_ConIgv = $costoPUconIgv;
                $objeto->OCOMDEC_Subtotal = $subTotal;
                $objeto->OCOMDEC_Total = $costoTotal;
                $objeto->OCOMDEC_GenInd = $flagGenInd;
                
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = "Almacen";
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PROD_GenericoIndividual = $flagGenInd;
                
                $objeto->PROYP_Codigo = isset($referencia_venta->PROYP_Codigo) ? $referencia_venta->PROYP_Codigo : 0;
                $objeto->PROYC_Nombre = isset($referencia_venta->PROYC_Nombre) ? $referencia_venta->PROYC_Nombre : 0;
                $objeto->OCOMP_Codigo_venta = $valor->OCOMP_Codigo_venta;

                $objeto->es_importado = $igvocom100 == 0 ? 1 : 0;
                $objeto->PROD_CodigoUsuario = $datos_producto[0]->PROD_CodigoUsuario;

                $objeto->OCOMDEC_flete = $valor->OCOMDEC_flete;

                if(isset($referencia_venta->CLIP_Codigo) && !is_null($referencia_venta->CLIP_Codigo)) {
                    $ref_cliente = $this->cliente_model->obtener($referencia_venta->CLIP_Codigo);
                    $objeto->RazonSocial = $ref_cliente->nombre;
                }

                if($referencia_venta) {
                    $objeto->OCOMP_Codigo_venta_ref = $referencia_venta->OCOMP_Codigo;
                }

                if(!is_null($tipo) && $datos_ocompra[0]->OCOMC_TipoOperacion == 'V' && $datos_producto[0]->PROD_FlagBienServicio != $tipo) 
                    continue;

                $listado[] = $objeto;
            }
        }

        echo json_encode($listado);
    }

    /* Combos */
     public function calcula_ocantidad_pendiente($ocodigo,$prod,$cant){
        $cant=$cant;
        $verificar_cantidad=$this->ocompra_model->calcula_ocantidad_pendiente($ocodigo,$prod);
        $cantidad=$verificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$verificar_cantidad->OCOMDEC_Pendiente;  //2

        if($pendiente>=$cant ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
           $cantidadt=$pendiente;
        }elseif($pendiente<=$cant){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$pendiente;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    }

    public function calcula_ocantidad_pendiente_by_id_detalle($id_detalle, $cant, $current_cant){
        $cant=$cant;
        $verificar_cantidad=$this->ocompra_model->calcula_ocantidad_pendiente_by_id_detalle($id_detalle);
        $cantidad=$verificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$verificar_cantidad->OCOMDEC_Pendiente;  //2

        $cantida_permitida = $current_cant + $pendiente;

        if($cantida_permitida >= $cant ){
     
           $estado=1;
           $cantidadt=$cantida_permitida;
        }elseif($cantida_permitida < $cant){
     
            $estado=0;
            $cantidadt=$cantida_permitida;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    }

    public function cantidad_oregistrada($oventa,$prod,$cant,$pend){
        $cant=$cant;
        $pend=$pend;
        $verificar_cantidad=$this->ocompra_model->verificar_ocantidad($oventa,$prod);
        $cantidad=$verificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$verificar_cantidad->OCOMDEC_Pendiente;  //2
       // $cant_impor=$verificar_cantidad->IMPORDEC_Cantidad; //2

         $calcula=$pendiente+$pend;//roberto
        if($calcula>=$cant ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
           $cantidadt=$calcula;
        }elseif($calcula<=$cant){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$calcula;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    }

    public function seleccionar_cotizacion($indSel = ''){
    	$this->load->model('compras/cotizacion_model');
        $array_cotizacion = $this->cotizacion_model->listar_cotizaciones();
        $arreglo = array();
        if (count($array_cotizacion) > 0) {
            foreach ($array_cotizacion as $indice => $valor) {
                $indice1 = $valor->COTIP_Codigo;
                $valor1 = $valor->COTIC_Numero;

                $arreglo[$indice1] = $valor1;
            }
        }
        $resultado = $this->html->optionHTML($arreglo, $indSel, array('0', '::Seleccione::'));
        return $resultado;
    }

    public function seleccionar_moneda($indSel = '')
    {
        $array_rol = $this->moneda_model->listar();
        $arreglo = array();
        foreach ($array_rol as $indice => $valor) {
            $indice1 = $valor->MONED_Codigo;
            $valor1 = $valor->MONED_Descripcion;
            $arreglo[$indice1] = $valor1;
        }
        $resultado = $this->html->optionHTML($arreglo, $indSel, array('', '::Seleccione::'));
        return $resultado;
    }

    /*     * **********************REPORTES ***************************** */

    public function reportes()
    {
    	$this->load->model('ventas/comprobante_model');
        $anio = $this->comprobante_model->anios_para_reportes('V');
        $combo = '<select id="anioVenta" name="anioVenta">';
        $combo .= '<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo .= '<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo .= '</select>';

        $combo2 = '<select id="anioVenta2" name="anioVenta2">';
        $combo2 .= '<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo2 .= '<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo2 .= '</select>';

        $combo3 = '<select id="anioVenta3" name="anioVenta3">';
        $combo3 .= '<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo3 .= '<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo3 .= '</select>';

        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
        $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
        $this->load->library('layout', 'layout');
        $data['titulo'] = "REPORTES DE COMPRAS";
        $data['combo'] = $combo;
        $data['combo2'] = $combo2;
        $data['combo3'] = $combo3;
        $this->layout->view('compras/ocompra_reporte', $data);
    }

    public function ver_reporte_pdf($params,$tipo_oper)
    {
        $temp = (explode('_', $params));
        $fechai = $temp[0];
        $fechaf = $temp[1];
        $proveedor = $temp[2];
        $producto = $temp[3];
        $aprobado = $temp[4];
        $ingreso = $temp[5];

        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
        $listado = $this->ocompra_model->buscar_ocompra($tipo_oper,$fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso);

        if ($fechai != '') {
            $temp = explode('-', $fechai);
            $fechai = $temp[2] . '/' . $temp[1] . '/' . $temp[0];
        }
        if ($fechaf != '') {
            $temp = explode('-', $fechaf);
            $fechaf = $temp[2] . '/' . $temp[1] . '/' . $temp[0];
        }
        $nomprovee = '';
        if ($proveedor != '') {
            $temp = $this->proveedor_model->obtener_datosProveedor($proveedor);
            if ($temp[0]->PROVC_TipoPersona == '0') {
                $temp = $this->persona_model->obtener_datosPersona($temp[0]->PERSP_Codigo);
                $nomprovee = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;
            } else {
                $temp = $this->empresa_model->obtener_datosEmpresa($temp[0]->EMPRP_Codigo);
                $nomprovee = $temp[0]->EMPRC_RazonSocial;
            }
        }
        $nomprod = '';
        if ($producto != '') {
            $temp = $this->producto_model->obtener_producto($producto);
            $nomprod = $temp[0]->PROD_Nombre;
        }
        $nomaprob = '';
        if ($aprobado == '0')
            $nomaprob = 'Pendente';
        elseif ($aprobado == '1')
            $nomaprob = 'Aprobado';
        elseif ($aprobado == '2')
            $nomaprob = 'Desaprobado';

        $nomingre = '';
        if ($ingreso == '0')
            $nomingre = 'Pendiente';
        elseif ($ingreso == '1')
            $nomingre = 'Si';
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;
        $options = array("leading" => 15, "left" => 0);
        $this->cezpdf->ezText('Usuario:  ' . $persona[0]->PERSC_Nombre . ' ' . $persona[0]->PERSC_ApellidoPaterno . ' ' . $persona[0]->PERSC_ApellidoMaterno . '       Fecha: ' . $fechahoy, 7, $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText('REPORTE DE ORDENES DE COMPRA', 17, $options);
        if (($fechai != '' && $fechaf != '') || $proveedor != '' || $producto != '' || $aprobado != '' || $ingreso != '') {
            $this->cezpdf->ezText('Filtros aplicados', 10, $options);
            if ($fechai != '' && $fechaf != '')
                $this->cezpdf->ezText('       - Fecha inicio: ' . $fechai . '   Fecha fin: ' . $fechaf, '', $options);
            if ($proveedor != '')
                $this->cezpdf->ezText('       - Proveedor:  ' . $nomprovee, '', $options);
            if ($producto != '')
                $this->cezpdf->ezText('       - Producto:    ' . $nomprod, '', $options);
            if ($aprobado != '')
                $this->cezpdf->ezText('       - Aprobacion:   ' . $nomaprob, '', $options);
            if ($ingreso != '')
                $this->cezpdf->ezText('       - Ingreso:         ' . $nomingre, '', $options);
        }

        $this->cezpdf->ezText('', '', $options);


        /* Listado */

        foreach ($listado as $indice => $valor) {
            $db_data[] = array(
                'col1' => $indice + 1,
                'col2' => $valor->fecha,
                'col3' => $valor->OCOMC_Numero,
                'col4' => $valor->cotizacion,
                'col5' => $valor->nombre,
                'col6' => $valor->MONED_Simbolo . ' ' . number_format($valor->OCOMC_total, 2),
                'col7' => $valor->aprobado,
                'col8' => $valor->ingreso
            );
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'NRO',
            'col4' => 'COTIZACION',
            'col5' => 'RAZON SOCIAL',
            'col6' => 'TOTAL',
            'col7' => 'C.INGRESO',
            'col8' => 'APROBACION'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 555,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 7,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 50, 'justification' => 'center'),
                'col3' => array('width' => 30, 'justification' => 'center'),
                'col4' => array('width' => 55, 'justification' => 'center'),
                'col5' => array('width' => 200),
                'col6' => array('width' => 50, 'justification' => 'center'),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 60, 'justification' => 'center')
            )
        ));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ventana_ocompra_factura($ocompra)
    {
        $codigo = $this->input->post('codigo');
        $numero = $this->input->post('numero');
        if ($numero != '') {
            $this->ocompra_model->modificar_ocompra_flagRecibido($codigo, $numero);
            echo "<script>window.close();</script>";
        }
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($ocompra);
        $data['numero_factura'] = $datos_ocompra[0]->OCOMC_NumeroFactura;
        $data['ocompra'] = $ocompra;
        $this->load->view('compras/ventana_ocompra_factura', $data);
    }

    public function estadisticas()
    {
        /* Imagen 1 */
        $listado = $this->ocompra_model->reporte_ocompra_5_prov_mas_importantes();

        if (count($listado) == 0) { // Esto significa que no hay ordenes de compra por tando no muestros ningun reporte
            echo '<h3>Ha ocurrido un problema</h3>
                      <span style="color:#ff0000">No se ha encontrado Órdenes de Compra</span>';
            exit;
        }
        $temp1 = array(0, 0, 0, 0, 0);
        $temp2 = array('Vacio', 'Vacio', 'Vacio', 'Vacio', 'Vacio');
        foreach ($listado as $item => $reg) {
            $temp1[$item] = $reg->total;
            if (strlen($reg->nombre) > 30)
                $temp2[$item] = substr($reg->nombre, 0, 28) . '... S/.' . $reg->total;
            else
                $temp2[$item] = $reg->nombre . ' S/.' . $reg->total;
        }


        $DataSet = new pData;
        $DataSet->AddPoint($temp1, "Serie1");
        $DataSet->AddPoint($temp2, "Serie2");
        $DataSet->AddAllSeries();
        $DataSet->SetAbsciseLabelSerie("Serie2");

        // Initialise the graph  
        $Test = new pChart(600, 200);
        $Test->drawFilledRoundedRectangle(7, 7, 593, 193, 5, 240, 240, 240);
        $Test->drawRoundedRectangle(5, 5, 595, 195, 5, 230, 230, 230);

        // Draw the pie chart  
        $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf", 8);
        $Test->drawPieGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 150, 90, 110, PIE_PERCENTAGE, TRUE, 50, 20, 5);
        $Test->drawPieLegend(310, 15, $DataSet->GetData(), $DataSet->GetDataDescription(), 250, 250, 250);

        $Test->Render("images/img_dinamic/imagen1.png");
        echo '<h3>1. Los 5 proveedores más importantes</h3>
               Según el monto (S/.) histórico órdenes de compra<br />
               <img style="margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen1.png" alt="Imagen 1" />';


        /* Imagen 2 */
        $listado = $this->ocompra_model->reporte_ocompra_monto_x_mes();
        $reg = $listado[0];

        // Dataset definition   
        $DataSet = new pData;
        $DataSet->AddPoint(array($reg->enero, $reg->febrero, $reg->marzo, $reg->abril, $reg->mayo, $reg->junio, $reg->julio, $reg->agosto, $reg->setiembre, $reg->octubre, $reg->noviembre, $reg->diciembre), "Serie1");
        $DataSet->AddPoint(array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"), "Serie2");
        $DataSet->AddAllSeries();
        $DataSet->SetAbsciseLabelSerie();
        $DataSet->SetAbsciseLabelSerie("Serie2");
        $DataSet->SetYAxisName("Monto (S/.)");
        $DataSet->SetXAxisName("Meses");

        // Initialise the graph  
        $Test = new pChart(600, 240);
        $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf", 8);
        $Test->setGraphArea(70, 30, 580, 200);
        $Test->drawFilledRoundedRectangle(7, 7, 593, 223, 5, 240, 240, 240);
        $Test->drawRoundedRectangle(5, 5, 595, 225, 5, 230, 230, 230);
        $Test->drawGraphArea(255, 255, 255, TRUE);
        $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2, TRUE);
        $Test->drawGrid(4, TRUE, 230, 230, 230, 50);

        // Draw the 0 line  
        $Test->setFontProperties("Fonts/tahoma.ttf", 6);
        $Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

        // Draw the bar graph  
        $Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), TRUE);

        // Finish the graph  
        $Test->setFontProperties("Fonts/tahoma.ttf", 8);
        $Test->setFontProperties("Fonts/tahoma.ttf", 10);
        $Test->Render("images/img_dinamic/imagen2.png");
        echo '<h3>2. Montos (S/.) de órdenes de compra según mes</h3>
               Considerando el presente año<br />
               <img style="margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen2.png" alt="Imagen 2" />';


        /* Imagen 3 */
        $listado = $this->ocompra_model->reporte_ocompra_cantidad_x_mes();
        $reg = $listado[0];

        $DataSet = new pData;
        $DataSet->AddPoint(array($reg->enero, $reg->febrero, $reg->marzo, $reg->abril, $reg->mayo, $reg->junio, $reg->julio, $reg->agosto, $reg->setiembre, $reg->octubre, $reg->noviembre, $reg->diciembre), "Serie1");
        $DataSet->AddPoint(array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"), "Serie2");
        $DataSet->AddAllSeries();
        $DataSet->RemoveSerie("Serie2");
        $DataSet->SetAbsciseLabelSerie("Serie2");
        $DataSet->SetYAxisName("Cantidad de O. de Compra");
        $DataSet->SetXAxisName("Meses");


        // Initialise the graph  
        $Test = new pChart(600, 230);
        $Test->drawGraphAreaGradient(132, 153, 172, 50, TARGET_BACKGROUND);
        $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf", 8);
        $Test->setGraphArea(60, 20, 585, 180);
        $Test->drawGraphArea(213, 217, 221, FALSE);
        $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 213, 217, 221, TRUE, 0, 2);
        $Test->drawGraphAreaGradient(162, 183, 202, 50);
        $Test->drawGrid(4, TRUE, 230, 230, 230, 20);

        // Draw the line chart  
        $Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
        $Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 2);

        // Draw the legend  
        $Test->setFontProperties("Fonts/tahoma.ttf", 8);

        // Render the picture  
        $Test->Render("images/img_dinamic/imagen3.png");
        echo '<h3>3. Cantidades de órdenes de compra según mes</h3>
               Considerando el presente año<br />
               <img style="margin-top:5px; margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen3.png" alt="Imagen 3" />';

        /* Imagen 4 => COMPRAS */
        $listado = $this->ocompra_model->reporte_comparativo_compras_ventas('C');
        $reg = $listado[0];

        // Dataset definition   
        $DataSet = new pData;
        $DataSet->AddPoint(array($reg->enero, $reg->febrero, $reg->marzo, $reg->abril, $reg->mayo, $reg->junio, $reg->julio, $reg->agosto, $reg->setiembre, $reg->octubre, $reg->noviembre, $reg->diciembre), "Serie1");
        $DataSet->AddPoint(array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"), "Serie2");
        $DataSet->AddAllSeries();
        $DataSet->SetAbsciseLabelSerie();
        $DataSet->SetAbsciseLabelSerie("Serie2");
        $DataSet->SetYAxisName("Monto (S/.)");
        $DataSet->SetXAxisName("Meses");

        // Initialise the graph  
        $Test = new pChart(600, 240);
        $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf", 8);
        $Test->setGraphArea(70, 30, 580, 200);
        $Test->drawFilledRoundedRectangle(7, 7, 593, 223, 5, 240, 240, 240);
        $Test->drawRoundedRectangle(5, 5, 595, 225, 5, 230, 230, 230);
        $Test->drawGraphArea(255, 255, 255, TRUE);
        $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2, TRUE);
        $Test->drawGrid(4, TRUE, 230, 230, 230, 50);

        // Draw the 0 line  
        $Test->setFontProperties("Fonts/tahoma.ttf", 6);
        $Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

        // Draw the bar graph  
        $Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), TRUE);

        // Finish the graph  
        $Test->setFontProperties("Fonts/tahoma.ttf", 8);
        $Test->setFontProperties("Fonts/tahoma.ttf", 10);
        $Test->Render("images/img_dinamic/imagen4.png");
        echo '<h3>4. Compras</h3>
               Considerando las compras en el presente año<br />
               <img style="margin-top:5px; margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen4.png" alt="Imagen 4" />
               <br />';
        /* Imagen 5 => VENTAS */
        /* $listado=$this->ocompra_model->reporte_comparativo_compras_ventas('V'); 
          $reg=$listado[0];

          // Dataset definition
          $DataSet = new pData;
          $DataSet->AddPoint(array($reg->enero,$reg->febrero,$reg->marzo,$reg->abril,$reg->mayo, $reg->junio,$reg->julio,$reg->agosto,$reg->setiembre,$reg->octubre,$reg->noviembre,$reg->diciembre),"Serie1");
          $DataSet->AddPoint(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic"),"Serie2");
          $DataSet->AddAllSeries();
          $DataSet->SetAbsciseLabelSerie();
          $DataSet->SetAbsciseLabelSerie("Serie2");
          $DataSet->SetYAxisName("Monto (S/.)");
          $DataSet->SetXAxisName("Meses");

          // Initialise the graph
          $Test = new pChart(600,240);
          $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf",8);
          $Test->setGraphArea(70,30,580,200);
          $Test->drawFilledRoundedRectangle(7,7,593,223,5,240,240,240);
          $Test->drawRoundedRectangle(5,5,595,225,5,230,230,230);
          $Test->drawGraphArea(255,255,255,TRUE);
          $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
          $Test->drawGrid(4,TRUE,230,230,230,50);

          // Draw the 0 line
          $Test->setFontProperties("Fonts/tahoma.ttf",6);
          $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

          // Draw the bar graph
          $Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);

          // Finish the graph
          $Test->setFontProperties("Fonts/tahoma.ttf",8);
          $Test->setFontProperties("Fonts/tahoma.ttf",10);
          $Test->Render("images/img_dinamic/imagen5.png");
          echo 'Considerando las ventas en el presente año<br />
          <img style="margin-top:5px; margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen5.png" alt="Imagen 5" />'; */
    }

    public function comprobante_nueva_ocompra($codigo, $tipo_oper = 'C')
    {
    	$this->load->model('compras/pedido_model');
        $this->load->helper('my_guiarem');

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigo);
        $presupuesto = $datos_ocompra[0]->PRESUP_Codigo;
        $cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        $pedido = $datos_ocompra[0]->PEDIP_Codigo;
        $numero = $datos_ocompra[0]->OCOMC_Numero;
        $codigo_usuario = $datos_ocompra[0]->OCOMC_CodigoUsuario;
        $serie = $datos_ocompra[0]->OCOMC_Serie;

        $descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        $igv100 = $datos_ocompra[0]->OCOMC_igv100;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $cliente = $datos_ocompra[0]->CLIP_Codigo;
        $proveedor = $datos_ocompra[0]->PROVP_Codigo;
        $centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        $moneda = $datos_ocompra[0]->MONED_Codigo;
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_MiPersonal);
        $mi_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_ocompra[0]->OCOMC_Personal);
        $contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $envio_direccion = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_ocompra[0]->OCOMC_FactDireccion;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $fecha = substr($datos_ocompra[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_ocompra[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;
        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $usuario = $datos_ocompra[0]->USUA_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $estado = $datos_ocompra[0]->OCOMC_FlagEstado;

        $subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        $descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        $igvtotal = $datos_ocompra[0]->OCOMC_igv;
        $percepciontotal = $datos_ocompra[0]->OCOMC_percepcion;
        $total = $datos_ocompra[0]->OCOMC_total;

        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $empresa = '';
        $persona = '';

        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $tipo = $datos_cliente->tipo;
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $empresa = $datos_cliente->empresa;
                $persona = $datos_cliente->persona;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $tipo = $datos_proveedor->tipo;
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
                $empresa = $datos_proveedor->empresa;
                $persona = $datos_proveedor->persona;
            }
        }

        $data['tipo_oper'] = $tipo_oper;
        //$data['cboPresupuesto'] = $this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, 'F');
        //$data['cboCotizacion'] = $this->cotizacion_model->obtener_cotizacion($cotizacion);
        $data['cboMoneda'] = $this->moneda_model->obtener($moneda);


        if ($cotizacion == 0) {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        } else {
            $data['cboAlmacen'] = $this->almacen_model->obtener($almacen);
            $data['cboFormapago'] = $this->formapago_model->obtener($formapago);
        }

        $data['mi_contacto'] = $mi_contacto;
        $data['contacto'] = $contacto;
        $data['cboPedidos'] = form_dropdown("pedidos", $this->pedido_model->seleccionar_finalizados(), "", " onchange='load_cotizaciones();' class='comboGrande' style='width:200px;' id='pedidos'");
        $datos_usuario = $this->usuario_model->obtener($usuario);
        $data['nombre_usuario'] = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;
        $data['numero'] = $numero;
        $data['codigo_usuario'] = $codigo_usuario;
        $data['igv'] = $igv100;
        $data['descuento'] = $descuento100;
        $data['percepcion'] = $percepcion100;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['pedido'] = $pedido;
        $data['cotizacion'] = $cotizacion;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => $empresa, 'persona' => $persona, 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/compras/ocompra/modificar_ocompra";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = mysql_to_human($fecha);
        $data['fechaentrega'] = ($fechaentrega != '' ? mysql_to_human($fechaentrega) : '');
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuentototal;
        $data['igvtotal'] = $igvtotal;
        $data['percepciontotal'] = $percepciontotal;
        $data['importetotal'] = $total;
        $data['ctactesoles'] = $ctactesoles;
        $data['ctactedolares'] = $ctactedolares;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;

        $data['envio_direccion'] = $envio_direccion;
        $data['fact_direccion'] = $fact_direccion;

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigo);
        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                // $tipo_orden,$tipo_guia,$cod_orden,$cod_prod
                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
                $cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;
                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $unidad = $valor->UNDMED_Codigo;
                $puconigv = $valor->OCOMDEC_Pu_ConIgv;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $igv_total = $valor->OCOMDEC_Igv100;
                $total = $valor->OCOMDEC_Total;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento2 = $valor->OCOMDEC_Descuento2;
                $observ = $valor->OCOMDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detocompra;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->COTDEC_Cantidad = $cantidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->OCOMDEC_Subtotal = $subtotal;
                $objeto->OCOMDEC_Descuento = $descuento;
                $objeto->OCOMDEC_Descuento2 = $descuento2;
                $objeto->OCOMDEC_Igv = $igv;
                $objeto->OCOMDEC_Total = $total;
                $objeto->OCOMDEC_Pu = $pu;
                $objeto->OCOMDEC_Pu_ConIgv = $puconigv;
                $objeto->cantidad_entregada = $cantidad_entregada;
                $objeto->cantidad_pendiente = $cantidad_pendiente;
                $objeto->codigo = $codigo;
                $objeto->flagGenInd = $flagGenInd;
                if (count($datos_unidad) > 0) {
                    $objeto->nombre_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                    $objeto->UNDMED_Codigo = $unidad;
                } else {
                    $objeto->nombre_unidad = "UNI";
                    $objeto->UNDMED_Codigo = "7";
                }
                $objeto->igv_total = $igv_total;

                $detalle_ocompra[] = $objeto;
            }
        }
        $data['detalle_ocompra'] = $detalle_ocompra;

        $this->load->view('compras/ocompra_ventana_mostrar', $data);
    }

    public function ventana_muestra_proveedor($tipo_oper)
    {
        $ocompras = $this->ocompra_model->listar($tipo_oper);
        $lista = array();
        if (count($ocompras) > 0) {
            foreach ($ocompras as $value) {
                $filter = new stdClass();
                $filter->codigo = $value->OCOMP_Codigo;
                $filter->numero = $value->OCOMC_Numero;
                $proveedor = $this->proveedor_model->obtener($value->PROVP_Codigo);
                $filter->proveedor = (count($proveedor) > 0) ? $proveedor->nombre : '';
                $lista[] = $filter;
            }
        }
        $data['lista'] = $lista;
        $data['tipo_oper'] = $tipo_oper;
        $data['titulo'] = "VER GUIAS DE REMISION POR O. COMPRA";
        $this->load->view('compras/ocompra_ventana_seleccionar', $data);
    }
    public function registro_ocompras_pdf($tipo_oper, $fecha_ini='', $fecha_fin='',$ruc='', $nombre='')
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

            

        //        $this->cezpdf->ezText('', '', array("leading" => 100));
        $this->cezpdf->ezText('<b>RELACION DE ORDENES DE VENTA</b>', 14, array("leading" => 0, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */


        //        /* Listado de detalles */

        $db_data = array();


        $listado_productos = $this->ocompra_model->listar_ocompras_pdf($tipo_oper,$fecha_ini, $fecha_fin,$codigo,$nombre);
    
            if (count($listado_productos) > 0) {
                foreach ($listado_productos as $indice => $valor) {
                    $codigo = $valor->FAMI_Codigo;
                    $codigo_interno = $valor->FAMI_CodigoInterno;
                    $descripcion = $valor->FAMI_Descripcion;


                    $db_data[] = array(
                        'cols1' => $indice + 1,
                        'cols2' => $codigo_interno,
                        'cols3' => $descripcion
                    );
                }
            }

        


        $col_names = array(
            'cols1' => '<b>ITEM</b>',
            'cols2' => '<b>CODIGO</b>',
            'cols3' => '<b>DESCRIPCION</b>'
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
                'cols2' => array('width' => 70, 'justification' => 'center'),
                'cols3' => array('width' => 245, 'justification' => 'left')
            )
        ));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');

        ob_end_clean();

        $this->cezpdf->ezStream($cabecera);
    }

    public function getInfoSendMail(){
        $ocompra = $this->input->post("id");
        $ocompraInfo = $this->ocompra_model->getOcompraMail($ocompra);

        $data = array();

        foreach ($ocompraInfo as $i => $val) {

            if ($val->empresa != NULL){
                $contactos = $this->empresa_model->listar_contactosEmpresa($val->empresa);

                $lista = array();
                
                if ($val->email != NULL && $val->email != "")
                    $lista[] = array( "contacto" => $val->razon_social, "correo" => $val->email);
                
                if (count($contactos) > 0) {
                    foreach ($contactos as $value) {
                        $nombres_persona = $value->PERSC_Nombre . " " . $value->PERSC_ApellidoPaterno . " " . $value->PERSC_ApellidoMaterno;
                        $emailcontacto = $value->ECONC_Email;
                        $lista[] = array( "contacto" => $nombres_persona, "correo" => $emailcontacto);
                    }
                }
            }

            $data[] = array(
                                "codigo" => $val->OCOMP_Codigo,
                                "nombre" => $val->razon_social,
                                "ruc" => $val->ruc,
                                "serie" => $val->OCOMC_Serie,
                                "numero" => $val->OCOMC_Numero,
                                "fecha" => mysql_to_human($val->OCOMC_Fecha),
                                "importe" => $val->MONED_Simbolo . " " . $val->OCOMC_total,
                                "contactos" => $lista,
                                "empresa_envio" => $_SESSION["nombre_empresa"]
                            );
        }

        $json = ( count($data) > 0 ) ? array( "match" => true, "info" => $data ) : array( "match" => false, "info" => NULL );

        echo json_encode($json);
    }

    public function sendDocMail(){
        $titulo = $this->input->post("asunto");
        $destinatario = $this->input->post("destinatario");
        $mensaje = $this->input->post("mensaje");

        $adjunto = true;

        $tipo = $this->input->post("documento");
        $codigo = $this->input->post("codigo");

        $send = $this->lib_props->sendDocMail($titulo, $destinatario, $mensaje, $adjunto, $tipo, $codigo);
        $json = ($send == true) ? array("result" => "success") : array("result" => "error");

        echo json_encode($json);
    }

    public function ventana_cotizacion_correos($codigo) {
        
        $nombre_persona1 = $this->session->userdata('nombre_persona');
        $persona1 = $this->session->userdata('persona');
        
        $compania = $this->compania;
        $data['compania'] = $compania;
        $data_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania = $this->compania_model->obtener_compania($this->compania);
        
        $datos_presupuesto = $this->ocompra_model->obtener_ocompra($codigo);       

        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $fecha = mysql_to_human($datos_presupuesto[0]->OCOMC_Fecha);
        $persona1 = $datos_presupuesto[0]->OCOMC_Personal;

        $datos_usuario = $this->persona_model->obtener_datosPersona($persona1);
        if ($datos_usuario[0]) {
            $emailusuario = $datos_usuario[0]->PERSC_Email;
        }

        $serie = $datos_presupuesto[0]->OCOMC_Serie;
        $numero = $datos_presupuesto[0]->OCOMC_Numero;

        $datos_cliente = $this->cliente_model->obtener($cliente);
        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $empresa = '';
        $persona = '';
        $emailenviar = '';
        if ($datos_cliente) {

            $tipo = $datos_cliente->tipo;
            $nombre_cliente = $datos_cliente->nombre;
            $ruc_cliente = $datos_cliente->ruc;
            $empresa = $datos_cliente->empresa;
            $persona = $datos_cliente->persona;
            $emailenviar = $datos_cliente->correo;
        }

        //Contactos
        $contactos = $this->empresa_model->listar_contactosEmpresa($empresa);
        $lista = array();
        if (count($contactos) > 0) {
            foreach ($contactos as $value) {
                $persona = $value->ECONC_Persona . '-' . $value->AREAP_Codigo;
                $nombres_persona = $value->PERSC_Nombre . " " . $value->PERSC_ApellidoPaterno . " " . $value->PERSC_ApellidoMaterno . ($value->AREAP_Codigo != '0' && $value->AREAP_Codigo != '' ? " - " . $value->AREAC_Descripcion : '');
                $emailcontacto = $value->ECONC_Email;
                $lista[] = array($persona, $nombres_persona, $emailcontacto);
            }
        }

        $data['lista'] = $lista;
        $data['nomFechaEmi'] = $fecha;
        $data['nomTotal'] = $datos_presupuesto[0]->OCOMC_total;
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['documento'] = "COTIZACIÓN";
        
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['nombre_persona1'] = $nombre_persona1;
        $data['emailusuario'] = $_SESSION['user_name']."@osa-fact.com";
        $data['emailenviar'] = $emailenviar;
        $data['titulo'] = "ENVIAR COTIZACIÓN ELECTRONICA - CORREO";
        $data['formulario'] = "frmPresupuestoCorreo";
        $data['url_action'] = base_url() . "index.php/compras/ocompra/Enviarcorreo";
        $data['hoy'] = $fecha;
        $data['codigo'] = $codigo;

        $data['tipo_codificacion'] = 1;
        $this->load->view('compras/ocompra_correo', $data);
    }

    public function Enviarcorreo() {
        $xlsgrabar = 0;
        $pdfgrabar = 0;
        $pdfgrabar2 = 0;
        $usuario = $this->input->post('usuario');
        $nombreusuario = $this->input->post('nombreusuario');
        $nombreDestinatario = $this->input->post('nomcontactopersona');
        $destinatario = $this->input->post('destinatario');
        $mensaje = $this->input->post('mensaje');
        $codigo = $this->input->post('codigo');
        $documento = $this->input->post('documento');
        $titulomensaje = $this->input->post('titulomensaje');
        $nomEmpresaDest = $this->input->post('nomEmpresaDest');
        $nomLink = $this->input->post('nomLink');
        $nomSerie = $this->input->post('nomSerie');
        $nomNumero = $this->input->post('nomNumero');
        $nomFechaEmi = $this->input->post('nomFechaEmi');
        $nomTotal = $this->input->post('nomTotal');
        //falta traer tip
        $tip=$this->input->post('tip');

        $nombre_empresa = $_SESSION['nombre_empresa'];
        
        $this->load->library('My_PHPMailer');
        $mail = new PHPMailer(); // Passing `true` enables exceptions
        $mail->isSMTP();  
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true; // Set mailer to use SMTP
        $mail->PluginDir = $this->load->library('My_PHPMailer')->class.smtp;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'mail.facturacionelectronicaccapa.com'; 
        $mail->Port = 465; // Specify main and backup SMTP servers
        
        $mail->Username = 'facturacionelectronica@facturacionelectronicaccapa.com'; 
        $mail->Password = '(zmkLTu]j_{Y';
        // correo vinculados por ver
        $mail->From = 'facturacionelectronica@facturacionelectronicaccapa.com';   
        $mail->FromName = $nombre_empresa;
        $mail->Subject = $titulomensaje;
        $enviarformatopag = '<table cellpadding="0" cellspacing="0" width="900" align="center" >
                                <tr>
                                    <td style="padding:0px; font-size:12pt; font-weight:bold;"> Sres. ' . $nomEmpresaDest . ' </td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:12pt;"> <b>'.$nombre_empresa.'</b>, ENVIA UN DOCUMENTO ELECTRONICO.</td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:12pt;"> </td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt; font-weight:bold;">- TIPO: '.$documento.' ELECTRONICA </td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt;"><b>- SERIE: </b>' . $nomSerie . '</td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt;"><b>- NUMERO: </b>' . $nomNumero . '</td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt;"><b>- FECHA DE EMISION: </b>' . $nomFechaEmi . '</td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt;"><b>- MONTO TOTAL: </b>' . $nomTotal . '</td>
                                </tr>
                                <tr>
                                    <td style="padding:0px; font-size:11pt;"> </td>
                                </tr>
                            </table>';
                        
        $Tdestinatarios = explode(",",$destinatario);
        $size = count($Tdestinatarios);

        for ($i = 0; $i < $size; $i++){
            if ($Tdestinatarios[$i] != "")
                $mail->addAddress($Tdestinatarios[$i]);
        }

        #if ($this->input->post('pdf') != '') {
            $pdfgrabar = 1;
            $uPDF = $this->lib_props->ocompra_pdf($codigo, 1, true);
            $mail->AddStringAttachment($uPDF, 'Cotizacion.pdf');
        #}

        $mail->MsgHTML($enviarformatopag);
        
        if(!$mail->Send()) {
            $error = 'Mail error: '.$mail->ErrorInfo;                            
            echo $error;
        }
        else {
            $filter = new stdClass();
            $filter->PRESUP_Codigo = $codigo;
            $filter->CE_FechaEnvio = date("Y-m-d h:i:s");
            $filter->CE_CorreoRemitente = $usuario;
            $filter->CE_CorreoReceptor = $destinatario;
            $filter->CE_NombreRemitente = $nombreusuario;
            $filter->CE_NombreReceptor = $nombreDestinatario;
            $filter->CE_Mensaje = $mensaje;
            $filter->CE_Excel = $xlsgrabar;
            $filter->CE_Pdf = $pdfgrabar;
            $filter->CE_Estado = 1;
            $this->presupuesto_model->Insertar_correo_enviado($filter);
            echo true;
            $error = 'Message sent!';
            return true;
        }        
    }

    public function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    public function ocompra_descarga_excel($codigo){
        $this->lib_props->ocompra_descarga_excel($codigo);
    }

}

?>
