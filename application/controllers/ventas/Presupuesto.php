<?php

ini_set('error_reporting', 1);

include("system/application/libraries/cezpdf.php");
//include("system/application/libraries/fpd/fpdf.php");
include("system/application/libraries/class.backgroundpdf.php");
//include_once("system/application/libraries/vo/plantilla.php");

class Presupuesto extends CI_Controller {

    public function __construct() {
        parent::Controller();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('vo/plantilla');
        $this->load->model('maestros/marca_model');
        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/pedidodetalle_model');
        $this->load->model('ventas/presupuesto_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('compras/presupuesto_model');
        $this->load->model('ventas/presupuestodetalle_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/emprestablecimiento_model');
        $this->load->model('maestros/companiaconfidocumento_model');
        $this->load->model('empresa/emprcontacto_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/area_model');
        $this->load->model('configuracion_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
    }

    public function index() {
        $this->load->view('seguridad/inicio');
        
    }

    public function presupuestos($j = '0', $limpia = '') {
        $data['compania'] = $this->somevar['compania'];
        
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);

        if ($limpia == '1') {
            $this->session->unset_userdata('fechai');
            $this->session->unset_userdata('fechaf');
            $this->session->unset_userdata('serie');
            $this->session->unset_userdata('numero');
            $this->session->unset_userdata('codigo_usuario');
            $this->session->unset_userdata('cliente');
            $this->session->unset_userdata('ruc_cliente');
            $this->session->unset_userdata('nombre_cliente');
            $this->session->unset_userdata('producto');
            $this->session->unset_userdata('codproducto');
            $this->session->unset_userdata('nombre_producto');
        }

        $filter = new stdClass();
        if (count($_POST) > 0) {
            $filter->fechai = $this->input->post('fechai');
            $filter->fechaf = $this->input->post('fechaf');
            $filter->serie = $this->input->post('serie');
            $filter->numero = $this->input->post('numero');
            $filter->codigo_usuario = $this->input->post('codigo_usuario');
            $filter->cliente = $this->input->post('cliente');
            $filter->ruc_cliente = $this->input->post('ruc_cliente');
            $filter->nombre_cliente = $this->input->post('nombre_cliente');

            $filter->producto = $this->input->post('producto');
            $filter->codproducto = $this->input->post('codproducto');
            $filter->nombre_producto = $this->input->post('nombre_producto');
            $this->session->set_userdata(array('fechai' => $filter->fechai, 'fechaf' => $filter->fechaf, 'serie' => $filter->serie, 'numero' => $filter->numero, 'codigo_usuario' => $filter->codigo_usuario, 'cliente' => $filter->cliente, 'ruc_cliente' => $filter->ruc_cliente, 'nombre_cliente' => $filter->nombre_cliente, 'producto' => $filter->producto, 'codproducto' => $filter->codproducto, 'nombre_producto' => $filter->nombre_producto));
        } else {
            $filter->fechai = $this->session->userdata('fechai');
            $filter->fechaf = $this->session->userdata('fechaf');
            $filter->serie = $this->session->userdata('serie');
            $filter->numero = $this->session->userdata('numero');
            $filter->codigo_usuario = $this->session->userdata('codigo_usuario');
            $filter->cliente = $this->session->userdata('cliente');
            $filter->ruc_cliente = $this->session->userdata('ruc_cliente');
            $filter->nombre_cliente = $this->session->userdata('nombre_cliente');
            $filter->producto = $this->session->userdata('producto');
            $filter->codproducto = $this->session->userdata('codproducto');
            $filter->nombre_producto = $this->session->userdata('nombre_producto');
        }
        $data['fechai'] = $filter->fechai;
        $data['fechaf'] = $filter->fechaf;
        $data['serie'] = $filter->serie;
        $data['numero'] = $filter->numero;
        $data['codigo_usuario'] = $filter->codigo_usuario;
        $data['cliente'] = $filter->cliente;
        $data['ruc_cliente'] = $filter->ruc_cliente;
        $data['nombre_cliente'] = $filter->nombre_cliente;
        $data['producto'] = $filter->producto;
        $data['codproducto'] = $filter->codproducto;
        $data['nombre_producto'] = $filter->nombre_producto;

        $data['registros'] = count($this->presupuesto_model->buscar_presupuestos($filter));
        $conf['base_url'] = site_url('ventas/presupuesto/presupuestos');
        $conf['per_page'] = 30;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);

        $listado_presupuestos = $this->presupuesto_model->buscar_presupuestos($filter, $conf['per_page'], $offset);
        $item = $j + 1;
        $lista = array();
        if (count($listado_presupuestos) > 0) {
            foreach ($listado_presupuestos as $indice => $valor) {
                $codigo = $valor->PRESUP_Codigo;

                $fecha = mysql_to_human($valor->PRESUC_Fecha);
                $serie = $valor->PRESUC_Serie;
                $numero = $valor->PRESUC_Numero;

                $codigo_usuario = $valor->PRESUC_CodigoUsuario;
                $Mensajesenviados = $this->presupuesto_model->correoenviado_presu($codigo);
                if (count($Mensajesenviados) > 0)
                    $vermensaje = '<a href="javascript:;" title="Mensajes enviados:' . count($Mensajesenviados) . '" class="tooltip"><img src="' . base_url() . '/images/entregado.png"></a>';
                else
                    $vermensaje = '<a href="javascript:;" title="Ninungun Mensaje enviado" class="tooltip"><img src="' . base_url() . '/images/ninguno.png"></a>';

                $nombre_cliente = $valor->nombre;
                if ($valor->CLIP_Codigo == 144 ||
                        $valor->CLIP_Codigo == 135 ||
                        $valor->CLIP_Codigo == 218 ||
                        $valor->CLIP_Codigo == 1037
                )
                    $nombre_cliente = $valor->PRESUC_NombreAuxiliar;

                $nom_tipodocu = $valor->nom_tipodocu;
                $total = $valor->MONED_Simbolo . ' ' . number_format($valor->PRESUC_total, 2);
                $estado = $valor->PRESUC_FlagEstado;

                $img_estado = ($estado == '1' ? "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />" : "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />");
                $editar = "<a href='javascript:;' onclick='editar_presupuesto(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver = "<a href='javascript:;' onclick='ver_presupuesto_ver_pdf_conmenbrete(" . $codigo . ",1)' target='_parent'><img src='" . base_url() . "images/imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2 = "<a href='javascript:;' onclick='ver_presupuesto_ver_pdf_conmenbrete(" . $codigo . ",0)' target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $ver3 = "<a href='javascript:;' onclick='ver_presupuesto_ver_xls(" . $codigo . ")' target='_self'><img src='" . base_url() . "images/xls.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar = $estado == '1' ? "<a href='javascript:;' onclick='eliminar_presupuesto(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>" : "";
                $enviarcorreo = $estado == '1' ? "<a href='" . base_url() . "index.php/ventas/presupuesto/ventana_presupuesto_correos/" . $codigo . "' class='enviarcorreo'><img src='" . base_url() . "images/send.png' width='16' height='16' border='0' title='Enviar Presupuesto via correo'></a>" : "";
                $lista[] = array($item++, $fecha, $serie, $this->getOrderNumeroSerie($numero), $codigo_usuario, $nombre_cliente, $nom_tipodocu, $total, $img_estado, $editar, $ver, $ver2, $ver3, $enviarcorreo, $vermensaje, $eliminar);
            }
        }
        $data['titulo_tabla'] = "RELACIÃ“N DE PRE-VENTA";
        $data['titulo_busqueda'] = "BUSCAR PRE-VENTA";
        $data['lista'] = $lista;
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $data['tipo_codificacion'] = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('ventas/presupuesto_index', $data);
    }

    public function ventana_muestra_presupuesto($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = 'G', $almacen = "", $comprobante = '') {
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

        $lista_comprobante = $this->presupuesto_model->buscar_presupuestos_asoc($tipo_oper, $docu_orig = 'G', $filter);


        //$lista_comprobante = $this->comprobante_model->buscar_comprobantes_asoc($tipo_oper, $comprobante , $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documentoPresupuesto(" . $value->PRESUP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_presupuesto(" . $value->PRESUP_Codigo . " ," . $value->PRESUC_Serie . "," . $value->PRESUC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->PRESUC_Fecha), $value->PRESUC_Serie, $value->PRESUC_Numero, '', $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->PRESUC_total), $ver, $select);
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

    ///GCBQ Ventana Presupuesto para comprobante segun tipo
    public function ventana_muestra_presupuestoCom($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $tipo_doc, $almacen = "", $comprobante = '', $ventana = '') {
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

        $lista_comprobante = $this->presupuesto_model->buscar_presupuestos_asoc($tipo_oper, $tipo_doc, $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documentoPresupuesto(" . $value->PRESUP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            $serie = $value->PRESUC_Serie;
            if ($serie == null || $serie == '')
                $serie = 0;

            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_presupuesto(" . $value->PRESUP_Codigo . " ," . $serie . "," . $value->PRESUC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->PRESUC_Fecha), $value->PRESUC_Serie, $value->PRESUC_Numero, '', $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->PRESUC_total), $ver, $select);
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
        if ($ventana != 'OC') {
            $data['form_open'] = form_open(base_url() . "index.php/almacen/producto/ventana_muestra_guiarem", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "tipo_doc" => $tipo_doc, "formato" => $formato));
            $this->load->view('almacen/ventana_muestra_guiarem', $data);
        } else {
            $data['form_open'] = form_open(base_url() . "index.php/compras/ventana_muestra_ocompra", array("name" => "frmComprobante", "id" => "frmComprobante"));
            $data['form_close'] = form_close();
            $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "tipo_doc" => $tipo_doc, "formato" => $formato));
            $this->load->view('compras/ventana_muestra_ocompra', $data);
        }
    }

    public function ventana_muestra_presupuestoRecu($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = 'G', $almacen = "", $comprobante = '') {
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';


        $cliente = $codigo;
        $datos_cliente = $this->cliente_model->obtener($cliente);
        if ($datos_cliente) {
            $nombre_cliente = $datos_cliente->nombre;
            $ruc_cliente = $datos_cliente->ruc;
        }
        $filter = new stdClass();
        $filter->cliente = $cliente;


        $lista_comprobante = $this->presupuesto_model->buscar_presupuestos_asoc($tipo_oper, $docu_orig = 'G', $filter);

        $lista = array();
        foreach ($lista_comprobante as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documentoPresupuesto(" . $value->PRESUP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $select = '';
            if ($formato == 'SELECT_HEADER')
                $select = "<a href='javascript:;' onclick='seleccionar_presupuesto(" . $value->PRESUP_Codigo . " ," . $value->PRESUC_Serie . "," . $value->PRESUC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar Comprobante'></a>";
            $lista[] = array(mysql_to_human($value->PRESUC_Fecha), $value->PRESUC_Serie, $value->PRESUC_Numero, '', $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->PRESUC_total), $ver, $select);
        }

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['almacen'] = $almacen;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['docu_orig'] = $docu_orig;
        $data['formato'] = $formato;
        $data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_presupuesto", array("name" => "frmComprobante", "id" => "frmComprobante"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));

        $this->load->view('ventas/ventana_muestra_presupuesto', $data);
    }
    
    public function ventana_muestra_Opedido($tipo_oper, $codigo = '', $formato = 'SELECT_ITEM', $docu_orig = 'G', $almacen = "", $comprobante = '') {
    	$cliente = '';
    	$nombre_cliente = '';
    	$ruc_cliente = '';
    
    
    	$cliente = $codigo;
    	$datos_cliente = $this->cliente_model->obtener($cliente);
    	if ($datos_cliente) {
    		$nombre_cliente = $datos_cliente->nombre;
    		$ruc_cliente = $datos_cliente->ruc;
    	}
    	$filter = new stdClass();
    	$filter->cliente = $cliente;
    
    
    	$lista_pedido = $this->pedido_model->buscar_pedido_asoc($tipo_oper, $docu_orig = 'G', $filter);
    
    	$lista = array();
    	foreach ($lista_pedido as $indice => $value) {
    		$ver = "<a href='javascript:;' onclick='ver_detalle_documentoPedido(" . $value->PEDIP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
    		$select = '';
    		if ($formato == 'SELECT_HEADER')
    			$select = "<a href='javascript:;' onclick='seleccionar_pedido(" . $value->PEDIP_Codigo . " ," . $value->PEDIC_Serie . "," . $value->PEDIC_Numero . ")'><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Seleccionar OR. Pedido'></a>";
    			$lista[] = array(mysql_to_human($value->PEDIC_FechaRegistro), $value->PEDIC_Serie, $value->PEDIC_Numero, '', $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->PEDIC_PrecioTotal), $ver, $select);
    	}
    
    	$data['lista'] = $lista;
    	$data['cliente'] = $cliente;
    	$data['nombre_cliente'] = $nombre_cliente;
    	$data['ruc_cliente'] = $ruc_cliente;
    	$data['almacen'] = $almacen;
    	$data['comprobante'] = $comprobante;
    	$data['tipo_oper'] = $tipo_oper;
    	$data['docu_orig'] = $docu_orig;
    	$data['formato'] = $formato;
    	$data['form_open'] = form_open(base_url() . "index.php/ventas/comprobante/ventana_muestra_pedido", array("name" => "frmComprobante", "id" => "frmComprobante"));
    	$data['form_close'] = form_close();
    	$data['form_hidden'] = form_hidden(array("base_url" => base_url(), "docu_orig" => $docu_orig, "formato" => $formato));
    
    	$this->load->view('ventas/ventana_muestra_opedido', $data);
    }

    public function presupuesto_nueva($tipo_docu = 'V') {
        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;
        

        unset($_SESSION['serie']);

        // $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        //
        $tipo = 13;
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);

        //$data_confi1 = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $tipo);
        //


        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $codigo = "";
        $data['contiene_igv'] = (($data_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0'), "tipo_codificacion" => $data_confi_docu[0]->COMPCONFIDOCP_Tipo));

        $data['url_action'] = base_url() . "index.php/ventas/presupuesto/presupuesto_insertar";
        $data['titulo'] = "REGISTRAR PRESUPUESTO";
        $data['formulario'] = "frmPresupuesto";
        $data['oculto'] = $oculto;
        $data['onload'] = "onload=\"$('#nombre').focus();\"";
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '12'); //12: Al contado
        $data['cboContacto'] = form_dropdown("contacto", array('' => ':: Seleccione ::'), "", " class='comboGrande' style='width:307px;' id='contacto'");
       $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');



//SERIE DE PRESUPUESTO
        // $serie = '001';
        $data['lugar_entrega'] = "";
        $data['serie'] = "";
        $data['numero'] = "";
        //f
        //$data['numero'] = $numero;

        $data['codigo_usuario'] = "";
        if ($tipo_docu == "B") {
            $data['cliente'] = ""; //144
            $data['ruc_cliente'] = ""; //11111111
            $data['nombre_cliente'] = "";  //*VARIOS
        } else {
            $data['cliente'] = "";
            $data['ruc_cliente'] = "";
            $data['nombre_cliente'] = "";
        }
        $data['proveedor'] = "";
        $data['ruc_proveedor'] = "";
        $data['nombre_proveedor'] = "";
        $data['detalle_presupuesto'] = array();
        $data['observacion'] = "";
        $data['focus'] = "";
        $data['pedido'] = "";
        $data['descuento'] = "0";
        $data['igv'] = $data_confi[0]->COMPCONFIC_Igv;
        $data['hidden'] = "";
        $data['preciototal'] = "";
        $data['descuentotal'] = "";
        $data['igvtotal'] = "";
        $data['importetotal'] = "";
        $data['preciototal_conigv'] = "";
        $data['descuentotal_conigv'] = "";
        $data['hidden'] = "";
        $data['observacion'] = "";
        $data['envio_direccion'] = "";
        $data['fact_direccion'] = "";
        $data['contacto'] = "";
        $data['tiempo_entrega'] = "";
        $data['garantia'] = "";
        $data['validez'] = "";
        $data['tipo_docu'] = $tipo_docu;
        $data['codigo'] = "";
        $data['estado'] = "1";
        $data['modo_impresion'] = "1";
        if ($tipo_docu != 'B')
            $data['modo_impresion'] = "2";
        $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";

        $serie = $data_confi_docu[0]->COMPCONFIDOCP_Serie;
        $data['tipo_codificacion'] = 2;
        $data['tipo_codificacion'] = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        //$cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($my_empresa, 13);
        /* $data['serie_suger'] = $data_confi1[0]->CONFIC_Serie;
          $data['numero_suger'] = $data_confi1[0]->CONFIC_Numero + 1; */

        $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        /* $cofiguracion_datos[0]->CONFIC_Serie;
          $cofiguracion_datos[0]->CONFIC_Numero; */
        // $ultimo_serie_numero = $this->comprobante_model->ultimo_serie_numero($tipo_oper, 'B');
        $data['serie_suger'] = $cofiguracion_datos[0]->CONFIC_Serie;
        $data['numero_suger'] = $this->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
        $data['cmbVendedor']=$this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
        $this->layout->view('ventas/presupuesto_nuevo', $data);
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
    public function presupuesto_insertar() {

        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $tipo_codificacion = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        //echo $guiarem_id; exit;

        switch ($tipo_codificacion) {
            case '2':
                if ($this->input->post('serie') == '')
                    exit('{"result":"error", "campo":"serie"}');

                if ($this->input->post('numero') == '')
                    exit('{"result":"error", "campo":"numero"}');

                break;
            case '3':
                if ($this->input->post('codigo_usuario') == '')
                    exit('{"result":"error", "campo":"codigo_usuario"}');
                break;
        }
        if ($this->input->post('serie') == '')
            exit('{"result":"error", "campo":"serie"}');

        if ($this->input->post('numero') == '')
            exit('{"result":"error", "campo":"numero"}');

        if ($this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');

        if ($this->input->post('moneda') == '' || $this->input->post('moneda') == '0')
            exit('{"result":"error", "campo":"moneda"}');

        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');

        $tipo_docu = $this->input->post('tipo_docu');

        $filter = new stdClass();
        $filter->PRESUC_TipoDocumento = $tipo_docu;
//guardar serie de presupuesto
        /*  $serie = NULL;
          if ($this->input->post("serie"))
          $filter->PRESUC_Serie= $this->input->post("serie"); */


        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->PRESUC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->PRESUC_Fecha = human_to_mysql($this->input->post('fecha'));


        $compania = $this->somevar['compania'];
    $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
        $numero_predt = $this->presupuesto_model->ultimo_numero();
        $numero = $numero_predt[0]->PRESUC_Numero;
        $num = $configuracion_datos[0]->CONFIC_Numero + 1;
        $filter->PRESUC_Numero = $numero + 1;
        $numero = $this->input->post('numero');
        $filter->PRESUC_Numero = $this->input->post('numero');



        ///SERIE NUMERO GUARDAR PRESUPEUSTO FLO
        if ($this->input->post('serie') != '' && $this->input->post('serie') != '0') {
            $filter->PRESUC_Serie = $this->input->post('serie');
        }
       
        if ($this->input->post('codigo_usuario'))
            $filter->PRESUC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->MONED_Codigo = $this->input->post('moneda');
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0') {
            $temp = explode('-', $this->input->post('contacto'));
            $filter->PERSP_Codigo = $temp[0];
            $filter->AREAP_Codigo = $temp[1];
        }
        if ($this->input->post('vendedor') != '' && $this->input->post('vendedor') != '0') {
            $filter->PRESUC_VendedorPersona = $this->input->post('vendedor');
        }

        $filter->PRESUC_LugarEntrega = $this->input->post('lugar_entrega');
        $filter->PRESUC_TiempoEntrega = $this->input->post('tiempo_entrega');
        $filter->PRESUC_Garantia = $this->input->post('garantia');
        $filter->PRESUC_Validez = $this->input->post('validez');
        $filter->PRESUC_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->PRESUC_ModoImpresion = $this->input->post('modo_impresion');
        $filter->PRESUC_FlagEstado = $this->input->post('estado');
        $filter->PRESUC_descuento100 = $this->input->post('descuento');
        $filter->PRESUC_igv100 = $this->input->post('igv');
        $filter->CLIP_Codigo = $this->input->post('cliente');
        //if ($tipo_docu != 'B') {
        $filter->PRESUC_subtotal = $this->input->post('preciototal');
        $filter->PRESUC_descuento = $this->input->post('descuentotal');
        $filter->PRESUC_igv = $this->input->post('igvtotal');
       
        //} else {
        //$filter->PRESUC_subtotal_conigv = $this->input->post('preciototal_conigv');
        //  $filter->PRESUC_descuento_conigv = $this->input->post('descuentotal_conigv');
        //}
        $filter->PRESUC_total = $this->input->post('importetotal');
        if ($this->input->post('cliente') == 144 ||
                $this->input->post('cliente') == 135 ||
                $this->input->post('cliente') == 218 ||
                $this->input->post('cliente') == 1037
        )
            $filter->PRESUC_NombreAuxiliar = strtoupper($this->input->post('nombre_cliente'));
        $presupuesto = $this->presupuesto_model->insertar_presupuesto($filter);
        $this->configuracion_model->update_numero_presupuesto($this->input->post('numero'), $this->somevar['compania']);
        //relacion con PEDIDO
        $pedido=$this->input->post('pedidocodigo');
        IF($pedido != ""){
        	$this->pedido_model->update_pedido_presupuesto($pedido,$presupuesto);
        	
        }

        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        //  if ($tipo_docu != 'B') {
        $prodpu = $this->input->post('prodpu');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $prodigv = $this->input->post('prodigv');
        /* } else {
          $prodprecio_conigv = $this->input->post('prodprecio_conigv');
          $proddescuento_conigv = $this->input->post('proddescuento_conigv');
          } */

        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescri = $this->input->post('proddescri');
        if (is_array($prodcodigo)) {
            foreach ($prodcodigo as $indice => $valor) {
                $filter = new stdClass();
                $filter->PRESUP_Codigo = $presupuesto;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                if ($flagBS[$indice] == 'B')
                    $filter->UNDMED_Codigo = $produnidad[$indice];
                $filter->PRESDEC_Cantidad = $prodcantidad[$indice];
                // if ($tipo_docu != 'B') {
                $filter->PRESDEC_Pu = $prodpu[$indice];
                $filter->PRESDEC_Subtotal = $prodprecio[$indice];
                $filter->PRESDEC_Descuento = $proddescuento[$indice];
                $filter->PRESDEC_Igv = $prodigv[$indice];
                /* } else {
                  $filter->PRESDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                  $filter->PRESDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
                  } */
                $filter->PRESDEC_Total = $prodimporte[$indice];
                $filter->PRESDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->PRESDEC_Descuento100 = $proddescuento100[$indice];
                $filter->PRESDEC_Igv100 = $prodigv100[$indice];
                $filter->PRESDEC_Descripcion = strtoupper($proddescri[$indice]);
                $filter->PRESDEC_Observacion = "";
                if ($detaccion[$indice] != 'e')
                    $this->presupuestodetalle_model->insertar($filter);
            }
        }
        exit('{"result":"ok", "codigo":"' . $presupuesto . '"}');
    }

    public function presupuesto_editar($codigo) {
        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;
        
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa = $data_compania[0]->EMPRP_Codigo;

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $forma_pago = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda = $datos_presupuesto[0]->MONED_Codigo;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $persona = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $contacto = ($persona != '' ? $persona . '-' . $area : '');
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $vendedor_contacto = ($vendedor_persona != '' ? $vendedor_persona . '-' . $vendedor_area : '');
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $modo_impresion = $datos_presupuesto[0]->PRESUC_ModoImpresion;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $estado = $datos_presupuesto[0]->PRESUC_FlagEstado;

        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $subtotal_conigv = $datos_presupuesto[0]->PRESUC_subtotal_conigv;
        $descuento_conigv = $datos_presupuesto[0]->PRESUC_descuento_conigv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;

         $vendedor = $datos_presupuesto[0]->PRESUC_VendedorPersona;
      
        $data['cmbVendedor']=$this->select_cmbVendedor($vendedor);

        $datos_cliente = $this->cliente_model->obtener($cliente);
        $tipo = '';
        $ruc_cliente = '';
        $nombre_cliente = '';
        $empresa = '';
        $persona = '';
        if ($datos_cliente) {

            $tipo = $datos_cliente->tipo;

            $nombre_cliente = $datos_cliente->nombre;
            if ($cliente == 144 ||
                    $cliente == 135 ||
                    $cliente == 218 ||
                    $cliente == 1037
            )
                $nombre_cliente = $datos_presupuesto[0]->PRESUC_NombreAuxiliar;

            $ruc_cliente = $datos_cliente->ruc;
            $empresa = $datos_cliente->empresa;
            $persona = $datos_cliente->persona;
        }

        //Contactos
        $contactos = $this->empresa_model->listar_contactosEmpresa($empresa);
        $arrContacto = array("" => "::Seleccione::");
        if (count($contactos) > 0) {
            foreach ($contactos as $value) {
                $persona = $value->ECONC_Persona . '-' . $value->AREAP_Codigo;
                $nombres_persona = $value->PERSC_Nombre . " " . $value->PERSC_ApellidoPaterno . " " . $value->PERSC_ApellidoMaterno . ($value->AREAP_Codigo != '0' && $value->AREAP_Codigo != '' ? " - " . $value->AREAC_Descripcion : '');
                $arrContacto[$persona] = $nombres_persona;
            }
        }

        $data['contacto'] = $contacto;
        $data['cboContacto'] = form_dropdown("contacto", $arrContacto, $contacto, " class='comboMedio' style='width:307px;' id='contacto'");
        $data['cboVendedor'] = form_dropdown("vendedor", $this->emprcontacto_model->seleccionar($my_empresa), $vendedor_contacto, " class='comboGrande' style='width:207px;' id='vendedor'");
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', ($forma_pago != '' ? $forma_pago : '12'));  //12: Al contado
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', $moneda);




        $data['lugar_entrega'] = $lugar_entrega;
        $data['tiempo_entrega'] = $tiempo_entrega;
        $data['garantia'] = $garantia;
        $data['validez'] = $validez;
        $data['estado'] = $estado;

        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['codigo_usuario'] = $codigo_usuario;

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
        $data['contiene_igv'] = (($data_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
        $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0'), "tipo_codificacion" => $data_confi_docu[0]->COMPCONFIDOCP_Tipo));
        $data['titulo'] = "EDITAR PRE-VENTA";
        $data['formulario'] = "frmPresupuesto";
        $data['oculto'] = $oculto;
        $data['onload'] = "onload=\"\"";
        $data['url_action'] = base_url() . "index.php/ventas/presupuesto/presupuesto_modificar";
        $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
        $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos);
        $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos);
        $data['hoy'] = $fecha;
        $data['observacion'] = $observacion;
        $data['hidden'] = "";
        $data['tipo_docu'] = $tipo_docu;
        $data['codigo'] = $codigo;
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger'] = "";
        $data['numero_suger'] = "";

        $data['detalle_presupuesto'] = $this->obtener_lista_detalles($codigo);

        //$data['tipo_codificacion'] = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        $data['tipo_codificacion'] = 1;
        $this->layout->view('ventas/presupuesto_nuevo', $data);
    }

    public function ventana_presupuesto_correos($codigo) {
        $nombre_persona1 = $this->session->userdata('nombre_persona');
        $persona1 = $this->session->userdata('persona');

        $datos_usuario = $this->persona_model->obtener_datosPersona($persona1);
        if ($datos_usuario) {
            $emailusuario = $datos_usuario[0]->PERSC_Email;
        }

        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;

        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $forma_pago = $datos_presupuesto[0]->FORPAP_Codigo;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $persona = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $vendedor_contacto = ($vendedor_persona != '' ? $vendedor_persona . '-' . $vendedor_area : '');


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
        //$ver2 = " onclick='ver_presupuesto_ver_pdf_conmenbrete(" . $codigo . ",0)' >";
        //$ver3 = " onclick='ver_presupuesto_ver_xls(" . $codigo . ")'" ;


        $data['lista'] = $lista;
        /* $data['ver2'] = $ver2;
          $data['ver3'] = $ver3; */
        $data['cliente'] = $cliente;
        $data['serie'] = $serie;
        $data['numero'] = $numero;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['nombre_persona1'] = $nombre_persona1;
        $data['emailusuario'] = $emailusuario;
        $data['emailenviar'] = $emailenviar;
        $data['titulo'] = "ENVIAR PRESUPUESTO-CORREO";
        $data['formulario'] = "frmPresupuestoCorreo";
        $data['url_action'] = base_url() . "index.php/ventas/presupuesto/Enviarcorreo";
        $data['hoy'] = $fecha;
        $data['codigo'] = $codigo;


        $data['tipo_codificacion'] = 1;
        $this->load->view('ventas/presupuesto_correo', $data);
    }

    public function Enviarcorreo() {

        $this->load->library('email');

        $xlsgrabar = 0;
        $pdfgrabar = 0;
        $usuario = $this->input->post('usuario');
        $nombreusuario = $this->input->post('nombreusuario');
        $nombreDestinatario = $this->input->post('nomcontactopersona');
        $destinatario = $this->input->post('destinatario');
        $mensaje = $this->input->post('mensaje');
        $codigo = $this->input->post('codigo');
        $titulomensaje = $this->input->post('titulomensaje');

        $xls = $this->presupuesto_ver_xls_correo($codigo);

        $enviarformatopag = '<table cellpadding="0" cellspacing="0" width="900" align="center">
<tr>
<td style="background-color:#4617B4;padding:50px 30px 30px;" width="900">
<p style="font-size:30px;font-weight:lighter;line-height:38px;color:#ffffff;font-family:Segoe UI Light, Segoe WP Light, Segoe UI, Helvetica, Arial;">
		' . $titulomensaje . '</p>
</td>
    </tr>
    <tr>
        <td style="color:#454545;padding:53px 30px 35px;font-family:Segoe UI, Arial, Helvetica;font-size:14px;line-height:20px;" width="600">
<table border="0" cellspacing="0" cellpadding="0" style="color:#454545;font-size:14px;line-height:20px;font-family:Segoe UI, Arial, Helvetica;">
<tr>
<td style="padding:0px;">CuÃ¡ndo:&nbsp;' . date("Y-m-d") . '</td>
</tr>
<tr>
<td style="padding:0px;">' . $mensaje . '</td>
</tr>
</table><br>
<div style="padding-bottom:14px;">' . utf8_encode($xls) . '
</div> 
</tr>
    <tr>
        <td style="padding:0px 30px;background-color:#969696;color:#ffffff;font-size:78%;font-family:Segoe UI, Arial, Helvetica;" width="600">
            <br>LAMIBL<br><br>
        </td>
		
    </tr>
</table>';


        $this->email->set_mailtype("html");
        $this->email->from($usuario, $nombreusuario);
        $this->email->to($destinatario);
        $this->email->subject($titulomensaje);
        $this->email->message($enviarformatopag);

        if ($this->input->post('xls') != '') {
            $xlsgrabar = 1;
            $xlscode = $xls;
            $fpxls = fopen('presupuesto.xls', 'wb');
            fwrite($fpxls, $xlscode);
            fclose($fpxls);
            $this->email->attach('presupuesto.xls');
        }
        if ($this->input->post('pdf') != '') {
            $pdfgrabar = 1;
            $pdf = $this->presupuesto_ver_pdf_correo($codigo, 0);
            $pdfcode = $pdf;
            $fp = fopen('presupuesto.pdf', 'wb');
            fwrite($fp, $pdfcode);
            $this->email->attach('presupuesto.pdf');
            fclose($fp);
        }


        if ($this->email->send()) {
            if ($this->input->post('xls') != '')
                unlink('presupuesto.xls');


            if ($this->input->post('pdf') != '')
                unlink('presupuesto.pdf');

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

            echo '1';
        } else {
            if ($this->input->post('xls') != '')
                unlink('presupuesto.xls');


            if ($this->input->post('pdf') != '')
                unlink('presupuesto.pdf');

            echo 'mensaje no enviado';
        }
    }

    public function presupuesto_ver_xls_correo($codigo) {

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $xls = "<table><tr><td colspan=7 align=center>" . utf8_decode_seguro('<b>Presupuesto: ') . $codificacion . '</b></td></tr></table>';


        $xls .= "<table>
		<tr><td>" . utf8_decode_seguro('SeÃ±or(es) :') . " </td><td>$nombre_cliente</td><td></td><td>R.U.C. : </td><td>$ruc</td><td>Fecha : </td><td>$fecha</td></tr>
		<tr><td>" . utf8_decode_seguro('DirecciÃ³n :') . " </td><td>$direccion</td><td></td><td></td><td></td><td></td></tr>
		<tr><td>" . utf8_decode_seguro('AtenciÃ³n Sr(a) :') . " </td><td>$nombre_contacto " . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '') . "</td><td></td>&nbsp;&nbsp;&nbsp;<td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td>" . utf8_decode_seguro('TelÃ©fono : ') . "</td><td>$telefono</td><td></td><td></td><td></td><td>E-mail :</td><td>$email</td></tr>
		</table><br><br>
		";

        $date = date('Y-m-d') . '-' . $ruc . '-Presupuesto';
        header('Content-Disposition: attachment; filename="' . $date . '.xls"');
        header("Content-Type: application/vnd.ms-excel");

        $extra = "<th>Marca</th>";
        if (FORMATO_IMPRESION == 3) {
            $extra = "<th>Codigo</th><th>Marca</th><th>Modelo</th>";
        }

        $xls .= "
		<table border=1>
			<tr><th>Item</th>
      $extra
      <th>" . utf8_decode_seguro('DescripciÃ³n') . "</th>
      <th>Uni.</th>
      <th>Cant.</th>
      <th>Precio Uni.</th>
      <th>Precio Total</th></tr>
		";

        foreach ($detalle_presupuesto as $indice => $valor) {

            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($valor->PROD_Codigo);

            $xls .= "<tr>
      <td>" . ($indice + 1) . "</td>";
            if (FORMATO_IMPRESION == 3) {
                $xls .= "<td>" . $valor->PROD_CodigoUsuario . "</td>";
                $xls .= "<td>" . $marca_prod[0]->MARCC_Descripcion . "</td>";
                $xls .= "<td>" . $valor->PROD_Modelo . "</td>";
            } else {
                if (isset($marca_prod[0]->MARCC_Descripcion))
                    $xls .= "<td>" . $marca_prod[0]->MARCC_Descripcion . "</td>";
                else
                    $xls .= "<td></td>";
            }

            $xls .= "
      <td>" . utf8_decode_seguro($valor->PROD_Nombre) . "</td>
      <td>" . $valor->UNDMED_Simbolo . "</td>
      <td>" . $valor->PRESDEC_Cantidad . "</td>
      <td>" . number_format(($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2) . "</td>
      <td>" . number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2) . "</td></tr>";
        }

        $xls .= "</table><br><br>";


        if (FORMATO_IMPRESION == 3) {
            $subtotal_text = "SUBTOTAL";
            $igv_text = "IGV";
        } else {
            $subtotal_text = "VALOR VENTA";
            $igv_text = "IMPUESTO";
        }
        $xls .= "
		 <table>
			<tr><td colspan=5><b>SON : " . strtoupper(num2letras(round($total, 2))) . " $moneda_nombre</td>
      <td><b>$subtotal_text</b></td><td><b>" . number_format($subtotal, 2) . "</b></td></tr>
			<tr><td colspan=5></td><td><b>$igv_text</b></td><td><b>" . number_format($igv, 2) . "</b></td></tr>
			<tr><td colspan=5></td><td><b>TOTAL $moneda_simbolo</b></td><td><b>" . number_format($total, 2) . "</b></td></tr>
		</table>
		";


        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }


        $xls .= "
			<table>
			<tr><td colspan=2><b>CONDICIONES DE VENTA:</b></td></tr>";
        if (FORMATO_IMPRESION != 3) {
            $xls .= "<tr><td>" . utf8_decode_seguro('Tipo de Cambio del DÃ­a :') . "</td><td>" . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '') . "</td></tr>
        <tr><td>Moneda</td><td>$moneda_nombre</td></tr>";
        }
        $xls .= "<tr><td>Forma de Pago</td><td>" . utf8_decode_seguro($forma_pago) . "</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Los Precios de los Productos</td><td>" . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV') . "</td></tr>";

        if (FORMATO_IMPRESION == 3) {
            $xls .= "<tr><td>" . utf8_decode_seguro('Banco de CrÃ©dito Soles') . "</td><td>" . utf8_decode_seguro('NÂ°  191-1435467-0-65') . "</td></tr>
        <tr><td>" . utf8_decode_seguro('Banco de CrÃ©dito DÃ³lares') . "</td><td>" . utf8_decode_seguro('NÂ° 191-1466829-1-62') . "</td></tr>";
        } else {
            $xls .= "<tr><td>" . utf8_decode_seguro('Cta. Cte. en Soles') . "</td><td>" . utf8_decode_seguro('NÂ°  191-1435467-0-65') . "</td></tr>
        <tr><td>" . utf8_decode_seguro('Cta. Cte. en DÃ³lares') . "</td><td>" . utf8_decode_seguro('NÂ° 191-1466829-1-62') . "</td></tr>";
        }

        $xls .= "<tr><td>Tiempo de Entrega</td><td>$tiempo_entrega</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Lugar de Entrega</td><td>" . utf8_decode_seguro($lugar_entrega) . "</td></tr>";
        $xls .= "<tr><td>Validez de la Oferta</td><td>" . utf8_decode_seguro($validez) . "</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Contacto</td><td>" . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')) . "</td></tr>";

        $xls .= "</table>";

        return $xls;
    }

    public function presupuesto_ver_pdf_correo($codigo, $img) {


    	$datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
    	$tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
    	$serie = $datos_presupuesto[0]->PRESUC_Serie;
    	$numero = $datos_presupuesto[0]->PRESUC_Numero;
    	$codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
    	$codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '-' . $this->getOrderNumeroSerie($numero) : 'Nro. ' . $numero));
    	$cliente = $datos_presupuesto[0]->CLIP_Codigo;
    	$subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
    	$descuento = $datos_presupuesto[0]->PRESUC_descuento;
    	$igv = $datos_presupuesto[0]->PRESUC_igv;
    	$igv100 = $datos_presupuesto[0]->PRESUC_igv100;
    	$descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
    	$total = $datos_presupuesto[0]->PRESUC_total;
    	$observacion = $datos_presupuesto[0]->PRESUC_Observacion;
    	$tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
    	$fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
    	$lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
    	$tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
    	$garantia = $datos_presupuesto[0]->PRESUC_Garantia;
    	$validez = $datos_presupuesto[0]->PRESUC_Validez;
    	$contacto = $datos_presupuesto[0]->PERSP_Codigo;
    	$area = $datos_presupuesto[0]->AREAP_Codigo;
    	$vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
    	$vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
    	$modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
    	
    	$forma_pago = '';
    	if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
    		$datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
    		$forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
    	}
    	$datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
    	$empresa = $datos_cliente[0]->EMPRP_Codigo;
    	$persona = $datos_cliente[0]->PERSP_Codigo;
    	$tipo = $datos_cliente[0]->CLIC_TipoPersona;
    	
    	
    	$datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
    	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
    	$moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
    	
    	$temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
    	$nombre_cliente = $temp['nombre'];
    	$ruc = $temp['numdoc'];
    	$direccion = $temp['direccion'];
    	$telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
    	$fax = $temp['fax'];
    	$email = $temp['email'];
    	
    	
    	$nombre_contacto = $nombre_cliente;
    	if ($contacto != '' && $contacto != '0') {
    		$datos_persona = $this->persona_model->obtener_datosPersona($contacto);
    		if (count($datos_persona) > 0)
    			$nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
    	}
    	$nombre_area = '';
    	if ($area != '' && $area != '0') {
    		$datos_area = $this->area_model->obtener_area($area);
    		if (count($datos_area) > 0)
    			$nombre_area = $datos_area[0]->AREAC_Descripcion;
    	}
    	$vendedor_nombre = '';
    	if ($vendedor_persona != '' && $vendedor_persona != '0') {
    		$datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
    		if (count($datos_persona) > 0)
    			$vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
    	}
    	$vendedor_nombre_area = '';
    	if ($vendedor_area != '' && $vendedor_area != '0') {
    		$datos_area = $this->area_model->obtener_area($vendedor_area);
    		if (count($datos_area) > 0)
    			$vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
    	}
    	$filter = new stdClass();
    	$filter->TIPCAMC_Fecha = date('Y-m-d', time());
    	$filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
    	$data_tipocambio = $this->tipocambio_model->buscar($filter);
    	$tipo_cambio = '';
    	if (count($data_tipocambio) > 0)
    		$tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;
    	
    		$detalle_presupuesto = $this->obtener_lista_detalles($codigo);
    		 
    		$this->cezpdf = new Cezpdf('a4');
    		//$this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/documentos/presupuestformato.jpg'));
    		$this->cezpdf->ezImage("images/documentos/presupuestformato.jpg", -28, 590, 'none', 'left');
    		$datacreator = array(
    				'Title' => 'Estadillo de ',
    				'Name' => 'Estadillo de ',
    				'Author' => 'gian carlos',
    				'Subject' => 'PDF con Tablas',
    				'Creator' => 'ccapaempresas.com',
    				'Producer' => 'ccapaempresas.com'
    		);
    		 
    		 
    		//$this->cezpdf->ezText('', '', array("leading" => 100));
    		$this->cezpdf->ezText(utf8_decode_seguro('COTIZACION'), 12, array("leading" => -710, 'left' => 420));
    		$this->cezpdf->ezText($codificacion . '</b>', 15, array("leading" => 25, 'left' => 420));
    		$this->cezpdf->ezText('', '', array("leading" => 35));
    	
    		/* Datos del cliente */
    		$db_data = array(array('cols1' => '','cols2' => utf8_decode_seguro('Se?r(es)'), 'cols3' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols4' => 'R.U.C.', 'cols5' => ': ' . $ruc . '   Fecha: ' . $fecha),
    				array('cols1' => '','cols2' => utf8_decode_seguro('Direcci?'), 'cols3' => ': ' . utf8_decode_seguro($direccion), 'cols4' => 'Vend.', 'cols5' => ': ' . $vendedor_nombre), //NOMBRE DEL VENDEDOR
    				array('cols1' => '','cols2' => utf8_decode_seguro('Atenci? Sr(a)'), 'cols3' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols4' => '', 'cols5' => ''),
    				array('cols1' => '','cols2' => utf8_decode_seguro('Tel?ono'), 'cols3' => ': ' . $telefono, 'cols4' => 'E-mail', 'cols5' => ': ' . $email)
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
    	
    		/* Listado de detalles */
    		$db_data = array();
    		foreach ($detalle_presupuesto as $indice => $valor) {
    			$producto = $valor->PROD_Codigo;
    			$marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
    			$db_data[] = array(
    					'cols1' => $indice + 1,
    					'cols2' => $marca_prod[0]->MARCC_Descripcion,
    					'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
    					'cols4' => $valor->UNDMED_Simbolo,
    					'cols5' => $valor->PRESDEC_Cantidad,
    					'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
    					'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
    			);
    		}
    		$col_names = array(
    				'cols1' => 'Item',
    				'cols2' => 'Marca',
    				'cols3' => utf8_decode_seguro('Descripci?'),
    				'cols4' => 'Uni.',
    				'cols5' => 'Cant.',
    				'cols6' => 'Precio Uni.',
    				'cols7' => 'Precio Total'
    		);
    	
    		$this->cezpdf->ezTable($db_data, $col_names, '', array(
    				'width' => 500,
    				'showLines' => 1,
    				'shaded' => 0,
    				'showHeadings' => 1,
    				'xPos' => '320',
    				'fontSize' => 8,
    				'cols' => array(
    						'cols1' => array('width' => 30, 'justification' => 'center'),
    						'cols2' => array('width' => 60, 'justification' => 'left'),
    						'cols3' => array('width' => 200, 'justification' => 'left'),
    						'cols4' => array('width' => 30, 'justification' => 'left'),
    						'cols5' => array('width' => 30, 'justification' => 'left'),
    						'cols6' => array('width' => 50, 'justification' => 'right'),
    						'cols7' => array('width' => 60, 'justification' => 'right')
    				)
    		));
    	
    		$this->cezpdf->ezText('', '');
    	
    		/* Totales */
    		if ($tipo_docu != 'B') {
    			$db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
    					array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
    					array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
    			);
    		} else {
    			$db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
    					array('cols0' => '', 'cols1' => '', 'cols2' => '')
    			);
    		}
    	
    		$this->cezpdf->ezTable($db_data, "", "", array(
    				'width' => 525,
    				'showLines' => 0,
    				'shaded' => 0,
    				'showHeadings' => 0,
    				'xPos' => '320',
    				'fontSize' => 8,
    				'cols' => array(
    						'cols0' => array('width' => 330, 'justification' => 'left'),
    						'cols1' => array('width' => 70, 'justification' => 'left'),
    						'cols2' => array('width' => 60, 'justification' => 'right')
    				)
    		));
    	
    		$vendedor_nombre = '';
    		if ($vendedor_persona != '' && $vendedor_persona != '0') {
    			$datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
    			if (count($datos_persona) > 0)
    				$vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
    		}
    		$vendedor_nombre_area = '';
    		if ($vendedor_area != '' && $vendedor_area != '0') {
    			$datos_area = $this->area_model->obtener_area($vendedor_area);
    			if (count($datos_area) > 0)
    				$vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
    		}
    		/* Condiciones de venta */
    		$db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
    				array('cols0' => utf8_decode_seguro('Tipo de Cambio del D?'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
    				array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
    				array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
    				array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
    				array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
    				array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
    				//array('cols0' => utf8_decode_seguro('Garantía'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
    				array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
    				array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : ''))),
    				array('cols0' => 'Observaci?', 'cols1' => ': ' . utf8_decode_seguro($observacion))
    		);
    	
    		$this->cezpdf->ezText('', 15);
    		$this->cezpdf->ezTable($db_data, "", "", array(
    				'width' => 525,
    				'showLines' => 0,
    				'shaded' => 0,
    				'showHeadings' => 0,
    				'xPos' => '360',
    				'fontSize' => 8,
    				'cols' => array(
    						'cols0' => array('width' => 120, 'justification' => 'left'),
    						'cols1' => array('width' => 415, 'justification' => 'left'),
    				)
    		));
    	
    		$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
    		return $this->cezpdf->ezOutput($cabecera);
    	

        ///////////////
    }

    public function presupuesto_modificar() {
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $tipo_codificacion = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;

        switch ($tipo_codificacion) {
            case '2':
                if ($this->input->post('serie') == '')
                    exit('{"result":"error", "campo":"serie"}');
                if ($this->input->post('numero') == '')
                    exit('{"result":"error", "campo":"numero"}');
                break;
            case '3':
                if ($this->input->post('codigo_usuario') == '')
                    exit('{"result":"error", "campo":"codigo_usuario"}');
                break;
        }
        if ($this->input->post('cliente') == '')
            exit('{"result":"error", "campo":"ruc_cliente"}');
        if ($this->input->post('moneda') == '' || $this->input->post('moneda') == '0')
            exit('{"result":"error", "campo":"moneda"}');
        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');

        $codigo = $this->input->post('codigo');
        $tipo_docu = $this->input->post('tipo_docu');

        $filter = new stdClass();
        $filter->PERSP_Codigo = NULL;
        $filter->AREAP_Codigo = NULL;
        if ($this->input->post('contacto') != '' && $this->input->post('contacto') != '0') {
            $temp = explode('-', $this->input->post('contacto'));
            $filter->PERSP_Codigo = $temp[0];
            $filter->AREAP_Codigo = $temp[1];
        }
        $filter->PRESUC_VendedorPersona = NULL;
        $filter->PRESUC_VenedorArea = NULL;
        if ($this->input->post('vendedor') != '' && $this->input->post('vendedor') != '0') {
            $temp = explode('-', $this->input->post('vendedor'));
            $filter->PRESUC_VendedorPersona = $temp[0];
            $filter->PRESUC_VenedorArea = $temp[1];
        }
        $filter->PRESUC_LugarEntrega = $this->input->post('lugar_entrega');
        $filter->PRESUC_TiempoEntrega = $this->input->post('tiempo_entrega');
        $filter->PRESUC_Garantia = $this->input->post('garantia');
        $filter->PRESUC_Validez = $this->input->post('validez');
        $filter->FORPAP_Codigo = NULL;
        if ($this->input->post('forma_pago') != '' && $this->input->post('forma_pago') != '0')
            $filter->FORPAP_Codigo = $this->input->post('forma_pago');
        $filter->PRESUC_Observacion = strtoupper($this->input->post('observacion'));
        $filter->PRESUC_Fecha = human_to_mysql($this->input->post('fecha'));
        $filter->PRESUC_Serie = NULL;
        if ($this->input->post('serie'))
            $filter->PRESUC_Serie = $this->input->post('serie');
        $filter->PRESUC_Numero = NULL;
        if ($this->input->post('numero'))
            $filter->PRESUC_Numero = $this->input->post('numero');
        $filter->PRESUC_CodigoUsuario = NULL;
        if ($this->input->post('codigo_usuario'))
            $filter->PRESUC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->MONED_Codigo = $this->input->post('moneda');
        $filter->CLIP_Codigo = $this->input->post('cliente');
        $filter->PRESUC_ModoImpresion = '1';
        if ($this->input->post('modo_impresion') != '0' && $this->input->post('modo_impresion') != '')
            $filter->PRESUC_ModoImpresion = $this->input->post('modo_impresion');
        $filter->PRESUC_FlagEstado = $this->input->post('estado');

        $filter->PRESUC_descuento100 = $this->input->post('descuento');
        $filter->PRESUC_igv100 = $this->input->post('igv');

        //if ($tipo_docu != 'B') {
        $filter->PRESUC_subtotal = $this->input->post('preciototal');
        $filter->PRESUC_descuento = $this->input->post('descuentotal');
        $filter->PRESUC_igv = $this->input->post('igvtotal');
        /* } else {
          $filter->PRESUC_subtotal_conigv = $this->input->post('preciototal_conigv');
          $filter->PRESUC_descuento_conigv = $this->input->post('descuentotal_conigv');
          } */
        $filter->PRESUC_total = $this->input->post('importetotal');

        if ($this->input->post('cliente') == 144 ||
                $this->input->post('cliente') == 135 ||
                $this->input->post('cliente') == 218 ||
                $this->input->post('cliente') == 1037
        )
            $filter->PRESUC_NombreAuxiliar = strtoupper($this->input->post('nombre_cliente'));


        $this->presupuesto_model->modificar_presupuesto($codigo, $filter);

        $flagBS = $this->input->post('flagBS');
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        // if ($tipo_docu != 'B') {
        $prodpu = $this->input->post('prodpu');
        $prodprecio = $this->input->post('prodprecio');
        $proddescuento = $this->input->post('proddescuento');
        $prodigv = $this->input->post('prodigv');
        /* } else {
          $prodprecio_conigv = $this->input->post('prodprecio_conigv');
          $proddescuento_conigv = $this->input->post('proddescuento_conigv');
          } */
        $prodimporte = $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad = $this->input->post('produnidad');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $prodigv100 = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $proddescri = $this->input->post('proddescri');

        if (is_array($detacodi)) {
            foreach ($detacodi as $indice => $valor) {
                $detalle_accion = $detaccion[$indice];

                $filter = new stdClass();
                $filter->PRESUP_Codigo = $codigo;
                $filter->PROD_Codigo = $prodcodigo[$indice];
                if ($produnidad[$indice] == '' || $produnidad[$indice] == "null")
                    $produnidad[$indice] = NULL;
                //if ($flagBS[$indice] == 'B')
                $filter->UNDMED_Codigo = $produnidad[$indice];
                $filter->PRESDEC_Cantidad = $prodcantidad[$indice];
                //if ($tipo_docu != 'B') {
                $filter->PRESDEC_Pu = $prodpu[$indice];
                $filter->PRESDEC_Subtotal = $prodprecio[$indice];
                $filter->PRESDEC_Descuento = $proddescuento[$indice];
                $filter->PRESDEC_Igv = $prodigv[$indice];
                /* } else {
                  $filter->PRESDEC_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                  $filter->PRESDEC_Descuento_ConIgv = $proddescuento_conigv[$indice];
                  } */
                $filter->PRESDEC_Total = $prodimporte[$indice];
                $filter->PRESDEC_Pu_ConIgv = $prodpu_conigv[$indice];
                $filter->PRESDEC_Descuento100 = $proddescuento100[$indice];
                $filter->PRESDEC_Igv100 = $prodigv100[$indice];
                $filter->PRESDEC_Descripcion = strtoupper($proddescri[$indice]);
                $filter->PRESDEC_Observacion = "";

                if ($detalle_accion == 'n') {
                    $this->presupuestodetalle_model->insertar($filter);
                } elseif ($detalle_accion == 'm') {
                    $this->presupuestodetalle_model->modificar($valor, $filter);
                } elseif ($detalle_accion == 'e') {
                    $this->presupuestodetalle_model->eliminar($valor);
                }
            }
        }
        exit('{"result":"ok", "codigo":"' . $codigo . '"}');
    }

    public function presupuesto_eliminar() {
        

        $presupuesto = $this->input->post('presupuesto');
        $this->presupuesto_model->eliminar_presupuesto($presupuesto);

        $presupuesto_id = $this->input->post();
    }

    /*
      $guiarem_id = $this->input->post('codigo');

      $guiarem = $this->guiarem_model->obtener($guiarem_id);

      $guiasa_id = $guiarem[0]->GUIASAP_Codigo;

      $this->guiaremdetalle_model->eliminar2($guiarem_id);

      $this->guiarem_model->eliminar($guiarem_id);

      $this->guiasadetalle_model->eliminar2($guiasa_id);

      $this->guiasa_model->eliminar($guiasa_id);

      echo true;
     */

    public function presupuesto_buscar() {
        
    }

    public function obtener_lista_detalles($codigo) {
        $detalle = $this->presupuestodetalle_model->listar($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->PRESDEP_Codigo;

                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->PRESDEC_Cantidad;
                $pu = $valor->PRESDEC_Pu;
                $subtotal = $valor->PRESDEC_Subtotal;
                $igv = $valor->PRESDEC_Igv;
                $descuento = $valor->PRESDEC_Descuento;
                $total = $valor->PRESDEC_Total;
                $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                $subtotal_conigv = $valor->PRESDEC_Subtotal_ConIgv;
                $descuento_conigv = $valor->PRESDEC_Descuento_ConIgv;
                $descuento100 = $valor->PRESDEC_Descuento100;
                $igv100 = $valor->PRESDEC_Igv100;
                $observacion = $valor->PRESDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = ($valor->PRESDEC_Descripcion != '' ? $valor->PRESDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\', '', $nombre_producto);
                $marca = "";
                if ($datos_producto[0]->MARCP_Codigo != '0' && $datos_producto[0]->MARCP_Codigo != '1') {
                    $datos_marca = $this->marca_model->getMarca($datos_producto[0]->MARCP_Codigo);
                    if (count($datos_marca) > 0)
                        $marca = $datos_marca[0]->MARCC_Descripcion;
                }

                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $modelo = $datos_producto[0]->PROD_Modelo;
                $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : '';
                $objeto = new stdClass();
                $objeto->PRESDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->MARCC_Descripcion = $marca;
                $objeto->PROD_Modelo = $modelo;
                $objeto->PRESDEC_Cantidad = $cantidad;
                $objeto->PRESDEC_Pu = $pu;
                $objeto->PRESDEC_Subtotal = $subtotal;
                $objeto->PRESDEC_Descuento = $descuento;
                $objeto->PRESDEC_Igv = $igv;
                $objeto->PRESDEC_Total = $total;
                $objeto->PRESDEC_Pu_ConIgv = $pu_conigv;
                $objeto->PRESDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->PRESDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->PRESDEC_Descuento100 = $descuento100;
                $objeto->PRESDEC_Igv100 = $igv100;
                $objeto->PRESDEC_Observacion = $observacion;
                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }

    public function presupuesto_ver_pdf($codigo, $img) {
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat
                //$this->presupuesto_ver_pdf_formato($codigo);
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo);
                break;
            case 2:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->presupuesto_ver_pdf_formato4($codigo);
                break;
            default:
                presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img);
                break;
        }
    }

    public function presupuesto_ver_xls($codigo) {
//PRESUPUESTO EXCEL VENTA COTIZACION
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario :
                ($serie != '' ? $serie . ' - ' . $this->getOrderNumeroSerie($numero) : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $xls = "<table><tr><td colspan=7 align=center>" . utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b></td></tr></table>';


        $xls .= "<table>
		<tr><td>" . utf8_decode_seguro('SeÃ±or(es) :') . "
        </td><td>" . utf8_decode("$nombre_cliente") . " 

        </td><td></td><td>R.U.C. : </td><td>$ruc
        </td><td>Fecha : </td><td>$fecha</td></tr>

		<tr><td>" . utf8_decode_seguro('DirecciÃ³n :') . " 
        </td><td>" . utf8_decode_seguro("$direccion") . "

        </td><td></td><td>Vend:
        </td><td>" . utf8_decode_seguro("$vendedor_nombre") . "</td><td></td></tr>

		<tr><td>" . utf8_decode_seguro('AtenciÃ³n Sr(a) :') . " 
        </td><td>" . utf8_decode_seguro("$nombre_contacto ") . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '') . "</td><td></td>&nbsp;&nbsp;&nbsp;<td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td>" . utf8_decode_seguro('TelÃ©fono : ') . "</td><td>$telefono</td><td></td><td></td><td></td><td>E-mail :</td><td>$email</td></tr>
		</table><br><br>
		";

        $date = date('Y-m-d') . '-' . $ruc . '-Presupuesto';
        header('Content-Disposition: attachment; filename="' . $date . '.xls"');
        header("Content-Type: application/vnd.ms-excel");

        $extra = "<th>Marca</th>";
        if (FORMATO_IMPRESION == 3) {
            $extra = "<th>Codigo</th><th>Marca</th><th>Modelo</th>";
        }

        $xls .= "
		<table border=1>
			<tr><th>Item</th>
      $extra
      <th>" . utf8_decode_seguro('DescripciÃ³n') . "</th>
      <th>Uni.</th>
      <th>Cant.</th>
      <th>Precio Uni.</th>
      <th>Precio Total</th></tr>
		";

        foreach ($detalle_presupuesto as $indice => $valor) {

            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($valor->PROD_Codigo);

            $xls .= "<tr>
      <td>" . ($indice + 1) . "</td>";
            if (FORMATO_IMPRESION == 3) {
                $xls .= "<td>" . $valor->PROD_CodigoUsuario . "</td>";
                $xls .= "<td>" . $marca_prod[0]->MARCC_Descripcion . "</td>";
                $xls .= "<td>" . $valor->PROD_Modelo . "</td>";
            } else {
                if (isset($marca_prod[0]->MARCC_Descripcion))
                    $xls .= "<td>" . $marca_prod[0]->MARCC_Descripcion . "</td>";
                else
                    $xls .= "<td></td>";
            }

            $xls .= "
      <td>" . utf8_decode_seguro($valor->PROD_Nombre) . "</td>
      <td>" . $valor->UNDMED_Simbolo . "</td>
      <td>" . $valor->PRESDEC_Cantidad . "</td>
      <td>" . number_format(($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2) . "</td>
      <td>" . number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2) . "</td></tr>";
        }

        $xls .= "</table><br><br>";


        if (FORMATO_IMPRESION == 3) {
            $subtotal_text = "SUBTOTAL";
            $igv_text = "IGV";
        } else {
            $subtotal_text = "VALOR VENTA";
            $igv_text = "IMPUESTO";
        }
        $xls .= "
		 <table>
			<tr><td colspan=5><b>SON : " . strtoupper(num2letras(round($total, 2))) . " $moneda_nombre</td>
      <td><b>$subtotal_text</b></td><td><b>" . number_format($subtotal, 2) . "</b></td></tr>
			<tr><td colspan=5></td><td><b>$igv_text</b></td><td><b>" . number_format($igv, 2) . "</b></td></tr>
			<tr><td colspan=5></td><td><b>TOTAL $moneda_simbolo</b></td><td><b>" . number_format($total, 2) . "</b></td></tr>
		</table>
		";


        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }


        $xls .= "
			<table>
			<tr><td colspan=2><b>CONDICIONES DE VENTA:</b></td></tr>";
        if (FORMATO_IMPRESION != 3) {
            $xls .= "<tr><td>" . utf8_decode_seguro('Tipo de Cambio del DÃ­a :') . "</td><td>" . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '') . "</td></tr>
        <tr><td>Moneda</td><td>$moneda_nombre</td></tr>";
        }
        $xls .= "<tr><td>Forma de Pago</td><td>" . utf8_decode_seguro($forma_pago) . "</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Los Precios de los Productos</td><td>" . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV') . "</td></tr>";

        if (FORMATO_IMPRESION == 3) {
            $xls .= "<tr><td>" . utf8_decode_seguro('Banco de CrÃ©dito Soles') . "</td><td>" . utf8_decode_seguro('NÂ°  191-1435467-0-65') . "</td></tr>
        <tr><td>" . utf8_decode_seguro('Banco de CrÃ©dito DÃ³lares') . "</td><td>" . utf8_decode_seguro('NÂ° 191-1466829-1-62') . "</td></tr>";
        } else {
            $xls .= "<tr><td>" . utf8_decode_seguro('Cta. Cte. en Soles') . "</td><td>" . utf8_decode_seguro('NÂ°  191-1435467-0-65') . "</td></tr>
        <tr><td>" . utf8_decode_seguro('Cta. Cte. en DÃ³lares') . "</td><td>" . utf8_decode_seguro('NÂ° 191-1466829-1-62') . "</td></tr>";
        }

        $xls .= "<tr><td>Tiempo de Entrega</td><td>$tiempo_entrega</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Lugar de Entrega</td><td>" . utf8_decode_seguro($lugar_entrega) . "</td></tr>";
        $xls .= "<tr><td>Validez de la Oferta</td><td>" . utf8_decode_seguro($validez) . "</td></tr>";
        if (FORMATO_IMPRESION != 3)
            $xls .= "<tr><td>Contacto</td><td>" . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')) . "</td></tr>";

        $xls .= "</table>";

        $data['xls'] = $xls;
        $this->load->view('ventas/presupuesto_ver_xls', $data);
    }

    public function presupuesto_ver_pdf_formato1($codigo) {

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($empresa, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');

        $this->cezpdf = new Cezpdf('a4');
        /* Para las imagenes */

        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $valor->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 230, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
            array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
            array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 405, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 50, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => utf8_decode_seguro('Cta. Cte. en Soles'), 'cols1' => ': ' . utf8_decode_seguro('NÂ°  191-1435467-0-65')),
            array('cols0' => utf8_decode_seguro('Cta. Cte. en DÃ³lares'), 'cols1' => ': ' . utf8_decode_seguro('NÂ° 191-1466829-1-62')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_formato2($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
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
        /* Para las imagenes */

        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $valor->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_formato4($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
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
        /* Para las imagenes */

        $delta = 20;

        $this->cezpdf->ezText('', '', array("leading" => 100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $producto = $valor->PROD_Codigo;
            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $marca_prod[0]->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_conmenbrete($codigo, $img) {
        switch (FORMATO_IMPRESION) {
            case 1:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
            case 2:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img);
                break;
            case 4:  //Formato para ferremax
                $this->presupuesto_ver_pdf_conmenbrete_formato4($codigo);
                break;
            case 5:
                if ($_SESSION['compania'] == "1") {
                    $this->presupuesto_ver_pdf_conmenbrete_formato5($codigo); //Formato para CYG
                } else {
                    $this->presupuesto_ver_pdf_conmenbrete_formato6($codigo); //Formato para CYG ELECTRO DATA
                }
                break;
            case 6:
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img); //Formato para CYL
                break;
            default:
                presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
        }
    }

    public function presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img) {


        //////SIN DOCUMENTO  STV
        

    	$datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
    	$tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
    	$serie = $datos_presupuesto[0]->PRESUC_Serie;
    	$numero = $datos_presupuesto[0]->PRESUC_Numero;
    	$codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
    	$codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '-' . $this->getOrderNumeroSerie($numero) : 'Nro. ' . $numero));
    	$cliente = $datos_presupuesto[0]->CLIP_Codigo;
    	$subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
    	$descuento = $datos_presupuesto[0]->PRESUC_descuento;
    	$igv = $datos_presupuesto[0]->PRESUC_igv;
    	$igv100 = $datos_presupuesto[0]->PRESUC_igv100;
    	$descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
    	$total = $datos_presupuesto[0]->PRESUC_total;
    	$observacion = $datos_presupuesto[0]->PRESUC_Observacion;
    	$tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
    	$fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
    	$lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
    	$tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
    	$garantia = $datos_presupuesto[0]->PRESUC_Garantia;
    	$validez = $datos_presupuesto[0]->PRESUC_Validez;
    	$contacto = $datos_presupuesto[0]->PERSP_Codigo;
    	$area = $datos_presupuesto[0]->AREAP_Codigo;
    	$vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
    	$vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
    	$modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
    	
    	$forma_pago = '';
    	if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
    		$datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
    		$forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
    	}
    	$datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
    	$empresa = $datos_cliente[0]->EMPRP_Codigo;
    	$persona = $datos_cliente[0]->PERSP_Codigo;
    	$tipo = $datos_cliente[0]->CLIC_TipoPersona;
    	
    	
    	$datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
    	$moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
    	$moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
    	
    	$temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
    	$nombre_cliente = $temp['nombre'];
    	$ruc = $temp['numdoc'];
    	$direccion = $temp['direccion'];
    	$telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
    	$fax = $temp['fax'];
    	$email = $temp['email'];
    	
    	
    	$nombre_contacto = $nombre_cliente;
    	if ($contacto != '' && $contacto != '0') {
    		$datos_persona = $this->persona_model->obtener_datosPersona($contacto);
    		if (count($datos_persona) > 0)
    			$nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
    	}
    	$nombre_area = '';
    	if ($area != '' && $area != '0') {
    		$datos_area = $this->area_model->obtener_area($area);
    		if (count($datos_area) > 0)
    			$nombre_area = $datos_area[0]->AREAC_Descripcion;
    	}
    	$vendedor_nombre = '';
    	if ($vendedor_persona != '' && $vendedor_persona != '0') {
    		$datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
    		if (count($datos_persona) > 0)
    			$vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
    	}
    	$vendedor_nombre_area = '';
    	if ($vendedor_area != '' && $vendedor_area != '0') {
    		$datos_area = $this->area_model->obtener_area($vendedor_area);
    		if (count($datos_area) > 0)
    			$vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
    	}
    	$filter = new stdClass();
    	$filter->TIPCAMC_Fecha = date('Y-m-d', time());
    	$filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
    	$data_tipocambio = $this->tipocambio_model->buscar($filter);
    	$tipo_cambio = '';
    	if (count($data_tipocambio) > 0)
    		$tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;
    	$detalle_presupuesto = $this->obtener_lista_detalles($codigo);
    	   /*$this->cezpdf = new Cezpdf('a4');
           $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/documentos/presupuestformato.jpg'));

    		$cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
    		$this->cezpdf->ezStream($cabecera);*/
           //$pdf = new FPDF('L','mm',array(100,50));
            $pdf = new Plantilla();
            //$pdf->AliasPages();
            $pdf->AddPage();

            /*$pdf->SetFillColor(232,232,232);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(70,6,'ESTADO',1,0,'C',1);
            $pdf->Cell(20,6,'ID',1,0,'C',1);
            $pdf->Cell(70,6,'MUNICIPIO',1,1,'C',1);*/

            $pdf->SetFont('Arial','',10);
            $pdf->Cell(19,8,'Lima     '.substr($fecha,0,2),0,0,'L');
            $pdf->Cell(28,8,'de   '.mes_textual(substr($fecha,3,2)),0,0,'L');
            $pdf->Cell(30,8,'del  '.substr($fecha,6,4),0,1,'L');

            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(19,8,utf8_decode('Señr(es)'),0,1,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->SetX(25);
            $pdf->Cell(100,8,utf8_decode($nombre_cliente),0,1,'L');
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(19,8,utf8_decode('Presente'),0,1,'L');
            $pdf->Cell(19,8,utf8_decode('Ref :  '.' Cotizacion'),0,1,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->SetX(25);
            $pdf->Cell(100,8,utf8_decode($codificacion),0,1,'L');
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(19,8,utf8_decode('Estimado Señores'),0,1,'L');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(19,8,utf8_decode('A continuacion nuestra cotizacion.'),0,1,'L');
            $pdf->Cell(19,8,utf8_decode('1.0 Precios, terminos y condiciones'),0,1,'L');
            $pdf->SetX(17);
            $pdf->Cell(19,8,'De acuerdo al siguiente cuadro:',0,1,'L');
            $y=$pdf->GetY();
            $pdf->SetY($y+10);
            $pdf->SetFillColor(232,232,232);
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(15,6,'ITEM',1,0,'C',1);
            $pdf->Cell(90,6,utf8_decode('DESCRIPCIÓN'),1,0,'C',1);
            $pdf->Cell(20,6,'CODIGO',1,0,'C',1);
            $pdf->Cell(15,6,'CANT.',1,0,'C',1);
            $pdf->Cell(25,6,'PRECIO UNIT.',1,0,'C',1);
            $pdf->Cell(25,6,'PRECIO TOTAl.',1,1,'C',1);
            foreach ($detalle_presupuesto as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(15,6,$indice + 1,1,0,'C');
                $pdf->Cell(90,6,utf8_decode($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),1,0,'L');
                $pdf->Cell(20,6,$marca_prod[0]->PROD_CodigoUsuario,1,0,'C');
                $pdf->Cell(15,6,$valor->PRESDEC_Cantidad,1,0,'C');
                $pdf->Cell(25,6,number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),1,0,'C');
                $pdf->Cell(25,6,number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),1,1,'C');
            }
            //$pdf->Cell(55,6,'CODIGO'.' :  hola' ,1,1,'L');
            $pdf->SetFont('Arial','',8);
            $y=$pdf->GetY();
            $pdf->SetY($y+10);
            $pdf->Cell(138,6,'           SON : '.strtoupper(num2letras(round($total, 2))). ' ' . $moneda_nombre,0,0,'L');
            $pdf->Cell(25,6,'VALOR VENTA  ',0,0,'L');
            $pdf->Cell(25,6,number_format($subtotal, 2),0,1,'R');
            $pdf->SetX(148);
            $pdf->Cell(25,6,'IMPUESTOS',0,0,'L');
            $pdf->Cell(25,6,number_format($igv, 2),0,1,'R');
            $pdf->SetX(148);
            $pdf->Cell(25,6,'TOTAL   '. $moneda_simbolo ,0,0,'L');
            $pdf->Cell(25,6,number_format($total, 2),0,1,'R');

            $pdf->SetFont('Arial','',12);
            $y=$pdf->GetY();
            $pdf->SetY($y+10);
            $pdf->Cell(19,8,utf8_decode('2.0 Validez de la oferta'),0,1,'L');
            $pdf->SetX(20);
            $pdf->MultiCell(180,5,'Para todos sus efectos esta oferta es valida para una orden de compra recibida durante los proximo 20 dias',0,'L');
            $pdf->Cell(19,8,utf8_decode('3.0 Forma de pago'),0,1,'L');
            $pdf->SetX(20);
            $pdf->Cell(25,6,utf8_decode_seguro($forma_pago),0,1,'L');
            $pdf->SetX(20);
            $pdf->Cell(19,8,utf8_decode('3.1 Condicion de entrega'),0,1,'L');
            $pdf->SetX(28);
            $pdf->Cell(25,8,'Plazo :  '.$tiempo_entrega,0,1,'L');
            $pdf->SetX(35);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(25,8,'Datos para la Orden de compra',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Razon Social ',0,0,'L');
            $pdf->Cell(80,8,':  S.E.I MONTOYA S.A.C',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'RUC ',0,0,'L');
            $pdf->Cell(80,8,':  20600417011',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Direccion ',0,0,'L');
            $pdf->Cell(80,8,':  Mza. L, Lote 4, Grupo 3 Sector9 Lima-Lima-VIilla El Salvador',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Cuenta BCP S/. ',0,0,'L');
            $pdf->Cell(80,8,':  001-2001750-0-36',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Cuenta BCP S/. ',0,0,'L');
            $pdf->Cell(80,8,':  S.E.I MONTOYA S.A.C',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Interbancario BCP S/. ',0,0,'L');
            $pdf->Cell(80,8,':  194-20000006-0-36',0,1,'L');
            $pdf->SetX(40);
            $pdf->Cell(50,8,'Interbancario BCP USD. ',0,0,'L');
            $pdf->Cell(80,8,':  000-190-002081756036-90',0,1,'L');
            $y=$pdf->GetY();
            $pdf->SetY($y+10);
            $pdf->Cell(80,8,'Muy atentamente',0,1,'L');
            
            $pdf->Output();
        ///////////////
    }

    public function presupuesto_ver_pdf_conmenbrete_formato2($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/jimmyplast_fondo_presupuesto.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

        $this->cezpdf->addInfo($datacreator);
        /* Para las imagenes */
        $this->cezpdf->ezImage("images/img_db/jimmyplast_cabe.jpg", -10, 555, 'none', 'left');

        $delta = 20;

        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $valor->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img) {

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $vendedor = $datos_presupuesto[0]->USUA_Codigo;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;

        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;

        $temp = $this->usuario_model->obtener($vendedor);
        $temp = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo);
        $vendedor = $temp[0]->PERSC_Nombre . ' ' . $temp[0]->PERSC_ApellidoPaterno . ' ' . $temp[0]->PERSC_ApellidoMaterno;

        $datos_compania = $this->compania_model->obtener_compania($this->somevar['compania']);
        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo);

        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        echo $cliente;
        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_contacto = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_contacto) > 0)
                $nombre_contacto = $datos_contacto[0]->PERSC_Nombre . ' ' . $datos_contacto[0]->PERSC_ApellidoPaterno . ' ' . $datos_contacto[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            if (count($datos_formapago) > 0)
                $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;

        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $total_items = count($detalle_presupuesto);


        //$this->load->library('cezpdf_horizontal');
        //$this->load->helper('pdf_helper');
        //prep_pdf_horizontal();
        // $this->cezpdf_horizontal = new Cezpdf('a4', 'landscape');

        /* Para las imagenes */

        $delta = 20;
        var_dump($img);
        if ($img != 1) {

            $this->cezpdf_horizontal->ezImage("images/img_db/logo_instrume_unido.jpg", 0, 740, 'none', 'left');
        } else {
            $this->cezpdf_horizontal->ezImage("images/img_db/logo_instrume_unido1.jpg", 0, 740, 'none', 'left');
        }

        $this->cezpdf_horizontal->ezText(utf8_decode_seguro($datos_empresa[0]->EMPRC_RazonSocial), 8, array("left" => 15));
        $this->cezpdf_horizontal->ezText(utf8_decode_seguro('Lima: Av. Chorrillos NÂº 200 Chorrillos - Lima  Telf.: 251 6727  Fax: 252 7547'), 8, array("left" => 15));
        $this->cezpdf_horizontal->ezText('Cusco: Av. Garcilaso S/N C.C. La Salle Of. 143 Wanchaq - Cusco  Telf.: 84-253453  Telefax: 84-263225', 8, array("left" => 15));
        $this->cezpdf_horizontal->ezText('www.instrumentosysistemas.com ventas@instrumentosysistemas.com', 8, array("left" => 15));
        $this->cezpdf_horizontal->ezText(utf8_decode_seguro('<b>CotizaciÃ³n Nro.  ') . ($codigo_usuario != '' ? $codigo_usuario : $numero) . '</b>', 17, array("leading" => 30, "left" => 300));
        $this->cezpdf_horizontal->ezText('', '');


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc, 'cols5' => 'Fecha', 'cols6' => ': ' . $fecha, 'cols7' => ''),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . $direccion, 'cols3' => '', 'cols4' => '', 'cols5' => '', 'cols6' => '', 'cols7' => ''),
            array('cols1' => utf8_decode_seguro('Contacto'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => '', 'cols5' => '', 'cols6' => '', 'cols7' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email, 'cols5' => '', 'cols6' => '', 'cols7' => '')
        );
        $this->cezpdf_horizontal->ezTable($db_data, "", "", array(
            'width' => 740,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 300, 'justification' => 'left'),
                'cols3' => array('width' => 40, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 70, 'justification' => 'left'),
                'cols7' => array('width' => 70, 'justification' => 'left'),
            )
        ));

        $this->cezpdf_horizontal->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        $contador = 0;
        foreach ($detalle_presupuesto as $indice => $valor) {

            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($valor->PROD_Codigo);

            $valor->MARCC_Descripcion = $marca_prod[0]->MARCC_Descripcion;
            $contador++;


            if ($total_items > 11) {

                $sobrantes = $total_items % 11;
                $pagina = 1;
                $items_exceso = $total_items - 11;


                if ($contador == 11) {
                    $col_names = array(
                        'cols1' => 'Item',
                        'cols2' => utf8_decode_seguro('CÃ³digo'),
                        'cols3' => 'Marca',
                        'cols4' => 'Modelo',
                        'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                        'cols6' => 'Uni.',
                        'cols7' => 'Cant.',
                        'cols8' => 'Precio Uni.',
                        'cols9' => 'Precio Total'
                    );


                    $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                        'width' => 730,
                        'showLines' => 1,
                        'shaded' => 0,
                        'showHeadings' => 1,
                        'xPos' => 'center',
                        'fontSize' => 9,
                        'cols' => array(
                            'cols1' => array('width' => 30, 'justification' => 'center'),
                            'cols2' => array('width' => 70, 'justification' => 'center'),
                            'cols3' => array('width' => 75, 'justification' => 'left'),
                            'cols4' => array('width' => 75, 'justification' => 'left'),
                            'cols5' => array('width' => 250, 'justification' => 'left'),
                            'cols6' => array('width' => 50, 'justification' => 'left'),
                            'cols7' => array('width' => 50, 'justification' => 'right'),
                            'cols8' => array('width' => 60, 'justification' => 'right'),
                            'cols9' => array('width' => 70, 'justification' => 'right')
                        )
                    ));

                    $this->cezpdf_horizontal->ezText('', '');
                    $this->cezpdf_horizontal->ezNewPage();
                    $db_data = array();
                }

                if (($contador - 11 > 0) AND ( $contador - 11) % 25 == 0) {
                    $col_names = array(
                        'cols1' => 'Item',
                        'cols2' => utf8_decode_seguro('CÃ³digo'),
                        'cols3' => 'Marca',
                        'cols4' => 'Modelo',
                        'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                        'cols6' => 'Uni.',
                        'cols7' => 'Cant.',
                        'cols8' => 'Precio Uni.',
                        'cols9' => 'Precio Total'
                    );


                    $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                        'width' => 730,
                        'showLines' => 1,
                        'shaded' => 0,
                        'showHeadings' => 1,
                        'xPos' => 'center',
                        'fontSize' => 9,
                        'cols' => array(
                            'cols1' => array('width' => 30, 'justification' => 'center'),
                            'cols2' => array('width' => 70, 'justification' => 'center'),
                            'cols3' => array('width' => 75, 'justification' => 'left'),
                            'cols4' => array('width' => 75, 'justification' => 'left'),
                            'cols5' => array('width' => 250, 'justification' => 'left'),
                            'cols6' => array('width' => 50, 'justification' => 'left'),
                            'cols7' => array('width' => 50, 'justification' => 'right'),
                            'cols8' => array('width' => 60, 'justification' => 'right'),
                            'cols9' => array('width' => 70, 'justification' => 'right')
                        )
                    ));

                    $this->cezpdf_horizontal->ezText('', '');
                    $this->cezpdf_horizontal->ezNewPage();
                    $db_data = array();
                }


                if ($valor->PRESDEC_Pu_ConIgv != '')
                    $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                else
                    $pu_conigv = $valor->PRESDEC_Pu + $valor->PRESDEC_Pu * $valor->PRESDEC_Igv100 / 100;
                $db_data[] = array(
                    'cols1' => $contador,
                    'cols2' => $valor->PROD_CodigoUsuario,
                    'cols3' => utf8_decode_seguro($valor->MARCC_Descripcion),
                    'cols4' => utf8_decode_seguro($valor->PROD_Modelo),
                    'cols5' => utf8_decode_seguro($valor->PROD_Nombre),
                    'cols6' => $valor->UNDMED_Descripcion,
                    'cols7' => $valor->PRESDEC_Cantidad,
                    'cols8' => number_format($pu_conigv, 2),
                    'cols9' => number_format($valor->PRESDEC_Cantidad * $pu_conigv, 2)
                );

                if ($contador == $total_items AND $sobrantes > 0) {
                    $col_names = array(
                        'cols1' => 'Item',
                        'cols2' => utf8_decode_seguro('CÃ³digo'),
                        'cols3' => 'Marca',
                        'cols4' => 'Modelo',
                        'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                        'cols6' => 'Uni.',
                        'cols7' => 'Cant.',
                        'cols8' => 'Precio Uni.',
                        'cols9' => 'Precio Total'
                    );


                    $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                        'width' => 730,
                        'showLines' => 1,
                        'shaded' => 0,
                        'showHeadings' => 1,
                        'xPos' => 'center',
                        'fontSize' => 9,
                        'cols' => array(
                            'cols1' => array('width' => 30, 'justification' => 'center'),
                            'cols2' => array('width' => 70, 'justification' => 'center'),
                            'cols3' => array('width' => 75, 'justification' => 'left'),
                            'cols4' => array('width' => 75, 'justification' => 'left'),
                            'cols5' => array('width' => 250, 'justification' => 'left'),
                            'cols6' => array('width' => 50, 'justification' => 'left'),
                            'cols7' => array('width' => 50, 'justification' => 'right'),
                            'cols8' => array('width' => 60, 'justification' => 'right'),
                            'cols9' => array('width' => 70, 'justification' => 'right')
                        )
                    ));

                    $this->cezpdf_horizontal->ezText('', '');
                }
            } else {
                if ($total_items < 6) {

                    if ($valor->PRESDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->PRESDEC_Pu + $valor->PRESDEC_Pu * $valor->PRESDEC_Igv100 / 100;
                    $db_data[] = array(
                        'cols1' => $contador,
                        'cols2' => $valor->PROD_CodigoUsuario,
                        'cols3' => utf8_decode_seguro($valor->MARCC_Descripcion),
                        'cols4' => utf8_decode_seguro($valor->PROD_Modelo),
                        'cols5' => utf8_decode_seguro($valor->PROD_Nombre),
                        'cols6' => $valor->UNDMED_Simbolo,
                        'cols7' => $valor->PRESDEC_Cantidad,
                        'cols8' => number_format($pu_conigv, 2),
                        'cols9' => number_format($valor->PRESDEC_Cantidad * $pu_conigv, 2)
                    );

                    if ($contador == $total_items) {
                        $col_names = array(
                            'cols1' => 'Item',
                            'cols2' => utf8_decode_seguro('CÃ³digo'),
                            'cols3' => 'Marca',
                            'cols4' => 'Modelo',
                            'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                            'cols6' => 'Uni.',
                            'cols7' => 'Cant.',
                            'cols8' => 'Precio Uni.',
                            'cols9' => 'Precio Total'
                        );


                        $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                            'width' => 730,
                            'showLines' => 1,
                            'shaded' => 0,
                            'showHeadings' => 1,
                            'xPos' => 'center',
                            'fontSize' => 9,
                            'cols' => array(
                                'cols1' => array('width' => 30, 'justification' => 'center'),
                                'cols2' => array('width' => 70, 'justification' => 'center'),
                                'cols3' => array('width' => 75, 'justification' => 'left'),
                                'cols4' => array('width' => 75, 'justification' => 'left'),
                                'cols5' => array('width' => 250, 'justification' => 'left'),
                                'cols6' => array('width' => 50, 'justification' => 'left'),
                                'cols7' => array('width' => 50, 'justification' => 'right'),
                                'cols8' => array('width' => 60, 'justification' => 'right'),
                                'cols9' => array('width' => 70, 'justification' => 'right')
                            )
                        ));

                        $this->cezpdf_horizontal->ezText('', '');
                    }
                } else {


                    if ($contador % 8 == 0) {
                        $col_names = array(
                            'cols1' => 'Item',
                            'cols2' => utf8_decode_seguro('CÃ³digo'),
                            'cols3' => 'Marca',
                            'cols4' => 'Modelo',
                            'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                            'cols6' => 'Uni.',
                            'cols7' => 'Cant.',
                            'cols8' => 'Precio Uni.',
                            'cols9' => 'Precio Total'
                        );


                        $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                            'width' => 730,
                            'showLines' => 1,
                            'shaded' => 0,
                            'showHeadings' => 1,
                            'xPos' => 'center',
                            'fontSize' => 9,
                            'cols' => array(
                                'cols1' => array('width' => 30, 'justification' => 'center'),
                                'cols2' => array('width' => 70, 'justification' => 'center'),
                                'cols3' => array('width' => 75, 'justification' => 'left'),
                                'cols4' => array('width' => 75, 'justification' => 'left'),
                                'cols5' => array('width' => 250, 'justification' => 'left'),
                                'cols6' => array('width' => 50, 'justification' => 'left'),
                                'cols7' => array('width' => 50, 'justification' => 'right'),
                                'cols8' => array('width' => 60, 'justification' => 'right'),
                                'cols9' => array('width' => 70, 'justification' => 'right')
                            )
                        ));

                        $this->cezpdf_horizontal->ezText('', '');
                        $this->cezpdf_horizontal->ezNewPage();
                        $db_data = array();
                    }


                    if ($valor->PRESDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->PRESDEC_Pu + $valor->PRESDEC_Pu * $valor->PRESDEC_Igv100 / 100;
                    $db_data[] = array(
                        'cols1' => $contador,
                        'cols2' => $valor->PROD_CodigoUsuario,
                        'cols3' => utf8_decode_seguro($valor->MARCC_Descripcion),
                        'cols4' => utf8_decode_seguro($valor->PROD_Modelo),
                        'cols5' => utf8_decode_seguro($valor->PROD_Nombre),
                        'cols6' => $valor->UNDMED_Simbolo,
                        'cols7' => $valor->PRESDEC_Cantidad,
                        'cols8' => number_format($pu_conigv, 2),
                        'cols9' => number_format($valor->PRESDEC_Cantidad * $pu_conigv, 2)
                    );

                    if ($contador == $total_items) {
                        $col_names = array(
                            'cols1' => 'Item',
                            'cols2' => utf8_decode_seguro('CÃ³digo'),
                            'cols3' => 'Marca',
                            'cols4' => 'Modelo',
                            'cols5' => utf8_decode_seguro('DescripciÃ³n'),
                            'cols6' => 'Uni.',
                            'cols7' => 'Cant.',
                            'cols8' => 'Precio Uni.',
                            'cols9' => 'Precio Total'
                        );


                        $this->cezpdf_horizontal->ezTable($db_data, $col_names, '', array(
                            'width' => 730,
                            'showLines' => 1,
                            'shaded' => 0,
                            'showHeadings' => 1,
                            'xPos' => 'center',
                            'fontSize' => 9,
                            'cols' => array(
                                'cols1' => array('width' => 30, 'justification' => 'center'),
                                'cols2' => array('width' => 70, 'justification' => 'center'),
                                'cols3' => array('width' => 75, 'justification' => 'left'),
                                'cols4' => array('width' => 75, 'justification' => 'left'),
                                'cols5' => array('width' => 250, 'justification' => 'left'),
                                'cols6' => array('width' => 50, 'justification' => 'left'),
                                'cols7' => array('width' => 50, 'justification' => 'right'),
                                'cols8' => array('width' => 60, 'justification' => 'right'),
                                'cols9' => array('width' => 70, 'justification' => 'right')
                            )
                        ));

                        $this->cezpdf_horizontal->ezText('', '');
                    }
                }
            }
        }


        /* Totales */
        $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>SUBTOTAL</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
            array('cols0' => '', 'cols1' => '<b>IGV</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
            array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
        );
        $this->cezpdf_horizontal->ezTable($db_data, "", "", array(
            'width' => 730,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols0' => array('width' => 620, 'justification' => 'left'),
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 55, 'justification' => 'right')
            )
        ));

        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>Condiciones de venta:</b>', 'cols1' => '', 'cols2' => ''),
            array('cols0' => 'Forma de pago', 'cols1' => ': ', 'cols2' => utf8_decode_seguro(strtoupper($forma_pago))),
            array('cols0' => utf8_decode_seguro('Banco de CrÃ©dito Soles'), 'cols1' => ':', 'cols2' => 'Cta. Cte. No. 285-1178292-0-25'),
            array('cols0' => utf8_decode_seguro('Banco de CrÃ©dito DÃ³lares'), 'cols1' => ':', 'cols2' => 'Cta. Cte. No. 285-1202278-1-18'),
            array('cols0' => 'Lugar de entrega', 'cols1' => ':', 'cols2' => utf8_decode_seguro(strtoupper($lugar_entrega))),
            array('cols0' => 'Plazo de entrega', 'cols1' => ':', 'cols2' => utf8_decode_seguro(strtoupper($tiempo_entrega))),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ':', 'cols2' => utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Pte.', 'cols1' => ':', 'cols2' => utf8_decode_seguro($validez))
        );
        $this->cezpdf_horizontal->ezTable($db_data, "", "", array(
            'width' => 730,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 20, 'justification' => 'center'),
                'cols2' => array('width' => 590, 'justification' => 'left')
            )
        ));
        $this->cezpdf_horizontal->ezText('-------------------------------------------------------------------', 9, array("left" => 300));
        $this->cezpdf_horizontal->ezText('<b>p. ' . $datos_empresa[0]->EMPRC_RazonSocial . '</b>', 9, array("left" => 320));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf_horizontal->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_conmenbrete_formato4($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];
        $nom = $temp['vendedor_nombre'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        if ($this->somevar['compania'] == 1)
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_fondo.jpg'));
        else
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/ferremax_jmb_fondo.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

        $this->cezpdf->addInfo($datacreator);
        /* Para las imagenes */
        if ($this->somevar['compania'] == 1)
            $this->cezpdf->ezImage("images/img_db/ferremax_cabe.jpg", -10, 555, 'none', 'left');
        else
            $this->cezpdf->ezImage("images/img_db/ferremax_cabe_jmb.jpg", -10, 555, 'none', 'left');

        $delta = 20;

        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => 'Vend.', 'cols4' => ': ' . $nom),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $producto = $valor->PROD_Codigo;
            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $marca_prod[0]->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function obtener_detalle_presupuesto($tipo_oper, $tipo_docu, $presupuesto) {

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto_filtrado($presupuesto);

        if (count($datos_presupuesto) > 0) {
            $formapago = $datos_presupuesto[0]->FORPAP_Codigo;
            $moneda = $datos_presupuesto[0]->MONED_Codigo;
            $serie = $datos_presupuesto[0]->PRESUC_Serie;
            $numero = $datos_presupuesto[0]->PRESUC_Numero;
            $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
            $tipo_doc = $datos_presupuesto[0]->PRESUC_TipoDocumento;

            $detalle = $this->presupuestodetalle_model->listar($presupuesto);
            $lista_detalles = array();
            if (count($detalle) > 0) {
                foreach ($detalle as $indice => $valor) {
                    $detpresup = $valor->PRESDEP_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad_medida = $valor->UNDMED_Codigo;
                    $cantidad = $valor->PRESDEC_Cantidad;
                    $igv100 = $valor->PRESDEC_Igv100;
                    $pu = $valor->PRESDEC_Pu;
                    $subtotal = $valor->PRESDEC_Subtotal;
                    $igv = $valor->PRESDEC_Igv;
                    $descuento = $valor->PRESDEC_Descuento;
                    $total = $valor->PRESDEC_Total;



                    $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                    $subtotal_conigv = $valor->PRESDEC_Subtotal_ConIgv;
                    $descuento_conigv = $valor->PRESDEC_Descuento_ConIgv;
                    $observacion = $valor->PRESDEC_Observacion;
                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
                    $nombre_producto = ($valor->PRESDEC_Descripcion != '' ? $valor->PRESDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                    $nombre_producto = str_replace('"', "''", $nombre_producto);
                    $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $costo = $datos_producto[0]->PROD_CostoPromedio;
                    $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                    $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;


                    $objeto = new stdClass();
                    $objeto->flagBS = $flagBS;
                    $objeto->PRESDEP_Codigo = $detpresup;
                    $objeto->PROD_Codigo = $producto;
                    $objeto->PROD_CodigoInterno = $codigo_interno;
                    $objeto->UNDMED_Codigo = $unidad_medida;
                    $objeto->UNDMED_Simbolo = $nombre_unidad;
                    $objeto->PROD_Nombre = $nombre_producto;
                    $objeto->PROD_GenericoIndividual = $flagGenInd;
                    $objeto->PROD_CostoPromedio = $costo;
                    $objeto->PRESDEC_Cantidad = $cantidad;
                    $objeto->PRESDEC_Pu = $pu;
                    $objeto->PRESDEC_Subtotal = $subtotal;
                    $objeto->PRESDEC_Descuento = $descuento;
                    $objeto->PRESDEC_Igv = $igv;
                    $objeto->PRESDEC_Total = $total;
                    $objeto->PRESDEC_Pu_ConIgv = $pu_conigv;
                    $objeto->PRESDEC_Subtotal_ConIgv = $subtotal_conigv;
                    $objeto->PRESDEC_Descuento_ConIgv = $descuento_conigv;
                    $objeto->MONED_Codigo = $moneda;
                    $objeto->FORPAP_Codigo = $formapago;
                    $objeto->PRESUC_Serie = $serie;
                    $objeto->PRESUC_Numero = $numero;
                    $objeto->PRESUC_CodigoUsuario = $codigo_usuario;

                    $lista_detalles[] = $objeto;
                }
            } else {
                $objeto = new stdClass();
                $objeto->PRESDEP_Codigo = '';
                $objeto->MONED_Codigo = $moneda;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PRESUC_Numero = $numero;
                $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
                $lista_detalles[] = $objeto;
            }
            $resultado = json_encode($lista_detalles);

            echo $resultado;
        } else {
            echo 0;
        }
    }
    
    public function obtener_detalle_pedido($pedido) {
    	$pedido = $this->input->post('pedido');
    	$datos_pedido = $this->pedido_model->obtener_pedido_filtrado($pedido);
   
    	
    	if (count($datos_pedido) > 0) {
    		$formapago = "";
    		$moneda = $datos_pedido[0]->MONED_Codigo;
    		$serie = $datos_pedido[0]->PEDIC_Serie;
    		$numero = $datos_pedido[0]->PEDIC_Numero;
    
    		$detalle = $this->pedidodetalle_model->listar1($pedido);
    		$lista_detalles = array();
    		if (count($detalle) > 0) {
    			foreach ($detalle as $indice => $valor) {
    				$detpresup = $valor->PEDIDETP_Codigo;
    				$producto = $valor->PROD_Codigo;
    				$unidad_medida = $valor->UNDMED_Codigo;
    				$cantidad = $valor->PEDIDETC_Cantidad;
    				$igv100 = $valor->PEDIDETC_PCIGV;
    				$pu = $valor->PEDIDETC_PSIGV;
    				$subtotal = $valor->PEDIDETC_Precio;
    				$igv = $valor->PEDIDETC_IGV;
    				$descuento = $valor->PEDIDETC_Descuento ;
    				$descuento100 = $valor->PEDIDETC_Descuento100;
    				if($descuento == "")
    					$descuento = 0;
    				
    				$total = $valor->PEDIDETC_Importe;
    				$pu_conigv = $valor->PEDIDETC_PCIGV;
    				$subtotal_conigv = $valor->PEDIDETC_Importe;
    				$descuento_conigv = "";
    				$observacion = "";
    					
    				$datos_producto = $this->producto_model->obtener_producto($producto);
    				$codigo_interno = $datos_producto[0]->PROD_CodigoInterno;
    				
    				
    				$nombre_producto = $datos_producto[0]->PROD_Nombre;
    				$nombre_producto = str_replace('"', "''", $nombre_producto);
    				$flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
    				$flagBS = $datos_producto[0]->PROD_FlagBienServicio;
    				$costo = $datos_producto[0]->PROD_CostoPromedio;
    				$datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
    				$nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;
    
    
    				$objeto = new stdClass();
    				$objeto->flagBS = $flagBS;
    				$objeto->PRESDEP_Codigo = $detpresup;
    				$objeto->PROD_Codigo = $producto;
    				$objeto->PROD_CodigoInterno = $codigo_interno;
    				$objeto->UNDMED_Codigo = $unidad_medida;
    				$objeto->UNDMED_Descripcion = $nombre_unidad;
    				$objeto->PROD_Nombre = $nombre_producto;
    				$objeto->PROD_GenericoIndividual = $flagGenInd;
    				$objeto->PROD_CostoPromedio = $costo;
    				$objeto->PRESDEC_Cantidad = $cantidad;
    				$objeto->PRESDEC_Pu = $pu;
    				$objeto->PRESDEC_Subtotal = $subtotal;
    				$objeto->PRESDEC_Descuento = $descuento;
    				$objeto->PRESDEC_Descuento100 = $descuento100;
    				$objeto->PRESDEC_Igv = $igv;
    				$objeto->PRESDEC_Total = $total;
    				$objeto->PRESDEC_Pu_ConIgv = $pu_conigv;
    				$objeto->PRESDEC_Subtotal_ConIgv = $subtotal_conigv;
    				$objeto->PRESDEC_Descuento_ConIgv = $descuento_conigv;
    				$objeto->MONED_Codigo = $moneda;
    				$objeto->FORPAP_Codigo = $formapago;
    				$objeto->PRESUC_Serie = $serie;
    				$objeto->PRESUC_Numero = $numero;
    
    				$lista_detalles[] = $objeto;
    			}
    		} else {
    			$objeto = new stdClass();
    			$objeto->PRESDEP_Codigo = '';
    			$objeto->MONED_Codigo = $moneda;
    			$objeto->FORPAP_Codigo = $formapago;
    			$objeto->PRESUC_Numero = $numero;
    			$objeto->PRESUC_CodigoUsuario = $codigo_usuario;
    			$lista_detalles[] = $objeto;
    		}
    		$resultado = json_encode($lista_detalles);
    
    		echo $resultado;
    	} else {
    		echo 0;
    	}
    }
    public function obtener_detalle_presupuesto1($tipo_oper, $tipo_docu, $serie, $numero) {

        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto_filtrado1($serie, $numero);

        if (count($datos_presupuesto) > 0) {
            $formapago = $datos_presupuesto[0]->FORPAP_Codigo;
            $moneda = $datos_presupuesto[0]->MONED_Codigo;
            $serie = $datos_presupuesto[0]->PRESUC_Serie;
            $numero = $datos_presupuesto[0]->PRESUC_Numero;
            $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;

            $cliente = $datos_presupuesto[0]->CLIP_Codigo;

            $tipo_doc = $datos_presupuesto[0]->PRESUC_TipoDocumento;
            $presupuesto = $datos_presupuesto[0]->PRESUP_Codigo;
            $temp = $this->obtener_datos_cliente($cliente);

            $ruc = $temp['numdoc'];
            $razon_social = $temp['nombre'];
            $detalle = $this->presupuestodetalle_model->listar($presupuesto);
            $lista_detalles = array();
            if (count($detalle) > 0) {
                foreach ($detalle as $indice => $valor) {
                    $detpresup = $valor->PRESDEP_Codigo;
                    $producto = $valor->PROD_Codigo;
                    $unidad_medida = $valor->UNDMED_Codigo;
                    $cantidad = $valor->PRESDEC_Cantidad;
                    $igv100 = $valor->PRESDEC_Igv100;
                    $pu = $valor->PRESDEC_Pu;
                    $subtotal = round($valor->PRESDEC_Subtotal, 2);
                    $igv = $valor->PRESDEC_Igv;
                    $descuento = round($valor->PRESDEC_Descuento, 2);
                    $total = $valor->PRESDEC_Total;


                    /////aumentado stv

                    /* if($tipo_doc=='B'){
                      $pu = $valor->PRESDEC_Pu_ConIgv;
                      $subtotal = ($pu * $cantidad);
                      $igv = $valor->PRESDEC_Igv;
                      $descuento = $valor->PRESDEC_Descuento;
                      $total = $subtotal;
                      } */

//                    if($tipo_doc == 'B'){
//                    $pu = round((($tipo_doc == 'B') ? $valor->PRESDEC_Pu : $valor->PRESDEC_Pu_ConIgv), 2);
//                    $subtotal = round((($tipo_doc == 'B') ? $valor->PRESDEC_Subtotal : $pu * $cantidad), 2);
//                    $igv = round(0, 2);
//                    $descuento = round($valor->PRESDEC_Descuento_ConIgv, 2);
//                    $total = round((($tipo_doc == 'B') ? $valor->PRESDEC_Total : $subtotal), 2);
//                    }
//                    
                    /////////////////


                    $pu_conigv = $valor->PRESDEC_Pu_ConIgv;
                    $subtotal_conigv = $valor->PRESDEC_Subtotal_ConIgv;
                    $descuento_conigv = $valor->PRESDEC_Descuento_ConIgv;
                    $observacion = $valor->PRESDEC_Observacion;
                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                    $nombre_producto = ($valor->PRESDEC_Descripcion != '' ? $valor->PRESDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                    $nombre_producto = str_replace('"', "''", $nombre_producto);
                    $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
                    $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                    $costo = $datos_producto[0]->PROD_CostoPromedio;
                    $datos_umedida = $this->unidadmedida_model->obtener($unidad_medida);
                    if ($datos_umedida) {
                        $nombre_unidad = $datos_umedida[0]->UNDMED_Descripcion;
                    } else {
                        $nombre_unidad = "SERV";
                    }
                    //echo "uni -> ".$unidad_medida;

                    $objeto = new stdClass();
                    $objeto->flagBS = $flagBS;
                    $objeto->PRESDEP_Codigo = $detpresup;
                    $objeto->PROD_Codigo = $producto;
                    $objeto->PROD_CodigoInterno = $codigo_interno;
                    $objeto->UNDMED_Codigo = $unidad_medida;
                    $objeto->UNDMED_Descripcion = $nombre_unidad;
                    $objeto->PROD_Nombre = $nombre_producto;
                    $objeto->PROD_GenericoIndividual = $flagGenInd;
                    $objeto->PROD_CostoPromedio = $costo;
                    $objeto->PRESDEC_Cantidad = $cantidad;
                    $objeto->PRESDEC_Pu = $pu;
                    $objeto->PRESDEC_Subtotal = $subtotal;
                    $objeto->PRESDEC_Descuento = $descuento;
                    $objeto->PRESDEC_Igv = $igv;
                    $objeto->PRESDEC_Total = $total;
                    $objeto->PRESDEC_Pu_ConIgv = $pu_conigv;
                    $objeto->PRESDEC_Subtotal_ConIgv = $subtotal_conigv;
                    $objeto->PRESDEC_Descuento_ConIgv = $descuento_conigv;
                    $objeto->Ruc = $ruc;
                    $objeto->RazonSocial = $razon_social;
                    $objeto->CLIP_Codigo = $cliente;
                    $objeto->MONED_Codigo = $moneda;
                    $objeto->FORPAP_Codigo = $formapago;
                    $objeto->PRESUP_Codigo = $presupuesto;
                    $objeto->PRESUC_Serie = $serie;
                    $objeto->PRESUC_Numero = $numero;
                    $objeto->PRESUC_CodigoUsuario = $codigo_usuario;

                    $lista_detalles[] = $objeto;
                }
            } else {
                $objeto = new stdClass();
                $objeto->PRESDEP_Codigo = '';
                $objeto->Ruc = $ruc;
                $objeto->RazonSocial = $razon_social;
                $objeto->CLIP_Codigo = $cliente;
                $objeto->MONED_Codigo = $moneda;
                $objeto->FORPAP_Codigo = $formapago;
                $objeto->PRESUC_Numero = $numero;
                $objeto->PRESUC_CodigoUsuario = $codigo_usuario;
                $objeto->PRESUP_codigo = $presupuesto;
                $lista_detalles[] = $objeto;
            }
            $resultado = json_encode($lista_detalles);

            echo $resultado;
            //echo $presupuesto;
        } else {
            echo 0;
        }
    }

    public function JSON_obtener_presupuesto($presupuesto) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($presupuesto);
        echo json_encode($datos_presupuesto);
    }

    function obtener_datos_cliente($cliente, $tipo_docu = 'F') {
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
        $direccion = '';
        if ($tipo == 0) {
            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
            $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            if ($tipo_docu != 'B')
                $numdoc = $datos_persona[0]->PERSC_Ruc;
            else
                $numdoc = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $direccion = $datos_persona[0]->PERSC_Direccion;
            $telefono = $datos_persona[0]->PERSC_Telefono;
            $movil = $datos_persona[0]->PERSC_Movil;
            $fax = $datos_persona[0]->PERSC_Fax;
            $email = $datos_persona[0]->PERSC_Email;
            $contacto = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
        } else if ($tipo == 1) {
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;

            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            //$direccion        = $emp_direccion[0]->EESTAC_Direccion;
            //stv asi taba   $direccion = "";
            //stv
            $direccion = $datos_empresa[0]->EMPRC_Direccion;
            ////
            $telefono = $datos_empresa[0]->EMPRC_Telefono;
            $movil = $datos_empresa[0]->EMPRC_Movil;
            $fax = $datos_empresa[0]->EMPRC_Fax;
            $email = $datos_empresa[0]->EMPRC_Email;
            $contacto = '';

            $contactos = $this->empresa_model->obtener_contactoEmpresa($empresa);
            if (count($contactos) > 0) {
                $datos_persona = $this->persona_model->obtener_datosPersona($contactos[0]->ECONC_Persona);
                $contacto = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
            }
        }

        return array('numdoc' => $numdoc, 'nombre' => $nombre, 'direccion' => $direccion, 'telefono' => $telefono, 'movil' => $movil, 'fax' => $fax, 'email' => $email, 'contacto' => $contacto, 'vendedor_nombre' => $vendedor_nombre);
    }

    public function presupuesto_ver_pdf_conmenbrete_formato5($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        echo $direccion;
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/cyg_fondo.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

        $this->cezpdf->addInfo($datacreator);
        /* Para las imagenes */
        $this->cezpdf->ezImage("images/img_db/gyc_cabecera.jpg", -10, 555, 'none', 'left');

        $delta = 20;

        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $producto = $valor->PROD_Codigo;
            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $marca_prod[0]->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_conmenbrete_formato6($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/cyg_fondo_2.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

        $this->cezpdf->addInfo($datacreator);
        /* Para las imagenes */
        $this->cezpdf->ezImage("images/img_db/gyc_cabecera_2.jpg", -10, 555, 'none', 'left');

        $delta = 20;

        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $producto = $valor->PROD_Codigo;
            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $marca_prod[0]->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function presupuesto_ver_pdf_conmenbrete_formato7($codigo) {
        $datos_presupuesto = $this->presupuesto_model->obtener_presupuesto($codigo);
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie = $datos_presupuesto[0]->PRESUC_Serie;
        $numero = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion = ($codigo_usuario != '' ? $codigo_usuario : ($serie != '' ? $serie . '/' . $numero : 'Nro. ' . $numero));
        $cliente = $datos_presupuesto[0]->CLIP_Codigo;
        $subtotal = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento = $datos_presupuesto[0]->PRESUC_descuento;
        $igv = $datos_presupuesto[0]->PRESUC_igv;
        $igv100 = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100 = $datos_presupuesto[0]->PRESUC_descuento100;
        $total = $datos_presupuesto[0]->PRESUC_total;
        $observacion = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto = $datos_presupuesto[0]->PERSP_Codigo;
        $area = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion = ((int) $datos_presupuesto[0]->PRESUC_ModoImpresion > 0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');

        $forma_pago = '';
        if ($datos_presupuesto[0]->FORPAP_Codigo != '') {
            $datos_formapago = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $datos_cliente = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa = $datos_cliente[0]->EMPRP_Codigo;
        $persona = $datos_cliente[0]->PERSP_Codigo;
        $tipo = $datos_cliente[0]->CLIC_TipoPersona;


        $datos_moneda = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        $temp = $this->obtener_datos_cliente($cliente, $tipo_docu);
        $nombre_cliente = $temp['nombre'];
        $ruc = $temp['numdoc'];
        $direccion = $temp['direccion'];
        $telefono = ($temp['telefono'] == '' ? $temp['movil'] : $temp['telefono']);
        $fax = $temp['fax'];
        $email = $temp['email'];

        $nombre_contacto = $nombre_cliente;
        if ($contacto != '' && $contacto != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($contacto);
            if (count($datos_persona) > 0)
                $nombre_contacto = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area = '';
        if ($area != '' && $area != '0') {
            $datos_area = $this->area_model->obtener_area($area);
            if (count($datos_area) > 0)
                $nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        $filter = new stdClass();
        $filter->TIPCAMC_Fecha = date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino = 2;  // De soles a dolares
        $data_tipocambio = $this->tipocambio_model->buscar($filter);
        $tipo_cambio = '';
        if (count($data_tipocambio) > 0)
            $tipo_cambio = $data_tipocambio[0]->TIPCAMC_FactorConversion;


        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);

        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/cyl_fondo.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

        $this->cezpdf->addInfo($datacreator);
        /* Para las imagenes */
        $this->cezpdf->ezImage("images/img_db/cyl_cabecera.jpg", -10, 555, 'none', 'left');

        $delta = 20;

        $this->cezpdf->ezText(utf8_decode_seguro('<b>CotizaciÃ³n: ') . $codificacion . '</b>', 17, array("leading" => 40, 'left' => 185));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */
        $db_data = array(array('cols1' => utf8_decode_seguro('SeÃ±or(es)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_cliente), 'cols3' => 'R.U.C.', 'cols4' => ': ' . $ruc . '       Fecha: ' . $fecha),
            array('cols1' => utf8_decode_seguro('DirecciÃ³n'), 'cols2' => ': ' . utf8_decode_seguro($direccion), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('AtenciÃ³n Sr(a)'), 'cols2' => ': ' . utf8_decode_seguro($nombre_contacto . ($nombre_area != '' ? ' - AREA: ' . $nombre_area : '')), 'cols3' => '', 'cols4' => ''),
            array('cols1' => utf8_decode_seguro('TelÃ©fono'), 'cols2' => ': ' . $telefono, 'cols3' => 'E-mail', 'cols4' => ': ' . $email)
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 65, 'justification' => 'left'),
                'cols2' => array('width' => 275, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 150, 'justification' => 'left')
            )
        ));

        $this->cezpdf->ezText('', 8);

        /* Listado de detalles */
        $db_data = array();
        foreach ($detalle_presupuesto as $indice => $valor) {
            $producto = $valor->PROD_Codigo;
            $marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            $db_data[] = array(
                'cols1' => $indice + 1,
                'cols2' => $marca_prod[0]->MARCC_Descripcion,
                'cols3' => utf8_decode_seguro($valor->PROD_Nombre . ($valor->PROD_Modelo != '' ? ' - ' . $valor->PROD_Modelo : '')),
                'cols4' => $valor->UNDMED_Simbolo,
                'cols5' => $valor->PRESDEC_Cantidad,
                'cols6' => number_format(($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2),
                'cols7' => number_format($valor->PRESDEC_Cantidad * ($modo_impresion == '1' || $tipo_docu == 'B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu), 2)
            );
        }
        $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('DescripciÃ³n'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7' => 'Precio Total'
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 525,
            'showLines' => 1,
            'shaded' => 0,
            'showHeadings' => 1,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 30, 'justification' => 'center'),
                'cols2' => array('width' => 70, 'justification' => 'left'),
                'cols3' => array('width' => 235, 'justification' => 'left'),
                'cols4' => array('width' => 40, 'justification' => 'left'),
                'cols5' => array('width' => 40, 'justification' => 'left'),
                'cols6' => array('width' => 50, 'justification' => 'right'),
                'cols7' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $this->cezpdf->ezText('', '');

        /* Totales */
        if ($tipo_docu != 'B') {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>VALOR VENTA</b>', 'cols2' => '<b>' . number_format($subtotal, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>IMPUESTO</b>', 'cols2' => '<b>' . number_format($igv, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>')
            );
        } else {
            $db_data = array(array('cols0' => '<b>SON : ' . strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . '</b>', 'cols1' => '<b>TOTAL ' . $moneda_simbolo . '</b>', 'cols2' => '<b>' . number_format($total, 2) . '</b>'),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        }

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 395, 'justification' => 'left'),
                'cols1' => array('width' => 70, 'justification' => 'left'),
                'cols2' => array('width' => 60, 'justification' => 'right')
            )
        ));

        $vendedor_nombre = '';
        if ($vendedor_persona != '' && $vendedor_persona != '0') {
            $datos_persona = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if (count($datos_persona) > 0)
                $vendedor_nombre = $datos_persona[0]->PERSC_Nombre . ' ' . $datos_persona[0]->PERSC_ApellidoPaterno . ' ' . $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area = '';
        if ($vendedor_area != '' && $vendedor_area != '0') {
            $datos_area = $this->area_model->obtener_area($vendedor_area);
            if (count($datos_area) > 0)
                $vendedor_nombre_area = $datos_area[0]->AREAC_Descripcion;
        }
        /* Condiciones de venta */
        $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            array('cols0' => utf8_decode_seguro('GarantÃ­a'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function getOrderNumeroSerie($numero) {
        $cantidad = strlen($numero);
        if ($cantidad == 1) {
            $dato = "00000$numero";
        }
        if ($cantidad == 2) {
            $dato = "0000$numero";
        }
        if ($cantidad == 3) {
            $dato = "000$numero";
        }
        if ($cantidad == 4) {
            $dato = "00$numero";
        }
        if ($cantidad == 5) {
            $dato = "0$numero";
        }
        if ($cantidad == 6) {
            $dato = "$numero";
        }
        return $dato;
    }

    /*     * seleccionamos presupuesto para que no pueda ser sxeleccionado/deseleccionado en otros docuymentos* */

    public function modificarTipoSeleccion($codigoPresupuesto, $estadoSeleccion) {
        /*         * verificamos si el presupuesto ya ha sido seleccionado* */
        $presupuesto = $this->presupuesto_model->obtener_presupuesto($codigoPresupuesto);
        $estadoSeleccionReal = $presupuesto[0]->PRESUP_Seleccion;
        if (count($presupuesto) > 0) {
            if ($estadoSeleccionReal == 1 && $estadoSeleccion == 1) {
                echo "0";
            } else {
                /*                 * 1:sdeleccionado,0:deseleccionado* */
                $this->presupuesto_model->modificarTipoSeleccion($codigoPresupuesto, $estadoSeleccion);
                echo "1";
            }
        } else {
            echo "2";
        }
    }
	
	public function registro_presupuesto_pdf($flagBS, $fechai, $fechaf, $numero, $cliente, $producto)
    {
		IF($fechai!="--" && $fechaf!="--"){
        $fi = explode("-",$fechai);
        $ff = explode("-",$fechaf);
        $fechain = $fi[2].'/'.$fi[1].'/'.$fi[0];
        $fechafin = $ff[2].'/'.$ff[1].'/'.$ff[0];
		}else{
		$fechain = "--";
        $fechafin = "--";
		}

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

        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","SÃ¡bado");
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
 
$fechhoy="".$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');

        $this->cezpdf->ezText('', '', array("leading" => 50));
        $this->cezpdf->ezText('<b>RELACION DE PRE-VENTA
</b>', 14, array("leading" => 0, 'left' => 40));
$this->cezpdf->ezText(($fechhoy), 9, array("left" => 350));
        $this->cezpdf->ezText('', '', array("leading" => 10));


        /* Datos del cliente */


//        /* Listado de detalles */

        $db_data = array();


        $listado_presupuesto = $this->presupuesto_model->listar_presupuesto_pdf($flagBS, $fechain, $fechafin, $numero, $cliente, $producto);
    
            if (count($listado_presupuesto) > 0) {
                foreach ($listado_presupuesto as $indice => $valor) {
                    $fecha = $valor->PRESUC_Fecha;
                    $serie = $valor->PRESUC_Serie;
                    $numero = $valor->PRESUC_Numero;
                    $codigo = $valor->CLIP_Codigo;
                    $nombre = $valor->nombre;
                    $total = $valor->MONED_Simbolo.$valor->PRESUC_total;
                    $Stotal+= $valor->PRESUC_total;

                    $db_data[] = array(
                        'cols1' => $indice + 1,
                        'cols2' => $fecha,
                        'cols3' => $serie,
                        'cols4' => $numero,
                        'cols5' => $codigo,
                        'cols6' => $nombre,
                        'cols7' => $total
                    );
                }
            }

        


        $col_names = array(
            'cols1' => '<b>ITEM</b>',
            'cols2' => '<b>FECHA</b>',
            'cols3' => '<b>SERIE</b>',
            'cols4' => '<b>NUMERO</b>',
            'cols5' => '<b>CODIGO</b>',
            'cols6' => '<b>NOMBRE</b>',
            'cols7' => '<b>TOTAL</b>'
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
                'cols2' => array('width' => 60, 'justification' => 'center'),
                'cols3' => array('width' => 40, 'justification' => 'center'),
                'cols4' => array('width' => 50, 'justification' => 'center'),
                'cols5' => array('width' => 50, 'justification' => 'center'),
                'cols6' => array('width' => 165, 'justification' => 'center'),
                'cols7' => array('width' => 50, 'justification' => 'center')
            )
        ));
        $this->cezpdf->ezText('TOTAL:   '. $valor->MONED_Simbolo.number_format($Stotal,2), '8', array("leading" => 15, 'left' => 400));


        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');

        ob_end_clean();

        $this->cezpdf->ezStream($cabecera);
    }

}

?>