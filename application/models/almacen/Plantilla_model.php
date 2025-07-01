<?php
class Plantilla_model extends CI_Model{
    var $somevar;
	function __construct(){
		parent::__construct();
		$this->load->database();
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']              = date("%Y-%m-%d %h:%i:%s",time());
	}
	function insertar_plantilla($tipo,$descripcion){
		$data = array(
                              "TIPPROD_Codigo" => $tipo,
                              "ATRIB_Codigo"  => $descripcion
                             );
		$this->db->insert("cji_plantilla",$data);
	}
        function obtener_plantilla($tipo){
                $query = $this->db->where("TIPPROD_Codigo",$tipo)->get("cji_plantilla");
                if($query->num_rows() > 0){
                        foreach($query->result() as $fila){
                                $data[] = $fila;
                        }
                        return $data;           
                }       
        }

        function listar_plantilla($tipoprod){
		$query = $this->db->select('cji_plantilla.PLANT_Codigo, cji_atributo.ATRIB_Codigo  ,cji_atributo.ATRIB_Descripcion ATRIB_Descripcion, cji_atributo.ATRIB_TipoAtributo ATRIB_TipoAtributo')
                                  ->order_by('ATRIB_Descripcion')
                                  ->join('cji_atributo', 'cji_atributo.ATRIB_Codigo = cji_plantilla.ATRIB_Codigo ')
                                  ->where('TIPPROD_Codigo',$tipoprod)
                                  ->get('cji_plantilla');
                
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
                else
                    return array();
	}
        public function eliminar_plantilla($id)
        {
            $this->db->delete('cji_plantilla', array('PLANT_Codigo' => $id));
        }
        public function eliminar_plantilla_por_tipo($tipoprod)
        {
            $this->db->delete('cji_plantilla', array('TIPPROD_Codigo ' => $tipoprod));
        }
}
?>