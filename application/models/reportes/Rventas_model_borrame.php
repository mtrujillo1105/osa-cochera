<?php
class Rventas_Model extends CI_Model
{

  public function __construct()
  {
      parent::__construct();
      $this->load->database();
      $this->load->helper('date');
      $this->compania = $this->session->userdata('compania');
      /*$this->somevar ['compania'] = $this->session->userdata('compania');
      $this->somevar ['usuario']  = $this->session->userdata('usuario');
      $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());*/
  }
  
  public function ventas_por_vendedor_resumen($inicio,$fin)
  {
    //$inicio = human_to_mysql($inicio);
    //$fin = human_to_mysql($fin);
    $sql = "SELECT SUM(CPC_Total) as VENTAS,
	p.PERSC_Nombre as NOMBRE,
	p.PERSC_ApellidoPaterno as PATERNO 
	FROM cji_usuario u 
	LEFT JOIN cji_comprobante c ON c.USUA_Codigo = u.USUA_Codigo 
	JOIN cji_persona p ON u.PERSP_Codigo = p.PERSP_Codigo 
	WHERE c.CPC_Fecha BETWEEN '$inicio' AND '$fin' GROUP BY c.USUA_Codigo";
    $query = $this->db->query($sql);
    
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  
  
  
}
?>