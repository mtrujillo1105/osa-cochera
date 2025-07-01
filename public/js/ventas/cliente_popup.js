$(document).ready(function() {
    base_url   = $("#base_url").val();

    $('.defaultDOMWindow').openDOMWindow({ 
        eventType:'click', 
        loader:1, 
        loaderImagePath:'animationProcessing.gif', 
        loaderHeight:16, 
        loaderWidth:17,
        width:500,
        height:340
    });
    $('.defaultCloseDOMWindow').closeDOMWindow({eventType:'click'});

    $("#buscarCliente").click(function(){
         $("#form_busqueda").submit();
    });	
    $("#nuevoCliente").click(function(){
        $('#open').click();
        $("#datosEmpresa").show();
        $("#datosPersona").hide();
        
    });
    $("#limpiarCliente").click(function(){
        url = base_url+"index.php/empresa/cliente/ventana_busqueda_cliente/0/1";
        location.href=url;
    });
	
    $("#imgGuardarCliente").click(function(){
        dataString  = $('#frmCliente').serialize();
        url = base_url+"index.php/empresa/cliente/insertar_cliente";
		//alert(dataString);
		//$("#form_busqueda").submit();
        $.post(url,dataString,function(data){
                switch(data.result){
                    case 'ok': 
                        $('#form_busqueda').submit();
                           break;
                    case 'error': 
                            limpiar_campos_genericos();
                            $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                            $('#'+data.campo+'_msg').html(data.msg);
                            break
                }
        },'json');
		
    });
	
    $("#imgCancelarCliente").click(function(){
        $('#close').click();
    });

    $(":radio").click(function(){
        valor = $(this).attr("value");
        limpiar_campos();
        if(valor==0){//Persona
            $("#datosEmpresa").hide();
            $("#datosPersona").show();
                $("#nombres").focus();
        }
        else if(valor==1){//Empresa
            $("#datosEmpresa").show();
            $("#datosPersona").hide();
            $("#ruc").focus();
        }
    });
    $('#cerrarCliente').click(function(){
      parent.$.fancybox.close(); 
    });
    $('#numdoc, #nombre').keyup(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#form_busqueda").submit();
        } 
    });
});

function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
     parent.seleccionar_cliente(codigo,ruc,razon_social, empresa, persona);
     parent.$.fancybox.close(); 
}
function limpiar_campos(){
    //Para los campos de la empresa
    $("#cboTipoCodigo").val('0');
    $("#ruc").val('');
    $("#razon_social").val('');
    //Para los campos de la persona
    $("#numero_documento").val('');
    $("#nombres").val('');
    $("#paterno").val('');
    $("#materno").val('');
    //Para los capos comunes
    $("#cboTipoCodigo").val('1');
    $("#direccion").val('');
    $("#telefono").val('');
    $("#movil").val('');
    $("#fax").val('');
    $("#email").val('');
    $("#web").val('');   
    $("#categoria").val('');  

    limpiar_campos_genericos();
}
function limpiar_campos_genericos(){
    $('input[type="text"]').css('background-color', '#FFFFFF');
    $('.etiqueta_error').html('');
}