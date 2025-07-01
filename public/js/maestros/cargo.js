/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-cargo').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/cargo/datatable_cargo/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function(){
				$("#table-cargo .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-cargo .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 1, "asc" ]]
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

	$('#nombre_cargo').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});
});

function search( search = true){
	if (search == true){
		nombre = $("#nombre_cargo").val();
	}
	else{
		$("#nombre_cargo").val("");
		nombre = "";
	}
	
	$('#table-cargo').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/maestros/cargo/datatable_cargo/',
			type: "POST",
			data: {
				nombre: nombre
			},
			beforeSend: function(){
				$("#table-cargo .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-cargo .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 1, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/maestros/cargo/getCargo";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			cargo: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;
				$("#cargo").val(info.cargo);
				$("#cargo_nombre").val(info.nombre);
				$("#cargo_descripcion").val(info.descripcion);

				$("#add_cargo").modal("toggle");
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

function registrar_cargo(){
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
			var cargo = $("#cargo").val();
			var url = base_url + "index.php/maestros/cargo/guardar_registro";
			var nombre = $("#cargo_nombre").val();
			var descripcion = $("#cargo_descripcion").val();
			validacion = true;

			if (nombre == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un nombre.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#cargo_descripcion").focus();
				validacion = false;
			}

			if (validacion == true){
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: {
						cargo: cargo,
						cargo_nombre: nombre,
						cargo_descripcion: descripcion
					},
					success: function(data){
						if (data.result == "success") {
							if (cargo == "")
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
						$("#cargo_nombre").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(cargo){
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
			var url = base_url + "index.php/maestros/cargo/deshabilitar_cargo";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					cargo: cargo
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
						search(false);
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
				}
			});
		}
	});
}

function clean(){
	$("#cargo").val("");
	$("#cargo_nombre").val("");
	$("#cargo_descripcion").val("");
}