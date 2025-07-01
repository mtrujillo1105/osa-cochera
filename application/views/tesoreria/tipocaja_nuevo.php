<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
	header("location:$url");
	?>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/tipocaja.js?=<?=JS;?>"></script>
   
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>",'</div>');?>
                <?php echo $form_open;?>
                    <div id="datosGenerales">
 <table class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="0">
              <tr>
    <td width="10%">Descripción(*)</td>
    <td width="10%">
    <input maxlength="50"  type="text" name="txtDescrip" id="txtDescrip" value="<?=$tipo_descripcion?>" autofocus  onkeypress="return soloLetras_andNumero(event)">
    <input size="4" type="hidden" name="txtcodigo" id="txtcodigo" value="<?=$codigocaja?>" ></td>
    <td>
     Tipo Caja(*)
    <input  type="hidden" name="txtCompania" id="txtCompania" value="<?=$compania?>">
    <input type="hidden" name="txtEstado" id="txtEstado" value="<?=$estado?>">
        
      </td>
      <td>
      <select name="txtTipocaja" id="txtTipocaja" class="comboGrande" style="font-size: 15px">
      <?php
if ($codigocaja=="") {
?>
<option >::Seleccione::</option>
<option value="1">Tipo Caja 1</option>
<option value="2">Tipo Caja 2</option>
<?php
}else{
  if($tip_Caja=="1"){
?>
<option value="1">Tipo Caja 1</option>
<option value="2">Tipo Caja 2</option>
<?php
  }else{
    ?>
<option value="2">Tipo Caja 2</option>
<option value="1">Tipo Caja 1</option>
    <?php
  }
  ?>
          
  <?php
}
?>
          
      </select><br>
      <input type="hidden" name="txtTipoCodigo" id="txtTipoCodigo" value="<?=$tip_Caja?>">
          
    <input type="hidden" name="txtusuarioR" id="txtusuarioR" value="<?=$usu_registro?>">
     <input NAME="fecha" type="hidden" class="cajaGeneral" id="fecha" value="<?=$fechaReg ?>"    > 
      </td>
<!--
     <td width="5%"> Fecha</td>
     <td width="15%">
        <input NAME="fecha" type="hidden" class="cajaGeneral" id="fecha" value="<?=$fechaReg ?>"
                           size="10" maxlength="10" readonly="readonly"/>
       <img height="16" border="0" width="16" id="Calendario1" name="Calendario1"
                         src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
        <script type="text/javascript">
                        Calendar.setup({
                            inputField: "fecha",      // id del campo de texto
                            ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                            button: "Calendario1"   // el id del botón que lanzará el calendario
                        });
        </script>-->
        </td>
    </tr>
  <tr>
  <td>Abreviatura(*)</td>
      <td>
      <input maxlength="10" type="text" name="txtAbreviatura" id="txtAbreviatura" value="<?=$abreviatura?>" onkeypress="return soloLetras_andNumero(event)">
    
        </td>
      
  </tr>
</table>
                    </div>
 <div style="margin-top:20px; text-align: center">
                        <a href="#" id="grabartipoCaja"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <a href="#" id="limpiartipoCaja"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                        <a href="#" id="cancelartipoCaja"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <?php echo $oculto?>
                    </div>
                <?php echo $form_close;?>
</div>
        </div>
    </div>
</div>