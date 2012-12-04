<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
			<br>
			<?php echo form_open($url_filtro); ?>
			Buscar
			<?php echo form_input('filtro',set_value('filtro', $filtro), 'size="30" maxlength="30" class="form_edit round" style="' . (($filtro == '') ? '' : 'background-color: yellow;') . '"'); ?>
			<?php echo form_submit('btn_filtrar', 'Filtrar', 'class="button_mini b-active round ic-search"') ?>
			<?php echo form_close(''); ?>
		</div>

		<div class="fr">
			<a href="<?php echo $url_editar; ?>" class="button b-active round ic-agregar fl" id="btn_mostrar_agregar">Agregar <?php echo strtolower($modelo->get_model_label()); ?></a>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_editar"'); ?>
		<?php echo form_hidden('formulario','editar'); ?>
		<table>
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
					<td class="ac">
						<a href="<?php echo $url_editar . '/' . $o->get_model_id(); ?>" class="button_micro b-active round boton-borrar">
							<img src="<?php echo base_url(); ?>img/ic_edit.png" />
						</a>
					</td>
					</tr>
				<?php endforeach; ?>

				<?php if ($links_paginas != ''):?>
				<tr>
					<td colspan="10"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->
