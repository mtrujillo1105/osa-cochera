/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-empresa').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/empresa/dt_empresas/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function(){
				$("#table-empresa .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: spanish,
		columnDefs: [
									{"className": "dt-center", "targets": 0}
								],
		order: [[ 1, "desc" ]]
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

	$("#formEmpresa").keypress(function(e){
		if ( e.which == 13 ){
			registrar_empresa();
		}
	});

	$("#formCtaBancaria").keypress(function(e){
		if ( e.which == 13 ){
			registrar_CtaBancaria();
		}
	});

	$('#search_documento, #nombre_empresa').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});

	$("#departamento").change(function(){
		getProvincias();
	});

	$("#provincia").change(function(){
		getDistritos();
	});

	$(".btn-addBanco").click(function(){
		clean_CtaBancaria();
		$("#modal_addctabancaria").modal("toggle");
	});
});

/* EMPRESA */
function search( search = true){
	if (search == true){
		documento = $("#search_documento").val();
		nombre = $("#nombre_empresa").val();
	}
	else{
		$("#search_documento").val("");
		$("#nombre_empresa").val("");

		documento = "";
		nombre = "";
	}

	$('#table-empresa').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/empresa/dt_empresas/',
			type: "POST",
			data: {
				documento: documento,
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
		columnDefs: [
									{"className": "dt-center", "targets": 0}
								],
		order: [[ 0, "desc" ]]
	});
}

function editar_empresa(id){
	var url = base_url + "index.php/empresa/empresa/getEmpresa";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			empresa: id
		},
		beforeSend: function(){
			clean();
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#empresa").val(info.empresa);
			
				$("#tipo_documento").val(info.tipo_documento);
				$("#numero_documento").val(info.numero_documento);

				$("#razon_social").val(info.razon_social);

				$("#direccion").val(info.direccion);
				$("#departamento").val(info.departamento);
				getProvincias(info.departamento, info.provincia, '', '', false);
				getDistritos(info.departamento, info.provincia, info.distrito);

				if (info.sector_comercial != null)
					$("#sector_comercial").val(info.sector_comercial);

				$("#telefono").val(info.telefono);
				$("#movil").val(info.movil);
				$("#fax").val(info.fax);
				$("#correo").val(info.correo);
				$("#web").val(info.web);

				$("#modal_addempresa").modal("toggle");
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

function registrar_empresa(){
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
			var url = base_url + "index.php/empresa/empresa/guardar_registro";

			empresa         = $("#empresa").val();
			numero_documento = $("#numero_documento").val();
			razon_social    = $("#razon_social").val();
			direccion       = $("#direccion").val();

			validacion = true;

			if (razon_social == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una razón social.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#razon_social").focus();
				validacion = false;
				return false;
			}

			if (numero_documento == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un número de documento valido.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#numero_documento").focus();
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
				var dataForm = $("#formEmpresa").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (empresa == "")
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
								icon: data.result,
								title: "Sin cambios.",
								html: "<b class='color-red'>" + data.message + "</b>",
								showConfirmButton: true,
								timer: 4000
							});
						}
					},
					complete: function(){
						search();
					}
				});
			}
		}
	});
}

function clean( id = null ){
	$("#empresa").val("");
	$("#formEmpresa")[0].reset();
	getProvincias('15','01');
}

/* END EMPRESA */


/* CTA BANCARIA */

function modal_CtasBancarias( empresa = null, razon_social = "" ){

	$("#modal_bancos").modal("toggle");

	title = razon_social.split("-");
	$(".titleRuc").html(title[0]);
	$(".titleRazonSocial").html(title[1]);
	$("#btn-ctabancoempresa").val(empresa);

	getTableCtaBancarias();
}

function getTableCtaBancarias(){
	$('#table-bancos').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/tesoreria/bancocta/datatable_ctaEmpresa',
			type: "POST",
			data: {
				empresa: $("#btn-ctabancoempresa").val()
			},
			beforeSend: function(){
				$("#table-bancos .loading-table").show();
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

function editar_CtaBancaria( id ){
	var url = base_url + "index.php/tesoreria/bancocta/getCtaBancaria";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			cta_bancaria: id
		},
		beforeSend: function(){
			clean_CtaBancaria();
			$("#modal_addctabancaria").modal("toggle");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#cta_bancaria").val(info.cta_bancaria);
				$("#cta_bancaria_empresa").val(info.empresa);
				$("#cta_bancaria_persona").val(info.persona);
				$("#banco").val(info.banco);
				$("#cta_bancaria_titular").val(info.titular);
				$("#cta_bancaria_numero").val(info.cta_numero);
				$("#cta_bancaria_interbancaria").val(info.cta_interbancaria);
				$("#cta_bancaria_tipo").val(info.tipo_cuenta);
				$("#cta_bancaria_moneda").val(info.moneda);
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

function registrar_CtaBancaria(){
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
			var url = base_url + "index.php/tesoreria/bancocta/guardar_ctabancaria";

			var cta = $("#cta_bancaria").val();
			var empresa = $("#cta_bancaria_empresa").val();
			var persona = $("#cta_bancaria_persona").val();
			var banco = $("#banco").val();
			var titular = $("#cta_bancaria_titular").val();
			var tipo = $("#cta_bancaria_tipo").val();
			var moneda = $("#cta_bancaria_moneda").val();
			var numero = $("#cta_bancaria_numero").val();
			var interbancaria = $("#cta_bancaria_interbancaria").val();

			validacion = true;

			if (titular == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un titular.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#cta_bancaria_titular").focus();
				validacion = false;
				return false;
			}

			if (numero == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un número de cuenta.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#cta_bancaria_numero").focus();
				validacion = false;
				return false;
			}

			if (cta == ""){
				$("#cta_bancaria_empresa").val( $("#btn-ctabancoempresa").val() );

				if ( $("#cta_bancaria_empresa").val() == "" && $("#cta_bancaria_persona").val() == "" ){
					Swal.fire({
						icon: "error",
						title: "No hay empresa seleccionada.",
						html: "<b class='color-red'>Cierre el formulario de cuentas bancarias e intente ingresar nuevamente.</b>",
						showConfirmButton: true,
						timer: 4000
					});
				}
			}

			if (validacion == true){
				var dataForm = $("#formCtaBancaria").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (cta == "")
								titulo = "¡Registro exitoso!";
							else
								titulo = "¡Actualización exitosa!";

							Swal.fire({
								icon: "success",
								title: titulo,
								showConfirmButton: true,
								timer: 2000
							});

							clean_CtaBancaria();
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
						getTableCtaBancarias();
					}
				});
			}
		}
	});
}

function deshabilitar_CtaBancaria(id){
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
			var url = base_url + "index.php/tesoreria/bancocta/deshabilitar_ctabancaria";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					cta_bancaria: id
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
					getTableCtaBancarias();
				}
			});
		}
	});
}

function clean_CtaBancaria(){
	$("#cta_bancaria").val("");
	$("#cta_bancaria_empresa").val("");
	$("#cta_bancaria_persona").val("");
	$("#formCtaBancaria")[0].reset();
}

/* END CTA BANCARIA */

/* CONTACTOS */

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