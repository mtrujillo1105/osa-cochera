<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class basedatos extends CI_Controller{
	##  -> Begin
	private $compania;
	private $usuario;
  ##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->load->model('empresa/empresa_model');
		$this->load->model('maestros/eliminar_model');
		$this->compania = $this->session->userdata('compania');
		$this->usuario = $this->session->userdata('user');
	}
	##  -> End
	
	public function index(){
		$this->layout->view('seguridad/inicio');
	}
	
	public function basedatos_principal($j = 0){
		$this->layout->view('basedatos/basedatos_index');
	}
	
	##  -> Begin
	public  function truncate_comprobantes(){
		$this->eliminar_model->truncate_comprobantes();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> Begin
	public  function truncate_docs(){
		$this->eliminar_model->truncate_docs();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> Begin
	public  function truncate_inventarios(){
		$this->eliminar_model->truncate_inventarios();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_stock(){
		$this->eliminar_model->truncate_stock();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_productos(){
		$this->eliminar_model->truncate_productos();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_usuarios(){
		$this->eliminar_model->truncate_usuarios();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_personal(){
		$this->eliminar_model->truncate_personal();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_empresas(){
		$this->eliminar_model->truncate_empresas();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_clientes_proveedores(){
		$this->eliminar_model->truncate_clientes_proveedores();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	##  -> Begin
	public  function truncate_all(){
		$this->eliminar_model->truncate_all();
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	##  -> End

	## Dev:  -> Begin
	public function obtener_estado_pago(){
		$empresa 		= $this->session->userdata('empresa');
		$datos 			= $this->empresa_model->obtener_datosEmpresa($empresa);
		$estado_pago 	= $datos[0]->EMPRC_EstadoPago;
		$numero_transaccion = $datos[0]->EMPRC_NumeroPago;

		$arrayName 		= array('pago' => $estado_pago, "deposito" => $numero_transaccion);

		echo json_encode($arrayName);
	}
	## Dev:  -> End

	## Dev:  -> Begin
	public function UpdateEstadoPago($value=''){
		$empresa 		= $this->session->userdata('empresa');
		$estado_pago	= $this->input->post("estado_pago");
		$deposito 		= $this->input->post("deposito");
		$filter 		= new stdClass();
		$filter->EMPRC_NumeroPago = $deposito;
		$filter->EMPRC_EstadoPago = $estado_pago;
		$this->empresa_model->UpdateEstadoPago($empresa,$filter);
		$json = array("result" => "success");
		echo json_encode( $json );
	}
	## Dev:  -> End
}