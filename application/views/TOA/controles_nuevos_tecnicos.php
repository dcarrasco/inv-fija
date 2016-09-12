<div class="row">
	<div class="col-md-offset-1 col-md-10">
		{msg_agregar}

		<?php if (count($nuevos_tecnicos) > 0): ?>
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th>id tecnico</th>
						<th>tecnico</th>
						<th>rut</th>
						<th>id empresa</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($nuevos_tecnicos as $tecnico): ?>
					<tr>
						<td><?php echo $tecnico['id_tecnico']; ?></td>
						<td><?php echo $tecnico['tecnico']; ?></td>
						<td><?php echo $tecnico['rut']; ?></td>
						<td><?php echo $tecnico['id_empresa']; ?></td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>

			<?php if (empty($msg_agregar)): ?>
				<?php echo form_open('','class="form-horizontal"'); ?>
				<?php echo form_hidden('agregar', 'agregar'); ?>
				<div class="form-group text-right">
					<button name="submit" type="submit" class="btn btn-primary" id="btn_imprimir" {update_status}>
						<span class="fa fa-user-plus"></span>
						{_toa_controles_nuevos_tecnicos_}
					</button>
				</div>
				<?php echo form_close(); ?>
			<?php endif ?>

		<?php else: ?>
			<?php echo print_message($this->lang->line('toa_controles_sin_tecnicos')); ?>
		<?php endif ?>
	</div>
</div>
