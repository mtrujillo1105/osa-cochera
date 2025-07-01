<?php
class Cotizacion extends CI_Controller
{
	public function __construct()
	{
            parent::Controller();
            $this->load->helper('form');
            $this->load->helper('date');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('compras/cotizacion_model');
            $this->load->model('empresa/proveedor_model');
            $this->load->model('maestros/persona_model');
            $this->load->model('empresa/empresa_model');
            $this->load->model('maestros/formapago_model');
            $this->load->model('maestros/condicionentrega_model');
            $this->load->model('compras/pedido_model');
            $this->load->model('maestros/centrocosto_model');
            $this->load->model('maestros/moneda_model');
            $this->load->model('almacen/producto_model');
            $this->load->model('maestros/unidadmedida_model');
            $this->load->model('maestros/almacen_model');
            $this->load->model('seguridad/usuario_model');
            $this->somevar['user'] = $this->session->userdata('user');
            $this->somevar['compania'] = $this->session->userdata('compania');
	}
	public function index()
	{
            
            $this->layout->view('seguridad/inicio');
	}
	public function cotizaciones($j='0')
        {
            
            $data['registros']  = count($this->cotizacion_model->listar_cotizaciones());
            $conf['base_url']   = site_url('compras/cotizacion/cotizaciones/');
            $conf['per_page']   = 50;
            $conf['num_links']  = 3;
            $conf['uri_segment'] = 4;
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link']  = "&gt;&gt;";
            $conf['total_rows'] = $data['registros'];
            $offset             = (int)$this->uri->segment(4);
            $listado_ocompras     = $this->cotizacion_model->listar_cotizaciones($conf['per_page'],$offset);
            $item               = $j+1;
            $lista              = array();
            if(count($listado_ocompras)>0)
            {
                foreach($listado_ocompras as $indice=>$valor){
                $codigo     = $valor->COTIP_Codigo;
                $pedido     = $valor->PEDIP_Codigo;
                $numero     = $valor->COTIC_Numero;
                $proveedor  = $valor->PROVP_Codigo;
                $proveedor  = $valor->PROVP_Codigo;
                $flagOcompra= $valor->COTIC_FlagCompra;
                $arrfecha   = explode(" ",$valor->COTIC_FechaRegistro);
                $fecha      = $arrfecha[0];
                $datos_proveedor = $this->proveedor_model->obtener_datosProveedor($proveedor);
                $datos_pedido    = $this->pedido_model->obtener_pedido($pedido);
                $empresa   = $datos_proveedor[0]->EMPRP_Codigo;
                $persona   = $datos_proveedor[0]->PERSP_Codigo;
                $tipo      = $datos_proveedor[0]->PROVC_TipoPersona;
                $opedido   = $datos_pedido[0]->PEDIC_Numero;
                $ocompra   = $flagOcompra==1?"Si":"Pend";
                if($opedido=='0'){$opedido = "****";}
                if($tipo==0){
                 $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                 $nombre_proveedor = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
                }
                elseif($tipo==1){
                 $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                 $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
                }
                $editar         = "<a href='#' onclick='editar_cotizacion(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='#' onclick='ver_cotizacion_pdf(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar       = "<a href='#' onclick='eliminar_cotizacion(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[]        = array($item++,$fecha,$numero,$opedido,$nombre_proveedor,$ocompra,$editar,$ver,$eliminar);
            }
        }
        $data['titulo_tabla']    = "RELACI&Oacute;N de SOLICITUDES DE COTIZACIONES";
        $data['titulo_busqueda'] = "BUSCAR SOLICITUD DE COTIZACION";
        $data['lista']      = $lista;
        $data['oculto']     = form_hidden(array('base_url'=>base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('compras/cotizacion_index',$data);
    }
    public function nueva_cotizacion()
    {
        
        $modo                        = "";
        $accion                      = "";
        $modo                        = "insertar";
        $codigo                      = "";
        $usuario                     = $this->somevar['user'];
        $datos_usuario               = $this->usuario_model->obtener($usuario);
        $data['nombre_usuario']      = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
        $oculto                      = form_hidden(array('accion'=>$accion,'codigo'=>$codigo,'modo'=>$modo,'base_url'=>base_url()));
        $data['url_action']          = base_url()."index.php/compras/cotizacion/insertar_cotizacion";
        $data['titulo']              = "REGISTRAR SOLICITUDES DE COTIZACIONES";
        $data['formulario']          = "frmCotizacion";
        $data['oculto']              = $oculto;
        $data['onload']	             = "onload=\"$('#nombre').focus();\"";
        $data['cboMoneda']           = $this->seleccionar_moneda('1');
        $data['cboFormaPago']        = $this->seleccionar_forma_pago('1');
        $data['cboCondicionEntrega'] = $this->seleccionar_condicion_entrega('1');
        $data['cboLugarEntrega']     = $this->seleccionar_lugar_entrega('1');
        $data['cboPedido']           = $this->seleccionar_pedido('0');
        $data['numero']              = "";
        $data['ruc']                 = "";
        $data['nombre_proveedor']    = "";
        $data['proveedor']           = "";
        $data['detalle_cotizacion']  = array();
        $data['observacion']         = "";
        $data['focus']               = "";
        $data['hoy']                 = mdate("%Y-%m-%d ",time());
        $atributos                   = array('width'=>600,'height'=>400,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
        $contenido                   = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar' border='0'>";
        $data['verproveedor']	     = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor',$contenido,$atributos);
        $data['verproducto']	     = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos);
        $this->layout->view('compras/cotizacion_nueva',$data);
    }
    public function insertar_cotizacion()
    {
         $this->form_validation->set_rules('nombre_proveedor','Nombre Proveedor','required');
         $this->form_validation->set_rules('nombre_usuario','Nombre Usuario','required');
         $this->form_validation->set_rules('lugar_entrega','Lugar de entrega','required');
         $this->form_validation->set_rules('forma_pago','Forma de pago','required');
         $this->form_validation->set_rules('condicion_entrega','Condicion de entrega','required');
         $this->form_validation->set_rules('prodcodigo','Detalle de Productos','callback_prodproducto_check');
         if($this->form_validation->run()==FALSE){
               $this->nueva_cotizacion();
         }
         else{
              $proveedor          = $this->input->post('proveedor');
              $pedido                 = $this->input->post('pedido');
              $forma_pago       = $this->input->post('forma_pago');
              $lugar_entrega   = $this->input->post('lugar_entrega');
              $condicion_entrega  = $this->input->post('condicion_entrega');
              $prodcodigo        = $this->input->post('prodcodigo');
              $prodcantidad    = $this->input->post('prodcantidad');
              $produnidad       = $this->input->post('produnidad');
              $observacion      = $this->input->post('observacion');
              $numero               = "";//Se genera automatico y en forma correlativa.
              $detobserv          = "oob";
              $centrocosto      = "1";
              $cotizacion         = $this->cotizacion_model->insertar_cotizacion($pedido,$numero,$proveedor,$forma_pago,$condicion_entrega,$lugar_entrega,$centrocosto,$observacion);
              if(count($prodcodigo)>0){
                   foreach($prodcodigo as $indice=>$valor){
                        $producto = $prodcodigo[$indice];
                        $cantidad  = $prodcantidad[$indice];
                        $unidad     = $produnidad[$indice];
                        $observ      = "";
                         $this->cotizacion_model->insertar_detalle_cotizacion($cotizacion,$pedido,$producto,$cantidad,$unidad,$observ);
                   }
              }
              header("location:".base_url()."index.php/compras/cotizacion/cotizaciones");
         }
	}
	public function editar_cotizacion($codigo)
	{
            
            $accion              = "";
            $modo                = "modificar";
            $datos_cargo         = $this->cotizacion_model->obtener_cotizacion($codigo);
            $pedido              = $datos_cargo[0]->PEDIP_Codigo;
            $numero              = $datos_cargo[0]->COTIC_Numero;
            $proveedor           = $datos_cargo[0]->PROVP_Codigo;
            $forma_pago          = $datos_cargo[0]->FORPAP_Codigo;
            $condiciones_entrega = $datos_cargo[0]->CONENP_Codigo;
            $centro_costo        = $datos_cargo[0]->CENCOSP_Codigo;
            $almacen             = $datos_cargo[0]->ALMAP_Codigo;
            $observacion         = $datos_cargo[0]->COTIC_Observacion;
            $tiempo_oferta       = $datos_cargo[0]->COTIC_TiempoOferta;
            $fecha               = explode(" ",$datos_cargo[0]->COTIC_FechaRegistro);
            $fecha               = $fecha[0];
            $flagCompra          = $datos_cargo[0]->COTIC_FlagCompra;
            $flagIngreso         = $datos_cargo[0]->COTIC_FlagIngreso;
            $datos_proveedor     = $this->proveedor_model->obtener_datosProveedor($proveedor);
            $empresa             = $datos_proveedor[0]->EMPRP_Codigo;
            $persona             = $datos_proveedor[0]->PERSP_Codigo;
            $tipo                = $datos_proveedor[0]->PROVC_TipoPersona;
            $usuario             = $datos_cargo[0]->USUA_Codigo;
            if($tipo==0){
               $datos_persona = $this->persona_model->obtener_datosPersona($persona);
               $nombre_proveedor = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
               $ruc  = $datos_persona[0]->PERSC_Ruc;
           }
            elseif($tipo==1){
               $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
               $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
               $ruc  = $datos_empresa[0]->EMPRC_Ruc;
            }
            
            $datos_usuario            = $this->usuario_model->obtener($usuario);
            $data['nombre_usuario']   = $datos_usuario->PERSC_Nombre." ".$datos_usuario->PERSC_ApellidoPaterno;
            $data['cboPedido']                        = $this->seleccionar_pedido($pedido);
            $data['cboFormaPago']                = $this->seleccionar_forma_pago($forma_pago);
            $data['cboCondicionEntrega']  = $this->seleccionar_condicion_entrega($condiciones_entrega);
            $data['cboLugarEntrega']            = $this->seleccionar_lugar_entrega($almacen);
            $data['numero']             = $numero;
            $data['ruc']                      = $ruc;
            $data['nombre_proveedor'] = $nombre_proveedor;
            $oculto                              = form_hidden(array('accion'=>$accion,'codigo'=>$codigo,'modo'=>$modo,'base_url'=>base_url()));
            $data['titulo']                 = "EDITAR SOLICITUD DE COTIZACION";
            $data['formulario']      = "frmCotizacion";
            $data['oculto']               = $oculto;
            $data['onload']		         = "onload=\"\"";
            $data['url_action']        = base_url()."index.php/compras/cotizacion/modificar_cotizacion";
            $atributos                  = array('width'=>600,'height'=>400,'scrollbars'=>'yes','status'=>'yes','resizable'=>'yes','screenx'=>'0','screeny'=>'0');
            $contenido                = "<img height='16' width='16' src='".base_url()."images/ver.png' title='Buscar' border='0'>";
            $data['verproveedor'] = anchor_popup('empresa/proveedor/ventana_busqueda_proveedor',$contenido,$atributos);
            $data['verproducto']	  = anchor_popup('almacen/producto/ventana_busqueda_producto',$contenido,$atributos);
            $data['hoy']                      = $fecha;
            $data['proveedor']        = $proveedor;
            $data['observacion']     = $observacion;
            $data['focus']                   = "javascript:this.blur();return false;";
            $detalle                             = $this->cotizacion_model->obtener_detalle_cotizacion($codigo);
            $detalle_cotizacion      = array();
            if(count($detalle)>0){
                 foreach($detalle as $indice=>$valor){
                      $detcotiz   = $valor->COTDEP_Codigo;
                       $producto = $valor->PROD_Codigo;
                       $cantidad  = $valor->COTDEC_Cantidad;
                       $unidad     = $valor->UNDMED_Codigo;
                       $datos_producto      = $this->producto_model->obtener_producto($producto);
                       $datos_unidad           = $this->unidadmedida_model->obtener($unidad);
                       $nombre_producto = $datos_producto[0]->PROD_Nombre;
                       $codigo_interno       = $datos_producto[0]->PROD_CodigoInterno;
                      $nombre_unidad      = $datos_unidad[0]->UNDMED_Simbolo;
                      $objeto   =   new stdClass();
                      $objeto->COTDEP_Codigo             = $detcotiz;
                      $objeto->PROD_Codigo                  = $producto;
                      $objeto->PROD_CodigoInterno  = $codigo_interno;
                      $objeto->COTDEC_Cantidad         = $cantidad;
                      $objeto->UNDMED_Codigo           = $unidad;
                      $objeto->PROD_Nombre               = $nombre_producto;
                      $objeto->UNDMED_Simbolo = $nombre_unidad;
                      $detalle_cotizacion[]                       = $objeto;
                  }
            }
            $data['detalle_cotizacion']                = $detalle_cotizacion;
            $this->layout->view('compras/cotizacion_nueva',$data);
	}
    public function modificar_cotizacion()
    {
        $codigo           = $this->input->post('codigo');
        $this->form_validation->set_rules('nombre_proveedor','Nombre Proveedor','required');
        $this->form_validation->set_rules('nombre_usuario','Nombre Usuario','required');
        $this->form_validation->set_rules('lugar_entrega','Lugar de entrega','required');
        $this->form_validation->set_rules('forma_pago','Forma de pago','required');
        $this->form_validation->set_rules('condicion_entrega','Condicion de entrega','required');
        $this->form_validation->set_rules('prodcodigo','Detalle de Productos','callback_prodproducto_check');
        if($this->form_validation->run() == FALSE){
            $this->editar_cotizacion($codigo);
        }
        else{
            $pedido            = $this->input->post('pedido');
            $proveedor         = $this->input->post('proveedor');
            $forma_pago        = $this->input->post('forma_pago');
            $lugar_entrega     = $this->input->post('lugar_entrega');
            $condicion_entrega = $this->input->post('condicion_entrega');
            $prodcodigo        = $this->input->post('prodcodigo');
            $prodcantidad      = $this->input->post('prodcantidad');
            $produnidad        = $this->input->post('produnidad');
            $detaccion         = $this->input->post('detaccion');
            $detcotiz          = $this->input->post('detcotiz');
            $observacion       = $this->input->post('observacion');
            $this->cotizacion_model->modificar_cotizacion($codigo,$proveedor,$forma_pago,$lugar_entrega,$condicion_entrega,$observacion);
            if(count($detcotiz)>0){
                foreach($detcotiz as $indice=>$valor){
                  $detalle_cotizacion = $detcotiz[$indice];
                  $detalle_accion        = $detaccion[$indice];
                  $detalle_producto  = $prodcodigo[$indice];
                  $detalle_cantidad   = $prodcantidad[$indice];
                  $detalle_unidad      = $produnidad[$indice];
                  if($detalle_accion=='n'){
                          $this->cotizacion_model->insertar_detalle_cotizacion($codigo,$pedido,$detalle_producto,$detalle_cantidad,$detalle_unidad,'Insertado');
                  }elseif($detalle_accion=='m') {
                          $this->cotizacion_model->modificar_producto_cotizacion($detalle_cotizacion,$detalle_cantidad,'Modificado');
                  }elseif($detalle_accion=='e'){
                          $this->cotizacion_model->eliminar_producto_cotizacion($detalle_cotizacion);
                  }
                }
            }
            $this->cotizaciones();
        }
    }
    public function eliminar_cotizacion()
    {
        $codigo = $this->input->post('codigo');
        $this->cotizacion_model->eliminar_cotizacion($codigo);
    }
    public function eliminar_producto_cotizacion()
    {
         $producto = $this->input->post('producto');
         echo $producto;
    }
    public function ver_cotizacion()
    {

    }
    public function ver_cotizacion_pdf($codigo)
    {
        /*Datos principales*/
        $datos_cotizacion         = $this->cotizacion_model->obtener_cotizacion($codigo);
        $datos_detalle_cotizacion = $this->cotizacion_model->obtener_detalle_cotizacion($codigo);
        $pedido        = $datos_cotizacion[0]->PEDIP_Codigo;
        $numero        = $datos_cotizacion[0]->COTIC_Numero;
        $serie         = $datos_cotizacion[0]->COTIC_Serie;
        $proveedor     = $datos_cotizacion[0]->PROVP_Codigo;
        $formapago     = $datos_cotizacion[0]->FORPAP_Codigo;
        $condentrega   = $datos_cotizacion[0]->CONENP_Codigo;
        $centro_costo  = $datos_cotizacion[0]->CENCOSP_Codigo;
        $almacen       = $datos_cotizacion[0]->ALMAP_Codigo;
        $observacion   = $datos_cotizacion[0]->COTIC_Observacion;
        $datos_almacen      = $this->almacen_model->obtener($almacen);
        $datos_formapago    = $this->formapago_model->obtener($formapago);
        $datos_condentrega  = $this->condicionentrega_model->obtener($condentrega);
        $nombre_almacen     = $datos_almacen[0]->ALMAC_Descripcion;
        $nombre_formapago   = $datos_formapago[0]->FORPAC_Descripcion;
        $nombre_condentrega = $datos_condentrega[0]->CONENC_Descripcion;
        $arrfecha           = explode(" ",$datos_cotizacion[0]->COTIC_FechaRegistro);
        $fecha              = $arrfecha[0];
        $flagOcompra        = $datos_cotizacion[0]->COTIC_FlagCompra;
        $datos_proveedor    = $this->proveedor_model->obtener_datosProveedor($proveedor);
        $empresa            = $datos_proveedor[0]->EMPRP_Codigo;
        $persona            = $datos_proveedor[0]->PERSP_Codigo;
        $tipo               = $datos_proveedor[0]->PROVC_TipoPersona;
        if($tipo==0){
            $datos_persona    = $this->persona_model->obtener_datosPersona($persona);
            $nombre_proveedor = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
            $ruc              = $datos_persona[0]->PERSC_Ruc;
            $telefono         = $datos_persona[0]->PERSC_Telefono;
            $direccion        = $datos_persona[0]->PERSC_Direccion;
            $fax              = "ss";
        }
        elseif($tipo==1){
            $datos_empresa    = $this->empresa_model->obtener_datosEmpresa($empresa);
            $nombre_proveedor = $datos_empresa[0]->EMPRC_RazonSocial;
            $ruc              = $datos_empresa[0]->EMPRC_Ruc;
            $telefono         = $datos_empresa[0]->EMPRC_Telefono;
            $fax              = $datos_empresa[0]->EMPRC_Fax;
            $emp_direccion    = $this->empresa_model->obtener_establecimientosEmpresa_principal($empresa);
            $direccion        = $emp_direccion[0]->EESTAC_Direccion;
        }
        /*Cabecera*/
        $this->load->library('cezpdf');
        $this->load->helper('pdf_helper');
        prep_pdf();
        $delta=20;
		$this->cezpdf->ezText('','',array("leading"=>130-$delta));
        $this->cezpdf->ezText('<b>COTIZACION N '.$numero.'</b>','13',array("leading"=>50-$delta,'left'=>190));
        $this->cezpdf->ezText('<b>FECHA:                  '.$fecha.'</b>','10',array("leading"=>40-$delta,'left'=>350));
        //$img=base_url()."images/2.jpg";
        $options = array("leading"=>15,"left"=>35);
		//$this->cezpdf->addJpegFromFile($img,10,10);
        $this->cezpdf->ezText('<b>Senor(es) :</b>'.$nombre_proveedor,'10',$options);
        $this->cezpdf->ezText('<b>Direccion :</b>'.$direccion,'10',$options);
        $this->cezpdf->ezText('<b>Atte Sr(a) :</b>','10',$options);
        $this->cezpdf->ezText("Nos es grato saludarlos y les hacemos llegar nuestra cotizacion de acuerdo a lo solicitado.",'10',$options);
        $this->cezpdf->ezText('','10');
        /*Detalle*/
        if(count($datos_detalle_cotizacion)>0){
             foreach($datos_detalle_cotizacion as $indice=>$valor){
                $producto       = $valor->PROD_Codigo;
                $unidad         = $valor->UNDMED_Codigo;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $datos_unidad   = $this->unidadmedida_model->obtener($unidad);
                $prod_nombre    = $datos_producto[0]->PROD_Nombre;
                $prod_codigo    = $datos_producto[0]->PROD_CodigoInterno;
                $prod_unidad    = $datos_unidad[0]->UNDMED_Simbolo;
                $prod_cantidad  = $valor->COTDEC_Cantidad;
                $db_data[] = array(
                    'col1'=>$indice+1,
                    'col2'=>$prod_codigo,
                    'col3'=>$prod_nombre,
                    'col4'=>$prod_unidad,
                    'col5' =>$prod_cantidad
                    );
             }
        }
        $col_names = array(
            'col1' => 'Itm',
            'col2' => 'Codigo',
            'col3' => 'Descripcion',
            'col4' => 'Und',
            'col5' => 'Cant.'
        );
        $this->cezpdf->ezTable($db_data,$col_names,'', array(
            'width'=>450,
            'showLines'=>1,
            'shaded'=>0,
            'showHeadings'=>1,
            'xPosition'=>'right',
            'fontSize' => 8,
            'titleFontSize'=>12,
            'cols'=>array(
                'col1'=>array('width'=>30,'justification'=>'center'),
                'col2'=>array('width'=>80,'justification'=>'center'),
                'col3'=>array('width'=>255,'justification'=>'left'),
                'col4'=>array('width'=>35,'justification'=>'center'),
                'col5'=>array('width'=>40,'justification'=>'right'),
		)
        ));
        /**Firma**/
        $positionx = 85;
        $positiony = 250+$delta;
        $this->cezpdf->addText($positionx,$positiony,10,"Esperando que la presente sea de su conformidad, quedamos a la espera de sus ordenes.");
        $this->cezpdf->addText($positionx,$positiony-40,10,"ATENTAMENTE.");
        $this->cezpdf->addText($positionx,$positiony-80,10,"Marleni Morante.");
        $this->cezpdf->ezStream(array('Content-Disposition'=>'nama_file.pdf'));
    }
    public function buscar_cotizacion(){

    }
    public function prodproducto_check($arrproducto){
        if(count($arrproducto)==0){
          $this->form_validation->set_message('prodproducto_check','El detalle de productos no puede estar vacio.');
          return false;
        }else{
          if(count($arrproducto)!=count(array_unique($arrproducto))){
                $this->form_validation->set_message('prodproducto_check','El detalle no puede tener productos repetidos.');
                return false;
          }else{
               return true;
          }
        }
    }
    public function seleccionar_pedido($indSel=''){
        $array_pedido = $this->pedido_model->listar_pedidos();
        $arreglo = array();
        foreach($array_pedido as $indice=>$valor){
            $ccosto    = $valor->CENCOST_Codigo;
            $datos_ccosto      = $this->centrocosto_model->obtener_centro_costo($ccosto);
            $nombre_ccosto =  $datos_ccosto[0]->CENCOSC_Descripcion;
            $indice1   = $valor->PEDIP_Codigo;
            $valor1    = $valor->PEDIC_Numero;
            if($valor1==0) $valor1='***';
                $arreglo[$indice1] = $valor1." :: ".$nombre_ccosto;
        }
        $resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione ::'));
        return $resultado;
    }
	public function seleccionar_forma_pago($indSel=''){
		$array_rol = $this->formapago_model->listar();
		$arreglo = array();
		foreach($array_rol as $indice=>$valor){
			$indice1   = $valor->FORPAP_Codigo;
			$valor1    = $valor->FORPAC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
		return $resultado;
	}
	public function seleccionar_condicion_entrega($indSel=''){
		$array_rol = $this->condicionentrega_model->listar();
		$arreglo = array();
		foreach($array_rol as $indice=>$valor){
			$indice1   = $valor->CONENP_Codigo;
			$valor1    = $valor->CONENC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
		return $resultado;
	}
	public function seleccionar_lugar_entrega($indSel=''){
		$array_rol = $this->almacen_model->listar($this->somevar['compania']);
		$arreglo = array();
		foreach($array_rol as $indice=>$valor){
			$indice1   = $valor->ALMAP_Codigo;
			$valor1    = $valor->ALMAC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
		return $resultado;
	}
	public function seleccionar_moneda($indSel=''){
		$array_rol = $this->moneda_model->listar();
		$arreglo = array();
		foreach($array_rol as $indice=>$valor){
			$indice1   = $valor->MONED_Codigo;
			$valor1    = $valor->MONED_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		$resultado = $this->html->optionHTML($arreglo,$indSel,array('','::Seleccione::'));
		return $resultado;
	}
}
?>