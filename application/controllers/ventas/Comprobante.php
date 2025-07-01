<?php
 ini_set('error_reporting', 1); 
include("application/libraries/pchart/pData.php");
include("application/libraries/pchart/pChart.php");
include("application/libraries/cezpdf.php");
include("application/libraries/class.backgroundpdf.php");
include("application/libraries/lib_fecha_letras.php");
include("application/controllers/maestros/configuracionimpresion.php");

class Comprobante extends CI_Controller{

    private $empresa;
    private $compania;
    private $usuario;
    private $rol;
    private $url;
    private $view_js = NULL;    

    public function __construct(){
		
        parent::__construct();
        date_default_timezone_set('America/Lima');   
		
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');

        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('tokens'); #CARGAR EL TOKEN
        $this->load->library('lib_props');
        $this->load->library('movimientos');
        $this->load->library("lib_comprobantes");

        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/ocompra_model');

        $this->load->model('ventas/comprobante_model');
        $this->load->model('ventas/importacion_model');
        $this->load->model('ventas/comprobantedetalle_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/presupuesto_model');
        $this->load->model('ventas/parqueo_model');

        $this->load->model('tesoreria/caja_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/cuota_model');
        
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('almacen/inventario_model');
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
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/tipocambio_model');
        
        $this->load->model('seguridad/usuario_model');
        $this->load->model('configuracion_model');
        //$this->load->library('My_PHPMailer');

        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
        $this->rol = $this->session->userdata('rol');
        $this->url = base_url();
        $this->load->library('cezpdf');
        $this->load->helper('pdf_helper');
        $this->view_js = array(0 => "ventas/comprobante.js");
        prep_pdf();        
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

    public function cargar_comprobante($codigo_documento){
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




    public function obtener_detalle_comprobante($comprobante, $tipo_oper = 'V', $almacen = ""){
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
                $prod_codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
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
                $objeto->PROD_CodigoUsuario = $prod_codigo_usuario;
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

    public function obtener_detalle_comprobante_x_numero_com($serie, $numero, $tipo_oper = 'V', $almacen = ""){
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


    public function fecha_limite($fecha, $days = 7, $oper = "+", $fecha2 = NULL ){
        #if ($fecha == date('Y-m-d')) // LA BOLETA O FACTURA NO PODRA ELIMINARSE SI NO HASTA EL DIA SIGUIENTE.
        #    return false;
            
        if ($fecha2 == NULL)
            $fecha2 = date('Y-m-d');

        $nuevafecha = strtotime("$oper$days days" , strtotime($fecha) ) ;
        $fechaLimite = date( 'Y-m-d' , $nuevafecha );

        if ($fechaLimite >= $fecha2 )
            return true;
        else
            return false;
    }

    public function comprobantes($tipo_oper = '', $tipo_docu = '', $j = '0'){
        $data['compania'] = $this->compania;
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);
        $data['action'] = 'index.php/ventas/comprobante/comprobantes/' . $tipo_oper . '/' . $tipo_docu;
        $conf['base_url'] = site_url('ventas/comprobante/comprobantes/' . $tipo_oper . '/' . $tipo_docu);
        $registros = $this->comprobante_model->contar_comprobantes($tipo_oper, $tipo_docu, NULL, '', '');
        
        $data['registros'] = count($registros);
        $data['titulo_tabla'] = "RELACIÓN DE " . strtoupper($this->obtener_tipo_documento($tipo_docu)) . "S";
        $data['titulo_busqueda'] = "BUSCAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = $tipo_docu;
        switch ($tipo_docu) {
            case 'F':
                $data['id_documento'] = 8;
                break;
            case 'B':
                $data['id_documento'] = 9;
                break;
            case 'N':
                $data['id_documento'] = 14;
                break;
            
            default:
                $data['id_documento'] = "";
                break;
        }
        $data["series_emitidas"] = $this->comprobante_model->getSeriesEmitidas($tipo_oper, $tipo_docu, $this->compania);
        $lista = array();
        $data['lista'] = $lista;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => $tipo_docu));
        $data['scripts'] = $this->view_js;
        $this->layout->view('ventas/comprobante_index', $data);
    }

    public function datatable_comprobantes($tipo_oper = '', $tipo_docu = ''){
    	$posDT = -1;
        $columnas = array(
                            ++$posDT => "CPC_FechaRegistro",
                            ++$posDT => "CPC_Fecha",
                            ++$posDT => "CPC_Serie",
                            ++$posDT => "CPC_Numero",
                            ++$posDT => "CLIC_CodigoUsuario",
                            ++$posDT => "numdoc",
                            ++$posDT => "nombre",
                            ++$posDT => "CPC_total",
                            ++$posDT => "",
                            ++$posDT => "CPC_FlagEstado"
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
        $filter->fecha_ini = ($fecha_ini != "") ? $fecha_ini : ""; # date("Y-m") . '-1';
        $fecha_fin = $this->input->post('fechaf');
        $filter->fecha_fin = ($fecha_fin != "") ? $fecha_fin : ""; # date("Y-m-d");
        $filter->tipo_docu = $tipo_docu;
        $filter->tipo_oper = $tipo_oper;
        $filter->seriei = $this->input->post('seriei');
        $filter->numero = $this->input->post('numero');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');
        $filter->nombre_proveedor = $this->input->post('nombre_proveedor');
        $listado_comprobantes = $this->comprobante_model->getComprobantes($filter);   
        $data['compania'] = $this->compania;
        $lista = array();
        $rol = $this->rol;
        $estadoSA = true;
        $indiceEstados = array();

        if ($listado_comprobantes != NULL) {
            
            foreach ($listado_comprobantes as $indice => $valor) {

                $arrLetra = array("F" => 8, "B" => 9);
                $letraParaConvertir = $valor->CPC_TipoDocumento; 
                $ConversorDeNumero = $arrLetra[$letraParaConvertir];
                $codigo = $valor->CPP_Codigo;
                $fechaR = $valor->CPC_FechaR;
                $fecha  = $valor->CPC_Fecha;
                $codigo_canje = $valor->CPP_Codigo_canje;
                $serie  = $valor->CPC_Serie;
                $numero = $valor->CPC_Numero;
                $numero_ref = $serie . '-' . $numero;
                $estadoAsociacion='';
                //$listaGuiaremAsociados=$this->comprobante_model->buscarComprobanteGuiarem($codigo,$estadoAsociacion);
        
                $img_estado = "";
                
                /*$guiarem_codigo = '';
                $docurefe_codigo = '<a href="'.base_url().'index.php/compras/ocompra/ocompra_ver_pdf_conmenbrete/'.$valor->idOC.'/1" data-fancybox data-type="iframe">'.$valor->seriOC.'</a>';
                if ( count($listaGuiaremAsociados) > 0 ){
                    foreach ( $listaGuiaremAsociados as $j => $valorGuiarem ){
                        $codigoGuiarem = $valorGuiarem->GUIAREMP_Codigo;
                        $serieG = $valorGuiarem->GUIAREMC_Serie;
                        $numeroG = $valorGuiarem->GUIAREMC_Numero;
                        $estadoRelacion = $valorGuiarem->COMPGUI_FlagEstado;
                        $guiarem_codigo .= "<a href='".base_url()."index.php/almacen/guiarem/guiarem_ver_pdf/$codigoGuiarem/a4/1' data-fancybox data-type='iframe'><span>$serieG-$numeroG</span></a><br>";
                    }
                }
                else
                    if ($valor->sin_lote >= 0 && $tipo_docu != "N" && $tipo_oper == "V")
                        $guiarem_codigo = "<a href='javascript:;' onclick='canjeToGuia($codigo, $item)' title='Generar Guia de Remisión' style='color:red'>G. Guia</a>";
                */
                
                $nombre = $valor->nombre;
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->CPC_total, 2);
                $estado = $valor->CPC_FlagEstado;
                $fechaLimite = $valor->CPC_Fecha;
                $enviarcorreo = "";

                if ( $rol == 1 || $rol == 7000  || $rol == 7005 || $_SESSION['user_name'] == 'ccapasistemas' ){
                    if ( $estado == 1 || $estado == 2 ){
                        if ( $this->fecha_limite($fechaLimite,14) == true && $estado == 1 || $tipo_oper == 'C' && $estado == 1 || $tipo_docu == 'N' && $estado == 1 )
                            $img_estado = "<a href='".base_url()."index.php/seguridad/usuario/ventana_confirmacion_usuario2/$serie/$codigo/$tipo_oper/$tipo_docu"."' data-fancybox data-type='iframe'> <img src='" . base_url() . "public/images/active.png' alt='Activo' title='Activo'/></a>";
                        else
                            $img_estado = "";
                    }
                    else
                        $img_estado = "";
                    
                    //Agregue para poder anular comprobantes fuera de fecha
                    //$img_estado = "<a href='".base_url()."index.php/seguridad/usuario/ventana_confirmacion_usuario2/$serie/$codigo/$tipo_oper/$tipo_docu"."' data-fancybox data-type='iframe'> <img src='" . base_url() . "public/images/active.png' alt='Activo' title='Activo'/></a>";                    
                    
                }
                else {
                    $editar = "";
                    $contadoVacios++;
                }

                $pdfImprimir = "<a href='".base_url()."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/TICKET' data-fancybox data-type='iframe'><img src='".base_url()."public/images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $pdfImprimir2 = "<a href='".base_url()."index.php/ventas/comprobante/comprobante_ver_pdf/$codigo/a4' data-fancybox data-type='iframe'><img src='".base_url()."public/images/pdf.png' width='16' height='16' border='0' title='ver A4'></a>";

                if ($estado == 1)
                    $editar = "<img src='" . base_url() . "public/images/completado.png' width='16' height='16' border='0' title='Completado'>";
                else
                    if ($estado == '2'){
                        $editar = "<a href='javascript:;' onclick='editar_comprobante($codigo)' target='_parent'><img src='" . base_url() . "public/images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    }

                $pdfSunat = "";
                $excel =  "<a onclick='comprobante_download_excel($codigo);' href='#' class='excel'><img src='" . base_url() . "public/images/excel.png' width='16' height='16' border='0' title='Excel'></a>" ;
                $xml = "";
                                
                if ($estado == '1' && $tipo_oper == 'V') {
                    if($tipo_docu != 'N'){
                        $pdfSunat .= "<a href='#' onclick='abrir_pdf_envioSunat($codigo);' target='_parent'><img src='" . base_url() . "public/images/pdf-sunat.png' width='16' height='16' border='0' title='pdf sunat'></a>";
                    }
                    $enviarcorreo =  "<a onclick='open_mail($codigo);' href='javascript:;' class='enviarcorreo'><img src='" . base_url() . "public/images/send.png' width='16' height='16' border='0' title='Enviar comprobante via correo'></a>";
                    $pdfCompromiso = "<a href='".base_url()."index.php/ventas/comprobante/comprobante_garantia/$codigo' data-fancybox data-type='iframe'><img src='".base_url()."public/images/compromiso.png' width='16' height='16' border='0' title='ver garantia'></a>";
                }
                
                if ($estado == '2') {
                    if ($valor->sin_lote >= 0){ # 0 TIENE LOTE, != 0 EXISTEN ARTICULOS SIN LOTE
                        $disparador = "<a href='javascript:;' onclick='disparador($codigo, $item)'>Aprobar</a>";
                        if ($tipo_oper == 'V'){
                            $respSunat = $this->comprobante_model->lsResSunat($codigo, '1,2');
                            if ($respSunat != NULL){
                                if ( $respSunat->respuestas_deta != "" )
                                    $disparador .= "<br> <span class='detallesWrong'>Denegado <span class='detallesWrong2'> $respSunat->respuestas_deta </span> </span>";
                            }
                        }
                    }
                    else{
                        if ($tipo_oper == "V")
                            $disparador = "<a href='javascript:;' onclick='asignar_lotes($codigo, $item)'>Asignar Lotes</a>";
                        else
                            $disparador = "<a href='javascript:;'>Faltan Lotes</a>";
                    }
                }
                else {
                    $disparador = "";
                    $contadoVacios++;
                }

                $motivoAnulacion = NULL;
                if ($estado == 0){
                    $motivoAnulacion = explode(' * ',$valor->CPC_Observacion);
                    $size = count($motivoAnulacion);
                    $motivo = ($size > 1) ? $motivoAnulacion[$size-1] : 'SIN MOTIVO REGISTRADO.';
                    $img_estado = "<div> <span class='detallesAnulado'> <img src='".base_url()."public/images/icons/inactive.png' alt='Anulado' title='Anulado'/> <span class='detallesAnulado2'><b>DETALLES:</b> $motivo </span> </span> </div>";
                    $editar = "";
                    $pdfCompromiso = "";
                    //$xml = "<a href='".$this->consutarRespuestaXmlsunat($codigo,false)."' target='_parent' style='cursor:pointer;'><img src='" . base_url() . "public/images/xml.png' width='16' height='16' border='0' title='XML Sunat'></a>";
                }

                /**TIPO:N, verificamos si es de tipo Comprobante y verificamos si se canjeo**/
                $numeroSerieCanjeado="";
                $comprobantesRelacion="";
                if($tipo_docu=='N' && $tipo_oper=='V'){
                    /**si tiene codigo de canjear**/
                    if($codigo_canje!=null && $codigo_canje!='0'){
                        /**OBTENER DATOS DE COMPROBANTE**/
                        $datosComprobanteCanje=$this->comprobante_model->obtener_comprobante($codigo_canje);
                        $tipoDocumentoCanje=$datosComprobanteCanje[0]->CPC_TipoDocumento;
                        $serieCanje=$datosComprobanteCanje[0]->CPC_Serie;
                        $numeroCanje=$datosComprobanteCanje[0]->CPC_Numero;
                        $numeroSerieCanjeado=$tipoDocumentoCanje." : ".$serieCanje.'-'.$numeroCanje;
                    }
                }
                else{                    
                    if($tipo_oper=='V'){
                        $listaRelacionadosCanje=$this->comprobante_model->buscarComprobanteRelacionadoCanje($codigo);
                        if(count($listaRelacionadosCanje)>0){
                            /**muestrsa la impresion**/
                            foreach ($listaRelacionadosCanje as $ind=>$valorCanje){
                                $serieCanjeR=$valorCanje->CPC_Serie;
                                $numeroCanjeR=$valorCanje->CPC_Numero;
                                $comprobantesRelacion.=$serieCanjeR."-".$numeroCanjeR;
                                $comprobantesRelacion.=" <br>";
                            }
                        } 
                    }
                }
                
                if ($tipo_oper == 'C') {
                    $ConversorDeNumero = "";
                    $numeroSerieCanjeado = "";
                    $comprobantesRelacion = "";
                    $pdfCompromiso = "";
                    $valor->CPP_Compracliente = "";
                }

                $canjeHTML = "";
                if ($tipo_docu == 'N' && $estado == 1)
                    if ($codigo_canje == '' || $codigo_canje == NULL || $codigo_canje == 0)
                        $canjeHTML =  "<a onclick='canjear_documento($codigo);' href='javascript:;' class=''>Canjear</a>";
                $posDT = -1;
                $lista[] = array(
                                ++$posDT => "<div align='center' class='fecha_data_$item'>$fechaR</div>",
                                ++$posDT => "<div align='center' class='fecha_data_$item'>$fecha</div>",
                                ++$posDT => $serie,
                                ++$posDT => $this->lib_props->getOrderNumeroSerie($numero),
                                ++$posDT => $valor->CLIC_CodigoUsuario,
                                ++$posDT => $valor->numdoc,
                                ++$posDT => $nombre,
                                ++$posDT => $total,
                                ++$posDT => "<div align='center' class='img_estado_$item'> <span class='icon-loading'></span> <span class='img_estado_data_$item'>$img_estado</span> </div>",
                                ++$posDT => "<div align='center' class='editar_data_$item'><span class='icon-loading'></span> $editar</div>",
                                ++$posDT => $pdfImprimir,
                                ++$posDT => $pdfImprimir2,
                                ++$posDT => $excel,
                                ++$posDT => "<div align='center' class='pdfSunat_$item'> <span class='icon-loading'></span> <span class='pdfSunat_data_$item'>$pdfSunat</span> </div>",
                                ++$posDT => "<div align='center' class='compromiso_data_$item'>$pdfCompromiso</div>",
                                ++$posDT => "<div align='center' class='enviarcorreo_data_$item'>$enviarcorreo</div>",
                                ++$posDT => "<div align='center' class='disparador_$item'> <span class='icon-loading'></span> <span class='disparador_data_$item'>$disparador</span> </div>" . $canjeHTML
                                );
                $contadoVacios = 1;
                $item++;
            }
        }
        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;
        $filterAll->tipo_docu = $tipo_docu;
        $filterAll->count = true;
        $filter->count = true;
        $recordsTotal = $this->comprobante_model->getComprobantes($filterAll);
        $recordsFiltered = $this->comprobante_model->getComprobantes($filter);
        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => $recordsTotal->registros,
                            "recordsFiltered" => $recordsFiltered->registros,
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function OPTION_generador($datos,$indice,$valor,$tam=1){
        $fila = "";
        if($datos!=NULL){
            foreach($datos as $value){
                eval("\$ind = \$value->$indice;");
                eval("\$val = \$value->$valor;");
                $selected = ($ind==$tam)?"selected='selected'":"";
                $fila .= "<option value='".$ind."' ".$selected." >".$val."</option>'";
            }
        }
        return $fila;
    }

    /**
     * Modulo para visualizar 3 tipos => Factura(F), Boleta(B) o Comprobante(N)
     * EL URI: Sirve para obtener el numero de segmento seleccionado
     * ejmp: htt p://localhost/ccmi/index.php/ventas/comprobante/comprobante_nueva / V / Factura
     *                                        1   /    2       /     3           /  4 /   5
     * @param string $tipo_oper
     * @param string $tipo_docu
     */
    public function comprobante_nueva($tipo_oper = '', $tipo_docu = ''){       
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);
        $tipoDocumento = strtoupper($this->obtener_tipo_documento($tipo_docu));
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/
        if($tipoDocumento == '')
        {
            redirect(base_url().'index.php/index/inicio');
        }else {
            
            // Variables
            $compania = $this->compania;
            $codigo = "";
            $punto_llegada = "";
            unset($_SESSION['serie']);
            /*PARA CUOTAS*/
            /* // CUOTAS*/
            $comp_confi = $this->companiaconfiguracion_model->obtener($compania);
            $data['compania'] = $compania;
            //Para cambio comprobante_A
            $data['cambio_comp'] = "0";
            $data['total_det'] = "0";   
            $data['codigo'] = $codigo;
            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
            $data['contiene_igv'] = ($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false;
            $oculto = form_hidden(
                        array(
                            'codigo' => $codigo, 
                            'base_url' => base_url(), 
                            'tipo_oper' => $tipo_oper, 
                            'tipo_docu' => $tipo_docu, 
                            'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')
                        )
                    );
            $data['url_action'] = base_url() . "index.php/ventas/comprobante/comprobante_insertar";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmComprobante";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $data['usa_adelanto'] = 0;
            $lista_almacen     = $this->almacen_model->seleccionar();
            $data['idalmacen'] = key($lista_almacen);
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
            $data['guia'] = "";
            $data['cboproyecto'] =$this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');
            $data['cboimportacion'] =$this->OPTION_generador($this->importacion_model->listar_importacion(0), 'IMPOR_Codigo', 'IMPOR_Nombre', '2');
    
    /*$data['cboimportacion'] = form_dropdown("importacion", $this->importacion_model->listar_importacion(0), obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");*/

            $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
            $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion','29');
            $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboOrdencompra'] = NULL; #$this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
            $data['cboGuiaRemision'] = NULL; #$this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['direccionsuc'] = form_input(array("name" => "direccionsuc", "id" => "direccionsuc", "class" => "cajaGeneral", "size" => "40", "maxlength" => "250", "value" => $punto_llegada));
            //$data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');    

            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', 'PERSC_ApellidoPaterno', '', array('', '::Seleccione::'), ' ');    
            
            $data['cboVendedor'] = $this->lib_props->listarUsuarios($this->session->userdata('cajero_id'));
            $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));
            if (count($cambio_dia) > 0) {
                $data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
            } else {
                $data['tdcDolar'] = 3;
            } 
            
            $data['serie'] = '';
            $data['numero'] = '';
            if ($tipo_oper == 'V') {
                $temp = $this->obtener_serie_numero($tipo_docu);
            }
            
            //Cliehnte por defecto
            $cod_cliente_defecto = "";
            $ruc_cliente_defecto = "";
            $nombre_cliente_defecto = "";
            if($tipo_docu == 'B'){
                $dd_datos_cliente = $this->cliente_model->getCliente(421);
                if($dd_datos_cliente != NULL){
                    $cod_cliente_defecto    = $dd_datos_cliente[0]->CLIP_Codigo;
                    $ruc_cliente_defecto    = $dd_datos_cliente[0]->numero;
                    $nombre_cliente_defecto = $dd_datos_cliente[0]->razon_social;
                }
            }
            //!Clientes por defecto

            $data['cliente'] = $cod_cliente_defecto;
            $data['ruc_cliente'] = $ruc_cliente_defecto;
            $data['nombre_cliente'] = $nombre_cliente_defecto;
            
            $data['proveedor'] = "";
            $data['ruc_proveedor'] = "";
            $data['nombre_proveedor'] = "";
            $data['detalle_comprobante'] = array();
            $data['observacion'] = "";
            $data['focus'] = "";
            $data['pedido'] = "";
            $data['descuento'] = "0";
            $data['igv'] = $comp_confi[0]->COMPCONFIC_Igv;
            $data['igv_default'] = $comp_confi[0]->COMPCONFIC_Igv;
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
            $data['oc_cliente'] = "";
            $data["codigoRetencion"] = "";
            $data["porcRetencion"] = "3";
            $data['modo_impresion'] = "1";
            if ($tipo_docu != 'B') {
                if (FORMATO_IMPRESION == 1)
                    $data['modo_impresion'] = "2";
                else
                    $data['modo_impresion'] = "1";
            }
            $data['hoy'] = date("Y-m-d");
            $data['fecha_vencimiento'] = date("Y-m-d");
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
            //obtengo las series de la configuracion
            if ($tipo_docu == 'F') {
                $tipo = 8;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }
            if ($tipo_docu == 'N') {
                $tipo = 14;
            }
            /**gcbq limpiamos la session de series guardadas**/
            unset($_SESSION['serie']);
			unset($_SESSION['serieReal']);
			unset($_SESSION['serieRealBD']);
            /**fin de limpiar session***/
            //$hoy = date('Y-m-d H:m:s');
            //var_dump(microtime());
			$listaGuiarem=array();
			$listaGuiarem=null;
			$data['listaGuiaremAsociados']=$listaGuiarem;
            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            //$ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'B');
            $data['serie_suger_b'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_b'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            //$ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'F');
            $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_f'] =$this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            $data['cmbVendedor']=$this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            if ($tipo_oper == 'V') {
                $data['serie'] = $cofiguracion_datos[0]->CONFIC_Serie;
                $data['numero'] = $this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            }
            $data['caja']      = $_SESSION["caja_activa"];
            $data['cajero_id'] = $_SESSION["cajero_id"];
            $filter = new stdClass();
            $filter->situacion = 1;
            $data['cajas'] = $this->caja_model->getCajas($filter);
            $data['scripts'] = $this->view_js;
            $data['btnCancelVisible'] = 'S';
            $this->layout->view('ventas/comprobante_nueva', $data);
        }
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
    
    
    public function create_comprobante_parqueo(){
        $filter = new stdClass();
        $filter->tipo_docu   = $this->input->post("tipo_docu");
        $filter->codservicio = $this->input->post("codservicio");
        $filter->fpago       = 29;
        $filter->cliente     = 421;//Cliente varios
        //if($filter->tipo_docu == 'B')    $filter->cliente = 421;//Cliente varios
        $comprobante = $this->lib_comprobantes->create_comprobante($filter,true);
        
        if($comprobante){
            $info = array("codcomprobante"=>$comprobante);
            $json = array("match" => true, "info" => $info);
        }
        else{
            $json = array("match" => false, "info" => NULL);
        }
        echo json_encode($json);        
    }
    
    public function create_comprobante_abonado(){
        $tipo_docu = $this->input->post("tipo_docu");
        $cliente   = $this->input->post("cliente");
        
        //Vehiculos information
        $filter = new stdClass();
        $filter->cliente = $cliente;
        $datos_vehiculos = $this->cliente_model->getVehiculos($filter,false);
        $vehiculos = array();
        if(count($datos_vehiculos) > 0){
            foreach($datos_vehiculos as $value){
                $vehiculos[] =  $value->CLIEVEHIP_Placa;
            }
        }
        $txtVehiculos = implode(",",$vehiculos);
        
        //Cliente information
        $datos_cliente = $this->cliente_model->getCliente($cliente);        
        if($datos_cliente != NULL && $datos_vehiculos != NULL){
            $fechai  = date("Y-m-",time()).
                       date("d", strtotime($datos_cliente[0]->CLIC_FechaIngreso)); 
            $fechaf  = date("Y-m-d",strtotime($fechai." + 1 month - 1 days"));
            $monto   = $datos_cliente[0]->CLIC_Monto;
            $fpago   = $datos_cliente[0]->FORPAP_Codigo;
            
            //Create Service
            $descripcion = "SERV. ESTACIONAMIENTO PLACA ".$txtVehiculos." DEL ".$fechai." AL ".$fechaf;
            $service = $this->lib_comprobantes->crear_servicio($descripcion,$monto);
            
            //Create invoice
            $filter = new stdClass();
            $filter->tipo_docu   = $tipo_docu;
            $filter->codservicio = $service;
            $filter->cliente     = $cliente;
            $filter->fpago       = $fpago;
            $comprobante = $this->lib_comprobantes->create_comprobante($filter);
            
            if($comprobante){
                $info = array("comprobante"=>$comprobante);
                $json = array("match" => true, "info" => $info, "message" => "Registro exitoso");
                
                //Update Client
                $filter = new stdClass();
                $filter->CLIC_MesFacturacion = (int)date("Ym",time());
                $filter->CLIC_FlagSituracion = 1;
                $filter->CLIC_FechaModificacion = date("Y-m-d H:i:s");
                $this->cliente_model->actualizar_cliente($cliente,$filter);
            }
            else{
                $json = array("match" => false, "info" => NULL, "message" => "Error: no se pudo crear el comprobante");
            }
            
        }
        else{
            $json = array("match" => false, "info" => NULL, "message" => "No existen datos del cliente y/o vehiculo");
        }        
       
        echo json_encode($json);           
    }

    public function comprobante_insertar(){

        $this->load->helper('my_guiarem');

        if ($this->input->post('serie') == ''){
            $json = array("result" => false, "campo" => "serie");
            return NULL;
        }
        if ($this->input->post('numero') == ''){
            $json = array("result" => false, "campo" => "numero");
            return NULL;
        }
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == ''){
            $json = array("result" => false, "campo" => "ruc_cliente");
            return NULL;
        }
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == ''){
            $json = array("result" => false, "campo" => "ruc_proveedor");
            return NULL;
        }
        if ($this->input->post('moneda') == '0' || $this->input->post('moneda') == ''){
            $json = array("result" => false, "campo" => "moneda");
            return NULL;
        }
        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == ''){
            $json = array("result" => false, "campo" => "observacion");
            return NULL;
        }
        if ($this->input->post('tdcDolar') == ''){
            $json = array("result" => false, "campo" => "tdcDolar");
            return NULL;
        }
        if ($this->input->post('almacen') == ''){
            $json = array("result" => false, "campo" => "almacen");
            return NULL;
        }

		//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $obra = $this->input->post('obra');
        $proyecto = $this->input->post('proyecto');
        $importacion = $this->input->post('importacion');
        $dref = $this->input->post('dRef');
        $guiarem_id = $this->input->post("codigo");
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $accion = $this->input->post("detaccion");
        $filter = new stdClass();
        $filter->CPC_TipoOperacion = $tipo_oper;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->ALMAP_Codigo = $this->input->post('almacen');
        $filter->CPC_NumeroAutomatico = $this->input->post('numeroAutomatico');

        $this->load->model('maestros/documento_model');
        $documento = $this->documento_model->obtenerAbreviatura(trim($tipo_docu));
        $tipo = $documento[0]->DOCUP_Codigo;
        $compania = $this->compania;

        if ( $this->input->post("forma_pago") != "" && $this->input->post("forma_pago") != "0" ){
            $filter->FORPAP_Codigo = $this->input->post("forma_pago");
            $f_pago = $this->input->post("forma_pago");
        }

        $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo); // Vuelve a consultar el ultimo correlativo - 
        $filter->CPC_Numero = ($tipo_oper == 'V') ? $configuracion_datos[0]->CONFIC_Numero + 1 : $this->input->post('numero'); // Incrementa en numero el ultimo correlativo consultado para ingresar.
                
        $filter->CPC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CPC_Fecha = $this->input->post('fecha');
        $filter->CPC_FechaVencimiento = $this->input->post('fecha_vencimiento');
        $filter->CPC_Hora = date("H:i:s");
        $filter->CPC_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CPC_descuento100 = $this->input->post('descuento');
        $filter->CPC_igv100 = $this->input->post('igv');
        $filter->CPC_Tipo_venta = $this->input->post('tipo_venta');
        $filter->CPC_FlagEstado = 2;
        $nombre = $this->input->post('nombre_cliente');

        //$filter->CPC_FlagUsaAdelanto = $this->input->post("adelanto");
        $filter->CPC_FlagUsaAdelanto = 1;

        if ($tipo_oper == 'V'){
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $filter->CPC_Direccion = $this->input->post('direccionsuc');
        }
        else
            $filter->PROVP_Codigo = $this->input->post('proveedor');

        
        if ($this->input->post('presupuesto_codigo') != '' && $this->input->post('presupuesto_codigo') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto_codigo');
        
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
      
        if ($this->input->post('oc_cliente') != '' && $this->input->post('oc_cliente') != '0')
            $filter->CPP_Compracliente = $this->input->post('oc_cliente');
        
        $filter->CPC_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CPC_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CPC_ModoImpresion = '1';

        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->CPC_ModoImpresion = $this->input->post('modo_impresion');

        $filter->CPC_subtotal = $this->input->post('preciototal');
        $filter->CPC_descuento = $this->input->post('descuentotal');
        $filter->CPC_igv = $this->input->post('igvtotal');

        $filter->CPC_total = $this->input->post('importetotal');

        if ($this->input->post('cboVendedor') != '')
            $filter->CPC_Vendedor = $this->input->post('cboVendedor');
        else
            $filter->CPC_Vendedor = $_SESSION['persona'];

        $filter->CPC_TDC = $this->input->post('moneda') > 2 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');
        $filter->CPC_TDC_opcional = $this->input->post('moneda') > 2 ? $this->input->post('tdcDolar') : NULL;

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        //Datos cabecera de la guiasa.

        if ($dref!=null  && trim($dref)=='')
            $filter->GUIAREMP_Codigo = $dref;

        $filter->PROYP_Codigo = $obra;
        
        if($tipo_oper == 'C')
            $filter->IMPOR_Nombre = $importacion;
        
        $filter->CAJA_Codigo = $this->input->post('caja');
        $filter->CPC_Retencion = $this->input->post('retencion_codigo');
        $filter->CPC_RetencionPorc = $this->input->post('retencion_porc');
        $comprobante = $this->comprobante_model->insertar_comprobante($filter);

        /*:::::::::: CUOTAS ::::::::::::::::*/
        $cuota_fechai = $this->input->post('cuota-fechai');
        $cuota_fechaf = $this->input->post('cuota-fechaf');
        $monto_cuotas = $this->input->post('cuota-monto');

        $tipo_tributo = "";
        if (isset($f_pago) && $f_pago != 1) {
            if($monto_cuotas != ""){
                foreach ($monto_cuotas as $indice => $cuota) {
                    $cuotasData = new stdClass();
                    $cuotasData->CUOT_Numero = $indice + 1;
                    $cuotasData->CPP_Codigo = $comprobante;
                    $cuotasData->CUOT_Monto = $monto_cuotas[$indice];
                    $cuotasData->CUOT_FechaInicio = $cuota_fechai[$indice];
                    $cuotasData->CUOT_Fecha = $cuota_fechaf[$indice];
                    $cuotasData->CUOT_FlagFisica = 0;
                    $cuotasData->CUOT_FlagEstado = 1;
                    $cuotasData->CUOT_FlagPagado = 0;
                    $cuotasData->CUOT_UsuarioRegistro = $_SESSION['user'];
                    $cuotasData->CUOT_TipoCuenta = ($tipo_oper == "V") ? 1 : 2;
                    $this->cuota_model->registrar($cuotasData);
                }
            }
        }

        /*:::::: /// CUOTAS /// :::::::::::*/

        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodpu = $this->input->post('prodpu');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $prodigv = $this->input->post('prodigv');
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
        $oventacod =$this->input->post('oventacod');
        $pendiente =$this->input->post('pendiente');
        $obs_detalle = $this->input->post('prodobservacion');
        $lote = $this->input->post('idLote');
        $tafectacion = $this->input->post('tafectacion');
        $icbper = $this->input->post('icbper');
        // gcbq ---orden de compra total bienes que existe 
        $cantidad_entregada_total = 0;
        $cantidad_total_ingresada = 0;
        $cant_total = 0;
        $ordencompra=$this->input->post('ordencompra');
        if($ordencompra!=''){
	        $detalle = $this->ocompra_model->obtener_detalle_ocompra($ordencompra);
	        if (is_array($detalle) > 0) {
	            foreach ($detalle as $valor2) {
	                $cant_total += $valor2->OCOMDEC_Cantidad;
	            }
	        }
        }
        ///////////////

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $filter = new stdClass();
                $filter->CPP_Codigo = $comprobante;
                $filter->PROD_Codigo = $prodcodigo[$indice];

                if ($produnidad[$indice] == '' || $produnidad[$indice] == "null") {
                    $produnidad[$indice] = NULL;
                }

                $filter->UNDMED_Codigo = $produnidad[$indice];
                $filter->LOTP_Codigo = $lote[$indice];
                $filter->AFECT_Codigo = $tafectacion[$indice];
                $filter->CPDEC_ITEMS = $icbper[$indice];

                $filter->CPDEC_Cantidad = $prodcantidad[$indice];
                
                if(isset($pendiente[$indice]) && !is_null($pendiente[$indice])) $filter->CPDEC_Pendiente = $pendiente[$indice];
                
                $filter->CPDEC_Pu = $prodpu[$indice];
                $filter->CPDEC_Subtotal = $prodprecio[$indice];
                $filter->CPDEC_Descuento = $proddescuento[$indice];
                $filter->CPDEC_Igv = $prodigv[$indice];
                $filter->CPDEC_Total = $prodimporte[$indice];
                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
                $filter->CPDEC_Igv100 = $prodigv100[$indice];
                $codigoAlmacenProducto = $almacenProducto[$indice];
                $filter->ALMAP_Codigo = $codigoAlmacenProducto;
                
                //gcbq agrgar flagestado de terminado ocompra 
                if ($ordencompra != '' && $detaccion[$indice]!="e" && $detaccion[$indice]!="EE") {
	                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper,$ordencompra, $filter->PROD_Codigo);
					$cantidad_entregada_total += $cantidad_entregada;
	                $cantidad_total_ingresada += $prodcantidad[$indice];
	                if ($cant_total <= $cantidad_entregada_total + $cantidad_total_ingresada) {
	                    $this->ocompra_model->modificar_flagTerminado($this->input->post('ordencompra'), "1");
	                }
	                if ($cant_total > $cantidad_entregada_total + $cantidad_total_ingresada) {
	                    $this->ocompra_model->modificar_flagTerminado($this->input->post('ordencompra'), "0");
	                }
                }
                ///////////////////


                ////stv    va ser nuevo precio costo en compra
                if ($tipo_oper == 'C') {
                    $filter->CPDEC_Costo = $prodpu_conigv[$indice];
                }
                ////

                if(isset($oventacod[$indice]) && !is_null($oventacod[$indice])) $filter->OCOMP_Codigo_VC = $oventacod[$indice];

                if ($tipo_oper == 'V')
                    $filter->CPDEC_Costo = $prodcosto[$indice];
                    $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
                	$filter->CPDEC_GenInd = $flagGenInd[$indice];
                	$filter->CPDEC_Observacion = $obs_detalle[$indice];
                    $filter->CPDEC_Pu = $prodpu[$indice];
                    $filter->CPDEC_Subtotal = $prodprecio[$indice];

                if ($detaccion[$indice] != 'e' && $detaccion[$indice] != 'EE') {
                    
                    /**SISTEMA DE COCHERA**/
                    $idproducto = $prodcodigo[$indice];
                    
                    //Recuperamos el producto
                    $datos_producto = $this->producto_model->getProducto($idproducto);
                    $familiaid      = $datos_producto[0]->FAMI_Codigo;
                    
                    if($familiaid == 77){
                        /** Cambiamos el estado del producto**/
                        $filtroprod = new stdClass();
                        $filtroprod->PROD_FlagSituacion = 2;
                        $this->producto_model->modificarRegistro($idproducto,$filtroprod);                        
                    }
                    
                    /** Cambiamos el estado al parqueo*/
                    $filtroparqueo = new stdClass();
                    $filtroparqueo->servicio = $idproducto;
                    $datos_parqueo = $this->parqueo_model->getParqueos($filtroparqueo);
                    if($datos_parqueo != NULL){
                        $idparqueo     = $datos_parqueo[0]->PARQP_Codigo;   
                        $filtroparqueo = new stdClass();
                        $filtroparqueo->PARQC_FlagSituacion = 3;
                        $filtroparqueo->CPP_Codigo          = $comprobante;
                        $this->parqueo_model->actualizar_parqueo($idparqueo,$filtroparqueo);
                    }
                    
                    /**Insertamos detalle**/
                    $this->comprobantedetalle_model->insertar($filter);
				
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
                                                        $json = array("result" => false, "msj" => "ya ha sido ingresado por otro usuario esta serie $serieNumero en el producto $proddescri[$indice]");
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

                 $codoventa= $oventacod[$indice];
                     if($accion[$indice] != "e" && $accion[$indice] != "EE" && !is_null($codoventa) && $codoventa != ''){
                            $this->ocompra_model->modificar_pendientecomprobante($codoventa,$filter->CPDEC_Cantidad);
                        }
            }
        }

        $json = array( "result" => true, "codigo" => $comprobante );
        echo json_encode($json);
    }

    public function insertar_guiarem($comprobante){

        $comprobanteInfo = $this->comprobante_model->obtener_comprobante($comprobante);
        $compania = $comprobanteInfo[0]->COMPP_Codigo;
        $this->load->model("maestros/compania_model");

        #####################################
        ####### DATOS DE LA EMPRESA EMISORA
        #####################################
            $companiaInfo = $this->compania_model->obtener($compania);
            $establecimientoInfo = $this->emprestablecimiento_model->listar( $companiaInfo[0]->EMPRP_Codigo, '', $companiaInfo[0]->COMPP_Codigo );
            $empresaInfo =  $this->empresa_model->obtener_datosEmpresa( $establecimientoInfo[0]->EMPRP_Codigo );

            $ubigeo_origen = $establecimientoInfo[0]->UBIGP_Codigo;
            $direccion_origen = $establecimientoInfo[0]->EESTAC_Direccion;
            
            $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, 10);
            $serie = ($configuracion_datos[0]->CONFIC_Serie == NULL || $configuracion_datos[0]->CONFIC_Serie == "") ? 1 : $configuracion_datos[0]->CONFIC_Serie;
            $numero = $this->lib_props->getOrderNumeroSerie($configuracion_datos[0]->CONFIC_Numero + 1);
            #####################################

            $tipo_oper = $comprobanteInfo[0]->CPC_TipoOperacion;
            $serieOC = $comprobanteInfo[0]->OCOMC_Serie;
            $numeroOC = $comprobanteInfo[0]->OCOMC_Numero;
            $cliente = $comprobanteInfo[0]->CLIP_Codigo;
            $proveedor = $comprobanteInfo[0]->PROVP_Codigo;
            $moneda = $comprobanteInfo[0]->MONED_Codigo;
            $fecha = $comprobanteInfo[0]->CPC_Fecha;
            $almacen = $comprobanteInfo[0]->ALMAP_Codigo;
            $observacion = $comprobanteInfo[0]->CPC_Observacion;
            $OCcliente = $comprobanteInfo[0]->CPP_Compracliente; # NUMERO DE ORDEN DE COMPRA CLIENTE
            $obra = $comprobanteInfo[0]->PROYP_Codigo;
            $tipo_movimiento = 1; # Por defecto es venta
            $otro_motivo = NULL;
        
        #####################################
        ####### TOTALES
        #####################################
            $igv = $comprobanteInfo[0]->CPC_igv;
            $igv100 = $comprobanteInfo[0]->CPC_igv100;
            $descuento = $comprobanteInfo[0]->CPC_descuento;
            $descuento100 = $comprobanteInfo[0]->CPC_descuento100;
            $subtotal = $comprobanteInfo[0]->CPC_subtotal;
            $total = $comprobanteInfo[0]->CPC_total;

            $filter = new stdClass();
            $filter->GUIAREMC_TipoOperacion = $tipo_oper;
            $filter->GUIAREMC_Serie = $serie;
            $filter->GUIAREMC_Numero = $numero;
            $filter->MONED_Codigo = $moneda;

            $filter->PROYP_Codigo = $obra;
            $filter->OCOMP_Codigo = $comprobanteInfo[0]->OCOMP_Codigo;
            $filter->PRESUP_Codigo = NULL;

            $filter->TIPOMOVP_Codigo = $tipo_movimiento;
            $filter->GUIAREMC_OtroMotivo = $otro_motivo;
            $filter->ALMAP_Codigo = $almacen;
            
            $filter->GUIAREMC_CodigoUsuario = NULL;
            $filter->USUA_Codigo = $this->usuario;

            $filter->COMPP_Codigo = $compania;
            $filter->DOCUP_Codigo = $OCcliente; # OC cliente

            $filter->GUIAREMC_PersReceNombre = "-";
            $filter->GUIAREMC_PersReceDNI = "-";
            $filter->GUIAREMC_NumeroRef = "";
            $filter->GUIAREMC_OCompra = "$serieOC-$numeroOC";
            $filter->GUIAREMC_FechaTraslado = $fecha;
            
            $filter->GUIAREMC_Fecha = $fecha;
            $filter->EMPRP_Codigo = NULL;

        ##########################################
        ########## CLIENTE Y DIRECCIONES
        ##########################################
            if ($tipo_oper == 'V'){
                $filter->CLIP_Codigo = $cliente;
                $datos_cliente = $this->cliente_model->obtener($cliente);

                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $ruc_cliente = ( $ruc_cliente == "" ) ? $dni_cliente : $ruc_cliente ;
                $email   = $datos_cliente->correo;

                #$direccion_destino = $datos_cliente->direccion;
                #$ubigeo_destino = $datos_cliente->ubigeo;
            }
            else{
                $filter->PROVP_Codigo = $proveedor;
                $datos_proveedor = $this->proveedor_model->obtener($proveedor);

                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;
                #$direccion_destino = $datos_proveedor->direccion;
                #$ubigeo_destino = $datos_proveedor->ubigeo_codigo;
            }

            
            $filter->GUIAREMC_UbigeoPartida = $ubigeo_origen;
            $filter->GUIAREMC_PuntoPartida = strtoupper($direccion_origen);
            $filter->GUIAREMC_UbigeoLlegada = "";
            $filter->GUIAREMC_PuntoLlegada = strtoupper( $comprobanteInfo[0]->CPC_Direccion );

        ##########################################
        ########## TRANSPORTE
        ##########################################
            $filter->EMPRP_Codigo = 1;
            $filter->GUIAREMC_Marca = "-";
            $filter->GUIAREMC_Placa = "-";
            $filter->GUIAREMC_RegistroMTC = "";
            $filter->GUIAREMC_Certificado = "";
            $filter->GUIAREMC_Licencia = "";
            $filter->GUIAREMC_PersReceDNI = "00000000";
            $filter->GUIAREMC_NombreConductor = "-";
        ##########################################
        
        $filter->GUIAREMC_Observacion = strtoupper($observacion);
        $filter->GUIAREMC_descuento100 = $descuento100;
        $filter->GUIAREMC_igv100 = $igv100;
        $filter->GUIAREMC_subtotal = $subtotal;
        $filter->GUIAREMC_descuento = $descuento;
        $filter->GUIAREMC_igv = $igv;
        $filter->GUIAREMC_total = $total;
        $filter->GUIAREMC_FlagEstado = "2";
        $guiarem_id = $this->guiarem_model->insertar($filter);
        
        if ($tipo_oper == 'V')
            $this->configuracion_model->modificar_configuracion($compania, 10, $numero); # ACTUALIZA EL CORRELATIVO DE GUIAS

        ##########################################################################
        ####### ASOCIAMOS AL COMPROBANTE, LA GUIA DE REMISION GENERADA
        ##########################################################################
            if ($guiarem_id != NULL){
                $filterCG = new stdClass();
                $filterCG->CPP_Codigo = $comprobante;
                $filterCG->GUIAREMP_Codigo = $guiarem_id;
                $filterCG->COMPGUI_FlagEstado = 1;
                $filterCG->COMPGU_FechaRegistro = date("Y-m-d H:i:s");
                $this->comprobante_model->insertar_comprobante_guiarem($filterCG);
            }

        ##########################################################################
        ########### ARTICULOS
        ##########################################################################

        $detalle = $this->comprobantedetalle_model->detalles($comprobante);
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detocompra = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $descripcion = $valor->CPDEC_Descripcion;
                $observacionDet = $valor->CPDEC_Observacion;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $detflag = $valor->CPDEC_GenInd;
                $cantidad = $valor->CPDEC_Cantidad;
                $costo = $valor->CPDEC_Costo;
                $pu = $valor->CPDEC_Pu;
                $subtotal = $valor->CPDEC_Subtotal;
                $descuento = $valor->CPDEC_Descuento;
                $descuento100 = $valor->CPDEC_Descuento100;
                $igv = $valor->CPDEC_Igv;
                $igv100 = $valor->CPDEC_Igv100;
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                $total = $valor->CPDEC_Total;
            
                    $filterGuia = new stdClass();
                    $filterGuia->GUIAREMP_Codigo = $guiarem_id;
                    $filterGuia->PRODCTOP_Codigo = $producto;
                    $filterGuia->UNDMED_Codigo = $unidad;
                    $filterGuia->LOTP_Codigo = $lote;
                    $filterGuia->AFECT_Codigo = $tafectacion;
                    $filterGuia->GUIAREMDETC_Cantidad = $cantidad;
                    $filterGuia->GUIAREMDETC_Pu = $pu;
                    $filterGuia->GUIAREMDETC_Subtotal = $subtotal;
                    $filterGuia->GUIAREMDETC_Descuento = $descuento;
                    $filterGuia->GUIAREMDETC_Igv = $igv;
                    $filterGuia->GUIAREMDETC_Total = $total;
                    $filterGuia->GUIAREMDETC_Pu_ConIgv = $pu_conigv;
                    $filterGuia->GUIAREMDETC_Descuento100 = $descuento100;
                    $filterGuia->GUIAREMDETC_Igv100 = $igv100;
                    $filterGuia->GUIAREMDETC_Costo = $costo;
                    ###########################################
                    $filterGuia->GUIAREMDETC_Venta = NULL;
                    $filterGuia->GUIAREMDETC_ITEM = $indice + 1;
                    $filterGuia->GUIAREMDETC_Peso = 0;
                    ###########################################
                    $filterGuia->GUIAREMDETC_GenInd = $detflag;
                    $filterGuia->GUIAREMDETC_Descripcion = $descripcion;
                    $filterGuia->GUIAREMDETC_Observacion = $observacionDet;
                    $filterGuia->ALMAP_Codigo = $almacen;
                    $this->guiaremdetalle_model->insertar($filterGuia);
            }
        }
        
        $success = array(
                            "result" => "success",
                            "pdf" => "<a href='".base_url()."index.php/almacen/guiarem/guiarem_ver_pdf/$guiarem_id/a4/1' data-fancybox data-type='iframe'> <span>$serie-$numero</span> </a>"
                        );

        echo json_encode($success);
    }
    
    public function ConsultarComprobanteNubefact( $codigo ){
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = ltrim($datos_comprobante[0]->CPC_Numero, "0");
            
        $compania = $this->compania;               
        $deftoken = $this->tokens->deftoken("$compania");

        $ruta = $deftoken['ruta'];
        $token = $deftoken['token'];
        
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento; 

        switch ($tipo_docu) {
            case 'F':
            $tipo_de_comprobante = '1';
            break;
            case 'B':
            $tipo_de_comprobante = '2';
            break;
            case 'N':
            $tipo_de_comprobante = '3';
            break;

            default:
            $tipo_de_comprobante = '1';
            break;
        } 
        
        $data2 = array(
            "operacion"				=> "consultar_comprobante",
            "tipo_de_comprobante"   => "${tipo_de_comprobante}",
            "serie"                 => "${serie}",
            "numero"                => "${numero}"
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
        curl_close($ch);     
        $filter2 = new stdClass();
        $exito = false;

        /*
        {
             "tipo_de_comprobante": 1,
             "serie": "FFF1",
             "numero": 1,
             "enlace": "https://www.nubefact.com/cpe/d268f882-4554-a403c6712e6",
             "enlace_del_pdf": "",
             "enlace_del_xml": "",
             "enlace_del_cdr": "",
             "aceptada_por_sunat": true,
             "sunat_description": "La Factura numero FFF1-1, ha sido aceptada",
             "sunat_note": null,
             "sunat_responsecode": "0",
             "sunat_soap_error": "",
             "cadena_para_codigo_qr": "20600695771 | 01 | FFF1 | 000001 | ...",
             "codigo_hash": "xMLFMnbgp1/bHEy572RKRTE9hPY="
            }
        */
        if ( !isset($respuesta2->errors) && $respuesta2->enlace_del_pdf != NULL ){
            $filter2->CPP_codigo = $codigo; 
            $filter2->respuestas_compañia = $this->compania;
            $filter2->respuestas_tipoDocumento = $respuesta2->tipo_de_comprobante;
            $filter2->respuestas_serie = $respuesta2->serie;    
            $filter2->respuestas_numero = $respuesta2->numero;
            $filter2->respuestas_enlace = $respuesta2->enlace;
            $filter2->respuestas_aceptadaporsunat = $respuesta2->aceptada_por_sunat;
            $filter2->respuestas_sunatdescription = $respuesta2->sunat_description;
            $filter2->respuestas_sunatnote = $respuesta2->sunat_note;
            $filter2->respuestas_sunatresponsecode = $respuesta2->sunat_responsecode;
            $filter2->respuestas_sunatsoaperror = $respuesta2->sunat_soap_error;
            $filter2->respuestas_cadenaparacodigoqr = $respuesta2->cadena_para_codigo_qr;
            $filter2->respuestas_codigohash = $respuesta2->codigo_hash;
            $filter2->respuestas_enlacepdf = $respuesta2->enlace_del_pdf;
            $filter2->respuestas_enlacexml = $respuesta2->enlace_del_xml;
            $filter2->respuestas_enlacecdr = $respuesta2->enlace_del_cdr;
            $filter2->respuestas_pdfzipbase64 = $respuesta2->pdf_zip_base64;
            $filter2->respuestas_xmlzipbase64 = $respuesta2->xml_zip_base64;
            $filter2->respuestas_cdrzipbase64 = $respuesta2->cdr_zip_base64;

            $exito = true;
            $this->comprobante_model->insertar_respuestaSunat($filter2);
        }
        else{
            $exito = false;
        }
        
        return $exito;
    }

    public function ConsultarAnulacionNubefact($codigo = 0){
        if($codigo > 0) {            
            $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
            $numero = $datos_comprobante[0]->CPC_Numero;
                
            $compania=$this->compania;
               
            $deftoken= $this->tokens->deftoken("$compania");
            $ruta = $deftoken['ruta'];
            $token = $deftoken['token'];
            
            $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;

            switch ($tipo_docu) {
                case 'F':
                $tipo_de_comprobante = '1';
                break;
                case 'B':
                $tipo_de_comprobante = '2';
                break;
                case 'N':
                $tipo_de_comprobante = '3';
                break;

                default:
                $tipo_de_comprobante = '1';
                break;
            }

            //serie
            $serieFac = $serie;
            $numero2 = trim($numero);
            
            $data2 = array(
                "operacion"		=> "consultar_anulacion",
                "tipo_de_comprobante"   => "${tipo_de_comprobante}",
                "serie"                 => "${serieFac}",
                "numero"                => "${numero2}"
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
            curl_close($ch);     
            
            $filter2  = new stdClass();                             

            /*
                {
                     "numero": 1,
                     "enlace": "https://www.nubefact.com/anulacion/b7fc0c001-b31a",
                     "sunat_ticket_numero": "1494358661332",
                     "aceptada_por_sunat": false,
                     "sunat_description": null,
                     "sunat_note": null,
                     "sunat_responsecode": null,
                     "sunat_soap_error": "",
                     "enlace_del_pdf": "https://www.nubefact.com/anulacion/b7fc0c001-b31a.pdf",
                     "enlace_del_xml": "https://www.nubefact.com/anulacion/b7fc0c001-b31a.xml",
                     "enlace_del_cdr": "https://www.nubefact.com/anulacion/b7fc0c001-b31a.cdr"
                }
            */

            $filter2->CPP_codigo = $codigo;
            $filter2->respuestas_compañia = $this->compania;
            $filter2->respuestas_tipoDocumento = $respuesta2->tipo_de_comprobante;
            $filter2->respuestas_serie = $serieFac;    
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

            $this->comprobante_model->insertar_respuestaSunat($filter2);
        }
    }

    public function asignar_lotes($codigo){

        $detalle_comprobante = $this->comprobantedetalle_model->detalles($codigo);

        $success = array( "result" => "success", "msg" => "");

        if (count($detalle_comprobante) > 0) {
            foreach ($detalle_comprobante as $indice => $valor) {
                $idRegistro = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $almacen = $valor->ALMAP_Codigo;
                $afectacion = $valor->AFECT_Codigo;

                $vu = $valor->CPDEC_Pu;
                $pu = $valor->CPDEC_Pu_ConIgv;

                $cantidad = $valor->CPDEC_Cantidad;
                $stockInfo = $this->almacenproducto_model->obtener($almacen, $producto); # CONSULTO EL STOCK ACTUAL

                if ($stockInfo != NULL){
                    foreach ($stockInfo as $keyAlmacen => $valueAlmacen) {
                        $stockGeneral = $valueAlmacen->ALMPROD_Stock;
                        $idAlmacenProducto = $valueAlmacen->ALMPROD_Codigo;

                        if ($valor->LOTP_Codigo == "0"){
                            # CONSULTO LA CANTIDAD DISPONIBLE - "PUEDEN HABER 10 DEL STOCK ACTUAL, PERO SOLO 2 DISPONIBLES"
                            $stockDisponible = $this->almaprolote_model->stockDisponible($idAlmacenProducto);

                            if ($stockGeneral != NULL && $stockGeneral >= $cantidad && $stockDisponible >= $cantidad){
                                $lotesInfo = $this->almaprolote_model->listar($producto, $idAlmacenProducto); # CONSULTO EL STOCK DE LOS LOTES

                                foreach ($lotesInfo as $keyLote => $valLote) {
                                    $stockLote = $valLote->ALMALOTC_CantidadDisponible; # SELECCIONAMOS LA CANTIDAD NO ASIGNADA DEL LOTE

                                    if ($stockLote >= $cantidad){ # SI LA CANTIDAD ES CUBIERTA POR EL LOTE, SE ASIGNA EL ID LOTE AL COMPROBANTE
                                        $filterCPDEC = new stdClass();
                                        $filterCPDEC->LOTP_Codigo = $valLote->LOTP_Codigo;
                                        $this->comprobantedetalle_model->modificar($idRegistro, $filterCPDEC);

                                        $stockLoteResta = $stockLote - $cantidad;
                                        $filterAPL = new stdClass();
                                        $idLoteAPL = $valLote->ALMALOTP_Codigo;
                                        $filterAPL->ALMALOTC_CantidadDisponible = $stockLoteResta; # ACTUALIZAMOS LA CANTIDAD NO ASIGNADA DEL LOTE
                                        
                                        if ($stockLoteResta == 0){ # SI TODO EL LOTE ESTA ASIGNADO, CAMBIAMOS EL FLAG PARA NO LISTARLO DURANTE LA SIGUIENTE ASIGNACION
                                            $filterAPL->ALMALOTC_FlagEstado = 0;
                                        }

                                        $this->almaprolote_model->modificar($idLoteAPL, $filterAPL);

                                        break; # SALGO DEL FOREACH lotesInfo
                                    }
                                    else{ # SI LA CANTIDAD NO ES CUBIERTA POR EL LOTE SE DEBE DIVIDIR ENTRE LA CANTIDAD DE LOTES QUE CUBRAN DICHA CANTIDAD
                                        $this->repartir_lote($idRegistro);
                                        break; # SALGO DEL FOREACH lotesInfo
                                    }
                                }
                            }
                            else{
                                $success["result"] = "warning";
                                $success["msg"] .= "\nEl stock del articulo: $valor->CPDEC_Descripcion es insuficiente. Stock actual: $stockGeneral, stock disponible: $stockDisponible";
                                break;
                            }
                        }
                    }
                }
                else{
                    $success["result"] = "warning";
                    $success["msg"] .= "\nEl stock del articulo: $valor->CPDEC_Descripcion es insuficiente.";
                }
            }

            if ( $success["result"] == "success" ){
                $success["msg"] = "Lotes asignados.";
            }
        }
        else{
                $success["result"] = "error";
                $success["msg"] = "Los detalles del comprobante no estan disponibles.";
        }

        echo json_encode($success);
    }

    public function repartir_lote($idRegistro){
        $detalles = $this->comprobantedetalle_model->obtener($idRegistro);

        if (count($detalles) > 0) {
            foreach ($detalles as $indice => $valor) {
                $idRegistro = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $almacen = $valor->ALMAP_Codigo;
                $afectacion = $valor->AFECT_Codigo;

                $vu = $valor->CPDEC_Pu;
                $pu = $valor->CPDEC_Pu_ConIgv;

                $cantidad = $valor->CPDEC_Cantidad;
                $stockInfo = $this->almacenproducto_model->obtener($almacen, $producto); # CONSULTO EL STOCK ACTUAL
                $update = false;

                foreach ($stockInfo as $keyAlmacen => $valueAlmacen) {
                    $stockGeneral = $valueAlmacen->ALMPROD_Stock;
                    $idAlmacenProducto = $valueAlmacen->ALMPROD_Codigo;

                    $lotesInfo = $this->almaprolote_model->listar($producto, $idAlmacenProducto); # CONSULTO EL STOCK DE LOS LOTES

                    foreach ($lotesInfo as $keyLote => $valLote) {
                        $stockLote = $valLote->ALMALOTC_CantidadDisponible; # SELECCIONAMOS LA CANTIDAD NO ASIGNADA DEL LOTE
                        
                        if ($cantidad < 0 )
                            $cantidad = 0;

                        $stockLoteResta = $stockLote - $cantidad;

                        if ($update == false){
                            # MODIFICAMOS EL REGISTRO ACTUAL
                            $filterCPDEC = new stdClass();
                            $filterCPDEC->CPDEC_Cantidad = $stockLote;
                            $filterCPDEC->CPDEC_Pu = $vu;
                            $filterCPDEC->CPDEC_Pu_ConIgv = $pu;
                            $filterCPDEC->CPDEC_Subtotal = ($vu * $stockLote);
                            $filterCPDEC->CPDEC_Total = ($pu * $stockLote);
                            $filterCPDEC->CPDEC_Igv = ($pu * $stockLote) - ($vu * $stockLote);

                            $filterCPDEC->LOTP_Codigo = $valLote->LOTP_Codigo;
                            $this->comprobantedetalle_model->modificar($idRegistro, $filterCPDEC);

                            $update = true;
                        }
                        else{
                            if ($cantidad > 0){ # SI CANTIDAD ES MAYOR QUE 0 AUN QUEDAN ARTICULOS POR REPARTIR
                                if ( $cantidad >= $stockLote )
                                    $nvaCantidad = $stockLote;
                                else
                                    $nvaCantidad = $cantidad;

                                    # INSERTAMOS UN REGISTRO NUEVO
                                    $filterDet = new stdClass();
                                    $filterDet->CPP_Codigo          = $valor->CPP_Codigo;
                                    $filterDet->PROD_Codigo         = $valor->PROD_Codigo;
                                    $filterDet->UNDMED_Codigo       = $valor->UNDMED_Codigo;
                                    $filterDet->LOTP_Codigo         = $valLote->LOTP_Codigo;
                                    $filterDet->AFECT_Codigo        = $valor->AFECT_Codigo;
                                    $filterDet->CPDEC_Cantidad      = $nvaCantidad;
                                    $filterDet->CPDEC_Pu            = $valor->CPDEC_Pu;
                                    $filterDet->CPDEC_Subtotal      = $valor->CPDEC_Pu * $nvaCantidad; #$valor->CPDEC_Subtotal;
                                    $filterDet->CPDEC_Descuento     = $valor->CPDEC_Descuento;
                                    $filterDet->CPDEC_Igv           = ( $valor->CPDEC_Pu_ConIgv * $nvaCantidad ) - ($valor->CPDEC_Pu * $nvaCantidad); #$valor->CPDEC_Igv; TOTAL - SUBTOTAL
                                    $filterDet->CPDEC_Total         = $valor->CPDEC_Pu_ConIgv * $nvaCantidad; #$valor->CPDEC_Total;
                                    $filterDet->CPDEC_Pu_ConIgv     = $valor->CPDEC_Pu_ConIgv;
                                    $filterDet->CPDEC_Descuento100  = $valor->CPDEC_Descuento100;
                                    $filterDet->CPDEC_Igv100        = $valor->CPDEC_Igv100;
                                    $filterDet->ALMAP_Codigo        = $valor->ALMAP_Codigo;
                                    $filterDet->CPDEC_Costo         = $valor->CPDEC_Costo;
                                    $filterDet->CPDEC_GenInd        = $valor->CPDEC_GenInd;
                                    $filterDet->CPDEC_Descripcion   = $valor->CPDEC_Descripcion;
                                    $filterDet->CPDEC_Observacion   = $valor->CPDEC_Observacion;
                                    $this->comprobantedetalle_model->insertar($filterDet);
                            }
                        }

                        # MODIFICO LA CANTIDAD DISPONIBLE DEL LOTE
                        $filterAPL = new stdClass();
                        $idLoteAPL = $valLote->ALMALOTP_Codigo;
                        $filterAPL->ALMALOTC_CantidadDisponible = $stockLoteResta;
                            
                        if ($stockLoteResta <= 0){
                            $filterAPL->ALMALOTC_CantidadDisponible = 0;
                            $filterAPL->ALMALOTC_FlagEstado = 0;
                        }
                        $this->almaprolote_model->modificar($idLoteAPL, $filterAPL);

                        $cantidad -= $stockLote; # LA NUEVA CANTIDAD ES LO QUE RESTA POR ASIGNAR
                        if ($cantidad <= 0 ){ # SI LA NUEVA CANTIDAD = 0, YA SE HAN REPARTIDO Y ASIGNADO TODOS LOS LOTES -> SALGO DEL FOREACH
                            break;
                        }
                    }
                }
            }
        }
    }

    public function verificar_lotes_asignados($codigo){

        $detalle_comprobante = $this->comprobantedetalle_model->detalles($codigo);

        if (count($detalle_comprobante) > 0) {
            foreach ($detalle_comprobante as $indice => $valor) {
                $idRegistro = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $almacen = $valor->ALMAP_Codigo;

                $cantidad = $valor->CPDEC_Cantidad;
                #$stockInfo = $this->almacenproducto_model->obtener($almacen, $producto); # CONSULTO EL STOCK ACTUAL

                if ($valor->LOTP_Codigo == "0"){
                    return false; # exit('{"result":"error","msg":"El stock del articulo: '.$valor->CPDEC_Descripcion.' es insuficiente."}');
                }
            }
            return true; # exit('{"result":"success","msg":"Lotes asignados. Recargue la página.", "redirect":""}');
        }
        else{
            return false; # exit('{"result":"error","msg":"El detalle del comprobante no esta disponible."}');
        }
    }

    public function confirmInventariado($codigo){

        $detalle_comprobante = $this->comprobantedetalle_model->detalles($codigo);

        if (count($detalle_comprobante) > 0) {
            foreach ($detalle_comprobante as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $almacen = $valor->ALMAP_Codigo;
                $this->inventario_model->confirmInventariado($producto, $almacen);
            }
        }
    }

    public function disparador($tipo_oper = 'V', $codigo, $tipo_docu = 'F'){

        $aceptada = false;
        $lotesAsignados = true; # $this->verificar_lotes_asignados($codigo);

        if ($lotesAsignados == true){

            $comprobanteInfo = $this->comprobante_model->obtener_comprobante($codigo);
            $comprobante = $comprobanteInfo[0];

            if ($comprobante->CPC_TipoDocumento == "F" || $comprobante->CPC_TipoDocumento == "B")
                $aceptada = $this->envioSunatFac($codigo, $comprobante);
                
            if ($aceptada == true || $comprobante->CPC_TipoDocumento == 'N'){
                $this->confirmInventariado($codigo);

                //Inicio transaccion                
                $this->db->trans_start();
                $query = $this->db->query("CALL COMPROBANTE_DISPARADOR($codigo)");
                if($this->db->trans_status() == false){
                    $this->db->trans_rollback();
                    $success = array( "result" => "error", "msg" => "Intente nuevamente! De persistir el inconveniente, contacte al administrador." );
                    
                }else{
                    $this->db->trans_commit();                      
                    $success = array( "result" => "success" );
                
                    # SI UNA CAJA ESTA ASOCIADA AL DOCUMENTO, REGISTRA EL MOVIMIENTO EN LA CAJA
                    
                    if ($comprobante->CAJA_Codigo != "" && $comprobante->CAJA_Codigo != NULL && $comprobante->FORPAP_Codigo != NULL){
                        $cuenta = $this->cuentas_model->getCuentaComprobante($codigo);
                        $justification = ($comprobante->CPC_TipoOperacion == "V") ? "INGRESO POR VENTA " : "EGRESO POR COMPRA ";
                        $justification = $justification . $comprobante->CPC_Serie . " - " . 
                                $this->lib_props->getOrderNumeroSerie($comprobante->CPC_Numero);
                        
                        $filter = new stdClass();
                        $filter->CAJAMOV_Codigo = ""; # "" PARA INGRESAR UN REGISTRO NUEVO
                        $filter->CAJA_Codigo    = $comprobante->CAJA_Codigo;
                        $filter->PAGP_Codigo    = NULL;
                        $filter->RESPMOV_Codigo = NULL;
                        $filter->CUENT_Codigo   = $cuenta[0]->CUE_Codigo;
                        $filter->MONED_Codigo   = $comprobante->MONED_Codigo;
                        $filter->CAJAMOV_Monto  = $comprobante->CPC_total;
                        $filter->CAJAMOV_MovDinero = ($comprobante->CPC_TipoOperacion == 'V') ? 1 : 2; # (V:1) = INGRESO | (C:2) = EGRESO
                        $filter->FORPAP_Codigo  = $comprobante->FORPAP_Codigo;
                        $filter->CAJAMOV_FechaRecep    = $comprobante->CPC_Fecha;
                        $filter->CAJAMOV_Justificacion = $justification;
                        $filter->CAJAMOV_Observacion   = $comprobante->CPC_Observacion;
                        $filter->CAJAMOV_FlagEstado    = "1";
                        $filter->CAJAMOV_CodigoUsuario = $this->usuario;
                        $filter->CPP_Codigo            = $codigo;
                        $result = $this->movimientos->guardar_movimiento($filter);
                        //echo $result;
                        //die;
                    }
                }
            }
            else{
                $existe = $this->comprobante_model->consultar_respuestaSunat($codigo);
                if ( $existe->respuestas_enlacepdf != NULL && $pdfRespSunat->respuestas_enlacepdf != '' ){
                    $success = array( "result" => "success" );
                }
                else{
                    $error = $this->comprobante_model->lsResSunat($codigo);

                    $msg = "";

                    if ( $error->respuestas_deta != "" )
                        $msg = $error->respuestas_deta;

                    $success = array( "result" => "error", "msg" => $msg );
                }
            }
        }
        else{
            $success = array( "result" => "error", "msg" => "Existen articulos sin lote" );
        }
        echo json_encode($success);
    }

    public function envioSunatFac($codigo, $datos_comprobante){
        
        $tipo_oper = $datos_comprobante->CPC_TipoOperacion;
        $tipo_docu = $datos_comprobante->CPC_TipoDocumento;
        $compania = $datos_comprobante->COMPP_Codigo;

        if ($compania == NULL || $compania == ""){ # SI EL COMPROBANTE NO TIENE COMPAÑIA ASIGNADA TERMINA LA EJECUCION DEL SCRIPT
            $success = array( "result" => "error", "msg" => "El comprobante no tiene compañia asignada. Actualice los datos e intentelo nuevamente.");
            echo json_encode($success);
            exit();
        }

        $deftoken = $this->tokens->deftoken($compania);
        $ruta = $deftoken['ruta'];
        $token = $deftoken['token'];
        
        $presupuesto = $datos_comprobante->PRESUP_Codigo;
        $ordencompra = $datos_comprobante->OCOMP_Codigo;
        $guiainp_codigo = $datos_comprobante->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante->GUIASAP_Codigo;
        $guiaremision = $datos_comprobante->GUIAREMP_Codigo;
        $serie = $datos_comprobante->CPC_Serie;
        $numero = $datos_comprobante->CPC_Numero;
        $cliente = $datos_comprobante->CLIP_Codigo;
        $proveedor = $datos_comprobante->PROVP_Codigo;
        $forma_pago = $datos_comprobante->FORPAP_Codigo;
        $sunat_transaction = $datos_comprobante->CPC_Tipo_venta;
        
        $moneda = $datos_comprobante->MONED_Codigo;
        $fecha_emision = $datos_comprobante->CPC_Fecha;
        $fecha_vencimiento = ($datos_comprobante->CPC_FechaVencimiento == NULL) ? $fecha_emision : $datos_comprobante->CPC_FechaVencimiento;

        $subtotal = $datos_comprobante->CPC_subtotal;
        $descuento = $datos_comprobante->CPC_descuento;
        $igv = $datos_comprobante->CPC_igv;
        $total = $datos_comprobante->CPC_total;
        $subtotal_conigv = $datos_comprobante->CPC_subtotal_conigv;
        $descuento_conigv = $datos_comprobante->CPC_descuento_conigv;
        $igv100 = $datos_comprobante->CPC_igv100;
        $descuento100 = $datos_comprobante->CPC_descuento100;
        //guiarem_codigo y docurefe_codigo VACIOS
        $guiarem_codigo = $datos_comprobante->CPC_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante->CPC_DocuRefeCodigo;
        $compraCliente =$datos_comprobante->CPP_Compracliente;
        $observacion = $datos_comprobante->CPC_Observacion;
        //$vendedor = $datos_comprobante->CPC_Vendedor;

        $flagMueveStock = $datos_comprobante->CPC_FlagMueveStock;
        $flagEstado = $datos_comprobante->CPC_FlagEstado;

        //tipo de cambio vacio verificar
        $tdc = $datos_comprobante->CPC_TDC;

        $ordenCompra = $datos_comprobante->CPC_ordenCompra;
 
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $direccion ='';
        $email = '';

        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $ruc_cliente = ($ruc_cliente == "" || $ruc_cliente == 0) ? $dni_cliente : $ruc_cliente;
                $email   = $datos_cliente->correo;
                $tipoDocIdentidad = $datos_cliente->tipoDocIdentidad;
                if ($datos_comprobante->CPC_Direccion != "")
                    $direccion = $datos_comprobante->CPC_Direccion;
                else
                    $direccion = $datos_cliente->direccion;
            }
        } else if ($proveedor != '' && $proveedor != '0') {
            $datos_proveedor = $this->proveedor_model->obtener($proveedor);
            if ($datos_proveedor) {
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor = $datos_proveedor->ruc;

                if ($datos_comprobante->CPC_Direccion != "")
                    $direccion = $datos_comprobante->CPC_Direccion;
                else
                    $direccion   = $datos_proveedor->direccion;
            }
        }
        
        switch ($tipo_docu) {
            case 'F':
                $tipo_de_comprobante = 1;
                break;
            case 'B':
                $tipo_de_comprobante = 2;
                break;
            case 'N':
                $tipo_de_comprobante = 7;
                break;
        }

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);
        $items=array();
        $guias=array();
            
            $gravada = 0;
            $inafecto = 0;
            $exonerado = 0;
            $gratuito = 0;
            $tdescuento = 0;
            $totalBolsa = 0;

            if (count($detalle_comprobante) > 0) {
                foreach ($detalle_comprobante as $indice => $valor) {
                    $unidad_medida = $valor->UNDMED_Codigo;
                    $tipo_afectacion = $valor->AFECT_Codigo;
                    $codigo_usuario = $valor->PROD_CodigoUsuario;
                    $codigo_original = ($valor->PROD_CodigoOriginal != "" AND $valor->PROD_CodigoOriginal > 0) ? $valor->PROD_CodigoOriginal : ""; # ESTE ES EL CODIGO PRODUCTO SUNAT

                    $nombre_producto = $valor->PROD_Nombre." ".$valor->CPDEC_Observacion;                    
                    $prodcantidad = $valor->CPDEC_Cantidad;

                    $precio_con_igv = $valor->CPDEC_Pu_ConIgv;
                    $precio_sin_igv = $valor->CPDEC_Pu; #( $tipo_afectacion == 1 ) ? round( $precio_con_igv / (1.18), 5) : $precio_con_igv;

                    $igvxprod = $valor->CPDEC_Igv;
                    $proddescuento = $valor->CPDEC_Descuento;

                    $prodsubtotal = $valor->CPDEC_Subtotal;
                    $prodtotal = $valor->CPDEC_Total;

                    $medidaDetalle = $valor->UNDMED_Simbolo;

                    $afectacionInfo = $this->producto_model->tipo_afectacion($tipo_afectacion);

                    switch ($tipo_afectacion) {
                        case 1: # GRAVADA
                            $gravada += $prodsubtotal;
                            break;
                        case 8: # EXONERADO
                            $exonerado += $prodsubtotal;
                            $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; #" [EXONERADA]";
                            break;
                        case 9: # INAFECTO
                            $inafecto += $prodsubtotal;
                            $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; #" [INAFECTA]";
                            break;
                        case 16: # EXPORTACION SE GUARDA COMO INAFECTO
                            $inafecto += $prodsubtotal;
                            $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; #" [INAFECTA]";
                            break;
                        default:
                            $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; #" [GRATUITA]";
                            $gratuito += $prodsubtotal;
                            break;
                    }

                    $totalBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $valor->CPDEC_Total + $totalBolsa : $totalBolsa; # SI TIENE BOLSA SUMA
                    $importeBolsa = ( $valor->CPDEC_ITEMS == "1" ) ? $valor->CPDEC_Total : 0; # SI TIENE BOLSA SUMA

                    #$nombre_producto = ($valor->MARCC_CodigoUsuario != '') ? "$nombre_producto. LAB: $valor->MARCC_CodigoUsuario" : $nombre_producto;
                    #$nombre_producto = ($valor->LOTC_Numero != '') ? "$nombre_producto. LOTE: $valor->LOTC_Numero" : $nombre_producto;
                    #$nombre_producto = ($valor->LOTC_FechaVencimiento != '') ? "$nombre_producto. VCTO LOTE: ". mysql_to_human($valor->LOTC_FechaVencimiento) : $nombre_producto;

                    array_push($items,array(
                        "unidad_de_medida"          => "${medidaDetalle}",
                        "codigo"                    => "${codigo_usuario}",
                        "codigo_producto_sunat"     => "${codigo_original}",
                        "descripcion"               => "${nombre_producto}",
                        "cantidad"                  => "${prodcantidad}",
                        "valor_unitario"            => "${precio_sin_igv}",
                        "precio_unitario"           => "${precio_con_igv}",
                        "descuento"                 => "",
                        "subtotal"                  => "${prodsubtotal}",
                        "tipo_de_igv"               => "${tipo_afectacion}",
                        "igv"                       => "${igvxprod}",
                        "impuesto_bolsas"           => "${importeBolsa}",
                        "total"                     => "${prodtotal}",
                        "anticipo_regularizacion"   => "false",
                        "anticipo_documento_serie"  => "",
                        "anticipo_documento_numero" => ""
                    ));
                }

                $consul_guia = $this->comprobante_model->buscar_guiarem_comprobante($codigo);

                if ($consul_guia != NULL){
                    foreach ($consul_guia as $key => $value) {
                        $serieguia = $value->GUIAREMC_Serie;
                        $numeroguia = $value->GUIAREMC_Numero;
                        array_push($guias,array(
                            "guia_tipo"          => "1",
                            "guia_serie_numero"  => "${serieguia}-${numeroguia}"
                            
                        ));
                    }
                }
            }

            $gravada -= $gravada * $descuento100 / 100;
            $exonerado -= $exonerado * $descuento100 / 100;
            $inafecto -= $inafecto * $descuento100 / 100;

            $desformapago = $this->formapago_model->obtener2($forma_pago);
            $forma_pago2 = $desformapago[0]->FORPAC_Descripcion;
            $cliente_tipoDoc="";
            $cliente_document="";

            if ($tipo_de_comprobante == '1') {//factura
                $cliente_tipoDoc = $tipoDocIdentidad;
                $cliente_document = $ruc_cliente;
            }
            else
                if($tipo_de_comprobante == '2') {//boleta
                    if ($dni_cliente != "" && $dni_cliente != "-" && $dni_cliente != NULL){
                        $cliente_document = $dni_cliente;
                        $tipodocid = 1;
                    }
                    else
                        if ($ruc_cliente != "" && $ruc_cliente != "-" && $ruc_cliente != NULL){
                            $tipodocid = 2;
                            $cliente_document = $ruc_cliente;
                        }
                    else
                        $cliente_document = $ruc_cliente;

                    switch ($tipodocid) {
                        case '1':
                        $cliente_tipoDoc = '1'; // DNI
                            break;
                        case '2':
                        $cliente_tipoDoc = '6'; // RUC
                            break;
                        case '3':
                        $cliente_tipoDoc = '7'; // PASAPORTE
                            break; 
                        
                        default:
                        $cliente_tipoDoc ="-"; // VARIOS
                            break;
                    }
            }
            else{
                $cliente_tipoDoc = (strlen($ruc_cliente)== 6) ? 1 : '-' ;
                $cliente_document = (strlen($ruc_cliente)== 11) ? $ruc_cliente :  '-' ;
            }

            if ($tipo_oper == 'C'){
                $filter2 = array('CPP_codigo' => $codigo,
                                'respuestas_tipoDocumento' => 'F',
                                'respuestas_serie' => $serie,
                                'respuestas_numero' => $numero
                                );
                $this->comprobante_model->insertar_respuestaSunat($filter2);
                #$this->lib_props->sendMail(116, $codigo, NULL, "SE HA REGISTRADO UN INGRESO DE MERCADERIA.", "COMPROBANTE"); # MENU 116 = Producción
                $exito = true;
                return $exito;
            }else{
                    if ( $fecha_emision != date("Y-m-d") ){
                        $objFecha1 = new DateTime($fecha_emision); # CREA UN OBJETO CON LA FECHA DE EMISION
                        $objFecha2 = new DateTime($fecha_vencimiento); # CREA UN OBJETO CON LA FECHA DE VENCIMIENTO
                        $intervFechas = $objFecha1->diff($objFecha2); # CREA UN OBJETO CON LA DIFERENCIA ENTRE AMBAS FECHAS

                        # NUEVA FECHA DE EMISION
                        $fecha_emision = date("Y-m-d");
                        
                        # SUMA LA CANTIDAD DE DIAS ENTRE AMBAS FECHAS PARA LA NUEVA FECHA DE VENCIMIENTO
                        $fechaV = strtotime("$oper$intervFechas->days days", strtotime($fecha_emision) );
                        # APLICA FORMATO A LA FECHA DE VENCIMIENTO
                        $fecha_vencimiento = date("Y-m-d" , $fechaV);

                        # ACTUALIZA EN LA DB LAS FECHAS DE EMISION Y VENCIMIENTO DEL COMPROBANTE

                        $filterFechas = new stdClass();
                        $filterFechas->CPC_Fecha = $fecha_emision;
                        $filterFechas->CPC_FechaVencimiento = $fecha_vencimiento;
                        $this->comprobante_model->modificar_comprobante($codigo, $filterFechas);
                    }

                    # TIPO DE CAMBIO REGISTRADO PARA EL DIA DE LA EMISION, SEGUN LA MONEDA Y LA COMPAÑIA
                    $tdcInfo = $this->tipocambio_model->get_tdc_dia($fecha_emision, $moneda, $compania);
                    $tdc = $tdcInfo->TIPCAMC_FactorConversion;

                    $data2 = array(
                        "operacion"                         => "generar_comprobante",
                        "tipo_de_comprobante"               => "${tipo_de_comprobante}",
                        "serie"                             => "${serie}",
                        "numero"                            => "${numero}",
                        "sunat_transaction"                 => "${sunat_transaction}",
                        "cliente_tipo_de_documento"         => "${cliente_tipoDoc}",
                        "cliente_numero_de_documento"       => "${cliente_document}",
                        "cliente_denominacion"              => "${nombre_cliente}",
                        "cliente_direccion"                 => "${direccion}",
                        "cliente_email"                     => "${email}",
                        "cliente_email_1"                   => "",
                        "cliente_email_2"                   => "",
                        "fecha_de_emision"                  => "${fecha_emision}",
                        "fecha_de_vencimiento"              => "${fecha_vencimiento}",
                        "moneda"                            => "${moneda}",
                        "tipo_de_cambio"                    => "${tdc}",
                        "porcentaje_de_igv"                 => "${igv100}",
                        "descuento_global"                  => "${descuento}",
                        "total_descuento"                   => "${descuento}",
                        "total_anticipo"                    => "",
                        "total_gravada"                     => "${gravada}",
                        "total_inafecta"                    => "${inafecto}",
                        "total_exonerada"                   => "${exonerado}",
                        "total_igv"                         => "${igv}",
                        "total_gratuita"                    => "${gratuito}",
                        "total_otros_cargos"                => "",
                        "total"                             => "${total}",
                        "percepcion_tipo"                   => "",
                        "percepcion_base_imponible"         => "",
                        "total_percepcion"                  => "",
                        "total_incluido_percepcion"         => "",
                        "total_impuestos_bolsas"            => "${totalBolsa}",
                        "detraccion"                        => "false",
                        "observaciones"                     => "${observacion}",
                        "documento_que_se_modifica_tipo"    => "",
                        "documento_que_se_modifica_serie"   => "",
                        "documento_que_se_modifica_numero"  => "",
                        "tipo_de_nota_de_credito"           => "",
                        "tipo_de_nota_de_debito"            => "",
                        "enviar_automaticamente_a_la_sunat" => "true",
                        "enviar_automaticamente_al_cliente" => "false",
                        "codigo_unico"                      => "",
                        "condiciones_de_pago"               => "${forma_pago2}",
                        "medio_de_pago"                     => "",
                        "placa_vehiculo"                    => "",
                        "orden_compra_servicio"             => "${compraCliente}",
                        "tabla_personalizada_codigo"        => "",
                        "formato_de_pdf"                    => "",
                        "items"                             => $items,
                        "guias"                             => $guias 
                        
                    );

                    $data_json = json_encode($data2);
                    //Invocamos el servicio de NUBEFACT
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

                    if( !isset($respuesta2->errors) ) {
                        if ( $respuesta2->enlace_del_pdf != NULL && $respuesta2->enlace_del_pdf != ''){
                            $filter2->respuestas_compañia = $this->compania;
                            $filter2->CPP_codigo = $codigo;
                            $filter2->respuestas_serie = $respuesta2->serie;    
                            $filter2->respuestas_numero = $respuesta2->numero;
                            $filter2->respuestas_tipoDocumento = $respuesta2->tipo_de_comprobante;
                            $filter2->respuestas_enlace = $respuesta2->enlace;
                            $filter2->respuestas_aceptadaporsunat = $respuesta2->aceptada_por_sunat;
                            $filter2->respuestas_sunatdescription = $respuesta2->sunat_description;
                            $filter2->respuestas_sunatnote = $respuesta2->sunat_note;
                            $filter2->respuestas_sunatresponsecode = $respuesta2->sunat_responsecode;
                            $filter2->respuestas_sunatsoaperror = $respuesta2->sunat_soap_error;
                            $filter2->respuestas_pdfzipbase64 = $respuesta2->pdf_zip_base64;
                            $filter2->respuestas_xmlzipbase64 = $respuesta2->xml_zip_base64;
                            $filter2->respuestas_cdrzipbase64 = $respuesta2->cdr_zip_base64;
                            $filter2->respuestas_cadenaparacodigoqr = $respuesta2->cadena_para_codigo_qr;
                            $filter2->respuestas_codigohash = $respuesta2->codigo_hash;
                            $filter2->respuestas_enlacepdf = $respuesta2->enlace_del_pdf;
                            $filter2->respuestas_enlacexml = $respuesta2->enlace_del_xml;
                            $filter2->respuestas_enlacecdr = $respuesta2->enlace_del_cdr;

                            $exito = true;
                        }
                        else{
                            $exito = $this->ConsultarComprobanteNubefact( $codigo );
                            if ( $exito == false ){
                                $filter2->respuestas_compañia = $this->compania;
                                $filter2->CPP_codigo = $codigo;
                                $filter2->respuestas_serie = $serieFac;    
                                $filter2->respuestas_numero = $numero;
                                $filter2->respuestas_tipoDocumento = $tipo_de_comprobante;
                                $filter2->respuestas_deta = "ERROR DE COMUNICACIÓN, INTENTE ENVIAR EL DOCUMENTO NUEVAMENTE.";
                            }
                        }
                    }
                    else {
                        $filter2->respuestas_compañia = $this->compania;
                        $filter2->CPP_codigo = $codigo;
                        $filter2->respuestas_serie = $serieFac;    
                        $filter2->respuestas_numero = $numero;
                        $filter2->respuestas_tipoDocumento = $tipo_de_comprobante;
                        $filter2->respuestas_deta = $respuesta2->errors;
                        $exito = ($respuesta2->errors == "Este documento ya existe en [PSE]") ? true : false;
                    }
                //Si viene directamente del metodo canjear_documento ambos flag = 1 y no ejecuta lo siguiente
                //Si el envio tuvo exito y ya fue movido el stock por el canje de comprobante actualizo el flag
                if ( $exito == true && $flagMueveStock == 1 && $flagEstado == 2){
                    $flagFilter = new stdClass();
                    $flagFilter->CPC_FlagEstado = 1;
                    $this->comprobante_model->modificar_comprobante($codigo, $flagFilter);
                    $exito = false; // Retorno false para que el disparador no mueva el stock.
                }
                    
                $this->comprobante_model->insertar_respuestaSunat($filter2);
                return $exito;
            }
    }

    public function consutarRespuestaPdfsunat($codigoRespCompro=null){
        $pdfRespSunat = $this->comprobante_model->consultar_respuestaSunat($codigoRespCompro);

        if ( $pdfRespSunat->respuestas_enlacepdf == NULL || $pdfRespSunat->respuestas_enlacepdf == '' ){
            $exito = $this->ConsultarComprobanteNubefact( $codigoRespCompro );
            if ($exito == true)
                $pdfRespSunat = $this->comprobante_model->consultar_respuestaSunat($codigoRespCompro);
            #else
            #    $pdfRespSunat->respuestas_enlacepdf = NULL;
        }

        echo json_encode($pdfRespSunat);
    }

    /*public function downloadsFiles($inicio, $fin){

        $zip = new ZipArchive();
        $zip->open("archivos.zip",ZipArchive::CREATE);
        $zip->addEmptyDir("pdf");
        $zip->addEmptyDir("xml");
        $zip->addEmptyDir("cdr");
        
        # LOCAL PRINCIPAL
            #$ruta = "https://www.pse.pe/api/v1/820240ea59fc407c8a0157b3f194ca415edb56d289904591a2857807449b6cc2";
            #$token = "eyJhbGciOiJIUzI1NiJ9.ImM0M2ZhODM1MWIzNzQ5MWM5NGU3NDE5MDlhYmNjYjAxYzI5Nzk4NTI3ZjllNGQxMWE4MTc0NGM4OTQ5ZDQ0MWIi.m_q2goKbeTpddfGVy3azUYh6CGTezJNMzt3JTuAaU2s";
        
        # SUCURSAL I
            $ruta = "https://www.pse.pe/api/v1/b2205019197f44ff8d92956791bd41f15eee4f68999345b793d8d71287e32eb8";
            $token = "eyJhbGciOiJIUzI1NiJ9.IjM4ZjMzZDM2Y2RmZjQxZDA4OTlkNGU2YjcyNDc1OTYzOGVmOWFhNGQxNTkyNDIzNGJlMGZlZmU3N2M0OGQyMmIi.c0vegErBqLH6NQBu_XyH9hSQI5VqxOmtxZLLOaEwlEE";

        for ($i = $inicio; $i <= $fin; $i++){

            $serieFac = "FC05";
            $tipo_de_comprobante = '1';

            $numero = $i;
            
            $data2 = array(
                "operacion"             => "consultar_comprobante",
                "tipo_de_comprobante"   => "${tipo_de_comprobante}",
                "serie"                 => "${serieFac}",
                "numero"                => "${numero}"
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
            curl_close($ch);

            if ( !isset($respuesta2->errors) && $respuesta2->enlace_del_pdf != NULL ){
                if ( $respuesta2->enlace_del_pdf != ""){
                    $this->downloadFile($respuesta2->enlace_del_pdf, "$serieFac-$numero.pdf");
                    $zip->addFile("downloads/$serieFac-$numero.pdf", "/pdf/$serieFac-$numero.pdf");
                }
                
                if ( $respuesta2->enlace_del_xml != ""){
                    $this->downloadFile($respuesta2->enlace_del_xml, "$serieFac-$numero.xml");
                    $zip->addFile("downloads/$serieFac-$numero.xml", "/xml/$serieFac-$numero.xml");
                }
                
                if ( $respuesta2->enlace_del_cdr != ""){
                    $this->downloadFile($respuesta2->enlace_del_cdr, "$serieFac-$numero.cdr");
                    $zip->addFile("downloads/$serieFac-$numero.cdr", "/cdr/$serieFac-$numero.cdr");
                }
            }
        }
        
        $zip->close();
        
        $files = glob('downloads/*'); //obtenemos todos los nombres de los ficheros
        foreach($files as $file){
            if (is_file($file))
                unlink($file); //elimino el fichero
        }

        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=archivos.zip");
        readfile('archivos.zip');
        unlink('archivos.zip');
    }*/

    function downloadFile($fileUrl, $file){
        $saveTo = "downloads/$file";
        $fp = fopen($saveTo, 'w+');

        if($fp === false){
            throw new Exception('Could not open: ' . $saveTo);
        }
         
        $ch = curl_init($fileUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        curl_exec($ch);
         
        if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        fclose($fp);
    }

    public function consutarRespuestaXmlsunat($codigo = null, $ventana = true){
        $pdfRespSunat = $this->comprobante_model->consultar_respuestaXMLSunat($codigo);

        if ( $pdfRespSunat->respuestas_enlacexml == NULL || $pdfRespSunat->respuestas_enlacexml == '' ){
            $exito = $this->ConsultarComprobanteNubefact( $codigo );
            if ($exito == true)
                $pdfRespSunat = $this->comprobante_model->consultar_respuestaSunat($codigo);
        }
        
        if ($ventana == true)
            echo json_encode($pdfRespSunat);
        else
            return $pdfRespSunat->respuestas_enlacexml;
    }

    public function getFechaE(){
        $id = $this->input->post("comprobante");
        $comprobanteInfo = $this->comprobante_model->obtener_comprobante($id);

        if ( $comprobanteInfo != NULL ){
            foreach ($comprobanteInfo as $i => $val) {
                # SI ES UNA VENTA Y ES DISTINTO A COMPROBANTE
                if ( $val->CPC_TipoOperacion == "V" && $val->CPC_TipoDocumento != "N" ){
                    # SI LA FECHA ES DISTINTA AL DIA DE ENVIO (HOY)
                    if ( $val->CPC_Fecha != date("Y-m-d") )
                        $json = array( "update" => true, "fecha_hoy" => date("Y-m-d"), "comprobante_fecha" => $val->CPC_Fecha );
                    else
                        $json = array( "update" => false, "fecha_hoy" => date("Y-m-d"), "comprobante_fecha" => $val->CPC_Fecha );
                }
                else
                    $json = array( "update" => false, "fecha_hoy" => date("Y-m-d"), "comprobante_fecha" => $val->CPC_Fecha );
            }
        }
        else
            $json = array( "update" => true, "fecha_hoy" => date("Y-m-d"), "comprobante_fecha" => date("Y-m-d") );

        echo json_encode($json);
    }

    public function getInfoSendMail(){
        $comprobante = $this->input->post("id");
        $comprobanteInfo = $this->comprobante_model->getComprobanteMail($comprobante);

        $data = array();

        foreach ($comprobanteInfo as $i => $val) {

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
                                "codigo" => $val->CPP_Codigo,
                                "nombre" => $val->razon_social,
                                "ruc" => $val->ruc,
                                "serie" => $val->CPC_Serie,
                                "numero" => $val->CPC_Numero,
                                "fecha" => mysql_to_human($val->CPC_Fecha),
                                "importe" => $val->MONED_Simbolo . " " . $val->CPC_total,
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

        $docs = new stdClass();
        $docs->ticket = $this->input->post("ticket");
        $docs->a4 = $this->input->post("a4");
        $docs->xml = $this->input->post("xml");

        $tipo = $this->input->post("documento");
        $codigo = $this->input->post("codigo");

        $send = $this->lib_props->sendDocMail($titulo, $destinatario, $mensaje, $adjunto, $tipo, $codigo, $docs);
        $json = ($send == true) ? array("result" => "success") : array("result" => "error");

        echo json_encode($json);
    }

    public function ventana_osafact_correos($codigo) {
        $nombre_persona1 = $this->session->userdata('nombre_persona');
        $persona1 = $this->session->userdata('persona');

        $datos_usuario = $this->persona_model->obtener_datosPersona($persona1);
        if ($datos_usuario[0]) {
            $emailusuario = $datos_usuario[0]->PERSC_Email;
        }

        $compania = $this->compania;
        $data['compania'] = $compania;
        $data_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania = $this->compania_model->obtener_compania($this->compania);

        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);

        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $respuestaSunatCompr= $this->comprobante_model->consultar_respuestaSunat($codigo);

        $serie = $respuestaSunatCompr->respuestas_serie;
        $numero = $respuestaSunatCompr->respuestas_numero;

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

        switch ($datos_comprobante[0]->CPC_TipoDocumento) {
            case 'F':
                $documento = "FACTURA";
                break;
            case 'B':
                $documento = "BOLETA";
                break;
            case 'N':
                $documento = "COMPROBANTE";
                break;
            
            default:
                $documento = "FACTURA";
                break;
        }
        
        $RespuestaCadema = $respuestaSunatCompr->respuestas_cadenaparacodigoqr;
        $arrayRespuestaCadema = explode('|',$RespuestaCadema);
        $data['prueba']= $arrayRespuestaCadema;
        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['documento'] = $documento;
        $data['serie'] = $arrayRespuestaCadema[2];
        $data['numero'] = $arrayRespuestaCadema[3];
        $data['pdfsunatresp'] = $respuestaSunatCompr->respuestas_enlace;

        $data['nomFechaEmi'] = $arrayRespuestaCadema[6];
        $data['nomTotal'] = $arrayRespuestaCadema[5];
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['nombre_persona1'] = $nombre_persona1;
        $data['emailusuario'] = $_SESSION['user_name']."@osa-fact.com";
        $data['emailenviar'] = $emailenviar;
        $data['titulo'] = "ENVIAR $documento DE VENTA ELECTRONICA - CORREO";
        $data['formulario'] = "frmPresupuestoCorreo";
        $data['url_action'] = base_url() . "index.php/ventas/comprobante/Enviarcorreo";
        $data['hoy'] = $fecha;
        $data['codigo'] = $codigo;


        $data['tipo_codificacion'] = 1;
        $this->load->view('ventas/comprobante_correo', $data);
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
        $titulomensaje = $this->input->post('titulomensaje');
        $nomEmpresaDest = $this->input->post('nomEmpresaDest');
        $documento = $this->input->post('documento');
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
                                                    <td style="padding:0px; font-size:12pt;"><b>'.$nombre_empresa.'</b>, ENVIA UN DOCUMENTO ELECTRONICO.</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:0px; font-size:12pt;"> </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:0px; font-size:11pt; font-weight:bold;">- TIPO: '.$documento.' DE VENTA ELECTRONICA</td>
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
                                                <tr>
                                                    </br>
                                                    <td style="padding:0px; font-size:12px; color:green;">Se puede ver el documento en PDF y XML en el siguiente LINK:</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:0px; font-size:10px;">- ' . $nomLink . '</td>
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
                            $uPDF = $nomLink.".pdf";
                            $cdr = $nomLink.".cdr";
                            $xml = $nomLink.".xml";

                            $mail->AddStringAttachment($this->file_get_contents_curl($uPDF), 'comprobante.pdf');
                            $mail->AddStringAttachment($this->file_get_contents_curl($cdr), 'cdr.cdr');
                            $mail->AddStringAttachment($this->file_get_contents_curl($xml), 'xml.xml');
                        #}

                        $mail->MsgHTML($enviarformatopag);
                        
                        if(!$mail->Send()) {
                            $error = 'Mail error: '.$mail->ErrorInfo;
                            echo $error;
                        } else {
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
                            echo (" Mensaje Enviado");
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

    public function comprobante_insertar_ref(){
        $compania = $this->compania;
        /**verificamos codigo del comprobante  ***/
        $codigo = $this->input->post('codigo');
        
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $dref = $this->input->post('dRef');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $obra = $this->input->post('obra');
        $documento = $this->documento_model->obtenerAbreviatura(trim($tipo_docu));
        $tipo = $documento[0]->DOCUP_Codigo;
        $filter = new stdClass();        
        $filter->CPC_TipoOperacion = $tipo_oper;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->GUIAREMP_Codigo = $dref;
        $filter->CPC_NumeroAutomatico= $this->input->post('numeroAutomatico');
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0'){
            $filter->FORPAP_Codigo = $this->input->post('forma_pago');
            $f_pago = $this->input->post('forma_pago');
        }
        
        $filter->CPC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CPC_Fecha = $this->input->post('fecha');
        $filter->CPC_FechaVencimiento = $this->input->post('fecha_vencimiento');
        $filter->CPC_Numero = $this->input->post('numero');
        $filter->CPC_Serie = $this->input->post('serie');
        
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo); // Vuelve a consultar el ultimo correlativo - 
        $filter->CPC_Numero = ($tipo_oper == 'V') ? $cofiguracion_datos[0]->CONFIC_Numero + 1 : $this->input->post('numero'); // Incrementa en numero el ultimo correlativo consultado para ingresar.
        
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
        if ($tipo_oper == 'V'){

            $filter->CLIP_Codigo = $this->input->post('cliente');
            $direccion = $this->input->post('direccionsuc');
            $filter->CPC_Direccion =$direccion;
        }else
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto');
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
    
            if ($this->input->post('oc_cliente') != '' && $this->input->post('oc_cliente') != '0')
            $filter->CPP_Compracliente = $this->input->post('oc_cliente');
        
  
        $filter->CPC_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CPC_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CPC_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->CPC_ModoImpresion = $this->input->post('modo_impresion');
        #if ($tipo_docu != 'B' && $tipo_docu != 'N') {
            $filter->CPC_subtotal = $this->input->post('preciototal');
            $filter->CPC_descuento = $this->input->post('descuentotal');
            $filter->CPC_igv = $this->input->post('igvtotal');
        #} else {
            $filter->CPC_subtotal_conigv = $this->input->post('preciototal_conigv');
            $filter->CPC_descuento_conigv = $this->input->post('descuentotal_conigv');
        #}
        $filter->CPC_total = $this->input->post('importetotal');

        if ($this->input->post('cboVendedor') != '')
            $filter->CPC_Vendedor = $this->input->post('cboVendedor');
        else
            $filter->CPC_Vendedor = $this->usuario;

        $filter->CPC_TDC = $this->input->post('tdc');


        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        $filter->ALMAP_Codigo = $this->input->post('almacen');
        $filter->PROYP_Codigo = $obra;
        $filter->PRESUP_Codigo = null;

        //$filter->OCOMP_Codigo=null;
        //Datos cabecera de la guiasa.
        
        $filter->CAJA_Codigo = $this->input->post('caja');
        $filter->CPC_Retencion = $this->input->post('retencion_codigo');
        $filter->CPC_RetencionPorc = $this->input->post('retencion_porc');

		if($codigo!=0){
			$comprobante=$codigo;
			$this->comprobante_model->modificar_comprobante($comprobante,$filter);
		}else{
			$comprobante = $this->comprobante_model->insertar_comprobante($filter);
		}	
        
        /*:::::::::: CUOTAS ::::::::::::::::*/

        $cuota_fechai = $this->input->post('cuota-fechai');
        $cuota_fechaf = $this->input->post('cuota-fechaf');
        $monto_cuotas = $this->input->post('cuota-monto');

        $tipo_tributo = "";
        if (isset($f_pago) && $f_pago != 1) {
            if($monto_cuotas != ""){
                foreach ($monto_cuotas as $indice => $cuota) {
                    $cuotasData = new stdClass();
                    $cuotasData->CUOT_Numero = $indice + 1;
                    $cuotasData->CPP_Codigo = $comprobante;
                    $cuotasData->CUOT_Monto = $monto_cuotas[$indice];
                    $cuotasData->CUOT_FechaInicio = $cuota_fechai[$indice];
                    $cuotasData->CUOT_Fecha = $cuota_fechaf[$indice];
                    $cuotasData->CUOT_FlagFisica = 0;
                    $cuotasData->CUOT_FlagEstado = 1;
                    $cuotasData->CUOT_FlagPagado = 0;
                    $cuotasData->CUOT_UsuarioRegistro = $_SESSION['user'];
                    $cuotasData->CUOT_TipoCuenta = ($tipo_oper == "V") ? 1 : 2;
                    
                    $this->cuota_model->registrar($cuotasData);
                }
            }
        }

        /*:::::: /// CUOTAS /// :::::::::::*/



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
        #if ($tipo_docu != 'B' && $tipo_docu != 'N') {
            $prodpu = $this->input->post('prodpu');
            $prodprecio = $this->input->post('prodprecio');
            $proddescuento = $this->input->post('proddescuento');
            $prodigv = $this->input->post('prodigv');
        #} else {
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        #}
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
        $obs_detalle = $this->input->post('prodobservacion');
        $lote = $this->input->post('idLote');
        $tafectacion = $this->input->post('tafectacion');
        $icbper = $this->input->post('icbper');
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
                    
                    $filter->LOTP_Codigo = $lote[$indice];
                    $filter->AFECT_Codigo = $tafectacion[$indice];
                    $filter->CPDEC_ITEMS = $icbper[$indice];
	                
                    $filter->CPDEC_Cantidad = $prodcantidad[$indice];
	                #if ($tipo_docu != 'B' && $tipo_docu != 'N') {
	                    $filter->CPDEC_Pu = $prodpu[$indice];
	                    $filter->CPDEC_Subtotal = $prodprecio[$indice];
	                    $filter->CPDEC_Descuento = $proddescuento[$indice];
	                    $filter->CPDEC_Igv = $prodigv[$indice];
	                #} else {
	                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
	                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
	                #}
	                $filter->CPDEC_Total = $prodimporte[$indice];
	                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
	                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
	                $filter->CPDEC_Igv100 = $prodigv100[$indice];

	                if ($tipo_oper == 'V')
	                    $filter->CPDEC_Costo = $prodcosto[$indice];
	                
	                $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
	                $filter->CPDEC_GenInd =$flagGenInd[$indice]; 
                    $filter->CPDEC_Observacion = strtoupper($obs_detalle[$indice]);
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

        $json = array("result" => true, "codigo" => $comprobante);
        echo json_encode($json);
    }

    public function comprobante_ver($codigo, $tipo_oper = 'V', $tipo_docu = 'F'){
        
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
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

    public function comprobante_editar($codigo, $tipo_oper = 'V', $tipo_docu = 'F',$btnCancelVisible = 'S'){

        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/
        
        $data["lista_cuotas"] = $this->cuota_model->listarByIdComprobante($codigo);

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);      
        
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        //$obra = $datos_comprobante[0]->PROYP_Codigo;
        $proyecto = $datos_comprobante[0]->PROYP_Codigo;
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
        $fecha = $datos_comprobante[0]->CPC_Fecha;
        $fecha_vencimiento = $datos_comprobante[0]->CPC_FechaVencimiento;
        $vendedor = $datos_comprobante[0]->CPC_Vendedor;
        $importacion = $datos_comprobante[0]->IMPOR_Nombre;
        $direccion = $datos_comprobante[0]->CPC_Direccion;
        $oc_cliente = $datos_comprobante[0]->CPP_Compracliente;
        $codigoRetencion = $datos_comprobante[0]->CPC_Retencion;
        $porcRetencion = $datos_comprobante[0]->CPC_RetencionPorc;
        $caja = $datos_comprobante[0]->CAJA_Codigo;
        $tipo_venta = $datos_comprobante[0]->CPC_Tipo_venta;

        $data['compania']  = $datos_comprobante[0]->COMPP_Codigo;

        $tdc = $datos_comprobante[0]->CPC_TDC;
        
        $codigocliente = $datos_comprobante[0]->CLIP_Codigo;
        $codigoproyecto = $datos_comprobante[0]->PROYP_Codigo;
        
        if($codigoproyecto != 0){
        	$listaproyecto = $this->proyecto_model->seleccionar($codigoproyecto);
        	$data['cboObra'] = form_dropdown("obra",$listaproyecto,$codigoproyecto, " class='comboGrande'  id='obra' ");
        }else{
        	$data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
        }
        $data["oc_cliente"]=$oc_cliente;
        $data['numeroAutomatico'] = $datos_comprobante[0]->CPC_NumeroAutomatico;
        $data['cmbVendedor']=$this->select_cmbVendedor($vendedor);
        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = ($datos_cliente->ruc != '' && $datos_cliente->ruc != 0) ? $datos_cliente->ruc : $datos_cliente->dni;
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
        
        $data['direccionsuc'] = form_input(array("name" => "direccionsuc", "id" => "direccionsuc", "class" => "cajaGeneral", "size" => "40", "maxlength" => "250", "value" => $direccion));
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

        $data['cboproyecto'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', $proyecto);
        $data['cboimportacion'] = $this->OPTION_generador($this->importacion_model->listar_importacion(), 'IMPOR_Codigo', 'IMPOR_Nombre', $importacion);

        $data['cboVendedor'] = $this->lib_props->listarUsuarios($vendedor);
        $filter = new stdClass();
        $filter->situacion = 1;
        $data['cajas'] = $this->caja_model->getCajas($filter);

        $data['caja']       = $_SESSION["caja_activa"];
        $data['cajero_id']  = $_SESSION["cajero_id"];
        $data['tipo_venta'] = $tipo_venta;

        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['usa_adelanto'] = $datos_comprobante[0]->CPC_FlagUsaAdelanto;
        
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
        
        $data['descuento'] = $descuento100;
        $data['igv'] = $igv100;
        $data['igv_default'] = $comp_confi[0]->COMPCONFIC_Igv;
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
        $data['fecha_vencimiento'] = $fecha_vencimiento;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo'] = $docurefe_codigo;
        $data['observacion'] = $observacion;
        $data['estado'] = $estado;
        $data['hidden'] = "";
        $data['focus'] = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger'] = "";
        $data['numero_suger'] = "";
        
        $data['tdcDolar'] = $moneda > 2 ? $datos_comprobante[0]->CPC_TDC_opcional : $tdc;
        $data['tdcEuro'] = $moneda > 2 ? $tdc : '';

        $data['id_proyecto'] = $codigoproyecto;
        
        $data["codigoRetencion"] = $codigoRetencion;
        $data["porcRetencion"] = $porcRetencion;

		$data['guiaremision'] = $guiaremision;    
		$data['dRef'] = $guiaremision;
		$data['tipoOperCodigo'] =$tipo;
        //$detalle_comprobante = $this->obtener_lista_detalles($codigo);
        //$data['detalle_comprobante'] = $detalle_comprobante;
        unset($_SESSION['serie']);
        unset($_SESSION['serieReal']);
        unset($_SESSION['serieRealBD']);
        /**gcbq verificamos si el detalle dee comprobante contiene productos individuales**/
        if($detalle_comprobante!=null  && count($detalle_comprobante)>0){
        	/**iniciamos la libreria actualizacion de serie seleccionada solo se da en ventas**/
        	$this->load->model('almacen/almacenproductoserie_model');
        	/**fin**/
        	
			foreach ($detalle_comprobante as $key=>$valor){
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

                $referencia_oc_detalle = $this->comprobante_model->obtener_referencia_comprobante_by_id_detalle_compra($valor->OCOMP_Codigo_VC);

                $referencia_ov = $this->ocompra_model->obtener_referencia_compra_by_id_detalle_venta($referencia_oc_detalle->OCOMP_Codigo_venta);

                $valor->RazonSocialRef = "Almacen";
                $valor->PROYP_Codigo = isset($referencia_ov->PROYP_Codigo) ? $referencia_ov->PROYP_Codigo : 0;
                $valor->PROYC_Nombre = isset($referencia_ov->PROYC_Nombre) ? $referencia_ov->PROYC_Nombre : 0;

                $valor->OCOMP_Codigo_venta_ref = isset($referencia_ov) ? $referencia_ov->OCOMP_Codigo : 0;
                    
                if(isset($referencia_ov->CLIP_Codigo)){
                    $datos_cliente = $this->cliente_model->obtener($referencia_ov->CLIP_Codigo);
                    $valor->RazonSocialRef = $datos_cliente->nombre;
                }
			}
        }
        $data['btnCancelVisible'] = $btnCancelVisible;
        /**fin de procewso de realizaciom**/
        $data['scripts'] = $this->view_js;
        $this->layout->view('ventas/comprobante_nueva', $data);
    }

    public function comprobante_modificar(){
        if ($this->input->post('serie') == ''){
            $json = array("result" => false, "campo" => "serie");
            return NULL;
        }
        if ($this->input->post('numero') == ''){
            $json = array("result" => false, "campo" => "numero");
            return NULL;
        }
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == ''){
            $json = array("result" => false, "campo" => "ruc_cliente");
            return NULL;
        }
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == ''){
            $json = array("result" => false, "campo" => "ruc_proveedor");
            return NULL;
        }
        if ($this->input->post('moneda') == '0' || $this->input->post('moneda') == ''){
            $json = array("result" => false, "campo" => "moneda");
            return NULL;
        }
        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == ''){
            $json = array("result" => false, "campo" => "observacion");
            return NULL;
        }
        if ($this->input->post('tdcDolar') == ''){
            $json = array("result" => false, "campo" => "tdcDolar");
            return NULL;
        }
        if ($this->input->post('almacen') == ''){
            $json = array("result" => false, "campo" => "almacen");
            return NULL;
        }

        //VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $codigo = $this->input->post('codigo');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $prodpu= $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');
        $obra= $this->input->post('obra');
        $proyecto= $this->input->post('proyecto');
        $importacion = $this->input->post('importacion');
        $filter = new stdClass();
        $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->CPC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CPC_Fecha = $this->input->post('fecha');
        $filter->CPC_FechaVencimiento = $this->input->post('fecha_vencimiento');
        $filter->CPC_Numero = $this->input->post('numero');
        $filter->CPC_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CPC_descuento100 = $this->input->post('descuento');
        $filter->CPC_igv100 = $this->input->post('igv');
        $filter->CPC_Tipo_venta = $this->input->post('tipo_venta');
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->CPC_NumeroAutomatico = $this->input->post('numeroAutomatico');
        $filter->PROYP_Codigo = $obra;
        
        if ($this->input->post('oc_cliente') != '' && $this->input->post('oc_cliente') != '0')
        $filter->CPP_Compracliente = $this->input->post('oc_cliente');
        
        //$filter->PROYP_Codigo=$proyecto;
        if($tipo_oper == 'C') $filter->IMPOR_Nombre=$importacion;
        $nombre = $this->input->post('nombre_cliente');

        $filter->CPC_TDC = $filter->MONED_Codigo > 2 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');
        $filter->CPC_TDC_opcional = $filter->MONED_Codigo > 2 ? $this->input->post('tdcDolar') : NULL;

        if ($tipo_oper == 'V'){
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $direccion = $this->input->post('direccionsuc');
            $filter->CPC_Direccion = $direccion;
        }else{
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        }


        $filter->PRESUP_Codigo = NULL;
        if ($this->input->post('presupuesto_codigo') != '' && $this->input->post('presupuesto_codigo') != '0')
            $filter->PRESUP_Codigo = $this->input->post('presupuesto_codigo');
        
        $filter->OCOMP_Codigo = NULL;
        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
        
        $filter->GUIAREMP_Codigo = NULL;
        if ($this->input->post('guiaremision') != '' && $this->input->post('guiaremision') != '0')
            $filter->GUIAREMP_Codigo = $this->input->post('guiaremision');
        
        $filter->CPC_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CPC_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        //$filter->CPC_FlagEstado = $this->input->post('estado');
        $filter->CPC_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->CPC_ModoImpresion = $this->input->post('modo_impresion');
        #if ($tipo_docu != 'B') {
            $filter->CPC_subtotal = $this->input->post('preciototal');
            $filter->CPC_descuento = $this->input->post('descuentotal');
            $filter->CPC_igv = $this->input->post('igvtotal');
        #} else {
            $filter->CPC_subtotal_conigv = $this->input->post('preciototal_conigv');
            $filter->CPC_descuento_conigv = $this->input->post('descuentotal_conigv');
        #}
        $filter->CPC_total = $this->input->post('importetotal');

        if ($this->input->post('cboVendedor') != '')
            $filter->CPC_Vendedor = $this->input->post('cboVendedor');
        else
            $filter->CPC_Vendedor = $this->usuario;

        /**gcbq implementamos el tipo de documento dinamico***/
        $this->load->model('maestros/documento_model');
        $documento=$this->documento_model->obtenerAbreviatura(trim($tipo_docu));
        $tipo = $documento[0]->DOCUP_Codigo;

        $filter->CAJA_Codigo = $this->input->post('caja');
        $filter->CPC_Retencion = $this->input->post('retencion_codigo');
        $filter->CPC_RetencionPorc = $this->input->post('retencion_porc');
        $this->comprobante_model->modificar_comprobante($codigo, $filter);

        /*:::::::::: CUOTAS ::::::::::::::::*/

        $cuota_fechai = $this->input->post('cuota-fechai');
        $cuota_fechaf = $this->input->post('cuota-fechaf');
        $monto_cuotas = $this->input->post('cuota-monto');
        $this->cuota_model->delete($codigo);

        $tipo_tributo = "";
        if ( $this->input->post("forma_pago") != 1 ) {
            if( $monto_cuotas != "" ){
                foreach ($monto_cuotas as $indice => $cuota) {
                    $cuotasData = new stdClass();
                    $cuotasData->CUOT_Numero = $indice + 1;
                    $cuotasData->CPP_Codigo = $codigo;
                    $cuotasData->CUOT_Monto = $monto_cuotas[$indice];
                    $cuotasData->CUOT_FechaInicio = $cuota_fechai[$indice];
                    $cuotasData->CUOT_Fecha = $cuota_fechaf[$indice];
                    $cuotasData->CUOT_FlagFisica = 0;
                    $cuotasData->CUOT_FlagEstado = 1;
                    $cuotasData->CUOT_FlagPagado = 0;
                    $cuotasData->CUOT_UsuarioRegistro = $_SESSION['user'];
                    $cuotasData->CUOT_TipoCuenta = ($tipo_oper == "V") ? 1 : 2;
                    
                    $this->cuota_model->registrar($cuotasData);
                }
            }
        }

        /*:::::: /// CUOTAS /// :::::::::::*/

        /**verificamos para ELIMINAR LAS GUIAS RELACIONADAS TIPO:1**/
        /**modificamos a estado 0 LOS REGUISTROS ASOCIADOS AL DOCUMENTO y seriesDocumento asociado***/
        #$this->eliminarGuiaRelacionadasComprobante($tipo,$codigo);
        /**FIN DE ELIMINACION DE DOCUMENTOS***/
        
        
        $prodcodigo = $this->input->post('prodcodigo');
        $flagBS = $this->input->post('flagBS');
        $prodcantidad = $this->input->post('prodcantidad');
        #if ($tipo_docu != 'B') {
            $prodpu = $this->input->post('prodpu');
            $prodprecio = $this->input->post('prodprecio');
            $proddescuento = $this->input->post('proddescuento');
            $prodigv = $this->input->post('prodigv');

            $prodpu= $this->input->post('prodpu');
            $prodprecio= $this->input->post('prodprecio');
        #} else {
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        #}
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodcosto = $this->input->post('prodcosto');
        $almacenProducto = $this->input->post('almacenProducto');
        $proddescri = $this->input->post('proddescri');
        $obs_detalle = $this->input->post('prodobservacion');
        $lote = $this->input->post('idLote');
        $tafectacion = $this->input->post('tafectacion');
        $icbper = $this->input->post('icbper');
        $estado = $this->input->post('estado');

        $prodpu = $this->input->post('prodpu');
        $prodprecio= $this->input->post('prodprecio');
        $pendiente = $this->input->post('pendiente');
        $oventacod = $this->input->post("oventacod");

        $pedir = $this->input->post("pedir");
        
        if (is_array($detacodi) > 0) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];

                $filter = new stdClass();
                $filter->CPP_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                if ($flagBS[$indice] == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];

                $filter->LOTP_Codigo = $lote[$indice];
                $filter->AFECT_Codigo = $tafectacion[$indice];
                $filter->CPDEC_ITEMS = $icbper[$indice];
                
                $filter->CPDEC_Cantidad = $prodcantidad[$indice];
                $filter->CPDEC_Pendiente = $prodcantidad[$indice];
                #if ($tipo_docu != 'B') {
                    $filter->CPDEC_Pu = $prodpu[$indice];
                    $filter->CPDEC_Subtotal = $prodprecio[$indice];

                    $filter->CPDEC_Descuento = $proddescuento[$indice];
                    $filter->CPDEC_Igv = $prodigv[$indice];
                #} else {
                    $filter->CPDEC_Pu = $prodpu[$indice];
                    $filter->CPDEC_Subtotal = $prodprecio[$indice];
                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
                #}
                $filter->CPDEC_Total = $prodimporte[$indice];
                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
                $filter->CPDEC_Igv100 = $prodigv100[$indice];
                if ($tipo_oper == 'V')
                    $filter->CPDEC_Costo = $prodcosto[$indice];
                
                $filter->CPDEC_GenInd = $flagGenInd[$indice];
                $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
                $filter->CPDEC_Observacion = $obs_detalle[$indice];
                $codigoAlmacenProducto = $almacenProducto[$indice];
                $filter->ALMAP_Codigo = $codigoAlmacenProducto;

                $filter->OCOMP_Codigo_VC = $oventacod[$indice];

                $pendientef = $pendiente[$indice]-$prodcantidad[$indice];
              //  var_dump($pendientef);
               // exit();
                $codovente = $oventacod[$indice];
                
                $producto_id=$prodcodigo[$indice];
                if ($detalle_accion == 'n') {
                    $this->comprobantedetalle_model->insertar($filter);

                    if(!is_null($codovente) && $codovente != ''){
                    $this->ocompra_model->modificar_pendiente_cantidad_comprobante($codovente, -$prodcantidad[$indice]);
                    }
                    /**gcbq insertar serie de cada producto**/
                    if($flagGenInd[$indice]='I'){
                    	if($producto_id!=null){
                    		/**obtenemos las series de session por producto***/
                    		$seriesProducto=$this->session->userdata('serieReal');
                    		if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
                    			foreach ($seriesProducto as $alm2 => $arrAlmacen2) {
                    				if($alm2==$codigoAlmacenProducto){
                    					foreach ($arrAlmacen2 as $ind2 => $arrserie2) {
		                    				if ($ind2 == $producto_id) {
		                    					$serial = $arrserie2;
		                    					if($serial!=null && count($serial)>0){
		                    						foreach ($serial as $i => $serie) {
		                    							/**INSERTAMOS EN SERIE SI ES COMPRA**/
		                    							$filterSerie= new stdClass();
		                    							if($tipo_oper == 'C'){
		                    								$filterSerie->PROD_Codigo=$producto_id;
		                    								$filterSerie->SERIC_Numero=$serie->serieNumero;
		                    								$filterSerie->SERIC_FechaRegistro=date("Y-m-d H:i:s");
		                    								$filterSerie->SERIC_FlagEstado='1';
		                    								$filterSerie->ALMAP_Codigo=$codigoAlmacenProducto;
		                    								$serieCodigo=$this->serie_model->insertar($filterSerie);
		                    								$tipoIngreso=1;
		                    							}
		                    							
		                    							/**SI ES VENTA SE crea un nuevo registro en seriedocumento solamente**/
		                    							if($tipo_oper == 'V'){
		                    								$serieCodigo=$serie->serieCodigo;
		                    								$tipoIngreso=2;
		                    							}
		                    							/**insertamso serie documento**/
		                    							$filterSerieD= new stdClass();
		                    							$filterSerieD->SERDOC_Codigo=null;
		                    							$filterSerieD->SERIP_Codigo=$serieCodigo;
		                    							$filterSerieD->DOCUP_Codigo=$tipo;
		                    							$filterSerieD->SERDOC_NumeroRef=$codigo;
		                    							/**1:ingreso 2:salida**/
		                    							$filterSerieD->TIPOMOV_Tipo=$tipoIngreso;
		                    							$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
		                    							$filterSerieD->SERDOC_FlagEstado=1;
		                    							$this->seriedocumento_model->insertar($filterSerieD);
		                    							/**FIN DE INSERTAR EN SERIE**/
		                    							/**FIN DE INSERTAR EN SERIE**/
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
                    
                } elseif ($detalle_accion == 'm') {
                    $this->comprobantedetalle_model->modificar($valor, $filter);

                    if(!is_null($codovente) && $codovente != ''){

                    $this->ocompra_model->modificar_pendiente_cantidad_comprobante($codovente,$pendientef);
                    }
                    /**gcbq insertar serie de cada producto**/
                    if($flagGenInd[$indice]='I'){
                    	if($producto_id!=null){
                    		/**obtenemos las series de session por producto***/
                    		$seriesProducto=$this->session->userdata('serieReal');
                    		$serieReal = $seriesProducto;
                    		if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
                    			/***pongo todos en estado cero de las series asociadas a ese producto**/
                    			$seriesProductoBD=$this->session->userdata('serieRealBD');
                    			$serieBD = $seriesProductoBD;
                    			if($serieBD!=null && count($serieBD)>0){
                    				foreach ($serieBD as $almBD => $arrAlmacenBD) {
                    					if($almBD==$codigoAlmacenProducto){
                    						foreach ($arrAlmacenBD as $ind1BD => $arrserieBD) {
		                    					if ($ind1BD == $producto_id) {
		                    						foreach ($arrserieBD as $keyBD => $valueBD) {
		                    							/**cambiamos a ewstado 0**/
		                    							$filterSerie= new stdClass();
		                    							/**SI ES COMPRA SE MODIFICA EL ESTADO***/
		                    							if($tipo_oper == 'C'){
		                    								$filterSerie->SERIC_FlagEstado='0';
		                    								$this->serie_model->modificar($valueBD->SERIP_Codigo,$filterSerie);
		                    							}
		                    								
		                    								
		                    							$filterSerieD= new stdClass();
		                    							$filterSerieD->SERDOC_FlagEstado='0';
		                    							$this->seriedocumento_model->modificar($valueBD->SERDOC_Codigo,$filterSerieD);
		                    							
		                    							if($tipo_oper == 'V'){
		                    								/**deseleccionamos los registros en estadoSeleccion cero:0:desleccionado**/
		                    								$this->almacenproductoserie_model->seleccionarSerieBD($valueBD->SERIP_Codigo,0);
		                    							}	
		                    						}
		                    					}
                    						}
                    					}	
                    				}
                    			}
                    			/**fin de poner estado cero**/
                    			foreach ($serieReal  as $alm2 => $arrAlmacen2) {
                    				if($alm2==$codigoAlmacenProducto){
                    					foreach ($arrAlmacen2  as $ind2 => $arrserie2) {
		                    				if ($ind2 == $producto_id) {
		                    						foreach ($arrserie2 as $i => $serie) {
		                    							$filterSerie= new stdClass();
		                    							/**INSERTAMOS EN SERIE**/
		                    							if($tipo_oper == 'C'){
			                    							$filterSerie->PROD_Codigo=$producto_id;
			                    							$filterSerie->SERIC_Numero=$serie->serieNumero;
			                    							if($serie->serieCodigo!=null && $serie->serieCodigo!=0)
			                    								$filterSerie->SERIC_FechaModificacion=date("Y-m-d H:i:s");
			                    							else
			                    								$filterSerie->SERIC_FechaRegistro=date("Y-m-d H:i:s");
			                    							
			                    							$filterSerie->SERIC_FlagEstado='1';
			                    							if($serie->serieCodigo!=null && $serie->serieCodigo!=0){
			                    								$this->serie_model->modificar($serie->serieCodigo,$filterSerie);
			                    								$filterSerieD= new stdClass();
			                    								$filterSerieD->SERDOC_FlagEstado='1';
			                    								$this->seriedocumento_model->modificar($serie->serieDocumentoCodigo,$filterSerieD);
			                    							}else{
			                    								$filterSerie->ALMAP_Codigo=$codigoAlmacenProducto;
			                    								$codigoSerie=$this->serie_model->insertar($filterSerie);
			                    								/**insertamso serie documento**/
			                    								/**DOCUMENTO COMPROBANTE**/
			                    								$filterSerieD= new stdClass();
			                    								$filterSerieD->SERDOC_Codigo=null;
			                    								$filterSerieD->SERIP_Codigo=$codigoSerie;
			                    								$filterSerieD->DOCUP_Codigo=$tipo;
			                    								$filterSerieD->SERDOC_NumeroRef=$codigo;
			                    								/**1:ingreso**/
			                    								$filterSerieD->TIPOMOV_Tipo=1;
			                    								$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
			                    								$filterSerieD->SERDOC_FlagEstado='1';
			                    								$this->seriedocumento_model->insertar($filterSerieD);
			                    								/**FIN DE INSERTAR EN SERIE**/
			                    							}
		                    							}
		                    							/**FIN DE INSERTAR EN SERIE**/
		                    							/**ACTUALIZAMOS  EN SERIE  CON EL DOCUMENTO Y NUMERO DE REFERENCIA**/
		                    							if($tipo_oper=='V'){
		                    								if($serie->serieDocumentoCodigo!=null && $serie->serieDocumentoCodigo!=0){
		                    									$filterSerie->SERDOC_FlagEstado='1';
		                    									$this->seriedocumento_model->modificar($serie->serieDocumentoCodigo,$filterSerie);
		                    								}else{
		                    									/**insertamso serie documento**/
		                    									/**DOCUMENTO COMPROBANTE**/
		                    									$filterSerieD= new stdClass();
		                    									$filterSerieD->SERDOC_Codigo=null;
		                    									$filterSerieD->SERIP_Codigo=$serie->serieCodigo;
		                    									$filterSerieD->DOCUP_Codigo=$tipo;
		                    									$filterSerieD->SERDOC_NumeroRef=$codigo;
		                    									/**1:ingreso**/
		                    									$filterSerieD->TIPOMOV_Tipo=2;
		                    									$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
		                    									$filterSerieD->SERDOC_FlagEstado='1';
		                    									$this->seriedocumento_model->insertar($filterSerieD);
		                    									/**FIN DE INSERTAR EN SERIE**/
		                    								}
		                    								/**los registros en estadoSeleccion 1:seleccionado**/
		                    								$this->almacenproductoserie_model->seleccionarSerieBD($serie->serieCodigo,1);
		                    							}
		                    						}
		                    					break;
		                    				}
                    					}	
                    				}
                    			}
                    			
                    			//if($estado=='2'){
                    				if($tipo_oper == 'C'){
                    				/**eliminamos los registros en estado cero**/
                    				$this->seriedocumento_model->eliminarEstadoDocumentoSerie($tipo,$codigo);
                    				}
                    				
                    				if($tipo_oper == 'V'){
                    					/**eliminamos los registros en estado cero solo de serieDocumento**/
                    					$this->seriedocumento_model->eliminarDocumento($codigo,$tipo);
                    				}
                    				
                    			//}
                    			
                    		}
                    	}
                    }
                    /**fin de insertar serie**/
                    
                    
                } elseif ($detalle_accion == 'e') {

                    if(!isset($pedir[$indice]) && !is_null($codovente) && $codovente != ''){

                        $this->ocompra_model->modificar_pendiente_cantidad_comprobante($codovente, $prodcantidad[$indice]);
                    }
                    //$this->ocompra_model->modificar_pendiente_cantidad_comprobante($codovente,$pendientef);             
                    /**gcbq insertar serie de cada producto**/
                    if($flagGenInd[$indice]='I'){
                    	/***pongo todos en estado cero de las series asociadas a ese producto**/
                    			$seriesProductoBD=$this->session->userdata('serieRealBD');
                    			$serieBD = $seriesProductoBD;
                    			if($serieBD!=null && count($serieBD)>0){
                    				foreach ($serieBD as $alm1BD => $arrAlmaBD) {
                    					if($alm1BD ==$codigoAlmacenProducto){
                    						foreach ($arrAlmaBD as $ind1BD => $arrserieBD) {
		                    					if ($ind1BD == $producto_id) {
		                    							foreach ($arrserieBD as $keyBD => $valueBD) {
		                    								$serieCodigo=$valueBD->SERIP_Codigo;
		                    								/**cambiamos a ewstado 0**/
		                    								$filterSerie= new stdClass();
		                    								
		                    								/**SI ES COMPRA SE MODIFICA EL ESTADO***/
		                    								if($tipo_oper == 'C'){
		                    									$filterSerie->SERIC_FlagEstado='0';
		                    									$this->serie_model->modificar($serieCodigo,$filterSerie);
		                    								}
		                    							
		                    								/**si es venta solamente cambia de estado seridocumento**/	
		                    								$filterSerieD= new stdClass();
		                    								$filterSerieD->SERDOC_FlagEstado='0';
		                    								$this->seriedocumento_model->modificar($valueBD->SERDOC_Codigo,$filterSerieD);
		                    								
		                    								/**TIPO OPERACION VENTA SE DESHABILITAN LAS SERIES SELECCIONADAS POR EL COMPROBANTE**/
		                    								if($tipo_oper == 'V'){
		                    									/**eliminamos los registros en estadoSeleccion cero:0:desleccionado**/
		                    									$this->almacenproductoserie_model->seleccionarSerieBD($serieCodigo,0);
		                    								}
		                    								/**FIN DE DESELECCIONAR***/
		                    							}
		                    					}
                    						}
                    					}
                    				}
                    				
                    				//if($estado=='2'){
                    					if($tipo_oper == 'C'){
                    						/**eliminamos los registros en estado cero**/
                    						$this->seriedocumento_model->eliminarEstadoDocumentoSerie($tipo,$codigo);
                    					}
                    					if($tipo_oper == 'V'){
                    						/**eliminamos los registros en estado cero solo de serieDocumento**/
                    						$this->seriedocumento_model->eliminarDocumento($codigo,$tipo);
                    					}
                    				//}
                    				
                    				
                    				
                    			}
                    			/**fin de poner estado cero**/
                    }
                    
                    if($estado==2){
                    	$this->comprobantedetalle_model->eliminar($valor);
                        if(/*$tipo_oper == 'C'  &&*/ !is_null($codovente) && $codovente != ''  ){
                            //$this->ocompra_model->modificar_pendiente_cantidad_comprobante($codovente,$filter->CPDEC_Cantidad); 
                        }
                    }else{
                    	$objetoM=new stdClass();
                    	$objetoM->CPDEC_FlagEstado=0;
                    	$this->comprobantedetalle_model->modificar($valor,$objetoM);
                    }
                    
                }
                
            }
        }
        
        /**ingreso de modificacion comprobante en los diferentes movimientos 
         * verifica si alguna guia de remision lo contiene y lo modifica segun el comprobante
         * **/
        if($codigo!=null &&  $codigo!=0 && $estado==1){
        	if($this->db->query("CALL COMPROBANTE_GUIAREM_MODIFICAR($codigo)")){
                $json = array("result" => true, "codigo" => $codigo);
                echo json_encode($json);
                exit();
        	}else{
                $json = array("result" => false, "campo" => "consulte con el administrador");
                echo json_encode($json);
                exit();
        	}
        }

        $tipo_tributo = "";

        /**finde modificacion**/
        $json = array("result" => true, "codigo" => $codigo);
        echo json_encode($json);
    }

    public function comprobante_eliminar()
    {
        
        $comprobante = $this->input->post('comprobante');
        $usercod = $this->usuario;
        $eliminar = $this->EliminarComprobanteNubefact($comprobante);

        if ($eliminar == true)
            $this->comprobante_model->eliminar_comprobante($comprobante,$usercod);
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

    public function obtener_lista_detalles($codigo){
        $detalle = $this->comprobantedetalle_model->detalles($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $nombre_unidad = ($valor->UNDMED_Simbolo != "") ? $valor->UNDMED_Simbolo : "ZZ";
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
                $flagBS = $valor->PROD_FlagBienServicio;
                $GenInd = $valor->CPDEC_GenInd;
                $costo = $valor->CPDEC_Costo;
                $almacenProducto = $valor->ALMAP_Codigo;
                $codigoGuiaremAsociadaDetalle = $valor->GUIAREMP_Codigo;
                $codigovc = $valor->OCOMP_Codigo_VC;
                $flagBolsa = $valor->CPDEC_ITEMS;

                $nombre_producto = ($valor->CPDEC_Descripcion != '') ? $valor->CPDEC_Descripcion : $valor->PROD_Nombre;
                $codigo_interno = $valor->PROD_CodigoUsuario;
                $codigo_usuario = $valor->PROD_CodigoUsuario;
                $codigo_original = $valor->PROD_CodigoOriginal;

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
                $objeto->CPDEC_ITEMS = $flagBolsa;

                $objeto->MARCC_CodigoUsuario = $valor->MARCC_CodigoUsuario;
                $objeto->MARCC_Descripcion = $valor->MARCC_Descripcion;
                $objeto->LOTC_Numero = $valor->LOTC_Numero;
                $objeto->LOTC_FechaVencimiento = $valor->LOTC_FechaVencimiento;
                
                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }

    public function comprobante_pdf_a4_rango($inicio = 1, $fin = 20, $oper = "V", $docu = "V", $flagPdf = 1){
        $this->lib_props->comprobante_pdf_a4_rango($inicio, $fin, $oper, $docu);
    }

    public function comprobante_descarga_excel($id){
        $this->lib_props->comprobante_descarga_excel($id);
    }

    public function comprobante_garantia($id){
        $this->lib_props->carta_de_garantia($id);
    }

    public function comprobante_ver_pdf($codigo, $tipo_docu = 'TICKET'){
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                if ($tipo_docu != 'TICKET')
                    $this->lib_props->comprobante_pdf_a4($codigo);
                    #$this->comprobante_ver_pdf_a4($codigo);
                else{
                    $this->lib_props->comprobante_pdf_ticket($codigo);
                    #$this->comprobante_ver_pdf_ticket($codigo);
                }
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
                $this->comprobante_ver_pdf_formato1($codigo);
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

    /**
     * TODO - LA IMPRESION EXISTE UNA RESTRICCION SI ES COMPAÑIA 3 REALIZA OTRA ACCIONES DIFERENTE (Pregunta a Israel)
     * Tener en cuenta que este formato es utilizado por varios, como en compra o venta de facturas o boletas o comprobantes o Pedidos
     * @param $tipo_oper
     * @param $codigo
     * @param string $tipo_docu
     * @param string $img
     */

    /* Auxiliares */

    public function obtener_tipo_documento($tipo)
    {
        $tiponom = '';
        switch ($tipo) {
            case 'F':
                $tiponom = 'factura';
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
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->compania, $codtipodocu);

        if (count($datos_configuracion) > 0) {
            $data['serie'] = $datos_configuracion[0]->CONFIC_Serie;
            $data['numero'] = $datos_configuracion[0]->CONFIC_Numero + 1;
        }
        return $data;
    }

    public function reportes(){
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
        $data['f_fechai'] = form_input(array("name" => "f_fechai", "id" => "f_fechai", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
        $data['f_fechaf'] = form_input(array("name" => "f_fechaf", "id" => "f_fechaf", "class" => "cajaPequena", "readonly" => "readonly", "maxlength" => "10", "value" => ""));
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

        $usuario = $this->usuario_model->obtener($this->usuario);
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

        $confi = $this->configuracion_model->obtener_configuracion($this->compania);
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

    public function ver_reporte_pdf_factura($params,$tipo_oper)
    {
        $temp = (explode('_', $params));
        $fechai = $temp[0];
        $fechaf = $temp[1];
        $cliente = $temp[2];
        $producto = $temp[3];
        $aprobado = $temp[4];
        $ingreso = $temp[5];

        $usuario = $this->usuario_model->obtener($this->usuario);

        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
        $listado = $this->comprobante_model->buscar_factura_venta($fechai, $fechaf, $cliente, $aprobado, $ingreso,$tipo_oper);

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

        $confi = $this->configuracion_model->obtener_configuracion($this->compania);
        $serie = '';
        foreach ($confi as $key => $value) {
            if ($value->DOCUP_Codigo == 15) {
                $serie = $value->CONFIC_Serie;
            }
        }

        /* Listado */
        $cont = 1;
        foreach ($listado as $indice => $valor) {

            $comprobante = $this->comprobante_model->estado_pago_factura($valor->CPP_Codigo);
            if (count($comprobante)>0) {
                if($ingreso == '0'){
                    if ($comprobante[0]->CUE_FlagEstadoPago == 'V' || $comprobante[0]->CUE_FlagEstadoPago == 'A') {
                        $db_data[] = array(
                        'col1' => $cont,
                        'col2' => $valor->fecha,
                        'col3' => $serie,
                        'col4' => $valor->CPC_Numero,
                        'col6' => $valor->nombre,
                        'col7' => $valor->MONED_Simbolo . ' ' . number_format($valor->CPC_total, 2),
                        'col8' => "Pendiente"
                        );
                        $cont++;
                    }
                }else{
                    if ($comprobante[0]->CUE_FlagEstadoPago == 'C') {
                        $db_data[] = array(
                        'col1' => $cont,
                        'col2' => $valor->fecha,
                        'col3' => $serie,
                        'col4' => $valor->CPC_Numero,
                        'col6' => $valor->nombre,
                        'col7' => $valor->MONED_Simbolo . ' ' . number_format($valor->CPC_total, 2),
                        'col8' => "Pendiente"
                        );
                        $cont++;
                    }

                }
            }
            
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'SERIE',
            'col4' => 'NRO',
            'col6' => 'RAZON SOCIAL',
            'col7' => 'TOTAL',
            'col8' => 'EST. PAGO',
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
                'col6' => array('width' => 200),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 50, 'justification' => 'center')
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
 



        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');


        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_venta_3($anio ,$mes ,$fech1 ,$fech2 ,$tipodocumento);

        $confi = $this->configuracion_model->obtener_configuracion($this->compania);
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

        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');      

        $titulo="Reporte de compras al: ".$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');

        $compania = $this->compania;
        $img = 'images/img_db/menbrete'.$compania.'.jpg';

        $this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img));
        $this->cezpdf->ezSetCmMargins(5,4,1.5,1.5);
        $this->cezpdf->ezStartPageNumbers(60, 40, 10, 'left', '', 1);

        $this->cezpdf->ezText("", 7, array("leading" => 10, "left" => 5));
        $this->cezpdf->ezText($titulo ."  ", 17, array("leading" => 5, "justification" => "center", "left" => 0));        
        $this->cezpdf->ezText("", 7, array("leading" => 10, "left" => 5));

        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_compras($anio);

        $confi = $this->configuracion_model->obtener_configuracion($this->compania);
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
                'col5' => $value->MONED_Simbolo."  ".$value->CPC_subtotal,
                'col6' => $value->MONED_Simbolo."  ".$value->CPC_igv,
                'col7' => $value->MONED_Simbolo."  ".$value->CPC_total
            );
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
            'col3' => 'SERIE',
            'col4' => 'NRO',
            'col5' => 'VALOR DE VENTA',
            'col6' => 'I.G.V. 18%',
            'col7' => 'TOTAL',
        );

          $sum = 'S/ '.number_format($sum, 2);

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 555,
            'showLines' => 2,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 7,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 50, 'justification' => 'center'),
                'col3' => array('width' => 50, 'justification' => 'center'),
                'col4' => array('width' => 50, 'justification' => 'center'),
                'col5' => array('width' => 80, 'justification' => 'right'),
                'col6' => array('width' => 80, 'justification' => 'right'),
                'col7' => array('width' => 80, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText(" ", 7, array("left" => 0));
        $this->cezpdf->ezText(('TOTAL  '.$sum), 9, array("justification" => "right", "right" => 50));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function estadisticas_compras_ventas($tipo, $anio){
        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');

        $compania = $this->compania;
        $img = 'images/img_db/menbrete'.$compania.'.jpg';

        $this->cezpdf = new Cezpdf('a4', 'landscape');
        //$this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img));
        $this->cezpdf->ezSetCmMargins(2,2,1,1);
        $this->cezpdf->ezStartPageNumbers(60, 40, 10, 'left', '', 1);

        /* Cabecera */
        $delta = 20;
        $r = '';
        if ($tipo == "C") {
            $r = ' COMPRAS';
        } else {
            $r = ' VENTAS';
        }
        $options = array("leading" => 15, "left" => 0);
        $this->cezpdf->ezText('USUARIO:  ' . $persona[0]->PERSC_Nombre . ' ' . $persona[0]->PERSC_ApellidoPaterno . ' ' . $persona[0]->PERSC_ApellidoMaterno . '       FECHA: ' . $fechahoy, 9, $options);
        $this->cezpdf->ezText("", '', $options);
        $this->cezpdf->ezText('ESTADISTICAS DE' . $r . ' ANUALES', 17, $options);
        $this->cezpdf->ezText('', '', $options);

        //$listado = $this->comprobante_model->buscar_comprobante_compras();
        $listado = $this->comprobante_model->estadisticas_compras_ventas($tipo, $anio);

        $confi = $this->configuracion_model->obtener_configuracion($this->compania);
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
            'showLines' => 2,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 7,
            'cols' => array(
                'col1' => array('width' => 180, 'justification' => 'left'),
                'col2' => array('width' => 50, 'justification' => 'center'),
                'col3' => array('width' => 50, 'justification' => 'center'),
                'col4' => array('width' => 50, 'justification' => 'center'),
                'col5' => array('width' => 50, 'justification' => 'center'),
                'col6' => array('width' => 50, 'justification' => 'center'),
                'col7' => array('width' => 50, 'justification' => 'center'),
                'col8' => array('width' => 50, 'justification' => 'center'),
                'col9' => array('width' => 50, 'justification' => 'center'),
                'col10' => array('width' => 50, 'justification' => 'center'),
                'col11' => array('width' => 50, 'justification' => 'center'),
                'col12' => array('width' => 50, 'justification' => 'center'),
                'col13' => array('width' => 50, 'justification' => 'center')
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


    public function estadisticas_compras_ventas_mensual_excel($tipo, $anio, $mes){
        $tipoE = ($tipo == 'C') ? 'COMPRAS' : 'VENTAS';

        $listado = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        $listadoNotas = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        echo '<script type="text/javascript" src="' . base_url() . 'js/ventas/reporteexcel.js"></script>
		<h2 >ESTADISTICAS DE '.$tipoE.'</h2>
		<a href="javascript:;" onclick="tableToExcel()" ><img  style="margin:15px 0px;"  src="' . base_url() . 'images/xls.png" width="22" height="22" class="imgBoton" ></a>
		<table id="Table1" border="1" >
		<tr width="100%" bgcolor="#c1c1c1">
    		<td><b>MES</b></td>
            <td><b>FECHA</b></td>
    		<td><b>NOMBRE / RAZON ZOCIAL</b></td>
    		<td><b>DNI / RUC</b></td>
            <td><b>TIPO DOCUMENTO</b></td>
            <td><b>SERIE / NUMERO</b></td>
    		<td><b>MONEDA</b></td>
            <td><b>VALOR DE VENTA</b></td>
    		<td><b>IGV</b></td>
    		<td><b>VENTA</b></td>
            <td><b>ESTADO</b></td>
		</tr>';

        $totalFacturas = 0;
        $totalBoletas = 0;
        $totalComprobantes = 0;
        $totalNotasCredito = 0;
        $totalNotasDebito = 0;

        foreach ($listado as $key => $value) {
            $tdoc = "";
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

            switch ($value->CPC_TipoDocumento) {
                case 'F':
                    $totalFacturas += $value->monto;
                    $tdoc = "FACTURA";
                    break;
                case 'B':
                    $totalBoletas += $value->monto;
                    $tdoc = "BOLETA";
                    break;
                case 'N':
                    $totalComprobantes += $value->monto;
                    $tdoc = "COMPROB.";
                    break;
            }

            switch ($value->CPC_FlagEstado) {
                case '0':
                    $estado = "ANULADO";
                    break;
                case '1':
                    $estado = "APROBADO";
                    break;
                
                default:
                    $estado = "";
                    break;
            }

            if ($value->CPC_FlagEstado == 0)
                echo '<tr style="color:red">';
            else
                echo '<tr style="color:black">';

            echo ' <td>' . $this->meses($value->mes) . '</td>';
            echo ' <td>' . mysql_to_human($value->CPC_Fecha) . '</td>';
            echo ' <td>' . $datos_generales . '</td>';
            echo ' <td>' . $ruc_dni . '</td>';
            echo ' <td>' . $tdoc . '</td>';
            echo ' <td>' . $value->CPC_Serie.' - 00'.$value->CPC_Numero. '</td>';
            echo ' <td>' . $value->MONED_Simbolo . '</td>';
            echo ' <td>' . $value->CPC_subtotal . '</td>';
            echo ' <td>' . $value->CPC_igv . '</td>';
            echo ' <td>' . $value->monto . '</td>';
            echo ' <td>' . $estado . '</td>';
            echo ' </tr>';
        }

        $this->load->model('ventas/notacredito_model');
        $listadoNotas = $this->notacredito_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);

        foreach ($listadoNotas as $key => $value) {
            $tdoc = '';

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

            switch ($value->CRED_TipoNota) {
                case 'C':
                    $totalNotasCredito += $value->monto;
                    $tdoc = "N. CREDITO";
                    break;
                case 'D':
                    $totalNotasDebito += $value->monto;
                    $tdoc = "N. DEBITO";
                    break;
            }

            switch ($value->CRED_FlagEstado) {
                case '0':
                    $estado = "ANULADO";
                    break;
                case '1':
                    $estado = "APROBADO";
                    break;
                
                default:
                    $estado = "";
                    break;
            }

            if ($value->CRED_FlagEstado == 0)
                echo '<tr style="color:red">';
            else
                echo '<tr style="color:black">';
            
            echo ' <td>' . $this->meses($value->mes) . '</td>';
            echo ' <td>' . mysql_to_human($value->CRED_Fecha) . '</td>';
            echo ' <td>' . $datos_generales . '</td>';
            echo ' <td>' . $ruc_dni . '</td>';
            echo ' <td>' . $tdoc . '</td>';
            echo ' <td>' . $value->CRED_Serie.' - 000'.$value->CRED_Numero. '</td>';
            echo ' <td>' . $value->MONED_Simbolo . '</td>';
            echo ' <td>' . $value->CRED_subtotal . '</td>';
            echo ' <td>' . $value->CRED_igv . '</td>';
            echo ' <td>' . $value->monto . '</td>';
            echo ' <td>' . $estado . '</td>';
            echo ' </tr>';
        }

        $totalesFBC = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes, true);
        $totalesN = $this->notacredito_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes, true);
        
        foreach ($totalesFBC as $key => $value) {
            switch ($value->CPC_TipoDocumento) {
                case 'F':
                    $tdoc = "FACTURAS";
                    break;
                case 'B':
                    $tdoc = "BOLETAS";
                    break;
                case 'N':
                    $tdoc = "COMPROBANTES";
                    break;
            }

            echo '<tr>
                    <td colspan="7"></td>
                    <td>'.$tdoc.' EN</td>
                    <td>'.$value->MONED_Simbolo.'</td>
                    <td >'.number_format($value->total,2).'</td>
                </tr>';
        }

        foreach ($totalesN as $key => $value) {
            switch ($value->CRED_TipoNota) {
                case 'C':
                    $tdoc = "NOTAS DE CREDITO";
                    break;
                case 'D':
                    $tdoc = "NOTAS DE DEBITO";
                    break;
            }

            echo '<tr>
                    <td colspan="7"></td>
                    <td>'.$tdoc.' EN</td>
                    <td>'.$value->MONED_Simbolo.'</td>
                    <td>'.number_format($value->total,2).'</td>
                </tr>';
        }

        echo '</table>';

    }

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes){
        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $mesLetras = $this->lib_props->mesesEs(date('m'));
        
        $fechahoy = date('d'). " DE $mesLetras DEL " . date('Y');

        $listado = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        

        $this->load->model('ventas/notacredito_model');
        $listadoNotas = $this->notacredito_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes);
        
        $r = '';
        if ($tipo == "C") {
            $r = ' COMPRAS';
        } else {
            $r = ' VENTAS';
        }

        $cabeceraHTML = '<table>
                            <tr>
                                <td style="font-weight:bold; font-size:12pt">ESTADISTICAS DE '.$r.'</td>
                            </tr>
                            <tr>
                                <td style="font-size:9pt">FECHA DE EMISIÓN: '.$fechahoy.'</td>
                            </tr>
                         </table>';

        $datos_generales = '';
        $ruc_dni = '';
        $totalValorVenta = 0;
        $totalIGV = 0;
        $totalGeneral = 0;

        $totalFacturas = 0;
        $totalBoletas = 0;
        $totalComprobantes = 0;
        $totalNotasCredito = 0;
        $totalNotasDebito = 0;

        $detallesHTML = '<table cellpadding="0.05cm">
                            <tr bgcolor="#D1D1D1">
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:left; width:2cm;">MES</th>
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:left; width:2cm;">FECHA</th>
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:left; width:7cm;">NOMBRE / RAZON SOCIAL</th>
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:center; width:2.5cm;">DNI / RUC</th>
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:left; width:3.5cm;">TIPO DOCUMENTO</th>
                                <th style="border:1px #000 solid;font-weight:bold; font-size:10pt; text-align:right; width:2cm;">TOTAL</th>
                            </tr>';

        foreach ($listado as $key => $value) {
            $tdoc = '';

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

            switch ($value->CPC_TipoDocumento) {
                case 'F':
                    $totalFacturas += $value->monto;
                    $tdoc = "FACTURA";
                    break;
                case 'B':
                    $totalBoletas += $value->monto;
                    $tdoc = "BOLETA";
                    break;
                case 'N':
                    $totalComprobantes += $value->monto;
                    $tdoc = "COMPROB.";
                    break;
            }

            $detallesHTML .= '<tr>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$this->meses($value->mes).'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.mysql_to_human($value->CPC_Fecha).'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$datos_generales.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:center;">'.$ruc_dni.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$tdoc.' '.$value->CPC_Serie.' - '.$value->CPC_Numero.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:right;">'.$value->MONED_Simbolo.' '.$value->monto.'</td>
                            </tr>';
        }

        foreach ($listadoNotas as $key => $value) {
            $tdoc = '';

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

            switch ($value->CRED_TipoNota) {
                case 'C':
                    $totalNotasCredito += $value->monto;
                    $tdoc = "N. CREDITO";
                    break;
                case 'D':
                    $totalNotasDebito += $value->monto;
                    $tdoc = "N. DEBITO";
                    break;
            }

            $detallesHTML .= '<tr>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$this->meses($value->mes).'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.mysql_to_human($value->CRED_Fecha).'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$datos_generales.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:center;">'.$ruc_dni.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:left;">'.$tdoc.' '.$value->CRED_Serie.' - '.$value->CRED_Numero.'</td>
                                <td style="border:1px #000 solid; font-size:8pt; text-align:right;">'.$value->monto.'</td>
                            </tr>';
        }

        $detallesHTML .= "</table>";
        $totalesFBC = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes, true);
        $totalesN = $this->notacredito_model->estadisticas_compras_ventas_mensual($tipo, $anio, $mes, true);
        
        $totalesHTML = '<table cellpadding="0.05cm">';

        foreach ($totalesFBC as $key => $value) {
            switch ($value->CPC_TipoDocumento) {
                case 'F':
                    $tdoc = "FACTURAS";
                    break;
                case 'B':
                    $tdoc = "BOLETAS";
                    break;
                case 'N':
                    $tdoc = "COMPROBANTES";
                    break;
            }

            $totalesHTML .= '<tr>
                                <td style="width:12.5cm;"></td>
                                <td style="border-bottom:1px #000 solid; width:3.5cm; text-align:right; font-size:8pt; font-weight:bold;">'.$tdoc.' EN</td>
                                <td style="border-bottom:1px #000 solid; width:1cm; text-align:right; font-size:8pt; font-weight:bold;">'.$value->MONED_Simbolo.'</td>
                                <td style="border-bottom:1px #000 solid; width:2cm; text-align:right; font-size:8pt;">'.number_format($value->total,2).'</td>
                            </tr>';
        }

        foreach ($totalesN as $key => $value) {
            switch ($value->CRED_TipoNota) {
                case 'C':
                    $tdoc = "NOTAS DE CREDITO";
                    break;
                case 'D':
                    $tdoc = "NOTAS DE DEBITO";
                    break;
            }

            $totalesHTML .= '<tr>
                                <td style="width:12.5cm;"></td>
                                <td style="border-bottom:1px #000 solid; width:3.5cm; text-align:right; font-size:8pt; font-weight:bold;">'.$tdoc.' EN</td>
                                <td style="border-bottom:1px #000 solid; width:1cm; text-align:right; font-size:8pt; font-weight:bold;">'.$value->MONED_Simbolo.'</td>
                                <td style="border-bottom:1px #000 solid; width:2cm; text-align:right; font-size:8pt;">'.number_format($value->total,2).'</td>
                            </tr>';
        }
        
        $totalesHTML .= '</table>';


        $this->load->library("tcpdf");
        $medidas = "a4"; // a4 - carta
        $this->pdf = new pdfCotizacion('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(10, 55, 10); // Cada 10 es 1cm - Como es hoja estoy tratando las medidad en cm -> 
        $this->pdf->SetTitle('REPORTE DE ');
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->setPrintHeader(true);
        $this->pdf->SetAutoPageBreak(true, 25);
        $this->pdf->AddPage();
        
        $this->pdf->writeHTML($cabeceraHTML,true,false,true,'');        
        $this->pdf->writeHTML($detallesHTML,true,false,true,'');        
        $this->pdf->writeHTML($totalesHTML,true,false,true,'');

        $this->pdf->Output('Reporte.pdf', 'I');
    }

    public function obtener_tipo_de_cambio($fecha_comprobante)
    {
        return $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);
    }

    public function ventana_muestra_comprobante($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = ''){
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

        #$lista_comprobante = $this->comprobante_model->buscar_comprobantes_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        /*foreach ($lista_comprobante as $indice => $value) {
            $pdfImprimir = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_comprobante(" . $value->CPP_Codigo . " ," . $value->CPC_Serie . "," . $value->CPC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->CPC_Fecha), $value->CPC_Serie, $value->CPC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->CPC_total), $pdfImprimir, $select);
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
        $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));

        $this->load->view('ventas/ventana_muestra_comprobante', $data);
    }

    public function datatable_muestra_comprobante() {
        $data['compania'] = $this->compania;

        $columnas = array(
                            0 => "CPC_Fecha",
                            1 => "CPC_Serie",
                            2 => "CPC_Numero"
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

        $lista_comprobante = $this->comprobante_model->getComprobantesAsoc($filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(\"$value->CPP_Codigo\")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = "<a href='javascript:;' onclick=seleccionar_comprobante('$value->CPP_Codigo') ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(
                                0 => $value->CPC_Fecha,
                                1 => $value->CPC_Serie,
                                2 => $value->CPC_Numero,
                                3 => $value->numdoc,
                                4 => $value->nombre,
                                5 => $value->MONED_Simbolo . ' ' . number_format($value->CPC_total),
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
                            "recordsTotal"    => count($this->comprobante_model->getComprobantesAsoc($filterAll)),
                            "recordsFiltered" => intval( count($this->comprobante_model->getComprobantesAsoc($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
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
            $pdfImprimir = "<a href='javascript:;' onclick='ver_detalle_documento_recu(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $ir = "<a href='javascript:;' onclick='seleccionar_comprobante_recu(" . $value->CPP_Codigo . "," . $value->CPC_Serie . "," . $value->CPC_Numero . ")' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Guia de remision " . $value->GUIAREMC_Serie . " - " . $value->GUIAREMC_Numero . "' /></a>";
            $lista[] = array(mysql_to_human($value->GUIAREMC_Fecha), $value->CPC_Serie, $value->CPC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->CPC_total), $pdfImprimir, $ir);

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

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);

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
        $data['url_action'] = base_url() . "index.php/ventas/comprobante/comprobante_insertar";
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
    public function ver_comprobantes_x_orden_producto($tipo_orden, $tipo_guia, $cod_orden, $cod_prod){
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
           $titulo="REPORTE DE COMPRAS ";

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
         $this->compania;
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
                            $fecha = $valor->CPC_Fecha;
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
        }
        else{
            $resultado="0";
        }
        echo $resultado;
    }   
	
	public function ver_reporte_pdf_productos($anio ,$mes ,$fech1 ,$fech2, $tipodocumento, $Prodcod){
        $this->lib_props->ventas_pdf_productos($fech1, $fech2, $tipodocumento, $Prodcod);
    }

    public function productos_vendidos($fech1, $fech2){

        $columns = array(
                        0 => "PROD_CodigoUsuario",
                        1 => "PROD_Nombre",
                        2 => "MARCC_Descripcion",
                        3 => "cantidad_documentos",
                        4 => "cantidad_vendidos",
                        5 => "suma"
                    );

        $params = new stdClass();
        $params->search = $this->input->post("search")["value"];
        $params->limit = $this->input->post("start") . ", " . $this->input->post("length");
        $params->order .= ( $columns[$this->input->post("order")[0]["column"]] != "" && $columns[$this->input->post("order")[0]["dir"]] != "" ) ? $columns[$this->input->post("order")[0]["column"]] . ", " . $columns[$this->input->post("order")[0]["dir"]] : "$columns[1] ASC";

        $info = $this->comprobante_model->productos_vendidos($fech1, $fech2, $params);

        foreach ($info as $row => $col) {
            $data[$row] = array(
                                "codigo" => $col->PROD_CodigoUsuario,
                                "descripcion" => $col->PROD_Nombre,
                                "marca" => $col->MARCC_Descripcion,
                                "ndocumentos" => $col->cantidad_documentos,
                                "nvendidos" => $col->cantidad_vendidos,
                                "total" => $col->suma
                            );
        }
        $json = array(
                        "draw" => intval($this->input->post("draw")),
                        "recordsTotal" => count( $this->comprobante_model->productos_vendidos($fech1, $fech2, "") ),
                        "recordsFiltered" => count( $info ),
                        "data" => $data
                    );
        echo json_encode($json);
    }

    public function productos_vendidos_table($fech1, $fech2){

        $columns = array(
                        0 => "PROD_CodigoUsuario",
                        1 => "PROD_Nombre",
                        2 => "MARCC_Descripcion",
                        3 => "cantidad_documentos",
                        4 => "cantidad_vendidos",
                        5 => "suma"
                    );

        $params = new stdClass();
        $params->search = $this->input->post("search")["value"];
        $params->limit = $this->input->post("start") . ", " . $this->input->post("length");
        $params->order .= ( $columns[$this->input->post("order")[0]["column"]] != "" && $columns[$this->input->post("order")[0]["dir"]] != "" ) ? $columns[$this->input->post("order")[0]["column"]] . ", " . $columns[$this->input->post("order")[0]["dir"]] : "$columns[1] ASC";

        $info = $this->comprobante_model->productos_vendidos($fech1, $fech2, "");

        foreach ($info as $row => $col) {
            $data[$row] = array(
                                "codigo" => $col->PROD_CodigoUsuario,
                                "descripcion" => $col->PROD_Nombre,
                                "marca" => $col->MARCC_Descripcion,
                                "ndocumentos" => $col->cantidad_documentos,
                                "nvendidos" => $col->cantidad_vendidos,
                                "total" => $col->suma
                            );
        }
        $json = $data;
        echo json_encode($json);
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

    public function calcula_ocantidad_pendiente($ocodigo,$prod,$cant){
        $cant=$cant;
        $pdfImprimirificar_cantidad=$this->ocompra_model->calcula_ocantidad_pendiente_compro($ocodigo,$prod);
        $cantidad=$pdfImprimirificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$pdfImprimirificar_cantidad->OCOMDEC_Pendiente_pago;  //2

        if($cant<=$pendiente ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
           $cantidadt=$pendiente;
        }elseif($cant>=$pendiente){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$pendiente;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidadt));
    } 

    public function cantidad_oregistrada($oventa,$prod,$cant,$pend){
        $cant=$cant;
        $pend=$pend;
        $pdfImprimirificar_cantidad=$this->ocompra_model->verificar_ocantidad($oventa,$prod);
        $cantidad=$pdfImprimirificar_cantidad->OCOMDEC_Cantidad;  //4
        $pendiente=$pdfImprimirificar_cantidad->OCOMDEC_Pendiente_pago;  //2
       // $cant_impor=$pdfImprimirificar_cantidad->IMPORDEC_Cantidad; //2

        // $calcula=$pendiente+$pend;//
        if($cant<=$cantidad ){
           // echo "<script> alert(".$cant.");</script>";
           $estado=1;
           $cantidadt=$cantidad;
        }elseif($cant>=$cantidad){
           // echo "<script> alert('el N ingresado en mayor al N de Compra');</script>";
            $estado=0;
            $cantidadt=$cantidad;
        }
     echo json_encode(array("estado"=>$estado,"cantidad"=>$cantidad));
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

    ###############################################
    ## FUNCIONES PARA CANJE DE DOCUMENTO
    ## 01/01/2020
    ##############################################
    public function canje_documento(){
        
        $compania = $this->session->userdata('compania');
        $codigo_documento = $this->input->post("codigo");
        $data['titulo_tabla'] = 'CANJE DE COMPROBANTES';
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo_documento);
        
        if (!$datos_comprobante)
            die('ERROR DE DATOS COMPROBANTE');

        $tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
        if($tipo_oper =='V'){
            $datos_cliente = $this->cliente_model->obtener($datos_comprobante[0]->CLIP_Codigo);
            $codigo_cliente = $datos_cliente->cliente;
            $operacion="VENTA";
            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, 8);
            $serie = $cofiguracion_datos[0]->CONFIC_Serie;
            $numero = $this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
        }else{
            $datos_cliente = $this->proveedor_model->obtener($datos_comprobante[0]->PROVP_Codigo);
            $codigo_cliente = $datos_comprobante[0]->PROVP_Codigo;
            $operacion="COMPRA";
            $serie = "";
            $numero = "";
        }

        if (!$datos_cliente)
            die('ERROR DATOS CLIENTE');

        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $datos_moneda=$this->moneda_model->obtener($moneda);
        $filter = new stdClass();
        $filter->cliente = $datos_comprobante[0]->CLIP_Codigo;
        //$codigo_cliente = $datos_cliente->cliente;
        $nombre_cliente = $datos_cliente->nombre;
        $ruc_cliente = $datos_cliente->ruc;
        $direccion_cliente = $datos_cliente->direccion;
        $serie_numero = $datos_comprobante[0]->CPC_Serie."-".$datos_comprobante[0]->CPC_Numero;
        $total_comprobante = $datos_moneda[0]->MONED_Simbolo." ".$datos_comprobante[0]->CPC_total;
        $fecha = $datos_comprobante[0]->CPC_Fecha;
        $docu_codigo = $datos_comprobante[0]->DOCUP_Codigo;
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $fecha2 =explode("-", $fecha);
        
        $result[] = array(
            "codigo_cliente"    => $codigo_cliente,
            "cod_comprobante"   => $codigo_documento,
            "titulo_tabla"      => 'CANJE DE COMPROBANTES',
            "moneda"            => $this->moneda_model->obtener($moneda), 
            "comprobantes"      => $this->comprobante_model->buscar_comprobantes('V', 'N', $filter),
            "codigo_cliente"    => $codigo_cliente,
            "nombre_cliente"    => $nombre_cliente,
            "ruc_cliente"       => $ruc_cliente,
            "direccion_cliente" => $direccion_cliente,
            "datos"             => $datos_comprobante,
            "numeroAutomatico"  => 1,
            "serie_suger_b"     => $serie, 
            "numero_suger_b"    => $numero,
            "tipo_operacion"    => $tipo_oper,
            "operacion"         => $operacion,
            "serie_numero"      => $serie_numero,
            "total_comprobante" => $total_comprobante,
            "fecha_comprobante" => $fecha
            
        ); 

        echo json_encode($result);
        //$this->load->view('ventas/ventana_canje', $data);
    }

    public function canjear_documento(){

        $compania = $this->session->userdata('compania');

        $cliente = $this->input->post('cod_cliente');
        $fecha = $this->input->post('fecha_comprobante');
        $comprobantes = $this->input->post('cod_comprobante');
        $tipo_docu = $this->input->post('cmbDocumento');
        $user = $this->session->userdata('user');
        $serie = $this->input->post('serie_suger_b');
        $numero =$this->input->post('numero_suger_b');
        $numeroAutomatico = $this->input->post('numeroAutomatico');
        $tipo_operacion = $this->input->post('tipo_operacion');
        $observaciones = $this->input->post('observaciones');

        
        $subtotal = $total / (1 + ($t_igv / 100));
        $igv = $total - $subtotal;

        $detalle = $this->comprobantedetalle_model->listar($comprobantes);
        $datos = $this->comprobante_model->obtener_comprobante($comprobantes);

        if ($tipo_docu == 'F') {
            $tipo = 8;
        }
        if ($tipo_docu == 'B') {
            $tipo = 9;
        }

        if($tipo_operacion=="V"){
            $numeroAutomatico=1;
            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $serie = $cofiguracion_datos[0]->CONFIC_Serie;
            $numero = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        }

        $filter = new stdClass();
        $tipoOperacion = $datos[0]->CPC_TipoOperacion;
        $filter->CPC_TipoOperacion = $tipoOperacion;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->PRESUP_Codigo = $datos[0]->PRESUP_Codigo;
        $filter->OCOMP_Codigo = $datos[0]->OCOMP_Codigo;
        $filter->COMPP_Codigo = $datos[0]->COMPP_Codigo;
        $filter->CPC_Serie = $serie;
        $filter->CPC_Numero =$numero;
        
        if ($tipoOperacion=="V") {
            $filter->CLIP_Codigo = $cliente;
            $filter->PROVP_Codigo = $datos[0]->PROVP_Codigo;
        }else{
            $filter->CLIP_Codigo =$datos[0]->CLIP_Codigo;
            $filter->PROVP_Codigo = $cliente;
        }

        $filter->CPC_NombreAuxiliar = $datos[0]->CPC_NombreAuxiliar;
        $filter->USUA_Codigo = $user;
        $filter->MONED_Codigo = $datos[0]->MONED_Codigo;
        $filter->FORPAP_Codigo = $datos[0]->FORPAP_Codigo;
        $filter->CPC_igv100 = $datos[0]->CPC_igv100;
        $filter->CPC_total = $datos[0]->CPC_total;
        $filter->CPC_subtotal = $datos[0]->CPC_subtotal;
        $filter->CPC_descuento = $datos[0]->CPC_descuento;
        $filter->CPC_igv = $datos[0]->CPC_igv;
        $filter->CPC_subtotal_conigv = $datos[0]->CPC_subtotal_conigv;
        $filter->CPC_descuento_conigv = $datos[0]->CPC_descuento_conigv;
        $filter->CPC_descuento100 = $datos[0]->CPC_descuento100;
        $filter->GUIAREMP_Codigo = $datos[0]->GUIAREMP_Codigo;
        $filter->CPC_GuiaRemCodigo = $datos[0]->CPC_GuiaRemCodigo;
        $filter->CPC_DocuRefeCodigo = $datos[0]->CPP_Codigo;
        $filter->CPC_Observacion = $datos[0]->CPC_Observacion.' '.$observaciones;
        $filter->CPC_ModoImpresion = $datos[0]->CPC_ModoImpresion;
        $filter->CPC_Fecha = $fecha;
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
        $filter->CPC_NumeroAutomatico = $numeroAutomatico;
        $filter->CPP_Codigo_Canje = $comprobantes;

        $comprobante = $this->comprobante_model->insertar_comprobante2($filter);

        if( $numeroAutomatico == 1 ){
            $this->configuracion_model->modificar_configuracion($compania, $tipo, $numero);
        }


        $a_filter = new stdClass();
        $a_filter->CPP_Codigo_Canje = $comprobante;
        $this->comprobante_model->modificar_comprobante($comprobantes, $a_filter);



        foreach ($detalle as $key => $value) {
            $d_filter = new stdClass();
            $d_filter->CPP_Codigo               = $comprobante;
            $d_filter->PROD_Codigo              = $value->PROD_Codigo;
            $d_filter->LOTP_Codigo              = $value->LOTP_Codigo; #$lote;
            $d_filter->AFECT_Codigo             = $value->AFECT_Codigo; #$tafectacion;
            $d_filter->CPDEC_GenInd             = $value->CPDEC_GenInd;
            $d_filter->UNDMED_Codigo            = $value->UNDMED_Codigo;
            $d_filter->CPDEC_Cantidad           = $value->CPDEC_Cantidad;
            $d_filter->CPDEC_Pu                 = $value->CPDEC_Pu;
            $d_filter->CPDEC_Subtotal           = $value->CPDEC_Subtotal;
            $d_filter->CPDEC_Descuento          = $value->CPDEC_Descuento;
            $d_filter->CPDEC_Igv                = $value->CPDEC_Igv;
            $d_filter->CPDEC_Total              = $value->CPDEC_Total;
            $d_filter->CPDEC_Pu_ConIgv          = $value->CPDEC_Pu_ConIgv;
            $d_filter->CPDEC_Subtotal_ConIgv    = $value->CPDEC_Subtotal_ConIgv;
            $d_filter->CPDEC_Descuento_ConIgv   = $value->CPDEC_Descuento_ConIgv;
            $d_filter->CPDEC_Igv100             = $value->CPDEC_Igv100;
            $d_filter->CPDEC_Descuento100       = $value->CPDEC_Descuento100;
            $d_filter->CPDEC_Costo              = $value->CPDEC_Costo;
            $d_filter->CPDEC_Descripcion        = $value->CPDEC_Descripcion;
            $d_filter->CPDEC_Observacion        = $value->CPDEC_Observacion;
            $d_filter->CPDEC_FlagEstado         = $value->CPDEC_FlagEstado;
            $d_filter->ALMAP_Codigo             = $value->ALMAP_Codigo;
            $d_filter->GUIAREMP_Codigo          = 0;
        }   


        $this->comprobantedetalle_model->insertar($d_filter);
        $comprobanteInfo = $this->comprobante_model->obtener_comprobante($comprobante);
        $datos_comprobante = $comprobanteInfo[0];
        $enviado = $this->envioSunatFac($comprobante,$datos_comprobante);

        /**verificamos si tiene productos en serie y creamos la relacion con el nuevo documento**/
        $filterSerie=new stdClass();
        $filterSerie->SERIC_FlagEstado='1';
        /**comprobante general:14**/
        $filterSerie->DOCUP_Codigo = 14;
        $filterSerie->SERDOC_NumeroRef = $comprobantes[0];
        $listaSeriesDocumento=$this->seriedocumento_model->buscar($filterSerie,null,null);
        if(count($listaSeriesDocumento)>0){
            foreach ($listaSeriesDocumento as $valorSerie){

                /**insertamso serie documento**/
                $serieCodigo = $valorSerie->SERIP_Codigo;
                $almacen = $valorSerie->ALMAP_Codigo;
                $filterSerieD = new stdClass();
                $filterSerieD->SERDOC_Codigo = null;
                $filterSerieD->SERIP_Codigo = $serieCodigo;
                $filterSerieD->DOCUP_Codigo = $tipo;
                $filterSerieD->SERDOC_NumeroRef = $comprobante;
                $tipoIngreso = 2;
                $filterSerieD->TIPOMOV_Tipo = $tipoIngreso;
                $filterSerieD->SERDOC_FechaRegistro = date("Y-m-d H:i:s");
                $filterSerieD->SERDOC_FlagEstado = 1;
                $this->seriedocumento_model->insertar($filterSerieD);
            }
        }
        /**FIN DE INSERTAR EN SERIE**/

        if ($enviado == true)
            exit('{"result":"success", "serie":"' . $serie . '", "numero":"00' . $numero.'"}');
        else{
            $flagFilter = new stdClass();
            $flagFilter->CPC_FlagEstado = 2;
            $this->comprobante_model->modificar_comprobante($comprobante, $flagFilter);
            exit('{"result":"error", mensaje":"Error de comunicacion, debe ir a Factura/Boleta e intentar aprobar el documento nuevamente", "serie":"' . $serie . '", "numero":"00' . $numero.'"}');
        }
    }

    public function obtenerSerieNumero(){
        $compania  = $this->session->userdata('compania');
        $tipo_docu = $this->input->post("tipo_documento");

        if ($tipo_docu == 'F') {
            $tipo = 8;
        }
        if ($tipo_docu == 'B') {
            $tipo = 9;
        }

        $numeroAutomatico=1;
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        $serie = $cofiguracion_datos[0]->CONFIC_Serie;
        $numero = $cofiguracion_datos[0]->CONFIC_Numero + 1;

        $result[] = array(
            "serie"    => $serie,
            "numero"   => $numero
            
        ); 

        echo json_encode($result);        
    }

}
?>