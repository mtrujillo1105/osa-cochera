<script type="text/javascript" src="<?php echo base_url(); ?>public/js/compras/ocompra.js?=<?=JS;?>"></script>
<script>
    $(document).ready(function(){
        $("#buscarOcompra").click(function(){
            busqueda_ocompra();
        });
        $("#nuevoOcompa").click(function(){
            url = base_url+"index.php/compras/ocompra/nueva_ocompra/"+tipo_oper;
            location.href = url;
        });
        $("#limpiarOcompra").click(function(){
            url = base_url+"index.php/compras/ocompra/ocompras/0/"+tipo_oper;
            location.href = url;
        });
    });

    function busqueda_ocompra()
    {
        var url = $('#form_busqueda').attr('action');
        var dataString = $('#form_busqueda').serialize();
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
                $('#cargarBusqueda').html(data);
                $('#cargando_datos').hide();
            }

        });
    }

</script>
                <div class="acciones">
                    <div id="botonBusqueda"> <?php
                        if ($tipo_oper == "V"){ ?>
                            <ul id="imprimirOcompra" class="lista_botones">
                                <li id="imprimir">Imprimir</li>
                            </ul><?php
                        }

                        if ($evalua) { ?>
                            <ul id="nuevoOcompa" class="lista_botones">
                                <li id="nuevo">Nueva Cotiz. de <?php if ($tipo_oper == 'V') echo 'Venta'; else echo 'Compra'; ?></li>
                            </ul> <?php
                        } ?>
                        <ul id="buscarOcompra" class="lista_botones">
                            <li id="buscar">Buscar</li>
                        </ul>
                        <ul id="limpiarOcompra" class="lista_botones">
                            <li id="limpiar">Limpiar</li>
                        </ul>
                        <?php if ($evalua == true) { ?>
                           <!-- <ul id="desaprobarOcompra" class="lista_botones">
                                <li id="desaprobar" style="background-position:44px 4px;width:90px;">Desaprobar</li>
                            </ul>
                            <ul id="aprobarOcompra" class="lista_botones">
                                <li id="aprobar" style="background-position:44px 4px;width:90px;">Aprobar</li>
                            </ul>-->
                        <?php } ?>
                    </div>
                    <div id="lineaResultado">
                        <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="30%" align="left">N de registros encontrados:&nbsp;<?php echo $registros; ?> </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla; ?></div>
                <div id="frmResultado">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                    </div>
                    <form id="frmEvaluar" name="frmEvaluar" method="post" action="<?php echo base_url(); ?>index.php/compras/ocompra/evaluar_ocompra/">
                        <input type="hidden" value="" id="flag" name="flag"/>
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                            <tr class="cabeceraTabla">
                                <td width="15%">FECHA</td>
                                <td width="05%">NUMERO</td>
                                <td width="05%">ID CLIENTE</td>
                                <td width="29%">RAZON SOCIAL</td>
                                <td width="07%">ENTREGA</td>
                                <td width="07%">TOTAL</td>
                                <td colspan="10">ESTADO</td>
                            </tr>
                            <tr class="cabeceraTabla">
                                <td width="03%">&nbsp;</td>
                                <td width="07%">&nbsp;</td>
                                <td width="05%">&nbsp;</td>
                                <td width="29%">&nbsp;</td>
                                <td width="07%">&nbsp;</td>
                                <td width="06%">&nbsp;</td>
                                <td>ESTADO</td>
                                <td colspan="4">OPCIONES</td>
                                <td>&nbsp;</td>
                                <!--<td>GUIA R.</td>-->
                                <td>&nbsp;</td>
                                <td>COMPROBANTES</td>
                            </tr>
                            <?php
                            if (count($lista) > 0) {
                                foreach ($lista as $indice => $valor) {
                                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla'; ?>
                                    <tr class="<?php echo $class; ?>">
                                        <td>
                                            <div align="left"><?=$valor[2];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[3];?></div>
                                        </td>
                                        <td>
                                            <div align="left"><?=$valor[18];?></div>
                                        </td>
                                        <td>
                                            <div align="left"><?=$valor[6];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[7];?></div>
                                        </td>
                                        <td>
                                            <div align="right"><?=$valor[8];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[10];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[12];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[13];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[14];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[19];?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[15];?></div>
                                        </td>
                                        <!--<td>
                                            <div align="left"><span class='icon-loading loading_g_<?=$valor[1];?>'></span></div>
                                        </td>
                                        <td>
                                            <div align="left"><?=$valor[16];?></div>
                                        </td>-->
                                        <td>
                                            <div align="left"><span class='icon-loading loading_c_<?=$valor[1];?>'></span></div>
                                        </td>
                                        <td>
                                            <div align="left" class="cResult_<?=$valor[1]?>"><?=$valor[17];?></div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                    <tbody>
                                        <tr>
                                            <td width="100%" class="mensaje">No hay ningún registro que cumpla con los criterios de búsqueda</td>
                                        </tr>
                                    </tbody>
                                </table> <?php
                            } ?>
                        </table>
                    </form>
                </div>
                <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>