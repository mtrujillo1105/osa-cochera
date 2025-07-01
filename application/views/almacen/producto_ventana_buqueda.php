<html>
    <head>
        <title><?php echo TITULO; ?></title>
        <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto_popup.js?=<?=JS;?>"></script>   
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script type="text/javascript">
            $(document).ready(function(){
                $("a#nuevoProducto").fancybox({
                    'width'          : 650,
                    'height'         : 550,
                    'autoScale'  : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : true,
                    'type'           : 'iframe'
                });
        
                $("a#editar_producto_popup").fancybox({
                    'width'          : 650,
                    'height'         : 550,
                    'autoScale'  : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : true,
                    'type'           : 'iframe'
                });
            });
        </script>
    </head>
    <body onload="document.getElementById('txtNombre').focus();">
        <div align="center">
            <?php
            if (isset($kardex)) {
                ?>  
                <form name="form_busqueda" id="form_busqueda" method="post" action="<?php echo site_url('almacen/producto/ventana_busqueda_producto_kardex/' . $flagBS); ?>" >

                    <?php
                } else {
                    ?> 
                    <form name="form_busqueda" id="form_busqueda" method="post" action="<?php echo site_url('almacen/producto/ventana_busqueda_producto/' . $flagBS); ?>" >

                        <?php
                    }
                    ?>
                    <div id="frmBusqueda" style="width:95%">
                        <table class="fuente8_2" width="100%" cellspacing=0 cellpadding=3 border=0>
                            <tr class="cabeceraTabla" height="25px">
                                <td align="center" colspan="3"><?php if ($flagBS == 'B') echo 'ARTICULOS'; else echo 'SERVICIOS'; ?></td>
                            </tr>
                            <tr>
                                <td width="8%">Código </td>
                                <td colspan="2"><input id="txtCodigo" type="text" class="cajaPequena" NAME="txtCodigo" maxlength="30" value="<?php echo $codigo; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Nombre</td>
                                <td colspan="2"><input id="txtNombre" name="txtNombre" type="text" class="cajaGeneral" size="40"  maxlength="100" value="<?php echo $nombre; ?>"/></td>
                            </tr>
                            <tr>
                                <td><?php if ($flagBS == 'B') { ?>Familia<?php } ?></td>
                                <td><?php if ($flagBS == 'B') { ?><input id="txtFamilia" type="text" class="cajaGeneral" size="40" NAME="txtFamilia" maxlength="100" value="<?php echo $familia; ?>" /><?php } ?></td>
                                <td align="right"><a href="javascript:;" id="buscarProducto"><img  src="<?php echo base_url(); ?>public/images/icons/botonbuscar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                    <a href="javascript:;" id="limpiarProducto"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                    <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_nuevo_producto/<?php echo $flagBS; ?>" id="nuevoProducto"><img src="<?php echo base_url(); ?>public/images/icons/<?php if ($flagBS == 'B') echo 'botonnuevoarticulo.jpg?=<?=IMG;?>'; else echo 'botonnuevoservicio.jpg?=<?=IMG;?>'; ?>" class="imgBoton" /></a>
                                    <a href="javascript:;" id="cerrarProducto"><img src="<?php echo base_url(); ?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="lineaResultado" style="width:95%; margin-top:10px">
                        <table class="fuente8_2" width="100%" cellspacing=0 cellpadding=3 border=0>
                            <tr>
                                <td width="50%" align="left">N° de registros encontrados:&nbsp;<?php echo $registros; ?></td>
                                <td width="50%" align="right">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                    <div id="frmResultado" style="width:95%; height: 280px; overflow: auto; background-color: #f5f5f5">
                        <table class="fuente8_2" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" border="0" cellpadding="4" cellspacing="1">
                            <tr class="cabeceraTabla">
                                <td width="5%"><div align="center"><b>Item</b></div></td>
                                <td width="5%"><div align="center"><b></b></div></td>
                                <?php if (FORMATO_IMPRESION != 4): ?>
                                    <td width="7%"><div align="center"><b>CÓDIGO</b></div></td>
                                <?php endif; ?>
                                <td width="48%"><div align="center"><b>Nombre</b></div></td>
                                <?php if ($flagBS == 'B') { ?>
                                    <?php if (FORMATO_IMPRESION != 4): ?>
                                        <td width="20%"><div align="center"><b>FAMILIA</b></div></td>
                                    <?php endif; ?>
                                    <td width="15%"><div align="center"><b>MODELO</b></div></td>
                                    <td width="15%"><div align="center"><b>Marca</b></div></td>
                                    <?php if (FORMATO_IMPRESION == 4): ?>
                                        <td width="15%"><div align="center"><b>P.VENTA</b></div></td>
                                        <td width="15%"><div align="center"><b>P.COSTO</b></div></td>
                                    <?php endif; ?>
                                <?php } ?>
                                <td width="5%"><div align="center"></div></td>
                            </tr>
                            <?php
                            $indice = 0;
                            foreach ($lista as $valor) {
                                $classfila = $indice % 2 == 0 ? "itemImparTabla" : "itemParTabla";
                                ?>
                                <tr class="<?php echo $classfila; ?>">
                                    <td><div align="center"><?php echo $valor[0]; ?></div></td>
                                    <td><div align="center"><?php echo $valor[9]; ?></div></td>
                                    <?php if (FORMATO_IMPRESION != 4): ?>
                                        <td><div align="center"><?php echo $valor[1]; ?></div></td>
                                    <?php endif; ?>
                                    <td><div align="left"><?php echo $valor[2]; ?></div></td>
                                    <?php if ($flagBS == 'B') { ?>
                                        <?php if (FORMATO_IMPRESION != 4): ?>
                                            <td><div align="left"><?php echo $valor[3]; ?></div></td>
                                        <?php endif; ?>
                                        <td><div align="left"><?php echo $valor[4]; ?></div></td>
                                        <td><div align="left"><?php echo $valor[5]; ?></div></td>
                                        <?php if (FORMATO_IMPRESION == 4): ?>           
                                            <td><div align="left"><?php echo $valor[6]; ?></div></td>
                                            <td><div align="left"><?php echo $valor[7]; ?></div></td>
                                        <?php endif; ?>
                                    <?php } ?>
                                    <td><div align="left"><?php echo $valor[8]; ?></div></td>
                                </tr>
                                <?php
                                $indice++;
                            }
                            ?>
                        </table>
                        <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>" />
                        <input type="hidden" name="flagBS" id="flagBS" value="<?php echo $flagBS; ?>" />
                    </div>
                </form>
                <div style="margin-top:15px" class="fuente8_2"><?php echo $paginacion; ?></div>
        </div>
    </body>
</html>