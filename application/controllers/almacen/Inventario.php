<?php

class Inventario extends CI_Controller {

    public function __construct() {

        parent::Controller();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->helper('my_permiso');
        $this->load->helper('my_almacen');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('pagination');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/guiaindetalle_model');
        $this->load->model('almacen/inventario_model');
        $this->load->model('almacen/kardex_model');
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/almacenproducto_model');
        
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/almaprolote_model');

        $this->load->model('almacen/almacenproductoserie_model');
        $this->load->model('almacen/Serie_model');
        $this->load->model('almacen/seriedocumento_model');
        
        $this->load->model('almacen/Seriemov_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->load->model('maestros/configuracion_model');
        $this->somevar['user'] = $this->session->userdata('user');
        date_default_timezone_set('America/Lima');
    }

    public function listar($j = 0) {

        
        $count_datos = $this->inventario_model->count_inventario();
        $data['registros'] = $count_datos[0]->conteo;
        $data['lista'] = array();
        $data['titulo_busqueda'] = 'Listado de Inventarios Realizados';
        $conf['base_url'] = site_url('almacen/inventario/listar/');
        $conf['per_page'] = 25;
        $conf['num_links'] = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['total_rows'] = $data['registros'];
        $conf['last_link'] = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['t_indice'] = $j;
        $datos = $this->inventario_model->buscar_inventario(NULL, $conf['per_page'], $offset);
        $data['paginacion'] = $this->pagination->create_links();
        $data['lista'] = $datos;
        $this->layout->view('almacen/inventario_index', $data);
    }

    public function listar_refresh($j = 0) {
        $count_datos = $this->inventario_model->count_inventario();
        $data['registros'] = $count_datos[0]->conteo;
        $data['lista'] = array();
        $data['titulo_busqueda'] = 'Listado de Inventarios Realizados';
        $conf['base_url'] = site_url('almacen/inventario/listar/');
        $conf['per_page'] = 25;
        $conf['num_links'] = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['total_rows'] = $data['registros'];
        $conf['last_link'] = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $offset = (int) $this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['t_indice'] = $j;
        $datos = $this->inventario_model->buscar_inventario(NULL, $conf['per_page'], $offset);
        $data['paginacion'] = $this->pagination->create_links();
        $data['lista'] = $datos;
        $this->load->view('almacen/inventario_index_refresh', $data);
    }

    public function nuevo() {
        $data['titulo'] = '';
        $data['action'] = base_url() . 'index.php/almacen/inventario/insertar';
        $data['fecha_registro'] = date('d/m/Y');
        $data['cod_inventario'] = '';
        $compania = $this->session->userdata('compania');
        // $establecimiento = $this->session->userdata('idcompania');
        $data['almacenes'] = $this->almacen_model->buscar_x_compania($compania);
        $data['almacen'] = '';
        $documento = $this->configuracion_model->obtener_numero_documento($compania, 4);
        $data['serie'] = str_pad($documento[0]->CONFIC_Serie, 3, "0", STR_PAD_LEFT);
        $data['numero'] = str_pad($documento[0]->CONFIC_Numero, 6, "0", STR_PAD_LEFT);
        $this->load->view('almacen/inventario_nuevo', $data);
    }

    public function modificar($cod_inventario) {

        $data['titulo'] = 'MODIFICAR INVENTARIO';
        $data['action'] = base_url() . 'index.php/almacen/inventario/editar';
        $data['fecha_registro'] = date('d/m/Y');
        $data['cod_inventario'] = $cod_inventario;
        $compania = $this->session->userdata('compania');
        $data['almacenes'] = $this->almacen_model->buscar_x_compania($compania);
        $filter = new stdClass();
        $filter->cod_inventario = $cod_inventario;
        $datos = $this->inventario_model->buscar_inventario($filter);
        $data['almacen'] = $datos[0]->ALMAP_Codigo;
        $data['titulo'] = $datos[0]->INVE_Titulo;
        $data['serie'] = str_pad($datos[0]->INVE_Serie, 3, "0", STR_PAD_LEFT);
        $data['numero'] = str_pad($datos[0]->INVE_Numero, 6, "0", STR_PAD_LEFT);
        $this->load->view('almacen/inventario_nuevo', $data);
    }

    public function insertar() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $resul = $this->inventario_model->insertar($datos);
            if ($resul)
                die('ok');
            else
                die('ERROR: No se puedo completar la operación.');

        }
    }

    public function editar() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $resul = $this->inventario_model->editar($datos);
            if ($resul)
                die('ok');
            else
                die('ERROR: No se puedo completar la operación.');

        }
    }

    public function agregar_detalle($cod_inventario) {

        $data['titulo'] = 'AGREGAR DETALLE AL INVENTARIO';
        $data['action'] = base_url() . 'index.php/almacen/inventario/insertar_detalle';
        $data['fecha_registro'] = date('d/m/Y');
        $data['cod_inventario'] = $cod_inventario;
        $filter = new stdClass();
        $filter->cod_inventario = $cod_inventario;
        $datos = $this->inventario_model->buscar_inventario($filter);
        $data['titulo'] = $datos[0]->INVE_Titulo;
        $data['codigoAlmacen'] = $datos[0]->ALMAP_Codigo;
        $data['serie'] = str_pad($datos[0]->INVE_Serie, 3, "0", STR_PAD_LEFT);
        $data['numero'] = str_pad($datos[0]->INVE_Numero, 6, "0", STR_PAD_LEFT);
        /**PARA SERIES LO DEFINIMOS COMO UNA COMPRA**/
        $data['tipo_oper']='C';
        $this->load->view('almacen/inventario_nuevo_detalle', $data);
        /**gcbq limpiamos la session de series guardadas**/
        unset($_SESSION['serie']);
        unset($_SESSION['serieReal']);
        unset($_SESSION['serieRealBD']);
        /**fin de limpiar session***/
    }

    public function insertar_detalle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $valor = $datos['cod_producto'];
            $codigoAlmacen = $datos['almacen'];
            $flagGenInd = $datos['flagGenInd'];
            $result = $this->inventario_model->insertar_detalle($datos);
            if ($result){
            	/**gcbq insertar serie de cada producto**/
            	if($flagGenInd='I'){
            		if($valor!=null){
            			/**obtenemos las series de session por producto***/
            			$seriesProducto=$this->session->userdata('serieReal');
            			if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
            				foreach ($seriesProducto as $alm => $arrAlmacen) {
            					if($alm==$codigoAlmacen){
            						foreach ($arrAlmacen as $ind2 => $arrserie2){
		            					if ($ind2 == $valor) {
		            						$serial = $arrserie2;
		            						if($serial!=null && count($serial)>0){
		            							foreach ($serial as $i => $serie) {
		            								$serieNumero=$serie->serieNumero;
		            								/**INSERTAMOS EN SERIE**/
		            								$filterSerie= new stdClass();
		            								$filterSerie->SERIP_Codigo=null;
		            								$filterSerie->PROD_Codigo=$valor;
		            								$filterSerie->SERIC_Numero=$serieNumero;
		            								$filterSerie->SERIC_FechaRegistro=date("Y-m-d H:i:s");
		            								$filterSerie->SERIC_FechaModificacion=null;
		            								$filterSerie->SERIC_FlagEstado='1';
		            								$filterSerie->ALMAP_Codigo=$codigoAlmacen;
		            								$codigoSerie=$this->serie_model->insertar($filterSerie);
		            								
		            								
		            								/**insertamso serie documento**/
		            								
		            								/**4:invenmtario**/
		            								$filterSerieD= new stdClass();
		            								$filterSerieD->SERDOC_Codigo=null;
		            								$filterSerieD->SERIP_Codigo=$codigoSerie;
		            								$filterSerieD->DOCUP_Codigo=4;
		            								$filterSerieD->SERDOC_NumeroRef=$datos['cod_inventario'];
		            								/**1:ingreso**/
		            								$filterSerieD->TIPOMOV_Tipo=1;
		            								$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
		            								$filterSerieD->SERDOC_FlagEstado=1;
		            								$this->seriedocumento_model->insertar($filterSerieD);
		            								/**FIN DE INSERTAR EN SERIE**/
		            								 
		            							}
		            						}
		            						break;
		            					}
            						}
            					}
            				}
            			}
            		}
            	}
            	/**fin de insertar serie**/
                $this->cargar_detalle($datos['cod_inventario']);
            }
        }
    }

    public function editar_detalle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $producto_id=$datos['codigoProducto'];
            $codigoAlmacen=$datos['codigoAlmacen'];
            $flagGenInd=$datos['flagGenInd'];
            $result = $this->inventario_model->editar_detalle($datos);
            if ($result)
            	/**gcbq insertar serie de cada producto**/
            	if($flagGenInd='I'){
            		if($producto_id!=null){
            			/**obtenemos las series de session por producto***/
            			$seriesProducto=$this->session->userdata('serieReal');
            			$serieReal = $seriesProducto;
            			if ($seriesProducto!=null && count($seriesProducto) > 0 && $seriesProducto!= "") {
            				/***pongo todos en estado cero de las series asociadas a ese producto**/
            				$seriesProductoBD=$this->session->userdata('serieRealBD');
            				$serieBD = $seriesProductoBD;
            				if($serieBD!=null && count($serieBD)>0){
            					foreach ($serieBD as $almBD => $arrAlmacenBD) {
            						if($almBD==$codigoAlmacen){
            							foreach ($arrAlmacenBD as $ind1BD => $arrserieBD){
		            						if ($ind1BD == $producto_id) {
		            							foreach ($arrserieBD as $keyBD => $valueBD) {
		            								/**cambiamos a ewstado 0**/
		            								$filterSerie= new stdClass();
		            								$filterSerie->SERIC_FlagEstado='0';
		            								$this->serie_model->modificar($valueBD->SERIP_Codigo,$filterSerie);
		            								$filterSerieD= new stdClass();
		            								$filterSerieD->SERDOC_FlagEstado='0';
		            								$this->seriedocumento_model->modificar($valueBD->SERDOC_Codigo,$filterSerieD);
		            							}
		            						}
            							}
            							break;
            						}
            					}
            				}
            				/**fin de poner estado cero**/
            				foreach ($serieReal  as $alm => $arrAlmacen) {
            					if($alm==$codigoAlmacen){
            						foreach ($arrAlmacen as  $ind2 => $arrserie2){
		            					if ($ind2 == $producto_id) {
		            						foreach ($arrserie2 as $i => $serie) {
		            							/**INSERTAMOS EN SERIE**/
		            							$filterSerie= new stdClass();
		            							$filterSerie->PROD_Codigo=$producto_id;
		            							$filterSerie->SERIC_Numero=$serie->serieNumero;
		            							if($serie->serieCodigo!=null && $serie->serieCodigo!=0)
		            								$filterSerie->SERIC_FechaModificacion=date("Y-m-d H:i:s");
		            							else
		            								$filterSerie->SERIC_FechaRegistro=date("Y-m-d H:i:s");
		            									 
		            								$filterSerie->SERIC_FlagEstado='1';
		            								
		            								if($serie->serieCodigo!=null && $serie->serieCodigo!=0){
		            									$this->serie_model->modificar($serie->serieCodigo,$filterSerie);
		            									
		            									$filterSerieD= new stdClass();
		            									$filterSerieD->SERDOC_FlagEstado='1';
		            									$this->seriedocumento_model->modificar($serie->serieDocumentoCodigo,$filterSerieD);
		            								}else{
		            									
		            									
		            									
		            									$filterSerie->SERIC_FlagEstado='1';
		            									$filterSerie->ALMAP_Codigo=$codigoAlmacen;
		            									$codigoSerie=$this->serie_model->insertar($filterSerie);
		            								
		            								
			            								/**insertamso serie documento**/
			            								
			            								/**4:invenmtario**/
			            								$filterSerieD= new stdClass();
			            								$filterSerieD->SERDOC_Codigo=null;
			            								$filterSerieD->SERIP_Codigo=$codigoSerie;
			            								$filterSerieD->DOCUP_Codigo=4;
			            								$filterSerieD->SERDOC_NumeroRef=$datos['cod_inventario'];
			            								/**1:ingreso**/
			            								$filterSerieD->TIPOMOV_Tipo=1;
			            								$filterSerieD->SERDOC_FechaRegistro=date("Y-m-d H:i:s");
			            								$filterSerieD->SERDOC_FlagEstado=1;
			            								$this->seriedocumento_model->insertar($filterSerieD);
			            								/**FIN DE INSERTAR EN SERIE**/
		            								}			 
		            											/**FIN DE INSERTAR EN SERIE**/
		            						}
		            						break;
		            					}
            						}
            						break;
            					}
            				}
            	
            				//if($estado=='2'){
            					/**eliminamos los registros en estado cero 4:inventario**/
            					$this->seriedocumento_model->eliminarEstadoDocumentoSerie(4,$datos['cod_inventario']);
            	
            				//}
            				/**obtenemos en session los nuevos series y lo ponemos en sessionBD**/
            					
            					/**gcbq verificamos si el detalle dee comprobante contiene productos individuales**/
            					$filter=new stdClass();
            					$filter->PROD_Codigo=$producto_id;
            					/**4:inventario**/
            					$filter->DOCUP_Codigo=4;
            					$filter->SERIC_FlagEstado='1';
            					$filter->SERDOC_NumeroRef=$datos['cod_inventario'];
            					$filterSerie->ALMAP_Codigo=$codigoAlmacen;
            					$listaSeriesProducto=$this->seriedocumento_model->buscar($filter,null,null);
            							/**verificamos si es individual**/
            								if($listaSeriesProducto!=null  &&  count($listaSeriesProducto)>0){
            									unset($_SESSION['serieReal'][$codigoAlmacen][$producto_id]);
            									unset($_SESSION['serieRealBD'][$codigoAlmacen][$producto_id]);
            									$reg = array();
            									$regBD = array();
            									foreach($listaSeriesProducto as $serieValor){
            										/**lo ingresamos como se ssion ah 2 variables 1:session que se muestra , 2:sesion que queda intacta bd
            										 * cuando se actualice la session  1 se compra con la session 2.**/
            										$filter = new stdClass();
            										$filter->serieNumero= $serieValor->SERIC_Numero;
            										$filter->serieCodigo= $serieValor->SERIP_Codigo;
            										$filter->serieDocumentoCodigo=$serieValor->SERDOC_Codigo;
            										$reg[] =$filter;
            										$filterBD = new stdClass();
            										$filterBD->SERIC_Numero= $serieValor->SERIC_Numero;
            										$filterBD->SERIP_Codigo= $serieValor->SERIP_Codigo;
            										$filterBD->SERDOC_Codigo=$serieValor->SERDOC_Codigo;
            										$regBD[] =$filterBD;
            									}
            									$_SESSION['serieReal'][$codigoAlmacen][$producto_id] = $reg;
            									$_SESSION['serieRealBD'][$codigoAlmacen][$producto_id] = $regBD;
            								}
            					/**fin de procewso de realizaciom**/
            				/**fin de obtener las series**/		
            			}
            		}
            	}
            	/**fin de insertar serie**/
            	
                $this->cargar_detalle($datos['cod_inventario']);
        }
    }

    public function eliminar_detalle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $producto_id=$datos['codigoProducto'];
            $flagGenInd=$datos['flagGenInd'];
            $result = $this->inventario_model->eliminar_detalle($datos);
            if ($result)
                $this->cargar_detalle($datos['cod_inventario']);
            
            if($flagGenInd=='I'){
            	$this->seriedocumento_model->eliminarEstadoDocumentoProducto(4,$datos['cod_inventario'],$producto_id);
            }
        }
    }

    public function generar_movimiento($cod_detalle, $cod_inventario) {

        #####################################################
        ###### OBTENEMOS LOS DETALLES DEL INGRESO
        #####################################################
        	$cInventario = new stdClass();
        	$cInventario->codigo_detalle = $cod_detalle;
        	$data = $this->inventario_model->buscar_inventario_detalles($cInventario);  	
            
            $cod_inv = $data[0]->INVE_Codigo;
            $prod = $data[0]->PROD_Codigo;
            $flagGenInd = $data[0]->PROD_GenericoIndividual;
    	
        #####################################################
    	###### INSERTAMOS EN EL ALMACEN
        #####################################################
            $cdInventario = new stdClass();
        	$cdInventario->cod_inventario = $data[0]->INVE_Codigo;
        	$datos_inventario = $this->inventario_model->buscar_inventario($cdInventario);
        	#$codigoAlmacenProducto = $this->almacenproducto_model->colocar_stock($datos_inventario[0]->ALMAP_Codigo, $data[0]->PROD_Codigo, $data[0]->INVD_Cantidad,$data[0]->INVD_Pcosto); // Agrega stock
            $codigoAlmacenProducto = $this->almacenproducto_model->aumentar($datos_inventario[0]->ALMAP_Codigo, $data[0]->PROD_Codigo, $data[0]->INVD_Cantidad,$data[0]->INVD_Pcosto); // Suma cantidad ingresada
            $almacen = $codigoAlmacenProducto;

        #####################################################
        ###### CREAMOS LA GUIA DE INGRESO
        #####################################################
        	$cGuiaI = new stdClass();
        	$cGuiaI->TIPOMOVP_Codigo = 2;
        	$cGuiaI->ALMAP_Codigo = $almacen;
        	$cGuiaI->PROVP_Codigo = null;
        	$cGuiaI->DOCUP_Codigo = 4;
        	$cGuiaI->GUIAINC_Fecha = date('Y-m-d h:m:s');
        	$cGuiaI->GUIAINC_Observacion = '';
        	$cGuiaI->USUA_Codigo = $this->somevar['user'];
        	$cGuiaI->GUIAINC_Automatico = 1;
        	$cGuiaI->GUIAINC_NumeroRef = $cod_inv;
        	$guia_id = $this->guiain_model->insertar($cGuiaI);
    	
        #####################################################
        ###### INSERTAMOS EL PRODUCTO EN LA GUIA DE INGRESO
        #####################################################       
        	$cGuiaId = new stdClass();
        	$cGuiaId->GUIAINP_Codigo = $guia_id;
        	$cGuiaId->PRODCTOP_Codigo = $prod;
        	$cGuiaId->ALMAP_Codigo = $almacen;
        	$cGuiaId->UNDMED_Codigo = 1;
        	$cGuiaId->GUIIAINDETC_GenInd = $data[0]->PROD_GenericoIndividual;
        	$cGuiaId->GUIAINDETC_Cantidad = $data[0]->INVD_Cantidad;
        	$cGuiaId->GUIAINDETC_Costo = $data[0]->INVD_Pcosto;
        	$cGuiaId->GUIAINDETC_Descripcion = 'G';
        	$cGuiaId->ALMAP_Codigo = $almacen;
        	$this->guiaindetalle_model->insertar($cGuiaId, false); # false para no ingresar 2 veces al kardex
    	
        #####################################################
        ###### CREAMOS EL LOTE
        #####################################################
            $dLote = new stdClass();
            $dLote->PROD_Codigo = $data[0]->PROD_Codigo;
            $dLote->LOTC_Cantidad = $data[0]->INVD_Cantidad;
            $dLote->LOTC_Costo = $data[0]->INVD_Pcosto;
            $dLote->GUIAINP_Codigo = $guia_id;
            $dLote->LOTC_Numero = $data[0]->LOTC_Numero;
            $dLote->LOTC_FechaVencimiento = $data[0]->LOTC_FechaVencimiento;
            $lote = $this->lote_model->insertar($dLote);
            $this->almaprolote_model->aumentar($almacen, $lote, $data[0]->INVD_Cantidad, $data[0]->INVD_Pcosto);

        #####################################################
        ###### INSERTAMOS EL MOVIMIENTO EN EL KARDEX
        #####################################################
        	$cKardex = new stdClass();
        	$cKardex->KARD_Fecha = date('Y-m-d h:m:s');
        	$cKardex->KARDC_Cantidad = $data[0]->INVD_Cantidad;
        	$cKardex->PROD_Codigo = $data[0]->PROD_Codigo;
        	$cKardex->KARDC_Costo = $data[0]->INVD_Pcosto;
        	$cKardex->KARDC_TipoIngreso = 3;
        	$cKardex->LOTP_Codigo = $lote;
        	$cKardex->TIPOMOVP_Codigo = NULL;
        	$cKardex->KARDC_CodigoDoc = $data[0]->INVE_Codigo;
        	$cKardex->ALMPROD_Codigo = $almacen;
        	$cKardex->KARDP_FlagEstado = 1;
        	$this->kardex_model->insertar(4, $cKardex);
        	
        #####################################################
        ###### CAMBIAMOS EL FLAG DEL MOVIMIENTO GENERADO
        #####################################################
    	$result = $this->inventario_model->editar_detalle_activacion($cod_detalle);
    	
    	if ($result){
            #********************************************************************************************************
            #********** VERIFICAMOS SI EL PRODUCTO ES INDIVIDUAL PARA INGRESAR SERIES. SE DEBE DESARROLLAR NUEVAMENTE
            #********************************************************************************************************
    		if($flagGenInd=='I'){
    			/**obtenemos la serie de ese producto**/
    			$filter=new stdClass();
    			$filter->PROD_Codigo=$prod;
    			/**4:inventario**/
    			$filter->DOCUP_Codigo=4;
    			$filter->SERDOC_NumeroRef=$cod_inv;
    			$datosSerieProducto=$this->seriedocumento_model->buscar($filter,null,null);
    			if($datosSerieProducto!=null && count($datosSerieProducto)>0){
    				/**obtneenmos el productoalmacen **/
    				$datosAlmacenProducto=$this->almacenproducto_model->obtener($almacen,$prod);
    				
    				foreach ($datosSerieProducto as $ind=>$valor){
    					$codigoSerie=$valor->SERIP_Codigo;
    					/**ingresar seriemov**/
    					$filterInsertar=new stdClass();
    					$filterInsertar->SERIP_Codigo=$codigoSerie;
    					/**1:ingreso**/
    					$filterInsertar->SERMOVP_TipoMov=1;
    					$filterInsertar->GUIAINP_Codigo=$guia_id;
    					$filterInsertar->SERMOVC_FechaRegistro=date('Y-m-d h:m:s');
    					$this->seriemov_model->insertar($filterInsertar);
    					/**fin de ingresar serie mov**/
    					/**insertamos en el almacenproductoSerie**/
    					$almacenproducto_id=$datosAlmacenProducto[0]->ALMPROD_Codigo;
    					$this->almacenproductoserie_model->insertar($almacenproducto_id,$codigoSerie);
    					/**fin de ingresar zlmacenProductoSerie**/
    				}
    				
    			}
    		}
            #*************************
            #***** FIN DE VERIFICACION
            #*************************

    		$this->cargar_detalle($cod_inventario);
    	}else{
    		die('ERROR');
    	}
    }

    public function cargar_detalle($cod_inventario, $j = 0) {
        $data['lista'] = array();
        $filter = new stdClass();
        $filter->codigo_inventario = $cod_inventario;
        $c_datos = count($this->inventario_model->buscar_inventario_detalles($filter));

        $conf['base_url'] = site_url('almacen/inventario/cargar_detalle/' + $cod_inventario);
        $conf['per_page'] = 30;
        $conf['num_links'] = 3;
        $conf['next_link'] = "&gt;";
        $conf['prev_link'] = "&lt;";
        $conf['first_link'] = "&lt;&lt;";
        $conf['total_rows'] = $c_datos;
        $conf['last_link'] = "&gt;&gt;";
        $conf['uri_segment'] = 5;
        $offset = (int) $this->uri->segment(5);
        $this->pagination->initialize($conf);
        $data['t_indice'] = $j;
        $datos = $this->inventario_model->buscar_inventario_detalles($filter, $conf['per_page'], $j);
        $data['paginacion'] = $this->pagination->create_links();
        $data['lista'] = $datos;
        
        if($datos!=null && count($datos)>0){
        	
        	foreach ($datos as $indice => $valor) {
        		$GenInd=$valor->PROD_GenericoIndividual;
        		$producto=$valor->PROD_Codigo;
        		$almacen=$valor->ALMAP_Codigo;
        		if ($valor->INVD_FlagActivacion != 1) {
        			/**gcbq verificamos si el detalle dee comprobante contiene productos individuales**/
        			/**verificamos si es individual**/
        			if($GenInd!=null && trim($GenInd)=="I"){
        				
        				/**obtenemos serie de ese producto **/
        				$producto_id=$producto;
        				unset($_SESSION['serieReal'][$almacen][$producto_id]);
        				unset($_SESSION['serieRealBD'][$almacen][$producto_id]);
        				
        				$filterSerie= new stdClass();
        				$filterSerie->PROD_Codigo=$producto_id;
        				$filterSerie->SERIC_FlagEstado='1';
        				/**4:inventario**/
        				$filterSerie->DOCUP_Codigo=4;
        				$filterSerie->SERDOC_NumeroRef=$cod_inventario;
        				$filterSerie->ALMAP_Codigo=$almacen;
        				$listaSeriesProducto=$this->seriedocumento_model->buscar($filterSerie,null,null);
        				if($listaSeriesProducto!=null  &&  count($listaSeriesProducto)>0){
        					$reg = array();
        					$regBD = array();
        					foreach($listaSeriesProducto as $serieValor){
        						/**lo ingresamos como se ssion ah 2 variables 1:session que se muestra , 2:sesion que queda intacta bd
        						 * cuando se actualice la session  1 se compra con la session 2.**/
        						$filter = new stdClass();
        						$filter->serieNumero= $serieValor->SERIC_Numero;
        						$filter->serieCodigo= $serieValor->SERIP_Codigo;
        						$filter->serieDocumentoCodigo=$serieValor->SERDOC_Codigo;
        						$reg[] =$filter;
        			
        			
        						$filterBD = new stdClass();
        						$filterBD->SERIC_Numero= $serieValor->SERIC_Numero;
        						$filterBD->SERIP_Codigo= $serieValor->SERIP_Codigo;
        						$filterBD->SERDOC_Codigo=$serieValor->SERDOC_Codigo;
        						$regBD[] =$filterBD;
        					}
        					$_SESSION['serieReal'][$almacen][$producto_id] = $reg;
        					$_SESSION['serieRealBD'][$almacen][$producto_id] = $regBD;
        				}
        			}
        			/**fin de procewso de realizaciom**/
        			
        			
        		}
        	}
        	
        }
        
        
        
        $this->load->view('almacen/inventario_detalle_refresh', $data);
    }

    public function articulo_importado($idProducto){
        $this->load->model('ventas/importacion_model');

        /*echo json_encode(array(
                'importaciones' => $this->importacion_model->getArticulos($idProducto, $this->somevar['compania'])
            ));*/
    }

    public function encuentrax_producto() {
        $compania = $this->somevar['compania']; //session
        $keyword = $this->input->post('term'); //captura de ajax mando un valor
        $codigoInventario = $this->input->post('codigoInventario'); // igual capturo un valor de ajax
        $filter=new stdClass();
        $filter->nombre=$keyword;
        $filter->codigo = $keyword;
        $filter->flagBS='B';
        $result = array();
        
        if($keyword!=null && count(trim($keyword))>0){
       		//$datosProducto = $this->producto_model->buscar_productos_general($filter,null,null);
            $datosProducto = $this->producto_model->obtenerProductos($keyword, 'B', $compania);

            if(!is_null($datosProducto) && count($datosProducto) > 0){
                foreach ($datosProducto as $indice => $valor) {
                    $cod_prod = $valor->PROD_Codigo;
                    $proGenInd = $valor->PROD_GenericoIndividual;
                    $almacen_id = $this->input->post('almacen');
                    /**gcbq obtener los productos que no se encuentrar registrados en el inventario detalle**/
                    $filterBusqueda = new stdClass();
                    $filterBusqueda->codigo_inventario = $codigoInventario;
                    $filterBusqueda->PROD_Codigo = $cod_prod;
                    $detalleDatos = $this->inventario_model->buscar_inventario_detalles($filterBusqueda,null,null); // ES NULL PARA BUSCAR EL PRODUCTO SIN IMPORTAR QUE YA SE ENCUENTRE INVENTARIADO

                    /**obtenemos los datos de producto inventariado***/
                    $datosExiste = 0;
                    if($detalleDatos != NULL && count($detalleDatos)){
                    /**verificamos si nse encuentra invenmtariado**/
                        foreach ($detalleDatos as $valorDetalle){
                            if($valorDetalle->INVE_Codigo==$codigoInventario){
                                $datosExiste = 1;
                                break;
                            }
                        }
                    }
                    else{
                        $datosExiste = 0;
                    }

                    if($datosExiste == 0){ /**fin de verificacion**/
                        $datosAlmacenProducto = $this->almacenproducto_model->obtener($almacen_id, $cod_prod);
                        $stock = 0;
                        if($datosAlmacenProducto != null && count($datosAlmacenProducto) > 0){
                            $stock = $datosAlmacenProducto[0]->ALMPROD_Stock;
                        }

                        $result[] = array("value" => $valor->PROD_CodigoUsuario . " - " . $valor->PROD_Nombre. " - " . $valor->MARCC_CodigoUsuario, "label" => $valor->PROD_Nombre . " - " . $valor->MARCC_CodigoUsuario, "codigo" => $valor->PROD_Codigo, "codinterno" => $valor->PROD_CodigoUsuario, "flagGenInd" => $proGenInd ,"stock" => $stock);
                    }
                }
            }
        }

        echo json_encode(array('articulos' => $result));
    }
}
?>