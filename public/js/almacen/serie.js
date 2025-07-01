var base_url;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    producto_id = $("#producto_id").val();
    almacen_id = $("#almacen_id").val();
    compania = $("#compania").val();
    $("#limpiarSerie").click(function(){
        url = base_url+"index.php/almacen/producto/ventana_producto_serie0/"+producto_id+"/"+almacen_id+"/"+compania;
        location.href=url;
    });
    $("#buscarSerie").click(function(){
        serie = $("#txtSerie").val();
        url = base_url+"index.php/almacen/producto/ventana_producto_serie0/"+producto_id+"/"+almacen_id+"/"+compania+"/"+serie;
        location.href=url;
    });
    $('#cerrarSerie').click(function(){
        parent.$.fancybox.close();
    });
    $('#txtSerie').keyup(function(e){
       var key=e.keyCode || e.which;
        if (key==13){
            $("#buscarSerie").click();
        } 
    });
});

function ver_movimientos(serie){
    base_url   = $("#base_url").val();
    url          = base_url+"index.php/almacen/producto/JSON_movimientos_serie/"+serie;
    
    
    $("#tblMovimientoSerie tr[class!='cabeceraTabla']").html('');    
    $('#tblMovimientoSerie').hide();
    $('img#loading,').show();
    $.getJSON(url,function(data){
              $('#tblMovimientoSerie').show();
              $('img#loading').hide();
              $.each(data, function(i,item){
                    if(i%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}
    
                    fila = '<tr class="'+clase+'">';
                    fila+= '<td width="4%"><div align="center">'+item.item+'</div></td>';
                    fila+= '<td width="10%"><div align="center">'+item.fecha+'</div></td>';
                    fila+= '<td width="10%"><div align="left">'+item.tipo+'</div></td>';
                    fila+= '<td width="17%"><div align="left">'+item.motivo+'</div></td>';
                    fila+= '<td><div align="left">'+item.nombre+'</div></td>';
                    fila+= '<td width="5%"><div align="center">'+item.numdoc+'</div></td>';
                    fila+= '</tr>';
                    $("#tblMovimientoSerie").append(fila);
              });
    });
}
