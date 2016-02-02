<?php echo form_open('','id="frm_ppal"'); ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
			<span class="fa fa-filter"></span>
			{_stock_gestor_panel_params_}
		</a>
	</div>

	<div class="panel-body collapse in" id="form_param">
		<div class="accordion-inner">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<div class="radio-inline">
							<label>
								<?php echo form_radio('set_serie', 'serie_deco', set_radio('set_serie','serie_deco', TRUE));?>
								{_stock_gestor_radio_deco_}
							</label>
						</div>
						<div class="radio-inline">
							<label>
								<?php echo form_radio('set_serie', 'rut', set_radio('set_serie','rut'))?>
								{_stock_gestor_radio_cliente_}
							</label>
						</div>
						<?php echo form_textarea(array(
								'id' => 'series',
								'name' => 'series',
								'rows' => '10',
								'cols' => '30',
								'value' => set_value('series'),
								'class' => 'form-control',
							)); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>{_stock_gestor_label_report_}</label>

						<div>
							<div class="radio-inline">
								<label>
									<?php echo form_radio('tipo_reporte', 'log', set_radio('tipo_reporte', 'log', TRUE)); ?>
									{_stock_gestor_radio_log_complete_}
								</label>
							</div>
							<div class="radio-inline">
								<label>
									<?php echo form_radio('tipo_reporte', 'ultimo', set_radio('tipo_reporte', 'ultimo')); ?>
									{_stock_gestor_radio_log_ultdeco_}
								</label>
							</div>
						</div>

						<div class="checkbox">
							<label>
								<?php echo form_checkbox('ult_mov', 'show', set_value('ult_mov')); ?>
								{_stock_gestor_check_filter_last_mov_}
							</label>
						</div>

						<label>{_stock_gestor_label_typeop_}</label>

						<div>
							<div class="checkbox-inline">
								<label>
									<?php echo form_checkbox('tipo_op_alta', 'alta', set_value('tipo_op_alta', 'alta')); ?>
									{_stock_gestor_check_alta_}
								</label>
							</div>
							<div class="checkbox-inline">
								<label>
									<?php echo form_checkbox('tipo_op_baja', 'baja', set_value('tipo_op_baja', 'baja')); ?>
									{_stock_gestor_check_baja_}
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group pull-right">
						<button type="submit" name="submit" class="btn btn-primary" id="boton-submit">
							<span class="fa fa-search"></span>
							{_stock_gestor_button_report_}
						</button>
						<button name="excel" class="btn btn-default" id="boton-reset">
							<span class="fa fa-refresh"></span>
							{_stock_gestor_button_reset_}
						</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_movimientos" class="accordion-toggle" data-toggle="collapse">
			{_stock_gestor_panel_log_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_movimientos">
		<div class="accordion-inner" style="overflow: auto">
			<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
			<?php foreach($log as $log_serie): ?>
				<tr>
					<th>id</th>
					<th>fecha</th>
					<th>serie deco</th>
					<th>serie tarjeta</th>
					<th>peticion</th>
					<th>estado</th>
					<th>tipo operacion cas</th>
					<th>telefono</th>
					<th>RUT</th>
					<th>Nombre cliente</th>
				</tr>
			<?php foreach($log_serie as $reg_log): ?>
				<tr>
					<td><?php echo $reg_log['id_log_deco_tarjeta'] ?></td>
					<td><?php echo $reg_log['fecha_log'] ?></td>
					<td><span class="serie"><?php echo $reg_log['serie_deco'] ?></span></td>
					<td><?php echo $reg_log['serie_tarjeta'] ?></td>
					<td><?php echo $reg_log['peticion'] ?></td>
					<td><?php echo $reg_log['estado'] ?></td>
					<td><?php echo $reg_log['tipo_operacion_cas'] ?></td>
					<td><?php echo $reg_log['telefono']?></td>
					<td><?php echo $reg_log['rut'] ?></td>
					<td><?php echo $reg_log['nombre'] ?></td>
				</tr>
			<?php endforeach; ?>
			<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

<?php echo form_open($this->router->class . '/historia', array('id' => 'frmHistoria')); ?>
<?php echo form_hidden('series'); ?>
<?php echo form_hidden('show_mov', 'show'); ?>
<?php echo form_close(); ?>

<script type="text/javascript">
	$(document).ready(function () {

	    $('span.serie').css('cursor', 'pointer');

		$('span.serie').click(function (event) {
			var serie = $(this).text();
			$('input[name="series"]').val(serie);
			$('#frmHistoria').submit();
		});

		if ($("#series").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#boton-reset").click(function(event) {
			//event.preventDefault();
			$("#series").val("");
			$("#series").focus();
		});

		$("table tr").hover(function() {
			$(this).addClass("highlight");
		}, function() {
			$(this).removeClass("highlight");
		});

		$("div.content-header").click(function() {
			$(this).next("div.mostrar-ocultar").slideToggle("fast");
			$(this).children("span.mostrar-ocultar").toggle();
		});

	});

</script>
