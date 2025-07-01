<?php
class Permiso extends CI_Controller{
	public function __construct(){
            parent::Controller();
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->library('html');

            $this->load->model('seguridad/rol_model');
            $this->load->model('seguridad/permiso_model');
            $this->somevar['compania'] = $this->session->userdata('compania');
	}
	
	public function index(){
		$this->load->library('layout','layout');
		$this->layout->view("seguridad/permiso");
	}
	
}
?>