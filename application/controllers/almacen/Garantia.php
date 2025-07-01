<?php
class Garantia extends CI_Controller
{ 
    public function __construct()
    {
        parent::Controller();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('almacen/garantia_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
       
        $this->somevar['empresa'] = $this->session->userdata('empresa');
    }

    public function JSON_buscarFacturaBoletaXserie(){
        $resultado="";
        $cod=$this->input->post('buscarSerie');
        $codigoSeries="0";
      $dataList=  $this->garantia_model->JSON_buscarFacturaBoletaXserie($cod);
        if (count($dataList)>0) {
            foreach ($dataList as $key1 => $value1) {
                $resultado.='<tr>';
                 $lisProducto=$this->garantia_model->getNombreProducto($value1->PROD_Codigo);
                if (count($lisProducto)>0) {
                    foreach ($lisProducto as $key => $value) {
                         
                        //$resultado.='<td>'.$value->PROD_Nombre.'</td>';
                     $resultado.='<td colspan="4"><label>'.$value->PROD_Nombre.'</label></td>';
                      
                    }
                }
                $codigoSeries=$value1->SERIP_Codigo;
                $resultado.='</tr>';
            }
        }
        else{
        $codigoSeries="0";
        $resultado='No Existe la Serie Ingresado';  
        }
        //$resultado.=print_r($value1);
        if($codigoSeries!="0"){
            $SeriesVendidosOcomprados=$this->garantia_model->obtenerSeriesVendidosOcomprados($codigoSeries);
          if(count($SeriesVendidosOcomprados)>0){
            foreach ($SeriesVendidosOcomprados as $key2 => $value2) {
               $resultado.='<tr>';
             if($value2->DOCUP_Codigo==8){
              $listFactura=$this->garantia_model->obtener_facturaVentaCompra($value2->SERDOC_NumeroRef);
                if(count($listFactura)>0){

                    foreach ($listFactura as $key3 => $value3) {
                         
                      //$resultado.=print_r($value3); 

                      if($value3->CPC_TipoOperacion=='V'){
                        $resultado.='<td width="20%">VENTA REALIZADA</td>';
                        $resultado.='<td width="10%">' .$value3->CPC_Numero. '</td>';
                        $buscarCliente=$this->garantia_model->obtener_cliente_data($value3->CLIP_Codigo);
                         
                        if(count($buscarCliente)>0){
                         foreach ($buscarCliente as $key6 => $value6) {
                                $resultado.='<td width="10%">' .$value6->EMPRC_Ruc. '</td>';
                                $resultado.='<td>' .$value6->EMPRC_RazonSocial. '</td>';
                            }   
                        }
                      }
                      if($value3->CPC_TipoOperacion=='C'){
                        $resultado.='<td width="20%">COMPRA REALIZADO</td>';
                        $resultado.='<td width="10%">' .$value3->CPC_Numero. '</td>';
                        $buscarprovee=$this->garantia_model->obtener_proveedor_data($value3->PROVP_Codigo);
                        if(count($buscarprovee)>0){
                         foreach ($buscarprovee as $key7 => $value7) {
                                 $resultado.='<td width="10%">' .$value7->EMPRC_Ruc. '</td>';
                                $resultado.='<td>' .$value7->EMPRC_RazonSocial. '</td>';
                            }   
                        }
                      }
                    }
                }
              
              }
              if ($value2->DOCUP_Codigo==10) {
               $listFactura=$this->garantia_model->obtener_guiaremVentaCompra($value2->SERDOC_NumeroRef);

                if(count($listFactura)>0){
                    foreach ($listFactura as $key4 => $value4) {
                      //$resultado.=print_r($value4); 
                      if($value4->GUIAREMC_TipoOperacion=='V'){
                        $resultado.='<td width="20%">COMPRA REALIZADO</td>';
                        $resultado.='<td width="10%">' .$value3->CPC_Numero. '</td>';
                        $buscarCliente=$this->garantia_model->obtener_cliente_data($value4->CLIP_Codigo);
                        if(count($buscarCliente)>0){
                         foreach ($buscarCliente as $key6 => $value6) {
                                $resultado.='<td width="10%">' .$value7->EMPRC_Ruc. '</td>';
                                $resultado.='<td>' .$value7->EMPRC_RazonSocial. '</td>';
                            }   
                        }
                      }
                      if($value4->GUIAREMC_TipoOperacion=='C'){
                        
                        $buscarprovee=$this->garantia_model->obtener_proveedor_data($value4->PROVP_Codigo);
                        if(count($buscarprovee)>0){
                         foreach ($buscarprovee as $key7 => $value7) {
                                 $resultado.='<td width="10%">' .$value7->EMPRC_Ruc. '</td>';
                                $resultado.='<td>' .$value7->EMPRC_RazonSocial. '</td>';
                            }   
                        }
                      }
                    }
                }
              }
              $resultado.='</tr>';
            }
            
          }
        }
        

        
        echo $resultado;
    }
        public function listar($j='0'){
        
        $data['descripcion_garantia']  = "";
        $data['registros']   = count($this->garantia_model->listar());
        $conf['base_url']    = site_url('almacen/garantia/listar/');
        $conf['total_rows']  = $data['registros'];
        $conf['per_page']    = 50;
        $conf['num_links']   = 3;
        $conf['next_link']   = "&gt;";
        $conf['prev_link']   = "&lt;";
        $conf['first_link']  = "&lt;&lt;";
        $conf['last_link']   = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $offset              = (int)$this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $listado            = $this->garantia_model->listar($conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
        if(count($listado)>0){
            foreach($listado as $indice=>$valor)
            {
                $codigo         = $valor->GARAN_Codigo;
                $checkGarantia  = "<input type='checkbox' id='checkGarantia' name='checkGarantia[]' value='".$codigo."' />";
                $editar         = "<a href='#' onclick='editar_garantia(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='#' onclick='ver_garantia(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver'></a>";
                $eliminar       = "<a href='#' onclick='eliminar_garantia(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[]        = array($checkGarantia, $item++,$valor->EMPRC_RazonSocial,$valor->PROD_Nombre, $valor->GARAN_Descripcion,$valor->GARAN_DescripcionFalla, $valor->GARAN_FechaRegistro,$valor->GARAN_Estado,  $editar, $ver, $eliminar);
            }
        }
        $data['lista']                 = $lista;
        $data['titulo_busqueda']       = "BUSCAR GARANTIA";
        $data['descripcion_garantia']  = form_input(array( 'name'  => 'descripcion_garantia','id' => 'descripcion_garantia','value' => '','maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']             = form_open(base_url().'index.php/almacen/garantia/buscar',array("name"=>"form_busquedaGarantia","id"=>"form_busquedaGarantia"));
        $data['form_close']            = form_close();
        $data['titulo_tabla']          = "Relaci&oacute;n DE GARANTIA";
        $data['url_action']        = "";
      //  $data['url_action']        = base_url()."index.php/almacen/entregacliente/nuevo";

        $data['oculto']                = form_hidden(array('accion'=>"",'codigo'=>"",'modo'=>"insertar",'base_url'=>base_url()));
        $this->layout->view('almacen/garantia_index',$data);
			
    }
	 public function ventana_busqueda_marca($j='0',$limpia=0)
    {
	
		$filter = new stdClass();
        if(count($_POST)>0){
             $data['nombre_marca'] = $this->input->post('txtNombre');     
        }
        if($limpia=='1'){
            $this->session->unset_userdata('nombre_marca');
        }
        if(count($_POST)>0){
            $this->session->set_userdata(array('nombre_marca'=>$data['nombre_marca']));
        }
        else{
            $data['nombre_marca']=$this->session->userdata('nombre_marca');
        }
        
        $filter=new stdClass();
        $filter->MARCC_Descripcion = $data['nombre_marca'];
		
		
        $data['registros']   = count($this->marca_model->buscar($filter));
        $conf['base_url']    = site_url('maestros/marca/ventana_busqueda_marca/');
        $conf['total_rows']  = $data['registros'];
        $conf['per_page']    = 50;
        $conf['num_links']   = 3;
        $conf['next_link']   = "&gt;";
        $conf['prev_link']   = "&lt;";
        $conf['first_link']  = "&lt;&lt;";
        $conf['last_link']   = "&gt;&gt;";

        $conf['uri_segment'] = 4;
        $offset              = (int) $this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $listado            = $this->marca_model->buscar($filter, $conf['per_page'],$j);
        $item               = $j+1;
        $lista              = array();
        if(count($listado)>0){
            foreach($listado as $indice=>$valor)
            {
                $codigo         = $valor->MARCP_Codigo;
				$nombre 		= $valor->MARCC_Descripcion;
				$seleccionar    = '<a href="#" onclick="seleccionar_marca('.$codigo.',\''.$nombre.'\')"><img src="'.base_url().'images/convertir.png"  border="0" title="Seleccionar"></a>';
                $lista[]        = array($checkGarantia, $item++,$valor->CLIP_Codigo,$valor->PROD_Nombre, $valor->GARAN_Descripcion,$valor->GARAN_DescripcionFalla, $valor->GARAN_FechaRegistro,$valor->GARAN_Estado,  $editar, $ver, $eliminar);
            }
        }
		
		$data['nombre']           = $filter->MARCC_Descripcion;
        $data['lista']            = $lista;
        $data['titulo_busqueda']  = "BUSCAR MARCA";
        $data['nombre_marca'] = form_input(array( 'name'  => 'nombre_marca','id' => 'nombre_marca','value' => '','maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']        = form_open(base_url().'index.php/maestros/marca/buscar',array("name"=>"form_busquedaMarca","id"=>"form_busquedaMarca"));
        $data['form_close']       = form_close();
        $data['titulo_tabla']     = "Relaci&oacute;n DE MARCAS";

        $data['oculto']           = form_hidden(array('accion'=>"",'codigo'=>"",'modo'=>"insertar",'base_url'=>base_url()));
        $this->load->view('maestros/marca_ventana_busqueda',$data);
			
    }
	
	
    public function nuevo()
    {
                
        $data['titulo']     = "REGISTRAR GARANTIA";
       // $data['form_open']  = form_open(base_url().'index.php/almacen/garantia/grabar',array("name"=>"frmGarantia","id"=>"frmGarantia"));
        //$data['form_close'] = form_close();
        $data['nombre']     = '';
        $data['registros']  = '';
        $data['paginacion'] = '';
        $data['lista']      = array();        
        $data['cliente']= "";
        //agregué
        $data['ruc_proveedor']       = "";
        $data['nombre_proveedor']    = "";
        $data['url_action']        = base_url()."index.php/almacen/garantia/grabar";

        $data['codpadre']= "";
        $data['nompadre']= "";
        $data['padre']= "";
        $data['codigo_comprobante']= "";     
        $data['codigo_empresa']= "";         
        $data['codigo_compania']= "";        
        $data['descripcion_falla']= "";   
         $data['descripcion_garantia']= ""; 
        $data['nombre_contacto']= "";        
        $data['nextel']= "";                 
        $data['telefono']= ""; 
        $data['comprobante']= "1";   
        $data['fecha']= "";        
        $data['numerofactura']= "";                 
        $data['empresa']= ""; 
        $data['celular']= "";                
        $data['email']= "";                  
        $data['accesorio']= ""; 
        $data['costo']= ""; 
        $data['cod']= "";                   
        $data['comentario']= "";   
        $data['oculto']     = form_hidden(array('codigo'=>"",'base_url'=>base_url(),'garantia_id'=>''));
       // $data['onload']	    = "onload=\"$('#nombres').focus();\"";
        $this->layout->view('almacen/garantia_nuevo',$data);
        
      
    }
    public function editar($id)
    {
        
        $oMarca             = $this->marca_model->getMarca($id);
        $lblDescripcion         = form_label("Nombre Forma pago","Nombre Forma pago");
        $lblCodigoUsuario       = form_label("Código","CodigoUsuario");
        $nombre_marca       = form_input(array( 'name'  => 'nombre_marca','id' => 'nombre_marca','value' => $oMarca[0]->MARCC_Descripcion,'maxlength' => '100','class' => 'cajaMedia'));
        $codigo_usuario         = form_input(array( 'name'  => 'codigo_usuario','id' => 'codigo_usuario','value' => $oMarca[0]->MARCC_CodigoUsuario,'maxlength' => '20','class' => 'cajaPequena'));
        $data['form_open']      = form_open(base_url().'index.php/maestros/marca/grabar/',array("name"=>"frmMarca","id"=>"frmMarca"));
        $data['campos']         = array($lblDescripcion, $lblCodigoUsuario);
        $data['valores']        = array($nombre_marca, $codigo_usuario);
        $data['oculto']         = form_hidden(array('codigo'=>"",'base_url'=>base_url(),'marca_id'=>$id));
        $data['form_hidden']    = form_hidden("base_url",base_url());
        $data['form_close']     = form_close();
        $data['titulo']  = "EDITAR MARCA";
        $this->layout->view('maestros/marca_nueva',$data);
    }
    public function grabar()
    {
        
        $this->form_validation->set_rules('nombre_contacto','Nombre de Contacto','required');
        $this->form_validation->set_rules('descripcion_garantia','Descripcion Garantia','required');
        if($this->form_validation->run() == FALSE){
            $this->nuevo();
        }
        else{
            $compania = $this->somevar['compania'];
	    $empresa = $this->somevar['empresa'];
            //$garantia_id           = $this->input->post("garantia_id");
            $cliente       = $this->input->post("cliente");
            $producto       = $this->input->post("padre");       
            $comprobante    = $this->input->post("comprobante");  
           // $codigo_empresa        = $this->input->post("codigo_empresa");  
          //  $codigo_compania       = $this->input->post("codigo_compania");  
           $descripcion_garantia  = $this->input->post("descripcion_garantia");  
            $nombre_contacto       = $this->input->post("nombre_contacto");  
            $nextel                = $this->input->post("nextel");  
            $telefono              = $this->input->post("telefono");  
            $celular               = $this->input->post("celular");  
            $email                 = $this->input->post("email"); 
            $costo                = $this->input->post("costo");  
            $accesorio             = $this->input->post("accesorio");  
            $descripcion_falla     = $this->input->post("descripcion_falla");  
            $comentario            = $this->input->post("comentario");          
            $filter = new stdClass();
            $filter->CLIP_Codigo = strtoupper($cliente);
            $filter->PROD_Codigo = strtoupper($producto);
            $filter->CPP_Codigo  = strtoupper($comprobante);
            $filter->EMPRP_Codigo  = $empresa;
            $filter->COMPP_Codigo  = $compania;
            $filter->GARAN_Descripcion  = strtoupper($descripcion_garantia);
            $filter->GARAN_Nombrecontacto  = strtoupper($nombre_contacto);
            $filter->GARAN_Nextel  = strtoupper($nextel);
            $filter->GARAN_Telefono  = strtoupper($telefono);
            $filter->GARAN_Celular  = strtoupper($celular);
            $filter->GARAN_Email  = strtoupper($email);
            $filter->GARAN_DescripcionAccesorios  = strtoupper($accesorio);
            $filter->GARAN_DescripcionFalla  = strtoupper($descripcion_falla);
            $filter->GARAN_Comentario  = strtoupper($comentario);
            $filter->GARAN_Estado  = 'Pendiente';
           
            
               $this->garantia_model->insertar($filter);
            
            $this->listar();
        }
    }
    public function eliminar()
    {
        $id = $this->input->post('garantia');
        $this->garantia_model->eliminar($id);
    }
    public function ver($codigo)
    {
        
        $datos_garantia              = $this->garantia_model->obtener($codigo);
        $data['garantia']            = $datos_garantia[0]->GARAN_Codigo;
        $data['cliente']             = $datos_garantia[0]->EMPRC_RazonSocial;
        $data['producto']            = $datos_garantia[0]->PROD_Nombre;
        $data['comprobante']         = $datos_garantia[0]->CPP_Codigo;
        //$data['empresa']             = $datos_garantia[0]->EMPRP_Codigo; 
        
        //$data['compania']            = $datos_garantia[0]->COMPP_Codigo; 
        $data['descripcion']         = $datos_garantia[0]->GARAN_Descripcion;
        $data['contacto']            = $datos_garantia[0]->GARAN_Nombrecontacto; 
        $data['nextel']              = $datos_garantia[0]->GARAN_Nextel;
        $data['telefono']            = $datos_garantia[0]->GARAN_Telefono; 
        $data['celular']             = $datos_garantia[0]->GARAN_Celular; 
        $data['email']               = $datos_garantia[0]->GARAN_Email;
        $data['accesorio']          = $datos_garantia[0]->GARAN_DescripcionAccesorios;
        $data['falla']              = $datos_garantia[0]->GARAN_DescripcionFalla;
        $data['comentario']          = $datos_garantia[0]->GARAN_Comentario;
        $data['fecha_registro']      = $datos_garantia[0]->GARAN_FechaRegistro; 
        $data['estado']              = $datos_garantia[0]->GARAN_Estado;
        $data['titulo']              = "DETALLE GARANTIA";
        $data['oculto']              = form_hidden(array('base_url'=>base_url()));
        $this->layout->view('almacen/garantia_ver',$data);
    }
    public function buscar($j=0)
    {
        $this->load->library('layout','layout');
        $descripcion_garantia = $this->input->post('descripcion_garantia');
        $filter = new stdClass();
        $filter->GARAN_Descripcion = $descripcion_garantia;
        $data['registros']      = count($this->garantia_model->buscar($filter));
        $conf['base_url']       = site_url('almacen/garantia/buscar/');
        $conf['total_rows']     = $data['registros'];
        $conf['per_page']       = 50;
        $conf['num_links']      = 3;
           $data['url_action']        = "";
        $conf['first_link']     = "&lt;&lt;";
        $conf['last_link']      = "&gt;&gt;";
        $offset                 = (int)$this->uri->segment(4);
        $listado                = $this->garantia_model->buscar($filter,$conf['per_page'],$offset);
        $item                   = $j+1;
        $lista                  = array();
        if(count($listado)>0){
            foreach($listado as $indice=>$valor){
                $codigo         = $valor->GARAN_Codigo;
  
                $checkGarantia  = "<input type='checkbox' id=checkGarantia checked=checked  name=checkGarantia value='".$codigo."' />";
                $editar         = "<a href='#' onclick='editar_garantia(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='#' onclick='ver_garantia(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver'></a>";
                $eliminar       = "<a href='#' onclick='eliminar_garantia(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                $lista[]        = array($checkGarantia, $item++,$valor->CLIP_Codigo,$valor->PROD_Nombre, $valor->GARAN_Descripcion,$valor->GARAN_DescripcionFalla, $valor->GARAN_FechaRegistro,$valor->GARAN_Estado,  $editar, $ver, $eliminar);
            }
        }
        $data['titulo_tabla']            = "RESULTADO DE BUSQUEDA DE GARANTIA";
        $data['titulo_busqueda']         = "BUSCAR GARANTIA";
        $data['descripcion_garantia']    = form_input(array( 'name'  => 'descripcion_garantia','id' => 'descripcion_garantia','value' => $descripcion_garantia,'maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']               = form_open(base_url().'index.php/almacen/garantia/buscar',array("name"=>"form_busquedaGarantia","id"=>"form_busquedaGarantia"));
        $data['form_close']              = form_close();
        $data['lista']                   = $lista;
        $data['oculto']                  = form_hidden(array('base_url'=>base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/garantia_index',$data);
    }

	
	
   
}
?>