<?php
class Guiasa extends CI_Controller
{
    private $_hoy;
    public function __construct()
    {
        parent::Controller();
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiasadetalle_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/tipomovimiento_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->helper('form','url');
        $this->load->library('pagination');
        $this->load->library('form_validation');
		
		$this->load->model('maestros/companiaconfidocumento_model');
		$this->load->model('maestros/companiaconfiguracion_model');
		
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['compania'] = $this->session->userdata('compania');
        date_default_timezone_set('America/Los_Angeles');          
        $this->_hoy                = mdate("%Y-%m-%d ",time());
    }
	
	
	
	public function limpiar()
	{
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
		$this->session->unset_userdata('situacion');
		$this->session->unset_userdata('cotizacion');
		$this->session->unset_userdata('pedido');
		
		redirect('almacen/guiasa/listar');
	}
	
	
	
    public function listar($j=0)
    {
        
		
		/* Cargamos modelos de la compa�ia actual */
		$data_confi = $this->companiaconfiguracion_model->obtener($this->somevar['compania']);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
		/* Fin de carga de modelos */
		
		$filter = new stdClass();
		
		if(count($_POST)>0){
            $filter->fechai        = $this->input->post('fechai');
            $filter->fechaf        = $this->input->post('fechaf');
            $filter->serie         = $this->input->post('serie');
            $filter->numero        = $this->input->post('numero');
            $filter->codigo_usuario= $this->input->post('codigo_usuario');
            $filter->cliente       = $this->input->post('cliente');
            $filter->ruc_cliente   = $this->input->post('ruc_cliente');
            $filter->nombre_cliente= $this->input->post('nombre_cliente');
            $filter->producto      = $this->input->post('producto');
            $filter->situacion      = $this->input->post('situacion');
            $filter->producto      = $this->input->post('producto');
            $filter->codproducto      = $this->input->post('codproducto');
            $filter->nombre_producto  = $this->input->post('nombre_producto');
            $filter->cotizacion  = $this->input->post('cotizacion');
            $filter->pedido  = $this->input->post('pedido');
            $this->session->set_userdata(array('situacion'=>$filter->situacion,'cotizacion'=>$filter->cotizacion,'pedido'=>$filter->pedido,'fechai'=>$filter->fechai, 'fechaf'=>$filter->fechaf, 'serie'=>$filter->serie, 'numero'=>$filter->numero, 'codigo_usuario'=>$filter->codigo_usuario, 'cliente'=>$filter->cliente, 'ruc_cliente'=>$filter->ruc_cliente, 'nombre_cliente'=>$filter->nombre_cliente, 'producto'=>$filter->producto, 'codproducto'=>$filter->codproducto, 'nombre_producto'=>$filter->nombre_producto));
        }
        else{
            $filter->fechai         = $this->session->userdata('fechai');
            $filter->fechaf         = $this->session->userdata('fechaf');
            $filter->serie          = $this->session->userdata('serie');
            $filter->numero         = $this->session->userdata('numero');
            $filter->codigo_usuario = $this->session->userdata('codigo_usuario');
            $filter->cliente        = $this->session->userdata('cliente');
            $filter->ruc_cliente    = $this->session->userdata('ruc_cliente');
            $filter->nombre_cliente = $this->session->userdata('nombre_cliente');
            $filter->situacion       = $this->session->userdata('situacion');
            $filter->producto       = $this->session->userdata('producto');
            $filter->codproducto    = $this->session->userdata('codproducto');
            $filter->nombre_producto= $this->session->userdata('nombre_producto');
            $filter->cotizacion  = $this->session->userdata('cotizacion');
            $filter->pedido  = $this->session->userdata('pedido');
        }
		
				
		
        $data['registros']  = count($this->guiasa_model->buscar_guiasa($filter));
        $conf['base_url']   = site_url('almacen/guiasa/listar/');
        $conf['per_page']   = 30;
        $conf['num_links']  = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link']  = "&gt;&gt;";
        $conf['next_link']  = "&gt;";
        $conf['prev_link']  = "&lt;";
        $conf['uri_segment']= 4;
        $conf['total_rows'] = $data['registros'];
        $offset             = (int)$this->uri->segment(3);
        $listado            = $this->guiasa_model->buscar_guiasa($filter, $conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
		
		
        if(count($listado) > 0){
             foreach($listado as $indice=>$valor){
				
				 $fecha          = $valor->GUIASAC_Fecha;
				 $numero         = $valor->GUIASAP_Codigo;
				 $almacen        = $valor->ALMAC_Descripcion;
                 $razon          = $valor->EMPRC_RazonSocial;
                 $editar         = "<a href='javascript:;' onclick='editar_guiasa(".$numero.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $ver            = "<a href='javascript:;' onclick='ver_guiasa_pdf(".$numero.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver'></a>";
                 $eliminar       = "<a href='javascript:;' onclick='eliminar_guiasa(".$numero.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                 $lista[]        = array($item++,$fecha,$numero,$almacen,$razon,$editar,$ver,$eliminar);
             }
        }
		
		
		$data['tipo_codificacion']  = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
		$data['fechai']            = $filter->fechai;
        $data['fechaf']            = $filter->fechaf;
        $data['serie']             = $filter->serie;
        $data['numero']            = $filter->numero;
        $data['codigo_usuario']    = $filter->codigo_usuario;
        $data['cliente']           = $filter->cliente;
        $data['ruc_cliente']       = $filter->ruc_cliente;
        $data['nombre_cliente']    = $filter->nombre_cliente;
        $data['producto']          = $filter->producto;
        $data['situacion']         = $filter->situacion;
        $data['codproducto']       = $filter->codproducto;
        $data['nombre_producto']   = $filter->nombre_producto;
        $data['situacion']          = $filter->situacion;
        $data['cotizacion']          = $filter->cotizacion;
        $data['pedido']          = $filter->pedido;
        $data['lista']           = $lista;
        $data['titulo_busqueda'] = "BUSCAR COMPROBANTE DE SALIDA";
        $data['titulo_tabla']    = "Relaci&oacute;n de COMPROBANTE DE SALIDA";
        $this->pagination->initialize($conf);
        $data['paginacion']      = $this->pagination->create_links();
        $this->layout->view('almacen/guiasa_index',$data);
    }
	
	
	
	
	
    public function nueva()
    {
        
        $usuario                  = $this->somevar['user'];
        $datos_usuario            = $this->usuario_model->obtener($usuario);
        $nombre_usuario           = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
        $data['titulo']           = "NUEVO COMPROBANTE DE SALIDA";
        $data['form_open']        = form_open(base_url().'index.php/almacen/guiasa/grabar',array("name"=>"frmGuiasa","id"=>"frmGuiasa","onsubmit"=>"return valida_guiasa();"));
        $data['oculto']           = form_hidden(array("base_url"=>base_url(),"guiasa_id"=>'',"centro_costo"=>1,"accion"=>"n","GenInd"=>""));
        $data['numero']  	  = form_input(array("name"=>"numero","id"=>"numero","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"10"));
        $data['fecha']  	  = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>$this->_hoy));
        $data['nombre_usuario']   = form_input(array("name"=>"nombre_usuario","id"=>"nombre_usuario","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"30","value"=>$nombre_usuario));
        $data['cliente']          = form_input(array("name"=>"cliente","id"=>"cliente","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"30","value"=>"","type"=>"hidden"));
        $data['nombre_cliente']   = form_input(array("name"=>"nombre_cliente","id"=>"nombre_cliente","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"50","value"=>""));
        $data['ruc']              = form_input(array("name"=>"ruc","id"=>"ruc","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"11","value"=>"","onblur"=>"obtener_cliente();","onkeypress","return numbersonly(this,event,'.');","type"=>"hidden"));
        $atributos                = array('width'=>600,'height'=>400,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido                = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar' border='0'>";
        $data['vercliente']       = anchor_popup('empresa/cliente/ventana_busqueda_cliente',$contenido,$atributos);
        $data['verproducto']      = "<a href='#' onclick='busqueda_producto_x_almacen();'>".$contenido."</a>";
        $data['hidden']		  = "";
        $data['detalle']          = array();
        $filterin                 = new stdClass();
        $filterin->TIPOMOVC_Tipo  = 2;
        $data['almacen']          = "";
        $almacen_dafault     = '';
        $lista_almacen=$this->almacen_model->seleccionar();
        if(count($lista_almacen)==1){
            foreach($lista_almacen as $indice=>$value)
                $almacen_dafault=$indice;
        }
        $data['cboAlmacen']       = form_dropdown("almacen",$lista_almacen,$almacen_dafault," class='comboMedio' id='almacen'");
        $data['cboTipoMov']       = form_dropdown("tipo_movimiento",$this->tipomovimiento_model->seleccionar($filterin),"1"," class='comboMedio' id='tipo_movimiento'");
        $data['form_close']       = form_close();
        $data['observacion']      = form_textarea(array("name"=>"observacion","id"=>"observacion","class"=>"fuente8","cols"=>"108","rows"=>"3"));
        $this->layout->view('almacen/guiasa_nueva',$data);
    }
    public function editar($codigo)
    {
        
        $modo            = "modificar";
        $datos_guiasa    = $this->guiasa_model->obtener($codigo);
        $tipo_movimiento = $datos_guiasa->TIPOMOVP_Codigo;
        $almacen         = $datos_guiasa->ALMAP_Codigo;
        $usuario         = $datos_guiasa->USUA_Codigo;
        $cliente         = $datos_guiasa->CLIP_Codigo;
        $numero 	 = $datos_guiasa->GUIASAC_Numero;
        $observacion     = $datos_guiasa->GUIASAC_Observacion;
        $arrfecha        = explode(" ",$datos_guiasa->GUIASAC_FechaRegistro);
        $fecha           = $arrfecha[0];
        $datos_cliente   = $this->cliente_model->obtener($cliente);
        $nombre_cliente  = $datos_cliente->nombre;
        $ruc             = $datos_cliente->ruc;
        $datos_usuario   = $this->usuario_model->obtener($usuario);
        $nombre_usuario  = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
        $data['titulo']           = "EDITAR COMPROBANTE DE SALIDA";
        $data['form_open']        = form_open(base_url().'index.php/almacen/guiasa/grabar',array("name"=>"frmGuiasa","id"=>"frmGuiasa","onsubmit"=>"return valida_guiasa();"));
        $data['oculto']           = form_hidden(array('accion'=>"m",'guiasa_id'=>$codigo,'modo'=>$modo,'base_url'=>base_url(),"GenInd"=>"G"));
        $data['numero']  	  = form_input(array("name"=>"numero","id"=>"numero","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"10","value"=>$numero));
        $data['fecha']  	  = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>$fecha));
        $data['nombre_usuario']   = form_input(array("name"=>"nombre_usuario","id"=>"nombre_usuario","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"30","value"=>$nombre_usuario));
        $data['cliente']          = form_input(array("name"=>"cliente","id"=>"cliente","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"30","value"=>"","type"=>"hidden","value"=>$cliente));
        $data['nombre_cliente']   = form_input(array("name"=>"nombre_cliente","id"=>"nombre_cliente","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"50","value"=>$nombre_cliente));
        $data['ruc']              = form_input(array("name"=>"ruc","id"=>"ruc","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"11","value"=>"","onblur"=>"obtener_cliente();","onkeypress","return numbersonly(this,event);","type"=>"hidden","value"=>$ruc));
        $atributos                = array('width'=>600,'height'=>400,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido                = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar' border='0'>";
        $data['vercliente']       = anchor_popup('empresa/cliente/ventana_busqueda_cliente',$contenido,$atributos);
        $data['verproducto']      = "<a href='#' onclick='busqueda_producto_x_almacen();'>".$contenido."</a>";
        $data['hidden']		  = "";
        $filterin                 = new stdClass();
        $filterin->TIPOMOVC_Tipo  = 2;
        $data['almacen']          = $almacen;
        $data['cboAlmacen']       = form_dropdown("almacen",$this->almacen_model->seleccionar(),$almacen," class='comboMedio' id='almacen'");
        $data['cboTipoMov']       = form_dropdown("tipo_movimiento",$this->tipomovimiento_model->seleccionar($filterin),$tipo_movimiento," class='comboMedio' id='tipo_movimiento'");
        $data['observacion']      = form_textarea(array("name"=>"observacion","id"=>"observacion","class"=>"fuente8","cols"=>"108","rows"=>"3","value"=>$observacion));
        $data['form_close']       = form_close();
        /*Detalle*/
        $detalle               = $this->guiasadetalle_model->obtener2($codigo);
        $detalle_guiasa         = array();
        if(count($detalle)>0){
             foreach($detalle as $indice=>$valor)
             {
                $detguiasa   = $valor->GUIASADETP_Codigo;
                $producto    = $valor->PRODCTOP_Codigo;
                $unidad      = $valor->UNDMED_Codigo;
                $cantidad    = $valor->GUIASADETC_Cantidad;
                $costo       = $valor->GUIASADETC_Costo;
                $GenInd      = $valor->GUIASADETC_GenInd;
                $descri      = $valor->GUIASADETC_Descripcion;
                $datos_producto  = $this->producto_model->obtener_producto($producto);
                $datos_unidad    = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $codigo_interno  = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_unidad   = $datos_unidad[0]->UNDMED_Simbolo;
                if($GenInd=="I"){
                    /*$filter2 = new stdClass();
                    $filter2->SERIC_Guiasa = $codigo;
                    $arrserie = $this->serie_model->obtener($producto,$filter2);
                    $data2     = array();
                    if(count($arrserie)>0){
                        foreach($arrserie as $value){
                            $data2[] = $value->SERIP_Codigo;
                        }
                    }
                    $_SESSION['serie'][$producto] = $data2;*/
                }
                $objeto          =   new stdClass();
                $objeto->GUIASADETP_Codigo    = $detguiasa;
                $objeto->PRODCTOP_Codigo      = $producto;
                $objeto->PROD_CodigoInterno   = $codigo_interno;
                $objeto->GUIASADETC_Cantidad  = $cantidad;
                $objeto->GUIASADETC_Costo     = $costo;
                $objeto->UNDMED_Codigo        = $unidad;
                $objeto->PROD_Nombre          = $nombre_producto;
                $objeto->UNDMED_Simbolo       = $nombre_unidad;
                $objeto->GenInd              = $GenInd;
                $objeto->GUIASADETC_Descripcion              = $descri;
                $detalle_guiasa[]             = $objeto;
            }
        }
        $data['detalle']                     = $detalle_guiasa;
        $this->layout->view('almacen/guiasa_nueva',$data);
    }
    public function grabar()
    {
        $this->form_validation->set_rules('nombre_usuario','usuario','required');
        $this->form_validation->set_rules('nombre_cliente','cliente','required');
        $this->form_validation->set_rules('almacen','almacen','required');
        $this->form_validation->set_rules('tipo_movimiento','motivo de movimiento','required');
        $this->form_validation->set_rules('prodcodigo','detalle de producto','required');
        if($this->form_validation->run() == FALSE){
            $this->nueva();
        }
        else{
            $guiasa_id            = $this->input->post("guiasa_id");
            $almacen              = $this->input->post("almacen");
            $fecha                = $this->input->post("fecha");
            $cliente              = $this->input->post("cliente");
            $numero_ref           = $this->input->post("numero_ref");
            $tipo_movimiento      = $this->input->post("tipo_movimiento");
            $observacion          = $this->input->post("observacion");
            $accion               = $this->input->post("accion");
            $prodcodigo           = $this->input->post('prodcodigo');
            $produnidad           = $this->input->post('produnidad');
            $prodcantidad         = $this->input->post('prodcantidad');
            $prodcosto            = $this->input->post('prodcosto');
            $prodventa            = $this->input->post('prodventa');
            $detaccion            = $this->input->post('detaccion');
            $detguiasa            = $this->input->post('detguiasa');
            $proddescri           = $this->input->post('proddescri');
            $flagGenInd           = $this->input->post('flagGenIndDet');
            $detobserv            = "oob";
            $filter = new stdClass();
            $filter->TIPOMOVP_Codigo = $tipo_movimiento;
            $filter->ALMAP_Codigo    = $almacen;
            $filter->USUA_Codigo     = $this->somevar['user'];
            $filter->CLIP_Codigo     = $cliente;
            $filter->GUIASAC_Observacion   = $observacion;
            $filter->GUIASAC_Fecha         = $fecha;
            $filter->GUIASAC_FechaRegistro = $this->_hoy;
            if($accion=="m"){
                $this->guiasadetalle_model->eliminar2($guiasa_id);
            }
            if(isset($guiasa_id) && $guiasa_id>0){
              unset($filter->GUIASAC_FechaRegistro);
              $this->guiasa_model->modificar($guiasa_id,$filter);
            }
            else{
               $guiasa_id = $this->guiasa_model->insertar($filter);
            }
            if(count($prodcodigo)>0){
               foreach($prodcodigo as $indice=>$valor){
                 $producto = $prodcodigo[$indice];
                 $unidad   = $produnidad[$indice];
                 $cantidad = $prodcantidad[$indice];
                 $costo    = $prodcosto[$indice];
                 $accion   = $detaccion[$indice];
                 $detg     = $detguiasa[$indice];
                 $detflag  = $flagGenInd[$indice];
                 $descri   = $proddescri[$indice];
                 $observ   = "Insertar";
                 $filter2  = new stdClass();
                 $filter2->GUIASAP_Codigo      = $guiasa_id;
                 $filter2->PRODCTOP_Codigo     = $producto;
                 $filter2->UNDMED_Codigo       = $unidad;
                 $filter2->GUIASADETC_Cantidad = $cantidad;
                 $filter2->GUIASADETC_Costo    = $costo;
                 $filter2->GUIASADETC_GenInd   = $detflag;
                 $filter2->GUIASADETC_Descripcion   = $descri;
                 $this->guiasadetalle_model->insertar($filter2);
               }
            }
            unset($_SESSION['serie']);//Elimina la serie
            redirect('almacen/guiasa/listar');
        }
    }
    public function ver()
    {

    }
    public function ver_pdf($codigo){
        $this->load->library('cezpdf');
        $this->load->helper('pdf_helper');
        prep_pdf();
        /*Datos principales*/
        $datos_guiasa         = $this->guiasa_model->obtener($codigo);
        $datos_detalle_guiasa = $this->guiasadetalle_model->obtener2($codigo);
        $tipo_movimiento = $datos_guiasa->TIPOMOVP_Codigo;
        $almacen         = $datos_guiasa->ALMAP_Codigo;
        $usuario         = $datos_guiasa->USUA_Codigo;
		$guiasap		 =$datos_guiasa->GUIASAP_Codigo;
        $cliente         = $datos_guiasa->CLIP_Codigo;
        $numero          = $datos_guiasa->GUIASAC_Numero;
        $observacion     = $datos_guiasa->GUIASAC_Observacion;
        $arrfecha             = explode(" ",$datos_guiasa->GUIASAC_FechaRegistro);
        $datos_almacen        = $this->almacen_model->obtener($almacen);
        $nombre_almacen       = $datos_almacen[0]->ALMAC_Descripcion;
        $fecha                = $arrfecha[0];
        $datos_cliente        = $this->cliente_model->obtener($cliente);
        $razon_social         = $datos_cliente->nombre;
        $ruc                  = $datos_cliente->ruc;
        $telefono             = $datos_cliente->telefono;
        $direccion            = $datos_cliente->direccion;
        $fax                  = $datos_cliente->fax;
        $datos_tipomov        = $this->tipomovimiento_model->obtener($tipo_movimiento);
        $nombre_tipomov       = $datos_tipomov[0]->TIPOMOVC_Descripcion;
        /*Cabecera*/
        $delta=20;
        $this->cezpdf->ezText('','',array("leading"=>120-$delta));
		$this->cezpdf->ezText('<b>COMPROBANTE DE SALIDA No '.$numero.'</b>','13',array("leading"=>10,"left"=>150));
		$this->cezpdf->ezText('<b>FECHA:                  '.$fecha.'</b>','10',array("leading"=>40-$delta,'left'=>350));
		$this->cezpdf->ezText('','',array("leading"=>10));
        $data_cabecera = array(
            array('c1'=>'Senor(es):','c2'=>$razon_social,'c3'=>'Telefono:','c4'=>$telefono),
            array('c1'=>'RUC:','c2'=>$ruc,'c3'=>'Fax:','c4'=>$fax),
            array('c1'=>'Direccion:','c2'=>$direccion,'c3'=>'Mot. Mov.','c4'=>$nombre_tipomov)
            );
        $this->cezpdf->ezTable($data_cabecera,"","",array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'cols'=>array(
                'c1'=>array('width'=>70,'justification'=>'left'),
                'c2'=>array('width'=>355,'justification'=>'left'),
                'c3'=>array('width'=>60,'justification'=>'left'),
                'c4'=>array('width'=>70,'justification'=>'right')
                )
        ));
        $this->cezpdf->ezText('','',array("leading"=>10));
        /*Detalle*/
        if(count($datos_detalle_guiasa)>0){
             foreach($datos_detalle_guiasa as $indice=>$valor){
                $producto       = $valor->PRODCTOP_Codigo;
                $unidad         = $valor->UNDMED_Codigo;
                $costo          = $valor->GUIASADETC_Costo;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad   = $this->unidadmedida_model->obtener($unidad);
				$prod_cod	    = $datos_producto[0]->PROD_Codigo;
                $prod_nombre    = $datos_producto[0]->PROD_Nombre;
                $prod_codigo    = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad    = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad  = $valor->GUIASADETC_Cantidad;
            $datos_serie=$this->seriemov_model->buscar_x_guiasap($guiasap,$prod_cod);   
			$ser="";
			$c=0;
				if(count($datos_serie)>0){ 
		 
            foreach($datos_serie as $indices=>$valor){
			$c+=1;
            $seriecodigo=$valor->SERIC_Numero;
            $ser=$ser." / ".$seriecodigo;
			}
			}
				
                $db_data[] = array(
                    'col1'=>$indice+1,
                    'col2'=>$prod_codigo,
                    'col3'=>$prod_nombre." ".$ser,
                    'col4' =>number_format($prod_cantidad,2),
                    'col5'=>$prod_unidad
                    );
             }
        }

        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Codigo',
            'col3' => 'Descripcion',
            'col4' => 'Cant',
            'col5' => 'Und'
        );
        $this->cezpdf->ezTable($db_data,$col_names, '', array(
            'width'=>550,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPos'=>'center',
            'cols'=>array(
                'col1'=>array('width'=>25,'justification'=>'center'),
                'col2'=>array('width'=>70,'justification'=>'center'),
                'col3'=>array('width'=>355,'justification'=>'left'),
                'col4'=>array('width'=>60,'justification'=>'right'),
                'col5'=>array('width'=>40,'justification'=>'left')
                )
         ));
        /**Pie de pagina**/
        $this->cezpdf->ezSetY(105+$delta);
        $this->cezpdf->ezText('<b>Observacion(es) :</b>'.$observacion,'10',array('leading'=>18,'left'=>0));
        $this->cezpdf->ezStream();
    }
    public function eliminar()
    {
        $codigo = $this->input->post('codigo');
        $this->guiasadetalle_model->eliminar2($codigo);
        $this->guiasa_model->eliminar($codigo);
        echo true;
    }
    public function cancelar()
    {
        unset($_SESSION['serie']);//Elimina la serie
        redirect('almacen/guiasa/listar');
    }
}
?>