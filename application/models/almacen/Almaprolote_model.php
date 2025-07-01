<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Almaprolote_Model extends CI_Model{
	protected $_name = "cji_almaprolote";
	##  -> Begin
	private $compania;
	##  -> End

	public function  __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}
	public function listarFIFO($almacenproducto_id)
	{
		$where = array("ALMPROD_Codigo"=>$almacenproducto_id,"COMPP_Codigo"=>$this->session->userdata('compania'));
		$query = $this->db->where($where)->order_by("ALMALOTP_Codigo")->get('cji_almaprolote');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function listarLIFO($almacenproducto_id)
	{
		$where = array("ALMPROD_Codigo"=>$almacenproducto_id,"COMPP_Codigo"=>$this->session->userdata('compania'));
		$query = $this->db->where($where)->order_by("ALMALOTP_Codigo","desc")->get('cji_almaprolote');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function obtener($almacenproducto_id,$lote_id)
	{
		$where = array("ALMPROD_Codigo"=>$almacenproducto_id,"COMPP_Codigo"=>$this->compania,"LOTP_Codigo"=>$lote_id);
		$query = $this->db->where($where)->get('cji_almaprolote');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}

	public function aumentar($almacenproducto_id, $lote_id, $cantidad, $costo, $compania = NULL){
		$compania = ($compania == NULL) ? $this->compania : $compania;

		$filter                    = new stdClass();
		$filter->COMPP_Codigo      = $compania;
		$filter->ALMPROD_Codigo    = $almacenproducto_id;
		$filter->LOTP_Codigo       = $lote_id;
		$stock                     = $this->obtener($almacenproducto_id,$lote_id);
		if(count($stock)>0){
			unset($filter->COMPP_Codigo);
			unset($filter->ALMPROD_Codigo);
			unset($filter->LOTP_Codigo);
			$cantidad_anterior              = $stock[0]->ALMALOTC_Cantidad;
			$almacenprodlote_id             = $stock[0]->ALMALOTP_Codigo;
			$filter->ALMALOTC_Cantidad      = $cantidad_anterior + $cantidad;
			$filter->ALMALOTC_CantidadDisponible = $stock[0]->ALMALOTC_CantidadDisponible + $cantidad;
			$filter->ALMALOTC_Costo         = $costo;
			$filter->ALMALOTC_FlagEstado    = 1;
			$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
			$this->db->update("cji_almaprolote",(array)$filter);
		}
		else{
			$filter->ALMALOTC_Cantidad = $cantidad;
			$filter->ALMALOTC_CantidadDisponible = $cantidad;
			$filter->ALMALOTC_Costo    = $costo;
			$this->db->insert("cji_almaprolote",(array)$filter);
		}

	}

	public function disminuir_stock($almacenproducto_id, $lote_id, $cantidad, $costo, $compania = NULL){
		$compania = ($compania == NULL) ? $this->compania : $compania;

		$filter                    = new stdClass();
		$filter->COMPP_Codigo      = $compania;
		$filter->ALMPROD_Codigo    = $almacenproducto_id;
		$filter->LOTP_Codigo       = $lote_id;
		$stock                     = $this->obtener($almacenproducto_id,$lote_id);
		if(count($stock)>0){
			unset($filter->COMPP_Codigo);
			unset($filter->ALMPROD_Codigo);
			unset($filter->LOTP_Codigo);
			$cantidad_anterior              = $stock[0]->ALMALOTC_Cantidad;
			$almacenprodlote_id             = $stock[0]->ALMALOTP_Codigo;
			$filter->ALMALOTC_Cantidad      = $cantidad_anterior - $cantidad;
			$filter->ALMALOTC_CantidadDisponible = $stock[0]->ALMALOTC_CantidadDisponible - $cantidad;
			$filter->ALMALOTC_Costo         = $costo;

			if ( $filter->ALMALOTC_Cantidad <= 0 )
				$filter->ALMALOTC_FlagEstado = 0;
			else
				$filter->ALMALOTC_FlagEstado = 1;

			$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
			$this->db->update("cji_almaprolote",(array)$filter);
		}
		else{
			$filter->ALMALOTC_Cantidad = $cantidad;
			$filter->ALMALOTC_CantidadDisponible = $cantidad;
			$filter->ALMALOTC_Costo    = $costo;
			$this->db->insert("cji_almaprolote",(array)$filter);
		}

	}

	public function disminuir($almacenproducto_id,$cantidad){
		$this->load->model('maestros/compania_model');
		$datos_compania    = $this->compania_model->obtener($this->compania);
		$tipo_valorizacion = $datos_compania[0]->COMPC_TipoValorizacion;
		if($tipo_valorizacion==0)
			$lotes       = $this->listarFIFO($almacenproducto_id);
		elseif($tipo_valorizacion==1)
			$lotes       = $this->listarLIFO($almacenproducto_id);
		$qlotes = count($lotes);
		if(count($lotes)>0){
			foreach ($lotes as $indice=>$value){
				$almacenprodlote_id = $value[$indice]->ALMALOTP_Codigo;
				$almacenproducto_id = $value[$indice]->ALMPROD_Codigo;
				$lote_id            = $value[$indice]->LOTP_Codigo;
				$anterior           = $value[$indice]->ALMALOTC_Cantidad;
				$costo_anterior     = $value[$indice]->ALMALOTC_Costo;
				if($anterior>0){
					if($cantidad>=$anterior){
						if($qlotes>$indice+1){
							$cantidad = $cantidad - $anterior;
							$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
							$this->db->delete("cji_almaprolote");
						}
						else{
							$cantidad_total = $anterior - $cantidad;
							$filter  = new stdClass();
							$filter->ALMALOTC_Cantidad = $cantidad_total;
							$filter->ALMALOTC_Costo    = $costo_anterior;
							$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
							$this->db->update("cji_almaprolote",(array)$filter);
						}
					}
					else{
						$cantidad_total     = $anterior - $cantidad;
						$filter  = new stdClass();
						$filter->ALMALOTC_Cantidad = $cantidad_total;
						$filter->ALMALOTC_Costo    = $costo_anterior;
						$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
						$this->db->update("cji_almaprolote",(array)$filter);
						break;
					}
				}
				else{
					if($qlotes==$indice+1){
						$cantidad_total     = $anterior - $cantidad;
						$filter  = new stdClass();
						$filter->ALMALOTC_Cantidad = $cantidad_total;
						$filter->ALMALOTC_Costo    = $costo_anterior;
						$this->db->where("ALMALOTP_Codigo",$almacenprodlote_id);
						$this->db->update("cji_almaprolote",(array)$filter);
					}
				}
			}
		}
	}

	public function disminuir2($almacenproducto_id,$lote_id,$cantidad)
	{
		$datos_almacenproducto = $this->obtener($almacenproducto_id,$lote_id);
		$almacenprolote_id = $datos_almacenproducto[0]->ALMALOTP_Codigo;
		$cantidad_inicial  = $datos_almacenproducto[0]->ALMALOTC_Cantidad;
		$filter = new stdClass();
		$filter->ALMALOTC_Cantidad = $cantidad_inicial-$cantidad;
		$this->db->where("ALMALOTP_Codigo",$almacenprolote_id);
		$this->db->update("cji_almaprolote",(array)$filter);
	}
	public function eliminar($almacenproducto_id,$lote_id){
		$this->db->delete('cji_almaprolote', array('ALMPROD_Codigo' => $almacenproducto_id,'LOTP_Codigo' => $lote_id));
	}

	public function modificar($id,$filter){
		$this->db->where("ALMALOTP_Codigo", $id);
		$this->db->update("cji_almaprolote",(array)$filter);
	}

	public function listar($producto, $almacenP){
		$sql = "SELECT apl.*, l.*
		FROM cji_almaprolote apl
		INNER JOIN cji_lote l ON l.LOTP_Codigo = apl.LOTP_Codigo
		WHERE l.PROD_Codigo = $producto AND apl.ALMPROD_Codigo = $almacenP AND apl.ALMALOTC_FlagEstado = 1 AND apl.ALMALOTC_Cantidad > 0
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

	public function stockDisponible($idAlmacenP){
		$sql = "SELECT SUM(apl.ALMALOTC_CantidadDisponible) as stockDisponible
		FROM cji_almaprolote apl
		WHERE apl.ALMPROD_Codigo = $idAlmacenP AND apl.ALMALOTC_FlagEstado = 1 AND apl.ALMALOTC_CantidadDisponible > 0
		";
		$query = $this->db->query($sql);
		$data = array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $key => $fila) {
				if ($fila->stockDisponible == NULL)
					$fila->stockDisponible = 0;

				$data[] = $fila;
			}
		}
		return $data[0]->stockDisponible;
	}
}
?>