<?php
    class Envioproveedor extends CI_Controller{
        public function __construct(){
            parent::Controller();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('almacen/envioproveedor_model');
            //$this->load->model('maestros/moneda_model');
            $this->somevar['compania'] = $this->session->userdata('compania');
	    $this->somevar['empresa'] = $this->session->userdata('empresa');
        }
        public function index(){
            $this->load->library('layout','layout'); 
            $this->layout->view('seguridad/inicio');	
        }
        public function listar($j='0'){
            
            $data['registros']  = count($this->envioproveedor_model->listar());
            $conf['base_url']   = site_url('almacen/envioproveedor/listar/');
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
            $listado            = $this->envioproveedor_model->listar('', $conf['per_page'],$offset);
            $item               = $j+1;
            $lista              = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor)
                {   $codigo = $valor->ENVIPRO_Codigo ;
                    $descripcion = $valor->ENVIPRO_Observacion;
                    $fecha = $valor->ENVIPRO_FechaRegistro;
                    $producto= $valor->PROD_Nombre;
                    $proveedor= $valor->EMPRC_RazonSocial;
                    
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$producto,$proveedor);
                }
            }
            //$data['listado_moneda']   = $listado_moneda;
            $data['lista']           = $lista;
            $data['titulo_busqueda'] = "BUSCAR ENVIO ";
            //$data['fecha']  	       = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
            $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>""));
           
	    $data['form_open']       = form_open(base_url().'index.php/almacen/envioproveedor/buscar',array("name"=>"form_busquedaEnvioProveedor","id"=>"form_busquedaEnvioProveedor"));
            $data['form_close']      = form_close();
            $data['titulo_tabla']    = "Relaci&oacute;n DE GARANTIAS ENVIADAS AL PROVEEDOR";
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
            $this->layout->view('almacen/envioproveedor_index',$data);
        }
        public function buscar($j=0){
            $this->load->library('layout','layout');
            $filtro                = $this->input->post('filtro');
            $data['registros']      = count($this->envioproveedor_model->listar($filtro));
			
            $conf['base_url']       = site_url('almacen/almacen/buscar/');
            $conf['per_page']       = 10;
            $conf['num_links']      = 3;
            $conf['first_link']     = "&lt;&lt;";
            $conf['last_link']      = "&gt;&gt;";
            $conf['total_rows']     = $data['registros'];
            $offset                 = (int)$this->uri->segment(4);
            $listado                = $this->envioproveedor_model->listar($filtro,$conf['per_page'],$offset);
            $item                   = $j+1;
            $lista                  = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor){   
                    $codigo = $valor->ENVIPRO_Codigo;
                    $descripcion = $valor->ENVIPRO_Observacion;
                    $fecha = $valor->ENVIPRO_FechaRegistro;
                     $producto= $valor->PROD_Nombre;
                    $proveedor= $valor->EMPRC_RazonSocial;
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$producto,$proveedor);
                }
            }
            $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de ENVIO PROVEEDOR";
            $data['titulo_busqueda'] = "BUSCAR ENVIO PROVEEDORES";
             $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>$filtro));
            $data['form_open']       = form_open(base_url().'index.php/almacen/envioproveedor/buscar',array("name"=>"form_busquedaEnvioProveedor","id"=>"form_busquedaEnvioProveedor"));
            $data['form_close']      = form_close();
            $data['lista']           = $lista;
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $this->layout->view('almacen/envioproveedor_index',$data);
        }
        public function ver($cod){ 
            
            $listado  = $this->envioproveedor_model->obtener_envioproveedor($cod);
            $lista    = array();
            $item     = 1;
            if(count($listado)>0){
                foreach($listado as $indice=>$valor){
                    $codigo       = $valor->ENVIPRO_Codigo;
                    $descripcion  = $valor->ENVIPRO_Observacion;
                    $fecha        = $valor->ENVIPRO_FechaRegistro;
                    $producto       = $valor->PROVP_Codigo;
                    $garantia        = $valor->GARAN_Codigo;
                    $lista[]      = array($item++,$codigo,$descripcion,$fecha,$garantia,$producto);
                }
            }
            $data['lista'] = $lista;
            $data['titulo']= "VER  ENVIO PROVEEDOR : ";
            $data['oculto']=form_hidden(array('base_url'=>base_url()));	
            $this->layout->view("almacen/envioproveedor_ver", $data);
        }
        public function nuevo(){
            
            $data['titulo']           = "REGISTRAR ENVIO PROVEEDOR : ";
            $data['garantia'] =$this->input->post("checkGarantia");
            $data['cod']              = '';
            $data['proveedor']        = '';
            $data['cod']     = '';
            $data['nombre_proveedor'] = '';
            $data['url_action']        = base_url()."index.php/almacen/envioproveedor/grabar";
            $data['ruc_proveedor']   = '';
            $data['descripcion']   = '';
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->layout->view('almacen/envioproveedor_nuevo',$data);
            
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
            $garantia=$this->input->post("checkGarantia");
            foreach( $garantia as $valores){
            $filter->GARAN_Codigo = $valores;
            $filter->ENVIPRO_Observacion = $descripcion;
            $filter->PROVP_Codigo = $proveedor;	
            $filter->COMPP_Codigo = $compania;
	    $filter->EMPRP_Codigo = $empresa;
            
            $this->envioproveedor_model->insertar($filter,$valores);
           
            }
             $this->listar();
            }     
        }
        public function eliminar_envioproveedor(){
            $cod = $this->input->post('cod');
            $this->envioproveedor_model->eliminar_envioproveedor($cod);
        }
        public function editar_envioproveedor($cod){
            
            $datos =$this->envioproveedor_model->obtener_envioproveedor($cod);
            $codigo      = $datos[0]->ENVIPRO_Codigo;
            $descripcion      = $datos[0]->ENVIPRO_Observacion;
            $data['modo']	    = "modificar";
            $data['form_open']      = form_open(base_url().'index.php/almacen/envioproveedor/modificar_envioproveedor',array("name"=>"frmEnvioproveedor","id"=>"frmEnvioproveedor"));
            $data['form_close']     = form_close();
            $data['campo'] = form_input(array('name'=>'descripcion','id'=>'descripcion','value'=>$descripcion,'maxlength'=>'30','class'=>'cajaMedia'));
            $data['descripcion']    = $descripcion;
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $data['cod']     = $codigo;
            $data['titulo']         = "EDITAR ENVIO PROVEEDOR ::: ";
            $this->layout->view("almacen/envioproveedor_editar",$data);
        }
        public function modificar_envioproveedor(){
            $this->form_validation->set_rules("descripcion","descripcion de Envio Proveedor","required");
            $codigo             = $this->input->post('cod');
            $descripcion        = $this->input->post('descripcion');
            $filter = new stdClass();
            if($this->form_validation->run() == false)
                $this->editar_envioproveedor($codigo);
            else{
                $filter->ENVIPRO_Observacion=$descripcion;
                $this->envioproveedor_model->modificar_envioproveedor($codigo,$filter);
                $this->listar();                
            }
        }
    }
?>