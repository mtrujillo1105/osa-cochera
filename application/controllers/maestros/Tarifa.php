<?php
/* *********************************************************************************
Autor: Martín Trujillo
Fecha: 18/12/2020
/* ******************************************************************************** */
class Tarifa extends CI_Controller{
    private $empresa;
    private $compania;
    private $url;
    private $view_js = NULL;

    public function __construct(){
        parent::__construct();
        $this->load->model('seguridad/usuario_compania_model');	        
        $this->load->model('maestros/tarifa_model');	
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->view_js = array(0 => "maestros/tarifa.js");
    }

    public function index($j='0'){
        $data['scripts'] = $this->view_js;
        $this->layout->view('maestros/tarifa_index',$data);    
    }  

    public function datatable_tarifa(){
        $posDT = -1;
        $columnas = array(
                ++$posDT => "TARIFC_Descripcion",
                ++$posDT => "TARIFC_Precio"
        );
        $filter = new stdClass();
        $filter->start  = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];
        $ordenar        = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
            $filter->order = $columnas[$ordenar];
            $filter->dir = $this->input->post("order")[0]["dir"];
        }
        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        
        $filter->descripcion = $this->input->post('descripcion');
        
        $tarifasInfo = $this->tarifa_model->getTarifas($filter,false);
        $records = array();
        if ( $tarifasInfo["records"] != NULL ) {
            foreach ($tarifasInfo["records"] as $indice => $valor) {
                $btn_modal = "<button type='button' onclick='editar($valor->TARIFP_Codigo)' class='btn btn-default'>
                              <img src='".base_url()."/public/images/icons/modificar.png' class='image-size-1b'></button>";
                $btn_borrar = "<button type='button' onclick='deshabilitar($valor->TARIFP_Codigo)' class='btn btn-default'>
                                <img src='".base_url()."/public/images/icons/documento-delete-mod.png' class='image-size-1b'></button>";
                $posDT = -1;
                $records[] = array(
                        ++$posDT => $valor->TARIFC_Descripcion,
                        ++$posDT => $valor->TARIFC_Precio,				        
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
        $tarifa = $this->input->post("tarifa");
        $tarifa_descripcion  = $this->input->post("tarifa_descripcion");
        $tarifa_precio       = $this->input->post("tarifa_precio");
        $tarifa_tipo         = $this->input->post("tarifa_tipo");
        $tarifa_horaini      = $this->input->post("tarifa_hora_inicio");
        $tarifa_horafin      = $this->input->post("tarifa_hora_fin");
        
        $msj = "";
        $filterTarifa = new stdClass();
        $filterTarifa->TARIFC_Descripcion = $tarifa_descripcion;
        $filterTarifa->TARIFC_Precio      = $tarifa_precio;
        $filterTarifa->TARIFC_Tipo        = $tarifa_tipo;
        $filterTarifa->TARIFC_Hinicio     = $tarifa_horaini;
        $filterTarifa->TARIFC_Hfin        = $tarifa_horafin;
        $filterTarifa->COMPP_Codigo       = $this->compania;
        $filterTarifa->TARIFC_Estado      = "1";    
        
        if ($tarifa != ""){
            $filterTarifa->TARIFC_FechaModificacion = date("Y-m-d H:i:s");
            $result = $this->tarifa_model->actualizar_tarifa($tarifa, $filterTarifa);
        }
        else{
            $filterTarifa->TARIFC_FechaRegistro = date("Y-m-d H:i:s");
            $result = $this->tarifa_model->registrar_tarifa($filterTarifa);
        }
        if ($result)
            $json = array("result" => "success", "mensaje" => "Registro exitoso.");
        else
            $json = array("result" => "error", "mensaje" => "Error al guardar el registro, intentelo nuevamente.");
        echo json_encode($json);
    }

    public function getTarifas(){
        $tarifa = $this->input->post('tarifa');
        $data   = $this->tarifa_model->getTarifa($tarifa);
        if ($data != NULL){
                $info = array(
                        "tarifa"       => $data[0]->TARIFP_Codigo,
                        "descripcion"  => $data[0]->TARIFC_Descripcion,
                        "precio"       => $data[0]->TARIFC_Precio,
                        "tipo"         => $data[0]->TARIFC_Tipo,
                        "hinicio"      => $data[0]->TARIFC_Hinicio,
                        "hfin"         => $data[0]->TARIFC_Hfin,
                        "abonado"      => $data[0]->TARIFC_FlagAbonado
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

    public function deshabilitar_tarifa(){
        $tarifa = $this->input->post("tarifa");
        $filter = new stdClass();
        $filter->TARIFC_Estado  = "0";
        if ($tarifa != ""){
            $filter->TARIFC_FechaModificacion  = date("Y-m-d H:i:s");
            $result = $this->tarifa_model->actualizar_tarifa($tarifa, $filter);
        }
        if ($result)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        echo json_encode($json);
    }

    
}
?>