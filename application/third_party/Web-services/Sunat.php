<?php

/**
* 
*/
class Sunat
{
	private $_ruc;

	private $_url_service = "https://186.64.123.228:8080/Sunat/ServicioRuc?wsdl";

	public function consulta_ruc($nro_ruc)
	{
		$this->_ruc = $nro_ruc;
		
		$cliente = $this->_soap();

		if($cliente) {
			$cliente = explode('|', $cliente);

			$cliente_rep = new stdClass();
			$cliente_rep->ruc = $cliente[0];
			$cliente_rep->nombre = $cliente[1];
			$cliente_rep->estado = $cliente[2];
			$cliente_rep->condicion = $cliente[3];
			$cliente_rep->ubigeo = $cliente[4];
			$cliente_rep->direccion = $cliente[5] . " " . $cliente[6] . " " . $cliente[7] . " " . $cliente[8];

			return $cliente_rep;
		}

		return null;
	}

	private function _soap()
	{
		try{
			ini_set('soap.wsdl_cache_enabled', 0);
			ini_set('soap.wsdl_cache_ttl', 900);
			ini_set('default_socket_timeout', 15);

			$client = new SoapClient($this->_url_service, array(
				'uri' => 'http://schemas.xmlsoap.org/soap/envelope/',
				'style' => SOAP_RPC,
				'use' => SOAP_ENCODED,
				'soap_version' => SOAP_1_1,
				'cache_wsdl' => WSDL_CACHE_NONE,
				'connection_timeout' => 15,
				'trace' => true,
				'encoding' => 'UTF-8',
				'exceptions' => true
			));

			$response = $client->ConsultaRuc(array(
				"ruc" => $this->_ruc
			));
			
			return $response->return;
		}catch(SoapFault $se) {
			header($_SERVER["SERVER_PROTOCOL"]." 500 Error");
			header("Content-type: application/json");

			echo json_encode(array(
				"status" => 500,
				"message" => "No se puede conectar con sunat, intente ingresar de manera directa."
			));

			die;
		}
	}
}