<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/tipocambio.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
        <div id="frmBusqueda" >
            <?php echo $form_open;?>
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="5" border=0>
                    <tr>
                        <td align='left' width="13%">Fecha</td>
                        <td align='left'><?php echo $fecha;?>
                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField     :    "fecha",      // id del campo de texto
                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                button         :    "Calendario1"   // el id del botè´¸n que lanzarè°© el calendario
                            });
                        </script></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            <?php echo $form_close;?>
        </div>
<div class="acciones">
        <div id="botonBusqueda">
            <!--<ul id="imprimirTipoCambio" class="lista_botones"><li id="imprimir">Imprimir</li></ul>-->
            <ul id="limpiarTipoCambio" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
            <ul id="buscarTipoCambio" class="lista_botones"><li id="buscar">Buscar</li></ul> 
        </div>
        <div id="lineaResultado">
            <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                <tr>
                    <td width="50%" align="left">N de tipos de cambios encontrados:&nbsp;<?php echo $registros;?> </td>
                </tr>
            </table>
        </div>
</div>
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
            <div id="frmResultado">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="4" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="20">ITEM</td>
                        <td width="120">FECHA</td>
                        <?php
                        $reg_sol=$listado_moneda[0];
                        foreach($listado_moneda as $reg){
                            if($reg->MONED_Codigo!=1)
                                echo '<td>DE '.$reg_sol->MONED_Descripcion.' ('.$reg_sol->MONED_Simbolo.')'.' A '.$reg->MONED_Descripcion.' ('.$reg->MONED_Simbolo .')'.'</td>';
                        }
                            
                        ?>
                        <td width="25">&nbsp;</td>
                        <td width="25">&nbsp;</td>
                    </tr>
                    <?php
                    if(count($lista)>0){
                        foreach($lista as $indice=>$valor)
                        {
                            $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class;?>">
                            <td><div align="center"><?php echo $valor[0];?></div></td>

                            <td><div align="center"><?php echo substr($valor[1],8,2).'/'.substr($valor[1],5,2).'/'.substr($valor[1],0,4);?></div></td>
                            
                            <?php 
                            foreach($valor[2] as $reg){
                                    echo '<td align="center">'. $reg .'</td>';
                            }
                            ?>
                             
                            <td><div align="right"><?php echo $valor[3];?></div></td>
                            <td><div align="right"><?php echo $valor[4]; ?>
                                 
                             </div></td>
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









			
								
										<tr height="28" class="itemParTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>
										
										<tr height="28" class="itemImparTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>


			
								
										<tr height="28" class="itemParTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>
										
										<tr height="28" class="itemImparTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>




			
								
										<tr height="28" class="itemParTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>
										
										<tr height="28" class="itemImparTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>




			
								
										<tr height="28" class="itemParTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>
										
										<tr height="28" class="itemImparTabla">
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="center"> </div></td>
                                            <td><div align="left"> </div></td>
                                        </tr>



            </table>
            </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
            <?php echo $oculto;?>
    </div>
</div>			
</div>