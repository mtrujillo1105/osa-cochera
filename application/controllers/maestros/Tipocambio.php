<?php
class Tipocambio extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/moneda_model');
        $this->compania = $this->session->userdata('compania');
    }
    public function index(){
            $this->load->library('layout','layout'); 
            $this->layout->view('seguridad/inicio');	
    }
    public function listar($j='0')
    {
        
        $data['registros']  = count($this->tipocambio_model->listar());
        $conf['base_url']   = site_url('maestros/tipocambio/listar/');
        $conf['total_rows'] = $data['registros'];
        $conf['per_page']   = 10;
        $conf['num_links']  = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link']  = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $offset             = (int)$this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $listado            = $this->tipocambio_model->listar('', $conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
        $listado_moneda =$this->moneda_model->listar();
        if(count($listado)>0){
            
            foreach($listado as $indice=>$valor)
            {   $codigo = $valor->TIPCAMP_Codigo;
                $fecha = $valor->TIPCAMC_Fecha;
                
                $valores_tipocam=array();
                foreach($listado_moneda as $reg){
                    if($reg->MONED_Codigo!=1){
                        $filter=new stdClass();
                        $filter->TIPCAMC_MonedaDestino=$reg->MONED_Codigo;
                        $filter->TIPCAMC_Fecha=$fecha;
                        $temp=$this->tipocambio_model->buscar($filter);
                        //var_dump($filter);
                        //var_dump($temp);
                        if(count($temp)>0)
                            $valores_tipocam[]=$temp[0]->TIPCAMC_FactorConversion;                        
                        else
                            $valores_tipocam[]='';
                    }
                }
                $ver= "<a href='#' onclick='ver_tipocambio(".str_replace('-', '',$fecha).")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Ver'></a>";

                $modificar = "<a href='#' onclick='modificar_tipocambio(".str_replace('-', '',$fecha).")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[]= array($item++,$fecha,$valores_tipocam,$ver,$modificar);
            }
        }
        //var_dump($lista);
        $data['listado_moneda']   = $listado_moneda;
        $data['lista']           = $lista;
        $data['titulo_busqueda'] = "BUSCAR TIPO DE CAMBIO";
        $data['fecha']  	 = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>""));
        $data['form_open']       = form_open(base_url().'index.php/maestros/tipocambio/buscar',array("name"=>"form_busquedaTipoCambio","id"=>"form_busquedaTipoCambio"));
        $data['form_close']      = form_close();
        $data['titulo_tabla']    = "Relaci&oacute;n DE TIPO DE CAMBIOS";
        $data['oculto']          = form_hidden(array('base_url'=>base_url()));	
        $this->layout->view('maestros/tipocambio_index',$data);
			
    }
    public function nuevo($ventana=false)
    {
        
        $data['lista_monedas']=$this->moneda_model->listar();

        $data['titulo']     = "REGISTRAR TIPO DE CAMBIO DEL DIA : ".date('d/m/Y');
        $data['form_open']  = form_open(base_url().'index.php/maestros/tipocambio/grabar',array("name"=>"frmTipoCambio","id"=>"frmTipoCambio"));
        $data['form_close'] = form_close();
        $data['oculto']     = form_hidden(array('base_url'=>base_url()));
        if($ventana==false)
            $this->layout->view('maestros/tipocambio_nuevo',$data);
        else
            $this->load->view('maestros/tipocambio_ventana_configura',$data);
        
    }

	public function editar($fecha){
		
        
        if(strlen($fecha)!=8)
            show_error('La fecha enviada es incorrecta.');

        $fecha=substr($fecha,0,4).'-'.substr($fecha,4,2).'-'.substr($fecha,6,2);
    
        $lista_monedas=$this->moneda_model->listar();
        $data['lista_monedas']=$lista_monedas;
        $valores=array();

        foreach($lista_monedas as $reg){
            if($reg->MONED_Codigo!=1){
                $filter=new stdClass();
                $filter->TIPCAMC_Fecha=$fecha;
                $filter->TIPCAMC_MonedaDestino =$reg->MONED_Codigo;
                $temp=$this->tipocambio_model->buscar($filter);
                if(count($temp)>0)
                    $valores[$reg->MONED_Codigo]=$temp[0]->TIPCAMC_FactorConversion;
                else
                    $valores[$reg->MONED_Codigo]='';
            }
        }
		
        $data['valores']=$valores;
        $data['fecha']=$fecha;
        $data['titulo']= "MODIFICAR TIPO DE CAMBIO DEL DIA : ".substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4);
        $data['oculto']=form_hidden(array('base_url'=>base_url()));	
        $this->layout->view("maestros/tipocambio_nuevo", $data);
		
    }

    public function grabar(){
        $diasfaltantes = $this->input->post("dfalt")[1];
        $fecha = date('Y-m-d');
        $tipocambios  = $this->input->post("tipocambio")[1];
    
        $filter = new stdClass();
        $filter->TIPCAMC_MonedaOrigen  = 1;
        $filter->TIPCAMC_MonedaDestino = 2;
        $filter->TIPCAMC_Fecha = $fecha;         
        $filter->TIPCAMC_FactorConversion = $tipocambios;
        $filter->COMPP_Codigo = NULL;
        $this->tipocambio_model->insertar($filter);
    }

    public function eliminar(){
        $id = $this->input->post('almacen');
        $this->almacen_model->eliminar($id);
    }

    public function ver($fecha){   
	   
        
        if(strlen($fecha)!=8)
            show_error('La fecha enviada es incorrecta.');

        $fecha=substr($fecha,0,4).'-'.substr($fecha,4,2).'-'.substr($fecha,6,2);
    
        $lista_monedas=$this->moneda_model->listar();
        $data['lista_monedas']=$lista_monedas;
        $valores=array();

        foreach($lista_monedas as $reg){
            if($reg->MONED_Codigo!=1){
                $filter=new stdClass();
                $filter->TIPCAMC_Fecha=$fecha;
                $filter->TIPCAMC_MonedaDestino =$reg->MONED_Codigo;
                $temp=$this->tipocambio_model->buscar($filter);
                if(count($temp)>0)
                    $valores[$reg->MONED_Codigo]=$temp[0]->TIPCAMC_FactorConversion;
                else
                    $valores[$reg->MONED_Codigo]='';
            }
        }
        $data['valores']=$valores;
        $data['titulo']= "VER TIPO DE CAMBIO DEL DIA : ".substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4);
        $data['oculto']=form_hidden(array('base_url'=>base_url()));	
        $this->layout->view("maestros/tipocambio_ver", $data);
    }

    public function buscar($j=0){
        $this->load->library('layout','layout');
        $fecha                 = $this->input->post('fecha');
        $data['registros']      = count($this->tipocambio_model->listar($fecha));
        $conf['base_url']       = site_url('almacen/almacen/buscar/');
        $conf['per_page']       = 10;
        $conf['num_links']      = 3;
        $conf['first_link']     = "&lt;&lt;";
        $conf['last_link']      = "&gt;&gt;";
        $conf['total_rows']     = $data['registros'];
        $offset                 = (int)$this->uri->segment(4);
        $listado                = $this->tipocambio_model->listar($fecha,$conf['per_page'],$offset);
        $item                   = $j+1;
        $lista                  = array();
        $listado_moneda =$this->moneda_model->listar();
        if(count($listado)>0){
            
            foreach($listado as $indice=>$valor)
            {   $codigo = $valor->TIPCAMP_Codigo;
                $fecha = $valor->TIPCAMC_Fecha;
                
                $valores_tipocam=array();
                foreach($listado_moneda as $reg){
                    if($reg->MONED_Codigo!=1){
                        $filter=new stdClass();
                        $filter->TIPCAMC_MonedaDestino=$reg->MONED_Codigo;
                        $filter->TIPCAMC_Fecha=$fecha;
                        $temp=$this->tipocambio_model->buscar($filter);
                        if(count($temp)>0)
                            $valores_tipocam[]=$temp[0]->TIPCAMC_FactorConversion;                        
                        else
                            $valores_tipocam[]='';
                    }
                }
                $ver = "<a href='#' onclick='ver_tipocambio(".str_replace('-', '',$fecha).")'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                //cambiar
                $modificar = "<a href='#' onclick='modificar_tipocambio(".str_replace('-', '',$fecha).")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";

                $lista[]        = array($item++,$fecha,$valores_tipocam,$ver,$modificar );
            }
        }
        $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de TIPO DE CAMBIO DEL DIA";
        $data['titulo_busqueda'] = "BUSCAR TIPO DE CAMBIO";
        $data['fecha']  	 = form_input(array("name"=>"fecha","id"=>"fecha","class"=>"cajaPequena","readonly"=>"readonly","maxlength"=>"10","value"=>$fecha));
        $data['form_open']       = form_open(base_url().'index.php/maestros/tipocambio/buscar',array("name"=>"form_busquedaTipoCambio","id"=>"form_busquedaTipoCambio"));
        $data['form_close']      = form_close();
        $data['listado_moneda']   = $listado_moneda;
        $data['lista']           = $lista;
        $data['oculto']          = form_hidden(array('base_url'=>base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('maestros/tipocambio_index',$data);
    }
	
    ###############################
    ####### TIPOS DE CAMBIO 2020
    ###############################
	
    public function buscar_json(){
        $fecha = $this->input->post("fecha");
        $moneda = $this->input->post("moneda");
        $compania = $this->input->post("compania");
        $tdc = $this->tipocambio_model->get_tdc_dia($fecha, $moneda);

        if ( count($tdc) > 0 )
            $json = array( "match" => true, "tdc" => $tdc->TIPCAMC_FactorConversion);
        else
            $json = array( "match" => false, "tdc" => NULL);

        echo json_encode($json);
    }

    public function ingesar_tipo_cambio(){
        $fecha = date('Y-m-d');
        $compania = $this->compania;

        $moneda = $this->input->post("moneda");
        $cambio = $this->input->post("tipo_cambio");

        $size = count($moneda);
        $id = 0;

        for ($i = 0; $i < $size; $i++) {
            if ($cambio[$i] != ""){
                $exists = $this->tipocambio_model->tcExists($fecha, $compania, $moneda[$i]); # VERIFICA SI EL TIPO DE CAMBIO YA ESTA REGISTRADO EN ALGUNA COMPAÑIA, "SI ESTA EN 1 ESTA EN TODAS".

                $filter = new stdClass();
                $filter->TIPCAMC_MonedaOrigen = 1;
                $filter->TIPCAMC_MonedaDestino = $moneda[$i];
                $filter->TIPCAMC_Fecha = $fecha;
                $filter->TIPCAMC_FactorConversion = $cambio[$i];

                if ($exists == false)
                    $id = $this->tipocambio_model->insertar($filter); # REGISTRA EL TIPO DE CAMBIO EN TODAS LAS COMPAÑIAS
                else
                    $id = $this->tipocambio_model->update_tc($filter); # ACTUALIZA EL TIPO DE CAMBIO EN TODAS LAS COMPAÑIAS
            }
        }

        $json = ($id != 0) ? array("result" => "success") : array("result" => "error");
        echo json_encode($json);
    }

    function getFechaTc(){
        $fecha = ( $this->input->post("fecha") != "" ) ? $this->input->post("fecha") : date("Y-m-d");
        $tcInfo = $this->tipocambio_model->getTCday($fecha);

        $allTc = true;

        if ($tcInfo != NULL){
            foreach ($tcInfo as $key => $value) {
                $data[] = array(
                                    "moneda" => $value->MONED_Codigo,
                                    "descripcion" => $value->MONED_Descripcion,
                                    "simbolo" => $value->MONED_Simbolo,
                                    "tipo_cambio" => $value->TIPCAMC_FactorConversion,
                                    "moneda_origen" => $value->moneda_origen
                                );
                if ( $value->TIPCAMC_FactorConversion == NULL )
                    $allTc = false;
            }

            $json = array( "match" => $allTc, "info" => $data);
        }
        else
            $json = array( "match" => false, "info" => NULL);

        echo json_encode($json);
    }

    public function datatable_listTipoCambio(){

        $columnas = array(
                            0 => "MONED_Descripcion",
                            1 => "",
                            2 => "",
                            3 => ""
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

        $filter->fecha = ( $this->input->post("fecha") != "" ) ? $this->input->post("fecha") : date("Y-m-d");

        $listado = $this->tipocambio_model->getListTipoCambio($filter);
        $lista = array();
        
        if ( count($listado) > 0 ){
            foreach ($listado as $indice => $valor) {
                $lista[] = array(
                    0 => $valor->MONED_Descripcion,
                    1 => $moneda_origen,
                    2 => number_format($valor->TIPCAMC_FactorConversion,2),
                    3 => "$valor->MONED_Simbolo 1.00 = $valor->simbolo_origen $valor->TIPCAMC_FactorConversion"
                );
            }
        }

        unset($filter->start);
        unset($filter->length);

        $json = array(
                            "draw"            => intval( $this->input->post('draw') ),
                            "recordsTotal"    => intval( count($this->tipocambio_model->getListTipoCambio()) ),
                            "recordsFiltered" => intval( count($this->tipocambio_model->getListTipoCambio($filter)) ),
                            "data"            => $lista
                    );

        echo json_encode($json);
    }
}
?>