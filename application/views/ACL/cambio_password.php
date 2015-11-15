<div class="row">

	<div class="col-md-6 col-md-offset-3 well">
		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<h2>
				<?php echo $this->lang->line('login_form_change_password'); ?>
			</h2>
		</div>

		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<hr>
		</div>

		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<?php echo print_validation_errors(); ?>
		</div>

		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<?php echo (isset($msg_alerta) and $msg_alerta !== '') ? $msg_alerta : ''; ?>
		</div>

		<?php echo form_open('login/cambio_password/' . $usr, 'id="frm_login" class="form-horizontal"'); ?>


			<div class="control-group col-md-8 col-md-offset-2 <?php echo form_has_error('usr') ? 'has-error' : ''; ?>">
				<label class="control-label" for="usr">
					<?php echo $this->lang->line('login_input_user'); ?>
				</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr', $usr),'maxlength="45" class="form-control"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 <?php echo form_has_error('pwd_old') ? 'has-error' : ''; ?>">
				<label class="control-label" for="pwd_old">
					<?php echo $this->lang->line('login_input_old_password'); ?>
				</label>
				<div class="controls">
					<?php echo form_password('pwd_old', '','maxlength="45" autocomplete="off" class="form-control" ' . $tiene_clave_class); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 <?php echo form_has_error('pwd_new1') ? 'has-error' : ''; ?>">
				<label class="control-label" for="pwd_new1">
					<?php echo $this->lang->line('login_input_new1_password'); ?>
				</label>
				<div class="controls">
					<?php echo form_password('pwd_new1', '','maxlength="45" class="form-control"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 <?php echo form_has_error('pwd_new2') ? 'has-error' : ''; ?>">
				<label class="control-label" for="pwd_new2">
					<?php echo $this->lang->line('login_input_new2_password'); ?>
				</label>
				<div class="controls">
					<?php echo form_password('pwd_new2', '','maxlength="45" class="form-control"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2">
				<hr/>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<div class="controls">
						<button type="submit" name="btn_submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-lock"></span>
							<?php echo $this->lang->line('login_button_change_password'); ?>
						</button>
					</div>
				</div>
			</div>
		<?php echo form_close(); ?>

	</div>

</div>
