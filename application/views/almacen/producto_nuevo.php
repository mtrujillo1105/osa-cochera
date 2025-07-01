<script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
    jQuery(document).ready(function(){
        $("a#linkVerProveedor").fancybox({
            'width'          : 700,
            'height'         : 450,
            'autoScale'	 : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
        }); 
        $("a#linkVerProducto").fancybox({
            'width'          : 800,
            'height'         : 600,
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
    });

    $(function(){
        $("#idSunat").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/almacen/producto/autocompleteIdSunat/",
                    type: "POST",
                    data: {
                        term: $("#idSunat").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response( $.map(data, function(item) {
                                return {
                                    label: item.descripcion,
                                    value: item.descripcion,
                                    idsunat: item.idsunat
                                }})
                            );
                    }
                });
            },
            select: function (event, ui) {
                        console.log(ui.item);
                $("#codigo_original").val(ui.item.idsunat);
            },
            minLength: 2
        });

        $("#descripcion_producto").keyup(function(){
            var descripcion = $("#descripcion_producto").val().length;

            longitud = 800 - descripcion;
            $("#contadorCaracteres").html(longitud);
        });
    });

    function cargar_familia(familia,nombre,codfamilia){
        $("#codigo_familia_aux").val(codfamilia);
        $("#codigo_familia").val(codfamilia);
        document.getElementById('familia').value = familia;
        document.getElementById('nombre_familia').value = nombre;
        $("#nombre_producto").focus();
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
<form id="frmProducto" name="frmProducto" method="post" enctype="multipart/form-data" action="<?php echo $url_action; ?>" onsubmit="return valida_producto();">
    <div id="pagina">
        <div id="zonaContenido">
            <div align="center" style='position: relative;width:935px;margin:0 auto;'>
                <?php if($flagBS=='B'){ ?>
                <div style='position: absolute;right:0;top: -16px'><a href="<?php echo base_url('')?>index.php/almacen/producto/productos/B" ><img src="<?php echo base_url(); ?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a></div>
                <?php }  else {?>
                   <div style='position: absolute;right:0;top: -16px'><a href="<?php echo base_url('')?>index.php/almacen/producto/productos/S" ><img src="<?php echo base_url(); ?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a></div> 
                <?php
               } ?>

                <div style='position: absolute;right:100px;top: -16px'>
                    <a href="<?php echo base_url('')?>index.php/almacen/producto/nuevo_producto/<?php echo $flagBS ?>" ><img src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                </div>

                <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
                
                <div id="divProducto">
                    <?php echo validation_errors("<div class='error'>", '</div>'); ?>
                    <div id="container" class="container">
                        <h4>Primero debe completar los siguientes campos antes de enviar.</h4>
                        <ol>
                            <li><label for="descripcion_producto" class="descripcion_producto">Por favor ingrese la descripciopn de un producto</label></li>
                        </ol>                        
                    </div>
                    
                    <?php if (isset($flagGuardado) && $flagGuardado == true) echo '<div class="mensaje_grabar"><img src="' . base_url() . 'images/icono_aprobar.png?='.IMG.'" width="18" height="15" border=0 alt="Ok" /> Los datos del artículo se guardaron correctamente</div>'; ?>
                    <?php if (isset($flagError) && $flagError == 1) echo '<div class="mensaje_no_grabar"><img src="' . base_url() . 'images/icono_desaprobar.png?='.IMG.'" width="18" height="15" border=0 alt="Ok" /> Los datos del artículo NO se guardaron correctamente</div>'; ?>
                    <div align="left" class="fuente8" style="float:left;height:25px;margin-top:7px;margin-left: 15px;width: 470px;">
                        <a href="javascript:;" id="prodGeneral" style="font-weight: bold;">General</a>
                        <?php if ($flagBS == 'B') { ?>
                            <span class="aTab" style="display: none"><a href="javascript:;" id="prodProveedores">Proveedores</a> </span>
                            <span class="aTab" style="display: none"><a href="javascript:;" id="prodCompras">Compras</a> </span>
                            <span class="aTab" style="display: none"><a href="javascript:;" id="prodKardex">Kardex</a> </span>
                        <?php } ?>
                        <span class="aTab">
                            <span id="spanPrecio">
                                <?=($tabla_precios == '') ? '<span title="Guarde los cambios primero.">&nbsp;Precios</span>' : '<a href="javascript:;" id="prodPrecios">&nbsp;Precios</a>'; ?>
                            </span>
                        </span>
                    </div>
                    <div id="nuevoRegistroProv" style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;">
                        <input type="hidden" name="fila" id="fila" value="<?php echo count($lista_proveedores); ?>" />
                        <a href="#">Nuevo <image src="<?php echo base_url(); ?>public/images/icons/add.png?=<?=IMG;?>" name="agregarFila" id="agregarFila" border="0" alt="Agregar"></a>
                    </div>
                    <div id="general" style="float:left;width:98%; text-align: left;">
                        <div style="width:100%">
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                <tr>
                                    <td width="16%">Familia</td>
                                    <td>
                                        <input type="hidden" class="cajaPequena" name="familia" id="familia" value="<?php echo $familia; ?>" />
                                        <input type="hidden" id="codigo_familia" name="codigo_familia" value="<?php echo $codigo_familia; ?>" />
                                        <input type="text" <?php echo $readonly; ?> class="cajaGrande cajaSoloLectura" name="nombre_familia" id="nombre_familia" required="required" readonly="readonly" value="<?php echo $nombre_familia; ?>" />
                                        <a href="<?php echo base_url(); ?>index.php/almacen/familia/nueva_familia/<?php echo $flagBS; ?>/" id="linkVerFamilia"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                    <td colspan="2">Tipo de Afectación &nbsp;&nbsp;&nbsp;
                                        <select id="afectacion" name="afectacion" class="comboGrande"><?=$cboTipoAfectacion;?></select>
                                    </td>
                                        <div style="display: none">
                                            <input type="hidden" class="cajaPequena2" name="producto" id="producto" readonly="readonly" value="<?php echo $producto; ?>">
                                            <input type="text" id="codigo_familia_aux" class="cajaMedia  cajaSoloLectura" style="width:60px;" readonly="readonly" name="codigo_familia_aux" value="<?php echo $codigo_familia; ?>">
                                            <input type="text" class="cajaMedia cajaSoloLectura" style="width:60px;" name="codigo_producto" id="codigo_producto" readonly="readonly" value="<?php
                                            $temp = explode(".", $codigo_producto);
                                            echo $temp[count($temp) - 1];
                                            ?>">
                                        </div>
                                </tr>
                                <tr>
                                    <td>Código del Articulo</td>
                                    <td>
                                        <input type="text" class="cajaMedia" name="codigo_usuario" style="width:125px;" id="codigo_usuario" onchange="valida_codigo();" value="<?php echo $codigo_usuario; ?>" />   
                                    </td>
                                    <td>Código Sunat</td>
                                    <td>
                                        <input type="text" class="cajaPequena cajaSoloLectura" name="codigo_original" id="codigo_original" value="<?=$codigo_original;?>" readonly/>
                                        <input type="text" class="cajaGrande" name="idSunat" id="idSunat" autocomplete="off">
                                    </td>
                                </tr>
                                <?php if($flagBS == "B"): ?>
                                <tr hidden>
                                   <td>Partida Arancelaria</td>
                                   <td><input type="text" name="partida-arancelaria" id="partida-arancelaria" class="cajaGrande" value="<?php echo $partida_arancelaria ?>"></td>
                                </tr>
                                <?php endif; ?>

                                <tr>
                                    <td colspan="4" style="color: red"><label id="persona_msg" name="persona_msg"></label></td>
                                </tr>
                                <tr>
                                    <td>Nombre <?php
                                        if ($flagBS == 'B')
                                            echo 'Artículo';
                                        else
                                            echo 'Servicio';
                                        ?> (*)</td>
                                    <td><input type="text" class="cajaGrande" name="nombre_producto" maxlength="130" id="nombre_producto" required="required" style="width:320px"   value="<?php echo str_replace('"', "''", $nombre_producto); ?>" onblur="buscar_productoint();"></td>
                                    <td><?php
                                        if ($flagBS == 'B')
                                            echo 'Marca';
                                        else
                                            echo '&nbsp;';
                                        ?></td>
                                    <td><?php
                                        if ($flagBS == 'B')
                                            echo $cbo_marca;
                                        else
                                            echo '&nbsp;'
                                            ?></td>
                                </tr>
                                <?php if ($flagBS == 'B') { ?>
                                    <tr>
                                        <td>Fabricante</td>
                                        <td><?php echo $cbo_fabricante; ?></td>
                                        <td style="display: none">Modelo</td> <?php # LOS DATOS DEL MODELO SON UTILIZADOS EN PRODUCCION, ASI DIFERENCIA ENTRE ARTICULOS E INSUMOS. ?>
                                        <td style="display: none">
                                            <select class="cajaMedia" name="modelo" id="modelo">
                                                <option value="ARTICULO" <?=($modelo == 'ARTICULO') ? 'selected' : '';?>>ARTICULO</option>
                                                <option value="INSUMO" <?=($modelo == 'INSUMO') ? 'selected' : '';?>>INSUMO</option>
                                            </select>
                                            <!--<input type="text" class="cajaMedia" name="modelo" id="modelo" value="<?=$modelo;?>">-->
                                        </td>
                                    </tr>
                                    <tr style="display: none;">
                                      
                                        <td>Presentación</td>
                                        <td ><input type="text" class="cajaMedia" name="presentacion" id="presentacion" value="<?php echo $presentacion; ?>"></td>
                                    </tr>    


                                    <tr style="display: none;">
                                        <td valign="top">Especificación Técnica</td>
                                        <td>
                                            <input name="pdf" id="pdf" style="font-size:0.9em;" type="file" />
                                            <?php if ($especificacionPDF != '') 
                                                echo '<br /><a href="' . base_url() . 'pdf/' . $especificacionPDF . '" target="_blank"><img src="' . base_url() . 'images/pdf.png?=<?=IMG;?>" alt="Descargar PDF" width="15" height="15" border="0" /> Descargar PDF</a>' ?>
                                        </td>
                                    </tr>

                                <?php } ?>
                                <tr>
                                    <?php if($flagBS == 'B'){?>
                                    <td>Stock Mínimo</td>
                                    <td><input type="text" class="cajaMedia" name="stock_min" id="stock_min" value="<?php echo $stock_min; ?>"/></td>
                                    <?php }?>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td valign="top">Descripción</td>
                                    <td colspan="3"><textarea rows="8" cols="76" class="cajaTextArea" maxlength="800" name="descripcion_producto" id="descripcion_producto"><?php echo $descripcion_breve; ?></textarea><br>
                                        <div>Caracteres restantes: <span id="contadorCaracteres"id="contadorCaracteres" name="contadorCaracteres">800</span> </div> </td>
                                    <td style="display: none" valign="top"><?php
                                        if ($flagBS == 'B')
                                            echo 'Imagen';
                                        else
                                            echo '&nbsp;';
                                        ?></td>
                                    <td style="display: none" valign="top">
                                        <?php if ($flagBS == 'B') { ?>
                                            <input name="imagen" id="imagen" style="font-size:0.9em" type="file" />
                                            <?php if ($imagen != '') echo '<img style="margin-top:10px;" src="' . base_url() . '/images/img_db/' . $imagen . '" alt="' . $imagen . '" width="120" height="120" border="1" />' ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="left" valign="top">
                                        <div id="divUnidades"><?php echo $filaunidad; ?></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <tr>
                                        <td width="16%">Tipo <?php
                                            if ($flagBS == 'B')
                                                echo 'Artículo';
                                            else
                                                echo 'Servicio';
                                            ?></td>
                                        <td>
                                            <select name="tipo_producto" id="tipo_producto" class="comboMedio" onChange="mostrar_atributos();"><?php echo $cbo_tipoProducto; ?></select>
                                            <input type="hidden" name="factor[0]" id="factor[0]" value="1">
                                            <input type="hidden" name="flagPrincipal[0]" id="flagPrincipal[0]" value="1">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="divAtributos"><?php echo $fila; ?></div>
                        </div>
                        <div style="width:100%;"><hr width="98%"></div>
                        <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                            <tr>
                                <td width="16%" valign="top">Comentario</td>
                                <td width="42%"><textarea name="comentario" id="comentario" rows="4" cols="56" class="cajaTextArea" ><?php echo $comentario; ?></textarea></td>
                                <td width="16%" valign="top">Estado</td>
                                <td width="42%" valign="top">
                                    <select name="activo" id="activo" class="comboMedio">
                                        <option value="1" <?php if ($flagActivo == '1') echo "selected='selected'"; ?>>Activo</option>
                                        <option value="0" <?php if ($flagActivo == '0') echo "selected='selected'"; ?>>Inactivo</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="datosPrecios" style="float:left; display:none;width:100%;">
                        <?=$tabla_precios;?>
                    </div>
                    <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id='linkVerProveedor'></a>
                    <div id="datosProveedores" style="float:left; display:none;width:100%;">
                        <table id="tblProveedor" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
                            <tr align="center" bgcolor="#BBBB20" height="10px;">
                                <td>Nro</td>
                                <td>RUC</td>
                                <td>Nombre Proveedor</td>
                                <td>Domicilio</td>
                                <td>Departamento / Provincia / Distrito</td>
                                <td>Borrar</td>
                            </tr>
                            <?php
                            $kk = 1;
                            $cantidad2 = count($lista_proveedores);
                            if ($cantidad2 > 0) {
                                foreach ($lista_proveedores as $indice => $valor) {
                                    $kk = $indice + 1;
                                    ?>
                                    <tr bgcolor="#ffffff">
                                        <td align="center">
                                            <?php echo $kk; ?>
                                            <input type="hidden" name="productoproveedor[<?php echo $indice; ?>]" id="productoproveedor[<?php echo $indice; ?>]" value="<?php echo $valor->prodproveedor; ?>" />
                                            <input type="hidden" name="proveedor[<?php echo $indice; ?>]" id="proveedor[<?php echo $indice; ?>]" value="<?php echo $valor->proveedor; ?>" />
                                        </td>
                                        <td align="left"><input type="text" name="ruc[<?php echo $indice; ?>]" id="ruc[<?php echo $indice; ?>]" class="cajaPequena cajaSoloLectura" readonly="readonly" value="<?php echo $valor->ruc; ?>" /></td>
                                        <td align="left"><input type="text" name="nombre_proveedor[<?php echo $indice; ?>]" id="nombre_proveedor[<?php echo $indice; ?>]" class="cajaGrande cajaSoloLectura" readonly='readonly'" value="<?php echo $valor->nombre_proveedor; ?>" readonly="readonly" /></td>
                                        <td align="left"><input type="text" name="direccion[<?php echo $indice; ?>]" id="direccion[<?php echo $indice; ?>]" class="cajaGrande cajaSoloLectura" readonly="readonly" value="<?php echo $valor->direccion; ?>" /></td>
                                        <td align="left"><input type="text" name="distrito[<?php echo $indice; ?>]" id="distrito[<?php echo $indice; ?>]" class="cajaMedia cajaSoloLectura" readonly="readonly" value="<?php echo $valor->distrito; ?>" /></td>
                                        <td align="center"><a href="#" onclick="eliminar_productoproveedor(<?php echo $indice; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/delete.gif?=<?=IMG;?>" border="0" /></a></td>
                                    </tr>
                                    <?php
                                    $kk++;
                                }
                            }
                            ?>
                        </table>
                        <?php
                        $displaySucursal = $cantidad2 != '0' ? "display:none;" : "";
                        ?>
                        <div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displaySucursal; ?>">NO EXISTEN REGISTROS</div>
                    </div>
                    <div id="datosOcompras" style="float:left; display:none;width:100%;"></div>
                </div>
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                    <a href="javascript:;" id="imgGuardarProducto"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgLimpiarProducto"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgCancelarProducto"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                        <?php echo $oculto; ?>
                </div>
            </div>
        </div>
    </div>
</form>