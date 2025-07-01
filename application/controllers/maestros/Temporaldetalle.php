<?php
ini_set('error_reporting', 1); 
//require_once 'application/libraries/PHPExcel/IOFactory.php';

class Temporaldetalle extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('date');
        $this->load->library('form_validation');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('table');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/guiatrans_model');
        $this->load->model('almacen/seriemov_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('almacen/productopublicacion_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('almacen/familia_model');
        $this->load->model('almacen/tipoproducto_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('maestros/fabricante_model');
        $this->load->model('almacen/productoprecio_model');
        $this->load->model('almacen/plantilla_model');
        $this->load->model('almacen/atributo_model');
        $this->load->model('maestros/marca_model');
        $this->load->model('almacen/productounidad_model');
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/loteprorrateo_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/emprestablecimiento_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/categoriapublicacion_model');
        $this->load->model('maestros/temporaldetalle_model');
        $this->load->model('maestros/tipocliente_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('almacen/serie_model');
        $this->load->model('almacen/seriedocumento_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('ventas/comprobantedetalle_model');
        $this->load->model('ventas/notacredito_model');
        $this->load->model('ventas/notacreditodetalle_model');
        $this->compania = $this->session->userdata('compania'); 
        date_default_timezone_set("America/Lima");
    }

    public function index(){
        echo "hola";
    }

    public function autocomplete_producto($f = "B", $compania = "", $almacen = ""){
        $keyword = $this->input->post('term');
        $tipo_oper = $this->input->post('tipo_oper');
        $filtro = new stdClass();
        if ( $this->input->post('familia') != '' ){
            $familia = explode(' - ', $this->input->post('familia'));
        }
        else
            $familia = array();

        if ( $this->input->post('marca') != '' ){
            $marca = explode(' - ', $this->input->post('marca'));
        }
        else
            $marca = array();

        $filtro->familia = ( is_numeric($familia[0]) && $familia[0] > 0) ? $familia[0] : '';
        $filtro->marca = ( is_numeric($marca[0]) && $marca[0] > 0) ? $marca[0] : '';
        $filtro->modelo = ($this->input->post('modelo') != '') ? $this->input->post('modelo') : '';
        $filtro->situacion = 1;
        $result = array();
        if($keyword!=null && count(trim($keyword))>0){
            $compania = ($compania == "") ? $this->compania : $compania;
            $datosProducto = $this->producto_model->buscar_por_nombre_filtros($keyword, $f, $compania, $filtro);
            
            if($datosProducto != null && count($datosProducto) > 0){
                foreach ($datosProducto as $indice => $valor) {
                    $cod_prod = $valor->PROD_Codigo;
                    $stock = 0;
                    $datosAlmacenProducto = $this->almacenproducto_model->obtener($almacen, $cod_prod);
                    $CodigoAlmacenProducto = 0;
                    $datosUnidadProducto = $this->productounidad_model->obtenerprincipal($cod_prod);
                    if ( count($datosUnidadProducto) > 0 ) {
                        $codunidad = $datosUnidadProducto->UNDMED_Codigo;
                    }else{
                        $codunidad = 1;
                    }

                    $pcosto = 0;
                    $PrecioCosto = $this->producto_model->seleccionar_ultimo_costo($cod_prod);
                    $pcosto = $PrecioCosto[0]->PROD_UltimoCosto;

                    $pventa = 0;
                    /* AGREGAR PRECIO PRODUCTO   */
                        if ($this->input->post('TipCli') != "")
                            $PrecioXcliente = $this->producto_model->seleccionar_precio_cliente( $cod_prod, $this->input->post('TipCli') );
                        else
                            $PrecioXcliente = $this->producto_model->seleccionar_precio_cliente( $cod_prod );
                        
                        $pventa = $PrecioXcliente[0]->PRODPREC_Precio;
                    // End agregar precio
                    
                    $descripcionProducto = $valor->PROD_Nombre;
                    #$descripcionProducto = ( $valor->marca != '' ) ? $descripcionProducto . '. ' . $valor->marca : $descripcionProducto;
                    #$descripcionProducto = ( $valor->PROD_Modelo != '' ) ? $descripcionProducto . '. ' . $valor->PROD_Modelo : $descripcionProducto;
                    
                    if( $datosAlmacenProducto != NULL && count($datosAlmacenProducto) > 0 ){
                        foreach ($datosAlmacenProducto as $key => $valorReal){
                            $CodigoAlmacenProducto = $valorReal->ALMAC_Codigo;
                            if($almacen != NULL && $almacen != 0 && trim($almacen) != "" ){
                                if($CodigoAlmacenProducto == $almacen){
                                    $stock = $valorReal->ALMPROD_Stock;
                                    $lote = $this->lote_model->detalles($cod_prod, $almacen, $tipo_oper);

                                    $result[] = array("value" => $descripcionProducto, "label" => "$valor->PROD_CodigoUsuario - $descripcionProducto - $valor->marca", "codigo" => $valor->PROD_Codigo, "codinterno" => $valor->PROD_CodigoUsuario,"flagGenInd" => $valor->PROD_GenericoIndividual, "pcosto" => $pcosto, "pventa" => $pventa, "stock" =>$stock,"almacenProducto"=>$CodigoAlmacenProducto, "codunidad" => $codunidad, 'descripcion' => $valor->PROD_DescripcionBreve, 'tipo_afectacion' => $valor->AFECT_Codigo, "marca" => "$valor->MARCP_Codigo - $valor->marca", "lote" => $lote);
                                }
                            }
                            else{
                                $stock = $valorReal->ALMPROD_Stock;
                                $result[] = array("value" => $descripcionProducto, "label" => "$valor->PROD_CodigoUsuario - $descripcionProducto - $valor->marca", "codigo" => $valor->PROD_Codigo, "codinterno" => $valor->PROD_CodigoUsuario,"flagGenInd" => $valor->PROD_GenericoIndividual, "pcosto" => $pcosto, "pventa" => $pventa, "stock" =>$stock,"almacenProducto"=>$CodigoAlmacenProducto, "codunidad" => $codunidad, 'descripcion' => $valor->PROD_DescripcionBreve, 'tipo_afectacion' => $valor->AFECT_Codigo, "marca" => "$valor->MARCP_Codigo - $valor->marca",);
                            }
                            
                        }
                    }
                    else{
                        $stock = 0;
                        $result[] = array("value" => $descripcionProducto, "label" => "$valor->PROD_CodigoUsuario - $descripcionProducto - $valor->marca", "codigo" => $valor->PROD_Codigo, "codinterno" => $valor->PROD_CodigoUsuario,"flagGenInd" => $valor->PROD_GenericoIndividual, "pcosto" => $pcosto, "pventa" => $pventa, "stock" =>$stock,"almacenProducto"=>$CodigoAlmacenProducto, "codunidad" => $codunidad, 'descripcion' => $valor->PROD_DescripcionBreve, 'tipo_afectacion' => $valor->AFECT_Codigo, "marca" => "$valor->MARCP_Codigo - $valor->marca",);
                    }
                }
            }
        }
        echo json_encode($result);
    }

    public function registrar_prodtemporal(){

        $codproducto        = $this->input->post('tempde_codproducto');
        $almacenProducto    = $this->input->post('tempde_almacenproducto');
        $tempsession        = $this->input->post('tempSession');
        $unidadMedida       = $this->input->post('tempde_unidadmedida');
        $flagGenInd         = $this->input->post('tempde_flagGenInd');
        $descripcionProd    = $this->input->post('tempde_producto');
        $costo              = $this->input->post('tempde_productocosto');
        $stock              = $this->input->post('tempde_prodStock');
        $cantidad           = $this->input->post('tempde_cantidad');
        $precioUnitario     = $this->input->post('tempde_precioUnitario');
        $subTotal           = $this->input->post('tempde_subTotal');
        //$tipoIgv            = $this->input->post('tempde_tipoIgv');
        $tipoIgv            = 1;
        $igv                = $this->input->post('tempde_igvLinea');
        $igv100             = $this->input->post('tempde_igv100');
        $moneda             = $this->input->post('tempde_moneda');
        $descuento          = $this->input->post('tempde_descuento');
        $descuento100       = $this->input->post('tempde_descuento100');
        $total              = $this->input->post('tempde_total');
        $observacion        = $this->input->post('tempde_detalleItem');
        $flagBs             = $this->input->post('tempde_flagBs');

        $idLote             = $this->input->post('tempde_lote');
        $icbper             = $this->input->post('tempde_icbper');

        $filter = new stdClass();
        $filter->TEMPDE_SESSION     = $tempsession;
        $filter->PROD_Codigo        = $codproducto;
        $filter->UNDMED_Codigo      = $unidadMedida;
        $filter->MONED_Codigo       = $moneda;
        $filter->ALMAP_Codigo       = $almacenProducto;
        $filter->LOTP_Codigo        = $idLote;
        $filter->TEMPDE_Costo       = $costo;
        $filter->TEMPDE_Stock       = $stock;
        $filter->TEMPDE_Cantidad    = $cantidad;
        $filter->TEMPDE_Pendiente   = $cantidad;
        $filter->TEMPDE_Precio      = $precioUnitario;
        $filter->TEMPDE_Subtotal    = $subTotal;
        $filter->TEMPDE_Descuento   = $descuento;
        $filter->TEMPDE_Igv         = $igv;
        $filter->TEMPDE_TipoIgv     = $tipoIgv;
        $filter->TEMPDE_Total       = $total;
        $filter->TEMPDE_Igv100      = $igv100;
        $filter->TEMPDE_Descuento100= $descuento100;
        $filter->TEMPDE_Descripcion = $descripcionProd;
        $filter->TEMPDE_Observacion = $observacion;
        $filter->TEMPDE_FlagBs      = $flagBs;
        $filter->TEMPDE_FlagEstado  = 1;
        $filter->TEMPDE_ICBPER      = 8;
        //$filter->TEMPDE_ICBPER      = $icbper;

        $rspta = $this->temporaldetalle_model->insertar_productodetalle($filter);

        if ($rspta) {
            echo "1";
        }else{
            echo "0";
        }

    }

    public function modificar_prodtemporal(){
        $tempsession        = $this->input->post('tempSession');
        $id_tempdet         = $this->input->post('tempde_id');
        $codproducto        = $this->input->post('tempde_codproducto');
        $almacenProducto    = $this->input->post('tempde_almacenproducto');
        $stock              = $this->input->post('tempde_prodStock');
        $unidadMedida       = $this->input->post('tempde_unidadmedida');
        $flagGenInd         = $this->input->post('tempde_flagGenInd');
        $descripcionProd    = $this->input->post('tempde_producto');
        $costo              = $this->input->post('tempde_productocosto');
        $cantidad           = $this->input->post('tempde_cantidad');
        $precioUnitario     = $this->input->post('tempde_precioUnitario');
        $subTotal           = $this->input->post('tempde_subTotal');
        $tipoIgv            = $this->input->post('tempde_tipoIgv');
        $igv                = $this->input->post('tempde_igvLinea');
        $igv100             = $this->input->post('tempde_igv100');
        $moneda             = $this->input->post('tempde_moneda');
        $descuento          = $this->input->post('tempde_descuento');
        $descuento100       = $this->input->post('tempde_descuento100');
        $total              = $this->input->post('tempde_total');
        $observacion        = $this->input->post('tempde_detalleItem');
        $flagBs             = $this->input->post('tempde_flagBs');

        $idLote             = $this->input->post('tempde_lote');
        $icbper             = $this->input->post('tempde_icbper');


        $filter = new stdClass();
        $filter->PROD_Codigo        = $codproducto;
        $filter->UNDMED_Codigo      = $unidadMedida;
        $filter->MONED_Codigo       = $moneda;
        $filter->LOTP_Codigo        = $idLote;
        $filter->TEMPDE_Costo       = $costo;
        $filter->TEMPDE_Stock       = $stock;
        $filter->TEMPDE_Cantidad    = $cantidad;
        $filter->TEMPDE_Pendiente   = $cantidad;
        $filter->TEMPDE_Precio      = $precioUnitario;
        $filter->TEMPDE_Subtotal    = $subTotal;
        $filter->TEMPDE_Descuento   = $descuento;
        $filter->TEMPDE_Igv         = $igv;
        $filter->TEMPDE_TipoIgv     = $tipoIgv;
        $filter->TEMPDE_Total       = $total;
        $filter->TEMPDE_FlagBs      = $flagBs;
        $filter->TEMPDE_Igv100      = $igv100;
        $filter->TEMPDE_Descuento100= $descuento100;
        $filter->TEMPDE_Descripcion = $descripcionProd;
        $filter->TEMPDE_Observacion = $observacion;
        //$filter->TEMPDE_FlagEstado  = 1;
        $filter->TEMPDE_ICBPER      = $icbper;

        $rspta =  $this->temporaldetalle_model->modificar_prodtemporal($id_tempdet,$filter,$tempsession);
        if ($rspta) {
            echo "1";
        }else{
            echo "0";
        }
        
    }

    public function eliminar_producto_temporal(){
        $codproducto = $this->input->post('codproducto');
        $tempSession = $this->input->post('tempSession');
        $result = $this->temporaldetalle_model->eliminar_producto_temporal($codproducto,$tempSession);
        if ($result) {
            echo "1";
        }else{
            echo "0";
        }

    }


    public function obtener_producto_temporal(){
        $detalleId = $this->input->post('detalleId');
        $codproducto = $this->input->post('codproducto');
        $tempSession = $this->input->post('tempSession');
        $idLote = $this->input->post('idLote');
        $idLote = ( $this->input->post('idLote') == "null" || $this->input->post('idLote') == NULL ) ? 0 : $this->input->post('idLote');
        $result = array();
        $data = $this->temporaldetalle_model->obtener_producto_temporal($detalleId, $codproducto, $tempSession, $idLote);
        

        
        if (count($data)>0) {
            $result = array(
                "message" => "1",
                "datos" => $data
            );

        }else{
            $result = array(
                "message" => "0",
                "datos"    => ""
            );
        }

        echo json_encode($result);
    }

#####################################################################################
########### SE USA CUANDO SE ASOCIA UN DOCUMENTO. Ejem. Factura de Cotización
#####################################################################################

    public function obtener_comprobantes_temproductos(){
        $idcomprobante = $this->input->post('comprobante');
        $tabla     = $this->input->post('tabla');
        $tempSession   = $this->input->post('tempSession');
        /*::::: VACIAMOS LA TABLA TEMP DETALLES ::::*/
        //$rspta = $this->temporaldetalle_model->eliminar_temporalProductos_session($tempSession);

        switch ($tabla) {
            case 'comprobantes':
                    $datos = $this->introducir_prodtemporal_comprobante_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'notas':
                    $datos = $this->introducir_prodtemporal_notas_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'ocompras':
                    $datos = $this->introducir_prodtemporal_ocompra_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'guiarem':
                    $datos = $this->introducir_prodtemporal_guiarem_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'presupuesto':
                    $datos = $this->introducir_prodtemporal_presupuesto_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'pedido':
                    $datos = $this->introducir_prodtemporal_pedido_referencial($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            default:
                # code...
                break;
        }
    }

    public function introducir_prodtemporal_pedido_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);

        $this->load->model('compras/pedido_model');
        $this->load->model('compras/pedidodetalle_model');

        $datos_pedido = $this->pedido_model->obtener_pedido($comprobante);
        $listado_detalle = $this->pedidodetalle_model->listar($comprobante);

        $lista_detalles = array();

        $moneda = $datos_pedido[0]->MONED_Codigo;
        $tdc = 0;
        $codigocliente = $datos_pedido[0]->CLIP_Codigo;
        $codigoproyecto = $datos_pedido[0]->PROYP_Codigo;

        if (count($listado_detalle) > 0) {
            foreach ($listado_detalle as $indice => $valor) {
                $detacodi = $valor->PEDIDETP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->PEDIDETC_Cantidad;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);

                if ($datos_producto[0]->FABRIP_Codigo == 1){
                    $nombre_producto = $datos_producto[0]->PROD_Nombre;
                    $observacion = ($datos_producto[0]->PROD_DescripcionBreve == NULL) ? "" : $datos_producto[0]->PROD_DescripcionBreve;
                    $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                    $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                    $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                    $filter = new stdClass();
                    $filter->TEMPDE_SESSION     = $session;
                    $filter->PROD_Codigo        = $producto;
                    $filter->UNDMED_Codigo      = $unidad;
                    $filter->LOTP_Codigo        = 0;
                    $filter->MONED_Codigo       = $moneda;
                    $filter->ALMAP_Codigo       = 0;
                    $filter->TEMPDE_Costo       = 0;
                    $filter->TEMPDE_Stock       = "";
                    $filter->TEMPDE_Cantidad    = $cantidad;
                    $filter->TEMPDE_Pendiente   = 0;

                    $filter->TEMPDE_Precio      = 0;
                    
                    $filter->TEMPDE_Subtotal    = 0;
                    $filter->TEMPDE_Descuento   = 0;
                    $filter->TEMPDE_Igv         = 0;
                    $filter->TEMPDE_TipoIgv     = 4;
                    $filter->TEMPDE_Total       = 0;
                    $filter->TEMPDE_Igv100      = 0;
                    $filter->TEMPDE_Descuento100= 0;
                    $filter->TEMPDE_Descripcion = $nombre_producto;
                    $filter->TEMPDE_Observacion = $observacion;
                    $filter->TEMPDE_FlagBs      = $flagBS;
                    $filter->TEMPDE_CodDetalle  = $detacodi;
                    $filter->TEMPDE_FlagEstado  = 1;
                    $filter->TEMPDE_ICBPER      = 0;

                    $this->temporaldetalle_model->insertar_productodetalle($filter);
                }
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_notas_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->comprobantedetalle_model->listar($comprobante); //(17)lista el detalle de la comprobante
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante); //(27)
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;

        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);
        $ruc = $datos->ruc;
        $razon_social = $datos->nombre;

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $igv100 = round($valor->CPDEC_Igv100, 2);
                $descuento100 = $valor->CPDEC_Descuento100;
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
                $icbper = $valor->CPDEC_ITEMS;
                $almacenProducto=$valor->ALMAP_Codigo;
                
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $nombre_producto = $valor->CPDEC_Descripcion;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                $stock = $datos_almaprod[0]->ALMPROD_Stock;

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $stock;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }

                if ($icbper == NULL || $icbper == "")
                    $icbper = 0;
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $comprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = $icbper;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_comprobante_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->comprobantedetalle_model->listar($comprobante); //(17)lista el detalle de la comprobante
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante); //(27)
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;

        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);
        $ruc = $datos->ruc;
        $razon_social = $datos->nombre;

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $igv100 = round($valor->CPDEC_Igv100, 2);
                $descuento100 = $valor->CPDEC_Descuento100;
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
                $icbper=$valor->CPDEC_ITEMS;
                
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $nombre_producto = $valor->CPDEC_Descripcion;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                $stock = $datos_almaprod[0]->ALMPROD_Stock;

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $stock;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }

                if ($icbper == NULL || $icbper == "")
                    $icbper = 0;
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $comprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = $icbper;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_ocompra_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($comprobante);
        $moneda          = $datos_ocompra[0]->MONED_Codigo;
        $almacenProducto = $datos_ocompra[0]->ALMAP_Codigo;
        $igv100          = $datos_ocompra[0]->OCOMC_igv100;
        $proyecto       = $datos_ocompra[0]->PROYP_Codigo;
        $OrdenCompraEmpresa       = $datos_ocompra[0]->OCOMC_PersonaAutorizada;
        $datos_detalle_ocompra = $this->ocompra_model->obtener_detalle_ocompra2($comprobante);

        $listado = array();
        if (count($datos_detalle_ocompra) > 0) {
            foreach ($datos_detalle_ocompra as $indice => $valor) {
                $detacodi       = $valor->OCOMDEP_Codigo;
                $ocompra        = $valor->OCOMP_Codigo;
                $producto       = $valor->PROD_Codigo;
                $unidad         = $valor->UNDMED_Codigo;
                $lote           = $valor->LOTP_Codigo;
                $tafectacion    = $valor->AFECT_Codigo;
                $cantidad       = $valor->OCOMDEC_Cantidad;
                $costo          = $valor->OCOMDEC_Costo;
                $pu             = $valor->OCOMDEC_Pu;
                $pu_conigv      = $valor->OCOMDEC_Pu_ConIgv;
                $total          = $valor->OCOMDEC_Total;
                $subTotal       = $valor->OCOMDEC_Subtotal;
                $igvocom        = $valor->OCOMDEC_Igv;
                $igvocom100     = $valor->OCOMDEC_Igv100;
                $descuento      = $valor->OCOMDEC_Descuento;
                $descuento100   = $valor->OCOMDEC_Descuento100;
                $flagGenInd     = $valor->OCOMDEC_GenInd;
                $igv            = $valor->OCOMDEC_Igv;
                $observacion    = $valor->OCOMDEC_Observacion;
                
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
                $datos_umedida = is_null($unidad) ? NULL : $this->unidadmedida_model->obtener($unidad);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = $valor->OCOMDEC_Descripcion;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                
                $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }
                
                $filter->TEMPDE_Subtotal    = $subTotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $comprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }
        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "proyecto" => $proyecto,
                "OrdenCompraEmpresa" => $OrdenCompraEmpresa,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_guiarem_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->guiaremdetalle_model->listar($comprobante);
        $lista_detalles = array();
        $datos_guiarem = $this->guiarem_model->obtener($comprobante);
        $moneda = $datos_guiarem[0]->MONED_Codigo;
        $serie = $datos_guiarem[0]->GUIAREMC_Serie;
        $numero = $datos_guiarem[0]->GUIAREMC_Numero;
        $codigo_usuario = $datos_guiarem[0]->GUIAREMC_CodigoUsuario;
        $cliente = $datos_guiarem[0]->CLIP_Codigo;
        $proveedor = $datos_guiarem[0]->PROVP_Codigo;
        $igv100        = $datos_guiarem[0]->GUIAREMC_igv100;
        $descuento100  = $datos_guiarem[0]->GUIAREMC_descuento100;
        $proyecto  = $datos_guiarem[0]->PROYP_Codigo;

        if (count($detalle) > 0) {
            
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->GUIAREMDETP_Codigo;
                $producto = $valor->PRODCTOP_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $cantidad = $valor->GUIAREMDETC_Cantidad;
                $flagGenInd = $valor->GUIAREMDETC_GenInd;
                $pu = $valor->GUIAREMDETC_Pu;
                $subtotal = $valor->GUIAREMDETC_Subtotal;
                $igv = $valor->GUIAREMDETC_Igv;
                $descuento = $valor->GUIAREMDETC_Descuento;
                $total = $valor->GUIAREMDETC_Total;
                $pu_conigv = $valor->GUIAREMDETC_Pu_ConIgv;
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

                $observacion = $valor->GUIAREMDETC_Observacion;
                $nombre_producto = $valor->GUIAREMDETC_Descripcion;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;

                
                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $comprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);

            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos"   => $data,
                "serie"   => $serie,
                "numero"  => $numero,
                "proyecto" => $proyecto
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_presupuesto_referencial($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->presupuestodetalle_model->listar($comprobante);
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($comprobante);
        $formapago = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda = $datos_presupuesto[0]->MONED_Codigo;
        $tipo_doc = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->PRESDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->PRESDEC_Cantidad;
                $igv100 = round($valor->PRESDEC_Igv100, 2);
                $descuento100 = $valor->PRESDEC_Descuento100;
                $pu = round((($tipo_doc == 'F') ? $valor->PRESDEC_Pu : $valor->PRESDEC_Pu_ConIgv - ($valor->PRESDEC_Pu_ConIgv * $igv100 / 100)), 2);
                $subtotal = round((($tipo_doc == 'F') ? $valor->PRESDEC_Subtotal : $pu * $cantidad), 2);
                $igv = round($valor->PRESDEC_Igv, 2);
                $descuento = round($valor->PRESDEC_Descuento, 2);
                $total = round((($tipo_doc == 'F') ? $valor->PRESDEC_Total : $subtotal), 2);
                $pu_conigv = round($valor->PRESDEC_Pu_ConIgv, 2);
                $subtotal_conigv = round($valor->PRESDEC_Subtotal_ConIgv, 2);
                $descuento_conigv = round($valor->PRESDEC_Descuento_ConIgv, 2);
                $observacion = $valor->PRESDEC_Observacion;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = $valor->PRESDEC_Descripcion;
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $costo = $datos_producto[0]->PROD_CostoPromedio;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = 0;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = 1;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = 4;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $comprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

########################################################################################################
########### SE USA CUANDO SE EDITA UN DOCUMENTO ASOCIADO. Ejem. Editar Factura relacionada a Cotización
########################################################################################################

    public function editar_comprobantes_temproductos(){
        $idcomprobante = $this->input->post('comprobante');
        $tipo_docu     = $this->input->post('tipo_docu');
        $tempSession   = $this->input->post('tempSession');
        /*::::: VACIAMOS LA TABLA TEMP DETALLES ::::*/
        //$rspta = $this->temporaldetalle_model->eliminar_temporalProductos_session($tempSession);

        switch ($tipo_docu) {
            case 'F':
                    $datos = $this->introducir_prodtemporal_comprobante($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'B':
                    $datos = $this->introducir_prodtemporal_comprobante($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'N':
                    $datos = $this->introducir_prodtemporal_comprobante($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'C':
                    $datos = $this->introducir_prodtemporal_notas_editar($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'D':
                    $datos = $this->introducir_prodtemporal_notas_editar($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'OC':
                    $datos = $this->introducir_prodtemporal_ocompra($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'OV':
                    $datos = $this->introducir_prodtemporal_ocompra($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'GRC':
                    $datos = $this->introducir_prodtemporal_guiarem($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'GRV':
                    $datos = $this->introducir_prodtemporal_guiarem($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'GT':
                    $datos = $this->introducir_prodtemporal_guiatrans($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'P':
                    $datos = $this->introducir_prodtemporal_pedido($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'R':
                    $datos = $this->introducir_prodtemporal_receta($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            case 'PR':
                    $datos = $this->introducir_prodtemporal_produccion($idcomprobante,$tempSession);
                    echo json_encode($datos);
                break;
            default:
                # code...
                break;
        }
    }

    public function introducir_prodtemporal_receta($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);

        $filterR = new stdClass();
        $filterR->codigo = $comprobante;

        $receta = $this->producto_model->listarRecetas($filterR);
        $listado_detalle = $this->producto_model->detallesReceta($comprobante);

        $lista_detalles = array();

        $moneda = 1;
        $tdc = 0;
        $codigocliente = $datos_pedido[0]->CLIP_Codigo;
        $codigoproyecto = $datos_pedido[0]->PROYP_Codigo;

        if (count($listado_detalle) > 0) {
            foreach ($listado_detalle as $indice => $valor) {
                    $detacodi = $valor->RECDET_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad = 1;
                    $cantidad = $valor->RECDET_Cantidad;

                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $datos_unidad = $this->unidadmedida_model->obtener($unidad);

                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $observacion = ($datos_producto[0]->PROD_DescripcionBreve == NULL) ? "" : $datos_producto[0]->PROD_DescripcionBreve;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = 0;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = 0;
                $filter->TEMPDE_Costo       = 0;
                $filter->TEMPDE_Stock       = $valor->stock;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = 0;

                $filter->TEMPDE_Precio      = 0;
                
                $filter->TEMPDE_Subtotal    = 0;
                $filter->TEMPDE_Descuento   = 0;
                $filter->TEMPDE_Igv         = 0;
                $filter->TEMPDE_TipoIgv     = 4;
                $filter->TEMPDE_Total       = 0;
                $filter->TEMPDE_Igv100      = 0;
                $filter->TEMPDE_Descuento100= 0;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_produccion($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);

        $filterR = new stdClass();
        $filterR->codigo = $comprobante;

        #$receta = $this->producto_model->listarRecetas($filterR);
        $listado_detalle = $this->producto_model->detallesProduccion($comprobante);

        $lista_detalles = array();

        $moneda = 1;
        $tdc = 0;

        if (count($listado_detalle) > 0) {
            foreach ($listado_detalle as $indice => $valor) {
                    $detacodi = $valor->PRD_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad = 1;
                    $cantidad = $valor->PRD_Cantidad;

                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $datos_unidad = $this->unidadmedida_model->obtener($unidad);

                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $observacion = ($datos_producto[0]->PROD_DescripcionBreve == NULL) ? "" : $datos_producto[0]->PROD_DescripcionBreve;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = 0;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = 0;
                $filter->TEMPDE_Costo       = 0;
                $filter->TEMPDE_Stock       = $valor->stock;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = 0;

                $filter->TEMPDE_Precio      = 0;
                
                $filter->TEMPDE_Subtotal    = 0;
                $filter->TEMPDE_Descuento   = 0;
                $filter->TEMPDE_Igv         = 0;
                $filter->TEMPDE_TipoIgv     = 4;
                $filter->TEMPDE_Total       = 0;
                $filter->TEMPDE_Igv100      = 0;
                $filter->TEMPDE_Descuento100= 0;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_pedido($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);

        $this->load->model('compras/pedido_model');
        $this->load->model('compras/pedidodetalle_model');

        $datos_pedido = $this->pedido_model->obtener_pedido($comprobante);
        $listado_detalle = $this->pedidodetalle_model->listar($comprobante);

        $lista_detalles = array();

        $moneda = $datos_pedido[0]->MONED_Codigo;
        $tdc = 0;
        $codigocliente = $datos_pedido[0]->CLIP_Codigo;
        $codigoproyecto = $datos_pedido[0]->PROYP_Codigo;

        if (count($listado_detalle) > 0) {
            foreach ($listado_detalle as $indice => $valor) {
                    $detacodi = $valor->PEDIDETP_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad = $valor->UNDMED_Codigo;
                    $cantidad = $valor->PEDIDETC_Cantidad;

                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $datos_unidad = $this->unidadmedida_model->obtener($unidad);

                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $observacion = ($datos_producto[0]->PROD_DescripcionBreve == NULL) ? "" : $datos_producto[0]->PROD_DescripcionBreve;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = 0;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = 0;
                $filter->TEMPDE_Costo       = 0;
                $filter->TEMPDE_Stock       = "";
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = 0;

                $filter->TEMPDE_Precio      = 0;
                
                $filter->TEMPDE_Subtotal    = 0;
                $filter->TEMPDE_Descuento   = 0;
                $filter->TEMPDE_Igv         = 0;
                $filter->TEMPDE_TipoIgv     = 4;
                $filter->TEMPDE_Total       = 0;
                $filter->TEMPDE_Igv100      = 0;
                $filter->TEMPDE_Descuento100= 0;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    # TIENEN LOTE DE PRODUCTO
    public function introducir_prodtemporal_comprobante($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->comprobantedetalle_model->listar($comprobante);
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante);
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $tdc = $datos_comprobante[0]->CPC_TDC;
        $codigocliente = $datos_comprobante[0]->CLIP_Codigo;
        $codigoproyecto = $datos_comprobante[0]->PROYP_Codigo;
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $pendiente = $valor->CPDEC_Pendiente;
                
                $pu = $valor->CPDEC_Pu;
                $subtotal = $valor->CPDEC_Subtotal;
                $igv = $valor->CPDEC_Igv;
                $tafectacion = $valor->AFECT_Codigo;
                $descuento = $valor->CPDEC_Descuento;
                $total = $valor->CPDEC_Total;
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                
                $subtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
                $descuento_conigv = $valor->CPDEC_Descuento_ConIgv;
                $descuento100 = $valor->CPDEC_Descuento100;
                $igv100 = $valor->CPDEC_Igv100;
                $icbper = $valor->CPDEC_ITEMS;
                
                $observacion = $valor->CPDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->CPDEC_GenInd;
                $costo = $valor->CPDEC_Costo;
                $almacenProducto = $valor->ALMAP_Codigo;
                $codigoGuiaremAsociadaDetalle = $valor->GUIAREMP_Codigo;
                $codigovc = $valor->OCOMP_Codigo_VC;

                $nombre_producto = $valor->CPDEC_Descripcion;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $pendiente;

                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }

                if ($icbper == NULL || $icbper == "")
                    $icbper = 0;
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = $icbper;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    # TIENEN LOTE DE PRODUCTO
    public function introducir_prodtemporal_notas_editar($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $detalle = $this->notacreditodetalle_model->listar($comprobante); //(17)lista el detalle de la comprobante
        $codComprobante = $detalle[0]->CRED_ComproInicio;
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codComprobante); //(27)
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;

        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V')
            $datos = $this->cliente_model->obtener($cliente);
        else if ($datos_comprobante[0]->CPC_TipoOperacion == 'C')
            $datos = $this->proveedor_model->obtener($proveedor);
        
        $ruc = $datos->ruc;
        $razon_social = $datos->nombre;

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CREDET_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $moneda = $valor->MONED_Codigo;
                $cantidad = $valor->CREDET_Cantidad;
                $igv100 = round($valor->CREDET_Igv100, 2);
                $descuento100 = $valor->CREDET_Descuento100;
                $pu = round($valor->CREDET_Pu,2);
                $subtotal = round( $valor->CREDET_Subtotal, 2 );
                $igv = round($valor->CREDET_Igv, 2);
                $tafectacion = $valor->AFECT_Codigo;
                $descuento = round($valor->CREDET_Descuento, 2);
                $total = round( $valor->CREDET_Total, 2);
                $pu_conigv = round($valor->CREDET_Pu_ConIgv, 2);
                $subtotal_conigv = round($valor->CREDET_Subtotal_ConIgv, 2);
                $descuento_conigv = round($valor->CREDET_Descuento_ConIgv, 2);
                $observacion = $valor->CREDET_Observacion;
                $flagGenInd = $valor->CREDET_GenInd;
                $costo = $valor->CREDET_Costo;
                $icbper = $valor->CREDET_FlagICBPER;
                #$almacenProducto=$valor->ALMAP_Codigo;
                
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $nombre_producto = $valor->CREDET_Descripcion;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $datos_almaprod = $this->almacenproducto_model->obtener($almacen, $producto);
                $stock = $datos_almaprod[0]->ALMPROD_Stock;

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                #$filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $stock;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }

                if ($icbper == NULL || $icbper == "")
                    $icbper = 0;
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodComprobante = $codComprobante;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = $icbper;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
       
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    # TIENEN LOTE DE PRODUCTO
    public function introducir_prodtemporal_ocompra($comprobante,$session){
        $compania = $this->compania;
        $detalle = $this->ocompra_model->obtener_detalle_ocompra($comprobante);
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($comprobante);
        $moneda          = $datos_ocompra[0]->MONED_Codigo;
        $almacenProducto = $datos_ocompra[0]->ALMAP_Codigo;
        $igv100          = $datos_ocompra[0]->OCOMC_igv100;
        $descuento100    = $datos_ocompra[0]->OCOMC_descuento100;
        $proyecto       = $datos_ocompra[0]->PROYP_Codigo;
        $OrdenCompraEmpresa       = $datos_ocompra[0]->OCOMC_PersonaAutorizada;

        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {

                $detacodi = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $pendiente = $valor->OCOMDEC_Pendiente;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $igv = $valor->OCOMDEC_Igv;
                $total = $valor->OCOMDEC_Total;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;

                $descuento = $valor->OCOMDEC_Descuento;
                $descuento2 = $valor->OCOMDEC_Descuento2;
                $observacion = $valor->OCOMDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->OCOMDEC_GenInd;
                $costo = $valor->OCOMDEC_Costo; // HELLO WORLD PEOPLE
                $nombre_producto = $valor->OCOMDEC_Descripcion;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $pendiente;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBS;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);
            }
        }

        $result = array();
        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "proyecto" => $proyecto,
                "OrdenCompraEmpresa" => $OrdenCompraEmpresa,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    # TIENEN LOTE DE PRODUCTO
    public function introducir_prodtemporal_guiarem($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
        $datos_guiarem = $this->guiarem_model->obtener($comprobante);
        $moneda        = $datos_guiarem[0]->MONED_Codigo;
        $igv100        = $datos_guiarem[0]->GUIAREMC_igv100;
        $descuento100  = $datos_guiarem[0]->GUIAREMC_descuento100;
        $detalle = $this->guiaremdetalle_model->obtener2($comprobante);

        $datos_ocompra = $this->ocompra_model->obtener_ocompra_guia($comprobante);
        $proyecto       = $datos_ocompra[0]->PROYP_Codigo;
        $OrdenCompraEmpresa       = $datos_ocompra[0]->OCOMC_PersonaAutorizada;
        if (count($detalle) > 0) {

            foreach ($detalle as $indice => $valor) {

                $detacodi = $valor->GUIAREMDETP_Codigo;
                $producto = $valor->PRODCTOP_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $cantidad = $valor->GUIAREMDETC_Cantidad;
                $pu = $valor->GUIAREMDETC_Pu;
                $subtotal = $valor->GUIAREMDETC_Subtotal;
                $igv = $valor->GUIAREMDETC_Igv;
                $tafectacion = $valor->AFECT_Codigo;
                $descuento = $valor->GUIAREMDETC_Descuento;
                $total = $valor->GUIAREMDETC_Total;
                $pu_conigv = $valor->GUIAREMDETC_Pu_ConIgv;
                $costo = $valor->GUIAREMDETC_Costo;
                $almacenProducto = $valor->ALMAP_Codigo;
                $venta = $valor->GUIAREMDETC_Venta;
                $peso = $valor->GUIAREMDETC_Peso;
                $GenInd = $valor->GUIAREMDETC_GenInd;
                $observacion = $valor->GUIAREMDETC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = $valor->GUIAREMDETC_Descripcion;
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;

                if ($datos_unidad){
                    $nombre_unidad = $datos_unidad[0]->UNDMED_Descripcion;
                    $flagBs = "B";
                }
                else{
                    $nombre_unidad = "SERV";
                    $flagBs = "S";
                }

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = $lote;
                $filter->MONED_Codigo       = $moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = (is_null($costo))?0:$costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;
                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = $pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = $pu;
                }
                
                $filter->TEMPDE_Subtotal    = $subtotal;
                $filter->TEMPDE_Descuento   = $descuento;
                $filter->TEMPDE_Igv         = $igv;
                $filter->TEMPDE_TipoIgv     = $tafectacion;
                $filter->TEMPDE_Total       = $total;
                $filter->TEMPDE_Igv100      = $igv100;
                $filter->TEMPDE_Descuento100= $descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBs;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);                 
            }
        }

        $result = array();
        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "proyecto" => $proyecto,
                "OrdenCompraEmpresa" => $OrdenCompraEmpresa,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

    public function introducir_prodtemporal_guiatrans($comprobante,$session){
        $compania = $this->compania;
        $flagIgv = $this->temporaldetalle_model->obtener_flagIgv($compania);
         
        $datos_guiatrans = $this->guiatrans_model->obtener($comprobante);
        $almacenProducto = $datos_guiatrans[0]->GTRANC_AlmacenOrigen;

        $detalle = $this->guiatransdetalle_model->listar($comprobante);
 
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->GTRANDETP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->GTRANDETC_Cantidad;
                $costo = $valor->GTRANDETC_Costo;
                $GenInd = $valor->GTRANDETC_GenInd;
                $descri = str_replace('"', "''", $valor->GTRANDETC_Descripcion);
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $observacion = ""; #str_replace('"', "''", $valor->GUIAREMDETC_Descripcion);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = $datos_unidad[0]->UNDMED_Descripcion;

                if ($datos_unidad){
                    $nombre_unidad = $datos_unidad[0]->UNDMED_Descripcion;
                    $flagBs = "B";
                }
                else{
                    $nombre_unidad = "SERV";
                    $flagBs = "S";
                }

                $filter = new stdClass();
                $filter->TEMPDE_SESSION     = $session;
                $filter->PROD_Codigo        = $producto;
                $filter->UNDMED_Codigo      = $unidad;
                $filter->LOTP_Codigo        = 0;
                $filter->MONED_Codigo       = 1; #$moneda;
                $filter->ALMAP_Codigo       = $almacenProducto;
                $filter->TEMPDE_Costo       = ( is_null($costo) ) ? 0 : $costo;
                $filter->TEMPDE_Stock       = $cantidad;
                $filter->TEMPDE_Cantidad    = $cantidad;
                $filter->TEMPDE_Pendiente   = $cantidad;

                if ($flagIgv[0]->COMPCONFIC_PrecioContieneIgv == "1") {
                    $filter->TEMPDE_Precio      = 0; #$pu_conigv;
                }else{
                    $filter->TEMPDE_Precio      = 0; #$pu;
                }
                
                $filter->TEMPDE_Subtotal    = 0; #$subtotal;
                $filter->TEMPDE_Descuento   = 0; #$descuento;
                $filter->TEMPDE_Igv         = 0; #$igv;
                $filter->TEMPDE_TipoIgv     = 4; #Retiro
                $filter->TEMPDE_Total       = 0; #$total;
                $filter->TEMPDE_Igv100      = 0; #$igv100;
                $filter->TEMPDE_Descuento100= 0; #$descuento100;
                $filter->TEMPDE_Descripcion = $nombre_producto;
                $filter->TEMPDE_Observacion = $observacion;
                $filter->TEMPDE_FlagBs      = $flagBs;
                $filter->TEMPDE_CodDetalle  = $detacodi;
                $filter->TEMPDE_FlagEstado  = 1;
                $filter->TEMPDE_ICBPER      = 0;

                $this->temporaldetalle_model->insertar_productodetalle($filter);                 
            }
        }

        $result = array();
        $data = $this->temporaldetalle_model->mostrar_temporal_producto($session);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        return $result;
    }

########################################################################################################
########### DETALLES DEL TEMPORAL
########################################################################################################

    public function mostrar_temporal_producto(){
        $tempSession = $this->input->post('tempSession');
        $result = array();

        $data = $this->temporaldetalle_model->mostrar_temporal_producto($tempSession);
        if (count($data)>0) {
            $result = array(
                "message" => 1,
                "datos" => $data
            );
        }else{
            $result = array(
                "message" => 0,
                "datos"    => ''
            );
        }

        echo json_encode($result);
    }

    public function tipo_afectacion_temproductos(){
        $idafectacion = $this->input->post('codafectacion');
        $select = ($idafectacion == '') ? 1 : $idafectacion;
        $option = $this->OPTION_generador($this->temporaldetalle_model->listar_afectacion(), 'AFECT_Codigo', 'AFECT_Descripcion',$select);

        $result = array(
            "selected" => $idafectacion,
            "option" => $option
        );

        echo json_encode($result);
    }

    public function OPTION_generador($datos,$indice,$valor,$tam=1){
        $fila = "";
        if($datos!=NULL){
            foreach($datos as $value){
                eval("\$ind = \$value->$indice;");
                eval("\$val = \$value->$valor;");
                $fila .= "<option value='".$ind."'>".$val."</option>'";
            }
        }
        return $fila;
    }    

    public function filtro_familia_temproductos(){
        $listaFamilia = $this->temporaldetalle_model->listar_familia();
        $data = "";
        foreach ($listaFamilia as $key => $value) {
            $data .= "<option value='$value->FAMI_Codigo - $value->FAMI_Descripcion' label='$value->FAMI_Descripcion'>";
        }

        $result = array(
            "selected" => $idmarca,
            "option" => $data
        );
        echo json_encode($result);
    }

    public function filtro_marca_temproductos(){
        $listaMarcas = $this->temporaldetalle_model->listar_marca();
        $data = "";
        foreach ($listaMarcas as $key => $value) {
            $data .= "<option value='$value->MARCP_Codigo - $value->MARCC_Descripcion' label='$value->MARCC_Descripcion'>";
        }

        $result = array(
            "selected" => $idmarca,
            "option" => $data
        );

        echo json_encode($result);
    }

    public function filtro_modelo_temproductos(){
        $marca = $this->input->post('marca');
        $listaModelos = $this->temporaldetalle_model->listar_modelo($marca);

        $data = "";
        foreach ($listaModelos as $key => $value) {
            $data .= "<option value='$value->PROD_Modelo' label='$value->PROD_Modelo'>";
        }
        
        $result = array(
            "selected" => $prodModelo,
            "option" => $data
        );

        echo json_encode($result);
    }

    public function cantidad_ventas_articulo(){
        $producto = $this->input->post('producto');
        $cliente = $this->input->post('cliente');
        $listarVentas = $this->temporaldetalle_model->listarVentas($producto, $cliente);
        echo json_encode($listarVentas);
    }

    public function cantidad_cotizaciones_articulo(){
        $producto = $this->input->post('producto');
        $cliente = $this->input->post('cliente');
        $listarCotizaciones = $this->temporaldetalle_model->listarCotizaciones($producto, $cliente);
        echo json_encode($listarCotizaciones);
    }

}

?>
