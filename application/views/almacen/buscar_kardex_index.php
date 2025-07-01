<div id="activarBusqueda" >
    <div id="lineaResultado" style="margin-top:10px;">
        <table class="fuente8" width="100%" cellspacing='0' cellpadding='3' border='0'>
            <tr>
                <td width="50%" align="left">N de registros encontrados:&nbsp;<?php echo $registros;?> </td>
                <td width="50%" align="right">&nbsp; Reporte Kardex</td>
            </tr>
        </table>
    </div>


    <div id="frmResultado">
        <table class="fuente8" width="100%" cellspacing="1" cellpadding="3" border="0" ID="Table1">
            <tr class="cabeceraTabla">
                <td width="3%" rowspan="2">MOV.</td>
                <td width="12%" rowspan="2">ALMACEN</td>
                <td width="5%" rowspan="2">FECHA MOVIMIENTO</td>
                <!--<td width="5%" rowspan="2">FECHA DE FACTURA</td>-->
                <td width="6%" rowspan="2">NUM DOC</td>
                <td rowspan="2">
                    <div align="center">CLIENTE / PROVEEDOR</div>
                </td>
                <td width="4%" rowspan="2">
                    <div align="center">TIPO OPER.</div>
                </td>
                <td colspan="3">
                    <div align="center">ENTRADA</div>
                </td>
                <td colspan="3">
                    <div align="center">SALIDA</div>
                </td>
                <!--<td colspan="3">
                    <div align="center">SALDOS</div>
                </td>-->
                <!--<td width="10" rowspan="2">PDF</td>-->
                <td width="10" rowspan="2">Aprobado</td>
                <td width="10" rowspan="2">Usuario</td>
            </tr>
            <tr class="cabeceraTabla">
                <td width="4%">CANT.</td>
                <td width="4%">P.U.</td>
                <td width="5%">C.T.</td>
                <td width="4%">CANT.</td>
                <td width="4%">P.U.</td>
                <td width="5%">C.T.</td>
                <!--<td width="4%">CANT.</td>
                <td width="4%">P.U.</td>
                <td width="5%">C.T.</td>-->
            </tr>
            <?php
            if (count($lista) > 0) {
                foreach ($lista as $indice => $valor) {
                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>
                    <tr class="<?php echo $class;?>" <?php if ($valor[9] == "0" || $valor[6] == "0" && $indice > 1) {
                        echo "style='color: red'";
                    } ?>>
                        <td>
                            <div align="center"><?php echo $valor[0]; ?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[1];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[2];?></div>
                        </td>
                        <!--<td>
                            <div align="center"><?php echo date('d/m/Y', strtotime($valor[16]));?></div>
                        </td>-->

                        <td>
                            <div align="center"><?php echo $valor[3];?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[4];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[5];?>
                           
                            </div>
                        </td>
                        <td>
                            <div align="right"><?php echo $valor[6];?>
                             <?php if(isset($flagGenInd) && $flagGenInd=='I') {?>
                             <a href="javascript:;"
                                       onclick="mostrarSeriesProducto('<?php echo $valor[17]; ?>')"
                                       target="_parent"><img src="<?php echo base_url(); ?>public/images/icons/flag-green_icon.png?=<?=IMG;?>" width="20" height="20" border="0"
                                                             title="Ver Series"></a>
                            <?php } ?>
                            </div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[7] != "") echo number_format($valor[7], 2); else echo "";?></div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[8] != "") echo number_format($valor[8], 2); else echo "";?></div>
                        </td>
                        <td>
                            <div align="right"><?php echo $valor[9];?></div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[10] != "") echo number_format($valor[10], 2); else echo "";?></div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[11] != "") echo number_format($valor[11], 2); else echo "";?></div>
                        </td>
                        <!--<td>
                            <div align="right"><?php echo $valor[12];?></div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[13] != "") echo number_format($valor[13], 2); else echo "";?></div>
                        </td>
                        <td>
                            <div
                                align="right"><?php if ($valor[14] != "") echo number_format($valor[14], 2); else echo "";?></div>
                        </td>-->
                        <td>
                            <div align="right">
                                <input onclick='ValidaKardex(<?="\"$valor[20]\",\"$indice\"";?>)' id="checkValida[<?=$indice;?>]" name="checkValida[<?=$indice;?>]" type="checkbox" <?=($valor[18]=='1') ? "checked disabled" : "";?>>
                            </div>
                        </td>
                        <td>
                            <div align="left" id="usuario[<?=$indice;?>]" name="usuario[<?=$indice;?>]"><?=$valor[19];?></div>
                        </td>
                    </tr>

                <?php
                }
            } else {
                ?>
                <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                    <tbody>
                    <tr>
                        <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los
                            criterios de b&uacute;squeda
                        </td>
                    </tr>
                    </tbody>
                </table>
            <?php
            }
            ?>
        </table>
    </div>
</div>