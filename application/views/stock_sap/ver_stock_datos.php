<?php $totales = array(); ?>
<?php $campos  = array(); ?>
<?php //$campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
<?php $campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad'); ?>
<?php $campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>

<table id='stock' class="table table-bordered table-striped table-hover table-condensed reporte">
	<?php foreach($stock as $key_reg => $reg): ?>
		<?php // ********************************************************* ?>
		<?php // Imprime encabezados                                       ?>
		<?php // ********************************************************* ?>
		<?php if ($key_reg == 0): ?>
			<thead>
				<tr>
				<?php foreach($reg as $key => $val): ?>
					<?php if (substr($key, 0, 4) != 'VAL_'):  ?>
						<?php if (in_array($key, $campos_sumables)): ?>
							<th class="text-right">
								<span data-cantidad="<?php echo $key; ?>" data-monto="<?php echo 'VAL_'.$key; ?>">
									<?php echo $key; ?>
								</span>
							</th>
						<?php else: ?>
							<th><?php echo str_replace('_', ' ', $key); ?></th>
						<?php endif; ?>
						<?php array_push($campos, $key); ?>
					<?php endif; ?>
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
				<?php if (substr($key, 0, 4) != 'VAL_'):  ?>
					<?php if (in_array($key, $campos_sumables)): ?>
						<?php $str_url      = base_url(
												'stock_sap/detalle_series/' .
												(array_key_exists('centro', $reg) ? $reg['centro'] : '_') . '/' .
												(array_key_exists('cod_almacen', $reg) ? $reg['cod_almacen'] : '_') . '/' .
												(array_key_exists('cod_articulo', $reg) ? $reg['cod_articulo'] : '_') . '/' .
												(array_key_exists('lote', $reg) ? $reg['lote'] : '_')
											); ?>
						<td class="text-right">
							<a href="<?php echo $str_url; ?>">
								<span data-cantidad="<?php echo fmt_cantidad($val); ?>" data-monto="<?php echo fmt_monto($reg['VAL_'.$key]); ?>">
									<?php echo fmt_cantidad($val); ?>
								</span>
							</a>
						</td>
						<?php if (!array_key_exists($key, $totales)) $totales[$key] = 0; ?>
						<?php if (!array_key_exists('VAL_'.$key, $totales)) $totales['VAL_'.$key] = 0; ?>
						<?php $totales[$key] += $val; $totales['VAL_'.$key] += $reg['VAL_'.$key];?>
					<?php else: ?>
						<td><?php echo $val; ?></td>
					<?php endif; ?>
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
						<span data-cantidad="<?php echo fmt_cantidad($totales[$val]); ?>" data-monto="<?php echo fmt_monto($totales['VAL_'.$val]); ?>">
							<?php echo fmt_cantidad($totales[$val]); ?>
						</span>
					</th>
				<?php else: ?>
					<th></th>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	</tfoot>
</table>


<div class="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<a href="#panel_graficos" class="accordion-toggle" data-toggle="collapse">Ver grafico</a>
		</div>
		<div class="panel-body collapse" id="panel_graficos">
			<div class="row">
				<div class="col-md-4">
					<div>
						<strong>Mostrar tipo de material</strong>
					</div>
					<div>
						<label class="checkbox-inline">
							<?php echo form_radio('sel_graph_tipo', 'equipos', set_radio('sel_graph_tipo', 'equipos'), 'id="sel_graph_tipo_equipos"'); ?>
							Equipos
						</label>
						<label class="checkbox-inline">
							<?php echo form_radio('sel_graph_tipo', 'simcard', set_radio('sel_graph_tipo', 'simcard'), 'id="sel_graph_tipo_simcard"'); ?>
							Simcard
						</label>
						<label class="checkbox-inline">
							<?php echo form_radio('sel_graph_tipo', 'otros', set_radio('sel_graph_tipo', 'otros'), 'id="sel_graph_tipo_otros"'); ?>
							Otros
						</label>
					</div>
					<div>
						<strong>Mostrar tipo de dato</strong>
					</div>
					<div>
						<label class="checkbox-inline">
							<?php echo form_radio('sel_graph_valor', 'cantidad', set_radio('sel_graph_valor','cantidad'), 'id="sel_graph_valor_cantidad"'); ?>
							Cantidad
						</label>
						<label class="checkbox-inline">
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
<script type="text/javascript" src="<?php echo base_url(); ?>js/view_stock_datos.js"></script>

<script language="javascript">
	var data_grafico = {
		q_equipos: <?php echo $datos_grafico['serie_q_equipos']; ?>,
		v_equipos: <?php echo $datos_grafico['serie_v_equipos']; ?>,
		q_simcard: <?php echo $datos_grafico['serie_q_simcard']; ?>,
		v_simcard: <?php echo $datos_grafico['serie_v_simcard']; ?>,
		q_otros: <?php echo $datos_grafico['serie_q_otros']; ?>,
		v_otros: <?php echo $datos_grafico['serie_v_otros']; ?>,
		x_label: <?php echo $datos_grafico['str_eje_x']; ?>,
		series_label: <?php echo $datos_grafico['str_label_series']; ?>,
	}

	$(document).ready(function(){
		$('input:radio[name=sel_graph_tipo], input:radio[name=sel_graph_valor]').click(function (event) {
			render_grafico(data_grafico, $('input:radio[name=sel_graph_tipo]:checked').val(), $('input:radio[name=sel_graph_valor]:checked').val());
		});

	});
</script>