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
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
  $(document).ready(function(){
      base_url = $("#base_url").val();
    $(".fuente8 tbody tr:odd").addClass("itemParTabla");
    $(".fuente8 tbody tr:even").addClass("itemImparTabla");
    
    $(".fecha").datepicker({ dateFormat: "yy-mm-dd" });
    
    $("#reporte").click(function(){
    
     /* if($('#fecha_inicio').val() == "" || $('#fecha_fin').val() == "")
      {

       alert("Ingrese ambas fechas");
      }else{
        var startDate = new Date($('#fecha_inicio').val());
        var endDate = new Date($('#fecha_fin').val());

        if (startDate > endDate){
          alert("Rango de Fechas inválido");
        }else
        {*/
          $("#generar_reporte").submit();
      /*  }
      }
    });*/
  });
    function factura(oper,tipo,codigo){
	var op;
	if(oper==0){
		op="C";
	}else{
		op="V";
	}
		switch(tipo){
			case 1:
				var url = base_url+"index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete_formato1/"+op+"/"+codigo+"/N/0";
				break;
			case 2:
				var url = base_url+"index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete_formato11/"+op+"/"+codigo+"/F/0";
				break;
		}
		
        window.open(url,'',"width=800,height=600,menubars=no,resizable=no;");
    }
    function boleta(oper,codigo){
		if(oper==0){
			var url = base_url+"index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete_formato1_boleta/C/"+codigo+"/B/0";
		}
		if(oper==1){
			var url = base_url+"index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete_formato1_boleta/V/"+codigo+"/B/0";
		}
       
        window.open(url,'',"width=800,height=600,menubars=no,resizable=no;");
    }
</script>
<script>
  var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) } 
 return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table);
	//$('#pintado').html(table.innerHTML);
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
    window.location.href = uri + base64(format(template, ctx));
  }
})()



</script>


<div id="pagina">
    <div id="zonaContenido">
		<div align="center">
    <div id="tituloForm" class="header">REPORTES DE PRODUCTOS EN STOCK POR MAS DE 15 DIAS</div>
    <div id="frmBusqueda">
      <br>
        <p>Reporte de los 150 productos que no han sido vendidos en 15 dias</p>
      <br><br>
       <table class="fuente8" cellspacing="0" cellpadding="3" border="0" >
      <tr>
      <td>
	  <ul  onclick="tableToExcel('Table1', 'exceltabla')" class="lista_botones"><li id="imprimir">Descargar EXCEL Resumen</li></ul>
	
      </td>
      </tr>
	</table>
      
     
    <table class="fuente8" cellspacing="0" cellpadding="3" border="0" id="Table1">
      <thead>
        <tr class="cabeceraTablaResultado"><th colspan="5">Resumen</th></tr>
        <tr class="cabeceraTabla">
            <th>Item</th>
            <th>Nombre de Producto</th>
            <th colspan="1">Fecha de ultima venta</th>
            <th>Dias en stock</th>
        </tr>
      </thead>
      <tbody>
      <?php
            $i=1;
            if(count($lista)>0){
            foreach($lista as $indice=>$valor){
            $class = $indice%2==0?'itemParTabla':'itemImparTabla';
            ?>
            <tr class="<?php echo $class;?>">
            <td><div align="center"><?php echo $i;?></div></td>
            <td><div align="center"><?php echo $valor[0];?></div></td>
            <td><div align="center"><?php echo $valor[1];?></div></td>
            <td><div align="center"><?php echo $valor[2];?></div></td>
            </tr>
            <?php $i++;} }?>
      </tbody>
      </table>
      
    </div>
    </div>
    </div>
</div>