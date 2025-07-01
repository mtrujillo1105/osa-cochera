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
	<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/compras/presupuesto.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
    <script type="text/javascript">
     $(document).ready(function(){
	 almacen = $("#cboCompania").val();
        $("a#linkVerPersona").fancybox({
                'width'	         : 800,
                    'height'         : 500,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : false,
                    'type'	         : 'iframe'
        });  
        $("#linkSelecProveedor").fancybox({
                 'width'	         : 800,
                    'height'         : 500,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : false,
                    'type'	         : 'iframe'
        });  
        $("#linkSelecProducto").fancybox({
                 'width'	         : 800,
                    'height'         : 500,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : false,
                    'type'	         : 'iframe'
        }); 
		$(".verDocuRefe").fancybox({
            'width'          : 670,
            'height'         : 420,
            'autoScale'      : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': true,
            'modal'          : false,
            'type'	     : 'iframe',
            'onStart'        : function(){

               

                    if($('#proveedor').val()==''){
                        alert('Debe seleccionar el proveedor.');
                        $('#nombre_proveedor').focus();
                        return false;
                    }else{
                       
					   if($('.verDocuRefe::checked').val()=='P' )	
						baseurl=base_url+'index.php/compras/presupuesto/ventana_muestra_presupuestoRecu/C/'+$('#proveedor').val()+'/SELECT_HEADER/<?php echo $tipo_docu; ?>/'+almacen+'/P';
					
						$('.verDocuRefe::checked').attr('href', baseurl );
						}
                }                         
            
        });
  
		
		

     });
	 
	  $(function() {
	 $("#buscar_producto").autocomplete({
            //flag = $("#flagBS").val();
            source: function(request, response){
                $.ajax({ 
                  url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/"+$("#flagBS").val()+"/"+$("#compania").val(),
                    type: "POST",
                    data:  { 
                        term: $("#buscar_producto").val()
                    },
                    dataType: "json", 
                    success: function(data){
                        response(data);
                    }
                });
            }, 
            select: function(event, ui){
                $("#buscar_producto").val(ui.item.codinterno);
                $("#producto").val(ui.item.codigo)
                $("#codproducto").val(ui.item.codinterno);
                $("#costo").val(ui.item.pcosto);
                $("#cantidad").focus();
                listar_unidad_medida_producto(ui.item.codigo);
                // obtener_producto_desde_codigo(n);

                // return false;

            },
            minLength: 2
        });
                
			$("#nombre_proveedor").autocomplete({
            source: function(request, response){
                $.ajax({  url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                    type: "POST",
                    data:  { term: $("#nombre_proveedor").val()  },
                    dataType: "json", 
                    success: function(data){response(data); }
                });

            }, 
            select: function(event, ui){
                $("#buscar_proveedor").val(ui.item.ruc)
                $("#proveedor").val(ui.item.codigo);
                $("#ruc_proveedor").val(ui.item.ruc);
                
                empresa=ui.item.codigoEmpresa;
                limpiar_combobox('contacto');
                listar_contactos(empresa);
                
				$("#buscar_producto").focus();
            },
            minLength: 2
        });	
	 });
	 
	 
     function seleccionar_proveedor(codigo,ruc,razon_social, empresa, persona,otro,otro2){
        $("#proveedor").val(codigo);
        $("#ruc_proveedor").val(ruc);
        $("#nombre_proveedor").val(razon_social);

        if(empresa!='0'){
            if(empresa!=$('#empresa').val()){
                limpiar_combobox('contacto');
                $('#empresa').val(empresa);
                $('#persona').val(0);
                listar_contactos(empresa);
            }
        }
        else{
            limpiar_combobox('contacto');
            $('#linkVerPersona').hide();
            if(persona!=$('#persona').val()){
                $('#empresa').val(0);
                $('#persona').val(persona);
            }
        }
    }
     function seleccionar_producto(codigo,interno,familia,stock,costo){
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        $("#cantidad").focus();
        listar_unidad_medida_producto(codigo);
    }
	
	
	function seleccionar_cotizacion(guia,serieguia,numeroguia){
				tipo_oper = 'C';
                agregar_todocotizacion(guia,tipo_oper);
                serienumero="Numero de COTIZACION :"+serieguia+ " - " + numeroguia;
                $("#serieguiaverPre").html(serienumero);
                $("#serieguiaverPre").show(2000);
            }	

     </script>
	</head>
	<body <?php echo $onload;?>>	
	<!-- Inicio -->
	<input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
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
		    <td width="4%">N&uacute;mero</td>
		    <td width="41%" valign="middle">
                        <?php 
                      switch($tipo_codificacion){
                            case '1': echo '<input type="text" name="numero" id="numero" value="'.$numero.'" class="cajaGeneral cajaSoloLectura" readonly="readonly" size="10" maxlength="10"  /><input type="hidden" name="serie" id="serie" value="'.$serie.'" class="cajaGeneral cajaSoloLectura" readonly="readonly" size="10" maxlength="10"  />'; break;
                            case '2': echo '<input type="text" name="serie" id="serie" value="'.$serie.'" class="cajaGeneral" size="3" maxlength="10"  /> ';
                                      echo '<input type="text" name="numero" id="numero" value="'.$numero.'" class="cajaGeneral" size="10" maxlength="10"  /> ';
                                      echo '<a href="javascript:;" id="linkVerSerieNum"'.($codigo!='' ? 'style="display:none"' : '').'><p style="display:none">'.$serie_suger.'-'.$numero_suger.'</p><image src="'.base_url().'images/flecha.png?=<?=IMG;?>" border="0" alt="Serie y número sugerido" title="Serie y número sugerido" /></a>'; break;
                            case '3': echo '<input type="text" name="codigo_usuario" id="codigo_usuario" value="'.$codigo_usuario.'" class="cajaGeneral" size="20" maxlength="50"  />'; break;
                        }
                        ?>
                    </td>
                    <!--<td width="9%" valign="middle">Solicitud de Cotizaci&oacute;n</td>
                    <td width="23%" valign="middle"><select name="cotizacion" id="cotizacion" class="comboMedio"><option value='0'>::Seleccione::</option></select></td>-->
                    <td width="9%" valign="middle"></td>
					<td width="10%" valign="middle">
								<label for="P"><img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>" class="imgBoton" /></label>
								<input type="radio" name="referenciar" id="P" value="P" href="javascript:;" class="verDocuRefe" style="display:none;">
								<div id="serieguiaverPre" name="serieguiaverPre" style="background-color: #cc7700; color:fff; padding:5px;display:none" ></div>
											
					</td>
					<td width="9%" valign="middle">Fecha</td>
                    <td width="20%" valign="middle"><input NAME="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy;?>" size="10" maxlength="10" readonly="readonly" />
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
                    <td>Proveedor *</td>
                    <td valign="middle">
						<input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>" />
						<input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;
						<input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" onkeypress="return numbersonly(this,event,'.');" />
						<input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50"  value="<?php echo $nombre_proveedor; ?>" />
						<a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a>
					</td>
					
		    <td valign="middle">Moneda *</td>
		    <td valign="middle">
                         <select name="moneda" id="moneda" class="comboMedio"><?php echo $cboMoneda;?></select>
                    </td>
		    <!--<td valign="middle">Vendedor</td>
                    <td><?php echo $cboVendedor;?></td>-->
		   </tr>
		   <tr>
                   <td>Contacto </td>
		    <td><?php echo $cboContacto;?>
                        <a href="<?=$base_url;?>index.php/maestros/persona/persona_ventana_mostrar/<?php if($contacto!=''){ $temp=explode('-', $contacto);  echo $temp[0];} else echo '1';  ?>" <?php if($contacto=='') echo 'style="display:none;"'; ?> id="linkVerPersona"><img height='16' id="" width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Más Información' border='0' /></a>
                   </td>
		   <td>I.G.V.</td>
		   <td><input NAME="igv" type="text" class="cajaGeneral cajaSoloLectura" size="2" maxlength="2" id="igv" value="<?php echo $igv;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly" /> %</td>
                 <td>Descuento</td>
                 <td><input NAME="descuento" type="text" class="cajaGeneral" size="2" maxlength="2" id="descuento"  value="<?php echo $descuento;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_descuento_total();" /> %</td>
                </tr>
		   <tr>
        <td>Pedido</td>
        <td>
		<?php echo $cboPedidos; ?>
        </td>
        <td id="text_cotizacion"></td>
        <td id="show_cotizaciones">
        
        </td>
       <td></td>
       <td></td>
       </tr>
		</table>
		</div>	
		<div id="frmBusqueda"  <?php echo $hidden;?>>
		<table class="fuente8" width="100%" cellspacing='0' cellpadding='3' border='0' >
		  <tr>
                            <td width="6%">
                                <select name="flagBS" id="flagBS" style="width:50px;" ass="comboMedio" onchange="limpiar_campos_producto()">
                                    <option value="B" selected="selected" title="Producto">P</option>
                                    <option value="S" title="Servicio">S</option>
                                </select>
                            </td>
                            <td width="37%">
                                <input name="producto" type="hidden" class="cajaGeneral" id="producto" />
                                <input name="buscar_producto" type="text" class="cajaGeneral" id="buscar_producto" size="10" title="Ingrese parte del nombre o el nro. de serie del producto, luego presione ENTER." />&nbsp;
                                <input name="codproducto" type="hidden" class="cajaGeneral" id="codproducto" size="10" maxlength="20"  />&nbsp;
                                <input NAME="nombre_producto" type="text" class="cajaGeneral cajaSoloLectura" id="nombre_producto" size="40" readonly="readonly" />
                                <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_selecciona_producto/" id="linkSelecProducto"></a>
							
							<a id="linkMostrarNumero" href="#ventana"></a>
							<div id="ventana" style="display: none;" >
                                <div id="imprimir" style="padding:20px; text-align: center">
                                    <span style="font-weight: bold;">
                                        <?php echo 'SERIE-NUMERO'; ?>
                                        <br/>
                                        <input type="text" name="ser_imp" id="ser_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="<?php echo $serie_suger?>" class="cajaGeneral" maxlength="3" size="3">-
                                        <input type="text" name="num_imp" id="num_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="<?php echo $numero_suger?>" class="cajaGeneral" maxlength="10" size="10">
                                    </span> 
                                    <br/>
                                    <a href="javascript:;" id="seriePreVente"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                                </div>  
					 
                            </td>
                            <td width="6%">Cantidad</td>
                            <td width="22%">
                                <input NAME="cantidad" type="text" class="cajaGeneral"  id="cantidad" value="" size="3" maxlength="10" onkeypress="return numbersonly(this,event,'.');" />
<!--                                                                                                                obtener_precio_producto-->
                                <select name="unidad_medida" id="unidad_medida" class="comboMedio" onchange="listar_precios_x_producto_unidad();"><option value="">::Seleccione::</option></select>
                            </td>
                            <td width="17%">
                                <select name="precioProducto" id="precioProducto" class="comboPequeno" onchange="mostrar_precio();" style="width:84px;">
                                    <option value="0">::Seleccion::</option>
                                </select>
                                <input NAME="precio" type="text" class="cajaGeneral" id="precio" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');" title="<?php if ($tipo_docu != 'B' && $contiene_igv == true) echo 'Precio con IGV'; ?>" />
                            </td>
                            <td width="12%">
                                <div align="right"><a href="javascript:;" onClick="agregar_producto_presupuesto();"><img src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom"></a></div>
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
                            <?php //if ($tipo_docu != 'B') { ?>
                                <td width="6%"><div align="center">PU S/IGV</div></td>
                            <?php //} ?>
                            <td width="6%"><div align="center">PRECIO</div></td>
                            <?php //if ($tipo_docu != 'B') { ?>
                                <td width="6%"><div align="center">I.G.V.</div></td>
                            <?php //} ?>
                            <td width="6%"><div align="center">IMPORTE</div></td>
                        </tr>
			</table>
                    <div>
                            <table id="tblDetalleCotizacion" class="fuente8" width="100%" border="0">
                             <?php
                                  if(count($detalle_presupuesto)>0){
                                       foreach($detalle_presupuesto as $indice=>$valor){
                                            $detacodi        = $valor->PRESDEP_Codigo;
                                            $prodproducto    = $valor->PROD_Codigo;
											$flagBS   = $valor->flagBS;
                                            $unidad_medida   = $valor->UNDMED_Codigo;
                                            $codigo_interno  = $valor->PROD_CodigoInterno;
                                            $prodcantidad    = $valor->PRESDEC_Cantidad;
                                            $nombre_producto = $valor->PROD_Nombre;
                                            $nombre_unidad   =  $valor->UNDMED_Simbolo;
                                            $prodpu          = $valor->PRESDEC_Pu;
                                            $prodsubtotal    = $valor->PRESDEC_Subtotal;
                                            $proddescuento   = $valor->PRESDEC_Descuento;
                                            $prodigv         = $valor->PRESDEC_Igv;
                                            $prodtotal       = $valor->PRESDEC_Total;
											$prodpu_conigv   = $valor->PRESDEC_Pu_ConIgv;
                                            $prodsubtotal_conigv    = $valor->PRESDEC_Subtotal_ConIgv;
                                            $proddescuento_conigv   = $valor->PRESDEC_Descuento_ConIgv;
                                            if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                                            ?>
                                             <tr class="<?php echo $clase; ?>">
                                        <td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_presupuesto(<?php echo $indice; ?>);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                                        <td width="4%"><div align="center"><?php echo $indice + 1; ?></div></td>
                                        <td width="10%"><div align="center"><?php echo $codigo_interno; ?></div></td>
                                        <td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[<?php echo $indice; ?>]" id="proddescri[<?php echo $indice; ?>]" value="<?php echo $nombre_producto; ?>" /></div></td>
                                        <?php //if ($tipo_docu != 'B') { ?>
                                            <td width="10%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[<?php echo $indice; ?>]" id="prodcantidad[<?php echo $indice; ?>]" value="<?php echo $prodcantidad; ?>" onblur="calcula_importe(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad; ?></div></td>
                                            <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[<?php echo $indice; ?>]" id="prodpu_conigv[<?php echo $indice; ?>]" value="<?php echo $prodpu_conigv; ?>" onblur="modifica_pu_conigv(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /></div></td>
                                            <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[<?php echo $indice; ?>]" id="prodpu[<?php echo $indice; ?>]" value="<?php echo $prodpu; ?>" onblur="modifica_pu(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" />
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[<?php echo $indice; ?>]" id="prodprecio[<?php echo $indice; ?>]" value="<?php echo $prodsubtotal; ?>" readonly="readonly" />
                                                        </div></td>
                                               <!-- <?php //} else { ?>
                                                    <td width="6%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[<?php echo $indice; ?>]" id="prodcantidad[<?php echo $indice; ?>]" value="<?php echo $prodcantidad; ?>" onblur="calcula_importe_conigv(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad; ?></div></td>
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[<?php echo $indice; ?>]" id="prodpu_conigv[<?php echo $indice; ?>]" value="<?php echo $prodpu_conigv; ?>" onblur="calcula_importe_conigv(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /></div></td>
                                                    <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv[<?php echo $indice; ?>]" id="prodprecio_conigv[<?php echo $indice; ?>]" value="<?php echo $prodsubtotal_conigv; ?>" readonly="readonly" /></div></td>
                                                <?php //} ?>    -->
                                                <?php //if ($tipo_docu != 'B') { ?>
                                                    <td width="6%">
                                                        <div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[<?php echo $indice; ?>]" id="prodigv[<?php echo $indice; ?>]" readonly="readonly" value="<?php echo $prodigv; ?>" /></div>
                                                    </td>
                                                <?php //} ?>
                                                <td width="6%">
                                                    <div align="center">
                                                        <input type="hidden" name="detaccion[<?php echo $indice; ?>]" id="detaccion[<?php echo $indice; ?>]" value="m">
                                                        <input type="hidden" name="prodigv100[<?php echo $indice; ?>]" id="prodigv100[<?php echo $indice; ?>]" value="<?php echo $igv; ?>" />
                                                        <input type="hidden" name="detacodi[<?php echo $indice; ?>]" id="detacodi[<?php echo $indice; ?>]" value="<?php echo $detacodi; ?>" />
                                                        <input type="hidden" name="flagBS[<?php echo $indice; ?>]" id="flagBS[<?php echo $indice; ?>]" value="<?php echo $flagBS; ?>" />
                                                        <input type="hidden" name="prodcodigo[<?php echo $indice; ?>]" id="prodcodigo[<?php echo $indice; ?>]" value="<?php echo $prodproducto; ?>" />
                                                        <input type="hidden"  name="produnidad[<?php echo $indice; ?>]" id="produnidad[<?php echo $indice; ?>]" value="<?php echo $unidad_medida; ?>" />
                                                        <input type="hidden" name="proddescuento100[<?php echo $indice; ?>]" id="proddescuento100[<?php echo $indice; ?>]" value="<?php echo $descuento; ?>" />
                                                        <?php //if ($tipo_docu != 'B') { ?>
                                                            <input type="hidden" name="proddescuento[<?php echo $indice; ?>]" id="proddescuento[<?php echo $indice; ?>]" value="<?php echo $proddescuento; ?>" onblur="calcula_importe2(<?php echo $indice; ?>);" />
                                                        <?php //} else { ?>
                                                          <!--  <input type="hidden" name="proddescuento_conigv[<?php echo $indice; ?>]" id="proddescuento_conigv[<?php echo $indice; ?>]" value="<?php echo $proddescuento_conigv; ?>" onblur="calcula_importe2_conigv(<?php echo $indice; ?>);" />-->
                                                        <?php //} ?>
                                                            <input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[<?php echo $indice; ?>]" id="prodimporte[<?php echo $indice; ?>]" readonly="readonly" value="<?php echo number_format($prodtotal,2); ?>" />
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
    <table width="100%" border="0" align="right" cellpadding='3' cellspacing='0' class="fuente8">
       <tr>
        <td>   
           <table  width="100%" border="0" align="right" cellpadding='3' cellspacing='0' >
                               <tr>
                                <td colspan="2" height="25"> <b>CONDICIONES DE VENTA </b></td>
                                <td style="visibility:hidden;"><b>ESTADO</b></td>
                               </tr>
                               <tr>
                                <td>Lugar de entrega</td>
                                <td><input type="text" size="56" maxlength="250" class="cajaGeneral" name="lugar_entrega" id="lugar_entrega" value="<?php if($codigo!='') echo  $lugar_entrega; ?>" />
                                    <a href="javascript:;"  id="linkVerDirecciones"><image src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0" /></a>
                                    <div id="lista_direcciones" class="cuadro_flotante" style="width:305px">
                                         <ul>
                                        </ul>
                                    </div>
                                </td>
                                <td style="visibility:hidden;"><select name="estado" id="estado" class="comboPequeno">
                                        <option <?php if($estado=='1') echo 'selected="selected"'; ?> value="1">Activo</option>
                                        <option <?php if($estado=='0') echo 'selected="selected"'; ?> value="0">Anulado</option>
                                    </select>
                                    
                                </td>
                               </tr>
                               <tr>
                                <td width="15%">Forma de Pago</td>
                                <td><select name="forma_pago" id="forma_pago" class="comboMedio" style="width:200px"><?php echo $cboFormaPago;?></select></td>
                                <td><b>OBSERVACION</b></td>
                               </tr>
                               <tr>
                                <td width="15%">Tiempo de entrega</td>
                                <td><textarea name="tiempo_entrega" id="tiempo_entrega" class="cajaTextArea" cols="52" rows="2"><?php echo $tiempo_entrega;?></textarea></td>
                                <td rowspan="5" valign="top"><textarea id="observacion" name="observacion" class="cajaTextArea" cols="52" rows="8"><?php echo $observacion;?></textarea></td>
                               </tr>
                               <tr>
                                <td width="15%">Garantía</td>
                                <td><input type="text" size="56" maxlength="100" class="cajaGeneral" name="garantia" id="garantia" value="<?php if($codigo!='') echo  $garantia; else echo '1 AÑO CONTRA DEFECTOS DE FABRICA'; ?>" /></td>
                               </tr>
                               <tr>
                                <td width="15%">Validez de la pte.</td>
                                <td><input type="text" size="56" maxlength="100" class="cajaGeneral" name="validez" id="validez" value="<?php if($codigo!='') echo  $validez; else echo (FORMATO_IMPRESION==4 ? '5' : '30' ).' CALENDARIOS'; ?>" /></td>
                               </tr>
                               <tr>
                                <td width="15%">Modo de impresión</td>
                                <td><select name="modo_impresion" <?php if($tipo_docu=='B') echo 'disabled="disabled"'; ?> id="modo_impresion" class="comboGrande" style="width:307px">
                                        <option <?php if($modo_impresion=='1') echo 'selected="selected"'; ?> value="1">LOS PRECIOS DE LOS PRODUCTOS DEBEN INCLUIR IGV</option>
                                        <option <?php if($modo_impresion=='2') echo 'selected="selected"'; ?> value="2">LOS PRECIOS DE LOS PRODUCTOS NO DEBEN INCLUIR IGV</option>
                                    </select></td>
                               </tr>
                           </table>
                        </td>
                          <td width="10%" valign="top">
                                <table  width="5%" border="0" align="right" cellpadding=3 cellspacing=0  style="margin-top:20px;">
                                    <tr>
                                        <td>Sub-total</td>
                                            <td width="10%" align="right"><div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal, 2); ?>" /></div></td>
                                    </tr>
                                    <tr>
                                        <td>Descuento</td>
                                      <td align="right"><div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal, 2); ?>" /></div></td>
                                       
                                    </tr>
                                    <?php //if ($tipo_docu != 'B') { ?>
                                        <tr>
                                            <td>IGV</td>
                                            <td align="right"><div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly="readonly" value="<?php echo round($igvtotal, 2); ?>" /></div></td>
                                        </tr>
                                    <?php //} ?>
                                    <tr>
                                        <td>Precio Total</td>
                                        <td align="right"><div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly="readonly" value="<?php echo round($importetotal, 2); ?>" /></div></td>
                                    </tr> 
                                </table>
                            </td>
                    </tr>
                </table>
                        
		</div>		
		<br />
		<div id="botonBusqueda2" style="padding-top:20px;">
			<img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
                        <a href="javascript:;" id="grabarCotizacion"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="limpiarCotizacion"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
			<a href="javascript:;" id="cancelarCotizacion"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
			<?php echo $oculto?>
		</div>
                
	</div>
    </form>
	</body>
</html>