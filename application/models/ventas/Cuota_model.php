<?php

class Cuota_model extends CI_Model {

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