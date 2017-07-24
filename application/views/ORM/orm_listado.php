<div class="row hidden-print">
	<?= form_open('', 'class="form-search" method="get"'); ?>
	<div class="col-md-3 col-sm-5 col-xs-6">
		<div class="input-group input-group-sm">
			<?= form_input('filtro', request('filtro', $modelo->get_filtro()), 'class="form-control" id="filtro" maxlength="30" placeholder="{_orm_filter_}"'); ?>
			<span class="input-group-btn">
				<button type="submit" id="btn_filtro" class="btn btn-default">
					<span class="fa fa-search"></span>
				</button>
			</span>
		</div>
	</div>

	<div class="col-md-9 col-sm-7 col-xs-6 text-right">
		<a href="{url_editar}{url_params}" class="btn btn-primary" id="btn_mostrar_agregar" role="button">
			<span class="fa fa-plus-circle"></span>
			{_orm_button_new_} <?= strtolower($modelo->get_label()); ?>
		</a>
	</div>
	<?= form_close(''); ?>
</div>

<div>
	<?= form_open('', 'id="frm_editar"'); ?>
	<?= form_hidden('formulario','editar'); ?>
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<?php foreach ($modelo as $campo => $valor): ?>
					<?php if ($modelo->get_mostrar_lista($campo)): ?>
						<th><?= strtolower($modelo->get_field_label($campo)); ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
				<th class="text-center"></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modelos as $obj_modelo): ?>
				<tr>
					<?php foreach ($obj_modelo->get_fields(TRUE) as $campo => $valor): ?>
						<td><?= $obj_modelo->get_field_value($campo); ?></td>
					<?php endforeach; ?>
					<td class="text-center">
						<a href="{url_editar}<?= $obj_modelo->get_id() ?>{url_params}" class="">
							<!-- <span class="fa fa-edit"></span> -->
							{_orm_link_edit_}
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?= form_close(); ?>

	<div class="text-center">
		<?= $modelo->crea_links_paginas(); ?>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	if ($('#filtro').val() != '')
	{
		$('#filtro').addClass('search_found');
		$('#btn_filtro').removeClass('btn-default').addClass('btn-primary');
	}
});
</script>
