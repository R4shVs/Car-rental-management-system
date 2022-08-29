<?php
include('../auth.inc.php');

$role = array(User::MANAGER);
include('../authorization.inc.php');

require_once('../../app/Controllers/CarsController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        CarsController::editState($auth);
        break;
    case 'POST':
        CarsController::updateState($auth);
        break;
    default:
        echo ('Error 405');
}
