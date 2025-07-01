<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Almacenproductoserie_Model extends CI_Model{

	##  -> Begin
	public function  __construct(){
		parent::__construct();
	}
	##  -> End

	public function seleccionar($almacenproducto_id,$default="")
	{
		$nombre_defecto = $default==""?":: Seleccione ::":$default;
		$lista=$this->listar($almacenproducto_id);
		$arreglo = array(''=>$nombre_defecto);
		if(count($lista)>0){
			foreach($lista as $indice=>$valor)
			{
				$indice1   = $valor->SERIP_Codigo;
				$valor1    = $valor->SERIC_Numero;
				$arreglo[$indice1] = $valor1;
			}
		}
		return $arreglo;
	}

	public function listar($almacenproducto_id)
	{
		$this->db->select('cji_almacenproductoserie.*, cji_serie.*');
		$this->db->from('cji_almacenproductoserie');
		$this->db->join('cji_serie','cji_serie.SERIP_Codigo=cji_almacenproductoserie.SERIP_Codigo');
		$this->db->where('cji_almacenproductoserie.ALMPROD_Codigo',$almacenproducto_id);
		$query = $this->db->get();
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();

	}

	public function listar_x_serie($almacenproducto_id, $serie)
	{
		$this->db->select('cji_almacenproductoserie.*');
		$this->db->from('cji_almacenproductoserie');
		$this->db->join('cji_serie','cji_serie.SERIP_Codigo=cji_almacenproductoserie.SERIP_Codigo');
		$this->db->where('cji_almacenproductoserie.ALMPROD_Codigo',$almacenproducto_id);
		$this->db->like('cji_serie.SERIC_Numero',$serie, 'both');
		$query = $this->db->get();
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();

	}

	public function obtener($almacenproducto_id,$serie)
	{
		$where = array("ALMPROD_Codigo"=>$almacenproducto_id,"SERIP_Codigo"=>$serie);
		$query = $this->db->where($where)->get('cji_almacenproductoserie');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function insertar($almacenproducto_id,$serie)
	{
		$data = array("ALMPROD_Codigo"=>$almacenproducto_id,"SERIP_Codigo"=>$serie,"ALMPRODSERC_Seleccion"=>0,"ALMPRODSERC_FlagEstado"=>1);
		$this->db->insert("cji_almacenproductoserie",$data);
	}
	public function modificar($id,$filter)
	{
		$this->db->where("ALMPRODSERP_Codigo",$id);
		$this->db->update("cji_almacenproductoserie",(array)$filter);
	}
	public function eliminar($id)
	{
		$this->db->delete('cji_almacenproductoserie',array('ALMPROD_Codigo' => $id));
	}
	public function eliminar2($id)
	{
		$this->db->delete('cji_almacenproductoserie',array('SERIP_Codigo' => $id));
	}
	public function buscar($filter,$number_items='',$offset='')
	{
	}
	public function buscar_x_serie($serie)
	{
		$where = array("SERIP_Codigo"=>$serie);
		$query = $this->db->where($where)->get('cji_almacenproductoserie');
		if($query->num_rows() > 0)
			return $query->result();
		else
			return array();
	}

	/**gcbq buscar series no seleccionadas3**/
	public function buscarNoseleccionados($codigoAlmacenProducto,$numeroSerie=''){
		if($codigoAlmacenProducto!=null ){
			$this->db->select('cji_almacenproductoserie.ALMPRODSERP_Codigo codigoAlmacenProductoSerie, 
				cji_almacenproductoserie.SERIP_Codigo CodigoSerie,
				cji_almacenproductoserie.ALMPRODSERC_FechaRegistro fechaRegistro,
				cji_serie.SERIC_Numero  numero
				');
			$this->db->join('cji_serie','cji_serie.SERIP_Codigo=cji_almacenproductoserie.SERIP_Codigo');
			$this->db->where('cji_almacenproductoserie.ALMPROD_Codigo',$codigoAlmacenProducto);
			$this->db->where('cji_almacenproductoserie.ALMPRODSERC_Seleccion!=',1,false);
			if(trim($numeroSerie)!=''){
				$this->db->like('UPPER(cji_serie.SERIC_Numero)', strtoupper($numeroSerie));
			}

			$query = $this->db->get('cji_almacenproductoserie');    	
			if($query->num_rows() > 0)
				return $query->result();
			else
				return array();

		}else{
			return array();
		}
	}

	/**GUARDAMOS  LA SERIE POR BD 1:SELECCIONADO 0:DESELECCIONAR **/
	public function seleccionarSerieBD($codigoSerie,$estadoSeleccionado){

		$filter=new stdClass();
		$filter->ALMPRODSERC_Seleccion=$estadoSeleccionado;
		$filter->ALMPRODSERC_FechaSeleccion=date('Y-m-d h:m:s');
		$filter->ALMPRODSERC_FlagEstado=1;
		$this->db->where("SERIP_Codigo",$codigoSerie);
		$this->db->update("cji_almacenproductoserie",(array)$filter);
	}

}
?>