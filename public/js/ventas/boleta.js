jQuery(document).ready(function(){
	base_url   = $("#base_url").val();
	$("#nuevoOcompa").click(function(){
		url = base_url+"index.php/compras/nueva_ocompra";
		location.href = url;
	});	
 	$("#grabarOcompra").click(function(){
		$("#frmOrdenCompra").submit();
	}); 
	$("#limpiarOcompra").click(function(){
		$("#frmOrdenCompra").each(function(){
			this.reset();
		});
	});
	$("#cancelarOcompra").click(function(){
		url = base_url+"index.php/compras/ocompras";
		location.href = url;		
	});
	$("#cancelarOcompra2").click(function(){
		url = base_url+"index.php/compras/ocompras";
		location.href = url;		
	});	
	$("#buscarOcompra").click(function(){
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
})
function obtener_proveedor(){
	ruc        = $("#ruc").val();
	url        = base_url+"index.php/comercial/obtener_nombre_proveedor/"+ruc;
	$.getJSON(url,function(data){
		$.each(data,function(i,item){
			ruc       = item.EMPRC_Ruc;
			proveedor = item.PROVP_Codigo;
			nombre    = item.EMPRC_RazonSocial;
			$("#nombre_proveedor").val(nombre);
			$("#proveedor").val(proveedor);
			if(nombre==''){
				alert('No existe el proveedor.');
				$("#ruc").val("");
			}
		});
	});
}
function obtener_producto(){
	codproducto   = $("#codproducto").val();
	url        = base_url+"index.php/producto/obtener_nombre_producto/"+codproducto;
    if(codproducto!=''){
         $.getJSON(url,function(data){
             $.each(data,function(i,item){
                 producto        = item.PROD_Codigo;
                 nombre_producto = item.PROD_Nombre;
                 stock                        = item.PROD_Stock;
                 nombre_familia  = item.FAMI_Descripcion;
                 if(nombre_producto==''){
                     alert('Este codigo no corresponde a ningun producto.');
                     $("#producto").val("");
                     $("#codproducto").val("");
                     $("#nombre_producto").val("");
                 }
                 else{
                     $("#producto").val(producto);
                     $("#stock").val(stock);
                     $("#nombre_familia").val(nombre_familia);
                     $("#nombre_producto").val(nombre_producto);
                     listar_unidad_medida_producto(producto);
                 }
             });
         });
    }
}
function agregar_producto_cotizacion(){
	codproducto            = $("#codproducto").val();
    producto                   =  $("#producto").val();
	nombre_producto = $("#nombre_producto").val(); 
	descuento                = $("#descuento").val();
	igv                                = $("#igv").val();
	cantidad                    = $("#cantidad").val();
    unidad_medida     = $("#unidad_medida").val();
    select_umedida    = document.getElementById("unidad_medida");
    options_umedida = select_umedida.getElementsByTagName("option");
    nombre_unidad_medida = $("#nombre_unidad_medida").val();
	n = document.getElementById('tblDetalleCotizacion').rows.length;
	j = n+1;	
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
	if(nombre_producto!='' && cantidad!='0' && unidad_medida!='0')
	{
		fila  = '<tr class="'+clase+'">';
       fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_cotizacion('+n+');">';
       fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
       fila+='</a></strong></font></div></td>';
		fila +=	'<td width="6%">';
        fila+='<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">';
        fila+='<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
        fila+='<div align="center">'+j+'</div>';
        fila+='</td>';
        fila += '<td width="10%"><div align="left">'+codproducto+'</div></td>';
		fila +=	'<td width="54%"><div align="left">'+nombre_producto+'</div></td>';
		fila += '<td width="14%"><div align="center">'+nombre_unidad_medida+'</div></td>';
		fila += '<td width="13%"><div align="center">';
         fila+='<input type="hidden" class="cajaMinima" name="detcotiz['+n+']" id="detcotiz['+n+']">';
        fila+='<input type="text" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';        
        fila+='<input type="text" class="cajaPequena2" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onkeypress="return numbersonly(this,event)">';
        fila+='</div></td>';
		fila += '</tr>';
		$("#tblDetalleCotizacion").append(fila);
		//Inicializo valores de carga
		$("#producto").val('');
        $("#codproducto").val('');
		$("#nombre_producto").val(''); 
		$("#cantidad").val('0');
        $("#unidad_medida").val('0');
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
          tabla       = document.getElementById('tblDetalleCotizacion');
          a                = "detcotiz["+n+"]";
          b                = "detaccion["+n+"]";
          fila            = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.backgroundColor = "#FF8000";
          document.getElementById(b).value = "e";
     }
}
function agregar_producto_ocompra(){
     codproducto  = $("#codproducto").val();
	producto          = $("#producto").val();
	nombre_producto = $("#nombre_producto").val();
	descuento       = $("#descuento").val();
	igv                       = $("#igv").val();
	cantidad           = $("#cantidad").val();
    unidad_medida     = $("#unidad_medida").val();//select
    select_umedida    = document.getElementById("unidad_medida");
    options_umedida = select_umedida.getElementsByTagName("option");
	nombre_unidad    = $("#nombre_unidad_medida").val();
	n = document.getElementById('tblDetalleOcompra').rows.length;
	j = n+1;
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
	if(nombre_producto!='' && cantidad!='0')
	{
		fila  = '<tr class="'+clase+'">';
       fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_ocompra('+n+');">';
       fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
       fila+='</a></strong></font></div></td>';
		fila +=	'<td width="5%"><div align="center">1</div></td>';
		fila += '<td width="10%"><div align="center">';
        fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
        fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
        fila+= '</div></td>';
		fila +=	'<td width="32%"><div align="left">'+nombre_producto+'</div></td>';
		fila += '<td width="8%"><div align="center">';
        fila+= '<input type="text" class="cajaPequena2" name="prodpu['+n+']" id="prodpu['+n+']" value="" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
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
         fila+='<input type="hidden" class="cajaMinima" name="detocom['+n+']" id="detocom['+n+']">';
        fila+='<input type="text" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';                
        fila+= '<input type="text" class="cajaPequena2" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
        fila+= '</div></td>';
		fila += '</tr>';
		$("#tblDetalleOcompra").append(fila);
		//Inicializo valores
		$("#producto").val('');
        $("#codproducto").val('');
		$("#nombre_producto").val('');
		$("#cantidad").val('0');
		$("#nombre_unidad").val('');
        $("#unidad_medida").val('0');
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
function eliminar_producto_ocompra(n){
     if(confirm('Esta seguro que desea eliminar este producto?')){
          tabla       = document.getElementById('tblDetalleOcompra');
          a                = "detocom["+n+"]";
          b                = "detaccion["+n+"]";
          fila            = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.backgroundColor = "#FF8000";
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
	pu        = document.getElementById(a).value;
	cantidad  = document.getElementById(b).value;
	descuento = document.getElementById(c).value;
    igv100  = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
	precio    = pu*cantidad;
	total_dscto = precio*descuento100/100;
	precio2   = precio-total_dscto;
	total_igv   = precio2*igv100/100;
	importe     = precio-total_dscto+total_igv;
    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
	document.getElementById(e).value = precio;
	document.getElementById(f).value = importe;
	//calcula_totales();
}
function calcula_totales(){
	n     = document.getElementById('tblDetalleOcompra').rows.length;
	importe_total = 0;
	igv_total     = 0;
    descuento_total     = 0;
    precio_total = 0;
	for(i=0;i<n;i++){//Estanb al reves los campos
		a       = "prodimporte["+i+"]"
		b       = "prodigv["+i+"]";
        c        = "proddescuento["+i+"]";
        d       = "prodprecio["+i+"]";
		importe                  = parseInt(document.getElementById(a).value);
		igv                             = parseInt(document.getElementById(b).value);
        descuento             = parseInt(document.getElementById(c).value);
        precio                      = parseInt(document.getElementById(d).value);
		importe_total      = importe + importe_total;
		igv_total                 = igv   + igv_total;
        descuento_total = descuento + descuento_total;
        precio_total          = precio + precio_total;
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
function obtener_detalle_cotizacion(){//En realidad es detalle de la cotizacion
     cotizacion  =  $("#cotizacion").val();
     descuento =  $("#descuento").val();
     igv                 = $("#igv").val();
     url = base_url+"index.php/compras/obtener_detalle_cotizacion/"+cotizacion;//Es detalle de la cotizacion
     dataString = "cotizacion="+cotizacion;
     n = 0;
     $.getJSON(url,function(data){
          fila= '<table width="100%" height="250px;" border="0" cellpadding="0" cellspacing="0">';
          fila+= '<tr>';
          fila+= '<td valign="top">';
          fila = '<table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">';
          $.each(data,function(i,item){
               pedido                        = item.PEDIP_Codigo;
               n=i;
               j=i+1
               producto                    = item.PROD_Codigo;
               codproducto            = item.PROD_CodigoInterno;
               unidad_medida      = item.UNDMED_Codigo;
               nombre_unidad     =item.UNDMED_Simbolo;
               nombre_producto = item.PROD_Nombre;
               cantidad                     = item.COTDEC_Cantidad;
               proveedor                 = item.PROVP_Codigo;
               ruc                                = item.Ruc;
               razon_social             = item.RazonSocial;
               if((i+1)%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
                fila+= '<tr class="'+clase+'">';
               fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_ocompra('+n+');">';
               fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
               fila+='</a></strong></font></div></td>';
               fila +=	'<td width="5%"><div align="center">'+j+'</div></td>';
               fila += '<td width="10%"><div align="center">';
               fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
               fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
               fila+= '</div></td>';
               fila +=	'<td width="32%"><div align="left">'+nombre_producto+'</div></td>';
               fila += '<td width="8%"><div align="center">';
               fila+= '<input type="text" class="cajaPequena2" name="prodpu['+n+']" id="prodpu['+n+']" value="" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
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
               fila+='<input type="hidden" class="cajaMinima" name="detocom['+n+']" id="detocom['+n+']">';
               fila+='<input type="text" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
               fila+= '<input type="text" class="cajaPequena2" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
               fila+= '</div></td>';
               fila += '</tr>';
          })
          fila+='</table>'
           fila+= '</td>';
           fila+= '</tr>';
           fila+= '</table>';
           $('#pedido').val(pedido);
           $('#ruc').val(ruc);
           $('#proveedor').val(proveedor);
           $('#nombre_proveedor').val(razon_social);
           if(n>=0){
               $("#lineaResultado").html(fila);
           }
           else{
                $("#lineaResultado").html('');
                alert('La cotizacion no tiene elementos.');
           }
     });
}
