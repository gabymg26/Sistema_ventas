<div class="container is-fluid mb-6">
	<h1 class="title">Proveedores</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar proveedores</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$id=$insLogin->limpiarCadena($url[1]);

		$datos=$insLogin->seleccionarDatos("Unico","proveedor","proveedor_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered"><?php echo $datos['proveedor_nombre']." (".$datos['proveedor_email'].")"; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_proveedor" value="actualizar">
		<input type="hidden" name="proveedor_id" value="<?php echo $datos['proveedor_id']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre completo <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="proveedor_nombre" value="<?php echo $datos['proveedor_nombre']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="70" required >
				</div>
		  	</div>
			<div class="column">
		    	<div class="control">
					<label>Teléfono</label>
				  	<input class="input" type="text" name="proveedor_telefono" pattern="[0-9()+]{8,20}" maxlength="20" >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Email</label>
				  	<input class="input" type="email" name="proveedor_email" maxlength="50" >
				</div>
		  	</div>
		  	</div>

		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>