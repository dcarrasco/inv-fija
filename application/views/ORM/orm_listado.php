<div class="row-fluid">
	<?php echo $menu_configuracion; ?>
</div>

<?php if ($msg_alerta != ''): ?>
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $msg_alerta; ?>
	</div>
<?php endif; ?>

<div class="row-fluid">
	<?php echo form_open($url_filtro, 'class="form-search"'); ?>
	<div class="input-append span6">
		<?php echo form_input('filtro',set_value('filtro', $filtro), 'class="span3 search-query" maxlength="30" placeholder="Texto a filtrar..."'); ?>
		<button type="submit" class="btn">Filtrar</button>
	</div>

	<div class="span6 text-right">
		<a href="<?php echo $url_editar; ?>" class="btn" id="btn_mostrar_agregar">
			<i class="icon-plus-sign"></i>
			Agregar <?php echo strtolower($modelo->get_model_label()); ?>
		</a>
	</div>
	<?php echo form_close(''); ?>
</div>
<hr />
<div>
	<?php echo form_open('', 'id="frm_editar"'); ?>
	<?php echo form_hidden('formulario','editar'); ?>
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<?php foreach ($modelo as $campo => $valor): ?>
					<?php if ($modelo->get_mostrar_lista($campo)): ?>
						<th><?php echo ucfirst($modelo->get_label_field($campo)); ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
				<th><div class="text-center">Editar</div></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($modelo->get_model_all() as $o): ?>
				<tr>
					<?php foreach ($o as $campo => $valor): ?>
						<?php if ($o->get_mostrar_lista($campo)): ?>
							<td><?php echo $o->get_valor_field($campo); ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					<td>
						<div class="text-center">
							<a href="<?php echo $url_editar . '/' . $o->get_model_id(); ?>" class="btn btn-mini">
								<i class="icon-edit"></i>
							</a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

		<div class="pagination pagination-centered">
			<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
		</div>
	</div> <!-- fin content-module-main -->

</div> <!-- fin content-module -->
