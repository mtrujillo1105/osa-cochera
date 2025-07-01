<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Proyecto_model extends CI_Model {

	##  -> Begin
	public function __construct(){
		parent::__construct();
	}
	##  -> End

	##  -> Begin
	public function getProyectos($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->proyecto_titulo) && $filter->proyecto_titulo != '')
			$where .= " AND p.PROYC_Nombre LIKE '%$filter->proyecto_titulo%'";

		if (isset($filter->cliente) && $filter->cliente != '')
			$where .= " AND p.CLIP_Codigo = '$filter->cliente'";

		$rec = "SELECT e.EMPRC_Ruc, e.EMPRC_RazonSocial, p.*
							FROM cji_proyecto p
							LEFT JOIN cji_cliente c ON c.CLIP_Codigo = p.CLIP_Codigo
							LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
							WHERE p.PROYC_FlagEstado LIKE '1' AND p.CLIP_Codigo > 0
							$where $order $limit";
		$recF = "SELECT COUNT(*) as registros FROM cji_proyecto p WHERE p.PROYC_FlagEstado LIKE '1' AND p.CLIP_Codigo > 0 $where";
		$recT = "SELECT COUNT(*) as registros FROM cji_proyecto p WHERE p.PROYC_FlagEstado LIKE '1' AND p.CLIP_Codigo > 0";

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
	public function getProyecto($codigo) {
		$sql = "SELECT e.EMPRC_Ruc, e.EMPRC_RazonSocial, p.*
							FROM cji_proyecto p
							LEFT JOIN cji_cliente c ON c.CLIP_Codigo = p.CLIP_Codigo
							LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
							WHERE p.PROYP_Codigo = '$codigo'
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function insertar_proyecto($filter){
		$this->db->insert("cji_proyecto", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_proyecto($proyecto, $filter){
		$this->db->where('PROYP_Codigo',$proyecto);
		return $this->db->update('cji_proyecto', $filter);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_proyecto($proyecto, $filter){
		$this->db->where('PROYP_Codigo',$proyecto);
		$query = $this->db->update('cji_proyecto', $filter);
		return $query;
	}
	##  -> End

	##  -> Begin
	public function getComprobantes($proyecto){
		$sql = "SELECT e.EMPRC_RazonSocial, c.CPP_Codigo, c.CPC_TipoDocumento, c.CPC_Serie, c.CPC_Numero, c.CPC_Total, c.CPC_Fecha, c.CPC_FlagEstado, m.MONED_Simbolo,
									CASE c.CPC_TipoDocumento
										WHEN 'F' THEN 'FACTURA'
										WHEN 'B' THEN 'BOLETA'
										WHEN 'N' THEN 'COMPROBANTE'
										ELSE 'NO DEFINIDO'
									END documento,
									CASE c.CPC_FlagEstado
										WHEN '0' THEN 'ANULADO'
										WHEN '1' THEN 'APROBADO'
										WHEN '2' THEN 'POR APROBAR'
										ELSE 'NO DEFINIDO'
									END as estado
								FROM cji_comprobante c
								INNER JOIN cji_compania co ON co.COMPP_Codigo = c.COMPP_Codigo
								INNER JOIN cji_empresa e ON e.EMPRP_Codigo = co.EMPRP_Codigo
								LEFT JOIN cji_proyecto p ON p.PROYP_Codigo = c.PROYP_Codigo
								LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
								WHERE c.PROYP_Codigo = $proyecto AND p.PROYP_Codigo > 0
						";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

  ## FUNCTIONS DIRECTIONS

	##  -> Begin
  public function getDirections($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->proyecto) && $filter->proyecto != '')
			$where .= " AND p.PROYP_Codigo = '$filter->proyecto'";

		$sql = "SELECT d.*, u.UBIGP_Codigo, u.UBIGC_Descripcion as dist, u.UBIGC_DescripcionProv as prov, u.UBIGC_DescripcionDpto as dpto,
							p.PROYC_Nombre, p.PROYC_Descripcion, p.PROYC_FechaInicio, p.PROYC_FechaFin
							FROM cji_direccion d
							INNER JOIN cji_proyecto p ON p.PROYP_Codigo = d.PROYP_Codigo
							LEFT JOIN cji_ubigeo u ON u.UBIGP_Codigo = d.UBIGP_Domicilio
							WHERE p.PROYC_FlagEstado LIKE '1' AND d.DIRECC_FlagEstado LIKE '1'
							$where $order $limit";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getDirection($codigo) {
		$sql = "SELECT d.*
							FROM cji_direccion d
							WHERE d.DIRECC_Codigo = '$codigo'
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function saveDirection($filter){
		$this->db->insert("cji_direccion", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function updateDirection($direccion, $filter){
      $this->db->where('DIRECC_Codigo',$direccion);
      return $this->db->update('cji_direccion', $filter);
  }
  ##  -> End


  ## FUNCTIONS OLDS

	public function listar_proyectos(){
		$where = array("PROYC_FlagEstado"=>1);
		$query = $this->db->order_by('PROYC_Nombre')
		->where($where)
		->select('PROYP_Codigo,PROYC_Nombre,PROYC_Descripcion,DIREP_Codigo')
		->from('cji_proyecto')
		->get();
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_datosProyecto($proyecto){
		$query = $this->db->where('PROYP_Codigo',$proyecto)->get('cji_proyecto');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
	public function obtener_NAMEProyecto($proyecto){
		$query = $this->db->where('PROYP_Codigo',$proyecto)->get('cji_proyecto');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}


	public function obtener_direccion($proyecto){
		$query = $this->db->where('PROYP_Codigo',$proyecto)->get('cji_direccion');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscarproyecto_cliente($proyecto){
		$query = $this->db->where('EMPRP_Codigo',$proyecto)->get('cji_proyecto');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}

	}

	public function buscar_proyectos($filter,$number_items='',$offset='')
	{       
		if(isset($filter->PROYC_Nombre) && $filter->PROYC_Nombre!=""){
			$this->db->like('PROYC_Nombre',$filter->PROYC_Nombre);          
		}
		$query = $this->db->order_by('PROYC_Nombre')
		->where('PROYC_FlagEstado','1')
		->get('cji_proyecto',$number_items='',$offset='');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_detalle($proyecto)
	{
		$where = array("PROYP_Codigo"=>$proyecto , "DIRECC_FlagEstado" => '1' );
		$query = $this->db->order_by('PROYP_Codigo')->where($where)->get('cji_direccion');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_usuario_terminal($usu){
		$query = $this->db->where('USUA_Codigo',$usu)->get('cji_usuario_terminal');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_terminal($terminal){
		$query = $this->db->where('TERMINAL_Codigo',$terminal)->get('cji_terminal');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_direccion_proyecto($direccion){
		$query = $this->db->where('DIRECC_Codigo',$direccion)->get('cji_direccion');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_detalle_terminal($direccionCodigo,$total="",$inicio="")
	{
		$where = array("DIRECC_Codigo"=>$direccionCodigo , "TERMINAL_FlagEstado" => '1' );
		$query = $this->db->order_by('DIRECC_Codigo')->where($where)->get('cji_terminal',$total='',$inicio='');
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}

	}

	public function eliminar_direccion($valor)
	{
		$data  = array("DIRECC_FlagEstado"=>'0');
		$where = array("DIRECC_Codigo"=>$valor);
		$this->db->where($where);
		$this->db->update('cji_direccion',$data);
	}

	public function modificar_direccion($valor ,$filter)
	{
		$where = array("DIRECC_Codigo"=>$valor);
		$this->db->where($where);
		$this->db->update('cji_direccion',(array)$filter);
	}



	public function seleccionar($codigoproyecto)
	{

		$listado    = $this->obtener_datosProyecto($codigoproyecto);
		if(count($listado) > 0){
			foreach($listado as $indice=>$valor){
				$indice1   = $valor->PROYP_Codigo;
				$valor1    = $valor->PROYC_Nombre;
				$arreglo[$indice1] = $valor1;
			}
		}
		return $arreglo;

	}

	public function listar_personas($contacto){

		$sql = "select contacto.ECONC_Persona,PERSC_Nombre from cji_emprcontacto contacto inner JOIN cji_persona persona
		on contacto.ECONC_Persona = persona.PERSP_Codigo where contacto.ECONC_Persona = $contacto";
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function seleccionarcontacto($contacto)
	{

		$listado    = $this->listar_personas($contacto);
		$arreglo='';

		if(count($listado) > 0){
			foreach($listado as $indice=>$valor){
				$indice1   = $valor->ECONC_Persona;
				$valor1    = $valor->PERSC_Nombre;
				$arreglo[$indice1] = $valor1;
			}
		}
		return $arreglo;

	}

	public function obtenerContacto($filter){
		$sql = "select cliente.EMPRP_Codigo,PROYC_Nombre,PROYP_Codigo,EMPRC_RazonSocial,EMPRC_Ruc from cji_proyecto obra 
		inner join cji_cliente cliente on cliente.CLIP_Codigo = obra.EMPRP_Codigo 
		inner join cji_empresa empresa on cliente.EMPRP_Codigo = empresa.EMPRP_Codigo where PROYP_Codigo = $filter ";
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_comprobantesxproyecto($idproyecto){

		$sql= "SELECT c.*,e.* FROM cji_comprobante c
		INNER JOIN cji_cliente cl ON cl.CLIP_Codigo = c.CLIP_Codigo
		INNER JOIN cji_empresa e ON e.EMPRP_Codigo = cl.EMPRP_Codigo WHERE c.PROYP_Codigo = $idproyecto";
		$query  = $this->db->query($sql);
		if ($query->num_rows()>0) {
			return $query->result();
		}


	}

}

?>