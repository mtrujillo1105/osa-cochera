<!DOCTYPE html>
<html lang="es">
<head>
    <title><?php echo TITULO; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto_popup.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>
</head>
<body>
<div align="center">
    <form name="form_busqueda" id="form_busqueda" method="post"
          action="<?php echo site_url('almacen/producto/ventana_busqueda_producto/' . $flagBS); ?>">
        <div id="frmBusqueda" style="width:95%">
            <table class="fuente8_2" width="100%" cellspacing='0' cellpadding='3' border='0'>
                <tr class="cabeceraTabla" height="25px">
                    <td align="center"
                        colspan="3"><?php if ($flagBS == 'B') echo 'ARTICULOS'; else echo 'SERVICIOS'; ?></td>
                </tr>
            </table>
        </div>
        <div id="lineaResultado" style="width:95%; margin-top:10px">
            <table class="fuente8_2" width="100%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                	<td>Almacen*</td>
                    <td>
                    	<select name="almacen" id="almacen" class="comboGrande" style="width:307px">
								<?php  foreach ($listaAlmacen as $codigoAlmacen=>$valor){ ?>
								<option <?php echo ($codigoAlmacen == $almacen) ?'selected="selected"':''; ?>
                                            value="<?php echo $codigoAlmacen; ?>"><?php echo $valor;?>
								</option>
								<?php  } ?>
                        </select>
                    
                    
                    </td>
                </tr>
                <tr>
                    <td width="50%" align="left">N° de registros encontrados:&nbsp;<?php echo $registros; ?></td>
                    <td width="50%" align="right">&nbsp;</td>
                    
                </tr>
                
            </table>
        </div>
        <div id="frmResultado" style="width:98%; height: 370px; overflow: auto; background-color: #f5f5f5">
            <table class="fuente8_2" width="100%" id="tabla_resultado" name="tabla_resultado" align="center" border="0"
                   cellspacing=1 cellpadding="3">
                <tr class="cabeceraTabla">
                    <td width="5%">
                        <div align="center"><b>ITEM</b></div>
                    </td>

                    <td width="10%">
                        <div align="center"><b>CÓDIGO</b></div>
                    </td>
                    <td width="45%">
                        <div align="center"><b>NOMBRE</b></div>
                    </td>
                    <?php if ($flagBS == 'B') { ?>
                        <td width="10%">
                            <div align="center"><b>SERIE</b></div>
                        </td>
                        <td width="20%">
                            <div align="center"><b>FAMILIA</b></div>
                        </td>
                        <td width="15%">
                            <div align="center"><b>Marca</b></div>
                        </td>
                        <td width="15%">
                            <div align="center"><b>STOCK</b></div>
                        </td>
                    <?php } ?>
                    <td width="5%">
                        <div align="center"></div>
                    </td>
                </tr>
                <?php
                $indice = 0;
                foreach ($lista as $valor) {
                    $classfila = $indice % 2 == 0 ? "itemImparTabla" : "itemParTabla";
                    ?>
                    <tr class="<?php echo $classfila;?>">
                        <td>
                            <div align="center"><?php echo $valor[0];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[1];?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[2];?></div>
                        </td>
                        <?php if ($flagBS == 'B') { ?>
                            <td>
                                <div align="left"><?php echo $valor[3]; ?></div>
                            </td>
                            <td>
                                <div align="left"><?php echo $valor[4]; ?></div>
                            </td>
                            <td>
                                <div align="left"><?php echo $valor[5]; ?></div>
                            </td>
                            <td>
                                <div align="center"><?php echo $valor[6]; ?></div>
                            </td>
                        <?php } ?>
                        <td>
                            <div align="left"><?php echo $valor[7];?></div>
                        </td>
                    </tr>
                    <?php
                    $indice++;
                }
                ?>
            </table>

            <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>"/>
            <input type="hidden" name="flagBS" id="flagBS" value="<?php echo $flagBS; ?>"/>
            <input type="hidden" name="tipo_oper" id="tipo_oper" value="<?php echo $tipo_oper; ?>"/>
            <input type="hidden" name="buscar_producto" id="buscar_producto" value="<?php echo $buscar_producto; ?>"/>
            
            
        </div>
        <div style="margin-top:15px" class="fuente8_2"><?php echo $paginacion; ?></div>
    </form>
</body>
</html>