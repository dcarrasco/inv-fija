<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main-agregar">
		<?php echo form_open('', 'id="frm_editar"'); ?>
		<table>
			<tbody>
				<?php foreach ($modelo as $campo => $valor): ?>
				<tr>
					<th>
						<?php echo ucfirst($modelo->get_label_field($campo)); ?>
					</th>
					<td>
						<br>
						<?php echo $modelo->print_form_field($campo); ?>
						<?php echo form_error($campo); ?>
						<br><span class="texto-ayuda"><?php echo $modelo->get_texto_ayuda_field($campo); ?></span>
						<br>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-footer cf">
		<?php echo form_submit('borrar', 'Borrar', 'class="button b-active round ic-borrar fr" onclick="return confirm(\'Esta seguro de borrar este(a) ' . strtolower($modelo->get_model_label()) . '?\n\nEliminar: ' . strtoupper($modelo->__toString()) . '\n\');" '); ?>
		<?php echo form_submit('grabar', 'Guardar', 'class="button b-active round ic-ok fr"'); ?>
	</div> <!-- fin content-module-footer -->
	<?php echo form_close(); ?>

</div> <!-- fin content-module -->
