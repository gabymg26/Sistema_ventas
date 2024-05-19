<?php

	namespace app\controllers;
	use app\models\mainModel;

	class proveedorController extends mainModel{

		/*----------  Controlador registrar proveedor  ----------*/
		public function registrarProveedorControlador(){

			# Almacenando datos#
		    $nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
			$telefono=$this->limpiarCadena($_POST['proveedor_telefono']);
			$email=$this->limpiarCadena($_POST['proveedor_email']);
		    

		    # Verificando campos obligatorios #
            if($nombre==""){
            	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado todos los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }


		    # Verificando nombre #
		    $check_nombre=$this->ejecutarConsulta("SELECT proveedor_nombre FROM proveedor WHERE proveedor_nombre='$nombre'");
		    if($check_nombre->rowCount()>0){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

			if($telefono!=""){
		    	if($this->verificarDatos("[0-9()+]{8,20}",$telefono)){
			    	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"El TELEFONO no coincide con el formato solicitado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
			    }
		    }

			
		    # Verificando email #
		    if($email!=""){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$check_email=$this->ejecutarConsulta("SELECT proveedor_email FROM proveedor WHERE proveedor_email='$email'");
					if($check_email->rowCount()>0){
						$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
							"icono"=>"error"
						];
						return json_encode($alerta);
						exit();
					}
				}else{
					$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Ha ingresado un correo electrónico no valido",
						"icono"=>"error"
					];
					return json_encode($alerta);
					exit();
				}
            }


		    $proveedor_datos_reg=[
				[
					"campo_nombre"=>"proveedor_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"proveedor_telefono",
					"campo_marcador"=>":Telefono",
					"campo_valor"=>$telefono
				],

				[
					"campo_nombre"=>"proveedor_email",
					"campo_marcador"=>":Email",
					"campo_valor"=>$email
				]
			];

			$registrar_proveedor=$this->guardarDatos("proveedor",$proveedor_datos_reg);

			if($registrar_proveedor->rowCount()==1){
				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Proveedor registrado",
					"texto"=>"El proveedor ".$nombre." se registró con éxito",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar el proveedor, por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
		}


		/*----------  Controlador listar proveedor  ----------*/
		public function listarProveedorControlador($pagina,$registros,$url,$busqueda){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!=""){

				$consulta_datos="SELECT * FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_email LIKE '%$busqueda%' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

				$consulta_total="SELECT COUNT(proveedor_id) FROM proveedor WHERE proveedor_nombre LIKE '%$busqueda%' OR proveedor_email LIKE '%$busqueda%'";

			}else{

				$consulta_datos="SELECT * FROM proveedor ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

				$consulta_total="SELECT COUNT(proveedor_id) FROM proveedor";

			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();

			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();

			$numeroPaginas =ceil($total/$registros);

			$tabla.='
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">No.</th>
		                    <th class="has-text-centered">Nombre</th>
		                    <th class="has-text-centered">Teléfono</th>
							          <th class="has-text-centered">Email</th>
		                    <th class="has-text-centered">Productos</th>
		                    <th class="has-text-centered">Actualizar</th>
		                    <th class="has-text-centered">Eliminar</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

		    if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
						<tr class="has-text-centered" >
							<td>'.$contador.'</td>
							<td>'.$rows['proveedor_nombre'].'</td>
							<td>'.$rows['proveedor_email'].'</td>
							<td>
			                    <a href="'.APP_URL.'productCategory/'.$rows['proveedor_id'].'/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-boxes fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="'.APP_URL.'proveedorUpdate/'.$rows['proveedor_id'].'/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>

			                	<form class="FormularioAjax" action="'.APP_URL.'app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_proveedor" value="eliminar">
			                		<input type="hidden" name="proveedor_id" value="'.$rows['proveedor_id'].'">
			                    <button type="submit" class="button is-danger is-rounded is-small">
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>
			                </td>
						</tr>
					';

					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
				}else{
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
				}
			}

			$tabla.='</tbody></table></div>';

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando Proveedores <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}


		/*----------  Controlador eliminar proveedor  ----------*/
		public function eliminarProveedorControlador(){

			$id=$this->limpiarCadena($_POST['proveedor_id']);

			# Verificando proveedor #
		    $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el proveedor en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Verificando productos #
		    $check_productos=$this->ejecutarConsulta("SELECT proveedor_id FROM producto WHERE proveedor_id='$id' LIMIT 1");
		    if($check_productos->rowCount()>0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el proveedor del sistema ya que tiene productos asociados",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    $eliminarProveedor=$this->eliminarRegistro("proveedor","proveedor_id",$id);

		    if($eliminarProveedor->rowCount()==1){

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Proveedor eliminado",
					"texto"=>"El proveedor ".$datos['proveedor_nombre']." ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];

		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el proveedor ".$datos['proveedor_nombre']." del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);
		}


		/*----------  Controlador actualizar proveedor  ----------*/
		public function actualizarProveedorControlador(){

			$id=$this->limpiarCadena($_POST['proveedor_id']);

			# Verificando proveedor #
		    $datos=$this->ejecutarConsulta("SELECT * FROM proveedor WHERE proveedor_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el proveedor en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Almacenando datos#
		    $nombre=$this->limpiarCadena($_POST['proveedor_nombre']);
		    $telefono=$this->limpiarCadena($_POST['proveedor_telefono']);
		    $email=$this->limpiarCadena($_POST['proveedor_email']);

		    # Verificando campos obligatorios #
            if($nombre==""){
            	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado todos los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }
		
		    # Verificando nombre #
		    $check_nombre=$this->ejecutarConsulta("SELECT proveedor_nombre FROM proveedor WHERE proveedor_nombre='$nombre'");
		    if($check_nombre->rowCount()>0){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

			if($telefono!=""){
		    	if($this->verificarDatos("[0-9()+]{8,20}",$telefono)){
			    	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"El TELEFONO no coincide con el formato solicitado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
			    }
		    }

			
		    # Verificando email #
		    if($email!=""){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$check_email=$this->ejecutarConsulta("SELECT proveedor_email FROM proveedor WHERE proveedor_email='$email'");
					if($check_email->rowCount()>0){
						$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
							"icono"=>"error"
						];
						return json_encode($alerta);
						exit();
					}
				}else{
					$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Ha ingresado un correo electrónico no válido",
						"icono"=>"error"
					];
					return json_encode($alerta);
					exit();
				}
            }


		    $proveedor_datos_up=[
				[
					"campo_nombre"=>"proveedor_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"proveedor_telefono",
					"campo_marcador"=>":Telefono",
					"campo_valor"=>$telefono
				],

				[
					"campo_nombre"=>"proveedor_email",
					"campo_marcador"=>":Email",
					"campo_valor"=>$email
				]
			];

			$condicion=[
				"condicion_campo"=>"proveedor_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("proveedor",$proveedor_datos_up,$condicion)){
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Proveedor actualizada",
					"texto"=>"Los datos del proveedor ".$datos['proveedor_nombre']." se actualizaron correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido actualizar los datos del proveedor ".$datos['proveedor_nombre'].", por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
		}

	}
