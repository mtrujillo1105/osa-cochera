/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {
	$('#table-precios').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		autoWidth: false,
		paging: false,
		language: "spanish"
	});

	$('#table-productos').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		pageLength: 25,
		ajax: {
			url: base_url + "index.php/almacen/producto/datatable_productos/" + flagBS,
			type: "POST",
			data: { dataString: "" },
			beforeSend: function () {
			},
			error: function () {
			}
		},
		language: "spanish"
	});

	$("#buscar").click(function () {
		search();
	});

	$("#limpiar").click(function () {
		search(false);
	});

	$("#nvo_autocompleteCodigoSunat").autocomplete({
		appendTo: "#modal_producto",
		source: function (request, response) {
			$.ajax({
				url: base_url + "index.php/almacen/producto/autocompleteIdSunat/",
				type: "POST",
				data: {
					term: $("#nvo_autocompleteCodigoSunat").val()
				},
				dataType: "json",
				success: function (data) {
					response($.map(data, function (item) {
						return {
							label: item.descripcion,
							value: item.descripcion,
							idsunat: item.idsunat
						}
					})
					);
				}
			});
		},
		select: function (event, ui) {
			$("#nvo_codigoSunat").val(ui.item.idsunat);
		},
		minLength: 2
	});

	$("#nuevo").click(function () {
		$('#modal_producto').modal('toggle');
		clean();
	});

	$("#nvo_codigo").change(function () {
		checkCode();
	});

	$("#getCode").click(function () {
		if ($(this).val() == "")
			getCode();
	});

	$("#nvo_fabricante, #nvo_marca").change(function () {
		getCode();
	});

	$("#nvo_nombre").change(function () {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: base_url + "index.php/almacen/producto/existsNombre/",
			data: {
				nombre: $(this).val(),
				producto: $("#id").val()
			},
			success: function (data) {
				if (data.match == true) {
					Swal.fire({
						icon: "info",
						title: "Nombre registrado.",
						html: "<b class='color-red'>El nombre ingresado ha sido registrado anteriormente.</b>",
						showConfirmButton: true
					});
				}
			}
		});
	});

	$("#nvo_descripcion").keyup(function () {
		var descripcion = $("#nvo_descripcion").val().length;

		longitud = 800 - descripcion;
		$("#contadorCaracteres").html(longitud);

		if (longitud <= 50) {
			$("#contadorCaracteres").removeClass('text-green');
			$("#contadorCaracteres").addClass('text-red');
		}
		else if (longitud > 50 && longitud < 800) {
			$("#contadorCaracteres").removeClass('text-red');
			$("#contadorCaracteres").addClass('text-green');
		}

	});

	$(".nvo_limpiar").click(function () {
		clean();
	});
});

function search(search = true) {

	if (search == false) {
		$("#form_busqueda")[0].reset();
	}

	codigo = $('#txtCodigo').val();
	producto = $('#txtNombre').val();
	familia = $('#txtFamilia').val();
	marca = $('#txtMarca').val();
	modelo = $('#txtModelo').val();

	$('#table-productos').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		pageLength: 25,
		ajax: {
			url: base_url + "index.php/almacen/producto/datatable_productos/" + flagBS,
			type: "POST",
			data: { txtCodigo: codigo, txtNombre: producto, txtFamilia: familia, txtMarca: marca, txtModelo: modelo },
			error: function () {
			}
		},
		language: spanish
	});
}

function getProducto(id) {
	var url = base_url + "index.php/almacen/producto/getProductoInfo";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: {
			producto: id
		},
		beforeSend: function () {
			clean();
		},
		success: function (data) {
			if (data.match == true) {
				info = data.producto;
				unidad = data.unidades;
				precio = data.precios;

				$("#id").val(info.producto);
				$("#nvo_codigo").val(info.codigo);
				$("#nvo_nombre").val(info.nombre);
				$("#nvo_autocompleteCodigoSunat").val(info.sunatDescripcion);
				$("#nvo_codigoSunat").val(info.sunatCodigo);
				$("#nvo_tipoAfectacion").val(info.afectacion);
				$("#nvo_descripcion").val(info.descripcion);
				$("#nvo_familia").val(info.familia);
				$("#nvo_fabricante").val(info.fabricante);
				$("#nvo_marca").val(info.marca);
				$("#nvo_modelo").val(info.modelo);
				$("#nvo_stockMin").val(info.stockMin);

				$("#nvo_codigo").attr({
					readOnly: true
				});

				$.each(unidad, function (i, v) {
					$("#nvo_unidad").val(v.unidad);
				});

				$.each(precio, function (i, v) {
					$(".precio-" + v.categoria + v.moneda).val(v.precio);
				});


				$("#modal_producto").modal("toggle");
			}
			else {
				Swal.fire({
					icon: "info",
					title: "Información no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function () {
		}
	});
}

function registrar() {
	Swal.fire({
		icon: "question",
		title: "¿Esta seguro de guardar el registro?",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value) {
			let id = $("#id").val();
			let nombre = $("#nvo_nombre").val();
			let validacion = true;
			let codigo_usuario = $("#nvo_codigo").val();
			let familia = $('#nvo_familia').val();
			let marca = $('#nvo_marca option:selected').text();
			let modelo = $('#nvo_modelo').val();
			
			if (nombre == "") {
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un nombre.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nvo_nombre").focus();
				validacion = false;
				return null;
			}

			if (codigo_usuario == "" && codeProductCfg == 2) {
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un código.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nvo_codigo").focus();
				validacion = false;
				return null;
			}

			if (validacion == true) {
				var url = base_url + "index.php/almacen/producto/guardar_registro";
				var info = $("#form_nvo").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function (data) {
						if (data.result == "success") {
							Swal.fire({
								icon: data.result,
								title: data.message,
								showConfirmButton: true,
								timer: 2000
							});
							clean();
							$("#txtCodigo").val(codigo_usuario);
							$("#txtNombre").val(nombre);
							$("#txtFamilia").val(familia);
							if (flagBS == 'B'){
								$("#txtMarca").val(marca);
								$("#txtModelo").val(modelo);
							}
							search();
						}
						else {
							Swal.fire({
								icon: data.result,
								title: data.message,
								html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function () {
						$("#nvo_codigo").focus();
					}
				});
			}
		}
	});
}

function insertar_costo(id, precioc) {
	costo = $("#" + precioc).val();

	if (id != '' && costo != '') {
		url = base_url + "index.php/almacen/producto/nvoCosto";

		$.ajax({
			type: "POST",
			url: url,
			data: { codigo: id, nvoCosto: costo },
			dataType: 'json',
			beforeSend: function () {
				$("#btnCosto" + precioc).hide();
				$("#loading" + precioc).show();
			},
			success: function (data) {
				console.log(data);
				if (data.result == 'success') {

					Swal.fire({
						icon: "success",
						title: "Precio actualizado.",
						showConfirmButton: true,
						timer: 2000
					});

					$("#span" + precioc).html(costo);
					$("#loading" + precioc).hide();
					$("#btnCosto" + precioc).show();
				}
				else {
					Swal.fire({
						icon: "warning",
						title: data.msg,
						showConfirmButton: true,
						timer: 2000
					});

					$("#loading" + precioc).hide();
					$("#btnCosto" + precioc).show();
				}
			},
			error: function (HXR, error) {
				$("#loading" + precioc).hide();
				$("#btnCosto" + precioc).show();
			}
		});
	}
}

function clean() {
	$("#form_nvo")[0].reset();
	$("#id").val("");
	$("#contadorCaracteres").html("800");

	if (codeProductCfg != 1)
		$("#nvo_codigo").removeAttr("readOnly");
}

function barcode(codigo) {
    url = base_url+"index.php/almacen/producto/ver_productobarra/"+codigo;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function getCode() {
	if (codeProductCfg == 1 || codeProductCfg == 3) {
		let code = (codeProductCfg == 1) ? "" : $("#nvo_codigo").val();
		let flagBS = $("#flagBS").val();
		let fab = $("#nvo_fabricante").val();
		let fami = $("#nvo_familia").val();
		let mark = $("#nvo_marca").val();

		$.ajax({
			type: 'POST',
			url: base_url + "index.php/almacen/producto/getNewCode",
			dataType: 'json',
			data: {
				codigo: code,
				tipo: flagBS,
				fabricante: fab,
				familia: fami,
				marca: mark
			},
			success: function (data) {
				if (data.result == 'success') {
					$("#nvo_codigo").val(data.codigo);
					checkCode();
				}
				else {
					Swal.fire({
						icon: "info",
						title: "Información no disponible.",
						html: "<b class='color-red'></b>",
						showConfirmButton: true,
						timer: 4000
					});
				}
			}
		});
	}
}

function checkCode() {
	$.ajax({
		type: "POST",
		dataType: "json",
		url: base_url + "index.php/almacen/producto/existsCode/",
		data: {
			codigo: $("#nvo_codigo").val(),
			producto: $("#id").val()
		},
		success: function (data) {
			if (data.match == true) {
				if (repiteCodigo == 1) {
					Swal.fire({
						icon: "info",
						title: "Código registrado.",
						html: "<b class='color-red'>El código ingresado ha sido registrado anteriormente.</b>",
						showConfirmButton: true
					});
					$("#getCode").removeClass('bg-success, bg-warning').addClass('bg-secondary');
					$("#nvo_codigo").removeClass('is-valid').addClass('is-invalid');
				}
				else {
					$("#getCode").removeClass('bg-success, bg-secondary').addClass('bg-warning');
					$("#nvo_codigo").removeClass('is-invalid').addClass('is-valid');
				}
			}
			else {
				$("#getCode").removeClass('bg-secondary, bg-warning').addClass('bg-success');
				$("#nvo_codigo").removeClass('is-invalid').addClass('is-valid');
			}
		}
	});
}















//*************************************
//******** RECETA
//*************************************

function editar_receta(receta) {
	url = base_url + "index.php/almacen/produccion/editar_receta/" + receta;
	location.href = url;
}

function eliminar_receta(receta) {
	var success = confirm('¿Desea eliminar esta receta?');
	if (success == true) {
		url = base_url + "index.php/almacen/produccion/eliminar_receta/" + receta;
		location.href = url;
	}
	else
		return null;
}

function ver_receta(id, imagen = 0) {
	var url = base_url + "index.php/almacen/produccion/receta_pdf/" + id + "/" + imagen;
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

//*************************************
//******** PRODUCCION
//*************************************

function editar_produccion(id) {
	url = base_url + "index.php/almacen/produccion/editar_produccion/" + id;
	location.href = url;
}

function ver_produccion(id, imagen = 0) {
	var url = base_url + "index.php/almacen/produccion/produccion_pdf/" + id + "/" + imagen;
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

//*************************************
//******** PRODUCCION
//*************************************

function editar_despacho(id) {
	url = base_url + "index.php/almacen/produccion/despacho_editar/" + id;
	location.href = url;
}

function ver_despacho(id, imagen = 0) {
	var url = base_url + "index.php/almacen/produccion/despachoPdf/" + id + "/" + imagen;
	window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

