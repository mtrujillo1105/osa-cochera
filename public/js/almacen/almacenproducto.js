/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {
	$('#table-stockAlmacen').DataTable({
		responsive: false,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		pageLength: 25,
		ajax: {
			url: base_url + "index.php/almacen/almacenproducto/dtStockAlmacen/",
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

	$("#buscar").click(function () {
		search();
	});

	$("#limpiar").click(function () {
		search(false);
	});
});

function search(search = true) {

	if (search == false) {
		$("#form_busqueda")[0].reset();
	}

	almacen = $('#txtAlmacen').val();
	codigo = $('#txtCodigo').val();
	producto = $('#txtNombre').val();
	familia = $('#txtFamilia').val();
	marca = $('#txtMarca').val();
	modelo = $('#txtModelo').val();

	$('#table-stockAlmacen').DataTable({
		responsive: false,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		pageLength: 25,
		ajax: {
			url: base_url + "index.php/almacen/almacenproducto/dtStockAlmacen/",
			type: "POST",
			data: { txtAlmacen: almacen, txtCodigo: codigo, txtNombre: producto, txtFamilia: familia, txtMarca: marca, txtModelo: modelo },
			error: function () {
			}
		},
		language: spanish,
		order: [[2, "asc"]]
	});
}