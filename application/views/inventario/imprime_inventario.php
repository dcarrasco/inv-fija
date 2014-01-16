<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('formulario','imprime'); ?>

		<div class="col-md-10 col-md-offset-2">
			<div class="control-group">
				<label class="control-label">
					Inventario
				</label>
				<div class="controls">
					<span class="input-xlarge uneditable-input">
						<?php echo $inventario_id . ' - ' . $inventario_nombre; ?>
					</span>
				</div>
			</div>
		</div>

		<div>
			<div class="col-md-3 col-md-offset-2">
				<div class="control-group">
					<label class="control-label">
						Pagina desde
					</label>
					<div class="controls">
						<?php echo form_input('pag_desde', set_value('pag_desde',1), 'class="form-control"  maxlength="5"'); ?>
						<?php echo form_error('pag_desde'); ?>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-md-offset-2">
				<div class="control-group">
					<label class="control-label">
						Pagina hasta
					</label>
					<div class="controls">
						<?php echo form_input('pag_hasta', set_value('pag_hasta',$max_hoja), 'class="form-control"  maxlength="5"'); ?>
						<?php echo form_error('pag_hasta'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-10 col-md-offset-2">
			<div class="control-group">
				<label class="control-label">
					Opciones
				</label>
				<div class="controls">
					<label class="checkbox-inline">
						<?php echo form_checkbox('oculta_stock_sap', 'oculta_stock_sap', set_checkbox('oculta_stock_sap','oculta_stock_sap', FALSE)); ?>
						Oculta columnas [STOCK_SAP] y [F/A]
					</label>
				</div>
			</div>
		</div>

		<div class="control-group">
			<div class="controls pull-right">
				<button name="submit" type="submit" class="btn btn-primary" id="btn_imprimir">
					<span class="glyphicon glyphicon-print"></span>
					Imprimir
				</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
