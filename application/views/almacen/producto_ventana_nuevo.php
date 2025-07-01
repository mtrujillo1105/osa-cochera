<link rel="stylesheet" href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css"/>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto_popup.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
jQuery(document).ready(function(){
    $("a#linkVerProducto, a#linkVerProveedor").fancybox({
            'width'          : 700,
            'height'         : 450,
            'autoScale'	 : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    }); 
    $("a#linkVerFamilia").fancybox({
            'width'          : 400,
            'height'         : 300,
            'autoScale'	 : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    });
	
	$('a#prodPrecios').click(function(){
        $('#general').hide();
        $('#datosPrecios').show();
        $('#datosProveedores').hide();
        $("#nuevoRegistroProv").hide();
        $('#datosOcompras').hide();
        $('#divBotones').show();
    });
	
	$('#prodProveedores').click(function(){
        $('#general').hide();
        $('#datosPrecios').hide();
        $('#datosProveedores').show();
        $("#nuevoRegistroProv").show();
        $('#datosOcompras').hide();
        $('#divBotones').show();
    });
	
	$('#prodGeneral').click(function(){        
        $('#general').show();
        $('#datosPrecios').hide();
        $('#datosProveedores').hide();
        $("#nuevoRegistroProv").hide();
        $('#datosOcompras').hide();
        $('#divBotones').show();
    });
	
	$("#nuevoRegistroProv").click(function(){
        $("#msgRegistros").hide();
        n = document.getElementById('tblProveedor').rows.length;
        fila  = "<tr>";
        fila += "<td align='center'>"+n+"</td>";
        fila += "<td align='left'><input type='text' name='ruc["+n+"]' id='ruc["+n+"]' class='cajaPequena cajaSoloLectura' readonly='readonly'></td>";
        fila += "<td align='left'>";
        fila += "<input type='hidden' name='proveedor["+n+"]' id='proveedor["+n+"]'>";        
        fila += "<input type='text' name='nombre_proveedor["+n+"]' id='nombre_proveedor["+n+"]' class='cajaMedia cajaSoloLectura'>";
        fila += "<a href='javascript:;' onclick='buscar_proveedor("+n+");'>&nbsp;<img height='16' width='16' border='0' title='Agregar Proveedor' src='"+base_url+"images/ver.png?=<?=IMG;?>'></a>";
        fila += "</td>";
        fila += "<td align='left'><input type='text' name='direccion["+n+"]' id='direccion["+n+"]' class='cajaMedia cajaSoloLectura' readonly='readonly'></td>";
	fila += "<td align='center'><a href='#' onclick='eliminar_productoproveedor("+n+");'><img src='"+base_url+"images/delete.gif?=<?=IMG;?>' border='0'></a></td>";
        $("#tblProveedor").append(fila);
    });
	
	$("a.limpiarPrecios").click(function(){
          $(this).parents("tr").find("input").val('');
     });
        
    $("#nombre_producto").focus();
    modo = $("#modo").val();
    tipo = $("#tipo").val();
    if(modo=='insertar'){
        $("#nombres").val('&nbsp;');
        $("#paterno").val('&nbsp;');
        $("#ruc").focus();
        $("#cboSexo").val('0');
    }
    else if(modo=='modificar'){
        if(tipo=='0'){
            $("#ruc").val('11111111111');
        }
        else if(tipo=='1'){
            $("#nombres").val('&nbsp;');
            $("#paterno").val('&nbsp;');
            $("#cboSexo").val('0');
        }
    }
	
	if($("#tiene_padre").attr('checked')==true && $("#padre").val()==""){
        alert('Debe seleccionar un producto.');
        $("#linkVerProducto").focus();
        return false;
    }
	
	$('#tiene_padre').click(function(){
        if(this.checked==true)
            $('#lblTienePadre').fadeIn('slow');
        else
            $('#lblTienePadre').fadeOut('slow', function(){$('#padre, #codpadre, #nompadre').val('');});    
     });
	
});
function cargar_familia(familia,nombre,codfamilia){
    $("#codigo_familia_aux").val(codfamilia);
    $("#codigo_familia").val(codfamilia);
    document.getElementById('familia').value = familia;
    document.getElementById('nombre_familia').value = nombre;
    $("#nombre_producto").focus();
}

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

function seleccionar_proveedor(codigo,ruc,razon_social){
    fila = $("#fila").val();
    a    = "proveedor["+fila+"]";
    b    = "nombre_proveedor["+fila+"]";
    c    = "ruc["+fila+"]";
    d    = "distrito["+fila+"]";
    e    = "direccion["+fila+"]";
    url  = base_url + "index.php/empresa/proveedor/obtener_proveedor/"+codigo;
    $.getJSON(url,function(data){
        proveedor        = data.proveedor;
        nombre_proveedor = data.nombre;
        ruc              = data.ruc;
        direccion        = data.direccion;
        distrito         = data.distrito;
        document.getElementById(a).value = proveedor;
        document.getElementById(b).value = nombre_proveedor;
        document.getElementById(c).value = ruc;
        document.getElementById(d).value = distrito;
        document.getElementById(e).value = direccion;
    });
}
function buscar_proveedor(n){
    $("#fila").val(n);
    base_url = $("#base_url").val();
    $('#linkVerProveedor').click();
}
function seleccionar_producto(producto,cod_interno,nombre_familia,stock,costo){
     $("#padre").val(producto);
     $("#codpadre").val(cod_interno);
     obtener_nombre_producto(producto);    
}
function obtener_nombre_producto(producto){
        url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
	$.getJSON(url,function(data){
		  $.each(data, function(i,item){
                      $("#nompadre").val(item.PROD_Nombre);
		  });
                  
	});
}
</script>
<br>
<form id="frmProducto" name="frmProducto" method="post" enctype="multipart/form-data" action="<?php echo $url_action;?>/true" onsubmit="return valida_producto();">
    <div id="pagina">
    <div id="zonaContenido">
            <div align="center">
                <div id="tituloForm" class="header" style="width: 620px !important;"><?php echo $titulo;?></div>
                <div id="divProducto" style="width: 620px !important;">
                    <?php echo validation_errors("<div class='error'>",'</div>');?>
                    <div id="container" class="container">
                        <h4>Primero debe completar los siguientes campos antes de enviar.</h4>
                        <ol>
                            <li><label for="descripcion_producto" class="descripcion_producto">Por favor ingrese la descripciopn de un producto</label></li>
                        </ol>
                    </div>
                    <?php if(isset($flagGuardado) && $flagGuardado==true) echo '<div class="mensaje_grabar"><img src="'.base_url().'images/icono_aprobar.png?=<?=IMG;?>" width="18" height="15" border=0 alt="Ok" /> Los datos del artículo se guardaron correctamente</div>'; ?>
                    <div align="left" class="fuente8" style="float:left;height:25px;margin-top:7px;margin-left: 15px;width: 450px;">
                        <a href="javascript:;" id="prodGeneral">General&nbsp;&nbsp;&nbsp;|</a>
                        <?php if($flagBS=='B'){ ?>
                        <a href="javascript:;" id="prodProveedores">&nbsp;Proveedores&nbsp;&nbsp;&nbsp;|</a>&nbsp;
                        <span id="spanPrecio"><?php if($tabla_precios=='') echo '<span title="Guarde los cambios primero para ingresar los precios">&nbsp;Precios</span>'; else  echo '<a href="javascript:;" id="prodPrecios">&nbsp;Precios</a>'; ?></span>
                        <?php } ?>
                    </div>
                    <div id="nuevoRegistroProv" style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;">
                        <input type="hidden" name="fila" id="fila" value="<?php echo count($lista_proveedores);?>" />
                        <a href="#">Nuevo <image src="<?=$base_url;?>public/images/icons/add.png?=<?=IMG;?>" name="agregarFila" id="agregarFila" border="0" alt="Agregar"></a>
                    </div>
                    <div id="general" style="float:left;width:98%; text-align: left;">
                        <div style="width:100%">
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                              <tr>
                                <td width="16%">Familia</td>
                                <td width="29%">
                                    <input type="hidden" class="cajaPequena" name="familia" id="familia" value="<?php echo $familia;?>" />
                                    <input type="hidden" id="codigo_familia" name="codigo_familia" value="<?php echo $codigo_familia;?>" />
                                    <input type="text" <?php echo $readonly;?> class="cajaMedia cajaSoloLectura" name="nombre_familia" id="nombre_familia" readonly="readonly" value="<?php echo $nombre_familia;?>" />
                                    <a href="<?=$base_url;?>index.php/almacen/familia/nueva_familia/<?php echo $flagBS; ?>" id="linkVerFamilia"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                </td>
                                <td width="16%">C&oacute;digo Interno</td>
                                <td width="29%">
                                    <input type="hidden" class="cajaPequena2" name="producto" id="producto" readonly="readonly" value="<?php echo $producto;?>">
                                    <input type="text" id="codigo_familia_aux" class="cajaMedia  cajaSoloLectura" style="width:60px;" readonly="readonly" name="codigo_familia_aux" value="<?php echo $codigo_familia;?>">
                                    <input type="text" class="cajaMedia cajaSoloLectura" style="width:60px;" name="codigo_producto" id="codigo_producto" readonly="readonly" value="<?php $temp=explode(".", $codigo_producto); echo $temp[count($temp)-1];?>">                                                                          
                                </td>
                              </tr>
                              <tr>
                                <td height="30">Tiene P. Sup.</td>
                                <td>
                                    <input type="checkbox" name="tiene_padre" id="tiene_padre" value="1" <?php if($padre!='' && $padre!='0') echo 'checked="checked"'; ?> />
                                    <label id="lblTienePadre" <?php if($padre=='') echo 'style="display:none"'; ?>>
                                    <input type="hidden" name="padre" id="padre" value="<?php echo $padre; ?>" />
                                    <input type="text" name="codpadre" id="codpadre" class="cajaPequena cajaSoloLectura" readonly="readonly" value="<?php echo $codpadre; ?>" />
                                    <input type="text" name="nompadre" id="nompadre" class="cajaMedia cajaSoloLectura" readonly="readonly" style="width:215px;"  value="<?php echo $nompadre; ?>" />
                                    <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </label>
                                </td>
                                <td>C&oacute;digo de Usuario</td>
                                <td>
                                    <input type="text" class="cajaMedia" name="codigo_usuario" style="width:125px;"id="codigo_usuario" value="<?php echo $codigo_usuario; ?>" />   
                                </td>
                              </tr>
                              <tr>
                                <td>Nombre(*)</td>
                                <td><input type="text" class="cajaGrande" name="nombre_producto" id="nombre_producto" style="width:200px" onblur="" value="<?php echo str_replace('"', "''", $nombre_producto);?>"></td>
                                <td><?php if($flagBS=='B'){ ?>Marca<?php } ?></td>
                                <td><?php if($flagBS=='B'){ ?><?php echo $cbo_marca;?><?php } ?></td>
                              </tr>
                              <tr>
                                <td>N. Corto</td>
                                <td><input type="text" class="cajaGrande" name="nombrecorto_producto" id="nombrecorto_producto" style="width:200px" value="<?php echo $nombrecorto_producto;?>"></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <?php if($flagBS=='B'){ ?>
                              <tr>
                                <td>Fabricante</td>
                                <td><?php echo $cbo_fabricante;?></td>
                                <td>Modelo</td>
                                <td><input type="text" class="cajaMedia" name="modelo" id="modelo" value="<?php echo $modelo;?>"></td>
                              </tr>
                              <tr>
                                <td>G. / I.</td>
                                <td><select name="geneindi" id="geneindi" class="comboMedio">
                                        <option value="0">::Seleccionar::</option>    
                                        <option value="G" <?php if($geneindi=='G') echo "selected='selected'";?>>Genérico (Sin N/S)</option>
                                        <option value="I" <?php if($geneindi=='I') echo "selected='selected'";?>>Individual (Con N/S)</option>
                                    </select></td>
                                <td>Presentación</td>
                                <td><input type="text" class="cajaMedia" name="presentacion" id="presentacion" value="<?php echo $presentacion;?>"></td>
                              </tr>
                              <tr>
                                <td>L&iacute;nea</td>
                                <td colspan="3"><?php echo $cbo_linea;?></td>
                              </tr>
                              <tr>
                                <td colspan="4" align="left" valign="top">
                                    <div id="divUnidades"><?php echo $filaunidad;?></div>
                                </td>
                              </tr>
                              <?php } ?>
                            </table>
                        </div>
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table>
                                    <tr>
                                        <td>
                                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                              <tr>
                                                <td width="16%">Tipo <?php if($flagBS=='B') echo 'Artículo'; else echo 'Servicio'; ?></td>
                                                <td>
                                                     <select name="tipo_producto" id="tipo_producto" class="comboMedio" onChange="mostrar_atributos();"><?php echo $cbo_tipoProducto;?></select>
                                                     <input type="hidden" name="factor[0]" id="factor[0]" value="1">
                                                     <input type="hidden" name="flagPrincipal[0]" id="flagPrincipal[0]" value="1">
                                                </td>
                                              </tr>
                                            </table>
                                        </td>
                                        <td>
                                           <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                                <tr>
                                                    <td width="16%" valign="top">Estado</td>
                                                    <td valign="top">
                                                        <select name="activo" id="activo" class="comboMedio">
                                                            <option value="1" <?php if($flagActivo=='1') echo "selected='selected'";?>>Activo</option>
                                                            <option value="0" <?php if($flagActivo=='0') echo "selected='selected'";?>>Inactivo</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table> 
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="divAtributos"><?php echo $fila;?></div>
                        </div>
                    </div>
                    <div id="datosPrecios" style="float:left; display:none;width:100%;">
                        <?php 
                        echo $tabla_precios;
                        ?>
                    </div>
                    <a href="<?=$base_url;?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id='linkVerProveedor'></a>
                    <div id="datosProveedores" style="float:left; display:none;width:100%;">
                        <table id="tblProveedor" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
                            <tr align="center" bgcolor="#BBBB20" height="10px;">
                                <td>Nro</td>
                                <td>RUC</td>
                                <td>Nombre Proveedor</td>
                                <td>Domicilio</td>
                                <td>Borrar</td>
                            </tr>
                            <?php
                            $kk=1;
                            $cantidad2 = count($lista_proveedores);
                            if($cantidad2>0){
                                foreach($lista_proveedores as $indice=>$valor){
                                $kk=$indice+1;
                                ?>
                                <tr bgcolor="#ffffff">
                                    <td align="center">
                                        <?php echo $kk;?>
                                        <input type="hidden" name="productoproveedor[<?php echo $indice;?>]" id="productoproveedor[<?php echo $indice;?>]" value="<?php echo $valor->prodproveedor;?>" />
                                        <input type="hidden" name="proveedor[<?php echo $indice;?>]" id="proveedor[<?php echo $indice;?>]" value="<?php echo $valor->proveedor;?>" />
                                    </td>
                                    <td align="left"><input type="text" name="ruc[<?php echo $indice;?>]" id="ruc[<?php echo $indice;?>]" class="cajaPequena cajaSoloLectura" readonly="readonly" value="<?php echo $valor->ruc;?>" /></td>
                                    <td align="left"><input type="text" name="nombre_proveedor[<?php echo $indice;?>]" id="nombre_proveedor[<?php echo $indice;?>]" class="cajaMedia cajaSoloLectura" readonly='readonly'" value="<?php echo $valor->nombre_proveedor;?>" readonly="readonly" /></td>
                                    <td align="left"><input type="text" name="direccion[<?php echo $indice;?>]" id="direccion[<?php echo $indice;?>]" class="cajaMedia cajaSoloLectura" readonly="readonly" value="<?php echo $valor->direccion;?>" /></td>
                                    <td align="center"><a href="#" onclick="eliminar_productoproveedor(<?php echo $indice;?>);"><img src="<?=$base_url;?>public/images/icons/delete.gif?=<?=IMG;?>" border="0" /></a></td>
                                </tr>
                                <?php
                                $kk++;
                                }
                            }
                            ?>
                        </table>
                        <?php
                        $displaySucursal = $cantidad2!='0'?"display:none;":"";
                        ?>
                        <div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displaySucursal;?>">NO EXISTEN REGISTROS</div>
                    </div>
                    <div id="datosOcompras" style="float:left; display:none;width:100%;"></div>
                </div>
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                    <a href="javascript:;" id="imgGuardarProducto"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgLimpiarProducto"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgCancelarProducto"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <?php echo $oculto;?>
                </div>
            </div>
        </div>
    </div>
</form>