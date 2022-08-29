<?php
include('../auth.inc.php');

require_once('../../app/Controllers/Auth/LoginController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        LoginController::logout();
        break;
    default:
        echo ('Error 405');
}
