<div class="row">
	<?php echo form_open($url_filtro, 'class="form-search"'); ?>
	<div class="col-md-3">
		<div class="input-group input-group-sm">
			<?php echo form_input('filtro',set_value('filtro', $modelo->get_model_filtro()), 'class="form-control" maxlength="30" placeholder="Texto a filtrar..."'); ?>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default">
					<span class="glyphicon glyphicon-search"></span>
				</button>
			</span>
		</div>
	</div>

	<div class="col-md-9 text-right">
		<a href="<?php echo $url_editar; ?>" class="btn btn-default" id="btn_mostrar_agregar">
			<span class="glyphicon glyphicon-plus-sign"></span>
			Agregar <?php echo strtolower($modelo->get_model_label()); ?>
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
							<a href="<?php echo $url_editar . '/' . $o->get_model_id(); ?>" class="btn  btn-default btn-xs">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="text-center">
		<?php echo $modelo->crea_links_paginas(); ?>
	</div>
</div> <!-- fin content-module-main -->
