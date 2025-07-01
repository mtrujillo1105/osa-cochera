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
    <script type="text/javascript" src="<?=$base_url;?>public/js/ventas/credito.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
    <script type="text/javascript">		
     $(document).ready(function() {
        $("a#linkVerCliente, a#linkVerProveedor, #linkSelecProducto").fancybox({
                'width'	     : 700,
                'height'         : 550,
                'autoScale'	     : false,
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : true,
                'type'	     : 'iframe'
        });
        $("#linkSelecProducto").fancybox({
                'width'	         : 800,
                'height'         : 500,
                'autoScale'	 : false,
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : false,
                'type'	         : 'iframe'
        });
        $("a#linkVerProducto").fancybox({
                'width'	     : 800,
                'height'         : 650,
                'autoScale'	     : false,
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : true,
                'type'	     : 'iframe'
        });
        $("#linkVerImpresion").fancybox({
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : true
		});
		
		$("a#verOrden").fancybox({
                'width'	     : 780,
                'height'         : 450,
                'autoScale'	     : false,
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : true,
                'type'	     : 'iframe'
        });
        		
    });

     function seleccionar_cliente(codigo,ruc,razon_social){
          $("#cliente").val(codigo);
          $("#ruc_cliente").val(ruc);
          $("#nombre_cliente").val(razon_social);
     }
     function seleccionar_proveedor(codigo,ruc,razon_social){
          $("#proveedor").val(codigo);
          $("#ruc_proveedor").val(ruc);
          $("#nombre_proveedor").val(razon_social);
     }
     function seleccionar_producto(producto,cod_interno,familia,stock,costo,flagGenInd){
             $("#codproducto").val(cod_interno);
             $("#producto").val(producto);
             $("#cantidad").focus();
             $("#costo").val(costo);
             $("#flagGenInd").val(flagGenInd);
             
             listar_unidad_medida_producto(producto);

     }
     </script>
	</head>
	<body>
	<?php
	// stylo para ocultar botones combos, etc
	$style = "";
	if(FORMATO_IMPRESION == 8){
		$style="display:none;";
	}
	?>
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
		<table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
		  <tr>
		    <td width="8%">N&uacute;mero *</td>
		    <td width="38%" valign="middle">
                     <input class="cajaGeneral" name="serie" type="text" id="serie" size="3" maxlength="3" value="<?php echo $serie;?>" />&nbsp;
                     <input class="cajaGeneral" name="numero" type="text" id="numero" size="10" maxlength="10" value="<?php echo $numero;?>" />
                     <?php if($tipo_oper=='V'){ ?>
                     <a href="javascript:;" id="linkVerSerieNum" <?php if($codigo!='') echo 'style="display:none"' ?>><p style="display:none"><?php echo $serie_suger.'-'.$numero_suger?></p><image src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0" alt="Serie y número sugerido" title="Serie y número sugerido" /></a>
                     <?php } ?>
                     <label style="margin-left:80px; margin-right: 20px;">IGV</label>
                     <input NAME="igv" type="text" class="cajaGeneral cajaSoloLectura" id="igv" size="2" maxlength="2" value="<?php echo $igv;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly" /> %
                     <input type="hidden" name="descuento" id="descuento" value="" />
                    </td>
                    <td width="9%" valign="middle">Presupuesto</td>
                    <td width="23%" valign="middle">
                                <select name="presupuesto" id="presupuesto" class="comboMedio"  onfocus="<?php echo $focus;?>" onchange="obtener_detalle_presupuesto()" ><?php echo $cboPresupuesto;?></select>
                    </td>
                    <td width="7%" valign="middle">Fecha</td>
                    <td width="22%" valign="middle"><input NAME="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy;?>" size="10" maxlength="10" readonly="readonly" />
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField     :    "fecha",      // id del campo de texto
                                ifFormat       :    "%d/%m/%Y",       // formaClienteto de la fecha, cuando se escriba en el campo de texto
                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                            });
                        </script>
                    </td>
		  </tr>
		  <tr>
                    <?php if($tipo_oper=='V'){ ?>
                    <td>Cliente *</td>
                    <td valign="middle">
                         <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente?>" />
                         <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente;?>" onkeypress="return numbersonly(this,event,'.');" />
                         <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_cliente;?>" />
                         <a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                    </td>
                    <?php }else{ ?>
                    <td>Proveedor *</td>
                    <td valign="middle">
                         <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor?>" />
                         <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor;?>" onkeypress="return numbersonly(this,event,'.');" />
                         <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_proveedor;?>" />
                         <a href="<?=$base_url;?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                    </td>
                    <?php } ?>
		    <td valign="middle">Moneda *</td>
		    <td valign="middle">
                	<select name="moneda" id="moneda" class="comboPequeno" style="width:150px;"><?php echo $cboMoneda;?></select>
		    <!--<td valign="middle">Descuento</td>
                    <td><input NAME="descuento" type="text" class="cajaGeneral" id="descuento"  size="2" maxlength="2" value="<?php echo $descuento;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_descuento_total();" />%  </td>-->
                    <td colspan="2">
                        <?php if($tipo_oper=='C'){ ?>
                        <a href="<?php echo base_url() ?>index.php/compras/ocompra/ventana_muestra_proveedor/<?php echo $tipo_oper; ?>" id="verOrden"><img src="<?php echo base_url() ?>public/images/icons/referenciardoc.png?=<?=IMG;?>" class="imgBoton" /></a>
                        <?php } ?>
                    </td>
		  </tr>
		  <tr>
                    <td>TDC</td>
                    <td>
                        <input NAME="tdc" type="text" class="cajaGeneral cajaSoloLectura" id="tdc" size="3" value="<?php echo $tdc;?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly" />
                        <?php if($tipo_oper=='V'){ ?>Vendedor<?php } ?>
                        <?php if($tipo_oper=='V'){ ?><select name="vendedor" id="vendedor" class="comboMedio" style="width:210px;"><?php echo $cboVendedor;?></select><?php } ?>
                    </td>
		    <td colspan="4">&nbsp;</td>
		    </tr>
              <!--<tr>
                <td><div style="<?php echo $style; ?>">Doc. Refer.</div></td>
                <td><div style="<?php echo $style; ?>"><input type="text" name="docurefe_codigo" id="docurefe_codigo" class="cajaGeneral" size="25" maxlength="50" value="<?php echo $docurefe_codigo;?>" /></div></td>
                <td><div style="<?php echo $style; ?>">G. Remisión</div></td>
                <td>
                        <?php
                        if(FORMATO_IMPRESION == 8){
                                ?>
                                <a href="<?php echo base_url() ?>index.php/compras/ocompra/ventana_muestra_proveedor/<?php echo $tipo_oper; ?>" id="verOrden"><img src="<?php echo base_url() ?>public/images/icons/referenciardoc.png?=<?=IMG;?>" /></a>
                                <?php
                        }
                        ?>
                        <div style="<?php echo $style; ?>">
                                <select <?php if($tipo_docu=='D') echo 'disabled="disabled"'; ?> name="guiaremision" id="guiaremision" class="comboMedio" onchange="obtener_detalle_guiarem()"><?php echo $cboGuiaRemision;?></select>
                                Número <input <?php if($tipo_docu=='D') echo 'readonly="readonly"'; ?> type="text" name="guiaremision_codigo" id="guiaremision_codigo" class="cajaGeneral <?php if($tipo_docu=='D') echo 'cajaSoloLectura'; ?>" size="17" maxlength="50" value="<?php echo $guiarem_codigo;?>" />
                        </div>
                </td>
                <td>O. <?php if($tipo_oper=='C') echo 'Compra'; else echo 'Venta'; ?></td>
                <td><select <?php if($tipo_docu=='D') echo 'disabled="disabled"'; ?> name="ordencompra" id="ordencompra" class="comboMedio"><?php echo $cboOrdencompra;?></select></td>
              </tr>
              -->
		</table>
</div>	
		<div id="frmBusqueda"  <?php echo $hidden;?>>
		<table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
		  <tr>
			<td width="8%">
                            <select name="flagBS" id="flagBS" style="width:68px;" ass="comboMedio" onchange="limpiar_campos_producto()">
                                <option value="B" selected="selected" title="Producto">P</option>
                                <option value="S" title="Servicio">S</option>
                            </select>
                        </td>
			<td width="37%">
                                <input name="producto" type="hidden" class="cajaGeneral" id="producto" />
                                <input name="buscar_producto" type="text" class="cajaGeneral" id="buscar_producto" size="10" title="Ingrese parte del nombre o el nro. de serie del producto, luego presione ENTER." />&nbsp;
                                <input name="codproducto" type="hidden" class="cajaGeneral" id="codproducto" size="10" maxlength="20" onblur="obtener_producto();" />
                                <input NAME="nombre_producto" type="text" class="cajaGeneral cajaSoloLectura" id="nombre_producto" size="39" readonly="readonly" />
                                <input name="costo" type="hidden" id="costo" />
                                <input name="flagGenInd" type="hidden" id="flagGenInd" />
                                <!--<a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
                                <a href="<?=$base_url;?>index.php/almacen/producto/ventana_selecciona_producto/" id="linkSelecProducto"></a>
                                
                        </td>
                        <td width="6%">Cantidad</td>
			<td width="24%">
                            <input NAME="cantidad" type="text" class="cajaGeneral" id="cantidad" size="3" maxlength="10" onkeypress="return numbersonly(this,event,'.');" />
			    <select name="unidad_medida" id="unidad_medida" class="comboMedio" <?php if($tipo_oper=='V') echo 'onchange="listar_precios_x_producto_unidad();"'; ?>><option value="0">::Seleccione::</option></select>
                	</td>
                        <td width="16%">
                            <select name="precioProducto" id="precioProducto" class="comboPequeno" onchange="mostrar_precio();" style="width:84px;">
                                    <option value="0">::Seleccion::</option>
                            </select>
                            <input NAME="precio" type="text" class="cajaGeneral" id="precio" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');" title="<?php if($tipo_docu!='D' && $contiene_igv==true) echo 'Precio con IGV'; ?>" />
                        </td>
			<td width="10%">
                            <div align="right"><a href="javascript:;" onClick="agregar_producto_comprobante();"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom"></a></div>
                        </td>
		  </tr>
		</table>
		</div>
		<div id="frmBusqueda" style="height:250px; overflow: auto">
			<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
				<tr class="cabeceraTabla">
                                        <td width="3%"><div align="center">&nbsp;</div></td>
					<td width="4%"><div align="center">ITEM</div></td>
					<td width="10%"><div align="center">C&Oacute;DIGO</div></td>
					<td><div align="center">DESCRIPCI&Oacute;N</div></td>
					<td width="10%"><div align="center">CANTIDAD</div></td>
                                        <td width="6%"><div align="center">PU C/IGV</div></td>
                                        <?php if($tipo_docu!='D'){ ?>
                                        <td width="6%"><div align="center">PU S/IGV</div></td>
                                        <?php } ?>
					<td width="6%"><div align="center">PRECIO</div></td>
					<!--<td width="6%"><div align="center">DSCTO</div></td>-->
                                        <?php if($tipo_docu!='D'){ ?>
					<td width="6%"><div align="center">IGV</div></td>
                                        <?php } ?>
					<td width="6%"><div align="center">IMPORTE</div></td>
				</tr>
			</table>
                    <div>
                            <table id="tblDetalleComprobante" class="fuente8" width="100%" border="0">
                             <?php
                                  if(count($detalle_comprobante)>0){
                                       foreach($detalle_comprobante as $indice=>$valor){
                                            $detacodi             = $valor->CREDET_Codigo;
                                            $flagBS               = $valor->flagBS;
                                            $prodproducto         = $valor->PROD_Codigo;
                                            $unidad_medida        = $valor->UNDMED_Codigo;
                                            $codigo_interno       = $valor->PROD_CodigoInterno;
                                            $nombre_producto      = $valor->PROD_Nombre;
                                            $nombre_unidad        =  $valor->UNDMED_Simbolo;
                                            $costo                = $valor->CREDET_Costo;
                                            $GenInd               = $valor->CREDET_GenInd;
                                            $prodcantidad         = $valor->CREDET_Cantidad;
                                            $prodpu               = $valor->CREDET_Pu;
                                            $prodsubtotal         = $valor->CREDET_Subtotal;
                                            $proddescuento        = $valor->CREDET_Descuento;
                                            $prodigv              = $valor->CREDET_Igv;
                                            $prodtotal            = $valor->CREDET_Total;
                                            $prodpu_conigv        = $valor->CREDET_Pu_ConIgv;
                                            $prodsubtotal_conigv  = $valor->CREDET_Subtotal_ConIgv;
                                            $proddescuento_conigv = $valor->CREDET_Descuento_ConIgv;
                                            if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                                            ?>
                                              <tr class="<?php echo $clase;?>">
                                                  <td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_comprobante(<?php echo $indice;?>);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                                                  <td width="4%"><div align="center"><?php echo $indice+1;?></div></td>
                                                  <td width="10%"><div align="center"><?php echo $codigo_interno;?></div></td>
                                                  <td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[<?php echo $indice;?>]" id="proddescri[<?php echo $indice;?>]" value="<?php echo $nombre_producto;?>" /></div></td>
                                                  <?php if($tipo_docu!='D'){ ?>
                                                    <td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onblur="calcula_importe(<?php echo $indice;?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad;?>
                                                        <?php if($GenInd=="I"){
                                                                if($tipo_oper=='V')
                                                                    echo ' <a href="javascript:;" onclick="ventana_producto_serie2('.$indice.')"><img src="'.base_url(),'images/flag-green_icon.png?=<?=IMG;?>" width="20" height="20" border="0" align="absmiddle" /></a>';
                                                                else
                                                                    echo ' <a href="javascript:;" onclick="ventana_producto_serie('.$indice.')"><img src="'.base_url(),'images/flag-green_icon.png?=<?=IMG;?>" width="20" height="20" border="0" align="absmiddle" /></a>';
                                                              }
                                                        ?>
                                                    </div></td>
						    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[<?php echo $indice;?>]" id="prodpu_conigv[<?php echo $indice;?>]" value="<?php echo $prodpu_conigv;?>" onblur="modifica_pu_conigv(<?php echo $indice;?>);" onkeypress="return numbersonly(this,event,'.');" /></div></td>
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[<?php echo $indice;?>]" id="prodpu[<?php echo $indice;?>]" value="<?php echo $prodpu;?>" onblur="modifica_pu(<?php echo $indice;?>);" onkeypress="return numbersonly(this,event,'.');" />
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[<?php echo $indice;?>]" id="prodprecio[<?php echo $indice;?>]" value="<?php echo $prodsubtotal;?>" readonly="readonly" /></div></td>
                                                   <?php }else{?>
						   <td width="10%"><div align="left"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onblur="calcula_importe_conigv(<?php echo $indice;?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad;?></div></td>
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[<?php echo $indice;?>]" id="prodpu_conigv[<?php echo $indice;?>]" value="<?php echo $prodpu_conigv;?>" onblur="calcula_importe_conigv(<?php echo $indice;?>);" onkeypress="return numbersonly(this,event,'.');" /></div></td>
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[<?php echo $indice;?>]" id="prodprecio_conigv[<?php echo $indice;?>]" value="<?php echo $prodsubtotal_conigv;?>" readonly="readonly" /></div></td>
                                                   <?php }?>
                                                  <?php if($tipo_docu!='D'){ ?>
                                                  <td width="6%">
                                                    <div align="center">  
                                                       <input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[<?php echo $indice;?>]" id="prodigv[<?php echo $indice;?>]" readonly="readonly" value="<?php echo $prodigv;?>" />   
                                                    </div>
                                                  </td>
                                                  <?php }?>
                                                  <td width="6%">
                                                    <div align="center">
                                                            <input type="hidden" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m"/>
                                                            <input type="hidden" name="prodigv100[<?php echo $indice;?>]" id="prodigv100[<?php echo $indice;?>]" value="<?php echo $igv;?>"/>
                                                            <input type="hidden" name="detacodi[<?php echo $indice;?>]" id="detacodi[<?php echo $indice;?>]" value="<?php echo $detacodi;?>"/>
                                                            <input type="hidden" name="flagBS[<?php echo $indice;?>]" id="flagBS[<?php echo $indice;?>]" value="<?php echo $flagBS;?>" />
                                                            <input type="hidden" name="prodcodigo[<?php echo $indice;?>]" id="prodcodigo[<?php echo $indice;?>]" value="<?php echo $prodproducto;?>"/>
                                                            <input type="hidden"  name="produnidad[<?php echo $indice;?>]" id="produnidad[<?php echo $indice;?>]" value="<?php echo $unidad_medida;?>"/>
                                                            <input type="hidden" name="flagGenIndDet[<?php echo $indice;?>]" id="flagGenIndDet[<?php echo $indice;?>]" value="<?php echo $GenInd;?>" />
                                                            <input type="hidden" name="prodcosto[<?php echo $indice;?>]" id="prodcosto[<?php echo $indice;?>]" value="<?php echo $costo;?>" />
                                                            <input type="hidden" name="proddescuento100[<?php echo $indice;?>]" id="proddescuento100[<?php echo $indice;?>]" value="<?php echo $descuento;?>" />
                                                            <?php if($tipo_docu!='D'){ ?>
                                                            <input type="hidden" name="proddescuento[<?php echo $indice;?>]" id="proddescuento[<?php echo $indice;?>]" value="<?php echo $proddescuento;?>" onblur="calcula_importe2(<?php echo $indice;?>);" />
                                                            <?php }else{?>
                                                            <input type="hidden" name="proddescuento_conigv[<?php echo $indice;?>]" id="proddescuento_conigv[<?php echo $indice;?>]" value="<?php echo $proddescuento_conigv;?>" onblur="calcula_importe2_conigv(<?php echo $indice;?>);" />
                                                            <?php }?>
                                                            <input type="text" size="5"  class="cajaGeneral cajaSoloLectura" name="prodimporte[<?php echo $indice;?>]" id="prodimporte[<?php echo $indice;?>]" readonly="readonly" value="<?php echo $prodtotal;?>"/>
                                                    </div>
                                                  </td>
                                              </tr>
                                            <?php
                                       }
                                  }
                                  ?>
                            </table>
			</div>					
		</div>	
		<div id="frmBusqueda3">
                    <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
				<tr>
                                    <td width="80%" rowspan="4" align="left">
                                        <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                                           <tr>
                                            <td width="14%" height="30">Modo de impresión</td>
                                            <td width="50%"><select name="modo_impresion" <?php if($tipo_docu=='D') echo 'disabled="disabled"'; ?> id="modo_impresion" class="comboGrande" style="width:307px">
                                                    <option <?php if($modo_impresion=='1') echo 'selected="selected"'; ?> value="1">LOS PRECIOS DE LOS PRODUCTOS DEBEN INCLUIR IGV</option>
                                                    <option <?php if($modo_impresion=='2') echo 'selected="selected"'; ?> value="2">LOS PRECIOS DE LOS PRODUCTOS NO DEBEN INCLUIR IGV</option>
                                                </select>
                                            </td>
                                            <td width="7%">Estado</td>
                                            <td><select name="estado" id="estado" class="comboPequeno">
                                                    <option <?php if($estado=='1') echo 'selected="selected"'; ?> value="1">Activo</option>
                                                    <option <?php if($estado=='0') echo 'selected="selected"'; ?> value="0">Anulado</option>
                                                </select></td>
                                           </tr>
                                           <tr>
                                            <td colspan="4">Observación</td>
                                           </tr>
                                           <tr>
                                            <td colspan="4"><textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%; height:70px;"><?php echo $observacion;?></textarea></td>
                                           </tr>
                                       </table>
                                    </td>
                                    <td width="10%" class="busqueda">Sub-total</td>
                                    <?php if($tipo_docu!='D'){ ?>
                                        <td width="10%" align="right"><div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal,2);?>"></div></td>
                                    <?php }else{ ?>
                                        <td width="10%" align="right"><div align="right"><input class="cajaTotales" name="preciototal_conigv" type="text" id="preciototal_conigv" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal_conigv,2);?>"></div></td>
                                    <?php } ?>
                                </tr>
				<tr>
                                    <td class="busqueda">Descuento</td>
                                    <?php if($tipo_docu!='D'){ ?>
                                        <td align="right"><div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal,2);?>"></div></td>
                                    <?php }else{ ?>
                                        <td align="right"><div align="right"><input class="cajaTotales" name="descuentotal_conigv" type="text" id="descuentotal_conigv" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal_conigv,2);?>"></div></td>
                                    <?php } ?>
                                </tr>
                                <?php if($tipo_docu!='D'){ ?>
				<tr>
                                    <td class="busqueda">IGV</td>
                                    <td align="right"><div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly="readonly" value="<?php echo round($igvtotal,2);?>" /></div></td>
				</tr>
                                <?php } ?>
				<tr>
                                    <td class="busqueda">Precio Total</td>
                                    <td align="right"><div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly="readonly" value="<?php echo round($importetotal,2);?>" /></div></td>
				</tr>                                
			</table>
                        
		</div>	
		<br />
		<div id="botonBusqueda2" style="padding-top:20px;">
			<img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
                        <a href="javascript:;" id="grabarCredito"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="limpiarCredito"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="cancelarCreditoS"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<?php echo $oculto?>
		</div>
                
	</div>
    </form>
<a id="linkVerImpresion" href="#ventana"></a>
<div id="ventana" style="display:none" >
     <div id="imprimir" style="padding:20px; text-align: center">
     <a href="javascript:;" id="imprimirComprobante"><img src="<?=$base_url;?>public/images/icons/impresora.jpg?=<?=IMG;?>" class="imgBoton"  alt="Imprimir"></a>
     <br/>
     <br/>
    <a href="javascript:;" id="cancelarImprimirComprobante"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
    </div>      
</div>
</body>
</html>