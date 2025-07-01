<?php
class Lote extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('almacen/producto_model');
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/loteprorrateo_model');
        $this->load->model('almacen/serie_model');
        $this->load->model('almacen/guiain_model');
        $this->compania = $this->session->userdata('compania');
    }
    
    public function prorratear_producto($lote, $prorrateo=''){
        $datos_lote = $this->lote_model->obtener($lote);
        $datos_guiain = $this->guiain_model->obtener($datos_lote->GUIAINP_Codigo);
        $cantidad_actual = $this->serie_model->cantidad_series_presente_x_guiain($datos_lote->GUIAINP_Codigo, $datos_lote->PROD_Codigo);
     
        $lista_lotesprorrateo = $this->loteprorrateo_model->listar($lote);
        $lista = array();
        $PCanterior=count($lista_lotesprorrateo)>0 ? $lista_lotesprorrateo[0]->LOTPROC_CostoNuevo : $datos_lote->LOTC_Costo;
        foreach($lista_lotesprorrateo as $indice=>$value){
            $prorratear     = "<a href='javascript:;' onclick='editar_prorrateo(".$value->LOTPROP_Codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
            $lista[]=array(number_format($value->LOTPROC_CostoAnterior,2),$value->LOTPROC_CantActual,mysql_to_human($value->LOTPROC_Fecha),$value->LOTPROC_TipoDesc.($value->LOTPROC_FlagRecepProdu=='0' ? ' <label class="etiqueta_error">(MERC. NO RECEP)</label>' : ''),$value->LOTPROC_CantidadAdi,$value->LOTPROC_Valor,  number_format($value->LOTPROC_CostoNuevo,2), $prorratear);
        }
        
        $datos_loteprorr='';
        if($prorrateo!=''){
            $datos_loteprorr = $this->loteprorrateo_model->obtener($prorrateo);
        }
        
        $data['fecha']       = is_object($datos_loteprorr) ? mysql_to_human($datos_loteprorr->LOTPROC_Fecha) :  date('d/m/Y');
        $data['tipo']        = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_Tipo : '';
        $data['cboTipo']     = form_dropdown("cboTipo",array(""=>"::Seleccione::", "1"=>"CON EL MISMO PRODUCTO","2"=>"CON OTRO PRODUCTO", "3"=>"CON NOTA DE CREDITO", "4"=>"CON DEPOSITO"),(is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_Tipo : '')," class='comboMedio' id='cboTipo' style='width:160px'");
        $data['valor']       = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_Valor :'';
        $data['cantidadAdi'] = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_CantidadAdi :'';
        $data['observacion'] = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_Obs :'';
        $data['PCnuevo']     = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_CostoNuevo :'';
        $data['PCanterior']  = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_CostoAnterior : $PCanterior;
        $data['cantidad_actual'] = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_CantActual : $cantidad_actual;
        $data['recep_produ']     = is_object($datos_loteprorr) ? $datos_loteprorr->LOTPROC_FlagRecepProdu : '0';
        $data['proveedor']   = $datos_guiain[0]->PROVP_Codigo;
        $data['lista']       = $lista;
        $data['prorrateo']   = $prorrateo;
        
        $data['form_open']     = form_open(base_url()."index.php/almacen/lote/prorratear_producto_grabar",array("name"=>"frmProductoProrratear","id"=>"frmProductoProrratear"));
        $data['form_hidden']   = form_hidden(array("base_url"=>base_url(), 'lote'=>$lote, 'producto'=>$datos_lote->PROD_Codigo));
        $data['form_close']    = form_close();
        $this->load->view('almacen/ventana_prorratear_producto',$data);
    }
    
    public function editar_prorrateo($codigo){
        
    }
    
    public function prorratear_producto_grabar(){
        if($this->input->post('cboTipo')=='' || $this->input->post('cboTipo')=='0')
           exit ('{"result":"error", "campo":"cboTipo"}');
        if($this->input->post('recep_produ')=='1' && ($this->input->post('cboTipo')=='1' || $this->input->post('cboTipo')=='2') && $this->input->post('guiarem_detalle')=='' && $this->input->post('comprobante_detalle')=='')
           exit ('{"result":"error2", "msj":"No ha referenciado el detalle del documento que prorratea el presente PC."}');
        if($this->input->post('cboTipo')=='1' && $this->input->post('cantidadAdi')=='')
           exit ('{"result":"error", "campo":"cantidadAdi"}');
        if($this->input->post('cboTipo')!='1' && $this->input->post('valor')=='')
           exit ('{"result":"error", "campo":"valor"}');
        if($this->input->post('PCnuevo')=='')
           exit ('{"result":"error", "campo":"PCnuevo"}');
        
        $filter= new stdClass();
        $filter->LOTP_Codigo=$this->input->post('lote');
        $filter->LOTPROC_CostoAnterior=$this->input->post('PCanterior');
        $filter->LOTPROC_CantActual=$this->input->post('cantidad_actual');
        $filter->LOTPROC_Fecha=human_to_mysql($this->input->post('fecha'));
        $filter->LOTPROC_Tipo=$this->input->post('cboTipo');
        $filter->LOTPROC_CantidadAdi=$this->input->post('cantidadAdi')!='' ? $this->input->post('cantidadAdi') : NULL;
        $filter->LOTPROC_Valor=$this->input->post('valor')!='' ? $this->input->post('valor') : NULL;
        $filter->LOTPROC_Obs=$this->input->post('observacion')!='' ? $this->input->post('observacion') : NULL;
        $filter->LOTPROC_CostoNuevo=$this->input->post('PCnuevo');
        $filter->GUIAREMDETP_Codigo=$this->input->post('guiarem_detalle')!='' ? $this->input->post('guiarem_detalle') : NULL;
        $filter->CPDEP_Codigo=$this->input->post('comprobante_detalle')!='' ? $this->input->post('comprobante_detalle') : NULL;
        $filter->LOTPROC_FlagRecepProdu=$this->input->post('recep_produ')=='1' ? '1' : '0';
        $this->loteprorrateo_model->insertar($filter);
        
        $lista_lotes = $this->lote_model->listar($this->input->post('producto'));
        if($lista_lotes[0]->LOTP_Codigo==$this->input->post('lote')){ // Si el lote prorrateado es el ultimo entonces actualizo el precio de costo
            $filter=new stdClass();
            $this->producto_model->modificar_ultCosto($this->input->post('producto'),$this->input->post('PCnuevo'));
        }
        
        exit('{"result":"ok"}');
    }

    public function detalles(){
        $lote = $this->input->post('idLote');
        $producto = $this->input->post('producto');
        $almacen = $this->input->post('almacen');
        $toper = $this->input->post('tipo_oper');
        $detallesLote = $this->lote_model->detalles($producto, $almacen, $toper, $lote);
        echo json_encode($detallesLote);
    }

    public function nuevo_lote(){
        $idLote = $this->input->post('idLote');
        $producto = $this->input->post('producto');
        $numero = $this->input->post('numeroLote');
        $vencimiento = $this->input->post('vencimientoLote');
        $costo = $this->input->post('costoLote');
        $estado = $this->input->post('estado');

        if ($idLote == "" || $idLote == NULL){
            $filter = new stdClass();
            $filter->PROD_Codigo = $producto;
            $filter->LOTC_Numero = $numero;
            $filter->LOTC_Cantidad = 0;
            $filter->LOTC_Costo = ($costo != "" && $costo != NULL) ? $costo : 0;
            $filter->LOTC_FechaVencimiento = $vencimiento;
            $filter->LOTC_FechaRegistro = date("Y-m-d h:i:s");
            $filter->LOTC_Fechamodificacion = date("Y-m-d h:i:s");
            $filter->LOTC_FlagEstado = 2;
            
            $idLote = $this->lote_model->insertar($filter);
            if ($idLote != NULL && $idLote > 0){
                $detallesLote = $this->lote_model->obtener_lote($idLote);
                $result[] = array("result" => "success", "mensaje" => "El lote fue agregado.", "lote" => $detallesLote);
            }
            else{
                $result[] = array("result" => "error", "mensaje" => "El lote no fue agregado. Intentelo nuevamente. Si el problema persiste, comuniquese con el administrador.", "lote" => NULL);
            }
            echo json_encode($result);
        }
        else{
            $filter = new stdClass();
            $filter->PROD_Codigo = $producto;
            $filter->LOTC_Numero = $numero;
            $filter->LOTC_Costo = ($costo != "" && $costo != NULL) ? $costo : 0;
            $filter->LOTC_FechaVencimiento = $vencimiento;
            $filter->LOTC_Fechamodificacion = date("Y-m-d h:i:s");
            $filter->LOTC_FlagEstado = $estado;
            
            $idLote = $this->lote_model->modificar($idLote, $filter);
            if ($idLote != NULL && $idLote > 0){
                $detallesLote = $this->lote_model->obtener_lote($idLote);
                $result[] = array("result" => "success", "mensaje" => "El lote fue actualizado.", "lote" => $detallesLote);
            }
            else{
                $result[] = array("result" => "error", "mensaje" => "El lote no fue actualizado. Intentelo nuevamente. Si el problema persiste, comuniquese con el administrador.", "lote" => NULL);
            }
            echo json_encode($result);
        }
    }

    public function index(){
        $this->layout->view('seguridad/inicio');    
    }

    ### FUNCIONES PARA LA VISTA

    public function lote_index( $j = '0' ){
        
        $data['action'] = base_url() . "index.php/almacen/lote/lote_index";
        $filter = new stdClass();
        $filter->nombre_producto = $this->input->post('nombre_producto');
        $filter->codigo = $this->input->post('txtCodigo');
        $filter->nombre = $this->input->post('txtNombre');

        $data['registros'] = count($this->lote_model->lista_lotes($filter));
        $conf['base_url'] = site_url('almacen/lote/lote_index');
        $conf['per_page'] = 50;
        $conf['num_links'] = 3;
        $conf['first_link'] = "&lt;&lt;";
        $conf['last_link'] = "&gt;&gt;";
        $conf['total_rows'] = $data['registros'];
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        
        $listar = $this->lote_model->lista_lotes($filter, $conf['per_page'], $offset);
        $listarecetas = '';
        $item   = $j+1;
        $lista  = array();
        
        if(count($listar)>0){
            foreach ($listar as $indice => $valor){
                $id = $valor->LOTP_Codigo;
                $numero = $valor->LOTC_Numero;
                $vencimiento = mysql_to_human($valor->LOTC_FechaVencimiento);
                $costo = number_format($valor->LOTC_Costo,2);
                $cantidad = $valor->LOTC_Cantidad;
                $producto = $valor->PROD_Codigo;
                $producto_nombre = $valor->PROD_Nombre;
                $marca = $valor->MARCC_Descripcion;
                
                $editar = "<a href='#' onclick='editar_lote($id)'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar = ""; # "<a href='#' onclick='eliminar_receta($id)'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Eliminar'></a>";

                $lista[]        = array($item, $numero, $vencimiento, $cantidad, $costo, $producto_nombre, $marca, $eliminar, $editar);
                $item++;
            }
        }
        $data['nombre_producto'] = $filter->nombre_producto;
        $data['codigo'] = $filter->codigo;
        $data['nombre'] = $filter->nombre;
        $data['lista'] = $lista;
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('almacen/lote_index',$data);
    }

    public function editar_lote( $idLote ){
        if ($idLote != NULL && $idLote > 0){
            $detallesLote = $this->lote_model->obtener_lote($idLote);
            $result[] = array("result" => "success", "mensaje" => "Detalles del lote.", "lote" => $detallesLote);
        }
        else{
            $result[] = array("result" => "error", "mensaje" => "El lote no fue agregado. Intentelo nuevamente. Si el problema persiste, comuniquese con el administrador.", "lote" => NULL);
        }
        echo json_encode($result);
    }
}
?>