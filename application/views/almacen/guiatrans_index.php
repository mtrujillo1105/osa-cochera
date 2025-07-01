<script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/guiatrans.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>"
      media="screen"/>
<style>
    .busqueda_transferencia{
        position: relative;
        text-align: center;
    }

    .realizadas{
        position: absolute;
        background-color: #004488;
        color: #f1f4f8;
        width: 98px;
        height: 70px;
        top: 1px;
        left: 156px;
        -webkit-box-shadow: 0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        -moz-box-shadow:    0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        box-shadow:         0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        cursor: pointer;
    }

    .realizadas_control .seleccionado{
        position: absolute;
        border-radius: 3px;
        background-color: #29fb00;
        width: 98px;
        height: 5px;
        bottom: 6px;
        left: 156px;
    }

    .recibidas{
        position: absolute;
        background: #109EC8;
        color: #f1f4f8;
        width: 95px;
        height: 70px;
        top: 1px;
        right: 102px;
        cursor: pointer;
        -webkit-box-shadow: 0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        -moz-box-shadow:    0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        box-shadow:         0px 0px 0px 3px rgba(47, 50, 50, 0.34);
    }

    .recibidas_control .seleccionado{
        position: absolute;
        border-radius: 3px;
        background-color: #ab1c27;
        width: 96px;
        height: 5px;
        bottom: 6px;
        right: 101px;
    }
</style>
<script language="javascript">
    $(document).ready(function () {

        $('#idSeleccionado_realizadas').click(function(){
            var seleccionado = Number($('#seleccionado_realizadas').val());
            if(seleccionado == 0){
                $('.realizadas_control .seleccionado').css('background', '#29fb00');
                $('#seleccionado_realizadas').val("1");
            }else{
                $('.realizadas_control .seleccionado').css('background', '#ab1c27');
                $('#seleccionado_realizadas').val("0");
            }
        });

        $('#idSeleccionado_recibidas').click(function(){
            var seleccionado = Number($('#seleccionado_recibidos').val());
            if(seleccionado == 0){
                $('.recibidas_control .seleccionado').css('background', '#29fb00');
                $('#seleccionado_recibidos').val("1");
            }else{
                $('.recibidas_control .seleccionado').css('background', '#ab1c27');
                $('#seleccionado_recibidos').val("0");
            }
        });


        $("a#linkVerProducto , a#linkVerProveedor").fancybox({
            'width': 700,
            'height': 450,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': false,
            'modal': true,
            'type': 'iframe'
        });


        $("a#linkEnviarPersonal").fancybox({
            'width': 700,
            'height': 350,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': false,
            'modal': true,
            'type': 'iframe'
        });
        $("a#linkRecepcionar").fancybox({
            'width': 700,
            'height': 350,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': false,
            'modal': true,
            'type': 'iframe'
        });

        $('#movimiento').change(function(){
            busqueda_transferencia("0");
        });

        $('#buscarGuiatrans').click(function(){
            busqueda_transferencia("0");
        });

    });

    function seleccionar_producto(codigo, interno, familia, stock, costo) {
        $("#producto").val(codigo);
        $("#codproducto").val(interno);

        base_url = $("#base_url").val();
        url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + codigo;
        $.getJSON(url, function (data) {
            $.each(data, function (i, item) {
                nombre_producto = item.PROD_Nombre;
            });
            $("#nombre_producto").val(nombre_producto);
        });
    }

    function busqueda_transferencia(estado)
    {
        var url = $('#form_busqueda').attr('action');
        var dataString = $('#form_busqueda').serialize();
        console.log(url);
        $.ajax({
            type: "POST",
            url: url,
            data: dataString,
            beforeSend: function (data) {
                $('#cargando_datos').show();
            },
            error: function (XRH, error) {
                $('#cargando_datos').hide();
                console.log(error);
            },
            success: function (data) {
                $('#frmResultado').html(data);
                $('#cargando_datos').hide();
            }

        });
    }

</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
            <div id="frmBusqueda">
                <form id="form_busqueda" name="form_busqueda" method="post"
                      action="<?php echo base_url(); ?>index.php/almacen/guiatrans/buscar">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0" >
                        <tr>
                            <td align='left' width="10%">Fecha inicial</td>
                            <td align='left' width="40%">
                                <input name="fechai" id="fechai" value="" type="text"
                                       class="cajaGeneral" size="10" maxlength="10"  onfinishinput="busqueda_transferencia('0');" />
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1"
                                     id="Calendario1" width="16" height="16" border="0"
                                     onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechai",      // id del campo de texto
                                        ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario1"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                                <label style="margin-left: 90px;">Fecha final</label>
                                <input name="fechaf" id="fechaf" value="" type="text"
                                       class="cajaGeneral" size="10" maxlength="10"  onfinishinput="busqueda_transferencia('0');" />
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2"
                                     id="Calendario2" width="16" height="16" border="0"
                                     onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaf",      // id del campo de texto
                                        ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario2"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                            </td>
                            <td rowspan="4" width="50%" class="busqueda_transferencia" >
                                <div class="realizadas_control" >
                                    <span class="realizadas" role="button" aria-checked="true" id="idSeleccionado_realizadas" >
                                        Trans.<br>
                                        Realizadas
                                    </span>
                                    <span class="seleccionado" ></span>
                                    <input type="hidden" id="seleccionado_realizadas" value="1" name="seleccionado_realizadas" />
                                </div>
                                <div class="recibidas_control">
                                    <span class="recibidas" id="idSeleccionado_recibidas" >
                                        Trans.<br>
                                        Recibidas
                                    </span>
                                    <span class="seleccionado" ></span>
                                    <input type="hidden" id="seleccionado_recibidos" value="0" name="seleccionado_recibidos" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align='left'>Número</td>
                            <td align='left'><input type="text" name="serie" id="serie" value="" placeholder="Serie" maxlength="3"
                                                    class="cajaGeneral" size="3" />
                                <input type="text" name="numero" id="numero" value="" placeholder="Numero"
                                       class="cajaGeneral" size="10" maxlength="6" onfinishinput="busqueda_transferencia('0');"/>
                            </td>
                        </tr>
                        <tr>
                            <!--<td align='left'>Artículo</td>
                            <td align='left'> Se puede usar mas adelante
                                <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10"
                                       maxlength="11"/>
                                <input name="codproducto" type="text" value="<?php echo $codproducto; ?>"
                                       class="cajaPequena" id="codproducto" size="10" maxlength="11"
                                       onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');"/>
                                <input NAME="nombre_producto" type="text" value="<?php echo $nombre_producto; ?>"
                                       class="cajaGrande cajaSoloLectura" id="nombre_producto" size="40"
                                       readonly="readonly"/>
                                <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/"
                                   id="linkVerProducto"><img height='16' width='16'
                                                             src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>'
                                                             title='Buscar' border='0'/></a>
                            </td>-->
                            <td align="left" >
                                Movimiento
                            </td>
                            <td>
                                <select class="cajaGrande" name="movimiento" id="movimiento" >
                                    <option value="0">::Completo::</option>
                                    <option value="1">Pendiente</option>
                                    <option value="2">Enviado</option>
                                    <option value="3">Transito</option>
                                    <option value="4">Devolucion</option>
                                    <option value="5">Recibido</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
                <!--<div class="busqueda_transferencia" >
                    <div class="realizadas" >
                        Realizadas
                    </div>
                    <div class="recibidas" >
                        Recibidas
                    </div>
                </div>-->
            </div>
            <div class="acciones">
                <span id="mensajeTransferencia" style="margin-top: 10px"></span>

                <div id="botonBusqueda">
                    <ul id="imprimirGuiatrans" class="lista_botones">
                        <li id="imprimir">Imprimir</li>
                    </ul>
                    <ul id="nuevaGuiatrans" class="lista_botones">
                        <li id="nuevo">Nueva G. Transferencia</li>
                    </ul>
                    <ul id="limpiarGuiatrans" class="lista_botones">
                        <li id="limpiar">Limpiar</li>
                    </ul>
                    <ul id="buscarGuiatrans" class="lista_botones">
                        <li id="buscar">Buscar</li>
                    </ul>
                </div>
            </div>
            <div id="cabeceraResultado" class="header"><label style="margin-right:140px;">TRANSFERENCIAS
                    REALIZADAS</label> <label style="margin-left:135px;">TRANSFERENCIAS RECIBIDAS</label></div>
            <div id="frmResultado">
                <div style="width:55%; float: left;">
                    <table class="fuente8_2_4" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                        <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
                            <td width="9%">FECHA</td>
                            <td width="3%">SERIE</td>
                            <td width="5%">NUMERO</td>
                            <td>ALMACEN DESTINO</td>
                            <td align="left" >MOVIMIENTO</td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
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
                                            <div align="left">
                                                <?php
                                                // Variables necesarios para el cambio de estado
                                                // Estado y Codigo de GuiaTransferencia
                                                $enviot = $valor[5] . "," . $valor[10];

                                                switch ($valor[5]) {
                                                    // Devolucion
                                                    case 3:
                                                        echo "<a href='#' id='idDevolucion' ><div style='width:70px; height:17px; background-color: #5baba8; text-align:center'>Devolucion</div></a>";
                                                        break;
                                                    // Recibido
                                                    case 2:
                                                        echo "<a href='#' id='idRecibido' ><div style='width:70px; height:17px; background-color: #00D269; text-align:center'>Recibido</div></a>";
                                                        break;
                                                    // Transito
                                                    case 1:
                                                        if ($valor[13] == $this->somevar['compania']) { // Devolucion
                                                            // Se cambia la logica de envio de estado por que se devuelve su
                                                            $enviot = ($valor[5]+1) . "," . $valor[10];
                                                            echo "<a href='#' id='trans$valor[10]' title='Enviado correctamente, puede cancelar el envio dando un click' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: orange; text-align:center' >Enviado</div></a>";
                                                        } else { // Transito
                                                            echo "<a href='#' id='trans$valor[10]' title='Transferencia enviada correctamente' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: yellow; text-align:center'>Transito</div></a>";
                                                        }
                                                        break;
                                                    // Pendiente
                                                    case 0:
                                                        if ($valor[12] == 0) {
                                                            echo "<a href='#' id='linkAnulado' ><div style='width:70px; height:17px; background-color: #ab080c; text-align:center; color: #f1f1f1' >Anulado</div></a>";
                                                        } else {
                                                            echo "<a href='#' id='trans$valor[10]' title='Transferencia pediente, falta confirmar' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: #FF6464; text-align:center'>Pendiente</div></a>";
                                                        }
                                                        break;
                                                }

                                                ?></div>
                                        </td>

                                    <?php
                                    if ($valor[5] == 0) {
                                        if ($valor[12] == 0) { ?>
                                            <td>
                                                <div align="center"><?php echo $valor[6]; ?></div>
                                            </td> <?php
                                        } else { ?>
                                            <td>
                                                <div align="center">
                                                    <a title="Cancelar la transferencia" href='<?php echo base_url(); ?>index.php/seguridad/usuario/ventana_confirmacion_usuario2/guiatrans/<?php echo $valor[10]; ?>' id='linkVerProveedor'>
                                                        <img src="<?php echo base_url(); ?>public/images/icons/error.png?=<?=IMG;?>" width="14px" height="14px">
                                                    </a>
                                                </div>
                                            </td> <?php
                                        }
                                    }
                                    else { ?>
                                        <td> <?php
                                            if ($valor[5] == 0) { ?>
                                                <div align="center"><a href="<?php echo base_url(); ?>"><?php echo $valor[6]; ?></a>
                                                </div> <?php
                                            }else
                                            if($valor[5] == 1) { ?>
                                                <div align="center"><img src="<?php echo base_url(); ?>public/images/icons/transito.png?=<?=IMG;?>" align="G" width="14px" height="14px" ></div> <?php
                                            }else{ ?>
                                                <div align="center"><!--<img src="<?php echo base_url(); ?>public/images/icons/active.png?=<?=IMG;?>" align="G" width="14px" height="14px" >--></div>
                                            <?php
                                            }

                                            ?>
                                        </td>

                                    <?php } ?>
                                    <td>
                                        <?php

                                        if ($valor[5] == 0) {

                                            if($valor[12] != '1'){
                                                ?>
                                                <img src='<?php echo base_url(); ?>public/images/icons/inactive.png?=<?=IMG;?>' alt='W' width='14px' height='14px' >
                                            <?php
                                            }else{
                                                ?>
                                                <div align="center"><?php echo $valor[7]; ?></div>
                                            <?php
                                            }

                                            ?>
                                        <?php
                                        }else if($valor[5] == 1) {
                                            ?>
                                            <img src='<?php echo base_url(); ?>public/images/icons/devolucion.png?=<?=IMG;?>' alt='Devolucion' width='14px' height='14px' >
                                        <?php
                                        }else{                                            ?>
                                            <div align="center"><img src="<?php echo base_url(); ?>public/images/icons/completado.png?=<?=IMG;?>" align="G" width="14px" height="14px" ></div>
                                        <?php
                                        }

                                        ?>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[8]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[9]; ?></div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td width="100%" class="mensaje" colspan="11">No hay ning&uacute;n registro que cumpla
                                    con los criterios de b&uacute;squeda
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
                <div style="width:44%; margin-left:1%; float: left;">
                    <table class="fuente8_2_4" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                        <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
                            <td width="9%">FECHA</td>
                            <td width="3%">SERIE</td>
                            <td width="5%">NUMERO</td>
                            <td>ALMACEN ORIGEN</td>
                            <td align="left" >MOVIMIENTO</td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
                        </tr>
                        <?php
                        if (count($lista_recibidos) > 0) {
                            foreach ($lista_recibidos as $indice => $valor) {
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
                                        <div align="left">
                                            <?php
                                            $enviot = $valor[5] . "," . $valor[10];
                                            switch ($valor[5]) {
                                                // Devolucion
                                                case 3:
                                                    echo "<a href='#' id='idDevolucion' ><div style='width:70px; height:17px; background-color: #5baba8; text-align:center'>Devolucion</div></a>";
                                                    break;
                                                // Recibido
                                                case 2:
                                                    echo "<a href='#' id='idRecibido2' ><div style='width:70px; height:17px; background-color: #00D269; text-align:center'>Recibido</div></a>";
                                                    break;
                                                // Transito
                                                case 1:
                                                    echo "<a href='#' id='trans$valor[10]' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: yellow; text-align:center'>Transito</div></a>";
                                                    break;
                                                // Pendiente
                                                case 0:
                                                    if ($valor[12] == 0) {
                                                        echo "<a href='#' id='linkAnulado' ><div style='width:70px; height:17px; background-color: #ab080c; text-align:center; color: #f1f1f1' >Anulado</div></a>";
                                                    } else {
                                                        if ($valor[13] != $this->somevar['compania']) {
                                                            $link = "#";
                                                            $idLink = "linkEnviarProhibido";
                                                        } else {
                                                            $link = "#";
                                                            $idLink = "linkEnviarProhibido";
                                                        }
                                                        echo "<a href='" . $link . "'  id='" . $idLink . "'><div style='width:70px; height:17px; background-color: #FF6464; text-align:center'>Pendiente</div></a>";
                                                    }
                                                    break;
                                            }
                                            ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[8]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[9]; ?></div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td width="100%" class="mensaje" colspan="11">No hay ning&uacute;n registro que cumpla
                                    con los criterios de b&uacute;squeda
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div id="cargando_datos" style="display: none;position: absolute;
                     width: 100%; height: 100%; left: 0; top: 0px;
                     z-index: 9999">
                <div align="center" style="background: #FFF;
                         z-index: 9999;
                         position: relative;
                         top: 40%; margin: 0 auto; width: 140px; height: 32px;padding: 30px 40px; border: 1px solid #cccccc;"
                     class="fuente8">
                    <b>ESPERE POR FAVOR...</b><br>
                    <img src="<?php echo base_url() ?>public/images/icons/cargando.gif?=<?=IMG;?>" border='0'/>
                </div>
            </div>
            <?php echo $oculto; ?>
            <?php echo $codUsuario; ?>
        </div>
         </div>
    </div>