<?php

class Notacredito_model extends CI_Model
{

    var $somevar;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('maestros/tipocambio_model');

        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());
    }

    public function listar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CRED_TipoOperacion" => $tipo_oper,
            "CRED_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CRED_FechaRegistro', 'DESC')->where($where)->get('cji_credito', $number_items, $offset);  //order_by('CPC_Serie','desc')->order_by('CPC_Numero','desc')
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function docReferencia($idcomprobante){
        $sql = "SELECT *
                    FROM cji_comprobante
                    WHERE CPP_Codigo = $idcomprobante;
                ";
        $query = $this->db->query($sql);

        $data = NULL;
        if ($query->num_rows() > 0){
            foreach ($query->result() as $value) {
                $data[] = $value;
            }
        }
        return $data;
    }

    public function consultar_respuestaSunat($codigo) {
        $compania = $this->somevar['compania'];
        
        $sql = "SELECT * FROM cji_respuestasunat WHERE CPP_codigo = '$codigo' AND respuestas_tipoDocumento IN(3,4) AND respuestas_enlace IS NOT NULL";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data[0];
        }
        else
            return NULL;
    }

    public function consultar_respuestaXMLSunat($codigo) {
        $compania = $this->somevar['compania'];
        
        $sql = "SELECT * FROM cji_respuestasunat WHERE respuestas_codigo = (SELECT MAX(respuestas_codigo) FROM cji_respuestasunat WHERE CPP_codigo = '$codigo' AND respuestas_tipoDocumento IN(3,4) AND respuestas_enlacexml IS NOT NULL)";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data[0];
        }
    }

    public function motivoAnulacion($nota, $motivo) {
        $sql = "UPDATE cji_nota SET CRED_Observacion = UPPER(CONCAT(CRED_Observacion,' * ','$motivo')) WHERE CRED_Codigo = $nota;";
        $this->db->query($sql);
    }

    public function listar_comprobantes_factura($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CRED_TipoOperacion" => $tipo_oper,
            "CRED_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CRED_Serie', 'desc')->order_by('CRED_Numero', 'desc')->where($where)->get('cji_credito', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_comprobantes_asoc($tipo_oper, $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where .= ' and cp.CPC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        if (isset($filter->seriei) && $filter->seriei != '')
            $where.=' and cp.CPC_Serie="' . $filter->seriei . '"';
        if (isset($filter->numero) && $filter->numero != '')
            $where.=' and cp.CPC_Numero=' . $filter->numero;


        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where.=' and cp.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where.=' and cp.PROVP_Codigo=' . $filter->proveedor;
        }

        if (isset($filter->producto) && $filter->producto != '')
            $where.=' and cpd.PROD_Codigo=' . $filter->producto;
        $limit = "";
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $viewNotas = " AND NOT EXISTS(
                                        SELECT CRED_ComproInicio
                                        FROM cji_nota
                                        WHERE CRED_TipoOperacion = '$tipo_oper' AND CRED_ComproInicio = cp.CPP_Codigo AND CRED_FlagEstado <> 0 AND
                                        ( CRED_TipoNota LIKE 'C' AND DOCUP_Codigo NOT IN(1,2,6) OR CRED_TipoNota LIKE 'D' )
                                    )";
        
        $sql = "SELECT cp.CPC_Fecha, cp.CPP_Codigo, cp.CPC_Serie, cp.CPC_Numero, cp.CPP_Codigo_canje, cp.CPC_GuiaRemCodigo, cp.CPC_DocuRefeCodigo, cp.CPC_NombreAuxiliar, cp.CLIP_Codigo, cp.CPC_TipoDocumento,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CPC_total,
                       cp.CPC_FlagEstado
                FROM  cji_comprobante cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_comprobantedetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
                 " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                WHERE cp.CPC_TipoOperacion='" . $tipo_oper . "'
                      AND cp.CPC_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                      AND cp.CPC_DocuRefeCodigo ='' and cp.CPC_FlagEstado = 1
                      $viewNotas
                GROUP BY cp.CPP_Codigo
                ORDER BY cp.CPC_Fecha DESC, cp.CPC_Serie DESC, cp.CPC_Numero DESC  " . $limit;
        #echo $sql."<br/>";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function getComprobantesAsoc($filter = NULL) {

        $compania = $this->somevar['compania'];
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_docu = $filter->tipo_docu;
        $tipo_oper = $filter->tipo_oper;

        $where = '';

        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' AND cp.CLIP_Codigo=' . $filter->cliente;
        }
        else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' AND cp.PROVP_Codigo=' . $filter->proveedor;
        }

        if (isset($filter->fecha) && $filter->fecha != '')
            $where .= " AND cp.CPC_Fecha BETWEEN '$filter->fecha' AND '$filter->fecha' ";

        if (isset($filter->serie) && $filter->serie != '')
            $where .= " AND cp.CPC_Serie LIKE '%$filter->serie%' ";

        if (isset($filter->numero) && $filter->numero != '')
            $where .= " AND cp.CPC_Numero = '$filter->numero' ";

        $viewNotas = " AND NOT EXISTS(
                                        SELECT CRED_ComproInicio
                                        FROM cji_nota
                                        WHERE CRED_TipoOperacion = '$tipo_oper' AND CRED_ComproInicio = cp.CPP_Codigo AND CRED_FlagEstado <> 0 AND
                                        ( CRED_TipoNota LIKE 'C' AND DOCUP_Codigo NOT IN(1,2,6) OR CRED_TipoNota LIKE 'C' )
                                    )";
        
        $sql = "SELECT cp.CPC_Fecha, cp.CPP_Codigo, cp.CPC_Serie, cp.CPC_Numero, cp.CPP_Codigo_canje, cp.CPC_GuiaRemCodigo, cp.CPC_DocuRefeCodigo, cp.CPC_NombreAuxiliar, cp.CLIP_Codigo, cp.CPC_TipoDocumento,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre, m.MONED_Simbolo, cp.CPC_total, cp.CPC_FlagEstado
                FROM  cji_comprobante cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_comprobantedetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                WHERE cp.CPC_TipoOperacion='" . $tipo_oper . "'
                      AND cp.CPC_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                      AND cp.CPC_DocuRefeCodigo ='' and cp.CPC_FlagEstado = 1
                      $viewNotas

                    GROUP BY cp.CPP_Codigo
                    $order
                    $limit
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

    /**
     * Lista las notas(credito o debito)
     * Devuelve todlas las nota de credito registrada
     * @param string $tipo_oper
     * @param string $tipo_docu
     * @param null $filter
     * @param string $number_items
     * @param string $offset
     * @return array
     */
    public function buscar_comprobantes($tipo_oper = 'V', $filter = NULL, $number_items = '', $offset = '')
    {
        $compania = $this->somevar['compania'];

        $where = '';

        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where = ' and cp.CRED_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';

        if (isset($filter->serie) && $filter->serie != '')
            $where .= ' and cp.CRED_Serie="' . $filter->serie . '"';

        if (isset($filter->numero) && $filter->numero != '')
            $where .= ' AND cp.CRED_Numero="'.$filter->numero.'"';

        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' and cp.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' and cp.PROVP_Codigo=' . $filter->proveedor;
        }
        
        #if (isset($filter->producto) && $filter->producto != '')
        #    $where .= ' and cpd.PROD_Codigo=' . $filter->producto;

        if (isset($filter->CRED_TipoNota) && $filter->CRED_TipoNota != '')
            $where .= " and cp.CRED_TipoNota = '$filter->CRED_TipoNota'";

        $limit = "";

        if ((string)$offset != '' && $number_items != '') {
            $limit = 'LIMIT ' . $offset . ',' . $number_items;
        }

        $sql = "SELECT cp.CRED_Fecha,
                       cp.CRED_TipoOperacion,
                       cp.CRED_Codigo,
                       cp.CRED_Serie,
                       cp.CRED_Numero,
                       (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CRED_total,
                       cp.CRED_FlagEstado,
                       cp.CRED_Flag,
                       cp.CRED_TipoDocumento_inicio,
                       cp.CRED_TipoDocumento_fin,
                       cp.CRED_ComproInicio,
                       cp.CRED_ComproFin,
                       cp.CRED_NumeroInicio,
                       cp.CRED_NumeroFin,
                       cp.CRED_Observacion
                FROM cji_nota cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                " . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "INNER JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '1'
                WHERE cp.CRED_TipoOperacion='" . $tipo_oper . "'
                     
                      AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                ORDER BY cp.CRED_FechaRegistro DESC " . $limit;  //cp.CPC_Serie DESC, cp.CPC_Numero DESC

                # AND cp.CRED_FlagEstado = 1 OLD
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function getNotas($filter = NULL){
        $compania = $this->somevar['compania'];
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_docu = $filter->tipo_docu;
        $tipo_oper = $filter->tipo_oper;

        $where = '';

        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where = ' and cp.CRED_Fecha BETWEEN "' .$filter->fechai. '" AND "' .$filter->fechaf. '"';

        if (isset($filter->serie) && $filter->serie != '')
            $where .= ' and cp.CRED_Serie="' . $filter->serie . '"';

        if (isset($filter->numero) && $filter->numero != '')
            $where .= ' AND cp.CRED_Numero="'.$filter->numero.'"';

        if ($tipo_oper == 'V') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' AND (EMPRC_RazonSocial LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR CLIC_CodigoUsuario LIKE "%' . $filter->nombre_cliente . '%" )';
            }
            if (isset($filter->ruc_cliente) && $filter->ruc_cliente != '') {
                $where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_cliente.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_cliente.'%"';
            }
        }
        else {
            if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '') {
                $where .= ' and EMPRC_RazonSocial LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_proveedor.'%"';
            }
            if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '') {
                $where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_proveedor.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_proveedor.'%"';
            }
        }
        
        #if (isset($filter->producto) && $filter->producto != '')
        #    $where .= ' and cpd.PROD_Codigo=' . $filter->producto;

        if (isset($filter->CRED_TipoNota) && $filter->CRED_TipoNota != '')
            $where .= " and cp.CRED_TipoNota = '$filter->CRED_TipoNota'";

        $sql = "SELECT cp.CRED_Fecha, cp.CRED_TipoOperacion, cp.CRED_Codigo, cp.CRED_Serie, cp.CRED_Numero,
                    (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario, cp.CRED_total, cp.CRED_FlagEstado, cp.CRED_Flag, cp.CRED_TipoDocumento_inicio, cp.CRED_TipoDocumento_fin, cp.CRED_ComproInicio, cp.CRED_ComproFin, cp.CRED_NumeroInicio, cp.CRED_NumeroFin, cp.CRED_Observacion,
                    (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo
                    FROM cji_nota cp
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                    " . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "INNER JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '1'
                    WHERE cp.CRED_TipoOperacion='" . $tipo_oper . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                    GROUP BY cp.CRED_Codigo
                    $order
                    $limit
                ";  //cp.CPC_Serie DESC, cp.CPC_Numero DESC

                # AND cp.CRED_FlagEstado = 1 OLD
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    ///GCBQ
    public function buscar_notadecredito_recu($tipo_oper, $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '')
    {
        $compania = $this->somevar['compania'];
        $where = '';
        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' and cp.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' and cp.PROVP_Codigo=' . $filter->proveedor;
        }

        if (isset($filter->producto) && $filter->producto != '')
            $where .= ' and cpd.PROD_Codigo=' . $filter->producto;
        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT cp.CRED_Fecha,
                       cp.CRED_Codigo,
                       cp.CRED_Serie,
                       cp.CRED_Numero,
                       cp.CLIP_Codigo,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CRED_total,
                       cp.CRED_FlagEstado
                FROM  cji_credito cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_creditodetalle cpd ON cpd.CRED_Codigo=cp.CRED_Codigo
                 " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
                
                WHERE cp.CRED_TipoOperacion='" . $tipo_oper . "' 
                AND cp.CRED_TipoDocumento='F' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                     
                GROUP BY cp.CRED_Codigo
                ORDER BY cp.CRED_Fecha DESC, cp.CRED_Numero DESC  " . $limit;
        //echo $sql."<br/>";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function cargarDetalleComprobante($comprobante){
        $query = $this->db->query("
                                SELECT comd.*, pro.PROD_CodigoInterno, pro.PROD_Nombre, pro.PROD_GenericoIndividual
                                FROM cji_comprobantedetalle comd inner join cji_producto pro
                                on comd.PROD_Codigo = pro.PROD_Codigo
                                WHERE comd.CPDEC_FlagEstado = 1 and
                                comd.CPP_Codigo = (
                                  SELECT CPP_Codigo
                                  FROM cji_comprobante
                                  WHERE CPP_Codigo = '".$comprobante."'
                                )
                                group by pro.PROD_Codigo
                            ");
        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return NULL;
        }
    }

    public function getNotaMail($nota){

        $sql = "SELECT n.*, m.MONED_Simbolo,
                    (SELECT e.EMPRP_Codigo 
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = n.CLIP_Codigo
                    ) as empresa,

                    (SELECT e.EMPRC_Email 
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = n.CLIP_Codigo
                    ) as email,

                    (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial)
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = n.CLIP_Codigo
                    ) as razon_social,

                    (SELECT CONCAT_WS(' ', p.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc)
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = n.CLIP_Codigo
                    ) as ruc
                    
                    FROM cji_nota n
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = n.MONED_Codigo
                    WHERE n.CRED_Codigo = $nota
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

    public function obtener_comprobante($comprobante){
        #$query = $this->db->where('CRED_Codigo', $comprobante)->get('cji_nota');

        $sql = "SELECT n.*,
                (SELECT CONCAT_WS(' ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) FROM cji_persona pp WHERE pp.PERSP_Codigo = c.CPC_Vendedor LIMIT 1) as vendedor
                    FROM cji_nota n
                    LEFT JOIN cji_comprobante c ON c.CPP_Codigo = n.CRED_ComproInicio
                    WHERE n.CRED_Codigo = $comprobante
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_total_comprobante_original($comprobante){
        
        $sql = "SELECT CPC_total as total FROM cji_comprobante WHERE CPP_Codigo = $comprobante";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data[0];
        }
    }

    public function insertar_notaCredito($filter)
    {
        $insert = $this->db->insert('cji_nota', (array)$filter);
        if ($insert) {
            return $this->db->insert_id();
        }else{
            return NULL;
        }
    }

    public function insertar_comprobante($filter = null)
    {
        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];

        $this->load->model('configuracion_model');

        $filter->COMPP_Codigo = $compania;
        $filter->USUA_Codigo = $user;
        $this->db->insert("cji_nota", (array)$filter);


        $comprobante = $this->db->insert_id();
        switch ($filter->CRED_TipoDocumento) {
            case 'F':
                $codtipodocu = '8';
                break;
            case 'B':
                $codtipodocu = '9';
                break;
            case 'N':
                $codtipodocu = '14';
                break;
            default:
                $codtipodocu = '0';
                break;
        }

        if ($filter->CRED_TipoOperacion == 'V')
            $this->configuracion_model->modificar_configuracion($compania, $codtipodocu, $filter->CRED_Numero);

        $filter2 = new stdClass();
        $filter2->CUE_TipoCuenta = $filter->CRED_TipoOperacion == 'V' ? 1 : 2;
        $filter2->DOCUP_Codigo = $codtipodocu;
        $filter2->CUE_CodDocumento = $comprobante;
        $filter2->MONED_Codigo = $filter->MONED_Codigo;
        $filter2->CUE_Monto = $filter->CRED_total;
        $filter2->CUE_FechaOper = $filter->CRED_Fecha;
        $filter2->COMPP_Codigo = $compania;
        $filter2->CUE_FlagEstado = '1';
        $cuenta = $this->cuentas_model->insertar($filter2);

        if (isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo == 1) {  //Si el pago es al contado           
            $filter3 = new stdClass();
            $filter3->PAGC_TipoCuenta = $filter->CRED_TipoOperacion == 'V' ? 1 : 2;
            $filter3->PAGC_FechaOper = $filter->CRED_Fecha;
            $tipo_cuenta = $filter3->PAGC_TipoCuenta;
            $forma_pago = $filter->FORPAP_Codigo;
            if ($filter3->PAGC_TipoCuenta == 1)
                $filter3->CLIP_Codigo = $filter->CLIP_Codigo;
            else
                $filter3->PROVP_Codigo = $filter->PROVP_Codigo;
            $filter4 = new stdClass();
            $filter4->TIPCAMC_Fecha = $filter->CRED_Fecha;
            $filter4->TIPCAMC_MonedaDestino = '2';
            $temp = $this->tipocambio_model->buscar($filter4);
            $tdc = is_array($temp) ? $temp[0]->TIPCAMC_FactorConversion : '';

            $filter3->PAGC_TDC = $tdc;
            $filter3->PAGC_Monto = $filter->CRED_total;
            $filter3->MONED_Codigo = $filter->MONED_Codigo;
            $filter3->PAGC_FormaPago = '1'; //Efectivo

            $filter3->PAGC_Obs = ($filter->CRED_TipoOperacion == 'V' ? 'INGRESO GENERADO' : 'SALIDA GENERADA') . ' AUTOMATICAMENTE POR EL PAGO AL CONTADO';
            $filter3->PAGC_Saldo = '0';

            $cod_pago = $this->pago_model->insertar($filter3, $tipo_cuenta, $forma_pago, $compania);

            $filter5 = new stdClass();
            $filter5->CUE_Codigo = $cuenta;
            $filter5->PAGP_Codigo = $cod_pago;
            $filter5->CPAGC_TDC = $tdc;
            $filter5->CPAGC_Monto = $filter->CRED_total;
            $filter5->MONED_Codigo = $filter->MONED_Codigo;

            $this->cuentaspago_model->insertar($filter5);

            $pago = $this->cuentas_model->modificar_estado($cuenta, 'C');

            $filter3 = new stdClass();
        }

        return $comprobante;

        //return "";
    }

    public function buscarComprobante_nota($comprobante){
        $query = $this->db->select('CPP_Codigo, CPC_TipoOperacion, CPC_TipoDocumento, CPC_Serie, CPC_Numero')
            ->from('cji_comprobante')
            ->where('CPP_Codigo', $comprobante)
            ->where('CPC_FlagEstado', '1')
            ->get();
        if($query->num_rows() > 0){
            return $query->row();
        }else{
            return NULL;
        }
    }

    public function modificar_comprobante($comprobante, $filter = null)
    {
        $user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;

        $where = array("CRED_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_nota', (array)$filter);
    }

    public function modificar_notaCredito($datosComprobante, $notaCredito)
    {
        $data = array(
            'CRED_ComproFin' => $datosComprobante->CPP_Codigo,
            'CRED_TipoDocumento_fin' => $datosComprobante->CPC_TipoDocumento,
            'CRED_NumeroFin' => $datosComprobante->CPC_Serie . '-' . $datosComprobante->CPC_Numero,
            'CRED_Flag' => '1'
        );
        $this->db->where('CRED_Codigo', $notaCredito);
        $valor = $this->db->update('cji_nota', $data);
        return $valor;
    }

    public function sunatEnviado($notaCredito, $datos)
    {
        $data = array(
            'CRED_ComproFin' => $datos->CPP_codigo,
            'CRED_TipoDocumento_fin' => $datos->respuestas_tipoDocumento,
            'CRED_NumeroFin' => $datos->respuestas_serie . '-' . $datos->respuestas_numero,
            'CRED_FlagEstado' => '1'
        );
        $this->db->where('CRED_Codigo', $notaCredito);
        $valor = $this->db->update('cji_nota', $data);
        return $valor;
    }

    public function eliminar_comprobante($comprobante)
    {
        $data = array("CRED_FlagEstado" => '0');
        $where = array("CRED_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_nota', $data);

        #$data = array("CREDET_FlagEstado" => '0');
        #$where = array("CRE_Codigo" => $comprobante);
        #$this->db->where($where);
        #$this->db->update('cji_notadetalle', $data);
    }

    public function buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $presupuesto)
    {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu, "CPC_FlagEstado" => "1", "PRESUP_Codigo" => $presupuesto);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_x_numero_presupuesto_cualquiera($tipo_oper, $tipo_docu, $presupuesto)
    {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper, "CPC_FlagEstado" => "1", "PRESUP_Codigo" => $presupuesto);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_x_numero_ocompra($tipo_oper, $ocompra)
    {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_FlagEstado" => "1", "OCOMP_Codigo" => $ocompra);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_x_numero_guiarem($guiarem)
    {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania,
            "CPC_FlagEstado" => "1", "GUIAREMP_Codigo" => $guiarem);
        $query = $this->db->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function ultimo_serie_numero($tipo_oper, $tipo_docu)
    {
        $compania = $this->somevar['compania'];
        $where = array("CRED_TipoOperacion" => $tipo_oper, "CRED_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CRED_Serie', 'desc')->order_by('CRED_Numero', 'desc')->where($where)->get('cji_credito', 1);
        $result['serie'] = "001";
        $result['numero'] = "1";
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $result['serie'] = $data[0]->CRED_Serie;
            $result['numero'] = (int)$data[0]->CRED_Numero + 1;
        }
        return $result;
    }

    //REPORTES

    public function reporte_ocompra_5_clie_mas_importantes()
    {
        $sql = "SELECT Q.total,Q.nombre
                FROM
                        (SELECT SUM(o.OCOMC_total) total,
                                (CASE p.CLIC_TipoPersona WHEN '1' THEN e.EMPRC_RazonSocial 
                                ELSE CONCAT(pe.PERSC_Nombre, ' ', pe.PERSC_ApellidoPaterno, 
                                ' ', pe.PERSC_ApellidoMaterno) END) nombre
                        FROM cji_ordencompra o
                        INNER JOIN cji_cliente p ON p.CLIP_Codigo=o.CLIP_Codigo
                        LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                        LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0'
                        WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%'
                        GROUP BY o.CLIP_Codigo)Q
                ORDER BY Q.total DESC
                LIMIT 5";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oventa_monto_x_mes()
    {
        $sql = "SELECT
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '01' THEN o.OCOMC_total ELSE 0 END)) enero,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '02' THEN o.OCOMC_total ELSE 0 END)) febrero,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '03' THEN o.OCOMC_total ELSE 0 END)) marzo,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '04' THEN o.OCOMC_total ELSE 0 END)) abril,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '05' THEN o.OCOMC_total ELSE 0 END)) mayo,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '06' THEN o.OCOMC_total ELSE 0 END)) junio,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '07' THEN o.OCOMC_total ELSE 0 END)) julio,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '08' THEN o.OCOMC_total ELSE 0 END)) agosto,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '09' THEN o.OCOMC_total ELSE 0 END)) setiembre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '10' THEN o.OCOMC_total ELSE 0 END)) octubre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '11' THEN o.OCOMC_total ELSE 0 END)) noviembre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '12' THEN o.OCOMC_total ELSE 0 END)) diciembre
                FROM cji_ordencompra o
                WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oventa_cantidad_x_mes()
    {
        $sql = "SELECT
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='01' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) enero,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='02' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) febrero,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='03' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) marzo,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='04' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) abril,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='05' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) mayo,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='06' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) junio,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='07' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) julio,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='08' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) agosto,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='09' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) setiembre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='10' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) octubre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='11' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) noviembre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='12' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) diciembre
            FROM cji_ordencompra o
            WHERE o.OCOMC_FlagEstado='1' AND  o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_comparativo_compras_ventas($tipo_op)
    {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = "SELECT
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '01' THEN c.CPC_total ELSE 0 END)) enero,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '02' THEN c.CPC_total ELSE 0 END)) febrero,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '03' THEN c.CPC_total ELSE 0 END)) marzo,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '04' THEN c.CPC_total ELSE 0 END)) abril,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '05' THEN c.CPC_total ELSE 0 END)) mayo,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '06' THEN c.CPC_total ELSE 0 END)) junio,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '07' THEN c.CPC_total ELSE 0 END)) julio,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '08' THEN c.CPC_total ELSE 0 END)) agosto,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '09' THEN c.CPC_total ELSE 0 END)) setiembre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '10' THEN c.CPC_total ELSE 0 END)) octubre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '11' THEN c.CPC_total ELSE 0 END)) noviembre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '12' THEN c.CPC_total ELSE 0 END)) diciembre
            FROM cji_comprobante c
            WHERE c.CPC_TipoOperacion='" . $tipo_op . "' AND c.CPC_FlagEstado='1' AND  c.CPP_Codigo<>0 AND YEAR(c.CPC_FechaRegistro)=YEAR(CURDATE())";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function buscar_comprobante_venta($fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso, $number_items = '', $offset = '')
    {
        $where = '';
        if ($fechai != '' && $fechaf != '')
            $where = ' and o.OCOMC_FechaRegistro BETWEEN "' . $fechai . '" AND "' . $fechaf . '"';
        if ($proveedor != '')
            $where .= ' and o.PROVP_Codigo=' . $proveedor;
        if ($producto != '')
            $where .= ' and od.PROD_Codigo=' . $producto;
        if ($aprobado != '')
            $where .= ' and o.OCOMC_FlagAprobado=' . $aprobado;
        if ($ingreso != '')
            $where .= ' and o.OCOMC_FlagIngreso=' . $ingreso;
        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha,
                         o.OCOMP_Codigo,
                         o.PEDIP_Codigo,
                         o.PROVP_Codigo,
                         o.CENCOSP_Codigo,
                         o.OCOMC_Numero,
                         
                           (CASE WHEN o.COTIP_Codigo =0 THEN '***'
                           ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,
                       (CASE p.CLIC_TipoPersona WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT( pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       o.OCOMC_total,
                       (CASE o.OCOMC_FlagAprobado 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Aprob.'
                                WHEN '2' THEN 'Desaprob.'
                                ELSE ''
                        END) aprobado,
                        (CASE o.OCOMC_FlagIngreso 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Si.'
                                ELSE ''
                        END) ingreso,
                        o.OCOMC_FlagEstado
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo
                INNER JOIN cji_cliente p ON p.CLIP_Codigo=o.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                WHERE o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='V'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function buscar_comprobante_venta_2($anio)
    {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT * FROM cji_comprobante c WHERE CPC_TipoOperacion='V' AND CPC_TipoDocumento='F' AND YEAR(CPC_FechaRegistro)=" . $anio . "";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function buscar_comprobante_compras($anio)
    {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT * FROM cji_comprobante c WHERE CPC_TipoOperacion='C' AND CPC_TipoDocumento='F' AND YEAR(CPC_FechaRegistro)=" . $anio . "";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function estadisticas_compras_ventas($tipo, $anio)
    {
        $sql = "SELECT p.CLIP_Codigo,e.EMPRC_RazonSocial,pe.PERSC_Nombre,MONTH(c.CPC_FechaRegistro) 
                AS mes,c.CPC_FechaRegistro,SUM(c.CPC_total) AS monto 
                FROM cji_cliente p 
                INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
                WHERE c.CPC_TipoOperacion='" . $tipo . "' AND YEAR(CPC_FechaRegistro)=" . $anio . " AND CPC_TipoDocumento='F' 
                GROUP BY c.CLIP_Codigo,MONTH(CPC_FechaRegistro)
                ";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function anios_para_reportes($tipo)
    {
        $sql = "SELECT YEAR(CPC_FechaRegistro) as anio FROM cji_comprobante WHERE CPC_TipoOperacion='" . $tipo . "' GROUP BY YEAR(CPC_FechaRegistro)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes, $totales = false)
    {
        if ($tipo == 'V'){
            $sql = "
                    SELECT p.CLIP_Codigo, e.EMPRC_RazonSocial, e.EMPRC_Ruc, pe.PERSC_Nombre, pe.PERSC_NumeroDocIdentidad, MONTH(n.CRED_Fecha) AS mes,
                    n.CRED_Fecha, n.CRED_subtotal, n.CRED_igv, n.CRED_total AS monto, n.MONED_Codigo, m.MONED_Simbolo, n.COMPP_Codigo, n.CRED_Serie, n.CRED_Numero, n.CRED_TipoNota
                    FROM cji_cliente p 
                    INNER JOIN cji_nota n ON p.CLIP_Codigo = n.CLIP_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = n.MONED_Codigo
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1' 
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
                    WHERE n.CRED_Flag IN(0,1) AND n.COMPP_Codigo='".$this->somevar ['compania']."' and n.CRED_TipoOperacion='" . $tipo . "' AND MONTH(CRED_Fecha) ='" . $mes . "' AND YEAR(CRED_Fecha) ='" . $anio . "' ORDER BY n.CRED_Fecha
            ";
        }
        else{
            $sql = "
                SELECT p.PROVP_Codigo, e.EMPRC_RazonSocial, e.EMPRC_Ruc, e.EMPRC_RazonSocial as PERSC_Nombre, MONTH(n.CRED_Fecha) AS mes,
                    n.CRED_Fecha, n.CRED_subtotal, n.CRED_igv, n.CRED_total AS monto, n.MONED_Codigo, m.MONED_Simbolo, n.COMPP_Codigo, n.CRED_Serie, n.CRED_Numero, n.CRED_TipoNota
                FROM cji_proveedor p 
                INNER JOIN cji_nota n ON p.PROVP_Codigo =  n.PROVP_Codigo
                LEFT JOIN cji_moneda m ON m.MONED_Codigo = n.MONED_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.PROVC_TipoPersona='1' 
                WHERE n.CRED_Flag IN(0,1) AND n.COMPP_Codigo='".$this->somevar ['compania']."' and  n.CRED_TipoOperacion='" . $tipo . "' AND MONTH( n.CRED_Fecha) ='" . $mes . "' AND YEAR( n.CRED_Fecha) ='" . $anio . "' ORDER BY n.CRED_Fecha
            ";
        }

        if ($totales == true){
            $sql = "
                    SELECT SUM(n.CRED_total) AS total, m.MONED_Simbolo, n.CRED_TipoNota
                    FROM cji_nota n
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = n.MONED_Codigo
                    WHERE n.CRED_Flag = 1 AND n.COMPP_Codigo='".$this->somevar ['compania']."' and n.CRED_TipoOperacion='" . $tipo . "' AND MONTH(n.CRED_Fecha) ='" . $mes . "' AND YEAR(n.CRED_Fecha) ='" . $anio . "' GROUP BY n.CRED_TipoNota
            ";
        }

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    /*NOTA DE CREDITO*/
     public function actualizarStock($comrpobanteIni, $cod_nota, $cod_pro){ // Sin uso, utilizo el de comprobantes
        $compania = $this->somevar['compania'];
        //$nota = $this->obtener_comprobante($cod_nota);
        $list = $this->comprobante_model->obtener_comprobante($comrpobanteIni);

        $gsap = $list[0]->GUIASAP_Codigo;

        $detalle = $this->listar_detalle_nota($cod_nota);
        for ($i = 0; $i < count($detalle); $i++) {
            $prodcod = $detalle[$i]->PROD_Codigo;
            //$unid_medida = $detalle[$i]->UNDMED_Codigo;
            $cantidad = $detalle[$i]->CREDET_Cantidad;
            
            //obtener el almacen        
            $guiasap_datos = $this->guiasa_model->obtener($gsap);

            if ($guiasap_datos){
                $almacencod = $guiasap_datos->ALMAP_Codigo;
                $docupcod = 6;

                //obtener el valor del stock

                $almacenproducto_datos = $this->almacenproducto_model->obtener($almacencod, $prodcod);
                $almacenprodcod = $almacenproducto_datos[0]->ALMPROD_Codigo;
                $stock = $almacenproducto_datos[0]->ALMPROD_Stock;
            
                    if ($cod_pro == $prodcod){
                        $cantidad_fin = $cantidad;
                    }
                

                $nuevostock = $stock + $cantidad_fin; // aqui esta la cosa

                $this->kardex_model->eliminar($docupcod, $gsap, $prodcod);  // Revisar el funcionamiento del kardex al actualizar desde nota de credito
                
                //actualizar stock
                $data = array("ALMPROD_Stock" => $nuevostock);
                $where = array("ALMAC_Codigo" => $almacencod, "PROD_Codigo" => $prodcod, "COMPP_Codigo" => $compania);
                $this->db->where($where);
                $this->db->update('cji_almacenproducto', $data);
            }
        }
    }



    public function listar_detalle_nota($cod_nota)
    {
        $where = array("CRED_Codigo"=>$cod_nota,"CREDET_FlagEstado"=>"1");
        $query = $this->db->order_by('CREDET_Codigo')->where($where)->get('cji_notadetalle');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

}

?>