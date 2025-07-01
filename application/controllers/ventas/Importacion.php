<?php
 ini_set('error_reporting', 1); 

include("system/application/libraries/pchart/pData.php");
include("system/application/libraries/pchart/pChart.php");
include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");
include("system/application/libraries/lib_fecha_letras.php");
include("system/application/controller/maestros/configuracionimpresion");

//include("system/application/models/importacion/importacion");

class Importacion extends CI_Controller{
  public function __construct(){
        parent::Controller();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('ventas/importacion_model');
        $this->load->model('ventas/importaciondetalle_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/presupuesto_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('almacen/Serie_model');
        $this->load->model('almacen/seriedocumento_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/proyecto_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('configuracion_model');
        
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
        $this->somevar['url'] = $_SERVER['REQUEST_URI'];
        date_default_timezone_set("America/Lima");
    }

    public function index(){
        $this->load->view('seguridad/inicio');
        
    }

    public function cargar_listado_comprobantes($codigo_cliente){

        $filter = new stdClass();
        $filter->cliente = $codigo_cliente;
        $comprobantes = $this->comprobante_model->buscar_comprobantes('V', 'N', $filter);
        $html = '<option value="">::SELECCIONE::</option>';
        for ($i = 0; $i < count($comprobantes); $i++):
            if ($comprobantes[$i]->CPP_Codigo_canje == '' || $comprobantes[$i]->CPP_Codigo_canje == NULL || $comprobantes[$i]->CPP_Codigo_canje == 0) {
				$html .= '<option value="' . $comprobantes[$i]->CPP_Codigo . '">';
                $html .= $comprobantes[$i]->CPC_Serie . '-' . $comprobantes[$i]->CPC_Numero;
                $html .= '</option>';
            }
        endfor;
        echo $html;
    }

    public function cargar_comprobante($codigo_documento)
    {

        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo_documento);
        if (!$datos_comprobante)
            die('ERROR');

        $datos_cliente = $this->cliente_model->obtener($datos_comprobante[0]->CLIP_Codigo);

        if (!$datos_cliente)
            die('ERROR');

        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $datos_moneda = $this->moneda_model->obtener($moneda);
        if (!$datos_moneda)
            die('ERROR');

        $html = '';
        $html .= '<tr><td class="tb_item">1</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($datos_comprobante[0]->CPC_Fecha)) . ' </td>';
        $html .= '<td>' . $datos_comprobante[0]->CPC_Serie . '</td>';
        $html .= '<td>' . $datos_comprobante[0]->CPC_Numero . '</td>';
        $html .= '<td style="text-align: left;">
                    <input class="cod_comprobante" type="hidden" name="cod_comprobante[]" 
                           value="' . $datos_comprobante[0]->CPP_Codigo . '">
            ' . $datos_cliente->nombre . '</td>';
        $html .= '<td style="text-align: right">
                                    <input class="comprobante_total" type="hidden"
                                           value="' . $datos_comprobante[0]->CPC_total . '">
            ' . $datos_moneda[0]->MONED_Simbolo . ' ' . $datos_comprobante[0]->CPC_total . '</td>';
        $html .= '<td><a class="remove_item"><b>X</b></a></td></tr>';
        echo $html;
    }

    public function canje_documento($codigo_documento)
    {
    	$compania = $this->somevar['compania'];
        $data['titulo_tabla'] = 'CANJE DE COMPROBANTES';
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo_documento);
        if (!$datos_comprobante)
            die('ERROR');

        $datos_cliente = $this->cliente_model->obtener($datos_comprobante[0]->CLIP_Codigo);

        if (!$datos_cliente)
            die('ERROR');

        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $data['moneda'] = $this->moneda_model->obtener($moneda);
        $filter = new stdClass();
        $filter->cliente = $datos_comprobante[0]->CLIP_Codigo;
        $data['comprobantes'] = $this->comprobante_model->buscar_comprobantes('V', 'N', $filter);
        $codigo_cliente = $datos_cliente->cliente;
        $nombre_cliente = $datos_cliente->nombre;
        $ruc_cliente = $datos_cliente->ruc;
        $direccion_cliente = $datos_cliente->direccion;
        $data['codigo_cliente'] = $codigo_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['direccion_cliente'] = $direccion_cliente;
        $data['datos'] = $datos_comprobante;
        $data['numeroAutomatico'] = 1;
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, 8);
        $data['serie_suger_b'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_b'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
        $this->load->view('ventas/ventana_canje', $data);
    }

    public function canjear_documento()
    {

        $compania = $this->somevar['compania'];
        $cliente = $this->input->post('cod_cliente');
        $fecha = $this->input->post('fecha');
        $comprobantes = $this->input->post('cod_comprobante');
        $tipo_docu = $this->input->post('cmbDocumento');
        $user = $this->session->userdata('user');
        $serie = $this->input->post('serie');
       	$numero =$this->input->post('numero');
       	$numeroAutomatico =$this->input->post('numeroAutomatico');
           
          
     
            $detalle = array();
            for ($i = 0; $i < count($comprobantes); $i++):
                $detalle_ = $this->comprobantedetalle_model->listar($comprobantes[$i]);
                if ($detalle_)
                    $detalle[] = $detalle_;
            endfor;

            $total = 0;
            $t_igv = 0;
            for ($i = 0; $i < count($comprobantes); $i++):
                $datos_ = $this->comprobante_model->obtener_comprobante($comprobantes[$i]);
                if ($datos_) {
                    $total += $datos_[0]->CPC_total;
                    $t_igv = $datos_[0]->CPC_igv100;
                }
            endfor;

            $subtotal = $total / (1 + ($t_igv / 100));

            $igv = $total - $subtotal;

            $datos = $this->comprobante_model->obtener_comprobante($comprobantes[0]);

            if ($tipo_docu == 'F') {
                $tipo = 8;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }

            if($numeroAutomatico==1){
            	$cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            	$serie = $cofiguracion_datos[0]->CONFIC_Serie;
            	$numero = $cofiguracion_datos[0]->CONFIC_Numero + 1;
            }

            $filter = new stdClass();
            $tipoOperacion=$datos[0]->CPC_TipoOperacion;
            $filter->CPC_TipoOperacion = $tipoOperacion;
            $filter->CPC_TipoDocumento = $tipo_docu;
            $filter->PRESUP_Codigo = $datos[0]->PRESUP_Codigo;
            $filter->OCOMP_Codigo = $datos[0]->OCOMP_Codigo;
            $filter->COMPP_Codigo = $datos[0]->COMPP_Codigo;
            $filter->CPC_Serie = $serie;
            $filter->CPC_Numero =$numero;
            $filter->CLIP_Codigo = $cliente;
            $filter->PROVP_Codigo = $datos[0]->PROVP_Codigo;
            $filter->CPC_NombreAuxiliar = $datos[0]->CPC_NombreAuxiliar;
            $filter->USUA_Codigo = $user;
            $filter->MONED_Codigo = $datos[0]->MONED_Codigo;
            $filter->FORPAP_Codigo = $datos[0]->FORPAP_Codigo;
            $filter->CPC_igv100 = $datos[0]->CPC_igv100;
            $filter->CPC_total = $total;
            $filter->CPC_subtotal = $subtotal;
            $filter->CPC_descuento = $datos[0]->CPC_descuento;
            $filter->CPC_igv = $igv;
            $filter->CPC_subtotal_conigv = $datos[0]->CPC_subtotal_conigv;
            $filter->CPC_descuento_conigv = $datos[0]->CPC_descuento_conigv;
            $filter->CPC_descuento100 = $datos[0]->CPC_descuento100;
            $filter->GUIAREMP_Codigo = $datos[0]->GUIAREMP_Codigo;
            $filter->CPC_GuiaRemCodigo = $datos[0]->CPC_GuiaRemCodigo;
            $filter->CPC_DocuRefeCodigo = $datos[0]->CPC_DocuRefeCodigo;
            $filter->CPC_Observacion = $datos[0]->CPC_Observacion;
            $filter->CPC_ModoImpresion = $datos[0]->CPC_ModoImpresion;
            $filter->CPC_Fecha = human_to_mysql($fecha);;
            $filter->CPC_Vendedor = $datos[0]->CPC_Vendedor;
            $filter->CPC_TDC = $datos[0]->CPC_TDC;
            $filter->CPC_FlagMueveStock = $datos[0]->CPC_FlagMueveStock;
            $filter->GUIASAP_Codigo = $datos[0]->GUIASAP_Codigo;
            $filter->GUIAINP_Codigo = $datos[0]->GUIAINP_Codigo;
            $filter->USUA_anula = $datos[0]->USUA_anula;
            $filter->CPC_FechaRegistro = $datos[0]->CPC_FechaRegistro;
            $filter->CPC_FechaModificacion = $datos[0]->CPC_FechaModificacion;
            $filter->CPC_FlagEstado = $datos[0]->CPC_FlagEstado;
            $filter->CPC_Hora = $datos[0]->CPC_Hora;
            $filter->ALMAP_Codigo = $datos[0]->ALMAP_Codigo;
            $filter->CPC_NumeroAutomatico=$numeroAutomatico;
            $comprobante = $this->comprobante_model->insertar_comprobante2($filter);
            if($numeroAutomatico==1){
            	$this->configuracion_model->modificar_configuracion($compania, $tipo, $numero);
            }
            
            for ($i = 0; $i < count($comprobantes); $i++):
                $a_filter = new stdClass();
                $a_filter->CPP_Codigo_Canje = $comprobante;
                $this->comprobante_model->modificar_comprobante($comprobantes[$i], $a_filter);
            endfor;


            for ($i = 0; $i < count($detalle); $i++):
                for ($z = 0; $z < count($detalle[$i]); $z++):
                    $c_detalle = $detalle[$i];
                    $d_filter = new stdClass();
                    $d_filter->CPP_Codigo = $comprobante;
                    $d_filter->PROD_Codigo = $c_detalle[$z]->PROD_Codigo;
                    $d_filter->CPDEC_GenInd = $c_detalle[$z]->CPDEC_GenInd;
                    $d_filter->UNDMED_Codigo = $c_detalle[$z]->UNDMED_Codigo;
                    $d_filter->CPDEC_Cantidad = $c_detalle[$z]->CPDEC_Cantidad;
                    $d_filter->CPDEC_Pu = $c_detalle[$z]->CPDEC_Pu;
                    $d_filter->CPDEC_Subtotal = $c_detalle[$z]->CPDEC_Subtotal;
                    $d_filter->CPDEC_Descuento = $c_detalle[$z]->CPDEC_Descuento;
                    $d_filter->CPDEC_Igv = $c_detalle[$z]->CPDEC_Igv;
                    $d_filter->CPDEC_Total = $c_detalle[$z]->CPDEC_Total;
                    $d_filter->CPDEC_Pu_ConIgv = $c_detalle[$z]->CPDEC_Pu_ConIgv;
                    $d_filter->CPDEC_Subtotal_ConIgv = $c_detalle[$z]->CPDEC_Subtotal_ConIgv;
                    $d_filter->CPDEC_Descuento_ConIgv = $c_detalle[$z]->CPDEC_Descuento_ConIgv;
                    $d_filter->CPDEC_Igv100 = $c_detalle[$z]->CPDEC_Igv100;
                    $d_filter->CPDEC_Descuento100 = $c_detalle[$z]->CPDEC_Descuento100;
                    $d_filter->CPDEC_Costo = $c_detalle[$z]->CPDEC_Costo;
                    $d_filter->CPDEC_Descripcion = $c_detalle[$z]->CPDEC_Descripcion;
                    $d_filter->CPDEC_Observacion = $c_detalle[$z]->CPDEC_Observacion;
                    $d_filter->CPDEC_FlagEstado = $c_detalle[$z]->CPDEC_FlagEstado;
                    $d_filter->ALMAP_Codigo = 5;
                    $d_filter->GUIAREMP_Codigo = 0;
                    $this->comprobantedetalle_model->insertar($d_filter);
                endfor;
            endfor;
            
            
            
            /**verificamos si tiene productos en serie y creamos la relacion con el nuevo documento**/
            $filterSerie=new stdClass();
            $filterSerie->SERIC_FlagEstado='1';
            /**comprobante general:14**/
            $filterSerie->DOCUP_Codigo=14;
            $filterSerie->SERDOC_NumeroRef=$comprobantes[0];
            $listaSeriesDocumento=$this->seriedocumento_model->buscar($filterSerie,null,null);
            if(count($listaSeriesDocumento)>0){
            	foreach ($listaSeriesDocumento as $valorSerie){
            		 
            		/**insertamso serie documento**/
            		$serieCodigo=$valorSerie->SERIP_Codigo;
            		$almacen=$valorSerie->ALMAP_Codigo;
            		$filterSerieD= new stdClass();
            		$filterSerieD->SERDOC_Codigo=null;
            		$filterSerieD->SERIP_Codigo=$serieCodigo;
            		/**8:facturac || boleta:9**/
            		$filterSerieD->DOCUP_Codigo=$tipo;
            		$filterSerieD->SERDOC_NumeroRef=$comprobante;
            		//$tipoIngreso=1;
            		//if($tipo_oper == 'V'){
            		$tipoIngreso=2;
            		//}
            		$filterSerieD->TIPOMOV_Tipo=$tipoIngreso;
            		$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
            		$filterSerieD->SERDOC_FlagEstado=1;
            		$this->seriedocumento_model->insertar($filterSerieD);
            		/**FIN DE INSERTAR EN SERIE**/
            	}
            }
            /**fin de verificacion**/           

            /**creamos guia de remision interna apartir cuando se genera un documento **/
            if($comprobante!=null &&  $comprobante!=0){
            	if($query = $this->db->query("CALL CREACION_GUIA_INTERNA(".$comprobante.",".$tipo.",'".$tipoOperacion."')"))
            	{
            		exit('{"result":"success", "serie":"' . $serie . '", "numero":"00' . $numero.'"}');
            	}else{
            		show_error('Error!');
            	}
            }
            /**fin de generar **/
        
       
        
        
    }

    public function obtener_detalle_comprobante($comprobante, $tipo_oper = 'V', $almacen = "")
    {
        $detalle = $this->comprobantedetalle_model->listar($comprobante); //(17)lista el detalle de la comprobante
        $lista_detalles = array();
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante); //(27)
        $formapago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $codigo_usuario = '';
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $tipo_doc = $datos_comprobante[0]->CPC_TipoDocumento;

        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);
        $ruc = $datos->ruc;
        $razon_social = $datos->nombre;

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detpresup = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $igv100 = round($valor->CPDEC_Igv100, 2);
                $pu = round((($tipo_doc == 'F') ? $valor->CPDEC_Pu : $valor->CPDEC_Pu_ConIgv - ($valor->CPDEC_Pu_ConIgv * $igv100 / 100)), 2);
                $subtotal = round((($tipo_doc == 'F') ? $valor->CPDEC_Subtotal : $pu * $cantidad), 2);
                $igv = round($valor->CPDEC_Igv, 2);
                $descuento = round($valor->CPDEC_Descuento, 2);
                $total = round((($tipo_doc == 'F') ? $valor->CPDEC_Total : $subtotal), 2);
                $pu_conigv = round($valor->CPDEC_Pu_ConIgv, 2);
                $subtotal_conigv = round($valor->CPDEC_Subtotal_ConIgv, 2);
                $descuento_conigv = round($valor->CPDEC_Descuento_ConIgv, 2);
                $observacion = $valor->CPDEC_Observacion;
                $flagGenInd = $valor->CPDEC_GenInd;
                $costo = $valor->CPDEC_Costo;
                $almacenProducto=$valor->ALMAP_Codigo;
                
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $nombre_producto = ($valor->CPDEC_Descripcion != '' ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('"', "''", $nombre_producto);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                $stock = $datos_almaprod[0]->ALMPROD_Stock;
                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detpresup;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_GenInd = $flagGenInd;
                $objeto->CPDEC_Costo = $costo;
                $objeto->CPDEC_Cantidad = $cantidad;
                $objeto->CPDEC_Pu = $pu;
                $objeto->CPDEC_Subtotal = $subtotal;
                $objeto->CPDEC_Descuento = $descuento;
                $objeto->CPDEC_Igv = $igv;
                $objeto->CPDEC_Total = $total;
                $objeto->CPDEC_Pu_ConIgv = $pu_conigv;
                $objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CPDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->ALMAP_Codigo = $almacenProducto;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->CLIP_Codigo = $cliente;
                $objeto->MONED_Codigo = $moneda;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PRESUC_Serie = $serie;
                $objeto->PRESUC_Numero = $numero;
                $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
                $objeto->onclick = $producto . ",'" . $codigo_interno . "','" . $nombre_producto . "'," . $cantidad . ",'" . $flagBS . "','" . $flagGenInd . "'," . $unidad_medida . ",'" . $nombre_unidad . "'," . $pu_conigv . "," . $pu . "," . $subtotal . "," . $igv . "," . $total . "," . $stock . "," . $costo;
                $lista_detalles[] = $objeto;
            }
        } else {
            $objeto = new stdClass();
            $objeto->CPDEP_Codigo = '';
            $objeto->Ruc = $ruc;
            $objeto->RazonSocial = $razon_social;
            $objeto->CLIP_Codigo = $cliente;
            $objeto->MONED_Codigo = $moneda;
            $objeto->FORPAP_Codigo = $formapago;
            $objeto->PRESUC_Numero = $numero;
            $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
            $lista_detalles[] = $objeto;
        }
        $resultado = json_encode($lista_detalles);

        echo $resultado;
    }


    /////////////////////////vico

    public function obtener_detalle_comprobante_x_numero_com($serie, $numero, $tipo_oper = 'V', $almacen = "")
    {
        $comprobante = $this->comprobante_model->buscar_xserienum($serie, $numero, "F", $tipo_oper);
        if (!isset($comprobante)) {
            $comprobante = $this->comprobante_model->buscar_xserienum($serie, $numero, "B", $tipo_oper);
        }
        $comprobante = $comprobante[0]->CPP_Codigo;
        //var_dump($comprobante);

        //echo("<script type='text/javascript'>alert(".count($comprobante).");</script>");

        $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante); //(27)

        $formapago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $codigo_usuario = '';
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $tipo_doc = $datos_comprobante[0]->CPC_TipoDocumento;

        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V') {
            $datos = $this->cliente_model->obtener($cliente);
        } else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C') {
            $datos = $this->proveedor_model->obtener($proveedor);
        }
        $ruc = $datos->ruc;
        $razon_social = $datos->nombre;


        $detalle = $this->comprobantedetalle_model->listar($comprobante); //(17)lista el detalle de la comprobante
        $lista_detalles = array();

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detpresup = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $igv100 = round($valor->CPDEC_Igv100, 2);
                $pu = round((($tipo_doc == 'F') ? $valor->CPDEC_Pu : $valor->CPDEC_Pu_ConIgv - ($valor->CPDEC_Pu_ConIgv * $igv100 / 100)), 2);
                $subtotal = round((($tipo_doc == 'F') ? $valor->CPDEC_Subtotal : $pu * $cantidad), 2);
                $igv = round($valor->CPDEC_Igv, 2);
                $descuento = round($valor->CPDEC_Descuento, 2);
                $total = round((($tipo_doc == 'F') ? $valor->CPDEC_Total : $subtotal), 2);


                ///stv

                if ($tipo_doc == 'B') {
                    $pu = round($valor->CPDEC_Pu_ConIgv, 2);
                    $subtotal = round(($pu * $cantidad), 2);
                    $igv = round($valor->CPDEC_Igv, 2);
                    $descuento = round($valor->CPDEC_Descuento, 2);
                    $total = round($subtotal, 2);
                }

//                if($tipo_doc=='B'){
//                $pu = round((($tipo_doc == 'B') ? $valor->CPDEC_Pu : $valor->CPDEC_Pu_ConIgv) , 2);
//                $subtotal = round((($tipo_doc == 'B') ? $valor->CPDEC_Subtotal : $pu * $cantidad), 2);
//                $igv = round(0, 2);
//                $descuento = round($valor->CPDEC_Descuento_ConIgv, 2);
//                $total = round((($tipo_doc == 'B') ? $valor->CPDEC_Total : $subtotal), 2);
//                }

                ////////

                $pu_conigv = round($valor->CPDEC_Pu_ConIgv, 2);
                $subtotal_conigv = round($valor->CPDEC_Subtotal_ConIgv, 2);
                $descuento_conigv = round($valor->CPDEC_Descuento_ConIgv, 2);
                $observacion = $valor->CPDEC_Observacion;
                $flagGenInd = $valor->CPDEC_GenInd;
                $costo = $valor->CPDEC_Costo;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $nombre_producto = ($valor->CPDEC_Descripcion != '' ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('"', "''", $nombre_producto);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                ////stv
                if (count($datos_almaprod) > 0) {
                    /////    
                    $stock = $datos_almaprod[0]->ALMPROD_Stock;
                    ///stv
                } else {
                    $stock = '';
                }
                ////
                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detpresup;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_GenInd = $flagGenInd;
                $objeto->CPDEC_Costo = $costo;
                $objeto->CPDEC_Cantidad = $cantidad;
                $objeto->CPDEC_Pu = $pu;
                $objeto->CPDEC_Subtotal = $subtotal;
                $objeto->CPDEC_Descuento = $descuento;
                $objeto->CPDEC_Igv = $igv;
                $objeto->CPDEC_Total = $total;
                $objeto->CPDEC_Pu_ConIgv = $pu_conigv;
                $objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CPDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->CLIP_Codigo = $cliente;
                $objeto->MONED_Codigo = $moneda;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PRESUC_Serie = $serie;
                $objeto->PRESUC_Numero = $numero;
                $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
                $objeto->onclick = $producto . ",'" . $codigo_interno . "','" . $nombre_producto . "'," . $cantidad . ",'" . $flagBS . "','" . $flagGenInd . "'," . $unidad_medida . ",'" . $nombre_unidad . "'," . $pu_conigv . "," . $pu . "," . $subtotal . "," . $igv . "," . $total . "," . $stock . "," . $costo;
                $lista_detalles[] = $objeto;

            }
        } else {
            $objeto = new stdClass();
            $objeto->CPDEP_Codigo = '';
            $objeto->Ruc = $ruc;
            $objeto->RazonSocial = $razon_social;
            $objeto->CLIP_Codigo = $cliente;
            $objeto->MONED_Codigo = $moneda;
            $objeto->FORPAP_Codigo = $formapago;
            $objeto->PRESUC_Numero = $numero;
            $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
            $lista_detalles[] = $objeto;
        }
        $resultado = json_encode($lista_detalles);

        echo $resultado;
    }

    ///////////////////

    public function buscar($tipo_oper = '', $tipo_docu = 'b')
    {
        //$this->output->enable_profiler(TRUE);
        $data['compania'] = $this->somevar['compania'];
       $tipo_oper = $this->uri->segment(4);
       $tipo_docu = $this->uri->segment(5);
        $b_fecha_ini = trim($this->input->post('fechai'));
        $b_fecha_fin = trim($this->input->post('fechaf'));
        $filter = new stdClass();

        if(isset($b_fecha_ini) && $b_fecha_ini != ""){
            $fi = explode("/",$b_fecha_ini);
            $filter->fecha_ini = $fi[2].'-'.$fi[1].'-'.$fi[0];
        }else{
            $filter->fecha_ini = "2010-12-12";
        }

        if(isset($b_fecha_fin) && $b_fecha_fin != ""){
            $fi = explode("/",$b_fecha_fin);
            $filter->fecha_fin = $fi[2].'-'.$fi[1].'-'.$fi[0];
        }else{
            $filter->fecha_fin = "2020-12-12";
        }

        $filter->seriei = $this->input->post('seriei');
        $filter->numero = $this->input->post('numero');

        $filter->ruc_cliente = $this->input->post('ruc_cliente');
        $filter->nombre_cliente = $this->input->post('nombre_cliente');

        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');
        $filter->nombre_proveedor = $this->input->post('nombre_proveedor');

        $listado_comprobantes = $this->comprobante_model->busqueda_comprobante($tipo_oper, $tipo_docu, $filter);
        $item = 1;
        $contadoVacios = 1;
        $lista = array();
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                
                $letraParaConvertir = $valor->CPC_TipoDocumento; 
                $arrayConversorDeNumero = $this->documento_model->obtenerAbreviatura($letraParaConvertir);  
                foreach ($arrayConversorDeNumero as $valueConvert) {
                    $ConversorDeNumero = $valueConvert->DOCUP_Codigo;
                }
                $codigo = $valor->CPP_Codigo;
                $fecha = mysql_to_human($valor->CPC_Fecha);
                $codigo_canje = $valor->CPP_Codigo_canje;
                $serie = $valor->CPC_Serie;
                $numero = $valor->CPC_Numero;
                $numero_ref = $serie . '-' . $numero;

                //guia de remision incluido en factura

                if ($valor->CPC_DocuRefeCodigo != '') {
                    $list_com = $this->comprobante_model->obtener_comprobante_ref3($valor->CPC_DocuRefeCodigo);
                    if (count($list_com) > 0) {
                        $tipo_o = $list_com[0]->GUIAREMC_TipoOperacion;
                        $guiaremp_co = $list_com[0]->GUIAREMP_Codigo;
                        $num_gui = $list_com[0]->GUIAREMC_Numero;
                        $serie = $list_com[0]->GUIAREMC_Serie;
                        $docurefe_codigo = '<a href="' . base_url() . 'index.php/almacen/guiarem/guiarem_ver/' . $guiaremp_co . '/' . $tipo_o . '" id="comprobante" name="comprobante">' . $serie . '-' . $num_gui . '</a>';
                        $guiarem_codigo = '';
                    } else {
                        $guiarem_codigo = '';
                        $docurefe_codigo = '';
                    }
                } else {
                    //factura incluido en guia de remision
                    $list_guirem = $this->comprobante_model->obtener_comprobante_guiaref($numero_ref);
                    if (count($list_guirem) > 0) {
                        $tipo_oref = $list_guirem[0]->GUIAREMC_TipoOperacion;
                        $guiaremp_coref = $list_guirem[0]->GUIAREMP_Codigo;
                        $compref = $list_guirem[0]->GUIAREMC_NumeroRef;
                        $num_guiref = $list_guirem[0]->GUIAREMC_Numero;
                        $serieref = $list_guirem[0]->GUIAREMC_Serie;
                        $guiarem_codigo = '<a href="' . base_url() . 'index.php/almacen/guiarem/guiarem_ver/' . $guiaremp_coref . '/' . $tipo_oref . '" id="comprobante" name="comprobante">' . $serieref . '-' . $num_guiref . '</a>';
                        $docurefe_codigo = '';
                    } else {
                        $guiarem_codigo = '';
                        $docurefe_codigo = '';
                    }

                }

                //$guiarem_codigo = $valor->CPC_GuiaRemCodigo;


                if ($tipo_oper == "V") {
                    if ($valor->CLIP_Codigo == 144 && $valor->CPC_NombreAuxiliar != 'cliente') {
                        $nombre = strtoupper($valor->CPC_NombreAuxiliar);
                    }
                    else {
                        $nombre = $valor->nombre;
                    }
                } else {
                    $nombre = $valor->nombre;
                }
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->CPC_total, 2);
                $estado = $valor->CPC_FlagEstado;
                $pago_pendiente = $this->comprobante_model->comprobante_pago_pendiente($codigo);
                $img_estado = ($estado == '1' || $estado == '2' ? "<a href='" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_usuario2/" . $serie . "/" . $codigo . "' id='linkVerProveedor'><img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' /></a> " : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
                if ($this->somevar['rol'] == '4' && $estado == '1' || $estado == '2') {
                    $editar = "<a href='javascript:;' onclick='editar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                }
                else {
                    $editar = "";
                    $contadoVacios++;
                }
                if ($estado == '2') { // Imprimir valor[10]
                    $ver = "<a href='javascript:;' onclick='ver_comprobante_pdf(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                }
                else {
                    $ver = "";
                    $contadoVacios++;
                }

                // Ver pdf valor[11]
/*"<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";*/
                $imp = 1;
                $tipo_oper2='"'.$tipo_oper.'"';
                $ver2 = "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(" . $codigo .",".$ConversorDeNumero.",".$imp.",".$tipo_oper2.")'  target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";


                $eliminar = "<a href='javascript:;' onclick='eliminar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                if ($estado == '2') {
                    $disparador = "<a href='javascript:;' onclick='disparador(" . $codigo . ")' >Por Aprobar</a>";
                }
                else {
                    $disparador = "";
                    $contadoVacios++;
                }
                if ($tipo_oper == 'V') { // Ventas
                    $lista[] = array($item++, $fecha, $serie, $numero, $guiarem_codigo, $docurefe_codigo, $nombre, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje, $contadoVacios);
                }
                else { // Compras
                    $lista[] = array($item++, $fecha, $serie, $numero, $guiarem_codigo, $docurefe_codigo, $nombre, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje, $contadoVacios);
                }

                $contadoVacios = 1;

            }
        }
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = $tipo_docu;
        $data['lista'] = $lista;
        $data['paginacion'] = "";
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => $tipo_docu));
        $this->load->view('ventas/buscar_comprobante_index', $data);
    }

    
     public function comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $j = '0')
    {
        $data['compania'] = $this->somevar['compania'];
     //   $tipo_oper = $this->uri->segment(4);
      //  $tipo_docu = $this->uri->segment(5);
        $ver2 = "";

        

        $data['action'] = 'index.php/ventas/importacion/comprobantes/' . $tipo_oper ;

        
        $conf['base_url'] = site_url('ventas/importacion/comprobantes/' . $tipo_oper );
        $registros = $this->importacion_model->contar_comprobantes($tipo_oper, $tipo_docu, NULL, '', '');
        $conf['per_page'] = 15;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $registros;
        $conf['uri_segment'] = 6;
        $offset = (int)$this->uri->segment(6);

        $listado_comprobantes = $this->importacion_model->buscar_comprobantes($tipo_oper, $tipo_docu, $conf['per_page'], $offset, date('Y-m-d'));
        $data['registros']=count($listado_comprobantes);
        $item = $j + 1;
        $contadoVacios = 1;
        $lista = array();
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {

                $letraParaConvertir = $valor->IMPOR_TipoDocumento; 
                $arrayConversorDeNumero = $this->documento_model->obtenerAbreviatura($letraParaConvertir);  
                foreach ($arrayConversorDeNumero as $valueConvert) {
                    $ConversorDeNumero = $valueConvert->DOCUP_Codigo;
                }

                $codigo = $valor->IMPOR_Codigo;
                $fecha = mysql_to_human($valor->IMPOR_Fecha);
                $codigo_canje = $valor->IMPOR_Codigo_canje;
                $serie = $valor->IMPOR_Serie;
                $numero = $valor->IMPOR_Numero;
                $numero_ref = $serie . '-' . $numero;
                $nimportacion = $valor->IMPOR_Nombre;
                
                /*SERIO DE OCOMPRA*/
                $ocompra_numero=$this->importacion_model->ocompra_serienumero($codigo);
                $o_serie=$ocompra_numero->OCOMC_Serie;
                $o_numero=$ocompra_numero->OCOMC_Numero;
                $numero_ocom=$o_serie.' - '.$o_numero;
                $ocompra=$ocompra_numero->OCOMP_Codigo;
                //var_dump($ocompra);
                $flag_terminado=$ocompra_numero->OCOMC_FlagTerminado;
                    if ($flag_terminado==0) {
                        $color_estado='<div style="width: 100%; height: 15px ; background-color:#13EE6B" title="AVANZADO"></div>';
                    }else{
                        
                         $color_estado='<div style="width: 100%; height: 15px ; background-color:#F11313" title="FINALIZADO"></div>';
                    }
            
                /*FIN*/
                  // var_dump($o_serie);
                $estadoAsociacion='';
                $listaGuiaremAsociados=$this->importacion_model->buscarComprobanteGuiarem($codigo,$estadoAsociacion);
                $guiarem_codigo = '';
                $docurefe_codigo = '';
                if(count($listaGuiaremAsociados)>0){
                    foreach ($listaGuiaremAsociados as $j=>$valorGuiarem){
                        $codigoGuiarem=$valorGuiarem->GUIAREMP_Codigo;
                        $serieG=$valorGuiarem->GUIAREMC_Serie;
                        $numeroG=$valorGuiarem->GUIAREMC_Numero;
                        $estadoRelacion=$valorGuiarem->COMPGUI_FlagEstado;
                        //guia de remision incluido en factura
                        if($estadoRelacion==1){
                            $docurefe_codigo .= '<a href="' . base_url() . 'index.php/almacen/guiarem/guiarem_ver/'.$codigoGuiarem.'/'.$tipo_oper.'" id="comprobante" name="comprobante">'.$serieG.'-'.$numeroG.'</a>';
                            $docurefe_codigo .= '<br>';
                        }
                        
                        //factura incluido en guia de remision
                        if($estadoRelacion==3){
                            $guiarem_codigo .= '<a href="' . base_url() . 'index.php/almacen/guiarem/guiarem_ver/'.$codigoGuiarem.'/'.$tipo_oper.'" id="comprobante" name="comprobante">'.$serieG .'-'.$numeroG.'</a>';
                            $guiarem_codigo .= '<br>';
                        }
                    }
                }
                
                
               

                if ($tipo_oper == "V") {
                    if ($valor->CLIP_Codigo == 144 && $valor->IMPOR_NombreAuxiliar != 'cliente') {
                        $nombre = strtoupper($valor->IMPOR_NombreAuxiliar);
                    }
                    else {
                        $nombre = $valor->nombre;
                    }
                } else {
                    $nombre = $valor->nombre;
                }
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->IMPOR_total, 2);
                $estado = $valor->IMPOR_FlagEstado;
                $img_estado = ($estado == '1' || $estado == '2' ? "<a href='" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_usuario2/" . $serie . "/" . $codigo . "' id='linkVerProveedor'><img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' /></a> " : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
               // $impor_liquidada=$valor->;

                if ($this->somevar['rol'] == '4' && $estado == '1' || $estado == '2') {
                    $editar = "<a href='javascript:;' onclick='editar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                }
                else {
                    $editar = "";
                    $contadoVacios++;
                }
                if ($estado == '2') { // Imprimir valor[10]
                    $imp=0;
                    $tipo_oper2='"'.$tipo_oper.'"';
                    //$ver = "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(". $codigo .",".$ConversorDeNumero.",".$imp.",".$tipo_oper2.")' target='_parent'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                    $ver = "";
                }
                else {
                    $ver = "";
                    $contadoVacios++;
                }

                // Ver pdf valor[11]
                $imp = 1;
                $tipo_oper2='"'.$tipo_oper.'"';

                if($tipo_oper == 'V'){
                $ver2 = "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(" . $codigo .",".$ConversorDeNumero.",".$imp.",".$tipo_oper2.")'  target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF '></a>";

                }
                    elseif ($tipo_oper == 'C') {
                        $ver2 = "<a href='javascript:;' onclick='comprobante_conmenbrete(".$codigo.")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                        $ver3 = "<a href='javascript:;' onclick='comprobante_importacion(".$codigo.")'><img src='" . base_url() . "images/observaciones.png' width='16' height='16' border='0' title='Ver Gastos Desaduanaje'></a>";
                        $ver4 = "<a href='javascript:;' id='liquidar_default' onclick='comprobante_importacionliquidado(".$ocompra.")'><img src='" . base_url() . "images/banco.png' width='16' height='16' border='0' title='Ver Liquidacion Total'></a>";
                    }

                $eliminar = "<a href='javascript:;' onclick='eliminar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                if ($estado == '2') {
                    $disparador = "<a href='javascript:;' onclick='disparador(" . $codigo . ")' >Por Aprobar</a>";
                }
                else {
                    $disparador = "";
                    $contadoVacios++;
                }
                
               
                /****/
                
                if ($tipo_oper == 'V') { // Ventas
                    $lista[] = array($item++, $fecha, $serie,$this->getOrderNumeroSerie($numero), $guiarem_codigo, $docurefe_codigo, $nombre, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje, $contadoVacios,$ConversorDeNumero,$numeroSerieCanjeado,$comprobantesRelacion);
                }
                else { // Compras
                    $lista[] = array($item++, $fecha, $serie,$this->getOrderNumeroSerie($numero), $guiarem_codigo, $docurefe_codigo, $nimportacion, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje, $contadoVacios, $ver3,$numero_ocom,$color_estado,$ver4, $nombre);
                }

                $contadoVacios = 1;

            }
        }
        $data['titulo_tabla'] = "RELACIÓN DE " . strtoupper($this->obtener_tipo_documento("F")) . "S";
        $data['titulo_busqueda'] = "BUSCAR " . strtoupper($this->obtener_tipo_documento("F"));
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = "F";
        $data['lista'] = $lista;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => "F"));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('ventas/importacion_index', $data);
    }

    /**
     * Modulo para visualizar 3 tipos => Factura(F), Boleta(B) o Comprobante(N)
     * EL URI: Sirve para obtener el numero de segmento seleccionado
     * ejmp: http://localhost/ccmi/index.php/ventas/comprobante/comprobante_nueva / V / Factura
     *                                        1   /    2       /     3           /  4 /   5
     * @param string $tipo_oper
     * @param string $tipo_docu
     */
    public function comprobante_nueva($tipo_oper = 'V', $tipo_docu = 'F')
    {
       // $tipo_oper = $this->uri->segment(4);
       // $tipo_docu = $this->uri->segment(5);
      //  $tipoDocumento = strtoupper($this->obtener_tipo_documento($tipo_docu));
       // if($tipoDocumento == '')
       // {
       //     redirect(base_url().'index.php/index/inicio');
       // }else {
            $tipo_oper = 'C';

            
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";
            unset($_SESSION['serie']);
            

            $comp_confi = $this->companiaconfiguracion_model->obtener($compania);
            $data['compania'] = $compania;
            //Para cambio comprobante_A
            $data['cambio_comp'] = "0";
            $data['total_det'] = "0";   
            $data['codigo'] = $codigo;
            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
            $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
            $data['url_action'] = base_url() . "index.php/ventas/importacion/comprobante_insertar";
            $data['titulo'] = "REGISTRAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
           // $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmComprobante";
            $data['oculto'] = $oculto;
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['guia'] = "";
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
            $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
            $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '1');
            $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
            $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

            if (count($cambio_dia) > 0) {
                $data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
            } else {
                $data['tdcDolar'] = '';
            }
            
            $data['serie'] = '';
            $data['numero'] = '';
            if ($tipo_oper == 'V') {
                $temp = $this->obtener_serie_numero($tipo_docu);
            }

            $data['cliente'] = "";
            $data['ruc_cliente'] = "";
            $data['nombre_cliente'] = "";
            $data['proveedor'] = "";
            $data['ruc_proveedor'] = "";
            $data['nombre_proveedor'] = "";
            $data['detalle_comprobante'] = array();
            $data['observacion'] = "";
            $data['focus'] = "";
            $data['pedido'] = "";
            $data['descuento'] = "0";
            $data['igv'] = $comp_confi[0]->COMPCONFIC_Igv;
            $data['hidden'] = "";
            $data['preciototal'] = "";
            $data['descuentotal'] = "";
            $data['igvtotal'] = "";
            $data['importetotal'] = "";
            $data['preciototal_conigv'] = "";
            $data['descuentotal_conigv'] = "";
            $data['hidden'] = "";
            $data['observacion'] = "";
            $data['ordencompra'] = "";
            $data['presupuesto_codigo'] ="";
            $data['dRef'] = "";
            $data['guiarem_codigo'] = "";
            $data['docurefe_codigo'] = "";
            $data['estado'] = "2";
            $data['numeroAutomatico'] = 1;
            $data['isProvieneCanje'] =false;
            $data['nombre'] = "";

            $data['modo_impresion'] = "1";
            if ($tipo_docu != 'B') {
                if (FORMATO_IMPRESION == 1)
                    $data['modo_impresion'] = "2";
                else
                    $data['modo_impresion'] = "1";
            }
            $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
            //obtengo las series de la configuracion
            if ($tipo_docu == 'F') {//importacion
                $tipo = 15;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }
          /*  if ($tipo_docu == 'N') {
                $tipo = 14;
            }*/
			
            
            /**gcbq limpiamos la session de series guardadas**/
            unset($_SESSION['serie']);
			unset($_SESSION['serieReal']);
			unset($_SESSION['serieRealBD']);
            /**fin de limpiar session***/
			
		/*	$listaGuiarem=array();
			$listaGuiarem=null;
			$data['listaGuiaremAsociados']=$listaGuiarem;*/

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            //$ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'B');
            $data['serie_suger_b'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_b'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            //$ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'F');
            $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_f'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            $data['cmbVendedor']=$this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('ventas/importacion_nueva', $data);
       // }
    }
public function select_cmbVendedor($index){
    $array_dist= $this->comprobante_model->select_cmbVendedor();
    $arreglo = array();
    foreach ($array_dist as $indice => $valor) {
        $indice1 = $valor->PERSP_Codigo;
        $valor1 = $valor->PERSC_Nombre." ".$valor->PERSC_ApellidoPaterno;
        $arreglo[$indice1] = $valor1;
        }
        $resultado = $this->html->optionHTML($arreglo, $index, array('', '::Seleccione::'));
        return $resultado;
}
    public function comprobante_insertar(){
        $this->load->helper('my_guiarem');

        if ($this->input->post('serie') == '')
            exit('{"result":"error", "campo":"serie"}');
        if ($this->input->post('numero') == '')
            exit('{"result":"error", "campo":"numero"}');
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == '')
            exit('{"result":"error", "campo":"ruc_proveedor"}');
        if ($this->input->post('moneda') == '0' || $this->input->post('moneda') == '')
            exit('{"result":"error", "campo":"moneda"}');

        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('moneda') > 2 && !preg_match("/^[0-9\.]+$/", $this->input->post('tdcEuro')))
            exit('{"result":"error", "campo":"tdcEuro"}');

        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');
        if ($this->input->post('tdcDolar') == '')
            exit('{"result":"error", "campo":"tdcDolar"}');
        /*if ($this->input->post('almacen') == '')
            exit('{"result":"error", "campo":"almacen"}');*/

		//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $obra = $this->input->post('obra');
        $proyecto = $this->input->post('proyecto');
        $dref = $this->input->post('dRef');
        $guiarem_id = $this->input->post("codigo");
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $nombrei = $this->input->post('nombrei');
        $compania = $this->somevar['compania'];


        $filter = new stdClass();
        $filter->IMPOR_TipoOperacion = $tipo_oper;
        $filter->IMPOR_TipoDocumento = $tipo_docu;
        $filter->IMPOR_NumeroAutomatico= $this->input->post('numeroAutomatico');
        $filter->USUA_Codigo = $this->somevar['user'];
        $filter->COMPP_Codigo = $compania;
        $filter->IMPOR_Nombre = $nombrei;
      //  $verificacion = $this->importacion_model->buscar_xserienum($serie, $numero, $tipo_docu, $tipo_oper);

        $filter->IMPOR_fobDUA = $this->input->post('FOBDUA');
        $filter->IMPOR_fleteDUA = $this->input->post('fleteDUA');
        $filter->IMPOR_seguroDUA = $this->input->post('seguroDUA');
        $filter->IMPOR_advalorem = $this->input->post('adValorem');
        $filter->IMPOR_tsaservicios = $this->input->post('tsaServicios');
        $filter->IMPOR_percepcion = $this->input->post('percepcion');

        $hora = date("H:i:s");
        /**gcbq implementamos el tipo de documento dinamico***/
        $this->load->model('maestros/documento_model');

         if ($tipo_oper == 'V') {
            if ($tipo_docu == 'F') {//nota de credito
                $tipo = 15;
            }
            if ($tipo_docu == 'B') {//nota de debito
                $tipo = 12;
            }

            $this->configuracion_model->modificar_configuracion2($compania, $tipo, $numero);

        }
        
        $filter->IMPOR_Numero = $this->input->post('numero');
      //  }
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
        $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        
        $filter->IMPOR_Observacion = strtoupper($this->input->post('observacion'));
        $filter->IMPOR_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->IMPOR_Hora = $hora;
        $filter->IMPOR_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->IMPOR_descuento100 = $this->input->post('descuento');
        $filter->IMPOR_igv100 = $this->input->post('igv');
        $filter->IMPOR_FlagEstado = 2;
        $nombre = $this->input->post('nombre_cliente');

        if ($this->input->post('cliente') == 144 ||
            $this->input->post('cliente') == 135 ||
            $this->input->post('cliente') == 218 ||
            $this->input->post('cliente') == 1037
        )
            $filter->IMPOR_NombreAuxiliar = $nombre;

        if ($tipo_oper == 'V')
            $filter->CLIP_Codigo = $this->input->post('cliente');
        else
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        
        
        if ($this->input->post('presupuesto_codigo') != '' && $this->input->post('presupuesto_codigo') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto_codigo');
        
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
        
        $filter->IMPOR_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->IMPOR_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        $filter->IMPOR_ModoImpresion = '1';

        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->IMPOR_ModoImpresion = $this->input->post('modo_impresion');

        if ($tipo_docu != 'B' && $tipo_docu != 'N') {
            $filter->IMPOR_subtotal = $this->input->post('preciototal');
            $filter->IMPOR_descuento = $this->input->post('descuentotal');
            $filter->IMPOR_igv = $this->input->post('igvtotal');

        } else {
            $filter->IMPOR_subtotal = $this->input->post('preciototal');
            $filter->IMPOR_descuento = $this->input->post('descuentotal');
            $filter->IMPOR_igv = $this->input->post('igvtotal');

            //$filter->CPC_subtotal_conigv = $this->input->post('preciototal_conigv');
            //$filter->CPC_descuento_conigv = $this->input->post('descuentotal_conigv');
        }
        $filter->IMPOR_total = $this->input->post('importetotal');
        //if ($this->input->post('cmbVendedor') != '') {
            $filter->IMPOR_Vendedor = $this->input->post('cmbVendedor');
        /*} else {
          $usuopt = $this->somevar['user'];
            $datos_vendex = $this->directivo_model->obtener_directivo_xusu($usuopt);
            $filter->CPC_Vendedor = $datos_vendex[0]->DIREP_Codigo;
        }*/
        $filter->IMPOR_TDC = $this->input->post("moneda") > 2 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');

        if($this->input->post("moneda") > 2) $filter->IMPOR_TDC_opcional = $this->input->post('tdcDolar');

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        //Datos cabecera de la guiasa.
        if ($dref!=null  && trim($dref)=='') {
            $filter->GUIAREMP_Codigo = $dref;
        }
        $filter->PROYP_Codigo=$obra;
        $filter->PROYP_Codigo=$proyecto;
        
        //$comprobante = $this->importacion_model->insertar_comprobante($filter);

        $comprobante = $this->importacion_model->insertar_importacion($filter);

        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        /* if ($tipo_docu != 'B' && $tipo_docu != 'N') {*/
        $prodpu = $this->input->post('prodpu');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $prodigv = $this->input->post('prodigv');
        /* } else {
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        }*/
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $detaccion = $this->input->post('detaccion');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodigv100 = $this->input->post('prodigv100');
        $prodcosto = $this->input->post('prodcosto');
        $almacenProducto= $this->input->post('almacenProducto');
        $proddescri = $this->input->post('proddescri');
        //producto datos
        $prodpu= $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');


        // gcbq ---orden de compra total bienes que existe 

        $cantidad_entregada_total = 0;
        $cantidad_total_ingresada = 0;
        $cant_total = 0;
        $ordencompra=$this->input->post('ordencompra');
        $ocomdet=$this->input->post('ocomdet');
     /*   if(!is_null($ordencompra)){
            $detalle = $this->ocompra_model->obtener_detalle_ocompra($ordencompra);
            if (is_array($detalle) && count($detalle) > 0) {
                foreach ($detalle as $valor2) {
                    $cant_total += $valor2->OCOMDEC_Pendiente;
                }
            }
        }*/
//$a=array();
        ///////////////para que ya no apresca laorden de compra cuando fue jalada
     /*   if(!is_null($ordencompra)) {
            $ordenesCompras = is_array($ordencompra) ? $ordencompra : array($ordencompra);
            foreach ($ordenesCompras as $oc) {
                $this->ocompra_model->modificar_flagTerminado($oc, "1");
            }
        }*/
       

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $filter = new stdClass();
                $filter->IMPOR_Codigo = $comprobante;
                $filter->PROD_Codigo = $prodcodigo[$indice];

                if ($produnidad[$indice] == '' || $produnidad[$indice] == "null") {
                    $produnidad[$indice] = NULL;
                }

                $filter->UNDMED_Codigo = $produnidad[$indice];

                $filter->IMPORDEC_Cantidad = $prodcantidad[$indice];
                /* if ($tipo_docu != 'B' && $tipo_docu != 'N') {*/
                $filter->IMPORDEC_Pu = $prodpu[$indice];
                $filter->IMPORDEC_Subtotal   = $prodprecio[$indice];
                $filter->IMPORDEC_Descuento = $proddescuento[$indice];
                $filter->IMPORDEC_Igv = $prodigv[$indice];

                /*} else {
                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
                }*/
                $filter->IMPORDEC_Total = $prodimporte[$indice];
                $filter->IMPORDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->IMPORDEC_Descuento100 = $proddescuento100[$indice];
                $filter->IMPORDEC_Igv100 = $prodigv100[$indice];
                $codigoAlmacenProducto=$almacenProducto[$indice];
                $filter->ALMAP_Codigo = $codigoAlmacenProducto;
                
                $filter->OCOMDEP_Codigo = $ocomdet[$indice];
                //gcbq agrgar flagestado de terminado ocompra 
                /*if ($ordencompra != '' && $detaccion[$indice]!="e") {
                    $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper,$ordencompra, $filter->PROD_Codigo);
                    $cantidad_entregada_total += $cantidad_entregada;
                    $cantidad_total_ingresada += $prodcantidad[$indice];
                    if ($cant_total <= $cantidad_entregada_total + $cantidad_total_ingresada) {
                        $this->ocompra_model->modificar_flagTerminado($this->input->post('ordencompra'), "1");
                    }
                    if ($cant_total > $cantidad_entregada_total + $cantidad_total_ingresada) {
                        $this->ocompra_model->modificar_flagTerminado($this->input->post('ordencompra'), "0");
                    }
                }*/
                ///////////////////


                ////stv    va ser nuevo precio costo en compra
                if ($tipo_oper == 'C') {
                    $filter->IMPORDEC_Costo = $prodpu_conigv[$indice];
                }
                ////


                if ($tipo_oper == 'V')
                    $filter->IMPORDEC_Costo = $prodcosto[$indice];
                    $filter->IMPORDEC_Descripcion = strtoupper($proddescri[$indice]);
                    $filter->IMPORDEC_GenInd = $flagGenInd[$indice];
                    $filter->IMPORDEC_Observacion = "";
                    $filter->IMPORDEC_FlagEstado = 1;
                    $filter->IMPORDEC_Pu = $prodpu[$indice];
                    $filter->IMPORDEC_Subtotal = $prodprecio[$indice];

                if ($detaccion[$indice] != 'e' && $detaccion[$indice] != 'EE') {
                    $this->importaciondetalle_model->insertar($filter);
                    if(isset($ocomdet[$indice]) && $ocomdet[$indice] != "" && $ocomdet[$indice] != 0){
                    //$this->ocompra_model->modificar_pendiente_importacion($ordencompra,$filter->OCOMDEP_Codigo,$filter->IMPORDEC_Cantidad); 
                        $this->ocompra_model->modificar_pendiente_importacion_by_id_detalle_compra($ocomdet[$indice], $filter->IMPORDEC_Cantidad);
                    }  
                /**gcbq insertar serie de cada producto**/
                if($flagGenInd[$indice]='I'){
                    if($valor!=null){
                        /**obtenemos las series de session por producto***/
                        $seriesProducto=$this->session->userdata('serieReal');
                        if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
                            foreach ($seriesProducto as $alm2 => $arrAlmacen2) {
                                if($alm2==$codigoAlmacenProducto){
                                    foreach ($arrAlmacen2 as $ind2 => $arrserie2){
                                        if ($ind2 == $valor) {
                                            $serial = $arrserie2;
                                            if($serial!=null && count($serial)>0){
                                                foreach ($serial as $i => $serie) {
                                                    $serieNumero=$serie->serieNumero;
                                                    /**verificamos si esa serie ya ha sido ingresada en COMPRAS**/
                                                    IF($tipo_oper == 'C')
                                                        $resultado=$this->serie_model->validarserie($serieNumero,0);
                                                    else
                                                        $resultado=null;
                                                    /**fin de verificacion**/
                                                    
                                                    if(count($resultado)==0){
                                                        /**INSERTAMOS EN SERIE SI ES COMPRA PERO SI ES VENTA SE ACTUALIZA**/
                                                        $filterSerie= new stdClass();
                                                        IF($tipo_oper == 'C'){
                                                            $filterSerie->SERIP_Codigo=null;
                                                            $filterSerie->PROD_Codigo=$valor;
                                                            $filterSerie->SERIC_Numero=$serieNumero;
                                                            $filterSerie->SERIC_FechaRegistro=date("Y-m-d H:i:s");
                                                            $filterSerie->SERIC_FechaModificacion=null;
                                                            $filterSerie->SERIC_FlagEstado='1';
                                                            $filterSerie->ALMAP_Codigo=$codigoAlmacenProducto;
                                                            $serieCodigo=$this->serie_model->insertar($filterSerie);
                                                            $tipoIngreso=1;
                                                            
                                                        }
                                                        /**SI ES VENTA SE crea un registro serieDocumento con la serie de compra**/
                                                        if($tipo_oper == 'V'){
                                                            $serieCodigo=$serie->serieCodigo;
                                                            $tipoIngreso=2;
                                                        }
                                                        
                                                        /**insertamso serie documento**/
                                                        $filterSerieD= new stdClass();
                                                        $filterSerieD->SERDOC_Codigo=null;
                                                        $filterSerieD->SERIP_Codigo=$serieCodigo;
                                                        $filterSerieD->DOCUP_Codigo=$tipo;
                                                        $filterSerieD->SERDOC_NumeroRef=$comprobante;
                                                        /**1:ingreso**/
                                                        $filterSerieD->TIPOMOV_Tipo=$tipoIngreso;
                                                        $filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
                                                        $filterSerieD->SERDOC_FlagEstado=1;
                                                        $this->seriedocumento_model->insertar($filterSerieD);
                                                        /**FIN DE INSERTAR EN SERIE**/
                                                        /**FIN DE INSERTAR EN SERIE**/
                                                    }else{
                                                        exit('{"result":"error3", "msj":"ya ha sido ingresado por otro usuario esta serie ' .$serieNumero. ' en el producto'.$proddescri[$indice].' "}');
                                                    }
                                                }
                                            }
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
                /**fin de insertar serie**/
                              
                //redirect('ventas/comprobante/comprobantes/' . $tipo_oper . '/' . $tipo_docu);
                }
            }
        }
                           if(!is_null($ordencompra)){
                                $detalle_ocompra=$this->ocompra_model->esta_importado($ordencompra);
                               // $cantidadp=$detalle_ocompra->OCOMDEC_Pendiente;
                                    if ($detalle_ocompra) {
                                         $this->ocompra_model->modificar_flagImportado($ordencompra, "1");
                                    }
                                
                            }

        exit('{"result":"ok", "codigo":"' . $comprobante . '"}');

    }

    public function disparador($tipo_oper = 'V', $codigo, $tipo_docu = 'F')
    {
		if($codigo!=null &&  $codigo!=0){
			if($query = $this->db->query("CALL COMPROBANTE_DISPARADOR($codigo)"))
			{
				print_r($query->row());
			}else{
				show_error('Error!');
			}
		}
		redirect('ventas/comprobante/comprobantes/' . $tipo_oper . '/' . $tipo_docu);
    }

    public function comprobante_insertar_ref()
    {
        $compania = $this->somevar['compania'];
        /**verificamos codigo del comprobante  ***/
        $codigo = $this->input->post('codigo');
        
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $dref = $this->input->post('dRef');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $documento=$this->documento_model->obtenerAbreviatura(trim($tipo_docu));
        $tipo=$documento[0]->DOCUP_Codigo;
        $filter = new stdClass();        
        $filter->CPC_TipoOperacion = $tipo_oper;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->GUIAREMP_Codigo = $dref;
        $filter->CPC_NumeroAutomatico= $this->input->post('numeroAutomatico');
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        
        $filter->CPC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CPC_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->CPC_Numero = $this->input->post('numero');
        $filter->CPC_Serie = $this->input->post('serie');
        //actualiza los numeros de configuracion 
        $numero = $filter->CPC_Numero;
        $filter->CPC_FlagEstado =1;
        if($codigo==0){
        $this->configuracion_model->modificar_configuracion($compania, $tipo_docu, $numero, $serie = null);
        }
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CPC_descuento100 = $this->input->post('descuento');
        $filter->CPC_igv100 = $this->input->post('igv');
        $estadoComprobante=$this->input->post('estado');
        $filter->CPC_FlagEstado=$estadoComprobante;
        if ($tipo_oper == 'V')
            $filter->CLIP_Codigo = $this->input->post('cliente');
        else
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto');
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
       
  
        $filter->CPC_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CPC_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CPC_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->CPC_ModoImpresion = $this->input->post('modo_impresion');
        if ($tipo_docu != 'B' && $tipo_docu != 'N') {
            $filter->CPC_subtotal = $this->input->post('preciototal');
            $filter->CPC_descuento = $this->input->post('descuentotal');
            $filter->CPC_igv = $this->input->post('igvtotal');
        } else {
            $filter->CPC_subtotal_conigv = $this->input->post('preciototal_conigv');
            $filter->CPC_descuento_conigv = $this->input->post('descuentotal_conigv');
        }
        $filter->CPC_total = $this->input->post('importetotal');
        $filter->CPC_Vendedor = null;
        //if ($this->input->post('vendedor') != '')
            $filter->CPC_Vendedor = $this->input->post('cmbVendedor');
        $filter->CPC_TDC = $this->input->post('tdc');


        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        $filter->ALMAP_Codigo = $this->input->post('almacen');
        
        $filter->PRESUP_Codigo=null;
        $filter->OCOMP_Codigo=null;
        
//Datos cabecera de la guiasa.
		if($codigo!=0){
			$comprobante=$codigo;
			$this->comprobante_model->modificar_comprobante($comprobante,$filter);
		}else{
			$comprobante = $this->comprobante_model->insertar_comprobante($filter);
		}	
        
        
        /**modificamos a estado 0 LOS REGUISTROS ASOCIADOS AL DOCUMENTO y seriesDocumento asociado***/
    
        $this->eliminarGuiaRelacionadasComprobante($tipo,$comprobante);
        /**FIN DE ELIMINACION DE DOCUMENTOS***/
        
        /***insertamos relacion comprobante guia de remision**/
        $accionAsociacionGuiarem = $this->input->post('accionAsociacionGuiarem');
        $codigoGuiaremAsociada=$this->input->post('codigoGuiaremAsociada');
        if($codigoGuiaremAsociada!=null && count($codigoGuiaremAsociada)>0){
        	foreach ($codigoGuiaremAsociada as $ind=>$valorGuia){
        		$estadoDocumentoAso=$accionAsociacionGuiarem[$ind];
        		if($estadoDocumentoAso!=0){
	        		/**insertamos comprobante y guiarem**/
	        		$filterCG=new stdClass();
	        		$filterCG->CPP_Codigo=$comprobante;
	        		$filterCG->GUIAREMP_Codigo=$valorGuia;
	        		$filterCG->COMPGUI_FlagEstado=1;
	        		$filterCG->COMPGU_FechaRegistro=date("Y-m-d H:i:s");
	        		$this->comprobante_model->insertar_comprobante_guiarem($filterCG);
	        		/**insertamos todas las series de la guia a los comprobantes***/
	        		$filterSG=new stdClass();
	        		$filterSG->DOCUP_Codigo=10;
	        		$filterSG->SERDOC_NumeroRef=$valorGuia;
	        		$listaSerieAsociado=$this->seriedocumento_model->buscar($filterSG);
	        		if(count($listaSerieAsociado)>0){
	        			foreach ($listaSerieAsociado as $k=>$valorSerie){
	        				/**insertamso serie documento**/
	        				$serieCodigo=$valorSerie->SERIP_Codigo;
	        				$almacen=$valorSerie->ALMAP_Codigo;
	        				$filterSerieD= new stdClass();
	        				$filterSerieD->SERDOC_Codigo=null;
	        				$filterSerieD->SERIP_Codigo=$serieCodigo;
	        				/**10:guiaremision**/
	        				$filterSerieD->DOCUP_Codigo=$tipo;
	        				$filterSerieD->SERDOC_NumeroRef=$comprobante;
	        				$tipoIngreso=1;
		                   	if($tipo_oper == 'V'){
		                   		$tipoIngreso=2;
		                   	}
	        				$filterSerieD->TIPOMOV_Tipo=$tipoIngreso;
	        				$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
	        				$filterSerieD->SERDOC_FlagEstado=1;
	        				$this->seriedocumento_model->insertar($filterSerieD);
	        				/**FIN DE INSERTAR EN SERIE**/
	        			}
	        		}
	        		/**fin de insertar las series al comprobante**/
        		}
        	}
        }
        /**fin de insertar relacion guia de remision **/
        
        
        
        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        if ($tipo_docu != 'B' && $tipo_docu != 'N') {
            $prodpu = $this->input->post('prodpu');
            $prodprecio = $this->input->post('prodprecio');
            $proddescuento = $this->input->post('proddescuento');
            $prodigv = $this->input->post('prodigv');
        } else {
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        }
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $detaccion = $this->input->post('detaccion');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodigv100 = $this->input->post('prodigv100');
        $prodcosto = $this->input->post('prodcosto');
        $almacenProducto= $this->input->post('almacenProducto');
        $proddescri = $this->input->post('proddescri');
        /**guia de remision asociada se ingresan en el detalle**/
        $codigoGuiarem=$this->input->post('codigoGuiarem');
        
        
       	if($codigo!=0){
	        /**eliminamos detalle comprobante***/
	        $listaDetalleComprobante=$this->comprobantedetalle_model->listar($comprobante);
	        if(count($listaDetalleComprobante)>0){
	        	foreach ($listaDetalleComprobante as $valorDetalle){
	        		$codigoDetalle=$valorDetalle->CPDEP_Codigo;
	        		$this->comprobantedetalle_model->eliminar($codigoDetalle);
	        	}
	        }
	        /**fin de eliminacion**/
        }
        
        
        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
            	if ($detaccion[$indice] != 'e') {
            		
	                $filter = new stdClass();
	                $filter->CPP_Codigo = $comprobante;
	                $filter->PROD_Codigo = $prodcodigo[$indice];
	                if ($flagBS[$indice] == 'B')
	                    $filter->UNDMED_Codigo = $produnidad[$indice];
	                else
	                    $filter->UNDMED_Codigo = NULL;
	                $filter->CPDEC_Cantidad = $prodcantidad[$indice];
	                if ($tipo_docu != 'B' && $tipo_docu != 'N') {
	                    $filter->CPDEC_Pu = $prodpu[$indice];
	                    $filter->CPDEC_Subtotal = $prodprecio[$indice];
	                    $filter->CPDEC_Descuento = $proddescuento[$indice];
	                    $filter->CPDEC_Igv = $prodigv[$indice];
	                } else {
	                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
	                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
	                }
	                $filter->CPDEC_Total = $prodimporte[$indice];
	                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
	                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
	                $filter->CPDEC_Igv100 = $prodigv100[$indice];
	                if ($tipo_oper == 'V')
	                    $filter->CPDEC_Costo = $prodcosto[$indice];
	                
	                $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
	                $filter->CPDEC_GenInd =$flagGenInd[$indice]; 
	                $filter->CPDEC_Observacion = "";
					$filter->ALMAP_Codigo=$almacenProducto[$indice];
					$filter->GUIAREMP_Codigo=$codigoGuiarem[$indice];
					$this->comprobantedetalle_model->insertar($filter);
            	}
            }
        }
        
        if($codigo!=null &&  $codigo!=0){
        	if($estadoComprobante==1){
	        	if($this->db->query("CALL COMPROBANTE_DISPARADOR_MODIFICAR($codigo)"))
	        	{
	        		exit('{"result":"ok", "codigo":"' . $codigo . '"}');
	        	}else{
	        		exit('{"result":"error", "campo":"consulte con el administrador"}');
	        	}
        	}
        }
        
        exit('{"result":"ok", "codigo":"' . $comprobante . '"}');
    }

    public function comprobante_ver($codigo, $tipo_oper = 'V', $tipo_docu = 'F')
    {
        
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago_id = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda_id = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $total = $datos_comprobante[0]->CPC_total;
        $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $modo_impresion = $datos_comprobante[0]->CPC_ModoImpresion;
        $estado = $datos_comprobante[0]->CPC_FlagEstado;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->CPC_Vendedor;
        $tdc = $datos_comprobante[0]->CPC_TDC;
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
        }
//Para cambio comprobante_A
        $data['cambio_comp'] = "0";
        $data['total_det'] = "0";
//---------------------------------------
        if ($tipo_oper == "V") {
            $data['guia'] = $guiasap_codigo;
        } else {
            $data['guia'] = $guiainp_codigo;
        }
//	
        $d_formapago = $this->formapago_model->obtener2($forma_pago_id);
        $forma_pago = $d_formapago[0]->FORPAC_Descripcion;
        $d_moneda = $this->moneda_model->obtener($moneda_id);
        $moneda = $d_moneda[0]->MONED_Descripcion . ' (' . $d_moneda[0]->MONED_Simbolo . ')';
        $data['codigo'] = $codigo;
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboPresupuesto'] = $presupuesto;
        $data['cboOrdencompra'] = $ordencompra;
        $data['cboGuiaRemision'] = $guiaremision;
        $data['cboFormaPago'] = $forma_pago;
        $data['cboMoneda'] = $moneda;
        $data['cboVendedor'] = $vendedor;
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['descuento'] = $descuento100;
        $data['igv'] = $igv100;
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuento;
        $data['igvtotal'] = $igv;
        $data['importetotal'] = $total;
        $data['preciototal_conigv'] = $subtotal_conigv;
        $data['descuentotal_conigv'] = $descuento_conigv;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "EDITAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/ventas/comprobante/comprobante_modificar";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = $fecha;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo'] = $docurefe_codigo;
        $data['observacion'] = $observacion;
        $data['hidden'] = "";
        $data['focus'] = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger'] = "";
        $data['numero_suger'] = "";
        $data['tdc'] = $tdc;
        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

        $data['detalle_comprobante'] = $detalle_comprobante;
        $this->load->view('ventas/comprobante_ver', $data);
    }


    public function comprobante_editar($codigo, $tipo_oper = 'V', $tipo_docu = 'F')
    {
        

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $datos_comprobante = $this->importacion_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        //$obra = $datos_comprobante[0]->PROYP_Codigo;
        $proyecto = $datos_comprobante[0]->PROYP_Codigo;
        $serie = $datos_comprobante[0]->IMPOR_Serie;
        $numero = $datos_comprobante[0]->IMPOR_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->IMPOR_subtotal;
        $descuento = $datos_comprobante[0]->IMPOR_descuento;
        $igv = $datos_comprobante[0]->IMPOR_igv;
        $total = $datos_comprobante[0]->IMPOR_total;
        $subtotal_conigv = $datos_comprobante[0]->IMPOR_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->IMPOR_descuento_conigv;
        $igv100 = $datos_comprobante[0]->IMPOR_igv100;
        $descuento100 = $datos_comprobante[0]->IMPOR_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->IMPOR_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->IMPOR_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->IMPOR_Observacion;
        $modo_impresion = $datos_comprobante[0]->IMPOR_ModoImpresion;
        $estado = $datos_comprobante[0]->IMPOR_FlagEstado;
        $fecha = mysql_to_human($datos_comprobante[0]->IMPOR_Fecha);
        $vendedor = $datos_comprobante[0]->IMPOR_Vendedor;
        $tdc = $datos_comprobante[0]->IMPOR_TDC;
        $nombrei = $datos_comprobante[0]->IMPOR_Nombre;
        $codigocliente = $datos_comprobante[0]->CLIP_Codigo;
        $codigoproyecto = $datos_comprobante[0]->PROYP_Codigo;
        
        if($codigoproyecto != 0){
        	$listaproyecto = $this->proyecto_model->seleccionar($codigoproyecto);
        	$data['cboObra'] = form_dropdown("obra",$listaproyecto,$codigoproyecto, " class='comboGrande'  id='obra' ");
        }else{
        	$data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
        }

        $data['numeroAutomatico'] = $datos_comprobante[0]->IMPOR_NumeroAutomatico;
        $data['cmbVendedor']=$this->select_cmbVendedor($vendedor);
        
        $data['liquidada'] = $datos_comprobante[0]->IMPOR_Liquidada;

        $data['fobDUA'] = $datos_comprobante[0]->IMPOR_fobDUA;
        $data['fleteDUA'] = $datos_comprobante[0]->IMPOR_fleteDUA;
        $data['seguroDUA'] = $datos_comprobante[0]->IMPOR_seguroDUA;
        $data['adValorem'] = $datos_comprobante[0]->IMPOR_advalorem;
        $data['tsaServicios'] = $datos_comprobante[0]->IMPOR_tsaservicios;
        $data['percepcion'] = $datos_comprobante[0]->IMPOR_percepcion;

        $data['compania'] = $this->somevar['compania'];

        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
        }
//Para cambio comprobante_A
        $data['cambio_comp'] = "0";
        $data['total_det'] = "0";
//---------------------------------------
        if ($tipo_oper == "V") {
            $data['guia'] = $guiasap_codigo;
        } else {
            $data['guia'] = $guiainp_codigo;
        }
        /**FIN codigo del tipo de documento***/
        
        
        /**ponemos en en estado seleccionado presupuesto**/
        if($presupuesto!=null && trim($presupuesto)!="" &&  $presupuesto!=0){
	        $estadoSeleccion=1;
	        $codigoPresupuesto=$presupuesto;
	        /**1:sdeleccionado,0:deseleccionado**/
	        $this->presupuesto_model->modificarTipoSeleccion($codigoPresupuesto,$estadoSeleccion);
        }
        /**fin de poner**/
        
        
        /**gcbq implementamos el tipo de documento dinamico***/
        $this->load->model('maestros/documento_model');
        $documento=$this->documento_model->obtenerAbreviatura(trim($tipo_docu));
        $tipo=$documento[0]->DOCUP_Codigo;
        /**FIN codigo del tipo de documento**/
        
        /**verificacion si comprobante esta asociada a una guia (se verifica que no sea interna)**/
        $listaGuiarem=array();
        $listaGuiarem=null;
        $estadoAsociacion=1;
        $listaGuiaremAsociados=$this->comprobante_model->buscarComprobanteGuiarem($codigo,$estadoAsociacion);
        if(count($listaGuiaremAsociados)>0){
        	foreach ($listaGuiaremAsociados as $j=>$valorGuiarem){
        		$objeto=new stdClass();
        		$objeto->codigoGuiarem=$valorGuiarem->GUIAREMP_Codigo;
        		$objeto->serie=$valorGuiarem->GUIAREMC_Serie;
        		$objeto->numero=$valorGuiarem->GUIAREMC_Numero;
        		$listaGuiarem[]=$objeto;
        	}
        }
        $data['listaGuiaremAsociados']=$listaGuiarem;
        /**fin de verificacion**/
        
        /***verificamos si factua o boleta  propviene de comprobvante**/
        $isProvieneCanje=false;
        if($tipo_oper=='V' && $tipo_docu!='N'){
        	$listaRelacionadosCanje=$this->comprobante_model->buscarComprobanteRelacionadoCanje($codigo);
        	if(count($listaRelacionadosCanje)>0){
        		$isProvieneCanje=true;
        	}
        }
        $data['isProvieneCanje'] =$isProvieneCanje;
        
        
        $data['codigo'] = $codigo;
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, $tipo_docu, $codigo), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), $presupuesto, array('', '::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper, $codigo), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), $ordencompra, array('', '::Seleccione::'), ' / ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper, $codigo), 'GUIAREMP_Codigo', array('codigo', 'nombre'), $guiaremision, array('', '::Seleccione::'), ' / ');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', $forma_pago);
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', $moneda);
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $vendedor, array('', '::Seleccione::'), ' ');
        $data['cboproyecto'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', $proyecto);
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        
        $data['ordencompra'] = $ordencompra;
        /**verificamos si orden de compra existe **/
        if($ordencompra!=null && $ordencompra!=0 && trim($ordencompra)!=""){
        	$datosOrdenCompra=$this->ocompra_model->obtener_ocompra($ordencompra);
        	$data['serieOC'] = $datosOrdenCompra[0]->OCOMC_Serie;
        	$data['numeroOC']= $datosOrdenCompra[0]->OCOMC_Numero;
        	$data['valorOC']=($tipo_oper=="V")?"0":"1";
        }
        /**fin de verificacion**/
        $data['presupuesto_codigo'] = $presupuesto;
        /**verificamos si presupuesto o cotizacion  existe **/
        if($presupuesto!=null && $presupuesto!=0 && trim($presupuesto)!=""){
        	$datosOrdenCompra=$this->presupuesto_model->obtener_presupuesto($presupuesto);
        	$data['seriePre'] = $datosOrdenCompra[0]->PRESUC_Serie;
        	$data['numeroPre']= $datosOrdenCompra[0]->PRESUC_Numero;
        }
        /**fin de verificacion**/
        $data['nombrei'] = $nombrei;
        $data['descuento'] = $descuento100;
        $data['igv'] = $igv100;
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuento;
        $data['igvtotal'] = $igv;
        $data['importetotal'] = $total;
        $data['preciototal_conigv'] = $subtotal_conigv;
        $data['descuentotal_conigv'] = $descuento_conigv;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "EDITAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/ventas/importacion/comprobante_modificar";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = $fecha;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo'] = $docurefe_codigo;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;
        $data['hidden'] = "";
        $data['focus'] = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger'] = "";
        $data['numero_suger'] = "";
        $data['tdcDolar'] = $moneda > 2 ? $datos_comprobante[0]->IMPOR_TDC_opcional : $tdc;
        $data['tdcEuro'] = $moneda > 2 ? $tdc : '';
		$data['guiaremision'] = $guiaremision;    
		$data['dRef'] = $guiaremision;
		$data['tipoOperCodigo'] =$tipo;
        $detalle_comprobante = $this->obtener_lista_detalles($codigo);
        $data['detalle_comprobante'] = $detalle_comprobante;
        unset($_SESSION['serie']);
        unset($_SESSION['serieReal']);
        unset($_SESSION['serieRealBD']);
        /**gcbq verificamos si el detalle dee comprobante contiene productos individuales**/
        if($detalle_comprobante!=null  && count($detalle_comprobante)>0){
        	/**iniciamos la libreria actualizacion de serie seleccionada solo se da en ventas**/
        	$this->load->model('almacen/almacenproductoserie_model');
        	/**fin**/
        	
			foreach ($detalle_comprobante as $key=>$valor){
                $valor->RazonSocial = "Almacen";
                $valor->PROYP_Codigo = 0;
                $valor->PROYC_Nombre = 0;

                $valor->OCOMP_Codigo_venta_ref = NULL;

                if($valor->OCOMDEP_Codigo != 0) {
                    $referencia_oc_detalle = $this->importacion_model->obtener_referencia_importacion_by_id_detalle_compra($valor->OCOMDEP_Codigo);

                    $referencia_ov = $this->ocompra_model->obtener_referencia_compra_by_id_detalle_venta($referencia_oc_detalle->OCOMP_Codigo_venta);

                    $valor->RazonSocial = "Almacen";
                    $valor->PROYP_Codigo = isset($referencia_ov->PROYP_Codigo) ? $referencia_ov->PROYP_Codigo : 0;
                    $valor->PROYC_Nombre = isset($referencia_ov->PROYC_Nombre) ? $referencia_ov->PROYC_Nombre : 0;

                    $valor->OCOMP_Codigo_venta_ref = $referencia_ov->OCOMP_Codigo;
                    
                    if(isset($referencia_ov->CLIP_Codigo)){
                        $datos_cliente = $this->cliente_model->obtener($referencia_ov->CLIP_Codigo);
                        $valor->RazonSocial = $datos_cliente->nombre;
                    }
                }

                $valor->OCOMDEC_flete = isset($referencia_oc_detalle) ? $referencia_oc_detalle->OCOMDEC_flete : 0;



				/**verificamos si es individual**/
				if($valor->CPDEC_GenInd!=null && trim($valor->CPDEC_GenInd)=="I"){
					/**obtenemos serie de ese producto **/+
					$producto_id=$valor->PROD_Codigo;
					$almacen=$valor->ALMAP_Codigo;
					$filterSerie= new stdClass();
					$filterSerie->PROD_Codigo=$producto_id;
					$filterSerie->SERIC_FlagEstado='1';
					
					$filterSerie->DOCUP_Codigo=$tipo;
					$filterSerie->SERDOC_NumeroRef=$codigo;
					$filterSerie->ALMAP_Codigo=$almacen;
					$listaSeriesProducto=$this->seriedocumento_model->buscar($filterSerie,null,null);
					if($listaSeriesProducto!=null  &&  count($listaSeriesProducto)>0){
						$reg = array();
						$regBD = array();
						foreach($listaSeriesProducto as $serieValor){
							/**lo ingresamos como se ssion ah 2 variables 1:session que se muestra , 2:sesion que queda intacta bd
							 * cuando se actualice la session  1 se compara con la session 2.**/
							$codigoSerie=$serieValor->SERIP_Codigo;
							$filter = new stdClass();
							$filter->serieNumero= $serieValor->SERIC_Numero;
							$filter->serieCodigo=$codigoSerie;
							$filter->serieDocumentoCodigo=$serieValor->SERDOC_Codigo;
							$reg[] =$filter;
							
							
							$filterBD = new stdClass();
							$filterBD->SERIC_Numero= $serieValor->SERIC_Numero;
							$filterBD->SERIP_Codigo=$codigoSerie;
							$filterBD->SERDOC_Codigo=$serieValor->SERDOC_Codigo;
							$regBD[] =$filterBD;
							
							/**si es venta lo seleccionamos en almacenproduyctoserie capaz exita perdida de datos**/
							if($tipo_oper=='V'){
								$this->almacenproductoserie_model->seleccionarSerieBD($codigoSerie,1);
							}
							/**fin de seleccion verificacion**/
						}
						$_SESSION['serieReal'][$almacen][$producto_id] = $reg;
						$_SESSION['serieRealBD'][$almacen][$producto_id] = $regBD;
					}
				}
			}
        }
        /**fin de procewso de realizaciom**/
        
        $this->layout->view('ventas/importacion_nueva', $data);
    }

    public function comprobante_modificar()
    {
        if ($this->input->post('serie') == '')
            exit('{"result":"error", "campo":"serie"}');
        if ($this->input->post('numero') == '')
            exit('{"result":"error", "campo":"numero"}');
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == '')
            exit('{"result":"error", "campo":"ruc_proveedor}');
        if ($this->input->post('moneda') == '0' || $this->input->post('moneda') == '')
            exit('{"result":"error", "campo":"moneda}');
        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');

        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('moneda') > 2 && !preg_match("/^[0-9\.]+$/", $this->input->post('tdcEuro')))
            exit('{"result":"error", "campo":"tdcEuro"}');

        if ($this->input->post('tdcDolar') == '')
            exit('{"result":"error", "campo":"tdcDolar"}');

//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $codigo = $this->input->post('cod');
//alert($codigo);
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');

        $moneda = $this->input->post("moneda");

        $prodpu= $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');
        $obra= $this->input->post('obra');
        $proyecto= $this->input->post('proyecto');
        $filter = new stdClass();
        $filter->FORPAP_Codigo = NULL;
        //if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
        $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->IMPOR_Observacion = strtoupper($this->input->post('observacion'));
        $filter->IMPOR_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->IMPOR_Numero = $this->input->post('numero');
        $filter->IMPOR_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->IMPOR_descuento100 = $this->input->post('descuento');
        $filter->IMPOR_igv100 = $this->input->post('igv');
        $filter->IMPOR_TipoDocumento = $tipo_docu;
        $filter->IMPOR_NumeroAutomatico= $this->input->post('numeroAutomatico');
        $filter->PROYP_Codigo=$obra;
        $filter->PROYP_Codigo=$proyecto;
        $filter->IMPOR_Nombre = $this->input->post('nombrei');
        $nombre = $this->input->post('nombre_cliente');

        $filter->IMPOR_TDC = $moneda > 2 ? $this->input->post("tdcEuro") : $this->input->post("tdcDolar");
        $filter->IMPOR_TDC_opcional = $moneda > 2 ? $this->input->post("tdcDolar") : $this->input->post("tdcEuro");

        $filter->IMPOR_fobDUA = $this->input->post('FOBDUA');
        $filter->IMPOR_fleteDUA = $this->input->post('fleteDUA');
        $filter->IMPOR_seguroDUA = $this->input->post('seguroDUA');
        $filter->IMPOR_advalorem = $this->input->post('adValorem');
        $filter->IMPOR_tsaservicios = $this->input->post('tsaServicios');
        $filter->IMPOR_percepcion = $this->input->post('percepcion');

        if ($this->input->post('cliente') == 144 ||
            $this->input->post('cliente') == 135 ||
            $this->input->post('cliente') == 218 ||
            $this->input->post('cliente') == 1037
        )
            $filter->IMPOR_NombreAuxiliar = $nombre;

        if ($tipo_oper == 'V')
            $filter->CLIP_Codigo = $this->input->post('cliente');
        else
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        $filter->PRESUP_Codigo = NULL;
        if ($this->input->post('presupuesto_codigo') != '' && $this->input->post('presupuesto_codigo') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto_codigo');
        
        $filter->OCOMP_Codigo = NULL;
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
        
        $filter->GUIAREMP_Codigo = NULL;
        if ($this->input->post('guiaremision') != '' && $this->input->post('guiaremision') != '0')
            $filter->GUIAREMP_Codigo = $this->input->post('guiaremision');
        
        $filter->IMPOR_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->IMPOR_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        //$filter->CPC_FlagEstado = $this->input->post('estado');
        $filter->IMPOR_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->IMPOR_ModoImpresion = $this->input->post('modo_impresion');
        if ($tipo_docu != 'B') {
            $filter->IMPOR_subtotal = $this->input->post('preciototal');
            $filter->IMPOR_descuento = $this->input->post('descuentotal');
            $filter->IMPOR_igv = $this->input->post('igvtotal');
        } else {
            $filter->IMPOR_subtotal_conigv = $this->input->post('preciototal_conigv');
            $filter->IMPOR_descuento_conigv = $this->input->post('descuentotal_conigv');
        }
        $filter->IMPOR_total = $this->input->post('importetotal');
        $filter->IMPOR_Vendedor = NULL;
       // if ($this->input->post('cmbVendedor') != '')
        $filter->IMPOR_Vendedor = $this->input->post('cmbVendedor');

        /**gcbq implementamos el tipo de documento dinamico***/
        $this->load->model('maestros/documento_model');
      //  $documento=$this->documento_model->obtenerAbreviatura(trim($tipo_docu));
       // $tipo=$documento[0]->DOCUP_Codigo;
        
        $this->importacion_model->modificar_comprobante($codigo, $filter);


        /**verificamos para ELIMINAR LAS GUIAS RELACIONADAS TIPO:1**/
        /**modificamos a estado 0 LOS REGUISTROS ASOCIADOS AL DOCUMENTO y seriesDocumento asociado***/
       // $this->eliminarGuiaRelacionadasComprobante($tipo,$codigo);
        /**FIN DE ELIMINACION DE DOCUMENTOS***/
$ordencompra=$this->input->post('ordencompra');

        
        $prodcodigo = $this->input->post('prodcodigo');
        $flagBS = $this->input->post('flagBS');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodpendiente = $this->input->post('prodpendiente');
        if ($tipo_docu != 'B') {
            $prodpu = $this->input->post('prodpu');
            $prodprecio = $this->input->post('prodprecio');
            $proddescuento = $this->input->post('proddescuento');
            $prodigv = $this->input->post('prodigv');

            $prodpu= $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');
        } else {
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        }
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodcosto = $this->input->post('prodcosto');
        $almacenProducto= $this->input->post('almacenProducto');
        $proddescri = $this->input->post('proddescri');
        $estado= $this->input->post('estado');

        $prodpu= $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');

        $det_compra_ref = $this->input->post("ocomdet");
        
        if (is_array($detacodi) > 0) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];

                $id_detalle_compra_ref = $det_compra_ref[$indice];

                $filter = new stdClass();
                $filter->IMPOR_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                if ($flagBS[$indice] == 'B'){
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                }
                if ($tipo_docu != 'B') {

                    $filter->IMPORDEC_Pu = $prodpu[$indice];
                    $filter->IMPORDEC_Subtotal = $prodprecio[$indice];

                    $filter->IMPORDEC_Descuento = $proddescuento[$indice];
                    $filter->IMPORDEC_Igv = $prodigv[$indice];
                } else {
                    $filter->IMPORDEC_Pu = $prodpu[$indice];
                    $filter->IMPORDEC_Subtotal = $prodprecio[$indice];
                    $filter->IMPORDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                    $filter->IMPORDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
                }
                $filter->IMPORDEC_Cantidad = $prodcantidad[$indice];
              ///  $filter->IMPORDEC_Cantidad = $prodpendiente[$indice];
                $filter->IMPORDEC_Total = $prodimporte[$indice];
                $filter->IMPORDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->IMPORDEC_Descuento100 = $proddescuento100[$indice];
                $filter->IMPORDEC_Igv100 = $prodigv100[$indice];
                if ($tipo_oper == 'V')
                    $filter->IMPORDEC_Costo = $prodcosto[$indice];
                
                $filter->IMPORDEC_GenInd=$flagGenInd[$indice];
                $filter->IMPORDEC_Descripcion = strtoupper($proddescri[$indice]);
                $filter->IMPORDEC_Observacion = "";
                $codigoAlmacenProducto=$almacenProducto[$indice];
                $filter->ALMAP_Codigo =$codigoAlmacenProducto;

               //var_dump($filter);
                $pendiente=$prodpendiente[$indice]-$prodcantidad[$indice];

                $producto_id=$prodcodigo[$indice];
             if ($detalle_accion == 'n') {
                    $filter->OCOMDEP_Codigo = $id_detalle_compra_ref;
                    $this->importaciondetalle_model->insertar($filter);
                    if(isset($id_detalle_compra_ref) && $id_detalle_compra_ref != "" && $id_detalle_compra_ref != 0){
                        $this->ocompra_model->modificar_pendiente_importacion_by_id_detalle_compra($id_detalle_compra_ref, $filter->IMPORDEC_Cantidad);
                    }
                  //  echo'<script>alert('.$detalle_accion.')</script>';
                  //  echo'<script>alert("hola")</script>';
                } elseif ($detalle_accion == 'm') {
                    $this->importaciondetalle_model->modificar($valor, $filter);
                     if(isset($id_detalle_compra_ref) && $id_detalle_compra_ref != "" && $id_detalle_compra_ref != 0){
                        $this->ocompra_model->modificar_pendiente_importacion_by_id_detalle_compra($id_detalle_compra_ref, -$pendiente);
                    //$this->ocompra_model->modificar_pendiente_cantidad($ordencompra,$filter->PROD_Codigo,$pendiente);
                    }
      
                } elseif ($detalle_accion == 'e') {                    
                  //  if($estado==2){
                        $this->importaciondetalle_model->eliminar($valor);
                        if(isset($id_detalle_compra_ref) && $id_detalle_compra_ref != "" && $id_detalle_compra_ref != 0){
                            $this->ocompra_model->modificar_pendiente_importacion_by_id_detalle_compra($id_detalle_compra_ref, -$filter->IMPORDEC_Cantidad);
                        }
                   /* }else{
                        $objetoM=new stdClass();
                        $objetoM->IMPORDEC_FlagEstado=0;
                        $this->importaciondetalle_model->modificar($valor,$objetoM);
                    }*/
                    
                 }
                
            }
        }

         if(!is_null($ordencompra)){
            $detalle_ocompra=$this->ocompra_model->esta_importado($ordencompra);
           // $cantidadp=$detalle_ocompra->OCOMDEC_Pendiente;
                if ($detalle_ocompra) {
                     $this->ocompra_model->modificar_flagImportado($ordencompra, "1");
                }
            
        }
        
        /**ingreso de modificacion comprobante en los diferentes movimientos 
         * verifica si alguna guia de remision lo contiene y lo modifica segun el comprobante
         * **/
        if($codigo!=null &&  $codigo!=0 && $estado==1){
            if($this->db->query("CALL COMPROBANTE_GUIAREM_MODIFICAR($codigo)"))
            {
                exit('{"result":"ok", "codigo":"' . $codigo . '"}');
            }else{
                exit('{"result":"error", "campo":"consulte con el administrador"}');
            }
        }
        /**finde modificacion**/
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }

    public function liquidar_importacion($impor)
    {
        $this->load->model('ventas/importaciondetalle_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/unidadmedida_model');

        $this->load->library('vo/importacionVO');

        $importacionVO = new ImportacionVO();

        $listadoimportacionGastos = $this->importacion_model->importacion_gastos_detalle($impor);
        $listadoimportacionArticulos = $this->importaciondetalle_model->listar($impor);
        
        $importacion = $this->importacion_model->getById($impor);
        $moneda = $this->moneda_model->getById($importacion->MONED_Codigo);
        $codigoMoneda = strtolower($moneda->MONED_smallName);

        $tdc = $codigoMoneda === "sol" ? 1 : $importacion->IMPOR_TDC;
        $tdcDolar = $codigoMoneda === "eur" ? $importacion->IMPOR_TDC_opcional : $importacion->IMPOR_TDC;

        if(count($listadoimportacionArticulos) > 0){
            foreach ($listadoimportacionArticulos as $articulo) {
                $unidad_medida = is_null($articulo->UNDMED_Codigo) ? NULL : $this->unidadmedida_model->getById($articulo->UNDMED_Codigo);
                
                //['id', 'cantidad', 'uniMedida', 'descripcion', 'precio', 'moneda', 'tdc', 'tdcDolar', *'gastoUnitario']
                $importacionVO->addProduct(array(
                    "id" => $articulo->PROD_Codigo,
                    "cantidad" => $articulo->IMPORDEC_Cantidad,
                    "uniMedida" => is_null($unidad_medida) ? NULL : $unidad_medida->UNDMED_Descripcion,
                    "descripcion" => $articulo->IMPORDEC_Descripcion,
                    "precio" => $articulo->IMPORDEC_Pu,
                    "moneda" => $moneda->MONED_Simbolo,
                    "tdc" => $codigoMoneda === "sol" ? 1 : $importacion->IMPOR_TDC,
                    "tdcDolar" => $codigoMoneda === "eur" ? $importacion->IMPOR_TDC_opcional : $importacion->IMPOR_TDC,
                    "gastoUnitario" => $articulo->IMPORDEC_Costo_uni_liquidado
                ));
            }
        }

        if (count($listadoimportacionGastos) > 0) {
            foreach ($listadoimportacionGastos as $gasto) {
                $codigoMoneda = strtolower($gasto->MONED_smallName);
                //$gasto = ['id', 'descripcion', 'cantidad', 'precio', 'igv', 'tdc', 'tdcDolar']
                $importacionVO->addGasto(array(
                    'id' => $gasto->PROD_Codigo,
                    'descripcion' => $gasto->CPDEC_Descripcion,
                    'cantidad' => $gasto->CPDEC_Cantidad,
                    'precio' => $gasto->CPDEC_Pu,
                    'igv' => $gasto->CPDEC_Igv100,
                    'tdc' => $codigoMoneda === "sol" ? 1 : $gasto->CPC_TDC,
                    'tdcDolar' => $codigoMoneda == "eur" ? $gasto->CPC_TDC_opcional : $gasto->CPC_TDC
                    ));
            }
        }

        $productos = $importacionVO->getProducts();
        $gastos = $importacionVO->getGastos();
        $fletes = $importacionVO->getFlete();

        $importacionVO->setAdvalorem(round(($importacion->IMPOR_advalorem), 2));
        $importacionVO->setTsaservicios(round(($importacion->IMPOR_tsaservicios), 2));
        $importacionVO->setPercepcion(round(($importacion->IMPOR_percepcion), 2));

        $importacionVO->setFOBDUA(round(($importacion->IMPOR_fobDUA), 2));
        $importacionVO->setSeguro(round(($importacion->IMPOR_seguroDUA), 2));
        $fletes->setTotalDolaresDUA(round(($importacion->IMPOR_fleteDUA), 2));
        
        $productos->liquidar(($gastos->getTotalDolares() + $fletes->getTotalDolares()));

        if($this->importacion_model->liquidar($impor, $productos->getItems()) == FALSE){
            $this->output->set_status_header(500);
            return;
        }
        
        echo json_encode(array(
                "productos" => $productos->getItems(),
                "unitarioGastos" => $productos->getUnitarioGastoDolares(),
                "totalGastos" => $productos->getTotalGastoDolares(),
                "unitarioCostos" => $productos->getUnitarioCostoDolares(),
                "totalCostos" => $productos->getTotalCostoDolares(),
                'exFabrica' => ($productos->getTotalCostoDolares() - $productos->getTotalDolares()) / $productos->getTotalDolares() * 100,
                'porcCIF' => ($productos->getTotalCostoDolares() - $importacionVO->getCIF()) / $importacionVO->getCIF() * 100
            ));
    }

    public function reversion_importacion($idImportacion)
    {
        //validar aqui si la importacion le pertenece al usuario

        if(!$this->importacion_model->revertir_liquidacion($idImportacion)){
            $this->output->set_status_header(500);
            return;
        }

        echo json_encode(array(
                'success' => true
            ));
    }

    public function listado_importacion($impor){
        $this->load->model('ventas/importaciondetalle_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/unidadmedida_model');

        $this->load->library('vo/importacionVO');

        $importacionVO = new ImportacionVO();

        $listadoimportacionGastos = $this->importacion_model->importacion_gastos_detalle($impor);
        $listadoimportacionArticulos = $this->importaciondetalle_model->listar($impor);
        
        $importacion = $this->importacion_model->getById($impor);
        $moneda = $this->moneda_model->getById($importacion->MONED_Codigo);
        $codigoMoneda = strtolower($moneda->MONED_smallName);

        $tdc = $codigoMoneda === "sol" ? 1 : $importacion->IMPOR_TDC;
        $tdcDolar = $codigoMoneda === "eur" ? $importacion->IMPOR_TDC_opcional : $importacion->IMPOR_TDC;

        if(count($listadoimportacionArticulos) > 0){
            foreach ($listadoimportacionArticulos as $articulo) {//var_dump($articulo);
                $unidad_medida = is_null($articulo->UNDMED_Codigo) ? NULL : $this->unidadmedida_model->getById($articulo->UNDMED_Codigo);
                
                //['id', 'cantidad', 'uniMedida', 'descripcion', 'precio', 'moneda', 'tdc', 'tdcDolar', *'gastoUnitario']
                $importacionVO->addProduct(array(
                    "id" => $articulo->PROD_Codigo,
                    "cantidad" => $articulo->IMPORDEC_Cantidad,
                    "uniMedida" => is_null($unidad_medida) ? NULL : $unidad_medida->UNDMED_Descripcion,
                    "descripcion" => $articulo->IMPORDEC_Descripcion,
                    "precio" => $articulo->IMPORDEC_Pu,
                    "moneda" => $moneda->MONED_Simbolo,
                    "tdc" => $codigoMoneda === "sol" ? 1 : $importacion->IMPOR_TDC,
                    "tdcDolar" => $codigoMoneda === "eur" ? $importacion->IMPOR_TDC_opcional : $importacion->IMPOR_TDC,
                    "gastoUnitario" => $articulo->IMPORDEC_Costo_uni_liquidado
                ));
            }
        }

        if (count($listadoimportacionGastos) > 0) {
            foreach ($listadoimportacionGastos as $gasto) {
                $codigoMoneda = strtolower($gasto->MONED_smallName);
                //$gasto = ['id', 'descripcion', 'cantidad', 'precio', 'igv', 'tdc', 'tdcDolar']
                $importacionVO->addGasto(array(
                    'id' => $gasto->PROD_Codigo,
                    'descripcion' => $gasto->CPDEC_Descripcion,
                    'cantidad' => $gasto->CPDEC_Cantidad,
                    'precio' => $gasto->CPDEC_Pu,
                    'igv' => $gasto->CPDEC_Igv100,
                    'tdc' => $codigoMoneda === "sol" ? 1 : $gasto->CPC_TDC,
                    'tdcDolar' => $codigoMoneda == "eur" ? $gasto->CPC_TDC_opcional : $gasto->CPC_TDC,
                    'aduana' => isset($gasto->FAMI_Descripcion) && strtolower($gasto->FAMI_Descripcion) === "aduana" ? 1 : 0
                    ));
            }
        }

        $productos = $importacionVO->getProducts();
        $gastos = $importacionVO->getGastos();
        $fletes = $importacionVO->getFlete();

        $importacionVO->setAdvalorem(round(($importacion->IMPOR_advalorem), 2));
        $importacionVO->setTsaservicios(round(($importacion->IMPOR_tsaservicios), 2));
        $importacionVO->setPercepcion(round(($importacion->IMPOR_percepcion), 2));

        $importacionVO->setFOBDUA(round(($importacion->IMPOR_fobDUA), 2));
        $importacionVO->setSeguro(round(($importacion->IMPOR_seguroDUA), 2));
        $fletes->setTotalDolaresDUA(round(($importacion->IMPOR_fleteDUA), 2));

        echo json_encode(array(
            'liquidada' => $importacion->IMPOR_Liquidada,
            'ingresado' => $importacion->GUIAREMP_Codigo ? 1 : 0,
            'codigo' => $importacion->IMPOR_Codigo,
            'nombre' => $importacion->IMPOR_Nombre,
            'moneda' => array(
                    "simbolo" => $moneda->MONED_Simbolo,
                    "codigo" => $codigoMoneda,
                    "tdcDolar" => $tdcDolar,
                    "tdc" => $tdc
                ),
            'fob' => $importacionVO->getFOB(),
            'fobdua' => $importacionVO->getFOBDUA(),
            'seguro' => $importacionVO->getSeguro(),
            'cif' => $importacionVO->getCIF(),
            'totalGastos' => $importacionVO->getTotalGastos(),
            'totalImportacion' => round($importacionVO->getTotalDerechos() + $gastos->getTotalIgvDolares(), 2),
            'ipm' => array(
                    'porcentaje' => $importacionVO->getIPM(),
                    'total' => round($importacionVO->getTotalIPM(), 2)
                ),
            'igv' => array(
                    'porcentaje' => $importacionVO->getIGV(),
                    'total' => round($importacionVO->getTotalIGV(), 2)
                ),
            'advalorem' => $importacionVO->getAdvalorem(),
            "tsaservicios" => $importacionVO->getTsaservicios(),
            "percepcion" => $importacionVO->getPercepcion(),
            'totalDerechos' => round($importacionVO->getTotalDerechos(), 2),
            'porcCIF2' => ($gastos->getTotalDolaresAduana() / $importacionVO->getCIF()) * 100,
            'exFabrica' => ($productos->getTotalCostoDolares() - $productos->getTotalDolares()) / $productos->getTotalDolares() * 100,
            'porcCIF' => ($productos->getTotalCostoDolares() - $importacionVO->getCIF()) / $importacionVO->getCIF() * 100,
            'gastos' => array(
                'detalle' => $gastos->getItems(),
                'total_soles' => $gastos->getTotalSoles(),
                'total_dolares' => $gastos->getTotalDolares(),
                'totalIGV' => $gastos->getMontoIGV(),
                'totalDolaresIGV' => $gastos->getTotalIgvDolares(),
                'totalDolaresAduana' => $gastos->getTotalDolaresAduana()
                ),
            'fletes' => array(
                'detalle' => $fletes->getItems(),
                'total' => $fletes->getTotalDolares(),
                'totalDUA' => $fletes->getTotalDolaresDUA()
                ),
            'articulos' => array(
                'detalle' => $productos->getItems(),
                'total_soles' => $productos->getTotalSoles(),
                'total_dolares' => $productos->getTotalDolares(),
                'total' => $productos->getTotal(),
                'totalUnitario' => $productos->getTotalUnitario(),
                'totalUnitarioDolares' => $productos->getTotalUnitarioDolares(),
                'unitarioGastos'=> $productos->getUnitarioGastoDolares(),
                'totalGastos' => $productos->getTotalGastoDolares(),
                'unitarioCostos' => $productos->getUnitarioCostoDolares(),
                'totalCostos' => $productos->getTotalCostoDolares()
                )
            ));
    }
/* ******************** ************************************
*/
public function listado_importacion_liquidado($ocompra){
     //   $impor=$impor;

        $this->load->model('ventas/importaciondetalle_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/unidadmedida_model');

        $this->load->library('vo/importacionVO');

        $importacionVO = new ImportacionVO();

        $listadoimportacionGastos = $this->importacion_model->importacion_gastos_detalle_total($ocompra);

        $listadoimportacionArticulos = $this->importaciondetalle_model->listar_impordetalle($ocompra);

       // var_dump($listadoimportacionArticulos);
        $importacion = $this->importacion_model->getById2($ocompra);


      
        if(count($listadoimportacionArticulos) > 0){
            foreach ($listadoimportacionArticulos as $articulo) {//var_dump($articulo);
                $unidad_medida = is_null($articulo->UNDMED_Codigo) ? NULL : $this->unidadmedida_model->getById($articulo->UNDMED_Codigo);
                $moneda = $this->moneda_model->getById($articulo->MONED_Codigo);
                $codigoMonedo= strtolower($moneda->MONED_smallName);

             
                //['id', 'cantidad', 'uniMedida', 'descripcion', 'precio', 'moneda', 'tdc', 'tdcDolar', *'gastoUnitario']
                $importacionVO->addProduct(array(
                    "id" => $articulo->PROD_Codigo,
                    "cantidad" => $articulo->IMPORDEC_Cantidad,
                    "uniMedida" => is_null($unidad_medida) ? NULL : $unidad_medida->UNDMED_Descripcion,
                    "descripcion" => $articulo->IMPORDEC_Descripcion,
                    "precio" => $articulo->IMPORDEC_Pu,
                    //"moneda" => $monedo->MONED_Simbolo,
                    "moneda" => $moneda->MONED_Simbolo ,
                    "tdc" => $codigoMonedo === "sol" ? 1 : $articulo->IMPOR_TDC,
                    "tdcDolar" => $codigoMonedo === "eur" ? $articulo->IMPOR_TDC_opcional : $articulo->IMPOR_TDC,
                    "gastoUnitario" => $articulo->IMPORDEC_Costo_uni_liquidado

                ));

                
            }
        }
       // var_dump($importacionVO->getFOBDUA());
        if (count($listadoimportacionGastos) > 0) {
            foreach ($listadoimportacionGastos as $gasto) {
                $codigoMoneda = strtolower($gasto->MONED_smallName);
                //$gasto = ['id', 'descripcion', 'cantidad', 'precio', 'igv', 'tdc', 'tdcDolar']
                $importacionVO->addGasto(array(
                    'id' => $gasto->PROD_Codigo,
                    'descripcion' => $gasto->CPDEC_Descripcion,
                    'cantidad' => $gasto->CPDEC_Cantidad,
                    'precio' => $gasto->CPDEC_Pu,
                    'igv' => $gasto->CPDEC_Igv100,
                    'tdc' => $codigoMoneda === "sol" ? 1 : $gasto->CPC_TDC,
                    'tdcDolar' => $codigoMoneda == "eur" ? $gasto->CPC_TDC_opcional : $gasto->CPC_TDC,
                    'aduana' => isset($gasto->FAMI_Descripcion) && strtolower($gasto->FAMI_Descripcion) === "aduana" ? 1 : 0
                    ));
            }
        }
        $fletes = $importacionVO->getFlete();
         $productos = $importacionVO->getProducts();
           $gastos = $importacionVO->getGastos();
        foreach ($importacion as $key => $value) {

            $importacionVO->setFOBDUA(round($value->IMPOR_fobDUA, 2));
            $importacionVO->setAdvalorem(round(($value->IMPOR_advalorem), 2));
            $importacionVO->setTsaservicios(round(($value->IMPOR_tsaservicios), 2));
            $importacionVO->setPercepcion(round(($value->IMPOR_percepcion), 2));
            $importacionVO->setSeguro(round(($value->IMPOR_seguroDUA), 2));
          
            $fletes->setTotalDolaresDUA(round(($value->IMPOR_fleteDUA), 2));   
            $productos->liquidar(($gastos->getTotalDolares() + $fletes->getTotalDolares()));
        }
    

        echo json_encode(array(
            'liquidada' => $importacion->IMPOR_Liquidada,
            'codigo' => $importacion->IMPOR_Codigo,
            'nombre' => $importacion->IMPOR_Nombre,
            'moneda' => array(
                    "simbolo" => $moneda->MONED_Simbolo,
                    "codigo" => $codigoMoneda,
                    "tdcDolar" => $tdcDolar,
                    "tdc" => $tdc
                ),
            'fob' => $importacionVO->getFOB(),
            'fobdua' => $importacionVO->getFOBDUA(),
            'seguro' => $importacionVO->getSeguro(),
            'cif' => $importacionVO->getCIF(),
            'totalGastos' => $importacionVO->getTotalGastos(),
            'totalImportacion' => round($importacionVO->getTotalDerechos() + $gastos->getTotalIgvDolares(), 2),
            'ipm' => array(
                    'porcentaje' => $importacionVO->getIPM(),
                    'total' => round($importacionVO->getTotalIPM(), 2)
                ),
            'igv' => array(
                    'porcentaje' => $importacionVO->getIGV(),
                    'total' => round($importacionVO->getTotalIGV(), 2)
                ),
            'advalorem' => $importacionVO->getAdvalorem(),
            "tsaservicios" => $importacionVO->getTsaservicios(),
            "percepcion" => $importacionVO->getPercepcion(),
            'totalDerechos' => round($importacionVO->getTotalDerechos(), 2),
            'porcCIF2' => ($gastos->getTotalDolaresAduana() / $importacionVO->getCIF()) * 100,
            'exFabrica' => ($productos->getTotalCostoDolares() - $productos->getTotalDolares()) / $productos->getTotalDolares() * 100,
            'porcCIF' => ($productos->getTotalCostoDolares() - $importacionVO->getCIF()) / $importacionVO->getCIF() * 100,
            'gastos' => array(
                'detalle' => $gastos->getItems(),
                'total_soles' => $gastos->getTotalSoles(),
                'total_dolares' => $gastos->getTotalDolares(),
                'totalIGV' => $gastos->getMontoIGV(),
                'totalDolaresIGV' => $gastos->getTotalIgvDolares(),
                'totalDolaresAduana' => $gastos->getTotalDolaresAduana()
                ),
            'fletes' => array(
                'detalle' => $fletes->getItems(),
                'total' => $fletes->getTotalDolares(),
                'totalDUA' => $fletes->getTotalDolaresDUA()
                ),
            'articulos' => array(
                'detalle' => $productos->getItems(),
                'total_soles' => $productos->getTotalSoles(),
                'total_dolares' => $productos->getTotalDolares(),
                'total' => $productos->getTotal(),
                'totalUnitario' => $productos->getTotalUnitario(),
                'totalUnitarioDolares' => $productos->getTotalUnitarioDolares(),
                'unitarioGastos'=> $productos->getUnitarioGastoDolares(),
                'totalGastos' => $productos->getTotalGastoDolares(),
                'unitarioCostos' => $productos->getUnitarioCostoDolares(),
                'totalCostos' => $productos->getTotalCostoDolares()
                ),
            'calculogasto'=>array(
                "productos" => $productos->getItems(),
                "unitarioGastos" => $productos->getUnitarioGastoDolares(),
                "totalGastos" => $productos->getTotalGastoDolares(),
                "unitarioCostos" => $productos->getUnitarioCostoDolares(),
                "totalCostos" => $productos->getTotalCostoDolares(),
                'exFabrica' => ($productos->getTotalCostoDolares() - $productos->getTotalDolares()) / $productos->getTotalDolares() * 100,
                'porcCIF' => ($productos->getTotalCostoDolares() - $importacionVO->getCIF()) / $importacionVO->getCIF() * 100

            )

            ));
    }

/* **************************** **************************
*/






     public function ver_reporte_pdf_importacion($impor){
       // $usuario = $this->usuario_model->obtener($this->somevar['user']);
        //$persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        //$fechahoy = date('d/m/Y');


        $this->cezpdf = new Cezpdf('a4');
       // $this->cezpdf->ezImage("images/tienda.gif", 5, 600, '', 'left');
        /* Cabecera */
        $delta = 20;
//$tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $listadoimportacion = $this->importacion_model->importacion_comprobante_reporte($impor);
        $detalle_importacion = $this->importacion_model->listado_detalle_importacion($impor);
        $serie = $detalle_importacion[0]->IMPOR_Serie;
        $numero = $detalle_importacion[0]->IMPOR_Numero;
        $nombre = $detalle_importacion[0]->EMPRC_RazonSocial;
        $ruc = $detalle_importacion[0]->EMPRC_Ruc;
        $fecha = $detalle_importacion[0]->IMPOR_Fecha;
        /*$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }*/
     
        $titulo="LISTA DE COMPROBANTE";
        $fonttitle = array("leading" => 30, "left" => 150);
        $fontespacio = array("leading" => 20, "left" => 100);
        $fontdataright = array("leading" => 0, "left" => 350);
        
        $hoy = date("d-m-Y");
        $this->cezpdf->ezText($titulo, 17, $fonttitle);
        $this->cezpdf->ezText("", 17, $fontespacio);
         
        $this->cezpdf->ezText("FECHA DE REPORTE: ".$hoy, 8, $fontdataright);
        $this->cezpdf->ezText(" " ."  ", 10, $options);
        
        $db_data = array(array('cols1' => '','cols2' => utf8_decode_seguro('Se?r(es)'), 'cols3' => ': ' . utf8_decode_seguro($nombre), 'cols4' => 'Fecha', 'cols5' => ': ' .  $fecha),
                    array('cols1' => '','cols2' => 'R.U.C.', 'cols3' => ': ' . $ruc , 'cols4' => '', 'cols5' => ' '  ), //NOMBRE DEL VENDEDOR
                    array('cols1' => '','cols2' => utf8_decode_seguro('Serie'), 'cols3' => ': ' . $serie .'   -   ' . $numero, 'cols4' => '', 'cols5' => ''),
                 //   array('cols1' => '','cols2' => utf8_decode_seguro('Tel?ono'), 'cols3' => ': ' . $telefono, 'cols4' => 'E-mail', 'cols5' => ': ' . $email)
            );
        
            $this->cezpdf->ezTable($db_data, "", "", array(
                    'width' => 525,
                    'showLines' => 0,
                    'shaded' => 0,
                    'showHeadings' => 0,
                    'xPos' => 'center',
                    'fontSize' => 8,
                    'cols' => array(
                            'cols1' => array('width' => 45, 'justification' => 'left'),
                            'cols2' => array('width' => 60, 'justification' => 'left'),
                            'cols3' => array('width' => 240, 'justification' => 'left'),
                            'cols4' => array('width' => 35, 'justification' => 'right'),
                            'cols5' => array('width' => 140, 'justification' => 'left')
                    )
            ));
        
            $this->cezpdf->ezText('', 8);
        
        $codigo="";
       // $sum = 0;
        foreach ($listadoimportacion as $key => $value) {
            

            $sum += $value->CPC_total;
     
            $db_data[] = array(
                'col1' => $key + 1,
                'col2' => $value->CPC_Fecha,
                'col3' => $value->CPC_Serie,
                'col4' => $value->CPC_Numero,
                'col5' => $value->ruc,
                'col6' => $value->nombre,
                'col7' => $value->MONED_Simbolo.$value->CPC_subtotal,
                'col8' => $value->MONED_Simbolo.$value->CPC_igv,
                'col9' => $value->MONED_Simbolo.$value->CPC_total
            );
        }
          
        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha de Registro',
            'col3' => 'SERIE',
            'col4' => 'NUMERO',
            'col5' => 'RUC',
            'col6' => 'RAZON SOCIAL',
            'col7' => 'VALOR DE VENTA',
            'col8' => 'I.G.V. 18%',
            'col9' => 'TOTAL'

        );

    $sum = $valor->MONED_Simbolo . ' ' . number_format($sum, 2);

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
                'col3' => array('width' => 45, 'justification' => 'center'),
                'col4' => array('width' => 45, 'justification' => 'center'),
                'col5' => array('width' => 60,'justification' => 'center'),
                'col6' => array('width' => 100, 'justification' => 'center'),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 60, 'justification' => 'center'),
                'col9' => array('width' => 60, 'justification' => 'center')
               /* 'col10' => array('width' => 60, 'justification' => 'center'),
                'col11' => array('width' => 60, 'justification' => 'center')*/
            )
        ));
        $this->cezpdf->ezText((''), 7, array("left" => 360));

         $this->cezpdf->ezText(('TOTAL'.'            '.$value->MONED_Simbolo.$sum), 7, array("left" => 420));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
        }

    public function comprobante_eliminar()
    {
        
        $comprobante = $this->input->post('comprobante');
        $this->comprobante_model->eliminar_comprobante($comprobante);
    }

    
   /**gcbq json verificacion de cantidad por producto y cantidad por serie este metodo lo usa json de inventario guiarem y otros***/
    public function verificacionCantidadJson(){
    	$valorProducto= $this->input->post('valorProductoJ');
    	$valorCantidad= $this->input->post('valorCantidadJ');
    	$almacen= $this->input->post('almacen');
    	
    	$serie_value2 = $this->session->userdata('serieReal');
    	$serial = array();
    	if ($serie_value2!=null && count($serie_value2) > 0 && $serie_value2 != "") {
    		foreach ($serie_value2 as $alm2 => $arrAlmacen2) {
    			if($alm2==$almacen){
    				foreach ($arrAlmacen2 as $ind2 => $arrserie2) {
		    			if ($ind2 == $valorProducto) {
		    				$serial = $arrserie2;
		    				break;
		    			}
    			
    				}
    				break;
    			}
    		}
    	}
    	if(count($serial)!=$valorCantidad){
    		echo 0;
    	}else{
    		echo 1;
    	}
    	
    	
    	
    }
    
    
    public function comprobante_buscar()
    {

    }

    function obtener_datos_cliente($cliente, $tipo_docu = 'F')
    {
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            if ($tipo_docu != 'B')
                $numdoc = $datos_persona[0]->PERSC_Ruc;
            else
                $numdoc = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $dni = $datos_persona[0]->PERSC_NumeroDocIdentidad;
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
        }

        return array('numdoc' => $numdoc, 'nombre' => $nombre, 'direccion' => $direccion, 'dni' => $dni);
    }

    public function obtener_lista_detalles($codigo)
    {
        $detalle = $this->importaciondetalle_model->listar($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->IMPORDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->IMPORDEC_Cantidad;
                $pu = $valor->IMPORDEC_Pu;
                $subtotal = $valor->IMPORDEC_Subtotal;
                $igv = $valor->IMPORDEC_Igv;
                $descuento = $valor->IMPORDEC_Descuento;
                $total = $valor->IMPORDEC_Total;
                $pu_conigv = $valor->IMPORDEC_Pu_ConIgv;
                $subtotal_conigv = $valor->IMPORDEC_Subtotal_ConIgv;
                $descuento_conigv = $valor->IMPORDEC_Descuento_ConIgv;
                $descuento100 = $valor->IMPORDEC_Descuento100;
                $igv100 = $valor->IMPORDEC_Igv100;
                $observacion = $valor->IMPORDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->IMPORDEC_GenInd;
                $costo = $valor->IMPORDEC_Costo;
                $almacenProducto= $valor->ALMAP_Codigo;
                $codigoGuiaremAsociadaDetalle= $valor->GUIAREMP_Codigo;
                $ocomdet = $valor->OCOMDEP_Codigo;
                $nombre_producto = ($valor->IMPORDEC_Descripcion != '' ? $valor->IMPORDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\', '', $nombre_producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : '';

                $objeto = new stdClass();
                $objeto->IMPORDEP_Codigo = $detacodi;
                $objeto->OCOMDEP_Codigo = $ocomdet;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->IMPORDEC_GenInd = $GenInd;
                $objeto->IMPORDEC_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->IMPORDEC_Cantidad = $cantidad;
                $objeto->IMPORDEC_Pu = $pu;
                $objeto->IMPORDEC_Subtotal = $subtotal;
                $objeto->IMPORDEC_Descuento = $descuento;
                $objeto->IMPORDEC_Igv = $igv;
                $objeto->IMPORDEC_Total = $total;
                $objeto->IMPORDEC_Pu_ConIgv = $pu_conigv;
                $objeto->IMPORDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->IMPORDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->IMPORDEC_Descuento100 = $descuento100;
                $objeto->IMPORDEC_Igv100 = $igv100;
                $objeto->IMPORDEC_Observacion = $observacion;
                $objeto->ALMAP_Codigo =$almacenProducto;
                $objeto->GUIAREMP_Codigo =$codigoGuiaremAsociadaDetalle;

                $objeto->PROYC_Nombre = $valor->PROYC_Nombre;
                $objeto->OCOMP_Codigo = $valor->OCOMP_Codigo;

                $objeto->CLIP_Codigo = $valor->CLIP_Codigo;
                $objeto->PROVP_Codigo = $valor->PROVP_Codigo;

                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }

    public function comprobante_ver_pdf($codigo, $tipo_docu = 'F')
    {

        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_formato1($codigo);
                else
                    $this->comprobante_ver_pdf_formato1_boleta($codigo);
                break;
            case 2:  //Formato para jimmyplat
                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_formato2($codigo);
                else
                    $this->comprobante_ver_pdf_formato2_boleta($codigo);
                break;
            case 3:  //Formato para jimmyplat
                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_formato3($codigo);
                else
                    $this->comprobante_ver_pdf_formato3_boleta($codigo);
                break;
            case 4:  //Formato para ferremax
                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_formato4($codigo);
                else
                    $this->comprobante_ver_pdf_formato4_boleta($codigo);
                break;
            case 5:  //Formato para G Y C
                if ($_SESSION['compania'] == "1") {
                    if ($tipo_docu != 'B')
                        $this->comprobante_ver_pdf_formato5($codigo);
                    else
                        $this->comprobante_ver_pdf_formato5_boleta($codigo);
                } else {
                    if ($tipo_docu != 'B')
                        $this->comprobante_ver_pdf_formato6($codigo);
                    else
                        $this->comprobante_ver_pdf_formato6_boleta($codigo);
                }
                break;
            case 6:  //Formato para CYL
                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_formato7($codigo);
                else
                    $this->comprobante_ver_pdf_formato7_boleta($codigo);
                break;
            default:
                comprobante_ver_pdf_formato1($codigo);
                break;
        }
    }

    public function comprobante_ver_pdf_formato1($codigo)
    {
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $modo_impresion = ((int)$datos_comprobante[0]->CPC_ModoImpresion > 0 ? $datos_comprobante[0]->CPC_ModoImpresion : '1');

        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;

        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc = $datos_persona[0]->PERSC_Ruc;
            $telefono = $datos_persona[0]->PERSC_Telefono;
            $movil = $datos_persona[0]->PERSC_Movil;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $fax = $datos_persona[0]->PERSC_Fax;
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc = $datos_empresa[0]->EMPRC_Ruc;
            $telefono = $datos_empresa[0]->EMPRC_Telefono;
            $movil = $datos_empresa[0]->EMPRC_Movil;
            $fax = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4', 'portrait');
        $this->cezpdf->selectFont('system/application/libraries/fonts/Helvetica-Bold.afm');

        /* Cabecera */
        $this->cezpdf->ezText('', '', array('leading' => 108));

        /* Datos del cliente */
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 10, array("leading" => 10, "left" => 40));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion), 10, array("leading" => 18, "left" => 40));
        $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha, 0, 2)), 10, array("leading" => 0, "left" => 380));
        $this->cezpdf->ezText(utf8_decode_seguro(strtoupper(mes_textual(substr($fecha, 3, 2)))), 10, array("leading" => 0, "left" => 430));
//$this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,9,1)),9, array("leading"=>0, "left"=>530));
        $this->cezpdf->addText(575, 675, 10, substr($fecha, 9, 1));
        $this->cezpdf->ezText($ruc, 10, array("leading" => 20, "left" => 40));
        $this->cezpdf->ezText($guiarem_codigo, 10, array("leading" => 0, "left" => 400));


        $this->cezpdf->ezText('', '', array("leading" => 30));


        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_comprobante as $indice => $valor) {
            if ($valor->CPDEC_Pu_ConIgv != '')
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
            $db_data[] = array(
                'cols2' => $valor->CPDEC_Cantidad,
                'cols5' => utf8_decode_seguro(substr($valor->PROD_Nombre, 0, 45)),
                'cols6' => number_format(($modo_impresion == '1' ? $pu_conigv : $valor->CPDEC_Pu), 2),
                'cols7' => number_format($valor->CPDEC_Cantidad * ($modo_impresion == '1' ? $pu_conigv : $valor->CPDEC_Pu), 2)
            );
        }

        $this->cezpdf->ezTable($db_data, '', '', array(
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 11,
            'cols' => array(
                'cols2' => array('width' => 45, 'justification' => 'center'),
                'cols5' => array('width' => 365, 'justification' => 'left'),
                'cols6' => array('width' => 55, 'justification' => 'right'),
                'cols7' => array('width' => 70, 'justification' => 'right'),
            )
        ));

        $this->cezpdf->addText(90, 155, 10, utf8_decode_seguro(strtoupper($docurefe_codigo)));

        /* Totales */

        $this->cezpdf->addText(90, 123, 11, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
        $this->cezpdf->addText(520, 100, 11, $moneda_simbolo . ' ' . number_format($subtotal, 2));
        $this->cezpdf->addText(470, 82, 11, $igv100 . ' %');
        $this->cezpdf->addText(520, 82, 11, $moneda_simbolo . ' ' . number_format($igv, 2));
        $this->cezpdf->addText(520, 64, 11, $moneda_simbolo . ' ' . number_format(($total), 2));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function comprobante_ver_pdf_formato1_boleta($codigo)
    {
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;

        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;

        $temp = $this->obtener_datos_cliente($cliente);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 9, array("leading" => 130, "left" => 30));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 17, "left" => 30));

        $this->cezpdf->ezText('', '', array("leading" => 25));

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_comprobante as $indice => $valor) {
            $nomprod = $valor->PROD_Nombre;

            if (strlen($nomprod) > 41)
                $nomprod = substr($nomprod, 0, 38) . ' ...';
            $db_data[] = array(
                'cols1' => '',
                'cols2' => $valor->CPDEC_Cantidad,
                'cols3' => utf8_decode_seguro($nomprod),
                'cols4' => number_format($valor->CPDEC_Pu_ConIgv, 2),
                'cols5' => number_format($valor->CPDEC_Total, 2),
                'cols6' => ''
            );
        }

        $this->cezpdf->ezTable($db_data, '', '', array(
            'width' => 555,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'center'),
                'cols2' => array('width' => 45, 'justification' => 'center'),
                'cols3' => array('width' => 205, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'right'),
                'cols5' => array('width' => 50, 'justification' => 'right'),
                'cols6' => array('width' => 150, 'justification' => 'center')
            )
        ));

        /*         * Sub Totales* */
        $delta = 130;
        $positionx = 400;
        $positiony = 120 + $delta;
        $this->cezpdf->addText(120, $positiony, 10, strtoupper(num2letras(round($total, 2))));
        $this->cezpdf->addText($positionx, $positiony - 19, 10, number_format($total, 2));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function comprobante_ver_pdf_conmenbrete($tipo_oper, $codigo, $tipo_docu = 'F', $img = 0)
    {

        switch (FORMATO_IMPRESION) {

            case 1: //Formato para ferresat
                switch ($tipo_docu) {
                    case 'F': // Se le manda el img 1 por defecto desde comprobante.js linea 455 => ver_comprobante_pdf(comprobante)
                        $this->comprobante_ver_pdf_conmenbrete_formato1($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'B':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_boleta1($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'N':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_com($codigo, $img);
                        break;
                }
                break;

            default:
                $this->comprobante_ver_pdf_conmenbrete_formato1($codigo, $tipo_docu, $tipo_oper, $img = 0);
                break;
        }
    }

    public function comprobante_ver_pdf_conmenbrete1($tipo_oper, $codigo, $tipo_docu = 'F', $img = 0)
    {


        switch (FORMATO_IMPRESION) {

            case 1: //Formato para ferresat
                switch ($tipo_docu) {
                    case 'F':
                        $this->comprobante_ver_pdf_conmenbrete_formato11($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'B':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_boleta1($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'N':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_com($codigo, $img);
                        break;
                }
                break;

            default:
                $this->comprobante_ver_pdf_conmenbrete_formato11($codigo, $tipo_docu, $tipo_oper, $img = 0);
                break;
        }
    }

    public function comprobante_ver_html($codigo, $tipo_docu = 'F')
    {
        $img = 1;
        switch (FORMATO_IMPRESION) {
            case 1:

                if ($tipo_docu != 'B')
                    $this->comprobante_ver_pdf_conmenbrete_formato1($codigo, $img);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato1_boleta($codigo, $img);
                break;
        }
    }

    public function comprobante_ver_pdf_conmenbrete_formato1_com($codigo, $img)
    {
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        //$proveedor = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
        $empresa = $datos_proveedor[0]->EMPRP_Codigo;
        $persona = $datos_proveedor[0]->PERSP_Codigo;
        $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
         $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $simb = $datos_moneda[0]->MONED_Simbolo;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc = $datos_persona[0]->PERSC_Ruc;
            ///stv
            $dni = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            ////////
            $telefono = $datos_persona[0]->PERSC_Telefono;
            $movil = $datos_persona[0]->PERSC_Movil;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $fax = $datos_persona[0]->PERSC_Fax;
        } else {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc = $datos_empresa[0]->EMPRC_Ruc;
            $telefono = $datos_empresa[0]->EMPRC_Telefono;
            $movil = $datos_empresa[0]->EMPRC_Movil;
            $fax = $datos_empresa[0]->EMPRC_Fax;
            $direccion = $datos_empresa[0]->EMPRC_Direccion;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
        }

        if ($datos_comprobante[0]->CLIP_Codigo == 144 ||
            $datos_comprobante[0]->CLIP_Codigo == 135 ||
            $datos_comprobante[0]->CLIP_Codigo == 218 ||
            $datos_comprobante[0]->CLIP_Codigo == 1037
        )
            $nombre_cliente = $datos_comprobante[0]->CPC_NombreAuxiliar;

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

        ///////stv
        if ($img == 0) {
            $notimg = "comprobante_proveedor_.jpg"; //madyplac_com.jpg
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
        } else {
            $notimg = "";
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
        }
        //////        

//   asi estaba     $this->cezpdf = new Cezpdf('a4');
        //////////

        for ($i = 1; $i <= 1; $i++) {
            if ($i == 1)
                $x = 10;
            else
                $x = 350;
//serie y numero de comprobante venta
            if ($img == 0) {
                //$this->cezpdf->addText(320, 762, 12, utf8_decode_seguro($serie . '   -  ' . $this->getOrderNumeroSerie($numero)));
                $this->cezpdf->addText($posicionX + 430, $posicionY + 810, 18, $ruc);
               $this->cezpdf->addText($posicionX + 425, $posicionY + 755, 20, $serie."  -  ");
            $this->cezpdf->addText($posicionX + 486, $posicionY + 755, 20, $numero);
            }

            if ($img != 0) {

                $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '       ' . mes_textual(substr($fecha, 3, 2)) . '      ' . substr($fecha, 8, 4));
                $this->cezpdf->addText($posicionX + 430, $posicionY + 810, 18, utf8_decode_seguro($fecha_text));
               //- $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . mes_textual(substr($fecha, 3, 2)) . '           ' . substr($fecha, 8, 4));
               //- $this->cezpdf->addText(296, 754, 10, utf8_decode_seguro($fecha_text));
                $this->cezpdf->addText($posicionX + 70, $posicionY + 710, 12, $nombre_cliente);
               //- $this->cezpdf->addText(62, 698, 8, utf8_decode_seguro($nombre_cliente));
                $this->cezpdf->addText(350, 735, 9, utf8_decode_seguro($ruc));
               //- $ruc = substr($ruc, 0, 1) . '    ' . substr($ruc, 1, 1) . '   ' . substr($ruc, 2, 1) . '   ' . substr($ruc, 3, 1) . '  ' . substr($ruc, 4, 1) . '   ' . substr($ruc, 5, 1) . '   ' . substr($ruc, 6, 1) . '   ' . substr($ruc, 7, 1) . '    ' . substr($ruc, 8, 1) . '   ' . substr($ruc, 9, 1) . '   ' . substr($ruc, 10, 1);
                //-$this->cezpdf->addText(296, 726, 9, utf8_decode_seguro($ruc));

                if ($tipo == 0) {
                    $this->cezpdf->addText(298, 678, 9, utf8_decode_seguro($dni));
           
                }
//            $this->cezpdf->addText(378,790, 11, utf8_decode_seguro('X'));
                /////
               
                $this->cezpdf->addText(60, 720, 8, utf8_decode_seguro(substr($direccion,0,58)));

                $y = 685;

               //- $this->cezpdf->addText(60, 684, 8, utf8_decode_seguro($direccion));

                //-$y = 646;


                foreach ($detalle_comprobante as $indice => $valor) {
                    $cod_prod = $valor->PROD_CodigoUsuario;
                    $cant = $valor->CPDEC_Cantidad;
                    $pu = number_format($valor->CPDEC_Pu_ConIgv, 2);
                    $st = number_format($valor->CPDEC_Total, 2);
                    $producto = substr($valor->PROD_Nombre, 0, 25);
                    $unidad = $valor->UNDMED_Simbolo;

                   /* $this->cezpdf->addText(60, $y, 8, utf8_decode_seguro($producto . ' - ' . $unidad));
                    $this->cezpdf->addText(32, $y, 9, utf8_decode_seguro($cant));
                    $this->cezpdf->addText(326, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($pu, 2)));
                    $this->cezpdf->addText(376, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($st, 2)));
                    $y -= 21;*/

                      $this->cezpdf->addText(60, $y, 8, utf8_decode_seguro($producto . ' - ' . $unidad));
                    $this->cezpdf->addText(32, $y, 9, utf8_decode_seguro($cant));
                    $this->cezpdf->addText(310, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($pu, 2)));
                    $this->cezpdf->addText(358, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($st, 2)));
                    $y -= 15;
                }
                


                //$this->cezpdf->addText(26, 296, 8, 'SON: ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                $this->cezpdf->addText(358, 575, 10, utf8_decode_seguro($simb . ' ' . number_format($total, 2)));
                //-$this->cezpdf->addText(358, 270, 10, utf8_decode_seguro($simb . ' ' . number_format($total, 2)));

            } else {

                $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '       ' . mes_textual(substr($fecha, 3, 2)) . '      ' . substr($fecha, 8, 4));
                $this->cezpdf->addText($posicionX + 200, $posicionY + 760, 12, utf8_decode_seguro($fecha_text));

                $this->cezpdf->addText(62, 735, 8, utf8_decode_seguro($nombre_cliente));
               // $ruc = substr($ruc, 0, 1) . '    ' . substr($ruc, 1, 1) . '   ' . substr($ruc, 2, 1) . '   ' . substr($ruc, 3, 1) . '  ' . substr($ruc, 4, 1) . '   ' . substr($ruc, 5, 1) . '   ' . substr($ruc, 6, 1) . '   ' . substr($ruc, 7, 1) . '    ' . substr($ruc, 8, 1) . '   ' . substr($ruc, 9, 1) . '   ' . substr($ruc, 10, 1);
                $this->cezpdf->addText(350, 735, 9, utf8_decode_seguro($ruc));

                ///stv
                if ($tipo == 0) {
                    $this->cezpdf->addText(298, 678, 9, utf8_decode_seguro($dni));
                }
//            $this->cezpdf->addText(378,790, 11, utf8_decode_seguro('X'));
                /////
                $this->cezpdf->addText(60, 720, 8, utf8_decode_seguro(substr($direccion,0,58)));

                $y = 640;

                foreach ($detalle_comprobante as $indice => $valor) {
                    $cod_prod = $valor->PROD_CodigoUsuario;
                    $cant = $valor->CPDEC_Cantidad;
                    $pu = number_format($valor->CPDEC_Pu_ConIgv, 2);
                    $st = number_format($valor->CPDEC_Total, 2);
                    $producto = substr($valor->PROD_Nombre, 0, 25);
                    $unidad = $valor->UNDMED_Simbolo;

                    $this->cezpdf->addText(60, $y, 8, utf8_decode_seguro($producto . ' - ' . $unidad));
                    $this->cezpdf->addText(32, $y, 9, utf8_decode_seguro($cant));
                    $this->cezpdf->addText(310, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($pu, 2)));
                    $this->cezpdf->addText(358, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($st, 2)));
                    $y -= 15;
                }


               // $this->cezpdf->addText(26, 296, 8, 'SON: ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);

                //$this->cezpdf->addText(358, 575, 10, utf8_decode_seguro($simb . ' ' . number_format($total, 2)));

            }
        }
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }


    //gcqb aumentado
    public function obtener_id_docuref()
    {
        $serie_numero = $this->input->post('serie_numero');
        $datos_guiarem = $this->comprobante_model->obtener_comprobante_ref3($serie_numero);
        echo $datos_guiarem[0]->GUIAREMP_Codigo;
    }


    ////stv aumentado

    public function ali_precio($precio = "")
    {
        if ($precio != "") {
            $pri_precio = substr($precio, 0, 3);
            $ter_precio = substr(substr($precio, strlen($pri_precio)), strpos(substr($precio, strlen($pri_precio)), "."));
            $seg_precio = substr(substr($precio, strlen($pri_precio)), 0, strlen(substr($precio, strlen($pri_precio))) - (strlen($ter_precio)));
            $nseg_precio = strlen($seg_precio);
            $nn = 5 - $nseg_precio;
            $esp = "";
            for ($j = 0; $j < $nn; $j++) {
                if ($j == 1) {
                    $esp = $esp . " ";
                } else {
                    $esp = $esp . "  ";
                }
            }
            $precio = $pri_precio . $esp . $seg_precio . $ter_precio;

            return $precio;

        }
    }

    //////

    /**
     * TODO - LA IMPRESION EXISTE UNA RESTRICCION SI ES COMPAÑIA 3 REALIZA OTRA ACCIONES DIFERENTE (Pregunta a Israel)
     * Tener en cuenta que este formato es utilizado por varios, como en compra o venta de facturas o boletas o comprobantes o Pedidos
     * @param $tipo_oper
     * @param $codigo
     * @param string $tipo_docu
     * @param string $img
     */
    public function comprobante_ver_pdf_conmenbrete_formato1($tipo_oper, $codigo, $tipo_docu = 'F', $img = 0){

        $hoy = date("Y-m-d");
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $total = $datos_comprobante[0]->CPC_total;
        $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $modo_impresion = $datos_comprobante[0]->CPC_ModoImpresion;
        $estado = $datos_comprobante[0]->CPC_FlagEstado;
        $almacen = $datos_comprobante[0]->ALMAP_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->CPC_Vendedor;
        $tdc = $datos_comprobante[0]->CPC_TDC;


        //DISPARADOR BEGIN
        if ($estado == 2) {
        	if($codigo!=null &&  $codigo!=0){
        		if($query = $this->db->query("CALL COMPROBANTE_DISPARADOR($codigo)"))
        		{
        			print_r($query->row());
        		}else{
        			show_error('Error!');
        		}
        	}
        }
// DISPARADOR END
//**************************************************************************
        if ($_SESSION['empresa'] == '3') {
//3 = dimesarty
//2 = ccmi       

            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = "";
                    //$notimg = "factura1.jpg";
                } else if ($img == 0) {
                    $notimg = "factura1.jpg";
                    //$notimg = "factura1.jpg";
                }
            } else {
                if ($img == 1) {
                    $notimg = "";
                } else if ($img == 0) {
                    $notimg = "factura_proveedor_1.jpg";
                }
            }

            if ($tipo_oper == 'V') {


                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $igv100 = $datos_comprobante[0]->CPC_igv100;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
                $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                $persona = $datos_proveedor[0]->PERSP_Codigo;
                $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }

                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;

                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    $ruc = $datos_persona[0]->PERSC_Ruc;
                    $telefono = $datos_persona[0]->PERSC_Telefono;
                    $movil = $datos_persona[0]->PERSC_Movil;
                    $direccion = $datos_persona[0]->PERSC_Direccion;
                    $fax = $datos_persona[0]->PERSC_Fax;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                    $ruc = $datos_empresa[0]->EMPRC_Ruc;
                    $telefono = $datos_empresa[0]->EMPRC_Telefono;
                    $movil = $datos_empresa[0]->EMPRC_Movil;
                    $fax = $datos_empresa[0]->EMPRC_Fax;
                    $direccion = $datos_empresa[0]->EMPRC_Direccion;
                    $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                }
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);

                if ($_SESSION['compania'] == '3') {
//dragon yuan mafgdalena
                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));

                    /* Cabecera */


                    //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

                    ///***************1era fecha    
                    ///***************fecha en letras
                    $posiciongeneralx = 0;
                    $posiciongeneraly = 4;

                    /*$this->cezpdf->addText($posiciongeneralx+365, $posiciongeneraly+720, 15, $serie);
            $this->cezpdf->addText($posiciongeneralx+430, $posiciongeneraly+720, 15, $numero);*/

                    $fecha_dia = substr($fecha, 0, 2);
                    $fecha_mes = mes_textual(substr($fecha, 3, 2));
                    $fecha_año = substr($fecha, 8, 8);

                    $this->cezpdf->addText($posiciongeneralx + 365, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_dia));
                    $this->cezpdf->addText($posiciongeneralx + 430, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_mes));
                    $this->cezpdf->addText($posiciongeneralx + 530, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_año));
                    //****************1era fecha

                    //****************2da fecha 
                    if ($nombre_formapago != 'CONTADO') {
                        $fecha_text2 = '';
                    } else {
                        $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '                    ' . mes_textual(substr($fecha, 3, 2)) . '                          ' . substr($fecha, 8, 4));
                    }

                    $this->cezpdf->addText($posiciongeneralx + 213, $posiciongeneraly + 443, 9, utf8_decode_seguro($fecha_text2));
                    //****************2da fecha

                    ///***************datos de empresa
                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 699, 8, utf8_decode_seguro($nombre_cliente));
                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 683, 6, utf8_decode_seguro($direccion));
                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 669, 8, utf8_decode_seguro($ruc));
                    //$this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
                    ///***************datos de empresa

                    // Num Ref Guia Remision
                    $this->cezpdf->addText($posiciongeneralx + 415, $posiciongeneraly + 682, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));


                    /* Listado de detalles */


                    $db_data = array();
                    $i = 632;
                    foreach ($detalle_comprobante as $indice => $valor) {
                        $c = 0;
                        $array_producto = explode('/', $valor->PROD_Nombre);
                        $producto = $valor->PROD_CodigoUsuario;

                        $unidad = $valor->UNDMED_Simbolo;
                        if ($valor->CPDEC_Pu_ConIgv != '')
                            $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                        else
                            $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;




                    $this->cezpdf->addText(88 + $posiciongeneralx, $i + $posiciongeneraly, 7, utf8_decode_seguro($valor->PROD_Nombre));
                    $this->cezpdf->addText(55 + $posiciongeneralx, $i + $posiciongeneraly, 7, $valor->CPDEC_Cantidad);
                    $this->cezpdf->addText(420 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(425 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($pu_conigv, 2), 'right');
                    $this->cezpdf->addText(480 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(490 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($valor->CPDEC_Total, 2), 'right');

                        $i -= 17;


                    }


                    /* Inicio Totales */
                    // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                    /* inicio Total EN LETRAS */
                    $this->cezpdf->addText(88 + $posiciongeneralx, 466 + $posiciongeneraly, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                    $this->cezpdf->addText(488 + $posiciongeneralx, 448 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 448 + $posiciongeneraly, 45, 7, number_format($subtotal - $descuento, 2), 'right');

                    $this->cezpdf->addText(488 + $posiciongeneralx, 431 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 431 + $posiciongeneraly, 45, 7, number_format($igv, 2), 'right');
                    $this->cezpdf->addText(488 + $posiciongeneralx, 413 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 413 + $posiciongeneraly, 45, 7, number_format($total, 2), 'right');

                    $this->cezpdf->addTextWrap(413 + $posiciongeneralx, 431 + $posiciongeneraly, 45, 7, $igv100, 'right');


                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 685, 8, 'ESTA LISTA CORRESPONDE A');
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 645, 8, 'FACTURA');
                    $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 645, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralx + 275, $posiciongeneraly + 585, 8, 'LISTA DE IMEIS');

                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 96);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText($posiciongeneralx + 100, $posiciongeneraly + 560 - ($i * 13), 8, substr($observacion, $i * 96, 96));
                    }

                    /* Fin Totales */
                    $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                    $this->cezpdf->ezStream($cabecera);

                } else {
//$_SESSION['compania']=='6' dragon yuan andahuaylas

                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));


                    //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

                    ///***************1era fecha    
                    ///***************fecha en letras
                    $posiciongeneralx = 12;
                    $posiciongeneraly = -23;

                    /*$this->cezpdf->addText($posiciongeneralx+365, $posiciongeneraly+740, 15, $serie);
            $this->cezpdf->addText($posiciongeneralx+430, $posiciongeneraly+740, 15, $numero);*/

                    $fecha_dia = substr($fecha, 0, 2);
                    $fecha_mes = mes_textual(substr($fecha, 3, 2));
                    $fecha_año = substr($fecha, 8, 8);

                    $this->cezpdf->addText($posiciongeneralx + 353, $posiciongeneraly + 682, 10, utf8_decode_seguro($fecha_dia));
                    $this->cezpdf->addText($posiciongeneralx + 410, $posiciongeneraly + 682, 10, utf8_decode_seguro($fecha_mes));
                    $this->cezpdf->addText($posiciongeneralx + 523, $posiciongeneraly + 682, 10, utf8_decode_seguro($fecha_año));
                    //****************1era fecha

                    //****************2da fecha 
                    if ($nombre_formapago != 'CONTADO') {
                        $fecha_text2 = '';
                    } else {
                        $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '                    ' . mes_textual(substr($fecha, 3, 2)) . '                                  ' . substr($fecha, 8, 4));
                    }

                    $this->cezpdf->addText($posiciongeneralx + 200, $posiciongeneraly + 456, 8, utf8_decode_seguro($fecha_text2));
                    //****************2da fecha

                    ///***************datos de empresa
                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 714, 9, utf8_decode_seguro($nombre_cliente));
                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 699, 9, utf8_decode_seguro(substr($direccion, 0, 50)));
                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 800, 9, utf8_decode_seguro($ruc));
                    //$this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
                    ///***************datos de empresa

                    // Num Ref Guia Remision
                    $this->cezpdf->addText($posiciongeneralx + 415, $posiciongeneraly + 699, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));

                    //$this->cezpdf->addText(270, 706, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
                    //$this->cezpdf->addText(480, 610, 9, utf8_decode_seguro($nombre_formapago));
                    //$this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));


                    /* Listado de detalles */

                    $db_data = array();
                    $i = 646;
                    foreach ($detalle_comprobante as $indice => $valor) {
                        $c = 0;
                        $array_producto = explode('/', $valor->PROD_Nombre);
                        $producto = $valor->PROD_CodigoUsuario;

                        $unidad = $valor->UNDMED_Simbolo;
                        if ($valor->CPDEC_Pu_ConIgv != '')
                            $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                        else
                            $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;

                        $this->cezpdf->addText(88 + $posiciongeneralx, $i + $posiciongeneraly, 9, utf8_decode_seguro($valor->PROD_Nombre));
                        $this->cezpdf->addText(55 + $posiciongeneralx, $i + $posiciongeneraly, 9, $valor->CPDEC_Cantidad);
                        $this->cezpdf->addText(410 + $posiciongeneralx, $i + $posiciongeneraly, 9, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(412 + $posiciongeneralx, $i + $posiciongeneraly, 45, 9, number_format($pu_conigv, 2), 'right');
                        $this->cezpdf->addText(480 + $posiciongeneralx, $i + $posiciongeneraly, 9, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(490 + $posiciongeneralx, $i + $posiciongeneraly, 45, 9, number_format($valor->CPDEC_Total, 2), 'right');

                        $i -= 16;

                    }


                    /* Inicio Totales */
                    // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                    /* inicio Total EN LETRAS */
                    $this->cezpdf->addText($posiciongeneralx + 75, $posiciongeneraly + 480, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                    /* fin Total EN LETRAS */
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
                    //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));

                    $this->cezpdf->addText(487 + $posiciongeneralx, 463 + $posiciongeneraly, 9, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(488 + $posiciongeneralx, 463 + $posiciongeneraly, 45, 9, number_format($subtotal - $descuento, 2), 'right');


                    $this->cezpdf->addText(488 + $posiciongeneralx, 445 + $posiciongeneraly, 9, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 445 + $posiciongeneraly, 45, 9, number_format($igv, 2), 'right');
                    $this->cezpdf->addText(488 + $posiciongeneralx, 427 + $posiciongeneraly, 9, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 427 + $posiciongeneraly, 45, 9, number_format($total, 2), 'right');
                    $this->cezpdf->addTextWrap(405 + $posiciongeneralx, 445 + $posiciongeneraly, 45, 9, $igv100, 'right');


                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 685, 8, 'ESTA LISTA CORRESPONDE A');
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 645, 8, 'FACTURA');
                    $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 645, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralx + 275, $posiciongeneraly + 585, 8, 'LISTA DE IMEIS');

                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 96);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText($posiciongeneralx + 100, $posiciongeneraly + 560 - ($i * 13), 8, substr($observacion, $i * 96, 96));
                    }

                    /* Fin Totales */
                    $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                    $this->cezpdf->ezStream($cabecera);

                }


            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $datos_comprobante[0]->CPC_total;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
                $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
                $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));

//         

                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
                $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
                $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha, 0, 2)), 9, array("leading" => 0, "left" => 458));
                $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 472));
                $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" => 515));
                $this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
                $this->cezpdf->ezText($serie, 18, array("leading" => -55, 'left' => 395));
                $this->cezpdf->ezText($numero, 18, array("leading" => 0, 'left' => 440));
                /* Listado de detalles */
                $posicionX = 0;
                $posicionY = 640;
                $db_data = array();

                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;

//                $ser = "";
//                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);

                    $posicionX = 10;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
                    $posicionX += 30;
                    $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
                    $posicionX += 30;
                    $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($array_producto[0]));
//                $this->cezpdf->addText(120, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    $posicionX += 380;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
                    $posicionX += 35;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));
                    $posicionX += 55;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));


//
//                if (count($datos_serie) > 0) {
//                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
//                    for ($i = 0; $i < count($datos_serie); $i++) {
//                        $c = $c + 1;
//                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
//
//                        $ser = $ser . " /" . $seriecodigo;
//
//                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
//                        if ($c == 8) {
//                            $posicionY-=10;
//                            $c = 0;
//                            $ser = "";
//                        }
//                    }
//                }
                    $posicionY -= 40;
                }
                /* Totales */
                $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

                $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
                $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));

                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
            }
//********************
        }
        else {
//********************
            // DISPARADOR END FACTURA IMPRIMIR
            

            // TODO - VENTAS  FACTURA IMPRIMIR
            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = "";//comentas para mostrar imagen
                    //$notimg = "factura1.jpg";
                } else if ($img == 0) {
                   // $notimg = "";
                    $notimg = "factura1.jpg";
                }
                // TODO - COMPRAS
            }
            //COMPRA FACTURA IMPRIMIR
             else {
                if ($img == 1) {
                    $notimg = "";
                } else if ($img == 0) {
                    $notimg = "factura_proveedor_1.jpg";
                }
            }

            if ($tipo_oper == 'V') {


                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                //$numero = $datos_comprobante[0]->CPC_Numero;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $igv100 = $datos_comprobante[0]->CPC_igv100;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
                $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                $persona = $datos_proveedor[0]->PERSP_Codigo;
                $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }

                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;

                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    $ruc = $datos_persona[0]->PERSC_Ruc;
                    $telefono = $datos_persona[0]->PERSC_Telefono;
                    $movil = $datos_persona[0]->PERSC_Movil;
                    $direccion = $datos_persona[0]->PERSC_Direccion;
                    $fax = $datos_persona[0]->PERSC_Fax;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                    $ruc = $datos_empresa[0]->EMPRC_Ruc;
                    $telefono = $datos_empresa[0]->EMPRC_Telefono;
                    $movil = $datos_empresa[0]->EMPRC_Movil;
                    $fax = $datos_empresa[0]->EMPRC_Fax;
                    $direccion = $datos_empresa[0]->EMPRC_Direccion;
                    $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                }
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                /*     
if($_SESSION['compania']=='1'){	
//magdalena
            $posiciongeneralx=10;
            $posiciongeneraly=-4;
			}
                        
                        else{
			$posiciongeneralx=18;
            $posiciongeneraly=-6;
			}*/

                  $posiciongeneralx = 0;
                $posiciongeneraly = 52;
                //$posiciongeneralx = -35;
                //$posiciongeneraly = -75;
//$this->cezpdf->ezText($serie, 20, array("leading" => -30 - $posiciongeneraly, 'left' => 390));
                    //$this->cezpdf->ezText('005' . $numero, 25, array("leading" => -110, 'left' => 400));
                    
                /* Cabecera */
               
                $this->cezpdf->ezText('', '', array('leading' => 203));
                if ($img != 1) {
                   // $this->cezpdf->ezText($serie.'-' , 22, array("leading" => -108, 'left' => 390));
            $this->cezpdf->ezText('0005'.$numero, 25, array("leading" => -110, 'left' => 400));//SERIE LA PRIMERA
                }else {
                    $this->cezpdf->ezText('', 15, array("leading" => -110, 'left' => 330));
                    $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 390));
                }
                $this->cezpdf->ezText('', '', array("leading" => 40));
                if ($nombre_formapago != 'CONTADO') {
                    $fecha_text2 = '';
                } else {
                    $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '         ' . substr($fecha, 3, 2) . '        ' . substr($fecha, 8, 4));
                }




                //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

                //CODIGO PARA FACTURA DE IMPRIMIR
                ///***************fecha en letras
                $fecha_dia = substr($fecha, 0, 2);
                $fecha_mes = fechaALetras($fecha);
                $fecha_año = substr($fecha, 8, 8);

                //$this->cezpdf->addText(190 + $posiciongeneralx, 760 + $posiciongeneraly, 9, utf8_decode_seguro($fecha_dia));
                //$this->cezpdf->addText(207 + $posiciongeneralx, 760 + $posiciongeneraly, 9, utf8_decode_seguro($fecha_mes));
                //$this->cezpdf->addText(232 + $posiciongeneralx, 760 + $posiciongeneraly, 9, utf8_decode_seguro($fecha_año));

                $this->cezpdf->addText($posiciongeneralx + 60, $posiciongeneraly + 609, 8, utf8_decode_seguro($fecha_dia));

                $this->cezpdf->addText($posiciongeneralx + 80, $posiciongeneraly + 609, 8, utf8_decode_seguro($fecha_mes));

                $this->cezpdf->addText($posiciongeneralx + 130, $posiciongeneraly + 609, 8, utf8_decode_seguro("20" . $fecha_año));
                //**************************************
                // $this->cezpdf->addText(180+$posiciongeneralx, 436+$posiciongeneraly, 9, utf8_decode_seguro($fecha_text2));
//nombre de vendedor imprimir factura
                $this->cezpdf->addText(260, 660, 7, utf8_decode_seguro($vendedor));
                $this->cezpdf->addText(490, 630, 7, utf8_decode_seguro($nombre_formapago));

//      $this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));
                //$this->cezpdf->addText(380+$posiciongeneralx, 610+$posiciongeneraly, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));
//$this->cezpdf->addText(500,200,30,'HOLAAAA');

                 $this->cezpdf->addText($posiciongeneralx + 73, $posiciongeneraly + 562, 9, utf8_decode_seguro($ruc));
                $this->cezpdf->addText($posiciongeneralx + 70, $posiciongeneraly + 593, 7, utf8_decode_seguro($nombre_cliente));
                //DIRECCION
                $this->cezpdf->addText($posiciongeneralx + 70, $posiciongeneraly + 580, 7, substr($direccion,0,62));

//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
              //$this->cezpdf->addText(93 + $posiciongeneralx, 708 + $posiciongeneraly, 9, utf8_decode_seguro($ruc));
                //$this->cezpdf->addText(91 + $posiciongeneralx, 748 + $posiciongeneraly, 9, utf8_decode_seguro($nombre_cliente));
                //$this->cezpdf->addText(93 + $posiciongeneralx, 726 + $posiciongeneraly, 9, utf8_decode_seguro($direccion));
//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));

                /* Listado de detalles */
                $posicionX = 0;
                $posicionY = 600;
                $db_data = array();
                $i =720;
                //$i = 662;
                $prod_nombreimei = '';
                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;
                    $posicionX = 10;
                    $unidad = $valor->UNDMED_Simbolo;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
//                $this->cezpdf->addText(50, $i, 9, $producto);

                    ///aumentado stv
                    //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    // $this->cezpdf->addText(95+$posiciongeneralx, 676+$posiciongeneraly, 9, utf8_decode_seguro($nombre_cliente));
                    //DESCRIPCION
                    $this->cezpdf->addText(135, $i-160, 6, substr($valor->PROD_Nombre,0,70));
                   //- $this->cezpdf->addText(112 + $posiciongeneralx, $i + $posiciongeneraly, 9, utf8_decode_seguro($valor->PROD_Nombre));

                   //- $prod_nombreimei = utf8_decode_seguro($valor->PROD_Nombre) . ' / ' . $prod_nombreimei;
 //UNIDA EN LETRA FACTURA IMPRIMIR
                $this->cezpdf->addText(100, $i-160, 6, utf8_decode_seguro($unidad));
                    ///

       //   $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
                  //$this->cezpdf->addText(70 + $posiciongeneralx, $i + $posiciongeneraly, 9, $valor->CPDEC_Cantidad);
                   //- $this->cezpdf->addText(500 + $posiciongeneralx, $i + $posiciongeneraly, 9, $moneda_simbolo);
                    //-$this->cezpdf->addTextWrap(503 + $posiciongeneralx, $i + $posiciongeneraly, 35, 9, number_format($pu_conigv, 2), 'right');
                    //-$this->cezpdf->addText(570 + $posiciongeneralx, $i + $posiciongeneraly, 9, $moneda_simbolo);
                    //-$this->cezpdf->addTextWrap(571 + $posiciongeneralx, $i + $posiciongeneraly, 45, 9, number_format($valor->CPDEC_Total, 2), 'right');

                    //$this->cezpdf->addText(520+$posiciongeneralx, $i+$posiciongeneraly, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                   //- $i -= 18;

                    //-$posicionY -= 17;
//CANTIDAD EN NUMERO FACTURA IMPRIMIR
                     $this->cezpdf->addText(65, $i-160, 8, $valor->CPDEC_Cantidad);

                    $this->cezpdf->addText(460, $i-165, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(460, $i-165, 35, 8, number_format($pu_conigv, 2), 'right');
                    //$this->cezpdf->addTextWrap(470, $i, 40, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2), "right");//revisar http://pubsvn.ez.no/doxygen/4.0/html/classCpdf.html#a4c3091f0936a733aa7e7ff98b876f3b1
                    $this->cezpdf->addText(515, $i-165, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(510, $i-165, 35, 8, number_format($valor->CPDEC_Total, 2), 'right');
                    //$this->cezpdf->addTextWrap(520, $i, 44, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2),"right");
                    $i -= 18;

                    $posicionY -= 30;
                }


                /* Totales */
                // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
               //- $this->cezpdf->addText(66 + $posiciongeneralx, 395 + $posiciongeneraly, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                //SOLES EN LETRA
                  $this->cezpdf->addText($posiciongeneralx + 75, $posiciongeneraly + 75, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
                //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));
              //-  $this->cezpdf->addText(570 + $posiciongeneralx, 366 + $posiciongeneraly, 9, $moneda_simbolo);
               //- $this->cezpdf->addTextWrap(570 + $posiciongeneralx, 366 + $posiciongeneraly, 45, 9, (number_format($subtotal - $descuento, 2)), 'right');
               //- $this->cezpdf->addText(570 + $posiciongeneralx, 343 + $posiciongeneraly, 9, $moneda_simbolo);
                //$this->cezpdf->addText(420+$posiciongeneralx, 426+$posiciongeneraly, 9, $igv100);
                //-$this->cezpdf->addTextWrap(570 + $posiciongeneralx, 343 + $posiciongeneraly, 45, 9, number_format($igv, 2), 'right');
                //-$this->cezpdf->addText(570 + $posiciongeneralx, 321 + $posiciongeneraly, 9, $moneda_simbolo);
                //-$this->cezpdf->addTextWrap(570 + $posiciongeneralx, 321 + $posiciongeneraly, 45, 9, number_format(($total), 2), 'right');
                $this->cezpdf->addText($posiciongeneralx + 64, $posiciongeneraly + 40, 7, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 64, $posiciongeneraly + 40, 40, 8, number_format($subtotal - $descuento, 2), 'right');

                $this->cezpdf->addText($posiciongeneralx + 415, $posiciongeneraly + 35, 7, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 415, $posiciongeneraly + 35, 35, 8, number_format($igv, 2), 'right');

                $this->cezpdf->addText($posiciongeneralx + 525, $posiciongeneraly +35, 7, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 525, $posiciongeneraly +35, 35, 8, number_format(( $total), 2), 'right');

                /*
    FINALDE IMPRE
			$this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));   
            $this->cezpdf->addText( 90, 705, 8, 'ESTA LISTA CORRESPONDE A');
            $this->cezpdf->addText(90,685, 8, 'FACTURA');
			$this->cezpdf->addText(250, 685, 8, $numero.' Nro '.$serie);
			
			/*$this->cezpdf->addText(90, 665, 8, 'BOLETA');
			$this->cezpdf->addText(250, 665, 8, $numero_ref);
			
            $this->cezpdf->addText(90,  645, 8, 'GUIA DE REMISION');
            $this->cezpdf->addText(250, 645, 8, $docurefe_codigo );


            $this->cezpdf->addText(275, 585, 8, 'LISTA DE IMEIS');
			
            $this->cezpdf->addText(90, 620, 6,  utf8_decode_seguro($prod_nombreimei));*/


                $valortotal = strlen($observacion);
                // strlen se obtiene la longitud de caracteres
                $exacta = round($valortotal / 112);
                // obtiene el numero entero de la operacion
                for ($i = 0; $i < $exacta; $i++) {
                    $this->cezpdf->addText(90, 560 - ($i * 10), 7, substr($observacion, $i * 112, 112));
                }


                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $datos_comprobante[0]->CPC_total;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
               // $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
               // $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));

//         
//COMPRA FACTURA IMPRIMIR INI
                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
                //$this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
//FECHAS COMPRA FACTURA 
                $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha, 0, 2)), 9, array("leading" =>150, "left" => 35));

                $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 50));

                $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" =>100));
                //$this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
                $this->cezpdf->ezText($serie, 18, array("leading" => -55, 'left' => 400 ) );
               $this->cezpdf->ezText(  ' / '.$numero,18, array("leading" => 0, 'left' => 420));
                /* Listado de detalles */
                $posicionX = 0;
                $posicionY = 550;
                $db_data = array();

                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;

//                $ser = "";
//                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);

                    $posicionX = 10;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
                   
                   //CODIGO
                    $posicionX += 43;
                    $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);

                    //CANTIDAD
                    $posicionX += 15;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
                   
                    $posicionX += 60;
                    $this->cezpdf->addText($posicionX, $posicionY, 6, utf8_decode_seguro($valor->PROD_Nombre));
//                $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($array_producto[0]));
//                $this->cezpdf->addText(120, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    //PRECIOS
                    $posicionX += 306;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));
                   
                    $posicionX += 90;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));


//
//                if (count($datos_serie) > 0) {
//                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
//                    for ($i = 0; $i < count($datos_serie); $i++) {
//                        $c = $c + 1;
//                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
//
//                        $ser = $ser . " /" . $seriecodigo;
//
//                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
//                        if ($c == 8) {
//                            $posicionY-=10;
//                            $c = 0;
//                            $ser = "";
//                        }
//                    }
//                }
                    $posicionY -= 40;
                }
                /* Totales */

                //$this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");

                $this->cezpdf->addText(60, 126, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

                //$this->cezpdf->addText(40, 100, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                //$this->cezpdf->addText(150, 100, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));
//LOS PRCIOS
                $this->cezpdf->addText(280, 100, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
                $this->cezpdf->addText(400, 100, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 100, 9, $moneda_simbolo . ' ' . number_format(($total), 2));

                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
            }


        }
//**************************************************************************


    }


    public function comprobante_ver_pdf_conmenbrete_formato11($tipo_oper, $codigo, $tipo_docu = 'F', $img)
    {
//**************************************************************************
        if ($_SESSION['empresa'] == '3') {



            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = "";
                } else if ($img == 0) {

                    if ($_SESSION['compania'] == '3') {
                        //dragon yuan magdalena
                        $notimg = "dragonYuan_factura1.jpg";
                    } else {
                        $notimg = "factur_yuam_andahuaylas.jpg";
                    }

                }
            } else {
                if ($img == 1) {
                    $notimg = "";
                } else if ($img == 0) {
                    $notimg = "factura_proveedor_1.jpg";
                }
            }

            if ($tipo_oper == 'V') {


                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $igv100 = $datos_comprobante[0]->CPC_igv100;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
                $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                $persona = $datos_proveedor[0]->PERSP_Codigo;
                $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }

                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;

                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    $ruc = $datos_persona[0]->PERSC_Ruc;
                    $telefono = $datos_persona[0]->PERSC_Telefono;
                    $movil = $datos_persona[0]->PERSC_Movil;
                    $direccion = $datos_persona[0]->PERSC_Direccion;
                    $fax = $datos_persona[0]->PERSC_Fax;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                    $ruc = $datos_empresa[0]->EMPRC_Ruc;
                    $telefono = $datos_empresa[0]->EMPRC_Telefono;
                    $movil = $datos_empresa[0]->EMPRC_Movil;
                    $fax = $datos_empresa[0]->EMPRC_Fax;
                    $direccion = $datos_empresa[0]->EMPRC_Direccion;
                    $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                }
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);

                if ($_SESSION['compania'] == '3') {
//dragon yuan mafgdalena			
                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                    //$this->cezpdf->selectFont(APPPATH.'libraries/fonts/Courier.afm');
                    /* Cabecera */


                    //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

                    ///***************1era fecha    
                    ///***************fecha en letras
                    $posiciongeneralx = 0;
                    $posiciongeneraly = 0;

                    $this->cezpdf->addText($posiciongeneralx + 365, $posiciongeneraly + 720, 15, $serie);
                    $this->cezpdf->addText($posiciongeneralx + 430, $posiciongeneraly + 720, 15, $this->getOrderNumeroSerie($numero));

                    $fecha_dia = substr($fecha, 0, 2);
                    $fecha_mes = mes_textual(substr($fecha, 3, 2));
                    $fecha_año = substr($fecha, 8, 8);

                    $this->cezpdf->addText($posiciongeneralx + 365, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_dia));
                    $this->cezpdf->addText($posiciongeneralx + 430, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_mes));
                    $this->cezpdf->addText($posiciongeneralx + 530, $posiciongeneraly + 669, 10, utf8_decode_seguro($fecha_año));
                    //****************1era fecha

                    //****************2da fecha 
                    if ($nombre_formapago != 'CONTADO') {
                        $fecha_text2 = '';
                    } else {
                        $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '                    ' . mes_textual(substr($fecha, 3, 2)) . '                          ' . substr($fecha, 8, 4));
                    }

                    $this->cezpdf->addText($posiciongeneralx + 213, $posiciongeneraly + 443, 9, utf8_decode_seguro($fecha_text2));
                    //****************2da fecha

                    ///***************datos de empresa

                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 699, 8, utf8_decode_seguro($nombre_cliente));
                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 683, 8, utf8_decode_seguro(strtoupper($direccion)));
                    $this->cezpdf->addText($posiciongeneralx + 95, $posiciongeneraly + 669, 8, utf8_decode_seguro($ruc));
                    //$this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
                    ///***************datos de empresa

                    // Num Ref Guia Remision
                    $this->cezpdf->addText($posiciongeneralx + 415, $posiciongeneraly + 682, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));


                    /* Listado de detalles */


                    $db_data = array();
                    $i = 632;
                    foreach ($detalle_comprobante as $indice => $valor) {
                        $c = 0;
                        $array_producto = explode('/', $valor->PROD_Nombre);
                        $producto = $valor->PROD_CodigoUsuario;

                        $unidad = $valor->UNDMED_Simbolo;
                        if ($valor->CPDEC_Pu_ConIgv != '')
                            $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                        else
                            $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;


                        $this->cezpdf->addText(88 + $posiciongeneralx, $i + $posiciongeneraly, 7, utf8_decode_seguro($valor->PROD_Nombre));
                        $this->cezpdf->addText(55 + $posiciongeneralx, $i + $posiciongeneraly, 7, $valor->CPDEC_Cantidad);
                        $this->cezpdf->addText(420 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(425 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($pu_conigv, 2), 'right');
                        $this->cezpdf->addText(480 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(490 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($valor->CPDEC_Total, 2), 'right');

                        $i -= 17;


                    }


                    /* Inicio Totales */
                    // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                    /* inicio Total EN LETRAS */

                    $this->cezpdf->addText(488 + $posiciongeneralx, 448 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 448 + $posiciongeneraly, 45, 7, number_format($subtotal - $descuento, 2), 'right');

                    $this->cezpdf->addText(488 + $posiciongeneralx, 431 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 431 + $posiciongeneraly, 45, 7, number_format($igv, 2), 'right');
                    $this->cezpdf->addText(488 + $posiciongeneralx, 413 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 413 + $posiciongeneraly, 45, 7, number_format($total, 2), 'right');
                    $this->cezpdf->addTextWrap(413 + $posiciongeneralx, 431 + $posiciongeneraly, 45, 7, $igv100, 'right');


                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 685, 8, 'ESTA LISTA CORRESPONDE A');
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 645, 8, 'FACTURA');
                    $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 645, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralx + 275, $posiciongeneraly + 585, 8, 'LISTA DE IMEIS');

                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 96);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText($posiciongeneralx + 100, $posiciongeneraly + 560 - ($i * 13), 8, substr($observacion, $i * 96, 96));
                    }

                    /* Fin Totales */
                    $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                    $this->cezpdf->ezStream($cabecera);

                } else {
//$_SESSION['compania']=='6' dragon yuan andahuaylas

                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));


                    //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

                    ///***************1era fecha    
                    ///***************fecha en letras
                    $posiciongeneralx = 0;
                    $posiciongeneraly = 0;

                    $this->cezpdf->addText($posiciongeneralx + 365, $posiciongeneraly + 740, 15, $serie);
                    $this->cezpdf->addText($posiciongeneralx + 430, $posiciongeneraly + 740, 15, $numero);

                    $fecha_dia = substr($fecha, 0, 2);
                    $fecha_mes = mes_textual(substr($fecha, 3, 2));
                    $fecha_año = substr($fecha, 8, 8);

                    $this->cezpdf->addText($posiciongeneralx + 350, $posiciongeneraly + 680, 8, utf8_decode_seguro($fecha_dia));
                    $this->cezpdf->addText($posiciongeneralx + 410, $posiciongeneraly + 680, 8, utf8_decode_seguro($fecha_mes));
                    $this->cezpdf->addText($posiciongeneralx + 515, $posiciongeneraly + 680, 8, utf8_decode_seguro($fecha_año));
                    //****************1era fecha

                    //****************2da fecha 
                    if ($nombre_formapago != 'CONTADO') {
                        $fecha_text2 = '';
                    } else {
                        $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '                    ' . mes_textual(substr($fecha, 3, 2)) . '                                  ' . substr($fecha, 8, 4));
                    }

                    $this->cezpdf->addText($posiciongeneralx + 200, $posiciongeneraly + 462, 8, utf8_decode_seguro($fecha_text2));
                    //****************2da fecha

                    ///***************datos de empresa

                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 710, 8, utf8_decode_seguro($nombre_cliente));
                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 695, 8, utf8_decode_seguro(substr($direccion, 0, 50)));
                    $this->cezpdf->addText($posiciongeneralx + 88, $posiciongeneraly + 400, 8, utf8_decode_seguro($ruc));
                    //$this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
                    ///***************datos de empresa

                    // Num Ref Guia Remision
                    $this->cezpdf->addText($posiciongeneralx + 415, $posiciongeneraly + 695, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));

                    //$this->cezpdf->addText(270, 706, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
                    //$this->cezpdf->addText(480, 610, 9, utf8_decode_seguro($nombre_formapago));
                    //$this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));


                    /* Listado de detalles */

                    $db_data = array();
                    $i = 646;
                    foreach ($detalle_comprobante as $indice => $valor) {
                        $c = 0;
                        $array_producto = explode('/', $valor->PROD_Nombre);
                        $producto = $valor->PROD_CodigoUsuario;

                        $unidad = $valor->UNDMED_Simbolo;
                        if ($valor->CPDEC_Pu_ConIgv != '')
                            $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                        else
                            $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;

                        $this->cezpdf->addText(88 + $posiciongeneralx, $i + $posiciongeneraly, 7, utf8_decode_seguro($valor->PROD_Nombre));
                        $this->cezpdf->addText(55 + $posiciongeneralx, $i + $posiciongeneraly, 7, $valor->CPDEC_Cantidad);
                        $this->cezpdf->addText(410 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(412 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($pu_conigv, 2), 'right');
                        $this->cezpdf->addText(480 + $posiciongeneralx, $i + $posiciongeneraly, 7, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(490 + $posiciongeneralx, $i + $posiciongeneraly, 45, 7, number_format($valor->CPDEC_Total, 2), 'right');

                        $i -= 16;

                    }


                    /* Inicio Totales */
                    // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                    /* inicio Total EN LETRAS */
                    $this->cezpdf->addText($posiciongeneralx + 75, $posiciongeneraly + 485, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                    /* fin Total EN LETRAS */
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
                    //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));

                    $this->cezpdf->addText(488 + $posiciongeneralx, 468 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 468 + $posiciongeneraly, 45, 7, number_format($subtotal - $descuento, 2), 'right');


                    $this->cezpdf->addText(488 + $posiciongeneralx, 450 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 450 + $posiciongeneraly, 45, 7, number_format($igv, 2), 'right');
                    $this->cezpdf->addText(488 + $posiciongeneralx, 432 + $posiciongeneraly, 7, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(489 + $posiciongeneralx, 432 + $posiciongeneraly, 45, 7, number_format($total, 2), 'right');
                    $this->cezpdf->addTextWrap(400 + $posiciongeneralx, 450 + $posiciongeneraly, 45, 7, $igv100, 'right');


                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 685, 8, 'ESTA LISTA CORRESPONDE A');
                    $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 645, 8, 'FACTURA');
                    $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 645, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralx + 275, $posiciongeneraly + 585, 8, 'LISTA DE IMEIS');

                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 96);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText($posiciongeneralx + 100, $posiciongeneraly + 560 - ($i * 13), 8, substr($observacion, $i * 96, 96));
                    }

                    /* Fin Totales */
                    $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                    $this->cezpdf->ezStream($cabecera);


                }


            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $datos_comprobante[0]->CPC_total;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
                $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
                $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));

                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
                $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
                $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha, 0, 2)), 9, array("leading" => 0, "left" => 458));
                $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 472));
                $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" => 515));
                $this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
                $this->cezpdf->ezText($serie, 18, array("leading" => -55, 'left' => 395));
                $this->cezpdf->ezText($numero, 18, array("leading" => 0, 'left' => 440));
                /* Listado de detalles */
                $posicionX = 0;
                $posicionY = 640;
                $db_data = array();

                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;

//                $ser = "";
//                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);

                    $posicionX = 10;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
                    $posicionX += 30;
                    $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
                    $posicionX += 30;

                    ///aumentado stv
                    //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));

                    $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    ///

//                $this->cezpdf->addText($posicionX, $posicionY, 9, utf8_decode_seguro($array_producto[0]));
                    $posicionX += 380;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
                    $posicionX += 35;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));
                    $posicionX += 55;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));


//
//                if (count($datos_serie) > 0) {
//                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
//                    for ($i = 0; $i < count($datos_serie); $i++) {
//                        $c = $c + 1;
//                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
//
//                        $ser = $ser . " /" . $seriecodigo;
//
//                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
//                        if ($c == 8) {
//                            $posicionY-=10;
//                            $c = 0;
//                            $ser = "";
//                        }
//                    }
//                }
                    $posicionY -= 40;
                }
                /* Totales */
                $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

                $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
                $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));

                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
            }


//************************
        } else {
//************************
            
///imagen de factura1 FACTURA VENTA PDF
            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = "factura1.jpg";
                    //$notimg = "";
                } else if ($img == 0) {
                   
                    $notimg = "factura1.jpg";
                }
            } else {
                if ($img == 1) {
                    $notimg = "";
                } else if ($img == 0) {
                    $notimg = "factura_proveedor_1.jpg";
                }
            }
            if ($tipo_oper == 'V') {


                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $igv100 = $datos_comprobante[0]->CPC_igv100;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
                $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                $persona = $datos_proveedor[0]->PERSP_Codigo;
                $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }

                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;

                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);

                if ($tipo == 0) {
                    $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                    $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    $ruc = $datos_persona[0]->PERSC_Ruc;
                    $telefono = $datos_persona[0]->PERSC_Telefono;
                    $movil = $datos_persona[0]->PERSC_Movil;
                    $direccion = $datos_persona[0]->PERSC_Direccion;
                    $fax = $datos_persona[0]->PERSC_Fax;
                } elseif ($tipo == 1) {
                    $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                    $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                    $ruc = $datos_empresa[0]->EMPRC_Ruc;
                    $telefono = $datos_empresa[0]->EMPRC_Telefono;
                    $movil = $datos_empresa[0]->EMPRC_Movil;
                    $fax = $datos_empresa[0]->EMPRC_Fax;
                    $direccion = $datos_empresa[0]->EMPRC_Direccion;
                    $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                }
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);

                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                // Sirve para cambiar el tipo de letra
                //$this->cezpdf->selectFont(APPPATH.'libraries/fonts/Courier.afm');
        //MODIFICAR EL CONTENIDO DEL PDF IMPRIMIR
                $posiciongeneralx = 0;
                $posiciongeneraly = 52;

                /* Cabecera */
                $this->cezpdf->ezText('', '', array('leading' => 203));
                if ($img != 1) {
                   // $this->cezpdf->ezText($serie.'-' , 22, array("leading" => -108, 'left' => 390));
//SERIE NUMERO FACTURA PDF LA PRIMERA
            $this->cezpdf->ezText( $serie .'-' . $this->getOrderNumeroSerie($numero), 25, array("leading" => -110, 'left' =>370));//SERIE LA PRIMERA
                } else {
                    $this->cezpdf->ezText('', 15, array("leading" => -110, 'left' => 330));
                    $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 390));
                }
                $this->cezpdf->ezText('', '', array("leading" => 40));
                if ($nombre_formapago != 'CONTADO') {
                    $fecha_text2 = '';
                } else {
                    $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '         ' . substr($fecha, 3, 2) . '          ' . substr($fecha, 8, 4));
                }

                //            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));

///FACTURA PDF

                ///***************fecha en letras
                $fecha_dia = substr($fecha, 0, 2);
                $fecha_mes = fechaALetras($fecha);
                $fecha_año = substr($fecha, 6, 8);
//cambiear las posiciones de las letras de la fechas
                $this->cezpdf->addText($posiciongeneralx + 60, $posiciongeneraly + 609, 8, utf8_decode_seguro($fecha_dia));

                $this->cezpdf->addText($posiciongeneralx + 80, $posiciongeneraly + 609, 8, utf8_decode_seguro($fecha_mes));

                $this->cezpdf->addText($posiciongeneralx + 130, $posiciongeneraly + 609, 8, utf8_decode_seguro($fecha_año));
                //**************************************
               // $this->cezpdf->addText($posiciongeneralx + 300, $posiciongeneraly + 100, 9, utf8_decode_seguro($fecha_text2));
   $this->cezpdf->addText($posiciongeneralx + 70, $posiciongeneraly + 580, 7, substr($direccion,0,62));

//NOMBRE DEL VENDEDOR FACTURA PDF
                $this->cezpdf->addText(220, 660, 7, utf8_decode_seguro("Vendedor : " . $vendedor));
//FORMA DE PAGO
                $this->cezpdf->addText($posiciongeneralx + 490, $posiciongeneraly + 579, 7, utf8_decode_seguro($nombre_formapago));
    // $this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));
               // $this->cezpdf->addText(380, 610, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));
                //$this->cezpdf->addText(500,200,30,'HOLAAAA');




                $this->cezpdf->addText($posiciongeneralx + 73, $posiciongeneraly + 562, 9, utf8_decode_seguro($ruc));

                $this->cezpdf->addText($posiciongeneralx + 70, $posiciongeneraly + 593, 8, utf8_decode_seguro($nombre_cliente));
                //Cliente Oculto
                //$this->cezpdf->addText($posiciongeneralx + 70, $posiciongeneraly + 663, 8, substr($direccion,0,62));
//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));

                /* Listado de detalles */
                $posicionX = 0;
                $posicionY = 800;
                $db_data = array();
                $i = 720;
                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;
                    $posicionX = 10;
                    $unidad = $valor->UNDMED_Simbolo;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
//                $this->cezpdf->addText(50, $i, 9, $producto);

                    ///aumentado stv
                    //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));

                    $this->cezpdf->addText(125, $i-160 , 7, substr($valor->PROD_Nombre,0,70));

                    $this->cezpdf->addText(100, $i-160, 6, utf8_decode_seguro($unidad));
                    //7

//                $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
                    $this->cezpdf->addText(65, $i-160 , 8, $valor->CPDEC_Cantidad);
                    $this->cezpdf->addText(440, $i-165 , 8, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(448, $i-165 , 40, 8, number_format($pu_conigv, 2), 'right');
                    //$this->cezpdf->addTextWrap(470, $i, 40, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2), "right");//revisar http://pubsvn.ez.no/doxygen/4.0/html/classCpdf.html#a4c3091f0936a733aa7e7ff98b876f3b1
                    $this->cezpdf->addText(500, $i-165 , 8, $moneda_simbolo);
                    $this->cezpdf->addTextWrap(510, $i-165 ,  40, 8, number_format($valor->CPDEC_Total, 2), 'right');
                    //$this->cezpdf->addTextWrap(520, $i, 44, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2),"right");
                    $i -= 18;

                    $posicionY -= 30;
                }


                /* Totales */
                // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                $this->cezpdf->addText($posiciongeneralx + 52, $posiciongeneraly + 75, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
                //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 50, 9, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 280, $posiciongeneraly + 50, 40, 9, number_format($subtotal - $descuento, 2), 'right');


                $this->cezpdf->addText($posiciongeneralx + 410, $posiciongeneraly + 50, 9, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 420, $posiciongeneraly + 50, 40, 9, number_format($igv, 2), 'right');


                $this->cezpdf->addText($posiciongeneralx + 510, $posiciongeneraly + 50, 9, $moneda_simbolo);
                $this->cezpdf->addTextWrap($posiciongeneralx + 530, $posiciongeneraly + 50, 40, 9, number_format(($total), 2), 'right');

//FINAL DE FACTURADE LLENAR DATOS
//                $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
//                $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 685, 8, 'ESTA LISTA CORRESPONDE A');
//                $this->cezpdf->addText($posiciongeneralx + 90, $posiciongeneraly + 645, 8, 'FACTURA');
//                $this->cezpdf->addText($posiciongeneralx + 250, $posiciongeneraly + 645, 8, $serie . ' Nro. ' . $numero);
//                $this->cezpdf->addText($posiciongeneralx + 275, $posiciongeneraly + 585, 8, 'LISTA DE IMEIS');
//                $valortotal = strlen($observacion);
                // strlen se obtiene la longitud de caracteres
                $exacta = round($valortotal / 96);
                // obtiene el numero entero de la operacion
                for ($i = 0; $i < $exacta; $i++) {
                    $this->cezpdf->addText($posiciongeneralx + 100, $posiciongeneraly + 560 - ($i * 13), 8, substr($observacion, $i * 96, 96));
                }


                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);


            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $datos_comprobante[0]->CPC_total;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);

                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
                //$this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
                //$this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));

                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));


//COMPRA FACTURA PDF INI
                //$this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => 18, "left" => 333));
                //FECHA FACTURA PDF
                  $this->cezpdf->addText($posicionX + 425, $posicionY + 725, 20, $serie."  -  ");
            $this->cezpdf->addText($posicionX + 486, $posicionY + 725, 20, $numero);

                $this->cezpdf->addText($posicionX + 458, $posicionY + 790, 18, $ruc);

                $this->cezpdf->addTextWrap($posicionX + 70, $posicionY + 663,200, 10, substr($fecha,0,2).'   / ');

                $this->cezpdf->addTextWrap($posicionX + 97, $posicionY + 663,200, 10, mes_textual(substr($fecha,3,2)).'   / ');

                $this->cezpdf->addTextWrap($posicionX + 150, $posicionY + 663,200, 10, substr($fecha,6,4));

                //$this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
              
                 $this->cezpdf->addText($posicionX + 70, $posicionY + 645, 8, $empresa);
            $this->cezpdf->addText($posicionX + 70, $posicionY + 610, 12, $ruc);
                /* Listado de detalles */
                $posicionX = 0;
                $posicionY =550;
                $db_data = array();

                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;

//                $ser = "";
//                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);

                    $posicionX = 10;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;

                    $posicionX += 50;
                    $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
                    
                    $posicionX += 15;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
                    ///aumentado stv
                    //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    $posicionX += 80;
                    $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    ///

//                $this->cezpdf->addText($posicionX, $posicionY, 9, utf8_decode_seguro($array_producto[0]));
                    

                    $posicionX +=260;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));

                    $posicionX += 80;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));


//
//                if (count($datos_serie) > 0) {
//                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
//                    for ($i = 0; $i < count($datos_serie); $i++) {
//                        $c = $c + 1;
//                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
//
//                        $ser = $ser . " /" . $seriecodigo;
//
//                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
//                        if ($c == 8) {
//                            $posicionY-=10;
//                            $c = 0;
//                            $ser = "";
//                        }
//                    }
//                }
                    $posicionY -= 40;
                }
                /* Totales */
                //$this->cezpdf->addText(200,600, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                $this->cezpdf->addText(60, 125, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

               // $this->cezpdf->addText(40, 100, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                //$this->cezpdf->addText(150, 100, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText(280, 100, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
                $this->cezpdf->addText(400, 100, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 100, 9, $moneda_simbolo . ' ' . number_format(($total), 2));

                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
            }


        }
//**************************************************************************

    }


    public function comprobante_ver_pdf_conmenbrete_formato1_boleta($tipo_oper, $codigo, $tipo_docu = '', $img = '')
    {

        $hoy = date("Y-m-d");
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $total = $datos_comprobante[0]->CPC_total;
        $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $modo_impresion = $datos_comprobante[0]->CPC_ModoImpresion;
        $estado = $datos_comprobante[0]->CPC_FlagEstado;
        $almacen = $datos_comprobante[0]->ALMAP_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor = $datos_comprobante[0]->CPC_Vendedor;
        $tdc = $datos_comprobante[0]->CPC_TDC;

        //DISPARADOR BEGIN
        if ($estado == 2) {

            $mueve = 1;
            $filter = new stdClass();
            $filter->CPC_TipoOperacion = $tipo_oper;
            $filter->CPC_TipoDocumento = $tipo_docu;
            $filter->MONED_Codigo = $moneda;
            $filter->CPC_total = $total;
            $filter->CPC_Fecha = $hoy;
            $filter->FORPAP_Codigo = $forma_pago;
            $filter->CLIP_Codigo = $cliente;
            $filter->PROVP_Codigo = $proveedor;
            $filter->CPC_Numero = $numero;
            $this->comprobante_model->insertar_disparador($codigo, $filter);


            if ($tipo_oper == 'V') {
                if ($mueve == 1) {
                    $filter3 = new stdClass();
                    $filter3->TIPOMOVP_Codigo = 1;
                    $filter3->ALMAP_Codigo = $almacen;
                    $filter3->CLIP_Codigo = $cliente;
                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
                    $filter3->GUIASAC_Fecha = $hoy;
                    $filter3->GUIASAC_Observacion = $observacion;
                    $filter3->USUA_Codigo = $this->somevar['user'];
                    $filter3->GUIASAC_Automatico = 1;
                    $guia_id = $this->guiasa_model->insertar($filter3);
                    $filter->GUIASAP_Codigo = $guia_id;
                    $filter->CPC_FlagMueveStock = '1';
                } else {
                    $filter->CPC_FlagMueveStock = '0';
                }
            } else {
                if ($mueve == 1) {
                    $filter3 = new stdClass();
                    $filter3->TIPOMOVP_Codigo = 2;
                    $filter3->ALMAP_Codigo = $almacen;
                    $filter3->PROVP_Codigo = $proveedor;
                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
                    $filter3->GUIAINC_Fecha = $hoy;
                    $filter3->GUIAINC_Observacion = $observacion;
                    $filter3->USUA_Codigo = $this->somevar['user'];
                    $filter3->GUIAINC_Automatico = 1;
                    $guia_id = $this->guiain_model->insertar($filter3);
                    $filter->GUIAINP_Codigo = $guia_id;
                    $filter->CPC_FlagMueveStock = '1';
                } else {
                    $filter->CPC_FlagMueveStock = '0';
                }
            }


            $a_filter = new stdClass();
            if ($tipo_oper == 'V')
                $a_filter->GUIASAP_Codigo = $guia_id;
            else
                $a_filter->GUIAINP_Codigo = $guia_id;

            $this->comprobante_model->modificar_comprobante($codigo, $a_filter);


            $detalle = $this->comprobantedetalle_model->listar($codigo);
            $lista_detalles = array();
            if (count($detalle) > 0) {
                foreach ($detalle as $indice => $valor) {
                    $detacodi = $valor->CPDEP_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad = $valor->UNDMED_Codigo;
                    $cantidad = $valor->CPDEC_Cantidad;
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
                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                    $GenInd = $valor->CPDEC_GenInd;
                    $costo = $valor->CPDEC_Costo;
                    $nombre_producto = ($valor->CPDEC_Descripcion != '' ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                    $nombre_producto = str_replace('\\', '', $nombre_producto);
                    $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                    $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                    $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';


                    if ($tipo_oper == 'V') {
                        $filter4 = new stdClass();
                        $filter4->GUIASAP_Codigo = $guia_id;
                        $filter4->PRODCTOP_Codigo = $producto;
                        $filter4->UNDMED_Codigo = $unidad;
                        $filter4->GUIASADETC_Cantidad = $cantidad;
                        $filter4->GUIASADETC_Costo = $subtotal_conigv;
                        $filter4->GUIASADETC_GenInd = $GenInd;
                        $filter4->GUIASADETC_Descripcion = $nombre_producto;;
                        $this->guiasadetalle_model->insertar($filter4);
                    } else {
                        $filter4 = new stdClass();
                        $filter4->GUIAINP_Codigo = $guia_id;
                        $filter4->PRODCTOP_Codigo = $producto;
                        $filter4->UNDMED_Codigo = $unidad;
                        $filter4->GUIAINDETC_Cantidad = $cantidad;
                        $filter4->GUIIAINDETC_GenInd = $subtotal_conigv;
                        $filter4->GUIAINDETC_Costo = $costo; // No estoy muy seguro de si debe agarrar este precio, porque puede ser $costo, $venta
                        $filter4->GUIAINDETC_Descripcion = $GenInd;
                        $this->guiaindetalle_model->insertar($filter4);
                    }
                }
            }
        }


        if ($tipo_oper == 'V') {
            if ($img == 1) {
                $notimg = "";
            } else {
                $notimg = "famyserfe_boleta.jpg";
            }
        } else {
            if ($img == 1) {
                $notimg = "";
            } else {
                $notimg = "factura_proveedor_1.jpg";
            }
        }

        if ($tipo_oper == 'V') {
            $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            $serie = $datos_comprobante[0]->CPC_Serie;
            $numero = $datos_comprobante[0]->CPC_Numero;
            $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
            $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
            $proveedor = $datos_comprobante[0]->CLIP_Codigo;
            $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
            $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
            $descuento100 = $datos_comprobante[0]->CPC_descuento100;
            $total = $datos_comprobante[0]->CPC_total;
            $observacion = $datos_comprobante[0]->CPC_Observacion;
            $hora = $datos_comprobante[0]->CPC_Hora;
            $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
            $vendedor = $datos_comprobante[0]->USUA_Codigo;
            $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $num_ser = $datos_comprobante[0]->CPC_Serie;
            $num_doc = $datos_comprobante[0]->CPC_Numero;
            $nombre_aux = $datos_comprobante[0]->CPC_NombreAuxiliar;
            $temp = $this->usuario_model->obtener($vendedor);
            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
            $vendedor = $temp[0]->PERSC_Nombre;
            $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
            if ($proveedor == 144 && $nombre_aux != 'cliente') {
                $nombre_cliente = strtoupper($nombre_aux);
                $ruc = '----------';
                $direccion = '----------';
            } else {
                $temp = $this->obtener_datos_cliente($proveedor);
                $nombre_cliente = $temp['nombre'];
                $ruc = $temp['numdoc'];
                $direccion = $temp['direccion'];
                $direccion = substr($direccion, 0, 50);
            }
            $array_fecha = explode("/", $fecha);
            $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
            $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
            $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
            $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');


            $detalle_comprobante = $this->obtener_lista_detalles($codigo);
//            $this->cezpdf = new Cezpdf('a4');
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
            /* Cabecera */


            if ($img != 1) {
                $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => 46, "left" => 260));
                $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 266));
                $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 304));
            } else {
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 46, "left" => 260));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 266));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 304));
            }


            $fechas = date($fecha_emision);
            $a = substr(date("Y", strtotime($fechas)), 2, 2);
            $m = mes_textual(date("m", strtotime($fechas)));
            $d = date("d", strtotime($fechas));


            $this->cezpdf->ezText(utf8_decode_seguro($a), 7, array("leading" => 36, "left" => 274));
            $this->cezpdf->ezText(utf8_decode_seguro($m), 7, array("leading" => 0, "left" => 216));
            $this->cezpdf->ezText(utf8_decode_seguro($d), 7, array("leading" => 0, "left" => 194));


            $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 6, array("leading" => 16, "left" => 38));

            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 6, array("leading" => 8, "left" => 38));
            $this->cezpdf->ezText(utf8_decode_seguro($ruc), 6, array("leading" => -10, "left" => 226));
//            $this->cezpdf->ezText(utf8_decode_seguro("Vendedor : " . $vendedor), 7, array("leading" => -13, "left" => 365));
//            $this->cezpdf->ezText(utf8_decode_seguro("Modo : " . $nombre_formapago), 7, array("leading" => 6, "left" => 263));


//            $this->cezpdf->ezText(utf8_decode_seguro($hora), 7, array("leading" => 0, "left" => 445));

            $this->cezpdf->ezText('', '', array("leading" => 70));

            /*
            $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 8, array("leading" => 131, "left" => 34));
            //$this->cezpdf->ezText(utf8_decode_seguro($ruc), 8, array("leading" => 14, "left" => 12));
            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 15, "left" => 35));
            $this->cezpdf->ezText(utf8_decode_seguro("Vendedor : " . $vendedor), 7, array("leading" => -13, "left" => 365));
            $this->cezpdf->ezText(utf8_decode_seguro("Modo : " . $nombre_formapago), 7, array("leading" => 6, "left" => 263));

            if ($img != 1) {
                $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => -40, "left" => 405));
                $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 430));
                $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 440));
            } else {
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => -35, "left" => 400));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 430));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 440));
            }

            $this->cezpdf->ezText(utf8_decode_seguro($fecha_emision), 9, array("leading" => 43, "left" => 385));

            $this->cezpdf->ezText(utf8_decode_seguro($hora), 7, array("leading" => 0, "left" => 445));

            $this->cezpdf->ezText('', '', array("leading" => 70));
			*/


            /* Listado de detalles */

            $db_data = array();
            $positiony = 676;
            $positionx = 0;
            $serie_producto = "00000000001, 00000000002";
            foreach ($detalle_comprobante as $indice => $valor) {
                $positionx = 8;
                //$array_prodnombre = explode('/', $valor->PROD_Nombre);


                $prod_nombre = $valor->PROD_Nombre;

                if (strpos($prod_nombre, "/")) {
                    $prod_nombre = substr($valor->PROD_Nombre, 0, strpos($valor->PROD_Nombre, "/"));
                }


                $producto = $valor->PROD_CodigoUsuario;
                $unidad = $valor->UNDMED_Simbolo;
                $ser = "";
//                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
                $positionx += 42;
                $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
                $positionx += 26;                                                            //$array_prodnombre[0]
                $this->cezpdf->addText($positionx, $positiony, 6, strtoupper(utf8_decode_seguro($prod_nombre)));
                $positionx += 154;


                /////alineado ppu

                $ppu = $moneda_simbolo . number_format($valor->CPDEC_Pu_ConIgv, 2);
                $ppu = $this->ali_precio($ppu);
                $this->cezpdf->addText($positionx, $positiony, 7, $ppu);

                ///////////


                //$this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx += 40;


                /////alineado ppt

                $ppt = $moneda_simbolo . number_format($valor->CPDEC_Total, 2);
                $ppt = $this->ali_precio($ppt);
                $this->cezpdf->addText($positionx, $positiony, 7, $ppt);

                ///////////


                //$this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                $positiony -= 14;
            }

            /*             * Sub Totales* */
            $delta = 130;
            $positionx = 420;
            $positiony = 350 + $delta;
            // $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");


            $this->cezpdf->addText(52, $positiony - 54, 6, "SON: " . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));


            /////alineado ppto

            $ppto = $moneda_simbolo . number_format($total, 2);
            $ppto = $this->ali_precio($ppto);
            $this->cezpdf->addText($positionx - 156, $positiony - 78, 8, $ppto);

            ///////////


            //$this->cezpdf->addText($positionx -156, $positiony - 78, 8, $moneda_simbolo . ' ' . number_format($total, 2));

            /*
            $db_data = array();
            $positiony = 632;
            $positionx = 0;
            $serie_producto = "00000000001, 00000000002";
            foreach ($detalle_comprobante as $indice => $valor) {
                $positionx = 50;
                $array_prodnombre = explode('/', $valor->PROD_Nombre);
                $producto = $valor->PROD_CodigoUsuario;
                $unidad = $valor->UNDMED_Simbolo;
                $ser = "";
                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
                $positionx +=60;
                $this->cezpdf->addText($positionx, $positiony, 8, strtoupper(utf8_decode_seguro($array_prodnombre[0] . "  --" . $unidad)));
                $positionx+=310;
                $this->cezpdf->addText($positionx, $positiony, 8, $valor->CPDEC_Cantidad);
                $positionx+=30;
                $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx+=65;
                $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                $positiony-=10;
            }

            /*             * Sub Totales* */
            /*
			$delta = 130;
            $positionx = 420;
            $positiony = 350 + $delta;
            // $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(110, $positiony - 21, 7, "SON " . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
            $this->cezpdf->addText($positionx + 90, $positiony - 42, 10, $moneda_simbolo . ' ' . number_format($total, 2));
			*/
        } else {
            $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
//            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            //$serie = $datos_comprobante[0]->CPC_Serie;
            //$numero = $datos_comprobante[0]->CPC_Numero;
            $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
            $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
            $proveedor = $datos_comprobante[0]->PROVP_Codigo;
            $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
            $ruc = $datos_proveedor[0]->EMPRC_Ruc;
            $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
//            $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
//            $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
//            $descuento100 = $datos_comprobante[0]->CPC_descuento100;
            $total = $datos_comprobante[0]->CPC_total;
//            $observacion = $datos_comprobante[0]->CPC_Observacion;
            $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
//            $vendedor = $datos_comprobante[0]->USUA_Codigo;
//            $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $num_ser = $datos_comprobante[0]->CPC_Serie;
            $num_doc = $datos_comprobante[0]->CPC_Numero;
//            $temp = $this->usuario_model->obtener($vendedor);
//            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
//            $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;
            $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
            $array_fecha = explode("/", $fecha);
            $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
            //$temp = $this->obtener_datos_cliente($proveedor);
//            $nombre_cliente = $temp['nombre'];
//            $ruc = $temp['numdoc'];
//            $direccion = $temp['direccion'];
//            $direccion = substr($direccion, 0, 50);
            $detalle_comprobante = $this->obtener_lista_detalles($codigo);
            $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
            $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
            $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
//            $this->cezpdf = new Cezpdf('a4');
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
            $this->cezpdf->ezText(utf8_decode_seguro($empresa), 8, array("leading" => 93, "left" => 15));
            $this->cezpdf->ezText(utf8_decode_seguro($ruc), 12, array("leading" => -90, "left" => 442));
            $this->cezpdf->ezText(utf8_decode_seguro("IMPORTACIONES IMPACTO SAC"), 9, array("leading" => 104, "left" => 10));
            $this->cezpdf->ezText('20527033798', 9, array("leading" => 15, "left" => 10));
//            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 11, "left" => 12));
//            $this->cezpdf->ezText(utf8_decode_seguro($vendedor), 7, array("leading" => -13, "left" => 333));
            $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 7, array("leading" => -16, "left" => 333));
            $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => -35, "left" => 400));
            $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 420));
            $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 440));
            $this->cezpdf->ezText(utf8_decode_seguro($fecha_emision), 8, array("leading" => 36, "left" => 440));
//
//            $this->cezpdf->ezText('', '', array("leading" => 70));
//
//
//            /* Listado de detalles */
            $db_data = array();
            $positiony = 640;
            $positionx = 0;
            $serie_producto = "00000000001, 00000000002";
            foreach ($detalle_comprobante as $indice => $valor) {
                $positionx = 0;
                $array_prodnombre = explode('/', $valor->PROD_Nombre);
                $producto = $valor->PROD_Codigo;

                $ser = "";
                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);
                if (count($datos_serie) > 0) {
                    for ($i = 0; $i < count($datos_serie); $i++) {
                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
                        $ser = $ser . " /" . $seriecodigo;
                    }
                }
                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
//$positionx = $positionx + 80;
                $positionx += 30;
// $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
//$positionx+=50;

                $this->cezpdf->addText($positionx, $positiony, 7, strtoupper(utf8_decode_seguro($array_prodnombre[0])));
                $positionx += 428;
                $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
                $positionx += 25;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx += 50;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
// $this->cezpdf->addText($positionx, $positiony, 7, number_format($valor->CPDEC_Total, 2));
                $this->cezpdf->addText(40, $positiony - 15, 7, "Series: " . $ser);
                $positiony -= 40;
            }

            /*             * Sub Totales* */
            $delta = 130;
            $positionx = 400;
            $positiony = 120 + $delta;

            $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(20, $positiony - 35, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
            $this->cezpdf->addText($positionx + 100, $positiony - 38, 10, $moneda_simbolo . ' ' . number_format($total, 2));
        }

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function comprobante_ver_pdf_conmenbrete_formato1_boleta1($tipo_oper, $codigo, $tipo_docu, $img)
    {
//**************************************************************************
        if ($_SESSION['empresa'] == '3') {
//3 = dragon yuan
//2 = dragoket

            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = "";
                } else {

                    if ($_SESSION['compania'] == '3') {
                        $notimg = "dragonYuan_boleta_de_venta.jpg";   //magadalena
                    } else {
                        $notimg = "boleta_yuan_andahuaylas.jpg";   //andahuaylas
                    }

                }
            } else {
                if ($img == 1) {
                    $notimg = "";
                } else {
                    $notimg = "factura_proveedor_1.jpg";
                }
            }

            if ($tipo_oper == 'V') {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
                $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $num_ser = $datos_comprobante[0]->CPC_Serie;
                $num_doc = $datos_comprobante[0]->CPC_Numero;
                $nombre_aux = $datos_comprobante[0]->CPC_NombreAuxiliar;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;
                $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
                if ($proveedor == 144 && $nombre_aux != 'cliente') {
                    $nombre_cliente = strtoupper($nombre_aux);
                    $ruc = '----------';
                    $direccion = '----------';
                } else {
                    $temp = $this->obtener_datos_cliente($proveedor);
                    $nombre_cliente = $temp['nombre'];
                    $ruc = $temp['numdoc'];
                    $dni = $temp['dni'];
                    $direccion = $temp['direccion'];
                    $direccion = substr($direccion, 0, 50);
                }
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);

                if ($_SESSION['compania'] == '3') {
//dragon yuan mafgdalena

                    $this->cezpdf = new Cezpdf('a4');
                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                    $posiciongeneralx = -2;
                    $posiciongeneraly = 3;
                    /* Cabecera */

                    if ($img != 1) {
                        $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => 106, "left" => 360));
                        $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 266));
                        $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 404));
                    } else {
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => -35, "left" => 400));
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 430));
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 440));
                    }


                    $fechas = date($fecha_emision);
                    $fecha_año = substr(date("Y", strtotime($fechas)), 2, 2);
                    $fecha_mes = mes_textual(date("m", strtotime($fechas)));
                    $fecha_dia = date("d", strtotime($fechas));

                    // CODIGO ACTUAL
                    $this->cezpdf->addText(73 + $posiciongeneralx, 725 + $posiciongeneraly, 10, utf8_decode_seguro($fecha_dia));
                    $this->cezpdf->addText(145 + $posiciongeneralx, 725 + $posiciongeneraly, 10, utf8_decode_seguro($fecha_mes));
                    $this->cezpdf->addText(240 + $posiciongeneralx, 725 + $posiciongeneraly, 10, utf8_decode_seguro($fecha_año));
                    //****************1era fecha

                    if ($nombre_formapago != 'CONTADO') {
                        $fecha_text2 = '';
                    } else {
                        $fecha_text2 = utf8_decode_seguro(substr($fecha, 0, 2) . '       ' . mes_textual(substr($fecha, 3, 2)) . '            ' . substr($fecha, 8, 4));
                    }

                    //****************2da fecha PENDIENTE
                    $this->cezpdf->addText(288 + $posiciongeneralx, 463 + $posiciongeneraly, 8, utf8_decode_seguro($fecha_text2));
                    //****************2da fecha


                    ///***************datos de empresa
                   
                    //$this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
                    ///***************datos de empresa


                    /*// inicio cliente//
            $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 8, array("leading" => 17, "left" => 60));
            // fin cliente//
            // inicio direccion //
            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 15, "left" => 57));
            $this->cezpdf->ezText(utf8_decode_seguro($ruc), 9, array("leading" => 15, "left" => 80));*/
                    // $this->cezpdf->addText(200,700,7,utf8_decode_seguro("Vendedor : " . $vendedor));
                    // $this->cezpdf->ezText(utf8_decode_seguro("Modo : " . $nombre_formapago), 7, array("leading" => 6, "left" => 263));


//            $this->cezpdf->ezText(utf8_decode_seguro($hora), 7, array("leading" => 0, "left" => 445));

                    $this->cezpdf->ezText('', '', array("leading" => 70));


                    /* inicio Listado de detalles */
                    $db_data = array();
                    /* mueve a todo lista detalle hacia arriba*/
                    $positiony = 643;
                    $positionx = 0;
                    $serie_producto = "00000000001, 00000000002";
                    foreach ($detalle_comprobante as $indice => $valor) {

                        $positionx = 55; // mueve todo hacia izquierda a derecha
                        //$array_prodnombre = explode('/', $valor->PROD_Nombre);
                        $producto = $valor->PROD_CodigoUsuario;
                        $unidad = $valor->UNDMED_Simbolo;
                        $ser = "";
//                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
                        $positionx += 0;
                        $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 18, 9, $valor->CPDEC_Cantidad, 'right');
                        $positionx += 30;                                                            //$array_prodnombre[0]
                        $this->cezpdf->addText($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 9, strtoupper(utf8_decode_seguro($valor->PROD_Nombre)));
                        $positionx += 345; /* mueve todo P.UNIT. y el importe */
                        $this->cezpdf->addText($positionx + $posiciongeneralx - 12, $positiony + $posiciongeneraly, 9, $moneda_simbolo);
                        $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 45, 9, number_format($valor->CPDEC_Pu_ConIgv, 2), 'right');
                        $positionx += 62;
                        $this->cezpdf->addText($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 9, $moneda_simbolo);
                        $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 60, 9, number_format($valor->CPDEC_Total, 2), 'right');
                        $positiony -= 17;
                    }


                    /*             * Sub Totales* */
                    $delta = 130;
                    $positionx = 420;
                    $positiony = 350 + $delta;
                    // $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                    /*  MUEVE NRO TOTAL EN LETRAS */
                    $this->cezpdf->addText(85 + $posiciongeneralx, $positiony - 3 + $posiciongeneraly, 9, "SON: " . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
                    /*  MUEVE EL TOTAL */
                    $this->cezpdf->addText($positionx + 70 + $posiciongeneralx, $positiony - 28 + $posiciongeneraly, 10, $moneda_simbolo . ' ' . number_format($total, 2));

                    $posiciongeneralix = 0;
                    $posiciongeneraliy = 0;

                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralix + 90, $posiciongeneraliy + 705, 8, 'ESTA LISTA CORRESPONDE A');

                    $this->cezpdf->addText($posiciongeneralix + 90, $posiciongeneraliy + 665, 8, 'BOLETA :');
                    $this->cezpdf->addText($posiciongeneralix + 140, $posiciongeneraliy + 665, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralix + 275, $posiciongeneraliy + 585, 8, 'LISTA DE IMEIS');

                    $this->cezpdf->addText(90, 620, 6, utf8_decode_seguro($prod_nombreimei));


                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 112);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText(90, 560 - ($i * 10), 7, substr($observacion, $i * 112, 112));
                    }


                } else {

                    $this->cezpdf = new Cezpdf('a4');
                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                    /* Cabecera */

                    if ($img != 1) {
                        $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => 67, "left" => 118));
                        $this->cezpdf->ezText(utf8_decode_seguro("N°"), 15, array("leading" => 0, "left" => 158));
                        $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 176));
                    } else {
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 67, "left" => 118));
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 158));
                        $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 174));
                    }
                    $posiciongeneralx = 8;
                    $posiciongeneraly = 40;
                    //2
                    $fechas = date($fecha_emision);
                    $a = substr(date("Y", strtotime($fechas)), 2, 2);
                    $m = substr(date("m", strtotime($fechas)), 0, 2);
                    $d = date("d", strtotime($fechas));
                    $this->cezpdf->addText(190 + $posiciongeneralx, 680 + $posiciongeneraly, 8, $d);
                    $this->cezpdf->addText(215 + $posiciongeneralx, 680 + $posiciongeneraly, 8, $m);
                    $this->cezpdf->addText(245 + $posiciongeneralx, 680 + $posiciongeneraly, 8, $a);
                    $this->cezpdf->addText(62 + $posiciongeneralx, 659 + $posiciongeneraly, 9, utf8_decode_seguro($nombre_cliente));
                    $this->cezpdf->addText(62 + $posiciongeneralx, 644 + $posiciongeneraly, 9, substr($direccion, 0, 20));
                    $this->cezpdf->addText(225 + $posiciongeneralx, 645 + $posiciongeneraly, 9, $dni);


                    /* Listado de detalles */
                    $db_data = array();
                    $positiony = 615;
                    $positionx = 0;
                    $serie_producto = "00000000001, 00000000002";
                    $prod_nombreimei = '';
                    foreach ($detalle_comprobante as $indice => $valor) {
                        $positionx = 36;
                        $producto = $valor->PROD_CodigoUsuario;
                        $unidad = $valor->UNDMED_Simbolo;
                        $ser = "";
                        $positionx += 0;
                        $nombreproducto = $valor->PROD_Nombre;
                        $valornombreproducto = strlen($nombreproducto);
                        $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 18, 8, $valor->CPDEC_Cantidad, 'right');
                        $positionx += 28;

                        if ($valornombreproducto <= 25) {

                            $this->cezpdf->addText($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 8, strtoupper(utf8_decode_seguro(substr($nombreproducto, 0, 25))));


                        } else {
                            $nombacortado = substr($nombreproducto, 0, 25);
                            $posicion1 = strrpos($nombacortado, ' ');
                            $this->cezpdf->addText($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 8, strtoupper(utf8_decode_seguro(substr($nombreproducto, 0, $posicion1))));
                            $positionx += 115;
                            $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Pu_ConIgv, 2), 'right');
                            $positionx += 40;
                            $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Total, 2), 'right');
                            $positiony -= 15;
                            $this->cezpdf->addText(60 + $posiciongeneralx, $positiony + $posiciongeneraly, 8, strtoupper(utf8_decode_seguro(substr($nombreproducto, $posicion1, 20))));


                        }

                        $prod_nombreimei = $valor->PROD_Nombre . ' / ' . $prod_nombreimei;
                        if ($valornombreproducto <= 25) {
                            $positionx += 115;
                            $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Pu_ConIgv, 2), 'right');
                            $positionx += 40;
                            $this->cezpdf->addTextWrap($positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Total, 2), 'right');
                        }


                        $positiony -= 15;
                    }

                    /*             * Sub Totales* */
                    $delta = 130;
                    $positionx = 427;
                    $positiony = 318 + $delta;
                    $this->cezpdf->addText($positionx - 210 + $posiciongeneralx, $positiony - 63 + $posiciongeneraly, 8, number_format($total, 2));

                    $posiciongeneralix = 0;
                    $posiciongeneraliy = 0;

                    $this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
                    $this->cezpdf->addText($posiciongeneralix + 90, $posiciongeneraliy + 705, 8, 'ESTA LISTA CORRESPONDE A');

                    $this->cezpdf->addText($posiciongeneralix + 90, $posiciongeneraliy + 665, 8, 'BOLETA :');
                    $this->cezpdf->addText($posiciongeneralix + 140, $posiciongeneraliy + 665, 8, $serie . ' Nro. ' . $numero);


                    $this->cezpdf->addText($posiciongeneralix + 275, $posiciongeneraliy + 585, 8, 'LISTA DE IMEIS');

                    $this->cezpdf->addText(90, 620, 6, utf8_decode_seguro($prod_nombreimei));


                    $valortotal = strlen($observacion);
                    // strlen se obtiene la longitud de caracteres
                    $exacta = round($valortotal / 112);
                    // obtiene el numero entero de la operacion
                    for ($i = 0; $i < $exacta; $i++) {
                        $this->cezpdf->addText(90, 560 - ($i * 10), 7, substr($observacion, $i * 112, 112));
                    }


                }


            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
//            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                //$serie = $datos_comprobante[0]->CPC_Serie;
                //$numero = $datos_comprobante[0]->CPC_Numero;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
//            $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
//            $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
//            $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
//            $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
//            $vendedor = $datos_comprobante[0]->USUA_Codigo;
//            $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $num_ser = $datos_comprobante[0]->CPC_Serie;
                $num_doc = $datos_comprobante[0]->CPC_Numero;
//            $temp = $this->usuario_model->obtener($vendedor);
//            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
//            $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;
                $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                //$temp = $this->obtener_datos_cliente($proveedor);
//            $nombre_cliente = $temp['nombre'];
//            $ruc = $temp['numdoc'];
//            $direccion = $temp['direccion'];
//            $direccion = substr($direccion, 0, 50);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
//            $this->cezpdf = new Cezpdf('a4');
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
                $this->cezpdf->ezText(utf8_decode_seguro($empresa), 8, array("leading" => 93, "left" => 15));
                $this->cezpdf->ezText(utf8_decode_seguro($ruc), 12, array("leading" => -90, "left" => 442));
                $this->cezpdf->ezText(utf8_decode_seguro("IMPORTACIONES IMPACTO SAC"), 9, array("leading" => 104, "left" => 10));
                $this->cezpdf->ezText('20527033798', 9, array("leading" => 15, "left" => 10));
//            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 11, "left" => 12));
//            $this->cezpdf->ezText(utf8_decode_seguro($vendedor), 7, array("leading" => -13, "left" => 333));
                $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 7, array("leading" => -16, "left" => 333));
                $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => -35, "left" => 400));
                $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 420));
                $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 440));
                $this->cezpdf->ezText(utf8_decode_seguro($fecha_emision), 8, array("leading" => 36, "left" => 440));
//
//            $this->cezpdf->ezText('', '', array("leading" => 70));
//
//
//            /* Listado de detalles */
                $db_data = array();
                $positiony = 640;
                $positionx = 0;
                $serie_producto = "00000000001, 00000000002";
                foreach ($detalle_comprobante as $indice => $valor) {
                    $positionx = 0;
                    $array_prodnombre = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_Codigo;

                    $ser = "";
                    $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);
                    if (count($datos_serie) > 0) {
                        for ($i = 0; $i < count($datos_serie); $i++) {
                            $seriecodigo = $datos_serie[$i]->SERIC_Numero;
                            $ser = $ser . " /" . $seriecodigo;
                        }
                    }
                    $this->cezpdf->addText($positionx, $positiony, 8, $producto);
//$positionx = $positionx + 80;
                    $positionx += 30;
// $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
//$positionx+=50;

                    $this->cezpdf->addText($positionx, $positiony, 8, strtoupper(utf8_decode_seguro($array_prodnombre[0])));
                    $positionx += 428;
                    $this->cezpdf->addText($positionx, $positiony, 8, $valor->CPDEC_Cantidad);
                    $positionx += 25;
                    $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                    $positionx += 50;
                    $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
// $this->cezpdf->addText($positionx, $positiony, 7, number_format($valor->CPDEC_Total, 2));
                    $this->cezpdf->addText(40, $positiony - 15, 7, "Series: " . $ser);
                    $positiony -= 40;
                }

                /*             * Sub Totales* */
                $delta = 130;
                $positionx = 400;
                $positiony = 120 + $delta;

                $this->cezpdf->addText(20, 230, 9, "Tipo de cambio LOS SOLOES" . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                $this->cezpdf->addText(20, $positiony - 35, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
                $this->cezpdf->addText($positionx + 100, $positiony - 38, 10, $moneda_simbolo . ' ' . number_format($total, 2));
            }


            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
//********************
        } else {




//dragotek BOLETA IMPRIMIR
            if ($tipo_oper == 'V') {
                if ($img == 1) {
                    $notimg = ""; 
                   //$notimg = "boleta_venta.jpg";//COMENTAS CUANDO TERMINAS
                } else{
                    $notimg = "";   //fullcolor_boleta.jpg
                    $notimg = "boleta_venta.jpg";
                }
            }

//BOLETA IMPRIMIR COMPRA
             else {
                if ($img == 1) {
                    $notimg = "boleta_venta.jpg";
                } else {
                    $notimg = "boleta_proveedor.jpg";
                     $notimg = "boleta_proveedor.jpg";
                }
            }

            if ($tipo_oper == 'V') {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
                $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $proveedor = $datos_comprobante[0]->CLIP_Codigo;
                $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
                $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
                $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
                $observacion = $datos_comprobante[0]->CPC_Observacion;
                $hora = $datos_comprobante[0]->CPC_Hora;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                $vendedor = $datos_comprobante[0]->USUA_Codigo;
                $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $num_ser = $datos_comprobante[0]->CPC_Serie;
                $num_doc = $datos_comprobante[0]->CPC_Numero;
                $nombre_aux = $datos_comprobante[0]->CPC_NombreAuxiliar;
                $temp = $this->usuario_model->obtener($vendedor);
                $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
                $vendedor = $temp[0]->PERSC_Nombre;
                $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
                if ($proveedor == 144 && $nombre_aux != 'cliente') {
                    $nombre_cliente = strtoupper($nombre_aux);
                    $ruc = '----------';
                    $direccion = '----------';
                } else {
                    $temp = $this->obtener_datos_cliente($proveedor);
                    $nombre_cliente = $temp['nombre'];
                    $ruc = $temp['numdoc'];
                    $direccion = $temp['direccion'];
                    $dni = $temp['dni'];
                    $direccion = substr($direccion, 0, 50);
                }
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');


                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $this->cezpdf = new Cezpdf('a4');
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
                /* Cabecera */
//NUMERO Y SERIE VENTA BOLETA
                if ($img != 1) {
                     $this->cezpdf->ezText(utf8_decode_seguro($num_ser."-".$this->getOrderNumeroSerie($num_doc)), 15, array("leading" => 110, "left" => 380));

                } else {
                    $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 67, "left" => 118));
                    $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 158));
                    $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 174));
                }

                /*if($_SESSION['compania']=='1'){//sede 002 ccmi
            $posiciongeneralx-=5;
            $posiciongeneraly=22;
			}else{
            $posiciongeneralx=8;
            $posiciongeneraly=20;
			}*/
            //CODIGO PARA LA BOLETA
                $posiciongeneralx = -25;
                $posiciongeneraly = 60;

                $fechas = date($fecha_emision);
                $a = substr(date("Y", strtotime($fechas)), 2, 2);
                $m = mes_textual(date("m", strtotime($fechas)));
                $d = date("d", strtotime($fechas));

                $this->cezpdf->addText(410+ $posiciongeneralx, 570 + $posiciongeneraly, 10, $d);

                $this->cezpdf->addText(465 + $posiciongeneralx, 570 + $posiciongeneraly, 10, $m);

                $this->cezpdf->addText(560 + $posiciongeneralx, 570 + $posiciongeneraly, 10, $a);

                $this->cezpdf->addText(155 + $posiciongeneralx, 530 + $posiciongeneraly, 10, utf8_decode_seguro($nombre_cliente));
//DIRECCION DE BOLETA_VENTA
                $this->cezpdf->addText(150+ $posiciongeneralx, 495 + $posiciongeneraly, 10, substr($direccion, 0, 20));

                $this->cezpdf->addText(450 + $posiciongeneralx, 720 + $posiciongeneraly, 18, $ruc);

                $this->cezpdf->addText(95 + $posiciongeneralx, 648 + $posiciongeneraly, 10, $dni);
                $this->cezpdf->addText(415 + $posiciongeneralx, 677 + $posiciongeneraly, 10, $num_guia);


                /* Listado de detalles */
                $db_data = array();
                $positiony = 433;
                $positionx = 0;
                $serie_producto = "00000000001, 00000000002";
                $prod_nombreimei = '';
                foreach ($detalle_comprobante as $indice => $valor) {
                    $positionx = 36;
                    $producto = $valor->PROD_CodigoUsuario;
                    $unidad = $valor->UNDMED_Simbolo;
                    $ser = "";
                    $positionx += 0;
                    //$this->cezpdf->addTextWrap(22+$positionx+$posiciongeneralx, $positiony+$posiciongeneraly,8, 9, $valor->CPDEC_Cantidad,'right');
                    $this->cezpdf->addText(60 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 9, $valor->CPDEC_Cantidad);

                    $positionx += 20;
                    //$array_prodnombre[0]
                    $nombreproducto = $valor->PROD_Nombre;
                    $valornombreproducto = strlen($nombreproducto);


                    if ($valornombreproducto <= 25) {

                        $this->cezpdf->addText(35 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 9, strtoupper(utf8_decode_seguro($nombreproducto)));


                    } else {
                        $nombacortado = substr($nombreproducto, 0, 10);
                        $posicion1 = strrpos($nombacortado, ' ');

                        //$this->cezpdf->addText(25 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 6, strtoupper(utf8_decode_seguro($nombreproducto)));
                        $this->cezpdf->addText(100+ $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 7,substr($nombreproducto,0,50));
                        $positionx += 150;
                        $this->cezpdf->addText(220 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 7, $moneda_simbolo);

                        $this->cezpdf->addTextWrap(230 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Pu_ConIgv, 2), 'right');

                        $positionx += 40;
                        $this->cezpdf->addText(285 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 7, $moneda_simbolo);


                        $this->cezpdf->addTextWrap(300 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Total, 2), 'right');

                        $positiony -= 1;

                    }

                    $prod_nombreimei = $valor->PROD_Nombre . ' / ' . $prod_nombreimei;
                    if ($valornombreproducto <= 25) {
                        $positionx += 210;
                        $this->cezpdf->addText(280 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 8, $moneda_simbolo);
                        $this->cezpdf->addTextWrap(280 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Pu_ConIgv, 2), 'right');
                        $positionx += 120;
                        $this->cezpdf->addText(315 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 8, $moneda_simbolo);

                        $this->cezpdf->addTextWrap(315 + $positionx + $posiciongeneralx, $positiony + $posiciongeneraly, 35, 8, number_format($valor->CPDEC_Total, 2), 'right');
                    }
                    $positiony -= 15;
                }

                /*             * Sub Totales* */
                $delta = 130;
                $positionx = 305;
                $positiony = 438 + $delta;
                $this->cezpdf->addText($positionx + 180, $positiony - 565+ $posiciongeneraly, 8, $moneda_simbolo);
                $this->cezpdf->addText($positionx + 220, $positiony - 565+ $posiciongeneraly, 9, number_format($total, 1));
                //$this->cezpdf->addText(90, 460, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);


                $posiciongeneralix = 0;
                $posiciongeneraliy = 0;
                /*
			$this->cezpdf->ezText(' ', 9, array("leading" => 1200, "left" => 0));
            $this->cezpdf->addText($posiciongeneralix+90, $posiciongeneraliy+705, 8, 'ESTA LISTA CORRESPONDE A');

            $this->cezpdf->addText($posiciongeneralix+90, $posiciongeneraliy+665, 8, 'BOLETA :');
           $this->cezpdf->addText($posiciongeneralix+140, $posiciongeneraliy+665, 8,$serie.' Nro. '.$numero);



            $this->cezpdf->addText($posiciongeneralix+275, $posiciongeneraliy+585, 8, 'LISTA DE IMEIS');

            $this->cezpdf->addText(90, 620, 6,  utf8_decode_seguro($prod_nombreimei));
*/

                $valortotal = strlen($observacion);
                // strlen se obtiene la longitud de caracteres
                $exacta = round($valortotal / 112);
                // obtiene el numero entero de la operacion
                for ($i = 0; $i < $exacta; $i++) {
                    $this->cezpdf->addText(90, 560 - ($i * 10), 7, substr($observacion, $i * 112, 112));
                }


            } else {
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
//            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
                //$serie = $datos_comprobante[0]->CPC_Serie;
                //$numero = $datos_comprobante[0]->CPC_Numero;
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
//            $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
//            $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
//            $descuento100 = $datos_comprobante[0]->CPC_descuento100;
                $total = $datos_comprobante[0]->CPC_total;
//            $observacion = $datos_comprobante[0]->CPC_Observacion;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
//            $vendedor = $datos_comprobante[0]->USUA_Codigo;
//            $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                $num_ser = $datos_comprobante[0]->CPC_Serie;
                $num_doc = $datos_comprobante[0]->CPC_Numero;
//            $temp = $this->usuario_model->obtener($vendedor);
//            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
//            $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;
                $fecha_emision = $datos_comprobante[0]->CPC_Fecha;
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                //$temp = $this->obtener_datos_cliente($proveedor);
//            $nombre_cliente = $temp['nombre'];
//            $ruc = $temp['numdoc'];
//            $direccion = $temp['direccion'];
//            $direccion = substr($direccion, 0, 50);
                $detalle_comprobante = $this->obtener_lista_detalles($codigo);
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
//            $this->cezpdf = new Cezpdf('a4');
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
              // $this->cezpdf->ezText(utf8_decode_seguro($empresa), 8, array("leading" => 12, "left" => 15));
                $this->cezpdf->addText($posicionX + 425, $posicionY + 755, 20, $num_ser."  -  ");
            $this->cezpdf->addText($posicionX + 486, $posicionY + 755, 20, $num_doc);

                $this->cezpdf->addText($posicionX + 430, $posicionY + 810, 18, $ruc);

                $this->cezpdf->addText($posicionX + 490, $posicionY + 710, 10,utf8_decode_seguro( $fecha_emision));
                $this->cezpdf->addText($posicionX + 70, $posicionY + 710, 12, $empresa);
                $this->cezpdf->addText($posicionX + 70, $posicionY + 680, 12, $ruc);
               


//BOLATA COMPRQA CON IMAGEN
               // $this->cezpdf->ezText(utf8_decode_seguro("IMPORTACIONES IMPACTO SAC"), 9, array("leading" => 104, "left" => 10));

                //$this->cezpdf->ezText('20527033798', 9, array("leading" => 15, "left" => 10));


//            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 11, "left" => 12));
//            $this->cezpdf->ezText(utf8_decode_seguro($vendedor), 7, array("leading" => -13, "left" => 333));
               // $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 7, array("leading" => -100, "left" => 500));
                //numero de seriessss
               // $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 420));
               


                
//
//            $this->cezpdf->ezText('', '', array("leading" => 70));
//BOLETA COMPRA BOLETA IMPRIMIR
//
//            /* Listado de detalles */
                $db_data = array();
                $positiony = 495;
                $positionx = 0;
                $serie_producto = "00000000001, 00000000002";
                foreach ($detalle_comprobante as $indice => $valor) {
                    $positionx = 0;
                    $array_prodnombre = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_Codigo;

                    $ser = "";
                    $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);
                    if (count($datos_serie) > 0) {
                        for ($i = 0; $i < count($datos_serie); $i++) {
                            $seriecodigo = $datos_serie[$i]->SERIC_Numero;
                            $ser = $ser . " /" . $seriecodigo;
                        }
                    }
                   // $this->cezpdf->addText($positionx, $positiony, 7, $producto);
//$positionx = $positionx + 80;
                    
// $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
//$positionx+=50;

                     //$positionx += 440;
                    $this->cezpdf->addText($positionx+440, $positiony+ 150, 8, $valor->CPDEC_Cantidad);

                    //$positionx += 50;
                    $this->cezpdf->addText($positionx+50, $positiony+150, 8, strtoupper(utf8_decode_seguro($array_prodnombre[0])));

                   
                  //  $positionx +=280;
                    $this->cezpdf->addText($positionx+480, $positiony+150, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));

                    //$positionx += 90;
                    $this->cezpdf->addText($positionx+520, $positiony+150, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
// $this->cezpdf->addText($positionx, $positiony, 7, number_format($valor->CPDEC_Total, 2));
//                    $this->cezpdf->addText(40, $positiony - 15, 7, "Series: " . $ser);
                    $positiony -= 40;
                }

                /*             * Sub Totales* */
                $delta = 130;
                $positionx = 400;
                $positiony = 120 + $delta;

                //$this->cezpdf->addText(20, 230, 9, "Tipo de cambio LOSMM" . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
//SOLES EN LETRA
               // $this->cezpdf->addText(20, $positiony - 10, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
//nUMERO TOTAL
                $this->cezpdf->addText($positionx + 100, $positiony -190, 10, $moneda_simbolo . ' ' . number_format($total, 2));
            }

            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
        }
//********************

//**************************************************************************

    }

    /* Auxiliares */

    public function obtener_tipo_documento($tipo)
    {
        $tiponom = '';
        switch ($tipo) {
            case 'F':
                $tiponom = 'importacion';
                break;
            case 'B':
                $tiponom = 'boleta';
                break;
            case 'N':
                $tiponom = 'comprobante';
                break;
        }
        return $tiponom;
    }

    public function obtener_serie_numero($tipo_docu)
    {
        $data['numero'] = '';
        $data['serie'] = '';
        switch ($tipo_docu) {
            case 'F':
                $codtipodocu = '8';
                break;
            case 'B':
                $codtipodocu = '9';
                break;
            case 'N':
                $codtipodocu = '14';
                break;
            default:
                $codtipodocu = '0';
                break;
        }
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $codtipodocu);

        if (count($datos_configuracion) > 0) {
            $data['serie'] = $datos_configuracion[0]->CONFIC_Serie;
            $data['numero'] = $datos_configuracion[0]->CONFIC_Numero + 1;
        }
        return $data;
    }

    public function reportes()
    {
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

        $combo4 = '<select id="anioVenta4" name="anioVenta4">';
        $combo4 .= '<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo4 .= '<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo4 .= '</select>';

        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
        $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
        
        $data['titulo'] = "REPORTES DE VENTAS";
        $data['combo'] = $combo;
        $data['combo2'] = $combo2;
        $data['combo3'] = $combo3;
        $data['combo4'] = $combo4;
         $data['cbo_dpto'] = $this->seleccionar_departamento('00');
        $data['cbo_prov'] = $this->seleccionar_provincia('00', '01');
        $data['cbo_dist'] = $this->seleccionar_distritos('00', '01');
        $this->layout->view('ventas/comprobante_reporte', $data);
    }

    public function estadisticas()
    {
        /* Imagen 1 */
        $listado = $this->comprobante_model->reporte_ocompra_5_clie_mas_importantes();

        if (count($listado) == 0) { // Esto significa que no hay ordenes de compra por tando no muestros ningun reporte
            echo '<h3>Ha ocurrido un problema</h3>
                      <span style="color:#ff0000">No se ha encontrado Ã“rdenes de Venta</span>';
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
        echo '<h3>1. Los 5 clientes mÃ¡s importantes</h3>
               SegÃºn el monto (S/.) histÃ³rico Ã³rdenes de venta<br />
               <img style="margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen1.png" alt="Imagen 1" />';

        /* Imagen 2 */
        $listado = $this->comprobante_model->reporte_oventa_monto_x_mes();
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
        echo '<h3>2. Montos (S/.) de Ã³rdenes de venta segÃºn mes</h3>
               Considerando el presente aÃ±o<br />
               <img style="margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen2.png" alt="Imagen 2" />';


        /* Imagen 3 */
        $listado = $this->comprobante_model->reporte_oventa_cantidad_x_mes();
        $reg = $listado[0];

        $DataSet = new pData;
        $DataSet->AddPoint(array($reg->enero, $reg->febrero, $reg->marzo, $reg->abril, $reg->mayo, $reg->junio, $reg->julio, $reg->agosto, $reg->setiembre, $reg->octubre, $reg->noviembre, $reg->diciembre), "Serie1");
        $DataSet->AddPoint(array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"), "Serie2");
        $DataSet->AddAllSeries();
        $DataSet->RemoveSerie("Serie2");
        $DataSet->SetAbsciseLabelSerie("Serie2");
        $DataSet->SetYAxisName("Cantidad de O. de Venta");
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
        echo '<h3>3. Cantidades de Ã³rdenes de venta segÃºn mes</h3>
               Considerando el presente aÃ±o<br />
               <img style="margin-top:5px; margin-bottom:20px;" src="' . base_url() . 'images/img_dinamic/imagen3.png" alt="Imagen 3" />';

        /* Imagen 4 => COMPRAS */
//$listado=$this->ocompra_model->reporte_ocompra_monto_x_mes(); 
        $listado = $this->ocompra_model->reporte_comparativo_compras_ventas('V');
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
        echo '<h3>4. Ventas</h3>
               Considerando las ventas en el presente aÃ±o<br />
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
          echo 'Considerando las ventas en el presente aÃ±o<br />
          <img style="margin-top:5px; margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen5.png" alt="Imagen 5" />'; */
    }
     public function seleccionar_departamento($indDefault = '') {
        $array_dpto = $this->ubigeo_model->listar_departamentos();
        $arreglo = array();
        if (count($array_dpto) > 0) {
            foreach ($array_dpto as $indice => $valor) {
                $indice1 = $valor->UBIGC_CodDpto;
                $valor1 = $valor->UBIGC_DescripcionDpto;
                $arreglo[$indice1] = $valor1;
            }
        }
        $resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
        return $resultado;
    }

    public function seleccionar_provincia($departamento, $indDefault = '') {
        $array_prov = $this->ubigeo_model->listar_provincias($departamento);
        $arreglo = array();
        if (count($array_prov) > 0) {
            foreach ($array_prov as $indice => $valor) {
                $indice1 = substr($valor->UBIGC_CodProv,2,2);
                $valor1 = $valor->UBIGC_DescripcionProv;
                $arreglo[$indice1] = $valor1;
            }
        }
        $resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
        return $resultado;
    }
    public function seleccionar_distritos($departamento, $provincia, $indDefault = '') {
        $array_dist = $this->ubigeo_model->listar_distritos($departamento, $provincia);
        $arreglo = array();
        if (count($array_dist) > 0) {
            foreach ($array_dist as $indice => $valor) {
                $indice1 = substr($valor->UBIGC_CodDist,4,2);
                $valor1 = $valor->UBIGC_Descripcion;
                $arreglo[$indice1] = $valor1;
            }
        }
        $resultado = $this->html->optionHTML($arreglo, $indDefault, array('00', '::Seleccione::'));
        return $resultado;
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

        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
        $listado = $this->comprobante_model->buscar_comprobante_venta($fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso,$tipo_oper);

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
            $temp = $this->cliente_model->obtener_datosCliente($proveedor);
            if ($temp[0]->CLIC_TipoPersona == '0') {
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
        $this->cezpdf->ezText('REPORTE DE ORDENES DE VENTA', 17, $options);
        if (($fechai != '' && $fechaf != '') || $proveedor != '' || $producto != '' || $aprobado != '' || $ingreso != '') {
            $this->cezpdf->ezText('Filtros aplicados', 10, $options);
            if ($fechai != '' && $fechaf != '')
                $this->cezpdf->ezText('       - Fecha inicio: ' . $fechai . '   Fecha fin: ' . $fechaf, '', $options);
            if ($proveedor != '')
                $this->cezpdf->ezText('       - Cliente:  ' . $nomprovee, '', $options);
            if ($producto != '')
                $this->cezpdf->ezText('       - Producto:    ' . $nomprod, '', $options);
            if ($aprobado != '')
                $this->cezpdf->ezText('       - Aprobacion:   ' . $nomaprob, '', $options);
            if ($ingreso != '')
                $this->cezpdf->ezText('       - Ingreso:         ' . $nomingre, '', $options);
        }

        $this->cezpdf->ezText('', '', $options);

        $confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }

        /* Listado */

        foreach ($listado as $indice => $valor) {
            $db_data[] = array(
                'col1' => $indice + 1,
                'col2' => $valor->fecha,
                'col3' => $serie,
                'col4' => $valor->OCOMC_Numero,
                'col5' => $valor->cotizacion,
                'col6' => $valor->nombre,
                'col7' => $valor->MONED_Simbolo . ' ' . number_format($valor->OCOMC_total, 2),
                'col8' => $valor->aprobado,
                'col9' => $valor->ingreso
            );
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'SERIE',
            'col4' => 'NRO',
            'col5' => 'COTIZACION',
            'col6' => 'RAZON SOCIAL',
            'col7' => 'TOTAL',
            'col8' => 'C.INGRESO',
            'col9' => 'APROBACION'
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
                'col4' => array('width' => 30, 'justification' => 'center'),
                'col5' => array('width' => 55, 'justification' => 'center'),
                'col6' => array('width' => 200),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 50, 'justification' => 'center'),
                'col9' => array('width' => 60, 'justification' => 'center')
            )
        ));


            $this->cezpdf->ezText((''), 7, array("left" => 30));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

   
    public function ver_reporte_pdf_ventas($anio ,$mes ,$fech1 ,$fech2, $tipodocumento)
    {

    $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
 



        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');


        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_venta_3($anio ,$mes ,$fech1 ,$fech2 ,$tipodocumento);

        $confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }

        $titulo="REPORTE DE VENTAS";
        $fonttitle = array("leading" => 30, "left" => 150);
        $fontespacio = array("leading" => 20, "left" => 100);
        $fontdataright = array("leading" => 0, "left" => 350);
        
        $hoy = date("d-m-Y");
        $this->cezpdf->ezText($titulo, 17, $fonttitle);
        $this->cezpdf->ezText("", 17, $fontespacio);
         
        $this->cezpdf->ezText("FECHA DE REPORTE: ".$hoy, 8, $fontdataright);
        $this->cezpdf->ezText(" " ."  ", 10, $options);
        
        
        $codigo="";
        $sum = 0;
        foreach ($listado as $key => $value) {
            

            $sum += $value->CPC_total;
     
            $db_data[] = array(
                'col1' => $key + 1,
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $value->nombre,
                'col4' => $value->CPC_TipoDocumento,
                'col5' => $serie,
                'col6' => $value->CPC_Numero,
                'col7' => $value->MONED_Simbolo.$value->CPC_subtotal,
                'col8' => $value->MONED_Simbolo.$value->CPC_igv,
                'col9' => $value->MONED_Simbolo.$value->CPC_total
            );
        }
          
        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha de Registro',
            'col3' => 'NOMBRE O RAZON SOCIAL',
            'col4' => 'T. Doc.',
            'col5' => 'SERIE',
            'col6' => 'NRO',
            'col7' => 'VALOR DE VENTA',
            'col8' => 'I.G.V. 18%',
            'col9' => 'TOTAL'

        );

     $sum = $valor->MONED_Simbolo . ' ' . number_format($sum, 2);
      






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
                'col3' => array('width' => 100, 'justification' => 'center'),
                'col4' => array('width' => 25, 'justification' => 'center'),
                'col5' => array('width' => 30,'justification' => 'center'),
                'col6' => array('width' => 30, 'justification' => 'center'),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 60, 'justification' => 'center'),
                'col9' => array('width' => 60, 'justification' => 'center'),
                'col10' => array('width' => 60, 'justification' => 'center'),
                'col11' => array('width' => 60, 'justification' => 'center')
            )
        ));
        $this->cezpdf->ezText((''), 7, array("left" => 360));

         $this->cezpdf->ezText(('TOTAL'.'            '.$value->MONED_Simbolo.$sum), 7, array("left" => 380));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function ver_reporte_pdf_commpras($anio)
    {

          $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sbado");
          $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");

        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
      
 
     //$titulo=""; $subTitulo=""; 

     $titulo="REPORTE DE COMPRAS AL: ".$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');


      $this->cezpdf = new Cezpdf('a4');

      ///////////////////////////////////////////////////////////////////////////////////////////////////

/*
      $notimg="";
     $this->cezpdf = new Cezpdf('a4', 'portrait');
     $explorarData =explode('_', $dataEviar);
     $fechaini=$explorarData[0];
     $fechafin=$explorarData[1];
     $series=$explorarData[2];
     $numero=$explorarData[3];
     $ruc_clente=$explorarData[4];
     $nombre_cliente=$explorarData[5];
     $this->somevar['compania'];
        $filter = new stdClass();
        $filter->fecha_ini=$fechaini;//$fechaini;
        $filter->fecha_fin=$fechafin;//$fechaini;
        $filter->seriei =$series;
        $filter->numero =$numero;
        $filter->ruc_cliente =$ruc_clente;
        $filter->nombre_cliente =$nombre_cliente;
        $filter->ruc_proveedor =$ruc_clente;
        $filter->nombre_proveedor =$nombre_cliente;
        $listado_comprobantes = $this->comprobante_model->busqueda_comprobante($tipo_oper, $tipo_docu, $filter);

     

 $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');

   
*/
        $this->cezpdf->ezText($titulo ."  ", 17, $options);
        
       //$this->cezpdf->ezText("<center>".$subTitulo."</center>", 17, $options);  

         $this->cezpdf->ezText(("  "), 17, array("left" => 200));

        $this->cezpdf->ezText(($subTitulo), 17, array("left" => 200));

       
//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//pr

        /* Cabecera */
        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_compras($anio);

        $confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }

        /* Listado */
        $sum = 0;
        foreach ($listado as $key => $value) {
            $sum += $value->CPC_total;
            $db_data[] = array(
                'col1' => $key + 1,
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $serie,
                'col4' => $value->CPC_Numero,
                'col6' => $value->MONED_Simbolo.$value->CPC_subtotal,
                'col7' => $value->MONED_Simbolo.$value->CPC_igv,
                'col8' => $value->MONED_Simbolo.$value->CPC_total
            );
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'SERIE',
            'col4' => 'NRO',
            'col6' => 'VALOR DE VENTA',
            'col7' => 'I.G.V. 18%',
            'col8' => 'TOTAL',
        );

          $sum = $valor->MONED_Simbolo . ' ' . number_format($sum, 2);

        $db_data[] = array(
            'col1' => "",
            'col2' => "",
            'col3' => "",
            'col4' => "",
            'col5' => "",
            'col6' => "",
            'col7' => "",
            'col8' => "",
            'col9' => ""
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
                'col3' => array('width' => 50, 'justification' => 'center'),
                'col4' => array('width' => 30, 'justification' => 'center'),
                'col6' => array('width' => 50),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 50, 'justification' => 'center'),
                'col9' => array('width' => 60, 'justification' => 'center')
            )
        ));


       
          $this->cezpdf->ezText(" ", 7, array("left" => 322));
  
         $this->cezpdf->ezText(('TOTAL'.'            '.$value->MONED_Simbolo.$sum), 7, array("left" => 325));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    
    }



    public function estadisticas_compras_ventas($tipo, $anio)
    {
        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;
        $r = '';
        if ($tipo == "C") {
            $r = ' COMPRAS';
        } else {
            $r = ' VENTAS';
        }
        $options = array("leading" => 15, "left" => 0);
        $this->cezpdf->ezText('Usuario:  ' . $persona[0]->PERSC_Nombre . ' ' . $persona[0]->PERSC_ApellidoPaterno . ' ' . $persona[0]->PERSC_ApellidoMaterno . '       Fecha: ' . $fechahoy, 7, $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText('ESTADISTICAS DE' . $r . ' ANUALES', 17, $options);
        $this->cezpdf->ezText('', '', $options);

//$listado = $this->comprobante_model->buscar_comprobante_compras();
        $listado = $this->comprobante_model->estadisticas_compras_ventas($tipo, $anio);

        $confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }

        /* Listado */
        $datos_generales = '';
        $en = $fe = $ma = $ab = $may = $ju = $jul = $ag = $se = $oc = $no = $di = 0;
        $s_en = $s_fe = $s_ma = $s_ab = $s_may = $s_ju = $s_jul = $s_ag = $s_se = $s_oc = $s_no = $s_di = 0;
        foreach ($listado as $key => $value) {
            if ($value->EMPRC_RazonSocial != "") {
                $datos_generales = $value->EMPRC_RazonSocial;
            } else {
                $datos_generales = $value->PERSC_Nombre;
            }

            if ($value->mes == 1) {
                $en = $value->monto;
                $s_en += $value->monto;
            } else if ($value->mes == 2) {
                $fe = $value->monto;
                $s_fe += $value->monto;
            } else if ($value->mes == 3) {
                $ma = $value->monto;
                $s_ma += $value->monto;
            } else if ($value->mes == 4) {
                $ab = $value->monto;
                $s_ab += $value->monto;
            } else if ($value->mes == 5) {
                $may = $value->monto;
                $s_may += $value->monto;
            } else if ($value->mes == 6) {
                $ju = $value->monto;
                $s_ju += $value->monto;
            } else if ($value->mes == 7) {
                $jul = $value->monto;
                $s_jul += $value->monto;
            } else if ($value->mes == 8) {
                $ag = $value->monto;
                $s_ag += $value->monto;
            } else if ($value->mes == 9) {
                $se = $value->monto;
                $s_se += $value->monto;
            } else if ($value->mes == 10) {
                $oc = $value->monto;
                $s_oc += $value->monto;
            } else if ($value->mes == 11) {
                $no = $value->monto;
                $s_no += $value->monto;
            } else if ($value->mes == 12) {
                $di = $value->monto;
                $s_di += $value->monto;
            }

            /* switch($value->mes){
              case 1 : $en = $value->monto;
              case 2 : $fe = $value->monto;
              case 3 : $ma = $value->monto;
              case 4 : $ab = $value->monto;
              case 5 : $may = $value->monto;
              case 6 : $ju = $value->monto;
              case 7 : $jul = $value->monto;
              case 8 : $ag = $value->monto;
              case 9 : $se = $value->monto;
              case 10 : $oc = $value->monto;
              case 11 : $no = $value->monto;
              case 12 : $di = $value->monto;
              } */

            $db_data[] = array(
                'col1' => $datos_generales,
                'col2' => $en,
                'col3' => $fe,
                'col4' => $ma,
                'col5' => $ab,
                'col6' => $may,
                'col7' => $ju,
                'col8' => $jul,
                'col9' => $ag,
                'col10' => $se,
                'col11' => $oc,
                'col12' => $no,
                'col13' => $di
            );
            $en = $fe = $ma = $ab = $may = $ju = $jul = $ag = $se = $oc = $no = $di = 0;
        }

        $db_data[] = array(
            'col1' => "TOTAL",
            'col2' => $s_en,
            'col3' => $s_fe,
            'col4' => $s_ma,
            'col5' => $s_ab,
            'col6' => $s_may,
            'col7' => $s_ju,
            'col8' => $s_jul,
            'col9' => $s_ag,
            'col10' => $s_se,
            'col11' => $s_oc,
            'col12' => $s_no,
            'col13' => $s_di
        );

        $col_names = array(
            'col1' => 'CLIENTES',
            'col2' => 'ENERO',
            'col3' => 'FEBRERO',
            'col4' => 'MARZO',
            'col5' => 'ABRIL',
            'col6' => 'MAYO',
            'col7' => 'JUNIO',
            'col8' => 'JULIO',
            'col9' => 'AGOSTO',
            'col10' => 'SETIE.',
            'col11' => 'OCTU.',
            'col12' => 'NOVIE.',
            'col13' => 'DICIE.',
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 555,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 7,
            'cols' => array(
                'col1' => array('width' => 80, 'justification' => 'center'),
                'col2' => array('width' => 40, 'justification' => 'center'),
                'col3' => array('width' => 40, 'justification' => 'center'),
                'col4' => array('width' => 40, 'justification' => 'center'),
                'col5' => array('width' => 40, 'justification' => 'center'),
                'col6' => array('width' => 40, 'justification' => 'center'),
                'col7' => array('width' => 40, 'justification' => 'center'),
                'col8' => array('width' => 40, 'justification' => 'center'),
                'col9' => array('width' => 40, 'justification' => 'center'),
                'col10' => array('width' => 40, 'justification' => 'center'),
                'col11' => array('width' => 40, 'justification' => 'center'),
                'col12' => array('width' => 40, 'justification' => 'center'),
                'col13' => array('width' => 40, 'justification' => 'center')
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    private function meses($anio)
    {
        switch ($anio) {
            case 1 :
                return "ENERO";
            case 2 :
                return "FEBRERO";
            case 3 :
                return "MARZO";
            case 4 :
                return "ABRIL";
            case 5 :
                return "MAYO";
            case 6 :
                return "JUNIO";
            case 7 :
                return "JULIO";
            case 8 :
                return "AGOSTO";
            case 9 :
                return "SETIEMBRE";
            case 10 :
                return "OCTUBRE";
            case 11 :
                return "NOVIEMBRE";
            case 12 :
                return "DICIEMBRE";
        }
    }


    public function estadisticas_compras_ventas_mensual_excel($tipo, $anio, $mes)
    {
        $listado = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        echo '<script type="text/javascript" src="' . base_url() . 'js/ventas/reporteexcel.js"></script>
		<h2 >ESTADISTICAS DE VENTAS</h2>
		<a href="javascript:;" onclick="tableToExcel()" ><img  style="margin:15px 0px;"  src="' . base_url() . 'images/xls.png" width="22" height="22" class="imgBoton" ></a>
		<table id="Table1" border="1" >
		<tr width="100%" >
		<td>MES</td><td>FECHA</td>
		<td>NOMBRE / RAZON ZOCIAL</td>
		<td>DNI / RUC</td>
		<td>VALOR DE VENTA</td>
		<td>IGV</td>
		<td>VENTA</td>
		</tr>';

        foreach ($listado as $key => $value) {
            if ($value->EMPRC_RazonSocial != "") {
                $datos_generales = $value->EMPRC_RazonSocial;
            } else {
                $datos_generales = $value->PERSC_Nombre;
            }
            if ($value->EMPRC_Ruc != "") {
                $ruc_dni = $value->EMPRC_Ruc;
            } else {
                $ruc_dni = $value->PERSC_NumeroDocIdentidad;
            }

            echo ' <tr>';
            echo ' <td>' . $this->meses($value->mes) . '</td>';
            echo ' <td>' . substr($value->CPC_FechaRegistro, 0, 10) . '</td>';
            echo ' <td>' . $datos_generales . '</td>';
            echo ' <td>' . $ruc_dni . '</td>';
            echo ' <td>' . $value->CPC_subtotal . '</td>';
            echo ' <td>' . $value->CPC_igv . '</td>';
            echo ' <td>' . $value->monto . '</td>';
            echo ' </tr>';
            /*$db_data[] = array(
                'col1' => $this->meses($value->mes),
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $datos_generales,
                'col4' => $ruc_dni,
                'col5' => $value->CPC_subtotal,
                'col6' => $value->CPC_igv,
                'col7' => $value->monto
            );*/
        }

        echo '</table>';

    }

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes)
    {
        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;

//$listado = $this->comprobante_model->buscar_comprobante_compras();
        $listado = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        $r = '';
        if ($tipo == "C") {
            $r = ' COMPRAS';
        } else {
            $r = ' VENTAS';
        }
        $options = array("leading" => 15, "left" => 0);
        $this->cezpdf->ezText('Usuario:  ' . $persona[0]->PERSC_Nombre . ' ' . $persona[0]->PERSC_ApellidoPaterno . ' ' . $persona[0]->PERSC_ApellidoMaterno . '       Fecha: ' . $fechahoy, 7, $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText('ESTADISTICAS DE' . $r, 17, $options);
        $this->cezpdf->ezText('', '', $options);

        /* Listado */
        $datos_generales = '';
        $ruc_dni = '';
        foreach ($listado as $key => $value) {
            if ($value->EMPRC_RazonSocial != "") {
                $datos_generales = $value->EMPRC_RazonSocial;
            } else {
                $datos_generales = $value->PERSC_Nombre;
            }
            if ($value->EMPRC_Ruc != "") {
                $ruc_dni = $value->EMPRC_Ruc;
            } else {
                $ruc_dni = $value->PERSC_NumeroDocIdentidad;
            }

            $db_data[] = array(
                'col1' => $this->meses($value->mes),
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $datos_generales,
                'col4' => $ruc_dni,
                'col5' => $value->CPC_subtotal,
                'col6' => $value->CPC_igv,
                'col7' => $value->monto
            );
        }
        $col_names = array(
            'col1' => 'MES',
            'col2' => 'FECHA',
            'col3' => 'NOMBRE / RAZON SOCIAL',
            'col4' => 'DNI / RUC',
            'col5' => 'VALOR DE VENTA',
            'col6' => 'IGV',
            'col7' => 'TOTAL',
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 555,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 7,
            'cols' => array(
                'col1' => array('width' => 70, 'justification' => 'center'),
                'col2' => array('width' => 60, 'justification' => 'center'),
                'col3' => array('width' => 150, 'justification' => 'center'),
                'col4' => array('width' => 100, 'justification' => 'center'),
                'col5' => array('width' => 60, 'justification' => 'center'),
                'col6' => array('width' => 60, 'justification' => 'center'),
                'col7' => array('width' => 60, 'justification' => 'center')
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function formatos_de_impresion_F($codigo, $tipo_docu)
    {
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $usuario = $datos_comprobante[0]->USUA_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $fecha_formato = $datos_comprobante[0]->CPC_Fecha;
        $dia = substr($fecha, 0, 2);
        $mes = substr($fecha, 3, 2);
        $anio = substr($fecha, 6, 4);
        $mess = $this->meses($mes);
        $fecha_pie = $dia . '/ ' . $mes . '/ ' . $anio;
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
//<formade pago>
        $codigo_forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $cond_pago = 'NO DEFINIDO';
        if (strlen(trim($codigo_forma_pago)) > 0) {
            $forma_pago = $this->formapago_model->obtener($codigo_forma_pago);
            if (count($forma_pago) > 0) {
                $cond_pago = $forma_pago[0]->FORPAC_Descripcion;
            }
        }
//</formade pago>
        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
//$vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
        $vendedor = substr($temp[0]->PERSC_Nombre, 0, 1) . '. ' . $temp[0]->PERSC_ApellidoPaterno;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc = $datos_persona[0]->PERSC_Ruc;
            $telefono = $datos_persona[0]->PERSC_Telefono;
            $movil = $datos_persona[0]->PERSC_Movil;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $fax = $datos_persona[0]->PERSC_Fax;
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc = $datos_empresa[0]->EMPRC_Ruc;
            $telefono = $datos_empresa[0]->EMPRC_Telefono;
            $movil = $datos_empresa[0]->EMPRC_Movil;
            $fax = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            if ($emp_direccion) {
                $direccion = $emp_direccion[0]->EESTAC_Direccion;
            } else
                $direccion = "";
        }
        $data['seniores'] = utf8_decode_seguro($nombre_cliente);
        if (isset($direccion)) {
            $data['direccion'] = utf8_decode_seguro($direccion);
        } else {
            $data['direccion'] = '';
        }
        $data['ruc'] = utf8_decode_seguro($ruc);
        $data['vendedor'] = $vendedor;
        $data['numero_guia_remision'] = utf8_decode_seguro($guiarem_codigo);
        $data['fecha'] = utf8_decode_seguro($fecha);
//<tipo de cambio>
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['elmes'] = $mes;
        $data['dia'] = $dia;
        $data['mes'] = $mess;
        $data['fecha_pie'] = $fecha_pie;
        $data['anio'] = $anio;
        $data['documento_referencia'] = utf8_decode_seguro($docurefe_codigo);
        $data['serie_numero'] = $serie . '-&nbsp;&nbsp;' . $numero;
        $detalle_comprobante = $this->obtener_lista_detalles($codigo);
        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_comprobante as $indice => $valor) {
            if ($valor->CPDEC_Pu_ConIgv != '')
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
            $db_data[] = array(
                'item_numero' => $indice + 1,
                'item_cantidad' => $valor->CPDEC_Cantidad,
                'item_codProduct' => $valor->PROD_CodigoInterno,
                'item_unidad' => $valor->UNDMED_Simbolo,
                'item_codigo' => $valor->PROD_CodigoUsuario,
                'item_descripcion' => utf8_decode_seguro($valor->PROD_Nombre, true),
                'item_precio_unitario' => number_format($pu_conigv, 2),
                'item_importe' => number_format($valor->CPDEC_Total, 2)
            );
        }
        $fecha_formato = $datos_comprobante[0]->CPC_Fecha;
        $lista = $this->obtener_tipo_de_cambio($fecha_formato);
        if (count($lista) > 0) {
            $valido_fecha = explode('-', $lista[0]->TIPCAMC_Fecha);
            $anio_v = $valido_fecha[0];
            $mes_v = $valido_fecha[1];
            $dia_v = $valido_fecha[2];
            $valido_fecha = $dia_v . ' /' . $mes_v . ' /' . $anio_v;
            $data['valido_fecha'] = $valido_fecha;
            $data['factor_de_conversion'] = $lista[0]->TIPCAMC_FactorConversion;
        } else {
            $data['valido_fecha'] = 'NO PRESENTA';
            $data['factor_de_conversion'] = 'NO EXISTE';
        }
        $data['lista_items'] = $db_data;
        $data['cond_pago'] = $cond_pago;
        $son = strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre;
        $data['igv100'] = $igv100;
        $data['total_texto'] = $son;
        $data['total_bruto'] = $moneda_simbolo . ' ' . number_format($total, 2);
        $data['igv'] = $moneda_simbolo . ' ' . number_format($igv, 2);
        $data['subtotal'] = $moneda_simbolo . ' ' . number_format(($total - $igv), 2);
        $data['total'] = $moneda_simbolo . ' ' . number_format($total, 2);
        $data['descuento'] = $moneda_simbolo . ' ' . number_format($descuento, 2);
        $this->load->view('ventas/comprobante_ver_html', $data);
    }

    public function formatos_de_impresion_B($codigo, $tipo_docu)
    {
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $usuario = $datos_comprobante[0]->USUA_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $dia = substr($fecha, 0, 2);
        $mes = substr($fecha, 3, 2);
        $anio = substr($fecha, 6, 4);
        $data['mes_numero'] = $mes;
        $mess = $this->meses($mes);
        $fecha_pie = $dia . '/ ' . $mes . '/ ' . $anio;
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
//$vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
        $vendedor = substr($temp[0]->PERSC_Nombre, 0, 1) . '. ' . $temp[0]->PERSC_ApellidoPaterno;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc = $datos_persona[0]->PERSC_Ruc;
            $telefono = $datos_persona[0]->PERSC_Telefono;
            $movil = $datos_persona[0]->PERSC_Movil;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $fax = $datos_persona[0]->PERSC_Fax;
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc = $datos_empresa[0]->EMPRC_Ruc;
            $telefono = $datos_empresa[0]->EMPRC_Telefono;
            $movil = $datos_empresa[0]->EMPRC_Movil;
            $fax = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            if ($emp_direccion)
                $direccion = $emp_direccion[0]->EESTAC_Direccion;
            else {
                $direccion = "DESCONOCIDO";
            }
        }
//<tipo de cambio>
        $fecha_formato = $datos_comprobante[0]->CPC_Fecha;
        $lista = $this->obtener_tipo_de_cambio($fecha_formato);
        if (count($lista) > 0) {
            $valido_fecha = explode('-', $lista[0]->TIPCAMC_Fecha);
            $anio_v = $valido_fecha[0];
            $mes_v = $valido_fecha[1];
            $dia_v = $valido_fecha[2];
            $valido_fecha = $dia_v . ' /' . $mes_v . ' /' . $anio_v;
            $data['valido_fecha'] = $valido_fecha;
            $data['factor_de_conversion'] = $lista[0]->TIPCAMC_FactorConversion;
        } else {
            $data['valido_fecha'] = 'NO PRESENTA';
            $data['factor_de_conversion'] = 'NO EXISTE';
        }
        $data['seniores'] = utf8_decode_seguro($nombre_cliente);
        $data['direccion'] = utf8_decode_seguro($direccion);
        $data['ruc'] = utf8_decode_seguro($ruc);
        $data['vendedor'] = $vendedor;
        $data['numero_guia_remision'] = utf8_decode_seguro($guiarem_codigo);
        $data['fecha'] = utf8_decode_seguro($fecha);
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['dia'] = $dia;
        $data['mes'] = $mess;
        $data['descuento'] = $descuento;
        $data['serie_numero'] = $serie . '-&nbsp;&nbsp;' . $numero;
        $data['anio'] = $anio;
        $data['documento_referencia'] = utf8_decode_seguro($docurefe_codigo);
        $data['fecha_pie'] = $fecha_pie;
        $detalle_comprobante = $this->obtener_lista_detalles($codigo);
        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_comprobante as $indice => $valor) {
            if ($valor->CPDEC_Pu_ConIgv != '')
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
            $db_data[] = array(
                'item_numero' => $indice + 1,
                'item_cantidad' => $valor->CPDEC_Cantidad,
                'item_unidad' => $valor->UNDMED_Simbolo,
                'item_codigo' => $valor->PROD_CodigoUsuario,
                'item_descripcion' => utf8_decode_seguro($valor->PROD_Nombre, true),
                'item_precio_unitario' => number_format($pu_conigv, 2),
                'item_importe' => number_format($valor->CPDEC_Total, 2)
            );
        }
        $data['lista_items'] = $db_data;
        $data['lista_items'] = $db_data;
        $son = 'SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre;
        $data['total_texto'] = $son;
        $data['total_bruto'] = $moneda_simbolo . ' ' . number_format($total, 2);
        $data['igv'] = $moneda_simbolo . ' ' . number_format($igv, 2);
        $data['subtotal'] = $moneda_simbolo . ' ' . number_format(($total - $igv), 2);
        $data['total'] = $moneda_simbolo . ' ' . number_format($total, 2);
        $data['descuento'] = $moneda_simbolo . ' ' . number_format($descuento, 2);
        $this->load->view('ventas/boleta_ver_html', $data);
    }

    public function obtener_tipo_de_cambio($fecha_comprobante)
    {
        return $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);
    }

    public function ventana_muestra_comprobante($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = '')
    { // $formato: SELECT_ITEM, SELECT_HEADER, $docu_orig: DOCUMENTO QUE SOLICITA LA REFERENCIA, FACTURA, GUIA DE REMISION, ETC
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
        } else {
            $proveedor = $codigo;
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
        }
        $filter = new stdClass();
        $filter->cliente = $cliente;
        $filter->proveedor = $proveedor;

        $lista_comprobante = $this->comprobante_model->buscar_comprobantes_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_comprobante(" . $value->CPP_Codigo . " ," . $value->CPC_Serie . "," . $value->CPC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->CPC_Fecha), $value->CPC_Serie, $value->CPC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->CPC_total), $ver, $select);
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
        $data['docu_orig'] = $docu_orig;
        $data['formato'] = $formato;
        $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));

        $this->load->view('ventas/ventana_muestra_comprobante', $data);
    }


//gcbq
    public function ventana_muestra_recurrentes($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $tipo_doc = '', $almacen = "", $comprobante = '')
    {

        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $almacen_id = $almacen;
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


        $lista_compro = $this->comprobante_model->buscar_comprobantes($tipo_oper, $tipo_doc, $filter);

        $lista = array();

        foreach ($lista_compro as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento_recu(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $ir = "<a href='javascript:;' onclick='seleccionar_comprobante_recu(" . $value->CPP_Codigo . "," . $value->CPC_Serie . "," . $value->CPC_Numero . ")' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Guia de remision " . $value->GUIAREMC_Serie . " - " . $value->GUIAREMC_Numero . "' /></a>";
            $lista[] = array(mysql_to_human($value->GUIAREMC_Fecha), $value->CPC_Serie, $value->CPC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->CPC_total), $ver, $ir);

        }

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen_id;
        $data['tipo_oper'] = $tipo_oper;
        $data['comprobante'] = $comprobante;
        $data['tipo_doc'] = $tipo_doc;
        $data['form_open'] = form_open(base_url() . "index.php/almacen/guiarem/ventana_muestra_guiarem", array("name" => "frmGuiarem", "id" => "frmGuiarem"));

        $data['form_close'] = form_close();

        $data['form_hidden'] = form_hidden(array("base_url" => base_url()));


        //$this->load->view('ventas/ventana_muestra_comprobante', $data);
        $this->load->view('almacen/ventana_muestra_guiarem', $data);

    }


    public function comprobante_cambiar()
    {

//***************   INICIO  CAMBIO   ******************//        
//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                if ($flagGenInd[$indice] == 'I' && isset($_SESSION['serie']) && is_array($_SESSION['serie'][$valor])) {
                    if (count($_SESSION['serie'][$valor]) != $prodcantidad[$indice])
                        exit('{"result":"error2", "msj":"No ha ingresado todos los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
                } else
                    exit('{"result":"error2", "msj":"No ha ingresado los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
            }
        }

        $tipo_docu = $this->input->post('tipo_docu');
        $tipo_oper = $this->input->post('cboTipoDocu');

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);

        if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
            $presupuesto = $this->input->post('presupuesto');
        else
            $presupuesto = "";
//
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $ordencompra = $this->input->post('ordencompra');
        else
            $ordencompra = "";
//
        if ($this->input->post('guiaremision') != '' && $this->input->post('guiaremision') != '0')
            $guiaremision = $this->input->post('guiaremision');
        else
            $guiaremision = "";
//
        $serie = $this->input->post('serie');
//
        $numero = $this->input->post('numero');
//
        if ($tipo_oper == 'V') {
            $cliente = $this->input->post('cliente');
            $proveedor = "";
        } else {
            $cliente = "";
            $proveedor = $this->input->post('proveedor');
        }
//
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $forma_pago = $this->input->post('forma_pago');
        else
            $forma_pago = "";
//
        $moneda = $this->input->post('moneda');
//
        if ($tipo_docu != 'B') {
            $subtotal = $this->input->post('preciototal');
            $descuento = $this->input->post('descuentotal');
            $igv = $this->input->post('igvtotal');
        } else {
            $subtotal_conigv = $this->input->post('preciototal_conigv');
            $descuento_conigv = $this->input->post('descuentotal_conigv');
        }
        $total = $this->input->post('importetotal');
//
        $igv100 = $this->input->post('igv');
//
        $descuento100 = $this->input->post('descuento');
//
        $guiarem_codigo = strtoupper($this->input->post('guiaremision_codigo'));
        $docurefe_codigo = strtoupper($this->input->post('docurefe_codigo'));
//
        $observacion = strtoupper($this->input->post('observacion'));
//
        $modo_impresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $modo_impresion = $this->input->post('modo_impresion');
//
        $estado = $this->input->post('estado');
//
        $fecha = $this->input->post('fecha');
//
        if ($this->input->post('vendedor') != '')
            $vendedor = $this->input->post('vendedor');
        $tdc = $this->input->post('tdc');

        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
            }
        } elseif ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
            }
        }

        $data['codigo'] = "";
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, $tipo_docu, $codigo), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), $presupuesto, array('', '::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper, $codigo), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), $ordencompra, array('', '::Seleccione::'), ' / ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper, $codigo), 'GUIAREMP_Codigo', array('codigo', 'nombre'), $guiaremision, array('', '::Seleccione::'), ' / ');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', $forma_pago);
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', $moneda);
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $vendedor, array('', '::Seleccione::'), ' ');
        $data['serie'] = $serie;
        $data['numero'] = "1234"; //$numero;

        $data['descuento'] = $descuento100;
        $data['igv'] = $igv100;
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuento;
        $data['igvtotal'] = $igv;
        $data['importetotal'] = $total;
        $data['preciototal_conigv'] = $subtotal_conigv;
        $data['descuentotal_conigv'] = $descuento_conigv;
        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['titulo'] = "NUEVA "; //.strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        if ($tipo_oper == "V")
            $data['cboTipoDocu'] = $tipo_docu;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/ventas/importacion/comprobante_insertar";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        $data['hoy'] = $fecha;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo'] = $docurefe_codigo;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;
        $data['hidden'] = "";
        $data['focus'] = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger'] = "";
        $data['numero_suger'] = "";
        $data['tdc'] = $tdc;

//$detalle_comprobante    = $this->obtener_lista_detalles($codigo);
        $detalle_comprobante = array();

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $detacodi = "";
                $producto = $prodcodigo[$indice];
                if ($flagBS[$indice] == 'B')
                    $unidad = $produnidad[$indice];
                $cantidad = $prodcantidad[$indice];

                if ($tipo_docu != 'B') {
                    $pu = $prodpu[$indice];
                    $subtotal = $prodprecio[$indice];
                    $descuento = $proddescuento[$indice];
                    $igv = $prodigv[$indice];
                } else {
                    $subtotal_conigv = $prodprecio_conigv[$indice];
                    $descuento_conigv = $proddescuento_conigv[$indice];
                }
                $total = $prodimporte[$indice];
                $pu_conigv = $prodpu_conigv[$indice];
                $descuento100 = $proddescuento100[$indice];
                $igv100 = $prodigv100[$indice];

                if ($tipo_oper == 'V')
                    $costo = $prodcosto[$indice];

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $flagGenInd[$indice];
                $nombre_producto = (strtoupper($proddescri[$indice]) != '' ? strtoupper($proddescri[$indice]) : $datos_producto[0]->PROD_Nombre);
                $observacion = "";

                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->CPDEC_GenInd = $GenInd;
                $objeto->CPDEC_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_Cantidad = $cantidad;
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
                $lista_detalles[] = $objeto;

//$this->comprobantedetalle_model->insertar($filter);
            }
        }

        $data['detalle_comprobante'] = $lista_detalles;
    }

    ///gcbq
    public function ver_comprobantes_x_orden_producto($tipo_orden, $tipo_guia, $cod_orden, $cod_prod)
    {

        $COMPR = $this->comprobante_model->buscar_x_producto_orden($tipo_orden, $tipo_guia, $cod_orden, $cod_prod);

        $producto = $this->producto_model->obtener_producto($cod_prod);

        $lista_detalles = array();

        if (count($COMPR) > 0) {

            foreach ($COMPR as $key => $value) {
                $serie = $value->CPC_Serie;
                $numero = $value->CPC_Numero;
                $TipoDoc = $value->CPC_TipoDocumento;
                $fecha = mysql_to_human($value->CPC_Fecha);
                if ($value->PROVP_Codigo != '')
                    $datos_prove = $this->proveedor_model->obtener($value->PROVP_Codigo);
                else
                    $datos_prove = $this->cliente_model->obtener($value->CLIP_Codigo);

                $razon = $datos_prove->nombre;
                $cantidad = $value->CPDEC_Cantidad;
                $objeto = new stdClass();
                $objeto->TipoDoc = $TipoDoc;
                $objeto->numero = $numero;
                $objeto->fecha = $fecha;
                $objeto->cantidad = $cantidad;
                $objeto->razon = $razon;
                $objeto->serie = $serie;
                $lista_detalles[] = $objeto;


            }

        }
        $data['lista_detalles'] = $lista_detalles;
        $data['producto'] = $producto;
        $this->load->view("ventas/comprobante_x_orden_producto", $data);

    }
	    
	public function getOrderNumeroSerie($numero){
	 
	$cantidad=strlen($numero);
	
	if($cantidad==1){
	    $dato ="00000$numero";
	}
	if($cantidad==2){
	     $dato ="0000$numero";
	}
	if($cantidad==3){
	    $dato ="000$numero";
	}
	if($cantidad==4){
	    $dato= "00$numero";
	}
	if($cantidad==5){
	    $dato ="0$numero";
	}
	if($cantidad==6){
	    $dato ="$numero";
	}
	return $dato;
	}
	
	
	
	public function eliminarGuiaRelacionadasComprobante($tipo,$comprobante){
		/**verificamos para ELIMINAR LAS GUIAS RELACIONADAS TIPO:1**/
		/**modificamos a estado 0 LOS REGUISTROS ASOCIADOS AL DOCUMENTO y seriesDocumento asociado***/
		$estado=0;
		$this->comprobante_model->modificarEstadoDocumetoCodigoAsociado($comprobante,$estado);
		/**eliminamos las series creadas**/
		$this->seriedocumento_model->eliminarDocumetoCodigoAsociado($tipo,$comprobante);
		/**FIN DE ELIMINACION DE DOCUMENTOS***/
	
	}
//////////////////////////////////////////////


public function verPdf($tipo_oper = '', $tipo_docu = '',$dataEviar=""){
   $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
 


 $titulo=""; $subTitulo="";

 $fechhoy="".$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');

    if($tipo_oper=='V'){
      $titulo="REPORTE DE VENTAS ";

    }else{
       $titulo="REPORTE DE VENTAS ";

    }

    if($tipo_docu=='F'){
        $subTitulo="FACTURA";
    }

    elseif($tipo_docu=='B'){
        $subTitulo="BOLETA";
    }

    elseif($tipo_docu=='N'){
        $subTitulo="COMPROBANTE";
    }
     $notimg="";
     $this->cezpdf = new Cezpdf('a4', 'portrait');
     $explorarData =explode('_', $dataEviar);
     $fechaini=$explorarData[0];
     $fechafin=$explorarData[1];
     $series=$explorarData[2];
     $numero=$explorarData[3];
     $ruc_clente=$explorarData[4];
     $nombre_cliente=$explorarData[5];
     $this->somevar['compania'];
        $filter = new stdClass();
        $filter->fecha_ini=$fechaini;//$fechaini;
        $filter->fecha_fin=$fechafin;//$fechaini;
        $filter->seriei =$series;
        $filter->numero =$numero;
        $filter->ruc_cliente =$ruc_clente;
        $filter->nombre_cliente =$nombre_cliente;
        $filter->ruc_proveedor =$ruc_clente;
        $filter->nombre_proveedor =$nombre_cliente;
        $listado_comprobantes = $this->comprobante_model->busqueda_comprobante($tipo_oper, $tipo_docu, $filter);

     $options2 = array("leading" => 15, "left" => 30);

        $this->cezpdf->ezText($titulo ." ". $subTitulo, 17, $options2);
        $this->cezpdf->ezText(($fechhoy), 9, array("left" => 350));
        $this->cezpdf->ezText("", 17, $options);
       
$nombre="";
$db_data=array();
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                $sum += $valor->CPC_total;
                $codigo = $valor->CPP_Codigo;
                $fecha = mysql_to_human($valor->CPC_Fecha);
                $codigo_canje = $valor->CPP_Codigo_canje;
                $serie = $valor->CPC_Serie;
                $numero = $valor->CPC_Numero;
                $numero_ref ="";
                $usu=$valor->USUA_Codigo;
 $usuarioNom=$this->cliente_model->getUsuarioNombre($usu);
 $nomusuario="";
 if($usuarioNom[0]->ROL_Codigo==0){
    $nomusuario= $usuarioNom[0]->USUA_usuario;
    }else{
    $explorar= explode(" ",$usuarioNom[0]->PERSC_Nombre);
        
    $nomusuario= strtolower($explorar[0]);
 }
            if ($valor->CPC_DocuRefeCodigo != '') {
                    $list_com = $this->comprobante_model->obtener_comprobante_ref3($valor->CPC_DocuRefeCodigo);
                    if (count($list_com) > 0) {
                        $tipo_o = $list_com[0]->GUIAREMC_TipoOperacion;
                        $guiaremp_co = $list_com[0]->GUIAREMP_Codigo;
                        $num_gui = $list_com[0]->GUIAREMC_Numero;
                         $serie = $list_com[0]->GUIAREMC_Serie;
                         $numero_ref=$serie." - ".$num_gui;
                    }
                }

                if ($tipo_oper == "V") {
                    if ($valor->CLIP_Codigo == 144 && $valor->CPC_NombreAuxiliar != 'cliente') {
                        $nombre = strtoupper($valor->CPC_NombreAuxiliar);
                    }
                    else {
                        $nombre = $valor->nombre;
                    }
                } else {
                    $nombre = $valor->nombre;
                }
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->CPC_total, 2);
               
               $db_data[] = array(
                'col1' => $indice + 1,
                'col2' => $fecha,
                'col3' => $serie,
                'col4' => $numero,
                //'col5' => $numero_ref,
                'col5' => $nombre,
                'col6' => $total
            ); 
 
        }
        }
         $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'SERIE',
            'col4' => 'NRO',
            //'col5' => 'GUIA REMISION',
            'col5' => 'RAZON SOCIAL',
            'col6' => 'TOTAL'
            
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
                'col3' => array('width' => 50, 'justification' => 'center'),
                'col4' => array('width' => 50, 'justification' => 'center'),
                //'col5' => array('width' => 55, 'justification' => 'center'),
                'col5' => array('width' => 220),
                'col6' => array('width' => 60, 'justification' => 'center')
            )
        ));
$sum = $valor->MONED_Simbolo . ' ' . number_format($sum, 2);
  
      $this->cezpdf->ezText("", 7, '');
$this->cezpdf->ezText(('TOTAL'.'                  '.$sum), 7, array("left" => 388));

       

 $this->cezpdf->ezText('', 8);
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $tipo_doc . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);       

}	



 public function verificar_inventariado(){
    $codigo=$this->input->post('enviarCodigo');
   $variable= $this->comprobante_model->verificar_inventariado($codigo);
   $resultado="";
   if (count($variable)>0) {
    
    //foreach ($variable as $key => $value) {
      $resultado="1";
     //}
}else{
$resultado="0";
}
echo $resultado;
    }   
	
	  public function ver_reporte_pdf_productos($anio ,$mes ,$fech1 ,$fech2, $tipodocumento,$Prodcod)
        {
        	$usuario = $this->usuario_model->obtener($this->somevar['user']);
        	$persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        	$fechahoy = date('d/m/Y');
        	//$this->load->library('cezpdf');
        	//$this->load->helper('pdf_helper');
        	//prep_pdf();
        	$this->cezpdf = new Cezpdf('a4');
        
        	/* Cabecera */
        	$delta = 20;
        	
        	$productname = $this->producto_model->nameproducto($Prodcod);
        
        	$listado = $this->comprobante_model->buscar_comprobante_producto($anio ,$mes ,$fech1 ,$fech2 ,$tipodocumento,$Prodcod);
        
        	$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
        	$serie = '';
        	
        	$prodn=$productname[0]->PROD_Nombre;
        	
        	$titulo="REPORTE DE VENTA POR PRODUCTO";
        	$fonttitle = array("leading" => 50, "left" => 100);
        	$fontespacio = array("leading" => 10, "left" => 100);
        	$fontdata = array("leading" => 12, "left" => 40);
        	$fontdataright = array("leading" => 0, "left" => 350);
        	 
        	$hoy = date("d-m-Y");
        	$this->cezpdf->ezText($titulo ." ". $subTitulo, 17, $fonttitle);
        	$this->cezpdf->ezText("", 17, $fontespacio);
        	
        	$this->cezpdf->ezText("PRODUCTO: ".$prodn, 8, $fontdata);
        	$this->cezpdf->ezText("FECHA DE REPORTE: ".$hoy, 8, $fontdataright);
        	
        	if($anio!="--" && $mes =="--" ){// SOLO AÃ‘O
        		$this->cezpdf->ezText("AÃ‘O: ".$anio, 8, $fontdata);
        	}
        	if($anio!="--" && $mes !="--" ){// MES Y  AÃ‘O
        		$this->cezpdf->ezText("AÃ‘O : ".$anio. "     MES : ".$this->get_mesletra($mes), 8, $fontdata);
        	}
        	if($anio=="--" && $mes !="--"){//MES CON AÃ‘O ACTUAL
        		$this->cezpdf->ezText("AÃ‘O : ".date("Y"). "    MES : ".$this->get_mesletra($mes), 8, $fontdata);
        	}
        	
        	//-----------------
        		
        	if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2=="--" ){//FECHA INICIAL
        		$this->cezpdf->ezText("DESDE: ".$fech1, 8, $fontdata);
        	}
        	if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2!="--"){//FECHA INICIAL Y FECHA FINAL
        		$this->cezpdf->ezText("DESDE: ".$fech1. "   HASTA: ".$fech2, 8, $fontdata);
        	}
        	
        	
        	foreach ($confi as $key => $value) {
        		if ($value->DOCUP_Codigo == 15) {
        			$serie = $value->CONFIC_Serie;
        		}
        	}
        
        	/* Listado */
        	$codigo="";
        	$sum = 0;
        	$this->cezpdf->ezText("", 17, $fontespacio);
        	foreach ($listado as $key => $value) {
        		 
        
        		$sum += $value->CPC_total;
        		$db_data[] = array(
        				'col1' => $key + 1,
        				'col2' => substr($value->CPC_FechaRegistro, 0, 10),
        				'col3' => $value->CPC_TipoDocumento,
        				'col4' => $serie,
        				'col5' => $value->CPC_Numero,
        				'col6' =>  $value->nombre,
        				'col7' => $value->MONED_Simbolo. $value->CPC_subtotal,
        				'col8' => $value->MONED_Simbolo.$value->CPC_igv,
        				'col9' => $value->MONED_Simbolo.$value->CPC_total
        		);
        	}
        	$sum = $value->MONED_Simbolo . ' ' . number_format($sum, 2);
        
        	$col_names = array(
        			'col1' => 'Itm',
        			'col2' => 'Fecha de Registro',
        			'col3' => 'T. Doc.',
        			'col4' => 'SERIE',
        			'col5' => 'NRO',
        			'col6' => 'RAZON SOCIAL',
        			'col7' => 'VALOR DE VENTA',
        			'col8' => 'I.G.V. 18%',
        			'col9' => 'TOTAL'
        
        	);
        
        	$db_data[] = array(
        			'col1' => "",
        			'col2' => "",
        			'col3' => "",
        			'col4' => "",
        			'col5' => "",
        			'col6' => "",
        			'col7' => "",
        			'col8' => "TOTAL",
        			'col9' => $sum,
        			'col10' => "",
        			'col11' => ""
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
        					'col3' => array('width' => 25, 'justification' => 'center'),
        					'col4' => array('width' => 30,'justification' => 'center'),
        					'col5' => array('width' => 30, 'justification' => 'center'),
        					'col6' => array('width' => 130, 'justification' => 'center'),
        					'col7' => array('width' => 60, 'justification' => 'center'),
        					'col8' => array('width' => 60, 'justification' => 'center'),
        					'col9' => array('width' => 60, 'justification' => 'center'),
        					'col10' => array('width' => 60, 'justification' => 'center'),
        					'col11' => array('width' => 60, 'justification' => 'center')
        			)
        	));
        
        	$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        	$this->cezpdf->ezStream($cabecera);
        }
 public function encuentrax_producto() {
        
        	$codigoProducto = $this->input->post('codigo'); //captura de ajax mando un valor
        	$result = array();
        
        		
        	if($codigoProducto!=null && count(trim($codigoProducto))>0){
        			
        		$consultaNombre =$datosTipoCaja = $this->comprobante_model->autocompleteProducto($keyword);
        		if($consultaNombre != null && count($consultaNombre)>0){
        			foreach ($datosTipoCaja as $indice => $valor) {
        				$nombre = $valor-> PROD_Nombre;
        				$codigito = $valor->  PROD_Codigo;
        				$result[] = array( "value" => $nombre ,"codigo" => $codigito);
        
        			}
        		}
        
        	}
        
        	echo json_encode($result);
        
        }
        

        public function get_mesletra($monthnumber){
        	switch ($monthnumber) {
        		case 1:
        			return "Enero";
        			break;
        		case 2:
        			return "Febrero";
        			break;
        		case 3:
        			return "Marzo";
        			break;
        	    case 4:
        	    	return "Abril";
        			break;
        		case 5:
        			return "Mayo";
        			break;
        		case 6:
        			return "Junio";
        			break;
        		case 7:
        			return "Julio";
        			break;
        		case 8:
        			return "Agosto";
        			break;
        		case 9:
        			return "Septiembre";
        			break;
        		case 10:
        			return "Octubre";
        			break;
        		case 11:
        			return "Noviembre";
        			break;
        		case 12:
        			return "Diciembre";
        			break;
        	}
        }

    public function ventana_muestra_importaciones($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = '')
    {
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $almacen_id = $almacen;
        
        $proveedor = $codigo;
        $datos_proveedor = $this->proveedor_model->obtener($proveedor);
        if ($datos_proveedor) {
            $nombre_proveedor = $datos_proveedor->nombre;
            $ruc_proveedor = $datos_proveedor->ruc;
        }
        $filter = new stdClass();
        $filter->proveedor = $proveedor;

        $filter->liquidada = 1;

        $lista_importaciones = $this->importacion_model->buscar($filter);
        $lista = array();
        foreach ($lista_importaciones as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_importacion(" . $value->IMPOR_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $ir = "<a href='javascript:;' onclick='seleccionar_importacion(" . $value->IMPOR_Codigo . "," . $value->IMPOR_Serie . "," . $value->IMPOR_Numero . ")' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Guia de remision " . $value->IMPOR_Serie . " - " . $value->IMPOR_Numero . "' /></a>";
            $lista[] = array(mysql_to_human($value->IMPOR_Fecha), $value->IMPOR_Serie, $value->IMPOR_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->IMPOR_total), $ver, $ir);
        }


        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen_id;
        $data['tipo_oper'] = $tipo_oper;
        $data['comprobante'] = $comprobante;
        $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmImportacion", "id" => "frmImportacion"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url()));
        $this->load->view('ventas/ventana_muestra_comprobante', $data);
    }

    public function cantidad_registrada($ocodigo,$prod,$cant,$pend){
        $cant=$cant;
        $pend=$pend;
        $verificar_cantidad=$this->importacion_model->verificar_cantidad($ocodigo,$prod);
        $cantidad=$verificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$verificar_cantidad->OCOMDEC_Pendiente;  //2
        $cant_impor=$verificar_cantidad->IMPORDEC_Cantidad; //2

         $calcula=$pendiente+$pend;
        if($calcula>=$cant ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
        }elseif($calcula<=$cant){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$calcula;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    }
    public function calcula_cantidad_pendiente($ocodigo,$prod,$cant){
        $cant=$cant;
        $verificar_cantidad=$this->importacion_model->calcula_cantidad_pendiente($ocodigo,$prod);
        $cantidad=$verificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$verificar_cantidad->OCOMDEC_Pendiente;  //2

        if($pendiente>=$cant ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
        }elseif($pendiente<=$cant){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$pendiente;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    }
     public function obtener_detalle_importacion($idImportacion)
    {
        $detalle = $this->importaciondetalle_model->listar($idImportacion);
        $lista_detalles = array();
        $datos_guiarem = $this->importacion_model->obtener_comprobante($idImportacion);
        $moneda = $datos_guiarem[0]->MONED_Codigo;
        $serie = $datos_guiarem[0]->IMPOR_Serie;
        $numero = $datos_guiarem[0]->IMPOR_Numero;
        $codigo_usuario = $datos_guiarem[0]->IMPOR_CodigoUsuario;
        $cliente = $datos_guiarem[0]->CLIP_Codigo;
        $proveedor = $datos_guiarem[0]->PROVP_Codigo;
        if ($tipo_oper == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($tipo_oper == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);

        if ($datos) {
            $ruc = $datos->ruc;
            $razon_social = $datos->nombre;
        } else {
            $ruc = "";
            $razon_social = "";
        }


        if (count($detalle) > 0) {
            
            foreach ($detalle as $indice => $valor) {
                $detacod = $valor->IMPORDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->IMPORDEC_Cantidad;
                $flagGenInd = $valor->IMPORDEC_GenInd;
                $pu = $valor->IMPORDEC_Pu;
                $subtotal = $valor->IMPORDEC_Subtotal;
                $igv = $valor->IMPORDEC_Igv;
                $descuento = $valor->IMPORDEC_Descuento;
                $pu_conigv = $valor->IMPORDEC_Pu_ConIgv + $valor->IMPORDEC_Costo_uni_liquidado;
                $total = round($pu_conigv * $cantidad, 2);
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $costo = $datos_producto[0]->PROD_UltimoCosto;
                $almacenProducto = $datos_guiarem[0]->ALMAP_Codigo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                if ($datos_almaprod)
                    $stock = $datos_almaprod[0]->ALMPROD_Stock;
                else
                    $stock = "";

                $nombre_producto = str_replace('"', "''", $valor->IMPORDEC_Descripcion);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;

               
                $objeto = new stdClass();
                $objeto->GUIAREMDETP_Codigo = $detacod;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->UNDMED_Descripcion = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->GUIAREMDETC_Cantidad = $cantidad;
                $objeto->GUIAREMDETC_Pu = $pu;
                $objeto->GUIAREMDETC_Subtotal = $subtotal;
                $objeto->GUIAREMDETC_Descuento = $descuento;
                $objeto->GUIAREMDETC_Igv = $igv;
                $objeto->GUIAREMDETC_Total = $total;
                $objeto->GUIAREMDETC_Pu_ConIgv = $pu_conigv;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->CLIP_Codigo = $cliente;
                $objeto->PROVP_Codigo=$proveedor;
                $objeto->MONED_Codigo = $moneda;
                $objeto->GUIAREMC_Serie = $serie;
                $objeto->GUIAREMC_Numero = $numero;
                $objeto->GUIAREMC_CodigoUsuario = $codigo_usuario;
                $objeto->ALMAP_Codigo =$almacenProducto;
                $objeto->GUIAREMDETC_GenInd =$flagGenInd;

                $objeto->PROD_CodigoUsuario = $datos_producto[0]->PROD_CodigoUsuario;
                $objeto->PROD_FlagBienServicio = $datos_producto[0]->PROD_FlagBienServicio;
                
                $objeto->onclick = $producto . ",'" . $codigo_interno . "','" . $nombre_producto . "'," . $cantidad . ",'" . $flagBS . "','" . $flagGenInd . "'," . $unidad_medida . ",'" . $nombre_unidad . "'," . $pu_conigv . "," . $pu . "," . $subtotal . "," . $igv . "," . $total . "," . $stock . "," . $costo;
                if($objeto->PROD_FlagBienServicio == "B") $lista_detalles[] = $objeto;
            }

        } else {

            $objeto = new stdClass();
            $objeto->GUIAREMDETP_Codigo = '';
            $objeto->Ruc = $ruc;
            $objeto->RazonSocial = $razon_social;
            $objeto->CLIP_Codigo = $cliente;
            $objeto->MONED_Codigo = $moneda;
            $objeto->GUIAREMC_Serie = $serie;
            $objeto->GUIAREMC_Numero = $numero;
            $objeto->GUIAREMC_CodigoUsuario = $codigo_usuario;
            $lista_detalles[] = $objeto;
        }
        $resultado = json_encode($lista_detalles);
        echo $resultado;

    }

     public function obtener_detalle_importacion_guia_ingreso($idImportacion)
    {
        $detalle = $this->importaciondetalle_model->listar($idImportacion);
        $lista_detalles = array();
        $datos_guiarem = $this->importacion_model->obtener_comprobante($idImportacion);
        $moneda = $datos_guiarem[0]->MONED_Codigo;
        $serie = $datos_guiarem[0]->IMPOR_Serie;
        $numero = $datos_guiarem[0]->IMPOR_Numero;
        $codigo_usuario = $datos_guiarem[0]->IMPOR_CodigoUsuario;
        $cliente = $datos_guiarem[0]->CLIP_Codigo;
        $proveedor = $datos_guiarem[0]->PROVP_Codigo;
        if ($tipo_oper == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($tipo_oper == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);

        if ($datos) {
            $ruc = $datos->ruc;
            $razon_social = $datos->nombre;
        } else {
            $ruc = "";
            $razon_social = "";
        }


        if (count($detalle) > 0) {
            
            foreach ($detalle as $indice => $valor) {
                $detacod = $valor->IMPORDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->IMPORDEC_Cantidad;
                $flagGenInd = $valor->IMPORDEC_GenInd;
                $pu = $valor->IMPORDEC_Pu;
                $subtotal = $valor->IMPORDEC_Subtotal;
                $igv = $valor->IMPORDEC_Igv;
                $descuento = $valor->IMPORDEC_Descuento;
                $pu_conigv = $valor->IMPORDEC_Pu_ConIgv + $valor->IMPORDEC_Costo_uni_liquidado;
                $total = round($pu_conigv * $cantidad, 2);
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $costo = $datos_producto[0]->PROD_UltimoCosto;
                $almacenProducto = $datos_guiarem[0]->ALMAP_Codigo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                if ($datos_almaprod)
                    $stock = $datos_almaprod[0]->ALMPROD_Stock;
                else
                    $stock = "";

                $nombre_producto = str_replace('"', "''", $valor->IMPORDEC_Descripcion);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;

               
                $objeto = new stdClass();
                $objeto->GUIAREMDETP_Codigo = $detacod;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->UNDMED_Descripcion = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->GUIAREMDETC_Cantidad = $cantidad;
                $objeto->GUIAREMDETC_Pu = $pu;
                $objeto->GUIAREMDETC_Subtotal = $subtotal;
                $objeto->GUIAREMDETC_Descuento = $descuento;
                $objeto->GUIAREMDETC_Igv = $igv;
                $objeto->GUIAREMDETC_Total = $total;
                $objeto->GUIAREMDETC_Pu_ConIgv = $pu_conigv;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->CLIP_Codigo = $cliente;
                $objeto->PROVP_Codigo=$proveedor;
                $objeto->MONED_Codigo = $moneda;
                $objeto->GUIAREMC_Serie = $serie;
                $objeto->GUIAREMC_Numero = $numero;
                $objeto->GUIAREMC_CodigoUsuario = $codigo_usuario;
                $objeto->ALMAP_Codigo =$almacenProducto;
                $objeto->GUIAREMDETC_GenInd =$flagGenInd;

                $objeto->PROD_CodigoUsuario = $datos_producto[0]->PROD_CodigoUsuario;
                $objeto->PROD_FlagBienServicio = $datos_producto[0]->PROD_FlagBienServicio;
                
                $objeto->onclick = $producto . ",'" . $codigo_interno . "','" . $nombre_producto . "'," . $cantidad . ",'" . $flagBS . "','" . $flagGenInd . "'," . $unidad_medida . ",'" . $nombre_unidad . "'," . $pu_conigv . "," . $pu . "," . $subtotal . "," . $igv . "," . $total . "," . $stock . "," . $costo;

                if($objeto->PROD_FlagBienServicio == "B") {
                    if(!isset($lista_detalles[$objeto->PROD_Codigo])) {
                        $lista_detalles[$objeto->PROD_Codigo] = $objeto;
                    }else{
                        $lista_detalles[$objeto->PROD_Codigo]->GUIAREMDETC_Cantidad += $nueva_cantidad = $objeto->GUIAREMDETC_Cantidad;
                        $lista_detalles[$objeto->PROD_Codigo]->GUIAREMDETC_Subtotal = round($nueva_cantidad * $objeto->GUIAREMDETC_Pu, 2);
                    }
                }
            }

        } else {

            $objeto = new stdClass();
            $objeto->GUIAREMDETP_Codigo = '';
            $objeto->Ruc = $ruc;
            $objeto->RazonSocial = $razon_social;
            $objeto->CLIP_Codigo = $cliente;
            $objeto->MONED_Codigo = $moneda;
            $objeto->GUIAREMC_Serie = $serie;
            $objeto->GUIAREMC_Numero = $numero;
            $objeto->GUIAREMC_CodigoUsuario = $codigo_usuario;
            $lista_detalles[] = $objeto;
        }
        
        echo json_encode($lista_detalles);

    }
}
	
	

?>