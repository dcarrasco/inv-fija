<?= form_open(); ?>
<?= form_hidden('tipo_op', $tipo_op); ?>
<div class="accordion hidden-print">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						<span class="fa fa-filter"></span>
						{_stock_sap_panel_params_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">

				{validation_errors}

				<div class="col-md-4 form-group <?= form_has_error_class('fecha[]') ?>">
					<label class="control-label">{_stock_sap_label_dates_}</label>
					<div class="radio">
						<label>
							<?= form_radio('sel_fechas', 'ultimodia', set_radio('sel_fechas','ultimodia', TRUE)); ?>
							{_stock_sap_radio_date1_}
						</label>
					</div>
					<div class="radio">
						<label>
							<?= form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
							{_stock_sap_radio_date2_}
						</label>
					</div>
					<?= form_multiselect('fecha[]', $combo_fechas, request('fecha'),'id="select_fechas" size="10" class="form-control"'); ?>
				</div>

				<div class="col-md-4 form-group <?= form_has_error_class('almacenes[]') ?>">
					<label class="control-label">{_stock_sap_label_alm_}</label>
					<div class="radio">
						<label>
							<?= form_radio('sel_tiposalm', 'sel_tiposalm', set_radio('sel_tiposalm','sel_tiposalm', TRUE)); ?>
							{_stock_sap_radio_alm1_}
						</label>
					</div>
					<div class="radio">
						<label>
							<?= form_radio('sel_tiposalm', 'sel_almacenes', set_radio('sel_tiposalm','sel_almacenes')); ?>
							{_stock_sap_radio_alm2_}
						</label>
					</div>
					<?= form_multiselect('almacenes[]', $combo_almacenes, request('almacenes'), 'id="select_almacenes" size="10" class="form-control"'); ?>
					<div id="show_tiposalm">
						<div class="checkbox">
							<label>
								<?= form_checkbox('almacen', 'almacen', request('almacen')); ?>
								{_stock_sap_check_show_alm_}
							</label>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">{_stock_sap_label_mats_}</label>

						<!--
						<div class="checkbox">
							<?php //echo form_checkbox('tipo_articulo', 'tipo_articulo', set_checkbox('tipo_articulo', 'tipo_articulo')); ?>
							Desplegar detalle tipos articulo
						</div>
						-->

						<div class="checkbox">
							<label>
								<?= form_checkbox('material', 'material', request('material')); ?>
								{_stock_sap_check_mat_}
							</label>
						</div>
						<div class="checkbox">
							<label>
								<?= form_checkbox('lote', 'lote', request('lote')); ?>
								{_stock_sap_check_lotes_}
							</label>
						</div>
						<div class="checkbox">
							<label>
								<?= form_checkbox('tipo_stock', 'tipo_stock', request('tipo_stock')); ?>
								{_stock_sap_check_tipstock_}
							</label>
						</div>

						<?php if ($tipo_op === 'MOVIL'): ?>
						<div>
							<div class="checkbox-inline">
								<?= form_checkbox('tipo_stock_equipos', 'tipo_stock_equipos', request('tipo_stock_equipos', TRUE)); ?>
								{_stock_sap_radio_equipos_}
							</div>
							<div class="checkbox-inline">
								<?= form_checkbox('tipo_stock_simcard', 'tipo_stock_simcard', request('tipo_stock_simcard', TRUE)); ?>
								{_stock_sap_radio_sim_}
							</div>
							<div class="checkbox-inline">
								<?= form_checkbox('tipo_stock_otros', 'tipo_stock_otros', request('tipo_stock_otros', TRUE)); ?>
								{_stock_sap_radio_otros_}
							</div>
						</div>
						<?php endif; ?>
					</div>

					<hr/>
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary">
							<span class="fa fa-search"></span>
							{_stock_sap_button_report_}
						</button>
						<button type="submit" name="excel" value="excel" class="btn btn-default">
							<span class="fa fa-file-text-o"></span>
							{_stock_sap_button_export_}
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

<?= form_close(); ?>

<script type="text/javascript" src="{base_url}js/ver_stock_form.js"></script>
