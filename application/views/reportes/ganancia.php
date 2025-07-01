<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script>
    $(document).ready(function(){
        $('#verReporte').click(function(){
            $('#frmReporte').submit();
            return true;
        });

        $("#verReportePDF").click(function(){
            codproducto    = $("#producto").val();
            view = true;
            
            if (codproducto == "")
                codproducto = "ALL";

            fechaInicio    = $("#fecha_inicio").val();
            fechaFin    = $("#fecha_fin").val();

            if (fechaInicio == ''){
                alert('Debe ingresar una fecha de inicio');
                $("#fecha_inicio").focus();
                view = false;
            }

            if (fechaFin == ''){
                alert('Debe ingresar una fecha fin');
                $("#fecha_fin").focus();
                view = false;
            }

            if (view == true){
                fechaI = fechaInicio.split('/');
                fechaF = fechaFin.split('/');

                fechaIF = fechaI[0]+"-"+fechaI[1]+"-"+fechaI[2]+"-"+fechaF[0]+"-"+fechaF[1]+"-"+fechaF[2];

                companias = '';

                for (i = 0; i < 30; i++){
                    if ( $("#COMPANIA_"+i).length > 0 && $("#COMPANIA_"+i).prop('checked') )
                        companias += "-"+$("#COMPANIA_"+i).val();
                }

                var url= "<?=base_url();?>index.php/reportes/ventas/gananciaPDF/"+codproducto+"/"+companias+"/"+fechaIF;
                window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
            }
        });

        $("#descargarExcel").click(function(){
            codproducto    = $("#producto").val();
            view = true;
            
            if (codproducto == "")
                codproducto = "ALL";

            fechaInicio    = $("#fecha_inicio").val();
            fechaFin    = $("#fecha_fin").val();

            if (fechaInicio == ''){
                alert('Debe ingresar una fecha de inicio');
                $("#fecha_inicio").focus();
                view = false;
            }

            if (fechaFin == ''){
                alert('Debe ingresar una fecha fin');
                $("#fecha_fin").focus();
                view = false;
            }

            if (view == true){
                fechaI = fechaInicio.split('/');
                fechaF = fechaFin.split('/');

                fechaIF = fechaI[0]+"-"+fechaI[1]+"-"+fechaI[2]+"-"+fechaF[0]+"-"+fechaF[1]+"-"+fechaF[2];

                companias = '';

                for (i = 0; i < 30; i++){
                    if ( $("#COMPANIA_"+i).length > 0 && $("#COMPANIA_"+i).prop('checked') )
                        companias += "-"+$("#COMPANIA_"+i).val();
                }

                location.href = "<?=base_url();?>index.php/reportes/ventas/gananciaExcel/"+codproducto+"/"+companias+"/"+fechaIF;
            }
        });

        $("#precioPromedio").click(function(){
            codproducto    = $("#producto").val();
            view = true;
            
            if (codproducto == "")
                codproducto = "ALL";

            fechaInicio    = $("#fecha_inicio").val();
            fechaFin    = $("#fecha_fin").val();

            if (fechaInicio == ''){
                alert('Debe ingresar una fecha de inicio');
                $("#fecha_inicio").focus();
                view = false;
            }

            if (fechaFin == ''){
                alert('Debe ingresar una fecha fin');
                $("#fecha_fin").focus();
                view = false;
            }

            if (view == true){
                fechaI = fechaInicio.split('/');
                fechaF = fechaFin.split('/');

                fechaIF = fechaI[0]+"-"+fechaI[1]+"-"+fechaI[2]+"-"+fechaF[0]+"-"+fechaF[1]+"-"+fechaF[2];
                location.href = "<?=base_url();?>index.php/reportes/ventas/promedioVentaExcel/"+codproducto+"/"+fechaIF;
            }
        });

        $("#descargarProductoExcel").click(function(){
            if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == ""){
                alert("Ingrese ambas fechas");
            }
            else{
                var startDate = $('#fecha_inicio').val();
                var endDate = $('#fecha_fin').val();

                if (startDate > endDate){
                  alert("Rango de Fechas inválido");
                }
                else{
                  fechaI = startDate.split('-');
                  fechaF = endDate.split('-');
                  fechaIF = startDate+"/"+endDate;

                  location.href = "<?=base_url();?>index.php/reportes/ventas/gananciaExcel/"+fechaIF;
                }
            }
        });

        $("#limpiar").click(function(){
            $("#producto").val('');
            $("#codproducto").val('');
            $("#nombre_producto").val('');            
            $("#fecha_inicio").val('');
            $("#fecha_fin").val('');
            $("#verReporte").click();
        });
        
        $("a#linkVerProducto").fancybox({
                'width'	     : 800,
                'height'         : 650,
                'autoScale'	     : false,
                'transitionIn'   : 'none',
                'transitionOut'  : 'none',
                'showCloseButton': false,
                'modal'          : true,
                'type'	     : 'iframe'
        });
        $('input[name^="COMPANIA_"]').click(function(){
            if($(this).is(':checked')==false)
                $('input[name^="TODOS"]').attr('checked', false);
        });
        $('input[name="TODOS"]').click(function(){
            $('input[name^="COMPANIA_"]').attr('checked', false);
            if($(this).is(':checked'))
                $('input[name^="COMPANIA_"]').attr('checked', true);
        });
    });
    
    function seleccionar_producto(codigo,interno,familia,stock,costo,flagGenInd){
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        listar_unidad_medida_producto(codigo);
    }
    function listar_unidad_medida_producto(producto){   
        base_url   = $("#base_url").val();
        url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
        select   = document.getElementById('unidad_medida');
        $.getJSON(url,function(data){
          $.each(data, function(i,item){
                nombre_producto = item.PROD_Nombre;
          });
          $("#nombre_producto").val(nombre_producto);
        });
    }
    
</script>
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
    <div id="tituloForm" class="header">REPORTES DE GANANCIAS POR PRODUCTO</div>
    <div id="frmBusqueda">
      <form method="post" action="" id="frmReporte">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border="0">
            <tr>
                <td>Producto</td>
                <td>
                    <input name="producto" type="hidden" class="cajaGeneral" id="producto" value="<?php echo $producto; ?>" />
                    <input name="buscar_producto" type="text" class="cajaGeneral" id="buscar_producto" size="10" />&nbsp;
                    <input name="codproducto" type="hidden" class="cajaGeneral" id="codproducto" size="10" maxlength="20" onblur="obtener_producto();" value="<?php echo $codproducto; ?>" />
                    <input NAME="nombre_producto" type="text" class="cajaGeneral cajaSoloLectura" id="nombre_producto" size="40" readonly="readonly" value="<?php echo $nombre_producto; ?>" />
                    <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                </td>
                <td>Moneda *</td>
                <td><?=$cboMoneda;?></td>
            </tr>
            <tr>
                <td width="10%" height="30">Fecha de Inicio</td>
                <td width="50%"><input NAME="fecha_inicio" id="fecha_inicio" type="text" class="cajaGeneral" value="<?php echo $f_ini; ?>" size="10" maxlength="10" />
                    <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField     :    "fecha_inicio",
                            ifFormat       :    "%d/%m/%Y",
                            button         :    "Calendario1"
                        });
                    </script>
                </td>
                <td width="10%" rowspan="2" valign="top">Establecimiento</td>
                <td rowspan="2" valign="top">
                    <ul style="list-style: none; margin: 0px; padding: 0px;">
                        <li><input type="checkbox" name="TODOS" id="TODOS" value="1" <?php if($TODOS==true) echo 'checked="checked"'; ?> />TODOS</li>
                   <?php 
                   foreach($lista_companias as $valor){
                        echo '<li><input type="checkbox" name="COMPANIA_'.$valor->COMPP_Codigo.'" id="COMPANIA_'.$valor->COMPP_Codigo.'" value="'.$valor->COMPP_Codigo.'" '.($valor->checked==true ? 'checked="checked"' : '').' />'.$valor->EESTABC_Descripcion.'</li>';
                    }
                   ?>
                   </ul>
                </td>
            </tr>
            <tr>
                <td valign="top">Fecha Fin</td>
                <td valign="top"><input NAME="fecha_fin" id="fecha_fin" type="text" class="cajaGeneral" value="<?php echo $f_fin; ?>" size="10" maxlength="10" />
                    <img height="16" border="0" width="16" id="Calendario2" name="Calendario2" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField     :    "fecha_fin",
                            ifFormat       :    "%d/%m/%Y",
                            button         :    "Calendario2"
                        });
                    </script>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <a href="javascript:;" id="limpiar">
                        <img src="<?=$base_url;?>public/images/icons/icono_limpiar.png?=<?=IMG;?>" width="24px" class="imgBoton" align="absmiddle"/>
                    </a>
                    <a href="javascript:;" id="verReporte">
                        <img src="<?=$base_url;?>public/images/icons/icono_buscar.png?=<?=IMG;?>" width="24px" class="imgBoton" align="absmiddle"/>
                    </a>
                    <a href="javascript:;" id="verReportePDF">
                        <img src="<?=$base_url;?>public/images/icons/pdf.png?=<?=IMG;?>" width="24px" class="imgBoton" align="absmiddle"/>
                    </a>
                    <a href="javascript:;" id="descargarExcel">
                        <img src="<?=$base_url;?>public/images/icons/xls.png?=<?=IMG;?>" width="24px" class="imgBoton" align="absmiddle"/>
                    </a>
                </td>
                <td colspan="2" align="right">
                    <span>PRECIOS PROMEDIOS</span>
                    <a href="javascript:;" id="precioPromedio">
                        <img src="<?=$base_url;?>public/images/icons/xls.png?=<?=IMG;?>" width="24px" class="imgBoton" align="absmiddle"/>
                    </a>
                </td>
            </tr>
        </table>
        <?php echo $oculto; ?>
      </form>
      </div>
      <div id="frmResultado">
                <table class="fuente8" width="100%" cellspacing=1 cellpadding="3" border="0">
                        <tr class="cabeceraTabla">
                            <th>Fecha</th>
                            <th>Establec</th>
                            <th>Producto</th>
                            <th>Lote Numero</th>
                            <th>Vencimiento Lote</th>
                            <th>Cantidad</th>
                            <th>Moneda</th>
                            <th>PU. Costo</th>
                            <th>PU. Venta</th>
                            <th>Costo</th>
                            <th>Venta</th>
                            <th>Utilidad</th>
                            <th>% Utilidad</th>
                        </tr>
                        <?php
                        if(count($lista) > 0){
                            foreach($lista as $indice=>$value):
                                $class = $indice%2==0?'itemParTabla':'itemImparTabla'; ?>
                                <tr class="<?php echo $class;?>">
                                    <td><div align="center"><?=$value[0];?></div></td>
                                    <td><div align="left"><?=$value[1];?></div></td>
                                    <td><div align="left"><?=$value[2];?></div></td>
                                    <td><div align="center"><?=$value[3];?></div></td>
                                    <td><div align="center"><?=$value[4];?></div></td>
                                    <td><div align="right"><?=$value[5];?></div></td>
                                    <td><div align="right"><?=$value[6];?></div></td>
                                    <td><div align="right"><?=$value[7];?></div></td>
                                    <td><div align="right"><?=$value[8];?></div></td>
                                    <td><div align="right"><?=$value[9];?></div></td>
                                    <td><div align="right"><?=$value[10];?></div></td>
                                    <td><div align="left"><?=$value[11];?></div></td>
                                    <td><div align="left"><?=$value[12];?></div></td>
                                </tr> <?php
                            endforeach;
                        }
                        else{ ?>
                            <td colspan="9"><div align="center">No hay ningún registro que cumpla con los criterios de búsqueda</div></td> <?php
                        } ?>
                        <tr>
                                <td colspan="7"><div align="right"><strong>TOTALES</strong></div></td>
                                <td><div align="right"><strong><?php echo $total_costo; ?></strong></div></td>
                                <td><div align="right"><strong><?php echo $total_venta; ?></strong></div></td>
                                <td><div align="right"><strong><?php echo $total_util; ?></strong></div></td>
                                <td><div align="right"><strong><?php echo $total_porc_util; ?></strong></div></td>

                        </tr>
                </table>
                <div class="fuente8" align="left"><b>RESUMEN POR ESTABLECIMIENTO</b></div>
                <table class="fuente8" width="40%" cellspacing=1 cellpadding="3" border=0 align="left">
                        <tr class="cabeceraTabla">
                                <th>Establec</th>
                                <th>Costo</th>
                                <th>Venta</th>
                                <th>Utilidad</th>
                                <th>% Utilidad</th>
                        </tr>
                        <?php
                        if(count($lista_companias) > 0){
                                foreach($lista_companias as $indice=>$value){
                                $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                                ?>
                                <tr class="<?php echo $class;?>">
                                        <td><div align="left"><?php echo $value->EESTABC_Descripcion; ?></div></td>
                                        <td><div align="right"><?php echo $resumen_compania[$value->COMPP_Codigo]['costo']; ?></div></td>
                                        <td><div align="right"><?php echo $resumen_compania[$value->COMPP_Codigo]['venta']; ?></div></td>
                                        <td><div align="right"><?php echo $resumen_compania[$value->COMPP_Codigo]['util']; ?></div></td>
                                        <td><div align="right"><?php echo $resumen_compania[$value->COMPP_Codigo]['porc']; ?></div></td>
                                </tr>
                        <?php
                                }
                        }else{
                        ?>
                                        <td colspan="4"><div align="center">No hay ningún registro que cumpla con los criterios de búsqueda</div></td>
                        <?php
                        }
                        ?>
                            <tr>
                                <td><div align="right"><b>TOTALES</b></div></td>
                                <td><div align="right"><b><?php echo $t_resumen_costo; ?></b></div></td>
                                <td><div align="right"><b><?php echo $t_resumen_venta; ?></b></div></td>
                                <td><div align="right"><b><?php echo $t_resumen_util; ?></b></div></td>
                                <td><div align="right"><b><?php echo $t_resumen_porc; ?></b></div></td>
                            </tr>
                </table>
        </div>
    </div>
    </div>
</div>