<?php
include("application/libraries/Cezpdf.php");
//include("application/libraries/class.backgroundpdf.php");

class Rptventas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Lima');             
        $this->load->helper('form');
        $this->load->helper('util');
        $this->load->library('lib_props');
        $this->load->model('reportes/rptventas_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('ventas/comprobantedetalle_model');
        $this->load->model('ventas/parqueo_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/tarifa_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->library('lib_comprobantes');
        $this->somevar['user'] = $this->session->userdata('user');
        $this->somevar['rol'] = $this->session->userdata('rol');
        $this->somevar['empresa'] = $this->session->userdata('empresa');
    }

    public function filtroVendedor() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';
        $data['cboVendedor'] = $this->lib_props->listarVendedores();

        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_vendedor_resumen($data['fecha_inicio'], $data['fecha_fin']);
            $data['mensual'] = $this->rptventas_model->ventas_por_vendedor_mensual($data['fecha_inicio'], $data['fecha_fin']);
            $data['anual'] = $this->rptventas_model->ventas_por_vendedor_anual($data['fecha_inicio'], $data['fecha_fin']);
        }
        $this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function filtroVendedorExcel($fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

            $resumen = $this->rptventas_model->ventas_por_vendedor_resumen($f_ini, $f_fin);
            $mensual = $this->rptventas_model->ventas_por_vendedor_mensual($f_ini, $f_fin);
            $anual = $this->rptventas_model->ventas_por_vendedor_anual($f_ini, $f_fin);
        
        $this->load->library('Excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Ventas Por Vendedor');
        
        $TipoFont = array( 'font'  => array( 'bold'  => false, 'color' => array('rgb' => '000000'), 'size'  => 14, 'name'  => 'Calibri'));
        $TipoFont2 = array( 'font'  => array( 'bold'  => false, 'color' => array('rgb' => '000000'), 'size'  => 12, 'name'  => 'Calibri'));
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $style2 = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

        $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($TipoFont);
        $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($style);

        $this->excel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($TipoFont);
        $this->excel->getActiveSheet()->getStyle("A3:N3")->applyFromArray($style);

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('40');
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth('18');

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:E2')->setCellValue('A1', $_SESSION['nombre_empresa']);
        
        $this->excel->getActiveSheet()->getStyle("A3:E3")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A3:E3")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:E3")->setCellValue("A3", "REPORTE DESDE $f_ini HASTA $f_fin");
        
        $this->excel->setActiveSheetIndex(0)->setCellValue('A4', 'N');
        $this->excel->setActiveSheetIndex(0)->setCellValue('B4', 'VENDEDOR');
        $this->excel->setActiveSheetIndex(0)->setCellValue('C4', 'FECHA DESDE');
        $this->excel->setActiveSheetIndex(0)->setCellValue('D4', 'FECHA HASTA');
        $this->excel->setActiveSheetIndex(0)->setCellValue('E4', 'VENTA');
    
        #$this->excel->setActiveSheetIndex(0);
        $numeroS = 0;
        $lugar = 5;

        foreach($resumen as $col)
            $keys = array_keys($col);
        
        foreach($resumen as $indice => $valor){
            $numeroS+=1;
            $ventas=$valor[$keys[0]];
            $nombre=$valor[$keys[1]];
            $paterno=$valor[$keys[2]];

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre $paterno")
            ->setCellValue('C'.$lugar, $f_ini)
            ->setCellValue('D'.$lugar, $f_fin)
            ->setCellValue('E'.$lugar, $ventas);
            $lugar+=1;    
        }

        $numeroS = 0;
        $lugar += 4;
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A$lugar:E$lugar")->setCellValue("A$lugar", "REPORTE MENSUAL");
        $lugar++;
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "N");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "NOMBRE");
        #$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "VENTAS");

        foreach($mensual as $col)
            $keys = array_keys($col);

        $size = count($keys);
        $lcol = $lugar;
        $lugar++;

        foreach($mensual as $indice => $valor){ // listo todos los meses seleccionados
            for ($x = 2; $x < $size; $x++){
                $mes = substr($keys[$x], -1); // obtengo el mes
                $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+1)."$lcol", $this->lib_props->mesesEs($mes));
            }
        }
        

        foreach($mensual as $indice => $valor){
            $numeroS+=1;
            $nombre=$valor[$keys[0]];
            $paterno=$valor[$keys[1]];

            for ($x = 2; $x < $size; $x++){ // 2 posicion de array donde inician ventas
                if ( $valor[$keys[$x]] != "" ){
                    $ventas = $valor[$keys[$x]];
                    $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+1)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna D
                    #break;
                }
            }

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre $paterno");
            $lugar+=1;
        }

        $numeroS = 0;
        $lugar += 4;
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "REPORTE ANUAL");
        $lugar++;
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "N");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "NOMBRE");
        
        foreach($anual as $col)
            $keys = array_keys($col); // obtengo las llaves

        $size = count($keys);
        $lcol = $lugar;
        $lugar++;

        foreach($anual as $indice => $valor){ // listo todos los años seleccionados
            for ($x = 2; $x < $size; $x++){
                $anio = substr($keys[$x], 1); // obtengo el año
                $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+1)."$lcol",$anio);
            }
        }
        

        foreach($anual as $indice => $valor){
            $numeroS+=1;
            $nombre=$valor[$keys[0]];
            $paterno=$valor[$keys[1]];

            for ($x = 2; $x < $size; $x++){ // 2 posicion de array donde inician ventas
                if ( $valor[$keys[$x]] != "" ){
                    $ventas = $valor[$keys[$x]];
                    $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+1)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna D
                    #break;
                }
            }

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre $paterno");
            $lugar+=1;
        }

        $filename = "Ventas De Vendedorre ".date('Y-m-d').".xls"; //save our workbook as this file name

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
        #$this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function ticket_emitidos(){
        $data["tarifas"] = $this->tarifa_model->getTarifas();
        $data["cajeros"] = $this->usuario_model->listar_usuarios();
        $data['scripts']  = array(0 => "reportes/ticket_emitidos.js");		
        $this->layout->view("reportes/ticket_emitidos",$data);
    }       
    
    public function datatable_tickets_emitidos(){
        $posDT = -1;
         $columnas = array(
             ++$posDT => "PARQP_Codigo",
             ++$posDT => "PARQC_Placa",
             ++$posDT => "PARQC_FechaIngreso",
             ++$posDT => "PARQC_HoraIngreso",
             ++$posDT => "PARQC_FechaSalida",
             ++$posDT => "PARQC_HoraSalida",
             ++$posDT => "PARQC_Tiempo",
             ++$posDT => "PARQC_Monto",
             ++$posDT => "TARIFC_Descripcion",
             ++$posDT => "CPC_SerieNumero",
             ++$posDT => "CAJA_Nombre",
             ++$posDT => "PARQC_FlagSituacion",
             ++$posDT => "PARQC_FechaRegistro"
         );
         $filter = new stdClass();
         $filter->start = $this->input->post("start");
         $filter->length = $this->input->post("length");
         $filter->search = $this->input->post("search")["value"];
         $ordenar        = $this->input->post("order");
         if (!is_null($ordenar)){
             foreach($ordenar as $indice => $value){
                 $arrOrd[$indice] = [$columnas[$value["column"]],$value["dir"]];
             }
             $filter->ordenar = $arrOrd;
         }
         $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
         
        $filter->placa  = $this->input->post('placa');
        $filter->tarifa = $this->input->post('tarifa');
        $filter->fechaing  = $this->input->post('fechaing');
        $filter->fechasal = $this->input->post('fechasal');
        $filter->cajero   = $this->input->post('cajero');
         
         $tarifasInfo = $this->rptventas_model->ticket_emitidos($filter);
         $records = array();
         if ( $tarifasInfo["records"] != NULL ) {
           foreach ($tarifasInfo["records"] as $indice => $valor) {

               //Datos cajero
               $nomusu   = "";                
               $cajero   = $valor->CAJA_Usuario;
               if($cajero != NULL){
                   $datosusu = $this->usuario_model->getUsuario($cajero);
                   $nomusu   = substr($datosusu[0]->PERSC_Nombre,0,1).".".$datosusu[0]->PERSC_ApellidoPaterno;    
               }

               //Situiacion ticket
               $situacion    = $valor->PARQC_FlagSituacion;
               $msgSituacion = "";

               switch($situacion){
                   case 0:
                       $msgSituacion = "<span class='color-red'>ANULADO</span>";
                       break;
                   case 1://En la cochera
                       $msgSituacion = "<span class='color-black'>EMITIDO</span>";
                       break;
                   case 2://Calculado, ya tiene precio
                       $msgSituacion = "<span class='color-yellow'>PENDIENTE</span>";
                       break;
                   case 3:
                       $msgSituacion = "<span class='color-green'>FACTURADO.</span>";
                       break;                    
               }            

               $posDT = -1;
               $records[] = array(
                       ++$posDT => $valor->PARQC_Numero,
                       ++$posDT => $valor->PARQC_Placa,
                       ++$posDT => $valor->PARQC_FechaIngreso,
                       ++$posDT => $valor->PARQC_HoraIngreso,          
                       ++$posDT => $valor->PARQC_FechaSalida,   
                       ++$posDT => $valor->PARQC_HoraSalida,   
                       ++$posDT => $this->lib_comprobantes->convertir_min_horamin($valor->PARQC_Tiempo),   
                       ++$posDT => number_format($valor->PARQC_Monto,2),
                       ++$posDT => $valor->TARIFC_Descripcion,
                       ++$posDT => $valor->CPC_Numero != "" ? $valor->CPC_Serie."-".str_pad($valor->CPC_Numero,4,0,STR_PAD_LEFT):"",
                       ++$posDT => $nomusu,
                       ++$posDT => $msgSituacion,
                       ++$posDT => $this->lib_comprobantes->convertir_fecha_numero($valor->PARQC_FechaRegistro)
               );
           }
         }
         $recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
         $recordsFilter = $tarifasInfo["recordsFilter"];
         $recordsAcum   = $tarifasInfo["recordsAcum"];
         $json = array(
                   "draw"            => intval( $this->input->post('draw') ),
                   "recordsTotal"    => $recordsTotal,
                   "recordsAcum"     => $recordsAcum,
                   "recordsFiltered" => $recordsFilter,
                   "data"            => $records
         );
         echo json_encode($json);
         die();
    }
    
    public function ticket_emitidosExcel(){
        
        $this->load->library('Excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Reporte de Tickets emitidos');

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

        $this->excel->getActiveSheet()->getStyle("A1:L2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:L3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:L2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:L3")->setCellValue("A3", "REPORTE DE TICKETS EMITIDOS");
        
        $lugar = 4;
        $numeroS = 0;
        
        //Datos ventas diarias
        $filter = new stdClass();
        $filter->tarifa   = $this->input->post("tarifa");
        $filter->cajero   = $this->input->post("cajero");
        $filter->fechaing = $this->input->post("fechaing");
        $filter->fechasal = $this->input->post("fechasal");
        $filter->placa    = $this->input->post("placa");

        $ticketemitidos = $this->rptventas_model->ticket_emitidos($filter);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "PLACA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "FECHA INGRESO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "HORA INGRESO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "FECHA SALIDA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "HORA SALIDA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "TIEMPO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "MONTO S/.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "TARIFA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "NUM. COMPROBANTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", "CAJERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("L$lugar", "SITUACIÓN");
        $this->excel->getActiveSheet()->getStyle("A$lugar:L$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($ticketemitidos != NULL){
            $lugar++;
            
            foreach($ticketemitidos["records"] as $indice => $valor){

               //Situiacion ticket
               $msgSituacion = "";
               switch($valor->PARQC_FlagSituacion){
                   case 0:
                       $msgSituacion = "ANULADO";
                       break;
                   case 1://En la cochera
                       $msgSituacion = "EMITIDO";
                       break;
                   case 2://Calculado, ya tiene precio
                       $msgSituacion = "PENDIENTE";
                       break;
                   case 3:
                       $msgSituacion = "FACTURADO";
                       break;                    
               }       
               
               //Datos cajero
               $nomusu   = "";                
               $cajero   = $valor->CAJA_Usuario;
               if($cajero != NULL){
                   $datosusu = $this->usuario_model->getUsuario($cajero);
                   $nomusu   = substr($datosusu[0]->PERSC_Nombre,0,1).".".$datosusu[0]->PERSC_ApellidoPaterno;    
               }               
                
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->PARQC_Numero)
                ->setCellValue("B$lugar", $valor->PARQC_Placa)
                ->setCellValue("C$lugar", $valor->PARQC_FechaIn)
                ->setCellValue("D$lugar", $valor->PARQC_HoraIn)
                ->setCellValue("E$lugar", $valor->PARQC_FechaSalida)
                ->setCellValue("F$lugar", $valor->PARQC_HoraSalida)
                ->setCellValue("G$lugar", $valor->PARQC_Tiempo)
                ->setCellValue("H$lugar", $valor->PARQC_Monto)
                ->setCellValue("I$lugar", $valor->TARIFC_Descripcion)
                ->setCellValue("J$lugar", $valor->CPC_SerieNumero)
                ->setCellValue("K$lugar", $nomusu)
                ->setCellValue("L$lugar", $msgSituacion);
                
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:L$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:L$lugar")->applyFromArray($estiloColumnasImpar);
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

        
        $filename = "Reporte de tickets emitidos ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }
    
    public function filtroVendedorExcelDet($vendedor = "0", $fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

        $this->load->library('Excel');
        $hoja = 0;
        $this->excel->setActiveSheetIndex($hoja);
        $this->excel->getActiveSheet()->setTitle('Ventas por cajero');

        $estiloTitulo = array(
                                'font' => array(
                                    'name'      => 'Calibri',
                                    'bold'      => true,
                                    'color'     => array('rgb' => '000000'),
                                    'size' => 14
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
                                            'color'     => array('rgb' => '000000'),
                                            'size' => 11
                                        ),
                                        'fill'  => array(
                                            'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('argb' => 'ECF0F1')
                                        ),
                                        'alignment' =>  array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                            'wrap'          => TRUE
                                        ),
                                        'borders' => array(
                                            'allborders' => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                'color' => array( 'rgb' => "000000")
                                            )
                                        )
                                    );

        $estiloColumnasPar = array(
                                    'font' => array(
                                        'name'      => 'Calibri',
                                        'bold'      => false,
                                        'color'     => array(
                                            'rgb' => '000000'
                                        )
                                    ),
                                    'fill'  => array(
                                        'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'FFFFFFFF')
                                    ),
                                    'alignment' =>  array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                            'wrap'          => TRUE
                                    ),
                                    'borders' => array(
                                        'allborders' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array( 'rgb' => "000000")
                                        )
                                    )
                                );

        $estiloColumnasImpar = array(
                                    'font' => array(
                                        'name'  => 'Calibri',
                                        'bold'  => false,
                                        'color' => array('rgb' => '000000')
                                    ),
                                    'fill'  => array(
                                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'DCDCDCDC')
                                    ),
                                    'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'       => TRUE
                                    ),
                                    'borders' => array(
                                        'allborders' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array( 'rgb' => "000000")
                                        )
                                    )
                                );
        
        $estiloBold = array(
                            'font' => array(
                                'name'      => 'Calibri',
                                'bold'      => true,
                                'color'     => array('rgb' => '000000'),
                                'size' => 11
                            )
                        );

        ###########################################################################
        ###### HOJA 0 VENTAS POR VENDEDOR
        ###########################################################################
        
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $this->excel->getActiveSheet()->getStyle("A1:K2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:K3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:K2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex($hoja)->mergeCells("A3:K3")->setCellValue("A3", "VENTAS POR CAJERO DESDE $f_ini HASTA $f_fin");

        $lugar = 4;
        $vendedor = ($vendedor == 0) ? "" : $vendedor;
        
        $listaVendedores = $this->usuario_model->listar_vendedores();
            
            foreach ($listaVendedores as $indice => $data) {
                $numeroS = 0;
                $fpago = NULL;

                $resumen = $this->rptventas_model->ventas_por_vendedor_general_suma($data->USUA_Codigo, $f_ini, $f_fin);
                $detalle = $this->rptventas_model->ventas_por_vendedor_detallado($data->USUA_Codigo, $f_ini, $f_fin);

                if ($resumen != NULL){
                    foreach($resumen as $indice => $valor){
                        $numeroS += 1;

                        if ($numeroS == 1){
                            $lugarN = $lugar + 1;
                            foreach($detalle as $i => $val){
                                $this->excel->getActiveSheet()->getStyle("A$lugar:C$lugarN")->applyFromArray($estiloBold);
                                $this->excel->setActiveSheetIndex($hoja)
                                ->setCellValue("A$lugar", "DNI: $val->PERSC_NumeroDocIdentidad")
                                ->setCellValue("A$lugarN", "VENDEDOR: $val->PERSC_Nombre $val->PERSC_ApellidoPaterno $val->PERSC_ApellidoMaterno");
                                break;
                            }
                            $lugar += 1;
                        }
                    }

                    $lugar++;
                    $numeroS = 0;

                    foreach($detalle as $indice => $valor){
                        $numeroS += 1;

                        if ($numeroS == 1){
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", 'N');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", 'FORMA DE PAGO');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("C$lugar", 'CÓDIGO');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("D$lugar", 'RUC Y RAZON SOCIAL');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("E$lugar", 'SERIE');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", 'NÚMERO');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", 'TOTAL');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar", 'FECHA');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("I$lugar", 'NOTA CREDITO RELACIONADA');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("J$lugar", 'TOTAL');
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue("K$lugar", 'FECHA');

                            $this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasTitulo);
                            $lugar++;
                        }
                        
                        $this->excel->setActiveSheetIndex($hoja)
                        ->setCellValue("A$lugar", $numeroS)
                        ->setCellValue("B$lugar", $valor->FORPAC_Descripcion)
                        ->setCellValue("C$lugar", $valor->CLIC_CodigoUsuario)
                        ->setCellValue("D$lugar", $valor->nombre_cliente)
                        ->setCellValue("E$lugar", $valor->CPC_Serie)
                        ->setCellValue("F$lugar", $valor->CPC_Numero)
                        ->setCellValue("G$lugar", number_format($valor->CPC_Total,2))
                        ->setCellValue("H$lugar", $valor->CPC_Fecha)
                        ->setCellValue("I$lugar", $valor->CRED_Serie."-".$valor->CRED_Numero)
                        ->setCellValue("J$lugar", $valor->CRED_Total)
                        ->setCellValue("K$lugar", $valor->CRED_Fecha);

                        if ($indice % 2 == 0)
                            $this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasPar);
                        else
                            $this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasImpar);

                        $lugar+=1;
                        $fpago = $valor->FORPAC_Descripcion;
                    }
                    $lugar++;
                }
            }

            for($i = 'B'; $i <= 'K'; $i++){
                $this->excel->setActiveSheetIndex($hoja)->getColumnDimension($i)->setAutoSize(true);
            }

        ###########################################################################
        ###### HOJA 1 VENTAS POR PRODUCTO SEGUN VENDEDOR
        ###########################################################################
            $productosInfo = $this->rptventas_model->ventas_por_producto_de_vendedor($f_ini, $f_fin);
            $col = count($productosInfo[0]);
            $split = $col - intval( $col / 2 ) + 7;
            $colE = $this->lib_props->colExcel( $split );
            $size = count($productosInfo);
            
            $hoja++;
            $this->excel->createSheet($hoja);
            $this->excel->setActiveSheetIndex($hoja); //Seleccionar la pestaña deseada
            $this->excel->getActiveSheet()->setTitle('Ventas por producto'); //Establecer nombre

            $this->excel->getActiveSheet()->getStyle('A1:'.$colE.'2')->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle('A3:'.$colE.'3')->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:'.$colE.'2')->setCellValue('A1', $_SESSION['nombre_empresa']);
            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A3:'.$colE.'3')->setCellValue("A3", "REPORTE DE VENTAS POR PRODUCTO SEGUN VENDEDOR. DESDE $f_ini HASTA $f_fin");

            $numeroS = 0;
            $lugar = 5;
            
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "CODIGO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "NOMBRE");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("C$lugar", "MARCA");


            $lugarTU = 4;
            $lugarT = $lugar;


            $this->excel->getActiveSheet()->getStyle("A$lugarTU:$colE$lugarT")->applyFromArray($estiloColumnasTitulo);
            $lugar++;

            for ($i = "D"; $i <= $colE; $i++){
                $this->excel->setActiveSheetIndex($hoja)->setCellValue("$i$lugarT", "CANTIDAD" );
                $i++;
                $this->excel->setActiveSheetIndex($hoja)->setCellValue("$i$lugarT", "TOTAL S/ " );
            }

            foreach($productosInfo as $nCol)
                $keys = array_keys($nCol);

            $merge = true;

            for ($x = 0; $x < $size; $x++){
                $vendedor = 0;
                $it = 4;
                for ($j = 0; $j < $col; $j++) {

                    if ( $keys[$j] != "vendedor".$vendedor ){
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel( $j + 1 - $vendedor)."$lugar", $productosInfo[$x][ $keys[$j] ] );
                    }
                    
                    if ( $keys[$j] == "vendedor".$vendedor )
                        $vendedor++;

                    if ( $x == 0 ){

                        $c1 = $this->lib_props->colExcel( $it );
                        $c2 = $this->lib_props->colExcel( $it + 1 );
                        $cols = "$c1$lugarTU:$c2$lugarTU";

                        if ( $c2 > $colE)
                            $merge = false;

                        if ($merge == true){
                            #$this->excel->setActiveSheetIndex($hoja)->mergeCells($cols)->setCellValue($this->lib_props->colExcel($it).$lugarTU, $productosInfo[$x]["vendedor$j"] );
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel($it).$lugarTU, $productosInfo[$x]["vendedor$j"] );
                            $it += 2;
                        }
                    }
                }

                #$this->excel->setActiveSheetIndex($hoja)->setCellValue("$colE$lugarT", "TOTAL" );

                if ($x % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasImpar);

                $lugar++;
            }

            $this->excel->getActiveSheet()->getColumnDimension("A")->setWidth('18');
            $this->excel->getActiveSheet()->getColumnDimension("B")->setWidth('30');
            $this->excel->getActiveSheet()->getColumnDimension("B")->setWidth('20');
            for ($i = "D"; $i <= $colE; $i++)
                $this->excel->getActiveSheet()->getColumnDimension($i)->setWidth('11');

        ###########################################################################
        ###### HOJA 2 VENTAS POR MARCA SEGUN VENDEDOR
        ###########################################################################
            $marcasInfo = $this->rptventas_model->ventas_por_marca_de_vendedor($f_ini, $f_fin);
            $col = count($marcasInfo[0]);
            $split = $col - intval( $col / 2 ) + 2;
            $colE = $this->lib_props->colExcel( $split );
            $size = count($marcasInfo);
            
            $hoja++;
            $this->excel->createSheet($hoja);
            $this->excel->setActiveSheetIndex($hoja); //Seleccionar la pestaña deseada
            $this->excel->getActiveSheet()->setTitle('Ventas por Lab. segun vendedor'); //Establecer nombre

            $this->excel->getActiveSheet()->getStyle('A1:'.$colE.'2')->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle('A3:'.$colE.'3')->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:'.$colE.'2')->setCellValue('A1', $_SESSION['nombre_empresa']);
            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A3:'.$colE.'3')->setCellValue("A3", "REPORTE DE VENTAS POR MARCA SEGUN VENDEDOR. DESDE $f_ini HASTA $f_fin");

            $numeroS = 0;
            $lugar = 4;
            
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "N");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "MARCA");

            $lugarT = $lugar;
            
            $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasTitulo);
            $lugar++;

            foreach($marcasInfo as $nCol)
                $keys = array_keys($nCol); // obtengo las llaves

            for ($x = 0; $x < $size; $x++){
                $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", $x + 1);
                $vendedor = 0;
                for ($j = 0; $j < $col; $j++) {

                    if ( $keys[$j] != "vendedor".$vendedor ){
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel( $j + 2 - $vendedor)."$lugar", $marcasInfo[$x][ $keys[$j] ] );
                    }
                    
                    if ( $keys[$j] == "vendedor".$vendedor )
                        $vendedor++;

                    if ( $x == 0 )
                        $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel( $j + 3 )."$lugarT", $marcasInfo[$x]["vendedor$j"] );

                }

                $this->excel->setActiveSheetIndex($hoja)->setCellValue("$colE$lugarT", "TOTAL" );

                if ($x % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasImpar);

                $lugar++;
            }

            for ($i = "B"; $i <= $colE; $i++)
                $this->excel->getActiveSheet()->getColumnDimension($i)->setWidth('20');
        
        $filename = "Ventas por vendedor ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }

    public function filtroVendedorExcelGeneral($fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

        $this->load->library('Excel');        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Ventas Por Cajero');
        
        $estiloTitulo = array(
                                'font' => array(
                                    'name'  => 'Calibri',
                                    'bold'  => true,
                                    'color' => array('rgb' => '000000'),
                                    'size'  => 11
                                ),
                                'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'       => TRUE
                                )
                            );

        $estiloColumnasTitulo = array(
                                    'font' => array(
                                        'name'  => 'Calibri',
                                        'bold'  => true,
                                        'color' => array('rgb' => '000000'),
                                        'size'  => 11
                                    ),
                                    'fill'  => array(
                                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'ECF0F1')
                                    ),
                                    'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'       => TRUE
                                    )
                                );

        $estiloColumnasPar = array(
                                    'font' => array(
                                        'name'  => 'Calibri',
                                        'bold'  => false,
                                        'color' => array('rgb' => '000000')
                                    ),
                                    'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'       => TRUE
                                    )
                                );

        $estiloColumnasImpar = array(
                                    'font' => array(
                                        'name'  => 'Calibri',
                                        'bold'  => false,
                                        'color' => array('rgb' => '000000')
                                    ),
                                    'fill'  => array(
                                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => 'DCDCDCDC')
                                    ),
                                    'alignment' =>  array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'wrap'       => TRUE
                                    )
                                );
        
        $estiloBold = array(
                            'font' => array(
                                'name'  => 'Calibri',
                                'bold'  => true,
                                'color' => array('rgb' => '000000'),
                                'size'  => 11
                            )
                        );

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $this->excel->getActiveSheet()->getStyle("A1:H2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:H3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:H2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:H3")->setCellValue("A3", "VENTAS POR CAJERO DESDE $f_ini HASTA $f_fin");
        
        $listaVendedores = $this->usuario_model->listar_vendedores();
                        
        $lugar = 5;
        
        foreach ($listaVendedores as $indice => $data) {
            $numeroS = 0;
            $fpago = NULL;

            $detalle = $this->rptventas_model->ventas_por_vendedor_general($data->USUA_Codigo, $f_ini, $f_fin);

            if ($detalle != NULL){
                foreach($detalle as $indice => $valor){
                    $numeroS += 1;

                    if ($numeroS == 1){
                        foreach($detalle as $i => $val){
                            $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloBold);
                            $this->excel->setActiveSheetIndex(0)->mergeCells("A$lugar:H$lugar")->setCellValue("A$lugar", "VENDEDOR: $val->vendedor");
                            break;
                        }
                        $lugar += 1;

                        $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasTitulo);
                        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", 'N');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", 'FORMA DE PAGO');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", 'FACTURAS');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", 'BOLETAS');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", 'COMPROBANTES');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", 'TOTAL');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", 'NOTAS DE CREDITO');
                        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", 'VENTAS - NOTAS DE CREDITO');
                        $lugar++;
                    }
                    
                    $this->excel->setActiveSheetIndex(0)
                    ->setCellValue("A$lugar", $numeroS)
                    ->setCellValue("B$lugar", $valor->FORPAC_Descripcion)
                    ->setCellValue("C$lugar", number_format($valor->totalFacturas,2))
                    ->setCellValue("D$lugar", number_format($valor->totalBoletas,2))
                    ->setCellValue("E$lugar", number_format($valor->totalComprobantes,2))
                    ->setCellValue("F$lugar", number_format($valor->total,2))
                    ->setCellValue("G$lugar", number_format($valor->totalNotas,2))
                    ->setCellValue("H$lugar", number_format($valor->total - $valor->totalNotas,2));

                    if ($indice % 2 == 0)
                        $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasPar);
                    else
                        $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasImpar);
                    $lugar++;
                }
                $lugar++;
                $numeroS = 0;
            }
            
        }
        
        for($i = 'A'; $i <= 'C'; $i++){
            $this->excel->setActiveSheetIndex(0)            
                ->getColumnDimension($i)->setAutoSize(true);
        }

        
        $filename = "Ventas por Vendedor General ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }

    public function filtroCliente() {
        
        $data['fecha_inicio'] = date("Y-m-d");
        $data['fecha_fin'] = date("Y-m-d");
        
        $data['cliente'] = "";
        $data['nombre_cliente'] = "";
        $data['buscar_cliente'] = "";
    
        if (isset($_POST['reporteT']) and $_POST['reporteT'] == 'cliente') {
            $data['cliente'] = $_POST['cliente'];
            $data['nombre_cliente'] = $_POST['nombre_cliente'];
            $data['buscar_cliente'] = $_POST['buscar_cliente'];
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_cliente_resumen($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
            $data['mensual'] = $this->rptventas_model->ventas_por_cliente_mensual($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
            $data['anual'] = $this->rptventas_model->ventas_por_cliente_anual($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
        }
        else
            if (isset($_POST['reporteT']) and $_POST['reporteT'] == 'general') {
                $data['fecha_inicio'] = $_POST['fecha_inicio'];
                $data['fecha_fin'] = $_POST['fecha_fin'];
                $data['resumen'] = $this->rptventas_model->ventas_por_cliente_resumen_general($data['fecha_inicio'], $data['fecha_fin']);
                $data['mensual'] = $this->rptventas_model->ventas_por_cliente_mensual_general($data['fecha_inicio'], $data['fecha_fin']);
                $data['anual'] = $this->rptventas_model->ventas_por_cliente_anual_general($data['fecha_inicio'], $data['fecha_fin']);
            }
        $this->layout->view('reportes/ventas_por_cliente', $data);
    }

    public function filtroProveedor() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';
    
        if ($_POST['reporteT'] == 'cliente') {
            $data['cliente'] = $_POST['cliente'];
            $data['nombre_cliente'] = $_POST['nombre_cliente'];
            $data['buscar_cliente'] = $_POST['buscar_cliente'];
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_cliente_resumen($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
            $data['mensual'] = $this->rptventas_model->ventas_por_cliente_mensual($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
            $data['anual'] = $this->rptventas_model->ventas_por_cliente_anual($data['fecha_inicio'], $data['fecha_fin'],$data['cliente']);
        }
        else
            if ($_POST['reporteT'] == 'general') {
                $data['fecha_inicio'] = $_POST['fecha_inicio'];
                $data['fecha_fin'] = $_POST['fecha_fin'];
                $data['resumen'] = $this->rptventas_model->ventas_por_proveedor_resumen_general($data['fecha_inicio'], $data['fecha_fin']);
                $data['mensual'] = $this->rptventas_model->ventas_por_proveedor_mensual_general($data['fecha_inicio'], $data['fecha_fin']);
                $data['anual'] = $this->rptventas_model->ventas_por_proveedor_anual_general($data['fecha_inicio'], $data['fecha_fin']);
            }
        $this->layout->view('reportes/compras_por_proveedor', $data);
    }

    public function resumen_ventas_detallado($fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

        
        $this->load->library('Excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Resumen Detallado de Ventas');
        
        ###########################################
        ######### ESTILOS
        ###########################################
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

        $this->excel->getActiveSheet()->getStyle("A1:O2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:O3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:O2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:O3")->setCellValue("A3", "DETALLE DE VENTAS DESDE $f_ini HASTA $f_fin");
        
        $lugar = 4;
        $numeroS = 0;

        $resumen = $this->rptventas_model->resumen_ventas_detallado($f_ini, $f_fin);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA DOC.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FECHA REG.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "SERIE/NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "CLIENTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "NOMBRE DE PRODUCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "LAB.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "LOTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "FECHA VCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "CANTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "P/U");
        $this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", "TOTAL");
        $this->excel->setActiveSheetIndex(0)->setCellValue("L$lugar", "NOTA DE CREDITO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("M$lugar", "CANTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("N$lugar", "P/U");
        $this->excel->setActiveSheetIndex(0)->setCellValue("O$lugar", "TOTAL");
        $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($resumen != NULL){
            $lugar++;
            foreach($resumen as $indice => $valor){
                $fRegistro = explode(" ", $valor->CPC_FechaRegistro);
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->CPC_Fecha)
                ->setCellValue("B$lugar", $fRegistro[0])
                ->setCellValue("C$lugar", $valor->CPC_Serie." - ".$valor->CPC_Numero)
                ->setCellValue("D$lugar", $valor->clienteEmpresa.$valor->clientePersona)
                ->setCellValue("E$lugar", $valor->PROD_Nombre)
                ->setCellValue("F$lugar", $valor->MARCC_CodigoUsuario)
                ->setCellValue("G$lugar", $valor->LOTC_Numero)
                ->setCellValue("H$lugar", $valor->LOTC_FechaVencimiento)
                ->setCellValue("I$lugar", $valor->CPDEC_Cantidad)
                ->setCellValue("J$lugar", $valor->CPDEC_Pu_ConIgv)
                ->setCellValue("K$lugar", $valor->CPDEC_Total)
                ->setCellValue("L$lugar", $valor->CRED_Serie."-".$valor->CRED_Numero)
                ->setCellValue("M$lugar", $valor->CREDET_Cantidad)
                ->setCellValue("N$lugar", $valor->CREDET_Pu_ConIgv)
                ->setCellValue("O$lugar", $valor->CREDET_Total);
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasImpar);
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

        
        $filename = "Reporte de ventas ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }
    
    public function resumen_ventastienda_detallado($fechai = NULL, $fechaf = NULL){
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

        $this->load->library('Excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Resumen Detallado de Ventas');

        $estiloTitulo = array(
                                'font' => array(
                                    'name'      => 'Calibri',
                                    'bold'      => true,
                                    'color'     => array('rgb' => '000000'),
                                    'size' => 11
                                ),
                                'alignment'  =>  array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap'       => TRUE
                                )
                            );

        $estiloColumnasTitulo = array(
                                        'font' => array(
                                            'name'  => 'Calibri',
                                            'bold'  => true,
                                            'color' => array('rgb' => '000000'),
                                            'size'  => 10
                                        ),
                                        'fill'  => array(
                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
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
                                        'color'     => array('rgb' => '000000'),
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
                                        'color'     => array('rgb' => '000000'),
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

        $this->excel->getActiveSheet()->getStyle("A1:P2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:P3")->applyFromArray($estiloColumnasTitulo);
        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:P2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:P3")->setCellValue("A3", "DETALLE DE VENTAS POR LOCAL");
        
        $lugar = 4;
        $numeroS = 0;

        $resumen = $this->rptventas_model->resumen_ventas_detallado($f_ini, $f_fin);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA DOC.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FECHA REG.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "SERIE/NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "CLIENTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "NOMBRE DE PRODUCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "LAB.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "LOTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "FECHA VCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "CANTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "P/U");
        $this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", "TOTAL");
        $this->excel->setActiveSheetIndex(0)->setCellValue("L$lugar", "NOTA DE CREDITO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("M$lugar", "CANTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("N$lugar", "P/U");
        $this->excel->setActiveSheetIndex(0)->setCellValue("O$lugar", "TOTAL");
        $this->excel->setActiveSheetIndex(0)->setCellValue("P$lugar", "LOCAL");
        $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($resumen != NULL){
            $lugar++;
            foreach($resumen as $indice => $valor){
                $fRegistro = explode(" ", $valor->CPC_FechaRegistro);
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->CPC_Fecha)
                ->setCellValue("B$lugar", $fRegistro[0])
                ->setCellValue("C$lugar", $valor->CPC_Serie." - ".$valor->CPC_Numero)
                ->setCellValue("D$lugar", $valor->clienteEmpresa.$valor->clientePersona)
                ->setCellValue("E$lugar", $valor->PROD_Nombre)
                ->setCellValue("F$lugar", $valor->MARCC_CodigoUsuario)
                ->setCellValue("G$lugar", $valor->LOTC_Numero)
                ->setCellValue("H$lugar", $valor->LOTC_FechaVencimiento)
                ->setCellValue("I$lugar", $valor->CPDEC_Cantidad)
                ->setCellValue("J$lugar", $valor->CPDEC_Pu_ConIgv)
                ->setCellValue("K$lugar", $valor->CPDEC_Total)
                ->setCellValue("L$lugar", $valor->CRED_Serie."-".$valor->CRED_Numero)
                ->setCellValue("M$lugar", $valor->CREDET_Cantidad)
                ->setCellValue("N$lugar", $valor->CREDET_Pu_ConIgv)
                ->setCellValue("O$lugar", $valor->CREDET_Total)
                ->setCellValue("P$lugar", $valor->EESTABC_Descripcion);
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasImpar);
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

        
        $filename = "Reporte de ventas por local ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }

    public function resumen_compras_detallado($fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

        
        $this->load->library('Excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Resumen Detallado de Compras');
        
        ###########################################
        ######### ESTILOS
        ###########################################
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

        $this->excel->getActiveSheet()->getStyle("A1:I2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:I3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:I2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:I3")->setCellValue("A3", "DETALLE DE COMPRAS DESDE $f_ini HASTA $f_fin");
        
        $lugar = 4;
        $numeroS = 0;

        $resumen = $this->rptventas_model->resumen_compras_detallado($f_ini, $f_fin);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA DOC.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FECHA ING.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "SERIE/NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "PROVEEDOR");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "NOMBRE DE PRODUCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "LAB.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "LOTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "FECHA VCTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "CANTIDAD");
        $this->excel->getActiveSheet()->getStyle("A$lugar:I$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($resumen != NULL){
            $lugar++;
            foreach($resumen as $indice => $valor){
                $fRegistro = explode(" ", $valor->CPC_FechaRegistro);
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->CPC_Fecha)
                ->setCellValue("B$lugar", $fRegistro[0])
                ->setCellValue("C$lugar", $valor->CPC_Serie." - ".$valor->CPC_Numero)
                ->setCellValue("D$lugar", $valor->proveedorEmpresa.$valor->proveedorPersona)
                ->setCellValue("E$lugar", $valor->PROD_Nombre)
                ->setCellValue("F$lugar", $valor->MARCC_CodigoUsuario)
                ->setCellValue("G$lugar", $valor->LOTC_Numero)
                ->setCellValue("H$lugar", $valor->LOTC_FechaVencimiento)
                ->setCellValue("I$lugar", $valor->CPDEC_Cantidad);
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:I$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:I$lugar")->applyFromArray($estiloColumnasImpar);
                $lugar++;
            }
            $lugar++;
        }

        $this->excel->getActiveSheet()->getColumnDimension("D")->setWidth("25");
        $this->excel->getActiveSheet()->getColumnDimension("E")->setWidth("25");

        for($i = 'A'; $i <= 'I'; $i++){
            if ($i != 'D' && $i != 'E')
            $this->excel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(true);
        }

        
        $filename = "Reporte de compras ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }
    
    public function filtroTienda() {
        $monthf = date('m');
        $yearf = date('Y');
        $monthi = date('m');
        $yeari = date('Y');
        //date('Y-m-d', mktime(0,0,0, $monthf, $dayf, $yearf))

        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';

        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_tienda_resumen($data['fecha_inicio'], $data['fecha_fin']);
            $data['mensual'] = $this->rptventas_model->ventas_por_tienda_mensual($data['fecha_inicio'], $data['fecha_fin']);
            $data['anual'] = $this->rptventas_model->ventas_por_tienda_anual($data['fecha_inicio'], $data['fecha_fin']);
        }
         
        $this->layout->view('reportes/ventas_por_tienda', $data);
    }

    public function filtroMarca() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';

        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_marca_resumen($data['fecha_inicio'], $data['fecha_fin']);
            $data['mensual'] = $this->rptventas_model->ventas_por_marca_mensual($data['fecha_inicio'], $data['fecha_fin']);
            $data['anual'] = $this->rptventas_model->ventas_por_marca_anual($data['fecha_inicio'], $data['fecha_fin']);
        }
        $this->layout->view('reportes/ventas_por_marca', $data);
    }

    public function filtroMarcaExcel($fechai = NULL, $fechaf = NULL) {
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

            $resumen = $this->rptventas_model->ventas_por_marca_resumen($f_ini, $f_fin);
            $mensual = $this->rptventas_model->ventas_por_marca_mensual($f_ini, $f_fin);
            $anual = $this->rptventas_model->ventas_por_marca_anual($f_ini, $f_fin);

        $this->load->library('Excel');
        $hoja = 0;
        $this->excel->setActiveSheetIndex($hoja);
        $this->excel->getActiveSheet()->setTitle('Ventas Por MARCA');
        
        ###########################################
        ######### ESTILOS
        ###########################################
            $estiloTitulo = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 14
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
                                                'size' => 11
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
                                                )
                                            ),
                                            'fill'  => array(
                                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('argb' => 'FFFFFFFF')
                                            ),
                                            'alignment' =>  array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                    'wrap'          => TRUE
                                            ),
                                            'borders' => array(
                                                'allborders' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                    'color' => array( 'rgb' => "000000")
                                                )
                                            )
                                        );

            $estiloColumnasImpar = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => false,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                )
                                            ),
                                            'fill'  => array(
                                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('argb' => 'DCDCDCDC')
                                            ),
                                            'alignment' =>  array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                    'wrap'          => TRUE
                                            ),
                                            'borders' => array(
                                                'allborders' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                    'color' => array( 'rgb' => "000000")
                                                )
                                            )
                                        );
            $estiloBold = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 11
                                            )
                                        );

        ###########################################################################
        ###### HOJA 0 VENTAS POR MARCA
        ###########################################################################
            
            $this->excel->getActiveSheet()->getStyle("A1:E2")->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle("A3:E3")->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:E2')->setCellValue('A1', $_SESSION['nombre_empresa']);

            $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('40');
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('18');
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('18');
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('18');
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('18');
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth('18');

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:E2')->setCellValue('A1', $_SESSION['nombre_empresa']);
            $this->excel->setActiveSheetIndex($hoja)->mergeCells("A3:E3")->setCellValue("A3", "REPORTE DE VENTAS POR MARCA DESDE $f_ini HASTA $f_fin");
            
            $this->excel->setActiveSheetIndex($hoja)->setCellValue('A4', 'N');
            $this->excel->setActiveSheetIndex($hoja)->setCellValue('B4', 'MARCA');
            $this->excel->setActiveSheetIndex($hoja)->setCellValue('C4', 'FECHA DESDE');
            $this->excel->setActiveSheetIndex($hoja)->setCellValue('D4', 'FECHA HASTA');
            $this->excel->setActiveSheetIndex($hoja)->setCellValue('E4', 'VENTA');
            
            $lugar = 4;
            $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasTitulo);
            
            $numeroS = 0;
            $lugar++;

            foreach($resumen as $col)
                $keys = array_keys($col);
            
            foreach($resumen as $indice => $valor){
                $numeroS += 1;
                $nombre = $valor[$keys[0]];
                $ventas = $valor[$keys[1]];

                $this->excel->setActiveSheetIndex($hoja)
                ->setCellValue('A'.$lugar, $numeroS)
                ->setCellValue('B'.$lugar, "$nombre")
                ->setCellValue('C'.$lugar, $f_ini)
                ->setCellValue('D'.$lugar, $f_fin)
                ->setCellValue('E'.$lugar, $ventas);

                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasImpar);

                $lugar+=1;
            }

            $numeroS = 0;
            $lugar += 4;

            $this->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:B$lugar")->setCellValue("A$lugar", "REPORTE MENSUAL");
            $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasTitulo);
            $ltituloMensual = $lugar;
            $lugar++;
            
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "N");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "MARCA");
            #$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "VENTAS");
            $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasTitulo);

            foreach($mensual as $col)
                $keys = array_keys($col);

            $size = count($keys);
            $lcol = $lugar;
            $lugar++;

            foreach($mensual as $indice => $valor){ // listo todos los meses seleccionados
                for ($x = 1; $x < $size; $x++){
                    if ( strlen($keys[$x]) == 7  ) // Entre Octubre y diciembre son 7 caracteres por ello descuento del array $keys 2 caracteres y ese es el mes.
                        $mes = substr($keys[$x], -2);
                    else 
                        $mes = substr($keys[$x], -1);
                    
                    $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel($x+2)."$lcol", $this->lib_props->mesesEs($mes));
                    $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lcol")->applyFromArray($estiloColumnasTitulo);
                    $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$ltituloMensual")->applyFromArray($estiloColumnasTitulo);
                }
            }
            
            foreach($mensual as $indice => $valor){
                $numeroS += 1;
                $nombre = $valor[$keys[0]];
                
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasImpar);


                for ($x = 1; $x < $size; $x++){ // 1 posicion de array donde inician ventas
                    if ( $valor[$keys[$x]] != "" ){
                        $ventas = $valor[$keys[$x]];
                        $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel($x+2)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna C
                        #break;

                        if ($indice % 2 == 0)
                            $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lugar")->applyFromArray($estiloColumnasPar);
                        else
                            $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lugar")->applyFromArray($estiloColumnasImpar);
                    }
                }

                $this->excel->setActiveSheetIndex($hoja)->setCellValue('A'.$lugar, $numeroS)->setCellValue('B'.$lugar, "$nombre");
                $lugar+=1;
            }

            $numeroS = 0;
            $lugar += 4;
            $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasTitulo);
            $this->excel->setActiveSheetIndex($hoja)->mergeCells("A$lugar:C$lugar")->setCellValue("A$lugar", "REPORTE ANUAL");
            $lugar++;

            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "N");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "MARCA");
            $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasTitulo);
            $ltituloMensual = $lugar;
            
            foreach($anual as $col)
                $keys = array_keys($col); // obtengo las llaves

            $size = count($keys);
            $lcol = $lugar;
            $lugar++;

            foreach($anual as $indice => $valor){ // listo todos los años seleccionados
                for ($x = 1; $x < $size; $x++){
                    $anio = substr($keys[$x], 1); // obtengo el año
                    $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel($x+2)."$lcol",$anio);
                    $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lcol")->applyFromArray($estiloColumnasTitulo);
                    $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$ltituloMensual")->applyFromArray($estiloColumnasTitulo);
                }
            }
            
            foreach($anual as $indice => $valor){
                $numeroS += 1;
                $nombre = $valor[$keys[0]];

                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:B$lugar")->applyFromArray($estiloColumnasImpar);

                for ($x = 1; $x < $size; $x++){ // 1 posicion de array donde inician ventas
                    if ( $valor[$keys[$x]] != "" ){
                        $ventas = $valor[$keys[$x]];
                        $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel($x+2)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna C
                        #break;
                        if ($indice % 2 == 0)
                            $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lugar")->applyFromArray($estiloColumnasPar);
                        else
                            $this->excel->getActiveSheet()->getStyle($this->lib_props->colExcel($x+2)."$lugar")->applyFromArray($estiloColumnasImpar);
                    }
                }

                $this->excel->setActiveSheetIndex($hoja)->setCellValue('A'.$lugar, $numeroS)->setCellValue('B'.$lugar, "$nombre");
                $lugar+=1;
            }

        ###########################################################################
        ###### HOJA 1 VENTAS POR MARCA SEGUN VENDEDOR
        ###########################################################################
            $marcasInfo = $this->rptventas_model->ventas_por_marca_de_vendedor($f_ini, $f_fin);
            $col = count($marcasInfo[0]);
            $split = $col - intval( $col / 2 ) + 2;
            $colE = $this->lib_props->colExcel( $split );
            $size = count($marcasInfo);
            
            $hoja++;
            $this->excel->createSheet($hoja);
            $this->excel->setActiveSheetIndex($hoja); //Seleccionar la pestaña deseada
            $this->excel->getActiveSheet()->setTitle('Ventas por Lab. Segun Vendedor'); //Establecer nombre

            $this->excel->getActiveSheet()->getStyle('A1:'.$colE.'2')->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle('A3:'.$colE.'3')->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:'.$colE.'2')->setCellValue('A1', $_SESSION['nombre_empresa']);
            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A3:'.$colE.'3')->setCellValue("A3", "REPORTE DE VENTAS POR MARCA SEGUN VENDEDOR. DESDE $f_ini HASTA $f_fin");

            $numeroS = 0;
            $lugar = 4;
            
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "N");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "MARCA");

            $lugarT = $lugar;
            
            $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasTitulo);
            $lugar++;

            foreach($marcasInfo as $nCol)
                $keys = array_keys($nCol); // obtengo las llaves

            for ($x = 0; $x < $size; $x++){
                $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", $x + 1);
                $vendedor = 0;
                for ($j = 0; $j < $col; $j++) {

                    if ( $keys[$j] != "vendedor".$vendedor ){
                            $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel( $j + 2 - $vendedor)."$lugar", $marcasInfo[$x][ $keys[$j] ] );
                    }
                    
                    if ( $keys[$j] == "vendedor".$vendedor )
                        $vendedor++;

                    if ( $x == 0 )
                        $this->excel->setActiveSheetIndex($hoja)->setCellValue($this->lib_props->colExcel( $j + 3 )."$lugarT", $marcasInfo[$x]["vendedor$j"] );

                }

                $this->excel->setActiveSheetIndex($hoja)->setCellValue("$colE$lugarT", "TOTAL" );

                if ($x % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:$colE$lugar")->applyFromArray($estiloColumnasImpar);

                $lugar++;
            }

            for ($i = "B"; $i <= $colE; $i++)
                $this->excel->getActiveSheet()->getColumnDimension($i)->setWidth('20');


        $filename = "Ventas por MARCA desde ".$f_ini." hasta ".$f_fin.".xls"; //save our workbook as this file name

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
        #$this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function filtroFamilia() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';

        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_familia_resumen($data['fecha_inicio'], $data['fecha_fin']);
            $data['mensual'] = $this->rptventas_model->ventas_por_familia_mensual($data['fecha_inicio'], $data['fecha_fin']);
            $data['anual'] = $this->rptventas_model->ventas_por_familia_anual($data['fecha_inicio'], $data['fecha_fin']);
        }
        $this->layout->view('reportes/ventas_por_familia', $data);
    }

    public function filtroFamiliaExcel($fechai = NULL, $fechaf = NULL) {
        #
        $fechaI = explode("-", $fechai);
        $fechaF = explode("-", $fechaf);
        #$f_ini = ($fechaI == NULL) ? date("Y-").date("m-")."-01" : "$fechaI[2]-$fechaI[1]-$fechaI[0]";
        $f_ini = ($fechai == NULL) ? date("Y-").date("m-")."-01" : "$fechai";
        #$f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaF[5]-$fechaF[4]-$fechaF[3]";
        $f_fin = ($fechaF == NULL) ? date('Y-m-d') : "$fechaf";

            $resumen = $this->rptventas_model->ventas_por_familia_resumen($f_ini, $f_fin);
            $mensual = $this->rptventas_model->ventas_por_familia_mensual($f_ini, $f_fin);
            $anual = $this->rptventas_model->ventas_por_familia_anual($f_ini, $f_fin);
        
        $this->load->library('Excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Ventas Por Familia');
        
        $TipoFont = array( 'font'  => array( 'bold'  => false, 'color' => array('rgb' => '000000'), 'size'  => 14, 'name'  => 'Calibri'));
        $TipoFont2 = array( 'font'  => array( 'bold'  => false, 'color' => array('rgb' => '000000'), 'size'  => 12, 'name'  => 'Calibri'));
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $style2 = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

        $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($TipoFont);
        $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($style);

        $this->excel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($TipoFont);
        $this->excel->getActiveSheet()->getStyle("A3:N3")->applyFromArray($style);

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('40');
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('18');
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth('18');

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:E2')->setCellValue('A1', $_SESSION['nombre_empresa']);
        
        $this->excel->getActiveSheet()->getStyle("A3:E3")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A3:E3")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:E3")->setCellValue("A3", "REPORTE DE VENTAS POR FAMILIA DESDE $f_ini HASTA $f_fin");
        
        $this->excel->setActiveSheetIndex(0)->setCellValue('A4', 'N');
        $this->excel->setActiveSheetIndex(0)->setCellValue('B4', 'FAMILIA');
        $this->excel->setActiveSheetIndex(0)->setCellValue('C4', 'FECHA DESDE');
        $this->excel->setActiveSheetIndex(0)->setCellValue('D4', 'FECHA HASTA');
        $this->excel->setActiveSheetIndex(0)->setCellValue('E4', 'VENTA');
    
        #$this->excel->setActiveSheetIndex(0);
        $numeroS = 0;
        $lugar = 5;

        foreach($resumen as $col)
            $keys = array_keys($col);
        
        foreach($resumen as $indice => $valor){
            $numeroS += 1;
            $nombre = $valor[$keys[0]];
            $ventas = $valor[$keys[1]];

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre")
            ->setCellValue('C'.$lugar, $f_ini)
            ->setCellValue('D'.$lugar, $f_fin)
            ->setCellValue('E'.$lugar, $ventas);
            $lugar+=1;    
        }

        $numeroS = 0;
        $lugar += 4;
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A$lugar:E$lugar")->setCellValue("A$lugar", "REPORTE MENSUAL");
        $lugar++;
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "N");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FAMILIA");
        #$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "VENTAS");

        foreach($mensual as $col)
            $keys = array_keys($col);

        $size = count($keys);
        $lcol = $lugar;
        $lugar++;

        foreach($mensual as $indice => $valor){ // listo todos los meses seleccionados
            for ($x = 1; $x < $size; $x++){
                if ( strlen($keys[$x]) == 7  ) // Entre Octubre y diciembre son 7 caracteres por ello descuento del array $keys 2 caracteres y ese es el mes.
                    $mes = substr($keys[$x], -2);
                else 
                    $mes = substr($keys[$x], -1);
                
                $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+2)."$lcol", $this->lib_props->mesesEs($mes));
            }
        }
        
        foreach($mensual as $indice => $valor){
            $numeroS += 1;
            $nombre = $valor[$keys[0]];

            for ($x = 1; $x < $size; $x++){ // 1 posicion de array donde inician ventas
                if ( $valor[$keys[$x]] != "" ){
                    $ventas = $valor[$keys[$x]];
                    $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+2)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna C
                    #break;
                }
            }

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre");
            $lugar+=1;
        }

        $numeroS = 0;
        $lugar += 4;
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "REPORTE ANUAL");
        $lugar++;
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "N");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FAMILIA");
        
        foreach($anual as $col)
            $keys = array_keys($col); // obtengo las llaves

        $size = count($keys);
        $lcol = $lugar;
        $lugar++;

        foreach($anual as $indice => $valor){ // listo todos los años seleccionados
            for ($x = 1; $x < $size; $x++){
                $anio = substr($keys[$x], 1); // obtengo el año
                $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+2)."$lcol",$anio);
            }
        }
        

        foreach($anual as $indice => $valor){
            $numeroS += 1;
            $nombre = $valor[$keys[0]];

            for ($x = 1; $x < $size; $x++){ // 1 posicion de array donde inician ventas
                if ( $valor[$keys[$x]] != "" ){
                    $ventas = $valor[$keys[$x]];
                    $this->excel->setActiveSheetIndex(0)->setCellValue($this->lib_props->colExcel($x+2)."$lugar", $ventas); // x + 2 posision donde inician ventas + iniciar en columna C
                    #break;
                }
            }

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A'.$lugar, $numeroS)
            ->setCellValue('B'.$lugar, "$nombre");
            $lugar+=1;
        }

        $filename = "Ventas por Familia desde ".$f_ini." hasta ".$f_fin.".xls"; //save our workbook as this file name

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
        #$this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function filtroProducto() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';

        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_producto_resumen($data['fecha_inicio'], $data['fecha_fin']);
            $data['mensual'] = $this->rptventas_model->ventas_por_producto_mensual($data['fecha_inicio'], $data['fecha_fin']);
            $data['anual'] = $this->rptventas_model->ventas_por_producto_anual($data['fecha_inicio'], $data['fecha_fin']);
        }
        $this->layout->view('reportes/ventas_por_producto', $data);
    }
    
    public function Producto_stock() {
        
     
        $listado_productos = $this->rptventas_model->producto_stock();
        
        if(count($listado_productos)>0){
            foreach($listado_productos as $indice=>$valor){
                $nombre = $valor->PROD_Nombre;
                $fecha = $valor->fecha;
                $dias = $valor->dias;
                $lista[] = array($nombre,$fecha,$dias);
            }
        }
        $data['lista'] = $lista;
        $this->layout->view('reportes/producto_stock', $data);
    }
    
    public function filtroDiario() {
        
        $data['fecha_inicio'] = '';
        $data['fecha_fin'] = '';
        $data['totalCreditos1']=0;
        $data['totalCreditos2']=0;
        if (isset($_POST['reporte'])) {
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
            $data['fecha_fin'] = $_POST['fecha_fin'];
            $data['resumen'] = $this->rptventas_model->ventas_por_dia($data['fecha_inicio'], $data['fecha_fin']);
        }
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/ventas_por_dia', $data);
    }

    public function ventasdiario() {
        $data['titulo'] = "Ventas Diarias";
        $data['titulo_tabla'] = "Ventas del dia";
        $data['scripts'] = array(0 => "reportes/ventasdiario.js");	
        $this->layout->view('reportes/ventasdiario', $data);
    }
  
    public function ventasdiarioExcel(){

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

        $this->excel->getActiveSheet()->getStyle("A1:O2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:O3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:O2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:O3")->setCellValue("A3", "REPORTE REGISTRO DE VENTAS");
        
        $lugar = 4;
        $numeroS = 0;
        
        //Datos ventas diarias
        $filter = new stdClass();
        $filter->fechaini = $this->input->post("fechaini");
        $filter->fechafin = $this->input->post("fechafin");
        $filter->tipo_doc = $this->input->post("tipo_doc");
        $filter->numero_doc = $this->input->post("numero_doc");
        $filter->nro_ruc  = $this->input->post("nro_ruc");
        $filter->razon_social = $this->input->post("razon_social");
        
        $ventasdiarias = $this->rptventas_model->ventas_diarios2($filter);

        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA EMISIÓN.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "TIPO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "SERIE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "NOMBRE Y RAZON SOCIAL");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "RUC");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "FORMA DE PAGO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "VALOR VENTA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "IGV");
        $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "TOTAL IMPORTE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("k$lugar", "SITUACIÓN");
        $this->excel->getActiveSheet()->getStyle("A$lugar:k$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($ventasdiarias != NULL){
            $lugar++;
            
            foreach($ventasdiarias["records"] as $indice => $valor){

                $estado       = $valor->CPC_FlagEstado;
                switch ($estado){
                    case 0:
                        $msgEstado = "ANULADO";
                        break;
                    case 1:
                        $msgEstado = "APROBADO";
                        break;
                    case 2:
                        $msgEstado = "EMITIDO";
                        break;                        
                    default:
                        $msgEstado = "ERROR";
                }                
                
                $subtotal = $estado == 0 ? 0 : $valor->CPC_subtotal;
                $igv      = $estado == 0 ? 0 : $valor->CPC_igv;
                $total    = $estado == 0 ? 0 : $valor->CPC_total;
                
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $valor->CPC_Fecha)
                ->setCellValue("B$lugar", $valor->CPC_TipoDocumento)
                ->setCellValue("C$lugar", $valor->CPC_Serie)
                ->setCellValue("D$lugar", $valor->CPC_Numero)
                ->setCellValue("E$lugar", $valor->razon_social)
                ->setCellValue("F$lugar", $valor->numero_doc)
                ->setCellValue("G$lugar", $valor->FORPAC_Descripcion)
                ->setCellValue("H$lugar", $subtotal)
                ->setCellValue("I$lugar", $igv)
                ->setCellValue("J$lugar", $total)
                ->setCellValue("K$lugar", $msgEstado)
                ;
                
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasImpar);
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

        
        $filename = "Reporte registro de ventas ".date('Y-m-d').".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }
    
    public function datatable_ventasdiario(){
       $posDT = -1;        
       $columnas = array(
                        ++$posDT => "CPC_Fecha",
                        ++$posDT => "CPC_TipoDocumento",
                        ++$posDT => "CPC_Serie",
                        ++$posDT => "CPC_Numero",
                        ++$posDT => "CPC_Numero",
                        ++$posDT => "CPC_Numero",
                        ++$posDT => "CPC_Numero",
                        ++$posDT => "CPC_FlagEstado"
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
        
        $filter->fechaini = $this->input->post('fechaini');
        $filter->fechafin = $this->input->post('fechafin');
        $filter->tipo_doc = $this->input->post('tipo_doc');
        $filter->numero_doc = $this->input->post('numero_doc');
        $filter->nro_ruc  = $this->input->post('nro_ruc');
        $filter->razon_social = $this->input->post('razon_social');
        
        $ventasdiarias = $this->rptventas_model->ventas_diarios2($filter);
        
        $records = array();
        if ($ventasdiarias["records"] != NULL) {
            
            $valor_ventaS = 0;
            $valor_igvS   = 0;
            $valor_totalS = 0;            
                    
            foreach ($ventasdiarias["records"] as $indice => $valor) {
                
                $estado       = $valor->CPC_FlagEstado;
                $flag         = $valor->CPC_FlagEstado;
                $tipo_persona = $valor->CLIC_TipoPersona;
                $tipo_Moneda  = $valor->MONED_Simbolo;
                $razon_social = $valor->razon_social;
                $numero_doc   = $valor->numero_doc;
                $venta        = $estado == 0 ? "0.00" : $valor->CPC_subtotal;
                $igv          = $estado == 0 ? "0.00" : $valor->CPC_igv;
                $total        = $estado == 0 ? "0.00" : $valor->CPC_total;
                
                switch ($estado){
                    case 0:
                        $msgEstado = "<font color='red'>ANULADO</font>";
                        break;
                    case 1:
                        $msgEstado = "APROBADO";
                        break;
                    case 2:
                        $msgEstado = "EMITIDO";
                        break;                        
                    default:
                        $msgEstado = "ERROR";
                }

                $valor_ventaS += $venta;
                $valor_igvS += $igv;
                $valor_totalS +=$total;
								
                $posDT = -1;
                $records[] = array(
                                ++$posDT => mysql_to_human($valor->CPC_Fecha),
                                ++$posDT => $valor->CPC_TipoDocumento,
                                ++$posDT => $valor->CPC_Serie,
                                ++$posDT => $valor->CPC_Numero,
                                ++$posDT => $razon_social,
                                ++$posDT => $numero_doc,
                                ++$posDT => $valor->FORPAC_Descripcion,
                                ++$posDT => ($venta != NULL)?$venta:"0.00",
                                ++$posDT => ($igv != NULL)?$igv:"0.00",
                                ++$posDT => $tipo_Moneda." ".$total,
                                ++$posDT => $msgEstado
                );
            }
        }
        
        //unset($filter->start);
        //unset($filter->length);

        $recordsTotal = ($ventasdiarias["recordsTotal"] != NULL) ? $ventasdiarias["recordsTotal"] : 0;
        $recordsFilter = $ventasdiarias["recordsFilter"];	        
        
        $json = array(
                    "draw"            => intval( $this->input->post('draw') ),
                    "recordsTotal"    => $recordsTotal,
                    "recordsFiltered" => $recordsFilter,
                    "data"            => $records
        ); 
        
        echo json_encode($json);
    }

    public function ejecutarAjax(){
        $tipo_oper = $this->input->post('tipo_oper');
        $tipo = $this->input->post('tipo_doc');
        $mes = $this->input->post('mes');
        $anio = $this->input->post('anio');
        
        $lista = $this->rptventas_model->registro_ventas($tipo_oper, $tipo, $mes, $anio); 
        $RetornarTable = "";
        $RetornarTable .= '<table class="fuente8 tableReporte" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                            <tr class="cabeceraTabla ">
                            <td width="10%">FEHCA DE EMISION</td>
                            <td width="7%">TIPO</td>
                            <td width="5%">SERIE</td>
                            <td width="5%">NUMERO</td>
                            <td width="10%">NOMBRE Y/O RAZON SOCIAL</td>
                            <td width="5%">RUC</td>
                            <td width="5%">VALOR VENTA</td>
                            <td width="5%">I.G.V</td>
                            <td width="5%">TOTAL IMPORTE</td>
                            </tr>';

        if(count($lista)>0){
            $valor_ventaS = 0;
            $valor_igvS = 0;
            $valor_totalS =0;
            $valor_ventaD = 0;
            $valor_igvD = 0;
            $valor_totalD =0;

            foreach ($lista as $indice => $valor) {
                $fecha = $valor->CPC_Fecha;
                $tipo = $valor->CPC_TipoDocumento;
                $serie = $valor->CPC_Serie;
                $numero = $valor->CPC_Numero;
                $flag = $valor->CPC_FlagEstado;
                $tipo_persona = $valor->CLIC_TipoPersona;
                $tipo_proveedor = $valor->PROVC_TipoPersona;
                $tipo_Moneda=$valor->MONED_Simbolo;
                $cod_Moneda=$valor->MONED_Codigo;
                if ($flag == 1) {
                    $venta = $valor->CPC_subtotal;
                    $igv = $valor->CPC_igv;
                    $total = $valor->CPC_total;

                   if($cod_Moneda==1){
                    $valor_ventaS += $venta;
                    $valor_igvS += $igv;
                    $valor_totalS +=$total;}
                    if($cod_Moneda==2){
                    $valor_ventaD += $venta;
                    $valor_igvD += $igv;
                    $valor_totalD +=$total;}    
                    
                    
                    if ($tipo_oper == 'V') {
                        if ($tipo_persona == '0') {
                            $nombre = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                            $ruc = $valor->PERSC_Ruc;
                        } else {
                            $nombre = $valor->EMPRC_RazonSocial;
                            $ruc = $valor->EMPRC_Ruc;
                        }
                    } else {
                        if ($tipo_proveedor == '0') {
                            $nombre = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                            $ruc = $valor->PERSC_Ruc;
                        } else {
                            $nombre = $valor->EMPRC_RazonSocial;
                            $ruc = $valor->EMPRC_Ruc;
                        }
                    }
                }
                else {

                    $nombre = "ANULADO";
                    $ruc = "";
                    $venta = "";
                    $igv = "";
                    $total = "";
                }

                $RetornarTable.='<tr>
                <td><div align="center">'.$fecha.'</div></td>
                <td><div align="left">';
                
                if ($tipo == 'F')
                    $RetornarTable .= "Factura";
                else
                    if($tipo == 'B')
                        $RetornarTable.="Boleta";
                else
                    if($tipo == 'N')
                        $RetornarTable.="Comprobante";

                $RetornarTable .= '</div></td>';
                $RetornarTable .= '<td><div align="center">'.$serie.'</div></td>
                    <td><div align="center">'.$numero.'</div></td>
                    <td><div align="center">'.$nombre.'</div></td>
                    <td><div align="center">'.$ruc.'</div></td>';
                $RetornarTable .= '<td><div align="center">'.$valor_ventaS.'</div></td><td><div align="center">'.$valor_igvS.'</div></td>
                                    <td><div align="center">S/.'.number_format($valor_totalS, 2).'</div></td> ';
            }
        }
        else {
            $RetornarTable .= '<table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                    <tbody>
                                        <tr>
                                            <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                                        </tr>
                                    </tbody>
                                </table>';
        }

        echo $RetornarTable;
    }

    /*public function ventasdiario_fecha($tipo = 'F', $hoy) {
        
        $data['titulo'] = "Ventas Diarias";
        $data['tipo_docu'] = $tipo;
        $data['titulo_tabla'] = "Ventas del dia";
        $data['lista'] = $this->rptventas_model->ventas_diarios($tipo, $hoy);
        $data['fecha'] = $hoy;

        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/ventas_diarios', $data);
    }*/

    public function ventas_pdf($tipo_doc = "F", $fechaini, $hoy) {

        if ($tipo_doc == "F")
            $titulo = "REPORTE FACTURAS";
        if ($tipo_doc == "B")
            $titulo = "REPORTE BOLETAS";
        if ($tipo_doc == "N")
            $titulo = "REPORTE COMPROBANTES";
        
        $lista = $this->rptventas_model->ventas_diarios($tipo_doc,$fechaini,$hoy);
        $this->cezpdf = new Cezpdf('a4', 'landscape');
        $this->cezpdf->ezText(($titulo . "  DIARIO  "), 11, array("left" => 180));

        $this->cezpdf->ezText('', '');
        /* Listado de detalles */
        $db_data = array();
        $valor_venta = 0;
        $valor_igv = 0;
        $valor_total = 0;
        foreach ($lista as $indice => $valor) {
            $tipo = $valor->CPC_TipoDocumento;
            $tipo_persona = $valor->CLIC_TipoPersona;
            $flag = $valor->CPC_FlagEstado;
            $nombre = '';
            if ($flag == 1) {

                if ($tipo_doc != "F") {
                    $subtotal = number_format($valor->CPC_total / 1.18, 2);
                    $igv = number_format($subtotal * 0.18, 2);
                } else {
                    $igv = $valor->CPC_igv;
                    $subtotal = $valor->CPC_subtotal;
                }
                $total = $valor->CPC_total;
                $valor_venta +=$subtotal;
                $valor_igv +=$igv;
                $valor_total +=$total;


                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';

                if ($tipo_persona == '0') {
                    $nombre_cliente = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                    $ruc = $valor->PERSC_Ruc;
                } else {
                    $nombre_cliente = $valor->EMPRC_RazonSocial;
                    $ruc = $valor->EMPRC_Ruc;
                }
            } else {
                $nombre_cliente = "ANULADO";
                $ruc = "";
                $subtotal = "";
                $igv = "";
                $total = "";
            }

            $db_data[] = array(
                'cols1' => $valor->CPC_Fecha,
                'cols2' => $nombre,
                'cols3' => $valor->CPC_Serie,
                'cols4' => $valor->CPC_Numero,
                'cols5' => $nombre_cliente,
                'cols6' => $ruc,
                'cols7' => $subtotal,
                'cols8' => $igv,
                'cols9' => $total,
            );
        }
        $col_names = array(
            'cols1' => 'Fecha',
            'cols2' => 'Tipo',
            'cols3' => 'Serie',
            'cols4' => 'Numero',
            'cols5' => 'Cliente',
            'cols6' => 'Ruc',
            'cols7' => 'Valor Venta',
            'cols8' => '   I.G.V      ',
            'cols9' => 'Importe Total',
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 450,
            'showLines' => 2,
            'shaded' => 0,
            'Leading' => 10,
            'showHeadings' => 1,
            'xPos' => 300,
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 58, 'justification' => 'center'),
                'cols2' => array('width' => 42, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 45, 'justification' => 'left'),
                'cols5' => array('width' => 155, 'justification' => 'center'),
                'cols6' => array('width' => 66, 'justification' => 'left'),
                'cols7' => array('width' => 54, 'justification' => 'left'),
                'cols9' => array('width' => 48, 'justification' => 'left'),
                'cols9' => array('width' => 48, 'justification' => 'left')
            )
        ));

        $db_data = array(
            array(
                'cols1' => '',
                'cols2' => '',
                'cols3' => '',
                'cols4' => '',
                'cols5' => '',
                'cols6' => number_format($valor_venta, 2)
                , 'cols7' => number_format($valor_igv, 2),
                'cols8' => number_format($valor_total, 2)),
        );



        $this->cezpdf->ezText('', '');
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 505,
            'showLines' => 0,
            'shaded' => 20,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols1' => array('width' => 10, 'justification' => 'left'),
                'cols2' => array('width' => 10, 'justification' => 'left'),
                'cols3' => array('width' => 40, 'justification' => 'left'),
                'cols4' => array('width' => 45, 'justification' => 'left'),
                'cols5' => array('width' => 50, 'justification' => 'left'),
                'cols6' => array('width' => 55, 'justification' => 'left'),
                'cols7' => array('width' => 45, 'justification' => 'left'),
                'cols8' => array('width' => 55, 'justification' => 'left'),
            )
        ));




        $this->cezpdf->ezText('', 8);
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $tipo_doc . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function registro_ventas_pdf($tipo_oper, $tipo_doc = "F", $fecha1, $fecha2) {
        if ($tipo_oper == 'V') {
            $titulo_personal = 'Cliente';
            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  VENTAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  VENTAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  VENTAS COMPROBANTES";
        }

        else {

            $titulo_personal = 'Proveedor';

            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  COMPRAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  COMPRAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  COMPRAS COMPROBANTES";
        }
        $lista = $this->rptventas_model->registro_ventas($tipo_oper, $tipo_doc, $fecha1, $fecha2);
        $this->cezpdf = new Cezpdf('a4', 'landscape');
        $this->cezpdf->ezText(($titulo), 11, array("left" => 180));

        $this->cezpdf->ezText('', '');
        /* Listado de detalles */
        $db_data = array();
        $valor_venta = 0;
        $valor_igv = 0;
        $valor_total = 0;
        foreach ($lista as $indice => $valor) {
            $tipo = $valor->CPC_TipoDocumento;
            $tipo_persona = $valor->CLIC_TipoPersona;
            $flag = $valor->CPC_FlagEstado;
            $nombre = '';
            if ($flag == 1) {

                if ($tipo_doc != "F") {
                    $subtotal = number_format($valor->CPC_total / 1.18, 2);
                    $igv = number_format($subtotal * 0.18, 2);
                } else {
                    $igv = $valor->CPC_igv;
                    $subtotal = $valor->CPC_subtotal;
                }
                $total = $valor->CPC_total;
                $valor_venta +=$subtotal;
                $valor_igv +=$igv;
                $valor_total +=$total;


                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
                if ($tipo_persona == '0') {
                    $nombre_cliente = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                    $ruc = $valor->PERSC_Ruc;
                } else {
                    $nombre_cliente = $valor->EMPRC_RazonSocial;
                    $ruc = $valor->EMPRC_Ruc;
                }
            } else {
                $nombre_cliente = "ANULADO";
                $ruc = "";
                $subtotal = "";
                $igv = "";
                $total = "";
                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
            }

            $db_data[] = array(
                'cols1' => $valor->CPC_Fecha,
                'cols2' => $nombre,
                'cols3' => $valor->CPC_Serie,
                'cols4' => $valor->CPC_Numero,
                'cols5' => $nombre_cliente,
                'cols6' => $ruc,
                'cols7' => $subtotal,
                'cols8' => $igv,
                'cols9' => $total,
            );
        }
        $col_names = array(
            'cols1' => 'Fecha',
            'cols2' => 'Tipo',
            'cols3' => 'Serie',
            'cols4' => 'Numero',
            'cols5' => $titulo_personal,
            'cols6' => 'Ruc',
            'cols7' => 'Valor Venta',
            'cols8' => '   I.G.V      ',
            'cols9' => 'Importe Total',
        );

        $this->cezpdf->ezTable($db_data, $col_names, '', array(
            'width' => 450,
            'showLines' => 1,
            'shaded' => 1,
            'Leading' => 10,
            'showHeadings' => 1,
            'xPos' => 300,
            'fontSize' => 8,
            'cols' => array(
                'cols1' => array('width' => 58, 'justification' => 'center'),
                'cols2' => array('width' => 42, 'justification' => 'left'),
                'cols3' => array('width' => 35, 'justification' => 'left'),
                'cols4' => array('width' => 45, 'justification' => 'left'),
                'cols5' => array('width' => 155, 'justification' => 'center'),
                'cols6' => array('width' => 66, 'justification' => 'left'),
                'cols7' => array('width' => 54, 'justification' => 'left'),
                'cols9' => array('width' => 48, 'justification' => 'left'),
                'cols9' => array('width' => 48, 'justification' => 'left')
            )
        ));

        $db_data = array(
            array(
                'cols1' => '',
                'cols2' => '',
                'cols3' => '',
                'cols4' => '',
                'cols5' => '',
                'cols6' => number_format($valor_venta, 2)
                , 'cols7' => number_format($valor_igv, 2),
                'cols8' => number_format($valor_total, 2)),
        );



        $this->cezpdf->ezText('', '');
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 505,
            'showLines' => 0,
            'shaded' => 20,
            'showHeadings' => 0,
            'xPos' => 'center',
            'fontSize' => 9,
            'cols' => array(
                'cols1' => array('width' => 10, 'justification' => 'left'),
                'cols2' => array('width' => 10, 'justification' => 'left'),
                'cols3' => array('width' => 40, 'justification' => 'left'),
                'cols4' => array('width' => 45, 'justification' => 'left'),
                'cols5' => array('width' => 50, 'justification' => 'left'),
                'cols6' => array('width' => 55, 'justification' => 'left'),
                'cols7' => array('width' => 45, 'justification' => 'left'),
                'cols8' => array('width' => 55, 'justification' => 'left'),
            )
        ));




        $this->cezpdf->ezText('', 8);
        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $tipo_doc . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

     //AQUI EXCEL NUEVO
    public function resumen_ventas_mensual($tipo_oper = "V", $tipo = "", $fecha1 = "", $fecha2 = "", $forma_pago = "", $vendedor = "", $moneda = "", $consolidado="") {
                
        if (isset($tipo) && $tipo!="" && $tipo!="-") {
            $tipo = $tipo;
        }else{
            $tipo = "";
        }
        if (isset($forma_pago) && $forma_pago!="" && $forma_pago!="-") {
            $forma_pago = $forma_pago;
        }else{
            $forma_pago = "";
        }

        if (isset($vendedor) && $vendedor!="" && $vendedor!="-") {
            $vendedor = $vendedor;
        }else{
            $vendedor = "";
        }
        if (isset($moneda) && $moneda!="" && $moneda!="-") {
            $moneda = $moneda;
        }else{
            $moneda = "";
        }
        if (isset($fecha1) && $fecha1!="" && $fecha1!=1) {
            $fecha1 = $fecha1;
        }else{
            $fecha1 = date('Y-m-d');
        }
        if (isset($fecha2) && $fecha2!="" && $fecha2!=1) {
            $fecha2 = $fecha2;
        }else{
            $fecha2 = date('Y-m-d');
        }    
        switch ($tipo_oper) {
            case 'C':
                    $operacion = "COMPRA";
                break;
            case 'V':
                    $operacion = "VENTA";
                break;
            
            default:
                    $operacion = "";
                break;
        }

        $this->load->library('Excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle("Resumen De $operacion");
        
        ###########################################
        ######### ESTILOS #########################
        ###########################################
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
            $estiloColumnasAnuladoNota = array(
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
                                                'color' => array('argb' => 'D20505')
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

        $fecha_ini = explode("-", $fecha1);
        $fecha_fin = explode("-", $fecha2);
        $fecha_inicio = $fecha_ini[2]."/".$fecha_ini[1]."/".$fecha_ini[0];
        $fecha_final = $fecha_fin[2]."/".$fecha_fin[1]."/".$fecha_fin[0];

        $this->excel->getActiveSheet()->getStyle("A1:O2")->applyFromArray($estiloTitulo);
        $this->excel->getActiveSheet()->getStyle("A3:O3")->applyFromArray($estiloColumnasTitulo);

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:O2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:O3")->setCellValue("A3", "REPORTE DE $operacion del ".$fecha_inicio." hasta el ".$fecha_final);
        
        $lugar = 4;
        $numeroS = 0;

        $filter = new stdClass();
        $filter->tipo_oper      = $tipo_oper;
        $filter->tipo           = $tipo;
        $filter->fecha1         = $fecha1;
        $filter->fecha2         = $fecha2;
        $filter->forma_pago     = $forma_pago;
        $filter->vendedor       = $vendedor;
        $filter->moneda         = $moneda;
        $filter->consolidado    = $consolidado;

        $resumen = $this->rptventas_model->resumen_ventas_mensual($filter);
        
        $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA EMISION.");
        $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "FECHA VENCIMIENTO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "TIPO DOC. (01: FACTURA. 03: BOLETA. 07: NOTA CREDITO. 12: TICKET, ETC)");
        $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "SERIE");
        $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "NUMERO");
        $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "TIPO ENTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "NUMERO DE DOC. DE ENTIDAD");
        $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "RAZON SOCIAL / APELLIDOS Y NOMBRES");
        $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "MONEDA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "T/C");
        $this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", "GRAVADA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("L$lugar", "EXONERADA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("M$lugar", "INAFECTA");
        $this->excel->setActiveSheetIndex(0)->setCellValue("N$lugar", "IGV");
        $this->excel->setActiveSheetIndex(0)->setCellValue("O$lugar", "TOTAL");
        $this->excel->setActiveSheetIndex(0)->setCellValue("P$lugar", "ESTADO");
        $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasTitulo);

        if ($resumen != NULL){
            $lugar++;
            foreach($resumen as $indice => $valor){
                $fEmision = explode("-", $valor->CPC_Fecha);
                switch ($valor->CPC_TipoDocumento) {
                    case 'F':
                        $tipoDoc = "01";
                        break;
                    case 'B':
                        $tipoDoc = "03";
                        break;
                    case 'N':
                        $tipoDoc = "00";
                        break;
                    case 'C':
                        $tipoDoc = "07";
                        break;
                    
                    default:
                        $tipoDoc = "00";
                        break;
                }

                if ( $valor->numero_documento_cliente != NULL ){
                    switch ( strlen($valor->numero_documento_cliente) ) {
                        case 11:
                            $tipoDocEntidad = "6";
                            break;
                        case 8:
                            $tipoDocEntidad = "1";
                            break;
                        default:
                            $tipoDocEntidad = "";
                            break;
                    }
                }
                else{
                    switch ( strlen($valor->numero_documento_proveedor) ) {
                        case 11:
                            $tipoDocEntidad = "6";
                            break;
                        case 8:
                            $tipoDocEntidad = "1";
                            break;
                        default:
                            $tipoDocEntidad = "";
                            break;
                    }
                }

                if ( $valor->numero_documento_cliente == "00000009" ){
                    $tipoDocEntidad = "0";
                }

                if($valor->CPC_FlagEstado=="0"){

                    $estado             = "ANULADO";
                    $valor->gravada     = 0;
                    $valor->exonerada   = 0;
                    $valor->inafecta    = 0;
                    $valor->CPC_igv     = 0;
                    $valor->CPC_total   = 0;
                }else{
                    $estado             = "APROBADO";
                }
                $resultado = str_replace("indefinida", "", $valor->razon_social_cliente);
                $resultado2 = str_replace("indefinida", "", $valor->razon_social_proveedor);
                $valor->razon_social_cliente = $resultado;
                $valor->razon_social_proveedor = $resultado2;
                $this->excel->setActiveSheetIndex(0)
                ->setCellValue("A$lugar", $fEmision[2]."/".$fEmision[1]."/".$fEmision[0])
                ->setCellValue("B$lugar", "")
                ->setCellValue("C$lugar", $tipoDoc)
                ->setCellValue("D$lugar", $valor->CPC_Serie)
                ->setCellValue("E$lugar", $valor->CPC_Numero)
                ->setCellValue("F$lugar", $tipoDocEntidad)
                ->setCellValue("G$lugar", $valor->numero_documento_cliente.$valor->numero_documento_proveedor)
                ->setCellValue("H$lugar", $valor->razon_social_cliente.$valor->razon_social_proveedor)
                ->setCellValue("I$lugar", $valor->MONED_Descripcion)
                ->setCellValue("J$lugar", $valor->CPC_TDC)
                ->setCellValue("K$lugar", number_format( $valor->gravada, 2) )
                ->setCellValue("L$lugar", number_format( $valor->exonerada, 2) )
                ->setCellValue("M$lugar", number_format( $valor->inafecta, 2) )
                ->setCellValue("N$lugar", number_format( $valor->CPC_igv, 2) )
                ->setCellValue("O$lugar", number_format( $valor->CPC_total, 2) )
                ->setCellValue("P$lugar", $estado);
                if ($indice % 2 == 0)
                    $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasPar);
                else
                    $this->excel->getActiveSheet()->getStyle("A$lugar:P$lugar")->applyFromArray($estiloColumnasImpar);
                
                $lugar++;
            }
            $lugar++;
        }

        $this->excel->getActiveSheet()->getColumnDimension("A")->setWidth("12");
        $this->excel->getActiveSheet()->getColumnDimension("B")->setWidth("12");
        $this->excel->getActiveSheet()->getColumnDimension("C")->setWidth("12");
        $this->excel->getActiveSheet()->getColumnDimension("D")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("E")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("F")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("G")->setWidth("12");
        $this->excel->getActiveSheet()->getColumnDimension("H")->setWidth("40");
        $this->excel->getActiveSheet()->getColumnDimension("I")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("J")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("K")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("L")->setWidth("12");
        $this->excel->getActiveSheet()->getColumnDimension("M")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("N")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("O")->setWidth("10");
        $this->excel->getActiveSheet()->getColumnDimension("P")->setWidth("10");

      
        
        $filename = "Reporte de $operacion de ".$valor->CPC_Fecha.".xls"; //save our workbook as this file name
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
    }

    /*public function registro_ventas_excel($tipo_oper, $tipo_doc = "F", $fecha1, $fecha2) {
        if ($tipo_oper == 'V') {
            $titulo_personal = 'Cliente';
            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  VENTAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  VENTAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  VENTAS COMPROBANTES";
        }

        else {
            $titulo_personal = 'Proveedor';
            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  COMPRAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  COMPRAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  COMPRAS COMPROBANTES";
        }
        $this->load->library("PHPExcel");

        $phpExcel = new PHPExcel();
        $prestasi = $phpExcel->setActiveSheetIndex(0);
        //merger
        $phpExcel->getActiveSheet()->mergeCells('A1:J1');
        //manage row hight
        $phpExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        //style alignment
        $styleArray = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $phpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        //border
        $styleArray1 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        //background
        $styleArray12 = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'FFEC8B',
                ),
            ),
        );
        //freeepane
        $phpExcel->getActiveSheet()->freezePane('A5');
        //coloum width
        $phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $prestasi->setCellValue('A1', $titulo);
        if ($tipo_oper == 'V') {
            $phpExcel->getActiveSheet()->getStyle('A2:V4')->applyFromArray($styleArray12);


            $phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('A2:A4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('A2:A4');
            $prestasi->setCellValue('A2', 'Número Correlativo del Registro o Código unico de la operación');

            $phpExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('B2:B4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('B2:B4');
            $prestasi->setCellValue('B2', 'Fecha de emisión del comprobante de pago o documento.');

            $phpExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('C2:C4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('C2:C4');
            $prestasi->setCellValue('C2', 'Fecha de vencimiento y/o pago.');


            $phpExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('D2:F2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('D2:F2');
            $prestasi->setCellValue('D2', 'Comprobante de Pago o Documento');

            $phpExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('D3:D4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('D3:D4');
            $prestasi->setCellValue('D3', 'Tipo (Tabla 10)');

            $phpExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('E3:E4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('E3:E4');
            $prestasi->setCellValue('E3', 'N° de serie o N° de serie de la maquina registradora');

            $phpExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('F3:F4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('F3:F4');
            $prestasi->setCellValue('F3', 'Número');


            $phpExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('G2:I2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('G2:I2');
            $prestasi->setCellValue('G2', 'Información del Cliente');

            $phpExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('G3:H3')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('G3:H3');
            $prestasi->setCellValue('G3', 'Documento de Identidad');

            $phpExcel->getActiveSheet()->getStyle('G4')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('G4')->applyFromArray($styleArray1);
            $prestasi->setCellValue('G4', 'Tipo (Tabla 2)');

            $phpExcel->getActiveSheet()->getStyle('H4')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('H4')->applyFromArray($styleArray1);
            $prestasi->setCellValue('H4', 'Número');

            $phpExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('I3:I4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('I3:I4');
            $prestasi->setCellValue('I3', 'Apellidos y Nombres, Denominación o Razón Social');


            $phpExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('J2:J4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('J2:J4');
            $prestasi->setCellValue('J2', 'Valor Facturado de la Exportación');

            $phpExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('K2:K4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('K2:K4');
            $prestasi->setCellValue('K2', 'Base imponible de la operación grabada');

            $phpExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('L2:N2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('L2:N2');
            $prestasi->setCellValue('L2', 'Importe Total de la Operación Exonerada o Inafecta');

            $phpExcel->getActiveSheet()->getStyle('L3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('L3:L4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('L3:L4');
            $prestasi->setCellValue('L3', 'Exonerada');

            $phpExcel->getActiveSheet()->getStyle('M3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('M3:M4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('M3:M4');
            $prestasi->setCellValue('M3', 'Inafecta');

            $phpExcel->getActiveSheet()->getStyle('N3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('N3:N4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('N3:N4');
            $prestasi->setCellValue('N3', 'ISC');

            $phpExcel->getActiveSheet()->getStyle('O2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('O2:O4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('O2:O4');
            $prestasi->setCellValue('O2', 'IGV Y/O IPM');

            $phpExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('P2:P4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('P2:P4');
            $prestasi->setCellValue('P2', 'OTROS TRIBUTOS Y CARGOS QUE NO FORMAN PARTE DE LA BASE IMPONIBLE');

            $phpExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('Q2:Q4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('Q2:Q4');
            $prestasi->setCellValue('Q2', 'IMPORTE TOTAL DEL COMPROBANTE DE PAGO');

            $phpExcel->getActiveSheet()->getStyle('R2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('R2:R4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('R2:R4');
            $prestasi->setCellValue('R2', 'TIPO DE CAMBIO');

            $phpExcel->getActiveSheet()->getStyle('S2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('S2:V2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('S2:V2');
            $prestasi->setCellValue('S2', 'REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA');

            $phpExcel->getActiveSheet()->getStyle('S3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('S3:S4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('S3:S4');
            $prestasi->setCellValue('S3', 'FECHA');

            $phpExcel->getActiveSheet()->getStyle('T3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('T3:T4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('T3:T4');
            $prestasi->setCellValue('T3', 'TIPO TABLA(10)');

            $phpExcel->getActiveSheet()->getStyle('U3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('U3:U4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('U3:U4');
            $prestasi->setCellValue('U3', 'SERIE');

            $phpExcel->getActiveSheet()->getStyle('V3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('V3:V4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('V3:V4');
            $prestasi->setCellValue('V3', 'N° DEL COMPROBATE DE PAGO O DOCUMENTO');
        } else {
            $phpExcel->getActiveSheet()->getStyle('A2:AB4')->applyFromArray($styleArray12);


            $phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('A2:A4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('A2:A4');
            $prestasi->setCellValue('A2', 'Número Correlativo del Registro o Código unico de la operación');

            $phpExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('B2:B4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('B2:B4');
            $prestasi->setCellValue('B2', 'Fecha de emisión del comprobante de pago o documento.');

            $phpExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('C2:C4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('C2:C4');
            $prestasi->setCellValue('C2', 'Fecha de vencimiento y/o pago.');


            $phpExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('D2:F2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('D2:F2');
            $prestasi->setCellValue('D2', 'Comprobante de Pago o Documento');

            $phpExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('D3:D4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('D3:D4');
            $prestasi->setCellValue('D3', 'Tipo (Tabla 10)');

            $phpExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('E3:E4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('E3:E4');
            $prestasi->setCellValue('E3', 'SERIE O CODIGO DE LA DEPENDENCIA ADUANERA (TABLA11)');

            $phpExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('F3:F4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('F3:F4');
            $prestasi->setCellValue('F3', 'AÑO DE EMISION DE LA DUA O DSI');

            $phpExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('G2:G4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('G2:G4');
            $prestasi->setCellValue('G2', ' N° DEL COMPROBANTE DE PAGO,
                                            DOCUMENTO, N° DE ORDEN DEL
                                           FORMULARIO F?SICO O VIRTUAL, 
                                          N° DE DUA, DSI O LIQUIDACIÓN DE 
                                         COBRANZA U OTROS DOCUMENTOS 
                                      EMITIDOS POR SUNAT PARA ACREDITAR 
                                      EL CRÉDITO FISCAL EN LA IMPORTACIÓN
                                     ');

            $phpExcel->getActiveSheet()->getStyle('H2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('H2:J2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('H2:J2');
            $prestasi->setCellValue('H2', 'INFORMACIÓN DEL PROVEEDOR');

            $phpExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('H3:I3')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('H3:I3');
            $prestasi->setCellValue('H3', 'Documento de Identidad');

            $phpExcel->getActiveSheet()->getStyle('H4')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('H4')->applyFromArray($styleArray1);
            $prestasi->setCellValue('H4', 'TIPO (TABLA 2)');

            $phpExcel->getActiveSheet()->getStyle('I4')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('I4')->applyFromArray($styleArray1);
            $prestasi->setCellValue('I4', 'NÚMERO');

            $phpExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('J3:J4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('J3:J4');
            $prestasi->setCellValue('J3', 'APELLIDOS Y NOMBRES, DENOMINACION SOCIAL O RAZON SOCIAL');

            $phpExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('K2:L2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('K2:L2');
            $prestasi->setCellValue('K2', ' ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES 
             GRAVADAS Y/O DE EXPORTACIÓN');

            $phpExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('K3:K4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('K3:K4');
            $prestasi->setCellValue('K3', 'BASE IMPONIBLE');

            $phpExcel->getActiveSheet()->getStyle('L3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('L3:L4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('L3:L4');
            $prestasi->setCellValue('L3', 'IGV');


            $phpExcel->getActiveSheet()->getStyle('M2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('M2:N2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('M2:N2');
            $prestasi->setCellValue('M2', ' ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES 
            GRAVADAS Y/O DE EXPORTACIÓN Y A OPERACIONES NO GRAVADAS');

            $phpExcel->getActiveSheet()->getStyle('M3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('M3:M4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('M3:M4');
            $prestasi->setCellValue('M3', 'BASE IMPONIBLE');

            $phpExcel->getActiveSheet()->getStyle('N3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('N3:N4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('N3:N4');
            $prestasi->setCellValue('N3', 'IGV');


            $phpExcel->getActiveSheet()->getStyle('O2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('O2:P2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('O2:P2');
            $prestasi->setCellValue('O2', ' ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS');

            $phpExcel->getActiveSheet()->getStyle('O3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('O3:O4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('O3:O4');
            $prestasi->setCellValue('O3', 'BASE IMPONIBLE');

            $phpExcel->getActiveSheet()->getStyle('P3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('P3:P4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('P3:P4');
            $prestasi->setCellValue('P3', 'IGV');

            $phpExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('Q2:Q4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('Q2:Q4');
            $prestasi->setCellValue('Q2', 'VALOR DE LAS ADQUISICIONES NO GRAVADAS');

            $phpExcel->getActiveSheet()->getStyle('R2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('R2:R4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('R2:R4');
            $prestasi->setCellValue('R2', 'ISC');

            $phpExcel->getActiveSheet()->getStyle('S2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('S2:S4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('S2:S4');
            $prestasi->setCellValue('S2', 'OTROS TRIBUTOS Y CARGOS');

            $phpExcel->getActiveSheet()->getStyle('T2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('T2:T4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('T2:T4');
            $prestasi->setCellValue('T2', 'IMPORTE TOTAL');

            $phpExcel->getActiveSheet()->getStyle('U2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('U2:U4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('U2:U4');
            $prestasi->setCellValue('U2', 'N° DE COMPROBANTE DE PAGO EMITIDO POR SUJETO NO DOMICILIADO (2)');

            $phpExcel->getActiveSheet()->getStyle('V2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('V2:W2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('V2:W2');
            $prestasi->setCellValue('V2', 'CONSTANCIA DE DEPÓSITO DE DETRACCIÓN (3)');

            $phpExcel->getActiveSheet()->getStyle('V3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('V3:V4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('V3:V4');
            $prestasi->setCellValue('V3', 'NUMERO');

            $phpExcel->getActiveSheet()->getStyle('W3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('W3:W4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('W3:W4');
            $prestasi->setCellValue('W3', 'FECHA DE EMISION');

            $phpExcel->getActiveSheet()->getStyle('X2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('X2:X4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('X2:X4');
            $prestasi->setCellValue('X2', 'TIPO DE CAMBIO');

            $phpExcel->getActiveSheet()->getStyle('Y2')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('Y2:AB2')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('Y2:AB2');
            $prestasi->setCellValue('Y2', 'REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA');

            $phpExcel->getActiveSheet()->getStyle('Y3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('Y3:Y4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('Y3:Y4');
            $prestasi->setCellValue('Y3', 'FECHA');

            $phpExcel->getActiveSheet()->getStyle('Z3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('Z3:Z4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('Z3:Z4');
            $prestasi->setCellValue('Z3', 'TIPO (TABLA 10)');

            $phpExcel->getActiveSheet()->getStyle('AA3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('AA3:AA4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('AA3:AA4');
            $prestasi->setCellValue('AA3', 'SERIE');

            $phpExcel->getActiveSheet()->getStyle('AB3')->getAlignment()->setWrapText(true);
            $phpExcel->getActiveSheet()->getStyle('AB3:AB4')->applyFromArray($styleArray1);
            $phpExcel->getActiveSheet()->mergeCells('AB3:AB4');
            $prestasi->setCellValue('AB3', 'N° DEL COMPROBANTE DE PAGO O DOCUMENTO');
        }
        $lista = $this->rptventas_model->registro_ventas($tipo_oper, $tipo_doc, $fecha1, $fecha2);

        $no = 0;
        $rowexcel = 4;
        $valor_venta = 0;
        $valor_igv = 0;
        $valor_total = 0;
        foreach ($lista as $indice => $valor) {
            $tipo = $valor->CPC_TipoDocumento;
            $tipo_persona = $valor->CLIC_TipoPersona;
            $flag = $valor->CPC_FlagEstado;
            $nombre = '';
            if ($flag == 1) {

                if ($tipo_doc != "F") {
                    $subtotal = number_format($valor->CPC_total / 1.18, 2);
                    $igv = number_format($subtotal * 0.18, 2);
                } else {
                    $igv = $valor->CPC_igv;
                    $subtotal = $valor->CPC_subtotal;
                }
                $total = $valor->CPC_total;
                $valor_venta +=$subtotal;
                $valor_igv +=$igv;
                $valor_total +=$total;


                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
                if ($tipo_persona == '0') {
                    $doc = 'DNI';
                    $nombre_cliente = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                    $ruc = $valor->PERSC_Ruc;
                } else {
                    $doc = 'RUC';
                    $nombre_cliente = $valor->EMPRC_RazonSocial;
                    $ruc = $valor->EMPRC_Ruc;
                }
            } else {
                $nombre_cliente = "ANULADO";
                $ruc = "";
                $subtotal = "";
                $igv = "";
                $total = "";
                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
            }

            $no++;
            $rowexcel++;

            if ($tipo_oper == 'V') {
                $prestasi->setCellValue('A' . $rowexcel, $no);
                $prestasi->setCellValue('B' . $rowexcel, $valor->CPC_Fecha);
                $prestasi->setCellValue('D' . $rowexcel, $nombre);
                $prestasi->setCellValue('E' . $rowexcel, (int) $valor->CPC_Serie);
                $prestasi->setCellValue('F' . $rowexcel, (int) $valor->CPC_Numero);
                $prestasi->setCellValue('G' . $rowexcel, $doc);
                $prestasi->setCellValue('H' . $rowexcel, $ruc);
                $prestasi->setCellValue('I' . $rowexcel, $nombre_cliente);
                $prestasi->setCellValue('O' . $rowexcel, $igv);
                $prestasi->setCellValue('Q' . $rowexcel, $total);
            } else {
                $prestasi->setCellValue('A' . $rowexcel, $no);
                $prestasi->setCellValue('B' . $rowexcel, $valor->CPC_Fecha);
                $prestasi->setCellValue('D' . $rowexcel, $nombre);
                $prestasi->setCellValue('E' . $rowexcel, (int) $valor->CPC_Serie);
                $prestasi->setCellValue('G' . $rowexcel, (int) $valor->CPC_Numero);
                $prestasi->setCellValue('H' . $rowexcel, $doc);
                $prestasi->setCellValue('I' . $rowexcel, $ruc);
                $prestasi->setCellValue('J' . $rowexcel, $nombre_cliente);
                $prestasi->setCellValue('P' . $rowexcel, $igv);
                $prestasi->setCellValue('T' . $rowexcel, $total);
            }
        }

        $prestasi->setTitle('ReportE');
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"Report.xls\"");
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
        $objWriter->save("php://output");
    }*/

    public function registro_ventas_excel2($tipo_oper, $tipo_doc = "F", $fecha1, $fecha2) {
        if ($tipo_oper == 'V') {
            $titulo_personal = 'Cliente';
            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  VENTAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  VENTAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  VENTAS COMPROBANTES";
        }

        else {
            $titulo_personal = 'Proveedor';
            if ($tipo_doc == "F")
                $titulo = "REGISTRO DE  COMPRAS FACTURAS";
            if ($tipo_doc == "B")
                $titulo = "REPORTE DE  COMPRAS BOLETAS";
            if ($tipo_doc == "N")
                $titulo = "REPORTE DE  COMPRAS COMPROBANTES";
        }
        $this->load->library("PHPExcel");

        $phpExcel = new PHPExcel();
        $prestasi = $phpExcel->setActiveSheetIndex(0);
        //merger
        $phpExcel->getActiveSheet()->mergeCells('A1:J1');
        //manage row hight
        $phpExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        //style alignment
        $styleArray = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $phpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        //border
        $styleArray1 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        //background
        $styleArray12 = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'FFEC8B',
                ),
            ),
        );
        //freeepane
        $phpExcel->getActiveSheet()->freezePane('A3');
        //coloum width
        $phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6.1);
        $phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $prestasi->setCellValue('A1', $titulo);
        $phpExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray);
        $phpExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray1);
        $phpExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray12);
        $prestasi->setCellValue('A2', 'No');
        $prestasi->setCellValue('B2', 'Fecha');
        $prestasi->setCellValue('C2', 'Tipo');
        $prestasi->setCellValue('D2', 'Serie');
        $prestasi->setCellValue('E2', 'Numero');
        $prestasi->setCellValue('F2', $titulo_personal);
        $prestasi->setCellValue('G2', 'Ruc');
        $prestasi->setCellValue('H2', 'Valor Venta');
        $prestasi->setCellValue('I2', 'I.G.V');
        $prestasi->setCellValue('J2', 'Importe Total');

        $lista = $this->rptventas_model->registro_ventas($tipo_oper, $tipo_doc, $fecha1, $fecha2);

        $no = 0;
        $rowexcel = 2;
        $valor_venta = 0;
        $valor_igv = 0;
        $valor_total = 0;
        foreach ($lista as $indice => $valor) {
            $tipo = $valor->CPC_TipoDocumento;
            $tipo_persona = $valor->CLIC_TipoPersona;
            $flag = $valor->CPC_FlagEstado;
            $nombre = '';
            if ($flag == 1) {

                if ($tipo_doc != "F") {
                    $subtotal = number_format($valor->CPC_total / 1.18, 2);
                    $igv = number_format($subtotal * 0.18, 2);
                } else {
                    $igv = $valor->CPC_igv;
                    $subtotal = $valor->CPC_subtotal;
                }
                $total = $valor->CPC_total;
                $valor_venta +=$subtotal;
                $valor_igv +=$igv;
                $valor_total +=$total;


                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
                if ($tipo_persona == '0') {
                    $nombre_cliente = $valor->PERSC_Nombre . " " . $valor->PERSC_ApellidoPaterno . " " . $valor->PERSC_ApellidoMaterno;
                    $ruc = $valor->PERSC_Ruc;
                } else {
                    $nombre_cliente = $valor->EMPRC_RazonSocial;
                    $ruc = $valor->EMPRC_Ruc;
                }
            } else {
                $nombre_cliente = "ANULADO";
                $ruc = "";
                $subtotal = "";
                $igv = "";
                $total = "";
                if ($tipo_doc == 'F')
                    $nombre = 'Factura';
                else
                    $nombre = 'Boleta';
            }

            $no++;
            $rowexcel++;

            $prestasi->setCellValue('A' . $rowexcel, $no);
            $prestasi->setCellValue('B' . $rowexcel, $valor->CPC_Fecha);
            $prestasi->setCellValue('C' . $rowexcel, $nombre);
            $prestasi->setCellValue('D' . $rowexcel, $valor->CPC_Serie);
            $prestasi->setCellValue('E' . $rowexcel, $valor->CPC_Numero);
            $prestasi->setCellValue('F' . $rowexcel, $nombre_cliente);
            $prestasi->setCellValue('G' . $rowexcel, $ruc);
            $prestasi->setCellValue('H' . $rowexcel, $subtotal);
            $prestasi->setCellValue('I' . $rowexcel, $igv);
            $prestasi->setCellValue('J' . $rowexcel, $total);
        }

        $prestasi->setTitle('ReportE');
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"Report.xls\"");
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
        $objWriter->save("php://output");
    }

    public function ganancia() {
        
        $lista = '';
        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';
        $producto = $this->input->post('producto');
        $f_ini = $this->input->post('fecha_inicio') != '' ? $this->input->post('fecha_inicio') : '01/' . date('m/Y');
        $f_fin = $this->input->post('fecha_fin') != '' ? $this->input->post('fecha_fin') : date('d/m/Y');
        $moneda = $this->input->post('moneda') != '' ? $this->input->post('moneda') : '1';

        $comp_select = array();
        $lista_companias = $this->compania_model->listar_establecimiento($this->somevar['empresa']);
        foreach ($lista_companias as $key => $compania) {
            if (count($_POST) > 0) {
                if ($this->input->post('COMPANIA_' . $compania->COMPP_Codigo) > 0) {
                    $comp_select[] = $compania->COMPP_Codigo;
                    $lista_companias[$key]->checked = true;
                }
                else
                    $lista_companias[$key]->checked = false;
            }else {
                $comp_select[] = $compania->COMPP_Codigo;
                $lista_companias[$key]->checked = true;
            }
        }

        $total_costo = 0;
        $total_venta = 0;
        $total_util = 0;
        $total_porc_util = 0;
        $lista_ganancia = $this->comprobantedetalle_model->reporte_ganancia($producto, human_to_mysql($f_ini), human_to_mysql($f_fin), $comp_select);
        $lista = array();
        $resumen_compania = array();
        foreach ($lista_ganancia as $value) {
            $fecha = mysql_to_human($value->CPC_Fecha);
            $establec = $value->EESTABC_Descripcion;
            $nombre_producto = $value->PROD_Nombre;
            $cantidad = $value->CPDEC_Cantidad;
            $simbolo_moneda = $value->MONED_Simbolo;
            $pcosto = $value->ALMALOTC_Costo;
            $pventa = $value->CPDEC_Pu_ConIgv;
            $costo = $pcosto * $value->CPDEC_Cantidad;
            $venta = $pventa * $value->CPDEC_Cantidad;
            $total_costo+=$costo;
            $total_venta+=$venta;
            $utilidad = $venta - $costo;
            $porc_util = $costo != 0 ? ($utilidad / $costo) * 100 : 0;
            $resumen_compania[$value->COMPP_Codigo] = array('costo' => isset($resumen_compania[$value->COMPP_Codigo]['costo']) ? $resumen_compania[$value->COMPP_Codigo]['costo'] + $costo : $costo,
                'venta' => isset($resumen_compania[$value->COMPP_Codigo]['venta']) ? $resumen_compania[$value->COMPP_Codigo]['venta'] + $venta : $venta
            );

            $lote_numero = $value->LOTC_Numero;
            $lote_fv = mysql_to_human($value->LOTC_FechaVencimiento);
            $lista[] = array($fecha, $establec, $nombre_producto, $lote_numero, $lote_fv, $cantidad, $simbolo_moneda, number_format($pcosto, 2), number_format($pventa, 2), number_format($costo, 2), number_format($venta, 2), number_format($utilidad, 2), round($porc_util));
        }

        $total_util = $total_venta - $total_costo;
        $total_porc_util = $total_costo != 0 ? ($total_util / $total_costo) * 100 : 0;

        /* Resumen por compania */
        $t_resumen_costo = 0;
        $t_resumen_venta = 0;
        foreach ($lista_companias as $key => $compania) {
            if (isset($resumen_compania[$compania->COMPP_Codigo])) {
                $st_costo = $resumen_compania[$compania->COMPP_Codigo]['costo'];
                $st_venta = $resumen_compania[$compania->COMPP_Codigo]['venta'];
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $resumen_compania[$compania->COMPP_Codigo]['costo'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['costo'], 2) : 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $resumen_compania[$compania->COMPP_Codigo]['venta'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['venta'], 2) : 0;
            } else {
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $st_costo = 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $st_venta = 0;
            }
            $resumen_compania[$compania->COMPP_Codigo]['util'] = $st_venta - $st_costo;
            $resumen_compania[$compania->COMPP_Codigo]['porc'] = round($st_costo != 0 ? (($st_venta - $st_costo) / $st_costo) * 100 : 0, 2);
            $t_resumen_costo+=$st_costo;
            $t_resumen_venta+=$st_venta;
        }
        $t_resumen_util = $t_resumen_venta - $t_resumen_costo;
        $t_resumen_porc = $t_resumen_costo != 0 ? ($t_resumen_util / $t_resumen_costo) * 100 : 0;

        $data['producto'] = $producto;
        $data['codproducto'] = $this->input->post('codproducto');
        $data['nombre_producto'] = $this->input->post('nombre_producto');
        $data['f_ini'] = $f_ini;
        $data['f_fin'] = $f_fin;
        $data['TODOS'] = $this->input->post('TODOS') == '1' ? true : false;
        $data['lista_companias'] = $lista_companias;
        $data['cboMoneda'] = form_dropdown("moneda", $this->moneda_model->seleccionar(), $moneda, " class='comboMedio cajaSoloLectura' disabled id='moneda' style='width:150px'");
        $data['lista'] = $lista;
        $data['total_costo'] = number_format($total_costo, 2);
        $data['total_venta'] = number_format($total_venta, 2);
        $data['total_util'] = number_format($total_util, 2);
        $data['total_porc_util'] = round($total_porc_util, 2);
        $data['resumen_compania'] = $resumen_compania;
        $data['t_resumen_costo'] = number_format($t_resumen_costo, 2);
        $data['t_resumen_venta'] = number_format($t_resumen_venta, 2);
        $data['t_resumen_util'] = number_format($t_resumen_util, 2);
        $data['t_resumen_porc'] = round($t_resumen_porc, 2);
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/ganancia', $data);
    }

    public function gananciaPDF($codigo = 'ALL', $companias = '', $fecha = NULL) {

        $comp_select = explode("-", $companias);

        $lista = '';
        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';

        $producto = ($codigo == "ALL") ? "" : $codigo;

        $fechaIF = explode("-", $fecha);

        $f_ini = ($fecha == NULL) ? "01/".date("m/").date("Y") : "$fechaIF[0]/$fechaIF[1]/$fechaIF[2]";
        $f_fin = ($fecha == NULL) ? date('d/m/Y') : "$fechaIF[3]/$fechaIF[4]/$fechaIF[5]";

        $lista_companias = $this->compania_model->listar_establecimiento($this->somevar['empresa']);

        $total_costo = 0;
        $total_venta = 0;
        $total_util = 0;
        $total_porc_util = 0;
        $lista_ganancia = $this->comprobantedetalle_model->reporte_ganancia($producto, human_to_mysql($f_ini), human_to_mysql($f_fin), $comp_select);
        $lista = array();
        $resumen_compania = array();
        foreach ($lista_ganancia as $value) {
            $fecha = mysql_to_human($value->CPC_Fecha);
            $establec = $value->EESTABC_Descripcion;
            $nombre_producto = $value->PROD_Nombre;
            $cantidad = $value->CPDEC_Cantidad;
            $simbolo_moneda = $value->MONED_Simbolo;
            $pcosto = $value->ALMALOTC_Costo;
            $pventa = $value->CPDEC_Pu_ConIgv;
            $costo = $pcosto * $value->CPDEC_Cantidad;
            $venta = $pventa * $value->CPDEC_Cantidad;
            $total_costo+=$costo;
            $total_venta+=$venta;
            $utilidad = $venta - $costo;
            $porc_util = $costo != 0 ? ($utilidad / $costo) * 100 : 0;
            $resumen_compania[$value->COMPP_Codigo] = array('costo' => isset($resumen_compania[$value->COMPP_Codigo]['costo']) ? $resumen_compania[$value->COMPP_Codigo]['costo'] + $costo : $costo,
                'venta' => isset($resumen_compania[$value->COMPP_Codigo]['venta']) ? $resumen_compania[$value->COMPP_Codigo]['venta'] + $venta : $venta
            );

            $lote_numero = $value->LOTC_Numero;
            $lote_fv = mysql_to_human($value->LOTC_FechaVencimiento);
            $lista[] = array($fecha, $establec, $nombre_producto, $lote_numero, $lote_fv, $cantidad, $simbolo_moneda, number_format($pcosto, 2), number_format($pventa, 2), number_format($costo, 2), number_format($venta, 2), number_format($utilidad, 2), round($porc_util));
        }

        $total_util = $total_venta - $total_costo;
        $total_porc_util = $total_costo != 0 ? ($total_util / $total_costo) * 100 : 0;

        /* Resumen por compania */
        $t_resumen_costo = 0;
        $t_resumen_venta = 0;
        foreach ($lista_companias as $key => $compania) {
            if (isset($resumen_compania[$compania->COMPP_Codigo])) {
                $st_costo = $resumen_compania[$compania->COMPP_Codigo]['costo'];
                $st_venta = $resumen_compania[$compania->COMPP_Codigo]['venta'];
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $resumen_compania[$compania->COMPP_Codigo]['costo'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['costo'], 2) : 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $resumen_compania[$compania->COMPP_Codigo]['venta'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['venta'], 2) : 0;
            } else {
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $st_costo = 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $st_venta = 0;
            }
            $resumen_compania[$compania->COMPP_Codigo]['util'] = $st_venta - $st_costo;
            $resumen_compania[$compania->COMPP_Codigo]['porc'] = round($st_costo != 0 ? (($st_venta - $st_costo) / $st_costo) * 100 : 0, 2);
            $t_resumen_costo+=$st_costo;
            $t_resumen_venta+=$st_venta;
        }
        $t_resumen_util = $t_resumen_venta - $t_resumen_costo;
        $t_resumen_porc = $t_resumen_costo != 0 ? ($t_resumen_util / $t_resumen_costo) * 100 : 0;

        $img = 'images/img_db/menbrete1.jpg';
        $this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img'=> $img));
        $this->cezpdf->ezSetCmMargins(5.5,4,1.5,1.5);
        $this->cezpdf->ezStartPageNumbers(60, 40, 10, 'left', '', 1);

        $this->cezpdf->ezText("", 8, array("leading" => 2));
        $this->cezpdf->ezText("REPORTE DE GANANCIA", 12, array("leading" => 10, "left" => 0, "justification" => "center"));
        $this->cezpdf->ezText("", 8, array("leading" => 10));
        
        if ( count($lista) > 0 ) {
            $view = array();

            foreach($lista as $indice=>$value){
                $view[] = array(
                            'col1' => $value[0],
                            'col2' => $value[1],
                            'col3' => $value[2],
                            'col4' => $value[3],
                            'col5' => $value[4],
                            'col6' => $value[5],
                            'col7' => $value[6],
                            'col8' => $value[7],
                            'col9' => $value[8],
                            'col10' => $value[9],
                            'col11' => $value[10],
                            'col12' => $value[11],
                            'col13' => $value[12]
                        );
            }

            $col_names = array(
                    'col1' => 'FECHA',
                    'col2' => 'ESTABLECIMIENTO',
                    'col3' => 'PRODUCTO',
                    'col4' => 'NUMETO LOTE',
                    'col5' => 'FECHA V.',
                    'col6' => 'CANT.',
                    'col7' => 'M.',
                    'col8' => 'P/COSTO',
                    'col9' => 'P/VENTA',
                    'col10' => 'COSTO',
                    'col11' => 'VENTA',
                    'col12' => 'UTILIDAD',
                    'col13' => '% UTIL'
            );

                $alignL = "left";
                $alignC = "center";
                $alignR = "right";

                $this->cezpdf->ezTable($view, $col_names, '', array(
                    'width' => 555,
                    'showLines' => 2,
                    'shaded' => 0,
                    'showHeadings' => 1,
                    'xPos' => '300',
                    'fontSize' => 6,
                    'cols' => array(
                        'col1' => array('width' => 45, 'justification' => $alignC), // FECHA
                        'col2' => array('width' => 60, 'justification' => $alignC), // ESTABLECIMIENTO
                        'col3' => array('width' => 70, 'justification' => $alignL),// PRODUCTO
                        'col4' => array('width' => 40, 'justification' => $alignC), // N. LOTE
                        'col5' => array('width' => 45, 'justification' => $alignC), // FECHA V.
                        'col6' => array('width' => 30, 'justification' => $alignR), // CANT.
                        'col7' => array('width' => 20, 'justification' => $alignR), // MONEDA
                        'col8' => array('width' => 40, 'justification' => $alignR), // P/COSTO
                        'col9' => array('width' => 40, 'justification' => $alignR), // P/VENTA
                        'col10' => array('width' => 40, 'justification' => $alignR),// COSTO
                        'col11' => array('width' => 40, 'justification' => $alignR),// VENTA
                        'col12' => array('width' => 40, 'justification' => $alignR),// UTILIDAD
                        'col13' => array('width' => 35, 'justification' => $alignR) // % UTILIDAD
                    )
                ));


        }

        $yPos = $this->cezpdf->y - $this->cezpdf->ez['bottomMargin'];
                
        if ($yPos < 70)
            $this->cezpdf->ezNewPage();

            if(count($lista_companias) > 0){
                $this->cezpdf->ezText("", 8, array("leading" => 15));
                $this->cezpdf->ezText("RESUMEN POR ESTABLECIMIENTO", 10, array("leading" => 10, "left" => 35));
                $this->cezpdf->ezText("", 8, array("leading" => 10));
            
                $col_names = array(
                        'col1' => 'ESTABLECIMIENTO',
                        'col2' => 'COSTO',
                        'col3' => 'VENTA',
                        'col4' => 'UTILIDAD',
                        'col5' => '% UTILIDAD'
                );

                $viewG = array();

                foreach($lista_companias as $indice=>$value){
                    $viewG[] = array(
                            'col1' => $value->EESTABC_Descripcion,
                            'col2' => $resumen_compania[$value->COMPP_Codigo]['costo'],
                            'col3' => $resumen_compania[$value->COMPP_Codigo]['venta'],
                            'col4' => $resumen_compania[$value->COMPP_Codigo]['util'],
                            'col5' => $resumen_compania[$value->COMPP_Codigo]['porc']
                        );
                }

                $alignL = "left";
                $alignC = "center";
                $alignR = "right";

                $this->cezpdf->ezTable($viewG, $col_names, '', array(
                    'width' => 525,
                    'showLines' => 2,
                    'shaded' => 0,
                    'showHeadings' => 1,
                    'xPos' => '295',
                    'fontSize' => 7,
                    'cols' => array(
                        'col1' => array('width' => 140, 'justification' => $alignL),
                        'col2' => array('width' => 70, 'justification' => $alignR),
                        'col3' => array('width' => 70, 'justification' => $alignR),
                        'col4' => array('width' => 70, 'justification' => $alignR),
                        'col5' => array('width' => 70, 'justification' => $alignR)
                    )
                ));
            }

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
    }

    public function gananciaExcel($codigo = 'ALL', $companias = '', $fecha = NULL) {

        $comp_select = explode("-", $companias);

        $lista = '';
        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';

        $producto = ($codigo == "ALL") ? "" : $codigo;

        $fechaIF = explode("-", $fecha);

        $f_ini = ($fecha == NULL) ? "01/".date("m/").date("Y") : "$fechaIF[0]/$fechaIF[1]/$fechaIF[2]";
        $f_fin = ($fecha == NULL) ? date('d/m/Y') : "$fechaIF[3]/$fechaIF[4]/$fechaIF[5]";

        $lista_companias = $this->compania_model->listar_establecimiento($this->somevar['empresa']);

        $total_costo = 0;
        $total_venta = 0;
        $total_util = 0;
        $total_porc_util = 0;
        $lista_ganancia = $this->comprobantedetalle_model->reporte_ganancia($producto, human_to_mysql($f_ini), human_to_mysql($f_fin), $comp_select);
        $lista = array();
        $resumen_compania = array();
        foreach ($lista_ganancia as $value) {
            $fecha = mysql_to_human($value->CPC_Fecha);
            $establec = $value->EESTABC_Descripcion;
            $nombre_producto = $value->PROD_Nombre;
            $cantidad = $value->CPDEC_Cantidad;
            $simbolo_moneda = $value->MONED_Simbolo;
            $pcosto = $value->ALMALOTC_Costo;
            $pventa = $value->CPDEC_Pu_ConIgv;
            $costo = $pcosto * $value->CPDEC_Cantidad;
            $venta = $pventa * $value->CPDEC_Cantidad;
            $total_costo+=$costo;
            $total_venta+=$venta;
            $utilidad = $venta - $costo;
            $porc_util = $costo != 0 ? ($utilidad / $costo) * 100 : 0;
            $resumen_compania[$value->COMPP_Codigo] = array('costo' => isset($resumen_compania[$value->COMPP_Codigo]['costo']) ? $resumen_compania[$value->COMPP_Codigo]['costo'] + $costo : $costo,
                'venta' => isset($resumen_compania[$value->COMPP_Codigo]['venta']) ? $resumen_compania[$value->COMPP_Codigo]['venta'] + $venta : $venta
            );

            $lote_numero = $value->LOTC_Numero;
            $lote_fv = mysql_to_human($value->LOTC_FechaVencimiento);
            $lista[] = array($fecha, $establec, $nombre_producto, $lote_numero, $lote_fv, $cantidad, $simbolo_moneda, number_format($pcosto, 2), number_format($pventa, 2), number_format($costo, 2), number_format($venta, 2), number_format($utilidad, 2), round($porc_util));
        }

        $total_util = $total_venta - $total_costo;
        $total_porc_util = $total_costo != 0 ? ($total_util / $total_costo) * 100 : 0;

        /* Resumen por compania */
        $t_resumen_costo = 0;
        $t_resumen_venta = 0;
        foreach ($lista_companias as $key => $compania) {
            if (isset($resumen_compania[$compania->COMPP_Codigo])) {
                $st_costo = $resumen_compania[$compania->COMPP_Codigo]['costo'];
                $st_venta = $resumen_compania[$compania->COMPP_Codigo]['venta'];
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $resumen_compania[$compania->COMPP_Codigo]['costo'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['costo'], 2) : 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $resumen_compania[$compania->COMPP_Codigo]['venta'] > 0 ? number_format($resumen_compania[$compania->COMPP_Codigo]['venta'], 2) : 0;
            } else {
                $resumen_compania[$compania->COMPP_Codigo]['costo'] = $st_costo = 0;
                $resumen_compania[$compania->COMPP_Codigo]['venta'] = $st_venta = 0;
            }
            $resumen_compania[$compania->COMPP_Codigo]['util'] = $st_venta - $st_costo;
            $resumen_compania[$compania->COMPP_Codigo]['porc'] = round($st_costo != 0 ? (($st_venta - $st_costo) / $st_costo) * 100 : 0, 2);
            $t_resumen_costo+=$st_costo;
            $t_resumen_venta+=$st_venta;
        }
        $t_resumen_util = $t_resumen_venta - $t_resumen_costo;
        $t_resumen_porc = $t_resumen_costo != 0 ? ($t_resumen_util / $t_resumen_costo) * 100 : 0;

        
        $this->load->library('Excel');
        
        ###########################################
        ######### TITULO Y ESTILOS
        ###########################################
            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('Reporte de Ganancia');
            $TipoFont = array( 'font'  => array( 'bold'  => true, 'color' => array('rgb' => '000000'), 'size'  => 16, 'name'  => 'Calibri'));
            $TipoFont2 = array( 'font'  => array( 'bold'  => true, 'color' => array('rgb' => '000000'), 'size'  => 14, 'name'  => 'Calibri'));
            $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
            $style2 = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

            $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($TipoFont);
            $this->excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($style);
            $this->excel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($TipoFont);
            $this->excel->getActiveSheet()->getStyle("A3:N3")->applyFromArray($style);

            $estiloColumnasTitulo = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 11
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
                                                )
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
                                                )
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

            $this->excel->getActiveSheet()->getStyle("A4:M4")->applyFromArray($estiloColumnasTitulo);

        ###########################################

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:M2')->setCellValue('A1', $_SESSION['nombre_empresa']);
        
        $this->excel->getActiveSheet()->getStyle("A3:M3")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A3:M3")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:M3")->setCellValue("A3", "REPORTE DE GANANCIA DESDE El $f_ini HASTA $f_fin");
        
        ###########################################
        ######### TITULO DE COLUMNA RODUCTO
        ###########################################
            $lugar = 4;
            $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "FECHA");
            $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "ESTABLECIMIENTO");
            $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "DESCRIPCIÓN");
            $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "NÚMERO DE LOTE");
            $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "VENCIMIENTO LOTE");
            $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "CANTIDAD");
            $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "MONEDA");
            $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "P / COSTO");
            $this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", "P / VENTA");
            $this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", "COSTO TOTAL");
            $this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", "VENTA TOTAL");
            $this->excel->setActiveSheetIndex(0)->setCellValue("L$lugar", "UTILIDAD");
            $this->excel->setActiveSheetIndex(0)->setCellValue("M$lugar", "% UTILIDAD");
        ###########################################

        $numeroS = 0;
        $lugar += 1;
        
        foreach($lista as $indice => $valor){
            $numeroS += 1;

            $this->excel->setActiveSheetIndex(0)
            ->setCellValue("A$lugar", $valor[0])
            ->setCellValue("B$lugar", $valor[1])
            ->setCellValue("C$lugar", $valor[2])
            ->setCellValue("D$lugar", $valor[3])
            ->setCellValue("E$lugar", $valor[4])
            ->setCellValue("F$lugar", $valor[5])
            ->setCellValue("G$lugar", $valor[6])
            ->setCellValue("H$lugar", $valor[7])
            ->setCellValue("I$lugar", $valor[8])
            ->setCellValue("J$lugar", $valor[9])
            ->setCellValue("K$lugar", $valor[10])
            ->setCellValue("L$lugar", $valor[11])
            ->setCellValue("M$lugar", $valor[12]);

            if ($indice % 2 == 0)
                $this->excel->getActiveSheet()->getStyle("A$lugar:M$lugar")->applyFromArray($estiloColumnasPar);
            else
                $this->excel->getActiveSheet()->getStyle("A$lugar:M$lugar")->applyFromArray($estiloColumnasImpar);
            $lugar += 1;
        }

        for($i = 'A'; $i <= 'M'; $i++){
            $this->excel->setActiveSheetIndex(0)            
                ->getColumnDimension($i)->setAutoSize(true);
        }

        if(count($lista_companias) > 0){
            $lugar += 3;
            $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($TipoFont2);
            $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($style2);
            $this->excel->setActiveSheetIndex(0)->mergeCells("A$lugar:E$lugar")->setCellValue("A$lugar", "RESUMEN POR ESTABLECIMIENTO");
            $lugar += 1;
            
            $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "ESTABLECIMIENTO");
            $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "COSTO TOTAL");
            $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "VENTA TOTAL");
            $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "UTILIDAD");
            $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "% UTILIDAD");
            if ($lugar % 2 == 0)
                $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasPar);
            else
                $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasImpar);

            $lugar += 1;

                foreach($lista_companias as $indice => $value){
                    $this->excel->setActiveSheetIndex(0)
                    ->setCellValue("A$lugar", $value->EESTABC_Descripcion)
                    ->setCellValue("B$lugar", $resumen_compania[$value->COMPP_Codigo]['costo'])
                    ->setCellValue("C$lugar", $resumen_compania[$value->COMPP_Codigo]['venta'])
                    ->setCellValue("D$lugar", $resumen_compania[$value->COMPP_Codigo]['util'])
                    ->setCellValue("E$lugar", $resumen_compania[$value->COMPP_Codigo]['porc']);

                    if ($indice % 2 == 0)
                        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasPar);
                    else
                        $this->excel->getActiveSheet()->getStyle("A$lugar:E$lugar")->applyFromArray($estiloColumnasImpar);
                }
        }

        $filename = "Reporte de ganancia desde ".$f_ini." hasta ".$f_fin.".xls"; //save our workbook as this file name

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
        #$this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function promedioVentaExcel($codigo = 'ALL', $fecha = NULL) {

        $lista = '';
        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';

        $producto = ($codigo == "ALL") ? "" : $codigo;

        $fechaIF = explode("-", $fecha);

        $f_ini = ($fecha == NULL) ? "01/".date("m/").date("Y") : "$fechaIF[0]/$fechaIF[1]/$fechaIF[2]";
        $f_fin = ($fecha == NULL) ? date('d/m/Y') : "$fechaIF[3]/$fechaIF[4]/$fechaIF[5]";

        $total_costo = 0;
        $total_venta = 0;
        $total_util = 0;
        $total_porc_util = 0;

        $lista_promedio = $this->comprobantedetalle_model->promedio_ventas_articulos($producto, human_to_mysql($f_ini), human_to_mysql($f_fin));
        $lista = array();
        foreach ($lista_promedio as $value) {
            $fecha = mysql_to_human($value->CPC_Fecha);
            $nombre_producto = $value->PROD_Nombre;
            $marca = $value->MARCC_Descripcion;
            $cantidad = $value->CPDEC_Cantidad;
            $simbolo_moneda = $value->MONED_Simbolo;
            $pventa_min = $value->pventa_minimo;
            $pventa_max = $value->pventa_maximo;
            $precio_promedio = $value->total / $value->cantidad_operaciones;

            $lista[] = array($fecha, $establec, $nombre_producto, $lote_numero, $lote_fv, $cantidad, $simbolo_moneda, number_format($pcosto, 2), number_format($pventa, 2), number_format($costo, 2), number_format($venta, 2), number_format($utilidad, 2), round($porc_util));
        }
        
        $this->load->library('Excel');
        
        ###########################################
        ######### TITULO Y ESTILOS
        ###########################################
            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('Reporte de Ganancia');
            $TipoFont = array( 'font'  => array( 'bold'  => true, 'color' => array('rgb' => '000000'), 'size'  => 16, 'name'  => 'Calibri'));
            $TipoFont2 = array( 'font'  => array( 'bold'  => true, 'color' => array('rgb' => '000000'), 'size'  => 14, 'name'  => 'Calibri'));
            $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
            $style2 = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));

            $this->excel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($TipoFont);
            $this->excel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($style);
            $this->excel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($TipoFont);
            $this->excel->getActiveSheet()->getStyle("A3:H3")->applyFromArray($style);

            $estiloColumnasTitulo = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 11
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
                                                )
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
                                                )
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

            $this->excel->getActiveSheet()->getStyle("A4:H4")->applyFromArray($estiloColumnasTitulo);
        ###########################################

        $this->excel->setActiveSheetIndex(0)->mergeCells('A1:H2')->setCellValue('A1', $_SESSION['nombre_empresa']);
        
        $this->excel->getActiveSheet()->getStyle("A3:H3")->applyFromArray($TipoFont2);
        $this->excel->getActiveSheet()->getStyle("A3:H3")->applyFromArray($style2);
        $this->excel->setActiveSheetIndex(0)->mergeCells("A3:H3")->setCellValue("A3", "REPORTE DE PRECIOS DE VENTA DEL $f_ini HASTA $f_fin");
        
        ###########################################
        ######### TITULO DE COLUMNA RODUCTO
        ###########################################
            $lugar = 4;
            $this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", "ITEM");
            $this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", "DESCRIPCIÓN");
            $this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", "MARCA");
            $this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", "TOTAL P/V.");
            $this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", "N# OPERACIONES");
            $this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", "PRECIO MIN.");
            $this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", "PRECIO MAX.");
            $this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", "PRECIO PROMEDIO.");
        ###########################################

        $numeroS = 0;
        $lugar += 1;
        
        foreach ($lista_promedio as $value){
            $numeroS += 1;
            $fecha = mysql_to_human($value->CPC_Fecha);
            $nombre_producto = $value->PROD_Nombre;
            $marca = $value->MARCC_Descripcion;
            $cantidad = $value->CPDEC_Cantidad;
            $simbolo_moneda = $value->MONED_Simbolo;
            $pventa_min = $value->pventa_minimo;
            $pventa_max = $value->pventa_maximo;
            $precio_promedio = $value->total / $value->cantidad_operaciones;

            $lista[] = array($fecha, $establec, $nombre_producto, $lote_numero, $lote_fv, $cantidad, $simbolo_moneda, number_format($pcosto, 2), number_format($pventa, 2), number_format($costo, 2), number_format($venta, 2), number_format($utilidad, 2), round($porc_util));


            $this->excel->setActiveSheetIndex(0)
            ->setCellValue("A$lugar", $numeroS)
            ->setCellValue("B$lugar", $value->PROD_Nombre)
            ->setCellValue("C$lugar", $value->MARCC_Descripcion)
            ->setCellValue("D$lugar", $value->total)
            ->setCellValue("E$lugar", $value->cantidad_operaciones)
            ->setCellValue("F$lugar", $value->pventa_minimo)
            ->setCellValue("G$lugar", $value->pventa_maximo)
            ->setCellValue("H$lugar", $value->total / $value->cantidad_operaciones);

            if ($indice % 2 == 0)
                $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasPar);
            else
                $this->excel->getActiveSheet()->getStyle("A$lugar:H$lugar")->applyFromArray($estiloColumnasImpar);
            $lugar += 1;
        }

        for($i = 'A'; $i <= 'H'; $i++){
            $this->excel->setActiveSheetIndex(0)            
                ->getColumnDimension($i)->setAutoSize(true);
        }

        $filename = "Reporte de precios de venta desde ".$f_ini." hasta ".$f_fin.".xls"; //save our workbook as this file name

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0"); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        // Forzamos a la descarga
        $objWriter->save('php://output');
        #$this->layout->view('reportes/ventas_por_vendedor', $data);
    }

    public function estado_cuenta() {
        

        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';
        $cliente = $this->input->post('cliente');
        $proveedor = $this->input->post('proveedor');
        $moneda = $this->input->post('moneda') != '' ? $this->input->post('moneda') : '2';
        $f_ini = $this->input->post('fecha_inicio') != '' ? $this->input->post('fecha_inicio') : '01/' . date('m/Y');
        $f_fin = $this->input->post('fecha_fin') != '' ? $this->input->post('fecha_fin') : date('d/m/Y');
        $lista_moneda = $this->moneda_model->obtener($moneda);
        $moneda_simbolo = $lista_moneda[0]->MONED_Simbolo;
        $total_saldo = 0;
        $lista = array();
        $lista_ultimos = array();
        if ($cliente != '' || $proveedor != '') {
            $listado_cuentas = $this->cuentas_model->buscar(($cliente != '' ? '1' : '2'), ($cliente != '' ? $cliente : $proveedor), array('V', 'A', 'C'), human_to_mysql($f_ini), human_to_mysql($f_fin));
            foreach ($listado_cuentas as $value) {
                $fecha = mysql_to_human($value->CUE_FechaOper);
                $tipo_docu = $value->CPC_TipoDocumento == 'F' ? 'FAC' : 'B';
                $numero = $value->CPC_Serie . '-' . $value->CPC_Numero;
                $simbolo_moneda = $value->MONED_Simbolo;
                $monto = $value->CUE_Monto;
                $monto = cambiar_moneda($monto, $value->CPC_TDC, $value->MONED_Codigo, $moneda);

                $listado_pago = $this->cuentaspago_model->listar($value->CUE_Codigo);
                $lista_pago = array();
                if(count($listado_pago)>0){
                    foreach ($listado_pago as $pago){
                        $lista_pago[] = array(mysql_to_human($pago->PAGC_FechaOper), $pago->MONED_Simbolo, number_format($pago->CPAGC_Monto, 2), $this->pago_model->obtener_forma_pago($pago->PAGC_FormaPago), $pago->PAGC_Obs);
                    }
                
                }
                $saldo = $monto - $this->pago_model->sumar_pagos($listado_pago, $moneda);
                $total_saldo+=$saldo;
                $estado = $value->CUE_FlagEstadoPago == 'C' ? 'CANC' : 'ACT';
                $lista[] = array($fecha, $tipo_docu, $numero, $simbolo_moneda, number_format($monto, 2), $lista_pago, number_format($saldo, 2), $estado);
            }
            $listado_pago = $this->pago_model->listar_ultimos(($cliente != '' ? '1' : '2'), ($cliente != '' ? $cliente : $proveedor), 10);
            $lista_utlimos = array();
            foreach ($listado_pago as $pago) {
                $lista_ultimos[] = array(mysql_to_human($pago->PAGC_FechaOper), $pago->MONED_Simbolo, number_format($pago->PAGC_Monto, 2), $this->pago_model->obtener_forma_pago($pago->PAGC_FormaPago), $pago->PAGC_Obs);
            }
        }



        $data['cliente'] = $cliente;
        $data['ruc_cliente'] = $this->input->post('ruc_cliente');
        $data['nombre_cliente'] = $this->input->post('nombre_cliente');
        $data['proveedor'] = $proveedor;
        $data['ruc_proveedor'] = $this->input->post('ruc_proveedor');
        $data['nombre_proveedor'] = $this->input->post('nombre_proveedor');
        $data['moneda_simbolo'] = $moneda_simbolo;
        $data['cboMoneda'] = form_dropdown("moneda", $this->moneda_model->seleccionar(), $moneda, " class='comboMedio' id='moneda' style='width:150px'");
        $data['f_ini'] = $f_ini;
        $data['f_fin'] = $f_fin;
        $data['lista'] = $lista;
        $data['lista_ultimos'] = $lista_ultimos;
        $data['total_saldo'] = number_format($total_saldo, 2);
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/estado_cuenta', $data);
    }

    public function descargarExcel($fechaini, $fechafin){
        $resultado = $this->rptventas_model->ventas_por_dia($fechaini, $fechafin);

        $this->load->library('Excel');
        $hoja = 0;

        ###########################################
        ######### ESTILOS
        ###########################################
            $estiloTitulo = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 14
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
                                                'size' => 11
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
                                                )
                                            ),
                                            'fill'  => array(
                                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('argb' => 'FFFFFFFF')
                                            ),
                                            'alignment' =>  array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                    'wrap'          => TRUE
                                            ),
                                            'borders' => array(
                                                'allborders' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                    'color' => array( 'rgb' => "000000")
                                                )
                                            )
                                        );

            $estiloColumnasImpar = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => false,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                )
                                            ),
                                            'fill'  => array(
                                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('argb' => 'DCDCDCDC')
                                            ),
                                            'alignment' =>  array(
                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                                    'wrap'          => TRUE
                                            ),
                                            'borders' => array(
                                                'allborders' => array(
                                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                    'color' => array( 'rgb' => "000000")
                                                )
                                            )
                                        );
            $estiloBold = array(
                                            'font' => array(
                                                'name'      => 'Calibri',
                                                'bold'      => true,
                                                'color'     => array(
                                                    'rgb' => '000000'
                                                ),
                                                'size' => 11
                                            )
                                        );

            # ROJO PARA ANULADOS
            $colorCelda = array(
                                    'font' => array(
                                        'name'      => 'Calibri',
                                        'bold'      => false,
                                        'color'     => array(
                                            'rgb' => '000000'
                                        )
                                    ),
                                    'fill'  => array(
                                        'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('argb' => "F28A8C")
                                    )
                                );

        ###########################################################################
        ###### HOJA 0 INGRESOS POR DIA
        ###########################################################################

            $tituloReporte = "Reporte de venta por dia";
            $titulosColumnas = array('FECHA DE COMPROBANTE', 'FECHA DE ULTIMO PAGO', 'NRO DOCUMENTO', 'VENTA S/', 'VENTA US$', 'CANCELADO', 'PENDIENTE', 'ESTADO');
            
            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:H1');
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('25');
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('25');
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('25');
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth('25');
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth('25');
            $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth('35');

            $this->excel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloColumnasTitulo);
                            
            // Se agregan los titulos del reporte
            $this->excel->setActiveSheetIndex($hoja)
                        ->setCellValue('A1',  $tituloReporte)
                        ->setCellValue('A3',  $titulosColumnas[0])
                        ->setCellValue('B3',  $titulosColumnas[1])
                        ->setCellValue('C3',  $titulosColumnas[2])
                        ->setCellValue('D3',  $titulosColumnas[3])
                        ->setCellValue('E3',  $titulosColumnas[4])
                        ->setCellValue('F3',  $titulosColumnas[5])
                        ->setCellValue('G3',  $titulosColumnas[6])
                        ->setCellValue('H3',  $titulosColumnas[7]);
                
            $i = 4;
            $tota_dolares = 0;
            $tota_soles = 0;

            $pago_soles = 0;
            $pago_dolares = 0;

            $pendiente_soles = 0;
            $pendiente_dolares = 0;

            foreach ($resultado as $value) {
                $numero = $value['SERIE'] ."-". $value['NUMERO'];
     
                $pago = ( $value['FORPAP_Codigo'] == 1 ) ? $value['VENTAS'] : $this->ventas_model->total_pagos($value['CUE_Codigo'], $fechaini, $fechafin);
                $pendiente = $value['VENTAS'] - $pago;
     
                if( $value['MONED_Codigo'] == 2 ){
                    $soles = "0.00";
                    $dolares = $value['VENTAS'];
                    
                    $tota_dolares = $tota_dolares + $dolares;
                    $pago_dolares += $pago;
                    $pendiente_dolares += $pendiente;
                }else{
                    $soles = $value['VENTAS'];
                    $dolares = "0.00";   
                    
                    $tota_soles = $tota_soles + $soles;
                    $pago_soles += $pago;
                    $pendiente_soles += $pendiente;
                }

                switch ($value['CPC_FlagEstado']) {
                    case '0':
                        $status = "ANULADO";
                        $color = "F28A8C";
                        break;

                    default:
                        $status = "APROBADO";
                        $color = "FFFFFF";
                        break;
                }

                $this->excel->setActiveSheetIndex($hoja)
                        ->setCellValue("A$i",  $value['FECHA'])
                        ->setCellValue("B$i",  $value['FECHAPAGO'])
                        ->setCellValue("C$i",  $numero)
                        ->setCellValue("D$i",  $soles)
                        ->setCellValue("E$i",  $dolares)
                        ->setCellValue("F$i",  $pago)
                        ->setCellValue("G$i",  $pendiente)
                        ->setCellValue("H$i",  $status);

                if ( $value['CPC_FlagEstado'] == 0 )
                    $this->excel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($colorCelda);

                $i++;
            }
                
            $this->excel->setActiveSheetIndex($hoja)
                        ->setCellValue("C$i", "TOTAL S/")
                        ->setCellValue("D$i", $tota_soles)
                        ->setCellValue("E$i", '')
                        ->setCellValue("F$i", $pago_soles)
                        ->setCellValue("G$i", $pendiente_soles);
            $i++;
            $this->excel->setActiveSheetIndex($hoja)
                        ->setCellValue("C$i", "TOTAL US$")
                        ->setCellValue("D$i", '')
                        ->setCellValue("E$i", $tota_dolares)
                        ->setCellValue("F$i", $pago_dolares)
                        ->setCellValue("G$i", $pendiente_dolares);
            
            $i--;
            $this->excel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($estiloColumnasTitulo);
            $i++;
            $this->excel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($estiloColumnasTitulo);

            for($i = 'A'; $i < 'D'; $i++){
                $this->excel->setActiveSheetIndex($hoja)->getColumnDimension($i)->setAutoSize(true);
            }
            
            # Se asigna el nombre a la hoja
            $this->excel->getActiveSheet()->setTitle('Ingreso Diario');
            # Se activa la hoja para que sea la que se muestre cuando el archivo se abre
            #$this->excel->setActiveSheetIndex($hoja);
            # INMOBILIZAR FILA
            $this->excel->getActiveSheet($hoja)->freezePaneByColumnAndRow(0,4);

        ###########################################################################
        ###### HOJA 1 VENTAS DEL DIA
        ###########################################################################
            $hoja++;
            $this->excel->createSheet($hoja);
            $this->excel->setActiveSheetIndex($hoja); //Seleccionar la pestaña deseada
            $this->excel->getActiveSheet()->setTitle('Ventas diarias general'); //Establecer nombre

            $this->excel->getActiveSheet()->getStyle("A1:G2")->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle("A3:G3")->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:G2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
            $this->excel->setActiveSheetIndex($hoja)->mergeCells("A3:G3")->setCellValue("A3", "DETALLE DE VENTAS DESDE $fechaini HASTA $fechafin");
            
            $lugar = 4;
            $numeroS = 0;

            $resumen = $this->rptventas_model->resumen_ventas($fechaini, $fechafin);

            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "FECHA DOC.");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "FECHA REG.");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("C$lugar", "SERIE/NUMERO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("D$lugar", "CLIENTE");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("E$lugar", "TOTAL");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", "NOTA DE CREDITO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", "TOTAL");
            $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasTitulo);

            if ($resumen != NULL){
                $lugar++;
                foreach($resumen as $indice => $valor){
                    $fRegistro = explode(" ", $valor->CPC_FechaRegistro);
                    $this->excel->setActiveSheetIndex($hoja)
                    ->setCellValue("A$lugar", $valor->CPC_Fecha)
                    ->setCellValue("B$lugar", $fRegistro[0])
                    ->setCellValue("C$lugar", $valor->CPC_Serie." - ".$valor->CPC_Numero)
                    ->setCellValue("D$lugar", $valor->clienteEmpresa.$valor->clientePersona)
                    ->setCellValue("E$lugar", $valor->CPC_total)
                    ->setCellValue("F$lugar", $valor->CRED_Serie."-".$valor->CRED_Numero)
                    ->setCellValue("G$lugar", $valor->CRED_Total);
                    if ($indice % 2 == 0)
                        $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasPar);
                    else
                        $this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasImpar);
                    $lugar++;
                }
                $lugar++;
            }

            $this->excel->getActiveSheet()->getColumnDimension("D")->setWidth("45");

            for($i = 'A'; $i <= 'G'; $i++){
                if ($i != "D")
                    $this->excel->setActiveSheetIndex($hoja)->getColumnDimension($i)->setAutoSize(true);
            }

        ###########################################################################
        ###### HOJA 2 VENTAS DEL DIA DETALLADO
        ###########################################################################
            $hoja++;
            $this->excel->createSheet($hoja);
            $this->excel->setActiveSheetIndex($hoja); //Seleccionar la pestaña deseada
            $this->excel->getActiveSheet()->setTitle('Ventas diarias detallado'); //Establecer nombre

            $this->excel->getActiveSheet()->getStyle("A1:O2")->applyFromArray($estiloTitulo);
            $this->excel->getActiveSheet()->getStyle("A3:O3")->applyFromArray($estiloColumnasTitulo);

            $this->excel->setActiveSheetIndex($hoja)->mergeCells('A1:O2')->setCellValue('A1', $_SESSION['nombre_empresa']);        
            $this->excel->setActiveSheetIndex($hoja)->mergeCells("A3:O3")->setCellValue("A3", "DETALLE DE VENTAS DESDE $fechaini HASTA $fechafin");
            
            $lugar = 4;
            $numeroS = 0;

            $resumen = $this->rptventas_model->resumen_ventas_detallado($fechaini, $fechafin);

            $this->excel->setActiveSheetIndex($hoja)->setCellValue("A$lugar", "FECHA DOC.");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("B$lugar", "FECHA REG.");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("C$lugar", "SERIE/NUMERO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("D$lugar", "CLIENTE");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("E$lugar", "NOMBRE DE PRODUCTO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("F$lugar", "LAB.");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("G$lugar", "LOTE");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("H$lugar", "FECHA VCTO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("I$lugar", "CANTIDAD");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("J$lugar", "P/U");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("K$lugar", "TOTAL");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("L$lugar", "NOTA DE CREDITO");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("M$lugar", "CANTIDAD");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("N$lugar", "P/U");
            $this->excel->setActiveSheetIndex($hoja)->setCellValue("O$lugar", "TOTAL");
            $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasTitulo);

            if ($resumen != NULL){
                $lugar++;
                foreach($resumen as $indice => $valor){
                    $fRegistro = explode(" ", $valor->CPC_FechaRegistro);
                    $this->excel->setActiveSheetIndex($hoja)
                    ->setCellValue("A$lugar", $valor->CPC_Fecha)
                    ->setCellValue("B$lugar", $fRegistro[0])
                    ->setCellValue("C$lugar", $valor->CPC_Serie." - ".$valor->CPC_Numero)
                    ->setCellValue("D$lugar", $valor->clienteEmpresa.$valor->clientePersona)
                    ->setCellValue("E$lugar", $valor->PROD_Nombre)
                    ->setCellValue("F$lugar", $valor->MARCC_CodigoUsuario)
                    ->setCellValue("G$lugar", $valor->LOTC_Numero)
                    ->setCellValue("H$lugar", $valor->LOTC_FechaVencimiento)
                    ->setCellValue("I$lugar", $valor->CPDEC_Cantidad)
                    ->setCellValue("J$lugar", $valor->CPDEC_Pu_ConIgv)
                    ->setCellValue("K$lugar", $valor->CPDEC_Total)
                    ->setCellValue("L$lugar", $valor->CRED_Serie."-".$valor->CRED_Numero)
                    ->setCellValue("M$lugar", $valor->CREDET_Cantidad)
                    ->setCellValue("N$lugar", $valor->CREDET_Pu_ConIgv)
                    ->setCellValue("O$lugar", $valor->CREDET_Total);
                    if ($indice % 2 == 0)
                        $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasPar);
                    else
                        $this->excel->getActiveSheet()->getStyle("A$lugar:O$lugar")->applyFromArray($estiloColumnasImpar);
                    $lugar++;
                }
                $lugar++;
            }

            $this->excel->getActiveSheet()->getColumnDimension("D")->setWidth("25");
            $this->excel->getActiveSheet()->getColumnDimension("E")->setWidth("25");

            for($i = 'A'; $i <= 'O'; $i++){
                if ($i != 'D' && $i != 'E')
                $this->excel->setActiveSheetIndex($hoja)->getColumnDimension($i)->setAutoSize(true);
            }


        $filename = "Reporte-".date('Y-m-d').".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function registro_ventas($tipo_oper, $tipo = 'F', $fecha1 = '', $fecha2 = '') {

        

        if ($tipo_oper == 'V')
            $data['titulo'] = "Registro de Ventas Desde " . $fecha1 . " Hasta " . $fecha2;
        else
            $data['titulo'] = "Registro de Compras Desde " . $fecha1 . " Hasta " . $fecha2;
        
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '');
        $data['cboVendedor'] = $this->lib_props->listarVendedores();
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '');
        $data['tipo_docu'] = $tipo;
        $data['tipo_oper'] = $tipo_oper;
        if ($tipo_oper == 'V')
            $data['titulo_tabla'] = "Registro de Ventas ";
        else
            $data['titulo_tabla'] = "Registro de Compras ";
        //$data['lista'] = $this->rptventas_model->registro_ventas($tipo_oper, $tipo, $fecha1, $fecha2,$forma_pago,$vendedor);
        // echo $this->db->last_query();
        $data['fecha1'] = $fecha1;
        $data['fecha2'] = $fecha2;
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/registro_ventas', $data);
    }

    public function registro_ventas_old($tipo_oper, $tipo = 'F', $fecha1 = '', $fecha2 = '') {

        

        if ($tipo_oper == 'V')
            $data['titulo'] = "Registro de Ventas Desde " . $fecha1 . " Hasta " . $fecha2;
        else
            $data['titulo'] = "Registro de Compras Desde " . $fecha1 . " Hasta " . $fecha2;

        $data['tipo_docu'] = $tipo;
        $data['tipo_oper'] = $tipo_oper;
        if ($tipo_oper == 'V')
            $data['titulo_tabla'] = "Registro de Ventas ";
        else
            $data['titulo_tabla'] = "Registro de Compras ";

        $data['lista'] = $this->rptventas_model->registro_ventas($tipo_oper, $tipo, $fecha1, $fecha2);
        $data['anio'] = $this->rptventas_model->getAnioVentas();
        $data['fecha1'] = $fecha1;
        $data['fecha2'] = $fecha2;
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->layout->view('reportes/registro_ventas', $data);
    }

    public function registro_ventas_table(){

        $tipo_oper      = $this->input->post('tipo_oper');
        $tipo           = $this->input->post('tipo_doc');
        $fecha1         = $this->input->post('fecha1');
        $fecha2         = $this->input->post('fecha2');
        $forma_pago     = $this->input->post('forma_pago');
        $vendedor       = $this->input->post('vendedor');
        $moneda         = $this->input->post('moneda');
        $consolidado    = $this->input->post('consolidado');

        if (isset($fecha1) && $fecha1!="" && $fecha1!='1') {
            $fecha1 = $this->input->post('fecha1');
        }
        else{
            $fecha1=date('Y-m-d');
        }
        
        if (isset($fecha2) && $fecha2!="" && $fecha2!='1') {
            $fecha2=$this->input->post('fecha2');
        }
        else{
            $fecha2=date('Y-m-d');
        }
   
        if($tipo_oper=="V"){
            $operacion="Ventas";
        }
        else{
            $operacion="Compras";
        }

        $columns = array(
                            0 => "mes",
                            1 => "CPC_Fecha",
                            2 => "CPC_subtotal",
                            3 => "CPC_igv",
                            4 => "CPC_total",
                            5 => "CPC_TDC",
                            6 => "COMPP_Codigo",
                            7 => "CPC_Serie",
                            8 => "CPC_Numero",
                            9 => "CPC_TipoDocumento",
                            10 => "CPC_FlagEstado",
                            11 => "MONED_Codigo",
                            12 => "MONED_Simbolo",
                            13 => "MONED_Descripcion",
                            14 => "razon_social_cliente",
                            15 => "numero_documento_cliente",
                            16 => "razon_social_proveedor",
                            17 => "numero_documento_proveedor",
                            18 => "gravada",
                            19 => "exonerada",
                            20 => "inafecta",
                            21 => "gratuita",
                            22 => "FORPAC_Descripcion"
                        );

        $params = new stdClass();
        $params->search = $this->input->post("search")["value"];
        $params->limit = $this->input->post("start") . ", " . $this->input->post("length");
        $params->order .= ( $columns[$this->input->post("order")[0]["column"]] != "" && $columns[$this->input->post("order")[0]["dir"]] != "" ) ? $columns[$this->input->post("order")[0]["column"]] . ", " . $columns[$this->input->post("order")[0]["dir"]] : "$columns[1] ASC";
        
        $filter = new stdClass();
        $filter->tipo_oper      = $tipo_oper;
        $filter->tipo           = $tipo;
        $filter->fecha1         = $fecha1;
        $filter->fecha2         = $fecha2;
        $filter->forma_pago     = $forma_pago;
        $filter->vendedor       = $vendedor;
        $filter->moneda         = $moneda;
        $filter->consolidado    = $consolidado;
        
        $info = $this->rptventas_model->resumen_ventas_mensual($filter);

        $cantidad_fac   = 0;
        $total_fac      = 0;
        $total_bol      = 0;
        $total_comp     = 0;
        $total_nota     = 0;
        $total          = 0;
        $total_fac_dolar      = 0;
        $total_bol_dolar      = 0;
        $total_comp_dolar     = 0;
        $total_nota_dolar     = 0;
        $total_dolar          = 0;
        foreach ($info as $row => $col) {
            //LA CONSULTA TRAE UNO DE LOS CAMPOS COMO "INDEFINIDA" => AQUI SE ELIMINA LA PALABRA PARA QUE NO APAREZCA EN LA VISTA
            $resultado = str_replace("indefinida", "", $col->razon_social_cliente);
            $col->razon_social_cliente = $resultado;
            $tachado1="";
            $tachado2="";
            $fecha = explode("-", $col->CPC_Fecha);
            $col->CPC_Fecha = $fecha[2]."/".$fecha[1]."/".$fecha[0];


            if($col->CPC_FlagEstado=='1'){
                
                if ($col->MONED_Codigo==1) {
                        $total += $col->CPC_total;
                }elseif($col->MONED_Codigo==2) {
                        $total_dolar+=$col->CPC_total;
                }
                if($col->CPC_TipoDocumento=="F"){
                    $col->CPC_TipoDocumento="FACTURA";
                    if ($col->MONED_Codigo==1) {
                        $total_fac += $col->CPC_total;
                    }elseif($col->MONED_Codigo==2) {
                        $total_fac_dolar+=$col->CPC_total;
                    }
                }
                if($col->CPC_TipoDocumento=="P"){
                    $col->CPC_TipoDocumento="PEDIDO";
                    
                }if($col->CPC_TipoDocumento=="B"){
                   
                    if ($col->MONED_Codigo==1) {
                        $total_bol += $col->CPC_total;
                    }elseif($col->MONED_Codigo==2) {
                        $total_bol_dolar+=$col->CPC_total;
                    }
                     $col->CPC_TipoDocumento="BOLETA";
                }if($col->CPC_TipoDocumento=="N"){
                   
                    if ($col->MONED_Codigo==1) {
                        $total_comp += $col->CPC_total;
                    }elseif($col->MONED_Codigo==2) {
                        $total_comp_dolar+=$col->CPC_total;
                    }
                     $col->CPC_TipoDocumento="COMPROBANTE";
                }if($col->CPC_TipoDocumento=="C"){
                   
                    if ($col->MONED_Codigo==1) {
                        $total_nota += $col->CPC_total;
                    }elseif($col->MONED_Codigo==2) {
                        $total_nota_dolar+=$col->CPC_total;
                    }
                     $col->CPC_TipoDocumento="NOTA CREDITO";
                     $col->FORPAC_Descripcion="-";
                }

                $col->CPC_FlagEstado='<font color="green">APROBADO</font>';
            }elseif($col->CPC_FlagEstado=='0'){
                if($col->CPC_TipoDocumento=="F"){
                    $col->CPC_TipoDocumento="FACTURA";
                    
                }if($col->CPC_TipoDocumento=="B"){
                   
                     $col->CPC_TipoDocumento="BOLETA";
                }if($col->CPC_TipoDocumento=="N"){
                    
                     $col->CPC_TipoDocumento="COMPROBANTE";
                }if($col->CPC_TipoDocumento=="C"){
                    
                     $col->CPC_TipoDocumento="NOTA CREDITO";
                     $col->FORPAC_Descripcion="-";
                }if($col->CPC_TipoDocumento=="P"){
                    
                     $col->CPC_TipoDocumento="PEDIDO";
                     
                }
                $col->CPC_FlagEstado='<font color="red">ANULADO</font>';
                $tachado1="<strike>";
                $tachado2="</strike>";
            }
            if ($tipo_oper=="V") {
                $denominacion   = $col->razon_social_cliente;
                $num_doc        = $col->numero_documento_cliente;
            }else{
                $denominacion   = $col->razon_social_proveedor;
                $num_doc        = $col->numero_documento_proveedor;

            }


            $item=$row+1;
           
           
            $data[$row] = array(
                                "item"          => $item,//0
                                "fecha"         => $col->CPC_Fecha,//1
                                "subtotal"      => $col->CPC_subtotal,//2
                                "igv"           => $col->CPC_igv,//3
                                "total"         => $col->CPC_total,//4
                                "tdc"           => $col->CPC_TDC,//5
                                "COMPP_Codigo"  => $col->COMPP_Codigo,//6
                                "serie"         => $col->CPC_Serie,//7
                                "numero"        => $col->CPC_Numero,//8
                                "tipo_documento"=> $col->CPC_TipoDocumento,//9
                                "estado"        => $col->CPC_FlagEstado,//10
                                "MONED_Codigo"  => $col->MONED_Codigo,//11
                                "MONED_Simbolo" => $col->MONED_Simbolo,//12
                                "moneda"        => $col->MONED_Descripcion,//13
                                "razon_social"  => $denominacion,//14
                                "num_doc"       => $num_doc,//15
                                "proveedor"     => $col->razon_social_proveedor,//16
                                "ruc_proveedor" => $col->numero_documento_proveedor,//17
                                "gravada"       => $col->gravada,//18
                                "exonerada"     => $col->exonerada,//19
                                "inafecta"      => $col->inafecta,//20
                                "gratuita"      => $col->gratuita,//21
                                "FORPAC_Descripcion" => $col->FORPAC_Descripcion,//22
                                "MONED_Descripcion" => $col->MONED_Descripcion,//23
                                "tachado1" => $tachado1,//24
                                "tachado2" => $tachado2//25
                               
                            );

        }
         $totales = array(
                                "total"             => $total, //0
                                "total_fac"         => $total_fac, //1
                                "total_bol"         => $total_bol, //2
                                "total_comp"        => $total_comp, //3
                                "total_nota"        => $total_nota, //4
                                "total_fac_dolar"   => $total_fac_dolar, //5
                                "total_bol_dolar"   => $total_bol_dolar, //6
                                "total_comp_dolar"  => $total_comp_dolar, //7
                                "total_nota_dolar"  => $total_nota_dolar, //8
                                "total_dolar"       => $total_dolar, //9
                                "cantidad"          => $item //10
                                                               
                            );
        $datos = array('data' =>$data,'totales' =>$totales);
        $json = $datos;

        echo json_encode($json);
    }
}