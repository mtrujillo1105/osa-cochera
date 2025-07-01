<?php
    class Impactousuario extends CI_Controller{
        public function __construct(){
            parent::Controller();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('seguridad/impactousuario_model');
            //$this->load->model('maestros/moneda_model');
            $this->somevar['compania'] = $this->session->userdata('compania');
	    $this->somevar['empresa'] = $this->session->userdata('empresa');
        }
        public function index(){
            $this->load->library('layout','layout'); 
            $this->layout->view('seguridad/inicio');	
        }
        public function listar($j='0'){
            
            $data['registros']  = count($this->impactousuario_model->listar());
            $conf['base_url']   = site_url('seguridad/impactousuario/listar/');
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
            $listado            = $this->impactousuario_model->listar('', $conf['per_page'],$offset);
            $item               = $j+1;
            $lista              = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor)
                {   $codigo = $valor->id   ;
                    $usuario= $valor->usuario;
                    $fecharegistro = $valor->fecharegistro;
                    $lista[]        = array($item++,$codigo,$usuario,$fecharegistro);
                }
            }
            //$data['listado_moneda']   = $listado_moneda;
            $data['lista']           = $lista;
            $data['titulo_busqueda'] = "BUSCAR USUARIO ";
            //$data['fecha']  	       = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
            $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>""));
           
	    $data['form_open']       = form_open(base_url().'index.php/seguridad/impactousuario/buscar',array("name"=>"form_busquedaActividad","id"=>"form_busquedaActividad"));
            $data['form_close']      = form_close();
            $data['titulo_tabla']    = "Relaci&oacute;n DE USUARIOS";
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
            $this->layout->view('seguridad/impactousuario_index',$data);
            
            
            
     
        $data['buscarusuario'] = form_input(array( 'name'  => 'buscarusuario','id' => 'buscarusuario','value' => $usuario,'maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']       = form_open(base_url().'index.php/seguridad/impactousuario/buscar',array("name"=>"form_busquedaUnidadmedida","id"=>"form_busquedaUnidadmedida"));
        $data['form_close']      = form_close();
        $data['lista']           = $lista;
   
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        }
       
//        public function buscar2($j='0'){
//             $this->load->library('layout','layout');
//        $nombre_unidadmedida    = $this->input->post('nombre_unidadmedida');
//        $simbolo                = $this->input->post('simbolo');
//        $filter = new stdClass();
//        $filter->usuario = $nombre_unidadmedida;
//        $filter->fecharegistro     = $simbolo;
//        $data['registros']      = count($this->impactousuario_model->buscar($filter));
//        $conf['base_url']       = site_url('seguridad/impactousuario/buscar/');
//        $conf['total_rows']     = $data['registros'];
//        $conf['per_page']       = 10;
//        $conf['num_links']      = 3;
//        $conf['first_link']     = "&lt;&lt;";
//        $conf['last_link']      = "&gt;&gt;";
//        $offset                 = (int)$this->uri->segment(4);
//        $listado                = $this->impactousuario_model->buscar($filter,$conf['per_page'],$offset);
//        $item                   = $j+1;
//        $lista                  = array();
//        if(count($listado)>0){
//            foreach($listado as $indice=>$valor){
//                $codigo       = $valor->id;
//                $editar       = "<a href='#' onclick='editar_unidadmedida(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
//                $ver          = "<a href='#' onclick='ver_unidadmedida(".$codigo.")' target='_parent'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
//                $eliminar     = "<a href='#' onclick='eliminar_unidadmedida(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
//                $lista[]      = array($item++,$valor->usuario,$valor->fecharegistro,$editar,$ver,$eliminar);
//            }
//        }
//        $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de UNIDADES DE MEDIDA";
//        $data['titulo_busqueda'] = "BUSCAR UNIDAD MEDIDA";
//        $data['nombre_unidadmedida'] = form_input(array( 'name'  => 'nombre_unidadmedida','id' => 'nombre_unidadmedida','value' => $nombre_unidadmedida,'maxlength' => '100','class' => 'cajaMedia'));
//        $data['simbolo']         = form_input(array( 'name'  => 'simbolo','id' => 'simbolo','value' => $simbolo,'maxlength' => '100','class' => 'cajaMedia'));
//        $data['form_open']       = form_open(base_url().'index.php/seguridad/impactousuario/buscar',array("name"=>"form_busquedaUnidadmedida","id"=>"form_busquedaUnidadmedida"));
//        $data['form_close']      = form_close();
//        $data['lista']           = $lista;
//        $data['oculto']          = form_hidden(array('base_url'=>base_url()));
//        $this->pagination->initialize($conf);
//        $data['paginacion'] = $this->pagination->create_links();
//        $this->layout->view('seguridad/impactousuario_index',$data);
//            
//        }
        public function buscar($j='0'){

        $this->load->library('layout','layout');
        $usuario   = $this->input->post('buscarusuario');
        //$password               = $this->input->post('password');
        $filter = new stdClass();
        $filter->usuario  = $usuario;
        //$filter->UNDMED_Simbolo     = $simbolo;
        $data['registros']      = count($this->impactousuario_model->buscar($filter));
        $conf['base_url']       = site_url('seguridad/impactousuario/buscar/');
        $conf['total_rows']     = $data['registros'];
        $conf['per_page']       = 10;
        $conf['num_links']      = 3;
        $conf['first_link']     = "&lt;&lt;";
        $conf['last_link']      = "&gt;&gt;";
        $offset                 = (int)$this->uri->segment(4);
        $data['paginacion'] = $this->pagination->create_links();
        $listado                = $this->impactousuario_model->buscar($filter,$conf['per_page'],$offset);
        $item                   = $j+1;
        $lista                  = array();
        if(count($listado)>0){
            foreach($listado as $indice=>$valor){
                $codigo = $valor->id   ;
                    $usuario= $valor->usuario;
                    $fecharegistro = $valor->fecharegistro;
                    $lista[]        = array($item++,$codigo,$usuario,$fecharegistro);
            }
        }
            $data['lista']           = $lista;
            $data['titulo_busqueda'] = "BUSCAR USUARIO ";
            //$data['fecha']  	       = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
            $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>""));
           
	    $data['form_open']       = form_open(base_url().'index.php/seguridad/impactousuario/buscar',array("name"=>"form_busquedaActividad","id"=>"form_busquedaActividad"));
            $data['form_close']      = form_close();
            $data['titulo_tabla']    = "Relaci&oacute;n DE USUARIOS";
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
            $this->layout->view('seguridad/impactousuario_index',$data);
        }
        public function ver($cod){ 
            
            $listado  = $this->impactousuario_model->obtener_recepcionproveedor($cod);
            $lista    = array();
            $item     = 1;
            if(count($listado)>0){
                foreach($listado as $indice=>$valor){
                    $codigo = $valor->id  ;
                   $nombre = $valor->usuario;
                    $fecha=$valor->fecharegistro;
                    //$tiposolucion = $valor->fechamodificacion;
                    $lista[]        = array($item++,$codigo,$nombre,$fecha);
                }
            }
            $data['lista'] = $lista;
            $data['titulo']= "VER  USUARIO : ";
            $data['oculto']=form_hidden(array('base_url'=>base_url()));	
            $this->layout->view("seguridad/impactousuario_ver", $data);
        }
        public function nuevo(){

            
            $data['titulo']           = "REGISTRAR USUARIO: ";
            $data['id']              = '';
            $data['usuario']        = ''; 
            $data['password']     = '';
            
            $data['url_action']        = base_url()."index.php/seguridad/impactousuario/grabar";
   
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->layout->view('seguridad/impactousuario_nuevo',$data);
            
        }
        public function grabar(){
       
            $this->form_validation->set_rules('usuario','Es necesario insertar un usuario','required');
        //$this->form_validation->set_rules('simbolo','Simbolo','required');
        if($this->form_validation->run() == FALSE){
            $this->nuevo();
        }
        else{
            $usuario  = $this->input->post("usuario");
            $password = $this->input->post("password");
            $id = $this->input->post("id");
            $filter = new stdClass();
            $filter ->id = $id;
            $filter->usuario = strtoupper($usuario);
            $filter->password = $password;
            
               $this->impactousuario_model->insertar($filter);
            
            header("location:".base_url()."index.php/seguridad/impactousuario/listar");
        }
                
        }
          public function grabar2(){
       
            $this->form_validation->set_rules('usuario','Es necesario insertar un usuario','required');
        //$this->form_validation->set_rules('simbolo','Simbolo','required');
        if($this->form_validation->run() == FALSE){
            $this->nuevo();
        }
        else{
            $usuario  = $this->input->post("usuario");
            $password = $this->input->post("password");
            $id = $this->input->post("id");
            $filter = new stdClass();
            //$filter ->id = $id;
            $filter->usuario = strtoupper($usuario);
            $filter->password = $password;
           
              $this->impactousuario_model->modificar($usuario,$filter);
           
            header("location:".base_url()."index.php/seguridad/impactousuario/listar");
        }
                
        }
        
       public function editar($id)
       {
            
            $data['titulo']     = "MODIFICAR USUARIO: ";
            $codigo             = $this->impactousuario_model->obtener($id);
            $data['usuario']    = $codigo[0]->usuario;      
            $data['password']   = '';  
            $data['url_action'] = base_url()."index.php/seguridad/impactousuario/grabar2";
   
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->layout->view('seguridad/impactousuario_nuevo',$data);

    }
        public function eliminar_usuario(){
            $cod = $this->input->post('id');
            $this->impactousuario_model->eliminar_usuario($cod);
        }
        
      
    }
?>