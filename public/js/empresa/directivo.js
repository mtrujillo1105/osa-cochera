/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-empleado').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/directivo/datatable_empleado/',
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
		columnDefs: [{"className": "text-center", "targets": 0, "targets": 8}],
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

	$('#search_codigo, #search_documento, #nombre_empleado').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});
});

function search( search = true){
	if (search == true){
		codigo = $("#search_codigo").val();
		documento = $("#search_documento").val();
		nombre = $("#nombre_empleado").val();
	}
	else{
		$("#search_codigo").val("");
		$("#search_documento").val("");
		$("#nombre_empleado").val("");

		codigo = "";
		documento = "";
		nombre = "";
	}
	
	$('#table-empleado').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/directivo/datatable_empleado/',
			type: "POST",
			data: {
				codigo: codigo,
				documento: documento,
				nombre: nombre
			},
			beforeSend: function(){
				$("#table-empleado .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-empleado .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "text-center", "targets": 0, "targets": 8}],
		order: [[ 1, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/empresa/directivo/getEmpleado";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			empleado: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#tipo_documento").val(info.tipo_documento);
				$("#numero_documento").val(info.numero_documento);
				$("#numero_ruc").val(info.numero_ruc);
				$("#nombres").val(info.nombres);
				$("#apellido_paterno").val(info.apellido_paterno);
				$("#apellido_materno").val(info.apellido_materno);
				$("#fecha_nacimiento").val(info.fecha_nacimiento);
				$("#genero").val(info.genero);
				$("#edo_civil").val(info.edo_civil);
				$("#nacionalidad").val(info.nacionalidad);
				$("#telefono").val(info.telefono);
				$("#movil").val(info.movil);
				$("#fax").val(info.fax);
				$("#correo").val(info.correo);
				$("#web").val(info.web);
				$("#direccion").val(info.direccion);
				$("#direccion").val(info.direccion);

				$("#banco").val(info.banco);
				$("#cta_soles").val(info.cta_soles);
				$("#cta_dolares").val(info.cta_dolares);

				$("#empleado").val(info.empleado);
				$("#cargo").val(info.cargo);
				$("#numero_contrato").val(info.numero_contrato);
				$("#fecha_inicio").val(info.fecha_inicio);
				$("#fecha_final").val(info.fecha_final);
				$("#codigo_empleado").val(info.codigo_empleado);

				$("#add_empleado").modal("toggle");
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

function registrar_empleado(){
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
			var url = base_url + "index.php/empresa/directivo/guardar_registro";

			empleado = $("#empleado").val();
			numero_documento = $("#numero_documento").val();
			nombres = $("#nombres").val();
			apellido_paterno = $("#apellido_paterno").val();
			apellido_materno = $("#apellido_materno").val();
			fecha_nacimiento = $("#fecha_nacimiento").val();
			direccion = $("#direccion").val();

			validacion = true;

			if (numero_documento == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un número de documento.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#numero_documento").focus();
				validacion = false;
				return false;
			}

			if (nombres == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar el nombre.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nombres").focus();
				validacion = false;
				return false;
			}

			if (apellido_paterno == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar el apellido paterno.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#apellido_paterno").focus();
				validacion = false;
				return false;
			}

			if (apellido_materno == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar el apellido materno.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#apellido_materno").focus();
				validacion = false;
				return false;
			}

			if (fecha_nacimiento == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar la fecha de nacimiento.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#fecha_nacimiento").focus();
				validacion = false;
				return false;
			}

			if (direccion == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar la dirección.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#direccion").focus();
				validacion = false;
				return false;
			}

			if (validacion == true){
				var dataForm = $("#formEmpleado").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (empleado == "")
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
                                            search();
                                            $("#numero_documento").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(empleado){
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
			var url = base_url + "index.php/empresa/directivo/deshabilitar_empleado";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					empleado: empleado
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
				}
			});
		}
	});
}

function clean(){
	$("#empleado").val("");
	$("#formEmpleado")[0].reset();
}