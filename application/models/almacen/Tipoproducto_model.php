<?php
class Tipoproducto_model extends CI_Model{
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('almacen/plantilla_model');
        $this->compania = $this->session->userdata('compania');
    }
        
    function listar_tipos_producto($flagBS, $number_items='',$offset=''){
        $query = $this->db->order_by('TIPPROD_Descripcion')->where('TIPPROD_FlagEstado','1')->where('TIPPROD_FlagBienServicio', $flagBS)->get('cji_tipoproducto', $number_items,$offset);
        $data = array();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                    $data[] = $fila;
            }
        }
        return $data;
    }
    
    function obtener_tipo_producto($tipoProducto){
        $query = $this->db->where("TIPPROD_Codigo",$tipoProducto)->get("cji_tipoproducto");
        $data = array();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;		
        }			
    }
        
	function insertar_tipo_producto($descripcion, $atributo, $flagBS){
		$data = array("TIPPROD_Descripcion" => strtoupper($descripcion), "TIPPROD_FlagBienServicio"=>$flagBS);
		$this->db->insert("cji_tipoproducto",$data);
                $tipo = $this->db->insert_id();
                
                //Inserta atributos
                if(is_array($atributo)){
                    foreach($atributo as $indice=>$valor){
                        $attrib = $valor;
                        if($valor!='')   
                            $this->plantilla_model->insertar_plantilla($tipo, $attrib);
                    }
                }
	}
	function modificar_tipo_producto($tipo,$descripcion, $atributo){
		$data = array("TIPPROD_Descripcion" => strtoupper($descripcion));
		$this->db->where('TIPPROD_Codigo',$tipo);
		$this->db->update("cji_tipoproducto",$data);
                
                $this->plantilla_model->eliminar_plantilla_por_tipo($tipo);
                //Inserta atributos
                if(is_array($atributo)){
                    foreach($atributo as $indice=>$valor){
                        $attrib        = $valor;
                        if($valor!='')
                            $this->plantilla_model->insertar_plantilla($tipo, $attrib);
                    }
                }
	}
	function eliminar_tipo_producto($tipo){
		$data  = array("TIPPROD_FlagEstado" => '0');
		$where = array("TIPPROD_Codigo"     => $tipo); 
		$this->db->where($where);
		$this->db->update('cji_tipoproducto',$data);	
	}
        
        public function buscar_tipo_producto($filter,$number_items='',$offset='')
	{
            if(isset($filter->nombre_tipoprod) && $filter->nombre_tipoprod!='')
                $this->db->like('TIPPROD_Descripcion',$filter->nombre_tipoprod,'both');
            $query = $this->db->where('TIPPROD_FlagEstado','1')->where('TIPPROD_FlagBienServicio',$filter->flagBS)->get('cji_tipoproducto',$number_items,$offset);
            if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
            }
	}

}
?>