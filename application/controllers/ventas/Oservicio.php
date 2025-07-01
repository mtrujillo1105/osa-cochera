<?php
include("system/application/libraries/pchart/pData.php");
include("system/application/libraries/pchart/pChart.php");
include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");

class Oservicio extends CI_Controller
{

    public function __construct()
    {
        parent::Controller();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('utf');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->helper('util');
        $this->load->helper('utf_helper');

        $this->load->model('compras/ocompra_model');
        $this->load->model('ventas/oservicio_model');
        $this->load->model('ventas/oserviciodetalle_model');
        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/emprestablecimiento_model');
        $this->load->model('empresa/emprcontacto_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/tipoestablecimiento_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/proyecto_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('almacen/productoproveedor_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/serie_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('seguridad/permiso_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/area_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/presupuesto_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('configuracion_model');
        $this->load->model('maestros/configuracion_model');

        date_default_timezone_set('America/Los_Angeles');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
    }

    public function index()
    {
        
        $this->layout->view('seguridad/inicio');
    }

    public function oservicios($j = '0', $tipo_oper = 'C', $eval = '0')
    {
        $data['compania'] = $this->somevar['compania'];
        $this->load->helper('my_guiarem');
        
        $evalua = true;
        if ($eval == '1' && count($this->permiso_model->busca_permiso($this->somevar['rol'], 38)) > 0) {
            $evalua = false;
        }
        $data['registros'] = $this->oservicio_model->total_oservicio($tipo_oper);
        $conf['base_url'] = site_url('ventas/oservicio/oservicios/0/' . $tipo_oper . '/0/');
        $conf['total_rows'] = $data['registros'];
        $conf['per_page'] = 50;
        $conf['num_links'] = 3;
        $conf['uri_segment'] = 7;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $offset = (int)$this->uri->segment(7);
        $listado_oservicios = $this->oservicio_model->listar($tipo_oper, $conf['per_page'], $offset);
        $item = $j + 1;
        $lista = array();
        if (count($listado_oservicios) > 0) {
            foreach ($listado_oservicios as $indice => $valor) {
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
                $estado = $valor->OCOMC_FlagEstado;
                $img_estado = ($estado == '1' ? "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />" : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");

                $detalle = $this->oservicio_model->obtener_detalle_oservicio($codigo);
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
                }
                $img = "<a href='javascript:;' title='" . $title . "' class='tooltip'><img src='" . base_url() . "" . $url_img . "' /></a>";
                $estado = $img;

                //$estado = $img_estado;
                // }
                if ($eval == '0') {
                    $estado = $img_estado;
                }

                $contents = "<img height='16' width='16' src='" . base_url() . "images/icono-factura.gif' title='Factura' border='0'>";
                $attribs = array('width' => 400, 'height' => 150, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
                $ver3 = anchor_popup('compras/oservicio/ventana_oservicio_factura/' . $codigo, $contents, $attribs);
                if ($evalua) {
                    $editar = "<a href='javascript:;' onclick='editar_oservicio(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                } else {
                    $editar = "<a href='javascript:;' onclick='ver_detalle_oservicio(" . $codigo . ")'><img src='" . base_url() . "images/ver_detalle.png' width='16' height='16' border='0' title='Ver Detalle'></a>";
                }
                $ver = "<a href='javascript:;' onclick='oservicio_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2 = "<a href='javascript:;' onclick='oservicio_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar = "<a href='javascript:;' onclick='eliminar_oservicio(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[] = array($check, $item++, $fecha, $numero, $cotizacion, $pedido, $nombre_proveedor, $msguiain, $monto_total, $msgaprob, $estado, $ver3, $editar, $ver, $ver2);
            }
        }
        $data['fechai'] = form_input(array("name" => "fechai", "id" => "fechai", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $data['fechaf'] = form_input(array("name" => "fechaf", "id" => "fechaf", "class" => "cajaGeneral cajaSoloLectura", "readonly" => "readonly", "size" => 10, "maxlength" => "10", "value" => ""));
        $atributos = array('width' => 600, 'height' => 400, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos);
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
        $data['evalua'] = $evalua;
        $data['titulo_tabla'] = "RELACI&Oacute;N de ORDENES DE Servicios";
        $data['titulo_busqueda'] = "BUSCAR ORDEN DE SERVICIOS";
        $data['lista'] = $lista;
        $data['tipo_oper'] = $tipo_oper;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_oper' => $tipo_oper));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('ventas/oservicio_index', $data);
    }

    public function nueva_oservicio($tipo_oper = 'C')
    {
        $compania = $this->somevar['compania'];
        $data['compania'] = $this->somevar['compania'];
        
        unset($_SESSION['serie']);

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $modo = "";
        $data['modo'] = $modo;
        $accion = "";
        $modo = "insertar";
        $codigo = "";
        $usuario = $this->somevar['user'];
        $datos_usuario = $this->usuario_model->obtener($usuario);
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $data['nombre_usuario'] = $datos_usuario->PERSC_Nombre . " " . $datos_usuario->PERSC_ApellidoPaterno;
        $oculto = form_hidden(array('accion' => $accion, 'codigo' => $codigo, 'empresa' => '', 'persona' => '', 'modo' => $modo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
        $data['url_action'] = base_url() . "index.php/ventas/oservicio/insertar_oservicio";
        $data['titulo'] = "REGISTRAR ORDEN DE SERVICIO";
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
        //$this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1')
        //$data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra' ' ");
        $data['cboObra'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');
        $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, $almacen_dafault, " class='comboMedio' id='almacen'");
        $data['cboFormapago'] = form_dropdown("formapago", $this->formapago_model->seleccionar(), "1", " class='comboMedio' id='formapago'");
        $data['cboContacto'] = $this->OPTION_generador(array(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'));
        $data['cboMiContacto'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), ($tipo_oper == 'V' ? '4' : '5')), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
       
        $data['detalle_oservicio'] = array();
        $data['numero'] = "";
        $data['codigo_usuario'] = "";
        $data['serie'] = "";
        $data['cliente'] = "";
        $data['ruc_cliente'] = "";
        $data['nombre_cliente'] = "";
        $data['proveedor'] = "";
        $data['nombre_proveedor'] = "";
        $data['ruc_proveedor'] = "";
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
        $data['focus'] = "";
        $data['pedido'] = "0";
        $data['hoy'] = mdate("%d/%m/%Y ", time());
        $data['fechaentrega'] = "";
        $data['contacto'] = "";
        $data['tiempo_entrega'] = "";
        $data['codigo'] = "";
        $data['estado'] = "1";
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

        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento_os($compania);
        $cofiguracion_datos[0]->CONFIC_Serie;
        $cofiguracion_datos[0]->CONFIC_Numero;

        $data['serie'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['serie_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger_oc'] = $cofiguracion_datos[0]->CONFIC_Numero + 1;

        $data["terminado"] = 0;

        $this->layout->view('ventas/oservicio_nueva', $data);

    }

    public function insertar_oservicio()
    {
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
        $filter->OCOMP_CodigoVenta = $this->input->post('ordenventa');
        $filter->PROYP_Codigo = $this->input->post('obra');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->OCOMC_descuento100 = $this->input->post('descuento');
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0')
            $filter->OCOMC_Personal = $this->input->post('contacto');

        if ($this->input->post('mi_contacto') != '' && $this->input->post('mi_contacto') != '0')
            $filter->OCOMC_MiPersonal = $this->input->post('mi_contacto');

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

        #datos correlativo
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $id_documento = 17);
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;

        $filter->OCOMC_Numero = $numero;

        $filter->OCOMC_FlagBS = 'S';

        $ocompra = $this->oservicio_model->insertar_oservicio($filter);
        $this->configuracion_model->modificar_configuracion($this->somevar['compania'], $id_documento, $numero);
        
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
        //$oventas = $this->input->post('oventacod');oventacod
        $ordencompra = $this->input->post('ordencompra');

        $oventacoddetalle = $this->input->post('oventacod');
        $observacionesdetalle = $this->input->post('prodobservacion');

        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $accion = $detaccion[$indice];
                if($accion!="e" && $accion!="EE"){
                    $producto = $prodcodigo[$indice];
                    $filter = new stdClass();
                    $filter->OCOMP_Codigo = $ocompra;
                    $filter->PROD_Codigo = $prodcodigo[$indice];
                    $filter->OCOMDEC_Cantidad = $prodcantidad[$indice];
                    $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                    $filter->OCOMDEC_Pendiente_pago = $filter->OCOMDEC_Cantidad;
                    $filter->UNDMED_Codigo = $produnidad[$indice] == 0 ? NULL : $produnidad[$indice];
                    
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

                    //incluimos el id de OV para poder identificar a que cliente y proyecto le corresponde
                    $filter->OCOMP_Codigo_venta = $oventacoddetalle[$indice];
                    $this->oserviciodetalle_model->insertar($filter);
                     
                     $codoventa= $oventacoddetalle[$indice];
                     if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != ''  ){
                    //$this->ocompra_model->modificar_pendienteoventa($codoventa,$filter->PROD_Codigo,$filter->OCOMDEC_Cantidad);
                            $this->ocompra_model->modificar_pendiente_cantidad_id_detalle_venta($codoventa, -$filter->OCOMDEC_Cantidad);
                        } 

                }
            }
        }

        /*if(is_array($oventacoddetalle) || is_object($oventacoddetalle)) {
            foreach ($oventacoddetalle as $codoventa) {
                
                if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != '' ){
                                $detalle_ocompra=$this->ocompra_model->esta_importado($codoventa);
                               $cantidadp=$detalle_ocompra->OCOMDEC_Pendiente;
                                    if ($detalle_ocompra) {
                                         $this->ocompra_model->modificar_flagTerminado_oventa($codoventa, "1");
                                    }
                                
                            }
            }
            
        }*/
            
            

        exit('{"result":"ok", "codigo":"' . $ocompra . '"}');
    }

    public function editar_oservicio($codigo, $tipo_oper = 'C')
    {
        $data['compania'] = $this->somevar['compania'];
        
        unset($_SESSION['serie']);
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $data['modo'] = $modo;
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $presupuesto = $datos_oservicio[0]->PRESUP_Codigo;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $codigo_usuario = $datos_oservicio[0]->OCOMC_CodigoUsuario;
        $serie = $datos_oservicio[0]->OCOMC_Serie;

        /**ponemos en en estado seleccionado presupuesto**/
        if($presupuesto!=null && trim($presupuesto)!="" &&  $presupuesto!=0){
        	$estadoSeleccion=1;
        	$codigoPresupuesto=$presupuesto;
        	/**1:sdeleccionado,0:deseleccionado**/
        	$this->presupuesto_model->modificarTipoSeleccion($codigoPresupuesto,$estadoSeleccion);
        }
        /**fin de poner**/
        
        $tiempo_entrega = $datos_oservicio[0]->OCOMC_Entrega;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $mi_contacto = $datos_oservicio[0]->OCOMC_MiPersonal;
        $contacto = $datos_oservicio[0]->OCOMC_Personal;
        $envio_direccion = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_oservicio[0]->OCOMC_FactDireccion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $fecha = substr($datos_oservicio[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_oservicio[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $usuario = $datos_oservicio[0]->USUA_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;
        $estado = $datos_oservicio[0]->OCOMC_FlagEstado;

        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $percepciontotal = $datos_oservicio[0]->OCOMC_percepcion;
        $total = $datos_oservicio[0]->OCOMC_total;

        //$codigocliente = $datos_oservicio[0]->CLIP_Codigo;
        $codigoproyecto = $datos_oservicio[0]->PROYP_Codigo;
        
        if($codigoproyecto != 0){
            $listaproyecto = $this->proyecto_model->seleccionar($codigoproyecto);
            $data['cboObra'] = form_dropdown("obra",$listaproyecto,$codigoproyecto, " class='comboGrande'  id='obra' ");
        }else{
            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");
        }
            


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
        $data['mi_contacto'] = $mi_contacto;
        $data['contacto'] = $contacto;

        $data['cboObra'] = $this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');

        $data['cboMiContacto'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), ($tipo_oper == 'V' ? '4' : '5')), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $mi_contacto, array('', '::Seleccione::'), ' ');
        $data['cboContacto'] = $this->OPTION_generador($this->directivo_model->listar_directivo($empresa, ($tipo_oper == 'V' ? '5' : '4')), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), $contacto, array('', '::Seleccione::'), ' ');
        //$data['cboPedidos'] = form_dropdown("pedidos", $this->pedido_model->seleccionar_finalizados(), "", " onchange='load_cotizaciones();' class='comboGrande' id='pedidos'");
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
        $data['titulo'] = "EDITAR ORDEN DE SERVICIO";
        $data['formulario'] = "frmOrdenCompra";
        $data['oculto'] = $oculto;
        $data['url_action'] = base_url() . "index.php/compras/oservicio/modificar_oservicio";
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
        $data['tiempo_entrega'] = $tiempo_entrega;
        $data['envio_direccion'] = $envio_direccion;
        $data['fact_direccion'] = $fact_direccion;

        $ordencompraventa = $datos_oservicio[0]->OCOMP_CodigoVenta;
        $data['ordencompraventa'] = $ordencompraventa;
        $data["ordenventa"] = $datos_oservicio[0]->OCOMP_CodigoVenta;

        #datos asociados a la orden de venta
        $this->load->model("compras/ocompra_model");
        $oventa_datos = $this->ocompra_model->get_by_id($ordencompraventa);
        $data["OCOMC_Serie"] = $oventa_datos->OCOMC_Serie;
        $data["OCOMC_Numero"] = $oventa_datos->OCOMC_Numero;

        $tdc = $datos_oservicio[0]->OCOMP_TDC;

        $data['tdcDolar'] = $moneda == 4 ? $datos_oservicio[0]->OCOMP_TDC_opcional : $tdc;
        $data['tdcEuro'] = $moneda == 4 ? $tdc : '';

        $detalle = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $detalle_oservicio = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detoservicio = $valor->OCOMDEP_Codigo;
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
                $GenInd = $valor->OCOMDEC_GenInd;
                $costo = $valor->OCOMDEC_Costo;
                $nombre_producto = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->OCOMDEC_Cantidad = $cantidad;
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

                $objeto->OCOMDEC_Observacion = $observ;
                $objeto->OCOMP_Codigo_venta = $valor->OCOMP_Codigo_venta;
                $objeto->OCOMDEC_Pendiente = $valor->OCOMDEC_Pendiente;

                $objeto->PROYP_Codigo = 0;
                $objeto->RazonSocial = "";
                $objeto->OCOMDEC_Pendiente_pago = $valor->OCOMDEC_Pendiente_pago;

                $detalle_oservicio[] = $objeto;
            }
        }
        $data['detalle_oservicio'] = $detalle_oservicio;

        $data['terminado'] = $datos_oservicio[0]->OCOMC_FlagTerminado;
        $data['terminado_importacion'] = $datos_oservicio[0]->OCOMC_FlagTerminadoProceso;

        $this->layout->view('ventas/oservicio_nueva', $data);
    }

    public function ver_detalle_oservicio($codigo, $tipo_oper = 'C')
    {
        $this->load->helper('my_guiarem');
        
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $presupuesto = $datos_oservicio[0]->PRESUP_Codigo;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $codigo_usuario = $datos_oservicio[0]->OCOMC_CodigoUsuario;
        $serie = $datos_oservicio[0]->OCOMC_Serie;

        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_MiPersonal);
        $mi_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
        $contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $envio_direccion = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_oservicio[0]->OCOMC_FactDireccion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $fecha = substr($datos_oservicio[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_oservicio[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $usuario = $datos_oservicio[0]->USUA_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;
        $estado = $datos_oservicio[0]->OCOMC_FlagEstado;

        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $percepciontotal = $datos_oservicio[0]->OCOMC_percepcion;
        $total = $datos_oservicio[0]->OCOMC_total;

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
        $data['url_action'] = base_url() . "index.php/compras/oservicio/modificar_oservicio";
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

        $detalle = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $detalle_oservicio = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detoservicio = $valor->OCOMDEP_Codigo;
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
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
				$cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;
				
				$cantidad_presente = $this->serie_model->cantidad_series_presente_x_oservicio($codigo, $producto);

                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
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

                $detalle_oservicio[] = $objeto;
            }
        }
        $data['detalle_oservicio'] = $detalle_oservicio;
        $this->layout->view('ventas/oservicio_detalle', $data);
    }

    public function ver_oservicio($codigo, $tipo_oper = 'C')
    {
        $this->load->helper('my_guiarem');
        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $presupuesto = $datos_oservicio[0]->PRESUP_Codigo;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $codigo_usuario = $datos_oservicio[0]->OCOMC_CodigoUsuario;
        $serie = $datos_oservicio[0]->OCOMC_Serie;

        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_MiPersonal);
        $mi_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
        $contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $envio_direccion = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_oservicio[0]->OCOMC_FactDireccion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $fecha = substr($datos_oservicio[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_oservicio[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $usuario = $datos_oservicio[0]->USUA_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;
        $estado = $datos_oservicio[0]->OCOMC_FlagEstado;

        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $percepciontotal = $datos_oservicio[0]->OCOMC_percepcion;
        $total = $datos_oservicio[0]->OCOMC_total;

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
        $data['url_action'] = base_url() . "index.php/compras/oservicio/modificar_oservicio";
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

        $detalle = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $detalle_oservicio = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detoservicio = $valor->OCOMDEP_Codigo;
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
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';

                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
                $cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;

                $cantidad_presente = $this->serie_model->cantidad_series_presente_x_oservicio($codigo, $producto);

                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
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

                $detalle_oservicio[] = $objeto;
            }
        }
        $data['detalle_oservicio'] = $detalle_oservicio;
        $this->load->view('ventas/oservicio_ver', $data);
    }

    /*public function modificar_oservicio()
    {
        $tipo_oper = $this->input->post('tipo_oper');
        $codigo = $this->input->post('codigo');

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
        $lista_guiarem = $this->guiarem_model->listar_ocompra($codigo, $tipo_oper);
        if ($this->input->post('estado') == '0' && count($lista_guiarem) > 0)
            exit('{"result":"error", "msj":"Esta orden de ' . ($tipo_oper == 'V' ? 'venta' : 'compra') . ' no se puede anular debido a que esta enlazada a una guia de remision"}');
       
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
        $filter->OCOMC_MiPersonal = NULL;
        if ($this->input->post('mi_contacto') != '' && $this->input->post('mi_contacto') != '0')
            $filter->OCOMC_MiPersonal = $this->input->post('mi_contacto');
        
        $filter->OCOMC_Personal = NULL;
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0')
            $filter->OCOMC_Personal = $this->input->post('contacto');
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

        $filter->OCOMP_TDC = $this->input->post('moneda') == 4 ? $this->input->post('tdcEuro') : $this->input->post('tdcDolar');
        $filter->OCOMP_TDC_opcional = $this->input->post('moneda') == 4 ? $this->input->post('tdcDolar') : 0;
        
        $this->oservicio_model->modificar_oservicio($codigo, $filter);

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

        $observacionesdetalle = $this->input->post("prodobservacion");

        if (is_array($detacodi)) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];
                $filter = new stdClass();
                $filter->OCOMP_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                $filter->OCOMDEC_Cantidad = $prodcantidad[$indice];
                if ($flagBS == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                
                $filter->OCOMDEC_Descuento100 = $proddescuento100[$indice];
                $filter->OCOMDEC_Igv100 = $prodigv100[$indice];
                $filter->OCOMDEC_Pu = $prodpu[$indice];
                $filter->OCOMDEC_Subtotal = $prodprecio[$indice];
                $filter->OCOMDEC_Descuento = $proddescuento[$indice];
                $filter->OCOMDEC_Descuento2 = 0;
                $filter->OCOMDEC_Igv = $prodigv[$indice];
                $filter->OCOMDEC_Total = $prodimporte[$indice];
                $filter->OCOMDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->OCOMDEC_Descripcion = strtoupper($proddescri[$indice]);

                $filter->OCOMDEC_Observacion = $observacionesdetalle[$indice];

                if ($detalle_accion == 'n') {
                    $this->oserviciodetalle_model->insertar($filter);
                } elseif ($detalle_accion == 'm') {
                    $this->oserviciodetalle_model->modificar($valor, $filter);
                } elseif ($detalle_accion == 'e') {
                    $this->oserviciodetalle_model->eliminar($valor);
                }
            }
        }
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }*/

    public function modificar_oservicio()
    {
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
        $filter->OCOMC_MiPersonal = NULL;
        if ($this->input->post('mi_contacto') != '' && $this->input->post('mi_contacto') != '0')
            $filter->OCOMC_MiPersonal = $this->input->post('mi_contacto');
        
        $filter->OCOMC_Personal = NULL;
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0')
            $filter->OCOMC_Personal = $this->input->post('contacto');
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

        $observacionesdetalle = $this->input->post("prodobservacion");
        $fleteDetalle = $this->input->post("flete");
//var_dump($oventas);
        //$ordencompra= $this->input->post("ordencompraventa");

        if (is_array($detacodi)) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];
                $filter = new stdClass();
                $filter->OCOMP_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                $filter->OCOMDEC_Cantidad = $prodcantidad[$indice];
              //  $pend=$pendiente[$indice];
               // $cantreal= $cantreal[$indice];
                if($tipo_oper == 'V'){
                    /*if($cantreal[$indice] == $prodcantidad[$indice] && $prodcantidad[$indice] == $pendiente[$indice]){
                        $filter->OCOMDEC_Pendiente = $pendiente[$indice];
                    }else{
                        $cantmed = $prodcantidad[$indice]-$cantreal[$indice];
                        $cantnew =$cantmed+$pendiente[$indice];
                        $filter->OCOMDEC_Pendiente = $cantnew;
                    }*/

                    /*if($cantreal[$indice] != $prodcantidad[$indice] && $prodcantidad[$indice] != $pendiente[$indice]){
                        $cantmed = $prodcantidad[$indice]-$cantreal[$indice];
                        $cantnew =$cantmed+$pendiente[$indice];
                        $filter->OCOMDEC_Pendiente = $cantnew;
                    }else{
                         $filter->OCOMDEC_Pendiente = $pendiente[$indice];  
                    }*/
                }else{
                    //$filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                }
                      

                if ($flagBS == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                
                $filter->OCOMDEC_Descuento100 = $proddescuento100[$indice];
                $filter->OCOMDEC_Igv100 = $prodigv100[$indice];
                $filter->OCOMDEC_Pu = $prodpu[$indice];
                $filter->OCOMDEC_Subtotal = $prodprecio[$indice];
                $filter->OCOMDEC_Descuento = $proddescuento[$indice];
                $filter->OCOMDEC_Descuento2 = 0;
                $filter->OCOMDEC_Igv = $prodigv[$indice];
                $filter->OCOMDEC_Total = $prodimporte[$indice];
                $filter->OCOMDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->OCOMDEC_Descripcion = strtoupper($proddescri[$indice]);
                //$filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];

                $filter->OCOMDEC_Observacion = $observacionesdetalle[$indice];
                $filter->OCOMDEC_flete = isset($fleteDetalle[$indice]) ? $fleteDetalle[$indice] : 0;

                $filter->OCOMP_Codigo_venta = $oventas[$indice];

                //$pendientef = $prodcantidad[$indice] - $cantreal[$indice];
                $pendientef= $pendiente[$indice] - $prodcantidad[$indice];
                $codovente = $oventas[$indice];
             

                if ($detalle_accion == 'n') {
                    $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                    $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];

                    $this->oserviciodetalle_model->insertar($filter);

                    if($tipo_oper == 'C'  && !is_null($codovente) && $codovente != ''  ){
                    //$this->ocompra_model->modificar_pendienteoventa($codovente,$filter->PROD_Codigo,$filter->OCOMDEC_Cantidad); 
                        $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, $filter->OCOMDEC_Cantidad);
                        }

                } elseif ($detalle_accion == 'm') {
                        //canr real(v)==cantidad(c)  y cantiudad(c)==pendien(c)
                   if($tipo_oper == 'V'){
                        if($cantreal[$indice] == $prodcantidad[$indice] && $prodcantidad[$indice] == $pendiente[$indice]){
                            $filter->OCOMDEC_Pendiente = $pendiente[$indice];
                            $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];
                        }/*elseif($prodcantidad[$indice] == $pendiente[$indice]){

                            $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                            $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];
                        }*/
                        else{
                            //cantmedia==cantidad(c)-cantidadreal(v)
                            $cantmed = $prodcantidad[$indice]-$cantreal[$indice];
                            $cantnew =$cantmed+$pendiente[$indice];
                            $filter->OCOMDEC_Pendiente = $cantnew;
                            if(isset($filter->OCOMDEC_Pendiente_pago)) {
                                $filter->OCOMDEC_Pendiente_pago += $cantmed;
                            }else {
                                $this->ocompra_model->modificar_pendiente_pago_by_id_detalle_venta($valor, - $cantmed);
                            }
                        }

                        #modificamos la cantidad real de todos aquellos oc que contengan el item
                    }else{
                        $filter->OCOMDEC_Pendiente = $prodcantidad[$indice];
                        $filter->OCOMDEC_Pendiente_pago = $prodcantidad[$indice];
                    }
                    $this->oserviciodetalle_model->modificar($valor, $filter);

                    if($tipo_oper == 'C'  && !is_null($codovente) && $codovente != ''){
                        //$this->ocompra_model->modificar_pendiente_cantidad($codovente,$filter->PROD_Codigo,$pendientef);
                        $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, - $pendientef);
                    }

                } elseif ($detalle_accion == 'e') {
                    $this->oserviciodetalle_model->eliminar($valor);
                    if($tipo_oper == 'C'  && !is_null($codovente) && $codovente != ''  ){
                    //$this->ocompra_model->modificar_pendienteoventa($codovente,$filter->PROD_Codigo,-($filter->OCOMDEC_Cantidad)); 
                        $this->ocompra_model->modificar_pendiente_by_id_detalle_venta($codovente, -($filter->OCOMDEC_Cantidad));
                        }
                }
            }
        }
        /* foreach ($oventas as  $codoventa) {
                
                if($tipo_oper == 'C'  && !is_null($codoventa) && $codoventa != '' ){
                                $detalle_ocompra=$this->ocompra_model->esta_importado($codoventa);
                               $cantidadp=$detalle_ocompra->OCOMDEC_Pendiente;
                                    if ($detalle_ocompra) {
                                         $this->ocompra_model->modificar_flagTerminado_oventa($codoventa, "1");
                                    }
                                
                            }
            }*/
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }

    public function eliminar_oservicio()
    {
        $codigo = $this->input->post('codigo');
        $this->oservicio_model->eliminar($codigo);
    }

    public function evaluar_oservicio()
    {
        $flag = $this->input->post('flag');

        $checkO = $this->input->post('checkO');
        if (is_array($checkO))
            $this->oservicio_model->evaluar_oservicio($flag, $checkO);

        if (count($this->permiso_model->busca_permiso($this->somevar['rol'], 38)) > 0)
            $this->oservicios(0, 1);
        else
            $this->index();
    }

    public function oservicio_ver_pdf($codigo)
    {
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                $this->oservicio_ver_pdf_formato1($codigo);
                break;
            case 2:  //Formato para jimmyplat
                $this->oservicio_ver_pdf_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->oservicio_ver_pdf_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->oservicio_ver_pdf_formato4($codigo);
                break;
            default:
                $this->oservicio_ver_pdf_formato1($codigo);
                break;
        }
    }

    public function oservicio_ver_pdf_conmenbrete($codigo, $flagPdf = 0)
    {
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                $this->oservicio_ver_pdf_conmenbrete_formato1($codigo, $flagPdf);
                break;
            case 2:  //Formato para jimmyplat
                $this->oservicio_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->oservicio_ver_pdf_conmenbrete_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->oservicio_ver_pdf_conmenbrete_formato4($codigo);
                break;
            default:
                $this->oservicio_ver_pdf_conmenbrete_formato1($codigo, $flagPdf);
                break;
        }
    }

    public function oservicio_ver_pdf_formato1($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;

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

        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_MiPersonal);
        $mi_nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $mi_nombre_area = '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_formato2($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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
        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_formato3($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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

        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_formato4($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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
        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_conmenbrete_formato1($codigo, $flagPdf)
    {

        ////aparte
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;

        $tiempo_entrega = $datos_oservicio[0]->OCOMC_Entrega;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;

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

        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_MiPersonal);
        $mi_nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $mi_nombre_area = '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
        $nombre_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $nombre_area = '';
        $datos_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);
        //aumentado stv
        $compa = $this->somevar['compania'];
        //

        /* Cabecera */
        $delta = 20;

        if ($tipo_oper == 'C') {
            $this->cezpdf = new Cezpdf('a4');
            if ($flagPdf == 1) {
                if ($compa == 1) {
                    $this->cezpdf->ezImage("images/ferremax_cabe_jmb.jpg", 0, 536, 'none', 'left');
                } else {
                    $this->cezpdf->ezImage("images/ferremax_cabe.jpg", 0, 536, 'none', 'left');
                }
            } else {
                $this->cezpdf->ezImage("images/img_db/logo_instrume_unido1.jpg", 1, 1, 'none', 'left');
            }
            if ($compa == 1) {
                 $notimg = "oservicio.jpg";
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' .$notimg));
               // $this->cezpdf->ezText(utf8_decode_seguro($datos_empresa[0]->EMPRC_RazonSocial), 11, array("left" => 15));
              //  $this->cezpdf->ezText(utf8_decode_seguro('Principal: Av. El Polo Mz.H Lt.12 C'), 9, array("left" => 15));
               // $this->cezpdf->ezText(utf8_decode_seguro('Urb.El Club, 1era Etapa'), 9, array("left" => 15));
               // $this->cezpdf->ezText(utf8_decode_seguro('Huachipa, Lurigancho, Lima - Peru'), 9, array("left" => 15));
//            $this->cezpdf->ezText('E-mail: madypla@hotmail.com,  web: www.madyplac.com', 9, array("left" => 15));    

            } else {
//            $this->cezpdf->ezText(utf8_decode_seguro($datos_empresa[0]->EMPRC_RazonSocial), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Urb. Los Portales de Javier Prado Mz N lt9'), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Ate - Lima 3, Peru'), 8, array("left" => 15));
//            $this->cezpdf->ezText('web: www.ferremax.com.pe', 8, array("left" => 15));            
            }

            //tab
//            $this->cezpdf->ezText(utf8_decode_seguro($datos_empresa[0]->EMPRC_RazonSocial), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Principal: Urb. Los Portales de Javier Prado Mz N lt9'), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Ate - Lima 3, Peru'), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Av. Garcilazo de la Vega Nro. 1348 int. 1041 (C. C. Cyberplaza) Lima, '), 8, array("left" => 15));
//            $this->cezpdf->ezText(utf8_decode_seguro('Av. Bolivia 148 C. Comercial Centro Lima interior Tda 501 '), 8, array("left" => 15));
//            $this->cezpdf->ezText('E-mail: compuventas_pc@hotmail.com,  web: www.ferremax.com.pe', 8, array("left" => 15));            
            //

        } else if ($tipo_oper == 'V') {
            $this->cezpdf = new Cezpdf('a4'); /// asi taba   , 'landscape'
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/oservicio.jpg')); 
        }
        $this->cezpdf->ezText('O' . ($tipo_oper == 'C' ? 'C-' : 'S-') . str_pad($numero, 4, '0', STR_PAD_LEFT) . '-'. strftime("%Y", strtotime($fecha)), 10, array("leading" => 110, 'left' => 402));
        $this->cezpdf->ezText('<b>Fecha:              ' . mysql_to_human($fecha) . '</b>', 9, array("leading" => 20 - $delta, 'left' => 5));
        //$this->cezpdf->ezText($posicionX + 486, $posicionY + 755, 20, '<b>R.U.C:    ' );
        $this->cezpdf->ezText('<b>R.U.C:    ' . ($ruc) . '</b>', 9, array("leading" => -50 , 'left' => 390));
        $this->cezpdf->ezText('', '', array("leading" => 60));
       $this->cezpdf->ezText('1. DATOS' . ($tipo_oper == 'C' ? ' DEL PROVEEDOR : ' : ' DEL CLIENTE :' ), 8, array("left" => 2));
        $this->cezpdf->ezText(utf8_decode_seguro('2. CONDICIONES GENERALES'), 8, array("leading" => 0,"left" => 355));
   // $this->cezpdf->ezText('1. DATOS DEL PROVEEDOR', 9, array("leading" => 10'left' => 390));
    //$this->cezpdf->ezText(utf8_decode_seguro('Urb.El Club, 1era Etapa'), 9, array("left" => 15));
   // $this->cezpdf->ezText('<b>R.U.C:    ', array("leading" => -50 , 'left' => 390));
        $data_cabecera = array(
            array('c1' => utf8_decode_seguro('PROVEEDOR').'        :  '.($nombres)),
            array('c1' => 'RUC'.'                       :           '.($ruc)),
            array('c1' => utf8_decode_seguro('DIRECCION:').'          :  '. utf8_decode_seguro($lugar_entrega))
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
           array('c1' => utf8_decode_seguro('CONTACTO').'           :  '.($mi_nombre_contacto)),
            array('c1' => 'TELEFONO'.'            :           '.($telefono).'        E-mail:'),
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
            array('c1' => utf8_decode_seguro('MONEDA : ').'     '.($moneda_nombre)),
            array('c1' =>  '        '),
            array('c1' =>  'TC :'),
            
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
        // $this->cezpdf->ezSetY(105 + $delta);
        $positionx2 = 35;
        $positiony2 = 270;
        $db_data = array();
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $positiony2 -= 45;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $descuento =$valor->OCOMDEC_Descuento100;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad = is_null($datos_unidad) ? '' : $datos_unidad[0]->UNDMED_Descripcion;
                $prod_cantidad = $valor->OCOMDEC_Cantidad;
                $db_data[] = array(
                    'col1' => $indice + 1,
                    'col2' => $prod_codigo,
                    'col3' => utf8_decode_seguro($prod_nombre) . "\n\n" . utf8_decode_seguro($valor->OCOMDEC_Observacion),
                    'col4' => $prod_cantidad,
                    'col5' => $prod_unidad,
                    'col6' => number_format($pu, 2),
                    'col7' => number_format($descuento, 2),
                    'col8' => number_format($importe, 2)
                );
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
            'fontSize' => 8,
            'cols' => array(
                'col1' => array('width' => 25, 'justification' => 'center'),
                'col2' => array('width' => 60, 'justification' => 'center'),
                'col3' => array('width' => 220),
                'col4' => array('width' => 35, 'justification' => 'left'),
                'col5' => array('width' => 45, 'justification' => 'right'),
                'col6' => array('width' => 40, 'justification' => 'right'),
                'col7' => array('width' => 45, 'justification' => 'right'),
                'col8' => array('width' => 45, 'justification' => 'right')
            )
        ));
         $this->cezpdf->ezText('', '', array("leading" => 10));
        /*         * Sub Totales* */

        ///stv
//        if($subtotal_p!=""){
//        $data_subtotal = array(
//            array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
//            array('cols0' => '', 'cols1' => 'Descuento    ' . $descuento100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($descuentototal, 2)),
//            array('cols0' => '', 'cols1' => 'I.G.V.           ' . $igv100 . '%', 'cols3' => $simbolo_moneda . " " . number_format($igvtotal, 2)),
//            array('cols0' => '', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal_p, 2)),
//            array('cols0' => '', 'cols1' => utf8_decode_seguro('Percepción') . '   ' . $percepcion100 . ' %', 'cols3' => $simbolo_moneda . " " . number_format($percepcion, 2)),
//            array('cols0' => '', 'cols1' => 'Totales', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
//        );
//        }else{
        ///
        $data_subtotal = array(
            array('cols0' => '                      ', 'cols1' => 'Sub-total', 'cols3' => $simbolo_moneda . " " . number_format($subtotal, 2)),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 1,
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
            'showLines' => 1,
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
             array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => 'Total', 'cols3' => $simbolo_moneda . " " . number_format($total, 2))
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 1,
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
        /* Observaciones */
        $this->cezpdf->ezText('', '', array("leading" => 0));
         $data_subtotal = array(
             array('cols0' => 'NOTAS :'),
        );
        $this->cezpdf->ezTable($data_subtotal, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '120',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 140, 'justification' => 'left'),
                
               )
        ));

        $data_footer = array(
            //array('cols0' => '', 'cols1' => ''),
           // array('cols0' => utf8_decode_seguro('<b> ' . ($tipo_oper == 'C' ? 'NOTAS' : 'VENTA') . '</b>'), 'cols1' => ''),
            array('cols0' => '     ', 'cols1' => '1 Lugar de entrega de las facturas y documentacion:'),
            array('cols0' => '     ', 'cols1' => '    Jr Meliton Carbajal 124 Dto.1 Urb Covima La Molina Lima 12' ),
            array('cols0' => '     ', 'cols1' => '2 Horario de recepcion de facturas 9 am a 2 pm' ),
            array('cols0' => '     ', 'cols1' => '3 Al momento de entregar la factura esta se debera entregar junto con la orden de compra' ),
            array('cols0' => '     ', 'cols1' => '4 Nos resevamos el derechp de anular el pedido si el proveedor no cumple con los plazos de entrega' ),
            array('cols0' => '     ', 'cols1' => '5 Comunicar si son de retencion y adjuntar el numero de cuenta del  Banco de la Nacion' ),
        );
        $this->cezpdf->ezTable($data_footer, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 265,
            'fontSize' => 7,
            'cols' => array(
                'cols0' => array('width' => '20', 'justification' => 'left'),
                'cols1' => array('width' => '400', 'justification' => 'left'),
            )
        ));
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);

    }

    public function oservicio_ver_pdf_conmenbrete_formato2($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/jimmyplast_fondo.jpg'));

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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
        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_conmenbrete_formato3($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/instrume_fondo_oservicio.jpg'));

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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

        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function oservicio_ver_pdf_conmenbrete_formato4($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $this->load->model('maestros/almacen_model');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        if ($this->somevar['compania'] == 1)
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_fondo.jpg'));
        else
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_jmb_fondo.jpg'));

        /* Datos principales */
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $tipo_oper = $datos_oservicio[0]->OCOMC_TipoOperacion;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $total = $datos_oservicio[0]->OCOMC_total;
        $percepcion = $datos_oservicio[0]->OCOMC_percepcion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $lugar_entrega = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $lugar_factura = $datos_oservicio[0]->OCOMC_FactDireccion;
        $fecha_entrega = ($datos_oservicio[0]->OCOMC_FechaEntrega != '' ? mysql_to_human($datos_oservicio[0]->OCOMC_FechaEntrega) : '');
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;

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
        $arrfecha = explode(" ", $datos_oservicio[0]->OCOMC_FechaRegistro);
        $fecha = $arrfecha[0];
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;

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

        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
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
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $pu = $valor->OCOMDEC_Pu;
                $importe = $valor->OCOMDEC_Subtotal;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = ($valor->OCOMDEC_Descripcion != '' ? $valor->OCOMDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $prod_nombre = str_replace('\\', '', $prod_nombre);
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
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

    public function buscar($tipo_oper, $eval = '0')
    {
        $data['compania'] = $this->somevar['compania'];
        $evalua = true;
        if ($eval == '1' && count($this->permiso_model->busca_permiso($this->somevar['rol'], 38)) > 0) {
            $evalua = false;
        }
        $filter = new stdClass();
        $fecha_ini = $this->input->post('fechai');
        if (isset($fecha_ini) && $fecha_ini != "") {
            $filter->fechai = date("Y-m-d", strtotime($fecha_ini));
        } else {
            $filter->fechai = "";
        }
        $fecha_fin = $this->input->post('fechaf');
        if (isset($fecha_fin) && $fecha_fin != "") {
            $filter->fechaf = date("Y-m-d", strtotime($fecha_fin));
        } else {
            $filter->fechaf = "";
        }
        $filter->tipo_oper = $tipo_oper;
        $filter->nombre_cliente = $this->input->post('nombre_cliente');
        $filter->ruc_cliente = $this->input->post('ruc_cliente');

        $filter->proveedor = $this->input->post('nombre_proveedor');
        $filter->ruc_proveedor = $this->input->post('ruc_proveedor');

        $filter->producto = $this->input->post('producto');
        $filter->aprobado = $this->input->post('aprobado');
        $filter->ingreso = $this->input->post('ingreso');

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
            $listado_oservicios = $this->oservicio_model->obtenerOrdenServicio($filter);
        } else {
            $listado_oservicios = $this->oservicio_model->obtenerOrdenServicio($filter);
        }
        $data['registros'] = count($listado_oservicios);
        $item = 1;
        $lista = array();
        if (count($listado_oservicios) > 0) {
            foreach ($listado_oservicios as $indice => $valor) {
                $arrfecha = explode(" ", $valor->fecha);
                $fecha = $arrfecha[0];
                $codigo = $valor->OCOMP_Codigo;
                $cotizacion = $valor->cotizacion;
                $pedido = $valor->PEDIP_Codigo;
                $numero = $valor->OCOMC_Numero;
                if ($tipo_oper == 'V') {
                    $cliente = $valor->nombre;
                } else {
                    $proveedor = $valor->nombre;
                }
                $ccosto = $valor->CENCOSP_Codigo;
                $total = $valor->OCOMC_total;
                $flagIngreso = $valor->ingreso;
                $flagAprobado = $valor->aprobado;
                if ($tipo_oper == 'V') {
                    $datos_proveedor = $this->cliente_model->obtener_datosCliente($cliente);
                } else {
                    $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                }

                $datos_cotizacion = $this->cotizacion_model->obtener_cotizacion($cotizacion);
                $nro_pedido = '';
                if ($pedido != '' && $pedido != '0') {
                    $datos_pedido = $this->pedido_model->obtener_pedido($pedido);
                    if (count($datos_pedido) > 0)
                        $nro_pedido = $datos_pedido[0]->PEDIC_Numero;
                }
                /* $empresa = $datos_proveedor[0]->EMPRP_Codigo;
                  $persona = $datos_proveedor[0]->PERSP_Codigo;
                  $tipo = $datos_proveedor[0]->PROVC_TipoPersona; */

                $simbolo_moneda = $valor->MONED_Simbolo;
                $monto_total = $simbolo_moneda . " " . number_format($total, 2);
                $nro_cotizacion = $cotizacion;
                if ($nro_pedido == 0)
                    $nro_pedido = "***";

                if ($tipo_oper == 'V') {
                    $nombre_proveedor = $cliente;
                } else {
                    $nombre_proveedor = $proveedor;
                }


                $msguiain = $flagIngreso;
                $msgaprob = $flagAprobado;
                if ($evalua == true)
                    $check = "<input type='checkbox' name='checkO[" . $item . "]' id='checkO[" . $item . "]' value='" . $codigo . "'>";
                else
                    $check = "";
                $estado = $valor->OCOMC_FlagEstado;
                $img_estado = ($estado == '1' ? "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />" : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
                $contents = "<img height='16' width='16' src='" . base_url() . "images/icono-factura.gif' title='Buscar' border='0'>";
                $attribs = array('width' => 400, 'height' => 150, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
                $ver3 = anchor_popup('compras/oservicio/ventana_oservicio_factura/' . $codigo, $contents, $attribs);
                $editar = "<a href='#' onclick='editar_oservicio(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver = "<a href='#' onclick='oservicio_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver2 = "<a href='#' onclick='oservicio_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar = "<a href='#' onclick='eliminar_oservicio(" . $codigo . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[] = array($check, $item++, $fecha, $numero, $nro_cotizacion, $nro_pedido, $nombre_proveedor, $msguiain, $monto_total, $msgaprob, $img_estado, $ver3, $editar, $ver, $ver2);
            }
        }
        $data['titulo_tabla'] = "RESULTADO DE BÚSQUEDA DE ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['titulo_busqueda'] = "BUSCAR ORDEN DE " . ($tipo_oper == 'V' ? 'VENTA' : 'COMPRA');
        $data['action'] = base_url() . "index.php/compras/oservicio/buscar";
        $data['tipo_oper'] = $tipo_oper;
        $data['lista'] = $lista;
        $data['evalua'] = $evalua;
        $data['paginacion'] = "";
        //$this->output->enable_profiler(TRUE);
        $this->load->view("ventas/buscar_oservicio_index", $data);
    }

    public function ventana_muestra_oservicio($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = '', $almacen = "", $comprobante = '', $ventana = '')
    {
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


        $lista_comprobante = $this->oservicio_model->buscar_oservicio_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_oservicio(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                if ($ventana != 'OC') {
                    //$select = "<a href='" . base_url() . "index.php/compras/oservicio/comprobante_nueva_oservicio/" . $value->OCOMP_Codigo . "/" . $tipo_oper . "' id='linkVerOrdenCompra' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";
                    $select = "<a href='javascript:;' onclick='seleccionar_oservicio(" . $value->OCOMP_Codigo . ",".$value->OCOMC_Serie." ," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";
                } else {
                    $select = "<a href='javascript:;' onclick='seleccionar_oservicio(" . $value->OCOMP_Codigo . ",0," . $value->OCOMC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Recurrente'></a>";

                }
            }
            $lista[] = array(mysql_to_human($value->OCOMC_Fecha), $value->OCOMC_Serie, $value->OCOMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->OCOMC_total), $ver, $select);
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
        if ($ventana != 'OC') {
            $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_comprobante", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('ventas/ventana_muestra_comprobante', $data);
        } else {
            $data['form_open'] = form_open(base_url() . "index.php/compras/ventana_muestra_oservicio", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
            $this->load->view('compras/ventana_muestra_oservicio', $data);
        }
    }

    public function ventana_muestra_oservicioCom($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $tipo_doc = '', $almacen = "", $comprobante = '')
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


        $lista_comprobante = $this->oservicio_model->buscar_oservicio_asoc($tipo_oper, $comprobante, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_oservicio(" . $value->OCOMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";

            if ($formato == 'SELECT_HEADER') {
                //$select = "<a href='" . base_url() . "index.php/compras/oservicio/comprobante_nueva_oservicio/" . $value->OCOMP_Codigo . "/" . $tipo_oper . "' id='linkVerOrdenCompra' ><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar O. compra'></a>";
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

    public function obtener_detalles_oservicios()
    {
        $detalle = array();
        $codigo_moneda = 0;
        foreach ($this->oservicio_model->listarByIds($this->input->post("compras")) as $articulo) {
            $codigo_moneda = $articulo->MONED_Codigo;
            
            if(isset($detalle[$articulo->PROD_Codigo])) {
                $detalle[$articulo->PROD_Codigo]['cantidad'] = $detalle[$articulo->PROD_Codigo]['cantidad'] + $articulo->OCOMDEC_Cantidad;
                $detalle[$articulo->PROD_Codigo]['precio_uni_c_igv'] = $articulo->OCOMDEC_Pu_ConIgv;
                $detalle[$articulo->PROD_Codigo]['precio_uni_s_igv'] = $articulo->OCOMDEC_Pu;
                $detalle[$articulo->PROD_Codigo]['precio_total'] = $detalle[$articulo->PROD_Codigo]['precio_uni_s_igv'] * $detalle[$articulo->PROD_Codigo]['cantidad'];
            }else {
                $detalle[$articulo->PROD_Codigo] = array(
                        'codigo' => $articulo->PROD_Codigo,
                        'codigo_interno' => $articulo->PROD_CodigoInterno,
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

    public function obtener_detalle_cotizacion($cotizacion)
    {
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
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->COTDEP_Codigo = $detcotizacion;
                $objeto->PEDIP_Codigo = $pedido;
                $objeto->PROD_Codigo = $producto;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->COTDEC_Cantidad = $cantidad;
                $objeto->PROD_CodigoInterno = $codigo_interno;
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

    public function obtener_detalle_oservicio($oservicio)
    {
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio($oservicio);
        $listado = array();
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $detoservicio = $valor->OCOMDEP_Codigo;
                $oservicio = $valor->OCOMP_Codigo;
                $cotizacion = $valor->COTIP_Codigo;
                $pedido = $valor->PEDIP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad_medida = $valor->UNDMED_Codigo;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $costo = $valor->OCOMDEC_Total;
                $datos_oservicio = $this->oservicio_model->obtener_oservicio($oservicio);
                $proveedor = $datos_oservicio[0]->PROVP_Codigo;
                $almacen = $datos_oservicio[0]->ALMAP_Codigo;
                $formapago = $datos_oservicio[0]->FORPAP_Codigo;

                $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                $razon_social = $datos_proveedor->nombre;
                $ruc = $datos_proveedor->ruc;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $nombre_unidad = $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->OCOMP_Codigo = $oservicio;
                $objeto->COTIP_Codigo = $cotizacion;
                $objeto->PEDIP_Codigo = $pedido;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_GenericoIndividual = $flagGenInd;
                $objeto->UNDMED_Codigo = $unidad_medida;
                $objeto->OCOMDEC_Cantidad = $cantidad;
                $objeto->OCOMDEC_Total = $costo;
                $objeto->PROD_CodigoInterno = $codigo_interno;
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

    public function obtener_detalle_oservicio2($oservicio)
    {
        $datos_detalle_oservicio = $this->oservicio_model->obtener_detalle_oservicio2($oservicio);
        $listado = array();
        if (count($datos_detalle_oservicio) > 0) {
            foreach ($datos_detalle_oservicio as $indice => $valor) {
                $detoservicio = $valor->OCOMDEP_Codigo;
                $oservicio = $valor->OCOMP_Codigo;
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
                
                $datos_oservicio = $this->oservicio_model->obtener_oservicio($oservicio);
                if ($datos_oservicio[0]->PROVP_Codigo == '') {
                    $proveedor = $datos_oservicio[0]->CLIP_Codigo;
                    $datos_proveedor = $this->cliente_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                } else {
                    $proveedor = $datos_oservicio[0]->PROVP_Codigo;
                    $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                    $razon_social = $datos_proveedor->nombre;
                    $ruc = $datos_proveedor->ruc;
                }
                $almacen = $datos_oservicio[0]->ALMAP_Codigo;
                $formapago = $datos_oservicio[0]->FORPAP_Codigo;
                $moned_codigo = $datos_oservicio[0]->MONED_Codigo;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_umedida = is_null($unidad_medida) ? NULL : $this->unidadmedida_model->obtener($unidad_medida);
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                //$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                
                $nombre_unidad = is_null($datos_umedida) ? '' : $datos_umedida[0]->UNDMED_Simbolo;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->OCOMP_Codigo = $oservicio;
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
                
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->PROVP_Codigo = $proveedor;
                $objeto->ALMAP_Codigo = $almacen;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PROD_GenericoIndividual = $flagGenInd;

                if($datos_oservicio[0]->OCOMC_TipoOperacion == 'C' && $datos_oservicio[0]->OCOMC_FlagBS != $datos_producto[0]->PROD_FlagBienServicio) 
                    continue;


                $listado[] = $objeto;
            }
        }
        $resultado = json_encode($listado);
        echo $resultado;
    }

    /* Combos */

    public function seleccionar_cotizacion($indSel = '')
    {
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
        
        $data['titulo'] = "REPORTES DE COMPRAS";
        $data['combo'] = $combo;
        $data['combo2'] = $combo2;
        $data['combo3'] = $combo3;
        $this->layout->view('compras/oservicio_reporte', $data);
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
        $listado = $this->oservicio_model->buscar_oservicio($tipo_oper,$fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso);

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

    public function ventana_oservicio_factura($oservicio)
    {
        $codigo = $this->input->post('codigo');
        $numero = $this->input->post('numero');
        if ($numero != '') {
            $this->oservicio_model->modificar_oservicio_flagRecibido($codigo, $numero);
            echo "<script>window.close();</script>";
        }
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($oservicio);
        $data['numero_factura'] = $datos_oservicio[0]->OCOMC_NumeroFactura;
        $data['oservicio'] = $oservicio;
        $this->load->view('compras/ventana_oservicio_factura', $data);
    }

    public function estadisticas()
    {
        /* Imagen 1 */
        $listado = $this->oservicio_model->reporte_oservicio_5_prov_mas_importantes();

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
        $listado = $this->oservicio_model->reporte_oservicio_monto_x_mes();
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
        $listado = $this->oservicio_model->reporte_oservicio_cantidad_x_mes();
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
        $listado = $this->oservicio_model->reporte_comparativo_compras_ventas('C');
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
        /* $listado=$this->oservicio_model->reporte_comparativo_compras_ventas('V'); 
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

    public function comprobante_nueva_oservicio($codigo, $tipo_oper = 'C')
    {

        $this->load->helper('my_guiarem');

        $comp_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/formapago_model');
        $accion = "";
        $modo = "modificar";
        $datos_oservicio = $this->oservicio_model->obtener_oservicio($codigo);
        $presupuesto = $datos_oservicio[0]->PRESUP_Codigo;
        $cotizacion = $datos_oservicio[0]->COTIP_Codigo;
        $pedido = $datos_oservicio[0]->PEDIP_Codigo;
        $numero = $datos_oservicio[0]->OCOMC_Numero;
        $codigo_usuario = $datos_oservicio[0]->OCOMC_CodigoUsuario;
        $serie = $datos_oservicio[0]->OCOMC_Serie;

        $descuento100 = $datos_oservicio[0]->OCOMC_descuento100;
        $igv100 = $datos_oservicio[0]->OCOMC_igv100;
        $percepcion100 = $datos_oservicio[0]->OCOMC_percepcion100;
        $cliente = $datos_oservicio[0]->CLIP_Codigo;
        $proveedor = $datos_oservicio[0]->PROVP_Codigo;
        $centro_costo = $datos_oservicio[0]->CENCOSP_Codigo;
        $moneda = $datos_oservicio[0]->MONED_Codigo;
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_MiPersonal);
        $mi_contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $lista_directivo = $this->directivo_model->obtener_directivo($datos_oservicio[0]->OCOMC_Personal);
        $contacto = is_array($lista_directivo) ? $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_ApellidoMaterno . ' ' . $lista_directivo[0]->PERSC_Nombre : '';
        $envio_direccion = $datos_oservicio[0]->OCOMC_EnvioDireccion;
        $fact_direccion = $datos_oservicio[0]->OCOMC_FactDireccion;
        $observacion = $datos_oservicio[0]->OCOMC_Observacion;
        $fecha = substr($datos_oservicio[0]->OCOMC_Fecha, 0, 10);
        $fechaentrega = substr($datos_oservicio[0]->OCOMC_FechaEntrega, 0, 10);
        $flagIngreso = $datos_oservicio[0]->OCOMC_FlagIngreso;
        $almacen = $datos_oservicio[0]->ALMAP_Codigo;
        $formapago = $datos_oservicio[0]->FORPAP_Codigo;
        $usuario = $datos_oservicio[0]->USUA_Codigo;
        $ctactesoles = $datos_oservicio[0]->OCOMC_CtaCteSoles;
        $ctactedolares = $datos_oservicio[0]->OCOMC_CtaCteDolares;
        $estado = $datos_oservicio[0]->OCOMC_FlagEstado;

        $subtotal = $datos_oservicio[0]->OCOMC_subtotal;
        $descuentototal = $datos_oservicio[0]->OCOMC_descuento;
        $igvtotal = $datos_oservicio[0]->OCOMC_igv;
        $percepciontotal = $datos_oservicio[0]->OCOMC_percepcion;
        $total = $datos_oservicio[0]->OCOMC_total;

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
        $data['url_action'] = base_url() . "index.php/compras/oservicio/modificar_oservicio";
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

        $detalle = $this->oservicio_model->obtener_detalle_oservicio($codigo);
        $detalle_oservicio = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                // $tipo_orden,$tipo_guia,$cod_orden,$cod_prod
                $cantidad_entregada = calcular_cantidad_entregada_x_producto($tipo_oper, $tipo_oper, $codigo, $valor->PROD_Codigo);
                $cantidad_pendiente = $valor->OCOMDEC_Cantidad - $cantidad_entregada;
                $detoservicio = $valor->OCOMDEP_Codigo;
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
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                $objeto = new stdClass();
                $objeto->OCOMDEP_Codigo = $detoservicio;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
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

                $detalle_oservicio[] = $objeto;
            }
        }
        $data['detalle_oservicio'] = $detalle_oservicio;

        $this->load->view('compras/oservicio_ventana_mostrar', $data);
    }

    public function ventana_muestra_proveedor($tipo_oper)
    {
        $oservicios = $this->oservicio_model->listar($tipo_oper);
        $lista = array();
        if (count($oservicios) > 0) {
            foreach ($oservicios as $value) {
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
        $this->load->view('compras/oservicio_ventana_seleccionar', $data);
    }
    public function registro_oservicios_pdf($tipo_oper, $fecha_ini='', $fecha_fin='',$ruc='', $nombre='')
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


        $listado_productos = $this->oservicio_model->listar_oservicios_pdf($tipo_oper,$fecha_ini, $fecha_fin,$codigo,$nombre);
    
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

}

?>
