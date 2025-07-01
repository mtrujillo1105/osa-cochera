<?php
class Tipoproveedor_model extends CI_Model{
    var $somevar;
	function __construct(){
            parent::__construct();
            $this->load->database();
            $this->somevar ['compania'] = $this->session->userdata('compania');
	}
	function listar_familias($codanterior='0'){
         $compania = $this->somevar['compania'];
		$where = array('FAMI_FlagEstado' => '1','FAMI_Codigo2' =>$codanterior,'COMPP_Codigo'=>$compania);
		$query = $this->db->order_by('FAMI_Descripcion')->where_not_in('FAMI_Codigo','0')->where($where)->get('cji_tipoproveedor');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
    function buscar_familias($codanterior, $filter){
                $compania = $this->somevar['compania'];
		$where = array('FAMI_FlagEstado' => '1','FAMI_Codigo2' =>$codanterior,'COMPP_Codigo'=>$compania);
                
		$this->db->order_by('FAMI_Descripcion')->where_not_in('FAMI_Codigo','0')->where($where);
                
                if(isset($filter->codigo) && $filter->codigo!="")
                    $this->db->where('FAMI_CodigoInterno', $filter->codigo);
                if(isset($filter->nombre) && $filter->nombre!="")
                    $this->db->like('FAMI_Descripcion', $filter->nombre, 'both');
                
                $query=$this->db->get('cji_tipoproveedor');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}		
	}
	function obtener_familia($familia){
		$query = $this->db->where('FAMI_Codigo',$familia)->get('cji_tipoproveedor');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}			
	}
	function obtener_familia_max($codanterior='0'){
		$where = array('FAMI_FlagEstado' => '1','FAMI_Codigo2' => $codanterior);
		$this->db->select_max('FAMI_CodigoInterno');
		$query = $this->db->where($where)->get('cji_tipoproveedor');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}		
	}	
	function insertar_familia($descripcion,$codrelacion,$codigointerno, $codigousuario=''){
         $compania = $this->somevar['compania'];
		$data = array(
                             "FAMI_Descripcion"       => strtoupper($descripcion),
                             "FAMI_Codigo2"              => $codrelacion,
                             "FAMI_CodigoInterno" => $codigointerno,
                             "FAMI_CodigoUsuario" => strtoupper($codigousuario),
                             "COMPP_Codigo" => $compania
			     );
		$this->db->insert("cji_tipoproveedor",$data);		
	}
	function modificar_familia($familia,$descripcion, $codigousuario){
		$data = array(
                             "FAMI_Descripcion"       => strtoupper($descripcion),
                             "FAMI_CodigoUsuario"     => strtoupper($codigousuario)
                             );
		$this->db->where('FAMI_Codigo',$familia);
		$this->db->update("cji_tipoproveedor",$data);
	}
	function modificar_familia_numeracion($familia,$numero){
		$data = array("FAMI_Numeracion" => $numero);
		$this->db->where('FAMI_Codigo',$familia);
		$this->db->update("cji_tipoproveedor",$data);	
	}
	function eliminar_familia($familia){
		$data  = array("FAMI_FlagEstado" => '0');
		$where = array("FAMI_Codigo"     => $familia);
		$this->db->where($where);
		$this->db->update('cji_tipoproveedor',$data);		
	}
	function busqueda_familia(){
	
	}
}
?>