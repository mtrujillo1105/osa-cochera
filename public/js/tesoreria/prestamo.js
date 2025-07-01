var base_url;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    $("#nuevoPrestamo").click(function(){
        url = base_url+"index.php/tesoreria/prestamo/nueva";
        location.href = url;
    });
    $("#grabarPrestamo").click(function(){
        $("#frmLinea").submit();
    });
    $("#limpiarPrestamo").click(function(){
        url = base_url+"index.php/tesoreria/prestamo/listar";
        $("#nombre_linea").val('');
        location.href=url;
    });
    $("#cancelarPrestamo").click(function(){
        url = base_url+"index.php/tesoreria/prestamo/listar";
        location.href = url;
    });
    $("#buscarLinea").click(function(){
        $("#form_busquedaLinea").submit();
    });
});
function ver_presupuesto_ver_pdf_conmenbrete(presupuesto,flag_img){    
    var url = base_url+"index.php/tesoreria/prestamo/presupuesto_ver_pdf_conmenbrete/"+presupuesto+"/"+flag_img;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
}

function editar_prestamo(linea){
    location.href = base_url+"index.php/tesoreria/prestamo/editar/"+linea;
}
function eliminar_presupuesto(linea){
    if(confirm('¿Está seguro que desea eliminar este prestamo?')){
        dataString        = "prestamo="+linea;
        url = base_url+"index.php/tesoreria/prestamo/eliminar";
        $.post(url,dataString,function(data){
            location.href = base_url+"index.php/tesoreria/prestamo/listar";
        });
    }
}
function ver_linea(linea){
    location.href = base_url+"index.php/maestros/linea/ver/"+linea;
}
function atras_linea(){
    location.href = base_url+"index.php/maestros/linea/listar";
}