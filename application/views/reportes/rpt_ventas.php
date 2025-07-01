
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
    var base_url
    $(document).ready(function(){
        base_url = $("#base_url").val();
        
        $('#verReporte').click(function(){
            $('#frmReporte').submit();
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
    
    
    function seleccionar_producto(codigo,interno,familia,stock,costo, flagGenInd){
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        $("#nombre_producto").val("NADA");
        base_url = $("#base_url").val();
       
        url      = base_url+"index.php/reportes/valorizacion/obtener_nombre_producto";//B/"+interno;
        alert("codigo:"+codigo+", interno:"+interno);
        alert(url)
        var dataString = "flagBS=B&interno="+interno;
        $.post(url,dataString,function(data){
            $("#nombre_producto").val(data.desc);
            //alert("PAS1");
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
            <div id="tituloForm" class="header">REPORTES DE VENTAS</div>
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
                            <td colspan="4" align="center"><a href="javascript:;" id="verReporte"><img src="<?php echo base_url(); ?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" align="absmiddle"/></a></td>
                        </tr>
                    </table>
                </form>
            </div>
            <br />

            <?php
            /*
              foreach($resumen as $fila):
              echo "nombre: ".$fila['NOMBRE'].", ventas: ".$fila['VENTAS']."<br/>";
              //echo "['Mirko', 1070],";
              endforeach;
             */
            ?>


            <!-- *********  FILTRO 01: VENDEDOR  ********* -->
            <!-- Criterio: Monto Ingresado -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    /*
                    var datos = google.visualization.arrayToDataTable([
                        ['Vendedor', 'Ventas', 'Cantidad'],
                        ['Mirko',  1700,      600],
                        ['Vendedor 1',  1000,      400],
                        ['Vendedor 2',  1170,      460],
                        ['Vendedor 3',  660,       1120],
                        ['Vendedor 4',  1030,      540]
                    ]);
                     */
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
<?php
$cant_v = 1;
foreach ($resumen as $fila):
    if ($cant_v == 1)
    //imprime la primera barra.
        echo "['{$fila['NOMBRE']}',{$fila['VENTAS']}]";
    else
    //imprime las siguientes barras (con una coma adelante).
        echo ",['{$fila['NOMBRE']}',{$fila['VENTAS']}]";
endforeach;
?>
        ]);
        var opciones = {
            title: 'Monto Ingresado',
            width:'400',height:'300',
            //Columna de 
            vAxis: {title: 'Soles', titleTextStyle: {color: 'blue'}},
            hAxis: {title: 'Vendedores', titleTextStyle: {color: 'blue'}}
        };
            
        var grafico = new google.visualization.ColumnChart(
        document.getElementById('capaGrafico1')
    );
        grafico.draw(datos, opciones);
    }


    /*
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
<?php
/* foreach($resumen as $fila):
  echo "['{$fila['NOMBRE']}',{$fila['VENTAS']}]";
  //echo "['Mirko', 1070],";
  endforeach; */
?>
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Monto Ingresado','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                        document.getElementById('capaGrafico1')
                    );
                    grafico.draw(datos, opciones);
                }*/
            </script>          
            <!-- Criterio: Cantidad -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Cantidad','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico2')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Ganancia -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Ganancia','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico3')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Rotacion -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Rotacion','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico4')
                );
                    grafico.draw(datos, opciones);
                }
            </script>
            <!-- FIN FILTRO 01 -->

            <!-- *********  FILTRO 02: MARCA  ********* -->
            <!-- Criterio: Monto Ingresado -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Monto Ingresado','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico5')
                );
                    grafico.draw(datos, opciones);
                }
            </script>          
            <!-- Criterio: Cantidad -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Cantidad','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico6')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Ganancia -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Ganancia','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico7')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Rotacion -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Rotacion','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico8')
                );
                    grafico.draw(datos, opciones);
                }
            </script>
            <!-- FIN FILTRO 02 -->

            <!-- *********  FILTRO 03: FAMILIA  ********* -->
            <!-- Criterio: Monto Ingresado -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Monto Ingresado','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico9')
                );
                    grafico.draw(datos, opciones);
                }
            </script>          
            <!-- Criterio: Cantidad -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Cantidad','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico10')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Ganancia -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Ganancia','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico11')
                );
                    grafico.draw(datos, opciones);
                }
            </script>    
            <!-- Criterio: Rotacion -->
            <script type="text/javascript">
                google.load('visualization', '1.0', {'packages':['corechart']});
                google.setOnLoadCallback(dibujaGrafico);
        
                function dibujaGrafico() {
                    var datos = new google.visualization.DataTable();
                    datos.addColumn('string', 'Variables');
                    datos.addColumn('number', 'Porcentaje');
                    datos.addRows([
                        ['Mirko', 1070],
                        ['Vendedor 1', 50],
                        ['Vendedor 2', 160],
                        ['Vendedor 3', 540],
                        ['Vendedor 4', 160]
                    ]);
            
                    var opciones = {'title':'Rotacion','width':400,'height':300};
            
                    var grafico = new google.visualization.PieChart(
                    document.getElementById('capaGrafico12')
                );
                    grafico.draw(datos, opciones);
                }
            </script>
            <!-- FIN FILTRO 03 -->    

            <br/><br/>
            <!-- *****   VISUALIZACION DE LOS GRAFICOS ESTADISTICOS ******** -->
            <table style="border: 2px #AAAAAA solid;">
                <tr style="background: #CCCCCC;">
                    <td colspan="2" style="text-align: center; font-size: 16px; font-weight: bold;">
                        Reporte de Ventas por Vendedor
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico1"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico2"></div></td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico3"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico4"></div></td>
                </tr>
            </table>
            <br/>
            <table style="border: 2px #AAAAAA solid;">
                <tr style="background: #CCCCCC;">
                    <td colspan="2" style="text-align: center; font-size: 16px; font-weight: bold;">
                        Reporte de Ventas por MARCA
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico5"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico6"></div></td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico7"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico8"></div></td>
                </tr>
            </table>
            <br/>
            <table style="border: 2px #AAAAAA solid;">
                <tr style="background: #CCCCCC;">
                    <td colspan="2" style="text-align: center; font-size: 16px; font-weight: bold;">
                        Reporte de Ventas por Familia
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico9"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico10"></div></td>
                </tr>
                <tr>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico11"></div></td>
                    <td style="border: 1px #CCCCCC solid;"><div id="capaGrafico12"></div></td>
                </tr>
            </table>
            <br/><br/><br/>
            <!-- *****   FIN DE VISUALIZACION    ******** -->   
            <?php
            /* ?>

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
              <?php ?>
              <br/>
              <div class="fuente8" align="left"><b>RESUMEN POR ESTABLECIMIENTO</b></div>
              <div class="fuente8" align="left">Producto(s) Seleccionado(s):  <b><?php if($prod_nombre!="") echo $prod_nombre; else echo "*** TODOS ***"; ?></b></div>
              <br/>
              <table class="fuente8" width="80%" cellspacing=1 cellpadding="3" border=0 align="left">
              <tr class="cabeceraTabla">
              <th>Establecimientos</th>
              <th>Existencia en Almacenes</th>
              <th>Deudas Por Pagar</th>
              <th>Monto Por Cobrar</th>
              <th><b>VALORIZACION ACTUAL TOTAL</b></th>
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
              if(count($existencia_dolares) > 0){
              foreach($existencia_dolares as $indice=>$value){
              $class = $indice%2==0?'itemParTabla':'itemImparTabla';
              ?>
              <tr class="<?php echo $class;?>">
              <td><div align="left"><?php echo $value[1]; ?></div></td>
              <!--<td><div align="right"><?php //echo $resumen_compania_sol[$value->COMPP_Codigo]; ?></div></td>-->
              <td><div align="right"><?php echo number_format($value[2],2); ?></div></td>

              <!--<td><div align="right">0</div></td>-->
              <td><div align="right">0</div></td>
              <!--<td><div align="right">0</div></td>-->
              <td><div align="right">0</div></td>
              <!--<td><div align="right">0</div></td>-->
              <td><div align="right">0</div></td>
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
              }
              }else{
              ?>
              <td colspan="4"><div align="center">No hay ningún registro que cumpla con los criterios de búsqueda</div></td>
              <?php
              }
              ?>
              <tr>
              <td><div align="right"><b>TOTALES</b></div></td>
              <!--<td><div align="right"><b><?php // echo $total_compani_sol; ?></b></div></td>-->
              <td><div align="right"><b><?php echo $total_compani_dol; ?></b></div></td>
              <!--<td><div align="right"><b>0.00</b></div></td>-->
              <td><div align="right"><b>0.00</b></div></td>
              <!--<td><div align="right"><b>0.00</b></div></td>-->
              <td><div align="right"><b>0.00</b></div></td>
              <!--<td style="background: #CCCCCC"><div align="right"><b>0.00</b></div></td>-->
              <td style="background: #CCCCCC"><div align="right"><b>0.00</b></div></td>
              </tr>
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

              </div>
              <?php
             */
            ?>
            <br/><br/><br/>
            <input type="text" style="visibility:hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
        </div>
    </div>
</div>