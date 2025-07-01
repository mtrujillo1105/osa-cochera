<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('buscar_permiso'))
{
	// ojo la URL al final no debe tener /
	// parametros
	// 1 .- codigo rol
	// 2 .- url
	function autocompletar_producto($rol,$url)
	{
		$ci = &get_instance();
		$ci->load->model("seguridad/permiso_model");
		$ci->load->model("seguridad/menu_model");
		//capturando la url despues del index.php
		$nueva_url = "";
		$ext = explode('/',$url);
		$total_url = count($ext) - 1;
		foreach($ext as $key=>$value){
			if($key >= 3){
				if($key == $total_url){
					$nueva_url .= $value;
				}else{
					$nueva_url .= $value."/";
				}
			}
		}
		$datos_menu = $ci->menu_model->obtener_x_url($nueva_url);
		$lista = array();
		if(is_array($datos_menu)){
			$menu = $datos_menu[0]->MENU_Codigo;
			$data = $ci->permiso_model->busca_permiso($rol,$menu);
			if(is_array($data) > 0){
				foreach($data as $valor){
					$filter 			= new stdClass();
					$datos_menu			= $ci->menu_model->obtener_datosMenu($valor->MENU_Codigo);
					$filter->menu		= $datos_menu[0]->MENU_Codigo;
					$filter->constante 	= $datos_menu[0]->MENU_Constante;
					/*$filter->crear		= $valor->PERM_Crear;
					$filter->ver		= $valor->PERM_Ver;
					$filter->editar		= $valor->PERM_Editar;
					$filter->eliminar	= $valor->PERM_Eliminar;*/
					$lista[]			= $filter;
				}
			}
		}
		return $lista;
	}
}

if ( ! function_exists('buscar_permiso_rol_menu'))
{
	// ojo la URL al final no debe tener /
	// parametros
	// 1 .- codigo rol
	// 2 .- url
	function buscar_permiso_rol_menu($rol,$menu)
	{
		$ci = &get_instance();
		$ci->load->model("seguridad/permiso_model");
		$lista = array();
		$data = $ci->permiso_model->busca_permiso($rol,$menu);
		if(is_array($data) > 0){
			foreach($data as $valor){
				$filter 			= new stdClass();
				$filter->crear		= $valor->PERM_Crear;
				$filter->ver		= $valor->PERM_Ver;
				$filter->editar		= $valor->PERM_Editar;
				$filter->eliminar	= $valor->PERM_Eliminar;
				$lista[]			= $filter;
			}
		}
		return $lista;
	}
}
?>
