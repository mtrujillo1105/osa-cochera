var base_url;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    $("#nuevaGuiasa").click(function(){
        url = base_url+"index.php/almacen/guiasa/nueva";
        location.href = url;
    });
    $("#grabarGuiasa").click(function(){
        $("#frmGuiasa").submit();
    });
    $("#limpiarGuiasa").click(function(){
         url = base_url+"index.php/almacen/guiasa/limpiar";
        location.href = url;
    });
    $("#cancelarGuiasa").click(function(){
        url = base_url+"index.php/almacen/guiasa/listar";
        location.href = url;
    });
    $("#cancelarGuiasa2").click(function(){
        url = base_url+"index.php/almacen/guiasa/listar";
        location.href = url;
    });
    $("#buscarGuiasa").click(function(){
        dataString = $("#form_busquedaGuiasa").serialize();
        txtCargo   = $("#txtCargo").val();
        if(txtCargo!=''){
            $("#form_busquedaGuiasa").submit();
        }
        else{
            $("#txtCargo").focus();
            alert('Debe ingresar un nombre a buscar.');
        }
    });
    $('#almacen').change(function(){
           if(this.value!=''){
               $('#linkVerProducto').show().attr('href', base_url+'index.php/almacen/producto/ventana_busqueda_producto_x_almacen/'+this.value);
            }
            else
                $('#linkVerProducto').hide();
        });
})
function editar_guiasa(guiasa){
     location.href=base_url+"index.php/almacen/guiasa/editar/"+guiasa;
}
function eliminar_guiasa(guiasa){
    if(confirm('Esta seguro desea eliminar este Comprobante de Salida?')){
        dataString   = "codigo="+guiasa;
        url          = base_url+"index.php/almacen/guiasa/eliminar";
            $.post(url,dataString,function(data){
                    location.href = base_url+"index.php/almacen/guiasa/listar";
            });
    }
}
function ver_guiasa(guiasa){
      location.href = base_url+"index.php/almacen/guiasa/ver/"+guiasa;
}
function ver_guiasa_pdf(guiasa){
     url = base_url+"index.php/almacen/guiasa/ver_pdf/"+guiasa;
     window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}
function atras_guiasa(){
     location.href = base_url+"index.php/almacen/guiasa/listar";
}
function valida_guiasa(){
    if($("#almacen").val()==""){
        alert("Seleccione un almacen.");
        $("#almacen").select();
        return false;
    }
    else if($("#GenInd").val()==""){
        alert("Debe ingresar los números de serie");
        return false;
    }
    else if($("#nombre_cliente").val()==""){
        alert("Ingrese un cliente");
        $("#verCliente").select();
        return false;
    }
    else if($("#tipo_movimiento").val()==""){
        alert("Ingrese el motivo del movimiento");
        $("#tipo_movimiento").select();
        return false;
    }
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
function busqueda_producto_x_almacen(){
    almacen_id = $("#almacen").val();
    if(almacen_id!=""){
        url        = base_url+"index.php/almacen/producto/ventana_busqueda_producto_x_almacen/"+almacen_id;
        window.open(url,"","width=600,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0");
    }
    else{
        alert("Debe seleccionar un almacen.");
    }
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
function agregar_producto_guiasa(){
    codproducto     = $("#codproducto").val();
    producto        = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    descuento       = $("#descuento").val();
    igv             = $("#igv").val();
    cantidad        = parseFloat($("#cantidad").val());
    stock           = parseFloat($("#stock").val());
    costo           = parseFloat($("#costo").val());
    unidad_medida   = $("#unidad_medida").val();//select
    select_umedida  = document.getElementById("unidad_medida");
    options_umedida = select_umedida.getElementsByTagName("option");
    nombre_unidad   = $("#nombre_unidad_medida").val();
    flagGenInd      = $("#flagGenInd").val();
    n = document.getElementById('tblDetalleOcompra').rows.length;
    j = n+1;
    if(j%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
    //if(nombre_producto!='' & stock>=cantidad)
    if(nombre_producto!='')
    {
        fila = '<tr class="'+clase+'">';
        fila+= '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_guiasa(this);">';
        fila+= '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
        fila+= '</a></strong></font></div></td>';
        fila+= '<td width="5%"><div align="center">1</div></td>';
        fila+= '<td width="10%"><div align="center">';
        fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
        fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
        fila+= '<input type="hidden" class="cajaMinima" name="flagGenIndDet['+n+']" id="flagGenIndDet['+n+']" value="'+flagGenInd+'">';
        fila+= '</div></td>';
        fila+= '<td width="66%"><div align="left">';
        fila+= '<input type="text" class="cajaSuperGrande" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'">';
        fila+= '</div></td>';
        fila+= '<td width="8%"><div align="center">';
        if(flagGenInd=="I"){
          fila+= '<a href="javascript:;" onclick="ventana_producto_serie2('+n+')"><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" border="0"/></a>';
          $("#GenInd").val('');
        }
        else{
           $("#GenInd").val('G');
        }
        fila+= '<input type="text" class="cajaPequena2" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onkeypress="return numbersonly(this,event,\'.\');">';
        fila+= '</div></td>';
        fila+= '<td width="8%"><div align="center">';
        fila+= nombre_unidad;
        fila+= '<input type="hidden" class="cajaMinima" name="detguiasa['+n+']" id="detguiasa['+n+']">';
        fila+= '<input type="hidden" class="cajaMinima" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
        fila+= '<input type="hidden" class="cajaPequena2" name="prodcosto['+n+']" id="prodcosto['+n+']" value="'+costo+'" readonly="readonly">';
        fila+= '<input type="hidden" class="cajaPequena2" name="prodventa['+n+']" id="prodventa['+n+']" value="0" readonly="readonly">';
        fila+= '<input type="hidden" class="cajaPequena2" name="prodpeso['+n+']" id="prodpeso['+n+']" value="0" readonly="readonly">';
        fila+= '</div></td>';
        fila+= '</tr>';
        $("#tblDetalleOcompra").append(fila);
        //Inicializo valores
        $("#producto").val('');
        $("#codproducto").val('');
        $("#nombre_producto").val('');
        $("#cantidad").val('0');
        $("#stock").val('0');
        $("#costo").val('0');
        $("#nombre_unidad").val('');
        $("#unidad_medida").val('0');
        $("#flagGenInd").val('');
        
        limpiar_combobox('unidad_medida');
    }
    else if(codproducto==''){
            $("#codproducto").focus();
            alert('Debe ingresar un producto.');
    }
    else if(unidad_medida=='0'){
         alert('Seleccione una unidad de medida.');
    }
    else if(cantidad>stock){
        $("#cantidad").focus();
        alert('No puede retirar mas de '+stock+' productos');
    }
    else{
         alert('No estan los datos completos');
    }
}
function eliminar_producto_guiasa(obj){
    if(confirm('Esta seguro que desea eliminar este producto?')){
        $(obj).parent().parent().parent().parent().parent().remove();
    }
}
function listar_unidad_medida_producto(producto){
    base_url   = $("#base_url").val();
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
                texto       = document.createTextNode(simbolo);
                opt.appendChild(texto);
                opt.value = codigo;
                if(i==0)
                    opt.selected=true;
                select_umedida.appendChild(opt);
          });
          var nombre;
          nombre=nombre_producto;
          if(marca)
            nombre+=' / Marca:'+marca;
          if(modelo)
            nombre+=' / Modelo: '+modelo;
          if(presentacion)
            nombre+=' / Prest: '+presentacion;  
           $("#nombre_producto").val(nombre);
    });
}