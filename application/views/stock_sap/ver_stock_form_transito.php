<?php echo form_open(); ?>
<div class="panel-group hidden-print" id="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						{_stock_sap_panel_params_}
					</a>
				</div>
				<div class="col-md-4">
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">

				{validation_errors}

				<div class="col-md-4">
					<div class="form-group">
						<label>
							{_stock_sap_label_dates_}
						</label>
						<div class="radio">
							<label>
								<?php echo form_radio('sel_fechas', 'ultimodia', set_radio('sel_fechas','ultimodia', TRUE)); ?>
								{_stock_sap_radio_date1_}
							</label>
						</div>
						<div class="radio">
							<label>
								<?php echo form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
								{_stock_sap_radio_date2_}
							</label>
						</div>
						<?php echo form_multiselect('fecha[]', $combo_fechas_todas, $this->input->post('fecha'),'id="select_fechas" size="10" class="form-control"'); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>
							{_stock_sap_label_mats_}
						</label>
						<div class="checkbox">
							<label>
								<?php echo form_checkbox('tipo_stock', 'tipo_stock', set_checkbox('tipo_stock', 'tipo_stock')); ?>
								{_stock_sap_check_tipstock_}
							</label>
						</div>
						<div class="checkbox">
							<label>
								<?php echo form_checkbox('material', 'material', set_checkbox('material', 'material')); ?>
								{_stock_sap_check_mat_}
							</label>
						</div>
						<div class="checkbox">
							<label>
								<?php echo form_checkbox('lote', 'lote', set_checkbox('lote', 'lote')); ?>
								{_stock_sap_check_lotes_}
							</label>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label>
							{_stock_sap_label_mostrar_}
						</label>
						<div class="radio-inline">
							<?php echo form_radio('mostrar_cant_monto', 'cantidad', TRUE); ?>
							{_stock_sap_radio_cant_}
						</div>
						<div class="radio-inline">
							<?php echo form_radio('mostrar_cant_monto', 'monto'); ?>
							{_stock_sap_radio_monto_}
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label></label>
						<div class="pull-right">
							<button type="submit" name="submit" class="btn btn-primary">
								<span class="glyphicon glyphicon-list-alt"></span>
								{_stock_sap_button_report_}
							</button>
							<button type="submit" name="excel" class="btn btn-default">
								<span class="glyphicon glyphicon-file"></span>
								{_stock_sap_button_export_}
							</button>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div> <!-- panel panel-default -->
</div> <!-- panel-group -->
<?php echo form_close(); ?>

<script type="text/javascript" src="{base_url}js/ver_stock_form.js"></script>
