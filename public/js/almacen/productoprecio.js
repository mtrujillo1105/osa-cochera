var base_url;
jQuery(document).ready(function(){
    base_url  = $('#base_url').val();
    
    $("#buscarProductoPrecio").click(function(){
        $("#form_busqueda").submit();
    });	
    
    $("#grabarProductoPrecio").click(function(){
            $('img#loading').css('visibility','visible');
            url = base_url+"index.php/almacen/producto/productos_precios_grabar";
            dataString  = $('#frmProductoPrecio').serialize();
            $.post(url,dataString,function(data){
                    $('img#loading').css('visibility','hidden');
                    switch(data.result){
                        case 'ok': $("#buscarProductoPrecio").click();
                                break;
                        case 'error': 
                                $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                break;
                    }
            },'json');
    });
    
    $('#txtCodigo, #txtNombre, #txtFamilia, #txtMarca, #txtFechaIni, #txtCantMin').keypress(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#form_busqueda").submit();
        } 
    });
    
    $('#form_busqueda').submit(function(){
       if(($('#txtFechaIni').val()!='' && $('#txtCantMin').val()=='') || ($('#txtFechaIni').val()=='' && $('#txtCantMin').val()!='')){
           alert('Debe ingresar la fecha inicial y la cantidad mínima');
           $('#txtCantMin').focus();
           return false;
       }
       return true;
           
    });
});
