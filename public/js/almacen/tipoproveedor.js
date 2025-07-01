var base_url;
var url_image;
var img_url;
jQuery(document).ready(function(){
	base_url  = $('#base_url').val();
	url_image = $("#url_image").val();
	img_url   = base_url+"system/application/views/images/";
	$("#nuevaFamilia").click(function(){
		var codanterior = $('#codanterior').val();
		var n     = (document.getElementById('tablaFamilia').rows.length);
		if(n%2!=0) {estilo='itemParTabla'}else{estilo='itemImparTabla';};
		fila  = "<tr class='"+estilo+"'>";
		fila += "<td align='center'>"+n+"</td>";
		fila += "<td align='center'><input type='text' readonly='readonly' style='background-color: #E6E6E6' name='codigointerno["+n+"]' id='codigointerno["+n+"]' maxlength='20' class='cajaMinima'></td>";
                fila += "<td align='center'><input type='text' name='codigousuario["+n+"]' id='codigousuario["+n+"]' class='cajaPequena' maxlength='3'></td>";
		fila += "<td align='left'><input type='text' name='descripcion["+n+"]' id='descripcion["+n+"]' class='cajaGrande'></td>";
		fila += "<td align='center'>&nbsp;</td>";
		fila += "<td align='center'><a href='#' onclick='insertar_familia("+n+");'><image src='"+base_url+"images/save.gif' border='0'></a></td>";
		fila += "<td align='center'>&nbsp;</td>";
		fila += "</tr>"
		$("#tablaFamilia").append(fila);
		var a     = "codigointerno["+n+"]";
                var b     = "codigousuario["+n+"]";
		var url1  = base_url+"index.php/almacen/tipoproveedor/correlativo_familia";
		var dataString = "codanterior="+codanterior;
		$.post(url1,dataString,function(data){
			document.getElementById(a).value = data;
			document.getElementById(b).focus();
		});
	});
        $("#limpiarFamilia").click(function(){
            url = base_url+"index.php/almacen/tipoproveedor/familias";
            location.href=url;
	});
        $("#buscarFamilia").click(function(){
                    $("#form_busqueda").submit();
        });	
	$("#seleccionarFamilia").click(function(){
		n     	   = (document.getElementById('tablaFamilia2').rows.length);
		var codfamilia = $("#codfamilia").val()+".";
		for(i=0;i<n;i++){
			j     = i+1;
			a     = "nivel["+i+"]";
			nivel = document.getElementById(a).value;
			index = document.getElementById(a).selectedIndex;
			nombre= document.getElementById(a).options[index].text;
			if(nivel==''){
				alert('Debe seleccionar una opcion para el Nivel '+j);
				break;
			}
		}
		if(nivel!=''){
			parent.cargar_familia(nivel,nombre,codfamilia);
			parent.$.fancybox.close(); 
		}
	});
	$("#cancelarFamilia").click(function(){
		parent.$.fancybox.close(); 
	});
        //$("#frmResultado #codigousuario").focus();
        //$("#frmResultado input[name='codigousuario']").focus();
        
});
function abrir_familia(codigo){
	url           = base_url+"index.php/almacen/tipoproveedor/familias/"+codigo;
	location.href = url
}
function insertar_familia(n){
	var a           = "descripcion["+n+"]";
	var b           = "codigointerno["+n+"]";
        var c           = "codigousuario["+n+"]";
	var descripcion   = document.getElementById(a).value;
	var codigointerno = document.getElementById(b).value;
	codigointerno = codigointerno.replace(/^\s*|\s*$/g,"");
        var codigousuario = document.getElementById(c).value;
	var codanterior   = $('#codanterior').val();
	var codanterior2  = $('#codanterior2').val();	
	var url           = base_url+"index.php/almacen/tipoproveedor/insertar_familia";
	var dataString    = "descripcion="+descripcion+"&codanterior="+codanterior+"&codigointerno="+codigointerno+"&codigousuario="+codigousuario;
	if(codigointerno.length=='3' && descripcion!=''){
		$('#VentanaTransparente').css("display","block");	
		$.post(url,dataString,function(data){
			$('#VentanaTransparente').css("display","none");		
			location.href = base_url+"index.php/almacen/tipoproveedor/familias/"+codanterior;
		});
	}
	else{
		document.getElementById(a).focus();
		alert('Debe ingresar un codigo y/o una descripcion.');
	}
}
function editar_familia(n){
        var base_url  = $('#base_url').val();
	var a           = "familia["+n+"]";
	var familia     = document.getElementById(a).value;
	var codanterior = $('#codanterior').val();
	var dataString  = "familia="+familia+"&codanterior="+codanterior;
	url         = base_url+"index.php/almacen/tipoproveedor/editar_familia";
	$.post(url,dataString,function(data){
		$("#frmResultado").html(data);	
                $("#frmResultado input[name^='codigousuario']").focus();	
	});	
}
function modificar_familia(n){
        var base_url  = $('#base_url').val();
	var  a            = "descripcion["+n+"]";
	var b            = "familia["+n+"]";
        var c           = "codigousuario["+n+"]";
	var descripcion  = document.getElementById(a).value;
	var codigo       = document.getElementById(b).value;
        var codigousuario= document.getElementById(c).value;
	var codanterior  = $('#codanterior').val();
	var codanterior2 = $('#codanterior2').val();
	var url          = base_url+"index.php/almacen/tipoproveedor/modificar_familia";
	var dataString   = "descripcion="+descripcion+"&codanterior="+codanterior+"&codigo="+codigo+"&codigousuario="+codigousuario;
	if(descripcion!=''){
		$.post(url,dataString,function(data){
			//alert('redirec');
                        location.href = base_url+"index.php/almacen/tipoproveedor/familias/"+codanterior;
		});
	}
	else{
		alert('Debe ingresar un nombre para la familia');
	}
}
function eliminar_familia(n){
var base_url  = $('#base_url').val();
	var a            = "familia["+n+"]";
	var codigo       = document.getElementById(a).value;
	var codanterior  = $('#codanterior').val();	
	var url          = base_url+"index.php/almacen/tipoproveedor/eliminar_familia";
	var dataString   = "codigo="+codigo;
	if(confirm('Esta seguro que sea elimina esta familia?')){
		$.post(url,dataString,function(data){
			if(data){
				location.href = base_url+"index.php/almacen/tipoproveedor/familias/"+codanterior;
			}
			else{
				alert('No puede eliminar esta familia pues tiene sub-familias creadas');
			}
		});	
	}
}
function agregar_nivel(obj){
	nivel = obj.value;
	n = (document.getElementById('tablaFamilia').rows.length);		
	j = n+1;	
	fila  = "<tr>";	
	fila += "<td align='left'>Nivel"+j+"</td>";
	fila += "<td align='left'>";
	fila += "<select name='nivel"+n+"' id='nivel"+n+"' class='comboMedio'></select>";
	fila += "</td>";
	fila += "</tr>";
	$("#tablaFamilia").append(fila);
	
}