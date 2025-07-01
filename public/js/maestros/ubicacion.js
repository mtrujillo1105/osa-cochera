// Call the dataTables jQuery plugin

$(document).ready(function () {

  $('#dataTable-ubicacion').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    autoWidth: false,
    ajax: {
      url: base_url + 'index.php/maestros/ubicacion/datatable_ubicacion/',
      type: "POST",
      data: { dataString: "" },
      beforeSend: function () {
      },
      error: function () {
      },
      complete: function () {
      }
    },
    pageLength: 50,
    language: "spanish",
    order: [[0, "asc"]]
  });

  $("#buscar").click(function () {
    search();
  });

  $("#limpiar").click(function () {
    search(false);
  });

  $('#form_busqueda').keypress(function (e) {
    if (e.which == 13) {
      return false;
    }
  });

});

function search(search = true){
  if (search == true) {
    descripcion = $("#ubicacion_descripcion").val();
  }
  else {
    $("#ubicacion_descripcion").val("");
    descripcion = "";
  }
  $('#dataTable-ubicacion').DataTable({
    responsive: true,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + 'index.php/maestros/ubicacion/datatable_ubicacion/',
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
  var url = base_url + "index.php/maestros/ubicacion/getUbicacion";
  $.ajax({
    type: 'POST',
    url: url,
    dataType: 'json',
    data: {
      ubicacion: id
    },
    beforeSend: function () {
      clean();
    },
    success: function (data) {
      if (data.match == true) {
        info = data.info;
        $("#ubicacion").val(info.ubicacion);
        $("#ubicacion_descripcion").val(info.descripcion);
        $("#ubicacion_easignado").val(info.easignados);
        $("#ubicacion_eusado").val(info.eusados);
        $("#add_ubicacion").modal("toggle");
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

function registrar_ubicacion() {
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
      var url = base_url + "index.php/maestros/ubicacion/guardar_registro";
      var descripcion = $("#ubicacion_descripcion").val();
      validacion = true;
      if (descripcion == "") {
        Swal.fire({
          icon: "error",
          title: "Verifique los datos ingresados.",
          html: "<b class='color-red'>Debe ingresar un descripcion.</b>",
          showConfirmButton: true,
          timer: 4000
        });
        $("#ubicacion_descripcion").focus();
        validacion = false;
      }

      if (validacion == true) {
        var info = $("#formUbicacion").serialize();
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
            $("#ubicacion_descripcion").focus();
          }
        });
      }
    }
  });
}

function clean() {
  $("#ubicacion").val("");
  $("#tarifa_descripcion").val("");
  $("#tarifa_precio").val("");
  $("#tarifa_precio_nuevo").val("");
}