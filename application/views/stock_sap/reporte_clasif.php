<?php echo form_open('', 'id="form_param"'); ?>
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

				<div class="col-md-3">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('stock_clasif_label_tipoop'); ?>
						</label>
						<?php echo form_dropdown('operacion', $combo_operacion, $tipo_op,'id="select_operacion" class="form-control"'); ?>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('stock_clasif_label_fechas'); ?>
						</label>
						<?php echo form_multiselect('fechas[]', $combo_fechas, $fechas,'size="10" id="select_fecha" class="form-control"'); ?>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<?php echo form_checkbox('sel_borrar', 'borrar', set_radio('sel_borrar','borrar')); ?>
								<?php echo $this->lang->line('stock_clasif_label_delete'); ?>
							</label>
						</div>
					</div>
				</div>

				<div class="col-md-3">
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

<?php $total = array(); ?>
<table class="table table-striped table-hover table-condensed reporte">
	<thead>
		<tr>
			<th>tipo op</th>
			<th>orden</th>
			<th>clasificacion</th>
			<th>tipo</th>
			<?php if (count($reporte['fechas'])>0): ?>
				<?php foreach ($reporte['fechas'] as $fec): ?>
					<th class="text-right"><?php echo $fec; ?></th>
					<?php $total[$fec] = 0; ?>
				<?php endforeach ?>
			<?php endif ?>
		</tr>
	</thead>

	<tbody>
		<?php if (count($reporte['datos']) > 0): ?>
		<?php foreach ($reporte['datos'] as $lin): ?>
		<tr>
			<td><?php echo $lin['tipo_op'] ?></td>
			<td><?php echo $lin['orden'] ?></td>
			<td><?php echo $lin['clasificacion'] ?></td>
			<td><?php echo $lin['tipo'] ?></td>
			<?php foreach ($reporte['fechas'] as $fec): ?>
				<td class="text-right"><?php echo fmt_monto($lin[$fec]); ?></td>
				<?php $total[$fec] += $lin[$fec]; ?>
			<?php endforeach ?>
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
			<?php foreach ($reporte['fechas'] as $fec): ?>
				<th class="text-right"><?php echo fmt_monto($total[$fec]); ?></th>
				<?php $total[$fec] = 0; ?>
			<?php endforeach ?>
		</tr>
	</tfoot>
</table>

<div id="donutchart" style="width: 900px; height: 500px;"></div>

<script type="text/javascript">
$('#select_operacion').change(function() {
	tipo_op = $('#select_operacion').val();
	var url_datos = js_base_url + 'stock_sap/ajax_fechas/' + tipo_op;
	$.get(url_datos, function (data) {$('#select_fecha').html(data); });
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