var base_url;
var flagBS;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    flagBS   = $("#flagBS").val();
    $("#buscarProducto").click(function(){
         $("#form_busqueda").submit();
    });
    $("#limpiarProducto").click(function(){
        url = base_url+"index.php/almacen/producto/ventana_busqueda_producto/"+flagBS+"/0/1"; //1: espara decirle al contralador que limpie las variables de la sesión para la busqueda
        location.href=url;
    });
    $('#cerrarProducto').click(function(){
      parent.$.fancybox.close(); 
    });
    
    $('#imgCancelarProducto').click(function(){
      parent.$.fancybox.close();
    });
    
    $("#imgGuardarProducto").click(function(){
        $("#frmProducto").submit();
    });
    
    $('#txtCodigo, #txtNombre, #txtFamilia').keyup(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#form_busqueda").submit();
        } 
    });
    
    /**seleccionar combo almacen nos cambia los articulos a buscar***/
    $("#almacen").change(function(){
    	var base_url = $("#base_url").val();
    	tipo_oper = $("#tipo_oper").val();
    	almacenSeleccionado=$(this).val();
    	/**ejecutamos el ajax**/
    	url= base_url + 'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val()+"/"+almacenSeleccionado; 
    	
    	$.get(url,function(data){
    		//alert('realizado');
    		$('#form_busqueda').html('');
            $('#form_busqueda').html(data);
        });
    	/**fin de ejecucion**/
    });
    
    
});
function seleccionar_producto(codigo,interno,familia,stock,costo, flagGenInd,codigoAlmacenProducto){
    parent.seleccionar_producto(codigo,interno,familia,stock,costo,flagGenInd,codigoAlmacenProducto);
    parent.$.fancybox.close();
     
}

function editar_producto(producto){
	var base_url = $("#base_url").val();
	url           = base_url+"index.php/almacen/producto/editar_producto_popup/"+producto;
	location.href = url;
}

function eliminar_productoproveedor(n){
    a = "nombre_proveedor["+n+"]"
    b = "proveedor["+n+"]";
    c = "productoproveedor["+n+"]";
    nombre_proveedor  = document.getElementById(a).value;
    proveedor         = document.getElementById(b).value;
    producto          = document.getElementById("producto").value;
    productoproveedor = document.getElementById(c).value;
    if(confirm('Esta seguro desea eliminar \n'+nombre_proveedor+'?')){
        dataString        = "productoproveedor="+productoproveedor;
        url = base_url+"index.php/almacen/producto/eliminar_productoproveedor";
        $.post(url,dataString,function(data){
			//http://192.168.1.90/ferresat/index.php/almacen/producto/editar_producto_popup/256
            location.href = base_url+"index.php/almacen/producto/editar_producto_popup/"+producto;
        });
    }
}

function valida_producto(){
    unidad           = "unidad_medida[0]";
    /*if($("#codigo_familia").val()==""){
        $("#codigo_familia").select();
        alert('Debe ingresar una familia');
        $("#linkVerFamilia").focus();
        return false;
    }*/
    if($("#tiene_padre").attr('checked')==true && $("#padre").val()==""){
        alert('Debe seleccionar un producto.');
        $("#linkVerProducto").focus();
        return false;
    }
    else if($("#nombre_producto").val()==""){
        $("#nombre_producto").select();
        alert('Debe ingresar un nombre de producto');
        $("#nombre_producto").focus();
        return false;
    }
   else if(document.getElementById(unidad).value==""){
        alert('Debe ingresar una unidad');
        document.getElementById(unidad).focus();
        return false;
    }
    return true;
}