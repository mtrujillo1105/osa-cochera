jQuery(document).ready(function(){
    $('#loading-sunat').css('opacity',0);

    $("#ruc").keyup(function(event){
        var ruc = event.target.value;
        var activeSunat = /^\d{11}$/.test(ruc);
        $("#search-sunat").attr('disabled', !activeSunat ? true : false).css('opacity', !activeSunat ? '.2' : '1');
    }).trigger('keyup'); //triger hace que se lance el evento una vez que se ejecutala funcion

    $("#search-sunat").click(function (event) {
        $('#search-sunat').attr('disabled',true);
        $('#search-sunat').css('opacity',.2);
        $('#loading-sunat').css('opacity',1);
        $('#imgGuardarCliente').attr('disabled',true); 
        $('#imgGuardarCliente').css('opacity',.2);
        $('#imgGuardarProveedor').attr('disabled',true); 
        $('#imgGuardarProveedor').css('opacity',.2);
        event.stopPropagation();
        var ruc=$("#ruc").val();
        var url = base_url+"index.php/empresa/cliente/cliente_sunat";
        
        $.ajax({
            type    : "POST",
            url     : url,
            dataType: "json",
            data    : {ruc:ruc},
            success : function(data) {
                var cliente = data.cliente;                
                $("#razon_social").val(cliente.result['razon_social']);
                $("#direccion").val(cliente.result['direccion']);
                $('#empresa_msg').text(data.message);
                $('#ruc_msg').text(data.message);
                $('#cboDepartamento').append('<option value="'+cliente.result['ubigeo']['UBIGC_CodDpto']+'" selected="selected">'+cliente.result['ubigeo']['UBIGC_DescripcionDpto']+'</option>');
                $('#cboProvincia').append('<option value="'+cliente.result['ubigeo']['UBIGC_CodProv']+'" selected="selected">'+cliente.result['ubigeo']['UBIGC_DescripcionProv']+'</option>');                
                $('#cboDistrito').append('<option value="'+cliente.result['ubigeo']['UBIGC_CodDist']+'" selected="selected">'+cliente.result['ubigeo']['UBIGC_Descripcion']+'</option>');
                
                $('#loading-sunat').css('opacity',0);  
                $('#search-sunat').attr('disabled',false);
                $('#search-sunat').css('opacity',1);
                
                $('#imgGuardarCliente').attr('disabled',false); 
                $('#imgGuardarCliente').css('opacity',1);
                $('#imgGuardarProveedor').attr('disabled',false); 
                $('#imgGuardarProveedor').css('opacity',1);
            },
            error : function(data){
                $('#empresa_msg').text(data.responseText.substr(12,33));
                $('#ruc_msg').text(data.responseText.substr(12,33));
                $('#loading-sunat').css('opacity',0);  
                $('#search-sunat').attr('disabled',false);
                $('#search-sunat').css('opacity',1);  
                $('#imgGuardarCliente').attr('disabled',false); 
                $('#imgGuardarCliente').css('opacity',1);
                $('#imgGuardarProveedor').attr('disabled',false); 
                $('#imgGuardarProveedor').css('opacity',1);
            }
        });
    });

    $("#search-sunat-comprobante").click(function (event) {
        $('#search-sunat-comprobante').attr('disabled',true);
        $('#search-sunat-comprobante').css('opacity',.2);
        $('#loading-sunat').css('opacity',1);
        event.stopPropagation();

        var toperacion = $("#tipo_oper").val();
        if (toperacion == 'V')
            var ruc = $("#buscar_cliente").val();
        else
            var ruc = $("#buscar_proveedor").val();
        
        var url = base_url+"index.php/empresa/cliente/cliente_sunat";
        $.ajax({
            type    : "POST",
            url     : url,
            dataType: "json",
            data    : {ruc:ruc},
            success : function(data) {
                var tipoCliente = data.tipoCliente;
                var cliente = data.cliente;
                var direc = data.ubigeo;
                var idNvoCliente = data.idNvoCliente;               
                
                if (tipoCliente == 'DNI'){
                    $("#nvoClienteCodeN").val(idNvoCliente);
                    $("#nvoClienteDNI").val(ruc);
                    $("#nvoClienteNombres").val(cliente['nombre']);
                    $("#nvoClientePaterno").val(cliente['paterno']);
                    $("#nvoClienteMaterno").val(cliente['materno']);

                    if (cliente['sexo'] == "Masculino")
                        $("#nvoClienteGenero").val(0).change();
                    else
                        $("#nvoClienteGenero").val(1).change();
                }
                else{
                    $("#nvoClienteCode").val(idNvoCliente);
                    $("#nvoClienteRuc").val(ruc);
                    $("#nvoClienteNombre").val(cliente.result['razon_social']);
                    $("#nvoClienteDireccion").val(cliente.result['direccion']);
                }

                $("#containerNvoCliente").show();
                $('#loading-sunat').css('opacity',0);
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',1);

                if (tipoCliente == "DNI"){
                    $("#juridico").hide();
                    $("#natural").show();
                }
                else{
                    $("#natural").hide();
                    $("#juridico").show();
                }

            },
            error : function(data){
                alert("No se encontro informacion disponible.");
                $("#containerNvoCliente").hide();
                $('#loading-sunat').css('opacity',0);  
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',1);  
            }
        });
    });

    $("#cancelarNvoClienteJuridico").click(function(){
        $("#containerNvoCliente").hide();
    });

    $("#cancelarNvoClienteNatural").click(function(){
        $("#containerNvoCliente").hide();
    });

    $("#grabarNvoClienteJuridico").click(function (event) {
        $('#search-sunat-comprobante').attr('disabled',true);
        $('#search-sunat-comprobante').css('opacity',.2);
        $('#loading-sunat').css('opacity',1);
        event.stopPropagation();
        var toperacion = $("#tipo_oper").val();
        if (toperacion == 'V')
            var ruc = $("#buscar_cliente").val();
        else
            var ruc = $("#buscar_proveedor").val();
        
        var url = base_url+"index.php/empresa/cliente/insertar_cliente";
        
        var rucCli = $("#nvoClienteRuc").val();
        var tipo_persona = 1; // PERSONA JURIDICA
        
        var tipo_documento = 1; // DNI
        var cboTipoCodigo = 6; // RUC

        var nombre = $("#nvoClienteNombre").val();
        var direccion = $("#nvoClienteDireccion").val();
        var correo = $("#nvoClienteMail").val();
        var vendedor = $("#nvoClienteVendedor").val();
        var digemin = $("#nvoClienteDigemin").val();
        
        $.ajax({
            type    : "POST",
            url     : url,
            dataType: "json",
            data    : {
                        tipo_persona:tipo_persona,
                        cboTipoCodigo:cboTipoCodigo,
                        ruc:rucCli,
                        razon_social:nombre,
                        direccion:direccion,
                        email:correo,
                        idVendedor:vendedor,
                        digemin:digemin
                    },
            success : function(data) {
                $("#containerNvoCliente").hide();
                $('#loading-sunat').css('opacity',0);
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',0);

                if (toperacion == 'V'){
                    $("#buscar_cliente").val(rucCli);
                    $("#nombre_cliente").val(nombre);
                    $("#cliente").val(data.codigo);
                    $("#ruc_cliente").val(rucCli);
                    $("#cboVendedor > option[value="+vendedor+"]").attr("selected", true);
                    //$("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 

                    if (digemin == 1){
                        $('#tipoComprobante > option[value="F"]').attr('disabled',false);
                        $('#tipoComprobante > option[value="B"]').attr('disabled',false);
                        $('#tipoComprobante > option[value="F"]').attr('selected',true);
                    }
                    else{
                        $('#tipoComprobante > option[value="F"]').attr('disabled',true);
                        $('#tipoComprobante > option[value="B"]').attr('disabled',true);
                        $('#tipoComprobante > option[value="N"]').attr('selected',true);
                    }

                    codigo = data.codigo;
                    get_obra(codigo);
                }
                else{
                    $("#buscar_proveedor").val(rucCli);
                    $("#nombre_proveedor").val(nombre);
                    $("#proveedor").val(data.proveedor);
                    $("#ruc_proveedor").val(rucCli);
                }
                
                $("#containerNvoCliente").hide();
                $('#loading-sunat').css('opacity',0);
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',0);
            },
            error : function(data){
                $("#containerNvoCliente").show();
                $('#loading-sunat').css('opacity',0);  
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',0);
            }
        });
    });

    $("#grabarNvoClienteNatural").click(function (event) {
        $('#search-sunat-comprobante').attr('disabled',true);
        $('#search-sunat-comprobante').css('opacity',.2);
        $('#loading-sunat').css('opacity',1);
        event.stopPropagation();

        var toperacion = $("#tipo_oper").val();
        if (toperacion == 'V')
            var ruc = $("#buscar_cliente").val();
        else
            var ruc = $("#buscar_proveedor").val();

        var url = base_url+"index.php/empresa/cliente/insertar_cliente";
        
        var dni = $("#nvoClienteDNI").val();
        var numero_documento = dni;

        var tipo_persona = 0; // PERSONA NATURAL
        var tipo_documento = 1; // DNI

        var nombre = $("#nvoClienteNombres").val();
        var paterno = $("#nvoClientePaterno").val();
        var materno = $("#nvoClienteMaterno").val();
        var genero = $("#nvoClienteGenero").val();

        var direccion = $("#nvoClienteDireccion").val();
        var correo = $("#nvoClienteMailN").val();
        var vendedor = $("#nvoClienteVendedorN").val();
        var digemin = $("#nvoClienteDigeminN").val();
        
        $.ajax({
            type    : "POST",
            url     : url,
            dataType: "json",
            data    : {
                        tipo_persona:tipo_persona,
                        tipo_documento:tipo_documento,
                        numero_documento:numero_documento,
                        nombres:nombre,
                        paterno:paterno,
                        materno:materno,
                        cboSexo:genero,
                        email:correo,
                        idVendedor:vendedor,
                        digemin:digemin
                    },
            success : function(data) {
                $("#containerNvoCliente").hide();
                $('#loading-sunat').css('opacity',0);
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',0);

                //$("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 

                if (toperacion == 'V'){
                    $("#buscar_cliente").val(dni);
                    $("#nombre_cliente").val(nombre + ' ' + paterno + ' ' + materno);
                    $("#cliente").val(data.codigo);
                    $("#ruc_cliente").val(dni);
                    $("#cboVendedor > option[value="+vendedor+"]").attr("selected", true);

                    if (digemin == 1){
                        $('#tipoComprobante > option[value="F"]').attr('disabled',false);
                        $('#tipoComprobante > option[value="B"]').attr('disabled',false);
                        $('#tipoComprobante > option[value="B"]').attr('selected',true);
                    }
                    else{
                        $('#tipoComprobante > option[value="F"]').attr('disabled',true);
                        $('#tipoComprobante > option[value="B"]').attr('disabled',true);
                        $('#tipoComprobante > option[value="N"]').attr('selected',true);
                    }
                    
                    codigo = data.codigo;
                    get_obra(codigo);
                }
                else{
                    $("#buscar_proveedor").val(dni);
                    $("#nombre_proveedor").val(nombre + ' ' + paterno + ' ' + materno);
                    $("#proveedor").val(data.proveedor);
                    $("#ruc_proveedor").val(dni);
                }
                codigo = data.codigo;
            },
            error : function(data){
                $("#containerNvoCliente").show();
                $('#loading-sunat').css('opacity',0);  
                $('#search-sunat-comprobante').attr('disabled',false);
                $('#search-sunat-comprobante').css('opacity',0);  
            }
        });
    });
});