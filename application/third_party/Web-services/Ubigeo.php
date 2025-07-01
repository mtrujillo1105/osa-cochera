<?php

/* Connect To Database*/
/*
require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
*/
/**
* 
*/
class Ubigeo
{
	private $ubigeo;
	private $departamento;
	private $provincia;
	private $distrito;

	private $conn;

	function __construct($ubigeo = '000000')
	{
		global $con;
		$this->load->database();
		$this->ubigeo = $ubigeo;

		$this->departamento = substr($this->ubigeo, 0, 2);
		$this->provincia = substr($this->ubigeo, 2, 2);
		$this->distrito = substr($this->ubigeo, 4, 2);

		$this->conn = $con;
	}

	public function listar_departamentos()
	{
		$departamentos = array();

		$result = mysqli_query($this->conn, "SELECT UBIGP_Codigo AS ubigeo, UBIGC_Descripcion AS descripcion, UBIGC_CodDpto AS codigo FROM cji_ubigeo WHERE UBIGC_CodProv = '00' AND UBIGC_CodDist = '00' AND UBIGC_CodDpto != '00'");

		if($result->num_rows > 0) {
			while ($departamento = mysqli_fetch_object($result)) {
				if($departamento->codigo == $this->departamento) $departamento->selected = true;

				$departamentos[] = $departamento;
			}
		}
		
		return $departamentos;
	}

	public function listar_provincias()
	{
		return $this->_listar_provincias();
	}

	public function listar_provincias_by_id_departamento($id_departamento)
	{
		return $this->_listar_provincias($id_departamento);
	}

	public function listar_distritos()
	{
		return $this->_listar_distritos();
	}

	public function listar_distritos_by_id_provincia($id_departamento, $id_provincia)
	{
		return $this->_listar_distritos($id_departamento, $id_provincia);
	}

	private function _listar_distritos($id_departamento = null, $id_provincia = null)
	{
		if($id_departamento) $this->departamento = $id_departamento;
		if($id_provincia) $this->provincia = $id_provincia;

		$distritos = array();

		$result =mysqli_query($this->conn, "SELECT UBIGP_Codigo as ubigeo, UBIGC_Descripcion as descripcion, UBIGC_CodDist as codigo FROM cji_ubigeo WHERE UBIGC_CodDpto = '{$this->departamento}' AND UBIGC_CodProv = '{$this->provincia}' AND UBIGC_CodDist != '00'");

		if($result->num_rows > 0) {
			while ($distrito = mysqli_fetch_object($result)) {
				if($distrito->codigo == $this->distrito) $distrito->selected = true;

				$distritos[] = $distrito;
			}
		}

		return $distritos;
	}

	private function _listar_provincias($id_departamento = null)
	{
		if($id_departamento) $this->departamento = $id_departamento;

		$provincias = array();

		$result = mysqli_query($this->conn, "SELECT UBIGP_Codigo as ubigeo, UBIGC_Descripcion as descripcion, UBIGC_CodProv as codigo FROM cji_ubigeo WHERE UBIGC_CodDpto = '{$this->departamento}' AND UBIGC_CodDist = '00' AND UBIGC_CodProv != '00'");

		if($result->num_rows > 0) {
			while ($provincia = mysqli_fetch_object($result)) {
				if($provincia->codigo == $this->provincia) $provincia->selected = true;

				$provincias[] = $provincia;
			}
		}

		return $provincias;
	}
}