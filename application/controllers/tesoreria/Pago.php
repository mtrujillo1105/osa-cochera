<?php

class Pago extends CI_Controller {

    public function __construct() {
        parent::Controller();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/cliente_model');

        $this->somevar['compania'] = $this->session->userdata('compania');
    }

    public function listar($cuenta) {
        
        $data['registros'] = count($this->cuentaspago_model->listar($cuenta));

        $lista_cuenta = $this->cuentas_model->obtener($cuenta);

        /* Comprobante de Pago */
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($lista_cuenta[0]->CUE_CodDocumento);
        $data['tipo_oper'] = $datos_comprobante[0]->CPC_TipoOperacion;
        $data['tipo_docu'] = $datos_comprobante[0]->CPC_TipoDocumento;
        $data['fecha'] = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $data['serie'] = $datos_comprobante[0]->CPC_Serie;
        $data['numero'] = $datos_comprobante[0]->CPC_Numero;
        $data['total'] = $datos_comprobante[0]->CPC_total;

        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $data['simbolo_moneda'] = $datos_moneda[0]->MONED_Simbolo;
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['cboFormaPago'] = $this->OPTION_generador($this->formapago_model->listar(), 'FORPAP_Codigo', 'FORPAC_Descripcion', '');
        $data['oculto'] = form_hidden(array('codigo' => '', 'base_url' => base_url(), 'tipo_cuenta' => $lista_cuenta[0]->CUE_TipoCuenta, 'cuenta' => $cuenta));

        $ruc_cliente = '';
        $nombre_cliente = '';
        $nombre_proveedor = '';
        $ruc_proveedor = '';
        if ($datos_comprobante[0]->CPC_TipoOperacion == 'V') {
            $datos_cliente = $this->cliente_model->obtener($datos_comprobante[0]->CLIP_Codigo);
            if ($datos_cliente) {
                $data['nombre_cliente'] = $datos_cliente->nombre;
                $data['ruc_cliente'] = $datos_cliente->ruc;
            }
        } else {
            $datos_proveedor = $this->proveedor_model->obtener($datos_comprobante[0]->PROVP_Codigo);
            if ($datos_proveedor) {
                $data['nombre_proveedor'] = $datos_proveedor->nombre;
                $data['ruc_proveedor'] = $datos_proveedor->ruc;
            }
        }

        /* pagos realizados */
        $listado_pago = $this->cuentaspago_model->listar($cuenta);
        $lista = array();
        if (is_array($listado_pago)) {
            foreach ($listado_pago as $indice => $valor)
              //  $numero=$valor->PAGP_Numero;

                $lista[] = array($indice + 1, mysql_to_human($valor->PAGC_FechaOper), $valor->MONED_Simbolo, $valor->CPAGC_Monto, $this->pago_model->obtener_forma_pago($valor->PAGC_FormaPago), $valor->PAGC_Obs, $valor->PAGP_Serie.'-'. $this->getOrderNumeroSerie($valor->PAGP_Numero));
        }

        $avance = $this->pago_model->sumar_pagos($listado_pago, $datos_comprobante[0]->MONED_Codigo);
        $array_estado = explode('_|_', obtener_estado_formato($lista_cuenta[0]->CUE_Monto, $avance));
        $data['estado_formato'] = $array_estado[0];
        $data['total'] = $lista_cuenta[0]->CUE_Monto;
        $data['saldo'] = $lista_cuenta[0]->CUE_Monto - $avance;


        $data['lista'] = $lista;
        $data['tipo_cuenta'] = $lista_cuenta[0]->CUE_TipoCuenta;
        $data['form_open'] = form_open(base_url() . 'index.php/tesoreria/flujocaja/grabar', array("name" => "frmFlujocaja", "id" => "frmFlujocaja"));
        $data['form_close'] = form_close();
        $data['titulo_tabla'] = "RELACION DE " . ($lista_cuenta[0]->CUE_TipoCuenta == '1' ? 'COBROS' : 'PAGOS');
        $this->layout->view('tesoreria/pago_index', $data);
    }

    public function getOrderNumeroSerie($numero){
    $dato="";
    $cantidad=strlen($numero);
    
    if($cantidad==1){
        $dato ="00000$numero";
    }
    if($cantidad==2){
         $dato ="0000$numero";
    }
    if($cantidad==3){
        $dato ="000$numero";
    }
    if($cantidad==4){
        $dato= "00$numero";
    }
    if($cantidad==5){
        $dato ="0$numero";
    }
    if($cantidad==6){
        $dato ="$numero";
    }
    return $dato;
    }
    public function listar_ultimos($tipo_cuenta, $codigo) {
        

        $listado_pago = $this->pago_model->listar_ultimos($tipo_cuenta, $codigo);
        $lista = array();
        if (is_array($listado_pago)) {
            foreach ($listado_pago as $indice => $valor) {
                /* $nombre='';
                  $ruc='';
                  if($valor->PAGC_TipoCuenta=='1'){
                  $datos_cliente   = $this->cliente_model->obtener($valor->CLIP_Codigo);
                  if($datos_cliente){
                  $nombre = $datos_cliente->nombre;
                  $ruc    = $datos_cliente->ruc;
                  }
                  }else{
                  $datos_proveedor   = $this->proveedor_model->obtener($valor->PROVP_Codigo);
                  if($datos_proveedor){
                  $nombre = $datos_proveedor->nombre;
                  $ruc    = $datos_proveedor->ruc;
                  }
                  } */
                $tipo_documento = $valor->CPC_TipoDocumento;
                $desc_documento='';
                if($tipo_documento=='B'){
                    $desc_documento='BOLETA';
                }else if($tipo_documento=='F'){
                    $desc_documento='FACTURA';
                }
                $serie = $valor->CPC_Serie;
                $num_doc = $valor->CPC_Numero;
                $anular = "<a href='javascript:;' onclick='anular_pago(" . $valor->PAGP_Codigo . ")'><img src='" . base_url() . "images/anularpago.png' width='16' height='16' border='0' title='Anular Pago' alt='anular'></a>";
                $lista[] = array(($indice + 1), mysql_to_human($valor->PAGC_FechaOper), $serie, $num_doc, obtener_forma_pago($valor->PAGC_FormaPago), $valor->MONED_Simbolo, number_format($valor->PAGC_Monto, 4), $valor->PAGC_Obs, $anular,$desc_documento);
            }
        }

        $temp = $this->obtener_nombre_numdoc(($tipo_cuenta == '1' ? 'CLIENTE' : 'PROVEEDOR'), $codigo);
        $data['ruc'] = $temp['numdoc'];
        $data['nombre'] = $temp['nombre'];

        $data['lista'] = $lista;
        $data['tipo_cuenta'] = $tipo_cuenta;
        $data['oculto'] = form_hidden(array('base_url' => base_url(), 'tipo_cuenta' => $tipo_cuenta, 'codigo' => $codigo));
        $this->layout->view('tesoreria/pago_ultimos', $data);
    }

    function anular($tipo_cuenta, $codigo, $pago) {
        $lista_pago = $this->cuentaspago_model->listar_pago($pago);
        if (is_array($lista_pago)) {
            foreach ($lista_pago as $indice => $valor) {
                $this->cuentaspago_model->anular($valor->CPAGP_Codigo);
                $listado_pago = $this->cuentaspago_model->listar($valor->CUE_Codigo);
                $this->cuentas_model->modificar_estado($valor->CUE_Codigo, (count($listado_pago) > 0 ? 'A' : 'V'));
                $this->pago_model->anular($valor->PAGP_Codigo);
            }
        }
        $this->listar_ultimos($tipo_cuenta, $codigo);
    }

    function obtener_nombre_numdoc($tipo, $codigo) {
        $nombre = '';
        $numdoc = '';
        if ($tipo == 'CLIENTE') {
            $datos_cliente = $this->cliente_model->obtener($codigo);
            if ($datos_cliente) {
                $nombre = $datos_cliente->nombre;
                $numdoc = $datos_cliente->ruc;
            }
        } else {
            $datos_proveedor = $this->proveedor_model->obtener($codigo);
            if ($datos_proveedor) {
                $nombre = $datos_proveedor->nombre;
                $numdoc = $datos_proveedor->ruc;
            }
        }
        return array('numdoc' => $numdoc, 'nombre' => $nombre);
    }

}

?>