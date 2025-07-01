<?php
class Documento_sentencia_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->compania = $this->session->userdata('compania');
      $this->usuario = $this->session->userdata('user');
        //$this->somevar['hoy']              = mdate("%Y-%m-%d %h:%i:%s",time());
    }

    public function buscar($codigoCompConfDoc)
    {
    	if($codigoCompConfDoc!=null && trim($codigoCompConfDoc)!="" && $codigoCompConfDoc!=0)
    	$this->db->where("COMPCONFIDOCP_Codigo",$codigoCompConfDoc);
    	
    	$this->db->order_by("DOCSENT_Tipo","ASC");
    	$query = $this->db->get('cji_documentosentenica');
    	
    	if($query->num_rows() > 0){
    		foreach($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }
    public function obtener($codigo)
    {
        $query = $this->db->where('DOCSENT_Codigo',$codigo)->get('cji_documentosentenica');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
  public function insertar(stdClass $filter = null)
    {
        $this->db->insert("cji_documentosentenica",(array)$filter);
    }
    public function modificar($codigo,$filter)
    {
        $this->db->where("DOCSENT_Codigo",$id);
        $this->db->update("cji_documentosentenica",(array)$filter);
    }
    public function eliminar($codigo)
    {
        $this->db->delete('cji_documentosentenica',array('DOCSENT_Codigo' => $$codigo));
    }
    
    public function eliminar_configuracion($codigo)
    {
    	$this->db->delete('cji_documentosentenica',array('COMPCONFIDOCP_Codigo	' => $codigo));
    }
    
     public  function ejecutarSentencia($sentencia){
        $resultado=$this->db->query($sentencia);
        if($resultado->num_rows>0){
            foreach($resultado->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    
    public  function validarSentecia($sentencia){
    	$resultado=$this->db->query($sentencia);
		$detallesTabla=$resultado->field_data();
    	return $detallesTabla;
    }
    
    
    
}
?>