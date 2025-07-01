/* *********************************************************************************
/* ******************************************************************************** */
$(document).ready(function () {

	$("#btn-up").click(function () {
		$("#inputFilters div").hide("fast");
		$("#btn-up").hide("fast");
		$("#btn-down").show("fast");

		$("#fabricanteSearch").val("");
		$("#familiaSearch").val("");
		$("#marcaSearch").val("");
		$("#modeloSearch").val("");
	});

	$("#btn-down").click(function () {
		$("#inputFilters div").show("fast");
		$("#btn-up").show("fast");
		$("#btn-down").hide("fast");
	});

	$("#codigoSearch, #nombreSearch").keypress(function () {
		let id = $(this).attr("id");

		if (id == 'codigoSearch')
			$("#nombreSearch").val("");
		else
			$("#codigoSearch").val("");
	});

	$("#codigoSearch, #nombreSearch").autocomplete({
		source: function (request, response) {
			let codigo = $("#codigoSearch").val();
			let nombre = $("#nombreSearch").val();
			let familia = $("#familiaSearch").val();
			let marca = $("#marcaSearch").val();
			$.ajax({
				url: base_url + "index.php/almacen/producto/getProductos/",
				type: "POST",
				data: {
					flagBS: 'B',
					codigo: codigo,
					nombre: nombre,
					familia: familia,
					marca: marca
				},
				dataType: "json",
				success: function (data) {
					response(data);
				}
			});
		},
		select: function (event, ui) {
			$("#productoSearch").val(ui.item.id);
			$("#codigoSearch").val(ui.item.codigo);
			$("#nombreSearch").val(ui.item.nombre);
			$("#fabricanteSearch").val(ui.item.fabricante);
			$("#familiaSearch").val(ui.item.familia);
			$("#marcaSearch").val(ui.item.marca);
			$("#modeloSearch").val(ui.item.modelo);
		},
		minLength: 3
	});

	$("#buscar").click(function () {
		search();
	});

	$("#limpiar").click(function () {
		$("#frmSearch")[0].reset();
		$("#table-movimientos .details").empty();
	});

	$('#frmSearch').keypress(function (e) {
		if (e.which == 13) {
			return false;
		}
	});
});

function search(search = true) {


	$('#table-movimiento').DataTable({
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		paging: false,
		ajax: {
			url: base_url + 'index.php/almacen/kardex/dtKardex/',
			type: "POST",
			data: {
				producto: 1,
				almacen: 1
			},
			beforeSend: function () {
			},
			error: function () { },
			complete: function () {
			}
		},
		language: spanish,
		columnDefs: [{
			"className": "dt-center",
			"targets": 0
		}],
		order: [
			[1, "desc"]
		]
	});
}