jQuery(document).ready(function(){
    
    $('#table-caja').DataTable({ responsive: true,
        filter: false,
        destroy: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url : base_url + 'index.php/tesoreria/caja/datatable_caja/',
                type: "POST",
                data: { dataString: "" },
                beforeSend: function(){
                    $("#table-caja .loading-table").show();
                },
                error: function(){
                },
                complete: function(){
                    $("#table-caja .loading-table").hide();
                }
        },
        language: spanish,
        columnDefs: [{"className": "dt-center", "targets": 0}],
        order: [[ 1, "asc" ]]
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
      
      $("#nuevo").click(function () {
         clean();
         $("#estado_caja option:not(:selected)").hide();
         
         //Habilitamos la caja codigo
         $("#codigo_caja").removeAttr("readonly")
         
         //Mostramos el modal
         $('#add_caja').modal('show');
      });        

      $('#search_descripcion').keyup(function(e){
          if ( e.which == 13 ){
              if( $(this).val() != '' )
                  search();
          }
      });
    
    $("#imgGuardarProyecto").click(function(){
		dataString = $('#frmProyecto').serialize();
		$("#container").show();
		$("#frmProyecto").submit();
    });
    
    $("#buscarProyecto").click(function(){
		$("#form_busqueda").submit();
    });	
    
    $("#nuevaCaja").click(function(){
		url = base_url+"index.php/tesoreria/caja/nueva_caja";
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
    
    
    
	/*container = $('div.container');
 	$("#frmProyecto").validate({
		event    : "blur",
		rules    : {
					'ruc'             : {required:true,minlength:11,number:true},
					'razon_social'    : "required"
 			    },
		debug    : true,
		errorContainer      : "container",
		errorLabelContainer : $(".container"),
		wrapper             : 'li',
		submitHandler       : function(form){
				dataString  = $('#frmProyecto').serialize();
				modo        = $("#modo").val();
				$('#VentanaTransparente').css("display","block");
				if(modo=='insertar'){
					url = base_url+"index.php/tesoreria/caja/insertar_cuenta";
					if (validateFormulario()) {

						$.post(url,dataString,function(data){
					   $("#VentanaTransparente").css("display","none");
					alert('Se ha ingresado una Caja.');
						location.href = base_url+"index.php/tesoreria/caja/cajas";
					});

					}else{
						$('#VentanaTransparente').css("display","none");
				
					}
					

				}
				else if(modo=='modificar'){
					if (validateFormulario()) {
					
					url = base_url+"index.php/tesoreria/caja/modificar_caja";
					$.post(url,dataString,function(data){
						$("#VentanaTransparente").css("display","none");
						alert('Su registro ha sido modificado.');
						location.href = base_url+"index.php/tesoreria/caja/cajas";
					});
				}else{
						$('#VentanaTransparente').css("display","none");
				
					}

			}
		}
	});*/
/*
    container = $('div.container');	
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
    });*/
  
});

function search( search = true){
    if (search == true){
        search_codigo = $("#search_codigo").val();
        search_descripcion = $("#search_descripcion").val();
        search_tipo = $("#search_tipo").val();
    }
    else{
        $("#search_codigo").val("");
        $("#search_descripcion").val("");
        $("#search_tipo").val("");
        search_codigo = "";
        search_descripcion = "";
        search_tipo = "";
    }

    $('#table-caja').DataTable({ responsive: true,
        filter: false,
        destroy: true,
        processing: true,
        serverSide: true,
        ajax:{
            url : base_url + 'index.php/tesoreria/caja/datatable_caja/',
            type: "POST",
            data: {
                codigo: search_codigo,
                descripcion: search_descripcion,
                tipo: search_tipo,
            },
            beforeSend: function(){
                $("#table-caja .loading-table").show();
            },
            error: function(){
            },
            complete: function(){
                $("#table-caja .loading-table").hide();
            }
        },
        language: spanish,
        columnDefs: [{"className": "dt-center", "targets": 0}],
        order: [[ 1, "asc" ]]
    });
}

function editar(id){
    
    $("#estado_caja option").show();
    
    var url = base_url + "index.php/tesoreria/caja/getCaja";
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data:{
            caja: id
        },
        beforeSend: function(){
            clean();
        },
        success: function(data){
            if (data.match == true) {
                info = data.info;
                $("#caja").val(info.caja);
                $("#codigo_caja").val(info.codigo);
                $("#nombre_caja").val(info.nombre);
                $("#tipo_caja").val(info.tipocaja);
                $("#obs_caja").val(info.obs);
                $("#estado_caja").val(info.estado);
                $("#estado_caja_ant").val(info.estado);
                $("#cajero_caja").val(info.cajero_caja);
                $("#nombrecajero_caja").val(info.nombre_responsable);
                
                //Deshabilito el option abrir del select estado caja
                $('#estado_caja option[value=1]').hide();
                
                //Readonly la caja editar
                $("#codigo_caja").attr("readonly","readonly");
                
                //Abrimos el modal
                $("#add_caja").modal("toggle");
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

function registrar_caja(){
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
                    var caja = $("#caja").val();
                    var estado = $("#estado_caja").val();
                    var descripcion = $("#nombre_caja").val();
                    validacion = true;
                    if (descripcion == ""){
                        Swal.fire({
                                    icon: "error",
                                    title: "Verifique los datos ingresados.",
                                    html: "<b class='color-red'>Debe ingresar un nombre para la caja.</b>",
                                    showConfirmButton: true,
                                    timer: 4000
                                });
                        $("#nombre_caja").focus();
                        validacion = false;
                        return null;
                    }
                    if (validacion == true){
                        var url = base_url + "index.php/tesoreria/caja/guardar_registro";
                        var info = $("#formCaja").serialize();
                        $.ajax({
                            type: 'POST',
                            url: url,
                            dataType: 'json',
                            data: info,
                            success: function(data){
                                if (data.result == "success") {
                                    if (caja == "")
                                        titulo = "¡Registro exitoso!";
                                    else
                                        titulo = "¡Actualización exitosa!";

                                    Swal.fire({
                                        icon: "success",
                                        title: titulo,
                                        showConfirmButton: true,
                                        timer: 2000
                                    });

                                    //Cerramos modal
                                     $("#add_caja").modal("hide");

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
                                $("#nombre_caja").focus();
                            }
                        });
                    }
                }
            });
}

function deshabilitar(caja){
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
                    var url = base_url + "index.php/tesoreria/caja/deshabilitar_caja";
                    $.ajax({
                        type: 'POST',
                        url: url,
                        dataType: 'json',
                        data: {
                            caja: caja
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
    $("#formCaja")[0].reset();
    $("#caja").val("");
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

function listamultiple_caja(caja){
	url = base_url+"index.php/tesoreria/movimiento/movimientos/"+caja;
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
		
	 inicializar_cuenta();		
	}else{
	cuentaCodigo       = null;
	cboBancos	 	   = $("#cboBancos").val();
	nombreBancos	   = $("#cboBancos option:selected").text();
	cboCuentas	 	   = $("#cboCuentas").val();
	nombreCuentas 	   = $("#cboCuentas option:selected").text();
	tipCuenta 	 	   = $("#tipCuenta").val();	
	monedaCuenta 	   = $("#monedaCuenta").val();
	tipoCaja 	 	   = $("#cboTipoCaja").val();
	tipocajaSelec=$("#cboTipoCaja option:selected").text();
	tipoCajas=$("#cboTipoCaja").val();
	if(tipoCaja == 1){
		nomTipoCaja    = "INGRESO";
	}else if (tipoCaja == 2) {
		nomTipoCaja    = "SALIDA";
	}
	NombretipoCaja 	   = $("#cboTipoCaja option:selected").text();
	limiteRetiro 	   =  $("#limiteRetiro").val();  
    n = document.getElementById('tblDetalleCuenta').rows.length; 
    //alert("caja: "+tipoCajas+" cboBancos: "+cboBancos+" tipCuenta: "+cboCuentas);
    verificarExiste=verificarCuentaCajas(tipoCajas,cboBancos,cboCuentas);
    if(!verificarExiste){
  	
    }else{
  	alert("ya Existe");
  	//alert("son iguales no");
  	return !verificarExiste;
    }
  //return false;
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }    
    fila = '<tr id="' + j + '" class="' + clase + '" >';
    fila += '<td width="1%">';
    fila += ' '+j;
    fila += '<input type="hidden" value="" name="cuentaCodigo[' + j + ']" id="cuentaCodigo[' + j + ']">';
    fila += '</td>';
    fila += '<td width="6.5%"><div align="center">'
    fila += '<label id="idlbancoCodigo'+j+'">'+nombreBancos+'</label>'
    fila += '<input type="hidden" class="cajaGeneral"  name="cboBancos['+j+']" id="cboBancos['+j+']"value="'+$("#cboBancos").val()+'"/>' 
    fila += '</div></td>'
    fila += '<td width="5.5%"><div align="center">'
    fila += '<input type="hidden" size="8"  class="cajaGeneral" value="' + cboCuentas + '" name="cboCuentas[' + j + ']" id="cboCuentas[' + j + ']">'
    fila += '<label id="idlnumroCuenta'+j+'">'+nombreCuentas+'</label>'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<input type="hidden" size="8"  class="cajaGeneral" value="' + tipCuenta + '" name="tipCuenta[' + j + ']" id="tipCuenta[' + j + ']">'
    fila += '<label id="idltipCuenta'+j+'">'+tipCuenta+'</label>'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idlmoneda'+j+'">'+monedaCuenta+'</label>'
    fila += '<input type="hidden" size="8" class="cajaGeneral" value="' + monedaCuenta + '" name="monedaCuenta[' + j + ']" id="monedaCuenta[' + j + ']">'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idltipo'+j+'">'+nomTipoCaja+'</label>'	
    fila += '<input type="hidden" size="8" class="cajaGeneral" value="' + tipoCajas + '" name="tipoCaja[' + j + ']" id="tipoCaja[' + j + ']">'
    fila += '</div></td>'
    fila += '<td width="5%"><div align="center">'
    fila += '<label id="idllimiteRetiro'+j+'">'+limiteRetiro+'</label>'	
    fila += '<input type="hidden" size="8" maxlength="10" class="cajaGeneral" value="' + limiteRetiro + '" name="limiteRetiro[' + j + ']" id="limiteRetiro[' + j + ']">'
    fila += '</div></td>'; 
    fila += '<td width="1%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_cuenta(' + j + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td  width="1%">';
    fila += '<a href="javascript:;" onclick="editar_cuenta('+j+')"><img src="'+base_url+'images/modificar.png" width="16" height="16" border="0" title="Modificar"></a>';
    fila += '<input type="hidden"  name="cuentaaccion[' + j + ']" id="cuentaaccion[' + j + ']" value="n">';
    fila += '</td>';
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
    $("#posicionEditar").val('');
    $("#cuentaCodigo").val('');
    $("#tipCuenta").val('');
}

function editar_cuenta(posicion){

	a='cboBancos['+posicion+']';
	b='cboCuentas['+posicion+']';
	
	c='tipCuenta['+posicion+']';
	d='monedaCuenta['+posicion+']';
	e='limiteRetiro['+posicion+']';
	
	f='tipoCaja['+posicion+']';
	g='cuentaCodigo['+posicion+']';
	z='txtCuentaCodigo'+posicion;
	cboBancos=document.getElementById(a).value;
	cboCuentas=document.getElementById(b).value;
	
	tipCuenta=document.getElementById(c).value;
	monedaCuenta=document.getElementById(d).value;
	limiteRetiro=document.getElementById(e).value;
	
	tipoCaja=document.getElementById(f).value;
	cuentaCodigo=document.getElementById(g).value;

	cargar_cuantas(cboBancos,cboCuentas);

	$('#cboBancos').val(cboBancos);
//cargar_cuenta(document.getElementById(a))
	//cargar_cuenta(document.getElementById(a));
//cboCuentas
		//$('#cboCuentas').val(cboCuentas);
		cargar_datosCuenta(document.getElementById(b));
		$('#tipCuenta').val(tipCuenta);
$('#monedaCuenta').val(monedaCuenta);
$('#cboTipoCaja').val(tipoCaja);
$('#limiteRetiro').val(limiteRetiro);
$('#cuentaCodigo').val(cuentaCodigo);
$('#posicionEditar').val(posicion);
}

function editar_chequera(posicion){
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
 function cargar_cuantas(cuentaCodigo,bancoCodigo){
 	url = base_url+"index.php/tesoreria/caja/cargarCuentaEmpresa/"+cuentaCodigo+"/"+bancoCodigo;
	$("#cboCuentas").load(url);
 }
 function cargar_bancoEdit(codigo){
 	url = base_url+"index.php/tesoreria/caja/cargarCuentaBanco/"+codigo;
	$("#cboBancos").load(url);
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
function validateFormulario(){
    // Campos de texto
 if($("#nombreCaja").val() == ""){
       $('#nombreCaja').css('background-color', '#FFC1C1').focus();
        return false;
    }//|| /^\s*$/.test(la caja de texto) cuando hay muchos espacios en blanco
    if($("#cboTipCaja").val() == "" || /^\s*$/.test($("#cboTipCaja").val())){
      $('#cboTipCaja').css('background-color', '#FFC1C1').focus();
      return false;
    }
    return true; // Si todo está correcto
}
function verificarCuentaCajas(codTipo,codbanco,codcuenta){
		n = document.getElementById('tblDetalleCuenta').rows.length;	
		isEncuentra=false;
		if(n!=0){
			for(x=0;x<n;x++){
					var contador=x+1;
					valorTipo=document.getElementById("tipoCaja["+contador+"]").value;
					cboBancos =document.getElementById("cboBancos["+contador+"]").value;
					cboCuentas= document.getElementById("cboCuentas["+contador+"]").value;
					if(codTipo==valorTipo 
						&& codbanco==cboBancos 
						&& codcuenta==cboCuentas){
						isEncuentra=true;	
						break;
					}
			}
		}
		return isEncuentra;
		}
	

function keypressError(id){
  $("#"+id).css({"background-color": "#fff"}); 
}

