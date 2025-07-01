<?php
class Guiain extends CI_Controller
{
    private $_hoy;
    public function __construct()
    {
        parent::Controller();
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/kardex_model');
        $this->load->model('almacen/tipomovimiento_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('almacen/serie_model');
		$this->load->model('almacen/seriemov_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('seguridad/usuario_model');
		
		$this->load->model('maestros/companiaconfidocumento_model');
		$this->load->model('maestros/companiaconfiguracion_model');
		
		
        $this->load->helper('form','url');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->somevar['user']     = $this->session->userdata('user');
        $this->somevar['compania'] = $this->session->userdata('compania');
        date_default_timezone_set('America/Los_Angeles');       
        $this->_hoy                = mdate("%Y-%m-%d %h:%i:%s",time());
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
		
		redirect('almacen/guiain/listar');
	}
	
    public function listar($j=0)
    {
        
		
		/* Cargamos modelos de la compañia actual */
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
		
        $data['registros']  = count($this->guiain_model->buscar_guian($filter));
        $conf['base_url']   = site_url('almacen/guiain/listar/');
        $conf['per_page']   = 30;
        $conf['num_links']  = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link']  = "&gt;&gt;";
        $conf['next_link']  = "&gt;";
        $conf['prev_link']  = "&lt;";
        $conf['uri_segment']= 4;
        $conf['total_rows'] = $data['registros'];
        $offset             = (int)$this->uri->segment(3);
        $listado            = $this->guiain_model->buscar_guian($filter, $conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
		
        if(count($listado) > 0){
             foreach($listado as $indice=>$valor){
				 $fecha          = $valor->CPC_Fecha;
				 $numero         = $valor->GUIAINP_Codigo;
				 $orden          = $valor->OCOMP_Codigo;
				 $almacen        = $valor->ALMAC_Descripcion;
                 $razon          = $valor->EMPRC_RazonSocial;
                 $editar         = "<a href='javascript:;' onclick='editar_guiain(".$numero.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $ver            = "<a href='javascript:;' onclick='ver_guiain_pdf(".$numero.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $eliminar       = "<a href='javascript:;' onclick='eliminar_guiain(".$numero.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $lista[]        = array($item++,$fecha,$numero,$orden,$almacen,$razon,$editar,$ver,$eliminar);
             }
        }
        $data['lista']           = $lista;
		/* Variables de prueba */
        $data['fechai']            = $filter->fechai;
        $data['fechaf']            = $filter->fechaf;
        $data['serie']             = $filter->serie;
        $data['numero']            = $filter->numero;
        $data['codigo_usuario']    = $filter->codigo_usuario;
        $data['cliente']           = $filter->cliente;
        $data['ruc_cliente']       = $filter->ruc_cliente;
        $data['nombre_cliente']    = $filter->nombre_cliente;
        $data['producto']          = $filter->producto;
        $data['situacion']          = $filter->situacion;
        $data['cotizacion']          = $filter->cotizacion;
        $data['pedido']          = $filter->pedido;
        $data['codproducto']       = $filter->codproducto;
        $data['nombre_producto']   = $filter->nombre_producto;
		$data['tipo_codificacion']  = $data_confi_docu[0]->COMPCONFIDOCP_Tipo;
		/* Fin de variables de prueba */
        $data['titulo_busqueda'] = "Buscar Comprobante de Ingreso";
        $data['titulo_tabla']    = "Relaci&oacute;n de Comprobantes de ingreso";
        $this->pagination->initialize($conf);
        $data['paginacion']      = $this->pagination->create_links();
        $this->layout->view('almacen/guiain_index',$data);
			
    }
    public function nueva()
    {
        
        $usuario                = $this->somevar['user'];
        $datos_usuario          = $this->usuario_model->obtener($usuario);
        $nombre_usuario         = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
        $fecha                  = explode(" ",$this->_hoy);
        $data['titulo']         = "Nuevo Comprobante de Ingreso";
        $data['form_open']      = form_open(base_url().'index.php/almacen/guiain/grabar',array("name"=>"frmGuiain","id"=>"frmGuiain","onsubmit"=>"return valida_guiain();"));
        $data['oculto']         = form_hidden(array("base_url"=>base_url(),"guiain_id"=>'',"centro_costo"=>1,"accion"=>"n","GenInd"=>""));
        $data['numero']  	= form_input(array("name"=>"numero","id"=>"numero","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"10"));
        $data['fecha']  	= form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>$fecha[0]));
        $data['nombre_usuario'] = form_input(array("name"=>"nombre_usuario","id"=>"nombre_usuario","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"30","value"=>$nombre_usuario));
        $data['proveedor']        = form_input(array("name"=>"proveedor","id"=>"proveedor","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"30","value"=>"","type"=>"hidden"));
        $data['nombre_proveedor'] = form_input(array("name"=>"nombre_proveedor","id"=>"nombre_proveedor","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"50","value"=>""));
        $data['ruc']              = form_input(array("name"=>"ruc","id"=>"ruc","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"11","value"=>"","onblur"=>"obtener_proveedor();","onkeypress","return numbersonly(this,event,'.');","type"=>"hidden"));
        $data['verproveedor']     = "";
        $data['verproducto']      = "";
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
        $data['cboDocumento']     = form_dropdown("referencia",$this->documento_model->seleccionar(),"10"," class='comboPequeno' id='referencia'");
        $data['cboTipoMov']       = form_dropdown("tipo_movimiento",$this->tipomovimiento_model->seleccionar($filterin),"3"," class='comboMedio' id='tipo_movimiento'");
        $data['cboOcompra']       = form_dropdown("orden_compra",$this->ocompra_model->seleccionar2(),""," class='comboMedio' id='orden_compra' onchange='obtener_detalle_ocompra();'");
        $data['form_close']       = form_close();
        $data['numero_ref']       = form_input(array("name"=>"numero_ref","id"=>"numero_ref","class"=>"cajaPequena","maxlength"=>"20","onkeypress"=>"return numbersonly(this,event,true);"));
        $data['fecha_emision']    = form_input(array("name"=>"fecha_emision","id"=>"fecha_emision","class"=>"cajaPequena","maxlength"=>"10"));
        $data['nombre_transportista'] = form_input(array("name"=>"nombre_transportista","id"=>"nombre_transportista","class"=>"cajaPequena","maxlength"=>"10"));
        $data['ruc_transportista']    = form_input(array("name"=>"ruc_transportista","id"=>"ruc_transportista","class"=>"cajaPequena","maxlength"=>"11","onkeypress"=>"return numbersonly(this,event);"));
        $data['marca_placa']      = form_input(array("name"=>"marca_placa","id"=>"marca_placa","class"=>"cajaPequena","maxlength"=>"10"));
        $data['certificado']      = form_input(array("name"=>"certificado","id"=>"certificado","class"=>"cajaPequena","maxlength"=>"10"));
        $data['licencia']         = form_input(array("name"=>"licencia","id"=>"licencia","class"=>"cajaPequena","maxlength"=>"10"));
        $data['observacion']      = form_textarea(array("name"=>"observacion","id"=>"observacion","class"=>"fuente8","cols"=>"108","rows"=>"3"));
        $this->layout->view('almacen/guiain_nueva',$data);
    }
    public function editar($codigo)
    {
        
        $modo            = "modificar";
        $datos_guiain    = $this->guiain_model->obtener($codigo);
        $tipo_movimiento = $datos_guiain[0]->TIPOMOVP_Codigo;
        $almacen         = $datos_guiain[0]->ALMAP_Codigo;
        $usuario         = $datos_guiain[0]->USUA_Codigo;
        $proveedor       = $datos_guiain[0]->PROVP_Codigo;
        $ocompra         = $datos_guiain[0]->OCOMP_Codigo;
        $referencia	 = $datos_guiain[0]->DOCUP_Codigo;
        $numero_ref      = $datos_guiain[0]->GUIAINC_NumeroRef;
        $numero          = $datos_guiain[0]->GUIAINC_Numero;
        $fecha           = $datos_guiain[0]->GUIAINC_Fecha;
        $fecha_emision   = explode(" ",$datos_guiain[0]->GUIAINC_FechaEmision);
        if($fecha_emision[0]=="0000-00-00") $fecha_emision[0]="";
        $observacion     = $datos_guiain[0]->GUIAINC_Observacion;
        $marca_placa     = $datos_guiain[0]->GUIAINC_MarcaPlaca;
        $certificado     = $datos_guiain[0]->GUIAINC_Certificado;
        $licencia        = $datos_guiain[0]->GUIAINC_Licencia;
        $ruc_transportista    = $datos_guiain[0]->GUIAINC_RucTransportista;
        $nombre_transportista = $datos_guiain[0]->GUIAINC_NombreTransportista;
        $datos_proveedor = $this->proveedor_model->obtener($proveedor);
        $nombre_proveedor= $datos_proveedor->nombre;
        $ruc             = $datos_proveedor->ruc;
        $datos_usuario   = $this->usuario_model->obtener($usuario);
        $nombre_usuario  = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
        $data['titulo']           = "EDITAR COMPROBANTE DE INGRESO";
        $data['form_open']        = form_open(base_url().'index.php/almacen/guiain/grabar',array("name"=>"frmGuiain","id"=>"frmGuiain"));
        $data['oculto']           = form_hidden(array('accion'=>"m",'guiain_id'=>$codigo,'modo'=>$modo,'base_url'=>base_url(),"GenInd"=>'G'));
        $data['numero']  	  = form_input(array("name"=>"numero","id"=>"numero","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"10","value"=>$numero));
        $data['fecha']  	  = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>$fecha));
        $data['nombre_usuario']   = form_input(array("name"=>"nombre_usuario","id"=>"nombre_usuario","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"30","value"=>$nombre_usuario));
        $data['proveedor']        = form_input(array("name"=>"proveedor","id"=>"proveedor","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"30","value"=>"","type"=>"hidden","value"=>$proveedor));
        $data['nombre_proveedor'] = form_input(array("name"=>"nombre_proveedor","id"=>"nombre_proveedor","class"=>"cajaMedia","readonly"=>"readonly","maxlength"=>"50","value"=>$nombre_proveedor));
        $data['ruc']              = form_input(array("name"=>"ruc","id"=>"ruc","class"=>"cajaPequena2","readonly"=>"readonly","maxlength"=>"11","value"=>"","onblur"=>"obtener_proveedor();","onkeypress","return numbersonly(this,event,'.');","type"=>"hidden","value"=>$ruc));
        $data['verproveedor']     = "";
        $data['verproducto']      = "";
        $data['hidden']		  = "style='display:none;'";
        $filterin                 = new stdClass();
        $filterin->TIPOMOVC_Tipo  = 2;
        $data['cboAlmacen']       = form_dropdown("almacen",$this->almacen_model->seleccionar(),$almacen," class='comboMedio' id='almacen'");
        $data['cboDocumento']     = form_dropdown("referencia",$this->documento_model->seleccionar(),$referencia," class='comboPequeno' id='referencia'");
        $data['cboTipoMov']       = form_dropdown("tipo_movimiento",$this->tipomovimiento_model->seleccionar($filterin),$tipo_movimiento," class='comboMedio' id='tipo_movimiento'");
        $data['cboOcompra']       = form_dropdown("orden_compra",$this->ocompra_model->seleccionar($ocompra),$ocompra," class='comboMedio' id='orden_compra' onchange='obtener_detalle_ocompra();'");
        $data['numero_ref']       = form_input(array("name"=>"numero_ref","id"=>"numero_ref","class"=>"cajaPequena","maxlength"=>"20","value"=>$numero_ref,"onkeypress"=>"return numbersonly(this,event,true);"));
        $data['fecha_emision']    = form_input(array("name"=>"fecha_emision","id"=>"fecha_emision","class"=>"cajaPequena","maxlength"=>"10","value"=>$fecha_emision[0]));
        $data['nombre_transportista'] = form_input(array("name"=>"nombre_transportista","id"=>"nombre_transportista","class"=>"cajaPequena","maxlength"=>"10","value"=>$nombre_transportista));
        $data['ruc_transportista']    = form_input(array("name"=>"ruc_transportista","id"=>"ruc_transportista","class"=>"cajaPequena","maxlength"=>"11","value"=>$ruc_transportista,"onkeypress"=>"return numbersonly(this,event);"));
        $data['marca_placa']      = form_input(array("name"=>"marca_placa","id"=>"marca_placa","class"=>"cajaPequena","maxlength"=>"10","value"=>$marca_placa));
        $data['certificado']      = form_input(array("name"=>"certificado","id"=>"certificado","class"=>"cajaPequena","maxlength"=>"10","value"=>$certificado));
        $data['licencia']         = form_input(array("name"=>"licencia","id"=>"licencia","class"=>"cajaPequena","maxlength"=>"10","value"=>$licencia));
        $data['observacion']      = form_textarea(array("name"=>"observacion","id"=>"observacion","class"=>"fuente8","cols"=>"108","rows"=>"3","value"=>$observacion));
        $data['form_close']       = form_close();
        /*Detalle*/
        $detalle               = $this->guiaindetalle_model->obtener2($codigo);
        $detalle_guiain        = array();
        if(count($detalle)>0){
             foreach($detalle as $indice=>$valor)
             {
                $detguiain   = $valor->GUIAINDETP_Codigo;
                $producto    = $valor->PRODCTOP_Codigo;
                $unidad      = $valor->UNDMED_Codigo;
                $cantidad    = $valor->GUIAINDETC_Cantidad;
                $costo        = $valor->GUIAINDETC_Costo;
                $GenInd       = $valor->GUIIAINDETC_GenInd;
                $datos_producto  = $this->producto_model->obtener_producto($producto);
                $datos_unidad    = $this->unidadmedida_model->obtener($unidad);
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                $codigo_interno  = $datos_producto[0]->PROD_CodigoInterno;
                $nombre_unidad   = $datos_unidad[0]->UNDMED_Simbolo;
				print_r($producto);
                if($GenInd=="I"){/*
                    $filter2 = new stdClass();
                    $filter2->SERIC_Guiain = $codigo;  
                    $arrserie = $this->serie_model->obtener($producto,$filter2->SERIC_Guiain);
                    $data2     = array();
					//print_r($filter2->SERIC_Guiain);
                    if(count($arrserie)>0){
					
                        foreach($arrserie as $indice=> $value){
                            $data2[$indice] = $value->SERIC_Numero;
                        }
                    }
                    $_SESSION['serie'][$producto] = $data2;*/
                }
                $objeto          =   new stdClass();
                $objeto->GUIAINDETP_Codigo   = $detguiain;
                $objeto->PROD_Codigo         = $producto;
                $objeto->PROD_CodigoInterno  = $codigo_interno;
                $objeto->GUIAINDETC_Cantidad = $cantidad;
                $objeto->GUIAINDETC_Costo    = $costo;
                $objeto->UNDMED_Codigo       = $unidad;
                $objeto->PROD_Nombre         = $nombre_producto;
                $objeto->UNDMED_Simbolo      = $nombre_unidad;
                $objeto->GenInd              = $GenInd;
                $detalle_guiain[]            = $objeto;
            }
        }
        $data['detalle']                     = $detalle_guiain;
        $this->layout->view('almacen/guiain_nueva',$data);
    }
    public function grabar()
    {   $guiain_id            = $this->input->post("guiain_id");
    
        $this->form_validation->set_rules('nombre_usuario','usuario','required');
        $this->form_validation->set_rules('nombre_proveedor','proveedor','required');
        $this->form_validation->set_rules('almacen','almacen','required');
        $this->form_validation->set_rules('orden_compra','orden de compra','required');
        $this->form_validation->set_rules('tipo_movimiento','motivo de movimiento','required');
        $this->form_validation->set_rules('referencia','documento de referencia','required');
        $this->form_validation->set_rules('GenInd','numeros de serie','required');
        if($this->form_validation->run() == FALSE){
            $this->nueva();
        }
        else{
            
            $almacen              = $this->input->post("almacen");
            $orden_compra         = $this->input->post("orden_compra");
            $proveedor            = $this->input->post("proveedor");
            $referencia           = $this->input->post("referencia");
            $numero_ref           = $this->input->post("numero_ref");
            $tipo_movimiento      = $this->input->post("tipo_movimiento");
            $fecha                = $this->input->post("fecha");
            $fecha_emision        = $this->input->post("fecha_emision");
            $nombre_transportista = $this->input->post("nombre_transportista");
            $ruc_transportista    = $this->input->post("ruc_transportista");
            $marca_placa          = $this->input->post("marca_placa");
            $certificado          = $this->input->post("certificado");
            $licencia             = $this->input->post("licencia");
            $observacion          = $this->input->post("observacion");
            $accion               = $this->input->post("accion");
            $prodcodigo           = $this->input->post('prodcodigo');
            $produnidad           = $this->input->post('produnidad');
            $prodcantidad         = $this->input->post('prodcantidad');
            $prodpu               = $this->input->post('prodpu');
            $prodimporte          = $this->input->post('prodimporte');
            $detaccion            = $this->input->post('detaccion');
            $detguiain            = $this->input->post('detguiain');
            $flagGenInd           = $this->input->post('flagGenInd');
            $detobserv            = "oob";
            $filter = new stdClass();
            $filter->TIPOMOVP_Codigo             = $tipo_movimiento;
            $filter->ALMAP_Codigo                = $almacen;
            $filter->PROVP_Codigo                = $proveedor;
            $filter->OCOMP_Codigo                = $orden_compra;
            $filter->DOCUP_Codigo                = $referencia;
            $filter->GUIAINC_NumeroRef           = $numero_ref;
            $filter->GUIAINC_Fecha               = $fecha;
            $filter->GUIAINC_FechaEmision        = $fecha_emision;
            $filter->GUIAINC_FechaModificacion   = $this->_hoy;
            $filter->GUIAINC_Observacion         = $observacion;
            $filter->GUIAINC_MarcaPlaca          = $marca_placa;
            $filter->GUIAINC_Certificado         = $certificado;
            $filter->GUIAINC_Licencia            = $licencia;
            $filter->GUIAINC_RucTransportista    = $ruc_transportista;
            $filter->GUIAINC_NombreTransportista = $nombre_transportista;
            $filter->USUA_Codigo                 = $this->somevar['user'];
            if($accion=="m"){
                $this->guiaindetalle_model->eliminar2($guiain_id);
                $this->ocompra_model->modificar_detocompra_flagsIngresos($orden_compra);
            }
            if(isset($guiain_id) && $guiain_id>0){
              unset($filter->GUIAINC_Numero);
              $this->guiain_model->modificar($guiain_id,$filter);
            }
            else{
               unset($filter->GUIAINC_FechaModificacion);
               $guiain_id = $this->guiain_model->insertar($filter);
            }
            if(count($prodcodigo)>0){
               foreach($prodcodigo as $indice=>$valor){
                 $producto = $prodcodigo[$indice];
                 $unidad   = $produnidad[$indice];
                 $cantidad = $prodcantidad[$indice];
                 $pu       = $prodpu[$indice];
                 $importe  = $prodimporte[$indice];
                 $accion   = $detaccion[$indice];
                 $detg     = $detguiain[$indice];
                 $detflag  = $flagGenInd[$indice];
                 $observ   = "Insertar";
                 $filter2  = new stdClass();
                 $filter2->GUIAINP_Codigo      = $guiain_id;
                 $filter2->PRODCTOP_Codigo     = $producto;
                 $filter2->UNDMED_Codigo       = $unidad;
                 $filter2->GUIAINDETC_Cantidad = $cantidad;
                 $filter2->GUIAINDETC_Costo    = $pu;
                 $filter2->GUIIAINDETC_GenInd  = $detflag;
                 $filter2->OCOMP_Codigo        = $orden_compra;
                 $this->guiaindetalle_model->insertar($filter2);
                 $this->ocompra_model->modificar_detocompra_flagIngreso($orden_compra,$producto);
               }
               $this->ocompra_model->modificar_ocompra_flagIngreso($orden_compra);
            }
            unset($_SESSION['serie']);//Elimina la serie
            redirect('almacen/guiain/listar');
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
        $datos_guiain         = $this->guiain_model->obtener($codigo);
        $datos_detalle_guiain = $this->guiaindetalle_model->obtener2($codigo);
        $tipo_movimiento = $datos_guiain[0]->TIPOMOVP_Codigo;
		$guiainp=$datos_guiain[0]->GUIAINP_Codigo;
        $almacen         = $datos_guiain[0]->ALMAP_Codigo;
        $usuario         = $datos_guiain[0]->USUA_Codigo;
        $proveedor       = $datos_guiain[0]->PROVP_Codigo;
        $ocompra         = $datos_guiain[0]->OCOMP_Codigo;
        $referencia      = $datos_guiain[0]->DOCUP_Codigo;
        $numero_ref      = $datos_guiain[0]->GUIAINC_NumeroRef;
        $numero          = $datos_guiain[0]->GUIAINC_Numero;
        $fecha_emision   = explode(" ",$datos_guiain[0]->GUIAINC_FechaEmision);
        $observacion     = $datos_guiain[0]->GUIAINC_Observacion;
        $marca_placa     = $datos_guiain[0]->GUIAINC_MarcaPlaca;
        $certificado     = $datos_guiain[0]->GUIAINC_Certificado;
        $licencia        = $datos_guiain[0]->GUIAINC_Licencia;
        $ruc_transportista    = $datos_guiain[0]->GUIAINC_RucTransportista;
        $nombre_transportista = $datos_guiain[0]->GUIAINC_NombreTransportista;
        $datos_almacen    = $this->almacen_model->obtener($almacen);
        $nombre_almacen   = $datos_almacen[0]->ALMAC_Descripcion;
        $arrfecha      = explode(" ",$datos_guiain[0]->GUIAINC_FechaRegistro);
        $fecha         = $arrfecha[0];
        $datos_proveedor = $this->proveedor_model->obtener($proveedor);
        $razon_social    = $datos_proveedor->nombre;
        $ruc             = $datos_proveedor->ruc;
        $telefono        = $datos_proveedor->telefono;
        $direccion       = $datos_proveedor->direccion;
        $fax             = $datos_proveedor->fax;
        $datos_tipomov   = $this->tipomovimiento_model->obtener($tipo_movimiento);
        $nombre_tipomov  = $datos_tipomov[0]->TIPOMOVC_Descripcion;
        $datos_doc       = $this->documento_model->obtener($referencia);
        $nombre_tipodoc  = $datos_doc[0]->DOCUC_Inicial;
        /*Cabecera*/
        $delta=20;
        $this->cezpdf->ezText('','',array("leading"=>120-$delta));
		$this->cezpdf->ezText('<b>COMPROBANTE DE INGRESO No '.$numero.'</b>','13',array("leading"=>10,"left"=>150));
		$this->cezpdf->ezText('<b>FECHA:                  '.$fecha.'</b>','10',array("leading"=>40-$delta,'left'=>350));
		$this->cezpdf->ezText('','',array("leading"=>10));
        $data_cabecera = array(
            array('c1'=>'Senor(es):','c2'=>$razon_social,'c3'=>'Telefono:','c4'=>$telefono),
            array('c1'=>'RUC:','c2'=>$ruc,'c3'=>'Fax:','c4'=>$fax),
            array('c1'=>'Direccion:','c2'=>$direccion,'c3'=>'','c4'=>'')
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
        if(count($datos_detalle_guiain)>0){
             foreach($datos_detalle_guiain as $indice=>$valor){
                $producto       = $valor->PRODCTOP_Codigo;
                $unidad         = $valor->UNDMED_Codigo;
                $costo          = $valor->GUIAINDETC_Costo;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad   = $this->unidadmedida_model->obtener($unidad);
				$prod_cod	 = $datos_producto[0]->PROD_Codigo;
                $prod_nombre    = $datos_producto[0]->PROD_Nombre;
                $prod_codigo    = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad    = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad  = $valor->GUIAINDETC_Cantidad;
                //----SERIE----------------------------------------------------
		
			$datos_serie=$this->seriemov_model->buscar_x_guiainp($guiainp,$prod_cod);   
			$ser="";
			$c=0;
		if(count($datos_serie)>0){ 
		 
            foreach($datos_serie as $indices=>$valor){
			$c+=1;
            $seriecodigo=$valor->SERIC_Numero;
            $ser=$ser." / ".$seriecodigo;
				/*if($c==7){
				
				$this->cezpdf->addText(90+$posicionX +=30, $posicionY + 620, 8, "" .$ser);$posicionY-=10;
				$posicionX -=30;
				$ser="";
				$c=0;
				}*/
			}
			//$this->cezpdf->addText(90+$posicionX +=30, $posicionY + 620, 8, "" .$ser);
 
			//$posicionY-=10;
			} 
			//$posicionX -=30;
				//-----------------------------------------------------
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
        $data_subtotal = array(
            array('cols0'=>$nombre_tipodoc,'cols1'=>$numero_ref,'cols2'=>'Motivo movimiento','cols3'=>$nombre_tipomov,'cols4'=>'Fecha emision','cols5'=>$fecha_emision[0]),
            array('cols0'=>'Nombre Transportista','cols1'=>$nombre_transportista,'cols2'=>'R.U.C. Transportista','cols3'=>$ruc_transportista,'cols4'=>'Vehiculo marca placa','cols5'=>$marca_placa),
            array('cols0'=>'Cert.Inscripcion','cols1'=>$certificado,'cols2'=>'Licencia conducir','cols3'=>$licencia,'cols4'=>'','cols5'=>'')
            );
        $this->cezpdf->ezTable($data_subtotal,"","",array(
            'width'=>555,
            'showLines'=>0,
            'shaded'=>0,
            'showHeadings'=>0,
            'xPos'=>'center',
            'cols'=>array(
                'cols0'=>array('width'=>110,'justification'=>'left'),
                'cols1'=>array('width'=>75,'justification'=>'left'),
                'cols2'=>array('width'=>105,'justification'=>'left'),
                'cols3'=>array('width'=>80,'justification'=>'left'),
                'cols4'=>array('width'=>115,'justification'=>'left'),
                'cols5'=>array('width'=>70,'justification'=>'left')
                )
        ));
        $this->cezpdf->ezText('<b>Observacion(es) :</b>'.$observacion,'10',array('leading'=>18,'left'=>0));
        $this->cezpdf->ezStream();
    }
    public function eliminar()
    {
        $this->load->model("almacen/guiaindetalle_model");
        $id = $this->input->post('codigo');
        $datos_guiain = $this->guiain_model->obtener($id);
        $orden_compra = $datos_guiain[0]->OCOMP_Codigo;
        $this->guiaindetalle_model->eliminar2($id);
        $this->ocompra_model->modificar_detocompra_flagsIngresos($orden_compra);
        $this->ocompra_model->modificar_ocompra_flagIngreso($orden_compra);
        $this->guiain_model->eliminar($id);
        echo true;
    }
    public function cancelar()
    {
        unset($_SESSION['serie']);//Elimina la serie
        redirect('almacen/guiain/listar');
    }
}
?>
