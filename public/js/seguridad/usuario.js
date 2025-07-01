/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function(){
	$('#table-usuarios').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url :  base_url + 'index.php/seguridad/usuario/datatable_usuarios/',
			type: "POST",
			data: { dataString: "" },
			beforeSend: function(){
				$("#table-usuarios .loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$("#table-usuarios .loading-table").hide();
			}
		},
		language: "spanish",
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

	$('#searchNombres, #searchUsuario, #searchRol').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});

	$("#persona").change(function(){
		getUsuarioPersona( $(this).val() );
	});

	table_establecimientos();
        
    $('#cerrarUsuario').click(function(){
        parent.$.fancybox.close(); 
    });         
        
  $("#verificarUsuario").click(function(){
        
             var comprobante = $("#comprobante").val();
             var RolUsuario = $("#rolinicio").val();
             var tipo_oper = $("#txtoper").val();
             var tipo_docu = $("#txtdocu").val();

             var txtClave = $("#txtClave").val();
             var txtUsuario = $("#txtUsuario").val();
             var motivoAnulacion = $("#motivoAnulacion").val();

             if(txtUsuario==''){
                alert('Ingrese el usuario');
                return false;
            }
            else{
                if(txtClave==''){
                    alert('Ingrese la clave');
                    return false;
                }
                else{
                    
                    url = base_url + "index.php/seguridad/usuario/confirmacion_usuario_anulafb/"+tipo_docu+"/"+comprobante;

                    $.ajax({
                        type: "POST",
                        async: false,
                        url: url,
                        data: {comprobante:comprobante,txtUsuario:txtUsuario,txtClave:txtClave,txtRol:RolUsuario,motivo:motivoAnulacion},
                        beforeSend: function (data) {
                        },
                        error: function (data) {
                            alert('No se puedo completar la operacion - Revise los campos ingresados.')
                        },
                        success: function (data) {
                            $("#frmBusqueda").html(data);
                            if ( $("#refresh").length > 0 ){
                                alert("Anulacion exitosa.");
                                parent.location.reload();
                            }
                        }
                    });
                }
            }
    });
    
});

function search( search = true){
	if (search == true){
		searchNombres = $("#searchNombres").val();
		searchUsuario = $("#searchUsuario").val();
		searchRol = $("#searchRol").val();
	}
	else{
		$("#searchNombres").val("");
		$("#searchUsuario").val("");
		$("#searchRol").val("");

		searchNombres = "";
		searchUsuario = "";
		searchRol = "";
	}

	$('#table-usuarios').DataTable({
		responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url :  base_url + 'index.php/seguridad/usuario/datatable_usuarios/',
			type: "POST",
			data: {
				txtNombres: searchNombres,
				txtUsuario: searchUsuario,
				txtRol: searchRol
			},
			beforeSend: function(){
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: "spanish",
		order: [[ 0, "asc" ]]
	});
}

function getUsuarioPersona(persona = ""){
	if( persona != "" ){
		url = base_url + "index.php/seguridad/usuario/getPersonaUsuario";
		$.ajax({
			type: "POST",
			url: url,
			data: {
				persona: persona
			},
			dataType: "json",
			beforeSend: function(data){
				clean();
			},
			success: function(data){
				$("#persona").val(persona);
				if (data.match == true){
					$('#txtNombres').val(data.info.nombres);
					$("#txtPaterno").val(data.info.apellido_paterno);
					$("#txtMaterno").val(data.info.apellido_materno);

					$("#usuario").val(data.info.usuario);
					$('#txtUsuario').val(data.info.nombre_usuario);
					if ( data.info.usuario == null )
						$('#txtUsuario').removeAttr("readonly");
					else{
						$('#txtUsuario').attr('readonly','readonly');
						$.each(data.info.acceso, function(i,item){
							$(".establecimientos-rol_"+item.establecimiento).val(item.rol);
							$(".establecimientos-acceso_"+item.establecimiento).val(1);
						});
					}
				}
				else{
					$('#txtUsuario').removeAttr('readonly');
					$('#txtClave').removeAttr('readonly');
					$('#txtClave2').removeAttr('readonly');

					$('#txtUsuario').val("");
					$('#txtClave').val("");
					$('#txtClave2').val("");
				}
			},
			complete: function(){
			}
		});
	}
	else{
		clean();
	}
}

function editar_usuario(persona){
	getUsuarioPersona(persona);
	$("#add_usuario").modal("toggle");
}

function table_establecimientos(){
	$('#establecimientos-table').DataTable({ responsive: true,
		autoWidth: false,
		filter: false,
		destroy: true,
		language: spanish
	});
}

function registrar_usuario(){
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
			var usuario = $("#usuario").val();
			var username = $("#txtUsuario").val();
			validacion = true;

			if (usuario == "" && $("#txtClave").val().length < 5 || usuario != "" && $("#txtClave").val() != "" && $("#txtClave").val().length < 5 ){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe ingresar una contraseña valida.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#txtClave").focus();
				validacion = false;
				return null;
			}

			if ( $("#txtClave").val() != $("#txtClave2").val() ){
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Las contraseñas ingresadas no coinciden.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#txtClave2").focus();
				validacion = false;
				return null;
			}

			expr = /[a-zA-Z0-9._\-]{3,15}$/
			if ( expr.test(username) == true ){
				url = base_url+"index.php/seguridad/usuario/buscar_nombre_usuario";
				$.ajax({
					url: url,
					type: "POST",
					dataType: 'json',
					data: { username: username },
					beforeSend: function(data) {

					},
					success: function(data){
						if ( data.match == true && usuario == "" ){
							Swal.fire({
								icon: "error",
								title: "Verifique los datos ingresados.",
								html: "<b class='color-red'>El nombre de usuario " + username + " no esta disponible.</b>",
								showConfirmButton: true,
								timer: 4000
							});
							$("#txtUsuario").focus();
							validacion = false;
							return null;
						}
					}
				});
			}
			else{
				Swal.fire({
					icon: "error",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>El nombre de usuario " + username + ". no esta permitido.</b>",
					showConfirmButton: true,
					timer: 4000
				});

				$("#txtUsuario").focus();
				validacion = false;
				return null;
			}

			if (validacion == true){
				var url = base_url + "index.php/seguridad/usuario/guardar_registro";
				$("#establecimientos-table").DataTable().destroy();
				var dataForm = $("#formUsuario").serialize();
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: dataForm,
					success: function(data){
						if (data.result == "success") {
							if (usuario == "")
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
						table_establecimientos();
					}
				});
			}
		}
	});
}

function deshabilitar(usuario){
	Swal.fire({
		icon: "info",
		title: "¿Esta seguro de eliminar el acceso del usuario seleccionado?",
		html: "<b class='color-red'></b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var url = base_url + "index.php/seguridad/usuario/deshabilitar_usuario";
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				data: {
					usuario: usuario
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

function ver_usuario(usuario){
	url = base_url + "index.php/seguridad/usuario/getUsuario";
	$.ajax({
		type: "POST",
		url: url,
		data: {
			usuario: usuario
		},
		dataType: "json",
		beforeSend: function(data){
			$('.data-nombres').html("");
			$(".data-apellidop").html("");
			$(".data-apellidom").html("");
			$(".data-usuario").html("");
			$('#table-view-accesos').DataTable().destroy();
			$(".info-accesos").html("");
		},
		success: function(data){
			if (data.match == true){
				$('.data-nombres').html(data.info.nombres);
				$(".data-apellidop").html(data.info.apellido_paterno);
				$(".data-apellidom").html(data.info.apellido_materno);

				$(".data-usuario").html(data.info.nombre_usuario);

				$.each(data.info.acceso, function(i,item){
					tr = "<tr>";
					tr += "<td>"+item.empresa+"</td>";
					tr += "<td>"+item.establecimiento+"</td>";
					tr += "<td>"+item.rol+"</td>";
					tr += "</tr>";
					$(".info-accesos").append(tr);
				});
			}
		},
		complete: function(){
			$('#table-view-accesos').DataTable({
				responsive: true,
				filter: false,
				destroy: true,
				autoWidth: false,
				language: spanish
			});
			$("#view_user").modal("toggle");
		}
	});
}

function clean(){
	$("#formUsuario")[0].reset();
	$("#usuario").val("");
}