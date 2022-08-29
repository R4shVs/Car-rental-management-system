<?php
include('../auth.inc.php');

$role = array(User::MANAGER);
include('../authorization.inc.php');

require_once('../../app/Controllers/EmployeesController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        EmployeesController::edit($auth);
        break;
    case 'POST':
        EmployeesController::update($auth);
        break;
    default:
        echo ('Error 405');
}
