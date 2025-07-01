<?php
class Opedido extends CI_Controller
{
	function __construct(){
		parent::Controller();
		$this->load->helper('form');
		$this->load->helper('date');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
		$this->load->library('html');
		$this->load->model('compras/compras_model');
        $this->load->model('mantenimiento_model');
        $this->load->model('comercial/comercial_model');
        $this->load->model('producto/producto_model');
		$this->load->library('layout', 'layout_main');  
	}
	function index()
	{
		$this->load->view('inicio');	
	}
	function pedidos($j='0'){
	
	}
	function nuevo_pedido(){
	
	}
	function pedido_insertar(){
	
	}
	function editar_pedido(){
	
	}
	function modificar_pedido(){
	
	}
	function eliminar_pedido(){
	
	}
	function ver_pedido(){
	
	}
    function ver_pedido_pdf(){
         
    }
	function buscar_pedido(){
	
	}	
}
?>