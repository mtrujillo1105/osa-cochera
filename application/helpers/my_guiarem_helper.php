<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('calcular_cantidad_entregada_x_producto'))
{
    function calcular_cantidad_entregada_x_producto($tipo_orden,$tipo_guia,$cod_orden,$cod_prod)
    {
        $ci = &get_instance();
        $ci->load->model("almacen/guiarem_model");
		$cic = &get_instance();
        $cic->load->model("ventas/comprobante_model");
        // parametros:
        // tipo_orden : para la operacion, COMPRA o VENTA en la OC
        // tipo_guia : para la operacion, COMPRA o VENTA en la GUIA
        // cod_orden : codigo de la OC
        // cod_prod : codigo del producto
        // $data = $ci->guiarem_model->buscar_x_producto_orden('C','C',2,32);
        $data = $ci->guiarem_model->buscar_x_producto_orden($tipo_orden,$tipo_guia,$cod_orden,$cod_prod);
		
		$suma = 0;
        if(is_array($data) > 0){
            foreach($data as $valor){
                $suma += $valor->GUIAREMDETC_Cantidad;
            }
        }
		
		$dataCom = $cic->comprobante_model->buscar_x_producto_orden($tipo_orden,$tipo_guia,$cod_orden,$cod_prod);
		
		$sumaCom = 0;
        if(is_array($dataCom) > 0){
            foreach($dataCom as $valor){
                $sumaCom += $valor->CPDEC_Cantidad;
            }
        }
		
		
        return $suma+$sumaCom;
    }
}