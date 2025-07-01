/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */

$("#apertura_caja_nombre").change(function(){    
    var caja = $("#apertura_caja_nombre").val();
    $("#apertura_caja_responsable").val("");
    $("#apertura_caja_responsable_id").val("");
    $("#apertura_caja_clave").val("");
    
    if(caja != ""){
        $.ajax({
            type: "POST",
            url: base_url + "index.php/tesoreria/caja/getCaja/",    
            dataType: "json",
            data: {
                caja: caja}
            ,
            success: function (data) {
                if (data.match == true) {
                    info = data.info;
                    $("#apertura_caja_responsable").val(info.nombre_responsable);
                    $("#apertura_caja_responsable_id").val(info.cajero_usuario);
                    $("#apertura_caja_clave").focus();
                }
                else{
                    Swal.fire({
                        icon: "info",
                        title: "Esta caja no tiene responsable, seleccione otra.",
                        html: "<b class='color-red'></b>",
                        showConfirmButton: true,
                        timer: 4000
                    });
                }
            }
        });       
    }
 
});

$("#btn_abrir_caja").click(function(){
    
    //Deshabilito el botón registrar parqueo
    $("#btn_abrir_caja").attr("disabled",true); 
    
    info = $("#frm_abrir_caja").serialize();

    $.ajax({
            type: 'post',
            dataType: 'json',
            url: base_url + "index.php/tesoreria/caja/abrir_caja",
            data: info,
            success: function (data) {
                    if (data.result == 'success') {
                        Swal.fire({
                                icon: "success",
                                title: "Cambio completo, la vista se actualizara en breve.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 4000
                        });
                        window.location.reload();
                    }
                    else {
                        $("#apertura_caja_clave").val("");
                        $("#apertura_caja_clave").focus();
                        Swal.fire({
                                icon: data.result,
                                title: 'Cambio no completado.',
                                html: "<b class='color-red'>" + data.message + "</b>",
                                showConfirmButton: true
                        });
                        
                        //Habilito el botón registrar parqueo
                        $("#btn_abrir_caja").removeAttr("disabled");   
                        
                    }
            }
    });
});

/**  -> Begin **/
function menu_activo(modulo = "", ruta = "") {
	$("#menu_" + modulo).addClass('menu-open');
	$("#menubg_" + modulo).addClass('active');
	$("#submenu_" + ruta).addClass('active');
}
/**  -> End **/

/** Dev:  -> Begin **/
function obtener_demora() {
	$.ajax({
		url: base_url + "index.php/basedatos/basedatos/obtener_estado_pago/",
		type: "POST",
		data: {
			term: ""
		},
		dataType: "json",
		success: function (data) {

			if (data.pago == 0) {
				Swal.fire({
					title: 'Alerta de corte de servicio, por favor contacte con el área comercial: tlf 916296548',
					icon: 'warning',
					showClass: {
						popup: 'animated fadeInDown faster'
					},
					hideClass: {
						popup: 'animated fadeOutUp faster'
					}
				})
			} if (data.pago == 2) {

			}
		}
	});
}
/** Dev:  -> End **/

/**  -> Begin **/
function change_session() {
	var compania = $("#sessionCompany").val();
	info = $("#frmChangeSession").serialize();
	$.ajax({
		type: 'post',
		dataType: 'json',
		url: base_url + "index.php/maestros/configuracion/cambiar_sesion",
		data: info,
		success: function (data) {
			if (data.result == 'success') {
				Swal.fire({
					icon: "success",
					title: "Cambio completo, la vista se actualizara en breve.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
				window.location.reload();
			}
			else {
				Swal.fire({
					icon: data.result,
					title: 'Cambio no completado.',
					html: "<b class='color-red'>" + data.message + "</b>",
					showConfirmButton: true
				});
			}
		}
	});
}
/**  -> End **/



var typingTimeout;
function startTypingTimer(input_field) {
	if (typingTimeout != undefined)
		clearTimeout(typingTimeout);
	typingTimeout = setTimeout(function () {
		eval(input_field.attr("onfinishinput"));
	}, 500);
}

function numbersonly(myfield, e, dec) {
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27))
		return true;
	if (dec && (keychar == "." || keychar == ",")) {
		var temp = "" + myfield.value;
		if (temp.indexOf(keychar) > -1)
			return false;
	}
	else if ((("0123456789").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function textoonly(myfield, e) {
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27)) {
		return true;
	}
	else if (("1234567890".indexOf(keychar) == -1)) {
		return true;
	}
	else {
		return false;
	}
}

function ventana_producto_serie0(indice, compania) {
	producto = "prodcodigo[" + indice + "]"
	prod = document.getElementById(producto).value;
	almacen_id = document.getElementById("almacen_id").value;
	if (almacen_id == '')
		almacen_id = '0';
	if (!compania)
		compania = document.getElementById("compania").value;
	url = base_url + "index.php/almacen/producto/ventana_producto_serie0/" + prod + "/" + almacen_id + "/" + compania;
	$("a#linkSerie").attr('href', url).click();
}

/**gcbq ventana producto serie compra venta**/
function ventana_producto_serie(indice) {
	producto = "prodcodigo[" + indice + "]"
	cantidad = "prodcantidad[" + indice + "]";
	guias = "codigoguia";
	prod = document.getElementById(producto).value;
	cant = document.getElementById(cantidad).value;
	tipo = 1;
	tipoOperacion = document.getElementById("tipo_oper").value;
	almacenProducto = "almacenProducto[" + indice + "]";
	almacen = document.getElementById(almacenProducto).value;
	if (tipoOperacion != null && tipoOperacion == 'V') {
		if (almacen == '') {
			alert('Seleccione primero un almacen');
			document.getElementById("almacen").focus();
			return false;
		}
	}

	/**verificamos si el almacen es null para series que seleccione el almacen de donde va sacarm de o.compra , presupuesto , recurrentes ***/

	isSeleccionarAlmacen = "isSeleccionarAlmacen[" + indice + "]";
	if (document.getElementById(isSeleccionarAlmacen)) {
		isSeleccionarAlmacen = document.getElementById(isSeleccionarAlmacen).value;
		if (isSeleccionarAlmacen != null && isSeleccionarAlmacen.trim() != '') {
			if (isSeleccionarAlmacen == 1) {
				isSeleccionarAlmacen = 1;
			}
		} else {
			isSeleccionarAlmacen = 0;
		}
	} else {
		isSeleccionarAlmacen = 0;
	}
	/**guardamos la posicion selecionada para el proceso de añadir almacen a esta posicion**/
	$("#posicionSeleccionadaSerie").val(indice);

	url = base_url + "index.php/almacen/producto/ventana_producto_serie/" + prod + "/" + cant + "/" + tipo + "/" + tipoOperacion + "/" + almacen + "/" + isSeleccionarAlmacen;
	var win = window.open(url, "_blank", "width=600,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'");
}

/**gcbq debe mostrar listado de series realizadas por cada documento producto y almacen**/
function ventana_producto_serieMostrar(docu, codigo, producto, almacen) {
	/**verificamos si esta inicializado para destroy los campos**/
	if ($('.ui-table').length > 0) {
		$('#detallesSeriesAsociadas').columns('destroy');
	}
	/**OBTENER DATOS JSON DE SERIES***/
	url = base_url + "index.php/almacen/producto/series_ingresadas_comprobante_producto_almacen_json/" + docu + "/" + codigo + "/" + producto + "/" + almacen,
		$.getJSON(url, function (data) {
			example1 = $('#detallesSeriesAsociadas').columns({
				data: data,
				schema: [
					{ "header": "Nro.", "key": "i" },
					{ "header": "Número Serie", "key": "numero" },
					{ "header": "Fecha de Registro", "key": "fecha" }
				],
				evenRowClass: 'even-rows'
			});
			$("#dialogSeriesAsociadas").dialog("open");
		});
}

function ventana_producto_serie_1() {
	prod = document.getElementById("producto").value;
	cant = document.getElementById("cantidad").value;
	tipo = 0;
	if (prod == '')
		return false;
	if (cant == '' || parseInt(cant) <= 0)
		return false;

	tipoOperacion = document.getElementById("tipo_oper").value;
	almacen = document.getElementById("almacenProducto").value;
	if (tipoOperacion != null && tipoOperacion == 'V') {
		almacen = document.getElementById("almacenProducto").value;
		if (almacen == '') {
			alert('Seleccione primero un almacen');
			document.getElementById("almacenProducto").focus();
			return false;
		}
	}
	/**tanto para VENTAS Y COMPRAS**/
	url = base_url + "index.php/almacen/producto/ventana_producto_serie/" + prod + "/" + cant + "/" + tipo + "/" + tipoOperacion + "/" + almacen;
	window.open(url, "_blank", "width=600,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'");

	return true;
}

function ventana_producto_serie_1_1(prod, cant) {
	if (prod == '')
		return false;
	if (cant == '' || parseInt(cant) <= 0)
		return false;

	url = base_url + "index.php/almacen/producto/ventana_producto_serie/" + prod + "/" + cant;
	window.open(url, "_blank", "width=400,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'");
	return true;
}

function ventana_producto_serie2(indice) {
	producto = "prodcodigo[" + indice + "]"
	cantidad = "prodcantidad[" + indice + "]";
	almacen = document.getElementById("almacen").value;
	guias = "codigoguia";
	guia = document.getElementById(guias).value;

	if (almacen == '') {
		alert('Seleccione primero un almacen');
		document.getElementById("almacen").focus();
		return false;
	}

	prod = document.getElementById(producto).value;
	cant = document.getElementById(cantidad).value;

	if (guia == "") {
		url = base_url + "index.php/almacen/producto/ventana_producto_serie2/" + prod + "/" + cant + "/" + almacen + "/" + guia;
	} else {
		url = base_url + "index.php/almacen/producto/ventana_producto_series2/" + prod + "/" + cant + "/" + almacen + "/" + guia;
	}

	window.open(url, "_blank", "width=700,height=500,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'");
	return true;
}



function ventana_producto_serie2t(indice) {
	producto = "prodcodigo[" + indice + "]"
	cantidad = "prodcantidad[" + indice + "]";
	almacen = document.getElementById("almacen").value;
	guiain = document.getElementById("codigoguiain").value;
	guiasa = document.getElementById("codigoguiasa").value;
	tipo = document.getElementById("tipoguia").value;

	if (almacen == '') {
		alert('Seleccione primero un almacen');
		document.getElementById("almacen").focus();
		return false;
	}

	prod = document.getElementById(producto).value;
	cant = document.getElementById(cantidad).value;

	if (guiain == "") {
		url = base_url + "index.php/almacen/producto/ventana_producto_serie2/" + prod + "/" + cant + "/" + almacen + "/" + guiasa + "/" + guiain;

	} else {
		url = base_url + "index.php/almacen/producto/ventana_producto_series2/" + prod + "/" + cant + "/" + almacen + "/" + guiasa + "/" + guiain;
	}

	window.open(url, "_blank", "width=700,height=500,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'");
	return true;
}

function money_format(amount) {
	return parseFloat(parseFloat(amount).format(false));
}

function limpiar_combobox(combobox) {
	select = $("#" + combobox);
	options = select.find("option");
	$.each(options, function (index, el) {
		$(el).remove();
	});
	opt = document.createElement("option");
	texto = document.createTextNode(":: Seleccione ::");
	opt.append(texto);
	opt.value = "";
	select.append(opt);
}

/**gcbq debe mostrar listado se series realizadas por cada almacen**/
function mostrarSeriesProducto(codigoProducto, codigoAlmacen) {
	/**verificamos si esta inicializado para destroy los campos**/
	if ($('.ui-table').length > 0) {
		$('#detallesSeries').columns('destroy');
	}
	/**OBTENER DATOS JSON DE SERIES***/
	url = base_url + "index.php/almacen/producto/series_ingresadas_almacen_json/" + codigoProducto + "/" + codigoAlmacen,
		$.getJSON(url, function (data) {
			example1 = $('#detallesSeries').columns({
				data: data,
				schema: [
					{ "header": "Nro.", "key": "i" },
					{ "header": "Número Serie", "key": "numero" },
					{ "header": "Fecha de Registro", "key": "fecha" },
					{ "header": "Almacen", "key": "almacen" }
				],
				evenRowClass: 'even-rows'
			});
			$("#dialogSeries").dialog("open");
		});
}

function comprobante_ver_pdf_conmenbrete(comprobante, documento, tipoImpresion, imagen = 0) {
	if (documento == 9 || documento == 8 || documento == 14) {
		if (documento == 8)
			documento = "F";

		if (documento == 9)
			documento = "B";

		if (documento == 14)
			documento = "C";
		var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf/" + comprobante + "/" + tipoImpresion;
	}
	else
		if (documento == 10) {
			documento = "GR";

			var url = base_url + "index.php/almacen/guiarem/guiarem_ver_pdf/" + comprobante + "/" + tipoImpresion + "/" + imagen;
		} else {
			tipoImpresion = "F";
			var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumento/" + comprobante + "/" + documento + "/" + imagen + "/" + tipoImpresion + "/";
		}
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function comprobante_ver_pdf_compromiso(comprobante, documento, imagen, tipo) {
	var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumentoCompromiso/" + comprobante + "/" + documento + "/" + imagen + "/" + tipo + "/";
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function comprobante_ver_pdf_compromiso_guia(comprobante, documento, imagen, tipo) {
	var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumentoCompromisoGuia/" + comprobante + "/" + documento + "/" + imagen + "/" + tipo + "/";
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

/**MODIFICAMOS EL TIPO DE SLECCION DE UN PRESUPUESTO***/
function modificarTipoSeleccionPrersupuesto(codigoPresupuesto, flagSeleccion) {
	url = base_url + "index.php/ventas/presupuesto/modificarTipoSeleccion/" + codigoPresupuesto + "/" + flagSeleccion;
	$isRealizado = true;
	$.ajax({
		url: url,
		async: false,
		success: function (data) {
			switch (data) {
				case 0:
					$isRealizado = false;
					alert("ya se encuentra seleccionado");
					break;
				case 1:
					break;
				case 0:
					$isRealizado = false;
					alert("error consulte con el administrador.");
					break;
			}
		}
	});
	return $isRealizado;
}

/* #########################################
	 ##### Botones
/* ######################################### */
/*
	$(document).ready(function(){
	});
*/

function btnHijos(element, id, tipo = '', pos = '') {
	if ($("#" + element + " .btn-hijo").is(':visible')) {
		$(".btn-hijo").hide('fast');
	}
	else {
		$(".btn-hijo").hide('fast');
		$("#" + element + " .btn-hijo").html("")

		btnHTML = "";

		// Cotizaciones (OC)
		if (tipo == 'oc') {
			// Editar OC
			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-default" onclick="editar_ocompra(' + id + ');">' +
				'		<img src="' + base_url + 'images/modificar.png" class="image-size-1l" title="">' +
				'	</button>' +
				'</li>';

			// PDF OC
			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-default" href="' + base_url + 'index.php/compras/ocompra/ocompra_ver_pdf_conmenbrete/' + id + '/0" data-fancybox data-type="iframe">' +
				'		<img src="' + base_url + 'images/icono_imprimir.png" class="image-size-1l" title="">' +
				'	</button>' +
				'</li>';

			// PDF OC
			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-default" href="' + base_url + 'index.php/compras/ocompra/ocompra_ver_pdf_conmenbrete/' + id + '/1" data-fancybox data-type="iframe">' +
				'		<img src="' + base_url + 'images/pdf.png" class="image-size-1l" title="">' +
				'	</button>' +
				'</li>';

			// Descargar Excel
			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-default" onclick="ocompra_download_excel(' + id + ');">' +
				'		<img src="' + base_url + 'images/excel.png" class="image-size-1l" title="">' +
				'	</button>' +
				'</li>';

			// Modal correo
			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-default" onclick="open_mail(' + id + ');" class="enviarcorreo">' +
				'		<img src="' + base_url + 'images/send.png" class="image-size-1l" title="Enviar Cotizacion via correo">' +
				'	</button>' +
				'</li>';

			// Canjes
			btnHTML += '<ul class="btn-hijoInfo">';

			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-success" onclick="canjeToGuia(' + id + ',' + pos + ')">' +
				'		Generar Guia de Remisión' +
				'	</button>' +
				'</li>';

			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-success" onclick="canjeToComprobante(' + id + ',' + pos + ',\'F\')">' +
				'		Generar Factura' +
				'	</button>' +
				'</li>';

			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-success" onclick="canjeToComprobante(' + id + ',' + pos + ',\'B\')">' +
				'		Generar Boleta' +
				'	</button>' +
				'</li>';

			btnHTML += '<li>' +
				'	<button type="button" class="btn2 btn-success" onclick="canjeToComprobante(' + id + ',' + pos + ',\'N\')">' +
				'		Generar Comprobante' +
				'	</button>' +
				'</li>';

			btnHTML += '</ul>';
		}

		// Facturas / boletas / comprobantes
		if (tipo == 'comprobante') {

		}



		if (btnHTML != "") {
			$("#" + element + " .btn-hijo").append(btnHTML);
			$("#" + element + " .btn-hijo").show('fast');
		}
		else {
			Swal.fire({
				icon: "info",
				title: "No definido",
				html: "<b class='color-red'>No definido en el controlador.</b>",
				showConfirmButton: true,
				timer: 4000
			});
		}
	}
}