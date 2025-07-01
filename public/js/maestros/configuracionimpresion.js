var base_url
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    
    $("#configuracionimprecion_nueva").click(function(){
        url = base_url+"index.php/maestros/configuracionimpresion/nueva_configuracionimpersion";
        location.href = url;
    });
        
    $("#grabarConfiguracionImpre").click(function(){
        $("#fmrModificarImpresion").submit();     
    }); 
    
    
    $("#limpiarConfiguracionImpre").click(function(){
        url = base_url+"index.php/ventas/presupuesto/presupuestos/0/1"; //1: espara decirle al contraldor que limpie las valirables de la sesión para la busqueda
        location.href = url;
    });
    
    $("#cancelarConfiguracionImpre").click(function(){
        url = base_url+"index.php/maestros/configuracionimpresion/configuracion_index";
        location.href = url;		
    });
   
});
var posicionAnterior=-1;
function divSeleccionadoModificar(posicion){
	if(posicionAnterior!=-1){
		$('#divModificacion_'+posicionAnterior).css('border',"2px solid black");
		$('#divModificacion_'+posicionAnterior).css('background',"none");
		$('#divModificacion_'+posicionAnterior).css('opacity',"1");
        $('#divModificacion_'+posicionAnterior).css('color',"black");
	}
	
	$('#divModificacion_'+posicion).css('border',"2px solid red");
	$('#divModificacion_'+posicion).css('background',"red");
	$('#divModificacion_'+posicion).css('opacity',"0.5");
    $('#divModificacion_'+posicion).css('color',"black");
	posicionAnterior=posicion;
}

function modificarDivConfiguracionDocumento(posicion) {   
    height = $('#campodo_height'+posicion).val();
    width = $('#campodo_width'+posicion).val();
    posx = $('#campodo_posx'+posicion).val();
    posy = $('#campodo_posy'+posicion).val();
    tamletra = $('#campodo_tamletra'+posicion).val();
    tipoletra = $('#campodo_tipoletra'+posicion).val();
    $('#divModificacion_'+posicion).animate({left:posx,top:posy});
    $('#divModificacion_'+posicion).css('height',height);
    $('#divModificacion_'+posicion).css('width',width);
    $('#divModificacion_'+posicion).css('font-size',tamletra+"px");
}

function editar_configuracionimpersion(configuracionimpersion){
    location.href = base_url+"index.php/maestros/configuracionimpresion/configuracionimpersion_editar/"+configuracionimpersion;
}        

