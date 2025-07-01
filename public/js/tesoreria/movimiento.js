jQuery(document).ready(function(){
    $("#botonGeneral").hide();
    $("#justificacionobservacion").hide();
	
    $('#table-movimiento').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
				url : base_url + 'index.php/tesoreria/movimiento/datatable_movimiento/',
				type: "POST",
				data: { dataString: "" },
				beforeSend: function(){
					$("#table-movimiento .loading-table").show();
				},
				error: function(){
				},
				complete: function(){
					$("#table-movimiento .loading-table").hide();
				}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 1, "desc" ]]
	});	
	
    $('#table-cajacierre').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
				url : base_url + 'index.php/tesoreria/movimiento/datatable_cajacierre/',
				type: "POST",
				data: { dataString: "" },
				beforeSend: function(){
					$("#table-cajacierre .loading-table").show();
				},
				error: function(){
				},
				complete: function(){
					$("#table-cajacierre .loading-table").hide();
				}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 1, "desc" ]]
	});	
	
	$(".pdfMovimientos").click(function(){
		codigo = $("#search_codigo").val();
		fechai = $("#search_fechai").val();
		fechaf = $("#search_fechaf").val();

		if ( codigo != "" && fechai != "" && fechaf != "" ){
			url = base_url + "index.php/tesoreria/movimiento/resumen_movimientos_pdf/" + codigo + "/" + fechai + "/" + fechaf;
			$.fancybox.open({
				src: url,
				type: 'iframe'
			});
		}
		else{
			Swal.fire({
					icon: "info",
					title: "Debe completar los filtros",
					html: "<b class='color-red'>Debe seleccionar la caja, fecha de inicio y fecha de fin.</b>",
					showConfirmButton: true,
					timer: 4000
			});
		}
	});	
	
    $("#buscarC").click(function(){
		search();
	});

	$("#limpiarC").click(function(){
		search(false);
	});

	$('#form_busqueda').keypress(function(e){
		if ( e.which == 13 ){
			return false;
		} 
	});

	$('#search_codigo, #search_descripcion').keyup(function(e){
		if ( e.which == 13 ){
			if( $(this).val() != '' )
				search();
		}
	});	
		
    $("#list_grupo").click(function(){
    	alert("hola mundo");
    });      
    $("#imgGuardarProyecto").click(function(){
		dataString = $('#frmProyecto').serialize();
		$("#container").show();
		$("#frmProyecto").submit();
    });
 
    $("#buscarMovimiento").click(function(){
    	var codigonombrecaja = $("#cboNombreCaja").val();    	
    	var estado = $("#estado").val();
    	
    	
    	if(codigonombrecaja == ""){
    		alert("Selecionar Nombre De Caja");
    		codigonombrecaja = "0";
    	}
    	
    	  url = base_url+"index.php/tesoreria/movimiento/buscarcaja_movimiento/"+codigonombrecaja+"/"+estado;
          location.href=url;
    });	
    
    $("#ingresoDineroMovimiento").click(function(){
    	var codigoCaja = $("#tdcajacodigo").val();
    	    	
    	var ingresoDinero = 1;
    	var tdcajamovi = $("#tdcajacodigo").val();
    		url = base_url+"index.php/tesoreria/movimiento/nuevo_movimiento/" + tdcajamovi + "/" + ingresoDinero + "/" + codigoCaja;
		$("#zonaContenido").load(url);
		

		
    });
    
    $("#ingresoSalidaMovimiento").click(function(){
    	var codigoCaja = $("#tdcajacodigo").val();
    	var salidaDinero = 2;
    	var tdcajamovi = $("#tdcajacodigo").val();
    		url = base_url+"index.php/tesoreria/movimiento/nuevo_movimiento/" + tdcajamovi + "/" + salidaDinero + "/" + codigoCaja;
		$("#zonaContenido").load(url);
    });
    
    $("#limpiarProyecto").click(function(){
        url = base_url+"index.php/maestros/proyecto/proyectos";
        location.href=url;
    });
    $("#imgCancelarProyecto").click(function(){
        base_url = $("#base_url").val();
        location.href = base_url+"index.php/tesoreria/caja/cajas";
    });
    
    $("#CancelarCajaMovimiento").click(function(){
    	base_url = $("#base_url").val();
    	location.href = base_url+"index.php/tesoreria/movimiento/movimientos";
    });
    $(":radio").click(function(){
        valor = $(this).attr("value");
        if(valor==0){//CAJA
            $("#datosBanco").hide();
            $("#datosCaja").show();
            $("#tabs-2").hide();
            $("#tabChequera").css("display","none");
            
        }
        else if(valor==1){//BANCOS
            $("#datosBanco").show();
            $("#datosCaja").hide();
            $("#tabs-2").show();
            $("#tabChequera").show();
            

        }
    });
    
//	container = $('div.container');
// 	$("#frmProyecto").validate({
//		event    : "blur",
//		rules    : {
//					'ruc'             : {required:true,minlength:11,number:true},
//					'razon_social'    : "required"
// 			    },
//		debug    : true,
//		errorContainer      : "container",
//		errorLabelContainer : $(".container"),
//		wrapper             : 'li',
//		submitHandler       : function(form){
//				dataString  = $('#frmProyecto').serialize();
//				modo        = $("#modo").val();
//				$('#VentanaTransparente').css("display","block");
//				if(modo=='insertar'){
//					url = base_url+"index.php/tesoreria/caja/insertar_cuenta";
//					$.post(url,dataString,function(data){
//					$("#VentanaTransparente").css("display","none");
//				alert('Se ha ingresado una Caja.');
//						location.href = base_url+"index.php/tesoreria/caja/cajas";
//					});
//				}
//				else if(modo=='modificar'){
//					url = base_url+"index.php/tesoreria/caja/modificar_caja";
//					$.post(url,dataString,function(data){
//						$("#VentanaTransparente").css("display","none");
//						alert('Su registro ha sido modificado.');
//						location.href = base_url+"index.php/tesoreria/caja/cajas";
//					});
//				}
//		}
//	});
//
//    container = $('div.container');	
    //Funcionalidades
    $("#nuevoRegistro").click(function(){
        opcion   = $("#opcion").val();
		proyecto  = $("#proyecto").val();
		
		modo     = $("#modo").val();
		img_url  = base_url+"system/application/views/images/";
		if(opcion==4){
			n = document.getElementById('tablaArea').rows.length/2;
			j = n+1;
			fila  = "<tr>";
			fila += "<td align='center'>"+j+"</td>";
			fila += "<td align='left'><input type='text' name='nombre_area["+n+"]' id='nombre_area["+n+"]' class='cajaGrande'></td>";
			if(modo=='modificar'){
				fila += "<td align='center'>&nbsp;</td>";
				fila += "<td align='center'><a href='#' onclick='insertar_area();'><img src='"+base_url+"images/save.gif' border='0'></a></td>";
				fila += "</tr>";
			}
			$("#tablaArea").append(fila);
		}
        else if(opcion==3){
			$("#msgRegistros").hide();		
			n = (document.getElementById('tablaContacto').rows.length);
			a = "contactoNombre["+n+"]";
			j = n+1;
			fila  = "<tr>";
			fila += "<td align='center'>"+n+"</td>";
			fila += "<td align='left' style='position:relative;'>";
			fila += "<input type='hidden' name='contactoPersona["+n+"]' id='contactoPersona["+n+"]' class='cajaMedia'>";
			fila += "<input type='text' name='contactoNombre["+n+"]' id='contactoNombre["+n+"]' class='cajaMedia' onfocus='ocultar_homonimos("+n+")'>";
			fila += "<a href='#' onclick='mostrar_homonimos("+n+");'><image src='"+base_url+"images/ver.png' border='0'></a>";
			fila += "<div id='homonimos["+n+"]' style='display:none;background:#ffffff;width:300px;border:1px solid #cccccc;height:40px;overflow:auto;position:absolute;z-index:1;'></div>";
			fila += "</td>";
			fila += "<td align='center'><select name='contactoArea["+n+"]' id='contactoArea["+n+"]' class='comboMedio' ><option value='0'>::Seleccionar::</option></select></td>";
			fila += "<td align='left'><select name='cargo_encargado["+n+"]' id='cargo_encargado["+n+"]' class='cajaMedia'><option value='0'>::Seleccione::</option></select></td>";
			fila += "<td align='left'><input type='text' name='contactoTelefono["+n+"]' id='contactoTelefono["+n+"]' class='cajaPequena'></td>";
			fila += "<td align='left'><input type='text' name='contactoEmail["+n+"]' id='contactoEmail["+n+"]' class='cajaPequena'></td>";
			if($('#proyecto_persona').val()!=''){
				fila += "<td align='center'>&nbsp;</td>";
				fila += "<td align='center'><a href='#' onclick='insertar_contacto("+n+");'><img src='"+base_url+"images/save.gif' border='0'></a></td>";
			}
                        else{
                            fila += "<td>&nbsp;</td>";
                            fila += "<td>&nbsp;</td>";
                        }
			fila += "</tr>";
			$("#tablaContacto").append(fila);
			document.getElementById(a).focus();
			listar_areas(n);
		}
		else if(opcion==2){
                        $("#msgRegistros2").hide();		
			n = document.getElementById('tablaSucursal').rows.length;
			a = "nombreSucursal["+n+"]";
			j = n+1;
			fila  = "<tr>";
			fila += "<td align='center'>"+n+"</td>";
			fila += "<td align='left'>";
			fila += "<input type='text' name='nombreSucursal["+n+"]' id='nombreSucursal["+n+"]' class='cajaMedia'>";
			fila += "<input type='hidden' name='proyectoSucursal["+n+"]' id='proyectoSucursal["+n+"]' class='cajaMedia' value='"+proyecto+"'>";
			fila += "</td>";
			fila += "<td align='left'><select name='tipoEstablecimiento["+n+"]' id='tipoEstablecimiento["+n+"]' class='comboMedio' ><option>::Seleccione::</option></select></td>";
			fila += "<td align='left'><input type='text' name='direccionSucursal["+n+"]' id='direccionSucursal["+n+"]' class='cajaGrande'></td>";
			fila += "<td align='left'>";
			fila += "<input type='hidden' name='dptoSucursal["+n+"]' id='dptoSucursal["+n+"]' class='cajaGrande' value='15'>";
			fila += "<input type='hidden' name='provSucursal["+n+"]' id='provSucursal["+n+"]' class='cajaGrande' value='01'>";
			fila += "<input type='hidden' name='distSucursal["+n+"]' id='distSucursal["+n+"]' class='cajaGrande'>";
			fila += "<input type='text' name='distritoSucursal["+n+"]' id='distritoSucursal["+n+"]' class='cajaPequena' readonly='readonly' onclick='abrir_formulario_ubigeo_sucursal("+n+");'/>";
			fila += "<a href='#' onclick='abrir_formulario_ubigeo_sucursal("+n+");'><image src='"+base_url+"images/ver.png' border='0'></a>";
			fila += "</td>";
			if($('#proyecto_persona').val()!=''){
				fila += "<td align='center'>&nbsp;</td>";
				fila += "<td align='center'><a href='#' onclick='insertar_sucursal("+n+");'><img src='"+base_url+"images/save.gif' border='0'></a></td>";
			}
                        else{
                            fila += "<td>&nbsp;</td>";
                            fila += "<td>&nbsp;</td>";
                        }
			fila += "</tr>";
			$("#tablaSucursal").append(fila);
			document.getElementById(a).focus();
			listar_tipoEstablecimientos(n);
		}
    });
//    $('#GuardarCajaMovimiento').click(function(){
//    	var codigClient = $("#cliente").val();
//    	var codigoTipoResponsable =$("#seleccionando").val(); 
//    	url = base_url+"index.php/tesoreria/movimiento/buscar_cajamovimiento/"+codigClient+"/"+codigoTipoResponsable;
//    	dataString = $('#frmcajamovimiento').serialize();
//    	$.post(url,dataString,function(data){
//    		
//    });	
//
//    });
    
});

function search( search = true){
	if (search == true){
		search_codigo = $("#search_codigo").val();
		search_descripcion = $("#search_descripcion").val();
		search_tipo = $("#search_tipo").val();

		search_fechai = $("#search_fechai").val();
		search_fechaf = $("#search_fechaf").val();
	}
	else{
		$("#search_codigo").val("");
		$("#search_descripcion").val("");
		$("#search_tipo").val("");
		$("#search_fechai").val("");
		$("#search_fechaf").val("");

		search_codigo = "";
		search_descripcion = "";
		search_tipo = "";
		search_fechai = "";
		search_fechaf = "";
	}
	
	$('#table-movimiento').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
				url : base_url + 'index.php/tesoreria/movimiento/datatable_movimiento/',
				type: "POST",
				data: {
						codigo: search_codigo,
						descripcion: search_descripcion,
						tipo: search_tipo,
						fechai: search_fechai,
						fechaf: search_fechaf
				},
				beforeSend: function(){
					$("#table-movimiento .loading-table").show();
				},
				error: function(){
				},
				complete: function(){
					$("#table-movimiento .loading-table").hide();
				}
		},
		language: spanish,
		columnDefs: [{"className": "dt-center", "targets": 0}],
		order: [[ 1, "desc" ]]
	});
}

function view(id){
	var url = base_url + "index.php/tesoreria/movimiento/getMovimiento";
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data:{
				movimiento: id
		},
		beforeSend: function(){
			clean();
			$(".registrar_movimiento").hide();
			$("#modal_titulo").html("VER");

			$("#caja").attr({"disabled": true});
			$("#tipo_movimiento").attr({"disabled": true});
			$("#fecha").attr({"disabled": true});
			$("#forma_pago").attr({"disabled": true});
			$("#moneda").attr({"disabled": true});
			$("#importe").attr({"disabled": true});
			$("#justificacion").attr({"disabled": true});
			$("#obs_movimiento").attr({"disabled": true});
		},
		success: function(data){
			if (data.match == true) {
				info = data.info;

				$("#movimiento").val(info.movimiento);
				$("#caja").val(info.caja);
				$("#tipo_movimiento").val(info.tipo_movimiento);
				$("#fecha").val(info.fecha);
				$("#forma_pago").val(info.forma_pago);
				$("#moneda").val(info.moneda);
				$("#importe").val(info.importe);
				$("#justificacion").val(info.justificacion);
				$("#obs_movimiento").val(info.obs_movimiento);

				$("#add_movimiento").modal("toggle");
			}
			else{
				Swal.fire({
							icon: "info",
							title: "Información no disponible.",
							html: "<b class='color-red'></b>",
							showConfirmButton: true,
							timer: 4000
						});
				clean();
			}
		},
		complete: function(){
		}
	});
}

function modal_movimientoscierre(cajacierre, caja='', responsable='',ingresos='0',egresos='0',saldo='0'){
	//Asignamos los enlaces a los botones
	url = base_url + "index.php/tesoreria/movimiento/imprimir_detalle_cierres/"+cajacierre;
	url2 = base_url + "index.php/tesoreria/movimiento/imprimir_detalle_cierres_excel/"+cajacierre;
	$("#modal_movimientoscierre").modal("toggle"); 
	$("#imprimir_detalle_cierres").attr("href", url)
	$("#imprimir_detalle_cierres_excel").attr("href", url2)
	
	//Colocamos etiquetas
	$(".titleCaja").html(caja);
	$(".titleIngresos").html(ingresos);
	$(".titleEgresos").html(egresos);
	$(".titleSaldos").html(saldo);
	$(".titleCierre").html(cajacierre);
	$(".titleResponsable").html(responsable);
	getTableMovimientosCierre(cajacierre);
}

function getTableMovimientosCierre(cajacierre){
	$('#table-movimientoscierre').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		autoWidth: false,
		ajax:{
			url : base_url + 'index.php/tesoreria/movimiento/datatable_movimientoscierre',
			type: "POST",
			data: {
				cajacierre: cajacierre,
			},
			beforeSend: function(){
				$("#table-movimientoscierre .loading-table").show();
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

function registrar_movimiento(){
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
					var movimiento = $("#movimiento").val();
					var fecha = $("#fecha").val();
					var importe = $("#importe").val();
					var justificacion = $("#justificacion").val();
					validacion = true;

					if (fecha == ""){
						Swal.fire({
									icon: "error",
									title: "Verifique los datos ingresados.",
									html: "<b class='color-red'>Debe ingresar la fecha del movimiento.</b>",
									showConfirmButton: true,
									timer: 4000
								});
						$("#fecha").focus();
						validacion = false;
						return null;
					}

					if (importe == ""){
						Swal.fire({
									icon: "error",
									title: "Verifique los datos ingresados.",
									html: "<b class='color-red'>Debe ingresar el importe del movimiento.</b>",
									showConfirmButton: true,
									timer: 4000
								});
						$("#importe").focus();
						validacion = false;
						return null;
					}

					if (justificacion == ""){
						Swal.fire({
									icon: "error",
									title: "Verifique los datos ingresados.",
									html: "<b class='color-red'>Debe ingresar una justificación.</b>",
									showConfirmButton: true,
									timer: 4000
								});
						$("#justificacion").focus();
						validacion = false;
						return null;
					}

					if (validacion == true){
						var url = base_url + "index.php/tesoreria/movimiento/guardar_registro";
						var info = $("#formMovimiento").serialize();
						$.ajax({
							type: 'POST',
							url: url,
							dataType: 'json',
							data: info,
							success: function(data){
								if (data.result == "success") {
									if (movimiento == "")
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
								$("#nombre_movimiento").focus();
							}
						});
					}
				}
			});
}

function deshabilitar(movimiento){
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
					var url = base_url + "index.php/tesoreria/movimiento/deshabilitar_movimiento";
					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: {
							movimiento: movimiento
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

								msj = "Algo ha ocurrido, verifique he intentelo nuevamente.";
								if (data.mensaje != "")
									msj = data.mensaje;

								Swal.fire({
									icon: "error",
									title: "Sin cambios.",
									html: "<b class='color-red'>" + msj + "</b>",
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
	$("#modal_titulo").html("REGISTRAR");
	$("#caja").removeAttr("disabled");
	$("#tipo_movimiento").removeAttr("disabled");
	$("#fecha").removeAttr("disabled");
	$("#forma_pago").removeAttr("disabled");
	$("#moneda").removeAttr("disabled");
	$("#importe").removeAttr("disabled");
	$("#justificacion").removeAttr("disabled");
	$("#obs_movimiento").removeAttr("disabled");
	$(".registrar_movimiento").show();
	$("#formMovimiento")[0].reset();
	$("#movimiento").val("");
}

function insertar_cajaMovimiento(){
	var dataString ;
	var observacion = 0;
	var justificacion = 0; 
	var movimientoDinero = 0;
	var seleccionando = 0;
	var cliente = 0;
	var nombres = $("#codigoCajaSeleccion").val();
		
	 movimientoDinero = $("#movimientoDinero").val(); // Ingreso = 1 ; Salida = 2 

	 if(movimientoDinero == 1){
		 
		 seleccionando = $("#seleccionando_1_0").val(); //codigo de la seleccion
		 cliente = $("#cliente_1_0").val(); //Codigo del nombre
	}else{
		/**BENEFICIARIO**/
		 seleccionando=$("#seleccionando_1_0").val(); //codigo de la seleccion
		 cliente=$("#cliente_1_0").val(); //Codigo del nombre
	}
		 
	 justificacion=$("#txtjustificacion").val();
	 observacion=$("#txtobservacion").val();	
	 
	 dataString = $('#frmcajamovimiento').serialize();
		url = base_url+"index.php/tesoreria/movimiento/buscar_cajamovimiento/"+cliente+"/"+seleccionando;
	
	 if(nombres != 'undefined'){
		 alert("no esta vacio");
		 $.post(url,dataString,function(data){
				url = base_url+"index.php/tesoreria/caja/cajas";
				location.href = url;
			});	
	 }else{
			$.post(url,dataString,function(data){
				url = base_url+"index.php/tesoreria/movimiento/movimientos";
				location.href = url;
			});
	 }
		
//		$("#txtMoneda").val("::SELECCIONE::");
//		$("#txtTipoCuenta").val("::SELECCIONE::");
//		$("#txtBanco").val("::SELECCIONE::");
//		$("#txtTitular").val("");
//	    $("#txtCuenta").val("");
//	    $("#txtOficina").val("");
//	    $("#txtSectoriza").val("");
//	     $("#txtInterban").val("");
		
}

function validateFormulario(){
	    // Campos de texto
	 if($("#txtBanco").val() == "S"){
	       $('#txtBanco').css('background-color', '#FFC1C1').focus();
	        return false;
	    }//|| /^\s*$/.test(la caja de texto) cuando hay muchos espacios en blanco
	    if($("#txtCuenta").val() == "" || /^\s*$/.test($("#txtCuenta").val())){
	      $('#txtCuenta').css('background-color', '#FFC1C1').focus();
	      return false;
	    }
	    if($("#txtTitular").val() == "" || /^\s*$/.test($("#txtTitular").val())) {
	        $("#txtTitular").css('background-color', '#FFC1C1').focus();
	        return false;
	    }
	if($("#txtOficina").val() == "" || /^\s*$/.test($("#txtOficina").val())) {
	        $("#txtOficina").css('background-color', '#FFC1C1').focus();
	        return false;
	    }
	if($("#txtSectoriza").val() == "" || /^\s*$/.test($("#txtSectoriza").val())) {
	        $("#txtSectoriza").css('background-color', '#FFC1C1').focus();
	        return false;
	    }
	if($("#txtInterban").val() == "" || /^\s*$/.test($("#txtInterban").val())) {
	        $("#txtInterban").css('background-color', '#FFC1C1').focus();
	        return false;
	    }

	    if($("#txtTipoCuenta").val() == "S"){
	         $("#txtTipoCuenta").css('background-color', '#FFC1C1').focus();
	        return false;
	    }
	   if($("#txtMoneda").val() == "S"){
	       $("#txtMoneda").css('background-color', '#FFC1C1').focus();
	        return false;
	    }

	    // Checkbox
	    if(!$("#txtMoneda").is(":checked")){
	        alert("Debe confirmar que es mayor de 18 años.");
	        return false;
	    }
	
    return true; // Si todo está correcto
}

function editar_caja(caja){
    var url = base_url+"index.php/tesoreria/caja/editar_caja/"+caja;
	$("#zonaContenido").load(url);
}


function eliminar_caja(caja){
	if(confirm('Esta seguro desea eliminar esta caja ?')){
		dataString = "caja="+caja;
		url = base_url+"index.php/tesoreria/caja/eliminar_caja";
		$.post(url,dataString,function(data){
			url = base_url+"index.php/tesoreria/caja/cajas";
			location.href = url;
		});
	}
}

function ver_caja(caja){
	url = base_url+"index.php/tesoreria/caja/ver_caja/"+caja;
	$("#zonaContenido").load(url);
}

function atras_proyecto(){
	location.href = base_url+"index.php/maestros/proyecto/proyectos";
}


function agregar_chequera() {
	
	posicion = $("#posicionEditarDos").val();
	if(posicion.trim()!=""){
		a='descripcion['+posicion+']';
		b='bancoCodigo['+posicion+']';
		c='cuenta['+posicion+']';
		d='cboSerie['+posicion+']';
		
		descripcionGeneral=$("#descripcion").val();
		$("#idldescripcion"+posicion).html(descripcionGeneral);
		document.getElementById(a).value=descripcionGeneral;
		
		document.getElementById(b).value=$("#cboBancoCuenta").val();
		document.getElementById(c).value=$("#cboCuentaCheque").val();
		document.getElementById(d).value=$("#cboSerie").val();
		
		$("#idlbancoCodigo"+posicion).html($("#cboBancoCuenta option:selected").text());
		$("#idlnumroCuenta"+posicion).html($("#cboCuentaCheque option:selected").text());
		$("#idlchequera"+posicion).html($("#cboSerie option:selected").text());

		
	}else{
		chequeraCodigo 		= null;
		descripcion 		= $("#descripcion").val();
		cboBancoCuenta 		= $("#cboBancoCuenta").val();
		nombreBancoCuenta   = $("#cboBancoCuenta option:selected").text();
		cboCuentaCheque 	= $("#cboCuentaCheque").val();
		nombreCuentaCheque  = $("#cboCuentaCheque option:selected").text();
		cboSerie 			= $("#cboSerie").val();
		nombreSerie			= $("#cboSerie option:selected").text();
		n = document.getElementById('tblDetalleChequera').rows.length;   
		j = n + 1;
		if (j % 2 == 0) {
			clase = "itemParTabla";
		} else {
			clase = "itemImparTabla";
		}    
		fila = '<tr id="' + n + '" class="' + clase + '" >';
		fila += '<td width="1.5%">';
		fila += ' '+j;
		fila += '</td>';
		fila += '<input type="hidden" value="" name="chequeraCodigo[' + n + ']" id="chequeraCodigo[' + n + ']">';
		fila += '<td width="6.5%"><div align="center">'
		fila += '<label id="idldescripcion">'+ descripcion +'</label>'
		fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + descripcion + '" name="descripcion[' + n + ']" id="descripcion[' + n + ']"></div></td>'
		fila += '<td width="5.5%"><div align="center">'
		fila += '<label id="idlnombreBancoCuenta">'+nombreBancoCuenta+'</label>'
		fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + cboBancoCuenta + '" name="cboBancoCuenta[' + n + ']" id="cboBancoCuenta[' + n + ']"></div></td>'
		fila += '<td width="5.5%"><div align="center">'
		fila += '<label id="idlnombreCuentaCheque">'+nombreCuentaCheque+'</label>'
		fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + cboCuentaCheque + '" name="cboCuentaCheque[' + n + ']" id="cboCuentaCheque[' + n + ']"></div></td>'
		fila += '<td width="5%"><div align="center">'
		fila += '<label id="idlnombreCuentaCheque">'+nombreSerie+'</label>'
		fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + cboSerie + '" name="cboSerie[' + n + ']" id="cboSerie[' + n + ']"></div></td>'
		fila += '<td width="2.5%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_chequera(' + n + ');">';
		fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
		fila += '</a></strong></font></div></td>';;
		fila += '<input type="hidden" class="cajaMinima" name="chequeaccion[' + n + ']" id="chequeaccion[' + n + ']" value="n">';
		fila += '</tr>';
		$("#tblDetalleChequera").append(fila);
		$("#chequera").focus();
	}
}

function agregar_cuenta() {
	posicion = $("#posicionEditar").val();
	if(posicion.trim()!=""){
		a='cboBancos['+posicion+']';
		b='cboCuentas['+posicion+']';
		c='tipCuenta['+posicion+']';
		d='monedaCuenta['+posicion+']';
		
		e='limiteRetiro['+posicion+']';
		f='tipoCaja['+posicion+']';
		
		limiteRetiro=$("#limiteRetiro").val();
		$("#idllimiteRetiro"+posicion).html(limiteRetiro);		
		document.getElementById(e).value=limiteRetiro;
		
		document.getElementById(a).value=$("#cboBancos").val();
		
		document.getElementById(f).value=$("#cboTipoCaja").val();
		
		document.getElementById(b).value=$("#cboCuentas").val();

		monedaCuenta=$("#monedaCuenta").val();
		$("#idlmoneda"+posicion).html(monedaCuenta);
		document.getElementById(d).value=monedaCuenta;
		
		tipCuenta=$("#tipCuenta").val();
		$("#idltipCuenta"+posicion).html(tipCuenta);
		document.getElementById(c).value=tipCuenta;

		$("#idlbancoCodigo"+posicion).html($("#cboBancos option:selected").text());
		$("#idlnumroCuenta"+posicion).html($("#cboCuentas option:selected").text());
		$("#idltipo"+posicion).html($("#cboTipoCaja option:selected").text());
		
	

		
	}else{
	cuentaCodigo = null;
	cboBancos	 	   = $("#cboBancos").val();
	nombreBancos	   = $("#cboBancos option:selected").text();
	cboCuentas	 	   = $("#cboCuentas").val();
	nombreCuentas 	   = $("#cboCuentas option:selected").text();
	tipCuenta 	 	   = $("#tipCuenta").val();	
	monedaCuenta 	   = $("#monedaCuenta").val();
	tipoCaja 	 	   = $("#cboTipoCaja").val();
	if(tipoCaja == 1){
		nomTipoCaja    = "INGRESO";
	}else if (tipoCaja == 2) {
		nomTipoCaja    = "SALIDA";
	}
	NombretipoCaja 	   = $("#cboTipoCaja option:selected").text();
	limiteRetiro 	   =  $("#limiteRetiro").val();  
    n = document.getElementById('tblDetalleCuenta').rows.length;   
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }    
    fila = '<tr id="' + n + '" class="' + clase + '" >';
    fila += '<td width="1%">';
    fila += ' '+j;
    fila += '</td>';
    fila += '<input type="hidden" value="" name="cuentaCodigo[' + n + ']" id="cuentaCodigo[' + n + ']">';
    fila += '<td width="6.5%"><div align="center">'
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + cboBancos + '" name="cboBancos[' + n + ']" id="cboBancos[' + n + ']">';
    fila += '<label id="idlbanco">'+nombreBancos+'</label>'
    fila += '</div></td>'
    fila += '<td width="5.5%"><div align="center">'
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + cboCuentas + '" name="cboCuentas[' + n + ']" id="cboCuentas[' + n + ']">'
    fila += '<label id="idlcuenta">'+nombreCuentas+'</label>'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + tipCuenta + '" name="tipCuenta[' + n + ']" id="tipCuenta[' + n + ']">'
    fila += '<label id="idltipCuenta">'+tipCuenta+'</label>'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idlmoneda">'+monedaCuenta+'</label>'
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + monedaCuenta + '" name="monedaCuenta[' + n + ']" id="monedaCuenta[' + n + ']">'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idltipoCaja">'+nomTipoCaja+'</label>'	
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + tipoCaja + '" name="tipoCaja[' + n + ']" id="tipoCaja[' + n + ']">'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idlmoneda">'+limiteRetiro+'</label>'	
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + limiteRetiro + '" name="limiteRetiro[' + n + ']" id="limiteRetiro[' + n + ']">'
    fila += '</div></td>'
    fila += '<td  width="1%">'
    fila += '<a href="javascript:;" onclick="editar_cuenta('+ n +')">'
    fila += '<img src="'+base_url+'images/modificar.png" width="16" height="16" border="0" title="Modificar"></a>'
    fila += '</td>'
    fila += '<td width="1%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_cuenta(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';;
    fila += '<input type="hidden" class="cajaMinima" name="cuentaaccion[' + n + ']" id="cuentaaccion[' + n + ']" value="n">';
    fila += '</tr>';

    $("#tblDetalleCuenta").append(fila);
    $("#cuenta").focus();
    inicializar_cuenta();
	}
}

function inicializar_cuenta() {
    $("#cboBancos").val('');
    $("#cboCuentas").val('');
    $("#tipoCuenta").val('');
    $("#monedaCuenta").val('');
    $("#cboTipoCaja").val('');
    $("#limiteRetiro").val('');
}

function editar_cuenta(posicion){

	a='cboBancos['+posicion+']';
	b='cboCuentas['+posicion+']';
	
	c='tipCuenta['+posicion+']';
	d='monedaCuenta['+posicion+']';
	e='limiteRetiro['+posicion+']';
	
	f='tipoCaja['+posicion+']';
	g='cuentaCodigo['+posicion+']';
	
	cboBancos=document.getElementById(a).value;
	cboCuentas=document.getElementById(b).value;
	
	tipCuenta=document.getElementById(c).value;
	monedaCuenta=document.getElementById(d).value;
	limiteRetiro=document.getElementById(e).value;
	
	tipoCaja=document.getElementById(f).value;
	cuentaCodigo=document.getElementById(g).value;
	
	$('#cboBancos').val(cboBancos);
	cargar_cuenta(document.getElementById(a));
		$('#cboCuentas').val(cboCuentas);
		cargar_datosCuenta(document.getElementById(b));
		$('#tipCuenta').val(tipCuenta);
		$('#monedaCuenta').val(monedaCuenta);
	$('#cboTipoCaja').val(tipoCaja);
	$('#limiteRetiro').val(limiteRetiro);
		$('#cuentaCodigo').val(cuentaCodigo);
		$('#posicionEditar').val(posicion);
}

function editar_chequera(posicion)
{
	a='descripcion['+posicion+']';
	b='bancoCodigo['+posicion+']';
	
	c='cuenta['+posicion+']';
	d='cboSerie['+posicion+']';

	e='chequeraCodigo['+posicion+']';
	
	descripcion=document.getElementById(a).value;
	bancoCodigo=document.getElementById(b).value;
	
	cuenta=document.getElementById(c).value;
	cboSerie=document.getElementById(d).value;
	
	chequeraCodigo=document.getElementById(e).value;
	$('#descripcion').val(descripcion);
	$('#cboBancoCuenta').val(bancoCodigo);
	cargar_cuentaCheque(document.getElementById(b));
		$('#cboCuentaCheque').val(cuenta);
		cargar_serieCuenta(document.getElementById(c));
		$('#cboSerie').val(cboSerie);
		$('#chequeraCodigo').val(chequeraCodigo);
		$('#posicionEditarDos').val(posicion);
}


function listar_bancos(){
	n = document.getElementById('tblDetalleCuenta').rows.length;	
	for(x=0;x<n;x++){
		 valor= "cboBancos["+x+"]"; 
         valor_banco = document.getElementById(valor).value;
	}
	url = base_url+"index.php/tesoreria/caja/cargar_banco/"+valor_banco;
    $("#cboBancoCuenta").load(url);	
}


function cargar_banco_moneda(obj){
	cuenta = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_tabla_cuenta/"+cuenta;
	$("#tableCuenta").load(url);
}

function cargar_serieNumero(obj){
	numeroSerie = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_serie/"+numeroSerie;
	$("#numeross").load(url);
}

function cargar_serieCuenta(obj){
	cuenta = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_serieCuenta/"+cuenta;
	$("#cboSerie").load(url);
}

function cargar_cuentaCheque(obj){
	bancoCodigo = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_cuentaCheque/"+bancoCodigo;
	$("#cboCuentaCheque").load(url);
}

function cargar_cuenta(obj){
	bancoCodigo = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_cuenta/"+bancoCodigo;
	$("#cboCuentas").load(url);
}

function cargar_datosCuenta(obj){
	cuentaCodigo = obj.value;
	url = base_url+"index.php/tesoreria/caja/cargar_datosCuenta/"+cuentaCodigo;
	$("#TipoCuenta").load(url);
}

function eliminar_cuenta(n) {
    if (confirm('Esta seguro que desea eliminar esta Cuenta ?')) {
    	a = "cuentaCodigo[" + n + "]";
    	b = "cuentaCodigo[" + n + "]";
        fila = document.getElementById(a).parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
    }
}

function eliminar_chequera(n) {
    if (confirm('Esta seguro que desea eliminar esta chequera ?')) {
    	a = "chequeraCodigo[" + n + "]";
    	b = "chequeraCodigo[" + n + "]";
        fila = document.getElementById(a).parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
    }
}

function cambiar_estado_campos(estado){
    //Para los campos del banco
    $("#cboBancos").attr('disabled', estado);
    $("#sectorista").attr('disabled', estado);
    $("#telefono").attr('disabled', estado);
    $("#direccion").attr('disabled', estado);
    $("#sobregiro").attr('disabled', estado);

    //Para los campos de la persona
    $("#moneda").attr('disabled', estado);
    $("#limiteRetiro").attr('disabled', estado);
    $("#observaciones").attr('disabled', estado);
    
}

function obtener_cliente() {
    var numdoc = $("#ruc_cliente").val();
    $('#cliente,#nombre_cliente').val('');

    if (numdoc == '')
        return false;

    var url = base_url + "index.php/empresa/cliente/JSON_buscar_cliente/" + numdoc;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.EMPRC_RazonSocial != '') {
                $('#nombre_cliente').val(item.EMPRC_RazonSocial);
                $('#cliente').val(item.CLIP_Codigo);
                $('#codproducto').focus();
            }
            else {
                $('#nombre_cliente').val('No se encontró ningún registro');
                $('#linkVerCliente').focus();
            }
        });
    });
    return true;
}

function eliminar_cajamovimineto(cajacodigomov){
	if(confirm('Esta seguro desea eliminar esta caja ?')){
		dataString = "cajacodigomov="+cajacodigomov;
		url = base_url+"index.php/tesoreria/movimiento/eliminar_cajamovimineto";
		$.post(url,dataString,function(data){
			
			url = base_url+"index.php/tesoreria/movimiento/movimientos";
			location.href = url;
		});
	}
	
}

function eliminar_Codigocajamovimineto(cajacodigomov){

	if(confirm('Esta seguro desea eliminar esta caja ?')){
		dataString = "cajacodigomov="+cajacodigomov;
		url = base_url+"index.php/tesoreria/movimiento/eliminar_cajamovimineto";
		$.post(url,dataString,function(data){
			
			url = base_url+"index.php/tesoreria/caja/cajas";
			location.href = url;
		});
	}
	
}

function editar_cajamovimineto(cajacodigomov,MovDinero){
//	jQuery(document).ready(function(){
//		
//		/**ES PARA LA FUNCION EDITAR DE CAJA-MOVIEMINTO**/
//		mostrandoDatosCuentas(this,1,0);
//		obtenerDatosCajaDiaria(this,2,0);
//	});
	
	
	 var url = base_url+"index.php/tesoreria/movimiento/editar_cajamovimineto/"+cajacodigomov+"/"+MovDinero;
		$("#tablaregisnombrecaja").load(url);
}

function DF(i){
	alert("data"+i);
}
