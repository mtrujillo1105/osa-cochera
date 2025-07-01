<?php

class TipoCaja extends CI_Controller{

    private $empresa;
    private $compania;
    private $usuario;
    private $url;

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');
        //$this->load->helper('json');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->model('tesoreria/tipocaja_model');
        $this->load->model('tesoreria/flujocaja_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('maestros/moneda_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('maestros/formapago_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('ventas/notacredito_model');
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('user');
        $this->view_js = array(0 => "tesoreria/tipocaja.js");
    }
	
    public function index(){
        $this->load->view('seguridad/inicio');
    }

    #########################
    ###### FUNCTIONS NEWS
    #########################

    public function tipocajas(){
        $data['base_url'] = base_url();
        $data['scripts']  = $this->view_js;
        $data['titulo_busqueda'] = "BUSCAR TIPOS DE CAJA";
        $data['titulo'] = "RELACIÓN EN TIPOS DE CAJA";
        $this->layout->view('tesoreria/tipoCajaIndex', $data);
    }

        public function datatable_tipocaja(){

            $columnas = array(
                                0 => "",
                                1 => "tipCa_Abreviaturas",
                                2 => "tipCa_Descripcion"
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

            $filter->descripcion = $this->input->post('descripcion');

            $cajaInfo = $this->tipocaja_model->getTipoCajas($filter);
            $lista = array();
            
            if (count($cajaInfo) > 0) {
                foreach ($cajaInfo as $indice => $valor) {
                    $btn_modal = "<button type='button' onclick='editar($valor->tipCa_codigo)' class='btn btn-default'>
                                    <img src='".base_url()."/public/images/icons/modificar.png' class='image-size-1b'>
                                </button>";

                    $btn_eliminar = "<button type='button' onclick='deshabilitar($valor->tipCa_codigo)' class='btn btn-default'>
                                    <img src='".base_url()."/public/images/icons/documento-delete.png' class='image-size-1b'>
                                </button>";

                    $lista[] = array(
                                        0 => $indice + 1,
                                        1 => $valor->tipCa_Abreviaturas,
                                        2 => $valor->tipCa_Descripcion,
                                        3 => $btn_modal,
                                        4 => $btn_eliminar
                                    );
                }
            }

            unset($filter->start);
            unset($filter->length);

            $json = array(
                                "draw"            => intval( $this->input->post('draw') ),
                                "recordsTotal"    => count($this->tipocaja_model->getTipoCajas()),
                                "recordsFiltered" => intval( count($this->tipocaja_model->getTipoCajas($filter)) ),
                                "data"            => $lista
                        );

            echo json_encode($json);
        }

        public function getTipoCaja(){

            $tipocaja = $this->input->post("tipocaja");

            $cajaInfo = $this->tipocaja_model->getTipoCaja($tipocaja);
            $lista = array();
            
            if ( $cajaInfo != NULL ){
                foreach ($cajaInfo as $indice => $val) {
                    $lista = array(
                                        "tipocaja" => $val->tipCa_codigo,
                                        "codigo" => $val->tipCa_Abreviaturas,
                                        "descripcion" => $val->tipCa_Descripcion
                                    );
                }

                $json = array("match" => true, "info" => $lista);
            }
            else
                $json = array("match" => false, "info" => "");

            echo json_encode($json);
        }

        public function guardar_registro(){

            $tipocaja = $this->input->post("tipocaja");
            $codigo_caja = $this->input->post("codigo_tipocaja");
            $descripcion_caja = $this->input->post("descripcion_tipocaja");
            
            $filter = new stdClass();
            $filter->tipCa_Descripcion = strtoupper($descripcion_caja);
            $filter->tipCa_Abreviaturas = $codigo_caja;
            $filter->tipCa_FlagEstado = "1";
            $filter->COMPP_Codigo = $this->compania;

            if ($tipocaja != ""){
                $filter->tipCa_codigo = $tipocaja;
                $filter->UsuarioModificado = $this->usuario;
                $filter->tipCa_FechaModificacion = date("Y-m-d H:i:s");
                $result = $this->tipocaja_model->actualizar_TipoCaja($tipocaja, $filter);
            }
            else{
                $filter->UsuarioRegistro = $this->usuario;
                $filter->tipCa_FechaRegistro = date("Y-m-d H:i:s");
                $result = $this->tipocaja_model->insertar_TipoCaja($filter);
            }

            if ($result)
                $json = array("result" => "success");
            else
                $json = array("result" => "error");
            
            echo json_encode($json);
        }

    public function deshabilitar_tipocaja(){
        $tipocaja = $this->input->post("tipocaja");
        $filter = new stdClass();
        $filter->tipCa_FlagEstado  = "0";
        if ($tipocaja != ""){
            $filter->tipCa_FechaModificacion = date("Y-m-d H:i:s");
            $result = $this->tipocaja_model->deshabilitar_TipoCaja($tipocaja, $filter);
        }
        if ($result)
            $json = array("result" => "success");
        else
            $json = array("result" => "error");
        echo json_encode($json);
    }

    #########################
    ###### FUNCTIONS OLDS
    #########################
	
	public  function  tipocajas_old( $j = '0', $limpia = ''){
		unset($_SESSION['serie']);
		
		if ($limpia == 1) {		
			$this->session->unset_userdata('fechai');
			$this->session->unset_userdata('fechaf');			
			$this->session->unset_userdata('txtTipo');
			$this->session->unset_userdata('txtCodigoT');	

		}
		$filter = new stdClass();
		if (count($_POST) > 0) {
			$filter->fechai = $this->input->post('fechai');
			$filter->fechaf = $this->input->post('fechaf');		
			$filter->txtTipo = $this->input->post('txtTipo');
			$filter->txtCodigoT = $this->input->post('txtCodigoT');		
			$this->session->set_userdata(array('fechai' => $filter->fechai, 'fechaf' => $filter->fechaf, 'txtTipo' => $filter->txtTipo,'txtCodigoT'=>$filter->txtCodigoT));
		} else {
			$filter->fechai = $this->session->userdata('fechai');
			$filter->fechaf = $this->session->userdata('fechaf');	
			$filter->txtTipo = $this->session->userdata('txtTipo');
			$filter->txtCodigoT = $this->session->userdata('txtCodigoT');
			
		}
		$data['fechai'] = $filter->fechai;
		$data['fechaf'] = $filter->fechaf;		
		$data['txtTipo'] = $filter->txtTipo;
		$data['txtCodigoT'] = $filter->txtCodigoT;				
		$conf['base_url'] = site_url('tesoreria/tipocaja/tipocajas');		
		$data['registros'] =count($this->tipocaja_model->tipocaja_listar_buscar($filter));//count($this->cuentas_model->listar(, '', '', $filter, $cond_pago, $comprobante, 1));
		$conf['per_page'] = 20;
		$conf['num_links'] = 3;
		$conf['first_link'] = "&lt;&lt;";
		$conf['last_link'] = "&gt;&gt;";
		//$conf['total_rows'] = $data['registros'];
		$conf['uri_segment'] = 5;
		//$offset = (int)$this->uri->segment(5);
		$conf['total_rows'] = $data['registros'];
		$offset = (int)$this->uri->segment(4);
		$listado_tipocaja =$this->tipocaja_model->tipocaja_listar_buscar($filter, $conf['per_page'], $offset);
		$item = $j + 1;
		$lista = array();
		//echo "<pre>";
		if (count($listado_tipocaja) > 0) {
			foreach ($listado_tipocaja as $indice => $valor) {
				$codigo = $valor->tipCa_codigo;
				$tipo_descripcion=$valor->tipCa_Descripcion;
				$abreviatura=$valor->tipCa_Abreviaturas;
				$tip_Caja=$valor->tipCa_Tipo;
				$usu_registro=$valor->UsuarioRegistro;
				$usu_modifi=$valor->UsuarioModificado;
				$fechaMod=$valor->tipCa_fechaModificacion;
				$fechaReg=$valor->tipCa_FechaRegistro;
				$compania=$valor->COMPP_Codigo;
				$estado=$valor->tipCa_FlagEstado;

				if($estado=="1"){
					$editar = "<a href='javascript:;' onclick='tipocaja_editar(" . $valor->tipCa_codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' </a>";
					$ver="<a href='#' id='vercaja".$indice."' onclick='getOptenerModal(".$codigo.','.$indice.")'  target='_parent'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' </a>";// = "<a 
					$eliminar ="<a href='javascript:;' onclick='tipocaja_Eliminar(" . $valor->tipCa_codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' </a>";
					$estados ="<a href='javascript:;'  target='_parent'><img src='" . base_url() . "images/active.png' width='16' height='16' border='0' </a>";					
				}else{
					$estados ="<a href='javascript:;' target='_parent'><img src='" . base_url() . "images/inactive.png' width='16' height='16' border='0' </a>";
					$editar = "";
					$ver="";
					$eliminar ="";
				}

				//= "<a href='javascript:;' onclick='comprobante_ver_pdf_conmenbrete(" . $codigo." )' target='_parent'><img src='" . base_url() . "images/pdf.png' width='16' height='16' border='0' title='Ver PDF'></a>";
				$lista[] = array($item++, $tipo_descripcion, $abreviatura,$tip_Caja , $usu_registro, $usu_modifi, $fechaMod, $fechaReg, $compania,$estado, $editar, $ver, $eliminar,$estados);
			}
		}

		//print_r($listado_cuentas);

		$data['titulo_tabla'] = "TIPO DE CAJA";
		$data['titulo_busqueda'] = "BUSCAR TIPO DE CAJA ";
		$data['tipo_caja'] = "";
		$data['lista'] = $lista;
		$data['oculto'] = form_hidden(array('base_url' => base_url()));
		$this->pagination->initialize($conf);
		$data['paginacion'] = $this->pagination->create_links();
		$this->layout->view('tesoreria/tipocajaIndex', $data);
	}
	
    public function tipocaja_nuevo() {
        $codigo = "";
        $this->session->unset_userdata('estado_pago2');
        $data['form_open'] = form_open(base_url() . 'index.php/tesoreria/tipocaja/tipocaja_grabar', array("name" => "frmtipocaja", "id" => "frmtipocaja"));
        $data['form_close'] = form_close();   
        $data['titulo'] = "REGISTRAR TIPO CAJA";
        $data["codigocaja"] ="";
        $data["tipo_descripcion"]="";
        $data["abreviatura"] =	"";
        $data["tip_Caja"]= "";
        $data["usu_registro"]=$this->session->userdata('nombre_persona');
        $data["usu_modifi"]= "";
        $data["fechaMod"]= "";
        $data["fechaReg"]=mysql_to_human(mdate("%Y-%m-%d ", time()));
        $data["compania"]= $this->session->userdata('compania');
        $data["estado"]= "1";
        //$data['alerta'] = $this->seleccionar_alerta();
        $data['oculto'] = form_hidden(array('codigo' => $codigo, 'base_url' => base_url()));
        $this->layout->view('tesoreria/tipocaja_nuevo', $data);
    }

    public function tipocaja_grabar() {
        $datos = array();
        if ($this->input->post('txtDescrip') == ''){
                exit('{"result":"error", "campo":"txtDescrip"}');
        } 
        if ( $this->input->post('txtAbreviatura') == ''){
                exit('{"result":"error", "campo":"txtAbreviatura"}');
        }
        if ( $this->input->post('txtTipocaja') == '::Seleccione::'){
                exit('{"result":"error", "campo":"txtTipocaja"}');
        } 
        $filter = new stdClass();
        $filter->tipCa_Descripcion = $this->input->post('txtDescrip');
        $filter->tipCa_FechaRegistro = human_to_mysql($this->input->post('fecha'));      
        $filter->UsuarioRegistro = $this->input->post('txtusuarioR');
        $filter->tipCa_Tipo = $this->input->post('txtTipocaja');
        $filter->tipCa_Abreviaturas = $this->input->post('txtAbreviatura');
        $filter->COMPP_Codigo = $this->input->post('txtCompania');
        $filter->tipCa_FlagEstado = $this->input->post('txtEstado');
        $presupuesto= $this->tipocaja_model->insert_tipocaja($filter);
        exit('{"result":"ok", "codigo":"' . $presupuesto . '"}');
    }

    public function tipocaja_editar($codigo){
        $this->session->unset_userdata('estado_pago2');
        $data['form_open'] = form_open(base_url() . 'index.php/tesoreria/tipocaja/tipocaja_modificar', array("name" => "frmtipocaja", "id" => "frmtipocaja"));
        $data['form_close'] = form_close();
        $obtenertipocaja=$this->tipocaja_model->getTipocaja($codigo);
        if (count($obtenertipocaja)>0) {

                foreach ($obtenertipocaja as $key => $value) {
                        $codigocaja = $value->tipCa_codigo;
                        $tipo_descripcion=$value->tipCa_Descripcion;
                        $abreviatura=$value->tipCa_Abreviaturas;
                        $tip_Caja=$value->tipCa_Tipo;
                        $usu_registro=$value->UsuarioRegistro;
                        $usu_modifi=$value->UsuarioModificado;
                        $fechaMod=$value->tipCa_fechaModificacion;
                        $fechaReg=$value->tipCa_FechaRegistro;
                        $compania=$value->COMPP_Codigo;
                        $estado=$value->tipCa_FlagEstado;
                }    
        }       
        $data["codigocaja"] =$codigocaja ;
        $data["tipo_descripcion"]=$tipo_descripcion;
        $data["abreviatura"] =	$abreviatura;
        $data["tip_Caja"]= $tip_Caja;
        $data["usu_registro"]=$usu_registro;
        $data["usu_modifi"]= $usu_modifi;
        $data["fechaMod"]= $fechaMod;
        $data["fechaReg"]=mysql_to_human($fechaReg);
        $data["compania"]= $compania;
        $data["estado"]= $estado;
        $data['titulo'] = "REGISTRAR PAGOS";
        $data['tipo_caja'] ="" ;
        //$data['alerta'] = $this->seleccionar_alerta();
        $data['oculto'] = form_hidden(array('codigo' => $codigo, 'base_url' => base_url()));
        $this->layout->view('tesoreria/tipocaja_nuevo', $data);	
    }	

	public function tipocaja_modificar(){
		$datos = array();
		if ($this->input->post('txtDescrip') == ''){
			exit('{"result":"error", "campo":"txtDescrip"}');
		}
		if ( $this->input->post('fecha') == ''){
			exit('{"result":"error", "campo":"fecha"}');
		} 
		if ( $this->input->post('txtAbreviatura') == ''){
			exit('{"result":"error", "campo":"txtAbreviatura"}');
		}
		if ( $this->input->post('txtTipocaja') == '::Seleccione::'){
			exit('{"result":"error", "campo":"txtTipocaja"}');
		} 
		$filter = new stdClass();
		$codigo=$this->input->post("txtcodigo");
		$filter->tipCa_Descripcion = $this->input->post('txtDescrip');
	        //$filter->tipCa_FechaRegistro = human_to_mysql($this->input->post('fecha'));   
		$filter->tipCa_fechaModificacion = human_to_mysql($this->input->post('fecha'));
		$filter->UsuarioModificado = $this->input->post('txtusuarioR');
		$filter->tipCa_Tipo = $this->input->post('txtTipocaja');
		$filter->tipCa_Abreviaturas = $this->input->post('txtAbreviatura');
		$filter->COMPP_Codigo = $this->input->post('txtCompania');
		$filter->tipCa_FlagEstado = $this->input->post('txtEstado');
		$this->tipocaja_model->tipocaja_modificar($codigo,$filter);
		exit('{"result":"ok", "codigo":"' . $codigo . '"}');  
	}

	public function JSON_listarTipoCaja($codigo){
		$lista_detalles = array();
		$obtenertipocaja=$this->tipocaja_model->getTipocaja($codigo);
		if (count($obtenertipocaja)>0) {

			foreach ($obtenertipocaja as $key => $value) {
				$objeto = new stdClass();
				$objeto->tipCa_codigo = $value->tipCa_codigo;
				$objeto->tipCa_Descripcion=$value->tipCa_Descripcion;
				$objeto->tipCa_Abreviaturas=$value->tipCa_Abreviaturas;
				$objeto->tipCa_Tipo=$value->tipCa_Tipo;
				$objeto->UsuarioRegistro=$value->UsuarioRegistro;
				$objeto->UsuarioModificado=$value->UsuarioModificado;
				$objeto->tipCa_fechaModificacion=$value->tipCa_fechaModificacion;
				$objeto->tipCa_FechaRegistro=$value->tipCa_FechaRegistro;
				$objeto->COMPP_Codigo=$value->COMPP_Codigo;
				$objeto->tipCa_FlagEstado=$value->tipCa_FlagEstado;
				$lista_detalles[] = ($objeto);
			}
			$resultado[] = array();
			$resultado = json_encode($lista_detalles,JSON_NUMERIC_CHECK);
			echo  $resultado;
		}
	}//final del metodo

	public function JSON_ActualizarTipoCaja($codigo){
		$this->tipocaja_model->getActualizarTipoCaja($codigo);
	}

}