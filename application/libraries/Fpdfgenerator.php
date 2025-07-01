<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once(APPPATH . 'libraries/fpdf/fpdf.php');
include_once(APPPATH . 'libraries/barcode/barcode.php');

class Fpdfgenerator{
    
    public function generate($data, $filename='reporte',$stream = true){
        $pdf = new Fpdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);
        $pdf->SetTextColor(0,0,0);
        $hoy=date("Y-m-d",time());

        //Cabecera
        $cabecera = $data["header"];

        
        /*$pdf->Cell(35,25,"",0,1,"L",0);
        $pdf->Cell(20,6,"",0,0,"L",0);
        $pdf->Cell(30,6,$hoy,0,0,"L",0);
        $pdf->Cell(35,6,"",0,0,"L",0);
        $pdf->Cell(30,6,$hoy,0,1,"L",0);
        $pdf->Cell(0,10,"",0,1,"L",0);
        $pdf->Cell(20,6,"",0,0,"L",0);
         * 
         */
        
        //Detalle
        foreach ($data["records"] as $value)
        {
            $pdf->Cell(30,4, $value[0],0,0,"L",0);
            $pdf->Cell(80,4, $value[1],0,0,"L",0);
            $pdf->Cell(30,4, $value[2],0,0,"L",0);
            $pdf->Cell(10,4, $value[3],0,0,"R",0);
            $pdf->Cell(20,4, $value[4],0,1,"R",0);

        };        
        
        
        if ($stream) {
            $pdf->Output('D',$filename.".pdf");
        } else {
            $pdf->Output();
        }        
        
    }
    
    public function generate_barcode($data, $filename='reporte',$stream = true){
        $pdf = new Fpdf();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);
        $pdf->SetTextColor(0,0,0);
        $hoy=date("Y-m-d",time());

        $numero = $data["numero"];
        barcode(FCPATH.'/public/images/barcodes/'.$numero.'.png',$numero,60,'horizontal','code128',true);        
        
        //Cabecera
        $cabecera = $data["header"];
        
        //Detalle
        $detalle = $data["records"];
        
        /*$pdf->Cell(35,25,"",0,1,"L",0);
        $pdf->Cell(20,6,"",0,0,"L",0);
        $pdf->Cell(30,6,$hoy,0,0,"L",0);
        $pdf->Cell(35,6,"",0,0,"L",0);
        $pdf->Cell(30,6,$hoy,0,1,"L",0);
        $pdf->Cell(0,10,"",0,1,"L",0);
        $pdf->Cell(20,6,"",0,0,"L",0);
         * 
         */

        $pdf->Image(base_url().'/public/images/barcodes/'.$numero.'.png',10,100,50,0,'PNG');
        
        if ($stream) {
            $pdf->Output('D',$filename.".pdf");
        } else {
            $pdf->Output();
        }        
        
    }    
}