<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/tipoproducto.js?=<?=JS;?>"></script>					
<script type="text/javascript">
function seleccionar_atributo(codigo,nombre, tipo){
    fila = $("#fila").val();
    a    = "atributo["+fila+"]";
    b    = "nombre_atributo["+fila+"]";
    c    = "tipo_atributo["+fila+"]";

    document.getElementById(a).value = codigo;
    document.getElementById(b).value = nombre;
    document.getElementById(c).value = tipo;
 
}
function buscar_atributo(n, flagBS){
    $("#fila").val(n);
    base_url = $("#base_url").val();
    url      = base_url + "index.php/almacen/atributo/ventana_busqueda_atributo/"+$("#flagBS").val();
    window.open(url, '', 'width=600,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0');
}
</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>",'</div>');?>
                <form id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action; ?> ">
                    <div id="datosGenerales">
                        <table class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="0">
                            <?php
                            foreach($campos as $indice=>$valor){
                            ?>
                                <tr>
                                  <td width="22%"><?php echo $campos[$indice];?></td>
                                  <td colspan="3"><?php echo $valores[$indice]?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <div id="nuevoRegistroProv" style="float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px; margin-bottom: 5px;">
                            <input type="hidden" name="fila" id="fila" value="<?php echo count($lista_atributos);?>">
                            <a href="#">Nuevo <image src="<?=$base_url;?>public/images/icons/add.png?=<?=IMG;?>" name="agregarFila" id="agregarFila" border="0" alt="Agregar"></a>
                        </div>
                        <table id="tblPlantilla" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
                            <tr align="center" bgcolor="#BBBB20" height="10px;">
                                <td width="25">Nro</td>
                                <td>Atributo</td>
                                <td>Tipo</td>
                                <td width="35">Borrar</td>
                            </tr>
                            <?php 
                            $sec=0;
                            foreach($lista_atributos as $valor){
                                $sec++;
                            ?>
                            <tr bgcolor="#ffffff">
                                <td align="center">
                                    <?php echo $sec; ?>
                                    <input type="hidden" name="plantilla[<?php echo $sec;?>]" id="plantilla[<?php echo $sec;?>]" value="<?php echo $valor->PLANT_Codigo;?>">
                                    <input type="hidden" name="atributo[<?php echo $sec;?>]" id="atributo[<?php echo $sec;?>]" value="<?php echo $valor->ATRIB_Codigo;?>">
                                </td>
                                <td align="left"><input type="text" name="nombre_atributo[<?php echo $sec;?>]" id="nombre_atributo[<?php echo $sec;?>]" class="cajaGrande" readonly="readonly" value="<?php echo $valor->ATRIB_Descripcion;?>"></td>
                                <td align="left"><input type="text" name="tipo_atributo[<?php echo $sec;?>]" id="tipo_atributo[<?php echo $sec;?>]" class="cajaPequena" readonly="readonly" value="<?php $temp=array('1'=>'Numérico', '2'=>'Fecha', '3'=>'Texto'); echo $temp[$valor->ATRIB_TipoAtributo];?>"></td>
                                <td align="center"><a href="#" onclick="eliminar_plantilla(<?php echo $sec;?>);"><img src="<?=$base_url;?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div style="margin-top: 20px; margin-bottom: 5px; text-align: center">
                        <a href="#" id="grabarTipoProd"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <a href="#" id="limpiarTipoProd"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                        <a href="#" id="cancelarTipoProd"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <?php echo $oculto?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>