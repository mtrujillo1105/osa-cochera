<?php
  class Impactousuario_model extends CI_Model{
      function __construct(){
          parent::__construct();
          $this->load->database();
          $this->load->helper('date');
          $this->load->model('seguridad/permiso_model');
          $this->load->model('seguridad/menu_model');
          $this->somevar ['compania'] = $this->session->userdata('compania');
          $this->somevar ['usuario']  = $this->session->userdata('usuario');
          $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
      }
      public function listar($descripcion='',$number_items='',$offset=''){
          if($descripcion!='')
          $this->db->like('usuario ',$descripcion);
          $this->db->where('flagestado',1);
          $this->db->order_by('fecharegistro', 'ASC');
          $query = $this->db->get('impactousuario', $number_items,$offset);
          if($query->num_rows() > 0){
             return $query->result();
          }
      }
      
      public function obtener_recepcionproveedor($id){
          $where = array("id"=>$id);
          $query = $this->db->where($where)->get('impactousuario',1);
          if($query->num_rows() > 0){
            return $query->result();
          }
      }
      public function insertar(stdClass $filter = NULL){
          
          
          $this->db->insert("impactousuario",(array)$filter);
          
        
      }
      public function eliminar_usuario($cod){
          
          $where = array("id"=>$cod);
          $this->db->delete('impactousuario',$where);
      }
    public function modificar($usuario,$filter)
    {
        $this->db->where("usuario",$usuario);
        $this->db->update("impactousuario",(array)$filter);
    }
    public function obtener($id)
    {
      $query = $this->db->where("id",$id)->get("impactousuario");
     if($query->num_rows() > 0){
         foreach($query->result() as $fila){
              $data[] = $fila;
           }
          return $data;
      }
        
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $this->db->where("flagestado",1);
        if(isset($filter->usuario) && $filter->usuario!='')
            $this->db->like('usuario',$filter->usuario,'right');
     if(isset($filter->usuario) && $filter->usuario!='')
            $this->db->like('usuario',$filter->usuario,'right');
        $query = $this->db->get('impactousuario',$number_items,$offset);
        if($query->num_rows() > 0){
            return $query->result();
        }
    }
    
    public function coincidencias($usuario){
        $coincidencia = 0;
        $query = $this->db->where("usuario",$usuario)->get('impactousuario');
        if($query->num_rows() > 0)
            $coincidencia = 1;
        return $coincidencia;
    }
  }
?>