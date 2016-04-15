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
				<div class="row">
					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tecnicos_empresas_}</label>
							<?php echo form_dropdown('empresa', $combo_empresas, $this->input->get('empresa'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tecnicos_meses_}</label>
							<?php echo form_dropdown('mes', $combo_meses, $this->input->get('mes'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tecnicos_dato_desplegar_}</label>
							<?php echo form_dropdown('dato', $combo_dato_desplegar, $this->input->get('dato'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
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
<?php if ($stock_tecnicos): ?>
	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($stock_tecnicos as $id_tecnico => $datos): ?>
		<?php if ($i == 0): ?>
			<tr>
				<th></th>
				<th>T&eacute;cnico</th>
				<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
					<th class="text-center">
						<?php echo $this->toa_model->dias_de_la_semana[date('w', strtotime($anomes.$dia_act))]; ?>
						<?php echo $dia_act; ?>
					</th>
				<?php endforeach; ?>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="text-muted">
				<?php echo $i+1; ?>
			</td>
			<td style="white-space: nowrap;">
				<?php echo $id_tecnico.' - '.$datos['tecnico']; ?>
			</td>
			<?php foreach ($datos['actuaciones'] as $dia_act => $valor): ?>
				<td class="text-center <?php echo $valor ? 'success' : ''; ?>">
					<?php $valor_desplegar = $this->input->get('dato') === 'monto' ? fmt_monto($valor, 'MM') : fmt_cantidad($valor); ?>
					<?php echo $valor ? anchor($url_detalle_dia.'/'.$anomes.$dia_act.'/'.$id_tecnico, $valor_desplegar) : ''; ?>
				</td>
			<?php endforeach; ?>
		</tr>
		<?php $i += 1; ?>
	<?php endforeach; ?>
</table>
<?php endif ?>

</div> <!-- fin content-module-main -->
