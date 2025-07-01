/* *********************************************************************************
/* ******************************************************************************** */
	$(document).ready(function(){
		$('#table-proveedor').DataTable({
			responsive: true,
			filter: false,
			destroy: true,
			processing: true,
			serverSide: true,
			autoWidth: false,
			ajax:{
				url : base_url + 'index.php/empresa/proveedor/datatable_proveedor/',
				type: "POST",
				data: { dataString: "" },
				beforeSend: function(){
					$("#table-proveedor .loading-table").show();
				},
				error: function(){
				},
				complete: function(){
				}
			},
			language: spanish,
			columnDefs: [
			{"className": "dt-center", "targets": 0},
			{"className": "dt-center", "targets": 1}
			],
			order: [[ 2, "asc" ]]
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

		$("#formProveedor").keypress(function(e){
			if ( e.which == 13 ){
				registrar_proveedor();
			}
		});

		$("#numero_documento").keyup(function(e){
			if ( e.which == 16 ){
				if( $(this).val() != '' )
					getSunat();
			}
		});

		$("#formCtaBancaria").keypress(function(e){
			if ( e.which == 13 ){
				registrar_CtaBancaria();
			}
		});

		$("#formContacto").keyup(function(e){
			if ( e.which == 13 ){
				registrar_contacto();
			}
		});

		$("#formSucursal").keyup(function(e){
			if ( e.which == 13 ){
				registrar_sucursal();
			}
		});

		$('#search_codigo, #search_documento, #nombre_proveedor').keyup(function(e){
			if ( e.which == 13 ){
				if( $(this).val() != '' )
					search();
			}
		});

		$("#tipo_proveedor").change(function(){
			show_tipoProveedor( parseInt($(this).val()) );
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

		$("#idproveedor").click(function(){
			if ($("#proveedor").val() == ""){
				var url = base_url + "index.php/empresa/proveedor/generateCodeProveedor";
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data:{
						json: true
					},
					beforeSend: function(){
					},
					success: function(data){
						if (data.code != "") {
							$("#idproveedor").val(data.code);
						}
						else{
							Swal.fire({
								icon: "info",
								title: "Información no disponible.",
								html: "<b class='color-red'>La información consultada no esta disponible. Intentelo nuevamente.</b>",
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

		$(".btn-search-sunat").click(function(){
			getSunat();
		});

		$(".btn-addSucursal").click(function(){
			clean_sucursal();
			$("#modal_addsucursal").modal("toggle");
		});

		$(".btn-addBanco").click(function(){
			clean_CtaBancaria();
			$("#modal_addctabancaria").modal("toggle");
		});

		$(".btn-addContacto").click(function(){
			clean_contacto();
			$("#modal_addcontacto").modal("toggle");
		});
	});

	/* PROVEEDOR */
	function search( search = true){
		if (search == true){
			documento = $("#search_documento").val();
			nombre = $("#nombre_proveedor").val();
		}
		else{
			$("#search_codigo").val("");
			$("#search_documento").val("");
			$("#nombre_proveedor").val("");

			documento = "";
			nombre = "";
		}

		$('#table-proveedor').DataTable({ responsive: true,
			filter: false,
			destroy: true,
			processing: true,
			serverSide: true,
			autoWidth: false,
			ajax:{
				url : base_url + 'index.php/empresa/proveedor/datatable_proveedor/',
				type: "POST",
				data: {
					documento: documento,
					nombre: nombre
				},
				beforeSend: function(){
					$("#table-proveedor .loading-table").show();
				},
				error: function(){
				},
				complete: function(){
				}
			},
			language: spanish,
			columnDefs: [
			{"className": "dt-center", "targets": 0},
			{"className": "dt-center", "targets": 1}
			],
			order: [[ 2, "asc" ]]
		});
	}

	function editar_proveedor(id){
		var url = base_url + "index.php/empresa/proveedor/getProveedor";
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'json',
			data:{
				proveedor: id
			},
			beforeSend: function(){
				clean();
				$(".divJuridico").hide();
				$(".divNatural").hide();
			},
			success: function(data){
				if (data.match == true) {
					info = data.info;

					show_tipoProveedor(info.tipo_proveedor);

					$("#proveedor").val(info.proveedor);

					doc = "DOC" + info.tipo_proveedor;

					$("#tipo_proveedor").val(info.tipo_proveedor);
					$("#tipo_documento").val(info.tipo_documento);
					$("#numero_documento").val(info.numero_documento);

					$("#razon_social").val(info.razon_social);

					$("#nombres").val(info.nombres);
					$("#apellido_paterno").val(info.apellido_paterno);
					$("#apellido_materno").val(info.apellido_materno);
					$("#genero").val(info.genero);
					$("#edo_civil").val(info.edo_civil);
					$("#nacionalidad").val(info.nacionalidad);

					$("#direccion").val(info.direccion);
					$("#departamento").val(info.departamento);

					getProvincias(info.departamento, info.provincia, "", "", false);
					getDistritos(info.departamento, info.provincia, info.distrito);

					if (info.sector_comercial != null)
						$("#sector_comercial").val(info.sector_comercial);

					if (info.forma_pago != null)
						$("#forma_pago").val(info.forma_pago);

					if (info.categoria != null)
						$("#categoria").val(info.categoria);

					$("#telefono").val(info.telefono);
					$("#movil").val(info.movil);
					$("#fax").val(info.fax);
					$("#correo").val(info.correo);
					$("#web").val(info.web);

					$("#modal_addproveedor").modal("toggle");
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

	function registrar_proveedor(){
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
				var url = base_url + "index.php/empresa/proveedor/guardar_registro";

				proveedor         = $("#proveedor").val();

				tipo_proveedor    = $("#tipo_proveedor").val();
				tipo_documento  = $("#tipo_documento").val();
				numero_documento = $("#numero_documento").val();

				razon_social    = $("#razon_social").val();

				nombres         = $("#nombres").val();
				apellido_paterno = $("#apellido_paterno").val();
				apellido_materno = $("#apellido_materno").val();
				genero          = $("#genero").val();
				edo_civil       = $("#edo_civil").val();
				nacionalidad    = $("#nacionalidad").val();

				direccion       = $("#direccion").val();
				departamento    = $("#departamento").val();
				provincia       = $("#provincia").val();
				distrito        = $("#distrito").val();

				idproveedor       = $("#idproveedor").val();
				vendedor        = $("#vendedor").val();
				sector_comercial = $("#sector_comercial").val();
				forma_pago      = $("#forma_pago").val();
				categoria       = $("#categoria").val();
				telefono        = $("#telefono").val();
				movil           = $("#movil").val();
				fax             = $("#fax").val();
				correo          = $("#correo").val();
				web             = $("#web").val();

				validacion = true;

				if (tipo_proveedor == "1"){
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
				}
				else{
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
					var dataForm = $("#formProveedor").serialize();
					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: dataForm,
						success: function(data){
							if (data.result == "success") {
								if (proveedor == "")
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
									html: "<b class='color-red'>" + data.message + "</b>",
									showConfirmButton: true,
									timer: 4000
								});
							}
						},
						complete: function(){
							$("#numero_documento").focus();
						}
					});
				}
			}
		});
}

function deshabilitar_proveedor(id){
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
			var url = base_url + "index.php/empresa/proveedor/deshabilitar_proveedor";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					proveedor: id
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
							icon: data.result,
							title: data.message,
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
	});
}

function show_tipoProveedor( id = null ){
	if (id == null)
		id = parseInt( $("#tipo_proveedor").val() );
	else
		$("#tipo_proveedor").val(id);

	$(".divNatural").removeAttr("hidden");

	if ( id == 0 ){
		$(".divJuridico").hide("fast");
		$(".divNatural").show("slow");

		$(".documentosJuridico").attr({ "disabled": "disabled" });
		$(".DOC1").removeAttr("selected");

		$(".documentosNatural").removeAttr("disabled");
		$(".DOC0").first().attr({"selected":"selected"});
	}
	else if ( id == 1 ){
		$(".divNatural").hide("fast");
		$(".divJuridico").show("slow");

		$(".documentosNatural").attr({ "disabled": "disabled" });
		$(".DOC0").removeAttr("selected");

		$(".documentosJuridico").removeAttr("disabled");
		$(".DOC1").first().attr({"selected":"selected"});
	}
}

function clean( id = null ){
	$("#proveedor").val("");
	$("#formProveedor")[0].reset();

	show_tipoProveedor( id );
	getProvincias('15','01', '', '', false);
	getDistritos('15','01','01');
}
/* END PROVEEDOR */

/* SUCURSAL */

function sucursales( empresa = null, razon_social = "" ){

	$("#modal_sucursales").modal("toggle");

	title = razon_social.split("-");
	$(".titleRuc").html(title[0]);
	$(".titleRazonSocial").html(title[1]);
	$(".btn-addSucursal").val(empresa);

	getTableSucursales();
}

function getTableSucursales(){
	$('#table-sucursales').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/empresa/datatable_sucursales',
			type: "POST",
			data: {
				empresa: $(".btn-addSucursal").val()
			},
			beforeSend: function(){
				$("#table-sucursales .loading-table").show();
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

function editar_sucursal( id ){
	var url = base_url + "index.php/empresa/empresa/getEstablecimiento";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			sucursal: id
		},
		beforeSend: function(){
			clean_sucursal();
			$("#modal_addsucursal").modal("toggle");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#sucursal").val(info.sucursal);
				$("#establecimiento_nombre").val(info.nombre);
				$("#establecimiento_tipo").val(info.tipo);
				$("#establecimiento_direccion").val(info.direccion);

				$("#establecimiento_departamento").val(info.departamento);
				getProvincias(info.departamento, info.provincia, "#establecimiento_departamento", "#establecimiento_provincia", false)
				getDistritos(info.departamento, info.provincia, info.distrito, "#establecimiento_departamento", "#establecimiento_provincia", "#establecimiento_distrito")
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

function registrar_sucursal(){
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
			var url = base_url + "index.php/empresa/empresa/guardar_sucursal";

			sucursal = $("#sucursal").val();
			nombre = $("#establecimiento_nombre").val();
			direccion = $("#establecimiento_direccion").val();

			validacion = true;

			if (nombre == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un nombre valido.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#establecimiento_nombre").focus();
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
				$("#establecimiento_direccion").focus();
				validacion = false;
				return false;
			}

			if (sucursal == ""){
				$("#sucursal_empresa").val( $(".btn-addSucursal").val() );

				if ( $("#sucursal_empresa").val() == "" ){
					Swal.fire({
						icon: "error",
						title: "No hay empresa seleccionada.",
						html: "<b class='color-red'>Cierre el formulario de sucursales e intente ingresar nuevamente.</b>",
						showConfirmButton: true,
						timer: 4000
					});
				}
			}

			if (validacion == true){
				var dataForm = $("#formSucursal").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (sucursal == "")
								titulo = "¡Registro exitoso!";
							else
								titulo = "¡Actualización exitosa!";

							Swal.fire({
								icon: "success",
								title: titulo,
								showConfirmButton: true,
								timer: 2000
							});

							clean_sucursal();
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
						getTableSucursales();
					}
				});
			}
		}
	});
}

function deshabilitar_sucursal(id){
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
			var url = base_url + "index.php/empresa/empresa/deshabilitar_sucursal";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					sucursal: id
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
					getTableSucursales();
				}
			});
		}
	});
}

function clean_sucursal(){
	$("#sucursal").val("");
	$("#sucursal_empresa").val("");
	$("#formSucursal")[0].reset();
}

/* END SUCURSAL */

/* CTA BANCARIA */

function modal_CtasBancarias( empresa = null, persona = null, razon_social = "" ){

	$("#modal_bancos").modal("toggle");

	title = razon_social.split("-");
	$(".titleRuc").html(title[0]);
	$(".titleRazonSocial").html(title[1]);
	$("#btn-ctabancoempresa").val(empresa);
	$("#btn-ctabancopersona").val(persona);

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
				empresa: $("#btn-ctabancoempresa").val(),
				persona: $("#btn-ctabancopersona").val()
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
				$("#cta_bancaria_persona").val( $("#btn-ctabancopersona").val() );

				if ( $("#cta_bancaria_empresa").val() == "" && $("#cta_bancaria_persona").val() == "" ){
					Swal.fire({
						icon: "error",
						title: "No hay proveedor/proveedor seleccionado.",
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

function modal_contactos( empresa = null, persona = null, razon_social = "" ){

	$("#modal_contactos").modal("toggle");

	title = razon_social.split("-");
	$(".titleRuc").html(title[0]);
	$(".titleRazonSocial").html(title[1]);
	$("#btn-contactoempresa").val(empresa);
	$("#btn-contactopersona").val(persona);

	getTableContactos();
}

function getTableContactos(){
	$('#table-contactos').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/empresa/datatable_contactos',
			type: "POST",
			data: {
				empresa: $("#btn-contactoempresa").val(),
				persona: $("#btn-contactopersona").val()
			},
			beforeSend: function(){
				$("#table-contactos .loading-table").show();
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

function editar_contacto( id ){
	var url = base_url + "index.php/empresa/empresa/getContacto";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
			contacto: id
		},
		beforeSend: function(){
			clean_contacto();
			$("#modal_addcontacto").modal("toggle");
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#contacto").val(info.contacto);
				$("#contacto_empresa").val(info.empresa);
				$("#contacto_persona").val(info.persona);
				$("#contacto_nombre").val(info.nombre);
				$("#contacto_area").val(info.area);
				$("#contacto_cargo").val(info.cargo);
				$("#contacto_telefono").val(info.telefono);
				$("#contacto_movil").val(info.movil);
				$("#contacto_fax").val(info.fax);
				$("#contacto_correo").val(info.correo);
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

function registrar_contacto(){
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
			var url = base_url + "index.php/empresa/empresa/guardar_contacto";

			var contacto = $("#contacto").val();
			var empresa = $("#contacto_empresa").val();
			var persona = $("#contacto_persona").val();

			var nombre = $("#contacto_nombre").val();
			var telefono = $("#contacto_telefono").val();
			var movil = $("#contacto_movil").val();
			var fax = $("#contacto_fax").val();
			var correo = $("#contacto_correo").val();

			validacion = true;

			if (nombre == ""){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar un nombre de contacto.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#contacto_nombre").focus();
				validacion = false;
				return false;
			}

			if (telefono == "" && movil == "" && fax == "" && correo == ""){
				Swal.fire({
					icon: "error",
					title: "Complete la información requerida.",
					html: "<b class='color-red'>Debe ingresar al menos un medio de contacto (telefono, movil, fax o correo).</b>",
					showConfirmButton: true,
					timer: 4000
				});
				validacion = false;
				return false;
			}

			if (contacto == ""){
				$("#contacto_empresa").val( $("#btn-contactoempresa").val() );
				$("#contacto_persona").val( $("#btn-contactopersona").val() );

				if ( $("#contacto_empresa").val() == "" && $("#contacto_persona").val() == "" ){
					Swal.fire({
						icon: "error",
						title: "No hay proveedor/proveedor seleccionado.",
						html: "<b class='color-red'>Cierre el formulario de contactos e intente ingresar nuevamente.</b>",
						showConfirmButton: true,
						timer: 4000
					});
				}
			}

			if (validacion == true){
				var dataForm = $("#formContacto").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (contacto == "")
								titulo = "¡Registro exitoso!";
							else
								titulo = "¡Actualización exitosa!";

							Swal.fire({
								icon: "success",
								title: titulo,
								showConfirmButton: true,
								timer: 2000
							});

							clean_contacto();
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
						getTableContactos();
					}
				});
			}
		}
	});
}

function deshabilitar_contacto(id){
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
			var url = base_url + "index.php/empresa/empresa/deshabilitar_contacto";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					contacto: id
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
					getTableContactos();
				}
			});
		}
	});
}

function clean_contacto(){
	$("#contacto").val("");
	$("#contacto_empresa").val("");
	$("#contacto_persona").val("");
	$("#formContacto")[0].reset();
}

/* END CONTACTOS */

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
			if (inputProv == "" && getDist == true)
				getDistritos();
			else
				if (getDist == true)
					getDistritos(null, null, null, "#establecimiento_departamento", "#establecimiento_provincia", "#establecimiento_distrito");
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

function getSunat(){
	if ( $("#numero_documento").val() != "" ){
		var url = base_url + "index.php/empresa/proveedor/search_documento";
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'json',
			data:{
				numero: $("#numero_documento").val()
			},
			beforeSend: function(){
				$('.btn-search-sunat').hide("fast");
				$(".icon-loading-lg").show("slow");

				$("#nombres").val("");
				$("#apellido_paterno").val("");
				$("#apellido_materno").val("");

				$("#razon_social").val("");
				$("#direccion").val("");
			},
			success: function(data){
				if (data.exists == false) {
					if (data.match == true){
						info = data.info;

						show_tipoProveedor(data.tipo_proveedor);
						$("#idproveedor").val(data.id_proveedor);

						if (data.tipo_proveedor == 0){
							$("#nombres").val(info.nombre);
							$("#apellido_paterno").val(info.paterno);
							$("#apellido_materno").val(info.materno);

							if (info.sexo == "Masculino")
								$("#genero").val("0");
							if (info.sexo == "Femenino")
								$("#genero").val("1");
						}
						else{
							$("#razon_social").val(info.razon_social);
							$("#direccion").val(info.direccion);

							dpto = info.ubigeo.substr(0,2);
							prov = info.ubigeo.substr(2,2);
							dist = info.ubigeo.substr(4,2);

							$("#departamento").val(dpto);

							getProvincias(dpto, prov, "", "", false);
							getDistritos(dpto, prov, dist);
						}
					}
					else{
						Swal.fire({
							icon: "info",
							title: "¡Algo ha ocurrido!",
							html: "<b class='color-red'>" + data.message + "</b>",
							showConfirmButton: true,
							timer: 6000
						});
					}
				}
				else{
					Swal.fire({
						icon: "info",
						title: "¡Algo ha ocurrido!",
						html: "<b class='color-red'>" + data.message + "</b>",
						showConfirmButton: true,
						timer: 6000
					});
				}
			},
			complete: function(){
				$(".icon-loading-lg").hide("fast");
				$('.btn-search-sunat').show("fast");
			}
		});
	}
}

/* DOCUMENTOS EMITIDOS */
function docs_emitidos( id , ndoc = '', razon_social = '' ){
	$(".titleRuc").html(ndoc);
	$(".titleRazonSocial").html(razon_social);
	$("#modal_documentos").modal("toggle");


	$('#table-documentos').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/empresa/proveedor/docs_emitidos/',
			type: "POST",
			data: {
				proveedor: id,
			},
			beforeSend: function(){
				$(".table-documentos").show();
			},
			error: function(){
			},
			complete: function(){
				$(".table-documentos").hide();
			}
		},
		language: spanish,
		columnDefs: [
		{"className": "dt-right", "targets": 4},
		{"className": "dt-right", "targets": 5},
		{"className": "dt-right", "targets": 6}
		],
		order: [[ 2, "desc" ]]
	});
}