<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');
/* *********************************************************************************
/* ******************************************************************************** */
class Movimientos{

    protected $ci;

    public function __construct(){
        date_default_timezone_set('America/Lima');
        $this->ci =& get_instance();
        $this->ci->load->model("tesoreria/movimiento_model");
        $this->ci->load->model("empresa/cliente_model");
    }

    public function guardar_movimiento($info){
        date_default_timezone_set('America/Lima');
        $fecha = date("Y-m-d");
        $fechaHora = date("Y-m-d H:i:s");
        $movimiento = $info->CAJAMOV_Codigo;
        $filter = new stdClass();
        $filter->CAJA_Codigo 		= $info->CAJA_Codigo;
        $filter->PAGP_Codigo 		= $info->PAGP_Codigo;
        $filter->RESPMOV_Codigo 	= $info->RESPMOV_Codigo;
        $filter->CUENT_Codigo 		= $info->CUENT_Codigo;
        $filter->MONED_Codigo 		= $info->MONED_Codigo;
        $filter->CAJAMOV_Monto 		= $info->CAJAMOV_Monto;
        $filter->CAJAMOV_MovDinero 	= $info->CAJAMOV_MovDinero;
        $filter->FORPAP_Codigo 		= $info->FORPAP_Codigo;
        $filter->CPP_Codigo 		= $info->CPP_Codigo;
        $filter->CAJAMOV_FechaRecep 	= (isset($info->CAJAMOV_FechaRecep) && $info->CAJAMOV_FechaRecep != NULL) ? $info->CAJAMOV_FechaRecep : $fecha;
        $filter->CAJAMOV_Justificacion	= strtoupper($info->CAJAMOV_Justificacion);
        $filter->CAJAMOV_Observacion 	= strtoupper($info->CAJAMOV_Observacion);
        $filter->CAJAMOV_FlagEstado 	= (isset($info->CAJAMOV_FlagEstado) && $info->CAJAMOV_FlagEstado != NULL) ? $info->CAJAMOV_FlagEstado : 1;
        $filter->CAJAMOV_CodigoUsuario 	= $info->CAJAMOV_CodigoUsuario;
        if ($movimiento != ""){
            $filter->CAJAMOV_Codigo = $movimiento;
            $filter->CAJAMOV_FechaModificacion = $fechaHora;
            $result = $this->ci->movimiento_model->actualizar_movimiento($movimiento, $filter);
        }
        else{
            $filter->CAJAMOV_FechaRegistro = $fechaHora;
            $result = $this->ci->movimiento_model->insertar_movimiento($filter);
        }
        return $result;
    }
    
    public function apertura_caja($caja){
        //Datos de caja
        $datos_caja  = $this->ci->caja_model->getCaja($caja);
        $cajero_id   = $datos_caja[0]->CAJA_Usuario;
        
        //Guardamos el codigo de la caja en la session
        $datos_session = $this->ci->session->userdata();    
        $datos_session['caja_activa'] = $caja;
        $datos_session['cajero_id']   = $cajero_id;
        $this->ci->session->set_userdata($datos_session);
        
        //Creamos un nuevo cierre
        $filter = new stdClass();
        $filter->CAJA_Codigo = $caja;
        $filter->CAJCIERRE_FlagSituacion = 1;
        $filter->CAJCIERRE_FechaRegistro = date("Y-m-d H:i:s");
        $filter->CAJCIERRE_Fapertura     = date("Y-m-d H:i:s");
        $result = $this->ci->cajacierre_model->insertar_cierre($filter); 
        
        //Update FlagSituación Clients -> Pendiente
        $filtercliente = new stdClass();
        $filtercliente->tipo_clienteabonado = 8;
        $clientes = $this->ci->cliente_model->getClientes($filtercliente);
        if($clientes["recordsTotal"] > 0){
            foreach($clientes["records"] as $value){
                $idcliente   =  $value->CLIP_Codigo;
                $periodofact =  $value->CLIC_MesFacturacion;//202102
                $diaingreso  = (int)date("d",strtotime($value->CLIC_FechaIngreso));
                $mesingreso  = (int)date("m",strtotime($value->CLIC_FechaIngreso));
                $diahoy      = (int)date("d",time());
                $periodoact  = (int)date("Ym",time());
                
                if($diahoy >= $diaingreso && ($periodofact < $periodoact || $periodofact == NULL)){
                    $filter = new stdClass();
                    $filter->CLIC_FlagSituracion = 0;//No facturado
                    $filter->CLIC_FechaModificacion = date("Y-m-d H:i:s");
                    $this->ci->cliente_model->actualizar_cliente($idcliente,$filter);
                }
            }
        }
        return $result;
    }
    
    public function cierre_caja($caja){
        $ingreso   = 0;
        $egreso    = 0;
        
        //Guardamos el codigo de la caja 0 en la session
        $datos_session = $this->ci->session->userdata();    
        $datos_session['caja_activa'] = 0;
        $datos_session['cajero_id']   = 0;
        $this->ci->session->set_userdata($datos_session);
        
        //Datos del último registro abierto
        $filter = new stdClass();
        $filter->caja = $caja;
        $filter->situacion = 1;
        $datosapertura = $this->ci->cajacierre_model->getCierres($filter);
        $result = false;
        
        //Calculamos todos los ingresos y egresos el tiempo que la caja estuvo abierta.
        if(count($datosapertura) > 0){
            $idcierre  = $datosapertura[0]->CAJCIERRE_Codigo;
            $fapertura = $datosapertura[0]->CAJCIERRE_Fapertura;
            
            date_default_timezone_set('America/Lima');  
            $fcierre   = date("Y-m-d H:i:s");                    
            $filtermov = new stdClass();
            $filtermov->caja = $caja;
            $filtermov->fechai = $fapertura;
            $filtermov->fechaf = $fcierre;
            $movimientos = $this->ci->movimiento_model->resumen_movimientos_total($filtermov);
            
            //Actualizamos la tabla cji_cajamovimientos con el id de la tabla cajacierre
            $this->ci->movimiento_model->actualizar_cierre($idcierre,$fapertura,$fcierre);
            
            if(count($movimientos)>0){
                foreach($movimientos as $value){
                    if($value->CAJAMOV_MovDinero == 1){//Ingreso
                        $ingreso += $value->Monto;
                    }
                    elseif($value->CAJAMOV_MovDinero == 2){//Egreso
                        $egreso += $value->Monto;
                    }
                }
            }
            $filtercierre = new stdClass();
            $filtercierre->CAJA_Codigo = $caja; 
            $filtercierre->CAJCIERRE_Fcierre = $fcierre;
            $filtercierre->CAJCIERRE_FechaModificacion = date("Y-m-d H:i:s");  
            $filtercierre->CAJCIERRE_FlagSituacion     = 0;
            $filtercierre->CAJCIERRE_Ingresos = $ingreso;
            $filtercierre->CAJCIERRE_Egresos  = $egreso;
            $filtercierre->CAJCIERRE_Saldo    = $ingreso - $egreso;
            $result = $this->ci->cajacierre_model->actualizar_cierre($idcierre,$filtercierre);  
        }
        
        return $result;
    }
    
}
