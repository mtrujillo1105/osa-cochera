<?php
ini_set('error_reporting', 1);  //bloq stv  pa q al inicio no cargue con error el pdf en firefox

include("system/application/libraries/pchart/pData.php");
include("system/application/libraries/pchart/pChart.php");
include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");

class Notacredito extends CI_Controller{
    private $compania;
    private $usuario;
    private $rol;    
    
    public function __construct(){
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
        //$this->load->library('My_PHPMailer');
        $this->load->library('tokens'); #CARGAR EL TOKEN
        $this->load->library('lib_props');

        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('ventas/notacredito_model');
        $this->load->model('ventas/notacreditodetalle_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/presupuesto_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('configuracion_model');
        
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('tesoreria/pago_model');

        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
        $this->rol = $this->session->userdata('rol');        
        $this->url = base_url();
        
        $this->view_js = array(0 => "ventas/notacredito.js");
        date_default_timezone_set('America/Lima');
    }

    public function index(){
        $this->load->view('seguridad/inicio');
        
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

    public function detalle_comprobante(){
        $comprobante = $this->input->post('comprobante');
        $detalleComprobante = $this->notacredito_model->cargarDetalleComprobante($comprobante);
        $data = array();
        if ($detalleComprobante == NULL) {
            $data = NULL;
        } else {
            $data = $detalleComprobante;
        }

        echo json_encode($data);
    }

    public function obtener_detalle_comprobante($comprobante){
        $detalle = $this->notacreditodetalle_model->listar($comprobante);
        $lista_detalles = array();
        $datos_presupuesto = $this->notacredito_model->obtener_comprobante($comprobante);
        $formapago = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda = $datos_presupuesto[0]->MONED_Codigo;
        $serie = $datos_presupuesto[0]->CRED_Serie;
        $numero = $datos_presupuesto[0]->CRED_Numero;
        $codigo_usuario = $datos_presupuesto[0]->USUA_Codigo;;

        if (isset($datos_presupuesto[0]->CLIP_Codigo)) {
            $cliente = $datos_presupuesto[0]->CLIP_Codigo;
            $temp = $this->obtener_datos_cliente($cliente);
        } else {
            $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
            $temp = $this->obtener_datos_proveedor($proveedor);
        }

        $tipo_doc = $datos_presupuesto[0]->CRED_TipoDocumento;

        $ruc = $temp['numdoc'];
        $razon_social = $temp['nombre'];

        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detpresup = $valor->CREDET_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->CREDET_Cantidad;
                $igv100 = round($valor->CREDET_Igv100, 2);
                $pu = round((($tipo_doc == 'F') ? $valor->CREDET_Pu : $valor->CREDET_Pu_ConIgv - ($valor->CREDET_Pu_ConIgv * $igv100 / 100)), 2);
                $subtotal = round((($tipo_doc == 'F') ? $valor->CREDET_Subtotal : $pu * $cantidad), 2);
                $igv = round($valor->CREDET_Igv, 2);
                $descuento = round($valor->CREDET_Descuento, 2);
                $total = round((($tipo_doc == 'F') ? $valor->CREDET_Total : $subtotal), 2);
                $pu_conigv = round($valor->CREDET_Pu_ConIgv, 2);
                $subtotal_conigv = round($valor->CREDET_Subtotal_ConIgv, 2);
                $descuento_conigv = round($valor->CREDET_Descuento_ConIgv, 2);
                $observacion = $valor->CREDET_Observacion;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = ($valor->CREDET_Descripcion != '' ? $valor->CREDET_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('"', "''", $nombre_producto);
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $costo = $datos_producto[0]->PROD_CostoPromedio;
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;


                $objeto = new stdClass();
                $objeto->CREDET_Codigo = $detpresup;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->UNDMED_Descripcion = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->PROD_GenericoIndividual = $flagGenInd;
                $objeto->PROD_CostoPromedio = $costo;
                $objeto->CREDET_Cantidad = $cantidad;
                $objeto->CREDET_Pu = $pu;
                $objeto->CREDET_Subtotal = $subtotal;
                $objeto->CREDET_Descuento = $descuento;
                $objeto->CREDET_Igv = $igv;
                $objeto->CREDET_Total = $total;
                $objeto->CREDET_Pu_ConIgv = $pu_conigv;
                $objeto->CREDET_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CREDET_Descuento_ConIgv = $descuento_conigv;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;

                if (isset($datos_presupuesto[0]->CLIP_Codigo))
                    $objeto->CLIP_Codigo = $cliente;
                else
                    $objeto->PROVP_Codigo = $proveedor;

                $objeto->MONED_Codigo = $moneda;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PRESUC_Serie = $serie;
                $objeto->PRESUC_Numero = $numero;
                $objeto->PRESUC_CodigoUsuario = $codigo_usuario;

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
            $objeto->PRESUC_CodigoUsuario = '15';
            $lista_detalles[] = $objeto;
        }
        $resultado = json_encode($lista_detalles);

        echo $resultado;
    }

    public function comprobantes($tipo_oper = 'V', $tipo_docu = 'C', $j = '0', $limpia = ''){

        $data['compania'] = $this->compania;
        if ($limpia == '1') {
            $this->session->unset_userdata('fechai');
            $this->session->unset_userdata('fechaf');
            $this->session->unset_userdata('serie');
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
            $this->session->unset_userdata('TipoNota');
        }
        $filter = new stdClass();
        
        if (count($_POST) > 0) {
            $filter->fechai = $this->input->post('fechai');
            $filter->fechaf = $this->input->post('fechaf');
            $filter->serie = $this->input->post('serie');
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
            $filter->CRED_TipoNota = $tipo_docu;
            $this->session->set_userdata(array('fechai' => $filter->fechai, 'fechaf' => $filter->fechaf, 'serie' => $filter->serie, 'numero' => $filter->numero, 'cliente' => $filter->cliente, 'ruc_cliente' => $filter->ruc_cliente, 'nombre_cliente' => $filter->nombre_cliente, 'proveedor' => $filter->proveedor, 'ruc_proveedor' => $filter->ruc_proveedor, 'nombre_proveedor' => $filter->nombre_proveedor, 'producto' => $filter->producto, 'codproducto' => $filter->codproducto, 'nombre_producto' => $filter->nombre_producto, 'TipoNota' => $filter->CRED_TipoNota));
        } else {
            $filter->fechai = $this->session->userdata('fechai');
            $filter->fechaf = $this->session->userdata('fechaf');
            $filter->serie = $this->session->userdata('serie');
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
        
        $filter->CRED_TipoNota = $tipo_docu; // FILTRO SI ES CREDITO O DEBITO PARA LA LISTA
        
        $data['fechai'] = $filter->fechai;
        $data['fechaf'] = $filter->fechaf;
        $data['serie'] = $filter->serie;
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

        $data['registros'] = count($this->notacredito_model->buscar_comprobantes($tipo_oper, $filter));
        $item = $j + 1;
        $lista = array();

        $data['titulo_tabla'] = "RELACIÓN NOTAS DE " . strtoupper($this->obtener_tipo_documento($tipo_docu)) . "S";
        $data['titulo_busqueda'] = "BUSCAR NOTAS DE " . strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_docu'] = $tipo_docu; #"F";

        switch ($tipo_docu) {
            case 'C':
                $data['id_documento'] = 11;
                break;
            case 'D':
                $data['id_documento'] = 12;
                break;
            
            default:
                $data['id_documento'] = "";
                break;
        }

        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper, "tipo_docu" => $tipo_docu));
        $data['scripts'] = $this->view_js;
        $this->layout->view('ventas/notacredito_index', $data);
    }

    public function datatable_comprobantes($tipo_oper = 'V', $tipo_docu = 'C'){

        $data['compania'] = $this->compania;

        $columnas = array(
                            0 => "CRED_Fecha",
                            1 => "CRED_Serie",
                            2 => "CRED_Numero",
                            3 => "CLIC_CodigoUsuario",
                            4 => "nombre",
                            5 => "CRED_ComproFin",
                            6 => "",
                            7 => "",
                            8 => "CRED_total",
                            9 => "CRED_FlagEstado",
                            10 => "",
                            11 => ""
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

        $filter->tipo_docu = $tipo_docu;
        $filter->tipo_oper = $tipo_oper;
        
        $filter->serie = $this->input->post('serie');
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
        
        $filter->CRED_TipoNota = $tipo_docu; // FILTRO SI ES CREDITO O DEBITO PARA LA LISTA
        
        $listado_comprobantes = $this->notacredito_model->getNotas($filter);
        
        $lista = array();
        if (count($listado_comprobantes) > 0) {
            foreach ($listado_comprobantes as $indice => $valor) {
                $tipoOperacion = $valor->CRED_TipoOperacion;
                $codigo = $valor->CRED_Codigo;
                $fecha = mysql_to_human($valor->CRED_Fecha);
                $serie = $valor->CRED_Serie;
                $numero = $valor->CRED_Numero;
                $guiarem_codigo = $valor->CRED_GuiaRemCodigo;
                $docurefe_codigo = $valor->CRED_DocuRefeCodigo;
                $idCliente = $valor->CLIC_CodigoUsuario;
                $nombre = $valor->nombre;
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->CRED_total, 2);
                $estado = $valor->CRED_Flag;
                $estado_programacion = $valor->CRED_FlagEstado;
                $docInicio = $valor->CRED_TipoDocumento_inicio;
                #$docInicio2 = $valor->CRED_TipoDocumento_inicio;
                $docFin = $valor->CRED_TipoDocumento_fin;
                $compInicio = $valor->CRED_ComproInicio;
                $compFin = $valor->CRED_ComproFin;
                $carga = "";
                if($compFin == NULL || $compFin == "NULL" || $compFin == ""){
                    $carga = "<div style='background-color: #004488; padding: 3px; text-align: center; color: #f1f1f1' >GENERADA</div>";
                }else{
                    $carga = "<div style='background-color: #008000; padding: 3px; text-align: center; color: #f1f1f1' >COBRADA</div>";
                }
                $numero_inicio = $valor->CRED_NumeroInicio;
                $numero_fin = $valor->CRED_NumeroFin;

                $img_estado = "";
                $motivoAnulacion = NULL;
                $enviarcorreo = "";

                    if($estado_programacion == 2){                       
                        $datosdocref = explode(" - ",$numero_inicio);
                        $seriedocref = $datosdocref[0];
                        $numerodocref = $datosdocref[1];

                        switch ($docInicio) {
                            case 'F':
                                $docInicio2=1;
                                break;
                            
                            default:
                                $docInicio2=2;
                                break;
                        }
                        
                        $enviarSunat = "<a href=javascript:; onclick='enviarsunat($codigo, $item)'>Aprobar</a>";

                        $img_estado = "<span class='icon-loading loading_$codigo' title='Enviando' height='20px'/></span>";

                        if ($tipo_oper == 'V'){
                            $detaTipoNota = ($filter->CRED_TipoNota == 'C') ? 3 : 4;
                            $respSunat = $this->comprobante_model->lsResSunat($codigo, $detaTipoNota);
                            if ($respSunat != NULL){
                                if ( $respSunat->respuestas_deta != "" )
                                    $enviarSunat .= "<br> <span class='detallesWrong'>Denegado <span class='detallesWrong2'> $respSunat->respuestas_deta </span> </span>";
                            }
                        }

                    }
                    else
                        if ($estado_programacion == '0') {
                            $enviarSunat = "";
                            $carga = "<div style='background-color: red; padding: 3px; text-align: center; color: #fff' >ANULADA</div>";
                            
                            $motivoAnulacion = explode(' * ',$valor->CRED_Observacion);
                            $size = count($motivoAnulacion);
                            $motivo = ($size > 1) ? $motivoAnulacion[$size-1] : 'SIN MOTIVO REGISTRADO.';
                            $img_estado = "<div> <img src='".base_url()."public/images/icons/inactive.png' alt='Anulado' title='Anulado'/> <br> <span class='detallesAnulado'>Anulado <span class='detallesAnulado2'><b>DETALLES:</b> $motivo </span> </span> </div>";
                        }
                    else {
                        $enviarSunat = "";
                        if ( $estado == 1 )
                            $img_estado = "<a href='" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_usuario2/$serie/$codigo/$tipo_oper/$tipo_docu"."' data-fancybox data-type='iframe'> <img src='" . base_url() . "public/images/icons/active.png' alt='Activo' title='Activo'/></a>";
                    }
                

                if ($this->rol == '1' || strcasecmp($_SESSION['user_name'], 'ccapasistemas') === 0) { // Rol Administrador
                    if($estado_programacion == 2) {
                        $editar = "<a href='javascript:;' onclick=editar_comprobante('$codigo','$tipo_oper','$tipo_docu') target='_parent'><img src='" . base_url() . "public/images/icons/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    }
                    else
                       if($estado_programacion == 0) {
                            $editar = ""; # "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />";
                        }
                    else{
                        $editar = "<img src='".base_url()."public/images/icons/icono_aprobar.png' height='16px' alt='GENERADA' />";
                    }
                }
                else {
                    $editar = "";
                }

                $ver = "<a href='".base_url()."index.php/ventas/notacredito/ver_pdf/$codigo/TICKET' data-fancybox data-type='iframe'><img src='" . base_url() . "public/images/icons/icono_imprimir.png' width='16' height='16' border='0' title='ver ticket'></a>";
                $ver2 = "<a href='".base_url()."index.php/ventas/notacredito/ver_pdf/$codigo/A4' data-fancybox data-type='iframe'><img src='" . base_url() . "public/images/icons/pdf.png' width='16' height='16' border='0' title='ver A4'></a>";

                $pdfSunat = "";

                if ($estado_programacion == 1 && $tipo_oper == 'V' && $docInicio != "N") {
                    $pdfSunat = "<a href='#' onclick='abrir_pdf_envioSunat($codigo);' target='_parent'><img src='" . base_url() . "public/images/icons/pdf-sunat.png' width='16' height='16' border='0' title='pdf sunat'></a>";
                    #$enviarcorreo =  "<a onclick=abrir_envioSunat($codigo) href='#' class='enviarcorreo'><img src='" . base_url() . "images/send.png' width='16' height='16' border='0' title='Enviar Factura via correo'></a>" ;
                    $enviarcorreo =  "<a onclick='open_mail($codigo);' href='javascript:;' class='enviarcorreo'><img src='" . base_url() . "public/images/icons/send.png' width='16' height='16' border='0' title='Enviar documento via correo'></a>";
                }
                
                // Eliminar
                $eliminar = "<a href='javascript:;' onclick='eliminar_comprobante(" . $codigo . ")' target='_parent'><img src='" . base_url() . "public/images/icons/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                $lista[] = array(
                                    0 => $fecha,
                                    1 => $serie,
                                    2 => $numero,
                                    3 => $idCliente,
                                    4 => $nombre,
                                    5 => $carga,

                                    6 => "<div> <a href='".base_url()."index.php/ventas/comprobante/comprobante_ver_pdf/$compInicio/a4' data-fancybox data-type='iframe'>$numero_inicio</a> </div>",
                                    7 => "<div class='docDestino_data_$item'> <a href='".base_url()."index.php/ventas/notacredito/ver_pdf/$codigo/A4' data-fancybox data-type='iframe'>$numero_fin</a> </div>",

                                    8 => $total,
                                    9 => $img_estado,
                                    10 => "<div align='center' class='editar_data_$item'>$editar</div>",
                                    11 => $ver,
                                    12 => $ver2,
                                    13 => "<div align='center' class='pdfSunat_$item'> <span class='pdfSunat_data_$item'>$pdfSunat</span> </div>",

                                    14 => "<div align='center' class='enviarcorreo_data_$item'>$enviarcorreo</div>",
                                    15 => "<div align='center' class='disparador_$item'> <span class='icon-loading'></span> <span class='disparador_data_$item'>$enviarSunat</span> </div>"
                                );
                $item++;
            }
        }
        
        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;
        $filterAll->tipo_docu = $tipo_docu;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->notacredito_model->getNotas($filterAll)),
                            "recordsFiltered" => intval( count($this->notacredito_model->getNotas($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }


    /**
     * Aqui se crear una nota de credito ya sea para V(clientes) o C(proveedores)
     * Las notas tiene un origen y fin
     * Una nota de credito es un credito que esta vinculado a una factura, boleta o comprobante aunque no es necesario(independiente)
     * Las nota reemplaza el dinero real por un dinero que solo se podra gastar en productos que se venden
     * Las notas obligatoriamente al final de su estado deven ser vinculadas a una Factura, Boleta o Comprobante
     * @param string $tipo_oper
     * @param string $tipo_docu
     * @see comprobante($tipo_oper, $tipo_docu, $j, $limpia)
     */
    public function comprobante_nueva($tipo_oper = 'V', $tipo_docu = 'C'){
        
        unset($_SESSION['serie']);

        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data['compania'] = $this->compania;
        $compania = $data['compania'];
        $codigo = "";
        $data['codigo'] = $codigo;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['url_action'] = base_url() . "index.php/ventas/notacredito/comprobante_insertar";
        $data['titulo'] = "REGISTRAR NOTA DE ";
        $data['titulo'] .= ($tipo_docu == 'C') ? "CREDITO" : "DEBITO"; #strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu'] = $tipo_docu;
        $data['tipo_oper'] = $tipo_oper;
        $data['formulario'] = "frmComprobante";
        $data['oculto'] = $oculto;
        $lista_almacen = $this->almacen_model->seleccionar();
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:200px;' id='almacen'");


        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '2');
        //$data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
        $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
        $data['tdc'] = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));
        date_default_timezone_set("America/Lima");
        
        //Ingresamo el tipo de cambio
        $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));
        if (count($cambio_dia) > 0) {
            //$data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
            $data['tdcDolar'] = 1;
        } else {
            //$data['tdcDolar'] = '';
            $data['tdcDolar'] = 1;
        }
        $data['serie'] = '';
        $data['numero'] = '';
        if ($tipo_oper == 'V') {
            $temp = $this->obtener_serie_numero($tipo_docu);
        }
        $data['idCompIni'] = "";
        $data['ltCompIni'] = "";
        $data['numserref'] = "";
        $data['numdocref'] = "";
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
        $data['guiarem_codigo'] = "";
        $data['docurefe_codigo'] = "";
        $data['estado'] = "1";

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
        if ($tipo_docu == 'C') {//nota de credito
            $tipo = 11;
        }
        if ($tipo_docu == 'D') {//nota de debito
            $tipo = 12;
        }
        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);

        $data['serie'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;

        $data['serie_suger_b'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_b'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_f'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;
        $data['scripts'] = $this->view_js;
        $this->layout->view('ventas/notacredito_nueva', $data);
    }

    public function ventana_muestra_notadecredito($tipo_oper = 'V', $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = ''){
        $filter = new stdClass();
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

        $filter->cliente = $cliente;
        $filter->proveedor = $proveedor;

        #$lista_comprobante = $this->notacredito_model->buscar_comprobantes_asoc($tipo_oper, $docu_orig, $filter);

        $lista = array();
        /*$tipoDocumento = 0;
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->CPP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
                if ($value->CPC_TipoDocumento == 'F') {
                    $tipoDocumento = 1; // Factura
                }
                else if ($value->CPC_TipoDocumento == 'B') {
                    $tipoDocumento = 2; // Boleta
                }
                else if ($value->CPC_TipoDocumento == 'N'){
                    $tipoDocumento = 3; // Comprobante
                }
                else {
                    $tipoDocumento = 0;
                }

                $select = "<a href='javascript:;' onclick=seleccionar_comprobante('$value->CPP_Codigo') ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->CPC_Fecha), $value->CPC_Serie, $value->CPC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->CPC_total), $ver, $select);

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
        $data['form_open'] = form_open(base_url() . "index.php/ventas/notacredito/ventana_muestra_notadecredito", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));

        $this->load->view('ventas/ventana_muestra_notadecredito', $data);
    }

    public function datatable_ventana_comprobantes(){

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

        $lista_comprobante = $this->notacredito_model->getComprobantesAsoc($filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(\"$value->CPP_Codigo\")'><img src='".base_url()."public/images/icons/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = "<a href='javascript:;' onclick=seleccionar_comprobante('$value->CPP_Codigo') ><img src='" . base_url() . "public/images/icons/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
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
                            "recordsTotal"    => count($this->notacredito_model->getComprobantesAsoc($filterAll)),
                            "recordsFiltered" => intval( count($this->notacredito_model->getComprobantesAsoc($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function relacionar_comprobante(){
        $codigo = $this->input->post("comprobante");
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        if ($datos_comprobante != NULL){
            foreach ($datos_comprobante as $key => $value) {
                $info = array(
                                "comprobante" => $value->CPP_Codigo,
                                "tipo_documento" => $value->CPC_TipoDocumento,
                                "serie" => $value->CPC_Serie,
                                "numero" => $value->CPC_Numero,
                                "moneda" => $value->MONED_Codigo,
                                "forma_pago" => $value->FORPAP_Codigo,
                                "descuento" => $value->CPC_descuento100,
                                "vendedor" => $value->CPC_Vendedor,
                                "almacen" => $value->ALMAP_Codigo
                            );
            }
            $json = array( "result" => "success", "info" => $info);
        }
        else
            $json = array( "result" => "warning", "info" => NULL);
            
        echo json_encode($json);
    }

    public function comprobante_insertar(){
        $mensaje = array();

        if ($this->input->post('serie') == '')
            $mensaje = array(
                'mensaje' => 'SERIE',
                'descripcion' => 'NO TIENE SERIE LA NOTA'
            );
        if ($this->input->post('numero') == '')
            $mensaje = array(
                'mensaje' => 'NUMERO',
                'descripcion' => 'NO TIENE NUMERO LA NOTA'
            );
        if ($this->input->post('observacion') == '')
            $mensaje = array(
                'mensaje' => 'Observacion',
                'descripcion' => 'NO TIENE OBSERVACION LA NOTA'
            );

        $tipo_oper = $this->input->post('tipo_oper'); // V o C
        $docuOrigen = $this->input->post('origenDocumento');

        $tipo_docu = $this->input->post('tipo_docu');
        $compania = $this->compania;

        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $comprobante = $this->input->post('guiaReferente');
        $numComprobante = $comprobante;

        if ($tipo_oper == 'V') {
            if ($tipo_docu == 'C') { # Nota de credito
                $tipo = 11;
            }
            if ($tipo_docu == 'D') { # Nota de debito
                $tipo = 12;
            }

            $serieInfo = $this->configuracion_model->obtener_numero_documento($compania, $tipo, $docuOrigen);
            $serie = strtoupper($docuOrigen.$serieInfo[0]->CONFIC_Serie);
            $numero = $serieInfo[0]->CONFIC_Numero + 1;
            
            $this->configuracion_model->modificar_configuracion($compania, $tipo, $numero);
        }

        $filter = new stdClass();
        $filter->CRED_FlagEstado = $this->input->post('estado');

        if ($tipo_oper == 'V') {
            $filter->CLIP_Codigo = $this->input->post('cliente');
        } else {
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        }

        $filter->CRED_TipoOperacion = $tipo_oper;

        $filter->CRED_TipoDocumento_inicio = $docuOrigen; // ORIGEN**************************************************** CRED_TipoDocumento_inicio

        if ($docuOrigen == "A") { // Valido si existe un comprobante
            $filter->CRED_NumeroInicio = "------";
        }else{
            $filter->CRED_ComproInicio = $this->input->post('guiaReferente');
            $numInicio = $this->input->post('idNumero');
            $serInicio = $this->input->post('idSerie');
            $filter->CRED_NumeroInicio = $serInicio . " - " . $numInicio; // SERIE Y NUMERO DE FACTURA ****************************** CRED_NumeroInicio
        }

        /*if ( isset($this->input->post('tdc')) && !empty($this->input->post('tdc')) ){
            $tdc = $this->input->post('tdc');
        }
        else{*/
            $tdcambio = $this->tipocambio_model->obtener_tdc_dolar( date('Y-m-d') );
            $tdc = $tdcambio[0]->TIPCAMC_FactorConversion;
        /*}*/

        $filter->CRED_Flag = 1;
        $filter->COMPP_Codigo = $compania;
        $filter->CRED_Serie = $serie;
        $filter->CRED_Numero = $numero;
        $filter->USUA_Codigo = $this->usuario;
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CRED_subtotal = $this->input->post('preciototal');
        $filter->CRED_descuento = $this->input->post('descuentotal');
        $filter->CRED_igv = $this->input->post('igvtotal');
        $filter->CRED_total = $this->input->post('importetotal');
        $filter->CRED_descuento100 = $this->input->post('descuento');
        $filter->CRED_igv100 = $this->input->post('igv');
        $filter->CRED_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CRED_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->CRED_TDC = $tdc;
        $filter->CRED_FechaRegistro = date('Y-m-d H:i:s');
        $filter->CRED_FlagEstado = 2;

        $filter->DOCUP_Codigo = $this->input->post('motivoNota'); // AQUI GUARDO EL MOTIVO DE LA NOTA DE CREDITO
        $filter->CRED_TipoNota = $tipo_docu; // Importante

        
        $nota = $this->notacredito_model->insertar_notaCredito($filter);


        if($nota == NULL) {
            $mensaje = array(
                'mensaje' => 'NOTA',
                'descripcion' => 'NO SE PUDO INSERTAR CORRECTAMENTE LA NOTA'
            );
        }else{

            // TODO - INSERTAR CJI_NOTADETALLE

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
            $proddescri = $this->input->post('proddescri');
            $obs_detalle = $this->input->post('prodobservacion');
            $tafectacion = $this->input->post('tafectacion');
            $lote = $this->input->post('idLote');
            $icbper = $this->input->post('icbper');

            $detalles = 0;
            $totalProductos = count($prodcodigo);

            if ($totalProductos > 0) {
                $updateStock = array();
                foreach ($prodcodigo as $indice => $valor) {
                    $detalle_accion = $detaccion[$indice];
                
                    $filter = new stdClass();
                    $filter->CRED_Codigo = $nota;
                    $filter->PROD_Codigo = $prodcodigo[$indice];
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                    $filter->LOTP_Codigo = $lote[$indice];
                    $filter->AFECT_Codigo = $tafectacion[$indice];
                    $filter->CREDET_FlagICBPER = $icbper[$indice];
                    $filter->CREDET_Cantidad = $prodcantidad[$indice];
                    $filter->CREDET_Pu = $prodpu[$indice];
                    $filter->CREDET_Subtotal = $prodprecio[$indice];
                    $filter->CREDET_Descuento = $proddescuento[$indice];
                    $filter->CREDET_Igv = $prodigv[$indice];
                    $filter->CREDET_Total = $prodimporte[$indice];
                    $filter->CREDET_Pu_ConIgv = $prodpu_conigv[$indice];
                    $filter->CREDET_Igv100 = $prodigv100[$indice];
                    $filter->CREDET_Descuento100 = $proddescuento100[$indice];
                    $filter->CREDET_Costo = $prodcosto[$indice];
                    $filter->CREDET_Descripcion = strtoupper($proddescri[$indice]);
                    $filter->CREDET_Observacion = $obs_detalle[$indice];
                    $filter->CREDET_FlagEstado = 1;

                    if ($detalle_accion != 'e'){ // Si el articulo no esta marcado como eliminado.
                        #$filter->CREDET_FlagEstado = 0;
                    
                        $notadetalle = $this->notacreditodetalle_model->insertar($filter);

                        $updateStock['PROD_Codigo'][] = $filter->PROD_Codigo; // Para actualizar el Stock
                        $updateStock['CREDET_Cantidad'][] = $filter->CREDET_Cantidad; // Para actualizar el stock

                        if($notadetalle){
                            $detalles++;
                        }
                    }
                    else{
                        $totalProductos--;
                    }
                }

                # ACTUALIZAR EL STOCK
                /*if ( $this->input->post('almacen') != "" && $this->input->post('almacen') != NULL && $this->input->post('almacen') != false ){
                    $this->comprobante_model->actualizarStock($numComprobante, $updateStock, $this->input->post('almacen'));
                }
                else{
                    $this->comprobante_model->actualizarStock($numComprobante, $updateStock);
                }*/

                # DEVOLVER STOCK true
                #$this->comprobante_eliminar($numComprobante, true);                
            }

            if($totalProductos != $detalles){
                $mensaje = array(
                    'mensaje' => 'NOTA DETALLE',
                    'descripcion' => 'NO SE PUDO INSERTAR TODOS LOS PRODUCTOS EN LA NOTA, Productos insertados['.$detalles.']'
                );
            }else{

                $mensaje = array(
                    'mensaje' => 'SUCCESS',
                    'descripcion' => 'SE REGISTRO CORRECTAMENTE LA NOTA DE CREDITO'
                );
            }
        }
        echo json_encode($mensaje);
    }

    public function comprobante_editar($codigo, $tipo_oper = 'V', $tipo_docu = 'C'){
        
        unset($_SESSION['serie']);
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data['compania'] = $this->compania;

        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        $datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        $serie = $datos_comprobante[0]->CRED_Serie;
        $numero = $datos_comprobante[0]->CRED_Numero;
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->CRED_subtotal;
        $descuento = $datos_comprobante[0]->CRED_descuento;
        $igv = $datos_comprobante[0]->CRED_igv;
        $total = $datos_comprobante[0]->CRED_total;
        $subtotal_conigv = $datos_comprobante[0]->CRED_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CRED_descuento_conigv;
        $igv100 = $datos_comprobante[0]->CRED_igv100;
        $descuento100 = $datos_comprobante[0]->CRED_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->CRED_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CRED_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->CRED_Observacion;
        $modo_impresion = $datos_comprobante[0]->CRED_ModoImpresion;
        $estado = $datos_comprobante[0]->CRED_FlagEstado;
        $fecha = mysql_to_human($datos_comprobante[0]->CRED_Fecha);
        $vendedor = $datos_comprobante[0]->CRED_Vendedor;
        $tdc = $datos_comprobante[0]->CRED_TDC;
        $motivoNota = $datos_comprobante[0]->DOCUP_Codigo;
        $idCompIni = $datos_comprobante[0]->CRED_ComproInicio;
        $ltCompIni = $datos_comprobante[0]->CRED_TipoDocumento_inicio;
        $num_serdoc_ref = $datos_comprobante[0]->CRED_NumeroInicio;
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
        $data['serie'] = $serie;
        $data['numero'] = $numero;

        //$data['doc_ref'] = $tipodoc_ref;
        $data['motivoNota'] = $motivoNota;
        $array_sernum_ref = explode(' - ', $num_serdoc_ref);
        $data['idCompIni'] = $idCompIni;
        $data['ltCompIni'] = $ltCompIni;
        $data['numserref'] = $array_sernum_ref[0];
        $data['numdocref'] = $array_sernum_ref[1];

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
        $data['url_action'] = base_url() . "index.php/ventas/notacredito/comprobante_modificar";
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

        $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

        $data['tdcDolar'] = $moneda > 2 ? $datos_comprobante[0]->CPC_TDC_opcional : $tdc;
        $data['tdcEuro'] = $moneda > 2 ? $tdc : '';

        if (count($cambio_dia) > 0) {
            $data['tdcDolar'] = $cambio_dia[0]->TIPCAMC_FactorConversion;
        } else {
            $data['tdcDolar'] = '';
        }

        #$detalle_comprobante = $this->obtener_lista_detalles($codigo);
        #$data['detalle_comprobante'] = $detalle_comprobante;

        #$detalle_comprobante = $this->obtener_lista_detalles($codigo);
        #$data['detalle_comprobante'] = $detalle_comprobante;
        
        $this->layout->view('ventas/notacredito_nueva', $data);
    }

    public function comprobante_modificar(){

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

        $codigo = $this->input->post('codigo');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');


        $filter = new stdClass();
        #$filter->FORPAP_Codigo = NULL;
        /* if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
          $filter->FORPAP_Codigo = $this->input->post('forma_pago'); */
        #$filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->CRED_Observacion = strtoupper($this->input->post('observacion'));
        $filter->CRED_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->CRED_Numero = $this->input->post('numero');
        $filter->CRED_Serie = $this->input->post('serie');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CRED_descuento100 = $this->input->post('descuento');
        $filter->CRED_igv100 = $this->input->post('igv');
        $filter->DOCUP_Codigo = $this->input->post('motivoNota'); // Aqui se guarda el motivo de la nota de credito
        #$filter->DOCUP_Codigo = $this->input->post('doc_ref');
        #$filter->CRED_NumeroInicio = $this->input->post('numserref') . ' - ' . $this->input->post('numdocref');
        if ($tipo_oper == 'V')
            $filter->CLIP_Codigo = $this->input->post('cliente');
        else
            $filter->PROVP_Codigo = $this->input->post('proveedor');
        
        #$filter->CRED_ComproInicio = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CRED_ComproInicio = $this->input->post('guiaReferente');
        $dataComprobante = $this->notacredito_model->docReferencia($filter->CRED_ComproInicio);

            $filter->CRED_TipoDocumento_inicio = $dataComprobante[0]->CPC_TipoDocumento; // ORIGEN***** CRED_TipoDocumento_inicio
            $filter->CRED_NumeroInicio = $dataComprobante[0]->CPC_Serie . " - " . $dataComprobante[0]->CPC_Numero; // SERIE Y NUMERO DE FACTURA ****************************** CRED_NumeroInicio        
        
        $filter->CRED_FlagEstado = 2;
                
            $filter->CRED_subtotal = $this->input->post('preciototal');
            $filter->CRED_descuento = $this->input->post('descuentotal');
            $filter->CRED_igv = $this->input->post('igvtotal');
            $filter->CRED_subtotal_conigv = $this->input->post('preciototal_conigv');
            $filter->CRED_descuento_conigv = $this->input->post('descuentotal_conigv');
        
        $filter->CRED_total = $this->input->post('importetotal');
        #$filter->CRED_Vendedor = NULL;
        
        #if ($this->input->post('vendedor') != '')
        #    $filter->CRED_Vendedor = $this->input->post('vendedor');

        $this->notacredito_model->modificar_comprobante($codigo, $filter);

        $prodcodigo = $this->input->post('prodcodigo');
        $flagBS = $this->input->post('flagBS');
        $prodcantidad = $this->input->post('prodcantidad');
            $prodpu = $this->input->post('prodpu');
            $prodprecio = $this->input->post('prodprecio');
            $proddescuento = $this->input->post('proddescuento');
            $prodigv = $this->input->post('prodigv');
            $prodprecio_conigv = $this->input->post('prodprecio_conigv');
            $proddescuento_conigv = $this->input->post('proddescuento_conigv');
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodcosto = $this->input->post('prodcosto');
        $proddescri = $this->input->post('proddescri');
        $obs_detalle = $this->input->post('prodobservacion');
        $tafectacion = $this->input->post('tafectacion');
        $lote = $this->input->post('idLote');
        $icbper = $this->input->post('icbper');

        if (is_array($detacodi) > 0) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];

                $filter = new stdClass();
                $filter->CRED_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                if ($flagBS[$indice] == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];

                $filter->LOTP_Codigo = $lote[$indice];
                $filter->AFECT_Codigo = $tafectacion[$indice];

                $filter->CREDET_FlagICBPER = $icbper[$indice];
                $filter->CREDET_Cantidad = $prodcantidad[$indice];
                //if ($tipo_docu != 'B') {
                    $filter->CREDET_Pu = $prodpu[$indice];
                    $filter->CREDET_Subtotal = $prodprecio[$indice];
                    $filter->CREDET_Descuento = $proddescuento[$indice];
                    $filter->CREDET_Igv = $prodigv[$indice];
                //} else {
                    $filter->CREDET_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                    $filter->CREDET_Descuento_ConIgv = $proddescuento_conigv[$indice];
                //}
                $filter->CREDET_Total = $prodimporte[$indice];
                $filter->CREDET_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->CREDET_Descuento100 = $proddescuento100[$indice];
                $filter->CREDET_Igv100 = $prodigv100[$indice];
                if ($tipo_oper == 'V')
                    $filter->CREDET_Costo = $prodcosto[$indice];
                $filter->CREDET_Descripcion = strtoupper($proddescri[$indice]);
                $filter->CREDET_Observacion = $obs_detalle[$indice];

                if ($detalle_accion == 'n') {
                    $this->notacreditodetalle_model->insertar($filter);
                } elseif ($detalle_accion == 'm') {
                    $this->notacreditodetalle_model->modificar($valor, $filter);
                } elseif ($detalle_accion == 'e') {
                    $this->notacreditodetalle_model->eliminar($valor);
                }
            }
        }
        exit('{"mensaje":"SUCCESS", "descripcion":"SE ACTUALIZO CORRECTAMENTE LA NOTA", "codigo":"' . $codigo . '"}');
    }

    public function envioSunatFac( $codigo ){
        
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->compania);
        $data['compania'] = $this->compania;
        $usercod = $this->usuario;

        $datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
        $cod_nota = $datos_comprobante[0]->CRED_Codigo;
        $tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra = $datos_comprobante[0]->OCOMP_Codigo;
        $guiaremision = $datos_comprobante[0]->GUIAREMP_Codigo;
        $serie = $datos_comprobante[0]->CRED_Serie;
        $numero = trim($datos_comprobante[0]->CRED_Numero);
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda = $datos_comprobante[0]->MONED_Codigo;
        $subtotal = $datos_comprobante[0]->CRED_subtotal;
        $descuento = $datos_comprobante[0]->CRED_descuento;
        $igv = $datos_comprobante[0]->CRED_igv;
        $total = $datos_comprobante[0]->CRED_total;
        $subtotal_conigv = $datos_comprobante[0]->CRED_subtotal_conigv;
        $descuento_conigv = $datos_comprobante[0]->CRED_descuento_conigv;
        $igv100 = $datos_comprobante[0]->CRED_igv100;
        $descuento100 = $datos_comprobante[0]->CRED_descuento100;
        $guiarem_codigo = $datos_comprobante[0]->CRED_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CRED_DocuRefeCodigo;
        $observacion = $datos_comprobante[0]->CRED_Observacion;
        $modo_impresion = $datos_comprobante[0]->CRED_ModoImpresion;
        $estado = $datos_comprobante[0]->CRED_FlagEstado;
        $fecha = mysql_to_human($datos_comprobante[0]->CRED_Fecha);
        $vendedor = $datos_comprobante[0]->CRED_Vendedor;
        $tdc = $datos_comprobante[0]->CRED_TDC;
        $tipoNota = $datos_comprobante[0]->DOCUP_Codigo; // Aqui guardo el tipo o motivo de la nota de credito o debito
        $num_serdoc_ref = $datos_comprobante[0]->CRED_NumeroRef;
        $comprobante_inicio = $datos_comprobante[0]->CRED_ComproInicio;

        /* BEGIN DOCUMENTO DE REFERENCIA */
            $tipoDocReferencia = ($datos_comprobante[0]->CRED_TipoDocumento_inicio == 'F') ? 1 : 2; // Tipo de documento relacionado, Facturas => 1 | Boletas => 2
            $letraDocReferencia = $datos_comprobante[0]->CRED_TipoDocumento_inicio; // letra del documento para utilizar en la serie
            $SerieNumero = explode(" - ",$datos_comprobante[0]->CRED_NumeroInicio); // Numero y serie de factura o boleta => 3 - 239
            $SerieReferencia = trim($SerieNumero[0]); // Elimina espacios en blanco si encuentra
            $numeroReferencia = trim($SerieNumero[1]);// Elimina espacios en blanco si encuentra

            $tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
            
            switch ($tipo_docu) { // Tipo de comprobante a enviar al facturador
                case 'C':
                    $tipo_de_comprobante = 3; // Notas de credito => 3
                    break;
                case 'D':
                    $tipo_de_comprobante = 4; // Notas de debito => 4
                    break;
            }
            
            $tipoNotaCredito = ($tipo_de_comprobante == 3) ? $tipoNota : ""; // Si el tipo de comprobante es nota de credito envia el tipo de nota
            $tipoNotaDebito = ($tipo_de_comprobante == 4) ? $tipoNota : ""; // Si el tipo de comprobante es nota de debito envia el tipo de nota
            
            /* Tokens */
            $compania = $this->compania;
            $deftoken = $this->tokens->deftoken($compania); // Selecciono el token de acuerdo a la compañia
            $ruta = $deftoken['ruta'];
            $token = $deftoken['token'];
                
            /* Series */
            //$SerieReferencia = $serie; # Serie del documento de referencia
            $serieFac = $serie; # Serie de la nota
        /* END DOCUMENTO DE REFERENCIA */
        
        $ruc_cliente = '';
        $nombre_cliente = '';
        $direccion ='';

        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $tipoCli = $datos_cliente->tipo; // 0 => natural | 1 => Juridico
                $tipoDocIdentidad = $datos_cliente->tipoDocIdentidad; // RUC, NIC, PASAPORTE, DNI, CARNET

                $ruc_cliente = ( $ruc_cliente == "" ) ? $dni_cliente : $ruc_cliente;
                $direccion   = $datos_cliente->direccion;
            }
        }

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

        $items=array();
        $gravada = 0;
        $inafecto = 0;
        $exonerado = 0;
        $gratuito = 0;
        $totalBolsa = 0;

        if (count($detalle_comprobante) > 0) {
            foreach ($detalle_comprobante as $indice => $valor) {
                $unidad_medida = $valor->UNDMED_Codigo;
                $tipo_afectacion = $valor->AFECT_Codigo;
                $codigo_usuario = $valor->PROD_CodigoUsuario;
                $codigo_original = ($valor->PROD_CodigoOriginal != "" AND $valor->PROD_CodigoOriginal > 0) ? $valor->PROD_CodigoOriginal : ""; # ESTE ES EL CODIGO PRODUCTO SUNAT
                $nombre_producto = $valor->PROD_Nombre.". ".$valor->CREDET_Observacion;
                $prodcantidad = $valor->CREDET_Cantidad;
                $medidaDetalle = $valor->UNDMED_Simbolo;
                $proddescuento = $valor->CREDET_Descuento;
                $precio_con_igv = $valor->CREDET_Pu_ConIgv;
                $precio_sin_igv = $valor->CREDET_Pu; #( $tipo_afectacion == 1 ) ? round( $precio_con_igv / (1.18), 5) : $precio_con_igv;
                $igvxprod = $valor->CREDET_Igv;
                $prodsubtotal = $valor->CREDET_Subtotal;
                $prodtotal = $valor->CREDET_Total;
                $afectacionInfo = $this->producto_model->tipo_afectacion($tipo_afectacion);

                switch ($tipo_afectacion) {
                    case 1: # GRAVADA
                        $gravada += $prodsubtotal;
                        break;
                    case 8: # EXONERADO
                        $exonerado += $prodsubtotal;
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; # " [EXONERADA]";
                        break;
                    case 9: # INAFECTO
                        $inafecto += $prodsubtotal;
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; # " [INAFECTA]";
                        break;
                    case 16: # EXPORTACION SE GUARDA COMO INAFECTO
                        $inafecto += $prodsubtotal;
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; # " [INAFECTA]";
                        break;
                    default:
                        $nombre_producto = $nombre_producto . " " . $afectacionInfo[0]->AFECT_Descripcion; # " [GRATUITA]";
                        $gratuito += $prodsubtotal;
                    break;
                }

                $totalBolsa = ( $valor->CREDET_FlagICBPER == "1" ) ? $valor->CREDET_Total + $totalBolsa : $totalBolsa; # SI TIENE BOLSA SUMA
                $importeBolsa = ( $valor->CREDET_FlagICBPER == "1" ) ? $valor->CREDET_Total : 0; # SI TIENE BOLSA SUMA

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
        }
        
        $cliente_tipoDoc = "";
        $cliente_document = $ruc_cliente;

        // if else con 2 switch debido a que la busqueda del documento se hace en distintas tablas y devuelven los mismos id para distintos documentos
        if ($tipoCli == 1){
            switch ($tipoDocIdentidad){
                case '1': // RUC -> DB
                    $cliente_tipoDoc = '6'; // RUC -> Facturador
                    break;
                default:
                    $cliente_tipoDoc ="6"; // RUC
                    break;
                # EN EL MANUAL DE FACTURADOR NO EXISTE N.I.C. U OTROS, SOLO RUC
            }
        }
        else{
                switch ($tipoDocIdentidad) {
                    case '1': // DNI -> DB
                        $cliente_tipoDoc = '1'; // DNI -> FACTURADOR
                        break;
                   case '2': // CARNET DE EXTRANJERIA -> DB
                       $cliente_tipoDoc = '4'; // CARNET DE EXTRANJERIA -> FACTURADOR
                        break;
                   case '3': // PASAPORTE -> DB
                        $cliente_tipoDoc = '7'; // PASAPORTE -> FACTURADOR
                        break;
                   default:
                   $cliente_tipoDoc ="-"; // VARIOS
                       break;
                }
                $cliente_tipoDoc = ($ruc_cliente == "-" || $ruc_cliente == "00000000") ? "-" : $cliente_tipoDoc; // Si el numero de documento "-" o 0, el tipo sera cliente varios
        }
        
        if ($tipo_oper == 'C'){
            // si la operacion es compra, actualiza el flag de la nota a 1 = aprobada y termina la ejecucion de la funcion luego de llamar a la vista
            $filter2 = array('CPP_codigo' => $codigo,
                            'respuestas_tipoDocumento' => $tipo_docu,
                            'respuestas_serie' => $serie,
                            'respuestas_numero' => $numero,
                            'CRED_FlagEstado' => '1'
                            );
            $this->notacredito_model->sunatEnviado($codigo,$filter2);
            if ($tipo_de_comprobante == 3){ // Si es nota de credito => 3
                $resultUpdate = $this->comprobante_model->actualizarStock($comprobante_inicio, $cod_nota, $tipo_de_comprobante); // Añade al inventario los productos de la nota de credito
            }
            #redirect("ventas/notacredito/comprobantes/$tipo_oper/$tipo_docu",'refresh');
            #exit();
            $success = array( "result" => "success", "pdf" => $pdf );
        }
        else{
            $data2 = array(
                            "operacion"                         => "generar_comprobante",
                            "tipo_de_comprobante"               => "${tipo_de_comprobante}",
                            "serie"                             => "${serieFac}",
                            "numero"                            => "{$numero}",
                            "sunat_transaction"                 => "1",
                            "cliente_tipo_de_documento"         => "${cliente_tipoDoc}",
                            "cliente_numero_de_documento"       => "${cliente_document}",
                            "cliente_denominacion"              => "${nombre_cliente}",
                            "cliente_direccion"                 => "${direccion}",
                            "cliente_email"                     => "",
                            "cliente_email_1"                   => "",
                            "cliente_email_2"                   => "",
                            "fecha_de_emision"                  => date('d-m-Y'),
                            "fecha_de_vencimiento"              => "",
                            "moneda"                            => "1",
                            "tipo_de_cambio"                    => "${tdc}",
                            "porcentaje_de_igv"                 => "${igv100}",
                            "descuento_global"                  => "",
                            "descuento_global"                  => "",
                            "total_descuento"                   => "",
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
                            "observaciones"                     => "",
                            "documento_que_se_modifica_tipo"    => "${tipoDocReferencia}",
                            "documento_que_se_modifica_serie"   => "${SerieReferencia}",
                            "documento_que_se_modifica_numero"  => "${numeroReferencia}",
                            "tipo_de_nota_de_credito"           => "${tipoNotaCredito}",
                            "tipo_de_nota_de_debito"            => "${tipoNotaDebito}",
                            "enviar_automaticamente_a_la_sunat" => "true",
                            "enviar_automaticamente_al_cliente" => "false",
                            "codigo_unico"                      => "",
                            "condiciones_de_pago"               => "",
                            "medio_de_pago"                     => "",
                            "placa_vehiculo"                    => "",
                            "orden_compra_servicio"             => "",
                            "tabla_personalizada_codigo"        => "",
                            "formato_de_pdf"                    => "",
                            "items"                             => $items
                        );
            
            if ( $letraDocReferencia != "N" ){
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
                }
                else{
                    $respuesta2->serie = $serieFac;
                    $respuesta2->numero = $numero;
                    $respuesta2->tipo_de_comprobante = $tipo_de_comprobante;
                    $respuesta2->enlace = "0";
                    $respuesta2->aceptada_por_sunat = "0";
                    $respuesta2->sunat_description = "0";
                    $respuesta2->sunat_note = "0";
                    $respuesta2->sunat_responsecode = "0";
                    $respuesta2->sunat_soap_error = "0";
                    $respuesta2->pdf_zip_base64 = "0";
                    $respuesta2->xml_zip_base64 = "0";
                    $respuesta2->cdr_zip_base64 = "0";
                    $respuesta2->cadena_para_codigo_qr = "0";
                    $respuesta2->codigo_hash = "0";
                    $respuesta2->enlace_del_pdf = "0";
                    $respuesta2->enlace_del_xml = "0";
                    $respuesta2->enlace_del_cdr = "0";
                }
                
                $exito = false;
                
                if( !isset($respuesta2->errors) ) {
                    if ( $respuesta2->enlace_del_pdf != NULL && $respuesta2->enlace_del_pdf != ''){
                        $filter2->respuestas_compañia = $compania;
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

                if ( $exito == true ){ # SI LA NOTA FUE ACEPTADA ACTUALIZA EL STOCK
                    # Si es nota de credito, los tipos son distintos a descuentos y no hay respuesta de error -> mueve el stock
                    if ( $tipo_de_comprobante == 3 && $tipoNota != 4 && $tipoNota != 5 && $tipoNota != 8 && $tipoNota != 9 )
                        $resultUpdate = $this->comprobante_model->actualizarStock($comprobante_inicio, $cod_nota, $tipo_de_comprobante); # Añade al inventario los productos de la nota de credito
                    
                    $this->notacredito_model->sunatEnviado($codigo, $filter2); // Si la nota fue aceptada, cambia su flag
                        
                    $pdf = ( $letraDocReferencia != "N" ) ? "<a href='#' onclick='abrir_pdf_envioSunat($codigo);' target='_parent'><img src='".base_url()."images/pdf-sunat.png' width='16' height='16' border='0' title='pdf sunat'></a>" : "";
                    $destino = "<a href='".base_url()."index.php/ventas/notacredito/ver_pdf/$codigo/A4' data-fancybox data-type='iframe'>$serie-$numero</a>";
                    $success = array( "result" => "success", "pdf" => $pdf, "docDestino" => $destino );
                }
                else
                    $success = array( "result" => "error", "msg" => $filter2->respuestas_deta );

                $this->comprobante_model->insertar_respuestaSunat($filter2);
            }
        echo json_encode($success);
    }

    public function consutarRespuestaPdfsunat( $codigoRespCompro ){
        $pdfRespSunat = $this->notacredito_model->consultar_respuestaSunat($codigoRespCompro);
        
        if ( $pdfRespSunat->respuestas_enlacepdf == NULL || $pdfRespSunat->respuestas_enlacepdf == '' ){
            $exito = $this->ConsultarComprobanteNubefact( $codigoRespCompro );
            if ($exito == true)
                $pdfRespSunat = $this->notacredito_model->consultar_respuestaSunat($codigoRespCompro);
        }
        echo json_encode($pdfRespSunat);
    }

    public function consultarRespuestaXmlsunat($codigo = null, $ventana = true){
        $pdfRespSunat = $this->notacredito_model->consultar_respuestaXMLSunat($codigo);

        if ( $pdfRespSunat->respuestas_enlacexml == NULL || $pdfRespSunat->respuestas_enlacexml == '' ){
            $exito = $this->ConsultarComprobanteNubefact( $codigo );
            if ($exito == true)
                $pdfRespSunat = $this->notacredito_model->consultar_respuestaSunat($codigo);
        }
        
        if ($ventana == true)
            echo json_encode($pdfRespSunat);
        else
            return $pdfRespSunat->respuestas_enlacexml;
    }

    public function getInfoSendMail(){
        $nota = $this->input->post("id");
        $notaInfo = $this->notacredito_model->getNotaMail($nota);

        $data = array();

        foreach ($notaInfo as $i => $val) {

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
                                "codigo" => $val->CRED_Codigo,
                                "nombre" => $val->razon_social,
                                "ruc" => $val->ruc,
                                "serie" => $val->CRED_Serie,
                                "numero" => $val->CRED_Numero,
                                "fecha" => mysql_to_human($val->CRED_Fecha),
                                "importe" => $val->MONED_Simbolo . " " . $val->CRED_total,
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

        $datos_notacredito = $this->notacredito_model->obtener_comprobante($codigo);

        $cliente = $datos_notacredito[0]->CLIP_Codigo;
        $fecha = mysql_to_human($datos_notacredito[0]->CRED_Fecha);
        
        $respuestaSunatCompr= $this->notacredito_model->consultar_respuestaSunat($codigo);

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

        switch ($datos_notacredito[0]->CRED_TipoNota) {
            case 'D':
                $documento = "NOTA DE DEBITO";
                break;
            case 'C':
                $documento = "NOTA DE CREDITO";
                break;            
            default:
                $documento = "NOTA DE CREDITO";
                break;
        }
        
        $RespuestaCadema = $respuestaSunatCompr->respuestas_cadenaparacodigoqr;
        $arrayRespuestaCadema = explode('|',$RespuestaCadema);
        $data['prueba']= $arrayRespuestaCadema;
        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['serie'] = $arrayRespuestaCadema[2];
        $data['numero'] = $arrayRespuestaCadema[3];
        $data['documento'] = $documento;
        $data['pdfsunatresp'] = $respuestaSunatCompr->respuestas_enlace;

        $data['nomFechaEmi'] = $arrayRespuestaCadema[6];
        $data['nomTotal'] = $arrayRespuestaCadema[5];
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['nombre_persona1'] = $nombre_persona1;
        $data['emailusuario'] = $_SESSION['user_name']."@osa-fact.com";
        $data['emailenviar'] = $emailenviar;
        $data['titulo'] = "ENVIAR $documento ELECTRONICA - CORREO";
        $data['formulario'] = "frmPresupuestoCorreo";
        $data['url_action'] = base_url() . "index.php/ventas/notacredito/Enviarcorreo";
        $data['hoy'] = $fecha;
        $data['codigo'] = $codigo;


        $data['tipo_codificacion'] = 1;
        $this->load->view('ventas/notacredito_correo', $data);
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
                                                    <td style="padding:0px; font-size:12pt;"><b>'.$nombre_empresa.'</b>, ENVIA UN DOCUMENTO ELECTRONICO.</td>
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

    public function ConsultarComprobanteNubefact( $codigo ){
        $datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
        $tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
        $serie = $datos_comprobante[0]->CRED_Serie;
        $numero = ltrim($datos_comprobante[0]->CRED_Numero, "0");
        
        $tipo_docu = $datos_comprobante[0]->CRED_TipoNota;
            
        switch ($tipo_docu) { // Tipo de comprobante a enviar al facturador
            case 'C':
                $tipo_de_comprobante = 3; // Notas de credito => 3
                break;
            case 'D':
                $tipo_de_comprobante = 4; // Notas de debito => 4
                break;
        }
        
        /* Tokens */
            $compania = $this->compania;
            $deftoken = $this->tokens->deftoken($compania); // Selecciono el token de acuerdo a la compañia
            $ruta = $deftoken['ruta'];
            $token = $deftoken['token'];
        /* Series */
        
        $data2 = array(
            "operacion"             => "consultar_comprobante",
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
            $exito = true;
            $this->comprobante_model->insertar_respuestaSunat($filter2);
        }
        else{
            $exito = false;
        }

        return $exito;
    }
    //FIN enviar sunat

    public function ctaXpagar($tipo_cuenta = 2, $nota, $cliente, $proveedor){
         
        $filterres = new stdClass();
        $filtero = new stdClass();
        $usuario = $this->usuario;
        $compania = $this->compania;
          
        $datos = array();

        $filter = new stdClass();
        $filter->PAGC_TipoCuenta = $tipo_cuenta;
        $filter->PAGC_FechaOper = human_to_mysql( date('Y-m-d') );
        
        if ($tipo_cuenta == 1)
            $filter->CLIP_Codigo = $cliente;
        else
            $filter->PROVP_Codigo = $proveedor;

        $notaDetalles = $this->notacredito_model->obtener_comprobante($nota);
        
        $tdc = $notaDetalles[0]->CRED_TDC;
        $monto = $notaDetalles[0]->CRED_total;
        $moneda = $notaDetalles[0]->MONED_Codigo;
        $codigoComprobanteInicio = $notaDetalles[0]->CRED_ComproInicio;
        $formapago = 5;
        $fecha = $notaDetalles[0]->CRED_Fecha;

        $filter->PAGC_TDC = $notaDetalles[0]->CRED_TDC;
        $filter->PAGC_Monto = $notaDetalles[0]->CRED_total;
        $filter->MONED_Codigo = $notaDetalles[0]->MONED_Codigo;
        $filter->PAGC_FormaPago = $formapago;
        $filter->PAGC_NotaCredito = $nota;
        $filter->COMPP_Codigo = $compania;
        
        $totalComprobante = $this->notacredito_model->obtener_total_comprobante_original($codigoComprobanteInicio);
        $monto = $totalComprobante->total - $monto;

        if($tipo_cuenta == '1')
            $tipo = 20;
        else
            $tipo = 21;

        $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        $filter->PAGP_Serie = $configuracion_datos[0]->CONFIC_Serie;
        $filter->PAGP_Numero = $cofiguracion_datos[0]->CONFIC_Numero + 1;

        $cod_pago = $this->pago_model->insertar($filter, $tipo_cuenta, $formapago, $compania);

        $listado_cuentas = $this->cuentas_model->obtener($cod_cuentaspago);
        
        $filter2 = new stdClass();
        $filter2->PAGP_Codigo = $cod_pago;
        $filter2->CPAGC_TDC = $tdc;
        $filter2->CPAGC_Monto = $monto;
        $filter2->CPAGC_FechaRegistro = date('Y-m-d h:m:s');
        $filter2->CPAGC_FlagEstado = 1;
        $filter2->MONED_Codigo = $moneda;

        $cod_cuentaspago = $this->cuentaspago_model->insertar($filter2);

        $detaCuenta = $this->cuentas_model->sum_pagos_cuenta($codigoComprobanteInicio);
        $totalPgos = $detaCuenta[0]->Tpagos;
        $totalCuenta = $detaCuenta[0]->CUE_Monto;

        $this->cuentas_model->modificar_estado($cod_cuentaspago, ($totalPgos == $totalCuenta ? 'C' : 'A'));

        $datosComprobante = $this->notacredito_model->buscarComprobante_nota($codigoComprobanteInicio);
        if($datosComprobante != NULL){
            $insertNotaCredito = $this->notacredito_model->modificar_notaCredito($datosComprobante, $nota);
        }
    }

    function obtener_datos_cliente($cliente, $tipo_docu = 'F'){
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
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
        }

        return array('numdoc' => $numdoc, 'nombre' => $nombre, 'direccion' => $direccion);
    }

    function obtener_datos_proveedor($proveedor, $tipo_docu = 'F'){
        $datos = $this->proveedor_model->obtener_datosProveedor($proveedor);

        //$datos_cliente = $this->cliente_model->obtener_datosCliente($proveedor);
        $empresa = $datos[0]->EMPRP_Codigo;
        $persona = $datos[0]->PERSP_Codigo;
        $tipo = $datos[0]->PROVC_TipoPersona;
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            if ($tipo_docu != 'B')
                $numdoc = $datos_persona[0]->PERSC_Ruc;
            else
                $numdoc = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $direccion = $datos_persona[0]->PERSC_Direccion;
        } elseif ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
        }

        return array('numdoc' => $numdoc, 'nombre' => $nombre, 'direccion' => $direccion);
    }


    public function obtener_lista_detalles($codigo){
        $detalle = $this->notacreditodetalle_model->listar($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CREDET_Codigo;
                $producto = $valor->PROD_Codigo;

                $tdc = $valor->CRED_TDC;
                $unidad = $valor->UNDMED_Codigo;
                $tipo_afectacion = $valor->AFECT_Codigo;
                $cantidad = $valor->CREDET_Cantidad;
                $pu = $valor->CREDET_Pu;
                $subtotal = $valor->CREDET_Subtotal;
                $igv = $valor->CREDET_Igv;
                $descuento = $valor->CREDET_Descuento;
                $total = $valor->CREDET_Total;
                $pu_conigv = $valor->CREDET_Pu_ConIgv;
                $subtotal_conigv = $valor->CREDET_Subtotal_ConIgv;
                $descuento_conigv = $valor->CREDET_Descuento_ConIgv;
                $descuento100 = $valor->CREDET_Descuento100;
                $igv100 = $valor->CREDET_Igv100;
                $observacion = $valor->CREDET_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $GenInd = $valor->CREDET_GenInd;
                $costo = $valor->CREDET_Costo;
                $icbper = $valor->CREDET_FlagICBPER;
                $nombre_producto = ($valor->CREDET_Descripcion != '') ? $valor->CREDET_Descripcion : $datos_producto[0]->PROD_Nombre;

                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_original = $datos_producto[0]->PROD_CodigoOriginal;
                
                $umDetalle = $this->unidadmedida_model->obtener($unidad);
                $nombre_unidad = ($umDetalle[0]->UNDMED_Simbolo != "") ? $umDetalle[0]->UNDMED_Simbolo : "ZZ";

                $objeto = new stdClass();
                $objeto->CREDET_Codigo = $detacodi;
                $objeto->CRED_TDC = $tdc;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->PROD_CodigoOriginal = $codigo_original;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->AFECT_Codigo = $tipo_afectacion;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->CREDET_GenInd = $GenInd;
                $objeto->CREDET_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CREDET_Cantidad = $cantidad;
                $objeto->CREDET_Pu = $pu;
                $objeto->CREDET_Subtotal = $subtotal;
                $objeto->CREDET_Descuento = $descuento;
                $objeto->CREDET_Igv = $igv;
                $objeto->CREDET_Total = $total;
                $objeto->CREDET_Pu_ConIgv = $pu_conigv;
                $objeto->CREDET_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CREDET_Descuento_ConIgv = $descuento_conigv;
                $objeto->CREDET_Descuento100 = $descuento100;
                $objeto->CREDET_Igv100 = $igv100;
                $objeto->CREDET_Observacion = $observacion;
                $objeto->CREDET_FlagICBPER = $icbper;
                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }

    //gcbq
    public function obtener_detalle_notadecredito($comprobante, $tipo_oper = 'V', $almacen = ""){
        $detalle = $this->notacreditodetalle_model->listar($comprobante);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CREDET_Codigo;
                $producto = $valor->PROD_Codigo;
                //echo $producto;exit;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->CREDET_Cantidad;
                $pu = $valor->CREDET_Pu;
                $subtotal = $valor->CREDET_Subtotal;
                $igv = $valor->CREDET_Igv;
                $descuento = $valor->CREDET_Descuento;
                $total = $valor->CREDET_Total;
                $pu_conigv = $valor->CREDET_Pu_ConIgv;
                $subtotal_conigv = $valor->CREDET_Subtotal_ConIgv;
                $descuento_conigv = $valor->CREDET_Descuento_ConIgv;
                $descuento100 = $valor->CREDET_Descuento100;
                $igv100 = $valor->CREDET_Igv100;
                $observacion = $valor->CREDET_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->CREDET_GenInd;
                $costo = $valor->CREDET_Costo;
                $nombre_producto = ($valor->CREDET_Descripcion != '' ? $valor->CREDET_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\', '', $nombre_producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $objeto = new stdClass();
                $objeto->CREDET_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->CREDET_GenInd = $GenInd;
                $objeto->CREDET_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CREDET_Cantidad = $cantidad;
                $objeto->CREDET_Pu = $pu;
                $objeto->CREDET_Subtotal = $subtotal;
                $objeto->CREDET_Descuento = $descuento;
                $objeto->CREDET_Igv = $igv;
                $objeto->CREDET_Total = $total;
                $objeto->CREDET_Pu_ConIgv = $pu_conigv;
                $objeto->CREDET_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CREDET_Descuento_ConIgv = $descuento_conigv;
                $objeto->CREDET_Descuento100 = $descuento100;
                $objeto->CREDET_Igv100 = $igv100;
                $objeto->CREDET_Observacion = $observacion;
                $lista_detalles[] = $objeto;
            }
        }
        $resultado = json_encode($lista_detalles);
        echo $resultado;
    }

    /**
     * Fondo
     *  0 : Imprimir
     *  1 : PDF
     *  Codigo, el comprobante seleccionado
     *  Tipo_docu, Factura, Boleta o Comprobante
     * @param int $fondo
     * @param $codigo
     * @param string $tipo_docu
     */
    public function ver_pdf($codigo, $formato = "A4"){
        switch ($formato) {
            case "A4":
                $this->lib_props->nota_pdf_a4($codigo, 1);
                break;
            case "TICKET":
                $this->lib_props->nota_pdf_ticket($codigo);
                break;
            default:
                $this->lib_props->nota_pdf_a4($codigo, 1);
                break;
        }
    }

    public function comprobante_ver_html($codigo, $tipo_docu = 'F'){

        if ($tipo_docu != 'B') {
            $this->formatos_de_impresion_F($codigo, $tipo_docu);
        } else {
            $this->formatos_de_impresion_B($codigo, $tipo_docu);
        }
    }

    /**
     * Fondo
     *  0 : Imprimir
     *  1 : PDF
     * @param int $fondo
     * @param $codigo
     * @param string $tipo_docu
     */

    public function comprobante_ver_pdf_conmenbrete_formato1($codigo, $flagPdf = 1, $tipo_oper = 'V'){
        $datos_comprobante = $this->notacredito_model->obtener_comprobante($codigo);
        $detalle_comprobante = $this->notacreditodetalle_model->listar($codigo);
        // DATOS DEL COMPROBANTE
        $companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
        $tipo_oper = $datos_comprobante[0]->CRED_TipoOperacion;
        $serie = $datos_comprobante[0]->CRED_Serie;
        $numero = $this->lib_props->getOrderNumeroSerie(trim($datos_comprobante[0]->CRED_Numero));

        $descuento = $datos_comprobante[0]->CRED_descuento;
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

        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
        $consulta_guia = NULL; #$this->comprobante_model->buscar_guiarem_comprobante($codigo);
        $guiaRemision = "";
        foreach ($consulta_guia as $key => $value) {
            $guiaRemision .= "$value->GUIAREMC_Serie - $value->GUIAREMC_Numero<br>";
        }

        /*FORMA DE PAGO*/
        $formapago_desc = "EFECTIVO";
        $formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        $datos_formapago = $this->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS

        // DATOS DEL USUARIO
        $vendedor = $datos_comprobante[0]->USUA_Codigo;
        /*$vendedor = $datos_comprobante[0]->USUA_Codigo;
        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;*/
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;

        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $ruc = ( $ruc_cliente == "" ) ? $dni_cliente : $ruc_cliente;
                $direccion   = $datos_cliente->direccion;
                $email   = $datos_cliente->correo;
            }
            $tp = "CLIENTE";
        }
        else
            if ($proveedor != '' && $proveedor != '0') {
                $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                if ($datos_proveedor) {
                    $nombre_cliente = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                    $direccion   = $datos_proveedor->direccion;
                }
                $tp = "PROVEEDOR";
            }

        $this->load->library("tcpdf");
        $medidas = "a4"; // a4 - carta
        $this->pdf = new pdfComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->SetTitle('COMPROBANTE ELECTRONICO');
        $this->pdf->SetFont('helvetica', '', 8);

        if ($flagPdf == 1)
            $this->pdf->setPrintHeader(true);
        else
            $this->pdf->setPrintHeader(false);

        $this->pdf->AddPage();

        /* Listado de detalles */
            $detaProductos = "";
            foreach ($detalle_comprobante as $indice => $valor) {
                $datos_producto = $this->producto_model->obtener_producto($valor->PROD_Codigo);
                $codigoUsuario = $datos_producto[0]->PROD_CodigoUsuario;

                $nomprod = $valor->CREDET_Descripcion;
                $nomprod = ($valor->CREDET_Observacion != '') ? $nomprod . " <br> " .$valor->CREDET_Observacion : $nomprod;

                #$nomprod = ($valor->CPDEC_GenInd == 'I') ? $nomprod.$this->ObtenerSeriesComprobante($codigo,$tipo_docu,$valor->PROD_Codigo) : $nomprod;
                $unidadMedida = $this->unidadmedida_model->obtener($valor->UNDMED_Codigo);
                $medidaDetalle = "";
                $medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

                    $detaProductos = $detaProductos. '
                    <tr>
                        <td style="text-align:right;">'.$valor->CREDET_Cantidad.'</td>
                        <td style="text-align:center;">'.$medidaDetalle.'</td>
                        <td style="text-align:center;">'.$codigoUsuario.'</td>
                        <td style="text-align:left;">'.$nomprod.'</td>
                        <td style="text-align:right;">'.number_format($valor->CREDET_Pu, 2).'</td>
                        <td style="text-align:right;">'.number_format($valor->CREDET_Subtotal, 2).'</td>
                    </tr>';
                
            }

        $this->load->model("maestros/emprestablecimiento_model");
        $this->load->model("maestros/compania_model");

        $datosCompania = $this->compania_model->obtener($companiaComprobante);
        $datosEstablecimiento = $this->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
        $datosEmpresa =  $this->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

        $tipoDocumento = "";
        
        switch ($datos_comprobante[0]->CRED_TipoNota) {
            case 'D':
                $tipoDocumento = ($tipo_oper == 'V') ?  "NOTA DE DEBITO DE VENTA<br>ELECTRÓNICA" :  "NOTA DE DEBITO DE COMPRA<br>ELECTRÓNICA";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "NOTA DE DEBITO DE VENTA ELECTRÓNICA" :  "NOTA DE DEBITO DE COMPRA ELECTRÓNICA";
                break;
            case 'C':
                $tipoDocumento = ($tipo_oper == 'V') ?  "NOTA DE CREDITO DE VENTA<br>ELECTRÓNICA" :  "NOTA DE CREDITO DE COMPRA<br>ELECTRÓNICA";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "NOTA DE CREDITO DE VENTA ELECTRÓNICA" :  "NOTA DE CREDITO DE COMPRA ELECTRÓNICA";
                break;
            default:
                $tipoDocumento = ($tipo_oper == 'V') ?  "NOTA DE CREDITO DE VENTA<br>ELECTRÓNICA" :  "NOTA DE CREDITO DE COMPRA<br>ELECTRÓNICA";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "NOTA DE CREDITO DE VENTA ELECTRÓNICA" :  "NOTA DE CREDITO DE COMPRA ELECTRÓNICA";
                break;
        }

        switch ($datos_comprobante[0]->CRED_TipoDocumento_inicio) {
            case 'F':
                $tdocumento = "FACTURA";
                break;
            case 'B':
                $tdocumento = "BOLETA";
                break;
            case 'N':
                $tdocumento = "COMPROBANTE";
                break;
            case 'A':
                $tdocumento = "N/A";
                break;
            default:
                $tdocumento = "N/A";
                break;
        }

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

        $logo = ""; #'<img src="'.base_url().'images/cabeceras/logo.jpg" height="70px"/>';

        // RECTANGULO REDONDEADO SERIE DOCUMENTO
        // $x, $y, $w, $h, $r, $round_corner='1111', $style='', $border_style=array(), $fill_color=array()
            $this->pdf->RoundedRect(125, 8, 80, 30, 1.50, '1111', 'DF');

        $cabeceraHTML = '
                <table align="left">
                    <tr>
                        <td style="width:12cm;">'.$logo.'</td>
                        <td style="width:8cm; font-size:14pt; text-align:center;" rowspan="2">
                            <div style="background-color:#E2E2E2;">
                                <b>RUC '.$datosEmpresa[0]->EMPRC_Ruc.'</b>
                                <br><b>'.$tipoDocumento.'</b>
                                <br><b>'.$serie.' - '.$numero.'</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-indent:0.5cm">
                            <br style="text-indent:0.5cm">
                            <br style="text-indent:0.5cm">
                        </td>
                    </tr>
                </table>';
                        /*<td style="text-indent:0.5cm"><b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b>
                            <br style="text-indent:0.5cm">'.$datosEstablecimiento[0]->EESTAC_Direccion.'
                            <br style="text-indent:0.5cm">'.$datosEstablecimiento[0]->distrito.' - '.$datosEstablecimiento[0]->provincia.' - '.$datosEstablecimiento[0]->departamento.'
                        </td>*/

        $this->pdf->writeHTML($cabeceraHTML,false,false,true,'');

        $this->pdf->RoundedRect(5, 41, 125, 25, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(132, 41, 73, 25, 1.50, '1111', ''); // FECHA

        $clienteHTML = '<table style="text-indent:0.5cm;" cellpadding="0.02cm">
                    <tr>
                        <td colspan="3" style="width:12.5cm;"><b>'.$tp.'</b></td>

                        <td style="width:3.5cm; font-weight:bold;">FECHA EMISIÓN:</td>
                        <td>'.$fecha.'</td>
                    </tr>
                    <tr>
                        <td style="width:3.5cm; font-weight:bold;">RUC:</td>
                        <td style="width:8cm; text-indent:-0.1cm;">'.$ruc.'</td>

                        <td style="width:1cm;"></td>
                        <td style="width:3.5cm; font-weight:bold;">MONEDA:</td>
                        <td>'.$moneda_nombre.'</td>
                    </tr>
                    <tr>
                        <td style="width:3.5cm; font-weight:bold;">DENOMINACIÓN:</td>
                        <td style="width:8cm; text-indent:-0.1cm; text-align:justification">'.$nombre_cliente.'</td>

                        <td style="width:1cm;"></td>
                        <td style="width:3.5cm; font-weight:bold;">IGV:</td>
                        <td>'.$igv100.'%</td>
                    </tr>
                    <tr> 
                        <td style="width:3.5cm; font-weight:bold;">DIRECCIÓN:</td>
                        <td style="width:8cm; text-indent:-0.1cm; text-align:justification">'.$direccion.'</td>
                        <td colspan="3"></td>
                    </tr>
                </table> <br><br><br> &nbsp;'; // el &nbsp; aplica el espacio en blanco

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        #$this->pdf->RoundedRect(5, 73, 200, 8, 1.50, '1111', ''); // PRODUCTOS
        $productoHTML = '
                <table>
                    <tr>
                        <th style="font-weight:bold; text-align:right; width:1cm;">CANT.</th>
                        <th style="font-weight:bold; text-align:center; width:2cm;">UM</th>
                        <th style="font-weight:bold; text-align:center; width:2cm;">CÓD.</th>
                        <th style="font-weight:bold; text-align:left; width:10.5cm;">DESCRIPCIÓN</th>
                        <th style="font-weight:bold; text-align:right; width:2cm;">P/U</th>
                        <th style="font-weight:bold; text-align:right; width:2cm;">IMPORTE</th>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                    '.$detaProductos.'
                </table>';
        $this->pdf->writeHTML($productoHTML,true,false,true,'');

        $totalesHTML = ' <br><br><br> &nbsp;
                <table align="right" cellspacing="0.1cm">
                    <tr>
                        <td style="width:16cm; font-weight:bold;">SUBTOTAL</td>
                        <td style="width:1cm; font-weight:bold;">'.$moneda_simbolo.'</td>
                        <td style="width:2cm; font-weight:bold;">'.number_format($subtotal, 2).'</td>
                    </tr>
                    <tr>
                        <td style="width:16cm; font-weight:bold;">DESCUENTO</td>
                        <td style="width:1cm; font-weight:bold;">'.$moneda_simbolo.'</td>
                        <td style="width:2cm; font-weight:bold;">'.number_format($descuento, 2).'</td>
                    </tr>
                    <tr>
                        <td style="width:16cm; font-weight:bold;">GRAVADA</td>
                        <td style="width:1cm; font-weight:bold;">'.$moneda_simbolo.'</td>
                        <td style="width:2cm; font-weight:bold;">'.number_format($subtotal-$descuento, 2).'</td>
                    </tr>
                    <tr>
                        <td style="width:16cm; font-weight:bold;">IGV</td>
                        <td style="width:1cm; font-weight:bold;">'.$moneda_simbolo.'</td>
                        <td style="width:2cm; font-weight:bold;">'.number_format($igv, 2).'</td>
                    </tr>
                    <tr>
                        <td style="width:16cm; font-weight:bold;">TOTAL</td>
                        <td style="width:1cm; font-weight:bold;">'.$moneda_simbolo.'</td>
                        <td style="width:2cm; font-weight:bold;">'.number_format($total, 2).'</td>
                    </tr>
                </table>&nbsp;'; // el &nbsp; aplica el espacio en blanco';

        $this->pdf->writeHTML($totalesHTML,true,false,true,'');

        $leyendaSelva = "";

        $guiaRem = ($guiaRemision != '') ? '<tr>
                                                <td style="width:4.5cm;"></td>
                                                <td style="text-align:left; width:3.8cm;"><b>GUIA DE REMISIÓN:</b></td>
                                                <td style="width:11.6cm;">'.$guiaRemision.'</td>
                                            </tr>' : '';
                    /*<tr>
                        <td style="width:4.5cm;"></td>
                        <td style="text-align:left; width:3.8cm"><b>CONDICIONES DE PAGO:</b></td>
                        <td style="width:11.6cm;">'.$formapago_desc.'</td>
                    </tr>*/

        $footerHTML = '
                <br><br><br>&nbsp;
                <table cellpadding="0.025cm" border="0">
                    <tr>
                        <td style="width:4.1cm;"></td>
                        <td style="text-align:left; width:15.5cm" ><b>IMPORTE EN LETRAS:</b> '.strtoupper(num2letras(round($total, 2))).'</td>
                    </tr>
                    <tr>
                        <td style="width:4.1cm;"></td>
                        <td style="text-align:left; width:15.5cm"><b>MOTIVO DE EMISIÓN:</b> 0'.$motivoNota.' - '.$motivoN.'</td>
                    </tr>
                    <tr>
                        <td style="width:4.1cm;"></td>
                        <td style="text-align:left; width:15.5cm"><b>DOCUMENTO RELACIONADO:</b> '.$tdocumento.' '.$referencia.'</td>
                    </tr>
                    <tr>
                        <td style="width:4.1cm;"></td>
                        <td style="text-align:left; width:15.5cm"><b>OBSERVACIÓN:</b> '.$observacion.'</td>
                    </tr>
                    <tr>
                        <td style="width:4.1cm;"></td>
                        <td >&nbsp;<br>Representación impresa de '.$tipoDocumentoF.'</td>
                    </tr>
                </table>
        ';
        // CODIGO QR INTERNO GENERADO POR EL SISTEMA
            $style = array(
                'border' => 2,
                'position' => 'L',
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(40,40,40),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );

            $dirUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

            $codeQR = $this->pdf->write2DBarcode($dirUrl, "QRCODE,L", '', '', 40, 40, $style, "");

        $this->pdf->writeHTML($footerHTML,true,false,true,false);

        if ($estado == 0){
            $this->pdf->Image(base_url().'images/cabeceras/anulado.png', 40, 25, 140, 140, '', '', '', false, 300);
        }

        $this->pdf->Output('comprobante.pdf', 'I');

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);

        //**************************************************************************
    }

    /* Auxiliares */
    public function obtener_tipo_documento($tipo){
        $tiponom = 'factura';
        switch ($tipo) {
            case 'F':
                $tiponom = 'Nota Credito';
                break;
            case 'B':
                $tiponom = 'boleta';
                break;
            case 'N':
                $tiponom = 'comprobante';
                break;
            case 'C':
                $tiponom = 'credito';
                break;
            case 'D':
                $tiponom = 'debito';
                break;
        }
        return $tiponom;
    }

    public function obtener_serie_numero($tipo_docu){
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
            case 'C':
                $codtipodocu = '11';
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
        $this->layout->view('ventas/comprobante_reporte', $data);
    }

    public function estadisticas()
    {
        /* Imagen 1 */
        $listado = $this->comprobante_model->reporte_ocompra_5_clie_mas_importantes();

        if (count($listado) == 0) { // Esto significa que no hay ordenes de compra por tando no muestros ningun reporte
            echo '<h3>Ha ocurrido un problema</h3>
                      <span style="color:#ff0000">No se ha encontrado Órdenes de Venta</span>';
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
        echo '<h3>1. Los 5 clientes más importantes</h3>
               Según el monto (S/.) histórico órdenes de venta<br />
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
        echo '<h3>2. Montos (S/.) de órdenes de venta según mes</h3>
               Considerando el presente año<br />
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
        echo '<h3>3. Cantidades de órdenes de venta según mes</h3>
               Considerando el presente año<br />
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
               Considerando las ventas en el presente año<br />
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

    public function ver_reporte_pdf($params)
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


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function ver_reporte_pdf_ventas($anio)
    {
        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
        $delta = 20;

        $listado = $this->comprobante_model->buscar_comprobante_venta_2($anio);

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

    public function ver_reporte_pdf_commpras($anio)
    {
        $usuario = $this->usuario_model->obtener($this->usuario);
        $persona = $this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy = date('d/m/Y');
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Cabecera */
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

    public function estadisticas_compras_ventas($tipo, $anio)
    {
        $usuario = $this->usuario_model->obtener($this->usuario);
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

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes)
    {
        $usuario = $this->usuario_model->obtener($this->usuario);
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


            if ($valor->CREDET_Pu_ConIgv != '')
                $pu_conigv = $valor->CREDET_Pu_ConIgv;
            else
                $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;
            $db_data[] = array(
                'item_numero' => $indice + 1,
                'item_cantidad' => $valor->CREDET_Cantidad,
                'item_unidad' => $valor->UNDMED_Simbolo,
                'item_codigo' => $valor->PROD_CodigoUsuario,
                'item_descripcion' => utf8_decode_seguro($valor->PROD_Nombre, true),
                'item_precio_unitario' => number_format($pu_conigv, 2),
                'item_importe' => number_format($valor->CREDET_Total, 2)
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

}

?>