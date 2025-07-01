<?php

class Cheque_Model extends CI_Model {

    protected $_name = "cji_cheque";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('pago');
    }

    public function obtener($cheque) {
        $where = array("cji_cheque.CHEP_Codigo" => $cheque);

        $query = $this->db->order_by('CHEC_FechaRegistro')
                ->where($where)
                ->join('cji_pago', 'cji_pago.CHEP_Codigo = cji_cheque.CHEP_Codigo', 'left')
                ->join('cji_moneda', 'cji_moneda.MONED_Codigo = cji_pago.MONED_Codigo', 'left')
                ->select('cji_cheque.*,cji_pago.PAGC_Monto, cji_moneda.MONED_Simbolo, cji_pago.CLIP_Codigo')
                ->from('cji_cheque')
                ->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function listar($number_items = '', $offset = '', $filter = null) {
        $where = array("cji_cheque.COMPP_Codigo" => $this->session->userdata('compania'), "cji_cheque.CHEC_FlagEstado" => '1');
        if (isset($filter->numero) && $filter->numero != '') {
            $this->db->like('cji_cheque.CHEC_Nro', $filter->numero,'both');
        }

        if (isset($filter->tipo_cheque) && $filter->tipo_cheque != '') {
            $this->db->where('cji_pago.PAGC_TipoCuenta', $filter->tipo_cheque);
        }
        $query = $this->db->order_by('CHEC_FechaRegistro')
                ->where($where)
                ->join('cji_pago', 'cji_pago.CHEP_Codigo = cji_cheque.CHEP_Codigo', 'left')
                ->join('cji_moneda', 'cji_moneda.MONED_Codigo = cji_pago.MONED_Codigo', 'left')
                ->select('cji_cheque.*,cji_pago.PAGC_Monto, cji_moneda.MONED_Simbolo, cji_pago.CLIP_Codigo,cji_pago.PROVP_Codigo')
                ->from('cji_cheque', $number_items, $offset)
                ->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function insertar($filter = null) {
        $this->db->insert("cji_cheque", $filter);
        $id = $this->db->insert_id();
        return $id;
    }

    public function modificar($id, $filter) {
        $this->db->where("CHEP_Codigo", $id);
        $this->db->update("cji_cheque ", (array) $filter);
    }

}

?>