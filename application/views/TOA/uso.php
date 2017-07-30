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
					<div class="col-md-4 form_group <?= form_has_error_class('mes') ?>">
						<label class="control-label">{_controles_tecnicos_meses_}</label>
						<?= form_month('mes', request('mes'), 'class="form-control" id="mes"') ?>
					</div>

					<div class="col-md-3 form_group <?= form_has_error_class('mostrar') ?>">
						<label class="control-label">{_uso_mostrar_}</label>
						<?= form_dropdown('mostrar', $combo_mostrar, request('mostrar'), 'class="form-control"') ?>
					</div>

					<div class="col-md-3 form_group <?= form_has_error_class('tipo_pet') ?>">
						<label class="control-label">{_uso_tipo_pet_}</label>
						<?= form_dropdown('tipo_pet', $combo_tipo_pet, request('tipo_pet'), 'class="form-control"') ?>
					</div>

					<div class="col-md-2 pull-right">
						<button type="submit" class="btn btn-primary">
							<span class="fa fa-search"></span> {_consumo_btn_reporte_}
						</button>
					</div>
				</div>

				<div class="row">
					<hr>
				</div>

				<div class="row">
					<div class="col-md-3 form_group <?= form_has_error_class('empresa') ?>">
						<label class="control-label">{_controles_tecnicos_empresas_}</label>
						<?= form_dropdown('empresa', $combo_empresa, request('empresa'), 'id="empresa" class="form-control"'); ?>
					</div>

					<div class="col-md-3 form_group <?= form_has_error_class('agencia') ?>">
						<label class="control-label">{_uso_label_agencia_}</label>
						<?= form_dropdown('agencia', $combo_agencia, request('agencia'), 'class="form-control" id="agencia"'); ?>
					</div>

					<div class="col-md-6 form_group <?= form_has_error_class('tecnico') ?>">
						<label class="control-label">{_uso_label_tecnico_}</label>
						<?= form_dropdown('tecnico', $combo_tecnico, request('tecnico'), 'class="form-control" id="tecnico"'); ?>
					</div>

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


<script type="text/javascript">
$(document).ready(function() {

	$('#mes').change(function() {
		actualizaAgenciaTecnico();
	});

	$('#empresa').change(function() {
		actualizaAgenciaTecnico();
	});

	function actualizaAgenciaTecnico() {
		var empresa = $('#empresa').val(),
			mes     = $('#mes').val();

		if (mes != '')
		{
			var url_datos = js_base_url + 'toa_uso/ajax_combo_agencia/' + mes + '/' + empresa;
			$.get(url_datos, function (data) {$('#agencia').html(data); });

			var url_datos = js_base_url + 'toa_uso/ajax_combo_tecnico/' + mes + '/' + empresa;
			$.get(url_datos, function (data) {$('#tecnico').html(data); });
		}

	}
});
</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
	var data={data_uso};
	var data_uso = google.visualization.arrayToDataTable(data);

	var max = data.length > 20 ? 5 : 40;
	var min = data.length > 20 ? 5 : 5;

	var options_uso = {
		title : 'Uso TOA ({mes})',
		legend: {position: 'top'},
		vAxis: {title: 'Uso TOA'},
		hAxis: {title: 'Q Peticiones'},
		sizeAxis: {minValue: 0, maxSize: max, minSize: min},
		bubble: {textStyle: {auraColor: 'none'}},
		series: {
			'OK':    {color: 'green'},
			'CASI':  {color: 'yellow'},
			'NO OK': {color: 'red'},
		}
	};
	var chart_uso = new google.visualization.{tipo_chart}(document.getElementById('chart_uso'));
	chart_uso.draw(data_uso, options_uso);

}

</script>
