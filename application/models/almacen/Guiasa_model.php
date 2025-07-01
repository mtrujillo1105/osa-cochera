<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiasa_Model extends CI_Model{
	protected $_name = "cji_guiasa";
  ##  -> Begin
	private $compania;
	##  -> End
	public function  __construct(){
		parent::__construct();
		$this->load->model('maestros/configuracion_model');
		$this->compania = $this->session->userdata('compania');
	}

	public function listar($number_items = '', $offset = '')
	{
		$where = array("GUIASAC_FlagEstado" => 1);
		$query = $this->db->order_by('GUIASAP_Codigo', 'desc')->where($where)->get('cji_guiasa', $number_items, $offset);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}


	public function buscar_guiasa($filter, $number_items = '', $offset = '')
	{
		$compania = $this->compania;
		$data_confi = $this->companiaconfiguracion_model->obtener($compania);
		$data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);

		$where = '';

		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' and GUIASAC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
		if (isset($filter->cliente) && $filter->cliente != '')
			$where .= ' and cji_cliente.CLIP_Codigo=' . $filter->cliente;
		if (isset($filter->numero) && $filter->numero != '')
			$where .= ' and GUIASAC_Numero=' . $filter->numero;
		if (isset($filter->situacion) && $filter->situacion != '')
			$where .= ' and GUIASAC_FlagEstado=' . $filter->situacion;
		if (isset($filter->cotizacion) && $filter->cotizacion != '')
			$where .= ' and COTIC_Numero=' . $filter->cotizacion;
		if (isset($filter->pedido) && $filter->pedido != '')
			$where .= ' and PEDIC_Numero=' . $filter->pedido;


		$limit = "";
		if ((string)$offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;


		$sql = "
		SELECT 
		GUIASAP_Codigo,
		GUIASAC_Fecha,
		GUIASAC_Numero,
		ALMAC_Descripcion,
		EMPRC_RazonSocial,
		GUIASAC_FlagEstado
		FROM cji_guiasa 
		LEFT JOIN cji_almacen USING(ALMAP_Codigo) 
		LEFT JOIN cji_cliente USING(CLIP_Codigo)
		LEFT JOIN cji_empresa USING(EMPRP_Codigo)
		WHERE 1=1 " . $where . "
		ORDER BY GUIASAP_Codigo DESC " . $limit . "
		";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}


	public function obtener($id)
	{
		$where = array("GUIASAP_Codigo" => $id);
		$query = $this->db->where($where)->get('cji_guiasa');
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function insertar(stdClass $filter = null)
	{
		$datos_configuracion = $this->configuracion_model->obtener_numero_documento($this->compania, '6');
		$numero = $datos_configuracion[0]->CONFIC_Numero + 1;
		$filter->GUIASAC_Numero = $numero;
		$this->db->insert("cji_guiasa", (array)$filter);
		$guiasa_id = $this->db->insert_id();
		if ($guiasa_id != 0) {
			$this->configuracion_model->modificar_configuracion($this->compania, 6, $numero);
		}
		return $guiasa_id;
	}

	public function modificar($id, $filter)
	{
		$this->db->where("GUIASAP_Codigo", $id);
		$this->db->update("cji_guiasa", (array)$filter);
	}

	public function eliminar($id)
	{
		$this->db->delete('cji_guiasa', array('GUIASAP_Codigo' => $id));
	}
}

?>
