jQuery(document).ready(function(){
        base_url     = $("#base_url").val();
        tipo_cuenta  = $("#tipo_cuenta").val();
        cuenta       = $("#cuenta").val();

 	$("#grabarFlujocaja").click(function(){
                $('img#loading').css('visibility','visible');
                url = base_url+"index.php/tesoreria/flujocaja/grabar";
                dataString  = $('#frmFlujocaja').serialize();
                $.post(url,dataString,function(data){
                        $('img#loading').css('visibility','hidden');
                        switch(data.result){
                            case 'ok': location.href = base_url+"index.php/tesoreria/flujocaja/listar"+"/"+cuenta;
                                    break;
                            case 'error': 
                                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                    break;
                        }
                },'json');
        });
	$("#limpiarFlujocaja").click(function(){
		url =  location.href = base_url+"index.php/tesoreria/flujocaja/listar"+"/"+cuenta;
		location.href = url;
	});
        $("#cancelarFlujocaja").click(function(){
		url =  location.href = base_url+"index.php/tesoreria/cuentas/listar"+"/"+tipo_cuenta;
		location.href = url;		
	});
        $("#atrasFlujocaja").click(function(){
		url =  location.href = base_url+"index.php/tesoreria/cuentas/listar"+"/"+tipo_cuenta;
		location.href = url;
	});
	
 
})

