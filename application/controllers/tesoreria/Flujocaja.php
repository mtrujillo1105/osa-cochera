<?php
class Flujocaja extends CI_Controller
{
    public function __construct()
    {
        parent::Controller();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('tesoreria/flujocaja_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/cliente_model');
        
        $this->somevar['compania'] = $this->session->userdata('compania');
    }
    public function listar($cuenta)
    {
        
        $data['registros']   = count($this->flujocaja_model->listar($cuenta));
        $conf['base_url']    = site_url('tesoreria/flujocaja/listar/');

        $listado_flujo       = $this->flujocaja_model->listar($cuenta);
        $lista               = array();
        if(is_array($listado_flujo)){
            foreach($listado_flujo as $indice=>$valor)
                $lista[]        = array($indice+1,mysql_to_human($valor->FLUCAJ_FechaOperacion), $valor->FLUCAJ_Importe,$valor->FORPAC_Descripcion, $valor->FLUCAJ_Observacion);
        }
        $lista_cuenta           = $this->cuentas_model->obtener($cuenta);
        $avance                 = $this->sumar_pagos($listado_flujo);
                
        $data['estado_formato'] = $this->obtener_estado_formato($lista_cuenta[0]->CUE_Monto, $avance);
        $data['total']          = $lista_cuenta[0]->CUE_Monto;
        $data['saldo']          = $lista_cuenta[0]->CUE_Monto - $avance;
                
        /*Comprobante de Pago*/
        $datos_comprobante      = $this->comprobante_model->obtener_comprobante($lista_cuenta[0]->CUE_CodDocumento);
        $data['tipo_oper']      = $datos_comprobante[0]->CPC_TipoOperacion;
        $data['tipo_docu']      = $datos_comprobante[0]->CPC_TipoDocumento;
        $data['fecha']          = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $data['serie']          = $datos_comprobante[0]->CPC_Serie;
        $data['numero']         = $datos_comprobante[0]->CPC_Numero;
        $data['total']          = $datos_comprobante[0]->CPC_total;
        
        $datos_moneda           = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $data['simbolo_moneda'] = $datos_moneda[0]->MONED_Simbolo;
        
        
        $data['cboMoneda']     = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['cboFormaPago']  = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '');
        $data['oculto']        = form_hidden(array('codigo'=>'','base_url'=>base_url(), 'tipo_cuenta'=>$lista_cuenta[0]->CUE_TipoCuenta, 'cuenta'=>$cuenta));
        
        $ruc_cliente='';
        $nombre_cliente='';
        $nombre_proveedor='';
        $ruc_proveedor='';
        if($datos_comprobante[0]->CPC_TipoOperacion=='V'){
            $datos_cliente   = $this->cliente_model->obtener($datos_comprobante[0]->CLIP_Codigo);
            if($datos_cliente){
                $data['nombre_cliente'] = $datos_cliente->nombre;
                $data['ruc_cliente']    = $datos_cliente->ruc;
            }
        }
        else{
            $datos_proveedor   = $this->proveedor_model->obtener($datos_comprobante[0]->PROVP_Codigo);
            if($datos_proveedor){
                $data['nombre_proveedor'] = $datos_proveedor->nombre;
                $data['ruc_proveedor']    = $datos_proveedor->ruc;
            }
        }
        
               
        $data['lista']            = $lista;
        $data['tipo_cuenta']      = $lista_cuenta[0]->CUE_TipoCuenta;
        $data['form_open']        = form_open(base_url().'index.php/tesoreria/flujocaja/grabar',array("name"=>"frmFlujocaja","id"=>"frmFlujocaja"));
        $data['form_close']       = form_close();
        $data['titulo_tabla']     = "RELACION DE ".($lista_cuenta[0]->CUE_TipoCuenta=='1' ? 'COBROS' : 'PAGOS');
        $this->layout->view('tesoreria/flujocaja_flujo',$data);
			
    }
    public function grabar()
    {
        if($this->input->post('importe')=='')   
           exit ('{"result":"error", "campo":"importe"}');
        if($this->input->post('forma_pago')=='' || $this->input->post('forma_pago')=='0')
           exit ('{"result":"error", "campo":"forma_pago"}');
        
       
        $descripcion  = $this->input->post("nombre_formapago");
        $codigo   = $this->input->post("codigo");
        $filter = new stdClass();
        $filter->CUE_Codigo = $this->input->post("cuenta");
        $filter->FLUCAJ_FechaOperacion = human_to_mysql($this->input->post("fecha"));
        $filter->MONED_Codigo = $this->input->post("moneda");
        $filter->FLUCAJ_Importe = $this->input->post("importe");
        $filter->FORPAP_Codigo = $this->input->post("forma_pago");
        $filter->FLUCAJ_NumeroDoc = $this->input->post("num_doc")!='' ? $this->input->post("num_doc") : NULL;
        $filter->FLUCAJ_Observacion = $this->input->post("observacion")!='' ? $this->input->post("observacion") : NULL;
        
        
        if(isset($codigo) && $codigo>0){
          $this->flujocaja_model->modificar($codigo,$filter);
        }
        else{
           $codigo=$this->flujocaja_model->insertar($filter);
        }
        exit('{"result":"ok", "codigo":"'.$codigo.'"}');
        
    }
   
    public function sumar_pagos($listado_flujo){
        $suma=0;
        if(is_array($listado_flujo)){
            foreach($listado_flujo as $indice=>$valor){
                $suma+=$valor->FLUCAJ_Importe;
            }
        }
        return $suma;
    }
    
    public function obtener_estado_formato($total, $avance){
        $grado = $total!=0 ? $avance/$total*100 : 100;
        $result = '';
        if($grado==100)
            $result="<label style='padding: 1px; background-color: #00D269; text-align:center'>Cancelado</label>";
        elseif($grado>=0 && $grado<50)
            $result="<label style='padding: 1px; background-color: #FF6464; text-align:center'>Pendiente</label>";
        else
            $result="<label style='padding: 1px; background-color: #FFB648; text-align:center'>Pendiente</label>";

        return $result;
    }

   
}
?>