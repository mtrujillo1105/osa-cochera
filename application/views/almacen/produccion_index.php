<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
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
                        <ul id="generarOC" class="lista_botones">
                            <li id="nuevo">Generar OC</li>
                        </ul>
                        <ul id="nuevaOProduccion" class="lista_botones">
                            <li id="nuevo">Nueva O. Producción</li>
                        </ul>
                        <ul id="cancelarProduccion" class="lista_botones">
                            <li id="limpiar">Limpiar</li>
                        </ul>
                    </div>
                    <div id="lineaResultado">
                        <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="50%" align="left">N de registros encontrados: &nbsp;<?php echo $registros; ?> </td>
                        </table>
                    </div>
                </div>  
                </div>
                
                <a id='ingresar_series' class='fancybox' href='"<?php echo base_url(); ?>"index.php/almacen/producto/ventana_nueva_serie/'></a>

                <div id="cabeceraResultado" class="header">Ordenes de producción</div>
                <div id="frmResultado">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                    </div>
                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1" style="text-align: center;">
                        <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
                            <td width="45%">SEDE</td>
                            <td width="5%">SERIE</td>
                            <td width="5%">NUMERO</td>
                            <td width="5%">ELABORACIÓN</td>
                            <td width="5%">ENTREGA</td>
                            <td colspan="4" width="15%">ESTADO</td>
                        </tr><?php
                        if(count($lista)>0){
                            foreach($lista as $indice => $valor){
                                $class = ($indice % 2 == 0) ?'itemParTabla':'itemImparTabla'; ?>
                                <tr class="<?php echo $class;?>">
                                            <td><div align="center"><?php echo $valor[0];?></div></td>
                                            <td><div align="left"><?php echo $valor[1];?></div></td>
                                            <td><div align="center"><?php echo $valor[2];?></div></td>
                                            <td><div align="center"><?php echo $valor[3];?></div></td>
                                            <td><div align="center"><?php echo $valor[4];?></div></td>
                                            <td><div align="center"><?php echo $valor[5];?></div></td>
                                            <td><div align="center"><?php echo $valor[6];?></div></td>

                                            <td><div align="center"><?php echo $valor[7];?></div></td>
                                            <td><div align="center"><?php echo $valor[8];?></div></td>
                                            <td><div align="center"><?php echo $valor[9];?></div></td>
                                        </tr> <?php
                                  }
                                }
                                else{ ?>
                                    <tr>
                                      <td colspan="9" class="mensaje">No hay ning&uacute;n pedido que cumpla con los criterios de b&uacute;squeda</td>
                                    </tr><?php
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