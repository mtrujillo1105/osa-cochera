<?php

class Inventario_model extends CI_Model {

    var $somevar;
    private $compania = NULL;

    public function __construct() {
        parent::__construct();
        #$this->load->database();
        $this->compania = $this->session->userdata('compania');
        $this->somevar['idcompania'] = $this->session->userdata('idcompania');
    }

    public function buscar_inventario($filter = null, $number_items = "", $offset = "") {

        if (isset($filter->cod_inventario) && $filter->cod_inventario != '')
            $this->db->where('cji_inventario.INVE_Codigo', $filter->cod_inventario);

        $compania = $this->compania;
		$this->db->where('cji_inventario.COMPP_Codigo', $compania);
        $this->db->orderby('cji_inventario.INVE_Codigo', 'DESC');
        //$query = $this->db->get('cji_inventario', $number_items, $offset);
        return $this->db->get('cji_inventario', $number_items, $offset)->result();


        /*if ($query->num_rows() > 0) {
            $data = array();

            foreach ($query->result() as $fila) {

                $data[] = $fila;
            }

            return $data;
        }*/

    }

    public function buscar_inventario_detalles($filter = null, $number_items = "", $offset = "") {

        $compania = $this->compania;

        $this->db->select('
                    cji_producto.PROD_Nombre,
                    cji_marca.MARCC_CodigoUsuario,
                    cji_producto.PROD_Codigo,
                    cji_producto.PROD_Presentacion,
        			cji_producto.PROD_GenericoIndividual,
                    cji_inventariodetalle.INVD_Codigo,
                    cji_inventariodetalle.INVD_FlagActivacion,
                    cji_inventariodetalle.INVE_Codigo,
                    cji_inventariodetalle.INVD_Cantidad,
                    cji_inventariodetalle.INVD_Pcosto,
                    cji_inventariodetalle.LOTC_Numero,
                    cji_inventariodetalle.LOTC_FechaVencimiento,
        			cji_inventario.ALMAP_Codigo
        		');

        if (isset($filter->codigo_inventario) && $filter->codigo_inventario != '') {
            $this->db->where('cji_inventariodetalle.INVE_Codigo', $filter->codigo_inventario);
        }
        if (isset($filter->codigo_detalle) && $filter->codigo_detalle != '') {
            $this->db->where('cji_inventariodetalle.INVD_Codigo', $filter->codigo_detalle);
        }
        
        if (isset($filter->PROD_Codigo) && $filter->PROD_Codigo != 0) {
        	$this->db->where('cji_inventariodetalle.PROD_Codigo', $filter->PROD_Codigo);
        }
        
        $this->db->where('cji_inventario.COMPP_Codigo', $compania);
        $this->db->join('cji_inventario', 'cji_inventario.INVE_Codigo = cji_inventariodetalle.INVE_Codigo ', 'INNER');
        $this->db->join('cji_producto', 'cji_producto.PROD_Codigo = cji_inventariodetalle.PROD_Codigo ', 'left');
        $this->db->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo ', 'left');
        $this->db->join('cji_almacenproducto', 'cji_almacenproducto.PROD_Codigo = cji_inventariodetalle.PROD_Codigo ', 'left');
        $this->db->join('cji_productoprecio', 'cji_productoprecio.PROD_Codigo = cji_inventariodetalle.PROD_Codigo ', 'left');
        $this->db->orderby('cji_inventariodetalle.INVD_Codigo', 'DESC');
        $this->db->group_by('cji_producto.PROD_Codigo');
        $query = $this->db->get('cji_inventariodetalle', $number_items, $offset);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function getProducto_Atributo($producto, $atributo) {

        $this->db->where(array('cji_productoatributo.ATRIB_Codigo' => $atributo, 'cji_productoatributo.PROD_Codigo' => $producto));
        $query = $this->db->get('cji_productoatributo');

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $fila) {

                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar($datos) {

        $filter = new stdClass();
        $filter->INVE_Titulo = $datos['titulo'];
        $filter->COMPP_Codigo= $this->compania;
        $filter->INVE_Serie = $datos['serie'];
        $filter->INVE_Numero = $datos['numero'];
        $filter->ALMAP_Codigo = $datos['almacen'];
        $fecha = explode("/",$datos['fecha_inicio']);
        $filter->INVE_FechaInicio = "$fecha[2]-$fecha[1]-$fecha[0]";
        $filter->INVE_FlagEstado = "1";

        $result = $this->db->insert("cji_inventario", (array) $filter);

        return $result;
    }

    public function editar($datos) {

        $filter = new stdClass();
        $filter->INVE_Titulo = $datos['titulo'];
        $filter->ALMAP_Codigo = $datos['almacen'];
        $fecha = explode("/",$datos['fecha_inicio']);
        $filter->INVE_FechaInicio = "$fecha[2]-$fecha[1]-$fecha[0]";

        $this->db->where('cji_inventario.INVE_Codigo', $datos['cod_inventario']);
        $result = $this->db->update("cji_inventario", (array) $filter);

        return $result;
    }

    public function insertar_detalle($datos) {

        $filter = new stdClass();
        $filter->INVE_Codigo = $datos['cod_inventario'];
        $filter->PROD_Codigo = $datos['cod_producto'];
        $filter->INVD_Cantidad = $datos['cantidad'];
        $filter->INVD_Pcosto = $datos['p_costo'];
        $filter->LOTC_Numero = $datos['numero_lote'];
        $filter->LOTC_FechaVencimiento = $datos['vencimiento_lote'];
        
        $filter->INVD_FechaRegistro = date('Y-m-d');

        $result = $this->db->insert("cji_inventariodetalle", (array) $filter);

        return $result;
    }

    public function editar_detalle($datos) {

        $filter = new stdClass();
        $filter->INVD_Cantidad = $datos['cantidad'];
		$filter->INVD_Pcosto = $datos['p_costo'];
        $filter->LOTC_Numero = $datos['numero_lote'];
        $filter->LOTC_FechaVencimiento = $datos['vencimiento_lote'];
		
        $this->db->where('cji_inventariodetalle.INVD_Codigo', $datos['cod_detalle']);
        $result = $this->db->update("cji_inventariodetalle", (array) $filter);

        return $result;
    }

    public function editar_detalle_activacion($codigo_detalle) {

        $filter = new stdClass();
        $filter->INVD_FlagActivacion = 1;

        $this->db->where('cji_inventariodetalle.INVD_Codigo', $codigo_detalle);
        $result = $this->db->update("cji_inventariodetalle", (array) $filter);

        return $result;
    }

    public function eliminar_detalle($datos) {

        $this->db->where('cji_inventariodetalle.INVD_Codigo', $datos['cod_detalle']);
        $result = $this->db->delete('cji_inventariodetalle');

        return $result;
    }

    public function count_inventario() {

        $this->db->select('COUNT(cji_inventario.INVE_Codigo) as conteo');
   $compania = $this->compania;
 $this->db->where('cji_inventario.COMPP_Codigo', $compania);
        $query = $this->db->get('cji_inventario');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
	
	///gcbq
	   public function activacion_inventario($datos) {

        $filter = new stdClass();
        $filter->INVE_FechaFin =  date('Y-m-d');
		$filter->INVE_FechaRegistro = date('Y-m-d');
		$filter->INVE_FlagEstado=1;
	
		
        $this->db->where('cji_inventario.INVE_Codigo', $datos['cod_inventario']);
        $result = $this->db->update("cji_inventario", (array) $filter);

        return $result;
    }
	 public function eliminar_inventario_detalles($codigo) {

        $this->db->where('cji_inventariodetalle.INVE_Codigo', $codigo);
        $result = $this->db->delete('cji_inventariodetalle');
		
		if ($result)
                $this->eliminar_inventario($codigo);
            
    }
	 public function eliminar_inventario($codigo) {

        $this->db->where('cji_inventario.INVE_Codigo', $codigo);
        $result = $this->db->delete('cji_inventario');

        return $result;
    }

    public function verificarProductoInventarios($codigoProducto){
    		$compania = $this->compania;
    		$this->db->select('cji_producto.PROD_Codigo');
    		$this->db->where('cji_inventariodetalle.PROD_Codigo',$codigoProducto);
    		$this->db->where('cji_inventariodetalle.INVD_FlagActivacion', 1);
    		$this->db->where('cji_inventario.COMPP_Codigo', $compania);
    		$this->db->join('cji_inventario', 'cji_inventario.INVE_Codigo = cji_inventariodetalle.INVE_Codigo ', 'INNER');
    		$this->db->join('cji_producto', 'cji_producto.PROD_Codigo = cji_inventariodetalle.PROD_Codigo ', 'INNER');
    		$this->db->join('cji_almacenproducto', 'cji_almacenproducto.PROD_Codigo = cji_inventariodetalle.PROD_Codigo ', 'INNER');
    		$this->db->orderby('cji_inventariodetalle.INVD_Codigo', 'DESC');
    		$this->db->group_by('cji_producto.PROD_Codigo');
    		$query = $this->db->get('cji_inventariodetalle');
    		if ($query->num_rows() > 0) {
    			foreach ($query->result() as $fila) {
    				$data[] = $fila;
    			}
    			return $data;
    		}
    }
    
    /**verificamos si el producto se encuentra inventariado y en el ese almacen**/
    public function verificarProductoInventarioAlmacen($codigoProducto,$almacen){
    	$compania = $this->compania;
    	$this->db->select('cji_inventario.ALMAP_Codigo');
    	$this->db->where('cji_inventariodetalle.PROD_Codigo',$codigoProducto);
    	$this->db->where('cji_inventariodetalle.INVD_FlagActivacion', 1);
    	$this->db->where('cji_inventario.COMPP_Codigo', $compania);
    	$this->db->where('cji_inventario.ALMAP_Codigo', $almacen);
    	$this->db->join('cji_inventario', 'cji_inventario.INVE_Codigo = cji_inventariodetalle.INVE_Codigo ', 'INNER');
    	$this->db->orderby('cji_inventariodetalle.INVD_Codigo', 'DESC');
    	$query = $this->db->get('cji_inventariodetalle');
    	if ($query->num_rows() > 0) {
    		foreach ($query->result() as $fila) {
    			$data[] = $fila;
    		}
    		return $data;
    	}
    	
    	
    }

    public function confirmInventariado($producto, $almacen){
        $this->load->model("almacen/almacenproducto_model");
        $this->load->model("almacen/guiain_model");
        $this->load->model("almacen/guiaindetalle_model");

        $sql = "SELECT p.* FROM cji_producto p WHERE p.PROD_Codigo = $producto";
        $psInfo = $this->db->query($sql);

        if ( $psInfo->num_rows > 0 ){
            $ps = $psInfo->result();
            if ( $ps[0]->PROD_FlagBienServicio == "B" ){
                $sql = "SELECT inv.*
                            FROM cji_inventario inv
                            INNER JOIN cji_inventariodetalle invd ON invd.INVE_Codigo = inv.INVE_Codigo
                            WHERE inv.ALMAP_Codigo = $almacen AND invd.PROD_Codigo = $producto
                        ";
                $query = $this->db->query($sql);

                if ( $query->num_rows() > 0 ){
                    return true;
                }
                else{
                    unset($query);

                    $sql = "SELECT MAX(INVE_Codigo) as INVE_Codigo
                            FROM cji_inventario inv
                            WHERE inv.ALMAP_Codigo = $almacen
                        ";
                    $query = $this->db->query($sql);
                    if ( $query->num_rows() > 0 ){
                        foreach ($query->result() as $key => $value){
                            $filter = new stdClass();
                            $filter->INVE_Codigo = $value->INVE_Codigo;
                            $filter->PROD_Codigo = $producto;
                            $filter->INVD_Cantidad = 0;
                            $filter->INVD_Pcosto = 0;
                            $filter->INVD_FechaRegistro = date('Y-m-d');
                            $filter->INVD_FlagActivacion = "1";
                            $result = $this->db->insert("cji_inventariodetalle", (array) $filter);

                            #####################################################
                            ###### INSERTAMOS EN EL ALMACEN
                            #####################################################
                                $cdInventario = new stdClass();
                                $cdInventario->cod_inventario = $value->INVE_Codigo;
                                $datos_inventario = $this->buscar_inventario($cdInventario);
                                $codigoAlmacenProducto = $this->almacenproducto_model->aumentar($datos_inventario[0]->ALMAP_Codigo, $producto, 0, 0); // Suma cantidad ingresada

                            #####################################################
                            ###### CREAMOS LA GUIA DE INGRESO
                            #####################################################
                                $cGuiaI = new stdClass();
                                $cGuiaI->TIPOMOVP_Codigo = 2;
                                $cGuiaI->ALMAP_Codigo = $almacen;
                                $cGuiaI->PROVP_Codigo = null;
                                $cGuiaI->DOCUP_Codigo = 4;
                                $cGuiaI->GUIAINC_Fecha = date('Y-m-d h:m:s');
                                $cGuiaI->GUIAINC_Observacion = '';
                                $cGuiaI->USUA_Codigo = $_SESSION['user'];
                                $cGuiaI->GUIAINC_Automatico = 1;
                                $cGuiaI->GUIAINC_NumeroRef = $value->INVE_Codigo;
                                $guia_id = $this->guiain_model->insertar($cGuiaI);
                            
                            #####################################################
                            ###### INSERTAMOS EL PRODUCTO EN LA GUIA DE INGRESO
                            #####################################################       
                                $cGuiaId = new stdClass();
                                $cGuiaId->GUIAINP_Codigo = $guia_id;
                                $cGuiaId->PRODCTOP_Codigo = $producto;
                                $cGuiaId->ALMAP_Codigo = $almacen;
                                $cGuiaId->UNDMED_Codigo = 1;
                                $cGuiaId->GUIIAINDETC_GenInd = "G";
                                $cGuiaId->GUIAINDETC_Cantidad = "0";
                                $cGuiaId->GUIAINDETC_Costo = "0";
                                $cGuiaId->GUIAINDETC_Descripcion = '';
                                $cGuiaId->ALMAP_Codigo = $almacen;
                                $this->guiaindetalle_model->insertar($cGuiaId, false); # false para no ingresar al kardex

                            #####################################################
                            ###### CREAMOS EL LOTE
                            #####################################################
                                #$dLote = new stdClass();
                                #$dLote->PROD_Codigo = $producto;
                                #$dLote->LOTC_Cantidad = "0";
                                #$dLote->LOTC_Costo = "0";
                                #$dLote->GUIAINP_Codigo = $guia_id;
                                #$lote = $this->lote_model->insertar($dLote);
                                #$this->almaprolote_model->aumentar($almacen, $lote, 0, 0);

                            #####################################################
                            ###### INSERTAMOS EL MOVIMIENTO EN EL KARDEX
                            #####################################################
                                #$cKardex = new stdClass();
                                #$cKardex->KARD_Fecha = date('Y-m-d h:m:s');
                                #$cKardex->KARDC_Cantidad = 0;
                                #$cKardex->PROD_Codigo = $producto;
                                #$cKardex->KARDC_Costo = 0;
                                #$cKardex->KARDC_TipoIngreso = 3;
                                #$cKardex->LOTP_Codigo = $lote;
                                #$cKardex->TIPOMOVP_Codigo = NULL;
                                #$cKardex->KARDC_CodigoDoc = $value->INVE_Codigo;
                                #$cKardex->ALMPROD_Codigo = $almacen;
                                #$cKardex->KARDP_FlagEstado = 1;
                                #$this->kardex_model->insertar(4, $cKardex);
                        }
                    }
                }
            }
        }
    }

}