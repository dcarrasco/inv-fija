<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('actualizar', 'actualizar'); ?>
		<?php if ($update_status == ' disabled'): ?>
		<div class="form-group">
			<div class="col-sm-12 text-center">
				<?php echo sprintf($this->lang->line('inventario_act_precios_msg'), fmt_cantidad($cant_actualizada)); ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="form-group">
			<div class="col-sm-12 text-center">
				<button name="submit" type="submit" class="btn btn-primary" id="btn_imprimir" {update_status}>
					<span class="glyphicon glyphicon-refresh"></span>
					{_inventario_act_precios_button_}
				</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
