<script type="text/javascript" src="<?=$base_url;?>public/js/seguridad/impactousuario.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
        <div id="frmBusqueda" >
            <?php echo $form_open;?>
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="5" border=0>
                    <tr>
                        <td align='left' width="13%">Buscar</td>
                        <td> <input type="text" name="buscarusuario" id="buscarusuario" value="<?php echo ''; ?>" class="cajaGeneral" size="20" maxlength="20"  /></td>
                        <td align='left'><? //  echo $filtro;   ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            <?php echo $form_close;?>
        </div>
 <div class="acciones">
        <div id="botonBusqueda">
            <ul id="nuevoImpactousuario" class="lista_botones"><li id="nuevo">Nuevo</li></ul> 
            <ul id="limpiarUsuario" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
           <ul id="buscarImpactousuario" class="lista_botones"><li id="buscar">Buscar</li></ul> 

        </div>
        <div id="lineaResultado">
            <table  width="100%" cellspacing=0 cellpadding=3 border=0>
                <tr>
                    <td width="50%" align="left">N de actividades encontradas:&nbsp;<?php echo $registros;?> </td>
                </tr>
            </table>
        </div>
 </div>    
        
        
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
            <div id="frmResultado">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="20">ITEM</td>
                        <td width="120">USUARIO</td>
                         <td width="120">FECHA REGISTRO</td>
                        <td width="5">&nbsp;</td>
                        <td width="5">&nbsp;</td>
                        <td width="5">&nbsp;</td>
                    </tr>
                    <?php
                    if(count($lista)>0){
                        foreach($lista as $indice=>$valor)
                        {
                            $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class;?>">
                                <td><div align="center"><?php echo $valor[0];?></div></td>
                                <td><div align="center"><?php echo $valor[2];?></div></td>
                                <td><div align="center"><?php echo $valor[3];?></div></td>
                               
                                <td><div align="center"><a href="#" onclick="ver_usuario(<?php echo $valor[1]; ?>)"><img src="<?php echo base_url()?>public/images/icons/ver.png?=<?=IMG;?>" width="16" height="16" border="0" title="Ver"></a></div></td>
                                <td><div align="center"><a href="#" onclick="modificar_usuario(<?php echo $valor[1]; ?>)"><img src="<?php echo base_url()?>public/images/icons/modificar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Modificar"></a></div></td>
                                <td><div align="center"><a href="#" onclick="eliminar_usuario(<?php echo $valor[1]; ?>)"><img src="<?php echo base_url()?>public/images/icons/eliminar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Eliminar"></a></div></td>
                               <?php //echo $valor[1]?>
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
            </table>
            </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
            <?php echo $oculto;?>
    </div>
</div>			
</div>