<?php

  function getMes($mes)
  {
    $mes = str_pad((int) $mes,2,"0",STR_PAD_LEFT);
    switch ($mes) 
    {
        case "01": return "ENE";
        case "02": return "FEB";
        case "03": return "MAR";
        case "04": return "ABR";
        case "05": return "MAY";
        case "06": return "JUN";
        case "07": return "JUL";
        case "08": return "AGO";
        case "09": return "SET";
        case "10": return "OCT";
        case "11": return "NOV";
        default: return "DIC";
    }
  }
  
  function getMonths($start, $end) {
      $startParsed = date_parse_from_format('Y-m-d', $start);
      $startMonth = $startParsed['month'];
      $startYear = $startParsed['year'];

      $endParsed = date_parse_from_format('Y-m-d', $end);
      $endMonth = $endParsed['month'];
      $endYear = $endParsed['year'];

      return ($endYear - $startYear) * 12 + ($endMonth - $startMonth) + 1;
  }
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
  $(document).ready(function(){
    $(".fuente8 tbody tr:odd").addClass("itemParTabla");
    $(".fuente8 tbody tr:even").addClass("itemImparTabla");
    
    //$(".fecha").datepicker({ dateFormat: "yy-mm-dd" });
    
    $("#reporte").click(function(){
      if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == "")
      {
        alert("Ingrese ambas fechas");
      }else{
        var startDate = new Date($('#fecha_inicio').val());
        var endDate = new Date($('#fecha_fin').val());

        if (startDate > endDate){
          alert("Rango de Fechas inválido");
        }else
        {
          $("#generar_reporte").submit();
        }
      }
    });
    
    //Reporte en Excel de ventas por tienda
    $('#verReporte').click(function(){
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
          location.href = base_url + "index.php/reportes/rptventas/resumen_ventastienda_detallado/"+fechaIF;
          
          
        }
      }
    });
    
  });
</script>
<div id="pagina">
    <div id="zonaContenido">
		<div align="center">
    <div id="tituloForm" class="header">REPORTES DE VENTAS POR LOCAL</div>
    <div id="frmBusqueda">
      <form method="post" action="" id="generar_reporte">
        Desde: <input type="date" id="fecha_inicio" name="fecha_inicio" class="fecha" 
                      value="<?php echo ((isset($_POST['reporte'])) ? $_POST['fecha_inicio'] : ''); ?>"> 
        Hasta: <input type="date" id="fecha_fin" name="fecha_fin" class="fecha" 
                      value="<?php echo ((isset($_POST['reporte'])) ? $_POST['fecha_fin'] : ''); ?>"> 
        <input type="hidden" name="reporte" value="">
        <input type="button" id="reporte" value="Generar" class="btn btn-success">
        <a href="javascript:;" id="verReporte">
          <img src="<?=$base_url;?>public/images/icons/xls.png" style="width:40px; border:none;" class="imgBoton" align="absmiddle"/>
        </a>
      </form>
      <?php if(isset($_POST['reporte'])): ?>
      <?php
        $inicio = explode('-',$fecha_inicio);
        $mesInicio = $inicio[1];
        $anioInicio = $inicio[0];
        $fin = explode('-',$fecha_fin);
        $mesFin = $fin[1];
        $anioFin = $fin[0];
      ?>
	  <style>
	  .fechase{
		  color:rgb(38, 152, 219);
	  }
	  </style>
      <br><br>
      Reporte de ventas por tienda desde <span class="fechase"> <?php echo $fecha_inicio; ?></span> 
      hasta el <span class="fechase"><?php echo $fecha_fin; ?></span><br/>
      <table class="fuente8" cellspacing="0" cellpadding="3" border="0" id="Table1">
        <br>
        <thead>
            <tr class="cabeceraTablaResultado"><th colspan="3">Resumen</th></tr>
            <tr class="cabeceraTabla"><th >TIENDA</th><th >DIRECCIÓN</th><th> Ventas S/.</th></tr>
        </thead>
        <tbody>
        <?php 
            $total = 0;
            $totalt=0;
            $total_filas = count($resumen);

            if($total_filas > 0){
                    //$cont=0;
                  foreach($resumen as $fila):

                           ECHO "<tr STYLE='text-align: center;'><td>{$fila['nombre']}</td><td>{$fila['direccion']}</td><td>S/.{$fila['VENTAS']}</td>"; 
                           $total += $fila['VENTAS'];
                  endforeach;
                  //echo $ver;
            }else{
                  echo "<tr><td>No hay filas!!!</td>";
            }
          ?>
        <tr><td colspan="2" STYLE='text-align: right;'>TOTAL</td><td STYLE='text-align: center;'>S/.<?php echo $total; ?></td></tr>
        </tbody>
      </table>
      
      <div id="chart_resumen" style="width: 900px; height: 500px;"></div>
      <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            
            function drawChart() {
              var data = google.visualization.arrayToDataTable([
                ['Vendedor', 'Ventas S/.'],
                
				
                <?php 
                $i = 0;
                foreach($resumen as $fila):
                
                  $nombre = $fila['nombre'];
                  $i++;
                  /*if($i == $total_filas)
                    echo "['{$nombre}',{$fila['VENTAS']}]";
                  else*/
                    echo "['{$nombre}',{$fila['VENTAS']}],";

                endforeach; ?>
              ]);

              var options = {
                title: 'Resumen de Ventas'
              };

              var chart = new google.visualization.PieChart(document.getElementById('chart_resumen'));
              chart.draw(data, options);
            }
      </script>
      <br/>
      <br/>
      <?php
        
        $months  = getMonths($fecha_inicio,$fecha_fin)+2;
      ?>
      <table class="fuente8" cellspacing="0" cellpadding="3" border="0" id="Table1">
      <thead>
      <tr class="cabeceraTablaResultado"><th colspan="<?php echo $months; ?>">Detalle Mensual</th></tr>
      <tr class="cabeceraTabla">
      <th  style="text-align:right"></th>
      <?php 
        for($i = $anioInicio; $i<=$anioFin;$i++):
          if($anioInicio == $anioFin):
            $span = intval($mesFin)-intval($mesInicio)+1;
            echo "<th colspan=\"$span\">$i</th>";
          else:
            if($i == $anioFin):
              $span = intval($mesFin);
              echo "<th colspan=\"$span\">$i</th>";
            elseif($i == $anioInicio):
              $span = 12-intval($mesInicio)+1;
              echo "<th colspan=\"$span\">$i</th>";
            else:
              $span = 12;
              echo "<th colspan=\"$span\">$i</th>";
            endif;
          endif;
        endfor;
      ?>
      </tr>
      <tr class="cabeceraTabla">
      <?php
	  echo "<th STYLE='text-align:left;'>TIENDA</th>";
        for($i = $anioInicio; $i<=$anioFin;$i++):
          if($anioInicio == $anioFin):
            for($j = intval($mesInicio); $j <= intval($mesFin); $j++):
              echo "<th>".getMes($j)."</th>";
            endfor;
          else:
            if($i == $anioFin):
              for($j = 1; $j <= intval($mesFin); $j++):
                echo "<th>".getMes($j)."</th>";
              endfor;
            elseif($i == $anioInicio):
              for($j = intval($mesInicio); $j <= 12; $j++):
                echo "<th>".getMes($j)."</th>";
              endfor;
            else:
              for($j = 1; $j <= 12; $j++):
                echo "<th>".getMes($j)."</th>";
              endfor;
            endif;
          endif;
        endfor;
      ?>
      </tr>
      </thead>
      <tbody>
      <?php
        $sumas = array();
        foreach($mensual as $fila):
          $keys = array_keys($fila);
          echo "<tr STYLE='text-align:left;'>";
          foreach($keys as $key):
            if(!in_array($key,array('VENTAS','nombre')))
            {
              if(isset($sumas[$key]))
              $sumas[$key] += $fila[$key];
              else
              $sumas[$key] = $fila[$key];
            }
            echo "<td>{$fila[$key]}</td>";
          endforeach;
          echo "</tr>";
        endforeach; 
      ?>
      <tr><td >TOTALES</td>
      <?php foreach($sumas as $suma):?>
        <?php echo '<td STYLE="text-align:left;">'.number_format($suma,2,'.','').'</td>'; ?>
      <?php endforeach; ?>
      </tr>
      </tbody>
      </table>
      <div id="chart_mensual" style="width: 900px; height: 500px;"></div>
      <script type="text/javascript">
        google.setOnLoadCallback(drawColumnChart);
        function drawColumnChart() {
          var data = google.visualization.arrayToDataTable([
            <?php 
            $i = 0;
            $cadena = "";
            $nombres = array();
            foreach($mensual as $fila):
            
              $nombre = $fila['nombre'];
              $nombres[] = $nombre;
              $i++;
              $cadena .= "'$nombre',";
            endforeach;
            
            $cadena = substr($cadena,0,strlen($cadena)-1);
            echo "['Periodo',$cadena],";
            $arreglo = array();
            $cadena = '';
            $periodo = array();
            foreach($mensual as $fila):
              for($i = $anioInicio; $i<=$anioFin;$i++):
                if($anioInicio == $anioFin):
                  for($j = intval($mesInicio); $j <= intval($mesFin); $j++):
                    $arreglo[$fila['nombre']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                    if(!in_array(getMes($j).'-'.$i,$periodo))
                      $periodo[] = getMes($j).'-'.$i;
                  endfor;
                else:
                  if($i == $anioFin):
                    for($j = 1; $j <= intval($mesFin); $j++):
                      $arreglo[$fila['nombre']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                      if(!in_array(getMes($j).'-'.$i,$periodo))
                        $periodo[] = getMes($j).'-'.$i;
                    endfor;
                  elseif($i == $anioInicio):
                    for($j = intval($mesInicio); $j <= 12; $j++):
                      $arreglo[$fila['nombre']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                      if(!in_array(getMes($j).'-'.$i,$periodo))
                        $periodo[] = getMes($j).'-'.$i;
                    endfor;
                  else:
                    for($j = 1; $j <= 12; $j++):
                      $arreglo[$fila['nombre']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                      if(!in_array(getMes($j).'-'.$i,$periodo))
                        $periodo[] = getMes($j).'-'.$i;
                    endfor;
                  endif;
                endif;
              endfor;
            endforeach;
            
            
            $datarow = "";
            foreach($periodo as $mes):
              
              $row = "['$mes',";
              foreach($nombres as $nombre):
                $row .= $arreglo[$nombre][$mes].",";
              endforeach;
              $row = substr($row,0,strlen($row)-1);
              $datarow .= $row.'],';
            endforeach;
            
            $datarow = substr($datarow,0,strlen($datarow)-1);
            
            echo $datarow;
            ?>
          ]);

          var options = {
            title: 'Comparativo Ventas Mes a Mes',
          };

          var chart = new google.visualization.ColumnChart(document.getElementById('chart_mensual'));
          chart.draw(data, options);
        }
      </script>
      <br/>
      <br/>
      
      <table class="fuente8"  cellspacing="0" cellpadding="3" border="0" id="Table1">
      <thead>
        <tr class="cabeceraTablaResultado"><th colspan="<?php echo (2+(1+($anioFin-$anioInicio))); ?>" align="center">Detalle Anual</th></tr>
        <tr class="cabeceraTabla" STYLE='text-align:left;'>
        <th >TIENDA</th>
        <?php 
          for($i = $anioInicio; $i<=$anioFin;$i++):
              echo "<th >$i</th>";
          endfor;
        ?>
        </tr>
      </thead>
      <tbody>
      <?php
        $sumas = array();
        foreach($anual as $fila):
          $keys = array_keys($fila);
          echo "<tr STYLE='text-align:left;'>";
		  
          foreach($keys as $key):
            if(!in_array($key,array('VENTAS','nombre')))
            {
              if(isset($sumas[$key]))
              $sumas[$key] += $fila[$key];
              else
              $sumas[$key] = $fila[$key];
            }
            echo "<td>{$fila[$key]}</td>";
          endforeach;
          echo "</tr>";
        endforeach;
        
      ?>
      <tr><td colspan="1">TOTALES</td>
      <?php foreach($sumas as $suma):?>
        <?php echo '<td STYLE="text-align:left;">'.number_format($suma,2,'.','').'</td>'; ?>
      <?php endforeach; ?>
      </tr>
      </tbody>
      </table>
      <div id="chart_anual" style="width: 900px; height: 500px;"></div>
      <script>
      google.setOnLoadCallback(drawBarChart);
      function drawBarChart() {
        var data = google.visualization.arrayToDataTable([
        <?php
          
          $arreglo = array();
          foreach($anual as $fila):
            $keys = array_keys($fila);
            foreach($keys as $key):
              $arreglo[$fila['nombre']][$key] = $fila[$key];
            endforeach;
          endforeach;
          
          echo "['Periodo','".implode("','",$nombres)."'],";
          
          $datarow = "";
          for($i=$anioInicio;$i<=$anioFin;$i++):
            
            $row = "['$i',";
            foreach($nombres as $nombre):
              $row .= $arreglo[$nombre]['y'.$i].",";
            endforeach;
            $row = substr($row,0,strlen($row)-1);
            $datarow .= $row.'],';
          endfor;
          
          $datarow = substr($datarow,0,strlen($datarow)-1);
          
          echo $datarow;
          
        ?>
        ]);

        var options = {
          title: 'Comparativo Ventas Anuales',
          vAxis: {title: 'Año',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_anual'));
        chart.draw(data, options);
      }
      </script>
      <?php endif; ?>
    </div>
		</div>
    </div>
</div>