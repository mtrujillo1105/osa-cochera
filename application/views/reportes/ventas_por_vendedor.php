<?php

  function getMes($mes){
    $mes = str_pad((int) $mes,2,"0",STR_PAD_LEFT);
    switch ($mes) {
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
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
  $(document).ready(function(){
    $(".fuente8 tbody tr:odd").addClass("itemParTabla");
    $(".fuente8 tbody tr:even").addClass("itemImparTabla");
    
    $(".fecha").datepicker({ dateFormat: "yy-mm-dd" });
    
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

    $("#reporteExcelVendedor").click(function(){
      if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == ""){
        alert("Ingrese ambas fechas");
      }
      else{
        var startDate = $('#fecha_inicio').val();
        var endDate = $('#fecha_fin').val();

        if (startDate > endDate){
          alert("Rango de Fechas inválido");
        }
        else {
          fechaI = startDate.split('-');
          fechaF = endDate.split('-');
          fechaIF = startDate+"/"+endDate;

          location.href = "<?=base_url();?>index.php/reportes/rptventas/filtroVendedorExcel/"+fechaIF;
        }
      }
    });

    $("#reporteExcelVendedorGeneral").click(function(){
      if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == ""){
        alert("Ingrese ambas fechas");
      }
      else{
        var startDate = $('#fecha_inicio').val();
        var endDate = $('#fecha_fin').val();

        if (startDate > endDate){
          alert("Rango de Fechas inválido");
        }
        else {
          fechaI = startDate.split('-');
          fechaF = endDate.split('-');
          fechaIF = startDate+"/"+endDate;

          location.href = "<?=base_url();?>index.php/reportes/rptventas/filtroVendedorExcelGeneral/"+fechaIF;
        }
      }
    });

    $("#reporteExcelVendedorDet").click(function(){
      if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == ""){
        alert("Ingrese ambas fechas");
      }
      else{
        var startDate = $('#fecha_inicio').val();
        var endDate = $('#fecha_fin').val();

        if (startDate > endDate){
          alert("Rango de Fechas inválido");
        }
        else {
          fechaI = startDate.split('-');
          fechaF = endDate.split('-');
          fechaIF = startDate+"/"+endDate;
          idVendedor = $("#cboVendedor").val();

          location.href = "<?=base_url();?>index.php/reportes/rptventas/filtroVendedorExcelDet/"+idVendedor+"/"+fechaIF;
        }
      }
    });
  });
</script>
<!--link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="< =base_url();?>/bootstrap/css/bootstrap.css" -->
<style>
input[type="search"]{
    background-color: #fff;
    text-transform: uppercase;
    color: #000;
    width: 240px;
    border-color: #a3b3bb;
    border-style: solid;
    border-width: 1px;
    font-size: 9pt;
    font-weight: bold;
    padding: 0.5em;
}
</style>
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
    <div id="tituloForm" class="header">REPORTES DE VENTAS POR CAJERO</div>
    <div id="frmBusqueda">
      <form method="post" action="" id="generar_reporte">
        Desde: <input type="date" id="fecha_inicio" name="fecha_inicio" class="cajaMedia" 
                      value="<?php echo ((isset($_POST['reporte'])) ? $_POST['fecha_inicio'] : ''); ?>"> 
        Hasta: <input type="date" id="fecha_fin" name="fecha_fin" class="cajaMedia" 
                      value="<?php echo ((isset($_POST['reporte'])) ? $_POST['fecha_fin'] : ''); ?>"> 
        <input type="hidden" name="reporte" value="">
        <input type="button" id="reporte" value="Generar" class="btn btn-success">
        <a href="javascript:;" id="reporteExcelVendedorGeneral">
          <img src="<?=$base_url;?>public/images/icons/xls.png" style="width:40px; border:none;" class="imgBoton" align="absmiddle"/>
        </a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <span class="d-none">Cajero: </span>
        <select id="cboVendedor" name="cboVendedor" class="cajaGrande d-none">
          <option value="0">TODOS</option>
          <?=$cboVendedor;?>
        </select>
        <a href="javascript:;" id="reporteExcelVendedorDet" class="d-none">
          <img src="<?=$base_url;?>public/images/icons/xls.png" style="width:40px; border:none;" class="imgBoton" align="absmiddle"/>
        </a>
      </form> <?php
        if(isset($_POST['reporte'])):
          $inicio = explode('-',$fecha_inicio);
          $mesInicio = $inicio[1];
          $anioInicio = $inicio[0];
          $fin = explode('-',$fecha_fin);
          $mesFin = $fin[1];
          $anioFin = $fin[0]; ?>
      <br>
      <table class="fuente8 display" cellspacing="0" cellpadding="3" border="0" id="resumen">
        <thead>
          <tr class="cabeceraTablaResultado"><th colspan="3">Reporte de ventas por vendedor desde <?php echo $fecha_inicio; ?> hasta el <?php echo $fecha_fin; ?></th></tr>
          <tr class="cabeceraTabla">
            <th>Nombre</th>
            <th>Apellidos</th>
            <th> Ventas S/.</th></tr>
        </thead>
        <tbody> <?php
          $total = 0;
          $total_filas = count($resumen);
          foreach($resumen as $fila):
            $total += $fila['VENTAS'];
            echo "<tr><td>{$fila['NOMBRE']}</td><td>{$fila['PATERNO']}</td><td>{$fila['VENTAS']}</td>";
          endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="cabeceraTabla">
            <th colspan="2">TOTAL</th>
            <th><?php echo $total; ?></th>
          </tr>
        </tfoot>
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
                
                  $nombre = $fila['NOMBRE']." ".$fila['PATERNO'];
                  $i++;
                  if($i == $total_filas)
                    echo "['{$nombre}',{$fila['VENTAS']}]";
                  else
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
      <table class="fuente8 display" cellspacing="0" cellpadding="3" border="0" id="resumenMensual">
        <thead>
          <tr class="cabeceraTablaResultado">
            <th colspan="<?php echo $months; ?>">Detalle Mensual</th>
          </tr>
          <tr class="cabeceraTabla">
            <th rowspan="2" valign="bottom">Nombre</th>
            <th rowspan="2" valign="bottom">Apellidos</th> <?php 
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
              endfor; ?>
          </tr>
          <tr class="cabeceraTabla"> <?php
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
            endfor; ?>
          </tr>
        </thead>
        <tbody> <?php
          $sumas = array();
          foreach($mensual as $fila):
            $keys = array_keys($fila);
            echo "<tr>";
            foreach($keys as $key):
              if(!in_array($key,array('VENTAS','PATERNO','NOMBRE')))
              {
                if(isset($sumas[$key]))
                $sumas[$key] += $fila[$key];
                else
                $sumas[$key] = $fila[$key];
              }
              echo "<td>{$fila[$key]}</td>";
            endforeach;
            echo "</tr>";
            endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="cabeceraTabla">
            <th colspan="2">TOTALES</th>
            <?php foreach($sumas as $suma):?>
              <?php echo '<th>'.number_format($suma,2,'.','').'</th>'; ?>
            <?php endforeach; ?>
          </tr>
        </tfoot>
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
            
              $nombre = $fila['PATERNO'];
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
                    $arreglo[$fila['PATERNO']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                    if(!in_array(getMes($j).'-'.$i,$periodo))
                      $periodo[] = getMes($j).'-'.$i;
                  endfor;
                else:
                  if($i == $anioFin):
                    for($j = 1; $j <= intval($mesFin); $j++):
                      $arreglo[$fila['PATERNO']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                      if(!in_array(getMes($j).'-'.$i,$periodo))
                        $periodo[] = getMes($j).'-'.$i;
                    endfor;
                  elseif($i == $anioInicio):
                    for($j = intval($mesInicio); $j <= 12; $j++):
                      $arreglo[$fila['PATERNO']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                      if(!in_array(getMes($j).'-'.$i,$periodo))
                        $periodo[] = getMes($j).'-'.$i;
                    endfor;
                  else:
                    for($j = 1; $j <= 12; $j++):
                      $arreglo[$fila['PATERNO']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
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
      
      <table class="fuente8 display" cellspacing="0" cellpadding="3" border="0" id="resumenAnual">
      <thead>
        <tr class="cabeceraTablaResultado"><th colspan="<?php echo (2+(1+($anioFin-$anioInicio))); ?>" align="center">Detalle Anual</th></tr>
        <tr class="cabeceraTabla">
        <th>Nombre</th>
        <th>Apellido</th>
        <?php 
          for($i = $anioInicio; $i<=$anioFin;$i++):
              echo "<th>$i</th>";
          endfor;
        ?>
        </tr>
      </thead>
      <tbody>
      <?php
        $sumas = array();
        foreach($anual as $fila):
          $keys = array_keys($fila);
          echo "<tr>";
          foreach($keys as $key):
            if(!in_array($key,array('VENTAS','PATERNO','NOMBRE')))
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
      </tbody>
      <tfoot>
        <tr class="cabeceraTabla">
          <td colspan="2">TOTALES</td>
          <?php foreach($sumas as $suma):?>
            <?php echo '<td>'.number_format($suma,2,'.','').'</td>'; ?>
          <?php endforeach; ?>
        </tr>
      </tfoot>
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
              $arreglo[$fila['PATERNO']][$key] = $fila[$key];
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

<script type="text/javascript">
  $(document).ready( function () {
      $('#resumen').DataTable({ responsive: true,
          language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Buscar",
            emptyTable: "No hay información",
                info: "Mostrando desde _START_ hasta _END_ de _TOTAL_ entradas",
                infoEmpty: "Mostrando desde 0 hasta 0 de 0 Entradas",
                infoFiltered: "(Filtrado de _MAX_ total entradas)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ Entradas",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                zeroRecords: "Sin resultados encontrados.",
                paginate: {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
          }
      });

      $('#resumenMensual').DataTable({ responsive: true,
          language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Buscar",
            emptyTable: "No hay información",
                info: "Mostrando desde _START_ hasta _END_ de _TOTAL_ entradas",
                infoEmpty: "Mostrando desde 0 hasta 0 de 0 Entradas",
                infoFiltered: "(Filtrado de _MAX_ total entradas)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ Entradas",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                zeroRecords: "Sin resultados encontrados.",
                paginate: {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
          }
      });

      $('#resumenAnual').DataTable({ responsive: true,
          language: {
            lengthMenu: "_MENU_",
            search: "_INPUT_",
            searchPlaceholder: "Buscar",
            emptyTable: "No hay información",
                info: "Mostrando desde _START_ hasta _END_ de _TOTAL_ entradas",
                infoEmpty: "Mostrando desde 0 hasta 0 de 0 Entradas",
                infoFiltered: "(Filtrado de _MAX_ total entradas)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ Entradas",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                zeroRecords: "Sin resultados encontrados.",
                paginate: {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
          }
      });


  } );
</script>