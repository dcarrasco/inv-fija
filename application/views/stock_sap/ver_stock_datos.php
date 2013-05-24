	<div class="content-module-main">

		<?php $totales = array(); ?>
		<?php $campos  = array(); ?>
		<?php $campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
		<?php $campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
		<table class="reporte">
			<?php foreach($stock as $key_reg => $reg): ?>
				<?php // ********************************************************* ?>
				<?php // Imprime encabezados                                       ?>
				<?php // ********************************************************* ?>
				<?php if ($key_reg == 0): ?>
					<thead>
						<tr>
						<?php foreach($reg as $key => $val): ?>
							<th <?php echo (in_array($key, $campos_sumables) ? 'class="ar"' : '')?>><?php echo str_replace('_', ' ', $key); ?></th>
							<?php array_push($campos, $key); ?>
						<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
				<?php endif; ?>


				<?php // ********************************************************* ?>
				<?php // Imprime linea normal con datos                            ?>
				<?php // ********************************************************* ?>
				<tr>
					<?php foreach($reg as $key => $val): ?>
						<?php if (in_array($key, $campos_sumables)): ?>
							<td class="ar">
								<?php echo anchor('stock_sap/detalle_series/' .
														(array_key_exists('centro', $reg) ? $reg['centro'] : '_') . '/' .
														(array_key_exists('cod_almacen', $reg) ? $reg['cod_almacen'] : '_') . '/' .
														(array_key_exists('cod_articulo', $reg) ? $reg['cod_articulo'] : '_') . '/' .
														(array_key_exists('lote', $reg) ? $reg['lote'] : '_'),
													((in_array($key, $campos_montos)) ? '$ ' : '') . number_format($val,0,',','.')
												); ?>
							</td>
							<?php if (!array_key_exists($key, $totales)) $totales[$key] = 0; ?>
							<?php $totales[$key] += $val; ?>
						<?php else: ?>
							<td><?php echo ($val); ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>

			<?php // ********************************************************* ?>
			<?php // Imprime totales finales de la tabla                       ?>
			<?php // ********************************************************* ?>
			<tfoot>
				<tr>
					<?php foreach($campos as $val): ?>
						<?php if (in_array($val, $campos_sumables)): ?>
							<th class="ar">
									<?php if (in_array($val, $campos_montos)): ?> $ <?php endif; ?>
									<?php echo number_format($totales[$val],0,',','.'); ?>
							</th>
						<?php else: ?>
							<th></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</tfoot>

		</table>

	</div> <!-- fin content-module-main -->

	<div class="ac">
		<div id="chart-q" class="jqplot-target" style="width: 600px; height: 500px; position: relative;"></div>
	</div>

	<div class="ac">
		<div id="chart-v" class="jqplot-target" style="width: 600px; height: 500px; position: relative;"></div>
	</div>

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.canvasAxisLabelRenderer.min.js"></script>

</div> <!-- fin content-module -->
<script language="javascript">
$(document).ready(function(){
	function jq_grafico(div_id, datos, x_label, y_label, series_label, title)
	{
		return $.jqplot(div_id, datos, {
			title: title,
			// Tell the plot to stack the bars.
			stackSeries: true,
			captureRightClick: true,
			seriesDefaults:{
				renderer:$.jqplot.BarRenderer,
				rendererOptions: {
					// Put a 30 pixel margin between bars.
					barMargin: 30,
					// Highlight bars when mouse button pressed.
					// Disables default highlighting on mouse over.
					highlightMouseDown: true
				},
				pointLabels: {show: true}
			},
			series: series_label,
			axes: {
				xaxis: {
					renderer: $.jqplot.CategoryAxisRenderer,
					ticks: x_label
				},
				yaxis: {
					padMin: 0,
					min: 0,
					tickOptions: {formatString: "%'i"},
					label: y_label,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
			},
			legend: {
				show: true,
				location: 's',
				placement: 'outsideGrid'
			}
		});
	}

	var datos_q_equipos = <?php echo $serie_q_equipos; ?>;
	var datos_v_equipos = <?php echo $serie_v_equipos; ?>;
	var x_label = <?php echo $arr_graph_fechas; ?>;
	var series_label = <?php echo $arr_graph_label_series; ?>;

	plot3 = jq_grafico('chart-q', datos_q_equipos, x_label, 'Cantidad', series_label, 'Equipos');
	plot4 = jq_grafico('chart-v', datos_v_equipos, x_label, 'Monto (MM$)', series_label, 'Equipos');

});
</script>