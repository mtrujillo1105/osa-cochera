<?php
ini_set('error_reporting', 1);  //bloq stv  pa q al inicio no cargue con error el pdf en firefox

include("system/application/libraries/pchart/pData.php");
include("system/application/libraries/pchart/pChart.php");
include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");

class Letra extends CI_Controller {

    private $compania;
    private $usuario;
    private $base_url;

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');

        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('lib_props');

        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('ventas/comprobantedetalle_model');
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
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/tipocambio_model');

        $this->load->model('tesoreria/banco_model');
        $this->load->model('configuracion_model');
        $this->load->model('ventas/letra_model');

        $this->compania = $this->session->userdata("compania");
        $this->usuario = $this->session->userdata("user");
        $this->base_url = base_url();
    }

    public function index() {
        $this->load->view('seguridad/inicio');
        
    }

    public function cargar_listado_comprobantes($codigo_cliente) {

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

    public function cargar_comprobante($codigo_documento) {

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

    public function obtener_detalle_comprobante($comprobante, $tipo_oper = 'V', $almacen = "") {
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
       
    public function comprobantes($tipo_oper = '', $tipo_docu = '') {
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);

        

        $data['action'] = "index.php/ventas/letra/comprobantes/$tipo_oper/$tipo_docu";

        #$data['registros'] = count($this->letra_model->buscar_comprobantes($tipo_oper, $tipo_docu, $filter, NULL, '', ''));
        $conf['base_url'] = site_url('ventas/letra/comprobantes/' . $tipo_oper . '/' . $tipo_docu);

        #$ver_cuotas = "";

        $data['titulo_tabla'] = "RELACIÓN DE LETRAS";
        $data['titulo_busqueda'] = "BUSCAR LETRAS";
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = $tipo_docu;
        $data['monedas'] = $this->moneda_model->listar();
        $data['bancos'] = $this->banco_model->listar_banco();
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => $tipo_docu));

        $this->layout->view('ventas/letra_index', $data);
    }

    public function datatable_letras($tipo_oper){

        $columnas = array(
                            0 => "LET_Fecha",
                            1 => "LET_FechaVenc",
                            2 => "LET_Serie",
                            3 => "LET_Numero",
                            4 => "CLIC_CodigoUsuario",
                            5 => "",
                            6 => "",
                            7 => ""
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

        $filter->tipo_oper = $tipo_oper;
        $filter->serie = $this->input->post('seriel');
        $filter->numero = $this->input->post('numerol');
        $filter->seriec = $this->input->post('seriec');
        $filter->numeroc = $this->input->post('numeroc');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');
        $filter->nombre_proveedor = $this->input->post('nombre_proveedor');
        $filter->estado_pago = $this->input->post('estado_pago');

        $listado_comprobantes = $this->letra_model->getLetras($filter);
        $lista = array();
        
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                $modal = "<button type='button' onclick='ver_letra($valor->LET_Codigo)' class='btn btn-info'>Editar</button>";
                $general_pdf = "<button href='" . $this->base_url . "index.php/ventas/letra/letra_pdf/$valor->LET_Codigo' data-fancybox data-type='iframe' class='btn btn-info'>PDF</button>";

                if ($valor->LET_FlagEstado==1) {
                    $estado='<font style="color:green;">PAGADO</font>';
                }elseif($valor->LET_FlagEstado==0){
                    $estado='<font style="color:red;">PENDIENTE</font>';
                }
                
                $lista[] = array(
                                    0 => mysql_to_human($valor->LET_Fecha),
                                    1 => mysql_to_human($valor->LET_FechaVenc),
                                    2 => $valor->LET_Serie,
                                    3 => $this->lib_props->getOrderNumeroSerie($valor->LET_Numero),
                                    4 => $valor->numdoc,
                                    5 => $valor->nombre,
                                    6 => $estado,
                                    7 => $valor->MONED_Simbolo." ".number_format($valor->LET_Total,2),
                                    8 => $modal,
                                    9 => $general_pdf
                                );
            }
        }

        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->letra_model->getLetras($filterAll)),
                            "recordsFiltered" => intval( count($this->letra_model->getLetras($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function getLetra(){

        $filter = new stdClass();
        $filter->LET_Codigo = $this->input->post("letra");

        $letra = $this->letra_model->getLetra($filter);
        $lista = array();
        
        if ( $letra != NULL ){
            foreach ($letra as $indice => $val) {
                $lista = array(
                                    "letra" => $val->LET_Codigo,
                                    "documento" => $val->numdoc,
                                    "razon_social" => $val->nombre,
                                    "forma_pago" => $val->FORPAP_Codigo,
                                    "moneda" => $val->MONED_Codigo,
                                    "banco" => $val->BANP_Codigo,
                                    "titular" => $val->LET_Representante,
                                    "numero_cuenta" => $val->LET_NumeroCuenta,
                                    "fecha_pago" => $val->LET_Fecha,
                                    "fecha_vencimiento" => $val->LET_FechaVenc,
                                    "importe" => $val->LET_Total,
                                    "estado" => $val->LET_FlagEstado,
                                    "observacion" => $val->LET_Observacion
                                );
            }

            $json = array("match" => true, "info" => $lista);
        }
        else
            $json = array("match" => false, "info" => "");

        echo json_encode($json);
    }

    public function listar_comprobantes(){
        $columnas = array(
                            0 => "CPC_Fecha",
                            1 => "CPC_FechaVencimiento",
                            2 => "CPC_Serie",
                            3 => "CPC_Numero",
                            4 => "MONED_Simbolo",
                            5 => "CPC_Total",
                            6 => ""
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

        $filter->cliente = ( $this->input->post("cliente") != "" ) ? $this->input->post("cliente") : NULL;
        $filter->proveedor = ( $this->input->post("proveedor") != "") ? $this->input->post("proveedor") : NULL;
        $filter->moneda = ( $this->input->post("moneda") != "") ? $this->input->post("moneda") : NULL;

        if ($filter->cliente != "" || $filter->proveedor != ""){
            $info = $this->letra_model->getComprobantesCP($filter);
            if ($info != NULL){
                foreach ($info as $i => $val) {
                    $lista[] = array(
                                        0 => mysql_to_human($val->CPC_Fecha),
                                        1 => mysql_to_human($val->CPC_FechaVencimiento),
                                        2 => $val->CPC_Serie,
                                        3 => $this->lib_props->getOrderNumeroSerie($val->CPC_Numero),
                                        4 => $val->MONED_Simbolo,
                                        5 => "<span class='span_importe_$val->CPP_Codigo'>".number_format($val->CPC_total,2, ".", "")."</span>",
                                        6 => "<input type='checkbox' name='documentos[]' onclick='total_documentos()' class='documentos' value='$val->CPP_Codigo'>"
                                    );
                }
            }
            else
                $lista = array();
        }
        else
            $lista = array();

        unset($filter->start);
        unset($filter->length);

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->letra_model->getComprobantesCP()),
                            "recordsFiltered" => intval( count($filter) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function obtener_cuotas_comprobante($codigo = NULL){
        $codigo = $this->input->post("comprobante");
        $datos = $this->letra_model->lista_cuotas_comprobantes($codigo);
                
        $lista = array();
        if ($datos != NULL) {
            foreach ($datos as $key => $value) {
                $lista[] = array(
                    "codigo_cuota" => $value->CUOT_Codigo,
                    "descripcion"  => $this->validar_formato_serie_cuota($value->CUOT_Codigo),
                    "fechaiv"      => $value->CUOT_FechaInicio,
                    "fechafv"      => $value->CUOT_Fecha,
                    "fechai"       => mysql_to_human($value->CUOT_FechaInicio),
                    "fechaf"       => mysql_to_human($value->CUOT_Fecha),
                    "cuota"        => $value->CUOT_Monto,
                    "estado_pago"  => $value->CUOT_FlagPagado,
                    "num_banco"    => (is_null($value->CUOT_NumBanco)) ? "" : $value->CUOT_NumBanco
                );
            }
            
            $comprobante = array(
                                    "documento" => $datos[0]->CPC_TipoDocumento,
                                    "operacion" => $datos[0]->CPC_TipoOperacion,
                                    "serie" => $datos[0]->CPC_Serie,
                                    "numero" => $datos[0]->CPC_Numero,
                                    "total" => $datos[0]->CPC_total
                                );

            $json = array("match" => true, "info" => $lista, "comprobante" => $comprobante);
        }
        else
            $json = array("match" => false, "info" => "", "comprobante" => "");

        echo json_encode($json);
    }

    public function obtener_det_comprobante($codigo = NULL){
        $codigo = $this->input->post("comprobante");
        
        $datos = $this->letra_model->Comprobante_det($codigo);
        
        $lista = array();
        if (count($datos)> 0) {
            $lista[] = array(
                    "tipo_documento" => ($datos[0]->CPC_TipoDocumento == 'B') ? "BOLETA" : "FACTURA",
                    "numero"  => $datos[0]->CPC_Numero,
                    "total"   => $datos[0]->CPC_total,
                    "message" => 1
                );
            echo json_encode($lista);          
        }else{
            echo json_encode(array(
                "message" => 0
            ));
        }
    }

    public function generar_letras(){

        $cliente = $this->input->post("cliente");
        $proveedor = $this->input->post("proveedor");
        $operacion = $this->input->post("operacion");

        $forma_pago = $this->input->post("forma_pago");
        $moneda = $this->input->post("moneda");
        $banco = $this->input->post("banco");
        $titular = $this->input->post("titular");
        $cuenta = $this->input->post("cuenta");
        $documentos = $this->input->post("documentos");
        $fechaPago = $this->input->post("FechaPago");
        $fechaVencimiento = $this->input->post("FechaVencimiento");
        $importe = $this->input->post("importe");
        $estado_letra = $this->input->post("estado_letra");
        $observacion = $this->input->post("observacion");

        $cantidad_letras = $this->input->post("cantidad_letras");
        $importe_documentos = $this->input->post("importe_documentos");
        $importe_letras = $this->input->post("importe_letras");

        $tipo = 16; # DOCUMENTO LETRA = 16

        $cantDocumentos = count($documentos);

        for ($i=0; $i<$cantidad_letras; $i++){

            if ($operacion == "V"){
                $configuracion_datos = $this->configuracion_model->obtener_numero_documento($this->compania, $tipo);
                $serie = $configuracion_datos[0]->CONFIC_Serie;
                $numero = $configuracion_datos[0]->CONFIC_Numero + 1;
            }
            else{
                $serie = $this->input->post("serie")[$i];
                $numero = $this->input->post("numero")[$i];
            }

            $filter = new stdClass();
            $filter->LET_TipoOperacion = $operacion;
            $filter->COMPP_Codigo      = $this->compania;
            $filter->LET_Serie         = $serie;
            $filter->LET_Numero        = $numero;
            $filter->CLIP_Codigo       = $cliente;
            $filter->PROVP_Codigo      = $proveedor;

            $filter->MONED_Codigo      = $moneda;
            $filter->FORPAP_Codigo     = $forma_pago;
            $filter->LET_Total         = $importe[$i];
            $filter->LET_Observacion   = $observacion[$i];
            $filter->LET_Fecha         = $fechaPago[$i];
            $filter->LET_FechaVenc     = $fechaVencimiento[$i];
            $filter->LET_FlagEstado    = $estado_letra[$i];

            $filter->USUA_Codigo       = $this->usuario;
            $filter->LET_Vendedor      = "";
            
            $filter->LET_FechaRegistro = date("Y-m-d h:i:s");
            
            $filter->BANP_Codigo       = $banco;
            $filter->LET_Representante = $titular;
            $filter->LET_Oficina       = "";
            $filter->LET_NumeroCuenta  = $cuenta;
            $filter->LET_Direccion     = "";

            $letra = $this->letra_model->insertar_letra($filter);

            if ( $letra != NULL ){
                for ( $j=0; $j<$cantDocumentos; $j++ ){
                    $filterDocs = new stdClass();
                    $filterDocs->LET_Codigo = $letra;
                    $filterDocs->CPP_Codigo = $documentos[$j];
                    $this->letra_model->insertar_documentos_letra($filterDocs);
                }
            }
        }

        if ($letra)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        
        echo json_encode($json);
    }

    public function actualizar_letras(){

        $letra = $this->input->post("letraUP");
        $forma_pago = $this->input->post("forma_pagoUP");
        $moneda = $this->input->post("monedaUP");
        $banco = $this->input->post("bancoUP");
        $titular = $this->input->post("titularUP");
        $cuenta = $this->input->post("cuentaUP");
        $fechaPago = $this->input->post("FechaPagoUP");
        $fechaVencimiento = $this->input->post("FechaVencimientoUP");
        $importe = $this->input->post("importeUP");
        $estado_letra = $this->input->post("estado_letraUP");
        $observacion = $this->input->post("observacionUP");

        $i = 0;

        $filter = new stdClass();
        $filter->MONED_Codigo      = $moneda;
        $filter->FORPAP_Codigo     = $forma_pago;
        $filter->LET_Total         = $importe[$i];
        $filter->LET_Observacion   = $observacion[$i];
        $filter->LET_Fecha         = $fechaPago[$i];
        $filter->LET_FechaVenc     = $fechaVencimiento[$i];
        $filter->LET_FlagEstado    = $estado_letra[$i];
        
        $filter->LET_FechaModificacion = date("Y-m-d h:i:s");
        
        $filter->BANP_Codigo       = $banco;
        $filter->LET_Representante = $titular;
        $filter->LET_Oficina       = "";
        $filter->LET_NumeroCuenta  = $cuenta;
        $filter->LET_Direccion     = "";

        if ($letra != "")
            $result = $this->letra_model->actualizar_letra($letra, $filter);

        if ($result)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        
        echo json_encode($json);
    }

    function obtener_datos_cliente($cliente, $tipo_docu = 'F') {
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
        }
        elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
        }

        return array('numdoc' => $numdoc, 'nombre' => $nombre, 'direccion' => $direccion);
    }

    public function obtener_lista_detalles($codigo) {
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

                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
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
            }
        }
        return $lista_detalles;
    }

    public function obtener_tipo_de_cambio($fecha_comprobante) {
        return $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);
    }

    public function letra_pdf($letra, $flagImage = 1){
        $this->lib_props->letra_pdf($letra, $flagImage);
    }
}
?>