<?php
/*
Fecha: 25/11/2020
Conexiones:
	* default -> llrvqmax_msa_db_cal
	* another_db -> llrvqmax_sec_db_cal
	* geo_db -> llrvqmax_geo_db_cal
*/
class AppModel extends CI_Model
{
	private $odb;

	public function __construct()
	{
		parent::__construct();
	}

	/* Consultar login */
	public function login($usuario)
	{
		$this->odb = $this->load->database('another_db', TRUE);
		$sql = "SELECT u.USU_Cod_usuario as usuario, u.USU_Nombre_corrido as nombre, u.USU_Contrasena as clave, u.USU_Estado as estado,
		r.ROL_Descripcion_rol as rol_descripcion
							FROM ms_usuarios u
							INNER JOIN ms_rolesh r ON r.ROL_Codigo_rol = u.ROL_Codigo_rol
							WHERE u.USU_Cod_usuario LIKE '%$usuario%'
								AND u.USU_Estado LIKE 'Activo'
						";
		$query = $this->odb->query($sql);

		if ($query->num_rows() > 0)
			return $query->row();
		else
			return NULL;
	}

	/* Obtener el intervalo de envio */
	public function getIntervalo()
	{
		$this->odb = $this->load->database('default', TRUE);
		$sql = "SELECT TB_Valor_num1 as intervalo
							FROM ms_tabla t
							WHERE t.TB_Id_tabla = '39' AND t.TB_Codigo_especifico = 'IG' LIMIT 1";
		$query = $this->odb->query($sql);

		if ($query->num_rows() > 0)
			return $query->row()->intervalo;
		else
			return 0;
	}

	/* Guardar detalles de conexion */
	public function detailsSave($filter)
	{
		$this->odb = $this->load->database('default', TRUE);
		$this->odb->insert('ms_cntrconect', (array) $filter);
		$this->odb->query('COMMIT');
		return $this->odb->insert_id();
	}

	/* Guardar posicion del dispositivo */
	public function geoSave($filter)
	{
		$this->odb = $this->load->database('geo_db', TRUE);
		$this->odb->insert('geo_datamov', (array) $filter);
		$this->odb->query('COMMIT');
		return $this->odb->insert_id();
	}

	public function lastGeoUsers()
	{
		$this->odb = $this->load->database('geo_db', TRUE);
		$sql = "SELECT MAX(g.Id), g.MGL_Cod_Personal, g.MGL_Fecha_Transm, g.MGL_Hora_Transm, g.MGL_Porc_Bateria, g.MGL_Telefono, g.MGL_Longitud_GPRS, g.MGL_Latitud_GPRS
		FROM geo_datamov g GROUP BY g.MGL_Cod_Personal";
		$info = $this->odb->query($sql);
		
		if ($info->num_rows() > 0) {
			$this->odb = $this->load->database('another_db', TRUE);
			$sql = "DROP TABLE IF EXISTS ubicacion_temp;";
			$this->odb->query($sql);

			$sql = "CREATE TEMPORARY TABLE ubicacion_temp(MGL_Cod_Personal varchar(15), MGL_Fecha_Transm date, MGL_Hora_Transm time, MGL_Latitud_GPRS varchar(20), MGL_Longitud_GPRS varchar(20), MGL_Porc_Bateria int(11), MGL_Telefono int(11));";
			$this->odb->query($sql);

			$save = "";
			foreach ($info->result() as $row => $col) {
				if ($save == "")
					$save .= "INSERT INTO ubicacion_temp (MGL_Cod_Personal, MGL_Fecha_Transm, MGL_Hora_Transm, MGL_Porc_Bateria, MGL_Telefono, MGL_Longitud_GPRS, MGL_Latitud_GPRS) VALUES(\"$col->MGL_Cod_Personal\", \"$col->MGL_Fecha_Transm\", \"$col->MGL_Hora_Transm\", \"$col->MGL_Porc_Bateria\", \"$col->MGL_Telefono\", \"$col->MGL_Longitud_GPRS\", \"$col->MGL_Latitud_GPRS\")";
				else
					$save .= ", (\"$col->MGL_Cod_Personal\", \"$col->MGL_Fecha_Transm\", \"$col->MGL_Hora_Transm\", \"$col->MGL_Porc_Bateria\", \"$col->MGL_Telefono\", \"$col->MGL_Longitud_GPRS\", \"$col->MGL_Latitud_GPRS\")";
			}
			$save .= ";";
			$this->odb->query($save);

			$sql = "SELECT u.USU_Nombre_corrido as nombre, r.ROL_Descripcion_rol as rol_descripcion, g.MGL_Cod_Personal, g.MGL_Fecha_Transm, g.MGL_Hora_Transm, g.MGL_Porc_Bateria, g.MGL_Telefono, g.MGL_Longitud_GPRS, g.MGL_Latitud_GPRS
							FROM ubicacion_temp g
							INNER JOIN ms_usuarios u ON u.USU_Cod_usuario = g.MGL_Cod_Personal
							INNER JOIN ms_rolesh r ON r.ROL_Codigo_rol = u.ROL_Codigo_rol
							WHERE u.USU_Estado LIKE 'Activo'
							";
			return $this->odb->query($sql)->result();
		}
		else{
			return NULL;
		}
	}
}
