jQuery(document).ready(function(){
	base_url   = $("#base_url").val();
	tipo_docu   = $("#tipo_docu").val();
        contiene_igv = $("#contiene_igv").val();
        tipo_codificacion = $("#tipo_codificacion").val();
        
	$("#nuevaCotizacion").click(function(){
		url = base_url+"index.php/compras/cotizaciones/presupuesto_nueva";
		location.href = url;
	});
	$("#nuevaCotizacion_factura").click(function(){
		url = base_url+"index.php/compras/cotizaciones/presupuesto_nueva/F";
		location.href = url;
	});
	$("#nuevaCotizacion_boleta").click(function(){
		url = base_url+"index.php/compras/cotizaciones/presupuesto_nueva/B";
		location.href = url;
	});
    var click =0;
 	$("#grabarCotizacion").click(function(){ 

         if(click == 0){

                $('img#loading').css('visibility','visible');
                var codigo=$('#codigo').val();
                if(codigo=='')
                    url = base_url+"index.php/compras/cotizaciones/presupuesto_insertar";
                else
                    url = base_url+"index.php/compras/cotizaciones/presupuesto_modificar";

                dataString  = $('#frmCotizacion').serialize();
                $.post(url,dataString,function(data){
                        $('img#loading').css('visibility','hidden');
                        switch(data.result){
                            case 'ok': location.href = base_url+"index.php/compras/cotizaciones/presupuestos";
                            console.log("echo");
                                    break;
                            case 'error': 
                                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                    console.log("error");
                                    break;
                        }
                },'json');
                 click = 1;
            }
            else{
               click = 1;
            }

	}); 
	
	$("#limpiarCotizacion").click(function(){
		url = base_url+"index.php/compras/cotizaciones/presupuestos/0/1"; //1: espara decirle al contraldor que limpie las valirables de la sesión para la busqueda
		location.href = url;
	});
	$("#cancelarCotizacion").click(function(){
		url = base_url+"index.php/compras/cotizaciones/presupuestos";
		location.href = url;		
	});
	$("#buscarCotizacion").click(function(){
               $("#form_busqueda").submit();
	});
        $('img#linkVerPersona').click(function(){
           var contacto=$('#contacto').val();
           if(contacto!='')
                window.open(base_url+'index.php/maestros/persona/persona_ventana_mostrar/'+contacto, '_blank', 'width=700,height=380,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0'); 
        });
        $('#contacto').change(function(){
           if(this.value!=''){
               var temp=this.value.split('-');
               $('#linkVerPersona').show().attr('href', base_url+'index.php/maestros/persona/persona_ventana_mostrar/'+temp[0]);
            }
            else
                $('#linkVerPersona').hide();
        });
        $("#lugar_entrega").click(function() {
            $('#lista_direcciones').slideUp("fast"); 
        });
        $("#linkVerDirecciones").click(function(){
            var cliente=$("#cliente").val();
            $('#lista_mis_direcciones').slideUp("fast");

            $("#lista_direcciones ul").html('');
            $("#lista_direcciones").slideToggle("fast", function(){
  
            var url = base_url+"index.php/empresa/cliente/JSON_listar_sucursalesEmpresa/"+cliente;
            $.getJSON(url,function(data){
                        $.each(data, function(i,item){
                            fila='';
                            if(item.Tipo=='1'){
                                fila+='<li style="list-style: none; font-weight:bold; color:#aaa"">'+item.Titulo+'</li>';
                            }
                            else{
                                fila+='<li><a href="javascript:;">'+item.EESTAC_Direccion;
                                /*if(item.distrito!='')
                                    fila+=' / '+item.distrito;
                                if(item.provincia!='')
                                    fila+=' / '+item.provincia;
                                if(item.departamento!='')
                                    fila+=' / '+item.departamento;*/
                                fila+='</a></li>';
                            } 
                            $("#lista_direcciones ul").append(fila);
                        });
            });
            return true;
            });

        });
        $("#lista_direcciones li").on('click', 'a', function(){
            $("#lugar_entrega").val($(this).html());
            $('#lista_direcciones').slideUp("fast"); 
        });
        $("#linkVerSerieNum").click(function () {
            var temp=$("#linkVerSerieNum p").html();
            var serienum=temp.split('-');
            switch(tipo_codificacion){
                case '1': $("#numero").val(serienum[1]); break;
                case '2': $("#serie").val(serienum[0]);
                          $("#numero").val(serienum[1]); break;    
            }
        });
	$('#buscar_proveedor_0').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==13){
            if($(this).val()!=''){
                $('#linkSelecProveedor').attr('href', base_url+'index.php/empresa/proveedor/ventana_selecciona_proveedor/'+$('#buscar_proveedor_0').val()).click();

            }
        } 
    });
	
		 $("#nombre_proveedor_0").keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==13){
            if($(this).val()!=''){
                $('#linkSelecProveedor').attr('href', base_url+'index.php/empresa/proveedor/ventana_selecciona_proveedor/'+$("#nombre_proveedor_0").val()).click();
            }
        } 
    });    
  $('#buscar_producto_0').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==13){
            if($(this).val()!=''){
                $('#linkSelecProducto').attr('href', base_url+'index.php/almacen/producto/ventana_selecciona_producto/F/'+$('#flagBS').val()+'/'+$('#buscar_producto_0').val()).click();
				}

        } 

    });
    $("#agregarProv").click(function(){
    
        var n = document.getElementById('tablaProv').rows.length;
        n += 1;
        fila = "<tr>";
        fila += "<td align='center'>";
        fila += "<input type='hidden' name='proveedor["+n+"]' id='proveedor_"+n+"' size='5' value='' />";
        fila += "<input name='buscar_proveedor["+n+"]' type='text' class='cajaGeneral' id='buscar_proveedor_"+n+"' size='10' title='Ingrese parte del nombre o el nro. de documento, luego presione ENTER.' />";
        fila += "<input type='hidden' name='ruc_proveedor["+n+"]' class='cajaGeneral' id='ruc_proveedor_"+n+"' size='10' maxlength='11'  value='' onkeypress='return numbersonly(this,event,'.');' />";
        fila += "<input type='text' name='nombre_proveedor["+n+"]' class='cajaGeneral cajaSoloLectura' id='nombre_proveedor_"+n+"' size='40' maxlength='50'  value='' onclick='proveedor_seleccionar("+n+")'/>"
        fila += "<a href='<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/' id='linkSelecProveedor'></a>";
        fila += "</td>";
        fila += "</tr>";
        $("#tablaProv").append(fila);
        $("#fila").val(""+n+"");


    })		
})

function editar_presupuesto(presupuesto){
        location.href = base_url+"index.php/compras/cotizaciones/presupuesto_editar/"+presupuesto;
}
function eliminar_presupuesto(presupuesto){
    if(confirm('Esta seguro que desea eliminar este presupuesto?')){
        dataString = "presupuesto="+presupuesto;
        url = base_url+"index.php/compras/cotizaciones/presupuesto_eliminar";
        $.post(url,dataString,function(data){
                location.href = base_url+"index.php/compras/cotizaciones/presupuestos";
        });
    }
}
function ver_presupuesto_ver_pdf(presupuesto){
    var url = base_url+"index.php/compras/cotizaciones/presupuesto_ver_pdf/"+presupuesto;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function ver_presupuesto_ver_xls(presupuesto){
    var url = base_url+"index.php/compras/cotizaciones/presupuesto_ver_xls/"+presupuesto;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function ver_presupuesto_ver_pdf_conmenbrete(presupuesto){
    var url = base_url+"index.php/compras/cotizaciones/presupuesto_ver_pdf_conmenbrete/"+presupuesto;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_presupuesto(){
    location.href = base_url+"index.php/compras/cotizaciones/presupuestos";
}
function agregar_producto_presupuesto(){
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
        if(contiene_igv=='1')
            precio=money_format(precio_conigv*100/(igv+100))
        else{
            precio=precio_conigv;
            precio_conigv = money_format(precio_conigv*(100+igv)/100);
        }
        unidad_medida = $("#unidad_medida").val();//select
        nombre_unidad = $('#unidad_medida option:selected').html()
	n = document.getElementById('tblDetalleCotizacion').rows.length;
	j = n+1;
	if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
	
        if(codproducto==''){
            alert('Ingrese el producto.');
            $("#codproducto").focus();
            return false;
        }
        if(cantidad==''){
            alert('Ingrese una cantidad.');
            $("#cantidad").focus();
            return false;
        }
        if(unidad_medida==''){
            $("#unidad_medida").focus();
            alert('Seleccine una unidad de medida.');
            return false;
        }
       fila  = '<tr class="'+clase+'">';
    fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_presupuesto('+n+');">';
    fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila+='</a></strong></font></div></td>';
    fila +=	'<td width="4%"><div align="center">'+j+'</div></td>';
    fila += '<td width="10%"><div align="center">';
    fila+= '<input type="hidden" name="flagBS['+n+']" id="flagBS['+n+']" value="'+flagBS+'">';
    fila+= '<input type="hidden" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
    fila+= '<input type="hidden" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
    fila+= '</div></td>';
    fila +=	'<td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'" /></div></td>';
    if(tipo_docu!='B')
        fila+= '<td width="10%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
    else
         fila+= '<td width="10%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
   if(tipo_docu!='B'){
        fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio_conigv+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" onblur="modifica_pu_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%" style="display:none;"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'
        fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
    }
    else{
         fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio_conigv+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" onblur="modifica_pu_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%" style="display:none;"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'
        fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
    }
        
    //if(tipo_docu!='B')
        fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly></div></td>';
    fila += '<td width="6%" style="display:none;"><div align="center">';
    fila+='<input type="hidden" value="n" name="detaccion['+n+']" id="detaccion['+n+']">';
    fila+= '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
    fila+='<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']">';
    fila+= '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento+'">';
    //if(tipo_docu!='B')
        fila+= '<input type="hidden" name="proddescuento['+n+']" id="proddescuento['+n+']" onblur="calcula_importe2('+n+');" />';
    //else
      //  fila+= '<input type="hidden" name="proddescuento_conigv['+n+']" id="proddescuento_conigv['+n+']" onblur="calcula_importe2_conigv('+n+');" />';
    fila+= '<input type="hidden" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
    fila+= '</div></td>';
    fila += '</tr>';
        $("#tblDetalleCotizacion").append(fila);
        
        //if(tipo_docu!='B')
            calcula_importe(n); //Para facturas o comprobantes
        //else
          //  calcula_importe_conigv(n); //Para boletas
        inicializar_cabecera_item(); 
        return true;
}
function eliminar_producto_presupuesto(n){
     if(confirm('Esta seguro que desea eliminar este producto?')){
          a                = "detacodi["+n+"]";
          b                = "detaccion["+n+"]";
          fila            = document.getElementById(a).parentNode.parentNode.parentNode;
          fila.style.display="none";
          document.getElementById(b).value = "e";
          
          calcula_totales();
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
    //alert(pu_conigv)
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
    n = document.getElementById('tblDetalleCotizacion').rows.length;
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
            //importe = parseFloat(document.getElementById(a).value);
            //igv = parseFloat(document.getElementById(b).value);
            descuento = parseFloat(document.getElementById(c).value);
            precio = parseFloat(document.getElementById(d).value);
            //importe_total = money_format(importe + importe_total);
            //igv_total = money_format(igv + igv_total);
            descuento_total = money_format(descuento + descuento_total);
            precio_total = money_format(precio + precio_total);
        }
    }

  
	igv100 = parseInt($("#igv").val());
	igv_total=money_format(precio_total*igv100/100);
	importe_total = money_format(precio_total + igv_total-descuento_total);
	
    $("#importetotal").val(importe_total.toFixed(2));
    $("#igvtotal").val(igv_total.toFixed(2));
    $("#descuentotal").val(descuento_total.toFixed(2));
    $("#preciototal").val(precio_total.toFixed(2));
}
function calcula_totales_conigv(){
    n = document.getElementById('tblDetalleCotizacion').rows.length;
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
function modifica_pu(n){
    a  = "prodpu["+n+"]";
    g = "prodigv100["+n+"]";
    i = "prodpu_conigv["+n+"]";
    
    pu = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value); 
    precio_conigv = money_format(pu*(100+igv100)/100);
    document.getElementById(i).value=precio_conigv;

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
    return true;
}

function load_cotizaciones(){
	var value = $('#pedidos').val();
	if(value == ''){
		$('#text_cotizacion').html("");
		$("#show_cotizaciones").html("");
	}else{
		var url = base_url+"index.php/compras/cuadrocom/cargar_todas/"+value;
		$("#show_cotizaciones").load(url);
		$('#text_cotizacion').html("Cotizacion :");
	}
	
}


function obtener_detalle_presupuesto_compra(){
    presupuesto =  $("#pedidos_ganadores").val();
    descuento100  =  $("#descuento").val();
    igv100        = $("#igv").val();
	
	
	
    url = base_url+"index.php/ventas/presupuesto/obtener_detalle_presupuesto/"+presupuesto;
    n = document.getElementById('tblDetalleCotizacion').rows.length;
    $.getJSON(url,function(data){
          limpiar_datos();
	  $.each(data,function(i,item){
			tipo_doc = $("#tipo_docu").val();
			if(tipo_doc == 'B'){
				   presupuesto          = item.PRESDEP_Codigo;
				   j=n+1;
				   producto        = item.PROD_Codigo;
				   codproducto     = item.PROD_CodigoInterno;
				   moneda          = item.MONED_Codigo;
				   unidad_medida   = item.UNDMED_Codigo;
				   nombre_unidad   = item.UNDMED_Simbolo;
				   nombre_producto = item.PROD_Nombre;
				   cantidad        = item.PRESDEC_Cantidad;
				   pu              = item.PRESDEC_Pu;
				   subtotal        = item.PRESDEC_Subtotal;
				   descuento       = item.PRESDEC_Descuento;
				   igv             = item.PRESDEC_Igv;
				   total           = item.PRESDEC_Total
				   pu_conigv         = item.PRESDEC_Pu_ConIgv;
				   subtotal_conigv   = item.PRESDEC_Subtotal_ConIgv;
			       descuento_conigv  = item.PRESDEC_Descuento_ConIgv;	
				   cliente         = item.CLIP_Codigo ;
				   ruc             = item.Ruc;
				   razon_social    = item.RazonSocial;
				   formapago       = item.FORPAP_Codigo;
				   descuento100 = item.PRESDEC_Descuento100;
				   importe = item.PRESDEC_Descuento_ConIgv;
				   if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
				   
				   
					var fila = '';
					fila += '<tr class="'+clase+'">';
					fila += '<td width="3%">';
					fila += '<div align="center">';
					fila += '<font color="red">';
					fila += '<strong>';
					fila += '<a href="javascript:;" onclick="eliminar_producto_presupuesto('+n+');">';
					fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
					fila += '</a>';
					fila += '</strong>';
					fila += '</font>';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="4%">';
					fila += '<div align="center">'+j+'</div>';
					fila += '</td>';
					fila += '<td width="10%">';
					fila += '<div align="center">'+codproducto+'</div>';
					fila += '</td>';
					fila += '<td>';
					fila += '<div align="left">';
					fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="left">';
					fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="11'+pu_conigv+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv['+n+']" id="prodprecio_conigv['+n+']" value="'+subtotal_conigv+'" readonly="readonly">';
					fila += '</div>';
					fila += '</td>';

					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento100+'">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" readonly name="proddescuento_conigv['+n+']" id="proddescuento_conigv['+n+']" value="0" onblur="calcula_importe2_conigv('+n+');">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="hidden" name="detaccion['+n+']" id="detaccion['+n+']" value="m">';
					fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
					fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
					fila += '<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']" value="'+presupuesto+'">';
					fila += '<input type="hidden" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">';
					fila += '<input type="hidden" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" readonly="readonly" value="'+importe+'">';
					fila += '</div>';
					fila += '</td>';
					fila += '</tr>';
					
				   $("#tblDetalleCotizacion").append(fila);
					   $('#ruc_cliente').val(ruc);
					   $('#cliente').val(cliente);
					   $('#nombre_cliente').val(razon_social);
					   $('#formapago').val(formapago);
					   $('#moneda').val(moneda);
				   n++;
			}else{
				   presupuesto          = item.PRESDEP_Codigo;
				   j=n+1;
				   producto        = item.PROD_Codigo;
				   codproducto     = item.PROD_CodigoInterno;
				   moneda          = item.MONED_Codigo;
				   unidad_medida   = item.UNDMED_Codigo;
				   nombre_unidad   = item.UNDMED_Simbolo;
				   nombre_producto = item.PROD_Nombre;
				   cantidad        = item.PRESDEC_Cantidad;
				   pu              = item.PRESDEC_Pu;
				   subtotal        = item.PRESDEC_Subtotal;
				   descuento       = item.PRESDEC_Descuento;
				   igv             = item.PRESDEC_Igv;
				   total           = item.PRESDEC_Total
				   pu_conigv         = item.PRESDEC_Pu_ConIgv;
				   subtotal_conigv   = item.PRESDEC_Subtotal_ConIgv;
			       descuento_conigv  = item.PRESDEC_Descuento_ConIgv;	
				   cliente         = item.CLIP_Codigo ;
				   ruc             = item.Ruc;
				   razon_social    = item.RazonSocial;
				   formapago       = item.FORPAP_Codigo;
				   descuento100 = item.PRESDEC_Descuento100;
				   importe = item.PRESDEC_Descuento_ConIgv;
				   prodigv = item.PRESDEC_Subtotal_ConIgv;
				   
               if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
					var fila = '';
					fila += '<tr class="'+clase+'">';
					fila += '<td width="3%">';
					fila += '<div align="center">';
					fila += '<font color="red">';
					fila += '<strong>';
					fila += '<a href="javascript:;" onclick="eliminar_producto_presupuesto('+n+');">';
					fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
					fila += '</a>';
					fila += '</strong>';
					fila += '</font>';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="4%">';
					fila += '<div align="center">'+j+'</div>';
					fila += '</td>';
					fila += '<td width="10%">';
					fila += '<div align="center">'+codproducto+'</div>';
					fila += '</td>';
					fila += '<td>';
					fila += '<div align="left">';
					fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="left">';
					fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu['+n+']" id="prodpu['+n+']" value="11'+pu+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="'+subtotal+'" readonly="readonly">';
					fila += '<input type="hidden" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+pu_conigv+'">';
					fila += '</div>';
					fila += '</td>';

					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento100+'">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" readonly name="proddescuento['+n+']" id="proddescuento['+n+']" value="'+descuento+'" onblur="calcula_importe2_conigv('+n+');">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly="readonly" value="'+prodigv+'">';
					fila += '</div>';
					fila += '</td>';
					fila += '<td width="6%">';
					fila += '<div align="center">';
					fila += '<input type="hidden" name="detaccion['+n+']" id="detaccion['+n+']" value="m">';
					fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
					fila += '<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']" value="'+presupuesto+'">';
					fila += '<input type="hidden" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">';
					fila += '<input type="hidden" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
					fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" readonly="readonly" value="'+importe+'">';
					fila += '</div>';
					fila += '</td>';
					fila += '</tr>';
	       $("#tblDetalleCotizacion").append(fila);
               $('#ruc_cliente').val(ruc);
               $('#cliente').val(cliente);
               $('#nombre_cliente').val(razon_social);
               $('#formapago').val(formapago);
               $('#moneda').val(moneda);
	       n++;
          
		  
		  }
		  
		  
		  })
           
           if(n>=0){
        	   calcula_totales();
           }
           else{
                alert('La cotizacion no tiene elementos.');
           }
     });
}


function limpiar_datos(){
    $('#ruc_cliente').val('');
    $('#cliente').val('');
    $('#nombre_cliente').val('');
    $('#formapago').val('');
    $('#moneda').val('1');
    
    n = document.getElementById('tblDetalleCotizacion').rows.length;
    for(i=0;i<n;i++){
	a                = "detacodi["+i+"]";
        b                = "detaccion["+i+"]";
        fila            = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display="none";
        document.getElementById(b).value = "e";
    }
}

function inicializar_cabecera_item(){
    $("#producto").val('');
    $("#codproducto").val('');
    $("#buscar_producto").val('');
    $("#nombre_producto").val('');
    $("#cantidad").val('');
    $("#nombre_unidad").val('');
    $("#unidad_medida").val('0');
    $("#precio").val('');
    limpiar_combobox('unidad_medida');
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
function listar_contactos(empresa){
    a      = "contacto";
    select = document.getElementById(a);
    
    url = base_url+"index.php/empresa/empresa/JSON_listar_contactos/"+empresa;
    $.getJSON(url,function(data){
          $.each(data, function(i,item){
            codigo      = item.persona+'-'+item.area;
            descripcion = item.nombre_persona;
            area        = item.nombre_area;
            opt         = document.createElement('option');
            if(area!='')
                texto   = document.createTextNode(descripcion+' - AREA: '+area);
            else
                texto   = document.createTextNode(descripcion);
            opt.appendChild(texto);
            opt.value = codigo;
            select.appendChild(opt);
            
          });
    });
    return true;
}
function obtener_producto(){
    var codproducto   = $("#codproducto").val();
    $("#producto, #nombre_producto").val('');
    if(codproducto=='')
        return false;
    
    var url = base_url+"index.php/almacen/producto/obtener_nombre_producto/"+codproducto;
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
                 $('#linkVerProducto').focus();
             }
                 
         });
     });
     return true;
}
function listar_precios_x_producto_unidad(producto){
    producto = $("#producto").val();
    unidad = $("#unidad_medida").val();
    moneda = $("#moneda").val();
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url          = base_url+"index.php/almacen/producto/listar_precios_x_producto_unidad/"+producto+"/"+unidad+"/"+moneda;
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

    var bd=0
    $.getJSON(url,function(data){
        console.log(data);
        $.each(data, function(i,item){
            codigo		= item.codigo;
            moneda		= item.moneda;
            precio		= item.precio;
            establecimiento		= item.establecimiento;
            posicion_precio	= item.posicion_precio;
             select	= item.posicion;
            opt         = document.createElement('option');
            texto       = document.createTextNode(moneda+" "+precio+" "+establecimiento);
            opt.appendChild(texto);
            opt.value = precio;
            if(select==true){                
                opt.setAttribute('selected','selected')
                $("#precio").val(precio);
                bd=1
            }
            if(bd==0){
                opt.removeAttribute('selected')
                $("#precio").val('');
            }
            select_precio.appendChild(opt);        
                        
                        
                        
        });
    });
}
function listar_unidad_medida_producto(producto){
    var base_url      = $("#base_url").val();
    var flagBS        = $("#flagBS").val();
    url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
    select_umedida   = document.getElementById('unidad_medida');
      
    limpiar_combobox('unidad_medida');
    
    $("#cantidad").val('');  
    $("#precio").val('');
        
    $.getJSON(url,function(data){
        $.each(data, function(i,item){
            codigo            = item.UNDMED_Codigo;
            descripcion  = item.UNDMED_Descripcion;
            simbolo         = item.UNDMED_Simbolo;
            nombre_producto = item.PROD_Nombre;
            marca           = item.MARCC_Descripcion;
            modelo          = item.PROD_Modelo;
            presentacion    = item.PROD_Presentacion;
            opt         = document.createElement('option');
            texto       = document.createTextNode(descripcion);
            opt.appendChild(texto);
            opt.value = codigo;
            if(i==0)
                opt.selected=true;
            select_umedida.appendChild(opt);
        });
        var nombre;
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
        listar_precios_x_producto_unidad(producto);
    });
}
function obtener_detalle_pedido(){
    var pedido = $('#pedidos').val();
    url = base_url+"index.php/compras/pedido/obtener_detalle_presupuesto/"+pedido;
    n = document.getElementById('tblDetalleCotizacion').rows.length;
    $.getJSON(url,function(data){
        limpiar_datos();
    $.each(data,function(i,item){
        pedido          = item.cod_pedido;
        j=n+1
        producto        = item.cod_producto;
        unidad_medida   = item.unidad;
        cantidad        = item.cantidad;
        nombre_producto = item.des_producto;
        nombre_unidad   = item.nombre_unidad;
        
        if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
        fila  = '<tr class="'+clase+'">';
        fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_presupuesto('+n+');">';
        fila+='<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
        fila+='</a></strong></font></div></td>';
        fila += '<td width="4%"><div align="center">'+j+'</div></td>';
        fila += '<td width="10%"><div align="center">';
        //fila+= '<input type="hidden" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
        fila+= '<input type="hidden" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+producto;
        fila+= '<input type="hidden" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
        fila+= '<input type="hidden" name="eliminado['+n+']" id="eliminado['+n+']" value="no">';
        fila+= '</div></td>';
        fila += '<td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'" /></div></td>';
        if(tipo_docu!='B')
            fila+= '<td width="10%"><div align="center"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
        else
            fila+= '<td width="10%"><div align="center"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'+nombre_unidad+'</div></td>';
        if(tipo_docu!='B'){
            fila += '<td width="6%" style="display:none;"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'
            fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
        }
        else{
            fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv['+n+']" id="prodprecio_conigv['+n+']" value="0" readonly="readonly"></div></td>';
        }

        fila += '<td width="6%" style="display:none;"><div align="center">';           
        fila+= '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento+'">';
        if(tipo_docu!='B')
            fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento['+n+']" id="proddescuento['+n+']" onblur="calcula_importe2('+n+');" />';
        else
            fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento_conigv['+n+']" id="proddescuento_conigv['+n+']" onblur="calcula_importe2_conigv('+n+');" />';
        fila+= '</div></td>';
        if(tipo_docu!='B')
            fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly></div></td>';
        fila += '<td width="6%" style="display:none;"><div align="center">';
        fila+='<input type="hidden" value="n" name="detaccion['+n+']" id="detaccion['+n+']">';
        fila+= '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
        fila+= '<input type="hidden" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+igv+'">';
        fila+='<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']">';
        fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
        fila+= '</div></td>';
        fila += '</tr>';
        $("#tblDetalleCotizacion").append(fila);
        n++;
        
      })
       
       if(n>=0){
           calcula_totales();
       }
       else{
            alert('El pedido no tiene elementos.');
       }
    });
}
