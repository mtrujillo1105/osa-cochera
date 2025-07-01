<?php
ini_set('error_reporting', 1);  //bloq stv  pa q al inicio no cargue con error el pdf en firefox

include("system/application/libraries/pchart/pData.php");
include("system/application/libraries/pchart/pChart.php");
include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");

class Letracambio extends CI_Controller {

    public function __construct() {
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
        ///
        $this->load->model('tesoreria/banco_model');
        ////
        $this->load->model('configuracion_model');
        
        $this->load->model('ventas/letracambio_model');

        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
        $this->somevar['url'] = $_SERVER['REQUEST_URI'];
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

    public function canje_documento($codigo_documento) {
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


        $this->load->view('ventas/ventana_canje', $data);
    }

    public function canjear_documento() {

        $compania = $this->somevar['compania'];

        $cliente = $this->input->post('cod_cliente');
        $fecha = $this->input->post('fecha');

        $comprobantes = $this->input->post('cod_comprobante');

        $tipo_docu = $this->input->post('cmbDocumento');

        $user = $this->session->userdata('user');

        if (count($comprobantes) == 1) {

            $datos = $this->comprobante_model->obtener_comprobante($comprobantes[0]);

            $filter = new stdClass();
            $filter->CPC_TipoOperacion = $datos[0]->CPC_TipoOperacion;
            $filter->CPC_TipoDocumento = $datos[0]->CPC_TipoDocumento;
            $filter->PRESUP_Codigo = $datos[0]->PRESUP_Codigo;
            $filter->OCOMP_Codigo = $datos[0]->OCOMP_Codigo;
            $filter->COMPP_Codigo = $datos[0]->COMPP_Codigo;
            $filter->CPC_Serie = $datos[0]->CPC_Serie;
            $filter->CPC_Numero = $datos[0]->CPC_Numero;
            $filter->CLIP_Codigo = $datos[0]->CLIP_Codigo;
            $filter->PROVP_Codigo = $datos[0]->PROVP_Codigo;
            $filter->CPC_NombreAuxiliar = $datos[0]->CPC_NombreAuxiliar;
            $filter->USUA_Codigo = $datos[0]->USUA_Codigo;
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
            $filter->CPC_DocuRefeCodigo = $datos[0]->CPC_DocuRefeCodigo;
            $filter->CPC_Observacion = $datos[0]->CPC_Observacion;
            $filter->CPC_ModoImpresion = $datos[0]->CPC_ModoImpresion;
            $filter->CPC_Fecha = $datos[0]->CPC_Fecha;
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
            $filter->CPP_Codigo_Canje = $comprobantes[0];
            $this->comprobante_model->insertar_comprobante2($filter);


            $a_filter = new stdClass();
            $a_filter->CPC_TipoDocumento = $tipo_docu;
            
            if ($tipo_docu == 'F') {
                $tipo = 8;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $cofiguracion_datos[0]->CONFIC_Serie;
            $cofiguracion_datos[0]->CONFIC_Numero;

            $serie = $cofiguracion_datos[0]->CONFIC_Serie;
            $numero = $cofiguracion_datos[0]->CONFIC_Numero + 1;

            $a_filter->CPC_Serie = $serie;
            $a_filter->CPC_Numero = '00' . $numero;
            $a_filter->CPC_Fecha = human_to_mysql($fecha);
            $a_filter->CLIP_Codigo = $cliente;

            $t_igv = $datos[0]->CPC_igv100;
            $total = $datos[0]->CPC_total;

            $subtotal = $total / (1 + ($t_igv / 100));

            $igv = $total - $subtotal;

            $a_filter->CPC_total = $total;

            $a_filter->CPC_subtotal = $subtotal;

            $a_filter->CPC_igv = $igv;

            $this->comprobante_model->modificar_comprobante($comprobantes[0], $a_filter);

            $this->configuracion_model->modificar_configuracion($compania, $tipo, $numero);
            // exit('{"result":"success", "serie":"' . $serie . '"}');

            exit('{"result":"success", "serie":"' . $serie . '", "numero":"00' . $numero . '"}');
        } else {


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

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $cofiguracion_datos[0]->CONFIC_Serie;
            $cofiguracion_datos[0]->CONFIC_Numero;

            $serie = $cofiguracion_datos[0]->CONFIC_Serie;
            $numero =  $cofiguracion_datos[0]->CONFIC_Numero + 1;

            $filter = new stdClass();
            $filter->CPC_TipoOperacion = $datos[0]->CPC_TipoOperacion;
            $filter->CPC_TipoDocumento = $tipo_docu;
            $filter->PRESUP_Codigo = $datos[0]->PRESUP_Codigo;
            $filter->OCOMP_Codigo = $datos[0]->OCOMP_Codigo;
            $filter->COMPP_Codigo = $datos[0]->COMPP_Codigo;
            $filter->CPC_Serie = $serie;
            $filter->CPC_Numero = '00' .$numero;
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
            $filter->CPC_Fecha =  human_to_mysql($fecha);;
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
            $comprobante = $this->comprobante_model->insertar_comprobante2($filter);

            $this->configuracion_model->modificar_configuracion($compania, $tipo, $numero);

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

                    $this->comprobantedetalle_model->insertar($d_filter);
                endfor;
            endfor;

            exit('{"result":"success", "serie":"' . $serie . '", "numero":"00' . $numero . '"}');
        }
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

    
    
    /////////////////////////vico

    public function obtener_detalle_comprobante_x_numero_com($serie,$numero, $tipo_oper = 'V', $almacen = "") {
        $comprobante = $this->comprobante_model->buscar_xserienum($serie,$numero,"F",$tipo_oper);
        if(!isset($comprobante)){
            $comprobante = $this->comprobante_model->buscar_xserienum($serie,$numero,"B",$tipo_oper);
        }
        $comprobante=$comprobante[0]->CPP_Codigo;
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
            $datos = $this->cliente_model->obtener($cliente);}
        else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C'){
            $datos = $this->proveedor_model->obtener($proveedor);}
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
                                
                if($tipo_doc=='B'){
                    $pu = round($valor->CPDEC_Pu_ConIgv,2);
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
                if(count($datos_almaprod)>0){
                /////    
                $stock = $datos_almaprod[0]->ALMPROD_Stock;    
                ///stv
                }else{                    
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
    
    
    
    
    public function comprobantes($tipo_oper = '', $tipo_docu = '', $j = '0', $limpia = '') {
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);


//************para validar permisos************//
// parametros
// 1 .- codigo rol
// 2 .- url
// $permiso 	= buscar_permiso($this->somevar['rol'],$this->somevar['url']);
// $sesion 	= array('constante'=>$permiso[0]->constante,'menu'=>$permiso[0]->menu);
// $this->session->set_userdata($sesion);
//************para validar permisos************//

        

        if ($limpia == '1') {
            $this->session->unset_userdata('fechai');
            $this->session->unset_userdata('fechaf');
            $this->session->unset_userdata('seriei');
            $this->session->unset_userdata('numero');
            $this->session->unset_userdata('cliente');
            $this->session->unset_userdata('ruc_cliente');
            $this->session->unset_userdata('nombre_cliente');
            $this->session->unset_userdata('proveedor');
            $this->session->unset_userdata('ruc_proveedor');
            $this->session->unset_userdata('nombre_proveedor');
            $this->session->unset_userdata('producto');
            $this->session->unset_userdata('codproducto');
            $this->session->unset_userdata('nombre_producto');
        }
        $filter = new stdClass();
        if (count($_POST) > 0) {
            $filter->fechai = $this->input->post('fechai');
            $filter->fechaf = $this->input->post('fechaf');
            $filter->seriei = $this->input->post('seriei');
            $filter->numero = $this->input->post('numero');
            $filter->cliente = $this->input->post('cliente');
            $filter->ruc_cliente = $this->input->post('ruc_cliente');
            $filter->nombre_cliente = $this->input->post('nombre_cliente');
            $filter->proveedor = $this->input->post('proveedor');
            $filter->ruc_proveedor = $this->input->post('ruc_proveedor');
            $filter->nombre_proveedor = $this->input->post('nombre_proveedor');
            $filter->producto = $this->input->post('producto');
            $filter->codproducto = $this->input->post('codproducto');
            $filter->nombre_producto = $this->input->post('nombre_producto');
            $this->session->set_userdata(array('fechai' => $filter->fechai, 'fechaf' => $filter->fechaf, 'seriei' => $filter->seriei, 'numero' => $filter->numero, 'cliente' => $filter->cliente, 'ruc_cliente' => $filter->ruc_cliente, 'nombre_cliente' => $filter->nombre_cliente, 'proveedor' => $filter->proveedor, 'ruc_proveedor' => $filter->ruc_proveedor, 'nombre_proveedor' => $filter->nombre_proveedor, 'producto' => $filter->producto, 'codproducto' => $filter->codproducto, 'nombre_producto' => $filter->nombre_producto));
        } else {
            $filter->fechai = $this->session->userdata('fechai');
            $filter->fechaf = $this->session->userdata('fechaf');
            $filter->seriei = $this->session->userdata('seriei');
            $filter->numero = $this->session->userdata('numero');
            $filter->cliente = $this->session->userdata('cliente');
            $filter->ruc_cliente = $this->session->userdata('ruc_cliente');
            $filter->nombre_cliente = $this->session->userdata('nombre_cliente');
            $filter->proveedor = $this->session->userdata('proveedor');
            $filter->ruc_proveedor = $this->session->userdata('ruc_proveedor');
            $filter->nombre_proveedor = $this->session->userdata('nombre_proveedor');
            $filter->producto = $this->session->userdata('producto');
            $filter->codproducto = $this->session->userdata('codproducto');
            $filter->nombre_producto = $this->session->userdata('nombre_producto');
        }
        $data['fechai'] = $filter->fechai;
        $data['fechaf'] = $filter->fechaf;
        $data['seriei'] = $filter->seriei;
        $data['numero'] = $filter->numero;
        $data['cliente'] = $filter->cliente;
        $data['ruc_cliente'] = $filter->ruc_cliente;
        $data['nombre_cliente'] = $filter->nombre_cliente;
        $data['proveedor'] = $filter->proveedor;
        $data['ruc_proveedor'] = $filter->ruc_proveedor;
        $data['nombre_proveedor'] = $filter->nombre_proveedor;
        $data['producto'] = $filter->producto;
        $data['codproducto'] = $filter->codproducto;
        $data['nombre_producto'] = $filter->nombre_producto;
        $data['action'] = 'index.php/ventas/letracambio/comprobantes/' . $tipo_oper . '/' . $tipo_docu;

        $data['registros'] = count($this->letracambio_model->buscar_comprobantes($tipo_oper, $tipo_docu, $filter, NULL, '', ''));
        $conf['base_url'] = site_url('ventas/letracambio/comprobantes/' . $tipo_oper . '/' . $tipo_docu);
        $conf['per_page'] = 30;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 6;
        $offset = (int) $this->uri->segment(6);
        $listado_comprobantes = $this->letracambio_model->buscar_comprobantes($tipo_oper, $tipo_docu, $filter, $conf['per_page'], $offset, date('Y-m-d'));

        $item = $j + 1;
        $lista = array();
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                $codigo = $valor->LET_Codigo;
                $fecha = mysql_to_human($valor->LET_Fecha);

                $codigo_canje = $valor->LET_Codigo_canje;

                $serie = $valor->LET_Serie;
                $numero = $valor->LET_Numero;
                $guiarem_codigo = $valor->LET_GuiaRemCodigo;
                $docurefe_codigo = $valor->LET_DocuRefeCodigo;
                if ($tipo_oper == "V") {
                    if ($valor->CLIP_Codigo == 144 && $valor->CPC_NombreAuxiliar != 'cliente')
                        $nombre = strtoupper($valor->CPC_NombreAuxiliar);
                    else
                        $nombre = $valor->nombre;
                }else {
                    $nombre = $valor->nombre;
                }
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->LET_total, 2);
                $estado = $valor->LET_FlagEstado;
                $pago_pendiente = $this->letracambio_model->comprobante_pago_pendiente($codigo);
//       asi taba         $img_estado = ($estado == '1' || $estado == '2' ? "<a href='" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_usuario2/" . $serie . "/" . $codigo . "' id='linkVerProveedor'><img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' /></a> " : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
                //aumentado
                $img_estado = ($estado == '1' || $estado == '2' ? "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' /> " : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
                ///

                if ($this->somevar['rol'] == '4' && $estado == '1' || $estado == '2')
                    $editar = "<a href='javascript:;' onclick='editar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                else
                    $editar = "";
                if ($estado == '2' || $estado == '1') //por mientras no tocara stock   estaba si if ($estado == '2' )
                    $ver = "<a href='javascript:;' onclick='ver_comprobante_pdf(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                else
                    $ver = "";

                $ver2 = "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar = "<a href='javascript:;' onclick='eliminar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                if ($estado == '2')
                    $disparador = "<a href='javascript:;' onclick='disparador(" . $codigo . ")' >Por Aprobar</a>";
                else
                    $disparador = "";
                if ($tipo_oper == 'V')
                    $lista[] = array($item++, $fecha, $serie, $numero, $guiarem_codigo, $docurefe_codigo, $nombre, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje);
                else
                    $lista[] = array($item++, $fecha, $serie, $numero, $guiarem_codigo, $docurefe_codigo, $nombre, $total, $img_estado, $editar, $ver, $ver2, $disparador, $estado, $codigo, $codigo_canje);
            }
        }
        $data['titulo_tabla'] = "RELACIÃ“N DE " . strtoupper($this->obtener_tipo_documento($tipo_docu)) . "S";
        $data['titulo_busqueda'] = "BUSCAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = $tipo_docu;
        $data['lista'] = $lista;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => $tipo_docu));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('ventas/letracambio_index', $data);
    }

    public function comprobante_nueva($tipo_oper = '', $tipo_docu = '') {
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);
        $compania = $this->somevar['compania'];
        
        unset($_SESSION['serie']);

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);

//Para cambio comprobante_A
        $data['cambio_comp'] = "0";
        $data['total_det'] = "0";
//$data['mueve']          = $mueve;
        $codigo = "";
        $data['codigo'] = $codigo;
        $data['cbo_dpto'] = $this->seleccionar_departamento('15');
        $data['cbo_prov'] = $this->seleccionar_provincia('15', '01');
        $data['cbo_dist'] = $this->seleccionar_distritos('15', '01');
        
        ////
//        $data['cbo_dptopago'] = $this->seleccionar_departamentopago('15');
//        $data['cbo_provpago'] = $this->seleccionar_provinciapago('15', '01');
//        $data['cbo_distpago'] = $this->seleccionar_distritospago('15', '01');
        ///
        
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['url_action'] = base_url() . "index.php/ventas/letracambio/comprobante_insertar";
        $data['titulo'] = "REGISTRAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tit_imp'] = strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['guia'] = "";
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        ////
        $data['cbobanco'] = $this->OPTION_generador($this->banco_model->listar(), 'BANP_Codigo', 'BANC_Nombre', '1');
        ///
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '1');
        $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
        date_default_timezone_set("America/Lima");
        $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

        if (count($cambio_dia) > 0) {
            $data['tdc'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
        } else {
            $data['tdc'] = '';
        }
        $data['serie'] = '';
        $data['numero'] = '';
        if ($tipo_oper == 'V') {
            $temp = $this->obtener_serie_numero($tipo_docu);
        }

        $data['cliente'] = "";  //1662
        $data['ruc_cliente'] = "";
        $data['nombre_cliente'] = "";
        ////
        $data['clientedos'] = "";
        $data['ruc_clientedos'] = "";
        $data['nombre_clientedos'] = "";
        $data['clientetres'] = "";
        $data['ruc_clientetres'] = "";
        $data['nombre_clientetres'] = "";
        ///
        $data['proveedor'] = "";
        $data['ruc_proveedor'] = "";
        $data['nombre_proveedor'] = "";
        ///
        /////
        $data['proveedordos'] = "";
        $data['ruc_proveedordos'] = "";
        $data['nombre_proveedordos'] = "";
        $data['proveedortres'] = "";
        $data['ruc_proveedortres'] = "";
        $data['nombre_proveedortres'] = "";
        /////
        ////
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
        $data['guiarem_codigo'] = "";
        $data['docurefe_codigo'] = "";
        $data['estado'] = "1";
        
        ////
        $data['representante'] = "";
        $data['oficina'] = "";
        $data['numerocuenta'] = "";
        $data['dc'] = "";
        $data['direccion'] = "";
        $data['direccionpago'] = "";
        //$data['cuentabanco'] = "";
        ////

        $data['modo_impresion'] = "1";
        if ($tipo_docu != 'B') {
            if (FORMATO_IMPRESION == 1)
                $data['modo_impresion'] = "2";
            else
                $data['modo_impresion'] = "1";
        }
        $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
        ///
        $data['hoyvenc'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
        ///
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
        //obtengo las series de la configuracion
        if ($tipo_docu == 'F') {
            //$tipo = 8;
            $tipo=16;
        }
        if ($tipo_docu == 'B') {
            $tipo = 9;
        }
        if ($tipo_docu == 'N') {
            $tipo = 14;
        }
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        $cofiguracion_datos[0]->CONFIC_Serie;
        $cofiguracion_datos[0]->CONFIC_Numero;
        // $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'B');
        $data['serie_suger_b'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_b'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        // $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'F');
        $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_f'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        $this->layout->view('ventas/letracambio_nueva', $data);
    }

    public function comprobante_insertar() {
        if ($this->input->post('serie') == '')
            exit('{"result":"error", "campo":"serie"}');
        if ($this->input->post('numero') == '')
            exit('{"result":"error", "campo":"numero"}');
        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');
        ////
//        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('clientedos') == '')
//            exit('{"result":"error", "campo":"ruc_clientedos"}');
//        if ($this->input->post('tipo_oper') == 'V' && $this->input->post('clientetres') == '')
//            exit('{"result":"error", "campo":"ruc_clientetres"}');        
        ////
        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedor') == '')
            exit('{"result":"error", "campo":"ruc_proveedor"}');
        ////
//        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedordos') == '')
//            exit('{"result":"error", "campo":"ruc_proveedordos"}');
//        if ($this->input->post('tipo_oper') == 'C' && $this->input->post('proveedortres') == '')
//            exit('{"result":"error", "campo":"ruc_proveedortres"}');
        ////
        if ($this->input->post('moneda') == '0' || $this->input->post('moneda') == '')
            exit('{"result":"error", "campo":"moneda"}');
//        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
//            exit('{"result":"error", "campo":"observacion"}');
        if ($this->input->post('tdc') == '')
            exit('{"result":"error", "campo":"tdc"}');
        
        ////
        if ($this->input->post('tipo_oper') == 'V' && ($this->input->post('importetotal') == '' || $this->input->post('importetotal') == '0' ))
            exit('{"result":"error", "campo":"importetotal"}');
        if ($this->input->post('tipo_oper') == 'C' && ($this->input->post('importetotal') == '' || $this->input->post('importetotal') == '0' ))
            exit('{"result":"error", "campo":"importetotal"}');
        ///

//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $dref = $this->input->post('dRef');
        $guiarem_id = $this->input->post("codigo");
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $filter = new stdClass();
        $filter->LET_TipoOperacion = $tipo_oper;
        $filter->LET_TipoDocumento = $tipo_docu;
        $filter->ALMAP_Codigo = 3; //$this->input->post('almacen');
        $verificacion = $this->letracambio_model->buscar_xserienum($serie, $numero, $tipo_docu, $tipo_oper);
        date_default_timezone_set('America/Lima');
        $hora = date("H:i:s");

        if (count($verificacion) > 0) {
//            if ($tipo_docu == 'F') {
//                $tipo = 16;
//            }
//            if ($tipo_docu == 'B') {
//                $tipo = 9;
//            }
//            if ($tipo_docu == 'N') {
//                $tipo = 14;
//            }
            $compania = $this->somevar['compania'];
            $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $num = $configuracion_datos[0]->CONFIC_Numero + 1;
            $filter->LET_Numero = '00' . $num;
        } else {
            $filter->LET_Numero = $this->input->post('numero');
        }
//        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $filter->FORPAP_Codigo = 1;//$this->input->post('forma_pago');
        $filter->LET_Observacion = strtoupper($this->input->post('observacion'));
        $filter->LET_Fecha = human_to_mysql($this->input->post('fecha'));
        ////
        $filter->LET_FechaVenc = human_to_mysql($this->input->post('fechavenc'));
        ////
        $filter->LET_Hora = $hora;
        $filter->LET_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        ///
        $filter->LET_Banco = $this->input->post('cbobanco');
        ///
        $filter->LET_descuento100 = $this->input->post('descuento');
        $filter->LET_igv100 = $this->input->post('igv');
        $filter->LET_FlagEstado = 1; //por mientras estaba 2;
        
        
        ///
        $filter->LET_Representante = $this->input->post('representante');
        $filter->LET_Oficina = $this->input->post('txtoficina');
        $filter->LET_NumeroCuenta = $this->input->post('txtnumcuenta');
        $filter->LET_DC = $this->input->post('txtdc');
        $filter->LET_Direccion = $this->input->post('direccion');
        
        ///
        $filter->LET_DireccionPago = $this->input->post('direccionpago');
        //
        
        $dep=$this->input->post('cboDepartamento');
        $pro=$this->input->post('cboProvincia');
        $dis=$this->input->post('cboDistrito');
        
        $ubigeo=''.$dep.$pro.$dis.'';
        
        $filter->LET_Ubigeo = $ubigeo;
        ///
        
        $nombre = $this->input->post('nombre_cliente');

        if ($this->input->post('cliente') == 144 ||
                $this->input->post('cliente') == 135 ||
                $this->input->post('cliente') == 218 ||
                $this->input->post('cliente') == 1037)
            $filter->LET_NombreAuxiliar = $nombre;

        if ($tipo_oper == 'V'){
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $filter->CLIPDOS_Codigo = $this->input->post('clientedos');
            $filter->CLIPTRES_Codigo = $this->input->post('clientetres');            
        }else{
            $filter->PROVP_Codigo = $this->input->post('proveedor');
            $filter->PROVPDOS_Codigo = $this->input->post('proveedordos');
            $filter->PROVPTRES_Codigo = $this->input->post('proveedortres');
        }
//        if ($this->input->post('presupuesto_codigo') != '' && $this->input->post('presupuesto_codigo') != '0')
//            $filter->PRESUP_Codigo = $this->input->post('presupuesto_codigo');
//        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
//            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
//        $filter->LET_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
//        $filter->LET_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
//        $filter->LET_ModoImpresion = '1';
//        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->LET_ModoImpresion = 2; //$this->input->post('modo_impresion');
//        if ($tipo_docu != 'B' && $tipo_docu != 'N') {
//            $filter->LET_subtotal = $this->input->post('preciototal');
//            $filter->LET_descuento = $this->input->post('descuentotal');
//            $filter->LET_igv = $this->input->post('igvtotal');
//        } else {
//            $filter->LET_subtotal_conigv = $this->input->post('preciototal_conigv');
//            $filter->LET_descuento_conigv = $this->input->post('descuentotal_conigv');
//        }
        $filter->LET_total = $this->input->post('importetotal');
//        if ($this->input->post('vendedor') != '')
//            $filter->LET_Vendedor = $this->input->post('vendedor');
        $filter->LET_TDC = $this->input->post('tdc');
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        //Datos cabecera de la guiasa.
        if ($dref == '') {
//            if ($tipo_oper == 'V') {
//                if ($mueve == 1) {
//                    $filter3 = new stdClass();
//                    $filter3->TIPOMOVP_Codigo = 1;
//                    $filter3->ALMAP_Codigo = $this->input->post('almacen') != '' ? $this->input->post('almacen') : NULL;
//                    $filter3->CLIP_Codigo = $this->input->post('cliente');
//                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
//                    $filter3->GUIASAC_Fecha = human_to_mysql($this->input->post('fecha'));
//                    $filter3->GUIASAC_Observacion = strtoupper($this->input->post('observacion'));
//                    $filter3->USUA_Codigo = $this->somevar['user'];
//                    $filter3->GUIASAC_Automatico = 1;
//                    $guia_id = $this->guiasa_model->insertar($filter3);
//                    $filter->GUIASAP_Codigo = $guia_id;
//                    $filter->CPC_FlagMueveStock = '1';
//                } else {
//                    $filter->CPC_FlagMueveStock = '0';
//                }
//            } else {
//                if ($mueve == 1) {
//                    $filter3 = new stdClass();
//                    $filter3->TIPOMOVP_Codigo = 2;
//                    $filter3->ALMAP_Codigo = $this->input->post('almacen') != '' ? $this->input->post('almacen') : NULL;
//                    $filter3->PROVP_Codigo = $this->input->post('proveedor');
//                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
//                    $filter3->GUIAINC_Fecha = human_to_mysql($this->input->post('fecha'));
//                    $filter3->GUIAINC_Observacion = strtoupper($this->input->post('observacion'));
//                    $filter3->USUA_Codigo = $this->somevar['user'];
//                    $filter3->GUIAINC_Automatico = 1;
//                    $guia_id = $this->guiain_model->insertar($filter3);
//                    $filter->GUIAINP_Codigo = $guia_id;
//                    $filter->CPC_FlagMueveStock = '1';
//                } else {
//                    $filter->CPC_FlagMueveStock = '0';
//                }
//            }
        } else {
//            $filter->GUIAREMP_Codigo = $dref;
        }
        $comprobante = $this->letracambio_model->insertar_comprobante($filter);

        
        ///detalle inicio
//        $flagBS = $this->input->post('flagBS');
//        $prodcodigo = $this->input->post('prodcodigo');
//        $prodcantidad = $this->input->post('prodcantidad');
//        if ($tipo_docu != 'B' && $tipo_docu != 'N') {
//            $prodpu = $this->input->post('prodpu');
//            $prodprecio = $this->input->post('prodprecio');
//            $proddescuento = $this->input->post('proddescuento');
//            $prodigv = $this->input->post('prodigv');
//        } else {
//            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
//            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
//        }
//        $prodimporte = $this->input->post('prodimporte');
//        $prodpu_conigv = $this->input->post('prodpu_conigv');
//        $produnidad = $this->input->post('produnidad');
//        $flagGenInd = $this->input->post('flagGenIndDet');
//        $detaccion = $this->input->post('detaccion');
//        $proddescuento100 = $this->input->post('proddescuento100');
//        $prodigv100 = $this->input->post('prodigv100');
//        $prodcosto = $this->input->post('prodcosto');
//        $proddescri = $this->input->post('proddescri');
        
        
        
//        if (is_array($prodcodigo)) {
//            foreach ($prodcodigo as $indice => $valor) {
//                $filter = new stdClass();
//                $filter->CPP_Codigo = $comprobante;
//                $filter->PROD_Codigo = $prodcodigo[$indice];
//                if ($produnidad[$indice] == '' || $produnidad[$indice] == "null")
//                    $produnidad[$indice] = NULL;
//                if ($flagBS[$indice] == 'B')
//                    $filter->UNDMED_Codigo = $produnidad[$indice];
//
//                $filter->CPDEC_Cantidad = $prodcantidad[$indice];
//                if ($tipo_docu != 'B' && $tipo_docu != 'N') {
//                    $filter->CPDEC_Pu = $prodpu[$indice];
//                    $filter->CPDEC_Subtotal = $prodprecio[$indice];
//                    $filter->CPDEC_Descuento = $proddescuento[$indice];
//                    $filter->CPDEC_Igv = $prodigv[$indice];
//                } else {
//                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
//                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
//                }
//                $filter->CPDEC_Total = $prodimporte[$indice];
//                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
//                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
//                $filter->CPDEC_Igv100 = $prodigv100[$indice];
//                
//                
//                
//                ////stv    va ser nuevo precio costo en compra
//                if ($tipo_oper == 'C'){
//                
//                $filter->CPDEC_Costo = $prodpu_conigv[$indice];
//                }
//                ////
//                
//                
//                
//                if ($tipo_oper == 'V')
//                $filter->CPDEC_Costo = $prodcosto[$indice];
//                $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
//                $filter->CPDEC_GenInd = $flagGenInd[$indice];
//                $filter->CPDEC_Observacion = "";
//
//                if ($detaccion[$indice] != 'e') {
                    
                    ///////fin detalle
                
                    
//                    if ($mueve == 1) {
//                        if ($tipo_oper == 'V') {
//                            $filter4 = new stdClass();
//                            $filter4->GUIASAP_Codigo            = $guia_id;
//                            $filter4->PRODCTOP_Codigo           = $prodcodigo[$indice];
//                            $filter4->UNDMED_Codigo             = $produnidad[$indice];
//                            $filter4->GUIASADETC_Cantidad       = $prodcantidad[$indice];
//                            $filter4->GUIASADETC_Costo          = $prodcosto[$indice];
//                            $filter4->GUIASADETC_GenInd         = $flagGenInd[$indice];
//                            $filter4->GUIASADETC_Descripcion    = strtoupper($proddescri[$indice]);
//                            ;
//                            $this->guiasadetalle_model->insertar($filter4);
//                        } else {
//                            $filter4 = new stdClass();
//                            $filter4->GUIAINP_Codigo            = $guia_id;
//                            $filter4->PRODCTOP_Codigo           = $prodcodigo[$indice];
//                            $filter4->UNDMED_Codigo             = $produnidad[$indice];
//                            $filter4->GUIAINDETC_Cantidad       = $prodcantidad[$indice];
//                            $filter4->GUIIAINDETC_GenInd        = $flagGenInd[$indice];
//                            $filter4->GUIAINDETC_Costo          = $prodpu_conigv[$indice]; // No estoy muy seguro de si debe agarrar este precio, porque puede ser $costo, $venta
//                            $filter4->GUIAINDETC_Descripcion    = $flagGenInd[$indice];
//                            $this->guiaindetalle_model->insertar($filter4);
//                        }
//                    }
                    
                    
                    ///detalle inicio
//                    $this->comprobantedetalle_model->insertar($filter);
//                }
//            }
//        }
        ///detalle fin

        exit('{"result":"ok", "codigo":"' . $comprobante . '"}');
    }

    public function disparador($tipo_oper = 'V', $codigo, $tipo_docu = 'F') {

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


            $compania = $this->somevar['compania'];


            if ($tipo_docu == 'F') {
                $tipo = 8;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }
            if ($tipo_docu == 'N') {
                $tipo = 14;
            }

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);

            if ($cofiguracion_datos) {
                $filter->CPC_Numero = sprintf("%07d", $cofiguracion_datos[0]->CONFIC_Numero + 1);
            }

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
                        $filter4->GUIASADETC_Descripcion = $nombre_producto;
                        
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
        }//
        //$this->comprobantes($tipo_oper , $tipo_docu , 0, 1);
        redirect('ventas/comprobante/comprobantes/' . $tipo_oper . '/' . $tipo_docu);
    }

    public function comprobante_insertar_ref() {
        $compania = $this->somevar['compania'];

//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $dref = $this->input->post('dRef');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('cboTipoDocu');

        $filter = new stdClass();

        $filter->CPC_TipoOperacion = $tipo_oper;
        $filter->CPC_TipoDocumento = $tipo_docu;
        $filter->GUIAREMP_Codigo = $dref;
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->CPC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CPC_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->CPC_Numero = $this->input->post('numero');
        $filter->CPC_Serie = $this->input->post('serie');
        //actualiza los numeros de configuracion 
        $numero = $filter->CPC_Numero;
        $this->configuracion_model->modificar_configuracion($compania, $tipo_docu, $numero, $serie = null);

        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CPC_descuento100 = $this->input->post('descuento');
        $filter->CPC_igv100 = $this->input->post('igv');
        $filter->CPC_FlagEstado = $this->input->post('estado');
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
        if ($this->input->post('vendedor') != '')
            $filter->CPC_Vendedor = $this->input->post('vendedor');
        $filter->CPC_TDC = $this->input->post('tdc');


        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
//Datos cabecera de la guiasa.
        $comprobante = $this->comprobante_model->insertar_comprobante($filter);

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
        $proddescri = $this->input->post('proddescri');

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
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
                $filter->CPDEC_GenInd = $flagGenInd[$indice];
                $filter->CPDEC_Observacion = "";

                if ($detaccion[$indice] != 'e') {
                    $this->comprobantedetalle_model->insertar($filter);
                }
            }
        }
        exit('{"result":"ok", "codigo":"' . $comprobante . '"}');
    }

    public function comprobante_ver($codigo, $tipo_oper = 'V', $tipo_docu = 'F') {
        
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

    public function comprobante_editar($codigo, $tipo_oper = 'V', $tipo_docu = 'F') {
        

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
        $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        $serie = $datos_comprobante[0]->LET_Serie;
        $numero = $datos_comprobante[0]->LET_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $clientedos = $datos_comprobante[0]->CLIPDOS_Codigo;
        $clientetres = $datos_comprobante[0]->CLIPTRES_Codigo;        
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $proveedordos = $datos_comprobante[0]->PROVPDOS_Codigo;
        $proveedortres = $datos_comprobante[0]->PROVPTRES_Codigo;        
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->LET_subtotal;
        $descuento = $datos_comprobante[0]->LET_descuento;
        $igv = $datos_comprobante[0]->LET_igv;
        $total = $datos_comprobante[0]->LET_total;
        $subtotal_conigv = $datos_comprobante[0]->LET_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->LET_descuento_conigv;
        $igv100 = $datos_comprobante[0]->LET_igv100;
        $descuento100 = $datos_comprobante[0]->LET_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->LET_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->LET_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->LET_Observacion;
        $modo_impresion = $datos_comprobante[0]->LET_ModoImpresion;
        $estado = $datos_comprobante[0]->LET_FlagEstado;
        $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
        ////
        $fechavenc = mysql_to_human($datos_comprobante[0]->LET_FechaVenc);
        ////
        $vendedor = $datos_comprobante[0]->LET_Vendedor;
        $tdc = $datos_comprobante[0]->LET_TDC;
        ////
        $banco = $datos_comprobante[0]->LET_Banco;
        $representante = $datos_comprobante[0]->LET_Representante;
        $oficina = $datos_comprobante[0]->LET_Oficina;
        $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
        $dc = $datos_comprobante[0]->LET_DC;
        $direccion = $datos_comprobante[0]->LET_Direccion;
        
        $direccionpago = $datos_comprobante[0]->LET_DireccionPago;
        
        $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
        
        $cbo_dpto=substr($ubigeo, 0,2);
        $cbo_prov=substr($ubigeo, 2,2);
        $cbo_dist=substr($ubigeo, 4,2);
        
        ////

        $ruc_cliente = '';
        $ruc_clientedos = '';
        $ruc_clientetres = '';
        $nombre_cliente = '';
        $nombre_clientedos = '';
        $nombre_clientetres = '';
        $nombre_proveedor = '';
        $nombre_proveedordos = '';
        $nombre_proveedortres = '';
        $ruc_proveedor = '';
        $ruc_proveedordos = '';
        $ruc_proveedortres = '';
        
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
        
        /////
        if ($clientedos != '' && $clientedos != '0') {
            $datos_clientedos = $this->cliente_model->obtener($clientedos);
            if ($datos_clientedos) {
                $nombre_clientedos = $datos_clientedos->nombre;
                $ruc_clientedos = $datos_clientedos->ruc;
            }
        } elseif ($proveedordos != '' && $proveedordos != '0') {
            $datos_proveedordos = $this->proveedor_model->obtener($proveedordos);
            if ($datos_proveedordos) {
                $nombre_proveedordos = $datos_proveedordos->nombre;
                $ruc_proveedordos = $datos_proveedordos->ruc;
            }
        }
        
        if ($clientetres != '' && $clientetres != '0') {
            $datos_clientetres = $this->cliente_model->obtener($clientetres);
            if ($datos_clientetres) {
                $nombre_clientetres = $datos_clientetres->nombre;
                $ruc_clientetres = $datos_clientetres->ruc;
            }
        } elseif ($proveedortres != '' && $proveedortres != '0') {
            $datos_proveedortres = $this->proveedor_model->obtener($proveedortres);
            if ($datos_proveedortres) {
                $nombre_proveedortres = $datos_proveedortres->nombre;
                $ruc_proveedortres = $datos_proveedortres->ruc;
            }
        }
        ////
        
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
        //// 
        $data['cbobanco'] = $this->OPTION_generador($this->banco_model->listar(), 'BANP_Codigo', 'BANC_Nombre', $banco);
        ////        
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $vendedor, array('', '::Seleccione::'), ' ');
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
        ////
        $data['clientedos'] = $clientedos;
        $data['ruc_clientedos'] = $ruc_clientedos;
        $data['nombre_clientedos'] = $nombre_clientedos;
        $data['clientetres'] = $clientetres;
        $data['ruc_clientetres'] = $ruc_clientetres;
        $data['nombre_clientetres'] = $nombre_clientetres;
        ///
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;        
        /////
        $data['proveedordos'] = $proveedordos;
        $data['ruc_proveedordos'] = $ruc_proveedordos;
        $data['nombre_proveedordos'] = $nombre_proveedordos;
        $data['proveedortres'] = $proveedortres;
        $data['ruc_proveedortres'] = $ruc_proveedortres;
        $data['nombre_proveedortres'] = $nombre_proveedortres;
        /////
        
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
        ///
        $data['hoyvenc'] = $fechavenc;
        ////
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
        ////
        
        $data['representante'] = $representante;
        $data['oficina'] = $oficina;
        $data['numerocuenta'] = $numerocuenta;
        $data['dc'] = $dc;
        $data['direccion'] = $direccion;
        
        ///
        $data['direccionpago'] = $direccionpago;
        ///
        
        ///
        $data['cbo_dpto'] = $this->seleccionar_departamento($cbo_dpto);
        $data['cbo_prov'] = $this->seleccionar_provincia($cbo_dpto, $cbo_prov);
        $data['cbo_dist'] = $this->seleccionar_distritos($cbo_dpto,$cbo_prov, $cbo_dist);
        ///
//        $data['cbo_dpto'] = $cbo_dpto;
//        $data['cbo_prov'] = $cbo_prov;
//        $data['cbo_dist'] = $cbo_dist;
        
        
        ////
//        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

//        $data['detalle_comprobante'] = $detalle_comprobante;
        $this->layout->view('ventas/letracambio_nueva', $data);
    }

    public function comprobante_modificar() {
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
//        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
//            exit('{"result":"error", "campo":"observacion"}');

//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $prodcodigo = $this->input->post('prodcodigo');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
//        if (is_array($prodcodigo)) {
//            foreach ($prodcodigo as $indice => $valor) {
//                if ($flagGenInd[$indice] == 'I' && isset($_SESSION['serie']) && is_array($_SESSION['serie'][$valor])) {
//                    if (count($_SESSION['serie'][$valor]) != $prodcantidad[$indice])
//                        exit('{"result":"error2", "msj":"No ha ingresado todos los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
//                }else
//                    exit('{"result":"error2", "msj":"No ha ingresado los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
//            }
//        }

        $codigo = $this->input->post('codigo');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');

        $filter = new stdClass();
        $filter->FORPAP_Codigo = NULL;
        //if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
        $filter->FORPAP_Codigo = 1; //estaba asi  $this->input->post('forma_pago');
        $filter->LET_Observacion = strtoupper($this->input->post('observacion'));
        $filter->LET_Fecha = human_to_mysql($this->input->post('fecha'));
        ////
        $filter->LET_FechaVenc = human_to_mysql($this->input->post('fechavenc'));
        ////
        $filter->LET_Numero = $this->input->post('numero');
        $filter->LET_Serie =$this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->LET_descuento100 = $this->input->post('descuento');
        $filter->LET_igv100 = $this->input->post('igv');
        $filter->LET_TipoDocumento = $tipo_docu;
        
        ///
        $filter->LET_Banco = $this->input->post('cbobanco');
        ///

        $nombre = $this->input->post('nombre_cliente');

        if ($this->input->post('cliente') == 144 ||
                $this->input->post('cliente') == 135 ||
                $this->input->post('cliente') == 218 ||
                $this->input->post('cliente') == 1037)
            $filter->LET_NombreAuxiliar = $nombre;

        if ($tipo_oper == 'V'){
            $filter->CLIP_Codigo = $this->input->post('cliente');
            $filter->CLIPDOS_Codigo = $this->input->post('clientedos');
            $filter->CLIPTRES_Codigo = $this->input->post('clientetres');
        }else{
            $filter->PROVP_Codigo = $this->input->post('proveedor');
            $filter->PROVPDOS_Codigo = $this->input->post('proveedordos');
            $filter->PROVPTRES_Codigo = $this->input->post('proveedortres');
        }
//        $filter->PRESUP_Codigo = NULL;
//        if ($this->input->post('presupuesto') != '' && $this->input->post('presupuesto') != '0')
//            $filter->PRESUP_Codigo = $this->input->post('presupuesto');
//        $filter->OCOMP_Codigo = NULL;
//        if ($this->input->post('ordencompra') != '' && $this->input->post('ordencompra') != '0')
//            $filter->OCOMP_Codigo = $this->input->post('ordencompra');
//        $filter->GUIAREMP_Codigo = NULL;
//        if ($this->input->post('guiaremision') != '' && $this->input->post('guiaremision') != '0')
//            $filter->GUIAREMP_Codigo = $this->input->post('guiaremision');
//        $filter->LET_GuiaRemCodigo = strtoupper($this->input->post('guiaremision_codigo'));
//        $filter->LET_DocuRefeCodigo = strtoupper($this->input->post('docurefe_codigo'));
        //$filter->CPC_FlagEstado = $this->input->post('estado');
//        $filter->LET_ModoImpresion = '1';
//        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
//            $filter->LET_ModoImpresion = $this->input->post('modo_impresion');
//        if ($tipo_docu != 'B') {
//            $filter->LET_subtotal = $this->input->post('preciototal');
//            $filter->LET_descuento = $this->input->post('descuentotal');
//            $filter->LET_igv = $this->input->post('igvtotal');
//        } else {
//            $filter->LET_subtotal_conigv = $this->input->post('preciototal_conigv');
//            $filter->LET_descuento_conigv = $this->input->post('descuentotal_conigv');
//        }
        $filter->LET_total = $this->input->post('importetotal');
//        $filter->LET_Vendedor = NULL;
//        if ($this->input->post('vendedor') != '')
//            $filter->LET_Vendedor = $this->input->post('vendedor');

        ///
        $filter->LET_Representante = $this->input->post('representante');
        $filter->LET_Oficina = $this->input->post('txtoficina');
        $filter->LET_NumeroCuenta = $this->input->post('txtnumcuenta');
        $filter->LET_DC = $this->input->post('txtdc');
        $filter->LET_Direccion = $this->input->post('direccion');
        
        ///
        $filter->LET_DireccionPago = $this->input->post('direccionpago');
        ///
        
        $dep=$this->input->post('cboDepartamento');
        $pro=$this->input->post('cboProvincia');
        $dis=$this->input->post('cboDistrito');
        
        $ubigeo=''.$dep.$pro.$dis.'';
        
        $filter->LET_Ubigeo = $ubigeo;
        ///
        
        
        $this->letracambio_model->modificar_comprobante($codigo, $filter);


        ///producto detalle no tiene
//        $prodcodigo = $this->input->post('prodcodigo');
//        $flagBS = $this->input->post('flagBS');
//        $prodcantidad = $this->input->post('prodcantidad');
//        if ($tipo_docu != 'B') {
//            $prodpu = $this->input->post('prodpu');
//            $prodprecio = $this->input->post('prodprecio');
//            $proddescuento = $this->input->post('proddescuento');
//            $prodigv = $this->input->post('prodigv');
//        } else {
//            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
//            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
//        }
//        $prodimporte = $this->input->post('prodimporte');
//        $prodpu_conigv = $this->input->post('prodpu_conigv');
//        $produnidad = $this->input->post('produnidad');
//        $detaccion = $this->input->post('detaccion');
//        $detacodi = $this->input->post('detacodi');
//        $prodigv100 = $this->input->post('prodigv100');
//        $proddescuento100 = $this->input->post('proddescuento100');
//        $prodcosto = $this->input->post('prodcosto');
//        $proddescri = $this->input->post('proddescri');
//
//        if (is_array($detacodi) > 0) {
//            foreach ($detacodi as $indice => $valor) {
//                $detalle_accion = $detaccion[$indice];
//
//                $filter = new stdClass();
//                $filter->CPP_Codigo = $codigo;
//                $filter->PROD_Codigo = $prodcodigo[$indice];
//                if ($flagBS[$indice] == 'B')
//                    $filter->UNDMED_Codigo = $produnidad[$indice];
//                $filter->CPDEC_Cantidad = $prodcantidad[$indice];
//                if ($tipo_docu != 'B') {
//                    $filter->CPDEC_Pu = $prodpu[$indice];
//                    $filter->CPDEC_Subtotal = $prodprecio[$indice];
//                    $filter->CPDEC_Descuento = $proddescuento[$indice];
//                    $filter->CPDEC_Igv = $prodigv[$indice];
//                } else {
//                    $filter->CPDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
//                    $filter->CPDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
//                }
//                $filter->CPDEC_Total = $prodimporte[$indice];
//                $filter->CPDEC_Pu_ConIgv = $prodpu_conigv[$indice];
//                $filter->CPDEC_Descuento100 = $proddescuento100[$indice];
//                $filter->CPDEC_Igv100 = $prodigv100[$indice];
//                if ($tipo_oper == 'V')
//                    $filter->CPDEC_Costo = $prodcosto[$indice];
//                $filter->CPDEC_Descripcion = strtoupper($proddescri[$indice]);
//                $filter->CPDEC_Observacion = "";
//
//
//                if ($detalle_accion == 'n') {
//                    $this->comprobantedetalle_model->insertar($filter);
//                } elseif ($detalle_accion == 'm') {
//                    $this->comprobantedetalle_model->modificar($valor, $filter);
//                } elseif ($detalle_accion == 'e') {
//                    $this->comprobantedetalle_model->eliminar($valor);
//                }
//            }
//        }
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }

    public function comprobante_eliminar() {
        

        $comprobante = $this->input->post('comprobante');
        $this->comprobante_model->eliminar_comprobante($comprobante);
    }

    public function comprobante_buscar() {
        
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

    public function comprobante_ver_pdf($codigo, $tipo_docu = 'F') {

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
                }else {
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
            default: comprobante_ver_pdf_formato1($codigo);
                break;
        }
    }

    public function comprobante_ver_pdf_formato1($codigo) {
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
        $modo_impresion = ((int) $datos_comprobante[0]->CPC_ModoImpresion > 0 ? $datos_comprobante[0]->CPC_ModoImpresion : '1');

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
        $this->cezpdf->ezText(utf8_decode_seguro((int) substr($fecha, 0, 2)), 10, array("leading" => 0, "left" => 380));
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

    public function comprobante_ver_pdf_formato1_boleta($codigo) {
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

    public function comprobante_ver_pdf_conmenbrete($tipo_oper, $codigo, $tipo_docu = 'F', $img = 0) {

        switch (FORMATO_IMPRESION) {

            case 1: //Formato para ferresat
                switch ($tipo_docu) {
                    case 'F':
                        $this->comprobante_ver_pdf_conmenbrete_formato1($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'B':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_boleta($tipo_oper, $codigo, $tipo_docu, $img);
                        break;
                    case 'N':
                        $this->comprobante_ver_pdf_conmenbrete_formato1_com($codigo,$img);
                        break;
                }
                break;

            default: $this->comprobante_ver_pdf_conmenbrete_formato1($codigo, $tipo_docu, $tipo_oper, $img = 0);
                break;
        }
    }

    public function comprobante_ver_pdf_conmenbrete1($tipo_oper, $codigo, $tipo_docu = 'F', $img = 0) {




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
                        $this->comprobante_ver_pdf_conmenbrete_formato1_com($codigo,$img);
                        break;
                }
                break;

            default: $this->comprobante_ver_pdf_conmenbrete_formato11($codigo, $tipo_docu, $tipo_oper, $img = 0);
                break;
        }
    }

    public function comprobante_ver_html($codigo, $tipo_docu = 'F') {
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

    public function comprobante_ver_pdf_conmenbrete_formato1_com($codigo,$img) {
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
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $datos_moneda = $this->moneda_model->obtener($moneda);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $simb = $datos_moneda[0]->MONED_Simbolo;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc = $datos_persona[0]->PERSC_Ruc;
            ///stv
            $dni= $datos_persona[0]->PERSC_NumeroDocIdentidad;
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
                $datos_comprobante[0]->CLIP_Codigo == 1037)
            $nombre_cliente = $datos_comprobante[0]->CPC_NombreAuxiliar;

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);
        
        ///////stv
        if($img==0){
            $notimg = ""; //madyplac_com.jpg
                    $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
        }else{
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

            if($img==0){
            $this->cezpdf->addText(320, 762, 12, utf8_decode_seguro($serie . ' - ' . $numero));
            }

            if($img!=0){

            $fecha_text= utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . mes_textual(substr($fecha, 3, 2)) . '           ' . substr($fecha, 8,4)); 
            $this->cezpdf->addText(296, 754, 10, utf8_decode_seguro($fecha_text));
            
            $this->cezpdf->addText(62, 698, 8, utf8_decode_seguro($nombre_cliente));

            $ruc= substr($ruc, 0, 1).'    '.substr($ruc, 1, 1).'   '.substr($ruc, 2, 1).'   '.substr($ruc, 3, 1).'  '.substr($ruc, 4, 1).'   '.substr($ruc, 5, 1).'   '.substr($ruc, 6, 1).'   '.substr($ruc, 7, 1).'    '.substr($ruc, 8, 1).'   '.substr($ruc, 9, 1).'   '.substr($ruc, 10, 1);    
            $this->cezpdf->addText(296, 726, 9, utf8_decode_seguro($ruc));

            if($tipo==0){
            $this->cezpdf->addText(298, 678, 9, utf8_decode_seguro($dni));
            }
//            $this->cezpdf->addText(378,790, 11, utf8_decode_seguro('X'));
            /////
            $this->cezpdf->addText(60, 684, 8, utf8_decode_seguro($direccion));

            $y = 646;


            foreach($detalle_comprobante as $indice => $valor) {
                $cod_prod = $valor->PROD_CodigoUsuario;
                $cant = $valor->CPDEC_Cantidad;
                $pu = number_format($valor->CPDEC_Pu_ConIgv, 2);
                $st = number_format($valor->CPDEC_Total, 2);
                $producto = substr($valor->PROD_Nombre, 0, 25);
                $unidad = $valor->UNDMED_Simbolo;

                $this->cezpdf->addText(60,$y, 8, utf8_decode_seguro($producto. ' - ' . $unidad));
                $this->cezpdf->addText(32, $y, 9, utf8_decode_seguro($cant));
                $this->cezpdf->addText(326, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($pu, 2)));
                $this->cezpdf->addText(376, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($st, 2)));
                $y-=21;
            }


            $this->cezpdf->addText(26, 296, 8, 'SON: '.strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);

            $this->cezpdf->addText(358, 270, 10, utf8_decode_seguro($simb . ' ' . number_format($total, 2)));
    
            }else{
                
            $fecha_text= utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . mes_textual(substr($fecha, 3, 2)) . '           ' . substr($fecha, 8,4)); 
            $this->cezpdf->addText(296, 754, 10, utf8_decode_seguro($fecha_text));
            
            $this->cezpdf->addText(62, 698, 8, utf8_decode_seguro($nombre_cliente));

            $ruc= substr($ruc, 0, 1).'    '.substr($ruc, 1, 1).'   '.substr($ruc, 2, 1).'   '.substr($ruc, 3, 1).'  '.substr($ruc, 4, 1).'   '.substr($ruc, 5, 1).'   '.substr($ruc, 6, 1).'   '.substr($ruc, 7, 1).'    '.substr($ruc, 8, 1).'   '.substr($ruc, 9, 1).'   '.substr($ruc, 10, 1);    
            $this->cezpdf->addText(296, 726, 9, utf8_decode_seguro($ruc));
            
            ///stv
            if($tipo==0){
            $this->cezpdf->addText(298, 678, 9, utf8_decode_seguro($dni));
            }
//            $this->cezpdf->addText(378,790, 11, utf8_decode_seguro('X'));
            /////
            $this->cezpdf->addText(60, 684, 8, utf8_decode_seguro($direccion));

            $y = 646;
            
            foreach($detalle_comprobante as $indice => $valor) {
                $cod_prod = $valor->PROD_CodigoUsuario;
                $cant = $valor->CPDEC_Cantidad;
                $pu = number_format($valor->CPDEC_Pu_ConIgv, 2);
                $st = number_format($valor->CPDEC_Total, 2);
                $producto = substr($valor->PROD_Nombre, 0, 25);
                $unidad = $valor->UNDMED_Simbolo;

                $this->cezpdf->addText(60,$y, 8, utf8_decode_seguro($producto. ' - ' . $unidad));
                $this->cezpdf->addText(32, $y, 9, utf8_decode_seguro($cant));
                $this->cezpdf->addText(326, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($pu, 2)));
                $this->cezpdf->addText(376, $y, 9, utf8_decode_seguro($simb . ' ' . number_format($st, 2)));
                $y-=21;
            }


            $this->cezpdf->addText(26, 296, 8, 'SON: '.strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);

            $this->cezpdf->addText(358, 270, 10, utf8_decode_seguro($simb . ' ' . number_format($total, 2)));

            }
        }
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

	
	
	
	
	
	
	////stv aumentado
	
	    public function ali_precio($precio=""){
                if($precio!=""){
                $pri_precio=substr($precio,0,3);
                $ter_precio=substr(substr($precio,strlen($pri_precio)),strpos(substr($precio,strlen($pri_precio)), "."));
                $seg_precio=substr(substr($precio,strlen($pri_precio)),0,strlen(substr($precio,strlen($pri_precio)))-(strlen($ter_precio)));
                $nseg_precio=strlen($seg_precio);
                $nn=5-$nseg_precio;
                $esp="";
                for($j=0;$j<$nn;$j++){
                if($j==1){
                $esp=$esp." ";
                }else{
                $esp=$esp."  ";
                }
                }
                $precio=$pri_precio.$esp.$seg_precio.$ter_precio;

                return $precio;

                }
    }
	
	//////
	
	
	
	
    public function comprobante_ver_pdf_conmenbrete_formato1($tipo_oper, $codigo, $tipo_docu = 'F', $img) {

        $hoy = date("Y-m-d");
        $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $serie = $datos_comprobante[0]->LET_Serie;
        $numero = $datos_comprobante[0]->LET_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $clientedos = $datos_comprobante[0]->CLIPDOS_Codigo;
        $clientetres = $datos_comprobante[0]->CLIPTRES_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $proveedordos = $datos_comprobante[0]->PROVPDOS_Codigo;        
        $proveedortres = $datos_comprobante[0]->PROVPTRES_Codigo;                
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $banco= $datos_comprobante[0]->LET_Banco;
        $subtotal = $datos_comprobante[0]->LET_subtotal;
        $descuento = $datos_comprobante[0]->LET_descuento;
        $igv = $datos_comprobante[0]->LET_igv;
        $total = $datos_comprobante[0]->LET_total;
        $subtotal_conigv = $datos_comprobante[0]->LET_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->LET_descuento_conigv;
        $igv100 = $datos_comprobante[0]->LET_igv100;
        $descuento100 = $datos_comprobante[0]->LET_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->LET_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->LET_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->LET_Observacion;
        $modo_impresion = $datos_comprobante[0]->LET_ModoImpresion;
        $estado = $datos_comprobante[0]->LET_FlagEstado;
        $almacen = $datos_comprobante[0]->ALMAP_Codigo;
        $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
        $fechavenc = mysql_to_human($datos_comprobante[0]->LET_FechaVenc);
        $vendedor = $datos_comprobante[0]->LET_Vendedor;
        $tdc = $datos_comprobante[0]->LET_TDC;
        $representante = $datos_comprobante[0]->LET_Representante;
        $oficina = $datos_comprobante[0]->LET_Oficina;
        $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
        $dc = $datos_comprobante[0]->LET_DC;
        $direccion = $datos_comprobante[0]->LET_Direccion;
        $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
        
        
        //DISPARADOR BEGIN
        if ($estado == 2) {
            $mueve = 1;
            $filter = new stdClass();
            $filter->LET_TipoOperacion = $tipo_oper;
            $filter->LET_TipoDocumento = $tipo_docu;
            $filter->MONED_Codigo = $moneda;
            $filter->LET_total = $total;
            $filter->LET_Fecha = $hoy;
            $filter->LET_FechaVenc = $hoyvenc;
            $filter->FORPAP_Codigo = $forma_pago;
            $filter->CLIP_Codigo = $cliente;
            ///
            $filter->CLIPDOS_Codigo = $clientedos;
            $filter->CLIPTRES_Codigo = $clientetres;
            //
            $filter->PROVP_Codigo = $proveedor;
            ////
            $filter->PROVPDOS_Codigo = $proveedordos;
            $filter->PROVPTRES_Codigo = $proveedortres;                        
            ////
            $filter->LET_Numero = $numero;
            $compania = $this->somevar['compania'];


            if ($tipo_docu == 'F') {
                $tipo = 16;
            }
            if ($tipo_docu == 'B') {
                $tipo = 9;
            }
            if ($tipo_docu == 'N') {
                $tipo = 14;
            }

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);

            if ($cofiguracion_datos) {
                $filter->LET_Numero = sprintf("%07d", $cofiguracion_datos[0]->CONFIC_Numero + 1);
            }
            
            
            ///por mientras stv
            $this->letracambio_model->aprobar_estadoletra($codigo,$filter);
            ///
            
            //bloqueado stv  pa stock
//            $this->letracambio_model->insertar_disparador($codigo, $filter);
            //
            
            
            ///bloqueado por mientras guia entrada salida
            //
//            if ($tipo_oper == 'V') {
//                if ($mueve == 1) {
//                    $filter3 = new stdClass();
//                    $filter3->TIPOMOVP_Codigo = 1;
//                    $filter3->ALMAP_Codigo = $almacen;
//                    $filter3->CLIP_Codigo = $cliente;
//                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
//                    $filter3->GUIASAC_Fecha = $hoy;
//                    $filter3->GUIASAC_Observacion = $observacion;
//                    $filter3->USUA_Codigo = $this->somevar['user'];
//                    $filter3->GUIASAC_Automatico = 1;
//                    $guia_id = $this->guiasa_model->insertar($filter3);
//                    $filter->GUIASAP_Codigo = $guia_id;
//                    $filter->CPC_FlagMueveStock = '1';
//                } else {
//                    $filter->CPC_FlagMueveStock = '0';
//                }
//            } else {
//                if ($mueve == 1) {
//                    $filter3 = new stdClass();
//                    $filter3->TIPOMOVP_Codigo = 2;
//                    $filter3->ALMAP_Codigo = $almacen;
//                    $filter3->PROVP_Codigo = $proveedor;
//                    $filter3->DOCUP_Codigo = $tipo_docu == 'F' ? 8 : 9;
//                    $filter3->GUIAINC_Fecha = $hoy;
//                    $filter3->GUIAINC_Observacion = $observacion;
//                    $filter3->USUA_Codigo = $this->somevar['user'];
//                    $filter3->GUIAINC_Automatico = 1;
//                    $guia_id = $this->guiain_model->insertar($filter3);
//                    $filter->GUIAINP_Codigo = $guia_id;
//                    $filter->CPC_FlagMueveStock = '1';
//                } else {
//                    $filter->CPC_FlagMueveStock = '0';
//                }
//            }
//            $a_filter = new stdClass();
//            if ($tipo_oper == 'V')
//                $a_filter->GUIASAP_Codigo = $guia_id;
//            else
//                $a_filter->GUIAINP_Codigo = $guia_id;
            //
            /////fin mientras
            
            

//            $this->letracambio_model->modificar_comprobante($codigo, $a_filter);

            
            //no hay detalle en letra
//            $detalle = $this->comprobantedetalle_model->listar($codigo);
//            $lista_detalles = array();
//            if (count($detalle) > 0) {
//                foreach ($detalle as $indice => $valor) {
//                    $detacodi = $valor->CPDEP_Codigo;
//                    $producto = $valor->PROD_Codigo;
//                    $unidad = $valor->UNDMED_Codigo;
//                    $cantidad = $valor->CPDEC_Cantidad;
//                    $pu = $valor->CPDEC_Pu;
//                    $subtotal = $valor->CPDEC_Subtotal;
//                    $igv = $valor->CPDEC_Igv;
//                    $descuento = $valor->CPDEC_Descuento;
//                    $total = $valor->CPDEC_Total;
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                    $subtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
//                    $descuento_conigv = $valor->CPDEC_Descuento_ConIgv;
//                    $descuento100 = $valor->CPDEC_Descuento100;
//                    $igv100 = $valor->CPDEC_Igv100;
//                    $observacion = $valor->CPDEC_Observacion;
//                    $datos_producto = $this->producto_model->obtener_producto($producto);
//                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
//                    $datos_unidad = $this->unidadmedida_model->obtener($unidad);
//                    $GenInd = $valor->CPDEC_GenInd;
//                    $costo = $valor->CPDEC_Costo;
//                    $nombre_producto = ($valor->CPDEC_Descripcion != '' ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
//                    $nombre_producto = str_replace('\\', '', $nombre_producto);
//                    $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
//                    $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
//                    $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';
//
//
//                    if ($tipo_oper == 'V') {
//                        $filter4 = new stdClass();
//                        $filter4->GUIASAP_Codigo = $guia_id;
//                        $filter4->PRODCTOP_Codigo = $producto;
//                        $filter4->UNDMED_Codigo = $unidad;
//                        $filter4->GUIASADETC_Cantidad = $cantidad;
//                        $filter4->GUIASADETC_Costo = $subtotal_conigv;
//                        $filter4->GUIASADETC_GenInd = $GenInd;
//                        $filter4->GUIASADETC_Descripcion = $nombre_producto;
//                        ;
//                        $this->guiasadetalle_model->insertar($filter4);
//                    } else {
//                        $filter4 = new stdClass();
//                        $filter4->GUIAINP_Codigo = $guia_id;
//                        $filter4->PRODCTOP_Codigo = $producto;
//                        $filter4->UNDMED_Codigo = $unidad;
//                        $filter4->GUIAINDETC_Cantidad = $cantidad;
//                        $filter4->GUIIAINDETC_GenInd = $subtotal_conigv;
//                        $filter4->GUIAINDETC_Costo = $costo; // No estoy muy seguro de si debe agarrar este precio, porque puede ser $costo, $venta
//                        $filter4->GUIAINDETC_Descripcion = $GenInd;
//                        $this->guiaindetalle_model->insertar($filter4);
//                    }
//                }
//            }
            /////fin detalle no hay
            
        }
// DISPARADOR END

        if ($tipo_oper == 'V') {
            if ($img == 1) {
                $notimg = "";
            } else if ($img == 0) {
                $notimg = "letra.jpg";
            }
        } else {
            if ($img == 1) {
                $notimg = "";
            } else if ($img == 0) {
                $notimg = "letra_proveedor_1.jpg";
            }
        }

        if ($tipo_oper == 'V') {


            $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            $serie = $datos_comprobante[0]->LET_Serie;
            $numero = $datos_comprobante[0]->LET_Numero;
            $proveedor = $datos_comprobante[0]->CLIP_Codigo;
            $proveedordos = $datos_comprobante[0]->CLIPDOS_Codigo;
            $proveedortres = $datos_comprobante[0]->CLIPTRES_Codigo;            
            $subtotal = $datos_comprobante[0]->LET_subtotal;
            $descuento = $datos_comprobante[0]->LET_descuento;
            $igv = $datos_comprobante[0]->LET_igv;
            $igv100 = $datos_comprobante[0]->LET_igv100;
            $descuento100 = $datos_comprobante[0]->LET_descuento100;
            $total = $datos_comprobante[0]->LET_total;
            $hora = $datos_comprobante[0]->LET_Hora;
            $observacion = $datos_comprobante[0]->LET_Observacion;
            $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
            $fechavenc = mysql_to_human($datos_comprobante[0]->LET_FechaVenc);
            $vendedor = $datos_comprobante[0]->USUA_Codigo;
            $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
            $empresa = $datos_proveedor[0]->EMPRP_Codigo;
            $persona = $datos_proveedor[0]->PERSP_Codigo;
            $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
            //////////
            $datos_proveedordos = $this->cliente_model->obtener_datosCliente($proveedordos);
            $empresados = $datos_proveedordos[0]->EMPRP_Codigo;
            $personados = $datos_proveedordos[0]->PERSP_Codigo;
            $tipodos = $datos_proveedordos[0]->CLIC_TipoPersona;
            
            $datos_proveedortres = $this->cliente_model->obtener_datosCliente($proveedortres);
            $empresatres = $datos_proveedortres[0]->EMPRP_Codigo;
            $personatres = $datos_proveedortres[0]->PERSP_Codigo;
            $tipotres = $datos_proveedortres[0]->CLIC_TipoPersona;
            //////////
            $tipo_docu = $datos_comprobante[0]->LET_TipoDocumento;
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
            $datos_banco = $this->banco_model->obtener($datos_comprobante[0]->BANP_Codigo);
            $banco_nombre = (count($datos_banco) > 0 ? $datos_banco[0]->BANC_Nombre : 'BANCO DE CREDITO');
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $temp = $this->usuario_model->obtener($vendedor);
            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
            $vendedor = $temp[0]->PERSC_Nombre;
            
            /////
            $representante = $datos_comprobante[0]->LET_Representante;
            $oficina = $datos_comprobante[0]->LET_Oficina;
            $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
            $dc = $datos_comprobante[0]->LET_DC;
            $direccion = $datos_comprobante[0]->LET_Direccion;
            $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
            /////

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
                
                ////
                $datos_personados = $this->persona_model->obtener_datosPersona($personados);
                $nombre_clientedos = $datos_personados[0]->PERSC_Nombre . " " . $datos_personados[0]->PERSC_ApellidoPaterno . " " . $datos_personados[0]->PERSC_ApellidoMaterno;
                $rucdos = $datos_personados[0]->PERSC_Ruc;
                $telefonodos = $datos_personados[0]->PERSC_Telefono;
                $movildos = $datos_personados[0]->PERSC_Movil;
                $direcciondos = $datos_personados[0]->PERSC_Direccion;
                $faxsdos = $datos_personados[0]->PERSC_Fax;
                
                $datos_personatres = $this->persona_model->obtener_datosPersona($personatres);
                $nombre_clientetres = $datos_personatres[0]->PERSC_Nombre . " " . $datos_personatres[0]->PERSC_ApellidoPaterno . " " . $datos_personatres[0]->PERSC_ApellidoMaterno;
                $ructres = $datos_personatres[0]->PERSC_Ruc;
                $telefonotres = $datos_personatres[0]->PERSC_Telefono;
                $moviltres = $datos_personatres[0]->PERSC_Movil;
                $direcciontres = $datos_personatres[0]->PERSC_Direccion;
                $faxstres = $datos_personatres[0]->PERSC_Fax;
                ////
                
            } elseif ($tipo == 1) {
                $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                $ruc = $datos_empresa[0]->EMPRC_Ruc;
                $telefono = $datos_empresa[0]->EMPRC_Telefono;
                $movil = $datos_empresa[0]->EMPRC_Movil;
                $fax = $datos_empresa[0]->EMPRC_Fax;
                $direccion = $datos_empresa[0]->EMPRC_Direccion;
                $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                
                ////
                $datos_empresados = $this->empresa_model->obtener_datosEmpresa($empresados);
                $nombre_clientedos = $datos_empresados[0]->EMPRC_RazonSocial;
                $rucdos = $datos_empresados[0]->EMPRC_Ruc;
                $telefonodos = $datos_empresados[0]->EMPRC_Telefono;
                $movildos = $datos_empresados[0]->EMPRC_Movil;
                $faxdos = $datos_empresados[0]->EMPRC_Fax;
                $direcciondos = $datos_empresados[0]->EMPRC_Direccion;
                $emp_direcciondos = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresados);
                
                $datos_empresatres = $this->empresa_model->obtener_datosEmpresa($empresatres);
                $nombre_clientetres = $datos_empresatres[0]->EMPRC_RazonSocial;
                $ructres = $datos_empresatres[0]->EMPRC_Ruc;
                $telefonotres = $datos_empresatres[0]->EMPRC_Telefono;
                $moviltres = $datos_empresatres[0]->EMPRC_Movil;
                $faxtres = $datos_empresatres[0]->EMPRC_Fax;
                $direcciontres = $datos_empresatres[0]->EMPRC_Direccion;
                $emp_direcciontres = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresatres);
                /////
                
            }
//            $detalle_comprobante = $this->obtener_lista_detalles($codigo);

            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));

            /* Cabecera */
			//aumentado stv
			$this->cezpdf->ezText('', '', array('leading' => 127));
            if ($img != 1) {
                $this->cezpdf->ezText($serie, 15, array("leading" => -20, 'left' => 360));
                $this->cezpdf->ezText($numero, 15, array("leading" => 0, 'left' => 410));
            } else {
                $this->cezpdf->ezText('', 15, array("leading" => -22, 'left' => 400));
                $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 440));
            }
            $this->cezpdf->ezText('', '', array("leading" => 40));

            $fecha_text2= utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . strtoupper(mes_textual(substr($fecha, 3, 2))) . '                     ' . substr($fecha, 8,4)); 
            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));
            $this->cezpdf->addText(92, 696, 9, utf8_decode_seguro($fecha_text));
            $this->cezpdf->addText(278, 432, 9, utf8_decode_seguro($fecha_text2));
			
//            $this->cezpdf->addText(300, 636, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
//            $this->cezpdf->addText(300, 626, 9, utf8_decode_seguro("Modo : " . $nombre_formapago));
            //$this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));
	    $ruc= substr($ruc, 0, 1).'  '.substr($ruc, 1, 1).'  '.substr($ruc, 2, 1).'  '.substr($ruc, 3, 1).'  '.substr($ruc, 4, 1).'  '.substr($ruc, 5, 1).'  '.substr($ruc, 6, 1).'  '.substr($ruc, 7, 1).'  '.substr($ruc, 8, 1).' '.substr($ruc, 9, 1).' '.substr($ruc, 10, 1);
            $this->cezpdf->addText(452, 702, 12, utf8_decode_seguro($ruc));
            $this->cezpdf->addText(112, 684, 9, utf8_decode_seguro($nombre_cliente));
            $this->cezpdf->addText(112, 672, 9, utf8_decode_seguro($direccion));
//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
			////////
			
			/*    stv comentado
            $this->cezpdf->ezText('', '', array('leading' => 127));
            if ($img != 1) {
                $this->cezpdf->ezText($serie, 15, array("leading" => -22, 'left' => 420));
                $this->cezpdf->ezText($numero, 15, array("leading" => 0, 'left' => 450));
            } else {
                $this->cezpdf->ezText('', 15, array("leading" => -22, 'left' => 400));
                $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 440));
            }
            $this->cezpdf->ezText('', '', array("leading" => 40));
            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . ' ' . mes_textual(substr($fecha, 3, 2)) . '           ' . substr($fecha, 6, 4));
            $this->cezpdf->addText(60, 689, 9, utf8_decode_seguro($fecha_text));
            $this->cezpdf->addText(300, 680, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
            $this->cezpdf->addText(300, 670, 9, utf8_decode_seguro("Modo : " . $nombre_formapago));
            $this->cezpdf->addText(490, 658, 9, utf8_decode_seguro($ruc));
            $this->cezpdf->addText(70, 673, 9, utf8_decode_seguro($nombre_cliente));
            $this->cezpdf->addText(70, 660, 9, utf8_decode_seguro($direccion));
            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora));
			*/
			
			
            /////no hay detalle
            /* Listado de detalles */
//            $posicionX = 0;
//            $posicionY = 625;
//            $db_data = array();
//            $i = 640;
//            foreach ($detalle_comprobante as $indice => $valor) {
//                $c = 0;
                //$array_producto = explode('/', $valor->PROD_Nombre);
				
				
				
//				$prod_nombre=$valor->PROD_Nombre;
//				
//				if(strpos($prod_nombre,"/")){
//				$prod_nombre=substr($valor->PROD_Nombre,0,strpos($valor->PROD_Nombre,"/"));
//				}
				
				
				
//                $producto = $valor->PROD_CodigoUsuario;
//                $posicionX = 10;
//                $unidad = $valor->UNDMED_Simbolo;
//                if ($valor->CPDEC_Pu_ConIgv != '')
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                else
//                    $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
//                $this->cezpdf->addText(50, $i, 9, $producto);
                
                ///aumentado stv
                //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                
//                $this->cezpdf->addText(124, $i, 9, utf8_decode_seguro($prod_nombre)." - ".$unidad);
                ///
                
				

//                $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
//                $this->cezpdf->addText(82, $i, 9, $valor->CPDEC_Cantidad);
				
				
				
				/////alineado ppu ppt

//                $ppu=$moneda_simbolo . number_format($pu_conigv, 2);
//                $ppu=$this->ali_precio($ppu);
//                $this->cezpdf->addText(484, $i, 9, $ppu);
//
//                $ppt=$moneda_simbolo . number_format($valor->CPDEC_Total, 2);
//                $ppt=$this->ali_precio($ppt);
//                $this->cezpdf->addText(538, $i, 9, $ppt);

                ///////////
				
				
				
                //$this->cezpdf->addText(484, $i, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2));
                //$this->cezpdf->addTextWrap(470, $i, 40, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2), "right");//revisar http://pubsvn.ez.no/doxygen/4.0/html/classCpdf.html#a4c3091f0936a733aa7e7ff98b876f3b1
                //$this->cezpdf->addText(538, $i, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                //$this->cezpdf->addTextWrap(520, $i, 44, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2),"right");
//                $i-=13;
//
//
//                $posicionY-=17;
//            }
            /////
            
			/*
			$posicionX = 0;
            $posicionY = 625;
            $db_data = array();
            $i = 620;
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
                $this->cezpdf->addText(50, $i, 9, $producto);
                
                ///aumentado stv
                //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                
                $this->cezpdf->addText(120, $i, 8, utf8_decode_seguro($valor->PROD_Nombre . '  --- ' . $unidad));
                ///
//                $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
                $this->cezpdf->addText(435, $i, 9, $valor->CPDEC_Cantidad);
                $this->cezpdf->addText(470, $i, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2));
                $this->cezpdf->addText(520, $i, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                $i-=10;


                $posicionY-=18;
            }
			*/

			
            //$this->cezpdf->addText(70, 127, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));

            /* Totales */

            $this->cezpdf->addText(108,456, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ');
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
            //$this->cezpdf->addText(458, 300, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));
            
			
			
			
			/////alineado pps  ppi  ppto

//                $pps=$moneda_simbolo . (number_format($subtotal - $descuento, 2));
//                $pps=$this->ali_precio($pps);
//                $this->cezpdf->addText(538, 438, 9, $pps);
//
//                $ppi=$moneda_simbolo . number_format($igv, 2);
//                $ppi=$this->ali_precio($ppi);
//                $this->cezpdf->addText(538, 424, 9, $ppi);

				
//				if(strlen(substr($total,0,strpos($total,'.')))==2){
//				$ppto=$moneda_simbolo .' '. number_format(($total), 2);
//                $ppto=$this->ali_precio($ppto);
//                $this->cezpdf->addText(540, 410, 9, $ppto);
//
//				}else{	
//				
//                $ppto=$moneda_simbolo .' '. number_format(($total), 2);
//                $ppto=$this->ali_precio($ppto);
//                $this->cezpdf->addText(538, 410, 9, $ppto);
//
//				}

            $this->cezpdf->addText(538, 410, 9, $total);
            
            ///////////
			
			
			
			//$this->cezpdf->addText(538, 438, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
            //$this->cezpdf->addText(538, 424, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
            //$this->cezpdf->addText(538, 410, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
			
			/*
			$this->cezpdf->addText(69, 425, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
            $this->cezpdf->addText(50, 445, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
            $this->cezpdf->addText(160, 445, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));
            $this->cezpdf->addText(305, 445, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
            $this->cezpdf->addText(410, 445, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
            $this->cezpdf->addText(500, 445, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
			*/
            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
        }

        else {
            $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
            $serie = $datos_comprobante[0]->LET_Serie;
            $numero = $datos_comprobante[0]->LET_Numero;
            $proveedor = $datos_comprobante[0]->PROVP_Codigo;
            $proveedordos = $datos_comprobante[0]->PROVPDOS_Codigo;
            $proveedortres = $datos_comprobante[0]->PROVPTRES_Codigo; 
            $subtotal = $datos_comprobante[0]->LET_subtotal;
            $descuento = $datos_comprobante[0]->LET_descuento;
            $igv = $datos_comprobante[0]->LET_igv;
            $total = $datos_comprobante[0]->LET_total;
            $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
            $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
            $ruc = $datos_proveedor[0]->EMPRC_Ruc;
            $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
            ///
            $datos_proveedordos = $this->proveedor_model->obtener_Proveedor($proveedordos);
            $rucdos = $datos_proveedordos[0]->EMPRC_Ruc;
            $empresados = $datos_proveedordos[0]->EMPRC_RazonSocial;
            
            $datos_proveedortres = $this->proveedor_model->obtener_Proveedor($proveedortres);
            $ructres = $datos_proveedortres[0]->EMPRC_Ruc;
            $empresatres = $datos_proveedortres[0]->EMPRC_RazonSocial;
            ////
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
            ///
            $datos_banco = $this->banco_model->obtener($datos_comprobante[0]->BANP_Codigo);
            $banco_nombre = (count($datos_banco) > 0 ? $datos_banco[0]->BANC_Nombre : 'BANCO DE CREDITO');            
            ////
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $array_fecha = explode("/", $fecha);
            $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
            
            
            /////
            $representante = $datos_comprobante[0]->LET_Representante;
            $oficina = $datos_comprobante[0]->LET_Oficina;
            $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
            $dc = $datos_comprobante[0]->LET_DC;
            $direccion = $datos_comprobante[0]->LET_Direccion;
            $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
            /////
            
            
            //$detalle_comprobante = $this->obtener_lista_detalles($codigo);
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
//            /* Cabecera */
            $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
            $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));

//            /* Datos del cliente */
            $this->cezpdf->ezText(utf8_decode_seguro("IMPORTACIONES IMPACTO SAC"), 9, array("leading" => 24, "left" => 10));
            $this->cezpdf->ezText('20527033798', 9, array("leading" => 12, "left" => 10));

            //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
            $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
            $this->cezpdf->ezText(utf8_decode_seguro((int) substr($fecha, 0, 2)), 9, array("leading" => 0, "left" => 458));
            $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 472));
            $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" => 515));
            $this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
            $this->cezpdf->ezText($serie, 18, array("leading" => -55, 'left' => 395));
            $this->cezpdf->ezText($numero, 18, array("leading" => 0, 'left' => 440));
            
            
            
            
            /////no tiene detalle letra
            /* Listado de detalles */
//            $posicionX = 0;
//            $posicionY = 640;
//            $db_data = array();
//
//            foreach ($detalle_comprobante as $indice => $valor) {
//                $c = 0;
//                $array_producto = explode('/', $valor->PROD_Nombre);
//                $producto = $valor->PROD_CodigoUsuario;
//
////                $ser = "";
////                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);
//
//                $posicionX = 10;
//                if ($valor->CPDEC_Pu_ConIgv != '')
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                else
//                    $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
//                $posicionX+=30;
//                $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
//                $posicionX+=30;
//                $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));
////                $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($array_producto[0]));
////                $this->cezpdf->addText(120, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                $posicionX+=380;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
//                $posicionX+=35;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));
//                $posicionX+=55;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
//
//
////
////                if (count($datos_serie) > 0) {
////                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
////                    for ($i = 0; $i < count($datos_serie); $i++) {
////                        $c = $c + 1;
////                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
////
////                        $ser = $ser . " /" . $seriecodigo;
////
////                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
////                        if ($c == 8) {
////                            $posicionY-=10;
////                            $c = 0;
////                            $ser = "";
////                        }
////                    }
////                }
//                $posicionY-=40;
//            }
            /* Totales */
//            $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

//            $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
//
//            $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));
//
//            $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
//            $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
            $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));

            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
        }
    }

    public function comprobante_ver_pdf_conmenbrete_formato11($tipo_oper, $codigo, $tipo_docu = 'F', $img) {



        if ($tipo_oper == 'V') {
            if ($img == 1) {
                $notimg = "";
            } else if ($img == 0) {
                $notimg = "letra.jpg";
            }
        } else {
            if ($img == 1) {
                $notimg = "";
            } else if ($img == 0) {
                $notimg = "letra_proveedor_1.jpg";
            }
        }

        if ($tipo_oper == 'V') {


            $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
            
            ////
            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            $serie = $datos_comprobante[0]->LET_Serie;
            $numero = $datos_comprobante[0]->LET_Numero;
            $proveedor = $datos_comprobante[0]->CLIP_Codigo;
            $proveedordos = $datos_comprobante[0]->CLIPDOS_Codigo;
            $proveedortres = $datos_comprobante[0]->CLIPTRES_Codigo;            
            $subtotal = $datos_comprobante[0]->LET_subtotal;
            $descuento = $datos_comprobante[0]->LET_descuento;
            $igv = $datos_comprobante[0]->LET_igv;
            $igv100 = $datos_comprobante[0]->LET_igv100;
            $descuento100 = $datos_comprobante[0]->LET_descuento100;
            $total = $datos_comprobante[0]->LET_total;
            $hora = $datos_comprobante[0]->LET_Hora;
            $observacion = $datos_comprobante[0]->LET_Observacion;
            $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
            $fechavenc = mysql_to_human($datos_comprobante[0]->LET_FechaVenc);
            $vendedor = $datos_comprobante[0]->USUA_Codigo;
            
            $direccionpago = $datos_comprobante[0]->LET_DireccionPago;
            
            ////

            ////
            //////////
            $datos_proveedor = $this->cliente_model->obtener_datosCliente($proveedor);
            $empresa = $datos_proveedor[0]->EMPRP_Codigo;
            $persona = $datos_proveedor[0]->PERSP_Codigo;
            $tipo = $datos_proveedor[0]->CLIC_TipoPersona;
            
            $datos_proveedordos = $this->cliente_model->obtener_datosCliente($proveedordos);
            $empresados = $datos_proveedordos[0]->EMPRP_Codigo;
            $personados = $datos_proveedordos[0]->PERSP_Codigo;
            $tipodos = $datos_proveedordos[0]->CLIC_TipoPersona;
            
            $datos_proveedortres = $this->cliente_model->obtener_datosCliente($proveedortres);
            $empresatres = $datos_proveedortres[0]->EMPRP_Codigo;
            $personatres = $datos_proveedortres[0]->PERSP_Codigo;
            $tipotres = $datos_proveedortres[0]->CLIC_TipoPersona;
            //////////
            $tipo_docu = $datos_comprobante[0]->LET_TipoDocumento;
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
            $datos_banco = $this->banco_model->obtener($datos_comprobante[0]->BANP_Codigo);
            $banco_nombre = (count($datos_banco) > 0 ? $datos_banco[0]->BANC_Nombre : '');
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $temp = $this->usuario_model->obtener($vendedor);
            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
            $vendedor = $temp[0]->PERSC_Nombre;
            
            /////
            $representante = $datos_comprobante[0]->LET_Representante;
            $oficina = $datos_comprobante[0]->LET_Oficina;
            $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
            $dc = $datos_comprobante[0]->LET_DC;
            $direccion_giro = $datos_comprobante[0]->LET_Direccion;
            $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
            /////

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
				
            /////    
			if ($tipodos == 0) {	
                $datos_personados = $this->persona_model->obtener_datosPersona($personados);
                $nombre_clientedos = $datos_personados[0]->PERSC_Nombre . " " . $datos_personados[0]->PERSC_ApellidoPaterno . " " . $datos_personados[0]->PERSC_ApellidoMaterno;
                $rucdos = $datos_personados[0]->PERSC_Ruc;
                $telefonodos = $datos_personados[0]->PERSC_Telefono;
                $movildos = $datos_personados[0]->PERSC_Movil;
                $direcciondos = $datos_personados[0]->PERSC_Direccion;
                $faxsdos = $datos_personados[0]->PERSC_Fax;
			}elseif($tipodos == 1){
				$datos_empresados = $this->empresa_model->obtener_datosEmpresa($empresados);
                $nombre_clientedos = $datos_empresados[0]->EMPRC_RazonSocial;
                $rucdos = $datos_empresados[0]->EMPRC_Ruc;
                $telefonodos = $datos_empresados[0]->EMPRC_Telefono;
                $movildos = $datos_empresados[0]->EMPRC_Movil;
                $faxdos = $datos_empresados[0]->EMPRC_Fax;
                $direcciondos = $datos_empresados[0]->EMPRC_Direccion;
                $emp_direcciondos = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresados);
			}	
                
			if ($tipotres == 0) {	
                $datos_personatres = $this->persona_model->obtener_datosPersona($personatres);
                $nombre_clientetres = $datos_personatres[0]->PERSC_Nombre . " " . $datos_personatres[0]->PERSC_ApellidoPaterno . " " . $datos_personatres[0]->PERSC_ApellidoMaterno;
                $ructres = $datos_personatres[0]->PERSC_Ruc;
                $telefonotres = $datos_personatres[0]->PERSC_Telefono;
                $moviltres = $datos_personatres[0]->PERSC_Movil;
                $direcciontres = $datos_personatres[0]->PERSC_Direccion;
                $faxstres = $datos_personatres[0]->PERSC_Fax;
			}elseif($tipotres == 1){                
                $datos_empresatres = $this->empresa_model->obtener_datosEmpresa($empresatres);
                $nombre_clientetres = $datos_empresatres[0]->EMPRC_RazonSocial;
                $ructres = $datos_empresatres[0]->EMPRC_Ruc;
                $telefonotres = $datos_empresatres[0]->EMPRC_Telefono;
                $moviltres = $datos_empresatres[0]->EMPRC_Movil;
                $faxtres = $datos_empresatres[0]->EMPRC_Fax;
                $direcciontres = $datos_empresatres[0]->EMPRC_Direccion;
                $emp_direcciontres = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresatres);                             
            }
            ////
  

            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));

            /* Cabecera */
            $this->cezpdf->ezText('', '', array('leading' => 127));
            if ($img != 1) {
                $this->cezpdf->ezText($serie, 15, array("leading" => -74, 'left' => 94));
                $this->cezpdf->ezText($numero, 15, array("leading" => 0, 'left' => 170));
            } else {
                $this->cezpdf->ezText('', 15, array("leading" => -22, 'left' => 400));
                $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 440));
            }
            $this->cezpdf->ezText('', '', array("leading" => 40));
//            $fecha_text2= utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . mes_textual(substr($fecha, 3, 2)) . '                   ' . substr($fecha, 8,4)); 
//            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));
            $this->cezpdf->addText(366, 760, 9, utf8_decode_seguro($fecha));
            $this->cezpdf->addText(446, 760, 9, utf8_decode_seguro($fechavenc));
            
//            $this->cezpdf->addText(270, 706, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
//            $this->cezpdf->addText(480, 610, 9, utf8_decode_seguro($nombre_formapago));
//            $this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));
//            $ruc= substr($ruc, 0, 1).'    '.substr($ruc, 1, 1).'    '.substr($ruc, 2, 1).'    '.substr($ruc, 3, 1).'   '.substr($ruc, 4, 1).'    '.substr($ruc, 5, 1).'    '.substr($ruc, 6, 1).'   '.substr($ruc, 7, 1).'    '.substr($ruc, 8, 1).'    '.substr($ruc, 9, 1).'   '.substr($ruc, 10, 1);
            
            $this->cezpdf->addText(254, 764, 8, utf8_decode_seguro($direccion_giro));
            $this->cezpdf->addText(120, 716, 9, utf8_decode_seguro("TRANSPORTES Y LOGISTICA INTERNACIONAL EIRL"));
            $this->cezpdf->addText(142, 642, 8, utf8_decode_seguro($nombre_cliente));
            $this->cezpdf->addText(150, 608, 5, utf8_decode_seguro($direccion));
            $this->cezpdf->addText(264, 591, 8, utf8_decode_seguro($telefono));
            $this->cezpdf->addText(142, 590, 9, utf8_decode_seguro($ruc));
            
            $this->cezpdf->addText(142, 564, 8, utf8_decode_seguro($nombre_clientedos));
            $this->cezpdf->addText(150, 530, 5, utf8_decode_seguro($direcciondos));
            $this->cezpdf->addText(265, 513, 8, utf8_decode_seguro($telefonodos));
            $this->cezpdf->addText(136, 512, 9, utf8_decode_seguro($rucdos));
            
            $this->cezpdf->addText(176, 546, 8, utf8_decode_seguro($nombre_clientetres));
            
            
            $this->cezpdf->addText(214, 478, 8, utf8_decode_seguro($representante));
            
            $this->cezpdf->addText(364, 664, 8, utf8_decode_seguro($direccionpago));
            
            $this->cezpdf->addText(344, 596, 7, utf8_decode_seguro($banco_nombre));
            $this->cezpdf->addText(418, 596, 7, utf8_decode_seguro($oficina));
            $this->cezpdf->addText(476, 596, 7, utf8_decode_seguro($numerocuenta));
            $this->cezpdf->addText(572, 596, 7, utf8_decode_seguro($dc));
            
//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora)); 

            /* Listado de detalles */
//            $posicionX = 0;
//            $posicionY = 625;
//            $db_data = array();
//            $i = 550;
//            foreach ($detalle_comprobante as $indice => $valor) {
//                $c = 0;
//                $array_producto = explode('/', $valor->PROD_Nombre);
//                $producto = $valor->PROD_CodigoUsuario;
//                $posicionX = 10;
//                $unidad = $valor->UNDMED_Simbolo;
//                if ($valor->CPDEC_Pu_ConIgv != '')
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                else
//                    $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
////                $this->cezpdf->addText(50, $i, 9, $producto);
//                
//                ///aumentado stv
//                //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                
//                $this->cezpdf->addText(112, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                
//                $this->cezpdf->addText(68, $i, 8, utf8_decode_seguro($unidad));
//                ///
//                
////                $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
//                $this->cezpdf->addText(30, $i, 9, $valor->CPDEC_Cantidad);
//                $this->cezpdf->addText(460, $i, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2));
//                //$this->cezpdf->addTextWrap(470, $i, 40, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2), "right");//revisar http://pubsvn.ez.no/doxygen/4.0/html/classCpdf.html#a4c3091f0936a733aa7e7ff98b876f3b1
//                $this->cezpdf->addText(520, $i, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
//                //$this->cezpdf->addTextWrap(520, $i, 44, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2),"right");
//                $i-=13;
//
//                $posicionY-=17;
//            }

//            $this->cezpdf->addText(70, 127, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));

            /* Totales */
            // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(116,688, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
            //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));
//            $this->cezpdf->addText(520, 120, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
//            $this->cezpdf->addText(520, 88, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
            $this->cezpdf->addText(516, 764, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
        }

        else {
            $datos_comprobante = $this->letracambio_model->obtener_comprobante($codigo);
            
            ///
            ////
            $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
            $serie = $datos_comprobante[0]->LET_Serie;
            $numero = $datos_comprobante[0]->LET_Numero;
            $proveedor = $datos_comprobante[0]->PROVP_Codigo;
            $proveedordos = $datos_comprobante[0]->PROVPDOS_Codigo;
            $proveedortres = $datos_comprobante[0]->PROVPTRES_Codigo;            
            $subtotal = $datos_comprobante[0]->LET_subtotal;
            $descuento = $datos_comprobante[0]->LET_descuento;
            $igv = $datos_comprobante[0]->LET_igv;
            $igv100 = $datos_comprobante[0]->LET_igv100;
            $descuento100 = $datos_comprobante[0]->LET_descuento100;
            $total = $datos_comprobante[0]->LET_total;
            $hora = $datos_comprobante[0]->LET_Hora;
            $observacion = $datos_comprobante[0]->LET_Observacion;
            $fecha = mysql_to_human($datos_comprobante[0]->LET_Fecha);
            $fechavenc = mysql_to_human($datos_comprobante[0]->LET_FechaVenc);
            $vendedor = $datos_comprobante[0]->USUA_Codigo;
            
            $direccionpago = $datos_comprobante[0]->LET_DireccionPago;
            
            ////

            ////
            //////////
            $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
            $empresa = $datos_proveedor[0]->EMPRP_Codigo;
            $persona = $datos_proveedor[0]->PERSP_Codigo;
            $tipo = $datos_proveedor[0]->PROVC_TipoPersona;
            
            $datos_proveedordos = $this->proveedor_model->obtener_datosProveedor($proveedordos);
            $empresados = $datos_proveedordos[0]->EMPRP_Codigo;
            $personados = $datos_proveedordos[0]->PERSP_Codigo;
            $tipodos = $datos_proveedordos[0]->PROVC_TipoPersona;
            
            $datos_proveedortres = $this->proveedor_model->obtener_datosProveedor($proveedortres);
            $empresatres = $datos_proveedortres[0]->EMPRP_Codigo;
            $personatres = $datos_proveedortres[0]->PERSP_Codigo;
            $tipotres = $datos_proveedortres[0]->PROVC_TipoPersona;
            //////////
            $tipo_docu = $datos_comprobante[0]->LET_TipoDocumento;
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
            $datos_banco = $this->banco_model->obtener($datos_comprobante[0]->BANP_Codigo);
            $banco_nombre = (count($datos_banco) > 0 ? $datos_banco[0]->BANC_Nombre : 'BANCO DE CREDITO');
            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
            $id_formapago = $this->formapago_model->obtener($forma_pago);
            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
            $temp = $this->usuario_model->obtener($vendedor);
            $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
            $vendedor = $temp[0]->PERSC_Nombre;
            
            /////
            $representante = $datos_comprobante[0]->LET_Representante;
            $oficina = $datos_comprobante[0]->LET_Oficina;
            $numerocuenta = $datos_comprobante[0]->LET_NumeroCuenta;
            $dc = $datos_comprobante[0]->LET_DC;
            $direccion_giro = $datos_comprobante[0]->LET_Direccion;
            $ubigeo = $datos_comprobante[0]->LET_Ubigeo;
            /////

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
                
                ////
                $datos_personados = $this->persona_model->obtener_datosPersona($personados);
                $nombre_clientedos = $datos_personados[0]->PERSC_Nombre . " " . $datos_personados[0]->PERSC_ApellidoPaterno . " " . $datos_personados[0]->PERSC_ApellidoMaterno;
                $rucdos = $datos_personados[0]->PERSC_Ruc;
                $telefonodos = $datos_personados[0]->PERSC_Telefono;
                $movildos = $datos_personados[0]->PERSC_Movil;
                $direcciondos = $datos_personados[0]->PERSC_Direccion;
                $faxsdos = $datos_personados[0]->PERSC_Fax;
                
                $datos_personatres = $this->persona_model->obtener_datosPersona($personatres);
                $nombre_clientetres = $datos_personatres[0]->PERSC_Nombre . " " . $datos_personatres[0]->PERSC_ApellidoPaterno . " " . $datos_personatres[0]->PERSC_ApellidoMaterno;
                $ructres = $datos_personatres[0]->PERSC_Ruc;
                $telefonotres = $datos_personatres[0]->PERSC_Telefono;
                $moviltres = $datos_personatres[0]->PERSC_Movil;
                $direcciontres = $datos_personatres[0]->PERSC_Direccion;
                $faxstres = $datos_personatres[0]->PERSC_Fax;
                ////
                
            } elseif ($tipo == 1) {
                $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                $nombre_cliente = $datos_empresa[0]->EMPRC_RazonSocial;
                $ruc = $datos_empresa[0]->EMPRC_Ruc;
                $telefono = $datos_empresa[0]->EMPRC_Telefono;
                $movil = $datos_empresa[0]->EMPRC_Movil;
                $fax = $datos_empresa[0]->EMPRC_Fax;
                $direccion = $datos_empresa[0]->EMPRC_Direccion;
                $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
                
                ////
                $datos_empresados = $this->empresa_model->obtener_datosEmpresa($empresados);
                $nombre_clientedos = $datos_empresados[0]->EMPRC_RazonSocial;
                $rucdos = $datos_empresados[0]->EMPRC_Ruc;
                $telefonodos = $datos_empresados[0]->EMPRC_Telefono;
                $movildos = $datos_empresados[0]->EMPRC_Movil;
                $faxdos = $datos_empresados[0]->EMPRC_Fax;
                $direcciondos = $datos_empresados[0]->EMPRC_Direccion;
                $emp_direcciondos = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresados);
                
                $datos_empresatres = $this->empresa_model->obtener_datosEmpresa($empresatres);
                $nombre_clientetres = $datos_empresatres[0]->EMPRC_RazonSocial;
                $ructres = $datos_empresatres[0]->EMPRC_Ruc;
                $telefonotres = $datos_empresatres[0]->EMPRC_Telefono;
                $moviltres = $datos_empresatres[0]->EMPRC_Movil;
                $faxtres = $datos_empresatres[0]->EMPRC_Fax;
                $direcciontres = $datos_empresatres[0]->EMPRC_Direccion;
                $emp_direcciontres = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresatres);
                /////
                
            }
            
            
            ////aumentado stv
            
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));

            /* Cabecera */
            $this->cezpdf->ezText('', '', array('leading' => 127));
            if ($img != 1) {
                $this->cezpdf->ezText($serie, 15, array("leading" => -74, 'left' => 94));
                $this->cezpdf->ezText($numero, 15, array("leading" => 0, 'left' => 170));
            } else {
                $this->cezpdf->ezText('', 15, array("leading" => -22, 'left' => 400));
                $this->cezpdf->ezText('', 15, array("leading" => 0, 'left' => 440));
            }
            $this->cezpdf->ezText('', '', array("leading" => 40));
//            $fecha_text2= utf8_decode_seguro(substr($fecha, 0, 2) . '            ' . mes_textual(substr($fecha, 3, 2)) . '                   ' . substr($fecha, 8,4)); 
//            $fecha_text = utf8_decode_seguro(substr($fecha, 0, 2) . '     ' . substr($fecha, 3, 2) . '         ' . substr($fecha, 8, 4));
            $this->cezpdf->addText(366, 760, 9, utf8_decode_seguro($fecha));
            $this->cezpdf->addText(446, 760, 9, utf8_decode_seguro($fechavenc));
            
//            $this->cezpdf->addText(270, 706, 9, utf8_decode_seguro("Vendedor : " . $vendedor));
//            $this->cezpdf->addText(480, 610, 9, utf8_decode_seguro($nombre_formapago));
//            $this->cezpdf->addText(400, 644, 9, utf8_decode_seguro($telefono));
//            $ruc= substr($ruc, 0, 1).'    '.substr($ruc, 1, 1).'    '.substr($ruc, 2, 1).'    '.substr($ruc, 3, 1).'   '.substr($ruc, 4, 1).'    '.substr($ruc, 5, 1).'    '.substr($ruc, 6, 1).'   '.substr($ruc, 7, 1).'    '.substr($ruc, 8, 1).'    '.substr($ruc, 9, 1).'   '.substr($ruc, 10, 1);
            
            $this->cezpdf->addText(254, 764, 8, utf8_decode_seguro($direccion_giro));
            $this->cezpdf->addText(120, 716, 9, utf8_decode_seguro($nombre_cliente));
            $this->cezpdf->addText(142, 642, 8, utf8_decode_seguro("TRANSPORTES Y LOGISTICA INTERNACIONAL EIRL"));
            $this->cezpdf->addText(150, 608, 5, utf8_decode_seguro("av. Av. El Polo Mz.H Lt.12 C Urb.El Club, 1era Etapa Huachipa, Lurigancho, Lima - Peru"));
            $this->cezpdf->addText(264, 591, 8, utf8_decode_seguro('995714864'));
            $this->cezpdf->addText(142, 590, 9, utf8_decode_seguro('20514929948'));
            
            $this->cezpdf->addText(142, 564, 8, utf8_decode_seguro($nombre_clientedos));
            $this->cezpdf->addText(150, 530, 5, utf8_decode_seguro($direcciondos));
            $this->cezpdf->addText(265, 513, 8, utf8_decode_seguro($telefonodos));
            $this->cezpdf->addText(136, 512, 9, utf8_decode_seguro($rucdos));
            
            $this->cezpdf->addText(176, 546, 8, utf8_decode_seguro($nombre_clientetres));
            
            
            $this->cezpdf->addText(214, 478, 8, utf8_decode_seguro($representante));
            
            $this->cezpdf->addText(364, 664, 8, utf8_decode_seguro($direccionpago));
            
            $this->cezpdf->addText(344, 596, 7, utf8_decode_seguro($banco_nombre));
            $this->cezpdf->addText(418, 596, 7, utf8_decode_seguro($oficina));
            $this->cezpdf->addText(476, 596, 7, utf8_decode_seguro($numerocuenta));
            $this->cezpdf->addText(572, 596, 7, utf8_decode_seguro($dc));
            
//            $this->cezpdf->addText(400, 670, 9, utf8_decode_seguro($hora)); 

            /* Listado de detalles */
//            $posicionX = 0;
//            $posicionY = 625;
//            $db_data = array();
//            $i = 550;
//            foreach ($detalle_comprobante as $indice => $valor) {
//                $c = 0;
//                $array_producto = explode('/', $valor->PROD_Nombre);
//                $producto = $valor->PROD_CodigoUsuario;
//                $posicionX = 10;
//                $unidad = $valor->UNDMED_Simbolo;
//                if ($valor->CPDEC_Pu_ConIgv != '')
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                else
//                    $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
////                $this->cezpdf->addText(50, $i, 9, $producto);
//                
//                ///aumentado stv
//                //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                
//                $this->cezpdf->addText(112, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                
//                $this->cezpdf->addText(68, $i, 8, utf8_decode_seguro($unidad));
//                ///
//                
////                $this->cezpdf->addText(120, $i, 9, utf8_decode_seguro($array_producto[0] . '  --- ' . $unidad));
//                $this->cezpdf->addText(30, $i, 9, $valor->CPDEC_Cantidad);
//                $this->cezpdf->addText(460, $i, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2));
//                //$this->cezpdf->addTextWrap(470, $i, 40, 9, $moneda_simbolo . '' . number_format($pu_conigv, 2), "right");//revisar http://pubsvn.ez.no/doxygen/4.0/html/classCpdf.html#a4c3091f0936a733aa7e7ff98b876f3b1
//                $this->cezpdf->addText(520, $i, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
//                //$this->cezpdf->addTextWrap(520, $i, 44, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2),"right");
//                $i-=13;
//
//                $posicionY-=17;
//            }

//            $this->cezpdf->addText(70, 127, 9, utf8_decode_seguro(strtoupper($docurefe_codigo)));

            /* Totales */
            // $this->cezpdf->addText(20, 360, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" valido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(116,688, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre);
//            $this->cezpdf->addText(500, 110, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
            //$this->cezpdf->addText(462, 306, 9, 'DCTO: '. $moneda_simbolo . ' ' . number_format($descuento, 2));
//            $this->cezpdf->addText(520, 120, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
//            $this->cezpdf->addText(520, 88, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
            $this->cezpdf->addText(516, 764, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
            $this->cezpdf->ezStream($cabecera);
            
            ///
            
            

            
            
            ////
            ////
            //////estaba asi 
//            $serie = $datos_comprobante[0]->CPC_Serie;
//            $numero = $datos_comprobante[0]->CPC_Numero;
//            $proveedor = $datos_comprobante[0]->PROVP_Codigo;
//            $subtotal = $datos_comprobante[0]->CPC_subtotal;
//            $descuento = $datos_comprobante[0]->CPC_descuento;
//            $igv = $datos_comprobante[0]->CPC_igv;
//            $total = $datos_comprobante[0]->CPC_total;
//            $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
//            $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
//            $ruc = $datos_proveedor[0]->EMPRC_Ruc;
//            $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
//            $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
//            $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
//            $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
//            if ($guiarem_codigo !== Null) {
//                $list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
//                $guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
//                $guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
//            }
//            $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
//            $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
//            $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
//            $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
//            $id_formapago = $this->formapago_model->obtener($forma_pago);
//            $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
//            $array_fecha = explode("/", $fecha);
//            $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
//            $detalle_comprobante = $this->obtener_lista_detalles($codigo);
            
            
            
            
            
//            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
////            /* Cabecera */
//            $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
//            $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));
//
////            /* Datos del cliente */
//            $this->cezpdf->ezText(utf8_decode_seguro("IMPORTACIONES IMPACTO SAC"), 9, array("leading" => 24, "left" => 10));
//            $this->cezpdf->ezText('20527033798', 9, array("leading" => 12, "left" => 10));
//
//            //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
//            $this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
//            $this->cezpdf->ezText(utf8_decode_seguro((int) substr($fecha, 0, 2)), 9, array("leading" => 0, "left" => 458));
//            $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 472));
//            $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" => 515));
//            $this->cezpdf->ezText('-----', 8, array("leading" => 16, "left" => 333));
//            $this->cezpdf->ezText($serie, 18, array("leading" => -55, 'left' => 395));
//            $this->cezpdf->ezText($numero, 18, array("leading" => 0, 'left' => 440));
            /* Listado de detalles */
//            $posicionX = 0;
//            $posicionY = 640;
//            $db_data = array();
//
//            foreach ($detalle_comprobante as $indice => $valor) {
//                $c = 0;
//                $array_producto = explode('/', $valor->PROD_Nombre);
//                $producto = $valor->PROD_CodigoUsuario;
//
////                $ser = "";
////                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);
//
//                $posicionX = 10;
//                if ($valor->CPDEC_Pu_ConIgv != '')
//                    $pu_conigv = $valor->CPDEC_Pu_ConIgv;
//                else
//                    $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
//                $posicionX+=30;
//                $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
//                $posicionX+=30;
//                
//                ///aumentado stv
//                //$this->cezpdf->addText(80, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
//                
//                $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));                
//                ///
//                
////                $this->cezpdf->addText($posicionX, $posicionY, 9, utf8_decode_seguro($array_producto[0]));
//                $posicionX+=380;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
//                $posicionX+=35;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($pu_conigv, 2));
//                $posicionX+=55;
//                $this->cezpdf->addText($posicionX, $posicionY, 9, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
//
//
////
////                if (count($datos_serie) > 0) {
////                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
////                    for ($i = 0; $i < count($datos_serie); $i++) {
////                        $c = $c + 1;
////                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
////
////                        $ser = $ser . " /" . $seriecodigo;
////
////                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
////                        if ($c == 8) {
////                            $posicionY-=10;
////                            $c = 0;
////                            $ser = "";
////                        }
////                    }
////                }
//                $posicionY-=40;
//            }
            /* Totales */
//            $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
//            $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
//
//            $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));
//
//            $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));
//
//            $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));
//            $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
//            $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
//
//            $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
//            $this->cezpdf->ezStream($cabecera);
        }
    }

    public function comprobante_ver_pdf_conmenbrete_formato1_boleta($tipo_oper, $codigo, $tipo_docu, $img) {

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
                        $filter4->GUIASADETC_Descripcion = $nombre_producto;
                        ;
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
            
            
	    $fechas=date($fecha_emision);
            $a=substr(date("Y", strtotime($fechas)),2,2);
            $m=mes_textual(date("m", strtotime($fechas)));
            $d=date("d", strtotime($fechas));
			
            
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
				
				
				$prod_nombre=$valor->PROD_Nombre;
				
				if(strpos($prod_nombre,"/")){
				$prod_nombre=substr($valor->PROD_Nombre,0,strpos($valor->PROD_Nombre,"/"));
				}
				
				
				
                $producto = $valor->PROD_CodigoUsuario;
                $unidad = $valor->UNDMED_Simbolo;
                $ser = "";
//                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
                $positionx+=42;
                $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
                $positionx +=26;                                                            //$array_prodnombre[0]
                $this->cezpdf->addText($positionx, $positiony, 6, strtoupper(utf8_decode_seguro($prod_nombre)));
                $positionx+=154;
				
				
				/////alineado ppu 

                $ppu=$moneda_simbolo . number_format($valor->CPDEC_Pu_ConIgv, 2);
                $ppu=$this->ali_precio($ppu);
                $this->cezpdf->addText($positionx, $positiony, 7, $ppu);

                ///////////
				
				
                //$this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx+=40;
				
				
				
				/////alineado ppt

                $ppt=$moneda_simbolo . number_format($valor->CPDEC_Total, 2);
                $ppt=$this->ali_precio($ppt);
                $this->cezpdf->addText($positionx, $positiony, 7, $ppt);

                ///////////
				
				
				
                //$this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                $positiony-=14;
            }

            /*             * Sub Totales* */
            $delta = 130;
            $positionx = 420;
            $positiony = 350 + $delta;
            // $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            
			
			$this->cezpdf->addText(52, $positiony -54, 6, "SON: " . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
            
			
			
			/////alineado ppto

                $ppto=$moneda_simbolo . number_format($total, 2);
                $ppto=$this->ali_precio($ppto);
                $this->cezpdf->addText($positionx -156, $positiony - 78, 8, $ppto);

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
                $positionx +=30;
// $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
//$positionx+=50;

                $this->cezpdf->addText($positionx, $positiony, 7, strtoupper(utf8_decode_seguro($array_prodnombre[0])));
                $positionx+=428;
                $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
                $positionx+=25;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx+=50;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
// $this->cezpdf->addText($positionx, $positiony, 7, number_format($valor->CPDEC_Total, 2));
                $this->cezpdf->addText(40, $positiony - 15, 7, "Series: " . $ser);
                $positiony-=40;
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

    public function comprobante_ver_pdf_conmenbrete_formato1_boleta1($tipo_oper, $codigo, $tipo_docu, $img) {

        if ($tipo_oper == 'V') {
            if ($img == 1) {
                $notimg = "";
            } else {
                $notimg = "";   //fullcolor_boleta.jpg
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
            $this->cezpdf = new Cezpdf('a4');
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
            /* Cabecera */

            if ($img != 1) {
                $this->cezpdf->ezText(utf8_decode_seguro($num_ser), 15, array("leading" => 46, "left" => 260));
                $this->cezpdf->ezText(utf8_decode_seguro(" "), 15, array("leading" => 0, "left" => 266));
                $this->cezpdf->ezText(utf8_decode_seguro($num_doc), 15, array("leading" => 0, "left" => 304));
            } else {
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => -35, "left" => 400));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 430));
                $this->cezpdf->ezText(utf8_decode_seguro(""), 15, array("leading" => 0, "left" => 440));
            }
            
            $fechas=date($fecha_emision);
            $a=substr(date("Y", strtotime($fechas)),2,2);
            $m=mes_textual(date("m", strtotime($fechas)));
            $d=date("d", strtotime($fechas));
            
            $this->cezpdf->ezText(utf8_decode_seguro($a), 9, array("leading" => 52, "left" => 414));
            $this->cezpdf->ezText(utf8_decode_seguro($m), 9, array("leading" => 0, "left" => 322));
            $this->cezpdf->ezText(utf8_decode_seguro($d), 9, array("leading" => 0, "left" => 284));
            
            
            $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente), 8, array("leading" => 24, "left" => 30));
            
            $this->cezpdf->ezText(utf8_decode_seguro($direccion), 8, array("leading" => 15, "left" => 30));
            $this->cezpdf->ezText(utf8_decode_seguro($ruc), 8, array("leading" => -16, "left" => 338));
            $this->cezpdf->addText(200,700,7,utf8_decode_seguro("Vendedor : " . $vendedor));
//            $this->cezpdf->ezText(utf8_decode_seguro("Modo : " . $nombre_formapago), 7, array("leading" => 6, "left" => 263));

            
            
            
//            $this->cezpdf->ezText(utf8_decode_seguro($hora), 7, array("leading" => 0, "left" => 445));

            $this->cezpdf->ezText('', '', array("leading" => 70));


            /* Listado de detalles */
            $db_data = array();
            $positiony = 634;
            $positionx = 0;
            $serie_producto = "00000000001, 00000000002";
            foreach ($detalle_comprobante as $indice => $valor) {
                $positionx = 36;
                //$array_prodnombre = explode('/', $valor->PROD_Nombre);
                $producto = $valor->PROD_CodigoUsuario;
                $unidad = $valor->UNDMED_Simbolo;
                $ser = "";
//                $this->cezpdf->addText($positionx, $positiony, 7, $producto);
                $positionx+=0;
                $this->cezpdf->addText($positionx, $positiony, 8, $valor->CPDEC_Cantidad);
                $positionx +=30;                                                            //$array_prodnombre[0]
                $this->cezpdf->addText($positionx, $positiony, 8, strtoupper(utf8_decode_seguro($valor->PROD_Nombre . " - " . $unidad)));
                $positionx+=260;
                $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx+=58;
                $this->cezpdf->addText($positionx, $positiony, 8, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
                $positiony-=22;
            }

            /*             * Sub Totales* */
            $delta = 130;
            $positionx = 420;
            $positiony = 350 + $delta;
            // $this->cezpdf->addText(20, 230, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" vï¿½lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
            $this->cezpdf->addText(40, $positiony - 206, 8, "SON: " . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));
            $this->cezpdf->addText($positionx -38, $positiony - 240, 10, $moneda_simbolo . ' ' . number_format($total, 2));
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
                $positionx +=30;
// $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
//$positionx+=50;

                $this->cezpdf->addText($positionx, $positiony, 7, strtoupper(utf8_decode_seguro($array_prodnombre[0])));
                $positionx+=428;
                $this->cezpdf->addText($positionx, $positiony, 7, $valor->CPDEC_Cantidad);
                $positionx+=25;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Pu_ConIgv, 2));
                $positionx+=50;
                $this->cezpdf->addText($positionx, $positiony, 7, $moneda_simbolo . ' ' . number_format($valor->CPDEC_Total, 2));
// $this->cezpdf->addText($positionx, $positiony, 7, number_format($valor->CPDEC_Total, 2));
                $this->cezpdf->addText(40, $positiony - 15, 7, "Series: " . $ser);
                $positiony-=40;
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

    /* Auxiliares */

    public function obtener_tipo_documento($tipo) {
        $tiponom = 'Letra de Cambio';
//        switch ($tipo) {
//            case 'F': $tiponom = 'factura';
//                break;
//            case 'B': $tiponom = 'boleta';
//                break;
//            case 'N': $tiponom = 'comprobante';
//                break;
//        }
        return $tiponom;
    }

    public function obtener_serie_numero($tipo_docu) {
        $data['numero'] = '';
        $data['serie'] = '';
        switch ($tipo_docu) {
            case 'F': $codtipodocu = '8';
                break;
            case 'B': $codtipodocu = '9';
                break;
            case 'N': $codtipodocu = '14';
                break;
            default: $codtipodocu = '0';
                break;
        }
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $codtipodocu);

        if (count($datos_configuracion) > 0) {
            $data['serie'] = $datos_configuracion[0]->CONFIC_Serie;
            $data['numero'] = $datos_configuracion[0]->CONFIC_Numero + 1;
        }
        return $data;
    }

    public function reportes() {
        $anio = $this->comprobante_model->anios_para_reportes('V');
        $combo = '<select id="anioVenta" name="anioVenta">';
        $combo .='<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo .='<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo .='</select>';

        $combo2 = '<select id="anioVenta2" name="anioVenta2">';
        $combo2 .='<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo2 .='<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo2 .='</select>';

        $combo3 = '<select id="anioVenta3" name="anioVenta3">';
        $combo3 .='<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo3 .='<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo3 .='</select>';

        $combo4 = '<select id="anioVenta4" name="anioVenta4">';
        $combo4 .='<option value="0">Seleccione...</option>';
        foreach ($anio as $key => $value) {
            $combo4 .='<option value="' . $value->anio . '">' . $value->anio . '</option>';
        }
        $combo4 .='</select>';

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
        $this->layout->view('ventas/comprobante_reporte', $data);
    }

    public function estadisticas() {
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

    public function ver_reporte_pdf($params) {
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
        $listado = $this->comprobante_model->buscar_comprobante_venta($fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso);

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



        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ver_reporte_pdf_ventas($anio) {
        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_venta_2($anio);

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
            $sum+=$value->CPC_total;
            $db_data[] = array(
                'col1' => $key + 1,
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $serie,
                'col4' => $value->CPC_Numero,
                'col6' => $value->CPC_subtotal,
                'col7' => $value->CPC_igv,
                'col8' => $value->CPC_total
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

        $db_data[] = array(
            'col1' => "",
            'col2' => "",
            'col3' => "",
            'col4' => "",
            'col5' => "",
            'col6' => "",
            'col7' => "TOTAL",
            'col8' => $sum,
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

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ver_reporte_pdf_commpras($anio) {
        $usuario = $this->usuario_model->obtener($this->somevar['user']);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
//$this->load->library('cezpdf');
//$this->load->helper('pdf_helper');
//prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

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
            $sum+=$value->CPC_total;
            $db_data[] = array(
                'col1' => $key + 1,
                'col2' => substr($value->CPC_FechaRegistro, 0, 10),
                'col3' => $serie,
                'col4' => $value->CPC_Numero,
                'col6' => $value->CPC_subtotal,
                'col7' => $value->CPC_igv,
                'col8' => $value->CPC_total
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

        $db_data[] = array(
            'col1' => "",
            'col2' => "",
            'col3' => "",
            'col4' => "",
            'col5' => "",
            'col6' => "",
            'col7' => "TOTAL",
            'col8' => $sum,
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

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function estadisticas_compras_ventas($tipo, $anio) {
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

    private function meses($anio) {
        switch ($anio) {
            case 1 : return "ENERO";
            case 2 : return "FEBRERO";
            case 3 : return "MARZO";
            case 4 : return "ABRIL";
            case 5 : return "MAYO";
            case 6 : return "JUNIO";
            case 7 : return "JULIO";
            case 8 : return "AGOSTO";
            case 9 : return "SETIEMBRE";
            case 10 : return "OCTUBRE";
            case 11 : return "NOVIEMBRE";
            case 12 : return "DICIEMBRE";
        }
    }

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes) {
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

    public function formatos_de_impresion_F($codigo, $tipo_docu) {
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
            }
            else
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

    public function formatos_de_impresion_B($codigo, $tipo_docu) {
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

    public function obtener_tipo_de_cambio($fecha_comprobante) {
        return $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);
    }

    public function ventana_muestra_comprobante($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "") { // $formato: SELECT_ITEM, SELECT_HEADER, $docu_orig: DOCUMENTO QUE SOLICITA LA REFERENCIA, FACTURA, GUIA DE REMISION, ETC
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
        $lista_comprobante = $this->comprobante_model->buscar_comprobantes($tipo_oper, 'F', $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_documento(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
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
        $data['tipo_oper'] = $tipo_oper;
        $data['docu_orig'] = $docu_orig;
        $data['formato'] = $formato;
        $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));

        $this->load->view('ventas/ventana_muestra_comprobante', $data);
    }

    public function comprobante_cambiar() {

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
                }
                else
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

    public function comprobante_nueva_cambiado() {


        

        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('cboTipoDocu');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $prodcantidad = $this->input->post('prodcantidad');
        $proddescri = $this->input->post('proddescri');
        $dref = $this->input->post('dRef');
//unset($_SESSION['serie']);
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);

//VERIFICO SI TODAS LAS SERIES HAN SIDO INGRESADAS
        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
//        if (is_array($prodcodigo)) {
//            foreach ($prodcodigo as $indice => $valor) {
//                if ($flagGenInd[$indice] == 'I' && isset($_SESSION['serie']) && is_array($_SESSION['serie'][$valor])) {
//                    if (count($_SESSION['serie'][$valor]) != $prodcantidad[$indice])
//                        exit('{"result":"error2", "msj":"No ha ingresado todos los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
//                }else
//                    exit('{"result":"error2", "msj":"No ha ingresado los nÃºmero de series de :\n' . $proddescri[$indice] . '"}');
//            }
//        }

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
        $detaccion = $this->input->post('detaccion');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodigv100 = $this->input->post('prodigv100');
        $prodcosto = $this->input->post('prodcosto');
        $proddescri = $this->input->post('proddescri');



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
//if($tipo_docu!='B' && $tipo_docu!='N'){
        $subtotal = $this->input->post('preciototal');
        $descuento = $this->input->post('descuentotal');
        $igv = $this->input->post('igvtotal');
//}else{
        $subtotal_conigv = $this->input->post('preciototal_conigv');
        $descuento_conigv = $this->input->post('descuentotal_conigv');
//}
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
        $vendedor = "";
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


        $codigo = "";
        $data['codigo'] = $codigo;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['url_action'] = base_url() . "index.php/ventas/comprobante/comprobante_insertar";
        $data['titulo'] = "REGISTRAR " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', $moneda);
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', $forma_pago);
        $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), $presupuesto, array('', '::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), $ordencompra, array('', '::Seleccione::'), ' - ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), $guiarem_codigo, array('', '::Seleccione::'), ' / ');
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $vendedor, array('', '::Seleccione::'), ' ');
        $data['tdc'] = $tdc;
        $data['observacion'] = $observacion;
        //obtenemos la configuracion
        $compania = $this->somevar['compania'];
        if ($tipo_docu == 'F') {
            $tipo = 8;
        }
        if ($tipo_docu == 'B') {
            $tipo = 9;
        }
        if ($tipo_docu == 'N') {
            $tipo = 14;
        }
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        if ($tipo_oper == 'V') {
            $sero = $cofiguracion_datos[0]->CONFIC_Serie;
            $numo = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        } else {
            $sero = '';
            $numo = '';
        }

        // $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'B');
        $data['serie_suger_b'] = $sero;
        $data['numero_suger_b'] = $numo;
        // $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'F');
        $data['serie_suger_f'] = $sero;
        $data['numero_suger_f'] = $numo;

        if ($tipo_docu == 'B') {
            $data['serie'] = $data['serie_suger_b'];
            $data['numero'] = $data['numero_suger_b'];
        } else if ($tipo_docu == 'F') {
            $data['serie'] = $data['serie_suger_f'];
            $data['numero'] = $data['numero_suger_f'];
        }

        if ($tipo_oper == 'V') {
            $temp = $this->obtener_serie_numero($tipo_docu);
        }

        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;

//contenidos en array $prodcodigo
        $cont_prod = 0;
//detalle de comprobante:        
        if (is_array($prodcodigo)) {
            $lista_detalles = array();
            foreach ($prodcodigo as $indice => $valor) {
                $cont_prod = $cont_prod + 1;
                $detacodi = "";
                $detvisible = $detaccion[$indice];
                $producto = $prodcodigo[$indice];
//if($flagBS[$indice]=='P')
                $unidad = $produnidad[$indice];
//else
//$unidad = "";
                $cantidad = $prodcantidad[$indice];

                if ($tipo_docu != 'B' && $tipo_docu != 'N') {
                    $pud = $prodpu[$indice];
                    $subtotald = $prodprecio[$indice];
                    $descuentod = $proddescuento[$indice];
                    $igvd = $prodigv[$indice];
                    $subtotal_conigvd = "";
                    $descuento_conigvd = "";
                } else {
                    $pud = "";
                    $subtotald = "";
                    $descuentod = "";
                    $igvd = "";
                    $subtotal_conigvd = $prodprecio_conigv[$indice];
                    $descuento_conigvd = $proddescuento_conigv[$indice];
                }
                $totald = $prodimporte[$indice];
                $pu_conigvd = $prodpu_conigv[$indice];
                $descuento100d = $proddescuento100[$indice];
                $igv100d = $prodigv100[$indice];

                //if ($tipo_oper == 'V')
                //$costod = $prodcosto[$indice];

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $flagGenInd[$indice];
                $nombre_producto = (strtoupper($proddescri[$indice]) != '' ? strtoupper($proddescri[$indice]) : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';
                $observacion = "";

                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->CPDEC_GenInd = $GenInd;
                $objeto->CPDEC_Costo = $prodcosto[$indice];
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_Cantidad = $cantidad;
                $objeto->CPDEC_Pu = $pud;
                $objeto->CPDEC_Subtotal = $subtotald;
                $objeto->CPDEC_Descuento = $descuentod;
                $objeto->CPDEC_Igv = $igvd;
                $objeto->CPDEC_Total = $totald;
                $objeto->CPDEC_Pu_ConIgv = $pu_conigvd;
                $objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigvd;
                $objeto->CPDEC_Descuento_ConIgv = $descuento_conigvd;
                $objeto->CPDEC_Descuento100 = $descuento100d;
                $objeto->CPDEC_Igv100 = $igv100d;
                $objeto->CPDEC_Observacion = $observacion;

                if ($detvisible != "e") {
                    $lista_detalles[] = $objeto;
                }

//$this->comprobantedetalle_model->insertar($filter);                
            }
            $data['detalle_comprobante'] = $lista_detalles;
        } else {
            $data['detalle_comprobante'] = array();
        }

//Para cambio comprobante_A
        $data['cambio_comp'] = 1;
        $data['total_det'] = $cont_prod;

        $data['observacion'] = $observacion;
        $data['focus'] = "";
        $data['pedido'] = "";
        $data['descuento'] = $descuento100;
        $data['igv'] = $igv100;
        $data['hidden'] = "";
        $data['preciototal'] = $subtotal;
        $data['descuentotal'] = $descuento;
        $data['igvtotal'] = $igv;
        $data['importetotal'] = $total;
        $data['preciototal_conigv'] = $subtotal;
        $data['descuentotal_conigv'] = $subtotal_conigv;
        $data['hidden'] = "";
        $data['observacion'] = $observacion;
        $data['cboTipoDocu'] = $tipo_docu;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo'] = $docurefe_codigo;
        $data['estado'] = $estado;
        $data['guia'] = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['hoy'] = $fecha;
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');

        $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, $tipo_docu);
        $data['serie_suger'] = $ultimo_serie_numero['serie'];
        $data['numero_suger'] = $ultimo_serie_numero['numero'];
        $this->layout->view('ventas/comprobante_nueva', $data);
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
    
    
    
    
    
    
    /////
    
    public function seleccionar_departamentopago($indDefault = '') {
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

    public function seleccionar_provinciapago($departamento, $indDefault = '') {
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

    public function seleccionar_distritospago($departamento, $provincia, $indDefault = '') {
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
    
    /////
    
    

}
?>