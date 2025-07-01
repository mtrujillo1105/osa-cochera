<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js"></script>
</head>
<body>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
            <div id="frmBusqueda">
                <form id="form_busqueda2" name="form_busqueda2" method="post" action="<?php echo $action; ?>">
                     <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                            <!--<tr>
                                <td width="16%">Código de Articulo:</td>
                                <td>
                                <input id="txtCodigo" type="text" class="cajaPequena" name="txtCodigo" placeholder="Codigo" maxlength="30" size="50" value="<?php echo $codigo;?>">
                                </td>
                            </tr>-->
                            <tr>
                                <td>Receta: </td>
                                <td><input id="txtNombre" name="txtNombre" type="text" class="cajaGrande" maxlength="100" placeholder="Nombre producto" value="<?php echo $nombre; ?>"></td>
                            </tr>
                     </table>
                </form>
            </div>

            <div id="cuerpoPagina" >
                <form id="frmpublicar" name="frmpublicar" method="post" enctype="multipart/form-data" action="">
                <div class="cargarBusqueda">
                  <div class="acciones">
                    <div id="botonBusqueda">
                        <ul id="nuevaReceta" class="lista_botones">
                            <li id="nuevo">Receta</li>
                        </ul>
                        <ul id="cancelarReceta" class="lista_botones">
                            <li id="limpiar">Limpiar</li>
                        </ul>
                        <ul id="buscarProductoReceta" class="lista_botones">
                            <li id="buscar">Buscar</li>
                        </ul>
                    </div>
                    <div id="lineaResultado">
                        <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="50%" align="left">N de recetas encontradas: &nbsp;<?php echo $registros; ?> </td>
                        </table>
                    </div>
                </div>  
                </div>
                
                <a id='ingresar_series' class='fancybox' href='"<?php echo base_url(); ?>"index.php/almacen/producto/ventana_nueva_serie/'></a>

                <div id="cabeceraResultado" class="header">Lista De Recetas</div>
                <div id="frmResultado">
                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1" style="text-align: center;">
                        <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
                            <td width="70%" align='center'>NOMBRE DE LA RECETA</td>
							<td align='center'>ACCIONES</td>
                        </tr>
                        <?php
                        if (count($lista) > 0) {
                            foreach ($lista as $indice => $valor){
                                $class = ($indice % 2 == 0) ? 'itemParTabla' : 'itemImparTabla'; ?>
                                    <tr class="<?=$class;?>">
                                	<td><?=$valor[0];?></td>
                                	<td style="text-align: left;"><?=$valor[1];?></td>
                                	<td><?=$valor[2];?>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?=$valor[3];?>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?=$valor[4];?>
                                    </td>
								</tr> <?php
                            }
                        } ?>
                    </table>
                </div>
               <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
                <input type="hidden" id="iniciopagina" name="iniciopagina">
            </form>
            </div>
        </div>
    </div>

</div>
</body>
</html>