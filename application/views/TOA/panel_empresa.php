<div class="accordion">
	<?php echo form_open('','method="get" id="frm_param" class="form-inline"'); ?>
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
				<div class="row">
					<div class="col-md-4">
						<div class="form_group">
							<label>{_controles_tecnicos_empresas_}</label>
							<?php echo form_dropdown('empresa', $combo_empresas, $this->input->get('empresa'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form_group">
							<label>{_controles_tecnicos_meses_}</label>
							<?php echo form_month('mes', $this->input->get('mes'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">
								<span class="fa fa-search"></span>
								{_consumo_btn_reporte_}
							</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_peticiones" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_peticiones_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_peticiones">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-6">
						<div id="chart_peticiones_cant" style="width: 100%; height: 190px;"></div>
					</div>
					<div class="col-md-6">
						<div id="chart_peticiones_monto" style="width: 100%; height: 190px;"></div>
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
				<div class="col-md-8">
					<a href="#form_peticiones" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_tecnicos_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_peticiones">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-6">
						<div id="chart_tecnicos" style="width: 100%; height: 190px;"></div>
					</div>
					<div class="col-md-6">
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
				<div class="col-md-8">
					<a href="#form_peticiones" class="accordion-toggle" data-toggle="collapse">
						{_panel_title_stock_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_peticiones">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-6">
						<div id="chart_stock" style="width: 100%; height: 190px;"></div>
					</div>
					<div class="col-md-6">
						<div id="chart_stock_tecnicos" style="width: 100%; height: 190px;"></div>
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
	var data_cant = google.visualization.arrayToDataTable(<?php echo $cant_peticiones_empresa; ?>);
	var options_cant = {
		title : 'Cantidad Peticiones',
		//legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontName: 'Calibri', fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Calibri', fontSize: 8}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_cant = new google.visualization.ComboChart(document.getElementById('chart_peticiones_cant'));
	chart_cant.draw(data_cant, options_cant);


	var data_monto = google.visualization.arrayToDataTable(<?php echo $monto_peticiones_empresa; ?>);
	var options_monto = {
		title : 'Monto Peticiones',
		// legend: {position: 'none'},
		vAxis: {title: 'Monto', textStyle: {fontName: 'Calibri', fontSize: 10}, format: '###,###'},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Calibri', fontSize: 8}},
		seriesType: 'bars',
		series: {0: {color: '#990099'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_monto = new google.visualization.ComboChart(document.getElementById('chart_peticiones_monto'));
	chart_monto.draw(data_monto, options_monto);



	var data_tecnicos = google.visualization.arrayToDataTable(<?php echo $cant_tecnicos_empresa; ?>);
	var options_tecnicos = {
		title : 'Cantidad Tecnicos',
		legend: {position: 'none'},
		vAxis: {title: 'Cantidad', textStyle: {fontName: 'Calibri', fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Calibri', fontSize: 8}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_tecnicos = new google.visualization.ComboChart(document.getElementById('chart_tecnicos'));
	chart_tecnicos.draw(data_tecnicos, options_tecnicos);

	var data_stock = google.visualization.arrayToDataTable(<?php echo $stock_empresa; ?>);
	var options_stock = {
		title : 'Stock Almacenes',
		legend: {position: 'none'},
		vAxis: {title: 'Monto', textStyle: {fontName: 'Calibri', fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Calibri', fontSize: 8}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_stock = new google.visualization.ComboChart(document.getElementById('chart_stock'));
	chart_stock.draw(data_stock, options_stock);

	var data_stock_tecnicos = google.visualization.arrayToDataTable(<?php echo $stock_tecnicos_empresa; ?>);
	var options_stock_tecnicos = {
		title : 'Stock Tecnicos',
		legend: {position: 'none'},
		vAxis: {title: 'Monto', textStyle: {fontName: 'Calibri', fontSize: 10}},
		hAxis: {title: 'Dias', textStyle: {fontName: 'Calibri', fontSize: 8}},
		seriesType: 'bars',
		series: {0: {color: '#FF6633'}, 1: {type: 'line', color: '#00C6DA'}}
	};
	var chart_stock_tecnicos = new google.visualization.ComboChart(document.getElementById('chart_stock_tecnicos'));
	chart_stock_tecnicos.draw(data_stock_tecnicos, options_stock_tecnicos);

}

</script>
