var base_url;
var flagBS;
jQuery(document).ready(function(){
	base_url   = $("#base_url").val();
        flagBS     = $("#flagBS").val();
	$("#nuevoTipoProd").click(function(){
		url = base_url+"index.php/almacen/tipoproducto/nuevo_tipoproducto/"+flagBS;
		location.href = url;
	});	
 	$("#grabarTipoProd").click(function(){
		$("#frmTipoProd").submit();	
	}); 
	$("#limpiarTipoProd").click(function(){
            url = base_url+"index.php/almacen/tipoproducto/tipoproductos"+flagBS;
            location.href=url;
	});
	$("#cancelarTipoProd").click(function(){
            url = base_url+"index.php/almacen/tipoproducto/tipoproductos/"+flagBS;
            location.href = url;
	});
	$("#buscarTipoProd").click(function(){
            $("#form_busquedaTipoProd").submit();
	});

        $("#nuevoRegistroProv").click(function(){
                n = document.getElementById('tblPlantilla').rows.length;
                fila  = "<tr bgcolor='#ffffff'>";
                fila += "<td align='center'>"+n+"</td>";
                fila += "<td align='left'>";
                fila += "<input type='hidden' name='atributo["+n+"]' id='atributo["+n+"]'>";        
                fila += "<input type='text' name='nombre_atributo["+n+"]' id='nombre_atributo["+n+"]' readonly='readonly' class='cajaGrande'>";
                fila += "<a href='#' onclick='buscar_atributo("+n+");'>&nbsp;<img height='16' width='16' border='0' title='Buscar Atributo' src='"+base_url+"images/ver.png'></a>";
                fila += "</td>";
                fila += "<td align='left'>";
                fila += "<input type='text' name='tipo_atributo["+n+"]' id='tipo_atributo["+n+"]' readonly='readonly' class='cajaPequena'>";
                fila += "</td>";
                fila += "<td align='center'><a href='#' onclick='eliminar_plantilla("+n+");'><img src='"+base_url+"images/delete.gif' border='0'></a></td>";
                $("#tblPlantilla").append(fila);
            });
});
function editar_tipoprod(tipoprod){
	location.href = base_url+"index.php/almacen/tipoproducto/editar_tipoproducto/"+tipoprod;
}
function eliminar_tipoprod(tipoprod){
        if(confirm('Esta seguro desea eliminar a este tipo de producto?')){
		dataString        = "tipoprod="+tipoprod;
                url = base_url+"index.php/almacen/tipoproducto/eliminar_tipoproducto";
		$.post(url,dataString,function(data){
			location.href = base_url+"index.php/almacen/tipoproducto/tipoproductos/"+flagBS;		
		});			
	}
}
function ver_tipoprod(tipoprod){
  location.href = base_url+"index.php/almacen/tipoproducto/ver_tipoproducto/"+tipoprod;
}
function atras_tipoprod(){
	location.href = base_url+"index.php/almacen/tipoproducto/tipoproductos/"+flagBS;
}
function eliminar_plantilla(n){

    a    = "plantilla["+n+"]";
    b    = "nombre_atributo["+n+"]";
    
    plantilla = document.getElementById(a).value;
    nombre_atributo = document.getElementById(b).value;
    tipoprod  = document.getElementById("codigo").value;
    if(confirm('Esta seguro que desea eliminar el atributo '+nombre_atributo+'?')){
        dataString        = "plantilla="+plantilla;
        url = base_url+"index.php/almacen/tipoproducto/eliminar_plantilla";
        $.post(url,dataString,function(data){
            location.href = base_url+"index.php/almacen/tipoproducto/editar_tipoproducto/"+tipoprod;
        });
    }
}

