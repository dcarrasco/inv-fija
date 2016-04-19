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
							<?php echo form_dropdown('mes', $combo_meses, $this->input->get('mes'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form_group">
							<label>{_controles_tipos_trabajo_}</label>
							<?php echo form_dropdown('tipo_trabajo', $combo_tipos_trabajo, $this->input->get('tipo_trabajo'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form_group">
							<label>{_controles_tecnicos_dato_desplegar_}</label>
							<?php echo form_dropdown('dato', $combo_dato_desplegar, $this->input->get('dato'), 'class="form-control"'); ?>
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
<?php $i = 0; $tot_col = array();?>
<?php if ($materiales_tipos_trabajo): ?>
	<table class="table table-bordered table-hover table-condensed reporte">
	<?php foreach ($materiales_tipos_trabajo as $referencia => $arr_referencia): ?>
		<?php if ($i == 0): ?>
			<thead>
			<tr>
				<th></th>
				<th>Petici&oacute;n</th>
				<?php foreach ($arr_referencia as $material => $arr_material): ?>
					<th class="text-center ">
						<?php echo $material; ?> - <?php echo $arr_material['texto_material']; ?>
						<?php $tot_col[$material] = 0; ?>
					</th>
				<?php endforeach; ?>
				<th>Total</th>
			</tr>
			</thead>
			<tbody>
		<?php endif; ?>
		<tr>
			<td class="text-muted"><?php echo $i+1; ?></td>
			<td style="white-space: nowrap;"><?php echo anchor($url_detalle_dia.'/'.$referencia, $referencia); ?></td>
				<?php $tot_lin = 0; ?>
				<?php foreach ($arr_referencia as $material => $arr_material): ?>
					<td class="text-center <?php echo $arr_material['dato'] ? 'success' : ''; ?>">
						<?php echo $arr_material['dato'] ? fmt_cantidad($arr_material['dato']) : ''; ?>
						<?php $tot_lin += $arr_material['dato']; $tot_col[$material] += $arr_material['dato']; ?>
					</td>
				<?php endforeach; ?>
				<th class="text-center"><?php echo fmt_cantidad($tot_lin); ?></th>
		</tr>
		<?php $i += 1; ?>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
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
