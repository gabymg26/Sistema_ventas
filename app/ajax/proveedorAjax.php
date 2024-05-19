<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\proveedorController;

if (isset($_POST['modulo_proveedor'])) {

    $insCategory = new proveedorController();

    if ($_POST['modulo_proveedor'] == "registrar") {
        echo $insCategory->registrarProveedorControlador();
    }

    if ($_POST['modulo_proveedor'] == "eliminar") {
        echo $insCategory->eliminarProveedorControlador();
    }

    if ($_POST['modulo_proveedor'] == "actualizar") {
        echo $insCategory->actualizarProveedorControlador();
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
