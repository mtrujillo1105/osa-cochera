/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-proyecto').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/proyecto/datatable_proyecto/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function(){
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: spanish,
		order: [[ 0, "asc" ]]
	});

	$("#buscar").click(function(){
		search();
	});

	$("#limpiar").click(function(){
		search(false);
	});

	$('#form_busqueda').keypress(function(e){
		if ( e.which == 13 ){
			return false;
		} 
	});

	$('#search_proyecto').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});

	$("#search_razon_social").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "index.php/empresa/cliente/autocomplete/",
				type: "POST",
				data: {
					term: $("#search_razon_social").val()
				},
				dataType: "json",
				success: function (data) {
					response(data);
				}
			});
		},
		select: function (event, ui){
			$("#search_cliente").val(ui.item.codigo);
			$("#search_ruc").val(ui.item.ruc);
			$("#search_razon_social").val(ui.item.nombre);
		},
		minLength: 2
	});		

	$("#search_ruc").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "index.php/empresa/cliente/autocomplete_ruc/",
				type: "POST",
				data: {
					term: $("#search_ruc").val()
				},
				dataType: "json",
				success: function (data){
					response(data);
				}
			});
		},
		select: function (event, ui) {
			$("#search_cliente").val(ui.item.codigo);
			$("#search_ruc").val(ui.item.ruc);
			$("#search_razon_social").val(ui.item.nombre);
		},
		minLength: 2
	});

	$("#razon_social").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "index.php/empresa/cliente/autocomplete/",
				type: "POST",
				data: {
					term: $("#razon_social").val()
				},
				dataType: "json",
				success: function (data) {
					response(data);
				}
			});
		},
		select: function (event, ui){
			$("#cliente").val(ui.item.codigo);
			$("#ruc").val(ui.item.ruc);
			$("#razon_social").val(ui.item.nombre);
		},
		minLength: 2
	});

	$("#ruc").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: base_url + "index.php/empresa/cliente/autocomplete_ruc/",
				type: "POST",
				data: {
					term: $("#ruc").val()
				},
				dataType: "json",
				success: function (data){
					response(data);
				}
			});
		},
		select: function (event, ui) {
			$("#cliente").val(ui.item.codigo);
			$("#ruc").val(ui.item.ruc);
			$("#razon_social").val(ui.item.nombre);
		},
		minLength: 2
	});

	$("#departamento").change(function(){
		getProvincias();
	});

	$("#provincia").change(function(){
		getDistritos();
	});

	$("#establecimiento_departamento").change(function(){
		getProvincias(null, null, "#establecimiento_departamento", "#establecimiento_provincia");
	});

	$("#establecimiento_provincia").change(function(){
		getDistritos(null, null, null, "#establecimiento_departamento", "#establecimiento_provincia", "#establecimiento_distrito");
	});
});

function search( search = true){
	if (search == true){
		search_proyecto = $("#search_proyecto").val();
		search_cliente = $("#search_cliente").val();
	}
	else{
		$("#search_proyecto").val("");
		$("#search_cliente").val("");

		search_proyecto = "";
		search_cliente = "";

		$("#search_ruc").val("");
		$("#search_razon_social").val("");
	}

	$('#table-proyecto').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/maestros/proyecto/datatable_proyecto/',
			type: "POST",
			data: {
				search_proyecto: search_proyecto,
				search_cliente: search_cliente
			},
			beforeSend: function(){
				$("#table-proyecto .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-proyecto .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 0, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/maestros/proyecto/getProyecto";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			proyecto: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#proyecto").val(info.proyecto);
				$("#cliente").val(info.cliente);
				$("#ruc").val(info.ruc);
				$("#razon_social").val(info.razon_social);
				$("#nombre_proyecto").val(info.nombre_proyecto);
				$("#fecha_inicio").val(info.fecha_inicio);
				$("#fecha_final").val(info.fecha_final);
				$("#descripcion_proyecto").val(info.descripcion_proyecto);

				$("#add_proyecto").modal("toggle");
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
		}
	});
}

function viewInfo(id){
	var url = base_url + "index.php/maestros/proyecto/getProyecto";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			proyecto: id
		},
		beforeSend: function(){
			$('#table-comprobantes').DataTable().destroy();
			$("#table-comprobantes .comprobantes-info").html("");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;
				docs = data.comprobantes;

				$(".modal_ruc").html(info.ruc);
				$(".modal_razonSocial").html(info.razon_social);
				$(".nombre_proyecto").html(info.nombre_proyecto);

				$(".modal_ruc").html(info.ruc);
				$(".modal_razonSocial").html(info.razon_social);
				$(".modal_titulo").html(info.nombre_proyecto);
				$(".modal_fechaInicio").html(info.fecha_inicio_corta);
				$(".modal_fechaFinal").html(info.fecha_final_corta);

				if (info.descripcion_proyecto != "")
					$(".modal_descripcion").html(info.descripcion_proyecto);
				else
					$(".modal_descripcion").html("Sin descripción.");

				if (docs != null){
					$.each(docs, function(i,item){
						tr = '<tr>';
						tr += '<td>' + item.fecha + '</td>';
						tr += '<td>' + item.empresa_emisora + '</td>';
						tr += '<td>' + item.documento + '</td>';
						tr += '<td>' + item.serie + '</td>';
						tr += '<td>' + item.numero + '</td>';
						tr += '<td>' + item.moneda + '</td>';
						tr += '<td>' + item.importe + '</td>';
						tr += '<td>' + item.estado + '</td>';
						tr += '<td>' + item.pdf + '</td>';
						tr += '</tr>';

						$("#table-comprobantes .comprobantes-info").append(tr);
					});

					$('#table-comprobantes').DataTable({
						responsive: true,
						filter: true,
						destroy: true,
						autoWidth: false,
						language: spanish
					});
				}
				else{
					tr = '<tr>';
					tr += '<td colspan="6">No se encontraron facturas, boletas o comprobantes asociados.</td>';
					tr += '</tr>';
				}

				$("#modal_infoProyecto").modal("toggle");
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
		}
	});
}

function registrar_proyecto(){
	Swal.fire({
		icon: "question",
		title: "¿Esta seguro de guardar el registro?",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var proyecto = $("#proyecto").val();
			var cliente = $("#cliente").val();
			var ruc = $("#ruc").val();
			var razon_social = $("#razon_social").val();
			var nombre_proyecto = $("#nombre_proyecto").val();
			var fecha_inicio = $("#fecha_inicio").val();
			var fecha_final = $("#fecha_final").val();
			var descripcion_proyecto = $("#descripcion_proyecto").val();

			validacion = true;

			if (cliente == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe seleccionar un cliente.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#cliente").focus();
				validacion = false;
				return null;
			}

			if (nombre_proyecto == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar el nombre del proyecto.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nombre_proyecto").focus();
				validacion = false;
				return null;
			}

			if (validacion == true){
				var url = base_url + "index.php/maestros/proyecto/guardar_registro";
				var info = $("#formProyecto").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							if (proyecto == "")
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
						}
						else{
							Swal.fire({
								icon: "error",
								title: "Sin cambios.",
								html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function(){
						$("#descripcion_proyecto").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(proyecto){
	Swal.fire({
		icon: "info",
		title: "Debe confirmar esta acción.",
		html: "<b class='color-red'>Esta acción no se puede deshacer</b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/maestros/proyecto/deshabilitar_proyecto";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					proyecto: proyecto
				},
				success: function(data){
					if (data.result == "success") {
						titulo = "¡Registro eliminado!";
						Swal.fire({
							icon: "success",
							title: titulo,
							showConfirmButton: true,
							timer: 2000
						});
					}
					else{
						Swal.fire({
							icon: "error",
							title: "Sin cambios.",
							html: "<b class='color-red'>Algo ha ocurrido, verifique he intentelo nuevamente.</b>",
							showConfirmButton: true,
							timer: 4000
						});
					}
				},
				complete: function(){
					search(false);
				}
			});
		}
	});
}

function clean(){
	$("#formProyecto")[0].reset();
	$("#proyecto").val("");
	$("#cliente").val("");
}

function viewdirections(id){
	var url = base_url + "index.php/maestros/proyecto/getProyecto";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			proyecto: id
		},
		beforeSend: function(){
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$(".modal_ruc").html(info.ruc);
				$(".modal_razonSocial").html(info.razon_social);
				$(".nombre_proyecto").html(info.nombre_proyecto);
				$(".modal_titulo").html(info.nombre_proyecto);
				$(".modal_fechaInicio").html(info.fecha_inicio_corta);
				$(".modal_fechaFinal").html(info.fecha_final_corta);

				if (info.descripcion_proyecto != "")
					$(".modal_descripcion").html(info.descripcion_proyecto);
				else
					$(".modal_descripcion").html("Sin descripción.");

				$("#modalDirections").modal("toggle");
				getTableDirections(id);
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
			$("#proyecto_id").val(id);
		}
	});
}

function getTableDirections(id){
	$('#table-directions').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/proyecto/datatable_directions',
			type: "POST",
			data: {
				proyecto: id
			},
			beforeSend: function(){
				$("#table-directions .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-directions .loading-table").hide();
			}
		},
		language: spanish,
		order: [[ 0, "asc" ]]
	});
}

function editar_directions( id ){
	var url = base_url + "index.php/maestros/proyecto/getDirection";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			direction: id
		},
		beforeSend: function(){
			$("#add_directions").modal("toggle");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#direction_id").val(info.direction_id);
				$("#proyecto_id").val(info.proyecto);
				$("#direccion_proyecto").val(info.direccion);
				$("#referencia_proyecto").val(info.referencia);

				$("#departamento").val(info.departamento);
				getProvincias(info.departamento, info.provincia, "", "", false)
				getDistritos(info.departamento, info.provincia, info.distrito)
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
		}
	});
}

function register_directions(){
	Swal.fire({
		icon: "question",
		title: "¿Esta seguro de guardar el registro?",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/maestros/proyecto/save_direction";

			var id = $("#direction_id").val();
			var proyecto = $("#proyecto_id").val();
			var direccion = $("#direccion_proyecto").val();

			validacion = true;

			if (proyecto == ""){
				Swal.fire({
					icon: "info",
					title: "Falta la asignación del proyecto",
					html: "<b class='color-red'>Cierre esta ventana he intentelo nuevamente.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				validacion = false;
				return false;
			}

			if (direccion == ""){
				Swal.fire({
					icon: "info",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una dirección.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#direccion_proyecto").focus();
				validacion = false;
				return false;
			}

			if (validacion == true){
				var dataForm = $("#formDirections").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (id == "")
								titulo = "¡Registro exitoso!";
							else
								titulo = "¡Actualización exitosa!";

							Swal.fire({
								icon: "success",
								title: titulo,
								showConfirmButton: true,
								timer: 2000
							});

							clean_direction();
						}
						else{
							Swal.fire({
								icon: "error",
								title: "Sin cambios.",
								html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function(){
						getTableDirections(proyecto);
					}
				});
			}
		}
	});
}

function disable_directions(id, proyecto = 0){
	Swal.fire({
		icon: "info",
		title: "¿Esta seguro de eliminar el registro seleccionado?",
		html: "<b class='color-red'>Esta acción no se puede deshacer.</b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/maestros/proyecto/disable_direction";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					direccion: id
				},
				success: function(data){
					if (data.result == "success") {
						titulo = "¡Registro eliminado!";
						Swal.fire({
							icon: "success",
							title: titulo,
							showConfirmButton: true,
							timer: 2000
						});
					}
					else{
						Swal.fire({
							icon: "error",
							title: "Sin cambios.",
							html: "<b class='color-red'>La información no pudo ser eliminada, intentelo nuevamente.</b>",
							showConfirmButton: true,
							timer: 4000
						});
					}
				},
				complete: function(){
					if (proyecto > 0)
						getTableDirections(proyecto);
				}
			});
		}
	});
}

function clean_direction(){
	$("#formDirections")[0].reset();
	$("#direction_id").val("");
}

/* UBIGEO */

function getProvincias( dpto = null, select = null, inputDpto = "", inputProv = "", getDist = true){

	if ( dpto == null )
		dpto = (inputDpto == "") ? $("#departamento").val() : $(inputDpto).val();

	var url = base_url + "index.php/maestros/ubigeo/getProvincias";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			departamento: dpto
		},
		beforeSend: function(){
			if (inputProv == "")
				$("#provincia").html("");
			else
				$(inputProv).html("");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				options = '';
				$.each(info, function(i,item){
					if (select != null && item.codigo == select)
						selected = "selected";
					else
						selected = "";

					options += '<option value="' + item.codigo + '" ' + selected + '>' + item.descripcion + '</option>';
				});

				if (inputProv == "")
					$("#provincia").append(options);
				else
					$(inputProv).append(options);
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información de provincias no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
			if (getDist == true){
				if (inputProv == "")
					getDistritos();
				else
					getDistritos(null, null, null, "#establecimiento_departamento", "#establecimiento_provincia", "#establecimiento_distrito");
			}
		}
	});
}

function getDistritos( dpto = null, prov = null, select = null, inputDpto = "", inputProv = "", inputDist = ""){

	if (dpto == null)
		dpto = (inputDpto == "") ? $("#departamento").val() : $(inputDpto).val();

	if (prov == null)
		prov = (inputProv == "") ? $("#provincia").val() : $(inputProv).val();

	var url = base_url + "index.php/maestros/ubigeo/getDistritos";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			departamento: dpto,
			provincia: prov
		},
		beforeSend: function(){
			if (inputDist == "")
				$("#distrito").html("");
			else
				$(inputDist).html("");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				options = '';
				$.each(info, function(i,item){
					if (select != null && item.codigo == select)
						selected = "selected";
					else
						selected = "";

					options += '<option value="' + item.codigo + '" ' + selected + '>' + item.descripcion + '</option>';
				});

				if (inputDist == "")
					$("#distrito").append(options);
				else
					$(inputDist).append(options);
			}
			else{
				Swal.fire({
					icon: "info",
					title: "Información de distritos no disponible.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
		}
	});
}

/* END UBIGEO */