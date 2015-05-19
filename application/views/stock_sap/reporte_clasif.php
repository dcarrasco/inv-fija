<?php echo form_open(); ?>
<div class="accordion hidden-print">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						<?php echo $this->lang->line('stock_sap_panel_params'); ?>
					</a>
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">

				<div class="col-md-4">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('stock_sap_label_dates'); ?>
						</label>
						<div id="show_fecha_ultimodia">
							<?php echo form_dropdown('operacion', $combo_operacion, $tipo_op,'id="select_operacion" class="form-control"'); ?>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('stock_sap_label_dates'); ?>
						</label>
						<div id="show_fecha_ultimodia">
							<?php echo form_dropdown('fecha', $combo_fechas, $fecha,'id="select_fecha" class="form-control"'); ?>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="pull-right">
						<button type="submit" id="btn_submit" name="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-list-alt"></span>
							<?php echo $this->lang->line('stock_sap_button_report'); ?>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>
<?php echo form_close(); ?>

<table class="table table-striped table-hover table-condensed reporte">
	<thead>
		<tr>
			<th>tipo op</th>
			<th>fecha</th>
			<th>clasificacion</th>
			<th>tipo</th>
			<th class="text-right">monto</th>
		</tr>
	</thead>

	<tbody>
		<?php $total = 0; ?>
		<?php if (count($reporte) > 0): ?>
		<?php foreach ($reporte as $lin): ?>
		<?php $total += $lin['monto']; ?>
		<tr>
			<td><?php echo $lin['tipo_op'] ?></td>
			<td><?php echo $lin['fecha_stock'] ?></td>
			<td><?php echo $lin['clasificacion'] ?></td>
			<td><?php echo $lin['tipo'] ?></td>
			<td class="text-right"><?php echo fmt_monto($lin['monto']); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>

	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right"><?php echo fmt_monto($total); ?></th>
		</tr>
	</tfoot>
</table>

<div id="donutchart" style="width: 900px; height: 500px;"></div>

<script type="text/javascript">
$('#btn_submit').click(function (event) {
	event.preventDefault();
	operacion = $('#select_operacion').val();
	fecha     = $('#select_fecha').val();
	window.location.href = '<?php echo $url_reporte; ?>' + '/' + operacion + '/' + fecha;
});

$('#select_operacion').change(function() {
	operacion = $('#select_operacion').val();
	window.location.href = '<?php echo $url_reporte; ?>' + '/' + operacion;
});
</script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
	var data = google.visualization.arrayToDataTable(<?php echo $reporte_js; ?>);

	var options = {
	title: 'Stock <?php echo $tipo_op; ?>',
	pieHole: 0.6,
	slices: <?php echo $js_slices; ?>,
	};

	var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
	chart.draw(data, options);
}
</script>