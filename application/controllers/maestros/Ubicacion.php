<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ubicacion extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('maestros/compania_model');	
        $this->load->model('maestros/persona_model');			
        $this->load->model('maestros/ubicacion_model');	    
        $this->load->model('seguridad/permiso_model');
        $this->load->model('seguridad/menu_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('seguridad/usuario_compania_model');  
        $this->compania = $this->session->userdata('compania');
        $this->base_url = base_url();
        $this->view_js = array(0 => "maestros/ubicacion.js");		      
    }

    public function index($msg = NULL)
    {		
        $data["ubicaciones"] = $this->ubicacion_model->getUbicaciones();
	$data['scripts']     = $this->view_js;
	$this->layout->view("maestros/ubicacion_index", $data);    
    }

    public function table()
    {		
        $data["ubicaciones"] = $this->ubicacion_model->getUbicaciones();
	$data['scripts']     = $this->view_js;
	$this->layout->view("maestros/ubicacion_table", $data);    
    }

    public function datatable_ubicacion(){
	$posDT = -1;
    	$columnas = array(
		++$posDT => "UBICC_Descripcion",
		++$posDT => "UBICC_EspaciosAsignados",
		++$posDT => "UBICC_EspaciosUsados"
        );
        $filter = new stdClass();
        $filter->start = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];
        $ordenar        = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
            $filter->order = $columnas[$ordenar];
            $filter->dir = $this->input->post("order")[0]["dir"];
        }
        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        $filter->descripcion = $this->input->post('descripcion');
        $tarifasInfo = $this->ubicacion_model->getUbicaciones($filter,false);
        $records = array();
	if ( $tarifasInfo["records"] != NULL ) {
            foreach ($tarifasInfo["records"] as $indice => $valor) {
		$btn_modal = "<button type='button' onclick='editar($valor->UBICP_Codigo)' class='btn btn-default'>
                                <img src='".$this->base_url."public/images/icons/modificar.png' class='image-size-1b'></button>";
                $btn_borrar = "<button type='button' onclick='deshabilitar($valor->UBICP_Codigo)' class='btn btn-default'>
                                <img src='".$this->base_url."public/images/icons/documento-delete-mod.png' class='image-size-1b'></button>";
                $posDT = -1;
		$records[] = array(
                    	++$posDT => $valor->UBICC_Descripcion,
			++$posDT => $valor->UBICC_EspaciosAsignados,
                        ++$posDT => $valor->UBICC_EspaciosUsados,					        
			++$posDT => $btn_modal,
			++$posDT => $btn_borrar
			);
            }
        }
	$recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
	$recordsFilter = $tarifasInfo["recordsFilter"];
	$json = array(
			"draw"            => intval( $this->input->post('draw') ),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
	);
	echo json_encode($json);
	die();
    }
    
     public function guardar_registro(){
        $ubicacion   = $this->input->post("ubicacion");
        $easignados  = $this->input->post("ubicacion_easignado");
        $eusados     = $this->input->post("ubicacion_eusado");
        $descripcion = $this->input->post("ubicacion_descripcion");
        $imagen      = NULL;
        $filter = new stdClass();
        $filter->UBICC_Descripcion       = $descripcion;
        //$filter->UBICC_Imagen            = $imagen;
        $filter->UBICC_EspaciosAsignados = $easignados;
        $filter->UBICC_EspaciosUsados    = $eusados;
        $filter->UBICC_FlagEstado        = "1";
        $filter->COMPP_Codigo            = $this->compania;
        if ($ubicacion != ""){
            $filter->UBICC_FechaModificacion = date("Y-m-d H:i:s");
            
            $result = $this->ubicacion_model->actualizar_ubicacion($ubicacion, $filter);
        }
        else{
            $filter->UBICC_FechaRegistro = date("Y-m-d H:i:s");
            $result = $this->ubicacion_model->registrar_ubicacion($filter);
        }
        if ($result)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");

        echo json_encode($json);
    }

    public function getUbicacion(){
        $ubicacion = $this->input->post('ubicacion');
        $data   = $this->ubicacion_model->getUbicacion($ubicacion);
        if ($data != NULL){
            $info = array(
                    "ubicacion"   => $data[0]->UBICP_Codigo,
                    "descripcion" => $data[0]->UBICC_Descripcion,
                    "easignados"  => $data[0]->UBICC_EspaciosAsignados,
                    "eusados"     => $data[0]->UBICC_EspaciosUsados
            );
        }
        else
            $info = NULL;
        if ($info != NULL)
            $json = array("match" => true, "info" => $info);
        else
            $json = array("match" => false, "info" => NULL);
        echo json_encode($json);
    }    
    
    public function deshabilitar_caja(){
        $ubicacion = $this->input->post("ubicacion");
        $filter = new stdClass();
        $filter->UBICC_FlagEstado  = "0";
        if ($ubicacion != ""){
            $filter->UBICC_FechaModificacion = date("Y-m-d H:i:s");
            $result = $this->ubicacion_model->deshabilitar_caja($ubicacion, $filter);
        }
        if ($result)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");

        echo json_encode($json);
    }

}

?>