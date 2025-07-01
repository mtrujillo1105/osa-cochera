<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* *********************************************************************************
Autor: Martín Trujillo
Fecha: 11/12/2020
/* ******************************************************************************** */
require 'vendor/autoload.php';
use Dompdf\Dompdf;

class Pdfgenerator{
  public function generate($html, $filename = '', $stream = true, $paper = 'A4', $orientation = "portrait"){
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper($paper, $orientation);
    $dompdf->render();
    if ($stream) {
        //$dompdf->stream($filename.".pdf", array("Attachment" => 0));
        $dompdf->stream($filename.".pdf");
    } else {
        $dompdf->output();
    }
  }
}
?>