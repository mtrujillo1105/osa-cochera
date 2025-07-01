<?php

class Comprobanteguiarem_model extends CI_Model {

 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());
    }


    public function listarGuiarem($codigoComprobante)
    {
    	$this->db->select('cji_comprobante_guiarem.*');
    	$this->db->from('cji_comprobante_guiarem');
    	$this->db->join('cji_guiarem','cji_guiarem.GUIAREMP_Codigo=cji_comprobante_guiarem.GUIAREMP_Codigo');
    	$this->db->where('cji_comprobante_guiarem.CPP_Codigo',$codigoComprobante);
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    		return $query->result();
    	else
    		return array();
    
    }
    
    public function listarComprobante($codigoGuiarem)
    {
    	$this->db->select('cji_comprobante_guiarem.*');
    	$this->db->from('cji_comprobante_guiarem');
    	$this->db->join('cji_comprobante','cji_comprobante.CPP_Codigo=cji_comprobante_guiarem.CPP_Codigo');
    	$this->db->where('cji_comprobante_guiarem.GUIAREMP_Codigo',$codigoGuiarem);
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    		return $query->result();
    	else
    		return array();
    
    }
    
    
    public function obtener($codigoComprobanteGuiarem)
    {
    	$where = array("COMPGUI_Codigo"=>$codigoComprobanteGuiarem);
    	$query = $this->db->where($where)->get('cji_comprobante_guiarem');
    	if($query->num_rows() > 0){
    		return $query->result();
    	}
    }
    public function insertar($codigoComprobante,$codigoGuiarem)
    {
    	$data = array("CPP_Codigo"=>$codigoComprobante,"GUIAREMP_Codigo"=>$codigoGuiarem,"COMPGUI_FlagEstado"=>1);
    	$this->db->insert("cji_comprobante_guiarem",$data);
    }
    public function modificar($id,$filter)
    {
    	$this->db->where("COMPGUI_Codigo",$id);
    	$this->db->update("cji_comprobante_guiarem",(array)$filter);
    }
    public function eliminar($id)
    {
    	$this->db->delete('cji_comprobante_guiarem',array('GUIAREMP_Codigo' => $id));
    }
    public function eliminar2($id)
    {
    	$this->db->delete('cji_comprobante_guiarem',array('	CPP_Codigo' => $id));
    }

}

?>