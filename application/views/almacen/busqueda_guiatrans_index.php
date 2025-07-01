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
                                            $enviot = 2 . "," . $valor[10];
                                            echo "<a href='#' title='Enviado correctamente, puede cancelar el envio dando un click' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: orange; text-align:center' >Enviado</div></a>";
                                        } else { // Transito
                                            echo "<a href='#' title='Transferencia enviada correctamente' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: yellow; text-align:center'>Transito</div></a>";
                                        }
                                        break;
                                    // Pendiente
                                    case 0:
                                        if ($valor[12] == 0) {
                                            echo "<a href='#' id='linkAnulado' ><div style='width:70px; height:17px; background-color: #ab080c; text-align:center; color: #f1f1f1' >Anulado</div></a>";
                                        } else {
                                            echo "<a href='#' title='Transferencia pediente, falta confirmar' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: #FF6464; text-align:center'>Pendiente</div></a>";
                                        }
                                        break;
                                }

                                ?></div>
                        </td>

                        <?php
                        if ($valor[5] == 0) {
                            if ($valor[12] == 0) {
                                ?>
                                <td>
                                    <div align="center"><?php echo $valor[6]; ?></div>
                                </td>

                            <?php
                            } else {
                                ?>
                                <td>
                                    <div align="center"><a title="Cancelar la transferencia"
                                                           href='<?php echo base_url(); ?>index.php/seguridad/usuario/ventana_confirmacion_usuario2/guiatrans/<?php echo $valor[10]; ?>'
                                                           id='linkVerProveedor'><img
                                                src="<?php echo base_url(); ?>public/images/icons/error.png?=<?=IMG;?>" width="14px" height="14px"></a></div>
                                </td>       <?php
                            }
                        } else { ?>
                            <td>
                                <?php

                                if ($valor[5] == 0) {
                                    ?>
                                    <div align="center"><a
                                            href="<?php echo base_url(); ?>"><?php echo $valor[6]; ?></a>
                                    </div>
                                <?php
                                }else if($valor[5] == 1) {
                                    ?>
                                    <div align="center"><img src="<?php echo base_url(); ?>public/images/icons/pensando.png?=<?=IMG;?>" align="G" width="14px" height="14px" ></div>
                                <?php
                                }else{                                            ?>
                                    <div align="center"><img src="<?php echo base_url(); ?>public/images/icons/active.png?=<?=IMG;?>" align="G" width="14px" height="14px" ></div>
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
                                    <img src='<?php echo base_url(); ?>public/images/icons/error.png?=<?=IMG;?>' alt='W' width='14px' height='14px' >
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
                                        echo "<a href='#' onClick='cargarTransferencia(" . $enviot . ");' ><div style='width:70px; height:17px; background-color: yellow; text-align:center'>Transito</div></a>";
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