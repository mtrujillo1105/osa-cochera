/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {
	$('#table-menu').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax: {
			url: base_url + 'index.php/basedatos/menu/datatable_menu/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function () {
			},
			error: function () {
			},
			complete: function () {
			}
		},
		language: spanish,
		columnDefs: [{ "className": "text-center", "targets": 3 }],
		order: [[6, "asc"]]
	});

	$("#buscar").click(function () {
		search();
	});

	$("#limpiar").click(function () {
		search(false);
	});

	$('#form_busqueda').keypress(function (e) {
		if (e.which == 13) {
			return false;
		}
	});

	$('#search_menu, #search_modulo').keyup(function (e) {
		if (e.which == 13) {
			if ($(this).val() != '')
				search();
		}
	});
});

function search(search = true) {
	if (search == true) {
		search_menu = $("#search_menu").val();
		search_modulo = $("#search_modulo").val();
	}
	else {
		$("#search_menu").val("");
		$("#search_modulo").val("");
		search_menu = "";
		search_modulo = "";
	}

	$('#table-menu').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax: {
			url: base_url + 'index.php/basedatos/menu/datatable_menu/',
			type: "POST",
			data: {
				menu: search_menu,
				modulo: search_modulo
			},
			beforeSend: function () {
			},
			error: function () {
			},
			complete: function () {
			}
		},
		language: spanish,
		pageLength: 50,
		columnDefs: [{ "className": "text-center", "targets": 3 }],
		order: [[6, "asc"]]
	});
}

function editar(id) {
	var url = base_url + "index.php/basedatos/menu/getMenu";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: {
			menu: id
		},
		beforeSend: function () {
			clean();
		},
		success: function (data) {
			if (data.match == true) {
				info = data.info;

				$("#menu").val(info.menu);
				$("#modulo_padre").val(info.padre);
				$("#modulo_titulo").val(info.titulo);
				$("#modulo_url").val(info.url);
				$("#modulo_access").val(info.access);
				$("#modulo_order").val(info.order);
				$("#modulo_icono").val(info.icon);

				$("#add_menu").modal("toggle");
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

function registrar_menu() {
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
			let menu = $("#menu").val();
			let titulo = $("#modulo_titulo").val();
			let url = $("#modulo_url").val();
			validacion = true;

			if (titulo == "") {
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un titulo.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#modulo_titulo").focus();
				validacion = false;
				return null;
			}

			if (url == "") {
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe definir la carpeta y la ruta.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#modulo_url").focus();
				validacion = false;
				return null;
			}

			if (validacion == true) {
				let info = $("#formMenu").serialize();
				$.ajax({
					type: 'POST',
					url: base_url + "index.php/basedatos/menu/guardar_registro",
					dataType: 'json',
					data: info,
					success: function (data) {
						if (data.result == "success") {
							if (menu == "")
								titulo = "¡Registro exitoso!";
							else
								titulo = "¡Actualización exitosa!";

							Swal.fire({
								icon: "success",
								title: titulo,
								showConfirmButton: true,
								timer: 2000
							});

							clean();
							search();
						}
						else {
							Swal.fire({
								icon: "error",
								title: "Sin cambios.",
								html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function () {
						$("#menu_nombre").focus();
					}
				});
			}
		}
	});
}

function habilitar(menu) {
	Swal.fire({
		icon: "info",
		title: "El menú sera habilitado",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value) {
			var url = base_url + "index.php/basedatos/menu/habilitar_menu";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					menu: menu
				},
				success: function (data) {
					if (data.result == "success") {
						titulo = "¡Menú habilitado!";
						Swal.fire({
							icon: "success",
							title: titulo,
							showConfirmButton: true,
							timer: 2000
						});
					}
					else {
						Swal.fire({
							icon: "error",
							title: "Sin cambios.",
							html: "<b class='color-red'>Algo ha ocurrido, verifique he intentelo nuevamente.</b>",
							showConfirmButton: true,
							timer: 4000
						});
					}
				},
				complete: function () {
				}
			});
		}
	});
}

function deshabilitar(menu) {
	Swal.fire({
		icon: "info",
		title: "Debe confirmar esta acción.",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value) {
			var url = base_url + "index.php/basedatos/menu/deshabilitar_menu";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					menu: menu
				},
				success: function (data) {
					if (data.result == "success") {
						titulo = "¡Menú deshabilitado!";
						Swal.fire({
							icon: "success",
							title: titulo,
							showConfirmButton: true,
							timer: 2000
						});
					}
					else {
						Swal.fire({
							icon: "error",
							title: "Sin cambios.",
							html: "<b class='color-red'>Algo ha ocurrido, verifique he intentelo nuevamente.</b>",
							showConfirmButton: true,
							timer: 4000
						});
					}
				},
				complete: function () {
					search(false);
				}
			});
		}
	});
}

function clean() {
	$("#formMenu")[0].reset();
	$("#menu").val("");
}

function changeOrderEdit(id) {
	$("#iorden-" + id).removeAttr('readonly');
	$("#editOrderEnable-" + id).hide('fast');
	$("#editOrderDisable-" + id + ", #editOrderUpdate-" + id).show('fast');
}

function changeOrderUpdate(id) {
	let form = $("#frmOrden" + id).serialize();
	$.ajax({
		type: 'post',
		dataType: 'json',
		url: base_url + "index.php/basedatos/menu/update_order",
		data: form,
		success: function (data) {
			if (data.result == "success") {
				Swal.fire({
					icon: "success",
					title: 'Orden cambiado',
					showConfirmButton: true,
					timer: 2000
				});
				changeOrderDisable(id);
			}
			else {
				Swal.fire({
					icon: "error",
					title: "Sin cambios.",
					html: "<b class='color-red'>Algo ha ocurrido, verifique he intentelo nuevamente.</b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		}
	});
}

function changeOrderDisable(id, val = null) {
	$("#iorden-" + id).attr({ 'readonly': 'readonly' });
	$("#editOrderEnable-" + id).show('fast');
	$("#editOrderDisable-" + id + ", #editOrderUpdate-" + id).hide('fast');
	
	if (val != null)
		$("#iorden-" + id).val(val);
}