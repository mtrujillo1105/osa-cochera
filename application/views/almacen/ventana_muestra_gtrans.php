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
        function seleccionar_gtrans(guia, serie, numero, tipo, estabc) {
            parent.seleccionar_gtrans(guia, serie, numero, tipo, estabc);
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

        function seleccionarOdenCompra(oCompra, serie, numero, valor) {
            parent.seleccionarOdenCompra(oCompra, serie, numero, valor);
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
            <li <?php if ($comprobante == 'DP') {
                echo 'style="background-color: #FF0000;"';
            } ?> ><a href="<?php echo base_url(); ?>index.php/almacen/produccion/ventana_muestra_gtrans/V/0/SELECT_HEADER/F/0/DP">GUIA DE TRANSFERENCIA</a></li>
        </ul>
    </div>
    <div id="frmBusqueda" style="width:97%;"></div>
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
                <th>ESTABLECIMIENTO</th>
                <th width="10%"></th>
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
