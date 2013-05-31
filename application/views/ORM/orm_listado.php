<div class="container-fluid">

	<div>
		<div>
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="row-fluid">
			<?php echo form_open($url_filtro, 'class="form-search"'); ?>
			<div class="input-append span6">
				<?php echo form_input('filtro',set_value('filtro', $filtro), 'class="span3 search-query" maxlength="30" placeholder="Texto a filtrar..."'); ?>
				<button type="submit" class="btn">Filtrar</button>
			</div>
			<?php echo form_close(''); ?>

			<div class="span6 text-right">
				<a href="<?php echo $url_editar; ?>" class="btn" id="btn_mostrar_agregar">
					<i class="icon-plus-sign"></i>
					Agregar <?php echo strtolower($modelo->get_model_label()); ?>
				</a>
			</div>

		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_editar"'); ?>
		<?php echo form_hidden('formulario','editar'); ?>
		<table class="table table-bordered table-striped table-hover table-condensed">
			<thead>
				<tr>
				<?php foreach ($modelo as $campo => $valor): ?>
					<th>
						<?php echo ucfirst($modelo->get_label_field($campo)); ?>
					</th>
				<?php endforeach; ?>
				<th class="ac">Editar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($modelo->get_model_all() as $o): ?>
					<tr>
					<?php foreach ($o as $campo => $valor): ?>
						<td>
							<?php echo $o->get_valor_field($campo); ?>
						</td>
					<?php endforeach; ?>
					<td class="text-center">
						<a href="<?php echo $url_editar . '/' . $o->get_model_id(); ?>" class="btn">
							<i class="icon-edit"></i>
						</a>
					</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="pagination text-center">
			<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
		</div>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->
