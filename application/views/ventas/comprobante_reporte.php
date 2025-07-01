<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/ventas/comprobante.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
     <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
 <style>    
    
  /***ESTILOS PARA EL AUTOCOMPLETE**/ 
  .cajaPadding {

        padding: 2px 10px;

    }
    
    .ui-autocomplete {
        padding: 0;
        margin: 0;
        width: 500px;
        list-style: none;
    }

    .ui-autocomplete a {
        color: #000;
        font-family: Arial;
        font-size: 8pt;
        display: block;
        padding: 4px 10px;
    }

    .ui-autocomplete a:hover {
        color: #000;
        font-weight: bold;;

    }

    .ui-state-hover {
        background: black !important;
        color: #FFF !important;
        border: 0px !important;
    }

    </style>
<script type="text/javascript">
$(document).ready(function(){
    $("a#linkVerProveedor, a#linkVerProducto").fancybox({
            'width'      : 700,
            'height'         : 450,
            'autoScale'      : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'       : 'iframe'
    });

    $("#productoDescripcion").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "<?=base_url();?>index.php/maestros/temporaldetalle/autocomplete_producto/B//",
                type: "POST",
                data: {
                    term: $("#productoDescripcion").val(), TipCli: "", marca: "", modelo: "" 
                },
                dataType: "json",
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#reporteProducto").val(ui.item.codigo);
        },
        minLength: 2
    });

    /*$("#fech1").change(function(){
      var fecha1 = $("#fech1").val();
      var fecha2 = $("#fech2").val();

      if ( fecha1 != "" && fecha2 != "" ){
        $(".table-producto").show();
        fechaI = fecha1.split("/");
        fecha1 = fechaI[2] + "-" + fechaI[1] + "-" + fechaI[0];

        fechaF = fecha2.split("/");
        fecha2 = fechaF[2] + "-" + fechaF[1] + "-" + fechaF[0];

        $("#table-producto").DataTable().destroy();
        $('#table-producto').DataTable({ responsive: true,
              processing: true,
              serverSide: true,
              bPaginate: true,
              ajax: {
                      url : "<?=base_url();?>index.php/ventas/comprobante/productos_vendidos/" + fecha1 + "/" + fecha2,
                      type: "post",
                      error: function(){
                      }
                    },
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
              },
              columns:[
                          { data: "codigo" },
                          { data: "descripcion" },
                          { data: "marca" },
                          { data: "ndocumentos" },
                          { data: "nvendidos" },
                          { data: "total" }
                      ]
          });
      }
    });

    $("#fech2").change(function(){
      var fecha1 = $("#fech1").val();
      var fecha2 = $("#fech2").val();

      if ( fecha1 != "" && fecha2 != "" ){
        $(".table-producto").show();
        fechaI = fecha1.split("/");
        fecha1 = fechaI[2] + "-" + fechaI[1] + "-" + fechaI[0];

        fechaF = fecha2.split("/");
        fecha2 = fechaF[2] + "-" + fechaF[1] + "-" + fechaF[0];

        $("#table-producto").DataTable().destroy();
        $('#table-producto').DataTable({ responsive: true,
              processing: true,
              serverSide: true,
              bPaginate: true,
              ajax: {
                      url : "<?=base_url();?>index.php/ventas/comprobante/productos_vendidos/" + fecha1 + "/" + fecha2,
                      type: "post",
                      error: function(){
                      }
                    },
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
              },
              columns:[
                          { data: "codigo" },
                          { data: "descripcion" },
                          { data: "marca" },
                          { data: "ndocumentos" },
                          { data: "nvendidos" },
                          { data: "total" }
                      ]
          });
      }
    });*/

    $("#fech1").change(function(){
      if ( $("#fech1").val() != "" && $("#fecha2").val() != "" ){
        var fecha1 = $('#fech1').val();
        var fecha2 = $('#fech2').val();

        if (fecha1 > fecha2){
          $(".table-producto").hide();
        }
        else{

          if ( fecha1 != "" && fecha2 != "" ){
            fechaI = fecha1.split("/");
            fecha1 = fechaI[2] + "-" + fechaI[1] + "-" + fechaI[0];

            fechaF = fecha2.split("/");
            fecha2 = fechaF[2] + "-" + fechaF[1] + "-" + fechaF[0];

            $("#table-producto").DataTable().destroy();

            $(".viewData").html('');

            $.ajax({
                    url : "<?=base_url();?>index.php/ventas/comprobante/productos_vendidos_table/" + fecha1 + "/" + fecha2,
                    type: "POST",
                    data: {
                        term: ""
                    },
                    dataType: "json",
                    success: function (data) {
                      if (data != null && data != undefined){ 
                        $.each(data, function(i, item) {
                              table = '';
                              table += '<tr>';
                                table += '<td>' + item.codigo + '</td>';
                                table += '<td>' + item.descripcion + '</td>';
                                table += '<td>' + item.marca + '</td>';
                                table += '<td>' + item.ndocumentos + '</td>';
                                table += '<td>' + item.nvendidos + '</td>';
                                table += '<td>' + item.total + '</td>';
                              table += '</tr>';

                              $(".viewData").append(table);
                        });

                        $('#table-producto').DataTable({ responsive: true,
                              language: {
                                lengthMenu: "_MENU_",
                                search: "_INPUT_",
                                searchPlaceholder: "Buscar",
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
                      else
                        $(".table-producto").hide();
                    }
            });

          }
        }
      }
    });

    $("#fech2").change(function(){
      if ( $("#fech1").val() != "" && $("#fecha2").val() != "" ){
        var fecha1 = $('#fech1').val();
        var fecha2 = $('#fech2').val();

        if (fecha1 > fecha2){
          $(".table-producto").hide();
        }
        else{

          if ( fecha1 != "" && fecha2 != "" ){
            fechaI = fecha1.split("/");
            fecha1 = fechaI[2] + "-" + fechaI[1] + "-" + fechaI[0];

            fechaF = fecha2.split("/");
            fecha2 = fechaF[2] + "-" + fechaF[1] + "-" + fechaF[0];

            $("#table-producto").DataTable().destroy();

            $(".viewData").html('');

            $.ajax({
                    url : "<?=base_url();?>index.php/ventas/comprobante/productos_vendidos_table/" + fecha1 + "/" + fecha2,
                    type: "POST",
                    data: {
                        term: ""
                    },
                    dataType: "json",
                    success: function (data) {
                      if (data != null && data != undefined){ 
                        $.each(data, function(i, item) {
                              table = '';
                              table += '<tr>';
                                table += '<td>' + item.codigo + '</td>';
                                table += '<td>' + item.descripcion + '</td>';
                                table += '<td>' + item.marca + '</td>';
                                table += '<td>' + item.ndocumentos + '</td>';
                                table += '<td>' + item.nvendidos + '</td>';
                                table += '<td>' + item.total + '</td>';
                              table += '</tr>';

                              $(".viewData").append(table);
                        });

                        $('#table-producto').DataTable({ responsive: true,
                              language: {
                                lengthMenu: "_MENU_",
                                search: "_INPUT_",
                                searchPlaceholder: "Buscar",
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
                      else
                        $(".table-producto").hide();
                    }
            });

          }
        }
      }
    });
});


var base_url   = $("#base_url").val(); 

function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
    $("#cliente").val(codigo);
    $("#ruc_proveedor").val(ruc);
    $("#nombre_proveedor").val(razon_social);

    $("#cliente_f").val(codigo);
    $("#ruc_proveedor_f").val(ruc);
    $("#nombre_proveedor_f").val(razon_social);
}
function seleccionar_producto(codigo,interno,familia,stock,costo){
    $("#producto").val(codigo);
    $("#codproducto").val(interno);
    $("#cantidad").focus();
    obtener_nombre_producto(codigo);
}
function obtener_nombre_producto(producto){ 
    base_url   = $("#base_url").val();
    url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto; 
    $.getJSON(url,function(data){
          $.each(data, function(i,item){
                nombre_producto = item.PROD_Nombre;
          });
           $("#nombre_producto").val(nombre_producto);
    });
}

</script>
<script>
   $.datepicker.regional['es'] = {
   closeText: 'Cerrar',
   prevText: '< Ant',
   nextText: 'Sig >',
   currentText: 'Hoy',
   monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
   monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
   dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
   dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sdo'],
   dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sb'],
   weekHeader: 'Sm',
   dateFormat: 'dd/mm/yy',
   firstDay: 1,
   isRTL: false,
   showMonthAfterYear: false,
   yearSuffix: ''
   };
   $.datepicker.setDefaults($.datepicker.regional['es']);
</script>

 <script>
  $( function() {
    $( "#fech1" ).datepicker();
  } );
  
 </script>
 <script>
  $( function() {
    $( "#fech2" ).datepicker();
  } );
</script>

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

<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda" >
                <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                  <tr>
                    <td width="200" align="left" valign="top">
                            <h3 style="margin:5px 0px;">Reportes disponibles</h3>
                            <ul id="menureporte">
                                <!--<li id="repo1">Listado de O. de Venta</li>
                                <li id="repo2">Estad&iacute;sticas de O. de Venta</li>
                                <li id="repo3">Reportes de Venta</li>
                                <li id="repo7">Listado de Facturas</li>-->
                                <li id="repo6">Ventas por producto</li>
                                <li id="repo4">Estad&iacute;sticas de Venta Anual</li>
                                <li id="repo5">Estad&iacute;sticas de Venta Mensual</li>
                            <!--<a href="javascript:;" onclick="estadisticas_compras_ventas('V')">Estad&iacute;sticas de Venta</a>-->
                            </ul>
                    </td>
                    <td>&nbsp;</td>
                    <td valign="top">
                        <div class="lienzoreporte" id="divRepo1">
                            <h3>Listado de O. de Venta</h3>   
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                                  <tr>
                                    <td align='left' width="20%">Fecha inicial</td>
                                    <td align='left' width="15%">
                                        <?php echo $fechai?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechai",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                    <td align='left' width="10%">Fecha final</td>
                                    <td align='left' width="45%">
                                        <?php echo $fechaf?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechaf",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Cliente </td>
                                    <td colspan="3">
                                            <input type="hidden" name="cliente" id="cliente" size="5" class="cajaPequena" value="">
                                            <input type="text" name="ruc_proveedor" class="cajaPequena" id="ruc_proveedor" size="10" maxlength="11" onBlur="obtener_proveedor();" value="" onKeyPress="return numbersonly(this,event,'.');" />
                                            &nbsp;<input type="text" name="nombre_proveedor" class="cajaGrande cajaSoloLectura" id="nombre_proveedor" size="15" maxlength="15" readonly="readonly" value="" />
                                            <a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Art&iacute;culo</td>
                                    <td colspan="3">
                                            <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10" maxlength="11" />
                                            <input name="codproducto" type="text" class="cajaPequena" id="codproducto" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');" />&nbsp;
                                            <input NAME="nombre_producto" type="text" class="cajaGrande cajaSoloLectura" id="nombre_producto" size="15" maxlength="15" readonly="readonly" />
                                            <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                  </tr>
                                  <tr>
                                     <td>Aprobaci&oacute;n</td>
                                     <td colspan="3">
                                        <select name="aprobado" id="aprobado" class="comboMedio">
                                           <option value="" selected="selected">::Seleccione::</option>
                                           <option value="0">Pendiente</option>
                                           <option value="1">Aprobado</option>
                                           <option value="2">Desaprobado</option>
                                        </select>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td>Verificaci&oacute;n de ingreso</td>
                                     <td colspan="3">
                                        <select name="ingreso" id="ingreso" class="comboMedio">
                                            <option value="" selected="selected">::Seleccione::</option>
                                            <option value="0">Pendiente</option>
                                            <option value="1">Si</option>
                                        </select>
                                     </td>
                                  </tr>
                            </table>
                
                          <a href="javascript:;" onclick="ver_reporte_pdf()" id="verReporte">
                            <img  style="margin:15px 0px;" src="<?=base_url();?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton">
                          </a>
                        </div>
                            <div class="lienzoreporte" id="divRepo2"></div>
                        <div class="lienzoreporte" id="divRepo6">
                          <table>
                                  <tr>
                                    <td colspan="2"> Reportes de Venta por producto </td>
                                  </tr>
                                  <tr>
                                    <td>Producto<input type="hidden" name="reporteProducto" id="reporteProducto"></td>
                                    <td><input type="text"  name="productoDescripcion" id="productoDescripcion" class="cajaGrande"></td>
                                  </tr>
                                  <tr style="display: none">
                                      <td>Seleccione A&ntilde;o</td>
                                      <td><?=$combo;?></td>
                                      <td>Seleccione Mes</td>
                                      <td>
                                        <select id="mesventa" name="mesventa">
                                          <option value="">Seleccione...</option>
                                          <option value="1">ENERO</option>
                                          <option value="2">FEBRERO</option>
                                          <option value="3">MARZO</option>
                                          <option value="4">ABRIL</option>
                                          <option value="5">MAYO</option>
                                          <option value="6">JUNIO</option>
                                          <option value="7">JULIO</option>
                                          <option value="8">AGOSTRO</option>
                                          <option value="9">SETIEMBRE</option>
                                          <option value="10">OCTUBRE</option>
                                          <option value="11">NOVIEMBRE</option>
                                          <option value="12">DICIEMBRE</option>
                                        </select>
                                      </td>
                                  </tr>
                                  <tr><td colspan="6"><hr></td></tr>
                                  <tr>
                                    <td align='left' >Fecha inicial</td>
                                    <td align='left' ><input type="text" id="fech1" name="fech1" class="cajaMedia" autocomplete="off"></td>
                                    <td align='left' >Fecha final</td>
                                    <td align='left' ><input type="text" id="fech2" name="fech2" class="cajaMedia" autocomplete="off"></td>
                                    <td>Seleccione tipo de documento</td>
                                    <td>
                                      <select id="tipodocumento" name="tipodocumento">
                                        <option value="">Seleccione...</option>
                                        <option value="F">FACTURA</option>
                                        <option value="B">BOLETA</option>
                                      </select>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td colspan="2">
                                      <a href="javascript:;" onclick="ver_reporte_productos()">
                                        <img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton">
                                      </a>
                                    </td>
                                  </tr>
                          </table>
                          <div id="result_data"></div>
                        </div>
                        <div class="lienzoreporte" id="divRepo3">
                              <table>
                                <tr>
                                  <td colspan="2">Reportes de Venta</td>
                                </tr>
                                <tr>
                                  <td>Seleccione A&ntilde;o</td>
                                  <td> <?php echo $combo; ?> </td>
                                  <td>Seleccione Mes</td>
                                  <td>
                                    <select id="mesventa" name="mesventa">
                                      <option value="">Seleccione...</option>
                                      <option value="1">ENERO</option>
                                      <option value="2">FEBRERO</option>
                                      <option value="3">MARZO</option>
                                      <option value="4">ABRIL</option>
                                      <option value="5">MAYO</option>
                                      <option value="6">JUNIO</option>
                                      <option value="7">JULIO</option>
                                      <option value="8">AGOSTRO</option>
                                      <option value="9">SETIEMBRE</option>
                                      <option value="10">OCTUBRE</option>
                                      <option value="11">NOVIEMBRE</option>
                                      <option value="12">DICIEMBRE</option>
                                    </select>
                                  </td>
                                  <td>Seleccione tipo de documento</td>
                                    <td>
                                      <select id="tipodocumento" name="tipodocumento">
                                        <option value="">Seleccione...</option>
                                        <option value="F">FACTURA</option>
                                        <option value="B">BOLETA</option>
                                      </select>
                                    </td>
                                </tr>
                                <tr><td colspan="6"><hr></td></tr>
                                <tr>
                                  <td align='left' >Fecha inicial</td>
                                  <td align='left' ><input type="text" id="fech1" name="fech1"></td>
                                  <td align='left' >Fecha final</td>
                                  <td align='left' ><input type="text" id="fech2" name="fech2"></td>
                                </tr>
                                <!--  <tr><td colspan="6"><hr></td></tr>
                                <tr>
                                  <td>Departamento&nbsp;</td>
                                  <td colspan="4">
                                      <div id="divUbigeo">
                                        <select id="cboDepartamento" name="cboDepartamento" class="comboMedio" onchange="cargar_provincia(this);">
                                          <?php echo $cbo_dpto; ?>
                                          </select>&nbsp; &nbsp;
                                          Provincia&nbsp;&nbsp; &nbsp;
                                          <select id="cboProvincia" namecomboMedio" onchange="cargar_distrito(this);">
                                            <?php echo $cbo_prov; ?>
                                          </select>&nbsp; &nbsp;
                                          Distrito&nbsp;&nbsp;  &nbsp;
                                          <select id="cboDistrito" name="cboDistrito" class="comboMedio">
                                            <?php echo $cbo_dist; ?>
                                          </select>
                                       </div>
                                   </td>
                               </tr>
                               <tr><td colspan="6"><hr></td></tr>
                                 <tr>
                                 <!--<td> Producto<input type="hidden" name="reporteProducto" id="reporteProducto" ></td>
                                 <td><input type="text"  name="productoDescripcion"  onfinishinput="busqueda_producto(this);" id="productoDescripcion" class="cajaGrande cajaPadding cajaBusquedaGrande"></td>  
                                 </tr>-->
                                <tr>
                                  <td colspan="2">
                                    <a href="javascript:;" onclick="ver_reporte_pdf_ventas()" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                                  </td>
                                </tr>
                              </table>
                        </div>
                            <div class="lienzoreporte" id="divRepo4">
                              <table>
                                <tr>
                                  <td colspan="2">Estad&iacute;sticas de Venta Anual</td>
                                </tr>
                                <tr>
                                  <td>Seleccione A&ntilde;o</td>
                                  <td><?=$combo2;?></td>
                                </tr>
                                <tr>
                                  <td colspan="2">
                                    <a href="javascript:;" onclick="estadisticas_compras_ventas('V')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                                  </td>
                                </tr>
                              </table>
                        </div>
                            <div class="lienzoreporte" id="divRepo5">
                              <table>
                                <tr>
                                  <td colspan="2"> Estad&iacute;sticas de Venta Mensual </td>
                                </tr>
                                <tr>
                                  <td>Seleccione A&ntilde;o</td>
                                  <td><?php echo $combo3; ?></td>
                                  <td>
                                    <select id="mesVenta3" name="mesVenta3">
                                      <option value="0">Seleccione...</option>
                                      <option value="1">ENERO</option>
                                      <option value="2">FEBRERO</option>
                                      <option value="3">MARZO</option>
                                      <option value="4">ABRIL</option>
                                      <option value="5">MAYO</option>
                                      <option value="6">JUNIO</option>
                                      <option value="7">JULIO</option>
                                      <option value="8">AGOSTRO</option>
                                      <option value="9">SETIEMBRE</option>
                                      <option value="10">OCTUBRE</option>
                                      <option value="11">NOVIEMBRE</option>
                                      <option value="12">DICIEMBRE</option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="1">
                                    <a href="javascript:;" onclick="estadisticas_compras_ventas_mensual('V')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                                  </td>
                                  <td colspan="3">
                                    <a href="javascript:;" onclick="estadisticas_compras_ventas_mensual_excel('V')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/xls.png?=<?=IMG;?>" width="22" height="22" class="imgBoton" ></a>
                                  </td>
                                </tr>
                              </table>
                        </div>
                        <div class="lienzoreporte" id="divRepo7">
                          <h3>Listado de Facturas</h3>   
                          <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td align='left' width="20%">Fecha inicial</td>
                                    <td align='left' width="15%">
                                        <?php echo $f_fechai?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "f_fechai",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                    <td align='left' width="10%">Fecha final</td>
                                    <td align='left' width="45%">
                                        <?php echo $f_fechaf?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "f_fechaf",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cliente </td>
                                    <td colspan="3">
                                      <input type="hidden" name="cliente_f" id="cliente_f" size="5" class="cajaPequena" value="">
                                      <input type="text" name="ruc_proveedor_f" class="cajaPequena" id="ruc_proveedor_f" size="10" maxlength="11" onBlur="obtener_proveedor();" value="" onKeyPress="return numbersonly(this,event,'.');" />
                                            &nbsp;<input type="text" name="nombre_proveedor_f" class="cajaGrande cajaSoloLectura" id="nombre_proveedor_f" size="15" maxlength="15" readonly="readonly" value="" />
                                      <a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Art&iacute;culo</td>
                                    <td colspan="3">
                                      <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10" maxlength="11" />
                                      <input name="codproducto" type="text" class="cajaPequena" id="codproducto" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');" />&nbsp;
                                      <input NAME="nombre_producto" type="text" class="cajaGrande cajaSoloLectura" id="nombre_producto" size="15" maxlength="15" readonly="readonly" />
                                      <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                 </tr>
                                 <tr>
                                     <td>Aprobaci&oacute;n</td>
                                     <td colspan="3">
                                       <select name="aprobado_f" id="aprobado_f" class="comboMedio">
                                         <option value="" selected="selected">::Seleccione::</option>
                                         <option value="0">Pendiente</option>
                                         <option value="1">Aprobado</option>
                                         <option value="2">Desaprobado</option>
                                       </select>
                                     </td>
                                 </tr>
                                 <tr>
                                    <td>Estado Pago</td>
                                    <td colspan="3">
                                      <select name="ingreso_f" id="ingreso_f" class="comboMedio">
                                          <option value="" selected="selected">::Seleccione::</option>
                                          <option value="0">Pendiente</option>
                                          <option value="1">Cancelado</option>
                                      </select>
                                    </td>
                                 </tr>
                          </table>

                          <a href="javascript:;" onclick="ver_reporte_pdf_factura()" id="verReporte"><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        </div>
                    </td>
                  </tr>
                </table>
            </div>
            <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>

        <div class="table-producto" style="display: none">
          <table class="fuente8 display" id="table-producto">
            <thead>
              <th>CODIGO</th>
              <th>PRODUCTO</th>
              <th>MARCA</th>
              <th>DOCUMENTOS</th>
              <th>CANTIDAD</th>
              <th>TOTAL</th>
            </thead>
            <tbody class="viewData">
              
            </tbody>
            <tfoot>
              
            </tfoot>
          </table>
        </div>
    </div>
</div>