<?php
class Produccion extends CI_Controller{
    public function __construct()
    {
        parent::Controller();
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/guiatrans_model');
        $this->load->model('almacen/guiatransdetalle_model');
        $this->load->model('almacen/inventario_model');
        
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('compras/pedidodetalle_model');
        $this->load->model('compras/presupuesto_model');

        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/proyecto_model');
        $this->load->model('empresa/emprcontacto_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/configuracion_model');
                
        $this->load->model('seguridad/usuario_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/comprobante_model');
        
        $this->load->helper('json');
        $this->load->helper('form');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');

        $this->load->library('html');
        $this->load->library('table');
        $this->load->library('layout','layout');
        $this->load->library('pagination');
        $this->load->library('lib_props');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
        $this->somevar['url'] = $_SERVER['REQUEST_URI'];
        $this->somevar['hoy']       = mdate("%Y-%m-%d",time());
        date_default_timezone_set("America/Lima");

    }

    public function index(){
        $this->layout->view('seguridad/inicio');    
    }

    public function receta_index( $j = '0' ){
        
        $data['action'] = base_url() . "index.php/almacen/produccion/receta_index";
        $filter = new stdClass();
        $filter->codigo = $this->input->post('txtCodigo');
        $filter->nombre = $this->input->post('txtNombre');

        $data['registros'] = count($this->producto_model->listarRecetas($filter));
        $conf['base_url'] = site_url('almacen/produccion/receta_index');
        $conf['per_page'] = 30;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        
        $listar = $this->producto_model->listarRecetas($filter, $conf['per_page'], $offset);
        $listarecetas = '';
        $item   = $j+1;
        $lista  = array();
        
        if(count($listar)>0){
            foreach ($listar as $indice => $valor){
                $id = $valor->REC_Codigo;
                $nombre = $valor->REC_Descripcion;
                $prodCodigo = $valor->PROD_Codigo;
                
                $editar = "<a href='#' onclick='editar_receta($id)'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar = "<a href='#' onclick='eliminar_receta($id)'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $pdf1 = "<a href='#' onclick='ver_receta($id, 1)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='PDF'></a>";

                $lista[]        = array($item,$nombre,$eliminar,$editar,$pdf1);
                $item++;
            }
        }
        $data['codigo'] = $filter->codigo;
        $data['nombre'] = $filter->nombre;
        $data['lista'] = $lista;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/receta_index',$data);
    }

    public function receta_nueva( $tipo_oper = 'C' ){
        $tipo_oper = "P";
        $tipo_docu = "R";
        $tipoDocumento = 'RECETA';
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

            
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";

            $data['compania'] = $compania;
            $data['cambio_comp'] = "0";
            $data['total_det'] = "0";   
            $data['codigo'] = $codigo;

            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/insertar_receta";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmReceta";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            
            $data['detalle_comprobante'] = array();
            $data['observacion'] = "";
            $data['focus'] = "";
            $data['pedido'] = "";
            $data['hidden'] = "";
            $data['observacion'] = "";
            $data['ordencompra'] = "";
            $data['dRef'] = "";
            $data['estado'] = "2";
            $data['numeroAutomatico'] = 1;
            $data['oc_cliente'] = "";
            
            $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
                        
            $listaGuiarem = array();
            $listaGuiarem = null;
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('almacen/receta_nueva', $data);
    }

    public function editar_receta( $idReceta ){
        
        $tipo_oper = "P";
        $tipo_docu = "R";
        $tipoDocumento = 'RECETA';
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        $filter->codigo = $idReceta;
        $receta = $this->producto_model->listarRecetas($filter);
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";

            $data['compania'] = $compania;
            $data['codigo'] = $idReceta;

            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/modificar_receta";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmReceta";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            
            $data['detalle_comprobante'] = array();
            $data['observacion'] = "";
            $data['focus'] = "";
            $data['pedido'] = "";
            $data['hidden'] = "";
            $data['observacion'] = "";
            $data['ordencompra'] = "";
            $data['dRef'] = "";
            $data['estado'] = "2";
            $data['numeroAutomatico'] = 1;
            
            $data['receta'] = $idReceta;
            $data['descripcionReceta'] = $receta[0]->REC_Descripcion;
            $data['idProducto'] = $receta[0]->PROD_Codigo;
            $data['descripcionProducto'] = $receta[0]->REC_Descripcion;
            
            $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
                        
            $listaGuiarem = array();
            $listaGuiarem = null;
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('almacen/receta_nueva', $data);
    }

    public function insertar_receta(){
        $descripcion = $this->input->post('descripcion_receta');
        $producto = $this->input->post('idProducto');

        $idReceta = $this->producto_model->insertarReceta($descripcion, $producto);
        
        if ($idReceta == NULL)
            exit('{"result":"error3", "msj":"No se pudo completar la operación. Intentelo Nuevamente.\nSi el inconveniente persiste comuniquese con el administrador."}');

        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');

        if (is_array($detacodi)) {
            foreach($detacodi as $indice => $value){
                $detalle_accion = $detaccion[$indice];

                $filterRC = new stdClass();
                $filterRC->RECDET_Codigo = NULL;
                $filterRC->REC_Codigo = $idReceta;
                $filterRC->PROD_Codigo = $prodcodigo[$indice];
                $filterRC->RECDET_Cantidad = $prodcantidad[$indice];
                $filterRC->RECDET_FlagEstado = "1";

                if ( $detalle_accion != 'e' )
                    $this->producto_model->insertarProductoReceta($filterRC);
            }
        }

        header("Location:".base_url()."index.php/almacen/produccion/receta_index");
    }

    public function modificar_receta(){
        $idReceta = $this->input->post('receta');
        $descripcion = $this->input->post('descripcion_receta');
        $producto = $this->input->post('idProducto');

        $this->producto_model->actualizarReceta($idReceta, $descripcion, $producto, '1');
        
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');

        if (is_array($detacodi)) {
            foreach($detacodi as $indice => $value){
                $detalle_accion = $detaccion[$indice];
                $filterRC = new stdClass();
                $filterRC->REC_Codigo = $idReceta;
                $filterRC->PROD_Codigo = $prodcodigo[$indice];
                $filterRC->RECDET_Cantidad = $prodcantidad[$indice];

                if ($detalle_accion == 'n') {
                    $filterRC->RECDET_FlagEstado = "1";
                    $this->producto_model->insertarProductoReceta($filterRC);
                }
                else
                    if ($detalle_accion == 'm') {
                        $filterRC->RECDET_FlagEstado = "1";
                        $this->producto_model->modificarProductoReceta($value, $filterRC);
                    }
                else
                    if ($detalle_accion == 'e') {
                        $filterRC->RECDET_FlagEstado = "0";
                        $this->producto_model->modificarProductoReceta($value, $filterRC);
                    }
            }
        }
        header("Location:".base_url()."index.php/almacen/produccion/receta_index");
    }

    public function eliminar_receta($receta = NULL){
        if ($receta != NULL){
            $this->producto_model->estadoReceta($receta);
        }
        header("Location:".base_url().'index.php/almacen/produccion/receta_index/');
    }


    public function select_cmbVendedor($index){
        $array_dist = $this->comprobante_model->select_cmbVendedor();
        $arreglo = array();
        foreach ($array_dist as $indice => $valor) {
            $indice1 = $valor->PERSP_Codigo;
            $valor1 = $valor->PERSC_Nombre." ".$valor->PERSC_ApellidoPaterno;
            $arreglo[$indice1] = $valor1;
            }
            $resultado = $this->html->optionHTML($arreglo, $index, array('', '::Seleccione::'));
            return $resultado;
    }

    public function receta_pdf( $id, $img = 1, $correo = false ){
        $this->lib_props->receta_pdf( $id, $img, $correo );
    }

    #*****************************************************
    #****** CONVERSION
    #*****************************************************

    public function produccion_index( $j = '0' ){
        
        $data['action'] = base_url() . "index.php/almacen/produccion/produccion_index";
        $filter = new stdClass();
        $filter->codigo = $this->input->post('txtCodigo');
        $filter->nombre = $this->input->post('txtNombre');

        $data['registros'] = count($this->producto_model->listar_produccion($filter));
        $conf['base_url'] = site_url('almacen/produccion/produccion_index');
        $conf['per_page'] = 30;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        
        $listar = $this->producto_model->listar_produccion($filter, $conf['per_page'], $offset);
        $listarecetas = '';
        $item   = $j + 1;
        $lista  = array();
        
        if( count($listar) > 0 ){
            foreach ($listar as $indice => $valor){
                $id = $valor->PR_Codigo;
                $compania = ( $valor->establecimiento == "" || $valor->establecimiento == NULL ) ? $this->somevar['compania'] : $valor->establecimiento;
                $comppName = $this->pedido_model->nameEstablecimiento($compania);
                $compp = $comppName[0]->EESTABC_Descripcion;

                    switch ($valor->PR_FlagTerminado) {
                        case '1':
                            $estado = "<span style='background:green; color:white; font-weight:bold; font-size: 6.5pt; text-align:center; display:block; width:10em; padding:0.3em;'>TERMINADO</span>";
                            break;
                        case '2':
                            $estado = "<span style='background:orange; color:white; font-weight:bold; font-size: 6.5pt; text-align:center; display:block; width:10em; padding:0.3em;'>EN PROCESO</span>";
                            break;
                        case '3':
                            $estado = "<span style='background:red; color:white; font-weight:bold; font-size: 6.5pt; text-align:center; display:block; width:10em; padding:0.3em;'>EN ESPERA</span>";
                            break;
                        
                        case '0':
                            $estado = "<span style='background:#5e2129; color:white; font-weight:bold; font-size: 6.5pt; text-align:center; display:block; width:10em; padding:0.3em;'>ANULADO</span>";
                            break;
                    }
                if ($compp == NULL)
                    $comppName = "TALLER";

                $estado = ($valor->PR_FlagTerminado > 1) ? "<a href='javascript:;' onclick='editar_produccion($id)'>$estado</a>" : "<a href='javascript:;'>$estado</a>";

                #$eliminar = "<a href='#' onclick='eliminar_receta(" . $id . ")'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                $editar = ($valor->PR_FlagTerminado == 1) ? "<a href='#' onclick=''><img src='" . base_url() . "images/complete.png' width='16' height='16' border='0' title='Modificar'></a>" : "<a href='#' onclick=''><img src='" . base_url() . "images/error.png' width='16' height='16' border='0' title='Modificar'></a>";

                $editar = ($valor->PR_FlagTerminado > 1) ? "<a href='#' onclick='editar_produccion($id)'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>" : $editar;

                $pdf1 = "<a href='#' onclick='ver_produccion($id, 0)'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='PDF'></a>";
                $pdf2 = "<a href='#' onclick='ver_produccion($id, 1)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='PDF'></a>";
                $lista[] = array( $item, $compp, $valor->PR_Serie, $valor->PR_Numero, mysql_to_human($valor->PR_FechaRecepcion), mysql_to_human($valor->PR_FechaFinalizado), $estado, $editar, $pdf1, $pdf2);
                $item++;
            }
        }
        $data['produccion'] = '';
        $data['codigo'] = $filter->codigo;
        $data['nombre'] = $filter->nombre;
        $data['lista'] = $lista;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/produccion_index',$data);
    }

    public function produccion_nueva( $tipo_oper = 'C' ){
        $tipo_oper = "V"; #$this->uri->segment(4);
        $tipo_docu = "PR"; #$this->uri->segment(5);
        $tipoDocumento = 'PRODUCCION'; #strtoupper($this->obtener_tipo_documento($tipo_docu));
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

            
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";
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

            $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/insertar_produccion";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmProduccion";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $data['usa_adelanto'] = 0;
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['guia'] = "";
            $data['cboproyecto'] = ""; #$this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');
            $data['cboimportacion'] = ""; #$this->OPTION_generador($this->importacion_model->listar_importacion(0), 'IMPOR_Codigo', 'IMPOR_Nombre', '2');
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboMoneda'] = ""; #$this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
            $data['cboFormaPago'] = ""; #$this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '1');
            $data['cboPresupuesto'] = ""; #$this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            #$data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
            $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            $data['direccionsuc'] = form_input(array("name" => "direccionsuc", "id" => "direccionsuc", "class" => "cajaGeneral", "size" => "40", "maxlength" => "250", "value" => $punto_llegada));
            
            $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

            if (count($cambio_dia) > 0) {
                $data['tdcDolar'] = 0; #$cambio_dia[0]->TIPCAMC_FactorConversion;
            } else {
                $data['tdcDolar'] = 0;
            }
            
            $data['serie'] = '';
            $data['numero'] = '';
            $data['flagTerminado'] = 3;

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
            
            $data['fechaI'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $data['fechaF'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
            //obtengo las series de la configuracion
            
            if ($tipo_docu == 'PR') {
                $tipo = 20;
            }

            $listaGuiarem=array();
            $listaGuiarem=null;
            $data['listaGuiaremAsociados']=$listaGuiarem;

            $cofiguracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $data['serie_suger_f'] = $cofiguracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_f'] = $this->lib_props->getOrderNumeroSerie($cofiguracion_datos[0]->CONFIC_Numero + 1);
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('almacen/produccion_nueva', $data);
        
    }

    public function insertar_produccion(){
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $pedido = $this->input->post('docReferencia');
        $fecha = explode("/",$this->input->post('fecha'));
        $fechaF = explode("/",$this->input->post('fechaF'));
        $observacion = $this->input->post('observacion');
        $estado = $this->input->post('estatus');

        $filterP = new stdClass();
        $filterP->PR_Serie = $serie;
        $filterP->PR_Numero = $numero;
        $filterP->PEDIP_Codigo = $pedido;
        $filterP->PR_FechaRecepcion = "$fecha[2]-$fecha[1]-$fecha[0]";
        $filterP->PR_FechaFinalizado = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
        $filterP->PR_Observacion = $observacion;
        $filterP->PR_FlagTerminado = $estado;
        $filterP->PR_FlagEstado = "1";
        
        $idProduccion = $this->producto_model->insertarProduccion($filterP);
        
        if ($idProduccion == NULL)
            exit('{"result":"error", "msg":"No se pudo completar la operación. Intentelo Nuevamente.\nSi el inconveniente persiste comuniquese con el administrador."}');

        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');

        if (is_array($detacodi)) {
            foreach($detacodi as $indice => $value){
                $detalle_accion = $detaccion[$indice];

                $filterPD = new stdClass();
                $filterPD->PR_Codigo = $idProduccion;
                $filterPD->PROD_Codigo = $prodcodigo[$indice];
                $filterPD->PRD_Cantidad = $prodcantidad[$indice];
                $filterPD->PRD_FlagEstado = "1";

                if ( $detalle_accion != 'e' )
                    $this->producto_model->insertarProductoProduccion($filterPD);
            }

            if ( $filterP->PR_FlagTerminado == 1 && $filterP->PEDIP_Codigo != 0 && $filterP->PEDIP_Codigo != NULL ){
                $this->lib_props->sendMail(61, $idProduccion, "CAMBIO DE ESTADO PARA PEDIDO EN PRODUCCION", "LA PRODUCCION DEL PEDIDO HA FINALIZADO", "PR"); # MENU 61 = "Órdenes de Pedidos"
            }
            if ( $filterP->PR_FlagTerminado == 2 && $filterP->PEDIP_Codigo != 0 && $filterP->PEDIP_Codigo != NULL ){
                $this->lib_props->sendMail(61, $idProduccion, "CAMBIO DE ESTADO PARA PEDIDO EN PRODUCCION", "LA PRODUCCION DEL PEDIDO HA INICIADO", "PR"); # MENU 61 = "Órdenes de Pedidos"
            }
        }

        $json = array(
                        "result" => "success",
                        "redirect" => base_url()."index.php/almacen/produccion/editar_produccion/$idProduccion"
                    );
        echo json_encode($json);
        exit();
    }

    public function editar_produccion( $idProduccion ){
        
        $tipo_oper = "V";
        $tipo_docu = "PR";
        $tipoDocumento = 'PRODUCCION';
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        $filter->codigo = $idProduccion;
        $produccion = $this->producto_model->obtenerProduccion($filter);
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";

            $data['compania'] = $compania;
            $data['codigo'] = $idProduccion;

            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/modificar_produccion";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmProduccion";
            $data['oculto'] = $oculto;
            $data["modo"] = "editar";
            $data['igv'] = 0;
            $data['igv_default'] = 0;
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            
            $data['detalle_comprobante'] = array();
            $data['focus'] = "";
            $data['hidden'] = "";
            $data['ordencompra'] = "";
            $data['dRef'] = "";
            $data['estado'] = "2";
            $data['numeroAutomatico'] = 1;
            
            $data['produccion'] = $idProduccion;
            $data['serie_suger_f'] = $produccion[0]->PR_Serie;
            $data['numero_suger_f'] = $produccion[0]->PR_Numero;

            $data['serieNumeroPedido'] = $produccion[0]->serieNumeroPedido;

            $compania = ( $produccion[0]->compania == "" || $produccion[0]->compania == NULL ) ? $this->somevar['compania'] : $produccion[0]->compania;
            $comppName = $this->pedido_model->nameEstablecimiento($compania);
            $data['establecimiento'] = $comppName[0]->EESTABC_Descripcion;

            $data['pedido'] = $produccion[0]->PEDIP_Codigo;
            $data['fechaI'] = mysql_to_human($produccion[0]->PR_FechaRecepcion);
            $data['fechaF'] = mysql_to_human($produccion[0]->PR_FechaFinalizado);
            $data['observacion'] = $produccion[0]->PR_Observacion;
            $data['flagTerminado'] = $produccion[0]->PR_FlagTerminado;
            
            $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
                        
            $listaGuiarem = array();
            $listaGuiarem = null;
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));

            $data["insumos"] = $this->insumosPorProduccion($idProduccion);
            $this->layout->view('almacen/produccion_nueva', $data);
    }

    public function modificar_produccion(){
        $idProduccion = $this->input->post('idProduccion');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $pedido = $this->input->post('docReferencia');
        $fecha = explode("/", $this->input->post('fecha'));
        $fechaF = explode("/", $this->input->post('fechaF'));
        $observacion = $this->input->post('observacion');
        $estado = $this->input->post('estatus');

        $filterP = new stdClass();
        $filterP->PR_Serie = $serie;
        $filterP->PR_Numero = $numero;
        $filterP->PEDIP_Codigo = $pedido;
        $filterP->PR_FechaRecepcion = "$fecha[2]-$fecha[1]-$fecha[0]";
        $filterP->PR_FechaFinalizado = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
        $filterP->PR_Observacion = $observacion;
        $filterP->PR_FlagTerminado = $estado;
        $filterP->PR_FlagEstado = "1";

        
        $prodcodigo = $this->input->post('prodcodigo');
        $prodcantidad = $this->input->post('prodcantidad');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');
        $proddescri = $this->input->post('proddescri');

        $recetasALL = true;
        $stockALL = true;
        $almacen = 3; #$this->almacen_model->obtenerAlmacenCompania(3); // almacen - taller

        if (is_array($detacodi)) {
            foreach($detacodi as $indiceV => $valueV){ // VERIFICO QUE TODOS LOS PRODUCTOS TENGAN RECETA ASOCIADA
                $detalle_accion = $detaccion[$indiceV];
                $filterPD = new stdClass();
                $filterPD->PROD_Codigo = $prodcodigo[$indiceV];
                $filterPD->PRD_Cantidad = $prodcantidad[$indiceV];

                if ( $filterP->PR_FlagTerminado == 1 && $detalle_accion != 'e'){
                    $searchRecetas = $this->producto_model->obtenerRecetaProducto($filterPD->PROD_Codigo, $almacen);
                    if ($searchRecetas == NULL){
                        $recetasALL = false;
                    }
                    else{
                        foreach ($searchRecetas as $fila => $columna) {
                            if ( $columna->RECDET_Cantidad * $filterPD->PRD_Cantidad > $columna->stock ){
                                $stockALL = false;
                            }
                        }
                    }
                }
            }

            if ($recetasALL == false)
                exit('{"result":"error", "msg":"Existen articulos en que no pueden ser procesados por no tener receta asociada.\nDebe Agregar una receta para cada articulo que desee producir."}');

            if ($stockALL == false)
                exit('{"result":"warning", "msg":"El stock de insumos actual, no cubre la demanda de articulos a producir."}');
            
            if ($recetasALL == true && $stockALL == true){ # SI TODOS TIENEN RECETA ASOCIADA Y LOS INSUMOS EN EL ALMACEN CUBREN LA DEMANDA
                if ( $filterP->PR_FlagTerminado == 1 ){ # SI ESTA FINALIZADO 1 CREA LA GUIA DE SALIDA PARA PRODUCTOS DE LA RECETA y GUIA DE INGRESO PARA PRODUCTO FINAL
                    $filterGuiain = new stdClass(); # GUIA DE ENTRADA
                    $filterGuiain->TIPOMOVP_Codigo = 6;
                    $filterGuiain->ALMAP_Codigo = 3; # ALMACEN PRODUCCION
                    $filterGuiain->DOCUP_Codigo = 1; # ORDEN DE PEDIDO 1
                    $filterGuiain->GUIAINC_Fecha = date('Y-m-d');
                    $filterGuiain->USUA_Codigo = $_SESSION['user'];
                    $guiin_id = $this->guiain_model->insertar($filterGuiain);

                    $filterGuiasa = new stdClass();
                    $filterGuiasa->TIPOMOVP_Codigo = 6;
                    $filterGuiasa->GUIASAC_TipoOperacion = "PR"; # PRODUCCION
                    $filterGuiasa->ALMAP_Codigo = 3; # ALMACEN PRODUCCION
                    $filterGuiasa->USUA_Codigo = $_SESSION['user'];
                    $filterGuiasa->CLIP_Codigo = ""; # CLIENTE
                    $filterGuiasa->PROVP_Codigo = ""; # PROVEEDOR
                    $filterGuiasa->DOCUP_Codigo = $idProduccion; # ORDEN DE PEDIDO 1
                    $filterGuiasa->GUIASAC_Fecha = date('Y-m-d');
                    $filterGuiasa->GUIASAC_FechaRegistro = date('Y-m-d h:i:s');
                    $filterGuiasa->GUIASAC_FechaModificacion = date('Y-m-d h:i:s');
                    $filterGuiasa->GUIASAC_Observacion = "DESCUENTO DE ARTICULOS POR PRODUCCIÓN";
                    $filterGuiasa->GUIASAC_FlagEstado = "1";
                    $guiasa_id = $this->guiasa_model->insertar($filterGuiasa);

                    # SI PROVIENE DE UN PEDIDO, SE GENERA LA GUIA DE TRANSFERENCIA
                    if ( $pedido > 0 ){
                        $detaPedido = $this->pedido_model->obtener_pedido($pedido);
                        
                        $data_confi1 = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], 15);
                        $serie = $data_confi1[0]->CONFIC_Serie;
                        $numero = $data_confi1[0]->CONFIC_Numero + 1;
                        $almacen_destino = $this->almacen_model->obtenerAlmacenCompania($detaPedido[0]->COMPP_Codigo);

                        $filter = new stdClass();
                        $filter->GTRANC_Serie = $serie;
                        $filter->GTRANC_Numero = $numero;
                        $filter->GTRANC_CodigoUsuario = $_SESSION['user'];

                        $filter->GTRANC_AlmacenOrigen = $almacen;
                        $filter->GTRANC_AlmacenDestino = $almacen_destino;
                        $filter->GTRANC_Fecha = date("Y-m-d");
                        $filter->GTRANC_Observacion = "ENVIO DESDE EL TALLER. PEDIDO FINALIZADO.";
                        $filter->GTRANC_Placa = "";
                        $filter->GTRANC_Licencia = "";
                        $filter->GTRANC_Chofer = "";
                        $filter->EMPRP_Codigo = NULL;
                        $filter->COMPP_Codigo = $this->somevar['compania'];
                        $filter->USUA_Codigo = $this->somevar['user'];
                        $filter->PEDIP_Codigo = $pedido;
                        $filter->GTRANC_EstadoTrans = 0;
                        $filter->GTRANC_FlagEstado = 1;
                        $guiatrans_id = $this->guiatrans_model->insertar($filter);

                        $this->configuracion_model->modificar_configuracion($this->somevar['compania'], 15, $numero, $serie);
                    }
                }

                foreach($detacodi as $indice => $value){
                    $detalle_accion = $detaccion[$indice];

                    $filterPD = new stdClass();
                    $filterPD->PR_Codigo = $idProduccion;
                    $filterPD->PROD_Codigo = $prodcodigo[$indice];
                    $filterPD->PRD_Cantidad = $prodcantidad[$indice];

                    if ($detalle_accion == 'n') {
                        $filterPD->PRD_FlagEstado = "1";
                        $this->producto_model->insertarProductoProduccion($filterPD);
                    }
                    else
                        if ($detalle_accion == 'm') {
                            $filterPD->PRD_FlagEstado = "1";
                            $this->producto_model->modificarProductoProduccion($value, $filterPD);
                        }
                    else
                        if ($detalle_accion == 'e') {
                            $filterPD->PRD_FlagEstado = "0";
                            $this->producto_model->modificarProductoProduccion($value, $filterPD);
                        }
                    
                    if ( $filterP->PR_FlagTerminado == 1 ){
                        $this->inventario_model->confirmInventariado($prodcodigo[$indice], 3); # VERIFICO SI EL PRODUCTO ESTA INVENTARIADO, SI NO LO ESTA, LO INGRESO AL INVENTARIO CON STOCK 0
                        if ($detalle_accion == 'n' || $detalle_accion == 'm'){ // SI ESTA FINALIZADA 1 INGRESA LOS ARTICULOS ACTIVOS
                            $filterIngreso = new stdClass();
                            $filterIngreso->GUIAINP_Codigo = $guiin_id;
                            $filterIngreso->PRODCTOP_Codigo = $prodcodigo[$indice];
                            $filterIngreso->UNDMED_Codigo = 1; #$produnidad[$indice];
                            $filterIngreso->GUIAINDETC_Cantidad = $prodcantidad[$indice];
                            $filterIngreso->GUIAINDETC_Costo = 0;
                            $filterIngreso->GUIIAINDETC_GenInd = 'G';
                            $filterIngreso->ALMAP_Codigo = 3; // ALMACEN - TALLER
                            $insertGuiain = $this->guiaindetalle_model->insertar_2015($filterIngreso, 'INGRESO POR PRODUCCIÓN', $idProduccion, 6);

                            # SALIDA DE INSUMOS
                            $insumosReceta = $this->producto_model->obtenerRecetaProducto($filterPD->PROD_Codigo);
                            foreach ($insumosReceta as $key => $val) {
                                $filterSalida = new stdClass();
                                $filterSalida->GUIASAP_Codigo = $guiasa_id;
                                $filterSalida->PRODCTOP_Codigo = $val->PROD_CodigoInsumo; #$prodcodigo[$indice];
                                $filterSalida->UNDMED_Codigo = 1; #$produnidad[$indice];
                                $filterSalida->GUIASADETC_GenInd = 'G';
                                $filterSalida->GUIASADETC_Cantidad = $val->RECDET_Cantidad * $filterPD->PRD_Cantidad; #$prodcantidad[$indice];
                                $filterSalida->GUIASADETC_Costo = 0;
                                $filterSalida->GUIASADETC_Descripcion = $val->nombre_producto; #$proddescri[$indice];
                                $filterSalida->GUIASADETC_FechaRegistro = date("Y-m-d h:i:s");
                                $filterSalida->GUIASADETC_FlagEstado = "1";
                                $filterSalida->ALMAP_Codigo = 3; // ALMACEN - TALLER
                                $insertGuiasa = $this->guiasadetalle_model->insertar_2015($filterSalida, $idProduccion);
                            }
                            # FIN SALIDA DE INSUMOS

                            # SI PROVIENE DE UN PEDIDO INGRESA ARTICULOS A LA G. DE TRANSFERENCIA GENERADA
                            if ( $pedido > 0 ){
                                $filter2 = new stdClass();
                                $filter2->GTRANP_Codigo = $guiatrans_id;
                                $filter2->PROD_Codigo = $prodcodigo[$indice];
                                $filter2->UNDMED_Codigo = 1; #$produnidad[$indice];
                                $filter2->GTRANDETC_Cantidad = $prodcantidad[$indice];
                                $filter2->GTRANDETC_Costo = 0;
                                $filter2->GTRANDETC_GenInd = 'G';
                                $filter2->GTRANDETC_Descripcion = $proddescri[$indice];
                                $filter2->GTRANDETC_FlagEstado = 1;
                                $this->guiatransdetalle_model->insertar($filter2);
                            }
                        }
                    }
                }

                $this->producto_model->actualizarProduccion($idProduccion, $filterP);
                if ( $filterP->PR_FlagTerminado == 1 && $filterP->PEDIP_Codigo != 0 && $filterP->PEDIP_Codigo != NULL ){
                    $this->lib_props->sendMail(61, $idProduccion, "CAMBIO DE ESTADO PARA PEDIDO EN PRODUCCION", "LA PRODUCCION DEL PEDIDO HA FINALIZADO", "PR"); # MENU 61 = "Órdenes de Pedidos"
                }

                if ( $filterP->PR_FlagTerminado == 2 && $filterP->PEDIP_Codigo != 0 && $filterP->PEDIP_Codigo != NULL ){
                    $this->lib_props->sendMail(61, $idProduccion, "CAMBIO DE ESTADO PARA PEDIDO EN PRODUCCION", "LA PRODUCCION DEL PEDIDO HA INICIADO", "PR"); # MENU 61 = "Órdenes de Pedidos"
                }
            }
        }
        exit('{"result":"success","redirect":"'.base_url().'index.php/almacen/produccion/produccion_index"}');
        #header("Location:".base_url()."index.php/almacen/produccion/produccion_index");
    }

    public function insumosPorProduccion($idProduccion){

        $detallesInfo = $this->producto_model->detallesProduccion($idProduccion);
        $insumosInfo = NULL;

        if ($detallesInfo != NULL){
            foreach ($detallesInfo as $row => $col) {
                $insumos = $this->producto_model->obtenerRecetaProducto($col->PROD_Codigo);

                $insumosInfo[$row]["produccion"] = $col->PRD_Cantidad;
                $insumosInfo[$row]["articulo"] = $col->PROD_Nombre;
                $insumosInfo[$row]["insumos"] = $insumos;
            }
        }
        return $insumosInfo;
    }

    public function generarOrdenCompra(){

        $insumosInfo = $this->producto_model->getInsumosRequired();
        $datosEmpresa =  $this->empresa_model->obtener_datosEmpresa( 4 ); # TALLER

        $new = true;
        $items = 0;

        if ($insumosInfo != NULL){
            foreach ($insumosInfo as $row => $col) {
                if ( $col->insumosFaltantes > 0 ){ # SI INSUMOS FALTANTES ES NEGATIVO, HAY STOCK SUFICIENTE.
                    if ( $new == true){
                        $tipo_oper = "C";
                        
                        $filter = new stdClass();
                        $filter->OCOMC_TipoOperacion = $tipo_oper;
                        $filter->OCOMC_CodigoUsuario = "";
                        $filter->OCOMC_Serie = "OC";
                        $filter->OCOMC_Entrega = "";

                        $filter->PROVP_Codigo = $_SESSION['empresa'];
                        $filter->COTIP_Codigo = NULL;

                        $filter->OCOMP_CodigoVenta = "";
                        $filter->PROYP_Codigo = "";
                        $filter->MONED_Codigo = "1";

                        $filter->OCOMC_descuento100 = "0";
                        $filter->OCOMC_PersonaAutorizada = ""; # GUARDA LA ORDEN DE COMPRA DE LA EMPRESA EN EL CAMPO OCOMC_PersonaAutorizada
                        $filter->OCOMC_MiPersonal = ""; # VENDEDOR
                        $filter->OCOMC_Personal = ""; # CONTACTO DE LA EMPRESA A QUIEN SE COTIZA

                        $filter->OCOMC_igv100 = "18";
                        $filter->OCOMC_igv = "";

                        $filter->OCOMC_subtotal = "0";
                        $filter->OCOMC_descuento = "0";
                        $filter->OCOMC_total = "0";
                        $filter->OCOMC_percepcion = "0";
                        $filter->CENCOSP_Codigo = "";
                        $filter->OCOMC_Observacion = "";
                        $filter->ALMAP_Codigo = "";

                        $filter->FORPAP_Codigo = "1";

                        $filter->OCOMC_EnvioDireccion = $datosEmpresa[0]->EMPRC_Direccion;
                        $filter->OCOMC_FactDireccion = $datosEmpresa[0]->EMPRC_Direccion;

                        $filter->OCOMP_TDC = "2";
                        $filter->OCOMP_TDC_opcional = "";

                        $filter->OCOMC_Fecha = date("Y-m-d");
                        $filter->OCOMC_FechaEntrega = "";

                        $filter->OCOMC_CtaCteSoles = "";
                        $filter->OCOMC_CtaCteDolares = "";
                        $filter->OCOMC_FlagEstado = 2;
                        $filter->CPC_TipoDocumento = "F";

                        #datos correlativo
                        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], 3);
                        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;
                        $filter->OCOMC_Numero = $numero;

                        $ocompra = $this->ocompra_model->insertar_ocompra($filter);
                        $this->configuracion_model->modificar_configuracion($this->somevar['compania'], 3, $numero);

                        $items += 29;
                        $new = false;
                    }

                    $filter = new stdClass();
                    $filter->OCOMP_Codigo = $ocompra;
                    $filter->PROD_Codigo = $col->PROD_Codigo;
                    $filter->OCOMDEC_Cantidad = $col->insumosFaltantes;
                    $filter->OCOMDEC_Pendiente = $col->insumosFaltantes;
                    $filter->UNDMED_Codigo = $col->UNDMED_Codigo;
                    $filter->AFECT_Codigo = ($col->AFECT_Codigo != '' && $col->AFECT_Codigo != 0) ? $col->AFECT_Codigo : 1;

                    $filter->OCOMDEC_Pendiente_pago = $filter->OCOMDEC_Cantidad;
                    
                    $filter->OCOMDEC_Descuento100 = 0;
                    $filter->OCOMDEC_Igv100 = 18;
                    $filter->OCOMDEC_Pu = 0;
                    $filter->OCOMDEC_Subtotal = 0;
                    $filter->OCOMDEC_Descuento = 0;
                    $filter->OCOMDEC_Descuento2 = 0;
                    $filter->OCOMDEC_Igv = 0;
                    $filter->OCOMDEC_Total = 0;
                    $filter->OCOMDEC_Pu_ConIgv = 0;
                    $filter->OCOMDEC_Costo = 0;
                    $filter->OCOMDEC_Descripcion = $col->PROD_Nombre;
                    $filter->OCOMDEC_GenInd = "G";

                    $filter->OCOMDEC_Observacion = "";
                    $filter->OCOMDEC_flete = 0;

                    //incluimos el id de OV para poder identificar a que cliente y proyecto le corresponde
                    $this->ocompradetalle_model->insertar($filter);

                    if ( $items == $row )
                        $new = true;
                }
            }

            if ( isset($ocompra) && $ocompra > 0 ){
                $success = array(
                                    "result" => "success",
                                    "msg" => "Orden de compra generada."
                                );
                $this->lib_props->sendMail(20, $ocompra); # MENU 20 = "Ordenes de Compras"
            }
            else{
                $success = array(
                                    "result" => "warning",
                                    "msg" => "La OC no fue generada. Verifique que su stock de insumos actual cubra la cantidad solicitada en las ordenes de producción."
                                );
            }
        }
        else{
            $success = array(
                                "result" => "warning",
                                "msg" => "¡La OC para las ordenes de producción generadas, ya ha sido emitida!"
                            );
        }


        echo json_encode($success);
    }

    public function produccion_pdf( $id, $img = 1, $correo = false ){
        $this->lib_props->produccion_pdf( $id, $img, $correo );
    }

    public function insertar_comprobante($codigoOC){
        $data = "";

        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigoOC);
        $compania = $datos_ocompra[0]->COMPP_Codigo;
        $this->load->model("maestros/compania_model");
        
        $datos_ocompra = $this->ocompra_model->obtener_ocompra($codigoOC);
        $this->load->model("maestros/compania_model");

        ##########################################################################
        ########### OC DETALLE GENERAL
        ##########################################################################
            $compania = $datos_ocompra[0]->COMPP_Codigo;
            $tipo_oper = $datos_ocompra[0]->OCOMC_TipoOperacion;
            $tipo_docu = $datos_ocompra[0]->CPC_TipoDocumento;
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

        ##########################################################################
        ########### COMPROBANTE DETALLE GENERAL
        ##########################################################################
            $filter = new stdClass();
            $filter->CPC_TipoOperacion = $tipo_oper;
            $filter->CPC_TipoDocumento = $tipo_docu;
            $filter->ALMAP_Codigo = $almacen;
            $filter->CPC_NumeroAutomatico = 1;
            $filter->CPC_Fecha = $fecha;
            $filter->CPC_Hora = $hora;
            $filter->CPC_Observacion = $datos_ocompra[0]->OCOMC_Observacion;
            $filter->CPC_FlagEstado = 2; # POR APROBAR
            
            $this->load->model('maestros/documento_model');
            $documento = $this->documento_model->obtenerAbreviatura(trim($tipo_docu));
            $tipo = $documento[0]->DOCUP_Codigo;

            $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo); // Consulta el ultimo correlativo
            $filter->CPC_Serie = $configuracion_datos[0]->CONFIC_Serie; // Serie de factura/Boleta/Comprobante
            $filter->CPC_Numero = $configuracion_datos[0]->CONFIC_Numero + 1; // Incrementa en numero el ultimo correlativo consultado para ingresar.
            
            $cSerie = $configuracion_datos[0]->CONFIC_Serie;
            $cNumero = $configuracion_datos[0]->CONFIC_Numero + 1;

            $filter->CLIP_Codigo = $cliente;
            $filter->CPC_Direccion = $direccion;
            $filter->PROVP_Codigo = $proveedor;

        #####################################
        ####### PAGO Y TOTAL
        #####################################
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
        
        #####################################
        ####### OTROS DETALLES
        #####################################
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

        $mueve = $comp_confi[0]->COMPCONFIC_StockComprobante;
        $comprobante = $this->comprobante_model->insertar_comprobante($filter);

        ##########################################################################
        ####### ASOCIAMOS LA GUIA DE REMISION SI EXISTE
        ##########################################################################
            if ($datos_ocompra[0]->GUIAREMP_Codigo != NULL){
                $filterCG = new stdClass();
                $filterCG->CPP_Codigo = $comprobante;
                $filterCG->GUIAREMP_Codigo = $datos_ocompra[0]->GUIAREMP_Codigo;
                $filterCG->COMPGUI_FlagEstado = 1;
                $filterCG->COMPGU_FechaRegistro = date("Y-m-d H:i:s");
                $this->comprobante_model->insertar_comprobante_guiarem($filterCG);
            }

        ##########################################################################
        ########### ARTICULOS
        ##########################################################################

        $detalle = $this->ocompra_model->obtener_detalle_ocompra($codigoOC);
        $detalle_ocompra = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detocompra = $valor->OCOMDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $descripcion = $valor->OCOMDEC_Descripcion;
                $observacionDet = $valor->OCOMDEC_Observacion;
                $unidad = $valor->UNDMED_Codigo;
                $lote = $valor->LOTP_Codigo;
                $tafectacion = $valor->AFECT_Codigo;
                $detflag = $valor->OCOMDEC_GenInd;
                $cantidad = $valor->OCOMDEC_Cantidad;
                $costo = $valor->OCOMDEC_Costo;
                $pu = $valor->OCOMDEC_Pu;
                $subtotal = $valor->OCOMDEC_Subtotal;
                $descuento = $valor->OCOMDEC_Descuento;
                $descuento100 = $valor->OCOMDEC_Descuento100;
                $igv = $valor->OCOMDEC_Igv;
                $igv100 = $valor->OCOMDEC_Igv100;
                $pu_conigv = $valor->OCOMDEC_Pu_ConIgv;
                $total = $valor->OCOMDEC_Total;
            
                $filterDet = new stdClass();
                $filterDet->CPP_Codigo = $comprobante;
                $filterDet->PROD_Codigo = $producto;
                $filterDet->UNDMED_Codigo = $unidad;
                $filterDet->LOTP_Codigo = $lote;
                $filterDet->AFECT_Codigo = $tafectacion;
                $filterDet->CPDEC_Cantidad = $cantidad;
                $filterDet->CPDEC_Pu = $pu;
                $filterDet->CPDEC_Subtotal = $subtotal;
                $filterDet->CPDEC_Descuento = $descuento;
                $filterDet->CPDEC_Igv = $igv;
                $filterDet->CPDEC_Total = $total;
                $filterDet->CPDEC_Pu_ConIgv = $pu_conigv;
                $filterDet->CPDEC_Descuento100 = $descuento100;
                $filterDet->CPDEC_Igv100 = $igv100;
                $filterDet->ALMAP_Codigo = $almacen;
                $filterDet->CPDEC_Costo = $costo;
                $filterDet->CPDEC_GenInd = $detflag;
                $filterDet->CPDEC_Descripcion = $descripcion;
                $filterDet->CPDEC_Observacion = $observacionDet;
                $this->comprobantedetalle_model->insertar($filterDet);
            }
        }

        $success = array(
                            "result" => "success",
                            "pdf" => "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete($comprobante, $tipo, \"a4\")'> <span style='font-weight: bold; font-size: 7pt; color:green'>► $cSerie-$cNumero</span> </a>"
                        );

        echo json_encode($success);
    }

    #*****************************************************
    #****** PROGRAMACIÓN DE DESPACHO
    #*****************************************************

    public function despacho_index( $j = '0', $fecha = NULL ){
        
        $data['action'] = base_url() . "index.php/almacen/produccion/produccion_index";

        $fechai = date("Y") - 1;
        $fechaf = date("Y") + 1;

        $filterl = new stdClass();
        $filterl->fechai = ($fecha != NULL) ? $fecha : NULL;

        $filter = new stdClass();
        $filter->fechai = "$fechai-12-1";
        $filter->fechaf = "$fechaf-01-31";

        $data['registros'] = count($this->producto_model->listar_despacho($filterl));
        $conf['base_url'] = site_url('almacen/produccion/despacho_index');
        $conf['per_page'] = 10;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        
        $listar = $this->producto_model->listar_despacho($filterl, $conf['per_page'], $offset);
        $calendarioListar = $this->producto_model->calendario_despacho($filter);
        $listarecetas = '';
        $item   = $j + 1;
        $lista  = array();
        $calendario  = array();
        
        if( count($listar) > 0 ){
            foreach ($listar as $indice => $valor){
                switch ($valor->DESC_Entregado) {
                    case '1':
                        $estado = "<span style='background:green; border-radius: 0.5em; color:black; font-weight:bold; font-size: 8pt; text-align:center; display:block; width:10em; padding:0.3em;'>RECIBIDO</span>";
                        break;
                    case '2':
                        $estado = "<span style='background:orange; border-radius: 0.5em; color:black; font-weight:bold; font-size: 8pt; text-align:center; display:block; width:10em; padding:0.3em;'>TRANSITO</span>";
                        break;
                    case '3':
                        $estado = "<span style='background:gold; border-radius: 0.5em; color:black; font-weight:bold; font-size: 8pt; text-align:center; display:block; width:10em; padding:0.3em;'>DESPACHADO</span>";
                        break;
                    case '4':
                        $estado = "<span style='background:yellow; border-radius: 0.5em; color:black; font-weight:bold; font-size: 8pt; text-align:center; display:block; width:10em; padding:0.3em;'>POR DESPACHAR</span>";
                        break;
                    
                    default:
                        $estado = "";
                        break;
                }

                $editar = "<a href='#' onclick='editar_despacho(\"$valor->DESP_Codigo\")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $pdf1 = "<a href='#' onclick='ver_despacho($valor->DESP_Codigo,\"0\")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Ver'></a>";
                $pdf2 = "<a href='#' onclick='ver_despacho($valor->DESP_Codigo,\"1\")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver'></a>";
                $lista[] = array( $item, $valor->DESC_Serie, $valor->DESC_Numero, $valor->DESC_FechaDespacho, $editar, $pdf1, $pdf2, $estado);
                $item++;
            }
        }

        if( count($calendarioListar) > 0 ){
            foreach ($calendarioListar as $indice => $valor){
                $cantidad = ($valor->cantidad == NULL) ? 0 : $valor->cantidad;
                $calendario[] = array( $item, $valor->DESC_FechaDespacho, $cantidad);
            }
        }

        $data['lista'] = $lista;
        $data['calendario'] = $calendario;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/despacho_index',$data);
    }

    public function consultarCalendario(){
        $calendarioListar = $this->producto_model->calendario_despacho('');
        $calendario  = array();
        
        if( count($calendarioListar) > 0 ){
            foreach ($calendarioListar as $indice => $valor){
                $cantidad = ($valor->cantidad == NULL) ? 0 : $valor->cantidad;
                $calendario[] = array( $item, $valor->DESC_FechaDespacho, $cantidad);
            }
        }

        $json = "";
        if (count($calendario) > 0) {
            $j = 0;
            foreach ($calendario as $indice => $despacho) {
                if ($j > 0)
                    $json .= ",";
                $json .= "'$despacho[1]' : {'number': $despacho[2], 'url': 'despacho_index/0/$despacho[1]'}";
                $j++;
            }
        }


        /*$(document).ready(function () {
            $(".responsive-calendar").responsiveCalendar({
              time: '2019-12',
              events: { "2019-12-04" : {"number": 1, "url": "despacho_index/0/2019-12-04"} }
            });
        });*/

        echo json_encode($json);
    }

    public function despacho_nueva( $tipo_oper = NULL ){
        $tipo_oper = "V";
        $tipo_docu = "DP"; # DESPACHO
        $tipoDocumento = 'DESPACHO'; #DESPACHO
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

            
            // Variables
            $compania = $this->somevar['compania'];
            $codigo = "";

            $comp_confi = $this->companiaconfiguracion_model->obtener($compania);
            
            $data['compania'] = $compania;
            //Para cambio comprobante_A
            $data['cambio_comp'] = "0";
            $data['total_det'] = "0";   
            $data['codigo'] = $codigo;

            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");

            $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/insertar_despacho";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmDespacho";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $data['usa_adelanto'] = 0;
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['guia'] = "";
            $data['cboproyecto'] = ""; #$this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');
            $data['cboimportacion'] = ""; #$this->OPTION_generador($this->importacion_model->listar_importacion(0), 'IMPOR_Codigo', 'IMPOR_Nombre', '2');
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboMoneda'] = ""; #$this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
            $data['cboFormaPago'] = ""; #$this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '1');
            $data['cboPresupuesto'] = ""; #$this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            #$data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
            $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            $data['direccionsuc'] = form_input(array("name" => "direccionsuc", "id" => "direccionsuc", "class" => "cajaGeneral", "size" => "40", "maxlength" => "250", "value" => $punto_llegada));
            
            $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

            if (count($cambio_dia) > 0) {
                $data['tdcDolar'] = 0; #$cambio_dia[0]->TIPCAMC_FactorConversion;
            } else {
                $data['tdcDolar'] = 0;
            }
            
            $data['serie'] = '';
            $data['numero'] = '';
            $data['flagTerminado'] = 3;

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
            $data['estado'] = "4";
            $data['numeroAutomatico'] = 1;
            $data['isProvieneCanje'] =false;
            $data['oc_cliente'] = "";
            
            $data['fechaI'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $data['fechaF'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
            //obtengo las series de la configuracion
            
            if ($tipo_docu == 'DP') {
                $tipo = 21;
            }

            $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $data['serie_suger_f'] = $configuracion_datos[0]->CONFIC_Serie;
            $data['numero_suger_f'] = $this->lib_props->getOrderNumeroSerie($configuracion_datos[0]->CONFIC_Numero + 1);
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('almacen/despacho_nueva', $data);
    }

    public function despacho_editar( $codigo ){
        $tipo_oper = "V";
        $tipo_docu = "DP"; # DESPACHO
        $tipoDocumento = 'DESPACHO'; #DESPACHO
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

            
            // Variables
            $compania = $this->somevar['compania'];
            $comp_confi = $this->companiaconfiguracion_model->obtener($compania);

            #************************************
            #******** DATOS DEL DESPACHO
            #************************************
                $datos_despacho = $this->producto_model->obtener_despacho($codigo);
                $data['detalles_despacho'] = $this->producto_model->obtener_detalles_despacho($codigo);
            
            
            $data['compania'] = $compania;
            $data['cambio_comp'] = "0";
            $data['total_det'] = "0";   
            $data['codigo'] = $codigo;

            $data['cboObra'] = form_dropdown("obra", array('' => ':: Seleccione ::'), "", " class='comboGrande'  id='obra'");

            $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv == '1') ? true : false);
            $oculto = form_hidden(array('codigo' => $codigo, 'base_url' => base_url(), 'tipo_oper' => $tipo_oper, 'tipo_docu' => $tipo_docu, 'contiene_igv' => ($data['contiene_igv'] == true ? '1' : '0')));
            $data['url_action'] = base_url() . "index.php/almacen/produccion/modificar_despacho";
            $data['titulo'] = "REGISTRAR " . $tipoDocumento;
            $data['tit_imp'] = $tipoDocumento;
            $data['tipo_docu'] = $tipo_docu;
            $data['tipo_oper'] = $tipo_oper;
            $data['formulario'] = "frmDespacho";
            $data['oculto'] = $oculto;
            $data["modo"] = "insertar";
            $data['usa_adelanto'] = 0;
            $lista_almacen = $this->almacen_model->seleccionar();
            $data['guia'] = "";
            $data['cboproyecto'] = ""; #$this->OPTION_generador($this->proyecto_model->listar_proyectos(), 'PROYP_Codigo', 'PROYC_Nombre', '1');
            $data['cboimportacion'] = ""; #$this->OPTION_generador($this->importacion_model->listar_importacion(0), 'IMPOR_Codigo', 'IMPOR_Nombre', '2');
            $data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, obtener_val_x_defecto($lista_almacen), " class='comboMedio' style='width:auto;' id='almacen'");
            $data['cboMoneda'] = ""; #$this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
            $data['cboFormaPago'] = ""; #$this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '1');
            $data['cboPresupuesto'] = ""; #$this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            #$data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero', 'nombre'), '', array('', '::Seleccione::'), ' - ');
            $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo', 'nombre'), '', array('', '::Seleccione::'), ' / ');
            $data['cboVendedor'] = $this->OPTION_generador($this->directivo_model->listar_directivo_personal(), 'DIREP_Codigo', array('PERSC_ApellidoPaterno', 'PERSC_ApellidoMaterno', 'PERSC_Nombre'), '', array('', '::Seleccione::'), ' ');
            $data['direccionsuc'] = form_input(array("name" => "direccionsuc", "id" => "direccionsuc", "class" => "cajaGeneral", "size" => "40", "maxlength" => "250", "value" => $punto_llegada));
            
            $cambio_dia = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));

            if (count($cambio_dia) > 0) {
                $data['tdcDolar'] = 0; #$cambio_dia[0]->TIPCAMC_FactorConversion;
            } else {
                $data['tdcDolar'] = 0;
            }
            
            $data['serie'] = $datos_despacho[0]->DESC_Serie;
            $data['numero'] = $datos_despacho[0]->DESC_Numero;
            $data['flagTerminado'] = 3;

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
            $data['estado'] = $datos_despacho[0]->DESC_Entregado;
            $data['numeroAutomatico'] = 1;
            $data['isProvieneCanje'] =false;
            $data['oc_cliente'] = "";
            
            #$fechaRegistro = explode(" ",$datos_despacho[0]->DESC_FechaRegistro);
            #$data['fechaI'] = mysql_to_human($fechaRegistro[0]);
            $data['fechaI'] = mysql_to_human($datos_despacho[0]->DESC_FechaDespacho);
            $atributos = array('width' => 700, 'height' => 450, 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
            $contenido = "<img height='16' width='16' src='" . base_url() . "images/ver.png' title='Buscar Cliente' border='0'>";
            $data['vercliente'] = anchor_popup('empresa/cliente/ventana_busqueda_cliente', $contenido, $atributos, 'linkVerCliente');
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor', $contenido, $atributos, 'linkVerProveedor');
            $data['verproducto'] = anchor_popup('almacen/producto/ventana_busqueda_producto', $contenido, $atributos, 'linkVerProducto');
            //obtengo las series de la configuracion
            
            if ($tipo_docu == 'DP') {
                $tipo = 21;
            }

            $configuracion_datos = $this->configuracion_model->obtener_numero_documento($compania, $tipo);
            $data['serie_suger_f'] = $datos_despacho[0]->DESC_Serie;
            $data['numero_suger_f'] = $this->lib_props->getOrderNumeroSerie($datos_despacho[0]->DESC_Numero);
            $data['cmbVendedor'] = $this->select_cmbVendedor($this->session->set_userdata('codUsuario'));
            $this->layout->view('almacen/despacho_nueva', $data);
    }

     /**obtenemos la lista de guiaremision creadas por cliente o proveedor pero no ewstan asociadas a un comprobante **/
    public function ventana_muestra_guiarem($tipo_oper = 'V', $codigo = '', $select = '', $tipo_doc = '', $almacen = '', $comprobante = '',$tipoMoneda=''){
        //$this->output->enable_profiler(TRUE);
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $almacen_id = $almacen;

        #$filter = new stdClass();
        $lista_guiarem = $this->guiarem_model->no_asociados_all("V");
        $lista = array();
        foreach ($lista_guiarem as $indice => $value) {
            $ver = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->GUIAREMP_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $ir = "<a href='javascript:;' onclick=\"seleccionar_guiaremD('$value->GUIAREMP_Codigo', '$value->GUIAREMC_Serie', '$value->GUIAREMC_Numero', 'GR', '$value->EESTABC_Descripcion',)\"><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Guia de remision " . $value->GUIAREMC_Serie . " - " . $value->GUIAREMC_Numero . "' /></a>";
            $lista[] = array(mysql_to_human($value->GUIAREMC_Fecha), $value->GUIAREMC_Serie, $value->GUIAREMC_Numero, $value->numdoc, $value->nombre, $value->MONED_Simbolo . ' ' . number_format($value->GUIAREMC_total), $ver, $ir);
        }
        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen_id;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_doc'] = $tipo_doc;
        $data['despacho'] = true;
        $data['form_open'] = form_open(base_url() . "index.php/almacen/producto/ventana_muestra_guiarem", array("name" => "frmGuiarem", "id" => "frmGuiarem"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url()));
        $this->load->view('almacen/ventana_muestra_guiarem', $data);
    }

    public function ventana_muestra_gtrans($tipo_oper = 'V', $codigo = '', $select = '', $tipo_doc = '', $almacen = '', $comprobante = '',$tipoMoneda=''){
        $cliente = '';
        $nombre_cliente = '';
        $ruc_cliente = '';
        $proveedor = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        $almacen_id = $almacen;

        #$filter = new stdClass();
        $lista_guiarem = $this->guiatrans_model->listar_transferencias_pendientes(false); #false lista todas las companias
        $lista = array();
        foreach ($lista_guiarem as $indice => $value) {
            #$ver = "<a href='javascript:;' onclick='ver_detalle_documento(" . $value->GUIATRANS_Codigo . ")'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Ver Detalles'></a>";
            $ir = "<a href='javascript:;' onclick=\"seleccionar_gtrans('$value->GTRANP_Codigo', '$value->GTRANC_Serie', '$value->GTRANC_Numero', 'GT', '$value->EESTABC_Descripcion')\"><img src='" . base_url() . "images/ir.png' width='16' height='16' border='0' title='Guia de transferencia" . $value->GTRANC_Serie . " - " . $value->GTRANC_Numero . "' /></a>";
            $lista[] = array(mysql_to_human($value->GTRANC_Fecha), $value->GTRANC_Serie, $value->GTRANC_Numero, $value->EESTABC_Descripcion,"", "", $ir);
        }

        $data['lista'] = $lista;
        $data['cliente'] = $cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['ruc_cliente'] = $ruc_cliente;
        $data['proveedor'] = $proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['ruc_proveedor'] = $ruc_proveedor;
        $data['almacen'] = $almacen_id;
        $data['comprobante'] = $comprobante;
        $data['tipo_oper'] = $tipo_oper;
        $data['tipo_doc'] = $tipo_doc;
        $data['form_open'] = form_open(base_url() . "index.php/almacen/producto/ventana_muestra_gtrans", array("name" => "frmGtrans", "id" => "frmGtrans"));
        $data['form_close'] = form_close();
        $data['form_hidden'] = form_hidden(array("base_url" => base_url()));
        $this->load->view('almacen/ventana_muestra_gtrans', $data);
    }

    public function insertar_despacho(){
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $fecha = explode("/",$this->input->post('fecha'));

        $filterP = new stdClass();
        $filterP->DESC_Serie = $serie;
        $filterP->DESC_Numero = $numero;
        $filterP->COMPP_Codigo = $this->somevar['compania'];
        $filterP->DESC_FechaRegistro = date("Y-m-d h:i:s");
        $filterP->DESC_FechaDespacho = "$fecha[2]-$fecha[1]-$fecha[0]";
        $filterP->DESC_Entregado = $this->input->post('estado');
        $filterP->DESC_FlagEstado = "1";
        
        $idDespacho = $this->producto_model->insertarDespacho($filterP);
        
        $guia = $this->input->post('guia');
        $tipo = $this->input->post('tipo');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');

        if (is_array($detacodi)) {
            foreach($detacodi as $indice => $value){
                $detalle_accion = $detaccion[$indice];

                $filterPD = new stdClass();
                $filterPD->DESP_Codigo = $idDespacho;

                $filterPD->GUIAREMP_Codigo = ($tipo[$indice] == 'GR') ? $guia[$indice] : NULL;
                $filterPD->GTRANP_Codigo = ($tipo[$indice] == 'GT') ? $guia[$indice] : NULL;
                $filterPD->DESPD_FlagEstado = "1";

                if ( $detalle_accion != 'e' ){
                    $this->producto_model->insertarDespachoGuias($filterPD);
                }
            }
        }
        exit('{"result":"success","redirect":"'.base_url().'index.php/almacen/produccion/despacho_index"}');
    }

    public function modificar_despacho(){
        $idDespacho = $this->input->post('codigo');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');
        $fecha = explode("/",$this->input->post('fecha'));

        $filterP = new stdClass();
        $filterP->DESC_Serie = $serie;
        $filterP->DESC_Numero = $numero;
        $filterP->COMPP_Codigo = $this->somevar['compania'];
        $filterP->DESC_FechaRegistro = date("Y-m-d h:i:s");
        $filterP->DESC_FechaDespacho = "$fecha[2]-$fecha[1]-$fecha[0]";
        $filterP->DESC_Entregado = $this->input->post('estado');
        $filterP->DESC_FlagEstado = "1";
        
        $this->producto_model->actualizarDespacho($idDespacho, $filterP);
        
        $guia = $this->input->post('guia');
        $tipo = $this->input->post('tipo');
        $detacodi = $this->input->post('detacodi');
        $detaccion = $this->input->post('detaccion');

        if (is_array($detacodi)) {
            foreach($detacodi as $indice => $value){
                $detalle_accion = $detaccion[$indice];

                $filterPD = new stdClass();
                $filterPD->DESP_Codigo = $idDespacho;

                $filterPD->GUIAREMP_Codigo = ($tipo[$indice] == 'GR') ? $guia[$indice] : NULL;
                $filterPD->GTRANP_Codigo = ($tipo[$indice] == 'GT') ? $guia[$indice] : NULL;
                $filterPD->DESPD_FlagEstado = "1";

                if ( $detalle_accion == 'n' ){
                    $this->producto_model->insertarDespachoGuias($filterPD);
                }
                else
                    if ( $detalle_accion == 'e' ){
                        $filterPD->DESPD_FlagEstado = "2";
                        $this->producto_model->actualizarDespachoGuias($value, $filterPD);
                    }
            }
        }
        exit('{"result":"success","redirect":"'.base_url().'index.php/almacen/produccion/despacho_index"}');
    }

    public function despachoPdf( $id, $img = 1, $correo = false ){
        $this->lib_props->despacho_pdf( $id, $img, $correo );
    }

}

?>