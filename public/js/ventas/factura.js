jQuery(document).ready(function(){
	base_url   = $("#base_url").val();
	$("#nuevaFactura").click(function(){
		url = base_url+"index.php/ventas/factura/factura_nueva";
		location.href = url;
	});	
 	$("#grabarFactura").click(function(){
		$("#frmFactura").submit();
	}); 
	$("#limpiarFactura").click(function(){
		url = base_url+"index.php/ventas/factura/facturas";
		location.href = url;
	});
	$("#cancelarFactura").click(function(){
		url = base_url+"index.php/ventas/factura/facturas";
		location.href = url;		
	});
	$("#buscarFactura").click(function(){
		dataString = $("#form_busquedaFactura").serialize();
		txtCargo   = $("#txtCargo").val();
		if(txtCargo!=''){
			$("#form_busquedaCargo").submit();
		}
		else{
			$("#txtCargo").focus();
			alert('Debe ingresar un nombre a buscar.');
		}
	});	
})
function editar_factura(factura){
	location.href = base_url+"index.php/ventas/factura/factura_editar/"+factura;
}
function eliminar_factura(factura){
    if(confirm('Esta seguro que desea eliminar esta factura?')){
        dataString = "factura="+factura;
        url = base_url+"index.php/ventas/factura/factura_eliminar";
        $.post(url,dataString,function(data){
                location.href = base_url+"index.php/ventas/factura/facturas";
        });
    }
}
function ver_factura_pdf(factura){
    var url = base_url+"index.php/ventas/factura/factura_ver_pdf/"+factura;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function ver_factura_pdf2(factura){
    var url = base_url+"index.php/ventas/factura/factura_ver_pdf2/"+factura;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_factura(){
    location.href = base_url+"index.php/ventas/facturta/facturas";
}
function agregar_producto_factura(){
        codproducto  = $("#codproducto").val();
	producto = $("#producto").val();
	nombre_producto = $("#nombre_producto").val();
	descuento = $("#descuento").val();
	igv = $("#igv").val();
	cantidad = $("#cantidad").val();
        precio = $("#precio").val();
        unidad_medida = $("#unidad_medida").val();//select
        select_umedida = document.getElementById("unidad_medida");
        options_umedida = select_umedida.getElementsByTagName("option");
        nombre_unidad = $('#unidad_medida option:selected').html()
	n = document.getElementById('tblDetalleFactura').rows.length;
	j = n+1;
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
	if(nombre_producto!='' && unidad_medida!='0' && cantidad!='')
	{
            fila  = '<tr class="'+clase+'">';
            fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_factura('+n+');">';
            fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
            fila+='</a></strong></font></div></td>';
            fila +=	'<td width="5%"><div align="center">1</div></td>';
            fila += '<td width="10%"><div align="center">';
            fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
            fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
            fila+= '</div></td>';
                    fila +=	'<td width="32%"><div align="left">'+nombre_producto+'</div></td>';
                    fila += '<td width="8%"><div align="center">';
            fila+= '<input type="text" class="cajaPequena2" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" value="" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                    fila += '<td width="8%"><div align="center">';
            fila+= '<input type="text" class="cajaPequena2" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad;
            fila+= '</div></td>';
                    fila += '<td width="8%"><div align="center"><input type="text" class="cajaPequena2" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
                    fila += '<td width="8%"><div align="center">';
            fila+= '<input type="hidden" class="cajaPequena2" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento+'">';
            fila+= '<input type="text" class="cajaPequena2" name="proddescuento['+n+']" id="proddescuento['+n+']" readonly>';
            fila+= '</div></td>';
                    fila += '<td width="8%"><div align="center">';
            fila+= '<input type="hidden" class="cajaPequena2" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
            fila+= '<input type="text" class="cajaPequena2" name="prodigv['+n+']" id="prodigv['+n+']" readonly>';
            fila+= '</div></td>';
            fila += '<td width="10%"><div align="center">';
            fila+='<input type="hidden" class="cajaMinima" value="n" name="detaccion['+n+']" id="detaccion['+n+']">';
            fila+='<input type="hidden" class="cajaMinima" value="" name="detfact['+n+']" id="detfact['+n+']">';
            fila+='<input type="hidden" class="cajaMinima" name="detocom['+n+']" id="detocom['+n+']">';
            fila+= '<input type="text" class="cajaPequena2" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
            fila+= '</div></td>';
            fila += '</tr>';
            $("#tblDetalleFactura").append(fila);
            calcula_importe(n);
            //Inicializo valores
            $("#producto").val('');
            $("#codproducto").val('');
            $("#nombre_producto").val('');
            $("#cantidad").val('');
            $("#nombre_unidad").val('');
            $("#unidad_medida").val('0');
            $("#precio").val('');
            //Elimino el contenido del select
            var num_option=options_umedida.length;
            for(i=1;i<=num_option;i++){
                select_umedida.remove(0)
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
    else if(unidad_medida=='0'){
         alert('Seleccione una unidad de medida.');
         $('#unidad_medida').focus();
    }
    else{
         alert('La cantidad del producto no puede ser menor a 1.');
         $("#cantidad").focus();
    }    
}
function eliminar_producto_ocompra(n){
     if(confirm('Esta seguro que desea eliminar este producto?')){
          a                = "detfact["+n+"]";
          b                = "detaccion["+n+"]";
          fila            = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.display="none";
          document.getElementById(b).value = "e";
     }
}
function calcula_importe(n){
    a  = "prodpu["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "proddescuento["+n+"]";
    d  = "prodigv["+n+"]";
    e  = "prodprecio["+n+"]";
    f  = "prodimporte["+n+"]";
    g = "prodigv100["+n+"]";
    h = "proddescuento100["+n+"]";
    pu = document.getElementById(a).value;
    cantidad = document.getElementById(b).value;
    descuento = document.getElementById(c).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio = pu*cantidad;
    total_dscto = money_format(precio*descuento100/100);
    precio2 = precio-parseFloat(total_dscto);
    total_igv = money_format(precio2*igv100/100);
    
    importe = precio-parseFloat(total_dscto)+parseFloat(total_igv);
    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
    document.getElementById(e).value = precio;
    document.getElementById(f).value = importe;
    
    calcula_totales();
}
function calcula_totales(){
    n = document.getElementById('tblDetalleFactura').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]"
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        importe = parseFloat(document.getElementById(a).value);
        igv = parseFloat(document.getElementById(b).value);
        descuento = parseFloat(document.getElementById(c).value);
        precio = parseFloat(document.getElementById(d).value);
        importe_total = importe + importe_total;
        igv_total = igv + igv_total;
        descuento_total = descuento + descuento_total;
        precio_total = precio + precio_total;
    }
    $("#importetotal").val(importe_total);
    $("#igvtotal").val(igv_total);
    $("#descuentotal").val(descuento_total);
     $("#preciototal").val(precio_total);
}
function modifica_descuento_total(){
     descuento = $('#descuento').val();
     n     = document.getElementById('tblDetalleOcompra').rows.length;
     for(i=0;i<n;i++){
          a = "proddescuento100["+i+"]";
          document.getElementById(a).value = descuento;
     }
     for(i=0;i<n;i++){
         calcula_importe(i);
     }
     calcula_totales();
}
function modifica_igv_total(){
     igv = $('#igv').val();
     n     = document.getElementById('tblDetalleOcompra').rows.length;
     for(i=0;i<n;i++){
          a = "prodigv100["+i+"]";
          document.getElementById(a).value = igv;
     }
     for(i=0;i<n;i++){
         calcula_importe(i);
     }
     calcula_totales();
}
function listar_unidad_medida_producto(producto){
	url          = base_url+"index.php/producto/producto/listar_unidad_medida_producto/"+producto;
	select   = document.getElementById('unidad_medida');
    //option  = document.getElementById('option');
    //padre = option[0].parentNode;
    //padre.removeChild(option[0]);
	$.getJSON(url,function(data){
		  $.each(data, function(i,item){
			codigo            = item.UNDMED_Codigo;
			descripcion  = item.UNDMED_Descripcion;
			simbolo         = item.UNDMED_Simbolo;
			opt         = document.createElement('option');
			texto       = document.createTextNode(simbolo);
			opt.appendChild(texto);
			opt.value = codigo;
			select.appendChild(opt);
		  });
	});
}
function obtener_precio_producto(){
    var producto = $("#producto").val();
    if(producto=='')
        producto='0';
    var moneda = $("#moneda").val();
    if(moneda=='')
        moneda='0';
    var cliente = $("#cliente").val();
    if(cliente=='')
        cliente='0';
    var unidad_medida = $("#unidad_medida").val();
    if(unidad_medida=='')
        unidad_medida='0';
    var url          = base_url+"index.php/almacen/producto/JSON_precio_producto/"+producto+"/"+moneda+"/"+cliente+"/"+unidad_medida;
    $.getJSON(url,function(data){
              $.each(data, function(i,item){
                    $('#precio').val(item.PRODPREC_Precio);
              });
    });
}