jQuery(document).ready(function () {
    //var	base_url = $("#base_url").val();
    //var tipo_caja = $("#tipo_caja").val();
    
    $('#table-tipocaja').DataTable({ responsive: true,
         filter: false,
         destroy: true,
         processing: true,
         serverSide: true,
         autoWidth: false,
         ajax:{
                 url : base_url + 'index.php/tesoreria/tipocaja/datatable_tipocaja/',
                 type: "POST",
                 data: { dataString: "" },
                 beforeSend: function(){
                     $("#table-tipocaja .loading-table").show();
                 },
                 error: function(){
                 },
                 complete: function(){
                     $("#table-tipocaja .loading-table").hide();
                 }
         },
         language: spanish,
         columnDefs: [{"className": "dt-center", "targets": 0}],
         order: [[ 1, "asc" ]]
     });
    
    
    
    $("#nuevoTipocaja").click(function(){
        //alert(base_url+" "+tipo_caja);
	url = base_url + "index.php/tesoreria/tipocaja/tipocaja_nuevo" ;
        location.href = url;
    });
	
    $("#cancelartipoCaja").click(function(){
        url = base_url + "index.php/tesoreria/tipocaja/tipocajas/" ;
        location.href = url;
    });
    $("#grabartipoCaja").click(function () {
   
        //if (confirm('¿Está seguro de grabar?')) {
        $('img#loading').css('visibility', 'visible');
        if($("#txtcodigo").val()==""){
           // alert("ingresando a guardar");
    url = base_url + "index.php/tesoreria/tipocaja/tipocaja_grabar";
      
        }else{
    //alert("ingresando a Actualizar");
    url = base_url + "index.php/tesoreria/tipocaja/tipocaja_modificar";
        }
       

      dataString = $('#frmtipocaja').serialize();
      if(validateFormulario()){ 
    $.post(url, dataString, function (data) {
                $('img#loading').css('visibility', 'hidden');
                switch (data.result) {
                    case 'ok':
                       
                  // alert("pasando al");
              location.href = base_url+"index.php/tesoreria/tipocaja/tipocajas";
                      
                
                        break;
                    case 'error':
                        $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                        $('#' + data.campo).css('background-color', '#FFC1C1').focus();
                        break;
                }
            }, 'json');}
    

        //}
    });	
    
    $("#txtTipo").click(function(){
        $("#txtCodigoT").val($("#txtTipo").val());
    });

    $("#buscarC").click(function(){
        search();
    });

    $("#limpiarC").click(function(){
        search(false);
    });

    $('#form_busqueda').keypress(function(e){
        if ( e.which == 13 ){
            return false;
        } 
    });

    $('#search_descripcion').keyup(function(e){
        if ( e.which == 13 ){
            if( $(this).val() != '' )
                search();
        }
    });

});

function search( search = true){
    if (search == true){
        search_descripcion = $("#search_descripcion").val();
    }
    else{
        $("#search_descripcion").val("");
        search_descripcion = "";
    }
    $('#table-tipocaja').DataTable({ responsive: true,
        filter: false,
        destroy: true,
        processing: true,
        serverSide: true,
        ajax:{
                url : base_url + 'index.php/tesoreria/tipocaja/datatable_tipocaja/',
                type: "POST",
                data: {
                        descripcion: search_descripcion
                },
                beforeSend: function(){
                    $("#table-tipocaja .loading-table").show();
                },
                error: function(){
                },
                complete: function(){
                    $("#table-tipocaja .loading-table").hide();
                }
        },
        language: spanish,
        columnDefs: [{"className": "dt-center", "targets": 0}],
        order: [[ 1, "asc" ]]
    });
 }

function editar(id){
    var url = base_url + "index.php/tesoreria/tipocaja/getTipoCaja";
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data:{
            tipocaja: id
        },
        beforeSend: function(){
            clean();
        },
        success: function(data){
            if (data.match == true) {
                info = data.info;
                $("#tipocaja").val(info.tipocaja);
                $("#codigo_tipocaja").val(info.codigo);
                $("#descripcion_tipocaja").val(info.descripcion);
                $("#add_caja").modal("toggle");
            }
            else{
                Swal.fire({
                    icon: "info",
                    title: "Información no disponible.",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 4000
                });
            }
        },
        complete: function(){
        }
    });
}

function registrar_caja(){
    Swal.fire({
                icon: "question",
                title: "¿Esta seguro de guardar el registro?",
                html: "<b class='color-red'></b>",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (result.value){
                    var caja = $("#tipocaja").val();
                    var descripcion = $("#descripcion_tipocaja").val();
                    validacion = true;
                    if (descripcion == ""){
                        Swal.fire({
                                icon: "error",
                                title: "Verifique los datos ingresados.",
                                html: "<b class='color-red'>Debe ingresar una descripcion.</b>",
                                showConfirmButton: true,
                                timer: 4000
                        });
                        $("#descripcion_tipocaja").focus();
                        validacion = false;
                        return null;
                    }
                    if (validacion == true){
                        var url = base_url + "index.php/tesoreria/tipocaja/guardar_registro";
                        var info = $("#formTipoCaja").serialize();
                        $.ajax({
                            type: 'POST',
                            url: url,
                            dataType: 'json',
                            data: info,
                            success: function(data){
                                if (data.result == "success") {
                                    if (caja == "")
                                        titulo = "¡Registro exitoso!";
                                    else
                                        titulo = "¡Actualización exitosa!";
                                    Swal.fire({
                                        icon: "success",
                                        title: titulo,
                                        showConfirmButton: true,
                                        timer: 2000
                                    });

                                    clean();
                                }
                                else{
                                    Swal.fire({
                                        icon: "error",
                                        title: "Sin cambios.",
                                        html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                }
                            },
                            complete: function(){
                                search(false);
                                $("#descripcion_tipocaja").focus();
                            }
                        });
                    }
                }
            });
  }

  function deshabilitar(caja){
      Swal.fire({
                icon: "info",
                title: "Debe confirmar esta acción.",
                html: "<b class='color-red'>Esta acción no se puede deshacer</b>",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar"
              }).then(result => {
                  if (result.value){
                      var url = base_url + "index.php/tesoreria/tipocaja/deshabilitar_tipocaja";
                      $.ajax({
                          type: 'POST',
                          url: url,
                          dataType: 'json',
                          data: {
                              tipocaja: caja
                          },
                          success: function(data){
                              if (data.result == "success") {
                                  titulo = "¡Registro eliminado!";
                                  Swal.fire({
                                      icon: "success",
                                      title: titulo,
                                      showConfirmButton: true,
                                      timer: 2000
                                  });
                              }
                              else{
                                  Swal.fire({
                                      icon: "error",
                                      title: "Sin cambios.",
                                      html: "<b class='color-red'>Algo ha ocurrido, verifique he intentelo nuevamente.</b>",
                                      showConfirmButton: true,
                                      timer: 4000
                                  });
                              }
                          },
                          complete: function(){
                              search(false);
                          }
                      });
                  }
              });
  }

  function clean(){
      $("#formTipoCaja")[0].reset();
      $("#tipocaja").val("");
  }

function tipocaja_editar(codigo){
   var url=$("#base_url").val();
    location.href = url+"index.php/tesoreria/tipocaja/tipocaja_editar/"+codigo;  
}
function fireMyFunction(){
    $("#buscarTipocaja").click();
}
jQuery(document).ready(function(){
     // $('#txtDescrip').validCampoFranz('0123456789 abcdefghijklmnñopqrstuvwxyzáéiou');
     // $('#txtAbreviatura').validCampoFranz('0123456789 abcdefghijklmnñopqrstuvwxyzáéiou');

$("#buscarTipocaja").click(function(){
        $("#form_busqueda").submit();
    });
 $("#limpiarTipocaja").click(function(){
    var base_url=$("#base_url").val();
        url = base_url+"index.php/tesoreria/tipocaja/tipocajas/0/1"; 
        location.href = url;
    });         
$("#limpiartipoCaja").click(function(){
    //alert("hola");
    document.getElementById("frmtipocaja").reset();
});
});


$(document).ready(function(){
  
  $("#open").click(function(){
        $('#cajaFlotante').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        return false;
    });
    
    $('#close').click(function(){
        $('#cajaFlotante').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });

   

});
function getOptenerModal(codigo,y){
    var base_u=$("#base_url").val();
    var url_data="index.php/tesoreria/tipocaja/JSON_listarTipoCaja/";
    var url= base_u+url_data+codigo;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {

  
    $("#tipCa_Descripcion").html(item.tipCa_Descripcion);
    $("#tipCa_Abreviaturas").html(item.tipCa_Abreviaturas);
    $("#tipCa_Tipo").html(item.tipCa_Tipo);
    $("#UsuarioRegistro").html(item.UsuarioRegistro);
    $("#UsuarioModificado").html(item.UsuarioModificado);
    $("#tipCa_fechaModificacion").html(item.tipCa_fechaModificacion);
    $("#tipCa_FechaRegsitro").html(item.tipCa_FechaRegsitro);
    });
});
    $('#cajaFlotante').fadeIn('slow');
    $('.popup-overlay').fadeIn('slow');
    $('.popup-overlay').height($(window).height());
    return false; 
}
function tipocaja_Eliminar(codigo){
    var base_u=$("#base_url").val();
    var url_data="index.php/tesoreria/tipocaja/JSON_ActualizarTipoCaja/";
    var url= base_u+url_data+codigo;
    if(confirm("Esta seguro que desea eliminar?")){
     $.ajax({url: url,type: "POST", success: function(result){
   url2 = base_u + "index.php/tesoreria/tipocaja/tipocajas/" ;
        location.href = url2;
        }
    });   
 }else{

 }
    
}

$(document).dblclick(function(){
   $('#cajaFlotante').fadeOut('slow');
$('.popup-overlay').fadeOut('slow');
    return false;
});

$(document).keydown(function(tecla){ 

   if(tecla.keyCode == 27||tecla.keyCode == 10){
   $('#cajaFlotante').fadeOut('slow');
   $('.popup-overlay').fadeOut('slow');
    return false;
   }
 
  });


function soloLetras_andNumero(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "áéíóúabcdefghijklmnñopqrstuvwxyz.1234567890 ";
    especiales = [8, 37, 39, 46];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i] ) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}
function validateFormulario(){
     if($("#txtDescrip").val() == "" || /^\s*$/.test($("#txtDescrip").val())) {
        $("#txtDescrip").css('background-color', '#FFC1C1').focus();
        return false;
    }
    // Campos de texto
     if($("#txtAbreviatura").val() == "" || /^\s*$/.test($("#txtTipocaja").val())){
       $('#txtAbreviatura').css('background-color', '#FFC1C1').focus();
        return false;
    }//|| /^\s*$/.test(la caja de texto) cuando hay muchos espacios en blanco
    if($("#txtTipocaja").val() == "::Seleccione::" ){
      $('#txtTipocaja').css('background-color', '#FFC1C1').focus();
      return false;
    }
   
   
    return true; // Si todo está correcto
}
$(document).ready(function(){

   $("#txtDescrip").keypress(function(){
  $("#txtDescrip").css({"background-color": "#fff"});
  });
  $("#txtAbreviatura").click(function(){
   $("#txtAbreviatura").css({"background-color": "#fff"});
  });
  $("#txtTipocaja").click(function(){
  $("#txtTipocaja").css({"background-color": "#fff"});
  });
});