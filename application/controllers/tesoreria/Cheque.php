<?php

class Cheque extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('pago');
        $this->load->helper('date');
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('tesoreria/cheque_model');
        $this->load->model('tesoreria/banco_model');
        $this->load->model('tesoreria/bancocta_model');

        $this->somevar['compania'] = $this->session->userdata('compania');
    }

    public function listar($j = '0', $limpia = '') {
        

        if ($limpia == '1') {
            $this->session->unset_userdata('fechai');
            $this->session->unset_userdata('fechaf');
            $this->session->unset_userdata('numero');
            $this->session->unset_userdata('cliente');
            $this->session->unset_userdata('ruc_cliente');
            $this->session->unset_userdata('nombre_cliente');
            $this->session->unset_userdata('tipo_cheque');
        }
        $filter = new stdClass();
        if (count($_POST) > 0) {
            $filter->fechai = $this->input->post('fechai');
            $filter->fechaf = $this->input->post('fechaf');
            $filter->numero = $this->input->post('numero');
            $filter->cliente = $this->input->post('cliente');
            $filter->ruc_cliente = $this->input->post('ruc_cliente');
            $filter->nombre_cliente = $this->input->post('nombre_cliente');
            $filter->tipo_cheque = $this->input->post('tipo_cheque');
            $this->session->set_userdata(array('fechai' => $filter->fechai, 'fechaf' => $filter->fechaf, 'numero' => $filter->numero, 'cliente' => $filter->cliente, 'ruc_cliente' => $filter->ruc_cliente, 'nombre_cliente' => $filter->nombre_cliente));
        } else {
            $filter->fechai = $this->session->userdata('fechai');
            $filter->fechaf = $this->session->userdata('fechaf');
            $filter->numero = '';
            $filter->tipo_cheque = $this->session->userdata('tipo_cheque');
            /*
              $filter->cliente        = $this->session->userdata('cliente');
              $filter->ruc_cliente    = $this->session->userdata('ruc_cliente');
              $filter->nombre_cliente = $this->session->userdata('nombre_cliente');
             */
            $filter->cliente = "";
            $filter->ruc_cliente = "";
            $filter->nombre_cliente = "";
        }
        $data['fechai'] = $filter->fechai;
        $data['fechaf'] = $filter->fechaf;
        $data['numero'] = $filter->numero;
        $data['cliente'] = $filter->cliente;
        $data['ruc_cliente'] = $filter->ruc_cliente;
        $data['nombre_cliente'] = $filter->nombre_cliente;
        $data['tipo_cheque'] = $filter->tipo_cheque;
        $data['registros'] = count($this->cheque_model->listar());
        $conf['base_url'] = site_url('tesoreria/cheque/listar/');
        $conf['per_page'] = 50;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 5;
        $offset = (int) $this->uri->segment(5);
        //var_dump($filter);
        $listado_cheque = $this->cheque_model->listar($conf['per_page'], $offset, $filter);
        $item = $j + 1;
        $lista = array();
        if (count($listado_cheque) > 0) {
            foreach ($listado_cheque as $indice => $valor) {
                $nombre = "";
                if ($valor->CLIP_Codigo != '') {
                    $datos_cliente = $this->cliente_model->obtener($valor->CLIP_Codigo);
                    if (count($datos_cliente) > 0) {
                        $nombre = $datos_cliente->nombre;
                    } else {
                        $nombre = "";
                    }
                }
                $razon_social = "";
//                var_dump($valor->PROVP_Codigo);
//                echo "<br/>";
//                var_dump($valor->CLIP_Codigo);
                if (isset($valor->PROVP_Codigo) && $valor->PROVP_Codigo != '') {
                    $datos_proveedor = $this->proveedor_model->obtener_Proveedor($valor->PROVP_Codigo);
                    if (count($datos_proveedor) > 0) {
                        $razon_social = $datos_proveedor[0]->EMPRC_RazonSocial;
                    } else {
                        $razon_social = "";
                    }
                }
                //
                $monto = $valor->PAGC_Monto;
                $moneda = $valor->MONED_Simbolo;
                $nro = $valor->CHEC_Nro;
                $femis = mysql_to_human($valor->CHEC_FEmis);
                $fvenc = mysql_to_human($valor->CHEC_FVenc);
                $flagCobro = $valor->CHEC_FlagCobro;
                $fCobro = $valor->CHEC_FCobro != '' ? mysql_to_human($valor->CHEC_FCobro) : '';
                $flagDeposito = $valor->CHEC_FlagDeposito;
                $fDeposito = $valor->CHEC_FDeposito != '' ? mysql_to_human($valor->CHEC_FDeposito) : '';

                $cobro = "<a href='javascript:;' onclick='ver_cobro(" . $valor->CHEP_Codigo . ")' target='_parent' style='display:none;'><img src='" . base_url() . "images/dolar.png' width='16' height='16' border='0' title='Cobro Realizado'></a>";
                $deposito = "<a href='javascript:;' onclick='ver_deposito(" . $valor->CHEP_Codigo . ")' target='_parent' style='display:none;><img src='" . base_url() . "images/banco.png' width='17' height='17' border='0' title='Depósito en el Banco'></a>";
                $lista[] = array($item++, $nro, $femis, $fvenc, $nombre, number_format($monto, 2), $moneda, $flagCobro, $fCobro, $flagDeposito, $fDeposito, $cobro, $deposito, $razon_social);
            }
        }
        $data['titulo_tabla'] = "CHEQUES";
        $data['titulo_busqueda'] = "BUSCAR CHEQUES";
        $data['lista'] = $lista;
        $data['oculto'] = form_hidden(array('base_url' => base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('tesoreria/cheque_index', $data);
    }

    public function cobro($cheque) {
        

        $lista_cheque = $this->cheque_model->obtener($cheque);
        $datos_cliente = $this->cliente_model->obtener($lista_cheque[0]->CLIP_Codigo);
        if ($datos_cliente)
            $nombre = $datos_cliente->nombre;
        $data['nro'] = $lista_cheque[0]->CHEC_Nro;
        $data['femis'] = mysql_to_human($lista_cheque[0]->CHEC_FEmis);
        $data['fvenc'] = mysql_to_human($lista_cheque[0]->CHEC_FVenc);
        $data['nombre'] = $nombre;
        $data['cobro'] = $lista_cheque[0]->CHEC_FlagCobro;
        $data['fecha'] = $lista_cheque[0]->CHEC_FCobro != '' ? mysql_to_human($lista_cheque[0]->CHEC_FCobro) : '';
        $data['observacion'] = $lista_cheque[0]->CHEC_ObsCobro;

        $oculto = form_hidden(array('accion' => "", 'codigo' => $cheque, 'base_url' => base_url()));
        $data['titulo'] = "REGISTRAR COBRO";
        $data['formulario'] = "frmCheque";
        $data['oculto'] = $oculto;
        $this->layout->view('tesoreria/cheque_cobro', $data);
    }

    public function cobro_grabar() {
        if ($this->input->post('cobro') == '1' && $this->input->post('fecha') == '')
            exit('{"result":"error", "campo":"fecha"}');

        $filter = new stdClass();
        $filter->CHEC_FlagCobro = $this->input->post('cobro') == '' ? '0' : $this->input->post('cobro');
        $filter->CHEC_FCobro = NULL;
        if ($this->input->post('cobro') == '1' && $this->input->post('fecha') != '')
            $filter->CHEC_FCobro = human_to_mysql($this->input->post('fecha'));
        $filter->CHEC_ObsCobro = NULL;
        if ($this->input->post('cobro') == '1' && $this->input->post('observacion') != '')
            $filter->CHEC_ObsCobro = $this->input->post('observacion');
        $this->cheque_model->modificar($this->input->post('codigo'), $filter);

        exit('{"result":"ok", "codigo":"' . $this->input->post('codigo') . '"}');
    }

    public function deposito($cheque) {
        

        $lista_cheque = $this->cheque_model->obtener($cheque);
        $datos_cliente = $this->cliente_model->obtener($lista_cheque[0]->CLIP_Codigo);
        if ($datos_cliente)
            $nombre = $datos_cliente->nombre;
        $data['nro'] = $lista_cheque[0]->CHEC_Nro;
        $data['femis'] = mysql_to_human($lista_cheque[0]->CHEC_FEmis);
        $data['fvenc'] = mysql_to_human($lista_cheque[0]->CHEC_FVenc);
        $data['nombre'] = $nombre;
        $data['deposito'] = $lista_cheque[0]->CHEC_FlagDeposito;
        $data['fecha'] = $lista_cheque[0]->CHEC_FDeposito != '' ? mysql_to_human($lista_cheque[0]->CHEC_FDeposito) : '';
        $data['banco'] = '';
        $data['cta'] = $lista_cheque[0]->CHEC_CtaDeposito;
        $data['observacion'] = $lista_cheque[0]->CHEC_ObsDeposito;


        $lista_cta = array('' => ':: Seleccione ::');
        if ($lista_cheque[0]->CHEC_CtaDeposito != '') {
            $data_cta = $this->bancocta_model->obtener($lista_cheque[0]->CHEC_CtaDeposito);
            $lista_cta = $this->bancocta_model->seleccionar($data_cta[0]->BANP_Codigo);
            $data['banco'] = $data_cta[0]->BANP_Codigo;
        }
        $data['cboBanco'] = $this->OPTION_generador($this->banco_model->listar(), 'BANP_Codigo', array('BANC_Nombre', 'BANC_Siglas'), $data['banco']);
        $data['cboCta'] = form_dropdown("cta", $lista_cta, $lista_cheque[0]->CHEC_CtaDeposito, " class='comboMedio' id='cta'" . ($lista_cheque[0]->CHEC_FlagDeposito != '1' ? " disabled='disabled'" : ""));

        $oculto = form_hidden(array('accion' => "", 'codigo' => $cheque, 'base_url' => base_url()));
        $data['titulo'] = "REGISTRAR DEPOSITO";
        $data['formulario'] = "frmCheque";
        $data['oculto'] = $oculto;
        $this->layout->view('tesoreria/cheque_deposito', $data);
    }

    public function deposito_grabar() {
        if ($this->input->post('deposito') == '1' && $this->input->post('fecha') == '')
            exit('{"result":"error", "campo":"fecha"}');
        if ($this->input->post('deposito') == '1' && $this->input->post('banco') == '')
            exit('{"result":"error", "campo":"banco"}');
        if ($this->input->post('deposito') == '1' && $this->input->post('cta') == '')
            exit('{"result":"error", "campo":"cta"}');
        if ($this->input->post('deposito') == '1' && $this->input->post('cta') == '')
            exit('{"result":"error", "campo":"banco"}');

        $filter = new stdClass();
        $filter->CHEC_FlagDeposito = $this->input->post('deposito') == '' ? '0' : $this->input->post('deposito');
        $filter->CHEC_FDeposito = NULL;
        if ($this->input->post('deposito') == '1' && $this->input->post('fecha') != '')
            $filter->CHEC_FDeposito = human_to_mysql($this->input->post('fecha'));
        $filter->CHEC_CtaDeposito = NULL;
        if ($this->input->post('deposito') == '1' && $this->input->post('cta') != '')
            $filter->CHEC_CtaDeposito = $this->input->post('cta');
        $filter->CHEC_ObsDeposito = NULL;
        if ($this->input->post('deposito') == '1' && $this->input->post('observacion') != '')
            $filter->CHEC_ObsDeposito = $this->input->post('observacion');
        $this->cheque_model->modificar($this->input->post('codigo'), $filter);
        exit('{"result":"ok", "codigo":"' . $this->input->post('codigo') . '"}');
    }

}

?>