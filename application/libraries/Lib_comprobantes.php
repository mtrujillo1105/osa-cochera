<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');
/* *********************************************************************************
Autor: Martín Trujillo
Dev: 
/* ******************************************************************************** */

class Lib_comprobantes {
    
    protected $ci;
    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model("almacen/producto_model");
        $this->ci->load->model("almacen/productoprecio_model");
        $this->ci->load->model("almacen/familia_model");
        $this->ci->load->model("maestros/documento_model");
        $this->ci->load->model("maestros/configuracion_model");
        $this->ci->load->model("maestros/almacen_model");
        $this->ci->load->model("ventas/comprobante_model");
        $this->ci->load->model("ventas/parqueo_model");
        $this->ci->load->model("tesoreria/caja_model");
    }
    
    public function crear_servicio($descripcion,$precio = 0){
         //Type of Service - Servicio de cochera
         $fa      = 77;
         $fami    = $this->ci->familia_model->getFamilia($fa);
         $familia = $fami->FAMI_Descripcion; 
         
         //Product code
         $longitud = 13;
         $codigo = substr(formatString($familia), 0, 3) . substr(formatString(''), 0, 3);
         $longitud -= strlen($codigo);
         $codigo .= getNumberFormat($this->ci->producto_model->getLastId('S') + 1, $longitud);
         
         $result = false;
         
         //Save service
         $filter = new stdClass();
         $filter->PROD_FlagBienServicio   = 'S';
         $filter->PROD_Nombre             = trim($descripcion);
         $filter->PROD_NombreCorto        = trim($descripcion);
         $filter->PROD_CodigoInterno      = $codigo;
         $filter->PROD_CodigoUsuario      = $codigo;
         $filter->PROD_GenericoIndividual = "G";
         $filter->PROD_FlagActivo         = "1";
         $filter->PROD_FlagEstado         = "1";        
         $filter->AFECT_Codigo            = 1;
         $filter->FAMI_Codigo             = $fa;
         $filter->TIPPROD_Codigo          = NULL;
         $filter->PROD_Comentario         = NULL;
         $filter->PROD_Imagen             = NULL;
         $filter->PROD_EspecificacionPDF  = NULL;
         $filter->LINP_Codigo             = NULL;
         $filter->PROD_Presentacion       = "";
         $filter->PROD_PadreCodigo        = NULL;
         $filter->PROD_PartidaArancelaria = NULL;     
         $codserv =  $this->ci->producto_model->insertarNvoRegistroCiaUnica($filter);
          
         /** Save service price*/
         $filter = new stdClass();
         $filter->PROD_Codigo     = $codserv;
         $filter->TIPCLIP_Codigo  = 9;
         $filter->EESTABP_Codigo  = $this->ci->session->userdata('compania');
         $filter->MONED_Codigo    = 1;
         $filter->PRODUNIP_Codigo = 34;
         $filter->PRODPREC_PorcGanancia = NULL;
         $filter->PRODPREC_Precio        = $precio;
         $filter->PRODPREC_FechaRegistro = date("Y-m-d H:i:s");
         $filter->PRODPREC_FlagEstado    = "1";     
         $rs_precio_prod = $this->ci->productoprecio_model->insertarNvoRegistro($filter);         
         
         if($rs_precio_prod!=0 && $codserv!=0){
             $result = $codserv;
         }
         
         return $result;
     }
    
     
     public function create_comprobante($info,$imprimetiempo = false){
        /*Get data necessary*/ 
        $tipo_docu   = $info->tipo_docu;
        $codservicio = $info->codservicio;         
        $cliente     = $info->cliente;  
        $fpago       = isset($info->fpago)?$info->fpago:29;//Forma de pago
        $establec    = $this->ci->session->userdata('compania');
        
        $filter      = new stdClass();
        $filter->establecimiento = $establec;
        $filter->situacion = 1;
        $almacenes   = $this->ci->almacen_model->getAlmacens($filter);
        $almacen     = $almacenes[0]->ALMAP_Codigo;

        //Obtengo datos de la session
        $caja        = $this->ci->session->userdata('caja_activa');
        $cajero_id   = $this->ci->session->userdata('cajero_id');

        /*Get Service*/
        $servicios   = $this->ci->producto_model->getProducto($codservicio);
        $nomservicio = $servicios[0]->PROD_Nombre;
        $precios     = $this->ci->productoprecio_model->getProductoPrecios($codservicio);
        $precio      = $precios[0]->PRODPREC_Precio;
        $p_igv       = 18;
        $cantidad    = 1;
        $subtotal    = round($precio*100/(100+$p_igv),2);
        $igv         = round($subtotal*$p_igv/100);
        $total       = $precio;   
        
        /*Get time of Parqueo*/
        $t_horamin = "";
        if($imprimetiempo){
            $filter = new stdClass();
            $filter->servicio = $codservicio;
            $parqueos  = $this->ci->parqueo_model->getParqueos($filter);
            $t_horamin = $parqueos[0]->PARQC_TiempoHoraMin;
            $t_hingreso = $parqueos[0]->PARQC_HoraIngreso;
            $t_hsalida  = $parqueos[0]->PARQC_HoraSalida;
            $id_parqueo = $parqueos[0]->PARQP_Codigo;
        }
        
        /*Get Number Invoice*/
        $documento = $this->ci->documento_model->obtenerDocuInicial(trim($tipo_docu));
        $tipo      = $documento[0]->DOCUP_Codigo;            
        $configuracion_datos = $this->ci->configuracion_model->obtener_numero_documento($this->ci->session->userdata('compania'), $tipo);
        $numero    = $configuracion_datos[0]->CONFIC_Numero + 1;
        $serie     = $configuracion_datos[0]->CONFIC_Serie;
        
        /*Insert Invoice*/
        $filter = new stdClass();
        $filter->CPC_TipoOperacion    = 'V';
        $filter->CPC_TipoDocumento    = $tipo_docu;
        $filter->ALMAP_Codigo         = $almacen;
        $filter->CLIP_Codigo          = $cliente;
        $filter->CPC_NumeroAutomatico = 1;
        $filter->CPC_Numero           = $numero;
        $filter->CPC_Fecha            = date('Y-m-d');
        $filter->CPC_FechaVencimiento = date('Y-m-d');
        $filter->CPC_Hora             = date('h:i:s');
        $filter->CPC_Serie            = $serie;
        $filter->MONED_Codigo         = 1;
        $filter->CPC_FlagEstado       = 2;
        $filter->CPC_ModoImpresion    = 1;
        $filter->CPC_TDC              = 3;
        $filter->CAJA_Codigo          = $caja;
        $filter->CPC_DocuRefeCodigo   = '';
        $filter->CPC_subtotal         = $subtotal;
        $filter->CPC_descuento        = 0;
        $filter->CPC_igv              = $igv;
        $filter->CPC_total            = $total;
        $filter->CPC_igv100           = $p_igv;
        $filter->FORPAP_Codigo        = $fpago;//Forma de pago
        $filter->CPC_Vendedor         = $cajero_id;//Cajero
        $comprobante = $this->ci->comprobante_model->insertar_comprobante($filter);
        
        /*Isert details Invoice*/
        $filterDet = new stdClass();
        $filterDet->CPP_Codigo      = $comprobante;
        $filterDet->PROD_Codigo     = $codservicio;
        $filterDet->LOTP_Codigo     = '';
        $filterDet->AFECT_Codigo    = 1;
        $filterDet->CPDEC_Cantidad  = 1;
        $filterDet->CPDEC_Pendiente = 1;
        $filterDet->CPDEC_Pu        = $subtotal;//antes precio
        $filterDet->CPDEC_Subtotal  = $subtotal;
        $filterDet->CPDEC_Pu_ConIgv = $precio;
        $filterDet->CPDEC_Subtotal_ConIgv  = $precio;//antes subtotal
        $filterDet->CPDEC_Descuento = 0;
        $filterDet->CPDEC_Igv       = $igv;
        $filterDet->CPDEC_Total     = $total;
        $filterDet->CPDEC_Igv100    = 1;
        $filterDet->ALMAP_Codigo    = 16;
        $filterDet->CPDEC_Igv100    = $p_igv;//18%
        $filterDet->CPDEC_Costo     = 0;
        $filterDet->CPDEC_Descripcion = $nomservicio;
        if($imprimetiempo)
            $filterDet->CPDEC_Observacion = $t_horamin.", Hora de ingreso:".$t_hingreso.", Hora de salida: ".$t_hsalida;
        else
            $filterDet->CPDEC_Observacion = "";
        $comprobanteDet = $this->ci->comprobantedetalle_model->insertar($filterDet);
        
        if($imprimetiempo){
            //Update idcomprobante in table Parqueo
            $filter = new stdClass();
            $filter->CPP_Codigo = $comprobante;
            $filter->PARQC_FlagSituacion = 3;
            $this->ci->parqueo_model->actualizar_parqueo($id_parqueo,$filter);
            
            //Update flagSituacion in table Product
            $filter = new stdClass();
            $filter->PROD_FlagSituacion = 2;
            $this->ci->producto_model->modificarRegistro($codservicio,$filter);
        }
        
        $result = false;
        if($comprobanteDet!=NULL && $comprobante!=NULL )
            $result = $comprobante;

        return $result;
        
     }
     
     public function convertir_min_horamin($minutos){
        $horas   = floor($minutos/60);
        $minutos = $minutos - $horas*60;
        return $horas." hr. con ".$minutos." min.";
    }
    
    public function convertir_fecha_numero($fecha,$hora="00:00:00"){
        if(strlen($fecha)>10){
            $arrFecha = explode(" ",$fecha);
            $fechanum = str_replace("-","", $arrFecha[0]).str_replace(":","",$arrFecha[1]);
        }
        else{
            $hora     = strlen($hora) > 5 ? $hora : $hora.":00";
            $fechanum = str_replace("-","", $fecha).str_replace(":","",$hora);
        }
        return $fechanum;
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
    }
    
    public function difftime_hours($hora_inicio,$hora_fin){
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
    
}
