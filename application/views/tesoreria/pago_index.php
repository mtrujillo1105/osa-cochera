<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/flujocaja.js?=<?=JS;?>"></script>

<div class="container-fluid">
    <div class="row header">
        <div class="col-md-12 col-lg-12">
            <div>DOCUMENTO DE <?=($tipo_cuenta=='1') ? 'COBRO' : 'PAGO';?></div>
        </div>
    </div>
    <form id="form_busqueda" method="post">
        <div class="row fuente8 py-1">
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="fechai">Fecha inicial: </label>
                <input id="fechai" name="fechai" type="date" class="form-control w-porc-90 h-1" value="<?=$fechai;?>">
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="fechaf">Fecha Final: </label>
                <input id="fechaf" name="fechaf" type="date" class="form-control w-porc-90 h-1" value="<?=$fechaf;?>">
            </div>
            <div class="col-sm-1 col-md-1 col-lg-1 form-group">
                <label for="serie">Serie: </label>
                <input id="serie" name="serie" type="text" class="form-control w-porc-90 h-1" value="<?=$serie;?>">
            </div>
            <div class="col-sm-1 col-md-1 col-lg-1 form-group">
                <label for="numero">Número: </label>
                <input id="numero" name="numero" type="number" class="form-control w-porc-90 h-1" value="<?=$numero;?>">
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="estado_pago">Estado de pago: </label>
                <select name="estado_pago" id="estado_pago" class="form-control w-porc-90 h-2">
                    <option value="T" <?=($cboestadopago == "" || $cboestadopago == "T") ? "selected" : "";?>>Todos</option>
                    <option value="C" <?=($cboestadopago == "C") ? "selected" : "";?>>Cancelado</option>
                    <option value="P" <?=($cboestadopago == "P") ? "selected" : "";?>>Pendiente</option>
                </select>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="comprobante">Tipo de documento: </label>
                <select name="comprobante" id="comprobante" class="form-control w-porc-90 h-2">
                    <option value="T" <?=($cboTipoDoc == "T") ? "selected" : "";?>>Todos</option>
                    <option value="8" <?=($cboTipoDoc == "8") ? "selected" : "";?>>Factura</option>
                    <option value="9" <?=($cboTipoDoc == "9") ? "selected" : "";?>>Boleta</option>
                    <option value="14" <?=($cboTipoDoc == "14") ? "selected" : "";?>>Comprobantes</option>
                </select>
            </div>
        </div>

        <div class="row fuente8">
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="">RUC/DNI: </label>
                <input id="ruc_cliente" name="ruc_cliente" type="<?=($tipo_cuenta == '1') ? "number" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="N° documento" value="">
                <input id="ruc_proveedor" name="ruc_proveedor" type="<?=($tipo_cuenta == '2') ? "number" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="N° documento" value="">
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3 form-group">
                <label for="">Razón Social: </label>
                <input id="nombre_cliente" name="nombre_cliente" type="<?=($tipo_cuenta == '1') ? "text" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="Razón social" value="">
                <input id="nombre_proveedor" name="nombre_proveedor" type="<?=($tipo_cuenta == '2') ? "text" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="Razón social" value="">
            </div>

            <div class="col-sm-1 col-md-1 col-lg-1"><br>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_totales">TOTALES</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="acciones">
                        <div id="botonBusqueda">
                            <ul id="nuevoCuenta" class="lista_botones">
                                <li id="nuevo"> Nuevo <?=($tipo_cuenta == 1) ? "Cobro" : "Pago";?></li>
                            </ul>
                            <ul id="limpiarC" class="lista_botones">
                                <li id="limpiar">Limpiar</li>
                            </ul>
                            <ul id="buscarC" class="lista_botones">
                                <li id="buscar">Buscar</li>
                            </ul>
                        </div>
                        <div id="lineaResultado">Registros encontrados</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="header text-align-center"><?=$titulo;?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                    </div>
                    <table class="fuente8 display" width="100%" cellspacing="0" cellpadding="3" border="0" id="table-cuentas">
                        <thead>
                            <tr class="cabeceraTabla">
                                <td style="width:10%" data-orderable="true">FECHA</td>
                                <td style="width:10%" data-orderable="true">TIPO DOC</td>
                                <td style="width:04%" data-orderable="true">SERIE</td>
                                <td style="width:06%" data-orderable="true">NUMERO</td>
                                <td style="width:30%" data-orderable="true">RAZON SOCIAL</td>
                                <td style="width:10%" data-orderable="false">TOTAL</td>
                                <td style="width:10%" data-orderable="false">SALDO</td>
                                <td style="width:10%" data-orderable="false">ESTADO</td>
                                <td style="width:2.5%" data-orderable="false">&nbsp;</td>
                                <td style="width:2.5%" data-orderable="false">&nbsp;</td>
                                <td style="width:2.5%" data-orderable="false">&nbsp;</td>
                                <td style="width:2.5%" data-orderable="false">&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $form_open;?>
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
        <div id="tituloForm" class="header"></div>
        <div id="frmBusqueda" style="background-color:#E2E2E2 " >
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="3" border=0>
                    <tr>
                        <td align='left' width="13%">Tipo de Doc:</td>
                        <td align='left' width="50%"><?php if($tipo_docu=='F') echo 'Factura'; else echo 'Boleta'; ?></td>
                        <td width="13%">Fecha:</td>
                        <td><?php echo $fecha; ?></td>
                    </tr>
                    <tr>
                        <td align='left' width="13%">Número:</td>
                        <td align='left'><?php echo $serie.' - '.$numero; ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php if($tipo_cuenta=='1'){ ?>
                    <tr>
                        <td align='left' width="13%">Cliente:</td>
                        <td align='left'><?php echo $nombre_cliente; ?></td>
                        <td>DNI / RUC</td>
                        <td><?php echo $ruc_cliente; ?></td>
                    </tr>
                    <?php }else{ ?>
                    <tr>
                        <td align='left' width="13%">Proveedor:</td>
                        <td align='left'><?php echo $nombre_proveedor; ?></td>
                        <td>DNI / RUC</td>
                        <td><?php echo $ruc_proveedor; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td align='left' width="13%">Total:</td>
                        <td align='left'><?php echo $simbolo_moneda.' '.number_format($total,4); ?></td>
                        <td>Saldo: </td>
                        <td><?php echo $simbolo_moneda.' '.number_format($saldo,4); ?> <?php echo $estado_formato; ?></td>
                    </tr>
                </table>
        </div>
      
        <div id="botonBusqueda">
            <a href="javascript:;" id="atrasFlujocaja"><img src="<?=$base_url;?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" /></a>
            <?php echo $oculto?>
        </div>
        <div id="lineaResultado">
            <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
                <tr>
                    <td width="50%" align="left">N de pagos encontrados:&nbsp;<?php echo $registros;?> </td>
                    <td width="50%" align="right">&nbsp;</td>
                </tr>
            </table>
        </div>
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
            <div id="frmResultado">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="5%">ITEM</td>
                        <td width="10%">FECHA</td>
                        <td width="10%">SERIE-NUMERO</td>
                        <td width="10%">MONEDA</td>
                        <td width="10%">IMPORTE</td>
                        <td width="15%">FORMA DE PAGO</td>
                        <td width="50%">OBSERVACION</td>
                    </tr>
                    <?php
                    if(count($lista)>0){
                        foreach($lista as $indice=>$valor)
                        {
                            $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class;?>">
                            <td><div align="center"><?php echo $valor[0];?></div></td>
                            <td><div align="center"><?php echo $valor[1];?></div></td>
                            <td><div align="center"><?php echo $valor[6];?></div></td>
                            <td><div align="center"><?php echo $valor[2];?></div></td>
                            <td><div align="left"><?php echo $valor[3];?></div></td>
                            <td><div align="left"><?php echo $valor[4];?></div></td>
                            <td><div align="left"><?php echo $valor[5];?></div></td>
                            </tr>

                            <?php
                        }
                    }
                    else{
                    ?>
                
                        <tbody>
                            <tr>
                                <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                            </tr>
                        </tbody>
                    <?php
                    }
                    ?>
               </table>
            </div>
            
    </div>
        
</div>	
</div>
<?php echo $form_close;?>