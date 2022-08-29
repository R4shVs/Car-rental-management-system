<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: http://localhost/ProgettoDB/gas_n_go/resources/');
    die();
}

require_once('../app/Controllers/Auth/LoginController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        LoginController::show();
        break;
    case 'POST':
        LoginController::login();
        break;
    default:
        echo ('Error 405');
}
