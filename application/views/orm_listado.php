<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<a href="<?php echo $url_editar; ?>" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nueva aplicacion ...</a>
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
						<?php echo $modelo->print_label_campo($campo); ?>
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
							<?php echo $o->print_campo($campo); ?>
						</td>
					<?php endforeach; ?>
					<td class="ac">
						<a href="<?php echo $url_editar . '/' . $o->{$o->get_model_campo_id()}; ?>" class="button_micro b-active round boton-borrar">
							<img src="<?php echo base_url(); ?>img/ic_edit.png" />
						</a>
					</td>
					</tr>
				<?php endforeach; ?>

				<?php if ($links_paginas != ''):?>
				<tr>
					<td colspan="5"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->
