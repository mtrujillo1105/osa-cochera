<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>

    <script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.min.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.js?=<?=JS;?>"></script>

    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.css?=<?=CSS;?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () {
		/***verificacion de si es editar y esta relacionada con otras guias **/
		<?php
            if(count($listaGuiaremAsociados)>0){  ?>
		    document.getElementById("tempde_producto").readOnly = true;
            $("#addItems").hide(200);
		<?php } ?>
		/***fin de realizar verificacion**/
		
		/**ejecutar mostrar orden de compra vista si existe**/
		<?php if($ordencompra!=0 &&  trim($ordencompra)!="" && $ordencompra!=null){   ?>
		mostrarOdenCompraVista(<?php echo $ordencompra.",".$serieOC.",".$numeroOC.",". $valorOC; ?>);
		<?php } ?>
		/**no mostrar**/
		/**ejecutar mostrar PRESUPUESTO vista si existe**/
		<?php if($presupuesto_codigo!=0 &&  trim($presupuesto_codigo)!="" && $presupuesto_codigo!=null){   ?>
		mostrarPresupuestoVista(<?php echo $presupuesto_codigo.",'".$seriePre."',".$numeroPre.",'". $tipo_oper."'"; ?>);7
		<?php } ?>
		/**no mostrar**/
		
        base_url = $("#base_url").val();
        tipo_oper = $("#tipo_oper").val();
        almacen = $("#cboCompania").val();

        $(" #linkSelecProducto").fancybox({
            'width': 800,
            'height': 500,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe'
        });

        $("a#linkVerProducto").fancybox({
            'width': 800,
            'height': 650,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
            'type': 'iframe'
        });

        $("#linkVerImpresion").fancybox({
            'width': 300,
            'height': 450,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true
        });

        $(".verDocuRefe").fancybox({
            'width': 770,
            'height': 520,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe',
            'onStart': function () {
            	tipoMoneda=$("#moneda").val();
            	almacen=$("#almacen").val();
                if (tipo_oper == 'V') {
                    if ($('#cliente').val() == '') {
                        alert('Debe seleccionar el cliente.');
                        $('#nombre_cliente').focus();
                        return false;
                    } else {
                        if ($(".verDocuRefe::checked").val() == 'G')
                            baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                        else if ($('.verDocuRefe::checked').val() == 'P')
                            baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                        else if ($('.verDocuRefe::checked').val() == 'O')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                        else if ($('.verDocuRefe::checked').val() == 'R')
                            baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';
                     
                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                } else {

                    if ($('#proveedor').val() == '') {
                        alert('Debe seleccionar el proveedor.');
                        $('#nombre_proveedor').focus();
                        return false;
                    } else {
                        if ($('.verDocuRefe::checked').val() == 'G') {
                            baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                        }
                        else if ($('.verDocuRefe::checked').val() == 'P') {
                            if (tipo_oper == 'V')
                                baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                            else
                                baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                        }
                        else if ($('.verDocuRefe::checked').val() == 'O') {
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                        }
                        else if ($('.verDocuRefe::checked').val() == 'R') {
                            baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';
                        }
                        //alert(baseurl);

                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                }
            }
        });

    });

    //AUTOCOMPLETO DE PRODUCTOS
    $(function () {
        $("#descripcionProducto").autocomplete({
                source: function (request, response) {

                    $("#tempde_message").html('');
                    $("#tempde_message").hide();
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/maestros/temporaldetalle/autocomplete_producto/" + $("#flagBS").val() + "/" + $("#compania").val()+"/"+$("#almacen").val(),
                        type: "POST",
                        data: {
                            term: $("#descripcionProducto").val(), TipCli: 0
                        },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                        $("#idProducto").val(ui.item.codigo);
                        $("#descripcionProducto").val(ui.item.value);
                        $("#descripcion_receta").val(ui.item.value);
                        $("#addItems").click();
                        $("#tempde_producto").focus();
                },
                minLength: 1
            });
    });

    $('a').on('click', function(){
      window.last_clicked_time = new Date().getTime();
      window.last_clicked = $(this);
    });

    $(window).bind('beforeunload', function() {
        if ( $("#salir").val() == 0 ){
          var time_now = new Date().getTime();
          var link_clicked = window.last_clicked != undefined;
          var within_click_offset = (time_now - window.last_clicked_time) < 100;
          if (link_clicked && within_click_offset) {
            return 'You clicked a link to '+window.last_clicked[0].href+'!';
          } else {
            return 'Estas abandonando la página!';
          }
        }
    });
    
    function seleccionar_producto(producto, cod_interno, familia, stock, costo, flagGenInd,codigoAlmacenProducto) {
    	/**si el producto tiene almacen : es que no esta inventariado en ese almacen , se le asigna el almacen general de cabecera**/
        if(codigoAlmacenProducto==0){
        	codigoAlmacenProducto=$("#almacen").val();
         }
        /**fin de asignacion**/
    	/**verificamos si se e3ncuentra en la lista**/
    	isEncuentra=verificarProductoDetalle(producto,codigoAlmacenProducto);
        if(!isEncuentra){
	           $("#codproducto").val(cod_interno);
	           $("#producto").val(producto);
	           $("#cantidad").focus();
	           $("#stock").val(stock);
	           $("#costo").val(costo);
	           $("#flagGenInd").val(flagGenInd);
	           $("#almacenProducto").val(codigoAlmacenProducto);
	           listar_unidad_medida_producto(producto);
        }else{
        	$("#buscar_producto").val("");
            $("#producto").val("");
            $("#codproducto").val("");
            $("#costo").val("");
            $("#stock").val("");
            $("#flagGenInd").val("");
            $("#nombre_producto").val("");
            $("#almacenProducto").val("");
        	$("#buscar_producto").val("");
        	$("#buscar_producto").focus();
        	alert("El producto ya se encuentra ingresado en la lista de detalles.");
      }
    }

    function seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
        agregar_fila(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo);
    }

    function seleccionarOdenCompra(oCompra, serie, numero, valor){
    	mostrarOdenCompraVista(oCompra,serie, numero, valor);
        //obtener_detalle_ocompra(oCompra);
        obtener_comprobantes_temproductos(oCompra,'ocompras');
        /**quitamos lista de guiarem **/
        listadoGuiaremEstadoDeseleccionado();
        verificarOcultarListadoGuiaremAsociado();
    }
        

	function mostrarOdenCompraVista(oCompra,serie, numero, valor){
		if(valor == 1){
            serienumero = "Numero de Orden Compra. :" + serie + " - " + numero;
        }else{
            serienumero = "Numero de Orden Venta. :" + serie + " - " + numero;
        }
        $("#serieguiaverOC").html(serienumero);
        $("#serieguiaverOC").show(200);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverRecu").hide(200);
        $('#ordencompra').val(oCompra);
        codigoPresupuesto=$("#presupuesto_codigo").val();
		if(codigoPresupuesto!="" && codigoPresupuesto!=0){
				modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
		}
		$("#presupuesto_codigo").val("");
	}

    function seleccionar_guiarem(guia, serieguia, numeroguia) {
        //realizado=agregar_todo(guia);
        realizado = verificar_agregar(guia);
        if(realizado!=false){
            obtener_comprobantes_temproductos(guia,'guiarem');
	           $("#serieguiaverPre").hide(200);
	           $("#serieguiaverOC").hide(200);
	           $("#serieguiaverRecu").hide(200);
	           $('#ordencompra').val('');
		}
		codigoPresupuesto=$("#presupuesto_codigo").val();
		if(codigoPresupuesto!="" && codigoPresupuesto!=0){
				modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
		}
		$("#presupuesto_codigo").val("");
    }
 
    function seleccionar_presupuesto(guia, serieguia, numeroguia) {
        isRealizado=modificarTipoSeleccionPrersupuesto(guia,1);
		if(isRealizado){
        	tipo_oper = $("#tipo_oper").val();
        	//agregar_todopresupuesto(guia, tipo_oper);
            obtener_comprobantes_temproductos(guia,'presupuesto');
        	mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper);
        	/**quitamos lista de guiarem **/
        	listadoGuiaremEstadoDeseleccionado();
        	verificarOcultarListadoGuiaremAsociado();
		}
        
    }

	function mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper){
		if(tipo_oper=="V")
			serienumero = "Numero de PRESUPUESTO :" + serieguia + " - " + numeroguia;
		else
			serienumero = "Numero de COTIZACIÓN :" + serieguia + " - " + numeroguia;
			
        $("#serieguiaverPre").html(serienumero);
        $("#serieguiaverPre").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#serieguiaverRecu").hide(200);
        $("#docurefe_codigo").val('');
        $("#dRef").val('');
        $('#ordencompra').val('');
        $("#numero_ref").val('');
        $("#presupuesto_codigo").val(guia);
	}
		
    function seleccionar_comprobante_recu(guia, serieguia, numeroguia) {
        //agregar_todo_recu(guia);
        obtener_comprobantes_temproductos(guia,'comprobantes');
        serienumero = "NÂ° de Comprobante: <br>" + serieguia + " - " + numeroguia;
        $("#serieguiaverRecu").html('<span style="font-size:15px" >Doc. Recurrente: <br>' + serienumero + '</span>');
        $("#serieguiaverRecu").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverRecuFlecha").show(400);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#numero_ref").val('');
        $("#dRef").val('');
        $('#ordencompra').val('');
        $("#docurefe_codigo").val('');
        codigoPresupuesto=$("#presupuesto_codigo").val();
		if(codigoPresupuesto!="" && codigoPresupuesto!=0){
			modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
		}
		$("#presupuesto_codigo").val("");
		
        /**quitamos lista de guiarem **/
        listadoGuiaremEstadoDeseleccionado();
        verificarOcultarListadoGuiaremAsociado();
    }

	function cambiarAlmacenProductoCodigo(almacen){
		posicionSeleccionado=$("#posicionSeleccionadaSerie").val();
		if(posicionSeleccionado!=null && posicionSeleccionado!=''){
		a="almacenProducto["+posicionSeleccionado+"]";
		document.getElementById(a).value=almacen;

		}
	}

	/**seleccionamos un almacen para el producto agregaod po o.vc cotizacioon, recurrentes**/
	function mostrarPopUpSeleccionarAlmacen(posicionSeleccionado, isView){
        var isView = isView == undefined ? true : isView;
		var a="almacenProducto["+posicionSeleccionado+"]";
		var b="prodcodigo["+posicionSeleccionado+"]";
		$("#posicionSeleccionadaSerie").val(posicionSeleccionado);
		var almacenProducto=document.getElementById(a).value;
		var codigoProducto=document.getElementById(b).value;
		var url="<?php echo base_url(); ?>index.php/almacen/producto/buscarAlmacenProducto/"+codigoProducto;

		var n = document.getElementById('idTblAlmacen').rows.length;
		if(n!=null && n!='' && n>1){
			for(var x=1;x<n;x++){
				document.getElementById("idTblAlmacen").deleteRow(1);
			}
		}
		
		$.ajax({
		        url: url,
		        dataType: 'json',
		        //async: false,
		        success: function (data) {
		        	$.each(data, function (i, item) {
						var codigoAlmacen=item.codigo;
						var nombreAlmacen=item.nombreAlmacen;
						var stock=item.stock;
						var j=i+1;
						var fila="<tr id='idTr_"+j+"' >";
						fila+="<td>";
						fila+="<input type='radio' name='almacenListado' id='idRdAlmacen"+j+"' value='"+codigoAlmacen+"'>";	
						fila+="</td>";
						fila+="<td>";
						fila+="<label for='idRdAlmacen"+j+"' >"+nombreAlmacen+"</label>";	
						fila+="</td>";
						fila+="<td>";
						fila+="<label>"+stock+"</label>";	
						fila+="</td>";
						fila+="</tr>";
						$("#idTblAlmacen").append(fila);

                        if($("#almacen").val() == codigoAlmacen) document.getElementById("prodstock["+posicionSeleccionado+"]").value = stock;
		        	});
		        	if(isView) $("#dialogoSeleccionarALmacenProducto").dialog("open");
		        }
		});
	}

	function grabarSeleccionarAlmacen(){
		almacen=$('input:radio[name=almacenListado]:checked').val();
		if(almacen!=null && almacen!=""){
			cambiarAlmacenProductoCodigo(almacen);
			$("#dialogoSeleccionarALmacenProducto").dialog("close");
		}else{
			alert("Debe de seleccionar un almacen para el producto.");
		}
	}
</script>

</head>

<body>

<input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>

<!-- Inicio -->
    <input value='<?php echo $compania; ?>' name="compania" type="hidden" id="compania"/>

    <div id="VentanaTransparente" style="display:none;">
        <div class="overlay_absolute"></div>
        <div id="cargador" style="z-index:2000">
            <table width="100%" height="100%" border="0" class="fuente8">
                <tr valign="middle">
                    <td> Por Favor Espere</td>
                    <td>
    					<img src="<?php echo base_url(); ?>public/images/icons/cargando.gif" border="0" title="CARGANDO"/>
    					<a href="#" id="hider2"></a>
    				</td>
                </tr>
            </table>
        </div>
    </div>
<!-- Fin -->

<form id="<?php echo $formulario; ?>" name="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">
    <div id="zonaContenido" align="center">
        <?php echo validation_errors("<div class='error'>", '</div>'); ?>
        <div id="tituloForm" class="header" style="height: 20px">
            <?php echo $titulo;?>
            <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio">
                <option value="P">RECETA</option>
            </select>
        </div>

		<div id="idDivGuiaRelacion" style="<?php echo (count($listaGuiaremAsociados)>0)?'':'display:none'; ?>">
    		<div id="dialogSeriesAsociadas" title="Series Ingresadas">
                <div id="mostrarDetallesSeriesAsociadas">
                    <div id="detallesSeriesAsociadas"></div>
                </div>
    		</div>
    		
    		<!-- dialogo para mostrarse que sleccionar el almacen de un producto -->
    		
    		<div id="dialogoSeleccionarALmacenProducto" title="Seleccionar Almacen">
                <div id="mostrarDetallesSeleecionarALmacen">	
    		  	 	<table id="idTblAlmacen" >
    	            	<tr id="idTr_0">
    		            	<td></td>
    		            	<td width="200px" >Descripci&oacute;n</td>
    		            	<td width="50px">Stock</td>            	
    	            	</tr>
    		  		</table>
                </div>
    		</div>
    		<!-- fin de dialogo -->
        		<div id="tituloForm" class="header" style="height: 30px">
                    <h3>GUIAS RELACIONADAS</h3>
        		</div>
    			<table class="fuente8" id="idTableGuiaRelacion">
        			<tr id="idTrDetalleRelacion_0" >
        				<td></td>
        				<td>ITEM</td>
        				<td>SERIE</td>
        				<td>NUMERO</td>
        				<td>BACKGROUND</td>
        			</tr> <?php
                    if(count($listaGuiaremAsociados)>0){ 
    					foreach ($listaGuiaremAsociados as $indice=>$valorGuiarem){
    						$codigoGuiarem=$valorGuiarem->codigoGuiarem;
    						$serieGuiarem=$valorGuiarem->serie;
    						$numeroGuiarem=$valorGuiarem->numero;
    						$j=$indice+1;
    						$colorGuiar[$codigoGuiarem]="#".dechex(rand(0,10000000)); ?>
    			
                            <tr id="idTrDetalleRelacion_<?php echo $j; ?>"> 
                                <td> 
                                    <a href="javascript:void(0);" onclick="deseleccionarGuiaremision(<?php echo $codigoGuiarem; ?>,<?php echo $j; ?>)" title="Deseleccionar Guia de remision"> x</a> 
                                </td> 
                                <td><?php echo $j; ?></td> 
                                <td><?php echo $serieGuiarem; ?></td> 
                                <td><?php echo $numeroGuiarem; ?></td> 
                                <td>
                                    <div style="width:10px;height:10px;background-color:<?php echo $colorGuiar[$codigoGuiarem] ?>; border:1px solid black"></div> 
                                    <input type="hidden" id="codigoGuiaremAsociada[<?php echo $j; ?>]"  name="codigoGuiaremAsociada[<?php echo $j; ?>]" value="<?php echo $codigoGuiarem; ?>" /> 
                                    <input type="hidden" id="accionAsociacionGuiarem[<?php echo $j; ?>]"  name="accionAsociacionGuiarem[<?php echo $j; ?>]" value="2" />
                                    <input type="hidden" id="proveedorRelacionGuiarem[<?php echo $j; ?>]"  name="proveedorRelacionGuiarem[<?php echo $j; ?>]" value="<?php echo $proveedor; ?>" />
                                </td>
                            </tr> <?php
                        }
                    } ?>
    			</table>
		</div>
        
        <div id="frmBusqueda">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
                <tr>
                    <td width="20%">Nombre de la receta</td>
                    <td valign="middle">
                        <input type="hidden" name="receta" id="receta" size="5" value="<?=$receta;?>"/>
                        <input placeholder="Nombre de la receta" name="descripcion_receta" type="text" class="cajaGrande cajaSoloLectura" id="descripcion_receta" value="<?=$descripcionReceta;?>" readonly maxlength="50" title="Ingrese el nombre de la receta"/>
                    </td>
                </tr>
                <tr>
                    <td>Articulo *</td>
                    <td valign="middle">
                        <input type="hidden" name="idProducto" id="idProducto" value="<?=$idProducto;?>"/>
                        <input placeholder="Nombre del producto" type="text" name="descripcionProducto" id="descripcionProducto" class="cajaGrande" size="37" maxlength="50" value="<?=$descripcionProducto;?>"/>
                    </td>
                </tr>
                <tr style="display: none">
                    <td>Almacen</td>
                    <td valign="middle">
                        <?=$cboAlmacen;?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="frmBusqueda"  <?php echo $hidden; ?> class="box-add-product" style="text-align: right;" >
            <a href="#" id="addItems" name="addItems" style="color:#ffffff;" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="limpiar_campos_modal(); ">Agregar Items</a></td>
        </div>
       <!-- TABLA DETALLE DE TEMPORAL -->
<?php $this->load->view('maestros/temporal_subdetalles_second'); ?>
       <!-- FIN DE TABLA TEMPORAL DETALLE -->

        <br/>

        <div id="botonBusqueda2" style="padding:10px; background: #e7ebef;">
            <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif" style="visibility: hidden"/>
            <?php if($estado != 0): ?>
            <a href="javascript:;" id="imgGuardarReceta"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg" width="85" height="22" class="imgBoton"></a>
            <?php endif; ?>
            <a href="javascript:;" id="limpiarReceta"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg" width="69" height="22" class="imgBoton"></a>
            <a href="javascript:;" id="cancelarReceta"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg" width="85" height="22" class="imgBoton"></a>
            <input type="hidden" name="salir" id="salir" value="0"/>
            <?php echo $oculto ?>
        </div>

    </div>
<style type="text/css">
    #popup {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 1001;
    }

    .content-popup {
        margin:0px auto;
        margin-top:150px;
        position:relative;
        padding:10px;
        width:300px;
        min-height:150px;
        border-radius:4px;
        background-color:#FFFFFF;
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
    #btnInventario{
        size: 20px;
        width: 200px;
        height: 50px;
        border-radius: 33px 33px 33px 33px;
        -moz-border-radius: 33px 33px 33px 33px;
        -webkit-border-radius: 33px 33px 33px 33px;
        border: 0px solid #000000;
        background-color:rgba(199, 255, 206, 1);
    }
</style>

</form>


<a id="linkVerImpresion" href="#ventana"></a>
<div id="ventana" style="display: none; width: 350px">
    <div id="imprimir" style="padding:20px; text-align: center">
        <span style="font-weight: bold;">
          <?php if ($tipo_docu == 'P') echo 'RECETA'; else echo 'RECETA'; ?>
      </span>
      <div style="padding-left: 100px" >
          <input type="text" name="ser_imp" id="ser_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="fsd" class="cajaMinima" maxlength="3">
          - <input type="text" name="num_imp" id="num_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="lknmlk" class="cajaMedia" maxlength="10">
      </div>  <br/>
      <a href="javascript:;" id="imprimirPedido"><img src="<?php echo base_url(); ?>public/images/icons/impresora.jpg" class="imgBoton" alt="Imprimir"></a>
      <br/> <br/>
      <a href="javascript:;" id="cancelarImprimirPedido"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg" width="85" height="22" class="imgBoton"></a>
  </div>
</div>
<?php $this->load->view('maestros/temporal_detalles_second'); ?>
</body>

</html>