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
							<label>{_controles_tecnicos_filtro_trx_}</label>
							<?php echo form_dropdown('filtro_trx', $combo_filtro_trx, set_value('filtro_trx'), 'class="form-control"'); ?>
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
<?php if ($control): ?>
	<?php $num_lin = 0; $tot_col = array();?>

	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($control as $tip_mat_material => $datos): ?>
		<?php if ($num_lin == 0): ?>
			<thead>
			<tr class="active">
				<th></th>
				<th>Tipo</th>
				<th>Material</th>
				<th>unidad</th>
				<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
					<th class="text-center">
						<?php echo $this->toa_model->dias_de_la_semana[date('w', strtotime($anomes.$dia_act))]; ?>
						<?php echo $dia_act; ?>
						<?php $tot_col[$dia_act] = 0; ?>
					</th>
				<?php endforeach; ?>
				<th>Tot Mes</th>
			</tr>
			</thead>
			<tbody>
		<?php endif; ?>
		<tr>
			<td class="text-muted"><?php echo $num_lin + 1; ?></td>
			<td><?php echo $datos['tip_material'] ?></td>
			<td style="white-space: nowrap;"><?php echo $datos['material']; ?> - <?php echo $datos['descripcion']; ?></td>
			<td><?php echo $datos['unidad'] ?></td>
				<?php $tot_lin = 0; ?>
				<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
					<td class="text-center <?php echo $cant_act ? 'info' : ''; ?>">
						<?php echo $cant_act ? anchor($url_detalle_dia.'/'.$anomes.$dia_act.'/'.$anomes.$dia_act.'/'.$datos['material'], fmt_cantidad($cant_act)) : ''; ?>
						<?php $tot_lin += $cant_act; $tot_col[$dia_act] += $cant_act; ?>
					</td>
				<?php endforeach; ?>
				<th class="text-center"><?php echo fmt_cantidad($tot_lin); ?></th>
		</tr>
		<?php $num_lin += 1; ?>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="active">
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<?php $tot_lin = 0; ?>
			<?php foreach ($tot_col as $dia_act => $total): ?>
				<th class="text-center"><?php echo fmt_cantidad($total); ?><?php $tot_lin += $total ?></th>
			<?php endforeach; ?>
			<th class="text-center"><?php echo fmt_cantidad($tot_lin); ?></th>
		</tr>
	</tfoot>
</table>

<?php endif ?>
</div> <!-- fin content-module-main -->
