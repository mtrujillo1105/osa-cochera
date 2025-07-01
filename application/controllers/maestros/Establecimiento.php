<?php
class Establecimiento extends CI_Controller{

	public function __construct(){
            parent::__construct();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');
            $this->load->model('maestros/tipoestablecimiento_model');
            $this->load->library('layout','layout');
	}
	
	public function index(){
		$this->layout->view('seguridad/inicio');	
	}

	public function establecimientos($j='0'){
        $data['txtEstablecimiento']   = "";
		$data['registros']  = count($this->tipoestablecimiento_model->listar_tiposEstablecimiento());
		$conf['base_url']   = site_url('maestros/establecimiento/establecimientos/');
		$conf['per_page']   = 10;
		$conf['num_links']  = 3;
                $conf['next_link'] = "&gt;";
                $conf['prev_link'] = "&lt;";
                $conf['first_link'] = "&lt;&lt;";
                $conf['last_link']  = "&gt;&gt;";
                $conf['uri_segment'] = 4;	
		$conf['total_rows'] = $data['registros'];
                $offset             = (int)$this->uri->segment(4);
                $this->pagination->initialize($conf);
                $data['paginacion'] = $this->pagination->create_links();
		$listado_establecimientos = $this->tipoestablecimiento_model->listar_tiposEstablecimiento($conf['per_page'],$offset);
		$item               = $j+1;
		foreach($listado_establecimientos as $indice=>$valor){
			$codigo         = $valor->TESTP_Codigo;
			$editar         = "<a href='#' onclick='editar_establecimiento(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
			$ver            = "<a href='#' onclick='ver_establecimiento(".$codigo.")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
			$eliminar       = "<a href='#' onclick='eliminar_establecimiento(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
			$lista[]        = array($item++,$valor->TESTC_Descripcion,$editar,$ver,$eliminar);
		}
                $data['action']          = base_url()."index.php/maestros/establecimiento/buscar_establecimientos";
		$data['titulo_tabla']    = "RELACI&Oacute;N DE ESTABLECIMIENTOS";	
		$data['titulo_busqueda'] = "BUSCAR ESTABLECIMIENTO";
		$data['lista']      = $lista;
		$data['oculto']     = form_hidden(array('base_url'=>base_url()));
		$this->layout->view('maestros/establecimiento_index',$data);		
	}
	public function nuevo_establecimiento(){		
		$lblEstablecimiento = form_label('NOMBRES','nombres');
		$txtEstablecimiento = form_input(array('name'=>'txtEstablecimiento','id'=>'txtEstablecimiento','value'=>'','maxlength'=>'30','class'=>'cajaMedia'));
		$oculto             = form_hidden(array('accion'=>"",'codigo'=>"",'modo'=>"insertar",'base_url'=>base_url()));	
		$data['titulo']     = "REGISTRAR ESTABLECIMIENTO";		
		$data['formulario'] = "frmEstablecimiento";	
		$data['campos']     = array($lblEstablecimiento);
		$data['valores']    = array($txtEstablecimiento);		
		$data['oculto']     = $oculto;
		$data['onload']		= "onload=\"$('#txtEstablecimiento').focus();\"";
		$this->layout->view('maestros/establecimiento_nuevo',$data);
	}	
	public function insertar_establecimiento(){
		$this->form_validation->set_rules('nombres','Nombre','required');
		if($this->form_validation->run() == FALSE){
			$this->nuevo_establecimiento();
		}
		else{
			$txtNombres = $this->input->post('nombres');
			$this->tipoestablecimiento_model->insertar_establecimiento($txtNombres);
			$this->load->view('maestros/establecimiento_index');		
		}	
	}
	public function editar_establecimiento($codigo){
		$datos_area  = $this->tipoestablecimiento_model->obtener_tipoEstablecimiento($codigo);
		$descripcion = $datos_area[0]->TESTC_Descripcion;
		$lblEstablecimiento = form_label('NOMBRE','establecimiento');
		$txtEstablecimiento = form_input(array('name'=>'txtEstablecimiento','id'=>'txtEstablecimiento','value'=>$descripcion,'maxlength'=>'50','class'=>'cajaMedia'));
		$oculto             = form_hidden(array('accion'=>"",'codigo'=>$codigo,'modo'=>"modificar",'base_url'=>base_url()));	
		$data['titulo']     = "EDITAR ESTABLECIMIENTO";		
		$data['formulario'] = "frmEstablecimiento";	
		$data['campos']     = array($lblEstablecimiento);
		$data['valores']    = array($txtEstablecimiento);		
		$data['oculto']     = $oculto;
		$data['onload']		= "onload=\"$('#txtEstablecimiento').select();$('#txtArea').focus();\"";		
		$this->layout->view('maestros/establecimiento_nuevo',$data);
	}
	public function modificar_establecimiento(){
		$this->form_validation->set_rules('nombres','Nombre','required');
		if($this->form_validation->run() == FALSE){
			$this->nueva_area();
		}
		else{
			$establecimiento = $this->input->post('codigo');
			$descripcion     = $this->input->post('nombres');
			$this->tipoestablecimiento_model->modificar_establecimiento($establecimiento,$descripcion);
			$this->layout->view('maestros/establecimiento_index');		
		}	
	}
	public function eliminar_establecimiento(){
		$establecimiento = $this->input->post('establecimiento');
		$this->tipoestablecimiento_model->eliminar_establecimiento($establecimiento);
	}
	public function ver_establecimiento($codigo){
		$data['datos_establecimiento']    = $this->tipoestablecimiento_model->obtener_tipoEstablecimiento($codigo);
		$data['titulo']        = "VER ESTABLECIMIENTO";
		$data['oculto']        = form_hidden(array('base_url'=>base_url()));
		$this->layout->view('maestros/establecimiento_ver',$data);
	}
	public function buscar_establecimientos($j='0')
        {
            $nombre_establecimiento  = $this->input->post('txtEstablecimiento');
            $filter = new stdClass();
            $filter->nombre_establecimiento = $nombre_establecimiento;
            $data['txtEstablecimiento']     = $nombre_establecimiento;
            $data['registros']   = count($this->tipoestablecimiento_model->buscar_establecimientos($filter));
            $conf['base_url']    = site_url('maestros/establecimiento/buscar_establecimientos/');
            $conf['per_page']    = 10;
            $conf['num_links']   = 3;
            $conf['next_link'] = "&gt;";
            $conf['prev_link'] = "&lt;";
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link']  = "&gt;&gt;";
            $conf['uri_segment'] = 4;
            $conf['total_rows']  = $data['registros'];
            $offset              = (int)$this->uri->segment(4);
            $listado_areas       = $this->tipoestablecimiento_model->buscar_establecimientos($filter,$conf['per_page'],$offset);
            $item                = $j+1;
            $lista               = array();
            if(count($listado_areas)>0){
                foreach($listado_areas as $indice=>$valor){
                    $codigo         = $valor->TESTP_Codigo;
                    $editar          = "<a href='#' onclick='editar_establecimiento(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $ver               = "<a href='#' onclick='ver_establecimiento(".$codigo.")' target='_parent'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $eliminar     = "<a href='#' onclick='eliminar_establecimiento(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                    $lista[]          = array($item++,$valor->TESTC_Descripcion,$editar,$ver,$eliminar);
                }
            }
            $data['action']          = base_url()."index.php/maestros/establecimiento/buscar_establecimientos";
            $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA DE ESTABLECIMIENTOS";
            $data['titulo_busqueda'] = "BUSCAR ESTABLECIMIENTO";
            $data['lista']      = $lista;
            $data['oculto']     = form_hidden(array('base_url'=>base_url()));
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $this->layout->view('maestros/establecimiento_index',$data);
    }
	public function listar_tiposEstablecimiento(){
		$listado_establecimientos = $this->tipoestablecimiento_model->listar_tiposEstablecimiento();
		$resultado = json_encode($listado_establecimientos);
		echo $resultado;
	}		
}
?>