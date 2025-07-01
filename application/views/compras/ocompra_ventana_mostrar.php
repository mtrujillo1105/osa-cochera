<link rel="stylesheet" href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css">
<link rel="stylesheet" href="<?=$base_url;?>public/css/theme.css?=<?=CSS;?>" type="text/css">
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/ocompra_popup.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>	

<div id="zonaContenido" align="center">
<form id="frmOventa" id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action;?>" onsubmit="return valida_oventa();">
<div id="tituloForm" class="header" style="width: 670px;"><?php echo $titulo;?></div>
<div id="frmBusqueda" style="width: 650px;">

    <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0" >
      <tr>
        <td width="8%" >N&uacute;mero : </td>
        <td width="38%"><?php echo $numero;?>
		
         <label style="padding-left:52px;">Código : &nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $codigo_usuario;?>
		 <input type="hidden" name="codigo_usuario" id="codigo_usuario" value="<?php echo $codigo_usuario; ?>" />
         <input name="pedido" type="hidden" class="cajaPequena2" id="pedido" size="10" maxlength="10" readonly="readonly" value="<?php echo $pedido;?>" /></td>
		 <input type="hidden" name="numero" id="numero" value="<?php echo $numero; ?>" />
		 <input type="hidden" name="tipo_oper" id="tipo_oper" value="<?php echo $tipo_oper; ?>" />
        <td width="8%">Almacen</td>
        <td width="20%"><?php echo $cboAlmacen[0]->ALMAC_Descripcion;?></td>
		<input type="hidden" name="Almacen" id="Almacen" value="<?php echo $cboAlmacen[0]->ALMAC_Descripcion; ?>" />
        <td width="8%">Fecha</td>
        <td width="18%"><?php echo $hoy;?></td>
      </tr>
      <tr>
        <?php if($tipo_oper=='V'){ ?>
            <td>Cliente *</td>
            <td valign="middle">
                 <?php echo $ruc_cliente;?>
				 <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente; ?>" />
				 <input type="hidden" name="ruc_cliente" id="ruc_cliente" value="<?php echo $ruc_cliente; ?>" />
                 <?php echo $nombre_cliente;?>
				 <input type="hidden" name="nombre_cliente" id="nombre_cliente" value="<?php echo $nombre_cliente; ?>" />
            </td>
        <?php }else{ ?> 
        <td>Proveedor </td>
        <td>
             <?php echo $ruc_proveedor;?>
			 <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor; ?>" />
			 <input type="hidden" name="ruc_proveedor" id="ruc_proveedor" value="<?php echo $ruc_proveedor; ?>" />
             <?php echo $nombre_proveedor;?>
			 <input type="hidden" name="nombre_proveedor" id="nombre_proveedor" value="<?php echo $nombre_proveedor; ?>" />
        </td>
        <?php } ?>
        <td>Moneda </td>
        <td> <?php echo $cboMoneda[0]->MONED_Descripcion;?></td>
		 <input type="hidden" name="moneda" id="moneda" value="<?php echo $cboMoneda[0]->MONED_Descripcion; ?>" />
                                                   
      </tr>
      <tr>
         <td valign="middle"><?php if($tipo_oper=='V') echo 'Comprador'; else echo 'Vendedor'; ?></td>
         <td><?php echo $contacto;?>
         </td>    
         <td valign="middle">Forma Pago</td>
         <td><?php if(count($cboFormapago)>0){ echo $cboFormapago[0]->FORPAC_Descripcion;}?></td>
         <td valign="middle"><?php if($tipo_oper=='V') echo 'Vendedor'; else echo 'Comprador'; ?></td>
         <td><?php echo $mi_contacto;?></td>    
      </tr>
      <tr>
        <td>I.G.V.</td>
        <td><?php echo $igv;?>%
        </td>
        <td>Dscto</td>
        <td>
           <?php echo $descuento;?>
        </td>
        <td>Percepci&oacute;n</td>
        <td>
            <?php echo $percepcion;?>
             <label> % </label>
        </td>
      </tr>
    </table>
    </div>
    <br>
	<form name="frmSeguimiento" id="frmSeguimiento">
    <div id="frmBusqueda" style="height:300px; overflow: auto; width:650px">
	
           <div class="fuente8" align="left" style="color:white;font-weight:bold;">
			<span style="border:1px solid green;background-color:green;">&nbsp;ENTREGA FINALIZADA&nbsp;</span>
			<span style="border:1px solid orange;background-color:orange;">&nbsp;ENTREGA EN PROCESO&nbsp;</span>
			<span style="border:1px solid red;background-color:red;">&nbsp;ENTREGA SIN MOVIMIENTO&nbsp;</span>
		</div>
		   <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                    <tr class="cabeceraTabla">
                            <td width="3%"><div align="center">&nbsp;</div></td>
                            <td width="4%"><div align="center">ITEM</div></td>
                            <td width="10%"><div align="center">C&Oacute;DIGO</div></td>
                            <td  width="20%"><div align="center">DESCRIPCI&Oacute;N</div></td>
                            <td width="6%"><div align="center">CANTIDAD</div></td>
                            <td width="8%"><div align="center"># A DESPACHAR</div></td>
                            <td width="10%"><div align="center"># DESPACHADOS</div></td>
                            <td width="6%"><div align="center"># RESTANTE</div></td>
                            <td width="6%"><div align="center"># TOTAL</div></td>
                    </tr>
            </table>        
            <div>
			
                <table id="tblDetalleOcompra" class="fuente8" width="650" cellspacing="0" cellpadding="3"  border="0" >
                 <?php
                      if(count($detalle_ocompra)>0){
                           foreach($detalle_ocompra as $indice=>$valor){
                                $detocom           = $valor->OCOMDEP_Codigo;
                                $codproducto       = $valor->PROD_Codigo;
                                $unidad_medida     = $valor->UNDMED_Codigo;
                                $codigo_interno    = $valor->PROD_CodigoInterno;
                                $prodcantidad      = $valor->COTDEC_Cantidad;
                                $nombre_producto   = $valor->PROD_Nombre;
                                $prodpu           	= $valor->OCOMDEC_Pu;
                                $prodsubtotal     =  $valor->OCOMDEC_Subtotal;
                                $proddescuento    =  $valor->OCOMDEC_Descuento;
                                $proddescuento2   =  $valor->OCOMDEC_Descuento2;
                                $prodigv          =  $valor->OCOMDEC_Igv;
                                $prodtotal        =  $valor->OCOMDEC_Total;
                                $cantidad_pendiente= $valor->cantidad_pendiente;
                                $cantidad_entregada= $valor->cantidad_entregada;
                                $nombre_unidad	   = $valor->nombre_unidad;
                                $flagGenInd	   = $valor->flagGenInd;
                                $codigo		   = $valor->codigo;
                                $igv_total	   = $valor->igv_total;
                                $calculo_aumento   = (100 + $igv_total) / 100;
                                $precio_conigv 	= $valor->OCOMDEC_Pu_ConIgv;
								
								 $color_f = "";
                                if($cantidad_entregada == 0){
                                        $color_f = "red";
                                }
                                if($cantidad_entregada > 0){
                                        $color_f = "orange";
                                }
                                if($cantidad_entregada == $prodcantidad){
                                        $color_f = "green";
                                }
							
				
                                if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                                ?>
                                  <tr class="<?php echo $clase;?>">
                                    <td width="3%"><div align="center">
                                    <?php
                                    if($prodcantidad != $cantidad_entregada){
                                            ?>
                                            <input type="checkbox" name="chk_producto[<?php echo $indice;?>]" id="chk_producto[<?php echo $indice;?>]" value="<?php echo $indice; ?>" class="seleccion_producto"/>
                                            <!-- <input type="checkbox" name="chk_producto[]" id="chk_producto[]" class="seleccion_producto"/> -->
                                            <?php
                                    }
                                    ?>
                                    </div></td>
                                    <td width="4%"><div align="center"><?php echo $indice+1;?></div></td>
                                    <td width="10%">
                                            <div align="center">
                                                    <?php echo $codigo_interno; ?>
                                                    <input type="hidden" name="comprobado[<?php echo $indice;?>]" id="comprobado[<?php echo $indice;?>]" value="" />
                                                    <input type="hidden" name="codigo_orden[<?php echo $indice;?>]" id="codigo_orden[<?php echo $indice;?>]" value="<?php echo $codigo; ?>" />
                                                    <input type="hidden" name="producto[<?php echo $indice;?>]" id="producto[<?php echo $indice;?>]" value="<?php echo $codigo_interno; ?>" />
                                                    <input type="hidden" name="codproducto[<?php echo $indice;?>]" id="codproducto[<?php echo $indice;?>]" value="<?php echo $codproducto; ?>" />
                                                    <input type="hidden" name="precio_conigv[<?php echo $indice;?>]" id="precio_conigv[<?php echo $indice;?>]" value="<?php echo $precio_conigv; ?>" />
                                                    <input type="hidden" name="igv[<?php echo $indice;?>]" id="igv[<?php echo $indice;?>]" value="<?php echo $igv_total; ?>" />
                                                    <input type="hidden" name="unidad_medida[<?php echo $indice;?>]" id="unidad_medida[<?php echo $indice;?>]" value="<?php echo $unidad_medida; ?>" />
                                                    <input type="hidden" name="nombre_unidad[<?php echo $indice;?>]" id="nombre_unidad[<?php echo $indice;?>]" value="<?php echo $nombre_unidad; ?>" />
                                                    <input type="hidden" name="flagGenInd[<?php echo $indice;?>]" id="flagGenInd[<?php echo $indice;?>]" value="<?php echo $flagGenInd; ?>" />
                                            </div>
                                    </td>
                                      <td  width="20%" ><div align="left"><input type="hidden" class="cajaGeneral cajaSoloLectura" style="width:330px;" maxlength="250" name="proddescri[<?php echo $indice;?>]" id="proddescri[<?php echo $indice;?>]" readonly="readonly" value="<?php echo $nombre_producto;?>"/><?php echo $nombre_producto;?></div></td>
                                      <td width="6%"><div align="center"><input type="hidden" size="1" maxlength="5" class="cajaGeneral cajaSoloLectura" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]"  value="<?php echo $prodcantidad;?>" /><?php echo $prodcantidad;?></div></td>
                                      <td width="8%"><div align="center">
                                                <?php
                                                if($prodcantidad == $cantidad_entregada){
                                                        ?>
                                                        <input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" readonly="readonly" name="prodadespachar[<?php echo $indice;?>]"  id="prodadespachar[<?php echo $indice;?>]" />
                                                        <?php
                                                }else{
                                                        ?>
                                                        <input type="text" size="5" maxlength="10" class="cajaGeneral" value="" name="prodadespachar[<?php echo $indice;?>]"  id="prodadespachar[<?php echo $indice;?>]" onKeyPress="return numbersonly(this,event,'.');" onchange="calcula_resantes(<?php echo $indice;?>)"  />
                                                        <?php
                                                }
                                                ?>
                                      </div></td>
                                      <td width="10%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" value="<?php echo $cantidad_entregada; ?>" name="proddespachados[<?php echo $indice; ?>]" id="proddespachados[<?php echo $indice; ?>]" readonly="readonly" /></div></td>
                                      <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" value="<?php echo $cantidad_pendiente; ?>" name="prodrestantes[<?php echo $indice; ?>]" id="prodrestantes[<?php echo $indice; ?>]" readonly="readonly" style="font-weight:bold;"/></div></td>
                                      <td width="6%"><div align="center" style="background:<?php echo  $color_f; ?>; color:white;" ><input type="hidden" size="5" class="cajaGeneral cajaSoloLectura" name="prodtotal[<?php echo $indice; ?>]" id="prodtotal[<?php echo $indice; ?>]" readonly="readonly" value="<?php echo $prodcantidad;?>" /><?php echo $prodcantidad;?></div></td>
                                      <input type="hidden" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m" />
                                  </tr>
                                <?php
                           }
                      }
                      ?>
                </table>
            </div>
    </div>
	</form>
	 <div id="frmBusqueda3" style="width: 650px;">
        <table  width="100%" border="0" align="right" cellpadding="3" cellspacing="0" class="fuente8">
				<tr>
                                    <td valign="top">  
                                       <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                                           <tr>
                                            <td colspan="2" height="25"> <b>INFORMACION DE LA ENTREGA </b></td>
                                           </tr>
                                           <tr>
                                               <td width="100">Lugar de entrega</td>
                                               <td width="340">
                                                  <?php echo $envio_direccion; ?>
                                               </td>   
                                           </tr>
                                           <tr>
                                            <td>Facturar en</td>
                                            <td>
												<?php echo $fact_direccion; ?>
                                            </td>
                                            <td height="25"><b>OBSERVACION</b></td>
                                           </tr>
                                           <tr>
                                            <td>Fecha límite entrega</td>
                                            <td>
												<?php echo $fechaentrega;?>
                                            </td>
                                            <td  rowspan="3" valign="top"><?php echo $observacion;?></td>
                                           </tr>
                                           <tr>
                                            <td><b>CTA. CTE.</b></td>
                                            <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td>Cta. Cte. S/.</td>
                                            <td><?php echo $ctactesoles;?>
                                                 Cta. Cte. US$ <?php echo $ctactedolares;?></td>
                                           </tr>
                                       </table>
                                    </td>
                                    <td width="10%" valign="top">
                                        <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8" style="margin-top:20px;">
                                           <tr>
                                            <td>Sub-total</td>
                                            <td width="10%" align="right"><div align="right"><?php echo round($preciototal,2);?></div></td>
                                            </tr>
                                            <tr>
                                                <td class="busqueda">Descuento</td>
                                                <td align="right"><div align="right"><?php echo round($descuentotal,2);?></div></td>
                                            </tr>
                                            <tr>
                                                <td class="busqueda">IGV</td>
                                                <td align="right">
                                                    <div align="right"><?php echo round($igvtotal,2);?></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="busqueda">Percepci&oacute;n</td>
                                                <td align="right">
                                                    <div align="right"><?php echo round($percepciontotal,2);?></div>
                                                </td>
                                                </tr>
                                            <tr>
                                                <td class="busqueda">Precio Total</td>
                                                <td align="right">
                                                    <div align="right"><?php echo round($importetotal,2);?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
			</table>
    </div>
	
    <table  width="650" border="0"  cellpadding="3" cellspacing="0" class="fuente8" style="margin-top:20px; display:none;">
        <tr>
            <td align="right">Sub-total</td>
            <td width="10%" align="left"><div align="left"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal, 2); ?>" /></div></td>
            <td class="busqueda" align="right">Descuento</td>
            <td align="left"><div align="left"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal, 2); ?>" /></div></td>
            <td class="busqueda" align="right">IGV</td>
            <td align="left">
                <div align="left"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly="readonly" value="<?php echo round($igvtotal, 2); ?>" /></div>
            </td>
            <td class="busqueda" align="right">Percepci&oacute;n</td>
            <td align="left">
                <div align="left"><input class="cajaTotales" name="percepciontotal" type="text" id="percepciontotal" size="12" align="right" readonly="readonly" value="<?php echo round($percepciontotal, 2); ?>" /></div>
            </td>
            <td class="busqueda" align="right">Precio Total</td>
            <td align="left">
                <div align="left"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly="readonly" value="<?php echo round($importetotal, 2); ?>" /></div>
            </td>
        </tr>
    </table>
	
    <br />
	<br />
    <div style="margin-top:70px; clear:both">
            <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
            <a href="javascript:;" id="agregarOcompra"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" width="72" height="22" class="imgBoton" ></a>
            <a href="javascript:;" id="limpiarOcompra"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
            <a href="javascript:;" id="cerrarOcompra"><img src="<?=$base_url;?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" width="70" height="22" class="imgBoton" ></a>
            <?php echo $oculto?>
    </div>
</form>
</div>

