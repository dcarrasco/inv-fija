<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('formulario','imprime'); ?>
		<fieldset>

			<legend>{_inventario_print_label_legend_}</legend>

			{validation_errors}

			<div class="form-group">
				<label class="control-label col-sm-4">{_inventario_print_label_inventario_}</label>
				<div class="col-sm-8">
					<p class="form-control-static">{inventario_id} - {inventario_nombre}</p>
				</div>
			</div>

			<div class="form-group <?php echo form_has_error('pag_desde'); ?>">
				<label class="control-label col-sm-4">{_inventario_print_label_page_from_}</label>
				<div class="col-sm-8">
					<?php echo form_input('pag_desde', set_value('pag_desde',1), 'class="form-control"  maxlength="5"'); ?>
				</div>
			</div>

			<div class="form-group <?php echo form_has_error('pag_hasta'); ?>">
				<label class="control-label col-sm-4">{_inventario_print_label_page_to_}</label>
				<div class="col-sm-8">
					<?php echo form_input('pag_hasta', set_value('pag_hasta',$max_hoja), 'class="form-control"  maxlength="5"'); ?>
				</div>
			</div>

			<div class="form-group <?php echo form_has_error('oculta_stock_sap'); ?>">
				<label class="control-label col-sm-4">{_inventario_print_label_options_}</label>
				<div class="col-sm-8">
					<label class="checkbox-inline">
						<?php echo form_checkbox('oculta_stock_sap', 'oculta_stock_sap', set_value('oculta_stock_sap')); ?>
						{_inventario_print_check_hide_columns_}
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<button name="submit" type="submit" class="btn btn-primary pull-right" id="btn_imprimir">
						<span class="fa fa-print"></span>
						{_inventario_print_button_print_}
					</button>
				</div>
			</div>

		</fieldset>
		<?php echo form_close(); ?>
	</div>
</div>
