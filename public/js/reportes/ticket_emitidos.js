$(document).ready(function () {
    
    search();
    
    $("#buscar").click(function () {
      search();
    });

    $("#limpiar").click(function () {
      search(false);
    });   
    
    $("#verReporte").click(function(){
        var fechaini = $('#search_fechai').val();
        var fechafin = $('#search_fechaf').val();
        var fpago    = $("#search_fpago").val();
        var caja     = $("#search_caja").val();

        if (fechaini > fechafin){
          alert("Rango de Fechas inválido");
        }
        else {
          var url     = base_url + "index.php/reportes/rptventas/ticket_emitidosExcel/";
            $('#form_reporte').attr('action', url);
            $('#form_reporte').serialize();
            $('#form_reporte').submit();            
        }
    });     

    function search(search = true){
        //Clean Sumary
        $("#ticket_anulados").html("0");
        $("#ticket_emitidos").html("0");
        $("#ticket_pendientes").html("0");
        $("#ticket_facturados").html("0"); 
        
        if (search == true){
            tarifa   = $("#tarifa").val();
            cajero   = $("#cajero").val();
            fechaing = $("#fechaing").val();
            fechasal = $("#fechasal").val();
            placa    = $("#placa").val();
        }
        else{
            $("#tarifa").val("");
            $("#cajero").val("");
            $("#fechaing").val("");
            $("#fechasal").val("");
            $("#placa").val("");
            tarifa   = "";
            cajero   = "";
            fechaing = "";
            fechasal = "";
            placa    = "";
        }        

        $('#datatable-tickets').DataTable({
            responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            "lengthChange": false,      
            ajax: {
              url: base_url + 'index.php/reportes/rptventas/datatable_tickets_emitidos/',
              type: "POST",
              data: {
                tarifa   : tarifa,
                cajero   : cajero,
                fechaing : fechaing,
                fechasal : fechasal,
                placa    : placa
              },
              beforeSend: function () {
              },
              error: function () {
              },
              complete: function (d) {
                  $.each(d.responseJSON.recordsAcum,function(key,value){
                      if(value.PARQC_FlagSituacion == 0) $("#ticket_anulados").html("<b>"+value.registros+"</b>");
                      if(value.PARQC_FlagSituacion == 1) $("#ticket_emitidos").html("<b>"+value.registros+"</b>");
                      if(value.PARQC_FlagSituacion == 2) $("#ticket_pendientes").html("<b>"+value.registros+"</b>");
                      if(value.PARQC_FlagSituacion == 3) $("#ticket_facturados").html("<b>"+value.registros+"</b>");
                  }); 
              }
            },
            pageLength: 50,
            language: "spanish",
            columnDefs: [
                {"className": "text-center","targets": 0},
                {"visible":false,"targets":12}
            ],
            order: [[12, "desc"]]
        });  
    }



});