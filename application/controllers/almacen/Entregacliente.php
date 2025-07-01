<?php
    class Entregacliente extends CI_Controller{
        public function __construct(){
            parent::Controller();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('almacen/entregacliente_model');
            //$this->load->model('maestros/moneda_model');
            $this->somevar['compania'] = $this->session->userdata('compania');
	    $this->somevar['empresa'] = $this->session->userdata('empresa');
        }
        public function index(){
            $this->load->library('layout','layout'); 
            $this->layout->view('seguridad/inicio');	
        }
        public function listar($j='0'){
            
            $data['registros']  = count($this->entregacliente_model->listar());
            $conf['base_url']   = site_url('almacen/entregacliente/listar/');
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
            $listado            = $this->entregacliente_model->listar('', $conf['per_page'],$offset);
            $item               = $j+1;
            $lista              = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor)
                {   $codigo = $valor->ENTRECLI_Codigo   ;
                    $descripcion = $valor->ENTRECLI_Observacion;
                    $fecha = $valor->ENTRECLI_FechaRegistro;
                    $tiposolucion = $valor->ENTRECLI_TipoSolucion;
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$tiposolucion);
                }
            }
            //$data['listado_moneda']   = $listado_moneda;
            $data['lista']           = $lista;
            $data['titulo_busqueda'] = "BUSCAR RECEPCION ";
            //$data['fecha']  	       = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
            $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>""));
           
	    $data['form_open']       = form_open(base_url().'index.php/almacen/entregacliente/buscar',array("name"=>"form_busquedaActividad","id"=>"form_busquedaActividad"));
            $data['form_close']      = form_close();
            $data['titulo_tabla']    = "Relaci&oacute;n DE ENTREGAS AL CLIENTE";
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
            $this->layout->view('almacen/entregacliente_index',$data);
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
            $listado                = $this->envioproveedor_model->listar($filtro,$conf['per_page'],$offset);
            $item                   = $j+1;
            $lista                  = array();
            //$listado_moneda =$this->moneda_model->listar();
            if(count($listado)>0){
                
                foreach($listado as $indice=>$valor){   
                    $codigo = $valor->ACTI_Codigo;
                    $descripcion = $valor->ACTI_Descripcion;
                    $fecha = $valor->ACTI_FechaRegistro;
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha);
                }
            }
            $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de ACTIVIDADES";
            $data['titulo_busqueda'] = "BUSCAR ACTIVIDADES";
             $data['filtro']  	 = form_input(array("name"=>"filtro","id"=>"filtro","class"=>"cajaMediana","maxlength"=>"20","value"=>$filtro));
            $data['form_open']       = form_open(base_url().'index.php/maestros/actividad/buscar',array("name"=>"form_busquedaActividad","id"=>"form_busquedaActividad"));
            $data['form_close']      = form_close();
            $data['lista']           = $lista;
            $data['oculto']          = form_hidden(array('base_url'=>base_url()));
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $this->layout->view('maestros/actividad_index',$data);
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
                    $lista[]        = array($item++,$codigo,$descripcion,$fecha,$tiposolucion);
                }
            }
            $data['lista'] = $lista;
            $data['titulo']= "VER  RECEPCIONES : ";
            $data['oculto']=form_hidden(array('base_url'=>base_url()));	
            $this->layout->view("almacen/recepcionproveedor_ver", $data);
        }
        public function nuevo(){
   
            //if(!(empty($this->input->post("checkGarantia")))){ 
                
//            foreach (($this->input->post("checkGarantia")) as $garantia) {  
//               $valores+=$garantia.",";                }               
//         
//            
//            
            
            
            $data['titulo']           = "REGISTRAR ENTREGA: ";
            $data['cod']              = '';
            $data['cliente']        = '';
            $data['garantia'] =$this->input->post("checkGarantia");           
            $data['cod']     = '';
            $data['nombre_proveedor'] = '';
            $data['serie']              = '';
            $data['numero']        = '';
            $data['padre']     = '';
            $data['codpadre'] = '';
            $data['nompadre'] = '';
            $data['url_action']        = base_url()."index.php/almacen/entregacliente/grabar";
            $data['ruc_proveedor']   = '';
            $data['descripcion']   = '';
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->layout->view('almacen/entregacliente_nuevo',$data);
            
        }
        public function grabar(){
            $compania = $this->somevar['compania'];
	    $empresa = $this->somevar['empresa'];
            $this->form_validation->set_rules("descripcion","descripcion de la entrega","required");
            $filter = new stdClass();
            if($this->form_validation->run() == false)
                $this->nuevo();
            else{
            $descripcion=$this->input->post("descripcion");
            $cliente=$this->input->post("cliente");
            $codpadre=$this->input->post("codpadre");
            $nompadre=$this->input->post("nompadre");
            $serie=$this->input->post("serie");
            $solucion=$this->input->post("solucion");
            $numero=$this->input->post("numero");
            $garantia=$this->input->post("checkGarantia");
           // var_dump($garantia);
            foreach( $garantia as $valores){
            $filter->ENTRECLI_Observacion  =$descripcion;
            $filter->CLIP_Codigo  = $cliente;	
            $filter->COMPP_Codigo = $compania;
	    $filter->EMPRP_Codigo = $empresa;
            $filter->GARAN_Codigo = $valores;
            $filter->ENTRECLI_CodigoProducto  = $codpadre;
            $filter->ENTRECLI_NombreProducto  = $nompadre;
            $filter->ENTRECLI_TipoSolucion = $solucion;	
            $filter->ENTRECLI_NumeroCredito = $numero;
            $filter->ENTRECLI_SerieCredito = $serie;
            $this->entregacliente_model->insertar($filter,$valores);
                   
               }
              
           
            $this->listar();
            }
                
        }
        public function eliminar_entregacliente(){
            $cod = $this->input->post('cod');
            $this->entregacliente_model->eliminar_entregacliente($cod);
        }
        public function editar_envioproveedor($cod){
            
            $datos =$this->actividad_model->obtener_actividad($cod);
            $codigo      = $datos[0]->ACTI_Codigo;
            $descripcion      = $datos[0]->ACTI_Descripcion;
            $data['modo']	    = "modificar";
            $data['form_open']      = form_open(base_url().'index.php/maestros/actividad/modificar_actividad',array("name"=>"frmActividad","id"=>"frmActividad"));
            $data['form_close']     = form_close();
            $data['campo'] = form_input(array('name'=>'descripcion','id'=>'descripcion','value'=>$descripcion,'maxlength'=>'30','class'=>'cajaMedia'));
            $data['descripcion']    = $descripcion;
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $data['cod']     = $codigo;
            $data['titulo']         = "EDITAR ACTIVIDAD ::: ";
            $this->layout->view("maestros/actividad_nuevo",$data);
        }
        public function modificar_envioproveedor(){
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