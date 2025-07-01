<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* *********************************************************************************
Fecha: 07/10/2020
/* ******************************************************************************** */
use Dompdf\Dompdf;

class pdf{
	public function __construct(){
		require APPPATH.'/third_party/dompdf-v0.8.6/src/Dompdf.php';
	}
}
?>