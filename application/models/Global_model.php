<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
Descripcion: Unknow
/* ******************************************************************************** */

/*
	Dev: RC
	Change: Definicion publicos en todos los metodos del modelo.
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Global_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function filas($tabla) {
		$consulta = $this->db->get($tabla);
		return $consulta->num_rows();
	}

	/* Paginador */
	public function paginados($tabla, $por_pagina, $segmento) {
		$consulta = $this->db->get($tabla, $por_pagina, $segmento);
		if ($consulta->num_rows() > 0) {
			return $consulta->result();
		}
	}
	/* Fin paginador */

	public function get($tabla) {
		$query = $this->db->get($tabla);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function get_insert($table, $where) {
		$objeto = $this->get_where($table, $where, 1);
		if (!$objeto) {
			$this->insert($table, $where);
			$objeto = $this->get_where($table, $where, 1);
		}
		return $objeto;
	}

	public function get_where($tabla = '', $array = array(), $cant = 0) {
		if ($cant > 0) {
			$this->db->limit($cant);
		}
		$query = $this->db->get_where($tabla, $array);
		if ($query->num_rows() > 0) {
			return $cant == 1 ? $query->row() : $query->result();
		} else {
			return FALSE;
		}
	}

	public function get_or_where($tabla = '', $array = array(), $cant = 0) {
		if ($cant > 0) {
			$this->db->limit($cant);
		}
		$query = $this->db->get_where($tabla, $array);
		if ($query->num_rows() > 0) {
			return $cant == 1 ? $query->row() : $query->result();
		} else {
			return FALSE;
		}
	}

	public function get_where_order($tabla = '', $array = array(), $col, $cant = 1) {
		$this->db->order_by($col);
		$query = $this->db->get_where($tabla, $array, $cant);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}


	public function max($tabla, $col) {
		$query = $this->db->select(" MAX({$col}) as max ")->from($tabla)->get();
		return $query->num_rows() > 0 ? $query->row() : FALSE;
	}

	public function max_where($tabla, $col, $where) {
		$query = $this->db->select(" MAX({$col}) as max ")->from($tabla)->where($where)->get();
		return $query->num_rows() > 0 ? $query->row() : FALSE;
	}

	public function update($tabla, $update = array(), $where = array()) {
		$query = $this->db->update($tabla, $update, $where);
		if ($query) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function query($query) {
		$query = $this->db->query($query);
		return $query->num_rows() > 0 ? $query->result() : FALSE;
	}

	public function insert($tabla, $data = array()) {
		$query = $this->db->insert($tabla, $data);
		return $query ? TRUE : FALSE;
	}

	public function insert_batch($tabla, $data = array()) {
		$query = $this->db->insert_batch($tabla, $data);
		return $query ? TRUE : FALSE;
	}

	public function delete($tabla, $where = array()) {
		$query = $this->db->delete($tabla, $where);
		return $query ? TRUE : FALSE;
	}

	public function distinct($tabla, $campo) {
		$query = $this->db->distinct()->select($campo)->from($tabla)->get();
		return $query->num_rows() > 0 ? $query->result() : FALSE;
	}

	public function count($tabla) {
		$query = $this->db->select("count(*) as cant ")->from($tabla)->get();
		return $query->num_rows() > 0 ? $query->row() : FALSE;
	}

	public function count_col($tabla, $campo) {
		$query = $this->db->select(" {$campo},count(*) as cant ")
		->from($tabla)
		->group_by($campo)
		->order_by("cant desc")
		->get();
		return $query->num_rows() > 0 ? $query->result() : FALSE;
	}

	public function count_where($tabla, $campo, $where) {
		$query = $this->db->select(" {$campo},count(*) as cant ")->from($tabla)->where($where)->group_by($campo)->get();
		return $query->num_rows() > 0 ? $query->result() : FALSE;
	}

	public function count_int($tabla, $where) {
		$query = $this->db->select(" count(*) as cant ")->from($tabla)->where($where)->get();
		return $query->num_rows() > 0 ? $query->row() : FALSE;
	}

	public function datetime() {
		$query = $this->db->query("SELECT NOW() AS fecha_hora ,CURDATE() AS fecha, TIME(NOW()) AS hora ");
		return $query ? $query->row() : FALSE;
	}
	public function lista_por_fecha($tabla,$fecha_i,$fecha_f){
		$query=  $this->db->query("SELECT * FROM $tabla WHERE fecha BETWEEN '$fecha_i' AND '$fecha_f'");
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}
	public function lista_por_mes($tabla,$fecha){
		$query=  $this->db->query("SELECT * FROM $tabla where month(fecha)='$fecha'");
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}
	public function menu($id_padre) {
		$this->db->order_by('m.orden');
		$query = $this->db->get_where('tbl_menu m', array("id_padre" => $id_padre));
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function rol($where) {
		$query = $this->db->get_where('tbl_roles', $where);
		if ($query->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
