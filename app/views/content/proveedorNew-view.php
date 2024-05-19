<div class="container is-fluid mb-6">
	<h1 class="title">Proveedores</h1>
	<h2 class="subtitle"><i class="fas fa-tag fa-fw"></i> &nbsp; Nuevo proveedor</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_proveedor" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombre completo <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="proveedor_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="70" required >
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
		<p class="has-text-centered">
			<button type="reset" class="button is-warning is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-success is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
</div>