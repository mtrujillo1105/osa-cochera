<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
</head>
<body>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
            <div id="cuerpoPagina">
                <form id="frmpublicar" name="frmpublicar" method="post" enctype="multipart/form-data" action="">
                    <div class="cargarBusqueda">
                        <div class="acciones">
                            <div id="botonBusqueda">
                                <ul id="nuevaODespacho" class="lista_botones">
                                    <li id="nuevo">Nueva O. Despacho</li>
                                </ul>
                                <ul id="cancelarDespacho" class="lista_botones">
                                    <li id="limpiar">Limpiar</li>
                                </ul>
                            </div>
                            <div id="lineaResultado">
                            </div>
                        </div>  
                    </div>
                </form>
                
                <div style="width:100%">
                    <br style="line-height: 4.5em;">
                    <span class="btn-calendario">
                        <div class="btn btn-warning">Calendario</div>
                        <hr>

                        <section class="calendario">
                            <div class="responsive-calendar">
                                <div class="controls">
                                    <span class="pull-left" data-go="prev">
                                        <div class="btn btn-primary">Anterior</div>
                                    </span>
                                    <h4>
                                        <span data-head-month></span>
                                        <span> del </span>
                                        <span data-head-year></span>
                                    </h4>
                                    <span class="pull-right" data-go="next">
                                        <div class="btn btn-primary">Siguiente</div>
                                    </span>
                                </div>
                                <br>
                                <div class="day-headers">
                                    <div class="day header">Lunes</div>
                                    <div class="day header">Martes</div>
                                    <div class="day header">Miercoles</div>
                                    <div class="day header">Jueves</div>
                                    <div class="day header">Viernes</div>
                                    <div class="day header">Sabado</div>
                                    <div class="day header">Domingo</div>
                                </div>
                                <div class="days" data-group="days"></div>
                            </div>
                        </section>
                    </span>
                </div>
                <br style="line-height: 4.5em;">

                <div id="frmResultado">
                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1" style="text-align: center;">
                        <tr class="cabeceraTabla">
                            <td width="10%">ITEM</td>
                            <td width="10%">SERIE</td>
                            <td width="10%">NÚMERO</td>
                            <td width="10%">FECHA</td>
                            <td colspan="4">ESTADO</td>
                        </tr><?php
                        if(count($lista)>0){
                            foreach($lista as $indice => $valor){
                                $class = ($indice % 2 == 0) ?'itemParTabla':'itemImparTabla'; ?>
                                <tr class="<?=$class;?>">
                                    <td><div align="center"><?=$valor[0];?></div></td>
                                    <td><div align="center"><?=$valor[1];?></div></td>
                                    <td><div align="center"><?=$valor[2];?></div></td>
                                    <td><div align="center"><?=$valor[3];?></div></td>
                                    
                                    <td width="70%"><div align="center"><?=$valor[7];?></div></td>
                                    <td><div align="center"><?=$valor[4];?></div></td>
                                    <td><div align="center"><?=$valor[5];?></div></td>
                                    <td><div align="center"><?=$valor[6];?></div></td>
                                </tr> <?php
                            }
                        }
                        else{ ?>
                            <tr>
                                <td colspan="5" class="mensaje">No hay ning&uacute;n pedido que cumpla con los criterios de b&uacute;squeda</td>
                            </tr><?php
                        } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<?php
    $fechasScript = "";
    if (count($calendario) > 0) {
        $j = 0;
        foreach ($calendario as $indice => $despacho) {
            if ($j > 0)
                $fechasScript .= ",";
            $fechasScript .= "\"$despacho[1]\" : {\"number\": $despacho[2], \"url\": \"despacho_index/0/$despacho[1]\"}";
            $j++;
        }
    }
    # "2019-10-26": {"number": 1, "url": "http://w3widgets.com"},
?>
<script type="text/javascript">
      $(document).ready(function () {
        $(".responsive-calendar").responsiveCalendar({
          time: '<?=date("Y-m");?>',
          events: { <?=$fechasScript;?> }
        });
      });
</script>
</html>