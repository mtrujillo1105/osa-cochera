<?php
 class RVentas extends CI_Controller{
    public function __construct(){
		parent::__construct();
        $this->load->model('tesoreria/pago_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('maestros/compania_model');
        //producto busqueda:
        $this->load->model('almacen/producto_model');
        
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('reportes/rptventas_model');
        $this->load->model('reportes/rptventas_model');
        
        $this->somevar['compania']  = $this->session->userdata('compania');
        $this->somevar['empresa']   = $this->session->userdata('empresa');
        $this->somevar['user']      = $this->session->userdata('user');
        $this->somevar['rol']       = $this->session->userdata('rol');
    }
    public function index(){
        
    }
    
    public function reporte_ventas(){
        $this->load->library('layout','layout');
        $data['f_ini'] = '01/01/2013';
        $data['f_fin'] = '31/01/2013';
        $data['producto'] = '';
        $data['codproducto'] = '';
        $data['nombre_producto'] = '';
        $data['TODOS'] = false;
                
        //if(isset($_POST['reporte'])){
        if(isset($_POST['fecha_inicio']))
            $data['fecha_inicio'] = $_POST['fecha_inicio'];
        if(isset($_POST['fecha_fin']))
            $data['fecha_fin'] = $_POST['fecha_fin'];
        if(isset($_POST['producto']))
            $data['producto'] = $_POST['producto'];
        if(isset($_POST['codproducto']))
            $data['codproducto'] = $_POST['codproducto'];
        if(isset($_POST['nombre_producto']))
            $data['nombre_producto'] = $_POST['nombre_producto'];
        if(isset($_POST['TODOS']))            
            $data['TODOS'] = $_POST['producto']=='1' ? true : false;
        
        $lista_companias = $this->compania_model->listar_establecimiento($this->somevar['empresa']);
        foreach($lista_companias as $key=>$compania){
            if(count($_POST)>0){
                if($this->input->post('COMPANIA_'.$compania->COMPP_Codigo)=='1'){
                    $comp_select[]=$compania->COMPP_Codigo;
                    $lista_companias[$key]->checked=true;
                }
                else
                    $lista_companias[$key]->checked=false;
            }else{
                $comp_select[]=$compania->COMPP_Codigo;
                $lista_companias[$key]->checked=true;
            }
        }
        $data['lista_companias'] = $lista_companias;
        
        $data['resumen'] = $this->rptventas_model->ventas_por_vendedor_resumen($data['f_ini'],$data['f_fin']);
        //$data['mensual'] = $this->rptventas_model->ventas_por_vendedor_mensual($data['fecha_inicio'],$data['fecha_fin']);
        //$data['anual'] = $this->rptventas_model->ventas_por_vendedor_anual($data['fecha_inicio'],$data['fecha_fin']);
        //}
        //$this->layout->view('reportes/ventas_por_vendedor',$data);
        $this->layout->view('reportes/rpt_ventas',$data);
        
        /*
        //para producto busqueda: Inicio
        $this->session->unset_userdata('producto');
        $this->session->unset_userdata('codproducto');
        $this->session->unset_userdata('nombre_producto');        
        $filter = new stdClass();
        if(count($_POST)>0){
            $filter->producto      = $this->input->post('producto');
            $filter->codproducto      = $this->input->post('codproducto');
            $filter->nombre_producto  = $this->input->post('nombre_producto');
            $this->session->set_userdata(array('codproducto'=>$filter->codproducto, 'nombre_producto'=>$filter->nombre_producto));
        }
        else{
            $filter->producto       = $this->session->userdata('producto');
            $filter->codproducto    = $this->session->userdata('codproducto');
            $filter->nombre_producto= $this->session->userdata('nombre_producto');
        }
        //Fin producto
        
        
        $lista = '';
        $total_soles = '';
        $total_dolares = '';
        $resumen_suma = '';
        $resumen_suma_d = '';
        $resumen_cantidad = '';
        $resumen_fpago = '';
        
        //variables
        $producto_busca = $this->input->post('producto')!='' ?  $this->input->post('producto') : '';
        
        $f_ini = $this->input->post('fecha_inicio')!='' ? $this->input->post('fecha_inicio') : '01/'.date('m/Y');
        $f_fin = $this->input->post('fecha_fin')!='' ?  $this->input->post('fecha_fin') : date('d/m/Y');
        
        $comp_select=array();
        $lista_companias = $this->compania_model->listar_establecimiento($this->somevar['empresa']);
        foreach($lista_companias as $key=>$compania){
            if(count($_POST)>0){
                if($this->input->post('COMPANIA_'.$compania->COMPP_Codigo)=='1'){
                    $comp_select[]=$compania->COMPP_Codigo;
                    $lista_companias[$key]->checked=true;
                }
                else
                    $lista_companias[$key]->checked=false;
            }else{
                $comp_select[]=$compania->COMPP_Codigo;
                $lista_companias[$key]->checked=true;
            }
        }
        $prod_nombre = "";
        if($producto_busca!=""){
            $prod = $this->producto_model->obtener_producto($producto_busca);
            $prod_nombre = "*** ".$prod[0]->PROD_Nombre." ***";
        }
        
        //valorizacion bruta
        $lista_existencia = $this->almacenproducto_model->buscar_x_fechas(human_to_mysql($f_ini),human_to_mysql($f_fin),$producto_busca,$comp_select);
        
        //pagos
        $lista_cuentaspago = $this->cuentaspago_model->buscar_x_fechas(human_to_mysql($f_ini),human_to_mysql($f_fin), '1', $comp_select);
        $lista 	= array();
        $total_soles 	= 0;
        $total_dolares 	= 0;
        $formapago_soles = array(0,0,0,0,0,0);
        $formapago_dolares = array(0,0,0,0,0,0);
        $cantidad = array(0,0,0,0,0,0);
        $total_soles=0; 
        $monto_dolares=0;
        
        $existencia_dolares = array();
        $x=0;
        foreach($lista_companias as $value){
            $existencia_dolares[$x][0] = $value->COMPP_Codigo;
            $existencia_dolares[$x][1] = $value->EESTABC_Descripcion;
            $existencia_dolares[$x][2] = 0;
            $x++;
        }
        foreach($existencia_dolares as $indice=>$value){
            $aux_comp = $existencia_dolares[$indice][0];
            //$existencia_dolares[$indice][2]= count($lista_existencia);
            foreach($lista_existencia as $indice2=>$value2){
                if($aux_comp == $lista_existencia[$indice2]->COMPP_Codigo){
                    $totalcp = number_format($lista_existencia[$indice2]->ALMPROD_Stock)*number_format($lista_existencia[$indice2]->ALMPROD_CostoPromedio);
                    $existencia_dolares[$indice][2] = $existencia_dolares[$indice][2] + 10;// number_format($totalcp);
                }
            }
        }
        
        
        foreach($lista_cuentaspago as $value){
            $fecha_cuenta=mysql_to_human($value->CUE_FechaOper);
            $moneda_cuenta=$value->MONED_Simbolo2;
            $monto_cuenta=$value->CUE_Monto;
            
            $fecha=mysql_to_human($value->PAGC_FechaOper);
            $forma_pago=$this->pago_model->obtener_forma_pago($value->PAGC_FormaPago);
                 
            $temp = $this->obtener_nombre_numdoc('CLIENTE', $value->CLIP_Codigo );
            $tipo_persona = $temp['tipo_persona']==2 ? 'NATURAL' : 'JURIDICO';
            $numdoc = $temp['numdoc'];
            $nombre = $temp['nombre'];
            $moneda = $value->MONED_Simbolo;
            $tdc = number_format($value->PAGC_TDC,2);
            
            $monto_soles = 0;
            $monto_dolares = 0;
            if($value->MONED_Codigo==1){
                $monto_soles = $value->CPAGC_Monto;
                $formapago_soles[$value->PAGC_FormaPago-1]+=$monto_soles;
                $total_soles+=$monto_soles;
            }else{
                $monto_dolares = $value->CPAGC_Monto;
                $formapago_dolares[$value->PAGC_FormaPago-1]+=$monto_dolares;
                $total_dolares+=$monto_dolares;
            }
            
            $cantidad[$value->PAGC_FormaPago-1]++;
            
            $resumen_compania_sol[$value->COMPP_Codigo]=(isset($resumen_compania_sol[$value->COMPP_Codigo]) ? $resumen_compania_sol[$value->COMPP_Codigo] : 0) + $monto_soles;
            $resumen_compania_dol[$value->COMPP_Codigo]=(isset($resumen_compania_dol[$value->COMPP_Codigo]) ? $resumen_compania_dol[$value->COMPP_Codigo] : 0) + $monto_dolares;
            $lista[]=array($fecha,$forma_pago,$fecha_cuenta,$moneda_cuenta,number_format($monto_cuenta,2),$tipo_persona,$numdoc,$nombre,$moneda,$tdc,($monto_soles!=0 ? number_format($monto_soles,2) : ''),($monto_dolares!=0 ? number_format($monto_dolares,2) : ''));
        }

        $lista_resumen = array();
        $lista_resumen[0]=array('EFECTIVO', $formapago_soles[0], $formapago_dolares[0], $cantidad[0]);
        $lista_resumen[1]=array('DEPOSITO', $formapago_soles[1], $formapago_dolares[1], $cantidad[1]);
        $lista_resumen[2]=array('CHEQUE', $formapago_soles[2], $formapago_dolares[2], $cantidad[2]);
        $lista_resumen[3]=array('CANJE POR FACTURA', $formapago_soles[3], $formapago_dolares[3], $cantidad[3]);
        $lista_resumen[4]=array('NOTA DE CREDITO', $formapago_soles[4], $formapago_dolares[4], $cantidad[4]);
        $lista_resumen[5]=array('DESCUENTO', $formapago_soles[5], $formapago_dolares[5], $cantidad[5]);
        
        $total_soles_res=0; $total_dolares_res=0; $total_cantidad=0;
        for($i=0; $i<=5; $i++){
            $lista_resumen[$i]=array($this->pago_model->obtener_forma_pago($i+1), $formapago_soles[$i]>0 ? number_format($formapago_soles[$i],2) : 0, $formapago_dolares[$i]>0 ? number_format($formapago_dolares[$i],2) : 0, $cantidad[$i]);
            $total_soles_res+=$formapago_soles[$i];
            $total_dolares_res+=$formapago_dolares[$i];
            $total_cantidad+=$cantidad[$i];  
        }
        $total_compani_sol=0; $total_compani_dol=0;
        foreach($lista_companias as $compania){
            if(isset($resumen_compania_sol[$compania->COMPP_Codigo])){
                $total_compani_sol+=$resumen_compania_sol[$compania->COMPP_Codigo];
                $resumen_compania_sol[$compania->COMPP_Codigo]=$resumen_compania_sol[$compania->COMPP_Codigo]>0 ? number_format($resumen_compania_sol[$compania->COMPP_Codigo],2) :0;
            }else
                $resumen_compania_sol[$compania->COMPP_Codigo]=0;
            if(isset($resumen_compania_dol[$compania->COMPP_Codigo])){
                $total_compani_dol+=$resumen_compania_dol[$compania->COMPP_Codigo];
                $resumen_compania_dol[$compania->COMPP_Codigo]=$resumen_compania_dol[$compania->COMPP_Codigo]>0 ? number_format($resumen_compania_dol[$compania->COMPP_Codigo],2) : 0;
            }else
                $resumen_compania_dol[$compania->COMPP_Codigo]=0;
        }
        //producto busqueda:
        $data['producto']          = $filter->producto;
        $data['codproducto']       = $filter->codproducto;
        $data['nombre_producto']   = $filter->nombre_producto;
        
        $data['f_ini']  = $f_ini;
        $data['f_fin']  = $f_fin;
        $data['prod_nombre'] = $prod_nombre;
        $data['TODOS'] = $this->input->post('TODOS')=='1' ? true : false;
        $data['lista_companias'] = $lista_companias;
        $data['existencia_dolares'] = $existencia_dolares;
        $data['lista']  = $lista;
        $data['lista_resumen'] = $lista_resumen;
        $data['total_soles']=number_format($total_soles,2);
        $data['total_dolares']=number_format($total_dolares,2);
        $data['total_soles_res'] = number_format($total_soles_res,2);
        $data['total_dolares_res'] = number_format($total_dolares_res,2);
        $data['total_cantidad'] = $total_cantidad;
        $data['resumen_compania_sol'] = $resumen_compania_sol;
        $data['resumen_compania_dol'] = $resumen_compania_dol;
        $data['total_compani_sol'] = number_format($total_compani_sol,2);
        $data['total_compani_dol'] = number_format($total_compani_dol,2);
        $this->layout->view('reportes/rpt_ventas',$data);
        */
    }
    
    function obtener_nombre_numdoc($tipo, $codigo){
        $nombre='';
        $numdoc='';
        $tipo_persona='';
        if($tipo=='CLIENTE'){
            $datos_cliente   = $this->cliente_model->obtener($codigo);
            if($datos_cliente){
                $nombre  = $datos_cliente->nombre;                        
                $numdoc  = $datos_cliente->ruc;
                $tipo_persona    = $datos_cliente->tipo;
            }
        }else{
            $datos_proveedor   = $this->proveedor_model->obtener($codigo);
            if($datos_proveedor){
                $nombre  = $datos_proveedor->nombre;
                $numdoc  = $datos_proveedor->ruc;
                $tipo_persona    = $datos_cliente->tipo;
            }
        }  
         return array('numdoc'=>$numdoc, 'nombre'=>$nombre, 'tipo_persona'=>$tipo_persona);
    }
    
    public function obtener_nombre_producto(){
        $flagBS       = $this->input->post('flagBS');            
        $codigo_interno = $this->input->post('interno');
        
        $datos_producto = $this->producto_model->obtener_producto_x_codigo($flagBS, $codigo_interno);
        if(count($datos_producto)>0){
            $descripcion = addslashes($datos_producto[0]->PROD_Nombre);
        }
        exit('{"result":"ok", "desc":"'.$descripcion.'"}');
        /*
        $datos_producto = $this->producto_model->obtener_producto_x_codigo($flagBS, $codigo_interno);
        $listado_array  = '[{"PROD_Codigo":"0","PROD_Nombre":"","PROD_Stock":"","UNDMED_Simbolo":""}]';
        if(count($datos_producto)>0){
            $producto = $datos_producto[0]->PROD_Codigo;
            $familia = $datos_producto[0]->FAMI_Codigo;
            $tipo_producto = $datos_producto[0]->TIPPROD_Codigo;
            $descripcion = addslashes($datos_producto[0]->PROD_Nombre);
            $stock = $datos_producto[0]->PROD_Stock;
            $flagGenInd = $datos_producto[0]->PROD_GenericoIndividual;
            //$datos_familia = $this->familia_model->obtener_familia($familia);
            //$nombre_familia = addslashes($datos_familia[0]->FAMI_Descripcion);
            $listado_array = '[{"PROD_Codigo":"'.$producto.'","PROD_Nombre":"'.$descripcion.'","PROD_Stock":"'.$stock.'", "flagGenInd":"'.$flagGenInd.'"}]';
        } 
        $resultado = json_encode($listado_array);
        echo $resultado;
         * 
        $com = "OTRO";
        exit('{"result":"ok", "codigo":"'.$com.'"}');
        */
    }
    
 }
?>