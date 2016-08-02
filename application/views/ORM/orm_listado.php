<div class="row hidden-print">
	<?php echo form_open($url_filtro, 'method="get" class="form-search"'); ?>
	<div class="col-md-3 col-sm-5 col-xs-6">
		<div class="input-group input-group-sm">
			<?php echo form_input('filtro',set_value('filtro', $modelo->get_model_filtro()), 'class="form-control" id="filtro" maxlength="30" placeholder="{_orm_filter_}"'); ?>
			<span class="input-group-btn">
				<button type="submit" id="btn_filtro" class="btn btn-default">
					<span class="fa fa-search"></span>
				</button>
			</span>
		</div>
	</div>

	<div class="col-md-9 col-sm-7 col-xs-6 text-right">
		<a href="{url_nuevo}" class="btn btn-primary" id="btn_mostrar_agregar" role="button">
			<span class="fa fa-plus-circle"></span>
			{_orm_button_new_} <?php echo strtolower($modelo->get_model_label()); ?>
		</a>
	</div>
	<?php echo form_close(''); ?>
</div>

<div>
	<?php echo form_open('', 'id="frm_editar"'); ?>
	<?php echo form_hidden('formulario','editar'); ?>
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<?php foreach ($modelo as $campo => $valor): ?>
					<?php if ($modelo->get_mostrar_lista($campo)): ?>
						<th><?php echo strtolower($modelo->get_label_field($campo)); ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
				<th class="text-center"></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modelos as $o): ?>
				<tr>
					<?php foreach ($o as $campo => $valor): ?>
						<?php if ($o->get_mostrar_lista($campo)): ?>
							<td><?php echo $o->get_valor_field($campo); ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					<td class="text-center">
						<a href="<?php echo str_replace('%id%', $o->get_model_id(), $url_editar); ?>" class="">
							<!-- <span class="fa fa-edit"></span> -->
							{_orm_link_edit_}
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo form_close(); ?>

	<div class="text-center">
		<?php echo $modelo->crea_links_paginas(); ?>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	if ($('#filtro').val() != '')
	{
		$('#filtro').addClass('search_found');
		$('#btn_filtro').removeClass('btn-default');
		$('#btn_filtro').addClass('btn-primary');
	}
});
</script>
