<?php
class Tipoproducto extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->model('almacen/tipoproducto_model');
        $this->load->model('almacen/plantilla_model');
    }
    public function index(){
        $this->load->library('layout','layout');
        $this->layout->view('seguridad/inicio');
    }
    public function tipoproductos($flagBS='B', $j='0'){ //flagBS: Dice si es Bien o Servicio
        $this->load->library('layout','layout');
        $data['txtTipoProd']   = "";
        $data['registros']  = count($this->tipoproducto_model->listar_tipos_producto($flagBS));
        $conf['base_url']   = site_url('almacen/tipoproducto/tipoproductos/'.$flagBS);
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
        $listado_tipoproducto     = $this->tipoproducto_model->listar_tipos_producto($flagBS, $conf['per_page'],$offset);
        $item               = $j+1;
        $lista                = array();
        if(is_array($listado_tipoproducto)>0){
             foreach($listado_tipoproducto as $indice=>$valor){
                 $codigo         = $valor->TIPPROD_Codigo;
                 $numplantila    = count($this->plantilla_model->listar_plantilla($codigo));
                 $editar         = "<a href='#' onclick='editar_tipoprod(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                 $ver            = "<a href='#' onclick='ver_tipoprod(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver'></a>";
                 $eliminar       = "<a href='#' onclick='eliminar_tipoprod(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";
                 $lista[]        = array($item++,$valor->TIPPROD_Descripcion,$numplantila,$editar,$ver,$eliminar);
             }
        }
        $data['flagBS']          = $flagBS;
        $data['action']          = base_url()."index.php/almacen/tipoproducto/buscar_tipoproductos";
        $data['titulo_tabla']    = "RELACI&Oacute;N de TIPOS DE ".($flagBS=='B' ? 'ARTICULO' : 'SERVICIO');
        $data['titulo_busqueda'] = "BUSCAR TIPO DE ".($flagBS=='B' ? 'ARTICULO' : 'SERVICIO');
        $data['lista']      = $lista;
        $data['oculto']     = form_hidden(array('base_url'=>base_url()));
        $this->layout->view('almacen/tipoproducto_index',$data);
    }
    public function nuevo_tipoproducto($flagBS='B'){
            $this->load->library('layout','layout');
            $modo               = "";
            $accion             = "";
            $modo               = "insertar";
            $data['url_action'] = base_url()."index.php/almacen/tipoproducto/insertar_tipoproducto";
            $codigo    = "";
            $lblCargo  = form_label('NOMBRE DEL TIPO DEL '.($flagBS=='B' ? 'ARTICULO' : 'SERVICIO'),'nombre');
            $txtCargo  = form_input(array('name'=>'nombre','id'=>'nombre','value'=>'','maxlength'=>'30','class'=>'cajaMedia'));
            $oculto    = form_hidden(array('accion'=>$accion,'codigo'=>$codigo,'modo'=>$modo,'base_url'=>base_url(), 'flagBS'=>$flagBS));
            $data['titulo']     = "REGISTRAR TIPO DE ".($flagBS=='B' ? 'ARTICULO' : 'SERVICIO');
            $data['formulario'] = "frmTipoProd";
            $data['campos']     = array($lblCargo);
            $data['valores']    = array($txtCargo);
            $data['oculto']     = $oculto;
            $data['onload']		= "onload=\"$('#nombre').focus();\"";
            
            $data['lista_atributos'] = array();
            
            $this->layout->view('almacen/tipoproducto_nuevo',$data);
    }
    public function insertar_tipoproducto(){
            $this->form_validation->set_rules('nombre','Nombre del tipo de producto','required');
            if($this->form_validation->run() == FALSE){
                    $this->nuevo_tipoproducto();
            }
            else{
                    $nombre   = $this->input->post('nombre');
                    $atributo = $this->input->post('atributo');
                    $flagBS   = $this->input->post('flagBS');
                    $this->tipoproducto_model->insertar_tipo_producto($nombre, $atributo, $flagBS);
                    $this->tipoproductos($flagBS);
            }
    }
    public function editar_tipoproducto($codigo){
        $this->load->library('layout','layout');
        $accion       = "";
        $modo         = "modificar";
        $data['url_action'] = base_url()."index.php/almacen/tipoproducto/modificar_tipoproducto/";
        $datos_tipoprod  = $this->tipoproducto_model->obtener_tipo_producto($codigo);
        $nombre_tipoprod = $datos_tipoprod[0]->TIPPROD_Descripcion;
        $lblCargo     = form_label('NOMBRE DEL TIPO DE PRODUCTO','nombre');
        $txtCargo     = form_input(array('name'=>'nombre','id'=>'nombre','value'=>$nombre_tipoprod,'maxlength'=>'30','class'=>'cajaMedia'));
        $oculto       = form_hidden(array('accion'=>$accion,'codigo'=>$codigo,'modo'=>$modo,'base_url'=>base_url(), 'flagBS'=>$datos_tipoprod[0]->TIPPROD_FlagBienServicio));
        $data['titulo']     = "EDITAR TIPO DE PRODUCTO";
        $data['formulario'] = "frmTipoProd";
        $data['campos']     = array($lblCargo);
        $data['valores']    = array($txtCargo);
        $data['oculto']     = $oculto;
        $data['onload']		= "onload=\"$('#nombre').select();$('#nombre').focus();\"";
        
        $data['lista_atributos']  = $this->plantilla_model->listar_plantilla($codigo);
        
        
        $this->layout->view('almacen/tipoproducto_nuevo',$data);
    }
    public function modificar_tipoproducto(){
        $this->form_validation->set_rules('nombre','Nombre del tipo de producto','required');
        if($this->form_validation->run() == FALSE){
                $this->nuevo_tipoproducto();
        }
        else{
                $tipoprod  = $this->input->post('codigo');
                $nombre = $this->input->post('nombre');
                $atributo = $this->input->post('atributo');
                $flagBS   = $this->input->post('flagBS');
                
                $this->tipoproducto_model->modificar_tipo_producto($tipoprod,$nombre, $atributo);
                $this->tipoproductos($flagBS);
        }
    }
    public function eliminar_tipoproducto(){
        $tipoprod = $this->input->post('tipoprod');
        $this->plantilla_model->eliminar_plantilla_por_tipo($tipoprod);
        $this->tipoproducto_model->eliminar_tipo_producto($tipoprod);
    }
    public function ver_tipoproducto($codigo)
    {
        $this->load->library('layout','layout');
        $data['datos_tipoprod'] = $this->tipoproducto_model->obtener_tipo_producto($codigo);
        $datos                  = $data['datos_tipoprod'];
        $data['titulo']      = "VER TIPO DE PRODUCTO";
        $data['oculto']      = form_hidden(array('base_url'=>base_url(), 'flagBS'=>$data['datos_tipoprod'][0]->TIPPROD_FlagBienServicio));
        $data['lista_atributos']  = $this->plantilla_model->listar_plantilla($codigo);
        $this->layout->view('almacen/tipoproducto_ver',$data);
    }
    public function buscar_tipoproductos($j='0')
    {
        $this->load->library('layout','layout');
        $nombre_tipoprod       = $this->input->post('txtTipoProd');
        $flagBS                = $this->input->post('flagBS');
        $filter=new stdClass();
        $filter->nombre_tipoprod = $nombre_tipoprod;
        $filter->flagBS         = $flagBS;
        $data['txtTipoProd']   = $nombre_tipoprod;
        $data['registros']  = count($this->tipoproducto_model->buscar_tipo_producto($filter));
        $conf['base_url']   = site_url('almacen/tiproducto/buscar_tipoproductos/'.$flagBS);
        $conf['per_page']   = 10;
        $conf['num_links']  = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link']  = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $conf['total_rows'] = $data['registros'];
        $offset             = (int)$this->uri->segment(4);
        $listado_tipoprod     = $this->tipoproducto_model->buscar_tipo_producto($filter,$conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
        if(is_array($listado_tipoprod)>0){
            foreach($listado_tipoprod as $indice=>$valor){
                $codigo         = $valor->TIPPROD_Codigo;
                $numplantila    = count($this->plantilla_model->listar_plantilla($codigo));
                $editar         = "<a href='#' onclick='editar_productotipo(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='#' onclick='ver_productotipo(".$codigo.")' target='_parent'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar       = "<a href='#' onclick='eliminar_productotipo(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[]        = array($item++,$valor->TIPPROD_Descripcion,$numplantila,$editar,$ver,$eliminar);
            }
        }
        $data['flagBS']          = $flagBS;
        $data['action']          = base_url()."index.php/almacen/tipoproducto/buscar_tipoproductos/".$flagBS;
        $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de TIPOS DE ".($flagBS=='B' ? 'ARTICULO' : 'SERVICIO');
        $data['titulo_busqueda'] = "BUSCAR TIPO DE ".($flagBS=='B' ? 'ARTICULO' : 'SERVICIO');
        $data['lista']      = $lista;
        $data['oculto']     = form_hidden(array('base_url'=>base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/tipoproducto_index',$data);
    }
    public function eliminar_plantilla()
    {
        $this->load->model('almacen/plantilla_model');
        $id = $this->input->post('plantilla');
        $this->plantilla_model->eliminar_plantilla($id);
    }
}
?>