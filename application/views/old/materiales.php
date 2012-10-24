<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<div class="fl" style="margin-right: 30px">
			Filtrar
			<?php echo form_input('filtro',$filtro,'id="filtro" size="30" placeholder="Ingrese filtro..."'); ?>
			</div>
			<?php echo anchor('/config/act_precio_materiales','Actualizar precios', 'class="button b-active round ic-update fl"'); ?>
			<a href="#" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nuevo material ...</a>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main-agregar" style="display: none;">
		<?php echo form_open('','id=frm_agregar')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">Catalogo</th>
					<th class="ac">Descripcion</th>
					<th class="ac">PMP</th>
					<th></th>
				</tr>
			</thead>
			<tr>
				<td class="ac">
					<?php echo form_input('agr_catalogo', set_value('agr_catalogo'), 'maxlength="45" size="45"'); ?>
					<?php echo form_error('agr_catalogo'); ?>
				</td>
				<td class="ac">
					<?php echo form_input('agr_descripcion', set_value('agr_descripcion'), 'maxlength="100" size="60"'); ?>
					<?php echo form_error('agr_descripcion'); ?>
				</td>
				<td class="ac">
					<?php echo form_input('agr_pmp', set_value('agr_pmp'), 'maxlength="10" size="10"', 'class="ar"'); ?>
					<?php echo form_error('agr_pmp'); ?>
				</td>
				<td class="ac">		
					<a href="#" class="button b-active round ic-agregar fl" id="btn_agregar">Agregar</a>
				</td>
			</tr>
		</table>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_materiales"'); ?>
		<?php echo form_hidden('formulario','materiales'); ?>
		<table>
			<thead>
				<tr>
					<th>Catalogo</th>
					<th>Descripcion</th>
					<th>PMP</th>
					<th class="ac">Borrar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td>
						<?php echo form_input($reg['catalogo'].'-catalogo', set_value($reg['catalogo'].'-catalogo', $reg['catalogo']),'maxlength="45" size="25"'); ?>
						<?php echo form_error($reg['catalogo'].'-catalogo'); ?>
					</td>
					<td>
						<?php echo form_input($reg['catalogo'].'-descripcion', set_value($reg['catalogo'].'-descripcion', $reg['descripcion']),'maxlength="100" size="60"'); ?>
						<?php echo form_error($reg['catalogo'].'-descripcion'); ?>
					</td>
					<td>
						<?php echo form_input($reg['catalogo'].'-pmp', number_format(set_value($reg['catalogo'].'-pmp', $reg['pmp']),2,'.',''),'maxlength="10" size="10" , class="ar"'); ?>
						<?php echo form_error($reg['catalogo'].'-pmp'); ?>
					</td>
					<td class="ac">
						<a href="#" class="button_micro b-active round boton-borrar" id="btn_borrar" id-borrar="<?php echo $reg['catalogo']; ?>">
							<img src="<?php echo base_url(); ?>img/ic_delete.png" />
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
				
				<tr>
					<td colspan="3"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
				</tr>
			</tbody>
		</table>
		<?php echo form_close(); ?>
		
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
		<a href="#" class="button b-active round ic-ok fr" id="btn_guardar">Guardar</a>
	</div> <!-- fin content-module-footer -->

	<?php echo form_open('','id="frm_borrar"'); ?>
		<?php echo form_hidden('formulario','borrar'); ?>
		<?php echo form_hidden('id_borrar'); ?>
	<?php echo form_close(); ?>

</div> <!-- fin content-module -->

<script type="text/javascript">
	$(document).ready(function() {
		if ($('div.content-module-main-agregar div.error').length > 0) {
			$('div.content-module-main-agregar').toggle();				
		}

		$('#btn_mostrar_agregar').click(function(event) {
			event.preventDefault();
			$('div.content-module-main-agregar').toggle();
			$('#btn_guardar').toggle();
		});

		$('#btn_guardar').click(function(event) {
			event.preventDefault();
			$('form#frm_materiales').submit();
		});			

		$('#btn_agregar').click(function(event) {
			event.preventDefault();
			$('form#frm_agregar').submit();
		});			

		$('a.boton-borrar').click(function (event) {
			event.preventDefault();
			var id_borrar = $(this).attr('id-borrar');
			if (confirm('Seguro que desea borrar el catalogo id=' + id_borrar + ' ?')) {
				$('form#frm_borrar input[name="id_borrar"]').val(id_borrar);
				$('form#frm_borrar').submit();
			}
		});

		$('#filtro').keypress(function (event) {
			if (event.which == 13)
			{
				window.location.replace(js_base_url + 'index.php/config/materiales/' + $('#filtro').val());
			}
		});

		$('#filtro').blur(function () {
			window.location.replace(js_base_url + 'index.php/config/materiales/' + $('#filtro').val());
		});

	});
</script>
