                <div id="contenedor-busqueda" >
                    <div id="frmResultado">
                        <div id="cargando_datos" class="loading-table">
                            <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                        </div>
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                            <!--
                                00    $item
                                01    $fecha
                                02    $serie
                                03    $numero
                                04    $guiarem_codigo
                                05    $docurefe_codigo
                                06    $nombre
                                07    $total
                                08    $img_estado
                                09    $editar
                                10    $imprimirPDF
                                11    $imprimirPDF2
                                12    $disparador
                                13    $estado
                                14    $codigo
                                15    $codigo_canje
                                16    $contadoVacios
                                17    $ConversorDeNumero
                                18    $numeroSerieCanjeado
                                19    $comprobantesRelacion
                                20    $ver3
                                21    $enviarcorreo
                                22    $xml
                                23    $CPP_Compracliente
                                24    $CLIC_CodigoUsuario
                                24    $pdfsunat
                            );-->

                            <tr class="cabeceraTabla">
                                <td width="5%">FECHA</td>
                                <td width="5%">SERIE</td>
                                <td width="5%">NUMERO</td>
                                <td width="3%">OC.</td>
                                <td width="3%">COTIZ.</td>
                                <td width="9%">GUIA</td>
                                <td width="9%"><?=($tipo_docu == "N") ? "COMPR." : "CANJE";?></td>
                                
                                <td width="5%"><?=($tipo_oper == "V") ? "CLIENTE" : "PROVEEDOR";?></td>
                                <td width="30%">RAZON SOCIAL</td>
                                <td width="7%">TOTAL</td>
                                <td colspan="9">ESTADO</td>
                            </tr>
                            <?php
                            if (count($lista) > 0) {
                                foreach ($lista as $indice => $valor) {
                                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla'; ?>
                                    <tr class="<?php echo $class; ?>">
                                        <input type="hidden" name="numeroClave" id="numeroClave" value="<?php echo $valor[17]; ?>">
                                        <td>
                                            <div align="center"><?=$valor[1];?></div> <!-- FECHA -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[2];?></div> <!-- SERIE -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[3];?></div> <!-- NUMERO -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[23];?></div> <!-- OC -->
                                        </td>

                                        <td>
                                            <div align="center"><?=$valor[5];?></div> <!-- COTIZ -->
                                        </td>
                                        <td>
                                            <div align="center" class="guiarem_c_<?=$valor[0];?>"> <!-- GUIA REM -->
                                                <span class="icon-loading"></span>
                                                <span class="guiarem_data_<?=$valor[0];?>"><?=$valor[4];?></span>
                                            </div>
                                        </td>
                                        <td>
                                           <div align="center"><?=$valor[18];?></div> <!-- CANJE -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[24];?></div> <!-- CLIENTE -->
                                        </td>
                                        <td>
                                            <div align="left"><?=$valor[6];?></div> <!-- RAZON SOCIAL -->
                                        </td>
                                        <td>
                                            <div align="right"><?=$valor[7];?></div> <!-- TOTAL -->
                                        </td>
                                            
                                            <!-- ACCIONES -->

                                        <td>
                                            <div align="center"><?=$valor[8];?></div> <!-- ANULAR -->
                                        </td>
                                        <td>
                                            <div align="center" class="editar_data_<?=$valor[0]?>"><?=$valor[9];?></div> <!-- EDITAR -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[10];?></div> <!-- IMPRIMIR PDF -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[11];?></div> <!-- IMPRIMIR PDF 2 -->
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[26];?></div> <!-- IMPRIMIR EXCEL -->
                                        </td>
                                        <td>
                                            <div align="center" class="pdfSunat_<?=$valor[0]?>"> <!-- IMPRIMIR PDF SUNAT -->
                                                <span class="icon-loading"></span>
                                                <span class="pdfSunat_data_<?=$valor[0]?>"><?=$valor[25];?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div align="center"><?=$valor[22];?></div> <!-- XML -->
                                        </td>
                                        <td>
                                            <div align="center" class="enviarcorreo_data_<?=$valor[0]?>"><?=$valor[21];?></div> <!-- ENVIAR CORREO -->
                                        </td>
                                        <td>
                                            <div align="center" class="disparador_<?=$valor[0]?>"> <!-- APROBAR -->
                                                <span class='icon-loading'></span>
                                                <span class="disparador_data_<?=$valor[0]?>"><?=$valor[12];?></span>
                                            </div>

                                            <?php
                                            if ($tipo_docu == 'N' && $valor[13] == 1)
                                                if ($valor[15] == '' || $valor[15] == NULL || $valor[15] == 0){ ?>
                                                    <a href="<?=base_url();?>index.php/ventas/comprobante/canje_documento/<?=$valor[14];?>" class="canjear_doc">Canjear</a> <?php
                                                } ?>
                                        </td>
                                    </tr><?php
                                }
                            }
                            else { ?>
                                <tr>
                                    <td colspan="16">
                                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                            <tbody>
                                                <tr>
                                                    <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr> <?php
                            } ?>
                        </table>
                    </div>
                    <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
                    <input type="hidden" id="iniciopagina" name="iniciopagina">
                    <?php echo $oculto ?>
                </div>