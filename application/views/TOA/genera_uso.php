<div class="accordion">
	<?= form_open($url_form,'id="frm_param" class="form-inline"'); ?>
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						{_consumo_parametros_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_param">
			<div class="accordion-inner">

				{validation_errors}

				<div class="col-md-3">
				</div>

				<div class="col-md-4 form_group <?= form_has_error_class('mes') ?>">
					<label class="cosntrol-label">{_controles_tecnicos_meses_}</label>
					<?= form_month('mes', request('mes'), 'class="form-control"'); ?>
				</div>

				<div class="col-md-4">
					<button type="submit" class="btn btn-primary">
						<span class="fa fa-search"></span>
						{_consumo_btn_reporte_}
					</button>
				</div>

		</div>
	</div>
	<?= form_close(); ?>
</div>

<div class="content-module-main">
	<div class="row">
		<div id="chart_uso" style="width: 100%; height: 600px;"></div>
	</div>
</div> <!-- fin content-module-main -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
	var data_uso = google.visualization.arrayToDataTable(<?= $data_uso; ?>);
	var options_uso = {
		title : 'Uso TOA',
		legend: {position: 'none'},
		vAxis: {title: 'Uso TOA'},
		hAxis: {title: 'Q Peticiones'},
		sizeAxis: {minValue: 0},
	};
	var chart_uso = new google.visualization.{tipo_chart}(document.getElementById('chart_uso'));
	chart_uso.draw(data_uso, options_uso);

}

</script>
