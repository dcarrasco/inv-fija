<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>
		<div class="fr">
			<?php echo anchor('stock_sap/edita_grupos/' . $tipo_op, 'Nuevo Grupo...', 'class="button b-active round ic-agregar fl"')?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<table>
			<tr>
				<th>tipo</th>
				<th>almacenes</th>
			</tr>
			<?php foreach($tiposalm as $reg): ?>
			<tr>
				<td><?php echo anchor('stock_sap/edita_grupos/' . $tipo_op . '/' . $reg['id_tipo'], $reg['tipo']); ?></td>
				<td>
					<?php foreach($detalle_almacenes[$reg['id_tipo']] as $reg_alm): ?>
						<?php echo $reg_alm['centro']; ?> - <?php echo $reg_alm['cod_almacen']; ?>: <?php echo $reg_alm['des_almacen']; ?><br />
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">

	</div> <!-- fin content-module-footer -->


</div> <!-- fin content-module -->

