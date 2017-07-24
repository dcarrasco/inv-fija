<div class="row">
	<div class="col-md-offset-1 col-md-10">
		{msg_agregar}

		<?php if (count($tecnicos) > 0): ?>
			<?= form_open($url_form,'class="form-horizontal"'); ?>
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th>Empresa</th>
						<th>ID T&eacute;cnico</th>
						<th>Nombre T&eacute;cnico</th>
						<th>Rut T&eacute;cnico</th>
						<th class="text-center">Empresa TOA</th>
						<th>Agencia</th>
						<th>Ciudad</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($tecnicos as $tecnico): ?>
					<?= form_hidden('tecnico[]', $tecnico['id_tecnico']) ?>
					<tr>
						<td><?= $tecnico['empresa'] ?></td>
						<td><?= $tecnico['id_tecnico'] ?></td>
						<td><?= $tecnico['tecnico'] ?></td>
						<td><?= fmt_rut($tecnico['rut']) ?></td>
						<td class="text-center"><?= $tecnico['contractor_company'] ?></td>
						<td><?= $tecnico['xa_original_agency'] ?></td>
						<td><?= form_dropdown('ciudad[]', $empresas[$tecnico['id_empresa']], '', ['class'=>'form-control']) ?></td>
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
			<?= print_message($this->lang->line('toa_controles_sin_tecnicos')); ?>
		<?php endif ?>
	</div>
</div>
