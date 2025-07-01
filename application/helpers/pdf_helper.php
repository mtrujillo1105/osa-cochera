<?php
if(!defined('BASEPATH'))exit('No direct script access allowed');

/*helper funcion ayuda a definir los margenes tipografía y creación del footer y números de pagína*/
function prep_pdf($orientation = 'portrait'){
    $CI =& get_instance();
    $CI->cezpdf->selectFont(APPPATH.'libraries/fonts/Helvetica.afm');
    //$CI->cezpdf->selectFont(APPPATH.'libraries/fonts/Courier.afm');
    $all = $CI->cezpdf->openObject();
    $CI->cezpdf->saveState();
    $CI->cezpdf->setStrokeColor(0,0,0,1);
    if($orientation == 'portrait') {
        $CI->cezpdf->ezSetMargins(35,10,50,20);
        //$CI->cezpdf->ezSetMargins(35,10,50,70);
        //$CI->cezpdf->ezStartPageNumbers(570,28,8,'','{PAGENUM}',1);
        //$CI->cezpdf->line(20,40,578,40);
        //$CI->cezpdf->addText(25,32,8,'Impreso ' . date('m/d/Y h:i:s a'));
    }
    else {
        //$CI->cezpdf->ezStartPageNumbers(750,28,8,'','{PAGENUM}',1);
        //$CI->cezpdf->line(20,40,800,40);
        //$CI->cezpdf->addText(25,32,8,'Impreso '.date('m/d/Y h:i:s a'));
    }
    $CI->cezpdf->restoreState();
    $CI->cezpdf->closeObject();
    $CI->cezpdf->addObject($all,'all');
}
function prep_pdf_horizontal($orientation = 'portrait'){
    $CI =& get_instance();
    $CI->cezpdf_horizontal->selectFont(APPPATH.'libraries/fonts/Courier.afm');
    //$CI->cezpdf->selectFont(APPPATH.'libraries/fonts/Courier.afm');
    $all = $CI->cezpdf_horizontal->openObject();
    $CI->cezpdf_horizontal->saveState();
    $CI->cezpdf_horizontal->setStrokeColor(0,0,0,1);
    if($orientation == 'portrait') {
        $CI->cezpdf_horizontal->ezSetMargins(35,10,50,20);
        //$CI->cezpdf->ezSetMargins(35,10,50,70);
        //$CI->cezpdf->ezStartPageNumbers(570,28,8,'','{PAGENUM}',1);
        //$CI->cezpdf->line(20,40,578,40);
        //$CI->cezpdf->addText(25,32,8,'Impreso ' . date('m/d/Y h:i:s a'));
    }
    else {
        $CI->cezpdf_horizontal->ezSetMargins(20,10,30,20);
        //$CI->cezpdf->ezStartPageNumbers(750,28,8,'','{PAGENUM}',1);
        //$CI->cezpdf->line(20,40,800,40);
        //$CI->cezpdf->addText(25,32,8,'Impreso '.date('m/d/Y h:i:s a'));
    }
    $CI->cezpdf_horizontal->restoreState();
    $CI->cezpdf_horizontal->closeObject();
    $CI->cezpdf_horizontal->addObject($all,'all');
}
?>
