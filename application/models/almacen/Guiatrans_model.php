<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiatrans_Model extends CI_Model{
	##  -> Begin
	private $compania;
	private $establec;
	private $usuario;
  ##  -> End
	protected $_name = "cji_guiatrans";

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/configuracion_model');
		$this->load->model('maestros/companiaconfiguracion_model');
		$this->load->model('maestros/companiaconfidocumento_model');
		$this->load->model('almacen/guiatransdetalle_model');
		$this->load->model('almacen/kardex_model');
		$this->compania = $this->session->userdata('compania');
		$this->establec = $this->session->userdata('establec');
		$this->usuario = $this->session->userdata('user');
	}

	public function listar_transferencias_transito( $filter = NULL ){
		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? " ORDER BY $filter->order $filter->dir " : "";

		$sql = "SELECT g.*,
		(SELECT a.ALMAC_Descripcion FROM cji_almacen a WHERE a.ALMAP_Codigo = g.GTRANC_AlmacenOrigen) as ALMAC_DescripcionOri,
		(SELECT e.EESTABC_Descripcion FROM cji_emprestablecimiento e WHERE e.EESTABP_Codigo = (SELECT a.EESTABP_Codigo FROM cji_almacen a WHERE a.ALMAP_Codigo = g.GTRANC_AlmacenOrigen) ) as EESTABC_DescripcionOri,

		ad.ALMAC_Descripcion as ALMAC_DescripcionDes,
		ed.EESTABC_Descripcion as EESTABC_DescripcionDest

		FROM cji_guiatrans g
		INNER JOIN cji_almacen ad ON ad.ALMAP_Codigo = g.GTRANC_AlmacenDestino
		INNER JOIN cji_emprestablecimiento ed ON ed.EESTABP_Codigo = ad.EESTABP_Codigo
		WHERE g.GTRANC_EstadoTrans = 1 AND ed.EESTABP_Codigo = " . $this->establec . $order . $limit;

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return array();
	}

	public function listar_transferencias_pendientes( $compania = true){

		if ($compania == true){
			$this->db->select('cji_guiatrans.GTRANP_Codigo, cji_guiatrans.GTRANC_Fecha,cji_guiatrans.GTRANC_Serie, cji_guiatrans.GTRANC_Numero,cji_guiatrans.GTRANC_AlmacenDestino,cji_usuario.USUA_usuario,cji_emprestablecimiento.EESTABC_Descripcion')
			->join('cji_compania', 'cji_guiatrans.GTRANC_AlmacenDestino=cji_compania.COMPP_Codigo', 'inner')
			->join('cji_emprestablecimiento', 'cji_compania.EESTABP_Codigo=cji_emprestablecimiento.EESTABP_Codigo')
			->join('cji_usuario', 'cji_guiatrans.USUA_Codigo=cji_usuario.USUA_Codigo')
			->where('cji_guiatrans.GTRANC_EstadoTrans', 1)
			->where('cji_guiatrans.COMPP_Codigo', $this->session->userdata('idcompania'))
			->from('cji_guiatrans');
		}
		else{
			$this->db->select('cji_guiatrans.GTRANP_Codigo, cji_guiatrans.GTRANC_Fecha,cji_guiatrans.GTRANC_Serie, cji_guiatrans.GTRANC_Numero,cji_guiatrans.GTRANC_AlmacenDestino,cji_usuario.USUA_usuario,cji_emprestablecimiento.EESTABC_Descripcion')
			->join('cji_compania', 'cji_guiatrans.GTRANC_AlmacenDestino=cji_compania.COMPP_Codigo', 'inner')
			->join('cji_emprestablecimiento', 'cji_compania.EESTABP_Codigo=cji_emprestablecimiento.EESTABP_Codigo')
			->join('cji_usuario', 'cji_guiatrans.USUA_Codigo=cji_usuario.USUA_Codigo')
			->where('cji_guiatrans.GTRANC_EstadoTrans <= 1')
			->from('cji_guiatrans');
		}

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return array();
	}

	public function listar($number_items = '', $offset = '')
	{
		$where = array("a.EESTABP_Codigo" => $this->establec);
		$query = $this->db->order_by('g.GTRANC_FechaRegistro', 'desc')
		->where($where)
		->join('cji_almacen a', 'a.ALMAP_Codigo=g.GTRANC_AlmacenOrigen', 'left')
		->join('cji_almacen a2', 'a2.ALMAP_Codigo=g.GTRANC_AlmacenDestino', 'left')
		->join('cji_emprestablecimiento e', 'e.EESTABP_Codigo=a.EESTABP_Codigo', 'left')
		->join('cji_emprestablecimiento e2', 'e2.EESTABP_Codigo=a2.EESTABP_Codigo', 'left')
		->select('g.*, e.EESTABC_Descripcion EESTABC_DescripcionOri, e2.EESTABC_Descripcion EESTABC_DescripcionDest, a.ALMAC_Descripcion ALMAC_DescripcionOri, a2.ALMAC_Descripcion ALMAC_DescripcionDes, a.COMPP_Codigo COMPP_CodigoOri, a2.COMPP_Codigo COMPP_CodigoDes')
		->get('cji_guiatrans g', $number_items, $offset);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return array();
	}

	public function listar2($filter)
	{
		$this->db->select('g.*, e.EESTABC_Descripcion EESTABC_DescripcionOri, e2.EESTABC_Descripcion EESTABC_DescripcionDest, a.ALMAC_Descripcion ALMAC_DescripcionOri, a2.ALMAC_Descripcion ALMAC_DescripcionDes, a.COMPP_Codigo COMPP_CodigoOri, a2.COMPP_Codigo COMPP_CodigoDes');
		$this->db->join('cji_almacen a', 'a.ALMAP_Codigo=g.GTRANC_AlmacenOrigen', 'left');
		$this->db->join('cji_almacen a2', 'a2.ALMAP_Codigo=g.GTRANC_AlmacenDestino', 'left');
		$this->db->join('cji_emprestablecimiento e', 'e.EESTABP_Codigo=a.EESTABP_Codigo', 'left');
		$this->db->join('cji_emprestablecimiento e2', 'e2.EESTABP_Codigo=a2.EESTABP_Codigo', 'left');
		$this->db->where("a.EESTABP_Codigo", $this->establec);
		if($filter->numero != "" && isset($filter->numero)){
			$this->db->like('g.GTRANC_Numero', $filter->numero);
		}
		if($filter->serie != "" && isset($filter->serie)){
			$this->db->like('g.GTRANC_Serie', $filter->serie);
		}
		if($filter->movimiento == '1'){
			$this->db->where('g.GTRANC_EstadoTrans', '0');
		}
		if($filter->movimiento == '2'){
			$this->db->where('g.GTRANC_EstadoTrans', '1');
		}
		if($filter->movimiento == '4'){
			$this->db->where('g.GTRANC_EstadoTrans', '3');
		}
		if($filter->movimiento == '5'){
			$this->db->where('g.GTRANC_EstadoTrans', '2');
		}
		if($filter->fecha_ini != "" && isset($filter->fecha_ini) && $filter->fecha_fin == "" || !isset($filter->fecha_fin)){
			$this->db->where('g.GTRANC_Fecha >=', $filter->fecha_ini);
			$this->db->where('g.GTRANC_Fecha <', '2050-12-12');
		}else if($filter->fecha_fin != "" && isset($filter->fecha_fin) && $filter->fecha_ini == "" || !isset($filter->fecha_ini)){
			$this->db->where('g.GTRANC_Fecha <=', $filter->fecha_fin);
			$this->db->where('g.GTRANC_Fecha >', '2010-12-12');
		}else{
			$this->db->where('g.GTRANC_Fecha >=', $filter->fecha_ini);
			$this->db->where('g.GTRANC_Fecha <=', $filter->fecha_fin);
		}
		$this->db->order_by('g.GTRANC_FechaRegistro', 'desc');
		$query = $this->db->get('cji_guiatrans g');
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return array();
	}

	public function listar_recibidos($number_items = '', $offset = '')
	{
		$compania = $this->compania;
		$where = array("a2.EESTABP_Codigo" => $this->establec);
		$query = $this->db->order_by('g.GTRANC_FechaRegistro', 'DESC')
		->where($where)
		->join('cji_almacen a', 'a.ALMAP_Codigo=g.GTRANC_AlmacenOrigen')
		->join('cji_almacen a2', 'a2.ALMAP_Codigo=g.GTRANC_AlmacenDestino')
		->join('cji_emprestablecimiento e', 'e.EESTABP_Codigo=a.EESTABP_Codigo', 'left')
		->join('cji_emprestablecimiento e2', 'e2.EESTABP_Codigo=a2.EESTABP_Codigo', 'left')
		->select('g.*, e.EESTABC_Descripcion EESTABC_DescripcionOri, e2.EESTABC_Descripcion EESTABC_DescripcionDest, a.ALMAC_Descripcion ALMAC_DescripcionOri, a2.ALMAC_Descripcion ALMAC_DescripcionDes, a.COMPP_Codigo COMPP_CodigoOri, a2.COMPP_Codigo COMPP_CodigoDes')
		->get('cji_guiatrans g', $number_items, $offset);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return array();
	}

	public function listar_recibidos2($filter)
	{
		$this->db->select('g.*, e.EESTABC_Descripcion EESTABC_DescripcionOri, e2.EESTABC_Descripcion EESTABC_DescripcionDest, a.ALMAC_Descripcion ALMAC_DescripcionOri, a2.ALMAC_Descripcion ALMAC_DescripcionDes, a.COMPP_Codigo COMPP_CodigoOri, a2.COMPP_Codigo COMPP_CodigoDes');
		$this->db->join('cji_almacen a', 'a.ALMAP_Codigo=g.GTRANC_AlmacenOrigen');
		$this->db->join('cji_almacen a2', 'a2.ALMAP_Codigo=g.GTRANC_AlmacenDestino');
		$this->db->join('cji_emprestablecimiento e', 'e.EESTABP_Codigo=a.EESTABP_Codigo', 'left');
		$this->db->join('cji_emprestablecimiento e2', 'e2.EESTABP_Codigo=a2.EESTABP_Codigo', 'left');
		$this->db->where("a2.EESTABP_Codigo", $this->establec);
		if($filter->numero != "" && isset($filter->numero)){
			$this->db->like('g.GTRANC_Numero', $filter->numero);
		}
		if($filter->serie != "" && isset($filter->serie)){
			$this->db->like('g.GTRANC_Serie', $filter->serie);
		}
		if($filter->movimiento == '1'){
			$this->db->where('g.GTRANC_EstadoTrans', '0');
		}
		if($filter->movimiento == '3'){
			$this->db->where('g.GTRANC_EstadoTrans', '1');
		}
		if($filter->movimiento == '4'){
			$this->db->where('g.GTRANC_EstadoTrans', '3');
		}
		if($filter->movimiento == '5'){
			$this->db->where('g.GTRANC_EstadoTrans', '2');
		}
		if($filter->fecha_ini != "" && isset($filter->fecha_ini) && $filter->fecha_fin == "" || !isset($filter->fecha_fin)){
			$this->db->where('g.GTRANC_Fecha >=', $filter->fecha_ini);
			$this->db->where('g.GTRANC_Fecha <', '2020-12-12');
		}else if($filter->fecha_fin != "" && isset($filter->fecha_fin) && $filter->fecha_ini == "" || !isset($filter->fecha_ini)){
			$this->db->where('g.GTRANC_Fecha <=', $filter->fecha_fin);
			$this->db->where('g.GTRANC_Fecha >', '2010-12-12');
		}else{
			$this->db->where('g.GTRANC_Fecha >=', $filter->fecha_ini);
			$this->db->where('g.GTRANC_Fecha <=', $filter->fecha_fin);
		}
		$this->db->order_by('g.GTRANC_FechaRegistro', 'DESC');
		$query = $this->db->get('cji_guiatrans g');
		if ($query->num_rows() > 0) {
			return $query->result();
		} else
		return array();
	}

	public function obtener($id)
	{
		$where = array("GTRANP_Codigo" => $id);
		$query = $this->db->where($where)->get('cji_guiatrans', 1);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function obtener2($id)
	{
		$query = $this->db->select('*')
		->from('cji_guiatrans')
		->where('GTRANP_Codigo', $id)
		->get();
		if ($query->num_rows() >= 0) {
			return $query->row();
		} else {
			return array();
		}
	}

	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_guiatrans", (array)$filter);
		$guiatrans_id = $this->db->insert_id();
		return $guiatrans_id;
	}

	public function obtener_ultimo_numero($serie = '')
	{
		$compania = $this->compania;
		$where = array("COMPP_Codigo" => $compania);

		if ($serie != '')
			$where['GTRANC_Serie'] = $serie;
		else
			$where['GTRANC_Serie'] = NULL;

		$query = $this->db->order_by('GTRANC_Serie', 'desc')->order_by('GTRANC_Numero', 'desc')->where($where)->get('cji_guiatrans', 1);
		$numero = 1;
		if ($query->num_rows() > 0) {
			$data = $query->result();
			$numero = (int)$data[0]->GTRANC_Numero + 1;
		}
		return $numero;
	}

	public function buscar_x_guiain($guiain)
	{
		$where = array("GUIAINP_Codigo" => $guiain, 'GTRANC_FlagEstado' => '1');
		$query = $this->db->where($where)->get('cji_guiatrans g');
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function buscar_x_guiasa($guiasa)
	{
		$where = array("GUIASAP_Codigo" => $guiasa, 'GTRANC_FlagEstado' => '1');
		$query = $this->db->where($where)->get('cji_guiatrans');
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function actualiza_usuatrans($userTrans, $estadoTrans, $codigo)
	{
		$update = array('GTRANC_CodigoUsuario' => $userTrans,
			'GTRANC_EstadoTrans' => $estadoTrans);
		$this->db->where('GTRANP_Codigo', $codigo);
		$valor = $this->db->update('cji_guiatrans', $update);
		return $valor;
	}

	public function actualiza_receptrans($perTrans, $estadoTrans, $codigo)
	{
		$update = array('GTRANC_PersonalRecep' => $perTrans,
			'GTRANC_EstadoTrans' => $estadoTrans);
		$this->db->where(array('GTRANP_Codigo' => $codigo));
		$valor = $this->db->update('cji_guiatrans', $update);
		return $valor;
	}

	public function actualiza_guia($codigo, $filter)
	{
		$this->db->where('GTRANP_Codigo', $codigo);
		$this->db->update('cji_guiatrans', $filter);
	}

	public function actualiza_almacen_destino($guiatrasn, $filter)
	{
		$data = array(
			'GTRANC_Serie' => $filter->GTRANC_Serie,
			'GTRANC_Numero' => $filter->GTRANC_Numero,
			'GTRANC_CodigoUsuario' => $filter->GTRANC_CodigoUsuario,
			'GTRANC_AlmacenOrigen' => $filter->GTRANC_AlmacenOrigen,
			'GTRANC_AlmacenDestino' => $filter->GTRANC_AlmacenDestino,
			'GTRANC_Fecha' => $filter->GTRANC_Fecha,
			'GTRANC_Observacion' => $filter->GTRANC_Observacion,
			'GTRANC_Placa' => $filter->GTRANC_Placa,
			'GTRANC_Licencia' => $filter->GTRANC_Licencia,
			'GTRANC_Chofer' => $filter->GTRANC_Chofer,
			'EMPRP_Codigo' => $filter->EMPRP_Codigo,
			'COMPP_Codigo' => $filter->COMPP_Codigo,
			'USUA_Codigo' => $filter->USUA_Codigo,
			'GTRANC_FlagEstado' => $filter->GTRANC_FlagEstado
		);
		$this->db->where('GTRANP_Codigo', $guiatrasn);
		$valor = $this->db->update('cji_guiatrans', $data);
		if ($valor) {
			return $guiatrasn;
		} else {
			return -1;
		}
	}

	public function actualiza_guia2($codigo, $filter)
	{
		$update = array('GUIAINP_Codigo' => $filter);
		$this->db->where('GTRANP_Codigo', $codigo);
		$this->db->update('cji_guiatrans', $update);
	}

	public function actualizar_guia_salida($codigo, $filter)
	{
		$update = array('GUIASAP_Codigo' => $filter);
		$this->db->where('GTRANP_Codigo', $codigo);
		$valor = $this->db->update('cji_guiatrans', $update);
		return $valor;
	}

	public function eliminar($codigo){
		$sql = "UPDATE `cji_guiatrans` SET `GTRANC_FlagEstado` = '0' WHERE `cji_guiatrans`.`GTRANP_Codigo` = '$codigo'";
		$this->db->query($sql);
	}

}

?>