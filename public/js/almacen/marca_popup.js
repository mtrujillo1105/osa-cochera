var base_url;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    $("#buscarMarca").click(function(){
         $("#form_busqueda").submit();
    });
    $("#limpiarMarca").click(function(){
        url = base_url+"index.php/maestros/marca/marca_ventana_busqueda/0/1"; //1: espara decirle al contraldor que limpie las valirables de la sesión para la busqueda
        location.href=url;
    });
    $('#cerrarMarca').click(function(){
      parent.$.fancybox.close(); 
    });
    
    $('#imgCancelarMarca').click(function(){
      parent.$.fancybox.close();
    });
    
    $("#imgGuardarMarca").click(function(){
        $("#frmMarca").submit();
    });
    
});
function seleccionar_marca(codigo,interno,familia,stock,costo, flagGenInd){
    parent.seleccionar_marca(codigo,interno,familia,stock,costo,flagGenInd);
    parent.$.fancybox.close(); 
}

function editar_marca(marca){
	var base_url = $("#base_url").val();
	url           = base_url+"index.php/maestros/marca/editar_marca_popup/"+marca;
	location.href = url;
}

function valida_marca(){
    unidad           = "unidad_medida[0]";
    /*if($("#codigo_familia").val()==""){
        $("#codigo_familia").select();
        alert('Debe ingresar una familia');
        $("#linkVerFamilia").focus();
        return false;
    }*/
    if($("#tiene_padre").attr('checked')==true && $("#padre").val()==""){
        alert('Debe seleccionar un marca.');
        $("#linkVerMarca").focus();
        return false;
    }
    else if($("#nombre_marca").val()==""){
        $("#nombre_marca").select();
        alert('Debe ingresar un nombre de marca');
        $("#nombre_marca").focus();
        return false;
    }
   else if(document.getElementById(unidad).value==""){
        alert('Debe ingresar una unidad');
        document.getElementById(unidad).focus();
        return false;
    }
    return true;
}