<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Formapago_Model extends CI_Model{

	##  -> Begin
	public function  __construct(){
		parent::__construct();
	}
	##  -> End

	##  -> Begin
	public function getFpagos($filter = NULL, $onlyRecords = true) {
            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
            $where = '';
            if (isset($filter->descripcion) && $filter->descripcion != '')
                    $where .= " AND FORPAC_Descripcion LIKE '%$filter->descripcion%'";
            $rec = "SELECT * FROM cji_formapago WHERE FORPAC_FlagEstado LIKE '1' $where $order $limit";
            $recF = "SELECT COUNT(*) as registros FROM cji_formapago WHERE FORPAC_FlagEstado LIKE '1' $where";
            $recT = "SELECT COUNT(*) as registros FROM cji_formapago WHERE FORPAC_FlagEstado LIKE '1'";
            $records = $this->db->query($rec);
            if ($onlyRecords == false){
                    $recordsFilter = $this->db->query($recF)->row()->registros;
                    $recordsTotal = $this->db->query($recT)->row()->registros;
            }
            if ($records->num_rows() > 0){
                if ($onlyRecords == false){
                    $info = array(
                            "records" => $records->result(),
                            "recordsFilter" => $recordsFilter,
                            "recordsTotal" => $recordsTotal
                    );
                }
                else{
                    $info = $records->result();
                }
            }
            else{
                if ($onlyRecords == false){
                    $info = array(
                            "records" => NULL,
                            "recordsFilter" => 0,
                            "recordsTotal" => $recordsTotal
                    );
                }
                else{
                    $info = $records->result();
                }
            }
            return $info;
	}
	##  -> End

	##  -> Begin
	public function getFpago($codigo) {
		$sql = "SELECT f.* FROM cji_formapago f WHERE f.FORPAP_Codigo = '$codigo'";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function insertar_fpago($filter){
		$this->db->insert("cji_formapago", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_fpago($fpago, $filter){
		$this->db->where('FORPAP_Codigo',$fpago);
		return $this->db->update('cji_formapago', $filter);
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_fpago($fpago, $filter){
		$this->db->where('FORPAP_Codigo',$fpago);
		$query = $this->db->update('cji_formapago', $filter);
		return $query;
	}
	##  -> End


	## FUNCTIONS OLDS

	public function seleccionar(){
		$arreglo = array(''=>':: Seleccione ::');
		foreach($this->listar() as $indice=>$valor){
			$indice1   = $valor->FORPAP_Codigo;
			$valor1    = $valor->FORPAC_Descripcion;
			$arreglo[$indice1] = $valor1;
		}
		return $arreglo;
	}

	public function listar($number_items='',$offset=''){
		$where = array("FORPAC_FlagEstado"=>1);
		$query = $this->db->order_by('FORPAC_Descripcion')->where($where)->where_not_in('FORPAP_Codigo','0')->get('cji_formapago',$number_items,$offset);
		if($query->num_rows() > 0){
			return $query->result();
		}
	}

	public function obtener($id){
		$where = array("FORPAP_Codigo"=>$id);
		$query = $this->db->order_by('FORPAC_Descripcion')->where($where)->get('cji_formapago',1);
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	
	public function obtener2($id){
		$where = array("FORPAP_Codigo"=>$id);
		$query = $this->db->order_by('FORPAC_Descripcion')->where($where)->get('cji_formapago',1);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
}
?>