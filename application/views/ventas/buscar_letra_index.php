<div id="contenedor-busqueda" >
    <div id="frmResultado">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
            <tr class="cabeceraTabla">
                <td width="5%">ITEM</td>
                <td width="10%">FECHA</td>
                <td width="10%">NUMERO</td>
                <td width="40%">CLIENTE</td>
                <td width="15%">TOTAL</td>
                <td width="5%">VER</td>
                <td width="5%">PDF</td>
                <td width="5%">REPORTE GENERAL</td>
                <td width="5%">&nbsp;</td>
            </tr>
            <?php
            if (count($lista) > 0) { $repeatPDF = 0;
                foreach ($lista as $indice => $valor) {
                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                    ?>

                    <tr class="<?php echo $class; ?>">
                        <td><div align="center"><?php echo $valor[0]; ?></div></td>
                        <td><div align="center"><?php echo $valor[2]; ?></div></td>
                        <td><div align="center"><?php echo $valor[3]; ?></div></td>
                        <td><div align="center"><?php echo $valor[4]; ?></div></td>
                        <td><div align="center"><?php echo $valor[5]; ?></div></td>
                        <td><div align="center"><?php echo $valor[6]; ?></div></td>
                        <td>
                            <div align="center">
                                <img src="<?=base_url().'images/pdf.png?=<?=IMG;?>';?>" height='16px' onclick="abrir_pdf_factura_letra(<?=$valor[1];?>)" style="cursor:pointer;" />
                            </div>
                        </td>
                        <td align="center">
                            <?php
                            if ($repeatPDF != $valor[7]){
                                $repeatPDF = $valor[7]; ?>
                            
                                <div align="center">
                                    <img src="<?=base_url().'images/pdf.png?=<?=IMG;?>';?>" onclick="abrir_pdf_factura_letra_all(<?="'$valor[7]','$tipo_oper'";?>)" style="cursor:pointer;" height='16px'/>
                                </div> <?php 
                            } ?>
                        </td>
                        <td><div align="center"><?php echo ""; ?></div></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="9">
                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                            <tbody>
                                <tr>
                                    <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php
            }
            ?>

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
            </tr>

            


        </table>
    </div>
    <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
    <input type="hidden" id="iniciopagina" name="iniciopagina">
    <?php echo $oculto ?>
</div>