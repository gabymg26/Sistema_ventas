<?php

namespace app\controllers;

use app\models\mainModel;

class proveedorController extends mainModel
{

    /*----------  Controlador registrar categoria  ----------*/
    public function registrarProveedorControlador()
    {

        # Almacenando datos#
        $nombre = $this->limpiarCadena($_POST['proveedor_nombre']);

        # Verificando campos obligatorios #
        if ($nombre == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos que son obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de los datos #
        if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando nombre #
        $check_nombre = $this->ejecutarConsulta("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
        if ($check_nombre->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }


        $proveedor_datos_reg = [
            [
                "campo_nombre" => "proveedor_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
        ];

        $registrar_proveedor = $this->guardarDatos("proveedores", $proveedor_datos_reg);

        if ($registrar_proveedor->rowCount() == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Proveedor registrado",
                "texto" => "El proveedor " . $nombre . " se registro con exito",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No se pudo registrar el proveedor, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }


    /*----------  Controlador listar proveedor  ----------*/
    public function listarProveedorControlador($pagina, $registros, $url, $busqueda)
    {

        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);

        $url = $this->limpiarCadena($url);
        $url = APP_URL . $url . "/";

        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {

            $consulta_datos = "SELECT * FROM proveedores WHERE proveedor_nombre LIKE '%$busqueda%' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(proveedor_id) FROM proveedores WHERE proveedor_nombre LIKE '%$busqueda%'";
        } else {

            $consulta_datos = "SELECT * FROM proveedores ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

            $consulta_total = "SELECT COUNT(proveedor_id) FROM proveedores";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();

        $numeroPaginas = ceil($total / $registros);

        $tabla .= '
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">NO.</th>
		                    <th class="has-text-centered">Nombre</th>
		                    <th class="has-text-centered">Productos</th>
		                    <th class="has-text-centered">Actualizar</th>
		                    <th class="has-text-centered">Eliminar</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                $tabla .= '
						<tr class="has-text-centered" >
							<td>' . $contador . '</td>
							<td>' . $rows['proveedor_nombre'] . '</td>
							<td>
			                    <a href="' . APP_URL . 'productCategory/' . $rows['proveedor_id'] . '/" class="button is-info is-rounded is-small">
			                    	<i class="fas fa-boxes fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                    <a href="' . APP_URL . 'categoryUpdate/' . $rows['proveedor_id'] . '/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/proveedorAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_proveedor" value="eliminar">
			                		<input type="hidden" name="proveedor_id" value="' . $rows['proveedor_id'] . '">

			                    	<button type="submit" class="button is-danger is-rounded is-small">
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>
			                </td>
						</tr>
					';
                $contador++;
            }
            $pag_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
            } else {
                $tabla .= '
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total > 0 && $pagina <= $numeroPaginas) {
            $tabla .= '<p class="has-text-right">Mostrando proveedores <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
        }

        return $tabla;
    }


    /*----------  Controlador eliminar proveedor  ----------*/
    public function eliminarProveedorControlador()
    {

        $id = $this->limpiarCadena($_POST['proveedor_id']);

        # Verificando proveedor #
        $datos = $this->ejecutarConsulta("SELECT * FROM proveedores WHERE proveedor_id='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el proveedor en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        # Verificando productos #
        $check_productos = $this->ejecutarConsulta("SELECT proveedor_id FROM producto WHERE proveedor_id='$id' LIMIT 1");
        if ($check_productos->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No podemos eliminar el proveedor del sistema ya que tiene productos asociados",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $eliminarProveedor = $this->eliminarRegistro("proveedores", "proveedor_id", $id);

        if ($eliminarProveedor->rowCount() == 1) {

            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Proveedor eliminado",
                "texto" => "El proveedor " . $datos['proveedor_nombre'] . " ha sido eliminada del sistema correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido eliminar el proveedor " . $datos['proveedor_nombre'] . " del sistema, por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }


    /*----------  Controlador actualizar proveedor  ----------*/
    public function actualizarProveedorControlador()
    {

        $id = $this->limpiarCadena($_POST['proveedor_id']);

        # Verificando categoria #
        $datos = $this->ejecutarConsulta("SELECT * FROM proveedores WHERE proveedor_id='$id'");
        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos encontrado el proveedor en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        # Almacenando datos#
        $nombre = $this->limpiarCadena($_POST['proveedor_nombre']);

        # Verificando campos obligatorios #
        if ($nombre == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No has llenado todos los campos que son obligatorios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de los datos #
        if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El NOMBRE no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando nombre #
        if ($datos['proveedor_nombre'] != $nombre) {
            $check_nombre = $this->ejecutarConsulta("SELECT proveedor_nombre FROM proveedores WHERE proveedor_nombre='$nombre'");
            if ($check_nombre->rowCount() > 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }


        $proveedor_datos_up = [
            [
                "campo_nombre" => "proveedor_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ]
        ];

        $condicion = [
            "condicion_campo" => "proveedor_id",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

        if ($this->actualizarDatos("proveedores", $proveedor_datos_up, $condicion)) {
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Proveedor actualizado",
                "texto" => "Los datos de el proveedor " . $datos['proveedor_nombre'] . " se actualizaron correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No hemos podido actualizar los datos de el proveedor " . $datos['proveedor_nombre'] . ", por favor intente nuevamente",
                "icono" => "error"
            ];
        }

        return json_encode($alerta);
    }
}
