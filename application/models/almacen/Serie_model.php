<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Serie_Model extends CI_Model {

	##  -> Begin
	public function __construct() {
		parent::__construct();
	}
	##  -> End

	public function seleccionar($producto_id, stdClass $filter = null, $default = "") {
		$nombre_defecto = $default == "" ? ":: Seleccione ::" : $default;
		$arreglo = array('' => $nombre_defecto);
		foreach ($this->obtener($producto_id, $filter) as $indice => $valor) {
			$indice1 = $valor->SERIP_Codigo;
			$valor1 = $valor->SERIC_Numero;
			$arreglo[$indice1] = $valor1;
		}
		return $arreglo;
	}

	public function listar($producto_id) {
		$where = array("PROD_Codigo" => $producto_id, "SERIC_FlagEstado" => 1);
		$query = $this->db->where($where)->get('cji_serie');
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function listar_x_codigodoc($producto, $tipomov, $codigodoc) {
		$where = array('cji_serie.PROD_Codigo' => $producto, 'cji_seriemov.SERMOVP_TipoMov' => $tipomov);
		if ($tipomov == '1')
			$where['cji_seriemov.GUIAINP_Codigo'] = $codigodoc;
		else
			$where['cji_seriemov.GUIASAP_Codigo'] = $codigodoc;

		$query = $this->db->where($where)
		->join('cji_seriemov', 'cji_seriemov.SERIP_Codigo = cji_serie.SERIP_Codigo', 'inner')
		->select('cji_serie.SERIP_Codigo,cji_seriemov.SERMOVP_Codigo')
		->get('cji_serie');
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	public function obtenerserie($producto, $serie_producto) {
		$query = $this->db->select("SERIC_Numero")->where('PROD_Codigo', $producto)->where('SERIC_Numero', "'" . $serie_producto . "'")->get('cji_serie');
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function obtener2($serie_id) {
		$query = $this->db->where("SERIP_Codigo", $serie_id)->get('cji_serie');
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function validarserie($serie,$serieCodigo){

		$query = $this->db->where("SERIC_Numero	", $serie)->where('SERIP_Codigo !=',$serieCodigo)->get('cji_serie');
		if ($query->num_rows() > 0) {
			return $query->row();
		}

	}

	public function obtener($producto, $serie_id) {
		$where = array("SERIP_Codigo" => $serie_id, "PROD_Codigo" => $producto);
		$query = $this->db->where($where)->get('cji_serie');

		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function insertar(stdClass $filter = null) {
		$this->db->insert("cji_serie", (array) $filter);
		return $this->db->insert_id();
	}

	public function modificar($id, $filter) {
		$this->db->where("SERIP_Codigo", $id);
		$this->db->update("cji_serie", (array) $filter);
	}

	public function obtenerSerieProducto($codigo_producto, $serie_producto) {
		$this->db->select('cji_serie.SERIC_Numero')->join('cji_almacenproductoserie', 'cji_almacenproductoserie.SERIP_Codigo=cji_serie.SERIP_Codigo');
		$this->db->where('cji_serie.PROD_Codigo', $codigo_producto)->where('cji_almacenproductoserie.ALMPROD_Codigo', 1)->where_in('cji_serie.SERIC_Numero', $serie_producto)->from('cji_serie');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return "";
		}
	}

	public function registrar_series($producto, $series) {
		date_default_timezone_set("America/Lima");
		$data3 = array(
			"PROD_Codigo" => $producto,
			"SERIC_Numero" => $series,
			"SERIC_FechaRegistro" => date('Y-m-d H:i:s'),
			"SERIC_FlagEstado" => 1
		);
		$this->db->insert("cji_serie", $data3);
		$query = $this->db->select_max('SERIP_Codigo')->get('cji_serie');
		if ($query->num_rows() > 0) {
			$idserie = $query->row();
			$data4 = array(
				"ALMPROD_Codigo" => 1,
				"SERIP_Codigo" => $idserie->SERIP_Codigo,
				"ALMPRODSERC_FechaRegistro" => date('Y-m-d H:i:s')
			);
			$this->db->insert("cji_almacenproductoserie", $data4);
		}
	}

	public function actualiza_series($producto, $series, $hdseries) {
		date_default_timezone_set("America/Lima");
		$data3 = array(
			"SERIC_Numero" => $series,
			"SERIC_FechaModificacion" => date('Y-m-d H:i:s')
		);
		$this->db->where('PROD_Codigo', $producto)->where("ALMPROD_Codigo", 1)->where('SERIP_Codigo', $hdseries);
		$this->db->update("cji_serie", $data3);
	}

	public function eliminar2($id) {
		$this->db->delete('cji_serie', array("SERIP_Codigo" => $id));
	}



	/**gcbq mejora implementra busqueda general de serie***/
	public function buscar($filter, $number_items = '', $offset = '') {
		if($filter!=null){
			if(isset($filter->SERIP_Codigo) && $filter->SERIP_Codigo!=null && $filter->SERIP_Codigo!=0){
				$this->db->where('SERIP_Codigo', $filter->SERIP_Codigo);
			}
			if(isset($filter->PROD_Codigo) && $filter->PROD_Codigo!=null && $filter->PROD_Codigo!=0){
				$this->db->where('PROD_Codigo', $filter->PROD_Codigo);
			}
			if(isset($filter->SERIC_Numero) && $filter->SERIC_Numero!=null && trim($filter->SERIC_Numero)!=""){
				$this->db->where('SERIC_Numero', $filter->SERIC_Numero);
			}
			if(isset($filter->SERIC_FlagEstado) && $filter->SERIC_FlagEstado!=null && trim($filter->SERIC_FlagEstado)!=""){
				$this->db->where('SERIC_FlagEstado', $filter->SERIC_FlagEstado);
			}
			/**SERIE RELACIONADA CON COMPRA**/
			if(isset($filter->DOCUP_Codigo) && $filter->DOCUP_Codigo!=null && $filter->DOCUP_Codigo!=0){
				$this->db->where('DOCUP_Codigo', $filter->DOCUP_Codigo);
			}
			if(isset($filter->SERIC_NumeroRef) && $filter->SERIC_NumeroRef!=null && $filter->SERIC_NumeroRef!=0){
				$this->db->where('SERIC_NumeroRef', $filter->SERIC_NumeroRef);
			}

			/**SERIE RELACIONADA CON VENTA**/
			if(isset($filter->DOCUP_CodigoV) && $filter->DOCUP_CodigoV!=null && $filter->DOCUP_CodigoV!=0){
				$this->db->where('DOCUP_CodigoV', $filter->DOCUP_CodigoV);
			}
			if(isset($filter->SERIC_NumeroRefV) && $filter->SERIC_NumeroRefV!=null && $filter->SERIC_NumeroRefV!=0){
				$this->db->where('SERIC_NumeroRefV', $filter->SERIC_NumeroRefV);
			}

			$query=$this->db->get('cji_serie', $number_items, $offset);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $fila) {
					$data[] = $fila;
				}
				return $data;
			}
		}else{
			return $data;
		}
	}

	public function cantidad_series_presente_x_ocompra($ocompra, $producto) {
		$this->load->model('almacen/almacenproductoserie_model');
		$this->load->model('almacen/guiarem_model');
		$lista_guiarem = $this->guiarem_model->buscar_x_orden('C', 'C', $ocompra);
		$presente = 0;
		foreach ($lista_guiarem as $guiarem) {
			$guiain = $guiarem->GUIAINP_Codigo;
			$lista_series = $this->listar_x_codigodoc($producto, '1', $guiain);
			foreach ($lista_series as $serie) {
				$lista_prodalmaserie = $this->almacenproductoserie_model->buscar_x_serie($serie->SERIP_Codigo);
				if (count($lista_prodalmaserie) > 0)
					$presente++;
			}
		}
		return $presente;
	}

	public function cantidad_series_presente_x_guiain($guiain, $producto) {
		$this->load->model('almacen/almacenproductoserie_model');
		$presente = 0;
		$lista_series = $this->listar_x_codigodoc($producto, '1', $guiain);
		foreach ($lista_series as $serie) {
			$lista_prodalmaserie = $this->almacenproductoserie_model->buscar_x_serie($serie->SERIP_Codigo);
			if (count($lista_prodalmaserie) > 0)
				$presente++;
		}

		return $presente;
	}

}

?>