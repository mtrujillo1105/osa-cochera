<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* *********************************************************************************
Autor: Martín Trujillo
Fecha: 11/12/2020
/* ******************************************************************************** */
require 'vendor/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class Excelspout{
  public function generate($data, $filename='report'){
      
    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToBrowser($filename.'.xlsx');
    
    /** Cabecera */
    $singleRow = WriterEntityFactory::createRowFromArray($data['header']);
    $writer->addRow($singleRow);
    
    /** Detalle */
    foreach($data['records'] as $value){
      $rowFromValues = WriterEntityFactory::createRowFromArray($value);
      $writer->addRow($rowFromValues);
    }

    /** Close*/
    $writer->close();
    
  }
}
?>