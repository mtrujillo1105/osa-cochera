<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Producto_model extends CI_Model
{

	##  -> Begin
	private $empresa;
	private $compania;
	##  -> End

	##  -> Begin
	public function __construct()
	{
		parent::__construct();
		$this->load->model('almacen/productoproveedor_model');
		$this->load->model('maestros/compania_model');
		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
	}
	##  -> End

	##  -> Begin
	public function getProductos($filter = NULL, $onlyRecords = true)
	{
		$compania = $this->compania;

		$limit = (isset($filter->start) && isset($filter->length)) ? " LIMIT $filter->start, $filter->length " : "";
		$order = (isset($filter->order) && isset($filter->dir)) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = "";

		/** búsqueda de articulos actualizada para buscar palabras **/
		/*
		$match = "";
		$string = split(" ", $filter->searchProducto);
		foreach ($string as $key => $value) {
			if($value!=" " && $value!="+" && $value!="-" && $value!="/" && strlen($value)>=3)
				$match.=" +(".$value.")";
		}
		*/

		if (isset($filter->searchNombre) && trim($filter->searchNombre) != "") {
			$filter->searchNombre = trim($filter->searchNombre);
			if (strlen($filter->searchNombre) < 7)
				$where .= " AND p.PROD_Nombre LIKE '%$filter->searchNombre%'";
			else
				$where .= " AND MATCH(p.PROD_Nombre, p.PROD_CodigoUsuario) AGAINST ('$filter->searchNombre')";
		}

		if (isset($filter->searchCodigoUsuario) && trim($filter->searchCodigoUsuario) != "")
			$where .= " AND p.PROD_CodigoUsuario LIKE '%$filter->searchCodigoUsuario%' ";

		if (isset($filter->searchModelo) && trim($filter->searchModelo) != "")
			$where .= " AND p.PROD_Modelo LIKE '%$filter->searchModelo%' ";

		if (isset($filter->searchMarca) && trim($filter->searchMarca) != "")
			$where .= " AND p.MARCP_Codigo = '$filter->searchMarca' ";

		if (isset($filter->searchFamilia) && trim($filter->searchFamilia) != "")
			$where .= " AND p.FAMI_Codigo = '$filter->searchFamilia' ";

		if (isset($filter->searchFabricante) && trim($filter->searchFabricante) != "")
			$where .= " AND p.FABRIP_Codigo = '$filter->searchFabricante' ";

		if (isset($filter->searchFlagBS) && trim($filter->searchFlagBS) != "")
			$where .= " AND p.PROD_FlagBienServicio = '$filter->searchFlagBS' ";

		$rec = "SELECT p.PROD_Codigo, p.PROD_Nombre, p.PROD_FlagBienServicio, p.PROD_StockMinimo, p.PROD_StockMaximo, p.PROD_CodigoInterno, p.PROD_CodigoUsuario,
										p.PROD_CodigoOriginal, p.PROD_Modelo, p.PROD_GenericoIndividual, p.PROD_UltimoCosto, p.PROD_FlagEstado,
										p.AFECT_Codigo, m.MARCP_Codigo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion,
										p.FAMI_Codigo, f.FAMI_Descripcion, fb.FABRIP_Codigo, fb.FABRIC_Descripcion, CONCAT_WS(' - ', um.UNDMED_Simbolo, um.UNDMED_Descripcion) as UNDMED_Simbolo
						FROM cji_producto p
						INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
						LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
						LEFT JOIN cji_familia f ON f.FAMI_Codigo = p.FAMI_Codigo
						LEFT JOIN cji_fabricante fb ON fb.FABRIP_Codigo = p.FABRIP_Codigo
						LEFT JOIN cji_productounidad pu ON pu.PROD_Codigo = p.PROD_Codigo
						LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = pu.UNDMED_Codigo
						WHERE pc.COMPP_Codigo = $compania AND p.PROD_FlagEstado LIKE '1'
						$where $order $limit
						";

		$recF = "SELECT COUNT(DISTINCT p.PROD_Codigo) as registros
						FROM cji_producto p
						INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
						WHERE pc.COMPP_Codigo = $compania AND p.PROD_FlagEstado LIKE '1'
						$where";
		$recT = "SELECT COUNT(DISTINCT p.PROD_Codigo) as registros FROM cji_producto p INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
						WHERE pc.COMPP_Codigo = $compania AND p.PROD_FlagEstado LIKE '1'";

		$records = $this->db->query($rec);

		if ($onlyRecords == false) {
			$recordsFilter = $this->db->query($recF)->row()->registros;
			$recordsTotal = $this->db->query($recT)->row()->registros;
		}

		if ($records->num_rows() > 0) {
			if ($onlyRecords == false) {
				$info = array(
					"records" => $records->result(),
					"recordsFilter" => $recordsFilter,
					"recordsTotal" => $recordsTotal
				);
			} else {
				$info = $records->result();
			}
		} else {
			if ($onlyRecords == false) {
				$info = array(
					"records" => NULL,
					"recordsFilter" => 0,
					"recordsTotal" => $recordsTotal
				);
			} else {
				$info = $records->result();
			}
		}
		return $info;
	}
	##  -> End

	##  -> Begin
	public function getProducto($producto)
	{
		$sql = "SELECT p.*, ps.PRODS_Descripcion
							FROM cji_producto p
							LEFT JOIN cji_productosunat ps ON ps.PRODS_Codigo = p.PROD_CodigoOriginal
							WHERE p.PROD_Codigo = '$producto'
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getLastId()
	{
		$sql = "SELECT MAX(PROD_Codigo) as id FROM cji_producto";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->id;
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getCountType($flagBS)
	{
		$sql = "SELECT COUNT(*) as cantidad FROM cji_producto WHERE PROD_FlagBienServicio LIKE 'flagBS'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->cantidad;
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getPrecios($producto)
	{
		$sql = "SELECT tp.TIPCLIP_Codigo, tp.TIPCLIC_Descripcion,
										GROUP_CONCAT( CONCAT_WS(' ', m.MONED_Simbolo, FORMAT(pp.PRODPREC_Precio,2) ) SEPARATOR ' <br> ') as precio
							FROM cji_tipocliente tp
							LEFT JOIN cji_productoprecio pp ON pp.TIPCLIP_Codigo = tp.TIPCLIP_Codigo AND pp.PROD_Codigo = $producto
							LEFT JOIN cji_moneda m ON m.MONED_Codigo = pp.MONED_Codigo
							WHERE tp.COMPP_Codigo = '$this->compania' AND tp.TIPCLIC_FlagEstado = 1
							GROUP BY tp.TIPCLIP_Codigo, tp.TIPCLIC_Descripcion
					";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getCategorias()
	{
		$sql = "SELECT tp.TIPCLIP_Codigo, tp.TIPCLIC_Descripcion
							FROM cji_tipocliente tp
							WHERE tp.TIPCLIC_FlagEstado = 1
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	public function getTotalesCategoria()
	{
		$compania = $this->compania;

		$sql = "SELECT SUM(pp.PRODPREC_Precio * ap.ALMPROD_Stock) as total, tc.TIPCLIC_Descripcion as categoria, md.MONED_Simbolo as moneda
		FROM cji_producto as p
		INNER JOIN cji_productocompania as pc ON p.PROD_Codigo = pc.PROD_Codigo
		INNER JOIN cji_productoprecio as pp ON p.PROD_Codigo = pp.PROD_Codigo AND pp.EESTABP_Codigo = '$_SESSION[establec]'
		INNER JOIN cji_almacenproducto as ap ON ap.PROD_Codigo = p.PROD_Codigo AND ap.COMPP_Codigo = $compania
		INNER JOIN cji_tipocliente as tc on tc.TIPCLIP_Codigo = pp.TIPCLIP_Codigo
		INNER JOIN cji_moneda as md on md.MONED_Codigo = pp.MONED_Codigo
		WHERE pc.COMPP_Codigo = '$compania' AND pp.PRODPREC_FlagEstado = 1
		GROUP BY pp.TIPCLIP_Codigo, pp.MONED_Codigo
		";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else
			return NULL;
	}

	public function getTotalPrecioCosto($all = false)
	{
		if ($all == false)
			$compania = "AND ap.COMPP_Codigo = " . $this->compania;
		else
			$compania = "AND EXISTS(SELECT c.COMPP_Codigo FROM cji_compania c WHERE c.COMPP_Codigo = pc.COMPP_Codigo AND c.EMPRP_Codigo = $this->empresa)";

		$sql = "SELECT SUM(p.PROD_UltimoCosto * ap.ALMPROD_Stock) as costo
		FROM cji_producto as p
		INNER JOIN cji_productocompania as pc ON p.PROD_Codigo = pc.PROD_Codigo AND p.PROD_FlagEstado = 1
		INNER JOIN cji_almacenproducto as ap ON ap.PROD_Codigo = p.PROD_Codigo $compania
		WHERE p.PROD_FlagBienServicio = 'B' $compania";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data[0]->costo;
		} else
			return 0;
	}









	public function obtenerProductos($keyword, $flag, $compania)
	{
		$sql = "SELECT p.*, m.MARCC_CodigoUsuario, m.MARCC_Descripcion
		FROM cji_producto p
		INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
		LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
		WHERE p.PROD_FlagEstado LIKE '1' AND p.PROD_FlagBienServicio LIKE '$flag' AND pc.COMPP_Codigo = '$compania' AND CONCAT_WS(' ', p.PROD_CodigoUsuario, p.PROD_Nombre) LIKE '%$keyword%'
		LIMIT 10
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function insertarNvoRegistro($filter)
	{
		$this->db->insert("cji_producto", (array) $filter);
		$registro = $this->db->insert_id();

		$companias = $this->compania_model->listar();
		foreach ($companias as $i => $val) {
			$data = array(
				"PROD_Codigo" => $registro,
				"COMPP_Codigo" => $val->COMPP_Codigo
			);
			$this->db->insert("cji_productocompania", $data);
		}

		return $registro;
	}
        
	public function insertarNvoRegistroCiaUnica($filter)
	{
		$this->db->insert("cji_producto", (array) $filter);
		$registro = $this->db->insert_id();
                $data = array(
                        "PROD_Codigo" => $registro,
                        "COMPP_Codigo" => $this->compania
                );
                $this->db->insert("cji_productocompania", $data);
		return $registro;
	}        

	public function modificarRegistro($registro, $filter)
	{
		$this->db->where('PROD_Codigo', $registro);
		return $this->db->update('cji_producto', $filter);
	}



	##  -> Begin
	public function existsCode($codigo, $producto = NULL)
	{
		$where = ($producto != NULL && $producto != "") ? "AND p.PROD_Codigo <> $producto" : "";

		$sql = "SELECT p.* FROM cji_producto p WHERE p.PROD_CodigoUsuario LIKE '$codigo' $where";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function existsNombre($nombre, $producto = NULL)
	{
		$where = ($producto != NULL && $producto != "") ? "AND p.PROD_Codigo <> $producto" : "";

		$sql = "SELECT p.*
		FROM cji_producto p
		WHERE p.PROD_Nombre LIKE '$nombre' $where
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	public function listar_producto_stockmin($filter = NULL)
	{
		$compania = $this->compania;

		$limit = (isset($filter->start) && isset($filter->length)) ? " LIMIT $filter->start, $filter->length " : "";
		$order = (isset($filter->order) && isset($filter->dir)) ? "ORDER BY $filter->order $filter->dir " : "";

		$sql = "SELECT p.PROD_Codigo, p.PROD_Nombre, p.PROD_FlagBienServicio, p.PROD_StockMinimo, p.PROD_StockMaximo, p.PROD_CodigoInterno, p.PROD_CodigoUsuario, p.PROD_CodigoOriginal, p.PROD_Modelo, p.PROD_GenericoIndividual, p.PROD_UltimoCosto, p.PROD_FlagEstado, p.AFECT_Codigo, m.MARCP_Codigo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion, p.FAMI_Codigo, f.FAMI_Descripcion, fb.FABRIC_Descripcion, CONCAT_WS(' - ', um.UNDMED_Simbolo, um.UNDMED_Descripcion) as UNDMED_Simbolo, ap.ALMPROD_Stock
		FROM cji_producto p
		INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
		INNER JOIN cji_almacenproducto ap ON ap.PROD_Codigo = pc.PROD_Codigo
		LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
		LEFT JOIN cji_familia f ON f.FAMI_Codigo = p.FAMI_Codigo
		LEFT JOIN cji_fabricante fb ON fb.FABRIP_Codigo = p.FABRIP_Codigo
		LEFT JOIN cji_productounidad pu ON pu.PROD_Codigo = p.PROD_Codigo
		LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = pu.UNDMED_Codigo
		WHERE pc.COMPP_Codigo = $compania AND p.PROD_FlagBienServicio LIKE 'B' AND p.PROD_FlagEstado LIKE '1'
		HAVING p.PROD_StockMinimo >= ap.ALMPROD_Stock
		$order
		$limit
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else
			return NULL;
	}

	public function stockMin($lista = true)
	{

		$compania = $this->compania;

		$sql = "SELECT pc.COMPP_Codigo, p.PROD_CodigoInterno, p.PROD_CodigoUsuario, p.PROD_Nombre, ap.ALMAC_Codigo,
		ap.ALMPROD_Stock, p.PROD_StockMinimo,
		(SELECT SUM(ocd.OCOMDEC_Cantidad)
		FROM cji_ordencompra oc
		INNER JOIN cji_ocompradetalle ocd ON ocd.OCOMP_Codigo = oc.OCOMP_Codigo
		WHERE oc.OCOMC_FlagEstado != '0' AND oc.OCOMC_TipoOperacion = 'V' AND oc.OCOMC_FlagTerminadoProceso = '0' AND oc.COMPP_Codigo = $compania AND ocd.OCOMDEC_FlagEstado = '1' AND ocd.PROD_Codigo = p.PROD_Codigo AND oc.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cS.OCOMP_Codigo FROM cji_comprobante cS WHERE cS.OCOMP_Codigo = oc.OCOMP_Codigo) AND NOT EXISTS(SELECT g.OCOMP_Codigo FROM cji_guiarem g WHERE g.OCOMP_Codigo = oc.OCOMP_Codigo)
		) as pendienteOC,
		(SELECT SUM(gd.GUIAREMDETC_Cantidad)
		FROM cji_guiarem g
		INNER JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo = g.GUIAREMP_Codigo
		WHERE g.GUIAREMC_FlagEstado != '0' AND g.GUIAREMC_TipoOperacion = 'V' AND g.COMPP_Codigo = $compania AND gd.GUIAREMDETC_FlagEstado = '1' AND gd.PRODCTOP_Codigo = p.PROD_Codigo AND gd.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cgr.GUIAREMP_Codigo FROM cji_comprobante_guiarem cgr WHERE cgr.GUIAREMP_Codigo = g.GUIAREMP_Codigo AND cgr.COMPGUI_FlagEstado = 1)
		) as pendienteGuia,
		(SELECT SUM(cd.CPDEC_Cantidad)
		FROM cji_comprobante c
		INNER JOIN cji_comprobantedetalle cd ON cd.CPP_Codigo = c.CPP_Codigo
		WHERE c.CPC_FlagEstado = '2' AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania AND cd.CPDEC_FlagEstado = '1' AND cd.PROD_Codigo = p.PROD_Codigo AND cd.ALMAP_Codigo = ap.ALMAC_Codigo
		) as pendienteComprobante
		FROM cji_producto p
		INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo AND pc.COMPP_Codigo = $compania
		INNER JOIN cji_almacenproducto ap ON ap.PROD_Codigo = p.PROD_Codigo AND ap.COMPP_Codigo = $compania
		WHERE p.PROD_FlagBienServicio = 'B' AND p.PROD_FlagEstado LIKE '1' 
		AND pc.COMPP_Codigo = $compania
		";
		$query = $this->db->query($sql);

		$data = array();

		if ($lista == false)
			return true;
		else {
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $fila) {
					if ($fila->PROD_StockMinimo >= ($fila->ALMPROD_Stock - ($fila->pendienteOC + $fila->pendienteComprobante + $fila->pendienteGuia))) {
						$fila->pendienteOC = ($fila->pendienteOC == NULL) ? "" : $fila->pendienteOC;
						$fila->pendienteGuia = ($fila->pendienteGuia == NULL) ? "" : $fila->pendienteGuia;
						$fila->pendienteComprobante = ($fila->pendienteComprobante == NULL) ? "" : $fila->pendienteComprobante;
						$data[] = $fila;
					}
				}
				return $data;
			} else
				return NULL;
		}
	}

	public function getStockDisponible($producto)
	{

		$compania = $this->compania;

		$sql = "SELECT pc.COMPP_Codigo, p.PROD_CodigoInterno, p.PROD_CodigoUsuario, p.PROD_Nombre, ap.ALMAC_Codigo,
		ap.ALMPROD_Stock, p.PROD_StockMinimo,
		@pendienteOC := (SELECT SUM(ocd.OCOMDEC_Cantidad)
		FROM cji_ordencompra oc
		INNER JOIN cji_ocompradetalle ocd ON ocd.OCOMP_Codigo = oc.OCOMP_Codigo
		WHERE oc.OCOMC_FlagEstado != '0' AND oc.OCOMC_TipoOperacion = 'V' AND oc.OCOMC_FlagTerminadoProceso = '0' AND oc.COMPP_Codigo IN($compania) AND ocd.OCOMDEC_FlagEstado = '1' AND ocd.PROD_Codigo = p.PROD_Codigo AND oc.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cS.OCOMP_Codigo FROM cji_comprobante cS WHERE cS.OCOMP_Codigo = oc.OCOMP_Codigo) AND NOT EXISTS(SELECT g.OCOMP_Codigo FROM cji_guiarem g WHERE g.OCOMP_Codigo = oc.OCOMP_Codigo)
		) as pendienteOC,
		@pendienteGuia := (SELECT SUM(gd.GUIAREMDETC_Cantidad)
		FROM cji_guiarem g
		INNER JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo = g.GUIAREMP_Codigo
		WHERE g.GUIAREMC_FlagEstado != '0' AND g.GUIAREMC_TipoOperacion = 'V' AND g.COMPP_Codigo IN($compania) AND gd.GUIAREMDETC_FlagEstado = '1' AND gd.PRODCTOP_Codigo = p.PROD_Codigo AND gd.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cgr.GUIAREMP_Codigo FROM cji_comprobante_guiarem cgr WHERE cgr.GUIAREMP_Codigo = g.GUIAREMP_Codigo AND cgr.COMPGUI_FlagEstado = 1)
		) as pendienteGuia,
		@pendienteComprobante := (SELECT SUM(cd.CPDEC_Cantidad)
		FROM cji_comprobante c
		INNER JOIN cji_comprobantedetalle cd ON cd.CPP_Codigo = c.CPP_Codigo
		WHERE c.CPC_FlagEstado = '2' AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo IN($compania) AND cd.CPDEC_FlagEstado = '1' AND cd.PROD_Codigo = p.PROD_Codigo AND cd.ALMAP_Codigo = ap.ALMAC_Codigo
		) as pendienteComprobante
		FROM cji_producto p
		INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = p.PROD_Codigo
		INNER JOIN cji_almacenproducto ap ON ap.PROD_Codigo = pc.PROD_Codigo AND ap.COMPP_Codigo IN($compania)
		WHERE p.PROD_FlagBienServicio = 'B' AND p.PROD_FlagEstado = 1 
		AND pc.COMPP_Codigo IN($compania) AND p.PROD_Codigo = $producto
		";
		$query = $this->db->query($sql);

		$data = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$fila->ALMPROD_StockDisponible = $fila->ALMPROD_Stock - ($fila->pendienteOC + $fila->pendienteComprobante + $fila->pendienteGuia);
				$data[] = $fila;
			}
			return $data;
		} else
			return NULL;
	}

	public function seleccionar()
	{
		$arreglo = array('' => ":: Seleccione ::");
		$listar = $this->listar_productos("1");
		foreach ($listar as $indice => $valor) {
			$indice1 = $valor->PROD_Codigo;
			$valor1 = $valor->PROD_Nombre;
			$arreglo[$indice1] = $valor1;
		}
		return $arreglo;
	}

	public function obtenerPrecioCV($codigo)
	{
		$compania = $this->compania;

		$sql = "SELECT DISTINCT p.PROD_Codigo, p.PROD_UltimoCosto, pp.PRODPREC_Precio
		FROM cji_producto as p
		INNER JOIN cji_productocompania as pc
		ON p.PROD_Codigo = pc.PROD_Codigo
		INNER JOIN cji_productoprecio as pp
		ON p.PROD_Codigo = pp.PROD_Codigo

		WHERE p.PROD_Codigo = '$codigo' AND pc.COMPP_Codigo = '$compania' AND pp.PRODPREC_FlagEstado = 1 LIMIT 3";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return NULL;
	}

	public function obtenerPrecioV($codigo)
	{
		$compania = $this->compania;

		$sql = "SELECT p.PROD_Codigo, p.PROD_UltimoCosto
		FROM cji_producto as p
		INNER JOIN cji_productocompania as pc
		ON p.PROD_Codigo = pc.PROD_Codigo
		WHERE p.PROD_Codigo = '$codigo' AND pc.COMPP_Codigo = '$compania'";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data[0]->PROD_UltimoCosto;
		} else
			return 0;
	}

	public function nvoCosto($codigo, $costo)
	{
		$sql = "UPDATE cji_producto SET PROD_UltimoCosto = '$costo' WHERE PROD_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query == true)
			return 1;
		else
			return 0;
	}

	public function listar_prod($flagBS, $tipo, $opcion = "", $orden = "1", $number_items = "", $offset = "")
	{
		$this->db->limit($number_items, $offset);
		$query = $this->db->get('cji_producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_prod($codigo, $nombre, $familia, $marca, $flagBS, $number_items = "", $offset = "")
	{
		if ($codigo != '') {
			$this->db->where('PROD_CodigoUsuario', $codigo);
		}
		if ($nombre != '') {
			$this->db->like('PROD_Nombre', $nombre);
		}
		if ($familia != '') {
			$this->db->where('FAMI_Codigo', $familia);
		}
		if ($marca != '') {
			$this->db->where('MARCP_Codigo', $marca);
		}
		$this->db->select('cji_producto.PROD_Codigo,cji_producto.PROD_CodigoInterno,cji_producto.PROD_Nombre,
			cji_producto.TIPPROD_Codigo,cji_producto.FAMI_Codigo,cji_producto.PROD_Modelo,cji_producto.PROD_FlagEstado,
			cji_producto.PROD_FlagActivo');
		$this->db->from('cji_productocompania');

		$this->db->limit($number_items, $offset);
		$query = $this->db->get('cji_producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_establecimiento(stdClass $filter = null)
	{

		$this->db->insert("cji_productocompania", (array)$filter);
	}

	public function validar_establecimiento($codigo)
	{

		$this->db->where('PROD_Codigo	', $codigo);
		$query = $this->db->get('cji_productocompania');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_productos($flagBS, $tipo = '1', $opcion = "", $orden = "1", $number_items = "", $offset = "")
	{
		$compania = $this->compania;
		//$names = $this->companiaconfiguracion_model->listar('3');
		$arr = array();

		switch ($tipo) {
			case 1: //Todos
				$where = array("PROD_FlagEstado" => 1);
				break;
			case 2: //Por tipo de producto
				$where = array("PROD_FlagEstado" => 1, "TIPPROD_Codigo" => $opcion);
				break;
			case 3: //Por familia
				$where = array("PROD_FlagEstado" => 1, "FAMI_Codigo" => $opcion);
				break;
			case 4: //Por proveedor
				$where = array("PROD_FlagEstado" => 1, "cji_producto.PROVP_Codigo" => $opcion);
				break;
			case 5: //Por unidad de medida
				$where = array("PROD_FlagEstado" => 1, "UNDMED_Codigo" => $opcion);
				break;
		}
		switch ($orden) {
			case 1: //Por nombre
				$orden = "PROD_Nombre";
				break;
			case 2: //Por codigo
				$orden = "PROD_Codigo";
				break;
		}
		$query = $this->db->select('cji_producto.*')
			->join('cji_producto', 'cji_producto.PROD_Codigo=cji_productocompania.PROD_Codigo')
			->where('PROD_FlagBienServicio', $flagBS)
			->where($where)
			->where_in('cji_productocompania.COMPP_Codigo', $compania)
			->order_by($orden)
			->get('cji_productocompania', $number_items, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else {
			return array();
		}
	}

	public function productos_activos($flagBS = 'B', $number_items = "", $offset = "", $filter = null)
	{
		$compania = $this->compania;

		$this->db->select('cji_producto.*');
		$this->db->join('cji_producto', 'cji_producto.PROD_Codigo=cji_productocompania.PROD_Codigo');
		$this->db->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo', 'left');
		$this->db->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo', 'left');

		if (isset($filter->tipo) && $filter->tipo != "")
			$this->db->where('cji_producto.TIPPROD_Codigo', $filter->tipo);

		if (isset($filter->codigo) && $filter->codigo != "")
			$this->db->like('cji_producto.PROD_CodigoUsuario', $filter->codigo);

		if (isset($filter->nombre) && $filter->nombre != "")
			$this->db->like('cji_producto.PROD_Nombre', $filter->nombre, 'both');

		if (isset($filter->familia) && $filter->familia != "")
			$this->db->like('cji_familia.FAMI_Descripcion', $filter->familia, 'both');

		if (isset($filter->marca) && $filter->marca != "")
			$this->db->like('cji_marca.MARCC_Descripcion', $filter->marca, 'both');

		if (isset($filter->modelo) && $filter->modelo != "")
			$this->db->like('cji_producto.PROD_Modelo', $filter->modelo, 'both');

		$this->db->where('cji_producto.PROD_FlagBienServicio', $flagBS);
		$this->db->where('cji_producto.PROD_FlagActivo', '1');
		$this->db->where('cji_producto.PROD_FlagEstado', '1');
		$this->db->where_in('cji_productocompania.COMPP_Codigo', $compania);
		$this->db->order_by('cji_producto.PROD_Nombre');
		$query = $this->db->get('cji_productocompania', $number_items, $offset);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}



	public function productos_no_activos($flagBS = 'B', $number_items = "", $offset = "", $filter = null)
	{
		$compania = $this->compania;
		$this->db->select('cji_producto.*');
		$this->db->join('cji_producto', 'cji_producto.PROD_Codigo=cji_productocompania.PROD_Codigo');
		$this->db->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo', 'inner');
		$this->db->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo', 'inner');
		if (isset($filter->tipo) && $filter->tipo != "")
			$this->db->where('cji_producto.TIPPROD_Codigo', $filter->tipo);
		if (isset($filter->codigo) && $filter->codigo != "")
			$this->db->like('cji_producto.PROD_CodigoUsuario', $filter->codigo);
		if (isset($filter->nombre) && $filter->nombre != "")
			$this->db->like('cji_producto.PROD_Nombre', $filter->nombre, 'both');
		if (isset($filter->familia) && $filter->familia != "")
			$this->db->like('cji_familia.FAMI_Descripcion', $filter->familia, 'both');
		if (isset($filter->marca) && $filter->marca != "")
			$this->db->like('cji_marca.MARCC_Descripcion', $filter->marca, 'both');
		$this->db->where('cji_producto.PROD_FlagBienServicio', $flagBS);
		$this->db->where('cji_producto.PROD_FlagActivo', '0');
		$this->db->where('cji_producto.PROD_FlagEstado', '0');
		$this->db->where_in('cji_productocompania.COMPP_Codigo', $compania);
		$this->db->order_by('cji_producto.PROD_Nombre');
		$query = $this->db->get('cji_productocompania', $number_items, $offset);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function cambiarEstado($data, $codProducto)
	{
		$this->db->where('PROD_Codigo', $codProducto);
		$valor = $this->db->update('cji_producto', $data);
		return $valor;
	}

	public function listar_productos_inventariados($flagBS, $number_items = "", $offset = "")
	{
		$compania = $this->compania;
		$this->db->select('p.*');
		$this->db->join('cji_inventariodetalle invd', 'invd.PROD_Codigo=p.PROD_Codigo ', 'left');
		$this->db->join('cji_inventario inv', 'inv.INVE_Codigo = invd.INVE_Codigo', 'inner');
		$this->db->where('p.PROD_FlagBienServicio ', $flagBS);
		$this->db->where('p.PROD_FlagEstado', 1);
		$this->db->where('inv.COMPP_Codigo', $compania);
		$this->db->where('inv.INVE_FlagEstado', 1);
		$this->db->order_by('p.PROD_FechaRegistro', 'ASC');
		$query = $this->db->get('cji_producto p', $number_items, $offset);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function listar_productos_no_inventariados($flagBS = 'B', $number_items = "", $offset = "")
	{
		$compania = $this->compania;
		$sql = "SELECT pro.*
        	FROM cji_producto pro
        	WHERE pro.PROD_Codigo not in (
        	SELECT p.PROD_Codigo
        	FROM cji_producto p RIGHT JOIN cji_inventariodetalle invd ON invd.PROD_Codigo=p.PROD_Codigo
        	INNER JOIN cji_inventario inv ON inv.INVE_Codigo = invd.INVE_Codigo
        	WHERE p.PROD_FlagBienServicio  = '" . $flagBS . "'
        	AND p.PROD_FlagEstado = 1
        	AND inv.COMPP_Codigo = " . $compania . "
        	AND inv.INVE_FlagEstado = 1
        	)
        	and pro.PROD_FlagActivo = 1 and pro.PROD_FlagEstado = 1 order by pro.PROD_FechaRegistro ";

		if ($number_items != "" && $offset != "") {
			$sql .= "ASC LIMIT " . $offset . "," . $number_items;
		}

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function obtenerSerieProducto($codigo)
	{
		//$this->db->select('cji_serie.SERIC_Numero,cji_serie.SERIP_Codigo')->join('cji_almacenproductoserie','cji_almacenproductoserie.SERIP_Codigo=cji_serie.SERIP_Codigo');
		$this->db->select('cji_serie.SERIC_Numero')->join('cji_almacenproductoserie', 'cji_almacenproductoserie.SERIP_Codigo=cji_serie.SERIP_Codigo');
		$this->db->where('cji_serie.PROD_Codigo', $codigo)->where('cji_almacenproductoserie.ALMPROD_Codigo', 1)->from('cji_serie');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function listar_productos_general($flagBS = 'B', $number_items = 0, $offset = 20, $producto_busca = '', $comp_select = null)
	{
            $compania = $this->compania;
            $where = '';
            if (isset($producto_busca) && $producto_busca != '') {
                $where = " AND cp.PROD_Codigo = $producto_busca";
            }
            $sql = "SELECT * FROM cji_producto cp
            INNER JOIN cji_productocompania pc
            ON cp.PROD_Codigo = pc.PROD_Codigo AND pc.COMPP_Codigo = $compania
            WHERE cp.PROD_FlagBienServicio = '$flagBS' AND cp.PROD_FlagEstado = '1' $where "
                    . "ORDER BY cp.PROD_Nombre LIMIT $offset,$number_items";
            $query = $this->db->query($sql);
            $data = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $fila) {
                    $data[] = $fila;
                }
            }
            return $data;
	}

	public function buscar_productos($filter, $number_items = "", $offset = "")
	{

		$compania = $this->compania;


		$this->db->select('cji_producto.*')
			->join('cji_producto', 'cji_producto.PROD_Codigo = cji_productocompania.PROD_Codigo ', 'left')
			->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo ', 'left')
			->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo ', 'left')
			->where('cji_productocompania.COMPP_Codigo', $compania)
			->where('PROD_FlagEstado', 1)
			->where('PROD_FlagBienServicio', $filter->flagBS)
			->order_by('cji_producto.PROD_Nombre');

		if (isset($filter->tipo) && $filter->tipo != "")
			$this->db->where('cji_producto.TIPPROD_Codigo', $filter->tipo);

		if (isset($filter->codigo) && $filter->codigo != "")
			$this->db->where('cji_producto.PROD_CodigoUsuario', $filter->codigo);

		if (isset($filter->nombre) && $filter->nombre != "")
			$this->db->like('cji_producto.PROD_Nombre', $filter->nombre, 'both');

		if (isset($filter->familia) && $filter->familia != "")
			$this->db->like('cji_familia.FAMI_Descripcion', $filter->familia, 'both');

		if (isset($filter->marca) && $filter->marca != "")
			$this->db->like('cji_marca.MARCC_Descripcion', $filter->marca, 'both');

		if (isset($filter->codigoInterno) && $filter->codigoInterno != "")
			$this->db->like('cji_producto.PROD_CodigoOriginal', $filter->codigoInterno, 'both');

		if (isset($filter->modelo) && $filter->modelo != "")
			$this->db->like('cji_producto.PROD_Modelo', $filter->modelo, 'both');

		$query = $this->db->get('cji_productocompania', $number_items, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_productos1($filter, $number_items = "", $offset = "")
	{
		if ($filter->nombre != "") {
			$a_filter = new stdClass();
			$a_filter->codigo = $filter->nombre;
			$a_filter->flagBS = $filter->flagBS;

			$data = $this->buscar_productos_general($a_filter);
		}
		//var_dump($data);

		$compania = $this->compania;

		$this->db->select('cji_producto.*, cji_productocompania.COMPP_Codigo');
		$this->db->join('cji_producto', 'cji_producto.PROD_Codigo = cji_productocompania.PROD_Codigo ', 'left');
		$this->db->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo ', 'left');


		$this->db->where('cji_productocompania.COMPP_Codigo', $compania);
		$this->db->where('PROD_FlagEstado', 1);
		$this->db->where('PROD_FlagActivo', 1);
		$this->db->where('PROD_FlagBienServicio', $filter->flagBS);


		if ($filter->nombre != "") {

			if ($data) {
				$this->db->like('cji_producto.PROD_CodigoUsuario', $filter->nombre);
			} else
				$this->db->or_like('cji_producto.PROD_Nombre', $filter->nombre, 'both');
		}

		$this->db->order_by('cji_producto.PROD_Nombre');

		$query = $this->db->get('cji_productocompania', $number_items, $offset);
		// var_dump($this->db->last_query());

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data1[] = $fila;
			}
			return $data1;
		}
	}

	/* public function registrar_publicacion_web(stdClass $filter = null)
      {
      $this->db->insert("impacto_publicacion",(array)$filter);
    } */

	public function buscar_productos_general($filter, $number_items = "", $offset = "")
	{
		$compania = $this->compania;

		$this->db->select('cji_producto.*')
			->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo ', 'left')
			->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo ', 'left')
			->join("cji_productocompania", "cji_productocompania.PROD_Codigo = cji_producto.PROD_Codigo AND cji_productocompania.COMPP_Codigo = $compania", "INNER")
			->where('PROD_FlagEstado', 1)
			->where_not_in('cji_producto.PROD_Codigo', 0)
			->where('PROD_FlagBienServicio', $filter->flagBS)
			->order_by('cji_producto.PROD_Nombre');

		if (isset($filter->codigo) && $filter->codigo != "")
			$this->db->where('cji_producto.PROD_CodigoUsuario', $filter->codigo);
		if (isset($filter->nombre) && $filter->nombre != "")
			$this->db->like('cji_producto.PROD_Nombre', $filter->nombre, 'both');
		if (isset($filter->familia) && $filter->familia != "")
			$this->db->like('cji_familia.FAMI_Codigo', $filter->familia, 'both');
		if (isset($filter->marca) && $filter->marca != "")
			$this->db->like('cji_marca.MARCC_Descripcion', $filter->marca, 'both');
		$query = $this->db->get('cji_producto', $number_items, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_productos_serie($serie, $number_items = "", $offset = "")
	{
		$compania = $this->compania;

		$limit = $number_items == "" && $offset == "" ? $limit = "" : $limit = "limit $offset,$number_items";
		$sql = "SELECT p.*, s.SERIC_Numero FROM cji_almacenproducto ap 
    	INNER JOIN cji_producto p ON ap.PROD_Codigo=ap.PROD_Codigo
    	INNER JOIN cji_almacenproductoserie aps ON aps.ALMPROD_Codigo=ap.ALMPROD_Codigo
    	INNER JOIN cji_serie s ON s.SERIP_Codigo=aps.SERIP_Codigo
    	WHERE ap.ALMAC_Codigo=" . $compania . " AND s.SERIC_Numero LIKE '%" . $serie . "%' 
    	" . $limit;
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_stock($producto, $establec = '', $almacen = '')
	{
		$where = array('ap.PROD_Codigo' => $producto);
		if ($establec != '') {
			$where['c.EESTABP_Codigo'] = $establec;
		}
		if ($almacen != '') {
			$where['ap.ALMAP_Codigo'] = $almacen;
		}
		$query = $this->db->where($where)
			->join('cji_almacen a', 'a.ALMAP_Codigo=ap.ALMAP_Codigo')
			->join('cji_compania c', 'c.COMPP_Codigo=a.COMPP_Codigo')
			->get('cji_almacenproducto ap');
		$stock = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
				$stock += $fila->ALMPROD_Stock;
			}
		}
		return $stock;
	}

	public function obtener_precio($producto, $establec = '', $almacen = '')
	{
		$where = array('ap.PROD_Codigo' => $producto);
		if ($establec != '') {
			$where['c.EESTABP_Codigo'] = $establec;
		}
		if ($almacen != '') {
			$where['ap.ALMAC_Codigo'] = $almacen;
		}
		$query = $this->db->where($where)
			->join('cji_almacen a', 'a.ALMAP_Codigo=ap.ALMAC_Codigo')
			->join('cji_compania c', 'c.COMPP_Codigo=a.COMPP_Codigo')
			->get('cji_almacenproducto ap');
		$precio = 0;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
				$precio += $fila->ALMPROD_CostoPromedio;
			}
		}
		return $precio;
	}

	public function obtener_precios_promedios($producto, $establec = '', $almacen = '')
	{
		$where = array('ap.PROD_Codigo' => $producto);
		if ($establec != '') {
			$where['c.EESTABP_Codigo'] = $establec;
		}
		if ($almacen != '') {
			$where['ap.ALMAP_Codigo'] = $almacen;
		}
		$query = $this->db->where($where)
			->join('cji_almacen a', 'a.ALMAP_Codigo=ap.ALMAP_Codigo')
			->join('cji_compania c', 'c.COMPP_Codigo=a.COMPP_Codigo')
			->get('cji_almacenproducto ap');
		$precios = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
				$precios[] = $fila->ALMPROD_CostoPromedio;
			}
		}
		return $precios;
	}


	public function insertar_carga(stdClass $filter = null)
	{
		$this->db->insert("impacto_documento", (array)$filter);
	}

	public function listar_productos_atributos($producto)
	{
		$query = $this->db->where('PROD_Codigo', $producto)->get('cji_productoatributo');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_producto_unidades($producto, $unidad = '')
	{
		$where = array("PROD_Codigo" => $producto, "PRODUNIC_flagEstado" => 1);
		if ($unidad != '')
			$where['UNDMED_Codigo'] = $unidad;
		$query = $this->db->where($where)->order_by('PRODUNIP_Codigo')->get('cji_productounidad');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return array();
	}

	public function obtener_producto($producto)
	{
		$query = $this->db->where('PROD_Codigo', $producto)->get('cji_producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	//Publicacion Web
	public function obtener_producto_impacto($producto)
	{
		$query = $this->db->where('PROD_Codigo', $producto)->get('impacto_publicacion');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function registrar_publicacion_web(stdClass $filter = null)
	{
		$this->db->insert("impacto_publicacion", (array)$filter);
	}

	public function modificar_publicacion_web($imppub_codigo, stdClass $filter = null)
	{


		$this->db->where("IMPPUB_Codigo", $imppub_codigo);
		$this->db->update("impacto_publicacion", (array)$filter);
	}

	//Fin de la Publicacion Web
	public function obtener_producto_x_nombre($nombre_producto)
	{
		$where = array('PROD_Nombre' => $nombre_producto);
		$query = $this->db->where($where)->get('cji_producto');
		return $query->result();
	}

	public function obtener_producto_x_codigo_usuario($codigo_usuario)
	{
		$where = array('PROD_CodigoUsuario' => $codigo_usuario);
		$query = $this->db->where($where)->get('cji_producto');
		return $query->result();
	}


	///stv
	public function obtener_producto_x_codigo_original($codigo_original)
	{
		$where = array('PROD_CodigoOriginal' => $codigo_original);
		$query = $this->db->where($where)->get('cji_producto');
		return $query->result();
	}

	////


	public function obtener_producto_x_modelo($modelo_producto, $producto)
	{
		$this->db->select('cji_producto.*');
		if ($producto == "") {
			$this->db->where('PROD_Modelo', $modelo_producto);
		} else {
			$this->db->where('PROD_Modelo', $modelo_producto);
			$this->db->where_not_in('PROD_Codigo', $producto);
		}
		$query = $this->db->get('cji_producto');
		return $query->result();
	}

	public function obtener_producto_x_codigo($flagBS, $codigo_interno)
	{
		$query = $this->db->where('PROD_FlagBienServicio', $flagBS)->where('PROD_CodigoInterno', $codigo_interno)->get('cji_producto');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_producto_atributos($producto, $atributo)
	{
		$query = $this->db->where(array("ATRIB_Codigo" => $atributo, "PROD_Codigo" => $producto))->get("cji_productoatributo");
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_producto_unidad($producto)
	{
		$where = array("PROD_Codigo" => $producto, "PRODUNIC_flagPrincipal" => 1, "PRODUNIC_flagEstado" => 1);
		$query = $this->db->order_by('PRODUNIC_flagPrincipal', 'desc')->where($where)->get('cji_productounidad');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_producto($familia, $tipo_producto, $tipoAfectacion, $nombre_producto, $descripcion_breve, $comentario, $codigo_interno, $imagen, $fabricante, $linea, $marca, $pdf, $modelo, $presentacion, $geneindi, $padre, $codigo_usuario, $nombrecorto_producto, $flagBS, $stock_min, $codigo_original, $partida_arancelaria)
	{
		if ($fabricante == '' || $fabricante == '0')
			$fabricante = NULL;
		if ($marca == '' || $marca == '0')
			$marca = NULL;
		if ($linea == '' || $linea == '0')
			$linea = NULL;

		$nombrecorto_producto = $nombrecorto_producto != '' ? strtoupper($nombrecorto_producto) : NULL;
		$descripcion_breve = $descripcion_breve != '' ? strtoupper($descripcion_breve) : NULL;
		$comentario = $comentario != '' ? strtoupper($comentario) : NULL;
		$presentacion = $presentacion != '' ? strtoupper($presentacion) : NULL;

		if ($codigo_usuario == '')
			$codigo_usuario = NULL;
		if ($familia == '' || $familia == '0')
			$familia = NULL;
		if ($codigo_interno == '' || $codigo_interno == '0')
			$codigo_interno = NULL;
		if ($tipo_producto == '' || $tipo_producto == '0')
			$tipo_producto = NULL;
		if ($geneindi == '' || $geneindi == '0')
			$geneindi = NULL;
		if ($padre == '' || $padre == '0')
			$padre = NULL;

		$data = array(
			"FAMI_Codigo" => $familia,
			"TIPPROD_Codigo" => $tipo_producto,
			"AFECT_Codigo" => $tipoAfectacion,
			"PROD_Nombre" => strtoupper($nombre_producto),
			"PROD_NombreCorto" => $nombrecorto_producto,
			"PROD_DescripcionBreve" => $descripcion_breve,
			"PROD_Comentario" => $comentario,
			"PROD_CodigoInterno" => $codigo_interno,
			"PROD_Imagen" => $imagen,
			"PROD_EspecificacionPDF" => $pdf,
			"FABRIP_Codigo" => $fabricante,
			"LINP_Codigo" => $linea,
			"MARCP_Codigo" => $marca,
			"PROD_Modelo" => $modelo,
			"PROD_Presentacion" => $presentacion,
			"PROD_GenericoIndividual" => $geneindi,
			"PROD_PadreCodigo" => $padre,
			"PROD_CodigoUsuario" => $codigo_usuario,
			"PROD_CodigoOriginal" => $codigo_original,
			"PROD_FlagBienServicio" => $flagBS,
			"PROD_StockMinimo" => $stock_min,
			"PROD_PartidaArancelaria" => $partida_arancelaria
		);

		$this->db->insert("cji_producto", $data);
		return $this->db->insert_id();
	}

	public function insertar_producto_unidad($unidad_medida, $producto, $factor, $flagPrincipal)
	{
		$data = array(
			"UNDMED_Codigo" => $unidad_medida,
			"PROD_Codigo" => $producto,
			"PRODUNIC_Factor" => $factor,
			"PRODUNIC_flagPrincipal" => $flagPrincipal
		);
		$this->db->insert("cji_productounidad", $data);
		return $this->db->insert_id();
	}


	public function insertar_producto_total($proveedor, $familia, $tipo_producto, $nombre_producto, $descripcion_breve, $comentario, $unidad_medida, $factor, $flagPrincipal, $atributo, $nombre_atributo, $codigo_familia, $fabricante, $linea, $marca, $imagen, $pdf, $modelo, $presentacion, $geneindi, $padre = '', $codigo_usuario = '', $nombrecorto_producto = '', $flagBS = 'B', $stock_min = 0, $factorprin, $codigo_original, $tipoAfectacion, $partida_arancelaria)
	{
		$this->load->model('almacen/atributo_model');
		$this->load->model('almacen/familia_model');
		$codigo_interno = '';
		if ($familia != '') {
			$datos_familia = $this->familia_model->obtener_familia($familia);
			$numero = $datos_familia[0]->FAMI_Numeracion;
			$numero2 = $numero + 1;
			$codigo_interno = $codigo_familia . str_pad($numero2, 3, "0", STR_PAD_LEFT);

			$this->familia_model->modificar_familia_numeracion($familia, $numero2);
		}

		$producto = $this->insertar_producto($familia, $tipo_producto, $tipoAfectacion, $nombre_producto, $descripcion_breve, $comentario, $codigo_interno, $imagen, $fabricante, $linea, $marca, $pdf, $modelo, $presentacion, $geneindi, $padre, $codigo_usuario, $nombrecorto_producto, $flagBS, $stock_min, $codigo_original, $partida_arancelaria);

		#$this->insertar_producto_compania($producto);
		$comp = $this->compania_model->listar();
		foreach ($comp as $indice => $fila) {
			$this->insertar_producto_compania2($producto, $fila->COMPP_Codigo);
		}

		#Inserta unidad de medida en productos
		if (is_array($unidad_medida) > 0) {
			foreach ($unidad_medida as $indice => $valor) {
				$umedida = $unidad_medida[$indice];
				if ($indice == 0 && $factorprin != "" || $factorprin != 0) {
					#$factor[$indice]=$factorprin;
					#$fact = $factor[$indice];
					$fact = $factorprin;
				} else {
					$fact = $factor[$indice];
				}
				$flagP = $flagPrincipal[$indice];
				$this->insertar_producto_unidad($umedida, $producto, $fact, $flagP);
			}
		}

		#Inserta unidad de medida en servicios
		#if ($flagBS == 'S') {
		#    $umedida = '4';
		#    $fact = '1';
		#    $flagP = '1';
		#    $this->insertar_producto_unidad($umedida, $producto, $fact, $flagP);
		#}


		#Inserta atributos
		if (is_array($atributo) > 0) {
			foreach ($atributo as $indice => $valor) {
				$attrib = $atributo[$indice];
				$valor_attrib = $nombre_atributo[$indice];
				$datos_attrib = $this->atributo_model->obtener_atributo($attrib);
				$tipo_attrib = $datos_attrib[0]->ATRIB_TipoAtributo;
				$this->insertar_producto_atributos($producto, $attrib, $tipo_attrib, $valor_attrib);
			}
		}

		if (is_array($proveedor) > 0) {
			foreach ($proveedor as $indice => $valor) {
				$prov = $valor;
				$filter = new stdClass();
				$filter->PROVP_Codigo = $prov;
				$filter->PROD_Codigo = $producto;
				$this->productoproveedor_model->insertar($filter);
			}
		}

		return $producto;
	}

	public function insertar_producto_compania($producto)
	{
		$data = array(
			"PROD_Codigo" => $producto,
			"COMPP_Codigo" => $this->compania,
		);
		$this->db->insert("cji_productocompania", $data);
	}

	public function insertar_producto_compania2($producto, $compania)
	{
		$data = array(
			"PROD_Codigo" => $producto,
			"COMPP_Codigo" => $compania
		);
		$this->db->insert("cji_productocompania", $data);
	}

	public function obtener_producto_compania($producto, $compania)
	{


		$where = array("PROD_Codigo" => $producto, "COMPP_Codigo" => $compania);
		$query = $this->db->where($where)->get('cji_productocompania');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_producto_atributos($producto, $atributo, $tipo, $valor)
	{

		switch ($tipo) {
			case 1: //Numerico
				$valorNumerico = $valor == '' ? '0' : $valor;
				$valorDate = "0000-00-00 00:00:00";
				$valorString = "";
				break;
			case 2: //Date
				$valorNumerico = "0";
				$valorDate = $valor;
				$valorString = "";
				break;
			case 3: //Strind
				$valorNumerico = "0";
				$valorDate = "0000-00-00 00:00:00";
				$valorString = $valor;
				break;
		}
		$data = array(
			"PROD_Codigo" => $producto,
			"ATRIB_Codigo" => $atributo,
			"PRODATRIB_Numerico" => $valorNumerico,
			"PRODATRIB_Date" => $valorDate,
			"PRODATRIB_String" => $valorString
		);
		$this->db->insert("cji_productoatributo", $data);
	}

	public function modificar_producto($producto, $familia, $tipo_producto, $tipoAfectacion, $nombre_producto, $descripcion_breve, $comentario, $codigo_interno, $imagen, $activo, $fabricante, $linea, $marca, $pdf, $modelo, $presentacion, $geneindi, $padre, $codigo_usuario, $nombrecorto_producto, $stock_min, $codigo_original, $partida_arancelaria)
	{
		if ($fabricante == '' || $fabricante == '0')
			$fabricante = NULL;
		if ($linea == '' || $linea == '0')
			$linea = NULL;
		$nombrecorto_producto = $nombrecorto_producto != '' ? strtoupper($nombrecorto_producto) : NULL;
		$descripcion_breve = $descripcion_breve != '' ? strtoupper($descripcion_breve) : NULL;
		$comentario = $comentario != '' ? strtoupper($comentario) : NULL;
		$presentacion = $presentacion != '' ? strtoupper($presentacion) : NULL;
		if ($codigo_usuario == '')
			$codigo_usuario = NULL;
		if ($familia == '' || $familia == '0')
			$familia = NULL;
		if ($codigo_interno == '' || $codigo_interno == '0')
			$codigo_interno = NULL;
		if ($tipo_producto == '' || $tipo_producto == '0')
			$tipo_producto = NULL;
		if ($geneindi == '' || $geneindi == '0')
			$geneindi = NULL;
		if ($padre == '' || $padre == '0')
			$padre = NULL;

		$data = array(
			"FAMI_Codigo" => $familia,
			"TIPPROD_Codigo" => $tipo_producto,
			"AFECT_Codigo" => $tipoAfectacion,
			"PROD_Nombre" => strtoupper($nombre_producto),
			"PROD_NombreCorto" => $nombrecorto_producto,
			"PROD_DescripcionBreve" => $descripcion_breve,
			"PROD_Comentario" => $comentario,
			"PROD_CodigoInterno" => $codigo_interno,
			"PROD_Imagen" => $imagen,
			"PROD_EspecificacionPDF" => $pdf,
			"PROD_Modelo" => $modelo,
			"PROD_Presentacion" => $presentacion,
			"PROD_GenericoIndividual" => $geneindi,
			"PROD_FlagActivo" => $activo,
			"FABRIP_Codigo" => $fabricante,
			"LINP_Codigo" => $linea,
			"MARCP_Codigo" => $marca,
			"PROD_PadreCodigo" => $padre,
			"PROD_CodigoUsuario" => $codigo_usuario,
			"PROD_CodigoOriginal" => $codigo_original,
			"PROD_StockMinimo" => $stock_min,
			"PROD_PartidaArancelaria" => $partida_arancelaria
		);
		if ($imagen == '')
			unset($data['PROD_Imagen']);
		if ($pdf == '')
			unset($data['PROD_EspecificacionPDF']);
		$this->db->where('PROD_Codigo', $producto);
		$this->db->update("cji_producto", $data);
	}

	public function modificar_producto_unidad($produnidad, $unidad_medida, $producto, $factor, $flagPrincipal)
	{
		$data = array(
			"UNDMED_Codigo" => $unidad_medida,
			"PROD_Codigo" => $producto,
			"PRODUNIC_Factor" => $factor,
			"PRODUNIC_flagPrincipal" => $flagPrincipal
		);
		$this->db->where("PRODUNIP_Codigo", $produnidad);
		$this->db->update("cji_productounidad", $data);
	}

	//$factorprin
	public function modificar_producto_total($producto, $proveedor, $familia, $tipo_producto, $nombre_producto, $descripcion_breve, $comentario, $codigo_interno, $unidad_medida, $factor, $flagPrincipal, $atributo, $tipo_atributo, $nombre_atributo, $produnidad, $imagen, $activo, $fabricante, $linea, $marca, $pdf, $modelo, $presentacion, $geneindi, $padre = '', $codigo_usuario = '', $nombrecorto_producto = '', $stock_min = 0, $factorprin, $codigo_original, $tipoAfectacion, $partida_arancelaria)
	{
		$this->load->model('almacen/familia_model');
		$temp = explode(".", $codigo_interno);
		if ($familia != '' && $temp[count($temp) - 1] == '') {
			$pos = strrpos($codigo_interno, '.');
			$datos_familia = $this->familia_model->obtener_familia($familia);
			$numero = $datos_familia[0]->FAMI_Numeracion;
			$numero2 = $numero + 1;
			$codigo_interno = $datos_familia[0]->FAMI_CodigoInterno . '.' . str_pad($numero2, 3, "0", STR_PAD_LEFT);

			$this->familia_model->modificar_familia_numeracion($familia, $numero2);
		}

		$this->modificar_producto($producto, $familia, $tipo_producto, $tipoAfectacion, $nombre_producto, $descripcion_breve, $comentario, $codigo_interno, $imagen, $activo, $fabricante, $linea, $marca, $pdf, $modelo, $presentacion, $geneindi, $padre, $codigo_usuario, $nombrecorto_producto, $stock_min, $codigo_original, $partida_arancelaria);

		if (is_array($unidad_medida)) {
			foreach ($unidad_medida as $indice => $valor) {
				$umedida = $unidad_medida[$indice];

				///stv
				if ($indice == 0 && ($factorprin != '' || $factorprin != 0)) {

					$fact = $factorprin; //$factor[0];  // var_dump($fact);
				} else {
					///
					$fact = $factor[$indice];
					///stv
				}
				////                
				$flagP = $flagPrincipal[$indice];
				$punidad = $produnidad[$indice];
				if ($punidad != '') {
					if ($umedida != '' && $umedida != '0')
						$this->modificar_producto_unidad($punidad, $umedida, $producto, $fact, $flagP);
					else {
						$filter = new stdClass();
						$filter->PROD_Codigo = $producto;
						$filter->PRODUNIP_Codigo = $punidad;

						$this->eliminar_producto_unidades($punidad);
					}
				} else {
					if ($umedida != '' && $umedida != '0')
						$this->insertar_producto_unidad($umedida, $producto, $fact, $flagP);
				}
			}
		}
		$this->eliminar_producto_atributos($producto);
		if (is_array($nombre_atributo) > 0) {
			foreach ($nombre_atributo as $indice => $valor) {
				$attrib = $atributo[$indice];
				$t_attrib = $tipo_atributo[$indice];
				$v_attrib = $nombre_atributo[$indice];
				$data_prod_atr = $this->buscar_producto_atributo($producto, $attrib);
				if (count($data_prod_atr) > 0)
					$this->modificar_producto_atributos($producto, $attrib, $t_attrib, $v_attrib);
				else
					$this->insertar_producto_atributos($producto, $attrib, $t_attrib, $v_attrib);
			}
		}
		$this->productoproveedor_model->eliminar_proveedores($producto);
		if (is_array($proveedor) > 0) {
			foreach ($proveedor as $indice => $valor) {
				$prov = $valor;
				$filter = new stdClass();
				$filter->PROVP_Codigo = $prov;
				$filter->PROD_Codigo = $producto;
				$this->productoproveedor_model->insertar($filter);
			}
		}
	}

	public function modificar_stock($producto_id, $stock)
	{
		$this->db->where("PROD_Codigo", $producto_id);
		$this->db->update('cji_producto', array("PROD_Stock" => $stock));
	}

	public function modificar_ultCosto($producto_id, $costo)
	{
		$this->db->where("PROD_Codigo", $producto_id);
		$this->db->update('cji_producto', array("PROD_UltimoCosto" => $costo));
	}

	public function modificar_costoPromedio($producto_id, $costo)
	{
		$this->db->where("PROD_Codigo", $producto_id);
		$this->db->update('cji_producto', array("PROD_CostoPromedio" => $costo));
	}

	public function modificar_producto_atributos($producto, $atributo, $tipo, $valor)
	{
		switch ($tipo) {
			case 1: //Numerico
				$valorNumerico = $valor;
				$valorDate = "0000-00-00 00:00:00";
				$valorString = "";
				break;
			case 2: //Date
				$valorNumerico = "0";
				$valorDate = $valor;
				$valorString = "";
				break;
			case 3: //Strind
				$valorNumerico = "0";
				$valorDate = "0000-00-00 00:00:00";
				$valorString = $valor;
				break;
		}

		$fechaModificacion = date('Y-m-d H:i:s');

		$where = array("PROD_Codigo" => $producto, "ATRIB_Codigo" => $atributo);
		$data = array("PRODATRIB_Numerico" => $valorNumerico, "PRODATRIB_Date" => $valorDate, "PRODATRIB_String" => $valorString, "PRODATRIB_FechaModificacion" => $fechaModificacion);
		$this->db->where($where);
		$this->db->update("cji_productoatributo", $data);
	}

	public function eliminar_producto_total($producto)
	{
		/* $this->eliminar_producto_proveedor($producto);

          $filter=new stdClass();
          $filter->PROD_Codigo=$producto;
          $this->productoprecio_model->eliminar_varios($filter);

          $this->eliminar_producto_unidades_total($producto);
          $this->eliminar_producto_atributos($producto);
          $this->eliminar_producto($producto); */

		$where = array("PROD_Codigo" => $producto, "COMPP_Codigo" => $this->compania);
		$this->db->delete('cji_productocompania', $where);
	}

	public function eliminar_producto($producto)
	{
		$where = array("PROD_Codigo" => $producto);
		$this->db->delete('cji_producto', $where);
	}

	public function eliminar_producto_atributos($producto)
	{
		$where = array("PROD_Codigo" => $producto);

		$this->db->delete('cji_productoatributo', $where);
	}

	public function eliminar_producto_proveedor($producto)
	{
		$where = array("PROD_Codigo" => $producto);
		$this->db->delete('cji_productoproveedor', $where);
	}

	public function eliminar_producto_unidades($productounidad)
	{

		$where = array("PRODUNIP_Codigo " => $productounidad);
		$this->db->delete('cji_productounidad', $where);
	}

	public function eliminar_producto_unidades_total($producto)
	{
		$where = array("PROD_Codigo" => $producto);
		$this->db->delete('cji_productounidad', $where);
	}

	public function buscar_producto_atributo($producto, $atributo)
	{
		$where = array("PROD_Codigo" => $producto, "ATRIB_Codigo" => $atributo, "PRODATRIB_FlagEstado" => 1);
		$query = $this->db->where($where)->get('cji_productoatributo');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_marca_modelo_por_producto($producto)
	{
		$sql = "SELECT * FROM cji_producto p INNER JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo WHERE p.PROD_Codigo =" . $producto . "";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}


	///aumentado stv

	public function obtener_maxcodigousu()
	{
		$sql = "select max(PROD_CodigoUsuario) as PROD_CodigoUsuario from cji_producto ";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	////

	////
	public function listar_productos_pdf($flagbs, $codigo,  $nombre,  $idfami, $marca)
	{
		$compania = $this->compania;


		$this->db->select('cji_producto.*,cji_marca.*,cji_familia.*')
			->join('cji_producto', 'cji_producto.PROD_Codigo = cji_productocompania.PROD_Codigo ', 'left')
			->join('cji_familia', 'cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo ', 'left')
			->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo ', 'left')
			->where('cji_productocompania.COMPP_Codigo', $compania)
			->where('PROD_FlagEstado', 1)
			->where('PROD_FlagBienServicio', $flagbs)
			->order_by('cji_producto.PROD_Nombre');


		if ($codigo != "--")
			$this->db->where('cji_producto.PROD_CodigoUsuario', $codigo);
		if ($nombre != "--")
			$this->db->like('cji_producto.PROD_Nombre', $nombre, 'both');
		if ($idfami != "--")
			$this->db->like('cji_familia.FAMI_Descripcion', $idfami, 'both');
		if ($marca != "--")
			$this->db->like('cji_marca.MARCC_Descripcion', $marca, 'both');



		$query = $this->db->get('cji_productocompania');

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	////
	public function listar_producto_sinInventario($filter = null, $number_items = "", $offset = "")
	{
		$this->db->select('p.* , pc.COMPP_Codigo');
		$this->db->join('cji_productocompania pc', 'pc.PROD_Codigo=p.PROD_Codigo', 'left');
		$this->db->join('cji_inventariodetalle invd', 'invd.PROD_Codigo=p.PROD_Codigo ', 'left');
		$this->db->where('p.PROD_FlagBienServicio ', 'B');
		$this->db->where('p.PROD_FlagEstado', 1);
		$this->db->where('pc.COMPP_Codigo', $this->session->userdata('compania'));
		$this->db->where('invd.PROD_Codigo is NULL');
		$this->db->orderby('p.PROD_FechaRegistro , p.PROD_Nombre', 'ASC');
		$query = $this->db->get('cji_producto p', $number_items, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else {
			return;
		}
	}

	public function listar_producto_enInventarioNull($filter = null, $number_items = "", $offset = "")
	{


		$this->db->select('p.* , pc.COMPP_Codigo');
		$this->db->join('cji_productocompania pc', 'pc.PROD_Codigo=p.PROD_Codigo', 'left');
		$this->db->join('cji_inventariodetalle invd', 'invd.PROD_Codigo=p.PROD_Codigo ', 'left');
		$this->db->where('p.PROD_FlagBienServicio ', 'B');
		$this->db->where('invd.INVD_FlagActivacion', 0);
		$this->db->where('p.PROD_FlagEstado', 1);
		$this->db->where('pc.COMPP_Codigo', $this->session->userdata('compania'));
		$this->db->orderby('p.PROD_Nombre', 'ASC');
		$query = $this->db->get('cji_producto p', $number_items, $offset);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else {
			return;
		}
	}


	public function listar_familia_pdf($flagBS, $codigo, $nombre)
	{

		$where = "";

		if ($codigo != "--") {
			$where .= " and FAMI_CodigoInterno ='" . $codigo . "'";
		}
		if ($nombre != "--") {
			$where .= " and FAMI_Descripcion  LIKE '%" . $nombre . "%'";
		}


		$sql = "SELECT * from cji_familia where  FAMI_FlagBienServicio ='" . $flagBS . "'" . $where . "  order by 1 asc ";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_x_codigo($keyword, $flag, $compania)
	{
		$query = $this->db->select('DISTINCT(cji_producto.PROD_Codigo), cji_producto.*')
			->from('cji_productocompania')
			->join('cji_producto', 'cji_producto.PROD_Codigo = cji_productocompania.PROD_Codigo', 'inner')
			->where('cji_producto.PROD_FlagActivo', '1')
			->where('cji_producto.PROD_FlagEstado', '1')
			->where('cji_productocompania.COMPP_Codigo', $compania)
			->where('cji_producto.PROD_FlagBienServicio', $flag)
			->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo', 'left')
			->like('cji_producto.PROD_CodigoInterno', $keyword)
			->or_like('cji_producto.PROD_CodigoUsuario', $keyword)
			->order_by('cji_producto.PROD_Nombre')
			->limit(50)
			->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}


	public function buscar_por_nombre($keyword, $flag, $compania)
	{
		$query = $this->db->select('DISTINCT(cji_producto.PROD_Codigo), cji_producto.*')
			->from('cji_productocompania')
			->join('cji_producto', 'cji_producto.PROD_Codigo = cji_productocompania.PROD_Codigo', 'inner')
			->where('cji_producto.PROD_FlagActivo', '1')
			->where('cji_producto.PROD_FlagEstado', '1')
			->where('cji_productocompania.COMPP_Codigo', $compania)
			->where('cji_producto.PROD_FlagBienServicio', $flag)
			->join('cji_marca', 'cji_marca.MARCP_Codigo = cji_producto.MARCP_Codigo', 'left')
			->like('cji_producto.PROD_Nombre', $keyword, 'both')
			->or_like('cji_producto.PROD_CodigoUsuario', $keyword, 'both')
			->order_by('cji_producto.PROD_Nombre')
			->limit(50)
			->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function buscar_por_nombre_filtros($keyword, $flag, $compania, $filtro = NULL)
	{

		$where = "";

		if (isset($keyword) && $keyword != "" && strlen($keyword) > 1)
			$where .= " AND CONCAT_WS(' ', p.PROD_Nombre, p.PROD_CodigoUsuario) LIKE '%$keyword%'";

		if (isset($filtro->modelo) && $filtro->modelo != NULL && $filtro->modelo != '')
			$where .= " AND p.PROD_Modelo LIKE '%$filtro->modelo%' ";

		if (isset($filtro->marca) && $filtro->marca != NULL && $filtro->marca != '')
			$where .= " AND p.MARCP_Codigo = '$filtro->marca' ";

		if (isset($filtro->familia) && $filtro->familia != NULL && $filtro->familia != '')
			$where .= " AND p.FAMI_Codigo = '$filtro->familia' ";
                
		if (isset($filtro->situacion) && $filtro->situacion != NULL && $filtro->situacion != '')
			$where .= " AND p.PROD_FlagSituacion = '$filtro->situacion' ";                


		$sql = "SELECT DISTINCT p.PROD_Codigo, p.*, marca.MARCP_Codigo, marca.MARCC_Descripcion as marca
        	FROM cji_productocompania pc
        	INNER JOIN cji_producto p ON p.PROD_Codigo = pc.PROD_Codigo
        	LEFT JOIN cji_marca marca ON marca.MARCP_Codigo = p.MARCP_Codigo
        	WHERE p.PROD_FlagActivo = 1 AND p.PROD_FlagEstado = 1 AND pc.COMPP_Codigo = $compania AND p.PROD_FlagBienServicio = '$flag'
        	$where
                ORDER BY p.PROD_CodigoInterno desc
        	LIMIT 15;
        	";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function seleccionar_ultimo_costo($prodCodigo = NULL)
	{
		$sql = "SELECT PROD_UltimoCosto FROM `cji_producto` WHERE `PROD_Codigo` = $prodCodigo";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function seleccionar_precio_cliente($prodCodigo = NULL, $tipCli = NULL)
	{ // FUNCION PRECIO - 
		if ($tipCli == NULL || $tipCli == 0)
			$sql = "SELECT max(PRODPREC_Precio) as PRODPREC_Precio FROM `cji_productoprecio` WHERE `PROD_Codigo` = $prodCodigo";
		else
			$sql = "SELECT PRODPREC_Precio FROM `cji_productoprecio` WHERE `PROD_Codigo` = $prodCodigo AND `TIPCLIP_Codigo` = $tipCli";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}


	public function cargarProductos_autocompletado($term, $flag, $compania, $almacen)
	{
		$query = $this->db->select('pro.*, alp.ALMPROD_Stock, alp.ALMPROD_CostoPromedio')
			->from('cji_almacen al')
			->join('cji_almacenproducto alp', 'alp.ALMAC_Codigo = al.ALMAP_Codigo', 'inner')
			->join('cji_producto pro', 'pro.PROD_Codigo = alp.PROD_Codigo', 'inner')
			->where('alp.ALMAC_Codigo', $almacen)
			->where('alp.COMPP_Codigo', $compania)
			->where('pro.PROD_FlagBienServicio', $flag)
			->where('pro.PROD_FlagActivo', '1')
			->where('pro.PROD_FlagEstado', '1')
			->like('pro.PROD_Nombre', $term)
			->order_by('pro.PROD_Nombre')
			->limit(10)
			->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return NULL;
		}
	}

	public function obtenerPreciosUnoDos($value = '')
	{
		$query = $this->db->select('cji_productoprecio.PRODPREC_Precio')
			->from('cji_productoprecio')
			->join('cji_producto', 'cji_producto.PROD_Codigo=cji_productoprecio.PROD_Codigo', 'left')
			->where('cji_producto.PROD_Codigo', $value)
			->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	/***************************************************************************************************************/

	public function verificaProductoDetalle($ordAdj)
	{

		$sql = "select * from cji_producto where UPPER(PROD_Nombre) = '$ordAdj' ";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return array();
	}

	public function nameproducto($codigo)
	{
		$this->db->select("p.*");
		$query = $this->db->where(array("PROD_Codigo" => $codigo))->get('cji_producto p');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $valor) {
				$data[] = $valor;
			}
			return $data;
		}
	}

	public function buscar_codigo_sunat($search)
	{

		if (strlen($search) < 4)
			$where = " PRODS_Descripcion LIKE '%$search%' ";
		else
			$where = " Match(PRODS_Codigo, PRODS_Descripcion) AGAINST ('$search') ";

		$sql = "SELECT * FROM cji_productosunat WHERE $where LIMIT 10";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function tipo_afectacion($id = NULL)
	{

		$where = ($id != NULL) ? " AND AFECT_Codigo = $id" : "";

		$sql = "SELECT * FROM cji_tipo_afectacion WHERE AFECT_FlagEstado = 1 $where";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return NULL;
	}

	##  -> Begin
	public function getModelos()
	{
		$sql = "SELECT PROD_Modelo FROM cji_producto WHERE PROD_FlagBienServicio LIKE 'B' AND PROD_FlagEstado LIKE '1' GROUP BY PROD_Modelo";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	#***********************************************
	#******** RECETAS
	#***********************************************

	public function listarRecetas($filter = null, $number_items = '', $offset = '')
	{
		$where = '';
		if (isset($filter->codigo) && $filter->codigo != '')
			$where .= " AND r.REC_Codigo = $filter->codigo";

		if (isset($filter->nombre) && $filter->nombre != '')
			$where .= " AND r.REC_Descripcion LIKE '%$filter->nombre%'";

		$limit = "";
		if ((string)$offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;

		$sql = "SELECT *
    	FROM cji_receta r
    	WHERE r.REC_FlagEstado = '1' $where ORDER BY r.REC_Descripcion DESC $limit";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtenerRecetaProducto($producto, $almacen = NULL)
	{
		$compania = $this->compania;

		$stock = ($almacen == NULL) ? "(SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as stock" : "(SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.ALMAC_Codigo = $almacen) as stock";

		$sql = "SELECT r.REC_Codigo, r.REC_Descripcion, r.PROD_Codigo as PROD_CodigoReceta, r.REC_FlagEstado, rd.RECDET_Codigo, rd.PROD_Codigo as PROD_CodigoInsumo, rd.RECDET_Cantidad, rd.RECDET_FlagEstado,
    	p.PROD_Nombre as nombre_producto, p.PROD_CodigoUsuario, p.PROD_Modelo,
    	$stock
    	FROM cji_receta r
    	INNER JOIN cji_recetadetalle rd ON rd.REC_Codigo = r.REC_Codigo
    	LEFT JOIN cji_producto p ON p.PROD_Codigo = rd.PROD_Codigo
    	WHERE r.PROD_Codigo = $producto AND r.REC_FlagEstado = 1 AND rd.RECDET_FlagEstado = 1
    	";
		$query = $this->db->query($sql);

		$data = NULL;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $indice => $columna) {
				$columna->stock = ($columna->stock == NULL) ? 0 : $columna->stock;
				$data[] = $columna;
			}
		}
		return $data;
	}

	public function getInsumosRequired()
	{
		$compania = $this->compania;

		# LISTA TODOS LOS INSUMOS FALTANTES PARA COMPLETAR LAS ORDENES DE PRODUCCION
		$sql = "SELECT SUM(rd.RECDET_Cantidad) as cantidadReceta, SUM( rd.RECDET_Cantidad * pd.PRD_Cantidad ) as cantidaInsumos, rd.PROD_Codigo,
    	(SELECT p.PROD_Nombre FROM cji_producto p WHERE p.PROD_Codigo = rd.PROD_Codigo) as PROD_Nombre,
    	(SELECT p.PROD_UltimoCosto FROM cji_producto p WHERE p.PROD_Codigo = rd.PROD_Codigo) as PROD_UltimoCosto,
    	(SELECT p.AFECT_Codigo FROM cji_producto p WHERE p.PROD_Codigo = rd.PROD_Codigo) as AFECT_Codigo,
    	pu.UNDMED_Codigo,
    	(SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as stock,
    	SUM( rd.RECDET_Cantidad * pd.PRD_Cantidad ) - (SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as insumosFaltantes

    	FROM cji_recetadetalle rd
    	INNER JOIN cji_receta r ON r.REC_Codigo = rd.REC_Codigo
    	INNER JOIN cji_producciondetalle pd ON pd.PROD_Codigo = r.PROD_Codigo
    	LEFT JOIN cji_productounidad pu ON pu.UNDMED_Codigo = rd.PROD_Codigo
    	WHERE pd.PRD_FlagEstado = 1 AND rd.RECDET_FlagEstado = 1 AND EXISTS(SELECT pr.PR_Codigo FROM cji_produccion pr WHERE pr.PR_FlagTerminado > 1 AND pr.PR_FlagOC IS NULL AND pr.PR_Codigo = pd.PR_Codigo)
    	GROUP BY rd.PROD_Codigo
    	";
		$query = $this->db->query($sql);

		# ACTUALIZA EL FlagOC DE TODOS LOS PEDIDOS, PARA QUE NO VUELVAN A SER CONTABILIZADOS AL GENERAR OTRA OC
		$update = "UPDATE cji_produccion pr SET pr.PR_FlagOC = 1 WHERE pr.PR_FlagTerminado > 1 AND pr.PR_FlagOC IS NULL
    	AND pr.PR_Codigo IN (SELECT pd.PR_Codigo
    	FROM cji_recetadetalle rd
    	INNER JOIN cji_receta r ON r.REC_Codigo = rd.REC_Codigo
    	INNER JOIN cji_producciondetalle pd ON pd.PROD_Codigo = r.PROD_Codigo
    	WHERE pd.PRD_FlagEstado = 1 AND rd.RECDET_FlagEstado = 1 AND pd.PR_Codigo = pr.PR_Codigo GROUP BY pd.PR_Codigo)
    	";
		$this->db->query($update);

		$data = NULL;
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $indice => $columna) {
				$columna->stock = ($columna->stock == NULL) ? 0 : $columna->stock;
				$columna->insumosFaltantes = ($columna->insumosFaltantes == NULL) ? $columna->cantidaInsumos : $columna->insumosFaltantes;
				$data[] = $columna;
			}
		}
		return $data;
	}

	public function detallesReceta($receta)
	{
		$compania = $this->compania;
		$sql = "SELECT rd.*, (SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE rd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as stock
    	FROM cji_recetadetalle rd
    	WHERE rd.RECDET_FlagEstado = '1' AND rd.REC_Codigo = $receta";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return NULL;
	}

	public function insertarReceta($descripcion, $producto)
	{
		$data = array(
			"REC_Codigo" => NULL,
			"REC_Descripcion" => $descripcion,
			"PROD_Codigo" => $producto,
			"REC_FlagEstado" => '1'
		);
		$this->db->insert("cji_receta", $data);
		return $this->db->insert_id();
	}

	public function actualizarReceta($codigo, $descripcion, $producto, $flag = '1')
	{
		$sql = "UPDATE cji_receta
    	SET REC_Descripcion = '$descripcion',
    	PROD_Codigo = $producto,
    	REC_FlagEstado = $flag
    	WHERE REC_Codigo = $codigo
    	";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return true;
		else
			return false;
	}

	public function estadoReceta($codigo, $flag = '0')
	{
		$sql = "UPDATE cji_receta
    	SET REC_FlagEstado = $flag
    	WHERE REC_Codigo = $codigo
    	";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return true;
		else
			return false;
	}

	public function insertarProductoReceta($filter = NULL)
	{
		$this->db->insert('cji_recetadetalle', (array)$filter);
		return $this->db->insert_id();
	}

	public function modificarProductoReceta($recetaDet, $filter = NULL)
	{
		$where = array("RECDET_Codigo" => $recetaDet);
		$this->db->where($where);
		$this->db->update("cji_recetadetalle", (array)$filter);
	}

	#***********************************************
	#******** DESPACHO
	#***********************************************

	public function insertarDespacho($filter = NULL)
	{
		$this->db->insert('cji_despacho', (array)$filter);
		$id = $this->db->insert_id();
		return $id;
	}


	public function actualizarDespacho($codigo, $filter = NULL)
	{
		$where = array("DESP_Codigo" => $codigo);
		$this->db->where($where);
		$this->db->update("cji_despacho", (array)$filter);
	}

	public function insertarDespachoGuias($filter = NULL)
	{
		$this->db->insert('cji_despachodetalle', (array)$filter);
		return $this->db->insert_id();
	}

	public function actualizarDespachoGuias($despachoDet, $filter = NULL)
	{
		$where = array("DESPD_Codigo" => $despachoDet);
		$this->db->where($where);
		$this->db->update("cji_despachodetalle", (array)$filter);
	}

	public function listar_despacho($filter = NULL, $page = NULL, $offset = NULL)
	{
		if ($filter->fechai != NULL)
			$where = " AND d.DESC_FechaDespacho BETWEEN '$filter->fechai 00:00:00' AND '$filter->fechai 23:59:59' ";

		$limit = ($page != NULL && $offset != NULL) ? " LIMIT $offset, $page" : "";

		$sql = "SELECT d.*
    	FROM cji_despacho d
    	WHERE d.DESC_FlagEstado = 1 $where
    	ORDER BY d.DESC_FechaDespacho DESC
    	$limit
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

	public function obtener_despacho($codigo)
	{
		$sql = "SELECT d.* FROM cji_despacho d WHERE d.DESC_FlagEstado = 1 AND d.DESP_Codigo = $codigo";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}

	public function obtener_detalles_despacho($codigo)
	{
		$sql = "SELECT dd.*,
    	gr.GUIAREMC_Serie, gr.GUIAREMC_Serie, gr.GUIAREMC_Numero, gr.GUIAREMC_FechaTraslado, gr.GUIAREMC_PuntoLlegada, gr.GUIAREMC_Observacion, gr.GUIAREMC_UbigeoLlegada,
    	(SELECT e.EESTABC_Descripcion FROM cji_emprestablecimiento e INNER JOIN cji_compania c ON c.EESTABP_Codigo = e.EESTABP_Codigo WHERE gr.COMPP_Codigo IS NOT NULL AND c.COMPP_Codigo = gr.COMPP_Codigo) as emisorGuiaRem,
    	gt.GTRANC_Serie, gt.GTRANC_Numero, gt.GTRANC_Fecha, gt.GTRANC_Observacion, (SELECT a.ALMAC_Direccion FROM cji_almacen a WHERE gt.GTRANC_AlmacenDestino IS NOT NULL AND a.ALMAP_Codigo = gt.GTRANC_AlmacenDestino) as almacenDestino,
    	(SELECT ee.EESTABC_Descripcion FROM cji_emprestablecimiento ee INNER JOIN cji_compania cc ON cc.EESTABP_Codigo = ee.EESTABP_Codigo WHERE gt.COMPP_Codigo IS NOT NULL AND cc.COMPP_Codigo = gt.COMPP_Codigo) as emisorGuiaTrans

    	FROM cji_despachodetalle dd
    	LEFT JOIN cji_guiarem gr ON gr.GUIAREMP_Codigo = dd.GUIAREMP_Codigo AND gr.GUIAREMC_FlagEstado = 1
    	LEFT JOIN cji_guiatrans gt ON gt.GTRANP_Codigo = dd.GTRANP_Codigo AND gt.GTRANC_FlagEstado = 1
    	WHERE dd.DESPD_FlagEstado = 1 AND dd.DESP_Codigo = $codigo
    	ORDER BY gr.GUIAREMC_UbigeoLlegada ASC, almacenDestino
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

	public function calendario_despacho($filter)
	{
		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' AND d.DESC_FechaDespacho BETWEEN "' . $filter->fechai . '" AND "' . $filter->fechaf . '"';

		$sql = "SELECT d.DESP_Codigo, d.DESC_FechaDespacho, COUNT(dc.DESP_Codigo) as cantidad
    	FROM cji_despacho d
    	LEFT JOIN cji_despachodetalle dc ON dc.DESP_Codigo = d.DESP_Codigo
    	WHERE dc.DESP_Codigo = d.DESP_Codigo AND d.DESC_FlagEstado = 1 $where
    	GROUP BY d.DESC_FechaDespacho
    	ORDER BY d.DESC_FechaDespacho DESC
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

	#***********************************************
	#******** PRODUCCION
	#***********************************************

	public function insertarProduccion($filter = NULL)
	{
		$this->db->insert('cji_produccion', (array)$filter);
		$id = $this->db->insert_id();

		if ($filter->PEDIP_Codigo != NULL && $filter->PEDIP_Codigo != "") {
			$sql = "UPDATE cji_pedido SET PEDIC_FlagEstado = $filter->PR_FlagTerminado WHERE PEDIP_Codigo = $filter->PEDIP_Codigo";
			$this->db->query($sql);
		}

		return $id;
	}

	public function actualizarProduccion($codigo, $filter = NULL)
	{
		$where = array("PR_Codigo" => $codigo);
		$this->db->where($where);
		$this->db->update("cji_produccion", (array)$filter);

		if ($filter->PEDIP_Codigo != NULL && $filter->PEDIP_Codigo > 0) {
			$sql = "UPDATE cji_pedido SET PEDIC_FlagEstado = $filter->PR_FlagTerminado WHERE PEDIP_Codigo = $filter->PEDIP_Codigo";
			$this->db->query($sql);
		}
	}

	public function insertarProductoProduccion($filter = NULL)
	{
		$this->db->insert('cji_producciondetalle', (array)$filter);
		return $this->db->insert_id();
	}

	public function modificarProductoProduccion($produccionDet, $filter = NULL)
	{
		$where = array("PRD_Codigo" => $produccionDet);
		$this->db->where($where);
		$this->db->update("cji_producciondetalle", (array)$filter);
	}

	public function listar_produccion($filter = NULL, $number_items = '', $offset = '', $tipo_oper = 'ALL')
	{
		$where = '';
		if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
			$where = ' and pe.PEDIC_FechaRegistro BETWEEN "' . formatDate($filter->fechai, 'db') . '" AND "' . formatDate($filter->fechaf, 'db') . '"';

		if (isset($filter->cliente) && $filter->cliente != '')
			$where .= ' and pe.CLIP_Codigo=' . $filter->cliente;

		$limit = "";

		if ((string) $offset != '' && $number_items != '')
			$limit = 'LIMIT ' . $offset . ',' . $number_items;

		$sql = "SELECT DISTINCT pr.*,
    	(SELECT CONCAT_WS(' ',pe.PEDIC_Serie,pe.PEDIC_Numero) FROM cji_pedido pe WHERE pe.PEDIP_Codigo = pr.PEDIP_Codigo) as pedido, 
    	(SELECT pe.COMPP_Codigo FROM cji_pedido pe WHERE pe.PEDIP_Codigo = pr.PEDIP_Codigo) as establecimiento
    	FROM cji_produccion pr
    	WHERE pr.PR_FlagEstado = 1 $where
    	ORDER BY pr.PR_FlagTerminado DESC
    	$limit
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

	public function obtenerProduccion($filter = NULL)
	{
		$where = '';
		if (isset($filter->codigo) && $filter->codigo != '')
			$where .= " AND pr.PR_Codigo = $filter->codigo";

		$sql = "SELECT pr.*, 
    	(SELECT CONCAT_WS('-',pe.PEDIC_Serie,pe.PEDIC_Numero) FROM cji_pedido pe WHERE pe.PEDIP_Codigo = pr.PEDIP_Codigo) as serieNumeroPedido,
    	(SELECT pe.COMPP_Codigo FROM cji_pedido pe WHERE pe.PEDIP_Codigo = pr.PEDIP_Codigo) as compania
    	FROM cji_produccion pr
    	WHERE pr.PR_FlagEstado = '1' $where";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function detallesProduccion($id)
	{
		$compania = ($this->compania == 2) ? "1" : $this->compania; # ESTO PARA QUE monteverde utilice el mismo almacen que sharks

		$sql = "SELECT pd.*, p.PROD_Nombre, (SELECT SUM(ap.ALMPROD_Stock) FROM cji_almacenproducto ap WHERE pd.PROD_Codigo = ap.PROD_Codigo AND ap.COMPP_Codigo = $compania) as stock
        FROM cji_producciondetalle pd
        LEFT JOIN cji_producto p ON p.PROD_Codigo = pd.PROD_Codigo
        WHERE pd.PRD_FlagEstado = '1' AND pd.PR_Codigo = $id";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		} else
			return NULL;
	}
}
