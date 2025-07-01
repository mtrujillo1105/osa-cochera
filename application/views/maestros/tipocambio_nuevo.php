<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/tipocambio.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
           <form name="frmTipoCambio" id="frmTipoCambio" method="post" action="<?php echo base_url() ?>index.php/maestros/tipocambio/grabar"  onSubmit="javascript: return false">
                            <div id="datosGenerales">
                                <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <?php
                                    $reg_sol = $lista_monedas[0];
                                    foreach ($lista_monedas as $item => $reg) {
                                        if ($reg->MONED_Codigo != 1) {
                                            echo '<tr>';
                                            echo '<td align="center">' . $reg_sol->MONED_Descripcion . ' (' . $reg_sol->MONED_Simbolo . ')' . ' a ' . $reg->MONED_Descripcion . ' (' . $reg->MONED_Simbolo . ')' . '</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo '<td align="center">' . form_hidden("moneda[" . $item . "]", $reg->MONED_Codigo) . form_input(array('name' => 'tipocambio[' . $item . ']', 'id' => 'tipocambio[' . $item . ']', 'value' => $valores[$reg->MONED_Codigo], 'maxlength' => '5', 'class' => 'cajaTextoGrande')) . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    echo form_hidden(array("moneda_origen" => $reg_sol->MONED_Codigo, 'fecha' =>$fecha ,'dfalt' =>'0'));
                                   
									
								   ?>

                                </table>
                            </div>
                            <div style="margin-top:20px;margin-bottom:10px; text-align: center">
                                <a href="javascript:;" id="grabarTipoCambio2"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                                <a href="#" onclick="atras_tipocambio();"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                            </div>
                            <?php echo $oculto ?>
							
            </form>
        </div>
    </div>
  </div>
</div>