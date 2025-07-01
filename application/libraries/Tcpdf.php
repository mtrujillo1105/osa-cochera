<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once APPPATH."/third_party/TCPDF/Tcpdf.php";
 
class pdf extends Tcpdf {
    public function __construct(){
        parent::__construct(); 
    }

    public function Header( $flagPdf = 1 ){
        $this->Image("images/cabeceras/au.jpg", 10, 15, 40, 30, '', '', '', true, 300, '', false, false, 0);
    }
}

class pdfGeneral extends Tcpdf {
    
    protected $ci;
    private $ruc;
    private $doc;
    private $serie;
    private $numero;
    private $fondo;
    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
        $this->ci->load->model('maestros/persona_model');
        $this->compania = $_SESSION["compania"];
        $this->empresa = $_SESSION["empresa"];
        //$this->usuario = $_SESSION["usuario"];
        $this->persona = $_SESSION["persona"];
        $this->fondo = "images/img_db/comprobante_general_".$this->compania.".jpg";
    }

    public function Header( $flagPdf = 1 ){
        $this->SetAutoPageBreak(false, 0);
        $this->Image($this->fondo, 0, 0, 210, 297, '', '', '', true, 300, '', false, false, 0);
        $this->SetAutoPageBreak(true, 5);

        $this->printHeaderData();
    }

    public function settingHeaderData($ruc, $doc, $serie, $numero){
        $this->ruc = $ruc;
        $this->doc = $doc;
        $this->serie = $serie;
        $this->numero = $numero;
    }

    public function printHeaderData(){
        $posY = 15;
        $posX = 139;
        $this->SetY($posY + 4);
        $this->SetX($posX);

        $rucHTML = ($this->ruc != NULL) ? '<tr>
                                            <td style="">R.U.C. '.$this->ruc.'</td>
                                        </tr>' : '';

            $serie = ($this->numero != NULL) ? $this->serie.' - '.$this->numero : $this->serie;


        $comprobanteHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            '.$rucHTML.'
                            <tr>
                                <td>'.$this->doc.'</td>
                            </tr>
                            <tr>
                                <td>'.$serie.'</td>
                            </tr>
                        </table>';

        $posI = $this->getY();
        $this->writeHTML($comprobanteHTML,true,false,true,'');
        $posF = $this->getY();

        $this->RoundedRect($posX, $posY, 60, $posF-$posI, 1.50, '1111', '');
    }

    public function Footer(){
        $this->SetFont('freesans', '', 7);
        $this->SetY(-9);
        $pieHTML = '
            <table border="0" cellpadding="0.1cm">
                <tr>
                    <td style="text-align:center;">Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages().'</td>
                </tr>
            </table>';

        $this->writeHTML($pieHTML,false,'');
        $this->SetY(55);
    }
}

class pdfCotizacion extends Tcpdf {

    protected $ci;
    private $ruc;
    private $doc;
    private $serie;
    private $numero;
    private $fondo;
    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
        $this->ci->load->model('maestros/persona_model');
        $this->compania = $_SESSION["compania"];
        $this->empresa = $_SESSION["empresa"];
        //$this->usuario = $_SESSION["usuario"];
        $this->persona = $_SESSION["persona"];
        $this->fondo = "images/img_db/comprobante_general_".$this->compania.".jpg";
    }

    public function Header( $flagPdf = 1 ){
        $this->SetAutoPageBreak(false, 0);
        $this->Image($this->fondo, 0, 0, 210, 297, '', '', '', true, 300, '', false, false, 0);
        $this->SetAutoPageBreak(true, 5);

        $this->printHeaderData();
    }

    public function settingHeaderData($ruc, $doc, $serie, $numero){
        $this->ruc = $ruc;
        $this->doc = $doc;
        $this->serie = $serie;
        $this->numero = $numero;
    }

    public function printHeaderData(){
        $posY = 15;
        $posX = 139;
        $this->RoundedRect($posX, $posY, 60, 35, 1.50, '1111', '');
        $this->SetY($posY + 4);
        $this->SetX($posX);

        $rucHTML = ($this->ruc != NULL) ? '<tr>
                                            <td style="">R.U.C. '.$this->ruc.'</td>
                                        </tr>' : '';

        $comprobanteHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            '.$rucHTML.'
                            <tr>
                                <td style="">'.$this->doc.'</td>
                            </tr>
                            <tr>
                                <td style="">'.$this->serie.' - '.$this->numero.'</td>
                            </tr>
                        </table>';

        $this->writeHTML($comprobanteHTML,true,false,true,'');
    }

    public function Footer( $personal = NULL ){
                $this->SetFont('freesans', '', 7);
        $this->SetY(-9);
        $pieHTML = '
            <table border="0" cellpadding="0.1cm">
                <tr>
                    <td style="text-align:center;">Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages().'</td>
                </tr>
            </table>';

        $this->writeHTML($pieHTML,false,'');
        $this->SetY(55);

        /* $personal = $this->ci->persona_model->obtener_datosPersona($personal);

        $this->SetFont('freesans', '', 8);
        $this->SetY(-30);
        $pieHTML = '
            <table border="0" cellpadding="0.1cm">
                <tr bgcolor="#F6F6F6">
                    <td style="border-right: 1px #000 solid; font-weight:bold; text-align:center;">Elaboró</td>
                    <td style="border-right: 1px #000 solid; font-weight:bold; text-align:center;">Teléfonos</td>
                    <td style="border-right: 1px #000 solid; font-weight:bold; text-align:center;">Móvil</td>
                    <td style="font-weight:bold; text-align:center;">Email</td>
                </tr>
                <tr>
                    <td style="text-align:center;">'.$personal[0]->PERSC_Nombre.' '.$personal[0]->PERSC_ApellidoPaterno.' '.$personal[0]->PERSC_ApellidoMaterno.'</td>
                    <td style="text-align:center;">'.$personal[0]->PERSC_Telefono.'</td>
                    <td style="text-align:center;">'.$personal[0]->PERSC_Movil.'</td>
                    <td style="text-align:center;">'.$personal[0]->PERSC_Email.'</td>
                </tr>
            </table>';

        $this->writeHTML($pieHTML,false,'');
        */
        $this->SetY(55);
    }
}

class pdfComprobante extends Tcpdf {

    protected $ci;
    private $ruc;
    private $doc;
    private $serie;
    private $numero;
    private $fondo;
    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct(){
        parent::__construct();
        $this->ci =& get_instance();
        $this->compania = $_SESSION["compania"];
        $this->empresa = $_SESSION["empresa"];
        //$this->usuario = $_SESSION["usuario"];
        $this->persona = $_SESSION["persona"];
        $this->fondo = "public/images/docs/comprobante_general_".$this->compania.".jpg";
    }

    public function Header( $flagPdf = 1 ){
        if ($flagPdf == 1){
            $this->SetAutoPageBreak(false, 0);
            $this->Image($this->fondo, 0, 0, 210, 297, '', '', '', true, 300, '', false, false, 0);
            $this->SetAutoPageBreak(true, 40);
        }
        
        $this->printHeaderData();
    }

    public function settingHeaderData($ruc, $doc, $serie, $numero){
        $this->ruc = $ruc;
        $this->doc = $doc;
        $this->serie = $serie;
        $this->numero = $numero;
    }

    public function printHeaderData(){
        $posY = 15;
        $posX = 139;
        $this->RoundedRect($posX, $posY, 60, 35, 1.50, '1111', '');
        $this->SetY($posY + 4);
        $this->SetX($posX);

        $rucHTML = ($this->ruc != NULL) ? '<tr>
                                            <td style="">R.U.C. '.$this->ruc.'</td>
                                        </tr>' : '';

        $comprobanteHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            '.$rucHTML.'
                            <tr>
                                <td style="">'.$this->doc.'</td>
                            </tr>
                            <tr>
                                <td style="">'.$this->serie.' - '.$this->numero.'</td>
                            </tr>
                        </table>';

        $this->writeHTML($comprobanteHTML,true,false,true,'');
    }
}

class pdfGarantiaComprobante extends Tcpdf {

    protected $ci;
    private $ruc;
    private $doc;
    private $serie;
    private $numero;
    private $fondo;
    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct(){
        parent::__construct();
        $this->ci =& get_instance();

        $this->ci->load->model('maestros/persona_model');
        $this->compania = $_SESSION["compania"];
        $this->empresa = $_SESSION["empresa"];
        //$this->usuario = $_SESSION["usuario"];
        $this->persona = $_SESSION["persona"];
        $this->fondo = "images/img_db/garantia".$this->compania.".jpg";
    }

    public function Header( $flagPdf = 1 ){
        $this->SetAutoPageBreak(false, 0);
        $this->Image($this->fondo, 0, 0, 210, 297, '', '', '', true, 300, '', false, false, 0);
        $this->SetAutoPageBreak(true, 5);
    }

    public function settingHeaderData($ruc, $doc, $serie, $numero){
        $this->ruc = $ruc;
        $this->doc = $doc;
        $this->serie = $serie;
        $this->numero = $numero;
    }

    public function printHeaderData(){
        $posY = 15;
        $posX = 139;
        $this->RoundedRect($posX, $posY, 60, 35, 1.50, '1111', '');
        $this->SetY($posY + 4);
        $this->SetX($posX);

        $rucHTML = ($this->ruc != NULL) ? '<tr>
                                            <td style="">R.U.C. '.$this->ruc.'</td>
                                        </tr>' : '';

        $comprobanteHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            '.$rucHTML.'
                            <tr>
                                <td style="">'.$this->doc.'</td>
                            </tr>
                            <tr>
                                <td style="">'.$this->serie.' - '.$this->numero.'</td>
                            </tr>
                        </table>';

        $this->writeHTML($comprobanteHTML,true,false,true,'');
    }

    public function Footer(){
        $this->SetFont('freesans', '', 7);
        $this->SetY(-9);
        $pieHTML = '
            <table border="0" cellpadding="0.1cm">
                <tr>
                    <td style="text-align:center;">Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages().'</td>
                </tr>
            </table>';

        #$this->writeHTML($pieHTML,false,'');
        $this->SetY(55);
    }
}

class pdfGuiaRemision extends Tcpdf {

    protected $ci;
    private $ruc;
    private $doc;
    private $serie;
    private $numero;
    private $fondo;
    private $compania;
    private $empresa;
    private $usuario;
    private $persona;

    public function __construct(){
        parent::__construct(); 
        $this->ci =& get_instance();
        $this->ci->load->library('lib_props');
        $this->compania = $_SESSION["compania"];
        $this->empresa = $_SESSION["empresa"];

        $this->persona = $_SESSION["persona"];
        $this->fondo = "images/img_db/comprobante_general_".$this->compania.".jpg";
    }

    public function Header(){
        $this->SetAutoPageBreak(false, 0);
        $this->Image($this->fondo, 0, 0, 210, 297, '', '', '', true, 300, '', false, false, 0);

        $this->printHeaderData();
    }

    public function settingHeaderData($ruc, $doc, $serie, $numero){
        $this->ruc = $ruc;
        $this->doc = $doc;
        $this->serie = $serie;
        $this->numero = $numero;
    }

    public function printHeaderData(){
        $posY = 15;
        $posX = 130;
        $this->RoundedRect($posX, $posY, 60, 35, 1.50, '1111', '');
        $this->SetY($posY + 4);
        $this->SetX($posX);

        $guiaRemHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            <tr>
                                <td style="">R.U.C. '.$this->ruc.'</td>
                            </tr>
                            <tr>
                                <td style="">'.$this->doc.'</td>
                            </tr>
                            <tr>
                                <td style="">'.$this->serie.' - '.$this->numero.'</td>
                            </tr>
                        </table>';

        $this->writeHTML($guiaRemHTML,true,false,true,'');
    }
}

?>