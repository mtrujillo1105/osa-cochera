var base_url;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
  
  $("#NuevoGarantia").click(function(){
        url = base_url+"index.php/almacen/garantia/nuevo";
        location.href = url;
    });
    
    
   $("#listaentregaCliente").click(function(){
        url = base_url+"index.php/almacen/entregacliente/listar";
        location.href = url;
    });
    
    
   $("#listarecepcionProveedor").click(function(){
        url = base_url+"index.php/almacen/recepcionproveedor/listar";
        location.href = url;
    }); 
    
    
    $("#listaenvioProveedor").click(function(){
        url = base_url+"index.php/almacen/envioproveedor/listar";
        location.href = url;
    }); 
    
    
    
    
    
    $("#imgLimpiarGarantiaNuevo").click(function(){
        $("#frmGarantia").each(function(){
            this.reset();
        });
    });
    
    
     $("#envioProveedor").click(function(){
       
var checkboxes = frmgarantia.checkGarantia; //Array que contiene los checkbox 
var cont = 0; //Variable que lleva la cuenta de los checkbox pulsados 

for (var x=0; x < checkboxes.length; x++) { 
if (checkboxes[x].checked) 
{ 
cont = cont + 1; 

//alert ("El valor del checkbox pulsados es " + checkboxes[x].value); 
} 
} 

if(cont==0){
alert ("Debe seleccionar una garantia"); 
}

else {
    
   valida2(); 
    
    
}
    });
     $("#recepcionProveedor").click(function(){
      
      
var checkboxes = frmgarantia.checkGarantia; //Array que contiene los checkbox 
var cont = 0; //Variable que lleva la cuenta de los checkbox pulsados 

for (var x=0; x < checkboxes.length; x++) { 
if (checkboxes[x].checked) 
{ 
cont = cont + 1; 

//alert ("El valor del checkbox pulsados es " + checkboxes[x].value); 
} 
} 

if(cont==0){
alert ("Debe seleccionar una garantia"); 
}

else {
    
   valida1(); 
    
    
}
       
    });
    
    
$("#entregaCliente").click(function(){
       


var checkboxes = frmgarantia.checkGarantia; //Array que contiene los checkbox 
var cont = 0; //Variable que lleva la cuenta de los checkbox pulsados 

for (var x=0; x < checkboxes.length; x++) { 
if (checkboxes[x].checked) 
{ 
cont = cont + 1; 

//alert ("El valor del checkbox pulsados es " + checkboxes[x].value); 
} 
} 

if(cont==0){
alert ("Debe seleccionar una garantia"); 
}

else {
    
   valida(); 
    
    
}
   
        
    });
    
    
    $("#imgGuardarGarantia").click(function(){
        $("#frmGarantia").submit();
    });
    
    $("#limpiarGarantia").click(function(){
        url = base_url+"index.php/almacen/garantia/listar";
        $("#descripcion_garantia").val('');
        location.href=url;
    });
    $("#imgCancelarGarantia").click(function(){
        url = base_url+"index.php/almacen/garantia/listar";
        location.href = url;
    });
    $("#buscarGarantia").click(function(){
        $("#form_busquedaGarantia").submit();
    });
});

function contar() { 

var checkboxes = frmgarantia.checkGarantia; //Array que contiene los checkbox 
var cont = 0; //Variable que lleva la cuenta de los checkbox pulsados 

for (var x=0; x < checkboxes.length; x++) { 
if (checkboxes[x].checked) 
{ 
cont = cont + 1; 
return true;
//alert ("El valor del checkbox pulsados es " + checkboxes[x].value); 
} 
else {
    
    return false;
}
} 

//alert ("El número de checkbox pulsados es " + cont); 

} 


function editar_marca(marca){
	location.href = base_url+"index.php/maestros/marca/editar/"+marca;
}

function valida(){
         document.forms["frmgarantia"].action=base_url+"index.php/almacen/entregacliente/nuevo";
             document.forms["frmgarantia"].submit();


 }
 function valida1(){
         document.forms["frmgarantia"].action=base_url+"index.php/almacen/recepcionproveedor/nuevo";
          document.forms["frmgarantia"].submit();


 }
 function valida2(){

       document.forms["frmgarantia"].action=base_url+"index.php/almacen/envioproveedor/nuevo";

       document.forms["frmgarantia"].submit();


 }
function valida_producto(boton){
   
   if($("#checkGarantia").is(':checked')){
                
    }
      else{
      alert('Debe seleccionar una garantia.');
      $("#limpiarGarantia").focus();
       return false;
   }
      
    
    valor = boton.value;
    if(valor == "Entrega Cliente") {
         document.forms["frmgarantia"].action=base_url+"index.php/almacen/entregacliente/nuevo";
    } 
    if(valor == "Recepcion Del Proveedor") {
         document.forms["frmgarantia"].action=base_url+"index.php/almacen/recepcionproveedor/nuevo";
    } 
    if(valor == "Envio A Proveedor") {
         document.forms["frmgarantia"].action=base_url+"index.php/almacen/envioproveedor/nuevo";
    } 
    
    
    

     document.forms["frmgarantia"].submit();
//   
//   
      
}



function eliminar_garantia(garantia){
    if(confirm('¿Está seguro que desea eliminar este registro de garantia?')){
        dataString        = "garantia="+garantia;
        url = base_url+"index.php/almacen/garantia/eliminar";
        $.post(url,dataString,function(data){
            location.href = base_url+"index.php/almacen/garantia/listar";
        });
    }
}
function ver_garantia(garantia){
    location.href = base_url+"index.php/almacen/garantia/ver/"+garantia;
}
function atras_garantia(){
    location.href = base_url+"index.php/almacen/garantia/listar";
}