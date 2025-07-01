<?php


class Configuracionimpresion extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('util');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('lib_props');
        $this->load->helper('utf_helper');
        $this->load->model('maestros/documento_model_ac');
        $this->load->model('maestros/documento_sentencia_model');
        $this->load->model('maestros/companiaconfiguracion_model');
        $this->load->model('maestros/companiaconfidocumento_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('ventas/comprobante_model');
        $this->load->model('compras/ocompra_model');
        $this->load->model('maestros/unidadmedida_model');


        $this->usuario = $this->session->userdata('user');
        $this->compania = $this->session->userdata('compania');        

        
        
    }
    public function index(){
        
        $this->layout->view('seguridad/inicio');
    }
    
    public function configuracion_index($j=0){
        $this->load->library('layout','layout');
        $codigoCompania=$this->session->userdata('compania');
        $data['titulo_tabla'] = "TIPO DE DOCUMENTO";
        $data['registros'] = count($this->documento_model_ac->listar($codigoCompania));
        $data['titulo_configuracion'] = "CONFIGURACION IMPRESION"; 
        $data['action'] = base_url() . "index.php/maestros/configuracionimpresion/configuracion_index";

        $listado_directivos = $this->documento_model_ac->listar($codigoCompania);
        $item = $j + 1;
        $lista = array();
        if (count($listado_directivos) > 0) {
            foreach ($listado_directivos as $indice => $valor) {
                $codigo = $valor->DOCUP_Codigo;
                $numdoc = $valor->DOCUC_Descripcion;

                $editar = "<a href='javascript:;' onclick='editar_configuracionimpersion(" . $codigo . ")'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[] = array($item, $numdoc, $editar);
                $item++;
            }
        }
        $data['lista'] = $lista;
        $this->layout->view('maestros/configuracionimpresion_index',$data);
    }

    public function nueva_configuracionimpersion($codigoDocumento = null)
    {
        $this->load->library('layout','layout');
        $codigoCompania=$this->session->userdata('compania');
        $data["titulo_configuracioneditar"] = "CONFIGURACION DETALLES DOCUMENTO";
        /**obtenemos detalle  de companiaConfiguracio**/
        //$datosCompaniaConfiguracion=$this->companiaconfiguracion_model->obtener($codigoCompania);
        //$comp_confi=1;
        //$datosCompaniaConfiguracionDoc=$this->companiaconfidocumento_model->obtener($comp_confi, $codigoDocumento);
        $data['posicionGeneralX'] = 0;//$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralX;
        $data['posicionGeneralY'] = 0;//$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralY;
        $data['imagenDocumento']  = 0;//$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Imagen;
        //$codigoCompConfDoc=$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Codigo;
        $data['codigoCompConfDoc']= 0;$codigoCompConfDoc;
        
        /**fin**/
        
        $datos_configuracionimpre = $this->documento_model_ac->obtener_configuracion($codigoDocumento,$codigoCompania);
        $datos_configuracionimpredesafult=$this->documento_model_ac->obtener_configuracion_default($codigoDocumento,$codigoCompania);

        $lista_detalles = array();
        
        /**listado alineamiento**/
        $listadoAlineamiento=array();
        $objetoAl=new stdClass();
        $objetoAl->nombre="LEFT";
        $objetoAl->valor="L";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="CENTER";
        $objetoAl->valor="C";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="RIGHT";
        $objetoAl->valor="R";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="JUSTIFICATION";
        $objetoAl->valor="J";
        $listadoAlineamiento[]=$objetoAl;
        $data['listadoAlineamiento']=$listadoAlineamiento;
        /**fin listado**/
        if ($datos_configuracionimpre) {
            
            foreach ($datos_configuracionimpre as $valor) {

                $compadocumenitem_codigo = $valor->COMPADOCUITEM_Codigo;
                $compacofidocum = $valor->COMPCONFIDOCP_Codigo;
                $documento_codigo = $valor->DOCUP_Codigo;
                $tipo_docu = $valor->DOCUC_Descripcion;
                $item_nom = $valor->COMPADOCUITEM_Nombre;
                $docuitem_wid = $valor->COMPADOCUITEM_Width;
                $docuitem_hei = $valor->COMPADOCUITEM_Height;
                $activacion=$valor->COMPADOCUITEM_Activacion;
                $docuitem_posix = $valor->COMPADOCUITEM_PosicionX;
                $docuitem_posiy = $valor->COMPADOCUITEM_PosicionY;
                $docuitem_tamletra = $valor->COMPADOCUITEM_TamanioLetra;
                $docuitem_tipoletra = $valor->COMPADOCUITEM_TipoLetra;
                $variable = $valor ->COMPADOCUITEM_Variable;
                $perteneceGrupo= $valor->COMPADOCUITEM_VGrupo;
                $alineamiento= $valor->COMPADOCUITEM_Alineamiento;
                
                $objeto = new stdClass();
                $objeto->COMPADOCUITEM_Codigo = $compadocumenitem_codigo;
                $objeto->COMPCONFIDOCP_Codigo = $compacofidocum;
                $objeto->DOCUP_Codigo = $documento_codigo;
                $objeto->DOCUC_Descripcion = $tipo_docu;
                $objeto->ITEM_Nombre = $item_nom;
                $objeto->DOCUITEM_Width = $docuitem_wid;
                $objeto->DOCUITEM_Height = $docuitem_hei;
                $objeto->COMPADOCUITEM_Activacion =$activacion;
                $objeto->DOCUITEM_PosicionX = $docuitem_posix;
                $objeto->DOCUITEM_PosicionY = $docuitem_posiy;
                $objeto->DOCUITEM_TamanioLetra = $docuitem_tamletra;
                $objeto->DOCUITEM_TipoLetra = $docuitem_tipoletra;
                $objeto->DOCUITEM_Variable =$variable;
                $objeto->COMPADOCUITEM_VGrupo =$perteneceGrupo;
                $objeto->COMPADOCUITEM_Alineamiento =$alineamiento;
                $lista[] = $objeto;
                
            }
        }
        else{
            if ($datos_configuracionimpredesafult) {
                foreach ($datos_configuracionimpredesafult as $indice => $valor) {

                $compadocumenitem_codigo = "";
                $compacofidocum = "";
                $documento_codigo = $valor->DOCUP_Codigo;
                $tipo_docu = $valor ->DOCUC_Descripcion;
                $item_nom = $valor ->ITEM_Nombre;
                $docuitem_wid = $valor ->DOCUITEM_Width;
                $docuitem_hei = $valor ->DOCUITEM_Height;
                $docuitem_posix = $valor ->DOCUITEM_PosicionX;
                $docuitem_posiy = $valor ->DOCUITEM_PosicionY;
                $docuitem_tamletra = $valor ->DOCUITEM_TamanioLetra;
                $docuitem_tipoletra = $valor ->DOCUITEM_TipoLetra;
                $variable = $valor ->DOCUITEM_Variable;
                
                $objeto = new stdClass();
                $objeto->COMPADOCUITEM_Codigo = $compadocumenitem_codigo;
                $objeto->COMPCONFIDOCP_Codigo = $compacofidocum;
                $objeto->DOCUP_Codigo = $documento_codigo;
                $objeto->DOCUC_Descripcion = $tipo_docu;
                $objeto->ITEM_Nombre = $item_nom;
                $objeto->DOCUITEM_Width = $docuitem_wid;
                $objeto->DOCUITEM_Height = $docuitem_hei;
                $objeto->DOCUITEM_PosicionX = $docuitem_posix;
                $objeto->DOCUITEM_PosicionY = $docuitem_posiy;
                $objeto->DOCUITEM_TamanioLetra = $docuitem_tamletra;
                $objeto->DOCUITEM_TipoLetra = $docuitem_tipoletra;
                $objeto->DOCUITEM_Variable =$variable;
                $lista[] = $objeto;
                }
            }
        }


        /**obtenemos sentencias guardadas**/
        $datosDocumentoSentencia=$this->documento_sentencia_model->buscar($codigoCompConfDoc);
        if(count($datosDocumentoSentencia)>0){
            $listaSentencia = array();
            foreach ($datosDocumentoSentencia as $indice=>$valor){
                $tipo=$valor->DOCSENT_Tipo;
                $codigoRelacion=$valor->DOCSENT_CodigoRelacion;
                $variableRelacion=$valor->DOCSENT_VariableCodigoRelacion;
                $sentencia=$valor->DOCSENT_Select;
                $sentenciaGrupo=$valor->DOCSENT_VariableGrupo;
                
                /**indice principal***/
                if($tipo==1){
                    $data['sentenciaPrincipal']=$sentencia;
                }else{
                    
                    $objeto = new stdClass();
                    $objeto->tipo=$tipo;
                    $objeto->codigoRelacion=$codigoRelacion;
                    $objeto->variableRelacion=$variableRelacion;
                    $objeto->sentencia=$sentencia;
                    $objeto->sentenciaGrupo=$sentenciaGrupo;
                    $listaSentencia[] = $objeto;
                    $data['listaSentencia'] = $listaSentencia;
                }
                
                
                
                
            }           
        }
        
        
        
        
        /**fin de sentencias**/
        
        
        
        $data['formulario'] = "fmrModificarImpresion";
        $data['url_action'] = base_url() . "index.php/maestros/configuracionimpresion/configuracionimpresion_insertar";
        $data['lista'] = $lista;
        $this->layout->view('maestros/configuracionimpresion_editar',$data);
         
    }
    
    public function configuracionimpersion_editar($codigoDocumento)
    {
        $this->load->library('layout','layout');
        $codigoCompania=$this->session->userdata('compania');
        //$compania = $this->session->userdata('compania');
        $data["titulo_configuracioneditar"] = "CONFIGURACION DETALLES DOCUMENTO";
        /**obtenemos detalle  de companiaConfiguracio**/
        $datosCompaniaConfiguracion=$this->companiaconfiguracion_model->obtener($codigoCompania);
        $comp_confi=$datosCompaniaConfiguracion[0]->COMPCONFIP_Codigo;
        $datosCompaniaConfiguracionDoc=$this->companiaconfidocumento_model->obtener($comp_confi, $codigoDocumento);
        $data['posicionGeneralX'] = $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralX;
        $data['posicionGeneralY'] = $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralY;
        $data['imagenDocumento']  = $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Imagen;
        $codigoCompConfDoc=$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Codigo;
        $data['codigoCompConfDoc']=$codigoCompConfDoc;

        $data['companiadato']=$codigoCompania;
        /**fin**/
        
        $datos_configuracionimpre = $this->documento_model_ac->obtener_configuracion($codigoDocumento,$codigoCompania);
        $datos_configuracionimpredesafult=$this->documento_model_ac->obtener_configuracion_default($codigoDocumento,$codigoCompania);

        $lista_detalles = array();
        
        /**listado alineamiento**/
        $listadoAlineamiento=array();
        $objetoAl=new stdClass();
        $objetoAl->nombre="LEFT";
        $objetoAl->valor="L";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="CENTER";
        $objetoAl->valor="C";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="RIGHT";
        $objetoAl->valor="R";
        $listadoAlineamiento[]=$objetoAl;
        
        $objetoAl=new stdClass();
        $objetoAl->nombre="JUSTIFICATION";
        $objetoAl->valor="J";
        $listadoAlineamiento[]=$objetoAl;
        $data['listadoAlineamiento']=$listadoAlineamiento;
        /**fin listado**/
        if ($datos_configuracionimpre) {
            
            foreach ($datos_configuracionimpre as $valor) {

                $compadocumenitem_codigo = $valor->COMPADOCUITEM_Codigo;
                $compacofidocum = $valor->COMPCONFIDOCP_Codigo;
                $documento_codigo = $valor->DOCUP_Codigo;
                $tipo_docu = $valor->DOCUC_Descripcion;
                $item_nom = $valor->COMPADOCUITEM_Nombre;
                $docuitem_wid = $valor->COMPADOCUITEM_Width;
                $docuitem_hei = $valor->COMPADOCUITEM_Height;
                $activacion=$valor->COMPADOCUITEM_Activacion;
                $docuitem_posix = $valor->COMPADOCUITEM_PosicionX;
                $docuitem_posiy = $valor->COMPADOCUITEM_PosicionY;
                $docuitem_tamletra = $valor->COMPADOCUITEM_TamanioLetra;
                $docuitem_tipoletra = $valor->COMPADOCUITEM_TipoLetra;
                $variable = $valor ->COMPADOCUITEM_Variable;
                $perteneceGrupo= $valor->COMPADOCUITEM_VGrupo;
                $alineamiento= $valor->COMPADOCUITEM_Alineamiento;
                
                $objeto = new stdClass();
                $objeto->COMPADOCUITEM_Codigo = $compadocumenitem_codigo;
                $objeto->COMPCONFIDOCP_Codigo = $compacofidocum;
                $objeto->DOCUP_Codigo = $documento_codigo;
                $objeto->DOCUC_Descripcion = $tipo_docu;
                $objeto->ITEM_Nombre = $item_nom;
                $objeto->DOCUITEM_Width = $docuitem_wid;
                $objeto->DOCUITEM_Height = $docuitem_hei;
                $objeto->COMPADOCUITEM_Activacion =$activacion;
                $objeto->DOCUITEM_PosicionX = $docuitem_posix;
                $objeto->DOCUITEM_PosicionY = $docuitem_posiy;
                $objeto->DOCUITEM_TamanioLetra = $docuitem_tamletra;
                $objeto->DOCUITEM_TipoLetra = $docuitem_tipoletra;
                $objeto->DOCUITEM_Variable =$variable;
                $objeto->COMPADOCUITEM_VGrupo =$perteneceGrupo;
                $objeto->COMPADOCUITEM_Alineamiento =$alineamiento;
                $lista[] = $objeto;
                
            }
        }
        else{
            if ($datos_configuracionimpredesafult) {
                foreach ($datos_configuracionimpredesafult as $indice => $valor) {

                $compadocumenitem_codigo = "";
                $compacofidocum = "";
                $documento_codigo = $valor->DOCUP_Codigo;
                $tipo_docu = $valor ->DOCUC_Descripcion;
                $item_nom = $valor ->ITEM_Nombre;
                $docuitem_wid = $valor ->DOCUITEM_Width;
                $docuitem_hei = $valor ->DOCUITEM_Height;
                $docuitem_posix = $valor ->DOCUITEM_PosicionX;
                $docuitem_posiy = $valor ->DOCUITEM_PosicionY;
                $docuitem_tamletra = $valor ->DOCUITEM_TamanioLetra;
                $docuitem_tipoletra = $valor ->DOCUITEM_TipoLetra;
                $variable = $valor ->DOCUITEM_Variable;
                
                $objeto = new stdClass();
                $objeto->COMPADOCUITEM_Codigo = $compadocumenitem_codigo;
                $objeto->COMPCONFIDOCP_Codigo = $compacofidocum;
                $objeto->DOCUP_Codigo = $documento_codigo;
                $objeto->DOCUC_Descripcion = $tipo_docu;
                $objeto->ITEM_Nombre = $item_nom;
                $objeto->DOCUITEM_Width = $docuitem_wid;
                $objeto->DOCUITEM_Height = $docuitem_hei;
                $objeto->DOCUITEM_PosicionX = $docuitem_posix;
                $objeto->DOCUITEM_PosicionY = $docuitem_posiy;
                $objeto->DOCUITEM_TamanioLetra = $docuitem_tamletra;
                $objeto->DOCUITEM_TipoLetra = $docuitem_tipoletra;
                $objeto->DOCUITEM_Variable =$variable;
                $lista[] = $objeto;
                }
            }
        }


        /**obtenemos sentencias guardadas**/
        $datosDocumentoSentencia=$this->documento_sentencia_model->buscar($codigoCompConfDoc);
        if(count($datosDocumentoSentencia)>0){
            $listaSentencia = array();
            foreach ($datosDocumentoSentencia as $indice=>$valor){
                $tipo=$valor->DOCSENT_Tipo;
                $codigoRelacion=$valor->DOCSENT_CodigoRelacion;
                $variableRelacion=$valor->DOCSENT_VariableCodigoRelacion;
                $sentencia=$valor->DOCSENT_Select;
                $sentenciaGrupo=$valor->DOCSENT_VariableGrupo;
                
                /**indice principal***/
                if($tipo==1){
                    $data['sentenciaPrincipal']=$sentencia;
                }else{
                    
                    $objeto = new stdClass();
                    $objeto->tipo=$tipo;
                    $objeto->codigoRelacion=$codigoRelacion;
                    $objeto->variableRelacion=$variableRelacion;
                    $objeto->sentencia=$sentencia;
                    $objeto->sentenciaGrupo=$sentenciaGrupo;
                    $listaSentencia[] = $objeto;
                    $data['listaSentencia'] = $listaSentencia;
                }
                
                
                
                
            }           
        }
        
        
        
        
        /**fin de sentencias**/
        
        
        
        $data['formulario'] = "fmrModificarImpresion";
        $data['url_action'] = base_url() . "index.php/maestros/configuracionimpresion/configuracionimpresion_insertar";
        $data['lista'] = $lista;
        $this->layout->view('maestros/configuracionimpresion_editar',$data);
         
    }
    
    public function configuracionimpresion_insertar(){
        $this->load->library('layout','layout');

        $companiadato= $this->input->post('companiadato');
        $codigoCompConfDoc= $this->input->post('codigoCompConfDoc');

        $docuid = $this->input->post('documentoid');

        $compadocumenitem_codigo_array = $this->input->post('compadocumenitem_codigo');

        $compaconfi = $this->input->post('compacofidocum');
        $item_nom = $this->input->post('item_nom');
        $tipo_docu = $this->input->post('tipo_docu');

        $campw = $this->input->post('campodo_width');
        $camph = $this->input->post('campodo_height');
        $posx = $this->input->post('campodo_posx');
        $posy = $this->input->post('campodo_posy');
        $taml = $this->input->post('campodo_tamletra');
        $tipol = $this->input->post('campodo_tipoletra');
        $variable = $this->input->post('variable');
        $perteneceGrupo = $this->input->post('grupo');
        $alineamiento= $this->input->post('alineamiento');
        $activacion= $this->input->post('activacion');
        
        if (is_array($docuid)) {
            foreach ($docuid as $indice => $value) {

            $compadocumenitem_codigo = $compadocumenitem_codigo_array[$indice];

            $filter = new stdClass();
            
            $filter->COMPADOCUITEM_UsuCrea = $this->session->userdata('nombre_persona');
            $filter->COMPADOCUITEM_FechaIng = date("Y-m-d H:i:s");

            $filter->COMPADOCUITEM_Descripcion = "";
            $filter->COMPADOCUITEM_Abreviatura = "";
            $filter->COMPADOCUITEM_Valor = "";
            $filter->COMPADOCUITEM_Estado = "1";
            $filter->COMPADOCUITEM_Variable = "";
            $filter->COMPADOCUITEM_Activacion = "";
            $filter->COMPADOCUITEM_UsuModi = "";
            $filter->COMPADOCUITEM_FechaModi = "";
            $filter->COMPADOCUITEM_Nombre = $item_nom[$indice];
            

            $filter->DOCUITEM_Codigo = $docuid[$indice];
            $filter->COMPCONFIDOCP_Codigo = $compaconfi[$indice];
            $filter->COMPADOCUITEM_Width = $campw[$indice];
            $filter->COMPADOCUITEM_Height = $camph[$indice];
            $filter->COMPADOCUITEM_Activacion= isset($activacion[$indice])?$activacion[$indice]:0;
            $filter->COMPADOCUITEM_PosicionX = $posx[$indice];
            $filter->COMPADOCUITEM_PosicionY = $posy[$indice];
            $filter->COMPADOCUITEM_TamanioLetra = $taml[$indice];
            $filter->COMPADOCUITEM_TipoLetra = $tipol[$indice];
            $filter->COMPADOCUITEM_Variable = $variable[$indice];
            $filter->COMPADOCUITEM_VGrupo = $perteneceGrupo[$indice];
            $filter->COMPADOCUITEM_Alineamiento =$alineamiento[$indice];
            
                if ($compadocumenitem_codigo!="" && $compadocumenitem_codigo!=NULL) {
                    $this->documento_model_ac->modificar_configuracion($filter,$compadocumenitem_codigo,$codigoCompConfDoc);
                }
                else{
                    $this->documento_model_ac->insertar_configuracion($filter);
                }
            }
            
            
            /**datos cabecera**/
           
            $posicionGeneralY= $this->input->post('posicionGeneralY');
            $posicionGeneralX = $this->input->post('posicionGeneralX');
           
            

            $imagen="";
            $config = array();
            $config['upload_path'] = 'images/documentos/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']      = '5120';
            $this->load->library('upload');
            $files = $_FILES;
            
            for($i=0; $i< count($_FILES['files']['name']); $i++){
                
                $_FILES['files']['name']= $files['files']['name'][$i];
                if( $_FILES['files']['name']!="")
                    {
                    $_FILES['files']['type']= $files['files']['type'][$i];
                    $_FILES['files']['tmp_name']= $files['files']['tmp_name'][$i];
                    $_FILES['files']['error']= $files['files']['error'][$i];
                    $_FILES['files']['size']= $files['files']['size'][$i];            
                    $this->upload->initialize($config);
                    $this->upload->do_upload('files');
                    $imagen=$_FILES['files']['name'];
                    
                    /**eliminamos la imagen anterior**/
                    $imagenAnteriorNombre= $this->input->post('imagenAnteriorNombre');
                    $file = 'images/documentos/' . $imagenAnteriorNombre;
                    unlink($file);
                }
            }
            
            $filter=new stdClass();
            $filter->COMPCONFIDOCP_PosicionGeneralX=$posicionGeneralX;
            $filter->COMPCONFIDOCP_PosicionGeneralY=$posicionGeneralY;
            if($imagen!=null && trim($imagen)!="")
                $filter->COMPCONFIDOCP_Imagen=$imagen;
            
            $this->companiaconfidocumento_model->modificar($codigoCompConfDoc,$companiadato,$filter);
            /**fin**/
            
            /**insertamos documento sentenia**/
            $sentencia=$this->input->post('sentencia');
            $tipoSentencia=$this->input->post('tipoSentencia');
            $vCodigoRelacionSentencia=$this->input->post('vCodigoRelacionSentencia');
            $codigoRelacionSentencia=$this->input->post('codigoRelacionSentencia');
            $sentenciaGrupo=$this->input->post('sentenciaGrupo');
            
            if(count($sentencia)>0 && trim($sentencia[0])!=""){
                $this->documento_sentencia_model->eliminar_configuracion($codigoCompConfDoc);
                foreach ($sentencia as $i=>$valor){
                    
                    if(trim($valor)!=""){
                        $valorTipoSentencia=$tipoSentencia[$i];
                        $valorCodigoRe=$codigoRelacionSentencia[$i];
                        $filterDS=new stdClass();
                        $filterDS->DOCSENT_Tipo=$valorTipoSentencia;
                        $filterDS->DOCSENT_Select=$valor;
                        $filterDS->DOCSENT_CodigoRelacion=$valorCodigoRe;
                        $filterDS->COMPCONFIDOCP_Codigo=$codigoCompConfDoc;
                        $filterDS->DOCSENT_VariableCodigoRelacion=$vCodigoRelacionSentencia[$i];
                        $filterDS->DOCSENT_VariableGrupo=$sentenciaGrupo[$i];
                        if($valorTipoSentencia==2){
                            if(trim($valorCodigoRe)!=""){
                                $this->documento_sentencia_model->insertar($filterDS);
                            }
                            
                        }else{
                            $this->documento_sentencia_model->insertar($filterDS);
                        }
                    }
                }
            
            }
            
            /**fin de insertar**/
        }
        redirect('maestros/configuracionimpresion/configuracion_index','refresh');
    }
   
    
    public function  verificarSentenciaVariable(){
        $sentencia = $this->input->post('sentenciaReal');
        $listaVariables=$this->documento_sentencia_model->validarSentecia($sentencia);
        $lista_detalles = array();
        if(count($listaVariables)>0){
            foreach ($listaVariables as $indice=>$valor){
                $objeto=new stdClass();
                $objeto->variableReal=$valor->name;
                $lista_detalles[] = $objeto;
            }
        }
        $resultado = json_encode($lista_detalles);
        echo $resultado;
    }
    
    
    
    
    
    /**metodo de impresion
     * @param int $CodigoPrincipal (relacionada con la variable principal)
     * @param int $imagen***/
    public function impresionDocumento($CodigoPrincipal,$codigoDocumento,$isImagen,$ventaCompra){
        $codigoCompania=$this->session->userdata('compania');
        /**obtenemos detalle  de companiaConfiguracio**/
        $datosCompaniaConfiguracion=$this->companiaconfiguracion_model->obtener($codigoCompania);
        $comp_confi=$datosCompaniaConfiguracion[0]->COMPCONFIP_Codigo;
        
        /***cabecaer documento**/
        $datosCompaniaConfiguracionDoc=$this->companiaconfidocumento_model->obtener($comp_confi, $codigoDocumento);
        $posicionGeneralX = $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralX;
        $posicionGeneralY = $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_PosicionGeneralY;
        if ($ventaCompra=="V") {
            $imagenDocumento =  $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Imagen;
        } else {
            $imagenDocumento =  $datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_ImagenCompra;
        }
        
        
        $codigoCompConfDoc=$datosCompaniaConfiguracionDoc[0]->COMPCONFIDOCP_Codigo;
        
         
        /**fin**/
        
        /***sentencia por documento**/
        $datosDocumentoSentencia=$this->documento_sentencia_model->buscar($codigoCompConfDoc);
        /**fin**/
        if(count($datosDocumentoSentencia)>0){
            $ListaDatosSentencia = array();
            $ListaDatosSentenciaGrupo= array();
            foreach ($datosDocumentoSentencia as $i=>$valor){
                $tipo=$valor->DOCSENT_Tipo;
                $codigoRelacion=$valor->DOCSENT_CodigoRelacion;
                $variableRelacion=$valor->DOCSENT_VariableCodigoRelacion;
                $sentencia=$valor->DOCSENT_Select;
                $grupoVariable=$valor->DOCSENT_VariableGrupo;
                if($tipo==1){
                    /**reemplazamos los valores para se ejecute la sentencia**/
                    $sentencia=str_replace($variableRelacion, $CodigoPrincipal, $sentencia);
                    //echo $sentencia;
                    /**ejecutamos la sentencia realizada**/
                    $datosSentencia=$this->documento_sentencia_model->ejecutarSentencia($sentencia);
                    $ListaDatosSentencia[]=$datosSentencia;
                }else{
                    $CodigoSecundario="";
                    /**buscamos la variable asociada a la principal y capturamos el codigo**/
                        foreach ($ListaDatosSentencia as $objeto){
                           // print_r($objeto);
                           IF(COUNT($objeto)>0){
                            foreach ($objeto as $valorVariable){
                                if(isset($valorVariable->$codigoRelacion)){
                                    $CodigoSecundario=$valorVariable->$codigoRelacion;
                                    break;
                                }
                            }
                           }
                        }
                    /**buscamos en principal y en los demas Datos si existe**/
                    $sentencia=str_replace($variableRelacion, $CodigoSecundario, $sentencia);
                    //echo "___________";                    echo $sentencia;
                    /**ejecutamos la sentencia realizada**/
                    $datosSentencia=$this->documento_sentencia_model->ejecutarSentencia($sentencia);
                    $ListaDatosSentencia[]=$datosSentencia;
                    
                    if(trim($grupoVariable)!=""){
                        $datosSentencia=$this->documento_sentencia_model->ejecutarSentencia($sentencia);
                        $ListaDatosSentenciaGrupo[$grupoVariable]=$datosSentencia;
                    }else{
                        $datosSentencia=$this->documento_sentencia_model->ejecutarSentencia($sentencia);
                        $ListaDatosSentencia[]=$datosSentencia;
                    }
                    
                }
            }
        }
        
        /**fin**
        /**detalles de impresionn**/
        $datos_configuracionimpre = $this->documento_model_ac->obtener_configuracion($codigoDocumento,$codigoCompania);
        $nombreArchivoIMG="images/documentos/".$imagenDocumento;

        if(count($datos_configuracionimpre)>0 && file_exists($nombreArchivoIMG)){
            
            
            $this->load->library('fpdf/fpdf');
            $pdf = new FPDF('P','mm','A4');
            $pdf->AliasNbPages();
            $pdf->AddPage();
            /**tama? de la imagen es de A4**/
            IF($isImagen==1)         
            $pdf->Image($nombreArchivoIMG, '0', '0','210','297','JPG');
            
            foreach ($datos_configuracionimpre as $key=>$valor){
            
                $item_nom = $valor->COMPADOCUITEM_Nombre;
                $docuitem_wid = $this->convertirMm($valor->COMPADOCUITEM_Width);
                $docuitem_hei =$this->convertirMm( $valor->COMPADOCUITEM_Height);
                $docuitem_posix =$this->convertirMm( $valor->COMPADOCUITEM_PosicionX);
                $docuitem_posiy =$this->convertirMm( $valor->COMPADOCUITEM_PosicionY);
                $docuitem_tamletra = $valor->COMPADOCUITEM_TamanioLetra;
                $docuitem_tipoletra = $valor->COMPADOCUITEM_TipoLetra;
                $variable = $valor->COMPADOCUITEM_Variable;
                $alineamiento=$valor->COMPADOCUITEM_Alineamiento;
                $activacion=$valor->COMPADOCUITEM_Activacion;
                //capturo la condicion de letra o numero
                $numeroEnLetra=$valor->COMPADOCUITEM_Convertiraletras;
                //**************************************
                $isListado= $valor->COMPADOCUITEM_Listado;
                $perteneceGrupo= $valor->COMPADOCUITEM_VGrupo;
                
                $valorVariableMostrar="";


                if($activacion!=1 && trim($activacion)!="1"){
                    /**verificamos si existe detallles **/
                    if(trim($perteneceGrupo)!="" && trim($perteneceGrupo)!="0"){
                        if(count($ListaDatosSentenciaGrupo)>0){
                            /**obtenemos datos de la lista detalle y lo pintamos**/
                            foreach ($ListaDatosSentenciaGrupo[$perteneceGrupo] as $valorArray){
                                if(isset($valorArray->$variable) && $valorArray->$variable!=null && trim($valorArray->$variable)!=""){
                                    $valorVariableMostrar=$valorArray->$variable;

                                    $pdf->SetFont('Arial', '', $docuitem_tamletra);
                                    $pdf->SetY($docuitem_posiy);
                                    $pdf->SetX($docuitem_posix);
                                    $pdf->MultiCell($docuitem_wid,$docuitem_hei, utf8_decode(mb_strtoupper($valorVariableMostrar)),0,$alineamiento);
                                    $docuitem_posiy=$docuitem_posiy+6;
                                }
                            }
                         
                        }
                    }else{
                        
                        foreach ($ListaDatosSentencia as $objeto){
                            if ($objeto!=NULL && count($objeto)>0 && isset($objeto)) {
                                foreach ($objeto AS $valorVariable){
                                    //var_dump($valorVariable);
                                    if(isset($valorVariable->$variable)){
                                        $valorVariableMostrar=$valorVariable->$variable;
                                        break;
                                    }
                                }
                            }
                            
                        }
                        

                        if ($numeroEnLetra==1) {
                        $buscar_moneda="";#$this->companiaconfiguracion_model->get_monedalist($CodigoPrincipal,$codigoDocumento);
                        $valorVariableMostrar="";#num2letras(round($valorVariableMostrar, 2)).'     '.$buscar_moneda[0]->MONED_Descripcion;

                        }
                        elseif($numeroEnLetra==2){

                            $valorVariableMostrar=mes_textual(round($valorVariableMostrar, 2));
                           // print_r($valorVariableMostrar)   ; 

                        }
                        $pdf->SetFont('Arial', '', $docuitem_tamletra);
                        $pdf->SetY($docuitem_posiy);
                        $pdf->SetX($docuitem_posix);
                        $pdf->MultiCell($docuitem_wid,$docuitem_hei, utf8_decode(mb_strtoupper($valorVariableMostrar)),0,$alineamiento);

                        
                    }
                }
                
                
                
                
                
                
            }
            $archivo = "temporal/Impresion.pdf";
            $pdf->Output('I', $archivo);
            
        }
        else
        {
            echo "</br>No se puede mostrar el PDF ya que no se encontr?el archivo que contiene la imagen, asegurese de haber cargado correctamente la imagen en: mantenimiento/configuracion impresión.</br>Si el inconveniente persiste comuniquese con el administrador.";
        }
        /***fin */
        
    }

    public function impresionDocumentoComprobante($CodigoPrincipal,$codigoDocumento,$isImagen,$ventaCompra){
            $codigoCompania=$this->session->userdata('compania');
            $hoy = date("Y-m-d");
            include("system/application/libraries/pchart/pData.php");
            include("system/application/libraries/pchart/pChart.php");
            include("system/application/libraries/cezpdf.php");
            include("system/application/libraries/class.backgroundpdf.php");
            include("system/application/libraries/lib_fecha_letras.php");
            //include("system/application/controller/maestros/configuracionimpresion");
            // DISPARADOR END
            //**************************************************************************
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($CodigoPrincipal);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $datos_cliente = $this->comprobante_model->obtener_cliente($datos_comprobante[0]->CLIP_Codigo);
                if (count($datos_cliente)>0) {
                    $cliente_rs = $datos_cliente[0]->EMPRC_RazonSocial;
                    $cliente_doc = $datos_cliente[0]->EMPRC_Ruc;
                    $cliente_direc = $datos_cliente[0]->EMPRC_Direccion;

                }else{
                    $datos_cliente = $this->comprobante_model->obtener_cliente_dni($datos_comprobante[0]->CLIP_Codigo);
                    $cliente_rs = $datos_cliente[0]->PERSC_Nombre." ".$datos_cliente[0]->PERSC_ApellidoPaterno." ".$datos_cliente[0]->PERSC_ApellidoMaterno;
                    $cliente_doc = $datos_cliente[0]->PERSC_NumeroDocIdentidad;
                    $cliente_direc = $datos_cliente[0]->PERSC_Direccion;
                }
                $ocompra = $this->comprobante_model->buscar_ocompra($datos_comprobante[0]->OCOMP_Codigo);
                $proyecto = $this->comprobante_model->buscar_proyecto($datos_comprobante[0]->PROYP_Codigo);
                if (count($proyecto)>0) {

                    $nombre_proyecto = $proyecto[0]->PROYC_Nombre;
                    $direccion = $this->comprobante_model->buscar_direccion_proyecto($datos_comprobante[0]->PROYP_Codigo);
                    if(count($direccion)>0){
                    $direccion_proyecto = $direccion[0]->DIRECC_Descrip;
                    }else{
                        $direccion_proyecto = "";
                    }

                }else{
                    $nombre_proyecto = "";
                    $direccion_proyecto = "";
                }
                /*
                if (count($ocompra)>0) {
                    $serie_ocompra = $ocompra[0]->OCOMC_Serie;
                    $numero_ocompra = $ocompra[0]->OCOMC_Numero;
                    $oc = $this->validar_formato_serie($serie_ocompra,$numero_ocompra);
                }else{
                    $numero_ocompra = $datos_comprobante[0]->CPP_Compracliente;
                    $oc = $this->validar_formato_serie(1,$numero_ocompra);

                }
                */
                $numero_ocompra = $datos_comprobante[0]->CPP_Compracliente;
                $oc = $this->validar_formato_serie(1,$numero_ocompra);


                $numero_factura = $this->validar_formato_serie($serie,$numero);

                $dguiarem = $this->comprobante_model->buscar_guiarem_comprobante($CodigoPrincipal);

                if (count($dguiarem)>0) {
                    $gserie = $dguiarem[0]->GUIAREMC_Serie;
                    $gnumero = $dguiarem[0]->GUIAREMC_Numero;

                    $numero_guia = $this->validar_formato_serie($gserie,$gnumero);
                }else{
                    $numero_guia = "";
                }

                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $datos_comprobante[0]->CPC_total;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                /*
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                */
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    //$list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    //$guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    //$guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                /*
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                */
                /*
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion;
                */
                $image = "images/img_db/comprobante".$codigoCompania.".jpg";
                $array_fecha = explode("/", $fecha);
                $TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);
                $detalle_comprobante = $this->obtener_lista_detalles($CodigoPrincipal);
                //$this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/documentos/garantia.jpg'));
                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => $image));
                /* Cabecera */
                /*
                $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
                $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));
                */
         

                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
                #$this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
                /*:::::::::::::: PRIMERA FILA :::::::::::::::::::::: */
                //RAZON SOCIAL
                $this->cezpdf->ezText("N?".$numero_factura, 10, array("leading" => 160, "left" => 430));
                $this->cezpdf->ezText($cliente_rs, 7, array("leading" => 23, "left" => 90));
                $this->cezpdf->ezText(utf8_decode_seguro((int)substr($fecha, 0, 2)), 9, array("leading" => 0, "left" => 430));
                $this->cezpdf->ezText(utf8_decode_seguro(mes_textual(substr($fecha, 3, 2))), 9, array("leading" => 0, "left" => 450));
                $this->cezpdf->ezText(utf8_decode_seguro(substr($fecha, 6, 4)), 9, array("leading" => 0, "left" => 480));
                /*:::::::::::::::: SEGUNDA FILA ::::::::::::::::*/
                //RUC CLIENTE
                $this->cezpdf->ezText($cliente_doc, 9, array("leading" => 15, "left" => 75));
                //DIRECCION CLIENTE
                $this->cezpdf->ezText($cliente_direc, 7, array("leading" => 15, "left" => 100));
                //$this->cezpdf->ezText($nombre_proyecto, 8, array("leading" => 15, "left" => 105));
                //$this->cezpdf->ezText($direccion_proyecto, 8, array("leading" => 13, "left" => 175));
                
                $this->cezpdf->ezText($numero_guia, 10, array("leading" => 14, "left" => 150));

                $this->cezpdf->ezText("CODIGO", 9, array("leading" => 14, "left" => 50));
                $this->cezpdf->ezText("DESCRIPCION", 9, array("leading" => 0, "left" => 200));
                $this->cezpdf->ezText("CANT", 9, array("leading" => 0, "left" => 400));
                $this->cezpdf->ezText("PU", 9, array("leading" => 0, "left" => 450));
                $this->cezpdf->ezText("IMPORTE", 9, array("leading" => 0, "left" => 490));
                $this->cezpdf->ezText("TOTAL ", 10, array("leading" => 440, "left" => 400));
                $this->cezpdf->ezText("S/ ".number_format($total,2), 10, array("leading" => 0, "left" => 470));

                /* Listado de detalles */
                $posicionX = 200;
                $posicionY = 550;
                $db_data = array();
                
                foreach ($detalle_comprobante as $indice => $valor) {
                    $c = 0;
                    $array_producto = explode('/', $valor->PROD_Nombre);
                    $producto = $valor->PROD_CodigoUsuario;

            //                $ser = "";
            //                $datos_serie = $this->seriemov_model->buscar_x_guiainp($guiainp_codigo, $producto);

                    $posicionX = 80;
                    if ($valor->CPDEC_Pu_ConIgv != '')
                        $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                    else
                        $pu_conigv = $valor->CPDEC_Pu + $valor->CPDEC_Pu * $valor->CPDEC_Igv100 / 100;

                    $importe_unit = $pu_conigv*$valor->CPDEC_Cantidad;
                    $posicionX += 30;
                    $this->cezpdf->addText($posicionX - 30, $posicionY, 9, $producto);
                    $posicionX += 30;
                    $this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($valor->PROD_Nombre));
                #$this->cezpdf->addText($posicionX, $posicionY, 8, utf8_decode_seguro($array_producto[0]));
                #$this->cezpdf->addText(120, $i, 8, utf8_decode_seguro($valor->PROD_Nombre));
                    $posicionX += 300;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $valor->CPDEC_Cantidad);
                    $posicionX += 40;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, $pu_conigv);
                    $posicionX += 40;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, number_format($importe_unit,2));
                    /*
                    $posicionX += 35;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, 'S/'. ' ' . number_format($pu_conigv, 2));
                    $posicionX += 55;
                    $this->cezpdf->addText($posicionX, $posicionY, 9, 'S/' . ' ' . number_format($valor->CPDEC_Total, 2));
                    */

                    //
                    //                if (count($datos_serie) > 0) {
                    //                    $this->cezpdf->addText(40, $posicionY - 15, 9, "Series: ");
                    //                    for ($i = 0; $i < count($datos_serie); $i++) {
                    //                        $c = $c + 1;
                    //                        $seriecodigo = $datos_serie[$i]->SERIC_Numero;
                    //
                    //                        $ser = $ser . " /" . $seriecodigo;
                    //
                    //                        $this->cezpdf->addText(70, $posicionY - 15, 9, "" . $ser);
                    //                        if ($c == 8) {
                    //                            $posicionY-=10;
                    //                            $c = 0;
                    //                            $ser = "";
                    //                        }
                    //                    }
                    //                }
                    $posicionY -= 10;
                }

                
                /* Totales */
                /*
                $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" v�lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                
                $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

                $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));

                $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
                */
                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);
        


            //**************************************************************************


        
    }

    public function impresionDocumentoCompromiso($codigo){   
        $datos_comprobante = $this->comprobante_model->obtener_comprobante($codigo);
        $this->load->model("maestros/moneda_model");
        $this->load->model("maestros/formapago_model");
        $this->load->model("empresa/cliente_model");
        $this->load->model("empresa/proveedor_model");

        // DATOS DEL COMPROBANTE
        $companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
        $presupuesto = $datos_comprobante[0]->PRESUP_Codigo;
        $tipo_oper = $datos_comprobante[0]->CPC_TipoOperacion;
        $serie = $datos_comprobante[0]->CPC_Serie;
        $numero = $datos_comprobante[0]->CPC_Numero;
        $descuento_conigv = $datos_comprobante[0]->CPC_descuento_conigv;
        $descuento100 = $datos_comprobante[0]->CPC_descuento100;
        $descuento = $datos_comprobante[0]->CPC_descuento;
        $igv = $datos_comprobante[0]->CPC_igv;
        $igv100 = $datos_comprobante[0]->CPC_igv100;
        $subtotal = $datos_comprobante[0]->CPC_subtotal;
        $subtotal_conigv = $datos_comprobante[0]->CPC_subtotal_conigv;
        $total = $datos_comprobante[0]->CPC_total;
        $observacion = $datos_comprobante[0]->CPC_Observacion;
        $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
        $tipo_docu = $datos_comprobante[0]->CPC_TipoDocumento;
        $estado = $datos_comprobante[0]->CPC_FlagEstado;

        // REFERENCIAS
        $numero_factura = $this->validar_formato_serie($tipo_docu."PP".$companiaComprobante,$numero);
        $numero_ocompra = $datos_comprobante[0]->CPP_Compracliente;
        $oc = ($numero_ocompra != "" && $numero_ocompra != NULL) ? $this->validar_formato_serie(1,$numero_ocompra) : "";
            // CONSULTO SI TIENE GUIA DE REMISION Y LAS CONCATENO
        $consulta_guia = $this->comprobante_model->buscar_guiarem_comprobante($codigo);
        $guiaRemision = "";
        $jump = 1;
        foreach ($consulta_guia as $key => $value) {
            $guiaRemision .= "$value->GUIAREMC_Serie - $value->GUIAREMC_Numero";
            if ($jump > 1)
                $guiaRemision .= "<br>";
            $jump++;
        }

        // DATOS DE MONEDAS
        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        /*FORMA DE PAGO*/
        $formapago_desc = "EFECTIVO";
        $formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        $datos_formapago = $this->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS

        // FORMATO DE FECHA
        $nFecha = explode( '/', $fecha );
        $fecha = $nFecha[0]." DE ".$this->lib_props->mesesEs($nFecha[1])." DEL ".$nFecha[2];
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
 
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $ruc_cliente = ($ruc_cliente=="") ? $dni_cliente : $ruc_cliente ;
                $direccion   = $datos_cliente->direccion;
            }
        }
        else
            if ($proveedor != '' && $proveedor != '0') {
                $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                if ($datos_proveedor) {
                    $nombre_cliente = $datos_proveedor->nombre;
                    $ruc_cliente = $datos_proveedor->ruc;
                    $direccion   = $datos_proveedor->direccion;
                }
            }

        // DATOS DEL PROYECTO
        $proyecto = $this->comprobante_model->buscar_proyecto($datos_comprobante[0]->PROYP_Codigo);
        if ( count($proyecto) > 0 ) {
            $nombre_proyecto = $proyecto[0]->PROYC_Nombre;
            $direccionProyecto = $this->comprobante_model->buscar_direccion_proyecto($datos_comprobante[0]->PROYP_Codigo);
            if( count($direccionProyecto) > 0 )
                $direccion_proyecto = $direccionProyecto[0]->DIRECC_Descrip;
            else
                $direccion_proyecto = "";
        }else{
            $nombre_proyecto = "";
            $direccion_proyecto = "";
        }

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

        $this->load->library("tcpdf");
        $medidas = "a4"; // a4 - carta
        $this->pdf = new pdfGarantiaComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(20, 50, 20);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->SetTitle('CARTA DE GARANTIA');
        $this->pdf->setPrintHeader(true);
        $this->pdf->SetFont('freesans', '', 8);
        $this->pdf->AddPage();

        /* Listado de detalles */
            $detaProductos = "";
            foreach ($detalle_comprobante as $indice => $valor) {
                $codigo_usuario = $valor->PROD_CodigoUsuario;
                $nomprod = $valor->PROD_Nombre;

                if (strlen($nomprod) > 41)
                    $nomprod = substr($nomprod, 0, 38) . ' ...';

                $nomprod = ($valor->CPDEC_GenInd == 'I') ? $nomprod.$this->ObtenerSeriesComprobante($codigo,$tipo_docu,$valor->PROD_Codigo) : $nomprod;
                $unidadMedida = $this->unidadmedida_model->obtener($valor->UNDMED_Codigo);
                $medidaDetalle = "";
                $medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

                    $detaProductos = $detaProductos. '
                    <tr>
                        <td style="border-bottom:1px #000 solid; text-align:center;">'.$codigo_usuario.'</td>
                        <td style="border-bottom:1px #000 solid; text-align:left;">'.$nomprod.'</td>
                        <td style="border-bottom:1px #000 solid; text-align:right;">'.$valor->CPDEC_Cantidad.'</td>
                    </tr>';
                
            }

        $this->load->model("maestros/emprestablecimiento_model");
        $this->load->model("maestros/compania_model");
        $this->load->model("empresa/empresa_model");

        $datosCompania = $this->compania_model->obtener($companiaComprobante);
        $datosEstablecimiento = $this->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
        $datosEmpresa =  $this->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

        $tipoDocumento = "";
        
        switch ($tipo_docu) {
            case 'F':
                $tipoDocumento = ($tipo_oper == 'V') ?  "FACTURA DE VENTA<br>ELECTRÓNICA" :  "FACTURA DE COMPRA<br>ELECTRÓNICA";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "FACTURA DE VENTA ELECTRÓNICA" :  "FACTURA DE COMPRA ELECTRÓNICA";
                break;
            case 'B':
                $tipoDocumento = ($tipo_oper == 'V') ?  "BOLETA DE VENTA<br>ELECTRÓNICA" :  "BOLETA DE COMPRA<br>ELECTRÓNICA";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "BOLETA DE VENTA ELECTRÓNICA" :  "BOLETA DE COMPRA ELECTRÓNICA";
                break;
            case 'N':
                $tipoDocumento = ($tipo_oper == 'V') ?  "COMPROBANTE DE VENTA<br>ELECTRÓNICO" :  "COMPROBANTE DE COMPRA ELECTRÓNICO";
                $tipoDocumentoF = ($tipo_oper == 'V') ?  "COMPROBANTE DE VENTA ELECTRÓNICO" :  "COMPROBANTE DE COMPRA ELECTRÓNICO";
                break;
        }

        $tituloHTML = '
                <table border="0">
                    <tr>
                        <td style="width:17cm; font-weight:bold; font-size:14pt; text-align:center;">CARTA DE GARANTIA</td>
                    </tr>
                </table> <br><br><br><br>&nbsp;';

        $this->pdf->writeHTML($tituloHTML,true,false,true,'');

        $clienteHTML = '
                <table cellpadding="0.05cm">
                    <tr>
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;"><b>CLIENTE:</b></td>
                        <td style="line-height:0.5cm; width:9.5cm;">'.$nombre_cliente.'</td>

                        <td style="line-height:0.5cm; width:5.5cm;"><b>FECHA:</b> '.$fecha.'</td>
                    </tr>
                    <tr>
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;">R.U.C:</td>
                        <td style="line-height:0.5cm; width:15cm;" colspan="2">'.$ruc_cliente.'</td>
                    </tr>
                    <tr> 
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;">DIRECCION:</td>
                        <td style="line-height:0.5cm; width:15cm;" colspan="2">'.$direccion.'</td>
                    </tr>
                </table><br><br>&nbsp;';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        $garantiaHTML = '
                        <table border="0">
                            <tr>
                                <td style="text-align:justify; line-height:0.8cm; font-size:12pt;">Por medio de la presente <b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b> con <b>R.U.C. '.$datosEmpresa[0]->EMPRC_Ruc.'</b>, garantiza que los productos que fabricamos y comercializamos han sido elaborados; Bajo estrictos controles de calidad los cuales tienen 2 años de garantía. Por tanto, cualquiera de ellas será reemplazado en caso de presentar algún defecto de fabricación siempre y cuando se siga un correcto manipuleo, almacenaje e instalación.</td>
                            </tr>
                        </table><br><br>&nbsp;';
        $this->pdf->writeHTML($garantiaHTML,true,false,true,'');

        $referenciasHTML = '
                        <table border="0" cellpadding="0.05cm">
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">ORDEN DE COMPRA:</td>
                                <td style="width:12.5cm;">'.$oc.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">FACTURA:</td>
                                <td style="width:12.5cm;">'.$numero_factura.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">GUIA DE REMISION:</td>
                                <td style="width:12.5cm;">'.$guiaRemision.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">NOMBRE DEL PROYECTO:</td>
                                <td style="width:12.5cm;">'.$nombre_proyecto.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">DIRECCION DEL PROYECTO:</td>
                                <td style="width:12.5cm;">'.$direccion_proyecto.'</td>
                            </tr>
                        </table><br><br>&nbsp;';
        $this->pdf->writeHTML($referenciasHTML,true,false,true,'');

        $productoHTML = '
                <table border="0" cellpadding="0.05cm">
                    <tr>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:center; width:3cm;">CODIGO</th>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:left; width:12cm;">DESCRIPCION</th>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:right; width:2cm;">CANTIDAD</th>
                    </tr>
                    '.$detaProductos.'
                </table>';
        $this->pdf->writeHTML($productoHTML,true,false,true,'');

        $this->pdf->Output('Garantia.pdf', 'I');
    }

    public function impresionDocumentoCompromisoGuia($codigo){
        $this->load->model("maestros/moneda_model");
        $this->load->model("maestros/formapago_model");
        $this->load->model("empresa/cliente_model");
        $this->load->model("empresa/proveedor_model");

        $datos_comprobante = $this->guiarem_model->obtener_datos_guia($codigo);
        $serie = $datos_comprobante[0]->GUIAREMC_Serie;
        $numero = $datos_comprobante[0]->GUIAREMC_Numero;

        // DATOS DEL COMPROBANTE
        $companiaComprobante = $datos_comprobante[0]->COMPP_Codigo;
        $tipo_oper = $datos_comprobante[0]->GUIAREMC_TipoOperacion;

        $fecha = mysql_to_human($datos_comprobante[0]->GUIAREMC_Fecha);

        // REFERENCIAS
        $numero_guia = $this->validar_formato_serie($serie,$numero);
        $cotizacion = $datos_comprobante[0]->GUIAREMC_OCompra;
            $ordenCompra = $this->ocompra_model->obtener_ocompra( $datos_comprobante[0]->OCOMP_Codigo );
            $oc = $ordenCompra[0]->OCOMC_PersonaAutorizada;
        
        // DATOS DE MONEDAS
        $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');

        /*FORMA DE PAGO*/
        $formapago_desc = "EFECTIVO";
        $formapago_id = $datos_comprobante[0]->FORPAP_Codigo;
        $datos_formapago = $this->formapago_model->obtener2($formapago_id);
        $formapago_desc = $datos_formapago[0]->FORPAC_Descripcion; // NO APLICA PARA NOTAS

        // FORMATO DE FECHA
        $nFecha = explode( '/', $fecha );
        $fecha = $nFecha[0]." DE ".$this->lib_props->mesesEs($nFecha[1])." DEL ".$nFecha[2];
        
        // DATOS DEL CLIENTE
        $cliente = $datos_comprobante[0]->CLIP_Codigo;
        $proveedor = $datos_comprobante[0]->PROVP_Codigo;
 
        if ($cliente != '' && $cliente != '0') {
            $datos_cliente = $this->cliente_model->obtener($cliente);
            if ($datos_cliente) {
                $nombre_cliente = $datos_cliente->nombre;
                $ruc_cliente = $datos_cliente->ruc;
                $dni_cliente = $datos_cliente->dni;
                $ruc_cliente = ($ruc_cliente=="") ? $dni_cliente : $ruc_cliente ;
                $direccion   = $datos_cliente->direccion;
            }
        }
        else
            if ($proveedor != '' && $proveedor != '0') {
                $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                if ($datos_proveedor) {
                    $nombre_cliente = $datos_proveedor->nombre;
                    $ruc_cliente = $datos_proveedor->ruc;
                    $direccion   = $datos_proveedor->direccion;
                }
            }

        // DATOS DEL PROYECTO
        $proyecto = $this->comprobante_model->buscar_proyecto($datos_comprobante[0]->PROYP_Codigo);
        if ( count($proyecto) > 0 ) {
            $nombre_proyecto = $proyecto[0]->PROYC_Nombre;
            $direccionProyecto = $this->comprobante_model->buscar_direccion_proyecto($datos_comprobante[0]->PROYP_Codigo);
            if( count($direccionProyecto) > 0 )
                $direccion_proyecto = $direccionProyecto[0]->DIRECC_Descrip;
            else
                $direccion_proyecto = "";
        }else{
            $nombre_proyecto = "";
            $direccion_proyecto = "";
        }

        $detalle_comprobante = $this->obtener_lista_detalles($codigo);

        $this->load->library("tcpdf");
        $medidas = "a4"; // a4 - carta
        $this->pdf = new pdfGarantiaComprobante('P', 'mm', $medidas, true, 'UTF-8', false);
        $this->pdf->SetMargins(20, 50, 20);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->SetTitle('CARTA DE GARANTIA');
        $this->pdf->setPrintHeader(true);
        $this->pdf->SetFont('freesans', '', 8);
        $this->pdf->AddPage();

        /* Listado de detalles */
            $detaProductos = "";
            foreach ($detalle_comprobante as $indice => $valor) {
                $codigo_usuario = $valor->PROD_CodigoUsuario;
                $nomprod = $valor->PROD_Nombre;

                if (strlen($nomprod) > 41)
                    $nomprod = substr($nomprod, 0, 38) . ' ...';

                $nomprod = ($valor->CPDEC_GenInd == 'I') ? $nomprod.$this->ObtenerSeriesComprobante($codigo,$tipo_docu,$valor->PROD_Codigo) : $nomprod;
                $unidadMedida = $this->unidadmedida_model->obtener($valor->UNDMED_Codigo);
                $medidaDetalle = "";
                $medidaDetalle = ($unidadMedida[0]->UNDMED_Simbolo != "") ? $unidadMedida[0]->UNDMED_Simbolo : "NIU";

                    $detaProductos = $detaProductos. '
                    <tr>
                        <td style="border-bottom:1px #000 solid; text-align:center;">'.$codigo_usuario.'</td>
                        <td style="border-bottom:1px #000 solid; text-align:left;">'.$nomprod.'</td>
                        <td style="border-bottom:1px #000 solid; text-align:right;">'.$valor->CPDEC_Cantidad.'</td>
                    </tr>';
                
            }

        $this->load->model("maestros/emprestablecimiento_model");
        $this->load->model("maestros/compania_model");
        $this->load->model("empresa/empresa_model");

        $datosCompania = $this->compania_model->obtener($companiaComprobante);
        $datosEstablecimiento = $this->emprestablecimiento_model->listar( $datosCompania[0]->EMPRP_Codigo, '', $companiaComprobante );
        $datosEmpresa =  $this->empresa_model->obtener_datosEmpresa( $datosCompania[0]->EMPRP_Codigo );

        $tituloHTML = '
                <table border="0">
                    <tr>
                        <td style="width:17cm; font-weight:bold; font-size:14pt; text-align:center;">CARTA DE GARANTIA</td>
                    </tr>
                </table> <br><br><br><br>&nbsp;';

        $this->pdf->writeHTML($tituloHTML,true,false,true,'');

        $clienteHTML = '
                <table cellpadding="0.05cm">
                    <tr>
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;"><b>CLIENTE:</b></td>
                        <td style="line-height:0.5cm; width:9.5cm;">'.$nombre_cliente.'</td>

                        <td style="line-height:0.5cm; width:5.5cm;"><b>FECHA:</b> '.$fecha.'</td>
                    </tr>
                    <tr>
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;">R.U.C:</td>
                        <td style="line-height:0.5cm; width:15cm;" colspan="2">'.$ruc_cliente.'</td>
                    </tr>
                    <tr> 
                        <td style="line-height:0.5cm; width:2cm; font-weight:bold;">DIRECCION:</td>
                        <td style="line-height:0.5cm; width:15cm;" colspan="2">'.$direccion.'</td>
                    </tr>
                </table><br><br>&nbsp;';

        $this->pdf->writeHTML($clienteHTML,true,false,true,'');

        $garantiaHTML = '
                        <table border="0">
                            <tr>
                                <td style="text-align:justify; line-height:0.8cm; font-size:12pt;">Por medio de la presente <b>'.$datosEmpresa[0]->EMPRC_RazonSocial.'</b> con <b>R.U.C. '.$datosEmpresa[0]->EMPRC_Ruc.'</b>, garantiza que los productos que fabricamos y comercializamos han sido elaborados; Bajo estrictos controles de calidad los cuales tienen 2 años de garantía. Por tanto, cualquiera de ellas será reemplazado en caso de presentar algún defecto de fabricación siempre y cuando se siga un correcto manipuleo, almacenaje e instalación.</td>
                            </tr>
                        </table><br><br>&nbsp;';
        $this->pdf->writeHTML($garantiaHTML,true,false,true,'');

        $referenciasHTML = '
                        <table border="0" cellpadding="0.05cm">
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">ORDEN DE COMPRA:</td>
                                <td style="width:12.5cm;">'.$oc.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">COTIZACIÓN:</td>
                                <td style="width:12.5cm;">'.$cotizacion.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">GUIA DE REMISION:</td>
                                <td style="width:12.5cm;">'.$numero_guia.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">NOMBRE DEL PROYECTO:</td>
                                <td style="width:12.5cm;">'.$nombre_proyecto.'</td>
                            </tr>
                            <tr>
                                <td style="width:4.5cm; font-weight:bold;">DIRECCION DEL PROYECTO:</td>
                                <td style="width:12.5cm;">'.$direccion_proyecto.'</td>
                            </tr>
                        </table><br><br>&nbsp;';
        $this->pdf->writeHTML($referenciasHTML,true,false,true,'');

        $productoHTML = '
                <table border="0" cellpadding="0.05cm">
                    <tr>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:center; width:3cm;">CODIGO</th>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:left; width:12cm;">DESCRIPCION</th>
                        <th style="background-color:#D9D9D9; border-bottom:1px #000 solid; font-weight:bold; text-align:right; width:2cm;">CANTIDAD</th>
                    </tr>
                    '.$detaProductos.'
                </table>';
        $this->pdf->writeHTML($productoHTML,true,false,true,'');

        $this->pdf->Output('Garantia.pdf', 'I');
    }

    public function obtener_lista_detalles_guia($codigo){
        $detalle = $this->guiarem_model->listar_productos_guias($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->GUIAREMDETP_Codigo;
                $producto = $valor->PRODCTOP_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->GUIAREMDETC_Cantidad;

                $pendiente = NULL;
                $pu = $valor->GUIAREMDETC_Pu;

                $subtotal = $valor->GUIAREMDETC_Subtotal;
                $igv = $valor->GUIAREMDETC_Igv;
                $descuento = $valor->GUIAREMDETC_Descuento;
                $total = $valor->GUIAREMDETC_Total;
                $pu_conigv = $valor->GUIAREMDETC_Pu_ConIgv;

                #$subtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
                #$descuento_conigv = $valor->CPDEC_Descuento_ConIgv;
                $subtotal_conigv = NULL;
                $descuento_conigv = NULL;

                $descuento100 = $valor->GUIAREMDETC_Descuento100;
                $igv100 = $valor->GUIAREMDETC_Igv100;

                #$observacion = $valor->CPDEC_Observacion;
                $observacion = NULL;

                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);

                #$GenInd = $valor->CPDEC_GenInd;
                $GenInd = NULL;
                
                $costo = $valor->GUIAREMDETC_Costo;
                $almacenProducto= $valor->ALMAP_Codigo;
                $codigoGuiaremAsociadaDetalle= $valor->GUIAREMP_Codigo;

                #$codigovc = $valor->OCOMP_Codigo_VC;
                $codigovc = NULL;

                #$nombre_producto = ($valor->GUIAREMDETC_Descripcion != '' ? $valor->GUIAREMDETC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = $datos_producto[0]->PROD_Nombre;
                #$nombre_producto = str_replace('\\', '', $nombre_producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->CPDEC_GenInd = $GenInd;
                $objeto->CPDEC_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_Cantidad = $cantidad;
                $objeto->CPDEC_Pendiente = $pendiente;
                $objeto->CPDEC_Pu = $pu;
                $objeto->CPDEC_Subtotal = $subtotal;
                $objeto->CPDEC_Descuento = $descuento;
                $objeto->CPDEC_Igv = $igv;
                $objeto->CPDEC_Total = $total;
                $objeto->CPDEC_Pu_ConIgv = $pu_conigv;
                $objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CPDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->CPDEC_Descuento100 = $descuento100;
                $objeto->CPDEC_Igv100 = $igv100;
                $objeto->CPDEC_Observacion = $observacion;
                $objeto->ALMAP_Codigo =$almacenProducto;
                $objeto->GUIAREMP_Codigo =$codigoGuiaremAsociadaDetalle;
                $objeto->OCOMP_Codigo_VC = $codigovc;
                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }


    public function impresionDocumentoCuota($CodigoPrincipal,$codigoDocumento,$isImagen,$ventaCompra){
            $hoy = date("Y-m-d");
            include("system/application/libraries/pchart/pData.php");
            include("system/application/libraries/pchart/pChart.php");
            include("system/application/libraries/cezpdf.php");
            include("system/application/libraries/class.backgroundpdf.php");
            include("system/application/libraries/lib_fecha_letras.php");
            //include("system/application/controller/maestros/configuracionimpresion");
            // DISPARADOR END
            //**************************************************************************
                $comprobante = $this->comprobante_model->obtener_comprobantexcuota($CodigoPrincipal);
                $datos_comprobante = $this->comprobante_model->obtener_comprobante($comprobante[0]->CPP_Codigo);
                $serie = $datos_comprobante[0]->CPC_Serie;
                $numero = $datos_comprobante[0]->CPC_Numero;
                $proveedor = $datos_comprobante[0]->PROVP_Codigo;
                $subtotal = $datos_comprobante[0]->CPC_subtotal;
                $datos_cliente = $this->comprobante_model->obtener_cliente($datos_comprobante[0]->CLIP_Codigo);
                if (count($datos_cliente)>0) {
                    $cliente_rs = $datos_cliente[0]->EMPRC_RazonSocial;
                    $cliente_doc = $datos_cliente[0]->EMPRC_Ruc;
                    $cliente_direc = $datos_cliente[0]->EMPRC_Direccion;
                    $cliente_telf = $datos_cliente[0]->EMPRC_Telefono;

                }else{
                    $datos_cliente = $this->comprobante_model->obtener_cliente_dni($datos_comprobante[0]->CLIP_Codigo);
                    $cliente_rs = $datos_cliente[0]->PERSC_Nombre." ".$datos_cliente[0]->PERSC_ApellidoPaterno." ".$datos_cliente[0]->PERSC_ApellidoMaterno;
                    $cliente_doc = $datos_cliente[0]->PERSC_NumeroDocIdentidad;
                    $cliente_direc = $datos_cliente[0]->PERSC_Direccion;
                    $cliente_telf = $datos_cliente[0]->PERSC_Telefono;
                }
                $ocompra = $this->comprobante_model->buscar_ocompra($datos_comprobante[0]->OCOMP_Codigo);
                $cliente_direc = $datos_comprobante[0]->CPC_Direccion;

                if (strlen($cliente_direc)>59) {
                    $c_direc_a = substr($cliente_direc, 0,59);
                    $c_direc_b = substr($cliente_direc, 59,strlen($cliente_direc));
                }else{
                    $c_direc_a = $cliente_direc;
                    $c_direc_b = "";
                }


                /*
                if (count($ocompra)>0) {
                    $serie_ocompra = $ocompra[0]->OCOMC_Serie;
                    $numero_ocompra = $ocompra[0]->OCOMC_Numero;
                    $oc = $this->validar_formato_serie($serie_ocompra,$numero_ocompra);
                }else{
                    $numero_ocompra = $datos_comprobante[0]->CPP_Compracliente;
                    $oc = $this->validar_formato_serie(1,$numero_ocompra);

                }
                */
                if (is_null($cliente_telf)) {
                    $cliente_telf = "";
                }
                $numero_ocompra = $datos_comprobante[0]->CPP_Compracliente;
                $oc = $this->validar_formato_serie(1,$numero_ocompra);


                $numero_factura = $this->validar_formato_serie($serie,$numero);

                $dguiarem = $this->comprobante_model->buscar_guiarem_comprobante($CodigoPrincipal);

                if (count($dguiarem)>0) {
                    $gserie = $dguiarem[0]->GUIAREMC_Serie;
                    $gnumero = $dguiarem[0]->GUIAREMC_Numero;

                    $numero_guia = $this->validar_formato_serie($gserie,$gnumero);
                }else{
                    $numero_guia = "";
                }

                $descuento = $datos_comprobante[0]->CPC_descuento;
                $igv = $datos_comprobante[0]->CPC_igv;
                $total = $comprobante[0]->CUOT_Monto;
                $fecha = mysql_to_human($datos_comprobante[0]->CPC_Fecha);
                /*
                $datos_proveedor = $this->proveedor_model->obtener_Proveedor($proveedor);
                $ruc = $datos_proveedor[0]->EMPRC_Ruc;
                $empresa = $datos_proveedor[0]->EMPRC_RazonSocial;
                */
                $guiainp_codigo = $datos_comprobante[0]->GUIAINP_Codigo;
                $guiasap_codigo = $datos_comprobante[0]->GUIASAP_Codigo;
                $guiarem_codigo = $datos_comprobante[0]->GUIAREMP_Codigo;
                if ($guiarem_codigo !== Null) {
                    //$list_guiare = $this->guiarem_model->obtener($guiarem_codigo);
                    //$guiasap_codigo = $list_guiare[0]->GUIASAP_Codigo;
                    //$guiainp_codigo = $list_guiare[0]->GUIAINP_Codigo;
                }
                /*
                $datos_moneda = $this->moneda_model->obtener($datos_comprobante[0]->MONED_Codigo);
                $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
                $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');
                */
                /*
                $forma_pago = $datos_comprobante[0]->FORPAP_Codigo;
                $id_formapago = $this->formapago_model->obtener($forma_pago);
                $nombre_formapago = $id_formapago[0]->FORPAC_Descripcion
                */
                $moneda_nombre = " SOLES";
                if (!is_null($comprobante[0]->CUOT_FechaInicio)) {
                    $fecha = mysql_to_human($comprobante[0]->CUOT_FechaInicio);
                }
                
                $fecha_cuota = mysql_to_human($comprobante[0]->CUOT_Fecha);
                $numero_cuota = $this->validar_formato_serie_cuota($comprobante[0]->CUOT_Codigo);
                $codigoCompania=$this->session->userdata('compania');
                $image = "images/documentos/letra_".$codigoCompania.".jpg";

                $monto_letras = strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre;

                //$image = "images/documentos/garantia.jpg";
                //$array_fecha = explode("/", $fecha);
                //$TDC = $this->tipocambio_model->obtener_tdcxfactura($array_fecha[2] . "-" . $array_fecha[1] . "-" . $array_fecha[0]);

                if ($isImagen == 0) {
                    $image = "";
                }

                $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => $image));
                /* Cabecera */
                /*
                $this->cezpdf->ezText($ruc, 12, array("leading" => 3, "left" => 445));
                $this->cezpdf->ezText($empresa, 9, array('leading' => 82, "left" => 25));
                */
                //$this->cezpdf->ezText(utf8_decode_seguro($direccion), 9, array("leading" => 14, "left" => 11));
                #$this->cezpdf->ezText(utf8_decode_seguro($nombre_formapago), 9, array("leading" => -15, "left" => 333));
                /*:::::::::::::: PRIMERA FILA :::::::::::::::::::::: */
                //RAZON SOCIAL
                $this->cezpdf->ezText($numero_cuota, 9, array("leading" => 65, "left" => 90));
                $this->cezpdf->ezText($numero_factura, 9, array("leading" => 0, "left" => 170));

                $this->cezpdf->ezText("LIMA", 9, array("leading" => 0, "left" => 330));

                $this->cezpdf->ezText("S/ ".$total, 9, array("leading" => 0, "left" => 450));

                $this->cezpdf->ezText($fecha, 9, array("leading" => 0, "left" => 250));

                $this->cezpdf->ezText($fecha_cuota, 9, array("leading" => 0, "left" => 390));


                $this->cezpdf->ezText($monto_letras, 8, array("leading" => 35, "left" => 90));

                $this->cezpdf->ezText($cliente_rs, 7, array("leading" => 37, "left" => 125));

                $this->cezpdf->ezText($c_direc_a, 8, array("leading" => 23, "left" => 120));

                $this->cezpdf->ezText($c_direc_b, 8, array("leading" => 10, "left" => 90));

                $this->cezpdf->ezText($cliente_doc, 9, array("leading" => 13, "left" => 120));

                $this->cezpdf->ezText($cliente_telf, 8, array("leading" => 0, "left" => 220));
                /*:::::::::::::::: SEGUNDA FILA ::::::::::::::::*/
                

                /* Listado de detalles */
                $posicionX = 200;
                $posicionY = 320;
                $db_data = array();
                
              
                
                /* Totales */
                /*
                $this->cezpdf->addText(20, 260, 9, "Tipo de cambio " . $TDC[0]->TIPCAMC_FactorConversion . utf8_decode_seguro(" v�lido solo ") . $fecha . " // S/. " . ($total * $TDC[0]->TIPCAMC_FactorConversion) . " NUEVOS SOLES");
                
                $this->cezpdf->addText(20, 245, 9, strtoupper(num2letras(round($total, 2))) . ' ' . $moneda_nombre . ' ' . $moneda_simbolo . ' ' . number_format($total, 2));

                $this->cezpdf->addText(40, 215, 9, $moneda_simbolo . ' ' . number_format($subtotal, 2));

                $this->cezpdf->addText(150, 215, 9, $moneda_simbolo . ' ' . number_format($descuento, 2));

                $this->cezpdf->addText(280, 215, 9, $moneda_simbolo . ' ' . (number_format($subtotal - $descuento, 2)));

                $this->cezpdf->addText(400, 215, 9, $moneda_simbolo . ' ' . number_format($igv, 2));
                $this->cezpdf->addText(500, 215, 9, $moneda_simbolo . ' ' . number_format(($total), 2));
                */
                $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => 'nama_file.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
                $this->cezpdf->ezStream($cabecera);       
    }

    /**convertir de pixeles a mm para que se muestre en la impresion**/
    public function convertirMm($valor){
        return ($valor*0.264583);
    }
    
    public function obtener_lista_detalles($codigo){
        $detalle = $this->comprobantedetalle_model->listar($codigo);
        $lista_detalles = array();
        if (count($detalle) > 0) {
            foreach ($detalle as $indice => $valor) {
                $detacodi = $valor->CPDEP_Codigo;
                $producto = $valor->PROD_Codigo;
                $unidad = $valor->UNDMED_Codigo;
                $cantidad = $valor->CPDEC_Cantidad;
                $pendiente = $valor->CPDEC_Pendiente;
                $pu = $valor->CPDEC_Pu;
                $subtotal = $valor->CPDEC_Subtotal;
                $igv = $valor->CPDEC_Igv;
                $descuento = $valor->CPDEC_Descuento;
                $total = $valor->CPDEC_Total;
                $pu_conigv = $valor->CPDEC_Pu_ConIgv;
                $subtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
                $descuento_conigv = $valor->CPDEC_Descuento_ConIgv;
                $descuento100 = $valor->CPDEC_Descuento100;
                $igv100 = $valor->CPDEC_Igv100;
                $observacion = $valor->CPDEC_Observacion;
                $datos_producto = $this->producto_model->obtener_producto($producto);
                $flagBS = $datos_producto[0]->PROD_FlagBienServicio;
                $datos_unidad = $this->unidadmedida_model->obtener($unidad);
                $GenInd = $valor->CPDEC_GenInd;
                $costo = $valor->CPDEC_Costo;
                $almacenProducto= $valor->ALMAP_Codigo;
                $codigoGuiaremAsociadaDetalle= $valor->GUIAREMP_Codigo;
                $codigovc = $valor->OCOMP_Codigo_VC;

                $nombre_producto = ($valor->CPDEC_Descripcion != '' ? $valor->CPDEC_Descripcion : $datos_producto[0]->PROD_Nombre);
                $nombre_producto = str_replace('\\', '', $nombre_producto);
                $codigo_interno = $datos_producto[0]->PROD_CodigoUsuario;
                $codigo_usuario = $datos_producto[0]->PROD_CodigoUsuario;
                $nombre_unidad = is_array($datos_unidad) ? $datos_unidad[0]->UNDMED_Descripcion : 'SERV';

                $objeto = new stdClass();
                $objeto->CPDEP_Codigo = $detacodi;
                $objeto->flagBS = $flagBS;
                $objeto->PROD_Codigo = $producto;
                $objeto->PROD_CodigoInterno = $codigo_interno;
                $objeto->PROD_CodigoUsuario = $codigo_usuario;
                $objeto->UNDMED_Codigo = $unidad;
                $objeto->UNDMED_Simbolo = $nombre_unidad;
                $objeto->CPDEC_GenInd = $GenInd;
                $objeto->CPDEC_Costo = $costo;
                $objeto->PROD_Nombre = $nombre_producto;
                $objeto->CPDEC_Cantidad = $cantidad;
                $objeto->CPDEC_Pendiente = $pendiente;
                $objeto->CPDEC_Pu = $pu;
                $objeto->CPDEC_Subtotal = $subtotal;
                $objeto->CPDEC_Descuento = $descuento;
                $objeto->CPDEC_Igv = $igv;
                $objeto->CPDEC_Total = $total;
                $objeto->CPDEC_Pu_ConIgv = $pu_conigv;
                $objeto->CPDEC_Subtotal_ConIgv = $subtotal_conigv;
                $objeto->CPDEC_Descuento_ConIgv = $descuento_conigv;
                $objeto->CPDEC_Descuento100 = $descuento100;
                $objeto->CPDEC_Igv100 = $igv100;
                $objeto->CPDEC_Observacion = $observacion;
                $objeto->ALMAP_Codigo =$almacenProducto;
                $objeto->GUIAREMP_Codigo =$codigoGuiaremAsociadaDetalle;
                $objeto->OCOMP_Codigo_VC = $codigovc;
                $lista_detalles[] = $objeto;
            }
        }
        return $lista_detalles;
    }
    
    public function validar_formato_serie($serie,$numero){
        $ns = strlen($serie);
        $nn = strlen($numero);

        switch ($ns){
            case 1:
                $temp_serie = "00".$serie;
                break;
            
            case 2:
                $temp_serie = "0".$serie;
                break;
            default:
                $temp_serie = $serie;
                break;
        }

        switch ($nn){
            case 1:
                $temp_numero = "00000".$numero;
                break;
            
            case 2:
                $temp_numero = "0000".$numero;
                break;
            case 3:
                $temp_numero = "000".$numero;
                break;
            case 4:
                $temp_numero = "00".$numero;
                break;
            case 5:
                $temp_numero = "0".$numero;
                break;
            default:
                $temp_numero = $numero;
                break;
        }

        return $temp_serie."-".$temp_numero;
        
        
    }

    public function validar_formato_serie_cuota($numero){
        $nn = strlen($numero);


        switch ($nn){
            case 1:
                $temp_numero = "00000".$numero;
                break;
            
            case 2:
                $temp_numero = "0000".$numero;
                break;
            case 3:
                $temp_numero = "000".$numero;
                break;
            case 4:
                $temp_numero = "00".$numero;
                break;
            case 5:
                $temp_numero = "0".$numero;
                break;
            default:
                $temp_numero = $numero;
                break;
        }

        return "L-".$temp_numero;
        
        
    }
    
 
    
    
  
}
?>