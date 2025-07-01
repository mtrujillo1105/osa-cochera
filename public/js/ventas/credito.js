jQuery(document).ready(function(){
        base_url     = $("#base_url").val();
        tipo_oper    = $("#tipo_oper").val();
        tipo_docu    = $("#tipo_docu").val();
        contiene_igv = $("#contiene_igv").val();
               
	$("#nuevaCredito").click(function(){
		url = base_url+"index.php/ventas/credito/credito_nuevo"+"/"+tipo_oper+"/"+tipo_docu;
		location.href = url;
	});
  $("#cancelarCreditoS").click(function(){
		$.fancybox.close();
                url = base_url+"index.php/ventas/credito/listar"+"/"+tipo_oper+"/"+tipo_docu;
		location.href = url;		
	});
	


 	$("#grabarCredito").click(function(){
	    $('img#loading').css('visibility','visible');
            var codigo=$('#codigo').val();
            if(codigo=='')
                url = base_url+"index.php/ventas/credito/credito_insertar";
            else
                url = base_url+"index.php/ventas/credito/credito_modificar";
            
            dataString  = $('#frmCredito').serialize();
            
            $.post(url,dataString,function(data){
          
                    $('img#loading').css('visibility','hidden');
                    switch(data.result){
                        case 'ok': 
                                if(codigo==''){
                                    $('#codigo').val(data.codigo);
                                    $('#ventana').show();
                                    $('#linkVerImpresion').click();
                                }
                                else
                                    location.href = base_url+"index.php/ventas/credito/listar"+"/"+tipo_oper+"/"+tipo_docu;
                                break;
                        case 'error': 
                                $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                break;
                    }
            },'json');
	}); 
	$("#limpiarCredito").click(function(){
		url = base_url+"index.php/ventas/credito/listar"+"/"+tipo_oper+"/"+tipo_docu+"/0/1";
		location.href = url;
	});
        $("#cancelarCredito, #cancelarImprimirCredito").click(function(){
		$.fancybox.close();
                url = base_url+"index.php/ventas/credito/listar"+"/"+tipo_oper+"/"+tipo_docu;
		location.href = url;		
	});
	
	$("#repo1").click(function(){
		$("#divRepo1").show();
		$("#divRepo2").hide();
		$("#divRepo3").hide();
		$("#divRepo4").hide();
		$("#divRepo5").hide();
		$("#divRepo6").hide();
	});
  
	$("#repo6").click(function(){
		$("#divRepo1").hide();
		$("#divRepo2").hide();
		$("#divRepo3").hide();
		$("#divRepo4").hide();
		$("#divRepo5").hide();
		$("#divRepo6").show();
	});
	
	$("#repo2").click(function(){
		$("#divRepo1").hide();
		$("#divRepo3").hide();
		$("#divRepo4").hide();
		$("#divRepo5").hide();
 		$("#divRepo6").hide();
		url = base_url+"index.php/ventas/credito/estadisticas";
		$.post(url,'',function(data){
			$('#divRepo2').html(data).show();
		});
	});
	
	$("#repo3").click(function(){
		$("#divRepo1").hide();
		$("#divRepo2").hide();
		$("#divRepo4").hide();
		$("#divRepo5").hide();
		$("#divRepo3").show();
 		$("#divRepo6").hide();
	});
	
	$("#repo4").click(function(){
		$("#divRepo1").hide();
		$("#divRepo2").hide();
		$("#divRepo3").hide();
		$("#divRepo5").hide();
		$("#divRepo4").show();
 		$("#divRepo6").hide();
	});
	
	$("#repo5").click(function(){
		$("#divRepo1").hide();
		$("#divRepo2").hide();
		$("#divRepo3").hide();
		$("#divRepo4").hide();
		$("#divRepo5").show();
 		$("#divRepo6").hide();
	});
	
  $("#imprimirCredito").click(function(){		
        if($('#codigo').val()==''){
              alert('Ha ocurrido un error, no se puede realizar la impresión')
              return false;
        }
        ver_comprobante_pdf($('#codigo').val());
       
       $("#cancelarCredito").click();
          return true;
	});
  
	$("#buscarCredito").click(function(){
		$("#form_busqueda").submit();
	});
        $("#presupuesto").change(function(){
                if(this.value!='')
                   $("#ordencompra").val('');
	});
        $("#ordencompra").change(function(){
                if(this.value!='')
                    $("#presupuesto").val('');
	});
        $("#linkVerSerieNum").click(function () {
            var temp=$("#linkVerSerieNum p").html();
            var serienum=temp.split('-');
            $("#serie").val(serienum[0]);
            $("#numero").val(serienum[1]);
        });
        
        $('#buscar_producto').keyup(function(e){
           var key=e.keyCode || e.which;
            if (key==13){
                if($(this).val()!=''){
                    $('#linkSelecProducto').attr('href', base_url+'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val()).click();
                }
            } 
        });
        
        $('#cantidad').bind('keypress', function(e) {
            tipo_oper = $("#tipo_oper").val();
            flagGenInd      = $("#flagGenInd").val();
            if(tipo_oper=='V' && flagGenInd=='I'){
                if(e.keyCode==9 || e.keyCode==13){
                    if(tipo_oper == 'V'){
                        ventana_producto_serie2_2();
                    }else if(tipo_oper == 'C'){
                        ventana_producto_serie_1();
                    }
                }
            }
        });
})

function ver_reporte_pdf(){
    var fechai=$('#fechai').val();
    var fechaf=$('#fechaf').val();
    var cliente=$('#cliente').val();
    var producto=$('#producto').val();
    var aprobado=$('#aprobado').val();
    var ingreso=$('#ingreso').val();
    
    url = base_url+"index.php/ventas/comprobante/ver_reporte_pdf/"+fechai+'_'+fechaf+'_'+cliente+'_'+producto+'_'+aprobado+'_'+ingreso;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function ver_reporte_pdf_ventas(){
	var anio = $("#anioVenta").val();
    url = base_url+"index.php/ventas/comprobante/ver_reporte_pdf_ventas/"+anio;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function estadisticas_compras_ventas(tipo){
	var anio = $("#anioVenta2").val();
    url = base_url+"index.php/ventas/comprobante/estadisticas_compras_ventas/"+tipo+"/"+anio;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function estadisticas_compras_ventas_mensual(tipo){
	var anio = $("#anioVenta3").val();
	var mes = $("#mesVenta3").val();
    url = base_url+"index.php/ventas/comprobante/estadisticas_compras_ventas_mensual/"+tipo+"/"+anio+"/"+mes+"";
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function editar_comprobante(comprobante){
	//alert(base_url)
        location.href = base_url+"index.php/ventas/credito/credito_editar/"+comprobante+"/"+tipo_oper+"/"+tipo_docu;
}
function eliminar_comprobante(comprobante){
    if(confirm('Esta seguro que desea eliminar este comprobante?')){
        dataString = "comprobante="+comprobante;
        url = base_url+"index.php/ventas/comprobante/comprobante_eliminar";
        $.post(url,dataString,function(data){
                location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;
        });
    }
}
function ver_comprobante_pdf(comprobante){
    var url = base_url+"index.php/ventas/comprobante/comprobante_ver_html/"+comprobante+"/"+tipo_docu;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function comprobante_ver_pdf_conmenbrete(comprobante){
    var url = base_url+"index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete/"+comprobante+"/"+tipo_docu;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_comprobante(){
    location.href = base_url+"index.php/ventas/credito/listar";
}
function agregar_producto_comprobante(){
        flagBS  = $("#flagBS").val();
        
        if($("#codproducto").val()==''){
            $("#codproducto").focus();
            alert('Ingrese el producto.');
            return false;
        }
        if($("#cantidad").val()==''){
            $("#cantidad").focus();
            alert('Ingrese una cantidad.');
            return false;
        }
        if(flagBS=='B' && $("#unidad_medida")=='0'){
            $("#unidad_medida").focus();
            alert('Seleccine una unidad de medida.');
            return false;
        }
        
        codproducto  = $("#codproducto").val();
	producto = $("#producto").val();
	nombre_producto = $("#nombre_producto").val();
	descuento = $("#descuento").val();
	igv = parseInt($("#igv").val());
	cantidad = $("#cantidad").val();
        if( $("#precio").val()!='')
            precio_conigv = $("#precio").val();
        else
            precio_conigv=0;
        if(tipo_docu!='B' && contiene_igv=='1')
            precio=money_format(precio_conigv*100/(igv+100))
        else{
            precio=precio_conigv;
            precio_conigv = money_format(precio_conigv*(100+igv)/100);
        }
        costo             = parseFloat($("#costo").val());
        unidad_medida     ='';
        nombre_unidad     ='';
        if(flagBS=='B'){
            unidad_medida = $("#unidad_medida").val();
            nombre_unidad = $('#unidad_medida option:selected').html()
        }
        flagGenInd      = $("#flagGenInd").val();
	n = document.getElementById('tblDetalleComprobante').rows.length;
	j = n+1;
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
        
        
            
        fila  = '<tr class="'+clase+'">';
        fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante('+n+');">';
        fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
        fila+='</a></strong></font></div></td>';
        fila +='<td width="4%"><div align="center">'+j+'</div></td>';
        fila += '<td width="10%"><div align="center">'+codproducto+'</div></td>';
        fila +=	'<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'" /></div></td>';
        fila += '<td width="10%"><div align="left">';
        if(tipo_docu!='B')
            fila+= '<input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad;
        else
            fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad;
        if(tipo_oper=='V' && flagGenInd=="I"){
            if(tipo_oper=='V')
               fila+= ' <a href="javascript:;" onclick="ventana_producto_serie2('+n+')"><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle" /></a>';
            else
               fila+= ' <a href="javascript:;" onclick="ventana_producto_serie('+n+')"><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle"/></a>';
        }
        fila+= '</div></td>';
        if(tipo_docu!='D'){
            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio_conigv+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" onblur="modifica_pu_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" value="0" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';                  
            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly">';
        }
        else{
            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="0" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';       
            fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv['+n+']" id="prodprecio_conigv['+n+']" value="0" readonly="readonly"></div></td>';
        }
        if(tipo_docu!='D')
            fila += '<td width="6%"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly="readonly"></div></td>';
        fila += '<td width="6%"><div align="center">';
        fila+='<input type="hidden" value="n" name="detaccion['+n+']" id="detaccion['+n+']">';
        fila+= '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
        fila+='<input type="hidden" value="" name="detacodi['+n+']" id="detacodi['+n+']">';
        fila+= '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento+'">';
         if(tipo_docu!='D')
            fila+= '<input type="hidden" name="proddescuento['+n+']" id="proddescuento['+n+']" onblur="calcula_importe2('+n+');" />';
        else
            fila+= '<input type="hidden" name="proddescuento_conigv['+n+']" id="proddescuento_conigv['+n+']" onblur="calcula_importe2_conigv('+n+');" />';
        fila+= '<input type="hidden" name="flagBS['+n+']" id="flagBS['+n+']" value="'+flagBS+'">';
        fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">';
        fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
        fila+= '<input type="hidden" class="cajaMinima" name="flagGenIndDet['+n+']" id="flagGenIndDet['+n+']" value="'+flagGenInd+'">';
        fila+= '<input type="hidden" class="cajaPequena2" name="prodcosto['+n+']" id="prodcosto['+n+']" value="'+costo+'" readonly="readonly">';
        fila+= '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
        fila+= '</div></td>';
        fila += '</tr>';
        $("#tblDetalleComprobante").append(fila);

        if(tipo_docu!='D')
            calcula_importe(n); //Para facturas o comprobantes
        else
            calcula_importe_conigv(n); //Para boletas

        inicializar_cabecera_item(); 
        
        return true;
}

function eliminar_producto_comprobante(n){
     if(confirm('Esta seguro que desea eliminar este producto?')){
          a                = "detacodi["+n+"]";
          b                = "detaccion["+n+"]";
          fila            = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.display="none";
          document.getElementById(b).value = "e";
          if(tipo_docu!='D')
              calcula_totales();
          else
              calcula_totales_conigv();
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
    i = "prodpu_conigv["+n+"]";
    pu = document.getElementById(a).value;
    pu_conigv = document.getElementById(i).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio = money_format(pu*cantidad);
    total_dscto = money_format(precio*descuento100/100);
    precio2 = money_format(precio-parseFloat(total_dscto));
    if(pu_conigv=='')
        total_igv = money_format(precio2*igv100/100);
    else
        total_igv = money_format((pu_conigv-pu)*cantidad);
    
    importe = money_format(precio-parseFloat(total_dscto)+parseFloat(total_igv));

    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
    document.getElementById(e).value = precio;
    document.getElementById(f).value = importe;
    
    calcula_totales();
}
function calcula_importe_conigv(n){
    a  = "prodpu_conigv["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "proddescuento_conigv["+n+"]";
    e  = "prodprecio_conigv["+n+"]";
    f  = "prodimporte["+n+"]";
    g = "prodigv100["+n+"]";
    h = "proddescuento100["+n+"]";
    pu_conigv = document.getElementById(a).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio_conigv = money_format(pu_conigv*cantidad);
    total_dscto_conigv = money_format(precio_conigv*descuento100/100);
    precio2 = money_format(precio_conigv-parseFloat(total_dscto_conigv));
    
    importe = money_format(precio_conigv-parseFloat(total_dscto_conigv));
    document.getElementById(c).value = total_dscto_conigv;
    document.getElementById(e).value = precio_conigv;
    document.getElementById(f).value = importe;
    
    calcula_totales_conigv();
}
function calcula_importe2(n){
    a  = "prodpu["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "proddescuento["+n+"]";
    e  = "prodigv["+n+"]";
    f  = "prodprecio["+n+"]";
    g  = "prodimporte["+n+"]";
    pu           = parseFloat(document.getElementById(a).value);
    cantidad     = parseFloat(document.getElementById(b).value);
    descuento    = parseFloat(document.getElementById(c).value);
    total_igv    = parseFloat(document.getElementById(e).value);
    importe      = money_format((pu*cantidad)-descuento+total_igv);
    document.getElementById(g).value = importe;
    
    calcula_totales();
}
function calcula_importe2_conigv(n){
    a  = "prodpu_conigv["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "proddescuento_conigv["+n+"]";
    f  = "prodprecio_conigv["+n+"]";
    g  = "prodimporte["+n+"]";
    pu_conigv        = parseFloat(document.getElementById(a).value);
    cantidad         = parseFloat(document.getElementById(b).value);
    descuento_conigv = parseFloat(document.getElementById(c).value);
    importe          = money_format((pu_conigv*cantidad)-descuento_conigv);
    document.getElementById(g).value = importe;
    
    calcula_totales_conigv();
}
function calcula_totales(){
    n = document.getElementById('tblDetalleComprobante').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]"
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        e  = "detaccion["+i+"]";
        if(document.getElementById(e).value!='e'){
            importe = parseFloat(document.getElementById(a).value);
            igv = parseFloat(document.getElementById(b).value);
            descuento = parseFloat(document.getElementById(c).value);
            precio = parseFloat(document.getElementById(d).value);
            importe_total = money_format(importe + importe_total);
            igv_total = money_format(igv + igv_total);
            descuento_total = money_format(descuento + descuento_total);
            precio_total = money_format(precio + precio_total);
        }
    }
    $("#importetotal").val(importe_total.toFixed(2));
    $("#igvtotal").val(igv_total.toFixed(2));
    $("#descuentotal").val(descuento_total.toFixed(2));
     $("#preciototal").val(precio_total.toFixed(2));
}
function calcula_totales_conigv(){
    n = document.getElementById('tblDetalleComprobante').rows.length;
    importe_total = 0;
    descuento_total_conigv = 0;
    precio_total_conigv = 0;
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]"
        c = "proddescuento_conigv["+i+"]";
        d = "prodprecio_conigv["+i+"]";
        e  = "detaccion["+i+"]";
        if(document.getElementById(e).value!='e'){
            importe = parseFloat(document.getElementById(a).value);
            descuento_conigv = parseFloat(document.getElementById(c).value);
            precio_conigv = parseFloat(document.getElementById(d).value);
            importe_total = money_format(importe + importe_total);
            descuento_total_conigv = money_format(descuento_conigv + descuento_total_conigv);
            precio_total_conigv = money_format(precio_conigv + precio_total_conigv);
        }
    }
    $("#importetotal").val(importe_total.toFixed(2));
    $("#descuentotal_conigv").val(descuento_total_conigv.toFixed(2));
    $("#preciototal_conigv").val(precio_total_conigv.toFixed(2));
}
function mostrar_productos_factura(guias){
	for(i=0;i<guias.length;i++){
		var codigo_guia = guias[i];
		url 	= base_url+"index.php/almacen/guiarem/obtener_detalle_guiarem/"+codigo_guia+"/C",
		$.getJSON(url,function(data){
			$.each(data, function(i,item){
				n 		= document.getElementById('tblDetalleComprobante').rows.length;
				id_tr_dguia = n;
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
				$("#tblDetalleComprobante").append(fila);
			});
		});
	}
}
// para agregar productos cuando ingreso por el seguimiento de orden
function mostrar_productos_factura(guias){
	for(i=0;i<guias.length;i++){
		var codigo_guia = guias[i];
		url 	= base_url+"index.php/almacen/guiarem/obtener_detalle_guiarem/"+codigo_guia+"/C",
		$.getJSON(url,function(data){
			$.each(data, function(i,item){
				var igv = 18;
				flagBS  = $("#flagBS").val();
				precio_conigv = parseFloat(item.GUIAREMDETC_Pu_ConIgv);
				precio = parseFloat(item.GUIAREMDETC_Subtotal);
				codproducto = item.PROD_Codigo;
				producto = item.PROD_CodigoInterno;
				unidad_medida = item.UNDMED_Codigo;
				nombre_unidad = item.UNDMED_Simbolo;
				nombre_producto = item.PROD_Nombre;
				cantidad = item.GUIAREMDETC_Cantidad;
				stock           = '0'
				costo           = '0';
				n 		= document.getElementById('tblDetalleComprobante').rows.length;
				j = n+1;
				if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
				fila = '<tr class="'+clase+'">';
				fila+= '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra('+n+');">';
				fila+= '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
				fila+= '</a></strong></font></div></td>';
				fila+= '<td width="4%"><div align="center">'+j+'</div></td>';
				fila+= '<td width="10%"><div align="center">';
				fila+= '<input type="hidden" name="flagBS['+n+']" id="flagBS['+n+']" value="'+flagBS+'">';
				fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+codproducto+'">'+producto;
				fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
				fila+= '</div></td>';
				fila+= '<td><div align="left">';
				fila+= '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'">';
				fila+= '</div></td>';
				fila+= '<td width="10%"><div align="left">';
				fila+= '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
				fila+= '</div></td>';
				fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'
				fila += '<input type="hidden"  value="'+precio_conigv+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']"></div></td>';
				fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
				fila += '<td width="6%"><div align="center">';           
				fila+= '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="0">';
				fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento['+n+']" id="proddescuento['+n+']" onblur="calcula_importe2('+n+');" />';
				fila+= '</div></td>';
				fila+= '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly></div></td>';
				fila+= '<td width="6%"><div align="center">';
				fila+= '<input type="hidden" class="cajaMinima" name="detacodi['+n+']" id="detacodi['+n+']">';
				fila+= '<input type="hidden" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
				fila+= '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
				fila+= '<input type="hidden" class="cajaPequena2" name="prodcosto['+n+']" id="prodcosto['+n+']" value="'+costo+'" readonly="readonly">';
				fila+= '<input type="hidden" class="cajaPequena2" name="prodventa['+n+']" id="prodventa['+n+']" value="0" readonly="readonly">';
				fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
				fila+= '</div></td>';
				fila+= '</tr>';
				$("#tblDetalleComprobante").append(fila);
				calcula_importe(n);
			});
		});
	}
    return true;
}
function modifica_pu_conigv(n){
    a  ="prodpu_conigv["+n+"]";
    g = "prodigv100["+n+"]";
    i = "prodpu["+n+"]";
    pu_conigv = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value);
    
    pu = money_format(100*pu_conigv/(100+igv100));
    document.getElementById(i).value=pu;
    
    calcula_importe(n);
}
function modifica_pu(n){
    a  ="prodpu["+n+"]";
    g = "prodigv100["+n+"]";
    i = "prodpu_conigv["+n+"]";
    pu = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value); 
    
    pu_conigv = money_format(pu*(100+igv100)/100);
    document.getElementById(i).value=pu_conigv;
    
    calcula_importe(n);
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
    base_url   = $("#base_url").val();
    flagBS   = $("#flagBS").val();
    url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
    select_umedida   = document.getElementById('unidad_medida');
    options_umedida = select_umedida.getElementsByTagName("option"); 

    var num_option=options_umedida.length;
    for(i=1;i<=num_option;i++){
        select_umedida.remove(0)
     }
    opt = document.createElement("option");
    texto = document.createTextNode(":: Seleccione ::");
    opt.appendChild(texto);
    opt.value = "0";
    select_umedida.appendChild(opt);
    $("#cantidad").val('');  
    $("#precio").val('');
        
    $.getJSON(url,function(data){
          $.each(data, function(i,item){
                codigo            = item.UNDMED_Codigo;
                descripcion  = item.UNDMED_Descripcion;
                simbolo         = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                nombrecorto_producto = item.PROD_NombreCorto;
                marca           = item.MARCC_Descripcion;
                modelo          = item.PROD_Modelo;
                presentacion    = item.PROD_Presentacion;
                opt         = document.createElement('option');
                texto       = document.createTextNode(simbolo);
                opt.appendChild(texto);
                opt.value = codigo;
                if(i==0)
                    opt.selected=true;
                select_umedida.appendChild(opt);
          });
          var nombre;
          if(nombrecorto_producto)
              nombre=nombrecorto_producto;
          else
              nombre=nombre_producto;
          if(flagBS=='B'){
              if(marca)
                nombre+=' / Marca:'+marca;
              if(modelo)
                nombre+=' / Modelo: '+modelo;
              if(presentacion)
                nombre+=' / Prest: '+presentacion;
          }
          $("#nombre_producto").val(nombre);
          listar_precios_x_producto_unidad();
    });
}

function listar_precios_x_producto_unidad(){
    unidad = $("#unidad_medida").val();
    moneda = $("#moneda").val();
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url          = base_url+"index.php/almacen/producto/listar_precios_x_producto_unidad/1/"+unidad+"/"+moneda;
    select_precio   = document.getElementById('precioProducto');
    options_umedida = select_precio.getElementsByTagName("option"); 

    var num_option=options_umedida.length;
    for(i=1;i<=num_option;i++){
        select_precio.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode("::Seleccion::");
    opt.appendChild(texto);
    opt.value = "";
    select_precio.appendChild(opt);
    $.getJSON(url,function(data){
		$.each(data, function(i,item){
			codigo		= item.codigo;
			moneda		= item.moneda;
			precio		= item.precio;
			opt         = document.createElement('option');
			texto       = document.createTextNode(moneda+" "+precio);
			opt.appendChild(texto);
			opt.value = precio;
			select_precio.appendChild(opt);
		});
	});
}

function mostrar_precio(){
        precio = $("#precioProducto").val();
	$("#precio").val(precio);
}

function obtener_precio_producto(){
    var producto = $("#producto").val();
    $('#precio').val("");
    if(producto=='' || producto=='0')
        return false;
    var moneda = $("#moneda").val();
    if(moneda=='' || moneda=='0')
        return false;
    var unidad_medida = $("#unidad_medida").val();
    if(unidad_medida=='' || unidad_medida=='0')
        return false;
    var cliente = $("#cliente").val();
    if(cliente=='')
        cliente='0';
    var igv;
   if(contiene_igv=='1')
        igv=0;
    else
        if(tipo_docu!='B')
            igv=0;
        else
            igv=$("#igv").val();
    
    var url = base_url+"index.php/almacen/producto/JSON_precio_producto/"+producto+"/"+moneda+"/"+cliente+"/"+unidad_medida+"/"+igv;
    $.getJSON(url,function(data){
              $.each(data, function(i,item){
                    $('#precio').val(item.PRODPREC_Precio);
              });
    });
    return false;
}
function inicializar_cabecera_item(){
    $("#producto").val('');
    $("#buscar_producto").val('');
    $("#codproducto").val('');
    $("#nombre_producto").val('');
    $("#cantidad").val('');
    $("#costo").val('');
    $("#unidad_medida").val('0');
    $("#precioProducto").val('');
    $("#precio").val('');
    limpiar_combobox('unidad_medida');
}
function obtener_detalle_presupuesto(){
    presupuesto =  $("#presupuesto").val();
    descuento100  =  $("#descuento").val();
    igv100        = $("#igv").val();
    url = base_url+"index.php/ventas/presupuesto/obtener_detalle_presupuesto/"+presupuesto;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    $.getJSON(url,function(data){
          limpiar_datos();
	  $.each(data,function(i,item){
               cliente         = item.CLIP_Codigo ;
               ruc             = item.Ruc;
               razon_social    = item.RazonSocial;
               moneda          = item.MONED_Codigo;
               formapago       = item.FORPAP_Codigo;
               serie           = item.PRESUC_Serie;
               numero          = item.PRESUC_Numero;
               codigo_usuario  = item.PRESUC_CodigoUsuario;
               
               if(item.PRESDEP_Codigo!=''){
                   j=n+1
                   producto        = item.PROD_Codigo;
                   codproducto     = item.PROD_CodigoInterno;
                   unidad_medida   = item.UNDMED_Codigo;
                   nombre_unidad   = item.UNDMED_Simbolo;
                   nombre_producto = item.PROD_Nombre;
                   cantidad        = item.PRESDEC_Cantidad;
                   pu              = item.PRESDEC_Pu;
                   subtotal        = item.PRESDEC_Subtotal;
                   descuento       = item.PRESDEC_Descuento;
                   igv             = item.PRESDEC_Igv;
                   total           = item.PRESDEC_Total
                   pu_conigv              = item.PRESDEC_Pu_ConIgv;
                   subtotal_conigv        = item.PRESDEC_Subtotal_ConIgv;
                   
                   descuento_conigv       = item.PRESDEC_Descuento_ConIgv;	

                   if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
                   fila = '<tr class="'+clase+'">';
                   fila +='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante('+n+');">';
                   fila +='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                   fila +='</a></strong></font></div></td>';
                   fila += '<td width="4%"><div align="center">'+j+'</div></td>';
                   fila += '<td width="10%"><div align="center">';
                   fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
                   fila += '<input type="hidden" class="cajaGeneral" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
                   fila += '</div></td>';
                   fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'" /></div></td>';
                   if(tipo_docu!='B')
                        fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
                   else
                        fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
                   if(tipo_docu!='B'){
                        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu['+n+']" id="prodpu['+n+']" value="'+pu+'" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">';
                        fila += '<div align="center"><input type="hidden" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+pu_conigv+'"></div></td>';
                        fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio['+n+']" id="prodprecio['+n+']" value="'+subtotal+'" readonly="readonly"></div></td>';
                   }else{
                        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+pu_conigv+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                        fila += '<td width="6%"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv['+n+']" id="prodprecio_conigv['+n+']" value="'+subtotal_conigv+'" readonly="readonly"></div></td>';
                   }
                   fila += '<td width="6%"><div align="center">';
                   fila += '<input type="hidden" readonly name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento100+'">';
                   if(tipo_docu!='B')
                       fila += '<input type="text" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento['+n+']" id="proddescuento['+n+']" value="'+descuento+'" onblur="calcula_importe2('+n+');calcula_totales();">';
                   else
                       fila += '<input type="text" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento_conigv['+n+']" id="proddescuento_conigv['+n+']" value="'+descuento_conigv+'" onblur="calcula_importe2_conigv('+n+');calcula_totales_conigv();">';
                   fila += '</div></td>';
                   if(tipo_docu!='B')
                       fila += '<td width="6%"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" value="'+igv+'" id="prodigv['+n+']" readonly></div></td>';
                   fila += '<td width="6%"><div align="center">';
                   fila +='<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']">';
                   fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv100+'">';
                   fila +='<input type="hidden" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
                   fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="'+total+'" readonly="readonly" value="0">';
                   fila += '</div></td>';
                   fila += '</tr>';
                   $("#tblDetalleComprobante").append(fila);
                }
               
               $('#ruc_cliente').val(ruc);
               $('#cliente').val(cliente);
               $('#nombre_cliente').val(razon_social);
               $('#forma_pago').val(formapago);
               $('#moneda').val(moneda);
               if(codigo_usuario) 
                   $("#docurefe_codigo").val(codigo_usuario);
               else
                   if(serie)
                       $("#docurefe_codigo").val('PR: '+serie+' / '+numero); 
                   else
                       $("#docurefe_codigo").val('PR: '+numero); 
               
	       n++;      
          })
           if(n>=0){
               if(tipo_docu!='B')
		   calcula_totales();
	       else
		   calcula_totales_conigv();
           }
           else{
                alert('El presupuesto no tiene elementos.');
           }
     });
}
function obtener_detalle_guiarem(){
    guiarem =  $("#guiaremision").val();
    descuento100  =  $("#descuento").val();
    igv100        = $("#igv").val();
    url = base_url+"index.php/almacen/guiarem/obtener_detalle_guiarem/"+guiarem;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    $.getJSON(url,function(data){
          limpiar_datos();
	  $.each(data,function(i,item){
               cliente         = item.CLIP_Codigo ;
               ruc             = item.Ruc;
               razon_social    = item.RazonSocial;
               moneda          = item.MONED_Codigo;
               serie           = item.GUIAREMC_Serie;
               numero          = item.GUIAREMC_Numero;
               codigo_usuario  = item.GUIAREMC_CodigoUsuario;
               
               if(item.GUIAREMP_Codigo!=''){
                   j=n+1
                   producto        = item.PROD_Codigo;
                   codproducto     = item.PROD_CodigoInterno;
                   unidad_medida   = item.UNDMED_Codigo;
                   nombre_unidad   = item.UNDMED_Simbolo;
                   nombre_producto = item.PROD_Nombre;
                   cantidad        = item.GUIAREMDETC_Cantidad;
                   pu              = item.GUIAREMDETC_Pu;
                   subtotal        = item.GUIAREMDETC_Subtotal;
                   descuento       = item.GUIAREMDETC_Descuento;
                   igv             = item.GUIAREMDETC_Igv;
                   total           = item.GUIAREMDETC_Total
                   pu_conigv       = item.GUIAREMDETC_Pu_ConIgv;
	

                   if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
                   fila = '<tr class="'+clase+'">';
                   fila +='<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante('+n+');">';
                   fila +='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                   fila +='</a></strong></font></div></td>';
                   fila += '<td width="4%"><div align="center">'+j+'</div></td>';
                   fila += '<td width="10%"><div align="center">';
                   fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
                   fila += '<input type="hidden" class="cajaGeneral" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
                   fila += '</div></td>';
                   fila += '<td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'" /></div></td>';
                   fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
                   fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu['+n+']" id="prodpu['+n+']" value="'+pu+'" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">';
                   fila += '<div align="center"><input type="hidden" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+pu_conigv+'"></div></td>';
                   fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio['+n+']" id="prodprecio['+n+']" value="'+subtotal+'" readonly="readonly"></div></td>';
                   fila += '<td width="6%"><div align="center">';
                   fila += '<input type="hidden" readonly name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento100+'">';
                   fila += '<input type="text" size="5" readonly maxlength="10" class="cajaGeneral" name="proddescuento['+n+']" id="proddescuento['+n+']" value="'+descuento+'" onblur="calcula_importe2('+n+');calcula_totales();">';
                   fila += '</div></td>';
                   fila += '<td width="6%"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" value="'+igv+'" id="prodigv['+n+']" readonly></div></td>';
                   fila += '<td width="6%"><div align="center">';
                   fila +='<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']">';
                   fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv100+'">';
                   fila +='<input type="hidden" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
                   fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="'+total+'" readonly="readonly" value="0">';
                   fila += '</div></td>';
                   fila += '</tr>';
                   $("#tblDetalleComprobante").append(fila);
                }
               
               $('#ruc_cliente').val(ruc);
               $('#cliente').val(cliente);
               $('#nombre_cliente').val(razon_social);
               $('#moneda').val(moneda);
               if(codigo_usuario)
                   $("#guiaremision_codigo").val(codigo_usuario);
               else
                   if(serie)
                       $("#guiaremision_codigo").val(serie+'-'+numero);
                   else
                       $("#guiaremision_codigo").val(numero);
               
	       n++;      
          })
           if(n>=0)
        	   calcula_totales();
           else
                alert('La guía de remisión no tiene elementos.');
     });
}
function limpiar_datos(){
    $('#ruc_cliente').val('');
    $('#cliente').val('');
    $('#nombre_cliente').val('');
    $('#formapago').val('');
    $('#moneda').val('1');
    
    n = document.getElementById('tblDetalleComprobante').rows.length;
    for(i=0;i<n;i++){
		a                = "detacodi["+i+"]";
        b                = "detaccion["+i+"]";
        fila            = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display="none";
        document.getElementById(b).value = "e";
    }
}
function obtener_cliente(){
    var numdoc = $("#ruc_cliente").val();
    $('#cliente,#nombre_cliente').val('');
    
    if(numdoc=='')
        return false;

    var url = base_url+"index.php/empresa/cliente/JSON_buscar_cliente/"+numdoc;
    $.getJSON(url,function(data){
                $.each(data, function(i,item){
		    if(item.EMPRC_RazonSocial!=''){
                        $('#nombre_cliente').val(item.EMPRC_RazonSocial);
                        $('#cliente').val(item.CLIP_Codigo);
                        $('#codproducto').focus();
                    }
                    else{
                        $('#nombre_cliente').val('No se encontró ningún registro');
                        $('#linkVerCliente').focus();
                    }
		});
    });
    return true;
}
function obtener_proveedor(){
    var numdoc = $("#ruc_proveedor").val();
    $("#proveedor, #nombre_proveedor").val('');
    
    if(numdoc=='')
	return false;

    var url = base_url+"index.php/empresa/proveedor/obtener_nombre_proveedor/"+numdoc;
    $.getJSON(url,function(data){
                $.each(data, function(i,item){
		    if(item.EMPRC_RazonSocial!=''){
                        $('#nombre_proveedor').val(item.EMPRC_RazonSocial);
                        $('#proveedor').val(item.PROVP_Codigo);
                        $('#codproducto').focus();
                    }
                    else{
                        $('#nombre_proveedor').val('No se encontró ningún registro');
                        $('#linkVerProveedor').focus();
                    }
		});
    });
    return true;
}
function obtener_producto(){
    var flagBS        = $("#flagBS").val();
    var codproducto   = $("#codproducto").val();
    $("#producto, #nombre_producto").val('');
    if(codproducto=='')
        return false;
    
    var url = base_url+"index.php/almacen/producto/obtener_nombre_producto/"+flagBS+"/"+codproducto;
     $.getJSON(url,function(data){
         $.each(data,function(i,item){
             if(item.PROD_Nombre!=''){
                 $("#producto").val(item.PROD_Codigo);
                 $("#nombre_producto").val(item.PROD_Nombre);
                 listar_unidad_medida_producto($("#producto").val());
                 $('#cantidad').focus();
             }
             else{
                 $('#nombre_producto').val('No se encontró ningún registro');
                 $('#linkVerProdcuto').focus();
             }
                 
         });
     });
     return true;
}

function limpiar_campos_producto(){
    $("#producto,  #codproducto, #nombre_producto, #cantidad, #precio").val('');
    limpiar_combobox('unidad_medida');
    if($('#flagBS').val()=='B')
        $('#unidad_medida').show();
    else
        $('#unidad_medida').hide();
    $('#linkVerProducto').attr('href', ''+base_url+'index.php/almacen/producto/ventana_busqueda_producto/'+$('#flagBS').val());
}


