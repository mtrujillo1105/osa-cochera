<?php
ini_set('error_reporting', 1);

include("system/application/libraries/cezpdf.php");
include("system/application/libraries/class.backgroundpdf.php");
class Prestamo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('date');
        $this->load->helper('util');
        $this->load->helper('utf_helper');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('html');
        $this->load->library('form_validation');
        $this->load->model('maestros/linea_model');
        $this->load->model('tesoreria/prestamo_model');
        $this->load->model('maestros/moneda_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
    }
    public function listar($j='0')
    {
        
        $data['nombre_linea']  = "";
        $data['registros']   = count($this->prestamo_model->listar());
        $conf['base_url']    = site_url('tesoreria/prestamo/listar/');
        $conf['total_rows']  = $data['registros'];
        $conf['per_page']    = 10;
        $conf['num_links']   = 3;
        $conf['next_link']   = "&gt;";
        $conf['prev_link']   = "&lt;";
        $conf['first_link']  = "&lt;&lt;";
        $conf['last_link']   = "&gt;&gt;";
        $conf['uri_segment'] = 4;
        $offset              = (int)$this->uri->segment(4);
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $listado            = $this->prestamo_model->listar($conf['per_page'],$offset);
        $item               = $j+1;
        $lista              = array();
        if(count($listado)>0){
        foreach($listado as $indice=>$valor)
            {
                $codigo         = $valor->PRES_Codigo;
                $moneda         = $valor->MONED_Descripcion;
                $monto          = $valor->MONED_Simbolo . '  '. $valor->PRES_Precio;
                $editar         = "<a href='#' onclick='editar_prestamo(".$codigo.")'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver            = "<a href='javascript:;' onclick='ver_presupuesto_ver_pdf_conmenbrete(" . $codigo . ",1)' target='_parent'><img src='" . base_url() . "images/imprimir.png' width='16' height='16' border='0' title='Imprimir'></a>";
                $eliminar       = "<a href='#' onclick='eliminar_presupuesto(".$codigo.")'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[]        = array($item++,$valor->PRES_Nombre . '     ' . $valor->PRES_Apellido,$valor->PRES_Dni,$moneda,$monto,$editar,$ver,$eliminar);
            }
        }
        $data['lista']            = $lista;
        $data['titulo_busqueda']  = "BUSCAR PRESTAMO";
        $data['nombre_linea'] = form_input(array( 'name'  => 'nombre_linea','id' => 'nombre_linea','value' => '','maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']        = form_open(base_url().'index.php/maestros/linea/buscar',array("name"=>"form_busquedaLinea","id"=>"form_busquedaLinea"));
        $data['form_close']       = form_close();
        $data['titulo_tabla']     = "Relaci&oacute;n DE PRESTAMOS";
        $data['oculto']           = form_hidden(array('accion'=>"",'codigo'=>"",'modo'=>"insertar",'base_url'=>base_url()));
        $this->layout->view('tesoreria/prestamo_index',$data);
            
    }
    public function nueva()
    {
        
        $data['nombre'] = "";
        $data['dni'] = "";
        $data['cargo'] = "";
        $data['forma'] = "";
        $data['valor'] = "";
        $data['apellido'] = "";
        $data['observacion'] = "";
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', '1');
        $data['titulo']     = "PRESTAMO PERSONAL";
        $data['form_open']  = form_open(base_url().'index.php/tesoreria/prestamo/grabar',array("name"=>"frmLinea","id"=>"frmLinea"));
        $data['hoy'] = mysql_to_human(mdate("%Y-%m-%d ", time()));
        $data['form_close'] = form_close();
        $data['oculto']     = form_hidden(array('codigo'=>"",'base_url'=>base_url(),'prestamo_id'=>''));
        $data['onload']     = "onload=\"$('#nombres').focus();\"";
        $this->layout->view('tesoreria/prestamo_nuevo',$data);
    }
    public function editar($id)
    {
        
        $datos_prestamo                 = $this->prestamo_model->obtener($id);
        $nombre = $datos_prestamo[0]->PRES_Nombre;
        $apellido = $datos_prestamo[0]->PRES_Apellido;
        $dni = $datos_prestamo[0]->PRES_Dni;
        $cargo = $datos_prestamo[0]->PRES_Cargo;
        $forma = $datos_prestamo[0]->PRES_Forma;
        $valor = $datos_prestamo[0]->PRES_Precio;
        $observacion = $datos_prestamo[0]->PRES_Observacion;
        $moneda = $datos_prestamo[0]->MONED_Codigo;
        $hoy = $datos_prestamo[0]->PRES_Fecha;
        $data['nombre'] = $nombre;
        $data['apellido'] = $apellido;
        $data['dni'] = $dni;
        $data['cargo'] = $cargo;
        $data['forma'] = $forma;
        $data['valor'] = $valor;
        $data['hoy'] = $hoy;
        $data['observacion'] = $observacion;
        $data['cboMoneda'] = $this->OPTION_generador($this->moneda_model->listar(), 'MONED_Codigo', 'MONED_Descripcion', $moneda);    
        $data['form_open']      = form_open(base_url().'index.php/tesoreria/prestamo/grabar/',array("name"=>"frmLinea","id"=>"frmLinea"));
        $data['oculto']         = form_hidden(array('codigo'=>"",'base_url'=>base_url(),'prestamo_id'=>$id));
        $data['form_hidden']    = form_hidden("base_url",base_url());
        $data['form_close']     = form_close();
        $data['titulo']  = "EDITAR PRESTAMOS PERSONAL";
        $this->layout->view('tesoreria/prestamo_nuevo',$data);
    }
    public function grabar()
    {
        $this->form_validation->set_rules('nombre','Nombre','required');
        if($this->form_validation->run() == FALSE){
            $this->nuevo();
        }
        else{
            $nombre  = $this->input->post("nombre");
            $dni  = $this->input->post("dni");
            $cargo  = $this->input->post("cargo");
            $forma  = $this->input->post("forma");
            $valor  = $this->input->post("valor");
            $prestamo_id   = $this->input->post("prestamo_id");

            $filter = new stdClass();
            $filter->PRES_Nombre = strtoupper($nombre);
            $filter->PRES_Dni = $dni;
            $filter->PRES_Cargo = $cargo;
            $filter->PRES_Forma = $forma;
            $filter->PRES_Precio = $valor;
            $filter->MONED_Codigo = $this->input->post('moneda');
            $filter->PRES_FlagEstado=1;
            $filter->PRES_Apellido = $this->input->post('apellido');
            $filter->PRES_Observacion = strtoupper($this->input->post('observacion'));
            $filter->PRES_Fecha = human_to_mysql($this->input->post('fecha'));

            if(isset($prestamo_id) && $prestamo_id>0){
              $this->prestamo_model->modificar($prestamo_id,$filter);
            }
            else{
               $this->prestamo_model->insertar($filter);
            }
            header("location:".base_url()."index.php/tesoreria/prestamo/listar");
        }
    }
    public function eliminar()
    {
        $id = $this->input->post('linea');
        $this->prestamo_model->eliminar($id);
    }
    public function ver($codigo)
    {
        
        $datos_linea       = $this->linea_model->obtener($codigo);
        $data['nombre_linea']= $datos_linea[0]->LINC_Descripcion;
        $data['linea']= $datos_linea[0]->LINP_Codigo;
        $data['titulo']        = "VER LINEA";
        $data['oculto']        = form_hidden(array('base_url'=>base_url()));
        $this->layout->view('maestros/linea_ver',$data);
    }
    public function presupuesto_ver_pdf_conmenbrete($codigo, $img) {
        switch (FORMATO_IMPRESION) {
            case 1:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
            case 2:  //Formato para jimmyplat
                $this->presupuesto_ver_pdf_conmenbrete_formato2($codigo);
                break;
            case 3:  //Formato para instrumentos y systemas
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img);
                break;
            case 4:  //Formato para ferremax
                $this->presupuesto_ver_pdf_conmenbrete_formato4($codigo);
                break;
            case 5:
                if ($_SESSION['compania'] == "1") {
                    $this->presupuesto_ver_pdf_conmenbrete_formato5($codigo); //Formato para CYG
                } else {
                    $this->presupuesto_ver_pdf_conmenbrete_formato6($codigo); //Formato para CYG ELECTRO DATA
                }
                break;
            case 6:
                $this->presupuesto_ver_pdf_conmenbrete_formato3($codigo, $img); //Formato para CYL
                break;
            default:
                $this->presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img);
                break;
        }
    }
    public function presupuesto_ver_pdf_conmenbrete_formato1($codigo, $img) {


        //////SIN DOCUMENTO  STV

        $datos_prestamo                 = $this->prestamo_model->obtener($codigo);
        $nombre = $datos_prestamo[0]->PRES_Nombre;
        $apellido = $datos_prestamo[0]->PRES_Apellido;
        $dni = $datos_prestamo[0]->PRES_Dni;
        $cargo = $datos_prestamo[0]->PRES_Cargo;
        $forma = $datos_prestamo[0]->PRES_Forma;
        $valor = $datos_prestamo[0]->PRES_Precio;
        $observacion = $datos_prestamo[0]->PRES_Observacion;
        $fecha = mysql_to_human($datos_prestamo[0]->PRES_Fecha);
        $datos_moneda = $this->moneda_model->obtener($datos_prestamo[0]->MONED_Codigo);
        $moneda_nombre = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Descripcion : 'NUEVOS SOLES');
        $moneda_simbolo = (count($datos_moneda) > 0 ? $datos_moneda[0]->MONED_Simbolo : 'S/.');


    
        $this->cezpdf = new Cezpdf('a4');
        $this->cezpdf = new backgroundPDF('a4', 'portrait', 'image', array('img' => 'images/documentos/prestamo.jpg'));
        $datacreator = array(
            'Title' => 'Estadillo de ',
            'Name' => 'Estadillo de ',
            'Author' => 'gian carlos',
            'Subject' => 'PDF con Tablas',
            'Creator' => 'ccapaempresas.com',
            'Producer' => 'ccapaempresas.com'
        );


 
        $this->cezpdf->ezText('', '', array("leading" => 180));

        /* Datos del cliente */
        $db_data = array(array('cols1' => '','cols2' => $nombre),
                    array('cols1' => '','cols2' => $apellido),
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '280',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));

        $this->cezpdf->ezText('', 20);
         $db_data = array(array('cols1' => '','cols2' => $dni),
                    
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '320',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));
        $this->cezpdf->ezText('', 45);
         $db_data = array(array('cols1' => '','cols2' => $cargo),
                    
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '320',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));
        $this->cezpdf->ezText('', 50);
         $db_data = array(array('cols1' => '','cols2' => $moneda_simbolo . '  ' . $valor),
                    
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '320',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));
        $this->cezpdf->ezText('', 40);
         $db_data = array(array('cols1' => '','cols2' => $forma),
                    
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '320',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));
        $this->cezpdf->ezText('', 45);
         $db_data = array(array('cols1' => '','cols2' => $fecha),
                    
        );

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '345',
            'fontSize' => 12,
            'cols' => array(
                'cols1' => array('width' => 45, 'justification' => 'left'),
                'cols2' => array('width' => 380, 'justification' => 'left'),
                
            )
        ));
        /* Listado de detalles */

       /* $this->cezpdf->ezText('', '');

        /* Totales */
        $this->cezpdf->ezText('', 30);
        $db_data = array(array('cols0' => '<b> ' . strtoupper(num2letras(round($valor, 2))) . ' ' . $moneda_nombre ),
                array('cols0' => '', 'cols1' => '', 'cols2' => '')
            );
        

        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '410',
            'fontSize' => 7,
            'cols' => array(
                'cols0' => array('width' => 330, 'justification' => 'left'),
               
            )
        ));

        
        /* Condiciones de venta */
      /*   $db_data = array(array('cols0' => '<b>CONDICIONES DE VENTA:</b>', 'cols1' => ''),
            array('cols0' => utf8_decode_seguro('Tipo de Cambio del DÃ­a'), 'cols1' => ': ' . ($tipo_cambio > 0 ? round($tipo_cambio, 2) : '')),
            array('cols0' => 'Moneda', 'cols1' => ': ' . $moneda_nombre),
            array('cols0' => 'Forma de Pago', 'cols1' => ': ' . utf8_decode_seguro($forma_pago)),
            array('cols0' => 'Los Precios de los Productos ', 'cols1' => ': ' . ($modo_impresion == '1' ? 'CONTIENEN IGV' : 'NO CONTIENEN IGV')),
            array('cols0' => 'Tiempo de Entrega', 'cols1' => ': ' . $tiempo_entrega),
            array('cols0' => 'Lugar de Entrega', 'cols1' => ': ' . utf8_decode_seguro($lugar_entrega)),
            //array('cols0' => utf8_decode_seguro('Garantía'), 'cols1' => ': ' . utf8_decode_seguro($garantia)),
            array('cols0' => 'Validez de la Oferta', 'cols1' => ': ' . utf8_decode_seguro($validez)),
            array('cols0' => 'Contacto', 'cols1' => ': ' . utf8_decode_seguro($vendedor_nombre . ($vendedor_nombre_area != '' ? ' - AREA: ' . $vendedor_nombre_area : '')))
        );
        
        $this->cezpdf->ezText('', 15);
        $this->cezpdf->ezTable($db_data, "", "", array(
            'width' => 525,
            'showLines' => 0,
            'shaded' => 0,
            'showHeadings' => 0,
            'xPos' => '360',
            'fontSize' => 8,
            'cols' => array(
                'cols0' => array('width' => 120, 'justification' => 'left'),
                'cols1' => array('width' => 415, 'justification' => 'left'),
            )
        ));*/

     

        $cabecera = array('Content-Type' => 'application/pdf', 'Content-Disposition' => $codificacion . '.pdf', 'Expires' => '0', 'Pragma' => 'cache', 'Cache-Control' => 'private');
        $this->cezpdf->ezStream($cabecera);
        ///////////////
    }
    public function buscar($j=0)
    {
        $this->load->library('layout','layout');
        $nombre_linea = $this->input->post('nombre_linea');
        $filter = new stdClass();
        $filter->LINC_Descripcion = $nombre_linea;
        $data['registros']      = count($this->linea_model->buscar($filter));
        $conf['base_url']       = site_url('almacen/almacen/buscar/');
        $conf['total_rows']     = $data['registros'];
        $conf['per_page']       = 10;
        $conf['num_links']      = 3;
        $conf['first_link']     = "&lt;&lt;";
        $conf['last_link']      = "&gt;&gt;";
        $offset                 = (int)$this->uri->segment(4);
        $listado                = $this->linea_model->buscar($filter,$conf['per_page'],$offset);
        $item                   = $j+1;
        $lista                  = array();
        if(count($listado)>0){
            foreach($listado as $indice=>$valor){
                $codigo       = $valor->LINP_Codigo;
                $editar       = "<a href='#' onclick='editar_prestamo(".$codigo.")' target='_parent'><img src='".base_url()."images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $ver          = "<a href='#' onclick='ver_linea(".$codigo.")' target='_parent'><img src='".base_url()."images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
                $eliminar     = "<a href='#' onclick='eliminar_linea(".$codigo.")' target='_parent'><img src='".base_url()."images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
                $lista[]      = array($item++,$valor->LINC_Descripcion,$valor->LINC_CodigoUsuario,$editar,$ver,$eliminar);
            }
        }
        $data['titulo_tabla']    = "RESULTADO DE BUSQUEDA de LINEAS";
        $data['titulo_busqueda'] = "BUSCAR LINEA";
        $data['nombre_linea']  = form_input(array( 'name'  => 'nombre_linea','id' => 'nombre_linea','value' => $nombre_linea,'maxlength' => '100','class' => 'cajaMedia'));
        $data['form_open']       = form_open(base_url().'index.php/maestros/linea/buscar',array("name"=>"form_busquedaLinea","id"=>"form_busquedaLinea"));
        $data['form_close']      = form_close();
        $data['lista']           = $lista;
        $data['oculto']          = form_hidden(array('base_url'=>base_url()));
        $this->pagination->initialize($conf);
        $data['paginacion'] = $this->pagination->create_links();
        $this->layout->view('maestros/linea_index',$data);
    }
}
?>