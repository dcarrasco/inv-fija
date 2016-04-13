<div class="accordion">
	<?php echo form_open('','id="frm_param" class="form-inline"'); ?>
	<?php echo form_hidden('order_by', set_value('order_by','')); ?>
	<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
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
					<div class="col-md-5">
						<div class="form_group">
							<label>{_controles_tecnicos_empresas_}</label>
							<?php echo form_dropdown('empresa', $combo_empresas, set_value('empresa'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-5">
						<div class="form_group">
							<label>{_controles_tecnicos_meses_}</label>
							<?php echo form_dropdown('mes', $combo_meses, set_value('mes'), 'class="form-control"'); ?>
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
<?php $i = 0; ?>
<?php if ($control): ?>
	<table class="table table-bordered reporte">
	<?php foreach ($control as $id_tecnico => $datos): ?>
		<?php if ($i == 0): ?>
			<tr>
				<th></th>
				<th>T&eacute;cnico</th>
				<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
					<th class="text-center"><?php echo $dia_act; ?></th>
				<?php endforeach; ?>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="text-muted"><?php echo $i+1; ?></td>
			<td><?php echo $id_tecnico; ?> - <?php echo $datos['nombre']; ?></td>
				<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
					<td class="text-center <?php echo $cant_act ? 'success' : ''; ?>">
						<?php echo $cant_act ? anchor('toa_consumos/ver_peticiones/tecnicos/'.$anomes.$dia_act.'/'.$id_tecnico, $cant_act) : ''; ?>
					</td>
				<?php endforeach; ?>

		</tr>
		<?php $i += 1; ?>
	<?php endforeach; ?>
</table>
<?php endif ?>

</div> <!-- fin content-module-main -->