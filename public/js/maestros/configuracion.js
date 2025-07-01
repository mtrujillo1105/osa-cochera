/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$("#guardarPrecios").click(function(){
		Swal.fire({
			icon: "question",
			title: "¿Esta seguro de guardar la configuración?",
			html: "<b class='color-red'></b>",
			showConfirmButton: true,
			showCancelButton: true,
			confirmButtonText: "Aceptar",
			cancelButtonText: "Cancelar"
		}).then(result => {
			if (result.value){
				validacion = true;
				var igv = $("#igv").val();

				if (igv.trim() == ""){
					Swal.fire({
						icon: "warning",
						title: "Verifique los datos ingresados.",
						html: "<b class='color-red'>Debe agregar el IGV.</b>",
						showConfirmButton: true,
						timer: 4000
					});
					$("#igv").focus();
					validacion = false;
					return null;
				}

				if (validacion == true){
					var url = base_url + "index.php/maestros/configuracion/guardar_articulos_cfg";
					var info = $("#formArticulos").serialize();

					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: info,
						beforeSend: function(){
						},
						success: function(data){
							if (data.result == "success") {
								titulo = "¡Actualización exitosa!";
								Swal.fire({
									icon: "success",
									title: titulo,
									showConfirmButton: true,
									timer: 2000
								});
							}
							else{
								Swal.fire({
									icon: data.result,
									title: "Sin cambios.",
									html: "<b class='color-red'>"+data.message+"</b>",
									showConfirmButton: true,
									timer: 4000
								});
							}
						},
						complete: function(){
						}
					});
				}
			}
		});
	});

	$("#guardarSeries").click(function(){
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
				validacion = true;


				$("#formSeries input").each(function(){
					if ( $(this).val().trim() == '' ){
						Swal.fire({
							icon: "warning",
							title: "Verifique los datos ingresados.",
							html: "<b class='color-red'>Existen campos vacios.</b>",
							showConfirmButton: true,
							timer: 4000
						});
						validacion = false;
					}
				});

				if (validacion == true){
					var url = base_url + "index.php/maestros/configuracion/guardar_series_cfg";
					var info = $("#formSeries").serialize();
					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: info,
						success: function(data){
							if (data.result == "success") {
								titulo = "¡Actualización exitosa!";

								Swal.fire({
									icon: "success",
									title: titulo,
									showConfirmButton: true,
									timer: 2000
								});
							}
							else{
								Swal.fire({
									icon: data.result,
									title: "Sin cambios.",
									html: "<b class='color-red'>"+data.message+"</b>",
									showConfirmButton: true,
									timer: 4000
								});
							}
						},
						complete: function(){
						}
					});
				}
			}
		});
	});
	
	$("#formArticulos input").keyup(function(){
		if ( $(this).val().trim() == '' ){
			$(this).removeClass('is-valid');
			$(this).addClass('is-invalid');
		}
		else{
			$(this).removeClass('is-invalid');
			$(this).addClass('is-valid');
		}
	});

	$("#formSeries input").keyup(function(){
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