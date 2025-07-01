<link rel="stylesheet" href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css">
<link rel="stylesheet" href="<?=$base_url;?>public/css/theme.css?=<?=CSS;?>" type="text/css">
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>	
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/ocompra.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>	
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
$(document).ready(function(){
    $("a#ver_detalles_guias").fancybox({
            'width'	     	 : 750,
            'height'         : 400,
            'autoScale'	     : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	  		 : 'iframe'
    });
	
});
</script>	
<form id="frmOcompra" id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action;?>" onsubmit="return valida_ocompra();">
    <div id="zonaContenido" align="center">
    <div id="tituloForm" class="header" style="width: 750px;"><?php echo $titulo;?></div>
<div id="frmBusqueda" style="width: 750px;">
    <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
      <tr>
        <td width="8%" >N&uacute;mero : </td>
        <td width="38%"><?php echo $numero;?>
         <label style="padding-left:52px;">Código : &nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $codigo_usuario;?>
         
         <input name="pedido" type="hidden" class="cajaPequena2" id="pedido" size="10" maxlength="10" readonly="readonly" value="<?php echo $pedido;?>" /></td>
        <td width="8%">Almacen</td>
        <td width="20%"><?php echo $cboAlmacen[0]->ALMAC_Descripcion;?></td>
        <td width="8%">Fecha</td>
        <td width="18%"><?php echo $hoy;?></td>
      </tr>
      <tr>
        <?php if($tipo_oper=='V'){ ?>
            <td>Cliente *</td>
            <td valign="middle">
                 <?php echo $ruc_cliente;?>
                 <?php echo $nombre_cliente;?>
            </td>
        <?php }else{ ?> 
        <td>Proveedor </td>
        <td>
             <?php echo $ruc_proveedor;?>
             <?php echo $nombre_proveedor;?>
        </td>
        <?php } ?>
        <td>Moneda </td>
        <td> <?php echo $cboMoneda[0]->MONED_Descripcion;?></td>
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
    <div id="frmBusqueda" style="height:250px; width:750px;overflow: auto">
		
		<div class="fuente8" align="left" style="color:white;font-weight:bold;">
			<span style="border:1px solid green;background-color:green;">&nbsp;ENTREGA FINALIZADA&nbsp;</span>
			<span style="border:1px solid orange;background-color:orange;">&nbsp;ENTREGA EN PROCESO&nbsp;</span>
			<span style="border:1px solid red;background-color:red;">&nbsp;ENTREGA SIN MOVIMIENTO&nbsp;</span>
		</div>
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                    <tr class="cabeceraTabla">
                            <td width="4%"><div align="center">ITEM</div></td>
                            <td width="5%"><div align="center">C&Oacute;DIGO</div></td>
                            <td width="43%"><div align="center">DESCRIPCI&Oacute;N</div></td>
                            <td width="7%"><div align="center">CANTIDAD</div></td>
                            <td width="10%"><div align="center">ESTADO</div></td>
                    </tr>
            </table>
        
            <div  >
                <table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">
                 <?php
                      if(count($detalle_ocompra)>0){
                           foreach($detalle_ocompra as $indice=>$valor){
                                $detocom           = $valor->OCOMDEP_Codigo;
                                $flagBS            = $valor->flagBS;
                                $prodproducto      = $valor->PROD_Codigo;
                                $unidad_medida     = $valor->UNDMED_Codigo;
                                $codigo_interno    = $valor->PROD_CodigoInterno;
                                $prodcantidad      = $valor->COTDEC_Cantidad;
                                $nombre_producto   = $valor->PROD_Nombre;
                                $nombre_unidad    =  $valor->UNDMED_Simbolo;
                                $prodpu           = $valor->OCOMDEC_Pu;
                                $prodsubtotal     =  $valor->OCOMDEC_Subtotal;
                                $proddescuento    =  $valor->OCOMDEC_Descuento;
                                $proddescuento2   =  $valor->OCOMDEC_Descuento2;
                                $prodigv          =  $valor->OCOMDEC_Igv;
                                $prodtotal        =  $valor->OCOMDEC_Total;
                                $cantidad_entregada =  $valor->cantidad_entregada;
                                $cantidad_pendiente =  $valor->cantidad_pendiente;
                                $cantidad_vendida   =  $valor->cantidad_vendida;
                                $codigo		    =  $valor->codigo;
                                $tipo_oper	    =  $valor->tipo_oper;

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
                                    <td width="4%"><div align="center"><?php echo $indice+1;?></div></td>
                                    <td width="5%"><div align="center"><?php echo $codigo_interno;?></div></td>
                                    <td width="43%"><div align="left"><?php echo $nombre_producto;?>"</div></td>
                                    <td width="7%"><div align="center"><?php echo $prodcantidad;?></div></td>
                                    <td width="10%" style="background-color:<?php echo $color_f; ?>;color:white;font-weight:bold;"><div align="center"><?php echo $cantidad_entregada." de ".$prodcantidad; ?></div></td>
                                    
                                </tr>
                                <?php
                           }
                      }
                      ?>
                </table>
            </div>
    </div>
    <div id="frmBusqueda3" style="width: 750px;">
        <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
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
</div>
</form>
