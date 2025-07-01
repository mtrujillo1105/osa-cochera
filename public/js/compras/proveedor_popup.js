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

    $("#buscarProveedor").click(function(){
         $("#form_busqueda").submit();
    }); 
    $("#nuevoProveedor").click(function(){
        $('#open').click();
        $("#datosEmpresa").hide();
        $("#datosPersona").show();
                   });
    $("#limpiarProveedor").click(function(){
        url = base_url+'index.php/empresa/proveedor/ventana_busqueda_proveedor/0/1';
        location.href=url;
    });
    $("#imgGuardarProveedor").click(function(){
        dataString  = $('#frmProveedor').serialize();
        url = base_url+'index.php/empresa/proveedor/insertar_proveedor';
        //alert("hola prueba");
         //$("#form_busqueda").submit();
         $.post(url,dataString,function(data){

                switch(data.result){
                    case 'ok': 
                        ///stv
                        $('#form_busqueda').submit();
                        ////
                        
                        ///bloqueado stv
//                               if($('#ruc_persona').val()=='') numdoc=$('#numero_documento').val()
//                               else numdoc=$('#ruc_persona').val()
//                               if($("#tipo_persona:checked").val()=='0')
//                                  seleccionar_cliente(data.codigo, numdoc, $('#nombres').val()+' '+$('#paterno').val()+' '+$('#materno').val(), data.empresa);
//                               else
//                                seleccionar_cliente(data.codigo, $('#ruc').val(), $('#razon_social').val(), data.empresa);
                        ////   
                           break;
                    case 'error': 
                            limpiar_campos_genericos();
                            $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                            $('#'+data.campo+'_msg').html(data.msg);
                            break
                }
        },'json');
    });
    $("#imgCancelarProveedor").click(function(){
        $('#close').click();
    });

    $(":radio").click(function(){
        valor = $(this).attr("value");
        limpiar_campos();
        if(valor==0){//Persona
            $("#datosEmpresa").hide();
            $("#datosPersona").show();
            if(window.opener.tipo_docu!='B')
                $("#ruc_persona").focus();
            else
                $("#nombres").focus();
        }
        else if(valor==1){//Empresa
            $("#datosEmpresa").show();
            $("#datosPersona").hide();
            $("#ruc_search").focus();
        }
    });
    $('#cerrarProveedor').click(function(){
      parent.$.fancybox.close(); 
    });
    $('#numdoc, #nombre').keyup(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#form_busqueda").submit();
        } 
    });
});
function seleccionar_proveedor(codigo,ruc,razon_social, empresa, persona){
     parent.seleccionar_proveedor(codigo,ruc,razon_social, empresa, persona);
     parent.$.fancybox.close(); 
}
function limpiar_campos(){
    //Para los campos de la empresa
    $("#cboTipoCodigo").val('0');
    $("#ruc_search").val('');
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