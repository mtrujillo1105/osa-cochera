var base_url
personaCodigo=$("#personaCodigo").val();
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    
    $("#imgGuardarPersona").click(function(){
		dataString = $('#frmPersona').serialize();
		$("#container").show();
		$("#frmPersona").submit();
    });
    $("#btnCancelarCuentaE").click(function(){
limpiarCaja();
    });
    $("#idCuentas").click(function(){
    	var persona=$("#personaCodigo").val();
         $("#txtModificar").val("");
		url = base_url+"index.php/maestros/cuenta_empresa/nuevo_cuentaempresa/"+persona;
    	//url = base_url+"index.php/empresa/empresa/nuevo_empresa";
		$("#datosGenerales").load(url+" #datosCuentas");
    });
    $("#idGeneral").click(function(){
        var persona=$("#personaCodigo").val();
        var persona=$("#personaCodigo").val();
    	var url = base_url+"index.php/maestros/persona/editar_persona/"+persona;
		$("#zonaContenido").load(url);
        $("#txtModificar").val(persona);
    });
    $("#hol").click(function(){

    });
    $("#buscarPersona").click(function(){
		$("#form_busqueda").submit();
    });	
    $("#nuevoPersona").click(function(){
		url = base_url+"index.php/maestros/persona/nuevo_persona";
		$("#zonaContenido").load(url);
    });
    $("#limpiarPersona").click(function(){
        url = base_url+"index.php/maestros/persona/personas";
        location.href=url;
    });
    $("#imgCancelarPersona").click(function(){
	base_url = $("#base_url").val();
        location.href = base_url+"index.php/maestros/persona/personas";
    });
    
	container = $('div.container');
 	$("#frmPersona").validate({
		event    : "blur",
		rules    : {
					'nombres'         : "required",
					'paterno'         : "required",
					'email'           : {required:false,email:true},
					'tipo_documento'  : "required",
					'cboSexo'         : "required",
					'cboNacionalidad' : "required"
 				   },
		debug    : true,
		errorContainer      : "container",
		errorLabelContainer : $(".container"),
		wrapper             : 'li',
		submitHandler       : function(form){
				dataString  = $('#frmPersona').serialize();                               
				modo        = $("#modo").val();
				$('#VentanaTransparente').css("display","block");
				if(modo=='insertar'){
					url = base_url+"index.php/maestros/persona/insertar_persona";
					$.post(url,dataString,function(data){
					$("#VentanaTransparente").css("display","none");
						alert('Se ha ingresado un persona.');
						location.href = base_url+"index.php/maestros/persona/personas";
					});
				}
				else if(modo=='modificar'){
                     if($("#txtModificar").val()==""){
                        //alert("no pudes")
                        $("#VentanaTransparente").css("display","none");
                        $("#idGeneral").css('background-color', '#FFC1C1').focus();
                     }else{
                    $('tipo_documento').val('2');
                    $('cboNacionalidad').val('193');
                    url = base_url+"index.php/maestros/persona/modificar_persona";
                    $.post(url,dataString,function(data){
                        $("#VentanaTransparente").css("display","none");
                        alert('Su registro ha sido modificado.');
                        location.href = base_url+"index.php/maestros/persona/personas";
                    });
                     }
                    

				}
		}
	});
   
	container = $('div.container');   
});
function editar_persona(persona){
        var url = base_url+"index.php/maestros/persona/editar_persona/"+persona;
	$("#zonaContenido").load(url);
}
function eliminar_persona(persona){
	if(confirm('Esta seguro desea eliminar este persona?')){
		dataString = "persona="+persona;
		url = base_url+"index.php/maestros/persona/eliminar_persona";
		$.post(url,dataString,function(data){
			url = base_url+"index.php/maestros/persona/personas";
			location.href = url;
		});
	}
}


function cargar_provincia(obj){
    departamento = obj.value;
    provincia    = "01";
    if(departamento!='00'){
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
    }
}
function cargar_distrito(obj){
    departamento = $("#cboDepartamento").val();
    provincia    = obj.value;
    if(departamento!='00' && provincia!='00'){
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
    }
}


function abrir_formulario_ubigeo(){
	ubigeo = $("#cboNacimiento").val();
        if(ubigeo=='')
            ubigeo='000000';
	url = base_url+"index.php/maestros/ubigeo/formulario_ubigeo/"+ubigeo;
	window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=610,height=110');
}

function ver_persona(persona){
	url = base_url+"index.php/maestros/persona/ver_persona/"+persona;
	$("#zonaContenido").load(url);
}
function atras_persona(){
	location.href = base_url+"index.php/maestros/persona/personas";
}
function insertar_cuentaEmpresa(){
	var codigo=$("#txtCodCuenEmpre").val();
	var empresa_persona=$("#empresa_persona").val();
	var txtBanco=$("#txtBanco").val();
	var txtCuenta=$("#txtCuenta").val();
	var txtTitular=$("#txtTitular").val();
	var txtTipoCuenta=$("#txtTipoCuenta").val();
	var txtMoneda=$("#txtMoneda").val();
	var TIP_Codigo=$("#TIP_Codigo").val();
	var txtOficina =$("#txtOficina").val();
	var txtInterban=$("#txtInterban").val();
	var txtSectoriza=$("#txtSectoriza").val();
	var identif=$("#indentifPersona").val();
	//var personaCodigo=$("#personaCodigo").val();
	//REGISTRAR UN NUEVO CUENTA EMPRESA
	
	if($("#txtCodCuenEmpre").val()==""){

    var dataString="personaCodigo="+personaCodigo+"&txtBanco="+txtBanco+"&txtCuenta="+txtCuenta+"&txtTitular="+txtTitular+"&txtTipoCuenta="+txtTipoCuenta+"&txtMoneda="+txtMoneda+"&TIP_Codigo="+TIP_Codigo+"&txtOficina="+txtOficina+"&txtInterban="+txtInterban+"&txtSectoriza="+txtSectoriza;
	url = base_url+"index.php/maestros/cuenta_empresa/insert_cuantasEmpresa";
    if(validateFormulario()){
    	
	$.post(url,dataString,function(data){
	 	$('#contenidoCuentaTable').load(base_url+"index.php/maestros/cuenta_empresa/TABLA_cuentaEmpresa/"+personaCodigo);
	});	
	limpiarCaja();
	}

}else{
	//ACTUALIZAR DATA DE CUENTA EMPRESA
    var dataString="txtCodCuenEmpre="+codigo+"&personaCodigo="+personaCodigo+ "&txtBanco="+txtBanco+"&txtCuenta="+txtCuenta+"&txtTitular="+txtTitular+"&txtTipoCuenta="+txtTipoCuenta+"&txtMoneda="+txtMoneda+"&TIP_Codigo="+TIP_Codigo+"&txtOficina="+txtOficina+"&txtInterban="+txtInterban+"&txtSectoriza="+txtSectoriza;
	url = base_url+"index.php/maestros/cuenta_empresa/update_cuantasEmpresa";
	  if(validateFormulario()){
	$.post(url,dataString,function(data){
	$('#contenidoCuentaTable').load(base_url+"index.php/maestros/cuenta_empresa/TABLA_cuentaEmpresa/"+personaCodigo);
	limpiarCaja();
});	
}
}
}
function actualizar_cuentaEmpresa(codigo){
	//var url_data="index.php/empresa/empresa/TABLA_cuentaEmpresa/";
    //var url=base_url+url_data+codigo+"/E";
    $('#contenedorCuenta').load(base_url+"index.php/maestros/cuenta_empresa/TABLA_cuentaEmpresa/"+codigo+"/E");
}
function eliminar_cuantaEmpresa(codigo){

   //var personaCodigo=$("#personaCodigo").val();
    var url_data="index.php/maestros/cuenta_empresa/JSON_EliminarCuentaEmpresa/";
    var url= base_url+url_data+codigo;
    if(confirm("Esta Seguro Eliminar?")){
     $.ajax({url: url,type: "POST", success: function(result){
    	 	$('#contenidoCuentaTable').load(base_url+"index.php/maestros/cuenta_empresa/TABLA_cuentaEmpresa/"+personaCodigo);
    
        }
    });   
 }else{

 }
    

}
function ventanaChekera(codigo){
var url_data="index.php/maestros/cuenta_empresa/JSON_ListarCuentaEmpresa/";
    var url= base_url+url_data+codigo;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
        	$("#txtCodCuentaEmpre").val(item.CUENT_Codigo);
        	$("#txtnumeroEmpr").val(item.CUENT_NumeroEmpresa);
        	$("#txtMonedaChekera").val(item.MONED_Descripcion);
});
});
    var url_data1="index.php/maestros/cuenta_empresa/listarChikera/";
    var url1= base_url+url_data1+codigo;
    $.getJSON(url1, function (data) {
        $.each(data, function (i, item) {
        	if(item.SERIP_Codigo!=null){
        	var  	 fila = '<tr> <td>'+(i+1)+'</td>';
             fila+='<td>'+item.CHEK_FechaRegistro+'</td>';
             fila+='<td>'+item.CUENT_NumeroEmpresa+'</td>';
             fila+='<td>'+item.SERIP_Codigo+'</td>';
             fila+='<td>'+item.CHEK_Numero+'</td>';
             fila+='<td><a href="#" onclick="eliminarChikera('+item.CHEK_Codigo+')" ><img src='+base_url+'images/delete.gif ></a></td>';
        $("#listarChekera").append(fila);  
                    fila='</tr>';	
                }else{
                	//var fila="<tr><td align=center colspan=6 >" ;
  					//	fila+="<div>NO EXISTEN REGISTROS</div>";
                	//fila+='</td>';
          $("#listarChekera").append("<div>NO EXISTEN REGISTROS</div>");  
                	//fila='</tr>';
    }            
});
});	
   $('#popup').fadeIn('slow');
    $('.popup-overlay').fadeIn('slow');
    $('.popup-overlay').height($(window).height());
    return false;
}
function insertChekera(){
    var txtCodCuentaEmpre=$("#txtCodCuentaEmpre").val();
    //var txtMonedaChekera=$("#txtMonedaChekera").val();
    var txtSerieChekera=$("#txtSerieChekera").val();
    var txtNumeroChek=$("#txtNumeroChek").val();
    var empresa_persona=$("#empresa_persona").val();
    
     var dataString="txtSerieChekera="+txtSerieChekera+"&txtCodCuentaEmpre="+txtCodCuentaEmpre+"&txtTitular="+txtTitular+"&empresa_persona="+empresa_persona+"&txtNumeroChek="+txtNumeroChek;
    url = base_url+"index.php/maestros/cuenta_empresa/insertChekera";
    //if(validateFormulario()){
   if(validarChikera()){
    $.post(url,dataString,function(data){
     $('#contenedorTableChekera').load(base_url+"index.php/maestros/cuenta_empresa/TABLE_listarChekera/"+txtCodCuentaEmpre);
    $("#txtNumeroChek").val("");
    $("#txtSerieChekera").val("");
    }); 
    }
    
}
function eliminarChikera(codigo){
    var txtCodCuentaEmpre=$("#txtCodCuentaEmpre").val();
    var url_data="index.php/maestros/cuenta_empresa/eliminarChikera/";
    var url= base_url+url_data+codigo;
    if(confirm("Esta Seguro de Eliminar?")){
     $.ajax({   url: url,
                type: "POST", 
                success: function(result){
            $('#contenedorTableChekera').load(base_url+"index.php/maestros/cuenta_empresa/TABLE_listarChekera/"+txtCodCuentaEmpre);
    
        }
    });   
 }else{

 }
}



function limpiarCaja(){
	$("#txtMoneda").val("::SELECCIONE::");
	$("#txtTipoCuenta").val("::SELECCIONE::");
	$("#txtBanco").val("::SELECCIONE::");
	$("#txtTitular").val("");
    $("#txtCuenta").val("");
    $("#txtOficina").val("");
    $("#txtSectoriza").val("");
     $("#txtInterban").val("");
}
function validarChikera(){
if($("#txtSerieChekera").val() == "" || /^\s*$/.test($("#txtSerieChekera").val())){
      $('#txtSerieChekera').css('background-color', '#FFC1C1').focus();
      return false;
    }
 if($("#txtNumeroChek").val() == "" || /^\s*$/.test($("#txtNumeroChek").val())){
      $('#txtNumeroChek').css('background-color', '#FFC1C1').focus();
      return false;
    } 
 return true; 
}
function validateFormulario(){
    // Campos de texto
 if($("#txtBanco").val() == "S"){
       $('#txtBanco').css('background-color', '#FFC1C1').focus();
        return false;
    }//|| /^\s*$/.test(la caja de texto) cuando hay muchos espacios en blanco
    if($("#txtCuenta").val() == "" || /^\s*$/.test($("#txtCuenta").val())){
      $('#txtCuenta').css('background-color', '#FFC1C1').focus();
      return false;
    }
    if($("#txtTitular").val() == "" || /^\s*$/.test($("#txtTitular").val())) {
        $("#txtTitular").css('background-color', '#FFC1C1').focus();
        return false;
    }
if($("#txtOficina").val() == "" || /^\s*$/.test($("#txtOficina").val())) {
        $("#txtOficina").css('background-color', '#FFC1C1').focus();
        return false;
    }
if($("#txtSectoriza").val() == "" || /^\s*$/.test($("#txtSectoriza").val())) {
        $("#txtSectoriza").css('background-color', '#FFC1C1').focus();
        return false;
    }
if($("#txtInterban").val() == "" || /^\s*$/.test($("#txtInterban").val())) {
        $("#txtInterban").css('background-color', '#FFC1C1').focus();
        return false;
    }

    if($("#txtTipoCuenta").val() == "S"){
         $("#txtTipoCuenta").css('background-color', '#FFC1C1').focus();
        return false;
    }
   if($("#txtMoneda").val() == "S"){
       $("#txtMoneda").css('background-color', '#FFC1C1').focus();
        return false;
    }

  /*  // Checkbox
    if(!$("#txtMoneda").is(":checked")){
        alert("Debe confirmar que es mayor de 18 años.");
        return false;
    }
*/
    return true; // Si todo está correcto
}
function soloLetras_andNumero(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz.1234567890-_+";
    especiales = [8, 37, 39, 46];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}
$(document).ready(function(){
  $('#open').click(function(){
        $('#popup').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        return false;
    });
    $('#close').click(function(){
    	$("#listarChekera").html('');//limpiar la tabla
		$('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    
    });
function  onkeypress_cuenta(){
    var cod=$("#txtCuenta").val();
    var url=base_url+"index.php/maestros/cuenta_empresa/getBuscaCuenta/"+cod;
        // $("div").html(" ");//limpia el campo
        $.getJSON(url, function(result){
            $.each(result, function(i, item){
                $("#txtCodCuenEmpre").val(item.CUENT_Codigo);
                $("#txtOficina").val(item.CUENT_Oficina);
                $("#txtSectoriza").val(item.CUENT_Sectoriza);
                $("#txtInterban").val(item.CUENT_Interbancaria);
                $("#txtTitular").val(item.CUENT_Titular);
                document.getElementById("txtBanco").value=item.BANP_Codigo;
                $("#txtTipoCuenta").val(item.CUENT_TipoCuenta);
                $("#txtMoneda").val(item.MONED_Codigo);
               });
        });
}
