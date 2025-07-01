<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Lote_Model extends CI_Model{
	protected $_name = "cji_lote";
	##  -> Begin
	private $compania;
	##  -> End
	
	public function  __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}

	public function seleccionar($default=""){
		$nombre_defecto = $default==""?":: Seleccione ::":$default;
		$arreglo = array(''=>$nombre_defecto);
		foreach($this->listar() as $indice=>$valor)
		{
			$indice1   = $valor->LOTP_Codigo;
			$valor1    = substr($valor->LOTC_FechaRegistro, 0,10);
			$arreglo[$indice1] = $valor1;
		}
		return $arreglo;
	}

	public function listar($prodcuto){
		$where = array('LOTC_FlagEstado'=>'1', 'PROD_Codigo'=>$prodcuto);
		$query = $this->db->order_by('LOTC_FechaRegistro', 'DESC')->join('cji_guiain g', 'g.GUIAINP_Codigo=l.GUIAINP_Codigo')->select('l.*, g.PROVP_Codigo, g.ALMAP_Codigo, g.GUIAINC_Fecha')->where($where)->get('cji_lote l');
		if($query->num_rows() > 0){
			return $query->result();
		}
		else
			return array();
	}

	public function detalles($producto, $almacen, $tipo_oper = "V", $idLote = NULL){
		$compania = $this->compania;

		$whereLote = ($idLote != NULL && $idLote != "") ? " OR l.LOTP_Codigo = $idLote" : "";

		$where = ($tipo_oper == "V") ? " apl.ALMALOTC_CantidadDisponible > 0 AND l.PROD_Codigo = $producto" : " l.LOTC_FlagEstado = 2 AND l.PROD_Codigo = $producto";

		$sql = "SELECT l.LOTP_Codigo, l.LOTC_Cantidad, l.LOTC_Numero, l.LOTC_FechaVencimiento, l.LOTC_FlagEstado, apl.ALMALOTP_Codigo, apl.ALMALOTC_Cantidad, ap.ALMAP_Codigo, ap.PROD_Codigo, ap.COMPP_Codigo, ap.ALMPROD_Stock
		FROM cji_lote l
		LEFT JOIN cji_almaprolote apl ON apl.LOTP_Codigo = l.LOTP_Codigo
		LEFT JOIN cji_almacenproducto ap ON ap.ALMPROD_Codigo = apl.ALMPROD_Codigo AND ap.COMPP_Codigo = $compania AND ap.ALMAP_Codigo = $almacen
		WHERE $where $whereLote
		ORDER BY l.LOTC_FechaVencimiento ASC
		";
		$query = $this->db->query($sql);
		$data = array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $key => $fila) {
				$data[] = $fila;
			}
		}
		return $data;
	}

	public function obtener_lote($lote){
		$sql = "SELECT l.LOTP_Codigo, l.LOTC_Cantidad, l.LOTC_Costo, l.LOTC_Numero, l.LOTC_FechaVencimiento, l.PROD_Codigo,
		(SELECT CONCAT_WS(' - ', p.PROD_Nombre, m.MARCC_Descripcion)
		FROM cji_producto p
		LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
		WHERE p.PROD_Codigo = l.PROD_Codigo) as descripcion_producto
		FROM cji_lote l
		WHERE l.LOTP_Codigo = $lote
		";
		$query = $this->db->query($sql);
		$data = array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $key => $fila) {
				$data[] = $fila;
			}
		}
		return $data;
	}

	public function buscar($producto, $proveedor, $fechaIni, $fechaFin){
		$where = array('LOTC_FlagEstado'=>'1', 'PROD_Codigo'=>$producto);
		if($proveedor!='')
			$where['g.PROVP_Codigo']=$proveedor;
		if($fechaIni!='' && $fechaFin!=''){
			$where['g.GUIAINC_Fecha >=']=$fechaIni;
			$where['g.GUIAINC_Fecha <=']=$fechaFin;
		}
		$query = $this->db->order_by('LOTC_FechaRegistro', 'DESC')->join('cji_guiain g', 'g.GUIAINP_Codigo=l.GUIAINP_Codigo')->select('l.*, g.PROVP_Codigo, g.ALMAP_Codigo, g.GUIAINC_Fecha')->where($where)->get('cji_lote l');
		if($query->num_rows() > 0){
			return $query->result();
		}
		else
			return array();
	}

	public function listar_lotes_recientes($producto, $dias_atras, $cantidad=''){
		$where = array('l.LOTC_FlagEstado'=>'1', 'PROD_Codigo'=>$producto);
		$cantidad = $cantidad==='' ? $cantidad : 0;

		$query = $this->db->order_by('LOTC_FechaRegistro')
		->join('cji_guiain g', 'g.GUIAINP_Codigo=l.GUIAINP_Codigo')
		->select('l.*, g.PROVP_Codigo, g.ALMAP_Codigo, g.GUIAINC_Fecha')
		->where($where)
		->where('g.GUIAINC_Fecha>DATE_ADD(CURDATE(), INTERVAL -'.$dias_atras.' DAY)')
		->get('cji_lote l');
		if($query->num_rows() > 0){
			$result=$query->result();
			foreach($result as $indice=>$valor){
				if($valor->LOTC_Cantidad<=$cantidad)
					unset($result[$indice]);
			}
			return $result;
		}
		else
			return array();
	}
	public function listar_lotes_recientes_ultimos($fecha_ini, $cant_minima)
	{   $where = array('l.LOTC_FlagEstado'=>'1', 'g.GUIAINC_Fecha >='=>$fecha_ini, 'l.LOTC_Cantidad >='=>$cant_minima);

	$query = $this->db->order_by('p.PROD_Nombre')
	->join('cji_guiain g', 'g.GUIAINP_Codigo=l.GUIAINP_Codigo')
	->join('cji_producto p', 'p.PROD_Codigo=l.PROD_Codigo')
	->select('p.PROD_Codigo, p.PROD_CodigoInterno, p.PROD_Nombre, p.PROD_UltimoCosto')
	->where($where)
	->group_by('p.PROD_Codigo, p.PROD_CodigoInterno, p.PROD_Nombre, p.PROD_UltimoCosto')  
	->get('cji_lote l');
	if($query->num_rows() > 0){
		return $query->result();
	}
	else
		return array();
}

public function obtener($id){
	$where = array("LOTP_Codigo"=>$id);
	$query = $this->db->where($where)->get('cji_lote');
	if($query->num_rows() > 0){
		return $query->row();
	}
}

public function obtener2($codprod){
	$where = array("PROD_Codigo"=>$codprod);
	$query = $this->db->where($where)->get('cji_lote');
	if($query->num_rows() > 0){
		foreach($query ->result() as $fila){
			$data[]=$fila;
		}

		return $data;
	}
}

public function insertar(stdClass $filter = null){
	$this->db->insert("cji_lote",(array)$filter);
	return $this->db->insert_id();
}

public function modificar($id,$filter){
	$this->db->where("LOTP_Codigo",$id);
	return $this->db->update("cji_lote",(array)$filter);
}

public function eliminar($id){
	$this->db->where("LOTP_Codigo",$id);
	$this->db->delete('cji_lote',array('LOTC_Cantidad' =>0));
}
	//busqueda
public function obtener_x_guia($prod_codigo,$guia){
	$where = array('LOTC_FlagEstado'=>'1', 'PROD_Codigo'=>$prod_codigo,'GUIAINP_Codigo'=>$guia);
	$query = $this->db->where($where)->get('cji_lote');
	if($query->num_rows() > 0){
		return $query->result();
	}
	else
		return array();
}

public function lista_lotes($filter = null, $number_items='',$offset=''){
	$where = '';
	if ( isset($filter->codigo) && $filter->codigo != '')
		$where .= " AND l.PROD_Codigo = $filter->codigo";

	if ( isset($filter->nombre) && $filter->nombre != '')
		$where .= " AND l.LOTC_Numero LIKE '%$filter->nombre%'";

	$limit = "";
	if((string)$offset != '' && $number_items != '')
		$limit = 'LIMIT '.$offset.','.$number_items;

	$sql = "SELECT l.*, p.PROD_Codigo, p.PROD_Nombre, p.PROD_Modelo, m.MARCC_Descripcion
	FROM cji_lote l
	INNER JOIN cji_producto p ON p.PROD_Codigo = l.PROD_Codigo
	LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
	WHERE l.LOTC_FlagEstado <> 0
	$where
	ORDER BY l.PROD_Codigo, l.LOTC_FechaVencimiento ASC
	$limit
	";

	$query = $this->db->query($sql);
	$data = NULL;
	if ($query->num_rows() > 0){
		foreach ($query->result() as $value) {
			$data[] = $value;
		}
	}
	return $data;
}
    }
    ?>