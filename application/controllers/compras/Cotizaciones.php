<?php
ini_set('error_reporting', 1); 
include("system/application/libraries/cezpdf.php"); 
include("system/application/libraries/class.backgroundpdf.php"); 
class Cotizaciones extends CI_Controller
{
    public function __construct()
    {
        parent::Controller();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->model('maestros/marca_model');
        $this->load->model('compras/cotizacion_model');
        $this->load->model('compras/pedido_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('compras/cotizaciones_model');
        $this->load->model('compras/presupuestodetalle_model');
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
        $this->load->model('maestros/condicionentrega_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/area_model');
        $this->load->model('configuracion_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
        
    }
    public function index(){
        $this->load->view('seguridad/inicio');
        $this->load->library('layout','layout');
    }
    public function presupuestos($j='0', $limpia='')
    {   
		$data['compania'] =$this->somevar['compania'];
		$this->load->library('layout','layout');
        $data_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
           
        if($limpia=='1'){
            $this->session->unset_userdata('fechai');
            $this->session->unset_userdata('fechaf');
            $this->session->unset_userdata('serie');
            $this->session->unset_userdata('numero');
            $this->session->unset_userdata('codigo_usuario');
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
            $filter->codigo_usuario= $this->input->post('codigo_usuario');
            $filter->proveedor       = $this->input->post('proveedor');
            $filter->ruc_proveedor   = $this->input->post('ruc_proveedor');
            $filter->nombre_proveedor = $this->input->post('nombre_proveedor');
            $filter->producto      = $this->input->post('producto');
            $filter->codproducto      = $this->input->post('codproducto');
            $filter->nombre_producto  = $this->input->post('nombre_producto');
            $this->session->set_userdata(array('fechai'=>$filter->fechai, 'fechaf'=>$filter->fechaf, 'serie'=>$filter->serie, 'numero'=>$filter->numero, 'codigo_usuario'=>$filter->codigo_usuario, 'proveedor'=>$filter->proveedor, 'ruc_proveedor'=>$filter->ruc_proveedor, 'nombre_proveedor'=>$filter->nombre_proveedor, 'producto'=>$filter->producto, 'codproducto'=>$filter->codproducto, 'nombre_producto'=>$filter->nombre_producto));
        }else{
            $filter->fechai         = $this->session->userdata('fechai');
            $filter->fechaf         = $this->session->userdata('fechaf');
            $filter->serie          = $this->session->userdata('serie');
            $filter->numero         = $this->session->userdata('numero');
            $filter->codigo_usuario = $this->session->userdata('codigo_usuario');
            $filter->proveedor        = $this->session->userdata('proveedor');
            $filter->ruc_proveedor    = $this->session->userdata('ruc_proveedor');
            $filter->nombre_proveedor = $this->session->userdata('nombre_proveedor');
            $filter->producto       = $this->session->userdata('producto');
            $filter->codproducto    = $this->session->userdata('codproducto');
            $filter->nombre_producto= $this->session->userdata('nombre_producto');
        }
		
        $filter->CPC_TipoOperacion = 'S';
        
        $data['fechai']            = $filter->fechai;
        $data['fechaf']            = $filter->fechaf;
        $data['serie']             = $filter->serie;
        $data['numero']            = $filter->numero;
        $data['codigo_usuario']    = $filter->codigo_usuario;
        $data['proveedor']           = $filter->proveedor;
        $data['ruc_proveedor']       = $filter->ruc_proveedor;
        $data['nombre_proveedor']    = $filter->nombre_proveedor;
        $data['producto']          = $filter->producto;
        $data['codproducto']       = $filter->codproducto;
        $data['nombre_producto']   = $filter->nombre_producto;
    
        $data['registros']   = count($this->cotizaciones_model->buscar_presupuestos($filter));
        $conf['base_url']    = site_url('compras/cotizaciones/presupuestos');
        $conf['per_page']    = 12;
        $conf['num_links']   = 3;
        $conf['first_link']  = "&lt;&lt;";
        $conf['last_link']   = "&gt;&gt;";
        $conf['total_rows']  = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset              = (int)$this->uri->segment(4);
        
        $listado_presupuestos    = $this->cotizaciones_model->buscar_presupuestos($filter, $conf['per_page'],$offset);
        $item                = $j+1;
        $lista               = array();
        if(count($listado_presupuestos)>0){
            foreach($listado_presupuestos as $indice=>$valor)
            {
                $codigo          = $valor->PRESUP_Codigo;
                $fecha           = mysql_to_human($valor->PRESUC_Fecha);
                $serie           = $valor->PRESUC_Serie;
                $numero          = $valor->PRESUC_Numero;
                $codigo_usuario  = $valor->PRESUC_CodigoUsuario;
                $nombre_proveedor  = $valor->nombre;
                $nom_tipodocu    = $valor->nom_tipodocu;
                $total           = $valor->MONED_Simbolo.' '.number_format($valor->PRESUC_total,2);
                $estado          = $valor->PRESUC_FlagEstado;

                $img_estado     =($estado=='1' ? "<img src='".base_url()."images/active.png' alt='Activo' title='Activo' />" : "<img src='".base_url()."images/inactive.png' alt='Anulado' title='Anulado' />") ;
                $editar         = "<a href='javascript:;' onclick='editar_presupuesto(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='javascript:;' onclick='ver_presupuesto_ver_pdf(".$codigo.")' target='_parent'><img src='".base_url()."images/imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $ver2            = "<a href='javascript:;' onclick='ver_presupuesto_ver_pdf_conmenbrete(".$codigo.")' target='_parent'><img src='".base_url()."images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $ver3            = "<a href='javascript:;' onclick='ver_presupuesto_ver_xls(".$codigo.")' target='_self'><img src='".base_url()."images/xls.png' width='16' height='16' border='0' title='Ver PDF'></a>";
                $eliminar       = "<a href='javascript:;' onclick='eliminar_presupuesto(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[]        = array($item++,$fecha, $serie, $numero,$codigo_usuario, $nombre_proveedor, $nom_tipodocu, $total,$img_estado,$editar,$ver,$ver2,$ver3);
            }
        }
        $data['titulo_tabla']    = "RELACIÓN DE SOLICITUDES DE COTIZACION";
        $data['titulo_busqueda'] = "BUSCAR SOLICITUD DE COTIZACION";
        $data['lista']      = $lista;
        $data['oculto']     = form_hidden(array('base_url'=>base_url()));
        $data['tipo_codificacion']  = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('compras/cotizaciones_index',$data);
    }
    public function presupuesto_nueva($tipo_docu)
    {   
		$compania = $this->somevar['compania'];
		$data['compania'] = $compania;
		$this->load->library('layout','layout');
        $data_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania        = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa           = $data_compania[0]->EMPRP_Codigo;
    
        $codigo               = "";
        $data['contiene_igv'] = (($data_confi[0]->COMPCONFIC_PrecioContieneIgv=='1')?true:false);
        $oculto               = form_hidden(array('codigo'=>$codigo,'base_url'=>base_url(), 'tipo_docu'=>$tipo_docu, 'contiene_igv'=>($data['contiene_igv']==true?'1':'0'), "tipo_codificacion"=>$data_confi_docu[0]->COMPCONFIDOCP_Tipo));
        $data['url_action']   = base_url()."index.php/compras/cotizaciones/presupuesto_insertar";
        $data['titulo']       = "REGISTRAR SOLICITUD DE COTIZACION";
        $data['formulario']   = "frmCotizacion";
        $data['oculto']       = $oculto;
        $data['onload']	      = "onload=\"$('#nombre').focus();\"";
        $data['cboMoneda']    = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '12'); //12: Al contado
        $data['cboContacto']      = form_dropdown("contacto",array(''=>':: Seleccione ::'),""," class='comboGrande' style='width:307px;' id='contacto'");
        $data['cboVendedor']  = form_dropdown("vendedor",$this->emprcontacto_model->seleccionar($my_empresa),""," class='comboGrande' style='width:200px;' id='vendedor'");
        $data['cboPedidos']      = form_dropdown("pedidos",$this->pedido_model->seleccionar(),""," onchange='obtener_detalle_pedido();' class='comboGrande' style='width:200px;' id='pedidos'");
        $data['lugar_entrega']    = "";
        $data['serie']  	  = "";
		
		$numero_predt = $this->cotizaciones_model->ultimo_numero();
        $numero = $numero_predt[0]->PRESUC_Numero;
		
        $data['numero']  	  = $numero+1;
        $data['codigo_usuario']   = "";
        $data['proveedor']         = "";
        $data['ruc_proveedor']     = "";
        $data['nombre_proveedor']  = "";
        $data['proveedor']       = "";
        $data['ruc_proveedor']   = "";
        $data['nombre_proveedor']= "";
        $data['detalle_presupuesto'] = array();
        $data['observacion']     = "";
        $data['focus']           = "";
        $data['pedido']          = "";
        $data['descuento']       = "0";
        $data['igv']             = $data_confi[0]->COMPCONFIC_Igv;
        $data['hidden']          = "";
        $data['preciototal']     = "";
        $data['descuentotal']    = "";
        $data['igvtotal']        = "";
        $data['importetotal']    = "";
        $data['preciototal_conigv']  = "";
        $data['descuentotal_conigv'] = "";
        $data['hidden']          = "";
        $data['observacion']     = "";
        $data['envio_direccion'] = "";
        $data['fact_direccion']  = "";
        $data['contacto']        = "";
        $data['tiempo_entrega']  = "";
        $data['garantia']        = "";
        $data['validez']         = "";
        $data['tipo_docu']       = $tipo_docu;
        $data['codigo']          = "";
        $data['estado']          = "1";
        $data['modo_impresion']  = "1";
        if($tipo_docu!='B')
            $data['modo_impresion']  = "2";
        
        $data['hoy']             = mysql_to_human(mdate("%Y-%m-%d ",time()));
        $atributos               = array('width'=>700,'height'=>450,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido               = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar Cliente' border='0'>";
        
        $serie                      = $data_confi_docu[0]->COMPCONFIDOCP_Serie;
        $data['tipo_codificacion']  = $data_confi_docu[0]->COMPCONFIDOCP_Tipo ;
        $data['serie_suger']        = $serie;
        $data['numero_suger']       = $this->cotizaciones_model->obtener_ultimo_numero($serie);
        
        $this->layout->view('compras/cotizaciones_nuevo',$data);
    }
    public function presupuesto_insertar(){ 
			$data_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $tipo_codificacion    = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
        
        switch($tipo_codificacion){
            case '2':
                if($this->input->post('serie')=='')   
                   exit ('{"result":"error", "campo":"serie"}');
                if($this->input->post('numero')=='')   
                   exit ('{"result":"error", "campo":"numero"}');
                break;
            case '3':
                if($this->input->post('codigo_usuario')=='')
                   exit ('{"result":"error", "campo":"codigo_usuario"}');
                break;
        }
		
		if($this->input->post('proveedor')=='')   
           exit ('{"result":"error", "campo":"ruc_proveedor"}');
        if($this->input->post('moneda')=='' || $this->input->post('moneda')=='0')
           exit ('{"result":"error", "campo":"moneda"}');
        if($this->input->post('estado')=='0' && $this->input->post('observacion')=='')
           exit ('{"result":"error", "campo":"observacion"}');
        
        $tipo_docu = $this->input->post('tipo_docu');
           
        $filter=new stdClass();
        $filter->PRESUC_TipoDocumento = $tipo_docu;
        
        
        if($this->input->post('forma_pago')!='' && $this->input->post('forma_pago')!='0')
            $filter->FORPAP_Codigo    = $this->input->post('forma_pago');
        $filter->PRESUC_Observacion   = strtoupper($this->input->post('observacion'));
        $filter->PRESUC_Fecha         = human_to_mysql($this->input->post('fecha'));
        if($this->input->post('serie'))
            $filter->PRESUC_Serie = $this->input->post('serie');
        if($this->input->post('numero'))
		$numero_predt = $this->cotizaciones_model->ultimo_numero();
        $numero = $numero_predt[0]->PRESUC_Numero;
        $filter->PRESUC_Numero = $numero + 1;
        if($this->input->post('codigo_usuario'))
            $filter->PRESUC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->MONED_Codigo      = $this->input->post('moneda');
        if($this->input->post('contacto')!='' && $this->input->post('contacto')!='0'){
            $temp=explode('-',$this->input->post('contacto'));
            $filter->PERSP_Codigo  = $temp[0];
            $filter->AREAP_Codigo  = $temp[1];
        }
        if($this->input->post('vendedor')!='' && $this->input->post('vendedor')!='0'){
            $temp=explode('-',$this->input->post('vendedor'));
            $filter->PRESUC_VendedorPersona  = $temp[0];
            $filter->PRESUC_VenedorArea  = $temp[1];
        }
        $filter->PRESUC_LugarEntrega   = $this->input->post('lugar_entrega');
        $filter->PRESUC_TiempoEntrega      = $this->input->post('tiempo_entrega');
        $filter->PRESUC_Garantia           = $this->input->post('garantia');
        $filter->PRESUC_Validez            = $this->input->post('validez');
        $filter->PRESUC_ModoImpresion      = '1'  ;
        if($this->input->post('modo_impresion')!='0' && $this->input->post('modo_impresion')!='') 
            $filter->PRESUC_ModoImpresion      = $this->input->post('modo_impresion');
        $filter->PRESUC_FlagEstado         = $this->input->post('estado');

        $filter->PRESUC_descuento100  = $this->input->post('descuento');
        $filter->PRESUC_igv100        = $this->input->post('igv');
        // $filter->CLIP_Codigo          = $this->input->post('cliente');
        //$filter->PROVP_Codigo          = $this->input->post('proveedor');
        $proveedor_array = $this->input->post('proveedor');
        if($tipo_docu!='B'){
            $filter->PRESUC_subtotal      = $this->input->post('preciototal');
            $filter->PRESUC_descuento     = $this->input->post('descuentotal');
            $filter->PRESUC_igv           = $this->input->post('igvtotal');
        }else{
            $filter->PRESUC_subtotal_conigv       = $this->input->post('preciototal_conigv');
            $filter->PRESUC_descuento_conigv      = $this->input->post('descuentotal_conigv');
        }       
        $filter->PRESUC_total         = $this->input->post('importetotal');
        $filter->PEDIP_Codigo         = $this->input->post('pedidos');
        $presupuesto = array();
        $num = $this->input->post('numero');
        foreach ($proveedor_array as $key => $value) {
            $filter->PRESUC_Numero = $num;
            $presupuesto[] = $this->cotizaciones_model->insertar_presupuesto_varios($filter,$value);
            $num ++;
        }
       // $presupuesto          = $this->cotizaciones_model->insertar_presupuesto($filter);
        
        $prodcodigo       = $this->input->post('prodcodigo');
        $prodcantidad     = $this->input->post('prodcantidad');
        if($tipo_docu!='B'){
            $prodpu        = $this->input->post('prodpu');
            $prodprecio    =  $this->input->post('prodprecio');
            $proddescuento =  $this->input->post('proddescuento');
            $prodigv       =  $this->input->post('prodigv');
        }
        else{
            $prodprecio_conigv    =  $this->input->post('prodprecio_conigv');
            $proddescuento_conigv =  $this->input->post('proddescuento_conigv');
        }
        $prodpu_conigv        = $this->input->post('prodpu_conigv');
        $prodimporte      =  $this->input->post('prodimporte');
        $produnidad       = $this->input->post('produnidad');
        $detaccion         = $this->input->post('detaccion');
        $proddescuento100 =  $this->input->post('proddescuento100');
        $prodigv100       =  $this->input->post('prodigv100');
        $proddescri       = $this->input->post('proddescri');
        
        if(is_array($prodcodigo))
        {
            foreach($prodcodigo as $indice=>$valor)
            {                 
                  $filter=new stdClass();
                 // $filter->PRESUP_Codigo=$presupuesto;
                  $filter->PROD_Codigo=$prodcodigo[$indice];
                  $filter->UNDMED_Codigo =$produnidad[$indice];
                  $filter->PRESDEC_Cantidad =$prodcantidad[$indice];
                  if($tipo_docu!='B'){
                      $filter->PRESDEC_Pu = $prodpu[$indice];
                      $filter->PRESDEC_Subtotal =$prodprecio[$indice];
                      $filter->PRESDEC_Descuento =$proddescuento[$indice];
                      $filter->PRESDEC_Igv =$prodigv[$indice];
                  }
                  else{
                      $filter->PRESDEC_Subtotal_ConIgv  =$prodprecio_conigv[$indice];
                      $filter->PRESDEC_Descuento_ConIgv  =$proddescuento_conigv[$indice];
                  }
                  $filter->PRESDEC_Pu_ConIgv =$prodpu_conigv[$indice];                 
                  $filter->PRESDEC_Total =$prodimporte[$indice];
                  $filter->PRESDEC_Descuento100 =$proddescuento100[$indice];
                  $filter->PRESDEC_Igv100  =$prodigv100[$indice];
                  $filter->PRESDEC_Descripcion =strtoupper($proddescri[$indice]);
                  $filter->PRESDEC_Observacion ="";
                  if($detaccion[$indice]!='e')
                    foreach ($presupuesto as $key => $value) {
                     $this->presupuestodetalle_model->insertar_varios($filter,$value);   
                    }
                     
              
            }
        }
        exit('{"result":"ok", "codigo":"'.$presupuesto.'"}');
    }
    public function presupuesto_editar($codigo)
    {   
	$data['compania'] =$this->somevar['compania'];
		$this->load->library('layout','layout');
        $data_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $data_compania        = $this->compania_model->obtener_compania($this->somevar['compania']);
        $my_empresa              = $data_compania[0]->EMPRP_Codigo;
    
        $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $proveedor         = $datos_presupuesto[0]->PROVP_Codigo;
        $forma_pago      = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda          = $datos_presupuesto[0]->MONED_Codigo;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $persona         = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $contacto        = ($persona!='' ? $persona.'-'.$area : '');
        $vendedor_persona = $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area    = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $vendedor_contacto        = ($vendedor_persona!='' ? $vendedor_persona.'-'.$vendedor_area : '');
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia        = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $modo_impresion  = $datos_presupuesto[0]->PRESUC_ModoImpresion;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $estado          = $datos_presupuesto[0]->PRESUC_FlagEstado;
        
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $subtotal_conigv = $datos_presupuesto[0]->PRESUC_subtotal_conigv;
        $descuento_conigv= $datos_presupuesto[0]->PRESUC_descuento_conigv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $pedido = $datos_presupuesto[0]->PEDIP_Codigo;
        
        $tipo='';
        $ruc_cliente='';
        $nombre_cliente='';
        $empresa='';
        $persona='';

		$proveedor_datos = $this->proveedor_model->obtener($proveedor);
        
        //Contactos
        $contactos   = $this->empresa_model->listar_contactosEmpresa($empresa);
        $arrContacto = array(""=>"::Seleccione::");
        if(count($contactos)>0){
            foreach($contactos as $value){
                $persona = $value->ECONC_Persona.'-'.$value->AREAP_Codigo;
                $nombres_persona = $value->PERSC_Nombre." ".$value->PERSC_ApellidoPaterno." ".$value->PERSC_ApellidoMaterno.($value->AREAP_Codigo!='0' && $value->AREAP_Codigo!='' ? " - ".$value->AREAC_Descripcion : '');
                $arrContacto[$persona]=$nombres_persona;
            }
        }
        
        $data['contacto']       = $contacto;
        $data['cboContacto']    = form_dropdown("contacto",$arrContacto,$contacto," class='comboMedio' style='width:307px;' id='contacto'");
        $data['cboVendedor']  = form_dropdown("vendedor",$this->emprcontacto_model->seleccionar($my_empresa),$vendedor_contacto," class='comboGrande' style='width:207px;' id='vendedor'");
        $data['cboPedidos']      = form_dropdown("pedidos",$this->pedido_model->seleccionar(),""," onchange='load_cotizaciones();' class='comboGrande' style='width:200px;' id='pedidos'");
        $data['cboFormaPago']   = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', ($forma_pago!='' ? $forma_pago : '12'));  //12: Al contado
        $data['cboMoneda']      = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion',$moneda);
        
        $data['lugar_entrega']  = $lugar_entrega;
        $data['tiempo_entrega'] = $tiempo_entrega;
        $data['garantia']       = $garantia;
        $data['validez']        = $validez;
        $data['estado']         = $estado;
        
        $data['serie']         = $serie;
        $data['numero']         = $numero;
        $data['codigo_usuario'] = $codigo_usuario;
        
        $data['descuento']      = $descuento100;
        $data['igv']            = $igv100;
        $data['preciototal']    = $subtotal;
        $data['descuentotal']   = $descuento;
        $data['igvtotal']       = $igv;
        $data['importetotal']   = $total;
        $data['preciototal_conigv']    = $subtotal_conigv;
        $data['descuentotal_conigv']   = $descuento_conigv;
        $data['proveedor']        = $proveedor;
        $data['ruc_proveedor']    = $proveedor_datos->ruc;
        $data['nombre_proveedor'] = $proveedor_datos->nombre;
        $data['contiene_igv']   = (($data_confi[0]->COMPCONFIC_PrecioContieneIgv=='1')?true:false);
        $oculto                 = form_hidden(array('codigo'=>$codigo,'base_url'=>base_url(), 'tipo_docu'=>$tipo_docu, 'contiene_igv'=>($data['contiene_igv']==true?'1':'0') ,"tipo_codificacion"=>$data_confi_docu[0]->COMPCONFIDOCP_Tipo));
        $data['titulo']         = "EDITAR SOLICITUD DE COTIZACION";
        $data['formulario']     = "frmCotizacion";
        $data['oculto']         = $oculto;
        $data['onload']		= "onload=\"\"";
        $data['url_action']     = base_url()."index.php/compras/cotizaciones/presupuesto_modificar";
        $atributos              = array('width'=>700,'height'=>450,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido              = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar Cliente' border='0'>";
        $data['vercliente']	= anchor_popup('compras/cliente/ventana_busqueda_cliente',$contenido,$atributos);
        $data['verproducto']    = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos);
        $data['hoy']            = $fecha;
        $data['observacion']    = $observacion;
        $data['hidden']         = "";
        $data['tipo_docu']      = $tipo_docu;
        $data['codigo']         = $codigo;
        $data['modo_impresion'] = $modo_impresion;
        $data['serie_suger']    = "";
        $data['numero_suger']   = "";
        
        $data['detalle_presupuesto']= $this->obtener_lista_detalles($codigo);
        
        $data['tipo_codificacion']  = $data_confi_docu[0]->COMPCONFIDOCP_Tipo ;
        $this->layout->view('compras/cotizaciones_nuevo',$data);
    }
    public function presupuesto_modificar()
    {   $data_confi           = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
        $tipo_codificacion    = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;

        switch($tipo_codificacion){
            case '2':
                if($this->input->post('serie')=='')   
                   exit ('{"result":"error", "campo":"serie"}');
                if($this->input->post('numero')=='')   
                   exit ('{"result":"error", "campo":"numero"}');
                break;
            case '3':
                if($this->input->post('codigo_usuario')=='')
                   exit ('{"result":"error", "campo":"codigo_usuario"}');
                break;
        }
        
        if($this->input->post('moneda')=='' || $this->input->post('moneda')=='0')
           exit ('{"result":"error", "campo":"moneda"}');
        if($this->input->post('estado')=='0' && $this->input->post('observacion')=='')
           exit ('{"result":"error", "campo":"observacion"}');
		if($this->input->post('proveedor')=='')   
           exit ('{"result":"error", "campo":"ruc_proveedor"}');
        $codigo = $this->input->post('codigo');
        $tipo_docu = $this->input->post('tipo_docu');
        
        $filter=new stdClass();
        $filter->PERSP_Codigo        = NULL;
        $filter->AREAP_Codigo        = NULL;
        $filter->CLIP_Codigo        = 1;
        if($this->input->post('contacto')!='' && $this->input->post('contacto')!='0'){
           $temp                     = explode('-',$this->input->post('contacto'));
           $filter->PERSP_Codigo     = $temp[0];
           $filter->AREAP_Codigo     = $temp[1];
        }
        $filter->PRESUC_VendedorPersona = NULL;
        $filter->PRESUC_VenedorArea     = NULL;
        if($this->input->post('vendedor')!='' && $this->input->post('vendedor')!='0'){
            $temp=explode('-',$this->input->post('vendedor'));
            $filter->PRESUC_VendedorPersona  = $temp[0];
            $filter->PRESUC_VenedorArea  = $temp[1];
        }
        $filter->PRESUC_LugarEntrega  = $this->input->post('lugar_entrega');
        $filter->PRESUC_TiempoEntrega = $this->input->post('tiempo_entrega');
        $filter->PRESUC_Garantia      = $this->input->post('garantia');
        $filter->PRESUC_Validez       = $this->input->post('validez');
        $filter->FORPAP_Codigo        = NULL;
        if($this->input->post('forma_pago')!='' && $this->input->post('forma_pago')!='0')
            $filter->FORPAP_Codigo    = $this->input->post('forma_pago');
        $filter->PRESUC_Observacion   = strtoupper($this->input->post('observacion'));
        $filter->PRESUC_Fecha         = human_to_mysql($this->input->post('fecha'));
        $filter->PRESUC_Serie         = NULL;
        if($this->input->post('serie'))
            $filter->PRESUC_Serie = $this->input->post('serie');
        $filter->PRESUC_Numero = NULL;
        if($this->input->post('numero'))
            $filter->PRESUC_Numero = $this->input->post('numero');
        $filter->PRESUC_CodigoUsuario = NULL;
        if($this->input->post('codigo_usuario'))
            $filter->PRESUC_CodigoUsuario = $this->input->post('codigo_usuario');
        $filter->MONED_Codigo      = $this->input->post('moneda');
        // $filter->CLIP_Codigo       = $this->input->post('cliente');
        $filter->PRESUC_ModoImpresion = '1'  ;
        if($this->input->post('modo_impresion')!='0' && $this->input->post('modo_impresion')!='') 
            $filter->PRESUC_ModoImpresion      = $this->input->post('modo_impresion');
        $filter->PRESUC_FlagEstado    = $this->input->post('estado');
        
        $filter->PRESUC_descuento100  = $this->input->post('descuento');
        $filter->PRESUC_igv100        = $this->input->post('igv');
        
        if($tipo_docu!='B'){
            $filter->PRESUC_subtotal      = $this->input->post('preciototal');
            $filter->PRESUC_descuento     = $this->input->post('descuentotal');
            $filter->PRESUC_igv           = $this->input->post('igvtotal');
        }else{
            $filter->PRESUC_subtotal_conigv       = $this->input->post('preciototal_conigv');
            $filter->PRESUC_descuento_conigv      = $this->input->post('descuentotal_conigv');
        }       
        $filter->PRESUC_total         = $this->input->post('importetotal');
        $filter->PEDIP_Codigo         = $this->input->post('pedidos');
        $filter->PROVP_Codigo         = $this->input->post('proveedor');
        $filter->CLIP_Codigo         = 1;
        
        $this->cotizaciones_model->modificar_presupuesto($codigo,$filter);
        
        $prodcodigo    = $this->input->post('prodcodigo');
        $prodcantidad  = $this->input->post('prodcantidad');
        if($tipo_docu!='B'){
            $prodpu        = $this->input->post('prodpu');
            $prodprecio    =  $this->input->post('prodprecio');
            $proddescuento =  $this->input->post('proddescuento');
            $prodigv       =  $this->input->post('prodigv');
        }
        else{
            $prodprecio_conigv    =  $this->input->post('prodprecio_conigv');
            $proddescuento_conigv =  $this->input->post('proddescuento_conigv');
        } 
        $prodpu_conigv        = $this->input->post('prodpu_conigv');
        $prodimporte   =  $this->input->post('prodimporte');
        $produnidad    = $this->input->post('produnidad');
        $detaccion     = $this->input->post('detaccion');
        $detacodi      = $this->input->post('detacodi');
        $prodigv100    = $this->input->post('prodigv100');
        $proddescuento100 = $this->input->post('proddescuento100');
        $proddescri       = $this->input->post('proddescri');
        
        if(is_array($detacodi))
        {
            foreach($detacodi as $indice=>$valor){
              $detalle_accion    = $detaccion[$indice];
              
              $filter=new stdClass();
              $filter->PRESUP_Codigo=$codigo;
              $filter->PROD_Codigo=$prodcodigo[$indice];
              $filter->UNDMED_Codigo =$produnidad[$indice];
              $filter->PRESDEC_Cantidad =$prodcantidad[$indice];
              if($tipo_docu!='B'){
                  $filter->PRESDEC_Pu = $prodpu[$indice];
                  $filter->PRESDEC_Subtotal =$prodprecio[$indice];
                  $filter->PRESDEC_Descuento =$proddescuento[$indice];
                  $filter->PRESDEC_Igv =$prodigv[$indice];
              }
              else{
                  $filter->PRESDEC_Subtotal_ConIgv  =$prodprecio_conigv[$indice];
                  $filter->PRESDEC_Descuento_ConIgv  =$proddescuento_conigv[$indice];
              }
              $filter->PRESDEC_Pu_ConIgv =$prodpu_conigv[$indice];
              $filter->PRESDEC_Total =$prodimporte[$indice];
              $filter->PRESDEC_Descuento100 =$proddescuento100[$indice];
              $filter->PRESDEC_Igv100  =$prodigv100[$indice];
              $filter->PRESDEC_Descripcion =strtoupper($proddescri[$indice]);
              $filter->PRESDEC_Observacion ="";
       
              if($detalle_accion=='n'){
                    $this->presupuestodetalle_model->insertar($filter);  
              }elseif($detalle_accion=='m') {
                      $this->presupuestodetalle_model->modificar($valor, $filter);
              }elseif($detalle_accion=='e'){
                      $this->presupuestodetalle_model->eliminar($valor);
              }
            }
        }
        exit('{"result":"ok", "codigo":"'.$codigo.'"}');
        
    }
    public function presupuesto_eliminar(){
        $this->load->library('layout','layout');
        
        $presupuesto = $this->input->post('presupuesto');
        $this->cotizaciones_model->eliminar_presupuesto($presupuesto);
    }
    public function presupuesto_buscar(){

    }
    public function obtener_lista_detalles($codigo){
        $detalle               = $this->presupuestodetalle_model->listar($codigo);
        $lista_detalles        = array();
        if(count($detalle)>0){
            foreach($detalle as $indice=>$valor)
            {
                $detacodi        = $valor->PRESDEP_Codigo;
                $producto        = $valor->PROD_Codigo;
                $unidad          = $valor->UNDMED_Codigo;
                $cantidad        = $valor->PRESDEC_Cantidad;
                $pu              = $valor->PRESDEC_Pu;
                $subtotal        = $valor->PRESDEC_Subtotal;
                $igv             = $valor->PRESDEC_Igv;
                $descuento       = $valor->PRESDEC_Descuento;
                $total           = $valor->PRESDEC_Total;
                $pu_conigv       = $valor->PRESDEC_Pu_ConIgv;
                $subtotal_conigv = $valor->PRESDEC_Subtotal_ConIgv;
                $descuento_conigv= $valor->PRESDEC_Descuento_ConIgv;
                $descuento100    = $valor->PRESDEC_Descuento100;
                $igv100          = $valor->PRESDEC_Igv100;
                $observacion     = $valor->PRESDEC_Observacion;
                $datos_producto  = $this->producto_model->obtener_producto($producto);
                $datos_unidad    = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = ($valor->PRESDEC_Descripcion!='' ? $valor->PRESDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\','',$nombre_producto);
                $marca           = "";
                if($datos_producto[0]->MARCP_Codigo!='0' &&  $datos_producto[0]->MARCP_Codigo!='1'){
                    $datos_marca = $this->marca_model->getMarca($datos_producto[0]->MARCP_Codigo);
                    if(count($datos_marca)>0)
                        $marca = $datos_marca[0]->MARCC_Descripcion;
                }
                
                $modelo          = $datos_producto[0]->PROD_Modelo;
                $codigo_interno  = $datos_producto[0]->PROD_CodigoInterno;
                $codigo_usuario  = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad   = $datos_unidad[0]->UNDMED_Simbolo;
                $objeto   =   new stdClass();
                $objeto->PRESDEP_Codigo      = $detacodi;
                $objeto->PROD_Codigo         = $producto;
                $objeto->PROD_CodigoInterno  = $codigo_interno;
                $objeto->PROD_CodigoUsuario  = $codigo_usuario;
                $objeto->UNDMED_Codigo       = $unidad;
                $objeto->UNDMED_Simbolo      = $nombre_unidad;
                $objeto->PROD_Nombre         = $nombre_producto;
                $objeto->MARCC_Descripcion   = $marca;
                $objeto->PROD_Modelo         = $modelo;
                $objeto->PRESDEC_Cantidad    = $cantidad;
                $objeto->PRESDEC_Pu          = $pu;
                $objeto->PRESDEC_Subtotal    = $subtotal;
                $objeto->PRESDEC_Descuento   = $descuento;
                $objeto->PRESDEC_Igv         = $igv;
                $objeto->PRESDEC_Total       = $total;
                $objeto->PRESDEC_Pu_ConIgv   = $pu_conigv;
                $objeto->PRESDEC_Subtotal_ConIgv    = $subtotal_conigv;
                $objeto->PRESDEC_Descuento_ConIgv   = $descuento_conigv;
                $objeto->PRESDEC_Descuento100= $descuento100;
                $objeto->PRESDEC_Igv100      = $igv100;
                $objeto->PRESDEC_Observacion = $observacion;
                $lista_detalles[]           = $objeto;
            }
        }
        return $lista_detalles;
    }
    
    public function presupuesto_ver_pdf($codigo){
        switch(FORMATO_IMPRESION){
            case 1: //Formato para ferresat
                $this->presupuesto_ver_pdf_formato1($codigo);
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
            default: presupuesto_ver_pdf_conmenbrete_formato1($codigo); break;
        }
    }
	
	
	
	public function presupuesto_ver_xls($codigo)
	{
		
		$datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion    =($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));  
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
		
		$xls = utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>';
        
         
		$xls .= "<table>
		<tr><td>".utf8_decode_seguro('Señor(es) :')." </td><td>$nombre_cliente</td><td></td><td>R.U.C.</td><td>$ruc</td><td>Fecha : </td><td>$fecha</td></tr>
		<tr><td>".utf8_decode_seguro('Dirección :')." </td><td>$direccion</td><td></td><td></td><td></td><td></td></tr>
		<tr><td>".utf8_decode_seguro('Atención Sr(a) :')." </td><td>$nombre_contacto ".($nombre_area!='' ? ' - AREA: '.$nombre_area: '')."</td><td></td>&nbsp;&nbsp;&nbsp;<td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td>".utf8_decode_seguro('Teléfono : ')."</td><td>$telefono</td><td></td><td></td><td></td><td>E-mail :</td><td>$email</td></tr>
		</table><br><br>
		";
		
		$date = date('Y-m-d').'-'.$ruc.'-Presupuesto';
		header('Content-Disposition: attachment; filename="'.$date.'.xls"');
		header("Content-Type: application/vnd.ms-excel");
		
		$xls .= "
		<table border=1>
			<tr><th>Item</th><th>Marca</th><th>".utf8_decode_seguro('Descripción')."</th><th>Uni.</th><th>Cant.</th></tr>
		";
		
		foreach($detalle_presupuesto as $indice=>$valor){
			$xls.= "<tr><td>".($indice+1)."</td><td>".$valor->MARCC_Descripcion."</td><td>".utf8_decode_seguro($valor->PROD_Nombre)."</td><td>".$valor->UNDMED_Simbolo."</td><td>".$valor->PRESDEC_Cantidad."</td></tr>";
		}
		
		$xls .= "</table><br><br>";
		 
        
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        
		
		$xls .= "
			<table>
			<tr><td colspan=2><b>CONDICIONES DE VENTA:</b></td></tr>
			<tr><td>".utf8_decode_seguro('Tipo de Cambio del Día :')."</td><td>".($tipo_cambio>0 ? round($tipo_cambio,2) : '' )."</td></tr>
			<tr><td>Moneda</td><td>$moneda_nombre</td></tr>
			<tr><td>Forma de Pago</td><td>".utf8_decode_seguro($forma_pago)."</td></tr>
			<tr><td>".utf8_decode_seguro('Cta. Cte. en Soles')."</td><td>".utf8_decode_seguro('N°  191-1435467-0-65')."</td></tr>
			<tr><td>".utf8_decode_seguro('Cta. Cte. en Dólares')."</td><td>".utf8_decode_seguro('N° 191-1466829-1-62')."</td></tr>
			<tr><td>Lugar de Entrega</td><td>".utf8_decode_seguro($lugar_entrega)."</td></tr>
			<tr><td>Contacto</td><td>".utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' ))."</td></tr>
			
			</table>
		";
			
		$data['xls'] = $xls;
		$this->load->view('compras/presupuesto_ver_xls',$data);
        
	}
	
	
    public function presupuesto_ver_pdf_formato1($codigo)
    {   
	
		$datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion    =($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));  
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new Cezpdf('a4');
        
        /*Para las imagenes*/ 
        
        $delta=20;
        
        $this->cezpdf->ezText('','',array("leading"=>100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );        
						
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>  ''), 
                        array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                        array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                        );
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>405,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>50,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>'Los Precios de los Productos ', 'cols1'=>': '.($modo_impresion=='1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
                        array('cols0'=>utf8_decode_seguro('Cta. Cte. en Soles'), 'cols1'=>': '.utf8_decode_seguro('N°  191-1435467-0-65')),
                        array('cols0'=>utf8_decode_seguro('Cta. Cte. en Dólares'), 'cols1'=>': '.utf8_decode_seguro('N° 191-1466829-1-62')),
                        array('cols0'=>'Tiempo de Entrega', 'cols1'=>': '.$tiempo_entrega),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>'Validez de la Oferta', 'cols1'=>': '.utf8_decode_seguro($validez)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function presupuesto_ver_pdf_formato2($codigo)
    {   $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion=($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));        
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
        $datacreator = array (
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
         );

          $this->cezpdf->addInfo($datacreator); 
        /*Para las imagenes*/ 
        
        $delta=20;
        
        $this->cezpdf->ezText('','',array("leading"=>100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre.($valor->PROD_Modelo!='' ? ' - '.$valor->PROD_Modelo: '')),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         if($tipo_docu!='B'){
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>  ''), 
                            array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
         else{
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
             
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>395,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>60,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>'Los Precios de los Productos ', 'cols1'=>': '.($modo_impresion=='1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
                        array('cols0'=>'Tiempo de Entrega', 'cols1'=>': '.$tiempo_entrega),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>'Validez de la Oferta', 'cols1'=>': '.utf8_decode_seguro($validez)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function presupuesto_ver_pdf_formato4($codigo)
    {   $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion=($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));        
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia        = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
         $datacreator = array (
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
          );

          $this->cezpdf->addInfo($datacreator); 
        /*Para las imagenes*/ 
        
        $delta=20;
        
        $this->cezpdf->ezText('','',array("leading"=>100));
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre.($valor->PROD_Modelo!='' ? ' - '.$valor->PROD_Modelo: '')),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         if($tipo_docu!='B'){
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>  ''), 
                            array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
         else{
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
             
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>395,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>60,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>'Los Precios de los Productos ', 'cols1'=>': '.($modo_impresion=='1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
                        array('cols0'=>'Tiempo de Entrega', 'cols1'=>': '.$tiempo_entrega),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>utf8_decode_seguro('Garantía'),'cols1'=>': '.utf8_decode_seguro($garantia)),
                        array('cols0'=>'Validez de la Oferta', 'cols1'=>': '.utf8_decode_seguro($validez)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    
    public function presupuesto_ver_pdf_conmenbrete($codigo){
        switch(FORMATO_IMPRESION){
            case 1: //Formato para ferresat
                $this->presupuesto_ver_pdf_conmenbrete_formato1($codigo);
                break;
            case 2:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo); 
                break;
            case 4:  //Formato para ferremax
                $this->presupuesto_ver_pdf_conmenbrete_formato4($codigo); 
                break;
            default: presupuesto_ver_pdf_conmenbrete_formato1($codigo); break;
        }
    }
    
    public function presupuesto_ver_pdf_conmenbrete_formato1($codigo)
    {   $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion=($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));        
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        $this->cezpdf = new Cezpdf('a4');
        
         $datacreator = array (
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
          );

          $this->cezpdf->addInfo($datacreator); 
        /*Para las imagenes*/ 
        
        $delta=20;
        $this->cezpdf->ezImage("images/img_db/ferresat_cabe.jpg", -10, 555, 'none', 'left');
        
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad,
                'cols6'=>number_format(($modo_impresion=='1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu),2),
                'cols7'=>number_format($valor->PRESDEC_Cantidad*($modo_impresion=='1' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu),2)
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7'  => 'Precio Total'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>  ''), 
                        array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                        array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                        );
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>405,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>50,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>utf8_decode_seguro('Cta. Cte. en Soles'), 'cols1'=>': '.utf8_decode_seguro('N°  191-1435467-0-65')),
                        array('cols0'=>utf8_decode_seguro('Cta. Cte. en Dólares'), 'cols1'=>': '.utf8_decode_seguro('N° 191-1466829-1-62')),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function presupuesto_ver_pdf_conmenbrete_formato2($codigo)
    {   $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion=($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));        
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/jimmyplast_fondo_presupuesto.jpg')); 
         $datacreator = array (
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
          );

          $this->cezpdf->addInfo($datacreator); 
        /*Para las imagenes*/ 
        $this->cezpdf->ezImage("images/img_db/jimmyplast_cabe.jpg", -10, 555, 'none', 'left');
        
        $delta=20;
        
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre.($valor->PROD_Modelo!='' ? ' - '.$valor->PROD_Modelo: '')),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad,
                'cols6'=>number_format(($modo_impresion=='1' || $tipo_docu=='B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu),2),
                'cols7'=>number_format($valor->PRESDEC_Cantidad*($modo_impresion=='1' || $tipo_docu=='B' ? $valor->PRESDEC_Pu_ConIgv : $valor->PRESDEC_Pu),2)
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.',
            'cols6' => 'Precio Uni.',
            'cols7'  => 'Precio Total'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         if($tipo_docu!='B'){
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>  ''), 
                            array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
         else{
             $db_data=array(array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
             
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>395,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>60,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>'Los Precios de los Productos ', 'cols1'=>': '.($modo_impresion=='1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
                        array('cols0'=>'Tiempo de Entrega', 'cols1'=>': '.$tiempo_entrega),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>'Validez de la Oferta', 'cols1'=>': '.utf8_decode_seguro($validez)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    public function presupuesto_ver_pdf_conmenbrete_formato3($codigo)
    {      
        $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $vendedor        = $datos_presupuesto[0]->USUA_Codigo;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia        = $datos_presupuesto[0]->PRESUC_Garantia;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
        $temp            = $this->usuario_model->obtener($vendedor);
        $temp            = $this->persona_model->obtener_datosPersona($temp->PERSP_Codigo );
        $vendedor        = $temp[0]->PERSC_Nombre.' '.$temp[0]->PERSC_ApellidoPaterno.' '.$temp[0]->PERSC_ApellidoMaterno;
        
        $datos_compania  = $this->compania_model->obtener_compania($this->somevar['compania']);
        $datos_empresa   = $this->empresa_model->obtener_datosEmpresa($datos_compania[0]->EMPRP_Codigo );
        
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_contacto   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_contacto)>0)
                $nombre_contacto  = $datos_contacto[0]->PERSC_Nombre.' '. $datos_contacto[0]->PERSC_ApellidoPaterno.' '. $datos_contacto[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $lugar_entrega=$datos_presupuesto[0]->PRESUC_LugarEntrega;
        $forma_pago='';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
             $datos_formapago=$this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
             if(count($datos_formapago)>0)
                $forma_pago=$datos_formapago[0]->FORPAC_Descripcion;
        }
        $tiempo_entrega=$datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia=$datos_presupuesto[0]->PRESUC_Garantia;
        $validez=$datos_presupuesto[0]->PRESUC_Validez;
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf_horizontal');
        //$this->load->helper('pdf_helper');
        //prep_pdf_horizontal();
        $this->cezpdf_horizontal = new Cezpdf('a4', 'landscape');
        
        /*Para las imagenes*/ 
        
        $delta=20;
        
        $this->cezpdf_horizontal->ezText('','');
        $this->cezpdf_horizontal->ezImage("images/img_db/logo_instrume_unido.jpg", 0, 740, 'none', 'left');

        $this->cezpdf_horizontal->ezText(utf8_decode_seguro($datos_empresa[0]->EMPRC_RazonSocial),8, array("left"=>15));
        $this->cezpdf_horizontal->ezText(utf8_decode_seguro('Lima: Av. Chorrillos Nº 200 Chorrillos - Lima  Telf.: 251 6727  Fax: 252 7547'),8, array("left"=>15));
        $this->cezpdf_horizontal->ezText('Cusco: Av. Garcilaso S/N C.C. La Salle Of. 143 Wanchaq - Cusco  Telf.: 84-253453  Telefax: 84-263225',8, array("left"=>15));
        $this->cezpdf_horizontal->ezText('www.instrumentosysistemas.com ventas@instrumentosysistemas.com',8, array("left"=>15));
        $this->cezpdf_horizontal->ezText(utf8_decode_seguro('<b>Solicitud de Cotización Nro.  ').($codigo_usuario!='' ? $codigo_usuario : $numero).'</b>',17,array("leading"=>30,"left"=>300));
        $this->cezpdf_horizontal->ezText('','');
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc, 'cols5'=>'Fecha', 'cols6'=>': '.$fecha, 'cols7'=>''), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.$direccion, 'cols3'=>'', 'cols4'=>'', 'cols5'=>'', 'cols6'=>'', 'cols7'=>''),
                       array('cols1'=>utf8_decode_seguro('Contacto'),'cols2'=>': '.utf8_decode_seguro($contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>'', 'cols5'=>'', 'cols6'=>'', 'cols7'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email, 'cols5'=>'', 'cols6'=>'', 'cols7'=>'')
                        );         
         $this->cezpdf_horizontal->ezTable($db_data,"","",array(
            'width'=>740,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>300,'justification'=>'left'),
                'cols3'=>array('width'=>40,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>70,'justification'=>'left'),
                'cols7'=>array('width'=>70,'justification'=>'left'),
                )
        ));
        
        $this->cezpdf_horizontal->ezText('',8);
              
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            if($valor->PRESDEC_Pu_ConIgv!='')
                $pu_conigv=$valor->PRESDEC_Pu_ConIgv;
            else
                $pu_conigv=$valor->PRESDEC_Pu+$valor->PRESDEC_Pu*$valor->PRESDEC_Igv100/100;
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->PROD_CodigoUsuario,
                'cols3' =>utf8_decode_seguro($valor->MARCC_Descripcion),
                'cols4' =>utf8_decode_seguro($valor->PROD_Modelo),
                'cols5'=>utf8_decode_seguro($valor->PROD_Nombre),
                'cols6'=>$valor->UNDMED_Simbolo,
                'cols7'=>$valor->PRESDEC_Cantidad,
                'cols8'=>number_format($pu_conigv,2),
                'cols9'=>number_format($valor->PRESDEC_Cantidad*$pu_conigv,2)
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => utf8_decode_seguro('Código'),
            'cols3' => 'Marca',
            'cols4' => 'Modelo',
            'cols5' => utf8_decode_seguro('Descripción'),
            'cols6' => 'Uni.',
            'cols7' => 'Cant.',
            'cols8' => 'Precio Uni.',
            'cols9'  => 'Precio Total'
         );
         
         $this->cezpdf_horizontal->ezTable($db_data,$col_names, '', array(
            'width'=>730,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'center'),
                'cols3'=>array('width'=>75,'justification'=>'left'),
                'cols4'=>array('width'=>75,'justification'=>'left'),
                'cols5'=>array('width'=>250,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'left'),
                'cols7'=>array('width'=>50,'justification'=>'right'),
                'cols8'=>array('width'=>60,'justification'=>'right'),
		'cols9'=>array('width'=>70,'justification'=>'right')
                )
         ));
         
         $this->cezpdf_horizontal->ezText('','');
         
         /*Totales*/
         $db_data=array(array('cols0'=>'', 'cols1'=>'<b>SUBTOTAL</b>','cols2'=>  ''), 
                        array('cols0'=>'', 'cols1'=>'<b>IGV</b>','cols2'=>''),
                        array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                        );
         $this->cezpdf_horizontal->ezTable($db_data,"","",array(
            'width'=>730,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>9,
            'cols'=>array(
                'cols0'=>array('width'=>620,'justification'=>'left'),
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>55,'justification'=>'right')
                )
        ));
         
        /*Condiciones de venta*/
         $db_data=array(array('cols0'=>'<b>Condiciones de venta:</b>','cols1'=>'','cols2'=>''), 
                        array('cols0'=>'Forma de pago','cols1'=>': ', 'cols2'=>utf8_decode_seguro(strtoupper($forma_pago))),
                        array('cols0'=>utf8_decode_seguro('Banco de Crédito Soles'), 'cols1'=>':','cols2'=>'Cta. Cte. No. 285-1178292-0-25'),
                        array('cols0'=>utf8_decode_seguro('Banco de Crédito Dólares'), 'cols1'=>':','cols2'=>'Cta. Cte. No. 285-1202278-1-18'),
                        array('cols0'=>'Lugar de entrega', 'cols1'=>':','cols2'=>utf8_decode_seguro(strtoupper($lugar_entrega))),
                        array('cols0'=>'Plazo de entrega','cols1'=>':', 'cols2'=>utf8_decode_seguro(strtoupper($tiempo_entrega)))
                        );         
         $this->cezpdf_horizontal->ezTable($db_data,"","",array(
            'width'=>730,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>20,'justification'=>'center'),
                'cols2'=>array('width'=>590,'justification'=>'left')
                )
        ));
         $this->cezpdf_horizontal->ezText('-------------------------------------------------------------------',9,array("left"=>300));
         $this->cezpdf_horizontal->ezText('<b>p. '.$datos_empresa[0]->EMPRC_RazonSocial.'</b>',9,array("left"=>320));
        

        $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>'nama_file.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf_horizontal->ezStream($cabecera);
    }
    public function presupuesto_ver_pdf_conmenbrete_formato4($codigo)
    {   $datos_presupuesto   = $this->cotizaciones_model->obtener_presupuesto($codigo);
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $serie           = $datos_presupuesto[0]->PRESUC_Serie;
        $numero          = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario  = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $codificacion=($codigo_usuario!='' ? $codigo_usuario : ($serie!='' ? $serie.'/'.$numero : 'Nro. '.$numero));        
        $proveedor = $datos_presupuesto[0]->PROVP_Codigo;
        $subtotal        = $datos_presupuesto[0]->PRESUC_subtotal;
        $descuento       = $datos_presupuesto[0]->PRESUC_descuento;
        $igv             = $datos_presupuesto[0]->PRESUC_igv;
        $igv100          = $datos_presupuesto[0]->PRESUC_igv100;
        $descuento100    = $datos_presupuesto[0]->PRESUC_descuento100;
        $total           = $datos_presupuesto[0]->PRESUC_total;
        $observacion     = $datos_presupuesto[0]->PRESUC_Observacion;
        $tipo_docu       = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $fecha           = mysql_to_human($datos_presupuesto[0]->PRESUC_Fecha);
        $lugar_entrega   = $datos_presupuesto[0]->PRESUC_LugarEntrega;
        $tiempo_entrega  = $datos_presupuesto[0]->PRESUC_TiempoEntrega;
        $garantia        =$datos_presupuesto[0]->PRESUC_Garantia;
        $validez         = $datos_presupuesto[0]->PRESUC_Validez;
        $contacto        = $datos_presupuesto[0]->PERSP_Codigo;
        $area            = $datos_presupuesto[0]->AREAP_Codigo;
        $vendedor_persona= $datos_presupuesto[0]->PRESUC_VendedorPersona;
        $vendedor_area   = $datos_presupuesto[0]->PRESUC_VenedorArea;
        $modo_impresion  = ((int)$datos_presupuesto[0]->PRESUC_ModoImpresion>0 ? $datos_presupuesto[0]->PRESUC_ModoImpresion : '1');
        
        $forma_pago      = '';
        if($datos_presupuesto[0]->FORPAP_Codigo!=''){
            $datos_formapago    = $this->formapago_model->obtener($datos_presupuesto[0]->FORPAP_Codigo);
            $forma_pago         = $datos_formapago[0]->FORPAC_Descripcion;
        }
        $cliente=1;$datos_cliente   = $this->cliente_model->obtener_datosCliente($cliente);
        $empresa         = $datos_cliente[0]->EMPRP_Codigo;
        $persona         = $datos_cliente[0]->PERSP_Codigo;
        $tipo            = $datos_cliente[0]->CLIC_TipoPersona;
        
              
        $datos_moneda    = $this->moneda_model->obtener($datos_presupuesto[0]->MONED_Codigo);
        $moneda_nombre   = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Descripcion  : 'NUEVOS SOLES');
        $moneda_simbolo  = (count($datos_moneda)>0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
            
        $temp = $this->proveedor_model->obtener($proveedor);
        $nombre_cliente=$temp->nombre;
        $ruc=$temp->ruc;
        $direccion=$temp->direccion;
        $telefono=($temp->telefono=='' ? $temp->movil : $temp->telefono);
        $fax=$temp->fax;
        $email='';
        
        $nombre_contacto=$nombre_cliente;
        if($contacto!='' && $contacto!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($contacto);
            if(count($datos_persona)>0)
                $nombre_contacto  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $nombre_area='';
        if($area!='' && $area!='0'){
            $datos_area   = $this->area_model->obtener_area($area);
            if(count($datos_area)>0)
                $nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        $filter=new stdClass();
        $filter->TIPCAMC_Fecha=date('Y-m-d', time());
        $filter->TIPCAMC_MonedaDestino =2;  // De soles a dolares
        $data_tipocambio=$this->tipocambio_model->buscar($filter);
        $tipo_cambio='';
        if(count($data_tipocambio)>0)
            $tipo_cambio=$data_tipocambio[0]->TIPCAMC_FactorConversion;
        
        
        $detalle_presupuesto = $this->obtener_lista_detalles($codigo);
        
        //$this->load->library('cezpdf');
        //$this->load->helper('pdf_helper');
        //prep_pdf();
        //$this->cezpdf = new Cezpdf('a4');
        if($this->somevar['compania']==1)
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/ferremax_fondo.jpg')); 
        else
            $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=>'images/img_db/ferremax_jmb_fondo.jpg')); 
        $datacreator = array (
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'Vicente Producciones',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'info@vicenteproducciones.com',
            'Producer' => 'http://www.vicenteproducciones.com'
        );

          $this->cezpdf->addInfo($datacreator); 
        /*Para las imagenes*/ 
        if($this->somevar['compania']==1)
            $this->cezpdf->ezImage("images/img_db/ferremax_cabe.jpg", -10, 555, 'none', 'left');
        else
            $this->cezpdf->ezImage("images/img_db/ferremax_cabe_jmb.jpg", -10, 555, 'none', 'left');
        
        $delta=20;
        
        $this->cezpdf->ezText(utf8_decode_seguro('<b>Solicitud de Cotización: ').$codificacion.'</b>',17,array("leading"=>40,'left'=>185));
        $this->cezpdf->ezText('','',array("leading"=>10));
        
         
        /*Datos del cliente*/ 
        $db_data=array(array('cols1'=>utf8_decode_seguro('Señor(es)'),'cols2'=>': '.utf8_decode_seguro($nombre_cliente), 'cols3'=>'R.U.C.', 'cols4'=>': '.$ruc.'       Fecha: '.$fecha ), 
                       array('cols1'=>utf8_decode_seguro('Dirección'),'cols2'=>': '.utf8_decode_seguro($direccion), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Atención Sr(a)'),'cols2'=>': '.utf8_decode_seguro($nombre_contacto.($nombre_area!='' ? ' - AREA: '.$nombre_area: '')), 'cols3'=>'', 'cols4'=>''),
                       array('cols1'=>utf8_decode_seguro('Teléfono'),'cols2'=>': '.$telefono, 'cols3'=>'E-mail', 'cols4'=>': '.$email)
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>65,'justification'=>'left'),
                'cols2'=>array('width'=>275,'justification'=>'left'),
                'cols3'=>array('width'=>35,'justification'=>'left'),
                'cols4'=>array('width'=>150,'justification'=>'left')
                )
        ));
        
        $this->cezpdf->ezText('',8);
        
        /*Listado de detalles*/
        $db_data=array();
        foreach($detalle_presupuesto as $indice=>$valor){
            $db_data[] = array(
                'cols1'=>$indice+1,
                'cols2' =>$valor->MARCC_Descripcion,
                'cols3'=>utf8_decode_seguro($valor->PROD_Nombre.($valor->PROD_Modelo!='' ? ' - '.$valor->PROD_Modelo: '')),
                'cols4'=>$valor->UNDMED_Simbolo,
                'cols5'=>$valor->PRESDEC_Cantidad
                );
         }
         $col_names = array(
            'cols1' => 'Item',
            'cols2' => 'Marca',
            'cols3' => utf8_decode_seguro('Descripción'),
            'cols4' => 'Uni.',
            'cols5' => 'Cant.'
         );
         
         $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>525,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols1'=>array('width'=>30,'justification'=>'center'),
                'cols2'=>array('width'=>70,'justification'=>'left'),
                'cols3'=>array('width'=>235,'justification'=>'left'),
                'cols4'=>array('width'=>40,'justification'=>'left'),
                'cols5'=>array('width'=>40,'justification'=>'left'),
                'cols6'=>array('width'=>50,'justification'=>'right'),
		'cols7'=>array('width'=>60,'justification'=>'right')
                )
         ));
         
         $this->cezpdf->ezText('','');
         
         /*Totales*/
         if($tipo_docu!='B'){
             $db_data=array(array('cols0'=>'', 'cols1'=>''), 
                            array('cols0'=>'', 'cols1'=>'','cols2'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
         else{
             $db_data=array(array('cols0'=>'', 'cols1'=>''),
                            array('cols0'=>'', 'cols1'=>'','cols2'=>'')
                            );
         }
             
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>395,'justification'=>'left'),
                'cols1'=>array('width'=>70,'justification'=>'left'),
                'cols2'=>array('width'=>60,'justification'=>'right')
                )
        ));
         
         $vendedor_nombre='';
        if($vendedor_persona!='' && $vendedor_persona!='0'){
            $datos_persona   = $this->persona_model->obtener_datosPersona($vendedor_persona);
            if(count($datos_persona)>0)
                $vendedor_nombre  = $datos_persona[0]->PERSC_Nombre.' '. $datos_persona[0]->PERSC_ApellidoPaterno.' '. $datos_persona[0]->PERSC_ApellidoMaterno;
        }
        $vendedor_nombre_area='';
        if($vendedor_area!='' && $vendedor_area!='0'){
            $datos_area   = $this->area_model->obtener_area($vendedor_area);
            if(count($datos_area)>0)
                $vendedor_nombre_area  = $datos_area[0]->AREAC_Descripcion;
        }
        /*Condiciones de venta*/
        $db_data=array(array('cols0'=>'<b>CONDICIONES DE VENTA:</b>', 'cols1'=>''), 
                        array('cols0'=>utf8_decode_seguro('Tipo de Cambio del Día'), 'cols1'=>': '.($tipo_cambio>0 ? round($tipo_cambio,2) : '' )),
                        array('cols0'=>'Moneda', 'cols1'=>': '.$moneda_nombre),
                        array('cols0'=>'Forma de Pago', 'cols1'=>': '.utf8_decode_seguro($forma_pago)),
                        array('cols0'=>'Tiempo de Entrega', 'cols1'=>': '.$tiempo_entrega),
                        array('cols0'=>'Lugar de Entrega', 'cols1'=>': '.utf8_decode_seguro($lugar_entrega)),
                        array('cols0'=>'Contacto', 'cols1'=>': '.utf8_decode_seguro($vendedor_nombre.($vendedor_nombre_area!='' ? ' - AREA: '.$vendedor_nombre_area : '' )))
                        );         
         $this->cezpdf->ezTable($db_data,"","",array(
            'width'=>525,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'fontSize'=>8,
            'cols'=>array(
                'cols0'=>array('width'=>120,'justification'=>'left'),
                'cols1'=>array('width'=>415,'justification'=>'left'),
                )
        ));
         
         $cabecera = array('Content-Type'=>'application/pdf','Content-Disposition'=>$codificacion.'.pdf','Expires'=>'0','Pragma'=>'cache','Cache-Control'=>'private');
        $this->cezpdf->ezStream($cabecera);
    }
    
    public function obtener_detalle_presupuesto($presupuesto){
        $detalle               = $this->presupuestodetalle_model->listar($presupuesto);
        $lista_detalles        = array();
        $datos_presupuesto  = $this->cotizaciones_model->obtener_presupuesto($presupuesto);
        $formapago          = $datos_presupuesto[0]->FORPAP_Codigo;
        $moneda             = $datos_presupuesto[0]->MONED_Codigo;
        $serie             = $datos_presupuesto[0]->PRESUC_Serie;
        $numero             = $datos_presupuesto[0]->PRESUC_Numero;
        $codigo_usuario     = $datos_presupuesto[0]->PRESUC_CodigoUsuario;
        $cliente            = $datos_presupuesto[0]->CLIP_Codigo;
        $tipo_doc            = $datos_presupuesto[0]->PRESUC_TipoDocumento;
        $temp               = $this->obtener_datos_cliente($cliente); 
        $ruc                = $temp['numdoc'];
        $razon_social       = $temp['nombre'];

        if(count($detalle)>0){
            foreach($detalle as $indice=>$valor)
            {
				$detpresup       = $valor->PRESDEP_Codigo;
                $producto        = $valor->PROD_Codigo;
                $unidad_medida   = $valor->UNDMED_Codigo;
                $cantidad        = $valor->PRESDEC_Cantidad;
				$igv100			 = round($valor->PRESDEC_Igv100,2);
                $pu              = round((($tipo_doc == 'F') ? $valor->PRESDEC_Pu : $valor->PRESDEC_Pu_ConIgv - ($valor->PRESDEC_Pu_ConIgv*$igv100/100)),2);
                $subtotal        = round((($tipo_doc == 'F') ? $valor->PRESDEC_Subtotal : $pu*$cantidad),2);
                $igv             = round($valor->PRESDEC_Igv,2);
                $descuento       = round($valor->PRESDEC_Descuento,2);
                $total           = round((($tipo_doc == 'F') ? $valor->PRESDEC_Total : $subtotal),2);
                $pu_conigv       = round($valor->PRESDEC_Pu_ConIgv,2);
                $subtotal_conigv   = round($valor->PRESDEC_Subtotal_ConIgv,2);
                $descuento_conigv  = round($valor->PRESDEC_Descuento_ConIgv,2);
                $observacion     = $valor->PRESDEC_Observacion;
                 
                $datos_producto     = $this->producto_model->obtener_producto($producto);
                $codigo_interno     = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_producto    = ($valor->PRESDEC_Descripcion!='' ? $valor->PRESDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto    =  str_replace('"', "''", $nombre_producto);
                $flagGenInd         = $datos_producto[0]->PROD_GenericoIndividual;
                $costo              = $datos_producto[0]->PROD_CostoPromedio;
                $datos_umedida      = $this->unidadmedida_model->obtener($unidad_medida);
                $nombre_unidad      = $datos_umedida[0]->UNDMED_Simbolo;
                
                
                $objeto   =   new stdClass();
                $objeto->PRESDEP_Codigo      = $detpresup;
                $objeto->PROD_Codigo         = $producto;
                $objeto->PROD_CodigoInterno  = $codigo_interno;
                $objeto->UNDMED_Codigo       = $unidad_medida;
                $objeto->UNDMED_Simbolo      = $nombre_unidad;
                $objeto->PROD_Nombre         = $nombre_producto;
                $objeto->PROD_GenericoIndividual    = $flagGenInd;
                $objeto->PROD_CostoPromedio         = $costo;
                $objeto->PRESDEC_Cantidad    = $cantidad;
                $objeto->PRESDEC_Pu          = $pu;
                $objeto->PRESDEC_Subtotal    = $subtotal;
                $objeto->PRESDEC_Descuento   = $descuento;
                $objeto->PRESDEC_Igv         = $igv;
                $objeto->PRESDEC_Total       = $total;
                $objeto->PRESDEC_Pu_ConIgv   = $pu_conigv;
                $objeto->PRESDEC_Subtotal_ConIgv    = $subtotal_conigv;
                $objeto->PRESDEC_Descuento_ConIgv   = $descuento_conigv;
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
            $objeto->PRESDEP_Codigo      = '';
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
    public function JSON_obtener_presupuesto($presupuesto){
        $datos_presupuesto = $this->cotizaciones_model->obtener_presupuesto($presupuesto);
        echo  json_encode($datos_presupuesto);
    }
    
    function obtener_datos_cliente($cliente, $tipo_docu='F'){
         $datos_cliente     = $this->cliente_model->obtener_datosCliente($cliente);
         $empresa           = $datos_cliente[0]->EMPRP_Codigo;
         $persona           = $datos_cliente[0]->PERSP_Codigo;
         $tipo              = $datos_cliente[0]->CLIC_TipoPersona;
         if($tipo==0){
             $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
             $nombre           = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
             if($tipo_docu!='B')
                $numdoc        = $datos_persona[0]->PERSC_Ruc;
             else
                $numdoc        = $datos_persona[0]->PERSC_NumeroDocIdentidad;
             $direccion        = $datos_persona[0]->PERSC_Direccion;
             $telefono         = $datos_persona[0]->PERSC_Telefono;
             $movil            = $datos_persona[0]->PERSC_Movil;
             $fax              = $datos_persona[0]->PERSC_Fax;
             $email            = $datos_persona[0]->PERSC_Email;
             $contacto         = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno; 
         }
         elseif($tipo==1){
            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre           = $datos_empresa[0]->EMPRC_RazonSocial;
            $numdoc           = $datos_empresa[0]->EMPRC_Ruc;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $movil            = $datos_empresa[0]->EMPRC_Movil;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $email            = $datos_empresa[0]->EMPRC_Email;
            $contacto         = '';
            
            $contactos    = $this->empresa_model->obtener_contactoEmpresa($empresa);
            if(count($contactos)>0){
                $datos_persona = $this->persona_model->obtener_datosPersona($contactos[0]->ECONC_Persona);
                $contacto    = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            }
            
         }
         
         return array('numdoc'=>$numdoc, 'nombre'=>$nombre, 'direccion'=>$direccion ,'telefono'=>$telefono, 'movil'=>$movil, 'fax'=>$fax, 'email'=>$email, 'contacto'=>$contacto);
    }

   
}
?>