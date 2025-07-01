<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiain_Model extends CI_Model{
	protected $_name = "cji_guiain";
	##  -> Begin
	private $compania;
	private $usuario;
	##  -> End

	##  -> Begin
	public function  __construct(){
		parent::__construct();
		$this->load->model('maestros/configuracion_model');
		$this->compania = $this->session->userdata('compania');
		$this->usuario  = $this->session->userdata('user');
	}
	##  -> End
	
	public function listar($number_items='',$offset='')
	{
		$where = array("GUIAINC_FlagEstado"=>1);
		$query = $this->db->order_by('GUIAINP_Codigo','desc')->where($where)->get('cji_guiain',$number_items,$offset);
		if($query->num_rows() > 0){
			return $query->result();
		}
	}

	public function buscar_guian($filter, $number_items='',$offset='')
	{
		$compania = $this->compania;
		$data_confi           = $this->companiaconfiguracion_model->obtener($compania);
		$data_confi_docu      = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
		
		$where='';
		
		if(isset($filter->fechai) && $filter->fechai!='' && isset($filter->fechaf) && $filter->fechaf!='')
			$where=' and CPC_Fecha BETWEEN "'.human_to_mysql($filter->fechai).'" AND "'.human_to_mysql($filter->fechaf).'"';
		if(isset($filter->cliente) && $filter->cliente!='')
			$where.=' and cji_comprobante.CLIP_Codigo='.$filter->cliente;
		if(isset($filter->numero) && $filter->numero!='')
			$where.=' and CPC_Numero='.$filter->numero;
		if(isset($filter->situacion) && $filter->situacion!='')
			$where.=' and CPC_FlagEstado='.$filter->situacion;
		if(isset($filter->cotizacion) && $filter->cotizacion!='')
			$where.=' and COTIC_Numero='.$filter->cotizacion;
		if(isset($filter->pedido) && $filter->pedido!='')
			$where.=' and PEDIC_Numero='.$filter->pedido;
		
		
		$limit="";
		if((string)$offset!='' && $number_items!='')
			$limit = 'LIMIT '.$offset.','.$number_items;

		
		$sql = "SELECT CPP_Codigo, CPC_TipoOperacion, GUIAINP_Codigo, OCOMP_Codigo, CPC_Fecha, CPC_Numero, OCOMP_Codigo, ALMAC_Descripcion, EMPRC_RazonSocial, CPC_FlagEstado
		FROM cji_comprobante
		LEFT JOIN cji_cliente
		USING ( CLIP_Codigo ) 
		LEFT JOIN cji_empresa ON cji_empresa.EMPRP_Codigo = cji_cliente.EMPRP_Codigo
		LEFT JOIN cji_ordencompra
		USING ( OCOMP_Codigo ) 
		LEFT JOIN cji_almacen ON cji_ordencompra.ALMAP_Codigo = cji_almacen.ALMAP_Codigo
		WHERE CPC_TipoOperacion =  'c'AND cji_comprobante.COMPP_Codigo =".$compania." ".$where."
		ORDER BY CPP_Codigo DESC ".$limit."
		";
		$query = $this->db->query($sql);        
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
		return array();
	}

	public function obtener($id)
	{
		$where = array("GUIAINP_Codigo"=>$id);
		$query = $this->db->where($where)->get('cji_guiain',1);
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();

	}
	public function insertar(stdClass $filter = null)
	{
		$datos_configuracion  = $this->configuracion_model->obtener_numero_documento($this->compania,'5');
		$numero = $datos_configuracion[0]->CONFIC_Numero + 1;
		$filter->GUIAINC_Numero = $numero;
		$this->db->insert("cji_guiain",(array)$filter);
		$guiain = $this->db->insert_id();
		if($guiain!=0) $this->configuracion_model->modificar_configuracion($this->compania,'5',$numero);
		return $guiain;
	}
	public function modificar($id,$filter)
	{
		$this->db->where("GUIAINP_Codigo",$id);
		$this->db->update("cji_guiain",(array)$filter);
	}
	public function eliminar($id)
	{
		$this->db->delete('cji_guiain', array('GUIAINP_Codigo' => $id));
	}
}
?>
