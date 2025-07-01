<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>
<html>
    <head>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/ventas/factura.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>
    <script type="text/javascript">
     function seleccionar_cliente(codigo,ruc,razon_social){
          $("#cliente").val(codigo);
          $("#ruc").val(ruc);
          $("#nombre_cliente").val(razon_social);
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
				<td><img src="<?=$base_url;?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
			</tr>
		</table>
	  </div>
	</div>
	<!-- Fin -->		
    <form id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action;?>">
    <div id="zonaContenido" align="center">
		<?php echo validation_errors("<div class='error'>",'</div>');?>
		<div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
		<table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
		  <tr>
		    <td width="9%">N&uacute;mero</td>
		    <td width="27%" valign="middle">
                 <input name="serie" type="text" class="cajaPequena2" id="serie" size="10" maxlength="10" readonly="readonly" value="<?php echo $serie;?>">&nbsp;
                 <input name="numero" type="text" class="cajaPequena2" id="numero" size="10" maxlength="10" readonly="readonly" value="<?php echo $numero;?>">
	        </td>
            <td width="9%" valign="middle">Presupuesto</td>
            <td width="27%" valign="middle"><select name="presupuesto" id="presupuesto" class="comboMedio" onchange="obtener_detalle_presupuesto();" onfocus="<?php echo $focus;?>"><?php echo $cboPresupuesto;?></select></td>
            <td width="9%" valign="middle">Fecha</td>
            <td width="25%" valign="middle"><input NAME="fecha" type="text" class="cajaPequena" id="fecha" value="<?php echo $hoy;?>" size="10" maxlength="10" readonly="readonly">
        	<img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                <script type="text/javascript">
                    Calendar.setup({
                        inputField     :    "fecha",      // id del campo de texto
                        ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                        button         :    "Calendario1"   // el id del botón que lanzará el calendario
                    });
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
		</table>
		</div>	
		<div id="frmBusqueda"  <?php echo $hidden;?>>
		<table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
		  <tr>
			<td width="5%">Art&iacute;culo</td>
			<td width="40%">
                                <input name="producto" type="hidden" class="cajaPequena2" id="producto" size="10" maxlength="11">
                                <input name="codproducto" type="text" class="cajaPequena2" id="codproducto" size="10" maxlength="50" onblur="obtener_producto();">&nbsp;
                                <input NAME="nombre_producto" type="text" class="cajaGrande" id="nombre_producto" size="15" maxlength="15" readonly="readonly">
                                &nbsp;<?php echo $verproducto;?>
                        </td>
                        <td width="6%">Cantidad</td>
			<td width="25%">
                            <input NAME="cantidad" type="text" class="cajaPequena2" id="cantidad" value="" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');">
			    <select name="unidad_medida" id="unidad_medida" class="comboMedio" onchange="obtener_precio_producto();"><option value="0">::Seleccione::</option></select>
                	</td>
                        <td width="5%">PU</td>
			<td width="10%">
                            <input NAME="precio" type="text" class="cajaPequena2" id="precio" value="" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');">
                	</td>
			<td width="2%"></td>
			<td width="15%">
                       <div align="right"><a href="javascript:;" onClick="agregar_producto_factura();"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom"></a></div>
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
                            <table id="tblDetalleFactura" class="fuente8" width="100%" border="0">
                             <?php
                                  if(count($detalle_factura)>0){
                                       foreach($detalle_factura as $indice=>$valor){
                                            $detfac                     = $valor->FACTDEP_Codigo;
                                            $prodproducto           = $valor->PROD_Codigo;
                                            $unidad_medida      = $valor->UNDMED_Codigo;
                                            $codigo_interno       = $valor->PROD_CodigoInterno;
                                            $prodcantidad           = $valor->FACTDEC_Cantidad;
                                            $nombre_producto = $valor->PROD_Nombre;
                                             $nombre_unidad    =  $valor->UNDMED_Simbolo;
                                             $prodpu                       = number_format($valor->FACTDEC_Pu,2);
                                             $prodsubtotal            = number_format($valor->FACTDEC_Subtotal,2);
                                             $proddescuento        = number_format($valor->FACTDEC_Descuento,2);
                                             $prodigv                        = number_format($valor->FACTDEC_Igv,2);
                                             $prodtotal                    = number_format($valor->FACTDEC_Total,2);
                                             if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                                            ?>
                                              <tr class="<?php echo $clase;?>">
                                                   <td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(<?php echo $indice;?>);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                                                   <td width="5%"><div align="center"><?php echo $indice+1;?></div></td>
                                                  <td width="10%"><div align="center"><?php echo $codigo_interno;?></div></td>
                                                  <td width="32%"><div align="left"><?php echo $nombre_producto;?></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodpu[<?php echo $indice;?>]" id="prodpu[<?php echo $indice;?>]" value="<?php echo $prodpu;?>" onblur="calcula_importe(<?php echo $indice;?>);calcula_totales();" onkeypress="return numbersonly(this,event,'.');"></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onblur="calcula_importe(<?php echo $indice;?>);calcula_totales();" onkeypress="return numbersonly(this,event,'.');"><?php echo $nombre_unidad;?></div></td>
                                                  <td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodprecio[<?php echo $indice;?>]" id="prodprecio[<?php echo $indice;?>]" value="<?php echo $prodsubtotal;?>" readonly="readonly"></div></td>
                                                  <td width="8%"><div align="center">
                                                       <input type="hidden" class="cajaPequena2" name="proddescuento100[<?php echo $indice;?>]" id="proddescuento100[<?php echo $indice;?>]" value="<?php echo $descuento;?>">
                                                       <input type="text" class="cajaPequena2" name="proddescuento[<?php echo $indice;?>]" id="proddescuento[<?php echo $indice;?>]" readonly value="<?php echo $proddescuento;?>">
                                                  </div></td>
                                                  <td width="8%"><div align="center">
                                                       <input type="hidden" class="cajaPequena2" name="prodigv100[<?php echo $indice;?>]" id="prodigv100[<?php echo $indice;?>]" value="<?php echo $igv;?>">
                                                       <input type="text" class="cajaPequena2" name="prodigv[<?php echo $indice;?>]" id="prodigv[<?php echo $indice;?>]" readonly value="<?php echo $prodigv;?>">
                                                  </div></td>
                                                  <td width="10%"><div align="center">
                                                            <input type="hidden" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m">
                                                            <input type="hidden" class="cajaMinima" name="detfact[<?php echo $indice;?>]" id="detfact[<?php echo $indice;?>]" value="<?php echo $detfac;?>">
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
                    <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
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
		<br />
		<div id="botonBusqueda2" style="padding-top:20px;">
			<a href="javascript:;" id="grabarFactura"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="limpiarFactura"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="cancelarFactura"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<?php echo $oculto?>
		</div>
                
	</div>
    </form>
	</body>
</html>