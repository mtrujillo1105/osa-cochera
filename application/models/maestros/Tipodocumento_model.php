<?php
class Tipodocumento_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function listar_tipo_documento(){
		$query = $this->db->where('TIPOCC_FlagEstado','1')->get('cji_tipdocumento');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_tipoDocumento($tipo){
		$query = $this->db->where('TIPDOCP_Codigo',$tipo)->get('cji_tipdocumento');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
}

?>