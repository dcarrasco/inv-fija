<div>
	<?php echo $menu_ajustes; ?>
</div>

<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row-fluid">
	<div class="span8 offset2">
	<?php echo form_open_multipart('analisis/sube_stock', 'class="form-horizontal"'); ?>
	<?php echo form_hidden('formulario','upload'); ?>

	<div class="control-group">
		<label class="control-label">
			Inventario
		</label>
		<div class="controls">
			<?php echo $inventario_id . ' - ' . $inventario_nombre; ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			Archivo
		</label>
		<div class="controls">
			<div>
				<span style="color: red; font-weight: bold">ADVERTENCIA</span><br>
				<span style="color: red";>Al subir un archivo se eliminar <strong>TODOS</strong> los registros asociados al inventario
					"<?php echo $inventario_nombre?>"</span>
			</div>
			<?php echo form_upload('upload_file','','class="input-large"'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			Clave Administrador
		</label>
		<div class="controls">
			<?php echo form_password('password'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			Progreso
		</label>
		<div class="controls">
			<?php echo $upload_error; ?>
			<?php echo $msj_error; ?>
			<div id="progreso_carga">
				<div id="barra"><div id="barra_progreso"></div></div>
				<div id="status_progreso">Cargando registros OK <span id="reg_actual">0</span> de <?php echo ($regs_OK); ?></div>
			</div>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn btn-primary" id="btn_guardar">
				<i class="icon-upload icon-white"></i>
				Subir archivo
			</button>
		</div>
	</div>

	<?php echo form_close(); ?>
	</div>

	<div class="span8 offset2">
		<pre>
Formato del archivo:
	Archivo de texto
	Extension .txt
	Campos separados por tabulación

	Campos
			Ubicacion
			HU
			Catalogo
			Descripcion catalogo
			Lote
			Centro
			Almacen
			Unidad de medida
			Stock SAP
			Hoja
		</pre>
	</div>

</div>

<form id="frm_aux">
	<input type="hidden" name="id" id="id_id" value="">
	<input type="hidden" name="id_inv" id="id_id_inv" value="">
	<input type="hidden" name="hoja" id="id_hoja" value="">
	<input type="hidden" name="aud" id="id_aud" value="">
	<input type="hidden" name="dig" id="id_dig" value="">
	<input type="hidden" name="ubic" id="id_ubic" value="">
	<input type="hidden" name="hu" id="id_hu" value="">
	<input type="hidden" name="cat" id="id_cat" value="">
	<input type="hidden" name="desc" id="id_desc" value="">
	<input type="hidden" name="lote" id="id_lote" value="">
	<input type="hidden" name="cen" id="id_cen" value="">
	<input type="hidden" name="alm" id="id_alm" value="">
	<input type="hidden" name="um" id="id_um" value="">
	<input type="hidden" name="ssap" id="id_ssap" value="">
	<input type="hidden" name="sfis" id="id_sfis" value="">
	<input type="hidden" name="obs" id="id_obs" value="">
	<input type="hidden" name="fec" id="id_fec" value="">
	<input type="hidden" name="nvo" id="id_nvo" value="">
</form>

<script type="text/javascript">
$(document).ready(function() {
	$('#barra_progreso').css('height', '30px');
	$('#barra_progreso').css('width', '0px');
	$('#barra_progreso').css('background-color', '#236ab3');

	$('#barra').css('border', '1px solid black');
	$('#barra').css('height', '30px');
	$('#barra').css('width', '400px');
});

	var regs_procesados = 0;

	function proc_linea_carga(count, id, id_inv, hoja, aud, dig, ubic, hu, cat, desc, lote, cen, alm, um, ssap, sfis, obs, fec, nvo) {
		var total_regs = <?php echo ($regs_OK); ?>;
		var total_carac = 40;
		$('#id_id').val(id);
		$('#id_id_inv').val(id_inv);
		$('#id_hoja').val(hoja);
		$('#id_aud').val(aud);
		$('#id_dig').val(dig);
		$('#id_ubic').val(ubic);
		$('#id_hu').val(hu);
		$('#id_cat').val(cat);
		$('#id_desc').val(desc);
		$('#id_lote').val(lote);
		$('#id_cen').val(cen);
		$('#id_alm').val(alm);
		$('#id_um').val(um);
		$('#id_ssap').val(ssap);
		$('#id_sfis').val(sfis);
		$('#id_obs').val(obs);
		$('#id_fec').val(fec);
		$('#id_nvo').val(nvo);

		$.ajax({
			type:  "POST",
			url:   js_base_url + "analisis/inserta_linea_archivo",
			async: false,
			data:  $('#frm_aux').serialize(),
			success: function(datos) {
				regs_procesados += 1;
				var pixeles_progreso = parseInt(400 * regs_procesados/total_regs);
				$('#barra_progreso').css('width', pixeles_progreso + 'px');
				//$('#barra_progreso').text('['+Array(carac_progreso).join('*') + Array(total_carac - carac_progreso).join('-')+']');
				$('#reg_actual').text(regs_procesados);
				if (regs_procesados >= total_regs) {
					$('#status_progreso').html('Carga finalizada (' + total_regs + ' registros cargados)');
				}
				//alert( "Se guardaron los datos: " + datos);
			}
		});
	}
</script>


<script type="text/javascript">
$(document).ready(function() {
<?php echo $script_carga; ?>
//$('#barra_progreso').text('Proceso finalizado.');
});
</script>
