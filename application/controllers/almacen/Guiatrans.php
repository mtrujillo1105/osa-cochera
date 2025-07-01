<?php

include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");

class Guiatrans extends CI_Controller
{

    private $_hoy;

    public function __construct()
    {
        parent::Controller();

        $this->load->model('almacen/guiatrans_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiain_model');

        $this->load->model('almacen/guiatransdetalle_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/tipomovimiento_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/companiaconfidocumento_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/marca_model');
        $this->load->model('almacen/seriedocumento_model');
        $this->load->helper('form', 'url');
        $this->load->helper('utf_helper');
        $this->load->helper('util_helper');
        $this->load->helper('my_almacen');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->library('lib_props');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['compania'] = $this->session->userdata('compania');
        date_default_timezone_set('America/Lima');
        $this->_hoy = mdate("%Y-%m-%d ", time());
    }

    public function listar($j = 0)
    {
        

        $data['fechai'] = '';
        $data['fechaf'] = '';
        $data['serie'] = '';
        $data['numero'] = '';
        $data['producto'] = '';
        $data['codproducto'] = '';
        $data['nombre_producto'] = '';

        // ALMACEN DESTINO
        $listado = $this->guiatrans_model->listar();
        $lista = array();
        $item = 1;
        foreach ($listado as $indice => $valor) {
            $codigo = $valor->GTRANP_Codigo;
            $fecha = mysql_to_human($valor->GTRANC_Fecha);
            $serie = $valor->GTRANC_Serie;
            $numero = $valor->GTRANC_Numero;
            $nombre_establec = $valor->EESTABC_DescripcionDest;
            $estado = $valor->GTRANC_FlagEstado;
            $estado_trans = $valor->GTRANC_EstadoTrans;
            $comporigen = $valor->GTRANC_AlmacenOrigen;
            $companiaOri = $valor->COMPP_CodigoOri;

            if ($estado == '1') {
                $img_estado = ""; #"<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />";
            } else {
                $img_estado = ""; #"<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />";
            }

            $editar = "<a href='javascript:;' onclick='editar_guiatrans(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
            $ver = "<a href='javascript:;' onclick='guiatrans_ver_pdf($codigo,0)'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
            $ver2 = "<a href='javascript:;' onclick='guiatrans_ver_pdf($codigo,1)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";

            $lista[] = array($item++, $fecha, $serie, $numero, $nombre_establec, $estado_trans, $img_estado, $editar, $ver, $ver2, $codigo, $comporigen, $estado, $companiaOri);

        }

        // ALMACEN ORIGEN
        $listado_recibidos = $this->guiatrans_model->listar_recibidos();
        $lista_recibidos = array();
        $item = 1;
        foreach ($listado_recibidos as $indice => $valor) {
            $codigo = $valor->GTRANP_Codigo;
            $fecha = mysql_to_human($valor->GTRANC_Fecha);
            $serie = $valor->GTRANC_Serie;
            $numero = $valor->GTRANC_Numero;
            $nombre_establec = $valor->EESTABC_DescripcionOri;
            $estado = $valor->GTRANC_FlagEstado;
            $estado_trans = $valor->GTRANC_EstadoTrans;
            $comporigen = $valor->GTRANC_AlmacenOrigen;
            $companiaOri = $valor->COMPP_CodigoOri;

            if ($estado == '1') {
                $img_estado = ""; #"<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />";
            } else {
                $img_estado = ""; #"<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />";
            }
            $editar = "<a href='javascript:;' onclick='editar_guiatrans(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
            $ver = "<a href='javascript:;' onclick='guiatrans_ver_pdf($codigo,0)'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
            $ver2 = "<a href='javascript:;' onclick='guiatrans_ver_pdf($codigo,1)'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
            $lista_recibidos[] = array($item++, $fecha, $serie, $numero, $nombre_establec, $estado_trans, $img_estado, $editar, $ver, $ver2, $codigo, $comporigen, $estado, $companiaOri);
        }
        $data['lista'] = $lista;
        $data['lista_recibidos'] = $lista_recibidos;
        $data['titulo_busqueda'] = "Buscar GUIA DE TRANSFERENCIA";
        $data['titulo_tabla'] = "Relaci&oacute;n de GUIAS DE TRANSFERENCIA";
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $data['codUsuario'] = form_hidden(array('codUsuario' => $this->somevar['user']));
        $this->layout->view('almacen/guiatrans_index', $data);
    }

    public function datatable_guias_transito(){

        $columnas = array(
                            0 => "GTRANC_Fecha",
                            1 => "GTRANC_Serie",
                            2 => "GTRANC_Numero",
                            3 => "EESTABC_DescripcionOri",
                            4 => ""
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

        $guias_transito = $this->guiatrans_model->listar_transferencias_transito($filter);
        $lista = array();

        if (count($guias_transito) > 0) {
            foreach ($guias_transito as $indice => $valor) {
                $lista[] = array(
                                    0 => $valor->GTRANC_Fecha,
                                    1 => $valor->GTRANC_Serie,
                                    2 => $this->lib_props->getOrderNumeroSerie($valor->GTRANC_Numero),
                                    3 => $valor->EESTABC_DescripcionOri,
                                    4 => "TRANSITO"
                                );
            }
        }

        unset($filter->start);
        unset($filter->length);

        $filterAll = new stdClass();
        $filterAll->tipo_oper = $tipo_oper;
        $filterAll->tipo_docu = $tipo_docu;

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => count($this->guiatrans_model->listar_transferencias_transito($filterAll)),
                            "recordsFiltered" => intval( count($this->guiatrans_model->listar_transferencias_transito($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }

    public function buscar()
    {
        $b_seleccionado_realizadas = $this->input->post('seleccionado_realizadas');
        $b_seleccionado_recibidos = $this->input->post('seleccionado_recibidos');
        $b_fecha_ini = trim($this->input->post('fechai'));
        $b_fecha_fin = trim($this->input->post('fechaf'));
        $b_numero = $this->input->post('numero');
        $b_serie = $this->input->post('serie');
        $b_movimiento = $this->input->post('movimiento');

        $filter = new stdClass();
        $filter->seleccionado_realizadas = $b_seleccionado_realizadas;
        $filter->seleccionado_recibidos = $b_seleccionado_recibidos;

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

        $filter->numero = $b_numero;
        $filter->movimiento = $b_movimiento;
        $filter->serie = $b_serie;

        // ALMACEN DESTINO
        if($b_seleccionado_realizadas == 1 || $b_seleccionado_realizadas == '1'){
            $listado = $this->guiatrans_model->listar2($filter);
        }else {
            $listado = $this->guiatrans_model->listar();
        }
        $lista = array();
        $item = 1;
        foreach ($listado as $indice => $valor) {
            $codigo = $valor->GTRANP_Codigo;
            $fecha = mysql_to_human($valor->GTRANC_Fecha);
            $serie = $valor->GTRANC_Serie;
            $numero = $valor->GTRANC_Numero;
            $nombre_establec = $valor->EESTABC_DescripcionDest;
            $estado = $valor->GTRANC_FlagEstado;
            $estado_trans = $valor->GTRANC_EstadoTrans;
            $comporigen = $valor->GTRANC_AlmacenOrigen;
            $companiaOri = $valor->COMPP_CodigoOri;

            if ($estado == '1') {
                $img_estado = "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />";
            } else {
                $img_estado = "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />";
            }

            $editar = "<a href='javascript:;' onclick='editar_guiatrans(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";

            $ver = "<a href='javascript:;' onclick='guiatrans_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";

            $ver2 = "<a href='javascript:;' onclick='guiatrans_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";

            $lista[] = array($item++, $fecha, $serie, $numero, $nombre_establec, $estado_trans, $img_estado, $editar, $ver, $ver2, $codigo, $comporigen, $estado, $companiaOri);

        }

        // ALMACEN ORIGEN
        if($b_seleccionado_recibidos == 1 || $b_seleccionado_recibidos == '1'){
            $listado_recibidos = $this->guiatrans_model->listar_recibidos2($filter);
        }else {
            $listado_recibidos = $this->guiatrans_model->listar_recibidos();
        }
        $lista_recibidos = array();
        $item = 1;
        foreach ($listado_recibidos as $indice => $valor) {
            $codigo = $valor->GTRANP_Codigo;
            $fecha = mysql_to_human($valor->GTRANC_Fecha);
            $serie = $valor->GTRANC_Serie;
            $numero = $valor->GTRANC_Numero;
            $nombre_establec = $valor->EESTABC_DescripcionOri;
            $estado = $valor->GTRANC_FlagEstado;
            $estado_trans = $valor->GTRANC_EstadoTrans;
            $comporigen = $valor->GTRANC_AlmacenOrigen;
            $companiaOri = $valor->COMPP_CodigoOri;

            if ($estado == '1') {
                $img_estado = "<img src='" . base_url() . "images/active.png' alt='Activo' title='Activo' />";
            } else {
                $img_estado = "<img src='" . base_url() . "images/inactive.png' alt='Anulado' title='Anulado' />";
            }
            $editar = "<a href='javascript:;' onclick='editar_guiatrans(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
            $ver = "<a href='javascript:;' onclick='guiatrans_ver_pdf(" . $codigo . ")'><img src='" . base_url() . "images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
            $ver2 = "<a href='javascript:;' onclick='guiatrans_ver_pdf_conmenbrete(" . $codigo . ")'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
            $lista_recibidos[] = array($item++, $fecha, $serie, $numero, $nombre_establec, $estado_trans, $img_estado, $editar, $ver, $ver2, $codigo, $comporigen, $estado, $companiaOri);
        }
        $data['lista'] = $lista;
        $data['lista_recibidos'] = $lista_recibidos;
        $data['titulo_busqueda'] = "Buscar GUIA DE TRANSFERENCIA";
        $data['titulo_tabla'] = "Relaci&oacute;n de GUIAS DE TRANSFERENCIA";
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $data['codUsuario'] = form_hidden(array('codUsuario' => $this->somevar['user']));

        $this->load->view('almacen/busqueda_guiatrans_index', $data);
    }

    public function cargarTransferencia()
    {
        $userCod = $this->input->post('usuario');
        $codTrans = $this->input->post('guiaTrans');
        $estado = $this->input->post('estado');

        $buscarGuiaTransferencia = $this->guiatrans_model->obtener2($codTrans);
        $estadoTransferencia = $buscarGuiaTransferencia->GTRANC_EstadoTrans;

        // Sirve para verificar si el movimiento ya fue ejecutado
        if($estadoTransferencia == 3 || ($estado == 2 && $estadoTransferencia == 2))
        {
            $data = array(
                'movimiento' => 'Movimiento ya realizado por el DESTINO',
            );

            echo json_encode($data);

        }else {

            // Inicio del FLag
            $flagEstado = "-1";
            // Aumento de estado para confirmar su alteracion del estado de la transferencia
            $estadoTrans = $estado + 1;
            $updateGuiaInySa = FALSE;

            //-------------
            if ($estadoTrans == 0) {
                $flagEstado = "0";
                $confirmacionTransferencia = $this->guiatrans_model->actualiza_usuatrans("", $estadoTrans, $codTrans);
                if ($confirmacionTransferencia) {
                    $updateGuiaInySa = TRUE;
                }
            }
            // DE PENDIENTE A ENVIADO PARA TRANSFERENCIA ORIGEN
            // DE PEDIENTE A TRANSITO PARA TRANSFERENCIA DESTINO
            // AFECTA A LA GUIA DE SALIDA (ESTO AUN NO LO REALIZA - SE REALIZA CUANDO SE CREAR LA TRANSFERENCIA "verificar la cji_guiatrans")
            if ($estadoTrans == 1) {
                $flagEstado = "1";
                $confirmacionTransferencia = $this->guiatrans_model->actualiza_usuatrans($userCod, $estadoTrans, $codTrans);
                if ($confirmacionTransferencia) {
                    $updateGuiaInySa = $this->insertar_guiasatrans($codTrans, $userCod, 15, 'origen');
                    #$this->lib_props->sendMail(74, $codTrans); # MENU 74 = GUIA DE TRANSFERENCIA
                }
            }
            // DE TRANSITO A RECIBIDO PARA TRANSFERENCIA ORIGEN Y DESTINO
            // AFECTA A GUIA DE SALIDA Y AL KARDEX
            // Tipo de documento 15 = GUIA DE TRASFERENCIA
            if ($estadoTrans == 2) {
                $flagEstado = "2";
                $confirmacionTransferencia = $this->guiatrans_model->actualiza_receptrans($userCod, $estadoTrans, $codTrans);
                if ($confirmacionTransferencia) {
                    $updateGuiaInySa = $this->insertar_guiaintrans($codTrans, $userCod, 15, 'destino');
                }
            }
            // DE TRANSITO A DEVOLUCION PARA TRANSFERENCIA ORIGEN Y DESTINO
            // SE ESPECIFICA QUE ES UNA GUIA DEVOLUCION
            if ($estadoTrans == 3) {
                $flagEstado = "3";
                $confirmacionTransferencia = $this->guiatrans_model->actualiza_receptrans($userCod, $estadoTrans, $codTrans);
                if ($confirmacionTransferencia) {
                    $updateGuiaInySa = $this->insertar_guiaintrans($codTrans, $userCod, 15, 'origen');
                }
            }

            $data = array(
                'flagEstado' => $flagEstado,
                'usuario_guia' => $userCod,
                'guia_trans' => $codTrans,
                'estado_trans' => $estadoTrans,
                'updateGuiaInySa' => $updateGuiaInySa
            );
            echo json_encode($data);
        }
    }

    public function seleccionar_destino_general($sel = NULL){
        $almacen = $this->almacen_model->seleccionar_destino_general();

        $option = "";
        $emp = "";
        $j = 0;

        if ( count($almacen) > 0){
            $option .= "<select name='almacen_destino' id='almacen_destino' class='comboGrande'>";
            foreach ($almacen as $indice => $val) {
                if ($val->EMPRP_Codigo != $emp){
                    $emp = $val->EMPRP_Codigo;
                   
                    if ($j > 0)
                        $option .= "</optgroup>";

                    $option .= "<optgroup label='$val->EMPRC_RazonSocial'>";
                }

                $option .= ($sel != NULL AND $sel == $val->ALMAP_Codigo) ? "<option value='$val->ALMAP_Codigo' selected>$val->EESTABC_Descripcion - $val->ALMAC_Descripcion</option>" : "<option value='$val->ALMAP_Codigo'>$val->EESTABC_Descripcion - $val->ALMAC_Descripcion</option>";
                $j++;
            }
            $option .= "</optgroup>";
            $option .= "</select>";
        }
        return $option;
    }

    public function nueva(){

        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        
        unset($_SESSION['serie']);
        unset($_SESSION['serieReal']);
        unset($_SESSION['serieRealBD']);
        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $tipo = 15;
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 15);
        $data_confi1 = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $tipo);
        $data['titulo'] = "NUEVA GUIA DE TRANSFERENCIA";
        $data['codigo'] = "";
        $data['form_open'] = form_open(base_url() . 'index.php/almacen/guiatrans/grabar', array("name" => "frmGuiatrans", "id" => "frmGuiatrans", "onSubmit" => "javascript:return FALSE"));
        $data['form_close'] = form_close();
        $data['oculto'] = form_hidden(array("base_url" => base_url(), "tipo_codificacion" => $data_confi_docu[0]->COMPCONFIDOCP_Tipo, 'codigo' => ''));
        $data['serie'] = "";
        $data['cboEmpresaTrans'] = form_dropdown("empresa_transporte", $this->empresa_model->seleccionar(), "1", " class='comboGrande' id='empresa_transporte' style='width:300px'");
        $data['numero'] = "";
        $data['codigo_usuario'] = "";
        $data['fecha'] = form_input(array("name" => "fecha", "id" => "fecha", "class" => "cajaPequena cajaSoloLectura", "readonly" => "readonly", "maxlength" => "10", "value" => mysql_to_human($this->_hoy)));
        $data['observacion'] = "";
        $data['codguiain'] = "";
        $data['codguiasa'] = "";
        $data['placa'] = "";
        $data['licencia'] = "";
        $data['chofer'] = "";

        $tipoguia = 15;
        $data['tipoguia'] = $tipoguia;
        $data['detalle'] = array();
        $filterin = new stdClass();
        $filterin->TIPOMOVC_Tipo = 2;
        $lista_almacen = $this->almacen_model->cargarAlmacenesPorCompania($compania);
        $lista_almacen_general = $this->seleccionar_destino_general();
        $data['listar_almacen'] = $lista_almacen;

        $data['cboAlmacenDestino'] = $lista_almacen_general;
        $data['estado'] = form_dropdown("estado", array("1" => "Activo", "0" => "Anulado"), "1", " class='comboPequeno' id='estado' style='display:none'");
        $data['cboEmpresaTrans'] = form_dropdown("empresa_transporte", $this->empresa_model->seleccionar(), "1", " class='comboGrande' id='empresa_transporte' style='width:300px'");

        $data['tipo_codificacion'] = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        $data['serie_suger'] = $data_confi1[0]->CONFIC_Serie;
        $data['numero_suger'] = $data_confi1[0]->CONFIC_Numero + 1;
        $this->layout->view('almacen/guiatrans_nueva', $data);
    }

    public function editar($codigo){
        /* :::: SE CREA LA SESSION :::*/
        $hoy = date('Y-m-d H:i:s');
        $cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
        $tempSession = str_replace('.','',$cadena);
        $data['tempSession']  = $tempSession;
        /* :::::::::::::::::::::::::::*/

        $tipo_oper="V";
        
        unset($_SESSION['serie']);
        $compania = $this->somevar['compania'];
        $data['compania'] = $compania;
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $tipo = 15;
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 15);
        $data_confi1 = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'], $tipo);
        $data['titulo'] = "EDITAR GUIA DE TRANSFERENCIA";
        $data['tipo_docu'] = "GT";

        $datos_guiatrans = $this->guiatrans_model->obtener($codigo);
        $data['codigo'] = $codigo;
        $data['form_open'] = form_open(base_url() . 'index.php/almacen/guiatrans/grabar', array("name" => "frmGuiatrans", "id" => "frmGuiatrans"));
        $data['form_close'] = form_close();
        $data['oculto'] = form_hidden(array("base_url" => base_url(), "codigo" => $codigo, "tipo_codificacion" => $data_confi_docu[0]->COMPCONFIDOCP_Tipo));
        $data['codguiain'] = $datos_guiatrans[0]->GUIAINP_Codigo;
        $data['codguiasa'] = $datos_guiatrans[0]->GUIASAP_Codigo;
        $almacorigen = $datos_guiatrans[0]->GTRANC_AlmacenOrigen;
        $data['almacorigen']=$almacorigen;
        if ($almacorigen == $compania) {
            $tipoguia = "";
        } else {
            $tipoguia = 15;
        }
        $data['tipoguia'] = $tipoguia;


        $data['serie'] = $datos_guiatrans[0]->GTRANC_Serie;
        $data['numero'] = $datos_guiatrans[0]->GTRANC_Numero;
        $data['codigo_usuario'] = $datos_guiatrans[0]->GTRANC_CodigoUsuario;
        $data['fecha'] = form_input(array("name" => "fecha", "id" => "fecha", "class" => "cajaPequena cajaSoloLectura", "readonly" => "readonly", "maxlength" => "10", "value" => mysql_to_human($codigo != '' ? $datos_guiatrans[0]->GTRANC_Fecha : $this->_hoy)));
        $data['observacion'] = $datos_guiatrans[0]->GTRANC_Observacion;
        $data['placa'] = $datos_guiatrans[0]->GTRANC_Placa;
        $data['licencia'] = $datos_guiatrans[0]->GTRANC_Licencia;
        $data['chofer'] = $datos_guiatrans[0]->GTRANC_Chofer;
        $transporte = $datos_guiatrans[0]->EMPRP_Codigo;

        $filterin = new stdClass();
        $filterin->TIPOMOVC_Tipo = 6;
        $lista_almacen = $this->almacen_model->cargarAlmacenesPorCompania($datos_guiatrans[0]->COMPP_Codigo);
        $lista_almacen_general = $this->seleccionar_destino_general($datos_guiatrans[0]->GTRANC_AlmacenDestino); #$this->almacen_model->seleccionar_destino($this->session->userdata('empresa'));
        $data['listar_almacen'] = $lista_almacen;
        $data['cboAlmacenDestino'] = $lista_almacen_general; 
        //$data['cboAlmacen'] = form_dropdown("almacen", $lista_almacen, ($codigo != '' ? $datos_guiatrans[0]->GTRANC_AlmacenOrigen : obtener_val_x_defecto($lista_almacen)), " class='comboGrande' style='width:210px;' id='almacen'");
        #$data['cboAlmacenDestino'] = form_dropdown("almacen_destino", $lista_almacen_general, $datos_guiatrans[0]->GTRANC_AlmacenDestino, " class='comboGrande' id='almacen_destino'");
        $data['estado'] = form_dropdown("estado", array("1" => "Activo", "0" => "Anulado"), ($codigo != '' ? $datos_guiatrans[0]->GTRANC_FlagEstado : '1'), " class='comboPequeno' id='estado'");

        $data['tipo_codificacion'] = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;

        $data['cboEmpresaTrans'] = form_dropdown("empresa_transporte", $this->empresa_model->seleccionar(), $transporte, "1", " class='comboGrande' id='empresa_transporte' style='width:300px'");

        $data['serie_suger'] = $data_confi_docu[0]->COMPCONFIDOCP_Serie;
        $data['numero_suger'] = $this->guiatrans_model->obtener_ultimo_numero($data_confi_docu[0]->COMPCONFIDOCP_Serie);

        $detalle = $this->guiatransdetalle_model->listar($codigo);
        $detalle_guiatrans = array();
        unset($_SESSION['serie']);
        unset($_SESSION['serieReal']);
        unset($_SESSION['serieRealBD']);
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
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = $datos_unidad[0]->UNDMED_Descripcion;

                $objeto = new stdClass();
                $objeto->GTRANDETP_Codigo = $detacodi;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoUsuario = $codigo_interno;
                $objeto->GTRANDETC_Cantidad = $cantidad;
                $objeto->GTRANDETC_Costo = $costo;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->GTRANDETC_GenInd = $GenInd;
                $objeto->GTRANDETC_Descripcion = $descri;
                $detalle_guiatrans[] = $objeto;
                
                /**gcbq verificamos si el detalle dee comprobante contiene productos individuales**/
                    /**iniciamos la libreria actualizacion de serie seleccionada solo se da en ventas**/
                    $this->load->model('almacen/almacenproductoserie_model');
                    /**fin**/
                    /**verificamos si es individual**/
                    if($GenInd!=null && trim($GenInd)=="I"){
                        /**obtenemos serie de ese producto **/
                        $producto_id=$producto;
                        /**almacen de origen se convierte en el almacen verdadero**/
                        $almacen=$almacorigen;
                        $filterSerie= new stdClass();
                        $filterSerie->PROD_Codigo=$producto_id;
                        $filterSerie->SERIC_FlagEstado='1';
                        /**10:guiatransferencia origen**/
                        $filterSerie->DOCUP_Codigo=15;
                        $filterSerie->SERDOC_NumeroRef=$codigo;
                        $filterSerie->ALMAP_Codigo=$almacen;
                        $listaSeriesProducto = $this->seriedocumento_model->buscar($filterSerie);
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
                /**fin de proceso de realizacion**/
            }
        }
        $data['detalle'] = NULL; #$detalle_guiatrans;
        $this->layout->view('almacen/guiatrans_nueva', $data);
    }

    public function grabar(){
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 15);
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

        if ($this->input->post('almacen') == '' || $this->input->post('almacen') == '0')
            exit('{"result":"error", "campo":"almacen"}');
        if ($this->input->post('almacen_destino') == '' || $this->input->post('almacen_destino') == '0')
            exit('{"result":"error", "campo":"almacen_destino"}');
        if ($this->input->post('almacen') == $this->input->post('almacen_destino'))
            exit('{"result":"error", "campo":"almacen_destino"}');
        if ($this->input->post('fecha') == '')
            exit('{"result":"error", "campo":"fecha"}');
        if ($this->input->post('estado') == '0' && $this->input->post('observacion') == '')
            exit('{"result":"error", "campo":"observacion"}');

        $codigo = $this->input->post("codigo");
        $serie = $this->input->post("serie") ? $this->input->post("serie") : NULL;
        $numero = $this->input->post("numero") ? $this->input->post("numero") : NULL;
        $codigo_usuario = $this->input->post("codigo_usuario") ? $this->input->post("codigo_usuario") : NULL;
        $almacen = $this->input->post("almacen");
        $almacen_destino = $this->input->post("almacen_destino");
        $fecha = $this->input->post("fecha");
        $observacion = $this->input->post("observacion") ? $this->input->post("observacion") : NULL;
        $estado = $this->input->post("estado");
        $placa = $this->input->post("placa");
        $licencia = $this->input->post("licencia");
        $chofer = $this->input->post("chofer");
        $transporte = $this->input->post("empresa_transporte");

        $prodcodigo = $this->input->post('prodcodigo');
        $produnidad = $this->input->post('produnidad');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodcosto = $this->input->post('prodcosto');
        $proddescri = $this->input->post('proddescri');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $flagGenInd = $this->input->post('flagGenIndDet');
        $almacenProducto = $this->input->post('almacenProducto');
        //gcbq
        $this->configuracion_model->modificar_configuracion($this->somevar['compania'], 15, $numero, $serie);


        $filter = new stdClass();
        $filter->GTRANC_Serie = $serie;
        $filter->GTRANC_Numero = $numero;
        $filter->GTRANC_CodigoUsuario = $codigo_usuario;
        $filter->GTRANC_AlmacenOrigen = $almacen;
        $filter->GTRANC_AlmacenDestino = $almacen_destino;
        $filter->GTRANC_Fecha = human_to_mysql($fecha);
        $filter->GTRANC_Observacion = $observacion;
        $filter->GTRANC_Placa = $placa;
        $filter->GTRANC_Licencia = $licencia;
        $filter->GTRANC_Chofer = $chofer;
        $filter->EMPRP_Codigo = $transporte;
        $filter->COMPP_Codigo = $this->somevar['compania'];
        $filter->USUA_Codigo = $this->somevar['user'];
        $filter->GTRANC_FlagEstado = $estado;

        $guiatrans_id = 0;

        if (isset($codigo) && $codigo > 0) {
            $guiatrans_id = $this->guiatrans_model->actualiza_almacen_destino($codigo, $filter);
            // Para poder guardar los productos registrados correctamente
            if ($guiatrans_id > 0) {
                $this->guiatransdetalle_model->eliminar($guiatrans_id);
            }
                /**eliminamos los detalles de seriedocumento
                 * 15:guiatransferencia
                 * ***/
                $this->seriedocumento_model->eliminarDocumento($guiatrans_id,15);
                /**fin de eliminacionb**/
        } else {
            $guiatrans_id = $this->guiatrans_model->insertar($filter);
        }

        if ($guiatrans_id!=0) {

            if (is_array($prodcodigo)) {
                foreach ($prodcodigo as $indice => $valor) {
                    $producto = $prodcodigo[$indice];
                    $unidad = $produnidad[$indice];
                    $cantidad = $prodcantidad[$indice];
                    $costo = $prodcosto[$indice];
                    $descri = $proddescri[$indice];
                    $accion = $detaccion[$indice];
                    $detflag = $flagGenInd[$indice];

                    $filter2 = new stdClass();
                    $filter2->GTRANP_Codigo = $guiatrans_id;
                    $filter2->PROD_Codigo = $producto;
                    $filter2->UNDMED_Codigo = $unidad;
                    $filter2->GTRANDETC_Cantidad = $cantidad;
                    $filter2->GTRANDETC_Costo = $costo;
                    $filter2->GTRANDETC_GenInd = $detflag;
                    $filter2->GTRANDETC_Descripcion = $descri;
                    $filter2->GTRANDETC_FlagEstado = 1;

                    if ( $detaccion[$indice] != 'e' )
                        $this->guiatransdetalle_model->insertar($filter2);
                    
                    /**verificacion de tipo de producto si es con serie**/
                    if ( $detflag == 'I' ){
                        if ( $valor != null ){
                            /**obtenemos las series de session por producto***/
                            $codigoAlmacenProducto = $almacen;
                            $seriesProducto = $_SESSION['serieReal'];
                            #$seriesProducto=$this->session->userdata('serieReal');

                            if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
                                if( $accion != 'n' ){
                                    $producto_id=$valor;
                                    /***pongo todos en estado cero de las series asociadas a ese producto**/
                                    $seriesProductoBD = $_SESSION['serieRealBD'];
                                    $serieBD = $seriesProductoBD;
                                    if($serieBD!=null && count($serieBD)>0){
                                        foreach ($serieBD as $almBD => $arrAlmacenBD) {
                                            if($almBD==$codigoAlmacenProducto){
                                                foreach ($arrAlmacenBD as $ind1BD => $arrserieBD) {
                                                    if ($ind1BD == $producto_id) {
                                                        foreach ($arrserieBD as $keyBD => $valueBD) {
                                                            /**cambiamos a estado 0**/
                                                            $filterSerieD = new stdClass();
                                                            $filterSerieD->SERDOC_FlagEstado = '0';
                                                            $this->seriedocumento_model->modificar($valueBD->SERDOC_Codigo,$filterSerieD);
                                                            /**deseleccionamos los registros en estadoSeleccion cero:0:desleccionado**/
                                                            $tcomp = "GT-".$guiatrans_id;
                                                            $this->almacenproductoserie_model->seleccionarSerieBD($valueBD->SERIP_Codigo,0,$tcomp);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                if( $accion != 'e' ){
                                    foreach ($seriesProducto as $alm2 => $arrAlmacen2) {
                                        if($alm2==$codigoAlmacenProducto){
                                            foreach ($arrAlmacen2 as $ind2 => $arrserie2){
                                                if ($ind2 == $valor) {
                                                    $serial = $arrserie2;
                                                    if($serial != null && count($serial) > 0){
                                                        foreach ($serial as $i => $serie) {
                                                            $serieNumero=$serie->serieNumero;
                                                            if($serie->serieDocumentoCodigo!=null && $serie->serieDocumentoCodigo!=0){
                                                                $filterSerie= new stdClass();
                                                                $filterSerie->SERDOC_FlagEstado='1';
                                                                $this->seriedocumento_model->modificar($serie->serieDocumentoCodigo,$filterSerie);
                                                            }else{
                                                                /**insertamso serie documento**/
                                                                /**DOCUMENTO COMPROBANTE**/
                                                                $filterSerieD = new stdClass();
                                                                $filterSerieD->SERDOC_Codigo = NULL;
                                                                $filterSerieD->SERIP_Codigo = $serie->serieCodigo;
                                                                /**guiatransferencia origen :10**/
                                                                $filterSerieD->DOCUP_Codigo = 15;
                                                                $filterSerieD->SERDOC_NumeroRef = $guiatrans_id;
                                                                /**2:salida**/
                                                                $filterSerieD->TIPOMOV_Tipo = 6;
                                                                $filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
                                                                $filterSerieD->SERDOC_FlagEstado='1';
                                                                $this->seriedocumento_model->insertar($filterSerieD);
                                                                /**FIN DE INSERTAR EN SERIE**/
                                                                /**los registros en estadoSeleccion 1:seleccionado**/
                                                            }
                                                            $tcomp = "GT-".$guiatrans_id;
                                                            $this->almacenproductoserie_model->seleccionarSerieBD($serie->serieCodigo,1,$tcomp);
                                                        }
                                                    }
                                                    break;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                                
                                if($accion != 'n'){
                                    /**eliminamos los registros en estado cero solo de serieDocumento**/
                                    $this->seriedocumento_model->eliminarDocumento($guiatrans_id,15);
                                }
                            }
                        }
                    }
                    
                    
                    
                    /**fin de verificacion**/
                }
            }

            exit('{"result":"ok", "codigo":"' . $guiatrans_id . '"}');
        } else {
            exit('{"result":"error", "codigo":"' . $guiatrans_id . '"}');
        }
    }

    /**
     * Registro paara la guia de salida
     * Es llamada desde GUIA DE TRANSFERENCIA
     * @param $id_guiatrans
     * @param $codUsuario
     * @param $tipoDocumento
     * @param $guiaAlmacen
     * @return bool
     */
    public function insertar_guiasatrans($id_guiatrans, $codUsuario, $tipoDocumento, $guiaAlmacen)
    {
        //consulto  a la guia de transferencia
        $fecha = date("d/m/Y");
        $datos_guiatransAll = $this->guiatrans_model->obtener2($id_guiatrans);
        // Tipo de envio de almacen Origen(Devolucion) o destino(Transito)
        if ($guiaAlmacen == "destino") {
            $almacen_destino = $datos_guiatransAll->GTRANC_AlmacenDestino;
        } else if ($guiaAlmacen == "origen") {
            $almacen_destino = $datos_guiatransAll->GTRANC_AlmacenOrigen;
        } else {
            $almacen_destino = $datos_guiatransAll->GTRANC_AlmacenDestino;
        }

        //Datos cabecera de la guiain.
        $filterGuiasa = new stdClass();
        $filterGuiasa->TIPOMOVP_Codigo = 6;
        $filterGuiasa->ALMAP_Codigo = $almacen_destino;
        $filterGuiasa->DOCUP_Codigo = $tipoDocumento;
        $filterGuiasa->GUIASAC_Fecha = human_to_mysql($fecha);
        $filterGuiasa->USUA_Codigo = $codUsuario;
        $guisa_id = $this->guiasa_model->insertar($filterGuiasa);
        //detalle de la guia de transferencia
        //actualizar guiasa en guiatrans
        $datos_guiatrans = $this->guiatrans_model->actualizar_guia_salida($id_guiatrans, $guisa_id);

        if ($datos_guiatrans) {

            $datos_detallegtrans = $this->guiatransdetalle_model->listar($id_guiatrans);

            $totalDetalles = count($datos_detallegtrans);
            $contDetalles = 0;
            //datos del detalles de la guia

            if ($datos_detallegtrans != NULL) {

                foreach ($datos_detallegtrans as $indice => $valor) {
                    $producto = $valor->PROD_Codigo;
                    $unidad = $valor->UNDMED_Codigo;
                    $cantidad = $valor->GTRANDETC_Cantidad;
                    $costo = $valor->GTRANDETC_Costo;
                    $descri = $valor->GTRANDETC_Descripcion;
                    $GenInd = $valor->GTRANDETC_GenInd;
                    // Valores necesarios
                    $filterGuiasaDet = new stdClass();
                    $filterGuiasaDet->GUIASAP_Codigo = $guisa_id;
                    $filterGuiasaDet->PRODCTOP_Codigo = $producto;
                    $filterGuiasaDet->UNDMED_Codigo = $unidad;
                    $filterGuiasaDet->GUIASADETC_Cantidad = $cantidad;
                    $filterGuiasaDet->GUIASADETC_Costo = $costo;
                    $filterGuiasaDet->GUIASADETC_Descripcion = $descri;
                    $filterGuiasaDet->GUIASADETC_GenInd = $GenInd;
                    $filterGuiasaDet->ALMAP_Codigo = $almacen_destino;

                    $insertGuiasa = $this->guiasadetalle_model->insertar_2015($filterGuiasaDet,$id_guiatrans);

                    if ($insertGuiasa) {
                        $contDetalles++;
                    }
                }

                if ($contDetalles == $totalDetalles) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            } else {
                return FALSE;
            }

        } else {
            return FALSE;
        }
        // sirve cuando se quiere hacer un login
        //header("location:" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_transusuario/1/activo");
    }


    public function modificar()
    {
        $data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 15);
        $tipo_codificacion = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;


        $codigo_guiatrans = $this->input->post("codigo_guiatrans");
        $serie = $this->input->post("serie") ? $this->input->post("serie") : NULL;
        $numero = $this->input->post("numero") ? $this->input->post("numero") : NULL;
        $codigo_usuario = $this->input->post("codigo_usuario") ? $this->input->post("codigo_usuario") : NULL;
        $almacen = $this->input->post("almacen");
        $almacen_destino = $this->input->post("almacen_destino");
        $fecha = $this->input->post("fecha");
        $observacion = $this->input->post("observacion") ? $this->input->post("observacion") : NULL;
        $estado = $this->input->post("estado");
        $placa = $this->input->post("placa");
        $licencia = $this->input->post("licencia");
        $chofer = $this->input->post("chofer");
        $transporte = $this->input->post("empresa_transporte");

        $prodcodigo = $this->input->post('prodcodigo');
        $produnidad = $this->input->post('produnidad');
        $prodcantidad = $this->input->post('prodcantidad');
        $prodcosto = $this->input->post('prodcosto');
        $proddescri = $this->input->post('proddescri');
        $detaccion = $this->input->post('detaccion');
        $detacodi = $this->input->post('detacodi');
        $flagGenInd = $this->input->post('flagGenIndDet');


        $filter = new stdClass();
        $filter->GTRANC_Serie = $serie;
        $filter->GTRANC_Numero = $numero;
        $filter->GTRANC_CodigoUsuario = $codigo_usuario;
        $filter->GTRANC_AlmacenOrigen = $almacen;
        $filter->GTRANC_AlmacenDestino = $almacen_destino;
        $filter->GTRANC_Fecha = human_to_mysql($fecha);

        $filter->GTRANC_Observacion = $observacion;
        $filter->GTRANC_Placa = $placa;
        $filter->GTRANC_Licencia = $licencia;
        $filter->GTRANC_Chofer = $chofer;
        $filter->EMPRP_Codigo = $transporte;
        $filter->COMPP_Codigo = $this->somevar['compania'];
        $filter->USUA_Codigo = $this->somevar['user'];
        $filter->GTRANC_FlagEstado = $estado;


        //Datos cabecera de la guiasa.
        $filterGuiasa = new stdClass();
        $filterGuiasa->TIPOMOVP_Codigo = 6;
        $filterGuiasa->ALMAP_Codigo = $almacen;
        $filterGuiasa->DOCUP_Codigo = 15;
        $filterGuiasa->GUIASAC_Fecha = $fecha;
        $filterGuiasa->GUIASAC_Observacion = $observacion;
        $filterGuiasa->USUA_Codigo = $this->somevar['user'];


        $this->guiatrans_model->actualiza_guia($codigo_guiatrans, $filter);


        //
        //        if (is_array($prodcodigo)) {
        //            foreach ($prodcodigo as $indice => $valor) {
        //                $producto = $prodcodigo[$indice];
        //                $unidad = $produnidad[$indice];
        //                $cantidad = $prodcantidad[$indice];
        //                $costo = $prodcosto[$indice];
        //                $descri = $proddescri[$indice];
        //                $accion = $detaccion[$indice];
        //                $detflag = $flagGenInd[$indice];
        //
        //                $filter2 = new stdClass();
        //                $filter2->GTRANP_Codigo = $guiatrans_id;
        //                $filter2->PROD_Codigo = $producto;
        //                $filter2->UNDMED_Codigo = $unidad;
        //                $filter2->GTRANDETC_Cantidad = $cantidad;
        //                 $filter2->GTRANDETC_Costo = $costo;
        //                $filter2->GTRANDETC_GenInd = $detflag;
        //                $filter2->GTRANDETC_Descripcion = $descri;
        //
        //                /* Insertar detalle de guia de salida o ingreso */
        //                $filterGuiasaDet = new stdClass();
        //                $filterGuiasaDet->GUIASAP_Codigo = $filter->GUIASAP_Codigo;
        //                $filterGuiasaDet->PRODCTOP_Codigo = $producto;
        //                $filterGuiasaDet->UNDMED_Codigo = $unidad;
        //                $filterGuiasaDet->GUIASADETC_Cantidad = $cantidad;
        //                $filterGuiasaDet->GUIASADETC_Costo = $costo;
        //                $filterGuiasaDet->GUIASADETC_GenInd = $detflag;
        //                $filterGuiasaDet->GUIASADETC_Descripcion = $descri;
        //                /* /
        //                  $filterGuiainDet  = new stdClass();
        //                  $filterGuiainDet->GUIAINP_Codigo      = $filter->GUIAINP_Codigo;
        //                  $filterGuiainDet->PRODCTOP_Codigo     = $producto;
        //                  $filterGuiainDet->UNDMED_Codigo       = $unidad;
        //                  $filterGuiainDet->GUIAINDETC_Cantidad = $cantidad;
        //                  $filterGuiainDet->GUIAINDETC_Costo    = $costo;
        //                  $filterGuiainDet->GUIIAINDETC_GenInd  = $detflag;
        //                 */
        //               
        //                      $this->guiatransdetalle_model->eliminar($filter2);
        //                


        exit('{"result":"ok", "codigo":""}');
    }

    /**
     * Registro paara la guia de ingreso
     * Es llamada desde GUIA DE TRANSFERENCIA
     * @param $id_guiatrans
     * @param $codUsuario
     * @param $tipoDocumento
     * @param $guiaAlmacen
     * @return bool
     */
    public function insertar_guiaintrans($id_guiatrans, $codUsuario, $tipoDocumento, $guiaAlmacen)
    {

        //consulto  a la guia de transferencia
        $fecha = date("d/m/Y");
        $datos_guiatrans = $this->guiatrans_model->obtener($id_guiatrans);
        $id_guiasa = $datos_guiatrans[0]->GUIASAP_Codigo;
        $almacen_origen = $datos_guiatrans[0]->GTRANC_AlmacenOrigen;
        // Tipo de envio de almacen Origen(Devolucion) o destino(Transito)
        if ($guiaAlmacen == "destino") {
            $almacen_destino = $datos_guiatrans[0]->GTRANC_AlmacenDestino;
        } else if ($guiaAlmacen == "origen") {
            $almacen_destino = $datos_guiatrans[0]->GTRANC_AlmacenOrigen;
        } else {
            $almacen_destino = $datos_guiatrans[0]->GTRANC_AlmacenDestino;
        }

        //Datos cabecera de la guiain.
        $filterGuiain = new stdClass();
        $filterGuiain->TIPOMOVP_Codigo = 6;
        $filterGuiain->ALMAP_Codigo = $almacen_destino;
        $filterGuiain->DOCUP_Codigo = $tipoDocumento;
        $filterGuiain->GUIAINC_Fecha = human_to_mysql($fecha);
        $filterGuiain->USUA_Codigo = $codUsuario;
        $guiin_id = $this->guiain_model->insertar($filterGuiain);
        //detalle de la guia de transferencia 
        //actualizar guiainp en guiatrans 
        $datos_guiatrans = $this->guiatrans_model->actualiza_guia2($id_guiatrans, $guiin_id);
        $datos_detallegtrans = $this->guiatransdetalle_model->listar($id_guiatrans);
        $totalDetalles = count($datos_detallegtrans);
        $contDetalles = 0;
        //datos del detalles de la guia

        if (is_array($datos_detallegtrans)) {

            foreach ($datos_detallegtrans as $indice => $valor) {
                $producto = $datos_detallegtrans[$indice]->PROD_Codigo;
                $unidad = $datos_detallegtrans[$indice]->UNDMED_Codigo;
                $cantidad = $datos_detallegtrans[$indice]->GTRANDETC_Cantidad;
                $costo = $datos_detallegtrans[$indice]->GTRANDETC_Costo;
                $descri = $datos_detallegtrans[$indice]->GTRANDETC_Descripcion;
                $detflag = $datos_detallegtrans[$indice]->GTRANDETC_GenInd;
                /* Insertar detalle de guia de salida o ingreso */

                $filterGuiainDet = new stdClass();
                $filterGuiainDet->GUIAINP_Codigo = $guiin_id;
                $filterGuiainDet->PRODCTOP_Codigo = $producto;
                $filterGuiainDet->UNDMED_Codigo = $unidad;
                $filterGuiainDet->GUIAINDETC_Cantidad = $cantidad;
                $filterGuiainDet->GUIAINDETC_Costo = $costo;
                $filterGuiainDet->GUIIAINDETC_GenInd = $detflag;
                $filterGuiainDet->ALMAP_Codigo=$almacen_destino;

                $insertGuiain = $this->guiaindetalle_model->insertar_2015($filterGuiainDet, 'TRANSFERENCIA',$id_guiatrans,$almacen_origen);
                if ($insertGuiain) {
                    $contDetalles++;
                }
            }
        }

        if ($contDetalles == $totalDetalles) {
            return TRUE;
        } else {
            return FALSE;
        }
        // sirve cuando se quiere hacer un login
        //header("location:" . base_url() . "index.php/seguridad/usuario/ventana_confirmacion_transusuario/1/activo");
    }

    public function guiatrans_ver_pdf($codigo, $format = "print", $img = 0){
        switch ($format) {
            case "print":
                $this->guiatrans_print($codigo, $img);
                break;
            case "pdf":
                $this->guiatrans_pdf($codigo, $img);
                break;
            default:
                $this->guiatrans_pdf($codigo, $img);
                break;
        }
    }


    public function guiatrans_pdf($codigo, $flagPdf = 0, $enviarcorreo = false){
        $this->lib_props->guiatrans_pdf($codigo, $flagPdf, $enviarcorreo);
        return NULL;
        
        $datos_guiatrans = $this->guiatrans_model->obtener($codigo);
        $detalle_comprobante = $this->guiatransdetalle_model->listar($codigo);

        $guiasap = $datos_guiatrans[0]->GUIASAP_Codigo;
        $guiainp = $datos_guiatrans[0]->GUIAINP_Codigo;
        $serie = $datos_guiatrans[0]->GTRANC_Serie;
        $numero = $this->lib_props->getOrderNumeroSerie($datos_guiatrans[0]->GTRANC_Numero);
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
            $datos_emprtrans = $this->empresa_model->obtener_datosEmpresa($empresa_transporte);
            if (count($datos_emprtrans) > 0) {
                $ruc_empresaTrans = $datos_emprtrans[0]->EMPRC_Ruc;
                $nombre_empresaTrans = $datos_emprtrans[0]->EMPRC_RazonSocial;
            }
        }

        /* Listado de detalles */
        $detaProductos = "";
        if (count($detalle_comprobante) > 0) {
                foreach ($detalle_comprobante as $indice => $valor) {
                    $producto = $valor->PROD_Codigo;
                    
                    //$nomprod = $datos_producto[0]->PROD_Nombre;
                    $nomprod = $valor->GTRANDETC_Descripcion;
                    $nomprod = (isset($valor->GTRANDETC_Observacion) && $valor->GTRANDETC_Observacion != '') ? $nomprod . " <br> " .$valor->GTRANDETC_Observacion : $nomprod;
                    
                    $datos_producto = $this->producto_model->obtener_producto($producto);
                    
                    $costo = $valor->GTRANDETC_Costo;
                    $cantidad = $valor->GTRANDETC_Cantidad;

                    $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;

                    $unidadMedida = $this->unidadmedida_model->obtener($valor->UNDMED_Codigo);
                    $unidadSimbolo = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

                        $detaProductos = $detaProductos. '
                        <tr>
                            <td style="text-align:right;">'.$cantidad.'</td>
                            <td style="text-align:center;">'.$unidadSimbolo.'</td>
                            <td style="text-align:center;">'.$codigo_usuario.'</td>
                            <td style="text-align:left;">'.$nomprod.'</td>
                        </tr>';
            }
        }

        
        $this->load->library("tcpdf");
        $medidas = "a4"; // a4 - carta
        $this->pdf = new pdfCotizacion('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(5, 35, 5); // Cada 10 es 1cm - Como es hoja estoy tratando las medidad en cm -> 
        $this->pdf->SetTitle('GUIA DE TRANSFERENCIA');
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->Header();
        if ($flagPdf == 1)
            $this->pdf->setPrintHeader(true);
        else
            $this->pdf->setPrintHeader(false);

        $this->pdf->setPrintFooter(false);
        $this->pdf->SetAutoPageBreak(true, 30);
        $this->pdf->AddPage();


        $this->load->model("maestros/emprestablecimiento_model");
        $this->load->model("maestros/compania_model");

        $datosAlmacenOrigen = $this->almacen_model->obtener($idAlmacenOrigen);
        $datosCompania = $this->compania_model->obtener($companiaComprobante);
        $datosEstablecimiento = $this->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
        $datosEmpresa =  $this->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

        if ($pedido != NULL || $pedido != ''){
            $datosPedido = $this->pedido_model->obtener_pedido_filtrado($pedido);
            $pedidoSerie = $datosPedido[0]->PEDIC_Serie;
            $pedidoNumero = $datosPedido[0]->PEDIC_Numero;
            $datosAlmacenDestino[0]->COMPP_Codigo = $datosPedido[0]->COMPP_Codigo;
        }
        else{
            $pedidoSerie = "";
            $pedidoNumero = "";
        }

        $datosAlmacenDestino = $this->almacen_model->obtener($idAlmacenDestino);
        $datosCompaniaDestino = $this->compania_model->obtener($datosAlmacenDestino[0]->COMPP_Codigo);
        $datosEstablecimientoDestino = $this->emprestablecimiento_model->listar( $datosCompaniaDestino[0]->EMPRP_Codigo, '', $datosAlmacenDestino[0]->COMPP_Codigo );
        $datosEmpresaDestino =  $this->empresa_model->obtener_datosEmpresa( $datosCompaniaDestino[0]->EMPRP_Codigo );

        $tipoDocumento = "GUIA DE TRANSFERENCIA<br>ELECTRÓNICA";
        $tipoDocumentoF = "GUIA DE TRANSFERENCIA ELECTRÓNICA";

        $logo = ''; #'<img src="'.base_url().'images/cabeceras/logo.jpg" height="70px"/>';

        // RECTANGULO REDONDEADO SERIE DOCUMENTO
        // $x, $y, $w, $h, $r, $round_corner='1111', $style='', $border_style=array(), $fill_color=array()
            $this->pdf->RoundedRect(125, 40, 80, 30, 1.50, '1111', '');

                                #<b>RUC '.$datosEmpresa[0]->EMPRC_Ruc.'</b>
                    #<tr>
                    #    <td style="text-indent:0.5cm"><b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b>
                    #        <br style="text-indent:0.5cm">'.$datosEstablecimiento[0]->EESTAC_Direccion.'
                    #        <br style="text-indent:0.5cm">'.$datosEstablecimiento[0]->distrito.' - '.$datosEstablecimiento[0]->provincia.' - '.$datosEstablecimiento[0]->departamento.'
                    #    </td>
                    #</tr>
        $cabeceraHTML = '
                <table align="left">
                    <tr>
                        <td style="width:12cm;">'.$logo.'</td>
                        <td style="width:8cm; font-size:14pt; text-align:center;" rowspan="2">
                            <div style="background-color:#FFF;">
                                <br><b>'.$tipoDocumento.'</b>
                                <br><b>'.$serie.' - '.$numero.'</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </table>';

        $this->pdf->writeHTML($cabeceraHTML,true,false,true,'');

        $partida = $datosAlmacenOrigen[0]->ALMAC_Descripcion .' - ' . $datosAlmacenOrigen[0]->ALMAC_Direccion;
        $destino = $datosAlmacenDestino[0]->ALMAC_Descripcion .' - ' . $datosAlmacenDestino[0]->ALMAC_Direccion;

        $h = 35;
        $h = ( strlen($partida) > 98 ) ? $h + 2.5 : $h;
        $h = ( strlen($destino) > 98 ) ? $h + 2.5 : $h;

        $this->pdf->RoundedRect(5, 75, 200, 25, 1.50, '1111', ''); // CLIENTE
        $this->pdf->RoundedRect(5, 102, 200, $h, 1.50, '1111', ''); // FECHA

        $clienteHTML = '
                <table style="text-indent:0.2cm;" cellpadding="0.02cm" border="0">
                    <tr>
                        <td style="width:14cm;"><b>EMPRESA DESTINO</b></td>

                        <td style="width:3cm; font-weight:bold;">FECHA EMISIÓN:</td>
                        <td style="width:2.5cm;">'.$fecha.'</td>
                    </tr>
                    <tr>
                        <td style="width:3.5cm; font-weight:bold;">RUC:</td>
                        <td colspan="2" style="width:16cm;">'.$datosEmpresaDestino[0]->EMPRC_Ruc.'</td>
                    </tr>
                    <tr>
                        <td style="width:3.5cm; font-weight:bold;">DENOMINACIÓN:</td>
                        <td colspan="2" style="width:16cm; text-align:justification">'.$datosEmpresaDestino[0]->EMPRC_RazonSocial.'</td>
                    </tr>
                    <tr> 
                        <td style="width:3.5cm; font-weight:bold;">DIRECCIÓN:</td>
                        <td colspan="2" style="width:16cm; text-align:justification">'.$datosEmpresaDestino[0]->EMPRC_Direccion.'</td>
                    </tr>
                </table> <br><br><br> &nbsp;'; // el &nbsp; aplica el espacio en blanco
        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        $clienteHTML = '<table style="text-indent:0.2cm;" border="0" cellpadding="0.05cm">
                            <tr>
                                <td colspan="2" style="font-weight:bold;">DETALLES DEL TRASLADO</td>
                            </tr>
                            <tr> 
                                <td style="width:3.5cm; font-weight:bold">PUNTO DE PARTIDA:</td>
                                <td style="width:16.5cm; text-align:justification;">'.$partida.'</td>
                            </tr>
                            <tr> 
                                <td style="width:3.5cm; font-weight:bold">PUNTO DE LLEGADA:</td>
                                <td style="width:16.5cm; text-align:justification;">'.$destino.'</td>
                            </tr>
                        </table>
                        <table border="0" cellpadding="0.05cm">
                            <tr>
                                <td style="width:10cm;"><table style="text-indent:0.2cm;" border="0" cellpadding="0.05cm">
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
                                <td style="width:9cm;"><table style="text-indent:0.2cm;" border="0" cellpadding="0.05cm">
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
            $adicionalHTML = '<table border="0" style="width:19.5cm; font-size:8pt;" cellpadding="0.1cm">
                    <tr>
                        <td style="font-weight:bold; text-align:justification;">OBSERVACIÓN: <span style="font-style:italic; font-weight:normal;">'.$observacion.'</span></td>
                    </tr>
                </table>';
            $this->pdf->writeHTML($adicionalHTML,true,false,true,'');
        }

        #$this->pdf->RoundedRect(5, 73, 200, 8, 1.50, '1111', ''); // PRODUCTOS
        $productoHTML = '
                <table>
                    <tr>
                        <th style="font-weight:bold; text-align:right; width:2cm;">CANT.</th>
                        <th style="font-weight:bold; text-align:center; width:3cm;">UNIDAD M.</th>
                        <th style="font-weight:bold; text-align:center; width:3cm;">CÓDIGO.</th>
                        <th style="font-weight:bold; text-align:justification; width:11.5cm;">DESCRIPCIÓN</th>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
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

        $nameFile = "G. Transferencia -".$this->lib_props->getOrderNumeroSerie($serie)."-".$this->lib_props->getOrderNumeroSerie($numero).".pdf";

        if ($enviarcorreo == false)
            $this->pdf->Output($nameFile, 'I');
        else
            return $this->pdf->Output($nameFile, 'S');
    }

    public function guiatrans_ver_pdf_conmenbrete($codigo, $img)
    {
        //$img = "";
        switch (FORMATO_IMPRESION) {
            case 1: //Formato para ferresat

                $this->guiatrans_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
            case 2:  //Formato para jimmyplat
                $this->guiarem_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->guiarem_ver_pdf_conmenbrete_formato3($codigo);
                break;
            case 4:  //Formato para ferremax
                $this->guiarem_ver_pdf_conmenbrete_formato4($codigo);
                break;
            case 5:  //DISTRIBUIDORA G Y C
                if ($_SESSION['compania'] == "1") {
                    /* DISTRIBUIDORA G Y C */
                    $this->guiarem_ver_pdf_conmenbrete_formato5($codigo);
                } else {
                    /* DISTRIBUIDORA G Y C electro data */
                    $this->guiarem_ver_pdf_conmenbrete_formato6($codigo);
                }
                break;
            case 6:  //DISTRIBUIDORA C Y L
                $this->guiarem_ver_pdf_conmenbrete_formato7($codigo);
                break;
            case 7:  //FAMYSERFE
                $this->guiarem_ver_pdf_conmenbrete_formato8($codigo);
                break;
            case 8:  //COMPAÃ‘IA IMPACTO EL CASO 8_1 ES PARA LA COMPAÃ‘IA 1 Y EL 2 ES PARA LA COMÃ‘AIA DDO
                // if($_SESSION['compania'] == "1"){
                $this->guiarem_ver_pdf_conmenbrete_formato8_1($codigo);
                // }else{
                // $this->guiarem_ver_pdf_conmenbrete_formato8_2($codigo, $tipo_oper); 
                // }
                break;
            default:
                guiarem_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
        }
    }

    public function guiarem_ver_pdf_formato1($codigo)
    {
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        $datos_guiarem = $this->guiarem_model->obtener($codigo);
        $datos_detalle_guiarem = $this->guiaremdetalle_model->obtener2($codigo);
        $tipo_movimiento = $datos_guiarem[0]->TIPOMOVP_Codigo;
        $referencia = $datos_guiarem[0]->DOCUP_Codigo;
        $cliente = $datos_guiarem[0]->CLIP_Codigo;

        $proveedor = $datos_guiarem[0]->PROVP_Codigo;
        $guiasap = $datos_guiarem[0]->GUIASAP_Codigo;
        $guiainp = $datos_guiarem[0]->GUIAINP_Codigo;
        $numero_ref = $datos_guiarem[0]->GUIAREMC_NumeroRef;
        $numero_ocompra = $datos_guiarem[0]->GUIAREMC_OCompra;
        $serie = $datos_guiarem[0]->GUIAREMC_Serie;
        $numero = $datos_guiarem[0]->GUIAREMC_Numero;
        $fecha_traslado = mysql_to_human($datos_guiarem[0]->GUIAREMC_FechaTraslado);
        $observacion = $datos_guiarem[0]->GUIAREMC_Observacion;
        $marca = $datos_guiarem[0]->GUIAREMC_Marca;
        $placa = $datos_guiarem[0]->GUIAREMC_Placa;
        $registro_mtc = $datos_guiarem[0]->GUIAREMC_RegistroMTC;
        $certificado = $datos_guiarem[0]->GUIAREMC_Certificado;
        $licencia = $datos_guiarem[0]->GUIAREMC_Licencia;
        $nombre_conductor = $datos_guiarem[0]->GUIAREMC_NombreConductor;
        $punto_partida = $datos_guiarem[0]->GUIAREMC_PuntoPartida;
        $arr_punt_part = explode('/', $punto_partida);
        $punto_llegada = $datos_guiarem[0]->GUIAREMC_PuntoLlegada;
        $arr_punt_lleg = explode('/', $punto_llegada);
        $empresa_transporte = $datos_guiarem[0]->EMPRP_Codigo;
        $fecha = mysql_to_human($datos_guiarem[0]->GUIAREMC_Fecha);
        $otro_motivo = $datos_guiarem[0]->GUIAREMC_OtroMotivo;

        $nombre_emprtrans = "";
        $ruc_emprtrans = "";
        if ($empresa_transporte != '') {
            $datos_emprtrans = $this->empresa_model->obtener_datosEmpresa($empresa_transporte);
            if (count($datos_emprtrans) > 0) {
                $ruc_emprtrans = $datos_emprtrans[0]->EMPRC_Ruc;
                $nombre_emprtrans = $datos_emprtrans[0]->EMPRC_RazonSocial;
            }
        }
        $nombre_tipodoc = '';
        if ($referencia != '') {
            $datos_doc = $this->documento_model->obtener($referencia);
            $nombre_tipodoc = $datos_doc[0]->DOCUC_Descripcion;
        }

        /* Datos del cliente */
        if ($tipo_oper == "C") {
            $cliente = $proveedor;
        }
        $datos_cliente = $this->cliente_model->obtener($cliente);
        $razon_social = utf8_decode($datos_cliente->nombre);
        $tipo_doc = ($datos_cliente->tipo == '0' ? 'D.N.1' : 'R.U.C.');
        $ruc = $datos_cliente->ruc;
        $distrito_cliente = $datos_cliente->distrito;
        $provincia_cliente = $datos_cliente->provincia;
        $departamento_cliente = $datos_cliente->departamento;

        $razon_social2 = '';
        if (strlen($razon_social) > 26) {
            $razon_social2 = substr($razon_social, 26);
            $razon_social = substr($razon_social, 0, 26);
        }
        $nombre_emprtrans2 = '';
        if (strlen($nombre_emprtrans) > 27) {
            $nombre_emprtrans2 = substr($nombre_emprtrans, 27);
            $nombre_emprtrans = substr($nombre_emprtrans, 0, 27);
        }
        $otro_motivo2 = '';
        if (strlen($otro_motivo) > 18) {
            $otro_motivo2 = substr($otro_motivo, 18);
            $otro_motivo = substr($otro_motivo, 0, 18);
        }

        /* Cabecera */
        //prep_pdf();

        $this->cezpdf = new Cezpdf('a4');
        $this->cezpdf->selectFont('system/application/libraries/fonts/Helvetica-Bold.afm');

        $this->cezpdf->ezText('', '', array("leading" => 108));

        $this->cezpdf->ezText($fecha, 10, array("leading" => 15, "left" => 30));
        $this->cezpdf->ezText($fecha_traslado, 10, array("leading" => 0, "left" => 190));

        $this->cezpdf->ezText(utf8_decode_seguro($arr_punt_part[0]), 10, array("leading" => 45, "left" => 25));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_part[1]) ? $arr_punt_part[1] : ''), 10, array("leading" => 0, "left" => 160));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_lleg[0]) ? $arr_punt_lleg[0] : ''), 10, array("leading" => 0, "left" => 315));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_lleg[1]) ? substr($arr_punt_lleg[1], 0, 15) : ''), 10, array("leading" => 0, "left" => 445));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_part[2]) ? $arr_punt_part[2] : ''), 10, array("leading" => 18, "left" => 5));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_part[3]) ? substr($arr_punt_part[3], 0, 15) : ''), 10, array("leading" => 0, "left" => 110));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_part[4]) ? substr($arr_punt_part[4], 0, 12) : ''), 10, array("leading" => 0, "left" => 197));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_lleg[2]) ? substr($arr_punt_lleg[2], 0, 20) : ''), 10, array("leading" => 0, "left" => 290));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_lleg[3]) ? substr($arr_punt_lleg[3], 0, 15) : ''), 10, array("leading" => 0, "left" => 395));
        $this->cezpdf->ezText(utf8_decode_seguro(isset($arr_punt_lleg[4]) ? $arr_punt_lleg[4] : ''), 9, array("leading" => 0, "left" => 490));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_part[5]) ? $arr_punt_part[5] : ''), 0, 12)), 10, array("leading" => 18, "left" => 25));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_part[6]) ? $arr_punt_part[6] : ''), 0, 8)), 10, array("leading" => 0, "left" => 100));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_part[7]) ? $arr_punt_part[7] : ''), 0, 8)), 10, array("leading" => 0, "left" => 200));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_lleg[5]) ? $arr_punt_lleg[5] : ''), 0, 8)), 10, array("leading" => 0, "left" => 315));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_lleg[6]) ? $arr_punt_lleg[6] : ''), 0, 20)), 10, array("leading" => 0, "left" => 383));
        $this->cezpdf->ezText(utf8_decode_seguro(substr((isset($arr_punt_lleg[7]) ? $arr_punt_lleg[7] : ''), 0, 20)), 10, array("leading" => 0, "left" => 492));


        $this->cezpdf->ezText(($razon_social2 != '' ? $razon_social . '-' : $razon_social), 10, array("leading" => 43, "left" => 122));
        $this->cezpdf->ezText($marca . ($placa != '' ? ' / ' . $placa : ''), 10, array("leading" => 0, "left" => 400));
        $this->cezpdf->ezText($razon_social2, 10, array("leading" => 10, "left" => -10));
        $this->cezpdf->ezText($ruc, 11, array("leading" => 9, "left" => 22));
        $this->cezpdf->ezText($certificado, 10, array("leading" => 0, "left" => 410));
        $this->cezpdf->ezText($tipo_doc . '   ' . $ruc, 10, array("leading" => 18, "left" => 152));
        $this->cezpdf->ezText($licencia, 10, array("leading" => 0, "left" => 388));

        $this->cezpdf->ezText('', '', array("leading" => 35));

        /* Detalle */
        $db_data = array();
        if (count($datos_detalle_guiarem) > 0) {
            foreach ($datos_detalle_guiarem as $indice => $valor) {
                $producto = $valor->PRODCTOP_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $costo = $valor->GUIAREMDETC_Costo;
                $venta = $valor->GUIAREMDETC_Venta;
                $peso = $valor->GUIAREMDETC_Peso;
                $descri = $valor->GUIAREMDETC_Descripcion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre = $datos_producto[0]->PROD_Nombre;
                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->GUIAREMDETC_Cantidad;
                //---------------------------------------------------------------------------   
                if ($tipo_oper == "C") {
                    $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp, $producto);
                } else {
                    $datos_serie = $this->seriemov_model->buscar_x_guiasap($guiasap, $producto);
                }
                if (count($datos_serie) > 0) {
                    $ser = "";
                    foreach ($datos_serie as $indices => $valor) {
                        $seriecodigo = $valor->SERIC_Numero;
                        $ser = $ser . " *" . $seriecodigo;
                    }
                }
                //------------------------------------------------------------------------------        


                $db_data[] = array(
                    'col1' => utf8_decode_seguro($descri),
                    'col2' => $prod_unidad,
                    'col3' => $prod_cantidad,
                    'col4' => $ser
                );
                $ser = "";
            }
        }
        $this->cezpdf->ezTable($db_data, '', '', array(
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 11,
            'cols' => array(
                'col1' => array('width' => 320, 'justification' => 'left'),
                'col2' => array('width' => 60, 'justification' => 'center'),
                'col3' => array('width' => 55, 'justification' => 'center'),
                'col4' => array('width' => 110, 'justification' => 'center')
            )
        ));

        $this->cezpdf->addText(35, 220, 10, utf8_decode_seguro($observacion));
        $this->cezpdf->addText(55, 182, 10, utf8_decode_seguro($nombre_emprtrans2 != '' ? $nombre_emprtrans . '-' : $nombre_emprtrans));
        $this->cezpdf->addText(20, 172, 10, utf8_decode_seguro($nombre_emprtrans2));
        $this->cezpdf->addText(50, 157, 10, $ruc_emprtrans);
        $this->cezpdf->addText(55, 117, 10, utf8_decode_seguro(strtoupper($nombre_tipodoc)));
        $this->cezpdf->addText(55, 97, 10, $numero_ref);

        $posx = 0;
        $posy = 0;
        switch ($tipo_movimiento) {
            case 1:
                $posx = 227;
                $posy = 185;
                break;
            case 2:
                $posx = 227;
                $posy = 176;
                break;
            case 3:
                $posx = 227;
                $posy = 160;
                break;
            case 4:
                $posx = 227;
                $posy = 151;
                break;
            case 5:
                $posx = 227;
                $posy = 142;
                break;
            case 6:
                $posx = 227;
                $posy = 133;
                break;
            case 7:
                $posx = 227;
                $posy = 117;
                break;
            case 8:
                $posx = 227;
                $posy = 108;
                break;
            case 9:
                $posx = 227;
                $posy = 99;
                break;
            case 10:
                $posx = 373;
                $posy = 185;
                break;
            case 11:
                $posx = 373;
                $posy = 177;
                break;
            case 12:
                $posx = 373;
                $posy = 169;
                break;
            case 13:
                $posx = 373;
                $posy = 160;
                break;
        }
        $this->cezpdf->addText($posx, $posy, 14, 'x');
        if ($tipo_movimiento == 13) {
            $this->cezpdf->addText(383, 154, 8, ($otro_motivo2 != '' ? $otro_motivo . '-' : $otro_motivo));
            $this->cezpdf->addText(383, 145, 8, $otro_motivo2);
        }
        $this->cezpdf->addText(368, 140, 10, utf8_decode_seguro('NÂ° DE O.COMPRA:'));
        $this->cezpdf->addText(368, 120, 10, utf8_decode_seguro($numero_ocompra));

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function guiatrans_ver_pdf_conmenbrete_formato1($codigo, $img)
    {

        $datos_guiarem = $this->guiatrans_model->obtener($codigo);
        $datos_detalle_guiarem = $this->guiatransdetalle_model->listar($codigo);
        $guiasap = $datos_guiarem[0]->GUIASAP_Codigo;
        $guiainp = $datos_guiarem[0]->GUIAINP_Codigo;
        $serie = $datos_guiarem[0]->GTRANC_Serie;
        $numero = $datos_guiarem[0]->GTRANC_Numero;
        $observacion = $datos_guiarem[0]->GTRANC_Observacion;
        $placa = $datos_guiarem[0]->GTRANC_Placa;
        $licencia = $datos_guiarem[0]->GTRANC_Licencia;
        $nombre_conductor = $datos_guiarem[0]->GTRANC_Chofer;
        $punto_partida = $datos_guiarem[0]->GTRANC_AlmacenOrigen;
        $arr_punt_part = explode('/', $punto_partida);
        $punto_llegada = $datos_guiarem[0]->GTRANC_AlmacenDestino;
        
        $empresa_transporte = $datos_guiarem[0]->EMPRP_Codigo;
        $fecha = mysql_to_human($datos_guiarem[0]->GTRANC_Fecha);
        $ruc_emprtrans = "";
        if ($empresa_transporte != '') {
            $datos_emprtrans = $this->empresa_model->obtener_datosEmpresa($empresa_transporte);
            if (count($datos_emprtrans) > 0) {
                $ruc_emprtrans = $datos_emprtrans[0]->EMPRC_Ruc;
                $nombre_emprtrans = $datos_emprtrans[0]->EMPRC_RazonSocial;
            }
        }
        $nombre_emprtrans2 = '';
        if (strlen($nombre_emprtrans) > 29) {
            $nombre_emprtrans2 = substr($nombre_emprtrans, 29);
            $nombre_emprtrans = substr($nombre_emprtrans, 0, 29);
        }
        if ($img == 1) {
            $notimg = "";
        } else {
            $notimg = "guia_transferencia.jpg";
        }
        /* Cabecera */
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/img_db/' . $notimg));
        $posicionX = 0;
        $posicionY = 0;
        
        $this->cezpdf->ezText("$serie - $numero", 25, array("leading" => 108, "left" => 410));

        $dataF[] = array(
                    'cols1' => $fecha,
                    'cols2' => $fecha
                );

        $this->cezpdf->ezText("", 10, array("leading" => 20, "left" => 40));

        $this->cezpdf->ezTable($dataF, '', '', array(
            'width' => 555,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 10,
            'cols' => array(
                'cols1' => array('width' => 180, 'justification' => 'left'),
                'cols2' => array('width' => 180, 'justification' => 'right')
            )
        ));
        //$this->cezpdf->addText($posicionX + 330, $posicionY + 673, 9, $fecha_traslado);
        //direccion partida
        //  $this->cezpdf->addText($posicionX + 70, $posicionY + 696, 8, utf8_decode_seguro(substr($arr_punt_part[0], 0, 37)));
        //
        //        $this->cezpdf->addText($posicionX + 62, $posicionY + 663, 8, utf8_decode_seguro(isset($arr_punt_part[1]) ? $arr_punt_part[1] . '321321321' : '9999999999999999'));
        //
        //direccion destino
        //  $direccion_destino = substr($arr_punt_lleg[0], 0, 37);
        /*$this->cezpdf->ezText($nombre_emprtrans, 25, array("leading" => 110, "left" => 390));
        $this->cezpdf->addText(80, $posicionY + 613, 8, utf8_decode_seguro($direccion_destino));
        $this->cezpdf->addText(398, $posicionY + 685, 10, utf8_decode_seguro($nombre_emprtrans));
        $this->cezpdf->addText(370, $posicionY + 650, 8, $ruc_emprtrans);
        $this->cezpdf->addText(515, $posicionY + 652, 8, $placa);
        $this->cezpdf->addText(375, $posicionY + 635, 8, $licencia);*/

        /* Detalle */

        $db_data = array();
        if (count($datos_detalle_guiarem) > 0) {
            $this->load->library("lib_props");

            foreach ($datos_detalle_guiarem as $indice => $valor) {
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $costo = $valor->GTRANDETC_Costo;
                $descri = $valor->GTRANDETC_Descripcion;
                $descri = str_replace('\\', '', $descri);
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $prod_cod = $datos_producto[0]->PROD_Codigo;

                $prod_nombre = $datos_producto[0]->PROD_Nombre;

                $prod_nombre = ($datos_producto[0]->PROD_GenericoIndividual == 'I') ? $prod_nombre.$this->lib_props->ObtenerSeriesComprobante($codigo,'GT',$datos_producto[0]->PROD_Codigo) : $prod_nombre;

                $prod_codigo = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad = $valor->GTRANDETC_Cantidad;

                //------------------------------------------------------------------------------        

                $array_producto = explode("/", $descri);

                $db_data[] = array(
                        'cols1' => $prod_codigo,
                        'cols2' => $prod_cantidad,
                        'cols3' => $prod_nombre,
                        'cols4' => $prod_unidad,
                        'cols5' => ""
                );
            }

            $namesCol = array(
                'cols1' => "CODIGO",
                'cols2' => "CANTIDAD",
                'cols3' => "DESCRIPCION",
                'cols4' => "UNIDAD",
                'cols5' => ""
            );

            $this->cezpdf->ezText("", 10, array("leading" => 180, "left" => 40));

            $this->cezpdf->ezTable($db_data, $namesCol, '', array(
                'width' => 555,
                'showLines' => 0,
                'shaded' => 0,
                'showHeadings' => 0,
                'xPos' => '288',
                'fontSize' => 7,
                'cols' => array(
                    'cols1' => array('width' => 50, 'justification' => 'center'),
                    'cols2' => array('width' => 40, 'justification' => 'center'),
                    'cols3' => array('width' => 320, 'justification' => 'left'),
                    'cols4' => array('width' => 70, 'justification' => 'left'),
                    'cols5' => array('width' => 40, 'justification' => 'left')
                )
            ));
        }

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'GT.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

}

?>