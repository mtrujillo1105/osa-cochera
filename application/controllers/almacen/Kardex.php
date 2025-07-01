<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Kardex extends CI_Controller
{

	private $compania;
	private $url;
	private $view_js = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('almacen/familia_model');
		$this->load->model('maestros/almacen_model');
		$this->load->model('maestros/fabricante_model');
		$this->load->model('maestros/marca_model');

		$this->load->model('almacen/kardex_model');
		$this->load->model('almacen/producto_model');
		$this->load->model('almacen/almacenproducto_model');

		$this->compania = $this->session->userdata('compania');
		$this->url = base_url();
		$this->view_js = array(0 => "almacen/kardex.js");
	}

	public function index()
	{
		$this->listar();
	}

	public function listar()
	{
		$filter = new stdClass();
		$filter->dir = 'ASC';

		$filter->order = 'FAMI_Descripcion';
		$filter->flagBS = 'B';
		$data['familias'] = $this->familia_model->getFamilias($filter);
		unset($filter->flagBS);

		$filter->order = "FABRIC_Descripcion";
		$data['fabricantes'] = $this->fabricante_model->getFabricantes($filter);

		$filter->order = "MARCC_Descripcion";
		$data['marcas'] = $this->marca_model->getMarcas($filter);

		$filter->order = "ALMAC_Descripcion";
		$data['almacenes'] = $this->almacen_model->getAlmacens($filter);

		$data['scripts'] = $this->view_js;
		$this->layout->view('almacen/kardex_index', $data);
	}

	public function dtKardex()
	{
		$posDT = -1;
		$columnas = array(
			++$posDT => "fecha_movimiento",
			++$posDT => "tipo_movimiento",
			++$posDT => "ALMAC_Descripcion",
			++$posDT => "documento",
			++$posDT => "numero",
			++$posDT => "cantidad",
			++$posDT => "costo"
		);

		$producto = $this->input->post('productoSearch');
		$almacen = $this->input->post('almacenSearch');
		$fechaIni = $this->input->post('fechaIniSearch');
		$fechaFin = $this->input->post('fechaFinSearch');

		$filter = new stdClass();
		$filter->start = $this->input->post("start");
		$filter->length = $this->input->post("length");
		$filter->search = $this->input->post("search")["value"];

		$ordenar = $this->input->post("order")[0]["column"];
		if ($ordenar != "") {
			$filter->order = $columnas[$ordenar];
			$filter->dir = $this->input->post("order")[0]["dir"];
		}

		$filter->producto = $producto;
		$filter->almacen = $almacen;

		$kardex = $this->kardex_model->getMovimientos($filter);
	
		$json = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => 0,
			"recordsFiltered" => 0,
			"data"            => 0
		);

		echo json_encode($json);
	}
}
?>