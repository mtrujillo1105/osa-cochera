/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-um').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/unidadmedida/datatable_um/',
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

	$('#search_descripcion').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
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

	$('#table-um').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/maestros/unidadmedida/datatable_um/',
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
		order: [[ 0, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/maestros/unidadmedida/getUnidad";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			um: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#um").val(info.um);
				$("#simbolo_um").val(info.simbolo);
				$("#descripcion_um").val(info.descripcion);

				$("#add_um").modal("toggle");
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

function registrar_um(){
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
			var um = $("#um").val();
			var descripcion = $("#descripcion_um").val();
			var simbolo = $("#simbolo_um").val();
			validacion = true;

			if (descripcion == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una descripcion.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#descripcion_um").focus();
				validacion = false;
				return null;
			}

			if (simbolo == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar el simbolo (abreviación aceptada por el facturador).</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#simbolo_um").focus();
				validacion = false;
				return null;
			}

			if (validacion == true){
				var url = base_url + "index.php/maestros/unidadmedida/guardar_registro";
				var info = $("#formUnidad").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							if (um == "")
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
						$("#descripcion_um").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(um){
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
			var url = base_url + "index.php/maestros/unidadmedida/deshabilitar_um";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					um: um
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
	$("#formUnidad")[0].reset();
	$("#um").val("");
}