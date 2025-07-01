<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<!--<script type="text/javascript" src="https://www.google.com/jsapi"></script>-->

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/google-api.js?=<?=JS;?>"></script>
<!--<script type="text/javascript" src="<?php echo base_url(); ?>public/js/google-uds.js?=<?=JS;?>"></script>-->
<script>
    $(document).ready(function(){
        $('#verReporte').click(function(){
            $('#frmReporte').submit();
        });
        $('#limpiarProducto').click(function(){
            //$('#frmReporte').reset();
            top.location="<?php echo base_url(); ?>index.php/reportes/valorizacion/valor"
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
        
        $("a#linkVerProducto").fancybox({
            'width'          : 800,
            'height'         : 650,
            'autoScale'      : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
        });
    });
    
    function seleccionar_producto(codigo,interno,nombreProd){
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        $("#nombre_producto").val(nombreProd);
        
        base_url = $("#base_url").val();
        url      = base_url+"index.php/reportes/valorizacion/obtener_nombre_producto/B/"+interno;
        alert("codigo:"+codigo+", interno:"+interno);
        var dataString = "flagBS=B&interno="+interno;
        $.post(url,dataString,function(data){
            alert("PAS1");
            /*
            switch(data.result){
                case 'ok': 
                    if(codigo==''){
                        $('#codigo').val(data.codigo);
                        $('#ventana').show();
                        $('#linkVerImpresion').click();
                    }
                    else
                        location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;
                    break;
                case 'error': 
                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                    break;
                case 'error2': alert(data.msj);
                    break;
            }*/
        },'json');
        /*
        $.getJSON(url,function(data){
            alert("PAS1");
            $.each(data, function(i,item){
                //nombre_producto = item.PROD_Nombre;
                alert("Producto:"+item.PROD_Nombre);
            });
            //$("#nombre_producto").val(nombre_producto);
        });
         */
    }
    
    var cursor;
    if (document.all) { // Está utilizando EXPLORER            
        cursor='hand';
    } else { // Está utilizando MOZILLA/NETSCAPE
        cursor='pointer';
    }

</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header">REPORTES DE VALORIZACION ACTUAL</div>
            <div id="frmBusqueda">
                <form method="post" action="" id="frmReporte">
                    <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
                        <tr>
                            <td width="10%" height="30">Fecha de Inicio</td>
                            <td width="50%"><input NAME="fecha_inicio" id="fecha_inicio" type="text" class="cajaGeneral" value="<?php echo $f_ini; ?>" size="10" maxlength="10" />
                                <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" />
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField     :    "fecha_inicio",
                                        ifFormat       :    "%d/%m/%Y",
                                        button         :    "Calendario1"
                                    });
                                </script>
                            </td>
                            <td width="10%" rowspan="3" valign="top">Establecimiento</td>
                            <td rowspan="3" valign="top">
                                <ul style="list-style: none; margin: 0px; padding: 0px;">
                                    <li><input type="checkbox" name="TODOS" id="TODOS" value="1" <?php if ($TODOS == true) echo 'checked="checked"'; ?> />TODOS</li>
                                    <?php
                                    foreach ($lista_companias as $valor) {
                                        echo '<li><input type="checkbox" name="COMPANIA_' . $valor->COMPP_Codigo . '" id="COMPANIA_' . $valor->COMPP_Codigo . '" value="1" ' . ($valor->checked == true ? 'checked="checked"' : '') . ' />' . $valor->EESTABC_Descripcion . '</li>';
                                    }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">Fecha Fin</td>
                            <td valign="top">
                                <input NAME="fecha_fin" id="fecha_fin" type="text" class="cajaGeneral" value="<?php echo $f_fin; ?>" size="10" maxlength="10" />
                                <img height="16" border="0" width="16" id="Calendario2" name="Calendario2" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" />
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
                            <td valign="top">Art&iacute;culo</td>
                            <td valign="top">
                                <input name="producto" id="producto" type="hidden" class="cajaPequena" size="10" maxlength="11" />
                                <input name="codproducto" id="codproducto" type="text" value="<?php echo $codproducto; ?>" class="cajaPequena" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');" />
                                <input name="nombre_producto"  id="nombre_producto" type="text" value="<?php echo $nombre_producto; ?>" class="cajaGrande cajaSoloLectura" size="40" readonly="readonly" />
                                <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" style="vertical-align: middle;">
                                <a href="javascript:;" id="verReporte">
                                    <img src="<?php echo base_url(); ?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" align="absmiddle"/>
                                </a>&nbsp;
                                <a href="javascript:;" id="limpiarProducto">
                                    <img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" align="absmiddle" />
                                </a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div id="frmResultado">
                <?php /* ?>
                  <table class="fuente8" width="100%" cellspacing=1 cellpadding="3" border=0>
                  <tr class="cabeceraTabla">
                  <th>F.pago</th>
                  <th>Forma pago</th>
                  <th>F.Venta</th>
                  <th>M</th>
                  <th>Venta</th>
                  <th>Tipo</th>
                  <th>N° doc</th>
                  <th>Cliente</th>
                  <th>M</th>
                  <th>TDC</th>
                  <th>Soles</th>
                  <th>Dolares</th>
                  </tr>
                  <?php
                  if(count($lista) > 0){
                  foreach($lista as $indice=>$value):
                  $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                  ?>
                  <tr class="<?php echo $class;?>">
                  <td><div align="center"><?php echo $value[0]; ?></div></td>
                  <td><div align="left"><?php echo $value[1]; ?></div></td>
                  <td><div align="center"><?php echo $value[2]; ?></div></td>
                  <td><div align="center"><?php echo $value[3]; ?></div></td>
                  <td><div align="center"><?php echo $value[4]; ?></div></td>
                  <td><div align="center"><?php echo $value[5]; ?></div></td>
                  <td><div align="center"><?php echo $value[6]; ?></div></td>
                  <td><div align="left"><?php echo $value[7]; ?></div></td>
                  <td><div align="center"><?php echo $value[8]; ?></div></td>
                  <td><div align="center"><?php echo $value[9]; ?></div></td>
                  <td><div align="center"><?php echo $value[10]; ?></div></td>
                  <td><div align="center"><?php echo $value[11]; ?></div></td>
                  </tr>
                  <?php
                  endforeach;
                  }else{
                  ?>
                  <td colspan="9"><div align="center">No hay ningún registro que cumpla con los criterios de búsqueda</div></td>
                  <?php
                  }
                  ?>
                  <tr>
                  <td colspan="10"><div align="right"><strong>TOTALES</strong></div></td>
                  <td><div align="center"><strong><?php echo $total_soles; ?></strong></div></td>
                  <td><div align="center"><strong><?php echo $total_dolares; ?></strong></div></td>

                  </tr>
                  </table>
                  <?php */ ?>
                <div class="fuente8" align="left"><b>RESUMEN POR ESTABLECIMIENTO</b></div>
                <div class="fuente8" align="left">Producto(s) Seleccionado(s):  <b><?php if ($prod_nombre != "") echo $prod_nombre; else echo "*** TODOS ***"; ?></b></div>
                <table class="fuente8" width="80%" cellspacing=1 cellpadding="3" border=0 align="left" style="height: 420px;">
                    <tr class="cabeceraTabla">
                        <th>Establecimientos</th>
                        <th>Existencia en Almacenes</th>
                        <th>Cuentas Por Pagar</th>
                        <th>Cuentas Por Cobrar</th>
                        <th><b>VALORIZACI&Oacute;N ACTUAL TOTAL</b></th>
                    </tr>
                    <!--
                    <tr class="cabeceraTabla">
                        <th>Soles</th>
                        <th>Dolares</th>
                        <th>Soles</th>
                        <th>Dolares</th>
                        <th>Soles</th>
                        <th>Dolares</th>
                        <th><b>Soles</b></th>
                        <th><b>Dolares</b></th>
                    </tr>-->
                    <?php
                    if (count($existencia_dolares) > 0) {
                        $total = array();
                        $total[0] = $total[1] = $total[2] = $total[3] = 0;
                        foreach ($existencia_dolares as $indice => $value) {

                            $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class; ?>">
                                <td><div align="left"><?php echo $value[0]; ?></div></td>
                                <!--<td><div align="right"><?php //echo $resumen_compania_sol[$value->COMPP_Codigo];                                                                                              ?></div></td>-->
                                <td><div align="right"><?php echo number_format($value[1], 2); ?></div></td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <!--<td><div align="right">0</div></td>-->
                                <td><div align="right"><?php echo number_format($value[2], 2); ?></div></td>
                                <!--<td><div align="right">0</div></td>-->
                                <td><div align="right"><?php echo number_format($value[3], 2); ?></div></td>
                                <!--<td><div align="right">0</div></td>-->
                                <td><div align="right"><?php echo number_format($value[4], 2); ?></div></td>
                            </tr>
                            <?php
                            /*
                              if(count($lista_companias) > 0){
                              foreach($lista_companias as $indice=>$value){
                              $class = $indice%2==0?'itemParTabla':'itemImparTabla';

                              ?>
                              <tr class="<?php echo $class;?>">
                              <td><div align="left"><?php echo $value->EESTABC_Descripcion; ?></div></td>
                              <!--<td><div align="right"><?php //echo $resumen_compania_sol[$value->COMPP_Codigo]; ?></div></td>-->
                              <td><div align="right"><?php echo $resumen_compania_dol[$value->COMPP_Codigo]; ?></div></td>

                              <!--<td><div align="right">0</div></td>-->
                              <td><div align="right">0</div></td>
                              <!--<td><div align="right">0</div></td>-->
                              <td><div align="right">0</div></td>
                              <!--<td><div align="right">0</div></td>-->
                              <td><div align="right">0</div></td>
                              </tr>

                              <?php
                             */
                            $total[0]+=$value[1];
                            $total[1]+=$value[2];
                            $total[2]+=$value[3];
                            $total[3]+=$value[4];
                        }
                        ?>
                        <tr>
                            <td><div align="right"><b>TOTALES</b></div></td>
                            <!--<td><div align="right"><b><?php // echo $total_compani_sol;                                                                                              ?></b></div></td>-->
                            <!--<td><div align="right"><b><?php echo $total_compani_dol; ?></b></div></td>-->
                            <td><div align="right"><b><?php echo number_format($total[0], 2); ?></b></div></td>
                            <!--<td><div align="right"><b>0.00</b></div></td>-->
                            <td><div align="right"><b><?php echo number_format($total[1], 2); ?></b></div></td>
                            <!--<td><div align="right"><b>0.00</b></div></td>-->
                            <td><div align="right"><b><?php echo number_format($total[2], 2); ?></b></div></td>
                            <!--<td style="background: #CCCCCC"><div align="right"><b>0.00</b></div></td>-->
                            <td style="background: #CCCCCC"><div align="right"><b><?php echo number_format($total[3], 2); ?></b></div></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr><td colspan="4"><div align="center">No hay ningún registro que cumpla con los criterios de búsqueda</div></td></tr>
                        <?php
                    }
                    ?>

                    <tr>
                        <td><br/><br/><br/><br/></td>
                    </tr>
                </table>                
                <!--FORMA DE PAGO-->
                <?php
                /*
                  ?>
                  <div class="fuente8" align="left" style="clear:both; padding-top: 20px;"><b>RESUMEN POR FORMA DE PAGO</b></div>
                  <br/>
                  <table class="fuente8" width="40%" cellspacing=0 cellpadding="3" border=0 align="left">
                  <tr class="cabeceraTabla">
                  <th>Forma de pago</th>
                  <th width="40">Soles</th>
                  <th width="40">Dolares</th>
                  <th width="50">Cantidad</th>
                  </tr>
                  <?php
                  if(count($lista_resumen) > 0){
                  foreach($lista_resumen as $indice=>$value){
                  $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                  ?>
                  <tr class="<?php echo $class;?>">
                  <td><div align="left"><?php echo $value[0]; ?></div></td>
                  <td><div align="right"><?php echo $value[1]; ?></div></td>
                  <td><div align="right"><?php echo $value[2]; ?></div></td>
                  <td><div align="right"><?php echo $value[3]; ?></div></td>
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
                  <td><div align="right"><b><?php echo $total_soles_res; ?></b></div></td>
                  <td><div align="right"><b><?php echo $total_dolares_res; ?></b></div></td>
                  <td><div align="right"><b><?php echo $total_cantidad; ?></b></div></td>
                  </tr>
                  </table>
                  <?php
                 */
                ?>
            </div>
            <div style="clear: both;"></div>

            <?php
            if (count($existencia_dolares) > 0) {
                ?>
                <h1>Reporte en Gr&aacute;ficos</h1>
                <div style="text-align: center;margin-left: auto;margin-right: auto;width: 810px;">
                    <div id="barra1" style="width: 800px; height: 400px;border-style: solid;border-width: 1px;border-color: #3a9ccb;"></div>
                    <br/>
                    <div id="barra2" style="width: 800px; height: 400px;border-style: solid;border-width: 1px;border-color: #3a9ccb;"></div>
                    <br/>
                    <div id="barra3" style="width: 800px; height: 400px;border-style: solid;border-width: 1px;border-color: #3a9ccb;"></div>
                    <br/>
                    <div id="barra4" style="width: 800px; height: 400px;border-style: solid;border-width: 1px;border-color: #3a9ccb;"></div>
                    <div style="clear: both;"></div>
                </div>
                <br/>
                <script>
                    google.load("visualization", "1", {packages:["corechart"]});               
                    google.setOnLoadCallback(drawBarChart);
                    function drawBarChart() {
                        var data = google.visualization.arrayToDataTable([
                            /*['Year', 'Austria', 'Belgium', 'Czech Republic', 'Finland', 'France', 'Germany'],
                            ['2003',  1336060,   3817614,       974066,       1104797,   6651824,  15727003],
                            ['2004',  1538156,   3968305,       928875,       1151983,   5940129,  17356071],
                            ['2005',  1576579,   4063225,       1063414,      1156441,   5714009,  16716049],
                            ['2006',  1600652,   4604684,       940478,       1167979,   6190532,  18542843],
                            ['2007',  1968113,   4013653,       1037079,      1207029,   6420270,  19564053],
                            ['2008',  1901067,   6792087,       1037327,      1284795,   6240921,  19830493]*/
    <?php
    $j = 0;

    foreach ($existencia_grafico as $key => $value) {
        if ($j > 0) {
            echo ",";
            echo "['',";
        } else {
            echo "['Year',";
        }
        $i = 0;
        foreach ($value as $key => $value2) {
            if ($i > 0) {
                echo ',';
            }
            if ($j > 0) {
                if ($value2 != '') {
                    echo $value2;
                } else {
                    echo 0;
                }
            } else {
                echo "'" . $value2 . "'";
            }
            $i++;
        }
        echo "]";
        $j++;
    }
    ?>    
            ]);

            // Create and draw the visualization.
            new google.visualization.ColumnChart(document.getElementById('barra1')).
                draw(data,
            {
                title:"Existencia en almacén",               
                width:800, height:400,
                hAxis: {title: "Establecimientos"}
            });
                                                                                                                                                            
        }
                </script>

                <script>
                    google.load("visualization", "1", {packages:["corechart"]});               
                    google.setOnLoadCallback(drawBarChart);
                    function drawBarChart() {
                        var data = google.visualization.arrayToDataTable([
                            /*['Year', 'Austria', 'Belgium', 'Czech Republic', 'Finland', 'France', 'Germany'],
                            ['2003',  1336060,   3817614,       974066,       1104797,   6651824,  15727003],
                            ['2004',  1538156,   3968305,       928875,       1151983,   5940129,  17356071],
                            ['2005',  1576579,   4063225,       1063414,      1156441,   5714009,  16716049],
                            ['2006',  1600652,   4604684,       940478,       1167979,   6190532,  18542843],
                            ['2007',  1968113,   4013653,       1037079,      1207029,   6420270,  19564053],
                            ['2008',  1901067,   6792087,       1037327,      1284795,   6240921,  19830493]*/
    <?php
    $j = 0;

    foreach ($cuentas_pagar as $key => $value3) {
        if ($j > 0) {
            echo ",";
            echo "['',";
        } else {
            echo "['Year',";
        }
        $i = 0;
        foreach ($value3 as $key => $value4) {
            if ($i > 0) {
                echo ',';
            }
            if ($j > 0) {
                if ($value4 != '') {
                    echo $value4;
                } else {
                    echo 0;
                }
            } else {
                echo "'" . $value4 . "'";
            }
            $i++;
        }
        echo "]";
        $j++;
    }
    ?>    
            ]);

            // Create and draw the visualization.
            new google.visualization.ColumnChart(document.getElementById('barra2')).
                draw(data,
            {
                title:"Cuentas por Pagar",
                width:800, height:400,
                hAxis: {title: "Establecimientos"}
            });
                                                                                                                                                            
        }
                </script>


                <script>
                    google.load("visualization", "1", {packages:["corechart"]});               
                    google.setOnLoadCallback(drawBarChart);
                    function drawBarChart() {
                        var data = google.visualization.arrayToDataTable([
                            /*['Year', 'Austria', 'Belgium', 'Czech Republic', 'Finland', 'France', 'Germany'],
                            ['2003',  1336060,   3817614,       974066,       1104797,   6651824,  15727003],
                            ['2004',  1538156,   3968305,       928875,       1151983,   5940129,  17356071],
                            ['2005',  1576579,   4063225,       1063414,      1156441,   5714009,  16716049],
                            ['2006',  1600652,   4604684,       940478,       1167979,   6190532,  18542843],
                            ['2007',  1968113,   4013653,       1037079,      1207029,   6420270,  19564053],
                            ['2008',  1901067,   6792087,       1037327,      1284795,   6240921,  19830493]*/
    <?php
    $j = 0;

    foreach ($cuentas_cobrar as $key => $value5) {
        if ($j > 0) {
            echo ",";
            echo "['',";
        } else {
            echo "['Year',";
        }
        $i = 0;
        foreach ($value5 as $key => $value6) {
            if ($i > 0) {
                echo ',';
            }
            if ($j > 0) {
                if ($value6 != '') {
                    echo $value6;
                } else {
                    echo 0;
                }
            } else {
                echo "'" . $value6 . "'";
            }
            $i++;
        }
        echo "]";
        $j++;
    }
    ?>    
            ]);

            // Create and draw the visualization.
            new google.visualization.ColumnChart(document.getElementById('barra3')).
                draw(data,
            {
                title:"Cuentas por Cobrar",
                width:800, height:400,
                hAxis: {title: "Establecimientos"}
            });
                                                                                                                                                            
        }
                </script>




                <script>
                    google.load("visualization", "1", {packages:["corechart"]});               
                    google.setOnLoadCallback(drawBarChart);
                    function drawBarChart() {
                        var data = google.visualization.arrayToDataTable([
                            /*['Year', 'Austria', 'Belgium', 'Czech Republic', 'Finland', 'France', 'Germany'],
                            ['2003',  1336060,   3817614,       974066,       1104797,   6651824,  15727003],
                            ['2004',  1538156,   3968305,       928875,       1151983,   5940129,  17356071],
                            ['2005',  1576579,   4063225,       1063414,      1156441,   5714009,  16716049],
                            ['2006',  1600652,   4604684,       940478,       1167979,   6190532,  18542843],
                            ['2007',  1968113,   4013653,       1037079,      1207029,   6420270,  19564053],
                            ['2008',  1901067,   6792087,       1037327,      1284795,   6240921,  19830493]*/
    <?php
    $j = 0;

    foreach ($valorizacion_total as $key => $value7) {
        if ($j > 0) {
            echo ",";
            echo "['',";
        } else {
            echo "['Year',";
        }
        $i = 0;
        foreach ($value7 as $key => $value8) {
            if ($i > 0) {
                echo ',';
            }
            if ($j > 0) {
                if ($value8 != '') {
                    echo $value8;
                } else {
                    echo 0;
                }
            } else {
                echo "'" . $value8 . "'";
            }
            $i++;
        }
        echo "]";
        $j++;
    }
    ?>    
            ]);

            // Create and draw the visualization.
            new google.visualization.ColumnChart(document.getElementById('barra4')).
                draw(data,
            {
                title:"Valorización Total",
                width:800, height:400,
                hAxis: {title: "Establecimientos"}
            });
                                                                                                                                                            
        }
                </script>
                <?php
            }
            ?>
        </div>
    </div>
</div>