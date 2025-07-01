<?php
/* *********************************************************************************
/* ******************************************************************************** */
if (!defined('BASEPATH')) exit('No direct script access allowed');

function formatHours($hora, $format = "12")
{
	$hora = explode(":", $hora);

	if ($format == "12") {
		if ($hora[0] > "12") {
			$nvaHora = ($hora[0] - 12);
			$nvaHora .= ":" . $hora[1] . " PM";
		} else if ($hora[0] == "12") {
			$nvaHora = $hora[0] . ":" . $hora[1] . " PM";
		} else if ($hora[0] == "00") {
			$nvaHora = ($hora[0] + 12);
			$nvaHora .= ":" . $hora[1] . " AM";
		} else
			$nvaHora = $hora[0] . ":" . $hora[1] . " AM";

		if (strlen($nvaHora) == 7)
			$nvaHora = "0" . $nvaHora;
	} else {
		$nvaHora = $hora[0] . ":" . $hora[1];
	}

	return $nvaHora;
}

function formatMes($mes = NULL, $format = "large", $style = "upper")
{
	switch ($mes) {
		case '1':
			$mesNew = "ENERO";
			break;
		case '2':
			$mesNew = "FEBRERO";
			break;
		case '3':
			$mesNew = "MARZO";
			break;
		case '4':
			$mesNew = "ABRIL";
			break;
		case '5':
			$mesNew = "MAYO";
			break;
		case '6':
			$mesNew = "JUNIO";
			break;
		case '7':
			$mesNew = "JULIO";
			break;
		case '8':
			$mesNew = "AGOSTO";
			break;
		case '9':
			$mesNew = "SEPTIEMBRE";
			break;
		case '10':
			$mesNew = "OCTUBRE";
			break;
		case '11':
			$mesNew = "NOVIEMBRE";
			break;
		case '12':
			$mesNew = "DICIEMBRE";
			break;
		default:
			$mesNew = "UNKNOW";
			break;
	}

	$mesNew = ($format == "small") ? substr($mesNew, 0, 3) . '.' : $mesNew;
	$style = strtolower($style);
	switch ($style) {
		case 'upper':
			$mesNew = strtoupper($mesNew);
			break;
		case 'lower':
			$mesNew = strtolower($mesNew);
			break;
		case 'capitalize':
			$mesNew = strtoupper(substr($mesNew, 0, 1)) . strtolower(substr($mesNew, 1));
			break;
		default:
			$mesNew = strtoupper($mesNew);
			break;
	}

	return $mesNew;
}

function formatDate($date, $format = "small", $formatMes = 'large', $style = 'upper', $first = "F")
{
	$fecha = explode(" ", $date);
	$size = count($fecha);

	$hora = ($size > 1) ? formatHours($fecha[1]) : NULL;

	$part = explode("-", $fecha[0]);
	if (count($part) <= 1) {
		unset($part);
		$part = explode("/", $fecha[0]);

		if (count($part) <= 1 || count($part) > 3) {
			echo "error en formato de fecha";
		}
	}

	unset($fecha);
	switch (strlen($part[0])) {
		case 4:
			$fecha = array(0 => $part[2], 1 => $part[1], 2 => $part[0]);
			break;
		case 2:
			$fecha = ($part[1] > 12) ? array(0 => $part[1], 1 => $part[0], 2 => $part[2]) : array(0 => $part[0], 1 => $part[1], 2 => $part[2]);
			break;
		case 1:
			$fecha = ($part[1] > 12) ? array(0 => $part[1], 1 => $part[0], 2 => $part[2]) : array(0 => $part[0], 1 => $part[1], 2 => $part[2]);
			break;
	}

	$format = strtolower($format);
	$formatMes = strtolower($formatMes);
	$style = strtolower($style);
	switch ($format) {
		case 'small':
			$fechaNew = $fecha[0] . '/' . $fecha[1] . '/' . $fecha[2];
			break;
		case 'small_spacing':
			$fechaNew = $fecha[0] . ' / ' . $fecha[1] . ' / ' . $fecha[2];
			break;
		case 'large':
			if ($style == 'upper')
				$fechaNew = $fecha[0] . ' DE ' . formatMes($fecha[1], $formatMes, $style) . ' DEL ' . $fecha[2];
			else
				$fechaNew = $fecha[0] . ' de ' . formatMes($fecha[1], $formatMes, $style) . ' del ' . $fecha[2];
			break;
		case 'db':
			$fechaNew = $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0];
			break;

		default:
			$fechaNew = $fecha[0] . '/' . $fecha[1] . '/' . $fecha[2];
			break;
	}

	if ($hora != NULL) {
		switch ($first) {
			case 'F':
				$fechaNew .= ($style == 'upper') ? ' A LAS ' . $hora : ' a las ' . $hora;
				break;
			case 'H':
				$fechaNew = ($style == 'upper') ? $hora . ' DEL ' . $fechaNew : $hora . ' del ' . $fechaNew;
				break;
			default:
				$fechaNew .= ' ' . $hora;
				break;
		}
	}

	return $fechaNew;
}

function getNumberFormat($str, $cant = 4)
{
	$str = trim($str);
	$cantidad = strlen($str);

	$string = "";
	if ($cant > $cantidad) {
		while ($cantidad < $cant) {
			$string .= "0";
			$cantidad++;
		}
	}
	$string .= $str;
	return $string;
}

function formatString($string, $options = array('espacio' => true, 'guion' => true, 'guionbajo' => true, 'apostrofe' => true, 'comilla' => true, 'punto' => true, 'puntocoma' => true, 'dospuntos' => true))
{
	if (isset($options['espacio']) && $options['espacio'] == true)
		$string = str_replace(' ', '', $string);

	if (isset($options['guion']) && $options['guion'] == true)
		$string = str_replace('-', '', $string);

	if (isset($options['guionbajo']) && $options['guionbajo'] == true)
		$string = str_replace('_', '', $string);

	if (isset($options['apostrofe']) && $options['apostrofe'] == true)
		$string = str_replace('\'', '', $string);

	if (isset($options['comilla']) && $options['comilla'] == true)
		$string = str_replace('"', '', $string);

	if (isset($options['punto']) && $options['punto'] == true)
		$string = str_replace('.', '', $string);

	if (isset($options['puntocoma']) && $options['puntocoma'] == true)
		$string = str_replace(';', '', $string);

	if (isset($options['dospuntos']) && $options['dospuntos'] == true)
		$string = str_replace(':', '', $string);

	return $string;
}

function fecha_mysql_a_espanol($fecha){

  preg_match( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);

  $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];

  return $lafecha;

}