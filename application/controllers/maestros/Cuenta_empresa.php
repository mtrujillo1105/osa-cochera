<?php
class Cuenta_empresa extends CI_Controller{
    public function __construct(){
            parent::Controller();

    $this->load->model('empresa/empresa_model'); 
	  $this->load->model('maestros/compania_model');
    $this->load->model('empresa/cuentaempresa_model');
  
    $this->load->model('maestros/almacen_model');
    $this->load->model('maestros/persona_model'); 
    $this->load->model('maestros/tipoestablecimiento_model');
    $this->load->model('maestros/ubigeo_model');
    $this->load->model('empresa/directivo_model');
    $this->load->model('maestros/cargo_model');
    $this->load->model('maestros/area_model');
    $this->load->model('maestros/estadocivil_model');
    $this->load->model('maestros/nacionalidad_model');
    $this->load->model('maestros/tipocodigo_model');
    $this->load->model('maestros/tipodocumento_model');
    $this->load->model('maestros/sectorcomercial_model');
    $this->load->model('maestros/formapago_model');
    $this->load->model('empresa/proveedor_model');
    $this->load->model('tesoreria/tipocaja_model');
    $this->load->library('html');
    $this->load->library('pagination');	
    $this->load->library('layout','layout');
    $this->load->helper('json');
    }
    public function index(){
            $this->layout->view('seguridad/inicio');	
    }
public function nuevo_cuentaempresa($codigo=null,$identif=null){

        $data["listBanco"]=$this->empresa_model->listBanco();
        $data['listMoneda']=$this->empresa_model->listMoneda();
        $data['listSerie']=$this->empresa_model->listSerie();
        $data['listado_cuentaEmpresa']= $this->cuentaempresa_model->listCuentaPersona($codigo);  
        $this->load->view("empresa/cuentaempresa_nuevo",$data);
    }
  public function insert_cuantasEmpresa(){     
        $filter = new stdClass();
        $filter->EMPRE_Codigo=$this->input->post("empresa_persona");
        $filter->PERSP_Codigo=$this->input->post("personaCodigo");
        $filter->BANP_Codigo=$this->input->post("txtBanco");
        $filter->MONED_Codigo=$this->input->post("txtMoneda");
        $filter->CUENT_NumeroEmpresa=$this->input->post("txtCuenta");
        $filter->CUENT_Titular=$this->input->post("txtTitular");
        $filter->CUENT_TipoCuenta=$this->input->post("txtTipoCuenta");
        $filter->CUENT_TipoPersona=$this->input->post("TIP_Codigo");
        $filter->CUENT_Oficina=$this->input->post("txtOficina");
        $filter->CUENT_Sectoriza=$this->input->post("txtSectoriza");
        $filter->CUENT_Interbancaria=$this->input->post("txtInterban");
        $filter->CUENT_FechaRegistro=mdate("%Y-%m-%d ", time());
        $filter->CUENT_UsuarioRegistro=$this->session->userdata('user');
        $filter->CUENT_FlagEstado="1";
        $this->empresa_model->insertCuentaEmpresa($filter);
  
       }

public function update_cuantasEmpresa(){
        $codigo=$this->input->post("txtCodCuenEmpre");
        $filter = new stdClass();
        $filter->EMPRE_Codigo=$this->input->post("empresa_persona");
        $filter->PERSP_Codigo=$this->input->post("personaCodigo");
        $filter->BANP_Codigo=$this->input->post("txtBanco");
        $filter->MONED_Codigo=$this->input->post("txtMoneda");
        $filter->CUENT_NumeroEmpresa=$this->input->post("txtCuenta");
        $filter->CUENT_Titular=$this->input->post("txtTitular");
        $filter->CUENT_TipoCuenta=$this->input->post("txtTipoCuenta");
        $filter->CUENT_TipoPersona=$this->input->post("TIP_Codigo");
        $filter->CUENT_Oficina=$this->input->post("txtOficina");
        $filter->CUENT_Sectoriza=$this->input->post("txtSectoriza");
        $filter->CUENT_Interbancaria=$this->input->post("txtInterban");
        $filter->CUENT_FechaModificacion=mdate("%Y-%m-%d ", time());
        $filter->CUENT_UsuarioModificaion=$this->session->userdata('user');
        $filter->CUENT_FlagEstado="1";
        $this->empresa_model->UpdateCuentaEmpresa($codigo,$filter);
}
//buscar para actualizar data
public function JSON_listCuentaEmpresaEditar($codigo){
    $lista_detalles = array();
    $dataCuenta= $this->empresa_model->listCuentaEmpresaCodigo($codigo);
    if(count($dataCuenta)>0){
      foreach ($dataCuenta as $key => $value) {
      $objeto = new stdClass();
      $objeto->CUENT_Codigo        =$value->CUENT_Codigo;
      $objeto->CUENT_NumeroEmpresa =$value->CUENT_NumeroEmpresa;
      $objeto->CUENT_Titular       =$value->CUENT_Titular;
      $objeto->CUENT_TipoPersona   =$value->CUENT_TipoPersona;
      $objeto->CUENT_FechaRegistro =$value->CUENT_FechaRegistro;
      $objeto->BANC_Nombre         =$value->BANC_Nombre;
      $objeto->MONED_Descripcion   =$value->MONED_Descripcion;
      $objeto->CUENT_TipoCuenta    =$value->CUENT_TipoCuenta;
      $objeto->BANC_Selec=$this->seleccionar_banco($value->BANP_Codigo);
      $lista_detalles[] = ($objeto);          
    
    }  
  
    }

    $resultado[] = array();
    $resultado = json_encode($lista_detalles,JSON_NUMERIC_CHECK);
    echo  $resultado;

}

public function JSON_EliminarCuentaEmpresa($codigo){
    $this->empresa_model->eliminar_cuentaEmpresa($codigo);
}

public function TABLA_cuentaEmpresa($codigo,$tipo="", $number_items = '', $offset = ''){
   //E EDENTIFICA PARA EDITAR EL CONTENIDO
$dataCuentaEditar= $this->empresa_model->listCuentaEmpresaCodigo($codigo);
  if($tipo=="E"){
    if(count($dataCuentaEditar)>0){
    foreach ($dataCuentaEditar as $key => $value) {
        $tabla='<table id="tableData" border="0" class="fuente8" width="98%" cellspacing="0" cellpadding="6">
             <tr> <td>Banco</td><td>
            <select id="txtBanco" name="txtBanco" autofocus>' ;
      $tabla.=$this->seleccionar_banco($value->BANP_Codigo);
      $tabla.='</select ></td><td>N° Cuenta</td>
  <td><input type="text" id="txtCuenta" name="txtCuenta" value="'.$value->CUENT_NumeroEmpresa.'" onkeypress="return soloLetras_andNumero(event)"></td>
  <td>Titular</td>
  <td><input type="text" id="txtTitular" name="txtTitular" value="'.$value->CUENT_Titular.'" onkeypress="return soloLetras_andNumero(event)"></td> 
<tr>
<td>Oficina (*)</td>
<td><input type="text" name="txtOficina" id="txtOficina" onkeypress="return soloLetras_andNumero(event)" value="'.$value->CUENT_Oficina.'"></td>
<td>Sectoriza (*)</td>
<td><input type="text" name="txtSectoriza" id="txtSectoriza" onkeypress="return soloLetras_andNumero(event)" value="'.$value->CUENT_Sectoriza.'"></td>
<td>Interbancaria (*)</td>
<td><input type="text" name="txtInterban" id="txtInterban" onkeypress="return soloLetras_andNumero(event)" value="'.$value->CUENT_Interbancaria.'"></td>
</tr> 
  </tr><tr><td>Tipo de Cuenta</td><td>';
$tabla.='<select name="txtTipoCuenta" id="txtTipoCuenta" required="required">';
$tabla.='<option value="S">::SELECCIONE::</option>';
if($value->CUENT_TipoCuenta==1){
$tabla.='<option value="1"  selected="selected" >Ahorros</option>';
$tabla.='<option value="2" >Corriente</option>'; 
 }elseif ($value->CUENT_TipoCuenta==2) {
  $tabla.='<option value="1"   >Ahorros</option>';
$tabla.='<option value="2" selected="selected" >Corriente</option>'; 
 }
$tabla.='</select>
</td>
  <td>Moneda</td>
  <td>
    <select id="txtMoneda" name="txtMoneda" >';
$tabla.=$this->seleccionar_Moneda($value->MONED_Codigo);
$tabla.='</select></td> <td></td><td>' ;
$tabla.='<input type="hidden" id="txtCodCuenEmpre" name="txtCodCuenEmpre" value="'.$value->CUENT_Codigo.'">
<a href="#" id="btnInsertarCuentaE" onclick="insertar_cuentaEmpresa()">
  <img src='.base_url().'images/botonagregar.jpg></a>
  <a href="#" id="btnCancelarCuentaE" onclick="limpiar_cuentaEmpresa()">
  <img src='.base_url().'images/botoncancelar.jpg></a>
 </td> </tr><tr><td colspan="6">campos obligatorios (*)</td></tr></table>' ;
    echo $tabla;
  }
}
}
 else{
        
  $dataCuenta= $this->cuentaempresa_model->listCuentaPersona($codigo);

  $tabla='<table id="tableBancos" class="table table-bordered table-striped fuente8" width="98%" cellspacing="0" cellpadding="6" border="0"><thead>
        <tr align="center" class="cab1" height="10px;" style="background-color:#5F5F5F;color:#ffffff;font-weight: bold;">
        <td>Item</td>
        <td>Banco</td>
        <td>N° Cuenta</td>
        <td>Nombre o Titular de la cuenta</td>
        <td>Moneda</td>
        <td>Tipo de cuanta</td>
        <td colspan="3">Acciones</td></thead>
       </tr><tbody>'; 
   if(count($dataCuenta)>0){
      foreach ($dataCuenta as $key => $value) {
        $tabla.='<tr bgcolor="#ffffff">';
        $tabla.='<td align="center">'.($key+1).'</td>';
        $tabla.='<td align="left">'.$value->BANC_Nombre.'</td>';
        $tabla.='<td>'.$value->CUENT_NumeroEmpresa.'</td> ';              
        $tabla.='<td align="left">'.$value->CUENT_Titular.'</td>';
        $tabla.='<td align="left">'.$value->MONED_Descripcion.'</td>';
        if($value->CUENT_TipoCuenta==1){
            $tabla.='<td>Ahorros</td>';
        }else{
          $tabla.='<td>Corriente</td>';
        }
       
        $tabla.='<td align="center">';
        $tabla.='<a href="#" onclick="eliminar_cuantaEmpresa('.$value->CUENT_Codigo.');"><img src='.base_url().'images/delete.gif border="0"></a>';
        $tabla.='</td>';
        $tabla.='<td align="center">';
        $tabla.='<a href="#" id="btnAcualizarE" onclick="actualizar_cuentaEmpresa('.$value->CUENT_Codigo.');"><img src='.base_url().'images/modificar.png border="0"></a>';
       $tabla.='<td><a href="#" onclick="ventanaChekera('.$value->CUENT_Codigo.')"><img src='.base_url().'images/observaciones.png></a></td>
    ';
        $tabla.='</td>';
        $tabla.='</tr>';

      }
  }
    $tabla.='</tbody></table>';
    

echo $tabla;
}

 /* else{ 
    $cantidad= count($this->empresa_model->listCuentaEmpresa($codigo));
       
  $dataCuenta= $this->empresa_model->listCuentaEmpresa($codigo);
//$tabla='<div><script type="text/javascript" src="'.base_url().'js/jquery.js"></script>
   // <script type="text/javascript" src="'.base_url().'js/jquery-paginate.js"></div>';
  $tabla='<table id="tableBancos" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
        <tr align="center" class="cab1" height="10px;">
        <td>Item</td>
        <td>Banco</td>
        <td>N° Cuenta</td>
        <td>Nombre o Titular de la cuenta</td>
        <td>Moneda</td>
        <td>Tipo de cuanta</td>
        <td colspan="3">Acciones</td>
       </tr>'; 
   if(count($dataCuenta)>0){
      foreach ($dataCuenta as $key => $value) {
        $tabla.='<tr bgcolor="#ffffff">';
        $tabla.='<td align="center">'.($key+1).'</td>';
        $tabla.='<td align="left">'.$value->BANC_Nombre.'</td>';
        $tabla.='<td>'.$value->CUENT_NumeroEmpresa.'</td> ';              
        $tabla.='<td align="left">'.$value->CUENT_Titular.'</td>';
        $tabla.='<td align="left">'.$value->MONED_Descripcion.'</td>';
        if($value->CUENT_TipoCuenta==1){
            $tabla.='<td>Ahorros</td>';
        }else{
          $tabla.='<td>Corriente</td>';
        }
       
        $tabla.='<td align="center">';
        $tabla.='<a href="#" onclick="eliminar_cuantaEmpresa('.$value->CUENT_Codigo.');"><img src='.base_url().'images/delete.gif border="0"></a>';
        $tabla.='</td>';
        $tabla.='<td align="center">';
        $tabla.='<a href="#" id="btnAcualizarE" onclick="actualizar_cuentaEmpresa('.$value->CUENT_Codigo.');"><img src='.base_url().'images/modificar.png border="0"></a>';
       $tabla.='<td><a href="#" onclick="ventanaChekera('.$value->CUENT_Codigo.')"><img src='.base_url().'images/observaciones.png></a></td>
    ';
        $tabla.='</td>';
        $tabla.='</tr>';

      }
  }
    $tabla.='</table>';
    $tabla.='<script type="text/javascript" src="'.base_url().'js/jquery.js"></script>
    <script type="text/javascript" src="'.base_url().'js/jquery-paginate.js">';
echo $tabla;*/ 

}
 public function seleccionar_banco($indSel=''){
        $array_area = $this->empresa_model->listBanco();
        $arreglo = array();
        foreach($array_area as $indice=>$valor){
                $indice1   = $valor->BANP_Codigo;
                $valor1    = $valor->BANC_Nombre;
                $arreglo[$indice1] = $valor1;
        }
        $resultado = $this->html->optionHTML($arreglo,$indSel,array('S','::SELECCIONE::'));
        return $resultado;
    }
 public function seleccionar_Moneda($indSel=''){
        $array_area = $this->empresa_model->listMoneda();
        $arreglo = array();
        foreach($array_area as $indice=>$valor){
                $indice1   = $valor->MONED_Codigo;
                $valor1    = $valor->MONED_Descripcion;
                $arreglo[$indice1] = $valor1;
        }
        $resultado = $this->html->optionHTML($arreglo,$indSel,array('S','::SELECCIONE::'));
        return $resultado;
    }
public function seleccionar_TipoCuenta($indSel=''){
     } 

public function JSON_ListarCuentaEmpresa($codigo){
        $lista_detalles = array();
       $listDetalle= $this->empresa_model->listCuentaEmpresaCodigo($codigo);
    if(count($listDetalle)>0){ 
    foreach ($listDetalle as $key => $value) {
     $objeto = new stdClass();
      $objeto->CUENT_Codigo        =$value->CUENT_Codigo;
      $objeto->CUENT_NumeroEmpresa =$value->CUENT_NumeroEmpresa;
      $objeto->CUENT_Titular       =$value->CUENT_Titular;
      $objeto->CUENT_TipoPersona   =$value->CUENT_TipoPersona;
      $objeto->CUENT_FechaRegistro =$value->CUENT_FechaRegistro;
      $objeto->BANC_Nombre         =$value->BANC_Nombre;
      $objeto->MONED_Descripcion   =$value->MONED_Descripcion;
      $objeto->CUENT_TipoCuenta    =$value->CUENT_TipoCuenta;
      
      $lista_detalles[] = ($objeto);
   }
    $resultado[] = array();
    $resultado = json_encode($lista_detalles);
    echo  $resultado;
     }  
     }//final del metodo JSON_ListarCuentaEmpresa

//codigo para chekera
public function listarChikera($codigo){
    $lista_detalles = array();
       $listDetalle= $this->empresa_model->listChikera($codigo);
    if(count($listDetalle)>0){ 
    foreach ($listDetalle as $key => $value) {
      $objeto = new stdClass();
      $objeto->CHEK_Codigo        =$value->CHEK_Codigo;
      $objeto->CUENT_NumeroEmpresa =$value->CUENT_NumeroEmpresa;
      $objeto->CHEK_FechaRegistro  =mysql_to_human($value->CHEK_FechaRegistro);
      $objeto->SERIP_Codigo  =$value->SERIP_Codigo;
      $objeto->CHEK_Numero=$value->CHEK_Numero;
      $lista_detalles[] = ($objeto);
   }
    $resultado[] = array();
    $resultado = json_encode($lista_detalles,JSON_NUMERIC_CHECK);
    echo  $resultado;  
}    
}   
public function insertChekera(){
    $filter = new stdClass();
    $filter->SERIP_Codigo=$this->input->post("txtSerieChekera");
    $filter->CUENT_Codigo=$this->input->post("txtCodCuentaEmpre");
    $filter->EMPRP_Codigo=$this->input->post("empresa_persona");
    $filter->CHEK_Numero=$this->input->post("txtNumeroChek");
    //$filter->txtNumeroChek=$this->input->post("txtNumeroChek")
    $filter->PERSP_Codigo=0;
    $filter->CHEK_FechaRegistro=mdate("%Y-%m-%d ", time());
    $filter->CHEK_UsuarioRegistro=$this->session->userdata('user');
    $filter->CHEK_FlagEstado="1";
    $this->empresa_model->insertChekera($filter);
    } 

public function eliminarChikera($codigo){
    $this->empresa_model->delete_chikera($codigo);
}  

public function TABLE_listarChekera($codigo){
  $detalleChikera=  $this->empresa_model->listChikera($codigo);
    $table='<table id="tablechekera" width="100%"
 cellpadding="6" border="0" >
 <thead>
 <tr style="background-color:#5F5F5F;color:#ffffff;font-weight: bold;">
<td>Item</td><td>Fecha</td><td>Cuenta Empresa</td>
<td>Serie</td><td>Numero</td><td>Accion</td>
 </tr>
 </thead>
 <tbody id="listarChekera" style="color:black">  ';
 if(count($detalleChikera)>0){
    foreach ($detalleChikera as $key => $value) {
     $table.='<tr>';
     $table.='<td>'.($key+1).'</td>';
     $table.='<td>'.mysql_to_human($value->CHEK_FechaRegistro).'</td>';
     $table.='<td>'.$value->CUENT_NumeroEmpresa.'</td>';
     $table.='<td>'.$value->SERIP_Codigo .'</td>';
     $table.='<td>'.$value->CHEK_Numero. '</td>';
     $table.='<td><a href="#" onclick="eliminarChikera('.$value->CHEK_Codigo.')" ><img src='.base_url().'images/delete.gif ></a></td>';
     $table.='</tr>';
    }
 }
$table.='</tbody></table>';
echo $table;
} 
public function getBuscaCuenta($codigo){
  $data_model = $this->cuentaempresa_model->getBuscarNumCuenta($codigo);
  if (count($data_model )>0) {
     echo json_encode($data_model);
  }
 
}


}       
?>