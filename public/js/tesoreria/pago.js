jQuery(document).ready(function(){
        base_url   = $("#base_url").val();
        tipo_cuenta   = $("#tipo_cuenta").val();
        codigo   = $("#codigo").val();
    
        $("#atrasPago").click(function(){
            url = base_url+"index.php/tesoreria/cuentas/nuevo/"+tipo_cuenta;
            location.href=url;
        });
        
        $("#limpiarPago").click(function(){
            url = base_url+"index.php/tesoreria/pago/listar_ultimos/"+tipo_cuenta+"/"+codigo;
            location.href=url;
        });

        $("#buscarPago").click(function(){
            $("#form_busqueda").submit();
        });
        
  
})

function anular_pago(pago){
    if(confirm('¿Esta seguro de anular este pago?')){
        url = base_url+"index.php/tesoreria/pago/anular/"+tipo_cuenta+"/"+codigo+"/"+pago;
        location.href=url;
    }
}
