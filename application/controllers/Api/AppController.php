<?php
/*
Fecha: 25/11/2020
Descripcion: WebService para manejar las peticiones del aplicativo movil
*/
defined('BASEPATH') or exit('No direct script access allowed');

class AppController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Api/AppModel');
	}

	public function login()
	{
		$username = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
		$fecha = trim($this->input->post('fecha'));
		$hora = trim($this->input->post('hora'));

		$json = array();

		if ($username != "" && $password != "") {
			$user = $this->AppModel->login($username);
			if ($user != NULL) {
				if (password_verify($password, $user->clave)) {
					$info[] = array(
						'username' => $user->usuario,
						'name' => $user->nombre,
						'clave' => $user->clave,
						'rol_descripcion' => $user->rol_descripcion,
						'estado' => $user->estado,
						'intervalo' => $this->AppModel->getIntervalo()
					);
					$this->sesSave($user->usuario, "I", $fecha, $hora);
					$json = array("match" => true, "message" => "Completado con exito.", "info" => $info);
				} else {
					$json = array("match" => false, "message" => "Contraseña incorrecta.", "info" => NULL);
				}
			} else {
				$json = array("match" => false, "message" => "Usuario no valido.", "info" => NULL);
			}
		} else {
			$json = array("match" => false, "message" => "Solicitud invalida", "info" => NULL);
		}
		die(json_encode($json));
	}

	public function logout()
	{
		$username = trim($this->input->post('username'));
		$fecha = trim($this->input->post('fecha'));
		$hora = trim($this->input->post('hora'));

		$json = array();

		if ($username != "") {
			$this->sesSave($username, "S", $fecha, $hora);
			$json = array("match" => true, "message" => "Sesion finalizada.", "info" => NULL);
		} else {
			$json = array("match" => false, "message" => "Usuario no especificado.", "info" => NULL);
		}
		die(json_encode($json));
	}

	public function sesSave($username, $ingreso, $fecha, $hora)
	{
		$cols = new stdClass();
		$cols->CNTC_Cod_Personal = $username;
		$cols->CNTC_ING_SAL = $ingreso;
		$cols->CNTC_Fecha_Transm = $fecha;
		$cols->CNTC_Hora_Transm = $hora;
		$this->AppModel->detailsSave($cols);
	}

	public function geoSave()
	{
		$user = trim($this->input->post("username"));
		$location = trim($this->input->post("location"));
		$battery = trim($this->input->post("battery"));
		$telefono = trim($this->input->post("telefono"));
		$fecha = trim($this->input->post("fecha"));
		$hora = trim($this->input->post("hora"));

		$loc = explode("|", $location);
		if (count($loc) == 2) {
			$longitud = $loc[0];
			$latitud = $loc[1];
		} else {
			$longitud = "?";
			$latitud = "?";
		}

		$cols = new stdClass();
		$cols->MGL_Cod_Personal = $user;
		$cols->MGL_Fecha_Transm = $fecha;
		$cols->MGL_Hora_Transm = $hora;
		$cols->MGL_Porc_Bateria = $battery;
		$cols->MGL_Telefono = $telefono;
		$cols->MGL_Longitud_GPRS = $longitud;
		$cols->MGL_Latitud_GPRS = $latitud;

		$json = array();
		$save = $this->AppModel->geoSave($cols);
		if ($save) {
			$string = "$user,$fecha,$hora,$battery,$telefono,$longitud,$latitud\n";
			$this->writeFileText($string);
		}

		$json = array("match" => true, "message" => "Completado con exito.", "info" => NULL);
		die(json_encode($json));
	}

	private function writeFileText($string)
	{
		$file = "../../OBJETOS/PERSONAL/PERS_GEO/GEOLINE.txt";
		if (file_exists($file)) {
			file_put_contents($file, $string, FILE_APPEND | LOCK_EX);
		} else {
			$f = fopen($file, 'w') or die("Se produjo un error al crear el archivo");
			fwrite($f, $string);
			fclose($f);
		}
	}

	public function maps()
	{
		$info = "";
		$ubicacion = $this->AppModel->lastGeoUsers();
		if ($ubicacion != NULL) {
			foreach ($ubicacion as $row => $col) {
				if ($info != "")
					$info .= ", ";
				$info .= "[\"$col->nombre\", $col->MGL_Latitud_GPRS, $col->MGL_Longitud_GPRS]";
			}
		}
		$data["ubicacion"] = $info;
		$this->load->view('Api/mapa', $data);
	}
}
