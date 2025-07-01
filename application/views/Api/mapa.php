<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="content-header">
				<div class="container-fluid"></div>
			</div>
			<div class="row">
				<section class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<div id="map" style="height: 600px;"></div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</section>
</div>

<link rel="stylesheet" href="../../../assets/plugins/leaflet/leaflet.css" />
<script src="../../../assets/plugins/leaflet/leaflet.js"></script>

<script>
	var markers = [<?=$ubicacion;?>];
	var zoom = 12;
	var map = L.map('map').setView([-12.0732, -77.0070], zoom);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
	for (var i = 0; i < markers.length; i++) {
		marker = new L.marker([markers[i][1], markers[i][2]])
			.bindPopup(markers[i][0])
			.addTo(map);
	}
</script>