<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Menu_model extends CI_Model{

	##  -> Begin
	public function __construct(){
		parent::__construct();
	}
	##  -> End

	##  -> Begin
	public function getMenus($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->modulo) && $filter->modulo != '')
			$where .= " AND m.MENU_Codigo_Padre = $filter->modulo";

		if (isset($filter->menu) && $filter->menu != '')
			$where .= " AND m.MENU_Titulo LIKE '%$filter->menu%'";
		
			## Dev:  -> More Filter
		if (isset($filter->menuurl) && $filter->menuurl != '')
			$where .= " AND m.MENU_Url LIKE '%$filter->menuurl%'";			

		if (isset($filter->menuaccesor) && $filter->menuaccesor != '')
			$where .= " AND m.MENU_AccesoRapido = '$filter->menuaccesor'";					
		## Dev:  -> More Filter

		$rec = "SELECT m.*,
								CASE m.MENU_AccesoRapido
									WHEN '0' THEN 'NO'
									WHEN '1' THEN 'SI'
									ELSE '---'
								END as acceso,
								CASE m.MENU_FlagEstado
									WHEN 0 THEN 'DESHABILITADO'
									WHEN 1 THEN 'ACTIVO'
									ELSE '---'
								END as estado,
								(SELECT sm.MENU_Titulo FROM cji_menu sm WHERE sm.MENU_Codigo = m.MENU_Codigo_Padre) as modulo
							FROM cji_menu m
							WHERE m.MENU_FlagEstado IS NOT NULL $where $order $limit
						";

		$recF = "SELECT COUNT(*) as registros FROM cji_menu m WHERE m.MENU_FlagEstado IS NOT NULL $where";

		$recT = "SELECT COUNT(*) as registros FROM cji_menu m WHERE m.MENU_FlagEstado IS NOT NULL";

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
	public function getMenu($codigo) {

		$sql = "SELECT m.*,
									CASE m.MENU_AccesoRapido
										WHEN '0' THEN 'NO'
										WHEN '1' THEN 'SI'
										ELSE '---'
									END as acceso,
									CASE m.MENU_FlagEstado
										WHEN 0 THEN 'DESHABILITADO'
										WHEN 1 THEN 'HABILITADO'
										ELSE '---'
									END as estado,
									(SELECT sm.MENU_Titulo FROM cji_menu sm WHERE sm.MENU_Codigo = m.MENU_Codigo_Padre) as modulo
							FROM cji_menu m
							WHERE m.MENU_Codigo = $codigo
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return array();
	}
	##  -> End

	##  -> Begin
	public function getModulos() {

		$sql = "SELECT m.*,
								CASE m.MENU_AccesoRapido
									WHEN 0 THEN 'INACTIVO'
									WHEN 1 THEN 'ACTIVO'
									ELSE '---'
								END as acceso,
								CASE m.MENU_FlagEstado
									WHEN 0 THEN 'DESHABILITADO'
									WHEN 1 THEN 'HABILITADO'
									ELSE '---'
								END as estado
							FROM cji_menu m
							WHERE m.MENU_Codigo_Padre = 0;
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}
	##  -> End

	##  -> Begin
	public function insertar($filter){
		$this->db->insert("cji_menu", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar($menu, $filter){
		$this->db->where('MENU_Codigo',$menu);
		return $this->db->update('cji_menu', $filter);
	}
	##  -> End

  ## Functions olds
	public function obtener_datosMenu($menu){
		$query = $this->db->where('MENU_Codigo',$menu)->get('cji_menu');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}			
	}

	public function obtener_x_url($url){
		$query = $this->db->where('MENU_Url',$url)->get('cji_menu');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}
	}
}
?>