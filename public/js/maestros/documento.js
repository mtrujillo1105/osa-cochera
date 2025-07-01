/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-documento').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/documento/datatable_documento/',
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
		pageLength: 25,
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

	$('#search_descripcion').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});

	$("#formDocumento input").keyup(function(){
		if ( $(this).val().trim() == '' ){
			$(this).removeClass('is-valid');
			$(this).addClass('is-invalid');
		}
		else{
			$(this).removeClass('is-invalid');
			$(this).addClass('is-valid');
		}
	});
});

function search( search = true){
	if (search == true){
		search_descripcion = $("#search_descripcion").val();
	}
	else{
		$("#search_descripcion").val("");
		search_descripcion = "";
	}

	$('#table-documento').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/maestros/documento/datatable_documento/',
			type: "POST",
			data: {
				descripcion: search_descripcion
			},
			beforeSend: function(){
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: spanish,
		pageLength: 25,
		order: [[ 0, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/maestros/documento/getDocumento";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			documento: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#documento").val(info.documento);
				$("#descripcion_documento").val(info.descripcion);
				$("#inicial_documento").val(info.inicial);
				$("#estado_documento").val(info.estado);
				$("#abreviacion_documento").val(info.abreviacion);

				$("#add_documento").modal("toggle");
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

function registrar_documento(){
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
			var documento = $("#documento").val();
			var descripcion = $("#descripcion_documento").val();
			var inicial = $("#inicial_documento").val();
			var estado = $("#estado_documento").val();
			var abreviacion = $("#abreviacion_documento").val();
			validacion = true;

			if (descripcion == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una descripcion.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#descripcion_documento").focus();
				validacion = false;
				return null;
			}

			if (inicial == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar la inicial del documento.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#inicial_documento").focus();
				validacion = false;
				return null;
			}

			if (abreviacion == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una abreviación.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#abreviacion_documento").focus();
				validacion = false;
				return null;
			}

			if (validacion == true){
				var url = base_url + "index.php/maestros/documento/guardar_registro";
				var info = $("#formDocumento").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							if (documento == "")
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
							search(false);
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
						$("#descripcion_documento").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(documento){
	Swal.fire({
		icon: "info",
		title: "Debe confirmar esta acción.",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/maestros/documento/deshabilitar_documento";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					documento: documento
				},
				success: function(data){
					if (data.result == "success") {
						titulo = "Deshabilitado!";
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

function habilitar(documento){
	Swal.fire({
		icon: "info",
		title: "Debe confirmar esta acción.",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/maestros/documento/habilitar_documento";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					documento: documento
				},
				success: function(data){
					if (data.result == "success") {
						titulo = "¡Habilitado!";
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
	$("#formDocumento")[0].reset();
	$("#documento").val("");
}