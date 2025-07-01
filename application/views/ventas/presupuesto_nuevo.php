<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
?>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/presupuesto.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <script type="text/javascript">
            $(document).ready(function(){
			
				almacen = $("#cboCompania").val();
                $("a#linkVerPersona").fancybox({
                    'width'          : 750,
                    'height'         : 335,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : true,
                    'type'	         : 'iframe'
                }); 
				$("a#linkMostrarNumero").fancybox({
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : true
                });  				
                $("a#linkVerCliente, a#linkSelecCliente").fancybox({
                    'width'	         : 800,
                    'height'         : 500,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : false,
                    'type'	         : 'iframe'
                });  
                $("a#linkVerProducto").fancybox({
                    'width'          : 800,
                    'height'         : 650,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': true,
                    'modal'          : true,
                    'type'	     : 'iframe'
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

                
                    if($('#cliente').val()==''){
                        alert('Debe seleccionar el cliente.');
                        $('#nombre_cliente').focus();
                        return false;
                    }else{
						//alert($('.verDocuRefe::checked').val());
						if($('.verDocuRefe::checked').val()=='P' )					
						baseurl=base_url+'index.php/ventas/presupuesto/ventana_muestra_presupuestoRecu/V/'+$('#cliente').val()+'/SELECT_HEADER/<?php echo $tipo_docu; ?>/'+almacen+'/P';
						//alert(baseurl);
                        $('.verDocuRefe::checked').attr('href', baseurl );
					
					} 
				}					
        });
				 
				 $(".verOrpedido").fancybox({
			            'width'          : 670,
			            'height'         : 420,
			            'autoScale'      : false,
			            'transitionIn'   : 'none',
			            'transitionOut'  : 'none',
			            'showCloseButton': true,
			            'modal'          : false,
			            'type'	     : 'iframe',
			            'onStart'        : function(){

			                
			                    if($('#cliente').val()==''){
			                        alert('Debe seleccionar el cliente .');
			                        $('#nombre_cliente').focus();
			                        return false;
			                    }else{
									if($('.verOrpedido::checked').val()=='OP' )					
									baseurl=base_url+'index.php/ventas/presupuesto/ventana_muestra_Opedido/V/'+$('#cliente').val()+'/SELECT_HEADER/<?php echo $tipo_docu; ?>/'+almacen+'/OP';
								  $('.verOrpedido::checked').attr('href', baseurl );
								
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
            minLength: 3
        });
                                
               
           	$("#nombre_cliente").autocomplete({
                    source: function(request, response){
                        $.ajax({ 
                            url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                            type: "POST",
                            data:  { 
                                term: $("#nombre_cliente").val()
                            },
                            dataType: "json", 
                            success: function(data){
                                response(data);
                            }
                        });
                    }, 
                    select: function(event, ui){
                        //$("#nombre_cliente").val(ui.item.codinterno);
                        $("#buscar_cliente").val(ui.item.ruc)
                        $("#cliente").val(ui.item.codigo);
                        $("#ruc_cliente").val(ui.item.ruc);
                        empresa=ui.item.codigoEmpresa;
                        limpiar_combobox('contacto');
                        listar_contactos(empresa);
                        $("#buscar_producto").focus();

                        
                    },
                    minLength: 3
                });
                
                
                 //****** nuevo para ruc
        $("#buscar_cliente").autocomplete({
            //flag = $("#flagBS").val();
            source: function(request, response){
                $.ajax({ 
                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                    type: "POST",
                    data:  { 
                        term: $("#buscar_cliente").val()
                    },
                    dataType: "json", 
                    success: function(data){                        
                        response(data);                        
                    }
                });
            },
            select: function(event, ui){
            	
                //$("#nombre_cliente").val(ui.item.codinterno);
                $("#nombre_cliente").val(ui.item.nombre);                
                $("#cliente").val(ui.item.codigo);
                $("#ruc_cliente").val(ui.item.ruc);
                empresa=ui.item.codigoEmpresa;
                limpiar_combobox('contacto');
                listar_contactos(empresa);
            	
                
                $("#buscar_producto").focus();
                

                
            },
            minLength: 4
        });
                
                /*-------------------------------*/
                
                
            });
            function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
                $("#cliente").val(codigo);
                $("#ruc_cliente").val(ruc);
                $("#nombre_cliente").val(razon_social);

                if(empresa!=''){
                        limpiar_combobox('contacto');
                        listar_contactos(empresa);
                }else{

                	 limpiar_combobox('contacto');
                     listar_contactos(persona);
                    limpiar_combobox('contacto');
                    $('#linkVerPersona').hide();
                }

                
            }
            function seleccionar_producto(codigo,interno,familia,stock,costo){
                $("#producto").val(codigo);
                $("#codproducto").val(interno);
                $("#cantidad").focus();
                listar_unidad_medida_producto(codigo);
            }
	function seleccionar_presupuesto(guia,serieguia,numeroguia){
				tipo_oper = 'V';
                agregar_todopresupuesto(guia,tipo_oper);
                serienumero="Numero de PRESUPUESTO :"+serieguia+ " - " + numeroguia;
                $("#serieguiaverPre").html(serienumero);
                $("#serieguiaverPre").show(2000);
                $("#serieverPedi").html('');//boramos en caso de secciono pedido
                $("#serieverPedi").hide(2000);
            }

     function seleccionar_pedido(pedido,seriepedido,numeropedido){
				tipo_oper = 'V';
                agregar_todopedido(pedido,tipo_oper);
                serienumero="Numero de PEDIDO :"+seriepedido+ " - " + numeropedido;
                inpedido="<input type='hidden' name='pedidocodigo' id='pedidocodigo' value='"+pedido +"'>";
                $("#serieverPedi").html(inpedido+serienumero);
                $("#serieverPedi").show(2000);
                $("#serieguiaverPre").html('');//borramos presupuesto
                $("#serieguiaverPre").hide(2000);
                calcula_totales();
            }	

        </script>
    <body <?php echo $onload; ?>>	
        <!-- Inicio -->
		<input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
        <div id="VentanaTransparente" style="display:none;">
            <div class="overlay_absolute"></div>
            <div id="cargador" style="z-index:2000">
                <table width="100%" height="100%" border="0" class="fuente8">
                    <tr valign="middle">
                        <td> Por Favor Espere    </td>
                        <td><img src="<?php echo base_url(); ?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- Fin -->		
        <form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">
            <div id="zonaContenido" align="center">
                <?php echo validation_errors("<div class='error'>", '</div>'); ?>
                <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
                <div id="frmBusqueda">
                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
                        <tr>
                       <!--PRESUPUESTO INDEX-->
                     <td width="4%" >N&uacute;mero*</td>



                            <td width="41%" valign="middle" >
                                <?php
                                 if ($tipo_docu == 'V') {
                                switch ($tipo_codificacion) {
                                   case '1':
                                echo 
                                 '<input type="text" name="numero" id="numero" value="' . ($codigo != '' ? $numero : $numero_suger) . '" class="cajaGeneral cajaSoloLectura" readonly="readonly"  size="10" maxlength="10" placeholder="Numero" />';
                                break;
                                
                            case '2':
                                echo '<input type="text" name="serie" id="serie" value="' . $serie . '" class="cajaGeneral cajaSoloLectura" size="3" maxlength="3" placeholder="Serie" /> ';
                                echo '<input type="text" name="numero" id="numero" value="' . $numero . '" class="cajaGeneral cajaSoloLectura" size="10" maxlength="6" placeholder="Numero"  /> ';
                                echo '<a href="javascript:;" id="linkVerSerieNum"' . ($codigo != '' ? 'style="display:none"' : '') . '>
                                <p style="display:none">' . $serie_suger . '-'. $numero_suger . '</p><image src="' . base_url() . 'images/flecha.png?=<?=IMG;?>" border="0" alt="Serie y nÃºmero sugerido" title="Serie y nÃºmero sugerido" /></a>';
                                break;
                            case '3':
                                echo '<input type="text" name="codigo_usuario" id="codigo_usuario" value="' . $codigo_usuario . '" class="cajaGeneral" size="20" maxlength="50"  />';
                                break;
                                }
                            }
                            else {
                        echo '<input type="text" name="serie" id="serie" value="' . $serie . '" class="cajaGeneral" size="3" maxlength="3" placeholder="Serie"  /> ';
                        echo '<input type="text" name="numero" id="numero" value="' . $numero . '" class="cajaGeneral" size="10" maxlength="6" placeholder="Numero"  /> ';
                       
                        echo '<a href="javascript:;" id="linkVerSerieNum"' . ($codigo != '' ? 'style="display:none"' : '') . '>
                        <p style="display:none">' . $serie_suger . '-'. $numero_suger . '</p><image src="' . base_url() . 'images/flecha.png?=<?=IMG;?>" border="0" alt="Serie y nÃºmero sugerido" title="Serie y nÃºmero sugerido" /></a>';

                    }
                               //var_dump($tipo_codificacion);
                                ?>
                            </td>

<?php

?>								<td>Fecha *</td>
							 <td width="2%" valign="middle" ><input NAME="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy; ?>" size="10" maxlength="10" readonly="readonly" />
                                <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" />
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField     :    "fecha",      // id del campo de texto
                                        ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button         :    "Calendario1"   // el id del botÃ³n que lanzarÃ¡ el calendario
                                    });
                                </script>
                            </td>
							<td width="5%" valign="middle" >
								<label for="P"><img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>" class="imgBoton" /></label>
								<input type="radio" name="referenciar" id="P" value="P" href="javascript:;" class="verDocuRefe" style="display:none;">
								<div id="serieguiaverPre" name="serieguiaverPre" style="background-color: #cc7700; color:fff; padding:5px;display:none" ></div>
											
							</td>
							 <td width="9%" valign="middle">
							 <label for="OP"><img src="<?php echo base_url() ?>public/images/icons/opedido.png?=<?=IMG;?>" class="imgBoton" /></label>
								<input type="radio" name="referenciar" id="OP" value="OP" href="javascript:;" class="verOrpedido" style="display:none;">
								<div id="serieverPedi" name="serieverPedi" style="background-color: #cc7700; color:fff; padding:5px;display:none" ></div>
							 </td>
                           
                        </tr>
                        <tr>
                            <td>Cliente *</td>
                            <td valign="middle">
                                <?php
                                 //   if($tipo_docu!='F' && $cliente!=144){
                                ?>
                               <!-- <input type="hidden" name="cliente" id="cliente" size="5" value="144" />
                                <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;
                                <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="0000000000" onkeypress="return numbersonly(this,event,'.');" />
                                <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40" maxlength="50"  value="<?php echo $nombre_cliente; ?>" />
                                <?php
                                   // }else{
                                ?>-->
                                <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>" />
                                <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."  placeholder="Ruc"/>&nbsp;
                                <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');" />
                                <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40" maxlength="50"  value="<?php echo $nombre_cliente; ?>" placeholder="Nombre cliente"/>
                                <?php
                                    //}
                                ?>
                                <!--<a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
                                <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a>
                            </td>
                            <td valign="middle">Moneda *</td>
                            <td valign="middle">
                                <select name="moneda" id="moneda" class="comboMedio"><?php echo $cboMoneda; ?></select>
                            </td>
                            <td valign="middle">Vendedor</td>
                            <td><!--<select id="vendedor" name="vendedor" class="comboMedio"><?php echo $cboVendedor; ?></select>-->
                            <select  class="cajaGeneral" id="vendedor" name="vendedor">
    <?=$cmbVendedor?>
</select></td>
                        </tr>
                        <tr>
                            <td>Contacto </td>
                            <td><?php echo $cboContacto; ?>
                                <a href="<?php echo base_url(); ?>index.php/maestros/persona/persona_ventana_mostrar/<?php
                                if ($contacto != '') {
                                    $temp = explode('-', $contacto);
                                    echo $temp[0];
                                } else
                                    echo '1';
                                ?>" <?php if ($contacto == '') echo 'style="display:none;"'; ?> id="linkVerPersona"><img height='16' id="" width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='MÃ¡s InformaciÃ³n' border='0' /></a>
                            </td>
                            <td>I.G.V.</td>
                            <td><input name="igv" type="text" class="cajaGeneral cajaSoloLectura" size="2" maxlength="2" id="igv" value="<?php echo $igv; ?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly" /> %</td>
                            <td>Descuento</td>
                            <td><input NAME="descuento" type="text" class="cajaGeneral" size="2" maxlength="2" id="descuento"  value="<?php echo $descuento; ?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_descuento_total();" /> %</td>
                        </tr>
                    </table>
                </div>	
                <div id="frmBusqueda"  <?php echo $hidden; ?>>
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

                                <input name="buscar_producto" type="text" class="cajaGeneral" id="buscar_producto" size="10" title="Ingrese parte del nombre o el nro. de serie del producto, luego presione ENTER." placeholder="Producto"/>&nbsp;

                                <input name="codproducto" type="hidden" class="cajaGeneral" id="codproducto" size="10" maxlength="20" onblur="obtener_producto();" />&nbsp;

                                <input NAME="nombre_producto" type="text" class="cajaGeneral cajaSoloLectura" id="nombre_producto" size="40" readonly="readonly" placeholder="Descripcion producto"/>
                                <!--<a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/B" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
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
                        <table id="tblDetallePresupuesto" class="fuente8" width="100%" border="0">
                            <?php
                            if (count($detalle_presupuesto) > 0) {
                                foreach ($detalle_presupuesto as $indice => $valor) {
                                    $detacodi = $valor->PRESDEP_Codigo;
                                    $flagBS = $valor->flagBS;
                                    $prodproducto = $valor->PROD_Codigo;
                                    $unidad_medida = $valor->UNDMED_Codigo;
                                    $codigo_interno = $valor->PROD_CodigoUsuario;
                                    $prodcantidad = $valor->PRESDEC_Cantidad;
                                    $nombre_producto = $valor->PROD_Nombre;
                                    $nombre_unidad = $valor->UNDMED_Simbolo;
                                    $prodpu = $valor->PRESDEC_Pu;
                                    $prodsubtotal = $valor->PRESDEC_Subtotal;
                                    $proddescuento = $valor->PRESDEC_Descuento;
                                    $prodigv = $valor->PRESDEC_Igv;
                                    $prodtotal = $valor->PRESDEC_Total;
                                    $prodpu_conigv = $valor->PRESDEC_Pu_ConIgv;
                                    $prodsubtotal_conigv = $valor->PRESDEC_Subtotal_ConIgv;
                                    $proddescuento_conigv = $valor->PRESDEC_Descuento_ConIgv;
                                    if (($indice + 1) % 2 == 0) {
                                        $clase = "itemParTabla";
                                    } else {
                                        $clase = "itemImparTabla";
                                    }
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
                
                <style type="text/css">
                    #formato{margin-left: -0.1%;}
                </style>
<div id="frmBusqueda3">
<table width="100%"  align="right" cellpadding=3 cellspacing=0 class="fuente8">
        <tr>
            <td width="80%" rowspan="5" >
            <table width="100%"  cellpadding=3 cellspacing=0 class="fuente8" style="width: 736px;" id="formato" 
           >
        <tr>
            <td colspan="2" height="25"> <b>CONDICIONES DE VENTA </b></td>
                                        <td><b>ESTADO</b></td>
        </tr>
        <tr>
            <td>Lugar de entrega</td>
            <td>
                <input type="text" size="56" maxlength="250" class="cajaGeneral" name="lugar_entrega" id="lugar_entrega" value="<?php if ($codigo != '') echo $lugar_entrega; ?>" />
                <a href="javascript:;"  id="linkVerDirecciones"><image src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0" /></a>
                 <div id="lista_direcciones" class="cuadro_flotante" style="width:305px">
                 <ul> </ul>
                   </div>
             </td>
            <td>
            <select name="estado" id="estado" class="comboPequeno">
            <option <?php if ($estado == '1') echo 'selected="selected"'; ?> value="1">Activo</option>
            <option <?php if ($estado == '0') echo 'selected="selected"'; ?> value="0">Anulado</option>
            </select>
            </td>
        </tr>
        <tr>
           <td width="15%">Forma de Pago</td>
          <td><select name="forma_pago" id="forma_pago" class="comboMedio" style="width:200px"><?php echo $cboFormaPago; ?></select></td>
                                        <td><b>OBSERVACION</b></td>
        </tr>
        <tr>
         <td width="15%">Tiempo de entrega</td>
            <td><textarea name="tiempo_entrega" id="tiempo_entrega" class="cajaTextArea" cols="52" rows="2"><?php echo $tiempo_entrega; ?></textarea></td>
              <td rowspan="5" valign="top">
              <textarea id="observacion" name="observacion" class="cajaTextArea" cols="52" rows="8"><?php echo $observacion; ?></textarea></td>
        </tr>  
        <tr>
                <td width="15%">GarantÃ­a</td>
                 <td><input type="text" size="56" maxlength="100" class="cajaGeneral" name="garantia" id="garantia" value="<?php if ($codigo != '') echo $garantia; else echo '1 AÃ‘O CONTRA DEFECTOS DE FABRICA'; ?>" /></td>
        </tr>
        <tr>
            <td width="15%">Validez de la pte.</td>
            <td><input type="text" size="56" maxlength="100" class="cajaGeneral" name="validez" id="validez" value="<?php if ($codigo != '') echo $validez; else echo (FORMATO_IMPRESION == 4 ? '5' : '30' ) . ' DIAS CALENDARIOS'; ?>" /></td>
        </tr>
        <tr>
                <td width="15%">Modo de impresiÃ³n</td>
                <td><select name="modo_impresion" <?php if ($tipo_docu == 'B') echo 'disabled="disabled"'; ?> id="modo_impresion" class="comboGrande" style="width:307px">
                    <option <?php if ($modo_impresion == '1') echo 'selected="selected"'; ?> value="1">LOS PRECIOS DE LOS PRODUCTOS DEBEN INCLUIR IGV</option>
                       <option <?php if ($modo_impresion == '2') echo 'selected="selected"'; ?> value="2">LOS PRECIOS DE LOS PRODUCTOS NO DEBEN INCLUIR IGV</option>
                                            </select></td>
        </tr>                    
       
        </table>
        </td>
        </tr>
        <tr>
            <td><table   border="0'" align="right" cellpadding=3 cellspacing=0  style="margin-top:20px;">
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
      </table></td>
        </tr>
    </table>

</div>

<div id="botonBusqueda2" style="padding-top:20px;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
         <a href="javascript:;" id="grabarPresupuesto"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
         <a href="javascript:;" id="limpiarPresupuesto"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
         <a href="javascript:;" id="cancelarPresupuesto"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
         <?php echo $oculto ?>
</div>

</div>
        </form>
    </body>