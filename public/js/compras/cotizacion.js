jQuery(document).ready(function(){
    var base_url   = $("#base_url").val();
    $("#nuevaCotizacion").click(function(){
        url = base_url+"index.php/compras/cotizacion/nueva_cotizacion";
        location.href = url;
    });
    $("#grabarCotizacion").click(function(){
        $("#frmCotizacion").submit();
    });
    $("#limpiarCotizacion").click(function(){
        url = base_url+"index.php/compras/cotizacion/cotizaciones";
        location.href = url;
    });
    $("#cancelarCotizacion").click(function(){
        url = base_url+"index.php/compras/cotizacion/cotizaciones";
        location.href = url;
    });
    $("#buscarCotizacion").click(function(){
        dataString = $("#form_busquedaOcompra").serialize();
        txtCargo   = $("#txtCargo").val();
        if(txtCargo!=''){
            $("#form_busquedaCargo").submit();
        }
        else{
            $("#txtCargo").focus();
            alert('Debe ingresar un nombre a buscar.');
        }
    });
});
function editar_cotizacion(cotizacion){
    var base_url   = $("#base_url").val();
    location.href = base_url+"index.php/compras/cotizacion/editar_cotizacion/"+cotizacion;
}
function eliminar_cotizacion(cotizacion){
    var base_url   = $("#base_url").val();
    if(confirm('Esta seguro desea eliminar a esta cotizacion?')){
            dataString   = "codigo="+cotizacion;
    url                  = base_url+"index.php/compras/cotizacion/eliminar_cotizacion";;
            $.post(url,dataString,function(data){
                    location.href = base_url+"index.php/compras/cotizacion/cotizaciones";
            });
    }
}
function ver_cotizacion(cotizacion){
    var base_url   = $("#base_url").val();
    location.href = base_url+"index.php/compras/cotizacion/ver_cotizacion/"+cotizacion;
}
function ver_cotizacion_pdf(cotizacion){
    var base_url   = $("#base_url").val();
    url = base_url+"index.php/compras/cotizacion/ver_cotizacion_pdf/"+cotizacion;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_cotizacion(){
    var base_url   = $("#base_url").val();
    location.href = base_url+"index.php/compras/cotizacion/cotizaciones";
}
function valida_cotizacion(){
    if($("#nombre_proveedor").val()==""){
        alert("Ingrese un proveedor");
        $("#ruc").select();
        return false;
    }
    else if($("#lugar_entrega").val()==""){
        alert("Seleccione un almacen.");
        $("#lugar_entrega").select();
        return false;
    }
    else if($("#pedido").val()==""){
        alert("Seleccione un orden de pedido.");
        $("#pedido").select();
        return false;
    }
    else if($("#forma_pago").val()==""){
        alert("Seleccione la forma de pago");
        $("#forma_pago").select();
        return false;
    }
    else if($("#condicion_entrega").val()==""){
        alert("Seleccione la condicion de entrega");
        $("#condicion_entrega").select();
        return false;
    }
}
function agregar_producto_cotizacion(){
        codproducto     = $("#codproducto").val();
        producto        =  $("#producto").val();
        nombre_producto = $("#nombre_producto").val();
        descuento       = $("#descuento").val();
        igv             = $("#igv").val();
        cantidad        = $("#cantidad").val();
        unidad_medida   = $("#unidad_medida").val();
        select_umedida  = document.getElementById("unidad_medida");
        options_umedida = select_umedida.getElementsByTagName("option");
        nombre_unidad_medida = $("#nombre_unidad_medida").val();
	n = document.getElementById('tblDetalleCotizacion').rows.length;
	j = n+1;	
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
	if(nombre_producto!='' && unidad_medida!='0')
	{
            fila = '<tr class="'+clase+'">';
            fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_cotizacion('+n+');">';
            fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
            fila+='</a></strong></font></div></td>';
            fila+=	'<td width="6%">';
            fila+='<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">';
            fila+='<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
            fila+='<div align="center">'+j+'</div>';
            fila+='</td>';
            fila+= '<td width="10%"><div align="left">'+codproducto+'</div></td>';
            fila+=	'<td width="54%"><div align="left">'+nombre_producto+'</div></td>';
            fila+= '<td width="14%"><div align="center">'+nombre_unidad_medida+'</div></td>';
            fila+= '<td width="13%"><div align="center">';
            fila+='<input type="hidden" class="cajaMinima" name="detcotiz['+n+']" id="detcotiz['+n+']">';
            fila+='<input type="hidden" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
            fila+='<input type="text" class="cajaPequena2" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onkeypress="return numbersonly(this,event)">';
            fila+='</div></td>';
            fila+= '</tr>';
            $("#tblDetalleCotizacion").append(fila);
            //Inicializo valores de carga
            $("#producto").val('');
            $("#codproducto").val('');
            $("#nombre_producto").val('');
            $("#cantidad").val('0');
            $("#unidad_medida").val('0');
            $("#nombre_familia").val('');
            $("#stock").val('0');
            //Elimino el contenido del select
            for(i=0;i<=options_umedida.length;i++){
                 //select_umedida.removeChild(options_umedida[i]);
                select_umedida.options[0] = null;
            }
            opt = document.createElement("option");
            texto = document.createTextNode(":: Seleccione ::");
            opt.appendChild(texto);
            opt.value = "0";
            select_umedida.appendChild(opt);
        }
        else if(codproducto==''){
                $("#codproducto").focus();
                alert('Ingrese un codigo para el producto.');
        }
        //    else if(cantidad=='0'){
        //        $("#cantidad").focus();
        //        alert('Ingrese una cantidad para el producto');
        //    }
    else if(unidad_medida=='0'){
         alert('Seleccione una unidad de medida.');
    }
    else{
         alert('No estan los datos completos');
    }
}
function eliminar_producto_cotizacion(n){
     if(confirm('Esta seguro que desea eliminar este producto?')){
          tabla  = document.getElementById('tblDetalleCotizacion');
          a      = "detcotiz["+n+"]";
          b      = "detaccion["+n+"]";
          fila   = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.backgroundColor = "#FF8000";
          fila.style.display="none";
          document.getElementById(b).value = "e";
     }
}