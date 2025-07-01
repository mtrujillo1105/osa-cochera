<?php
  class Recepcionproveedor_model extends CI_Model{
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
            $this->db->select('cji_recepcionproveedor.RECEPRO_Codigo,cji_recepcionproveedor.RECEPRO_TipoSolucion,
                cji_recepcionproveedor.RECEPRO_Observacion,
            cji_recepcionproveedor.ENVIPRO_FechaRegistro,cji_garantia.GARAN_Codigo,cji_proveedor.PROVP_Codigo,
            cji_producto.PROD_Nombre,cji_empresa.EMPRC_RazonSocial');
          $this->db->from('cji_recepcionproveedor');
          $this->db->join('cji_garantia','cji_garantia.GARAN_Codigo=cji_recepcionproveedor.GARAN_Codigo');
           $this->db->join('cji_producto','cji_producto.PROD_Codigo=cji_garantia.PROD_Codigo');
          $this->db->join('cji_proveedor','cji_proveedor.PROVP_Codigo =cji_recepcionproveedor.PROVP_Codigo');
           $this->db->join('cji_empresa','cji_empresa.EMPRP_Codigo=cji_proveedor.EMPRP_Codigo');
          //$this->db->like('cji_contrato.CONTR_Descripcion',$descripcion);
          $this->db->where('RECEPRO_FlagEstado',1);
          $this->db->like('RECEPRO_Observacion',$descripcion);
          $this->db->order_by('cji_recepcionproveedor.RECEPRO_FechaRegistro', 'asc');
          $this->db->limit($number_items,$offset);   
           $query = $this->db->get();   
              
              
     
          if($query->num_rows() > 0){
             return $query->result();
          }
      }
      public function obtener_recepcionproveedor($id){
         $this->db->select('cji_recepcionproveedor.RECEPRO_Codigo,cji_recepcionproveedor.RECEPRO_TipoSolucion,
                cji_recepcionproveedor.RECEPRO_Observacion,
            cji_recepcionproveedor.RECEPRO_FechaRegistro,cji_garantia.GARAN_Codigo,cji_proveedor.PROVP_Codigo,
            cji_producto.PROD_Nombre,cji_empresa.EMPRC_RazonSocial');
          $this->db->from('cji_recepcionproveedor');
          $this->db->join('cji_garantia','cji_garantia.GARAN_Codigo=cji_recepcionproveedor.GARAN_Codigo');
           $this->db->join('cji_producto','cji_producto.PROD_Codigo=cji_garantia.PROD_Codigo');
          $this->db->join('cji_proveedor','cji_proveedor.PROVP_Codigo =cji_recepcionproveedor.PROVP_Codigo');
           $this->db->join('cji_empresa','cji_empresa.EMPRP_Codigo=cji_proveedor.EMPRP_Codigo');
          //$this->db->like('cji_contrato.CONTR_Descripcion',$descripcion);
          $this->db->where("RECEPRO_Codigo ",$id);
         
           $query = $this->db->get();  
        
         
          if($query->num_rows() > 0){
            return $query->result();
          }
      }
      public function insertar(stdClass $filter = null,$valores){
          $this->db->insert("cji_recepcionproveedor",(array)$filter);
          
           $titulo='Recepcionado';
          $data = array(
               'GARAN_Estado' => $titulo
              );

          $this->db->where('GARAN_Codigo', $valores);
          $this->db->update('cji_garantia', $data); 
          
          
      }
      public function eliminar_recepcionproveedor($cod){
          $where = array("RECEPRO_Codigo"=>$cod);
          $this->db->delete('cji_recepcionproveedor',$where);
      }
      public function modificar_envioproveedor($proyecto,stdClass $filter=null){
          $this->db->where("ACTI_Codigo",$proyecto);
          $this->db->update("cji_envioproveedor",(array)$filter);
      }
  }
?>