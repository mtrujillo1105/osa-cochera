/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {
  $(".contenido").hide();  
  $("#search_modulo").change(function () {
    let arrctrl = $(this).val().split("/");
    let ctrl = arrctrl[2];
    switch (ctrl){
      case 'stockproducto':
        $(".contenido").hide();          
        $("#stockproducto").show();
        break;
      case 'stockalmacenes':
        $(".contenido").hide();          
        $("#stockalmacenes").show();
        break;
      default:
        alert('Seleccion una opcion');
        break;
    }
  });

  /** Stock producto*/ 
  $('#table-stockProducto').DataTable({
    responsive: false,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    pageLength: 25,
    ajax: {
      url: base_url + "index.php/reportes/almacen/dtStockProducto/",
      type: "POST",
      data: {},
      beforeSend: function () {
      },
      error: function () {
      }
    },
    language: spanish,
    order: [[2, "asc"]]
  });

  $("#buscarSP").click(function () {
    searchSP();
  });

  $("#limpiarSP").click(function () {
    searchSP(false);
  });

  $("#imprimirP").click(function () {
    url = base_url + "index.php/reportes/almacen/imprimirP/"
    $('#form_stockproducto').attr('action', url);
    $('#form_stockproducto').serialize();
    $('#form_stockproducto').attr('target', '_blank');
    $('#form_stockproducto').submit();
  });

  $("#imprimirSP").click(function () {
    url = base_url + "index.php/reportes/almacen/imprimirE/rpt_StockProductos"
    $('#form_stockproducto').attr('action', url);
    $('#form_stockproducto').serialize();
    $('#form_stockproducto').submit();
  });	
  
  /** Stock Almacen producto */
  $(document).ready(function () {
    $('#table-stockAlmacen').DataTable({
      responsive: false,
      filter: false,
      destroy: true,
      processing: true,
      serverSide: true,
      pageLength: 25,
      ajax: {
        url: base_url + "index.php/reportes/almacen/dtStockAlmacen/",
        type: "POST",
        data: { txtAlmacen: $("#txtAlmacen").val() },
        beforeSend: function () {
        },
        error: function () {
        }
      },
      language: spanish,
      order: [[2, "asc"]]
    });

    $("#buscarAP").click(function () {
      searchAP();
    });

    $("#limpiarAP").click(function () {
      searchAP(false);
    });

    $("#imprimirAP").click(function () {
      url = base_url + "index.php/reportes/almacen/imprimirE/rpt_StockAlmacenes";
      $('#form_stockalmacenes').attr('action', url);
      $('#form_stockalmacenes').submit();
    });
  });
  
  
});

/** Search Stock Producto */
function searchSP(search = true) {
  if (search == false) { 
    $("#form_stockproducto")[0].reset();
  }
  codigo   = $('#txtCodigo').val();
  producto = $('#txtNombre').val();
  familia  = $('#txtFamilia').val();
  marca    = $('#txtMarca').val();
  modelo   = $('#txtModelo').val();
  $('#table-stockProducto').DataTable({
    responsive: false,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    pageLength: 25,
    ajax: {
      url: base_url + "index.php/reportes/almacen/dtStockProducto/",
      type: "POST",
      data: { txtCodigo: codigo, txtNombre: producto, txtFamilia: familia, txtMarca: marca, txtModelo: modelo },
      error: function () {
      }
    },
    language: spanish,
    order: [[2, "asc"]]
  });
}

/** Search Almacen producto */
function searchAP(search = true) {
  if (search == false) {
    $("#form_stockalmacenes")[0].reset();
  }
  almacen  = $('#txtAlmacenAP').val();
  codigo   = $('#txtCodigoAP').val();
  producto = $('#txtNombreAP').val();
  familia  = $('#txtFamiliaAP').val();
  marca    = $('#txtMarcaAP').val();
  modelo   = $('#txtModeloAP').val();
  $('#table-stockAlmacen').DataTable({
    responsive: false,
    filter: false,
    destroy: true,
    processing: true,
    serverSide: true,
    pageLength: 25,
    ajax: {
      url: base_url + "index.php/reportes/almacen/dtStockAlmacen/",
      type: "POST",
      data: { txtAlmacenAP: almacen, txtCodigoAP: codigo, txtNombreAP: producto, txtFamiliaAP: familia, txtMarcaAP: marca, txtModeloAP: modelo },
      error: function () {
      }
    },
    language: spanish,
    order: [[2, "asc"]]
  });
}