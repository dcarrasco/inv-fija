<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('formulario','imprime'); ?>

		<div class="form-group">
			<label class="control-label col-sm-3">
				<?php echo $this->lang->line('inventario_print_label_inventario'); ?>
			</label>
			<div class="col-sm-8">
				<p class="form-control-static">
					<?php echo $inventario_id . ' - ' . $inventario_nombre; ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('pag_desde'); ?>">
			<label class="control-label col-sm-3">
				<?php echo $this->lang->line('inventario_print_label_page_from'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo form_input('pag_desde', set_value('pag_desde',1), 'class="form-control"  maxlength="5"'); ?>
				<?php echo form_error('pag_desde'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('pag_hasta'); ?>">
			<label class="control-label col-sm-3">
				<?php echo $this->lang->line('inventario_print_label_page_to'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo form_input('pag_hasta', set_value('pag_hasta',$max_hoja), 'class="form-control"  maxlength="5"'); ?>
				<?php echo form_error('pag_hasta'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('oculta_stock_sap'); ?>">
			<label class="control-label col-sm-3">
				<?php echo $this->lang->line('inventario_print_label_options'); ?>
			</label>
			<div class="col-sm-8">
				<label class="checkbox-inline">
					<?php echo form_checkbox('oculta_stock_sap', 'oculta_stock_sap', set_checkbox('oculta_stock_sap','oculta_stock_sap', FALSE)); ?>
					<?php echo $this->lang->line('inventario_print_check_hide_columns'); ?>
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
			</label>
			<div class="col-sm-8">
				<button name="submit" type="submit" class="btn btn-primary pull-right" id="btn_imprimir">
					<span class="glyphicon glyphicon-print"></span>
					<?php echo $this->lang->line('inventario_print_button_print'); ?>
				</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
