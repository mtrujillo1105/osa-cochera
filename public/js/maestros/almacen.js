/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-almacen').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/maestros/almacen/datatable_almacen/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function(){
				$("#table-almacen .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-almacen .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "text-center", "targets": 0}],
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
		search_tipo = $("#search_tipo").val();
	}
	else{
		$("#search_descripcion").val("");
		$("#search_tipo").val("");
		search_descripcion = "";
		search_tipo = "";
	}

	$('#table-almacen').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : base_url + 'index.php/maestros/almacen/datatable_almacen/',
			type: "POST",
			data: {
				descripcion: search_descripcion,
				tipo: search_tipo
			},
			beforeSend: function(){
				$("#table-almacen .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-almacen .loading-table").hide();
			}
		},
		language: spanish,
		columnDefs: [{"className": "text-center", "targets": 0}],
		order: [[ 1, "asc" ]]
	});
}

function editar(id){
	var url = base_url + "index.php/maestros/almacen/getAlmacen";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			almacen: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#almacen").val(info.almacen);
				$("#codigo_almacen").val(info.codigo);
				$("#descripcion_almacen").val(info.descripcion);
				$("#tipo_almacen").val(info.tipo);
				$("#compartir_almacen").val(info.compartido);
				$("#direccion_almacen").val(info.direccion);

				$("#add_almacen").modal("toggle");
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
			$("#codigo_almacen").attr({"readonly": true});
		}
	});
}

function registrar_almacen(){
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
			var almacen = $("#almacen").val();
			var descripcion = $("#descripcion_almacen").val();
			var codigo = $("#codigo_almacen").val();
			validacion = true;
		
			if (codigo == "" && almacen == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un código.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#codigo_almacen").focus();
				validacion = false;
				return null;
			}
			
			if (descripcion == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una descripcion.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#descripcion_almacen").focus();
				validacion = false;
				return null;
			}

			if (validacion == true){
				var url = base_url + "index.php/maestros/almacen/guardar_registro";
				var info = $("#formAlmacen").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							Swal.fire({
								icon: "success",
								title: data.message,
								showConfirmButton: true,
								timer: 2000
							});
							clean();
							search(false);
						}
						else{
							Swal.fire({
								icon: data.result,
								title: "Sin cambios.",
								html: "<b class='color-red'>" + data.message + "</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function(){
						$("#descripcion_almacen").focus();
					}
				});
			}
		}
	});
}

function deshabilitar(almacen){
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
			var url = base_url + "index.php/maestros/almacen/deshabilitar_almacen";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					almacen: almacen
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
	$("#formAlmacen")[0].reset();
	$("#almacen").val("");
	$("#codigo_almacen").removeAttr("readonly");
}