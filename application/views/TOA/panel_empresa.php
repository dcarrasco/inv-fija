<div class="accordion">
	<?php echo form_open('','method="get" id="frm_param" class="form-inline"'); ?>
	<?php echo form_hidden('order_by', set_value('order_by','')); ?>
	<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
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
					<script type="text/javascript">
						google.charts.load('current', {'packages':['corechart']});
						google.charts.setOnLoadCallback(drawPeticiones);

						function drawPeticiones() {
							// Some raw data (not necessarily accurate)
							var data_cant = google.visualization.arrayToDataTable(<?php echo $cant_peticiones_empresa; ?>);
							var data_monto = google.visualization.arrayToDataTable(<?php echo $monto_peticiones_empresa; ?>);

							var options_cant = {
							title : 'Cantidad Peticiones',
							legend: {position: 'none'},
							vAxis: {title: 'Peticiones'},
							hAxis: {title: 'Dias'},
							seriesType: 'bars',
							series: {0: {color: '#FF6633'}}
							};

							var options_monto = {
							title : 'Monto',
							legend: {position: 'none'},
							vAxis: {title: 'Monto'},
							hAxis: {title: 'Dias'},
							seriesType: 'bars',
							series: {0: {color: '#990099'}}
							};

							var chart_cant = new google.visualization.ComboChart(document.getElementById('chart_peticiones_cant'));
							chart_cant.draw(data_cant, options_cant);

							var chart_monto = new google.visualization.ComboChart(document.getElementById('chart_peticiones_monto'));
							chart_monto.draw(data_monto, options_monto);
						}
					</script>

				</div>
			</div>
		</div>
	</div>
</div>

