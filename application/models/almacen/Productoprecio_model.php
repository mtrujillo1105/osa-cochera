<?php

class Productoprecio_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    ############################
    ##### FUNCTIONS NEWS
    ############################

        public function getProductoPrecios($producto){
            $sql = "SELECT *
                        FROM cji_productoprecio pp
                        WHERE pp.PROD_Codigo = $producto
                    ";
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0)
                return $query->result();
            else
                return NULL;
        }

        public function insertarNvoRegistro($filter){
            $this->db->insert("cji_productoprecio", (array) $filter);
            return $this->db->insert_id();
        }

        public function cleanRegistro($producto){
            $sql = "DELETE FROM cji_productoprecio WHERE PROD_Codigo = $producto";
            $this->db->query($sql);
        }

        public function actualizar_producto_precio($codigo, $filter){
            $this->db->where('PRODPREP_Codigo',$codigo);
            return $this->db->update('cji_productoprecio', $filter);
        }
        
    ############################
    ##### FUNCTIONS OLDS
    ############################

    public function obtener($prodprec) {
        $where = array("PRODPREP_Codigo" => $prodprec, "PRODPREC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_productoprecio');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtenerprecioA($producto, $emprestablecimiento) {
        $where = array("PROD_Codigo" => $producto, "EESTABP_Codigo" => $emprestablecimiento, "PRODPREC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_productoprecio');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtenerstockA($producto, $emprestablecimiento) {
        $where = array("cji_producto.PROD_Codigo" => $producto, "EESTABP_Codigo" => $emprestablecimiento, "PRODPREC_FlagEstado" => "1");
        $this->db->join('cji_producto', 'cji_producto.PROD_Codigo=cji_productoprecio.PROD_Codigo');
        $this->db->where($where);
        $query = $this->db->get('cji_productoprecio');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar(stdClass $filter = null) {
        $this->db->insert("cji_productoprecio", (array) $filter);
    }

    public function modificar($id, $filter) {
        $this->db->where("PRODPREP_Codigo", $id);
        $this->db->update("cji_productoprecio", (array) $filter);
    }

    public function eliminar($id) {
        $this->db->delete('cji_productoprecio', array('PRODPREP_Codigo' => $id));
    }

    public function eliminar_varios(stdClass $filter) {
        $where = array();
        $where['PROD_Codigo'] = $filter->PROD_Codigo;

        if (isset($filter->PRODUNIP_Codigo) && $filter->PRODUNIP_Codigo != '')
            $where['PRODUNIP_Codigo'] = $filter->PRODUNIP_Codigo;

        $this->db->delete('cji_productoprecio', $where);
    }

    public function buscar($filter) {
        if (isset($filter->PROD_Codigo))
            $this->db->where('PROD_Codigo', $filter->PROD_Codigo);
        if (isset($filter->MONED_Codigo) && $filter->MONED_Codigo != "")
            $this->db->where('MONED_Codigo', $filter->MONED_Codigo);
        if (isset($filter->PRODUNIP_Codigo) && $filter->PRODUNIP_Codigo != "")
            $this->db->where('PRODUNIP_Codigo', $filter->PRODUNIP_Codigo);
        if (isset($filter->TIPCLIP_Codigo))
            $this->db->where('TIPCLIP_Codigo', $filter->TIPCLIP_Codigo);
        if (isset($filter->EESTABP_Codigo))
            $this->db->where('EESTABP_Codigo', $filter->EESTABP_Codigo);

        $query = $this->db->where('PRODPREC_FlagEstado', '1')->get('cji_productoprecio');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }else
            return array();
    }

    public function listar_precios_x_producto_unidad($producto, $unidad, $moneda) {
        //echo $unidad;exit;
        ///////////////
        /////////
        $where = array('pp.PROD_Codigo' => $producto, 'um.UNDMED_Codigo' => $unidad, 'm.MONED_Codigo' => $moneda);
        $this->db->from('cji_productoprecio pp');
        $this->db->select('*');
        $this->db->join('cji_moneda m', 'm.MONED_Codigo = pp.MONED_Codigo');
        $this->db->join('cji_productounidad pu', 'pu.PRODUNIP_Codigo = pp.PRODUNIP_Codigo');
        $this->db->join('cji_unidadmedida um', 'um.UNDMED_Codigo  = pu.UNDMED_Codigo ');
        $this->db->join('cji_emprestablecimiento es', 'es.EESTABP_Codigo  = pp.EESTABP_Codigo ');
        $query = $this->db->where($where)->get('');
        // var_dump($this->db->last_query());
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function optener_precio_producto($codigo_producto){

         $sql = "SELECT PRODPREC_Precio FROM cji_productoprecio cpp inner join  cji_producto cp on cpp.PRODPREP_Codigo=cp.PROD_Codigo
                where cp.PROD_Codigo=$codigo_producto ";
                    
            $query = $this->db->query($sql);
           if ($query->num_rows() > 0) {
                foreach ($query->result() as $fila) {
                    $data[] = $fila;
                }
                return $data;
            }
       
    }

}

?>