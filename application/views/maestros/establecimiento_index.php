<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/establecimiento.js?=<?=JS;?>"></script>	
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
            <div id="frmBusqueda" >
                <form id="form_busquedaEstablecimiento" name="form_busquedaEstablecimiento" method="post" action="<?php echo $action;?>">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
                        <tr>
                            <td align='left' width="13%">Nombre establecimiento</td>
                            <td align='left'><input id="txtEstablecimiento" name="txtEstablecimiento" type="text" class="cajaGrande" maxlength="45" value="<?php echo $txtEstablecimiento;?>"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </div>
            
            <div class="acciones">
            <div id="botonBusqueda">
                <!-- <ul id="imprimirEstablecimiento" class="lista_botones"><li id="imprimir">Imprimir</li></ul>-->
                <ul id="nuevoEstablecimiento" class="lista_botones"><li id="nuevo">Nuevo Establecimiento</li></ul>
                <ul id="limpiarEstablecimiento" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                <ul id="buscarEstablecimiento" class="lista_botones"><li id="buscar">Buscar</li></ul> 
            </div>
            <div id="lineaResultado">
                <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                    <tr>
                        <td width="50%" align="left">N de establecimientos encontrados:&nbsp;<?php echo $registros;?> </td>
                        <td width="50%" align="right">&nbsp;</td>
                    </tr>
                </table>
            </div>
            </div>
            
            
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;?></div>
            <div id="frmResultado">
                <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" id="Table1">
                    <tr class="cabeceraTabla">
                        <td width="5%">ITEM</td>
                        <td width="60%">DESCRIPCION</td>
                        <td width="5%">&nbsp;</td>
                        <td width="5%">&nbsp;</td>
                        <td width="6%">&nbsp;</td>
                    </tr>
                    <?php
                    if(count($lista)>0){
                    foreach($lista as $indice=>$valor){
                        $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                        ?>
                        <tr class="<?php echo $class;?>">
                            <td><div align="center"><?php echo $valor[0];?></div></td>
                            <td><div align="left"><?php echo $valor[1];?></div></td>
                            <td><div align="center"><?php echo $valor[2];?></div></td>
                            <td><div align="center"><?php echo $valor[3];?></div></td>
                            <td><div align="center"><?php echo $valor[4];?></div></td>
                        </tr>
                        <?php
                        }
                   
                    ?>
 				</table>                    
                    <?php 
                     }else{
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
                <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
                <input type="hidden" id="iniciopagina" name="iniciopagina">
                <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
                <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>
    </div>
</div>