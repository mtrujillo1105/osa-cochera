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
    
      if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == ""){
        alert("Ingrese ambas fechas");
      }
      else{
          if( $('#cliente').val() != ""){
            var startDate = new Date($('#fecha_inicio').val());
            var endDate = new Date($('#fecha_fin').val());

          	if (startDate > endDate){
            		alert("Rango de Fechas inválido");
          	}
            else{
              $("#reporteT").val('cliente');
           	  $("#generar_reporte").submit();
         		 }
      	  }
          else{
                var startDate = new Date($('#fecha_inicio').val());
                var endDate = new Date($('#fecha_fin').val());
                $("#reporteT").val('general');

                if (startDate > endDate){
                  alert("Rango de Fechas inválido");
                }
                else{
                  $("#generar_reporte").submit();
                }
      	  }
       }
    });

    $("#clean").click(function(){
      $("#cliente").val("");
      $("#buscar_cliente").val("");
      $("#nombre_cliente").val("");
      $("#reporte").click();
    });

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

          location.href = "<?=base_url();?>" + "index.php/reportes/rptventas/resumen_ventas_detallado/"+fechaIF;
        }
      }
    });

  });


  $(function () {
   
//****** nuevo para ruc
      $("#buscar_cliente").autocomplete({
          source: function (request, response) {
              $.ajax({
                  url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                  type: "POST",
                  data: {
                      term: $("#buscar_cliente").val()
                  },
                  dataType: "json",
                  success: function (data) {
                      response(data);
                  }
              });
          },
          select: function (event, ui) {
             $("#nombre_cliente").val(ui.item.nombre);
              $("#cliente").val(ui.item.codigo);
              $("#buscar_cliente").val(ui.item.ruc);
          },
          minLength: 2
      });

      //AUTOCOMENTADO EN CLIENTE BUSCAR
      $("#nombre_cliente").autocomplete({
          //flag = $("#flagBS").val();
          source: function (request, response) {
              $.ajax({
                  url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                  type: "POST",
                  data: {
                      term: $("#nombre_cliente").val()
                  },
                  dataType: "json",
                  success: function (data) {
                      response(data);
                  }
              });

          },
          select: function (event, ui) {
              $("#nombre_cliente").val(ui.item.nombre);
              $("#cliente").val(ui.item.codigo);
              $("#buscar_cliente").val(ui.item.ruc);
          },
          minLength: 2
      });

  });
  function limpiarucr(){
	  var cliente = $("#nombre_cliente").val();
	   if(cliente=="" ||cliente==" " ){
		   $("#cliente").val("");
           $("#buscar_cliente").val("");
           $("#nombre_cliente").val(""); 
	   }
  }
</script>
<!--link rel="stylesheet" href="<?=base_url();?>resources/assets/bootstrap/css/bootstrap.css" /-->
<div id="pagina">
    <div id="zonaContenido">
		<div align="center">
    <div id="tituloForm" class="header">REPORTES DE VENTAS POR CLIENTE</div>
    <div id="frmBusqueda">
      <form method="post" action="" id="generar_reporte">
      <table id="conslta" border="0" width="100%">
      <tr>
      	<td width="8%">  Desde: </td>
      	<td width="40%">
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="cajaMedia fecha" value="<?=$fecha_inicio;?>">
            Hasta: <input type="date" id="fecha_fin" name="fecha_fin" class="cajaMedia fecha" value="<?=$fecha_fin;?>">
        </td>
        <td width="20%">
          <input type="button" id="clean" value="Ver Todos" class="btn btn-primary">
        </td>
        <td rowspan="2" style="text-align: center">
          <a href="javascript:;" id="verReporte">
              <img src="<?=$base_url;?>public/images/icons/xls.png" style="width:40px; border:none;" class="imgBoton" align="absmiddle"/><br>
              Detallado
          </a>
        </td>
      </tr>
      <tr>
      	<td>Cliente:</td>
       	<td> 
          <input type="hidden" id="cliente" name="cliente" value="<?=$cliente;?>">
          <input type="text" id="buscar_cliente" class="cajaPequena" name="buscar_cliente" value="<?=$buscar_cliente;?>" placeholder="RUC / DNI">
          <input type="text" id="nombre_cliente" class="cajaGrande" name="nombre_cliente" onblur="limpiarucr()" value="<?=$nombre_cliente;?>"  placeholder=" NOMBRE /RAZON SOCIAL">
          <input type="hidden" name="reporteT" id="reporteT" value="">
       	</td>
        <td>
          <input type="button" id="reporte" value="Buscar" class="btn btn-primary">
        </td>
       </tr>
      </table>
        <style>
        #conslta td{padding: 5px 0;}
        /*#buscar_cliente{ width:70px; background:#FFE8E8 !important; cursor:not-allowed}*/
        #buscar_cliente{ width:70px;}
        #nombre_cliente{ width:250px;}
        </style>
      </form>
      <?php if(isset($_POST['reporteT'])): ?>
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
      Reporte de ventas por cliente desde <span class="fechase"> <?php echo $fecha_inicio; ?></span> hasta el <span class="fechase"><?php echo $fecha_fin; ?></span><br/>
			<table class="fuente8" cellspacing="0" cellpadding="3" border="0" id="Table1">
			<br>
      <thead>
      <tr class="cabeceraTablaResultado"><th colspan="3">Resumen</th></tr>
      <tr class="cabeceraTabla"><th colspan="2">Cliente</th><th> Ventas S/.</th></tr>
      </thead>
      <tbody>
      <?php 
	  $total = 0;
	  $totalt=0;
      $total_filas = count($resumen);

	  if($total_filas > 0){
		  //$cont=0;
		foreach($resumen as $fila):
			IF($fila['NOMBRE']!=NULL || $fila['VENTAS'] !=NULL || $fila['RUC']!= NULL ){
			 ECHO "<tr><td>{$fila['NOMBRE']}</td><td>{$fila['RUC']}</td><td>S/.{$fila['VENTAS']}</td>"; 
			 $total += $fila['VENTAS'];}
		endforeach;
		//echo $ver;
	  }else{
		echo "<tr><td>No hay filas!!!</td>";
	  }
	?>
      <tr><td colspan="2">TOTAL</td><td>S/.<?php echo $total; ?></td></tr>
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
                IF($fila['NOMBRE']!=NULL || $fila['VENTAS'] !=NULL || $fila['RUC']!= NULL ){
                	
                  $nombre = $fila['NOMBRE'].' '.$fila['RUC'];
                  $i++;
                  /*if($i == $total_filas)
                    echo "['{$nombre}',{$fila['VENTAS']}]";
                  else*/
                    echo "['{$nombre}',{$fila['VENTAS']}],";
                }
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
       <th rowspan="2" >Cliente</th>
      <th rowspan="1" ></th>
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
	  echo "<th></th>";
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
        if($fila['NOMBRE']!=NULL ){
          $keys = array_keys($fila);
          echo "</td>";
          foreach($keys as $key):
        
            if(!in_array($key,array('VENTAS','RUC','NOMBRE')))
            {
              if(isset($sumas[$key]))
              $sumas[$key] += $fila[$key];
              else
              $sumas[$key] = $fila[$key];
            }
            echo "<td>{$fila[$key]}</td>";
          endforeach;
          echo "</tr>";
        }
        endforeach; 
      ?>
      <tr><td colspan="2">TOTALES</td>
      <?php foreach($sumas as $suma):?>
        <?php 
        if($fila['NOMBRE']!=NULL || $fila['RUC']!= NULL ){
        	echo '<td>'.number_format($suma,2,'.','').'</td>'; 
        }?>
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
            IF($fila['NOMBRE']!= NULL  ){
              $nombre = $fila['NOMBRE'];
              $nombres[] = $nombre;
              $i++;
              $cadena .= "'$nombre',";
}
            endforeach;
            
            
            
            $cadena = substr($cadena,0,strlen($cadena)-1);
            echo "['Periodo',$cadena],";
            $arreglo = array();
            $cadena = '';
            $periodo = array();
            foreach($mensual as $fila):
           
            IF($fila['NOMBRE']!= NULL ){
              for($i = $anioInicio; $i<=$anioFin;$i++):
                if($anioInicio == $anioFin):
                  for($j = intval($mesInicio); $j <= intval($mesFin); $j++):
                    $arreglo[$fila['NOMBRE']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                    if(!in_array(getMes($j).'-'.$i,$periodo)){
                      $periodo[] = getMes($j).'-'.$i;
                    }
                  endfor;
              	  else:
                	  if($i == $anioFin):
                   	 for($j = 1; $j <= intval($mesFin); $j++):
                    	  $arreglo[$fila['NOMBRE']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                     	 if(!in_array(getMes($j).'-'.$i,$periodo)){
                      	  $periodo[] = getMes($j).'-'.$i;
                       	
                    	  }
                   	 endfor;
                	  elseif($i == $anioInicio):
                  	  for($j = intval($mesInicio); $j <= 12; $j++):
                     	 $arreglo[$fila['NOMBRE']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                     	 if(!in_array(getMes($j).'-'.$i,$periodo)){
                       	 $periodo[] = getMes($j).'-'.$i;
                       	
                      	}
                    	endfor;
                	  else:
                   	 for($j = 1; $j <= 12; $j++):
                     	 $arreglo[$fila['NOMBRE']][getMes($j).'-'.$i] = $fila['m'.$i.$j];
                    	  if(!in_array(getMes($j).'-'.$i,$periodo)){
                      	  $periodo[] = getMes($j).'-'.$i;
                       	
                     	 }
                    	endfor;
                  	endif;
                endif;
                
              endfor;
           }
            endforeach;
            
           
            
            $datarow = "";
            foreach($periodo as $mes):
            IF($fila['NOMBRE']!=NULL ){
              $row = "['$mes',";
              foreach($nombres as $nombre):
                $row .= $arreglo[$nombre][$mes].",";
              endforeach;
              $row = substr($row,0,strlen($row)-1);
              $datarow .= $row.'],';
            }
            endforeach;
            
            $datarow = substr($datarow,0,strlen($datarow)-1);
            
            echo $datarow;
          
            ?>
          ]);
          
        <?php 
       // echo "<script>alert('". $periodo[0]."persiodo');</script>";
        //echo "<script>alert('".$datarow."nombre');</script>";
        ?>
          
          var options = {
            title: 'Comparativo Ventas Mes a Mes'
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
        <tr class="cabeceraTabla">
        <th colspan="2">Cliente</th>
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
        IF($fila['NOMBRE']!= NULL ){
          $keys = array_keys($fila);
          echo "<tr>";
          foreach($keys as $key):
            if(!in_array($key,array('VENTAS','RUC','NOMBRE')))
            {
              if(isset($sumas[$key]))
              $sumas[$key] += $fila[$key];
              else
              $sumas[$key] = $fila[$key];
            }
            echo "<td>{$fila[$key]}</td>";
          endforeach;
          echo "</tr>";
        }
        endforeach;
        
      ?>
      <tr><td colspan="2">TOTALES</td>
      <?php foreach($sumas as $suma):
      IF($fila['RUC']!= NULL ){
        echo '<td>'.number_format($suma,2,'.','').'</td>'; 
      }
       endforeach; ?>
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
          IF($fila['RUC']!= NULL ){
            $keys = array_keys($fila);
            foreach($keys as $key):
              $arreglo[$fila['NOMBRE']][$key] = $fila[$key];
            endforeach;
          }
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