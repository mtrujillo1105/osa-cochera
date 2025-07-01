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

        var currentOV;

        $(document).ready(function () {
            base_url = $("#base_url").val();

            $('#imgCancelarDocumento').click(function () {
                parent.$.fancybox.close();
            });

        });


        function ver_detalle_documentoPresupuesto(documento) {
            if ('<?php echo $tipo_oper; ?>' != 'C') {
                url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/v/<?php echo $tipo_oper; ?>/" + documento;
            } else {
                url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/c/<?php echo $tipo_oper; ?>/" + documento;
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
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
            //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
            //fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
        }

        function seleccionar_presupuesto(guia, serie, numero) {
            parent.seleccionar_presupuesto(guia, serie, numero);
            parent.$.fancybox.close();
        }

        function ver_detalle_ocompra(documento) {
            currentOV = documento;
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
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }
        function seleccionar_ocompra(guia, serie, numero) {
            parent.seleccionar_ocompra(guia, serie, numero);
            parent.$.fancybox.close();
        }

        function seleccionar_oventa(guia, serie, numero) {
            parent.seleccionar_oventa(guia, serie, numero);
            parent.$.fancybox.close();
        }
    </script>
</head>
<body style="padding: 3%">
<div align="center">
    <?php echo $form_open; ?>
    <div id="tituloForm" class="header" style="width:95%; padding-top: 0px;">
        <ul class="lista_tipodoc">
            <?php if ($tipo_oper != 'C') { ?>
                <li <?php if ($comprobante == 'P') {
                    echo 'style="background-color: #FF0000;"';
                } ?> ><a
                        href="<?php echo base_url(); ?>index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/P/OC">PRESUPUESTO</a>
                </li>
                <li <?php if ($comprobante == 'OV') {
                    echo 'style="background-color: #FF0000;"';
                } ?> ><a
                        href="<?php echo base_url(); ?>index.php/compras/ocompra/ventana_muestra_oventa">O. VENTA</a>
                </li>
            <?php } else { ?>
                <li <?php if ($comprobante == 'P') {
                    echo 'style="background-color: #FF0000;"';
                } ?> ><a
                        href="<?php echo base_url(); ?>index.php/compras/presupuesto/ventana_muestra_presupuestoCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/P/OC">COTIZACION</a>
                </li>
            <?php } ?>
            <li <?php if ($comprobante == 'O') {
                echo 'style="background-color: #FF0000;"';
            } ?> ><a
                    href="<?php echo base_url(); ?>index.php/compras/ocompra/ventana_muestra_ocompra/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/O/OC"><?php if ($tipo_oper == 'V') echo 'O. de venta(RECURRENTE)'; else echo 'O. de compra(RECURRENTE)'; ?></a>
            </li>

        </ul>
    </div>
    <div id="frmBusqueda" style="width:95%;">
        <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado" align="center" cellspacing="1"
               cellpadding="3" border="0">
            <tr>
                <?php if ($tipo_oper == 'V') { ?>
                    <td>Cliente *</td>
                    <td valign="middle">
                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                        <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10"
                               maxlength="11" onblur="obtener_cliente();"
                               value="<?php echo $ruc_cliente; ?>"/>
                        <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente"
                               size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_cliente; ?>"/>

                    </td>
                <?php } else { ?>
                    <td>Proveedor *</td>
                    <td valign="middle">
                        <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                        <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10"
                               maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>"/>
                        <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura"
                               id="nombre_proveedor" size="40" maxlength="50" readonly="readonly"
                               value="<?php echo $nombre_proveedor; ?>"/>
                        <a style="display:none;"
                           href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/"
                           id="linkVerProveedor"><img height='16' width='16'
                                                      src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar'
                                                      border='0'/></a>
                    </td>
                    <td style="display:none;"><a href="javascript:;" id="imgBuscarDocumento"><img
                                src="<?php echo base_url(); ?>public/images/icons/botonbuscar.jpg?=<?=IMG;?>" class="imgBoton"/></a>
                        <a href="javascript:;" id="imgCancelarDocumento"><img
                                src="<?php echo base_url(); ?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" width="70" height="22"
                                class="imgBoton"></a>
                    </td>
                <?php } ?>
            </tr>
        </table>
    </div>
    <?php echo $form_hidden; ?>
    <?php echo $form_close; ?>
    <div id="frmResultado" style="width:95%; height: 175px; overflow: auto;">
        <table class="" width="100%" id="tblMovimientoSerie" align="center" cellspacing="1" cellpadding="3"
               border="0">
            <tr class="cabeceraTabla">
                <td colspan="8">
                    <?php
                    if ($comprobante == 'P') {
                        echo 'PRESUPUESTO';
                    }
                    if ($comprobante == 'O') {
                        echo 'O COMPRA';
                    }
                    ?>
                </td>
            </tr>
            <tr class="cabeceraTabla">
                <td width="10%">FECHA</td>
                <td width="6%">SERIE</td>
                <td width="10%">NUMERO</td>
                <td width="12%">NUM DOC</td>
                <td><?php if ($tipo_oper == 'V') echo 'CLIENTE'; else echo 'PROVEEDOR'; ?></td>
                <td width="10%">TOTAL</td>
                <td width="5%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
            </tr>
            <?php
            if (count($lista) > 0) {
                foreach ($lista as $indice => $valor) {
                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class;?>">
                        <td>
                            <div align="center"><?php echo $valor[0];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[1];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[2];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[3];?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[4];?></div>
                        </td>
                        <td>
                            <div align="right"><?php echo $valor[5];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[6];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[7];?></div>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td width="100%" class="mensajeminimo" colspan="7">No hay ning&uacute;n registro que cumpla con los
                        criterios de b&uacute;squeda
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <br/>

    <div id="frmResultado" style="width:95%; height: 150px; overflow: auto;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="display:none"/>
        <table class="" width="100%" id="tblDocumentoDetalle" align="center" cellspacing="1" cellpadding="3"
               border="0" style="display:none">
            <tr class="cabeceraTabla">
                <td colspan="7">DETALLES DE LA FACTURA</td>
            </tr>
            <tr class="cabeceraTabla">
                <td width="10%">CODIGO</td>
                <td>DESCRIPCION</td>
                <td width="7%">CANT</td>
                <td width="9%">PU C/IGV</td>
                <td width="8%">IMPORTE</td>
            </tr>
        </table>
        
    </div>
    <div style="padding-top: 20px;text-align: left;">
            <button style="display: none" id="btn-get-items" onclick="selectProducts()">Obtener productos</button>
        </div>
    <input type="hidden" name="almacen" id="almacen" value="<?php echo $almacen; ?>">

    <script>
        var products = [];
        function appendProduct(idProduct) {
            var indx = products.indexOf(idProduct);

            if(indx == -1) {
                products.push(idProduct);
            }else {
                products.splice(indx, 1);
            }

            $("#btn-get-items").css('display', products.length > 0 ? '' : 'none');
        }

        function selectProducts() {
            parent.seleccionar_oventa(currentOV, products);
            parent.$.fancybox.close();
        }
    </script>
</body>
</html>
