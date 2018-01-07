<div class="accordion">
	<?= form_open('','method="get" id="frm_param" class="form-inline"'); ?>
	<?= form_hidden('nuevo_reporte', ''); ?>
	<div class="panel panel-default">


		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
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
					<div class="col-xs-4 form_group <?= form_has_error_class('empresa') ?>">
						<label class="control-label">{_controles_tecnicos_empresas_}</label>
						<?= form_dropdown('empresa', $combo_empresas, request('empresa'), 'class="form-control"'); ?>
					</div>

					<div class="col-xs-4 form_group <?= form_has_error_class('mes') ?>">
						<label class="control-label">{_controles_tecnicos_meses_}</label>
						<?= form_month('mes', request('mes'), 'class="form-control"'); ?>
					</div>

					<div class="col-xs-4">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">
								<span class="fa fa-search"></span>
								{_consumo_btn_reporte_}
							</button>
							<button type="button" class="btn btn-default" value="nuevo_reporte" id="nuevo-reporte">
								<span class="fa fa-file-text-o"></span>
								{_consumo_btn_nuevo_reporte_}
							</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?= form_close(); ?>
</div>

{genera_data}

<?php if ($form_validated): ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#peticiones-totales" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_peticiones_total_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="peticiones-totales">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1">
						<div id="chart_peticiones_cant" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-2">
						<div id="proy_peticiones_cant" style="width: 100%; height: 180px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#peticiones-totales" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_peticiones_total_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="peticiones-totales">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1">
						<div id="chart_peticiones_monto" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-2">
						<div id="proy_peticiones_monto" style="width: 100%; height: 180px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#peticiones-asignacion" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_peticiones_tipo_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="peticiones-asignacion">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1">
						<div id="chart_peticiones_instala" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-2">
						<div id="usage_peticiones_instala" style="width: 100%; height: 180px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#peticiones-asignacion" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_peticiones_tipo_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="peticiones-asignacion">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1">
						<div id="chart_peticiones_repara" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-2">
						<div id="usage_peticiones_repara" style="width: 100%; height: 180px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#tecnicos" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_tecnicos_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="tecnicos">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1">
						<div id="chart_tecnicos" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-2">
						<div id="usage_tecnicos" style="width: 100%; height: 180px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-8">
					<a href="#stock" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_stock_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="stock">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-xs-6">
						<div id="chart_stock" style="width: 100%; height: 180px;"></div>
					</div>
					<div class="col-xs-6">
						<div id="chart_stock_tecnicos" style="width: 100%; height: 180px;"></div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
	var data_cant = google.visualization.arrayToDataTable(<?= $cant_peticiones_empresa; ?>);
	var options_cant = {
		title : 'Cantidad Peticiones',
		legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontName: 'Arial', fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Arial', fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_cant = new google.visualization.ComboChart(document.getElementById('chart_peticiones_cant'));
	chart_cant.draw(data_cant, options_cant);


	var data_monto = google.visualization.arrayToDataTable(<?= $monto_peticiones_empresa; ?>);
	var options_monto = {
		title : 'Monto Peticiones',
		legend: {position: 'none'},
		vAxis: {title: 'Monto', textStyle: {fontSize: 10}, format: '###,###'},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#990099'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_monto = new google.visualization.ComboChart(document.getElementById('chart_peticiones_monto'));
	chart_monto.draw(data_monto, options_monto);


	var data_cant = google.visualization.arrayToDataTable(<?= $cant_peticiones_instala; ?>);
	var options_cant = {
		title : 'Cantidad Peticiones Instalacion',
		//legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_cant = new google.visualization.ComboChart(document.getElementById('chart_peticiones_instala'));
	chart_cant.draw(data_cant, options_cant);


	var data_cant = google.visualization.arrayToDataTable(<?= $cant_peticiones_repara; ?>);
	var options_cant = {
		title : 'Cantidad Peticiones Reparacion',
		//legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_cant = new google.visualization.ComboChart(document.getElementById('chart_peticiones_repara'));
	chart_cant.draw(data_cant, options_cant);


	var data_tecnicos = google.visualization.arrayToDataTable(<?= $cant_tecnicos_empresa; ?>);
	var options_tecnicos = {
		title : 'Cantidad Tecnicos',
		//legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_tecnicos = new google.visualization.ComboChart(document.getElementById('chart_tecnicos'));
	chart_tecnicos.draw(data_tecnicos, options_tecnicos);


	var data_stock = google.visualization.arrayToDataTable(<?= $stock_empresa; ?>);
	var options_stock = {
		title : 'Stock Almacenes',
		legend: {position: 'none'},
		vAxis: {title: 'Monto [MM$]', textStyle: {fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#990099'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_stock = new google.visualization.ComboChart(document.getElementById('chart_stock'));
	chart_stock.draw(data_stock, options_stock);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $stock_tecnicos_empresa; ?>);
	var options_stock_tecnicos = {
		title : 'Stock Tecnicos',
		legend: {position: 'none'},
		vAxis: {title: 'Monto [MM$]', textStyle: {fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontSize: 9}},
		seriesType: 'bars',
		series: {0: {color: '#990099'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_stock_tecnicos = new google.visualization.ComboChart(document.getElementById('chart_stock_tecnicos'));
	chart_stock_tecnicos.draw(data_stock_tecnicos, options_stock_tecnicos);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $usage_peticiones_instala; ?>);
	var options_stock_tecnicos = {
		title : 'Uso Instala',
		pieHole: 0.4,
		legend: {position: 'none'},
		slices: {0: {color: '#FF6633'}, 1: {color: '#EEE'}}
	};
	var chart_usage_instala = new google.visualization.PieChart(document.getElementById('usage_peticiones_instala'));
	chart_usage_instala.draw(data_stock_tecnicos, options_stock_tecnicos);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $usage_peticiones_repara; ?>);
	var options_stock_tecnicos = {
		title : 'Uso Repara',
		pieHole: 0.4,
		legend: {position: 'none'},
		slices: {0: {color: '#FF6633'}, 1: {color: '#EEE'}}
	};
	var chart_usage_repara = new google.visualization.PieChart(document.getElementById('usage_peticiones_repara'));
	chart_usage_repara.draw(data_stock_tecnicos, options_stock_tecnicos);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $usage_cant_tecnicos; ?>);
	var options_stock_tecnicos = {
		title : 'Uso Tecnicos',
		pieHole: 0.4,
		legend: {position: 'none'},
		slices: {0: {color: '#FF6633'}, 1: {color: '#EEE'}}
	};
	var chart_usage_tecnicos = new google.visualization.PieChart(document.getElementById('usage_tecnicos'));
	chart_usage_tecnicos.draw(data_stock_tecnicos, options_stock_tecnicos);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $cant_peticiones_empresa_proy; ?>);
	var options_stock_tecnicos = {
		title : 'Proyeccion Q Peticiones: <?= $proy_q_pet; ?>',
		pieHole: 0.4,
		legend: {position: 'none'},
		slices: {0: {color: '#FF6633'}, 1: {color: '#EEE'}}
	};
	var chart_usage_tecnicos = new google.visualization.PieChart(document.getElementById('proy_peticiones_cant'));
	chart_usage_tecnicos.draw(data_stock_tecnicos, options_stock_tecnicos);


	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?= $monto_peticiones_empresa_proy; ?>);
	var options_stock_tecnicos = {
		title : 'Proyeccion Monto Peticiones: $ <?= $proy_monto_pet; ?>',
		pieHole: 0.4,
		legend: {position: 'none'},
		slices: {0: {color: '#990099'}, 1: {color: '#EEE'}}
	};
	var chart_usage_tecnicos = new google.visualization.PieChart(document.getElementById('proy_peticiones_monto'));
	chart_usage_tecnicos.draw(data_stock_tecnicos, options_stock_tecnicos);

}

</script>
<?php endif ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#nuevo-reporte').click(function (event) {
		event.preventDefault();
		$('#frm_param input[name="nuevo_reporte"]').val('nuevo_reporte');
		$('#frm_param').submit();
	});
});
</script>
