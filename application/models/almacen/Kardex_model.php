<?php
/* *********************************************************************************
/* ******************************************************************************** */
class kardex_Model extends CI_Model
{

	##  -> Begin
	private $compania;
	##  -> End

	public function __construct()
	{
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}

	############################################################
	# function: obtiene_movimeintos_kardex
	# description: obtiene movimeintos de productos en tablas 
	#              transaccionales   
	############################################################
	public function obtiene_movimeintos_kardex($filter = NULL)
	{
		$compania = $this->compania;
		$limit 		= (isset($filter->start) && isset($filter->length)) ? " LIMIT $filter->start, $filter->length " : "";

		$where      = '';
		$where_2    = '';
		$where_3    = '';
		##  -> Begin
		$where_4    = '';
		##  -> End

		if (isset($filter->producto) && $filter->producto != '') {

			$where 		.= " AND cd.PROD_Codigo = $filter->producto";
			$where_2 	.= " AND gd.PROD_Codigo = $filter->producto";
			$where_3 	.= " AND id.PROD_Codigo = $filter->producto";
			$where_4    .= " AND nd.PROD_Codigo = $filter->producto";
		}

		if (isset($filter->almacen) && $filter->almacen != '') {

			$where 		.= " AND cd.ALMAP_Codigo = $filter->almacen";
			$where_2 	.= " AND (g.GTRANC_AlmacenOrigen = $filter->almacen OR g.GTRANC_AlmacenDestino = $filter->almacen)";
			$where_3 	.= " AND i.ALMAP_Codigo = $filter->almacen";
		}

		if (isset($filter->fechai) && $filter->fechai != '') {
			$fechaf 	 = (isset($filter->fechaf) && $filter->fechaf != '') ? $filter->fechaf : date("Y-m-d");
			$where 		.= " AND c.CPC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$fechaf 23:59:59'";
			$where_2 	.= " AND g.GTRANC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$fechaf 23:59:59'";
			$where_3 	.= " AND id.INVD_FechaRegistro BETWEEN '$filter->fechai 00:00:00' AND '$fechaf 23:59:59'";
			$where_4    .= " AND n.CRED_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$fechaf 23:59:59'";
		}


		$sql = "DROP TABLE IF EXISTS kardex";
		$query = $this->db->query($sql);

		$sql_comprobantes = "CREATE TEMPORARY TABLE kardex
            SELECT 
            cd.ALMAP_Codigo         AS almacen,
            c.CPC_Fecha             AS fecha, 
            c.CPP_Codigo            AS codigo_docu, 
            cd.PROD_Codigo          AS codigo, 
            cd.CPDEC_Cantidad       AS cantidad, 
            c.CPC_Numero            AS numero, 
            c.CPC_Serie             AS serie, 
            al.ALMAC_Descripcion    AS nombre_almacen,
            cd.CPDEC_Total          AS total, 
            cd.CPDEC_Pu_ConIgv      AS pu_conIgv, 
            cd.CPDEC_Subtotal       AS subtotal, 
            c.CPC_TipoOperacion     AS tipo_oper,
            p.PROD_UltimoCosto      AS costo,
            c.CPC_FlagEstado        AS estado,
            (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
            FROM cji_cliente cc
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
            LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
            WHERE cc.CLIP_Codigo = c.CLIP_Codigo
            ) as razon_social_cliente,
            (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
            FROM cji_proveedor pp
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
            LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
            WHERE pp.PROVP_Codigo = c.PROVP_Codigo
            ) as razon_social_proveedor

            FROM cji_comprobantedetalle cd 
            LEFT JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
            LEFT JOIN cji_almacen al ON cd.ALMAP_Codigo = al.ALMAP_Codigo
            LEFT JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
            WHERE cd.CPDEC_FlagEstado!=0 and c.CPC_FlagEstado=1 AND c.COMPP_Codigo = $compania $where 
            
            UNION
            SELECT 
            g.GTRANC_AlmacenOrigen  AS almacen, 
            g.GTRANC_Fecha          AS fecha, 
            g.GTRANP_Codigo         AS codigo_docu, 
            gd.PROD_Codigo          AS codigo, 
            gd.GTRANDETC_Cantidad   AS cantidad, 
            g.GTRANC_Numero         AS numero, 
            g.GTRANC_Serie          AS serie, 
            al.ALMAC_Descripcion    AS nombre_almacen,
            NULL                    AS total, 
            NULL                    AS pu_conIgv, 
            NULL                    AS subtotal, 
            'T'                     AS tipo_oper,
            NULL                    AS costo,
            g.GTRANC_EstadoTrans    AS estado,
            NULL AS razon_social_cliente,
            NULL AS razon_social_proveedor
            FROM cji_guiatransdetalle gd 
            LEFT JOIN cji_guiatrans g ON gd.GTRANP_Codigo = g.GTRANP_Codigo
            LEFT JOIN cji_almacen al ON g.GTRANC_AlmacenOrigen  = al.ALMAP_Codigo
            WHERE gd.GTRANDETC_FlagEstado!=0  AND g.GTRANC_FlagEstado!=0 AND g.GTRANC_EstadoTrans!=0  $where_2
            
            UNION
            SELECT 
            i.ALMAP_Codigo          AS almacen, 
            id.INVD_FechaRegistro   AS fecha, 
            null                    AS codigo_docu, 
            id.PROD_Codigo          AS codigo, 
            id.INVD_Cantidad        AS cantidad, 
            NULL                    AS numero, 
            NULL                    AS serie, 
            al.ALMAC_Descripcion    AS nombre_almacen,
            NULL                    AS total, 
            NULL                    AS pu_conIgv, 
            NULL                    AS subtotal, 
            'I'                     AS tipo_oper,
            NULL                    AS costo,
            NULL                    AS estado,
            NULL AS razon_social_cliente,
            NULL AS razon_social_proveedor
            FROM cji_inventariodetalle id 
            LEFT JOIN cji_inventario i ON id.INVE_Codigo = i.INVE_Codigo
            LEFT JOIN cji_almacen al ON i.ALMAP_Codigo  = al.ALMAP_Codigo
            WHERE id.INVD_FlagActivacion!=0 AND i.COMPP_Codigo = $compania $where_3

            ";
		/*UNION
            SELECT 
            nd.ALMAP_Codigo          AS almacen, 
            n.CRED_Fecha            AS fecha, 
            n.CRED_Codigo           AS codigo_docu, 
            nd.PROD_Codigo          AS codigo, 
            nd.CREDET_Cantidad      AS cantidad, 
            n.CRED_numero           AS numero, 
            n.CRED_Serie            AS serie, 
            al.ALMAC_Descripcion    AS nombre_almacen,
            nd.CREDET_Total         AS total, 
            NULL                    AS pu_conIgv, 
            nd.CREDET_Subtotal      AS subtotal, 
            'N'                     AS tipo_oper,
            NULL                    AS costo,
            CRED_FlagEstado         AS estado,
           (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
            FROM cji_cliente cc
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
            LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
            WHERE cc.CLIP_Codigo = n.CLIP_Codigo
            ) as razon_social_cliente,
            (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
            FROM cji_proveedor pp
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
            LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
            WHERE pp.PROVP_Codigo = n.PROVP_Codigo
            ) as razon_social_proveedor
            FROM cji_notadetalle nd 
            LEFT JOIN cji_nota n ON nd.CRED_Codigo = n.CRED_Codigo
            LEFT JOIN cji_almacen al ON nd.ALMAP_Codigo  = al.ALMAP_Codigo
            WHERE nd.CREDET_FlagEstado!=0 AND n.COMPP_Codigo = $compania $where_4*/


		$query_comprobantes = $this->db->query($sql_comprobantes);

		$sql_transferencias = "SELECT * FROM kardex order by fecha desc";

		$query_transferencias = $this->db->query($sql_transferencias);

		$data = array();



		if ($query_transferencias->num_rows > 0) {
			foreach ($query_transferencias->result() as $fila) {
				$data[] = $fila;
			}
		}

		if ($data) {
			return $data;
		} else {
			return array();
		}
	}

    public function obtener_registros_x_dcto($producto_id, $documento_id, $codigo_doc) {

        $where = array("COMPP_Codigo" => $this->compania, "PROD_Codigo" => $producto_id, "DOCUP_Codigo" => $documento_id, "KARDC_CodigoDoc" => $codigo_doc);

        $query = $this->db->where($where)->get('cji_kardex');

        if ($query->num_rows > 0) {

            return $query->result();
        }
    }        
        
	public function getMovimientos($filter = NULL)
	{
		$limit = (isset($filter->start) && isset($filter->length)) ? " LIMIT $filter->start, $filter->length " : "";
		$order = (isset($filter->order) && isset($filter->dir)) ? "ORDER BY $filter->order $filter->dir " : "";
		$where = "";

		if (isset($filter->producto) && trim($filter->producto) != "")
			$where .= " AND PROD_Codigo = '$filter->producto' ";

		if (isset($filter->almacen) && trim($filter->almacen) != "")
			$where .= " AND ALMAP_Codigo = '$filter->almacen' ";

		$comprobantes = "SELECT c.CPC_FechaModificacion as fecha_movimiento,
													CASE c.CPC_TipoOperacion
														WHEN 'C' THEN 'INGRESO'
														WHEN 'V' THEN 'SALIDA'
														ELSE ''
													END as tipo_movimiento,
													a.ALMAC_Descripcion,
													doc.DOCUC_Descripcion as documento,
													CONCAT_WS('-',c.CPC_Serie,c.CPC_Numero) as numero,
													cd.CPDEC_Cantidad as cantidad,
													CASE c.CPC_TipoOperacion
														WHEN 'C' THEN cd.CPDEC_Pu_ConIgv
														WHEN 'V' THEN cd.CPDEC_Costo
														ELSE ''
													END as costo
													FROM cji_comprobantedetalle cd
													INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
													INNER JOIN cji_almacen a ON a.ALMAP_Codigo = cd.ALMAP_Codigo
													INNER JOIN cji_documento doc ON doc.DOCUC_Inicial = c.CPC_TipoDocumento
													WHERE c.CPC_FlagEstado IN(0,1) AND c.CPC_FlagMueveStock LIKE '1' AND cd.CPDEC_FlagEstado LIKE '1'
													$where
													";

		$guias_remision = "SELECT gr.GUIAREMC_FechaModificacion as fecha_movimiento,
													CASE gr.GUIAREMC_TipoOperacion
														WHEN 'C' THEN 'INGRESO'
														WHEN 'V' THEN 'SALIDA'
														ELSE ''
													END as tipo_movimiento,
													a.ALMAC_Descripcion,
													doc.DOCUC_Descripcion as documento,
													CONCAT_WS('-',gr.GUIAREMC_Serie,gr.GUIAREMC_Numero) as numero,
													grd.GUIAREMDETC_Cantidad as cantidad,
													CASE gr.CPC_TipoOperacion
														WHEN 'C' THEN grd.GUIAREMDETC_Pu_ConIgv
														WHEN 'V' THEN grd.GUIAREMDETC_Costo
														ELSE ''
													END as costo
													FROM cji_guiaremdetalle grd
													INNER JOIN cji_guiarem gr ON gr.GUIAREMP_Codigo = grd.GUIAREMP_Codigo
													INNER JOIN cji_almacen a ON a.ALMAP_Codigo = grd.ALMAP_Codigo
													INNER JOIN cji_documento doc ON doc.DOCUP_Codigo = gr.DOCUP_Codigo
													WHERE gr.GUIAREMC_FlagEstado IN(0,1) AND gr.GUIAREMC_FlagMueveStock LIKE '1' AND grd.GUIAREMDETC_FlagEstado LIKE '1'
													$where
													";

		$notas_credito = "SELECT n.CRED_FechaModificacion as fecha_movimiento,
													CASE n.CRED_TipoOperacion
														WHEN 'V' THEN 'INGRESO'
														WHEN 'C' THEN 'SALIDA'
														ELSE ''
													END as tipo_movimiento,
													a.ALMAC_Descripcion,
													doc.DOCUC_Descripcion as documento,
													CONCAT_WS('-',n.CRED_Serie,n.CRED_Numero) as numero,
													nd.CREDET_Cantidad as cantidad,
													CASE n.CRED_TipoOperacion
														WHEN 'C' THEN nd.CREDET_Pu_ConIgv
														WHEN 'V' THEN nd.CREDET_Costo
														ELSE ''
													END as costo
													FROM cji_notadetalle nd
													INNER JOIN cji_nota n ON n.CRED_Codigo = nd.CRED_Codigo
													INNER JOIN cji_almacen a ON a.ALMAP_Codigo = nd.ALMAP_Codigo
													INNER JOIN cji_documento doc ON doc.DOCUP_Codigo = n.DOCUP_Codigo
													WHERE n.CRED_FlagEstado IN(0,1) AND n.CRED_TipoNota LIKE 'C' AND nd.CREDET_FlagEstado LIKE '1'
													$where
													";

		$inventario = "SELECT inv.INVD_FechaModificacion as fecha_movimiento,
													'INGRESO' as tipo_movimiento,
													a.ALMAC_Descripcion,
													doc.DOCUC_Descripcion as documento,
													CONCAT_WS('-',inv.INVE_Serie,inv.INVE_Numero) as numero,
													inv.INVD_Cantidad as cantidad,
													invd.INVD_Pcosto as costo
													FROM cji_inventariodetalle invd
													INNER JOIN cji_inventario inv ON inv.INVE_Codigo = invd.INVE_Codigo
													INNER JOIN cji_almacen a ON a.ALMAP_Codigo = inv.ALMAP_Codigo
													INNER JOIN cji_documento doc ON doc.DOCUP_Codigo = inv.DOCUP_Codigo
													WHERE inv.INVE_FlagEstado IN(1) AND invd.INVD_FlagActivacion LIKE '1'
													$where
													";

		$sql = "CREATE TEMPORARY TABLE kardex as $comprobantes UNION $guias_remision UNION $notas_credito UNION $inventario";

		$rec = $sql." SELECT * FROM kardex $order $limit";
		
		echo $rec;
		exit;
		$recF = "";
		$recT = "";

		$records = $this->db->query($rec);
		$recordsFilter = $this->db->query($recF)->row()->registros;
		$recordsTotal = $this->db->query($recT)->row()->registros;

		if ($records->num_rows() > 0) {
			$info = array(
				"records" => $records->result(),
				"recordsFilter" => $recordsFilter,
				"recordsTotal" => $recordsTotal
			);
		} else {
			$info = array(
				"records" => NULL,
				"recordsFilter" => 0,
				"recordsTotal" => $recordsTotal
			);
		}
		return $info;
	}
}
