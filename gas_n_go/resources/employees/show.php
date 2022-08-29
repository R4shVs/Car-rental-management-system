<?php
include('../auth.inc.php');

$role = array(User::MANAGER);
include('../authorization.inc.php');

require_once('../../app/Controllers/EmployeesController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        EmployeesController::show($auth);
        break;
    default:
        echo ('Error 405');
}
