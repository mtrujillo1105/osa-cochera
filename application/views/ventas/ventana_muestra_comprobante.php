<html>
<head>
    <title><?php echo TITULO; ?></title>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>

    <link rel="stylesheet" type="text/css" href="<?=base_url();?>public/js/datatables/datatables.css">
    <script type="text/javascript" charset="utf8" src="<?=base_url();?>public/js/datatables/datatables.js"></script>
    <link href="<?=base_url();?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
    
    <meta charset="utf-8"/>
    <script>
        var base_url;
        var flagBS;

        $(document).ready(function () {
            base_url = $("#base_url").val();

            $('#imgCancelarDocumento').click(function () {
                parent.$.fancybox.close();
            });

        });

        function ver_detalle_documento(documento) {
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
                    fila += '<td><div align="left">' + item.PROD_CodigoUsuario + '</div></td>';
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

        function seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
            parent.seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo);
            //parent.$.fancybox.close(); 
        }
        function seleccionar_comprobante(comprobante) {
            parent.seleccionar_comprobante(comprobante);
            parent.$.fancybox.close();
        }

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
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }

        function seleccionar_importacion(guia, serie, numero) {
            parent.seleccionar_importacion(guia, serie, numero);
            parent.$.fancybox.close();
        }

        function ver_detalle_importacion(documento) {
            var url = base_url + "index.php/ventas/importacion/obtener_detalle_importacion/" + documento;

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
                    fila += '<td><div align="right">' + item.GUIAREMDETC_Cantidad + ' ' + item.UNDMED_Descripcion + '</div></td>';
                    fila += '<td ><div align="right">' + item.GUIAREMDETC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.GUIAREMDETC_Total + '</div></td>';
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
                    fila += '<td><div align="left">' + item.PROD_CodigoUsuario + '</div></td>';
                    fila += '<td><div align="left">' + item.PROD_Nombre + '</div></td>';
                    fila += '<td><div align="right">' + item.OCOMDEC_Cantidad + ' ' + item.UNDMED_Simbolo + '</div></td>';
                    fila += '<td ><div align="right">' + item.OCOMDEC_Pu_ConIgv + '</div></td>';
                    fila += '<td><div align="right">' + item.OCOMDEC_Total + '</div></td>';
                    //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                    fila += '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle(' + item.onclick + ')"><img src="' + base_url + 'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                    fila += '</tr>';
                    $("#tblDocumentoDetalle").append(fila);
                });
            });
        }


        function ver_detalle_documento_recu(documento) {
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
        function seleccionar_guiarem_recu(guia, serie, numero) {
            parent.seleccionar_guiarem_recu(guia, serie, numero);
            parent.$.fancybox.close();
        }

        function seleccionar_ocompra(guia) {
            parent.seleccionar_ocompra(guia);
            parent.$.fancybox.close();
        }

    </script>
</head>
<body style="padding: 3%">
<div align="center">
    <?php echo $form_open; ?>
    <div id="tituloForm" class="header" style="width:100%; padding-top: 0;">
        <ul class="lista_tipodoc">
            <li <?=($comprobante == 'F') ? 'style="background-color: #FF0000;"' : '';?> >
                <a href="<?php echo base_url(); ?>index.php/ventas/comprobante/ventana_muestra_comprobante/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/F">Factura</a>
            </li>
            <li <?=($comprobante == 'B') ? 'style="background-color: #FF0000;"' : ''; ?> >
                <a href="<?php echo base_url(); ?>index.php/ventas/comprobante/ventana_muestra_comprobante/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/B">Boleta</a>
            </li>
            <li <?=($comprobante == 'N') ? 'style="background-color: #FF0000;"' : ''; ?> >
                <a href="<?php echo base_url(); ?>index.php/ventas/comprobante/ventana_muestra_comprobante/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/N">Comprobante</a>
            </li> <?php
                if ($tipo_oper != 'C') { ?>
                    <li <?=($comprobante == 'P') ? 'style="background-color: #FF0000;"' : ''; ?> hidden>
                        <a href="<?php echo base_url(); ?>index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $almacen; ?>/P">PRESUPUESTO</a>
                    </li> <?php
                }
                else { ?>
                    <li <?=($comprobante == 'P') ? 'style="background-color: #FF0000;"' : ''; ?> >
                        <a href="<?php echo base_url(); ?>index.php/compras/presupuesto/ventana_muestra_presupuestoCom/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $almacen; ?>/P">COTIZACION</a>
                    </li> <?php
                } ?>
            <li <?=($comprobante == 'O') ? 'style="background-color: #FF0000;"' : ''; ?> >
                <a href="<?php echo base_url(); ?>index.php/compras/ocompra/ventana_muestra_ocompra/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/O"><?php if ($tipo_oper == 'V') echo 'COTIZACIÓN'; else echo 'COTIZACIÓN'; ?></a>
            </li>
            <li <?=($comprobante == 'R') ? 'style="background-color: #FF0000;"' : ''; ?> hidden>
                <a href="<?php echo base_url(); ?>index.php/almacen/guiarem/ventana_muestra_recurrentes/<?php echo $tipo_oper; ?>/<?php if ($tipo_oper == 'V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/F/<?php echo $almacen; ?>/R">Doc. Recurrentes</a>
            </li>
        </ul>
    </div>
    <div id="frmBusqueda" style="width: 100%;">
        <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado" align="center" cellspacing="1" cellpadding="3" border="0">
            <tr> <?php
                if ($tipo_oper == 'V') { ?>
                    <td><span class="spanTitulo">Cliente *</span></td>
                    <td valign="middle">
                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                        <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" readonly="readonly" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_cliente; ?>"/>
                    </td> <?php
                } else { ?>
                    <td>Proveedor *</td>
                    <td valign="middle">
                        <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                        <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly"/>
                        <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_proveedor; ?>"/>
                        <a style="display:none;" href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0'/></a>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="fecha">Fecha:</label> &nbsp;
                    <input type="date" id="fecha" name="fecha" class="cajaMedia" style="height: 100%">
                    
                    &nbsp;&nbsp;&nbsp;
                    <label for="serie">Serie:</label> &nbsp;
                    <input type="text" id="serie" name="serie" class="cajaMedia" style="height: 100%">

                    &nbsp;&nbsp;&nbsp;
                    <label for="numero">Número:</label> &nbsp;
                    <input type="text" id="numero" name="numero" class="cajaMedia" style="height: 100%">

                    &nbsp;&nbsp;&nbsp;
                    <span class="btn btn-primary" id="clear">Limpiar</span>
                </td>
            </tr>
        </table>
    </div>
    <?php echo $form_hidden; ?>
    <?php echo $form_close; ?>
    <div id="frmResultado" style="width:100%; height: auto; overflow: auto;">
        <table class="fuente8 display" width="100%" id="table-docs" align="center" cellspacing="1" cellpadding="3" border="0">
            <div id="cargando_datos" class="loading-table">
                <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
            </div>
            <thead>
                <tr class="cabeceraTabla">
                    <th style="width:10%" data-orderable="true">FECHA</th>
                    <th style="width:06%" data-orderable="true">SERIE</th>
                    <th style="width:06%" data-orderable="true">NÚMERO</th>
                    <th style="width:12%" data-orderable="false">NUM DOC</th>
                    <th style="width:45%" data-orderable="false"><?=($tipo_oper == 'V') ? 'CLIENTE' : 'PROVEEDOR';?></th>
                    <th style="width:10%" data-orderable="false">TOTAL</th>
                    <th style="width:03%" data-orderable="false">&nbsp;</th>
                    <th style="width:03%" data-orderable="false">&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <br/>

    <div id="frmResultado" style="width:98%; height: 150px; overflow: auto;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="display:none"/>
        <table class="fuente8_2_3" width="100%" id="tblDocumentoDetalle" align="center" cellspacing="1" cellpadding="3" border="0" style="display:none">
            <tr class="cabeceraTabla">
                <td colspan="7">DETALLES DE LA FACTURA</td>
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

<script>
    $(document).ready(function(){
        doc = "<?=$comprobante = (isset($comprobante)) ? $comprobante : '';?>";
        base_url = "<?=base_url();?>";

        switch (doc) {
            case 'F':
                var url = base_url + "index.php/ventas/comprobante/datatable_muestra_comprobante";
                break;
            case 'B':
                var url = base_url + "index.php/ventas/comprobante/datatable_muestra_comprobante";
                break;
            case 'N':
                var url = base_url + "index.php/ventas/comprobante/datatable_muestra_comprobante";
                break;
            case 'O':
                var url = base_url + "index.php/compras/ocompra/datatable_ventana_ocompra";
                break;
            
            default:
                var url = base_url + "index.php/compras/ocompra/datatable_ventana_ocompra";
                break;
        }

        $('#table-docs').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : url,
                    type: "POST",
                    data:{
                            tipo_oper: "<?=$tipo_oper;?>",
                            cliente: "<?=($tipo_oper == 'V') ? $cliente : $proveedor;?>",
                            tipo_docu: doc,
                            fecha: $("#fecha").val(),
                            serie: $("#serie").val(),
                            numero: $("#numero").val()
                        },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 0, "desc" ]]
        });

        $("#clear").click(function(){
            $("#fecha").val("");
            $("#serie").val("");
            $("#numero").val("");
            search();
        });

        $("#fecha").change(function(){
            search();
        });

        $("#serie").keyup(function(){
            search();
        });

        $("#numero").keyup(function(){
            search();
        });

        function search(){
            $('#table-docs').DataTable({ responsive: true,
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url : url,
                        type: "POST",
                        data:{
                                tipo_oper: "<?=$tipo_oper;?>",
                                cliente: "<?=($tipo_oper == 'V') ? $cliente : $proveedor;?>",
                                tipo_docu: doc,
                                fecha: $("#fecha").val(),
                                serie: $("#serie").val(),
                                numero: $("#numero").val()
                            },
                        beforeSend: function(){
                            $(".loading-table").show();
                        },
                        error: function(){
                        },
                        complete: function(){
                            $(".loading-table").hide();
                        }
                },
                language: spanish,
                order: [[ 0, "desc" ]]
            });
        }
    });
</script>

</html>