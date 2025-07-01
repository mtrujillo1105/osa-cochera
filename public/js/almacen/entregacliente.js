var base_url;
jQuery(document).ready(function(){
    base_url  = $('#base_url').val();
    flagBS  = $('#flagBS').val();
    $("#nuevoRecepcionproveedor").click(function(){
        url = base_url+"index.php/almacen/recepcionproveedor/nuevo";
        location.href = url;
    });
    $("#buscarProducto").click(function(){
        $("#form_busqueda").submit();
    });	
    $("#limpiarProducto").click(function(){
        url = base_url+"index.php/almacen/recepcionproveedor/listar";
        location.href=url;
    });
    $("#imgGuardarEntregacliente").click(function(){
        $("#frmEntregacliente").submit();
    });
     $("#imgLimpiarEntregacliente").click(function(){
        $("#frmEntregacliente").each(function(){
            this.reset();
        });
    });
    $("#imgCancelarEntregacliente").click(function(){
        url = base_url+"index.php/almacen/garantia/listar";
        location.href = url;
    });
    $('#prodGeneral').click(function(){        
        $('#general').show();
        $('#datosPrecios').hide();
        $('#datosProveedores').hide();
        $("#nuevoRegistroProv").hide();
        $('#datosOcompras').hide();
        $('#divBotones').show();
        $('#publicacionweb').hide();
    });
    $('#prodPrecios').click(function(){
        $('#general').hide();
        $('#datosPrecios').show();
        $('#datosProveedores').hide();
        $("#nuevoRegistroProv").hide();
        $('#datosOcompras').hide();
        $('#divBotones').show();
        $('#publicacionweb').hide();
    });
    $('#prodProveedores').click(function(){
        $('#general').hide();
        $('#datosPrecios').hide();
        $('#datosProveedores').show();
        $("#nuevoRegistroProv").show();
        $('#datosOcompras').hide();
        $('#divBotones').show();
        $('#publicacionweb').hide();
    });
    $('#prodpublicaionweb').click(function(){
        $('#general').hide();        
        $('#datosPrecios').hide();
        $('#datosProveedores').hide();        
        $("#nuevoRegistroProv").hide();
        $('#datosOcompras').hide();
        $('#divBotones').show();
        $('#publicacionweb').show();
    });
    $('#prodCompras').click(function(){
        producto = $("#producto").val();
        //alert('Corregir cuando sale esto');
        if(producto!=''){
            $('#general').hide();
            $('#datosPrecios').hide();
            $('#datosProveedores').hide();
            $("#nuevoRegistroProv").hide();
            $('#datosOcompras').show();
            $('#divBotones').hide();
            $('#publicacionweb').hide();
            url = base_url+"index.php/almacen/producto/listar_lotes_producto/"+producto;
            $.post(url,'',function(data){
                $('#datosOcompras').html(data);
            });
        }
    });
    $("#nuevoRegistroProv").click(function(){
        $("#msgRegistros").hide();
        n = document.getElementById('tblProveedor').rows.length;
        fila  = "<tr>";
        fila += "<td align='center'>"+n+"</td>";
        fila += "<td align='left'><input type='text' name='ruc["+n+"]' id='ruc["+n+"]' class='cajaPequena cajaSoloLectura' readonly='readonly'></td>";
        fila += "<td align='left'>";
        fila += "<input type='hidden' name='proveedor["+n+"]' id='proveedor["+n+"]'>";        
        fila += "<input type='text' name='nombre_proveedor["+n+"]' id='nombre_proveedor["+n+"]' class='cajaGrande cajaSoloLectura'>";
        fila += "<a href='javascript:;' onclick='buscar_proveedor("+n+");'>&nbsp;<img height='16' width='16' border='0' title='Agregar Proveedor' src='"+base_url+"images/ver.png'></a>";
        fila += "</td>";
        fila += "<td align='left'><input type='text' name='direccion["+n+"]' id='direccion["+n+"]' class='cajaGrande cajaSoloLectura' readonly='readonly'></td>";
        fila += "<td align='left'><input type='text' name='distrito["+n+"]' id='distrito["+n+"]' class='cajaMedia cajaSoloLectura' readonly='readonly'></td>";        
	fila += "<td align='center'><a href='#' onclick='eliminar_productoproveedor("+n+");'><img src='"+base_url+"images/delete.gif' border='0'></a></td>";
        $("#tblProveedor").append(fila);
    });
     $("a.limpiarPrecios").click(function(){
          $(this).parents("tr").find("input").val('');

     });
     $('#tiene_padre').click(function(){
        if(this.checked==true)
            $('#lblTienePadre').fadeIn('slow');
        else
            $('#lblTienePadre').fadeOut('slow', function(){$('#padre, #codpadre, #nompadre').val('');});           
     });
     $('#publicarProducto').click(function(){
        var num=$('input[type="checkbox"][name^="producto"]:checked').length
        if(num==0){
            alert('Debe seleccionar al menos un producto.')
            return false;
        }
        var productos='';
        $.each($('input[type="checkbox"][name^="producto"]:checked'), function(i,item){
            productos+=$(this).val()+'-';
        });
        $('a#linkPublicar').attr('href', base_url+'index.php/almacen/producto/publicar_producto/'+productos).click();
        return true;
     });
     $('#txtCodigo, #txtNombre, #txtFamilia, #txtMarca').keyup(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#form_busqueda").submit();
        } 
    });
});
function mostrar_atributos(){
    var base_url  = $('#base_url').val();
    tipo_producto = $("#tipo_producto").val();
    dataString    = "";
    url           = base_url+"index.php/almacen/producto/mostrar_atributos/"+tipo_producto;
    if(tipo_producto!=''){
        $.post(url,dataString,function(data){
                $("#divAtributos").html(data);
        });
    }
    else{
        $("#divAtributos").html("");
    }
}
function valida_atributo(n){
    a = "tipo_atributo["+n+"]";
    b = "nombre_atributo["+n+"]";
    tipo_atributo   = document.getElementById(a).value;
    nombre_atributo = document.getElementById(b).value;
    if(nombre_atributo!=''){
        if(("0123456789.").indexOf(nombre_atributo) > -1){
                tipo = 1;//Numerico
                nombre_tipo = "Numerico";
        }
        else if(("ABCDEFGHIJKLMN?OPQRSTUVWXYZabcdefghijklmn?opqrstuvwxyz ").indexOf(nombre_atributo) > -1){
                tipo = 3;//String
                nombre_tipo = "String";
        }
        else{//Date
                tipo = 2;//Fecha
                nombre_tipo = "Date";
        }
        if(tipo!=tipo_atributo){
                alert('Favor ingrese un tipo de dato '+nombre_tipo);
        }
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
function valida_nombre_producto(){
    nombre_producto = $("#nombre_producto").val();
    producto        = $("#codigo").val();
    nombre_producto = $("#nombre_producto").val();
    url = base_url+"index.php/almacen/producto/obtener_producto_x_nombre/"+nombre_producto;
    if(nombre_producto!=""){
        $.post(url,'',function(data){
            if(data>0 && producto==""){
                alert('Este producto ya se encuentra registrado.');
                $("#nombre_producto").select();
            }
        });
    }
}
function editar_producto(producto){
	var base_url = $("#base_url").val();
	url           = base_url+"index.php/almacen/producto/editar_producto/"+producto;
	location.href = url;
}
function ver_recepcionproveedor(producto){
var base_url = $("#base_url").val();
  location.href = base_url+"index.php/almacen/recepcionproveedor/ver/"+producto;
}
function atras_recepcionproveedor(){
   url = base_url+"index.php/almacen/recepcionproveedor/listar";
   location.href = url;
}

function eliminar_entregacliente(cod){
    if(confirm('Esta seguro desea eliminar esta recepcion?')){
		dataString = "cod="+cod;
		url = base_url+"index.php/almacen/entregacliente/eliminar_entregacliente";
		$.post(url,dataString,function(data){
			url = base_url+"index.php/almacen/entregacliente/listar";
			location.href = url;
		});
	}
}
function agregar_unidad_producto(){
	n = (document.getElementById('tblUnidadMedida').rows.length);	
	a = "factor["+n+"]";
	fila  = '<tr>';	
    fila += '<td width="16%">Unidad medida Aux. '+n+'</td>';
    fila += '<td width="19%">';
	fila += '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="">';	
	fila += '<select name="unidad_medida['+n+']" id="unidad_medida['+n+']" class="comboMedio"><option value="" selected="selected">::Seleccione::</option></select>&nbsp;</td>';
	fila += '<td width="10%">F.C.<input type="text" class="cajaPequena2" onkeypress="return numbersonly(this,event,\'.\');" maxlength="5" name="factor['+n+']" id="factor['+n+']"></td>';
	fila += '<td width="54%"><input type="hidden" class="cajaPequena2" name="flagPrincipal['+n+']" id="flagPrincipal['+n+']" value="0"></td>';
	fila += '</tr>';
	$("#tblUnidadMedida").append(fila);
        $('#spanPrecio').html(' <span title="Guarde los cambios primero para ingresar los precios">&nbsp;Precios</span>');
	listar_unidad_medida(n);
}
function listar_unidad_medida(n){
    var base_url = $("#base_url").val();
    a      = "unidad_medida["+n+"]";
    url    = base_url+"index.php/almacen/producto/listar_unidad_medida/";
    select = document.getElementById(a);
    $.getJSON(url,function(data){
          $.each(data, function(i,item){
                codigo      = item.UNDMED_Codigo;
                descripcion = item.UNDMED_Descripcion;
                simbolo     = item.UNDMED_Simbolo;
                opt         = document.createElement('option');
                texto       = document.createTextNode(descripcion);
                opt.appendChild(texto);
                opt.value = codigo;
                select.appendChild(opt);
          });
    });
}
function listar_unidad_medida_producto1(producto){
	base_url   = $("#base_url").val();
	url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
	select   = document.getElementById('unidad_medida');
    //option  = document.getElementById('option');
    //padre = option[0].parentNode;
    //padre.removeChild(option[0]);
	$.getJSON(url,function(data){
		  $.each(data, function(i,item){
			codigo            = item.UNDMED_Codigo;
			alert(codigo);
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
function listar_unidad_medida_producto(producto){
    base_url   = $("#base_url").val();
    url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
    select   = document.getElementById('unidad_medida');
    //option  = document.getElementById('option');
    //padre = option[0].parentNode;
    //padre.removeChild(option[0]);
    $.getJSON(url,function(data){
      $.each(data, function(i,item){
        codigo            = item.UNDMED_Codigo;
        descripcion  = item.UNDMED_Descripcion;
        simbolo         = item.UNDMED_Simbolo;
        nombre_producto = item.PROD_Nombre;
        opt         = document.createElement('option');
        texto       = document.createTextNode(simbolo);
        opt.appendChild(texto);
        opt.value = codigo;
        if(i==0)
            opt.selected=true;
        select.appendChild(opt);
      });
      $("#nombre_producto").val(nombre_producto);
    });
}
function obtener_producto_unidad(producto){
	url          = base_url+"index.php/almacen/producto/obtener_producto_unidad/"+producto;
	$.getJSON(url,function(data){
		alert(data);
	});
}
