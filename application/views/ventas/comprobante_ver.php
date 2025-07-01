<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
?>
<html>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/theme.css?=<?=CSS;?>" type="text/css">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/comprobante.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>"
          media="screen"/>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($('#tdc').val() == '') {
                alert("Antes de registrar comprobantes debe ingresar Tipo de Cambio")
                top.location = "<?php echo base_url(); ?>index.php/index/inicio";
            }
            base_url = $("#base_url").val();
            tipo_oper = $("#tipo_oper").val();
            almacen = $("#cboCompania").val();
        });
    </script>
</head>
<body>
<input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>
<?php
//echo date("Y-m-d H:i:s");
// stylo para ocultar botones combos, etc
$style = "";
if (FORMATO_IMPRESION == 8) {
    $style = "display:none;";
}
?>
<!-- Inicio -->
<div id="VentanaTransparente" style="display:none;">
    <div class="overlay_absolute"></div>
    <div id="cargador" style="z-index:2000">
        <table width="100%" height="100%" border="0" class="fuente8">
            <tr valign="middle">
                <td> Por Favor Espere</td>
                <td><img src="<?php echo base_url(); ?>public/images/icons/cargando.gif?=<?=IMG;?>" border="0" title="CARGANDO"/><a href="#"
                                                                                                            id="hider2"></a>
                </td>
            </tr>
        </table>
    </div>
</div>
<!-- Fin -->
<form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">
    <div id="zonaContenido" align="center" style="height: 90%">
        <?php echo validation_errors("<div class='error'>", '</div>'); ?>
        <div id="tituloForm" class="header" style="width: 750px; height: 20px;">
            <?php echo $titulo; ?>
            <?php
            if ($tipo_docu != 'N') {
                if ($codigo == '') { ?>
                    <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio">
                        <option value="F" <?php if ($tipo_docu == 'F') echo 'selected="selected"'; ?>>FACTURA</option>
                        <option value="B" <?php if ($tipo_docu == 'B') echo 'selected="selected"'; ?>>BOLETA</option>
                    </select>
                <?php }
            } else { ?><input type="hidden" value="N" id="cboTipoDocu" name="cboTipoDocu"/><?php }; ?>
        </div>
        <div id="frmBusqueda" style="width: 750px;">
            <table class="fuente8_2" width="100%" cellspacing="0" cellpadding="5" border="0">
                <tr>
                    <td width="8%">N&uacute;mero</td>
                    <td width="38%" valign="middle">
                        <?php echo $serie . ' - ' . $numero; ?>
                        <label style="margin-left:80px; margin-right: 20px;">IGV</label>
                        <?php echo $igv; ?> %
                    </td>
                    <td width="9%" valign="middle">Presupuesto</td>
                    <td width="23%" valign="middle">
                        <?php echo $cboPresupuesto; ?>
                    </td>
                    <td width="7%" valign="middle">Fecha</td>
                    <td width="22%" valign="middle">
                        <?php echo $hoy; ?>

                    </td>
                </tr>
                <tr>
                    <?php if ($tipo_oper == 'V') { ?>
                        <td>Cliente</td>
                        <td valign="middle">
                            <?php echo $nombre_cliente; ?>"
                        </td>
                    <?php } else { ?>
                        <td>Proveedor</td>
                        <td valign="middle">
                            <?php echo $nombre_proveedor; ?>
                        </td>
                    <?php } ?>
                    <td valign="middle">Moneda</td>
                    <td valign="middle">
                        <?php echo $cboMoneda; ?>
                    </td>
                </tr>
                <tr>
                    <td>TDC</td>
                    <td>
                        <?php echo $tdc; ?>
                        <span  <?php if ($tipo_oper == 'C') {
                            echo 'style="display:none;"';
                        } ?>>
                                    Vendedor
                                    <select name="vendedor" id="vendedor" class="comboMedio"
                                            style="width:210px;"><?php echo $cboVendedor; ?></select>
                                </span>
                    </td>
                    <td>Forma Pago</td>
                    <td><?php echo $cboFormaPago; ?>
                    <td>Almacen</td>
                    <td><?php echo $cboAlmacen; ?></td>
                </tr>
            </table>
        </div>
        <div style="width: 750px;" id="frmBusqueda"  <?php echo $hidden; ?>>

        </div>
        <div id="frmBusqueda" style="height:250px; overflow: auto; width: 750px;">
            <table class="fuente8_2" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                <tr class="cabeceraTabla">
                    <td width="4%">
                        <div align="center">ITEM</div>
                    </td>
                    <td width="10%">
                        <div align="center">C&Oacute;DIGO</div>
                    </td>
                    <td>
                        <div align="center">DESCRIPCI&Oacute;N</div>
                    </td>
                    <td width="10%">
                        <div align="center">CANTIDAD</div>
                    </td>
                    <td width="6%">
                        <div align="center">PU C/IGV</div>
                    </td>
                    <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td width="6%">
                            <div align="center">PU S/IGV</div>
                        </td>
                    <?php } ?>
                    <td width="6%">
                        <div align="center">PRECIO</div>
                    </td>
                    <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td width="6%">
                            <div align="center">IGV</div>
                        </td>
                    <?php } ?>
                    <td width="6%">
                        <div align="center">IMPORTE</div>
                    </td>
                </tr>
            </table>
            <div>
                <table id="tblDetalleComprobante" class="fuente8_2" width="100%" border="0">
                    <?php
                    if (count($detalle_comprobante) > 0) {
                        foreach ($detalle_comprobante as $indice => $valor) {
                            $detacodi = $valor->CPDEP_Codigo;
                            $flagBS = $valor->flagBS;
                            $prodproducto = $valor->PROD_Codigo;
                            $unidad_medida = $valor->UNDMED_Codigo;
                            $codigo_interno = $valor->PROD_CodigoInterno;
                            $nombre_producto = $valor->PROD_Nombre;
                            $nombre_unidad = $valor->UNDMED_Simbolo;
                            $costo = $valor->CPDEC_Costo;
                            $GenInd = $valor->CPDEC_GenInd;
                            $prodcantidad = $valor->CPDEC_Cantidad;
                            $prodpu = $valor->CPDEC_Pu;
                            $prodsubtotal = $valor->CPDEC_Subtotal;
                            $proddescuento = $valor->CPDEC_Descuento;
                            $prodigv = $valor->CPDEC_Igv;
                            $prodtotal = $valor->CPDEC_Total;
                            $prodpu_conigv = $valor->CPDEC_Pu_ConIgv;
                            $prodsubtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
                            $proddescuento_conigv = $valor->CPDEC_Descuento_ConIgv;
                            if (($indice + 1) % 2 == 0) {
                                $clase = "itemParTabla";
                            } else {
                                $clase = "itemImparTabla";
                            }
                            ?>
                            <tr class="<?php echo $clase; ?>">

                                <td width="4%">
                                    <div align="center"><?php echo $indice + 1; ?></div>
                                </td>
                                <td width="10%">
                                    <div align="center"><?php echo $codigo_interno; ?></div>
                                </td>
                                <td>
                                    <div align="left"><?php echo $nombre_producto; ?></div>
                                </td>
                                <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                                    <td width="10%">
                                        <div align="left"><?php echo $prodcantidad; ?> <?php echo $nombre_unidad; ?>
                                        </div>
                                    </td>
                                    <td width="6%">
                                        <div align="center"><?php echo $prodpu_conigv; ?></div>
                                    </td>
                                    <td width="6%">
                                        <div align="center"><?php echo $prodpu; ?></div>
                                    </td>
                                    <td width="6%">
                                        <div align="center"><?php echo $prodsubtotal; ?></div>
                                    </td>
                                <?php } else { ?>
                                    <td width="10%">
                                        <div align="left"><?php echo $nombre_unidad; ?>
                                        </div>
                                    </td>
                                    <td width="6%">
                                        <div align="center"><?php echo $prodpu_conigv; ?></div>
                                    </td>
                                    <td width="6%">
                                        <div align="center"><?php echo $prodsubtotal_conigv; ?></div>
                                    </td>
                                <?php } ?>
                                <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                                    <td width="6%">
                                        <div align="center">
                                            <?php echo $prodigv; ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <td width="6%">
                                    <div align="center">
                                        <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                                            <?php //echo $proddescuento; ?>
                                        <?php } else { ?>
                                            <?php echo $proddescuento_conigv; ?>
                                        <?php } ?>
                                        <?php echo number_format($prodtotal, 2); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div id="frmBusqueda3" style="width: 750px;">
            <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8_2">
                <tr>
                    <td width="80%" rowspan="4" align="left">
                        <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8_2">

                            <tr>
                                <td colspan="4">Observación</td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo $observacion; ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="10%" class="busqueda">Sub-total</td>
                    <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td width="10%" align="right">
                            <div align="right"><?php echo number_format($preciototal, 2); ?></div>
                        </td>
                    <?php } else { ?>
                        <td width="10%" align="right">
                            <div align="right"><?php echo number_format($preciototal_conigv, 2); ?></div>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="busqueda">Descuento</td>
                    <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td align="right">
                            <div align="right"><?php echo number_format($descuentotal, 2); ?></div>
                        </td>
                    <?php } else { ?>
                        <td align="right">
                            <div align="right"><?php echo number_format($descuentotal_conigv, 2); ?></div>
                        </td>
                    <?php } ?>
                </tr>
                <?php if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                    <tr>
                        <td class="busqueda">IGV</td>
                        <td align="right">
                            <div align="right"><?php echo number_format($igvtotal, 2); ?></div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="busqueda">Precio Total</td>
                    <td align="right">
                        <div align="right"><?php echo number_format($importetotal, 2); ?></div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</form>
</body>
</html>
