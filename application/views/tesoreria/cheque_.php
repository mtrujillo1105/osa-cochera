<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
$CI = get_instance();
?>
<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/flujocaja.js?=<?=JS;?>"></script>
<?php echo $form_open;?>
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
        <div id="tituloForm" class="header">DOCUMENTO DE  <?php echo ($tipo_cuenta=='1' ? 'COBRO' : 'PAGO'); ?></div>
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
                
                    <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                        <tbody>
                            <tr>
                                <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    }
                    ?>
            </div>
            
    </div>
        
</div>	
</div>
<?php echo $form_close;?>