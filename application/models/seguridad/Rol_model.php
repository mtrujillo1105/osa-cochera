<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Rol_model extends CI_Model{

	##  -> Begin
	private $empresa;
	private $compania;
	##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->load->helper('date');
		$this->load->model('seguridad/permiso_model');
		$this->load->model('seguridad/menu_model');
		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
	}
	##  -> End

	##  -> Begin
	public function getRoles($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->nombre) && $filter->nombre != '')
			$where .= " AND ROL_Descripcion LIKE '%$filter->nombre%'";

		$rec = "SELECT * FROM cji_rol WHERE ROL_FlagEstado LIKE '1' AND ROL_Descripcion NOT LIKE '%SISTEMAS%' $where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_rol WHERE ROL_FlagEstado LIKE '1' AND ROL_Descripcion NOT LIKE '%SISTEMAS%' $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_rol WHERE ROL_FlagEstado LIKE '1' AND ROL_Descripcion NOT LIKE '%SISTEMAS%'";

		$records = $this->db->query($rec);
		$recordsFilter = $this->db->query($recF)->row()->registros;
		$recordsTotal = $this->db->query($recT)->row()->registros;

		if ($records->num_rows() > 0){
			$info = array(
										"records" => $records->result(),
										"recordsFilter" => $recordsFilter,
										"recordsTotal" => $recordsTotal
									);
		}
		else{
			$info = array(
										"records" => NULL,
										"recordsFilter" => 0,
										"recordsTotal" => $recordsTotal
									);
		}
		return $info;
	}
	##  -> End

	##  -> Begin
	public function registrar_rol($filter){
		$this->db->insert("cji_rol", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_rol($rol, $filter){
		$this->db->where('ROL_Codigo',$rol);
		return $this->db->update('cji_rol', $filter);
	}
	##  -> End

	#######################
	##### FUNCTIONS OLDS
	#######################

	public function listar_roles( $compartirRol = false ){
		$empresa = $this->empresa;
		$companias = ($compartirRol == false) ? $this->listar_companias_empresa($empresa) : $this->listar_companias_empresa($empresa, true);
		$where = "";

		if ( $companias != NULL ){
			$where .= " AND  (";
			$size = count($companias) - 1;
			foreach ($companias as $i => $value) {
				$where .= ( $i < $size ) ? " COMPP_Codigo = '$value->COMPP_Codigo' OR " : " COMPP_Codigo = '$value->COMPP_Codigo' ";
			}
			$where .= ")";
		}

		$sql = "SELECT * FROM cji_rol WHERE ROL_FlagEstado = '1' AND ROL_Descripcion NOT LIKE '%SISTEMAS%' $where ORDER BY 'ROL_Descripcion'";
		$query = $this->db->query($sql);

		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_companias_empresa($empresa, $compartir = false) {
		$where = ($compartir == false) ? array('COMPC_FlagEstado' => '1', 'EMPRP_Codigo' => $empresa) : array('COMPC_FlagEstado' => '1', 'EMPRP_Codigo <=' => 100);
		$query = $this->db->where($where)->from('cji_compania')->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		else
			return NULL;
	}

	public function obtener_rol($rol){
		$query = $this->db->where('ROL_Codigo',$rol)->get('cji_rol');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_rol_permiso(){
		$where = array('MENU_Codigo_Padre'=>0,'MENU_FlagEstado'=>'1');
		$qu = $this->db->from('cji_menu')
		->where($where)
		->get();
		$rows = $qu->result();
		foreach($rows as $row){
			$where1 = array('MENU_Codigo_Padre'=>$row->MENU_Codigo,'MENU_FlagEstado'=>'1');
			$qur = $this->db->from('cji_menu')
			->where($where1 )
			->get();
			$row->submenus = $qur->result();
		}
		return $rows;
	}

	public function buscar_roles($filter,$number_items='',$offset=''){
		$this->db->where('COMPP_Codigo',$this->compania);      
		$this->db->where_not_in('ROL_Codigo','0');
		if(isset($filter->nombres) && $filter->nombres!="")
			$this->db->like('ROL_Descripcion',$filter->nombres,'both');
		$query = $this->db->get('cji_rol',$number_items,$offset);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
}
?>