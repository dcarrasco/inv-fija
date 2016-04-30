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

					<div class="col-md-2">
						<div class="form_group">
							<label>{_controles_tecnicos_meses_}</label>
							<?php echo form_month('mes', $this->input->get('mes'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tecnicos_dato_desplegar_}</label>
							<?php echo form_dropdown('dato', $combo_dato_desplegar, $this->input->get('dato'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form_group">
							<label>{_controles_tecnicos_mostrar_}</label>
							<?php echo form_dropdown('mostrar', $combo_dato_mostrar, $this->input->get('mostrar'), 'class="form-control"'); ?>
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
<?php $num_lin = 0; $tot_col = array();?>
<?php if ($stock_tecnicos): ?>
	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($stock_tecnicos as $id_tecnico => $datos): ?>
	<?php if ($datos['con_datos'] > 0 OR $this->input->get('mostrar') === 'todos'): ?>

		<?php if ($num_lin == 0): ?>
			<thead>
				<tr class="active">
					<th></th>
					<th>T&eacute;cnico</th>
					<?php foreach ($datos['actuaciones'] as $dia_act => $cant_act): ?>
						<th class="text-center">
							<?php echo $this->toa_model->dias_de_la_semana[date('w', strtotime($anomes.$dia_act))]; ?>
							<?php echo $dia_act; ?>
						</th>
					<?php $tot_col[$dia_act] = 0; ?>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
		<?php endif; ?>
		<tr>
			<td class="text-muted">
				<?php echo $num_lin + 1; ?>
			</td>
			<td style="white-space: nowrap;">
				<?php echo $id_tecnico.' - '.$datos['tecnico']; ?>
			</td>
			<?php foreach ($datos['actuaciones'] as $dia_act => $valor): ?>
				<td class="text-center <?php echo $valor ? 'info' : ''; ?>">
					<?php $valor_desplegar = $this->input->get('dato') === 'monto' ? fmt_monto($valor, 'MM') : fmt_cantidad($valor); ?>
					<?php echo $valor ? anchor($url_detalle_dia.'/'.$anomes.$dia_act.'/'.$id_tecnico, $valor_desplegar) : ''; ?>
				</td>
			<?php $tot_col[$dia_act] += $valor;?>
			<?php endforeach; ?>
		</tr>
		<?php $num_lin += 1; ?>
	<?php endif ?>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="active">
			<th></th>
			<th></th>
			<?php foreach ($tot_col as $dia_act => $valor): ?>
				<th class="text-center"><?php echo $this->input->get('dato') === 'monto' ? fmt_monto($valor, 'MM') : fmt_cantidad($valor);  ?></th>
			<?php endforeach; ?>
		</tr>
	</tfoot>
</table>
<?php endif ?>

</div> <!-- fin content-module-main -->
