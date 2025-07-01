$(document).ready(function () {
  clock();  
  /** Parqueos */
  $("#datos_abonado").hide();

  $("#txt_placa").focus();
  var tabla;
  
  listar_parqueos();
  listar_abonados();
  listar_monitor_vehiculos();  

  function listar_parqueos() {
      
    tabla = $('#dataTable-parqueo').DataTable({
        responsive: true,
        filter: false,
        destroy: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        "lengthChange": false, 
        "dom": "lfrti",
        ajax: {
          url: base_url + 'index.php/seguridad/inicio/datatable_parqueo/',
          type: "POST",
          data: { dataString: "" },
          beforeSend: function () {
          },
          error: function () {
          },
          complete: function () {
          }
        },
        pageLength: 5000,
        language: "spanish",
        columnDefs: [
            {
                "className": "text-center", 
                "targets": [1,2,3] 
            }, 
            {
                "targets": [0,4],
                "visible": false
            }
        ],
        order: [[4, "desc"]]
    });
    
  }
  
//Seleccionmos una fila del datatable
$('#dataTable-parqueo tbody').on('click', 'tr', function () {
    
    //Habilitamos el botón actualizar parqueo
    $("#actualizar_parqueo").removeAttr("disabled");
    
    //Mostramos el botón actualizar parqueo
    $("#actualizar_parqueo").show();
    
    var data = tabla.row( this ).data();
    var parqueo = data[0];
    var url = base_url + "index.php/ventas/parqueo/getParqueo";

     $.ajax({
       type: 'POST',
       url: url,
       dataType: 'json',
       data: {
         parqueo: parqueo
       },
       beforeSend: function () {
         clean();
       },
       success: function (data) {
         if (data.match == true) {
           info = data.info;
           $("#parqueo_edit").val(info.parqueo);
           
           /*Salda de vehiculo*/
           editar(info.parqueo);

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
});  
  

  $("#buscar").click(function () {
    listar_parqueos();
  });

  $("#limpiar").click(function () {
    listar_parqueos(false);
  });

  $('#form_busqueda').keypress(function (e) {
    if (e.which == 13) {
      return false;
    }
  }); 
  
  $('#txt_placa').keyup(function(e){
      
    //Habilitamos el botón, registrar parqueo
    $("#registrar_parqueo").removeAttr("disabled");       
     
    //Mostramos el botón registrar parqueo
    $("#registrar_parqueo").show();
     
    //Habilitamos el botón actualizar parqueo
    $("#actualizar_parqueo").removeAttr("disabled");     
     
    //Mostramos el boón actualizar parqueo
    $("#actualizar_parqueo").show();
     
    if ( e.which == 13 ){
        var placa = $(this).val();
        
        if(placa!="" && placa.length > 3){
            var url = base_url + "index.php/ventas/parqueo/getParqueoXPlaca";
            $.ajax({
              type: 'POST',
              url: url,
              dataType: 'json',
              data: {
                placa: placa
              },
              beforeSend: function () {
                clean();
              },
              success: function (data) {
                
                //El vehiculo se encuentra registrado
                if (data.match == true){ 
                    
                    var compania = $("#sessionCompany").val();
                    
                    if(data.info.compania == compania){
                        /*Salda de vehiculo*/
                        editar(data.info.parqueo);
                    }
                    else{
                        //Mensaje: el vehiculo está registrado en el otro estacionamiento
                        Swal.fire({
                          icon: "info",
                          title: "El vehículo está ingresado en la otra cochera",
                          html: "<b class='color-red'></b>",
                          showConfirmButton: true,
                          timer: 4000
                        });
                        
                    }

                }
                else{
                    /*Ingreso de vehiculo*/
                    $("#parqueo_placa").focus();
                    busca_vehiculo_x_placa(placa);
                }
              },
              complete: function () {
                  $('#txt_placa').val("");
              }
            });
        }
    }
  });
  
  $("#txt_codparqueo").keyup(function(e){
      
    if ( e.which == 13 ){
        var parqueo = $(this).val();
        editar(parqueo); 
        $("#txt_codparqueo").val("");
    }
    
  });

function f_registrar_parqueo(){
    
    //Deshabilito el botón registrar parqueo
    $("#registrar_parqueo").attr("disabled",true);    
    
    var url = base_url + "index.php/seguridad/inicio/guardar_registro";
    var placa = $("#parqueo_placa").val();
    var tarifa = $("#parqueo_tarifa").val();
    var fecha = $("#usr_date").val();
    var hora = $("#usr_hour").val();
    validacion = true;

    if (placa == "") {
      Swal.fire({
        icon: "error",
        title: "Verifique los datos ingresados.",
        html: "<b class='color-red'>Debe ingresar un placa.</b>",
        showConfirmButton: true,
        timer: 4000
      });
      $("#parqueo_placa").focus();
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
      $("#parqueo_tarifa").focus();
      validacion = false;
    }    

    if(validacion){
      $("#parqueo_fingreso").val(fecha);
      $("#parqueo_hingreso").val(hora);
      var info = $("#formParqueo").serialize();
      $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: info,
        success: function (data) {
          if (data.result == "success") {
            var parqueo  = data.info;
            
            //Ocutamos modal agregar parqueo
            $("#agregar_parqueo").modal("hide");

            //Limpiamos el formulario
            clean();
            
            url2 = base_url + "index.php/ventas/parqueo/imprimir_ticket_pdf/" + parqueo;
            window.open(url2,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
            
            /*$('#frmParqueo').attr('target', '_blank');
            $('#frmParqueo').attr('action', url);
            $('#frmParqueo').submit();      */      

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
            
          //Mostramos los botónes de impresion de comprobantes
          $("#div_imprimir_ticket").show();
          
          //Ocultamos el botón registrar parqueo
          $("#registrar_parqueo").hide();
          
          //Devolvemos el foco a la caja de texto txt_placa
          $("#txt_placa").focus();
          
          listar_parqueos();
          listar_monitor_vehiculos();
        }
      });
    }   
}

  $("#registrar_parqueo").click(function (e) {
    f_registrar_parqueo();
  });  

  $("#actualizar_parqueo").click(function () {
    /*Swal.fire({
      icon: "question",
      title: "¿Esta seguro de actualizar el registro?",
      html: "<b class='color-red'></b>",
      showConfirmButton: true,
      showCancelButton: true,
      confirmButtonText: "Aceptar",
      cancelButtonText: "Cancelar"
    }).then(result => {*/
      //if (result.value) {
      
        //Des-Habilitamos el botón actualizar parqueo
        $("#actualizar_parqueo").attr("disabled",true);  
                
        var url = base_url + "index.php/seguridad/inicio/actualizar_registro";
        var fecha = $("#usr_date").val();
        var hora = $("#usr_hour").val();
        var placa = $("#parqueo_placa_edit").val();
        var tarifa = $("#parqueo_tarifa_edit").val();
        var tipotarifa = $("#parqueo_tipotarifa_edit").val();
        //var fingreso = $("#parqueo_fingreso_edit").val();
        //var hingreso = $("#parqueo_hingreso_edit").val();
        $("#parqueo_fsalida_edit").val(fecha);
        $("#parqueo_hsalida_edit").val(hora);  
        
        validacion = true;
        
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
                  
                var subtotal  = Math.round(data.info.PARQC_Monto*0.85*100)/100;
                var igv       = Math.round(data.info.PARQC_Monto*0.15*100)/100;
                var total     = data.info.PARQC_Monto;
                var situacion = data.info.PARQC_FlagSituacion;
                
                $("#parqueo_placa_edit").focus();
                $("#parqueo_fsalida_edit").val(data.info.PARQC_FechaSalida);
                $("#parqueo_hsalida_edit").val(data.info.PARQC_HoraSalida);                
                $("#parqueo_tiempo_edit").val(data.info.PARQC_TiempoHoraMin);
                $("#parqueo_valor_venta_edit").val(subtotal);
                $("#parqueo_igv_edit").val(igv);
                $("#parqueo_monto_edit").val(total);                

                //Ocultamos botón salir
                $("#salir_actualizar").hide();              
                
                //Ocultamos el botón actualizar parqueo
                $("#actualizar_parqueo").hide();
                
                //Mostramos el botón imprimir comprobante si no esta Facturado y si es tarifa normal o tarifa plana
                if(situacion != 3 && (tipotarifa == 1 || tipotarifa == 3) ){
                    $("#div_imprimir_comprobantes").show();                       
                }
                
                //Mostramos el botón salir si el tipo tarifa abonado, tarifa plana y exonerados
                if(tipotarifa == 2 || tipotarifa == 3 || tipotarifa == 4){
                    $("#salir_actualizar").show();    
                }
                
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
              listar_parqueos();
              listar_monitor_vehiculos();
            }
          });
        }
      //}
    //});
  });
  
  $("#parqueo_tarifa").change(function(){
      var tarifa = $(this).val();
      var url    = base_url + "index.php/maestros/tarifa/getTarifas";
      $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {tarifa: tarifa},
            success: function (data) {
              if (data.match == true) {
                $("#parqueo_preciotarifa").val(data.info.precio);
              }
            },
            complete: function () {}
          
      });
  });

    $("#salir_actualizar").click(function(){
        $("#txt_placa").focus();
        $('#add_parqueo').modal('hide');
        clean();
    });

    //Esto ya no se usa, revisar
    $('#nuevo').click(function(){
        clean();
        $('#agregar_parqueo').modal('show');
        
        //Habilitamos el botón, registrar parqueo
        $("#registrar_parqueo").removeAttr("disabled");          
        
        //Mostramos el botón, registrar paqueo
        $("#registrar_parqueo").show();
        
        if(id_compania == 1){
            $("#parqueo_tarifa").val(3);
        }
        else if(id_compania == 2){
            $("#parqueo_tarifa").val(25);
        }
    });

    $("#salir").click(function(){
      $("#txt_placa").focus();
      $('#agregar_parqueo').modal('hide');
      
      //Habilitamos el botón, registrar parqueo
      $("#registrar_parqueo").removeAttr("disabled");        
      
      //Mostramos el botón registrar parqueo
      $("#registrar_parqueo").show();
      
      //Ocultamos los botones imprimir comprobantes
      $("#div_imprimir_ticket").hide();
      
      ////Ocultamos los datos del abonado
      $("#datos_abonado").hide();
      
      $("input:radio").attr("checked", false);
      clean();
    });  

    $("#imprimir_parqueo").click(function(){
      url = base_url + "index.php/ventas/parqueo/imprimir_parqueo/"
      $('#formParqueo').attr('action', url);
      $('#formParqueo').serialize();
      $('#formParqueo').attr('target', '_blank');
      $('#formParqueo').submit();
      $('#agregar_parqueo').modal('hide');
    });
    
    $(".imprimir_tickets_activos").click(function () {
      var url = base_url + "index.php/seguridad/inicio/imprimir_ticket_diarios/";
      $('#frmParqueo').attr('target', '_blank');
      $('#frmParqueo').attr('action', url);
      $('#frmParqueo').submit();
    });    

    $(".create_comprobante").click(function () {
      var tipo_docu = $(this).attr("id");
      var codservicio = $("#parqueo_codservicio_edit").val();
      dataString = "tipo_docu="+tipo_docu+"&codservicio="+codservicio;
      url = base_url + "index.php/ventas/comprobante/create_comprobante_parqueo";

      $.ajax({
           type: "POST",
           url: url,
           data: dataString,
           dataType: 'json',
           success: function(data){
               comprobante = data.info.codcomprobante;
               location.href = base_url + "index.php/ventas/comprobante/comprobante_editar/" + comprobante + "/V/" + tipo_docu + "/N";
           },
           complete: function(data){
               $('img#loading').css('visibility', 'hidden');
           }
       });
      $("#div_imprimir_comprobantes").hide();
    });  

  function listar_monitor_vehiculos(){
    $('#dataTable-monitor').DataTable({
      responsive: true,
      filter: false,
      destroy: true,
      processing: true,
      serverSide: true,
      autoWidth: false,
      "lengthChange": false,
      ajax: {
        url: base_url + 'index.php/seguridad/inicio/datatable_monitor_vehiculos/',
        type: "POST",
        data: { dataString: "" },
        beforeSend: function () {
        },
        error: function () {
        },
        complete: function (d) {
            console.log(d.responseJSON.data);
            var txtplaca  = '';  
            var vermsg = false
            $.each(d.responseJSON.data,function(index,value){
                var placa     = value[0];
                var situacion = value[5];
                if(situacion == 2){
                    vermsg   = true;
                    txtplaca = txtplaca + placa + '<br>';
                }
            });
            
            //Muestra mensaje
            if(vermsg){
                Swal.fire({
                  icon: "warning",
                  title: "La siguientes placas están fuera de horario, retírelas del estacionamiente.\n",
                  html: "<b class='color-red'>" + txtplaca +"</b>",
                  showConfirmButton: true,
                  timer: 4000
                });
            }

        }
      },   
      pageLength: 100,
      language: "spanish",
      columnDefs: [{ "className": "text-center", "targets": 0 }],
      order: [[1, "desc"]]
    });
  }
  
  function listar_abonados(){
    $('#dataTable-abonados').DataTable({
      responsive: true,
      filter: false,
      destroy: true,
      processing: true,
      serverSide: true,
      autoWidth: false,
      "lengthChange": false,
      ajax: {
        url: base_url + 'index.php/seguridad/inicio/datatable_abonados/',
        type: "POST",
        data: { dataString: "" },
        beforeSend: function () {
        },
        error: function () {
        },
        complete: function () {
        }
      },   
      pageLength: 100,
      language: "spanish",
      columnDefs: [{ "className": "text-center", "targets": 0 }],
      order: [[1, "desc"]]
    });
  }
  
  $("#parqueo_placa").blur(function(){
    var placa = $("#parqueo_placa").val();
    if(placa!="" && placa.length > 3){
      url   = base_url + "index.php/ventas/parqueo/getParqueoXPlaca";
      placa = $("#parqueo_placa").val();
      $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {
          "placa": placa,
        },
        success: function (data) {
          if (data.match == true) {
            Swal.fire({
              icon: "error",
              title: "Sin cambios.",
              html: "<b class='color-red'>Este vehículo ya se encuentra registrado.</b>",
              showConfirmButton: true,
              timer: 4000
            });
            $("#parqueo_placa").val("");
          }
          else{
              verifica_abonado();
          }
        },
        complete: function () {}
      });
    }
    
  });
  
  /*
  $("#parqueo_placa").blur(function(){
      //Busco si el vehiculo es abonado
      var placa = $("#parqueo_placa").val();
      var url = base_url + 'index.php/empresa/empresa/getContactos/';
      if(placa != ""){
        $.ajax({
              type: 'POST',
              url: url,
              dataType: 'json',
              data: {
                "placa": placa
              },
              beforeSend: function () {
                clean();
              },
              success: function (data) {
                if (data.match == true) {
                  info = data.info;
                  if(info != null){
                    $("#parqueo_tarifa").val(info.tarifa);                      
                  }
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
  });
*/


});

function search(search = true) {
  if (search == true){
    placa = $("#txt_placa").val();
  }
  else{
    placa = "";
  }
  /*$('#dataTable-parqueo').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    autoWidth: false,   
    ajax: {
      url: base_url + 'index.php/seguridad/inicio/datatable_parqueo/',
      type: "POST",
      data: {
          placa : placa
      },
      beforeSend: function () {
      },
      error: function () {
      },
      complete: function () {
      }
    },
    pageLength: 5000,
    language: "spanish",
    columnDefs: [{ "className": "text-center", "targets": 0 }, {"targets": [0],"visible": false}],
    order: [[2, "desc"],[3, "desc"]]
  });*/
    
  listar_parqueos();
}

function editar(id) {
  //Mostramos botón salir 
  $("#salir_actualizar").show();  
    
  //Ocultamos capas botones para facturar y capa datos tarifa plana
  $("#div_imprimir_comprobantes").hide();
  $("#div_datos_tarifa_plana_edit").hide();
  $("#div_datos_abonado_edit").hide();
  
  var url = base_url + "index.php/ventas/parqueo/getParqueo";

  $.ajax({
    type: 'POST',
    url: url,
    dataType: 'json',
    data: {parqueo: id},
    beforeSend: function () {
      clean();
    },
    success: function (data) {
      if (data.match == true) {
        info = data.info;
        $("#parqueo_edit").val(info.parqueo);
        $("#parqueo_placa_edit").val(info.placa);
        $("#parqueo_tarifa_edit").val(info.tarifa);
        $("#parqueo_tarifa_edit2").val(info.tarifa);
        $("#parqueo_tipotarifa_edit").val(info.tipotarifa);
        $("#parqueo_fingreso_edit").val(info.fingreso);
        $("#parqueo_hingreso_edit").val(info.hingreso);
        $("#parqueo_codservicio_edit").val(info.codserv);    

        if(info.tipotarifa == "3"){//Tarifa plana
            
            //Datos tarifa plana
            $("#parqueo_tarifa_situacion_edit").html(info.situacion);
            $("#parqueo_tarifa_comprobante_edit").html(info.comprobante);   
            
            if(info.idsituacion == "3")//Ticket facturado
                $("#div_datos_tarifa_plana_edit").show();
            else if(info.idsituacion == "2")//Ticket calculado
                $("#div_imprimir_comprobantes").show(); 
            
            /*
            if(info.idsituacion != "3")//Ticket emitido, Ticket calculado                
                //Mostramos capa botones para facturar y capa datos tarifa plana
                $("#div_imprimir_comprobantes").show();      
            else//Ticket facturado
                //Mostramos datos de tarifa plana
                $("#div_datos_tarifa_plana_edit").show();
            */
        }
        else if(info.tipotarifa == "2"){//Tarifa abonado
            
            //Datos abonado
            $("#parqueo_abonado_edit").html(info.datos_abonado.nombre_abonado);   
            $("#parqueo_fpago_edit").html(info.datos_abonado.fecha_pago);   
            $("#parqueo_situacion_edit").html(info.datos_abonado.situacion_abon);   
            $("#cod_parqueo_situacion_edit").val(info.datos_abonado.cod_situacion_abon);  
            
            //Mostramos capa de abonados
            $("#div_datos_abonado_edit").show();
            
        }
        
        //Mostramos modal Salida de vehiculo y lo BLOQUEAMOS
        $('#add_parqueo').modal({backdrop: 'static', keyboard: false})
        
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

function clean() {
  $("#parqueo").val("");
  $("#parqueo_placa").val("");
  $("#parqueo_tarifa").val("");
  $("#parqueo_fingreso").val("");
  $("#parqueo_hingreso").val("");
  $("#parqueo_fsalida_edit").val("");
  $("#parqueo_hsalida_edit").val("");    
  $("#parqueo_tiempo_edit").val("");    
  $("#parqueo_valor_venta_edit").val("");    
  $("#parqueo_igv_edit").val("");          
  $("#parqueo_monto_edit").val("");   
  //Inicializamos datos abonado agregar
  $("#nombre_contacto").val("");   
  $("#parqueo_fpago").val("");   
  $("#parqueo_situacion").val("");   
  //Inicilizamos datos del abonado editar
  $("#parqueo_abonado_edit").html("");   
  $("#parqueo_fpago_edit").html("");   
  $("#parqueo_situacion_edit").html("");   
  //Inicializamos datos tarifa plana editar
  $("#parqueo_tarifa_situacion_edit").val("");
  $("#parqueo_tarifa_comprobante_edit").val("");
}

function verifica_abonado(){
    var placa = $("#parqueo_placa").val();
    if(placa!="" && placa.length > 3){
      url   = base_url + "index.php/empresa/cliente/getVehiculoXPlaca";
      $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {
          "placa": placa,
        },
        success: function (data) {
          if (data.match == true) {
              var contacto = data.info.contacto;
              var tarifa   = data.info.tarifa;
              var nombre   = data.info.nombre;
              $("#contacto").val(contacto);
              $("#parqueo_tarifa").val(tarifa);
              $("#nombre_contacto").html(nombre);
          }
        },
        complete: function () {}
      });
    }
}

function busca_vehiculo_x_placa(placa){
    
    $("#datos_abonado").hide();
    
    url   = base_url + "index.php/empresa/cliente/getVehiculoXPlaca";
    
    $.ajax({
      type: 'POST',
      url: url,
      dataType: 'json',
      data: {
        "placa": placa,
      },
      success: function (data) {
        //Mostramos modal agregar parqueo
        //$('#agregar_parqueo').modal('toggle');
        $('#agregar_parqueo').modal({backdrop: 'static', keyboard: false});
        
        //Colocamos el foco en la caja parqueo_placa - todavia falta un poco
        $("#agregar_parqueo").on("show.bs.modal",function(e){
            $("#parqueo_placa").trigger("focus");
        }); 
        
        //Mostramos la placa ingresada en el modal
        $("#parqueo_placa").val(placa);
        
        //Hora actual
        var fecha = new Date();
        var hora  = fecha.getHours();
        var minutos = String(fecha.getMinutes());
        minutos     = minutos.padStart(2,'0');
        var horactual = parseInt(hora+''+minutos);
        
        if (data.match == true) {//Es abonado 
            
            //Horario de abonado
            var hinicio = parseInt(data.info.hinicio);
            var hfin    = parseInt(data.info.hfin);            
            
            //Muestro datos del abonado
            $("#datos_abonado").show();
            $("#parqueo_tarifa").val(data.info.tarifa);
            $("#nombre_contacto").html(data.info.nombres);
            $("#parqueo_fpago").html(data.info.fpago);
            $("#parqueo_situacion").html(data.info.situacion);
            $("#parqueo_horario_abonado").html(data.info.hinicio + ' - ' + data.info.hfin);
            
            //Abonado dentro del horario
            if((horactual >= hinicio && horactual <= hfin) || (horactual >= hinicio || horactual <= hfin)){
                
                //Deshabilito las tarifas que no sean de abonados
                $('#parqueo_tarifa option:not(:selected)').attr('disabled',true);
                $('#parqueo_tarifa option:not(:selected)').hide();
                
            }
            else{//Abonado fuera del horario
                //
                //Habilito todas los tipos de tarifa
                $('#parqueo_tarifa option').show();
                $('#parqueo_tarifa option').removeAttr('disabled');
                var tarifa_defecto;
                if(id_compania == 1){
                    tarifa_defecto = 3;
                }
                else if(id_compania == 2){
                    tarifa_defecto = 25;
                }
                $("#parqueo_tarifa").val(tarifa_defecto);
                $("#parqueo_tarifa").change();   
                
            }

        }
        else{//Vehiculo normal
            
            //Habilito todas los tipos de tarifa
            $('#parqueo_tarifa option').show();
            $('#parqueo_tarifa option').removeAttr('disabled');
            var tarifa_defecto;
            if(id_compania == 1){
                tarifa_defecto = 3;
            }
            else if(id_compania == 2){
                tarifa_defecto = 25;
            }
            $("#parqueo_tarifa").val(tarifa_defecto);
            $("#parqueo_tarifa").change();          
        }
        
      },
      complete: function () {}
    });

}