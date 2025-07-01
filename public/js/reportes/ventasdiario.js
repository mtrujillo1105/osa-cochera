$(document).ready(function () {
    
    search();
  
    $("#buscar").click(function () {
      search();
    });

    $("#limpiar").click(function () {
      search(false);
    });

    $("#imprimir").click(function () {
      url = base_url + "index.php/reportes/almacen/imprimirE/rpt_StockAlmacenes";
      $('#form_stockalmacenes').attr('action', url);
      $('#form_stockalmacenes').submit();
    });

    $("#imprimirArea").click(function(){
        tipo_docu    = $("#tipo_doc").val();
        fechaini    = $("#fechaini").val();
        fecha    = $("#fecha").val();

        var url= base_url + "index.php/reportes/ventas/ventas_pdf/"+tipo_docu+"/"+fechaini+"/"+fecha;
        window.open(url,'',"menubars=no,resizable=no;");
    });  
    
    $("#verReporte").click(function(){
        var fechaini = $('#fechaini').val();
        var fechafin = $('#fechafin').val();
        var tipo_doc = $("#tipo_doc").val();
        var nro_ruc  = $("#nro_ruc").val();
        var razon_social = $("#razon_social").val();

        if (fechaini > fechafin){
          alert("Rango de Fechas inválido");
        }
        else {
          var url     = base_url + "index.php/reportes/rptventas/ventasdiarioExcel/";
            $('#form_reporte').attr('action', url);
            $('#form_reporte').serialize();
            $('#form_reporte').submit();            
        }
    });    
  
    function search(search = true){
        if (search == true){
            fechaini = $("#fechaini").val();
            fechafin = $("#fechafin").val();
            tipo_doc = $("#tipo_doc").val();
            nro_ruc  = $("#nro_ruc").val();
            razon_social = $("#razon_social").val();
            numero_doc   = $("#numero_doc").val();
        }
        else{
            $("#fechaini").val("");
            $("#fechafin").val("");
            $("#tipo_doc").val("");
            $("#nro_ruc").val("");
            $("#razon_social").val("");
             $("#numero_doc").val("");
            fechaini = "";
            fechafin = "";
            tipo_doc = "";
            nro_ruc  = "";
            razon_social = "";
            numero_doc   = "";
        }        
        
        $('#datatable_ventasdiario').DataTable({
          responsive: false,
          filter: false,
          destroy: true,
          processing: true,
          serverSide: true,
          pageLength: 10,
          ajax: {
            url: base_url + "index.php/reportes/rptventas/datatable_ventasdiario/",
            type: "POST",
            data: {
                fechaini : fechaini,
                fechafin : fechafin,
                tipo_doc : tipo_doc,
                nro_ruc  : nro_ruc,
                razon_social : razon_social,
                numero_doc   : numero_doc
            },
            beforeSend: function () {
            },
            error: function () {
            }
          },
          language: spanish,
          order: [[0, "desc"]]
        });        
    }
  
});