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
    <div id="botonBusqueda">
       <!-- <ul id="imprimirOcompra" class="lista_botones">
            <li id="imprimir" style="background-position:44px 4px;width:90px;">Imprimir</li>
        </ul>-->
        <?php
        if ($evalua) {
            ?>
            <ul id="nuevoOcompa" class="lista_botones">
                <li id="nuevo" style="background-position:44px 4px;width:90px;">Nueva O.
                    de <?php if ($tipo_oper == 'V') echo 'Venta'; else echo 'Compra'; ?></li>
            </ul>
        <?php
        }
        ?>
        <ul id="limpiarOcompra" class="lista_botones">
            <li id="limpiar" style="background-position:44px 4px;width:90px;">Limpiar</li>
        </ul>
        <ul id="buscarOcompra" class="lista_botones">
            <li id="buscar" style="background-position:44px 4px;width:90px;">Buscar</li>
        </ul>
        <?php if ($evalua == true) { ?>
          <!--  <ul id="desaprobarOcompra" class="lista_botones">
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
                <td width="50%" align="left">N de ordenes
                    de <?php if ($tipo_oper == 'V') echo 'venta'; else echo 'compra'; ?>
                    encontrados:&nbsp;<?php echo $registros; ?> </td>
            </tr>
        </table>
    </div>
</div>
<div id="cabeceraResultado" class="header"><?php echo $titulo_tabla; ?></div>
<div id="frmResultado">
    <form id="frmEvaluar" name="frmEvaluar" method="post"
          action="<?php echo base_url(); ?>index.php/compras/ocompra/evaluar_ocompra/">
        <input type="hidden" value="" id="flag" name="flag"/>
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
            <tr class="cabeceraTabla">
              <!--  <td width="3%"><?php if ($evalua == true) echo "<input type='checkbox' name='checkTodos' id='checkTodos' value='1'>"; ?></td>-->
                <td width="3%">ITEM</td>
                <td width="7%">FECHA</td>
                <td width="5%">NUMERO</td>
                <td width="5%">PRESUPUESTO</td>
                <td width="31%">RAZON SOCIAL</td>
                <td width="7%">C.INGRESO</td>
                <td width="9%">TOTAL</td>
                <td width="4%">ESTADO</td>
                <td width="4%">&nbsp;</td>
                <td width="4%">&nbsp;</td>
                <td width="4%">&nbsp;</td>
                <td width="4%">&nbsp;</td>
            </tr>
            <?php
            if (count($lista) > 0) {
                foreach ($lista as $indice => $valor) {
                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class; ?>">
                       <!-- <td>
                            <div align="center"><?php echo $valor[0]; ?></div>
                        </td>-->
                        <td>
                            <div align="center"><?php echo $valor[1]; ?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[2]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[3]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[4]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[6]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[7]; ?></div>
                        </td>
                        <td>
                            <div align="right"><?php echo $valor[8]; ?></div>
                        </td>
                        <td>
                            <div align="center">
                                <?php
                                echo $valor[10];
                                ?>
                            </div>
                        </td>
                        <td>
                            <div align="center"><?php //echo $valor[11];
                                ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[12]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[13]; ?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[14]; ?></div>
                        </td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                    <tbody>
                    <tr>
                        <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con
                            los
                            criterios de b&uacute;squeda
                        </td>
                    </tr>
                    </tbody>
                </table>
            <?php
            }
            ?>





            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


            <tr height="28" class="itemParTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>

            <tr height="28" class="itemImparTabla">
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="left"></div>
                </td>
                <td>
                    <div align="right"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
                <td>
                    <div align="center"></div>
                </td>
            </tr>


        </table>
    </form>
</div>
<div style="margin-top: 15px;"><?php echo $paginacion; ?></div>