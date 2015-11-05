<?php echo $tabla_stock ?>

<?php if ($datos_grafico): ?>
<div class="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<a href="#panel_graficos" class="accordion-toggle" data-toggle="collapse">
				<?php echo $this->lang->line('stock_sap_panel_graph'); ?>
			</a>
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

<?php endif ?>