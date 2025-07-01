<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/maestros/area.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="<?=base_url();?>/bootstrap/css/bootstrap.css" />
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
<style>
.button {
  display: inline-block;
  border-radius: 4px;
  background-color: #35afe3;
  border: none;
  color: #FFFFFF;
  text-align: center;
  font-size: 12px;
  padding: 10px;
  width: 100px;
  transition: all 0.5s;
  cursor: pointer;
  margin: 5px;
}

.button span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  transition: 0.5s;
}

.button span:after {
  content: '\00bb';
  position: absolute;
  opacity: 0;
  top: 0;
  right: -20px;
  transition: 0.5s;
}

.button:hover span {
  padding-right: 25px;
}

.button:hover span:after {
  opacity: 1;
  right: 0;
}
</style>
<script type="text/javascript">
                    
 function ejecutarAjax2(){
    tipo_docu = $("#tipo_doc").val();
    tipo_oper = $("#tipo_oper").val();
    fecha1 = $("#fecha1").val();
    fecha2 = $("#fecha2").val();
    forma_pago =$("#forma_pago").val();
    vendedor = $("#cboVendedor").val();
    moneda = $("#moneda").val();

    var checkBox = document.getElementById("myCheck");


    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
      consolidado = 1;
    } else {
      consolidado = 0;
    }

    base_url=$("#base_url").val();
    $("#table-producto").DataTable().destroy();
    $(".viewData").html('');
    $.ajax({
      url : "<?=base_url();?>index.php/reportes/ventas/registro_ventas_table/",
      type: "POST",
      data: {
          "term": "",
          "tipo_oper": tipo_oper,
          "tipo_doc": tipo_docu,
          "fecha1": fecha1,
          "fecha2": fecha2,
          "forma_pago": forma_pago,
          "vendedor": vendedor,
          "moneda": moneda,
          "consolidado": consolidado
      },
      dataType: "json",
      success: function (data) {
        if (data.data != null && data.data != undefined){ 
          totales=data.totales;      

          $("#total_fac").val(totales.total_fac);
          $("#total_bol").val(totales.total_bol);
          $("#total_comp").val(totales.total_comp);
          $("#total_nota").val(totales.total_nota);
          $("#total").val(totales.total);
          $("#total_fac_dolar").val(totales.total_fac_dolar);
          $("#total_bol_dolar").val(totales.total_bol_dolar);
          $("#total_comp_dolar").val(totales.total_comp_dolar);
          $("#total_nota_dolar").val(totales.total_nota_dolar);
          $("#total_dolar").val(totales.total_dolar);
          $("#cantidad_fac").val(totales.cantidad);
          $.each(data.data, function(i, item) {
              table = '';
              table += '<tr>'+item.tachado1;
              
              table += '<td>'+item.tachado1 + item.fecha          +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.tipo_documento +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.serie          +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.numero         +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.num_doc        +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.razon_social   +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.moneda       +item.tachado2+ '</td>';
              //table += '<td>'+item.tachado1 + item.subtotal       +item.tachado2+ '</td>';
              //table += '<td>'+item.tachado1 + item.igv            +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.total          +item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.FORPAC_Descripcion + item.tachado2+ '</td>';
              table += '<td>'+item.tachado1 + item.estado         +item.tachado2+ '</td>';
              
              table += '</tr>';

              $(".viewData").append(table);
          });

          $('#table-producto').DataTable({ responsive: true,
            language: {

              lengthMenu: "_MENU_",
              search: "_INPUT_",
              searchPlaceholder: "Búsqueda Rápida",
              emptyTable: "No hay información",
              info: "Mostrando desde _START_ hasta _END_ de _TOTAL_ entradas",
              infoEmpty: "Sin resultados",
              infoFiltered: "(Filtrado de _MAX_ entradas)",
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
          $(".table-producto").show();
        }
      }
    });
  }
    jQuery(document).ready(function() {
        $(".fecha").datepicker({ 
            dateFormat: "yy-mm-dd" 

        });
        $("#imprimirArea").click(function() {
            tipo_docu = $("#tipo_doc").val();
            tipo_oper = $("#tipo_oper").val();
            fecha1 = $("#fecha1").val();
            fecha2 = $("#fecha2").val();
            forma_pago = $("#forma_pago").val();

            var url = "<?php echo base_url() ?>index.php/reportes/ventas/registro_ventas_pdf/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2+"/"+forma_pago;
            window.open(url, '', "menubars=no,resizable=no;");
        });

        $("#imprimirexcel").click(function() {

            tipo_docu = $("#tipo_doc").val();

            tipo_oper = $("#tipo_oper").val();

            fecha1 = $("#fecha1").val();

            fecha2 = $("#fecha2").val();

            location.href = "<?php echo base_url() ?>index.php/reportes/ventas/registro_ventas_excel2/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2;

        });
        
        $("#imprimirexcel_contador").click(function() {
            
            tipo_docu   = $("#tipo_doc").val();
            tipo_oper   = $("#tipo_oper").val();
            fecha1      = $("#fecha1").val();
            fecha2      = $("#fecha2").val();
            forma_pago  = $("#forma_pago").val();
            vendedor    = $("#cboVendedor").val();
            moneda      = $("#moneda").val();
            consolidado = $("#consolidado").val();

            if(fecha1==""){
              fecha1=1;
            }
            if(fecha2==""){
              fecha2=1;
            }
            if(tipo_docu==""){
              tipo_docu="-";
            }
            if(forma_pago==""){
              forma_pago="-";
            }
            if(vendedor==""){
              vendedor="-";
            }
            if(moneda==""){
              moneda="-";
            }

            var checkBox = document.getElementById("myCheck");

            if (checkBox.checked == true){
              consolidado = 1;
            } else {
              consolidado = 0;
            }

            location.href = "<?php echo base_url() ?>index.php/reportes/ventas/resumen_ventas_mensual/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2 + "/" + forma_pago + "/" + vendedor + "/" + moneda+"/"+consolidado; 
        });

    });
                </script>
<script type="text/javascript">
    jQuery(document).ready(function() {
       ejecutarAjax2();
       $("#tipo_doc").change(function() {

            //tipo_docu = $("#tipo_doc").val();
            //tipo_oper = $("#tipo_oper").val();
            //fecha1 = $("#fecha1").val();
           //fecha2 = $("#fecha2").val();

            //location.href = "<?php //echo base_url() ?>index.php/reportes/ventas/registro_ventas/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2;

        });
        $("#imprimirArea").click(function() {
            tipo_docu = $("#tipo_doc").val();
            tipo_oper = $("#tipo_oper").val();
            fecha1 = $("#fecha1").val();
            fecha2 = $("#fecha2").val();

            var url = "<?php echo base_url() ?>index.php/reportes/ventas/registro_ventas_pdf/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2;
            window.open(url, '', "menubars=no,resizable=no;");
        });

        $("#imprimirexcel").click(function() {

            tipo_docu = $("#tipo_doc").val();

            tipo_oper = $("#tipo_oper").val();

            fecha1 = $("#fecha1").val();

            fecha2 = $("#fecha2").val();

            location.href = "<?php echo base_url() ?>index.php/reportes/ventas/registro_ventas_excel2/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2;

        });
        
      


//error key
//         $("#fecha").key(function() {
//             tipo_docu = $("#tipo_doc").val();
//             tipo_oper = $("#tipo_oper").val();
//             fecha1 = $("#fecha1").val();
//             fecha2 = $("#fecha2").val();
//            location.href = "<?php echo base_url() ?>index.php/reportes/ventas/registro_ventas/" + tipo_oper + "/" + tipo_docu + "/" + fecha1 + "/" + fecha2;
//       });




    });

function myFunction() {
  // Get the checkbox
  var checkBox = document.getElementById("myCheck");
  // Get the output text
  var text = document.getElementById("text");

  // If the checkbox is checked, display the output text
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
    text.style.display = "none";
  }
}
function limpiar(){
  $("#tipo_doc").val('');
 
  $("#fecha1").val('');
  $("#fecha2").val('');
  $("#forma_pago").val('');
  $("#cboVendedor").val('');
  $("#moneda").val('');
  ejecutarAjax2();
}

</script>    
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_tabla;?></div>
            <input type="hidden" name="tipo_oper" id="tipo_oper" value="<?php echo $tipo_oper ?>" />
             <style>
              .fechase{
                  color:rgb(38, 152, 219);
              }
              </style>
            <div>
                <div>
                    <div>
                        <table style="width: 100%; border:1px;" class="fuente8">
                                                        
                                <tr>

                                     
                                     
                                   
                                </tr>
                                <tr>
                                    
                                </tr>
                                <tr>
                                    <td><label>Tipo de Documento</label></td>
                                    <td>
                                        <select id="tipo_doc" name="tipo_doc" class="comboGrande">
                                        <option value="" selected="selected" >::Seleccione::</option>
                                        <option value="V" <?php if ($tipo_docu == 'V') echo ''; ?>>REGISTRO DE VENTAS</option>
                                        <option value="F" <?php if ($tipo_docu == 'F') echo ''; ?>>FACTURAS</option>
                                        <option value="B" <?php if ($tipo_docu == 'B') echo 'selected="selected"'; ?>>BOLETAS</option>
                                        <option value="N" <?php if ($tipo_docu == 'N') echo 'selected="selected"'; ?>>COMPROBANTES</option>
                                        <option value="C" <?php if ($tipo_docu == 'C') echo 'selected="selected"'; ?>>NOTAS DE CREDITOS</option>
                                       
                                        <option value="T">TODOS</option>
                                        </select> 
                                    </td>
                                    <td>Desde:</td>
                                     <td>
                                        <input type="text" id="fecha1" name="fecha1" class="fecha" value="<?php echo $fecha1; ?>" readonly> 
                                     </td>
                                </tr>
                                <tr>
                                    <td>Forma de Pago: </td>
                                    <td>
                                        <select name="forma_pago" id="forma_pago" class="comboGrande">
                                            <?php echo $cboFormaPago; ?> 
                                        </select>
                                    </td>
                                    <td>Hasta:</td>
                                     <td>
                                        <input type="text" id="fecha2" name="fecha2" class="fecha" value="<?php echo $fecha2; ?>" readonly> 
                                     </td>
                                </tr>
                                <tr>
                                    <td>Vendedor</td>
                                     
                                    <td><select id="cboVendedor" name="cboVendedor" class="comboGrande">
                                        <option value="">Seleccionar</option>
                                        option
                                    <?=$cboVendedor;?>
                                    </select>

                                    </td>
                                    <td><input type="checkbox" id="myCheck" onclick="myFunction()">Consolidado</td>
                                    <td><span id="text" style="display:none">Todas las sucursales</span></td>
                                </tr>
                                <tr>
                                  <td>Moneda</td>
                                    <td> 
                                      <select name="moneda" id="moneda" class="comboGrande">
                                        <?php echo $cboMoneda; ?>
                                      </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">
                                        <button class="button" style="vertical-align:middle" onclick="ejecutarAjax2()"><span>Buscar </span></button>
                                    </td>
                                    <td colspan="2" align="center">
                                        <button class="button" style="vertical-align:middle" onclick="limpiar()"><span>Limpiar </span></button>
                                    </td>
                                </tr>
                                               
                        </table>
                       
                    </div>
                </div>
            </div>
            <div class="acciones">
                <div id="botonBusqueda" style="width:100%">
                    <!--<ul id="imprimirArea" class="lista_botones"><li id="imprimir">Imprimir PDF</li></ul>-->
                    <!--<ul  id="imprimirexcel" class="lista_botones"><li id="excel">EXCEL</li></ul>-->
                    <ul id="imprimirexcel_contador" class="lista_botones"><li id="excel" style="width:200px; background-position:90px 4px;">EXCEL CONTADOR</li></ul>
                </div>
            </div>
           

<div id="cabeceraResultado" class="header"><?php echo $titulo_tabla; ?></div>
<table class="fuente8">
  <tr style="background-color: #e0e2e8;">
    <td><strong>Total Facturas: </strong></td>
    <td>S./<input type="text" id="total_fac" name="total_fac" style="border:0; background-color: #e0e2e8;" ></td><td>$<input type="text" id="total_fac_dolar" name="total_fac_dolar" style="border:0; background-color: #e0e2e8;" ></td>
    <td><strong>Total Boletas: </strong></td>
    <td>S./<input type="text" id="total_bol" name="total_bol" style="border:0; background-color: #e0e2e8; " > </td><td>$<input type="text" id="total_bol_dolar" name="total_bol_dolar" style="border:0; background-color: #e0e2e8;" ></td>

  </tr> 
  <tr style="background-color: #e0e2e8;">
    <td><strong>Total Notas de Crédito: </strong></td><td>S./<input type="text" id="total_nota" name="total_nota" style="border:0; background-color: #e0e2e8;" > </td><td>$<input type="text" id="total_nota_dolar" name="total_nota_dolar" style="border:0; background-color: #e0e2e8;" > </td>
    <td><strong>Total Comprobantes: </strong></td><td>S./<input type="text" id="total_comp" name="total_comp" style="border:0; background-color: #e0e2e8;" > </td><td>$<input type="text" id="total_comp_dolar" name="total_comp_dolar" style="border:0; background-color: #e0e2e8;" > </td>
  </tr>
  <tr style="background-color: #e1e1e1;" >
     <td><strong>Total Documentos: </strong></td><td>S./<input type="text" id="total" name="total" style="border:0; background-color: #e1e1e1;" > </td><td>$<input type="text" id="total_dolar" name="total_dolar" style="border:0; background-color: #e1e1e1;" > </td>
    <td><strong>Cantidad Documentos: </strong></td><td><input type="text" id="cantidad_fac" name="cantidad_fac" style="border:0; background-color: #e1e1e1;"></td><td></td>
  </tr>
    
</table>
<hr>

<div class="table-producto" style="display: none">
<table class="fuente8 display" id="table-producto">
  <thead>
    
    <tr>
    
    <th>FECHA</th>
    <th>TIPO</th>
    <th>SERIE</th>
    <th>NUEMERO</th>
    <th>NUM DOC</th>
    <th>RAZON SOCIAL</th>
    <th>MONEDA</th>
    <!--<th>SUBTOTAL</th>
    <th>IGV</th>-->
    <th>TOTAL</th>
    <th>FORMA PAGO</th>
    <th>ESTADO</th>
  </tr>
  </thead>
  <tbody class="viewData">
  </tbody>
  <tfoot>
  </tfoot>
</table>
</div>

            <input type="hidden" id="iniciopagina" name="iniciopagina">
            <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
        </div>
    </div>
 </div>    