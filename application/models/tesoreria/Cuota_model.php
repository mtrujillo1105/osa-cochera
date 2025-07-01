<?php

class Cuota_model extends CI_Model {

    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct() {
        parent::__construct();
        $this->compania = $this->session->userdata("compania");
        $this->empresa = $this->session->userdata("empresa");
        $this->usuario = $this->session->userdata("usuario");
        $this->persona = $this->session->userdata("persona");
    }

    public function getComprobantes($filter = NULL) {

        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_oper = $filter->tipo_oper;

        $where = '';
        if (isset($filter->seriei) && $filter->seriei != '') {
            $where .= ' and cp.CPC_Serie="' . $filter->seriei . '"';
        }
        if (isset($filter->numero) && $filter->numero != '') {
            $where .= ' and cp.CPC_Numero=' . $filter->numero;
        }

        if( isset($filter->fecha_ini) && $filter->fecha_ini != "" && isset($filter->fecha_fin) && $filter->fecha_fin != "" ){
            $where .= " AND cp.CPC_Fecha BETWEEN '$filter->fecha_ini 00:00:00' AND '$filter->fecha_fin 23:59:59' ";
        }

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

        $sql = "SELECT cc.*, cp.CPC_Fecha, cp.CPP_Codigo, cp.CPC_Serie, cp.CPC_Numero, cp.CPC_TipoDocumento, cp.CPC_total, cp.CPC_FlagEstado, m.MONED_Simbolo, cp.CLIP_Codigo, cp.PROVP_Codigo,
                       (SELECT CLIC_CodigoUsuario FROM cji_cliente WHERE cji_cliente.CLIP_Codigo = cp.CLIP_Codigo LIMIT 1) as CLIC_CodigoUsuario,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre
                    FROM comprobantes_cuotas cc
                    INNER JOIN cji_comprobante cp ON cp.CPP_Codigo = cc.CPP_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                    " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                    WHERE cp.CPC_TipoOperacion LIKE '$tipo_oper' AND cp.CPC_FlagEstado = '1' AND cp.COMPP_Codigo = '".$this->compania."' $where

                    GROUP BY cc.CPP_Codigo
                    $order
                    $limit
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    public function lista_cuotas_comprobantes($comprobante){
        $sql = "SELECT cc.*,
                    c.COMPP_Codigo, c.CPC_TipoDocumento, c.CPC_TipoOperacion, c.CPC_Serie, c.CPC_Numero, c.CPC_Fecha, c.CPC_FechaVencimiento, c.MONED_Codigo, c.FORPAP_Codigo, c.CLIP_Codigo, c.PROVP_Codigo, c.CPC_Direccion, c.CPC_FlagEstado,
        			@totalgravado := (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1 AND AFECT_Codigo = 1) as total_gravado,
        			CASE c.CPC_Retencion
        				WHEN NULL
        					THEN c.CPC_total
        				WHEN ''
        					THEN c.CPC_total
        				ELSE c.CPC_total - (@totalgravado * c.CPC_RetencionPorc / 100)
        			END as CPC_total
                    
                    FROM comprobantes_cuotas cc
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cc.CPP_Codigo
                    WHERE cc.CUOT_FlagEstado = 1 AND cc.CPP_Codigo = $comprobante
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }
  
    public function getComprobantesCP($cliente = NULL, $proveedor = NULL){
        $where = ($cliente != NULL && $cliente != "") ? " AND c.CLIP_Codigo = $cliente" : " AND c.PROVP_Codigo = $proveedor";

        $sql = "SELECT c.*
                    FROM cji_comprobante c
                    INNER JOIN comprobantes_cuotas cc ON cc.CPP_Codigo = c.CPP_Codigo
                    WHERE c.CPC_FlagEstado IN(0,1) $where
                    GROUP BY c.CPP_Codigo
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function getCuota($cuota){
        $sql = "SELECT cc.*,
                    c.COMPP_Codigo, c.CPC_TipoDocumento, c.CPC_TipoOperacion, c.CPC_Serie, c.CPC_Numero, c.CPC_Fecha, c.CPC_FechaVencimiento, c.MONED_Codigo, c.FORPAP_Codigo, c.CLIP_Codigo, c.PROVP_Codigo, c.CPC_Direccion,
                    @totalgravado := (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1 AND AFECT_Codigo = 1) as total_gravado,
                    CASE c.CPC_Retencion
                        WHEN NULL
                            THEN c.CPC_total
                        WHEN ''
                            THEN c.CPC_total
                        ELSE c.CPC_total - (@totalgravado * c.CPC_RetencionPorc / 100)
                    END as CPC_total, c.CPC_FlagEstado
                    
                    FROM comprobantes_cuotas cc
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cc.CPP_Codigo
                    WHERE cc.CUOT_FlagEstado = 1 AND cc.CUOT_Codigo = $cuota
                ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return NULL;
    }

    public function obtener_cuotas($idcuota){
        $query = $this->db->select('*')
                          ->where("CUOT_Codigo",$idcuota)
                          ->get('comprobantes_cuotas');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
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

    public function modificarCuota($idcuota,$filter){
        $this->db->where('CUOT_Codigo',$idcuota);
        return $this->db->update('comprobantes_cuotas',$filter);
    }

    public function insertarNvaCuota($comprobante, $filter){
        $fechaR = date("Y-m-d h:m:s");

        $sql = "SET @numero = 1 + (SELECT max(CUOT_Numero) FROM comprobantes_cuotas WHERE CPP_Codigo = $comprobante)";
        
        $this->db->query($sql);
        
        $sql = "INSERT INTO `comprobantes_cuotas` (`CUOT_Codigo`, `CUOT_Numero`, `CPP_Codigo`, `CUOT_Monto`, `CUOT_FechaInicio`, `CUOT_Fecha`, `CUOT_FlagFisica`, `CUOT_FlagEstado`, `CUOT_FlagPagado`, `CUOT_FechaRegistro`, `CUOT_FechaModificacion`, `CUOT_UsuarioRegistro`, `CUOT_UsuarioModifica`, `CUOT_TipoCuenta`, `CUOT_TipoTributo`, `PROVP_Codigo`, `CUOT_NumBanco`)
                VALUES (NULL, @numero, '$comprobante', '$filter->CUOT_Monto', '$filter->CUOT_FechaInicio', '$filter->CUOT_Fecha', '0', '1', b'$filter->CUOT_FlagPagado', '$fechaR', NOW(), '0', '0', '1', NULL, NULL, NULL)";

        $this->db->query($sql);
        return $this->db->insert_id();
    }

    public function borrarCuota($idcuota,$filter){
        $this->db->where('CUOT_Codigo',$idcuota);
        return $this->db->update('comprobantes_cuotas',$filter);
    }

    public function delete($comprobante){
        $this->db->where('CPP_Codigo', $comprobante);
        return $this->db->delete('comprobantes_cuotas');
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

    public function obtener_comprobante_letras($comprobante){
        $sql = "SELECT * FROM comprobantes_cuotas WHERE CPP_Codigo = $comprobante AND CUOT_FlagEstado = 1";
        $query = $this->db->query($sql);
        $data = array();

        if ($query->num_rows() > 0){
            foreach ($query->result() as $key => $value) {
                $data[] = $value;
            }
            return $data;
        }
        else
            return NULL;
    }

    public function getComprobanteInfo($filter){
    	$where = "";

    	if ($filter->comprobante != NULL && $filter->comprobante != "")
    		$where .= "cc.CPP_Codigo = $filter->comprobantes_cuotas";

    	if ($filter->cuota != NULL && $filter->cuota != "")
    		$where .= "cc.CUOT_Codigo = $filter->cuota";

    	$sql = "SELECT c.*
    				FROM comprobantes_cuotas cc
    				INNER JOIN cji_comprobante c ON c.CPP_Codigo = cc.CPP_Codigo
    				WHERE $where
    			";
    	$query = $this->db->query($sql);

    	if ($query->num_rows() > 0)
    		return $query->result();
    	else
    		return NULL;
    }

#######################################################
##################### METODOS COMPROBANTE
#######################################################

    public function registrar($filter){
		$this->db->insert("comprobantes_cuotas", (array) $filter);
	}

	public function insertar($idComprobante, Array $cuotas){
		$this->db->trans_begin();

		foreach ($cuotas as $cuota) {
			$this->db->insert("comprobantes_cuotas", array(
				 "CUOT_Numero" => $cuota["numero"],
				 "CPP_Codigo" => $idComprobante,
				 "CUOT_Monto" => str_replace(",", "", $cuota["monto"]),
				 "CUOT_FechaInicio" => $cuota["fechai"],
				 "CUOT_Fecha" => $cuota["fecha"],
				 "CUOT_FlagFisica" => $cuota["fisica"],
				 "CUOT_TipoCuenta" => $cuota["tipo_cuenta"],
				 "CUOT_TipoTributo" => isset($cuota["tipo_tributo"]) ? $cuota["tipo_tributo"] : NULL,
				 "PROVP_Codigo" => $cuota["proveedor"]
				));
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}else{
			$this->db->trans_commit();
		}
	}

	public function modificar($idComprobante, Array $cuotas)
	{
		$cuotas = $cuotas;
		$this->db->trans_begin();

		$list = $this->db->get_where("comprobantes_cuotas",array(
				"CPP_Codigo" => $idComprobante
			))->result();

		foreach ($list as $cuota) {
			$data = array();
			if(isset($cuotas[$cuota->CUOT_Numero])){
				$data = array(
					"CUOT_Monto" => str_replace(",", "", $cuotas[$cuota->CUOT_Numero]["monto"]),
					"CUOT_FechaInicio" => $cuotas[$cuota->CUOT_Numero]["fechai"],
					"CUOT_Fecha" => $cuotas[$cuota->CUOT_Numero]["fecha"],
					"CUOT_FlagFisica" => $cuotas[$cuota->CUOT_Numero]["fisica"],
					"CUOT_FlagEstado" => 1,
					"CUOT_TipoTributo" => (isset($cuotas["tipo_tributo"]) ? $cuota[$cuota->CUOT_Numero]["tipo_tributo"] : NULL)
				);

				unset($cuotas[$cuota->CUOT_Numero]);
			}else {
				$data["CUOT_FlagEstado"] = 0;
			}

			$this->db->where("CUOT_Codigo", $cuota->CUOT_Codigo)
					->update("comprobantes_cuotas", $data);
		}

		foreach ($cuotas as $cuota) {
			$this->db->insert("comprobantes_cuotas", array(
				 "CUOT_Numero" => $cuota["numero"],
				 "CPP_Codigo" => $idComprobante,
				 "CUOT_Monto" => str_replace(",", "", $cuota["monto"]),
				 "CUOT_Fecha" => $cuota["fecha"],
				 "CUOT_FlagFisica" => $cuota["fisica"],
				 "PROVP_Codigo" => $cuota["proveedor"],
				 "CUOT_TipoCuenta" => $cuota["tipo_cuenta"]
			));
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return false;
		}else {
			$this->db->trans_commit();
		}

		return true;
	}

	public function modificar_cuota_tributo($id_comprobante, $data)
	{
		$this->db->trans_begin();

		$this->db->where("CPP_Codigo", $id_comprobante)
				->where("CUOT_Numero", 0)
				->update("comprobantes_cuotas", $data);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return FALSE;
		}else {
			$this->db->trans_commit();
			return TRUE;
		}
	}

	public function generarLetras($idComprobante)
	{
		$this->db->trans_begin();

		if($this->db->trans_status() ===FALSE){
			$this->db->trans_rollback();
			return false;
		}else {
			$this->db->trans_commit();
		}
	}

	public function listarByIdComprobante($idComprobante)
	{
		return $this->db->from("comprobantes_cuotas")
						->where("CPP_Codigo", $idComprobante)
						->where("CUOT_FlagEstado", 1)
						->where("CUOT_TipoTributo", NULL)
						->order_by("CUOT_Numero")
						->get()->result();
	}

	public function existe_cuota_tributaria_by_comprobante($id_comprobante)
	{
		$data = $this->db->get_where("comprobantes_cuotas", array(
			"CPP_Codigo" => $id_comprobante,
			"CUOT_Numero" => 0
		))->result();

		if(count($data) == 0) return false;

		return true;
	}

}

?>