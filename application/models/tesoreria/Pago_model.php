<?php

class Pago_Model extends CI_Model{

	protected $_name = "cji_pago";
	private $compania;

	public function __construct(){
		parent::__construct();
		$this->load->helper('pago');
		$this->load->model('tesoreria/cheque_model');

		$this->compania = $this->session->userdata('compania');
	}

  ## FUNCTIONS NEWS

	public function insertar($filter){

		#$this->db->trans_start();

		if ( $filter->CHEC_Nro != "" && $filter->CHEC_FechaEmision != "" && $filter->CHEC_FechaVencimiento != "" ){
			$cheque = new stdCLass();
			$cheque->CHEC_Nro = $filter->CHEC_Nro;
			$cheque->CHEC_FechaEmision = $filter->CHEC_FechaEmision;
			$cheque->CHEC_FechaVencimiento = $filter->CHEC_FechaVencimiento;
			$cheque->COMPP_Codigo = $this->compania;
			$cheque->CHEC_FechaRegistro = date("Y-m-d H:i:s");
			$cheque->CHEC_FlagEstado = "1";


			$this->db->insert("cji_cheque", $cheque);
			$filter->CHEP_Codigo = $this->db->insert_id();
		}

		unset($filter->CHEC_Nro);
		unset($filter->CHEC_FechaEmision);
		unset($filter->CHEC_FechaVencimiento);

		$this->db->insert("cji_pago", $filter);
		$id = $this->db->insert_id();

		switch ($filter->PAGC_TipoCuenta) {
			case '1':
			$doc = '20';
			break;
			case '2':
			$docu = '21';
			break;
		}

		$this->configuracion_model->modificar_configuracion($filter->COMPP_Codigo, $doc, $filter->PAGP_Numero);
		/*
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit();
		*/
			return $id;
		#}
	}

	public function registrar_pago_cuota($filter){
		$this->db->insert("cji_pago", $filter);
		$id = $this->db->insert_id();

		switch ($filter->PAGC_TipoCuenta) {
			case '1':
			$doc = '20';
			break;
			case '2':
			$docu = '21';
			break;
		}

		$this->configuracion_model->modificar_configuracion($filter->COMPP_Codigo, $doc, $filter->PAGP_Numero);
		return $id;
	}

	## FUNCTIONS OLDS

	public function listar($cuenta){
		$where = array("CUE_Codigo" => $cuenta, "FLUCAJ_FlagEstado" => '1');

		$query = $this->db->order_by('FLUCAJ_FechaOperacion')
		->join('cji_formapago', 'cji_formapago.FORPAP_Codigo = cji_flujocaja.FORPAP_Codigo', 'left')
		->where($where)
		->select('cji_flujocaja.*, cji_formapago.FORPAC_Descripcion FORPAC_Descripcion')
		->from('cji_flujocaja')
		->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function listar_ultimos($tipo_cuenta, $codigo, $nummax = ''){
		$where = array("PAGC_TipoCuenta" => $tipo_cuenta, "PAGC_FlagEstado" => '1');
		if ($tipo_cuenta == '1')
			$where = array('cji_pago.CLIP_Codigo' => $codigo);
		else
			$where = array('cji_pago.PROVP_Codigo' => $codigo);

		$query = $this->db->group_by('cji_cuentaspago.PAGP_Codigo')->order_by('PAGC_FechaOper', 'DESC')->order_by('PAGC_FechaRegistro', 'DESC')
		->where($where)
		->join('cji_pago', 'cji_cuentaspago.PAGP_Codigo = cji_pago.PAGP_Codigo')
		->join('cji_moneda', 'cji_moneda.MONED_Codigo = cji_pago.MONED_Codigo', 'left')
		->join('cji_cuentas', 'cji_cuentaspago.CUE_Codigo=cji_cuentas.CUE_Codigo')
		->join('cji_comprobante', 'cji_cuentas.CUE_CodDocumento=cji_comprobante.CPP_Codigo')
		->select('cji_pago.*, cji_moneda.MONED_Simbolo,cji_comprobante.CPC_Serie,cji_comprobante.CPC_Numero,cji_comprobante.CPC_TipoDocumento')
		->from('cji_cuentaspago', $nummax)
		->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		else
			return array();
	}

	public function buscar_x_fechas($f_ini, $f_fin, $tipo_cuenta)
	{
		$where = array('PAGC_FechaOper >=' => $f_ini, 'PAGC_FechaOper <=' => $f_fin, 'PAGC_TipoCuenta' => $tipo_cuenta, 'PAGC_FlagEstado' => '1');
		$query = $this->db->where($where)
		->join('cji_moneda m', 'm.MONED_Codigo = p.MONED_Codigo', 'left')
		->select('p.*, m.MONED_Simbolo')->from('cji_pago p')->get();
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function anular($pago)
	{
		$data = array("PAGC_FlagEstado" => '0');
		$this->db->where("PAGP_Codigo", $pago);
		$this->db->update('cji_pago ', $data);
	}

	public function eliminar_delete($pago)
	{
		$this->db->where("PAGP_Codigo", $pago);
		$this->db->update('cji_pago ');
	}

	public function sumar_pagos($listado_pagos, $moneda = 2){
		$suma = 0;

		if($listado_pagos!=null && count($listado_pagos)>0){
			foreach ($listado_pagos as $indice => $valor) {
				$suma += round(cambiar_moneda($valor->CPAGC_Monto, $valor->CPAGC_TDC, $valor->MONED_Codigo, $moneda), 2);
			}
		}
		return $suma;
	}

	public function total_pagos($pagos){
		$suma = 0;
		if ($pagos != NULL && count($pagos) > 0){
			foreach ($pagos as $i => $val){
				$suma += round(($val->CPAGC_Monto * $val->CPAGC_TDC), 3);
			}
		}
		return $suma;
	}

	public function obtener_forma_pago($forma_pago)
	{
		$result = '';
		switch ($forma_pago) {
			case '1':
			$result = 'EFECTIVO';
			break;
			case '2':
			$result = 'DEPOSITO';
			break;
			case '3':
			$result = 'CHEQUE';
			break;
			case '4':
			$result = 'CANJE POR FACTURA';
			break;
			case '5':
			$result = 'NOTAS DE CREDITO';
			break;
			case '6':
			$result = 'DESCUENTO';
			break;
			case '7' :
			$result = 'TRANSFERENCIA';
			break;
		}

		return $result;
	}
}

?>