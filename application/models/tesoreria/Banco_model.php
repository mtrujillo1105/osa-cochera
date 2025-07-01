<?php
class Banco_Model extends CI_Model{

    protected $_name = "cji_banco";
    
    public function  __construct(){
        parent::__construct();
    }

    ##########################
    ##### FUNCTIONS NEWS
    ##########################

    public function getBancosCliente($cliente){
        $sql = "SELECT ce.*, b.BANC_Nombre, b.BANC_Siglas, m.MONED_Simbolo, m.MONED_Descripcion
                    FROM cji_cuentasempresas ce
                    INNER JOIN cji_banco b ON b.BANP_Codigo = ce.BANP_Codigo
                    INNER JOIN cji_moneda m ON m.MONED_Codigo = ce.MONED_Codigo
                    WHERE 
                        CASE
                            WHEN ce.EMPRE_Codigo <> 0 THEN ce.EMPRE_Codigo = (SELECT c.EMPRP_Codigo FROM cji_cliente c WHERE c.CLIP_Codigo = $cliente)
                            WHEN ce.EMPRE_Codigo = 0 THEN ce.PERSP_Codigo = (SELECT c.PERSP_Codigo FROM cji_cliente c WHERE c.CLIP_Codigo = $cliente)
                            ELSE ''
                        END AND ce.CUENT_FlagEstado LIKE '1'
                    ORDER BY b.BANC_Nombre ASC 
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getBancosProveedor($proveedor){
        $sql = "SELECT ce.*, b.BANC_Nombre, b.BANC_Siglas, m.MONED_Simbolo, m.MONED_Descripcion
                    FROM cji_cuentasempresas ce
                    INNER JOIN cji_banco b ON b.BANP_Codigo = ce.BANP_Codigo
                    INNER JOIN cji_moneda m ON m.MONED_Codigo = ce.MONED_Codigo
                    WHERE 
                        CASE
                            WHEN ce.EMPRE_Codigo <> 0 THEN ce.EMPRE_Codigo = (SELECT p.EMPRP_Codigo FROM cji_proveedor p WHERE p.PROVP_Codigo = $proveedor)
                            WHEN ce.EMPRE_Codigo = 0 THEN ce.PERSP_Codigo = (SELECT p.PERSP_Codigo FROM cji_proveedor p WHERE p.PROVP_Codigo = $proveedor)
                            ELSE ''
                        END AND ce.CUENT_FlagEstado LIKE '1'
                    ORDER BY b.BANC_Nombre ASC 
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getBancosEmpresa($empresa){
        $sql = "SELECT ce.*, b.BANC_Nombre, b.BANC_Siglas, m.MONED_Simbolo, m.MONED_Descripcion
                    FROM cji_cuentasempresas ce
                    INNER JOIN cji_banco b ON b.BANP_Codigo = ce.BANP_Codigo
                    INNER JOIN cji_moneda m ON m.MONED_Codigo = ce.MONED_Codigo
                    WHERE ce.EMPRE_Codigo = '$empresa' AND ce.CUENT_FlagEstado LIKE '1'
                    ORDER BY b.BANC_Nombre ASC 
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    ##########################
    ##### FUNCTIONS OLDS
    ##########################
    
    public function listar(){

        $where = array("BANC_FlagEstado"=>"1");
        $query = $this->db->order_by('BANC_Nombre')->get('cji_banco');

        if($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
    
    
    public function listar_banco(){
        $sql = "SELECT * FROM cji_banco WHERE BANC_FlagEstado = 1 ORDER BY BANC_Nombre";
        $query = $this->db->query($sql);

        if($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
    
    
    public function obtener($banco){
        $query = $this->db->where('BANP_Codigo',$banco)->get('cji_banco');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtenerCuenta($cuenta){
        $query = $this->db->where('CUENT_Codigo',$cuenta)->get('cji_cuentasempresas');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_by_id_empresa($id_empresa){
        return $this->db->from("cji_cuentasempresas")
                        ->join("cji_banco", "cji_banco.BANP_Codigo = cji_cuentasempresas.BANP_Codigo")
                        ->where("cji_cuentasempresas.EMPRE_Codigo", $id_empresa)
                        ->order_by("cji_banco.BANC_Nombre")
                        ->select("cji_banco.*")
                        ->get()->result();
    }

    public function listar_movimientos(Array $filter, $page = 1, $per_page = 20){
        $query = $this->db->from("cji_cajamovimiento");

        $query->join("cji_cuentasempresas", "cji_cuentasempresas.CUENT_Codigo = cji_cajamovimiento.CUENT_Codigo_G OR cji_cuentasempresas.CUENT_Codigo = cji_cajamovimiento.CUENT_Codigo_B");
        $query->join("cji_pago", "cji_pago.PAGP_Codigo = cji_cajamovimiento.PAGP_Codigo");
        $query->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_pago.MONED_Codigo");

        if(isset($filter["id_banco"])) {
            $query->where("cji_cuentasempresas.BANP_Codigo", $filter["id_banco"]);
        }

        if(isset($filter["id_cuenta"])) {
            $query->where("cji_cuentasempresas.CUENT_Codigo", $filter["id_cuenta"]);
        }

        $query->order_by("cji_cajamovimiento.CAJAMOV_FechaSistema", "DESC");

        return $query->select("cji_cajamovimiento.*, cji_cuentasempresas.*, cji_pago.*, cji_moneda.MONED_Simbolo")->get()->result();
    }

    public function ctas_bancarias($empresa){
        $sql = "SELECT ce.*, b.BANC_Nombre, b.BANC_Siglas, m.MONED_Simbolo, m.MONED_Descripcion
                    FROM cji_cuentasempresas ce
                    INNER JOIN cji_banco b ON b.BANP_Codigo = ce.BANP_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = ce.MONED_Codigo
                    WHERE CUENT_FlagEstado = 1 AND ce.EMPRE_Codigo = '$empresa'
                ";
        $query = $this->db->query($sql);
        $data = array();

        if ($query->num_rows() > 0){
            foreach ($query->result() as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }
   
}
?>