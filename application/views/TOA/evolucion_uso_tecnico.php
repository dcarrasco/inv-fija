<div class="accordion">
	<?= form_open('','method="get" id="frm_param" class="form-inline"'); ?>
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

				<div class="row">

					<div class="col-md-4 form_group <?= form_has_error_class('tecnico') ?>">
						<label class="control-label">{_uso_label_tecnico_}</label>
						<?= form_input('tecnico', request('tecnico'), 'class="form-control" id="tecnico"'); ?>
					</div>

					<div class="col-md-4 form_group <?= form_has_error_class('tipo_pet') ?>">
						<label class="control-label">{_uso_tipo_pet_}</label>
						<?= form_dropdown('tipo_pet', $combo_tipo_pet, request('tipo_pet'), 'class="form-control"') ?>
					</div>

					<div class="col-md-4">
						<button type="submit" class="btn btn-primary">
							<span class="fa fa-search"></span> {_consumo_btn_reporte_}
						</button>
					</div>
				</div>

				<div class="row">
					<hr>
				</div>

				<div class="row">

				</div>


		</div>
	</div>
	<?= form_close(); ?>
</div>

<div class="content-module-main">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div id="chart_uso" style="width: 100%; height: 500px;"></div>
		</div>
	</div>
</div> <!-- fin content-module-main -->


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
	var data={data_uso};
	var data_uso = google.visualization.arrayToDataTable(data);
	var options_uso = {
		title : 'Evolucion Uso TOA (tecnico: {mes})',
		legend: {position: 'top'},
		vAxis: {title: 'Uso TOA'},
		hAxis: {title: 'Q Peticiones'},
	};
	var chart_uso = new google.charts.Bar(document.getElementById('chart_uso'));
	chart_uso.draw(data_uso, options_uso);

}

</script>
