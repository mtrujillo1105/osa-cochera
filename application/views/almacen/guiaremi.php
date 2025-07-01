<?php
session_start();
require_once "libreria/fpdf/fpdf.php";
class PDF extends FPDF{
	function Header(){
		global $numero;
		global $fecha;
		global $nombre_empresa;
		global $ruc_empresa;
		global $transportista;
		global $ruc_transportista;
		global $referencia;
		global $nombre_persona;
		global $telefono;
		global $fax;
		//$this->Image('imagenes/logo.jpg?=< ?=IMG;? >,11,4,30);
		$this->setFont("Arial","",9);
		$this->Cell(190,5,"CALLE HUARI N� 158 URB. MESA REDONDA - LOS OLIVOS -LIMA - Telfax: 533-7266     ventas@ferresat.com",1,1,"C",0);
		$this->setFont("Arial","",12);		
		$this->Cell(150,15,"ORDEN DE COMPRA N�",0,0,"R",0);
		$this->Cell(40,15,$numero,0,1,"L",0);
		$this->SetFont("Arial","",8);	
		$this->Cell(20,5,"Se�or(es):".$nombre_empresa,0,0,"L",0);
		$this->Cell(170,5,$nombre_empresa ,0,1,"L",0);		
		$this->Cell(20,5,"Nro de RUC:".$ruc_empresa,0,0,"L",0);
		$this->Cell(170,5,$nombre_empresa ,0,1,"L",0);		
		$this->Cell(20,5,"Direcci�n:".$ruc_empresa,0,0,"L",0);
		$this->Cell(170,5,$nombre_empresa ,0,1,"L",0);				
		$this->Cell(20,5,"",0,0,"L",0);
		$this->Cell(130,5,$nombre_empresa ,0,0,"L",0);	
		$this->Cell(20,5,"Doc.Ref.:",0,0,"L",0);
		$this->Cell(20,5,$referencia,0,1,"L",0);
		$this->Cell(20,5,"Atenci�n:",0,0,"L",0);
		$this->Cell(130,5,$nombre_persona ,0,0,"L",0);	
		$this->Cell(20,5,"Tel�fono:",0,0,"L",0);
		$this->Cell(20,5,$telefono,0,1,"L",0);	
		$this->Cell(70,5,"S�rvase tomar nota del siguiente pedido:",0,0,"L",0);
		$this->Cell(80,5,"",0,0,"L",0);	
		$this->Cell(20,5,"Fax N�:",0,0,"L",0);
		$this->Cell(20,5,$fax,0,1,"L",0);			
	}
	function Footer(){
	}
}

$pdf = new PDF("P");
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont("Arial","",8);
$color="";
$pdf->SetFillColor(0,0,128);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(10,6,"Item",1,0,"C",0);
$pdf->Cell(15,6,"Cantidad",1,0,"C",0);
$pdf->Cell(15,6,"U.med",1,0,"C",0);
$pdf->Cell(25,6,"C�digo",1,0,"C",0);
$pdf->Cell(90,6,"ARTICULO",1,0,"C",0);
$pdf->Cell(20,6,"Precio Unitario",1,0,"C",0);
$pdf->Cell(15,6,"IMPORTE",1,1,"C",0);

	$pdf->Cell(8,6,  $i,0,0,"C",0);
	$pdf->Cell(30,6,  $row['interno'],0,0,"L",0);
	$pdf->Cell(125,6,  $row['nombre'],0,0,"L",0);
	$pdf->Cell(20,6,  number_format($row['cantidad'],2),0,0,"R",0);
	$pdf->Cell(9,6, $row['unidadmedida'],0,1,"R",0);
	
$pdf->Output();
?>