<?php
class Tipocodigo_model extends CI_Model{
    
    public $somevar;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function listar_tipo_codigo(){
		$query = $this->db->order_by('TIPCOD_Codigo','DESC')->where('TIPCOD_FlagEstado','1')->get('cji_tipocodigo');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}
	}
	
	public function obtener_tipoDocumento($tipo){
		$query = $this->db->where('TIPCOD_Codigo',$tipo)->get('cji_tipocodigo');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}		
	}
}
?>