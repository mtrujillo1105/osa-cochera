jQuery(document).ready(function(){
    
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
          var url     = base_url + "index.php/reportes/rpttesoreria/movimientos_cajaExcel/";
            $('#form_busqueda').attr('action', url);
            $('#form_busqueda').serialize();
            $('#form_busqueda').submit();            
        }
    });       
    
    function search(search = true){
        if (search == true){
            search_caja = $("#search_caja").val();
            search_descripcion = $("#search_descripcion").val();
            search_fpago = $("#search_fpago").val();
            search_fechai = $("#search_fechai").val();
            search_fechaf = $("#search_fechaf").val();
        }
        else{
            $("#search_caja").val("");
            $("#search_descripcion").val("");
            $("#search_fpago").val("");
            $("#search_fechai").val("");
            $("#search_fechaf").val("");
            search_caja = "";
            search_descripcion = "";
            search_fpago = "";
            search_fechai = "";
            search_fechaf = "";
        }    
        
        $('#datatable_movimiento').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                url : base_url + 'index.php/reportes/rpttesoreria/datatable_movimiento/',
                type: "POST",
                data: {
                    codigo: search_caja,
                    descripcion: search_descripcion,
                    fpago: search_fpago,
                    fechai: search_fechai,
                    fechaf: search_fechaf                   
                },
                beforeSend: function(){
                    $("#table-movimiento .loading-table").show();
                },
                error: function(){
                },
                complete: function(){
                    $("#table-movimiento .loading-table").hide();
                }
            },
            language: spanish,
            columnDefs: [{"className": "dt-center", "targets": 0}],
            order: [[ 1, "desc" ]]
        });
        
        
    }
    
});