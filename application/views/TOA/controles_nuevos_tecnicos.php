<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		{msg_agregar}
		<?php echo form_open('','class="form-horizontal"'); ?>
		<?php echo form_hidden('agregar', 'agregar'); ?>
		<div class="form-group">
			<div class="col-sm-12 text-center">
				<button name="submit" type="submit" class="btn btn-primary" id="btn_imprimir" {update_status}>
					<span class="fa fa-user-plus"></span>
					{_toa_controles_nuevos_tecnicos_}
				</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
