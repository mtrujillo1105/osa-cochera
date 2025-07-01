<html>
<head>
    <title><?php echo TITULO; ?></title>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto_popup.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>"
          media="screen"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div align="center">
    <div id="frmBusqueda" style="width:95%">
        <table class="" width="100%" cellspacing=0 cellpadding=3 border=0>
            <tr class="header" height="25px">
                <td align="center" colspan="3">LISTADO DE Comprobantes :: <?php echo $producto[0]->PROD_Nombre; ?></td>
            </tr>
            </tr>
        </table>
    </div>
    <div id="frmResultado" style="width:95%; height: 300px; overflow: auto; background-color: #f5f5f5">
        <table class="" width="100%" id="" name="tabla_resultado" align="center" border="0" cellpadding="4">
            <tr class="cabeceraTabla">
                <td width="3%">ITEM</td>
                <td width="9%">COMPROBANTE</td>
                <td width="9%">FECHA</td>
                <td width="3%">SERIE</td>
                <td width="5%">NUMERO</td>
                <td width="5%">CANTIDAD</td>
                <td width="20%">RAZON SOCIAL</td>
            </tr>
            <?php
            if (count($lista_detalles) > 0) {
                foreach ($lista_detalles as $key => $value) {
                    $class = $key % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class; ?>">
                        <td width="3%">
                            <div align="center"><?php echo ++$key; ?></div>
                        </td>
                        <td width="9%">
                            <div align="center"><?php if ($value->TipoDoc == 'F') {
                                    echo 'FACTURA';
                                } else {
                                    echo 'BOLETA';
                                } ?></div>
                        </td>
                        <td width="9%">
                            <div align="center"><?php echo $value->fecha; ?></div>
                        </td>
                        <td width="3%">
                            <div align="center"><?php echo $value->serie; ?></div>
                        </td>
                        <td width="5%">
                            <div align="center"><?php echo $value->numero; ?></div>
                        </td>
                        <td width="5%">
                            <div align="center"><?php echo $value->cantidad; ?></div>
                        </td>
                        <td width="20%">
                            <div align="center"><?php echo $value->razon; ?></div>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr class="itemParTabla">
                    <td colspan="6">
                        <div align="center"><?php echo "TODAV&Iacute;A NO HAY COMPROBANTES"; ?></div>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <br/>
    <table width="100%" border="0">
        <tr>
            <td>
                <div align="center"><a href="#" onclick="parent.$.fancybox.close(); "><img
                            src="<?php echo base_url(); ?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" width="70" height="22" border="1"></a>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>