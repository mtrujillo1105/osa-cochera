<?php

class Cuentas_model extends CI_Model {

    private $empresa;
    private $compania;

    public function __construct() {
        parent::__construct();
        $this->load->helper('date');

        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
    }

    public function tipodoc_get() {
        $query = $this->select('DOCUP_Codigo,DOCUC_Descripcion')->from('cji_cuentas')->where_in('DOCUP_Codigo', array(8, 9, 14))->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function listar($tipo_cuenta = '1', $number_items = '', $offset = '', $filter = NULL, $cond_pago = '', $comprobante = '') {

        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != ''){
            if ($filter->fechai != $filter->fechaf)
                $where .= ($filter->fechai != $filter->fechaf) ? ' and c.CPC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"' : " and c.CPC_Fecha = '". human_to_mysql($filter->fechai) ."'";
            else
                $where .= " and c.CPC_Fecha = '". human_to_mysql($filter->fechai) ."'";
        }
        
        if ($comprobante != '' && isset($comprobante)) {
            if ($comprobante == 9) {
                $where.= " AND cji_cuentas.DOCUP_Codigo = 9 ";
            } else if ($comprobante == 8) {
                $where.= " AND cji_cuentas.DOCUP_Codigo = 8 ";
            }
        }

        if ($cond_pago != '' && isset($cond_pago)) {
            if ($cond_pago == 'C') {
                $where.= " AND cji_cuentas.CUE_FlagEstadoPago ='C' ";
            } else if ($cond_pago == 'P') {
                $where.= " AND cji_cuentas.CUE_FlagEstadoPago IN ('V','A') ";
            } else if ($cond_pago == 'T') {
                $where.= " AND cji_cuentas.CUE_FlagEstadoPago IN ('C','V','A') ";
            }
        }

        if (isset($filter->cliente) && $filter->cliente != "")
            $where.=" AND c.CLIP_Codigo=" . $filter->cliente;

        if (isset($filter->proveedor) && $filter->proveedor != "")
            $where.=" AND c.PROVP_Codigo=" . $filter->proveedor;
            
            if (isset($filter->MONED_Codigo) && $filter->MONED_Codigo != "" && $filter->MONED_Codigo != 0)
           $where.=" AND c.MONED_Codigo=" . $filter->MONED_Codigo;
           
        $compania = $this->compania;

        $sql = "SELECT cji_cuentas.*, c.PROVP_Codigo, 
                c.CLIP_Codigo, c.CPC_TipoDocumento, 
                c.CPC_Serie, c.CPC_Numero, 
                cji_moneda.MONED_Simbolo FROM cji_cuentas
                LEFT JOIN cji_comprobante c ON c.CPP_Codigo = cji_cuentas.CUE_CodDocumento
                LEFT JOIN cji_moneda ON cji_moneda.MONED_Codigo = cji_cuentas.MONED_Codigo
                WHERE cji_cuentas.CUE_TipoCuenta= $tipo_cuenta AND cji_cuentas.CUE_FlagEstado=1
                AND cji_cuentas.COMPP_Codigo = " . $this->compania;

        if ($tipo_cuenta == 1) {
            $sql.=" AND c.CPC_TipoOperacion='V'";
        } else {
            $sql.=" AND c.CPC_TipoOperacion='C'";
        }
        $limit = "";
        $sql.= $where;
        if ($where == '')
            $limit = " LIMIT 0,50";


        $todos = "";
        if ($where == '') {
            $todos = " AND cji_cuentas.CUE_FlagEstadoPago IN ('V','A') ";
            $sql.=$todos;
        }
        $sql.=" ORDER BY cji_cuentas.CUE_FechaRegistro DESC " . $limit;

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function getCuentas($filter = NULL){
        
        $compania = $this->compania;
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_cuenta = $filter->tipo_cuenta;

        $where = '';

        if( isset($filter->fechai) && $filter->fechai != "" && isset($filter->fechaf) && $filter->fechaf != "" )
            $where .= " AND c.CUE_FechaOper BETWEEN '$filter->fechai 00:00:00' AND '$filter->fechaf 23:59:59' ";

        if ($filter->comprobante != '' && isset($filter->comprobante) && $filter->comprobante != 'T')
            $where .= " AND c.DOCUP_Codigo = $filter->comprobante ";

        if ($filter->cond_pago != '' && isset($filter->cond_pago)){
            switch ($filter->cond_pago){
                case 'C':
                    $where .= " AND c.CUE_FlagEstadoPago = 'C' ";
                    break;
                case 'P':
                    $where .= " AND c.CUE_FlagEstadoPago IN('V','A') ";
                    break;
                
                default:
                    $where .= " AND c.CUE_FlagEstadoPago IN('C','V','A') ";
                    break;
            }
        }

        if (isset($filter->search) && $filter->search != '')
          $where .= " AND CONCAT_WS('-',cp.CPC_Serie, cp.CPC_Numero) LIKE '%".$filter->search."%'";

        if ($filter->tipo_cuenta != '2') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= " AND cp.CLIP_Codigo = '$filter->cliente'";

            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno, e.EMPRC_RazonSocial, cli.CLIC_CodigoUsuario) LIKE '%$filter->nombre_cliente%'";
            
            if(isset($filter->ruc_cliente) && $filter->ruc_cliente != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_NumeroDocIdentidad e.EMPRC_Ruc) LIKE '%$filter->ruc_cliente%'";
        }
        else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= " AND cp.PROVP_Codigo = '$filter->proveedor'";

            if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno, e.EMPRC_RazonSocial) LIKE '%$filter->nombre_proveedor%'";

            if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_NumeroDocIdentidad e.EMPRC_Ruc) LIKE '%$filter->ruc_proveedor%'";
        }

        if (isset($filter->serie) && $filter->serie != '')
            $where .= " AND cp.CPC_Serie LIKE '$filter->serie' ";

        if (isset($filter->numero) && $filter->numero != '')
            $where .= " AND cp.CPC_Numero LIKE '$filter->numero' ";

        if (isset($filter->MONED_Codigo) && $filter->MONED_Codigo != "" && $filter->MONED_Codigo != 0)
           $where .= " AND cp.MONED_Codigo = " . $filter->MONED_Codigo;
        
        #$where = ($tipo_cuenta == '1') ? "$where AND cp.CPC_TipoOperacion LIKE 'V' " : "$where AND cp.CPC_TipoOperacion LIKE 'C' ";
           
        $sql = "SELECT c.*, CASE cp.CPC_TipoDocumento
                                WHEN 'F' THEN 'FACTURA'
                                WHEN 'B' THEN 'BOLETA'
                                WHEN 'N' THEN 'COMPROBANTE'
                                ELSE ''
                            END as tipo_documento,
                    cp.PROVP_Codigo, cp.CLIP_Codigo, cp.CPC_TipoDocumento, cp.CPC_TipoOperacion, cp.CPC_Serie, cp.CPC_Numero, cp.CPC_Fecha, cp.CPC_FechaVencimiento, m.MONED_Simbolo,

                    cli.CLIP_Codigo, pv.PROVP_Codigo, cli.CLIC_CodigoUsuario,
                    CONCAT_WS(' ', e.EMPRC_Ruc, pe.PERSC_NumeroDocIdentidad) as rucDni,
                    CONCAT_WS(' ', e.EMPRC_RazonSocial, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno) as nombre
                    
                    FROM cji_cuentas c
                    INNER JOIN cji_comprobante cp ON cp.CPP_Codigo = c.CUE_CodDocumento
                    
                    LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = cp.CLIP_Codigo
                    LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = cp.PROVP_Codigo

                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo

                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                    WHERE c.CUE_TipoCuenta = $tipo_cuenta AND c.CUE_FlagEstado = 1 AND c.COMPP_Codigo = $compania $where
                    $order
                    $limit
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
          return $query->result();
        else
          return NULL;
    }

    public function getCuenta($cuenta){
        $sql = "SELECT c.*, cp.CPP_Codigo, cp.PROVP_Codigo, cp.CLIP_Codigo, cp.CPC_TipoDocumento,
                            CASE cp.CPC_TipoDocumento
                                WHEN 'F' THEN 'FACTURA'
                                WHEN 'B' THEN 'BOLETA'
                                WHEN 'N' THEN 'COMPROBANTE'
                                ELSE 'N/A'
                            END as documento,
                            cp.CPC_Serie, cp.CPC_Numero, cp.CPC_Fecha, cp.CPC_FechaVencimiento, m.MONED_Simbolo,

                    cli.CLIP_Codigo, pv.PROVP_Codigo,
                    CONCAT_WS(' ', e.EMPRC_Ruc, pe.PERSC_NumeroDocIdentidad) as rucDni,
                    CONCAT_WS(' ', e.EMPRC_RazonSocial, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno) as razon_social

                    FROM cji_cuentas c
                    INNER JOIN cji_comprobante cp ON cp.CPP_Codigo = c.CUE_CodDocumento

                    LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = cp.CLIP_Codigo
                    LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = cp.PROVP_Codigo

                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo

                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                    
                    WHERE c.CUE_Codigo = $cuenta
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function add_prorroga($comprobante, $dias){
        $sql = "UPDATE cji_comprobante
                        SET CPC_FechaVencimiento = CASE
                                                        WHEN CPC_FechaVencimiento IS NULL THEN DATE_ADD(CPC_Fecha, INTERVAL $dias DAY)
                                                        WHEN CPC_FechaVencimiento LIKE '0000-00-00' THEN DATE_ADD(CPC_Fecha, INTERVAL $dias DAY)
                                                        WHEN CPC_FechaVencimiento LIKE '' THEN DATE_ADD(CPC_Fecha, INTERVAL $dias DAY)
                                                        ELSE DATE_ADD(CPC_FechaVencimiento, INTERVAL $dias DAY)
                                                    END, CPC_FechaModificacion = NOW()
                    WHERE CPP_Codigo = $comprobante";
        return $this->db->query($sql);
    }

    public function montosCuentas($tipo = 1){
        $oper = ($tipo == 1) ? 'V' : 'C';

        $sql = "SET @cuentas = (SELECT SUM(c.CUE_Monto)
                                    FROM cji_cuentas c 
                                    INNER JOIN cji_comprobante comp ON comp.CPP_Codigo = c.CUE_CodDocumento
                                    WHERE c.CUE_TipoCuenta = '".$tipo."' AND c.CUE_FlagEstado = 1 AND EXISTS(SELECT cc.COMPP_Codigo FROM cji_compania cc WHERE cc.COMPP_Codigo = c.COMPP_Codigo AND cc.EMPRP_Codigo = $this->empresa) AND comp.CPC_TipoOperacion = '$oper' AND c.CUE_FlagEstadoPago IN ('V','A')
                                );";
        $this->db->query($sql);

        $sql = "SET @pagos = (SELECT SUM(CPAGC_Monto)
                                    FROM cji_cuentaspago cp
                                    INNER JOIN cji_cuentas c ON c.CUE_Codigo = cp.CUE_Codigo
                                    INNER JOIN cji_comprobante comp ON comp.CPP_Codigo = c.CUE_CodDocumento
                                    WHERE c.CUE_TipoCuenta = '".$tipo."' AND c.CUE_FlagEstado = 1 AND EXISTS(SELECT cc.COMPP_Codigo FROM cji_compania cc WHERE cc.COMPP_Codigo = c.COMPP_Codigo AND cc.EMPRP_Codigo = $this->empresa) AND comp.CPC_TipoOperacion = '$oper' AND c.CUE_FlagEstadoPago IN ('V','A') AND cp.CPAGC_FlagEstado = 1);";
        $this->db->query($sql);

        $sql = "SELECT @cuentas as cuentas, @pagos as pagos, @cuentas-@pagos as saldo;";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getNotas($filter = NULL){
        
        $compania = $this->compania;
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_cuenta = $filter->tipo_cuenta;

        $where = '';

        if ($filter->tipo_cuenta != '2') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= " AND n.CLIP_Codigo = '$filter->cliente'";

            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno, e.EMPRC_RazonSocial, cli.CLIC_CodigoUsuario) LIKE '%$filter->nombre_cliente%'";
            
            if(isset($filter->ruc_cliente) && $filter->ruc_cliente != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_NumeroDocIdentidad e.EMPRC_Ruc) LIKE '%$filter->ruc_cliente%'";
        }
        else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= " AND n.PROVP_Codigo = '$filter->proveedor'";

            if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno, e.EMPRC_RazonSocial) LIKE '%$filter->nombre_proveedor%'";

            if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
                $where .= " AND CONCAT_WS(' ', pe.PERSC_NumeroDocIdentidad e.EMPRC_Ruc) LIKE '%$filter->ruc_proveedor%'";
        }

        if (isset($filter->serie) && $filter->serie != '')
            $where .= " AND n.CRED_Serie LIKE '$filter->serie' ";

        if (isset($filter->numero) && $filter->numero != '')
            $where .= " AND n.CRED_Numero LIKE '$filter->numero' ";

        if (isset($filter->MONED_Codigo) && $filter->MONED_Codigo != "" && $filter->MONED_Codigo != 0)
           $where .= " AND n.MONED_Codigo = " . $filter->MONED_Codigo;

        if (isset($filter->tipo_nota) && $filter->tipo_nota != "")
           $where .= " AND n.CRED_TipoNota LIKE '$filter->tipo_nota'";
        
        $where = ($tipo_cuenta == '1') ? "$where AND n.CRED_TipoOperacion LIKE 'V' " : "$where AND n.CRED_TipoOperacion LIKE 'C' ";
        
        $sql = "SELECT n.*, CASE n.CRED_TipoNota
		                            WHEN 'C' THEN 'NOTA DE CREDITO'
		                            WHEN 'D' THEN 'NOTA DE DEBITO'
		                            ELSE ''
		                        END as tipo_documento,
                  n.PROVP_Codigo, n.CLIP_Codigo, n.CRED_TipoOperacion, n.CRED_Serie, n.CRED_Numero, n.CRED_Fecha, m.MONED_Simbolo,
                  CASE c.CPC_TipoDocumento
                  	WHEN 'F' THEN 'FACTURA'
                  	WHEN 'B' THEN 'BOLETA'
                  	WHEN 'C' THEN 'COMPROBANTE'
                  	ELSE ''
                  END as documento_relacionado,
                  c.CPP_Codigo, c.CPC_Serie, c.CPC_Numero,

                  cli.CLIP_Codigo, pv.PROVP_Codigo, cli.CLIC_CodigoUsuario,
                  CONCAT_WS(' ', e.EMPRC_Ruc, pe.PERSC_NumeroDocIdentidad) as rucDni,
                  CONCAT_WS(' ', e.EMPRC_RazonSocial, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno) as nombre
                  
                  FROM cji_nota n
                  INNER JOIN cji_comprobante c ON c.CPP_Codigo = n.CRED_ComproInicio
                  LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = n.CLIP_Codigo
                  LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = n.PROVP_Codigo

                  LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
                  LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo

                  LEFT JOIN cji_moneda m ON m.MONED_Codigo = n.MONED_Codigo
                  WHERE n.CRED_FlagEstado LIKE '1' AND n.COMPP_Codigo = $compania
                  	AND NOT EXISTS(SELECT PAGP_Codigo FROM cji_pago pag WHERE pag.PAGC_NotaCredito = n.CRED_Codigo AND pag.PAGC_FlagEstado LIKE '1')
                  $where
                  $order
                  $limit
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
          return $query->result();
        else
        	return NULL;
    }

    public function condicion_pago() {
        $query = $this->select('DISTINCT(CUE_FlagEstadoPago)')->from('cji_cuentas')->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function buscar_notas_credito_cliente($codigo) {
        $query = $this->db->select('cji_nota.*')
            ->from('cji_nota')
            ->where('CLIP_Codigo', $codigo)
            ->where('CRED_FlagEstado', '1')
            ->where('CRED_Flag', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        else
            return NULL;
    }

    public function buscar_notas_credito_proveedor($codigo) {
        $query = $this->db->select('cji_nota.*')
            ->from('cji_nota')
            ->where('PROVP_Codigo', $codigo)
            ->where('CRED_FlagEstado', '1')
            ->where('CRED_Flag', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        else
            return NULL;
    }

    public function buscar($tipo_cuenta, $codigo, $estado = array('V', 'A'), $f_ini = '', $f_fin = '',$order='asc') {
        $compania = $this->compania;
        $where = array("cji_cuentas.CUE_TipoCuenta" => $tipo_cuenta, 'cji_cuentas.COMPP_Codigo' => $this->compania);
        if ($tipo_cuenta == '1')
            $where['co.CLIP_Codigo'] = $codigo;
        else
            $where['co.PROVP_Codigo'] = $codigo;
        if ($f_ini != '' && $f_fin != '') {
            $where['cji_cuentas.CUE_FechaOper >='] = human_to_mysql($f_ini);
            $where['cji_cuentas.CUE_FechaOper <='] = human_to_mysql($f_fin);
        }

        if (isset($filter->codigo) && $filter->FORPAC_Descripcion != '')
            $this->db->like('FORPAC_Descripcion', $filter->FORPAC_Descripcion, 'right');

         $this->db->order_by('cji_cuentas.CUE_FechaOper',$order)
                ->join('cji_comprobante co', 'co.CPP_Codigo = cji_cuentas.CUE_CodDocumento', 'left')
                ->join('cji_moneda m', 'm.MONED_Codigo = cji_cuentas.MONED_Codigo', 'left')
                ->where($where)
                ->where("cji_cuentas.CUE_FlagEstado",1)
                ->where_in('cji_cuentas.CUE_FlagEstadoPago', $estado) 
                ->select('cji_cuentas.*, co.CPC_Fecha, co.CPC_TipoDocumento, co.CPC_Serie, co.CPC_Numero, co.CPC_TDC, m.MONED_Simbolo');
        $query = $this->db->get('cji_cuentas');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        else
            return array();
    }

    function obtener($cuenta) {
        $where = array('CUE_Codigo' => $cuenta);
        $query = $this->db->where($where)->get('cji_cuentas');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar(stdClass $filter = null) {
        $this->db->insert('cji_cuentas', (array) $filter);
        $id = $this->db->insert_id();
        return $id;
    }

    function modificar_estado($cuenta, $estado) {
        $data = array("CUE_FlagEstadoPago" => $estado);
        $this->db->where('CUE_Codigo', $cuenta);
        $this->db->update("cji_cuentas", $data);
    }

    public function tabla_resumen($f_ini, $f_fin) {
        $where = array('p.PAGC_FechaOper >=' => $f_ini, 'p.PAGC_FechaOper <=' => $f_fin);
        $this->db->select('fp.FORPAP_Codigo,fp.FORPAC_Descripcion,p.*,COUNT(FORPAP_Codigo) AS CANTIDAD');
        $this->db->from('cji_formapago fp');
        $this->db->join('cji_pago p', 'p.PAGC_FormaPago = fp.FORPAP_Codigo', 'left');
        $query = $this->db
                ->where($where)
                ->or_where('p.PAGC_FechaOper IS NULL')
                ->select_sum('p.PAGC_Monto')
                ->group_by('fp.FORPAP_Codigo')
                ->get('');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function eliminar($codigo) {
        $data = array("CUE_FlagEstado" => '0');
        $this->db->where("CUE_Codigo", $codigo);
        $this->db->update('cji_cuentas', $data);
    }

    public function modificar_estado_comprobante($comprobante, $estado) {
        $data = array("CUE_FlagEstadoPago" => $estado);
        $this->db->where('CUE_CodDocumento', $comprobante);
        $this->db->update("cji_cuentas", $data);
    }
    
    public function obtener_segun_comprobante($comprobante) {
        $where = array('CUE_CodDocumento' => $comprobante);
        $query = $this->db->where($where)->get('cji_cuentas');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_alertas(){
        $query = $this->db->order_by('BANP_Codigo')->where('BANC_FlagEstado','1')->get('cji_banco');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
                return $data;       
        }
    }

    public function suma_pago_by_id_comprobante($id_comprobante, $tipo)
        {
            $query = $this->db->from("cji_cuentas");

            $query->join("cji_cuentaspago", "cji_cuentaspago.CUE_Codigo = cji_cuentas.CUE_Codigo");
            $query->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_cuentaspago.MONED_Codigo");

            $query->where("cji_cuentas.CUE_FlagEstado", 1);
            $query->where("cji_cuentaspago.CPAGC_FlagEstado", 1);
            $query->where("cji_cuentas.CUE_TipoCuenta", $tipo == 'ov' ? 1 : 2);
            $query->where("cji_cuentas.CUE_CodDocumento", $id_comprobante);

            return $query->get()->result();
        }

    public function sum_pagos_cuenta($comprobante){

        $sql = "SELECT SUM(pag.PAGC_Monto) as Tpagos, cc.CUE_Monto
                        FROM cji_pago as pag
                            INNER JOIN cji_cuentaspago as cp on cp.PAGP_Codigo = pag.PAGP_Codigo
                            INNER JOIN cji_cuentas as cc on cc.CUE_Codigo = cp.CUE_Codigo
                                WHERE cc.CUE_CodDocumento = $comprobante AND pag.PAGC_FlagEstado = 1 AND cp.CPAGC_FlagEstado = 1 AND cc.CUE_FlagEstado = 1";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getCuentaComprobante($comprobante){
        $sql = "SELECT c.*, cc.CPC_Serie, cc.CPC_Numero
                    FROM cji_cuentas c
                    INNER JOIN cji_comprobante cc ON cc.CPP_Codigo = c.CUE_CodDocumento
                    WHERE c.CUE_CodDocumento = '$comprobante'
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
    

}

?>