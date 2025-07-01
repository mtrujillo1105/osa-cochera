<?php
  class Entregacliente_model extends CI_Model{
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
          $this->db->like('ENTRECLI_Codigo ',$descripcion);
          $this->db->where('ENTRECLI_FlagEstado',1);
          $this->db->order_by('ENTRECLI_FechaRegistro', 'ASC');
          $query = $this->db->get('cji_entregacliente', $number_items,$offset);
          if($query->num_rows() > 0){
             return $query->result();
          }
      }
      public function obtener_recepcionproveedor($id){
          $where = array("ENTRECLI_Codigo "=>$id);
          $query = $this->db->where($where)->get('cji_entregacliente',1);
          if($query->num_rows() > 0){
            return $query->result();
          }
      }
      public function insertar(stdClass $filter = null,$valores){
          
          
          
          $this->db->insert("cji_entregacliente",(array)$filter);
          
          
          
          
          $titulo='Solucionado';
          $data = array(
               'GARAN_Estado' => $titulo
              );

          $this->db->where('GARAN_Codigo', $valores);
          $this->db->update('cji_garantia', $data); 

          
      }
      public function eliminar_entregacliente($cod){
          $where = array("ENTRECLI_Codigo"=>$cod);
          $this->db->delete('cji_entregacliente',$where);
      }
      public function modificar_envioproveedor($proyecto,stdClass $filter=null){
          $this->db->where("ENTRECLI_Codigo",$proyecto);
          $this->db->update("cji_entregacliente",(array)$filter);
      }
  }
?>