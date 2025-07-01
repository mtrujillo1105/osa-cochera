<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Tipocambio_Model extends CI_Model {

	protected $_name = "cji_tipocambio";
  ##  -> Begin - El array somevar es reemplazado por atributos
	private $compania;
	##  -> End

	##  -> Begin
	public function __construct() {
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}
	##  -> End

	public function cambioxdia($fecha_dia) {
		$this->db->where("TIPCAMC_Fecha", "$fecha_dia");
		$query = $this->db->get('cji_tipocambio');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function get_tdc_dia($fecha, $moneda, $compania = NULL) {

		$compania = ( $compania == NULL ) ? $this->compania : $compania;
		$sql = "
				SELECT * 
				FROM cji_tipocambio 
				WHERE TIPCAMC_MonedaDestino = $moneda 
				AND COMPP_Codigo = $compania 
				AND TIPCAMC_Fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59'
				";
		$query = $this->db->query($sql);

		$data = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $i => $val) {
				$data[] = $val;
			}
		}
		return $data[0];
	}

	public function obtener_tdcxfactura($fecha) {
		$this->db->select('TIPCAMC_FactorConversion')->where("TIPCAMC_Fecha", "$fecha");
		$query = $this->db->get('cji_tipocambio');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function listar($fecha = '', $number_items = '', $offset = '') {
		if ($fecha != '')
			$this->db->where('TIPCAMC_Fecha', $fecha);
		$this->db->where('TIPCAMC_FlagEstado', 1);
		$this->db->order_by('TIPCAMC_Fecha', 'desc');
		$this->db->group_by('TIPCAMC_Fecha');
		$query = $this->db->get('cji_tipocambio', $number_items, $offset);

		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function obtener($id) {
		$where = array("cji_tipocambio" => $id);
		$query = $this->db->where($where)->get('cji_tipocambio', 1);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function obtener2($moneda_id) {
		$where = array("TIPCAMC_MonedaOrigen" => 1, "TIPCAMC_MonedaDestino" => $moneda_id, "TIPCAMC_FlagEstado" => 1);
		$query = $this->db->order_by("TIPCAMP_Codigo", "desc")->where($where)->get('cji_tipocambio', 1);
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function eliminar_varios(stdClass $filter = null) {
		$where =NULL;
		if (isset($filter->TIPCAMC_Fecha) && $filter->TIPCAMC_Fecha != ""){
			$where = array("TIPCAMC_Fecha" => $filter->TIPCAMC_Fecha);
		}
		if($where!=NULL){
			$this->db->delete('cji_tipocambio', $where);
		}


	}

	public function buscar($filter, $number_items = '', $offset = '') {
		if (isset($filter->TIPCAMC_Fecha) && $filter->TIPCAMC_Fecha != "")
			$this->db->where('TIPCAMC_Fecha', $filter->TIPCAMC_Fecha);

		if (isset($filter->TIPCAMC_MonedaDestino) && $filter->TIPCAMC_MonedaDestino != "")
			$this->db->where('TIPCAMC_MonedaDestino', $filter->TIPCAMC_MonedaDestino);

		$query = $this->db->where('COMPP_Codigo', $this->compania)
		->where('TIPCAMC_FlagEstado', '1')
		->order_by('TIPCAMC_Fecha', 'desc')
		->get('cji_tipocambio', $number_items, $offset);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

    //PARA EL CASO DE LAS FECHAS VALIDAS DE NUESTRO TIPO DE CAMBIO
	public function obtener_x_fecha($fecha = "") {
		if ($fecha != '')
			$this->db->where('TIPCAMC_Fecha', $fecha);
		$this->db->where('TIPCAMC_FlagEstado', 1);
		$this->db->order_by('TIPCAMC_Fecha', 'desc');
		$this->db->group_by('TIPCAMC_Fecha');
		$query = $this->db->get('cji_tipocambio');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function obtener_tdc_dolar($fecha) {
		$query = $this->db->where('TIPCAMC_Fecha', "$fecha")->get('cji_tipocambio');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}  
	public function tdc_dolar_faltan_ingresar() {

		$query = $this->db->where('TIPCAMC_FactorConversion', "0.00")->get('cji_tipocambio');

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}

	}
	public function tdc_dolar_ult() {

		$this->db->order_by('TIPCAMC_Fecha', 'desc');
		$this->db->limit('1');
		$query = $this->db->get('cji_tipocambio');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	## FUNCIONES PARA EL TIPO DE CAMBIO 2020

	public function insertar(stdClass $filter = null) {

    # CONSULTO TODAS LAS COMPAÑIAS
		$sqlCompanias = "SELECT COMPP_Codigo FROM cji_compania";
		$comp = $this->db->query($sqlCompanias);

    # INGRESO EL TIPO DE CAMBIO DE CADA COMPAÑIA CONSULTADA
		foreach ($comp->result() as $key => $value) {
			$filter->COMPP_Codigo = $value->COMPP_Codigo;
			$this->db->insert("cji_tipocambio", (array) $filter);
		}
		return $this->db->insert_id();
	}

	public function update_tc($filter) {
		$sql = "UPDATE cji_tipocambio SET TIPCAMC_FactorConversion = '$filter->TIPCAMC_FactorConversion' WHERE TIPCAMC_Fecha = '$filter->TIPCAMC_Fecha' AND TIPCAMC_MonedaDestino = $filter->TIPCAMC_MonedaDestino";
		$query = $this->db->query($sql);

		$success = ($query == true) ? 1 : 0;
		return $success;
	}

	public function tcExists($fecha, $compania, $moneda){
		$sql = "SELECT TIPCAMC_FactorConversion FROM cji_tipocambio WHERE TIPCAMC_Fecha = '$fecha' AND COMPP_Codigo = $compania AND TIPCAMC_MonedaDestino = $moneda";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return true;
		else
			return false;
	}

	public function getTCday($fecha){
		$monedaOrigen = 1;

		$sql = "SELECT m.MONED_Codigo, m.MONED_Descripcion, m.MONED_Simbolo, tc.TIPCAMC_FactorConversion, (SELECT ms.MONED_Descripcion FROM cji_moneda ms WHERE ms.MONED_Codigo = $monedaOrigen) as moneda_origen
		FROM cji_moneda m
		LEFT JOIN cji_tipocambio tc
		ON tc.TIPCAMC_MonedaDestino = m.MONED_Codigo AND tc.TIPCAMC_Fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND tc.COMPP_Codigo = '$this->compania'

		WHERE m.MONED_Codigo <> $monedaOrigen AND m.MONED_FlagEstado LIKE '1'
		";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function getCambio($monedaDestino, $fecha, $monedaOrigen = 0){

		if ($monedaOrigen != 0){
			if ($monedaDestino == 1){
				$origen = $monedaDestino;
				$destino = $monedaOrigen;
			}
			else{
				$origen = $monedaOrigen;
				$destino = $monedaDestino;
			}

			$sql = "SELECT tc.TIPCAMC_FactorConversion
			FROM cji_tipocambio tc
			WHERE tc.TIPCAMC_Fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59'
			AND tc.COMPP_Codigo = $this->compania
			AND tc.TIPCAMC_MonedaOrigen = '$origen'
			AND tc.TIPCAMC_MonedaDestino = '$destino'
			LIMIT 1
			";
		}
		else
			$sql = "SELECT tc.TIPCAMC_FactorConversion FROM cji_tipocambio tc WHERE tc.TIPCAMC_Fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND tc.COMPP_Codigo = $this->compania AND tc.TIPCAMC_MonedaDestino = '$monedaDestino' LIMIT 1";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			$tasa = $query->row();

			if ($monedaDestino == 1)
				return (1 / $tasa->TIPCAMC_FactorConversion);
			else
				return $tasa->TIPCAMC_FactorConversion;
		}
		else
			if ($monedaOrigen == $monedaDestino)
				return 1;
			else
				return 0;
		}

		public function getListTipoCambio( $filter = NULL ){
			$monedaOrigen = 1;

			$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
			$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

			$sql = "SELECT m.MONED_Codigo, m.MONED_Descripcion, m.MONED_Simbolo, tc.TIPCAMC_FactorConversion,
			(SELECT ms.MONED_Simbolo FROM cji_moneda ms WHERE ms.MONED_Codigo = $monedaOrigen) as simbolo_origen,
			(SELECT ms.MONED_Descripcion FROM cji_moneda ms WHERE ms.MONED_Codigo = $monedaOrigen) as moneda_origen

			FROM cji_moneda m
			LEFT JOIN cji_tipocambio tc
			ON tc.TIPCAMC_MonedaDestino = m.MONED_Codigo AND tc.TIPCAMC_Fecha BETWEEN '$filter->fecha 00:00:00' AND '$filter->fecha 23:59:59' AND tc.COMPP_Codigo = $this->compania

			WHERE m.MONED_Codigo <> $monedaOrigen AND m.MONED_FlagEstado LIKE '1'

			$order
			$limit
			";
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0) {
				return $query->result();
			}
			else
				return NULL;
		}
	}
	?>