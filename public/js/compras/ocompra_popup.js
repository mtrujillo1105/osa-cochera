var base_url;
var tipo_oper;
var contiene_igv;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    tipo_oper    = $("#tipo_oper").val();
    contiene_igv = $("#contiene_igv").val();
   
    $('#cerrarOcompra').click(function(){
        parent.$.fancybox.close();
    });
	
	$('#limpiarOcompra').click(function(){
        window.location.reload();
    });
	
    $('#agregarOcompra').click(function(){
        mostrar_productos();
    });
    $('#agregarGuias').click(function(){
        agregar_prod_factura();
    });
	
	/*$('.chk_producto[0]:checkbox').change(function(){
	   if(this.checked){
			$("#prodadespachar["+n+"]").attr("disabled", "disabled");
			//$("#importetotal").val("SI");
	   }else{
			//$("#prodadespachar[0]").attr("disabled", "disabled");
			//$("#importetotal").val("NO");
		}	
		alert("I");
    });*/
	
	jQuery.fn.getCheckboxValues = function(){
		var values = [];
		var i = 0;
		this.each(function(){
			// guarda los valores en un array
			values[i++] = $(this).val();
		});
		// devuelve un array con los checkboxes seleccionados
		return values;
	}
	
	$("#cboOrden").change(function(){
		orden 	= $("#cboOrden").val();
		url 	= base_url+"index.php/almacen/guiarem/buscar_guias_x_orden/"+tipo_oper+"/"+orden,
		indice = 0;
		$.getJSON(url,function(data){
			limpiar_tablas_guia();
			limpiar_tablas_detalleGuia();
			$.each(data, function(i,item){
				n 		= document.getElementById('tablaGuias').rows.length;
				id_tr_guia = n;
				n		= n - 2;
				codigo 	= item.codigo;
				serie 	= item.serie;
				numero	= item.numero;
				razon	= item.razon;
				total	= item.total;
				fila 	= '<tr id="guia_'+id_tr_guia+'">';
				fila 	+= '<td><input type="checkbox" name="chkGuias['+n+']" id="chkGuias['+n+']" value="'+codigo+'" /></td>';
				fila 	+= '<td>'+serie+'</td>';
				fila 	+= '<td>'+numero+'</td>';
				fila 	+= '<td>'+razon+'</td>';
				fila 	+= '<td>'+total+'</td>';
				fila 	+= '<td><div align="center"><a href="javascript:;" onclick="ver_detalle_guias('+codigo+')" name="verDetalleGuia" id="verDetalleGuia"><img src="'+base_url+'images/ver.png" title="ver" alt="ver" /></a></div></td>';
				fila 	+= '</tr>';
				$("#tablaGuias").append(fila);
			});
		});
	});
	
});

function limpiar_tablas_guia(){
	n = document.getElementById('tablaGuias').rows.length;
	if(n > 2){
		for(i=2;i<n;i++){
			$("#guia_"+i+"").remove();
		}
	}
}

function limpiar_tablas_detalleGuia(){
	n2 = document.getElementById('tablaDetalleGuias').rows.length;
	if(n2 > 2){
		for(i=2;i<n2;i++){
			$("#dguia_"+i+"").remove();
		}
	}
}

function ver_detalle_guias(guia){
	url 	= base_url+"index.php/almacen/guiarem/obtener_detalle_guiarem/"+guia+"/C",
	indice = 0;
	$.getJSON(url,function(data){
		limpiar_tablas_detalleGuia();
		$.each(data, function(i,item){
			n 		= document.getElementById('tablaDetalleGuias').rows.length;
			id_tr_dguia = n;
			n		= n - 2;
			producto	= item.PROD_Codigo;
			codigo		= item.PROD_CodigoInterno;
			nombre		= item.PROD_Nombre;
			cantidad	= item.GUIAREMDETC_Cantidad;
			pu			= item.GUIAREMDETC_Pu;
			importe		= item.GUIAREMDETC_Pu_ConIgv;
			fila 	= '<tr id="dguia_'+id_tr_dguia+'">';
			fila 	+= '<td>';
			fila 	+= '<input type="hidden" name="producto['+n+']" id="producto['+n+']" value="'+producto+'"/>';
			fila 	+= codigo;
			fila 	+= '</td>';
			fila 	+= '<td>'+nombre+'</td>';
			fila 	+= '<td>'+cantidad+'</td>';
			fila 	+= '<td>'+pu+'</td>';
			fila 	+= '<td>'+importe+'</td>';
			fila 	+= '</tr>';
			$("#tablaDetalleGuias").append(fila);
		});
	});
}

function mostrar_productos(){

    
	if($('#tipo_oper').val()=='V'){	
	var proveedor = $('#cliente').val();
    var ruc_proveedor = $('#ruc_cliente').val();
    var nombre_proveedor = $('#nombre_cliente').val();
	}else{
	var proveedor = $('#proveedor').val();
    var ruc_proveedor = $('#ruc_proveedor').val();
    var nombre_proveedor = $('#nombre_proveedor').val();
	}
	
	var tipo_oper = $('#tipo_oper').val();
    var almacen = $('#almacen').val();
	var moneda = $('#moneda').val();
	var numero = $('#numero').val();
	var codigo_usuario = $('#codigo_usuario').val();
	
    var arr = $("input:checked").getCheckboxValues();
    for(i=0;i<arr.length;i++){
		var indice = arr[i];
		comp = "comprobado["+indice+"]";
		var comprobado = document.getElementById(comp).value;
		if(comprobado == 'SI'){
			a       = "codproducto["+indice+"]"
			b       = "producto["+indice+"]";
			c       = "proddescri["+indice+"]";
			d       = "prodadespachar["+indice+"]";
			e       = "igv["+indice+"]";
			f       = "precio_conigv["+indice+"]";
			g       = "unidad_medida["+indice+"]";
			h       = "nombre_unidad["+indice+"]";
			j       = "codigo_orden["+indice+"]";
			k       = "flagGenInd["+indice+"]";
			codproducto		= document.getElementById(a).value;
			producto        = document.getElementById(b).value;
			nombre_producto = document.getElementById(c).value;
			cantidad        = document.getElementById(d).value;
			igv      		= document.getElementById(e).value;
			precio_conigv   = document.getElementById(f).value;
			unidad_medida   = document.getElementById(g).value; 
			nombre_unidad   = document.getElementById(h).value;
			codigo_orden    = document.getElementById(j).value;
			flagGenInd      = document.getElementById(k).value;
			
			parent.agregar_producto_guiarem2(codproducto,producto,nombre_producto,cantidad,igv,precio_conigv,unidad_medida,nombre_unidad,codigo_orden,flagGenInd,moneda);
			
			
		}else{
			alert("Ingrese la cantida del producto "+(parseFloat(indice) + 1));
		}
	}
      parent.agregar_ocompra_guiarem2(proveedor,ruc_proveedor,nombre_proveedor,almacen,moneda,numero,codigo_usuario);
		  if(arr.length==1 && flagGenInd=='I')
            parent.mostrar_ventana_series();
			
	parent.$.fancybox.close();
}

function agregar_prod_factura(){
    var guias = new Array();
    var arr = $("input:checked").getCheckboxValues();
    for(i=0;i<arr.length;i++){
            guias[i] = arr[i];
    }
    parent.mostrar_productos_factura(guias);
    parent.$.fancybox.close();
}

// function mostrar_productos(){
	// var comprobado = $("#comprobado").val();
	// if(comprobado == "SI"){
		// var arr = $("input:checked").getCheckboxValues();
		// for(i=0;i<arr.length;i++){
			// alert(arr[i]);
			// var indice = arr[i];
			// a       = "codproducto["+indice+"]"
			// b       = "producto["+indice+"]";
			// c       = "proddescri["+indice+"]";
			// d       = "prodadespachar["+indice+"]";//Incluido I.G.V.
			// e       = "igv["+indice+"]";
			// f       = "precio_conigv["+indice+"]";
			// g       = "unidad_medida["+indice+"]";
			// h       = "nombre_unidad["+indice+"]";
			// j       = "codigo_orden["+indice+"]";
			// codproducto		= document.getElementById(a).value;
			// producto        = document.getElementById(b).value;
			// nombre_producto = document.getElementById(c).value;
			// cantidad        = document.getElementById(d).value;
			// igv      		= document.getElementById(e).value;
			// precio_conigv   = document.getElementById(f).value;
			// unidad_medida   = document.getElementById(g).value;
			// nombre_unidad   = document.getElementById(h).value;
			// codigo_orden    = document.getElementById(j).value;
			// parent.agregar_producto_guiarem2(codproducto,producto,nombre_producto,cantidad,igv,precio_conigv,unidad_medida,nombre_unidad,codigo_orden);
		// }
	// }else{
		// alert("Ingrese bien las cantidades de cada producto");
	// }
// }


function calcula_totales(){    
    n          = document.getElementById('tblDetalleOcompra').rows.length;
    percepcion = document.getElementById('percepcion').value;
    importe_total = 0;
    igv_total     = 0;
    descuento_total  = 0;
    descuento_total2 = 0;
    precio_total = 0;
    for(i=0;i<n;i++){//Estanb al reves los campos
        a       = "prodimporte["+i+"]"
        b       = "prodigv["+i+"]";
        c       = "proddescuento["+i+"]";
        d       = "prodprecio["+i+"]";//Incluido I.G.V.
        e       = "proddescuento2["+i+"]";
        f       = "detaccion["+i+"]";
        if(document.getElementById(f).value!='e'){
            importe         = parseFloat(document.getElementById(a).value);
            igv             = parseFloat(document.getElementById(b).value);
            descuento       = parseFloat(document.getElementById(c).value);
            precio          = parseFloat(document.getElementById(d).value);
            descuento2      = parseFloat(document.getElementById(e).value);
            importe_total   = importe + importe_total;
            igv_total       = igv   + igv_total;
            descuento_total = descuento + descuento_total;
            descuento_total2 = descuento2 + descuento_total2;
            precio_total    = precio + precio_total;
        }
    }
    descuento_totaltotal = descuento_total+descuento_total2;
    percepcion_total = importe_total*percepcion/100;
    importe_total    = importe_total + percepcion_total;
    precio_total = precio_total.toFixed(2);
    igv_total    = igv_total.toFixed(2);
    descuento_total = descuento_total.toFixed(2);
    descuento_total2 = descuento_total2.toFixed(2);
    percepcion_total = percepcion_total.toFixed(2);
    descuento_totaltotal = descuento_totaltotal.toFixed(2);
    importe_total = importe_total.toFixed(2);    
    $("#preciototal").val(precio_total);
    $("#igvtotal").val(igv_total);
    $("#descuentotal").val(descuento_totaltotal);
    $("#percepciontotal").val(percepcion_total);
    $("#importetotal").val(importe_total);
}

function calcula_resantes(n){
	a  = "prodcantidad["+n+"]";
	b  = "prodadespachar["+n+"]";
	c  = "proddespachados["+n+"]";
	d  = "prodrestantes["+n+"]";
    e  = "chk_producto["+n+"]";
	var comprobado = "NO";
	var cantidad = document.getElementById(a).value;
	var despachar = document.getElementById(b).value;
	var despachados = document.getElementById(c).value;
	var restantes = document.getElementById(d).value;
	cantidad = parseInt(cantidad);
        despachar = parseInt(despachar);
        despachados = parseInt(despachados);
	if(despachar > 0){
		if(despachados == 0){
			if(despachar <= cantidad){
				document.getElementById(d).value = cantidad - despachar;
				comprobado = "SI";
                                document.getElementById(e).checked=true;
			}else{
				alert("Ingrese un numero menor o igual que la cantidad");
				comprobado = "NO";
                                document.getElementById(b).value='';
                                document.getElementById(d).value=cantidad-despachados;
                                document.getElementById(e).checked=false;
			}
		}else if(despachados > 0){
			if(despachar <= restantes){
				document.getElementById(d).value = cantidad - despachar - despachados;
				comprobado = "SI";
                                document.getElementById(e).checked=true;
			}else{
				alert("Ingrese un numero menor o igual que la cantidad restante");
				comprobado = "NO";
                                document.getElementById(b).value='';
                                document.getElementById(d).value=cantidad-despachados;
                                document.getElementById(e).checked=false;
			}
		}
	}else{
		alert("Ingrese un numero mayor a cero");
		comprobado = "NO";
                document.getElementById(b).value='';
                document.getElementById(d).value=cantidad-despachados;
                document.getElementById(e).checked=false;
	}
	document.getElementById("comprobado["+n+"]").value = comprobado;
}

/*function calcula_resantes(n){
    a  = "chk_producto["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "prodadespachar["+n+"]";
    cantidad  = parseInt(document.getElementById(b).value);
    despachar =  parseInt(document.getElementById(c).value);
    if(cantidad>despachar)
        document.getElementById(a).checked=true;
    else
        if(cantidad<despachar)
            alert('La cantidad maxima a despachar es '+cantidad);
}*/