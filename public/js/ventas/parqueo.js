$(document).ready(function () {
    //Clean Sumary
    $("#ticket_anulados").html("0");
    $("#ticket_emitidos").html("0");
    $("#ticket_pendientes").html("0");
    $("#ticket_facturados").html("0"); 
    
    $('#datatable-parqueo').DataTable({
      responsive: true,
      filter: false,
      destroy: true,
      processing: true,
      serverSide: true,
      autoWidth: false,      
      ajax: {
        url: base_url + 'index.php/ventas/parqueo/datatable_parqueo/',
        type: "POST",
        data: { dataString: "" },
        beforeSend: function () {
        },
        error: function () {
        },
        complete: function (d) {
            $.each(d.responseJSON.recordsAcum,function(key,value){
                if(value.PARQC_FlagSituacion == 0) $("#ticket_anulados").html("<b>"+value.registros+"</b>");
                if(value.PARQC_FlagSituacion == 1) $("#ticket_emitidos").html("<b>"+value.registros+"</b>");
                if(value.PARQC_FlagSituacion == 2) $("#ticket_pendientes").html("<b>"+value.registros+"</b>");
                if(value.PARQC_FlagSituacion == 3) $("#ticket_facturados").html("<b>"+value.registros+"</b>");
            });
        }
      },   
      "pageLength": 25,      
      "language": "spanish",
      "columnDefs": [
          {"className":"text-center","targets": 0 },
          {"targets":[9],"visible":false}
      ],
      "order": [[9, "desc"]]
    });


  $("#buscar").click(function () {
    search();
  });

  $("#nuevo").click(function () {
    //Limpiamos formulario
    clean();
    
    //Mostramos datos para nuevo
    $(".datos_salida").collapse('show');
    
    //Muestro modal
    $('#add_parqueo').modal('show');
  });  

  $("#limpiar").click(function () {
      clean();
      search(false);
  });

  $('#form_busqueda').keypress(function (e) {
    if (e.which == 13) {
      return false;
    }
  });  

  $("#actualizar_parqueo").click(function () {
      
    var url = base_url + "index.php/ventas/parqueo/guardar_registro";
    var parqueo = $("#parqueo").val();
    var placa = $("#parqueo_placa_edit").val();
    var tarifa = $("#parqueo_tarifa_edit").val();
    var fingreso = $("#parqueo_fingreso_edit").val();
    var hingreso = $("#parqueo_hingreso_edit").val();       
    var fsalida  = $("#parqueo_fsalida_edit").val();
    var hsalida  = $("#parqueo_hsalida_edit").val();   
    var tiempo   = $("#parqueo_tiempo_edit").val();  
    var monto    = $("#parqueo_monto_edit").val();  
    validacion   = true;

    //Actualizamos el tickec
    if(parqueo != ""){
        
        Swal.fire({
          icon: "question",
          title: "¿Esta seguro de actualizar el registro?",
          html: "<b class='color-red'></b>",
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: "Aceptar",
          cancelButtonText: "Cancelar"
        }).then(result => {
          if (result.value) {

            if (placa == "") {
              Swal.fire({
                icon: "error",
                title: "Verifique los datos ingresados.",
                html: "<b class='color-red'>Debe ingresar un placa.</b>",
                showConfirmButton: true,
                timer: 4000
              });
              $("#parqueo_placa_edit").focus();
              validacion = false;
            }
            if (tarifa == "") {
              Swal.fire({
                icon: "error",
                title: "Verifique los datos ingresados.",
                html: "<b class='color-red'>Debe ingresar una tarifa.</b>",
                showConfirmButton: true,
                timer: 4000
              });
              $("#parqueo_tarifa_edit").focus();
              validacion = false;
            }
            if (validacion == true) {
              var datos = $("#formParqueo_edit").serialize();
              $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: datos,
                success: function (data) {
                  if (data.result == "success") {
                    titulo = "¡Se actualizó el ticket!";
                    Swal.fire({
                      icon: "success",
                      title: titulo,
                      showConfirmButton: true,
                      timer: 2000
                    });

                    $("#parqueo_placa_edit").focus();    

                    //Habilitamos controles placa, fingreso, hingreso
                    $('#parqueo_placa_edit').removeAttr("readonly");     
                    $('#parqueo_fingreso_edit').removeAttr("readonly");
                    $('#parqueo_hingreso_edit').removeAttr("readonly");                

                    //Limpiamos el formulario
                    clean();

                    //Cerramos el modal
                    $('#add_parqueo').modal('hide');
                  }
                  else {
                    Swal.fire({
                      icon: "error",
                      title: "Sin cambios.",
                      html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
                      showConfirmButton: true,
                      timer: 4000
                    });
                  }
                },
                complete: function () {
                    search(false);
                    $("#search_placa").focus();
                }
              });
            }
            
          }
        });        
        
    }
    //Creamos un nuevo ticket
    else{

        if (placa.trim() == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar un placa.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_placa_edit").focus();
          validacion = false;
        }
        else if (tarifa == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una tarifa.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_tarifa_edit").focus();
          validacion = false;
        } 
        else if (fingreso == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una fecha de ingreso.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_fingreso_edit").focus();
          validacion = false;
        }  
        else if (hingreso == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una hora de ingreso.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_hingreso_edit").focus();
          validacion = false;
        }  
        else if (fsalida == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una fecha de salida.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_fsalida_edit").focus();
          validacion = false;
        }  
        else if (hsalida == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una hora de salida.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_hsalida_edit").focus();
          validacion = false;
        }          
        else if (tiempo == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar un tiempo.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_tiempo_edit").focus();
          validacion = false;
        }         
        else if (monto == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar un monto.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#parqueo_monto_edit").focus();
          validacion = false;
        }           
        
        if (validacion == true) {
          var datos = $("#formParqueo_edit").serialize();
          $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: datos,
            success: function (data) {
              if (data.result == "success") {
                titulo = "¡Se creó un ticket!";
                Swal.fire({
                  icon: "success",
                  title: titulo,
                  showConfirmButton: true,
                  timer: 2000
                });

                $("#parqueo_placa_edit").focus();    

                //Habilitamos controles placa, fingreso, hingreso
                $('#parqueo_placa_edit').removeAttr("readonly");     
                $('#parqueo_fingreso_edit').removeAttr("readonly");
                $('#parqueo_hingreso_edit').removeAttr("readonly");                

                //Limpiamos el formulario
                clean();

                //Cerramos el modal
                $('#add_parqueo').modal('hide');
              }
              else {
                Swal.fire({
                  icon: "error",
                  title: "Sin cambios.",
                  html: "<b class='color-red'>La información no fue registrada/actualizada, intentelo nuevamente.</b>",
                  showConfirmButton: true,
                  timer: 4000
                });
              }
            },
            complete: function () {
                search(false);
                $("#search_placa").focus();
            }
          });
        }
        
    }
    

    
    
  });

  $("#salir_actualizar").click(function(){
    $('#add_parqueo').modal('hide');
    
    //Habilitamos controles placa, fingreso, hingreso
    $('#parqueo_placa_edit').removeAttr("readonly");
    $('#parqueo_fingreso_edit').removeAttr("readonly");
    $('#parqueo_hingreso_edit').removeAttr("readonly");
    $(".datos_salida").collapse('hide');
    
    //Limpiamos el formulario
    clean();
  });

  $("#salir").click(function(){
    $('#agregar_parqueo').modal('hide');
    clean();
  });  

  /*$("#imprimir_parqueo").click(function(){
    url = base_url + "index.php/ventas/parqueo/imprimir_parqueo/"
    $('#formParqueo').attr('action', url);
    $('#formParqueo').serialize();
    $('#formParqueo').attr('target', '_blank');
    $('#formParqueo').submit();
    $('#agregar_parqueo').modal('hide');
  });*/

});

function search(search = true) {
  //Clean summmary
  $("#ticket_anulados").html("0");
  $("#ticket_emitidos").html("0");
  $("#ticket_pendientes").html("0");
  $("#ticket_facturados").html("0");     
    
  if (search == true) {
    placa  = $("#search_placa").val();
    tarifa = $("#search_tarifa").val();
    serie  = $("#search_serie").val();
    numero = $("#search_numero").val();
    ticket = $("#search_numero_ticket").val();
  }
  else {
    placa  = "";
    tarifa = "";
    serie  = "";
    numero = "";
    ticket = "";
  }    
  $('#datatable-parqueo').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    autoWidth: false,
    ajax: {
      url: base_url + 'index.php/ventas/parqueo/datatable_parqueo/',
      type: "POST",
      data: {
          placa: placa, tarifa : tarifa, serie : serie, numero: numero, ticket: ticket,
      },
      beforeSend: function () {
      },
      error: function () {
      },
      complete: function (d) {
        $.each(d.responseJSON.recordsAcum,function(key,value){
            if(value.PARQC_FlagSituacion == 0) $("#ticket_anulados").html("<b>"+value.registros+"</b>");
            if(value.PARQC_FlagSituacion == 1) $("#ticket_emitidos").html("<b>"+value.registros+"</b>");
            if(value.PARQC_FlagSituacion == 2) $("#ticket_pendientes").html("<b>"+value.registros+"</b>");
            if(value.PARQC_FlagSituacion == 3) $("#ticket_facturados").html("<b>"+value.registros+"</b>");
        });          
      }
    },
    "pageLength" : 25,      
    "language"   : "spanish",
      "columnDefs": [
          {"className":"text-center","targets": 0 },
          {"targets":[9],"visible":false}
      ],
      "order": [[9, "desc"]]
  });
}

function editar(id) {
  //Placa, fingreso, hingreso atributo solo lectura
  $("#parqueo_placa_edit").attr("readonly",true);
  $("#parqueo_fingreso_edit").attr("readonly",true);
  $("#parqueo_hingreso_edit").attr("readonly",true);
  $(".datos_salida").collapse('hide');
  
  var url = base_url + "index.php/ventas/parqueo/getParqueo";
  
  $.ajax({
    type: 'POST',
    url: url,
    dataType: 'json',
    data: {
      parqueo: id
    },
    beforeSend: function () {
      clean();
    },
    success: function (data) {
      if (data.match == true) {
        info = data.info;
        $("#parqueo").val(info.parqueo);
        $("#parqueo_placa_edit").val(info.placa);
        $("#parqueo_tarifa_edit").val(info.tarifa);
        $("#parqueo_fingreso_edit").val(info.fingreso);
        $("#parqueo_hingreso_edit").val(info.hingreso);
        $("#parqueo_codservicio_edit").val(info.codserv);
        $("#add_parqueo").modal("toggle");
      }
      else {
        Swal.fire({
          icon: "info",
          title: "Información no disponible.",
          html: "<b class='color-red'></b>",
          showConfirmButton: true,
          timer: 4000
        });
      }
    },
    complete: function () {
    }
  });
}

function deshabilitar(parqueo,situacion) {
  
  //Ticket emitido
  if(situacion == 1){
     Swal.fire({
        icon: "info",
        title: "¿Esta seguro de anular el registro seleccionado?",
        html: "<b class='color-red'>Esta acción no se puede deshacer.</b>",
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
      }).then(result => {
        if (result.value) {
          var url = base_url + "index.php/ventas/parqueo/deshabilitar_parqueo";
          $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {
              parqueo: parqueo
            },
            success: function (data) {
              if (data.result == "success") {
                titulo = "¡Registro anulado!";
                Swal.fire({
                  icon: "success",
                  title: titulo,
                  showConfirmButton: true,
                  timer: 2000
                });
              }
              else {
                Swal.fire({
                  icon: "error",
                  title: "Sin cambios.",
                  html: "<b class='color-red'>La información no pudo ser eliminada, intentelo nuevamente.</b>",
                  showConfirmButton: true,
                  timer: 4000
                });
              }
            },
            complete: function () {
              search(false);
            }
          });
        }
      });
  }
  //Ticket calculado
  else if(situacion == 2){
    Swal.fire({
      icon: "info",
      title: "Este ticket esta emitido, para anularlo <br>introduzca sus credenciales.",
      html:
        '<input id="swal-usuario" class="swal2-input" type="text" placeholder="Introduzca su usuario">' +
        '<input id="swal-clave" class="swal2-input" type="password" placeholder="Introduzca su password">',
      focusConfirm: false,
      preConfirm: () => {
        return [
          document.getElementById('swal-usuario').value,
          document.getElementById('swal-clave').value
        ]
      },
      showCancelButton: true
    }).then(result => {
      let usuario = result.value[0];
      let clave   = result.value[1];
      $.ajax({
              type: 'post',
              dataType: 'json',
              url: base_url + "index.php/index/obtener_datosUsuarioLogin",
              data: {usuario:usuario, clave:clave, parqueo:parqueo},
              success: function (data) {
                  if (data.result == 'success') {

                     //Anulamos el registro
                     Swal.fire({
                        icon: "info",
                        title: "¿Esta seguro de anular el registro seleccionado?",
                        html: "<b class='color-red'>Esta acción no se puede deshacer.</b>",
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "Cancelar"
                      }).then(result => {
                          if (result.value) {

                            //Anulamos el registro
                            var url = base_url + "index.php/ventas/parqueo/deshabilitar_parqueo";
                            $.ajax({
                              type: 'POST',
                              url: url,
                              dataType: 'json',
                              data: {
                                parqueo: parqueo
                              },
                              success: function (data2) {
                                if (data2.result == "success") {
                                  titulo = "¡Registro anulado!";
                                  Swal.fire({
                                    icon: "success",
                                    title: titulo,
                                    showConfirmButton: true,
                                    timer: 2000
                                  });
                                }
                                else {
                                  Swal.fire({
                                    icon: "error",
                                    title: "Sin cambios.",
                                    html: "<b class='color-red'>La información no pudo ser eliminada, intentelo nuevamente.</b>",
                                    showConfirmButton: true,
                                    timer: 4000
                                  });
                                }
                              },
                              complete: function () {
                                search(false);
                              }
                            });      
                            //Fin anularcion                      

                          }
                      });

                  }
                  else {

                      Swal.fire({
                              icon: 'error',
                              title: 'Sin cambios.',
                              html: "<b class='color-red'>" + data.message + "</b>",
                              showConfirmButton: true
                      });
                      
                  }
              }
      });

    });

  }

}

function clean() {
  //Limpiamos campos de busqueda
  $("#search_placa").val("");
  $("#search_tarifa").val("");
  $("#search_serie").val("");
  $("#search_numero").val("");
  $("#search_numero_ticket").val("");
  
  //Limpiamos campos del formulario
  $("#parqueo").val("");
  $("#parqueo_placa_edit").val("");
  $("#parqueo_tarifa_edit").val("");
  $("#parqueo_fingreso_edit").val("");
  $("#parqueo_hingreso_edit").val("");
  $("#parqueo_fsalida_edit").val("");
  $("#parqueo_hsalida_edit").val("");
  $("#parqueo_tiempo_edit").val("");
  $("#parqueo_monto_edit").val("");
  $("#parqueo_observacion_edit").val("");
  
  //Limpiamos componentes
  $("#ticket_anulados").html("");
  $("#ticket_emitidos").html("");
  $("#ticket_pendientes").html("");
  $("#ticket_facturados").html("");  
}

function abrir_pdf_envioSunat(codigo){
    url = base_url+"index.php/ventas/comprobante/consutarRespuestaPdfsunat/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log('Get: '+url);
        },
        error: function (data) {
             console.log('Error:' + data);
        },
        success: function (data) {
           url = data.respuestas_enlacepdf;
        }
    });
    window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}