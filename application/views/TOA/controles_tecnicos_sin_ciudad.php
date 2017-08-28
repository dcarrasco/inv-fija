<div class="row">
	<div class="col-md-12">
		{msg_agregar}

		<?php if (count($tecnicos) > 0): ?>
			<?= form_open($url_form,'class="form-horizontal"'); ?>
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th>ID T&eacute;cnico</th>
						<th>Nombre T&eacute;cnico</th>
						<th>Rut T&eacute;cnico</th>
						<th class="text-center">Empresa T&eacute;cnico</th>
						<th>Empresa</th>
						<th>Agencia</th>
						<th>Ciudad</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($tecnicos as $tecnico): ?>
					<tr>
						<?= form_hidden('tecnico[]', $tecnico['tecnico']->id_tecnico) ?>
						<?= form_hidden('empresa[]', $tecnico['contractor_company']) ?>
						<td><?= $tecnico['tecnico']->id_tecnico; ?></td>
						<td><?= $tecnico['tecnico']->tecnico ?></td>
						<td><?= fmt_rut($tecnico['tecnico']->rut) ?></td>
						<td class="text-center"><?= $tecnico['tecnico']->id_empresa ?></td>
						<td><?= $tecnico['empresa'] ?></td>
						<td><?= $tecnico['original_agency'] ?></td>
						<td><?= form_dropdown('ciudad[]', $empresas[$tecnico['contractor_company']], '', ['class'=>'form-control']) ?></td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>

			<?php if (empty($msg_agregar)): ?>
				<?= form_hidden('agregar', 'agregar'); ?>
				<div class="form-group text-right">
					<button name="submit" type="submit" class="btn btn-primary" id="btn_imprimir" {update_status}>
						<span class="fa fa-check"></span>
						{_toa_controles_ciudades_tecnicos_}
					</button>
				</div>
				<?= form_close(); ?>
			<?php endif ?>

		<?php else: ?>
			<?= print_message(lang('toa_controles_sin_tecnicos')); ?>
		<?php endif ?>
	</div>
</div>
