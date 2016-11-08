<div class="accordion hidden-print">
	<?php echo form_open('','method="get" id="frm_param"'); ?>
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

				{validation_errors}

				<div class="row">
					<div class="col-md-3 form_group <?php echo form_error('empresa') ? 'has-error' : '' ?>">
						<label class="control-label">{_controles_tecnicos_empresas_}</label>
						<?php echo form_dropdown('empresa', $combo_empresas, set_value('empresa'), 'class="form-control"'); ?>
					</div>

					<div class="col-md-3 form_group <?php echo form_error('mes') ? 'has-error' : '' ?>">
						<label class="control-label">{_controles_tecnicos_meses_}</label>
						<?php echo form_month('mes', set_value('mes'), 'class="form-control"'); ?>
					</div>

					<div class="col-md-3 form_group <?php echo form_error('dato') ? 'has-error' : '' ?>">
						<label class="control-label">{_controles_tecnicos_dato_desplegar_}</label>
						<?php echo form_dropdown('dato', $combo_dato_desplegar, set_value('dato'), 'class="form-control"'); ?>
					</div>

					<div class="col-md-3">
						<button type="submit" class="pull-right btn btn-primary">
							<span class="fa fa-search"></span>
							{_consumo_btn_reporte_}
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>

<div class="content-module-main">
<?php if ($stock_almacenes): ?>
	<?php $num_lin = 0; $tot_col = array();?>
	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($stock_almacenes as $id_alm => $datos): ?>

		<?php if ($num_lin == 0): ?>
			<!-- ENCABEZADO TABLA REPORTE -->
			<thead>
				<tr class="active">
					<th></th>
					<th>Tipo Almac&eacute;n</th>
					<th>Almac&eacute;n</th>
					<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
						<th class="text-center">
							<?php echo $this->toa_model->dias_de_la_semana[date('w', strtotime($anomes.$dia_act))]; ?>
							<?php echo $dia_act; ?>
						</th>
					<?php $tot_col[$dia_act] = 0; ?>
					<?php endforeach; ?>
				</tr>
			</thead>

			<!-- CUERPO TABLA REPORTE -->
			<tbody>
		<?php endif; ?>

		<tr>
			<td class="text-muted"><?php echo $num_lin + 1; ?></td>
			<td style="white-space: nowrap;"><?php echo $datos['tipo']; ?></td>
			<td style="white-space: nowrap;"><?php echo $datos['centro']; ?>-<?php echo $datos['cod_almacen']; ?> <?php echo $datos['des_almacen']?></td>
			<?php foreach ($datos['actuaciones'] as $dia_act => $valor): ?>
				<?php if ($valor): ?>
					<?php $valor_desplegar = (set_value('dato') === 'monto') ? fmt_monto($valor, 'MM') : fmt_cantidad($valor); ?>
					<td class="text-center info"><?php echo anchor($url_detalle_dia.'/'.$anomes.$dia_act.'/'.$datos['centro'].'-'.$datos['cod_almacen'], $valor_desplegar); ?></td>
				<?php else: ?>
					<td></td>
				<?php endif ?>
			<?php $tot_col[$dia_act] += $valor;?>
			<?php endforeach; ?>
		</tr>
		<?php $num_lin += 1; ?>
	<?php endforeach; ?>
	</tbody>

	<!-- PIE TABLA REPORTE -->
	<tfoot>
		<tr class="active">
			<th></th>
			<th></th>
			<th></th>
			<?php foreach ($tot_col as $dia_act => $valor): ?>
				<th class="text-center"><?php echo set_value('dato') === 'monto' ? fmt_monto($valor, 'MM') : fmt_cantidad($valor);  ?></th>
			<?php endforeach; ?>
		</tr>
	</tfoot>
</table>
<?php endif ?>

</div> <!-- fin content-module-main -->
