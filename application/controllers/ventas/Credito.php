<?php
include("system/application/libraries/pchart/pData.php");  
include("system/application/libraries/pchart/pChart.php"); 
include("system/application/libraries/cezpdf.php"); 
include("system/application/libraries/class.backgroundpdf.php"); 
class Credito extends CI_Controller
{
    
    public function __construct()
    {
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
//        $this->load->model('ventas/comprobante_model');
//        $this->load->model('ventas/comprobantedetalle_model');
        $this->load->model('ventas/credito_model');
       $this->load->model('ventas/creditodetalle_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('ventas/presupuesto_model');
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
		
        $this->somevar['compania'] = $this->session->userdata('compania');
		$this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
		$this->somevar['url'] = $_SERVER['REQUEST_URI'];
        
    }
    public function index(){
        $this->load->view('seguridad/inicio');
        $this->load->library('layout','layout');
    }
	
    public function obtener_detalle_comprobante($credito){
        $detalle               = $this->creditodetalle_model->listar($credito);
        $lista_detalles        = array();
        $datos_presupuesto  = $this->credito_model->obtener_credito($credito);
        $formapago          = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda             = $datos_presupuesto[0]->MONED_Codigo;
        $serie             = $datos_presupuesto[0]->CRED_Serie;
        $numero             = $datos_presupuesto[0]->CRED_Numero;
        $codigo_usuario     = '';
        $cliente            = $datos_presupuesto[0]->CLIP_Codigo;
        $tipo_doc            = $datos_presupuesto[0]->CRED_TipoDocumento;
        $temp               = $this->obtener_datos_cliente($cliente); 
        $ruc                = $temp['numdoc'];
        $razon_social       = $temp['nombre'];

        if(count($detalle)>0){
            foreach($detalle as $indice=>$valor)
            {
				$detpresup       = $valor->CREDET_Codigo;
                $producto        = $valor->PROD_Codigo;
                $unidad_medida   = $valor->UNDMED_Codigo;
                $cantidad        = $valor->CREDET_Cantidad;
				$igv100			 = round($valor->CREDET_Igv100,2);
                $pu              = round((($tipo_doc == 'F') ? $valor->CREDET_Pu : $valor->CREDET_Pu_ConIgv - ($valor->CREDET_Pu_ConIgv*$igv100/100)),2);
                $subtotal        = round((($tipo_doc == 'F') ? $valor->CREDET_Subtotal : $pu*$cantidad),2);
                $igv             = round($valor->CREDET_Igv,2);
                $descuento       = round($valor->CREDET_Descuento,2);
                $total            = round((($tipo_doc == 'F') ? $valor->CREDET_Total : $subtotal),2);
                $pu_conigv         = round($valor->CREDET_Pu_ConIgv,2);
                $subtotal_conigv   = round($valor->CREDET_Subtotal_ConIgv,2);
                $descuento_conigv  = round($valor->CREDET_Descuento_ConIgv,2);
                $observacion       = $valor->CREDET_Observacion;
                 
                $datos_producto     = $this->producto_model->obtener_producto($producto);
                $codigo_interno     = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto    = ($valor->CREDET_Descripcion!='' ? $valor->CREDET_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto    =  str_replace('"', "''", $nombre_producto);
                $flagGenInd         = $datos_producto[0]->PROD_GenericoIndividual;
                $costo              = $datos_producto[0]->PROD_CostoPromedio;
                $datos_umedida      = $this->unidadmedida_model->obtener($unidad_medida);
                // $nombre_unidad      = $datos_umedida[0]->UNDMED_Simbolo;
                
                
                $objeto   =   new stdClass();
                $objeto->CREDET_Codigo      = $detpresup;
                $objeto->PROD_Codigo         = $producto;
                $objeto->PROD_CodigoInterno  = $codigo_interno;
                $objeto->UNDMED_Codigo       = $unidad_medida;
                $objeto->UNDMED_Simbolo      = $nombre_unidad;
                $objeto->PROD_Nombre         = $nombre_producto;
                $objeto->PROD_GenericoIndividual    = $flagGenInd;
                $objeto->PROD_CostoPromedio         = $costo;
                $objeto->CREDET_Cantidad    = $cantidad;
                $objeto->CREDET_Pu          = $pu;
                $objeto->CREDET_Subtotal    = $subtotal;
                $objeto->CREDET_Descuento   = $descuento;
                $objeto->CREDET_Igv         = $igv;
                $objeto->CREDET_Total       = $total;
                $objeto->CREDET_Pu_ConIgv   = $pu_conigv;
                $objeto->CREDET_Subtotal_ConIgv    = $subtotal_conigv;
                $objeto->CREDET_Descuento_ConIgv   = $descuento_conigv;
                $objeto->Ruc                 = $ruc;
                $objeto->RazonSocial         = $razon_social;
                $objeto->CLIP_Codigo         = $cliente;
                $objeto->MONED_Codigo        = $moneda;
                $objeto->FORPAP_Codigo       = $formapago;
                $objeto->PRESUC_Serie       = $serie;
                $objeto->PRESUC_Numero       = $numero;
                $objeto->PRESUC_CodigoUsuario= $codigo_usuario;

                $lista_detalles[]            = $objeto;
            }
        }
        else{
            $objeto   =   new stdClass();
            $objeto->CREDET_Codigo      = '';
            $objeto->Ruc                 = $ruc;
            $objeto->RazonSocial         = $razon_social;
            $objeto->CLIP_Codigo         = $cliente;
            $objeto->MONED_Codigo        = $moneda;
            $objeto->FORPAP_Codigo       = $formapago;
            $objeto->PRESUC_Numero       = $numero;
            $objeto->PRESUC_CodigoUsuario= $codigo_usuario;
            $lista_detalles[]            = $objeto;
        }
         $resultado = json_encode($lista_detalles);
         
         echo $resultado;
    }
	
    public function listar($tipo_oper='V', $tipo_docu='C',$j='0', $limpia='')
    {	
		
		
        
        
		
		//************para validar permisos************//
		// parametros
		// 1 .- codigo rol
		// 2 .- url
		// $permiso 	= buscar_permiso($this->somevar['rol'],$this->somevar['url']);
		// $sesion 	= array('constante'=>$permiso[0]->constante,'menu'=>$permiso[0]->menu);
		// $this->session->set_userdata($sesion);
		//************para validar permisos************//
		
		$this->load->library('layout','layout');
		
        if($limpia=='1'){
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
        }
        $filter = new stdClass();
        if(count($_POST)>0){
            $filter->fechai        = $this->input->post('fechai');
            $filter->fechaf        = $this->input->post('fechaf');
            $filter->serie         = $this->input->post('serie');
            $filter->numero        = $this->input->post('numero');
            $filter->cliente       = $this->input->post('cliente');
            $filter->ruc_cliente   = $this->input->post('ruc_cliente');
            $filter->nombre_cliente= $this->input->post('nombre_cliente');
            $filter->proveedor       = $this->input->post('proveedor');
            $filter->ruc_proveedor   = $this->input->post('ruc_proveedor');
            $filter->nombre_proveedor= $this->input->post('nombre_proveedor');
            $filter->producto      = $this->input->post('producto');
            $filter->codproducto      = $this->input->post('codproducto');
            $filter->nombre_producto  = $this->input->post('nombre_producto');
            $this->session->set_userdata(array('fechai'=>$filter->fechai, 'fechaf'=>$filter->fechaf, 'serie'=>$filter->serie, 'numero'=>$filter->numero, 'cliente'=>$filter->cliente, 'ruc_cliente'=>$filter->ruc_cliente, 'nombre_cliente'=>$filter->nombre_cliente, 'proveedor'=>$filter->proveedor, 'ruc_proveedor'=>$filter->ruc_proveedor, 'nombre_proveedor'=>$filter->nombre_proveedor, 'producto'=>$filter->producto, 'codproducto'=>$filter->codproducto, 'nombre_producto'=>$filter->nombre_producto));
        }
        else{
            $filter->fechai         = $this->session->userdata('fechai');
            $filter->fechaf         = $this->session->userdata('fechaf');
            $filter->serie          = $this->session->userdata('serie');
            $filter->numero         = $this->session->userdata('numero');
            $filter->cliente        = $this->session->userdata('cliente');
            $filter->ruc_cliente    = $this->session->userdata('ruc_cliente');
            $filter->nombre_cliente = $this->session->userdata('nombre_cliente');
            $filter->proveedor        = $this->session->userdata('proveedor');
            $filter->ruc_proveedor    = $this->session->userdata('ruc_proveedor');
            $filter->nombre_proveedor = $this->session->userdata('nombre_proveedor');
            $filter->producto       = $this->session->userdata('producto');
            $filter->codproducto    = $this->session->userdata('codproducto');
            $filter->nombre_producto= $this->session->userdata('nombre_producto');
        }
        $data['fechai']            = $filter->fechai;
        $data['fechaf']            = $filter->fechaf;
        $data['serie']             = $filter->serie;
        $data['numero']            = $filter->numero;
        $data['cliente']           = $filter->cliente;
        $data['ruc_cliente']       = $filter->ruc_cliente;
        $data['nombre_cliente']    = $filter->nombre_cliente;
        $data['proveedor']           = $filter->proveedor;
        $data['ruc_proveedor']       = $filter->ruc_proveedor;
        $data['nombre_proveedor']    = $filter->nombre_proveedor;
        $data['producto']          = $filter->producto;
        $data['codproducto']       = $filter->codproducto;
        $data['nombre_producto']   = $filter->nombre_producto;
        
        $data['registros']   = count($this->credito_model->buscar_creditos($tipo_oper,$tipo_docu,$filter));
        $conf['base_url']    = site_url('ventas/credito/listar/'.$tipo_oper.'/'.$tipo_docu);
        $conf['per_page']    = 50;
        $conf['num_links']   = 3;
        $conf['first_link']  = "&lt;&lt;";
        $conf['last_link']   = "&gt;&gt;";
        $conf['total_rows']  = $data['registros'];
        $conf['uri_segment'] = 6;
        $offset              = (int)$this->uri->segment(6);
        $listado_comprobantes    = $this->credito_model->buscar_creditos($tipo_oper,$tipo_docu,$filter,$conf['per_page'],$offset);
        $item                = $j+1;
        $lista               = array();
        if(count($listado_comprobantes)>0){
            foreach($listado_comprobantes as $indice=>$valor)
            {
                $codigo          = $valor->CRED_Codigo;
                $fecha           = mysql_to_human($valor->CRED_Fecha);
                $serie           = $valor->CRED_Serie;
                $numero          = $valor->CRED_Numero;
                $guiarem_codigo  = $valor->CRED_GuiaRemCodigo;
                $docurefe_codigo = $valor->CRED_DocuRefeCodigo;
                $nombre          = $valor->nombre;
                $total           = $valor->MONED_Simbolo.' '.number_format($valor->CRED_total,2);
                $estado          = $valor->CRED_FlagEstado;

                $img_estado     =($estado=='1' ? "<img src='".base_url()."images/active.png' alt='Activo' title='Activo' />" : "<img src='".base_url()."images/inactive.png' alt='Anulado' title='Anulado' />") ;
                $editar         = "<a href='javascript:;' onclick='editar_comprobante(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='javascript:;' onclick='ver_comprobante_pdf(".$codigo.")' target='_parent'><img src='".base_url()."images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2            = "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(".$codigo.")' target='_parent'><img src='".base_url()."images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar       = "<a href='javascript:;' onclick='eliminar_comprobante(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                if($tipo_oper=='V')
                    $lista[]        = array($item++,$fecha,$serie, $numero,$guiarem_codigo,$docurefe_codigo,$nombre,$total,$img_estado,$editar,$ver,$ver2);
                else
                    $lista[]        = array($item++,$fecha,$serie, $numero,$guiarem_codigo,$docurefe_codigo,$nombre,$total,$img_estado,$editar,$ver,$ver2);
            }
        }
        $data['titulo_tabla']    = "RELACIÓN DE ".strtoupper($this->obtener_tipo_documento($tipo_docu))."S";
        $data['titulo_busqueda'] = "BUSCAR ".strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_oper']   = $tipo_oper;
        $data['tipo_docu']   = $tipo_docu;
        $data['lista']      = $lista;
        $data['oculto']     = form_hidden(array('base_url'=>base_url(), 'tipo_oper'=>$tipo_oper, "tipo_docu"=>$tipo_docu));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('ventas/credito_index',$data);
    }
    public function credito_nuevo($tipo_oper='V', $tipo_docu='C')
    {
		
		// if($this->session->userdata('constante') != 1){
			// echo "no tiene permisos";
			// exit;
		// }else{
			// $permiso = buscar_permiso_rol_menu($this->somevar['rol'],$this->session->userdata('menu'));
			// if($permiso[0]->crear != '1'){
				// echo "NO tiene permisos para crear";
			// }else{
				// echo "SI tiene permisos para crear";
			// }
			// exit;
		// }
		
        $this->load->library('layout','layout');
        unset($_SESSION['serie']);
        $comp_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
            
        $codigo               = "";
        $data['codigo']       = $codigo;
        $data['contiene_igv'] = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv=='1')?true:false);
        $oculto               = form_hidden(array('codigo'=>$codigo,'base_url'=>base_url(), 'tipo_oper'=>$tipo_oper, 'tipo_docu'=>$tipo_docu, 'contiene_igv'=>($data['contiene_igv']==true?'1':'0')));
        $data['url_action']   = base_url()."index.php/ventas/credito/credito_insertar";
        $data['titulo']       = "REGISTRAR ".strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu']    = $tipo_docu;
        $data['tipo_oper']    = $tipo_oper;
        $data['formulario']   = "frmCredito";
        $data['oculto']       = $oculto;
        $lista_almacen        = $this->almacen_model->seleccionar();
        
        //$data['cboAlmacen']       = form_dropdown("almacen",$lista_almacen,obtener_val_x_defecto($lista_almacen)," class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboMoneda']    = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '2');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '2');
        $data['cboPresupuesto']  = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu), 'PRESUP_Codigo', array('PRESUC_Numero','nombre'),'', array('','::Seleccione::'), ' / ');
        $data['cboOrdencompra']  = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper), 'OCOMP_Codigo', array('OCOMC_Numero','nombre'),'', array('','::Seleccione::'), ' - ');
        $data['cboGuiaRemision'] = $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper), 'GUIAREMP_Codigo', array('codigo','nombre'),'', array('','::Seleccione::'), ' / ');
        $data['cboVendedor']     = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno','PERSC_ApellidoMaterno', 'PERSC_Nombre'),'', array('','::Seleccione::'), ' ');
        $data['tdc']             = $this->tipocambio_model->obtener_tdc_dolar(date('Y-m-d'));
        $data['serie']='';
        $data['numero']='';
        if($tipo_oper=='V'){
            $temp=$this->obtener_serie_numero($tipo_docu);
        }
        
        $data['cliente']         = "";
        $data['ruc_cliente']     = "";
        $data['nombre_cliente']  = "";
        $data['proveedor']         = "";
        $data['ruc_proveedor']     = "";
        $data['nombre_proveedor']  = "";
        $data['detalle_comprobante'] = array();
        $data['observacion']     = "";
        $data['focus']           = "";
        $data['pedido']          = "";
        $data['descuento']       = "0";
        $data['igv']             = $comp_confi[0]->COMPCONFIC_Igv;
        $data['hidden']          = "";
        $data['preciototal']     = "";
        $data['descuentotal']    = "";
        $data['igvtotal']        = "";
        $data['importetotal']    = "";
        $data['preciototal_conigv']  = "";
        $data['descuentotal_conigv'] = "";
        $data['hidden']          = "";
        $data['observacion']     = "";
        $data['guiarem_codigo']  = "";
        $data['docurefe_codigo'] = "";
        $data['estado']          = "1";
        
        $data['modo_impresion']  = "1";
        if($tipo_docu!='D'){
            if(FORMATO_IMPRESION==1)
                $data['modo_impresion']  = "2";
            else
                $data['modo_impresion']  = "1";
        }
        $data['hoy']             = mysql_to_human(mdate("%Y-%m-%d ",time()));
        $atributos               = array('width'=>700,'height'=>450,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido               = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente']	 = anchor_popup('empresa/cliente/ventana_busqueda_cliente',$contenido,$atributos, 'linkVerCliente');
        $data['verproveedor']     = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor',$contenido,$atributos,'linkVerProveedor');
        $data['verproducto']     = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos,'linkVerProducto');
        
        $ultimo_serie_numero  = $this->credito_model->ultimo_serie_numero($tipo_oper, $tipo_docu);
        $data['serie_suger']  = $ultimo_serie_numero['serie'];
        $data['numero_suger'] = $ultimo_serie_numero['numero'];
        $this->layout->view('ventas/credito_nuevo',$data);
    }
    
    public function credito_insertar()
    {
	
		
        if($this->input->post('serie')=='')   
           exit ('{"result":"error", "campo":"serie"}');
        if($this->input->post('numero')=='')   
           exit ('{"result":"error", "campo":"numero"}');
        if($this->input->post('tipo_oper')=='V' && $this->input->post('cliente')=='')   
           exit ('{"result":"error", "campo":"ruc_cliente"}');
        if($this->input->post('tipo_oper')=='C' && $this->input->post('proveedor')=='')   
           exit ('{"result":"error", "campo":"ruc_proveedor"}');
        if($this->input->post('moneda')=='0' || $this->input->post('moneda')=='')
           exit ('{"result":"error", "campo":"moneda"}');
        if($this->input->post('estado')=='0' && $this->input->post('observacion')=='')
           exit ('{"result":"error", "campo":"observacion"}');
//        if($this->input->post('tdc')=='')   
//           exit ('{"result":"error", "campo":"tdc"}');
    
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        
        $filter=new stdClass();               
        $filter->CRED_TipoOperacion = $tipo_oper;
        $filter->CRED_TipoDocumento = $tipo_docu;
        
        if($this->input->post('forma_pago')!='' && $this->input->post('forma_pago')!='0')
        $filter->FORPAP_Codigo     = $this->input->post('forma_pago');
        $filter->CRED_Observacion   = strtoupper($this->input->post('observacion'));
        $filter->CRED_Fecha         = human_to_mysql($this->input->post('fecha'));
        $filter->CRED_Numero        = $this->input->post('numero');
        $filter->CRED_Serie         = $this->input->post('serie');
       
        
        $filter->MONED_Codigo      = $this->input->post('moneda');
        $filter->CRED_descuento100  = $this->input->post('descuento');
        $filter->CRED_igv100  = $this->input->post('igv');
        $filter->CRED_FlagEstado        = $this->input->post('estado');
        
        if($tipo_oper=='V')
            $filter->CLIP_Codigo       = $this->input->post('cliente');
        else
            $filter->PROVP_Codigo      = $this->input->post('proveedor');
        if($this->input->post('presupuesto')!='' && $this->input->post('presupuesto')!='0')
             $filter->PRESUP_Codigo = $this->input->post('presupuesto');
        if($this->input->post('ordencompra')!='' && $this->input->post('ordencompra')!='0')
             $filter->OCOMP_Codigo  = $this->input->post('ordencompra');
        if($this->input->post('guiaremision')!='' && $this->input->post('guiaremision')!='0')
             $filter->GUIAREMP_Codigo  = $this->input->post('guiaremision');
        $filter->CRED_GuiaRemCodigo     = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CRED_DocuRefeCodigo    = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CRED_ModoImpresion      = '1';
        if($this->input->post('modo_impresion')!='0' && $this->input->post('modo_impresion')!='') 
            $filter->CRED_ModoImpresion      = $this->input->post('modo_impresion');
        if($tipo_docu!='D'){
            $filter->CRED_subtotal      = $this->input->post('preciototal');
            $filter->CRED_descuento     = $this->input->post('descuentotal');
            $filter->CRED_igv           = $this->input->post('igvtotal');
        }else{
            $filter->CRED_subtotal_conigv       = $this->input->post('preciototal_conigv');
            $filter->CRED_descuento_conigv      = $this->input->post('descuentotal_conigv');
        }
        $filter->CRED_total         = $this->input->post('importetotal');
        if($this->input->post('vendedor')!='')
            $filter->CRED_Vendedor      = $this->input->post('vendedor');
        $filter->CRED_TDC = $this->input->post('tdc');
        
        //Datos cabecera de la guiasa.
        if($tipo_oper=='V'){
            $filter3 = new stdClass();
            $filter3->TIPOMOVP_Codigo = 6;
            $filter3->ALMAP_Codigo    = $this->input->post('almacen')!='' ? $this->input->post('almacen') : NULL;
            $filter3->CLIP_Codigo     = $this->input->post('cliente');;
            $filter3->GUIASAC_Fecha   = human_to_mysql($this->input->post('fecha'));
            $filter3->GUIASAC_Observacion = strtoupper($this->input->post('observacion'));
            $filter3->USUA_Codigo         = $this->somevar['user'];
            $guia_id  = $this->guiasa_model->insertar($filter3);
            $filter->CRED_FlagMueveStock = '1';
            $filter->GUIASAP_Codigo = $guia_id;
        }
        
        $comprobante          = $this->credito_model->insertar_credito($filter);
        
        $flagBS     = $this->input->post('flagBS');
        $prodcodigo       = $this->input->post('prodcodigo');
        $prodcantidad     = $this->input->post('prodcantidad');
        if($tipo_docu!='D'){
            $prodpu        = $this->input->post('prodpu');
            $prodprecio    =  $this->input->post('prodprecio');
            $proddescuento =  $this->input->post('proddescuento');
            $prodigv       =  $this->input->post('prodigv');
        }
        else{
            $prodprecio_conigv    =  $this->input->post('prodprecio_conigv');
            $proddescuento_conigv =  $this->input->post('proddescuento_conigv');
        }
        $prodimporte      =  $this->input->post('prodimporte');
        $prodpu_conigv    = $this->input->post('prodpu_conigv');
        $produnidad       = $this->input->post('produnidad');
        $flagGenInd       = $this->input->post('flagGenIndDet');
        $detaccion        = $this->input->post('detaccion');
        $proddescuento100 =  $this->input->post('proddescuento100');
        $prodigv100       =  $this->input->post('prodigv100');
        $prodcosto        = $this->input->post('prodcosto');
        $proddescri       = $this->input->post('proddescri');
        
        if(is_array($prodcodigo))
        {
            foreach($prodcodigo as $indice=>$valor)
            {   
                  $filter=new stdClass();
                  $filter->CRED_Codigo=$comprobante;
                  $filter->PROD_Codigo=$prodcodigo[$indice];
                  if($flagBS[$indice]=='D')
                      $filter->UNDMED_Codigo =$produnidad[$indice];
                  $filter->CREDET_Cantidad =$prodcantidad[$indice];
                  if($tipo_docu!='D'){
                      $filter->CREDET_Pu = $prodpu[$indice];
                      $filter->CREDET_Subtotal =$prodprecio[$indice];
                      $filter->CREDET_Descuento =$proddescuento[$indice];
                      $filter->CREDET_Igv =$prodigv[$indice];
                  }
                  else{
                      $filter->CREDET_Subtotal_ConIgv = $prodprecio_conigv[$indice];
                      $filter->CREDET_Descuento_ConIgv = $proddescuento_conigv[$indice];
                  }
                  $filter->CREDET_Total = $prodimporte[$indice];
                  $filter->CREDET_Pu_ConIgv = $prodpu_conigv[$indice];
                  $filter->CREDET_Descuento100 = $proddescuento100[$indice];
                  $filter->CREDET_Igv100 =$prodigv100[$indice];
                  if($tipo_oper=='V')
                      $filter->CREDET_Costo = $prodcosto[$indice];
                  $filter->CREDET_Descripcion =  strtoupper($proddescri[$indice]);
                  $filter->CREDET_GenInd = $flagGenInd[$indice];
                  $filter->CREDET_Observacion = "";
                 
                  if($detaccion[$indice]!='e'){
                     if($tipo_oper=='V'){
                         $filter4  = new stdClass();
                         $filter4->GUIASAP_Codigo      = $guia_id;
                         $filter4->PRODCTOP_Codigo     = $prodcodigo[$indice];
                         $filter4->UNDMED_Codigo       = $produnidad[$indice];
                         $filter4->GUIASADETC_Cantidad = $prodcantidad[$indice];
                         $filter4->GUIASADETC_Costo    = $prodcosto[$indice];
                         $filter4->GUIASADETC_GenInd   = $flagGenInd[$indice];
                         $filter4->GUIASADETC_Descripcion   = strtoupper($proddescri[$indice]);;
                         $this->guiasadetalle_model->insertar($filter4);
                     }
                     $this->creditodetalle_model->insertar($filter);
                  }
            }
        }
        exit('{"result":"ok", "codigo":"'.$comprobante.'"}');
    }
    public function credito_editar($codigo, $tipo_oper='V', $tipo_docu='C')
    {   $this->load->library('layout','layout');
        unset($_SESSION['serie']);
        $comp_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
    
        $datos_comprobante   = $this->credito_model->obtener_credito($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $ordencompra     = $datos_comprobante[0]->OCOMP_Codigo;
        $guiaremision     = $datos_comprobante[0]->GUIAREMP_Codigo;
        $serie           = $datos_comprobante[0]->CRED_Serie;
        $numero          = $datos_comprobante[0]->CRED_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor       = $datos_comprobante[0]->PROVP_Codigo;
        $forma_pago      = $datos_comprobante[0]->FORPAP_Codigo;
        $moneda          = $datos_comprobante[0]->MONED_Codigo;
        $subtotal        = $datos_comprobante[0]->CRED_subtotal;
        $descuento       = $datos_comprobante[0]->CRED_descuento;
        $igv             = $datos_comprobante[0]->CRED_igv;
        $total           = $datos_comprobante[0]->CRED_total;
        $subtotal_conigv = $datos_comprobante[0]->CRED_subtotal_conigv;
        $descuento_conigv= $datos_comprobante[0]->CRED_descuento_conigv;
        $igv100          = $datos_comprobante[0]->CRED_igv100;
        $descuento100    = $datos_comprobante[0]->CRED_descuento100;
        $guiarem_codigo  = $datos_comprobante[0]->CRED_GuiaRemCodigo;
        $docurefe_codigo = $datos_comprobante[0]->CRED_DocuRefeCodigo;
        $observacion     = $datos_comprobante[0]->CRED_Observacion;
        $modo_impresion  = $datos_comprobante[0]->CRED_ModoImpresion;
        $estado          = $datos_comprobante[0]->CRED_FlagEstado;
        $fecha           = mysql_to_human($datos_comprobante[0]->CRED_Fecha);
        $vendedor        = $datos_comprobante[0]->CRED_Vendedor;
        $tdc             = $datos_comprobante[0]->CRED_TDC;
        
        $ruc_cliente='';
        $nombre_cliente='';
        $nombre_proveedor='';
        $ruc_proveedor='';
        if($cliente!='' && $cliente!='0'){
            $datos_cliente   = $this->cliente_model->obtener($cliente);
            if($datos_cliente){
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente  = $datos_cliente->ruc;
            }
        }
        elseif($proveedor!='' && $proveedor!='0'){
            $datos_proveedor   = $this->proveedor_model->obtener($proveedor);
            if($datos_proveedor){
                $nombre_proveedor = $datos_proveedor->nombre;
                $ruc_proveedor  = $datos_proveedor->ruc;
            }
        }
        $data['codigo']         = $codigo;   
        $data['tipo_docu']      = $tipo_docu;
        $data['tipo_oper']      = $tipo_oper;
        $lista_almacen          = $this->almacen_model->seleccionar();
        $data['cboAlmacen']     = form_dropdown("almacen",$lista_almacen,obtener_val_x_defecto($lista_almacen)," class='comboMedio' style='width:125px;' id='almacen'");
        $data['cboPresupuesto'] = $this->OPTION_generador($this->presupuesto_model->listar_presupuestos_nocomprobante($tipo_oper, $tipo_docu, $codigo), 'PRESUP_Codigo', array('PRESUC_Numero','nombre'),$presupuesto, array('','::Seleccione::'), ' / ');
        $data['cboOrdencompra'] = $this->OPTION_generador($this->ocompra_model->listar_ocompras_nocomprobante($tipo_oper, $codigo), 'OCOMP_Codigo', array('OCOMC_Numero','nombre'),$ordencompra, array('','::Seleccione::'), ' / ');
        $data['cboGuiaRemision']= $this->OPTION_generador($this->guiarem_model->listar_guiarem_nocomprobante($tipo_oper, $codigo), 'GUIAREMP_Codigo', array('codigo','nombre'),$guiaremision, array('','::Seleccione::'), ' / ');
        $data['cboFormaPago']   = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', $forma_pago);
        $data['cboMoneda']      = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion',$moneda);
        $data['cboVendedor']    = $this->OPTION_generador($this->directivo_model->listar_directivo($this->session->userdata('empresa'), '4'), 'DIREP_Codigo', array('PERSC_ApellidoPaterno','PERSC_ApellidoMaterno', 'PERSC_Nombre'),$vendedor, array('','::Seleccione::'), ' ');
        $data['serie']          = $serie;
        $data['numero']         = $numero;
        
        $data['descuento']      = $descuento100;
        $data['igv']            = $igv100;
        $data['preciototal']    = $subtotal;
        $data['descuentotal']   = $descuento;
        $data['igvtotal']       = $igv;
        $data['importetotal']   = $total;
        $data['preciototal_conigv']    = $subtotal_conigv;
        $data['descuentotal_conigv']   = $descuento_conigv;
        $data['cliente']        = $cliente;
        $data['ruc_cliente']    = $ruc_cliente;
        $data['nombre_cliente'] = $nombre_cliente;
        $data['proveedor']        = $proveedor;
        $data['ruc_proveedor']    = $ruc_proveedor;
        $data['nombre_proveedor'] = $nombre_proveedor;
        $data['contiene_igv']   = (($comp_confi[0]->COMPCONFIC_PrecioContieneIgv=='1')?true:false);
        $oculto                 = form_hidden(array('codigo'=>$codigo,'base_url'=>base_url(), 'tipo_oper'=>$tipo_oper, 'tipo_docu'=>$tipo_docu, 'contiene_igv'=>($data['contiene_igv']==true?'1':'0')));
        $data['titulo']         = "EDITAR ".strtoupper($this->obtener_tipo_documento($tipo_docu));
        $data['tipo_docu']      = $tipo_docu;
        $data['formulario']     = "frmCredito";
        $data['oculto']         = $oculto;
        $data['url_action']     = base_url()."index.php/ventas/credito/credito_modificar";
        $atributos              = array('width'=>700,'height'=>450,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido              = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente']	= anchor_popup('empresa/cliente/ventana_busqueda_cliente',$contenido,$atributos, 'linkVerCliente');
        $data['verproveedor']   = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor',$contenido,$atributos,'linkVerProveedor');
        $data['verproducto']    = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos,'linkVerProducto');
        $data['hoy']            = $fecha;
        $data['guiarem_codigo'] = $guiarem_codigo;
        $data['docurefe_codigo']= $docurefe_codigo;
        $data['observacion']    = $observacion;
        $data['estado']         = $estado;
        $data['hidden']         = "";
        $data['focus']          = "";
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger']    = "";
        $data['numero_suger']   = "";
        $data['tdc']            = $tdc;
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        $data['detalle_comprobante']= $detalle_comprobante;
        $this->layout->view('ventas/credito_nuevo',$data);
    }
    public function credito_modificar()
    {  
        
        if($this->input->post('serie')=='')   
           exit ('{"result":"error", "campo":"serie"}');
        if($this->input->post('numero')=='')   
           exit ('{"result":"error", "campo":"numero"}');
        if($this->input->post('tipo_oper')=='V' && $this->input->post('cliente')=='')   
           exit ('{"result":"error", "campo":"ruc_cliente"}');
        if($this->input->post('tipo_oper')=='C' && $this->input->post('proveedor')=='')   
           exit ('{"result":"error", "campo":"ruc_proveedor}');
        if($this->input->post('moneda')=='0' || $this->input->post('moneda')=='')
           exit ('{"result":"error", "campo":"moneda}');
        if($this->input->post('estado')=='0' && $this->input->post('observacion')=='')
           exit ('{"result":"error", "campo":"observacion"}');
        
        $codigo = $this->input->post('codigo');
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo_docu = $this->input->post('tipo_docu');
        
        $filter=new stdClass();
        $filter->FORPAP_Codigo = NULL;
        if($this->input->post('forma_pago')!='' && $this->input->post('forma_pago')!='0')
        $filter->FORPAP_Codigo     = $this->input->post('forma_pago');
        $filter->CRED_Observacion   = strtoupper($this->input->post('observacion'));
        $filter->CRED_Fecha         = human_to_mysql($this->input->post('fecha'));
        $filter->CRED_Numero        = $this->input->post('numero');
        $filter->CRED_Serie         = $this->input->post('serie');
        //$filter->CRED_Serie         = $this->input->post('serie');
        $filter->MONED_Codigo      = $this->input->post('moneda');
        $filter->CRED_descuento100  = $this->input->post('descuento');
        $filter->CRED_igv100        = $this->input->post('igv');
        
        if($tipo_oper=='V')
            $filter->CLIP_Codigo       = $this->input->post('cliente');  
        else
            $filter->PROVP_Codigo      = $this->input->post('proveedor');            
        $filter->PRESUP_Codigo         = NULL;
        if($this->input->post('presupuesto')!='' && $this->input->post('presupuesto')!='0')
            $filter->PRESUP_Codigo     = $this->input->post('presupuesto');
        $filter->OCOMP_Codigo          = NULL;
        if($this->input->post('ordencompra')!='' && $this->input->post('ordencompra')!='0')
            $filter->OCOMP_Codigo      = $this->input->post('ordencompra');
        $filter->GUIAREMP_Codigo       = NULL;
        if($this->input->post('guiaremision')!='' && $this->input->post('guiaremision')!='0')
             $filter->GUIAREMP_Codigo  = $this->input->post('guiaremision');
        $filter->CRED_GuiaRemCodigo     = strtoupper($this->input->post('guiaremision_codigo'));
        $filter->CRED_DocuRefeCodigo    = strtoupper($this->input->post('docurefe_codigo'));
        $filter->CRED_FlagEstado        = $this->input->post('estado');
        $filter->CRED_ModoImpresion = '1';
        if($this->input->post('modo_impresion')!='0' && $this->input->post('modo_impresion')!='')
            $filter->CRED_ModoImpresion = $this->input->post('modo_impresion');
        if($tipo_docu!='D'){
            $filter->CRED_subtotal      = $this->input->post('preciototal');
            $filter->CRED_descuento     = $this->input->post('descuentotal');
            $filter->CRED_igv           = $this->input->post('igvtotal');
        }else{
            $filter->CRED_subtotal_conigv       = $this->input->post('preciototal_conigv');
            $filter->CRED_descuento_conigv      = $this->input->post('descuentotal_conigv');
        }
        $filter->CRED_total         = $this->input->post('importetotal');
        $filter->CRED_Vendedor      = NULL;
        if($this->input->post('vendedor')!='')
            $filter->CRED_Vendedor  = $this->input->post('vendedor');
        
        $this->credito_model->modificar_comprobante($codigo,$filter);
        
        $prodcodigo    = $this->input->post('prodcodigo');
        $flagBS     = $this->input->post('flagBS');
        $prodcantidad  = $this->input->post('prodcantidad');
        if($tipo_docu!='D'){
            $prodpu        = $this->input->post('prodpu');
            $prodprecio    =  $this->input->post('prodprecio');
            $proddescuento =  $this->input->post('proddescuento');
            $prodigv       =  $this->input->post('prodigv');
        }
        else{
            $prodprecio_conigv    =  $this->input->post('prodprecio_conigv');
            $proddescuento_conigv =  $this->input->post('proddescuento_conigv');
        }
        $prodimporte   =  $this->input->post('prodimporte');
        $prodpu_conigv = $this->input->post('prodpu_conigv');
        $produnidad    = $this->input->post('produnidad');
        $detaccion     = $this->input->post('detaccion');
        $detacodi      = $this->input->post('detacodi');
        $prodigv100    = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $prodcosto        = $this->input->post('prodcosto');
        $proddescri       = $this->input->post('proddescri');
        
        if(is_array($detacodi)>0)
        {
            foreach($detacodi as $indice=>$valor){
              $detalle_accion    = $detaccion[$indice];
              
              $filter=new stdClass();
              $filter->CRED_Codigo=$codigo;
              $filter->PROD_Codigo=$prodcodigo[$indice];
              if($flagBS[$indice]=='D')
                   $filter->UNDMED_Codigo =$produnidad[$indice];
              $filter->CREDET_Cantidad =$prodcantidad[$indice];
              if($tipo_docu!='D'){
                  $filter->CREDET_Pu = $prodpu[$indice];
                  $filter->CREDET_Subtotal =$prodprecio[$indice];
                  $filter->CREDET_Descuento =$proddescuento[$indice];
                  $filter->CREDET_Igv =$prodigv[$indice];
              }
              else{
                  $filter->CREDET_Subtotal_ConIgv  =$prodprecio_conigv[$indice];
                  $filter->CREDET_Descuento_ConIgv  =$proddescuento_conigv[$indice];
              }
              $filter->CREDET_Total =$prodimporte[$indice];
              $filter->CREDET_Pu_ConIgv =$prodpu_conigv[$indice];
              $filter->CREDET_Descuento100 =$proddescuento100[$indice];
              $filter->CREDET_Igv100  =$prodigv100[$indice];
              if($tipo_oper=='V')
                      $filter->CREDET_Costo = $prodcosto[$indice];
              $filter->CREDET_Descripcion =strtoupper($proddescri[$indice]);
              $filter->CREDET_Observacion ="";
              
              
              if($detalle_accion=='n'){
                    $this->creditodetalle_model->insertar($filter);  
              }elseif($detalle_accion=='m') {
                      $this->creditodetalle_model->modificar($valor, $filter);
              }elseif($detalle_accion=='e'){
                      $this->creditodetalle_model->eliminar($valor);
              }
            }
        }
        exit('{"result":"ok", "codigo":"'.$codigo.'"}');
    }
    public function comprobante_eliminar(){
        $this->load->library('layout','layout');
        
        $comprobante = $this->input->post('comprobante');
        $this->creditos_model->eliminar_comprobante($credito);
    }
    public function comprobante_buscar(){

    }
    function obtener_datos_cliente($cliente, $tipo_docu='F'){
         $datos_cliente     = $this->cliente_model->obtener_datosCliente($cliente);
         $empresa           = $datos_cliente[0]->EMPRP_Codigo;
         $persona           = $datos_cliente[0]->PERSP_Codigo;
         $tipo              = $datos_cliente[0]->CLIC_TipoPersona;
         if($tipo==0){
             $datos_persona = $this->persona_model->obtener_datosPersona($persona);
             $nombre = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
             if($tipo_docu!='B')
                $numdoc = $datos_persona[0]->PERSC_Ruc;
             else
                $numdoc = $datos_persona[0]->PERSC_NumeroDocIdentidad;
             $direccion = $datos_persona[0]->PERSC_Direccion;
         }
         elseif($tipo==1){
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion = $emp_direccion[0]->EESTAC_Direccion;
         }
         
         return array('numdoc'=>$numdoc, 'nombre'=>$nombre, 'direccion'=>$direccion);
    }
    public function obtener_lista_detalles($codigo){
        $detalle                = $this->creditodetalle_model->listar($codigo);
        $lista_detalles        = array();
        if(count($detalle)>0){
            foreach($detalle as $indice=>$valor)
            {
                $detacodi        = $valor->CREDET_Codigo;
                $producto        = $valor->PROD_Codigo;
				//echo $producto;exit;
                $unidad          = $valor->UNDMED_Codigo;
                $cantidad        = $valor->CREDET_Cantidad;
                $pu              = $valor->CREDET_Pu;
                $subtotal        = $valor->CREDET_Subtotal;
                $igv             = $valor->CREDET_Igv;
                $descuento       = $valor->CREDET_Descuento;
                $total           = $valor->CREDET_Total;
                $pu_conigv       = $valor->CREDET_Pu_ConIgv;
                $subtotal_conigv = $valor->CREDET_Subtotal_ConIgv;
                $descuento_conigv= $valor->CREDET_Descuento_ConIgv;
                $descuento100    = $valor->CREDET_Descuento100;
                $igv100          = $valor->CREDET_Igv100;
                $observacion     = $valor->CREDET_Observacion;
                $datos_producto  = $this->producto_model->obtener_producto($producto);
                $flagBS          = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad    = $this->unidadmedida_model->obtener($unidad);
                $GenInd          = $valor->CREDET_GenInd;
                $costo           = $valor->CREDET_Costo;
                $nombre_producto = ($valor->CREDET_Descripcion!='' ? $valor->CREDET_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\','',$nombre_producto);
                $codigo_interno  = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario  = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad   = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Simbolo : '';
                
                $objeto   =   new stdClass();
                $objeto->CREDET_Codigo      = $detacodi;
                $objeto->flagBS              = $flagBS;
                $objeto->PROD_Codigo         = $producto;
                $objeto->PROD_CodigoInterno  = $codigo_interno;
                $objeto->PROD_CodigoUsuario  = $codigo_usuario;
                $objeto->UNDMED_Codigo       = $unidad;
                $objeto->UNDMED_Simbolo      = $nombre_unidad;
                $objeto->CREDET_GenInd        = $GenInd;
                $objeto->CREDET_Costo         = $costo;
                $objeto->PROD_Nombre         = $nombre_producto;
                $objeto->CREDET_Cantidad    = $cantidad;
                $objeto->CREDET_Pu          = $pu;
                $objeto->CREDET_Subtotal    = $subtotal;
                $objeto->CREDET_Descuento   = $descuento;
                $objeto->CREDET_Igv         = $igv;
                $objeto->CREDET_Total       = $total;
                $objeto->CREDET_Pu_ConIgv          = $pu_conigv;
                $objeto->CREDET_Subtotal_ConIgv    = $subtotal_conigv;
                $objeto->CREDET_Descuento_ConIgv   = $descuento_conigv;
                $objeto->CREDET_Descuento100 = $descuento100;
                $objeto->CREDET_Igv100      = $igv100;
                $objeto->CREDET_Observacion = $observacion;
                $lista_detalles[]           = $objeto;
            }
        }
        return $lista_detalles;
    }
    
    public function comprobante_ver_pdf($codigo, $tipo_docu='F'){
        switch(FORMATO_IMPRESION){
            case 1: //Formato para ferresat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_formato1($codigo);
                else
                    $this->comprobante_ver_pdf_formato1_boleta($codigo);
                break;
            case 2:  //Formato para jimmyplat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_formato2($codigo);
                else
                    $this->comprobante_ver_pdf_formato2_boleta($codigo);
                break;
            case 3:  //Formato para jimmyplat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_formato3($codigo);
                else
                    $this->comprobante_ver_pdf_formato3_boleta($codigo);
                break;
            case 4:  //Formato para ferremax
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_formato4($codigo);
                else
                    $this->comprobante_ver_pdf_formato4_boleta($codigo);
                break;
			case 5:  //Formato para G Y C
				if($_SESSION['compania'] == "1"){
					if($tipo_docu!='B')
						$this->comprobante_ver_pdf_formato5($codigo);
					else
						$this->comprobante_ver_pdf_formato5_boleta($codigo);
				}else{
					if($tipo_docu!='B')
						$this->comprobante_ver_pdf_formato6($codigo);
					else
						$this->comprobante_ver_pdf_formato6_boleta($codigo);
				}
                break;
			case 6:  //Formato para CYL
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_formato7($codigo);
                else
                    $this->comprobante_ver_pdf_formato7_boleta($codigo);
                break;
            default: comprobante_ver_pdf_formato1($codigo); break;
        }
    }
    public function comprobante_ver_pdf_formato1($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $modo_impresion  = ((int)$datos_comprobante[0]->CPC_ModoImpresion>0 ? $datos_comprobante[0]->CPC_ModoImpresion : '1');
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4', 'portrait');
        $this->cezpdf->selectFont('system/application/libraries/fonts/Helvetica-Bold.afm');
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>108));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),10, array("leading"=>10, "left"=>40));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),10, array("leading"=>18, "left"=>40));
        $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),10, array("leading"=>0, "left"=>380));
        $this->cezpdf->ezText(utf8_decode_seguro(strtoupper(mes_textual(substr($fecha,3,2)))),10, array("leading"=>0, "left"=>430));
        //$this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,9,1)),9, array("leading"=>0, "left"=>530));
        $this->cezpdf->addText(575,675,10, substr($fecha,9,1));
        $this->cezpdf->ezText($ruc,10, array("leading"=>20, "left"=>40));
        $this->cezpdf->ezText($guiarem_codigo,10, array("leading"=>0, "left"=>400));
       
        
        $this->cezpdf->ezText('','', array("leading"=>30));
        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format(($modo_impresion=='1' ? $pu_conigv : $valor->CPDEC_Pu),2),
                'cols7'=>number_format($valor->CPDEC_Cantidad*($modo_impresion=='1' ? $pu_conigv : $valor->CPDEC_Pu),2)
                );
         }
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>11,
            'cols'=>array(
                'cols2'=>array('width'=>45,'justification'=>'center'),
                'cols5'=>array('width'=>365,'justification'=>'left'),
                'cols6'=>array('width'=>55,'justification'=>'right'),
                'cols7'=>array('width'=>70,'justification'=>'right'),
            )
         ));
                 
        $this->cezpdf->addText(90,155,10,utf8_decode_seguro(strtoupper($docurefe_codigo)));
        
        /*Totales*/    
         
        $this->cezpdf->addText(90,123,11,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(520,100,11,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(470,82,11,$igv100.' %');
        $this->cezpdf->addText(520,82,11,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(520,64,11,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato2($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        $nombre_cliente2='';
        if(strlen($nombre_cliente)>49){
            $nombre_cliente2=substr($nombre_cliente,49);
            $nombre_cliente=substr($nombre_cliente,0,49);
            
        }
        $direccion2='';
        if(strlen($direccion)>49){
            $direccion2=substr($direccion,49);
            $direccion=substr($direccion,0,49);
            
        }
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>110));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente.($nombre_cliente2!='' ? '-' : '')),9, array("leading"=>47, "left"=>75));
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente2),9, array("leading"=>12, "left"=>21));
        $this->cezpdf->ezText(utf8_decode_seguro($fecha),9, array("leading"=>-3, "left"=>380));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>0, "left"=>500));
        
        $this->cezpdf->ezText(utf8_decode_seguro($direccion.($direccion2!='' ? '-': '')),9, array("leading"=>20, "left"=>90));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion2),9, array("leading"=>12, "left"=>21));
        $this->cezpdf->ezText($ruc,9, array("leading"=>0, "left"=>383));
        $this->cezpdf->ezText($docurefe_codigo,9, array("leading"=>0, "left"=>500));

       
        
        $this->cezpdf->ezText('','', array("leading"=>28));
        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols1'=>'',
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2),
                'cols8'=> ''
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>12,
            'cols'=>array(
                'cols1'=>array('width'=>50,'justification'=>'center'),
                'cols2'=>array('width'=>60,'justification'=>'center'),
                'cols5'=>array('width'=>365,'justification'=>'left'),
                'cols6'=>array('width'=>55,'justification'=>'right'),
                'cols7'=>array('width'=>75,'justification'=>'right'),
                'cols8'=>array('width'=>20,'justification'=>'right'),
            )
         ));
                 
         /*Totales*/    
         
        $this->cezpdf->addText(80,102,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(520,80,12,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(483,58,12,$igv100);
        $this->cezpdf->addText(520,58,12,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(520,35,12,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato3($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
        //$this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/instrume_fondo_factura.jpg')); 
 
        $this->cezpdf->ezText('','' ,array('leading'=>106));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>1, "left"=>120));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>12, "left"=>65));
        $this->cezpdf->ezText($ruc,9, array("leading"=>11, "left"=>65));
        $this->cezpdf->ezText($guiarem_codigo,9, array("leading"=>0, "left"=>332));
        $this->cezpdf->ezText($fecha,9, array("leading"=>0, "left"=>477));
        $this->cezpdf->ezText($docurefe_codigo,9, array("leading"=>13, "left"=>65));

        $this->cezpdf->ezText('','', array("leading"=>20));
              
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols3'=>$valor->UNDMED_Simbolo,
                'cols4'=>$valor->PROD_CodigoUsuario,
                'cols5'=>utf8_decode_seguro($valor->PROD_Nombre),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         $this->cezpdf->ezText('','', array("leading"=>10));
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols1'=>array('width'=>40,'justification'=>'right'),
                'cols2'=>array('width'=>30,'justification'=>'center'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>60,'justification'=>'left'),
                'cols5'=>array('width'=>250,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>95,'justification'=>'right'),
            )
         ));
         
        $son='SON : '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
        $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
  
        $this->cezpdf->addText($pos,185,10,$son); 
        $this->cezpdf->addText(490,137,10,$moneda_simbolo.' '.number_format($total,2));
        $this->cezpdf->addText(390,137,10,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(275,137,10,$moneda_simbolo.' '.number_format(($total-$igv),2));
        $this->cezpdf->addText(175,137,10,$moneda_simbolo.' '.number_format($descuento,2));
        $this->cezpdf->addText(65,137,10,$moneda_simbolo.' '.number_format($total,2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato4($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
        $this->cezpdf->selectFont('system/application/libraries/fonts/Helvetica-Bold.afm');
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>84));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($fecha),10, array("leading"=>42, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),10, array("leading"=>14, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),10, array("leading"=>14, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),10, array("leading"=>14, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($docurefe_codigo),10, array("leading"=>14, "left"=>55));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),10, array("leading"=>2, "left"=>430));        
        
        $this->cezpdf->ezText('','', array("leading"=>37));        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
			$producto       = $valor->PROD_Codigo;
			$marca_prod = $this->producto_model->obtener_marca_modelo_por_producto($producto);
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45))." / ".$marca_prod[0]->MARCC_Descripcion." / ".$marca_prod[0]->PROD_Modelo,
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>395,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
         /*Totales*/    
         
        $this->cezpdf->addText(20,73,11,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(520,73,11,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(520,57,11,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(484,57,11,$igv100);
        $this->cezpdf->addText(520,43,11,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato1_boleta($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal_conigv        = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv       = $datos_comprobante[0]->CPC_descuento_conigv;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;

        $temp=$this->obtener_datos_cliente($cliente);
        $nombre_cliente=$temp['nombre'];
        $ruc=$temp['numdoc'];
        $direccion=$temp['direccion'];
        
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9,array("leading"=>130, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9,array("leading"=>17, "left"=>30));
        
        $this->cezpdf->ezText('','',array("leading"=>25));
              
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            $nomprod = $valor->PROD_Nombre;
            if(strlen($nomprod)>41)
                $nomprod=substr($nomprod,0,38).' ...';
            $db_data[] = array(
                'cols1'=>'',
                'cols2'=>$valor->CPDEC_Cantidad ,
                'cols3'=>utf8_decode_seguro($nomprod),
                'cols4'=>number_format($valor->CPDEC_Pu_ConIgv,2),
                'cols5'=>number_format($valor->CPDEC_Total,2),
                'cols6'=>''
                );
         }
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'center'),
                'cols2'=>array('width'=>45,'justification'=>'center'),
                'cols3'=>array('width'=>205,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'right'),
                'cols5'=>array('width'=>50,'justification'=>'right'),
                'cols6'=>array('width'=>150,'justification'=>'center')
                )
         ));
         
         /**Sub Totales**/
         $delta=130;
         $positionx = 400;
         $positiony = 120+$delta;
         $this->cezpdf->addText(120,$positiony,10, strtoupper(num2letras(round($total,2))));
         $this->cezpdf->addText($positionx,$positiony-19,10,number_format($total,2));

        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato2_boleta($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal_conigv        = $datos_comprobante[0]->CPC_subtotal_conigv;
        $descuento_conigv       = $datos_comprobante[0]->CPC_descuento_conigv;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;

        $temp=$this->obtener_datos_cliente($cliente);
        $nombre_cliente=$temp['nombre'];
        $ruc=$temp['numdoc'];
        $direccion=$temp['direccion'];
        
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9,array("leading"=>130, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9,array("leading"=>17, "left"=>30));
        
        $this->cezpdf->ezText('','',array("leading"=>25));
              
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            $nomprod = $valor->PROD_Nombre;
            if(strlen($nomprod)>41)
                $nomprod=substr($nomprod,0,38).' ...';
            $db_data[] = array(
                'cols1'=>'',
                'cols2'=>$valor->CPDEC_Cantidad ,
                'cols3'=>utf8_decode_seguro($nomprod),
                'cols4'=>number_format($valor->CPDEC_Pu_ConIgv,2),
                'cols5'=>number_format($valor->CPDEC_Total,2),
                'cols6'=>''
                );
         }
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'center'),
                'cols2'=>array('width'=>45,'justification'=>'center'),
                'cols3'=>array('width'=>205,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'right'),
                'cols5'=>array('width'=>50,'justification'=>'right'),
                'cols6'=>array('width'=>150,'justification'=>'center')
                )
         ));
         
         /**Sub Totales**/
         $delta=130;
         $positionx = 400;
         $positiony = 120+$delta;
         $this->cezpdf->addText(120,$positiony,10, strtoupper(num2letras(round($total,2))));
         $this->cezpdf->addText($positionx,$positiony-19,10,number_format($total,2));

        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_formato3_boleta($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
        //$this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/instrume_fondo_boleta.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>11));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>97, "left"=>56));
        $this->cezpdf->ezText(utf8_decode_seguro($fecha),9, array("leading"=>0, "left"=>300));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>0, "left"=>440));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>11, "left"=>56));
        $this->cezpdf->ezText(utf8_decode_seguro(($telefono!='') ? $telefono : $movil),9, array("leading"=>0, "left"=>395));
       
        
        $this->cezpdf->ezText('','', array("leading"=>35));
        
             
        /*Listado de detalles*/
        $db_data=array();
        $item=0;
        foreach($detalle_comprobante as $indice=>$valor){
            $item++;
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols0'=>' &nbsp; ',
                'cols1'=>$item,
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols3'=>$valor->UNDMED_Simbolo,
                'cols4'=>$valor->PROD_CodigoUsuario,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2),
                'cols8'=>''
                );
         }
          
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols0'=>array('width'=>10,'justification'=>'center'),
                'cols1'=>array('width'=>35,'justification'=>'center'),
                'cols2'=>array('width'=>30,'justification'=>'center'),
                'cols3'=>array('width'=>35,'justification'=>'center'),
                'cols4'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>255,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>80,'justification'=>'left'),
                'cols8'=>array('width'=>10,'justification'=>'left')
            )
         ));
                 
         /*Totales*/    
         $son='SON: '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
         $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
        $this->cezpdf->addText($pos,140,9,$son);
        $this->cezpdf->addText(500,100,10,$moneda_simbolo.' '.number_format(($total),2));
        
        //$this->cezpdf->addText(250,105,9,(int)substr($fecha,0,2));
        //$this->cezpdf->addText(290,105,9,utf8_decode_seguro(mes_textual(substr($fecha,3,2))));
        //$this->cezpdf->addText(400,105,9,substr($fecha,8,2));
 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_conmenbrete($codigo, $tipo_docu='F'){
        switch(FORMATO_IMPRESION){
            case 1: //Formato para ferresat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_conmenbrete_formato1($codigo);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato1_boleta($codigo);
                break;
            case 2:  //Formato para jimmyplat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_conmenbrete_formato2($codigo);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato2_boleta($codigo);
                break;
            case 3:  //Formato para jimmyplat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_conmenbrete_formato3($codigo);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato3_boleta($codigo);
                break;
            case 4:  //Formato para jimmyplat
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_conmenbrete_formato4($codigo);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato4_boleta($codigo);
                break;
			case 5:  //Formato para CYG
				if($_SESSION['compania'] == "1"){
					if($tipo_docu!='B')
						$this->comprobante_ver_pdf_conmenbrete_formato5($codigo);
					else
						$this->comprobante_ver_pdf_conmenbrete_formato5_boleta($codigo);
				}else{
					if($tipo_docu!='B')
						$this->comprobante_ver_pdf_conmenbrete_formato6($codigo);
					else
						$this->comprobante_ver_pdf_conmenbrete_formato6_boleta($codigo);
				}
				break;
			case 6:  //Formato para CYL
                if($tipo_docu!='B')
                    $this->comprobante_ver_pdf_conmenbrete_formato7($codigo);
                else
                    $this->comprobante_ver_pdf_conmenbrete_formato7_boleta($codigo);
                break;
			case 8:  //formato para impacto modelo pdf
			// if($_SESSION['compania'] == "1"){
				if($tipo_docu!='B')
					$this->comprobante_ver_pdf_conmenbrete_formato8($codigo);
				else
					$this->comprobante_ver_pdf_conmenbrete_formato8_boleta($codigo);
			// }else{
				/*if($tipo_docu!='B')
					$this->comprobante_ver_pdf_conmenbrete_formato8_1($codigo);
				else
					$this->comprobante_ver_pdf_conmenbrete_formato8_1_boleta($codigo);*/
			// }
				break;
            default: comprobante_ver_pdf_conmenbrete_formato1($codigo, $tipo_docu); break;
        }
    }
	
	public function comprobante_ver_html($codigo, $tipo_docu='F') {
		//echo FORMATO_IMPRESION;exit;
		switch(FORMATO_IMPRESION){
			case 8:	//formato de impresion para impacto	
			// if($_SESSION['compania'] == "1"){
				if($tipo_docu!='B')
					$this->formatos_de_impresion_F($codigo, $tipo_docu);
				else
					$this->formatos_de_impresion_B($codigo, $tipo_docu);
			// }else{
				// if($tipo_docu !='B')
					// $this->formatos_de_impresion_F2($codigo, $tipo_docu);//para mostrar formato de impresion para html para la otra compañia
				// else
					// $this->formatos_de_impresion_B2($codigo, $tipo_docu);
			// }
			break;
		}
	}
	
    public function comprobante_ver_pdf_conmenbrete_formato1($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/ferresat_fondo_factura.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>88));
        $this->cezpdf->ezText($serie.'           '.$numero,18, array("leading"=>20,'left'=>350));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>22, "left"=>40));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>40));
        $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>380));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>430));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,9,1)),9, array("leading"=>0, "left"=>525));
        $this->cezpdf->ezText($ruc,9, array("leading"=>20, "left"=>40));
        $this->cezpdf->ezText($guiarem_codigo,9, array("leading"=>0, "left"=>380));
       
        
        $this->cezpdf->ezText('','', array("leading"=>30));
        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols2'=>array('width'=>35,'justification'=>'center'),
                'cols5'=>array('width'=>360,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>70,'justification'=>'right'),
            )
         ));
                 
        $this->cezpdf->addText(70,127,9,utf8_decode_seguro(strtoupper($docurefe_codigo)));
        
        /*Totales*/    
        
        $this->cezpdf->addText(70,108,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(500,83,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(450,65,11,$igv100.' %');
        $this->cezpdf->addText(500,65,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(500,47,9,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_conmenbrete_formato2($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        $nombre_cliente2='';
        if(strlen($nombre_cliente)>49){
            $nombre_cliente2=substr($nombre_cliente,49);
            $nombre_cliente=substr($nombre_cliente,0,49);
            
        }
        $direccion2='';
        if(strlen($direccion)>49){
            $direccion2=substr($direccion,49);
            $direccion=substr($direccion,0,49);
            
        }
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/jimmyplast_fondo_factura.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>74));
        $this->cezpdf->ezText($serie,18, array("leading"=>22,'left'=>325));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>410));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente.($nombre_cliente2!='' ? '-' : '')),9, array("leading"=>47, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente2),9, array("leading"=>12, "left"=>-4));
        $this->cezpdf->ezText(utf8_decode_seguro($fecha),9, array("leading"=>0, "left"=>340));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>0, "left"=>440));
        
        $this->cezpdf->ezText(utf8_decode_seguro($direccion.($direccion2!='' ? '-': '')),9, array("leading"=>17, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion2),9, array("leading"=>12, "left"=>-4));
        $this->cezpdf->ezText($ruc,9, array("leading"=>0, "left"=>340));
        $this->cezpdf->ezText($docurefe_codigo,9, array("leading"=>0, "left"=>440));

       
        
        $this->cezpdf->ezText('','', array("leading"=>31));
        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>585,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>12,
            'cols'=>array(
                'cols2'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>360,'justification'=>'left'),
                'cols6'=>array('width'=>55,'justification'=>'right'),
                'cols7'=>array('width'=>75,'justification'=>'right'),
            )
         ));
                 
         /*Totales*/    
         
        $this->cezpdf->addText(70,107,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(505,87,12,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(468,65,12,$igv100);
        $this->cezpdf->addText(505,65,12,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(505,42,12,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_conmenbrete_formato3($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/instrume_fondo_factura.jpg')); 
 
        $this->cezpdf->ezText('','' ,array('leading'=>47));
        $this->cezpdf->ezText($serie.'         '.$numero,18, array('left'=>350));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>29, "left"=>120));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>14, "left"=>65));
        $this->cezpdf->ezText($ruc,9, array("leading"=>13, "left"=>65));
        $this->cezpdf->ezText($guiarem_codigo,9, array("leading"=>0, "left"=>332));
        $this->cezpdf->ezText($fecha,9, array("leading"=>0, "left"=>477));
        $this->cezpdf->ezText($docurefe_codigo,9, array("leading"=>13, "left"=>65));

        $this->cezpdf->ezText('','', array("leading"=>20));
              
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols3'=>$valor->UNDMED_Simbolo,
                'cols4'=>$valor->PROD_CodigoUsuario,
                'cols5'=>utf8_decode_seguro($valor->PROD_Nombre),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         $this->cezpdf->ezText('','', array("leading"=>10));
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols1'=>array('width'=>40,'justification'=>'right'),
                'cols2'=>array('width'=>30,'justification'=>'center'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>60,'justification'=>'left'),
                'cols5'=>array('width'=>250,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>95,'justification'=>'right'),
            )
         ));
         
        $son='SON : '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
        $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
  
        $this->cezpdf->addText($pos,175,10,$son); 
        $this->cezpdf->addText(490,127,10,$moneda_simbolo.' '.number_format($total,2));
        $this->cezpdf->addText(390,127,10,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(275,127,10,$moneda_simbolo.' '.number_format(($total-$igv),2));
        $this->cezpdf->addText(175,127,10,$moneda_simbolo.' '.number_format($descuento,2));
        $this->cezpdf->addText(65,127,10,$moneda_simbolo.' '.number_format($total,2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_conmenbrete_formato4($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/ferremax_fondo_factura.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>64));
        $this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>335));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>435));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>18, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>-2, "left"=>380));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>48));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>-2, "left"=>390));
        
        $this->cezpdf->ezText('','', array("leading"=>30));        
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols2'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>335,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
         /*Totales*/    
         
        $this->cezpdf->addText(70,103,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(500,103,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(500,85,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(454,85,9,$igv100);
        $this->cezpdf->addText(500,67,9,$moneda_simbolo.' '.number_format(($total),2));
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function comprobante_ver_pdf_conmenbrete_formato3_boleta($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/instrume_fondo_boleta.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>72));
        $this->cezpdf->ezText($serie.'         '.$numero,18, array("leading"=>0,'left'=>350));
                
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>28, "left"=>56));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>0, "left"=>440));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>12, "left"=>56));
        $this->cezpdf->ezText(utf8_decode_seguro(($telefono!='') ? $telefono : $movil),9, array("leading"=>0, "left"=>395));
       
        
        $this->cezpdf->ezText('','', array("leading"=>35));
        
             
        /*Listado de detalles*/
        $db_data=array();
        $item=0;
        foreach($detalle_comprobante as $indice=>$valor){
            $item++;
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols1'=>$item,
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols3'=>$valor->UNDMED_Simbolo,
                'cols4'=>$valor->PROD_CodigoUsuario,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2),
                'cols8'=>''
                );
         }
          
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols1'=>array('width'=>35,'justification'=>'center'),
                'cols2'=>array('width'=>30,'justification'=>'center'),
                'cols3'=>array('width'=>35,'justification'=>'center'),
                'cols4'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>255,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>80,'justification'=>'right'),
                'cols8'=>array('width'=>10,'justification'=>'right')
            )
         ));
                 
         /*Totales*/    
        $son='SON: '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
        $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
        $this->cezpdf->addText($pos,128,9,$son);
        $this->cezpdf->addText(500,87,9,$moneda_simbolo.' '.number_format(($total),2));
        
        //$this->cezpdf->addText(250,105,9,(int)substr($fecha,0,2));
        //$this->cezpdf->addText(290,105,9,utf8_decode_seguro(mes_textual(substr($fecha,3,2))));
        //$this->cezpdf->addText(400,105,9,substr($fecha,8,2));
 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    
    /*Auxiliares*/
    public function obtener_tipo_documento($tipo){
        $tiponom='factura';
        switch($tipo){
            case 'C': $tiponom=' Nota de Credito'; break;
            case 'D': $tiponom='Nota de Debito'; break;
            case 'N': $tiponom='comprobante'; break;
        }
        return $tiponom;
    }
    public function obtener_serie_numero($tipo_docu){
        $data['numero']='';
        $data['serie']='';
        switch($tipo_docu){
            case 'C': $codtipodocu='8'; break;
            case 'D': $codtipodocu='9'; break;
            case 'N': $codtipodocu='14'; break;
            default:  $codtipodocu='0'; break;
        }
        $datos_configuracion     = $this->configuracion_model->obtener_numero_documento($this->somevar['compania'],$codtipodocu);
        
        if(count($datos_configuracion)>0){
            $data['serie']          = $datos_configuracion[0]->CONFIC_Serie;
            $data['numero']          = $datos_configuracion[0]->CONFIC_Numero + 1;
        }
        return $data;
    }
	public function reportes(){
		$anio = $this->comprobante_model->anios_para_reportes('V');
		$combo ='<select id="anioVenta" name="anioVenta">';
		$combo .='<option value="0">Seleccione...</option>';
		foreach($anio as $key=>$value){
			$combo .='<option value="'.$value->anio.'">'.$value->anio.'</option>';
		}
		$combo .='</select>';
		
		$combo2 ='<select id="anioVenta2" name="anioVenta2">';
		$combo2 .='<option value="0">Seleccione...</option>';
		foreach($anio as $key=>$value){
			$combo2 .='<option value="'.$value->anio.'">'.$value->anio.'</option>';
		}
		$combo2 .='</select>';
		
		$combo3 ='<select id="anioVenta3" name="anioVenta3">';
		$combo3 .='<option value="0">Seleccione...</option>';
		foreach($anio as $key=>$value){
			$combo3 .='<option value="'.$value->anio.'">'.$value->anio.'</option>';
		}
		$combo3 .='</select>';
    
		$combo4 ='<select id="anioVenta4" name="anioVenta4">';
		$combo4 .='<option value="0">Seleccione...</option>';
		foreach($anio as $key=>$value){
			$combo4 .='<option value="'.$value->anio.'">'.$value->anio.'</option>';
		}
		$combo4 .='</select>';
		
        $data['fechai']        = form_input(array("name"=>"fechai","id"=>"fechai","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
        $data['fechaf']        = form_input(array("name"=>"fechaf","id"=>"fechaf","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
        $atributos             = array('width'=>600,'height'=>400,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido             = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor']  = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor',$contenido,$atributos);
        $data['verproducto']   = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos);
        
        $data['titulo']    = "REPORTES DE VENTAS";
        $data['combo']    = $combo;
        $data['combo2']    = $combo2;
        $data['combo3']    = $combo3;
        $data['combo4']    = $combo4;
        $this->layout->view('ventas/comprobante_reporte',$data);
    }
	
	public function estadisticas(){
        /*Imagen 1*/ 
        $listado=$this->comprobante_model->reporte_ocompra_5_clie_mas_importantes(); 
        
        if(count($listado)==0){ // Esto significa que no hay ordenes de compra por tando no muestros ningun reporte
             echo '<h3>Ha ocurrido un problema</h3>
                      <span style="color:#ff0000">No se ha encontrado Órdenes de Venta</span>';
             exit;
        }
         $temp1=array(0,0,0,0,0);
         $temp2=array('Vacio','Vacio','Vacio','Vacio','Vacio');
         foreach($listado as $item=>$reg){
             $temp1[$item]=$reg->total;
             if(strlen($reg->nombre)>30)
                $temp2[$item]=substr($reg->nombre,0,28).'... S/.'.$reg->total;
             else
                 $temp2[$item]=$reg->nombre.' S/.'.$reg->total;
         }


         $DataSet = new pData;  
         $DataSet->AddPoint($temp1,"Serie1");  
         $DataSet->AddPoint($temp2,"Serie2");  
         $DataSet->AddAllSeries();  
         $DataSet->SetAbsciseLabelSerie("Serie2");  

         // Initialise the graph  
         $Test = new pChart(600,200);  
         $Test->drawFilledRoundedRectangle(7,7,593,193,5,240,240,240);  
         $Test->drawRoundedRectangle(5,5,595,195,5,230,230,230);  

         // Draw the pie chart  
         $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf",8);  
         $Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);  
         $Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);  

         $Test->Render("images/img_dinamic/imagen1.png");  
         echo '<h3>1. Los 5 clientes más importantes</h3>
               Según el monto (S/.) histórico órdenes de venta<br />
               <img style="margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen1.png" alt="Imagen 1" />';
		
         /*Imagen 2*/ 
         $listado=$this->comprobante_model->reporte_oventa_monto_x_mes(); 
         $reg=$listado[0];
         //
         
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
         $Test->Render("images/img_dinamic/imagen2.png");  
         echo '<h3>2. Montos (S/.) de órdenes de venta según mes</h3>
               Considerando el presente año<br />
               <img style="margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen2.png" alt="Imagen 2" />';
     
         
         /*Imagen 3*/ 
         $listado=$this->comprobante_model->reporte_oventa_cantidad_x_mes(); 
         $reg=$listado[0];
         
         $DataSet = new pData;  
         $DataSet->AddPoint(array($reg->enero,$reg->febrero,$reg->marzo,$reg->abril,$reg->mayo, $reg->junio,$reg->julio,$reg->agosto,$reg->setiembre,$reg->octubre,$reg->noviembre,$reg->diciembre),"Serie1");
         $DataSet->AddPoint(array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic"),"Serie2");  
         $DataSet->AddAllSeries();  
         $DataSet->RemoveSerie("Serie2");  
         $DataSet->SetAbsciseLabelSerie("Serie2");  
         $DataSet->SetYAxisName("Cantidad de O. de Venta"); 
         $DataSet->SetXAxisName("Meses");
 

         // Initialise the graph  
         $Test = new pChart(600,230);  
         $Test->drawGraphAreaGradient(132,153,172,50,TARGET_BACKGROUND);  
         $Test->setFontProperties("system/application/libraries/pchart/Fonts/tahoma.ttf",8);  
         $Test->setGraphArea(60,20,585,180);  
         $Test->drawGraphArea(213,217,221,FALSE);  
         $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,213,217,221,TRUE,0,2);  
         $Test->drawGraphAreaGradient(162,183,202,50);  
         $Test->drawGrid(4,TRUE,230,230,230,20);  

         // Draw the line chart  
         $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
         $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2);  

         // Draw the legend  
         $Test->setFontProperties("Fonts/tahoma.ttf",8);  

         // Render the picture  
         $Test->Render("images/img_dinamic/imagen3.png");  
         echo '<h3>3. Cantidades de órdenes de venta según mes</h3>
               Considerando el presente año<br />
               <img style="margin-top:5px; margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen3.png" alt="Imagen 3" />';
		
		/*Imagen 4 => COMPRAS*/ 
         //$listado=$this->ocompra_model->reporte_ocompra_monto_x_mes(); 
         $listado=$this->ocompra_model->reporte_comparativo_compras_ventas('V'); 
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
         $Test->Render("images/img_dinamic/imagen4.png");  
		echo '<h3>4. Ventas</h3>
               Considerando las ventas en el presente año<br />
			   <img style="margin-top:5px; margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen4.png" alt="Imagen 4" />
			   <br />';
		/*Imagen 5 => VENTAS*/ 
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
			   <img style="margin-top:5px; margin-bottom:20px;" src="'.base_url().'images/img_dinamic/imagen5.png" alt="Imagen 5" />';*/
    }
	
	public function ver_reporte_pdf($params){
        $temp=(explode('_', $params));
        $fechai=$temp[0];
        $fechaf=$temp[1];
        $proveedor=$temp[2];
        $producto=$temp[3];
        $aprobado=$temp[4];
        $ingreso=$temp[5];
		
        $usuario=$this->usuario_model->obtener($this->somevar['user']);
        $persona=$this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy=date('d/m/Y');
        $listado=$this->comprobante_model->buscar_comprobante_venta($fechai, $fechaf, $proveedor , $producto, $aprobado, $ingreso);
        
        if($fechai!=''){
            $temp=explode('-',$fechai);
            $fechai=$temp[2].'/'.$temp[1].'/'.$temp[0];
        }
        if($fechaf!=''){
            $temp=explode('-',$fechaf);
            $fechaf=$temp[2].'/'.$temp[1].'/'.$temp[0];
        }
        $nomprovee='';
        if($proveedor!=''){
            $temp=$this->cliente_model->obtener_datosCliente($proveedor);
            if($temp[0]->CLIC_TipoPersona=='0'){
                $temp=$this->persona_model->obtener_datosPersona($temp[0]->PERSP_Codigo);
                $nomprovee=$temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            }
            else{
                $temp=$this->empresa_model->obtener_datosEmpresa($temp[0]->EMPRP_Codigo);
                $nomprovee=$temp[0]->EMPRC_RazonSocial;
            }
        }
        $nomprod='';
        if($producto!=''){
            $temp=$this->producto_model->obtener_producto($producto);
            $nomprod=$temp[0]->PROD_Nombre;
        }
        $nomaprob='';
        if($aprobado=='0')
            $nomaprob='Pendente';
        elseif($aprobado=='1')
            $nomaprob='Aprobado';
        elseif($aprobado=='2')
            $nomaprob='Desaprobado';
        
        $nomingre='';
        if($ingreso=='0')
            $nomingre='Pendiente';
        elseif($ingreso=='1')
            $nomingre='Si';
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $delta=20;
        $options = array("leading"=>15,"left"=>0);
        $this->cezpdf->ezText('Usuario:  '.$persona[0]->PERSC_Nombre.' '.$persona[0]->PERSC_ApellidoPaterno.' '.$persona[0]->PERSC_ApellidoMaterno.'       Fecha: '.$fechahoy,7,$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText('REPORTE DE ORDENES DE VENTA',17,$options);
        if(($fechai!='' && $fechaf!='') || $proveedor!='' || $producto!='' || $aprobado!='' || $ingreso!=''){
            $this->cezpdf->ezText('Filtros aplicados',10,$options);
            if($fechai!='' && $fechaf!='')
                $this->cezpdf->ezText('       - Fecha inicio: '.$fechai.'   Fecha fin: '.$fechaf,'',$options);
            if($proveedor!='')
                $this->cezpdf->ezText('       - Cliente:  '.$nomprovee,'',$options);
            if($producto!='')
                $this->cezpdf->ezText('       - Producto:    '.$nomprod,'',$options);
            if($aprobado!='')
                $this->cezpdf->ezText('       - Aprobacion:   '.$nomaprob,'',$options);
            if($ingreso!='')
                $this->cezpdf->ezText('       - Ingreso:         '.$nomingre,'',$options);
        }
        
        $this->cezpdf->ezText('','',$options);

		$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
		$serie='';
		foreach($confi as $key=>$value){
			if($value->DOCUP_Codigo == 15){
				$serie = $value->CONFIC_Serie;
			}
		}
        
        /*Listado*/
        
         foreach($listado as $indice=>$valor){
            $db_data[] = array(
                'col1'=>$indice+1,
                'col2' =>$valor->fecha,
				'col3'=>$serie,
                'col4'=>$valor->OCOMC_Numero,
                'col5'=>$valor->cotizacion,
                'col6'=>$valor->nombre,
                'col7'=>$valor->MONED_Simbolo.' '.number_format($valor->OCOMC_total,2),
                'col8'=>$valor->aprobado,
                'col9'=>$valor->ingreso
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
            'col8'  => 'C.INGRESO',
            'col9'  => 'APROBACION'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>555,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>7,
            'cols'=>array(
                'col1'=>array('width'=>25,'justification'=>'center'),
                'col2'=>array('width'=>50,'justification'=>'center'),
				'col3'=>array('width'=>30,'justification'=>'center'),
                'col4'=>array('width'=>30,'justification'=>'center'),
                'col5'=>array('width'=>55,'justification'=>'center'),
                'col6'=>array('width'=>200),
                'col7'=>array('width'=>50,'justification'=>'center'),
                'col8'=>array('width'=>50,'justification'=>'center'),
				'col9'=>array('width'=>60,'justification'=>'center')
                )
         ));
       


        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
	
	public function ver_reporte_pdf_ventas($anio){
        $usuario=$this->usuario_model->obtener($this->somevar['user']);
        $persona=$this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy=date('d/m/Y');        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $delta=20;
		
		$listado = $this->comprobante_model->buscar_comprobante_venta_2($anio);

		$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
		$serie='';
		foreach($confi as $key=>$value){
			if($value->DOCUP_Codigo == 15){
				$serie = $value->CONFIC_Serie;
			}
		}
        
        /*Listado*/
		$sum = 0;
		foreach($listado as $key=>$value){
			$sum+=$value->CPC_total;
			$db_data[] = array(
				'col1'=>$key+1,
				'col2' =>substr($value->CPC_FechaRegistro,0,10),
				'col3'=>$serie,
				'col4'=>$value->CPC_Numero,
				'col6'=>$value->CPC_subtotal,
				'col7'=>$value->CPC_igv,
				'col8'=>$value->CPC_total
				);
		}
         
         $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
			'col3' => 'SERIE',
            'col4' => 'NRO',
            'col6' => 'VALOR DE VENTA',
            'col7' => 'I.G.V. 18%',
            'col8'  => 'TOTAL',
         );
		 
		 $db_data[] = array(
				'col1'=>"",
				'col2' =>"",
				'col3'=>"",
				'col4'=>"",
				'col5'=>"",
				'col6'=>"",
				'col7'=>"TOTAL",
				'col8'=>$sum,
				'col9'=>""
				);
         
         $this->cezpdf->ezTable($db_data,$col_names,'', array(
            'width'=>555,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>7,
            'cols'=>array(
                'col1'=>array('width'=>25,'justification'=>'center'),
                'col2'=>array('width'=>50,'justification'=>'center'),
				'col3'=>array('width'=>50,'justification'=>'center'),
                'col4'=>array('width'=>30,'justification'=>'center'),
                'col6'=>array('width'=>50),
                'col7'=>array('width'=>50,'justification'=>'center'),
                'col8'=>array('width'=>50,'justification'=>'center'),
				'col9'=>array('width'=>60,'justification'=>'center')
                )
         ));
		 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function ver_reporte_pdf_commpras($anio){
        $usuario=$this->usuario_model->obtener($this->somevar['user']);
        $persona=$this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy=date('d/m/Y');        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $delta=20;
		
		$listado = $this->comprobante_model->buscar_comprobante_compras($anio);

		$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
		$serie='';
		foreach($confi as $key=>$value){
			if($value->DOCUP_Codigo == 15){
				$serie = $value->CONFIC_Serie;
			}
		}
        
        /*Listado*/
		$sum = 0;
		foreach($listado as $key=>$value){
			$sum+=$value->CPC_total;
			$db_data[] = array(
				'col1'=>$key+1,
				'col2' =>substr($value->CPC_FechaRegistro,0,10),
				'col3'=>$serie,
				'col4'=>$value->CPC_Numero,
				'col6'=>$value->CPC_subtotal,
				'col7'=>$value->CPC_igv,
				'col8'=>$value->CPC_total
				);
		}
         
         $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Fecha',
			'col3' => 'SERIE',
            'col4' => 'NRO',
            'col6' => 'VALOR DE VENTA',
            'col7' => 'I.G.V. 18%',
            'col8'  => 'TOTAL',
         );
		 
		 $db_data[] = array(
				'col1'=>"",
				'col2' =>"",
				'col3'=>"",
				'col4'=>"",
				'col5'=>"",
				'col6'=>"",
				'col7'=>"TOTAL",
				'col8'=>$sum,
				'col9'=>""
				);
         
         $this->cezpdf->ezTable($db_data,$col_names,'', array(
            'width'=>555,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>7,
            'cols'=>array(
                'col1'=>array('width'=>25,'justification'=>'center'),
                'col2'=>array('width'=>50,'justification'=>'center'),
				'col3'=>array('width'=>50,'justification'=>'center'),
                'col4'=>array('width'=>30,'justification'=>'center'),
                'col6'=>array('width'=>50),
                'col7'=>array('width'=>50,'justification'=>'center'),
                'col8'=>array('width'=>50,'justification'=>'center'),
				'col9'=>array('width'=>60,'justification'=>'center')
                )
         ));
		 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function estadisticas_compras_ventas($tipo,$anio){
        $usuario=$this->usuario_model->obtener($this->somevar['user']);
        $persona=$this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy=date('d/m/Y');        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $delta=20;
		$r = '';
		if($tipo == "C"){
			$r = ' COMPRAS';
		}else{
			$r = ' VENTAS';
		}
		$options = array("leading"=>15,"left"=>0);
        $this->cezpdf->ezText('Usuario:  '.$persona[0]->PERSC_Nombre.' '.$persona[0]->PERSC_ApellidoPaterno.' '.$persona[0]->PERSC_ApellidoMaterno.'       Fecha: '.$fechahoy,7,$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText('ESTADISTICAS DE'.$r.' ANUALES',17,$options);        
        $this->cezpdf->ezText('','',$options);
		
		//$listado = $this->comprobante_model->buscar_comprobante_compras();
		$listado = $this->comprobante_model->estadisticas_compras_ventas($tipo,$anio);

		$confi = $this->configuracion_model->obtener_configuracion($this->somevar['compania']);
		$serie='';
		foreach($confi as $key=>$value){
			if($value->DOCUP_Codigo == 15){
				$serie = $value->CONFIC_Serie;
			}
		}
        
        /*Listado*/
		$datos_generales = '';
		$en=$fe=$ma=$ab=$may=$ju=$jul=$ag=$se=$oc=$no=$di=0;
		$s_en=$s_fe=$s_ma=$s_ab=$s_may=$s_ju=$s_jul=$s_ag=$s_se=$s_oc=$s_no=$s_di=0;
		foreach($listado as $key=>$value){
			if($value->EMPRC_RazonSocial != ""){
				$datos_generales = $value->EMPRC_RazonSocial;
			}else{
				$datos_generales = $value->PERSC_Nombre;
			}
			
			if($value->mes == 1){
				$en = $value->monto;
				$s_en += $value->monto;
			}else if($value->mes == 2){
				$fe = $value->monto;
				$s_fe += $value->monto;
			}else if($value->mes == 3){
				$ma = $value->monto;
				$s_ma += $value->monto;
			}else if($value->mes == 4){
				$ab = $value->monto;
				$s_ab += $value->monto;
			}else if($value->mes == 5){
				$may = $value->monto;
				$s_may += $value->monto;
			}else if($value->mes == 6){
				$ju = $value->monto;
				$s_ju += $value->monto;
			}else if($value->mes == 7){
				$jul = $value->monto;
				$s_jul += $value->monto;
			}else if($value->mes == 8){
				$ag = $value->monto;
				$s_ag += $value->monto;
			}else if($value->mes == 9){
				$se = $value->monto;
				$s_se += $value->monto;
			}else if($value->mes == 10){
				$oc = $value->monto;
				$s_oc += $value->monto;
			}else if($value->mes == 11){
				$no = $value->monto;
				$s_no += $value->monto;
			}else if($value->mes == 12){
				$di = $value->monto;
				$s_di += $value->monto;
			}
			
			/*switch($value->mes){
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
			}*/
			
			$db_data[] = array(
				'col1'=>$datos_generales,
				'col2' =>$en,
				'col3'=>$fe,
				'col4'=>$ma,
				'col5'=>$ab,
				'col6'=>$may,
				'col7'=>$ju,
				'col8'=>$jul,
				'col9'=>$ag,
				'col10'=>$se,
				'col11'=>$oc,
				'col12'=>$no,
				'col13'=>$di
				);
			$en=$fe=$ma=$ab=$may=$ju=$jul=$ag=$se=$oc=$no=$di=0;
		}
		
		$db_data[] = array(
				'col1'=>"TOTAL",
				'col2' =>$s_en,
				'col3'=>$s_fe,
				'col4'=>$s_ma,
				'col5'=>$s_ab,
				'col6'=>$s_may,
				'col7'=>$s_ju,
				'col8'=>$s_jul,
				'col9'=>$s_ag,
				'col10'=>$s_se,
				'col11'=>$s_oc,
				'col12'=>$s_no,
				'col13'=>$s_di
				);
         
         $col_names = array(
            'col1' => 'CLIENTES',
            'col2' => 'ENERO',
			'col3' => 'FEBRERO',
            'col4' => 'MARZO',
            'col5' => 'ABRIL',
            'col6' => 'MAYO',
            'col7'  => 'JUNIO',
            'col8'  => 'JULIO',
            'col9'  => 'AGOSTO',
            'col10'  => 'SETIE.',
            'col11'  => 'OCTU.',
            'col12'  => 'NOVIE.',
            'col13'  => 'DICIE.',
         );
         
         $this->cezpdf->ezTable($db_data,$col_names,'', array(
            'width'=>555,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>7,
            'cols'=>array(
                'col1'=>array('width'=>80,'justification'=>'center'),
                'col2'=>array('width'=>40,'justification'=>'center'),
				'col3'=>array('width'=>40,'justification'=>'center'),
                'col4'=>array('width'=>40,'justification'=>'center'),
                'col5'=>array('width'=>40,'justification'=>'center'),
                'col6'=>array('width'=>40,'justification'=>'center'),
                'col7'=>array('width'=>40,'justification'=>'center'),
                'col8'=>array('width'=>40,'justification'=>'center'),
                'col9'=>array('width'=>40,'justification'=>'center'),
                'col10'=>array('width'=>40,'justification'=>'center'),
                'col11'=>array('width'=>40,'justification'=>'center'),
                'col12'=>array('width'=>40,'justification'=>'center'),
				'col13'=>array('width'=>40,'justification'=>'center')
                )
         ));
		 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	private function meses($anio){
		switch($anio){
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
	
	public function estadisticas_compras_ventas_mensual($tipo,$anio,$mes){
        $usuario=$this->usuario_model->obtener($this->somevar['user']);
        $persona=$this->persona_model->obtener_datosPersona($usuario->PERSP_Codigo);
        $fechahoy=date('d/m/Y');        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
 
        /*Cabecera*/
        $delta=20;
		
		//$listado = $this->comprobante_model->buscar_comprobante_compras();
		$listado = $this->comprobante_model->estadisticas_compras_ventas_mensual($tipo,$anio,$mes);
		$r = '';
		if($tipo == "C"){
			$r = ' COMPRAS';
		}else{
			$r = ' VENTAS';
		}
		$options = array("leading"=>15,"left"=>0);
        $this->cezpdf->ezText('Usuario:  '.$persona[0]->PERSC_Nombre.' '.$persona[0]->PERSC_ApellidoPaterno.' '.$persona[0]->PERSC_ApellidoMaterno.'       Fecha: '.$fechahoy,7,$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText("",'',$options);
        $this->cezpdf->ezText('ESTADISTICAS DE'.$r,17,$options);        
        $this->cezpdf->ezText('','',$options);
		
        /*Listado*/
		$datos_generales = '';
		$ruc_dni = '';
		foreach($listado as $key=>$value){
			if($value->EMPRC_RazonSocial != ""){
				$datos_generales = $value->EMPRC_RazonSocial;
			}else{
				$datos_generales = $value->PERSC_Nombre;
			}
			if($value->EMPRC_Ruc != ""){
				$ruc_dni = $value->EMPRC_Ruc;
			}else{
				$ruc_dni = $value->PERSC_NumeroDocIdentidad;
			}
			
			$db_data[] = array(
				'col1'=>$this->meses($value->mes),
				'col2' =>substr($value->CPC_FechaRegistro,0,10),
				'col3'=>$datos_generales,
				'col4'=>$ruc_dni,
				'col5'=>$value->CPC_subtotal,
				'col6'=>$value->CPC_igv,
				'col7'=>$value->monto
				);
		}
		$col_names = array(
            'col1' => 'MES',
            'col2' => 'FECHA',
			'col3' => 'NOMBRE / RAZON SOCIAL',
            'col4' => 'DNI / RUC',
            'col5' => 'VALOR DE VENTA',
            'col6' => 'IGV',
            'col7'  => 'TOTAL',
         );
         
         $this->cezpdf->ezTable($db_data,$col_names,'', array(
            'width'=>555,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>7,
            'cols'=>array(
                'col1'=>array('width'=>70,'justification'=>'center'),
                'col2'=>array('width'=>60,'justification'=>'center'),
				'col3'=>array('width'=>150,'justification'=>'center'),
                'col4'=>array('width'=>100,'justification'=>'center'),
                'col5'=>array('width'=>60,'justification'=>'center'),
                'col6'=>array('width'=>60,'justification'=>'center'),
                'col7'=>array('width'=>60,'justification'=>'center')
                )
         ));
		 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato5($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
		$forma_pago = $this->formapago_model->obtener($datos_comprobante[0]->FORPAP_Codigo);
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/cyg_fondo_factura.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>60));
        $this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));
        
		$this->cezpdf->ezText('','', array('leading'=>40));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>18, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($forma_pago[0]->FORPAC_Descripcion),6, array("leading"=>0, "left"=>465));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>48));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>13, "left"=>25));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>1, "left"=>250));
		/******/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
		
        $this->cezpdf->ezText('','', array("leading"=>25));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>335,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
		$this->cezpdf->addText(70,322,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(510,305,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(510,285,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(469,283,9,$igv100);
        $this->cezpdf->addText(510,263,9,$moneda_simbolo.' '.number_format(($total),2));
		 
        /*$this->cezpdf->addText(70,103,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(500,103,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(500,85,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(454,85,9,$igv100);
        $this->cezpdf->addText(500,67,9,$moneda_simbolo.' '.number_format(($total),2));*/
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato6($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/cyg_fondo_factura_2.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>55));
        $this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));
        
		$this->cezpdf->ezText('','', array('leading'=>40));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>18, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>48));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>13, "left"=>25));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>1, "left"=>250));
		/******/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
		
        $this->cezpdf->ezText('','', array("leading"=>25));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>55,'justification'=>'center'),
                'cols5'=>array('width'=>335,'justification'=>'left'),
                'cols6'=>array('width'=>60,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
		$this->cezpdf->addText(70,330,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(510,312,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(510,292,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(469,292,9,$igv100);
        $this->cezpdf->addText(510,272,9,$moneda_simbolo.' '.number_format(($total),2));
		 
        /*$this->cezpdf->addText(70,322,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(510,305,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(510,285,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(469,283,9,$igv100);
        $this->cezpdf->addText(510,263,9,$moneda_simbolo.' '.number_format(($total),2));*/
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato7($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/cyl_fondo_factura.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>55));
        $this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));
        
		$this->cezpdf->ezText('','', array('leading'=>80));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),10, array("leading"=>18, "left"=>0));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),10, array("leading"=>0, "left"=>450));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>35, "left"=>0));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),10, array("leading"=>0, "left"=>410));
		/******/
		/*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));*/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)."/"),9, array("leading"=>0, "left"=>490));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)."/"),9, array("leading"=>0, "left"=>505));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
		
        $this->cezpdf->ezText('','', array("leading"=>30));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>70,'justification'=>'center'),
                'cols5'=>array('width'=>360,'justification'=>'left'),
                'cols6'=>array('width'=>70,'justification'=>'right'),
                'cols7'=>array('width'=>70,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
        $this->cezpdf->addText(140,335,10,$moneda_simbolo.' '.number_format($subtotal,2));
		$this->cezpdf->addText(270,335,10,$igv100);
        $this->cezpdf->addText(310,335,10,$moneda_simbolo.' '.number_format($igv,2));        
        $this->cezpdf->addText(480,335,9,$moneda_simbolo.' '.number_format(($total),2));
		
		$this->cezpdf->addText(50,312,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato7_boleta($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/cyl_fondo_boleta.jpg')); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>45));
        $this->cezpdf->ezText($serie.'          '.$numero,14, array("leading"=>0,'left'=>270));

		$this->cezpdf->ezText('','', array('leading'=>58));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>30, "left"=>40));
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>310));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)),9, array("leading"=>0, "left"=>330));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>360));
		$this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>16, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>1, "left"=>330));
        //$this->cezpdf->ezText(utf8_decode_seguro(($telefono!='') ? $telefono : $movil),9, array("leading"=>0, "left"=>395));
        
        $this->cezpdf->ezText('','', array("leading"=>30));
             
        /*Listado de detalles*/
        $db_data=array();
        $item=0;
        foreach($detalle_comprobante as $indice=>$valor){
            $item++;
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2),
                'cols8'=>''
                );
         }
          
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>40,'justification'=>'center'),
                'cols5'=>array('width'=>250,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>50,'justification'=>'right'),
                'cols8'=>array('width'=>130,'justification'=>'right')
            )
         ));
                 
         /*Totales*/    
        $son='SON: '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
        $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
        //$this->cezpdf->addText($pos,128,9,$son);
        $this->cezpdf->addText(380,297,9,$moneda_simbolo.' '.number_format(($total),2));
 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_formato5($codigo){      
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        $forma_pago  = $this->formapago_model->obtener($datos_comprobante[0]->FORPAP_Codigo);
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
		
        $this->cezpdf = new backgroundPDF('a4');
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>80));
        /*$this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));*/
        
		$this->cezpdf->ezText('','', array('leading'=>28));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>18, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($forma_pago[0]->FORPAC_Descripcion),6, array("leading"=>0, "left"=>485));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>48));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>13, "left"=>25));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>1, "left"=>260));
		/******/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>385));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>425));
		
        $this->cezpdf->ezText('','', array("leading"=>25));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
				'cols8'=>'',
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
				'cols8'=>array('width'=>20,'justification'=>'left'),
                'cols2'=>array('width'=>40,'justification'=>'center'),
                'cols5'=>array('width'=>345,'justification'=>'left'),
                'cols6'=>array('width'=>80,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
		$this->cezpdf->addText(565,652,9,utf8_decode_seguro(substr($fecha,8,2)));
		
		$this->cezpdf->addText(50,322,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(530,305,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(530,285,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(479,283,9,$igv100);
        $this->cezpdf->addText(530,263,9,$moneda_simbolo.' '.number_format(($total),2));
		 
        /*$this->cezpdf->addText(70,103,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(500,103,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(500,85,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(454,85,9,$igv100);
        $this->cezpdf->addText(500,67,9,$moneda_simbolo.' '.number_format(($total),2));*/
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
	
	public function comprobante_ver_pdf_formato6($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
		$forma_pago  = $this->formapago_model->obtener($datos_comprobante[0]->FORPAP_Codigo);
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4'); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>80));
        /*$this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));*/
        
		$this->cezpdf->ezText('','', array('leading'=>30));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>18, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($forma_pago[0]->FORPAC_Descripcion),6, array("leading"=>0, "left"=>485));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>18, "left"=>48));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>13, "left"=>25));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),9, array("leading"=>1, "left"=>260));
		/******/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>385));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>425));
		
        $this->cezpdf->ezText('','', array("leading"=>25));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
				'cols8'=>'',
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
				'cols8'=>array('width'=>20,'justification'=>'left'),
                'cols2'=>array('width'=>40,'justification'=>'center'),
                'cols5'=>array('width'=>345,'justification'=>'left'),
                'cols6'=>array('width'=>80,'justification'=>'right'),
                'cols7'=>array('width'=>85,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
		$this->cezpdf->addText(565,652,9,(utf8_decode_seguro(substr($fecha,8,2))));
		
		$this->cezpdf->addText(50,322,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(530,305,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(530,285,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(480,283,9,$igv100);
        $this->cezpdf->addText(530,263,9,$moneda_simbolo.' '.number_format(($total),2));
		 
        /*$this->cezpdf->addText(70,322,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(510,305,9,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(510,285,9,$moneda_simbolo.' '.number_format($igv,2));
        $this->cezpdf->addText(469,283,9,$igv100);
        $this->cezpdf->addText(510,263,9,$moneda_simbolo.' '.number_format(($total),2));*/
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_formato7($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new backgroundPDF('a4'); 
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>55));
        $this->cezpdf->ezText($serie,18, array("leading"=>20,'left'=>345));
        $this->cezpdf->ezText($numero,18, array("leading"=>0,'left'=>440));
        
		$this->cezpdf->ezText('','', array('leading'=>80));
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),10, array("leading"=>18, "left"=>0));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),10, array("leading"=>0, "left"=>450));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>35, "left"=>0));
        $this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),10, array("leading"=>0, "left"=>410));
		/******/
		/*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));*/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)."/"),9, array("leading"=>0, "left"=>490));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)."/"),9, array("leading"=>0, "left"=>505));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
		
        $this->cezpdf->ezText('','', array("leading"=>30));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>70,'justification'=>'center'),
                'cols5'=>array('width'=>360,'justification'=>'left'),
                'cols6'=>array('width'=>70,'justification'=>'right'),
                'cols7'=>array('width'=>70,'justification'=>'right'),
            )
         ));
                 
        /*Totales*/    
		
        $this->cezpdf->addText(140,335,10,$moneda_simbolo.' '.number_format($subtotal,2));
		$this->cezpdf->addText(270,335,10,$igv100);
        $this->cezpdf->addText(310,335,10,$moneda_simbolo.' '.number_format($igv,2));        
        $this->cezpdf->addText(480,335,9,$moneda_simbolo.' '.number_format(($total),2));
		
		$this->cezpdf->addText(50,312,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_formato7_boleta($codigo){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_NumeroDocIdentidad;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4');
 
        /*Cabecera*/
        $this->cezpdf->ezText('','', array('leading'=>45));
        //$this->cezpdf->ezText($serie.'          '.$numero,14, array("leading"=>0,'left'=>270));

		$this->cezpdf->ezText('','', array('leading'=>58));
        
        /*Datos del cliente*/ 
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>30, "left"=>40));
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>310));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)),9, array("leading"=>0, "left"=>330));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>360));
		$this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>16, "left"=>50));
        $this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>1, "left"=>330));
        //$this->cezpdf->ezText(utf8_decode_seguro(($telefono!='') ? $telefono : $movil),9, array("leading"=>0, "left"=>395));
        
        $this->cezpdf->ezText('','', array("leading"=>30));
             
        /*Listado de detalles*/
        $db_data=array();
        $item=0;
        foreach($detalle_comprobante as $indice=>$valor){
            $item++;
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>$valor->CPDEC_Cantidad,
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45)),
                'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2),
                'cols8'=>''
                );
         }
          
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>10,
            'cols'=>array(
                'cols2'=>array('width'=>40,'justification'=>'center'),
                'cols5'=>array('width'=>250,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>50,'justification'=>'right'),
                'cols8'=>array('width'=>130,'justification'=>'right')
            )
         ));
                 
         /*Totales*/    
        $son='SON: '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
        $pos=0;
        if(strlen($son)>0 && strlen($son)<40)
            $pos=200;
        elseif(strlen($son)>40 && strlen($son)<80)
            $pos=150;
        elseif(strlen($son)>80 && strlen($son)<120)
            $pos=100;
        else
            $pos=50;
         
        //$this->cezpdf->addText($pos,128,9,$son);
        $this->cezpdf->addText(380,297,9,$moneda_simbolo.' '.number_format(($total),2));
 
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato8($codigo){
        $datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        // $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
        $vendedor        = substr($temp[0]->PERSC_Nombre,0,1).' '.$temp[0]->PERSC_ApellidoPaterno;    
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
			$direccion        = 'NO PRESENTA DIRECCION';
            if(count($emp_direccion)>0){
				$direccion        = $emp_direccion[0]->EESTAC_Direccion;
			}
			
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        
        $this->load->library('cezpdf');
        $this->load->helper('pdf_helper');
        prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/impacto_factura.jpg')); 
			//<formade pago>
		$codigo_forma_pago 	 = $datos_comprobante[0]->FORPAP_Codigo;
		$cond_pago = 'NO DEFINIDO';
		if(strlen(trim($codigo_forma_pago))>0){
			$forma_pago = $this->formapago_model->obtener($codigo_forma_pago);
			if(count($forma_pago)>0){
				$cond_pago = $forma_pago[0]->FORPAC_Descripcion;
			}
		}
	//</formade pago>
        /*Cabecera $serie $numero*/
        $this->cezpdf->ezText('','', array('leading'=>80));//55
        $this->cezpdf->ezText('',18, array("leading"=>10,'left'=>400));//22 - 345
        $this->cezpdf->ezText('',18, array("leading"=>0,'left'=>450));//0 -440
        $this->cezpdf->ezText(utf8_decode_seguro($vendedor),9, array("leading"=>61, "left"=>330));
		$this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),10, array("leading"=>12, "left"=>470));
		$this->cezpdf->ezText('','', array('leading'=>5));//80
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));*/
        // $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));
         $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),9, array("leading"=>-40, "left"=>10));
         $this->cezpdf->ezText(utf8_decode_seguro($cond_pago),9, array("leading"=>0, "left"=>330));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),9, array("leading"=>20, "left"=>10));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>13, "left"=>10));
        
		/******/
		/*$this->cezpdf->ezText(zutf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));*/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)."/"),9, array("leading"=>-30, "left"=>440));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)."/"),9, array("leading"=>0, "left"=>460));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,4)),9, array("leading"=>0, "left"=>480));
		
        $this->cezpdf->ezText('','', array("leading"=>65));
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
				$db_data[] = array(
				'cols2'=>'',
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45),true),
                'cols3'=>$valor->CPDEC_Cantidad,
				'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>565,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols2'=>array('width'=>50,'justification'=>'center'),
                'cols5'=>array('width'=>400,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>60,'justification'=>'right'),
            )
         ));
        //para el tipo de cambio 
		   //<tipo cambi0>
				$fecha_formato = $datos_comprobante[0]->CPC_Fecha;
				$lista = $this->obtener_tipo_de_cambio($fecha_formato);
				if(count($lista)>0){
					$valido_fecha = explode('-',$lista[0]->TIPCAMC_Fecha);
					$anio_v = $valido_fecha[0];
					$mes_v = $valido_fecha[1];
					$dia_v = $valido_fecha[2];
					$valido_fecha = $dia_v.' /'.$mes_v.' /'.$anio_v;
					$factor_de_conversion = $lista[0]->TIPCAMC_FactorConversion;
				}else{
					$valido_fecha = 'NO PRESENTA';
					$factor_de_conversion = 'NO EXISTE';
				}
	//</tipo cambi0> 
		// $fecha_formato = $datos_comprobante[0]->CPC_Fecha;
		// $lista = $this->obtener_tipo_de_cambio($fecha_formato);
		// $valido_fecha = $lista[0]->TIPCAMC_Fecha;
		// $factor_de_conversion = $lista[0]->TIPCAMC_FactorConversion;
        /*Totales*/  
		$this->cezpdf->ezText('','', array("leading"=>437));
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>410));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>435));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
		
        $this->cezpdf->addText(230,150,10,$moneda_simbolo.' '.number_format($subtotal,2));
        $this->cezpdf->addText(410,150,10,$moneda_simbolo.' '.number_format($igv,2));
		$this->cezpdf->addText(390,150,10,$igv100);        
        $this->cezpdf->addText(480,150,9,$moneda_simbolo.' '.number_format(($total),2));
		
		$this->cezpdf->addText(60,195,7,strtoupper('TIPO DE CAMBIO '.$factor_de_conversion));
		$this->cezpdf->addText(180,195,7,utf8_decode_seguro(strtoupper('VÁLIDO SOLO '.$valido_fecha)));
		$this->cezpdf->addText(60,180,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function comprobante_ver_pdf_conmenbrete_formato8_boleta($codigo){
		
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
        $presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
        $serie           = $datos_comprobante[0]->CPC_Serie;
        $numero          = $datos_comprobante[0]->CPC_Numero;
        $cliente         = $datos_comprobante[0]->CLIP_Codigo;
        $subtotal        = $datos_comprobante[0]->CPC_subtotal;
        $descuento       = $datos_comprobante[0]->CPC_descuento;
        $igv             = $datos_comprobante[0]->CPC_igv;
        $igv100          = $datos_comprobante[0]->CPC_igv100;
        $descuento100    = $datos_comprobante[0]->CPC_descuento100;
        $total           = $datos_comprobante[0]->CPC_total;
        $observacion     = $datos_comprobante[0]->CPC_Observacion;
        $fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $vendedor        = $datos_comprobante[0]->USUA_Codigo ;
        $datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        $tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
        $guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
        $docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
        
        $datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        // $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
        $vendedor        = substr($temp[0]->PERSC_Nombre,0,1).' '.$temp[0]->PERSC_ApellidoPaterno;
            
        if($tipo==0)
        {   $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $movil            = $datos_persona[0]->PERSC_Movil;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = $datos_persona[0]->PERSC_Fax;
        }
        elseif($tipo==1)
        {   $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            if(count($emp_direccion)>0){
				$direccion        = $emp_direccion[0]->EESTAC_Direccion;
			}else{
				$direccion = 'NO DENIFIDO';
			}
			
        }
        $detalle_comprobante        = $this->obtener_lista_detalles($codigo);
        $this->load->library('cezpdf');
        $this->load->helper('pdf_helper');
        prep_pdf();
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/impacto_boleta.jpg')); 
 
        /*Cabecera $serie $numero*/
        $this->cezpdf->ezText('','', array('leading'=>80));//55
        $this->cezpdf->ezText('',18, array("leading"=>0,'left'=>390));//22 - 345
        $this->cezpdf->ezText('',18, array("leading"=>0,'left'=>440));//0 -440
        $this->cezpdf->ezText(utf8_decode_seguro($vendedor),9, array("leading"=>61, "left"=>330));
		$this->cezpdf->ezText('','', array('leading'=>19));//80
		
        /*Datos del cliente*/ 
        /*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>37, "left"=>30));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>70));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>188));*/
        $this->cezpdf->ezText(utf8_decode_seguro($nombre_cliente),10, array("leading"=>-30, "left"=>10));
		$this->cezpdf->ezText(utf8_decode_seguro($ruc),10, array("leading"=>12, "left"=>10));
        $this->cezpdf->ezText(utf8_decode_seguro($direccion),9, array("leading"=>12, "left"=>10));
		$this->cezpdf->ezText(utf8_decode_seguro($guiarem_codigo),10, array("leading"=>0, "left"=>470));
        $this->cezpdf->ezText('','', array('leading'=>25));//80

		/******/
		/*$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>375));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>415));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));*/
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)."/"),9, array("leading"=>-55, "left"=>440));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,3,2)."/"),9, array("leading"=>0, "left"=>460));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,4)),9, array("leading"=>0, "left"=>480));
		
        $this->cezpdf->ezText('','', array("leading"=>50));
             
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_comprobante as $indice=>$valor){
            if($valor->CPDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->CPDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
            $db_data[] = array(
                'cols2'=>'',
                'cols5'=>utf8_decode_seguro(substr($valor->PROD_Nombre,0,45),true),
                'cols3'=>$valor->CPDEC_Cantidad,
				'cols6'=>number_format($pu_conigv,2),
                'cols7'=>number_format($valor->CPDEC_Total,2)
                );
         }
         /*$col_names = array(
            'cols1' => '<b>IT.</b>',
            'cols2' => '<b>CANT.</b>',
            'cols3' => '<b>UNID.</b>',
            'cols4' => '<b>CODIGO</b>',
            'cols5' => '<b>DETALLE</b>',
            'cols6'  => '<b>P.U.</b>',
            'cols7'  => '<b>VALOR TOTAL</b>'
         );*/
         
         $this->cezpdf->ezTable($db_data,'', '', array(
            'width'=>570,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols2'=>array('width'=>50,'justification'=>'center'),
                'cols5'=>array('width'=>400,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
                'cols7'=>array('width'=>60,'justification'=>'right'),
            )
         ));
         //para el tipo de cambio 
		 $fecha_formato = $datos_comprobante[0]->CPC_Fecha;
   
		$lista = $this->obtener_tipo_de_cambio($fecha_formato);
		$valido_fecha = $lista[0]->TIPCAMC_Fecha;
		$factor_de_conversion = $lista[0]->TIPCAMC_FactorConversion;  
        /*Totales*/    
		$this->cezpdf->ezText('','', array("leading"=>505));
		$this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha,0,2)),9, array("leading"=>0, "left"=>410));
        $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha,3,2))),9, array("leading"=>0, "left"=>435));
        $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha,8,2)),9, array("leading"=>0, "left"=>520));
        // $this->cezpdf->addText(70,443,10,$moneda_simbolo.' '.number_format($subtotal,2));
        // $this->cezpdf->addText(310,443,10,$moneda_simbolo.' '.number_format($igv,2));
		// $this->cezpdf->addText(390,443,10,$igv100);        
        $this->cezpdf->addText(480,170,9,$moneda_simbolo.' '.number_format(($total),2));
		
		$this->cezpdf->addText(60,170,9,strtoupper(num2letras(round($total,2))).' '.$moneda_nombre);
        $this->cezpdf->addText(60,180,7,strtoupper('TIPO DE CAMBIO '.$factor_de_conversion));
		$this->cezpdf->addText(180,180,7,utf8_decode_seguro(strtoupper('VÁLIDO SOLO '.$valido_fecha)));
        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
	}
	
	public function formatos_de_impresion_F($codigo, $tipo_docu){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
		$presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
		$serie           = $datos_comprobante[0]->CPC_Serie;
		$numero          = $datos_comprobante[0]->CPC_Numero;
		$cliente         = $datos_comprobante[0]->CLIP_Codigo;
		$subtotal        = $datos_comprobante[0]->CPC_subtotal;
		$descuento       = $datos_comprobante[0]->CPC_descuento;
		$igv             = $datos_comprobante[0]->CPC_igv;
		$igv100          = $datos_comprobante[0]->CPC_igv100;
		$descuento100    = $datos_comprobante[0]->CPC_descuento100;
		$total           = $datos_comprobante[0]->CPC_total;
		$observacion     = $datos_comprobante[0]->CPC_Observacion;
		$usuario 		 = $datos_comprobante[0]->USUA_Codigo;
		$fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
		$fecha_formato = $datos_comprobante[0]->CPC_Fecha;
		$dia = substr($fecha,0,2);
		$mes = substr($fecha,3,2);
		$anio = substr($fecha,6,4);
		$mess = $this->meses($mes);
		$fecha_pie = $dia.'/ '.$mes.'/ '.$anio;
		$vendedor        = $datos_comprobante[0]->USUA_Codigo ;
		$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
		$empresa         = $datos_cliente[0]->EMPRP_Codigo;
		$persona         = $datos_cliente[0]->PERSP_Codigo;
		$tipo            = $datos_cliente[0]->CLIC_TipoPersona;
		$tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
		$guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
		$docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
		//<formade pago>
		$codigo_forma_pago 	 = $datos_comprobante[0]->FORPAP_Codigo;
		$cond_pago = 'NO DEFINIDO';
		if(strlen(trim($codigo_forma_pago))>0){
			$forma_pago = $this->formapago_model->obtener($codigo_forma_pago);
			if(count($forma_pago)>0){
				$cond_pago = $forma_pago[0]->FORPAC_Descripcion;
			}
		}
		//</formade pago>
		$datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
		$moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
		$moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
		$temp            = $this->usuario_model->obtener($vendedor);
		$temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
		//$vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
		$vendedor        = substr($temp[0]->PERSC_Nombre,0,1).'. '.$temp[0]->PERSC_ApellidoPaterno;
		if($tipo==0){
		  $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
		  $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
		  $ruc              = $datos_persona[0]->PERSC_Ruc;
		  $telefono         = $datos_persona[0]->PERSC_Telefono;
		  $movil            = $datos_persona[0]->PERSC_Movil;
		  $direccion        = $datos_persona[0]->PERSC_Direccion;
		  $fax              = $datos_persona[0]->PERSC_Fax;
		}
		elseif($tipo==1){
		  $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
		  $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
		  $ruc              = $datos_empresa[0]->EMPRC_Ruc;
		  $telefono         = $datos_empresa[0]->EMPRC_Telefono;
		  $movil            = $datos_empresa[0]->EMPRC_Movil;
		  $fax              = $datos_empresa[0]->EMPRC_Fax;
		  $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
			if($emp_direccion){
		  $direccion        = $emp_direccion[0]->EESTAC_Direccion;}
		  else $direccion="";
		} 
		$data['seniores'] = utf8_decode_seguro($nombre_cliente);
		if(isset($direccion)){
		$data['direccion'] = utf8_decode_seguro($direccion);
		}else{ $data['direccion'] = '';}
		$data['ruc'] = utf8_decode_seguro($ruc);
		$data['vendedor'] = $vendedor; 
		$data['numero_guia_remision'] = utf8_decode_seguro($guiarem_codigo);
		$data['fecha'] = utf8_decode_seguro($fecha);    
	   //<tipo de cambio>
		$data['serie'] = $serie;
		$data['numero'] = $numero;
		$data['elmes']=$mes;
		$data['dia'] = $dia;
		$data['mes'] = $mess;
		$data['fecha_pie'] = $fecha_pie;
		$data['anio'] = $anio;
		$data['documento_referencia'] = utf8_decode_seguro($docurefe_codigo);
		$data['serie_numero'] = $serie.'-&nbsp;&nbsp;'.$numero;
		$detalle_comprobante        = $this->obtener_lista_detalles($codigo);
		/*Listado de detalles*/
		$db_data=array();
		foreach($detalle_comprobante as $indice=>$valor){
		  if($valor->CPDEC_Pu_ConIgv!='')
			  $pu_conigv=$valor->CPDEC_Pu_ConIgv;
		  else
			  $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
		  $db_data[] = array(
			  'item_numero'=>$indice+1,
			  'item_cantidad'=>$valor->CPDEC_Cantidad,
			  'item_codProduct'=>$valor->PROD_CodigoInterno,
			  'item_unidad'=>$valor->UNDMED_Simbolo,
			  'item_codigo'=>$valor->PROD_CodigoUsuario,
			  'item_descripcion'=>utf8_decode_seguro($valor->PROD_Nombre,true),
			  'item_precio_unitario'=>number_format($pu_conigv,2),
			  'item_importe'=>number_format($valor->CPDEC_Total,2)
		  );
		}
		$fecha_formato = $datos_comprobante[0]->CPC_Fecha;
		$lista = $this->obtener_tipo_de_cambio($fecha_formato);
		if(count($lista)>0){
			$valido_fecha = explode('-',$lista[0]->TIPCAMC_Fecha);
			$anio_v = $valido_fecha[0];
			$mes_v = $valido_fecha[1];
			$dia_v = $valido_fecha[2];
			$valido_fecha = $dia_v.' /'.$mes_v.' /'.$anio_v;
			$data['valido_fecha'] = $valido_fecha;
			$data['factor_de_conversion'] = $lista[0]->TIPCAMC_FactorConversion;
		}else{
			$data['valido_fecha'] = 'NO PRESENTA';
			$data['factor_de_conversion'] = 'NO EXISTE';
			}
		$data['lista_items'] = $db_data;
		$data['cond_pago'] = $cond_pago;
		$son = strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
		$data['igv100']=$igv100;
		$data['total_texto'] = $son;
		$data['total_bruto'] = $moneda_simbolo.' '.number_format($total,2);
		$data['igv'] = $moneda_simbolo.' '.number_format($igv,2);
		$data['subtotal'] = $moneda_simbolo.' '.number_format(($total-$igv),2);
		$data['total'] = $moneda_simbolo.' '.number_format($total,2);
		$data['descuento'] = $moneda_simbolo.' '.number_format($descuento,2);
		$this->load->view('ventas/comprobante_ver_html',$data);
	}
	
	public function formatos_de_impresion_B($codigo, $tipo_docu){
		$datos_comprobante   = $this->comprobante_model->obtener_comprobante($codigo);
		$presupuesto     = $datos_comprobante[0]->PRESUP_Codigo;
		$serie           = $datos_comprobante[0]->CPC_Serie;
		$numero          = $datos_comprobante[0]->CPC_Numero;
		$cliente         = $datos_comprobante[0]->CLIP_Codigo;
		$subtotal        = $datos_comprobante[0]->CPC_subtotal;
		$descuento       = $datos_comprobante[0]->CPC_descuento;
		$igv             = $datos_comprobante[0]->CPC_igv;
		$igv100          = $datos_comprobante[0]->CPC_igv100;
		$descuento100    = $datos_comprobante[0]->CPC_descuento100;
		$total           = $datos_comprobante[0]->CPC_total;
		$observacion     = $datos_comprobante[0]->CPC_Observacion;
		$usuario 		 = $datos_comprobante[0]->USUA_Codigo;
		$fecha           = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
		$dia = substr($fecha,0,2);
		$mes = substr($fecha,3,2);
		$anio = substr($fecha,6,4);
		$data['mes_numero']=$mes;
		$mess = $this->meses($mes);
		$fecha_pie = $dia.'/ '.$mes.'/ '.$anio;
		$vendedor        = $datos_comprobante[0]->USUA_Codigo ;
		$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
		$empresa         = $datos_cliente[0]->EMPRP_Codigo;
		$persona         = $datos_cliente[0]->PERSP_Codigo;
		$tipo            = $datos_cliente[0]->CLIC_TipoPersona;
		$tipo_docu       = $datos_comprobante[0]->CPC_TipoDocumento;
		$guiarem_codigo  = $datos_comprobante[0]->CPC_GuiaRemCodigo;
		$docurefe_codigo  = $datos_comprobante[0]->CPC_DocuRefeCodigo;
		$datos_moneda    = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
		$moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
		$moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
		$temp            = $this->usuario_model->obtener($vendedor);
		$temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
		//$vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
		$vendedor        = substr($temp[0]->PERSC_Nombre,0,1).'. '.$temp[0]->PERSC_ApellidoPaterno;
		if($tipo==0){
		  $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
		  $nombre_cliente   = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
		  $ruc              = $datos_persona[0]->PERSC_Ruc;
		  $telefono         = $datos_persona[0]->PERSC_Telefono;
		  $movil            = $datos_persona[0]->PERSC_Movil;
		  $direccion        = $datos_persona[0]->PERSC_Direccion;
		  $fax              = $datos_persona[0]->PERSC_Fax;
		}
		elseif($tipo==1){
		  $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
		  $nombre_cliente   = $datos_empresa[0]->EMPRC_RazonSocial;
		  $ruc              = $datos_empresa[0]->EMPRC_Ruc;
		  $telefono         = $datos_empresa[0]->EMPRC_Telefono;
		  $movil            = $datos_empresa[0]->EMPRC_Movil;
		  $fax              = $datos_empresa[0]->EMPRC_Fax;
		  $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
		  if($emp_direccion)
		  $direccion        = $emp_direccion[0]->EESTAC_Direccion;
		  else{
		  $direccion="DESCONOCIDO";
		  }
		} 
		//<tipo de cambio>
		$fecha_formato = $datos_comprobante[0]->CPC_Fecha;
		$lista = $this->obtener_tipo_de_cambio($fecha_formato);
		if(count($lista)>0){
			$valido_fecha = explode('-',$lista[0]->TIPCAMC_Fecha);
			$anio_v = $valido_fecha[0];
			$mes_v = $valido_fecha[1];
			$dia_v = $valido_fecha[2];
			$valido_fecha = $dia_v.' /'.$mes_v.' /'.$anio_v;
			$data['valido_fecha'] = $valido_fecha;
			$data['factor_de_conversion'] = $lista[0]->TIPCAMC_FactorConversion;
		}else{
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
		$data['descuento']=$descuento;
		$data['serie_numero'] = $serie.'-&nbsp;&nbsp;'.$numero;
		$data['anio'] = $anio;
		$data['documento_referencia'] = utf8_decode_seguro($docurefe_codigo);
		$data['fecha_pie'] = $fecha_pie;
		$detalle_comprobante        = $this->obtener_lista_detalles($codigo);
		/*Listado de detalles*/
		$db_data=array();
		foreach($detalle_comprobante as $indice=>$valor){
		  if($valor->CPDEC_Pu_ConIgv!='')
			  $pu_conigv=$valor->CPDEC_Pu_ConIgv;
		  else
			  $pu_conigv=$valor->CPDEC_Pu+$valor->CPDEC_Pu*$valor->CPDEC_Igv100/100;
		  $db_data[] = array(
			  'item_numero'=>$indice+1,
			  'item_cantidad'=>$valor->CPDEC_Cantidad,
			  'item_unidad'=>$valor->UNDMED_Simbolo,
			  'item_codigo'=>$valor->PROD_CodigoUsuario,
			  'item_descripcion'=>utf8_decode_seguro($valor->PROD_Nombre,true),
			  'item_precio_unitario'=>number_format($pu_conigv,2),
			  'item_importe'=>number_format($valor->CPDEC_Total,2)
		  );
		}
		$data['lista_items'] = $db_data;
		$data['lista_items'] = $db_data;
		$son = 'SON : '.strtoupper(num2letras(round($total,2))).' '.$moneda_nombre;
		$data['total_texto'] = $son;
		$data['total_bruto'] = $moneda_simbolo.' '.number_format($total,2);
		$data['igv'] = $moneda_simbolo.' '.number_format($igv,2);
		$data['subtotal'] = $moneda_simbolo.' '.number_format(($total-$igv),2);
		$data['total'] = $moneda_simbolo.' '.number_format($total,2);
		$data['descuento'] = $moneda_simbolo.' '.number_format($descuento,2);
		$this->load->view('ventas/boleta_ver_html',$data);
	}
	
	public function obtener_tipo_de_cambio($fecha_comprobante){
		return  $this->tipocambio_model->obtener_x_fecha($fecha_comprobante);	
	}
	
}
?>