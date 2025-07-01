$(document).ready(function () {
   tipo_oper       = $("#Rtipo_oper").val();
   tipo_docu       = $("#Rtipo_docu").val();
   $('#table-comprobante').DataTable({
        filter: false,
        destroy: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
            url : base_url + 'index.php/ventas/comprobante/datatable_comprobantes/'+tipo_oper+'/'+tipo_docu,
            type: "POST",
            data: {
                seriei: $("#seriei").val()
                                },
            beforeSend: function(){
                $(".loading-table").show();
            },
            error: function(){
            },
            complete: function(){
                $(".loading-table").hide();
            }
        },
        "language": "spanish",
        order: [[ 0, "desc" ]]
    });    

    $("#btn-up").click(function () {
        $("#inputFilters div").hide("fast");
        $("#btn-up").hide("fast");
        $("#btn-down").show("fast");
        $("#fabricanteSearch").val("");
        $("#familiaSearch").val("");
        $("#marcaSearch").val("");
        $("#modeloSearch").val("");
    });

    $("#btn-down").click(function () {
        $("#inputFilters div").show("fast");
        $("#btn-up").show("fast");
        $("#btn-down").hide("fast");
    });

    //Nuevo comprobante
    $("#nuevaComprobante").click(function () {
        url = base_url + "index.php/ventas/comprobante/comprobante_nueva" + "/" + 
        tipo_oper + "/" + tipo_docu;
        location.href = url;
    });

    //Imprimir comprobante
    $("#imprimirComprobante").click(function () {
        verPdf();
    });

    //Limpiar formulario busqueda de comprobante
    $("#limpiarC").click(function(){
        $("#form_busqueda")[0].reset();
        $("#cliente").val("");
        $("#proveedor").val("");
        $("#producto").val("");
        fechai = "";
        fechaf = "";
        seriei = "";
        numero = "";
        cliente = "";
        ruc_cliente = "";
        nombre_cliente = "";
        proveedor = "";
        ruc_proveedor = "";
        nombre_proveedor = "";
        producto = "";
        $('#table-comprobante').DataTable({
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            ajax:{
                    url : base_url + 'index.php/ventas/comprobante/datatable_comprobantes/'+tipo_oper+'/'+tipo_docu,
                    type: "POST",
                    data: {
                            fechai: fechai, 
                            fechaf: fechaf,
                            seriei: seriei,
                            numero: numero,
                            cliente: cliente,
                            ruc_cliente: ruc_cliente,
                            nombre_cliente: nombre_cliente,
                            proveedor: proveedor,
                            ruc_proveedor: ruc_proveedor,
                            nombre_proveedor: nombre_proveedor,
                            producto: producto
                    },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 0, "desc" ]]
        });
    });    

   /*Buscar comprobantes del listado*/
   $("#buscarC").click(function(){
        fechai          = $("#fechai").val();
        fechaf          = $("#fechaf").val();
        seriei           = $("#seriei").val();
        numero          = $("#numero").val();
        ruc_cliente     = $("#ruc_cliente").val();
        nombre_cliente  = $("#nombre_cliente").val();
        ruc_proveedor   = $("#ruc_proveedor").val();
        nombre_proveedor = $("#nombre_proveedor").val();
        producto        = $("#producto").val();
        tipo_oper       = $("#Rtipo_oper").val();
        tipo_docu       = $("#Rtipo_docu").val();
        $('#table-comprobante').DataTable({
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            ajax:{
                    url : base_url + 'index.php/ventas/comprobante/datatable_comprobantes/'+tipo_oper+'/'+tipo_docu,
                    type: "POST",
                    data: {
                            fechai: fechai, 
                            fechaf: fechaf,
                            seriei: seriei,
                            numero: numero,
                            ruc_cliente: ruc_cliente,
                            nombre_cliente: nombre_cliente,
                            ruc_proveedor: ruc_proveedor,
                            nombre_proveedor: nombre_proveedor,
                            producto: producto
                    },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 0, "desc" ]]
        });
    });

   /* Impresión de rango de documentos*/
    $("#imprimirRango").click(function () {
        var inicio = $("#numeroI").val();
        var fin = $("#numeroF").val();
        var oper = $("#Rtipo_oper").val();
        var docu = $("#Rtipo_docu").val();
        if (inicio == null || inicio == undefined || inicio == NaN || inicio == ""){
            //alert("indique un numero de inicio.");
            Swal.fire({
                icon: "info",
                title: "indique un numero de inicio.",
                html: "<b class='color-red'></b>",
                showConfirmButton: true,
                timer: 1500
            });
            $("#numeroI").focus();
            return null;
        }
        if (fin == null || fin == undefined || fin == NaN || fin == ""){
            //alert("indique un numero de fin.");
            Swal.fire({
                icon: "info",
                title: "indique un numero de fin.",
                html: "<b class='color-red'></b>",
                showConfirmButton: true,
                timer: 1500
            });
            $("#numeroF").focus();
            return null;
        }
        if (parseInt(inicio) > parseInt(fin)){
            //alert("Rango de comprobantes invalido.");
            Swal.fire({
                icon: "info",
                title: "Rango de comprobantes invalido.",
                html: "<b class='color-red'></b>",
                showConfirmButton: true,
                timer: 1500
            });
        }
        else{
            var url = base_url + "index.php/ventas/comprobante/comprobante_pdf_a4_rango/"+inicio+"/"+fin+"/"+oper+"/"+docu;
            window.open(url, '_blank', 'width=700,height=480,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0');
        }
    });

    /** Link para ver direcciones*/
    $("#linkVerDirecciones").click(function () {
        tipo_oper = $("#tipo_oper").val();
        if (tipo_oper == 'V')
            cliente = $("#cliente").val();
        $("#lista_direcciones ul").html('');
        $("#lista_direcciones").slideToggle("fast", function () {
            if (tipo_oper == 'V')
                //var url = base_url + "index.php/empresa/cliente/JSON_listar_sucursalesCliente/" + cliente;
                var url = base_url + "index.php/empresa/cliente/JSON_listar_sucursalesEmpresa/" + cliente;
            else
                url = base_url + "index.php/empresa/empresa/JSON_listar_sucursalesEmpresa";
            
            $.getJSON(url, function (data) {
                var count = 0;
                $.each(data, function (i, item) {
                    fila = '';
                    valor='';
                    if (item.Tipo == '1')
                        fila += '<li style="list-style: none; font-weight:bold; color:#aaa">' + item.Titulo + '</li>';
                    else {
                        valor =  item.EESTAC_Direccion;

                        //if(item.distrito != "") valor += ' ' + item.distrito;
                        //if(item.provincia != "") valor += ' - ' + item.provincia;
                        //if(item.departamento != "") valor += ' - ' + item.departamento;

        

                        fila += '<li><a href="javascript:;">'+valor+'</a></li>';
                        count ++;
                    }
                    $("#lista_direcciones ul").append(fila);
                });
                if(count==1) $("#lista_direcciones li a").trigger('click')
               
            });
            return true;
        });
    });

    $("#cancelarComprobante, #cancelarImprimirComprobante").click(function () {
        var tipo_oper   = $("#tipo_oper").val();
		var tipo_docu   = $("#tipo_docu").val();
        $( "#salir").val(1);
        $.fancybox.close();
        url = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
        location.href = url;
    });
	
    $(".btn-search-sunat-comprobante").click(function(){
            var numeroruc = $("#buscar_cliente").val();
            if(numeroruc != ""){
				var url = base_url + "index.php/empresa/cliente/search_documento_insert/";			
			   $.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data:{numero: numeroruc},
						beforeSend: function(){
							$('.btn-search-sunat-comprobante').hide("fast");
							$(".icon-loading-lg").show("slow");
						},
						success: function(data){
							if (data.exists == false && data.match == true) {
								info = data.info;
								titulo = "Se registro un cliente";
								Swal.fire({
										icon: "success",
										title: titulo,
										showConfirmButton: true,
										timer: 2000
								});
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
							}
							$('.btn-search-sunat-comprobante').show("fast");                        
						},
						complete: function(){
							$(".icon-loading-lg").hide("fast");
							$('.btn-search-sunat-comprobante').show("fast");
						}
				});
            }
    });
	

});

var colors = {
    0 : "transparent"
};

/*Aca empiezan las funcoines*/
function calcular_totales_tempdetalle(){
    
    n = document.getElementById('tempde_tbl').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;

    igvtotal = 0;

    gravada_total = 0;
    exonerado_total = 0;
    inafecto_total = 0;
    gratuito_total = 0;
    
    preciototal = 0;
    importetotal = 0;
    tbolsa = 0;

    igv = $("#igv").val();
    descuento = $("#descuento").val(); 
    if (igv == null || igv == undefined) { igv = 18;}
    if (descuento == null || descuento == undefined ) { descuento = 0;}
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]";
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        e = "detaccion["+i+"]";
        f = "tafectacion["+i+"]";
        g = "icbper["+i+"]";

        if(document.getElementById(e) != null && document.getElementById(e).value != 'e' && document.getElementById(e).value != 'EE'){
            importeBolsa = parseFloat(document.getElementById(a).value);
            precio = parseFloat(document.getElementById(d).value); //subTotal || cantidad * precio sin igv
            afectacion = document.getElementById(f).value; // SUMA DE IGV
            icbper = document.getElementById(g).value; // IMPUESTO POR BOLSA

            if (afectacion == "1"){ // GRAVADA
                gravada_total += precio;

                //igvTo = parseFloat(document.getElementById(b).value); // SUMA DE IGV
                //igvtotal = (igvTo + igvtotal);
            }
            else
                if (afectacion == "8"){ // EXONERADO
                    exonerado_total += precio;
                }
            else
                if (afectacion == "9" || afectacion == '16'){ // INAFECTO O EXPORTACION
                    inafecto_total += precio;
                }
            else{ // GRATUITA
                gratuito_total += precio;
            }

            if ( icbper == "1" ){
                tbolsa += importeBolsa;
            }
        }
    }
    
    descuento_gravada = gravada_total * parseFloat(descuento/100);
    descuento_exonerado = exonerado_total * parseFloat(descuento/100);
    descuento_inafecto = inafecto_total * parseFloat(descuento/100);

    // AL GRATUITO NO SE LE APLICA EL DESCUENTO, "DE POR SI YA ES GRATUITO ;) "
    descuento_total = descuento_gravada + descuento_exonerado + descuento_inafecto;
    
    gravada_total = gravada_total - descuento_gravada;
    exonerado_total = exonerado_total - descuento_exonerado;
    inafecto_total = inafecto_total - descuento_inafecto;

    precio_total = gravada_total + exonerado_total + inafecto_total;
    igvtotal = parseFloat( (gravada_total*igv) / 100 );

    // IMPORTE TOTAL NO INCLUYE DESCUENTO TOTAL. EL DESCUENTO YA FUE RESTADO DE LAS AFECTACIONES
    importetotal = parseFloat(precio_total + igvtotal + tbolsa);

    /*$("#gravadatotal").val(gravada_total.format(false));
    $("#exoneradototal").val(exonerado_total.format(false));
    $("#inafectototal").val(inafecto_total.format(false));
    $("#gratuitatotal").val(gratuito_total.format(false));*/

    $("#gravadatotal").val(gravada_total);
    $("#exoneradototal").val(exonerado_total);
    $("#inafectototal").val(inafecto_total);
    $("#gratuitatotal").val(gratuito_total);    

    //php # AHORA SI INCLUIMOS EL DESCUENTO TOTAL, PARA MOSTRARLO CORRECTAMENTE EN LA VISTA Y GUARDAR EN LA DB 
    
    /*$("#preciototal").val(precio_total.format(false));  //val(precio_total)
    $("#descuentotal").val(descuento_total.format(false));
    $("#igvtotal").val(igvtotal.format(false));  //val(igv_total)
    $("#importetotal").val(importetotal.format(false));  //val(importe_total)
    $("#importeBolsa").val(tbolsa.format(false));*/

    $("#preciototal").val(precio_total);  //val(precio_total)
    $("#descuentotal").val(descuento_total);
    $("#igvtotal").val(igvtotal);  //val(igv_total)
    $("#importetotal").val(importetotal);  //val(importe_total)
    $("#importeBolsa").val(tbolsa);    


    // SI TIENE RETENCIÓN

    if ( $("#applyRetencion").is(":checked") == true ){
        importeR = ( gravada_total * $("#retencion_porc").val() / 100 );
        importeTMR = importetotal - importeR;

        $(".importe_retencion_span").html( importeR.toFixed(2) );
        $(".importe_retencion").val( importeTMR.toFixed(2) );
    }
}

function verificar_Inventariado_producto(){
    base_url = $("#base_url").val();
    tipo_oper = $("#tipo_oper").val();
    url = base_url + "index.php/ventas/comprobante/verificar_inventariado/";
    producto=$("#producto").val();
    prodNombre=$("#nombre_producto").val();
    dataEnviar="enviarCodigo="+producto;  
       $.ajax({url: url,
        data:dataEnviar,
        type:'POST', 
        success: function(result){
            if (result=="0") {
                prodNombre="<p>"+$("#nombre_producto").val()+"</p>";
                $('#popup').fadeIn('slow');
                $('.popup-overlay').fadeIn('slow');
                $('.popup-overlay').height($(window).height());
                $("#contendio").html(prodNombre);
                return false;
            }
        
    }}); 

}

function abrir_pdf_envioSunat(codigo){
    url = base_url+"index.php/ventas/comprobante/consutarRespuestaPdfsunat/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log('Get: '+url);
        },
        error: function (data) {
             console.log('Error:' + data);
        },
        success: function (data) {
           url = data.respuestas_enlacepdf;
        }
    });
	window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}

function abrir_envioSunat(codigo){
    url = base_url+"/index.php/ventas/comprobante/ventana_osafact_correos/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log(data);
        },
        error: function (data) {
             console.log(data);
        },
        success: function (data) {
        }
    });
    window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}

function consultarAnuladoXmlSunat(codigo){
    url = base_url+"index.php/ventas/comprobante/consutarRespuestaXmlsunat/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log(data);
        },
        error: function (data) {
             console.log(data);
        },
        success: function (data) {
           url = data.respuestas_enlacexml;
        }
    });
    window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}

function comprobante_conmenbrete(comprobante) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete/" + tipo_oper + "/" + comprobante + "/" + tipo_docu + "/0";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ejecutarModal(){
    $("#buscar_producto").val("").focus();
    $('#popup').fadeOut('slow');
    $('.popup-overlay').fadeOut('slow');
    return false;
}

jQuery(document).ready(function () {

    //base_url = "http://localhost/osa-cochera/";
    //var tipo_oper = $("#Rtipo_oper").val();
    //var tipo_docu = $("#Rtipo_docu").val();
    var tipo_oper = $("#tipo_oper").val();
    var tipo_docu = $("#tipo_docu").val();    
    contiene_igv = $("#contiene_igv").val();
	
    //$(document).ready(function(){
    $('#open').click(function(){
        $('#popup').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        return false;
    });
    
    $('#close').click(function(){
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });

//});

//cambir de Tipo de Comprobante(Factura,Boleta,Comprobante)
$("#cboTipoDocu").change(function () {
    tipo_docu = $("#cboTipoDocu").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_nueva" + "/" + tipo_oper + "/" + tipo_docu;
    document.forms['frmComprobante'].action = url;
    $("#frmComprobante").submit();
});
    //Guardar el comprobante
    $("#grabarComprobante").click(function () {
        
        //if ( $("#VerificadoSuccess").val() == "1" ){
            var tipo_oper = $('#tipo_oper').val();
            var tipo_docu = $('#tipo_docu').val();
           
            $("#grabarComprobante").css('visibility', 'hidden');
            var codigo = $('#codigo').val();
            var tipo_d = $("#cboTipoDocu").val();
            if ($("#serie").val() == "") {
                $("#serie").focus();
                //alert("Ingrese la serie.");
                Swal.fire({
                    icon: "info",
                    title: "Ingrese la serie.",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
            if ($("#almacen").val() == "") {
                $("#almacen").focus();
                //alert("Ingrese la almacen.");
                Swal.fire({
                    icon: "info",
                    title: "Ingrese la almacen.",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
            if (tipo_oper == 'C') {
                if ($("#numero").val() == "") {
                    $("#numero").focus();
                    //alert("Ingrese el numero documento.");
                    Swal.fire({
                        icon: "info",
                        title: "Ingrese el numero documento.",
                        html: "<b class='color-red'></b>",
                        showConfirmButton: true,
                        timer: 1500
                    });
                    $('#grabarComprobante').css('visibility', 'visible');
                    $('img#loading').css('visibility', 'hidden');
                    return false;
                }
            }

            if ($('#moneda').val() == '') {
                $("#moneda").focus();
                //alert("Debe seleccionar Moneda.");
                Swal.fire({
                    icon: "info",
                    title: "Debe seleccionar Moneda.",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }

            if (tipo_oper == 'V') { 
                
                if ($('#cliente').val() == '') {
                    $("#cliente").focus();
                    //alert("Debe seleccionar Cliente.");
                    Swal.fire({
                        icon: "info",
                        title: "Debe seleccionar Cliente.",
                        html: "<b class='color-red'></b>",
                        showConfirmButton: true,
                        timer: 1500
                    });
                    $('#grabarComprobante').css('visibility', 'visible');
                    $('img#loading').css('visibility', 'hidden');
                    return false;
                }
                else{
					
		    if(tipo_docu == 'F' && $('#cliente').val() == 421){
                        //alert("Debe seleccionar Cliente.");
                        Swal.fire({
                            icon: "info",
                            title: "No puede crear facturas para CLIENTES VARIOS.",
                            html: "<b class='color-red'></b>",
                            showConfirmButton: true,
                            timer: 1500
                        });
                        $('#grabarComprobante').css('visibility', 'visible');
                        $('img#loading').css('visibility', 'hidden');
                        return false;
		    } 
					
		}

                /*var sizeDoc = $('#ruc_cliente').val().length;

                if ( sizeDoc == 8 && tipoDocumento === 'F'){
                    alert("Solamente puedes emitir Facturas a Clientes Juridicos.");
                    $('#grabarComprobante').css('visibility', 'visible');
                    $('img#loading').css('visibility', 'hidden');
                    return false;
                }

                if ( sizeDoc == 11 && tipoDocumento === 'B'){
                    alert("Solamente puedes emitir Boletas a Clientes Naturales.");
                    $('#grabarComprobante').css('visibility', 'visible');
                    $('img#loading').css('visibility', 'hidden');
                    return false;
                }*/
            } else if (tipo_oper == 'C') {

                if ($('#proveedor').val() == '') {
                    $("#proveedor").focus();
                    //alert("Debe seleccionar Proveedor.");
                    Swal.fire({
                        icon: "info",
                        title: "Debe seleccionar Proveedor.",
                        html: "<b class='color-red'></b>",
                        showConfirmButton: true,
                        timer: 1500
                    });
                    $('#grabarComprobante').css('visibility', 'visible');
                    $('img#loading').css('visibility', 'hidden');
                    return false;
                }
            }

            if($("#moneda").val() > 2 && $("#tdcEuro").val() == "") {
                $("#tdcEuro").focus();
                //alert("Ingrese el valor del euro");
                Swal.fire({
                    icon: "info",
                    title: "Ingrese el valor del euro",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');               
                return false;
            }

            if ($("#forma_pago").val() == '') {
                //alert("Seleccione Forma de pago.");
                Swal.fire({
                    icon: "info",
                    title: "Seleccione Forma de pago.",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                $("#forma_pago option[value=2]").attr("selected", true);
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
            if ($("#caja").val() == '') {
                Swal.fire({
                    icon: "info",
                    title: "No existe una caja activa, favor salga y vuelva a ingresar al sistema",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }

            serie = $("#serie").val();
            numero = $("#numero").val();
            $("#ser_imp").val(serie);
            $("#num_imp").val(numero);
            
            /**verificamos si tiene guias de remision asociadas***/
            cantidadGuiaRemision=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
            /*** fin de verificacion*/
            n = document.getElementById('tempde_tbl').rows.length;
            console.log(n);
            /**verificamos si es producto Individual y verifiamos que tenga la misma cantidad de serie**/
            if(cantidadGuiaRemision==0){
    	        if(n!=0){
    	        	 var  isSalir=false;
    	        		for(x=0;x<n;x++){
    	        			valor= "flagGenIndDet["+x+"]"; 
    	                    //var  valor_flagGenIndDet = document.getElementById(valor).value ;
    	                    valorAccion="detaccion["+x+"]"; 
    	                    var  valorAccionReal = document.getElementById(valorAccion).value ;
    	        			
    	        			/***verificamos si contiene almacenProducto diferente de null o vacio **/
    	                    if(valorAccionReal!='e'){

                                if($("#almacen").find('option').length == 1) {
                                    document.getElementById("almacenProducto["+x+"]").value = $("#almacen").val();
                                }


    		        			alm="almacenProducto["+x+"]"; 
    		                    var  isExisteAlmacenProducto = document.getElementById(alm).value;
                                var elementImportado = document.getElementById("esImportado["+x+"]");
                                var esImportado = parseInt(!elementImportado ? 0 : elementImportado.value);
    		        			if(esImportado == 0 && (isExisteAlmacenProducto==null || isExisteAlmacenProducto=="null"
                                     || isExisteAlmacenProducto=="" || isExisteAlmacenProducto=="0")){
    		        				valorPD= "proddescri["+x+"]"; 
    	                            var  valorPDVA = document.getElementById(valorPD).value ;
    	                        	alert("almacen Producto no  ingresado- "+valorPDVA);
    	                        	trTabla=x;
    	                        	document.getElementById(trTabla).style.background = "#ffadad";
    	                        	 $('#grabarComprobante').css('visibility', 'visible');
    	                             $('img#loading').css('visibility', 'hidden');
    	                        	return false;
    		        			}
    	                    }
    	                }
    	        		if(isSalir==true){
    	                	$('#grabarComprobante').css('visibility', 'visible');
    	        	       	$('img#loading').css('visibility', 'hidden');
    	                	return false;
    	                }
    	        		
    	        }else {
    	            //alert("Ingrese un producto.");
                    Swal.fire({
                        icon: "info",
                        title: "Ingrese un producto.",
                        html: "<b class='color-red'></b>",
                        showConfirmButton: true,
                        timer: 1500
                    });
    	            $('#grabarComprobante').css('visibility', 'visible');
    	            $('img#loading').css('visibility', 'hidden');
    	            return ;
    	        }
                   
            }
            if (cantidadGuiaRemision==0) {
                if (codigo == '')
                    url = base_url + "index.php/ventas/comprobante/comprobante_insertar";
                else
                    url = base_url + "index.php/ventas/comprobante/comprobante_modificar";
                
            } else {
               
                if (codigo == '')
                	 url = base_url + "index.php/ventas/comprobante/comprobante_insertar_ref";
                else
                    url = base_url + "index.php/ventas/comprobante/comprobante_modificar";
                
            } 
            dataString = $('#frmComprobante').serialize();
            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                dataType: 'json',
                beforeSend: function(data) {
                    $( "#salir").val(1);
                    $('img#loading').css('visibility', 'visible');
                },
                error: function(data) {
                    $('img#loading').css('visibility', 'hidden');
                    Swal.fire({
                                icon: "error",
                                title: "No se puedo completar la operación - Revise los campos ingresados.",
                                showConfirmButton: false,
                                timer: 2000
                            });
                },
                success: function(data){
                    if ( codigo == "")
                        docOper = "generado";
                    else
                        docOper = "actualizado";

                    switch (data.result){
                        case true:
                            icono = "success";
                            titulo = "Documento " + docOper + " correctamente.";location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;
                            break;
                        case false:
                            icono = "error";
                            titulo = "Verifique la información ingresada.";

                            $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                            $('#' + data.campo).css('background-color', '#FFC1C1').focus();
                            break;
                    }

                    Swal.fire({
                                icon: icono,
                                title: titulo,
                                showConfirmButton: true
                            }).then(function(){
                                if (data.result == true)
                                    location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;
                            });


                },
                complete: function(data){
                    $('img#loading').css('visibility', 'hidden');
                }
            });
        /*}
        else{
            Swal.fire({
                            icon: "info",
                            title: "Debe seleccionar un vendedor primero.",
                            html: "<b class='color-red'>Debe seleccionar un vendedor y confirmar las credenciales del mismo.</b>",
                            showConfirmButton: true,
                            timer: 3000
                        });
        }*/
        
    });
	
    $("#limpiarComprobante").click(function () {
        /*$( "#salir").val(1);
        url = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu + "/0/1";
        location.href = url;*/
        $("#guia").val("");

        $("#cboMoneda").val("1");
        $("#cboFormaPago").val("1");
        $("#cboPresupuesto").val("");
        $("#cboOrdencompra").val("");
        $("#cboGuiaRemision").val("");
        $("#direccionsuc").val("");
        $("#cboVendedor").val("");
        $("#cliente").val("");
        $("#ruc_cliente").val("");
        $("#nombre_cliente").val("");
        $("#proveedor").val("");
        $("#ruc_proveedor").val("");
        $("#buscar_proveedor").val("");
        $("#buscar_cliente").val("");
        $("#nombre_proveedor").val("");
        $("#detalle_comprobante").val("");
        $("#observacion").val("");
        $("#pedido").val("");
        $("#descuento").val("0");
        $("#preciototal").val("");
        $("#descuentotal").val("");
        $("#igvtotal").val("");
        $("#importetotal").val("");
        $("#preciototal_conigv").val("");
        $("#descuentotal_conigv").val("");
        $("#observacion").val("");
        $("#ordencompra").val("");
        $("#presupuesto_codigo").val("");
        $("#dRef").val("");
        $("#guiarem_codigo").val("");
        $("#docurefe_codigo").val("");
        $("#oc_cliente").val("");
        $("#listaGuiaremAsociados").val("");
        $("#caja").val("");
        $("#cajas").val("");
        n = document.getElementById('tempde_tblbody').rows.length;
        for(i=0;i<n;i++){
            eliminar_producto_temporal_all(i);
        }

    });




    $("#repo1").click(function () {
        $("#divRepo1").show();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").hide();
        $("#divRepo7").hide();
    });

    $("#repo6").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").show();
        $("#divRepo7").hide();
    });

    $("#repo2").click(function () {
        $("#divRepo1").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").hide();
        $("#divRepo7").hide();
        url = base_url + "index.php/ventas/comprobante/estadisticas";
        $.post(url, '', function (data) {
            $('#divRepo2').html(data).show();
        });
    });

    $("#repo3").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo3").show();
        $("#divRepo6").hide();
        $("#divRepo7").hide();
    });

    $("#repo4").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo5").hide();
        $("#divRepo4").show();
        $("#divRepo6").hide();
        $("#divRepo7").hide();
    });

    $("#repo5").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").show();
        $("#divRepo6").hide();
        $("#divRepo7").hide();
    });

    $("#repo7").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo7").show();
        $("#divRepo6").hide();
    });







    $("#lista_direcciones li").on('click', 'a', function () {
        $("#direccionsuc").val($(this).html());
        $('#lista_direcciones').slideUp("fast");
    });
    function activarBusqueda()
    {
        var action = base_url + "index.php/ventas/comprobante/buscar/"+tipo_oper+"/"+tipo_docu;
        var datos = $('#form_busqueda').serialize();
        $.ajax({
         url : action,
         data : datos,
         type: "POST",
         beforeSend: function(data){
             $('#cargando_datos').show();
         },
         success: function(data){
             $('#cargando_datos').hide();
            $('#contenedor-busqueda').html(data);
         },
         error: function(XHR, error){
             $('#cargando_datos').hide();
            console.log("Error");
         }
         });
    }

    $('#seriei, #numero, #nombre_cliente, #ruc_cliente, #ruc_proveedor, #nombre_proveedor').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==13){
            //activarBusqueda();
        }
    });

    $("#buscarComprobante").click(function () {
        /*document.forms['form_busqueda'].action = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu + "/";
        $("#form_busqueda").submit();*/
        //activarBusqueda();

    });
    $("#presupuesto").change(function () {
        if (this.value != '')
            $("#ordencompra").val('');
    });
    $("#ordencompra").change(function () {
        if (this.value != '')
            $("#presupuesto").val('');
    });
    $("#linkVerSerieNum").click(function () {
        var temp = $("#linkVerSerieNum p").html();
        var serienum = temp.split('-');
        $("#serie").val(serienum[0]);
        $("#numero").val(serienum[1]);
    });

    $('#buscar_cliente').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==20){
            if($(this).val()!=''){
                $('#linkSelecCliente').attr('href', base_url+'index.php/empresa/cliente/ventana_selecciona_cliente/'+$('#buscar_cliente').val()).click();
            }
        } 
    });
    
    $('#nombre_cliente').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==20){
            if($(this).val()!=''){
                $('#linkSelecCliente').attr('href', base_url+'index.php/empresa/cliente/ventana_selecciona_cliente/'+$('#nombre_cliente').val()).click();
            }
        }
    });

    $('#buscar_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 20) {
            if ($(this).val() != '') {
                $('#linkSelecProveedor').attr('href', base_url + 'index.php/empresa/proveedor/ventana_selecciona_proveedor/' + $('#buscar_proveedor').val()).click();
            }
        }
    });
    $('#nombre_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 20) {
            if ($(this).val() != '') {
                $('#linkSelecProveedor').attr('href', base_url + 'index.php/empresa/proveedor/ventana_selecciona_proveedor/' + $('#nombre_proveedor').val()).click();
            }
        }
    });
    $('#buscar_producto').keyup(function (e) {

        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                //url=base_url+'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val();
                //alert(url);
                $('#linkSelecProducto').attr('href', base_url + 'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val()+"/"+$("#almacen").val()).click();

            }
        }
    });
    //
    $('#docurefe_codigo').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val()!= '') {
            	
            	 if (tipo_oper == 'V') {
                     if ($('#cliente').val() == '') {
                         //alert('Debe seleccionar el cliente.');
                         Swal.fire({
                            icon: "info",
                            title: "Debe seleccionar el cliente.",
                            html: "<b class='color-red'></b>",
                            showConfirmButton: true,
                            timer: 1500
                        });
                         $('#nombre_cliente').focus();
                         return false;
                     }
            	 }else{
	            	 if ($('#proveedor').val() == '') {
	            		 //alert('Debe seleccionar el proveedor.');
                            Swal.fire({
                                icon: "info",
                                title: "Debe seleccionar el proveedor.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 1500
                            });
	                     $('#nombre_proveedor').focus();
	                     return false;
	            	 }
            	 }
            	
	                $.ajax({
	                    url: base_url + "index.php/ventas/comprobante/obtener_id_docuref",
	                    type: "POST",
	                    data: {
	                        serie_numero: $(this).val()
	                    },
	                    success: function (data) {
	                    	if(data!=""){
	                    		realizado=agregar_todo(data);
	                			if(realizado!=false){
	                	            $("#serieguiaverPre").hide(200);
	                	            $("#serieguiaverOC").hide(200);
	                	            $("#serieguiaverRecu").hide(200);
	                	            $('#ordencompra').val('');
	                			}
	                			$("#presupuesto_codigo").val("");
	                    	}else{
	                    		alert("No se encontro ninguna guia de remisión.");
	                    		
	                    	}
	                    }
	                });
            	
            }
        }
    });


    $('#cantidad').bind('blur', function (e) {
        tipo_oper = $("#tipo_oper").val();
        flagGenInd = $("#flagGenInd").val();
        
        if (flagGenInd == 'I') {
                if (tipo_oper == 'V') {
                    if ($(this).val() != '') {
                        var cantidad = parseInt($(this).val());
                        var stock = parseInt($('#stock').val());
                        if (cantidad > stock) {
                            alert('La cantidad no debe ser mayor al stock.');
                            $(this).val('').focus();
                            return false;
                        }

                        ventana_producto_serie_1();
                    }
                } else if (tipo_oper == 'C') {
                    ventana_producto_serie_1();
                }
        }
    });
})

var limite_detalle = 15;
function getLimite() {
    return limite_detalle;
}

function setLimite(limite) {
    limite_detalle = limite;
}

function ver_reporte_pdf() {
    var fechai = $('#fechai').val();
    var fechaf = $('#fechaf').val();
    var cliente = $('#cliente').val();//ruc_proveedor
    var producto = $('#producto').val();
    var aprobado = $('#aprobado').val();
    var ingreso = $('#ingreso').val();

    var tipo_oper = $("#tipo_oper").val();
    tipo_oper="V";

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf/" + fechai + '_' + fechaf + '_' + cliente + '_' + producto + '_' + aprobado + '_' + ingreso+"/"+tipo_oper;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ver_reporte_pdf_factura() {
    var fechai = $('#f_fechai').val();
    var fechaf = $('#f_fechaf').val();
    var cliente = $('#cliente_f').val();//ruc_proveedor
    var producto = $('#producto').val();
    var aprobado = $('#aprobado_f').val();
    var ingreso = $('#ingreso_f').val();

    var tipo_oper = $("#tipo_oper").val();
    tipo_oper="V";

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf_factura/" + fechai + '_' + fechaf + '_' + cliente + '_' + producto + '_' + aprobado + '_' + ingreso+"/"+tipo_oper;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ver_reporte_pdf_ventas() {
    
    var prod = $("#productoDescripcion").val();

    var anio = $("#anioVenta").val();
    var mes = $("#mesventa").val();
    var fech1 = $("#fech1").val();
    var fech2 = $("#fech2").val();
    //var depar = $("#cboDepartamento").val();
    //var prov = $("#cboProvincia").val();
    //var dist = $("#cboDistrito").val();
    var tipodocumento = $("#tipodocumento").val();
    var Prodcod="";

    alert(anio + " " +mes);
    //var Prodcod = $("#reporteProducto").val();
    if(anio=="0") {anio="--";} 
    if(mes=="")   {mes="--";} 
    //if(depar=="00")  {depar="--";}
    //if(prov=="00")  {prov="--";}
    //if(dist=="00")  {dist="--";}
    if(Prodcod==""|| prod =="")  {Prodcod="--";}

    if(tipodocumento=="")  {tipodocumento="--";}

    var datafechaIni="";var datafechafin="";

    if(fech1=="") {
        fech1="--";
    }else{
        fechai=$("#fech1").val().split("/"); 
        fech1=fechai[2]+"-"+fechai[1]+"-"+fechai[0];
    }

    if(fech2=="") {
        fech2="--";
    }else{
        fechaf=$("#fech2").val().split("/");
        fech2=fechaf[2]+"-"+fechaf[1]+"-"+fechaf[0];

    }

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf_ventas/" + anio+"/" + mes+"/" + fech1+"/" + fech2+"/"+tipodocumento;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
 }

function estadisticas_compras_ventas(tipo) {
    var anio = $("#anioVenta2").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas/" + tipo + "/" + anio;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function estadisticas_compras_ventas_mensual(tipo) {
    var anio = $("#anioVenta3").val();
    var mes = $("#mesVenta3").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas_mensual/" + tipo + "/" + anio + "/" + mes + "";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function estadisticas_compras_ventas_mensual_excel(tipo) {
    var anio = $("#anioVenta3").val();
    var mes = $("#mesVenta3").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas_mensual_excel/" + tipo + "/" + anio + "/" + mes + "";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function editar_comprobante(comprobante) {
    //alert(base_url)
    location.href = base_url + "index.php/ventas/comprobante/comprobante_editar/" + comprobante + "/" + tipo_oper + "/" + tipo_docu;
}

function eliminar_comprobante(comprobante) {
    if (confirm('Esta seguro que desea eliminar este comprobante?')) {
        dataString = "comprobante=" + comprobante;
        url = base_url + "index.php/ventas/comprobante/comprobante_eliminar";
        $.post(url, dataString, function (data) {
            location.href = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
        });
    }
}

/**
 * Metodo para enlazar el boton imprimir
 * Se le manda como parametro img "0" para no mostrar la imagen
 * @param comprobante
 */
function ver_comprobante_pdf(comprobante) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete/" + tipo_oper + "/" + comprobante + "/" + tipo_docu + "/1";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function disparador(comprobante, pos) {
    updateFecha = false;

    $.ajax({
        type: "POST",
        dataType: "json",
        url: base_url + "index.php/ventas/comprobante/getFechaE",
        data:{ comprobante: comprobante },
        success: function(data){
            updateFecha = data.update;
            fecha = data.fecha_hoy;
        },
        complete: function(data){

            if ( updateFecha == true ){
                Swal.fire({
                    icon: "info",
                    title: "Notificación",
                    html: "<b>El documento debe ser enviado con la fecha actual.<br>Si continua la fecha se actualizara automaticamente.</b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if ( result.value == true){
                        execute_disparador(comprobante, pos);
                        $(".fecha_data_"+pos).html(fecha);
                    }
                    else{
                        Swal.fire({
                            icon: "warning",
                            title: "Envio cancelado",
                            html: "<b>La aprobación fue cancelada.</b>",
                            timer: 2000
                        });
                    }
                });
            }
            else
                execute_disparador(comprobante, pos);
        }
    });
}

function execute_disparador(comprobante, pos){
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/disparador/" + tipo_oper + "/" + comprobante + "/" + tipo_docu;

    var disparadorHtml = $(".disparador_data_"+pos).html();
    var sendHtml = $(".enviarcorreo_data_"+pos).html("");
    var editarHtml = $(".editar_data_"+pos).html();

    $(".editar_data_"+pos).html("");
    $(".disparador_data_"+pos).html("");

    $.ajax({
        type: "POST",
        url: url,
        data: { comprobante: comprobante },
        dataType: 'json',
        beforeSend: function (data) {
            $(".disparador_"+pos+" .icon-loading").show();
        },
        error: function (data) {
            $(".disparador_"+pos+" .icon-loading").hide();
        },
        success: function (data) {
            switch (data.result){
                case 'success':
                    editarHtml = '<img src="' + base_url + 'public/images/completado.png" width="16" height="16" border="0" title="Completado">';
                    $(".editar_data_"+pos).html(editarHtml);

                    if (tipo_oper == "V"){
                        sendHtml = '<a onclick="open_mail(' + comprobante + ')" href="#" class="enviarcorreo"><img src="' + base_url + 'public/images/send.png" width="16" height="16" border="0" title="Enviar via correo"></a>';
                        garantiaHtml = '<a href="' + base_url + 'index.php/ventas/comprobante/comprobante_garantia/' + comprobante + '" data-fancybox data-type="iframe"><img src="' + base_url + 'public/images/compromiso.png" width="16" height="16" border="0" title="ver garantia"></a>';
                        img_estadoHtml = '<a href="' + base_url + 'index.php/seguridad/usuario/ventana_confirmacion_usuario2/FPP1/' + comprobante + '/'+ tipo_oper +'/'+tipo_docu+'" data-fancybox data-type="iframe"><img src="' + base_url + 'public/images/active.png" alt="Activo" title="Activo"/></a>';
                        if ( tipo_oper == "V" && tipo_docu != "N"){
                            compHTML = '<a href="javascript:;" onclick="abrir_pdf_envioSunat(' + comprobante + ')" target="_parent"><img src="' + base_url + 'public/images/pdf-sunat.png" width="16" height="16" border="0" title="pdf sunat"></a>';
                            $(".pdfSunat_data_"+pos).html(compHTML);
                        }
                        $(".enviarcorreo_data_"+pos).html(sendHtml);
                        $(".compromiso_data_"+pos).html(garantiaHtml);
                        $(".img_estado_data_"+pos).html(img_estadoHtml);
                    }
                    break;
                case 'error':

                    disparadorHtml += '<br> <span class="detallesWrong">Denegado <span class="detallesWrong2"> ' + data.msg + ' </span> </span>';
                    //editarHtml += '<a href="javascript:;"" onclick="editar_comprobante('+comprobante+')"" target="_parent"><img src="' + base_url + 'images/modificar.png" width="16" height="16" border="0" title="Modificar"></a>';
                    $(".disparador_"+pos+" .disparador_data_"+pos).html(disparadorHtml);
                    $(".editar_data_"+pos).html(editarHtml);
                    break;
            }
            $(".disparador_"+pos+" .icon-loading").hide();
        }
    });
}

function asignar_lotes(comprobante, pos){

    $(".disparador_data_"+pos).html("");
    var url = base_url+"index.php/ventas/comprobante/asignar_lotes/"+comprobante;

    $.ajax({
        type: "POST",
        url: url,
        data: { comprobante: comprobante },
        dataType: 'json',
        beforeSend: function (data) {
            $(".disparador_"+pos+" .icon-loading").show();
        },
        error: function (data) {
            $(".disparador_"+pos+" .icon-loading").hide();
        },
        success: function (data) {
            switch (data.result){
                case 'success':
                    var aprobar = '<a href="javascript:;" onclick="disparador(' + comprobante + ', ' + pos + ')">Aprobar</a>';
                    $(".disparador_data_"+pos).html(aprobar);
                    break;
                case 'warning':
                    alert(data.msg);
                    /*Swal.fire({
                                type: 'warning',
                                title: data.msg,
                                showConfirmButton: false,
                                timer: 1500
                            });*/
                    var aprobar = '<a href="javascript:;" onclick="asignar_lotes(' + comprobante + ', ' + pos + ')">Asignar Lotes</a>';
                    $(".disparador_data_"+pos).html(aprobar);
                    break;
                case 'error':
                    alert(data.msg);
                    /*Swal.fire({
                                type: 'warning',
                                title: data.msg,
                                showConfirmButton: false,
                                timer: 1500
                            });*/
                    var aprobar = '<a href="javascript:;" onclick="asignar_lotes(' + comprobante + ', ' + pos + ')">Asignar Lotes</a>';
                    $(".disparador_data_"+pos).html(aprobar);
                    break;
            }
            $(".disparador_"+pos+" .icon-loading").hide();
        }
    });
}

function canjeToGuia(comprobante, pos){

    var url = base_url+"index.php/ventas/comprobante/insertar_guiarem/"+comprobante;
    var contenido = $(".guiarem_data_"+pos).html();
    $(".guiarem_data_"+pos).html("");

    $.ajax({
        type: "POST",
        url: url,
        data: { comprobante: comprobante },
        dataType: 'json',
        beforeSend: function (data) {
            $(".guiarem_c_"+pos+" .icon-loading").show();
        },
        error: function (data) {
            $(".guiarem_c_"+pos+" .icon-loading").hide();
        },
        success: function (data) {
            switch (data.result){
                case 'success':
                    $(".guiarem_data_"+pos).html(data.pdf);
                    break;
                case 'error':
                    alert(data.msg);
                    $(".guiarem_data_"+pos).html(contenido);
                    break;
            }
            $(".guiarem_c_"+pos+" .icon-loading").hide();
        }
    });
}

/*function canjeToComprobante(OC, pos){

    $(".loading_c_"+pos).show();
    $(".cResult_"+pos).html("");
    
    var url = base_url+"index.php/compras/ocompra/insertar_comprobante/"+OC;

    $.ajax({
        type: "POST",
        url: url,
        data: { idOC: OC },
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
        },
        error: function (data) {
        },
        success: function (data) {
            switch (data.result){
                case 'success':
                    $(".cResult_"+pos).html(data.pdf);
                    $(".loading_c_"+pos).hide();
                    break;
                case 'error':
                    alert(data.msg);
                    $(".loading_c_"+pos).hide();
                    break;
            }
        }
    });
}*/

function comprobante_download_excel( id ) {
    location.href = base_url + "index.php/ventas/comprobante/comprobante_descarga_excel/" + id;
}

function atras_comprobante() {
    location.href = base_url + "index.php/ventas/comprobante/comprobantes";
}

function agregar_producto_comprobante() {
    flagBS = $("#flagBS").val();

    if ($("#producto").val() == '') {
        $("#producto").focus();
        alert('Ingrese el producto.');
        return false;
    }
    if ($("#cantidad").val() == '') {
        $("#cantidad").focus();
        alert('Ingrese una cantidad.');
        return false;
    }
    if (flagBS == 'B' && $("#unidad_medida").val() == '0') {
        $("#unidad_medida").focus();
        alert('Seleccine una unidad de medida.');
        return false;
    }

    codproducto = $("#codproducto").val();
    producto = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    descuento = $("#descuento").val();
    igv = parseFloat($("#igv").val());
    cantidad = $("#cantidad").val();
    almacenProducto=$("#almacenProducto").val();
    if ($("#precio").val() != '')
        precio_conigv = $("#precio").val();
    else
        precio_conigv = 0;
    if (tipo_docu != 'B' && tipo_docu != 'N' && contiene_igv == '1')
        precio = (precio_conigv * 100 / (igv + 100))
    else {
        //precio=precio_conigv;
        //precio_conigv = (precio_conigv*(100+igv)/100);
        precio = (precio_conigv * 100 / (igv + 100));

    }
    stock = parseFloat($("#stock").val());
    costo = parseFloat($("#costo").val());
    unidad_medida = '';
    nombre_unidad = '';
    if (flagBS == 'B') {
        unidad_medida = $("#unidad_medida").val();
        nombre_unidad = $('#unidad_medida option:selected').html()
    }
    flagGenInd = $("#flagGenInd").val();
    n = document.getElementById('tblDetalleComprobante').rows.length;
    var limit = getLimite();
    if (n >= limit) {

        alert('Limite del detalle de Documento');
        return false
    }
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }


    fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" >';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="8%"><div align="center">' + codproducto + '</div></td>';
    fila += '<td width="36%"><div align="left"><input type="text" class="cajaGeneral ' + (/adelanto/.test(nombre_producto.toLowerCase()) ? 'percent-box' : '') + '" size="60" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" onblur="' + (/adelanto/.test(nombre_producto.toLowerCase()) ? 'verificarPorcentaje(event)' : '') + '" />';
    fila += '</div></td>';
    fila += '<td width="15%"><div align="left">';
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="text" size="10" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" style="width:29px;text-align: right;" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
    else
        fila += '<input type="text" size="10" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" style="width:29px;text-align: right;" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;

    if(flagGenInd!=null && flagGenInd=='I'){
    	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" class="imgBoton"></a>';
    	/**vamos al metodo de producto serie para eliminar el de la secciontemporal y agregar el de la seccion Real**/
        var url = base_url+"index.php/almacen/producto/agregarSeriesProductoSessionReal/"+producto+"/"+almacenProducto;
         $.get(url,function(data){});
         
   }
    fila += '</div></td>';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        fila += '<td width="6%"><div align="center"><input type="text" style="text-align: right;" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" style="text-align: right;" class="cajaGeneral" value="' + precio.format(false) + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" style="text-align: right;" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }
    else {
        fila += '<td width="6%"><div align="center"><input type="text" style="text-align: right;" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input style="text-align: right;" type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio.format(false) + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" style="text-align: right;" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }
    
    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" style="text-align: right;" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none;" ><div align="center">';
    fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento + '">';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        if (tipo_oper == 'C')
            fila += '<input type="text" style="text-align: right;" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
        else
            fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';

    } else {
        fila += '<input type="hidden" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    }
    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
    fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
    fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
    fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
    fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
    fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
    fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
    fila += '<input type="text" size="5" style="text-align: right;" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
    
    /**verificamos si es un servicio y a la vez verificampos si contiene guais de remiisiones asociadas**/
    var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
    if(flagBS=='S'){
    	if(total>0){
    		/**es servicio se le asigna codigo de guiaremision 0***/
    		codigoGuiarem=0;
            /**se agrega la guia de remision asociada***/
            fila += '<input type="hidden" name="codigoGuiarem[' + n + ']" id="codigoGuiarem[' + n + ']" value="' +codigoGuiarem + '">';
            /**fin de agregar la guia de remision**/
    	}
    }
    /**fin de verificacion **/
    
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    var my_pos_n = n;

    mostrarPopUpSeleccionarAlmacen(n, false);

    if (tipo_docu != 'B' && tipo_docu != 'N')
        calcula_importe(my_pos_n); //Para facturas o comprobantes
    else
        calcula_importe(my_pos_n); //Para boletas

    inicializar_cabecera_item();
    $("#buscar_producto").focus();

    return true;
}
function agregar_fila(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_unidad, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
    /*xxx
     *producto          = codigo del producto
     *codproducto       = codigo interno del producto
     *nombre_producto   = nombre del producto
     *descuento
     *flagBS            = B -> Bien S->Servicio
     */

    //igv = parseInt($("#igv").val());

    if (tipo_docu != 'B' && tipo_docu != 'N' && contiene_igv == '1')
        precio = (precio_conigv * 100 / (igv + 100))
    else {
        precio = precio_conigv;
        precio_conigv = (precio_conigv * (100 + igv) / 100);
    }
    /*if(flagBS=='B'){
     unidad_medida = $("#unidad_medida").val();
     nombre_unidad = $('#unidad_medida option:selected').html()
     }*/
    n = document.getElementById('tblDetalleComprobante').rows.length;
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }
    fila = '<tr class="' + clase + '">';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="10%"><div align="center">' + codproducto + '</div></td>';
    fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
    fila += '<td width="10%"><div align="left">';
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="text" size="1" maxlength="10" style="text-align: right;" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
    else
        fila += '<input type="text" size="5" style="text-align: right;" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

    fila += '</div></td>';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        fila += '<td width="6%"><div align="center"><input type="text" style="text-align: right;" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv.format(false) + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input type="text" style="text-align: right;" size="5" maxlength="10" class="cajaGeneral" value="' + precio_sinigv.format(false) + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input style="text-align: right;" type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + precio.format(false) + '" readonly="readonly">';
    }
    else {
        fila += '<td width="6%"><div align="center"><input type="text" style="text-align: right;" size="5" maxlength="10" class="cajaGeneral" value="' + precio.format(false) + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="0" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
        fila += '<td width="6%"><div align="center"><input style="text-align: right;" type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly"></div></td>';
    }
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<td width="6%" style="display:none;" ><div align="center"><input style="text-align: right;" type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" value="' + igv.format(false) + '" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none;" ><div align="center">';
    fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    else
        fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
    fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
    fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
    fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
    fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
    fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
    fila += '<input type="text" style="text-align: right;" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + importe.format(false) + '" readonly="readonly">';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    if (tipo_docu != 'B' && tipo_docu != 'N')
        calcula_importe(n); //Para facturas o comprobantes
    else
        calcula_importe(n); //Para boletas

    inicializar_cabecera_item();

    return true;
}
function eliminar_producto_comprobante(n) {
    if (confirm('Esta seguro que desea eliminar este producto?')) {
        a = "detacodi[" + n + "]";
        b = "detaccion[" + n + "]";
        fila = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
        if (tipo_docu != 'B' && tipo_docu != 'N')
            calcula_totales();
        else
            calcula_totales_conigv();
    }
}
function calcula_importe(n) {
    a = "prodpu[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento[" + n + "]";
    d = "prodigv[" + n + "]";
    e = "prodprecio[" + n + "]";
    f = "prodimporte[" + n + "]";
    g = "prodigv100[" + n + "]";
    h = "proddescuento100[" + n + "]";
    i = "prodpu_conigv[" + n + "]";
    pu = document.getElementById(a).value;
    pu_conigv = document.getElementById(i).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio = (pu * cantidad);
    //preciodescuento= (pu_conigv*cantidad);
    preciodescuento= (pu*cantidad);
    total_dscto = (preciodescuento * descuento100 / 100);
    precio2 = (precio - parseFloat(total_dscto));
    if (pu_conigv == '')
        total_igv = (precio2 * igv100 / 100);
    else
        total_igv = ((pu_conigv - pu) * cantidad);

    importe = (precio - parseFloat(total_dscto) + parseFloat(total_igv));

    /*document.getElementById(c).value = total_dscto.format(false);
    document.getElementById(d).value = total_igv.format(false);
    document.getElementById(e).value = precio.format(false);
    document.getElementById(f).value = importe.format(false);*/

    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
    document.getElementById(e).value = precio;
    document.getElementById(f).value = importe;    

    calcula_totales();
}
function calcula_importe_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento_conigv[" + n + "]";
    e = "prodprecio_conigv[" + n + "]";
    f = "prodimporte[" + n + "]";
    g = "prodigv100[" + n + "]";
    h = "proddescuento100[" + n + "]";

    pu_conigv = document.getElementById(a).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio_conigv = (pu_conigv * cantidad);
    total_dscto_conigv = (precio_conigv * descuento100 / 100);
    precio2 = (precio_conigv - parseFloat(total_dscto_conigv));

    importe = (precio_conigv - parseFloat(total_dscto_conigv));
    document.getElementById(c).value = total_dscto_conigv.format(false);
    document.getElementById(e).value = precio_conigv.format(false);
    document.getElementById(f).value = importe.format(false);

    calcula_totales_conigv();
}
function calcula_importe2(n) {

    var t_doc = $('#' + n).attr('t-doc');
    if (t_doc === 'F') {
        a = "prodpu[" + n + "]";
        b = "prodcantidad[" + n + "]";
        e = "prodigv[" + n + "]";
        f = "prodprecio[" + n + "]";
        g = "prodimporte[" + n + "]";
        h = "prodpu_conigv[" + n + "]";

        valor_igv = $("#igv").val();
        pu = parseFloat(document.getElementById(a).value);
        cantidad = parseFloat(document.getElementById(b).value);

        descuento = $('#' + n).find('.proddescuento').val();

        total_igv = parseFloat(document.getElementById(e).value);
        precio_u = parseFloat(document.getElementById(h).value);


        dsc = parseFloat(descuento / 100);
        importe = ((pu * cantidad) - ((pu * cantidad) * dsc));

        t_igv = ((importe * valor_igv) / 100);
        importe_total = (importe + t_igv);
        document.getElementById(g).value = importe_total.format(false);
        document.getElementById(e).value = t_igv.format(false);
        document.getElementById(f).value = importe.format(false);
        calcula_totales();
    } else {

        a = "prodimporte[" + n + "]";

        importe = parseFloat(document.getElementById(a).value);

        descuento = $('#' + n).find('.proddescuento').val();

        dsc = (parseFloat(descuento / 100));

        t_importe = (importe - (importe * dsc));

        document.getElementById(a).value = t_importe.format(false);
        calcula_totales3();
    }
}

function calcula_totales3() {
    var lht = $('#tblDetalleComprobante tr').length;
    var i = 0;
    var igv = $('#igv').val();

    var importe_total = 0;

    for (i; i < lht; i++) {
        a = "prodimporte[" + i + "]";
        importe_total += parseFloat(document.getElementById(a).value);
    }
    $('#porcentaje').val('0.00');
    $('#descuentotal_conigv').val('0.00');

    $('#importetotal').val(importe_total.format(false));
    $('#preciototal_conigv').val((importe_total / (1 + (parseFloat(igv) / 100))).format(false));

}
function descuento_porcentaje() {

    porcentaje = $('#porcentaje').val();
    if (isNaN(porcentaje)) {
        porcentaje = 0;
    }

    sub_total = $('#preciototal').val();

    if (isNaN(sub_total)) {
        sub_total = 0;
    }

    igv = parseInt($("#igv").val());
    descuento = money_format((sub_total * porcentaje) / 100);
    total = sub_total - descuento;
    valor = (total * igv) / 100;
    importe_total = total + valor;
    $('#igvtotal').val(valor.toFixed(2));
    $('#descuentotal').val(descuento.toFixed(2));
    $('#importetotal').val(importe_total.toFixed(2));

}
function incremento_visa() {
    calcula_totales();
    importe = $('#importetotal').val();
    // sub = $('#importetotal').val();

    igv = parseInt($('#igv').val()) + parseInt(100);
    visa = parseInt($('#visa').val()) + parseInt(100);

    if (isNaN(visa)) {
        visa = 0;
    }
    total = (importe * visa / 100);
    //importe_total= importe+total;
    sub_total = (total / (igv / 100));
    igv_total = total - sub_total;
    $('#importetotal').val(total.toFixed(2));
    $('#igvtotal').val(igv_total.toFixed(2));
    $('#preciototal').val(sub_total.toFixed(2));

    //if($('#visa').val()== 0 ){ calcula_totales();}


}
function cargar_provincia(obj){
    departamento = obj.value;
    provincia    = "01";
   
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
   
}
function cargar_distrito(obj){
    departamento = $("#cboDepartamento").val();
    provincia    = obj.value;
   
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
   
}
function descuento_nuevo() {
    alert('aki');
    descuento = $('descuentototal').val();
    sub_total = $('preciototal').val();
    igv = parseInt($("#igv").val());

    total = sub_total - descuento;
    valor = (total * igv) / 100;

    $('igvtotal').val(valor);
    importe_total = total + valor;
    $('importetotal').val(importe_total);
}
function calcula_importe2_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento_conigv[" + n + "]";
    f = "prodprecio_conigv[" + n + "]";
    g = "prodimporte[" + n + "]";
    pu_conigv = parseFloat(document.getElementById(a).value);
    cantidad = parseFloat(document.getElementById(b).value);
    descuento_conigv = parseFloat(document.getElementById(c).value);
    importe = money_format((pu_conigv * cantidad) - descuento_conigv);
    document.getElementById(g).value = importe;

    calcula_totales_conigv();
}
/*
function calcula_totales() {
    var n = document.getElementById('tblDetalleComprobante').rows.length;
    var importe_total = 0;
    var igv_total = 0;
    var descuento_total = 0;
    var precio_total = 0;

    for (var i = 0; i < n; i++) {
        var a = "prodimporte[" + i + "]";
        var b = "prodigv[" + i + "]";
        var c = "proddescuento[" + i + "]";
        var d = "prodprecio[" + i + "]";
        var e = "detaccion[" + i + "]";
        
        if (document.getElementById(e).value != 'e' && document.getElementById(e).value != 'EE') {
            //importe = parseFloat(document.getElementById(a).value);
            //igv = parseFloat(document.getElementById(b).value);
            descuento = parseFloat(document.getElementById(c).value);
            precio = parseFloat(document.getElementById(d).value);
            //importe_total = money_format(importe + importe_total);
            //igv_total = money_format(igv + igv_total);
            descuento_total = money_format(descuento + descuento_total);
            precio_total = money_format(precio + precio_total);
        }
    }

    var igv100 = parseInt($("#igv").val());
    igv_total = money_format(precio_total * igv100 / 100);
    importe_total = money_format(precio_total + igv_total - descuento_total);


    $("#importetotal").val(importe_total.toFixed(2));  //val(importe_total.toFixed(2))
    $("#igvtotal").val(igv_total.toFixed(2));  //val(igv_total.toFixed(2))
    $("#descuentotal").val(descuento_total.toFixed(2));

    if (tipo_oper == 'C')
        $("#preciototal").val(precio_total.toFixed(2));  //val(precio_total.toFixed(2))
    else
        $("#preciototal").val(precio_total.toFixed(2));  //val(precio_total.toFixed(2))
}
*/

function calcula_totales(){
    n = document.getElementById('tblDetalleComprobante').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;
     ////aumentado
    igvtotal=0;
    importetotal=0;
    preciototal=0;
    ///
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]"
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        e  = "detaccion["+i+"]";        

        if(document.getElementById(e) != null && document.getElementById(e).value != 'e' && document.getElementById(e).value != 'EE'){
            importe = parseFloat(document.getElementById(a).value);
            igv = parseFloat(document.getElementById(b).value);
            descuento = parseFloat(document.getElementById(c).value);
            precio = parseFloat(document.getElementById(d).value);
            importe_total = (importe + importe_total);
            igv_total = (igv + igv_total);
            descuento_total = (descuento + descuento_total);
            precio_total = (precio + precio_total);
        }
    }
    igvtotal=((importe_total * $("#igv").val()) / 118);
       preciototal=(importe_total-igvtotal);
       //importetotal=precio_total+igv_total;
       importetotal = importe_total;
    ///
    /*
    $("#importetotal").val(importetotal.format(false));  //val(importe_total)
    $("#igvtotal").val(igv_total.format(false));  //val(igv_total)
    $("#descuentotal").val(descuento_total.format(false));
    $("#preciototal").val(precio_total.format(false));  //val(precio_total)
    */
    $("#importetotal").val(importetotal.format(false));  //val(importe_total)
    $("#igvtotal").val(igvtotal.format(false));  //val(igv_total)
    $("#descuentotal").val(descuento_total.format(false));
    $("#preciototal").val(preciototal.format(false));  //val(precio_total)
}
function calcula_totales_conigv() {
    n = document.getElementById('tblDetalleComprobante').rows.length;
    importe_total = 0;
    descuento_total_conigv = 0;
    precio_total_conigv = 0;
    for (i = 0; i < n; i++) {//Estanb al reves los campos
        a = "prodimporte[" + i + "]"
        c = "proddescuento_conigv[" + i + "]";
        d = "prodprecio_conigv[" + i + "]";
        e = "detaccion[" + i + "]";
        if (document.getElementById(e).value != 'e') {
            importe = parseFloat(document.getElementById(a).value);
            descuento_conigv = parseFloat(document.getElementById(c).value);
            precio_conigv = parseFloat(document.getElementById(d).value);
            importe_total = money_format(importe + importe_total);
            descuento_total_conigv = money_format(descuento_conigv + descuento_total_conigv);
            precio_total_conigv = money_format(precio_conigv + precio_total_conigv);
        }
    }


    $("#importetotal").val(importe_total.toFixed(2));
    $("#descuentotal_conigv").val(descuento_total_conigv.toFixed(2));
    $("#preciototal_conigv").val(precio_total_conigv.toFixed(2));
}
function mostrar_productos_factura(guias) {
    for (i = 0; i < guias.length; i++) {
        var codigo_guia = guias[i];
        url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + codigo_guia + "/C",
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    n = document.getElementById('tblDetalleComprobante').rows.length;
                    id_tr_dguia = n;
                    producto = item.PROD_Codigo;
                    codigo = item.PROD_CodigoInterno;
                    nombre = item.PROD_Nombre;
                    cantidad = item.GUIAREMDETC_Cantidad;
                    pu = item.GUIAREMDETC_Pu;
                    importe = item.GUIAREMDETC_Pu_ConIgv;
                    fila = '<tr id="dguia_' + id_tr_dguia + '">';
                    fila += '<td>';
                    fila += '<input type="hidden" name="producto[' + n + ']" id="producto[' + n + ']" value="' + producto + '"/>';
                    fila += codigo;
                    fila += '</td>';
                    fila += '<td>' + nombre + '</td>';
                    fila += '<td>' + cantidad + '</td>';
                    fila += '<td>' + pu + '</td>';
                    fila += '<td>' + importe + '</td>';
                    fila += '</tr>';
                    $("#tblDetalleComprobante").append(fila);
                });
            });
    }
}
// para agregar productos cuando ingreso por el seguimiento de orden
function mostrar_productos_factura(guias) {
    for (i = 0; i < guias.length; i++) {
        var codigo_guia = guias[i];
        url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + codigo_guia + "/C",
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    var igv = 18;
                    flagBS = $("#flagBS").val();
                    precio_conigv = parseFloat(item.GUIAREMDETC_Pu_ConIgv);
                    precio = parseFloat(item.GUIAREMDETC_Subtotal);
                    codproducto = item.PROD_Codigo;
                    producto = item.PROD_CodigoInterno;
                    unidad_medida = item.UNDMED_Codigo;
                    nombre_unidad = item.UNDMED_Simbolo;
                    nombre_producto = item.PROD_Nombre;
                    cantidad = item.GUIAREMDETC_Cantidad;
                    stock = '0'
                    costo = '0';
                    n = document.getElementById('tblDetalleComprobante').rows.length;
                    j = n + 1;
                    if (j % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }
                    fila = '<tr class="' + clase + '">';
                    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(' + n + ');">';
                    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                    fila += '</a></strong></font></div></td>';
                    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                    fila += '<td width="10%"><div align="center">';
                    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '">';
                    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + codproducto + '">' + producto;
                    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                    fila += '</div></td>';
                    fila += '<td><div align="left">';
                    fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '">';
                    fila += '</div></td>';
                    fila += '<td width="10%"><div align="left">';
                    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="10" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
                    fila += '</div></td>';
                    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">'
                    fila += '<input type="hidden"  value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly"></div></td>';
                    fila += '<td width="6%"><div align="center">';
                    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                    fila += '</div></td>';
                    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly></div></td>';
                    fila += '<td width="6%" style="display:none;"><div align="center">';
                    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                    fila += '</div></td>';
                    fila += '</tr>';
                    $("#tblDetalleComprobante").append(fila);
                    calcula_importe(n);
                });
            });
    }
    return true;
}
function modifica_pu_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    g = "prodigv100[" + n + "]";
    i = "prodpu[" + n + "]";

    pu_conigv = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value);

    pu = money_format(100 * pu_conigv / (100 + igv100));

    if (isNaN(pu_conigv)) {
        pu_conigv = 0;
    }
    if (isNaN(igv100)) {
        igv100 = 0;
    }
    if (isNaN(pu)) {
        pu = 0;
    }
    document.getElementById(i).value = pu;

    calcula_importe(n);
}
function modifica_pu(n) {
    a = "prodpu[" + n + "]";
    g = "prodigv100[" + n + "]";
    i = "prodpu_conigv[" + n + "]";
    pu = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value);

    pu_conigv = money_format(pu * (100 + igv100) / 100);

    if (isNaN(pu_conigv)) {
        pu_conigv = 0;
    }
    if (isNaN(igv100)) {
        igv100 = 0;
    }
    if (isNaN(pu)) {
        pu = 0;
    }

    document.getElementById(i).value = pu_conigv;

    calcula_importe(n);
}
function modifica_descuento_total() {
    var descuento = $('#descuento').val();
    var n = document.getElementById('tblDetalleComprobante').rows.length;
    for (var i = 0; i < n; i++) {
        a = "proddescuento100[" + i + "]";
        document.getElementById(a).value = descuento;
        calcula_importe(i);
    }
    /*for (i = 0; i < n; i++) {
    }*/
    calcula_totales();
}
function modifica_igv_total() {
    igv = $('#igv').val();
    n = document.getElementById('tblDetalleComprobante').rows.length;
    for (i = 0; i < n; i++) {
        a = "prodigv100[" + i + "]";
        document.getElementById(a).value = igv;
    }
    for (i = 0; i < n; i++) {
        calcula_importe(i);
    }
    calcula_totales();
}
function listar_unidad_medida_producto(producto) {
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + producto;
    select_umedida = document.getElementById('unidad_medida');
    options_umedida = select_umedida.getElementsByTagName("option");

    var num_option = options_umedida.length;
    for (i = 1; i <= num_option; i++) {
        select_umedida.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode(":: Seleccione ::");
    opt.appendChild(texto);
    opt.value = "0";
    select_umedida.appendChild(opt);
    $("#cantidad").val('');
    $("#precio").val('');

    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            codigo = item.UNDMED_Codigo;
            descripcion = item.UNDMED_Descripcion;
            simbolo = item.UNDMED_Simbolo;
            nombre_producto = item.PROD_Nombre;
            nombrecorto_producto= item.PROD_NombreCorto; //Como se obtiene este campo
            marca = item.MARCC_Descripcion;
            modelo = item.PROD_Modelo;
            presentacion = item.PROD_Presentacion;
            opt = document.createElement('option');
            texto = document.createTextNode(descripcion);
            opt.appendChild(texto);
            opt.value = codigo;
            if (i == 0)
                opt.selected = true;
            select_umedida.appendChild(opt);
        });
        var nombre;
        if (nombrecorto_producto)
            nombre = nombrecorto_producto;
        else
            nombre = nombre_producto;

        if (flagBS == 'B') {
          if(marca)
             nombre+=' / '+marca;
             if(modelo)
             nombre+=' /  '+modelo;
             if(presentacion)
             nombre+=' /  '+presentacion;
        }
        $("#nombre_producto").val(nombre);
        listar_precios_x_producto_unidad();
    });
}

function listar_precios_x_producto_unidad() {
    producto = $("#producto").val();
    unidad = $("#unidad_medida").val();
    moneda = $("#moneda").val();
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url = base_url + "index.php/almacen/producto/listar_precios_x_producto_unidad/" + producto + "/" + unidad + "/" + moneda;
    //alert(url);
    select_precio = document.getElementById('precioProducto');
    options_umedida = select_precio.getElementsByTagName("option");

    var num_option = options_umedida.length;
    for (j = 1; j <= num_option; j++) {
        select_precio.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode("::Seleccion::");
    opt.appendChild(texto);
    opt.value = "";
    select_precio.appendChild(opt);
    var bd = 0
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {

            codigo = item.codigo;
            moneda = item.moneda;
            precio = item.precio;
            establecimiento = item.establecimiento;
            posicion_precio = item.posicion_precio;
            select = item.posicion;
            opt = document.createElement('option');
            texto = document.createTextNode(moneda + " " + precio + " " + establecimiento);
            opt.appendChild(texto);
            opt.value = precio;
            if (select == true) {
                opt.setAttribute('selected', 'selected')
                $("#precio").val(precio);
                bd = 1
            }
            if (bd == 0) {
                opt.removeAttribute('selected')
                $("#precio").val('');
            }
            select_precio.appendChild(opt);
        });
    });
}

function mostrar_precio() {
    precio = $("#precioProducto").val();
    $("#precio").val(precio);
}

function obtener_precio_producto() {
    var producto = $("#producto").val();
    $('#precio').val("");
    if (producto == '' || producto == '0')
        return false;
    var moneda = $("#moneda").val();
    if (moneda == '' || moneda == '0')
        return false;
    var unidad_medida = $("#unidad_medida").val();
    if (unidad_medida == '' || unidad_medida == '0')
        return false;
    var cliente = $("#cliente").val();
    if (cliente == '')
        cliente = '0';
    var igv;
    if (contiene_igv == '1')
        igv = 0;
    else if (tipo_docu != 'B' && tipo_docu != 'N')
        igv = 0;
    else
        igv = $("#igv").val();

    var url = base_url + "index.php/almacen/producto/JSON_precio_producto/" + producto + "/" + moneda + "/" + cliente + "/" + unidad_medida + "/" + igv;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            $('#precio').val(item.PRODPREC_Precio);
        });
    });
    return false;
}
function inicializar_cabecera_item() {
    $("#producto").val('');
    $("#buscar_producto").val('');
    $("#codproducto").val('');
    $("#nombre_producto").val('');
    $("#cantidad").val('');
    $("#costo").val('');
    $("#unidad_medida").val('0');
    $("#precioProducto").val('');
    $("#precio").val('');
    limpiar_combobox('unidad_medida');
}
function agregar_todopresupuesto(guia, tipo_oper) {
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    almacen=$("#almacen").val();
    url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/" + tipo_oper + "/" + tipo_docu + "/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
    	
        limpiar_datos();
        $.each(data, function (i, item) {
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.PRESUC_Serie;
            numero = item.PRESUC_Numero;
            codigo_usuario = item.PRESUC_CodigoUsuario;

            if (item.PRESDEP_Codigo != '') {
                j = n + 1;
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Descripcion;
                nombre_producto = item.PROD_Nombre;
                flagGenInd = item.PROD_GenericoIndividual
                cantidad = item.PRESDEC_Cantidad;
                pu = item.PRESDEC_Pu;
                subtotal = item.PRESDEC_Subtotal;
                descuento = item.PRESDEC_Descuento;
                igv = item.PRESDEC_Igv;
                total = item.PRESDEC_Total
                pu_conigv = item.PRESDEC_Pu_ConIgv;
                subtotal_conigv = item.PRESDEC_Subtotal_ConIgv;
                descuento_conigv = item.PRESDEC_Descuento_ConIgv;

                
                /**verificamos si el producto esta inventariado ***/
                var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                isMostrarArticulo=true;
                isSeleccionarAlmacen=false;
                $.ajax({
                    url: url2,
                    async: false, 
                    success: function (data2) {
    	            	/***articulos con serie**/
    	            	if(flagGenInd=="I"){
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
    	            			isMostrarArticulo=false;
    	            		}
    	            	}else{
    	            		/***articulos sin serie**/
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
    	            			almacenProducto=almacen;
    	            		}
    	            	}
                    }	
                });
                
                /**fin de verificacion**/
                if(isMostrarArticulo){
	                if (j % 2 == 0) {
	                    clase = "itemParTabla";
	                } else {
	                    clase = "itemImparTabla";
	                }
	                fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" >';
	                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
	                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
	                fila += '</a></strong></font></div></td>';
	                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
	                fila += '<td width="10%"><div align="center">';
	                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
	                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
	                fila += '</div></td>';
	                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="B"/>';
	                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="'+flagGenInd+'"/>'
	                
	                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
	                fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad + '</div>';
	                
	                if (flagGenInd == "I") {
		            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
		            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
		 	         }else{
			            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
			            if(isSeleccionarAlmacen){
			            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen('+ n +')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
			            } 	
		            }
	                
	                
	                fila += '</td>';
	                
	                if (tipo_docu != 'B' && tipo_docu != 'N') {
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
	                } else {
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
	                }
	                //fila += '<td width="6%"><div align="center">';
	                fila += '<input type="hidden" size="1" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
	                if (tipo_docu != 'B' && tipo_docu != 'N')
	                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
	                else
	                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
	                //fila += '</div></td>';
	
	                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
	                fila += '<td width="6%" style="display:none;" ><div align="center">';
	                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
	                fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" value="0">';
	                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
	                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
	                fila += '<input type="text" value="0" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
	                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
	                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
	                fila += '</div></td>';
	                fila += '</tr>';
	                $("#tblDetalleComprobante").append(fila);
	                $('#forma_pago').val(formapago);
	                $('#moneda').val(moneda);
	                n++;
                }
            }

            
        })
        calcula_totales();
        }
    });
    
    
    
    
}
function obtener_detalle_guiarem(guiarem) {
    url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + guiarem;

    n = document.getElementById('tblDetalleComprobante').rows.length;
    $.getJSON(url, function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            serie = item.GUIAREMC_Serie;
            numero = item.GUIAREMC_Numero;
            codigo_usuario = item.GUIAREMC_CodigoUsuario;

            if (item.GUIAREMP_Codigo != '') {
                j = n + 1;
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.GUIAREMDETC_Cantidad;
                precio = item.GUIAREMDETC_Pu;
                subtotal = item.GUIAREMDETC_Subtotal;
                descuento = item.GUIAREMDETC_Descuento100;
                igv = item.GUIAREMDETC_Igv100;
                precio_conigv = item.GUIAREMDETC_Pu_ConIgv;
                flagGenInd = item.GUIAREMDETC_GenInd;
                flagBS = item.PROD_FlagBienServicio;
                costo = item.GUIAREMDETC_Costo
                stock = '';

                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }

                fila = '<tr class="' + clase + '">';
                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                fila += '</a></strong></font></div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">' + codproducto + '</div></td>';
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                fila += '<td width="10%"><div align="left">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
                else
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

                fila += '</div></td>';
                if (tipo_docu != 'B' && tipo_docu != 'N') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
                }
                else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="0" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly"></div></td>';
                }
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
                fila += '<td width="6%" style="display:none;" ><div align="center">';
                fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento + '">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                else
                    fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
                fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
                fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
                fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
                fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                fila += '</div></td>';
                fila += '</tr>';
                $("#tblDetalleComprobante").append(fila);
            }

            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#moneda').val(moneda);

            calcula_importe(n);
        });
        calcula_totales();
    });
}

function obtener_detalle_ocompra(ocompra) {
    consultarAdelantos(ocompra);

	/***obtenenemos el almacen de la factura**/
	var almacen=$("#almacen").val();
    totalAmountOrden = 0;

    var isAdelanto = $("#forma_pago").val() == 19;
	/**fin de obtener el almacen**/
	
    var proyecto;
	
    var url = base_url + "index.php/compras/ocompra/obtener_detalle_ocompra_for_comprobante/" + ocompra;
    var n = document.getElementById('tblDetalleComprobante').rows.length;

    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
            //limpiar_datos();
            $("#igv").val(data[0]['OCOMDEC_Igv100']);

            $.each(data, function (i, item) {

                if(!proyecto && item.PROYP_Codigo != 0) proyecto = item.PROYP_Codigo;

                var cliente = item.CLIP_Codigo;
                var ruc = item.Ruc;
                var razon_social = item.RazonSocial;
                var moneda = item.MONED_Codigo;
                var serie = item.OCOMC_Serie;
                var numero = item.OCOMC_Numero;
                var codigo_usuario = item.PROD_CodigoUsuario;

                var pendiente       = item.OCOMDEC_Pendiente_pago;

                if(item.OCOMDEP_Codigo!='' && pendiente!=0) {
                    var my_pos_n = n;
                    var j = my_pos_n + 1;
                    var producto = item.PROD_Codigo;
                    var codproducto = item.PROD_CodigoInterno;
                    var unidad_medida = item.UNDMED_Codigo;
                    var nombre_unidad = item.UNDMED_Simbolo ? item.UNDMED_Simbolo : '';
                    var nombre_producto = item.PROD_Nombre;
                    var cantidad = item.OCOMDEC_Cantidad;
                    var precio = item.OCOMDEC_Pu;
                    var subtotal = item.OCOMDEC_Subtotal;
                    var descuento100 = item.OCOMDEC_Descuento100;
                    var igv = item.OCOMDEC_Igv100;
                    var precio_conigv = item.OCOMDEC_Pu_ConIgv;
                    var flagGenInd = item.OCOMDEC_GenInd;
                    var flagBS = item.PROD_FlagBienServicio;
                    var costo = item.OCOMDEC_Total;
                    var descuento = item.OCOMDEC_Descuento;
                    var stock = 0;

                    $('#moneda').val(moneda);

                    if(tipo_oper == 'C') totalAmountOrden += (parseFloat(cantidad) * parseFloat(precio));

                    if(isAdelanto) return;
                
                    /**verificamos si el producto esta inventariado ***/
                    var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                    var isMostrarArticulo=true;
                    var isSeleccionarAlmacen=false;

                    /*$.ajax({
                        url: url2,
                        async: false, 
                        success: function (data2) {
        	            	/***articulos con serie**/
        	            	/*if(flagGenInd=="I"){
        	            		if(data2.trim()=="1")
        	            		{
        	            			almacenProducto=null;
        	            			isSeleccionarAlmacen=1;
        	            		}else{
        	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
        	            			isMostrarArticulo=false;
        	            		}
        	            	}else{
        	            		/***articulos sin serie**/
        	            		/*if(data2.trim()=="1")
        	            		{
        	            			almacenProducto=null;
        	            			isSeleccionarAlmacen=1;
        	            		}else{
        	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
        	            			/*almacenProducto=almacen;
        	            		}
        	            	}
                        }	
                    });*/
                
                    /**fin de verificacion**/
                    if(isMostrarArticulo && $(".det_id_fk_"+item.OCOMDEP_Codigo).length == 0) {
        	            if (j % 2 == 0) {
        	                clase = "itemParTabla";
        	            } else {
        	                clase = "itemImparTabla";
        	            }

                        if(!colors[item.OCOMP_Codigo_venta_ref]) colors[item.OCOMP_Codigo_venta_ref] = '#'+Math.floor(Math.random()*16777215).toString(16);

        	            fila = '<tr class="tooltiped ' + clase + ' det_id_fk_'+item.OCOMDEP_Codigo+'" data-toggle="tooltip" data-placement="top" title="'+(item.PROYP_Codigo != 0 ? "Proyecto : "+item.PROYC_Nombre : ((tipo_oper == 'C' ? 'Cliente : ' : 'Proveedor : ') + (item.RazonSocialRef)))+'" id="' + n + '" >';
        	            fila += '<td width="3%"><input type="checkbox" name="pedir['+n+']" id="pedir['+n+']" onchange="togglePedir('+n+')" checked/></td>';
        	            fila += '<td width="4%"><div align="center">' + j + '</div></td>';
        	            fila += '<td width="10%" style="border-left: 10px solid ' + (colors[item.OCOMP_Codigo_venta_ref] ? colors[item.OCOMP_Codigo_venta_ref] : 'transparent') + '"><div align="center">' + codigo_usuario + '</div></td>';
        	            fila += '<td width="30%" ><div align="left"><input type="text" class="cajaGeneral" size="60" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
        	            fila += '<td width="10%"><div align="left">';
        	                       
        	            if (tipo_docu == 'F'){
        	                fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + pendiente + '" onchange="modificar_cantidad(' + n + ');calcula_cantidad_pendiente(' + n + ')" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
        	                fila+= '<input type="hidden" name="pendiente[' + n + ']" id="pendiente[' + n + ']" value="'+pendiente+'">';
                        }
                        else{
        	                fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + pendiente + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
        	                fila+= '<input type="hidden" name="pendiente[' + n + ']" id="pendiente[' + n + ']" value="'+pendiente+'">';  
        	            }
        	            if (igv != 0 && flagGenInd == "I") {
        	            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie(' + n + ')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
        	            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
        	 	         }else{
        		            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
        		            if(igv != 0 && isSeleccionarAlmacen){
        		            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen(' + n + ')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
        		            } 	
        	            }
                   
        	            
        	            
        	            fila += '</div></td>';
        	            if (tipo_docu == 'F') {
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
        	            }
        	            else {
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="'+precio_conigv+'" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly">';
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + precio_conigv + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
        	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
        	            }
        	            if (tipo_docu == 'F') {
        	                fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
        	            } else {
        	                fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" value="" readonly="readonly"></div></td>';
        	            }
        	            fila += '<td style="display:none;" ><div align="center">';
        	            fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
        	            fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
        	            fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
        	            fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
        	            
                        if (tipo_docu == 'F') {
        	                fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
        	            }
        	            else {
        	                fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
        	                fila += '<input type="hidden" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
        	            }

        	            fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
        	            fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
        	            fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
        	            fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
        	            fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
        	            fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
                        fila += '<input type="hidden" class="cajaPequena2" name="oventacod[' + n + ']" id="oventacod[' + n + ']" value="'+item.OCOMDEP_Codigo+'" readonly="readonly">';
                        fila += '<input type="hidden" name="esImportado[' + n + ']" id="esImportado[' + n + ']" value="' + item.es_importado + '"/>';
        	            fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
        	            fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
        	            fila += '</div></td>';
        	            fila += '</tr>';
        	            
                        $("#tblDetalleComprobante").append(fila);
        	            
                  
        	            calcula_importe(my_pos_n);

                        if(tipo_oper == 'V') mostrarPopUpSeleccionarAlmacen(my_pos_n, false);
                       
                        //mostrarPopUpSeleccionarAlmacen(my_pos_n, false);
                        n++;
                    };
                }
            })//aqui

            $("#obra").val(proyecto).trigger('change');

            if(tipo_oper == 'C') $(".tooltiped").tooltip();

            $("#igv").trigger('blur');

            if(isAdelanto) $(".percent-box").trigger('blur');

        }
    });
}

function obtener_detalle_oventa(oventa) {
    /***obtenenemos el almacen de la factura**/
    var almacen=$("#almacen").val();
    /**fin de obtener el almacen**/
    
    var proyecto;

    consultarAdelantos(oventa);
    
    var url = base_url + "index.php/compras/ocompra/obtener_detalle_ocompra2/" + oventa;
    var n = document.getElementById('tblDetalleComprobante').rows.length;
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
            //limpiar_datos();
            $("#igv").val(data[0]['OCOMDEC_Igv100']);

            $.each(data, function (i, item) {

                if(!proyecto && item.PROYP_Codigo != 0) proyecto = item.PROYP_Codigo;

                var cliente = item.CLIP_Codigo;
                var ruc = item.Ruc;
                var razon_social = item.RazonSocial;
                var moneda = item.MONED_Codigo;
                var serie = item.OCOMC_Serie;
                var numero = item.OCOMC_Numero;
                var codigo_usuario = item.PROD_CodigoUsuario;

                var pendiente       = item.OCOMDEC_Pendiente_pago;

                if(item.OCOMDEP_Codigo!='' && pendiente!=0) {
                    var my_pos_n = n;
                    var j = my_pos_n + 1;
                    var producto = item.PROD_Codigo;
                    var codproducto = item.PROD_CodigoInterno;
                    var unidad_medida = item.UNDMED_Codigo;
                    var nombre_unidad = item.UNDMED_Simbolo ? item.UNDMED_Simbolo : '';
                    var nombre_producto = item.PROD_Nombre;
                    var cantidad = item.OCOMDEC_Cantidad;
                    var precio = item.OCOMDEC_Pu;
                    var subtotal = item.OCOMDEC_Subtotal;
                    var descuento100 = item.OCOMDEC_Descuento100;
                    var igv = item.OCOMDEC_Igv100;
                    var precio_conigv = item.OCOMDEC_Pu_ConIgv;
                    var flagGenInd = item.OCOMDEC_GenInd;
                    var flagBS = item.PROD_FlagBienServicio;
                    var costo = item.OCOMDEC_Total;
                    var descuento = item.OCOMDEC_Descuento;
                    var stock = 0;

                    $('#moneda').val(moneda);

                   if($("#forma_pago").val() == 19) return;
                
                    /**verificamos si el producto esta inventariado ***/
                    var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                    isMostrarArticulo=true;
                    isSeleccionarAlmacen=false;

                    /*$.ajax({
                        url: url2,
                        async: false, 
                        success: function (data2) {
                            /***articulos con serie**/
                            /*if(flagGenInd=="I"){
                                if(data2.trim()=="1")
                                {
                                    almacenProducto=null;
                                    isSeleccionarAlmacen=1;
                                }else{
                                    alert("No se puede ingresar este producto Serie, no contiene Inventario");
                                    isMostrarArticulo=false;
                                }
                            }else{
                                /***articulos sin serie**/
                                /*if(data2.trim()=="1")
                                {
                                    almacenProducto=null;
                                    isSeleccionarAlmacen=1;
                                }else{
                                    /**no esta inventariado pero se selecciona almacen por default del comprobante**/
                                    /*almacenProducto=almacen;
                                }
                            }
                        }   
                    });*/
                
                    /**fin de verificacion**/
                    if(isMostrarArticulo && $(".det_id_fk_"+item.OCOMDEP_Codigo).length == 0) {
                        if (j % 2 == 0) {
                            clase = "itemParTabla";
                        } else {
                            clase = "itemImparTabla";
                        }
                        fila = '<tr class="' + clase + ' det_id_fk_'+item.OCOMDEP_Codigo+'" id="' + n + '" >';
                        fila += '<td width="3%"><input type="checkbox" name="pedir['+n+']" id="pedir['+n+']" onchange="togglePedir('+n+')" checked/></td>';
                        fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                        fila += '<td width="10%"><div align="center">' + codigo_usuario + '</div></td>';
                        fila += '<td width="30%" ><div align="left"><input type="text" class="cajaGeneral" size="60" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                        fila += '<td width="10%"><div align="left">';
                                   
                        if (tipo_docu == 'F'){
                            fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + pendiente + '" onchange="modificar_cantidad(' + n + ');calcula_cantidad_pendiente(' + n + ')" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
                            fila+= '<input type="hidden" name="pendiente[' + n + ']" id="pendiente[' + n + ']" value="'+pendiente+'">';
                        }
                        else{
                            fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + pendiente + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
                            fila+= '<input type="hidden" name="pendiente[' + n + ']" id="pendiente[' + n + ']" value="'+pendiente+'">';  
                        }
                        if (igv != 0 && flagGenInd == "I") {
                            fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie(' + n + ')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
                            fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
                         }else{
                            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
                            if(igv != 0 && isSeleccionarAlmacen){
                                fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen(' + n + ')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
                            }   
                        }
                   
                        
                        
                        fila += '</div></td>';
                        if (tipo_docu == 'F') {
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
                        }
                        else {
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="'+precio_conigv+'" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly">';
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + precio_conigv + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
                        }
                        if (tipo_docu == 'F') {
                            fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
                        } else {
                            fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" value="" readonly="readonly"></div></td>';
                        }
                        fila += '<td style="display:none;" ><div align="center">';
                        fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
                        fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                        fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                        fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
                        
                        if (tipo_docu == 'F') {
                            fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
                        }
                        else {
                            fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
                            fila += '<input type="hidden" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
                        }

                        fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
                        fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
                        fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
                        fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
                        fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
                        fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
                        fila += '<input type="hidden" class="cajaPequena2" name="oventacod[' + n + ']" id="oventacod[' + n + ']" value="'+item.OCOMDEP_Codigo+'" readonly="readonly">';
                        fila += '<input type="hidden" name="esImportado[' + n + ']" id="esImportado[' + n + ']" value="' + item.es_importado + '"/>';
                        fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
                        fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                        fila += '</div></td>';
                        fila += '</tr>';
                        
                        $("#tblDetalleComprobante").append(fila);
                        
                  
                        calcula_importe(my_pos_n);
                       
                        mostrarPopUpSeleccionarAlmacen(my_pos_n, false);
                    };
                    n++;
                }
            })//aqui

            $("#obra").val(proyecto).trigger('change');
        }
    });
}

function limpiar_datos() {
    /*$('#ruc_cliente').val('');
     $('#cliente').val('');
     $('#nombre_cliente').val('');*/
    $('#formapago').val('');
    $('#moneda').val('1');

    n = document.getElementById('tblDetalleComprobante').rows.length;
    for (i = 0; i < n; i++) {
        a = "detacodi[" + i + "]";
        b = "detaccion[" + i + "]";
        fila = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
    }
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
function obtener_proveedor() {
    var numdoc = $("#ruc_proveedor").val();
    $("#proveedor, #nombre_proveedor").val('');

    if (numdoc == '')
        return false;

    var url = base_url + "index.php/empresa/proveedor/obtener_nombre_proveedor/" + numdoc;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.EMPRC_RazonSocial != '') {
                $('#nombre_proveedor').val(item.EMPRC_RazonSocial);
                $('#proveedor').val(item.PROVP_Codigo);
                $('#codproducto').focus();
            }
            else {
                $('#nombre_proveedor').val('No se encontró ningún registro');
                $('#linkVerProveedor').focus();
            }
        });
    });
    return true;
}
function obtener_producto() {
    var flagBS = $("#flagBS").val();
    var codproducto = $("#codproducto").val();
    $("#producto, #nombre_producto").val('');
    if (codproducto == '')
        return false;

    var url = base_url + "index.php/almacen/producto/obtener_nombre_producto/" + flagBS + "/" + codproducto;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.PROD_Nombre != '') {
                $("#producto").val(item.PROD_Codigo);
                $("#nombre_producto").val(item.PROD_Nombre);
                listar_unidad_medida_producto($("#producto").val());
                $('#cantidad').focus();
            }
            else {
                $('#nombre_producto').val('No se encontró ningún registro');
                $('#linkVerProdcuto').focus();
            }

        });
    });
    return true;
}

function limpiar_campos_producto() {
    $("#producto,  #codproducto, #nombre_producto, #cantidad, #precio").val('');
    limpiar_combobox('unidad_medida');
    if ($('#flagBS').val() == 'B')
        $('#unidad_medida').show();
    else
        $('#unidad_medida').hide();
    $('#linkVerProducto').attr('href', '' + base_url + 'index.php/almacen/producto/ventana_busqueda_producto/' + $('#flagBS').val());
}

function calcular_importe_todos(total) {
    //Para Factura
    for (var i = 0; i < total; i++) {
        modifica_pu_conigv(i);
    }
}

function modificar_pu_conigv_todos(total) {
    //Para Boleta y Comprobante
    for (var i = 0; i < total; i++) {
        calcula_importe_conigv(i);
    }
}

function verificar_agregar(guia){
    n = document.getElementById('idTableGuiaRelacion').rows.length;
    /**limpiamos los articulos agregados si seleccionamos una guia de remision**/
    if(n==1){
        if(confirm("¿Desea agregar guia de remisión se eliminara todos los articulos y/o servicios agregados?")){
            $("#tblDetalleComprobante").html("");   
            /**bloqueamos los opcion de obtener articulos y ocultamnos agregar producto verificamos si es servicio para que no lo oculte**/
            valorFlagBS=$("#flagBS").val();
            if(valorFlagBS=='B'){
                //document.getElementById("buscar_producto").readOnly = true;
                document.getElementById("tempde_producto").readOnly = true;
                $("#idDivAgregarProducto").hide(200);
            }
            
            $("#moneda").hide(200);
            textoMoneda=$("#moneda option:selected").text();
            $("#textoMoneda").html(textoMoneda);
            $("#textoMoneda").show(200);
            /**fin de bloquear**/
        }else{
            return false;
        }
    }
    /**fin de limpiar**/
    if(n>1){
        for(x=1;x<n;x++){
            codGuia = document.getElementById('codigoGuiaremAsociada['+ x +']').value;
            if( codGuia == guia ){
                alert("Guia de Remision se encuentra seleccionada"); 
                return false;
            }
        }
    }
}




function agregar_todo(guia) {
	
	/**verificamos si guiaderemision ya se encuentra asociada**/
	n = document.getElementById('idTableGuiaRelacion').rows.length;
	/**limpiamos los articulos agregados si seleccionamos una guia de remision**/
	if(n==1){
		if(confirm("¿Desea agregar guia de remisión se eliminara todos los articulos y/o servicios agregados?")){
			$("#tblDetalleComprobante").html("");	
			/**bloqueamos los opcion de obtener articulos y ocultamnos agregar producto verificamos si es servicio para que no lo oculte**/
            valorFlagBS=$("#flagBS").val();
			if(valorFlagBS=='B'){
            	//document.getElementById("buscar_producto").readOnly = true;
                document.getElementById("tempde_producto").readOnly = true;
            	$("#idDivAgregarProducto").hide(200);
			}
			
            $("#moneda").hide(200);
            textoMoneda=$("#moneda option:selected").text();
            $("#textoMoneda").html(textoMoneda);
            $("#textoMoneda").show(200);
            /**fin de bloquear**/
		}else{
			return false;
		}
	}
	/**fin de limpiar**/
	if(n>1){
		for(x=1;x<n;x++){
			codA = "codigoGuiaremAsociada[" + x + "]";
			accCod = "accionAsociacionGuiarem[" + x + "]";
		    valorSeleccion=document.getElementById(accCod).value;
		    valorcodigoGuiaremAsociada=document.getElementById(codA).value;
			if(valorSeleccion==1 && valorcodigoGuiaremAsociada==guia){
				alert("Guia de Remision se encuentra seleccionada"); 
				return;
				}
		}
	}
	/**fin de verificacion**/
	descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();

    url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    var randomColor = Math.floor(Math.random()*16777215).toString(16);
    randomColor="#"+randomColor;
    $.getJSON(url, function (data) {
       valorVerificar=true;
        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            proveedor = item.PROVP_Codigo;
            if (tipo_oper == 'V') {
            	 if ($('#cliente').val()!=cliente) {
                     alert('la serie y numero ingresado no corresponden a este cliente');
                     valorVerificar=false;
                     return false;
                 } 
            }else{
            	
            	if ($('#proveedor').val() != proveedor) {
            		 alert('la serie y numero ingresado no corresponden a este Proveedor');
            		 valorVerificar=false;
                    return false;
                }
            	
            	
            }
            
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.GUIAREMC_Serie;
            numero = item.GUIAREMC_Numero;
            codigo_usuario = item.GUIAREMC_CodigoUsuario;
            almacenProducto=item.ALMAP_Codigo;
            flagGenInd=item.GUIAREMDETC_GenInd;
            oventa = item.OCOMP_Codigo;

            if (item.PRESDEP_Codigo != '') {
                j = n + 1
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Descripcion;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.GUIAREMDETC_Cantidad;
                pu = item.GUIAREMDETC_Pu;
                subtotal = item.GUIAREMDETC_Subtotal;
                descuento = item.GUIAREMDETC_Descuento;
                igv = item.GUIAREMDETC_Igv;
                total = item.GUIAREMDETC_Total;
                pu_conigv = item.GUIAREMDETC_Pu_ConIgv;
                subtotal_conigv = parseFloat(pu_conigv) * parseFloat(cantidad);
                descuento_conigv = '';
                
                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }
                
                fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" style="background-color:'+randomColor+';color:#000000;" >';
                fila += '<td width="3%"><div align="center">';
                fila += '</div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">';
                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                fila += '</div></td>';
                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="B"/>';
                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="'+flagGenInd+'"/>'
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                fila += '<td width="10%"><div align="left">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');" readonly>';
                else
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly>' + nombre_unidad ;

                if(flagGenInd!=null && flagGenInd=='I'){
                	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serieMostrar(10,'+guia+','+producto+','+almacenProducto+')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" class="imgBoton"></a>';
                	/**vamos al metodo de producto serie para eliminar el de la secciontemporal y agregar el de la seccion Real**/
               }
               fila += '</div></td>';
                if (tipo_docu != 'B' && tipo_docu != 'N') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly/></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
                } else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly/></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
                }
                fila += '<input type="hidden" size="1" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
                else
                    fila += '<input type="hidden" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento_conigv + '" onblur="calcula_importe2_conigv(' + n + ');calcula_totales_conigv();">';
               

                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
                fila += '<td width="6%" style="display:none;" ><div align="center">';
                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" value="0">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
                fila += '<input type="text" value="0" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
                /**se agrega la guia de remision asociada***/
                fila += '<input type="hidden" name="codigoGuiarem[' + n + ']" id="codigoGuiarem[' + n + ']" value="' + guia + '">';
                /**fin de agregar la guia de remision**/
                fila += '</div></td>';
                fila += '</tr>';
                $("#tblDetalleComprobante").append(fila);
            }
            
            $('#ordencompra').val(oventa);
            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#forma_pago').val(formapago);
            $('#moneda').val(moneda);
            n++;
        })
        /**verificamos si la comprobacion del documento es del mismo proveedor o cliente**/
        if(!valorVerificar){
        	return false;
        }
    
        agregarGuiasRelacion(guia,serie,numero,randomColor);
        if (n >= 0) {
            if (tipo_docu != 'B' && tipo_docu != 'N')
                calcula_totales();
            else
                calcula_totales();

        }
        else {
            alert('El presupuesto no tiene elementos.');
        }
    });
	}
	/**agregagamos listado de guias relacionadas**/
	function agregarGuiasRelacion(codigoGuiarem,serie,numero,color){
		
		var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		n = document.getElementById('idTableGuiaRelacion').rows.length;
		
		if(total==0){
			/***mmostramos el div tr de guias relacionadas**/
			$("#idDivGuiaRelacion").show(200);
		}
		
		
		
		proveedor=$("#proveedor").val();
		j=n;
		fila='<tr id="idTrDetalleRelacion_'+j+'">';
		fila+='<td>';
		fila+='<a href="javascript:void(0);" onclick="deseleccionarGuiaremision('+codigoGuiarem+','+j+')" title="Deseleccionar Guia de remision">';
		fila+='x';
		fila+='</a>';
		fila+='</td>';
		fila+='<td>'+j+'</td>';
		fila+='<td>'+serie+'</td>';
		fila+='<td>'+numero+'</td>';
		/**accionAsociacionGuiarem nuevo:1**/
		fila+='<td><div style="width:10px;height:10px;background-color:'+color+';border:1px solid black"></div>';
		fila+='	<input type="text" id="codigoGuiaremAsociada['+j+']"  name="codigoGuiaremAsociada['+j+']" value="'+codigoGuiarem+'" />';
		fila+='<input type="text" id="accionAsociacionGuiarem['+j+']"  name="accionAsociacionGuiarem['+j+']" value="1" />';
		fila+='<input type="text" id="proveedorRelacionGuiarem['+j+']"  name="proveedorRelacionGuiarem['+j+']" value="'+proveedor+'" />';
		fila+='</td>';
		fila+='</tr>';
		$("#idTableGuiaRelacion").append(fila);
		 
	}

	function deseleccionarGuiaremision(codigoGuiarem,posicion){
		
		/**ocultamos el registro de guiaremision asociadas**/
		 a = "codigoGuiaremAsociada[" + posicion + "]";
	     b = "accionAsociacionGuiarem["+posicion+"]";
	     fila = document.getElementById(a).parentNode.parentNode;
	     fila.style.display = "none";
	     /**0:deselecccionado**/
	     document.getElementById(b).value = "0";
		/**fin de ocultar**/
		
	     /**quitamos de lista detalle los productos asociados segun el codigodeGuiuarem***/
		
	     nDetalle = document.getElementById('tblDetalleComprobante').rows.length;
	     /**recorremos para obtener los productos aqsociados a esa guia de remision y lo deseleccionamos**/
	     for(x=0;x<nDetalle;x++){
	    	 c = "codigoGuiarem["+x+"]";
		     valorCodigoGuiarem = document.getElementById(c).value ;
	    	 if(valorCodigoGuiarem==codigoGuiarem){
	    		 a = "detacodi[" + x + "]";
	    	     b = "detaccion[" + x + "]";
	    	     fila = document.getElementById(a).parentNode.parentNode.parentNode;
	    	     fila.style.display = "none";
	    	     document.getElementById(b).value = "e";
	    	 }
	     }
	     
	     
	     if (tipo_docu != 'B' && tipo_docu != 'N')
	    	 calcula_totales();
	     else
	    	 calcula_totales_conigv();
	     
	     verificarOcultarListadoGuiaremAsociado();
		
	}
	
	
	function verificarOcultarListadoGuiaremAsociado(){
		
		/**fin de**/
	     var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		if(total==0){
			/**verificamos si contiene accion:0 lo eliminamos los tr**/
			n = document.getElementById('idTableGuiaRelacion').rows.length;
			if(n>1){
				for(x=1;x<n;x++){
					document.getElementById("idTableGuiaRelacion").deleteRow(1);
				}
			}
			$("#idDivGuiaRelacion").hide(200);
			//document.getElementById("buscar_producto").readOnly = false;
            document.getElementById("tempde_producto").readOnly = false;
			$("#idDivAgregarProducto").show(200);
			$("#moneda").show(200);
			$("#textoMoneda").html("");
			$("#textoMoneda").hide(200);
		}
		
	}
	
	/**desleccionamos todo el listado**/
	function listadoGuiaremEstadoDeseleccionado(){
		 var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
			if(total!=0){
				n = document.getElementById('idTableGuiaRelacion').rows.length;
				if(n>1){
					for(x=1;x<n;x++){
						aAG="accionAsociacionGuiarem["+x+"]";
						document.getElementById(aAG).value=0;
					}
				}
			}
		
	}
	
	
	

// gcbq
function agregar_producto_guiarem2(codproducto, producto, nombre_producto, cantidad, igv, precio_conigv, unidad_medida, nombre_unidad, codigo_orden, flagGenInd, moneda) {
    igv = parseInt(igv);
    if (contiene_igv == '1')
        precio = money_format(precio_conigv * 100 / (igv + 100))
    else {
        precio = precio_conigv;
        precio_conigv = money_format(precio_conigv * (100 + igv) / 100);
    }
    stock = '0'
    costo = '0';
    n = document.getElementById('tblDetalleComprobante').rows.length;

    if ($("#ordencompra").val() != codigo_orden) {
        limpiar_datos();
    }

    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }
    fila = '<tr class="' + clase + '">';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="10%"><div align="center">';
    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + codproducto + '">' + producto;
    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
    fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
    fila += '</div></td>';
    fila += '<td><div align="left">';
    fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '">';
    fila += '</div></td>';
    fila += '<td width="10%"><div align="left">';
    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
    if (flagGenInd == "I") {
        if (tipo_oper == 'V')
            fila += ' <a href="javascript:;" onclick="ventana_producto_serie2(' + n + ')"><img src="' + base_url + 'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle"/></a>';
        else
            fila += ' <a href="javascript:;" onclick="ventana_producto_serie(' + n + ')"><img src="' + base_url + 'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle" /></a>';
    }
    fila += '</div></td>';
    fila += '<td width="6%"><div align="center"><input type="text"  size="5" maxlength="10" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');"></div></td>';
    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">'
    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none"><div align="center">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
    fila += '<input type="hidden" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    fila += '</div></td>';
    fila += '<td width="6%" style="display:none" ><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly></div></td>';
    fila += '<td width="6%" style="display:none" ><div align="center">';
    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
    fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly" >';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    calcula_importe(n);
    $('#ordencompra').val(codigo_orden);
    return true;
}
function agregar_ocompra_guiarem2(proveedor, ruc_proveedor, nombre_proveedor, almacen, moneda, numero, codigo_usuario) {
    tipo_oper = $("#tipo_oper").val();

    if (tipo_oper == 'V') {
        $('#cliente').val(proveedor);
        $('#ruc_cliente').val(ruc_proveedor);
        $('#nombre_cliente').val(nombre_proveedor);
    } else {
        $('#proveedor').val(proveedor);
        $('#ruc_proveedor').val(ruc_proveedor);
        $('#nombre_proveedor').val(nombre_proveedor);
    }

    $("#serieguiaverOC").html("O. de compra: " + numero + '-' + codigo_usuario);
    $("#serieguiaverOC").show(2000);
    $("#serieguiaverRecu").hide(2000);
    $("#serieguiaver").hide(2000);
    $("#serieguiaverPre").hide(2000);
    $("#numero_ref").val('');
    $("#dRef").val('');
    $("#docurefe_codigo").val('');

    $('#almacen').val(almacen);
    if (moneda == 'NUEVOS SOLES') {
        $('#moneda').val('1');
    } else {
        $('#moneda').val('2');
    }
}

function agregar_todo_recu(guia) {
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    /***obtenenemos el almacen de la factura**/
	almacen=$("#almacen").val();
	/**fin de obtener el almacen**/
    url = base_url + "index.php/ventas/comprobante/obtener_detalle_comprobante/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.PRESUC_Serie;
            numero = item.PRESUC_Numero;
            codigo_usuario = item.PRESUC_CodigoUsuario;


            if (item.PRESDEP_Codigo != '') {
                j = n + 1
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                flagGenInd = item.CPDEC_GenInd;
                almacenProducto=item.ALMAP_Codigo;
                costo = item.CPDEC_Costo;
                cantidad = item.CPDEC_Cantidad;
                pu = item.CPDEC_Pu;
                subtotal = item.CPDEC_Subtotal;
                descuento = item.CPDEC_Descuento;
                igv = item.CPDEC_Igv;
                total = item.CPDEC_Total
                pu_conigv = item.CPDEC_Pu_ConIgv;
                subtotal_conigv = item.CPDEC_Subtotal_ConIgv;
                descuento_conigv = item.CPDEC_Descuento_ConIgv;

                /**verificamos si el producto esta inventariado ***/
                var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                isMostrarArticulo=true;
                isSeleccionarAlmacen=false;
                $.ajax({
                    url: url2,
                    async: false, 
                    success: function (data2) {
    	            	/***articulos con serie**/
    	            	if(flagGenInd=="I"){
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
    	            			isMostrarArticulo=false;
    	            		}
    	            	}else{
    	            		/***articulos sin serie**/
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
    	            			almacenProducto=almacen;
    	            		}
    	            	}
                    }	
                });
                
                /**fin de verificacion**/
                if(isMostrarArticulo){
	                if (j % 2 == 0) {
	                    clase = "itemParTabla";
	                } else {
	                    clase = "itemImparTabla";
	                }
	                fila = '<tr class="' + clase + '" id="'+n+'">';
	                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(' + n + ');">';
	                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
	                fila += '</a></strong></font></div></td>';
	                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
	                fila += '<td width="10%"><div align="center">';
	                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
	                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
	                fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
	                fila += '</div></td>';
	                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
	                fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
	                
	                if (flagGenInd == "I") {
		            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
		            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
		 	         }else{
			            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
			            if(isSeleccionarAlmacen){
			            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen('+ n +')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
			            } 	
		            }
	                fila += '</div></td>';
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">';
	                fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly"></div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center">';
	                fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
	                fila += '<input type="hidden" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe(' + n + ');calcula_totales();">';
	
	                fila += '</div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center">';
	                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
	                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
	                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
	                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
	                fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
	                fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
	                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
	                fila += '</div></td>';
	                fila += '</tr>';
	                $("#tblDetalleComprobante").append(fila);
	                n++;
                }
            }
            $('#moneda').val(moneda);
           
            
            
            
        })
        if (n >= 0)
            if (tipo_docu != 'B' && tipo_docu != 'N')
                calcula_totales();
            else
                calcula_totales();

       }
    });
    
}

	function verificarProductoDetalle(codigoProducto,codigoAlmacen){
		n = document.getElementById('tblDetalleComprobante').rows.length;	
		isEncuentra=false;
		if(n!=0){
			for(x=0;x<n;x++){
				d="detaccion["+x+"]";
				accionDetalle=document.getElementById(d).value;
				if(accionDetalle!="e"){
					/***verificamos si existe el mismo producto y no lo agregamos**/
					a="almacenProducto["+x+"]";
					c="prodcodigo["+x+"]";
					almacenProducto=document.getElementById(a).value;
					codProducto=document.getElementById(c).value;
					if(codProducto==codigoProducto && almacenProducto==codigoAlmacen){
						isEncuentra=true;	
						break;
					}
				}
			}
		}
		return isEncuentra;
	}
	
	/**mostrar agregar servicio si solamente tiene muchas guia de remision**/
	function verificarServicio(objeto){
		valorBS=$(objeto).val();
		/**OBTENENEMOS CANTIDAD SI EXISTE GUIAS DE REMISION RELACIONADAS**/
		var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		if(total>0){
			/**si es servicio***/
			if(valorBS=='S'){
				//document.getElementById("buscar_producto").readOnly = false;
                document.getElementById("tempde_producto").readOnly = false;
				$("#idDivAgregarProducto").show(200);
			}
			/**si es Bien**/
			if(valorBS=='B'){
				//document.getElementById("buscar_producto").readOnly = true;
                document.getElementById("tempde_producto").readOnly = false;
				$("#idDivAgregarProducto").hide(200);
			}
		}
	}
	

    function verPdf(){
    var dataEviar="_____";
    tipo_oper2 = $("#Rtipo_oper").val();
    tipo_docu2 = $("#Rtipo_docu").val();
    fechai2=$("#fechai").val().split("/");
    fechafin=$("#fechaf").val().split("/");
    series=$("#seriei").val();
    numeros=$("#numero").val();
    rucCs=$("#ruc_cliente").val();
    nombreCliente=$("#nombre_cliente").val();
    ruc_prove=$("#ruc_proveedor").val();
    nomb_proveer=$("#nombre_proveedor").val();
    //fechafin=$("#fechaf").val().split("/");
    var datafechaIni="";
    var datafechafin="";
    if($("#fechai").val()!=""){
        datafechaIni=fechai2[2]+"-"+fechai2[1]+"-"+fechai2[0];
    }
    if($("#fechaf").val()!=""){
        datafechafin=fechafin[2]+"-"+fechafin[1]+"-"+fechafin[0];
    }
    if(tipo_oper2=='V'){
        dataEviar=datafechaIni+"_"+datafechafin+"_"+series+"_"+numeros+"_"+rucCs+"_"+nombreCliente;
        alert(series);
    }else{
        dataEviar=datafechaIni+"_"+datafechafin+"_"+series+"_"+numeros+"_"+ruc_prove+"_"+nomb_proveer;
    }
    var url = base_url+ "index.php/ventas/comprobante/verPdf/" + tipo_oper2 + "/" + tipo_docu2+"/"+dataEviar;   
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
 
}
function ver_reporte_productos() {

    var prod = $("#productoDescripcion").val();

    var anio = $("#anioVenta").val();
    var mes = $("#mesventa").val();
    var fech1 = $("#fech1").val();
    var fech2 = $("#fech2").val();
   
    var tipodocumento = $("#tipodocumento").val();
    var Prodcod = $("#reporteProducto").val();
    
    if(anio=="0") {anio="--";} 
    if(mes=="")   {mes="--";} 
  
    if(tipodocumento=="")  {tipodocumento="--";}

    var datafechaIni="";var datafechafin="";

    if(fech1=="") {
        fech1="--";
    }else{
        fechai=$("#fech1").val().split("/"); 
        fech1=fechai[2]+"-"+fechai[1]+"-"+fechai[0];
    }

    if(fech2=="") {
        fech2="--";
    }else{
        fechaf=$("#fech2").val().split("/");
        fech2=fechaf[2]+"-"+fechaf[1]+"-"+fechaf[0];

    }
    

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf_productos/" + anio+"/" + mes+"/" + fech1+"/" + fech2+"/"+tipodocumento +"/"+Prodcod;
    if(Prodcod!="" && prod !="")  {
    	 if($("#fech1").val() <= $("#fech2").val())  {
    		 window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
    	 }else{
    	    	alert("Seleccione un rango de fechas validas");
    	    	$("#fech2").focus();
    	    }
    }else{
    	alert("Seleccione un Producto");
    	$("#productoDescripcion").focus();
    }
 
}
function modificar_cantidad(n){
    a="prodcantidad["+n+"]";
    b="pendiente["+n+"]";
    cantidad= document.getElementById(a).value;
    document.getElementById(b).value=cantidad;
}
function calcula_cantidad_pendiente(n){
  //ocodigo=$("#ordencompra").val();
  a="prodcodigo["+n+"]";
  b="prodcantidad["+n+"]";
  c="oventacod["+n+"]";
var  prod=document.getElementById(a).value;
var cant=document.getElementById(b).value;
var  ocodigo=document.getElementById(c).value;
//  alert("ocodigo"+ocodigo+" "+"producto"+prod+" "+"cantidad "+ cant);
/*var cantidadElm = document.getElementById(b);
cantidad= cantidadElm.value;*/

    url = base_url+"index.php/ventas/comprobante/calcula_ocantidad_pendiente/"+ocodigo+"/"+prod+"/"+cant;
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
    })
    .done(function(data) {

        console.log(data.estado);
      if (data.estado==1) {
         $("#grabarComprobante").show();
      }
      else{
        
        alert("la cantidad ingresado en mayor a la cantidad de O. Compra"+"  "+data.cantidad);
       // cantidadElm.value = event.target.defaultValue;

        $("#grabarComprobante").hide();
      }
      
    })  
}

function calcula_cantidad(n){
  a="prodcodigo["+n+"]";
  b="prodcantidad["+n+"]";
  c="pendiente["+n+"]";
  d="oventacod["+n+"]";
  var cantiTag = document.getElementById(b);
  prod=document.getElementById(a).value;
  cant=cantiTag.value;
  pend=document.getElementById(c).value;
  codvc=document.getElementById(d).value;
  //alert("ocodigo"+codvc+" "+"producto"+prod+" "+"cantidad "+ cant+" "+"pendiente "+ pend);

    url = base_url+"index.php/ventas/comprobante/cantidad_oregistrada/"+codvc+"/"+prod+"/"+cant+"/"+pend;
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
    })
    .done(function(data) {
      if (data.estado==1) {
         $("#grabarComprobante").show();
      }
      else{
        alert("la cantidad ingresado en mayor a la cantidad de O. Compra"+"  "+data.cantidad);
        cantiTag.value = cantiTag.defaultValue;
        $("#grabarComprobante").hide();
      }
      
    })   
}

function togglePedir(indice) {
    var pedir = document.getElementById("pedir["+indice+"]").checked;

    document.getElementById("detaccion["+indice+"]").value = pedir ? 'n' : 'EE';

    calcula_totales();
}

/*********************************************
/*FUNCIONES PARA CANJE DE DOCUMENTO
/*01/01/2020
/*********************************************/

function obtenerSerieNumero() {
    var tipo_documento=$("#cmbDocumento").val();
    url = base_url+"index.php/ventas/comprobante/obtenerSerieNumero";

    $.ajax({
        url: url,
        type: "POST",
        data:{ tipo_documento: tipo_documento },
        dataType: "json",
        error: function(data){
           
        },
        beforeSend: function(){
            
        },
        success: function(data){
            if (!$.isEmptyObject(data)) {
                $("#serie_suger_b").val(data[0].serie);
                $("#numero_suger_b").val(data[0].numero);
            }
        }
    });
}

function canjear_documento(codigo) {
        
        $(".modal-canje").modal("toggle");

        url = base_url+"index.php/ventas/comprobante/canje_documento";

        $.ajax({
            url: url,
            type: "POST",
            data:{ codigo: codigo },
            dataType: "json",
            error: function(data){
               
            },
            beforeSend: function(){
                
            },
            success: function(data){
                if (!$.isEmptyObject(data)) {
                    datos_comprobante = data.datos;
                    $("#cod_cliente").val(data[0].codigo_cliente);
                    $("#cod_comprobante").val(data[0].cod_comprobante);
                    $("#titulo_tabla").val(data[0].titulo_tabla);
                    $("#moneda").val(data[0].moneda);
                    $("#comprobantes").val(data[0].comprobantes);
                    $("#nombre_cliente_canje").val(data[0].nombre_cliente);
                    $("#ruc_cliente_canje").val(data[0].ruc_cliente);
                    $("#direccion_cliente").val(data[0].direccion_cliente);
                    $("#numeroAutomatico").val(data[0].numeroAutomatico);
                    $("#serie_suger_b").val(data[0].serie_suger_b);
                    $("#numero_suger_b").val(data[0].numero_suger_b);
                    $("#tipo_operacion").val(data[0].tipo_operacion);
                    $("#operacion").val(data[0].operacion);
                    $("#serie_numero").val(data[0].serie_numero);
                    $("#total_comprobante").val(data[0].total_comprobante);
                    $("#fecha_comprobante").val(data[0].fecha_comprobante);
                }
            }
        });
}
///////////////////////////////////////////////////////////////////////////////