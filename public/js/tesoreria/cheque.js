jQuery(document).ready(function(){
        base_url   = $("#base_url").val();
       
        $("#limpiarCuenta").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        $("#cancelarCheque").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href = url;
        });
        $("#buscarCheque").click(function(){
            $("#form_busqueda").submit();
        });
        
        $("#limpiarCheque").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        
        $("#limpiarCobro").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        $("#cancelarCobro").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        $("#limpiarDeposito").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        $("#cancelarDeposito").click(function(){
            url = base_url+"index.php/tesoreria/cheque/listar/";
            location.href=url;
        });
        
        $("#grabarCobro").click(function(){
            $('img#loading').css('visibility','visible');
            url = base_url+"index.php/tesoreria/cheque/cobro_grabar";
            dataString  = $('#frmCheque').serialize();
            $.post(url,dataString,function(data){
                    $('img#loading').css('visibility','hidden');
                    switch(data.result){
                        case 'ok': location.href = base_url+"index.php/tesoreria/cheque/listar/";
                                break;
                        case 'error': 
                                if(data.campo){
                                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                }else
                                    if(data.msj)
                                        alert(data.msj);
                                break;
                    }
            },'json');
	}); 
        
        $("#grabarDeposito").click(function(){
            $('img#loading').css('visibility','visible');
            url = base_url+"index.php/tesoreria/cheque/deposito_grabar";
            dataString  = $('#frmCheque').serialize();
            $.post(url,dataString,function(data){
                    $('img#loading').css('visibility','hidden');
                    switch(data.result){
                        case 'ok': location.href = base_url+"index.php/tesoreria/cheque/listar/";
                                break;
                        case 'error': 
                                if(data.campo){
                                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                }else
                                    if(data.msj)
                                        alert(data.msj);
                                break;
                    }
            },'json');
	}); 
})

function ver_cobro(cheque){
        location.href = base_url+"index.php/tesoreria/cheque/cobro/"+cheque;
}
function ver_deposito(cheque){
        location.href = base_url+"index.php/tesoreria/cheque/deposito/"+cheque;
}
