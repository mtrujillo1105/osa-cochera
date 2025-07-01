<?php
class Boleta extends CI_Controller{
	function __construct(){
		parent::Controller();
		$this->load->helper('form');
		$this->load->helper('date');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
		$this->load->library('html');
		$this->load->model('rptventas_model');
        $this->load->model('compras/compras_model');
        $this->load->model('mantenimiento_model');
        $this->load->model('comercial/comercial_model');
        $this->load->model('producto/producto_model');
	}
	function index(){
		$this->load->view('seguridad/inicio');
	}

	public function compromisos(){
		
	}
}
?>