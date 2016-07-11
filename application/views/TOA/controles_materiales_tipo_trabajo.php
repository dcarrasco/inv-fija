{validation_errors}

<div class="accordion hidden-print">
	<?php echo form_open('','id="frm_param"'); ?>
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
					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tecnicos_empresas_}</label>
							<?php echo form_dropdown('empresa', $combo_empresas, set_value('empresa'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form_group">
							<label>{_controles_tecnicos_meses_}</label>
							<?php echo form_month('mes', set_value('mes'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tipos_trabajo_}</label>
							<?php echo form_dropdown('tipo_trabajo', $combo_tipos_trabajo, set_value('tipo_trabajo'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form_group">
							<label>{_controles_tecnicos_dato_desplegar_}</label>
							<?php echo form_dropdown('dato', $combo_dato_desplegar, set_value('dato'), 'class="form-control"'); ?>
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

<div class="content-module-main">
<?php if ($materiales_tipos_trabajo): ?>
	<?php $num_lin= 0; $tot_col = array(); $count_col = array();?>
	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($materiales_tipos_trabajo as $referencia => $arr_referencia): ?>

		<?php if ($num_lin== 0): ?>
			<!-- ENCABEZADO TABLA REPORTE -->
			<thead>
			<tr>
				<th rowspan="2" class="active"></th>
				<th rowspan="2" class="active">Petici&oacute;n</th>
				<?php foreach ($arr_referencia as $material => $arr_material): ?>
					<th class="text-center <?php echo $arr_material['color'] ?>">
						<?php echo $arr_material['desc_tip_material']; ?>
					</th>
				<?php endforeach; ?>
				<th rowspan="2" class="active">Total</th>
			</tr>
			<tr class="active">
				<?php foreach ($arr_referencia as $material => $arr_material): ?>
					<th class="text-center ">
						<?php echo $material; ?> - <?php echo $arr_material['texto_material']; ?>
						<?php $tot_col[$material] = 0; $count_col[$material] = 0; ?>
					</th>
				<?php endforeach; ?>
				<?php $count_col['total'] = 0; ?>
			</tr>
			</thead>

			<!-- CUERPO TABLA REPORTE -->
			<tbody>
		<?php endif; ?>

		<tr>
			<td class="text-muted"><?php echo $num_lin+ 1; ?></td>
			<td style="white-space: nowrap;"><?php echo anchor($url_detalle_dia.'/'.$referencia, $referencia); ?></td>
			<?php $tot_lin = 0; ?>
			<?php foreach ($arr_referencia as $material => $arr_material): ?>
				<?php if ($arr_material['dato']): ?>
					<?php $class =  $arr_material['color'] ? $arr_material['color'] : 'info'; ?>
					<td class="text-center <?php echo $class; ?>">
						<?php echo fmt_cantidad($arr_material['dato']); ?>
					</td>
				<?php else: ?>
					<td></td>
				<?php endif ?>
				<?php $tot_lin += $arr_material['dato']; $tot_col[$material] += $arr_material['dato']; $count_col[$material] += $arr_material['dato'] ? 1 : 0;?>
			<?php endforeach; ?>
			<th class="text-center"><?php echo fmt_cantidad($tot_lin); ?></th>
			<?php $count_col['total'] += $tot_lin ? 1 : 0;?>
		</tr>
		<?php $num_lin+= 1; ?>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="active">
			<th></th>
			<th></th>
			<?php $tot_lin = 0; ?>
			<?php foreach ($tot_col as $material => $total): ?>
				<th class="text-center"><?php echo fmt_cantidad($total); ?><?php $tot_lin += $total ?></th>
			<?php endforeach; ?>
			<th class="text-center"><?php echo fmt_cantidad($tot_lin); ?></th>
		</tr>
		<tr class="active">
			<th></th>
			<th></th>
			<?php $tot_lin = 0; ?>
			<?php foreach ($count_col as $material => $count): ?>
				<?php $porcentaje = $count / $num_lin; ?>
				<th class="text-center <?php echo $this->toa_model->clase_cumplimiento_consumos($porcentaje) ?>"><?php echo fmt_cantidad(100*$porcentaje, 0, TRUE); ?>%</th>
			<?php endforeach; ?>
		</tr>
	</tfoot>
</table>
<?php endif ?>

</div> <!-- fin content-module-main -->

<script type="text/javascript">
$(document).ready(function () {

	$('#frm_param').submit(function(e) {
		e.preventDefault();
		var empresa      = $('select[name="empresa"]').val() ? $('select[name="empresa"]').val() : '_',
			mes          = $('input[name="mes"]').val() ? $('input[name="mes"]').val() : '_',
			tipo_trabajo = $('select[name="tipo_trabajo"]').val() ? $('select[name="tipo_trabajo"]').val() : '_',
			dato         = $('select[name="dato"]').val() ? $('select[name="dato"]').val() : '_';
		window.location.href = '<?php echo $submit_url ?>'+'/'+empresa+'/'+mes+'/'+tipo_trabajo+'/'+dato;
	});

});
</script>