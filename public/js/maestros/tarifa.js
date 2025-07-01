$(document).ready(function () {
    
  $('#dataTable-tarifa').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    autoWidth: false,
    ajax: {
      url: base_url + 'index.php/maestros/tarifa/datatable_tarifa/',
      type: "POST",
      data: { dataString: "" },
      beforeSend: function () {
      },
      error: function () {
      },
      complete: function () {
      }
    },      
    language: "spanish",
    order: [[0, "asc"]]
  });

  $("#buscar").click(function () {
    search();
  });
  
  $("#nuevo").click(function () {
    clean();
    $('#add_tarifa').modal('show');
  });  

  $("#limpiar").click(function () {
    search(false);
  });

  $('#form_busqueda').keypress(function (e) {
    if (e.which == 13) {
      return false;
    }
  });
  
  $("#tarifa_tipo").change(function(){
      tipotarifa = $(this).val();
      if(tipotarifa == 1){//Tarifa normal
          $("#div_tarifa_horarios").hide();
          $("#tarifa_hora_inicio").val("");
          $("#tarifa_hora_fin").val("");
          $("#tarifa_hora_inicio").attr("readonly",true);
          $("#tarifa_hora_fin").attr("readonly",true);
      }
      else{//Tarifa plana y de abonados
          $("#div_tarifa_horarios").show();
          $("#tarifa_hora_inicio").removeAttr("readonly");
          $("#tarifa_hora_fin").removeAttr("readonly");
      }
  });

});

function search(search = true) {
  if (search == true) {
    descripcion = $("#descripcion_tarifa").val();
  }
  else {
    $("#descripcion_tarifa").val("");
    descripcion = "";
  }
  $('#dataTable-tarifa').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + 'index.php/maestros/tarifa/datatable_tarifa/',
      type: "POST",
      data: {
        descripcion: descripcion
      },
      beforeSend: function () {
      },
      error: function () {
      },
      complete: function () {
      }
    },
    language: "spanish",
    order: [[0, "asc"]]
  });
}

function editar(id) {
  //$("#div_tarifa_horarios").show();
  var url = base_url + "index.php/maestros/tarifa/getTarifas";
  $.ajax({
    type: 'POST',
    url: url,
    dataType: 'json',
    data: {
      tarifa: id
    },
    beforeSend: function () {
      clean();
    },
    success: function (data) {
      if (data.match == true) {
        info   = data.info;
        
        $("#tarifa_hora_inicio").removeAttr("readonly");
        $("#tarifa_hora_fin").removeAttr("readonly");
        
        tipotarifa = info.tipo;
        $("#tarifa").val(info.tarifa);
        $("#tarifa_descripcion").val(info.descripcion);
        $("#tarifa_precio").val(info.precio);
        $("#tarifa_tipo").val(info.tipo);
        $("#tarifa_hora_inicio").val(info.hinicio);
        $("#tarifa_hora_fin").val(info.hfin);
        
        if(tipotarifa == 1){//Tarifa Normal no tiene horario
            //$("#div_tarifa_horarios").hide();
            $("#tarifa_hora_inicio").attr("readonly",true);
            $("#tarifa_hora_fin").attr("readonly",true);
        }    
        
        $("#add_tarifa").modal("toggle");
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

function registrar_tarifa() {
  Swal.fire({
    icon: "question",
    title: "¿Esta seguro de guardar el registro?",
    html: "<b class='color-red'></b>",
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: "Aceptar",
    cancelButtonText: "Cancelar"
  }).then(result => {
    if (result.value) {
      var rol = $("#rol").val();
      var url = base_url + "index.php/maestros/tarifa/guardar_registro";
      var descripcion   = $("#tarifa_descripcion").val();
      var tarifa_tipo   = $("#tarifa_tipo").val();
      var tarifa_precio = $("#tarifa_precio").val();
      var tarifa_hora_inicio = $("#tarifa_hora_inicio").val();
      var tarifa_hora_fin    = $("#tarifa_hora_fin").val();
      validacion = true;
      
      if (tarifa_tipo == "") {
        Swal.fire({
          icon: "error",
          title: "Verifique los datos ingresados.",
          html: "<b class='color-red'>Debe seleccionar un tipo de tarifa.</b>",
          showConfirmButton: true,
          timer: 4000
        });
        $("#tarifa_tipo").focus();
        validacion = false;
      }      
      else if (descripcion == "") {
        Swal.fire({
          icon: "error",
          title: "Verifique los datos ingresados.",
          html: "<b class='color-red'>Debe ingresar un descripcion.</b>",
          showConfirmButton: true,
          timer: 4000
        });
        $("#tarifa_descripcion").focus();
        validacion = false;
      }
      else if (tarifa_precio == "") {
        Swal.fire({
          icon: "error",
          title: "Verifique los datos ingresados.",
          html: "<b class='color-red'>Debe ingresar un precio.</b>",
          showConfirmButton: true,
          timer: 4000
        });
        $("#tarifa_precio").focus();
        validacion = false;
      }   
      else if(tarifa_tipo == 3 || tarifa_tipo == 2){//Validaciones para tarifa plana o abonados
          
        if (tarifa_hora_inicio == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una hora de inicio.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#tarifa_hora_inicio").focus();
          validacion = false;
        } 
        else if (tarifa_hora_fin == "") {
          Swal.fire({
            icon: "error",
            title: "Verifique los datos ingresados.",
            html: "<b class='color-red'>Debe ingresar una hora de fin.</b>",
            showConfirmButton: true,
            timer: 4000
          });
          $("#tarifa_hora_fin").focus();
          validacion = false;
        }         
        
      }

      if (validacion == true) {
        var info = $("#formTarifa").serialize();
        $.ajax({
          type: 'POST',
          url: url,
          dataType: 'json',
          data: info,
          success: function (data) {
            if (data.result == "success") {
              if (tarifa == "")
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
              
              //Cerramos el modal
              $("#add_tarifa").modal("hide");
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
            $("#tarifa_descripcion").focus();
          }
        });
      }
    }
  });
}

function deshabilitar(tarifa) {
  Swal.fire({
    icon: "info",
    title: "¿Esta seguro de eliminar el registro seleccionado?",
    html: "<b class='color-red'>Esta acción no se puede deshacer.</b>",
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: "Aceptar",
    cancelButtonText: "Cancelar"
  }).then(result => {
    if (result.value) {
      var url = base_url + "index.php/maestros/tarifa/deshabilitar_tarifa";
      $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {
          tarifa: tarifa
        },
        success: function (data) {
          if (data.result == "success") {
            titulo = "¡Registro eliminado!";
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

function clean() {
  $("#tarifa").val("");
  $("#tarifa_tipo").val("");
  $("#tarifa_descripcion").val("");
  $("#tarifa_precio").val("");
  $("#tarifa_hora_inicio").val("");
  $("#tarifa_hora_fin").val("");
}