<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>
<html>
	<head>
		<title><?php echo TITULO;?></title>
		<link href="<?php echo URL_CSS;?>estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo URL_CSS;?>theme.css?=<?=CSS;?>" type="text/css">	
		<link rel="stylesheet" href="<?php echo URL_CSS;?>calendario/calendar-blue.css?=<?=CSS;?>" type="text/css">			
		<script type="text/javascript" src="<?php echo URL_JS;?>JSCookMenu.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_JS;?>theme.js?=<?=JS;?>"></script>	
		<script type="text/javascript" src="<?php echo URL_JS;?>calendario/calendar.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_JS;?>calendario/lang/calendar-sp.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_JS;?>calendario/calendar-setup.js?=<?=JS;?>"></script>			
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.js?=<?=JS;?>"></script>	
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/ventas.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/producto.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/funciones.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
        <script type="text/javascript">
             function seleccionar_cliente(codigo,ruc,razon_social){
                  $("#cliente").val(codigo);
                  $("#ruc").val(ruc);
                  $("#nombre_cliente").val(razon_social);
             }
             function escribe_nombre_unidad_medida(){
                  index     = document.getElementById("unidad_medida").selectedIndex;
                  nombre = document.getElementById("unidad_medida").options[index].text;
                  $("#nombre_unidad_medida").val(nombre);
             }
             function seleccionar_producto(producto,cod_interno,nombre_producto,familia,nombre_familia,stock,costo){
                     $("#codproducto").val(cod_interno);
                     $("#producto").val(producto);
                     $("#stock").val(stock);
                     $("#nombre_familia").val(nombre_familia);
                     $("#nombre_producto").val(nombre_producto);
                     listar_unidad_medida_producto(producto);
             }
       </script>
	</head>
	<body <?php echo $onload;?>>	
	<!-- Inicio -->
	<div id="VentanaTransparente" style="display:none;">
	  <div class="overlay_absolute"></div>
	  <div id="cargador" style="z-index:2000">
		<table width="100%" height="100%" border="0" class="fuente8">
			<tr valign="middle">
				<td> Por Favor Espere    </td>
				<td><img src="<?php echo URL_IMAGE;?>cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
			</tr>
		</table>
	  </div>
	</div>
	<!-- Fin -->		
	<div id="contenedor" align="center"><img src="<?php echo URL_IMAGE;?>2.jpg?=<?=IMG;?>" height="81"></div>
	<div id="MenuAplicacion" align="center"></div>
	<?php require_once "menu.php";?>
	<div class="fuente8" align="right" style="width:95%;"><?php echo anchor('mantenimiento/editar_cuenta/'.$usuario,$nombre_persona);?></div>	
    <form id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action;?>">
    <div id="zonaContenido" align="center">
		<?php echo validation_errors("<div class='error'>",'</div>');?>
		<div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
		<table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
		  <tr>
		    <td width="9%">N&uacute;mero</td>
		    <td width="27%" valign="middle">
                 <input name="serie" type="text" class="cajaPequena2" id="serie" size="10" maxlength="10" readonly="readonly" value="001">&nbsp;
                 <input name="numero" type="text" class="cajaPequena2" id="numero" size="10" maxlength="10" readonly="readonly" value="<?php echo $numero;?>">
                 <input name="centro_costo" type="hidden" class="cajaPequena2" id="centro_costo" size="10" maxlength="10" readonly="readonly" value="1">
                 <input name="presupuesto" type="hidden" class="cajaPequena2" id="presupuesto" size="10" maxlength="10" readonly="readonly" value="<?php echo $presupuesto;?>">
	        </td>
            <td width="9%" valign="middle">Presupuesto</td>
            <td width="27%" valign="middle"><select name="presupuesto" id="presupuesto" class="comboPequeno" onchange="obtener_detalle_presupuesto();" onfocus="<?php echo $focus;?>"><?php echo $cboPresupuesto;?></select></td>
            <td width="9%" valign="middle">Fecha</td>
            <td width="25%" valign="middle"><input NAME="fecha" type="text" class="cajaPequena" id="fecha" value="<?php echo $hoy;?>" size="10" maxlength="10" readonly="readonly">
				<a href="#" style="display:none;"><img height="16" border="0" width="16" id="Image1" name="Image1" src="<?php echo URL_IMAGE;?>calendario.png?=<?=IMG;?>"></a>
				<script type="text/javascript">
				Calendar.setup(
					{
						inputField : "fecha",ifFormat : "%d/%m/%Y",button : "Image1"
					}
				);
				</script>
            </td>
		  </tr>
		  <tr>
			<td width="9%">Cliente </td>
			<td valign="middle">
                 <input type="hidden" name="cliente" id="cliente" size="5" class="cajaPequena2" value="<?php echo $cliente?>">
                 <input type="text" name="ruc" class="cajaPequena2" id="ruc" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc;?>" onkeypress="return numbersonly(this,event,'.');">
                 &nbsp;<input type="text" name="nombre_cliente" class="cajaMedia" id="nombre_cliente" size="15" maxlength="15" readonly="readonly" value="<?php echo $nombre_cliente;?>">
                 <?php echo $vercliente;?>
            </td>
		    <td valign="middle">Forma de Pago</td>
		    <td valign="middle">
                 <select name="forma_pago" id="forma_pago" class="comboMedio"><?php echo $cboFormaPago;?></select>
            </td>
		    <td valign="middle">Descuento</td>
          <td><input NAME="descuento" type="text" class="cajaPequena2" id="descuento"  maxlength="10" value="<?php echo $descuento;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_descuento_total();">%  </td>
		  </tr>
		  <tr>
            <td>Vendedor</td>
		    <td><input NAME="nombre_usuario" type="text" class="cajaGrande" id="nombre_usuario" size="30" maxlength="30" readonly value="<?php echo $nombre_persona;?>"></td>
		    <td>Moneda</td>
		    <td>
                 <select name="moneda" id="moneda" class="comboMedio"><?php echo $cboMoneda;?></select>
            </td>
		    <td>I.G.V.</td>
		    <td>
                 <input NAME="igv" type="text" class="cajaPequena2" id="igv" maxlength="10" value="<?php echo $igv;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();">
                 <label> % </label>
            </td>
	      </tr>
		  <tr>
		    <td>Concepto</td>
		    <td colspan="3"><input NAME="concepto" type="text" class="cajaSuperGrande" id="concepto"  maxlength="100" value="<?php echo $concepto;?>"></td>
            <td>&nbsp;</td>
            <td width="49%" valign="middle">&nbsp;</td>
	      </tr>
		</table>
		</div>	
		<div id="frmBusqueda"  <?php echo $hidden;?>>
		<table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
		  <tr <?php echo $hidden;?>>
			<td width="9%">Art&iacute;culo</td>
			<td width="49%">
                    <input name="producto" type="hidden" class="cajaPequena2" id="producto" size="10" maxlength="11">
                    <input name="codproducto" type="text" class="cajaPequena2" id="codproducto" size="10" maxlength="50" onblur="obtener_producto();">&nbsp;
                    <input NAME="nombre_producto" type="text" class="cajaGrande" id="nombre_producto" size="15" maxlength="15" readonly="readonly">
                    &nbsp;<?php echo $verproducto;?>
            </td>
			<td width="6%">Cantidad</td>
			<td width="25%">
                 <input NAME="cantidad" type="text" class="cajaPequena2" id="cantidad" value="0" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');">
			    <select name="unidad_medida" id="unidad_medida" class="comboMedio" onchange="escribe_nombre_unidad_medida();"><option value="0">::Seleccione::</option></select>
        	</td>
			<td width="2%"><input type="hidden" name="nombre_unidad_medida" id="nombre_unidad_medida" class="cajaMedia"></td>
			<td width="15%">
                 <div align="right"><a href="#" onClick="agregar_producto_boleta();"><img src="<?php echo URL_IMAGE;?>botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom"></a></div>
            </td>
		  </tr>
		</table>
		</div>
		<br>
		<div id="frmBusqueda">
			<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
				<tr class="cabeceraTabla">
                     <td width="3%"><div align="center">&nbsp;</div></td>
					<td width="5%"><div align="center">ITEM</div></td>
					<td width="10%"><div align="center">C&Oacute;DIGO</div></td>
					<td width="32%"><div align="center">DESCRIPCI&Oacute;N</div></td>
					<td width="8%"><div align="center">	P.U.</div></td>
					<td width="8%"><div align="center">CANTIDAD</div></td>
					<td width="8%"><div align="center">PRECIO</div></td>
					<td width="8%"><div align="center">DSCTO</div></td>
					<td width="8%"><div align="center">I.G.V.</div></td>
					<td width="10%"><div align="center">IMPORTE</div></td>
				</tr>
			</table>
			<div id="lineaResultado">
				<table width="100%" height="250px;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">
							<table id="tblDetalleBoleta" class="fuente8" width="100%" border="0">
                             <?php
                                  if(count($detalle_boleta)>0){
                                       foreach($detalle_boleta as $indice=>$valor){
                                            $detbol                         = $valor->BOLEDEP_Codigo;
                                            $prodproducto           = $valor->PROD_Codigo;
                                            $unidad_medida      = $valor->UNDMED_Codigo;
                                            $codigo_interno       = $valor->PROD_CodigoInterno;
                                            $prodcantidad           = $valor->BOLEDEC_Cantidad;
                                            $nombre_producto = $valor->PROD_Nombre;
                                             $nombre_unidad    =  $valor->UNDMED_Simbolo;
                                             $prodpu                       = $valor->BOLEDEC_Pu;
                                             $prodsubtotal            =  $valor->BOLEDEC_Subtotal;
                                             $proddescuento        =  $valor->BOLEDEC_Descuento;
                                             $prodigv                        =  $valor->BOLEDEC_Igv;
                                             $prodtotal                    =  $valor->BOLEDEC_Total;
                                             $proddescuento100  =  $valor->BOLEDEC_Descuento100;
                                             $prodigv100                 =  $valor->BOLEDEC_Igv100;
                                             if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                                            ?>
                                              <tr class="<?php echo $clase;?>">
                                                   <td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_ocompra(<?php echo $indice;?>);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                                                   <td width="5%"><div align="center"><?php echo $indice+1;?></div></td>
                                                  <td width="10%"><div align="center"><?php echo $codigo_interno;?></div></td>
                                                  <td width="32%"><div align="left"><?php echo $nombre_producto;?></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodpu[<?php echo $indice;?>]" id="prodpu[<?php echo $indice;?>]" value="<?php echo $prodpu;?>" onblur="calcula_importe(<?php echo $indice;?>);calcula_totales_boleta();" onkeypress="return numbersonly(this,event,'.');"></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onblur="calcula_importe(<?php echo $indice;?>);calcula_totales_boleta();" onkeypress="return numbersonly(this,event,'.');" <?php echo $readonly;?>><?php echo $nombre_unidad;?></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodprecio[<?php echo $indice;?>]" id="prodprecio[<?php echo $indice;?>]" value="<?php echo $prodsubtotal;?>" readonly="readonly"></div></td>
                                                  <td width="8%"><div align="center">
                                                       <input type="hidden" class="cajaPequena2" name="proddescuento100[<?php echo $indice;?>]" id="proddescuento100[<?php echo $indice;?>]" value="<?php echo $proddescuento100;?>">
                                                       <input type="text" class="cajaPequena2" name="proddescuento[<?php echo $indice;?>]" id="proddescuento[<?php echo $indice;?>]" readonly value="<?php echo $proddescuento;?>">
                                                  </div></td>
                                                  <td width="8%"><div align="center">
                                                       <input type="hidden" class="cajaPequena2" name="prodigv100[<?php echo $indice;?>]" id="prodigv100[<?php echo $indice;?>]" value="<?php echo $proddescuento100;?>">
                                                       <input type="text" class="cajaPequena2" name="prodigv[<?php echo $indice;?>]" id="prodigv[<?php echo $indice;?>]" readonly value="<?php echo $prodigv;?>">
                                                  </div></td>
                                                  <td width="10%"><div align="center">
                                                            <input type="text" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m">
                                                            <input type="hidden" class="cajaMinima" name="detbol[<?php echo $indice;?>]" id="detbol[<?php echo $indice;?>]" value="<?php echo $detbol;?>">
                                                            <input type="hidden" class="cajaMinima" name="prodcodigo[<?php echo $indice;?>]" id="prodcodigo[<?php echo $indice;?>]" value="<?php echo $prodproducto;?>">
                                                            <input type="hidden" class="cajaMinima" name="produnidad[<?php echo $indice;?>]" id="produnidad[<?php echo $indice;?>]" value="<?php echo $unidad_medida;?>">
                                                            <input type="text" class="cajaPequena2" name="prodimporte[<?php echo $indice;?>]" id="prodimporte[<?php echo $indice;?>]" readonly="readonly" value="<?php echo $prodtotal;?>">
                                                  </div></td>
                                              </tr>
                                            <?php
                                       }
                                  }
                                  ?>
                            </table>
						</td>
					</tr>
				</table>
			</div>					
		</div>	
		<div id="frmBusqueda">
			<table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
				<tr>
                     <td width="80%" rowspan="4" align="left">
                               <div style="float: left;padding-left:6px;padding-top: 0px;height:30px;width: 100px;border: 0px solid #000000;">OBSERVACION</div>
                               <div style="float:left;margin-right: 10px;"><textarea id="observacion" name="observacion" class="fuente8" cols="130" rows="3"><?php echo $observacion;?></textarea></div>
                     </td>
					<td width="10%" class="busqueda">Sub-total</td>
					<td width="10%" align="right">
						<div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly value="<?php echo $preciototal;?>">
						</div>
				  </td>
				</tr>
				<tr>
					<td class="busqueda">Descuento</td>
					<td align="right">
						<div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly value="<?php echo $descuentotal;?>"></div>
				  </td>
				</tr>
				<tr>
					<td class="busqueda">IGV</td>
					<td align="right">
						<div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly value="<?php echo $igvtotal;?>">
						</div>
					</td>
				</tr>
				<tr>
					<td class="busqueda">Precio Total</td>
					<td align="right">
						<div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly value="<?php echo $importetotal;?>">
						</div>
					</td>
				</tr>
			</table>
		</div>	
		<br>			  
		<div id="botonBusqueda2">
			<a href="#" id="grabarBoleta"><img src="<?php echo URL_IMAGE;?>botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1" ></a>
			<a href="#" id="limpiarBoleta"><img src="<?php echo URL_IMAGE;?>botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" border="1" ></a>
			<a href="#" id="cancelarBoleta"><img src="<?php echo URL_IMAGE;?>botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" border="1" ></a>
			<?php echo $oculto?>
		</div>		
	</div>
    </form>
	</body>
</html>