<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona)) header("location:$url");
?>
<html>
<head>
    <title><?php echo TITULO; ?></title>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script>
        var base_url;
        var flagBS;
        $(document).ready(function () {
            base_url = $("#base_url").val();
            //a=$("#almacen").val();
            $('#imgCancelarDocumento').click(function () {
                parent.$.fancybox.close();
            });

        });

        function ver_detalle_documento(documento) {
            //almacen = $("#almacen").val();
            url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + documento + "/<?php echo $tipo_oper; ?>/<?php echo $almacen; ?>";

            $("#tblDocumentoDetalle tr[class!='cabeceraTabla']").html('');
            $('#tblDocumentoDetalle').hide();
            $('img#loading,').show();
            $.getJSON(url, function (data) {
                $('#tblDocumentoDetalle').show();
                $('img#loading').hide();
                $.each(data, function (i, item) {
                    if (i % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }

                    fila = '<tr class="' + clase + '">';
                    fila += '<td><div align="left">' + item.PROD_CodigoInterno + '</div></td>';
                    fila += '<td><div align="left">' + item.PROD_Nombre + '</div></td>';
                    fila += '<td><div align="right">' + item.GUIAREMDETC_Cantidad + '</div></td>';
                    fila += '<td ><div align="right">' + item.GUIAREMDETC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.GUIAREMDETC_Total + '</div></td>';
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }

        function seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
            parent.seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo);
            //parent.$.fancybox.close(); 
        }

        function seleccionar_guiarem(guia, serie, numero, ocCliente = '') {
            parent.seleccionar_guiarem(guia, serie, numero, ocCliente);
            parent.$.fancybox.close();
        }

        function seleccionar_guiaremD(guia, serie, numero, tipo, estabc) {
            parent.seleccionar_guiaremD(guia, serie, numero, tipo, estabc);
            parent.$.fancybox.close();
        }

        function ver_detalle_documentoPresupuesto(documento) {
            if ('<?php echo $tipo_oper; ?>' != 'C') {
                url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/v/<?php echo $tipo_oper; ?>/" + documento;
            } else {
                url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/<?php echo $tipo_oper; ?>/<?php echo $tipo_oper; ?>/" + documento;
            }

            $("#tblDocumentoDetalle tr[class!='cabeceraTabla']").html('');
            $('#tblDocumentoDetalle').hide();
            $('img#loading,').show();
            $.getJSON(url, function (data) {
                $('#tblDocumentoDetalle').show();
                $('img#loading').hide();
                $.each(data, function (i, item) {
                    if (i % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }

                    fila = '<tr class="' + clase + '">';
                    fila += '<td><div align="left">' + item.PROD_CodigoInterno + '</div></td>';
                    fila += '<td><div align="left">' + item.PROD_Nombre + '</div></td>';
                    fila += '<td><div align="right">' + item.PRESDEC_Cantidad + ' ' + item.UNDMED_Simbolo + '</div></td>';
                    fila += '<td ><div align="right">' + item.PRESDEC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.PRESDEC_Total + '</div></td>';
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }

        function seleccionar_presupuesto(guia, serie, numero) {
            parent.seleccionar_presupuesto(guia, serie, numero);
            parent.$.fancybox.close();
        }

        function seleccionarOdenCompra(oCompra) {
            parent.seleccionarOdenCompra(oCompra);
            parent.$.fancybox.close();
        }

        function joinOComprasDetalle(data) {
            parent.joinOComprasDetalle(data);
            parent.$.fancybox.close();
        }

        function ver_detalle_ocompra(documento) {
            //alert(documento);
            url = base_url + "index.php/compras/ocompra/obtener_detalle_ocompra2/" + documento;
            $("#tblDocumentoDetalle tr[class!='cabeceraTabla']").html('');
            $('#tblDocumentoDetalle').hide();
            $('img#loading,').show();

            $.getJSON(url, function (data) {
                $('#tblDocumentoDetalle').show();
                $('img#loading').hide();
                $.each(data, function (i, item) {
                    if (i % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }

                    fila = '<tr class="' + clase + '">';
                    fila += '<td><div align="left">' + item.PROD_CodigoInterno + '</div></td>';
                    fila += '<td><div align="left">' + item.PROD_Nombre + '</div></td>';
                    fila += '<td><div align="right">' + item.OCOMDEC_Cantidad + ' ' + item.UNDMED_Simbolo + '</div></td>';
                    fila += '<td ><div align="right">' + item.OCOMDEC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.OCOMDEC_Total + '</div></td>';
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    //fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }

        function ver_detalle_documento_recu(documento) {
            //almacen = $("#almacen").val();
            url = base_url + "index.php/ventas/comprobante/obtener_detalle_comprobante/" + documento + "/<?php echo $tipo_oper; ?>/<?php echo $almacen; ?>";

            $("#tblDocumentoDetalle tr[class!='cabeceraTabla']").html('');
            $('#tblDocumentoDetalle').hide();
            $('img#loading,').show();
            $.getJSON(url, function (data) {
                $('#tblDocumentoDetalle').show();
                $('img#loading').hide();
                $.each(data, function (i, item) {
                    if (i % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }

                    fila = '<tr class="' + clase + '">';
                    fila += '<td><div align="left">' + item.PROD_CodigoInterno + '</div></td>';
                    fila += '<td><div align="left">' + item.PROD_Nombre + '</div></td>';
                    fila += '<td><div align="right">' + item.CPDEC_Cantidad + ' ' + item.UNDMED_Simbolo + '</div></td>';
                    fila += '<td ><div align="right">' + item.CPDEC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.CPDEC_Total + '</div></td>';
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }

        function seleccionar_comprobante_recu(guia, serie, numero) {
            parent.seleccionar_comprobante_recu(guia, serie, numero);
            parent.$.fancybox.close();
        }


    </script>
</head>
<body>
<div align="center">
    <?php echo $form_open; ?>
    <div id="tituloForm" class="header" style="width:95%; padding-top: 0; ">
        <ul class="lista_tipodoc">
            <li <?php if ($comprobante == 'G') {
                echo 'style="background-color: #FF0000;"';
            } ?> ><a href="<?php echo base_url(); ?>index.php/almacen/guiarem/ventana_muestra_guiarem/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/G">GUIA REMISION</a></li>
            <?php
            if ( !isset($despacho) ){ ?>
                <?php if ($tipo_oper != 'C') { ?>

                <?php } else { ?>
                    <li <?php if ($comprobante == 'P') {
                        echo 'style="background-color: #FF0000;"';
                    } ?> ><a href="<?php echo base_url(); ?>index.php/compras/presupuesto/ventana_muestra_presupuestoCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $tipo_doc; ?>/<?php echo $almacen; ?>/P">COTIZACION</a>
                    </li>
                <?php } ?>
                <li <?php if ($comprobante == 'O') {
                    echo 'style="background-color: #FF0000;"';
                } ?> ><a
                        href="<?php echo base_url(); ?>index.php/compras/ocompra/ventana_muestra_ocompraCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $tipo_doc; ?>/<?php echo $almacen; ?>/O">
                        <?php if ($tipo_oper == 'V') echo 'COTIZACIÓN'; else echo 'COTIZACIÓN'; ?></a></li>
                <li <?php if ($comprobante == 'R') {
                    echo 'style="background-color: #FF0000;"';
                } ?> ><a
                        href="<?php echo base_url(); ?>index.php/ventas/comprobante/ventana_muestra_recurrentes/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $tipo_doc; ?>/<?php echo $almacen; ?>/R">Doc.
                        Recurrentes</a></li> <?php
            } ?>
        </ul>
    </div>
    <div id="frmBusqueda" style="width:97%;">
        <table class="fuente8_2" width="100%" id="tabla_resultado" align="center" cellspacing="1"
               cellpadding="3" border="0">
            <tr>
                <?php if ($tipo_oper == 'V') { ?>
                    <td style="padding-left: 25px; color: #2d6674; font-size: 16px;">Cliente *</td>
                    <td valign="middle">
                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                        <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_cliente; ?>"/>
                        <!--<a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
                    </td>
                <?php } else { ?>
                    <td>Proveedor *</td>
                    <td valign="middle">
                        <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                        <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_proveedor; ?>"/>
                        <!--<a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
                    </td>
                <?php } ?>
            </tr>
        </table>
    </div>
    <?php echo $form_hidden; ?>
    <?php echo $form_close; ?>
    <div class="clear"></div>
    <div id="frmResultado" style="width:98%; height: 150px; overflow-y: auto;">
        <table class="fuente8_2" id="tblMovimientoSerie" align="center" cellspacing="1" cellpadding="3" border="0">
            <tr class="cabeceraTabla">
                <td colspan="8">LISTA</td>
            </tr>
            <tr class="cabeceraTabla">
                <th width="10%">FECHA</th>
                <th width="6%">SERIE</th>
                <th width="10%">NUMERO</th>
                <th width="12%">NUM DOC</th>
                <th><?php echo ($tipo_oper == 'V') ? 'CLIENTE' : 'PROVEEDOR'; ?></th>
                <th width="10%">TOTAL</th>
                <th width="5%">&nbsp;</th>
                <th width="5%">&nbsp;</th>
            </tr>
            <?php
            if (count($lista) > 0) {
                foreach ($lista as $indice => $valor) {
                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class; ?>">
                        <td>
                            <div align="center"><?php echo $valor[0]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[1]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[2]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[3]; ?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[4]; ?></div>
                        </td>
                        <td>
                            <div align="right"><?php echo $valor[5]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[6]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[7]; ?></div>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>

                    <td width="" class="mensaje2" colspan="8">No hay ning&uacute;n registro que cumpla con los
                        criterios de b&uacute;squeda
                    </td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                </tr>
            <?php
            }
            ?>
        </table>
        <div align="left">
            
        </div>
    </div>
    <br/>
    <div id="frmResultado" style="width:98%; height: 150px; overflow: auto; padding-top: 5px">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="display:none"/>
        <table class="fuente8_2_3" width="100%" id="tblDocumentoDetalle" align="center" cellspacing="1" cellpadding="3"
               border="0" style="display:none">
            <tr class="cabeceraTabla">
                <td colspan="7">DETALLES</td>
            </tr>
            <tr class="cabeceraTabla">
                <td width="10%">CODIGO</td>
                <td>DESCRIPCION</td>
                <td width="7%">CANT</td>
                <td width="9%">PU C/IGV</td>
                <td width="8%">IMPORTE</td>
                <td width="4%">&nbsp;</td>
            </tr>
        </table>
    </div>
    <input type="hidden" name="almacen" id="almacen" value="<?php echo $almacen; ?>">
</body>
</html>

<script>
    var tblOCompras = $("#tblMovimientoSerie")
        oCompras = [],
        oComprasCount = tblOCompras.find('.selectOCompra').length;

    tblOCompras.find("#selectAll").change(function(){
        var checked = $(this).attr('checked');
        $.each(tblOCompras.find(".selectOCompra"), function(index, el) {
            var elm = $(el);

            if(!checked) {
                elm.removeAttr('checked');
                elm.val("0");
            }else {
                elm.attr('checked', 'checked');
                elm.val("1");
            }

            elm.trigger('change');
        });
    });

    tblOCompras.find(".selectOCompra").change(function(){
        var elm = $(this),
            checked = elm.attr('checked'),
            id = elm.data('id');
        
        var indexOCompra = oCompras.indexOf(id);

        if(!checked){
            if(indexOCompra != -1) oCompras.splice(indexOCompra, 1);
        }else {
            if(indexOCompra == -1) oCompras.push(id);
        }

        if(oComprasCount == oCompras.length) {
            tblOCompras.find("#selectAll").attr('checked', 'checked');
        }else {
            tblOCompras.find("#selectAll").removeAttr('checked');
        }

        $("#btnSelectGroup").css('display', oCompras.length > 1 ? '' : 'none');
    })

    $("#btnSelectGroup").click(function () {
        $.ajax({
            url: '<?php echo base_url() ?>index.php/compras/ocompra/obtener_detalles_ocompras',
            type: 'POST',
            data: {compras : oCompras}
        })
        .done(function(data) {
            var data = $.parseJSON(data);
            joinOComprasDetalle(data);
        })
        .fail(function() {
            alert("No se pudo obtener los articulos.");
        });
        
    })
</script>
