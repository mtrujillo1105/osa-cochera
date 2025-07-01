<?php if (!defined('BASEPATH')) exit('No se permite el acceso directo al script');

/* *********************************************************************************
Fecha: Unknow
/* ******************************************************************************** */

include_once(APPPATH . 'libraries/Tcpdf.php');
class Lib_props{
	protected $ci;

	private $empresa;
	private $compania;
	private $usuario;

  ## CONFIG EMAIL
	private $mailHost = "mail.facturacionelectronicaccapa.com";
	private $mailSMTP = "ssl";
	private $mailPort = 465;
	private $mailUsername = "facturacionelectronica@facturacionelectronicaccapa.com";
	private $mailPassword = "(zmkLTu]j_{Y";

## METODOS GENERALES
##########################################################################

	public function __construct(){
		$this->ci =& get_instance();

		$this->ci->load->model('maestros/almacen_model');
		$this->ci->load->model('almacen/almacenproducto_model');
		$this->ci->load->model('almacen/seriedocumento_model');
		$this->ci->load->model('almacen/guiarem_model');
		$this->ci->load->model('almacen/guiatrans_model');
		$this->ci->load->model('almacen/guiatransdetalle_model');
		$this->ci->load->model('almacen/producto_model');
		$this->ci->load->model('maestros/unidadmedida_model');

		$this->ci->load->model('compras/ocompra_model');
		$this->ci->load->model('compras/ocompradetalle_model');
		$this->ci->load->model('compras/pedido_model');
		$this->ci->load->model('compras/pedidodetalle_model');
		$this->ci->load->model('empresa/proveedor_model');

		$this->ci->load->model('ventas/comprobante_model');
		$this->ci->load->model('ventas/comprobantedetalle_model');
		$this->ci->load->model('ventas/notacredito_model');
		$this->ci->load->model('ventas/notacreditodetalle_model');
		$this->ci->load->model('empresa/cliente_model');
		$this->ci->load->model('ventas/letra_model');

		$this->ci->load->model("maestros/emprestablecimiento_model");
		$this->ci->load->model("maestros/compania_model");

		$this->ci->load->model('maestros/persona_model');
		$this->ci->load->model('maestros/proyecto_model');
		$this->ci->load->model('maestros/moneda_model');
		$this->ci->load->model('maestros/formapago_model');
		$this->ci->load->model('maestros/tipocambio_model');
		$this->ci->load->model('maestros/persona_model');
		$this->ci->load->model('empresa/empresa_model');
		$this->ci->load->model('empresa/directivo_model');

		$this->ci->load->model('seguridad/usuario_model');

		$this->ci->load->model('tesoreria/cuentaspago_model');
		$this->ci->load->model('tesoreria/banco_model');
		$this->ci->load->model('tesoreria/bancocta_model');
		$this->ci->load->model('tesoreria/cuota_model');
		$this->ci->load->model('tesoreria/movimiento_model');
		$this->ci->load->model('tesoreria/pago_model');

		$this->ci->load->library('email');
		$this->ci->load->library('pdf');


		$this->empresa = $this->ci->session->userdata("empresa");
		$this->compania = $this->ci->session->userdata("compania");
		$this->usuario = $this->ci->session->userdata("user");
	}

	public function ObtenerSeriesComprobante($comprobante, $tipoComprobante, $producto, $return = "concat", $nota = NULL){
		switch ($tipoComprobante) {
			case 'F':
			$tipo = 8;
			break;
			case 'B':
			$tipo = 9;
			break;
			case 'N':
			$tipo = 11;
			break;
			case 'GT':
			$tipo = 15;
			break;
			default:
			$tipo = 1;
			break;
		}

		$filterSerie = new stdClass();
		$filterSerie->PROD_Codigo = $producto;
		$filterSerie->SERIC_FlagEstado = '1';

		$filterSerie->DOCUP_Codigo = $tipo;
		$filterSerie->SERDOC_NumeroRef = $comprobante;

		if ($nota != NULL)
			$filterSerie->TIPOMOV_Tipo = 1;

		$listaSeriesProducto = $this->ci->seriedocumento_model->buscar($filterSerie);

		$seriesComp = new stdClass();

		if ($return == "concat"){
			$numerosDeSerie = "\nSERIES: ";
			if($listaSeriesProducto != null && count($listaSeriesProducto) > 0){
				foreach($listaSeriesProducto as $serieValor){
					$numerosDeSerie = $numerosDeSerie."\n".$serieValor->SERIC_Numero;
				}
			}

			return $numerosDeSerie;
		}
		else
			if ($return == "class"){
				$listaSeries = $this->ci->seriedocumento_model->buscar($filterSerie);

				foreach($listaSeries as $key => $serieValor){
					$seriesComp->serieNumero[] = $serieValor->SERIC_Numero;
					$seriesComp->serieCodigo[] = $serieValor->SERIP_Codigo;
				}

				return $seriesComp;
			}
			else{
				if($listaSeriesProducto != null && count($listaSeriesProducto) > 0){
					foreach($listaSeriesProducto as $serieValor){
						$seriesComp->serieNumero[] = $serieValor->SERIC_Numero;
						$seriesComp->serieCodigo[] = $serieValor->SERIP_Codigo;
					}
					return $seriesComp;
				}
			}
	}

	public function mesesEs($mes = NULL){
		switch ($mes) {
			case '1':
			return "ENERO";
			break;
			case '2':
			return "FEBRERO";
			break;
			case '3':
			return "MARZO";
			break;
			case '4':
			return "ABRIL";
			break;
			case '5':
			return "MAYO";
			break;
			case '6':
			return "JUNIO";
			break;
			case '7':
			return "JULIO";
			break;
			case '8':
			return "AGOSTO";
			break;
			case '9':
			return "SEPTIEMBRE";
			break;
			case '10':
			return "OCTUBRE";
			break;
			case '11':
			return "NOVIEMBRE";
			break;
			case '12':
			return "DICIEMBRE";
			break;            
			default:
			return "-";
			break;
		}
	}

	public function colExcel($colExcel){
		switch ($colExcel) {
			case '1':
			return "A";
			break;
			case '2':
			return "B";
			break;
			case '3':
			return "C";
			break;
			case '4':
			return "D";
			break;
			case '5':
			return "E";
			break;
			case '6':
			return "F";
			break;
			case '7':
			return "G";
			break;
			case '8':
			return "H";
			break;
			case '9':
			return "I";
			break;
			case '10':
			return "J";
			break;
			case '11':
			return "K";
			break;
			case '12':
			return "L";
			case '13':
			return "M";
			break;
			case '14':
			return "N";
			break;
			case '15':
			return "O";
			break;
			case '16':
			return "P";
			break;
			case '17':
			return "Q";
			break;
			case '18':
			return "R";
			break;
			case '19':
			return "S";
			break;
			case '20':
			return "T";
			break;
			case '21':
			return "U";
			break;
			case '22':
			return "V";
			break;
			case '23':
			return "W";
			break;
			case '24':
			return "X";
			break;
			case '25':
			return "Y";
			break;
			case '26':
			return "z";
			break;
			case '27':
			return "AA";
			break;
			case '28':
			return "AB";
			break;
			case '29':
			return "AC";
			break;
			case '30':
			return "AD";
			break;
			case '31':
			return "AE";
			break;
			case '32':
			return "AF";
			break;
			case '33':
			return "AG";
			break;
			case '34':
			return "AH";
			break;
			case '35':
			return "AI";
			break;
			case '36':
			return "AJ";
			break;
			case '37':
			return "AK";
			break;
			case '38':
			return "AL";
			case '39':
			return "AM";
			break;
			case '40':
			return "AN";
			break;
			case '41':
			return "AO";
			break;
			case '42':
			return "AP";
			break;
			case '43':
			return "AQ";
			break;
			case '44':
			return "AR";
			break;
			case '45':
			return "AS";
			break;
			case '46':
			return "AT";
			break;
			case '47':
			return "AU";
			break;
			case '48':
			return "AV";
			break;
			case '49':
			return "AW";
			break;
			case '50':
			return "AX";
			break;
			case '51':
			return "AY";
			break;
			case '52':
			return "Az";
			break;

			default:
			return "A";
			break;
		}
	}

	public function getOrderNumeroSerie($numero){
		$cantidad = strlen($numero);
		switch ($cantidad) {
			case '1':
			$dato ="000$numero";
			break;
			case '2':
			$dato ="00$numero";
			break;
			case '3':
			$dato ="0$numero";
			break;
			case '4':
			$dato ="$numero";
			break;

			default:
			$dato = "$numero";
			break;
		}
		return $dato;
	}

	public function getNumberFormat($numero, $cant = 4){
		$numero = trim($numero);
		$cantidad = strlen($numero);

		$number = "";
		if ($cant > $cantidad){
			while($cantidad < $cant){
				$number .= "0";
				$cantidad++;
			}
		}
		$number .= $numero;

		return $number;
	}

	public function formatHours($hora, $format = "12"){
		$hora = explode(":", $hora);

		if ($format == "12"){
			if ($hora[0] > "12"){
				$nvaHora = ($hora[0] - 12 );
				$nvaHora .= ":" .$hora[1]. " PM";
			}
			else if ($hora[0] == "12"){
				$nvaHora = $hora[0] . ":" .$hora[1]. " PM";
			}
			else if ($hora[0] == "00"){
				$nvaHora = ($hora[0] + 12 );
				$nvaHora .= ":" .$hora[1]. " AM";
			}
			else
				$nvaHora = $hora[0] . ":" .$hora[1]. " AM";

			if ( strlen($nvaHora) == 7 )
				$nvaHora = "0".$nvaHora;
		}
		else{
			$nvaHora = $hora[0].":".$hora[1];
		}

		return $nvaHora;
	}

	public function sendMail($menu = NULL, $codigo = NULL, $titulo = NULL, $msg = NULL, $pdf = NULL) {

		if ($menu == NULL || $codigo == NULL)
			return NULL;

		$usuario = $_SESSION['user'];
		$nombreusuario = $_SESSION['user_name'];
		$nombre_empresa = $_SESSION['nombre_empresa'];
		$titulo = ($titulo == NULL) ? "NOTIFICACIÓN DE TAREA." : $titulo;

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
    // Set mailer to use SMTP
		$mail->SMTPAuth = true;
		$mail->PluginDir = $this->ci->load->library('My_PHPMailer')->class.smtp;
		$mail->SMTPSecure = 'ssl';
		$mail->Host = 'mail.facturacionelectronicaccapa.com';
		$mail->Port = 465;

		$mail->Username = 'facturacionelectronica@facturacionelectronicaccapa.com'; 
		$mail->Password = '(zmkLTu]j_{Y';
    // correo vinculados por ver
		$mail->From = 'facturacionelectronica@facturacionelectronicaccapa.com';   
		$mail->FromName = $_SESSION['nombre_empresa'];
		$mail->Subject = $titulo;

		$msg = ($msg == NULL) ? "LE INFORMA QUE EXISTE UNA TAREA PENDIENTE." : $msg;
		$mensaje = "$nombre_empresa $msg";

		$enviarformatopag = $mensaje;

		$destinatarios = $this->ci->usuario_model->usersNotifications($menu);

		if ($destinatarios == NULL)
			return NULL;

		foreach ($destinatarios as $key => $val) {
			if ($val->PERSC_Email != "" && $val->PERSC_Email != NULL)
				$mail->addAddress($val->PERSC_Email);
		}

		if ($pdf == NULL){
			switch ($menu) {
                #Ordenes de compra':
				case '20': 
				$adjunto = $this->ocompra_pdf($codigo, 1, true);
				break;
                #Seguimiento de Cotizaciones':
				case '60': 
				$adjunto = $this->ocompra_pdf($codigo, 1, true);
				break;
                #Órdenes de pedidos':
				case '61':
				$adjunto = $this->pedido_pdf($codigo, 1, true);
				break;
                #G. Transferencia':
				case '74':
				$adjunto = $this->guiatrans_pdf($codigo, 1, true);
				break;

				default:
				$adjunto = NULL;
				break;
			}
		}
		else{
			switch ($pdf) {
				case 'PR':
				$adjunto = $this->produccion_pdf($codigo, 1, true);
				break;
				case 'PD':
				$adjunto = $this->pedido_pdf($codigo, 1, true);
				break;
				case 'COMPROBANTE':
				$adjunto = $this->comprobante_pdf_a4($codigo, 1, true);
				break;

				default:
				$adjunto = NULL;
				break;
			}
		}

		if ($adjunto == NULL)
			return NULL;

		if ($adjunto != NULL)
			$mail->AddStringAttachment($adjunto, "$menu.pdf");

		$mail->MsgHTML($enviarformatopag);

		if(!$mail->Send()) {
			$error = $mail->ErrorInfo;
		} else {
			return true;
		}
	}

	public function sendDocMail($titulo = NULL, $destinatario, $mensaje = NULL, $adjunto = true, $tipo = NULL, $codigo = NULL, $docs = NULL) {
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
      // Set mailer to use SMTP
		$mail->SMTPAuth = true;
		$mail->PluginDir = $this->ci->load->library('My_PHPMailer')->class.smtp;
		$mail->CharSet = 'UTF-8';

		$mail->SMTPSecure = $this->mailSMTP;
		$mail->Host = $this->mailHost;
		$mail->Port = $this->mailPort;
		$mail->Username = $this->mailUsername; 
		$mail->Password = $this->mailPassword;

      // correo vinculados por ver
		$mail->From = $this->mailUsername;
		$mail->FromName = $_SESSION['nombre_empresa'];
		$mail->Subject = $titulo;
		

		$Tdestinatarios = explode(",",$destinatario);
		$size = count($Tdestinatarios);

		for ($i = 0; $i < $size; $i++){
			if ($Tdestinatarios[$i] != "")
				$mail->addAddress($Tdestinatarios[$i]);
		}

		$mail->MsgHTML($mensaje);
		$doc = NULL;

		if ($adjunto == true){

			if ($tipo == 18){
				$doc = $this->ocompra_pdf($codigo, 1, true);                    
				$mail->AddStringAttachment($doc, "Cotizacion.pdf");
			}

			if ($tipo == 8 || $tipo == 9 || $tipo == 14){
				if ($docs->ticket == 1){
					$doc = $this->comprobante_pdf_ticket($codigo, 1, true);
					$mail->AddStringAttachment($doc, "Ticket.pdf");
				}

				if ($docs->a4 == 1){
					$doc = $this->comprobante_pdf_a4($codigo, 1, true);
					$mail->AddStringAttachment($doc, "A4.pdf");

					$pdfRespSunat = $this->ci->comprobante_model->consultar_respuestaSunat($codigo);

					if ( $pdfRespSunat->respuestas_enlacepdf == NULL || $pdfRespSunat->respuestas_enlacepdf == '' ){
						$mensaje .= "Consulta este documento electronico en: ".$pdfRespSunat->respuestas_enlacepdf;
					}
				}

				if ($docs->xml == 1){
					$xml = $this->ci->comprobante_model->consultar_respuestaXMLSunat($codigo);
					if ( $xml->respuestas_enlacexml != NULL && $xml->respuestas_enlacexml != "" ){
						$doc = $this->file_get_contents_curl($codigo);
						$mail->AddStringAttachment($doc, "xml.xml");
					}
				}
			}

			if ($tipo == 11 || $tipo == 12){
				if ($docs->ticket == 1){
					$doc = $this->nota_pdf_ticket($codigo, 1, true);
					$mail->AddStringAttachment($doc, "Ticket.pdf");
				}

				if ($docs->a4 == 1){
					$doc = $this->nota_pdf_a4($codigo, 1, true);
					$mail->AddStringAttachment($doc, "A4.pdf");
				}

				if ($docs->xml == 1){
					$xml = $this->ci->notacredito_model->consultar_respuestaXMLSunat($codigo);
					if ( $xml->respuestas_enlacexml != NULL && $xml->respuestas_enlacexml != "" ){
						$doc = $this->file_get_contents_curl($codigo);
						$mail->AddStringAttachment($doc, "xml.pdf");
					}
				}
			}
		}

		$mail->MsgHTML($mensaje);
		
		if(!$mail->Send()) {
			return false;
		} else {
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

  public function listarVendedores( $persona = NULL ){
    #$persona = ($persona == NULL) ? $_SESSION['persona'] : $persona; # CAMBIO PARA SHARKS - SOLAMENTE MUESTRA AL VENDEDOR LOGUEADO

        $data = $this->ci->usuario_model->listar_vendedores();
  	$option = "<option value=''>Seleccionar</option>";

  	foreach ($data as $indice => $fila) {
  		$selected = ($fila->PERSP_Codigo == $persona) ? "selected" : "";
  		$option .= "<option value='$fila->PERSP_Codigo' $selected>$fila->PERSC_Nombre $fila->PERSC_ApellidoPaterno $fila->PERSC_ApellidoMaterno</option>";
  	}
  	return $option;
  }
  
  public function listarUsuarios( $usuario = NULL ){
    $data = $this->ci->usuario_model->listar_usuarios();
    $option = "<option value=''>Seleccionar</option>";
    foreach ($data as $indice => $fila) {
            $selected = ($fila->USUA_Codigo == $usuario) ? "selected" : "";
            $option .= "<option value='$fila->USUA_Codigo' $selected>$fila->PERSC_Nombre $fila->PERSC_ApellidoPaterno $fila->PERSC_ApellidoMaterno</option>";
    }
    return $option;
  }   

  public function tipo_afectacion( $id = NULL ){

    #1 = Gravado - Operación Onerosa
    #2 = Gravado – Retiro por premio
    #3 = Gravado – Retiro por donación
    #4 = Gravado – Retiro
    #5 = Gravado – Retiro por publicidad
    #6 = Gravado – Bonificaciones
    #7 = Gravado – Retiro por entrega a trabajadores
    #8 = Exonerado - Operación Onerosa
    #9 = Inafecto - Operación Onerosa
    #10 = Inafecto – Retiro por Bonificación
    #11 = Inafecto – Retiro
    #12 = Inafecto – Retiro por Muestras Médicas
    #13 = Inafecto - Retiro por Convenio Colectivo
    #14 = Inafecto – Retiro por premio
    #15 = Inafecto - Retiro por publicidad
    #16 = Exportación

  	$data = $this->ci->producto_model->tipo_afectacion();
  	$option = "";

  	foreach ($data as $indice => $fila) {
  		$selected = ($fila->AFECT_Codigo == $id) ? "selected" : "";
  		$option .= "<option value='$fila->AFECT_Codigo' $selected>$fila->AFECT_Descripcion</option>";
  	}
  	return $option;
  }

  public function obtener_detalles_comprobante($codigo){
  	$detalle = $this->ci->comprobantedetalle_model->listar($codigo);
  	$lista_detalles = array();
  	if (count($detalle) > 0) {
  		foreach ($detalle as $indice => $valor) {
  			$detacodi = $valor->CPDEP_Codigo;
  			$producto = $valor->PROD_Codigo;
  			$unidad = $valor->UNDMED_Codigo;
  			$tipo_afectacion = $valor->AFECT_Codigo;
  			$cantidad = $valor->CPDEC_Cantidad;
  			$pendiente = $valor->CPDEC_Pendiente;
  			$pu = $valor->CPDEC_Pu;
  			$subtotal = $valor->CPDEC_Subtotal;
  			$igv = $valor->CPDEC_Igv;
  			$descuento = $valor->CPDEC_Descuento;
  			$total = $valor->CPDEC_Total;
  			$pu_conigv = $valor->CPDEC_Pu_ConIgv;
  			$subtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
  			$descuento_conigv = $valor->CPDEC_Descuento_ConIgv;
  			$descuento100 = $valor->CPDEC_Descuento100;
  			$igv100 = $valor->CPDEC_Igv100;
  			$observacion = $valor->CPDEC_Observacion;
  			$datos_producto = $this->ci->producto_model->obtener_producto($producto);
  			$flagBS = $datos_producto[0]->PROD_FlagBienServicio;
  			$GenInd = $valor->CPDEC_GenInd;
  			$costo = $valor->CPDEC_Costo;
  			$almacenProducto= $valor->ALMAP_Codigo;
  			$codigoGuiaremAsociadaDetalle= $valor->GUIAREMP_Codigo;
  			$codigovc = $valor->OCOMP_Codigo_VC;
  			$icbper = $valor->CPDEC_ITEMS;

  			$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre;
  			$codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
  			$codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
  			$codigo_original = $datos_producto[0]->PROD_CodigoOriginal;

  			$umDetalle = $this->ci->unidadmedida_model->obtener($unidad);
  			$nombre_unidad = ($umDetalle[0]->UNDMED_Simbolo != "") ? $umDetalle[0]->UNDMED_Simbolo : "ZZ";

  			$objeto = new stdClass();
  			$objeto->CPDEP_Codigo = $detacodi;
  			$objeto->flagBS = $flagBS;
  			$objeto->PROD_Codigo = $producto;
  			$objeto->PROD_CodigoInterno = $codigo_interno;
  			$objeto->PROD_CodigoUsuario = $codigo_usuario;
  			$objeto->PROD_CodigoOriginal = $codigo_original;
  			$objeto->UNDMED_Codigo = $unidad;
  			$objeto->AFECT_Codigo = $tipo_afectacion;
  			$objeto->UNDMED_Simbolo = $nombre_unidad;
  			$objeto->CPDEC_GenInd = $GenInd;
  			$objeto->CPDEC_Costo = $costo;
  			$objeto->PROD_Nombre = $nombre_producto;
  			$objeto->CPDEC_Cantidad = $cantidad;
  			$objeto->CPDEC_Pendiente = $pendiente;
  			$objeto->CPDEC_Pu = $pu;
  			$objeto->CPDEC_Subtotal = $subtotal;
  			$objeto->CPDEC_Descuento = $descuento;
  			$objeto->CPDEC_Igv = $igv;
  			$objeto->CPDEC_Total = $total;
  			$objeto->CPDEC_Pu_ConIgv = $pu_conigv;
  			$objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigv;
  			$objeto->CPDEC_Descuento_ConIgv = $descuento_conigv;
  			$objeto->CPDEC_Descuento100 = $descuento100;
  			$objeto->CPDEC_Igv100 = $igv100;
  			$objeto->CPDEC_Observacion = $observacion;
  			$objeto->ALMAP_Codigo =$almacenProducto;
  			$objeto->GUIAREMP_Codigo =$codigoGuiaremAsociadaDetalle;
  			$objeto->OCOMP_Codigo_VC = $codigovc;
  			$objeto->CPDEC_ITEMS = $icbper;
  			$lista_detalles[] = $objeto;
  		}
  	}
  	return $lista_detalles;
  }


## FORMATOS EN PDF
#########################################################################

  ########################
  ##### PEDIDOS
  ########################

      public function pedido_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

      	$datos_pedido = $this->ci->pedido_model->obtener_pedido($codigo);

      	$codigopedido = $datos_pedido[0]->PEDIP_Codigo;
      	$numero = $datos_pedido[0]->PEDIC_Numero;
      	$serie = $datos_pedido[0]->PEDIC_Serie;

      	$serieNumeroOC = explode("-",$datos_pedido[0]->serieNumero);
      	$serieNumeroRel = $serieNumeroOC[0]." - ".$this->getOrderNumeroSerie($serieNumeroOC[1]);


      	$tipo_oper = $datos_pedido[0]->PEDIC_TipoDocume;
      	$flagPedido = $datos_pedido[0]->PEDIC_FlagEstado;
      	$compania = $datos_pedido[0]->COMPP_Codigo;
      	$observacion = $datos_pedido[0]->PEDIC_Observacion;
      	$estado = $datos_pedido[0]->PEDIC_FlagEstado;
      	$fechaReg = explode(" ",$datos_pedido[0]->PEDIC_FechaRegistro);
      	$fecha = mysql_to_human($fechaReg[0]);

      	$ordencompra = $datos_pedido[0]->OCOMP_Codigo;

      	$cliente = $datos_pedido[0]->CLIP_Codigo;
          #$datos_pedido[0]->PROVP_Codigo;
      	$proveedor = NULL;
      	$personal = $datos_pedido[0]->PERSP_Codigo;
      	$forma_pago = $datos_pedido[0]->FORPAP_Codigo;
      	$moneda = $datos_pedido[0]->MONED_Codigo;

      	$nFechaEntrega = explode( '/', $fecha );
      	$fecha_entrega = $nFechaEntrega[0]." DE ".$this->mesesEs($nFechaEntrega[1])." DEL ".$nFechaEntrega[2];

      	$nombre_almacen = '';
      	$nombre_formapago = '';

      	$datos_moneda = $this->ci->moneda_model->obtener($moneda);
      	$simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
      	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'Soles');

      	if ($proveedor != NULL && $proveedor != ""){
      		$datos_proveedor = $this->proveedor_model->obtener($proveedor);
      		$nombres = $datos_proveedor->nombre;
      		$ruc = $datos_proveedor->ruc;
      		$telefono = $datos_proveedor->telefono;
      		$direccion = $datos_proveedor->direccion;
      		$fax = $datos_proveedor->fax;
      		$tpCliente = "proveedor";
      	}
      	else
      		if ($cliente != NULL && $cliente != ""){
      			$datos_cliente = $this->ci->cliente_model->obtener($cliente);
      			$nombres = $datos_cliente->nombre;
      			$ruc = $datos_cliente->ruc;
      			$telefono = $datos_cliente->telefono;
      			$direccion = $datos_cliente->direccion;
      			$fax = $datos_cliente->fax;
      			$tpCliente = "cliente";
      		}
      		else{
      			$datos_persona = $this->ci->persona_model->obtener_datosPersona($personal);
      			$nombres = $datos_persona[0]->PERSC_Nombre;
      			$ruc = $datos_persona[0]->PERSC_NumeroDocIdentidad." / ".$datos_persona[0]->PERSC_Ruc;
      			$telefono = $datos_persona[0]->PERSC_Telefono." / ".$datos_persona[0]->PERSC_Movil;
      			$direccion = $datos_persona[0]->PERSC_Direccion;
      			$fax = $datos_persona[0]->PERSC_Fax;
      			$tpCliente = "vendedor";
      		}

      		$comppName = $this->ci->pedido_model->nameEstablecimiento($compania);
      		$compp = $comppName[0]->EESTABC_Descripcion;

          #$contacto = $this->persona_model->obtener_datosPersona($contacto);


      		$medidas = "a4";
      		$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
      		$this->pdf->SetMargins(7, 50, 10);
      		$this->pdf->SetTitle('PEDIDO '.$serie.'-'.$numero);
      		$this->pdf->SetFont('times', '', 9);
      		if ($flagPdf == 1)
      			$this->pdf->setPrintHeader(true);
      		else
      			$this->pdf->setPrintHeader(false);

      		$this->pdf->setPrintFooter(false);

      		$this->pdf->settingHeaderData($comppName[0]->EMPRC_Ruc, "ORDEN DE<br>PEDIDO", $serie, $this->getOrderNumeroSerie($numero) );

      		$this->pdf->AddPage();
      		$this->pdf->SetAutoPageBreak(true, 1);

      		/* Listado de detalles */
      		$detalles_pedido = $this->ci->pedidodetalle_model->listar($codigo);
      		$detaProductos = "";
      		$j = 1;
      		foreach ($detalles_pedido as $indice => $valor) {
      			$listaProductos = $this->ci->producto_model->obtener_producto($valor->PROD_Codigo);
      			$unidadMedida = $this->ci->unidadmedida_model->obtener($valor->UNDMED_Codigo);
      			$medidaDetalle = "";
      			$medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

      			$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

      			$detaProductos = $detaProductos. '
      			<tr bgcolor="'.$bgcolor.'">
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$j.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$listaProductos[0]->PROD_CodigoUsuario.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$listaProductos[0]->PROD_Nombre.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$medidaDetalle.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.$valor->PEDIDETC_Cantidad.'</td>
      			</tr>';
      			$j++;
      		}

      		$clienteHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="0">
      		<tr>
      		<td style="width:3.0cm; font-style:normal; font-weight:bold;">A SOLICITUD DE:</td>
      		<td style="width:auto; text-indent:0.1cm;">'.$comppName[0]->EMPRC_RazonSocial.' - '.$comppName[0]->EESTABC_Descripcion.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.0cm; font-style:normal; font-weight:bold;">RUC:</td>
      		<td style="width:auto; text-indent:0.1cm;">'.$ruc.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.0cm; font-style:normal; font-weight:bold;">NOMBRE:</td>
      		<td style="width:auto; text-indent:0.1cm; text-align:justification">'.$nombres.'</td>
      		</tr>
      		<tr> 
      		<td style="width:3.0cm; font-style:normal; font-weight:bold;">DIRECCIÓN:</td>
      		<td style="width:auto; text-indent:0.1cm; text-align:justification">'.$direccion.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.0cm; font-style:normal; font-weight:bold;">FECHA ELAB.:</td>
      		<td style="width:5cm; text-indent:0.1cm; text-align:justification">'.$fecha_entrega.'</td>

      		<td style="width:2.5cm; font-style:normal; font-weight:bold; text-align: right">COTIZACIÓN:</td>
      		<td style="width:auto; text-indent:0cm; text-align:justification">'.$serieNumeroRel.'</td>
      		</tr>
      		</table>';

      		$this->pdf->writeHTML($clienteHTML,true,false,true,'');

      		$productoHTML = '
      		<table cellpadding="0.05cm" style="font-size:8.5pt;">
      		<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:1.5cm;">ITEM</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:2.5cm;">CÓDIGO</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:9.5cm;">DESCRIPCIÓN</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:3.6cm;">UNIDAD</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:right; width:1.8cm;">CANTIDAD</th>
      		</tr>
      		'.$detaProductos.'
      		</table>';
      		$this->pdf->writeHTML($productoHTML,true,false,true,'');

      		$nameFile = "Pedido -".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero)." ".$fecha." ".$nombres.".pdf";

      		if ($enviarcorreo == false)
      			$this->pdf->Output($nameFile, 'I');
      		else
      			return $this->pdf->Output($nameFile, 'S');
      	}

  ########################
  ##### COTIZACION
  ########################

      	public function ocompra_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

      		/* Datos principales */
      		$datos_ocompra = $this->ci->ocompra_model->obtener_ocompra($codigo);
      		$datos_detalle_ocompra = $this->ci->ocompra_model->obtener_detalle_ocompra($codigo);
      		$tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
      		$cotizacion = $datos_ocompra[0]->COTIP_Codigo;
      		$pedido = $datos_ocompra[0]->PEDIP_Codigo;
      		$serie = $datos_ocompra[0]->OCOMC_Serie;
      		$numero = $datos_ocompra[0]->OCOMC_Numero;
      		$descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
      		$descuento = $datos_ocompra[0]->OCOMC_descuento;
      		$igv100 = $datos_ocompra[0]->OCOMC_igv100;
      		$igv = $datos_ocompra[0]->OCOMC_igv;
      		$cliente = $datos_ocompra[0]->CLIP_Codigo;
      		$proveedor = $datos_ocompra[0]->PROVP_Codigo;
      		$centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
      		$moneda = $datos_ocompra[0]->MONED_Codigo;
      		$subtotal = $datos_ocompra[0]->OCOMC_subtotal;
      		$descuentototal = $datos_ocompra[0]->OCOMC_descuento;
      		$igvtotal = $datos_ocompra[0]->OCOMC_igv;
      		$contacto = $datos_ocompra[0]->OCOMC_Personal;
      		$miPersonal = $datos_ocompra[0]->mipersonal;

      		$docCanje = $datos_ocompra[0]->CPC_TipoDocumento;
      		switch ($docCanje) {
      			case 'F':
      			$docToCanje = "FACTURA";
      			break;
      			case 'B':
      			$docToCanje = "BOLETA";
      			break;
      			case 'N':
      			$docToCanje = "COMPROBANTE";
      			break;

      			default:
      			$docToCanje = "COMPROBANTE";
      			break;
      		}

        // AQUI SE GUARDA EL NUMERO DE ORDEN DE COMPRA DEL CLIENTE EN MONTOYA
      		$ordenCompraCliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada;

      		$tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
      		$total = $datos_ocompra[0]->OCOMC_total;
      		$percepcion = $datos_ocompra[0]->OCOMC_percepcion;
      		$percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
      		$observacion = $datos_ocompra[0]->OCOMC_Observacion;
      		$lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
      		$lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;

      		$datosProyecto = $this->ci->proyecto_model->obtener_datosProyecto( $datos_ocompra[0]->PROYP_Codigo );
      		$proyecto = $datosProyecto[0]->PROYC_Nombre;

      		$fechaEntrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '') ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '';
      		$nFechaEntrega = explode( '/', $fechaEntrega );
      		$fecha_entrega = $nFechaEntrega[0]." de ".ucfirst( strtolower($this->mesesEs($nFechaEntrega[1])) )." del ".$nFechaEntrega[2];

      		$fecha_entrega = ($fechaEntrega != "") ? $fecha_entrega : "";

      		$almacen = $datos_ocompra[0]->ALMAP_Codigo;
      		$formapago = $datos_ocompra[0]->FORPAP_Codigo;
      		$ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
      		$ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
      		$tdc = $datos_ocompra[0]->OCOMP_TDC;

      		$nombre_almacen = '';
      		if ($almacen != '') {
      			$datos_almacen = $this->ci->almacen_model->obtener($almacen);
      			$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
      		}
      		$nombre_formapago = '';
      		if ($formapago != '') {
      			$datos_formapago = $this->ci->formapago_model->obtener($formapago);
      			$nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
      		}

      		$datos_moneda = $this->ci->moneda_model->obtener($moneda);
      		$simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
      		$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'Soles');

      		$arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
      		$nFecha = explode('/', mysql_to_human($arrfecha[0]) );
      		$fecha = $nFecha[0]." de ".ucfirst( strtolower($this->mesesEs($nFecha[1])) )." del ".$nFecha[2]."<br>".$this->formatHours($arrfecha[1]); 
      		$flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

      		$idCliente = "";
      		if ($tipo_oper == 'C') {
      			$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
      			$nombres = $datos_proveedor->nombre;
      			$ruc = $datos_proveedor->ruc;
      			$telefono = $datos_proveedor->telefono;
      			$direccion = $datos_proveedor->direccion;
      			$fax = $datos_proveedor->fax;
      			$tipoDocIdentidad = $datos_cliente->tipoDocIdentidad;
      		} else {
      			$datos_cliente = $this->ci->cliente_model->obtener($cliente);
      			$idCliente = $datos_cliente->idCliente;
      			$nombres = $datos_cliente->nombre;
      			$ruc = $datos_cliente->ruc;
      			$telefono = $datos_cliente->telefono;
      			$direccion = $datos_cliente->direccion;
      			$fax = $datos_cliente->fax;
      			$tipoDocIdentidad = $datos_cliente->tipoDocIdentidad;
      		}

      		switch ($tipoDocIdentidad) {
      			case '1':
      			$tipoID="DNI";
      			break;
      			case '6':
      			$tipoID="RUC";
      			break;
      			case '0':
      			$tipoID="ND";
      			break;

      			default:
      			$tipoID="ND";
      			break;
      		}

      		$contacto = $this->ci->empresa_model->get_contacto($contacto);

      		$companiaInfo = $this->ci->compania_model->obtener($datos_ocompra[0]->COMPP_Codigo);
      		$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
      		$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $companiaInfo[0]->EMPRP_Codigo );
      		if($tipo_oper=="V"){
      			$tipoDocumento = "COTIZACIÓN<br>ELECTRÓNICA";
      			$tipoDocumentoF = "COTIZACION ELECTRÓNICA";
      		}else{
      			$tipoDocumento = "ORDEN DE<br>COMPRA";
      			$tipoDocumentoF = "ORDEN DE COMPRA";
      		}

      		$medidas = "a4"; 
      		$this->pdf = new pdfCotizacion('P', 'mm', $medidas, true, 'UTF-8', false);
      		$this->pdf->SetMargins(7, 50, 10);
      		$this->pdf->SetTitle($tipoDocumentoF.' '.$serie.'-'.$numero);
      		$this->pdf->SetFont('freesans', '', 8);
      		if ($flagPdf == 1)
      			$this->pdf->setPrintHeader(true);
      		else
      			$this->pdf->setPrintHeader(false);

      		$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );

      		$this->pdf->setPrintFooter(false);
      		$this->pdf->AddPage();
      		$this->pdf->SetAutoPageBreak(true, 1);
      		$this->pdf->Footer( $miPersonal );


      		/* Listado de detalles */
      		$gravada = 0;
      		$exonerado = 0;
      		$inafecto = 0;
      		$gratuito = 0;

      		$detaProductos = "";
      		foreach ($datos_detalle_ocompra as $indice => $valor) {
      			$nombre_producto = $valor->OCOMDEC_Descripcion .". ". $valor->OCOMDEC_Observacion;
      			$tipo_afectacion = $valor->AFECT_Codigo;

      			$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

      			$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion);

      			switch ($tipo_afectacion) {
      				case 1: 
      				$gravada += $valor->OCOMDEC_Subtotal;
      				break;
      				case 8: 
      				$exonerado += $valor->OCOMDEC_Subtotal;
      				$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
      				break;
      				case 9: 
      				$inafecto += $valor->OCOMDEC_Subtotal;
      				$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
      				break;
      				case 16:
      				$inafecto += $valor->OCOMDEC_Subtotal;
      				$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
      				break;
      				default:
      				$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
      				$gratuito += $valor->OCOMDEC_Subtotal;
      				break;
      			}

      			$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

      			$detaProductos = $detaProductos. '
      			<tr bgcolor="'.$bgcolor.'">
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->OCOMDEC_Cantidad.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu, 2).'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu_ConIgv, 2).'</td>
      			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Total, 2).'</td>
      			</tr>';
      		}

      		$gravada -= ($gravada * $descuento100 / 100);
      		$exonerado -= ($exonerado * $descuento100 / 100);
      		$inafecto -= ($inafecto * $descuento100 / 100);

      		$tpCliente = ($tipo_oper == "V") ? "Cliente" : "Proveedor";
      		$clienteHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="0">
      		<tr>
      		<td colspan="2" bgcolor="#FDFDFD" style="width:auto; font-weight: bold">Dirigido al '.$tpCliente.'</td>
      		</tr>
      		<tr>
      		<td style="width:1.5cm; font-style:italic; font-weight: bold">Código:</td>
      		<td style="width:1.5cm; text-indent:0.1cm;">'.$idCliente.'</td>

      		<td style="width:1.0cm; font-style:italic; font-weight: bold">'.$tipoID.' :</td>
      		<td style="width:2.5cm; text-indent:0.1cm;">'.$ruc.'</td>

      		<td style="width:2.5cm; font-style:italic; font-weight: bold">Razón Social:</td>
      		<td style="text-indent:0.1cm; text-align:justification">'.$nombres.'</td>
      		</tr>
      		<tr>
      		<td style="width:1.7cm; font-style:italic; font-weight: bold">Vendedor:</td>
      		<td colspan="4" style="text-indent:0cm;">'.$miPersonal.'</td>

      		<td>
      		<span style="font-style:italic; font-weight: bold">Telefono: &nbsp;&nbsp;</span>'.$datos_ocompra[0]->PERSC_Telefono.' / '.$datos_ocompra[0]->PERSC_Movil.' 
      		&nbsp;&nbsp;
      		<span style="font-style:italic; font-weight: bold">Correo: &nbsp;&nbsp;</span>'.$datos_ocompra[0]->PERSC_Email.' 
      		</td>
      		</tr>
      		<tr>
      		<td colspan="2" style="font-style:italic; font-weight: bold">Tipo de documento:</td>
      		<td colspan="4" style="text-indent:0.1cm;">'.$docToCanje.'</td>
      		</tr>
      		</table><table style="font-size:8pt;" border="0">
      		<tr>
      		<td style="width:7cm;"><table cellpadding="0.1cm" border="0">
      		<tr> 
      		<td style="width:2cm; font-style:italic; text-indent:-0.1cm; font-weight: bold">Contacto:</td>
      		<td style="text-indent:0.1cm; text-align:justification">'.$contacto[0]->ECONC_Descripcion.'</td>
      		</tr>
      		<tr> 
      		<td style="width:2cm; font-style:italic; text-indent:-0.1cm; font-weight: bold">Teléfono:</td>
      		<td style="text-indent:0.1cm; text-align:justification">'.$contacto[0]->ECONC_Movil.' / '.$contacto[0]->ECONC_Telefono.'</td>
      		</tr>
      		</table>
      		</td>
      		<td style="width:12cm;"><table cellpadding="0.1cm" border="0">
      		<tr>
      		<td style="text-align:center; font-style:italic; font-weight: bold;">Fecha de Elaboración:</td>
      		<td style="text-align:center; font-style:italic; font-weight: bold;">Fecha de Vencimiento:</td>
      		<td style="text-align:center; font-style:italic; font-weight: bold;">Orden de Compra:</td>
      		</tr>
      		<tr>
      		<td style="font-style:italic; text-align:center;">'.$fecha.'</td>
      		<td style="font-style:italic; text-align:center;">'.$fecha_entrega.'</td>
      		<td style="font-style:italic; text-align:center;">'.$ordenCompraCliente.'</td>
      		</tr>
      		</table>
      		</td>
      		</tr>
      		</table>';

      		$this->pdf->writeHTML($clienteHTML,true,false,true,'');


      		$condicionesHTML = '<table border="0" style="width:19.5cm; font-size:8pt;" cellpadding="0.1cm">
      		<tr bgcolor="#F1F1F1">
      		<td style="border-right:1px #000 solid; width:4cm; text-align:center; font-style:italic; font-weight: bold;">Forma de Pago</td>
      		<td style="border-right:1px #000 solid; width:4cm; text-align:center; font-style:italic; font-weight: bold;">Tiempo de Entrega</td>
      		<td style="width:11.5cm; text-align:center; font-style:italic; font-weight: bold;">Lugar de Entrega</td>

      		</tr>
      		<tr>
      		<td style="text-align:center;">'.$nombre_formapago.'</td>
      		<td style="text-align:center;">'.strtoupper($tiempo_entrega).'</td>
      		<td style="text-align:center;">'.$lugar_entrega.'</td>

      		</tr>
      		</table>';
      		$this->pdf->writeHTML($condicionesHTML,true,false,true,'');

      		$productoHTML = '
      		<table cellpadding="0.05cm" style="font-size:8pt;" border="0">
      		<tr bgcolor="#F1F1F1" style="font-size:8pt;">
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Cantidad</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:11.0cm;">Descripción</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">MARCA</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
      		<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Total</th>
      		</tr>
      		'.$detaProductos.'
      		</table>';
      		$this->pdf->writeHTML($productoHTML,true,false,true,'');

      		$descuentoHTML = ( $descuento > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Descuento</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($descuento, 2).'</td>
      		</tr>' : '';

      		$exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Exonerado</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($exonerado, 2).'</td>
      		</tr>' : '';

      		$inafectoHTML = ( $inafecto > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Inafecto</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($inafecto, 2).'</td>
      		</tr>' : '';

      		$gravadaHTML = ( $gravada > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Gravado</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($gravada, 2).'</td>
      		</tr>' : '';

      		$igvHTML = ( $igv > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">18% IGV</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($igv, 2).'</td>
      		</tr>' : '';

      		$gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Gratuito</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td style="width:2.0cm; text-align:right;">'.number_format($gratuito, 2).'</td>
      		</tr>' : '';

      		$totalesHTML = '
      		<table border="0" style="font-weight: bold;">
      		<tr>
      		<td style="width:13cm;"><table border="0">
      		<tr>
      		<td><i>Son:   </i>  '.ucfirst(num2letras(round($total, 2))).' '.ucfirst( strtolower($moneda_nombre) ).'</td>
      		</tr>
      		</table>
      		</td>
      		<td style="width:6cm;"><table style="font-size:8t;" cellspacing="0.1cm" border="0">
      		'.$gratuitoHTML.'
      		'.$descuentoHTML.'
      		'.$exoneradoHTML.'
      		'.$inafectoHTML.'
      		'.$gravadaHTML.'
      		'.$igvHTML.'
      		<tr>
      		<td style="width:3.5cm; text-align:right; font-style:italic;">Total</td>
      		<td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
      		<td bgcolor="#F1F1F1" style="width:2cm; text-align:right;">'.number_format($total, 2).'</td>
      		</tr>
      		</table>
      		</td>
      		</tr>
      		</table>';

      		$this->pdf->writeHTML($totalesHTML,true,false,true,'');

      		$observacionesHTML = ($observacion != "") ? '<table border="0" style="width:19.5cm; font-size:8pt;" cellpadding="0.1cm">
      		<tr bgcolor="#F1F1F1">
      		<td style="text-align:left; font-weight: bold;">OBSERVACIONES</td>
      		</tr>

      		<tr>
      		<td style="text-align:left;">'.$observacion.'</td>
      		</tr>
      		</table>' : "";
      		$this->pdf->writeHTML($observacionesHTML,true,false,true,'');

      		$posY = $this->pdf->GetY();
      		$posX = 170;
      		$this->pdf->RoundedRect(6, $posY, 165, 25, 1.50, '1111', '');
        #$this->pdf->SetY(83);

      		$infoBancos = $this->ci->banco_model->ctas_bancarias($empresaInfo[0]->EMPRP_Codigo);
      		$infoBancosHTML = "";

      		if ($infoBancos != NULL){
      			$infoBancosHTML .= '<tr>
      			<td style="width:16cm; font-weight: bold;">CUENTAS BANCARIAS</td>
      			</tr>
      			<tr>
      			<td style="width:1.3cm; font-weight: bold;">TITULAR</td>
      			<td style="width:14cm; font-weight: normal;">'.$infoBancos[0]->CUENT_Titular.'</td>
      			</tr>';
      			foreach ($infoBancos as $indice => $val) {
      				$tipo_cuenta = ($val->CUENT_TipoCuenta == 1 ) ? "AHORROS" : "CORRIENTE";
      				$infoBancosHTML .= '<tr>
      				<td style="width:1.2cm; font-size: 7pt; font-weight:bold;">BANCO:</td>
      				<td style="width:1.3cm; font-size: 7pt; font-weight:normal;">'.$val->BANC_Siglas.'</td>
      				<td style="width:1.0cm; font-size: 7pt; font-weight:bold;">TIPO:</td>
      				<td style="width:1.7cm; font-size: 7pt; font-weight:normal;">'.$tipo_cuenta.'</td>
      				<td style="width:1.5cm; font-size: 7pt; font-weight:bold;">CUENTA:</td>
      				<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_NumeroEmpresa.'</td>
      				<td style="width:2.2cm; font-size: 7pt; font-weight:bold;">INTERBANCARIA:</td>
      				<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_Interbancaria.'</td>
      				</tr>
      				';
                /*
                $infoBancosHTML .= '<tr>
                                        <td style="width:1.5cm; font-weight:bold;">BANCO:</td>
                                        <td style="width:1.5cm; font-weight:normal;">'.$val->BANC_Siglas.'</td>
                                        <td style="width:2.0cm; font-weight:bold;">N° CUENTA:</td>
                                        <td style="width:11.5cm; font-weight:normal;">'.$val->CUENT_NumeroEmpresa.'</td>
                                    </tr>
                                ';
                */
                              }
                            }

                            $footerHTML = '<table cellspacing="0.05cm" border="0">
                            '.$infoBancosHTML.'
                            <tr>
                            <td style="width:16cm;">REPRESENTACIÓN IMPRESA DE '.$tipoDocumentoF.' '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
                            </tr>
                            </table>
                            ';
        // CODIGO QR INTERNO GENERADO POR EL SISTEMA

                            $style = array(
                            	'border' => 1,
                            	'position' => 'R',
                            	'vpadding' => 'auto',
                            	'hpadding' => 'auto',
                            	'fgcolor' => array(80,80,80),
            'bgcolor' => false, # array(180,180,180),
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
          );

                            if ( strlen($ruc) == 11 )
                            	$truc = "6";
                            else 
                            	if ( strlen($ruc) == 8 )
                            		$truc = "1"; 
                            	else
                            		$truc = "-";

                            	$cadenaQR = $empresaInfo[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
                            	$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 25, 25, $style, "");

                            	$this->pdf->writeHTML($footerHTML,false,false,true,false);

                            	$nameFile = $tipoDocumentoF." - ".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero)." ".$fecha." ".$nombres.".pdf";

                            	if ($enviarcorreo == false)
                            		$this->pdf->Output($nameFile, 'I');
                            	else
                            		return $this->pdf->Output($nameFile, 'S');
                            }

                            public function ocompra_rango($filtro = NULL, $inicio = 1, $fin = 20, $oper = "V", $flagPdf = 1, $enviarcorreo = false){

                            	$tipoDocumento = "COTIZACIÓN<br>ELECTRÓNICA";
                            	$tipoDocumentoF = "COTIZACION ELECTRÓNICA";

                            	$medidas = "a4"; 
                            	$this->pdf = new pdfCotizacion('P', 'mm', $medidas, true, 'UTF-8', false);
                            	$this->pdf->SetMargins(7, 50, 10); 
                            	$this->pdf->SetTitle('COTIZACIÓN '.$inicio.'-'.$fin);
                            	$this->pdf->SetFont('freesans', '', 8);
                            	if ($flagPdf == 1)
                            		$this->pdf->setPrintHeader(true);
                            	else
                            		$this->pdf->setPrintHeader(false);

                            	$this->pdf->setPrintFooter(false);

                            	if ( $filtro == NULL )
                            		$lista_ocompra = $this->ci->ocompra_model->obtener_ocompras_rango($inicio, $fin, $oper);
                            	else
                            		$lista_ocompra = $this->ci->ocompra_model->obtenerOrdenCompraFiltro($filtro);

                            	foreach ($lista_ocompra as $key => $val) {
                            		$codigo = $val->OCOMP_Codigo;

                            		/* Datos principales */
                            		$datos_ocompra = $this->ci->ocompra_model->obtener_ocompra($codigo);
                            		$datos_detalle_ocompra = $this->ci->ocompra_model->obtener_detalle_ocompra($codigo);
                            		$tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
                            		$cotizacion = $datos_ocompra[0]->COTIP_Codigo;
                            		$pedido = $datos_ocompra[0]->PEDIP_Codigo;
                            		$serie = $datos_ocompra[0]->OCOMC_Serie;
                            		$numero = $datos_ocompra[0]->OCOMC_Numero;
                            		$descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
                            		$descuento = $datos_ocompra[0]->OCOMC_descuento;
                            		$igv100 = $datos_ocompra[0]->OCOMC_igv100;
                            		$igv = $datos_ocompra[0]->OCOMC_igv;
                            		$cliente = $datos_ocompra[0]->CLIP_Codigo;
                            		$proveedor = $datos_ocompra[0]->PROVP_Codigo;
                            		$centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
                            		$moneda = $datos_ocompra[0]->MONED_Codigo;
                            		$subtotal = $datos_ocompra[0]->OCOMC_subtotal;
                            		$descuentototal = $datos_ocompra[0]->OCOMC_descuento;
                            		$igvtotal = $datos_ocompra[0]->OCOMC_igv;
                            		$contacto = $datos_ocompra[0]->OCOMC_Personal;
                            		$miPersonal = $datos_ocompra[0]->mipersonal;

                            		$docCanje = $datos_ocompra[0]->CPC_TipoDocumento;
                            		switch ($docCanje) {
                            			case 'F':
                            			$docToCanje = "FACTURA";
                            			break;
                            			case 'B':
                            			$docToCanje = "BOLETA";
                            			break;
                            			case 'N':
                            			$docToCanje = "COMPROBANTE";
                            			break;

                            			default:
                            			$docToCanje = "COMPROBANTE";
                            			break;
                            		}

            $ordenCompraCliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada; // AQUI SE GUARDA EL NUMERO DE ORDEN DE COMPRA DEL CLIENTE EN MONTOYA
            
            $tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
            $total = $datos_ocompra[0]->OCOMC_total;
            $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
            $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
            $observacion = $datos_ocompra[0]->OCOMC_Observacion;
            $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
            $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
            
            $datosProyecto = $this->ci->proyecto_model->obtener_datosProyecto( $datos_ocompra[0]->PROYP_Codigo );
            $proyecto = $datosProyecto[0]->PROYC_Nombre;

            $fechaEntrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '') ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '';
            $nFechaEntrega = explode( '/', $fechaEntrega );
            $fecha_entrega = $nFechaEntrega[0]." de ".ucfirst( strtolower($this->mesesEs($nFechaEntrega[1])) )." del ".$nFechaEntrega[2];

            $fecha_entrega = ($fechaEntrega != "") ? $fecha_entrega : "";

            $almacen = $datos_ocompra[0]->ALMAP_Codigo;
            $formapago = $datos_ocompra[0]->FORPAP_Codigo;
            $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
            $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
            $tdc = $datos_ocompra[0]->OCOMP_TDC;

            $nombre_almacen = '';
            if ($almacen != '') {
            	$datos_almacen = $this->ci->almacen_model->obtener($almacen);
            	$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
            }
            $nombre_formapago = '';
            if ($formapago != '') {
            	$datos_formapago = $this->ci->formapago_model->obtener($formapago);
            	$nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
            }

            $datos_moneda = $this->ci->moneda_model->obtener($moneda);
            $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
            $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'Soles');

            $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
            $nFecha = explode('/', mysql_to_human($arrfecha[0]) );
            $fecha = $nFecha[0]." de ".ucfirst( strtolower($this->mesesEs($nFecha[1])) )." del ".$nFecha[2]."<br>".$this->formatHours($arrfecha[1]); 
            $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

            $idCliente = "";
            if ($tipo_oper == 'C') {
            	$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
            	$nombres = $datos_proveedor->nombre;
            	$ruc = $datos_proveedor->ruc;
            	$telefono = $datos_proveedor->telefono;
            	$direccion = $datos_proveedor->direccion;
            	$fax = $datos_proveedor->fax;
            } else {
            	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
            	$idCliente = $datos_cliente->idCliente;
            	$nombres = $datos_cliente->nombre;
            	$ruc = $datos_cliente->ruc;
            	$telefono = $datos_cliente->telefono;
            	$direccion = $datos_cliente->direccion;
            	$fax = $datos_cliente->fax;
            }

            $contacto = $this->ci->persona_model->obtener_datosPersona($contacto);

            $companiaInfo = $this->ci->compania_model->obtener($datos_ocompra[0]->COMPP_Codigo);
            $establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
            $empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

            $this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );
            $posY = $this->pdf->getY();
            #if ($posY > "275.000000")
            $this->pdf->AddPage();
            $this->pdf->SetAutoPageBreak(true, 1);
            $this->pdf->printHeaderData();
            
            /* Listado de detalles */
            $gravada = 0;
            $exonerado = 0;
            $inafecto = 0;
            $gratuito = 0;

            $detaProductos = "";
            foreach ($datos_detalle_ocompra as $indice => $valor) {
            	$nombre_producto = $valor->OCOMDEC_Descripcion .". ". $valor->OCOMDEC_Observacion;
            	$tipo_afectacion = $valor->AFECT_Codigo;

            	$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

            	$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

            	switch ($tipo_afectacion) {
            		case 1: 
            		$gravada += $valor->OCOMDEC_Subtotal;
            		break;
            		case 8: 
            		$exonerado += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		case 9: 
            		$inafecto += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		case 16:
            		$inafecto += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		default:
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		$gratuito += $valor->OCOMDEC_Subtotal;
            		break;
            	}

            	$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

            	$detaProductos = $detaProductos. '
            	<tr bgcolor="'.$bgcolor.'">
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->OCOMDEC_Cantidad.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu, 2).'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu_ConIgv, 2).'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Total, 2).'</td>
            	</tr>';
            }

            $gravada -= ($gravada * $descuento100 / 100);
            $exonerado -= ($exonerado * $descuento100 / 100);
            $inafecto -= ($inafecto * $descuento100 / 100);

            $tpCliente = ($tipo_oper == "V") ? "Cliente" : "Proveedor";
            $clienteHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="1">
            <tr>
            <td colspan="2" bgcolor="#FDFDFD" style="width:auto; font-weight: bold">Dirigido al '.$tpCliente.'</td>
            </tr>
            <tr>
            <td style="width:1.5cm; font-style:italic; font-weight: bold">Código:</td>
            <td style="width:1.5cm; text-indent:0.1cm;">'.$idCliente.'</td>

            <td style="width:1.0cm; font-style:italic; font-weight: bold">RUC:</td>
            <td style="width:2.5cm; text-indent:0.1cm;">'.$ruc.'</td>

            <td style="width:2.5cm; font-style:italic; font-weight: bold">Razón Social:</td>
            <td style="text-indent:0.1cm; text-align:justification">'.$nombres.'</td>
            </tr>
            <tr>
            <td style="width:1.7cm; font-style:italic; font-weight: bold">Vendedor:</td>
            <td colspan="4" style="text-indent:0cm;">'.$miPersonal.'</td>

            <td>
            <span style="font-style:italic; font-weight: bold">Telefono: &nbsp;&nbsp;</span>'.$datos_ocompra[0]->PERSC_Telefono.' / '.$datos_ocompra[0]->PERSC_Movil.' 
            &nbsp;&nbsp;
            <span style="font-style:italic; font-weight: bold">Correo: &nbsp;&nbsp;</span>'.$datos_ocompra[0]->PERSC_Email.' 
            </td>
            </tr>
            <tr>
            <td colspan="2" style="font-style:italic; font-weight: bold">Tipo de documento:</td>
            <td colspan="4" style="text-indent:0.1cm;">'.$docToCanje.'</td>
            </tr>
            </table><table style="font-size:8pt;" border="0">
            <tr>
            <td style="width:7cm;"><table cellpadding="0.1cm" border="0">
            <tr> 
            <td style="width:2cm; font-style:italic; text-indent:-0.1cm; font-weight: bold">Teléfono:</td>
            <td style="text-indent:0.1cm; text-align:justification">'.$contacto[0]->PERSC_Movil.'  '.$contacto[0]->PERSC_Telefono.'</td>
            </tr>
            <tr> 
            <td style="width:2cm; font-style:italic; text-indent:-0.1cm; font-weight: bold">Contacto:</td>
            <td style="text-indent:0.1cm; text-align:justification">'.$contacto[0]->PERSC_Nombre.'</td>
            </tr>
            </table>
            </td>
            <td style="width:12cm;"><table cellpadding="0.1cm" border="0">
            <tr>
            <td style="text-align:center; font-style:italic; font-weight: bold;">Fecha de Elaboración:</td>
            <td style="text-align:center; font-style:italic; font-weight: bold;">Fecha de Vencimiento:</td>
            <td style="text-align:center; font-style:italic; font-weight: bold;">Orden de Compra:</td>
            </tr>
            <tr>
            <td style="font-style:italic; text-align:center;">'.$fecha.'</td>
            <td style="font-style:italic; text-align:center;">'.$fecha_entrega.'</td>
            <td style="font-style:italic; text-align:center;">'.$ordenCompraCliente.'</td>
            </tr>
            </table>
            </td>
            </tr>
            </table>';

            $this->pdf->writeHTML($clienteHTML,true,false,true,'');


            $condicionesHTML = '<table border="0" style="width:19.5cm; font-size:8pt;" cellpadding="0.1cm">
            <tr bgcolor="#F1F1F1">
            <td style="border-right:1px #000 solid; width:4cm; text-align:center; font-style:italic; font-weight: bold;">Forma de Pago</td>
            <td style="border-right:1px #000 solid; width:4cm; text-align:center; font-style:italic; font-weight: bold;">Tiempo de Entrega</td>
            <td style="border-right:1px #000 solid; width:7cm; text-align:center; font-style:italic; font-weight: bold;">Lugar de Entrega</td>
            <td style="width:4.5cm; text-align:center; font-style:italic; font-weight: bold;">Proyecto</td>
            </tr>
            <tr>
            <td style="text-align:center;">'.$nombre_formapago.'</td>
            <td style="text-align:center;">'.strtoupper($tiempo_entrega).'</td>
            <td style="text-align:center;">'.$lugar_entrega.'</td>
            <td style="text-align:center;">'.$proyecto.'</td>
            </tr>
            </table>';
            $this->pdf->writeHTML($condicionesHTML,true,false,true,'');

            $productoHTML = '
            <table cellpadding="0.05cm" style="font-size:8pt;" border="0">
            <tr bgcolor="#F1F1F1" style="font-size:8pt;">
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Cantidad</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:11.0cm;">Descripción</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">MARCA</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Total</th>
            </tr>
            '.$detaProductos.'
            </table>';
            $this->pdf->writeHTML($productoHTML,true,false,true,'');

            $descuentoHTML = ( $descuento > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Exonerado</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($descuento, 2).'</td>
            </tr>' : '';

            $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Exonerado</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($exonerado, 2).'</td>
            </tr>' : '';

            $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Inafecto</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($inafecto, 2).'</td>
            </tr>' : '';

            $gravadaHTML = ( $gravada > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Gravado</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($gravada, 2).'</td>
            </tr>' : '';

            $igvHTML = ( $igv > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">18% IGV</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($igv, 2).'</td>
            </tr>' : '';

            $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Gratuito</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($gratuito, 2).'</td>
            </tr>' : '';

            $totalesHTML = '
            <table border="0" style="font-weight: bold;">
            <tr>
            <td style="width:13cm;"><table border="0">
            <tr>
            <td><i>Son:   </i>  '.ucfirst(num2letras(round($total, 2))).' '.ucfirst( strtolower($moneda_nombre) ).'</td>
            </tr>
            </table>
            </td>
            <td style="width:6cm;"><table style="font-size:8t;" cellspacing="0.1cm" border="0">
            '.$gratuitoHTML.'
            '.$descuentoHTML.'
            '.$exoneradoHTML.'
            '.$inafectoHTML.'
            '.$gravadaHTML.'
            '.$igvHTML.'
            <tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Total</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td bgcolor="#F1F1F1" style="width:2cm; text-align:right;">'.number_format($total, 2).'</td>
            </tr>
            </table>
            </td>
            </tr>
            </table>';

            $this->pdf->writeHTML($totalesHTML,true,false,true,'');
          }

          $nameFile = "COTIZACION DESDE ".$this->getOrderNumeroSerie($inicio)." HASTA ".$this->getOrderNumeroSerie($fin).".pdf";

          if ($enviarcorreo == false)
          	$this->pdf->Output($nameFile, 'I');
          else
          	return $this->pdf->Output($nameFile, 'S');
        }

        public function ocompra_rango_group($inicio = 1, $fin = 20, $oper = "V", $flagPdf = 1, $enviarcorreo = false){

        	$tipoDocumento = "COTIZACIÓN<br>ELECTRÓNICA";
        	$tipoDocumentoF = "COTIZACION ELECTRÓNICA";

        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(7, 15, 10); 
        	$this->pdf->SetTitle('COTIZACIONES DESDE '.$inicio.' HASTA '.$fin);
        	$this->pdf->SetFont('freesans', '', 7);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(true);
        	$this->pdf->SetAutoPageBreak(true, 10);
        	$this->pdf->AddPage();

        	/* Datos principales */

        	$lista_ocompra = $this->ci->ocompra_model->obtener_ocompras_rango($inicio, $fin, $oper);

        	foreach ($lista_ocompra as $key => $val) {
        		$posY = $this->pdf->getY();
        		if ($posY > "275.000000")
        			$this->pdf->AddPage();

        		$codigo = $val->OCOMP_Codigo;

        		$datos_ocompra = $this->ci->ocompra_model->obtener_ocompra($codigo);
        		$datos_detalle_ocompra = $this->ci->ocompra_model->obtener_detalle_ocompra($codigo);
        		$tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        		$cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        		$pedido = $datos_ocompra[0]->PEDIP_Codigo;
        		$serie = $datos_ocompra[0]->OCOMC_Serie;
        		$numero = $datos_ocompra[0]->OCOMC_Numero;
        		$descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        		$descuento = $datos_ocompra[0]->OCOMC_descuento;
        		$igv100 = $datos_ocompra[0]->OCOMC_igv100;
        		$igv = $datos_ocompra[0]->OCOMC_igv;
        		$cliente = $datos_ocompra[0]->CLIP_Codigo;
        		$proveedor = $datos_ocompra[0]->PROVP_Codigo;
        		$centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        		$moneda = $datos_ocompra[0]->MONED_Codigo;
        		$subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        		$descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        		$igvtotal = $datos_ocompra[0]->OCOMC_igv;
        		$contacto = $datos_ocompra[0]->OCOMC_Personal;
        		$miPersonal = $datos_ocompra[0]->mipersonal;
            $ordenCompraCliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada; // AQUI SE GUARDA EL NUMERO DE ORDEN DE COMPRA DEL CLIENTE EN MONTOYA
            
            $tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
            $total = $datos_ocompra[0]->OCOMC_total;
            $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
            $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
            $observacion = $datos_ocompra[0]->OCOMC_Observacion;
            $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
            $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
            
            $fechaEntrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '') ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '';
            $nFechaEntrega = explode( '/', $fechaEntrega );
            $fecha_entrega = $nFechaEntrega[0]." de ".ucfirst( strtolower($this->mesesEs($nFechaEntrega[1])) )." del ".$nFechaEntrega[2];

            $fecha_entrega = ($fechaEntrega != "") ? $fecha_entrega : "";

            $almacen = $datos_ocompra[0]->ALMAP_Codigo;
            $formapago = $datos_ocompra[0]->FORPAP_Codigo;
            $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
            $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
            $tdc = $datos_ocompra[0]->OCOMP_TDC;

            $nombre_almacen = '';
            if ($almacen != '') {
            	$datos_almacen = $this->ci->almacen_model->obtener($almacen);
            	$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
            }
            $nombre_formapago = '';
            if ($formapago != '') {
            	$datos_formapago = $this->ci->formapago_model->obtener($formapago);
            	$nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
            }

            $datos_moneda = $this->ci->moneda_model->obtener($moneda);
            $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
            $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'Soles');

            $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_Fecha);
            $nFecha = explode( '/', mysql_to_human($arrfecha[0]) );
            $fecha = $nFecha[0]." de ".ucfirst( strtolower($this->mesesEs($nFecha[1])) )." del ".$nFecha[2]; 
            $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

            $idCliente = "";

            if ($tipo_oper == 'C') {
            	$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
            	$nombres = $datos_proveedor->nombre;
            	$ruc = $datos_proveedor->ruc;
            	$telefono = $datos_proveedor->telefono;
            	$direccion = $datos_proveedor->direccion;
            	$fax = $datos_proveedor->fax;
            } else {
            	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
            	$idCliente = $datos_cliente->idCliente;
            	$nombres = $datos_cliente->nombre;
            	$ruc = $datos_cliente->ruc;
            	$telefono = $datos_cliente->telefono;
            	$direccion = $datos_cliente->direccion;
            	$fax = $datos_cliente->fax;
            }

            $contacto = $this->ci->persona_model->obtener_datosPersona($contacto);

            /* Listado de detalles */
            $gravada = 0;
            $exonerado = 0;
            $inafecto = 0;
            $gratuito = 0;

            $detaProductos = "";
            foreach ($datos_detalle_ocompra as $indice => $valor) {
            	$nombre_producto = $valor->OCOMDEC_Descripcion .". ". $valor->OCOMDEC_Observacion;
            	$tipo_afectacion = $valor->AFECT_Codigo;

            	$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

            	$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

            	switch ($tipo_afectacion) {
            		case 1: 
            		$gravada += $valor->OCOMDEC_Subtotal;
            		break;
            		case 8: 
            		$exonerado += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		case 9: 
            		$inafecto += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		case 16:
            		$inafecto += $valor->OCOMDEC_Subtotal;
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		break;
            		default:
            		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            		$gratuito += $valor->OCOMDEC_Subtotal;
            		break;
            	}

            	$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

            	$detaProductos = $detaProductos. '
            	<tr bgcolor="'.$bgcolor.'">
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->OCOMDEC_Cantidad.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu, 2).'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Pu_ConIgv, 2).'</td>
            	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->OCOMDEC_Total, 2).'</td>
            	</tr>';
            }

            $tpCliente = ($tipo_oper == "V") ? "Cliente" : "Proveedor";
            $clienteHTML = '<table style="font-size:7pt;" cellpadding="0.1cm" border="0">
            <tr>
            <td colspan="2" bgcolor="#FDFDFD" style="width:auto; font-weight: bold">Cotización: '.$this->getOrderNumeroSerie($serie).'-'.$this->getOrderNumeroSerie($numero).'</td>
            </tr>
            <tr>
            <td style="width:1.5cm; font-style:italic; font-weight: bold">Código:</td>
            <td style="width:1.5cm; text-indent:0.1cm;">'.$idCliente.'</td>

            <td style="width:1.0cm; font-style:italic; font-weight: bold">RUC:</td>
            <td style="width:2.5cm; text-indent:0.1cm;">'.$ruc.'</td>

            <td style="width:2.0cm; font-style:italic; font-weight: bold">Razón Social:</td>
            <td style="text-indent:0.1cm; text-align:justification">'.$nombres.'</td>
            </tr>
            <tr>
            <td style="width:1.8cm; font-style:italic; font-weight: bold">Vendedor:</td>
            <td colspan="4" style="text-indent:0.1cm;">'.$miPersonal.'</td>
            </tr>
            </table>';

            $this->pdf->writeHTML($clienteHTML,false,false,true,'');

            $productoHTML = '
            <table cellpadding="0.05cm" style="font-size:6pt;" border="0">
            <tr bgcolor="#F1F1F1" style="font-size:6.5pt;">
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Cantidad</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:10cm;">Descripción</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:3.5cm;">MARCA</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Total</th>
            </tr>
            '.$detaProductos.'
            </table>';
            $this->pdf->writeHTML($productoHTML,false,false,true,'');

            $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Exonerado</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($exonerado, 2).'</td>
            </tr>' : '';

            $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Inafecto</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($inafecto, 2).'</td>
            </tr>' : '';

            $gravadaHTML = ( $gravada > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Gravado</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($gravada, 2).'</td>
            </tr>' : '';

            $igvHTML = ( $igv > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">18% IGV</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($igv, 2).'</td>
            </tr>' : '';

            $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Gratuito</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td style="width:2.0cm; text-align:right;">'.number_format($gratuito, 2).'</td>
            </tr>' : '';

            $totalesHTML = '
            <table border="0" style="font-weight: bold;">
            <tr>
            <td style="width:13cm;"><table border="0">
            <tr>
            <td><i>Son:   </i>  '.ucfirst(num2letras(round($total, 2))).' '.ucfirst( strtolower($moneda_nombre) ).'</td>
            </tr>
            </table>
            </td>
            <td style="width:6cm;"><table style="font-size:7t;" cellspacing="0.1cm" border="0">
            <tr>
            <td style="width:3.5cm; text-align:right; font-style:italic;">Total</td>
            <td style="width:0.5cm; text-align:right; font-style:italic;">' . $simbolo_moneda . '</td>
            <td bgcolor="#F1F1F1" style="width:2cm; text-align:right;">'.number_format($total, 2).'</td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            <hr>
            ';
            $this->pdf->writeHTML($totalesHTML,true,false,true,'');
          }

          $nameFile = "COTIZACIONES DESDE ".$this->getOrderNumeroSerie($inicio)." HASTA ".$this->getOrderNumeroSerie($fin).".pdf";

          if ($enviarcorreo == false)
          	$this->pdf->Output($nameFile, 'I');
          else
          	return $this->pdf->Output($nameFile, 'S');
        }

  ########################
  ##### COMPROBANTE
  ########################

        public function comprobante_pdf_ticket($codigo, $enviarcorreo = false){   
        	$datos_comprobante = $this->ci->comprobante_model->obtener_comprobante($codigo);

        // DATOS DEL COMPROBANTE
        	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
        	$presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        	$serie = $datos_comprobante[0]->CPC_Serie;
        	$numero = $datos_comprobante[0]->CPC_Numero;
        	$descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        	$descuento100 = $datos_comprobante[0]->CPC_descuento100;
        	$descuento = $datos_comprobante[0]->CPC_descuento;
        	$igv = $datos_comprobante[0]->CPC_igv;
        	$igv100 = $datos_comprobante[0]->CPC_igv100;
        	$subtotal = $datos_comprobante[0]->CPC_subtotal;
        	$subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        	$total = $datos_comprobante[0]->CPC_total;
        	$observacion = $datos_comprobante[0]->CPC_Observacion;
        	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        	$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);
        	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
        	$estado = $datos_comprobante[0]->CPC_FlagEstado;

        	/*DATOS MONEDA*/
        	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        	/*FORMA DE PAGO*/
        	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        	$formapago_desc = ($datos_formapago[0]->FORPAC_Descripcion == '' || $datos_formapago[0]->FORPAC_Descripcion == NULL) ? "EFECTIVO" : $datos_formapago[0]->FORPAC_Descripcion;

        	/*CONSULTO LA COTIZACION Y LA ORDEN DE COMPRA*/
        	//$serieOC = ($datos_comprobante[0]->OCOMC_Serie != NULL) ? $datos_comprobante[0]->OCOMC_Serie."-".$this->getOrderNumeroSerie($datos_comprobante[0]->OCOMC_Numero) : "";
                $serieOC = "";

        	$ordenCompraCliente = "";
        	/*if ( $datos_comprobante[0]->ordenCompra != NULL )
        		$ordenCompraCliente = $datos_comprobante[0]->ordenCompra;

        	if ( $datos_comprobante[0]->CPP_Compracliente != NULL ){
        		if ( $ordenCompraCliente != "" ) 
        			$ordenCompraCliente = $ordenCompraCliente . " | ";

        		$ordenCompraCliente .= $datos_comprobante[0]->CPP_Compracliente;
        	}*/

        // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
        	//$consulta_guia = $this->ci->comprobante_model->buscar_guiarem_comprobante($codigo);
        	$guiaRemision = "";
        	/*foreach ($consulta_guia as $key => $value) {
        		$guiaRemision = ($guiaRemision == "") ? $guiaRemision."$value->GUIAREMC_Serie - $value->GUIAREMC_Numero" : $guiaRemision." | $value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
        	}*/

        // DATOS DEL USUARIO
        	$vendedor = $datos_comprobante[0]->vendedor;
        /*$vendedor = $datos_comprobante[0]->USUA_Codigo;
        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;*/
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $idCliente = "";

        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        $detalle_comprobante = $this->ci->comprobantedetalle_model->detalles($codigo);

        $medidas = array(80, 200); // TICKET
        $this->pdf = new tcpdf('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(3, 3, 3);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->AddPage();

        /* Listado de detalles */

        $gravada = 0;
        $exonerado = 0;
        $inafecto = 0;
        $gratuito = 0;
        $tdescuento = 0;
        $importeBolsa = 0;

        $detaProductos = "";
        foreach ($detalle_comprobante as $indice => $valor) {
        	$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
        	$nombre_producto = ($valor->CPDEC_Observacion != '') ? $nombre_producto . ". " .$valor->CPDEC_Observacion : $nombre_producto;

        	$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

        	$tipo_afectacion = $valor->AFECT_Codigo;
        	$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

        	switch ($tipo_afectacion) {
        		case 1: 
        		$gravada += $valor->CPDEC_Subtotal;
        		break;
        		case 8: 
        		$exonerado += $valor->CPDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 9: 
        		$inafecto += $valor->CPDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 16:
        		$inafecto += $valor->CPDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		default:
                        $gratuito = ( $valor->CPDEC_ITEMS == "1" ) ? $gratuito : $gratuito + $valor->CPDEC_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                        break;
                      }

                $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $importeBolsa + $valor->CPDEC_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                $cantidad = ( strlen($valor->CPDEC_Cantidad) == 1) ? "0".$valor->CPDEC_Cantidad : $valor->CPDEC_Cantidad;

                $nombre_producto = ($valor->MARCC_Descripcion != '') ? "$nombre_producto. MARCA: $valor->MARCC_Descripcion" : $nombre_producto;
                #$nombre_producto = ($valor->LOTC_Numero != '') ? "$nombre_producto. LOTE: $valor->LOTC_Numero" : $nombre_producto;
                #$nombre_producto = ($valor->LOTC_FechaVencimiento != '') ? "$nombre_producto. VCTO LOTE: ". mysql_to_human($valor->LOTC_FechaVencimiento) : $nombre_producto;

                $detaProductos = $detaProductos. '
                <tr>
                <td style="border-top:#D1D1D1 1mm solid; text-align:left"><b>['.$cantidad.']</b> '.$nombre_producto.'</td>
                <td style="border-top:#D1D1D1 1mm solid; text-align:right">'.number_format($valor->CPDEC_Pu_ConIgv, 2).'</td>
                <td style="border-top:#D1D1D1 1mm solid; text-align:right">'.number_format($valor->CPDEC_Total, 2).'</td>
                </tr>';
              }

              $gravada -= ($gravada * $descuento100 / 100);
              $exonerado -= ($exonerado * $descuento100 / 100);
              $inafecto -= ($inafecto * $descuento100 / 100);

              $datosCompania = $this->ci->compania_model->obtener($companiaComprobante);
              $datosEstablecimiento = $this->ci->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
              $datosEmpresa =  $this->ci->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

              $tipoDocumento = "";

              switch ($tipo_docu) {
              	case 'F':
              	$tipoDocumento = ($tipo_oper == 'V') ?  "FACTURA DE VENTA<br>ELECTRÓNICA" :  "FACTURA DE COMPRA<br>ELECTRÓNICA";
              	$tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA ELECTRÓNICA DE VENTA" :  "FACTURA ELECTRÓNICA DE COMPRA";
              	$tdocQR = "1";
              	break;
              	case 'B':
              	$tipoDocumento = ($tipo_oper == 'V') ?  "BOLETA DE VENTA<br>ELECTRÓNICA" :  "BOLETA DE COMPRA<br>ELECTRÓNICA";
              	$tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA ELECTRÓNICA DE VENTA" :  "BOLETA ELECTRÓNICA DE COMPRA";
              	$tdocQR = "3";
              	break;
              	case 'N':
              	$tipoDocumento = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
              	$tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
              	$tdocQR = "";
              	break;
              }

              $cabeceraHTML = '<table align="center">';
              if ($tipo_docu != 'N'){
              	$cabeceraHTML .= '
              	<tr>
              	<td><b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b></td>
              	</tr>
              	<tr>
              	<td><b>'.$datosEstablecimiento[0]->EESTAC_Direccion.'</b></td>
              	</tr>
              	<tr>
              	<td><b>'.$datosEstablecimiento[0]->distrito.' - '.$datosEstablecimiento[0]->provincia.' - '.$datosEstablecimiento[0]->departamento.'</b></td>
              	</tr>
              	<tr>
              	<td><b>RUC '.$datosEmpresa[0]->EMPRC_Ruc.'</b></td>
              	</tr>';
              }

              $cabeceraHTML .= '
              <tr>
              <td><b>'.$tipoDocumento.'</b></td>
              </tr>
              <tr>
              <td><b>'.$serie.'-'.$this->getOrderNumeroSerie($numero).'</b></td>
              </tr>
              </table>';
              $this->pdf->writeHTML($cabeceraHTML,true,false,true,'');

              $direccion = ( $direccion != '' ) ? '<tr> <td>'.$direccion.'</td> </tr>' : '';

              $clienteHTML = '
              <table>
              <tr>
              <td><b>'.$tp.'</b></td>
              </tr>
              <tr>
              <td><b>CÓDIGO:</b> '.$idCliente.'</td>
              </tr>
              <tr>
              <td><b>RUC:</b> '.$ruc.'</td>
              </tr>
              <tr>
              <td>'.$nombre_cliente.'</td>
              </tr>
              '.$direccion.'
              <tr>
              <td><b>FECHA DE EMISIÓN:</b> '.$fecha.'</td>
              </tr>
              <tr>
              <td><b>FECHA DE VENCIMIENTO:</b> '.$fecha_vencimiento.'</td>
              </tr>
              <tr>
              <td><b>MONEDA:</b> '.$moneda_nombre.'</td>
              </tr>
              <tr>
              <td><b>IGV: </b>'.$igv100.'%</td>
              </tr>
              </table>';
              $this->pdf->writeHTML($clienteHTML,true,false,true,'');

              $productoHTML = '
              <table border="0">
              <tr>
              <td width="5.2cm"><b>[CANT.] DESCRIPCION</b></td>
              <td width="1.0cm" style="text-align:right"><b>P/U</b></td>
              <td width="1.2cm" style="text-align:right"><b>TOTAL</b></td>
              </tr>
              '.$detaProductos.'
              </table>';
              $this->pdf->writeHTML($productoHTML,true,false,true,'');

              $descuentoHTML = ( $descuento > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">DESCUENTO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($descuento, 2).'</td>
              </tr>' : '';

              $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">EXONERADO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($exonerado, 2).'</td>
              </tr>' : '';

              $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">INAFECTO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($inafecto, 2).'</td>
              </tr>' : '';

              $gravadaHTML = ( $gravada > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">GRAVADO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($gravada, 2).'</td>
              </tr>' : '';

              $igvHTML = ( $igv > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">18% IGV</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($igv, 2).'</td>
              </tr>' : '';

              $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">GRATUITO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($gratuito, 2).'</td>
              </tr>' : '';

              $importeBolsaHTML = ( $importeBolsa > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">IMPUESTO BOLSA</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($importeBolsa, 2).'</td>
              </tr>' : '';

              $totalesHTML = '
              <table border="0" style="font-weight: bold; padding: 0;">
              '.$gratuitoHTML.'
              '.$descuentoHTML.'
              '.$exoneradoHTML.'
              '.$inafectoHTML.'
              '.$gravadaHTML.'
              '.$igvHTML.'
              '.$impuestoBolsaHTML.'
              <tr>
              <td style="width:5.2cm; text-align:right;">Total</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($total, 2).'</td>
              </tr>
              </table>';

              $this->pdf->writeHTML($totalesHTML,true,false,true,'');

              $guiaRemHTML = ($guiaRemision != '') ? '<tr>
              <td><b>GUIA DE REMISIÓN:</b> '.$guiaRemision.'</td>
              </tr>' : '';
              $serieOCHTML = ($serieOC != "") ? '<tr>
              <td><b>COTIZACIÓN:</b> '.$serieOC.'</td>
              </tr>' : '';
              $ordenCompraClienteHTML = ($ordenCompraCliente != "") ? '<tr>
              <td><b>ORDEN DE COMPRA:</b> '.$ordenCompraCliente.'</td>
              </tr>' : '';
              $observacionHTML = ($observacion != "") ? '<tr>
              <td><b>OBSERVACIÓN:</b> '.$observacion.'</td>
              </tr>' : '';

              $footerHTML = '
              <table cellspacing="1px" align="justify">
              <tr>
              <td><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
              </tr>
              <tr>
              <td><b>FORMA DE PAGO: </b>'.$formapago_desc.'</td>
              </tr>
              <tr>
              <td><b>VENDEDOR: </b>'.$vendedor.'</td>
              </tr>
              '.$ordenCompraClienteHTML.'
              '.$serieOCHTML.'
              '.$guiaRemHTML.'
              '.$observacionHTML.'
              </table>
              ';

              $this->pdf->writeHTML($footerHTML,true,false,true,'');

        // CODIGO DE QR GENERADO POR EL SISTEMA
              if ($tipo_docu != 'N'){
              	$style = array(
              		'border' => 2,
              		'position' => 'C',
              		'vpadding' => 'auto',
              		'hpadding' => 'auto',
              		'fgcolor' => array(40,40,40),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
              );

              	if ( strlen($ruc) == 11 )
              		$truc = "6";
              	else 
              		if ( strlen($ruc) == 8 )
              			$truc = "1"; 
              		else
              			$truc = "-";

              		$cadenaQR = $datosEmpresa[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
              		$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 30, 30, $style, "");

              		$posY = $this->pdf->GetY();
              		$posY += 32;
              		$this->pdf->SetY($posY);
              	}

              	$footerHTML = '<table cellspacing="1px" style="font-size:7pt; text-align:center;">
              	<tr>
              	<td>REPRESENTACIÓN IMPRESA DE '.$tipoDocumentoF.': '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
              	</tr>
              	</table>';
              	$this->pdf->writeHTML($footerHTML,false,false,true,'');

              	if ($estado == 0){
              		$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 5, 15, 70, 70, '', '', '', false, 300);
              	}

              	if ($enviarcorreo == false)
              		$this->pdf->Output("Ticket.pdf", 'I');
              	else
              		return $this->pdf->Output("Ticket.pdf", 'S');
              }

        public function comprobante_pdf_a4($codigo, $flagPdf = 1, $enviarcorreo = false){   
              	$datos_comprobante = $this->ci->comprobante_model->obtener_comprobante($codigo);

        // DATOS DEL COMPROBANTE
              	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
              	$presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
              	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
              	$serie = $datos_comprobante[0]->CPC_Serie;
              	$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
              	$descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
              	$descuento100 = $datos_comprobante[0]->CPC_descuento100;
              	$descuento = $datos_comprobante[0]->CPC_descuento;
              	$igv = $datos_comprobante[0]->CPC_igv;
              	$igv100 = $datos_comprobante[0]->CPC_igv100;
              	$subtotal = $datos_comprobante[0]->CPC_subtotal;
              	$subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
              	$total = $datos_comprobante[0]->CPC_total;
              	$observacion = $datos_comprobante[0]->CPC_Observacion;
              	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
              	$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);
              	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
              	$estado = $datos_comprobante[0]->CPC_FlagEstado;

              	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
              	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
              	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

              	/*CONSULTO LA COTIZACION Y LA ORDEN DE COMPRA*/
              	$serieOC = ($datos_comprobante[0]->OCOMC_Serie != NULL) ? $datos_comprobante[0]->OCOMC_Serie."-".$this->getOrderNumeroSerie($datos_comprobante[0]->OCOMC_Numero) : "";

              	$ordenCompraCliente = "";
              	if ( $datos_comprobante[0]->ordenCompra != NULL )
              		$ordenCompraCliente = $datos_comprobante[0]->ordenCompra;

              	if ( $datos_comprobante[0]->CPP_Compracliente != NULL ){
              		if ($ordenCompraCliente != "" && $ordenCompraCliente != $datos_comprobante[0]->CPP_Compracliente){
              			$ordenCompraCliente = $ordenCompraCliente . " | ";

              			$ordenCompraCliente .= $datos_comprobante[0]->CPP_Compracliente;
              		}
              	}

        // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
              	$consulta_guia = $this->ci->comprobante_model->buscar_guiarem_comprobante($codigo);
              	$guiaRemision = "";
              	foreach ($consulta_guia as $key => $value) {
              		$guiaRemision = ($guiaRemision == "") ? $guiaRemision."$value->GUIAREMC_Serie - $value->GUIAREMC_Numero" : $guiaRemision." | $value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
              	}

              	/*FORMA DE PAGO*/
              	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
              	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS


        // DATOS DEL VENDEDOR
        
        $vendedor = ( strlen($datos_comprobante[0]->cajero) > 20 ) ? substr($datos_comprobante[0]->cajero, 0, 20) : $datos_comprobante[0]->cajero;
        /*$vendedor = $datos_comprobante[0]->USUA_Codigo;
        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;*/
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}


        	$detalle_comprobante = $this->ci->comprobantedetalle_model->detalles($codigo);

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $companiaInfo[0]->EMPRP_Codigo );
        	$tipoDocumento = "";
        	switch ($tipo_docu) {
        		case 'F':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "FACTURA DE VENTA<br>ELECTRÓNICA" :  "FACTURA DE COMPRA<br>ELECTRÓNICA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA ELECTRÓNICA DE VENTA" :  "FACTURA ELECTRÓNICA DE COMPRA";
        		$tdocQR = "1";
        		break;
        		case 'B':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "BOLETA DE VENTA<br>ELECTRÓNICA" :  "BOLETA DE COMPRA<br>ELECTRÓNICA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA ELECTRÓNICA DE VENTA" :  "BOLETA ELECTRÓNICA DE COMPRA";
        		$tdocQR = "3";
        		break;
        		case 'N':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
        		$tdocQR = "";
        		break;
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetTitle("COMPROBANTE $serie - $numero");
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1){
                    $this->pdf->setPrintHeader(true);
        	}
        	else
                    $this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	/* Listado de detalles */
        	$gravada = 0;
        	$exonerado = 0;
        	$inafecto = 0;
        	$gratuito = 0;
        	$importeBolsa = 0;


        	$detaProductos = "";
        	foreach ($detalle_comprobante as $indice => $valor) {               
        		$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
        		$nombre_producto = ($valor->CPDEC_Observacion != '') ? $nombre_producto . ". " .$valor->CPDEC_Observacion : $nombre_producto;

        		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
        		$tipo_afectacion = $valor->AFECT_Codigo;

        		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

        		switch ($tipo_afectacion) {
        			case 1: 
        			$gravada += $valor->CPDEC_Subtotal;
        			break;
        			case 8: 
        			$exonerado += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 9: 
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 16:
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			default:
                        $gratuito = ( $valor->CPDEC_ITEMS == "1" ) ? $gratuito : $gratuito + $valor->CPDEC_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                        break;
                      }

                $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $importeBolsa + $valor->CPDEC_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                $bgcolor = ($indice % 2 == 0) ? "#F1F1F1" : "#FFFFFF";

                $detaProductos = $detaProductos. '
                <tr bgcolor="'.$bgcolor.'">
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CPDEC_Cantidad.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->PROD_CodigoUsuario.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Pu, 2).'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Pu_ConIgv, 2).'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Total, 2).'</td>
                </tr>';
              }

              $gravada -= ($gravada * $descuento100 / 100);
              $exonerado -= ($exonerado * $descuento100 / 100);
              $inafecto -= ($inafecto * $descuento100 / 100);

        $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

        if ($tipo_docu == 'N'){
        	$tp = "COMPROBANTE: ".$serie."-".$this->getOrderNumeroSerie($numero);
        }

        $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.02cm" border="0">
        <tr>
        <td style="width:3.2cm"><b>'.$tp.'</b></td>
        <td colspan="2" style="width:9.8cm; text-indent:-0.1cm;">'.$idCliente.'</td>

        <td style="width:2.6cm; font-weight:bold;">FECHA EMISIÓN:</td>
        <td>'.$fecha.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">RUC:</td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;">MONEDA:</td>
        <td>'.$moneda_nombre.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
        <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;">IGV:</td>
        <td>'.$igv100.'%</td>
        </tr>
        <tr>
        <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
        <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;">FORMA DE PAGO:</td>
        <td>'.$formapago_desc.'</td>
        </tr>
        <tr> 

        <td style="width:0.6cm;"></td>
        <td style="width:2.0cm; font-weight:bold;">VENDEDOR:</td>
        <td>'.$vendedor.'</td>
        </tr>
        </table>';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
        $this->pdf->SetY(83);
        $productoHTML = '
        <table border="0" style="font-size: 8pt; line-height:5mm;">
        <tr style="font-size: 8pt">
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.2cm;">CANT.</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">CÓDIGO</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:8.3cm;">DESCRIPCIÓN</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.4cm;">MARCA</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">IMPORTE</th>
        </tr>
        '.$detaProductos.'
        </table>';
        $this->pdf->writeHTML($productoHTML,true,false,true,'');

        $descuentoHTML = ( $descuento > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Descuento</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($descuento, 2).'</td>
        </tr>' : '';

        $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Exonerado</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($exonerado, 2).'</td>
        </tr>' : '';

        $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Inafecto</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($inafecto, 2).'</td>
        </tr>' : '';

        $gravadaHTML = ( $gravada > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Gravado</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($gravada, 2).'</td>
        </tr>' : '';

        $igvHTML = ( $igv > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">18% IGV</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($igv, 2).'</td>
        </tr>' : '';

        $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Gratuito</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($gratuito, 2).'</td>
        </tr>' : '';

        $importeBolsaHTML = ( $importeBolsa > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Impuesto bolsa</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($importeBolsa, 2).'</td>
        </tr>' : '';

        $totalesHTML = '<table align="right" cellspacing="0.1cm">
        '.$gratuitoHTML.'
        '.$descuentoHTML.'
        '.$exoneradoHTML.'
        '.$inafectoHTML.'
        '.$gravadaHTML.'
        '.$igvHTML.'
        '.$importeBolsaHTML.'
        <tr>
        <td style="width:15cm; font-weight:bold;">Total</td>
        <td style="width:01.0cm; font-weight:bold;">'.$simbolo_moneda.'</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($total, 2).'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($totalesHTML,true,false,true,'');

        $posY = $this->pdf->GetY();
        $this->pdf->RoundedRect(8, $posY, 192, 4, 1.50, '1111', '');

        $totalLetrasHTML = '<table cellspacing="1px" border="0">
        <tr>
        <td style="width:18.4cm; text-align:justification;" ><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($totalLetrasHTML,true,false,true,false);

        $guiaRem = ($guiaRemision != '') ? '<tr>
        <td style="text-align:right; width:4cm;"><b>GUIA DE REMISIÓN:</b></td>
        <td style="width:11.6cm;">'.$guiaRemision.'</td>
        </tr>' : '';

        $serieOCHTML = ($serieOC != "") ? '<tr>
        <td style="text-align:right; width:4cm;"><b>COTIZACIÓN:</b></td>
        <td style="width:11.6cm;">'.$serieOC.'</td>
        </tr>' : '';

        $ordenCompraClienteHTML = ($ordenCompraCliente != "") ? '<tr>
        <td style="text-align:right; width:4cm;"><b>ORDEN DE COMPRA:</b></td>
        <td style="width:11.6cm;">'.$ordenCompraCliente.'</td>
        </tr>' : '';
        $observacionHTML = ($observacion != "") ? '<tr>
        <td style="text-align:right; width:4cm;"><b>OBSERVACIÓN:</b></td>
        <td style="width:11.6cm;">'.$observacion.'</td>
        </tr>' : '';

        $adicionalHTML = '<table cellspacing="0.1cm" border="0">
        '.$ordenCompraClienteHTML.'
        '.$serieOCHTML.'
        '.$guiaRem.'
        '.$observacionHTML.'
        </table>
        ';
        
        $this->pdf->writeHTML($adicionalHTML,false,false,true,false);

        if ($tipo_docu != 'N'){
        	$posY = $this->pdf->GetY();
        	$posX = 170;
        	$this->pdf->RoundedRect(8, $posY, 165, 25, 1.50, '1111', '');
            #$this->pdf->SetY(83);

        	$infoBancos = $this->ci->banco_model->ctas_bancarias($empresaInfo[0]->EMPRP_Codigo);
        	$infoBancosHTML = "";

        	if ($infoBancos != NULL){
        		$infoBancosHTML .= '<tr>
        		<td style="width:16cm; font-weight: bold;">CUENTAS BANCARIAS</td>
        		</tr>
        		<tr>
        		<td style="width:1.3cm; font-weight: bold;">TITULAR</td>
        		<td style="width:14cm; font-weight: normal;">'.$infoBancos[0]->CUENT_Titular.'</td>
        		</tr>';
        		foreach ($infoBancos as $indice => $val) {
        			$tipo_cuenta = ($val->CUENT_TipoCuenta == 1 ) ? "AHORROS" : "CORRIENTE";
        			$infoBancosHTML .= '<tr>
        			<td style="width:1.2cm; font-size: 7pt; font-weight:bold;">BANCO:</td>
        			<td style="width:1.3cm; font-size: 7pt; font-weight:normal;">'.$val->BANC_Siglas.'</td>
        			<td style="width:1.0cm; font-size: 7pt; font-weight:bold;">TIPO:</td>
        			<td style="width:1.7cm; font-size: 7pt; font-weight:normal;">'.$tipo_cuenta.'</td>
        			<td style="width:1.5cm; font-size: 7pt; font-weight:bold;">CUENTA:</td>
        			<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_NumeroEmpresa.'</td>
        			<td style="width:2.2cm; font-size: 7pt; font-weight:bold;">INTERBANCARIA:</td>
        			<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_Interbancaria.'</td>
        			</tr>
        			';
                    /*
                    $infoBancosHTML .= '<tr>
                                            <td style="width:1.5cm; font-weight:bold;">BANCO:</td>
                                            <td style="width:1.5cm; font-weight:normal;">'.$val->BANC_Siglas.'</td>
                                            <td style="width:2.0cm; font-weight:bold;">N° CUENTA:</td>
                                            <td style="width:11.5cm; font-weight:normal;">'.$val->CUENT_NumeroEmpresa.'</td>
                                        </tr>
                                    ';
                    */
                                  }
                                }

                                $footerHTML = '<table cellspacing="0.05cm" border="0">
                                '.$infoBancosHTML.'
                                <tr>
                                <td style="width:16cm;">REPRESENTACIÓN IMPRESA DE '.$tipoDocumentoF.' '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
                                </tr>
                                </table>
                                ';
            // CODIGO QR INTERNO GENERADO POR EL SISTEMA

                                $style = array(
                                	'border' => 1,
                                	'position' => 'R',
                                	'vpadding' => 'auto',
                                	'hpadding' => 'auto',
                                	'fgcolor' => array(80,80,80),
                'bgcolor' => false, # array(180,180,180),
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
              );

                                if ( strlen($ruc) == 11 )
                                	$truc = "6";
                                else 
                                	if ( strlen($ruc) == 8 )
                                		$truc = "1"; 
                                	else
                                		$truc = "-";

                                	$cadenaQR = $empresaInfo[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
                                	$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 25, 25, $style, "");

                                	$this->pdf->writeHTML($footerHTML,false,false,true,false);
                                }

                                if ($estado == 0){
                                	$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);
                                }

                                if ($enviarcorreo == false)
                                	$this->pdf->Output("comprobante.pdf", 'I');
                                else
                                	return $this->pdf->Output("comprobante.pdf", 'S');
                              }

                              public function carta_de_garantia($codigo, $flagPdf = 1, $enviarcorreo = false){   
                              	$datos_comprobante = $this->ci->comprobante_model->obtener_comprobante($codigo);

        // DATOS DEL COMPROBANTE
                              	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
                              	$presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                              	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
                              	$serie = $datos_comprobante[0]->CPC_Serie;
                              	$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
                              	$observacion = $datos_comprobante[0]->CPC_Observacion;
                              	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                              	$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);
                              	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                              	$estado = $datos_comprobante[0]->CPC_FlagEstado;

                              	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                              	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                              	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        // DATOS DEL PROYECTO
                              	$nombre_proyecto = "";
                              	$direccion_proyecto = "";

                              	$proyecto = $this->ci->comprobante_model->buscar_proyecto($datos_comprobante[0]->PROYP_Codigo);
                              	if ( count($proyecto) > 0 ) {
                              		$nombre_proyecto = $proyecto[0]->PROYC_Nombre;
                              		$direccionProyecto = $this->ci->comprobante_model->buscar_direccion_proyecto($datos_comprobante[0]->PROYP_Codigo);
                              		if( count($direccionProyecto) > 0 )
                              			$direccion_proyecto = $direccionProyecto[0]->DIRECC_Descrip;
                              		else
                              			$direccion_proyecto = "";
                              	}

                              	/*CONSULTO LA COTIZACION Y LA ORDEN DE COMPRA*/
                              	$serieOC = ($datos_comprobante[0]->OCOMC_Serie != NULL) ? $datos_comprobante[0]->OCOMC_Serie."-".$this->getOrderNumeroSerie($datos_comprobante[0]->OCOMC_Numero) : "";

                              	$ordenCompraCliente = "";
                              	if ( $datos_comprobante[0]->ordenCompra != NULL )
                              		$ordenCompraCliente = $datos_comprobante[0]->ordenCompra;

                              	if ( $datos_comprobante[0]->CPP_Compracliente != NULL ){
                              		if ($ordenCompraCliente != $datos_comprobante[0]->CPP_Compracliente){
                              			$ordenCompraCliente = ($ordenCompraCliente != "") ? $ordenCompraCliente." | ".$datos_comprobante[0]->CPP_Compracliente : $datos_comprobante[0]->CPP_Compracliente;
                              		}
                              	}

        // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
                              	$consulta_guia = $this->ci->comprobante_model->buscar_guiarem_comprobante($codigo);
                              	$guiaRemision = "";
                              	foreach ($consulta_guia as $key => $value) {
                              		$guiaRemision = ($guiaRemision == "") ? $guiaRemision."$value->GUIAREMC_Serie - $value->GUIAREMC_Numero" : $guiaRemision." | $value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
                              	}

                              	/*FORMA DE PAGO*/
                              	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
                              	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$detalle_comprobante = $this->ci->comprobantedetalle_model->detalles($codigo);

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	switch ($tipo_docu) {
        		case 'F':
        		$tipoDocumento = "FACTURA";
        		break;
        		case 'B':
        		$tipoDocumento = "BOLETA";
        		break;
        		case 'N':
        		$tipoDocumento = "COMPROBANTE";
        		break;

        		default:
        		$tipoDocumento = "";
        		break;
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfGarantiaComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(20, 50, 20); 
        	$this->pdf->SetTitle("CARTA DE GARANTIA");
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	/* Listado de detalles */
        	$gravada = 0;
        	$exonerado = 0;
        	$inafecto = 0;
        	$gratuito = 0;
        	$importeBolsa = 0;


        	$detaProductos = "";
        	foreach ($detalle_comprobante as $indice => $valor) {               
        		$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
        		$nombre_producto = ($valor->CPDEC_Observacion != '') ? $nombre_producto . ". " .$valor->CPDEC_Observacion : $nombre_producto;

        		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
        		$tipo_afectacion = $valor->AFECT_Codigo;

        		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

        		switch ($tipo_afectacion) {
        			case 1: 
        			$gravada += $valor->CPDEC_Subtotal;
        			break;
        			case 8: 
        			$exonerado += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 9: 
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 16:
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			default:
                        $gratuito = ( $valor->CPDEC_ITEMS == "1" ) ? $gratuito : $gratuito + $valor->CPDEC_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                        break;
                      }

                $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $importeBolsa + $valor->CPDEC_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                $bgcolor = ($indice % 2 == 0) ? "#F1F1F1" : "#FFFFFF";

                $detaProductos = $detaProductos. '
                <tr bgcolor="'.$bgcolor.'">
                <td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CPDEC_Cantidad.'</td>
                <td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$medidaDetalle.'</td>
                <td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->PROD_CodigoUsuario.'</td>
                <td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
                <td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
                </tr>';
              }

              $gravada -= ($gravada * $descuento100 / 100);
              $exonerado -= ($exonerado * $descuento100 / 100);
              $inafecto -= ($inafecto * $descuento100 / 100);


              $tituloHTML = '<table border="0">
              <tr>
              <td style="width:17cm; font-weight:bold; font-size:14pt; text-align:center;">CARTA DE GARANTIA</td>
              </tr>
              </table>';
              $this->pdf->writeHTML($tituloHTML,true,false,true,'');

              $clienteHTML = '<table cellspacing="2mm" border="0">
              <tr>
              <td style="width:3.0cm"><b>'.$tp.'</b></td>
              <td style="width:8.5cm; ">'.$idCliente.'</td>

              <td style="width:4.5cm; font-weight:bold;">FECHA EMISIÓN: '.$fecha.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">RUC:</td>
              <td colspan="2" style="">'.$ruc.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">DENOMINACIÓN:</td>
              <td colspan="2" style="text-align:justification">'.$nombre_cliente.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">DIRECCIÓN:</td>
              <td colspan="2" style="text-align:justification">'.$direccion.'</td>
              </tr>
              </table>';

              $posY = $this->pdf->getY();
              $this->pdf->writeHTML($clienteHTML,true,false,true,'');
              $posY2 = $this->pdf->getY();

              $size = $posY2 - $posY;
        #$this->pdf->RoundedRect(18, 58, 175, $size, 1.50, '1111', ''); // CLIENTE

              $this->pdf->SetY( $this->pdf->getY() + 5);
              $garantiaHTML = '<table border="0">
              <tr>
              <td style="text-align:justify; line-height:0.8cm; font-size:12pt;">Por medio de la presente <b>'.$empresaInfo[0]->EMPRC_RazonSocial.'</b> con <b>R.U.C. '.$empresaInfo[0]->EMPRC_Ruc.'</b>, garantiza que los productos que fabricamos y comercializamos han sido elaborados; Bajo estrictos controles de calidad los cuales tienen 2 años de garantía. Por tanto, cualquiera de ellas será reemplazado en caso de presentar algún defecto de fabricación siempre y cuando se siga un correcto manipuleo, almacenaje e instalación.</td>
              </tr>
              </table>';
              $this->pdf->writeHTML($garantiaHTML,true,false,true,'');

              $referenciasHTML = '<table border="0" cellspacing="1mm">
              <tr>
              <td style="width:3.0cm; font-weight:bold;">ORDEN DE COMPRA:</td>
              <td style="width:14.0cm;">'.$ordenCompraCliente.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">COTIZACIÓN:</td>
              <td style="width:14.0cm;">'.$serieOC.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">'.$tipoDocumento.':</td>
              <td style="width:14.0cm;">'.$serie.'-'.$numero.'</td>
              </tr>
              <tr>
              <td style="width:3.0cm; font-weight:bold;">GUIA DE REMISION:</td>
              <td style="width:14.0cm;">'.$guiaRemision.'</td>
              </tr>
              <tr>
              <td style="width:4.5cm; font-weight:bold;">NOMBRE DEL PROYECTO:</td>
              <td style="width:12.5cm;">'.$nombre_proyecto.'</td>
              </tr>
              <tr>
              <td style="width:4.5cm; font-weight:bold;">DIRECCION DEL PROYECTO:</td>
              <td style="width:12.5cm;">'.$direccion_proyecto.'</td>
              </tr>
              </table><br><br>&nbsp;';
              $this->pdf->writeHTML($referenciasHTML,true,false,true,'');

        #$this->pdf->SetY(83);
              $productoHTML = '<table border="0" style="font-size: 7pt; line-height:5mm;">
              <tr style="font-size: 7pt">
              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">CANT.</th>
              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.2cm;">UM</th>
              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.4cm;">CÓDIGO</th>
              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:9.0cm;">DESCRIPCIÓN</th>
              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:3.0cm;">MARCA</th>
              </tr>
              '.$detaProductos.'
              </table>';
              $this->pdf->writeHTML($productoHTML,true,false,true,'');

              if ($estado == 0){
              	$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);
              }

              if ($enviarcorreo == false)
              	$this->pdf->Output("garantia.pdf", 'I');
              else
              	return $this->pdf->Output("garantia.pdf", 'S');
            }

            public function comprobante_pdf_a4_rango($inicio = 1, $fin = 20, $oper = "V", $docu = "V", $flagPdf = 1){   
            	$medidas = "a4"; 

            	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
            	$this->pdf->SetMargins(10, 55, 10); 
            	$this->pdf->SetTitle("COMPROBANTES");
            	$this->pdf->SetFont('freesans', '', 8);

            	if ($flagPdf == 1){
            		if ($docu == 'N'){
            			$this->pdf->setPrintHeader(false);
            		}
            		else{
            			$this->pdf->setPrintHeader(true);
            		}
            	}
            	else
            		$this->pdf->setPrintHeader(false);

            	$listaGeneral = $this->ci->comprobante_model->obtener_comprobante_rango($inicio, $fin, $oper, $docu);

            	foreach ($listaGeneral as $keyGeneral => $valGeneral) {
            		$codigo = $valGeneral->CPP_Codigo;

            		$datos_comprobante = $this->ci->comprobante_model->obtener_comprobante($codigo);

            // DATOS DEL COMPROBANTE
            		$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
            		$presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            		$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
            		$serie = $datos_comprobante[0]->CPC_Serie;
            		$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
            		$descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
            		$descuento100 = $datos_comprobante[0]->CPC_descuento100;
            		$descuento = $datos_comprobante[0]->CPC_descuento;
            		$igv = $datos_comprobante[0]->CPC_igv;
            		$igv100 = $datos_comprobante[0]->CPC_igv100;
            		$subtotal = $datos_comprobante[0]->CPC_subtotal;
            		$subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
            		$total = $datos_comprobante[0]->CPC_total;
            		$observacion = $datos_comprobante[0]->CPC_Observacion;
            		$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
            		$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
            		$estado = $datos_comprobante[0]->CPC_FlagEstado;

            		$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
            		$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
            		$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

            		/*CONSULTO LA COTIZACION Y LA ORDEN DE COMPRA*/
            		$serieOC = ($datos_comprobante[0]->OCOMC_Serie != NULL) ? $datos_comprobante[0]->OCOMC_Serie."-".$this->getOrderNumeroSerie($datos_comprobante[0]->OCOMC_Numero) : "";

            		$ordenCompraCliente = "";
            		if ( $datos_comprobante[0]->ordenCompra != NULL )
            			$ordenCompraCliente = $datos_comprobante[0]->ordenCompra;

            		if ( $datos_comprobante[0]->CPP_Compracliente != NULL ){
            			if ($ordenCompraCliente != "" && $ordenCompraCliente != $datos_comprobante[0]->CPP_Compracliente){
            				$ordenCompraCliente = $ordenCompraCliente . " | ";

            				$ordenCompraCliente .= $datos_comprobante[0]->CPP_Compracliente;
            			}
            		}

            // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
            		$consulta_guia = $this->ci->comprobante_model->buscar_guiarem_comprobante($codigo);
            		$guiaRemision = "";
            		foreach ($consulta_guia as $key => $value) {
            			$guiaRemision = ($guiaRemision == "") ? $guiaRemision."$value->GUIAREMC_Serie - $value->GUIAREMC_Numero" : $guiaRemision." | $value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
            		}

            		/*FORMA DE PAGO*/
            		$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
            		$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
            $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS


            // DATOS DEL USUARIO
            $vendedor = ( strlen($datos_comprobante[0]->vendedor) > 20 ) ? substr($datos_comprobante[0]->vendedor, 0, 20) : $datos_comprobante[0]->vendedor;
            /*$vendedor = $datos_comprobante[0]->USUA_Codigo;
            $temp = $this->usuario_model->obtener($vendedor);
            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
            $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;*/
            
            // DATOS DEL CLIENTE
            $cliente = $datos_comprobante[0]->CLIP_Codigo;
            $proveedor = $datos_comprobante[0]->PROVP_Codigo;

            $idCliente = "";
            if ($cliente != '' && $cliente != '0') {
            	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
            	if ($datos_cliente) {
            		$idCliente = $datos_cliente->idCliente;
            		$nombre_cliente = $datos_cliente->nombre;
            		$ruc_cliente = $datos_cliente->ruc;
            		$dni_cliente = $datos_cliente->dni;
            		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
            		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
            		$email   = $datos_cliente->correo;
            	}
            	$tp = "CLIENTE";
            }
            else
            	if ($proveedor != '' && $proveedor != '0') {
            		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
            		if ($datos_proveedor) {
            			$nombre_cliente = $datos_proveedor->nombre;
            			$ruc = $datos_proveedor->ruc;
            			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
            		}
            		$tp = "PROVEEDOR";
            	}

            	$detalle_comprobante = $this->ci->comprobantedetalle_model->detalles($codigo);

            	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
            	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
            	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

            	$tipoDocumento = "";
            	switch ($tipo_docu) {
            		case 'F':
            		$tipoDocumento = ($tipo_oper == 'V') ?  "FACTURA DE VENTA<br>ELECTRÓNICA" :  "FACTURA DE COMPRA<br>ELECTRÓNICA";
            		$tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA ELECTRÓNICA DE VENTA" :  "FACTURA ELECTRÓNICA DE COMPRA";
            		$tdocQR = "1";
            		break;
            		case 'B':
            		$tipoDocumento = ($tipo_oper == 'V') ?  "BOLETA DE VENTA<br>ELECTRÓNICA" :  "BOLETA DE COMPRA<br>ELECTRÓNICA";
            		$tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA ELECTRÓNICA DE VENTA" :  "BOLETA ELECTRÓNICA DE COMPRA";
            		$tdocQR = "3";
            		break;
            		case 'N':
            		$tipoDocumento = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
            		$tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
            		$tdocQR = "";
            		break;
            	}

            	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );
            	$this->pdf->AddPage();
            	$this->pdf->SetAutoPageBreak(true, 1);

            	/* Listado de detalles */
            	$gravada = 0;
            	$exonerado = 0;
            	$inafecto = 0;
            	$gratuito = 0;
            	$importeBolsa = 0;


            	$detaProductos = "";
            	foreach ($detalle_comprobante as $indice => $valor) {               
            		$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
            		$nombre_producto = ($valor->CPDEC_Observacion != '') ? $nombre_producto . ". " .$valor->CPDEC_Observacion : $nombre_producto;

            		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
            		$tipo_afectacion = $valor->AFECT_Codigo;

            		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

            		switch ($tipo_afectacion) {
            			case 1: 
            			$gravada += $valor->CPDEC_Subtotal;
            			break;
            			case 8: 
            			$exonerado += $valor->CPDEC_Subtotal;
            			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            			break;
            			case 9: 
            			$inafecto += $valor->CPDEC_Subtotal;
            			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            			break;
            			case 16:
            			$inafecto += $valor->CPDEC_Subtotal;
            			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
            			break;
            			default:
                                $gratuito = ( $valor->CPDEC_ITEMS == "1" ) ? $gratuito : $gratuito + $valor->CPDEC_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                                $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                                break;
                              }

                    $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $importeBolsa + $valor->CPDEC_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                    $bgcolor = ($indice % 2 == 0) ? "#F1F1F1" : "#FFFFFF";

                    $detaProductos = $detaProductos. '
                    <tr bgcolor="'.$bgcolor.'">
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CPDEC_Cantidad.'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->PROD_CodigoUsuario.'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Pu, 2).'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Pu_ConIgv, 2).'</td>
                    <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CPDEC_Total, 2).'</td>
                    </tr>';
                  }

                  $gravada -= ($gravada * $descuento100 / 100);
                  $exonerado -= ($exonerado * $descuento100 / 100);
                  $inafecto -= ($inafecto * $descuento100 / 100);

            $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
            $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

            if ($tipo_docu == 'N'){
            	$tp = "COMPROBANTE: ".$serie."-".$this->getOrderNumeroSerie($numero);
            }

            $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.02cm" border="0">
            <tr>
            <td style="width:3.2cm"><b>'.$tp.'</b></td>
            <td colspan="2" style="width:9.8cm; text-indent:-0.1cm;">'.$idCliente.'</td>

            <td style="width:2.6cm; font-weight:bold;">FECHA EMISIÓN:</td>
            <td>'.$fecha.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">RUC:</td>
            <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:2.6cm; font-weight:bold;">MONEDA:</td>
            <td>'.$moneda_nombre.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
            <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:2.6cm; font-weight:bold;">IGV:</td>
            <td>'.$igv100.'%</td>
            </tr>
            <tr>
            <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
            <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:2.6cm; font-weight:bold;">FORMA DE PAGO:</td>
            <td>'.$formapago_desc.'</td>
            </tr>
            <tr> 

            <td style="width:0.6cm;"></td>
            <td style="width:2.0cm; font-weight:bold;">VENDEDOR:</td>
            <td>'.$vendedor.'</td>
            </tr>
            </table>';

            $this->pdf->writeHTML($clienteHTML,true,false,true,'');

            #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
            $this->pdf->SetY(83);
            $productoHTML = '
            <table border="0" style="font-size: 8pt; line-height:5mm;">
            <tr style="font-size: 8pt">
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.2cm;">CANT.</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">CÓDIGO</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:8.3cm;">DESCRIPCIÓN</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.4cm;">MARCA</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
            <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">IMPORTE</th>
            </tr>
            '.$detaProductos.'
            </table>';
            $this->pdf->writeHTML($productoHTML,true,false,true,'');

            $descuentoHTML = ( $descuento > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Descuento</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($descuento, 2).'</td>
            </tr>' : '';

            $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Exonerado</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($exonerado, 2).'</td>
            </tr>' : '';

            $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Inafecto</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($inafecto, 2).'</td>
            </tr>' : '';

            $gravadaHTML = ( $gravada > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Gravado</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($gravada, 2).'</td>
            </tr>' : '';

            $igvHTML = ( $igv > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">18% IGV</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($igv, 2).'</td>
            </tr>' : '';

            $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Gratuito</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($gratuito, 2).'</td>
            </tr>' : '';

            $importeBolsaHTML = ( $importeBolsa > 0 ) ? '<tr>
            <td style="width:15cm; font-weight:bold;">Impuesto bolsa</td>
            <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($importeBolsa, 2).'</td>
            </tr>' : '';

            $totalesHTML = '<table align="right" cellspacing="0.1cm">
            '.$gratuitoHTML.'
            '.$descuentoHTML.'
            '.$exoneradoHTML.'
            '.$inafectoHTML.'
            '.$gravadaHTML.'
            '.$igvHTML.'
            '.$importeBolsaHTML.'
            <tr>
            <td style="width:15cm; font-weight:bold;">Total</td>
            <td style="width:01.0cm; font-weight:bold;">'.$simbolo_moneda.'</td>
            <td style="width:02.0cm; font-weight:bold;">'.number_format($total, 2).'</td>
            </tr>
            </table>';
            $this->pdf->writeHTML($totalesHTML,true,false,true,'');

            $posY = $this->pdf->GetY();
            $this->pdf->RoundedRect(8, $posY, 192, 4, 1.50, '1111', '');

            $totalLetrasHTML = '<table cellspacing="1px" border="0">
            <tr>
            <td style="width:18.4cm; text-align:justification;" ><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
            </tr>
            </table>';
            $this->pdf->writeHTML($totalLetrasHTML,true,false,true,false);

            $guiaRem = ($guiaRemision != '') ? '<tr>
            <td style="text-align:right; width:4cm;"><b>GUIA DE REMISIÓN:</b></td>
            <td style="width:11.6cm;">'.$guiaRemision.'</td>
            </tr>' : '';

            $serieOCHTML = ($serieOC != "") ? '<tr>
            <td style="text-align:right; width:4cm;"><b>COTIZACIÓN:</b></td>
            <td style="width:11.6cm;">'.$serieOC.'</td>
            </tr>' : '';

            $ordenCompraClienteHTML = ($ordenCompraCliente != "") ? '<tr>
            <td style="text-align:right; width:4cm;"><b>ORDEN DE COMPRA:</b></td>
            <td style="width:11.6cm;">'.$ordenCompraCliente.'</td>
            </tr>' : '';
            $observacionHTML = ($observacion != "") ? '<tr>
            <td style="text-align:right; width:4cm;"><b>OBSERVACIÓN:</b></td>
            <td style="width:11.6cm;">'.$observacion.'</td>
            </tr>' : '';

            $adicionalHTML = '<table cellspacing="0.1cm" border="0">
            '.$ordenCompraClienteHTML.'
            '.$serieOCHTML.'
            '.$guiaRem.'
            '.$observacionHTML.'
            </table>
            ';
            
            $this->pdf->writeHTML($adicionalHTML,false,false,true,false);

            if ($tipo_docu != 'N'){
            	$posY = $this->pdf->GetY();
            	$this->pdf->RoundedRect(8, $posY, 165, 25, 1.50, '1111', '');
                #$this->pdf->SetY(83);

            	$infoBancos = $this->ci->banco_model->ctas_bancarias($empresaInfo[0]->EMPRP_Codigo);
            	$infoBancosHTML = "";

            	if ($infoBancos != NULL){
            		$infoBancosHTML .= '<tr>
            		<td style="width:16cm;">SIRVASE GIRAR CHEQUE A NOMBRE DE: '.$empresaInfo[0]->EMPRC_RazonSocial.'. O ABONAR A CTA</td>
            		</tr>
            		<tr>
            		<td style="width:1.3cm; font-weight: bold;">TITULAR</td>
            		<td style="width:14cm; font-weight: normal;">'.$infoBancos[0]->CUENT_Titular.'</td>
            		</tr>';
            		foreach ($infoBancos as $indice => $val) {
            			$tipo_cuenta = ($val->CUENT_TipoCuenta == 1 ) ? "AHORROS" : "CORRIENTE";
            			$infoBancosHTML .= '<tr>
            			<td style="width:1.2cm; font-size: 7pt; font-weight:bold;">BANCO:</td>
            			<td style="width:1.3cm; font-size: 7pt; font-weight:normal;">'.$val->BANC_Siglas.'</td>
            			<td style="width:1.5cm; font-size: 7pt; font-weight:bold;">TIPO:</td>
            			<td style="width:1.4cm; font-size: 7pt; font-weight:normal;">'.$tipo_cuenta.'</td>
            			<td style="width:1.5cm; font-size: 7pt; font-weight:bold;">CUENTA:</td>
            			<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_NumeroEmpresa.'</td>
            			<td style="width:2.2cm; font-size: 7pt; font-weight:bold;">INTERBANCARIA:</td>
            			<td style="width:3.5cm; font-size: 7pt; font-weight:normal;">'.$val->CUENT_Interbancaria.'</td>
            			</tr>
            			';
            		}
            	}

            	$footerHTML = '<table cellspacing="0.05cm" border="0">
            	'.$infoBancosHTML.'
            	<tr>
            	<td style="width:16cm;">REPRESENTACIÓN IMPRESA DE '.$tipoDocumentoF.' '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
            	</tr>
            	</table>
            	';
                // CODIGO QR INTERNO GENERADO POR EL SISTEMA

            	$style = array(
            		'border' => 1,
            		'position' => 'R',
            		'vpadding' => 'auto',
            		'hpadding' => 'auto',
            		'fgcolor' => array(80,80,80),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                  );

            	if ( strlen($ruc) == 11 )
            		$truc = "6";
            	else 
            		if ( strlen($ruc) == 8 )
            			$truc = "1"; 
            		else
            			$truc = "-";

            		$cadenaQR = $empresaInfo[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
            		$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 25, 25, $style, "");

            		$this->pdf->writeHTML($footerHTML,true,false,true,false);
            	}

            	if ($estado == 0){
            		$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);
            	}
            }

            $this->pdf->Output('comprobante.pdf', 'I');
          }

  ########################
  ##### CUOTAS
  ########################

          public function comprobante_cuotas($codigo, $flagPdf = 1, $enviarcorreo = false){   
          	$datos_comprobante = $this->ci->cuota_model->lista_cuotas_comprobantes($codigo);

        // DATOS DEL COMPROBANTE
          	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
          	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
          	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
          	$serie = $datos_comprobante[0]->CPC_Serie;
          	$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
          	$total = $datos_comprobante[0]->CPC_total;
          	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
          	$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);

          	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
          	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
          	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

          	/*FORMA DE PAGO*/
          	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
          	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS
        $estado = $datos_comprobante[0]->CPC_FlagEstado;
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = "";
        	switch ($tipo_docu) {
        		case 'F':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>FACTURA DE VENTA" :  "DETALLE DE CUOTAS<br>FACTURA DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTAS EN FACTURA ELECTRÓNICA DE VENTA" :  "CUOTAS EN FACTURA ELECTRÓNICA DE COMPRA";
        		$tdocQR = "1";
        		break;
        		case 'B':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>BOLETA DE VENTA" :  "DETALLE DE CUOTAS<br>BOLETA DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTAS EN BOLETA ELECTRÓNICA DE VENTA" :  "CUOTAS EN BOLETA ELECTRÓNICA DE COMPRA";
        		$tdocQR = "3";
        		break;
        		case 'N':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>COMPROBANTE DE SALIDA" :  "DETALLES DE CUOTAS EN COMPROBANTE DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTAS EN COMPROBANTE DE SALIDA" :  "CUOTAS EN COMPROBANTE DE COMPRA";
        		$tdocQR = "";
        		break;
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetTitle("CUOTAS DEL DOCUMENTO $serie - $numero");
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1){
        		if ($tipo_docu == 'N'){
        			$this->pdf->setPrintHeader(false);
        			$empresaInfo[0]->EMPRC_Ruc = NULL;
        		}
        		else
        			$this->pdf->setPrintHeader(true);
        	}
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

        if ($tipo_docu == 'N'){
        	$tp = "COMPROBANTE: ".$serie."-".$this->getOrderNumeroSerie($numero);
        }

        $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.5mm" border="0">
        <tr>
        <td style="width:3.2cm"><b>'.$tp.'</b></td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$idCliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">DOCUMENTO:</td>
        <td style="width:2.5cm;">'.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">RUC:</td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">FECHA EMISIÓN:</td>
        <td style="width:2.5cm;">'.$fecha.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
        <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">FECHA VENCIMIENTO:</td>
        <td style="width:2.5cm;">'.$fecha_vencimiento.'</td>
        </tr>
        <tr>
        <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
        <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">MONEDA:</td>
        <td style="width:2.5cm;">'.$moneda_nombre.'</td>
        </tr>
        <tr>
        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">IMPORTE:</td>
        <td style="width:2.5cm;">'.number_format($total,2).'</td>
        <td colspan="3"></td>
        </tr>
        </table>';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
        $this->pdf->SetY(83);

        $cuotas_detalles = "";

        $totalPendiente = 0;
        $totalPagado = 0;
        $totalCuotas = 0;

        foreach ($datos_comprobante as $i => $val) {
        	$cuotas_detalles .= '<tr>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.0cm;">CUOT - '.$this->getOrderNumeroSerie($val->CUOT_Numero).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.5cm;">'.mysql_to_human($val->CUOT_FechaInicio).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:3.0cm;">'.mysql_to_human($val->CUOT_Fecha).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:right; width:2.0cm;">'.number_format($val->CUOT_Monto,2).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.0cm;">'.( ($val->CUOT_FlagPagado == 1) ? 'PAGADO' : 'PENDIENTE').'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:justification; width:7.5cm;">'.$val->CUOT_Observacion.'</td>
        	</tr>';

        	if ($val->CUOT_FlagPagado == 1)
        		$totalPagado += $val->CUOT_Monto;

        	if ($val->CUOT_FlagPagado == 0)
        		$totalPendiente += $val->CUOT_Monto;

        	$totalCuotas += $val->CUOT_Monto;
        }

        $cuotasHTML = '
        <table border="0" style="font-size: 8pt; line-height:5mm;">
        <thead>
        <tr>
        <th colspan="5" style="font-weight:bold; text-align:center;">DETALLE DE CUOTAS<br></th>
        </tr>
        <tr style="font-size: 8pt">
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">CUOTA</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.5cm;">FECHA INICIO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3.0cm;">FECHA DE PAGO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">IMPORTE</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">ESTADO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:justification; width:7.5cm;">OBSERVACIONES</th>
        </tr>
        </thead>
        <tbody>'.$cuotas_detalles.'</tbody>
        </table>';
        $this->pdf->writeHTML($cuotasHTML,true,false,true,'');

        $totalesHTML = '<table>
        <tr>
        <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>IMPORTE TOTAL:</b> '.number_format($totalCuotas, 2).'</td>
        <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>EN CUOTAS:</b> '.number_format($totalCuotas, 2).'</td>
        <td style="border-bottom:#cccccc 1mm solid; width:4cm;"><b>PENDIENTE:</b> '.number_format($totalPendiente, 2).'</td>
        <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>PAGADO:</b> '.number_format($totalPagado, 2).'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($totalesHTML,true,false,true,'');
        
        if ($estado == 0)
        	$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);

        if ($enviarcorreo == false)
        	$this->pdf->Output("cuota.pdf", 'I');
        else
        	return $this->pdf->Output("cuota.pdf", 'S');
      }

      public function cuota_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){   
      	$datos_comprobante = $this->ci->cuota_model->getCuota($codigo);

        // DATOS DEL COMPROBANTE
      	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
      	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
      	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
      	$serie = $datos_comprobante[0]->CPC_Serie;
      	$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
      	$total = $datos_comprobante[0]->CPC_total;
      	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
      	$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);

      	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
      	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
      	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

      	/*FORMA DE PAGO*/
      	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
      	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS
        $estado = $datos_comprobante[0]->CPC_FlagEstado;
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = "";
        	switch ($tipo_docu) {
        		case 'F':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		break;
        		case 'B':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		break;
        		case 'N':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "CUOTA DE VENTA" : "CUOTA DE COMPRA";
        		break;
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetTitle("DOCUMENTO $serie - $numero. CUOTA ".$this->getOrderNumeroSerie($datos_comprobante[0]->CUOT_Numero));
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1){
        		if ($tipo_docu == 'N'){
        			$this->pdf->setPrintHeader(false);
        			$empresaInfo[0]->EMPRC_Ruc = NULL;
        		}
        		else
        			$this->pdf->setPrintHeader(true);
        	}
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, "CUOT", $this->getOrderNumeroSerie($datos_comprobante[0]->CUOT_Numero) );

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

        if ($tipo_docu == 'N'){
        	$tp = "COMPROBANTE: ".$serie."-".$this->getOrderNumeroSerie($numero);
        }

        $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.5mm" border="0">
        <tr>
        <td style="width:3.2cm"><b>'.$tp.'</b></td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$idCliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">DOCUMENTO:</td>
        <td style="width:2.5cm;">'.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">RUC:</td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">FECHA EMISIÓN:</td>
        <td style="width:2.5cm;">'.$fecha.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
        <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">FECHA VENCIMIENTO:</td>
        <td style="width:2.5cm;">'.$fecha_vencimiento.'</td>
        </tr>
        <tr>
        <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
        <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">MONEDA:</td>
        <td style="width:2.5cm;">'.$moneda_nombre.'</td>
        </tr>
        <tr>
        <td style="width:0.6cm;"></td>
        <td style="width:3.5cm; font-weight:bold;">IMPORTE:</td>
        <td style="width:2.5cm;">'.number_format($total,2).'</td>
        <td colspan="3"></td>
        </tr>
        </table>';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
        $this->pdf->SetY(83);

        $cuotas_detalles = "";

        $totalPendiente = 0;
        $totalPagado = 0;
        $totalCuotas = 0;

        foreach ($datos_comprobante as $i => $val) {
        	$cuotas_detalles .= '<tr>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:5.0cm;">'.mysql_to_human($val->CUOT_FechaInicio).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:5.0cm;">'.mysql_to_human($val->CUOT_Fecha).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:5.0cm;">'.number_format($val->CUOT_Monto,2).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:4.0cm;">'.( ($val->CUOT_FlagPagado == 1) ? 'PAGADO' : 'PENDIENTE').'</td>
                                </tr>'; # 9.5

                                if ($val->CUOT_FlagPagado == 1)
                                	$totalPagado += $val->CUOT_Monto;

                                if ($val->CUOT_FlagPagado == 0)
                                	$totalPendiente += $val->CUOT_Monto;

                                $totalCuotas += $val->CUOT_Monto;

                                $ncuota = "CUOT - ".$this->getOrderNumeroSerie($val->CUOT_Numero);
                              }

        #$posY = $this->pdf->GetY();
        #$this->pdf->RoundedRect(8, $posY-2, 192, 8, 1.50, '1111', '');

                              $cuotasHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
                              <thead>
                              <tr><th colspan="5" style="font-weight:bold; text-align:center;">DETALLES DE LA CUOTA '.$ncuota.'<br></th></tr>
                              <tr style="font-size: 8pt">
                              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">FECHA INICIO</th>
                              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">FECHA DE PAGO</th>
                              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">IMPORTE</th>
                              <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:4.0cm;">ESTADO</th>
                              </tr>
                              </thead>
                              <tbody>'.$cuotas_detalles.'</tbody>
                              </table>';
                              $this->pdf->writeHTML($cuotasHTML,true,false,true,'');

                              $totalesHTML = '<table>
                              <tr>
                              <td style="border-bottom:#cccccc 1mm solid; width:19cm;"><b>OBSERVACIÓN:</b> '.$datos_comprobante[0]->CUOT_Observacion.'</td>
                              </tr>
                              </table>';
                              $this->pdf->writeHTML($totalesHTML,true,false,true,'');

                              if ($estado == 0)
                              	$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);

                              if ($enviarcorreo == false)
                              	$this->pdf->Output("cuota.pdf", 'I');
                              else
                              	return $this->pdf->Output("cuota.pdf", 'S');
                            }

                            public function cuotas_cliente($clienteID = NULL, $proveedorID = NULL, $flagPdf = 1, $enviarcorreo = false){

                            	$medidas = "a4"; 
                            	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
                            	$this->pdf->SetMargins(10, 55, 10); 
                            	$this->pdf->SetTitle("CUOTAS");
                            	$this->pdf->SetFont('freesans', '', 8);

                            	$comprobante = $this->ci->cuota_model->getComprobantesCP($clienteID, $proveedorID);

                            	$idCliente = "";
                            	if ($clienteID != '' && $clienteID != '0'){
                            		$datos_cliente = $this->ci->cliente_model->obtener($clienteID);
                            		if ($datos_cliente) {
                            			$idCliente = $datos_cliente->idCliente;
                            			$nombre_cliente = $datos_cliente->nombre;
                            			$ruc_cliente = $datos_cliente->ruc;
                            			$dni_cliente = $datos_cliente->dni;
                            			$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
                            			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
                            			$email   = $datos_cliente->correo;
                            		}
                            		$tp = "CLIENTE";
                            	}
                            	else
                            		if ($proveedorID != '' && $proveedorID != '0') {
                            			$datos_proveedor = $this->ci->proveedor_model->obtener($proveedorID);
                            			if ($datos_proveedor) {
                            				$nombre_cliente = $datos_proveedor->nombre;
                            				$ruc = $datos_proveedor->ruc;
                            				$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
                            			}
                            			$tp = "PROVEEDOR";
                            		}

                            		$resumen = new stdClass();

                            		foreach ($comprobante as $i => $col) {
                            			$codigo = $col->CPP_Codigo;
                            			$datos_comprobante = $this->ci->cuota_model->lista_cuotas_comprobantes($codigo);

            // DATOS DEL COMPROBANTE
                            			$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
                            			$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
                            			$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                            			$serie = $datos_comprobante[0]->CPC_Serie;
                            			$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
                            			$total = $datos_comprobante[0]->CPC_total;
                            			$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                            			$fecha_vencimiento = mysql_to_human($datos_comprobante[0]->CPC_FechaVencimiento);
                            			$estado = $datos_comprobante[0]->CPC_FlagEstado;

                            			$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                            			$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                            			$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

                            			/*FORMA DE PAGO*/
                            			$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
                            			$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
            $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS
            
            // DATOS DEL CLIENTE
            $cliente = $datos_comprobante[0]->CLIP_Codigo;
            $proveedor = $datos_comprobante[0]->PROVP_Codigo;

            $companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
            $establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
            $empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

            $tipoDocumento = "";
            switch ($tipo_docu) {
            	case 'F':
            	$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>FACTURA DE VENTA" :  "DETALLE DE CUOTAS<br>FACTURA DE COMPRA";
            	$tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA DE VENTA" :  "FACTURA DE COMPRA";
            	break;
            	case 'B':
            	$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>BOLETA DE VENTA" :  "DETALLE DE CUOTAS<br>BOLETA DE COMPRA";
            	$tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA DE VENTA" :  "BOLETA DE COMPRA";
            	break;
            	case 'N':
            	$tipoDocumento = ($tipo_oper == 'V') ?  "DETALLE DE CUOTAS<br>COMPROBANTE DE SALIDA" :  "DETALLES DE CUOTAS EN COMPROBANTE DE COMPRA";
            	$tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE DE SALIDA" :  "COMPROBANTE DE COMPRA";
            	break;
            }

            if ($flagPdf == 1){
            	if ($tipo_docu == 'N'){
            		$this->pdf->setPrintHeader(false);
            		$empresaInfo[0]->EMPRC_Ruc = NULL;
            	}
            	else
            		$this->pdf->setPrintHeader(true);
            }
            else
            	$this->pdf->setPrintHeader(false);

            $this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $numero );

            $this->pdf->AddPage();
            
            $this->pdf->SetAutoPageBreak(true, 1);

            $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
            $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

            if ($tipo_docu == 'N'){
            	$tp = "COMPROBANTE: ".$serie."-".$numero;
            }

            $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.5mm" border="0">
            <tr>
            <td style="width:3.2cm"><b>'.$tp.'</b></td>
            <td style="width:9.2cm; text-indent:-0.1cm;">'.$idCliente.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:3.5cm; font-weight:bold;">DOCUMENTO:</td>
            <td style="width:2.5cm;">'.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">RUC:</td>
            <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:3.5cm; font-weight:bold;">FECHA EMISIÓN:</td>
            <td style="width:2.5cm;">'.$fecha.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
            <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:3.5cm; font-weight:bold;">FECHA VENCIMIENTO:</td>
            <td style="width:2.5cm;">'.$fecha_vencimiento.'</td>
            </tr>
            <tr>
            <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
            <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

            <td style="width:0.6cm;"></td>
            <td style="width:3.5cm; font-weight:bold;">MONEDA:</td>
            <td style="width:2.5cm;">'.$moneda_nombre.'</td>
            </tr>
            <tr>
            <td style="width:0.6cm;"></td>
            <td style="width:3.5cm; font-weight:bold;">IMPORTE:</td>
            <td style="width:2.5cm;">'.number_format($total,2).'</td>
            <td colspan="3"></td>
            </tr>
            </table>';

            $this->pdf->writeHTML($clienteHTML,true,false,true,'');

            #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
            $this->pdf->SetY(83);

            $cuotas_detalles = "";

            $totalPendiente = 0;
            $totalPagado = 0;
            $totalCuotas = 0;
            $cantidad_cuotas = 0;

            foreach ($datos_comprobante as $i => $val) {
            	$cuotas_detalles .= '<tr>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.0cm;">CUOT - '.$this->getOrderNumeroSerie($val->CUOT_Numero).'</td>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.5cm;">'.mysql_to_human($val->CUOT_FechaInicio).'</td>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:3.0cm;">'.mysql_to_human($val->CUOT_Fecha).'</td>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:right; width:2.0cm;">'.number_format($val->CUOT_Monto,2).'</td>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width:2.0cm;">'.( ($val->CUOT_FlagPagado == 1) ? 'PAGADO' : 'PENDIENTE').'</td>
            	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:justification; width:7.5cm;">'.$val->CUOT_Observacion.'</td>
            	</tr>';

            	if ($val->CUOT_FlagPagado == 1)
            		$totalPagado += $val->CUOT_Monto;

            	if ($val->CUOT_FlagPagado == 0)
            		$totalPendiente += $val->CUOT_Monto;

            	$totalCuotas += $val->CUOT_Monto;
            	$cantidad_cuotas++;

            	if ($i == 0)
            		$fechai_cuotas = $val->CUOT_FechaInicio;

            	$fechaf_cuotas = $val->CUOT_Fecha;
            }

            $cuotasHTML = '
            <table border="0" style="font-size: 8pt; line-height:5mm;">
            <thead>
            <tr>
            <th colspan="5" style="font-weight:bold; text-align:center;">DETALLE DE CUOTAS<br></th>
            </tr>
            <tr style="font-size: 8pt">
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">CUOTA</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.5cm;">FECHA INICIO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3.0cm;">FECHA DE PAGO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">IMPORTE</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">ESTADO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:justification; width:7.5cm;">OBSERVACIONES</th>
            </tr>
            </thead>
            <tbody>'.$cuotas_detalles.'</tbody>
            </table>';
            $this->pdf->writeHTML($cuotasHTML,true,false,true,'');

            $totalesHTML = '<table>
            <tr>
            <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>IMPORTE TOTAL:</b> '.number_format($total, 2).'</td>
            <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>EN CUOTAS:</b> '.number_format($totalCuotas, 2).'</td>
            <td style="border-bottom:#cccccc 1mm solid; width:4cm;"><b>PENDIENTE:</b> '.number_format($totalPendiente, 2).'</td>
            <td style="border-bottom:#cccccc 1mm solid; width:5cm;"><b>PAGADO:</b> '.number_format($totalPagado, 2).'</td>
            </tr>
            </table>';
            
            $this->pdf->writeHTML($totalesHTML,true,false,true,'');

            if ($estado != 0){
            	$resumen->documento[] = $tipoDocumentoF;
            	$resumen->serie[] = $serie;
            	$resumen->numero[] = $numero;
            	$resumen->importe[] = $total;
            	$resumen->cantidad_cuotas[] = $cantidad_cuotas;
            	$resumen->importe_cuotas[] = $totalCuotas;
            	$resumen->importe_pendiente[] = $totalPendiente;
            	$resumen->importe_pagado[] = $totalPagado;
            	$resumen->fechai_cuotas[] = $fechai_cuotas;
            	$resumen->fechaf_cuotas[] = $fechaf_cuotas;
            }

            if ($estado == 0)
            	$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);
          }


        ########################################
        ###### PAGINA RESUMEN
        ########################################

          $this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, "RESUMEN DE CUOTAS", "FECHA", date("d / m / Y"));
          $this->pdf->AddPage();
          $this->pdf->SetAutoPageBreak(true, 1);

            $this->pdf->RoundedRect(8, 53, 192, 26, 1.50, '1111', ''); // CLIENTE
            #$this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

            $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.5mm" border="0">
            <tr>
            <td style="width:3.2cm"><b>RESUMEN</b></td>
            <td style="width:15.8cm; text-indent:-0.1cm;">'.$idCliente.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">RUC:</td>
            <td style="width:15.8cm; text-indent:-0.1cm;">'.$ruc.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
            <td style="width:15.8cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>
            </tr>
            <tr>
            <td style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
            <td style="width:15.8cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>
            </tr>
            </table>';

            $this->pdf->writeHTML($clienteHTML,true,false,true,'');

            #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
            $this->pdf->SetY(83);

            $cuotas_detalles = "";

            $importeTotal = 0;
            $totalPendiente = 0;
            $totalPagado = 0;
            $totalCuotas = 0;
            $cantidad_cuotas = 0;

            $sizeResumen = count( $resumen->documento );
            for ( $i = 0; $i < $sizeResumen; $i++ ){
            	$cuotas_detalles .= '
            	<tr>
            	<td style="border-bottom:#ccc 1mm solid; text-align:left; width:4.0cm;">'.$resumen->documento[$i].'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:center; width:1.7cm;">'.$resumen->serie[$i].'-'.$resumen->numero[$i].'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($resumen->importe[$i],2).'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;">'.$resumen->cantidad_cuotas[$i].'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:right; width:2.0cm;">'.number_format($resumen->importe_cuotas[$i],2).'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($resumen->importe_pendiente[$i],2).'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($resumen->importe_pagado[$i],2).'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;">'.mysql_to_human($resumen->fechai_cuotas[$i]).'</td>
            	<td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;">'.mysql_to_human($resumen->fechaf_cuotas[$i]).'</td>
            	</tr>';

            	$importeTotal += $resumen->importe[$i];
            	$cantidad_cuotas += $resumen->cantidad_cuotas[$i];
            	$totalCuotas += $resumen->importe_cuotas[$i];
            	$totalPendiente += $resumen->importe_pendiente[$i];
            	$totalPagado += $resumen->importe_pagado[$i];
            }

            $cuotasHTML = '
            <table border="0" style="font-size: 7pt; line-height:5mm;">
            <thead>
            <tr>
            <th colspan="5" style="font-weight:bold; text-align:center;">RESUMEN DE DOCUMENTOS CON CUOTAS<br></th>
            </tr>
            <tr bgcolor="#eeeeee" style="font-size: 7pt">
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:4.0cm;">DOCUMENTO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:1.7cm;">NÚMERO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:1.7cm;">IMPORTE</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">N° CUOTAS</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">IMP. CUOTAS</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:1.7cm;">PENDIENTE</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:1.7cm;">PAGADO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">F. INICIO</th>
            <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">F. FIN</th>
            </tr>
            </thead>
            <tbody>
            '.$cuotas_detalles.'
            <tr bgcolor="#eeeeee">
            <td style="border-bottom:#ccc 1mm solid; text-align:left; width:5.7cm; font-weight:bold; text-align:right" colspan="2">TOTALES:</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($importeTotal,2).'</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;">'.$cantidad_cuotas.'</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:right; width:2.0cm;">'.number_format($totalCuotas,2).'</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($totalPendiente,2).'</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:right; width:1.7cm;">'.number_format($totalPagado,2).'</td>
            <td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;"></td>
            <td style="border-bottom:#ccc 1mm solid; text-align:center; width:2.0cm;"></td>
            </tr>
            </tbody>
            </table>';
            $this->pdf->writeHTML($cuotasHTML,true,false,true,'');
            
            # MUEVE LA ULTIMA PAGINA DE RESUMEN A LA POSICION INICIAL
            $this->pdf->movePage( $this->pdf->getPage(), 1);

            if ($enviarcorreo == false)
            	$this->pdf->Output("cuota.pdf", 'I');
            else
            	return $this->pdf->Output("cuota.pdf", 'S');
          }

  ########################
  ##### CUENTAS
  ########################

          public function cuentas_pdf($data, $flagPdf = 1, $enviarcorreo = false){

          	$cuentas = $this->ci->cuentas_model->getCuentas($data);

        // DATOS DEL CLIENTE
          	$cliente = $cuentas[0]->CLIP_Codigo;
          	$proveedor = $cuentas[0]->PROVP_Codigo;

          	$idCliente = "";
          	if ($cliente != '' && $cliente != '0') {
          		$datos_cliente = $this->ci->cliente_model->obtener($cliente);
          		if ($datos_cliente) {
          			$idCliente = $datos_cliente->idCliente;
          			$nombre_cliente = $datos_cliente->nombre;
          			$ruc_cliente = $datos_cliente->ruc;
          			$dni_cliente = $datos_cliente->dni;
          			$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
          			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
          			$email   = $datos_cliente->correo;
          		}
          		$tp = "CLIENTE";
          	}
          	else
          		if ($proveedor != '' && $proveedor != '0') {
          			$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
          			if ($datos_proveedor) {
          				$nombre_cliente = $datos_proveedor->nombre;
          				$ruc = $datos_proveedor->ruc;
          				$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
          			}
          			$tp = "PROVEEDOR";
          		}

          		$companiaInfo = $this->ci->compania_model->obtener($cuentas[0]->COMPP_Codigo);
          		$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
          		$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

          		$tipoDocumento = ($data->tipo_cuenta == 1) ?  "CUENTAS POR<br>COBRAR" : "CUENTAS POR<br>PAGAR";
          		$tipoDocumentoT = ($data->tipo_cuenta == 1) ?  "CUENTAS POR COBRAR" : "CUENTAS POR PAGAR";

          		$medidas = "a4"; 
          		$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
          		$this->pdf->SetMargins(10, 55, 10); 
          		$this->pdf->SetTitle($tipoDocumentoT);
          		$this->pdf->SetFont('freesans', '', 8);
          		if ($flagPdf == 1)
          			$this->pdf->setPrintHeader(true);
          		else
          			$this->pdf->setPrintHeader(false);

          		$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, "F. EMISIÓN ".date("Y-m-d"), NULL);

          		$this->pdf->AddPage();
          		$this->pdf->SetAutoPageBreak(true, 15);

          		$clienteHTML = '<table border="0">
          		<tr>
          		<td style="width:3.2cm"><b>'.$tp.'</b></td>
          		<td style="width:15.6cm; text-indent:-0.1cm;">'.$idCliente.'</td>
          		</tr>
          		<tr>
          		<td style="width:3.2cm; font-weight:bold;">RUC:</td>
          		<td style="width:15.6cm; text-indent:-0.1cm;">'.$ruc.'</td>
          		</tr>
          		<tr>
          		<td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
          		<td style="width:15.6cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>
          		</tr>
          		<tr>
          		<td style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
          		<td style="width:15.6cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>
          		</tr>
          		</table>';

          		$posY1 = $this->pdf->getY();
          		$this->pdf->writeHTML($clienteHTML,true,false,true,'');
          		$posY2 = $this->pdf->getY();

        $this->pdf->RoundedRect(8, 53, 192, ($posY2-$posY1+3), 1.50, '1111', ''); // CLIENTE
        $this->pdf->setY( $posY2 + 5 );

        $style = new stdClass();
        $style->border_top = "border-top:#cccccc 1mm solid;";
        $style->border_bottom = "border-bottom:#cccccc 1mm solid;";
        $style->border_tb = "border-top:#cccccc 1mm solid; border-bottom:#cccccc 1mm solid;";

        $cuentasDetaHTML = "";
        $totalCuenta = 0;
        $totalAvance = 0;
        $totalSaldo = 0;

        foreach ($cuentas as $i => $val){
        	$codigo = $val->CUE_CodDocumento;
        	$tipo_documento = $val->tipo_documento;
        	$fecha = mysql_to_human($val->CUE_FechaOper);
        	$serie = $val->CPC_Serie;
        	$numero = $val->CPC_Numero;
        	$ruc = $val->rucDni;
        	$nombre = $val->nombre;

        	$total = $val->CUE_Monto;
        	$listado_pagos = $this->ci->cuentaspago_model->listar($val->CUE_Codigo);
        	$avance =  $this->ci->pago_model->total_pagos($listado_pagos);

        	$saldo = $val->CUE_Monto - $avance;
        	$estado_formato = obtener_estado_de_cuenta($saldo, $avance, $val->CPC_Fecha, $val->CPC_FechaVencimiento, false);
        	$avance = $avance;
        	$saldo = $saldo;

        	$tipo_oper = $val->CPC_TipoOperacion;
        	$tipo_docu = $val->CPC_TipoDocumento;
        	$serie = $val->CPC_Serie;
        	$numero = $this->getOrderNumeroSerie($val->CPC_Numero);
        	$fecha = mysql_to_human($val->CPC_Fecha);
        	$fecha_vencimiento = mysql_to_human($val->CPC_FechaVencimiento);

        	$moneda = $val->MONED_Simbolo;

        	$cuentasDetaHTML .= '<tr>
        	<td style="'.$style->border_tb.' text-align:center; width:3.0cm;">'.$fecha.'</td>
        	<td style="'.$style->border_tb.' text-align:center; width:3.0cm;">'.$fecha_vencimiento.'</td>
        	<td style="'.$style->border_tb.' text-align:left; width:3.0cm;">'.$tipo_documento.'</td>
        	<td style="'.$style->border_tb.' text-align:center; width:2.0cm;">'.$serie.'</td>
        	<td style="'.$style->border_tb.' text-align:center; width:2.0cm;">'.$numero.'</td>
        	<td style="'.$style->border_tb.' text-align:right; width:0.9cm;">'.$moneda.'</td>
        	<td style="'.$style->border_tb.' text-align:right; width:1.7cm;">'.number_format($total,2).'</td>
        	<td style="'.$style->border_tb.' text-align:right; width:1.7cm;">'.number_format($avance,2).'</td>
        	<td style="'.$style->border_tb.' text-align:right; width:1.7cm;">'.number_format($saldo,2).'</td>
        	</tr>';

        	$totalCuenta += $total;
        	$totalAvance += $avance;
        	$totalSaldo += $saldo;
        }

        $cuentasHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
        <thead>
        <tr><th style="font-weight:bold; text-align:center; width: 19.0cm">CUENTAS PENDIENTES</th></tr>
        <tr style="font-size: 8pt">
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:3.0cm;">FECHA INICIO</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:3.0cm;">FECHA DE VENC.</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:3.0cm;">DOCUMENTO</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:2.0cm;">SERIE</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:2.0cm;">NÚMERO</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:0.9cm;"></th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:1.7cm;">IMPORTE</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:1.7cm;">AVANCE</th>
        <th style="'.$style->border_tb.' font-weight:bold; text-align:center; width:1.7cm;">SALDO</th>
        </tr>
        </thead>
        <tbody>
        '.$cuentasDetaHTML.'
        <tr>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:center; width:3.0cm;"></td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:center; width:3.0cm;"></td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:left; width:3.0cm;"></td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:center; width:2.0cm;"></td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:center; width:2.0cm;">TOTAL</td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:right; width:0.9cm;">'.$moneda.'</td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:right; width:1.7cm;">'.number_format($totalCuenta,2).'</td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:right; width:1.7cm;">'.number_format($totalAvance,2).'</td>
        <td style="'.$style->border_tb.' font-weight:bold; text-align:right; width:1.7cm;">'.number_format($totalSaldo,2).'</td>
        </tr>
        </tbody>
        </table>';
        $this->pdf->writeHTML($cuentasHTML,true,false,true,"");

        if ($enviarcorreo == false)
        	$this->pdf->Output("cuentas-$ruc-$nombre_cliente.pdf", 'I');
        else
        	return $this->pdf->Output("cuentas-$ruc-$nombre_cliente.pdf", 'S');
      }

      public function cuenta_pdf($cuenta, $flagPdf = 1, $enviarcorreo = false){

      	$cuentaResult = $this->ci->cuentas_model->getCuenta($cuenta);

      	foreach ($cuentaResult as $i => $val) {
      		$listado_pagos = $this->ci->cuentaspago_model->listar($val->CUE_Codigo);
      		$avance =  $this->ci->pago_model->total_pagos($listado_pagos);

      		$saldo = $val->CUE_Monto - $avance;
      		$total = number_format($val->CUE_Monto,2);
      		$saldo = number_format($saldo,2);

      		$moneda = $val->MONED_Simbolo;
      		$documento = $val->documento;
      		$serie = $val->CPC_Serie;
      		$numero = $this->getOrderNumeroSerie($val->CPC_Numero);
      		$fechaEmision = mysql_to_human($val->CPC_Fecha);
      		$fechaVencimiento = mysql_to_human($val->CPC_FechaVencimiento);
      	}

        // DATOS DEL CLIENTE
      	$cliente = $cuentaResult[0]->CLIP_Codigo;
      	$proveedor = $cuentaResult[0]->PROVP_Codigo;

      	$idCliente = "";
      	if ($cliente != '' && $cliente != '0') {
      		$datos_cliente = $this->ci->cliente_model->obtener($cliente);
      		if ($datos_cliente) {
      			$idCliente = $datos_cliente->idCliente;
      			$nombre_cliente = $datos_cliente->nombre;
      			$ruc_cliente = $datos_cliente->ruc;
      			$dni_cliente = $datos_cliente->dni;
      			$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
      			$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_cliente->direccion;
      			$email   = $datos_cliente->correo;
      		}
      		$tp = "CLIENTE";
      	}
      	else
      		if ($proveedor != '' && $proveedor != '0') {
      			$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
      			if ($datos_proveedor) {
      				$nombre_cliente = $datos_proveedor->nombre;
      				$ruc = $datos_proveedor->ruc;
      				$direccion = ($datos_comprobante[0]->CPC_Direccion != "") ? $datos_comprobante[0]->CPC_Direccion : $datos_proveedor->direccion;
      			}
      			$tp = "PROVEEDOR";
      		}

      		$companiaInfo = $this->ci->compania_model->obtener($cuentaResult[0]->COMPP_Codigo);
      		$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
      		$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

      		$tipoDocumento = ($cuentaResult[0]->CUE_TipoCuenta == 1) ?  "CUENTAS POR<br>COBRAR" : "CUENTAS POR<br>PAGAR";
      		$tipoDocumentoT = ($cuentaResult[0]->CUE_TipoCuenta == 1) ?  "CUENTAS POR COBRAR" : "CUENTAS POR PAGAR";

      		$medidas = "a4"; 
      		$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
      		$this->pdf->SetMargins(10, 55, 10); 
      		$this->pdf->SetTitle($tipoDocumentoT);
      		$this->pdf->SetFont('freesans', '', 8);
      		if ($flagPdf == 1)
      			$this->pdf->setPrintHeader(true);
      		else
      			$this->pdf->setPrintHeader(false);

      		$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $numero);

      		$this->pdf->AddPage();
      		$this->pdf->SetAutoPageBreak(true, 15);

      		$style = new stdClass();
      		$style->border_top = "border-top:#cccccc 1mm solid;";
      		$style->border_bottom = "border-bottom:#cccccc 1mm solid;";
      		$style->border_tb = "border-top:#cccccc 1mm solid; border-bottom:#cccccc 1mm solid;";

        # BEGIN CLIENTE
      		$clienteHTML = '<table border="0">
      		<tr>
      		<td style="width:3.2cm"><b>'.$tp.'</b></td>
      		<td style="width:15.6cm; text-indent:-0.1cm;">'.$idCliente.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.2cm; font-weight:bold;">RUC:</td>
      		<td style="width:15.6cm; text-indent:-0.1cm;">'.$ruc.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
      		<td style="width:15.6cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>
      		</tr>
      		<tr>
      		<td style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
      		<td style="width:15.6cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>
      		</tr>
      		</table>';

      		$posY1 = $this->pdf->getY();
      		$this->pdf->writeHTML($clienteHTML,true,false,true,'');
      		$posY2 = $this->pdf->getY();

      		$this->pdf->RoundedRect(8, 53, 192, ($posY2-$posY1+3), 1.50, '1111', '');
      		$this->pdf->setY( $posY2 + 5 );
        # END CLIENTE

        # BEGIN CUENTA
      		$cuentaHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      		<tr>
      		<td style="'.$style->border_bottom.'width:2.5cm; font-weight:bold;">DOCUMENTO:</td>
      		<td style="'.$style->border_bottom.'width:2.5cm;">'.$documento.'</td>

      		<td style="'.$style->border_bottom.'width:1.3cm; font-weight:bold;">SERIE:</td>
      		<td style="'.$style->border_bottom.'width:1.0cm; font-size: 7pt;">'.$serie.'</td>

      		<td style="'.$style->border_bottom.'width:1.7cm; font-weight:bold;">NÚMERO:</td>
      		<td style="'.$style->border_bottom.'width:1.5cm; font-size: 7pt;">'.$numero.'</td>

      		<td style="'.$style->border_bottom.'width:2.0cm; font-weight:bold;">FECHA EMI:</td>
      		<td style="'.$style->border_bottom.'width:2.0cm;">'.$fechaEmision.'</td>

      		<td style="'.$style->border_bottom.'width:2.5cm; font-weight:bold;">FECHA VENC:</td>
      		<td style="'.$style->border_bottom.'width:2.0cm;">'.$fechaVencimiento.'</td>
      		</tr>
      		<tr>
      		<td style="'.$style->border_bottom.' width:2.5cm; font-weight:bold;">MONEDA:</td>
      		<td style="'.$style->border_bottom.' width:2.5cm;">'.$moneda.'</td>

      		<td style="'.$style->border_bottom.' width:1.5cm; font-weight:bold;">TOTAL</td>
      		<td style="'.$style->border_bottom.' width:2.0cm;">'.$total.'</td>

      		<td style="'.$style->border_bottom.' width:2.0cm; font-weight:bold;">AVANCE:</td>
      		<td style="'.$style->border_bottom.' width:2.0cm;">'.number_format($avance,2).'</td>

      		<td style="'.$style->border_bottom.' width:2.0cm; font-weight:bold;">SALDO:</td>
      		<td style="'.$style->border_bottom.' width:4.0cm;">'.$saldo.'</td>
      		</tr>
      		</table>';
      		$this->pdf->writeHTML($cuentaHTML,true,false,true,'');
        # END CUENTA

      		$pagos = $this->ci->cuentaspago_model->listar($cuentaResult[0]->CUE_Codigo);
      		if ($pagos != NULL){
      			foreach ($pagos as $indice => $val){
      				$formaPago = ($val->PAGC_FormaPago == 1) ? "EFECTIVO" : "OP. BANCARIA";

      				$noperacion = "";

      				if ($val->PAGC_DepoNro != "")
      					$formaPago = "DEPOSITO";

      				if ($val->PAGC_Trans != "")
      					$formaPago = "TRANSFERENCIA";

      				if ($val->CHEC_Nro != "")
      					$formaPago = "CHEQUE";

      				if ($val->PAGC_NotaCredito != "")
      					$formaPago = "NOTA DE CREDITO";

      				$background = ($indice % 2 == 0) ? 'bgcolor="#F1F1F1"' : '';

      				$obs = ($val->PAGC_Obs != NULL) ? $val->PAGC_Obs : "";

      				$fecha = mysql_to_human($val->PAGC_FechaOper);
      				$serieNumero = $val->PAGP_Serie.' - '. $this->getNumberFormat($val->PAGP_Numero,2);
      				$moneda = $val->MONED_Simbolo;
      				$monto = number_format($val->CPAGC_Monto,2);
      				$tdc = $val->CPAGC_TDC;

      				$cuentasDetaHTML .= '<tr '.$background.' style="font-size: 8pt">
      				<td style="'.$style->border_top.' font-weight:bold; text-align:left; width:1.3cm;">FECHA:</td>
      				<td style="'.$style->border_top.' text-align:center; width:1.8cm;">'.$fecha.'</td>

      				<td style="'.$style->border_top.' font-weight:bold; text-align:center; width:1.2cm;">PAGO:</td>
      				<td style="'.$style->border_top.' text-align:center; width:1.0cm;">'.$serieNumero.'</td>

      				<td style="'.$style->border_top.' font-weight:bold; text-align:center; width:3.0cm;">FORMA DE PAGO:</td>
      				<td style="'.$style->border_top.' text-align:center; width:3.0cm;">'.$formaPago.'</td>

      				<td style="'.$style->border_top.' font-weight:bold; text-align:center; width:1.7cm;">MONEDA:</td>
      				<td style="'.$style->border_top.' text-align:center; width:0.5cm;">'.$moneda.'</td>

      				<td style="'.$style->border_top.' font-weight:bold; text-align:center; width:2.0cm;">IMPORTE</td>
      				<td style="'.$style->border_top.' text-align:center; width:2.0cm;">'.$monto.'</td>

      				<td style="'.$style->border_top.' font-weight:bold; text-align:center; width:1.0cm;">TDC</td>
      				<td style="'.$style->border_top.' text-align:center; width:1.0cm;">'.$tdc.'</td>
      				</tr>';

      				if ($val->PAGC_FormaPago != 1){

      					$ctaBancoCP = $this->ci->bancocta_model->getCtaEmpresa($val->CUENT_CodigoCP);
      					$ctaBancoEmpresa = $this->ci->bancocta_model->getCtaEmpresa($val->CUENT_CodigoEmpresa);

      					if ($ctaBancoCP != NULL){
      						$cuentasDetaHTML .= '<tr '.$background.'>
      						<td style="font-weight:bold; text-align:left; width:3.0cm;">BANCO '.$tp.':</td>
      						<td style="text-align:left; width:3.5cm;">'.$ctaBancoCP[0]->BANC_Nombre.'</td>

      						<td style="font-weight:bold; text-align:center; width:1.5cm;">CUENTA:</td>
      						<td style="text-align:left; width:2.5cm;">'.$ctaBancoCP[0]->CUENT_NumeroEmpresa.'</td>

      						<td style="font-weight:bold; text-align:center; width:2.0cm;">MONEDA:</td>
      						<td style="text-align:left; width:1.0cm;">'.$ctaBancoCP[0]->MONED_Simbolo.'</td>

      						<td style="font-weight:bold; text-align:center; width:2.0cm;">TITULAR:</td>
      						<td style="text-align:left; width:3.5cm;">'.$ctaBancoCP[0]->CUENT_Titular.'</td>
      						</tr>';
      					}

      					if ($ctaBancoEmpresa != NULL){
      						$cuentasDetaHTML .= '<tr '.$background.'>
      						<td style="font-weight:bold; text-align:left; width:3.0cm;">BANCO EMPRESA:</td>
      						<td style="text-align:left; width:3.5cm;">'.$ctaBancoEmpresa[0]->BANC_Nombre.'</td>

      						<td style="font-weight:bold; text-align:center; width:1.5cm;">CUENTA:</td>
      						<td style="text-align:left; width:2.5cm;">'.$ctaBancoEmpresa[0]->CUENT_NumeroEmpresa.'</td>

      						<td style="font-weight:bold; text-align:center; width:2.0cm;">MONEDA:</td>
      						<td style="text-align:left; width:1.0cm;">'.$ctaBancoEmpresa[0]->MONED_Simbolo.'</td>

      						<td style="font-weight:bold; text-align:center; width:2.0cm;">TITULAR:</td>
      						<td style="text-align:left; width:3.5cm;">'.$ctaBancoEmpresa[0]->CUENT_Titular.'</td>
      						</tr>';
      					}

      					if ($val->CHEP_Codigo != ""){
      						$cuentasDetaHTML .= '<tr '.$background.'>
      						<td style="'.$style->border_bottom.' font-weight:bold; text-align:left; width:3.0cm;">CHEQUE:</td>
      						<td style="'.$style->border_bottom.' text-align:center; width:3.0cm;">'.$val->CHEC_Nro.'</td>
      						<td style="'.$style->border_bottom.' font-weight:bold; text-align:left; width:3.0cm;">EMISIÓN:</td>
      						<td style="'.$style->border_bottom.' text-align:center; width:3.0cm;">'.$val->CHEC_FechaEmision.'</td>
      						<td style="'.$style->border_bottom.' font-weight:bold; text-align:left; width:3.0cm;">VENCIMIENTO:</td>
      						<td style="'.$style->border_bottom.' text-align:center; width:4.0cm;">'.$val->CHEC_FechaVencimiento.'</td>
      						</tr>';
      					}
      					else{
      						$cuentasDetaHTML .= '<tr '.$background.'>
      						<td style="'.$style->border_bottom.'font-weight:bold; text-align:left; width:3.0cm;">TRANSFERENCIA:</td>
      						<td style="'.$style->border_bottom.'text-align:left; width:3.6cm;">'.$val->PAGC_Trans.'</td>
      						<td style="'.$style->border_bottom.'font-weight:bold; text-align:left; width:3.0cm;">DEPOSITO:</td>
      						<td style="'.$style->border_bottom.'text-align:left; width:3.1cm;">'.$val->PAGC_DepoNro.'</td>
      						<td style="'.$style->border_bottom.'font-weight:bold; text-align:left; width:3.0cm;">NOTA DE CREDITO:</td>
      						<td style="'.$style->border_bottom.'text-align:left; width:3.0cm;">'.$val->CRED_Serie.'-'.$this->getOrderNumeroSerie($val->CRED_Numero).'</td>
      						</tr>';
      					}
      				}

      				$cuentasDetaHTML = ($obs != "") ? $cuentasDetaHTML . '<tr '.$background.' style="font-size: 8pt">
      				<td style="'.$style->border_top.' font-weight:bold; text-align:left; width:2.5cm;">OBSERVACIÓN:</td>
      				<td style="'.$style->border_top.' text-align:left; width:16.5cm;">'.$obs.'</td>
      				</tr>' : $cuentasDetaHTML;
      			}
      		}

      		$cuentasHTML = '<table border="0" style="font-size: 7pt; line-height:5mm;">
      		<thead>
      		<tr><th style="font-weight:bold; font-size: 9pt; text-align:center; width: 19.0cm">PAGOS REGISTRADOS</th></tr>
      		</thead>
      		<tbody>
      		'.$cuentasDetaHTML.'
      		</tbody>
      		</table>';
      		$this->pdf->writeHTML($cuentasHTML,true,false,true,"");

      		if ($enviarcorreo == false)
      			$this->pdf->Output("cuenta-$ruc-$nombre_cliente.pdf", 'I');
      		else
      			return $this->pdf->Output("cuenta-$ruc-$nombre_cliente.pdf", 'S');
      	}

  ########################
  ##### CAJA
  ########################

      	public function movimientos_pdf($caja, $fechai, $fechaf, $enviarcorreo = false){

      		$filterMov = new stdClass();
      		$filterMov->caja = $caja;
      		$filterMov->fechai = $fechai;
      		$filterMov->fechaf = $fechaf;

      		$movimientoInfo = $this->ci->movimiento_model->getMovimientos($filterMov);

      		$companiaInfo = $this->ci->compania_model->obtener($this->compania);
      		$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
      		$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

      		$tipoDocumento = "RESUMEN DE MOVIMIENTOS";
      		$tipoDocumentoF = "RESUMEN DE MOVIMIENTOS";

      		$medidas = "a4"; 
      		$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
      		$this->pdf->SetMargins(10, 55, 10); 
      		$this->pdf->SetTitle("RESUMEN DE MOVIMIENTOS");
      		$this->pdf->SetFont('freesans', '', 8);

      		$this->pdf->setPrintHeader(true);
      		$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, NULL, NULL);

      		$this->pdf->AddPage();
      		$this->pdf->SetAutoPageBreak(true, 1);


      		$filterMov->movimiento = 1;
      		$resumenMov = $this->ci->movimiento_model->resumen_movimientos($filterMov);

      		$tituloHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      		<tr>
      		<th style="width: 19.0cm; font-weight:bold; text-align:center; font-size: 10pt">RESUMEN DE MOVIMIENTOS</th>
      		</tr>
      		<tr>
      		<th style="width: 19.0cm; font-weight:bold; text-align:center;">FECHA DE EMISIÓN: '.date("d/m/Y").'</th>
      		</tr>
      		<tr>
      		<th style="width: 19.0cm; font-weight:bold; text-align:center;">DESDE: '.mysql_to_human($fechai).' HASTA '.mysql_to_human($fechaf).'</th>
      		</tr>
      		</table>';
      		$this->pdf->writeHTML($tituloHTML,true,false,true,'');

      		$ingreso = 0;
      		$egreso = 0;

      		foreach ($resumenMov as $i => $val) {
      			if ($i == 0){
      				$resumenHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      				<tr>
      				<th style="border-bottom:#cccccc 1mm solid; width: 9.0cm; font-weight:bold; text-align:center;">'.$val->movimiento.'</th>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 9.0cm;"><b>NOMBRE DE CAJA:</b> '.$val->CAJA_Nombre.'</td>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 3.0cm;"><b>CÓDIGO:</b> '.$val->CAJA_CodigoUsuario.'</td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 6.0cm;"><b>TIPO DE CAJA:</b> '.$val->tipCa_Descripcion.'</td>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width:4.5cm;"><b>FORMA DE PAGO:</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;"><b>MONEDA:</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right; width:2.5cm;"><b> TOTAL:</b></td>
      				</tr>';
      			}
      			$resumenHTML .= '<tr>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width:4.5cm;">'.$val->FORPAC_Descripcion.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;">'.$val->MONED_Simbolo.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right; width:2.5cm;">'.number_format($val->total,2).'</td>
      			</tr>';

      			$ingreso = ($val->MONED_Codigo == 1) ? $ingreso + $val->total : $ingreso + ( $this->ci->tipocambio_model->getCambio($val->MONED_Codigo, $val->CAJAMOV_FechaRecep) * $val->total );
      		}
      		$resumenHTML .= '</table>';
      		$posY = $this->pdf->getY();
      		$this->pdf->writeHTML($resumenHTML,true,false,true,'');

      		$resumenHTML = "";
      		$filterMov->movimiento = 2;
      		$resumenMov = $this->ci->movimiento_model->resumen_movimientos($filterMov);
      		foreach ($resumenMov as $i => $val) {
      			if ($i == 0){
      				$resumenHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      				<tr>
      				<th style="border-bottom:#cccccc 1mm solid; width: 9.0cm; font-weight:bold; text-align:center;">'.$val->movimiento.'</th>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 9.0cm;"><b>NOMBRE DE CAJA:</b> '.$val->CAJA_Nombre.'</td>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 3.0cm;"><b>CÓDIGO:</b> '.$val->CAJA_CodigoUsuario.'</td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width: 6.0cm;"><b>TIPO DE CAJA:</b> '.$val->tipCa_Descripcion.'</td>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width:4.5cm;"><b>FORMA DE PAGO:</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;"><b>MONEDA:</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right; width:2.5cm;"><b> TOTAL:</b></td>
      				</tr>';
      			}
      			$resumenHTML .= '<tr>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width:4.5cm;">'.$val->FORPAC_Descripcion.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;">'.$val->MONED_Simbolo.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right; width:2.5cm;">'.number_format($val->total,2).'</td>
      			</tr>';

      			$egreso = ($val->MONED_Codigo == 1) ? $egreso + $val->total : $egreso + ( $this->ci->tipocambio_model->getCambio($val->MONED_Codigo, $val->CAJAMOV_FechaRecep) * $val->total );
      		}
      		$resumenHTML .= '</table>';

      		$posYA = $posY;
      		$this->pdf->SetY($posY);
      		$this->pdf->SetX(105);
      		$this->pdf->writeHTML($resumenHTML,true,false,true,'');

      		$resumenHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      		<tr>
      		<th style="border-bottom:#cccccc 1mm solid; width: 6.0cm; text-align:left;"><b>TOTAL INGRESO:</b>  S/'.number_format($ingreso,2).'</th>
      		<th style="border-bottom:#cccccc 1mm solid; width: 6.0cm; text-align:left;"><b>TOTAL EGRESO:</b>  S/'.number_format($egreso,2).'</th>
      		<th style="border-bottom:#cccccc 1mm solid; width: 7.0cm; text-align:left;"><b>SALDO:</b>  S/'.number_format($ingreso - $egreso,2).'</th>
      		</tr>
      		</table>';

      		$posY = $this->pdf->getY();
      		if ($posYA > $posY)
      			$this->pdf->SetY($posYA + 5);
      		else
      			$this->pdf->SetY($posY + 5);

      		$this->pdf->writeHTML($resumenHTML,true,false,true,'');

      		foreach ($movimientoInfo as $i => $val) {
      			if ($i == 0){
      				$resumenHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
      				<tr>
      				<th style="border-bottom:#cccccc 1mm solid; width: 19.0cm; font-weight:bold; text-align:center;">LISTA DE MOVIMIENTOS</th>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width: 3.5cm;"><b>CÓDIGO:</b> '.$val->CAJA_CodigoUsuario.'</td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width: 15.5cm;"><b>NOMBRE DE CAJA:</b> '.$val->CAJA_Nombre.'</td>
      				</tr>
      				<tr>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:3.5cm;"><b>FECHA REG.</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.5cm;"><b>FECHA MOV.</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;"><b>TIPO MOV</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:3.0cm;"><b>FORMA DE PAGO</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:1.5cm;"><b>MONEDA</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm;"><b>IMPORTE</b></td>
      				<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:4.5cm;"><b>JUSTIFICACIÓN</b></td>
      				</tr>';
      			}
      			$fechaR = explode(" ", $val->CAJAMOV_FechaRegistro);

      			$color = ($val->CAJAMOV_MovDinero == 1) ? "green" : "red";
      			$decoration = ($val->CAJAMOV_FlagEstado == 1) ? "none" : "line-through";
      			$resumenHTML .= '<tr style="text-decoration: '.$decoration.'">
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:3.5cm;">'.mysql_to_human($fechaR[0]).' | '.$this->formatHours($fechaR[1]).'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.5cm;">'.mysql_to_human($val->CAJAMOV_FechaRecep).'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:2.0cm; color:'.$color.'">'.$val->movimiento.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left; width:3.0cm;">'.$val->FORPAC_Descripcion.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center; width:1.5cm;">'.$val->MONED_Simbolo.'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right; width:2.0cm;">'.number_format($val->CAJAMOV_Monto,2).'</td>
      			<td style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:justification; width:4.5cm;">'.$val->CAJAMOV_Justificacion.'</td>
      			</tr>';
      		}
      		$resumenHTML .= '</table>';

      		$this->pdf->writeHTML($resumenHTML,true,false,true,'');

      		if ($enviarcorreo == false)
      			$this->pdf->Output("caja.pdf", 'I');
      		else
      			return $this->pdf->Output("caja.pdf", 'S');
      	}

  ########################
  ##### LETRAS
  ########################

      	public function letra_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

      		$letra = new stdClass();
      		$letra->LET_Codigo = $codigo;

      		$letraInfo = $this->ci->letra_model->getLetra($letra);
      		$docsRelacionados = $this->ci->letra_model->getComprobantes($letra);

        // DATOS DEL COMPROBANTE
      		$compania = $letraInfo[0]->COMPP_Codigo;
      		$tipo_oper = $letraInfo[0]->LET_TipoOperacion;
      		$serie = $letraInfo[0]->LET_Serie;
      		$numero = $this->getOrderNumeroSerie($letraInfo[0]->LET_Numero);
      		$total = $letraInfo[0]->LET_total;
      		$fecha = mysql_to_human($letraInfo[0]->LET_Fecha);
      		$fecha_vencimiento = mysql_to_human($letraInfo[0]->LET_FechaVenc);

      		$datos_moneda = $this->ci->moneda_model->obtener($letraInfo[0]->MONED_Codigo);
      		$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
      		$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

      		/*FORMA DE PAGO*/
      		$formapago_id = $letraInfo[0]->FORPAP_Codigo;
      		$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS
        
        // DATOS DEL CLIENTE
        $cliente = $letraInfo[0]->CLIP_Codigo;
        $proveedor = $letraInfo[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion = ($letraInfo[0]->CPC_Direccion != "") ? $letraInfo[0]->CPC_Direccion : $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion = ($letraInfo[0]->CPC_Direccion != "") ? $letraInfo[0]->CPC_Direccion : $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($compania);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = ($tipo_oper == 'V') ?  "LETRA DE VENTA" : "LETRA DE COMPRA";
        	$tipoDocumentoF = ($tipo_oper == 'V') ?  "LETRA DE VENTA" : "LETRA DE COMPRA";

        	$medidas = "a4"; 
        	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetTitle("LETRA ".$letraInfo[0]->LET_Serie."-".$letraInfo[0]->LET_Numero);
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1){
        		if ($tipo_docu == 'N'){
        			$this->pdf->setPrintHeader(false);
        			$empresaInfo[0]->EMPRC_Ruc = NULL;
        		}
        		else
        			$this->pdf->setPrintHeader(true);
        	}
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $letraInfo[0]->LET_Serie, $this->getOrderNumeroSerie($letraInfo[0]->LET_Numero) );

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        $this->pdf->RoundedRect(8, 53, 192, 26, 1.50, '1111', ''); // CLIENTE

        $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.5mm" border="0">
        <tr>
        <td style="width:3.5cm"><b>'.$tp.'</b></td>
        <td style="width:15.5cm; text-indent:-0.1cm;">'.$idCliente.'</td>
        </tr>
        <tr>
        <td style="width:3.5cm; font-weight:bold;">RUC:</td>
        <td style="width:15.5cm; text-indent:-0.1cm;">'.$ruc.'</td>
        </tr>
        <tr>
        <td style="width:3.5cm; font-weight:bold;">DENOMINACIÓN:</td>
        <td style="width:15.5cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>
        </tr>
        <tr>
        <td style="width:3.5cm; font-weight:bold;">DIRECCIÓN:</td>
        <td style="width:15.5cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>
        </tr>
        </table>';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');
        $this->pdf->SetY(83);

        $letraHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
        <tr><th colspan="5" style="font-weight:bold; text-align:center;">DETALLES DE LETRA '.$ncuota.'<br></th></tr>
        <tr bgcolor="#F1F1F1" style="font-size: 8pt">
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">FECHA DE PAGO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">FECHA DE VCTO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">IMPORTE</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:4.0cm;">MONEDA</th>
        </tr>
        <tr>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.mysql_to_human($letraInfo[0]->LET_Fecha).'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.mysql_to_human($letraInfo[0]->LET_FechaVenc).'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.number_format($letraInfo[0]->LET_Total,2).'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 4.0cm;">'.$moneda_nombre.'</td>
        </tr>
        <tr bgcolor="#F1F1F1" style="font-size: 8pt">
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">BANCO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">TITULAR</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">NÚMERO DE CUENTA</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:4.0cm;">ESTADO</th>
        </tr>
        <tr>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.$letraInfo[0]->BANC_Nombre.'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.$letraInfo[0]->LET_Representante.'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.$letraInfo[0]->LET_NumeroCuenta.'</td>
        <td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 4.0cm;">'.( ($letraInfo[0]->LET_FlagEstado == 1) ? 'PAGADO' : 'PENDIENTE').'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($letraHTML,true,false,true,'');

        $observacionHTML = '<table>
        <tr>
        <td style="border-bottom:#cccccc 1mm solid; width:19cm;"><b>OBSERVACIÓN:</b> '.$letraInfo[0]->LET_Observacion.'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($observacionHTML,true,false,true,'');

        $documentos_detalles = '';
        foreach ($docsRelacionados as $i => $val) {
        	switch ($val->CPC_TipoDocumento) {
        		case 'F':
        		$tdocumento = "FACTURA";
        		break;
        		case 'B':
        		$tdocumento = "BOLETA";
        		break;
        		case 'N':
        		$tdocumento = "COMPROBANTE";
        		break;
        		default:
        		$tdocumento = "COMPROBANTE";
        		break;
        	}

        	$documentos_detalles .= '
        	<tr>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 5.0cm;">'.$tdocumento.'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 2.0cm;">'.$val->CPC_Serie.'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 2.0cm;">'.$this->getOrderNumeroSerie($val->CPC_Numero).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 3.5cm;">'.mysql_to_human($val->CPC_Fecha).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 3.5cm;">'.mysql_to_human($val->CPC_FechaVencimiento).'</td>
        	<td style="border-bottom:#ccc 1mm solid; border-top:#ccc 1mm solid; text-align:center; width: 3.0cm;">'.number_format($val->CPC_total,2).'</td>
        	</tr>
        	';
        }

        $documentosHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
        <tr><th colspan="5" style="font-weight:bold; text-align:center;">DOCUMENTO RELACIONADO<br></th></tr>
        <tr bgcolor="#F1F1F1" style="font-size: 8pt">
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:5.0cm;">DOCUMENTO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">SERIE</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:2.0cm;">NÚMERO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3.5cm;">FECHA EMISIÓN</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3.5cm;">FECHA VCTO</th>
        <th style="border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3.0cm;">IMPORTE</th>
        </tr>
        '.$documentos_detalles.'
        </table>';
        $this->pdf->writeHTML($documentosHTML,true,false,true,'');

        if ($enviarcorreo == false)
        	$this->pdf->Output("letra.pdf", 'I');
        else
        	return $this->pdf->Output("letra.pdf", 'S');
      }

  ########################
  ##### NOTAS
  ########################

      public function nota_pdf_ticket($codigo){

      	$datos_comprobante = $this->ci->notacredito_model->obtener_comprobante($codigo);
      	$detalle_comprobante = $this->ci->notacreditodetalle_model->listar($codigo);
        // DATOS DEL COMPROBANTE
      	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
      	$tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
      	$serie = $datos_comprobante[0]->CRED_Serie;
      	$numero = $this->getOrderNumeroSerie(trim($datos_comprobante[0]->CRED_Numero));

      	$descuento = $datos_comprobante[0]->CRED_descuento;
      	$descuento100 = $datos_comprobante[0]->CRED_descuento100;
      	$igv = $datos_comprobante[0]->CRED_igv;
      	$igv100 = $datos_comprobante[0]->CRED_igv100;
      	$subtotal = $datos_comprobante[0]->CRED_subtotal;
      	$total = $datos_comprobante[0]->CRED_total;
      	$observacion = $datos_comprobante[0]->CRED_Observacion;
      	$fecha = mysql_to_human($datos_comprobante[0]->CRED_Fecha);

      	$tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
        $motivoNota = $datos_comprobante[0]->DOCUP_Codigo; // Motivo de la nota
        
        $estado = $datos_comprobante[0]->CRED_FlagEstado;

        $referencia = $datos_comprobante[0]->CRED_NumeroInicio;

        $datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        /*FORMA DE PAGO*/
        $formapago_desc = "EFECTIVO";
        #$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        #$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        #$formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS

        // DATOS DEL USUARIO
        $vendedor = $datos_comprobante[0]->vendedor;
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $idCliente = "";

        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion   = $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion   = $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );



        	switch ($datos_comprobante[0]->CRED_TipoDocumento_inicio) {
        		case 'F':
        		$tdocumento = "FACTURA";
        		$docElectronico  = "ELECTRÓNICA";
        		break;
        		case 'B':
        		$tdocumento = "BOLETA";
        		$docElectronico  = "ELECTRÓNICA";
        		break;
        		case 'N':
        		$tdocumento = "COMPROBANTE";
        		$docElectronico  = "";
        		break;
        		case 'A':
        		$tdocumento = "N/A";
        		$docElectronico  = "";
        		break;
        		default:
        		$tdocumento = "N/A";
        		$docElectronico  = "";
        		break;
        	}

        	switch ($datos_comprobante[0]->CRED_TipoNota) {
        		case 'D':
        		$docBase = "DEBITO";
        		break;
        		case 'C':
        		$docBase = "CREDITO";
        		break;
        		default:
        		$docBase = "DEBITO";
        		break;
        	}



        $medidas = array(80, 200); // TICKET
        $this->pdf = new tcpdf('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(3, 3, 3);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->AddPage();

        /* Listado de detalles */

        $gravada = 0;
        $exonerado = 0;
        $inafecto = 0;
        $gratuito = 0;
        $importeBolsa = 0;

        $detaProductos = "";
        foreach ($detalle_comprobante as $indice => $valor) {
        	$nombre_producto = ($valor->CREDET_Descripcion != '') ? $valor->CREDET_Descripcion : $valor->PROD_Nombre;
        	$nombre_producto = ($valor->CREDET_Observacion != '') ? $nombre_producto . ". " .$valor->CREDET_Observacion : $nombre_producto;

        	$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

        	$tipo_afectacion = $valor->AFECT_Codigo;
        	$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 
        	switch ($tipo_afectacion) {
        		case 1: 
        		$gravada += $valor->CREDET_Subtotal;
        		break;
        		case 8: 
        		$exonerado += $valor->CREDET_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 9: 
        		$inafecto += $valor->CREDET_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 16:
        		$inafecto += $valor->CREDET_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		default:
                        $gratuito = ( $valor->CREDET_FlagICBPER == "1" ) ? $gratuito : $gratuito + $valor->CREDET_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                        break;
                      }

                $importeBolsa = ( $valor->CREDET_FlagICBPER == "1" ) ? $importeBolsa + $valor->CREDET_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                $cantidad = ( strlen($valor->CREDET_Cantidad) == 1) ? "0".$valor->CREDET_Cantidad : $valor->CREDET_Cantidad;

                $nombre_producto = ($valor->MARCC_Descripcion != '') ? "$nombre_producto. MARCA: $valor->MARCC_Descripcion" : $nombre_producto;
                #$nombre_producto = ($valor->LOTC_Numero != '') ? "$nombre_producto. LOTE: $valor->LOTC_Numero" : $nombre_producto;
                #$nombre_producto = ($valor->LOTC_FechaVencimiento != '') ? "$nombre_producto. VCTO LOTE: ". mysql_to_human($valor->LOTC_FechaVencimiento) : $nombre_producto;

                $detaProductos = $detaProductos. '
                <tr>
                <td style="border-top:#D1D1D1 1mm solid; text-align:left"><b>['.$cantidad.']</b> '.$nombre_producto.'</td>
                <td style="border-top:#D1D1D1 1mm solid; text-align:right">'.number_format($valor->CREDET_Pu_ConIgv, 2).'</td>
                <td style="border-top:#D1D1D1 1mm solid; text-align:right">'.number_format($valor->CREDET_Total, 2).'</td>
                </tr>';
              }

              $gravada -= ($gravada * $descuento100 / 100);
              $exonerado -= ($exonerado * $descuento100 / 100);
              $inafecto -= ($inafecto * $descuento100 / 100);

              $datosCompania = $this->ci->compania_model->obtener($companiaComprobante);
              $datosEstablecimiento = $this->ci->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
              $datosEmpresa =  $this->ci->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

              $tipoDocumento = "";
              $docOperacion = ($tipo_oper == 'V') ?  "VENTA" : "COMPRA";

              switch ($datos_comprobante[0]->CRED_TipoDocumento_inicio) {
              	case 'F':
              	$tdocumento = "FACTURA";
              	$docElectronico  = "ELECTRÓNICA";
              	break;
              	case 'B':
              	$tdocumento = "BOLETA";
              	$docElectronico  = "ELECTRÓNICA";
              	break;
              	case 'N':
              	$tdocumento = "COMPROBANTE";
              	$docElectronico  = "";
              	break;
              	case 'A':
              	$tdocumento = "N/A";
              	$docElectronico  = "";
              	break;
              	default:
              	$tdocumento = "N/A";
              	$docElectronico  = "";
              	break;
              }

              switch ($datos_comprobante[0]->CRED_TipoNota) {
              	case 'D':
              	$docBase = "DEBITO";
              	$tdocQR = "7";
              	break;
              	case 'C':
              	$docBase = "CREDITO";
              	$tdocQR = "";
              	break;
              	default:
              	$docBase = "CREDITO";
              	$tdocQR = "";
              	break;
              }

              $tipoDocumento = "NOTA DE $docBase<br>$docElectronico";
              $tipoDocumentoF = "NOTA DE $docBase DE $docOperacion $docElectronico";

              $motivosC = array("ANULACIÓN DE LA OPERACIÓN",
              	"ANULACIÓN POR ERROR EN EL RUC",
              	"CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN",
              	"DESCUENTO GLOBAL",
              	"DESCUENTO POR ÍTEM",
              	"DEVOLUCIÓN TOTAL",
              	"DEVOLUCIÓN POR ÍTEM",
              	"BONIFICACIÓN",
              	"DISMINUCIÓN EN EL VALOR");
              $motivosD = array(
              	"INTERESES POR MORA",
              	"AUMENTO DE VALOR",
              	"PENALIDADES");

              switch ($tipo_docu) {
              	case 'C':
              	$motivoN = $motivosC[$motivoNota];
              	break;
              	case 'D':
              	$motivoN = $motivosD[$motivoNota];
              	break;
              	default:
              	$motivoN = $motivosC[$motivoNota];
              	break;
              }

              $cabeceraHTML = '<table align="center">';
              if ($datos_comprobante[0]->CRED_TipoDocumento_inicio != 'N'){
              	$cabeceraHTML .= '<tr>
              	<td><b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b></td>
              	</tr>
              	<tr>
              	<td><b>'.$datosEstablecimiento[0]->EESTAC_Direccion.'</b></td>
              	</tr>
              	<tr>
              	<td><b>'.$datosEstablecimiento[0]->distrito.' - '.$datosEstablecimiento[0]->provincia.' - '.$datosEstablecimiento[0]->departamento.'</b></td>
              	</tr>
              	<tr>
              	<td><b>RUC '.$datosEmpresa[0]->EMPRC_Ruc.'</b></td>
              	</tr>';
              }

              $cabeceraHTML .= '<tr>
              <td><b>'.$tipoDocumento.'</b></td>
              </tr>
              <tr>
              <td><b>'.$serie.'-'.$this->getOrderNumeroSerie($numero).'</b></td>
              </tr>
              </table>';
              $this->pdf->writeHTML($cabeceraHTML,true,false,true,'');

              $direccion = ( $direccion != '' ) ? '<tr> <td>'.$direccion.'</td> </tr>' : '';

              $clienteHTML = '
              <table>
              <tr>
              <td><b>'.$tp.'</b></td>
              </tr>
              <tr>
              <td><b>CÓDIGO: </b> '.$idCliente.'</td>
              </tr>
              <tr>
              <td><b>RUC:</b> '.$ruc.'</td>
              </tr>
              <tr>
              <td>'.$nombre_cliente.'</td>
              </tr>
              '.$direccion.'
              <tr>
              <td><b>FECHA EMISIÓN:</b> '.$fecha.'</td>
              </tr>
              <tr>
              <td><b>MONEDA:</b> '.$moneda_nombre.'</td>
              </tr>
              <tr>
              <td><b>IGV: </b>'.$igv100.'%</td>
              </tr>
              </table>';
              $this->pdf->writeHTML($clienteHTML,true,false,true,'');

              $productoHTML = '
              <table border="0">
              <tr>
              <td width="5.2cm"><b>[CANT.] DESCRIPCION</b></td>
              <td width="1.0cm" style="text-align:right"><b>P/U</b></td>
              <td width="1.2cm" style="text-align:right"><b>TOTAL</b></td>
              </tr>
              '.$detaProductos.'
              </table>';
              $this->pdf->writeHTML($productoHTML,true,false,true,'');

              $descuentoHTML = ( $descuento > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">DESCUENTO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($descuento, 2).'</td>
              </tr>' : '';

              $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">EXONERADO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($exonerado, 2).'</td>
              </tr>' : '';

              $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">INAFECTO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($inafecto, 2).'</td>
              </tr>' : '';

              $gravadaHTML = ( $gravada > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">GRAVADO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($gravada, 2).'</td>
              </tr>' : '';

              $igvHTML = ( $igv > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">18% IGV</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($igv, 2).'</td>
              </tr>' : '';

              $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">GRATUITO</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($gratuito, 2).'</td>
              </tr>' : '';

              $importeBolsaHTML = ( $importeBolsa > 0 ) ? '<tr>
              <td style="width:5.2cm; text-align:right;">IMPUESTO BOLSA</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($importeBolsa, 2).'</td>
              </tr>' : '';

              $totalesHTML = '
              <table border="0" style="font-weight: bold; padding: 0;">
              '.$gratuitoHTML.'
              '.$descuentoHTML.'
              '.$exoneradoHTML.'
              '.$inafectoHTML.'
              '.$gravadaHTML.'
              '.$igvHTML.'
              '.$importeBolsaHTML.'
              <tr>
              <td style="width:5.2cm; text-align:right;">TOTAL</td>
              <td style="width:1.0cm; text-align:right;">' . $simbolo_moneda . '</td>
              <td style="width:1.2cm; text-align:right;">'.number_format($total, 2).'</td>
              </tr>
              </table>';

              $this->pdf->writeHTML($totalesHTML,true,false,true,'');


              $footerHTML = '
              <table cellspacing="1px" align="justify">
              <tr>
              <td><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
              </tr>
              <tr>
              <td><b>VENDEDOR:</b> '.$vendedor.'</td>
              </tr>
              <tr>
              <td><b>DOCUMENTO RELACIONADO:</b> '.$tdocumento.' '.$referencia.'</td>
              </tr>
              <tr>
              <td><b>MOTIVO DE EMISIÓN:</b> '.$motivoNota.' - '.$motivoN.'</td>
              </tr>
              <tr>
              <td><b>OBSERVACIÓN:</b> '.$observacion.'</td>
              </tr>
              </table>';
              $this->pdf->writeHTML($footerHTML,true,false,true,'');

            // CODIGO DE QR GENERADO POR EL SISTEMA
              $style = array(
              	'border' => 2,
              	'position' => 'C',
              	'vpadding' => 'auto',
              	'hpadding' => 'auto',
              	'fgcolor' => array(40,40,40),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
              );

              if ( strlen($ruc) == 11 )
              	$truc = "6";
              else 
              	if ( strlen($ruc) == 8 )
              		$truc = "1"; 
              	else
              		$truc = "-";

              	$cadenaQR = $datosEmpresa[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
              	$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 30, 30, $style, "");

              	$posY = $this->pdf->GetY();
              	$posY += 32;
              	$this->pdf->SetY($posY);

              	$footerHTML = '<table cellspacing="1px" style="font-size:7pt; text-align:center;">
              	<tr>
              	<td>REPRESENTACIÓN IMPRESA DE '.$tipoDocumentoF.': '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
              	</tr>
              	</table>';
              	$this->pdf->writeHTML($footerHTML,false,false,true,'');

              	if ($estado == 0){
              		$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 5, 15, 70, 70, '', '', '', false, 300);
              	}

              	$this->pdf->Output('Ticket.pdf', 'I');
              }

              public function nota_pdf_a4($codigo, $flagPdf = 1, $enviarcorreo = false){

              	$datos_comprobante = $this->ci->notacredito_model->obtener_comprobante($codigo);
              	$detalle_comprobante = $this->ci->notacreditodetalle_model->listar($codigo);
        // DATOS DEL COMPROBANTE
              	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
              	$tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
              	$serie = $datos_comprobante[0]->CRED_Serie;
              	$numero = $this->getOrderNumeroSerie(trim($datos_comprobante[0]->CRED_Numero));

              	$descuento = $datos_comprobante[0]->CRED_descuento;
              	$descuento100 = $datos_comprobante[0]->CRED_descuento100;
              	$igv = $datos_comprobante[0]->CRED_igv;
              	$igv100 = $datos_comprobante[0]->CRED_igv100;
              	$subtotal = $datos_comprobante[0]->CRED_subtotal;
              	$total = $datos_comprobante[0]->CRED_total;
              	$observacion = $datos_comprobante[0]->CRED_Observacion;
              	$fecha = mysql_to_human($datos_comprobante[0]->CRED_Fecha);

              	$tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
        $motivoNota = $datos_comprobante[0]->DOCUP_Codigo; // Motivo de la nota
        
        $estado = $datos_comprobante[0]->CRED_FlagEstado;

        $referencia = $datos_comprobante[0]->CRED_NumeroInicio;

        $datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        /*FORMA DE PAGO*/
        $formapago_desc = "";
        #$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        #$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        #$formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS

        // DATOS DEL VENDEDOR
        $vendedor = $datos_comprobante[0]->vendedor;
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $idCliente = "";

        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion   = $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion   = $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = "";
        	$docOperacion = ($tipo_oper == 'V') ?  "VENTA" : "COMPRA";

        	switch ($datos_comprobante[0]->CRED_TipoDocumento_inicio) {
        		case 'F':
        		$tdocumento = "FACTURA";
        		$docElectronico  = "ELECTRÓNICA";
        		break;
        		case 'B':
        		$tdocumento = "BOLETA";
        		$docElectronico  = "ELECTRÓNICA";
        		break;
        		case 'N':
        		$tdocumento = "COMPROBANTE";
        		$docElectronico  = "";
        		break;
        		case 'A':
        		$tdocumento = "N/A";
        		$docElectronico  = "";
        		break;
        		default:
        		$tdocumento = "N/A";
        		$docElectronico  = "";
        		break;
        	}

        	switch ($datos_comprobante[0]->CRED_TipoNota) {
        		case 'D':
        		$docBase = "DEBITO";
        		$tdocQR = "7";
        		break;
        		case 'C':
        		$docBase = "CREDITO";
        		$tdocQR = "";
        		break;
        		default:
        		$docBase = "CREDITO";
        		$tdocQR = "";
        		break;
        	}

        	$tipoDocumento = "NOTA DE $docBase<br>$docElectronico";
        	$tipoDocumentoF = "NOTA DE $docBase DE $docOperacion $docElectronico";

        	$motivosC = array("",
        		"ANULACIÓN DE LA OPERACIÓN",
        		"ANULACIÓN POR ERROR EN EL RUC",
        		"CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN",
        		"DESCUENTO GLOBAL",
        		"DESCUENTO POR ÍTEM",
        		"DEVOLUCIÓN TOTAL",
        		"DEVOLUCIÓN POR ÍTEM",
        		"BONIFICACIÓN",
        		"DISMINUCIÓN EN EL VALOR");
        	$motivosD = array(  "",
        		"INTERESES POR MORA",
        		"AUMENTO DE VALOR",
        		"PENALIDADES");

        	switch ($tipo_docu) {
        		case 'C':
        		$motivoN = $motivosC[$motivoNota];
        		break;
        		case 'D':
        		$motivoN = $motivosD[$motivoNota];
        		break;
        		default:
        		$motivoN = $motivosC[$motivoNota];
        		break;
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetTitle("$tipoDocumentoF  $serie - $numero");
        	$this->pdf->SetFont('freesans', '', 8);
        	if ($flagPdf == 1){
        		if ($datos_comprobante[0]->CRED_TipoDocumento_inicio == 'N'){
        			$this->pdf->setPrintHeader(false);
        			$empresaInfo[0]->EMPRC_Ruc = NULL;
        		}
        		else
        			$this->pdf->setPrintHeader(true);
        	}
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );

        #$this->pdf->setPrintFooter(false);
        	$this->pdf->AddPage();

        	if ($datos_comprobante[0]->CRED_TipoDocumento_inicio == 'N'){
        		$this->pdf->printHeaderData();
        		$this->pdf->writeHTML("<br><br><br>",true,false,true,'');
        	}

        	$this->pdf->SetAutoPageBreak(true, 1);

        	/* Listado de detalles */
        	$gravada = 0;
        	$exonerado = 0;
        	$inafecto = 0;
        	$gratuito = 0;
        	$importeBolsa = 0;

        	$detaProductos = "";
        	foreach ($detalle_comprobante as $indice => $valor) {               
        		$nombre_producto = ($valor->CREDET_Descripcion != '') ? $valor->CREDET_Descripcion : $valor->PROD_Nombre;
        		$nombre_producto = ($valor->CREDET_Observacion != '') ? $nombre_producto . ". " .$valor->CREDET_Observacion : $nombre_producto;

        		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
        		$tipo_afectacion = $valor->AFECT_Codigo;
        		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

        		switch ($tipo_afectacion) {
        			case 1: 
        			$gravada += $valor->CREDET_Subtotal;
        			break;
        			case 8: 
        			$exonerado += $valor->CREDET_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 9: 
        			$inafecto += $valor->CREDET_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 16:
        			$inafecto += $valor->CREDET_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			default:
                        $gratuito = ( $valor->CREDET_FlagICBPER == "1" ) ? $gratuito : $gratuito + $valor->CREDET_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                        break;
                      }

                $importeBolsa = ( $valor->CREDET_FlagICBPER == "1" ) ? $importeBolsa + $valor->CREDET_Total : $importeBolsa; # SI TIENE BOLSA SUMA

                $bgcolor = ($indice % 2 == 0) ? "#F1F1F1" : "#FFFFFF";

                $detaProductos = $detaProductos. '
                <tr bgcolor="'.$bgcolor.'">
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CREDET_Cantidad.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->PROD_CodigoUsuario.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nombre_producto.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CREDET_Pu, 2).'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CREDET_Pu_ConIgv, 2).'</td>
                <td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.number_format($valor->CREDET_Total, 2).'</td>
                </tr>';
              }

              $gravada -= ($gravada * $descuento100 / 100);
              $exonerado -= ($exonerado * $descuento100 / 100);
              $inafecto -= ($inafecto * $descuento100 / 100);

        $this->pdf->RoundedRect(8, 53, 130, 26, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(139, 53, 60, 26, 1.50, '1111', ''); // FECHA

        $clienteHTML = '<table style="text-indent:0cm;" cellpadding="0.02cm" border="0">
        <tr>
        <td style="width:3.2cm"><b>'.$tp.'</b></td>
        <td colspan="2" style="width:9.8cm; text-indent:-0.1cm;">'.$idCliente.'</td>

        <td style="width:2.6cm; font-weight:bold;">FECHA EMISIÓN:</td>
        <td>'.$fecha.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">RUC:</td>
        <td style="width:9.2cm; text-indent:-0.1cm;">'.$ruc.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;">MONEDA:</td>
        <td>'.$moneda_nombre.'</td>
        </tr>
        <tr>
        <td style="width:3.2cm; font-weight:bold;">DENOMINACIÓN:</td>
        <td style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;">IGV:</td>
        <td>'.$igv100.'%</td>
        </tr>
        <tr>
        <td rowspan="2" style="width:3.2cm; font-weight:bold;">DIRECCIÓN:</td>
        <td rowspan="2" style="width:9.2cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>

        <td style="width:0.6cm;"></td>
        <td style="width:2.0cm; font-weight:bold;">VENDEDOR:</td>
        <td>'.$vendedor.'</td>
        </tr>
        <tr> 

        <td style="width:0.6cm;"></td>
        <td style="width:2.6cm; font-weight:bold;"></td>
        <td></td>
        </tr>
        </table>';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        #$this->pdf->RoundedRect(8, 81, 191, 7, 1.50, '1111', ''); // PRODUCTOS
        $this->pdf->SetY(83);
        $productoHTML = '
        <table border="0" style="font-size: 8pt; line-height:5mm;">
        <tr style="font-size: 8pt">
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.2cm;">CANT.</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">CÓDIGO</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:8.3cm;">DESCRIPCIÓN</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:2.4cm;">MARCA</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">V/U</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
        <th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">IMPORTE</th>
        </tr>
        '.$detaProductos.'
        </table>';
        $this->pdf->writeHTML($productoHTML,true,false,true,'');

        $descuentoHTML = ( $descuento > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Descuento</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($descuento, 2).'</td>
        </tr>' : '';

        $exoneradoHTML = ( $exonerado > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Exonerado</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($exonerado, 2).'</td>
        </tr>' : '';

        $inafectoHTML = ( $inafecto > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Inafecto</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($inafecto, 2).'</td>
        </tr>' : '';

        $gravadaHTML = ( $gravada > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Gravado</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($gravada, 2).'</td>
        </tr>' : '';

        $igvHTML = ( $igv > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">18% IGV</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($igv, 2).'</td>
        </tr>' : '';

        $gratuitoHTML = ( $gratuito > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Gratuito</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($gratuito, 2).'</td>
        </tr>' : '';

        $importeBolsaHTML = ( $importeBolsa > 0 ) ? '<tr>
        <td style="width:15cm; font-weight:bold;">Impuesto bolsa</td>
        <td style="width:01.0cm; font-weight:bold;">' . $simbolo_moneda . '</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($importeBolsa, 2).'</td>
        </tr>' : '';

        $totalesHTML = '<table align="right" cellspacing="0.1cm">
        '.$gratuitoHTML.'
        '.$descuentoHTML.'
        '.$exoneradoHTML.'
        '.$inafectoHTML.'
        '.$gravadaHTML.'
        '.$igvHTML.'
        '.$importeBolsaHTML.'
        <tr>
        <td style="width:15cm; font-weight:bold;">Total</td>
        <td style="width:01.0cm; font-weight:bold;">'.$simbolo_moneda.'</td>
        <td style="width:02.0cm; font-weight:bold;">'.number_format($total, 2).'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($totalesHTML,true,false,true,'');

        $posY = $this->pdf->GetY();
        $this->pdf->RoundedRect(8, $posY, 192, 4, 1.50, '1111', '');

        $totalLetrasHTML = '<table cellspacing="1px" border="0">
        <tr>
        <td style="width:18.4cm; text-align:justification;" ><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
        </tr>
        </table>';
        $this->pdf->writeHTML($totalLetrasHTML,false,false,true,false);

        $adicionalHTML = '<table cellspacing="0.1cm" border="0">
        <tr>
        <td style="text-align:right; width:7cm;"><b>MOTIVO DE EMISIÓN:</b></td>
        <td style="width:8.6cm;">'.$motivoNota.' - '.$motivoN.'</td>
        </tr>
        <tr>
        <td style="text-align:right; width:7cm;"><b>DOCUMENTO RELACIONADO:</b></td>
        <td style="width:8.6cm;">'.$tdocumento.' '.$referencia.'</td>
        </tr>
        <tr>
        <td style="text-align:right; width:7cm;"><b>OBSERVACIÓN:</b></td>
        <td style="width:8.6cm;">'.$observacion.'</td>
        </tr>
        </table>
        ';
        
        $this->pdf->writeHTML($adicionalHTML,false,false,true,false);

        if ($datos_comprobante[0]->CRED_TipoDocumento_inicio != 'N'){
        	$posY = $this->pdf->GetY();
        	$this->pdf->RoundedRect(8, $posY, 165, 25, 1.50, '1111', '');
            #$this->pdf->SetY(83);

        	$footerHTML = '<table cellspacing="0.25cm" border="0">
        	<tr>
        	<td style="width:16cm;">REPRESENTACIÓN IMPRESA DE LA '.$tipoDocumentoF.' '.$serie.'-'.$this->getOrderNumeroSerie($numero).'</td>
        	</tr>
        	</table>
        	';
            // CODIGO QR INTERNO GENERADO POR EL SISTEMA
        	$style = array(
        		'border' => 1,
        		'position' => 'R',
        		'vpadding' => 'auto',
        		'hpadding' => 'auto',
        		'fgcolor' => array(80,80,80),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                  );

        	if ( strlen($ruc) == 11 )
        		$truc = "6";
        	else 
        		if ( strlen($ruc) == 8 )
        			$truc = "1"; 
        		else
        			$truc = "-";

        		$cadenaQR = $empresaInfo[0]->EMPRC_Ruc . "|" . $tdocQR . "|" . $serie . "|" . $numero . "|" . number_format($igv, 2) . "|" . number_format($total, 2) . "|" . $fecha . "|" . $truc . "|" . $ruc;
        		$codeQR = $this->pdf->write2DBarcode($cadenaQR, "QRCODE,L", '', '', 25, 25, $style, "");

        		$this->pdf->writeHTML($footerHTML,true,false,true,false);
        	}

        	if ($estado == 0){
        		$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 100, 140, '', '', '', false, 300);
        	}

        	if ($enviarcorreo == false)
        		$this->pdf->Output("doc.pdf", 'I');
        	else
        		return $this->pdf->Output("doc.pdf", 'S');
        }

  ########################
  ##### GUIAS
  ########################

        public function guiatrans_pdf($codigo, $flagPdf = 0, $enviarcorreo = false){

        	$datos_guiatrans = $this->ci->guiatrans_model->obtener($codigo);
        	$detalle_comprobante = $this->ci->guiatransdetalle_model->listar($codigo);

        	$guiasap = $datos_guiatrans[0]->GUIASAP_Codigo;
        	$guiainp = $datos_guiatrans[0]->GUIAINP_Codigo;
        	$serie = $datos_guiatrans[0]->GTRANC_Serie;
        	$numero = $this->getOrderNumeroSerie($datos_guiatrans[0]->GTRANC_Numero);
        	$companiaComprobante = $datos_guiatrans[0]->COMPP_Codigo;
        	$observacion = strtoupper($datos_guiatrans[0]->GTRANC_Observacion);
        	$placa = $datos_guiatrans[0]->GTRANC_Placa;
        	$licencia = $datos_guiatrans[0]->GTRANC_Licencia;
        	$nombre_conductor = $datos_guiatrans[0]->GTRANC_Chofer;
        	$idAlmacenOrigen = $datos_guiatrans[0]->GTRANC_AlmacenOrigen;
        	$idAlmacenDestino = $datos_guiatrans[0]->GTRANC_AlmacenDestino;
        	$estado = $datos_guiatrans[0]->GTRANC_FlagEstado;
        	$pedido = $datos_guiatrans[0]->PEDIP_Codigo;

        	$empresa_transporte = $datos_guiatrans[0]->EMPRP_Codigo;
        	$fecha = mysql_to_human($datos_guiatrans[0]->GTRANC_Fecha);
        	$ruc_empresaTrans = "";
        	$nombre_empresaTrans = "";

        	if ($empresa_transporte != '') {
        		$datos_emprtrans = $this->ci->empresa_model->obtener_datosEmpresa($empresa_transporte);
        		if (count($datos_emprtrans) > 0) {
        			$ruc_empresaTrans = $datos_emprtrans[0]->EMPRC_Ruc;
        			$nombre_empresaTrans = $datos_emprtrans[0]->EMPRC_RazonSocial;
        		}
        	}

        	/* Listado de detalles */
        	$detaProductos = "";
        	if (count($detalle_comprobante) > 0) {
        		foreach ($detalle_comprobante as $indice => $valor) {
        			$bgcolor = ($indice % 2 == 0) ? "#FFFFFF" : "#F1F1F1";
        			$producto = $valor->PROD_Codigo;

                    //$nomprod = $datos_producto[0]->PROD_Nombre;
        			$nomprod = $valor->GTRANDETC_Descripcion;
        			$nomprod = (isset($valor->GTRANDETC_Observacion) && $valor->GTRANDETC_Observacion != '') ? $nomprod . " <br> " .$valor->GTRANDETC_Observacion : $nomprod;

        			$datos_producto = $this->ci->producto_model->obtener_producto($producto);

        			$costo = $valor->GTRANDETC_Costo;
        			$cantidad = $valor->GTRANDETC_Cantidad;

        			$codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;

        			$unidadMedida = $this->ci->unidadmedida_model->obtener($valor->UNDMED_Codigo);
        			$unidadSimbolo = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

        			$detaProductos = $detaProductos. '
        			<tr bgcolor="'.$bgcolor.'">
        			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.$cantidad.'</td>
        			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$unidadSimbolo.'</td>
        			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$codigo_usuario.'</td>
        			<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$nomprod.'</td>
        			</tr>';
        		}
        	}

        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(7, 55, 10); 
        	$this->pdf->SetTitle('GUIA DE TRANSFERENCIA');
        	$this->pdf->SetFont('helvetica', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(false);

        	$datosAlmacenOrigen = $this->ci->almacen_model->obtener($idAlmacenOrigen);
        	$datosCompania = $this->ci->compania_model->obtener($companiaComprobante);
        	$datosEstablecimiento = $this->ci->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
        	$datosEmpresa =  $this->ci->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

        	if ($pedido != NULL || $pedido != ''){
        		$datosPedido = $this->ci->pedido_model->obtener_pedido_filtrado($pedido);
        		$pedidoSerie = $datosPedido[0]->PEDIC_Serie;
        		$pedidoNumero = $datosPedido[0]->PEDIC_Numero;
        		$datosAlmacenDestino[0]->COMPP_Codigo = $datosPedido[0]->COMPP_Codigo;
        	}
        	else{
        		$pedidoSerie = "";
        		$pedidoNumero = "";
        	}

        	$datosAlmacenDestino = $this->ci->almacen_model->obtener($idAlmacenDestino);
        	$datosCompaniaDestino = $this->ci->compania_model->obtener($datosAlmacenDestino[0]->COMPP_Codigo);
        	$datosEstablecimientoDestino = $this->ci->emprestablecimiento_model->listar( $datosCompaniaDestino[0]->EMPRP_Codigo, '', $datosAlmacenDestino[0]->COMPP_Codigo );
        	$datosEmpresaDestino =  $this->ci->empresa_model->obtener_datosEmpresa( $datosCompaniaDestino[0]->EMPRP_Codigo );

        	$tipoDocumento = "GUIA DE TRANSFERENCIA<br>ELECTRÓNICA";
        	$tipoDocumentoF = "GUIA DE TRANSFERENCIA ELECTRÓNICA";

        	$this->pdf->settingHeaderData($comppName[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );
        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	$partida = $datosAlmacenOrigen[0]->ALMAC_Descripcion .' - ' . $datosAlmacenOrigen[0]->ALMAC_Direccion;
        	$destino = $datosAlmacenDestino[0]->ALMAC_Descripcion .' - ' . $datosAlmacenDestino[0]->ALMAC_Direccion;

        	$h = 35;
        	$h = ( strlen($partida) > 98 ) ? $h + 2.5 : $h;
        	$h = ( strlen($destino) > 98 ) ? $h + 2.5 : $h;

        #$this->pdf->RoundedRect(5, 53, 200, 25, 1.50, '1111', ''); // CLIENTE
        #$this->pdf->RoundedRect(5, 80, 200, $h, 1.50, '1111', ''); // FECHA

        	$clienteHTML = '
        	<table style="text-indent:0.2cm; line-height:5mm;" border="0">
        	<tr>
        	<td style="width:14cm;"><b>EMPRESA DESTINO</b></td>

        	<td style="width:3cm; font-weight:bold;">FECHA EMISIÓN:</td>
        	<td style="width:2.5cm;">'.$fecha.'</td>
        	</tr>
        	<tr>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:3.5cm; font-weight:bold;">RUC:</td>
        	<td colspan="2" style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:16cm;">'.$datosEmpresaDestino[0]->EMPRC_Ruc.'</td>
        	</tr>
        	<tr>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:3.5cm; font-weight:bold;">DENOMINACIÓN:</td>
        	<td colspan="2" style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:16cm; text-align:justification">'.$datosEmpresaDestino[0]->EMPRC_RazonSocial.'</td>
        	</tr>
        	<tr> 
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:3.5cm; font-weight:bold;">DIRECCIÓN:</td>
        	<td colspan="2" style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:16cm; text-align:justification">'.$datosEmpresaDestino[0]->EMPRC_Direccion.'</td>
        	</tr>
        	</table>';
        	$this->pdf->writeHTML($clienteHTML,true,false,true,'');

        	$clienteHTML = '<table style="text-indent:0cm; line-height:5mm;" border="0">
        	<tr>
        	<td colspan="2" style="font-weight:bold;">DETALLES DEL TRASLADO</td>
        	</tr>
        	<tr> 
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:3.5cm; font-weight:bold">PUNTO DE PARTIDA:</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:16.0cm; text-align:justification;">'.$partida.'</td>
        	</tr>
        	<tr> 
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:3.5cm; font-weight:bold">PUNTO DE LLEGADA:</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:16.0cm; text-align:justification;">'.$destino.'</td>
        	</tr>
        	</table>
        	<table border="0" style="line-height:5mm;">
        	<tr>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:10cm;"><table style="text-indent:0.2cm; line-height:5mm;" border="0">
        	<tr>
        	<td style="font-weight:bold;">EMPRESA DE TRANSPORTE</td>
        	</tr>
        	<tr>
        	<td style="width:3cm; font-weight:bold">RUC: </td>
        	<td style="text-indent:0.1cm; text-align:left;">'.$ruc_empresaTrans.'</td>
        	</tr>
        	<tr> 
        	<td style="width:3cm; font-weight:bold">DENOMINACIÓN:</td>
        	<td style="text-indent:0.1cm; text-align:justification">'.$nombre_empresaTrans.'</td>
        	</tr>
        	</table>
        	</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; width:9.5cm;"><table style="text-indent:0.2cm; line-height:5mm;" border="0">
        	<tr>
        	<td style="font-weight:bold;">DATOS DEL CONDUCTOR</td>
        	</tr>
        	<tr>
        	<td style="width:3cm; font-weight:bold">NOMBRE: </td>
        	<td style="text-indent:0.1cm; text-align:left;">'.$nombre_conductor.'</td>
        	</tr>
        	<tr> 
        	<td style="width:3cm; font-weight:bold">LICENCIA:</td>
        	<td style="text-indent:0.1cm; text-align:justification">'.$licencia.'</td>
        	</tr>
        	<tr> 
        	<td style="width:3cm; font-weight:bold">PLACA:</td>
        	<td style="text-indent:0.1cm; text-align:justification">'.$placa.'</td>
        	</tr>
        	</table>
        	</td>
        	</tr>
        	</table>';
        	$this->pdf->writeHTML($clienteHTML,true,false,true,'');

        	if ($observacion != ''){
        		$adicionalHTML = '<table border="0" style="width:19.5cm; font-size:8pt; line-height:5mm;">
        		<tr>
        		<td style="font-weight:bold; text-align:justification;">OBSERVACIÓN: <span style="font-style:italic; font-weight:normal;">'.$observacion.'</span></td>
        		</tr>
        		</table>';
        		$this->pdf->writeHTML($adicionalHTML,true,false,true,'');
        	}

        #$this->pdf->RoundedRect(5, 73, 200, 8, 1.50, '1111', ''); // PRODUCTOS
        	$productoHTML = '<table border="0" style="font-size: 8pt; line-height:5mm;">
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:right; width:2cm;">CANTIDAD</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3cm;">UNIDAD M.</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:center; width:3cm;">CÓDIGO.</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-weight:bold; text-align:justification; width:11.5cm;">DESCRIPCIÓN</th>
        	</tr>
        	'.$detaProductos.'
        	</table>';
        	$this->pdf->writeHTML($productoHTML,true,false,true,'');

        	$descuentoHTML = ($descuento > 0) ? '<tr>
        	<td width="160px"><b>DESCUENTO '.$moneda_simbolo.'</b></td>
        	<td width="40px">'.number_format($descuento, 2).'</td>
        	</tr>' : '' ;

        	if ($estado == 0){
        		$this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 140, 140, '', '', '', false, 300);
        	}

        	$nameFile = "G. Transferencia -".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero).".pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

        public function guiarem_pdf($codigo, $flagPdf = 0, $enviarcorreo = false){
        	$datos_guiarem = $this->ci->guiarem_model->obtener($codigo);

        	$tipo_movimiento = $datos_guiarem[0]->TIPOMOVP_Codigo;
        	$tipo_oper = $datos_guiarem[0]->GUIAREMC_TipoOperacion;
        	$otro_motivo = $datos_guiarem[0]->GUIAREMC_OtroMotivo;
        	$empresa_transporte = $datos_guiarem[0]->EMPRP_Codigo;
        	$almacen = $datos_guiarem[0]->ALMAP_Codigo;
        	$usuario = $datos_guiarem[0]->USUA_Codigo;
        	$moneda = $datos_guiarem[0]->MONED_Codigo;
        	$referencia = $datos_guiarem[0]->DOCUP_Codigo;
        	$cliente = $datos_guiarem[0]->CLIP_Codigo;
        	$proveedor = $datos_guiarem[0]->PROVP_Codigo;
        	$recepciona_nombres = $datos_guiarem[0]->GUIAREMC_PersReceNombre;
        	$recepciona_dni = $datos_guiarem[0]->GUIAREMC_PersReceDNI;
        	$numero_ref = $datos_guiarem[0]->GUIAREMC_NumeroRef;
        	$numero_ocompra = $datos_guiarem[0]->GUIAREMC_OCompra;
        	$serie = $datos_guiarem[0]->GUIAREMC_Serie;
        	$numero = $datos_guiarem[0]->GUIAREMC_Numero;
        	$numero_ref = $datos_guiarem[0]->GUIAREMC_NumeroRef;
        	$codigo_usuario = $datos_guiarem[0]->GUIAREMC_CodigoUsuario;
        	$fecha_traslado = $datos_guiarem[0]->GUIAREMC_FechaTraslado;
        	$fecha = $datos_guiarem[0]->GUIAREMC_Fecha;
        	$observacion = $datos_guiarem[0]->GUIAREMC_Observacion;
        	$placa = $datos_guiarem[0]->GUIAREMC_Placa;
        	$marca = $datos_guiarem[0]->GUIAREMC_Marca;
        	$registro_mtc = $datos_guiarem[0]->GUIAREMC_RegistroMTC;
        	$certificado = $datos_guiarem[0]->GUIAREMC_Certificado;
        	$licencia = $datos_guiarem[0]->GUIAREMC_Licencia;
        	$nombre_conductor = $datos_guiarem[0]->GUIAREMC_NombreConductor;
        	$ocompra = $datos_guiarem[0]->OCOMP_Codigo;
        	$estado = $datos_guiarem[0]->GUIAREMC_FlagEstado;
        	$punto_partida = $datos_guiarem[0]->GUIAREMC_PuntoPartida;
        	$punto_llegada = $datos_guiarem[0]->GUIAREMC_PuntoLlegada;

        	$mod_transporte =  $datos_guiarem[0]->GUIAREMC_ModTransporte;
        	$peso_total = $datos_guiarem[0]->GUIAREMC_PesoTotal;
        	$num_bultos = $datos_guiarem[0]->GUIAREMC_NumBultos;
        	$nombre_empresa_transporte = $datos_guiarem[0]->GUIAREMC_EmpresaTransp;
        	$ruc_empresa_transporte = $datos_guiarem[0]->GUIAREMC_RucEmpresaTransp;

        	if($mod_transporte==1){
        		$modalidad_transporte="TRANSPORTE PUBLICO";
        	}elseif ($mod_transporte==2) {
        		$modalidad_transporte="TRANSPORTE PRIVADO";
        	}
        	$tipo_movimiento = $this->ci->tipomovimiento_model->obtener($tipo_movimiento);
        	$movimiento_descripcion = $tipo_movimiento[0]->TIPOMOVC_Descripcion; 

        	$fecha_traslado = mysql_to_human($fecha_traslado);
        	$nFechaEntrega = explode( '/', $fecha_traslado );
        	$fecha_entrega = ($datos_guiarem[0]->GUIAREMC_FechaTraslado == "" || $datos_guiarem[0]->GUIAREMC_FechaTraslado == NULL || $datos_guiarem[0]->GUIAREMC_FechaTraslado == "-") ? "" : $nFechaEntrega[0]." DE ".$this->mesesEs($nFechaEntrega[1])." DEL ".$nFechaEntrega[2];

        	$datos_moneda = $this->ci->moneda_model->obtener($moneda);

        	$nombre_almacen = '';
        	if ($almacen != '') {
        		$datos_almacen = $this->ci->almacen_model->obtener($almacen);
        		$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        	}

        	$simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'SOLES');

        	if ($tipo_oper == 'C') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		$nombres = $datos_proveedor->nombre;
        		$ruc = $datos_proveedor->ruc;
        		$telefono = $datos_proveedor->telefono;
        		$direccion = $datos_proveedor->direccion;
        		$fax = $datos_proveedor->fax;
        	} else {
        		$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        		$nombres = $datos_cliente->nombre;
        		$ruc = $datos_cliente->ruc;
        		$telefono = $datos_cliente->telefono;
        		$direccion = $datos_cliente->direccion;
        		$fax = $datos_cliente->fax;
        	}

        	$companiaInfo = $this->ci->compania_model->obtener($datos_guiarem[0]->COMPP_Codigo);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = "GUIA DE REMISIÓN<br>ELECTRÓNICA";
        	$tipoDocumentoF = "GUIA DE REMISIÓN ELECTRÓNICA";

        	$transporte = $this->ci->empresa_model->obtener_datosEmpresa($empresa_transporte);

        	$medidas = "a4"; 
        	$this->pdf = new pdfGuiaRemision('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetTitle('GUIA REMISION '.$serie.'-'.$numero);
        	$this->pdf->SetMargins(10, 55, 10); 
        	$this->pdf->SetFont('freesans', '', 9);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->settingHeaderData($empresaInfo[0]->EMPRC_Ruc, $tipoDocumento, $serie, $this->getOrderNumeroSerie($numero) );

        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	/* Listado de detalles */
        	$detalle_guiarem = $this->ci->guiaremdetalle_model->obtener2($codigo);
        	$detaProductos = '';
        	foreach ($detalle_guiarem as $indice => $valor) {
        		$detacodi = $valor->GUIAREMDETP_Codigo;
        		$producto = $valor->PRODCTOP_Codigo;
                //$unidad = $valor->UNDMED_Codigo;
        		$cantidad = $valor->GUIAREMDETC_Cantidad;
        		$pu = $valor->GUIAREMDETC_Pu;
        		$subtotal = $valor->GUIAREMDETC_Subtotal;
        		$igv = $valor->GUIAREMDETC_Igv;
        		$descuento = $valor->GUIAREMDETC_Descuento;
        		$total = $valor->GUIAREMDETC_Total;
        		$pu_conigv = $valor->GUIAREMDETC_Pu_ConIgv;
        		$costo = $valor->GUIAREMDETC_Costo;
        		$venta = $valor->GUIAREMDETC_Venta;
        		$peso = $valor->GUIAREMDETC_Peso;
        		$GenInd = $valor->GUIAREMDETC_GenInd;

        		$codigo_usuario = $valor->PROD_CodigoUsuario;
        		$nombre_producto = ($valor->GUIAREMDETC_Descripcion != NULL) ? "$valor->GUIAREMDETC_Descripcion. $valor->GUIAREMDETC_Observacion" : $valor->PROD_Nombre.". $valor->GUIAREMDETC_Observacion";
        		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
        		$unidadMedida = $this->ci->unidadmedida_model->obtener($valor->UNDMED_Codigo);
        		$medidaDetalle = "";
        		$medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Descripcion : "NIU";
        		$tipo_afectacion = $valor->AFECT_Codigo;
        		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion);

        		switch ($tipo_afectacion) {
        			case 1: 
        			$gravada += $valor->OCOMDEC_Subtotal;
        			break;
        			case 8: 
        			$exonerado += $valor->OCOMDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 9: 
        			$inafecto += $valor->OCOMDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 16:
        			$inafecto += $valor->OCOMDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			default:
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			$gratuito += $valor->OCOMDEC_Subtotal;
        			break;
        		}


        		$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

        		$detaProductos = $detaProductos. '<tr bgcolor="'.$bgcolor.'">
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; border-bottom:1px #000 solid; text-align:center;">'.$valor->GUIAREMDETC_Cantidad.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; border-bottom:1px #000 solid; text-align:center;">'.$codigo_usuario.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; border-bottom:1px #000 solid; text-align:center;">'.$medidaDetalle.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; border-bottom:1px #000 solid; text-align:left;">'.$nombre_producto.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; border-bottom:1px #000 solid; text-align:center;">'.$valor->MARCC_Descripcion.'</td>
        		</tr>';
        	}

        	$comprobanteRelacionado = ($numero_ref != '') ? '<tr>
        	<td style="width:4cm; font-style:italic;">FACTURA / BOLETA</td>
        	<td style="text-align:left;">'.$numero_ref.'</td>
        	</tr>' : '';

        	$tpCliente = ($tipo_oper == "V") ? "DESTINATARIO" : "PROVEEDOR";
        	$clienteHTML = '
        	<table  style="font-size:7pt;" cellpadding="0.05cm" border="0" width="100%">
        	<tr bgcolor="#007ce6" >
        	<td style="font-weight:bold; text-align: left; color: white;">'.$tpCliente.'</td>
        	</tr>
        	<tr>
        	<td style="width:4cm; text-indent:1cm; font-weight: bold;">RUC:</td>
        	<td style="text-indent:0.1cm;">'.$ruc.'</td>
        	</tr>
        	<tr>
        	<td style="width:4cm; text-indent:1cm; font-weight: bold;">DENOMINACIÓN:</td>
        	<td style="text-indent:0.1cm; text-align:justification">'.$nombres.'</td>
        	</tr>
        	</table>

        	<table style="font-size:7pt;" border="0" cellpadding="0.05cm">
        	<tr bgcolor="#007ce6">
        	<td style="font-weight:bold; text-align: left; color: white;">DATOS DEL TRASLADO</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">FECHA EMISION</td>
        	<td style="text-align:left;">'.$fecha.'</td>
        	</tr>

        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">FECHA INICIO DE TRASLADO</td>
        	<td style="text-align:left;">'.$fecha_traslado.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">MOTIVO DEL TRASLADO:</td>
        	<td style="text-align:left;">'.$movimiento_descripcion.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">MODALIDAD DE TRANSPORTE:</td>
        	<td style="text-align:left;">'.$modalidad_transporte.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">PESO BRUTO TOTAL (KGM):</td>
        	<td style="text-align:left;">'.$peso_total.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">NÚMERO DE BULTOS:</td>
        	<td style="text-align:left;">'.$num_bultos.'</td>
        	</tr>

        	</table>

        	<table style="font-size:7pt;" border="0" cellpadding="0.05cm">
        	<tr bgcolor="#007ce6">
        	<td style="font-weight:bold; text-align: left; color: white;" colspan>DATOS DEL TRASLADO</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">PUNTO DE PARTIDA:</td>
        	<td style="text-align:left;">'.$punto_partida.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">PUNTO DE LLEGADA:</td>
        	<td style="text-align:left;">'.$punto_llegada.'</td>
        	</tr>
        	</table>

        	<table style="font-size:7pt;" border="0" cellpadding="0.05cm">
        	<tr bgcolor="#007ce6">
        	<td style="font-weight:bold; text-align: left; color: white;">DATOS DEL TRANSPORTE</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">TRANSPORTISTA:</td>
        	<td style="text-align:left;">'.$ruc_empresa_transporte.' '.strtoupper($nombre_empresa_transporte).'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">VEHICULO:</td>
        	<td style="text-align:left;">Marca: '.$marca.' Placa: '.$placa.' Registro MTC: '.$registro_mtc.'</td>
        	</tr>
        	<tr>
        	<td style="width:6cm; font-weight: bold; text-indent:1cm;">CONDUCTOR:</td>
        	<td style="text-align:left;">'.$nombre_conductor.' Licencia: '.$licencia.'</td>
        	</tr>

        	</table>

        	';
        	$this->pdf->writeHTML($clienteHTML,true,false,true,'');

        	$productoHTML = '<table cellpadding="0.1cm" style="font-size:8pt;">
        	<tr bgcolor="#007ce6" style="font-size:8pt;">
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; color: #ffffff; text-align:center; width:10%;"> CANTIDAD</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; color: #ffffff; text-align:center; width:10%;"> CÓDIGO</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; color: #ffffff; text-align:center; width:10%;"> UND.</td>
        	<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; color: #ffffff; text-align:left; width:50%;">DESCRIPCIÓN</td>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:italic; font-weight:bold; color: #ffffff; text-align:center; width:20%;">MARCA</th>
        	</tr>
        	'.$detaProductos.'
        	</table>';
        	$this->pdf->writeHTML($productoHTML,true,false,true,'');
        	$adicionalHTML = '<table border="0" style="width:19.5cm; font-size:7pt;" cellpadding="0.1cm">
        	<tr>
        	<td style="font-weight:bold; text-align:justification;">Observaciones: <span style="font-style:italic; font-weight:normal;">'.$observacion.'</span></td>
        	</tr>
        	'.$comprobanteRelacionado.'
        	</table>';
        	$this->pdf->writeHTML($adicionalHTML,true,false,true,'');    
        	$nameFile = "G. Remision -".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero).".pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

        public function despacho_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

        	$datos_despacho = $this->ci->producto_model->obtener_despacho($codigo);
        	$detalles_despacho = $this->ci->producto_model->obtener_detalles_despacho($codigo);

        	$numero = $datos_despacho[0]->DESC_Numero;
        	$serie = $datos_despacho[0]->DESC_Serie;
        	$compania = $datos_despacho[0]->COMPP_Codigo;

        	$estado = $datos_despacho[0]->DESC_FlagEstado;
        	$nFechaEntrega = explode( '-', $datos_despacho[0]->DESC_FechaDespacho );
        	$fecha_entrega = $nFechaEntrega[2]." DE ".$this->mesesEs($nFechaEntrega[1])." DEL ".$nFechaEntrega[0];

        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(7, 50, 10); 
        	$this->pdf->SetTitle('DESPACHO '.$serie.'-'.$numero);
        	$this->pdf->SetFont('times', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(false);

        	/* Listado de detalles */
        	$detaGuiasRem = "";
        	$detaGuiasTrans = "";
        	$i = 1;
        	$j = 1;
        	foreach ($detalles_despacho as $indice => $valor) {

        		if ( $valor->GUIAREMP_Codigo != NULL ){
        			$bgcolor = ( $i % 2 == 0 ) ? "#F1F1F1" : "#FFFFFF";
        			$detaGuiasRem = $detaGuiasRem. '
        			<tr bgcolor="'.$bgcolor.'">
        			<td style="text-align:center;">'.$i.'</td>
        			<td style="text-align:center;">'.$valor->emisorGuiaRem.'</td>
        			<td style="text-align:center;">'.$valor->GUIAREMC_Serie.'</td>
        			<td style="text-align:center;">'.$valor->GUIAREMC_Numero.'</td>
        			<td style="text-align:center;">'.mysql_to_human($valor->GUIAREMC_FechaTraslado).'</td>
        			<td style="text-align:justification;">'.$valor->GUIAREMC_PuntoLlegada.'</td>
        			</tr>';
        			$i++;
        		}

        		if ( $valor->GTRANP_Codigo != NULL ){
        			$bgcolor = ( $j % 2 == 0 ) ? "#F1F1F1" : "#FFFFFF";
        			$detaGuiasTrans = $detaGuiasTrans. '
        			<tr bgcolor="'.$bgcolor.'">
        			<td style="text-align:center;">'.$i.'</td>
        			<td style="text-align:center;">'.$valor->emisorGuiaTrans.'</td>
        			<td style="text-align:center;">'.$valor->GTRANC_Serie.'</td>
        			<td style="text-align:center;">'.$valor->GTRANC_Numero.'</td>
        			<td style="text-align:center;">'.mysql_to_human($valor->GTRANC_Fecha).'</td>
        			<td style="text-align:justification;">'.$valor->almacenDestino.'</td>
        			</tr>';
        			$j++;
        		}
        	}

        	$comppName = $this->ci->pedido_model->nameEstablecimiento($compania);
        	$compp = $comppName[0]->EESTABC_Descripcion;

        	$this->pdf->settingHeaderData(NULL, $comppName[0]->EMPRC_RazonSocial."<br>ORDEN DE DESPACHO", $serie, $this->getOrderNumeroSerie($numero) );
        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	$despachoHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="0">
        	<tr>
        	<td style="width:3.5cm; font-weight:bold;">EMITIDO POR:</td>
        	<td style="width:auto; text-indent:0.1cm;">'.$comppName[0]->EMPRC_RazonSocial.'</td>
        	</tr>
        	<tr>
        	<td style="width:3.5cm; font-weight:bold;">FECHA DE DESPACHO:</td>
        	<td style="width:5cm; text-indent:0.1cm; text-align:justification">'.$fecha_entrega.'</td>
        	</tr>
        	</table>';

        	$this->pdf->writeHTML($despachoHTML,true,false,true,'');

        	$guiasRemHTML = '
        	<table cellpadding="0.05cm" style="font-size:8pt;">
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt; font-weight:bold">
        	<td colspan="6" style="width: 19cm">GUIAS DE REMISIÓN</td>
        	</tr>
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1cm;">ITEM</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:3cm;">EMISOR</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1cm;">SERIE</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1.5cm;">NÚMERO</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:2cm;">FECHA</th>
        	<th style="font-style:normal; font-weight:bold; text-align:justification; width:10.5cm;">DESTINO</th>
        	</tr>
        	'.$detaGuiasRem.'
        	</table>';
        	$this->pdf->writeHTML($guiasRemHTML,true,false,true,'');

        	$guiasTransHTML = '
        	<table cellpadding="0.05cm" style="font-size:8pt;">
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt; font-weight:bold">
        	<td colspan="6" style="width: 19cm">GUIAS DE TRANSFERENCIA</td>
        	</tr>
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1cm;">ITEM</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:3cm;">EMISOR</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1cm;">SERIE</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1.5cm;">NÚMERO</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:2cm;">FECHA</th>
        	<th style="font-style:normal; font-weight:bold; text-align:justification; width:10.5cm;">DESTINO</th>
        	</tr>
        	'.$detaGuiasTrans.'
        	</table>';
        	$this->pdf->writeHTML($guiasTransHTML,true,false,true,'');

        	$nameFile = "Despacho -".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero).".pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

        public function produccion_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

        	$filter = new stdClass();
        	$filter->codigo = $codigo;
        	$datos_produccion = $this->ci->producto_model->obtenerProduccion($filter);

        	$numero = $datos_produccion[0]->PR_Numero;
        	$serie = $datos_produccion[0]->PR_Serie;

        	$estadoTerminado = $datos_produccion[0]->PR_FlagEstado;
        	$estado = $datos_produccion[0]->PR_FlagEstado;

        	$pedido = $datos_produccion[0]->PEDIP_Codigo;

        	if ($pedido > 0){
        		$serNumPedido = explode("-", $datos_produccion[0]->serieNumeroPedido);
        		$seriePedido = $serNumPedido[0];
        		$numeroPedido = $this->getOrderNumeroSerie($serNumPedido[1]);
        	}
        	else{
        		$seriePedido = "";
        		$numeroPedido = "";
        	}

        	$fecha = explode("-", $datos_produccion[0]->PR_FechaRecepcion);
        	$fecha_recepcion = strtolower($fecha[2]." DE ".$this->mesesEs($fecha[1])." DEL ".$fecha[0]);

        	$fecha = explode("-", $datos_produccion[0]->PR_FechaFinalizado);
        	$fecha_entrega = strtolower($fecha[2]." DE ".$this->mesesEs($fecha[1])." DEL ".$fecha[0]);

        	if ($datos_produccion[0]->compania != '' && $datos_produccion[0]->compania != NULL ){
        		$compania = $datos_produccion[0]->compania;
        		$comppName = $this->ci->pedido_model->nameEstablecimiento($compania);
        		$razonSocial = $comppName[0]->EMPRC_RazonSocial . " - " . $comppName[0]->EESTABC_Descripcion;
        	}
        	else
        		$razonSocial = "";

        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(7, 50, 10); 
        	$this->pdf->SetTitle('PRODUCCIÓN '.$serie.'-'.$numero);
        	$this->pdf->SetFont('times', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(false);

        	$this->pdf->settingHeaderData(NULL, "<br>ORDEN DE PRODUCCIÓN", $serie, $this->getOrderNumeroSerie($numero) );
        	$this->pdf->AddPage();
        	$this->pdf->SetAutoPageBreak(true, 1);

        	/* Listado de detalles */
        	$datos_produccion = $this->ci->producto_model->detallesProduccion($codigo);
        	$detaProductos = "";
        	$j = 1;
        	foreach ($datos_produccion as $indice => $valor) {
        		$listaProductos = $this->ci->producto_model->obtener_producto($valor->PROD_Codigo);
        		$unidadMedida = $this->ci->unidadmedida_model->obtener($valor->UNDMED_Codigo);
        		$medidaDetalle = "";
        		$medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

        		$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

        		$detaProductos = $detaProductos. '
        		<tr bgcolor="'.$bgcolor.'">
        		<td style="text-align:center;">'.$j.'</td>
        		<td style="text-align:center;">'.$listaProductos[0]->PROD_CodigoUsuario.'</td>
        		<td style="text-align:left;">'.$listaProductos[0]->PROD_Nombre.'</td>
        		<td style="text-align:center;">'.$medidaDetalle.'</td>
        		<td style="text-align:right;">'.$valor->PRD_Cantidad.'</td>
        		</tr>';
        		$j++;
        	}

        	$clienteHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="0">
        	<tr>
        	<td style="width:2.5cm; font-style:italic; font-weight:bold;">Fecha elaboración:</td>
        	<td style="width:5cm; text-indent:0.1cm; text-align:justification">'.$fecha_recepcion.'</td>

        	<td style="width:2.5cm; font-style:italic; font-weight:bold;">Dirigido a:</td>
        	<td style="width:auto; text-indent:0.1cm;">'.$razonSocial.'</td>
        	</tr>
        	<tr>
        	<td style="width:2.5cm; font-style:italic; font-weight:bold;">Fecha culminado:</td>
        	<td style="width:5cm; text-indent:0.1cm; text-align:justification">'.$fecha_entrega.'</td>

        	<td style="width:2.5cm; font-style:italic; font-weight:bold;">Pedido asociado:</td>
        	<td style="width:5cm; text-indent:0.1cm; text-align:justification">'.$seriePedido.' - '.$numeroPedido.' </td>
        	</tr>
        	</table>';

        	$this->pdf->writeHTML($clienteHTML,true,false,true,'');

        	$productoHTML = '
        	<table cellpadding="0.05cm" style="font-size:8pt;">
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1.5cm;">Item</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:2.5cm;">Código</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:9.5cm;">Descripción</th>
        	<th style="font-style:normal; font-weight:bold; text-align:right; width:3.6cm;">Unidad de medida</th>
        	<th style="font-style:normal; font-weight:bold; text-align:right; width:1.8cm;">Cantidad</th>
        	</tr>
        	'.$detaProductos.'
        	</table>';
        	$this->pdf->writeHTML($productoHTML,true,false,true,'');

        	$nameFile = "Produccion -".$this->getOrderNumeroSerie($serie)."-".$this->getOrderNumeroSerie($numero).".pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

        public function receta_pdf($codigo, $flagPdf = 1, $enviarcorreo = false){

        	$filter = new stdClass();
        	$filter->codigo = $codigo;

        	$datos_receta = $this->ci->producto_model->listarRecetas($filter);
        	$nombre_receta = $datos_receta[0]->REC_Descripcion;

        	$producto = $this->ci->producto_model->obtener_producto( $datos_receta[0]->PROD_Codigo );


        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 40, 10); 
        	$this->pdf->SetTitle("RECETA - $nombre_receta");
        	$this->pdf->SetFont('times', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(false);

        	$this->pdf->SetAutoPageBreak(true, 20);
        	$this->pdf->AddPage();

        	/* Listado de detalles */
        	$datos_produccion = $this->ci->producto_model->detallesReceta($codigo);
        	$detaProductos = "";
        	$j = 1;
        	foreach ($datos_produccion as $indice => $valor) {
        		$listaProductos = $this->ci->producto_model->obtener_producto($valor->PROD_Codigo);
        		$unidadMedida = $this->ci->unidadmedida_model->obtener($valor->UNDMED_Codigo);
        		$medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

        		$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";
        		$detaProductos = $detaProductos. '
        		<tr bgcolor="'.$bgcolor.'">
        		<td style="text-align:center;">'.$j.'</td>
        		<td style="text-align:center;">'.$listaProductos[0]->PROD_CodigoUsuario.'</td>
        		<td style="text-align:left;">'.$listaProductos[0]->PROD_Nombre.'</td>
        		<td style="text-align:center;">'.$medidaDetalle.'</td>
        		<td style="text-align:right;">'.$valor->RECDET_Cantidad.'</td>
        		</tr>';
        		$j++;
        	}


        	$recetaHTML = '
        	<table style="text-align:left;" border="0" cellpadding="0" cellspacing="0">
        	<tr>
        	<td style="width:12cm;"></td>
        	<td style="width:4.5cm; font-weight:normal; font-style:italic; text-align:right; font-size:12pt;">RECETA</td>
        	<td style="width:3.5cm; font-weight:bold; font-size:14pt; color:#000;"></td>
        	</tr>
        	</table>
        	';
        	$this->pdf->writeHTML($recetaHTML,false,false,true,'');

        	$recetaHTML = '<table style="font-size:8.5pt;" cellpadding="0.1cm" border="0">
        	<tr>
        	<td style="width:3.5cm; font-style:normal; font-weight:bold;">Descripción de la Receta:</td>
        	<td style="width:15cm; text-indent:0.1cm; text-align:justification">'.$nombre_receta.'</td>
        	</tr>
        	</table>';

        	$this->pdf->writeHTML($recetaHTML,true,false,true,'');

        	$productoHTML = '
        	<table cellpadding="0.05cm">
        	<tr style="font-size:8.5pt;">
        	<th colspan="5" style="font-style:normal; font-weight:bold; text-align:left; border-bottom: 1px #000 solid;">Productos incluidos en la receta.</th>
        	</tr>
        	<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:1.5cm;">Item</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:2.5cm;">Código</th>
        	<th style="font-style:normal; font-weight:bold; text-align:center; width:9.5cm;">Descripción</th>
        	<th style="font-style:normal; font-weight:bold; text-align:right; width:3.6cm;">Unidad de medida</th>
        	<th style="font-style:normal; font-weight:bold; text-align:right; width:1.8cm;">Cantidad</th>
        	</tr>
        	'.$detaProductos.'
        	</table>';
        	$this->pdf->writeHTML($productoHTML,true,false,true,'');

        	$nameFile = "Receta - $nombre_receta.pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

        public function ventas_pdf_productos($fech1, $fech2, $tipodocumento, $codigo, $flagPdf = 1, $enviarcorreo = false){

        #$producto = $this->ci->producto_model->obtener_producto( $codigo );

        	$medidas = "a4"; 
        	$this->pdf = new pdfGeneral('P', 'mm', $medidas, true, 'UTF-8', false);
        	$this->pdf->SetMargins(10, 40, 10); 
        	$this->pdf->SetTitle("Ventas por producto");
        	$this->pdf->SetFont('times', '', 8);
        	if ($flagPdf == 1)
        		$this->pdf->setPrintHeader(true);
        	else
        		$this->pdf->setPrintHeader(false);

        	$this->pdf->setPrintFooter(false);

        	$this->pdf->SetAutoPageBreak(true, 20);
        	$this->pdf->AddPage();

        	/* Listado de detalles */
        	$listado = $this->ci->comprobante_model->buscar_comprobante_producto($fech1, $fech2, $tipodocumento, $codigo);
        	$detaProductos = "";
        	$j = 1;
        	foreach ($listado as $indice => $valor) {
        		$bgcolor = ( $indice % 2 == 0 ) ? "#FFFFFF" : "#F1F1F1";

        		switch ( $valor->CPC_TipoDocumento ) {
        			case 'F':
        			$tipodocumento = "FACTURA";
        			break;
        			case 'B':
        			$tipodocumento = "BOLETA";
        			break;
        			case 'N':
        			$tipodocumento = "COMPROBANTE";
        			break;

        			default:
        			$tipodocumento = "COMPROBANTE";
        			break;
        		}

        		$detaProductos = $detaProductos. '
        		<tr bgcolor="'.$bgcolor.'">
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$j.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CPC_Fecha.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.$tipodocumento.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:center;">'.$valor->CPC_Serie.'-'.$this->getOrderNumeroSerie($valor->CPC_Numero).'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:left;">'.$valor->nombre.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.$valor->CPDEC_Cantidad.'</td>
        		<td style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; text-align:right;">'.$valor->CPDEC_Total.'</td>
        		</tr>';
        		$j++;
        	}


        	$detalleHTML = '
        	<table style="text-align:left;" border="0" cellpadding="0" cellspacing="0">
        	<tr>
        	<td style="width:12cm;"></td>
        	<td style="width:4.5cm; font-weight:normal; font-style:italic; text-align:right; font-size:12pt;">Ventas por producto</td>
        	<td style="width:3.5cm; font-weight:bold; font-size:14pt; color:#000;"></td>
        	</tr>
        	</table>
        	';
        	$this->pdf->writeHTML($detalleHTML,false,false,true,'');

        	$detalleHTML = '<table style="font-size:7.5pt;" cellpadding="0.1cm" border="0">
        	<tr>
        	<td style="width:4.5cm; font-style:normal; font-weight:bold;">DESCRIPCIÓN DEL PRODUCTO:</td>
        	<td style="width:15cm; text-indent:0.1cm; text-align:justification">'.$listado[0]->PROD_Nombre.' - '.$listado[0]->MARCC_Descripcion.'</td>
        	</tr>
        	<tr>
        	<td style="width:4.5cm; font-style:normal; font-weight:bold;">FECHA INICIO:</td>
        	<td style="width:15cm; text-indent:0.1cm; text-align:justification">'.mysql_to_human($fech1).'</td>
        	</tr>
        	<tr>
        	<td style="width:4.5cm; font-style:normal; font-weight:bold;">FECHA FINAL:</td>
        	<td style="width:15cm; text-indent:0.1cm; text-align:justification">'.mysql_to_human($fech2).'</td>
        	</tr>
        	</table>';

        	$this->pdf->writeHTML($detalleHTML,true,false,true,'');

        	$productoHTML = '
        	<table cellpadding="0.05cm">
        	<tr style="font-size:8pt;">
        	<th colspan="8" style="font-style:normal; font-weight:bold; text-align:left; border-bottom: 1px #000 solid;">DOCUMENTOS EMITIDOS</th>
        	</tr>
        	<tr bgcolor="#F1F1F1" style="font-size:7.5pt;">
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:1.0cm;">ITEM</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:2.0cm;">FECHA</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:3.0cm;">DOCUMENTO</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:2.5cm;">SERIE/NÚMERO</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:center; width:7.0cm;">RAZÓN SOCIAL</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:right; width:2.0cm;">CANTIDAD</th>
        	<th style="border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid; font-style:normal; font-weight:bold; text-align:right; width:1.5cm;">TOTAL</th>
        	</tr>
        	'.$detaProductos.'
        	</table>';
        	$this->pdf->writeHTML($productoHTML,true,false,true,'');

        	$nameFile = "Ventas por producto.pdf";

        	if ($enviarcorreo == false)
        		$this->pdf->Output($nameFile, 'I');
        	else
        		return $this->pdf->Output($nameFile, 'S');
        }

## FORMATOS EN EXCEL
#########################################################################

        public function ocompra_descarga_excel($codigo){

        	$this->ci->load->library('Excel');

        	$datos_ocompra = $this->ci->ocompra_model->obtener_ocompra($codigo);
        	$datos_detalle_ocompra = $this->ci->ocompra_model->obtener_detalle_ocompra($codigo);
        	$tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
        	$cotizacion = $datos_ocompra[0]->COTIP_Codigo;
        	$pedido = $datos_ocompra[0]->PEDIP_Codigo;
        	$serie = $datos_ocompra[0]->OCOMC_Serie;
        	$numero = $datos_ocompra[0]->OCOMC_Numero;
        	$descuento100 = $datos_ocompra[0]->OCOMC_descuento100;
        	$descuento = $datos_ocompra[0]->OCOMC_descuento;
        	$igv100 = $datos_ocompra[0]->OCOMC_igv100;
        	$igv = $datos_ocompra[0]->OCOMC_igv;
        	$cliente = $datos_ocompra[0]->CLIP_Codigo;
        	$proveedor = $datos_ocompra[0]->PROVP_Codigo;
        	$centro_costo = $datos_ocompra[0]->CENCOSP_Codigo;
        	$moneda = $datos_ocompra[0]->MONED_Codigo;
        	$subtotal = $datos_ocompra[0]->OCOMC_subtotal;
        	$descuentototal = $datos_ocompra[0]->OCOMC_descuento;
        	$igvtotal = $datos_ocompra[0]->OCOMC_igv;
        	$contacto = $datos_ocompra[0]->OCOMC_Personal;
        	$miPersonal = $datos_ocompra[0]->mipersonal;

        	$docCanje = $datos_ocompra[0]->CPC_TipoDocumento;
        	switch ($docCanje) {
        		case 'F':
        		$docToCanje = "FACTURA";
        		break;
        		case 'B':
        		$docToCanje = "BOLETA";
        		break;
        		case 'N':
        		$docToCanje = "COMPROBANTE";
        		break;

        		default:
        		$docToCanje = "COMPROBANTE";
        		break;
        	}

        $ordenCompraCliente = $datos_ocompra[0]->OCOMC_PersonaAutorizada; // AQUI SE GUARDA EL NUMERO DE ORDEN DE COMPRA DEL CLIENTE EN MONTOYA
        
        $tiempo_entrega = $datos_ocompra[0]->OCOMC_Entrega;
        $total = $datos_ocompra[0]->OCOMC_total;
        $percepcion = $datos_ocompra[0]->OCOMC_percepcion;
        $percepcion100 = $datos_ocompra[0]->OCOMC_percepcion100;
        $observacion = $datos_ocompra[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_ocompra[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_ocompra[0]->OCOMC_FactDireccion;
        
        $datosProyecto = $this->ci->proyecto_model->obtener_datosProyecto( $datos_ocompra[0]->PROYP_Codigo );
        $proyecto = $datosProyecto[0]->PROYC_Nombre;

        $fechaEntrega = ($datos_ocompra[0]->OCOMC_FechaEntrega != '') ? mysql_to_human($datos_ocompra[0]->OCOMC_FechaEntrega) : '';
        $nFechaEntrega = explode( '/', $fechaEntrega );
        $fecha_entrega = $nFechaEntrega[0]." de ".ucfirst( strtolower($this->mesesEs($nFechaEntrega[1])) )." del ".$nFechaEntrega[2];

        $fecha_entrega = ($fechaEntrega != "") ? $fecha_entrega : "";

        $almacen = $datos_ocompra[0]->ALMAP_Codigo;
        $formapago = $datos_ocompra[0]->FORPAP_Codigo;
        $ctactesoles = $datos_ocompra[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_ocompra[0]->OCOMC_CtaCteDolares;
        $tdc = $datos_ocompra[0]->OCOMP_TDC;

        $nombre_almacen = '';
        if ($almacen != '') {
        	$datos_almacen = $this->ci->almacen_model->obtener($almacen);
        	$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
        }
        $nombre_formapago = '';
        if ($formapago != '') {
        	$datos_formapago = $this->ci->formapago_model->obtener($formapago);
        	$nombre_formapago = $datos_formapago[0]->FORPAC_Descripcion;
        }

        $datos_moneda = $this->ci->moneda_model->obtener($moneda);
        $simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'Soles');

        $arrfecha = explode(" ", $datos_ocompra[0]->OCOMC_FechaRegistro);
        $nFecha = explode('/', mysql_to_human($arrfecha[0]) );
        $fecha = $nFecha[0]." de ".ucfirst( strtolower($this->mesesEs($nFecha[1])) )." del ".$nFecha[2]." a las ".$this->formatHours($arrfecha[1]); 
        $flagIngreso = $datos_ocompra[0]->OCOMC_FlagIngreso;

        $idCliente = "";
        if ($tipo_oper == 'C') {
        	$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        	$nombres = $datos_proveedor->nombre;
        	$ruc = $datos_proveedor->ruc;
        	$telefono = $datos_proveedor->telefono;
        	$direccion = $datos_proveedor->direccion;
        	$fax = $datos_proveedor->fax;
        } else {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	$idCliente = $datos_cliente->idCliente;
        	$nombres = $datos_cliente->nombre;
        	$ruc = $datos_cliente->ruc;
        	$telefono = $datos_cliente->telefono;
        	$direccion = $datos_cliente->direccion;
        	$fax = $datos_cliente->fax;
        }

        $contacto = $this->ci->persona_model->obtener_datosPersona($contacto);

        $companiaInfo = $this->ci->compania_model->obtener($datos_ocompra[0]->COMPP_Codigo);
        $establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        $empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        $tipoDocumento = "COTIZACIÓN<br>ELECTRÓNICA";
        $tipoDocumentoF = "COTIZACION ELECTRÓNICA";
        
        $hoja = 0;

        ###########################################
        ######### ESTILOS
        ###########################################
        $estiloTitulo = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => true,
        		'color'     => array(
        			'rgb' => '000000'
        		),
        		'size' => 14
        	),
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	)
        );

        $estiloColumnasTitulo = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => true,
        		'color'     => array(
        			'rgb' => '000000'
        		),
        		'size' => 11
        	),
        	'fill'  => array(
        		'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('argb' => 'ECF0F1')
        	),
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	)
        );

        $estiloColumnasPar = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => false,
        		'color'     => array(
        			'rgb' => '000000'
        		)
        	),
        	'fill'  => array(
        		'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('argb' => 'FFFFFFFF')
        	),
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	),
        	'borders' => array(
        		'allborders' => array(
        			'style' => PHPExcel_Style_Border::BORDER_THIN,
        			'color' => array( 'rgb' => "000000")
        		)
        	)
        );

        $estiloColumnasImpar = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => false,
        		'color'     => array(
        			'rgb' => '000000'
        		)
        	),
        	'fill'  => array(
        		'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('argb' => 'DCDCDCDC')
        	),
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	),
        	'borders' => array(
        		'allborders' => array(
        			'style' => PHPExcel_Style_Border::BORDER_THIN,
        			'color' => array( 'rgb' => "000000")
        		)
        	)
        );
        $estiloBold = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => true,
        		'color'     => array(
        			'rgb' => '000000'
        		),
        		'size' => 11
        	)
        );
        $estiloCenter = array(
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	)
        );
        $estiloRight = array(
        	'alignment' =>  array(
        		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        		'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        		'wrap'          => TRUE
        	)
        );

            # ROJO PARA ANULADOS
        $colorCelda = array(
        	'font' => array(
        		'name'      => 'Calibri',
        		'bold'      => false,
        		'color'     => array(
        			'rgb' => '000000'
        		)
        	),
        	'fill'  => array(
        		'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        		'color' => array('argb' => "F28A8C")
        	)
        );

        ###########################################################################
        ###### HOJA 0 COTIZACIÓN
        ###########################################################################

        $this->ci->excel->setActiveSheetIndex($hoja);
        $this->ci->excel->getActiveSheet()->setTitle("COTIZACION $serie - $numero");

        for ($i = "A"; $i <= "L"; $i++)
        	$this->ci->excel->getActiveSheet()->getColumnDimension($i)->setWidth("10");

        $this->ci->excel->getActiveSheet()->getColumnDimension("C")->setWidth("25");


        $this->ci->excel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTitulo);

            ##############################################################
            ############# INFO CLIENTE
            ##############################################################

        $lugar = 7;
        $tpCliente = ($tipo_oper == "V") ? "CLIENTE: " : "PROVEEDOR ";

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRIGIDO AL $tpCliente ")
        ->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "$idCliente")
        ->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar",  "COTIZACIÓN:  $serie-".$this->getOrderNumeroSerie($numero));
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar:F$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RUC:")
        ->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "Nro. $ruc");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RAZÓN SOCIAL:")
        ->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$nombres");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRECCIÓN:")
        ->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$direccion");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "VENDEDOR:")
        ->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$miPersonal");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "TELEFONOS:")
        ->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  $datos_ocompra[0]->PERSC_Telefono.' / '.$datos_ocompra[0]->PERSC_Movil)
        ->setCellValue("E$lugar",  "CORREO:")
        ->setCellValue("F$lugar",  $datos_ocompra[0]->PERSC_Email);
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $this->ci->excel->getActiveSheet()->getStyle("E$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "FECHA DE ELAB.:")
        ->mergeCells("C$lugar:E$lugar")->setCellValue("C$lugar",  "$fecha");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "FECHA DE VENC.:")
        ->mergeCells("C$lugar:E$lugar")->setCellValue("C$lugar",  "$fecha_entrega");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "ORDEN DE COMPRA:")
        ->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "$ordenCompraCliente");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);            
        $lugar += 2;

            ##############################################################
            ############# INFO ARTICULOS
            ##############################################################

        $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar",  "CANTIDAD");
        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar",  "DESCRIPCION");
        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar",  "MARCA");
        $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar",  "V/U");
        $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar",  "P/U");
        $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar",  "TOTAL");
        $this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasTitulo);

        $gravada = 0;
        $exonerado = 0;
        $inafecto = 0;
        $gratuito = 0;
        $lugar++;

        foreach ($datos_detalle_ocompra as $indice => $valor) {
        	$nombre_producto = $valor->OCOMDEC_Descripcion .". ". $valor->OCOMDEC_Observacion;
        	$tipo_afectacion = $valor->AFECT_Codigo;

        	$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

        	$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion);

        	switch ($tipo_afectacion) {
        		case 1: 
        		$gravada += $valor->OCOMDEC_Subtotal;
        		break;
        		case 8: 
        		$exonerado += $valor->OCOMDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 9: 
        		$inafecto += $valor->OCOMDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		case 16:
        		$inafecto += $valor->OCOMDEC_Subtotal;
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		break;
        		default:
        		$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        		$gratuito += $valor->OCOMDEC_Subtotal;
        		break;
        	}

        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", $valor->OCOMDEC_Cantidad);
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar", $nombre_producto);
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar", $valor->MARCC_Descripcion);
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", $valor->OCOMDEC_Pu);
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", $valor->OCOMDEC_Pu_ConIgv);
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar", $valor->OCOMDEC_Total);

        	if ($indice % 2 == 0)
        		$this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasPar);
        	else
        		$this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasImpar);

        	$lugar++;
        }

                ############################
                ###### TOTALES
                ############################
        $lugar++;
        $lugarIT = $lugar;

        if ( $gratuito > 0 ){
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "GRATUITO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $gratuito,2);
        	$lugar++;
        }

        if ( $exonerado > 0 ){
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "EXONERADO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $exonerado,2);
        	$lugar++;
        }

        if ( $inafecto > 0 ){
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "INAFECTO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $inafecto,2);
        	$lugar++;
        }

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "GRAVADA")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $gravada,2);
        $lugar++;

        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "IGV")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $igv,2);
        $lugar++;
        $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "TOTAL")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $total,2);
        $lugar++;

        $this->ci->excel->getActiveSheet()->getStyle("E$lugarIT:F$lugar")->applyFromArray($estiloBold);
        $this->ci->excel->getActiveSheet()->getStyle("E$lugarIT:F$lugar")->applyFromArray($estiloRight);
        $this->ci->excel->getActiveSheet()->getStyle("G$lugarIT:G$lugar")->applyFromArray($estiloCenter);


        $img = "images/cabeceras/logo_".$datos_ocompra[0]->COMPP_Codigo.".jpg";
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('logo');
        $objDrawing->setPath($img);
            $objDrawing->setOffsetX(0); # setOffsetX works properly
            $objDrawing->setOffsetY(0); # setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(100); # height
            $objDrawing->setWorksheet($this->ci->excel->setActiveSheetIndex($hoja)); 

            $filename = "COTIZACION $serie - $numero.xls";
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment;filename=$filename");
            header("Cache-Control: max-age=0");
            $objWriter = PHPExcel_IOFactory::createWriter($this->ci->excel, 'Excel5');
            $objWriter->save('php://output');
          }

          public function guiarem_descarga_excel($codigo){

          	$this->ci->load->library('Excel');

          	$datos_guiarem = $this->ci->guiarem_model->obtener($codigo);

          	$tipo_movimiento = $datos_guiarem[0]->TIPOMOVP_Codigo;
          	$tipo_oper = $datos_guiarem[0]->GUIAREMC_TipoOperacion;
          	$otro_motivo = $datos_guiarem[0]->GUIAREMC_OtroMotivo;
          	$empresa_transporte = $datos_guiarem[0]->EMPRP_Codigo;
          	$almacen = $datos_guiarem[0]->ALMAP_Codigo;
          	$usuario = $datos_guiarem[0]->USUA_Codigo;
          	$moneda = $datos_guiarem[0]->MONED_Codigo;
          	$referencia = $datos_guiarem[0]->DOCUP_Codigo;
          	$cliente = $datos_guiarem[0]->CLIP_Codigo;
          	$proveedor = $datos_guiarem[0]->PROVP_Codigo;
          	$recepciona_nombres = $datos_guiarem[0]->GUIAREMC_PersReceNombre;
          	$recepciona_dni = $datos_guiarem[0]->GUIAREMC_PersReceDNI;
          	$numero_ref = $datos_guiarem[0]->GUIAREMC_NumeroRef;
          	$numero_ocompra = $datos_guiarem[0]->GUIAREMC_OCompra;
          	$serie = $datos_guiarem[0]->GUIAREMC_Serie;
          	$numero = $datos_guiarem[0]->GUIAREMC_Numero;
          	$numero_ref = $datos_guiarem[0]->GUIAREMC_NumeroRef;
          	$codigo_usuario = $datos_guiarem[0]->GUIAREMC_CodigoUsuario;
          	$fecha_traslado = $datos_guiarem[0]->GUIAREMC_FechaTraslado;
          	$observacion = $datos_guiarem[0]->GUIAREMC_Observacion;
          	$placa = $datos_guiarem[0]->GUIAREMC_Placa;
          	$marca = $datos_guiarem[0]->GUIAREMC_Marca;
          	$registro_mtc = $datos_guiarem[0]->GUIAREMC_RegistroMTC;
          	$certificado = $datos_guiarem[0]->GUIAREMC_Certificado;
          	$licencia = $datos_guiarem[0]->GUIAREMC_Licencia;
          	$nombre_conductor = $datos_guiarem[0]->GUIAREMC_NombreConductor;
          	$ocompra = $datos_guiarem[0]->OCOMP_Codigo;
          	$estado = $datos_guiarem[0]->GUIAREMC_FlagEstado;
          	$punto_partida = $datos_guiarem[0]->GUIAREMC_PuntoPartida;
          	$punto_llegada = $datos_guiarem[0]->GUIAREMC_PuntoLlegada;

          	$tipo_movimiento = $this->ci->tipomovimiento_model->obtener($tipo_movimiento);
          	$movimiento_descripcion = $tipo_movimiento[0]->TIPOMOVC_Descripcion; 

          	$fecha_traslado = mysql_to_human($fecha_traslado);
          	$nFechaEntrega = explode( '/', $fecha_traslado );
          	$fecha_entrega = ($datos_guiarem[0]->GUIAREMC_FechaTraslado == "" || $datos_guiarem[0]->GUIAREMC_FechaTraslado == NULL || $datos_guiarem[0]->GUIAREMC_FechaTraslado == "-") ? "" : $nFechaEntrega[0]." DE ".$this->mesesEs($nFechaEntrega[1])." DEL ".$nFechaEntrega[2];

          	$datos_moneda = $this->ci->moneda_model->obtener($moneda);

          	$nombre_almacen = '';
          	if ($almacen != '') {
          		$datos_almacen = $this->ci->almacen_model->obtener($almacen);
          		$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
          	}

          	$simbolo_moneda = $datos_moneda[0]->MONED_Simbolo;
          	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'SOLES');

          	if ($tipo_oper == 'C') {
          		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
          		$nombres = $datos_proveedor->nombre;
          		$ruc = $datos_proveedor->ruc;
          		$telefono = $datos_proveedor->telefono;
          		$direccion = $datos_proveedor->direccion;
          		$fax = $datos_proveedor->fax;
          	} else {
          		$datos_cliente = $this->ci->cliente_model->obtener($cliente);
          		$nombres = $datos_cliente->nombre;
          		$ruc = $datos_cliente->ruc;
          		$telefono = $datos_cliente->telefono;
          		$direccion = $datos_cliente->direccion;
          		$fax = $datos_cliente->fax;
          	}

          	$companiaInfo = $this->ci->compania_model->obtener($datos_guiarem[0]->COMPP_Codigo);
          	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
          	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

          	$tipoDocumento = "GUIA DE REMISIÓN<br>ELECTRÓNICA";
          	$tipoDocumentoF = "GUIA DE REMISIÓN ELECTRÓNICA";

          	$transporte = $this->ci->empresa_model->obtener_datosEmpresa($empresa_transporte);


        ###########################################
        ######### ESTILOS
        ###########################################
          	$estiloTitulo = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => true,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 12
          		),
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		)
          	);

          	$estiloColumnasTitulo = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => true,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 10
          		),
          		'fill'  => array(
          			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
          			'color' => array('argb' => 'ECF0F1')
          		),
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		)
          	);

          	$estiloColumnasPar = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => false,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 10
          		),
          		'fill'  => array(
          			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
          			'color' => array('argb' => 'FFFFFFFF')
          		),
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		),
          		'borders' => array(
          			'allborders' => array(
          				'style' => PHPExcel_Style_Border::BORDER_THIN,
          				'color' => array( 'rgb' => "000000")
          			)
          		)
          	);

          	$estiloColumnasImpar = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => false,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 10
          		),
          		'fill'  => array(
          			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
          			'color' => array('argb' => 'DCDCDCDC')
          		),
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		),
          		'borders' => array(
          			'allborders' => array(
          				'style' => PHPExcel_Style_Border::BORDER_THIN,
          				'color' => array( 'rgb' => "000000")
          			)
          		)
          	);
          	$estiloBold = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => true,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 10
          		)
          	);
          	$estiloCenter = array(
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		)
          	);
          	$estiloRight = array(
          		'alignment' =>  array(
          			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          			'wrap'          => TRUE
          		)
          	);

            # ROJO PARA ANULADOS
          	$colorCelda = array(
          		'font' => array(
          			'name'      => 'Calibri',
          			'bold'      => false,
          			'color'     => array(
          				'rgb' => '000000'
          			),
          			'size' => 10
          		),
          		'fill'  => array(
          			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
          			'color' => array('argb' => "F28A8C")
          		)
          	);

          	$hoja = 0;
        ###########################################################################
        ###### HOJA 0 COTIZACIÓN
        ###########################################################################

          	$this->ci->excel->setActiveSheetIndex($hoja);
          	$this->ci->excel->getActiveSheet()->setTitle("GUIA DE REMISION $serie - $numero");

          	for ($i = "A"; $i <= "H"; $i++)
          		$this->ci->excel->getActiveSheet()->getColumnDimension($i)->setWidth("11");

          	$this->ci->excel->getActiveSheet()->getColumnDimension("C")->setWidth("25");


          	$this->ci->excel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTitulo);

            ##############################################################
            ############# INFO CLIENTE
            ##############################################################

          	$lugar = 7;
          	$tpCliente = ($tipo_oper == "V") ? "DESTINATARIO: " : "PROVEEDOR ";

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRIGIDO AL $tpCliente ")
          	->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "$idCliente")
          	->mergeCells("E$lugar:G$lugar")->setCellValue("E$lugar",  "GUIA DE REMISIÓN:  $serie-".$this->getOrderNumeroSerie($numero));
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RUC:")
          	->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "Nro. $ruc");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RAZÓN SOCIAL:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$nombres");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRECCIÓN:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$direccion");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar += 2;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:G$lugar")->setCellValue("A$lugar",  "DETALLES DEL TRASLADO");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

                # TRASLADO
          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "MOTIVO DEL TRASLADO:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$movimiento_descripcion");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DOCUMENTO RELACIONADO:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$numero_ref");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "FECHA:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$fecha_entrega");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "PUNTO DE PARTIDA:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$punto_partida");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "PUNTO DE LLEGADA:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "$punto_llegada");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar += 2;

                # TRANSPORTE

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:G$lugar")->setCellValue("A$lugar",  "EMPRESA DE TRANSPORTE");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RUC:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  "Nro. ".$transporte[0]->EMPRC_Ruc);
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DENOMINACIÓN:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  $transporte[0]->EMPRC_RazonSocial);
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar += 2;

                # CONDUCTOR

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:G$lugar")->setCellValue("A$lugar",  "DATOS DEL CONDUCTOR");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "NOMBRE:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  $nombre_conductor);
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "CERTIFICADO:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  $certificado);
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "LICENCIA:")
          	->mergeCells("C$lugar:G$lugar")->setCellValue("C$lugar",  $licencia);
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar += 2;

                # VEHICULO

          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:G$lugar")->setCellValue("A$lugar",  "INFORMACIÓN DEL VEHICULO");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$lugar++;

          	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar",  "MARCA:")
          	->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar",  $marca)
          	->setCellValue("D$lugar",  "PLACA:")
          	->setCellValue("E$lugar",  $placa)
          	->setCellValue("F$lugar",  "MTC:")
          	->setCellValue("G$lugar",  $registro_mtc);

          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
          	$this->ci->excel->getActiveSheet()->getStyle("D$lugar")->applyFromArray($estiloBold);
          	$this->ci->excel->getActiveSheet()->getStyle("F$lugar")->applyFromArray($estiloBold);

          	$lugar += 2;

            ##############################################################
            ############# INFO ARTICULOS
            ##############################################################

          	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar",  "CÓDIGO");
          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar",  "DESCRIPCION");
          	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar",  "MARCA");;
          	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar",  "CANTIDAD");
          	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar",  "UNIDAD MED.");
          	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasTitulo);
          	$lugar++;

          	$detalle_guiarem = $this->ci->guiaremdetalle_model->obtener2($codigo);
          	foreach ($detalle_guiarem as $indice => $valor) {
          		$detacodi = $valor->GUIAREMDETP_Codigo;
          		$producto = $valor->PRODCTOP_Codigo;
          		$unidad = $valor->UNDMED_Codigo;
          		$cantidad = $valor->GUIAREMDETC_Cantidad;
          		$pu = $valor->GUIAREMDETC_Pu;
          		$subtotal = $valor->GUIAREMDETC_Subtotal;
          		$igv = $valor->GUIAREMDETC_Igv;
          		$descuento = $valor->GUIAREMDETC_Descuento;
          		$total = $valor->GUIAREMDETC_Total;
          		$pu_conigv = $valor->GUIAREMDETC_Pu_ConIgv;
          		$costo = $valor->GUIAREMDETC_Costo;
          		$venta = $valor->GUIAREMDETC_Venta;
          		$peso = $valor->GUIAREMDETC_Peso;
          		$GenInd = $valor->GUIAREMDETC_GenInd;

          		$codigo_usuario = $valor->PROD_CodigoUsuario;
          		$nombre_producto = ($valor->GUIAREMDETC_Descripcion != NULL) ? "$valor->GUIAREMDETC_Descripcion. $valor->GUIAREMDETC_Observacion" : $valor->PROD_Nombre.". $valor->GUIAREMDETC_Observacion";
          		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";

          		$tipo_afectacion = $valor->AFECT_Codigo;
          		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion);

          		$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", $codigo_usuario);
          		$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar", $nombre_producto);
          		$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar", $valor->MARCC_Descripcion);
          		$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", $cantidad);
          		$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", $medidaDetalle);

          		if ($indice % 2 == 0)
          			$this->ci->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasPar);
          		else
          			$this->ci->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasImpar);

          		$lugar++;
          	}

          	$img = "images/cabeceras/logo_".$datos_guiarem[0]->COMPP_Codigo.".jpg";
          	$objDrawing = new PHPExcel_Worksheet_Drawing();
          	$objDrawing->setName('Logo');
          	$objDrawing->setDescription('logo');
          	$objDrawing->setPath($img);
            $objDrawing->setOffsetX(0); # setOffsetX works properly
            $objDrawing->setOffsetY(0); # setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(100); # height
            $objDrawing->setWorksheet($this->ci->excel->setActiveSheetIndex($hoja)); 

            $filename = "GUIA DE REMISION $serie - $numero.xls";
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment;filename=$filename");
            header("Cache-Control: max-age=0");
            $objWriter = PHPExcel_IOFactory::createWriter($this->ci->excel, 'Excel5');
            $objWriter->save('php://output');
          }

          public function comprobante_descarga_excel($codigo){

          	$this->ci->load->library('Excel');

          	$datos_comprobante = $this->ci->comprobante_model->obtener_comprobante($codigo);

        // DATOS DEL COMPROBANTE
          	$companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
          	$presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
          	$tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
          	$serie = $datos_comprobante[0]->CPC_Serie;
          	$numero = $this->getOrderNumeroSerie($datos_comprobante[0]->CPC_Numero);
          	$descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
          	$descuento100 = $datos_comprobante[0]->CPC_descuento100;
          	$descuento = $datos_comprobante[0]->CPC_descuento;
          	$igv = $datos_comprobante[0]->CPC_igv;
          	$igv100 = $datos_comprobante[0]->CPC_igv100;
          	$subtotal = $datos_comprobante[0]->CPC_subtotal;
          	$subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
          	$total = $datos_comprobante[0]->CPC_total;
          	$observacion = $datos_comprobante[0]->CPC_Observacion;
          	$fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
          	$tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
          	$estado = $datos_comprobante[0]->CPC_FlagEstado;

          	$datos_moneda = $this->ci->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
          	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
          	$simbolo_moneda = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        #CONSULTO LA COTIZACION Y LA ORDEN DE COMPRA
          	$serieOC = ($datos_comprobante[0]->OCOMC_Serie != NULL) ? $datos_comprobante[0]->OCOMC_Serie."-".$this->getOrderNumeroSerie($datos_comprobante[0]->OCOMC_Numero) : "";

          	$ordenCompraCliente = "";
          	if ( $datos_comprobante[0]->ordenCompra != NULL )
          		$ordenCompraCliente = $datos_comprobante[0]->ordenCompra;

          	if ( $datos_comprobante[0]->CPP_Compracliente != NULL ){
          		if ( $ordenCompraCliente != "" ) 
          			$ordenCompraCliente = $ordenCompraCliente . " | ";

          		$ordenCompraCliente .= $datos_comprobante[0]->CPP_Compracliente;
          	}

        // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
          	$consulta_guia = $this->ci->comprobante_model->buscar_guiarem_comprobante($codigo);
          	$guiaRemision = "";
          	foreach ($consulta_guia as $key => $value) {
          		$guiaRemision = ($guiaRemision == "") ? $guiaRemision."$value->GUIAREMC_Serie - $value->GUIAREMC_Numero" : $guiaRemision." | $value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
          	}

        # FORMA DE PAGO
          	$formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
          	$datos_formapago = $this->ci->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS


        # DATOS DEL USUARIO
        $vendedor = ( strlen($datos_comprobante[0]->vendedor) > 20 ) ? substr($datos_comprobante[0]->vendedor, 0, 20) : $datos_comprobante[0]->vendedor;
        #$vendedor = $datos_comprobante[0]->USUA_Codigo;
        #$temp = $this->usuario_model->obtener($vendedor);
        #$temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        #$vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;
        
        # DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        $idCliente = "";
        if ($cliente != '' && $cliente != '0') {
        	$datos_cliente = $this->ci->cliente_model->obtener($cliente);
        	if ($datos_cliente) {
        		$idCliente = $datos_cliente->idCliente;
        		$nombre_cliente = $datos_cliente->nombre;
        		$ruc_cliente = $datos_cliente->ruc;
        		$dni_cliente = $datos_cliente->dni;
        		$ruc = ( $ruc_cliente == NULL || $ruc_cliente == "" || $ruc_cliente == 0 ) ? $dni_cliente : $ruc_cliente;
        		$direccion   = $datos_cliente->direccion;
        		$email   = $datos_cliente->correo;
        	}
        	$tp = "CLIENTE";
        }
        else
        	if ($proveedor != '' && $proveedor != '0') {
        		$datos_proveedor = $this->ci->proveedor_model->obtener($proveedor);
        		if ($datos_proveedor) {
        			$nombre_cliente = $datos_proveedor->nombre;
        			$ruc = $datos_proveedor->ruc;
        			$direccion   = $datos_proveedor->direccion;
        		}
        		$tp = "PROVEEDOR";
        	}


        	$detalle_comprobante = $this->ci->comprobantedetalle_model->detalles($codigo);

        	$companiaInfo = $this->ci->compania_model->obtener($companiaComprobante);
        	$establecimientoInfo = $this->ci->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
        	$empresaInfo =  $this->ci->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

        	$tipoDocumento = "";
        	switch ($tipo_docu) {
        		case 'F':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "FACTURA DE VENTA<br>ELECTRÓNICA" :  "FACTURA DE COMPRA<br>ELECTRÓNICA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA ELECTRÓNICA DE VENTA" :  "FACTURA ELECTRÓNICA DE COMPRA";
        		break;
        		case 'B':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "BOLETA DE VENTA<br>ELECTRÓNICA" :  "BOLETA DE COMPRA<br>ELECTRÓNICA";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA ELECTRÓNICA DE VENTA" :  "BOLETA ELECTRÓNICA DE COMPRA";
        		break;
        		case 'N':
        		$tipoDocumento = ($tipo_oper == 'V') ?  "COMPROBANTE DE VENTA<br>ELECTRÓNICO" :  "COMPROBANTE DE COMPRA<br>ELECTRÓNICO";
        		$tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE ELECTRÓNICO DE VENTA" :  "COMPROBANTE ELECTRÓNICO DE COMPRA";
        		break;
        	}

        	$gravada = 0;
        	$exonerado = 0;
        	$inafecto = 0;
        	$gratuito = 0;
        	$importeBolsa = 0;

        	$hoja = 0;

        ###########################################
        ######### ESTILOS
        ###########################################
        	$estiloTitulo = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => true,
        			'color'     => array(
        				'rgb' => '000000'
        			),
        			'size' => 14
        		),
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		)
        	);

        	$estiloColumnasTitulo = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => true,
        			'color'     => array(
        				'rgb' => '000000'
        			),
        			'size' => 11
        		),
        		'fill'  => array(
        			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        			'color' => array('argb' => 'ECF0F1')
        		),
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		)
        	);

        	$estiloColumnasPar = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => false,
        			'color'     => array(
        				'rgb' => '000000'
        			)
        		),
        		'fill'  => array(
        			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        			'color' => array('argb' => 'FFFFFFFF')
        		),
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		),
        		'borders' => array(
        			'allborders' => array(
        				'style' => PHPExcel_Style_Border::BORDER_THIN,
        				'color' => array( 'rgb' => "000000")
        			)
        		)
        	);

        	$estiloColumnasImpar = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => false,
        			'color'     => array(
        				'rgb' => '000000'
        			)
        		),
        		'fill'  => array(
        			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        			'color' => array('argb' => 'DCDCDCDC')
        		),
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		),
        		'borders' => array(
        			'allborders' => array(
        				'style' => PHPExcel_Style_Border::BORDER_THIN,
        				'color' => array( 'rgb' => "000000")
        			)
        		)
        	);
        	$estiloBold = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => true,
        			'color'     => array(
        				'rgb' => '000000'
        			),
        			'size' => 11
        		)
        	);
        	$estiloCenter = array(
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		)
        	);
        	$estiloRight = array(
        		'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
        		)
        	);

            # ROJO PARA ANULADOS
        	$colorCelda = array(
        		'font' => array(
        			'name'      => 'Calibri',
        			'bold'      => false,
        			'color'     => array(
        				'rgb' => '000000'
        			)
        		),
        		'fill'  => array(
        			'type'      => PHPExcel_Style_Fill::FILL_SOLID,
        			'color' => array('argb' => "F28A8C")
        		)
        	);

        ###########################################################################
        ###### HOJA 0 COTIZACIÓN
        ###########################################################################

        	$this->ci->excel->setActiveSheetIndex($hoja);
        	$this->ci->excel->getActiveSheet()->setTitle("COMPROBANTE $serie - $numero");

        	for ($i = "A"; $i <= "L"; $i++)
        		$this->ci->excel->getActiveSheet()->getColumnDimension($i)->setWidth("10");

        	$this->ci->excel->getActiveSheet()->getColumnDimension("C")->setWidth("25");


        	$this->ci->excel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTitulo);

            ##############################################################
            ############# INFO CLIENTE
            ##############################################################

        	$lugar = 7;
        	$tpCliente = ($tipo_oper == "V") ? "CLIENTE: " : "PROVEEDOR ";

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRIGIDO AL $tpCliente ")
        	->setCellValue("C$lugar",  "$idCliente")
        	->mergeCells("D$lugar:H$lugar")->setCellValue("D$lugar",  "$tipoDocumentoF:  $serie-".$this->getOrderNumeroSerie($numero));
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:F$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RUC:")
        	->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "Nro. $ruc");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "RAZÓN SOCIAL:")
        	->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$nombres");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "DIRECCIÓN:")
        	->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$direccion");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "VENDEDOR:")
        	->mergeCells("C$lugar:H$lugar")->setCellValue("C$lugar",  "$vendedor");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "FORMA DE PAGO:")
        	->mergeCells("C$lugar:E$lugar")->setCellValue("C$lugar",  "$formapago_desc");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "FECHA DE EMISIÓN.:")
        	->mergeCells("C$lugar:E$lugar")->setCellValue("C$lugar",  "$fecha");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "GUIA DE REMISIÓN:")
        	->mergeCells("C$lugar:E$lugar")->setCellValue("C$lugar",  "$guiaRemision");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);
        	$lugar++;

        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar",  "COTIZACIÓN:")
        	->mergeCells("C$lugar:D$lugar")->setCellValue("C$lugar",  "$serieOC");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar")->applyFromArray($estiloBold);            
        	$lugar += 2;

            ##############################################################
            ############# INFO ARTICULOS
            ##############################################################

        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar",  "CANTIDAD");
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar",  "DESCRIPCION");
        	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar",  "MARCA");
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar",  "V/U");
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar",  "P/U");
        	$this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar",  "TOTAL");
        	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasTitulo);

        	$gravada = 0;
        	$exonerado = 0;
        	$inafecto = 0;
        	$gratuito = 0;
        	$lugar++;

        	foreach ($detalle_comprobante as $indice => $valor) {               
        		$nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
        		$nombre_producto = ($valor->CPDEC_Observacion != '') ? $nombre_producto . ". " .$valor->CPDEC_Observacion : $nombre_producto;

        		$medidaDetalle = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
        		$tipo_afectacion = $valor->AFECT_Codigo;

        		$afectacionInfo = $this->ci->producto_model->tipo_afectacion($tipo_afectacion); 

        		switch ($tipo_afectacion) {
        			case 1: 
        			$gravada += $valor->CPDEC_Subtotal;
        			break;
        			case 8: 
        			$exonerado += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 9: 
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			case 16:
        			$inafecto += $valor->CPDEC_Subtotal;
        			$nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
        			break;
        			default:
                            $gratuito = ( $valor->CPDEC_ITEMS == "1" ) ? $gratuito : $gratuito + $valor->CPDEC_Subtotal; # SI ES GRATUITO PERO TIENE BOLSA NO LO DEBE SUMAR A GRATUITO
                            $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_DescripcionSmall; 
                            break;
                          }

                    $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $importeBolsa + $valor->CPDEC_Total : $importeBolsa; # SI TIENE BOLSA SUMA
                    
                    $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", $valor->CPDEC_Cantidad);
                    $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("B$lugar:C$lugar")->setCellValue("B$lugar", $nombre_producto);
                    $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("D$lugar:E$lugar")->setCellValue("D$lugar", $valor->MARCC_Descripcion);
                    $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", $valor->CPDEC_Pu);
                    $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", $valor->CPDEC_Pu_ConIgv);
                    $this->ci->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar", $valor->CPDEC_Total);

                    if ($indice % 2 == 0)
                    	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasPar);
                    else
                    	$this->ci->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasImpar);

                    $lugar++;
                  }

                ############################
                ###### TOTALES
                ############################
                  $lugar++;
                  $lugarIT = $lugar;

                  if ( $gratuito > 0 ){
                  	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "GRATUITO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $gratuito,2);
                  	$lugar++;
                  }

                  if ( $exonerado > 0 ){
                  	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "EXONERADO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $exonerado,2);
                  	$lugar++;
                  }

                  if ( $inafecto > 0 ){
                  	$this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "INAFECTO")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $inafecto,2);
                  	$lugar++;
                  }

                  $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "GRAVADA")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $gravada,2);
                  $lugar++;

                  $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "IGV")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $igv,2);
                  $lugar++;
                  $this->ci->excel->setActiveSheetIndex($hoja)->mergeCells("E$lugar:F$lugar")->setCellValue("E$lugar", "TOTAL")->setCellValue("G$lugar", $simbolo_moneda)->setCellValue("H$lugar", $total,2);
                  $lugar++;

                  $this->ci->excel->getActiveSheet()->getStyle("E$lugarIT:F$lugar")->applyFromArray($estiloBold);
                  $this->ci->excel->getActiveSheet()->getStyle("E$lugarIT:F$lugar")->applyFromArray($estiloRight);
                  $this->ci->excel->getActiveSheet()->getStyle("G$lugarIT:G$lugar")->applyFromArray($estiloCenter);


                  $img = "images/cabeceras/logo_".$companiaComprobante.".jpg";
                  $objDrawing = new PHPExcel_Worksheet_Drawing();
                  $objDrawing->setName('Logo');
                  $objDrawing->setDescription('logo');
                  $objDrawing->setPath($img);
            $objDrawing->setOffsetX(0); # setOffsetX works properly
            $objDrawing->setOffsetY(0); # setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(100); # height
            $objDrawing->setWorksheet($this->ci->excel->setActiveSheetIndex($hoja)); 

            $filename = "COMPROBANTE $serie - $numero.xls";
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment;filename=$filename");
            header("Cache-Control: max-age=0");
            $objWriter = PHPExcel_IOFactory::createWriter($this->ci->excel, 'Excel5');
            $objWriter->save('php://output');
          }
        }
        ?>