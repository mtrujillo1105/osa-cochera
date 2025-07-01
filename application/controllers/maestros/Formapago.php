<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Formapago extends CI_Controller{

	private $url;
	private $view_js = NULL;

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/formapago_model');
		$this->url = base_url();
		$this->view_js = array(0 => "maestros/formapago.js");
	}

	public function listar(){
		$data['scripts'] = $this->view_js;
		$this->layout->view('maestros/formapago_index', $data);
	}

    public function datatable_fpago(){
        $posDT = -1;
        $columnas = array(
            ++$posDT => "FORPAC_Descripcion"
        );
        $filter = new stdClass();
        $filter->start = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];
        $ordenar = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
            $filter->order = $columnas[$ordenar];
            $filter->dir = $this->input->post("order")[0]["dir"];
        }
        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        $filter->descripcion = $this->input->post('descripcion');
        $formapagoInfo = $this->formapago_model->getFpagos($filter,false);
        $records = array();
        if ($formapagoInfo["records"] != NULL) {
            foreach ($formapagoInfo["records"] as $indice => $valor) {
                $btn_modal = "<button type='button' onclick='editar($valor->FORPAP_Codigo)' class='btn btn-default'>
                <img src='".$this->url."/public/images/icons/modificar.png' class='image-size-1b'>
                </button>";

                $btn_eliminar = "<button type='button' onclick='deshabilitar($valor->FORPAP_Codigo)' class='btn btn-default'>
                <img src='".$this->url."/public/images/icons/documento-delete.png' class='image-size-1b'>
                </button>";

                $posDT = -1;
                $records[] = array(
                        ++$posDT => $valor->FORPAC_Descripcion,
                        ++$posDT => $btn_modal,
                        ++$posDT => $btn_eliminar
                );
            }
        }

            $recordsTotal = ( $formapagoInfo["recordsTotal"] != NULL ) ? $formapagoInfo["recordsTotal"] : 0;
            $recordsFilter = $formapagoInfo["recordsFilter"];

            $json = array(
                    "draw"            => intval( $this->input->post('draw') ),
                    "recordsTotal"    => $recordsTotal,
                    "recordsFiltered" => $recordsFilter,
                    "data"            => $records
            );

            echo json_encode($json);
            die();
    }

	public function getFpago(){

		$fpago = $this->input->post("fpago");

		$formapagoInfo = $this->formapago_model->getFpago($fpago);
		$lista = array();

		if ( $formapagoInfo != NULL ){
			foreach ($formapagoInfo as $indice => $val) {
				$lista = array(
					"fpago" => $val->FORPAP_Codigo,
					"descripcion" => $val->FORPAC_Descripcion
				);
			}

			$json = array("match" => true, "info" => $lista);
		}
		else
			$json = array("match" => false, "info" => "");

		echo json_encode($json);
	}

	public function guardar_registro(){

		$fpago = $this->input->post("fpago");
		$descripcion_fpago = $this->input->post("descripcion_fpago");

		$filter = new stdClass();
		$filter->FORPAC_Descripcion = strtoupper($descripcion_fpago);
		$filter->FORPAC_Orden = "0";
		$filter->FORPAC_FlagEstado = "1";

		if ($fpago != ""){
			$filter->FORPAP_Codigo = $fpago;
			$filter->FORPAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->formapago_model->actualizar_fpago($fpago, $filter);
		}
		else{
			$filter->FORPAC_FechaRegistro = date("Y-m-d H:i:s");
			$result = $this->formapago_model->insertar_fpago($filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}

	public function deshabilitar_fpago(){

		$fpago = $this->input->post("fpago");

		$filter = new stdClass();
		$filter->FORPAC_FlagEstado  = "0";

		if ($fpago != ""){
			$filter->FORPAC_FechaModificacion = date("Y-m-d H:i:s");
			$result = $this->formapago_model->deshabilitar_fpago($fpago, $filter);
		}

		if ($result)
			$json = array("result" => "success");
		else
			$json = array("result" => "error");

		echo json_encode($json);
	}
}
?>