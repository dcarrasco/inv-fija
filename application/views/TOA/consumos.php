<div class="accordion">
	<?php echo form_open('','id="frm_param" class="form-inline"'); ?>
	<?php echo form_hidden('order_by', set_value('order_by','')); ?>
	<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						{_consumo_parametros_}
					</a>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_param">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-5">
						<div class="form_group">
							<label>{_consumo_reporte_}</label>
							<?php echo form_dropdown('sel_reporte', $combo_reportes, $this->input->post('sel_reporte'), 'class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-5">
						<div class="form_group">
							<label>{_consumo_fechas_}</label>
							<div class="input-group date" data-provide="datepicker" data-date-today-highlight="true" data-date-language="es" data-date-autoclose="true">
								<?php echo form_input('fecha', $this->input->post('fecha'), 'class="form-control"'); ?>
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-2">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">
								<span class="fa fa-search"></span>
								{_consumo_btn_reporte_}
							</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>

<div class="content-module-main">
{reporte}
</div> <!-- fin content-module-main -->
