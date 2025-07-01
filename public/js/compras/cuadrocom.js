var base_url
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    
    $("#imgGuardarCuadro").click(function(){
		dataString = $('#frmCuadro').serialize();
		$("#container").show();
		$("#frmCuadro").submit();
    });
    $("#buscarCuadro").click(function(){
		$("#form_busqueda").submit();
    });	
    $("#nuevoCuadro").click(function(){
		url = base_url+"index.php/compras/cuadrocom/nuevo_cuadro";
		$("#zonaContenido").load(url);
    });
    $("#limpiarCuadro").click(function(){
        url = base_url+"index.php/compras/cuadrocom/pedidos";
        location.href=url;
    });
    $("#imgCancelarCuadro").click(function(){
	base_url = $("#base_url").val();
        location.href = base_url+"index.php/compras/cuadrocom/pedidos";
    });
    
	container = $('div.container');
 	$("#frmCuadro").validate({
		event    : "blur",
		debug    : true,
		errorContainer      : "container",
		errorLabelContainer : $(".container"),
		wrapper             : 'li',
		submitHandler       : function(form){
				var valor = $('#pedidos').val();
				if(valor == 0){
					alert('Elija un pedido');
					return false;
				}
				
				dataString  = $('#frmCuadro').serialize();                               
				modo        = $("#modo").val();
				$('#VentanaTransparente').css("display","block");
				if(modo=='insertar'){
					url = base_url+"index.php/compras/cuadrocom/insertar_cuadro";
					$.post(url,dataString,function(data){
					$("#VentanaTransparente").css("display","none");
						alert('Se ha ingresado un cuadro.');
						location.href = base_url+"index.php/compras/cuadrocom/cuadros";
					});
				}
				else if(modo=='modificar'){
					url = base_url+"index.php/compras/cuadrocom/modificar_cuadro";
					$.post(url,dataString,function(data){
						$("#VentanaTransparente").css("display","none");
						alert('Su registro ha sido modificado.');
						location.href = base_url+"index.php/compras/cuadrocom/cuadros";
					});
				}
		}
	});
   
	container = $('div.container');   
});

function load_cuadro(){
	var id = $("#pedidos").val();
	if(id!=0){
		var url = base_url+"index.php/compras/cuadrocom/cargar_cuadro/"+id;
		$("#datosCuadro").load(url);
	}
}

function editar_cuadro(pedido){
        var url = base_url+"index.php/compras/cuadrocom/editar_cuadro/"+pedido;
	$("#zonaContenido").load(url);
}
function eliminar_cuadro(pedido){
	if(confirm('Esta seguro desea eliminar este pedido?')){
		dataString = "pedido="+pedido;
		url = base_url+"index.php/compras/cuadrocom/eliminar_cuadro";
		$.post(url,dataString,function(data){
			url = base_url+"index.php/compras/cuadrocom/pedidos";
			location.href = url;
		});
	}
}


function ver_cuadro(pedido){
	url = base_url+"index.php/compras/pedido/ver_cuadro/"+pedido;
	//$("#zonaContenido").load(url);
}
function atras_persona(){
	location.href = base_url+"index.php/compras/pedido/pedidos";
}

