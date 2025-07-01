var base_url;
jQuery(document).ready(function(){
	base_url   = $("#base_url").val();
        $("#nuevaGuiain").click(function(){
            url = base_url+"index.php/almacen/guiain/nueva";
            location.href = url;
        });
        $("#grabarGuiain").click(function(){
            $("#frmGuiain").submit();
        });
        $("#limpiarGuiain").click(function(){
            url = base_url+"index.php/almacen/guiain/limpiar";
            location.href = url;
        });
        $("#cancelarGuiain").click(function(){
            url = base_url+"index.php/almacen/guiain/cancelar";
            location.href = url;
        });
        $("#cancelarGuiain2").click(function(){
            url = base_url+"index.php/almacen/guian/listar";
            location.href = url;
        });
        $("#buscarGuiain").click(function(){
                dataString = $("#form_busquedaGuiain").serialize();
                txtCargo   = $("#txtCargo").val();
                if(txtCargo!=''){
                        $("#form_busquedaGuiain").submit();
                }
                else{
                        $("#txtCargo").focus();
                        alert('Debe ingresar un nombre a buscar.');
                }
        });	
});
function editar_guiain(guiain){
    url = base_url+"index.php/almacen/guiain/editar/"+guiain;
    location.href= url;
}
function eliminar_guiain(guiain){
    if(confirm('Esta seguro desea eliminar este Comprobante de Ingreso?')){
        dataString   = "codigo="+guiain;
        url          = base_url+"index.php/almacen/guiain/eliminar";
        $.post(url,dataString,function(data){
            location.href = base_url+"index.php/almacen/guiain/listar";
        });
    }
}
function ver_guiain(guiain){
  location.href = base_url+"index.php/almacen/guiain/ver/"+guiain;
}
function ver_guiain_pdf(guiain){
    url = base_url+"index.php/almacen/guiain/ver_pdf/"+guiain;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_guiain(){
    location.href = base_url+"index.php/almacen/guiain/listar";
}
function valida_guiain(){
    if($("#orden_compra").val()==""){
        alert("Seleccione una orden de compra");
        $("#orden_compra").select();
        return false;
    }
    else if($("#GenInd").val()==""){
        alert("Debe ingresar los números de serie");
        return false;
    }
    else if($("#nombre_proveedor").val()==""){
        alert("Ingrese un proveedor");
        $("#ruc").select();
        return false;
    }
    else if($("#almacen").val()==""){
        alert("Seleccione un almacen.");
        $("#almacen").select();
        return false;
    }
    return true;
}
/********************************************************************************************/
function obtener_proveedor(){
	ruc        = $("#ruc").val();
	url        = base_url+"index.php/comercial/comercial/obtener_nombre_proveedor/"+ruc;
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
function agregar_producto_guiain(){
    codproducto     = $("#codproducto").val();
    producto        = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    descuento       = $("#descuento").val();
    igv             = $("#igv").val();
    cantidad        = $("#cantidad").val();
    unidad_medida   = $("#unidad_medida").val();//select
    select_umedida  = document.getElementById("unidad_medida");
    options_umedida = select_umedida.getElementsByTagName("option");
    nombre_unidad   = $("#nombre_unidad_medida").val();
    n = document.getElementById('tblDetalleOcompra').rows.length;
    j = n+1;
    if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
    if(nombre_producto!='' && cantidad!='0')
    {
        fila  = '<tr class="'+clase+'">';
        fila+='<td width="3%"><div align="center"><font color="red"><strong><a href="#" class="eliminar">';
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
        fila+='<input type="text" class="cajaMinima" name="detocom['+n+']" id="detocom['+n+']">';
        fila+='<input type="hidden" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
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
function eliminar_producto_guiain(obj){
    if(confirm('Esta seguro que desea eliminar este producto?')){
        $(obj).parent().parent().parent().parent().parent().remove();
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
function obtener_detalle_ocompra(){
     orden_compra = $("#orden_compra").val();
     url = base_url+"index.php/compras/ocompra/obtener_detalle_ocompra2/"+orden_compra;
     n = 0;
     $.getJSON(url,function(data){
          fila= '<table width="100%" height="250px;" border="0" cellpadding="0" cellspacing="0">';
          fila+= '<tr>';
          fila+= '<td valign="top">';
          fila = '<table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">';
          $.each(data,function(i,item){
               n=i;
               j=i+1
               ocompra         = item.OCOMP_Codigo;
               producto        = item.PROD_Codigo;
               codproducto     = item.PROD_CodigoInterno;
               unidad_medida   = item.UNDMED_Codigo;
               nombre_unidad   = item.UNDMED_Simbolo;
               nombre_producto = item.PROD_Nombre;
               cantidad        = item.OCOMDEC_Cantidad;
               costo           = item.OCOMDEC_Pu;
               proveedor       = item.PROVP_Codigo;
               ruc             = item.Ruc;
               razon_social    = item.RazonSocial;
               almacen         = item.ALMAP_Codigo;
               flagGenInd      = item.PROD_GenericoIndividual;
               if((i+1)%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
               fila+= '<tr class="'+clase+'" valign="middle">';
               fila+= '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_guiain(this);">';
               fila+= '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
               fila+= '</a></strong></font></div></td>';
               fila+= '<td width="5%"><div align="center">'+j+'</div></td>';
               fila+= '<td width="10%"><div align="center">';
               fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
               fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
               fila+= '<input type="hidden" class="cajaMinima" name="flagGenInd['+n+']" id="flagGenInd['+n+']" value="'+flagGenInd+'">';
               fila+= '</div></td>';
               fila+= '<td><div align="left">'+nombre_producto+'</div></td>';
               fila+= '<td width="8%"><div align="center">';
               if(flagGenInd=="I"){
                  fila+= '<a href="javascript:;" onclick="ventana_producto_serie('+n+')"><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" border="0"/></a>';
                  $("#GenInd").val('');
               }
               else{
                   $("#GenInd").val('G');
               }
               fila+= '&nbsp;<input type="text" class="cajaPequena2" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onkeypress="return numbersonly(this,event,\'.\');">';
               fila+= '</div></td>';
               fila+= '<td width="8%"><div align="center">'+nombre_unidad+'</div></td>';
               fila+= '<input type="hidden" class="cajaMinima" name="detguiain['+n+']" id="detguiain['+n+']">';
               fila+= '<input type="hidden" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
               fila+= '<input type="hidden" class="cajaPequena2" name="prodpu['+n+']" id="prodpu['+n+']" value="'+costo+'" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
               fila+= '</div></td>';
               fila+= '</tr>';
          })
          fila+='</table>'
          fila+= '</td>';
          fila+= '</tr>';
          fila+= '</table>';
          $('#ruc').val(ruc);
          $('#proveedor').val(proveedor);
          $('#nombre_proveedor').val(razon_social);
          $('#almacen').val(almacen);
          //$(".divBusqueda").hide();
          if(n>=0){
            $("#lineaResultado2").html(fila);
          }
          else{
            $("#lineaResultado2").html('');
            alert('La Orden de compra no tiene elementos.');
          }
     });
}