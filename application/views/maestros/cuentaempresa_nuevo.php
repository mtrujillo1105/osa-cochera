<html>
  <head>  
   </script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
      <script type="text/javascript" src="<?=$base_url;?>public/js/empresa/empresa.js?=<?=JS;?>"></script>    
    <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>  
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>

  <script type="text/javascript">

    function cargar_ubigeo_complementario(departamento,provincia,distrito,valor,seccion,n){
      if(seccion=="sucursal"){
        a = "dptoSucursal["+n+"]";
        b = "provSucursal["+n+"]";
        c = "distSucursal["+n+"]";
        d = "distritoSucursal["+n+"]"
        document.getElementById(a).value = departamento;
        document.getElementById(b).value = provincia;
        document.getElementById(c).value = distrito;
        document.getElementById(d).value = valor;
      }
    }
    $(document).ready(function(){

    });
    </script>
                <style>
                    .cab1{
                        background-color: #5F5F5F;
                        color: #ffffff;
                        font-weight: bold;
                    }
                </style>
  </head>
  <body>
<!-- Inicio -->
<div id="VentanaTransparente" style="display:none;">
  <div class="overlay_absolute"></div>
  <div id="cargador" style="z-index:2000">
    <table width="100%" height="100%" border="0" class="fuente8">
    <tr valign="middle">
      <td> Por Favor Espere    </td>
      <td><img src="<?=$base_url;?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>  </td>
    </tr>
    </table>
  </div>
</div>
<!-- Fin -->  
    <div id="pagina">

      <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"></div>
        <div id="frmBusqueda">
        <form id="frmEmpresa" name="frmEmpresa" method="post" action="">
          <div id="container" class="container">
            
          </div>                                  
<div id="datosCuentas" >
<!--<link href="<?=base_url()?>public/css/jquery.paginate.css?=<?=CSS;?>" rel="stylesheet" type="text/css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js?=<?=JS;?>">
</script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js?=<?=JS;?>"></script>
<script type="text/javascript">

</script>-->
  <div id="contenedorCuenta">

<table id="tableData" border="0" class="fuente8" width="98%" cellspacing="0" cellpadding="6">
<tr>
  <td>Banco (*)</td>
  <td>
  <select id="txtBanco" name="txtBanco" autofocus >
    <option value="S">::SELECCIONE::</option>
  <?php
if(count($listBanco)>0){
  foreach ($listBanco as $key => $value) {
    ?>
    <option value="<?=$value->BANP_Codigo?>"><?=$value->BANC_Nombre?></option>
    <?php
  }
}
  ?>
  </select >
  </td>
  <td>N° Cuenta (*)</td>
  <td><input  maxlength="20" type="text" id="txtCuenta" name="txtCuenta" onkeypress="return soloLetras_andNumero(event)," onkeyup="onkeypress_cuenta()" ></td>
  <td>Titular (*)</td>
  <td><input maxlength="20" type="text" id="txtTitular" name="txtTitular" onkeypress="return soloLetras_andNumero(event)"></td> 
</tr>
<tr>
<td>Oficina (*)</td>
<td><input type="text" name="txtOficina" id="txtOficina" onkeypress="return soloLetras_andNumero(event)"></td>
<td>Sectoriza (*)</td>
<td><input type="text" name="txtSectoriza" id="txtSectoriza" onkeypress="return soloLetras_andNumero(event)"></td>
<td>Interbancaria (*)</td>
<td><input type="text" name="txtInterban" id="txtInterban" onkeypress="return soloLetras_andNumero(event)" value=""></td>
</tr>
<tr>
  <td>Tipo de Cuenta (*)</td>
  <td>
<select name="txtTipoCuenta" id="txtTipoCuenta" >
      <option value="S">::SELECCIONE::</option>
      <option value='1' >Ahorros</option>
      <option value='2' >Corriente</option>
</select>
</td>
  <td>Moneda (*)</td>
  <td>
    <select id="txtMoneda" name="txtMoneda" >
    <option value="S">::SELECCIONE::</option>
    <?php
  if(count($listMoneda)>0){
    foreach ($listMoneda as $key => $value) {
?>
 <option value="<?=$value->MONED_Codigo?>" ><?=$value->MONED_Descripcion?></option>
      
   <?php
  }
}
?>
</select>
</td>
  <td>
  </td>
  <td> 
<input type="hidden" id="txtCodCuenEmpre" name="txtCodCuenEmpre" value="">
<a href="#" id="btncancelarCuentaE" onclick="insertar_cuentaEmpresa()">
  <img src="<?=base_url()?>public/images/icons/botonagregar.jpg?=<?=IMG;?>"></a>
  <a href="#" id="btnCancelarCuentaE">
  <img src="<?=base_url()?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>"></a>
  <br>

<br>
</td> 
</tr><tr>
<td colspan="6">campos obligatorios (*)

</td></tr>
    </table>
  </div>
  <div id="contenidoCuentaTable">

 <table id="tableBancos" class="table fuente8" width="98%" cellspacing="0" 
 cellpadding="6" border="0">
 <thead id="theadBancos">
    <tr align="center" class="cab1" height="10px;" style="background-color:#5F5F5F;color:#ffffff;font-weight: bold;">
        <td>Item</td>
        <td>Banco</td>
        <td>N° Cuenta</td>
        <td>Nombre o Titular de la cuenta</td>
        <td>Moneda</td>
        <td>Tipo de cuanta</td>
        <td colspan="3">Acciones</td>
       </tr> 
 </thead>
      <tbody id="tbodyBancos">
        
     
            <?php
            $kk=1;
          
            if(count($listado_cuentaEmpresa)>0){
              foreach ($listado_cuentaEmpresa as $key => $value) {
                ?>
                  <tr bgcolor="#ffffff">
                    <td align="center"><?=$key+1?></td>
                    <td align="left"><?=$value->BANC_Nombre?></td>
                    <td><?=$value->CUENT_NumeroEmpresa?></td>              
                    <td align="left"><?=$value->CUENT_Titular?></td>
                    <td align="left"><?=$value->MONED_Descripcion?></td>
                    <?php
                    if($value->CUENT_TipoCuenta==1)
                      {?><td>Ahorros</td>
                    <?php
                    }else{
                      ?><td>Corriente</td><?php
                    }
                    ?>
                    
                    <td align="center">
                      <a href="#" onclick="eliminar_cuantaEmpresa(<?=$value->CUENT_Codigo?>);"><img src="<?=$base_url;?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                    </td>
<td align="center">
  <a href="#" id="btnAcualizarE<?=$value->CUENT_Codigo?>" onclick="actualizar_cuentaEmpresa(<?=$value->CUENT_Codigo?>);"><img src="<?=$base_url;?>public/images/icons/modificar.png?=<?=IMG;?>" border="0"></a>
  </td>
<td><a href="#"  onclick="ventanaChekera(<?=$value->CUENT_Codigo?>)"><img src="<?=base_url()?>public/images/icons/observaciones.png?=<?=IMG;?>"></a></td>
                  </tr>           
                <?php
              
              }
            }else{
            ?><tr>
<td align="center" colspan="10">

 <div>NO EXISTEN REGISTROS</div>
</td>
          </tr>
            
            <?php 
            }
                ?>
  </tbody> </table>
   

  </div>
   
        
     </div>

        <div style="margin-top:20px; text-align: center">
      
          <input id="accion" name="accion" value="alta" type="hidden">
         
          <input type="hidden" name="opcion" id="opcion" value="1">
          <input type="text" name="base_url" id="base_url" value="<?=$base_url;?>">
        
          </div>
<div id="popup" style="display: none; ">
    <div class="content-popup">

        <div class="close"><a href="#" id="close"><img src="<?=base_url()?>public/images/icons/icono_desaprobar.png?=<?=IMG;?>"/></a></div>
        <div>
           <h2>Registro de Chekera</h2>
          <table border="0" width="100%">
<tr>
  <td>Cuenta Empresa</td>
  <td>
  <input type="hidden" name="txtCodCuentaEmpre" id="txtCodCuentaEmpre" />
  <input type="text" name="txtnumeroEmpr" id="txtnumeroEmpr" disabled="disabled"/>
</td>
   <td>Moneda</td>
   <td>
    <input name="txtMonedaChekera" id="txtMonedaChekera" disabled="disabled">
   </td>
</tr>
<tr>
<td>Numero</td>
<td>
 <input maxlength="6" name="txtSerieChekera"  id="txtSerieChekera" type="text" size="4"   onkeypress="return soloLetras_andNumero(event)" autofocus/>
 <input maxlength="10" name="txtNumeroChek"   id="txtNumeroChek"   type="text" size="10"   onkeypress="return soloLetras_andNumero(event)">
</td>
<td colspan="2">
  <a href="#" onclick="insertChekera()"><img src="<?=base_url()?>public/images/icons/botonagregar.jpg?=<?=IMG;?>"></a>
  <a href="#" id="LimpiarChikera"><img src="<?=base_url()?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>"></a>
</td>
</tr></table>
<div id="contenedorTableChekera">
<table id="tablechekera" width="100%"
 cellpadding="6" border="0" >
 <thead>
 <tr style="background-color:#5F5F5F;color:#ffffff;font-weight: bold;">
<td>Item</td>
<td>Fecha</td>
<td>Cuenta Empresa</td>
<td>Serie</td>
<td>Numero</td>
<td>Accion</td>
 </tr>
 </thead>
 <tbody id="listarChekera" style="color:black">
<tr>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
 

<style type="text/css">

/*
#contenidoCuentaTable{
  overflow:scroll;
     height:100px; 
}
*/

.paging-nav {
  text-align: right;
  padding-top: 2px;
}

.paging-nav a {
  margin: auto 1px;
  text-decoration: none;
  display: inline-block;
  padding: 1px 7px;
  background: #91b9e6;
  color: white;
  border-radius: 3px;
}

.paging-nav .selected-page {
  background: #187ed5;
  font-weight: bold;
}

#popup {
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1001;

}

.content-popup {
    margin:0px auto;
    margin-top:-5%;
    position:relative;
    padding:10px;
    width:50%;
    min-height:250px;
    border-radius:4px;
    background-color:#f5fffd;
    box-shadow: 0 2px 5px #666666;
}

.content-popup h2 {
    color:#48484B;
    border-bottom: 1px solid #48484B;
    margin-top: 0;
    padding-bottom: 4px;
}

.popup-overlay {
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 999;
    display:none;
    background-color: #777777;
    cursor: pointer;
    opacity: 0.7;
}

.close {
    position: absolute;
    right: 15px;
}
          </style>
        </form>
      </div>
      </div>
    </div>
 
  </body>
</html>