<div class="form-group <?php echo $item_error; ?>">
	<?php echo form_label(
				$item_label . ($item_required ? ' <span class="text-danger">*</span>' : ''),
				$item_id,
				array('class' => 'control-label col-sm-4')
			);
	?>
	<div class="col-sm-8">
		<?php echo $item_form; ?>
		<?php if ($item_help): ?>
		<span class="help-block">
			<em><small><?php echo $item_help; ?></small></em>
		</span>
		<?php endif; ?>
	</div>
</div>
