<?php
class Atributo extends CI_Controller{
    public function __construct()
    {
            parent::__construct();
            $this->load->model('almacen/atributo_model');
            
            $this->load->helper('json');
            $this->load->library('html');
            $this->load->library('table');
            $this->load->library('layout','layout');
            $this->load->library('pagination');	
	}
	public function index()
	{
		$this->layout->view('seguridad/inicio');
	}	
	
        public function ventana_busqueda_atributo($flagBS='B', $j=0){
            $filter=new stdClass();
            $filter->nombre_atributo = $this->input->post('txtNombre');
            $filter->flagBS          = $flagBS;
            $data['registros']  = count($this->atributo_model->buscar_atributo($filter));
            $data['txtNombre']   = $this->input->post('txtNombre');
            $data['base_url']  = site_url('almacen/atributo/ventana_busqueda_atributo/'.$flagBS);
            $conf['base_url']   = site_url('almacen/atributo/ventana_busqueda_atributo/'.$flagBS);
            $conf['total_rows'] = $data['registros'];
            $conf['per_page']   = 10;
            $conf['num_links']  = 3;
            $conf['next_link'] = "&gt;";
            $conf['prev_link'] = "&lt;";
            $conf['first_link'] = "&lt;&lt;";
            $conf['last_link']  = "&gt;&gt;";
            $conf['uri_segment'] = 5;
            $this->pagination->initialize($conf);
            $data['paginacion'] = $this->pagination->create_links();
            $listado_atributos = $this->atributo_model->buscar_atributo($filter, $conf['per_page'],$j);
            $item            = $j+1;
            $lista           = array();
            if(count($listado_atributos)>0){
                foreach($listado_atributos as $indice=>$valor){
                        $codigo         = $valor->ATRIB_Codigo;
                        $nombre            = $valor->ATRIB_Descripcion;
                        switch($valor->ATRIB_TipoAtributo){
                            case 1: $tipo='Númerico'; break;
                            case 2: $tipo='Fecha'; break;
                            case 3: $tipo='Texto'; break;
                            default: $tipo=''; break;

                        }
                        $seleccionar  = "<a href='#' onclick='editar_atributo(".$codigo.")' target='_parent'><img src='".base_url()."images/convertir.png' width='16' height='16' border='0' title='Seleccionar'></a>";
                        $lista[]        = array($item,$nombre,$tipo,$seleccionar,$codigo);
                        $item++;
                }
            }
            $data['lista'] = $lista;
                $this->load->view('almacen/atributo_ventana_buqueda',$data);
        }
  
}
?>