<?php $totales = array(); ?>
<?php $campos  = array(); ?>
<?php $campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
<?php $campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
<table class="table table-bordered table-striped table-hover table-condensed">
	<?php foreach($stock as $key_reg => $reg): ?>
		<?php // ********************************************************* ?>
		<?php // Imprime encabezados                                       ?>
		<?php // ********************************************************* ?>
		<?php if ($key_reg == 0): ?>
			<thead>
				<tr>
				<?php foreach($reg as $key => $val): ?>
					<th <?php echo (in_array($key, $campos_sumables) ? 'class="text-right"' : '')?>><?php echo str_replace('_', ' ', $key); ?></th>
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
					<td class="text-right">
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
					<th class="text-right">
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


<div class="accordion">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a href="#panel_graficos" class="accordion-toggle" data-toggle="collapse">Ver grafico</a>
		</div>
		<div class="accordion-body collapse" id="panel_graficos">
			<div class="row">
				<div class="col-md-4">
					<div>
						<strong>Mostrar tipo de material</strong>
					</div>
					<div>
						<label class="checkbox inline">
							<?php echo form_radio('sel_graph_tipo', 'equipos', set_radio('sel_graph_tipo','equipos'), 'id="sel_graph_tipo_equipos"'); ?>
							Equipos
						</label>
						<label class="checkbox inline">
							<?php echo form_radio('sel_graph_tipo', 'simcard', set_radio('sel_graph_tipo','simcard'), 'id="sel_graph_tipo_simcard"'); ?>
							Simcard
						</label>
						<label class="checkbox inline">
							<?php echo form_radio('sel_graph_tipo', 'otros', set_radio('sel_graph_tipo','otros'), 'id="sel_graph_tipo_otros"'); ?>
							Otros
						</label>
					</div>
					<div>
						<strong>Mostrar tipo de dato</strong>
					</div>
					<div>
						<label class="checkbox inline">
							<?php echo form_radio('sel_graph_valor', 'cantidad', set_radio('sel_graph_valor','cantidad'), 'id="sel_graph_valor_cantidad"'); ?>
							Cantidad
						</label>
						<label class="checkbox inline">
							<?php echo form_radio('sel_graph_valor', 'monto', set_radio('sel_graph_valor','monto'), 'id="sel_graph_valor_monto"'); ?>
							Monto
						</label>
					</div>
				</div>
				<div class="col-md-8">
					<div style="width:600px; margin-left:auto; margin-right:auto;">
						<div id="chart" class="jqplot-target" style="width: 100%; height: 450px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jqplot/jqplot.canvasAxisLabelRenderer.min.js"></script>

</div> <!-- fin content-module -->
<script language="javascript">
$(document).ready(function(){
	jq_grafico = function(div_id, datos, x_label, y_label, series_label, title)
	{
		return $.jqplot(div_id, datos, {
			title: title,
			animate: true,
			animateReplot: true,
			stackSeries: true,
			captureRightClick: true,
			seriesDefaults:{
				renderer:$.jqplot.BarRenderer,
				rendererOptions: {
					highlightMouseOver: true,
					animation: {
						speed: 300
					}
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
	};




	var datos_q_equipos = <?php echo $serie_q_equipos; ?>;
	var datos_v_equipos = <?php echo $serie_v_equipos; ?>;
	var datos_q_simcard = <?php echo $serie_q_simcard; ?>;
	var datos_v_simcard = <?php echo $serie_v_simcard; ?>;
	var datos_q_otros   = <?php echo $serie_q_otros; ?>;
	var datos_v_otros   = <?php echo $serie_v_otros; ?>;
	var x_label = <?php echo $str_eje_x; ?>;
	var series_label = <?php echo $str_label_series; ?>;

	var plot;

	var render_grafico = function(tipo, datos)
	{
		var data;
		var tipo  = $('input:radio[name=sel_graph_tipo]:checked').val();
		var datos = $('input:radio[name=sel_graph_valor]:checked').val()
		var str_tipo = '';
		var str_dato = '';
		var str_ejey = '';

		str_dato = (datos == 'monto') ? 'Valor (MM$)' : 'Cantidad';

		if (tipo == 'simcard')
		{
			str_tipo = 'Simcard';
			data = (datos == 'monto') ? datos_v_simcard : datos_q_simcard;
		}
		else if (tipo == 'otros')
		{
			str_tipo = 'Otros';
			data = (datos == 'monto') ? datos_v_otros : datos_q_otros;
		}
		else
		{
			str_tipo = 'Equipos';
			data = (datos == 'monto') ? datos_v_equipos : datos_q_equipos;
		}

		if (plot !== undefined) plot.destroy();
		plot = jq_grafico('chart', data, x_label, str_dato, series_label, str_dato + ' ' + str_tipo);
	};

	$('input:radio[name=sel_graph_tipo]').click(function (event) {
		render_grafico();
	});

	$('input:radio[name=sel_graph_valor]').click(function (event) {
		render_grafico();
	});

	$('#btn_grafico').click(function (event) {
		render_grafico();
	});


});
</script>