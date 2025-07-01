<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<!--<script type="text/javascript" src="https://www.google.com/jsapi"></script>-->

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/google-api.js?=<?=JS;?>"></script>
<!--<script type="text/javascript" src="<?php echo base_url(); ?>public/js/google-uds.js?=<?=JS;?>"></script>-->
<script type="text/javascript">
    $(document).ready(function(){
        $('#verReporte').click(function(){
            $('#frmReporte').submit();
        });
        $('#limpiarProducto').click(function(){
            //$('#frmReporte').reset();
            top.location="<?php echo base_url(); ?>index.php/reportes/valorizacion/valor"
        });
        
        $('#limpiarReporteValorizacion').click(function(){
            //$('#frmReporte').reset();
            top.location="<?php echo base_url(); ?>index.php/reportes/valorizacion/valorizacion_producto"
        });
        $('input[name^="COMPANIA_"]').click(function(){
            if($(this).is(':checked')==false)
                $('input[name^="TODOS"]').attr('checked', false);
        });
        $('input[name="TODOS"]').click(function(){
            $('input[name^="COMPANIA_"]').attr('checked', false);
            if($(this).is(':checked'))
                $('input[name^="COMPANIA_"]').attr('checked', true);
        });
        
        $("a#linkVerProducto").fancybox({
            'width'          : 800,
            'height'         : 650,
            'autoScale'      : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
        });
    });
    
    function seleccionar_producto(codigo,interno,nombreProd){
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        $("#nombre_producto").val(nombreProd);
        
        base_url = $("#base_url").val();
        url      = base_url+"index.php/reportes/valorizacion/obtener_nombre_producto/B/"+interno;
        alert("codigo:"+codigo+", interno:"+interno);
        var dataString = "flagBS=B&interno="+interno;
        $.post(url,dataString,function(data){
            alert("PAS1");
            /*
            switch(data.result){
                case 'ok': 
                    if(codigo==''){
                        $('#codigo').val(data.codigo);
                        $('#ventana').show();
                        $('#linkVerImpresion').click();
                    }
                    else
                        location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;
                    break;
                case 'error': 
                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                    break;
                case 'error2': alert(data.msj);
                    break;
            }*/
        },'json');
        /*
        $.getJSON(url,function(data){
            alert("PAS1");
            $.each(data, function(i,item){
                //nombre_producto = item.PROD_Nombre;
                alert("Producto:"+item.PROD_Nombre);
            });
            //$("#nombre_producto").val(nombre_producto);
        });
         */
    }
    
    var cursor;
    if (document.all) { // Está utilizando EXPLORER            
        cursor='hand';
    } else { // Está utilizando MOZILLA/NETSCAPE
        cursor='pointer';
    }

</script>
<div id="pagina" style="position: relative !important;">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header">REPORTE DE VALORIZACION POR PRODUCTO</div>
            <div id="frmBusqueda">
                <form method="post" action="" id="frmReporte">
                    <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0 align="center">
                        <tr>
                            <td valign="top">Art&iacute;culo</td>
                            <td valign="top">
                                <input name="producto" id="producto" type="hidden" value="<?php echo $producto; ?>" class="cajaPequena" size="10" maxlength="11" />
                                <input name="codproducto" id="codproducto" type="text" value="<?php echo $codproducto; ?>" class="cajaPequena" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');" />
                                <input name="nombre_producto"  id="nombre_producto" type="text" value="<?php echo $nombre_producto; ?>" class="cajaGrande cajaSoloLectura" size="40" readonly="readonly" />
                                <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                            </td>                            
                            <td rowspan="3" valign="top">Establecimiento</td>
                            <td rowspan="3" valign="top">
                                <ul style="list-style: none; margin: 0px; padding: 0px;">
                                    <li><input type="checkbox" name="TODOS" id="TODOS" value="1" <?php if ($TODOS == true) echo 'checked="checked"'; ?> />TODOS</li>
                                    <?php
                                    foreach ($lista_companias as $valor) {
                                        echo '<li><input type="checkbox" name="COMPANIA_' . $valor->COMPP_Codigo . '" id="COMPANIA_' . $valor->COMPP_Codigo . '" value="1" ' . ($valor->checked == true ? 'checked="checked"' : '') . ' />' . $valor->EESTABC_Descripcion . '</li>';
                                    }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"></td>
                            <td valign="top"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" style="vertical-align: middle;">
                                <a href="javascript:;" id="verReporte">
                                    <img src="<?php echo base_url(); ?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" align="absmiddle"/>
                                </a>&nbsp;
                                <a href="javascript:;" id="limpiarReporteValorizacion">
                                    <img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" align="absmiddle" />
                                </a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <div style="clear: both;"></div>          
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/almacenproducto.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
    $(document).ready(function() {
        $("a#linkSerie").fancybox({
            'width'	     : 750,
            'height'         : 540,
            'autoScale'	     : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : false,
            'type'	     : 'iframe'
        });
    });
</script>
<div style="clear: both;"></div>
<div id="pagina" >
    <div id="zonaContenido">
        <div align="center">
<div class="acciones">
            <div id="lineaResultado" style="margin-top:20px">
                <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                    <tr>
                        <td width="50%" align="left">N de articulos encontrados:&nbsp;<?php echo $registros; ?> </td>
                    </tr>
                </table>
            </div>
</div>
            <div id="frmResultado">   

                <form id="frmkardex" name="frmkardex" method="post" action="<?php echo $action2; ?>">
                    <input type="hidden" name="compania" id="compania"/>
                    <input type="hidden" name="almacen_id" id="almacen_id" />
                    <input type="hidden" name="producto" id="producto" />
                    <a href="javascript:;" id="linkSerie"></a>

                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                        <caption style="caption-side: bottom;">S=Stock, P.U.=Precio Unitario, P.T.=Precio Total</caption>
                        <tr class="cabeceraTabla">
                            <td width="3%" rowspan="2">ITEM</td>
                            <td width="5%" rowspan="2">CODIGO</td>
                            <td width="22%" rowspan="2">DESCRIPCION</td>
                            <!--<td width="4%">P.U.</td>-->
                            <?php foreach ($lista_establec as $indice => $valor) { ?>
                                <td align="center" colspan="3" rowspan="1">
                                    <?php echo $valor->EESTABC_Descripcion; ?></td>
                            <?php } ?>
                            <td width="4%" align="center" colspan="3" rowspan="1">TOTAL</td>
                        </tr>
                        <tr class="cabeceraTabla">

                            <?php
                            $i = 0;

                            foreach ($lista_establec as $indice => $valor) {
                                if ($i <= (count($lista_establec))) {
                                    ?>
                                    <td align="center" rowspan="1">
                                        S.
                                    </td>
                                    <td align="center" rowspan="1">
                                        P.U.
                                    </td>
                                    <td align="center" rowspan="1">
                                        P.T.
                                    </td>
                                    <?php
                                }
                                $i++;
                            }
                            ?>
                            <td align="center" rowspan="1">
                                S.
                            </td>
                            <td align="center" rowspan="1">
                                P.U.
                            </td>
                            <td align="center" rowspan="1">
                                P.T.
                            </td>

                        </tr>

                        <?php
                        if (count($lista) > 0) {
                            foreach ($lista as $indice => $valor) {
                                $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                                ?>
                                <tr class="<?php echo $class; ?>">
                                    <td><div align="center"><?php echo $valor[0]; ?></div></td>
                                    <td><div align="center"><?php echo $valor[3]; ?></div></td>
                                    <td><div align="center"><?php echo $valor[4]; ?></div></td>
                                    <!--<td><div align="left">$20</div></td>-->
                                    <?php foreach ($valor[5] as $indice2 => $valor2) { ?>
                                                                <!--                                        <td style="text-align: center;">
                                                                                                    <table>-->
                                        <td style="vertical-align: middle;text-align: center;border-left-width: 1px;border-left-style: solid;border-left-color: #868d99;">
                                            <label style="margin-top: auto;margin-bottom: auto;height: 10px;<?php if ($valor2 > 0) echo 'font-weight:bold; color:blue'; ?>"><?php echo $valor2; ?>
                                                <?php if ($indice2 < count($lista_establec)) { ?>

                                                    <?php if ($valor[2] == "I") { ?>

                                                    <?php } ?>
                                                <?php } ?>
                                            </label>
                                        </td>
                                        <td style="vertical-align: middle;text-align: center;">
                                            <?php echo round($valor[6][$indice2], 2); ?>
                                        </td>
                                        <td style="vertical-align: middle;text-align: center;">
                                            <?php echo $valor2 * round($valor[6][$indice2], 2); ?>
                                        </td>
                                        <!--                                            </table>
                                                                                </td>-->
                                    <?php } ?>
                                </tr>
                                <?php
                            }
                        } else {
                            ?><tr>
                                <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                              </tr>
                            <?php
                        }
                        ?>
                    </table>
                </form>
            </div>
            <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
            <input type="hidden" id="iniciopagina" name="iniciopagina">
            <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
            <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
        </div>
    </div>			
</div>