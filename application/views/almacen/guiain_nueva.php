<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/guiain.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript">
function seleccionar_proveedor(codigo,ruc,razon_social){
    $("#proveedor").val(codigo);
    $("#ruc").val(ruc);
    $("#nombre_proveedor").val(razon_social);
}
function escribe_nombre_unidad_medida(){
    index     = document.getElementById("unidad_medida").selectedIndex;
    nombre = document.getElementById("unidad_medida").options[index].text;
    $("#nombre_unidad_medida").val(nombre);
}
function seleccionar_producto(codigo,interno,nombre,familia,stock,costo){
    $("#producto").val(codigo);
    $("#codproducto").val(interno);
    $("#nombre_producto").val(nombre);
    $("#nombre_familia").val(familia);
    $("#stock").val(stock);
    $("#cantidad").select();
    var sel = document.getElementById('unidad_medida');
    var opt = sel.getElementsByTagName("option");
    for(i=1;i<opt.length;i++){
    sel.options[i]=null;
    }
    listar_unidad_medida_producto(codigo);
}
</script>	
<?php echo $form_open;?>
<div id="zonaContenido" align="center">
    <?php echo validation_errors("<div class='error'>",'</div>');?>
    <div id="tituloForm" class="header"><?php echo $titulo;?></div>
    <div id="frmBusqueda">
        <table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
            <tr>
                <td width="8%" >N&uacute;mero</td>
                <td width="29%"><?php echo $numero;?></td>
                <td width="10%">Almacen</td>
                <td width="23%"><?php echo $cboAlmacen;?></td>
                <td width="10%">Fecha</td>
                <td width="23%">
                    <?php echo $fecha;?>
                    <a href="#" style="display:none;"><img height="16" border="0" width="16" id="Image1" name="Image1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>"></a>
                </td>
            </tr>
            <tr>
                <td>Personal</td>
                <td><?php echo $nombre_usuario;?></td>
                <td>Proveedor </td>
                <td>
                    <?php echo $proveedor;?>
                    <?php echo $ruc;?>
                    &nbsp;<?php echo $nombre_proveedor;?>
                    <?php echo $verproveedor;?>
                </td>
                <td>O. Compra </td>
                <td>
                    <?php echo $cboOcompra;?>
                </td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda"  <?php echo $hidden;?>>
        <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                <td width="9%">Art&iacute;culo</td>
                <td width="45%">
                    <input name="producto" type="hidden" class="cajaPequena2" id="producto" size="10" maxlength="11">
                    <input name="codproducto" type="text" class="cajaPequena2" id="codproducto" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');">&nbsp;
                    <input NAME="nombre_producto" type="text" class="cajaGrande" id="nombre_producto" size="15" maxlength="15" readonly="readonly">
                    <?php echo $verproducto;?>
                    <input name="stock" type="hidden" id="stock">
                    <input name="simbolo" type="hidden" id="simbolo">
                    <input name="nombre_familia" type="hidden" id="nombre_familia">
                </td>
                <td width="6%">Cantidad</td>
                <td width="28%">
                    <input NAME="cantidad" type="text" class="cajaPequena2" id="cantidad" value="0" size="5" maxlength="10" onKeyPress="return numbersonly(this,event,'.');">
                    <select name="unidad_medida" id="unidad_medida" class="comboMedio" onChange="escribe_nombre_unidad_medida();"><option value="0">::Seleccione::</option></select>
                </td>
                <td width="2%"><input type="hidden" name="nombre_unidad_medida" id="nombre_unidad_medida" class="cajaMedia"></td>
                <td width="15%"><div align="right"><a href="#" onClick="agregar_producto_ocompra();"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" class="imgBoton" align="absbottom"></a></div></td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" id="Table1">
            <tr class="cabeceraTabla">
                <td width="3%"><div align="center">&nbsp;</div></td>
                <td width="5%"><div align="center">ITEM</div></td>
                <td width="10%"><div align="center">C&Oacute;DIGO</div></td>
                <td><div align="center">DESCRIPCI&Oacute;N</div></td>
                <td width="8%"><div align="center">CANTIDAD</div></td>
                <td width="8%"><div align="center">UNIDAD</div></td>
            </tr>
        </table>
    </div>
    <div id="lineaResultado2">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top">
                    <table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">
                    <?php
                    if(count($detalle)>0){
                        foreach($detalle as $indice=>$valor){
                            $detguiain          = $valor->GUIAINDETP_Codigo;
                            $prodproducto       = $valor->PROD_Codigo;
                            $unidad_medida      = $valor->UNDMED_Codigo;
                            $codigo_interno     = $valor->PROD_CodigoInterno;
                            $prodcantidad       = $valor->GUIAINDETC_Cantidad;
                            $prodcosto          = $valor->GUIAINDETC_Costo;
                            $nombre_producto    = $valor->PROD_Nombre;
                            $nombre_unidad      = $valor->UNDMED_Simbolo;
                            $GenInd             = $valor->GenInd;
                            if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                      ?>
                      <tr class="<?php echo $clase;?>">
                        <td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_guiain(this);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                        <td width="5%"><div align="center"><?php echo $indice+1;?></div></td>
                        <td width="10%"><div align="left">
                            <input type="hidden" class="cajaMinima" name="prodcodigo[<?php echo $indice;?>]" id="prodcodigo[<?php echo $indice;?>]" value="<?php echo $prodproducto;?>">
                            <input type="hidden" class="cajaMinima" name="produnidad[<?php echo $indice;?>]" id="produnidad[<?php echo $indice;?>]" value="<?php echo $unidad_medida;?>">
                            <input type="hidden" class="cajaMinima" name="flagGenInd[<?php echo $indice;?>]" id="flagGenInd[<?php echo $indice;?>]" value="<?php echo $GenInd;?>">
                            <?php echo $codigo_interno;?></div>
                        </td>
                        <td width="56%"><div align="left"><?php echo $nombre_producto;?></div></td>
                        <td width="8%">
                            <div align="right">
                                <?php if($GenInd=="I"):?>
                                <a href="#" onclick="ventana_producto_serie(<?php echo $indice;?>)"><img src="<?=$base_url;?>public/images/icons/flag-green_icon.png?=<?=IMG;?>" width="20" height="20" border="0"/></a>
                                <?php endif;?>
                                <input type="text" class="cajaPequena2" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onKeyPress="return numbersonly(this,event,'.');">
                            </div>
                        </td>
                        <td width="8%">
                            <div align="center">
                                <?php echo $nombre_unidad;?>
                                <input type="hidden" class="cajaMinima" name="detguiain[<?php echo $indice;?>]" id="detguiain[<?php echo $indice;?>]" value="<?php echo $detguiain;?>">
                                <input type="hidden" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m">
                                <input type="hidden" class="cajaPequena2" name="prodpu[<?php echo $indice;?>]" id="prodpu[<?php echo $indice;?>]" value="<?php echo $prodcosto;?>" readonly="readonly">
                            </div>
                        </td>
                      </tr>
                        <?php
                    }
                    }
                    ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda">
        <table class="fuente8" width="100%" border="0" cellpadding="3" cellspacing="5">
            <tr>
                <td>Doc Ref:&nbsp;<?php echo $cboDocumento;?></td>
                <td><?php echo $numero_ref;?></td>
                <td>Motivo movimiento</td>
                <td><?php echo $cboTipoMov;?></td>
                <td>Fecha Emision</td>
                <td>
                    <?php echo $fecha_emision;?>
                    <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField     :    "fecha_emision",      // id del campo de texto
                            ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                            button         :    "Calendario1"   // el id del botón que lanzará el calendario
                        });
                    </script>
                </td>
            </tr>
            <tr>
                <td>Nombre Transportista:</td>
                <td><?php echo $nombre_transportista;?></td>
                <td>RUC Transportista</td>
                <td><?php echo $ruc_transportista;?></td>
                <td>Vehiculo marca y placa</td>
                <td><?php echo $marca_placa;?></td>
            </tr>
            <tr>
                <td>Cert.Inscripcion</td>
                <td><?php echo $certificado;?></td>
                <td>Licencia de conducir</td>
                <td><?php echo $licencia;?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td valign="top">Observaci&oacute;n</td>
                <td colspan="5" align="left"><?php echo $observacion;?></td>
            </tr>
        </table>
    </div>
    <div style="position: relative;top:25px">
        <a href="javascript:;" id="grabarGuiain"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
        <a href="javascript:;" id="limpiarGuiain"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
        <a href="javascript:;" id="cancelarGuiain"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
        <?php echo $oculto?>
    </div>
    </div>
    <?php echo $form_close;?>