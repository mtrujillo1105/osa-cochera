<?php

class Letra_model extends CI_Model {

    var $somevar;
    private $compania;

    public function __construct() {
        parent::__construct();
        $this->load->helper('date');
        $this->load->model('configuracion_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('almacen/seriemov_model');
        $this->load->model('ventas/comprobantedetalle_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/productounidad_model');
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/almaprolote_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('almacen/cuentaspago_model');
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/kardex_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());

        $this->compania = $this->session->userdata('compania');
    }

    public function listar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CPC_FechaRegistro', 'DESC')->where($where)->get('cji_comprobante', $number_items, $offset);  //order_by('CPC_Serie','desc')->order_by('CPC_Numero','desc')
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_comprobantes_factura($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CPC_Serie', 'desc')->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function getLetras($filter = NULL) {

        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_oper = $filter->tipo_oper;
        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->seriec) && $filter->seriec != '')
            $where .= ' and cp.CPC_Serie="' . $filter->seriec . '"';

        if (isset($filter->numeroc) && $filter->numeroc != '')
            $where .= ' and cp.CPC_Numero=' . $filter->numeroc;

        if (isset($filter->serie) && $filter->serie != '')
            $where .= ' and l.LET_Serie="' . $filter->serie . '"';

        if (isset($filter->numero) && $filter->numero != '')
            $where .= ' and l.LET_Numero=' . $filter->numero;

        if (isset($filter->estado_pago) && $filter->estado_pago != '')
            $where .= ' and l.LET_FlagEstado=' . $filter->estado_pago;
        
        if( isset($filter->fecha_ini) && $filter->fecha_ini != "" && isset($filter->fecha_fin) && $filter->fecha_fin != "" ){
            $where .= " AND l.LET_Fecha BETWEEN '$filter->fecha_ini 00:00:00' AND '$filter->fecha_fin 23:59:59' ";
        }

        if ($tipo_oper == 'V') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' AND (EMPRC_RazonSocial LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR CLIC_CodigoUsuario LIKE "%' . $filter->nombre_cliente . '%" )';
            }
            if (isset($filter->ruc_cliente) && $filter->ruc_cliente != '') {
                $where .= ' and (EMPRC_Ruc LIKE "%' . $filter->ruc_cliente.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_cliente.'%")';
            }
        }
        else {
            if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '') {
                $where .= ' and (EMPRC_RazonSocial LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_proveedor.'%")';
            }
            if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '') {
                $where .= ' and (EMPRC_Ruc LIKE "%' . $filter->ruc_proveedor.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_proveedor.'%")';
            }
        }

        $sql = "SELECT l.*, cp.CPC_Fecha, cp.CPP_Codigo, cp.CPC_Serie, cp.CPC_Numero, cp.CPC_TipoDocumento, cp.CPC_total, cp.CPC_FlagEstado, m.MONED_Simbolo, l.LET_FlagEstado,
                       (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre

                    FROM cji_letra l
                    LEFT JOIN cji_comprobante_letra cl ON cl.LET_Codigo = l.LET_Codigo
                    LEFT JOIN cji_comprobante cp ON cp.CPP_Codigo = cl.CPP_Codigo

                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = cp.MONED_Codigo

                    " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo = l.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo = l.PROVP_Codigo") . "
                    
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                    WHERE l.LET_TipoOperacion LIKE '$tipo_oper' AND l.COMPP_Codigo = '$compania' $where

                    GROUP BY l.LET_Codigo
                    $order
                    $limit
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    public function getLetra($filter = NULL) {

        $sql = "SELECT l.*, cp.CPC_Fecha, cp.CPP_Codigo, cp.CPC_Serie, cp.CPC_Numero, cp.CPC_TipoDocumento, cp.CPC_total, cp.CPC_FlagEstado, m.MONED_Simbolo, b.BANC_Nombre, b.BANC_Siglas,
                       (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre

                    FROM cji_letra l
                    LEFT JOIN cji_comprobante_letra cl ON cl.LET_Codigo = l.LET_Codigo
                    LEFT JOIN cji_comprobante cp ON cp.CPP_Codigo = cl.CPP_Codigo

                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = cp.MONED_Codigo
                    LEFT JOIN cji_banco b ON b.BANP_Codigo = l.BANP_Codigo

                    " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo = l.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo = l.PROVP_Codigo") . "
                    
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                    WHERE l.LET_Codigo = $filter->LET_Codigo

                    GROUP BY l.LET_Codigo
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    public function getComprobantes($filter = NULL) {
        

        $sql = "SELECT l.LET_Serie, l.LET_Numero, cp.*, m.MONED_Simbolo,
                       (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre

                    FROM cji_comprobante cp
                    INNER JOIN cji_comprobante_letra cl ON cl.CPP_Codigo = cp.CPP_Codigo
                    INNER JOIN cji_letra l ON l.LET_Codigo = cl.LET_Codigo

                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = cp.MONED_Codigo

                    " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo = l.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo = l.PROVP_Codigo") . "
                    
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                    WHERE l.LET_Codigo = $filter->LET_Codigo
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    public function comprobante_det($comprobante){

        $sql = "SELECT CPC_TipoDocumento, CPC_total, CPC_Numero FROM cji_comprobante WHERE CPP_Codigo = $comprobante";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        else
            return NULL;
    }

    public function actualizar_letra($idletra, $filter){
        $this->db->where('LET_Codigo',$idletra);
        return $this->db->update('cji_letra', $filter);
    }

    public function insertar_letra($filter){
        $this->db->insert("cji_letra", (array) $filter);
        $id = $this->db->insert_id();

        if ($filter->LET_TipoOperacion == 'V')
            $this->configuracion_model->modificar_configuracion($filter->COMPP_Codigo, 16, $filter->LET_Numero);

        return $id;
    }

    public function insertar_documentos_letra($filter){
        $this->db->insert("cji_comprobante_letra", (array) $filter);
        return $this->db->insert_id();
    }

    public function comprobante_pago_pendiente($comprobante) {
        $query = $this->db->where('CUE_CodDocumento', $comprobante)->get('cji_cuentas');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_comprobante($comprobante) {
        $query = $this->db->where('LET_Codigo', $comprobante)->get('cji_letra');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_xserienum($serie, $numero, $doc, $oper) {
        $where = array('LET_Serie' => $serie,
            'LET_Numero' => $numero,
            'LET_TipoDocumento' => $doc,
            'LET_TipoOperacion' => $oper
        );
        $this->db->where($where);
        $query = $this->db->get('cji_letra');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar_comprobante($filter = null) {
        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];

        $filter->COMPP_Codigo = $compania;
        $filter->USUA_Codigo = $user;
        $this->db->insert("cji_letra", (array) $filter);

        $comprobante = $this->db->insert_id();
        switch ($filter->LET_TipoDocumento) {
            case 'F': $codtipodocu = '16';
                break;
            case 'B': $codtipodocu = '9';
                break;
            case 'N': $codtipodocu = '14';
                break;
            default: $codtipodocu = '0';
                break;
        }
        if ($filter->LET_TipoOperacion == 'V')
            $this->configuracion_model->modificar_configuracion($compania, $codtipodocu, $filter->LET_Numero);


        return $comprobante;
    }
    
    public function aprobar_estadoletra($letra, $filter = null) {
    
        $data = array(
            "LET_FlagEstado" => 1
        );
        $this->db->where('LET_Codigo', $letra);
        $this->db->update("cji_letra", $data);
        
    }
    
    public function modificar_comprobante($comprobante, $filter = null) {
        $user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;

        $where = array("LET_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_letra', (array) $filter);
    }

    ############################################
    ########## LETRAS
    ############################################

    public function getComprobantesCP($filter = NULL){
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? " ORDER BY $filter->order $filter->dir " : "";

        $where = "";

        if ($filter->cliente != NULL && $filter->cliente != "")
            $where .= " AND c.CLIP_Codigo = $filter->cliente";

        if ($filter->proveedor != NULL && $filter->proveedor != "")
            $where .= " AND c.PROVP_Codigo = $filter->proveedor";

        if ($filter->moneda != NULL && $filter->moneda != "")
            $where .= " AND c.MONED_Codigo = $filter->moneda";

        $where .= " AND c.COMPP_Codigo = ".$this->compania;

        $sql = "SELECT c.*, m.MONED_Simbolo
                    FROM cji_comprobante c
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                    WHERE c.CPC_FlagEstado = 1 AND NOT EXISTS(SELECT cc.CPP_Codigo FROM comprobantes_cuotas cc WHERE cc.CPP_Codigo = c.CPP_Codigo) AND NOT EXISTS(SELECT cl.CPP_Codigo FROM cji_comprobante_letra cl WHERE cl.CPP_Codigo = c.CPP_Codigo) $where
                    $order
                    $limit
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }


}

?>