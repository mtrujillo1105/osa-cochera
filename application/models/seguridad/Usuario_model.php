<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Usuario_model extends CI_Model {

	##  -> Begin
	private $empresa;
	private $compania;
	private $usuario;
	##  -> End

	##  -> Begin
	public function __construct() {
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
		$this->empresa = $this->session->userdata('empresa');
		$this->usuario = $this->session->userdata('usuario');
	}
	##  -> End

	##  -> Begin
	public function getPersonaUsuario($persona) {
		$sql = "SELECT *
							FROM cji_persona p
							LEFT JOIN cji_usuario u ON u.PERSP_Codigo = p.PERSP_Codigo
							WHERE p.PERSP_Codigo = '$persona'
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getUsuario($usuario) {
		$sql = "SELECT *
							FROM cji_persona p
							INNER JOIN cji_usuario u ON u.PERSP_Codigo = p.PERSP_Codigo
							WHERE u.USUA_Codigo = $usuario
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function getAccesoUsuario($usuario) {
		$sql = "SELECT uc.*, e.EMPRC_Ruc, e.EMPRC_RazonSocial, ep.EESTABC_Descripcion, r.ROL_Descripcion
							FROM cji_usuario_compania uc
							LEFT JOIN cji_compania c ON c.COMPP_Codigo = uc.COMPP_Codigo
							LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
							LEFT JOIN cji_emprestablecimiento ep ON ep.EESTABP_Codigo = c.EESTABP_Codigo
							LEFT JOIN cji_rol r ON r.ROL_Codigo = uc.ROL_Codigo
							WHERE uc.USUA_Codigo = $usuario
						";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	public function listar_vendedores($number_items = '', $offset = '') {
		$where = array("USUA_FlagEstado" => "1","COMPP_Codigo" => $this->compania);
		$query = $this->db->where($where)
		->join('cji_persona', 'cji_persona.PERSP_Codigo=cji_usuario.PERSP_Codigo')
                ->join('cji_usuario_compania', 'cji_usuario_compania.USUA_Codigo=cji_usuario.USUA_Codigo')
		->order_by('cji_persona.PERSC_Nombre, cji_persona.PERSC_ApellidoPaterno, cji_persona.PERSC_ApellidoMaterno')
		->get('cji_usuario', $number_items, $offset);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_usuarios($number_items = '', $offset = '') {
		$compania = $_SESSION['compania'];
		$where = " WHERE USUA_FlagEstado = 1";
		$sql = "
                    SELECT DISTINCT cji_usuario.*,
                           cji_persona.*
                    FROM cji_usuario
                    INNER JOIN cji_persona on cji_persona.PERSP_Codigo = cji_usuario.PERSP_Codigo
                    INNER JOIN cji_usuario_compania on cji_usuario_compania.USUA_Codigo = cji_usuario.USUA_Codigo
                    $where
                    ORDER BY cji_persona.PERSC_Nombre
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function getEstablecimientos() {
		$sql = "SELECT c.*, est.EESTABC_Descripcion, e.EMPRC_RazonSocial
		FROM cji_compania c
		INNER JOIN cji_emprestablecimiento est ON est.EESTABP_Codigo = c.EESTABP_Codigo
		INNER JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
		WHERE c.COMPC_FlagEstado LIKE '1'
		ORDER BY c.EMPRP_Codigo
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function registrar_usuario($filter){
		$this->db->insert("cji_usuario", (array) $filter);
		return $this->db->insert_id();
	}

	public function actualizar_usuario($idRegistro, $filter){
		$this->db->where('USUA_Codigo',$idRegistro);
		return $this->db->update('cji_usuario', $filter);
	}

	public function deshabilitar_usuario($usuario){

		$sql = "UPDATE cji_usuario SET USUA_FlagEstado = 0 WHERE USUA_Codigo = $usuario";
		$query = $this->db->query($sql);

		if ($query){
			$sql = "DELETE FROM cji_usuario_compania WHERE USUA_Codigo = $usuario";
			$query = $this->db->query($sql);
		}

		return $query;
	}

	##  -> Begin
	public function getUsuariosDatatable($filter = NULL){
		$compania = $this->compania;
		$empresa = $this->empresa;

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = "";

		if ( isset($filter->searchNombre) && $filter->searchNombre != "" )
			$where .= " AND Match(p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) AGAINST ('$filter->searchNombre') ";

		if ( isset($filter->searchUsuario) && trim($filter->searchUsuario) != "")
			$where .= " AND u.USUA_usuario LIKE '%$filter->searchUsuario%' ";

		if ( isset($filter->searchRol) && trim($filter->searchRol) != "")
			$where .= " AND r.ROL_Descripcion LIKE '%$filter->searchRol%' ";

		$rec = "SELECT u.*, CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as nombres,
							GROUP_CONCAT(DISTINCT r.ROL_Descripcion ORDER BY r.ROL_Descripcion SEPARATOR ' - ') as ROL_Descripcion
							FROM cji_usuario u
							LEFT JOIN cji_persona p ON p.PERSP_Codigo = u.PERSP_Codigo
							LEFT JOIN cji_usuario_compania uc ON uc.USUA_Codigo = u.USUA_Codigo
							LEFT JOIN cji_rol r ON r.ROL_Codigo = uc.ROL_Codigo
							WHERE u.USUA_usuario NOT LIKE '%ccapasistemas%' AND u.USUA_FlagEstado = 1 AND EXISTS(SELECT c.COMPP_Codigo FROM cji_compania c WHERE c.EMPRP_Codigo = $empresa ) $where
							GROUP BY u.USUA_Codigo $order $limit ";

		$recF = "SELECT COUNT(DISTINCT u.USUA_Codigo) as registros
							FROM cji_usuario u
							LEFT JOIN cji_persona p ON p.PERSP_Codigo = u.PERSP_Codigo
							LEFT JOIN cji_usuario_compania uc ON uc.USUA_Codigo = u.USUA_Codigo
							LEFT JOIN cji_rol r ON r.ROL_Codigo = uc.ROL_Codigo
							WHERE u.USUA_usuario NOT LIKE '%ccapasistemas%'
								AND u.USUA_FlagEstado = 1
								AND EXISTS(SELECT c.COMPP_Codigo FROM cji_compania c WHERE c.EMPRP_Codigo = $empresa )
								$where
						";

		$recT = "SELECT COUNT(*) as registros
							FROM cji_usuario u
							WHERE u.USUA_usuario NOT LIKE '%ccapasistemas%'
								AND u.USUA_FlagEstado = 1
								AND EXISTS(SELECT c.COMPP_Codigo FROM cji_compania c WHERE c.EMPRP_Codigo = $empresa )
						";

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

	public function obtener($usuario) {
		$this->db->select('*');
		$this->db->from('cji_usuario');
		$this->db->join('cji_persona', 'cji_persona.PERSP_Codigo=cji_usuario.PERSP_Codigo');
		$this->db->where('cji_usuario.USUA_Codigo', $usuario);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		}
	}

	public function buscar_nombre_usuario($usuario = "") {
		$sql = "SELECT * FROM cji_usuario WHERE USUA_usuario LIKE '$usuario' ";
		$query = $this->db->query($sql);

		$data = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $key => $value) {
				$data[] = $value;
			}
		}

		return $data;
	}

	public function obtener2($usuario) {
		$this->db->select('*');
		$this->db->from('cji_usuario');
		$this->db->join('cji_persona', 'cji_persona.PERSP_Codigo=cji_usuario.PERSP_Codigo');
		$this->db->where('cji_usuario.USUA_Codigo', $usuario);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		else
			return NULL;
	}

	public function obtener_datosUsuario($user, $clave, $compania) {
		$where = array('USUA_usuario' => $user, 'USUA_Password' => $clave, 'USUA_FlagEstado' => '1');
		$query = $this->db->where($where)->get('cji_usuario');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_rolesUsuario($user, $compania = NULL) {
		$where = array('cji_usuario_compania.USUA_Codigo' => $user, 'cji_usuario_compania.USUCOMC_Default' => '1');
		$query = $this->db->where($where)->join('cji_rol', 'cji_usuario_compania.ROL_Codigo=cji_rol.ROL_Codigo')->get('cji_usuario_compania');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_datosUsuarioLogin($user, $clave) {
		$sql = "SELECT * FROM cji_usuario WHERE USUA_usuario LIKE '$user' AND USUA_Password = '$clave' AND USUA_FlagEstado LIKE '1'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
                    return $query->result();
		else
                    return NULL;
	}

	public function obtener_datosUsuarioLoginVenta($user, $clave, $pers) {

		$sql = "SELECT * FROM cji_usuario WHERE USUA_usuario = '$user' AND USUA_Password = '$clave' AND USUA_FlagEstado = 1 AND PERSP_Codigo = '$pers' ";
		$query = $this->db->query($sql);
		$data = array();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	##  -> Begin
	public function getUserPers($pers) {
		$sql = "SELECT * FROM cji_usuario WHERE PERSP_Codigo = '$pers' ";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	public function obtener_datosUsuario2($usuario) {
		$query = $this->db->where('USUA_Codigo', $usuario)->get('cji_usuario');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_usuario($persona, $usuario, $clave) {
		$data = array(
			"PERSP_Codigo" => $persona,
			"USUA_usuario" => $usuario,
			"USUA_Password" => md5($clave)
		);
		$this->db->insert("cji_usuario", $data);
		return $this->db->insert_id();
	}

	public function insertar_datosUsuario($txtNombres, $txtPaterno, $txtMaterno, $txtUsuario, $txtClave, $cboEstablecimiento, $cboRol, $default, $detaccion, $hid_Persona) {
		$usuario = $this->insertar_usuario($hid_Persona, $txtUsuario, $txtClave);

		if (is_array($cboEstablecimiento)) {
			foreach ($cboEstablecimiento as $indice => $valor) {
				if ($detaccion[$indice] != 'e') {
					$filter = new stdClass();
					$filter->USUA_Codigo = $usuario;
					$filter->COMPP_Codigo = $valor;
					$filter->ROL_Codigo = $cboRol[$indice];
					$filter->USUCOMC_Default = ($default == $indice) ? 1 : 0;
					$this->usuario_compania_model->insertar($filter);
				}
			}
		}
	}

	public function modificar_datosUsuario($usuario, $rol, $establecimiento, $nombre_usuario, $nombres, $paterno, $materno) {
		$datos_usuario = $this->obtener_datosUsuario2($usuario);
		$persona = $datos_usuario[0]->PERSP_Codigo;
		$this->persona_model->modificar_datosPersona_nombres($persona, $nombres, $paterno, $materno);
		$this->modificar_usuario($usuario, $rol, $establecimiento, $nombre_usuario);
	}

	public function modificar_usuario($usuario, $rol, $establecimiento, $nombre_usuario) {
		$data = array("ROL_Codigo" => $rol,
			"EESTABP_Codigo" => $establecimiento,
			"USUA_usuario" => $nombre_usuario);
		$this->db->where('USUA_Codigo', $usuario);
		$this->db->update("cji_usuario", $data);
	}

    ///---------------------------------------------------------------
    /// modificar establecimiento y cargo
	public function modificar_datosUsuario22($usuario, $nombre_usuario, $nombres, $paterno, $materno) {
		$datos_usuario = $this->obtener_datosUsuario2($usuario);
		$persona = $datos_usuario[0]->PERSP_Codigo;
		$this->persona_model->modificar_datosPersona_nombres($persona, $nombres, $paterno, $materno);
        //$this->modificar_usuario($usuario,$rol,$establecimiento,$nombre_usuario);
		$this->modificar_usuario2($usuario, $nombre_usuario);
	}

    //--------------------------------------
	public function modificar_rolestauser($usuario, $rol, $establecimiento, $default) {
        //----INSERTAR
		if (is_array($establecimiento)) {

			$this->db->delete('cji_usuario_compania', array('USUA_Codigo' => $usuario));

			foreach ($establecimiento as $indice => $valor) {
				$filter = new stdClass();
				$filter->USUA_Codigo = $usuario;
				$filter->COMPP_Codigo = $valor;
				$filter->ROL_Codigo = $rol[$indice];
				$filter->USUCOMC_Default = ($default == $indice) ? 1 : 0;

				$this->usuario_compania_model->insertar($filter);
			}
		}
	}

	public function modificar_usuario2($usuario, $nombre_usuario) {
		$data = array("USUA_usuario" => $nombre_usuario);
		$this->db->where('USUA_Codigo', $usuario);
		$this->db->update("cji_usuario", $data);
	}

	public function modificar_usuarioClave($usuario, $clave) {
		$data = array("USUA_Password" => md5($clave));
		$this->db->where('USUA_Codigo', $usuario);
		$this->db->update("cji_usuario", $data);
	}

	public function eliminar_usuario($usuario){

		$sql = "UPDATE cji_usuario SET USUA_FlagEstado = 0 WHERE USUA_Codigo = $usuario";
		$query = $this->db->query($sql);

		if ($query){
			$sql = "DELETE FROM cji_usuario_compania WHERE USUA_Codigo = $usuario";
			$query = $this->db->query($sql);
		}
	}

	public function eliminar_rolestablecimiento($usuario) {
		$where = array("USUCOMP_Codigo" => $usuario);
		$this->db->delete('cji_usuario_compania', $where);
	}

	public function buscar_usuarios($filter, $number_items = '', $offset = '') {
		$wherenombres = "";
		$whereusuario = "";
		$whererol = "";
		if (isset($filter->nombres) && $filter->nombres != "") {
			$wherenombres = "and concat(c.PERSC_Nombre,' ',c.PERSC_ApellidoPaterno,' ',c.PERSC_ApellidoMaterno) like '%" . $filter->nombres . "%'";
		}
		if (isset($filter->usuario) && $filter->usuario != '') {
			$whereusuario = "and a.USUA_usuario like '%" . $filter->usuario . "%'";
		}
		if (isset($filter->rol) && $filter->rol != '') {
			$whererol = "and d.ROL_Descripcion like '" . $filter->rol . "%'";
		}
		$sql = "
		select 
		distinct a.USUA_Codigo,
		a.PERSP_Codigo,
		d.ROL_Codigo,
		a.USUA_usuario
		from cji_usuario as a
		inner join cji_usuario_compania as b on a.USUA_Codigo=b.USUA_Codigo
		inner join cji_persona as c on a.PERSP_Codigo=c.PERSP_Codigo
		inner join cji_rol as d on b.ROL_Codigo=d.ROL_Codigo
		where a.USUA_FlagEstado='1'
		" . $wherenombres . "
		" . $whereusuario . "
		" . $whererol . "  GROUP BY b.USUA_Codigo ORDER BY  c.PERSC_Nombre "
		;

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function buscar_usuariosrolesta($filter) {

		$sql = "
		select
		a.USUA_Codigo,
		b.COMPP_Codigo,
		b.USUCOMP_Codigo,
		a.PERSP_Codigo,
		d.ROL_Codigo,
		f.EESTABC_Descripcion,
		d.ROL_Descripcion,
		a.USUA_usuario
		from cji_usuario as a
		inner join cji_usuario_compania as b on a.USUA_Codigo=b.USUA_Codigo
		inner join cji_persona as c on a.PERSP_Codigo=c.PERSP_Codigo
		inner join cji_rol as d on b.ROL_Codigo=d.ROL_Codigo
		inner join cji_compania as e on b.COMPP_Codigo=e.COMPP_Codigo
		inner join cji_emprestablecimiento as f on e.EESTABP_Codigo=f.EESTABP_Codigo
		where a.USUA_FlagEstado='1'

		and a.USUA_Codigo = '" . $filter . "'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_empresa_usuario($user, $compania = NULL) {
		$sql = "SELECT *
								FROM cji_usuario_compania u
								INNER JOIN cji_compania c ON c.COMPP_Codigo = u.COMPP_Codigo
								WHERE u.USUA_Codigo = '$user' AND u.USUCOMC_Default LIKE '1'
						";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}

	public function usersNotifications($id){
		$compania = $this->compania;
		$sql = "SELECT p.PERSC_Email
		FROM cji_persona p
		INNER JOIN cji_usuario u ON p.PERSP_Codigo = u.PERSP_Codigo
		INNER JOIN cji_usuario_compania uc ON uc.USUA_Codigo = u.USUA_Codigo
		INNER JOIN cji_rol r ON r.ROL_Codigo = uc.ROL_Codigo
		INNER JOIN cji_permiso pm ON pm.ROL_Codigo = u.ROL_Codigo
		INNER JOIN cji_menu m ON m.MENU_Codigo = pm.MENU_Codigo
		WHERE pm.PERM_FlagEstado = 1 AND m.MENU_Codigo = '$id' AND m.MENU_FlagEstado = 1 AND uc.COMPP_Codigo = $compania AND p.PERSC_FlagEstado = 1;
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