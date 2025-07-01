<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiarem_Model extends CI_Model{

	protected $_name = "cji_guiarem";
  ##  -> Begin
	private $compania;
	private $usuario;
  ##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->model('maestros/configuracion_model');
		$this->load->model('almacen/guiaremdetalle_model');
		$this->load->model('maestros/companiaconfiguracion_model');
		$this->load->model('maestros/companiaconfidocumento_model');
		$this->compania = $this->session->userdata('compania');
		$this->usuario = $this->session->userdata('user');
	}

	public function listar($number_items = '', $offset = '')
	{
		$compania = $this->compania;
		$where = array("COMPP_Codigo" => $compania);
		$query = $this->db->order_by('GUIAREMC_Serie', 'desc')->order_by('GUIAREMC_Numero', 'desc')->where($where)->get('cji_guiarem', $number_items, $offset);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function listar_ocompra($ocompra)
	{
		$where = array("OCOMP_Codigo" => $ocompra, "GUIAREMC_FlagEstado" => "1");
		$query = $this->db->where($where)->get('cji_guiarem');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function buscar_totalFilas($tipo_oper, $filter = NULL, $number_items = '', $offset = '')
	{
		$compania = $this->compania;
		$data_confi = $this->companiaconfiguracion_model->obtener($compania);
		$data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 10);

		$where = '';
		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' and g.GUIAREMC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
		switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
			case '1':
			if (isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '2':
			if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Numero="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '3':
			if (isset($filter->codigo_usuario) && $filter->codigo_usuario != '')
				$where .= ' and g.GUIAREMC_Serie="' . $filter->codigo_usuario . '"';
			break;
		}
		if ($tipo_oper != 'C') {
			if (isset($filter->cliente) && $filter->cliente != '')
				$where .= ' and g.CLIP_Codigo=' . $filter->cliente;
		} else {
			if (isset($filter->proveedor) && $filter->proveedor != '')
				$where .= ' and g.PROVP_Codigo=' . $filter->proveedor;
		}

		if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
			$where .= ' and g.GUIAREMC_Serie="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
		if (isset($filter->producto) && $filter->producto != '')
			$where .= ' and gd.PRODCTOP_Codigo=' . $filter->producto;
		$limit = "";
		if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
			$where .= ' and e.EMPRC_Ruc=' . $filter->ruc_proveedor;


		if ((string)$offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;
		$sql = "SELECT COUNT(g.GUIAREMP_Codigo) as total FROM cji_guiarem g
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado != 9
		AND g.COMPP_Codigo =" . $compania . " " . $where . "

		GROUP BY g.GUIAREMP_Codigo
		ORDER BY " . ($tipo_oper == 'V' ? 'g.GUIAREMC_FechaRegistro DESC' : 'g.GUIAREMC_FechaRegistro DESC') . " " . $limit;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row()->total;
		} else {
			return 0;
		}
	}
	public function countFilas($tipo_oper, $filter = NULL, $number_items = '', $offset = ''){
		if($tipo_oper=='V')
		{
			$sql = "SELECT COUNT(*) as cantidad  FROM cji_guiarem g
			where GUIAREMC_TipoOperacion='V' ";

			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->row()->cantidad;
			} else {
				return 0;
			}
		}
		if($tipo_oper=='C')
		{
			$sql = "SELECT COUNT(*) as cantidad  FROM cji_guiarem g
			where GUIAREMC_TipoOperacion='C' ";

			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				return $query->row()->cantidad;
			} else {
				return 0;
			}
		}


	}


	public function buscar($tipo_oper, $filter = NULL, $number_items = '', $offset = ''){
		$compania = $this->compania;
		$data_confi = $this->companiaconfiguracion_model->obtener($compania);
		$data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 10);

		$where = '';
		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' and g.GUIAREMC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
		switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
			case '1':
			if (isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '2':
			if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Serie="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '3':
			if (isset($filter->codigo_usuario) && $filter->codigo_usuario != '')
				$where .= ' and g.GUIAREMC_CodigoUsuario="' . $filter->codigo_usuario . '"';
			break;
		}
		if ($tipo_oper != 'C') {
			if (isset($filter->cliente) && $filter->cliente != '')
				$where .= ' and g.CLIP_Codigo=' . $filter->cliente;
		} else {
			if (isset($filter->proveedor) && $filter->proveedor != '')
				$where .= ' and g.PROVP_Codigo=' . $filter->proveedor;
		}

		if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
			$where .= ' and g.GUIAREMC_Serie="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
		if (isset($filter->producto) && $filter->producto != '')
			$where .= ' and gd.PRODCTOP_Codigo=' . $filter->producto;
		$limit = "";
		if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
			$where .= ' and e.EMPRC_Ruc=' . $filter->ruc_proveedor;


		if ((string)$offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;
		$sql = "SELECT g.GUIAREMC_Fecha,
		g.GUIAREMC_FechaTraslado,
		g.GUIAREMP_Codigo,
		g.GUIAREMC_Serie,
		g.GUIAREMC_Numero,
		g.GUIAREMC_CodigoUsuario,
		g.TIPOMOVP_Codigo,
		al.ALMAC_Descripcion,
		g.OCOMP_Codigo,
		g.GUIAREMC_Numero,
		g.GUIAREMC_NumeroRef,
		g.DOCUP_Codigo,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
		m.MONED_Simbolo,
		g.GUIAREMC_total,
		g.GUIAREMC_FlagEstado,
		g.GUIAREMC_TipoGuia
		FROM cji_guiarem g
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado != 9
		AND g.COMPP_Codigo =" . $compania . " " . $where . "

		GROUP BY g.GUIAREMP_Codigo
		ORDER BY " . ($tipo_oper == 'V' ? 'g.GUIAREMC_FechaRegistro DESC' : 'g.GUIAREMC_FechaRegistro DESC') . " " . $limit;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}

	public function getSeriesEmitidas($oper, $doc, $compania){
		$sql = "SELECT GUIAREMC_Serie,
		(SELECT con.CONFIC_Serie
		FROM cji_configuracion con
		WHERE con.COMPP_Codigo = $compania
		AND con.DOCUP_Codigo IN(SELECT DOCUP_Codigo FROM cji_documento d WHERE d.DOCUC_Inicial LIKE '$doc')
		LIMIT 1
		) serie_actual
		FROM cji_guiarem
		WHERE GUIAREMC_TipoOperacion LIKE '$oper'
		AND COMPP_Codigo = $compania
		GROUP BY GUIAREMC_Serie
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function getGuiaRemision($filter = NULL){

		$compania = $this->compania;

		$where = '';


		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";


		$tipo_oper = $filter->tipo_oper;
		$compania = $this->compania;

		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' AND g.GUIAREMC_Fecha BETWEEN "' .$filter->fechai. '" AND "' .$filter->fechaf. '"';

		if ($tipo_oper == 'V') {
			if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
				$where .= ' AND (EMPRC_RazonSocial LIKE "%' . $filter->nombre_cliente.'%"';
				$where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_cliente.'%"';
				$where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente.'%"';
				$where .= ' OR CLIC_CodigoUsuario LIKE "%' . $filter->nombre_cliente . '%" )';
			}
			if (isset($filter->ruc_cliente) && $filter->ruc_cliente != '') {
				$where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_cliente.'%"';
				$where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_cliente.'%"';
			}
		}
		else {
			if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '') {
				$where .= ' and EMPRC_RazonSocial LIKE "%' . $filter->nombre_proveedor.'%"';
				$where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_proveedor.'%"';
				$where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_proveedor.'%"';
			}
			if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '') {
				$where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_proveedor.'%"';
				$where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_proveedor.'%"';
			}
		}

		if (isset($filter->serie) && $filter->serie != '')
			$where .= ' and g.GUIAREMC_Serie LIKE "' . $filter->serie . '"';

		if (isset($filter->numero) && $filter->numero != '')
			$where .= ' and g.GUIAREMC_Numero="' . $filter->numero . '"';

		if (isset($filter->producto) && $filter->producto != '')
			$where .= ' and gd.PRODCTOP_Codigo=' . $filter->producto;

		if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
			$where .= ' and e.EMPRC_Ruc=' . $filter->ruc_proveedor;

		if ( isset($filter->count) && $filter->count == true ){
			$cols = "COUNT(DISTINCT g.GUIAREMP_Codigo) as registros";
			$group_by = "";
			$limit = "";
		}
		else{
			$cols = "g.GUIAREMC_FechaRegistro, DATE(g.GUIAREMC_FechaRegistro) fechaReg, g.GUIAREMC_Fecha, g.GUIAREMC_FechaTraslado, g.GUIAREMP_Codigo, g.GUIAREMC_Serie, g.GUIAREMC_Numero, g.GUIAREMC_CodigoUsuario, g.TIPOMOVP_Codigo, al.ALMAC_Descripcion, g.OCOMP_Codigo, g.GUIAREMC_Numero, g.GUIAREMC_NumeroRef, g.DOCUP_Codigo, g.GUIAREMC_total, g.GUIAREMC_FlagEstado, g.GUIAREMC_TipoGuia,
			(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
			(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre, m.MONED_Simbolo";
			$group_by = "GROUP BY g.GUIAREMP_Codigo";
		}

		$sql = "SELECT $cols
		FROM cji_guiarem g
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado != 9
		AND g.COMPP_Codigo =" . $compania . " " . $where . "
		$group_by
		$order
		$limit
		";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			if ( isset($filter->count) && $filter->count == true )
				return $query->row();
			else
				return $query->result();
		}
		else
			return NULL;
	}

	public function consultar_respuestaGuia($codigo) {
		$compania = $this->compania;

		$sql = "SELECT rs.* FROM cji_respuestasunat rs WHERE respuestas_codigo = (SELECT MAX(rss.respuestas_codigo) FROM cji_respuestasunat rss WHERE rss.CPP_codigo = '$codigo'  AND rss.respuestas_tipoDocumento IN(5,7) AND rss.respuestas_enlace IS NOT NULL)";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data[0];
		}
		else
			return NULL;
	}

	public function buscar_no_asociados($tipo_oper = 'V', $filter = NULL, $number_items = '', $offset = ''){
		$compania = $this->compania;
		$where = '';
		if (isset($filter->cliente) && $filter->cliente != '')
			$where .= ' and g.CLIP_Codigo=' . $filter->cliente;
		else if (isset($filter->proveedor) && $filter->proveedor != '')
			$where .= ' and g.PROVP_Codigo=' . $filter->proveedor;

		if (isset($filter->tipoMoneda) && $filter->tipoMoneda != '')
			$where .= ' and g.MONED_Codigo=' . $filter->tipoMoneda;

		if (isset($filter->codigoAlmacen) && $filter->codigoAlmacen != '')
			$where .= ' and g.ALMAP_Codigo=' . $filter->codigoAlmacen;

		$limit = "";
		if ((string)$offset != '' && $number_items != ''){
			$limit = 'LIMIT ' . $offset . ',' . $number_items;
		}

		$sql = "SELECT g.GUIAREMC_Fecha,
		g.GUIAREMC_FechaTraslado,
		g.GUIAREMP_Codigo,
		g.GUIAREMC_Serie,
		g.GUIAREMC_Numero,
		g.GUIAREMC_CodigoUsuario,
		al.ALMAC_Descripcion,
		g.OCOMP_Codigo,
		g.GUIAREMC_Numero,
		g.GUIAREMC_NumeroRef,
		g.DOCUP_Codigo,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
		m.MONED_Simbolo,
		g.GUIAREMC_total,
		g.GUIAREMC_FlagEstado
		FROM cji_guiarem g
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_comprobante_guiarem CCPG ON CCPG.GUIAREMP_Codigo=g.GUIAREMP_Codigo AND CCPG.COMPGUI_FlagEstado=1
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado = 1
		AND g.GUIAREMC_TipoGuia != 1
		AND g.COMPP_Codigo =" . $compania . " " . $where . "
		AND CCPG.GUIAREMP_Codigo IS NULL
		GROUP BY g.GUIAREMP_Codigo
		ORDER BY " . ($tipo_oper == 'V' ? 'g.GUIAREMC_Serie DESC, g.GUIAREMC_Numero DESC' : 'g.GUIAREMC_FechaRegistro DESC') . " " . $limit;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}

	public function no_asociados_all($tipo_oper = 'V', $filter = NULL, $number_items = '', $offset = ''){
		$compania = $this->compania;
		$where = '';

		if (isset($filter->cliente) && $filter->cliente != '')
			$where .= ' and g.CLIP_Codigo=' . $filter->cliente;
		else if (isset($filter->proveedor) && $filter->proveedor != '')
			$where .= ' and g.PROVP_Codigo=' . $filter->proveedor;

		$limit = "";
		if ((string)$offset != '' && $number_items != ''){
			$limit = 'LIMIT ' . $offset . ',' . $number_items;
		}

		$sql = "SELECT g.GUIAREMC_Fecha,
		g.GUIAREMC_FechaTraslado,
		g.GUIAREMP_Codigo,
		g.GUIAREMC_Serie,
		g.GUIAREMC_Numero,
		g.GUIAREMC_CodigoUsuario,
		al.ALMAC_Descripcion,
		g.OCOMP_Codigo,
		g.GUIAREMC_Numero,
		g.GUIAREMC_NumeroRef,
		g.DOCUP_Codigo,
		emp.EESTABC_Descripcion,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
		m.MONED_Simbolo,
		g.GUIAREMC_total,
		g.GUIAREMC_FlagEstado
		FROM cji_guiarem g
		INNER JOIN cji_comprobante_guiarem CCPG ON CCPG.GUIAREMP_Codigo = g.GUIAREMP_Codigo AND CCPG.COMPGUI_FlagEstado = 1
		INNER JOIN cji_compania comp ON comp.COMPP_Codigo = g.COMPP_Codigo
		INNER JOIN cji_emprestablecimiento emp ON emp.EESTABP_Codigo = comp.EESTABP_Codigo
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado = 1
		AND g.GUIAREMC_TipoGuia != 1
		AND NOT EXISTS(SELECT d.GUIAREMP_Codigo FROM cji_despachodetalle d WHERE d.GUIAREMP_Codigo = g.GUIAREMP_Codigo) " . $where . "
		GROUP BY g.GUIAREMP_Codigo
		ORDER BY g.GUIAREMC_Serie DESC, g.GUIAREMC_Numero DESC $limit";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}

	public function buscar_asoc($tipo_oper = 'V', $filter = NULL, $number_items = '', $offset = '')
	{
		$compania = $this->compania;
		$data_confi = $this->companiaconfiguracion_model->obtener($compania);
		$data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 10);

		$where = '';
		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' and g.GUIAREMC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
		switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
			case '1':
			if (isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '2':
			if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
				$where .= ' and g.GUIAREMC_Numero="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
			break;
			case '3':
			if (isset($filter->codigo_usuario) && $filter->codigo_usuario != '')
				$where .= ' and g.GUIAREMC_Serie="' . $filter->codigo_usuario . '"';
			break;
		}
		if ($tipo_oper != 'C'){
			if (isset($filter->cliente) && $filter->cliente != '')
				$where .= ' and g.CLIP_Codigo=' . $filter->cliente;
		}
		else if (isset($filter->proveedor) && $filter->proveedor != '')
			$where .= ' and g.PROVP_Codigo=' . $filter->proveedor;


		if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
			$where .= ' and g.GUIAREMC_Serie="' . $filter->serie . '" and g.GUIAREMC_Numero="' . $filter->numero . '"';
		if (isset($filter->producto) && $filter->producto != '')
			$where .= ' and gd.PRODCTOP_Codigo=' . $filter->producto;
		$limit = "";
		if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
			$where .= ' and e.EMPRC_Ruc=' . $filter->ruc_proveedor;


		if ((string)$offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;
		$sql = "SELECT g.GUIAREMC_Fecha,
		g.GUIAREMC_FechaTraslado,
		g.GUIAREMP_Codigo,
		g.GUIAREMC_Serie,
		g.GUIAREMC_Numero,
		g.GUIAREMC_CodigoUsuario,
		al.ALMAC_Descripcion,
		g.OCOMP_Codigo,
		g.GUIAREMC_Numero,
		g.GUIAREMC_NumeroRef,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
		(CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
		m.MONED_Simbolo,
		g.GUIAREMC_total,
		g.GUIAREMC_FlagEstado
		FROM cji_guiarem g
		LEFT JOIN cji_moneda m ON m.MONED_Codigo=g.MONED_Codigo
		LEFT JOIN cji_comprobante cp ON cp.GUIAREMP_Codigo = g.GUIAREMP_Codigo
		LEFT JOIN cji_almacen al ON al.ALMAP_Codigo=g.ALMAP_Codigo
		LEFT JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo=g.GUIAREMP_Codigo
		" . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=g.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=g.PROVP_Codigo") . "
		LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
		WHERE g.GUIAREMC_TipoOperacion='" . $tipo_oper . "'
		AND g.GUIAREMC_FlagEstado = 1
		AND g.COMPP_Codigo =" . $compania . " " . $where . "

		GROUP BY g.GUIAREMP_Codigo
		ORDER BY " . ($tipo_oper == 'V' ? 'g.GUIAREMC_Serie DESC, g.GUIAREMC_Numero DESC' : 'g.GUIAREMC_FechaRegistro DESC') . " " . $limit;

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
		$where = array("GUIAREMP_Codigo" => $id);
		$query = $this->db->where($where)->get('cji_guiarem', 1);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

  # OBTENER GUIA Y DETALLES PARA MOSTRAR EL COMPROMISO AL HACER LA GUIA -> 
	public function obtener_datos_guia($id){
		$sql = "SELECT * FROM cji_guiarem WHERE GUIAREMP_Codigo = $id AND GUIAREMC_FlagEstado = 1";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function motivoAnulacion($guia, $motivo) {
		$sql = "UPDATE cji_guiarem SET GUIAREMC_Observacion = UPPER(CONCAT(GUIAREMC_Observacion,' * ','$motivo')) WHERE GUIAREMP_Codigo = $guia;";
		$this->db->query($sql);
	}

	public function obtener_SerNumCom($idGuia){
		$sql = "SELECT cp.CPC_Serie, cp.CPC_Numero FROM cji_comprobante cp WHERE GUIASAP_Codigo = (SELECT gr.GUIASAP_Codigo FROM cji_guiarem gr INNER JOIN) $idGuiaSap";
	}

	public function listar_productos_guias($id){
		$sql = "SELECT * FROM cji_guiaremdetalle WHERE GUIAREMP_Codigo = $id AND GUIAREMDETC_FlagEstado = 1";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_guiarem", (array)$filter);
		$guiarem_id = $this->db->insert_id();
		return $guiarem_id;
	}

	public function modificar($id, $filter)
	{
		$this->db->where("GUIAREMP_Codigo", $id);
		$this->db->update("cji_guiarem", (array)$filter);
	}

	public function eliminar($id, $userCod)
	{
		$compania = $this->compania;
		$detalle = $this->guiaremdetalle_model->listar($id);
		$list_guiare = $this->obtener($id);
		$gsap = $list_guiare[0]->GUIASAP_Codigo;
		$ginp = $list_guiare[0]->GUIAINP_Codigo;
		$oper = $list_guiare[0]->GUIAREMC_TipoOperacion;

        //listamos los detalles de la guiatrem
		$contador = count($detalle);
		for ($i = 0; $i < $contador; $i++) {
			$prodcod = $detalle[$i]->PRODCTOP_Codigo;
			$cantidad = $detalle[$i]->GUIAREMDETC_Cantidad;
			$unid_medida = $detalle[$i]->UNDMED_Codigo;


            //CUANDO SE TRATA DE UNA COMPRA
			if ($oper == "C") {
				$guiainp_datos = $this->guiain_model->obtener($ginp);
				$almacencod = $guiainp_datos[0]->ALMAP_Codigo;
				$docupcod = 5;
                //buscar lote 
				$lote_datos = $this->lote_model->obtener_x_guia($prodcod, $ginp);
				$codlote = $lote_datos[0]->LOTP_Codigo;
                //obtener el valor del stock
				$almacenproducto_datos = $this->almacenproducto_model->obtener($almacencod, $prodcod);
				$almacenprodcod = $almacenproducto_datos[0]->ALMPROD_Codigo;
				$stock = $almacenproducto_datos[0]->ALMPROD_Stock;

				$productoundad = $this->productounidad_model->obtener($prodcod, $unid_medida);
				if ($productoundad) {
					$flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
					$factor = $productoundad->PRODUNIC_Factor;
					if ($flagPrincipal == 0) {
						if ($factor > 0) {

							$cantidad = $cantidad / $factor;
							if (strpos($cantidad, ".") == true) {
								$cantidad = round($cantidad, 3);
							}
						}

					}
				}

                # La guia ya no suma ni resta productos del stock
				$nuevostock = $stock;
				if ($nuevostock < 0) {
					echo "<script>alert('STOCK EN NEGATIVO')</script>";
				} else {
					$data = array("GUIAINC_FlagEstado" => '0');
					$where = array("GUIAINP_Codigo" => $ginp);
					$this->db->where($where);
					$this->db->update('cji_guiain', $data);


                    //Eliminar Kardex
					$this->kardex_model->eliminar($docupcod, $ginp, $prodcod);
                    //elimina almaprolote
					$this->almaprolote_model->eliminar($almacenprodcod, $codlote);
                    //elimino lote
					$this->lote_model->eliminar($codlote);
                    //actualizar stock
					$data = array("ALMPROD_Stock" => $nuevostock);
					$where = array("ALMAC_Codigo" => $almacencod, "PROD_Codigo" => $prodcod, "COMPP_Codigo" => $compania);
					$this->db->where($where);
					$this->db->update('cji_almacenproducto', $data);
                    //eliminar los alamacenproductoseri
					$this->db->delete('cji_almacenproductoserie', array("ALMPROD_Codigo" => $almacenprodcod));
                    //obtenemos los datos del almacen stock
					$series_datos = $this->seriemov_model->buscar_x_guiainp($ginp, $prodcod);
					for ($j = 0; $j < count($series_datos); $j++) {
						$serie = $series_datos[$j]->SERIC_Numero;
						$numero = $series_datos[$j]->SERIP_Codigo;
                        //eliminar las series 
						$this->db->delete('cji_seriemov', array("SERIP_Codigo" => $numero));
						$this->db->delete('cji_serie', array("SERIP_Codigo" => $numero));
					}
				}
                //CUANDO SE TRATA DE VENDER			
			} else {

                //eliminacion logica de la guia	
				$data = array("GUIASAC_FlagEstado" => '0');
				$where = array("GUIASAP_Codigo" => $gsap);
				$this->db->where($where);
				$this->db->update('cji_guiasa', $data);
                //obtener el almacen		
				$guiasap_datos = $this->guiasa_model->obtener($gsap);
				$almacencod = $guiasap_datos->ALMAP_Codigo;
				$docupcod = 6;
                //buscar lote 
				$lote_datos = $this->kardex_model->obtener_registros_x_dcto($prodcod, $docupcod, $gsap);
				$codlote = $lote_datos[0]->LOTP_Codigo;
                //obtener el valor del stock

				$almacenproducto_datos = $this->almacenproducto_model->obtener($almacencod, $prodcod);
				$almacenprodcod = $almacenproducto_datos[0]->ALMPROD_Codigo;
				$stock = $almacenproducto_datos[0]->ALMPROD_Stock;
				$costo = $almacenproducto_datos[0]->ALMPROD_CostoPromedio;
				$productoundad = $this->productounidad_model->obtener($prodcod, $unid_medida);
				if ($productoundad) {
					$flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
					$factor = $productoundad->PRODUNIC_Factor;
					if ($flagPrincipal == 0) {
						if ($factor > 0)
							$cantidad = $cantidad / $factor;
					}
				}

				$series_datos = $this->seriemov_model->buscar_x_guiasap($gsap, $prodcod);
				for ($j = 0; $j < count($series_datos); $j++) {
					$serie = $series_datos[$j]->SERIC_Numero;
					$numero = $series_datos[$j]->SERIP_Codigo;
                    //--obtener la guia de entrada por el serip_codigo
					$guiaentrada_datos = $this->seriemov_model->obtener($numero);
					$guiainps = $guiaentrada_datos[0]->GUIAINP_Codigo;
                    //Inserto datos en la serie
					$data = array(
						'PROD_Codigo' => $prodcod,
						'SERIC_Numero' => $serie,
						'SERIC_FlagEstado' => '1'
					);
					$this->db->insert('cji_serie', $data);
					$seri = $this->db->insert_id();
                    //Inserto datos en la serieMOV
					$datas = array(
						'SERIP_Codigo' => $seri,
						'SERMOVP_TipoMov' => '1',
						'GUIAINP_Codigo' => $guiainps);
					$this->db->insert('cji_seriemov', $datas);

                    //almacen producto
					$datax = array('ALMPROD_Codigo' => $almacenprodcod,
						'SERIP_Codigo' => $seri);
					$this->db->insert('cji_almacenproductoserie', $datax);
                    //almacen producto serie
                    //eliminar las series 
					$this->db->delete('cji_seriemov', array("SERIP_Codigo" => $numero));
					$this->db->delete('cji_serie', array("SERIP_Codigo" => $numero));
				}
			}
		}


		$data = array("GUIAREMC_FlagEstado" => '0', "USUA_Anula" => $userCod);
		$where = array("GUIAREMP_Codigo" => $id);
		$this->db->where($where);
		$this->db->update('cji_guiarem', $data);
	}

	public function obtener_ultimo_numero($serie = '')
	{
		$compania = $this->compania;
		$where = array("COMPP_Codigo" => $compania);

		if ($serie != '')
			$where['GUIAREMC_Serie'] = $serie;
		else
			$where['GUIAREMC_Serie'] = NULL;

		$where['GUIAREMC_TipoOperacion'] = 'V';

		$query = $this->db->order_by('GUIAREMC_Serie', 'desc')->order_by('GUIAREMC_Numero', 'desc')->where($where)->get('cji_guiarem', 1);
		$numero = 1;
		if ($query->num_rows() > 0) {
			$data = $query->result();
			$numero = (int)$data[0]->GUIAREMC_Numero + 1;
		}
		return $numero;
	}

	public function listar_guiarem_nocomprobante($tipo_oper = 'V', $comprobante_codigo = '')
	{
		$where = array("COMPP_Codigo" => $this->compania, "GUIAREMC_TipoOperacion" => $tipo_oper);
		$query = $this->db->order_by('GUIAREMC_Serie', 'desc')->order_by('GUIAREMC_Numero', 'desc')->order_by('GUIAREMC_CodigoUsuario', 'desc')
		->where_not_in('GUIAREMP_Codigo', '0')
		->where($where)
		->get('cji_guiarem');
		$data = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$comprobante = $this->comprobante_model->buscar_x_numero_guiarem($fila->GUIAREMP_Codigo);

				if (count($comprobante) == 0 || ($comprobante_codigo != '' && $comprobante[0]->CPP_Codigo == $comprobante_codigo)) {
					if ($tipo_oper == 'V') {
						$datos_cliente = $this->cliente_model->obtener($fila->CLIP_Codigo);
						$fila->nombre = $datos_cliente->nombre;
					} else {
						$datos_proveedor = $this->proveedor_model->obtener($fila->PROVP_Codigo);
						$fila->nombre = $datos_proveedor->nombre;
					}

					if ($fila->GUIAREMC_CodigoUsuario != '')
						$fila->codigo = $fila->GUIAREMC_CodigoUsuario;
					elseif ($fila->GUIAREMC_Serie != '')
						$fila->codigo = $fila->GUIAREMC_Serie . '-' . $fila->GUIAREMC_Numero;
					else
						$fila->codigo = $fila->GUIAREMC_Numero;


					$data[] = $fila;
				}
			}
		}
		return $data;
	}

	public function buscar_x_numero_presupuesto($presupuesto)
	{
		$compania = $this->compania;

		$where = array("COMPP_Codigo" => $compania, "GUIAREMC_FlagEstado" => "1", "PRESUP_Codigo" => $presupuesto);
		$query = $this->db->order_by('GUIAREMC_Numero', 'desc')->where($where)->get('cji_guiarem');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_x_producto_orden($tipo_orden, $tipo_guia, $cod_orden, $cod_prod)
	{
		$compania = $this->compania;
		$where = array(
			"g.COMPP_Codigo" => $compania, "g.GUIAREMC_FlagEstado" => "1",
			"o.OCOMP_Codigo" => $cod_orden, "PRODCTOP_Codigo" => $cod_prod,
			"o.OCOMC_TipoOperacion" => $tipo_orden, "GUIAREMC_TipoOperacion" => $tipo_guia
		);

		$this->db->from('cji_guiarem g');
		$this->db->join('cji_guiaremdetalle gd', 'gd.GUIAREMP_Codigo = g.GUIAREMP_Codigo');
		$this->db->join('cji_ordencompra o', 'g.OCOMP_Codigo = o.OCOMP_Codigo');
		$query = $this->db->order_by('GUIAREMC_Numero', 'desc')->where($where)->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_x_orden($tipo_orden, $tipo_guia, $cod_orden)
	{
		$compania = $this->compania;
		$where = array(
			"g.COMPP_Codigo" => $compania, "g.GUIAREMC_FlagEstado" => "1",
			"o.OCOMP_Codigo" => $cod_orden, "o.OCOMC_TipoOperacion" => $tipo_orden,
			"GUIAREMC_TipoOperacion" => $tipo_guia
		);

		$this->db->from('cji_guiarem g');
		$this->db->join('cji_guiaremdetalle gd', 'gd.GUIAREMP_Codigo = g.GUIAREMP_Codigo');
		$this->db->join('cji_ordencompra o', 'g.OCOMP_Codigo = o.OCOMP_Codigo');
		$query = $this->db->order_by('GUIAREMC_Numero', 'desc')->where($where)->group_by('g.GUIAREMP_Codigo')->get('');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
		return array();
	}

	public function buscar_x_guiain($guiain)
	{
		$where = array("GUIAINP_Codigo" => $guiain, 'GUIAREMC_FlagEstado' => '1');
		$query = $this->db->where($where)
		->join('cji_moneda m', 'm.MONED_Codigo=g.MONED_Codigo')
		->join('cji_almacen a', 'a.ALMAP_Codigo=g.ALMAP_Codigo')
		->select('g.*, m.MONED_Simbolo, a.ALMAC_Descripcion')->get('cji_guiarem g');
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function buscar_x_guiasa($guiasa)
	{
		$where = array("GUIASAP_Codigo" => $guiasa, 'GUIAREMC_FlagEstado' => '1');
		$query = $this->db->where($where)->get('cji_guiarem');
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}



	public function buscarGuiaremComprobante($guiarem,$estadoAsociacion){
		$this->db->from('cji_comprobante_guiarem cg');
		$this->db->join('cji_comprobante c', 'c.CPP_Codigo=cg.CPP_Codigo');

		if($estadoAsociacion!=null && trim($estadoAsociacion)!="")
			$this->db->where("cg.COMPGUI_FlagEstado =",$estadoAsociacion);


		$this->db->where("cg.GUIAREMP_Codigo",$guiarem);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function serieNum_comprobante_guia($guia){
		$compania = $this->compania;
		$sql = "SELECT c.PROYP_Codigo, c.CPC_Serie, c.CPC_Numero
		FROM cji_comprobante c
		LEFT JOIN cji_comprobante_guiarem cg ON c.CPP_Codigo = cg.CPP_Codigo
		WHERE cg.GUIAREMP_Codigo = $guia";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result();
		}
		else
			return NULL;
	}

}

?>