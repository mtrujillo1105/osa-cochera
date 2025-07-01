<?php
/* *********************************************************************************
/* ******************************************************************************** */
class Tipocaja_model extends CI_Model {

    private $empresa;
    private $compania;

    public function __construct() {
        parent::__construct();
        $this->load->helper('date');
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
    }

    #########################
    ###### FUNCTIONS NEWS
    #########################

        public function getTipoCajas($filter = NULL) {

            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

            $where = '';
            if (isset($filter->descripcion) && $filter->descripcion != '')
                $where .= " AND tp.tipCa_Descripcion LIKE '%$filter->descripcion%'";

            $sql = "SELECT tp.* FROM cji_tipocaja tp WHERE tp.tipCa_FlagEstado LIKE '1' AND tp.COMPP_Codigo = $this->compania $where $order $limit";

            $query = $this->db->query($sql);
            if ($query->num_rows() > 0)
                return $query->result();
            else
                return array();
        }

        public function getTipoCaja($codigo) {

            $sql = "SELECT tp.* FROM cji_tipocaja tp WHERE tp.tipCa_Codigo = $codigo";
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0)
                return $query->result();
            else
                return array();
        }

        public function insertar_TipoCaja($filter){
            $this->db->insert("cji_tipocaja", (array) $filter);
            return $this->db->insert_id();
        }

        public function actualizar_TipoCaja($tipocaja, $filter){
            $this->db->where('tipCa_Codigo',$tipocaja);
            return $this->db->update('cji_tipocaja', $filter);
        }

        public function deshabilitar_TipoCaja($tipocaja, $filter){
            $this->db->where('tipCa_Codigo',$tipocaja);
            $query = $this->db->update('cji_tipocaja', $filter);
            return $query;
        }

    #########################
    ###### FUNCTIONS OLDS
    #########################

    public function insert_tipocaja($filter = null){
    	$this->db->insert("cji_tipocaja", (array) $filter);
        $tipocaja = $this->db->insert_id();
        return $tipocaja;
    }
    public function tipocaja_listar_buscar($filter, $number_items = '', $offset = '') {
        $compania = $this->compania;
        
        $where = '';
       if(isset($filter->txtCodigoT) && $filter->txtCodigoT=="1"){
            $where.='and tipCa_Tipo= 1';
        }
        if(isset($filter->txtCodigoT) && $filter->txtCodigoT=="2"){
            $where.='and tipCa_Tipo= 2';
        }
        if(isset($filter->txtCodigoT) && $filter->txtCodigoT=="3"){
           $where.='and tipCa_Tipo= 1 or tipCa_Tipo= 2'; 
        }
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != ''){
        $where = ' and tipCa_FechaRegistro BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        }
      
       /* if (isset($filter->cliente) && $filter->cliente != '')
            $where.=' and cc.CLIP_Codigo=' . $filter->cliente;

        if (isset($filter->producto) && $filter->producto != '')
            $where.=' and p.PROYP_Codigo=' . $filter->producto;
        $limit = "";*/
        $limit = "";
        if ((string) $offset != '' && $number_items != ''){
            $limit = 'LIMIT ' . $offset . ',' . $number_items;
        }

        $sql = "SELECT tipCa_codigo,tipCa_Descripcion,	tipCa_Abreviaturas,tipCa_Tipo,UsuarioRegistro,UsuarioModificado,tipCa_fechaModificacion,tipCa_FechaRegistro,COMPP_Codigo,tipCa_FlagEstado
                FROM  cji_tipocaja
                WHERE COMPP_Codigo =" . $compania . " " . $where . "".
                " GROUP BY  tipCa_codigo "." 
               
                  ORDER BY  tipCa_codigo desc " . $limit . "

                ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }
     
     public function obtenerTipocaja($codigo){
     	$where = array('tipCa_codigo' => $codigo);
     	$query = $this->db->where($where)->get('cji_tipocaja');
     	if ($query->num_rows() > 0) {
     		foreach ($query->result() as $fila) {
     			$data[] = $fila;
     		}
     		return $data;
     	}
     }
     public function tipocaja_modificar($codigo, $filter=null){
            $where = array("tipCa_codigo"=>$codigo);
            $this->db->where($where);
            $this->db->update('cji_tipocaja',(array)$filter);
       
     }
     public function getActualizarTipoCaja($codigo){
      
         $data  = array("tipCa_FlagEstado"=>'0');
        $this->db->where('tipCa_codigo',$codigo);
        $this->db->update('cji_tipocaja',$data);
     }

}

?>