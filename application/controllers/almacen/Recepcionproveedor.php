<?php
    class Recepcionproveedor extends CI_Controller{
        public function __construct(){
            parent::Controller();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('almacen/recepcionproveedor_model');
            //$this->load->model('maestros/moneda_model');
            $this->somevar['compania'] = $this->session->userdata('compania');
	    $this->somevar['empresa'] = $this->session->userdata('empresa');
        }
        public function index(){
            $this->load->library('layout','layout'); 
            $this->layout->view('seguridad/inicio');	
        }
        public function listar($j='0'){
            
            $data['registros']  = count($this->recepcionproveedor_model->listar());
            $conf['base_url']   = site_url('almacen/recepcionproveedor/listar/');
            $conf['total_rows'] = $data['registros'];
            $conf['per_page']   = 10;
            $conf['num_links']  = 3;
            $conf['next_link'] = "&gt;";
            $conf['prev_link'] = "&lt;";
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link']  = "&gt;&gt;";
            $conf['uri_segment'] = 4;
            $offset             = (int)$this->uri->segment(4);
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $listado            = $this->recepcionproveedor_model->listar('', $conf['per_page'],$offset);
            $item               = $j+1;
            $lista              = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor)
                {   $codigo = $valor->RECEPRO_Codigo  ;
                    $descripcion = $valor->RECEPRO_Observacion;
                    $fecha = $valor->RECEPRO_FechaRegistro;
                    $tiposolucion = $valor->RECEPRO_TipoSolucion;
                    $empresa = $valor->EMPRC_RazonSocial;
                    $producto = $valor->PROD_Nombre;
                    
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$tiposolucion,$empresa,$producto);
                }
            }
            //$data['listado_moneda']   = $listado_moneda;
            $data['lista']           = $lista;
            $data['titulo_busqueda'] = "BUSCAR RECEPCION ";
            //$data['fecha']  	       = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
            $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>""));
           
			$data['form_open']       = form_open(base_url().'index.php/almacen/recepcionproveedor/buscar',array("name"=>"form_busquedaRecepcionProveedor","id"=>"form_busquedaRecepcionProveedor"));
            $data['form_close']      = form_close();
            $data['titulo_tabla']    = "Relaci&oacute;n DE RECEPCION DEL PROVEEDOR";
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
            $this->layout->view('almacen/recepcionproveedor_index',$data);
        }
        public function buscar($j=0){
            $this->load->library('layout','layout');
            $filtro                = $this->input->post('filtro');
            $data['registros']      = count($this->recepcionproveedor_model->listar($filtro));
			
            $conf['base_url']       = site_url('almacen/almacen/buscar/');
            $conf['per_page']       = 10;
            $conf['num_links']      = 3;
            $conf['first_link']     = "&lt;&lt;";
            $conf['last_link']      = "&gt;&gt;";
            $conf['total_rows']     = $data['registros'];
            $offset                 = (int)$this->uri->segment(4);
            $listado                = $this->recepcionproveedor_model->listar($filtro,$conf['per_page'],$offset);
            $item                   = $j+1;
            $lista                  = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor){   
                    $codigo = $valor->RECEPRO_Codigo  ;
                    $descripcion = $valor->RECEPRO_Observacion;
                    $fecha = $valor->RECEPRO_FechaRegistro;
                    $tiposolucion = $valor->RECEPRO_TipoSolucion;
                    $empresa = $valor->EMPRC_RazonSocial;
                    $producto = $valor->PROD_Nombre;
                    
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$tiposolucion,$empresa,$producto);
                }
            }
            $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de ENVIO PROVEEDOR";
            $data['titulo_busqueda'] = "BUSCAR ENVIO PROVEEDORES";
             $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>$filtro));
            $data['form_open']       = form_open(base_url().'index.php/almacen/recepcionproveedor/buscar',array("name"=>"form_busquedaRecepcionProveedor","id"=>"form_busquedaRecepcionProveedor"));
            $data['form_close']      = form_close();
            $data['lista']           = $lista;
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $this->layout->view('almacen/recepcionproveedor_index',$data);
        }
        public function ver($cod){ 
            
            $listado  = $this->recepcionproveedor_model->obtener_recepcionproveedor($cod);
            $lista    = array();
            $item     = 1;
            if(count($listado)>0){
                foreach($listado as $indice=>$valor){
               $codigo = $valor->RECEPRO_Codigo  ;
                    $descripcion = $valor->RECEPRO_Observacion;
                    $fecha = $valor->RECEPRO_FechaRegistro;
                    $tiposolucion = $valor->RECEPRO_TipoSolucion;
                    $empresa = $valor->EMPRC_RazonSocial;
                    $producto = $valor->PROD_Nombre;
                    
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$tiposolucion,$empresa,$producto);
                }
            }
            $data['lista'] = $lista;
            $data['titulo']= "VER  RECEPCIONES : ";
            $data['oculto']=form_hidden(array('base_url'=>base_url()));	
            $this->layout->view("almacen/recepcionproveedor_ver", $data);
        }
        public function nuevo(){
            
            $data['titulo']           = "REGISTRAR RECEPCION : ";
            $data['garantia'] =$this->input->post("checkGarantia");
            $data['cod']              = '';
            $data['proveedor']        = '';
            $data['cod']     = '';
            $data['nombre_proveedor'] = '';
            $data['serie']              = '';
            $data['numero']        = '';
            $data['padre']     = '';
            $data['codpadre'] = '';
            $data['nompadre'] = '';
            $data['url_action']        = base_url()."index.php/almacen/recepcionproveedor/grabar";
            $data['ruc_proveedor']   = '';
            $data['descripcion']   = '';
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->layout->view('almacen/recepcionproveedor_nuevo',$data);
            
        }
        public function grabar(){
            $compania = $this->somevar['compania'];
	    $empresa = $this->somevar['empresa'];
            $this->form_validation->set_rules("descripcion","descripcion del envio","required");
            $filter = new stdClass();
            if($this->form_validation->run() == false)
                $this->nuevo();
            else{
            $descripcion=$this->input->post("descripcion");
            $proveedor=$this->input->post("proveedor");
            $codpadre=$this->input->post("codpadre");
            $nompadre=$this->input->post("nompadre");
            $serie=$this->input->post("serie");
            $solucion=$this->input->post("solucion");
            $numero=$this->input->post("numero");
            $garantia=$this->input->post("checkGarantia");
            foreach( $garantia as $valores){
            $filter->RECEPRO_Observacion =$descripcion;
            $filter->PROVP_Codigo = $proveedor;	
            $filter->COMPP_Codigo = $compania;
	    $filter->EMPRP_Codigo = $empresa;
            $filter->GARAN_Codigo = $valores;
            $filter->RECEPRO_CodigoProducto  = $codpadre;
            $filter->RECEPRO_NombreProducto  = $nompadre;
            $filter->RECEPRO_TipoSolucion = $solucion;	
            $filter->RECEPRO_NumeroCredito = $numero;
            $filter->RECEPRO_SerieCredito = $serie;
           
            $this->recepcionproveedor_model->insertar($filter,$valores);
           
            }
             $this->listar();
            }     
        }
        public function eliminar_recepcionproveedor(){
            $cod = $this->input->post('cod');
            $this->envioproveedor_model->eliminar_recepcionproveedor($cod);
        }
        public function editar($cod){
            
            $datos =$this->recepcionproveedor_model->obtener_recepcionproveedor($cod);
            $codigo      = $datos[0]->RECEPRO_Codigo;
            $descripcion      = $datos[0]->RECEPRO_Observacion;
            $data['modo']	    = "modificar";
            $data['form_open']      = form_open(base_url().'index.php/almacen/recepcionproveedor/modificar_recepcionproveedor',array("name"=>"form_RecepcionProveedor","id"=>"form_RecepcionProveedor"));
            $data['form_close']     = form_close();
            $data['campo'] = form_input(array('name'=>'descripcion','id'=>'descripcion','value'=>$descripcion,'maxlength'=>'30','class'=>'cajaMedia'));
            $data['descripcion']    = $descripcion;
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $data['cod']     = $codigo;
            $data['titulo']         = "EDITAR RECEPCION PROVEEDOR ::: ";
            $this->layout->view("maestros/actividad_nuevo",$data);
        }
        public function modificar_recepcionproveedor(){
            $this->form_validation->set_rules("descripcion","descripcion de Actividad","required");
            $codigo             = $this->input->post('cod');
            $descripcion        = $this->input->post('descripcion');
            $filter = new stdClass();
            if($this->form_validation->run() == false)
                $this->editar_actividad($codigo);
            else{
                $filter->ACTI_Descripcion=$descripcion;
                $this->actividad_model->modificar_actividad($codigo,$filter);
                $this->listar();                
            }
        }
    }
?>