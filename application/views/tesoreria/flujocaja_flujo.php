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
        <div id="tituloForm" class="header">DOCUMENTO DE  <?php echo ($tipo_cuenta='1' ? 'COBRO' : 'PAGO'); ?></div>
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
                    <?php if($tipo_oper=='V'){ ?>
                    <tr>
                        <td align='left' width="13%">Cliente:</td>
                        <td align='left'><?php echo $ruc_cliente.' '.$nombre_cliente; ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php }else{ ?>
                    <tr>
                        <td align='left' width="13%">Proveedor:</td>
                        <td align='left'><?php echo $ruc_proveedor.' '.$nombre_proveedor; ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td align='left' width="13%">Total:</td>
                        <td align='left'><?php echo $simbolo_moneda.' '.$total; ?></td>
                        <td>Saldo: </td>
                        <td><?php echo $simbolo_moneda.' '.number_format($saldo,2); ?> <?php echo $estado_formato; ?></td>
                    </tr>
                </table>
        </div>
        <div id="frmBusqueda">
            <h3>NUEVO <?php if($tipo_oper=='V') echo 'COBRO'; else echo 'PAGO'; ?></h3>
            
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="2" border=0>
                    <tr>
                        <td align='left' width="13%">Fecha de <?php if($tipo_oper=='V') echo 'Cobro'; else echo 'Pago'; ?> *</td>
                        <td align='left' width="50%">
                            <input NAME="fecha" id="fecha" type="text" class="cajaGeneral cajaSoloLectura" value="<?php echo date('d/m/Y');?>" size="10" maxlength="10" readonly="readonly" />
                            <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                            <script type="text/javascript">
                                Calendar.setup({
                                    inputField     :    "fecha",      // id del campo de texto
                                    ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                    button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                });
                            </script>
                        </td>
                        <td width="13%">Moneda *</td>
                        <td><select name="moneda" id="moneda" class="comboMedio"><?php echo $cboMoneda;?></select></td>
                    </tr>
                    <tr>
                        <td align='left' width="13%">Importe *</td>
                        <td align='left'><input type="text" name="importe"  id="importe" class="cajaGeneral" size="10" maxlength="10" /></td>
                        <td>Observación:</td>
                        <td rowspan="3"><textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%" rows="4"></textarea></td>
                    </tr>
                    <tr>
                        <td align='left' width="13%">Forma de Pago *</td>
                        <td align='left'><select name="forma_pago" id="forma_pago" class="comboMedio"><?php echo $cboFormaPago;?></select></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align='left' width="13%">Número de Documento</td>
                        <td align='left'><input type="text" name="num_doc"  id="num_doc" class="cajaGeneral" size="10" maxlength="10" /></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
        </div>
        <div id="botonBusqueda">
            <a href="javascript:;" id="atrasFlujocaja"><img src="<?=$base_url;?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" style="margin-right:170px;" /></a>
            
            <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
            <a href="javascript:;" id="grabarFlujocaja"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" /></a>
            <a href="javascript:;" id="limpiarFlujocaja"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" /></a>
            <a href="javascript:;" id="cancelarFlujocaja"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" /></a>
            <?php echo $oculto?>
        </div>
        <div id="lineaResultado">
            <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
                <tr>
                    <td width="50%" align="left">N de formas de pago encontradas:&nbsp;<?php echo $registros;?> </td>
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
                        <td width="10%">IMPORTE</td>
                        <td width="15%">FORMA DE PAGO</td>
                        <td width="60%">OBSERVACION</td>
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
                            <td><div align="left"><?php echo $valor[2];?></div></td>
                            <td><div align="left"><?php echo $valor[3];?></div></td>
                            <td><div align="left"><?php echo $valor[4];?></div></td>
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