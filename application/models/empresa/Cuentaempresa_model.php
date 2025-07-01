<?php
class Cuentaempresa_model extends CI_Model
{
    var $somevar;
    public function __construct()
    {
      
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }

public function listCuentaPersona($filter=null, $number_items='',$offset=''){
  $this->db->select('c.CUENT_Codigo,c.CUENT_NumeroEmpresa,c.CUENT_Titular,
                      c.CUENT_TipoPersona,c.CUENT_FechaRegistro,b.BANC_Nombre
                     ,m.MONED_Descripcion,c.CUENT_TipoCuenta');
   // $this->db->from('cji_cuentasempresas c ','INNER');
    $this->db->join('cji_banco b','b.BANP_Codigo=c.BANP_Codigo');
    $this->db->join('cji_moneda m','m.MONED_Codigo=c.MONED_Codigo');
   // if(isset($filter->EMPRE_Codigo) && $filter->EMPRE_Codigo!=''){
        $this->db->where('PERSP_Codigo',$filter);//->EMPRE_Codigo);
        $this->db->where('PERSP_Codigo !=',0);//->EMPRE_Codigo);
        
    //}
    //$this->db->where('EMPRE_Codigo',$filter->EMPRE_Codigo); 
    $this->db->where('CUENT_FlagEstado',"1");
    $this->db->order_by('CUENT_FechaRegistro','ASC');
    
    $query= $this->db->get('cji_cuentasempresas c ', $number_items,$offset);
    if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
 }
}

public function listCuentaEmpresa($filter=null, $number_items='',$offset=''){
  $this->db->select('c.CUENT_Codigo,c.CUENT_NumeroEmpresa,c.CUENT_Titular,
                      c.CUENT_TipoPersona,c.CUENT_FechaRegistro,b.BANC_Nombre
                     ,m.MONED_Descripcion,c.CUENT_TipoCuenta');
   // $this->db->from('cji_cuentasempresas c ','INNER');
    $this->db->join('cji_banco b','b.BANP_Codigo=c.BANP_Codigo');
    $this->db->join('cji_moneda m','m.MONED_Codigo=c.MONED_Codigo');
   // if(isset($filter->EMPRE_Codigo) && $filter->EMPRE_Codigo!=''){
        $this->db->where('EMPRE_Codigo',$filter);//->EMPRE_Codigo);
    //}
    //$this->db->where('EMPRE_Codigo',$filter->EMPRE_Codigo); 
    $this->db->where('CUENT_FlagEstado',"1");
    $this->db->order_by('CUENT_FechaRegistro','ASC');
    
    $query= $this->db->get('cji_cuentasempresas c ', $number_items,$offset);
    if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
 }
}
public function getBuscarNumCuenta($filter){
 $this->db->select('*');
    $this->db->where('CUENT_NumeroEmpresa',$filter);//->EMPRE_Codigo);
    $this->db->where('CUENT_FlagEstado',"1");
    $query= $this->db->get('cji_cuentasempresas');
    if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
 } 
}


}
?>