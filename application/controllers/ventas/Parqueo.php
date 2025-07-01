<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parqueo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Lima');        
        $this->load->model('almacen/familia_model');
        $this->load->model('almacen/productoprecio_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('maestros/compania_model');	
        $this->load->model('maestros/persona_model');			
        $this->load->model('maestros/tarifa_model');	
        $this->load->model('maestros/ubicacion_model');	 			
        $this->load->model('seguridad/permiso_model');
        $this->load->model('seguridad/menu_model');
        $this->load->model('seguridad/usuario_model');
        $this->load->model('seguridad/usuario_compania_model');
        $this->load->model('seguridad/rol_model');
        $this->load->model('ventas/parqueo_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('tesoreria/caja_model');
        $this->load->library('fpdfgenerator');	
        $this->load->library('lib_comprobantes');
        $this->load->library('pdfgenerator');	
        $this->load->library('lib_props');	
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->base_url = base_url();
        $this->view_js = array(0 => "ventas/parqueo.js");			
    }

    public function index($msg = NULL)
    {	
        $data["tarifas"] = $this->tarifa_model->getTarifas();
        $data['scripts'] = $this->view_js;
        $data['rol']     = $_SESSION['rol'];
        $this->layout->view("ventas/parqueo_index", $data);    
    }
  
  public function datatable_parqueo(){
    $posDT = -1;
    $columnas = array(
        ++$posDT => "PARQC_Numero",
        ++$posDT => "PARQC_Placa",
        ++$posDT => "PARQC_FechaIn",
        ++$posDT => "PARQC_HoraIn",
        ++$posDT => "PARQC_HoraSalida",
        ++$posDT => "TARIFC_Descripcion",
        ++$posDT => "CPC_Serie",
        ++$posDT => "CPC_Numero",
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
    $filter->serie  = $this->input->post('serie');
    $filter->numero = $this->input->post('numero');
    $filter->ticket = $this->input->post('ticket');
    
    $tarifasInfo = $this->parqueo_model->getParqueos($filter,false);
    
    $records = array();
        if ( $tarifasInfo["records"] != NULL ) {
            foreach ($tarifasInfo["records"] as $indice => $valor) {
                
                $situacion = $valor->PARQC_FlagSituacion;
                $estado    = $valor->CPC_FlagEstado;
                
                if($situacion == 1)
                    $btn_editar = "<button type='button' onclick='editar($valor->PARQP_Codigo)' class='btn btn-default'><img src='".$this->base_url."/public/images/icons/modificar.png' class='image-size-1b'></button>";
                else
                    $btn_editar = "<button type='button' class='btn btn-default'><img src='".$this->base_url."/public/images/icons/modificar-grises.png' class='image-size-1b'></button>";
                
                //Opciones para anular TICKETs
                if($situacion == 1 || $situacion == 2)//Ticket emitido o calculado
                    $btn_borrar = "<button type='button' onclick='deshabilitar($valor->PARQP_Codigo,$situacion)' class='btn btn-default'>
                              <img src='".$this->base_url."/public/images/icons/documento-delete-mod.png' class='image-size-1b'></button>";
                else
                    $btn_borrar = "<button type='button' class='btn btn-default'>
                              <img src='".$this->base_url."/public/images/icons/documento-delete-mod-grises.png' class='image-size-1b'></button>";        
                
                if($situacion == 3 && $estado == 1)//Ticket facturado       
                    //$btn_factura = "<a href='".base_url()."index.php/ventas/comprobante/comprobante_ver_pdf/$valor->CPP_Codigo/TICKET' data-fancybox data-type='iframe'><img src='".base_url()."public/images/icons/pdf.png' width='25' height='25' border='0' title='ver comprobante'></a>";      
                   $btn_factura = "<a href='#' onclick='abrir_pdf_envioSunat($valor->CPP_Codigo);' target='_parent'><img src='".base_url()."public/images/icons/pdf.png' width='25' height='25' border='0' title='ver comprobante'></a>";
                else
                   $btn_factura = "<img src='".base_url()."public/images/icons/pdf_grises.png' width='25' height='25' border='0' title='ver comprobante'>";      
                $btn_ticket = "<a href='".base_url()."index.php/ventas/parqueo/imprimir_ticket_pdf/$valor->PARQP_Codigo/TICKET' "
                        . "data-fancybox data-type='iframe'>"
                        . "<img src='".base_url()."public/images/barcode.png' width='40' height='40' border='0' title='ver ticket'></a>";                  
                
                $msgSituacion = "";

                switch($situacion){
                    case 0:
                        $msgSituacion = "<span class='color-red'>ANULADO</span>";
                        break;
                    case 1:
                        $msgSituacion = "<span class='color-black'>EMITIDO</span>";
                        break;
                    case 2:
                        $msgSituacion = "<span class='color-black'>PENDIENTE</span>";
                        break;
                    case 3:
                        $msgSituacion = "<span class='color-green'>FACTURADO.</span>";
                        break;                    
                }
                
                $numero = $valor->CPC_Numero != ""?str_pad($valor->CPC_Numero,4,"0",STR_PAD_LEFT):"";
                
                $posDT = -1;
                $records[] = array(
                        ++$posDT => $valor->PARQC_Numero,
                        ++$posDT => $valor->PARQC_Placa,
                        ++$posDT => $valor->PARQC_FechaIn,
                        ++$posDT => $valor->PARQC_HoraIn,
                        ++$posDT => $valor->PARQC_HoraSalida,
                        ++$posDT => $valor->TARIFC_Descripcion,   
                        ++$posDT => $valor->CPC_Serie, 
                        ++$posDT => $numero, 
                        ++$posDT => $msgSituacion,
                        ++$posDT => $this->lib_comprobantes->convertir_fecha_numero($valor->PARQC_FechaRegistro),
                        ++$posDT => $btn_ticket,
                        ++$posDT => $btn_factura,
                        ++$posDT => $btn_editar,
                        ++$posDT => $btn_borrar
                );
            }
    	}
        $recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
        $recordsAcum   = $tarifasInfo["recordsAcum"];
        $recordsFilter = $tarifasInfo["recordsFilter"];
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

    public function guardar_registro(){
        
        $parqueo = $this->input->post("parqueo");
        $parqueo_placa     = strtoupper($this->input->post("parqueo_placa_edit"));
        $parqueo_tarifa    = $this->input->post("parqueo_tarifa_edit");
        $parqueo_fingreso  = $this->input->post("parqueo_fingreso_edit");
        $parqueo_hingreso  = $this->input->post("parqueo_hingreso_edit");
        $parqueo_fsalida   = $this->input->post("parqueo_fsalida_edit");
        $parqueo_hsalida   = $this->input->post("parqueo_hsalida_edit");
        $parqueo_tiempo    = $this->input->post("parqueo_tiempo_edit");
        $parqueo_monto     = $this->input->post("parqueo_monto_edit");
        $parqueo_observacion = $this->input->post("parqueo_observacion_edit");
        
        $msj = "";

        //Actualizamos ticket - (solo actualiza la tarifa)
        if ($parqueo != ""){

            //Datos del ticket
            $datos_parqueo = $this->parqueo_model->getParqueo($parqueo);
            $producto      = $datos_parqueo[0]->PROD_Codigo;
            $tarifa_ant    = $datos_parqueo[0]->TARIFP_Codigo;
            
            //Datos de la tarifa anterior
            $datos_tarifa_ant = $this->tarifa_model->getTarifa($tarifa_ant);
            $descripcion_ant  = $datos_tarifa_ant[0]->TARIFC_Descripcion;
            
            //Datos de la tarifa actual
            $datos_tarifa = $this->tarifa_model->getTarifa($parqueo_tarifa);
            $tipo_tarifa  = trim($datos_tarifa[0]->TARIFC_Tipo);
            $descripcion  = $datos_tarifa[0]->TARIFC_Descripcion;
            $precio       = $datos_tarifa[0]->TARIFC_Precio;
            
            //Colocar precio a las tarifas plana y se puedan facturar en cualquier momento
            $productoprecios = $this->productoprecio_model->getProductoPrecios($producto);
            if($productoprecios != NULL){
                $prodprecio       = $productoprecios[0]->PRODPREP_Codigo;
                if($tipo_tarifa == 3){
                    $filterprodprecio = new stdClass();
                    $filterprodprecio->PRODPREC_Precio = $precio;
                    $this->productoprecio_model->actualizar_producto_precio($prodprecio,$filterprodprecio);
                }
                else{
                    $filterprodprecio = new stdClass();
                    $filterprodprecio->PRODPREC_Precio = 0;
                    $this->productoprecio_model->actualizar_producto_precio($prodprecio,$filterprodprecio); 
                }
            }
            
            //Opcion ACTUALIZAR
            $filter = new stdClass();
            $filter->TARIFP_Codigo           = $parqueo_tarifa;       
            $filter->PARQC_Observacion       = "El usuario ".$_SESSION['nombre_persona']." cambio la tarifa de ".$descripcion_ant." a ".$descripcion;//HMTB - 16/12/2021
            $filter->PARQC_FechaModificacion = date("Y-m-d H:i:s");
            $result = $this->parqueo_model->actualizar_parqueo($parqueo, $filter);
            
        }
        //Nuevo ticket
        else{
            //Obtengo el número correlativo según compania
            $datosMax = $this->parqueo_model->getMaxParqueo();
            $numero   = $datosMax[0]->maximo + 1 ;  
            
            //Crea producto y precio
            $descripcion = "SERV. ESTACIONAMIENTO PLACA ".$parqueo_placa;        
            $producto    = $this->lib_comprobantes->crear_servicio($descripcion,$parqueo_monto);            
            
            //Guardamos registro
            $filter = new stdClass();
            $filter->PARQC_Placa         = $parqueo_placa;
            $filter->TARIFP_Codigo       = $parqueo_tarifa;
            $filter->COMPP_Codigo        = $this->compania;            
            $filter->PARQC_FechaIngreso  = $parqueo_fingreso;
            $filter->PARQC_HoraIngreso   = $parqueo_hingreso!=''?$parqueo_hingreso.":00":"00:00:00";            
            $filter->PARQC_FechaSalida   = $parqueo_fsalida;
            $filter->PARQC_HoraSalida    = $parqueo_hsalida!=''?$parqueo_hsalida.":00":"00:00:00";
            $filter->PARQC_Tiempo        = $parqueo_tiempo;
            $filter->PARQC_Monto         = $parqueo_monto;
            $filter->PARQC_Observacion   = $parqueo_observacion;
            $filter->PARQC_FlagEstado    = "1";
            $filter->PARQC_FlagSituacion = "2";
            $filter->PARQC_FlagEstacionamiento = "0";
            $filter->PARQC_FechaRegistro = date("Y-m-d H:i:s");
            $filter->PARQC_Numero        = $numero;
            $filter->PARQC_TipoMensaje   = "MANUAL";
            $filter->PROD_Codigo         = $producto;
            $result = $this->parqueo_model->registrar_parqueo($filter);
            
            //Si es tarifa plana, actualizo el precio del producto
            $datos_tarifa = $this->tarifa_model->getTarifa($parqueo_tarifa);
            $tipo_tarifa  = $datos_tarifa[0]->TARIFP_Codigo; 
            $precio       = $datos_tarifa[0]->TARIFC_Precio; 
            //Tarifa plana
            if($tipo_tarifa == 3){
                $productoprecios = $this->productoprecio_model->getProductoPrecios($producto);
                if($productoprecios != NULL){
                    $prodprecio       = $productoprecios[0]->PRODPREP_Codigo;
                    $filterprodprecio = new stdClass();
                    $filterprodprecio->PRODPREC_Precio = $precio;
                    $this->productoprecio_model->actualizar_producto_precio($prodprecio,$filterprodprecio);
                }               
            } 
            
        }
        if ($result)
            $json = array("result" => "success", "mensaje" => "Registro exitoso.");
        else
            $json = array("result" => "error", "mensaje" => "Error al guardar el registro, intentelo nuevamente.");
        echo json_encode($json);
        die;
    } 
    
    public function getParqueo(){
        $parqueo = $this->input->post('parqueo');
        $data    = $this->parqueo_model->getParqueo($parqueo);
        
        $situacion = $data[0]->PARQC_FlagSituacion;
        $msgsitua  = "";
        
        switch($situacion){
            case 0:
                $msgsitua = "Ticket Anulado";
                break;
            case 1:
                $msgsitua = "Ticket Emitido";
                break;
            case 2:
                $msgsitua = "Ticket Calculado";
                break;
            case 3:
                $msgsitua = "Ticket Facturado";
                break;            
        }
        
        $comprobante = $data[0]->CPP_Codigo != NULL?$data[0]->CPC_Serie."".str_pad($data[0]->CPC_Numero,4,0,STR_PAD_LEFT):"";
        $tipo_tarifa = $data[0]->TARIFC_Tipo;
        $placa       = $data[0]->PARQC_Placa;
        
        $nomabon = "";
        $fpago   = "";
        $sitabon = "";        
        $cod_sitabon = "";      
        if($tipo_tarifa == 2){//Es abonado
            $filter = new stdClass();
            $filter->placa = $placa;
            $datos_abonado = $this->cliente_model->getClienteVehiculos($filter);
            if($datos_abonado['records'] != NULL){
                $nomabon   = $datos_abonado['records'][0]->CLIEVEHIP_Nombres;
                $fpago     = $datos_abonado['records'][0]->CLIC_FechaIngreso;
                $sitabon   = $datos_abonado['records'][0]->CLIC_FlagSituracion == 1?"FACTURADO":"<font color='red'>PENDIENTE</font>";
                $cod_sitabon   = $datos_abonado['records'][0]->CLIC_FlagSituracion;
            }
        }
        
        if ($data != NULL){
                $info = array(
                    "parqueo"       => $data[0]->PARQP_Codigo,
                    "placa"         => $data[0]->PARQC_Placa,
                    "tarifa"        => $data[0]->TARIFP_Codigo,
                    "fingreso"      => $data[0]->PARQC_FechaIngreso,
                    "hingreso"      => $data[0]->PARQC_HoraIngreso,
                    "fsalida"       => $data[0]->PARQC_FechaSalida,
                    "hsalida"       => $data[0]->PARQC_HoraSalida,
                    "codserv"       => $data[0]->PROD_Codigo,
                    "tipotarifa"    => $data[0]->TARIFC_Tipo,
                    "idsituacion"   => $data[0]->PARQC_FlagSituacion,//3: Ticket facturado
                    "situacion"     => $msgsitua,
                    "comprobante"   => $comprobante,
                    "datos_abonado" => array(
                        "nombre_abonado" => $nomabon,
                        "fecha_pago"     => $fpago,
                        "situacion_abon" => $sitabon,
                        "cod_situacion_abon" => $cod_sitabon
                    )
                );
        }
        else    
            $info = NULL;
        if ($info != NULL)
            $json = array("match" => true, "info" => $info);
        else
            $json = array("match" => false, "info" => NULL);
        echo json_encode($json);
    }
    
    public function getParqueoXPlaca(){
        $placa  = strtoupper(trim($this->input->post('placa')));
        $filter = new stdClass();
        $filter->placa  = $placa;
        $filter->estacionamiento = 1;//El ticket se encuentre emitido
        
        $data   = $this->parqueo_model->getParqueoXPlaca($filter,false);
        
        if ($data != NULL){
                $info = array(
                    "parqueo"  => $data[0]->PARQP_Codigo,
                    "placa"    => $data[0]->PARQC_Placa,
                    "tarifa"   => $data[0]->TARIFP_Codigo,
                    "fingreso" => $data[0]->PARQC_FechaIngreso,
                    "hingreso" => $data[0]->PARQC_HoraIngreso,
                    "fsalida"  => $data[0]->PARQC_FechaSalida,
                    "hsalida"  => $data[0]->PARQC_HoraSalida,
                    "codserv"  => $data[0]->PROD_Codigo,
                    "compania" => $data[0]->COMPP_Codigo
                );
        }
        else    
            $info = NULL;
        if ($info != NULL)
            $json = array("match" => true, "info" => $info);
        else
            $json = array("match" => false, "info" => NULL);
        echo json_encode($json);
    }     
    
    /*public function getParqueos(){
        $options = array('comillas' => true, 'apostrofe' => true);
        $parqueo = formatString($this->input->post('parqueo'), $options);
        $placa   = formatString($this->input->post('placa'), $options);
        
        $filter  = new stdClass();
        $filter->searchParqueo = $parqueo;
        $filter->searchPlaca   = $placa;
        $parqueos = $this->parqueo_model->getParqueo($filter);
        $json = array();
        if ($parqueos != NULL) {
                foreach ($parqueos as $row => $col) {
                        $json[] = array(
                                "parqueo"  => $col->PARQP_Codigo,
                        "placa"    => $col->PARQC_Placa,
                        "tarifa"   => $col->TARIFP_Codigo,
                                "fingreso" => $col->PARQC_FechaIngreso,
                                "hingreso" => $col->PARQC_HoraIngreso,
                                "fsalida"  => $col->PARQC_FechaSalida,
                                "hsalida"  => $col->PARQC_HoraSalida
                        );				
                }
        }
        die(json_encode($json));
    }*/

    /*public function imprimir_parqueo(){
        $parqueo  = $this->input->post('parqueo');
        $data['ticket'] = $this->parqueo_model->getParqueo($parqueo);
        $this->load->view('ventas/ticket_ingreso',$data);
        //$html    = $this->load->view('ventas/ticket_ingreso',$data,true);
        //$this->pdfgenerator->generate($html,'TicketIngreso');
    }*/
    
   /*public function imprimir_tickets_activos(){
        
        $tipo_reporte = $this->input->post("tipo_reporte");

        //List of parqueos
        $filter = new stdClass();
        $filter->situacion = 1;
        $ordenar      = $this->input->post("order");
        if (!is_null($ordenar)){
            foreach($ordenar as $indice => $value){
                $arrOrd[$indice] = [$columnas[$value["column"]],$value["dir"]];
            }
            $filter->ordenar = $arrOrd;
        }
        $parqueos = $this->parqueo_model->getParqueos($filter,false);

        $records = array();

        if ( $parqueos["records"] != NULL ) {
            
            foreach ($parqueos["records"] as $indice => $valor) {
                $posDT = -1;
                $records[] = array(
                        ++$posDT => $valor->PARQP_Codigo,
                        ++$posDT => $valor->PARQC_Placa,
                        ++$posDT => $valor->PARQC_FechaIn,
                        ++$posDT => $valor->PARQC_HoraIn,
                        ++$posDT => $valor->TARIFC_Descripcion
                );

            }
            
            $data["header"]  = ['CODIGO','PLACA','FECHAIN','HORAIN','DESCRIPCION'];
            $data["records"] = $records;     
            $filename = "TicketsActivos_".date("Ymd");
            
            if($tipo_reporte == "excel"){	
                $this->excelspout->generate($data,$filename);
            }
            elseif($tipo_reporte == "pdf"){
                $this->fpdfgenerator->generate($data,$filename);   
            }

    	}     

    }*/

    public function imprimir_ticket_pdf($parqueo){
        
        $datos_ticket   = $this->parqueo_model->getParqueo($parqueo);
        $datos_compania = $this->emprestablecimiento_model->getEstablecimiento($this->compania);
                    
        if($datos_ticket != NULL){
            $placa     = $datos_ticket[0]->PARQC_Placa;
            $fingreso  = $datos_ticket[0]->PARQC_FechaIngreso;
            $hingreso  = $datos_ticket[0]->PARQC_HoraIngreso;
            $tarifa    = $datos_ticket[0]->TARIFC_Descripcion;
            $precio    = $datos_ticket[0]->TARIFC_Precio;
            $fregistro = $datos_ticket[0]->PARQC_FechaRegistro;
        }
        
        if($datos_compania != NULL){
            $direccion = $datos_compania[0]->EESTAC_Direccion;
            $telefono  = $datos_compania[0]->EESTABC_Telefono;
        }
        
        $medidas = array(80, 200); 
        
        //Inicia PDF
        $this->pdf = new tcpdf('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(3, 3, 3);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->AddPage();
        
        //Barcode
        $style = array(
            'position'    => 'S', 
            'border'      => false, 
            'padding'     => 0, 
            'fgcolor'     => array(0,0,0), 
            'bgcolor'     => array(255,255,255), 
            'text'        => false, 
            'font'        => 'helvetica', 
            'fontsize'    => 9, 
            'stretchtext' => 4
         );
        $params = $this->pdf->serializeTCPDFtagParameters(array($placa, 'C128', '', '', 60, 40, 0.5, $style, 'N'));
        $barcodeHTML = '
                <table>
                    <tr><td colspan="3"></td></tr>
                    <tr>
                        <td width="10%"></td>
                        <td align="center"><tcpdf method="write1DBarcode" params="'.$params.'"/></td>
                        <td width="10%"></td>
                    </tr>
                </table>
                ';
        $this->pdf->writeHTML($barcodeHTML, true, 0, true, 0);          
        
        //Body
        $bodyHTML = '
                    <table style="font-size:10pt;">
                        <tr>
                            <td width="10%"></td>
                            <td width="80%" align="left" style="font-size:14pt;" height="20px"><b>'.$placa.'</b></td>
                            <td width="10%"></td>
                        </tr>                    
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">PLAYA NEW CAR</td>
                            <td width="10%"></td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">TK Nº '.$parqueo.' - '.$fingreso.'   '.$hingreso.'</td>
                            <td width="10%"></td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">'.$tarifa.'       S/.'.number_format($precio,2).'</td>
                            <td width="10%"></td>
                        </tr>  
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">*****************************************</td>
                            <td width="10%"></td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td width="80%" valign="top" align="center">NO PIERDA EL TICKET</td>
                            <td width="10%"></td>
                        </tr>                        
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">*****************************************</td>
                            <td width="10%"></td>
                        </tr>   
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">'.$direccion.'</td>
                            <td width="10%"></td>
                        </tr>   
                        <tr>
                            <td width="10%"></td>
                            <td width="80%">Teléfono: '.$telefono.'</td>
                            <td width="10%"></td>
                        </tr>  
                    </table>';
        $this->pdf->writeHTML($bodyHTML,true,false,true,'');  
        

        $this->pdf->SetY(107);
        // Set font
        $this->pdf->SetFont('helvetica', 'I', 7);
        // Page number
        $this->pdf->Cell(0, 5, 'Fec. registro: '.$fregistro , 0, false, 'C', 0, '', 0, false, 'T', 'M');

        
        $this->pdf->Output('ticket.pdf', 'I');
        
    }        
    
    public function deshabilitar_parqueo(){
        $parqueo = $this->input->post("parqueo");
        
        //GetTicket
        $datosTicket = $this->parqueo_model->getParqueo($parqueo);
        $productoId  = $datosTicket[0]->PROD_Codigo;
        
        //Elimino lógicamente el producto
        $data = ["PROD_FlagEstado" => 0];
        $valor = $this->producto_model->cambiarEstado($data,$productoId);
        if($valor){
            //Anulo ticket y saco del estacionamiento
            $filter = new stdClass();
            $filter->PARQC_FlagSituacion       = "0";//Ticket anulado
            $filter->PARQC_FlagEstacionamiento = "0";//Fuera del estacionamiento
            $filter->PARQC_FechaModificacion  = date("Y-m-d H:i:s");
            $result = $this->parqueo_model->actualizar_parqueo($parqueo, $filter);
        }

        //Mostramos los resultados
        if ($result && $valor)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        echo json_encode($json);
        
    }
    
    /*public function difftime_hours($hora_inicio,$hora_fin){
        if(strlen($hora_inicio) == 4 && strlen($hora_fin) == 4){
            $hora_inicio = substr($hora_inicio,0,2).":".substr($hora_inicio,2,4);
            $hora_fin    = substr($hora_fin,0,2).":".substr($hora_fin,2,4);
            //$tol   = 10;
            $horai = new DateTime($hora_inicio);
            $horaf = new DateTime($hora_fin);
            $hor   = $horaf->diff($horai)->format("%h");
            $min   = $horaf->diff($horai)->format("%i"); 
            //if($min > $tol) $hor++;		
        }
        else{
            $hor = 0;
        }
        return $hor;
    } 
    
    public function difftime_minutes($hora_inicio,$hora_fin){
        if(strlen($hora_inicio) == 4 && strlen($hora_fin) == 4){
            $hora_inicio = substr($hora_inicio,0,2).":".substr($hora_inicio,2,4);
            $hora_fin    = substr($hora_fin,0,2).":".substr($hora_fin,2,4);
            $horai = new DateTime($hora_inicio);
            $horaf = new DateTime($hora_fin);
            $hor   = $horaf->diff($horai)->format("%h");
            $min   = $horaf->diff($horai)->format("%i"); 
            $resul = $hor*60 + $min;
        }
        else{
            $resul = 0;
        }
        return $resul;
    }*/
    
}
?>