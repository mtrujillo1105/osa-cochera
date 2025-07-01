<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Lima');        
        $this->load->model('almacen/familia_model');
        $this->load->model('almacen/productoprecio_model');
        $this->load->model('maestros/documento_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('maestros/emprestablecimiento_model');
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
        $this->load->library('lib_comprobantes');	
        $this->load->library('lib_props');	
        $this->load->library('pdfgenerator');	
        $this->load->library('fpdfgenerator');	
        $this->load->library('excelspout');
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->user     = $this->session->userdata('user');
        $this->base_url = base_url();
        $this->view_js = array(0 => "seguridad/inicio.js",1 => "ventas/main.js");			
  }

    public function index($msg = NULL)
    {
        if(!isset($_SESSION['user'])) die("Sesion terminada. <a href='".  base_url()."'>Registrarse e ingresar.</a> ");  
        $filter = new stdClass();
        $filter->order = "TARIFC_Descripcion";
        $filter->dir   = "asc";
        $data["tarifas"]     = $this->tarifa_model->getTarifas($filter);
        $data["caja_activa"] = $this->session->caja_activa;
        $data["cajero_id"]   = $this->session->cajero_id;
        $data["ubicaciones"] = $this->ubicacion_model->getUbicaciones();	
        $data["establecim"]  = $this->emprestablecimiento_model->getEstablecimiento($this->compania);
        $data['scripts']     = $this->view_js;
        $this->layout->view("seguridad/inicio", $data);    
  }
  
  public function datatable_parqueo(){
    $posDT = -1;
    
    $columnas = array(
        ++$posDT => "PARQP_Codigo",        
        ++$posDT => "PARQC_Placa",
        ++$posDT => "PARQC_FechaIn",
        ++$posDT => "PARQC_HoraIn",
        ++$posDT => "PARQC_FechaRegistro",
        ++$posDT => "TARIFC_Descripcion"
    );
    
    $filter = new stdClass();
    $filter->start = $this->input->post("start");
    $filter->length = $this->input->post("length");
    $filter->search = $this->input->post("search")["value"];
    $filter->estacionamiento = 1;//Dentro de la cochera
    
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

    $tarifasInfo = $this->parqueo_model->getParqueos($filter,false);
    
    $records = array();
        if ( $tarifasInfo["records"] != NULL ) {
            foreach ($tarifasInfo["records"] as $indice => $valor) {
                $btn_modal = "<button type='button' onclick='editar($valor->PARQP_Codigo)' class='btn btn-default btn-sm'><img src='".$this->base_url."/public/images/icons/salida_vehiculo.jpg' class='image-size-1'></button>";
                
                $posDT = -1;
                $records[] = array(
                        ++$posDT => $valor->PARQP_Codigo,
                        ++$posDT => $valor->PARQC_Placa,
                        ++$posDT => $valor->PARQC_FechaIn,
                        ++$posDT => $valor->PARQC_HoraIn,
                        ++$posDT => $this->lib_comprobantes->convertir_fecha_numero($valor->PARQC_FechaRegistro),
                        ++$posDT => $valor->TARIFC_Descripcion,          
                        //++$posDT => $btn_modal
                );
            }
    	}
        $recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
        $recordsFilter = $tarifasInfo["recordsFilter"];
        $json = array(
                "draw"            => intval( $this->input->post('draw') ),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFilter,
                "data"            => $records
        );
        echo json_encode($json);
        die();
  }

    public function datatable_monitor_vehiculos(){
        $posDT = -1;
        $columnas = array(
                ++$posDT => "PARQC_Placa",
                ++$posDT => "PARQC_HoraIngreso",
                ++$posDT => "TARIFC_Descripcion",
                ++$posDT => "PARQC_FlagSituacion",
                ++$posDT => "PARQC_FlagSituacion"
              );
        $filter = new stdClass();
        $filter->start = $this->input->post("start");
        $filter->length = $this->input->post("length");
        $filter->search = $this->input->post("search")["value"];
        $ordenar        = $this->input->post("order")[0]["column"];
        if ($ordenar != ""){
                $filter->order = $columnas[$ordenar];
                $filter->dir = $this->input->post("order")[0]["dir"];
        }
        $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        
        //Sean tipo tarifa abonados o tipo tarifa tarifa plana
        $filter->tipo_tarifa = array(2,3);
        
        //No considera a los abonados dia y noche Union y Camaná
        $filter->tarifa_not  = array(41,47);
        
        //Para los vehiculos que se encuentran en el estacionamiento
        $filter->estacionamiento = 1;
        
        $tarifasInfo = $this->parqueo_model->getParqueos($filter,false);
        
        $records  = array();
        $fechahoy = date("Y-m-d H:i:s");
        if ( $tarifasInfo["records"] != NULL ) {
                foreach ($tarifasInfo["records"] as $indice => $valor) {
                    $posDT = -1;

                    //Mensaje situacion
                    $msgSituacion = "<span class='bold color-green'>CORRECTO</span>";
                    $situacion    = 1;

                    //Obtenemos la hora de inicio y fin considerando que salen el mismo día
                    $fechaingreso    = $valor->PARQC_FechaIngreso." ".$valor->PARQC_HoraIngreso;
                    $fechainiHorario = $valor->PARQC_FechaIngreso." ".$valor->TARIFC_Hinicio.":00";
                    $fechafinHorario = $valor->PARQC_FechaIngreso." ".$valor->TARIFC_Hfin.":00";

                    //Caso horarios nocturnos
                    if(strtotime($fechafinHorario) < strtotime($fechainiHorario)){
                        if(strtotime($fechaingreso) > strtotime($fechainiHorario)){
                            $fechafinHorario = date("Y-m-d H:i:s",strtotime ('+1 day',strtotime($fechafinHorario)));    
                        }
                        elseif(strtotime($fechaingreso) < strtotime($fechainiHorario)){
                            $fechainiHorario = date("Y-m-d H:i:s",strtotime ('-1 day',strtotime($fechainiHorario)));    
                        }
                        else{
                            $fechafinHorario = date("Y-m-d H:i:s",strtotime ('+1 day',strtotime($fechafinHorario)));    
                        }
                    }

                    //Validamos si está dentro del horario de la tarifa
                    if(strtotime($fechahoy) < strtotime($fechainiHorario) || strtotime($fechahoy) > strtotime($fechafinHorario)){
                        $msgSituacion = "<span class='bold color-red'>FUERA DE FECHA</span>";
                        $situacion    = 2;
                    }

                    $records[] = array(
                            ++$posDT => $valor->PARQC_Placa,
                            ++$posDT => $valor->PARQC_HoraIngreso,
                            ++$posDT => $valor->TARIFC_Descripcion,					        
                            ++$posDT => $valor->TARIFC_Hinicio." - ".$valor->TARIFC_Hfin,
                            ++$posDT => $msgSituacion,
                            ++$posDT => $situacion,
                    );
                }
        }
        $recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
        $recordsFilter = $tarifasInfo["recordsFilter"];
        $json = array(
                "draw"            => intval( $this->input->post('draw') ),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFilter,
                "data"            => $records
        );
        echo json_encode($json);
        die();
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
          ++$posDT => "CPC_TipoDocumento",
          ++$posDT => "CPC_SerieNumero",
          ++$posDT => "CAJA_Nombre"
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
      $tarifasInfo = $this->parqueo_model->getParqueos($filter,false);
      $records = array();
          if ( $tarifasInfo["records"] != NULL ) {
              foreach ($tarifasInfo["records"] as $indice => $valor) {
                  $posDT = -1;
                  $records[] = array(
                          ++$posDT => $valor->PARQP_Codigo,
                          ++$posDT => $valor->PARQC_Placa,
                          ++$posDT => $valor->PARQC_FechaIngreso,
                          ++$posDT => $valor->PARQC_HoraIngreso,          
                          ++$posDT => $valor->PARQC_FechaSalida,   
                          ++$posDT => $valor->PARQC_HoraSalida,   
                          ++$posDT => $valor->PARQC_Tiempo,   
                          ++$posDT => $valor->PARQC_Monto,
                          ++$posDT => $valor->CPC_TipoDocumento,
                          ++$posDT => $valor->CPC_SerieNumero,
                          ++$posDT => $valor->CAJA_Nombre
                  );
              }
          }
          $recordsTotal  = ( $tarifasInfo["recordsTotal"] != NULL ) ? $tarifasInfo["recordsTotal"] : 0;
          $recordsFilter = $tarifasInfo["recordsFilter"];
          $json = array(
                  "draw"            => intval( $this->input->post('draw') ),
                  "recordsTotal"    => $recordsTotal,
                  "recordsFiltered" => $recordsFilter,
                  "data"            => $records
          );
          echo json_encode($json);
          die();
    }   
    
    public function datatable_abonados(){
       $posDT = -1;

       $columnas = array(
                ++$posDT => "numero",
                ++$posDT => "razon_social",
                ++$posDT => "CLIC_FechaIngreso",
                ++$posDT => "CLIC_FlagSituracion"
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

       $filter->codigo    = $this->input->post('codigo');
       $filter->documento = $this->input->post('documento');
       $filter->nombre    = $this->input->post('nombre');
       $filter->tipo_clienteabonado = 8;

       $clienteInfo = $this->cliente_model->getClientes($filter);
       $records = array();

       if ($clienteInfo["records"] != NULL) {
           
            //Fecha de hoy
            $fechahoy   = date("Y-m-d");
            $diahoy     = (int)date("d");
            $periodohoy = (int)date("Ym",time());

            foreach ($clienteInfo["records"] as $indice => $valor) {
                
                $fingreso     = $valor->CLIC_FechaIngreso;
                $periodofact  = $valor->CLIC_MesFacturacion;//Periodo facturado: 202101
                $clisituacion = $valor->CLIC_FlagSituracion;
                
                if($clisituacion == 0){//0_Pendiente
                    $msgSituacion = "<span class='bold color-red'>PENDIENTE</span>";
                    $situacion    = 3;
                }
                elseif($clisituacion == 1){//1_Facturado
                    //Situacon: 1 al día, 2 por vencer, 3 vencido
                     //Update Fecha Ingreso
                     $arringreso  = explode("-",$fingreso);
                     $dingreso    = $arringreso[2];
                     $fingresoact =  date("Y-m-").$dingreso;

                     //Situation
                     if($periodofact == $periodohoy){
                         //Periodo facturado igual al periodo de la fecha de hoy
                         $msgSituacion = "<span class='bold color-green'>AL DÍA</span>";
                         $situacion    = 1;
                     }
                     else{
                         if(strtotime($fechahoy) >= strtotime($fingresoact)){
                             $msgSituacion = "<span class='bold color-red'>PENDIENTE</span>";
                             $situacion    = 3;
                         }
                         else{
                            if(strtotime($fingresoact) - strtotime($fechahoy) <= 604800){
                                $msgSituacion = "<span class='bold color-yellow'>POR VENCER</span>";
                                $situacion    = 2;
                            }
                            else{
                                $msgSituacion = "<span class='bold color-green'>AL DÍA</span>";  
                                $situacion    = 1;
                            }  
                         }
                     }                    
                }
                if($fingreso != ""){

                     if($situacion != 1){
                        $posDT = -1;
                        $records[] = array(
                                ++$posDT => $valor->numero,
                                ++$posDT => $valor->razon_social,
                                ++$posDT => $valor->CLIC_FechaIngreso,
                                ++$posDT => $msgSituacion
                        );   
                     }
                 
                }
                
            }
       }

       $recordsTotal = ( $clienteInfo["recordsTotal"] != NULL ) ? $clienteInfo["recordsTotal"] : 0;
       $recordsFilter = $clienteInfo["recordsFilter"];

       $json = array(
               "draw"            => intval( $this->input->post('draw') ),
               "recordsTotal"    => $recordsTotal,
               "recordsFiltered" => $recordsFilter,
               "data"            => $records
       );

       echo json_encode($json);
       die();
    }
    
   public function guardar_registro(){
        $parqueo_placa     = strtoupper($this->input->post("parqueo_placa"));
        $parqueo_tarifa    = $this->input->post("parqueo_tarifa");
        $parqueo_fingreso  = $this->input->post("parqueo_fingreso");
        $parqueo_hingreso  = $this->input->post("parqueo_hingreso");
        $parqueo_fsalida   = $this->input->post("parqueo_fsalida");
        $parqueo_ubicacion = $this->input->post("parqueo_ubicacion");
        $msj = "";
        
        //Datos de tarifa
        $datos_tarifa   = $this->tarifa_model->getTarifa($parqueo_tarifa);
        $tipo_tarifa   = $datos_tarifa[0]->TARIFC_Tipo;
        $precio_tarifa = $datos_tarifa[0]->TARIFC_Precio;      
        
        //Correlativo según compania
        $datosMax = $this->parqueo_model->getMaxParqueo();
        $numero   = $datosMax[0]->maximo + 1 ;
        
        /**Register Parqueo */
        $filter = new stdClass();
        $filter->PARQC_Placa         = $parqueo_placa;
        $filter->TARIFP_Codigo       = $parqueo_tarifa;
        $filter->PARQC_FechaIngreso  = $parqueo_fingreso;
        $filter->PARQC_HoraIngreso   = $parqueo_hingreso;
        $filter->PARQC_Numero        = $numero;
        $filter->COMPP_Codigo        = $this->compania;
        $filter->PARQC_FlagSituacion = "1";//Ticket emitido
        $filter->PARQC_FlagEstacionamiento    = "1";//Vehicula en la cochera
        $filter->PARQC_TipoMensaje   = "COCHERA";
        $filter->PARQC_FechaRegistro = date("Y-m-d H:i:s");
        $parqueo = $this->parqueo_model->registrar_parqueo($filter);
        
        if ($parqueo){
            
            //Creamos un servicio para el parqueo 
            $precio      = 0;            
            $descripcion = "SERV. ESTACIONAMIENTO PLACA ".$parqueo_placa;
            //Para los tarifa plana, les ponemos precio
            if($tipo_tarifa == 3)   $precio = $precio_tarifa;            
            $codServicio = $this->lib_comprobantes->crear_servicio($descripcion,$precio);
            
            //Actualizo el servicio en la tabla parqueo
            $filter = new stdClass();
            $filter->PROD_Codigo = $codServicio;
            $this->parqueo_model->actualizar_parqueo($parqueo, $filter);
            
            $json = array("result" => "success", "mensaje" => "Registro exitoso.", "info" => $parqueo);
        }
        else
            $json = array("result" => "error", "mensaje" => "Error al guardar el registro, intentelo nuevamente.");
        
        echo json_encode($json);
    }       

    //Se calcula el motno del ticket cuiando el vehiculo sale del estacionamiento
    public function actualizar_registro(){
        $parqueo          = $this->input->post("parqueo_edit");
        $parqueo_placa    = strtoupper($this->input->post("parqueo_placa_edit"));
        $parqueo_tarifa   = $this->input->post("parqueo_tarifa_edit");
        $parqueo_fingreso = $this->input->post("parqueo_fingreso_edit");
        $parqueo_hingreso = $this->input->post("parqueo_hingreso_edit");
        $parqueo_fsalida  = $this->input->post("parqueo_fsalida_edit");
        $parqueo_hsalida  = $this->input->post("parqueo_hsalida_edit");	
        $parqueo_codserv  = $this->input->post("parqueo_codservicio_edit");	
        
        /** Get situacion*/
        $datos_parqueo = $this->parqueo_model->getParqueo($parqueo);
        $situacion   = $datos_parqueo[0]->PARQC_FlagSituacion;
        $comprobante = $datos_parqueo[0]->CPC_SerieNumero;
        $tipotarifa  = $datos_parqueo[0]->TARIFC_Tipo;
        $tarifa      = $datos_parqueo[0]->TARIFP_Codigo;

        /** Calculate time and cost */
        $infoPrecio = $this->calcular_tiempo_costo();
        $tiempo = $infoPrecio["tiempo"];
        $precio = $infoPrecio["monto"];
        $igv    = $infoPrecio["igv"];
        $total  = $infoPrecio["total"];
        
        /** Update Parqueo */
        $filterParqueo = new stdClass();
        $filterParqueo->PARQC_Placa         = $parqueo_placa;
        $filterParqueo->TARIFP_Codigo       = $parqueo_tarifa;
        $filterParqueo->PARQC_FechaIngreso  = $parqueo_fingreso;
        $filterParqueo->PARQC_HoraIngreso   = $parqueo_hingreso;
        $filterParqueo->PARQC_FechaSalida   = $parqueo_fsalida;
        $filterParqueo->PARQC_HoraSalida    = $parqueo_hsalida;
        $filterParqueo->PARQC_FechaModificacion = date("Y-m-d H:i:s");		
        $filterParqueo->PARQC_Tiempo        = $tiempo;
        $filterParqueo->PARQC_TiempoHoraMin = $this->lib_comprobantes->convertir_min_horamin($tiempo);
        $filterParqueo->PARQC_Monto         = $precio;
        $filterParqueo->PARQC_FlagEstacionamiento = 0;//Fuera del estacionamiento
        $filterParqueo->COMPP_Codigo        = $this->compania;        
        
        //Si es cliente normal
        if($tipotarifa == 1){
            $filterParqueo->PARQC_FlagSituacion = 2;//Pasa a calculado	
        }
        //Si es abonado, se verifica si está al día
        elseif($tipotarifa == 2){
            $sitabon = 0;        
            $filterAbon = new stdClass();
            $filterAbon->placa = $parqueo_placa;
            $datos_abonado = $this->cliente_model->getClienteVehiculos($filterAbon);
            $filterParqueo->PARQC_FlagSituacion = 2;//Calculado
            if($datos_abonado['records'] != NULL){
                $sitabon   = $datos_abonado['records'][0]->CLIC_FlagSituracion;
                //Esta al día
                if($sitabon == 1){
                    $filterParqueo->PARQC_FlagSituacion = 3;//Facturado	
                }
                //El abonado no está al día, el estado será calculado
                else{
                    $filterParqueo->PARQC_FlagSituacion = 2;//Calculado	
                }
            }
        }
        //Si es tarifa plana, se verifica si se facturó
        elseif($tipotarifa == 3){
            $datos_tarifa = $this->tarifa_model->getTarifa($tarifa);
            
            //Se coloca el precio de la tarifa en el ticket
            $filterParqueo->PARQC_Monto   = $datos_tarifa[0]->TARIFC_Precio;            
            
            //Si no esta facturado
            if($situacion != 3){
                $filterParqueo->PARQC_FlagSituacion = 2;//Calculado	
            }
            else{
                $filterParqueo->PARQC_FlagSituacion = 3;//Facturado
            }
            
        }
        //Si es exonerado, se actualiza la situación a Facturado
        elseif($tipotarifa == 4){
            $filterParqueo->PARQC_FlagSituacion = 3;//Facturado	
        }
        $result = $this->parqueo_model->actualizar_parqueo($parqueo, $filterParqueo);
        
        //Si actualiza correctamente
        if ($result){

            //Si no esta facturado, se actualiza el precio del producto
            if($situacion != 3){
                /** Update service price*/
                $productoprecios = $this->productoprecio_model->getProductoPrecios($parqueo_codserv);
                $id_prodprecio   = $productoprecios[0]->PRODPREP_Codigo;            
                $filterPrecio = new stdClass();
                $filterPrecio->PRODPREC_Precio    = $precio;
                $filterPrecio->PRODPREC_FechaModificacion = date("Y-m-d H:i:s");                              
                $rs_prodprecio = $this->productoprecio_model->actualizar_producto_precio($id_prodprecio,$filterPrecio);                
            }
            
            /** Get parqueo*/
            $datos_parqueo = $this->parqueo_model->getParqueo($parqueo);
            $json = array("result" => "success", "mensaje" => "Registro exitoso.", "info" => $datos_parqueo[0]);
        }
        else
            $json = array("result" => "error", "mensaje" => "Error al calcular el costo.");
        echo json_encode($json);
        die;
    }

    public function calcular_tiempo_costo(){
        $tarifa   = $this->input->post("parqueo_tarifa_edit");
        $tarifas  = $this->tarifa_model->getTarifa($tarifa);
        $fingreso_edit = $this->input->post("parqueo_fingreso_edit");//20210107
        $fsalida_edit  = $this->input->post("parqueo_fsalida_edit");//20210107
        $fingreso = str_replace("-","",$fingreso_edit);
        $fsalida  = str_replace("-","",$fsalida_edit);
        $hingreso = str_replace(":","", $this->input->post("parqueo_hingreso_edit"));
        $hsalida  = str_replace(":","", $this->input->post("parqueo_hsalida_edit"));
        $hingreso = substr($hingreso,0,4);//1126
        $hsalida  = substr($hsalida,0,4);//1126

        //Obtenemos horario
        $horarioprecio = $tarifas[0]->TARIFC_Precio;//3
        $horarioprecio_fuera = $tarifas[0]->TARIFC_PrecioNuevo;//0
        $horariohinicio = str_replace(":","",$tarifas[0]->TARIFC_Hinicio==""?"00:00":$tarifas[0]->TARIFC_Hinicio);//0000
        $horariohfin    = str_replace(":","",$tarifas[0]->TARIFC_Hfin==""?"23:59":$tarifas[0]->TARIFC_Hfin);//2359
        $tipotarifa     = $tarifas[0]->TARIFC_Tipo;//Tipo 3: Tarifa plana, Tipo 2: Abonados, Tipo 1: Normal
        
        //Obtenemos los parametros
        $tolerancia = 10;//Tolerancia en minutos
        $tiempo_total = 0;
        $monto_total  = 0;
        
        for($i = strtotime($fingreso_edit); $i <= strtotime($fsalida_edit); $i+=86400){
            $fecha = date("Ymd", $i);
            
            /*TODO OCURRE EN UN DIA*/
            if($fingreso == $fsalida){
                
                //Hora de ingreso antes que el horario
                if($hingreso < $horariohinicio){
                    if($hsalida < $horariohinicio){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos 
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso, $hsalida);//En horas
                        if($tiempo < 60)
                            $monto = $horarioprecio_fuera;
                        else
                            $monto   = $tiempoH*$horarioprecio_fuera;
                    }
                    elseif($hsalida < $horariohfin){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio) + $this->lib_comprobantes->difftime_hours($horariohinicio,$hsalida);//En horas
                        $monto   = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio)*$horarioprecio_fuera + 
                                $this->lib_comprobantes->difftime_hours($horariohinicio,$hsalida)*$horarioprecio;
                    }
                    else{
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio) + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin) + 
                                     $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida);//En horas
                        $monto   = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio)*$horarioprecio_fuera + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin)*$horarioprecio + 
                                     $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida)*$horarioprecio_fuera;
                    }
                }
                
                //Hora de Ingreso dentro del horario
                elseif ($hingreso < $horariohfin) {
                    /*echo "hola<br>";
                    echo "H. salida: ".$hsalida."<br>";
                    echo "H. ingreso: ".$hingreso."<br>";
                    echo "H. fin: ".$horariohfin."<br>";*/
                    
                    if($hsalida < $horariohfin){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos 
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$hsalida);//En horas
                        if($tipotarifa == 3){//Tarifa plana
                            $monto = $horarioprecio;
                        }
                        else{
                            if($tiempo<60)
                                $monto = $horarioprecio;
                            else
                                $monto = $tiempoH*$horarioprecio;        
                        }
                        /*echo "Tiempo min: ".$tiempo."<br>";
                        echo "Tiempo horas: ".$tiempoH."<br>";
                        echo "Monto: ".$monto."<br>";*/                        
                    }
                    else{
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$horariohfin) + 
                                    $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida);//En horas
                        if($tipotarifa == 3){//Tarifa plana
                            $monto   = $horarioprecio + $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida)*$horarioprecio_fuera;                            
                        }
                        else{
                            $monto   = $this->lib_comprobantes->difftime_hours($hingreso,$horariohfin)*$horarioprecio + 
                                        $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida)*$horarioprecio_fuera;                            
                        }
                    }
                }
                
                //Hora de Ingreso fuera del horario
                else{
                    $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$hsalida);//En minutos
                    $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$hsalida);//En horas
                    $monto   = $this->lib_comprobantes->difftime_hours($hingreso,$hsalida)*$horarioprecio_fuera;
                }
            }
            else{
                /*PRIMER DIA*/
                if($fecha == $fingreso){
                    if($hingreso < $horariohinicio){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$horariohfin) + $this->lib_comprobantes->difftime_minutes($horariohfin,2400);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio) + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin) + 
                                     $this->lib_comprobantes->difftime_hours($horariohfin,2400);//En horas
                        $monto  = $this->lib_comprobantes->difftime_hours($hingreso,$horariohinicio)*$horarioprecio_fuera + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin)*$horarioprecio + 
                                     $this->lib_comprobantes->difftime_hours($horariohfin,2400)*$horarioprecio_fuera;
                    }
                    elseif($hingreso < $horariohfin){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($hingreso,$horariohfin) + $this->lib_comprobantes->difftime_minutes($horariohfin,2400);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($hingreso,$horariohfin) + $this->lib_comprobantes->difftime_hours($horariohfin,2400);//En horas
                        $monto   = $this->lib_comprobantes->difftime_hours($hingreso,$horariohfin)*$horarioprecio +
                                    $this->lib_comprobantes->difftime_hours($horariohfin,2400)*$horarioprecio_fuera;
                    }
                    else{
                        $tiempo  = $this->lib_comprobantes->difftime_minutes($horariohfin,2400);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours($horariohfin,2400);//En horas        
                        $monto   = $this->lib_comprobantes->difftime_hours($horariohfin,2400)*$horarioprecio_fuera;
                    }
                }
                
                /*ULTIMO DIA*/
                if($fecha == $fsalida){
                    if($hsalida < $horariohinicio){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes("0000",$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours("0000",$hsalida);//En horas  
                        $monto   = $this->lib_comprobantes->difftime_hours("0000",$hsalida)*$horarioprecio_fuera;
                    }
                    elseif($hsalida < $horariohfin){
                        $tiempo  = $this->lib_comprobantes->difftime_minutes("0000",$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio) + $this->lib_comprobantes->difftime_hours($horariohinicio,$hsalida);//En horas  
                        $monto   = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio)*$horarioprecio_fuera + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$hsalida)*$horarioprecio;
                    }
                    else{
                        $tiempo  = $this->lib_comprobantes->difftime_minutes("0000",$hsalida);//En minutos
                        $tiempoH = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio) + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin) + 
                                      $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida);//En horas
                        $monto   = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio)*$horarioprecio_fuera + 
                                    $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin)*$horarioprecio + 
                                       $this->lib_comprobantes->difftime_hours($horariohfin,$hsalida)*$horarioprecio_fuera;
                    }
                }
                
                /*PARA LOS OTROS DIAS QUE NO SEAN EL PRIMERO NI EL ULTIMO*/
                if($fecha != $fingreso && $fecha != $fsalida){
                    $tiempo  = $this->lib_comprobantes->difftime_minutes("0000","2359");//En minutos                    
                    $tiempoH = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio) + 
                                 $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin) + 
                                    $this->lib_comprobantes->difftime_hours($horariohfin,"2400");//En horas
                    $monto   = $this->lib_comprobantes->difftime_hours("0000",$horariohinicio)*$horarioprecio_fuera + 
                                 $this->lib_comprobantes->difftime_hours($horariohinicio,$horariohfin)*$horarioprecio + 
                                    $this->lib_comprobantes->difftime_hours($horariohfin,"2400")*$horarioprecio_fuera;
                }
                
            }
            $monto_total  += $monto;
            $tiempo_total += $tiempo;
        }     
        
        //Calculo del monto, tiempo total en minutos
        
        //Cantidad de horas
        $tt_horas = floor($tiempo_total/60);
        
        //Aumento 1 hora si la fracción sobrepasa la tolerancia
        if($tiempo_total%60 > $tolerancia)   $tt_horas++;
        
        //Si está menor que la tolerancia paga su hora completa
        if($tiempo_total <= $tolerancia) $tt_horas = 1;
        
        $monto_total = $tt_horas*$horarioprecio;
        if($tipotarifa == 3){//Tarifa plana
            $monto_total = $horarioprecio;
        }
        
        $info = NULL;
        if($tarifas != NULL){
                $info = array(
                        "tiempo" => $tiempo_total,
                        "monto"  => $monto_total,
                        "igv"    => 0.18*$monto_total,
                        "total"  => 1.18*$monto_total
                );
        }
        return $info;
    }
    
    public function imprimir_ticket_diarios(){
        
        /**List of parqueos*/
        $filter = new stdClass();
        $filter->estacionamiento = 1;
        $ordenar      = $this->input->post("order");
        if (!is_null($ordenar)){
            foreach($ordenar as $indice => $value){
                $arrOrd[$indice] = [$columnas[$value["column"]],$value["dir"]];
            }
            $filter->ordenar = $arrOrd;
        }
        $parqueos = $this->parqueo_model->getParqueos($filter,false);
        
        $fila1  = "";
        $fila2  = "";
        $fila3  = "";
        $nfilas = 30;//Cantidad de filas por hoja
        
        if($parqueos != NULL){
            foreach($parqueos['records'] as $indice => $value){
                
                //Calculamos el tiempo transcurrido
                $fecha_ingreso = $value->PARQC_FechaIngreso." ".$value->PARQC_HoraIngreso;
                $fecha_salida  = date("Y-m-d H:i:s");
                $tiempo        = floor((strtotime($fecha_salida) - strtotime($fecha_ingreso))/60);
                $tiempo_form   = str_replace(" con ",",",$this->lib_comprobantes->convertir_min_horamin($tiempo));
                $tiempo_form   = str_replace(".","",$tiempo_form);
                
                //Mostramos datos
                if($indice < $nfilas){
                    $fila1 .= "<tr>";
                    $fila1 .= "<td>".$value->PARQC_Numero."</td>";
                    $fila1 .= "<td>".$value->PARQC_Placa."</td>";
                    $fila1 .= "<td>".$value->TARIFC_Descripcion."</td>";
                    $fila1 .= "<td>".$value->PARQC_FechaIngreso."</td>";
                    $fila1 .= "<td>".$value->PARQC_HoraIngreso."</td>";
                    $fila1 .= "<td>".$tiempo_form."</td>";
                    $fila1 .= "</tr>";
                }
                else{
                    $fila2 .= "<tr>";
                    $fila2 .= "<td>".$value->PARQC_Numero."</td>";
                    $fila2 .= "<td>".$value->PARQC_Placa."</td>";
                    $fila2 .= "<td>".$value->TARIFC_Descripcion."</td>";
                    $fila2 .= "<td>".$value->PARQC_FechaIngreso."</td>";
                    $fila2 .= "<td>".$value->PARQC_HoraIngreso."</td>";
                    $fila2 .= "<td>".$tiempo_form."</td>";
                    $fila2 .= "</tr>";
                }
                
            }
        }
        
        //Creamos el reporte
        //Head report
        $medidas = array(80, 200); // 
        $this->pdf = new tcpdf('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(3, 3, 3);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->AddPage();
        $cabeceraHTML = '<table align="center">
                    <tr>
                        <td align="left">PLAYA NEW CAR</td>
                    </tr>              
                    <tr>
                        <td></td>
                    </tr>            
                    <tr>
                        <td><b>TICKETS DIARIOS</b></td>
                    </tr>
                    </table>';
        $this->pdf->writeHTML($cabeceraHTML,true,false,true,'');      
            
        //Body del documento
        //Imprimo hoja 1
        $bodyHTML1 = '
                    <table border=1>
                    <tr>
                        <td width="10%"><b>No</b></td>
                        <td width="15%"><b>PLACA</b></td>
                        <td width="25%"><b>TARIFA</b></td>
                        <td width="20%"><b>F.INGRESO</b></td>
                        <td width="15%"><b>H.INGR</b></td>
                        <td width="15%"><b>TIEMPO</b></td>
                    </tr>
                    '.$fila1.'
                    </table>';
        $this->pdf->writeHTML($bodyHTML1,true,false,true,''); 
        
        //Imprimo hoja 2
        if($fila2 != ""){
            
            //Agrego una pagina
            $this->pdf->AddPage();
            
            //Imprimo la otra hoja
            $bodyHTML2 = '
                        <table border=1>
                        <tr>
                            <td width="10%"><b>No</b></td>
                            <td width="15%"><b>PLACA</b></td>
                            <td width="25%"><b>TARIFA</b></td>
                            <td width="20%"><b>F.INGRESO</b></td>
                            <td width="15%"><b>H.INGR</b></td>
                            <td width="15%"><b>TIEMPO</b></td>
                        </tr>
                        '.$fila2.'
                        </table>';
            $this->pdf->writeHTML($bodyHTML2,true,false,true,'');   
            
        }
        
        //Footer
        $footerHTML = '<table cellspacing="1px" style="font-size:7pt; text-align:center;">
                        <tr>
                        <td>Fecha de Impresión: '.date("d/m/Y").'</td>
                        </tr>
                        </table>';
        
        $this->pdf->writeHTML($footerHTML,false,false,true,'');        
        
        $this->pdf->Output('ticketsdiarios.pdf', 'I');
        
    }        
    
}
?>