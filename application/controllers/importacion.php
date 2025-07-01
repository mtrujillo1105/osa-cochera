<?php
include("system/application/libraries/Excel/reader.php"); 
class Importacion extends CI_Controller
{
    public function __construct()
    {
        parent::Controller();
        $this->load->helper('url');
        $this->load->model('almacen/familia_model');
        $this->load->model('maestros/marca_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('empresa/cliente_model');
        $this->load->model('maestros/tipocliente_model');
        $this->load->model('empresa/empresa_model');
        $this->load->model('maestros/persona_model');
        $this->load->model('empresa/directivo_model');
        $this->load->model('maestros/cargo_model');
        $this->load->model('maestros/area_model');
        $this->load->model('maestros/tipoestablecimiento_model');
        $this->load->model('maestros/nacionalidad_model');
        $this->load->model('maestros/tipodocumento_model');
        $this->load->model('maestros/tipocodigo_model');
        $this->load->model('maestros/estadocivil_model');
        $this->load->model('maestros/ubigeo_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->library('html');
        $this->somevar['compania'] = $this->session->userdata('compania');
    }
    public function index(){
        $RutaArchivoCargado = 'datos_impacto.xls';  // Debe estar ubicado en la raiz del proyecto
        $data = new Spreadsheet_Excel_Reader();

        $data->setUTFEncoder('mb');
        $data->setOutputEncoding('UTF-8');

        $data->read($RutaArchivoCargado);
        error_reporting(E_ALL ^ E_NOTICE);
        
        /*Inserto las Familias*/
        /*for($fil = 2; $fil <= $data->sheets[0]['numRows']; $fil++) {
            $valor = &$data->sheets[0]['cells'][$fil];
            $familia=$this->familia_model->insertar_familia('B',  strtoupper(trim($valor[2])), '0', trim($valor[1]));
            echo $valor[1]."  ".$valor[2]." <br />";
            
        }*/
        
        /*Inserto las Marcas*/
        /*for($fil = 2; $fil <= $data->sheets[1]['numRows']; $fil++) {
            $valor = &$data->sheets[1]['cells'][$fil];
            $filter = new stdClass();
            $filter->MARCC_CodigoUsuario = strtoupper(trim($valor[1]));
            $filter->MARCC_Descripcion = strtoupper(trim($valor[2]));
            $familia=$this->marca_model->insertar($filter);
            echo $valor[1]."  ".$valor[2]." <br />";
            
        }*/
        
        /*Inserto las Produtos*/
        /*for($fil = 2; $fil <= $data->sheets[2]['numRows']; $fil++) {
            $valor = &$data->sheets[2]['cells'][$fil];
            //para obtener la marca
            $filter = new stdClass();
            $filter->MARCC_Descripcion = strtoupper(trim($valor[4]));
            $lista_marca=$this->marca_model->buscar($filter);
            $marca= is_array($lista_marca) ? $lista_marca[0]->MARCP_Codigo : '';
            
            //para obtener la familia
            $filter = new stdClass();
            $filter->nombre = strtoupper(trim($valor[3]));
            $filter->flagBS='B';
            $lista_familia=$this->familia_model->buscar_familias('0', $filter);
            $familia= is_array($lista_familia) ? $lista_familia[0]->FAMI_Codigo : '';
            $codigo_familia= is_array($lista_familia) ? $lista_familia[0]->FAMI_CodigoInterno.'.' : '';
                
            
            echo $valor[1]."  ".$valor[2]."  ".$valor[3]."  ".$valor[4]." ".$marca." ".$familia." "." <br />";
            
            $this->producto_model->insertar_producto_total('',$familia,'',strtoupper(trim($valor[2])),'','',array(7),array(1),array(1),'','',$codigo_familia,'','',$marca,'', '', '', '', 'I');
        }*/
        
        /*Inserto los clientes*/
        /*for($fil = 2; $fil <= 300; $fil++) {
            $valor = &$data->sheets[3]['cells'][$fil];
            
            if(trim($valor[4])=='')
                continue;
            echo ($fil-1).' '.strtoupper(trim($valor[1])).' '.strtoupper(trim($valor[2])).' '.strtoupper(trim($valor[3])).' '.strtoupper(trim($valor[4])).' '.strtoupper(trim($valor[5])).'<br />';
            $nombre_sucursal = array();
            $nombre_contacto = array();
            $empresa_persona = '';
            $tipo_persona    = strtoupper(trim($valor[2]))=='P' ? '0' : '1';
            $tipocodigo      = '1';
            $ruc             = strtoupper(trim($valor[1]));
            $razon_social    = strtoupper(trim($valor[4]));
            $telefono        = '';
            $movil           = '';
            $fax             = '';
            $email           = '';
            $web             = '';
            $direccion       = strtoupper(trim($valor[5]));
            $departamento    = '00';
            $provincia       = '00';
            $distrito        = '00';
            $categoria            = '';
            $sector_comercial  = '';
            $forma_pago           = '';
            $ctactesoles     = '';
            $ctactedolares   = '';
            $ubigeo_domicilio = $departamento.$provincia.$distrito;

            //Datos exclusivos de la persona
            $nombres         = strtoupper(trim($valor[4]));
            $paterno         = '';
            $materno         = '';
            $tipo_documento    = '1';	
            $numero_documento  = strtoupper(trim($valor[3]));
            $ubigeo_nacimiento = '000000';
            $sexo             = '';
            $estado_civil     = '';
            $nacionalidad     = '193';
            $ruc_persona      = '';


            /*Array de variables*/
            /*$nombre_sucursal      = $this->input->post('nombreSucursal');
            $direccion_sucursal   = $this->input->post('direccionSucursal');
            $tipo_establecimiento = $this->input->post('tipoEstablecimiento');		
            $arrayDpto            = $this->input->post('dptoSucursal');
            $arrayProv            = $this->input->post('provSucursal');
            $arrayDist            = $this->input->post('distSucursal');
            $persona_contacto     = $this->input->post('contactoPersona');
            $nombre_contacto      = $this->input->post('contactoNombre');
            $area_contacto        = $this->input->post('contactoArea');
            $cargo_contacto       = $this->input->post('cargo_encargado');
            $telefono_contacto    = $this->input->post('contactoTelefono');
            $email_contacto       = $this->input->post('contactoEmail');


            if($arrayDpto!='' && $arrayProv!='' && $arrayDist!=''){
                    $ubigeo_sucursal  = $this->html->array_ubigeo($arrayDpto,$arrayProv,$arrayDist);
            }
            if($tipo_persona==1){//Empresa
                    $persona = 0;
                    if($empresa_persona!='' && $empresa_persona!='0'){
                        $empresa=$empresa_persona;
                        $this->empresa_model->modificar_datosEmpresa($empresa,$tipocodigo, $ruc,$razon_social,$telefono,$movil,$fax,$web,$email,$sector_comercial,$ctactesoles,$ctactedolares);
                    }
                    else
                        $empresa = $this->empresa_model->insertar_datosEmpresa($tipocodigo, $ruc,$razon_social,$telefono,$fax,$web,$movil,$email,$sector_comercial,$ctactesoles,$ctactedolares);

                    $this->empresa_model->insertar_sucursalEmpresaPrincipal('1',$empresa,$ubigeo_domicilio,'PRINCIPAL',$direccion);//Direccion Principal
                    $cliente=$this->cliente_model->insertar_datosCliente($empresa,$persona,$tipo_persona, $categoria, $forma_pago);
                    //Insertar Establecimientos
                    if($nombre_sucursal!=''){
                        foreach($nombre_sucursal as $indice=>$valor){
                            if($nombre_sucursal[$indice]!='' && $direccion_sucursal!='' && $tipo_establecimiento[$indice]!=''){
                                $ubigeo_s = strlen($ubigeo_sucursal[$indice])<6?"000000":$ubigeo_sucursal[$indice];
                                $this->empresa_model->insertar_sucursalEmpresa($tipo_establecimiento[$indice],$empresa,$ubigeo_s,$nombre_sucursal[$indice],$direccion_sucursal[$indice]);
                                exit($ubigeo_s);
                            }
                        }
                    }
                    //Insertar contactos empresa
                    if($nombre_contacto!=''){
                            foreach($nombre_contacto as $indice=>$valor){
                                    if($nombre_contacto[$indice]!=''){
                                            $pers_contacto = $persona_contacto[$indice];
                                            $nom_contacto  = $nombre_contacto[$indice];
                                            $car_contacto  = $cargo_contacto[$indice];
                                            $ar_contacto   = $area_contacto[$indice];
                                            $arrTelConctacto = explode("/",$telefono_contacto[$indice]);
                                            switch(count($arrTelConctacto)){
                                                    case 2:
                                                            $tel_contacto  = $arrTelConctacto[0];
                                                            $mov_contacto  = $arrTelConctacto[1];	
                                                            break;
                                                    case 1:
                                                            $tel_contacto  = $arrTelConctacto[0];
                                                            $mov_contacto  = "";	
                                                            break;
                                                    case 0:
                                                            $tel_contacto  = "";
                                                            $mov_contacto  = "";	
                                                            break;							
                                            }	
                                            $e_contacto    = $email_contacto[$indice];
                                            if($pers_contacto==''){$pers_contacto = $this->persona_model->insertar_datosPersona('000000','000000','1','193',$nom_contacto,'','','','1');}//Inserto persona
                                            $directivo = $this->empresa_model->insertar_directivoEmpresa($empresa,$pers_contacto,$car_contacto);
                                            $this->empresa_model->insertar_areaEmpresa($ar_contacto,$empresa,$directivo,'::OBSERVACION::');
                                            $this->empresa_model->insertar_contactoEmpresa($empresa,'::OBSERVACION:',$tel_contacto,$mov_contacto,$e_contacto,$pers_contacto);			
                                    }
                            }
                    }
            }
            elseif($tipo_persona==0){//Persona
                    $empresa = 0;                       
                    if($empresa_persona!='' && $empresa_persona!='0'){
                        $persona=$empresa_persona;
                        $this->persona_model->modificar_datosPersona($persona,$ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc_persona,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$domicilio,$sexo,$fax,$web,$ctactesoles,$ctactedolares);
                    }                            
                    else
                        $persona = $this->persona_model->insertar_datosPersona($ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc_persona,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$direccion,$sexo,$web,$ctactesoles,$ctactedolares);

                    $cliente=$this->cliente_model->insertar_datosCliente($empresa,$persona,$tipo_persona, $categoria, $forma_pago);
            }
            
        }*/
        
        
        /*Inserto los proveedores*/
        
        for($fil = 2; $fil <= $data->sheets[4]['numRows']; $fil++) {
            $valor = &$data->sheets[4]['cells'][$fil];
            
            if(trim($valor[4])=='')
                continue;
            echo ($fil-1).' '.strtoupper(trim($valor[1])).' '.strtoupper(trim($valor[2])).' '.strtoupper(trim($valor[3])).' '.strtoupper(trim($valor[4])).' '.strtoupper(trim($valor[6])).'<br />';

            $nombre_sucursal = array();
            $nombre_contacto = array();
            $tipo_persona    = strtoupper(trim($valor[3]))=='P' ? '0' : '1';
            $tipocodigo      = '1';
            $ruc             = strtoupper(trim($valor[1]));
            $razon_social    = strtoupper(trim($valor[4]));
            $telefono        = '';
            $movil           = '';
            $fax             = '';
            $email           = '';
            $web             = '';
            $direccion       = strtoupper(trim($valor[6]));
            $departamento    = '00';
            $provincia       = '00';
            $distrito        = '00';
            $sector_comercial  = '';
            $ctactesoles     = '';
            $ctactedolares   = '';
            $ubigeo_domicilio = $departamento.$provincia.$distrito;

            $nombres         = strtoupper(trim($valor[4]));
            $paterno         = '';
            $materno         = '';	
            $tipo_documento    = '1';	
            $numero_documento  =strtoupper(trim($valor[2]));;
            $ubigeo_nacimiento = '000000';
            $sexo             = '';
            $estado_civil     = '';
            $nacionalidad     = '193';
            $ruc_persona      = '';


            //Array de variables
            $nombre_sucursal      = $this->input->post('nombreSucursal');
            $direccion_sucursal   = $this->input->post('direccionSucursal');
            $tipo_establecimiento = $this->input->post('tipoEstablecimiento');		
            $arrayDpto            = $this->input->post('dptoSucursal');
            $arrayProv            = $this->input->post('provSucursal');
            $arrayDist            = $this->input->post('distSucursal');
            $persona_contacto     = $this->input->post('contactoPersona');
            $nombre_contacto      = $this->input->post('contactoNombre');
            $area_contacto        = $this->input->post('contactoArea');
            $cargo_contacto       = $this->input->post('cargo_encargado');
            $telefono_contacto    = $this->input->post('contactoTelefono');
            $email_contacto       = $this->input->post('contactoEmail');
            if($arrayDpto!='' && $arrayProv!='' && $arrayDist!=''){
                    $ubigeo_sucursal  = $this->html->array_ubigeo($arrayDpto,$arrayProv,$arrayDist);
            }
            if($tipo_persona==1){//Empresa
                    $persona = 0;
                    if($empresa_persona!='' && $empresa_persona!='0'){
                        $empresa=$empresa_persona;
                        $this->empresa_model->modificar_datosEmpresa($empresa,$tipocodigo, $ruc,$razon_social,$telefono,$movil,$fax,$web,$email,$sector_comercial,$ctactesoles,$ctactedolares);                           
                    }
                    else
                        $empresa = $this->empresa_model->insertar_datosEmpresa($tipocodigo, $ruc,$razon_social,$telefono,$fax,$web,$movil,$email,$sector_comercial,$ctactesoles,$ctactedolares);

                    $this->empresa_model->insertar_sucursalEmpresaPrincipal('1',$empresa,$ubigeo_domicilio,'PRINCIPAL',$direccion);//Direccion Principal
                    $this->proveedor_model->insertar_datosProveedor($empresa,$persona,$tipo_persona);
                    //Insertar Establecimientos
                    if($nombre_sucursal!=''){
                        foreach($nombre_sucursal as $indice=>$valor){
                            if($nombre_sucursal[$indice]!='' && $direccion_sucursal!='' && $tipo_establecimiento[$indice]!=''){
                                $ubigeo_s = strlen($ubigeo_sucursal[$indice])<6?"000000":$ubigeo_sucursal[$indice];
                                $this->empresa_model->insertar_sucursalEmpresa($tipo_establecimiento[$indice],$empresa,$ubigeo_s,$nombre_sucursal[$indice],$direccion_sucursal[$indice]);
                            }
                        }
                    } 
                    //Insertar contactos empresa
                    if($nombre_contacto!=''){
                            foreach($nombre_contacto as $indice=>$valor){
                                    if($nombre_contacto[$indice]!=''){
                                            $pers_contacto = $persona_contacto[$indice];
                                            $nom_contacto  = $nombre_contacto[$indice];
                                            $car_contacto  = $cargo_contacto[$indice];
                                            $ar_contacto   = $area_contacto[$indice];
                                            $arrTelConctacto = explode("/",$telefono_contacto[$indice]);
                                            switch(count($arrTelConctacto)){
                                                    case 2:
                                                            $tel_contacto  = $arrTelConctacto[0];
                                                            $mov_contacto  = $arrTelConctacto[1];	
                                                            break;
                                                    case 1:
                                                            $tel_contacto  = $arrTelConctacto[0];
                                                            $mov_contacto  = "";	
                                                            break;
                                                    case 0:
                                                            $tel_contacto  = "";
                                                            $mov_contacto  = "";	
                                                            break;							
                                            }	
                                            $e_contacto    = $email_contacto[$indice];
                                            if($pers_contacto==''){$pers_contacto = $this->persona_model->insertar_datosPersona('000000','000000','1','193',$nom_contacto,'','','','1');}//Inserto persona
                                            $directivo = $this->empresa_model->insertar_directivoEmpresa($empresa,$pers_contacto,$car_contacto);
                                            $this->empresa_model->insertar_areaEmpresa($ar_contacto,$empresa,$directivo,'::OBSERVACION::');
                                            $this->empresa_model->insertar_contactoEmpresa($empresa,'::OBSERVACION:',$tel_contacto,$mov_contacto,$e_contacto,$pers_contacto);			
                                    }
                            }
                    }
            }
            elseif($tipo_persona==0){//Persona
                    $empresa = 0;                       
                    if($empresa_persona!='' && $empresa_persona!='0'){
                        $persona=$empresa_persona;
                        $this->persona_model->modificar_datosPersona($persona,$ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc_persona,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$domicilio,$sexo,$fax,$web,$ctactesoles,$ctactedolares);
                    }                            
                    else
                        $persona = $this->persona_model->insertar_datosPersona($ubigeo_nacimiento,$ubigeo_domicilio,$estado_civil,$nacionalidad,$nombres,$paterno,$materno,$ruc_persona,$tipo_documento,$numero_documento,$direccion,$telefono,$movil,$email,$direccion,$sexo,$web,$ctactesoles,$ctactedolares);

                    $this->proveedor_model->insertar_datosProveedor($empresa,$persona,$tipo_persona);
            }
        
        }
    }


}
?>