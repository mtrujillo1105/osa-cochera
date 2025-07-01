<?php
include("application/libraries/Cezpdf.php");
//include("application/libraries/class.backgroundpdf.php");

class Rpttesoreria extends CI_Controller {
    private $empresa;
    private $compania;
    private $usuario;
    private $usuario_nombre;
    private $rol;
    private $rol_nombre;
    private $url;
    
   public function __construct(){
        parent::__construct();
        $this->load->model('maestros/proyecto_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/compania_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('tesoreria/banco_model');
        $this->load->model('tesoreria/caja_model');
        $this->load->model('tesoreria/movimiento_model');
        $this->load->model('tesoreria/tipocaja_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('reportes/rpttesoreria_model');
        $this->load->model('reportes/rptventas_model');
        $this->load->library('html');
        $this->load->library('layout','layout');
        $this->load->library('lib_props');
        $this->compania = $this->session->userdata('compania');
        $this->view_js = array(0 => "reportes/rpttesoreria.js");
    }
    
    public function index(){
       $this->movimientos_caja();	
    }
    
    public function movimientos_caja(){
        $data['base_url'] = base_url();
        $data['caja'] = $this->caja_model->getCajas();
        $data['forma_pago'] = $this->formapago_model->getFpagos();
        $data['scripts'] = array(0 => "reportes/movimientos_caja.js");
        $this->layout->view('reportes/movimientos_caja', $data);
    }

    public function datatable_movimiento(){
        $posDT = -1;         
        $columnas = array(
                            ++$posDT  => "CAJAMOV_FechaRecep",
                            ++$posDT  => "CAJA_Nombre",
                            ++$posDT  => "CPC_Serie",
                            ++$posDT  => "CPC_Numero",
                            ++$posDT  => "FORPAC_Descripcion",
                            ++$posDT  => "CAJAMOV_Monto",
                            ++$posDT  => "movimiento"
                        );
        
        $filter = new stdClass();
        $filter->start = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];
        $ordenar = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
            $filter->order = $columnas[$ordenar];
            $filter->dir = $this->input->post("order")[0]["dir"];
        }
        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        
        $filter->caja = $this->input->post('codigo');
        $filter->nombre = $this->input->post('descripcion');
        $filter->fpago  = $this->input->post('fpago');
        $filter->fechai = $this->input->post('fechai');
        $filter->fechaf = $this->input->post('fechaf');
        
        $movimientosInfo = $this->rpttesoreria_model->getMovimientos($filter);
        
        $records = array();
        if ($movimientosInfo["records"] != NULL) {
            foreach ($movimientosInfo["records"]  as $indice => $valor) {

                $colors = ($valor->CAJAMOV_MovDinero == 2) ? "color-red" : "color-green";
                
                $posDT = -1;
                $records[] = array(
                                    ++$posDT => mysql_to_human($valor->CAJAMOV_FechaRecep),
                                    ++$posDT => $valor->CAJA_Nombre,
                                    ++$posDT => $valor->CPC_Serie,
                                    ++$posDT => $valor->CPC_Numero != "" ? str_pad($valor->CPC_Numero,4,0,STR_PAD_LEFT) : "",
                                    ++$posDT => $valor->FORPAC_Descripcion,
                                    ++$posDT => $valor->CAJAMOV_Monto,
                                    ++$posDT => "<span class='bold $colors'>$valor->movimiento</span>"
                                );
            }
        }

        unset($filter->start);
        unset($filter->length);
        
        $recordsTotal = ($movimientosInfo["recordsTotal"] != NULL) ? $movimientosInfo["recordsTotal"] : 0;
        $recordsFilter = $movimientosInfo["recordsFilter"];        

        $json = array(
                    "draw"            => intval( $this->input->post('draw') ),
                    "recordsTotal"    => $recordsTotal,
                    "recordsFiltered" => $recordsFilter,
                    "data"            => $records
        ); 

        echo json_encode($json);
        die;
    }
    
    public function movimientos_cajaExcel(){
        
        $this->load->library('Excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Resumen Detallado de Ventas');

        $estiloTitulo = array(
                                'font' => array(
                                    'name'      => 'Calibri',
                                    'bold'      => true,
                                    'color'     => array(
                                        'rgb' => '000000'
                                    ),
                                    'size' => 11
                                ),
                                'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'          => TRUE
                                )
                            );

        $estiloColumnasTitulo = array(
                                    'font' => array(
                                        'name'      => 'Calibri',
                                        'bold'      => true,
                                        'color'     => array(
                                            'rgb' => '000000'
                                        ),
                                        'size' => 10
                                    ),
                                    'fill'  => array(
                                        'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'ECF0F1')
                                    ),
                                    'alignment' =>  array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                            'wrap'          => TRUE
                                    )
                                );

        $estiloColumnasPar = array(
                                    'font' => array(
                                        'name'      => 'Calibri',
                                        'bold'      => false,
                                        'color'     => array(
                                            'rgb' => '000000'
                                        ),
                                        'size' => 9
                                    ),
                                    'alignment' =>  array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                            'wrap'          => TRUE
                                    )
                                );

        $estiloColumnasImpar = array(
                                    'font' => array(
                                        'name'      => 'Calibri',
                                        'bold'      => false,
                                        'color'     => array(
                                            'rgb' => '000000'
                                        ),
                                        'size' => 9
                                    ),
                                    'fill'  => array(
                                        'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'DCDCDCDC')
                                    ),
                                    'alignment' =>  array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                            'wrap'          => TRUE
                                    )
                                );
        $estiloBold = array(
                                'font' => array(
                                    'name'      => 'Calibri',
                                    'bold'      => true,
                                    'color'     => array(
                                        'rgb' => '000000'
                                    ),
                                    'size' => 9
                                )
                            );

        $this->excel->getActiveSheet()->getStyle("A1:G2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:G3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:G2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:G3")->setCellValue("A3", "REPORTE DE MOVIMIENTOS DE CAJA");
        
        $lugar = 4;
        $numeroS = 0;
        
        //Datos ventas diarias
        $filter = new stdClass();
        $filter->fechai = $this->input->post("search_fechai");
        $filter->fechaf = $this->input->post("search_fechaf");
        $filter->fpago  = $this->input->post("search_fpago");
        $filter->caja   = $this->input->post("search_caja");
        
        $ventasdiarias = $this->rpttesoreria_model->getMovimientos($filter);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA MOV..");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "CAJA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "SERIE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "FORMA PAGO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "MONTO S/.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "TIPO MOVIMENTO");
        $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($ventasdiarias != NULL){
            $lugar++;
            
            foreach($ventasdiarias["records"] as $indice => $valor){

                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->CAJAMOV_FechaRecep)
                ->setCellValue("B$lugar", $valor->CAJA_Nombre)
                ->setCellValue("C$lugar", $valor->CPC_Serie)
                ->setCellValue("D$lugar", $valor->CPC_Numero)
                ->setCellValue("E$lugar", $valor->FORPAC_Descripcion)
                ->setCellValue("F$lugar", $valor->CAJAMOV_Monto)
                ->setCellValue("G$lugar", $valor->movimiento);
                
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasImpar);
                $lugar++;
            }
            $lugar++;
        }

        $this->excel->getActiveSheet()->getColumnDimension("D")->setWidth("25");
        $this->excel->getActiveSheet()->getColumnDimension("E")->setWidth("25");

        for($i = 'A'; $i <= 'O'; $i++){
            if ($i != 'D' && $i != 'E')
            $this->excel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(true);
        }

        
        $filename = "Reporte movimientos de caja ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }
}
