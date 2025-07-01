/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-rol').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/seguridad/rol/datatable_rol/',
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

	$('#nombre_rol').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});

	$('#table-permisos').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		autoWidth: false,
		language: spanish,
		paging: false,
		columnDefs: [{"className": "text-right", "targets": 0}]
	});
});

function search( search = true){
	if (search == true){
		nombre = $("#nombre_rol").val();
	}
	else{
		$("#nombre_rol").val("");
		nombre = "";
	}

	$('#table-rol').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/seguridad/rol/datatable_rol/',
			type: "POST",
			data: {
				nombre: nombre
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

function autocheck(i){
	if ( $(".auto-check-"+i).is(":checked") ){
		console.log(0);
		$(".auto-check-"+i).removeAttr("checked");
	}
	else{
		console.log(1);
		$(".auto-check-"+i).attr("checked", "true");
	}
}

function editar(id){
	var url = base_url + "index.php/seguridad/rol/getPermisos";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			rol: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;
				$("#rol").val(info.rol);
				$("#rol_nombre").val(info.descripcion);

				$.each(info.permisos, function(i,item){
					$(".check-"+item).attr("checked", "true");
				});

				$("#add_rol").modal("toggle");
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

function registrar_rol(){
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
			var rol = $("#rol").val();
			var url = base_url + "index.php/seguridad/rol/guardar_registro";
			var nombre = $("#rol_nombre").val();
			validacion = true;

			if (nombre == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un nombre.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#rol_nombre").focus();
				validacion = false;
			}

			if (validacion == true){
				var info = $("#formRol").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							if (rol == "")
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
						search(false);
						$("#rol_nombre").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(rol){
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
			var url = base_url + "index.php/seguridad/rol/deshabilitar_rol";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					rol: rol
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
					search(false);
				}
			});
		}
	});
}

function clean(){
	$("#rol").val("");
	$(".permiso").removeAttr("checked");
	$("#formRol")[0].reset();
}