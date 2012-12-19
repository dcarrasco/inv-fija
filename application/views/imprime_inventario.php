<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_ajustes; ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta cf ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<?php echo form_open(); ?>
		<?php echo form_hidden('formulario','imprime'); ?>
		<table>
			<thead>
				<tr>
					<th>id</th>
					<th>Nombre</th>
					<th>Imprimir</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $inventario_id; ?></td>
					<td><?php echo $inventario_nombre; ?></td>
					<td>
						Pagina Desde:
						<?php echo form_input('pag_desde', set_value('pag_desde',1), ' size="5" maxlength="5"'); ?>
						<?php echo form_error('pag_desde'); ?>
						<br>
						Pagina Hasta:
						<?php echo form_input('pag_hasta', set_value('pag_hasta',$max_hoja), ' size="5" maxlength="5"'); ?>
						<?php echo form_error('pag_hasta'); ?>
						<br>
						<?php echo form_checkbox('oculta_stock_sap', 'oculta_stock_sap', set_checkbox('oculta_stock_sap','oculta_stock_sap', FALSE)); ?>
						Oculta columnas [STOCK_SAP] y [F/A]
						<br>

					</td>
				</tr>

				<tr>
					<td colspan="4">
						<?php echo form_submit('submit', 'Imprimir', ' class="button b-active round ic-ok fr" id="btn_imprimir"'); ?>
					</td>
				</tr>

		</tbody>
		</table>
		<?php echo form_close(); ?>

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->
