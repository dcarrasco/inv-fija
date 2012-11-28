
<div class="content-module">

	<div class="content-module-heading cf">
		<?php echo $menu_configuracion; ?>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">


		<?php echo form_open(); ?>
		<table>
			<tr>
				<td>Centro</td>
				<td>
					<?php echo form_input('centro', $data_centro, 'size="10" maxlength="10"'); ?>
					<?php echo form_error('centro'); ?>
				</td>
			</tr>
			<tr>
				<td>Codigo Almacen</td>
				<td>
					<?php echo form_input('cod_almacen', $data_cod_alm, 'size="10" maxlength="10"'); ?>
					<?php echo form_error('cod_almacen'); ?>
				</td>
			</tr>
			<tr>
				<td>Descripcion Almacen</td>
				<td>
					<?php echo form_input('des_almacen', $data_des_alm, 'size="50" maxlength="50"'); ?>
					<?php echo form_error('des_almacen'); ?>
				</td>
			</tr>
			<tr>
				<td>Uso Almacen</td>
				<td>
					<?php echo form_input('uso_almacen', $data_uso_alm, 'size="50" maxlength="50"'); ?>
					<?php echo form_error('uso_almacen'); ?>
				</td>
			</tr>
			<tr>
				<td>Responsable</td>
				<td>
					<?php echo form_input('responsable', $data_responsable, 'size="50" maxlength="50"'); ?>
					<?php echo form_error('responsable'); ?>
				</td>
			</tr>
			<tr>
				<td>Grupos</td>
				<td>
					<?php echo form_multiselect('grupos[]', $combo_grupos, $data_grupos, 'size="10"'); ?>
					<?php echo form_error('des_almacen'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php echo form_submit('btn_accion', 'Grabar'); ?>
				</td>
			</tr>
		</table>
		<?php echo form_close(); ?>
		<?php echo anchor('stock_sap/lista_almacenes/' . $tipo_op, 'Volver'); ?>


	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">

	</div> <!-- fin content-module-footer -->


</div> <!-- fin content-module -->






</body>
</html>
