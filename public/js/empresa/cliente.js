/* *********************************************************************************
/* ******************************************************************************** */

$(document).ready(function(){
    var tipo_clienteabonado = $("#tipo_clienteabonado").val();
    var categoria = $("#categoria").val();
    
    //Muestro datos de facturación al crea nuevo
    if(categoria == 8){
        $(".datos_tacturacion").show();
    }
    else{
        $(".datos_tacturacion").hide();
    }
    
    $('#table-cliente').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : base_url + 'index.php/empresa/cliente/datatable_cliente/',
                    type: "POST",
                    data: { tipo_clienteabonado: tipo_clienteabonado },
                    beforeSend: function(){
                            $("#table-cliente .loading-table").show();
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
            order: [[ 3, "asc" ]]
    });

    $("#buscar").click(function(){
            search();
    });

    $("#limpiar").click(function(){
            search(false);
    });
        
    $("#nuevo").click(function () {
        $("#tipo_cliente").val('0');
        $("#tipo_cliente option[value='0']").attr('selected',true);
        $('#modal_addcliente').modal('toggle');
        clean();
    });        

    $('#form_busqueda').keypress(function(e){
        if ( e.which == 13 ){
            return false;
        } 
    });

    $("#formCliente").keypress(function(e){
        if ( e.which == 13 ){
            registrar_cliente();
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

    $('#search_codigo, #search_documento, #nombre_cliente').keyup(function(e){
        if ( e.which == 13 ){
            if( $(this).val() != '' )
                    search();
        }
    });

    $("#tipo_cliente").change(function(){
            show_tipoCliente( parseInt($(this).val()) );
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

    $("#idcliente").click(function(){
            if ($("#cliente").val() == ""){
                    var url = base_url + "index.php/empresa/cliente/generateCodeCliente";
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
                                            $("#idcliente").val(data.code);
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

    $(".btn-search-sunat-vehiculo").click(function(){
            getSunatVehiculo();
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

    $(".btn-addVehiculo").click(function(){
            clean_vehiculo();
            $("#modal_addvehiculo").modal("toggle");
    });        
        
    $("#facturar_abonado").click(function(){
        var cliente      = $("#documento_cliente").val();
        var tipo_persona = $("#documento_tipo_persona").val();
        var url = base_url + "index.php/ventas/comprobante/create_comprobante_abonado";
        var tipo_docu;

        if(tipo_persona == 0)
            tipo_docu = 'B';
        else
            tipo_docu = 'F';

        $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data:{
                    cliente: cliente, tipo_docu : tipo_docu
                },
                beforeSend: function(){
                },
                success: function(data){
                        if (data.match) {
                            Swal.fire({
                                    icon: "success",
                                    title: "Registro exitoso",
                                    showConfirmButton: true,
                                    timer: 2000
                            });
                            $("#facturar_abonado").hide();
                        }
                        else{
                            var mensaje = data.message;
                            Swal.fire({
                                    icon: "info",
                                    title: mensaje,
                                    html: "<b class='color-red'></b>",
                                    showConfirmButton: true,
                                    timer: 4000
                            });
                        }
                },
                complete: function(){
                    search();
                    getTableDocumentos(cliente);
                }
        });            
    });
        
    $("#categoria").change(function(){
        var categoria = $(this).val();
        if(categoria == 8){
            $(".datos_tacturacion").show();
        }
        else{
            $(".datos_tacturacion").hide();
        }
    });
    
    $("#vehiculo_placa").blur(function(){
        let placa = $(this).val();
        let cliente = $("#vehiculo_cliente").val();
        let url   = base_url + "index.php/empresa/cliente/getVehiculoXPlacaTotal";
        if(placa.length > 3){
            $.ajax({
                type     : 'POST',
                url      : url,
                dataType : 'json',
                data     : {
                    placa : placa, cliente : cliente
                },
                beforeSend :function(){
                },
                success : function(data){
                    if(data.result){
                        
                        Swal.fire({
                            icon: "error",
                            title: "Sin cambios.",
                            html: "<b class='color-red'>" + data.message + "</b>",
                            showConfirmButton: true,
                            timer: 4000                            
                        }); 
                        
                        $("#vehiculo_placa").val('');
                        $("#vehiculo_placa").focus();
                        
                    }
                },
                complete : function(){
                }
            });
        }
    });

});

/* CLIENTE */
    function search( search = true){

        var tipo_clienteabonado = $("#tipo_clienteabonado").val();

        if (search == true){
                codigo    = $("#search_codigo").val();
                documento = $("#search_documento").val();
                nombre    = $("#nombre_cliente").val();
                placa     = $("#search_placa").val();
        }
        else{
                $("#search_codigo").val("");
                $("#search_documento").val("");
                $("#nombre_cliente").val("");
                $("#search_placa").val("");

                codigo    = "";
                documento = "";
                nombre    = "";
                placa     = "";
        }

            $('#table-cliente').DataTable({ responsive: true,
                    filter: false,
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax:{
                            url : base_url + 'index.php/empresa/cliente/datatable_cliente/',
                            type: "POST",
                            data: {
                                    codigo: codigo,
                                    documento: documento,
                                    nombre: nombre,
                                    tipo_clienteabonado : tipo_clienteabonado,
                                    placa : placa
                            },
                            beforeSend: function(){
                                    $("#table-cliente .loading-table").show();
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
                    order: [[ 3, "asc" ]]
            });
    }

	function editar_cliente(id){
            var url = base_url + "index.php/empresa/cliente/getCliente";
            $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data:{
                        cliente: id
                    },
                    beforeSend: function(){
                        clean();
                        $(".divJuridico").hide();
                        $(".divNatural").hide();
                    },
                    success: function(data){
                        if (data.match == true) {
                            info = data.info;

                            show_tipoCliente(info.tipo_cliente);

                            $("#cliente").val(info.cliente);

                            doc = "DOC" + info.tipo_cliente;

                            $("#tipo_cliente").val(info.tipo_cliente);
                            
                            //Deshabilitamos y ocultamos campos no seleccionados del combo tipo_cliente
                            $('#tipo_cliente option:not(:selected)').attr('disabled',true);
                            $('#tipo_cliente option:not(:selected)').hide();                            
                            
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

                            $("#idcliente").val(info.idcliente);

                            if (info.vendedor != null)
                                    $("#vendedor").val(info.vendedor);

                            if (info.sector_comercial != null)
                                    $("#sector_comercial").val(info.sector_comercial);

                            if (info.forma_pago != null)
                                    $("#forma_pago").val(info.forma_pago);

                            if (info.categoria != null){
                                $("#categoria").val(info.categoria);
                                
                                //Mostramos datos de facturación
                                if(info.categoria == 8){
                                    $(".datos_tacturacion").show();
                                }
                                else{
                                    $(".datos_tacturacion").hide();
                                }
                                
                            }
                                    

                            $("#fecha_ingreso_cliente").val(info.fecha_ingreso);
                            $("#monto_facturado").val(info.monto_facturado);
                            $("#telefono").val(info.telefono);
                            $("#movil").val(info.movil);
                            $("#fax").val(info.fax);
                            $("#correo").val(info.correo);
                            $("#web").val(info.web);
                            $("#modal_addcliente").modal("toggle");
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
                    search();
                }
        });
	}

	function registrar_cliente(){
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
                    var url = base_url + "index.php/empresa/cliente/guardar_registro";

                    cliente         = $("#cliente").val();
                    tipo_cliente    = $("#tipo_cliente").val();
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
                    idcliente       = $("#idcliente").val();
                    vendedor        = $("#vendedor").val();
                    sector_comercial = $("#sector_comercial").val();
                    forma_pago      = $("#forma_pago").val();
                    categoria       = $("#categoria").val();
                    telefono        = $("#telefono").val();
                    movil           = $("#movil").val();
                    fax             = $("#fax").val();
                    correo          = $("#correo").val();
                    web             = $("#web").val();
                    fecha_pago      = $("#fecha_ingreso_cliente").val();
                    monto_facturado = $("#monto_facturado").val();
                    forma_pago      = $("#forma_pago").val();

                    validacion = true;

                    if (tipo_cliente == "1"){
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
                    
                    if(categoria == 8){
                        
                        if(fecha_pago == "" || monto_facturado == "" || forma_pago == "" || forma_pago == null){
                            Swal.fire({
                                icon: "error",
                                title: "Verifique los datos ingresados.",
                                html: "<b class='color-red'>Debe ingresar los datos de facturación.</b>",
                                showConfirmButton: true,
                                timer: 4000
                            });
                            $("#fecha_ingreso_cliente").focus();
                            validacion = false;
                            return false;
                        }

                    }

                    if (validacion == true){
                            var dataForm = $("#formCliente").serialize();
                            $.ajax({
                                    type: 'POST',
                                    url: url,
                                    dataType: 'json',
                                    data: dataForm,
                                    success: function(data){
                                            if (data.result == "success") {
                                                    if (cliente == "")
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
                                        search();
                                        //$("#numero_documento").focus();
                                        $("#modal_addcliente").modal("hide");
                                    }
                            });
                    }
                }
            });
	}

	function show_tipoCliente( id = null ){
		if (id == null)
			id = parseInt( $("#tipo_cliente").val() );
		else
			$("#tipo_cliente").val(id);

		$(".divNatural").removeAttr("hidden");

		if ( id == 0 ){
			$(".divJuridico").hide("fast");
			$(".divNatural").show("fast");

			$(".documentosJuridico").attr({ "disabled": "disabled" });
			$(".DOC1").removeAttr("selected");


			$(".documentosNatural").removeAttr("disabled");
			$(".DOC0").first().attr({"selected":"selected"});
		}
		else if ( id == 1 ){
			$(".divNatural").hide("fast");
			$(".divJuridico").show("fast");

			$(".documentosNatural").attr({ "disabled": "disabled" });
			$(".DOC0").removeAttr("selected");

			$(".documentosJuridico").removeAttr("disabled");
			$(".DOC1").first().attr({"selected":"selected"});
		}
	}

	function clean( id = null ){
		$("#cliente").val("");
		$("#formCliente")[0].reset();

		show_tipoCliente( id );
		getProvincias('15','01', '', '', false);
		getDistritos('15','01','01');
	}

	function deshabilitar_cliente(id){
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
				var url = base_url + "index.php/empresa/cliente/deshabilitar_cliente";
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: {
						cliente: id
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
/* END CLIENTE */

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
							title: "No hay cliente/proveedor seleccionado.",
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
                                $("#contacto_placa").val(info.placa);
                                $("#contacto_tarifa").val(info.tarifa);
                                $("#contacto_fechai").val(info.fechai);
                                $("#contacto_monto").val(info.monto);
                                $("#contacto_numerodoc").val(info.numerodoc);
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
                var empresa  = $("#contacto_empresa").val();
                var persona  = $("#contacto_persona").val();
                var nombre   = $("#contacto_nombre").val();
                var telefono = $("#contacto_telefono").val();
                var movil    = $("#contacto_movil").val();
                var fax      = $("#contacto_fax").val();
                var correo   = $("#contacto_correo").val();
                var placa    = $("#contacto_placa").val();
                var tarifa   = $("#contacto_tarifa").val();
                var fechai   = $("#contacto_fechai").val();
                var monto    = $("#contacto_monto").val();
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
                                title: "No hay cliente/proveedor seleccionado.",
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


/* VEHICULOS */

	function modal_vehiculos( cliente = null,razon_social = "" ){

		$("#modal_vehiculo").modal("toggle");
		title = razon_social.split("-");
		$(".titleRuc").html(title[0]);
		$(".titleRazonSocial").html(title[1]);
		$("#btn-vehiculocliente").val(cliente);
                $("#vehiculocliente").val(cliente);

		getTableVehiculos();
	}

	function getTableVehiculos(){
		$('#table-vehiculos').DataTable({ responsive: true,
			filter: false,
			destroy: true,
			processing: true,
			serverSide: true,
			autoWidth: false,
			ajax:{
				url : base_url + 'index.php/empresa/cliente/datatable_vehiculos',
				type: "POST",
				data: {
					cliente: $("#btn-vehiculocliente").val(),
				},
				beforeSend: function(){
					$("#table-vehiculos .loading-table").show();
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

	function editar_vehiculo( id ){
            var url = base_url + "index.php/empresa/cliente/getVehiculo";
            $.ajax({
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data:{
                        vehiculo: id
                    },
                    beforeSend: function(){
                        clean_vehiculo();
                        $("#modal_addvehiculo").modal("toggle");
                    },
                    success: function(data){
                            if (data.match == true) {
                                info = data.info;
                                $("#vehiculo").val(info.vehiculo);
                                $("#vehiculo_cliente").val(info.cliente);
                                $("#vehiculo_nombre").val(info.nombre);
                                $("#vehiculo_telefono").val(info.telefono);
                                $("#vehiculo_movil").val(info.movil);
                                $("#vehiculo_placa").val(info.placa);
                                $("#vehiculo_tarifa").val(info.tarifa);
                                $("#vehiculo_numerodoc").val(info.numerodoc);
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

    function registrar_vehiculo(){
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
                var url = base_url + "index.php/empresa/cliente/guardar_vehiculo";
                var vehiculo = $("#vehiculo").val();
                var cliente  = $("#vehiculo_cliente").val();
                var nombre   = $("#vehiculo_nombre").val();
                var telefono = $("#vehiculo_telefono").val();
                var movil    = $("#vehiculo_movil").val();
                var placa    = $("#vehiculo_placa").val();
                var tarifa   = $("#vehiculo_tarifa").val();
                var numero   = $("#vehiculo_numerodoc").val();
                validacion = true;
                
                if (placa == ""){
                    Swal.fire({
                            icon: "error",
                            title: "Verifique los datos ingresados.",
                            html: "<b class='color-red'>Debe ingresar una placa para el vehiculo.</b>",
                            showConfirmButton: true,
                            timer: 4000
                    });
                    $("#vehiculo_placa").focus();
                    validacion = false;
                    return false;
                }  
                
               if (numero == ""){
                    Swal.fire({
                            icon: "error",
                            title: "Verifique los datos ingresados.",
                            html: "<b class='color-red'>Debe ingresar un numero de documento.</b>",
                            showConfirmButton: true,
                            timer: 4000
                    });
                    $("#vehiculo_numerodoc").focus();
                    validacion = false;
                    return false;
                }      
                
                if (nombre == ""){
                    Swal.fire({
                            icon: "error",
                            title: "Complete la información requerida.",
                            html: "<b class='color-red'>Debe ingresar un conductor para el vehiculo.</b>",
                            showConfirmButton: true,
                            timer: 4000
                    });
                    $("#vehiculo_nombre").focus();
                    validacion = false;
                    return false;
                }                
                
                if (tarifa == ""){
                    Swal.fire({
                            icon: "error",
                            title: "Verifique los datos ingresados.",
                            html: "<b class='color-red'>Debe ingresar una tarifa para el vehiculo.</b>",
                            showConfirmButton: true,
                            timer: 4000
                    });
                    $("#vehiculo_tarifa").focus();
                    validacion = false;
                    return false;
                }                 

                if (vehiculo == ""){
                    $("#vehiculo_cliente").val( $("#btn-vehiculocliente").val() );

                    if ( $("#vehiculo_cliente").val() == ""){
                        Swal.fire({
                                icon: "error",
                                title: "No hay cliente seleccionado.",
                                html: "<b class='color-red'>Cierre el formulario de contactos e intente ingresar nuevamente.</b>",
                                showConfirmButton: true,
                                timer: 4000
                        });
                    }
                }

                if (validacion == true){
                    var dataForm = $("#formVehiculo").serialize();
                    $.ajax({
                            type: 'POST',
                            url: url,
                            dataType: 'json',
                            data: dataForm,
                            success: function(data){
                                if (data.result == "success") {
                                    
                                    Swal.fire({
                                            icon: "success",
                                            title: data.message,
                                            showConfirmButton: true,
                                            timer: 2000
                                    });
                                    
                                    //Cerramos modal
                                     $("#modal_addvehiculo").modal("hide");                                    

                                    clean_contacto();
                                }
                                else{
                                    Swal.fire({
                                            icon: "error",
                                            title: "Sin cambios.",
                                            html: "<b class='color-red'>" + data.message + "</b>",
                                            showConfirmButton: true,
                                            timer: 4000
                                    });
                                    
                                    //Cerramos modal
                                     $("#modal_addvehiculo").modal("hide");     
                                }
                            },
                            complete: function(){
                                clean_vehiculo();
                                getTableVehiculos();
                            }
                    });
                }
            }
        });
    }

    function deshabilitar_vehiculo(id){
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
                        var url = base_url + "index.php/empresa/cliente/deshabilitar_vehiculo";
                        $.ajax({
                                type: 'POST',
                                url: url,
                                dataType: 'json',
                                data: {
                                    vehiculo: id
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
                                    getTableVehiculos();
                                }
                        });
                    }
            });
    }

    function clean_vehiculo(){
        $("#vehiculo").val("");
        $("#vehiculo_empresa").val("");
        $("#vehiculo_persona").val("");
        $("#formVehiculo")[0].reset();
    }

/* END VEHICULOS */

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
function getSunatVehiculo(){
    if($("#vehiculo_numerodoc").val() != ""){
        var url = base_url + "index.php/empresa/cliente/search_documento/false";
        $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data:{
                    numero: $("#vehiculo_numerodoc").val()
                },
                beforeSend: function(){
                    $('.btn-search-sunat-vehiculo').hide("fast");
                    $(".icon-loading-lg").show("slow");
                    $("#vehiculo_nombre").val("");
                },
                success: function(data){
                    if (data.exists == false && data.match == true) {
                        info = data.info;
                        if (data.tipo_cliente == 0){
                            $("#vehiculo_nombre").val(info.nombre+' '+info.paterno+' '+info.materno);
                        }
                    }
                    else{
                        var mensaje = data.message;
                        Swal.fire({
                                icon: "info",
                                title: mensaje,
                                html: "<b class='color-red'>No existen datos</b>",
                                showConfirmButton: true,
                                timer: 6000
                        });
                        $("#vehiculo_numerodoc").val("");
                    }
                    $('.btn-search-sunat-vehiculo').show("fast");                        
                },
                complete: function(){
                    $(".icon-loading-lg").hide("fast");
                    $('.btn-search-sunat').show("fast");
                }
        });
    }
}

function getSunat(){
	if ( $("#numero_documento").val() != "" ){
		var url = base_url + "index.php/empresa/cliente/search_documento";
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

						show_tipoCliente(data.tipo_cliente);
						$("#idcliente").val(data.id_cliente);

						if (data.tipo_cliente == 0){
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

function docs_emitidos( id , ndoc = '', razon_social = '',situacion='',tipo_persona = '' ){
	$(".titleRuc").html(ndoc);
	$(".titleRazonSocial").html(razon_social);
	$("#modal_documentos").modal("toggle");
        $("#documento_cliente").val(id);
        $("#documento_tipo_persona").val(tipo_persona);
        
        if(situacion == 0)
            $("#facturar_abonado").show();    
        else
            $("#facturar_abonado").hide();
        
        getTableDocumentos(id);

}

function getTableDocumentos(cliente){
    
    $('#table-documentos').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : base_url + 'index.php/empresa/cliente/docs_emitidos/',
                    type: "POST",
                    data: {
                        cliente: cliente,
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