<?php
class Mantenimiento_model extends CI_Model{
	function __construct(){
		parent::__construct();

	}
        public function obtener_numero_documento($compania, $documento){
            $query = $this->db->where('COMPP_Codigo',$compania)->where('DOCUP_Codigo',$documento)->get('cji_configuracion');
            if($query->num_rows() > 0){
                foreach($query->result() as $fila)
                {
                    $data[] = $fila;
                }
                return $data;
            }
        }
        public function modificar_configuracion($compania, $documento, $numero){
            $data = array(
                        "CONFIC_Numero" =>$numero,
                    );
            
            $where = array("COMPP_Codigo"=>$factura, "DOCUP_Codigo");
            $this->db->where($where);
            $this->db->update('cji_configuracion',$data);
        }
}
?>