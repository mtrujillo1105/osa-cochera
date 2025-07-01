/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {
	$('#table-familia').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax: {
			url: base_url + 'index.php/almacen/familia/datatable_familia/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function () {
			},
			error: function () {
			},
			complete: function () {
			}
		},
		pageLength: 25,
		language: spanish,
		columnDefs: [{ "className": "text-center", "targets": 0 }],
		order: [[2, "asc"]]
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

	$('#search_descripcion').keyup(function (e) {
		if (e.which == 13) {
			if ($(this).val() != '')
				search();
		}
	});
});

function search(search = true) {
	let search_tipo = '';
	let search_codigo = '';
	let search_descripcion = '';

	if (search == true) {
		search_tipo = $("#search_tipo").val();
		search_codigo = $("#search_codigo").val();
		search_descripcion = $("#search_descripcion").val();
	}
	else {
		$("#form_busqueda")[0].reset();
	}

	$('#table-familia').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax: {
			url: base_url + 'index.php/almacen/familia/datatable_familia/',
			type: "POST",
			data: {
				tipo: search_tipo,
				codigo: search_codigo,
				descripcion: search_descripcion
			},
			beforeSend: function () {
			},
			error: function () {
			},
			complete: function () {
			}
		},
		pageLength: 25,
		language: spanish,
		columnDefs: [{ "className": "text-center", "targets": 0 }],
		order: [[2, "asc"]]
	});
}

function editar(id) {
	var url = base_url + "index.php/almacen/familia/getFamilia";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: {
			familia: id
		},
		beforeSend: function () {
			clean();
		},
		success: function (data) {
			if (data.match == true) {
				info = data.info;
				$("#familia").val(info.id);
				$("#tipoFamilia").val(info.flagBS);
				$("#codigoFamilia").val(info.codigo);
				$("#descripcionFamilia").val(info.descripcion);
				$("#add_familia").modal("toggle");
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

function registrar_familia() {
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
			let familia = $("#familia").val();
			let tipo = $("#tipoFamilia").val();
			let codigo = $("#codigoFamilia").val();
			let descripcion = $("#descripcionFamilia").val();
			let validacion = true;

			if (descripcion == "") {
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una descripcion.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#descripcion_familia").focus();
				validacion = false;
			}

			if (validacion == true) {
				let url = base_url + "index.php/almacen/familia/guardar_registro";
				let info = $("#formFamilia").serialize();
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
					}
				});
			}
		}
	});
}

function deshabilitar(familia) {
	Swal.fire({
		icon: "info",
		title: "Debe confirmar esta acción.",
		html: "<b class='color-red'>Esta acción no se puede deshacer</b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value) {
			let url = base_url + "index.php/almacen/familia/deshabilitar_familia";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					familia: familia
				},
				success: function (data) {
					Swal.fire({
						icon: data.result,
						title: data.title,
						html: "<b class='color-red'>" + data.message + "</b>",
						showConfirmButton: true,
						timer: 5000
					});
					if (data.result == 'success') {
						search(false);
					}
				},
				complete: function () {
				}
			});
		}
	});
}

function clean() {
	$("#formFamilia")[0].reset();
	$("#familia").val("");
}