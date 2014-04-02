<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('formulario','imprime'); ?>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Inventario
			</label>
			<div class="col-sm-8">
				<p class="form-control-static">
					<?php echo $inventario_id . ' - ' . $inventario_nombre; ?>
				</p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Pagina desde
			</label>
			<div class="col-sm-2">
				<?php echo form_input('pag_desde', set_value('pag_desde',1), 'class="form-control"  maxlength="5"'); ?>
				<?php echo form_error('pag_desde'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Pagina hasta
			</label>
			<div class="col-sm-2">
				<?php echo form_input('pag_hasta', set_value('pag_hasta',$max_hoja), 'class="form-control"  maxlength="5"'); ?>
				<?php echo form_error('pag_hasta'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Opciones
			</label>
			<div class="col-sm-8">
				<label class="checkbox-inline">
					<?php echo form_checkbox('oculta_stock_sap', 'oculta_stock_sap', set_checkbox('oculta_stock_sap','oculta_stock_sap', FALSE)); ?>
					Oculta columnas [STOCK_SAP] y [F/A]
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
			</label>
			<div class="col-sm-8">
				<button name="submit" type="submit" class="btn btn-primary pull-right" id="btn_imprimir">
					<span class="glyphicon glyphicon-print"></span>
					Imprimir
				</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
