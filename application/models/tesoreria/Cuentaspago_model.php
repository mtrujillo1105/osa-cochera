<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Cuentaspago_Model extends CI_Model {

	protected $_name = "cji_cuentaspago";
	##  -> Begin
	private $compania;
	##  -> End

	public function __construct() {
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}

	public function listar($cuenta) {

		$sql = "SELECT cp.*, p.PAGP_Serie, p.PAGP_Numero, p.PAGC_Monto, p.PAGC_DepoNro, p.PAGC_Trans, p.CHEP_Codigo, p.PAGC_NotaCredito, p.PAGC_FechaOper, p.PAGC_FormaPago, p.PAGC_Obs, p.CUENT_CodigoCP, p.CUENT_CodigoEmpresa, m.MONED_Simbolo, ch.CHEC_Nro, ch.CHEC_FechaEmision, ch.CHEC_FechaVencimiento, n.CRED_Serie, n.CRED_Numero
		FROM cji_cuentaspago cp
		INNER JOIN cji_pago p ON p.PAGP_Codigo = cp.PAGP_Codigo
		INNER JOIN cji_moneda m ON m.MONED_Codigo = cp.MONED_Codigo
		LEFT JOIN cji_cheque ch ON ch.CHEP_Codigo = p.CHEP_Codigo
		LEFT JOIN cji_nota n ON n.CRED_Codigo = p.PAGC_NotaCredito

		WHERE cp.CUE_Codigo = $cuenta AND cp.CPAGC_FlagEstado LIKE '1'
		ORDER BY cp.CUE_Codigo DESC
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function listar_pago($pago) {
		$where = array("cji_cuentaspago.PAGP_Codigo" => $pago, "CPAGC_FlagEstado" => '1');

		$query = $this->db
		->where($where)
		->join('cji_pago', 'cji_pago.PAGP_Codigo = cji_cuentaspago.PAGP_Codigo', 'left')
		->join('cji_moneda', 'cji_moneda.MONED_Codigo = cji_cuentaspago.MONED_Codigo', 'left')
		->join('cji_cuentas c', 'c.CUE_Codigo = cji_cuentaspago.CUE_Codigo', 'left')
		->join('cji_moneda m2', 'm2.MONED_Codigo = c.MONED_Codigo', 'left')
		->select('cji_cuentaspago.*, cji_pago.PAGC_FechaOper, cji_pago.PAGC_Monto, cji_pago.PAGC_FormaPago, cji_pago.PAGC_Obs, cji_moneda.MONED_Simbolo, c.CUE_FechaOper, m2.MONED_Simbolo MONED_Simbolo2, c.CUE_Monto')
		->get('cji_cuentaspago');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function buscar_x_fechas($f_ini, $f_fin, $tipo_cuenta, $companias = '', $formapago) {
		$where = array('p.PAGC_FechaOper >=' => $f_ini, 'p.PAGC_FechaOper <=' => $f_fin, 'p.PAGC_TipoCuenta' => $tipo_cuenta, 'p.PAGC_FlagEstado' => '1');
		$companias = is_array($companias) ? $companias : array($this->compania);

		$this->db->where($where)
		->where_in('c.COMPP_Codigo', $companias)
		->join('cji_pago p', 'p.PAGP_Codigo = cp.PAGP_Codigo', 'left')
		->join('cji_moneda m', 'm.MONED_Codigo = p.MONED_Codigo', 'left')
		->join('cji_cuentas c', 'c.CUE_Codigo = cp.CUE_Codigo', 'left')
		->join('cji_moneda m2', 'm2.MONED_Codigo = c.MONED_Codigo', 'left')
		->select('p.*, m.MONED_Simbolo, c.CUE_FechaOper, m2.MONED_Simbolo MONED_Simbolo2, c.CUE_Monto, cp.CPAGC_Monto');
		if ($formapago != '') {
			$this->db->where('p.PAGC_FormaPago', $formapago);
		}
		$query = $this->db->from('cji_cuentaspago cp')->get();
		;
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function insertar(stdClass $filter = null) {
		$this->db->insert("cji_cuentaspago", (array) $filter);
		$id = $this->db->insert_id();
		return $id;
	}

	public function anular($cuentaspago) {
		$data = array("CPAGC_FlagEstado" => '0');
		$this->db->where("CPAGP_Codigo", $cuentaspago);
		$this->db->update('cji_cuentaspago ', $data);
	}

	public function eliminar($codigo){
		$data = array("CPAGC_FlagEstado" => '0');
		$this->db->where("CUE_Codigo", $codigo);
		$this->db->update('cji_cuentaspago', $data);
	}
	public function eliminar_delete($codigo){
		$this->db->where("CUE_Codigo", $codigo);
		$this->db->delete('cji_cuentaspago');
	}
	function obtener($codigo){
		$where = array('CUE_Codigo' => $codigo);
		$query = $this->db->where($where)->get('cji_cuentaspago');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

}

?>