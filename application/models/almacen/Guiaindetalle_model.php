<?php

class Guiaindetalle_Model extends CI_Model {

    protected $_name = "cji_guiaindetalle";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('almacen/kardex_model');
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/familia_model');
        $this->load->model('almacen/almaprolote_model');
        $this->load->model('almacen/almacenproductoserie_model');
        $this->load->model('almacen/serie_model');
        $this->load->model('almacen/seriedocumento_model');
        $this->load->model('almacen/seriemov_model');
        $this->load->model('almacen/productounidad_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('almacen/guiarem_model');

        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
    }

    public function listar($number_items = '', $offset = '') {
        $where = array("GUIAINDETC_FlagEstado" => 1);
        $query = $this->db->order_by('GUIAINDETP_Codigo')->where($where)->get('cji_guiaindetalle', $number_items, $offset);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function obtener($id) {
        $where = array("GUIAINDETP_Codigo" => $id);
        $query = $this->db->where($where)->get('cji_guiaindetalle', 1);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function obtener2($guia_in) {
        $where = array("GUIAINP_Codigo" => $guia_in);
        $query = $this->db->where($where)->get('cji_guiaindetalle');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    function obtenerCosto(stdClass $filter = null) {
        $moneda_id = '';
        if (isset($filter->OCOMP_Codigo) && $filter->OCOMP_Codigo != '') {
            $ocompra = $filter->OCOMP_Codigo;
            $datos_ocompra = $this->ocompra_model->obtener_ocompra($ocompra);
            $moneda_id = $datos_ocompra[0]->MONED_Codigo;
        } elseif (isset($filter->GUIAREMP_Codigo) && $filter->GUIAREMP_Codigo != '') {
            $guiarem = $filter->GUIAREMP_Codigo;
            $datos_guiarem = $this->guiarem_model->obtener($guiarem);
            $moneda_id = $datos_guiarem[0]->MONED_Codigo;
        }
        /* Aplico tipo de cambio */
        $costo = 0;
        $costo = $filter->GUIAINDETC_Costo;
        if ($moneda_id !== '' && $moneda_id != 1) {
            $tipocambio = $this->tipocambio_model->obtener2($moneda_id);
            $tc = $tipocambio->TIPCAMC_FactorConversion;
            $costo = $filter->GUIAINDETC_Costo * $tc;
        }
        return $costo;
    }

    public function insertar(stdClass $filter = null, $kardex = true) {
    	
    	$this->db->insert("cji_guiaindetalle", (array) $filter);
        $guia_id = $filter->GUIAINP_Codigo;
        $producto_id = $filter->PRODCTOP_Codigo;
        $umedida_id = $filter->UNDMED_Codigo;
        $cantidad = $filter->GUIAINDETC_Cantidad;
        
        $costo = $this->obtenerCosto($filter);
        if (is_null($costo))
            $costo = 0;
        
        $guia = $this->guiain_model->obtener($guia_id);
        $fecha = $guia[0]->GUIAINC_FechaRegistro;
        $motivo_mov = $guia[0]->TIPOMOVP_Codigo;
        $almacen_id = $guia[0]->ALMAP_Codigo;
        /* obtener famila de producto */
        $producto_datos = $this->producto_model->obtener_producto($producto_id);
        $familia = $producto_datos[0]->FAMI_Codigo;
        /* Convierto de unidades si es necesario */
        $productoundad = $this->productounidad_model->obtener($producto_id, $umedida_id);
        if ($productoundad) {
            $flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
            $factor = $productoundad->PRODUNIC_Factor;
            if ($flagPrincipal == 0) {
                //$cantidad = 0;
                if ($factor > 0){
                    $cantidad = $cantidad / $factor;
                    if(strpos($cantidad,".")==true){
                        $cantidad=round($cantidad,3);
                    }

                }

            }
        }
        /*
          
        /* Inserto las series si hubiesen */
        $arrserie = array();
        if ($filter->GUIIAINDETC_GenInd == "I") {
            /**obtenemos serie de ese producto **/
            $filterSerie= new stdClass();
            $filterSerie->PROD_Codigo=$producto_id;
            $filterSerie->SERIC_FlagEstado='1';
            $filterSerie->DOCUP_Codigo=10;
            $filterSerie->SERDOC_NumeroRef=$id_guiatrans;
            $filterSerie->ALMAP_Codigo=$almacen_origen;
            $listaSeriesProducto=$this->seriedocumento_model->buscar($filterSerie,null,null);
            if($listaSeriesProducto!=null && count($listaSeriesProducto)>0){
                foreach($listaSeriesProducto as $serieValor){
                    /**lo ingresamos como se ssion ah 2 variables 1:session que se muestra , 2:sesion que queda intacta bd
                     * cuando se actualice la session  1 se compara con la session 2.**/
                    $codigoSerie=$serieValor->SERIP_Codigo;
                    /**si es venta lo seleccionamos en almacenproduyctoserie capaz exita perdida de datos**/
                
                    /**fin de seleccion verificacion**/
                    if($query = $this->db->query("CALL MANTENIMIENTO_SERIEMOVIMIENTO(@SERMOVP_Codigo,$codigoSerie,1,NULL,$guia_id,0)"))
                    {
                        $datos_almaprod = $this->almacenproducto_model->obtener($almacen_id, $producto_id);
                        $ALMPROD_Codigo=$datos_almaprod[0]->ALMPROD_Codigo;
                        if($query1 = $this->db->query("CALL MANTENIMIENTO_ALMACENPRODUCTOSERIE(@ALMPRODSERP_Codigo,$ALMPROD_Codigo,$codigoSerie,0)")){
                        }
                    }
                    /*****/
                }
            }
        }

        if ($kardex == true){
            /* Inserto en el almacen */
            $almacenproducto_id = $this->almacenproducto_model->aumentar($almacen_id, $producto_id, $cantidad, $costo);

            /* Inserto lote en almacenproducto_lote */
            $filter3 = new stdClass();
            $filter3->PROD_Codigo = $producto_id;
            $filter3->LOTC_Cantidad = $cantidad;
            $filter3->LOTC_Costo = $costo;
            $filter3->GUIAINP_Codigo = $guia_id;
            $lote = $this->lote_model->insertar($filter3);
            $this->almaprolote_model->aumentar($almacenproducto_id, $lote, $cantidad, $costo);

            $filter2 = new stdClass();
            $filter2->PROD_Codigo = $producto_id;
            $filter2->DOCUP_Codigo = '5';
            $filter2->TIPOMOVP_Codigo = $motivo_mov;
            $filter2->KARDC_CodigoDoc = $guia_id;
            $filter2->KARD_Fecha = $fecha;
            $filter2->KARDC_Cantidad = $cantidad;
            $filter2->KARDC_Costo = $costo;
            $filter2->LOTP_Codigo = $lote;
            $filter2->ALMPROD_Codigo=$almacenproducto_id;
            $insertarKardex = $this->kardex_model->insertar(5, $filter2);
            return $insertarKardex;
        }
    }

    
    public function insertar_2015(stdClass $filter = null, $modo = 'GUIAREM',$id_guiatrans,$almacen_origen)
    {
        $this->db->insert("cji_guiaindetalle", (array) $filter);
        $guia_id = $filter->GUIAINP_Codigo;
        $producto_id = $filter->PRODCTOP_Codigo;
        $umedida_id = $filter->UNDMED_Codigo;
        $cantidad = $filter->GUIAINDETC_Cantidad;
        
        $costo = $this->obtenerCosto($filter);
        if (is_null($costo))
            $costo = 0;
        
        $guia = $this->guiain_model->obtener($guia_id);
        $fecha = $guia[0]->GUIAINC_FechaRegistro;
        $motivo_mov = $guia[0]->TIPOMOVP_Codigo;
        $almacen_id = $guia[0]->ALMAP_Codigo;
        /* obtener famila de producto */
        $producto_datos = $this->producto_model->obtener_producto($producto_id);
        $familia = $producto_datos[0]->FAMI_Codigo;
        /* Convierto de unidades si es necesario */
        $productoundad = $this->productounidad_model->obtener($producto_id, $umedida_id);
        if ($productoundad) {
            $flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
            $factor = $productoundad->PRODUNIC_Factor;
            if ($flagPrincipal == 0) {
                //$cantidad = 0;
                if ($factor > 0){
                    $cantidad = $cantidad / $factor;
                    if(strpos($cantidad,".")==true){
                        $cantidad=round($cantidad,3);
                    }

                }

            }
        }
        /*
          
        /* Inserto las series si hubiesen */
        $arrserie = array();
        if ($filter->GUIIAINDETC_GenInd == "I") {
            
        	/**obtenemos serie de ese producto **/
        	$filterSerie= new stdClass();
        	$filterSerie->PROD_Codigo=$producto_id;
        	$filterSerie->SERIC_FlagEstado='1';
        	$filterSerie->DOCUP_Codigo=10;
        	$filterSerie->SERDOC_NumeroRef=$id_guiatrans;
        	$filterSerie->ALMAP_Codigo=$almacen_origen;
        	$listaSeriesProducto=$this->seriedocumento_model->buscar($filterSerie,null,null);
        	if($listaSeriesProducto!=null && count($listaSeriesProducto)>0){
        		foreach($listaSeriesProducto as $serieValor){
        			/**lo ingresamos como se ssion ah 2 variables 1:session que se muestra , 2:sesion que queda intacta bd
        			 * cuando se actualice la session  1 se compara con la session 2.**/
        			$codigoSerie=$serieValor->SERIP_Codigo;
        			/**si es venta lo seleccionamos en almacenproduyctoserie capaz exita perdida de datos**/
        		
        			/**fin de seleccion verificacion**/
        			if($query = $this->db->query("CALL MANTENIMIENTO_SERIEMOVIMIENTO(@SERMOVP_Codigo,$codigoSerie,1,NULL,$guia_id,0)"))
        			{
        				$datos_almaprod = $this->almacenproducto_model->obtener($almacen_id, $producto_id);
        				$ALMPROD_Codigo=$datos_almaprod[0]->ALMPROD_Codigo;
        				if($query1 = $this->db->query("CALL MANTENIMIENTO_ALMACENPRODUCTOSERIE(@ALMPRODSERP_Codigo,$ALMPROD_Codigo,$codigoSerie,0)")){
        				}
        			}
        			/*****/
        		}
        	}
        	
        	
        }

        /* Inserto en el almacen */
        $almacenproducto_id = $this->almacenproducto_model->aumentar($almacen_id, $producto_id, $cantidad, $costo);

        $sql = "SELECT * FROM cji_inventariodetalle";
    
        /* Inserto lote en almacenproducto_lote */
        $filter3 = new stdClass();
        $filter3->PROD_Codigo = $producto_id;
        $filter3->LOTC_Cantidad = $cantidad;
        $filter3->LOTC_Costo = $costo;
        $filter3->GUIAINP_Codigo = $guia_id;
        $lote = $this->lote_model->insertar($filter3);
        $this->almaprolote_model->aumentar($almacenproducto_id, $lote, $cantidad, $costo);

        $filter2 = new stdClass();
        $filter2->PROD_Codigo = $producto_id;
        $filter2->DOCUP_Codigo = '5';
        $filter2->TIPOMOVP_Codigo = $motivo_mov;
        $filter2->KARDC_CodigoDoc = $guia_id;
        $filter2->KARD_Fecha = $fecha;
        $filter2->KARDC_Cantidad = $cantidad;
        $filter2->KARDC_Costo = $costo;
        $filter2->LOTP_Codigo = $lote;
        $filter2->ALMPROD_Codigo=$almacenproducto_id;
        $insertarKardex = $this->kardex_model->insertar(5, $filter2);
        return $insertarKardex;
    }

    public function eliminar($id) {
        $this->db->delete('cji_guiaindetalle', array('GUIAINDETP_Codigo' => $id));
    }

    public function eliminar2($guiain_id) {
        /*
         * Elimino registros del kardex.
         * Elimino almacenprodlote
         * Elimino lotes.
         */
        $datos_guia = $this->guiain_model->obtener($guiain_id);
        $almacen_id = $datos_guia[0]->ALMAP_Codigo;
        $datos_kardex = $this->kardex_model->obtener('5', $guiain_id);
        foreach ($datos_kardex as $value) {
            $lote_id = $value->LOTP_Codigo;
            $cantidad = $value->KARDC_Cantidad;
            $producto_id = $value->PROD_Codigo;
            $datos_almacenproducto = $this->almacenproducto_model->obtener($almacen_id, $producto_id);
            $almacenproducto_id = $datos_almacenproducto[0]->ALMPROD_Codigo;
            $this->almaprolote_model->eliminar($almacenproducto_id, $lote_id);
            $this->kardex_model->eliminar('5', $guiain_id, $producto_id);
            //$this->lote_model->eliminar($lote_id);
        }
        /*         * Disminuir stock(almacenproducto)
         * Eliminar almacenproductoserie
         * Eliminar serie
         */
        $noc = $this->obtener2($guiain_id);
        if (count($noc) > 0) {
            foreach ($this->obtener2($guiain_id) as $value) {
                $producto_id = $value->PRODCTOP_Codigo;
                $cantidad = $value->GUIAINDETC_Cantidad;
                $costo = $value->GUIAINDETC_Costo;
                $this->almacenproducto_model->disminuir($almacen_id, $producto_id, $cantidad, $costo);
                $datos_almacenproducto = $this->almacenproducto_model->obtener($almacen_id, $producto_id);
                $almacenproducto_id = $datos_almacenproducto[0]->ALMPROD_Codigo;
                $datos_serie = $this->almacenproductoserie_model->listar($almacenproducto_id);
                if (count($datos_serie) > 0) {
                    $lista_series = $this->serie_model->listar_x_codigodoc($producto_id, '1', $guiain_id);
                    foreach ($lista_series as $serie) {
                        $this->almacenproductoserie_model->eliminar2($serie->SERIP_Codigo);
                        $this->seriemov_model->eliminar($serie->SERMOVP_Codigo);
                        $lista_seriemov = $this->seriemov_model->listar($serie->SERIP_Codigo);
                        if (count($lista_seriemov) == 0) //si ya no existen movimientos para la serie, la elimino
                            $this->serie_model->eliminar2($serie->SERIP_Codigo);
                    }
                    //$this->serie_model->eliminar($producto_id,$filter3);
                    //$this->almacenproductoserie_model->eliminar($almacenproducto_id); 
                }
            }
        }
        /* Elimino registros del detalle */
        $this->db->delete('cji_guiaindetalle', array('GUIAINP_Codigo' => $guiain_id));
    }
    
    public function obtenerCostoOtro($cod_prod){
    	try {
    		
    		$compania = $this->somevar['compania'];
    		
    		$sql = "SELECT gd.GUIAINDETC_Costo 
    				FROM  cji_guiaindetalle gd, cji_guiain g  
    				WHERE gd.GUIAINP_Codigo=g.GUIAINP_Codigo 
    				and g.GUIAINC_FlagEstado=1 
    				and gd.PRODCTOP_Codigo=" . $cod_prod . " order by gd.GUIAINDETP_Codigo desc limit 1";
    		
    		$query = $this->db->query($sql);
    		if ($query->num_rows() > 0) {
    			foreach ($query->result() as $fila) {
    				$data[] = $fila;
    			}
    			return $data;
    		}
    		
    	} catch (Exception $e) {
    		
    		
    	}
    	
    	
    }

}

?>