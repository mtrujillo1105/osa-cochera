<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Emprestablecimiento_model extends CI_Model {

	##  -> Begin - El array somevar es reemplazado por atributos
	private $empresa;
	private $compania;
	private $url;
	private $usuario;
  ##  -> End

	##  -> Begin
	public function __construct() {
		parent::__construct();
		$this->load->model('maestros/ubigeo_model');
		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
		$this->usuario = $this->session->userdata('usuario');
	}
	##  -> End

	##  -> Begin
	public function getEstablecimientos($filter = NULL){
		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->empresa) && $filter->empresa != '')
			$where .= " AND e.EMPRP_Codigo LIKE '%$filter->empresa%'";

		$rec = "SELECT e.*, te.TESTC_Descripcion, CONCAT_WS(' ', u.UBIGC_Descripcion, ' - ', u.UBIGC_DescripcionProv, ' - ', u.UBIGC_DescripcionDpto) as ubigeo_descripcion
							FROM cji_emprestablecimiento e
							LEFT JOIN cji_tipoestablecimiento te ON te.TESTP_Codigo = e.TESTP_Codigo
							LEFT JOIN cji_ubigeo u ON u.UBIGP_Codigo = e.UBIGP_Codigo
							WHERE e.EESTABC_FlagEstado LIKE '1'
							$where $order $limit
						";

		$recF = "SELECT COUNT(*) as registros
							FROM cji_emprestablecimiento e
							LEFT JOIN cji_tipoestablecimiento te ON te.TESTP_Codigo = e.TESTP_Codigo
							LEFT JOIN cji_ubigeo u ON u.UBIGP_Codigo = e.UBIGP_Codigo
							WHERE e.EESTABC_FlagEstado LIKE '1'
							$where
						";

		$recT = "SELECT COUNT(*) as registros
							FROM cji_emprestablecimiento e
							LEFT JOIN cji_tipoestablecimiento te ON te.TESTP_Codigo = e.TESTP_Codigo
							LEFT JOIN cji_ubigeo u ON u.UBIGP_Codigo = e.UBIGP_Codigo
							WHERE e.EESTABC_FlagEstado LIKE '1'
						";

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
	public function getEstablecimiento($establecimiento){

		$sql = "SELECT e.*,emp.* 
                        FROM cji_emprestablecimiento e 
                        inner join cji_empresa emp on (emp.EMPRP_Codigo = e.EMPRP_Codigo)
                        WHERE e.EESTABP_Codigo = $establecimiento";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function insertar_establecimiento($filter){
		$this->db->insert("cji_emprestablecimiento", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_establecimiento_principal($filter){
		$this->db->where('EMPRP_Codigo',$filter->EMPRP_Codigo);
		$this->db->where('EESTABC_FlagTipo','1');
		$this->db->where('EESTABC_FlagEstado','1');
		return $this->db->update('cji_emprestablecimiento', $filter);
	}
	##  -> End

	##  -> Begin
	public function actualizar_establecimiento($establecimiento, $filter){
		$this->db->where('EESTABP_Codigo',$establecimiento);
		return $this->db->update('cji_emprestablecimiento', $filter);
	}
	##  -> End

  ## FUNCTIONS OLDS

	public function listar($empresa, $tipo = '',$comp_select=null) {
		$this->db->where('cji_emprestablecimiento.EMPRP_Codigo', $empresa)->where('EESTABC_FlagEstado', '1');
		$this->db->where_in('cji_compania.COMPP_Codigo',$comp_select);
		if ($tipo !== '')
			$this->db->where('cji_emprestablecimiento.EESTABC_FlagTipo', $tipo);
		$this->db->join('cji_compania', 'cji_compania.EESTABP_Codigo = cji_emprestablecimiento.EESTABP_Codigo', 'left');

		$this->db->where_not_in('cji_emprestablecimiento.EESTABP_Codigo', '0')        
		->order_by('EESTABC_Descripcion')->select('cji_emprestablecimiento.*,cji_compania.COMPP_Codigo');
		$query = $this->db->get('cji_emprestablecimiento');
		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $key => $reg) {
				$result[$key]->distrito = "";
				$result[$key]->provincia = "";
				$result[$key]->departamento = "";
				if ($reg->UBIGP_Codigo != '' && $reg->UBIGP_Codigo != '000000') {
					$datos_ubigeo_dist = $this->ubigeo_model->obtener_ubigeo_dist($reg->UBIGP_Codigo);
					$datos_ubigeo_prov = $this->ubigeo_model->obtener_ubigeo_prov($reg->UBIGP_Codigo);
					$datos_ubigeo_dep = $this->ubigeo_model->obtener_ubigeo_dpto($reg->UBIGP_Codigo);
					if (count($datos_ubigeo_dist) > 0)
						$result[$key]->distrito = $datos_ubigeo_dist[0]->UBIGC_Descripcion;
					if (count($datos_ubigeo_prov) > 0)
						$result[$key]->provincia = $datos_ubigeo_prov[0]->UBIGC_Descripcion;
					if (count($datos_ubigeo_dep) > 0)
						$result[$key]->departamento = $datos_ubigeo_dep[0]->UBIGC_Descripcion;
				}
			}
			return $result;
		}else
		return array();
	}

	public function obtener($id) {
		$where = array("EESTABP_Codigo" => $id, "EESTABC_FlagEstado" => "1");
		$query = $this->db->where($where)->get('cji_emprestablecimiento');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$fila->distrito = "";
				$fila->provincia = "";
				$fila->departamento = "";
				if ($fila->UBIGP_Codigo != '' && $fila->UBIGP_Codigo != '000000') {
					$datos_ubigeo_dist = $this->ubigeo_model->obtener_ubigeo_dist($fila->UBIGP_Codigo);
					$datos_ubigeo_prov = $this->ubigeo_model->obtener_ubigeo_prov($fila->UBIGP_Codigo);
					$datos_ubigeo_dep = $this->ubigeo_model->obtener_ubigeo_dpto($fila->UBIGP_Codigo);
					if (count($datos_ubigeo_dist) > 0)
						$fila->distrito = $datos_ubigeo_dist[0]->UBIGC_Descripcion;
					if (count($datos_ubigeo_prov) > 0)
						$fila->provincia = $datos_ubigeo_prov[0]->UBIGC_Descripcion;
					if (count($datos_ubigeo_dep) > 0)
						$fila->departamento = $datos_ubigeo_dep[0]->UBIGC_Descripcion;
				}
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar(stdClass $filter = null) {
		$this->db->insert("cji_emprestablecimiento", (array) $filter);
	}

	public function modificar($id, $filter) {
		$this->db->where("EESTABP_Codigo", $id);
		$this->db->update("cji_emprestablecimiento", (array) $filter);
	}

	public function eliminar($id) {
		$this->db->delete('cji_emprestablecimiento', array('EESTABP_Codigo' => $id));
	}

	public function eliminarlog_establecimiento($id) {
        //$this->db->delete('cji_emprestablecimiento',array('EESTABP_Codigo' => $id));
		$data = array('EESTABC_FlagEstado' => 0);
		$this->db->where('EESTABP_Codigo', $id);
		$this->db->update('cji_emprestablecimiento', $data);
	}

}

?>