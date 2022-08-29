<?php
include('../auth.inc.php');

$role = array(User::EMPLOYEE);
include('../authorization.inc.php');

require_once('../../app/Controllers/MembersController.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        MembersController::create($auth);
        break;
    case 'POST':
        MembersController::store();
        break;
    default:
        echo ('Error 405');
}
