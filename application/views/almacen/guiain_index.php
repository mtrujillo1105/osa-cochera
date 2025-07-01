<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/guiain.js?=<?=JS;?>"></script>
<!--<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>-->
<br>


<script>
$(document).ready(function(){
	$("a#linkVerCliente, a#linkVerProducto").fancybox({
			'width'          : 700,
			'height'         : 450,
			'autoScale'	 : false,
			'transitionIn'   : 'none',
			'transitionOut'  : 'none',
			'showCloseButton': false,
			'modal'          : true,
			'type'	     : 'iframe'
	});  
}); 

function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
	$("#cliente").val(codigo);
	$("#ruc_cliente").val(ruc);
	$("#nombre_cliente").val(razon_social);
}
</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>

<form id="form_busquedaGuiain" name="form_busquedaGuiain" method="post" action="<?=$base_url;?>index.php/almacen/guiain/listar">
<div id="frmBusqueda" >

<table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
	<tr>
		<td align='left' width="10%">Fecha inicial</td>
		<td align='left' width="90%">
										<input name="fechai" id="fechai" value="<?php echo $fechai; ?>" type="text" class="cajaGeneral" size="10" maxlength="10"/>
										<img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
										<script type="text/javascript">
											Calendar.setup({
												inputField     :    "fechai",      // id del campo de texto
												ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
												button         :    "Calendario1"   // el id del botón que lanzará el calendario
											});
										</script>
										<label style="margin-left: 90px;">Fecha final</label>
										<input name="fechaf" id="fechaf" value="<?php echo $fechaf; ?>" type="text" class="cajaGeneral" size="10" maxlength="10" />
										<img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
										<script type="text/javascript">
											Calendar.setup({
												inputField     :    "fechaf",      // id del campo de texto
												ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
												button         :    "Calendario2"   // el id del botón que lanzará el calendario
											});
										</script>
									</td>
	</tr>
	<tr>
								<td align='left' colspan="2">
								<div class="" style="width:180px;display:inline;">
								Número
									<div style="margin-left:52px;display:inline;">
								<?php 
									switch($tipo_codificacion){
										case '1': echo '<input type="text" name="numero" id="numero" value="'.$numero.'" class="cajaGeneral"size="10" maxlength="10"  />'; break;
										case '2': echo '<input type="text" name="serie" id="serie" value="'.$serie.'" class="cajaGeneral" size="3" maxlength="10"  /> ';
												  echo '<input type="text" name="numero" id="numero" value="'.$numero.'" class="cajaGeneral" size="10" maxlength="10"  /> '; break;
										case '3': echo '<input type="text" name="codigo_usuario" id="codigo_usuario" value="'.$codigo_usuario.'" class="cajaGeneral" size="20" maxlength="50"  />'; break;
									}
								?>
									</div>
								</div>
								
								
								<div class="" style="display:inline;margin-left:112px;">
								Cotización
								
									<input type="text" id="cotizacion" name="cotizacion" class="cajaGeneral" value="<?php echo $cotizacion; ?>" />
								</div> 
								</td>
	</tr>
	
	
	
		<tr>
								<td align='left' colspan="2">
								<div class="" style="width:180px;display:inline;">
								Pedido
									<div style="margin-left:56px;display:inline;">
										<input type="text" id="pedido" name="pedido" class="cajaGeneral" value="<?php echo $pedido; ?>" />
									</div>
								</div>
								
								
								<div class="" style="display:inline;margin-left:68px;">
								Situación
									<select name="situacion" id="situacion" class="comboPequeno">
										<option value="" <?php if($situacion == ''): ?> selected<?php endif;?>>:: :: :: :: ::</option>
										<option value="0"<?php if($situacion == 0 && $situacion != ''): ?> selected<?php endif;?>>Pend.</option>
										<option value="1"<?php if($situacion == 1): ?> selected<?php endif;?>>Atend.</option>
									</select>
								</div> 
								</td>
	</tr>
	<tr>
								<td align='left'>Cliente</td>
								<td align='left'>
									<input type="hidden" name="cliente" value="<?php echo $cliente; ?>" id="cliente" size="5" />
									<input type="text" name="ruc_cliente" value="<?php echo $ruc_cliente; ?>" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" onkeypress="return numbersonly(this,event,'.');" />
									<input type="text" name="nombre_cliente" value="<?php echo $nombre_cliente; ?>"  class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" readonly="readonly" />
									<a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
								</td>
	</tr>
</table>

  </div>
  
  </form>
  <div class="acciones">	  
    <div id="botonBusqueda">
            <ul id="imprimirGuiain" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
            <ul id="nuevaGuiain" class="lista_botones"><li id="nuevo">Nuevo C. de Ingreso</li></ul>
            <ul id="limpiarGuiain" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
            <ul id="buscarGuiain" class="lista_botones"><li id="buscar">Buscar</li></ul> 
    </div>
    <div id="lineaResultado">
        <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
        <tr>
            <td width="50%" align="left">N de ordenes de compra encontrados:&nbsp;<?php echo $registros;?> </td>
            
        </tr>
        </table>
    </div>
    </div>
    
    <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
    <div id="frmResultado">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
            <tr class="cabeceraTabla">
                    <td width="5%">ITEM</td>
                    <td width="9%">FECHA</td>
                    <td width="5%">NUMERO</td>
                    <td width="5%">O.COMPRA</td>
                    <td width="15%">ALMACEN</td>
                    <td width="46%">RAZON SOCIAL</td>
                    <td width="5%">&nbsp;</td>
                    <td width="5%">&nbsp;</td>
                    <td width="5%">&nbsp;</td>
            </tr>
            <?php
            if(count($lista)>0){
            foreach($lista as $indice=>$valor){
                    $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class;?>">
                            <td><div align="center"><?php echo $valor[0];?></div></td>
                            <td><div align="center"><?php echo $valor[1];?></div></td>
                            <td><div align="center"><?php echo $valor[2];?></div></td>
                            <td><div align="center"><?php echo $valor[3];?></div></td>
                            <td><div align="center"><?php echo $valor[4];?></div></td>
                            <td><div align="left"><?php echo $valor[5];?></div></td>
                            <td><div align="center"><?php echo $valor[6];?></div></td>
                            <td><div align="center"><?php echo $valor[7];?></div></td>
                            <td><div align="center"><?php echo $valor[8];?></div></td>
                    </tr>
                    <?php
                    }
            }
            else{
            ?>
            <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                    <tbody>
                            <tr>
                                    <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                            </tr>
                    </tbody>
            </table>
            <?php
            }
            ?>
        </table>
        </div>
         <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
        <input type="hidden" id="iniciopagina" name="iniciopagina">
        <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
        <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>
  </div>
</div>