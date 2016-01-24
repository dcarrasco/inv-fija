<?php echo form_open('','id="frm_ppal"'); ?>
<div class="panel panel-default hidden-print">
	<div class="panel-heading">
		<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
			<span class="glyphicon glyphicon-filter"></span>
			{_stock_analisis_params_}
		</a>
	</div>

	<div class="panel-collapse collapse in" id="form_param">
		<div class="panel-body">

			{validation_errors}

			<div class="col-md-4">
				<div class="form-group">
					<label>
						{_stock_analisis_label_series_}
					</label>
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
					<label>
						{_stock_analisis_label_reports_}
					</label>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_mov', 'show', set_checkbox('show_mov', 'show', TRUE)); ?>
							{_stock_analisis_check_movimientos_}
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('ult_mov', 'show', set_checkbox('ult_mov', 'show', FALSE)); ?>
							{_stock_analisis_check_filtrar_ultmov_}
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_despachos', 'show', set_checkbox('show_despachos', 'show', FALSE)); ?>
							{_stock_analisis_check_despachos_}
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_stock_sap', 'show', set_checkbox('show_stock_sap', 'show', FALSE)); ?>
							{_stock_analisis_check_stock_sap_}
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_stock_scl', 'show', set_checkbox('show_stock_scl', 'show', FALSE)); ?>
							{_stock_analisis_check_stock_scl_}
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_trafico', 'show', set_checkbox('show_trafico', 'show', FALSE)); ?>
							{_stock_analisis_check_trafico_}
							(<?php echo anchor($this->router->class.'/trafico_por_mes', '{_stock_analisis_link_detalle_trafico_}'); ?>)
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php echo form_checkbox('show_gdth', 'show', set_checkbox('show_gdth', 'show', FALSE)); ?>
							{_stock_analisis_check_gestor_}
						</label>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="pull-right">
					<button type="submit" name="submit" class="btn btn-primary" id="boton-submit">
						<span class="glyphicon glyphicon-list-alt"></span>
						{_stock_analisis_button_query_}
					</button>
					<button name="excel" class="btn btn-default" id="boton-reset">
						<span class="glyphicon glyphicon-refresh"></span>
						{_stock_analisis_button_reset_}
					</button>
				</div>
			</div>

		</div>
	</div>
</div>
<?php echo form_close(); ?>


<?php if (set_value('show_mov')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_movimientos" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_movimientos_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_movimientos">
		<div class="accordion-inner" style="overflow: auto">
			<?php echo $hist ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (set_value('show_despachos')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_despachos" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_despachos_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_despachos">
		<div class="accordion-inner" style="overflow: auto">
			<?php echo $desp ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (set_value('show_stock_sap')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_stock_sap" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_stock_sap_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_stock_sap">
		<div class="accordion-inner" style="overflow: auto">
			<?php echo $stock ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (set_value('show_stock_scl')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_stock_scl" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_stock_scl_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_stock_scl">
		<div class="accordion-inner" style="overflow: auto">
			<?php echo $stock_scl ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (set_value('show_trafico')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_trafico" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_trafico_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_trafico">
		<div class="accordion-inner" style="overflow: auto">
			<?php echo $trafico ?>
		</div>
	</div>
</div>
<?php endif; ?>


<?php if (set_value('show_gdth')): ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_gdth" class="accordion-toggle" data-toggle="collapse">
			{_stock_analisis_title_gestor_}
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_gdth">
		<div class="accordion-inner" style="overflow: auto">
			<table class="table table-bordered table-striped table-hover table-condensed reporte" style="white-space:nowrap;">
			<?php foreach($log_gdth as $serie_gdth): ?>
				<tr>
					<th>id</th>
					<th>fecha</th>
					<th>serie deco</th>
					<th>serie tarjeta</th>
					<th>peticion</th>
					<th>estado</th>
					<th>tipo operacion cas</th>
					<th>telefono</th>
					<th>rut</th>
					<th>nombre cliente</th>
				</th>
			<?php foreach($serie_gdth as $reg_log_gdth): ?>
				<tr>
					<td><?php echo $reg_log_gdth['id_log_deco_tarjeta'] ?></td>
					<td><?php echo $reg_log_gdth['fecha_log'] ?></td>
					<td><?php echo $reg_log_gdth['serie_deco'] ?></td>
					<td><?php echo $reg_log_gdth['serie_tarjeta'] ?></td>
					<td><?php echo $reg_log_gdth['peticion'] ?></td>
					<td><?php echo $reg_log_gdth['estado'] ?></td>
					<td><?php echo $reg_log_gdth['tipo_operacion_cas'] ?></td>
					<td><?php echo $reg_log_gdth['telefono'] ?></td>
					<td><?php echo $reg_log_gdth['rut'] ?></td>
					<td><?php echo $reg_log_gdth['nombre'] ?></td>
				</tr>
			<?php endforeach; ?>
			<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>


<script type="text/javascript">
	$(document).ready(function() {
		if ($("#series").val() != "")
		{
			//$("#form_param").collapse();
		}

		$("#boton-reset").click(function(event) {
			//event.preventDefault();
			$("#series").val("");
			$("#series").focus();
		})

	});
</script>
